<?php defined('BASEPATH') or exit('No direct script access allowed');

class shipping_api
{
    public static function getDataForPaging(array $conditions, $start, $limit, $sort_column, $sort_type)
    {
        ci()->load->model('shipping/shipping_apis_m');

        // Call search method
        $queryResult = ci()->shipping_apis_m->get_paging($conditions, $start, $limit, $sort_column, $sort_type);

        return $queryResult;
    }

    public static function addShippingAPI(array $data)
    {
        ci()->load->model('shipping/shipping_apis_m');

        $id = ci()->shipping_apis_m->create_shipping_api($data);

        return $id;
    }

    public static function getShippingAPIByID($id)
    {
        ci()->load->model('shipping/shipping_apis_m');

        $shippingAPI = ci()->shipping_apis_m->get($id);

        return $shippingAPI;
    }

    public static function updateShippingAPI($id, array $data)
    {
        ci()->load->model('shipping/shipping_apis_m');

        ci()->shipping_apis_m->update($id, $data);

        return true;
    }

    public static function deleteShippingAPI($id)
    {
        ci()->load->model('shipping/shipping_apis_m');

        ci()->shipping_apis_m->delete($id);

        return true;
    }

    public static function getAllShippingCarriers()
    {
        ci()->load->model('shipping/shipping_carriers_m');

        $carriers = ci()->shipping_carriers_m->get_all();

        return $carriers;
    }

    public static function getShippingServiceByID($shippingServiceID)
    {
        ci()->load->model('shipping/shipping_services_m');

        $shippingService = ci()->shipping_services_m->get($shippingServiceID);

        return $shippingService;
    }

    /**
     * Get list shipping service by ID.
     * 
     * @param array $shippingServiceIDs
     * @param type $shipping_service_type (0:Both| 1:national | 2:International)
     * @param type $returnArrayObjects
     * @return type
     */
    public static function getListShippingServicesByIDs(array $shippingServiceIDs, $shipping_service_type, $returnArrayObjects = true)
    {
        ci()->load->model('shipping/shipping_services_m');

        $shippingServices = ci()->shipping_services_m->get_shipping_services_by('shipping_services.id', $shippingServiceIDs, $shipping_service_type, $returnArrayObjects);

        return $shippingServices;
    }

    public static function getShippingServiceInfo($shippingServiceID)
    {
        ci()->load->model('shipping/shipping_services_m');

        $shippingServiceInfo = ci()->shipping_services_m->getShippingServiceInfo($shippingServiceID);

        return $shippingServiceInfo;
    }

    /**
     * FC = Customer based shipping factor FC
     * FL = Location based shipping factor FL
     */
    public static function calculateShippingRate(array $packages, array $shippingInfo, $service, $separate_package = true)
    {
        ci()->load->library('shipping/ShippingConfigs');
        
        $message = '{packages:'. json_encode($packages);
        $message .= ', shippingInfo: '. json_encode($shippingInfo);
        $message .= ', separate_package: '. json_encode($separate_package);
        $message .= ', service: '. json_encode($service);
        
        $rateData = array();
        $returnData = null;

        //Get all APIs for this service
        $apis = shipping_api::get_shipping_api_by_shipping_service($service->id);
        $api_codes = $apis['api_codes'];
        $api_credentials = $apis['api_credentials'];
        $message .= ', Shipping API : '. json_encode($apis);
        
        if (!empty($api_codes) && !empty($api_credentials)){
            foreach ($api_codes as $api_code) {
                $carrier_code =  $api_code->carrier_code;
                // Fedex
                if ($carrier_code == APConstants::FEDEX_CARRIER) {
                    ci()->load->library('shipping/FedExRatesCalculator');
                    //Call API for each credential
                    foreach($api_credentials as $api_credential) {
                        if ($api_code->api_id == $api_credential->api_id) {
                            $calculator = new FedExRatesCalculator();
                            $calculator->init($shippingInfo, $api_code, $api_credential);
                            $rateData[] = $calculator->calculateShippingRate($packages, $separate_package);
                        }
                    }
                } 
                // Canada post
                else if ($carrier_code == APConstants::CANADAPOST_CARRIER) {
                    ci()->load->library('shipping/CanadaPostRatesCalculator');
                    //Call API for each credential
                    foreach($api_credentials as $api_credential) {
                        if ($api_code->api_id == $api_credential->api_id) {
                            $calculator = new CanadaPostRatesCalculator();
                            $calculator->init($shippingInfo, $api_code, $api_credential);
                            $rateData[] = $calculator->calculateShippingRate($packages, $separate_package);
                        }
                    }

                }
                 // Shippo
                else if ($carrier_code == APConstants::SHIPPO_CARRIER) {
                    ci()->load->library('shipping/ShippoRatesCalculator');
                    //Call API for each credential
                    foreach($api_credentials as $api_credential) {
                        if ($api_code->api_id == $api_credential->api_id) {
                            $calculator = new ShippoRatesCalculator();
                            $calculator->init($shippingInfo, $api_code, $api_credential);
                            $rateData[] = $calculator->calculateShippingRate($packages, $separate_package);
                        }
                    }
                }
            }
        }

        //Get cheapest rate from result array
        if (!empty($rateData)) {
            //Remove element unavailable
            $rateData = array_filter($rateData, function($rate){
                return isset($rate['EP']);
            });
            //Sort array
            usort($rateData, function ($rate1, $rate2) {
                return $rate1['EP'] >= $rate2['EP'];
            });
            //Get cheapest rate
            $returnData = empty($rateData) ? null : $rateData[0];
        }
        
        $message .= ', $rateData: '. json_encode($returnData).'}';
        log_audit_message(APConstants::LOG_INFOR, $message, FALSE, 'calculateShippingRate');
        return $returnData;
    }
    
    /**
     * FC = Customer based shipping factor FC
     * FL = Location based shipping factor FL
     */
    public static function separatePackagesForCollectShipment(array $packages, array $shippingInfo)
    {
        $message = '{packages:'. json_encode($packages);
        $message = $message. ', shippingInfo: '. json_encode($shippingInfo).'}';
        log_audit_message(APConstants::LOG_INFOR, $message, FALSE, 'separatePackagesForCollectShipment');
        ci()->load->library('shipping/ShippingConfigs');
        ci()->load->library('shipping/FedExRatesCalculator');
        $calculator = new FedExRatesCalculator();
        $calculator->init($shippingInfo);
        $rateData = $calculator->separatePackagesForCollectShipment($packages);

        return $rateData;
    }
    
    /**
     * Get shipping service id by envelope.
     * @param type $envelope_id
     * @return array(shipping_service_id, type)
     */
    public static function getShippingServiceIdByEnvelope($envelope_id) {
        ci()->load->model('scans/envelope_m');
        ci()->load->model('addresses/location_m');
        ci()->load->model('mailbox/postbox_setting_m');
        ci()->load->library('scans/scans_api');
        
        $envelope = ci()->envelope_m->get($envelope_id);
        if (empty($envelope)) {
            $result = array(
                'shipping_service_id' => '',
                'shipping_service_type' => ''
            );
            return $result;
        }
        $customer_id = $envelope->to_customer_id;
        // If customer selected the shipping service id will return it
        if (!empty($envelope->shipping_rate_id)) {
            $result = array(
                'shipping_service_id' => $envelope->shipping_rate_id,
                'shipping_service_type' => 0
            );
            return $result;
        }
        
        $primary_letter_shipping = 0;
        $primary_international_letter_shipping = 0;
        $standard_national_parcel_service = 0;
        $standard_international_parcel_service = 0;
        
        // Get default shipping service id by postbox
        $postbox_setting = ci()->postbox_setting_m->get($envelope->postbox_id);
        
        if (!empty($postbox_setting)) {
            $primary_letter_shipping = $postbox_setting->standard_service_national_letter;
            $primary_international_letter_shipping = $postbox_setting->standard_service_international_letter;
            $standard_national_parcel_service = $postbox_setting->standard_service_national_package;
            $standard_international_parcel_service = $postbox_setting->standard_service_international_package;
        }
        
        // Get default shipping service id by location
        // Get postbox location
        $location = ci()->location_m->get($envelope->location_id);
        if (empty($postbox_setting) && empty($location)) {
            $result = array(
                'shipping_service_id' => '',
                'shipping_service_type' => ''
            );
            return $result;
        }
        
        $primary_letter_shipping = empty($primary_letter_shipping) ? $location->primary_letter_shipping : $primary_letter_shipping;
        $primary_international_letter_shipping = empty($primary_international_letter_shipping) ? $location->primary_international_letter_shipping : $primary_international_letter_shipping;
        $standard_national_parcel_service = empty($standard_national_parcel_service) ? $location->standard_national_parcel_service : $standard_national_parcel_service;
        $standard_international_parcel_service = empty($standard_international_parcel_service) ? $location->standard_international_parcel_service : $standard_international_parcel_service;
        
        //If collect shipping, check all item in package to decide default shipping service
        $package_items = array();
        if (!empty($envelope->package_id) && ($envelope->collect_shipping_flag == APConstants::OFF_FLAG)) {
            $package_items = ci()->envelope_m->get_many_by_many(array('package_id' => $envelope->package_id, 'collect_shipping_flag' => APConstants::OFF_FLAG));
        }
        //If direct shipping
        if (empty($package_items)) {
            $package_items[] = $envelope;
        }
        
        // Get all setting type
        $all_envelope_type = Settings::get_list(APConstants::ENVELOPE_TYPE_CODE);
        $all_envelope_type_letter = array();
        $all_envelope_type_package = array();
        // Get all letter type & package type
        foreach ($all_envelope_type as $envelope_type) {
            if ($envelope_type->Alias02 == 'Letter') {
                $all_envelope_type_letter [] = $envelope_type->ActualValue;
            }
            if ($envelope_type->Alias02 == 'Package') {
                $all_envelope_type_package [] = $envelope_type->ActualValue;
            }
        }
        
        // Get customer address
        $target_shipping_address = scans_api::getSelectedForwardingAddressOfEnvelopes($customer_id, $envelope->shipping_address_id);
        
        //Package shipping service
        if (shipping_api::isPackageShippingService($package_items, $all_envelope_type_package)){
             // Case 2: Envelope is Package and Shippment National
            if ($location->country_id === $target_shipping_address->shipment_country) {
                // forwarding country=same country as location : national letter
                $result = array(
                    'shipping_service_id' => $standard_national_parcel_service,
                    'shipping_service_type' => 2
                );
                return $result;
            } 
            // Case 3: Envelope is Package and Shippment international
            else {
                $result = array(
                    'shipping_service_id' => $standard_international_parcel_service,
                    'shipping_service_type' => 3
                );
                return $result;
            }
        } 
        //Letter shipping service
        else {
            //National
            if ($location->country_id === $target_shipping_address->shipment_country) {
                $result = array(
                    'shipping_service_id' => $primary_letter_shipping,
                    'shipping_service_type' => 1
                );
                return $result;
            } 
            //International
            else {
                 $result = array(
                    'shipping_service_id' => $primary_international_letter_shipping,
                    'shipping_service_type' => 1
                );
                return $result;
            }
        }
        
        $result = array(
            'shipping_service_id' => '0',
            'shipping_service_type' => ''
        );
        return $result;
    }
    
    private static function isPackageShippingService($package_items, $all_envelope_type_package){
        foreach ($package_items as $item) {
            $dimension_we = $item->weight;
            $item_type_id = $item->envelope_type_id;
            if (($dimension_we >= 500) || in_array($item_type_id, $all_envelope_type_package)) {
                return true;
            }
        }
        //is letter shipping service
        return false;
    }


    /**
     * Get shipping service id by envelope.
     * If 
     * @param type $envelope_id
     * @return array(shipping_service_id, type)
     */
    public static function getShippingAddressByEnvelope($customer_id, $envelope_id) {
        ci()->load->model('scans/envelope_m');
        ci()->load->model('scans/envelope_shipping_request_m');
        ci()->load->library('scans/scans_api');
        ci()->load->library('settings/settings_api');
        $envelope = ci()->envelope_m->get($envelope_id);
        $envelope_shipping_request_check = ci()->envelope_shipping_request_m->get_by_many(
            array(
                "customer_id" => $customer_id,
                "envelope_id" => $envelope_id
            ));
        
        $result = null;
        // Update detail request shipping
        if (!empty($envelope_shipping_request_check) && !empty($envelope_shipping_request_check->shipment_country)) {
            $result =  $envelope_shipping_request_check;
        } else {
            $shipping_address_id = !empty($envelope) ? $envelope->shipping_address_id : '';
            // Get customer address
            $customers_forward_address = scans_api::getSelectedForwardingAddressOfEnvelopes($customer_id, $shipping_address_id);
            $result = $customers_forward_address;
        }
        
        $message = '{customer_id:'. $customer_id . ', envelope_id:'.$envelope_id;
        $message = $message. ', customer_address: '. json_encode($result).'}';
        log_audit_message(APConstants::LOG_INFOR, $message, FALSE, 'getShippingAddressByEnvelope');
        
        // get country name of address
        $result->country_name = settings_api::getCountryNameByID($result->shipment_country);
        
        return $result;
    }
    
    /**
     * Save shipping address.
     * 
     * @param type $envelope_id
     * @param type $shipping_address_id
     */
    public static function saveShippingAddress($list_envelope_id, $shipping_address_id) {
        ci()->load->model('scans/envelope_m');
        ci()->load->model('scans/envelope_shipping_request_m');
        
        ci()->load->library('scans/scans_api');
        $envelope_ids = explode(",", $list_envelope_id);
        $envelope_id = $envelope_ids[0];
        $envelope = ci()->envelope_m->get($envelope_id);
        if (empty($envelope)) {
            return;
        }
        $customer_id = $envelope->to_customer_id;
        $postbox_id = $envelope->postbox_id;
        
        // Get customer address
        $customers_forward_address = scans_api::getSelectedForwardingAddressOfEnvelopes($customer_id, $shipping_address_id);
        if (empty($customers_forward_address)) {
            return;
        }
        
        $array_insert = array();
        $array_insert['customer_id'] = $customer_id;
        $array_insert['envelope_id'] = $envelope->id;
        $array_insert['postbox_id'] = $postbox_id;
        $array_insert['shipment_address_name'] = $customers_forward_address->shipment_address_name;
        $array_insert['shipment_city'] = $customers_forward_address->shipment_city;
        $array_insert['shipment_company'] = $customers_forward_address->shipment_company;
        $array_insert['shipment_country'] = $customers_forward_address->shipment_country;
        $array_insert['shipment_postcode'] = $customers_forward_address->shipment_postcode;
        $array_insert['shipment_region'] = $customers_forward_address->shipment_region;
        $array_insert['shipment_street'] = $customers_forward_address->shipment_street;
        $array_insert['shipment_phone_number'] = $customers_forward_address->shipment_phone_number;
        if ($array_insert['shipment_phone_number'] == null) {
            $array_insert['shipment_phone_number'] = '';
        }
        $array_insert['shipment_date'] = now();
        
        foreach($envelope_ids as $id){
            // Check exist shipping request
            $envelope_shipping_request_check = ci()->envelope_shipping_request_m->get_by_many(array(
                "customer_id" => $customer_id,
                "envelope_id" => $id
            ));
            
            $array_insert['envelope_id'] = $id;
            
            // Update detail request shipping
            if (empty($envelope_shipping_request_check)) {
                // Insert data to envelope_shipping
                ci()->envelope_shipping_request_m->insert($array_insert);
            } else {
                // update
                ci()->envelope_shipping_request_m->update_by_many( array(
                    "customer_id" => $customer_id,
                    "envelope_id" => $id
                ), $array_insert);
            }
        }
    }
    
    /**
     * Save service shipping fee information
     * @param type $envelope_id
     * @param type $shipping_fee
     * @param type $postal_charge
     * @param type $customs_handling
     * @param type $handling_charges
     * @param type $shipping_service_id
     */
    public static function saveShippingServiceFee($envelope_id, $shipping_fee, $postal_charge, $customs_handling, 
            $handling_charges, $number_parcel, $shipping_service_id) {
        ci()->load->model('scans/envelope_m');
        ci()->load->model('scans/envelope_shipping_request_m');
        
        $envelope = ci()->envelope_m->get($envelope_id);
        if (empty($envelope)) {
            return;
        }
        $customer_id = $envelope->to_customer_id;
        $postbox_id = $envelope->postbox_id;
        
        $array_insert = array();
        $array_insert['customer_id'] = $customer_id;
        $array_insert['envelope_id'] = $envelope->id;
        $array_insert['postbox_id'] = $postbox_id;
        $array_insert['shipping_fee'] = $shipping_fee;
        $array_insert['postal_charge'] = $postal_charge;
        $array_insert['customs_handling'] = $customs_handling;
        $array_insert['handling_charges'] = $handling_charges;
        $array_insert['number_parcel'] = $number_parcel;
        $array_insert['shipping_service_id'] = $shipping_service_id;
        $array_insert['shipment_date'] = now();
        
        // Check exist shipping request
        $envelope_shipping_request_check = ci()->envelope_shipping_request_m->get_by_many(
            array(
                "customer_id" => $customer_id,
                "envelope_id" => $envelope->id
            ));
        
        // Update detail request shipping
        if (empty($envelope_shipping_request_check)) {
            // Insert data to envelope_shipping
            ci()->envelope_shipping_request_m->insert($array_insert);
        } else {
            ci()->envelope_shipping_request_m->update_by_many(
                array(
                    "customer_id" => $customer_id,
                    "envelope_id" => $envelope->id
                ), $array_insert);
        }
        
    }
    
    /**
     * Shipping calculator function.
     * 
     * @param type $customerID The customer id
     * @param type $shippingInfo The shipping information (from address, to address)
     * @param type $number_of_parcels The number of shipment. Each shipment can have multiple parcel
     * @param type $length The length of package (use for one shipment - direct shipping)
     * @param type $width The width of package (use for one shipment - direct shipping)
     * @param type $height The height of package (use for one shipment - direct shipping)
     * @param type $weight The weight of package (use for one shipment - direct shipping)
     * @param type $multiple_quantity The list of parcel of shipment (use for many shipment - collect shipping) 2#1#2
     * @param type $multiple_number_shipment The list of number of shipment (use for many shipment - collect shipping) 1#2#3
     * @param type $multiple_length The list of length of shipment (use for many shipment - collect shipping) 20#10#20
     * @param type $multiple_width The list of width of shipment (use for many shipment - collect shipping) 25#15#25
     * @param type $multiple_height The list of height of shipment (use for many shipment - collect shipping) 25#15#25
     * @param type $multiple_weight  The list of weight of shipment (use for many shipment - collect shipping) 1000#2500#2500 (unit: g)
     * @param type $currency_id The currency id
     * @param type $separate_package_flag The flag specify need to sort and calculate package again
     * @return type
     */
    public static function shipping_calculator($customerID, $shippingInfo
            , $number_of_parcels,$length, $width, $height, $weight
            , $multiple_quantity, $multiple_number_shipment, $multiple_length, $multiple_width, $multiple_height, $multiple_weight
            ,$currency_id, $separate_package_flag = false){
        
        // load lib.
        ci()->load->library(array(
            'shipping/ShippingConfigs',
            'common/common_api',
            'settings/settings_api',
            'customers/customers_api',
            'addresses/addresses_api'
        ));

        ci()->lang->load('shipping/shipping');
        ci()->load->model('scans/envelope_m');
        
        // load helper
        ci()->load->helper('info/functions');
        
        $service = shipping_api::getShippingServiceInfo($shippingInfo[ShippingConfigs::SERVICE_ID]);
        if (empty($service)) {
            $message = lang('shipping_service.not_allow');
            $data = array('errors' => $message);
            return array(
                'status' => false,
                'data' => $data
            );
        }

        // Get list of shipment package
        $list_shipment_package = array();
        if ($number_of_parcels == 1) {
            $package_type = APConstants::ENVELOPE_TYPE_PACKAGE;
            if (empty($length) || empty($width) || empty($height)) {
                $package_type = APConstants::ENVELOPE_TYPE_LETTER;
            }
            $package = array(
                ShippingConfigs::PACKAGE_LENGTH => empty($length) ? 1: $length,
                ShippingConfigs::PACKAGE_WIDTH => empty($width) ? 1: $width,
                ShippingConfigs::PACKAGE_HEIGHT => empty($height) ? 1: $height,
                ShippingConfigs::PACKAGE_WEIGHT => floatval(str_replace(',', '.', $weight)) / 1000,
                ShippingConfigs::PACKAGE_TYPE => $package_type
            );
            $list_shipment_package[] = $package;
        } 
        // In this case the number of parcels will equals number of item in multiple_quantity
        else {
            $arrayQuantity = explode(ShippingConfigs::GROUP_PACKAGE_SEPARATOR, $multiple_quantity);
            $arrayNumberShipment = explode(ShippingConfigs::GROUP_PACKAGE_SEPARATOR, $multiple_number_shipment);
            $arrayLength = explode(ShippingConfigs::GROUP_PACKAGE_SEPARATOR, $multiple_length);
            $arrayWidth = explode(ShippingConfigs::GROUP_PACKAGE_SEPARATOR, $multiple_width);
            $arrayHeight = explode(ShippingConfigs::GROUP_PACKAGE_SEPARATOR, $multiple_height);
            $arrayWeight = explode(ShippingConfigs::GROUP_PACKAGE_SEPARATOR, $multiple_weight);
            
            // Call from customer information
            if (count($arrayNumberShipment) == 0) {
                $list_shipment_package = shipping_api::getAutoListShipmentPackage($arrayQuantity, $arrayNumberShipment, 
                        $arrayLength, $arrayWidth, $arrayHeight, $arrayWeight);
            }
            // Call from admin shipping UI
            else {
                $list_shipment_package = shipping_api::getManualListShipmentPackage($arrayQuantity, $arrayNumberShipment, 
                        $arrayLength, $arrayWidth, $arrayHeight, $arrayWeight, $service);
            }
        }
        
        // Separate list of shipment
        if ($separate_package_flag) {
            $list_shipment_package = shipping_api::separatePackagesForCollectShipment($list_shipment_package, $shippingInfo);
        }
        
        // Validate list of shipment
        foreach ($list_shipment_package as $package) {
            $validLimit = !self::exceedLimitPackage($service, $package);
            if (!$validLimit) {
                $message = '{package:'. json_encode($package);
                $message = $message. ', service: '. json_encode($service).'}';
                log_audit_message(APConstants::LOG_INFOR, $message, FALSE, 'calculateShippingRate-exceedLimitPackage');
                
                $data = array('errors' => lang('over_weight_error_message'));
                return array(
                    'status' => false,
                    'data' => $data
                );
            }
        }
        
        // For each shipment
        $result = shipping_api::calculateShippingRate($list_shipment_package, $shippingInfo, $service, false);
        
        if (is_array($result)) {
            // get vat.
            $VAT = APUtils::getVatRateOfCustomer($customerID)->rate;
            
            if($currency_id){
                $currency = settings_api::getCurrencyByID($currency_id);
            }else{
                $currency = customers_api::getStandardCurrency($customerID);
            }
            $decimalSeparator = customers_api::getStandardDecimalSeparator($customerID);

            // Postal Charge: P+[(UVP-P)*F*FC*FL]
            $postalCharge = $result['CP'];

            // Customs Handling: CC
            $customsHandling = $result['CC'];

            // Handling Charges: (P+[(UVP-P)*F*FC*FL])*(HCP)+HCA
            $handlingCharges = $postalCharge * $result['HCP'] + $result['HCA'];

            // VAT (0%): VAT * [(P+[(UVP-P)*F*FC*FL])*(1+HCP)+HCA+CC]
            $totalVAT = $VAT * $result['EP'];

            // Total Charge: (1+VAT) * [(P+[(UVP-P)*F*FC*FL])*(1+HCP)+HCA+CC]
            $totalCharge = (1 + $VAT) * $result['EP'];

            $data = array(
                'currency_short' => $currency->currency_short,
                'postal_charge' => common_api::convertCurrency($postalCharge, $currency->currency_rate, 2, $decimalSeparator),
                'customs_handling' => common_api::convertCurrency($customsHandling, $currency->currency_rate, 2, $decimalSeparator),
                'handling_charges' => common_api::convertCurrency($handlingCharges, $currency->currency_rate, 2, $decimalSeparator),
                'total_vat' => common_api::convertCurrency($totalVAT, $currency->currency_rate, 2, $decimalSeparator),
                'total_charge' => common_api::convertCurrency($totalCharge, $currency->currency_rate, 2, $decimalSeparator),
                'total_charge_no_vat' => common_api::convertCurrency($result['EP'], $currency->currency_rate, 2, $decimalSeparator),
                'carrier_code' => $result['CODE'],
                'api_code' => $result['API'],
                'api_credential' => $result['CREDENTIAL']
            );
            //Carrier account to create label by shippo api
            if (!empty($result['CARRIER'])){
                $data['carrier'] = $result['CARRIER'];
            }

            return array(
                'status' => true,
                "data" => $data
            );
        } else {
            $data = array('errors' => $result);
            return array(
                'status' => false,
                "data" => $data
            );
        }
    }
    
    /**
     * Get list of shipment package
     */
    public static function getAutoListShipmentPackage($arrayQuantity, $arrayNumberShipment, $arrayLength, 
                $arrayWidth, $arrayHeight, $arrayWeight) {
        $list_shipment_package = array();
        foreach ($arrayQuantity as $index => $quantity) {
            $package_type = APConstants::ENVELOPE_TYPE_PACKAGE;
            if (empty($arrayLength[$index]) || empty($arrayWidth[$index]) || empty($arrayHeight[$index])) {
                $package_type = APConstants::ENVELOPE_TYPE_LETTER;
            }
            $package = array(
                ShippingConfigs::PACKAGE_LENGTH => empty($arrayLength[$index]) ? 1 : $arrayLength[$index],
                ShippingConfigs::PACKAGE_WIDTH => empty($arrayWidth[$index]) ? 1: $arrayWidth[$index],
                ShippingConfigs::PACKAGE_HEIGHT => empty($arrayHeight[$index]) ? 1: $arrayHeight[$index],
                ShippingConfigs::PACKAGE_WEIGHT => floatval(str_replace(',', '.', $arrayWeight[$index])) / 1000,
                ShippingConfigs::PACKAGE_TYPE => $package_type
            );
            for ($i = 1; $i <= $quantity; $i++) {
                $list_shipment_package[] = $package;
            }
        }
        return $list_shipment_package;
    }
    
    /**
     * Get list of shipment package
     */
    public static function getManualListShipmentPackage($arrayQuantity, $arrayNumberShipment, $arrayLength, 
                $arrayWidth, $arrayHeight, $arrayWeight, $service) {
        
        $list_shipment_package = array();
        foreach ($arrayNumberShipment as $index => $shipment_number) {
            $package_type = APConstants::ENVELOPE_TYPE_PACKAGE;
            if (empty($arrayLength[$index]) || empty($arrayWidth[$index]) || empty($arrayHeight[$index])) {
                $package_type = APConstants::ENVELOPE_TYPE_LETTER;
            }
            $package = array(
                ShippingConfigs::PACKAGE_LENGTH => empty($arrayLength[$index]) ? 1 : $arrayLength[$index],
                ShippingConfigs::PACKAGE_WIDTH => empty($arrayWidth[$index]) ? 1: $arrayWidth[$index],
                ShippingConfigs::PACKAGE_HEIGHT => empty($arrayHeight[$index]) ? 1: $arrayHeight[$index],
                ShippingConfigs::PACKAGE_WEIGHT => floatval(str_replace(',', '.', $arrayWeight[$index])) / 1000,
                ShippingConfigs::PACKAGE_TYPE => $package_type
            );
            $quantity = $arrayQuantity[$index];
            $FB = $service->factor_b;
            if (key_exists($shipment_number, $list_shipment_package)) {
                for ($i = 1; $i <= $quantity; $i++) {

                    $list_shipment_package[$shipment_number][ShippingConfigs::PACKAGE_WEIGHT] 
                            += $package[ShippingConfigs::PACKAGE_WEIGHT];
                    
                    // calculate dimension
                    $dimension = shipping_api::calculateDimension($list_shipment_package[$shipment_number][ShippingConfigs::PACKAGE_WEIGHT], $FB);
                    
                    $list_shipment_package[$shipment_number][ShippingConfigs::PACKAGE_LENGTH] = $dimension;
                    $list_shipment_package[$shipment_number][ShippingConfigs::PACKAGE_WIDTH] = $dimension;
                    $list_shipment_package[$shipment_number][ShippingConfigs::PACKAGE_HEIGHT] = $dimension;
                    $list_shipment_package[$shipment_number][ShippingConfigs::PACKAGE_TYPE] = $package[ShippingConfigs::PACKAGE_TYPE];
                }
            } else {
                for ($i = 1; $i <= $quantity; $i++) {
                    $list_shipment_package[] = $package;
                }
            }
        }
        return $list_shipment_package;
    }
    
    /**
     * Calculate volumn weight.
     * 
     * @param type $H The height of package
     * @param type $W The weight of package
     * @param type $L The length of package
     * @param type $FB The factor
     */
    public static function calculateDimension($We, $FB) {
        // Volume weight (VW)
        $dimension = ceil(pow($We * $FB, 1 / 3));
        return $dimension;
    }
    
    /**
     * Calculate volumn weight.
     * 
     * @param type $H The height of package
     * @param type $W The weight of package
     * @param type $L The length of package
     * @param type $FB The factor
     */
    public static function calculateVolumeWeightItem($H, $W, $L, $FB) {
        // Volume weight (VW)
        // $VW = round((($L * $W * $H) * 1.2) / $FB, 2);
        // This package only check for 1 item, so we don't need to multiple with 1.2
        $VW = round((($L * $W * $H) * 1) / $FB, 2);
        return $VW;
    }
    
    /**
     * Check valid weight item.
     * 
     * @param type $EnvelopeID The envelope id
     */
    public static function checkValidCollectItem($EnvelopeID) {
        ci()->load->model('scans/envelope_properties_m');
        ci()->load->model('scans/envelope_m');
        ci()->load->library('addresses/addresses_api');
        ci()->load->library('shipping/ShippingConfigs');
        
        $envelope = ci()->envelope_m->get_by_many(array(
                'id' => $EnvelopeID
            ));
        
        if (empty($envelope)) {
            return false;
        }
        
        $location = addresses_api::getLocationByID($envelope->location_id);
        $available_shipping_services = isset($location->available_shipping_services) ? $location->available_shipping_services : '';
        $shippingServiceIDs = explode(',', $available_shipping_services);
            
        $H = 0; $W = 0; $L = 0;
        $dimension = ci()->envelope_properties_m->get_by_many(array(
            "envelope_id" => $EnvelopeID
        ));
        if (!empty($dimension)) {
            #1058 add multi dimension capability for admin
            $L =  floatval($dimension->length) == 0 ? '' : $dimension->length;
            $W =  floatval($dimension->width) == 0 ? '' : $dimension->width;
            $H =  floatval($dimension->height) == 0 ? '' : $dimension->height;
        }
        $valid = false;
        foreach ($shippingServiceIDs as $ShippingServiceID) {
            $service = shipping_api::getShippingServiceInfo($ShippingServiceID);
            if (empty($service)) {
                return true;
            }
            $FB = $service->factor_b;
            $VW = shipping_api::calculateVolumeWeightItem($H, $W, $L, $FB);
            $temp3 = 5 * ceil(pow($VW * $FB, 1 / 3));
            
            $valid = $valid || !self::exceedLimitValues($service, $W, $VW, $temp3);
        }
        
        return $valid;
    }
    
    /**
     *  Check valid weight item.
     * @param type $EnvelopeID
     * @param type $ShippingServiceID
     * @return boolean
     */
    public static function checkValidCollectItemByShippingService($EnvelopeID, $ShippingServiceID) {
        ci()->load->model('scans/envelope_properties_m');
        ci()->load->library('shipping/ShippingConfigs');
     
        $H = 0; $W = 0; $L = 0;
        $dimension = ci()->envelope_properties_m->get_by_many(array(
            "envelope_id" => $EnvelopeID
        ));
        if (!empty($dimension)) {
            #1058 add multi dimension capability for admin
            $L =  floatval($dimension->length) == 0 ? '' : $dimension->length;
            $W =  floatval($dimension->width) == 0 ? '' : $dimension->width;
            $H =  floatval($dimension->height) == 0 ? '' : $dimension->height;
        }
        
        $service = shipping_api::getShippingServiceInfo($ShippingServiceID);
        if (empty($service)) {
            return true;
        }
        $FB = $service->factor_b;
        $VW = shipping_api::calculateVolumeWeightItem($H, $W, $L, $FB);
        $temp3 = 5 * ceil(pow($VW * $FB, 1 / 3));

        $valid = !self::exceedLimitValues($service, $W, $VW, $temp3);
        return $valid;
    }
    
    private static function exceedLimitValues($service, $totalRW, $totalVW, $totalLength)
    {
        $weight_limit = $service->weight_limit;
        $dimension_limit = $service->dimension_limit;
        return ($totalRW > $weight_limit && !empty($weight_limit)) ||
        ($totalVW > $weight_limit && !empty($weight_limit)) ||
        ($totalLength > $dimension_limit && !empty($dimension_limit));
    }
    
    public static function exceedLimitPackage($service, array $package)
    {
        $weight_limit = $service->weight_limit;
        $dimension_limit = $service->dimension_limit;
        $totalW = $package[ShippingConfigs::PACKAGE_WEIGHT];
        $totalLength = $package[ShippingConfigs::PACKAGE_LENGTH];
        $totalWidth = $package[ShippingConfigs::PACKAGE_WIDTH];
        $totalHeght = $package[ShippingConfigs::PACKAGE_HEIGHT];
        
        return ($totalW > $weight_limit && !empty($weight_limit)) ||
        ($totalLength > $dimension_limit && !empty($dimension_limit)) || 
        ($totalWidth > $dimension_limit && !empty($dimension_limit)) ||
        ($totalHeght > $dimension_limit && !empty($dimension_limit));
    }
    
    /**
     * Get first available date
     */
    public static function getFirstAvailableDate($location_id, $package_info, $shipping_service_id) {
        ci()->load->library('addresses/addresses_api');
        $location = addresses_api::getLocationByID($location_id);
        $shipping_from = array(
            ShippingConfigs::STREET => $location->street,
            ShippingConfigs::CITY => $location->city,
            ShippingConfigs::COUNTRY_ID => $location->country_id,
            ShippingConfigs::POSTAL_CODE => $location->postcode,
            ShippingConfigs::REGION => $location->region
        );
        // Get shipping service id
        $service = shipping_api::getShippingServiceByID($shipping_service_id);
        
        $message = '{packages:'. json_encode($package_info);
        $message = $message. ', shippingInfo: '. json_encode($shipping_from);
        $message = $message. ', service: '. json_encode($service).'}';
        log_audit_message(APConstants::LOG_INFOR, $message, FALSE, 'getFirstAvailableDate');
        ci()->load->library('shipping/ShippingConfigs');
        ci()->load->library('shipping/PickupSchedule');
        $pickupSchedule = new PickupSchedule();
        $pickupSchedule->initVariables($shipping_from, $package_info, $service);
        $pickupSchedule->initAvailDateRequest();
        
        $list_available_date = $pickupSchedule->getAvailDate();
        var_dump($list_available_date);
        return count($list_available_date) > 0 ? $list_available_date[0] : null;
    }
    
    /**
     * Get first available date
     */
    public static function createPickupRequest($location_id, $package_info, $shipping_service_id) {
        ci()->load->library('addresses/addresses_api');
        $location = addresses_api::getLocationByID($location_id);
        $shipping_from = array(
            ShippingConfigs::STREET => $location->street,
            ShippingConfigs::CITY => $location->city,
            ShippingConfigs::COUNTRY_ID => $location->country_id,
            ShippingConfigs::POSTAL_CODE => $location->postcode,
            ShippingConfigs::REGION => $location->region,
            'ContactPersonName' => $location->location_name,
            'ContactCompanyName' => $location->location_name,
            'ContactPhoneNumber' => $location->phone_number
        );
        
        // Get shipping service id
        $service = shipping_api::getShippingServiceByID($shipping_service_id);
        
        $message = '{packages:'. json_encode($package_info);
        $message = $message. ', shippingInfo: '. json_encode($shipping_from);
        $message = $message. ', service: '. json_encode($service).'}';
        log_audit_message(APConstants::LOG_INFOR, $message, FALSE, 'createPickupRequest');
        ci()->load->library('shipping/ShippingConfigs');
        ci()->load->library('shipping/PickupSchedule');
        $pickupSchedule = new PickupSchedule();
        $pickupSchedule->initVariables($shipping_from, $package_info, $service);
        $pickupSchedule->initCreatePickupRequest();
        
        // Create pickup request
        $pickupSchedule->createPickupRequest();
    }
    
    /**
     * Calculate shipping cost (Using shipping service provier).
     * 
     * @param type $customer_id
     * @param type $list_envelopes
     * @param type $shipping_type (1: Direct Shipping | 2: Collect Shipping)
     * @return
     *      $data[$listShippingService->id] = array(
     *           'currency_short' => '',
     *           'postal_charge' => '',
     *           'customs_handling' => '',
     *           'handling_charges' => '',
     *           'total_vat' => '',
     *           'total_charge' => '',
     *           'raw_postal_charge' => '',
     *           'raw_customs_handling' => '',
     *           'raw_handling_charges' => '',
     *           'raw_total_charge' => '',
     *           'number_parcel' => '',
     *           'service_available_flag' => (0|1),
     *           'logo_url' => ''
     *       );
     */
    public static function calculateCostOfAllServices($customer_id, $list_envelopes, $envelope_id, $shipping_type) {
        ci()->load->model('scans/envelope_properties_m');
        ci()->load->model('scans/envelope_m');
        ci()->load->library('shipping/ShippingConfigs');
        ci()->load->library('common/common_api');
        ci()->load->library('shipping/shipping_api');
        ci()->load->library('settings/settings_api');
        ci()->load->library('customers/customers_api');
        ci()->load->library('mailbox/mailbox_api');
        ci()->load->library('addresses/addresses_api');
        ci()->load->helper('info/functions');
        ci()->lang->load('shipping/shipping');
        
        $customer = customers_api::getCustomerByID($customer_id);
        // Check input param
        if (empty($list_envelopes) || count($list_envelopes) == 0) {
            return null;
        }
        
        // Get first item
        $envelope = ci()->envelope_m->get($envelope_id);
        $location_id = $envelope->location_id;
        $customers_address = shipping_api::getShippingAddressByEnvelope($customer_id, $envelope_id);
        $location = addresses_api::getLocationByID($location_id);
        if (empty($customers_address) || empty($location)) {
            return null;
        }
        $shipping_service_type = '';
        if ($customers_address->shipment_country == $location->country_id) {
            $shipping_service_type = APConstants::SHIPPING_SERVICE_TYPE_NATIONAL;
        } else {
            $shipping_service_type = APConstants::SHIPPING_SERVICE_TYPE_INTERNATIONAL;
        }
        // Get list shipping services
        $available_shipping_services = isset($location->available_shipping_services) ? $location->available_shipping_services : '';
        $shippingServiceIDs = explode(',', $available_shipping_services);
        $listShippingServices = shipping_api::getListShippingServicesByIDs($shippingServiceIDs, $shipping_service_type, true);
        if (empty($listShippingServices) || count($listShippingServices) == 0) {
            return null;
        }
        
        // Init shipping information
        $process_shipping_type = $shipping_type;
        if (count($list_envelopes) == 1) {
            $process_shipping_type = ShippingConfigs::DIRECT_SHIPPING;
        }
        
        // Get total customs cost
        $total_customs_cost = mailbox_api::get_total_customs_cost($customer_id, $envelope_id);
        $shippingInfo = array(
            ShippingConfigs::CUSTOMER_ID => $customer_id,
            ShippingConfigs::SHIPPING_TYPE => $process_shipping_type,
            ShippingConfigs::CUSTOMS_VALUE => $total_customs_cost,
            ShippingConfigs::NAME => $customers_address->shipment_address_name,
            ShippingConfigs::PHONE_NUMBER => $customers_address->shipment_phone_number,
            ShippingConfigs::EMAIL => $customer->email,
            ShippingConfigs::COMPANY_NAME => $customers_address->shipment_company,
            ShippingConfigs::STREET => $customers_address->shipment_street,
            ShippingConfigs::POSTAL_CODE => $customers_address->shipment_postcode,
            ShippingConfigs::CITY => $customers_address->shipment_city,
            ShippingConfigs::REGION => $customers_address->shipment_region,
            ShippingConfigs::COUNTRY_ID => $customers_address->shipment_country,
            ShippingConfigs::LOCATION_ID => $location_id
        );
        
        // Init package information
        $packages = array();
        $all_envelope_type = APConstants::ENVELOPE_TYPE_LETTER;
        foreach ($list_envelopes as $item) {
            $envelope_properties = ci()->envelope_properties_m->get_by_many(array(
                'envelope_id' => $item->id
            ));
            $envelope_item = ci()->envelope_m->get_by_many(array(
                'id' => $item->id
            ));
            $envelope_type = $envelope_properties == null ? APConstants::ENVELOPE_TYPE_LETTER : APConstants::ENVELOPE_TYPE_PACKAGE;
            if ($envelope_type == APConstants::ENVELOPE_TYPE_PACKAGE) {
                $all_envelope_type = APConstants::ENVELOPE_TYPE_PACKAGE;
            }
            $package = array(
                ShippingConfigs::PACKAGE_LENGTH => $envelope_properties == null || $envelope_properties->length == null ? 1.0 : $envelope_properties->length,
                ShippingConfigs::PACKAGE_WIDTH => $envelope_properties == null || $envelope_properties->width == null ? 1.0 : $envelope_properties->width,
                ShippingConfigs::PACKAGE_HEIGHT => $envelope_properties == null || $envelope_properties->height == null ? 1.0 : $envelope_properties->height,
                ShippingConfigs::PACKAGE_WEIGHT => number_format($envelope_item->weight / 1000, 2),
                ShippingConfigs::PACKAGE_TYPE => $envelope_type
            );
            array_push($packages, $package);
        }
        
        // Prepare customer properties information
        $VAT = APUtils::getVatRateOfCustomer($customer_id)->rate;
        $currency = settings_api::getCurrencyByID($customer->currency_id);
        if(empty($currency)){
            $currency = customers_api::getStandardCurrency($customer_id);
        }
        $decimalSeparator = customers_api::getStandardDecimalSeparator($customer_id);

        // Filter by packaging type
        $filterListShippingServices = shipping_api::filterListShippingServices($listShippingServices, $all_envelope_type);
        if (empty($filterListShippingServices) || count($filterListShippingServices) == 0) {
            return null;
        }

        // Call Fedex service
        $data = array();
        foreach($filterListShippingServices as $shippingService){
            
            $shippingInfo[ShippingConfigs::SERVICE_ID] = $shippingService->id;
            $service = shipping_api::getShippingServiceInfo($shippingService->id);

            // If this service don't have API
            if (empty($service)) {
                $data[$shippingService->id] = array(
                    'currency_short' => '',
                    'postal_charge' => '',
                    'customs_handling' => '',
                    'handling_charges' => '',
                    'total_vat' => '',
                    'total_charge' => '',
                    'raw_postal_charge' => '',
                    'raw_customs_handling' => '',
                    'raw_handling_charges' => '',
                    'raw_total_charge' => '',
                    'number_parcel' => '',
                    'service_available_flag' => APConstants::OFF_FLAG,
                    'logo_url' => $shippingService->logo,
                    "shipping_service_id" => $shippingService->id,
                    "name" => $shippingService->name
                );
                continue;
            }

            // Validate package by shipping service
            $weight_limit = $service->weight_limit;
            $dimension_limit = $service->dimension_limit;
            $valid_demension = true;
            foreach ($packages as $package) {
                // is any of the items >68kg or 68kg dim weight?
                if (($package[ShippingConfigs::PACKAGE_WEIGHT] > $weight_limit && !empty($weight_limit)) 
                        || (calculateVolumeWeightOfPackage($package) > $dimension_limit) && !empty($dimension_limit)) {
                    $valid_demension = false;
                    break;
                    // throw new BusinessException(lang('over_weight_error_message'));
                }
            }

            // Check valid demension
            if (!$valid_demension) {
                continue;
            }

            // Call shipping calculate rate
            $result = shipping_api::calculateShippingRate($packages, $shippingInfo, $service, true);

            if (is_array($result)) {
                // Calculate number of parcel
                $number_parcel = 1;
                // Postal Charge: P+[(UVP-P)*F*FC*FL]
                $postalCharge = $result['CP'];
                // Customs Handling: CC
                $customsHandling = $result['CC'];
                // Handling Charges: (P+[(UVP-P)*F*FC*FL])*(HCP)+HCA
                $handlingCharges = $postalCharge * $result['HCP'] + $result['HCA'];
                // VAT (0%): VAT * [(P+[(UVP-P)*F*FC*FL])*(1+HCP)+HCA+CC]
                $totalVAT = $VAT * $result['EP'];
                // Total Charge: (1+VAT) * [(P+[(UVP-P)*F*FC*FL])*(1+HCP)+HCA+CC]
                $totalCharge = (1 + $VAT) * $result['EP'];
                $data[$shippingService->id] = array(
                    'currency_short' => $currency->currency_short,
                    'postal_charge' => common_api::convertCurrency($postalCharge, $currency->currency_rate, 2, $decimalSeparator),
                    'customs_handling' => common_api::convertCurrency($customsHandling, $currency->currency_rate, 2, $decimalSeparator),
                    'handling_charges' => common_api::convertCurrency($handlingCharges, $currency->currency_rate, 2, $decimalSeparator),
                    'total_vat' => common_api::convertCurrency($totalVAT, $currency->currency_rate, 2, $decimalSeparator),
                    'total_charge' => common_api::convertCurrency($totalCharge, $currency->currency_rate, 2, $decimalSeparator),
                    'raw_postal_charge' => $postalCharge,
                    'raw_customs_handling' => $customsHandling,
                    'raw_handling_charges' => $handlingCharges,
                    'raw_total_charge' => $totalCharge,
                    'number_parcel' => $number_parcel,
                    'service_available_flag' => APConstants::ON_FLAG,
                    'logo_url' => $shippingService->logo,
                    "shipping_service_id" => $shippingService->id,
                    "name" => $shippingService->name
                );
            }
            // Display N/A for other shipping service
            else {
                $show_calculation_fails = $service->show_calculation_fails;            
                if ($show_calculation_fails == APConstants::ON_FLAG) {
                    $data[$shippingService->id] = array(
                        'currency_short' => '',
                        'postal_charge' => '',
                        'customs_handling' => '',
                        'handling_charges' => '',
                        'total_vat' => '',
                        'total_charge' => '',
                        'raw_postal_charge' => '',
                        'raw_customs_handling' => '',
                        'raw_handling_charges' => '',
                        'raw_total_charge' => '',
                        'number_parcel' => '',
                        'service_available_flag' => APConstants::ON_FLAG,
                        'logo_url' => $shippingService->logo,
                        "shipping_service_id" => $shippingService->id,
                        "name" => $shippingService->name
                    );
                }
            }
        }
        return array('data' => $data, 'listShippingServices' => $filterListShippingServices);
    }
    
    /**
     * Filter shipping service list by packaging type.
     * 
     * @param type $listShippingServices
     * @param type $envelope_type
     */
    public static function filterListShippingServices($listShippingServices, $envelope_type, $shipping_type = array()) {
        // Filter by packaging type
        $filterListShippingServices = array();
        if ($envelope_type == APConstants::ENVELOPE_TYPE_PACKAGE) {
            foreach ($listShippingServices as $item) {
                if ($item->packaging_type != APConstants::SHIPPING_PACKAGING_TYPE_3) {
                    $filterListShippingServices[] = $item;
                }
            }
        } else {
            $filterListShippingServices = $listShippingServices;
        }
        
        if (!empty($shipping_type)) {
            foreach ($filterListShippingServices as $key => $item) {
                if (!in_array($item->service_type, $shipping_type)) {
                    unset($filterListShippingServices[$key]);
                }
            }
        }
        
        return $filterListShippingServices;
    }
    
    public static function get_shipping_services_by_postbox($postbox_id){
        ci()->load->model('mailbox/postbox_m');
        ci()->load->model('addresses/location_m');
        ci()->load->model('shipping/shipping_services_m');
        
        $location = ci()->postbox_m->select('location_available_id')->get($postbox_id);
        $shipping_service_ids_obj = ci()->location_m->select('available_shipping_services')->get($location->location_available_id);
        $shipping_services = array();
        if (!empty($shipping_service_ids_obj)) {
            $shipping_service_ids = explode(',', $shipping_service_ids_obj->available_shipping_services);
            $shipping_services = ci()->shipping_services_m->get_shipping_services_by('shipping_services.id', $shipping_service_ids, '');
        }
        
        return $shipping_services;
    }
    
    public static function get_standard_shipping_services_by_postbox($postbox_id){
        ci()->load->model('mailbox/postbox_m');
        ci()->load->model('addresses/location_m');
        
        $location_id = ci()->postbox_m->select('location_available_id')->get($postbox_id);
        $location = ci()->location_m->get($location_id->location_available_id);
        
        $result = array(
            'standard_service_national_letter' => 0,
            'standard_service_international_letter' => 0,
            'standard_service_national_package' => 0,
            'standard_service_international_package' => 0
        );
        
        if (!empty($location)){
            $result = array(
                'standard_service_national_letter' => $location->primary_letter_shipping,
                'standard_service_international_letter' => $location->primary_international_letter_shipping,
                'standard_service_national_package' => $location->standard_national_parcel_service,
                'standard_service_international_package' => $location->standard_international_parcel_service
            );
        }
        
        return $result;
    }
    
    public static function get_shipping_api_by_shipping_service($shipping_service_id) {
        ci()->load->model('shipping/shipping_services_m');
        ci()->load->model('shipping/shipping_credentials_m');
        ci()->load->model('shipping/shipping_apis_m');
        
        $shipping_service = ci()->shipping_services_m->get($shipping_service_id);
        $api_codes = array();
        $api_credentials = array();
        
        if (!empty($shipping_service)) {
            //Get shipping codes
            $shipping_api_codes = json_decode($shipping_service->shipping_api_code, true);
            if (!empty($shipping_api_codes)){
                //Assign refered api name
                foreach ($shipping_api_codes as $shipping_api_code){
                    $api_code_item = new stdClass();
                    $api_code_item->api_id = $shipping_api_code['api_id'];
                    $api_code_item->service_code = $shipping_api_code['service_code'];
                    //Get api detail                    
                    $api_code = ci()->shipping_apis_m->get_shipping_api_info($shipping_api_code['api_id']);
                    //Assign api info to item
                    $api_code_item->site_id = !empty($api_code) ? $api_code->site_id : null;
                    $api_code_item->carrier_code = !empty($api_code) ? $api_code->code : null;
                    $api_code_item->price_includes_vat = !empty($api_code) ? $api_code->price_includes_vat : null;
                    //Add to array data
                    $api_codes[] = $api_code_item;
                }
            }
            
            //Get shipping credentials
            $shipping_api_credentials = json_decode($shipping_service->shipping_api_credential, true);
            if (!empty($shipping_api_credentials)){
                //Assign refered api name
                foreach ($shipping_api_credentials as $shipping_api_credential){
                    $api_credential_item = new stdClass();
                    $api_credential_item->api_id = $shipping_api_credential['api_id'];
                    $api_credential_item->credential_id = $shipping_api_credential['credential_id'];
                    //Get credential detail                    
                    $api_credential = ci()->shipping_credentials_m->get($shipping_api_credential['credential_id']);
                    //Assign credential info to item
                    $api_credential_item->account_no = !empty($api_credential) ? $api_credential->account_no : null;
                    $api_credential_item->meter_no = !empty($api_credential) ? $api_credential->meter_no : null;
                    $api_credential_item->auth_key = !empty($api_credential) ? $api_credential->auth_key : null;
                    $api_credential_item->username = !empty($api_credential) ? $api_credential->username : null;
                    $api_credential_item->password = !empty($api_credential) ? $api_credential->password : null;
                    $api_credential_item->estamp_partner_signature = !empty($api_credential) ? $api_credential->estamp_partner_signature : null;
                    $api_credential_item->estamp_namespace = !empty($api_credential) ? $api_credential->estamp_namespace : null;
                    $api_credential_item->partner_id = !empty($api_credential) ? $api_credential->partner_id : null;
                    $api_credential_item->percental_partner_upcharge = !empty($api_credential) ? $api_credential->percental_partner_upcharge : null;
                    //Add to array data
                    $api_credentials[] = $api_credential_item;
                }
            }
        } 
        
        //Return array
        return array(
            'api_codes' => $api_codes,
            'api_credentials' => $api_credentials
        );
    }
    

}