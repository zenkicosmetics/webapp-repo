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
class CanadaPostRatesCalculator
{
    private $url = '';
    private $canadaPost;
    private $canadaPostVAT; // price includes VAT

    private $FA; // Shipping service based Factor A => used for cost calculation
    private $FB; // Shipping service based Factor B => used for volume weight (VW) calculation
    private $FL; // Location based shipping factor FL
    private $FC; // Customer based shipping factor FC

    private $HCP; // % handling charge from CM price list
    private $HCA; // abs. handling charge from CM price list
    private $CC; // customs charge from CM price list
    private $CANADAPOST_PACKAGE_WEIGHT_LIMIT;
    private $CANADAPOST_PACKAGE_DIMENSIONS_LIMIT;

    private $customerID;
    private $serviceID;
    private $shippingType;
    private $customsValue;
    private $serviceType;
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
    
    private $country_from;
    private $country_to;

    public function __construct()
    {
        ci()->load->library('shipping/ShippingConfigs');
        // Load CANADAPOST
        ci()->load->library('shipping/CanadaPost', array());
    }
    
    public function init(array $params, $api, $credential){
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
        // Validate country code from Canada
        if ($this->country_from->country_code != 'CA') {
            return 'This service does not support to ship outside Canada.';
        }
        
        $P = $UVP = 0;
        $newPackages = $packages;
        if ($separate_package) {
            $newPackages = $this->separatePackagesForCollectShipment($packages);
        }
        foreach ($newPackages as $newPackage) {
            $this->canadaPost->addPackageToShipment($newPackage);
            $result = $this->canadaPost->getRate();

            if (empty($result['error']) && !empty($result['result'])) {
                $list_prices = $result['result'];
                $price = null;
                if ($list_prices != null && count($list_prices) > 0) {
                    foreach ($list_prices as $price_item) {
                        if ($this->serviceType == $price_item->ServiceType) {
                            $price = $price_item;
                            break;
                        }
                    }
                }
                if ($price != null) {
                    $P += $price->Price; // net price that was calculated using the API for CM customer account
                    $UVP += $price->Price; // official price calculation without customer account
                }
            } else {
                return $result['error'];
            }
        }

        // Convert price includes VAT to get the net price
        $P = round($P/(1 + $this->canadaPostVAT), 2);
        $UVP = round($UVP/(1 + $this->canadaPostVAT), 2);

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
            'CODE' => APConstants::CANADAPOST_CARRIER,
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

        $this->canadaPost = ci()->canadapost;
        $this->canadaPost->setTotalInsuredValue($this->customsValue);
    }

    private function initShippingService($api, $credential)
    {
        ci()->load->library('shipping/shipping_api');

        $service = shipping_api::getShippingServiceInfo($this->serviceID);
        $this->service = $service;
        
        if(empty($service)){
            throw new BusinessException('Service is not found.');
        }
                
//        $this->canadaPost->setCredentials($service->auth_key, $service->password);
//        $this->canadaPost->setShippingAccount($service->account_no, $service->meter_no);
//        $this->canadaPost->setServiceType($service->api_svc_code1);
//        $this->canadaPost->setEndPoint(empty($service->site_id)  ? '' : $service->site_id.'/rs/soap/rating/v3');
        $this->api = $api;
        $this->credential = $credential;
        $this->canadaPost->setCredentials($this->credential->auth_key, $this->credential->password);
        $this->canadaPost->setShippingAccount($this->credential->account_no, $this->credential->meter_no);
        //$this->canadaPost->setServiceType($api->service_code);
        $this->serviceType = $this->api->service_code;
        $this->canadaPost->setEndPoint(empty($this->api->site_id)  ? '' : $this->api->site_id.'/rs/soap/rating/v3');
        
        $this->FA = $service->factor_a;
        $this->FB = $service->factor_b;
        $this->CANADAPOST_PACKAGE_WEIGHT_LIMIT = $service->weight_limit;
        $this->CANADAPOST_PACKAGE_DIMENSIONS_LIMIT = $service->dimension_limit;
        //$this->canadaPostVAT = $service->price_includes_vat;
        $this->canadaPostVAT = $this->api->price_includes_vat;
    }

    private function initAddressShippingFrom()
    {
        ci()->load->library('addresses/addresses_api');
        ci()->load->library('settings/settings_api');

        $location = addresses_api::getLocationByID($this->locationID);
        $country = settings_api::getCountryByID($location->country_id);
        $this->country_from = $country;
        $stateOrProvinceCode = $location->state_code;

        $senderName = ShippingConfigs::DEFAULT_SENDER_NAME;
        $senderCompanyName = ShippingConfigs::DEFAULT_SENDER_COMPANY_NAME;
        $phoneNumber = ShippingConfigs::DEFAULT_SENDER_PHONE_NUMBER;
        $this->canadaPost->setShipper($senderName, $senderCompanyName, $phoneNumber, $location->street, $location->city, $stateOrProvinceCode, $location->postcode, $country->country_code);

        $this->FL = $location->shipping_factor_fl;
    }

    private function setAddressShippingTo()
    {
        ci()->load->library('settings/settings_api');

        // We can get these empty fields from table [customer_address] by customer_id.
        $recipientName = ''; // Get from [customer_address].shipment_address_name
        $companyName = ''; // Get from [customer_address].shipment_company
        $phoneNumber = ''; // Get from [customer_address].shipment_phone_number
        $stateOrProvinceCode = $this->getStateOrProvinceCode($this->city, $this->region);
        $this->country_to = settings_api::getCountryByID($this->countryID);
        $is_domestic = $this->country_from->country_code == $this->country_to->country_code;
        $this->canadaPost->setRecipient($recipientName, $companyName, $phoneNumber, $this->street, $this->city, $stateOrProvinceCode, $this->postalCode, $this->country_to->country_code, $is_domestic);
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

    private function getStateOrProvinceCode($input_city, $input_region)
    {
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
        // Calculate weight for price calculation
        $this->calculateVolumeWeight($packages, $this->FB);
        // Sort packages in descending of weight for price calculation
        $this->sortPackages($packages);
        // Get shipments from multiple packages
        $shipments = $this->calculateShipments($packages);
      
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
            // Height, Width, Length
            $H = $package[ShippingConfigs::PACKAGE_HEIGHT];
            $W = $package[ShippingConfigs::PACKAGE_WIDTH];
            $L = $package[ShippingConfigs::PACKAGE_LENGTH];

            // Volume weight (VW)
            $VW = round((($L * $W * $H) * 1.2) / $F, 2);

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
        foreach ($shipment as $package) {
            $VW += $package[ShippingConfigs::PACKAGE_VOLUME_WEIGHT];
            $RW += $package[ShippingConfigs::PACKAGE_WEIGHT];
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
                ShippingConfigs::PACKAGE_WEIGHT => $totalDimWeight
            );
        } 
        // if it is only one shipment, we do not need to re-calculate the dimensions. we can use the actual dimensions
        else {
            $newPackage = array(
                ShippingConfigs::PACKAGE_LENGTH => $package[ShippingConfigs::PACKAGE_LENGTH],
                ShippingConfigs::PACKAGE_WIDTH => $package[ShippingConfigs::PACKAGE_WIDTH],
                ShippingConfigs::PACKAGE_HEIGHT => $package[ShippingConfigs::PACKAGE_HEIGHT],
                ShippingConfigs::PACKAGE_WEIGHT => $totalDimWeight
            );
        }

        return $newPackage;
    }

    private function exceedLimitValues($totalRW, $totalVW, $totalLength)
    {
        return ($totalRW > $this->CANADAPOST_PACKAGE_WEIGHT_LIMIT) ||
        ($totalVW > $this->CANADAPOST_PACKAGE_WEIGHT_LIMIT) ||
        ($totalLength > $this->CANADAPOST_PACKAGE_DIMENSIONS_LIMIT);
    }
}