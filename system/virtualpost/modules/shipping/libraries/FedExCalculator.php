<?php defined('BASEPATH') or exit('No direct script access allowed');

class FedExCalculator
{
    private $fedex;

    private $FA; // Shipping service based Factor A => used for cost calculation
    private $FB; // Shipping service based Factor B => used for volume weight (VW) calculation
    private $FL; // Location based shipping factor FL
    private $FC; // Customer based shipping factor FC

    private $HCP; // % handling charge from CM price list
    private $HCA; // abs. handling charge from CM price list
    private $CC; // customs charge from CM price list

    private $customerID;
    private $serviceID;
    private $shippingType;
    private $customsValue;

    // Shipping from address
    private $locationID;

    // Shipping to address
    private $street;
    private $city;
    private $region;
    private $postalCode;
    private $countryID;

    public function __construct(array $params)
    {
        ci()->load->library('shipping/ShippingConfigs');
        ci()->load->library('shipping/FedEx');

        $this->initVariables($params);
        $this->initShippingService();
        $this->initAddressShippingFrom();
        $this->initCustomerShippingFactor();
        $this->initPricingTemplateValues();
        $this->setAddressShippingTo();
    }

    /**
     * Set the total number of packages in the entire shipment (even when the shipment spans multiple transactions.)
     */
    public function setTotalPackages($packageCount)
    {
        // The summed total of packages
        $this->fedex->setPackageCount($packageCount);
    }

    /**
     * Set a group of identical packages for shipment
     */
    public function addGroupPackages(array $package, $packageNum)
    {
        $this->fedex->addPackageToShipment($package, $packageNum);
    }

    /**
     * Set the only one package for shipment
     */
    public function addPackage(array $package)
    {
        $this->fedex->addPackageToShipment($package);
    }

    /**
     * Postal charge calculation
     * =========================
     * CP =P+[(UVP-P)*FA*FC*FL]
     * EP =(P+[(UVP-P)*FA*FC*FL])*(1+HCP)+HCA+CC
     * ---------------------------------
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
    public function calculate()
    {
        $result = $this->fedex->getRate();
        if (empty($result['error'])) {
            $rateResult = $result['result'];

            // net price that was calculated using the API for CM customer account
            $P = $rateResult->DiscountRate->TotalNetCharge;

            // official price calculation without customer account
            $UVP = $rateResult->OfficialRate->TotalNetCharge;

            // postal charge given to customer
            $CP = $P + ($UVP - $P) * $this->FA * $this->FC * $this->FL;

            // End net price given to customer
            $EP = $CP * (1 + $this->HCP) + $this->HCA + $this->CC;

            $shipmentCharges = array(
                'F' => $this->FA,
                'CC' => $this->CC,
                'HCP' => $this->HCP,
                'HCA' => $this->HCA,
                'P' => $P,
                'UVP' => $UVP,
                'CP' => $CP,
                'EP' => $EP
            );

            return $shipmentCharges;
        } else {
            return $result['error'];
        }
    }

    private function initVariables(array $params)
    {
        $this->customerID = APContext::getCustomerCodeLoggedIn();

        $this->locationID = $params[ShippingConfigs::LOCATION_ID];
        $this->serviceID = $params[ShippingConfigs::SERVICE_ID];
        $this->shippingType = $params[ShippingConfigs::SHIPPING_TYPE];
        $this->customsValue = empty($params[ShippingConfigs::CUSTOMS_VALUE]) ? 0 : $params[ShippingConfigs::CUSTOMS_VALUE];
        $this->street = $params[ShippingConfigs::STREET];
        $this->city = $params[ShippingConfigs::CITY];
        $this->region = $params[ShippingConfigs::REGION];
        $this->countryID = $params[ShippingConfigs::COUNTRY_ID];
        $this->postalCode = $params[ShippingConfigs::POSTAL_CODE];

        $this->fedex = ci()->fedex;
        $this->fedex->setTotalInsuredValue($this->customsValue);
    }

    private function initShippingService()
    {
        ci()->load->library('shipping/shipping_api');

        $service = shipping_api::getShippingServiceInfo($this->serviceID);

        $this->fedex->setCredentials($service->auth_key, $service->password);
        $this->fedex->setShippingAccount($service->account_no, $service->meter_no);
        $this->fedex->setServiceType($service->api_svc_code1);
        $this->fedex->setShippingChargesPayment($service->account_no);

        $this->FA = $service->factor_a;
        $this->FB = $service->factor_b;
    }

    private function initAddressShippingFrom()
    {
        ci()->load->library('addresses/addresses_api');
        ci()->load->library('settings/settings_api');

        $location = addresses_api::getLocationByID($this->locationID);
        $country = settings_api::getCountryByID($location->country_id);
        $stateOrProvinceCode = $this->getStateOrProvinceCode($location->city, $location->region);

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
        $recipientName = ''; // Get from [customer_address].shipment_address_name
        $companyName = ''; // Get from [customer_address].shipment_company
        $phoneNumber = ''; // Get from [customer_address].shipment_phone_number
        $stateOrProvinceCode = $this->getStateOrProvinceCode($this->city, $this->region);
        $country = settings_api::getCountryByID($this->countryID);
        $this->fedex->setRecipient($recipientName, $companyName, $phoneNumber, $this->street, $this->city, $stateOrProvinceCode, $this->postalCode, $country->country_code);
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

        $pricingMap = price_api::getPricingTemplateByLocationID($this->locationID, $postboxType);

        if ($this->shippingType == ShippingConfigs::DIRECT_SHIPPING) {
            $this->HCP = $pricingMap['send_out_directly'];
            $this->HCA = $pricingMap['shipping_plus'];
        } elseif ($this->shippingType == ShippingConfigs::COLLECT_SHIPPING) {
            $this->HCP = $pricingMap['collect_shipping_plus'];
            $this->HCA = $pricingMap['send_out_collected'];
        }

        if ($this->customsValue > 1000) {
            $this->CC = $pricingMap['custom_declaration_outgoing_01'];
        } else {
            $this->CC = $pricingMap['custom_declaration_outgoing_02'];
        }
    }

    private function getStateOrProvinceCode($city, $region)
    {
        $region = trim($region);
        $city = trim($city);
        if ($region) {
            $stateOrProvinceCode = (strlen($region) > 2) ? substr($region, 0, 2) : $region;
        } elseif ($city) {
            $stateOrProvinceCode = (strlen($city) > 2) ? substr($city, 0, 2) : $city;
        } else {
            $stateOrProvinceCode = '';
        }

        return strtoupper($stateOrProvinceCode);
    }
}