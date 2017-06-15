<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class shipping extends MY_Controller
{
    /**
     * Loads the gazillion of stuff, in Flash Gordon speed.
     */
    public function __construct ()
    {
        parent::__construct();
        $this->load->library('shipping/ShippingConfigs');
    }

    public function testFedEx()
    {
        $this->load->library('shipping/FedEx', array('mode' => ShippingConfigs::MODE_PRODUCTION));

        $this->fedex->setCredentials(ShippingConfigs::FEDEX_PRODUCTION_KEY, ShippingConfigs::FEDEX_PRODUCTION_PASSWORD);
        $this->fedex->setShippingAccount(ShippingConfigs::FEDEX_PRODUCTION_ACCOUNT_NUMBER, ShippingConfigs::FEDEX_PRODUCTION_METER_NUMBER);
        $this->fedex->setShippingChargesPayment(ShippingConfigs::FEDEX_PRODUCTION_ACCOUNT_NUMBER);
        $this->fedex->setServiceType('INTERNATIONAL_PRIORITY'); // Use shipping service code

        // Shipper or Sender is from-location of ClevverMail
        /*
        $senderName = '';//'Christian Hemmrich';
        $senderCompanyName = '';//'Clevvermail GmbH';
        $phoneNumber = ''; //'49-030467260711';
        $addressLine1 = 'Friedrichstr. 123';
        $cityName = 'BERLIN';
        $stateOrProvinceCode = 'BE'; // BE
        $postalCode = '10117';
        $countryCode = 'DE';
        */
        $senderName = '';
        $senderCompanyName = '';
        $phoneNumber = '';
        $addressLine1 = 'Winterhuder Weg 29';
        $cityName = 'Hamburg';
        $stateOrProvinceCode = 'HA'; // BE
        $postalCode = '22085';
        $countryCode = 'DE';
        $this->fedex->setShipper($senderName, $senderCompanyName, $phoneNumber, $addressLine1, $cityName, $stateOrProvinceCode, $postalCode, $countryCode);

        // Recipient is customer's forwarding address of ClevverMail
        /*
        $recipientName = 'Andriy Lavrys';
        $companyName = '';
        $phoneNumber = '+380509062090';
        $addressLine1 = 'Konyskogo 4/21';
        $cityName = 'Kovel';
        $stateOrProvinceCode = 'Vo';
        $postalCode = '45008';
        $countryCode = 'UA';
        */
        $recipientName = 'Kristiyan Slavchev';
        $companyName = '';
        $phoneNumber = '01727276069';
        $addressLine1 = 'Weilbachweg 4';
        $cityName = 'Münsing';
        $stateOrProvinceCode = 'De';
        $postalCode = '82541';
        $countryCode = 'UA';
        $this->fedex->setRecipient($recipientName, $companyName, $phoneNumber, $addressLine1, $cityName, $stateOrProvinceCode, $postalCode, $countryCode);

        // Set customs value
        $this->fedex->setTotalInsuredValue(100.0, 'EUR');

        // Calculation with one shipment (one shipment can contain one or more customer packages)
        $package = array(
            'Weight' => 60,
            'Length' => 70,
            'Width' => 50,
            'Height' => 65
        );
        $this->fedex->addPackageToShipment($package);

        $rateData = $this->fedex->getRate();
        $this->print_r($rateData);
    }

    public function testFedexCalculator()
    {
        // Shipment information
        $shippingInfo = array(
            ShippingConfigs::LOCATION_ID => 1, // from a selected Location
            ShippingConfigs::SERVICE_ID => 1,
            ShippingConfigs::SHIPPING_TYPE => 1,
            ShippingConfigs::CUSTOMS_VALUE => 10,
            ShippingConfigs::STREET => 'Weilbachweg 4',
            ShippingConfigs::POSTAL_CODE => '82541',
            ShippingConfigs::CITY => 'Münsing',
            ShippingConfigs::REGION => 'De',
            ShippingConfigs::COUNTRY_ID => 427
        );

        // Shipment information (Direct shipment with the only one package)
        $package = array(
            ShippingConfigs::PACKAGE_LENGTH => 60,
            ShippingConfigs::PACKAGE_WIDTH => 55,
            ShippingConfigs::PACKAGE_HEIGHT => 65,
            ShippingConfigs::PACKAGE_WEIGHT => 67,
        );

        $this->load->library('shipping/FedExRatesCalculator', $shippingInfo, 'fedexratescalculator');

        $shipmentCharges = $this->fedexratescalculator->calculateShippingRate($package);
        $this->print_r($shipmentCharges);
    }

    private function print_r($result)
    {
        echo "<pre>";
        print_r($result);
        echo "</pre>";
    }
}