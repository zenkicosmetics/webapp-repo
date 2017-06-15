<?php defined('BASEPATH') or exit('No direct script access allowed');

class ShippingConfigs
{
    // Shipping types
    const DIRECT_SHIPPING = 1;
    const COLLECT_SHIPPING = 2;

    // Shipment information
    const LOCATION_ID = 'LocationID';
    const CUSTOMER_ID = 'CustomerID';
    const SERVICE_ID = 'ServiceID';
    const SHIPPING_TYPE = 'ShippingType';
    const CUSTOMS_VALUE = 'CustomsValue';
    const NAME = 'Name';
    const PHONE_NUMBER = 'PhoneNumber';
    const EMAIL = 'Email';
    const COMPANY_NAME = 'CompanyName';
    const STREET = 'Street';
    const CITY = 'City';
    const REGION = 'Region';
    const COUNTRY_ID = 'CountryID';
    const POSTAL_CODE = 'PostalCode';
    const PACKAGE_COUNT = 'PackageCount';

    const GROUP_PACKAGE_SEPARATOR = '#';

    // Package information
    const PACKAGE = 'Package';
    const PACKAGE_QUANTITY = 'Quantity';
    const PACKAGE_VOLUME_WEIGHT = 'VW';
    const PACKAGE_LENGTH = 'Length';
    const PACKAGE_WIDTH = 'Width';
    const PACKAGE_HEIGHT = 'Height';
    const PACKAGE_WEIGHT = 'Weight';
    const PACKAGE_NUMBERSHIPMENT = 'NumberShipment';
    const PACKAGE_TYPE = 'PackageType';

    // Sender's default values
    const DEFAULT_SENDER_NAME = 'Christian Hemmrich';
    const DEFAULT_SENDER_COMPANY_NAME = 'Clevvermail GmbH';
    const DEFAULT_SENDER_PHONE_NUMBER = '49-030467260711';
    const DEFAULT_SENDER_EMAIL = 'register@cleevermail.com';

    // Recipient's default values
    const DEFAULT_RECIPIENT_NAME = '';
    const DEFAULT_RECIPIENT_COMPANY_NAME = '';
    const DEFAULT_RECIPIENT_PHONE_NUMBER = '';

    // Default values
    const DEFAULT_LOCATION_ID = 0;
    const DEFAULT_COUNTRY_ID = 282; // Germany
    const DEFAULT_CURRENCY = 'EUR';
    const DEFAULT_FACTOR_F = 1.0;
    const DEFAULT_FACTOR_FL = 1.0;
    const DEFAULT_FACTOR_FC = 1.0;

    // Mode to call shipping API
    const MODE_TEST = 1;
    const MODE_PRODUCTION = 2;

    const FEDEX_PACKAGE_WEIGHT_LIMIT = 68; // 68 KG
    const FEDEX_PACKAGE_DIMENSIONS_LIMIT = 330; // 330 CM
    const FEDEX_FACTOR_B = 5000;

    // FedEx Test Credentials (test ClevverMail account at FedEx)
    const FEDEX_TEST_ACCOUNT_NUMBER = '601619601';
    const FEDEX_TEST_PASSWORD = 'I4aXn9YHm1TNaVg1UIwQx3o9k';
    const FEDEX_TEST_METER_NUMBER = '100259254';
    const FEDEX_TEST_KEY = '8bM2KLjWexxIji2A';
    const FEDEX_TEST_URL = 'https://wsbeta.fedex.com:443/web-services';

    // FedEx Live Credentials (live ClevverMail account at FedEx)
    const FEDEX_PRODUCTION_ACCOUNT_NUMBER = '223665330';
    const FEDEX_PRODUCTION_PASSWORD = 'aT6s8JAm14QZWikMYEwTD4jGW';
    const FEDEX_PRODUCTION_METER_NUMBER = '109123356';
    const FEDEX_PRODUCTION_KEY = 'xM5IDygB8kD78mhP';
    const FEDEX_PRODUCTION_URL = 'https://ws.fedex.com:443/web-services';
    
    const SHIPPO_TEST_API_KEY = 'shippo_test_42b92649652a52f75dbb2a07465e170b61eb1489';

}