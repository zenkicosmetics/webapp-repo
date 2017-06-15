<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Postal charge calculation
 * =========================
 * CP =P+[(UVP-P)*FA*FC*FL]
 * EP =(P+[(UVP-P)*FA*FC*FL])*(1+HCP)+HCA+CC
 * -----------------------------------------
 * CP = postal charge given to customer
 * EP = End net price given to customer
 * P = net price that was calculated using the API for CM customer account
 * UVP = official price calculation without customer account
 * CC = customs charge from CM price list
 * FA = Shipping service based Factor A => used for cost calculation
 * FB = Shipping service based Factor B => used for volume weight (VW) calculation
 * FC = Customer based shipping factor FC
 * FL = Location based shipping factor FL
 * HCP = % handling charge from CM price list
 * HCA = abs. handling charge from CM price list
 */
class FedExRatesCalculator
{
    private $mode = ShippingConfigs::MODE_PRODUCTION;
    private $url = '';
    private $fedex;
    private $fedexVAT; // price includes VAT

    private $FA; // Shipping service based Factor A => used for cost calculation
    private $FB; // Shipping service based Factor B => used for volume weight (VW) calculation
    private $FL; // Location based shipping factor FL
    private $FC; // Customer based shipping factor FC

    private $HCP; // % handling charge from CM price list
    private $HCA; // abs. handling charge from CM price list
    private $CC; // customs charge from CM price list
    private $FEDEX_PACKAGE_WEIGHT_LIMIT;
    private $FEDEX_PACKAGE_DIMENSIONS_LIMIT;

    private $customerID;
    private $serviceID;
    private $shippingType;
    private $customsValue;
    private $credential;
    private $api;

    // Shipping from address
    private $locationID;

    // Shipping to address
    private $street;
    private $city;
    private $region;
    private $postalCode;
    private $countryID;
    private $service;
    private $customer_address;

    public function __construct()
    {
        ci()->load->library('shipping/ShippingConfigs');
        // Load FedEx
        ci()->load->library('shipping/FedEx', array());
    }
    
    /**
     * init function.
     * @param array $param
     */
    public function init(array $params, $api = null, $credential = null){
        $this->initVariables($params);
        $this->initShippingService($api, $credential);
        $this->initAddressShippingFrom();
        $this->initCustomerShippingFactor();
        $this->initPricingTemplateValues();
        $this->setAddressShippingTo();

        $this->shipments = null;
    }
    
    public function calculateShippingRate(array $packages, $separate_package = true)
    {
        $P = $UVP = 0;
        $newPackages = $packages;
        if ($separate_package) {
            $newPackages = $this->separatePackagesForCollectShipment($packages);
        }
        foreach ($newPackages as $newPackage) {
            // Change packaging type
            // FedEx Envelope
            // -- Inside dimensions: 9.252” x 13.189” (23.5cm x 33.5cm).
            // -- Maximum weight allowed: 17.6 oz. (500 g).*
            // -- Weight when empty: 1.8 oz.
            // -- For documents (up to approximately 60 pages, 8.25" x 11.75", 21 x 29.7 cm).
            // * FedEx Envelope shipments exceeding 0.5kg will be charged at FedEx Pak rates.
            $this->fedex->setPackagingType('YOUR_PACKAGING');
            // if ($this->service->packaging_type == APConstants::SHIPPING_PACKAGING_TYPE_2
            if ($newPackage[ShippingConfigs::PACKAGE_TYPE] == APConstants::ENVELOPE_TYPE_LETTER
                && $newPackage[ShippingConfigs::PACKAGE_WEIGHT] <= 0.5) {
                $newPackage[ShippingConfigs::PACKAGE_WEIGHT] = 0.5;
                $newPackage[ShippingConfigs::PACKAGE_HEIGHT] = 0;
                $newPackage[ShippingConfigs::PACKAGE_WIDTH] = 0;
                $newPackage[ShippingConfigs::PACKAGE_LENGTH] = 0;
                $this->fedex->setPackagingType('FEDEX_ENVELOPE');
            }
        
            $this->fedex->addPackageToShipment($newPackage);
            $result = $this->fedex->getRate();
            if (empty($result['error']) && !empty($result['result'])) {
                $rateResult = $result['result'];
                $P += $rateResult->DiscountRate->TotalNetCharge; // net price that was calculated using the API for CM customer account
                $UVP += $rateResult->OfficialRate->TotalNetCharge; // official price calculation without customer account
            } else {
                return $result['error'];
            }
        }

        // Convert price includes VAT to get the net price
        $P = round($P/(1 + $this->fedexVAT), 2);
        $UVP = round($UVP/(1 + $this->fedexVAT), 2);

        $CP = $P + ($UVP - $P) * $this->FA * $this->FC * $this->FL; // postal charge given to customer
        $EP = $CP * (1 + $this->HCP) + $this->HCA + $this->CC; // End net price given to customer
        $shipmentCharges = array(
            'F' => $this->FA,
            'CC' => $this->CC,
            'HCP' => $this->HCP,
            'HCA' => $this->HCA,
            'P' => $P,
            'UVP' => $UVP,
            'CP' => $CP,
            'EP' => $EP,
            'CODE' => APConstants::FEDEX_CARRIER,
            'API' => $this->api,
            'CREDENTIAL' => $this->credential
        );

        return $shipmentCharges;
    }

    private function initVariables(array $params)
    {
        $this->customerID = $params[ShippingConfigs::CUSTOMER_ID];
        $this->locationID = $params[ShippingConfigs::LOCATION_ID];
        $this->serviceID = $params[ShippingConfigs::SERVICE_ID];
        $this->shippingType = $params[ShippingConfigs::SHIPPING_TYPE];
        $this->customsValue = empty($params[ShippingConfigs::CUSTOMS_VALUE]) ? 0 : $params[ShippingConfigs::CUSTOMS_VALUE];
        $this->street = $params[ShippingConfigs::STREET];
        $this->city = $params[ShippingConfigs::CITY];
        $this->region = $params[ShippingConfigs::REGION];
        $this->countryID = $params[ShippingConfigs::COUNTRY_ID];
        $this->postalCode = $params[ShippingConfigs::POSTAL_CODE];
        $this->customer_address = CustomerUtils::getCustomerAddressByID($this->customerID);
        $this->fedex = ci()->fedex;
        $this->fedex->setTotalInsuredValue($this->customsValue);
    }

    private function initShippingService($api, $credential)
    {
        ci()->load->library('shipping/shipping_api');

        $service = shipping_api::getShippingServiceInfo($this->serviceID);
        $this->service = $service;
        
        if(empty($service)){
            throw new BusinessException('Service is not found.');
        }
        
//        $this->fedex->setCredentials($service->auth_key, $service->password);
//        $this->fedex->setShippingAccount($service->account_no, $service->meter_no);
//        $this->fedex->setShippingChargesPayment($service->account_no);
//        $this->fedex->setServiceType($service->api_svc_code1);
//        $this->fedex->setEndPoint(empty($service->site_id)  ? ShippingConfigs::FEDEX_PRODUCTION_URL.'/rate' : $service->site_id.'/rate');

        if (!empty($api) && !empty($credential)){
            $this->api = $api;
            $this->credential = $credential;
            $this->fedex->setCredentials($this->credential->auth_key, $this->credential->password);
            $this->fedex->setShippingAccount($this->credential->account_no, $this->credential->meter_no);
            $this->fedex->setShippingChargesPayment($this->credential->account_no);
            $this->fedex->setServiceType($this->api->service_code);
            $this->fedex->setEndPoint(empty($this->api->site_id)  ? ShippingConfigs::FEDEX_PRODUCTION_URL.'/rate' : $this->api->site_id.'/rate');

            $this->fedexVAT = $this->api->price_includes_vat;
        }
        
        $this->FA = $service->factor_a;
        $this->FB = $service->factor_b;
        $this->FEDEX_PACKAGE_WEIGHT_LIMIT = $service->weight_limit;
        $this->FEDEX_PACKAGE_DIMENSIONS_LIMIT = $service->dimension_limit;
        
        //$this->fedexVAT = $service->price_includes_vat;
    }
    
    private function initAddressShippingFrom()
    {
        ci()->load->library('addresses/addresses_api');
        ci()->load->library('settings/settings_api');

        $location = addresses_api::getLocationByID($this->locationID);
        $country = settings_api::getCountryByID($location->country_id);
        $stateOrProvinceCode = $location->state_code;

        $senderName = ShippingConfigs::DEFAULT_SENDER_NAME;
        $senderCompanyName = ShippingConfigs::DEFAULT_SENDER_COMPANY_NAME;
        $phoneNumber = ShippingConfigs::DEFAULT_SENDER_PHONE_NUMBER;
        $this->fedex->setShipper($senderName, $senderCompanyName, $phoneNumber, $location->street, $location->city, $stateOrProvinceCode, $location->postcode, $country->country_code);

        $this->FL = $location->shipping_factor_fl;
    }

    private function setAddressShippingTo()
    {
        ci()->load->library('settings/settings_api');

        // We can get these empty fields from table [customer_address] by customer_id.
        $recipientName = $this->customer_address->shipment_address_name; // Get from [customer_address].shipment_address_name
        $companyName = $this->customer_address->shipment_company; // Get from [customer_address].shipment_company
        $phoneNumber = $this->customer_address->shipment_phone_number; // Get from [customer_address].shipment_phone_number
        $country = settings_api::getCountryByID($this->countryID);
        $country_code = $country->country_code;
        $country_code_3 = $country->country_code_3;
        $stateOrProvinceCode = $this->getStateOrProvinceCode($this->city, $this->region, $country_code_3);
        $this->fedex->setRecipient($recipientName, $companyName, $phoneNumber, $this->street, $this->city, $stateOrProvinceCode, $this->postalCode, $country_code);
    }

    private function initCustomerShippingFactor()
    {
        ci()->load->library('customers/customers_api');

        $customer = customers_api::getCustomerByID($this->customerID);
        $this->FC = $customer->shipping_factor_fc;
    }

    /**
     * Use the selected location & the postbox type that the customer has for this location to get his pricing template.
     * if not available or if multiple postboxes with different types then use business postbox type (type = 3)
     */
    private function initPricingTemplateValues()
    {
        ci()->load->library('price/price_api');
        ci()->load->library('mailbox/mailbox_api');

        $postboxTypes = mailbox_api::getPostboxTypesByLocationID($this->customerID, $this->locationID);
        $postboxType = (empty($postboxTypes) || count($postboxTypes) > 1) ? APConstants::BUSINESS_TYPE : $postboxTypes[0]->type;

        // $pricingMap = price_api::getPricingModelByLocationID($this->locationID, $postboxType);
        $pricing_map = price_api::getPricingModelByCusotomerAndLocationID($this->customerID, $this->locationID);
        $pricingMap = $pricing_map[$postboxType];

        if ($this->shippingType == ShippingConfigs::DIRECT_SHIPPING) {
            $this->HCP = $pricingMap['shipping_plus'] / 100; // Shipping collected to forwarding address (shipping_plus)
            $this->HCA = $pricingMap['send_out_directly']; // Shipping directly to forwarding address (send_out_directly)
        } elseif ($this->shippingType == ShippingConfigs::COLLECT_SHIPPING) {
            $this->HCP = $pricingMap['collect_shipping_plus'] / 100;
            $this->HCA = $pricingMap['send_out_collected'];
        }

        if ($this->customsValue > 1000) {
            $this->CC = $pricingMap['custom_declaration_outgoing_01'];
        } else if ($this->customsValue > 0 && $this->customsValue <= 1000) {
            $this->CC = $pricingMap['custom_declaration_outgoing_02'];
        } else {
            $this->CC = 0;
        }
    }

    private function getStateOrProvinceCode($input_city, $input_region, $countr_code_3)
    {
        ci()->load->model('settings/state_m');
        $state = ci()->state_m->get_by_many(array('country' => $countr_code_3, 'name' => $input_region));
        if (!empty($state)) {
            return $state->abbreviation;
        }
        $region = APUtils::convertToEnChar(trim($input_region));
        $city = APUtils::convertToEnChar(trim($input_city));
        if ($region) {
            $stateOrProvinceCode = (strlen($region) > 2) ? substr($region, 0, 2) : $region;
        } elseif ($city) {
            $stateOrProvinceCode = (strlen($city) > 2) ? substr($city, 0, 2) : $city;
        } else {
            $stateOrProvinceCode = '';
        }

        return strtoupper($stateOrProvinceCode);
    }

    // Calculate number of parcels
    public function separatePackagesForCollectShipment(array $packages)
    {
        //$message = '{packages:'. json_encode($packages).'}';
        //log_audit_message(APConstants::LOG_INFOR, $message, FALSE, 'separatePackagesForCollectShipment>>>calculateVolumeWeight');
        // Calculate weight for price calculation
        $this->calculateVolumeWeight($packages, $this->FB);
        // $message = '{packages:'. json_encode($packages).'}';
        // log_audit_message(APConstants::LOG_INFOR, $message, FALSE, 'separatePackagesForCollectShipment<<<calculateVolumeWeight');
        
        //$message = '{packages:'. json_encode($packages).'}';
        //log_audit_message(APConstants::LOG_INFOR, $message, FALSE, 'separatePackagesForCollectShipment>>>sortPackages');
        // Sort packages in descending of weight for price calculation
        $this->sortPackages($packages);
        // $message = '{packages:'. json_encode($packages).'}';
        // log_audit_message(APConstants::LOG_INFOR, $message, FALSE, 'separatePackagesForCollectShipment<<<sortPackages');
        
        //$message = '{packages:'. json_encode($packages).'}';
        // log_audit_message(APConstants::LOG_INFOR, $message, FALSE, 'separatePackagesForCollectShipment>>>calculateShipments');
        // Get shipments from multiple packages
        $shipments = $this->calculateShipments($packages);
        // $message = '{packages:'. json_encode($packages).'}';
        // log_audit_message(APConstants::LOG_INFOR, $message, FALSE, 'separatePackagesForCollectShipment<<<calculateShipments');
        
        $newPackages = array();
        foreach ($shipments as $shipment) {
            $newPackage = $this->calculateNewPackageFromShipment($shipment, $this->FB);
            array_push($newPackages, $newPackage);
        }
        return $newPackages;
    }

    private function calculateVolumeWeight(array &$packages, $F)
    {
        foreach ($packages as &$package) {
            if ($package[ShippingConfigs::PACKAGE_HEIGHT] == null) {
                $package[ShippingConfigs::PACKAGE_HEIGHT] = 0;
            }
            if ($package[ShippingConfigs::PACKAGE_WIDTH] == null) {
                $package[ShippingConfigs::PACKAGE_WIDTH] = 0;
            }
            if ($package[ShippingConfigs::PACKAGE_LENGTH] == null) {
                $package[ShippingConfigs::PACKAGE_LENGTH] = 0;
            }
            // Height, Width, Length
            $H = $package[ShippingConfigs::PACKAGE_HEIGHT];
            $W = $package[ShippingConfigs::PACKAGE_WIDTH];
            $L = $package[ShippingConfigs::PACKAGE_LENGTH];

            // Volume weight (VW)
            $VW = ($F == 0) ? 0 : round((($L * $W * $H) * 1.2) / $F, 2);

            $package[ShippingConfigs::PACKAGE_VOLUME_WEIGHT] = $VW;
        }
    }

    private function sortPackages(array &$packages)
    {
        usort($packages, function ($package1, $package2) {
            $RW1 = $package1[ShippingConfigs::PACKAGE_WEIGHT];
            $RW2 = $package2[ShippingConfigs::PACKAGE_WEIGHT];
            $VW1 = $package1[ShippingConfigs::PACKAGE_VOLUME_WEIGHT];
            $VW2 = $package2[ShippingConfigs::PACKAGE_VOLUME_WEIGHT];

            return ($RW1 + $VW1) <= ($RW2 + $VW2);
        });
    }

    private function calculateShipments($packages)
    {
        $shipments = array();
        $totalPackages = count($packages);
        $checks = array(); // To mark packages processed already
        for ($i = 0; $i < $totalPackages; $i++) {
            if (!in_array($i, $checks)) {
                $shipment = array();
                $package = $packages[$i];
                $totalRW = $package[ShippingConfigs::PACKAGE_WEIGHT];
                $totalVW = $package[ShippingConfigs::PACKAGE_VOLUME_WEIGHT];
                array_push($shipment, $package);
                array_push($checks, $i);
                for ($j = $i + 1; $j < $totalPackages; $j++) {
                    if (!in_array($j, $checks)) {
                        $package = $packages[$j];
                        $temp1 = $totalRW + $package[ShippingConfigs::PACKAGE_WEIGHT];
                        $temp2 = $totalVW + $package[ShippingConfigs::PACKAGE_VOLUME_WEIGHT];
                        $temp3 = 5 * ceil(pow($temp2 * $this->FB, 1 / 3));
                        // $temp3 = ceil(pow($temp2 * $this->FB, 1 / 3));
                        if (!$this->exceedLimitValues($temp1, $temp2, $temp3)) {
                            $totalRW = $temp1;
                            $totalVW = $temp2;
                            array_push($shipment, $package);
                            array_push($checks, $j);
                        }
                    }
                }
                array_push($shipments, $shipment);
            }
        }

        return $shipments;
    }

    private function calculateNewPackageFromShipment(array $shipment, $F)
    {
        $VW = 0;
        $RW = 0;
        $package_type = APConstants::ENVELOPE_TYPE_LETTER;
        foreach ($shipment as $package) {
            $VW += $package[ShippingConfigs::PACKAGE_VOLUME_WEIGHT];
            $RW += $package[ShippingConfigs::PACKAGE_WEIGHT];
            if ($package_type == APConstants::ENVELOPE_TYPE_LETTER) {
                $package_type = array_key_exists(ShippingConfigs::PACKAGE_TYPE, $package) ? $package[ShippingConfigs::PACKAGE_TYPE] : APConstants::ENVELOPE_TYPE_LETTER;
            }
        }
        $totalDimWeight = ($VW > $RW) ? $VW : $RW;
        $dimension = ceil(pow($VW * $F, 1 / 3));
        
        $totalShipment = count($shipment);
        // Calculate new dimension
        if ($totalShipment > 1) {
            $newPackage = array(
                ShippingConfigs::PACKAGE_LENGTH => $dimension,
                ShippingConfigs::PACKAGE_WIDTH => $dimension,
                ShippingConfigs::PACKAGE_HEIGHT => $dimension,
                ShippingConfigs::PACKAGE_WEIGHT => $totalDimWeight,
                ShippingConfigs::PACKAGE_TYPE => $package_type
            );
        } 
        // if it is only one shipment, we do not need to re-calculate the dimensions. we can use the actual dimensions
        else {
            $newPackage = array(
                ShippingConfigs::PACKAGE_LENGTH => $package[ShippingConfigs::PACKAGE_LENGTH],
                ShippingConfigs::PACKAGE_WIDTH => $package[ShippingConfigs::PACKAGE_WIDTH],
                ShippingConfigs::PACKAGE_HEIGHT => $package[ShippingConfigs::PACKAGE_HEIGHT],
                ShippingConfigs::PACKAGE_WEIGHT => $RW,
                ShippingConfigs::PACKAGE_TYPE => $package_type
            );
        }

        return $newPackage;
    }

    private function exceedLimitValues($totalRW, $totalVW, $totalLength)
    {
        return ($totalRW > $this->FEDEX_PACKAGE_WEIGHT_LIMIT) ||
        ($totalVW > $this->FEDEX_PACKAGE_WEIGHT_LIMIT) ||
        ($totalLength > $this->FEDEX_PACKAGE_DIMENSIONS_LIMIT);
    }
}