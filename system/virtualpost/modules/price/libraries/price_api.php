<?php defined('BASEPATH') or exit('No direct script access allowed');

class price_api extends Core_BaseClass
{
    public static function getPricingTemplateByID($pricingTemplateID)
    {
        ci()->load->model('price/pricing_template_m');

        $row = ci()->pricing_template_m->get($pricingTemplateID);

        return $row;
    }

    public static function getPricingTemplateNameByID($pricingTemplateID)
    {
        ci()->load->model('price/pricing_template_m');

        $row = ci()->pricing_template_m->get($pricingTemplateID);
        $pricingTemplateName = ($row) ? $row->name : '';

        return $pricingTemplateName;
    }

    public static function getPricingModelByID($pricingTemplateID)
    {
        ci()->load->model('price/pricing_m');

        $pricingMaps = ci()->pricing_m->get_many_by(array("pricing_template_id" => $pricingTemplateID));

        return $pricingMaps;
    }

    public static function getAllPricingModels()
    {
        ci()->load->library('addresses/addresses_api');

        $allPricingTemplates = array();
        $locations = addresses_api::getAllLocations();
        foreach ($locations as $location) {
            $locationID = $location->id;
            $pricingTemplateID = $location->pricing_template_id;
            if ($pricingTemplateID) {
                $allPricingTemplates[$locationID] = self::getPricingModelByID($pricingTemplateID);
            }
        }

        // Get default pricing template model
        $allPricingTemplates[APConstants::DEfAULT_PRICING_MODEL_TEMPLATE] = self::getPricingModelByID(APConstants::DEfAULT_PRICING_MODEL_TEMPLATE);

        return $allPricingTemplates;
    }

    public static function getDefaultPricingModel()
    {
        $pricingTemplateID = APConstants::DEfAULT_PRICING_MODEL_TEMPLATE;
        $pricingTemplate = self::getPricingModelByID($pricingTemplateID);
        $pricingMap = self::buildPricingMapFromPricingModel($pricingTemplate);

        return $pricingMap;
    }

    public static function getPricingModelByLocationID($locationID, $accountType = 0)
    {
        ci()->load->library('addresses/addresses_api');

        $location = addresses_api::getLocationByID($locationID);
        $pricingTemplateID = APConstants::DEfAULT_PRICING_MODEL_TEMPLATE;
        if (is_object($location) && (!empty($location->pricing_template_id))) {
            $pricingTemplateID = $location->pricing_template_id;
        }
        $pricingTemplate = self::getPricingModelByID($pricingTemplateID);
        $pricingMap = self::buildPricingMapFromPricingModel($pricingTemplate, $accountType);

        return $pricingMap;
    }

    public static function getPricingModelByPostboxID($postboxID, $accountType = 0)
    {
        ci()->load->library('mailbox/mailbox_api');
        ci()->load->library('addresses/addresses_api');

        $postbox = mailbox_api::getPostBoxByID($postboxID);
        $location = addresses_api::getLocationByID($postbox->location_available_id);
        $pricingTemplateID = $location->pricing_template_id;
        if (empty($pricingTemplateID)) {
            $pricingTemplateID = APConstants::DEfAULT_PRICING_MODEL_TEMPLATE;
        }
        $pricingTemplate = self::getPricingModelByID($pricingTemplateID);
        $pricingMap = self::buildPricingMapFromPricingModel($pricingTemplate, $accountType);

        return $pricingMap;
    }

    public static function getRevShareMapByLocationID($locationID)
    {
        ci()->load->library('addresses/addresses_api');
        $location = addresses_api::getLocationByID($locationID);
        $pricingTemplateID = $location ? $location->pricing_template_id : "";
        if (empty($pricingTemplateID)) {
            $pricingTemplateID = APConstants::DEfAULT_PRICING_MODEL_TEMPLATE;
        }
        $pricingTemplate = self::getPricingModelByID($pricingTemplateID);
        $pricingMap = array();
        foreach ($pricingTemplate as $priceItem) {
            if (!array_key_exists($priceItem->account_type, $pricingMap)) {
                $pricingMap[$priceItem->account_type] = array();
            }
            $pricingMap[$priceItem->account_type][$priceItem->item_name] = $priceItem->rev_share_in_percent;
        }

        return $pricingMap;
    }

    public static function getAllTemplatesExclude($id)
    {
        ci()->load->model('price/pricing_template_m');

        return ci()->pricing_template_m->get_all_templates_exclude($id);
    }

    public static function getAllTemplateByID($id)
    {
        ci()->load->model('price/pricing_template_m');

        return ci()->pricing_template_m->get_all_template_by($id);
    }

    public static function getDefaultTemplate()
    {
        ci()->load->model('price/pricing_template_m');

        $defaultTemplate = ci()->pricing_template_m->get_default_template();

        return $defaultTemplate;
    }

    public static function getAllTemplateExcludeDefault()
    {
        ci()->load->model('price/pricing_template_m');

        $allTemplateExcludeDefault = ci()->pricing_template_m->get_all_template_exclude_default();

        return $allTemplateExcludeDefault;
    }

    /**
     * Gets pricing map by location Id
     * @param unknown $locationId
     */
    public static function getPricingMapByLocationId($locationId)
    {
        ci()->load->model(array(
            'price/pricing_m',
            'addresses/location_m',
            "addresses/location_customers_m"
        ));

        // get location
        $location = ci()->location_m->get_by_many(array(
            'id' => $locationId
        ));

        // get pricing template id
        $templateId = APConstants::DEfAULT_PRICING_MODEL_TEMPLATE;
        if ($location) {
            $templateId = $location->pricing_template_id;
        }

        // Get don gia cua tat ca cac loai account type
        $pricings = ci()->pricing_m->get_pricing_by_template($templateId);

        $pricing_map = array();
        foreach ($pricings as $price) {
            if (!array_key_exists($price->account_type, $pricing_map)) {
                $pricing_map[$price->account_type] = array();
            }
            $pricing_map[$price->account_type][$price->item_name] = $price;
        }

        return $pricing_map;
    }
    
    /**
     * Gets rev share of location.
     * @param type $location_id
     */
    public static function getRevShareOfLocation($location_id){
        ci()->load->model("addresses/location_m");
        
        $location = ci()->location_m->get_by_many(array(
            'id' =>$location_id
        ));
        if($location){
            return $location->rev_share;
        }
        
        return 0;
    }
    
    public static function getAllPricingsGroupByTemplate(){
        ci()->load->model(array(
            'price/pricing_m',
            'price/pricing_template_m'
        ));
        
        // get all pricing tempalte 
        $pricing_template = ci()->pricing_template_m->get_all_public_template();
        $results = array();
        foreach($pricing_template as $t){
            $results[$t->id] = '';
        }
        
        // Gets all pricings
        $pricings = ci()->pricing_m->get_all();
        foreach($pricings as $price){
            $results[$price->pricing_template_id][$price->account_type] [$price->item_name] = $price->item_value;
        }
        
        return  $results;
    }

    private static function buildPricingMapFromPricingModel($pricingTemplate, $accountType = 0)
    {
        $pricingMap = array();
        foreach ($pricingTemplate as $priceItem) {
            if (!array_key_exists($priceItem->account_type, $pricingMap)) {
                $pricingMap[$priceItem->account_type] = array();
            }
            $pricingMap[$priceItem->account_type][$priceItem->item_name] = $priceItem->item_value;
        }
        if ($accountType) {
            return array_key_exists($accountType, $pricingMap) ? $pricingMap[$accountType] : array();
        } else {
            return $pricingMap;
        }
    }
    
    private static function buildPricingMapFromPricingModelAndPricingType($pricingTemplate, $pricingType, $isOwnerLocation)
    {
        $pricingMap = array();
        // Case 1: Pricing type is Normal
        if ($pricingType == APConstants::CUSTOMER_PRICING_TYPE_NORMAL) {
            // Case 1.1: This is posstbox of owner location
            if ($isOwnerLocation) {
                foreach ($pricingTemplate as $priceItem) {
                    if (!array_key_exists($priceItem->account_type, $pricingMap)) {
                        $pricingMap[$priceItem->account_type] = array();
                    }
                    $pricingMap[$priceItem->account_type][$priceItem->item_name] = $priceItem->item_value_owner;
                }
            }
            // Case 1.2: This is external postbox
            else {
                foreach ($pricingTemplate as $priceItem) {
                    if (!array_key_exists($priceItem->account_type, $pricingMap)) {
                        $pricingMap[$priceItem->account_type] = array();
                    }
                    $pricingMap[$priceItem->account_type][$priceItem->item_name] = $priceItem->item_value;
                }
            }
        }
        // Case 2: Pricing type is Special
        else if ($pricingType == APConstants::CUSTOMER_PRICING_TYPE_SPECIAL) {
            // Case 1.1: This is posstbox of owner location
            if ($isOwnerLocation) {
                foreach ($pricingTemplate as $priceItem) {
                    if (!array_key_exists($priceItem->account_type, $pricingMap)) {
                        $pricingMap[$priceItem->account_type] = array();
                    }
                    $pricingMap[$priceItem->account_type][$priceItem->item_name] = $priceItem->item_value_owner_special;
                }
            }
            // Case 1.2: This is external postbox
            else {
                foreach ($pricingTemplate as $priceItem) {
                    if (!array_key_exists($priceItem->account_type, $pricingMap)) {
                        $pricingMap[$priceItem->account_type] = array();
                    }
                    $pricingMap[$priceItem->account_type][$priceItem->item_name] = $priceItem->item_value_special;
                }
            }
        } 
        // Standard customer
        else {
            foreach ($pricingTemplate as $priceItem) {
                if (!array_key_exists($priceItem->account_type, $pricingMap)) {
                    $pricingMap[$priceItem->account_type] = array();
                }
                $pricingMap[$priceItem->account_type][$priceItem->item_name] = $priceItem->item_value;
            }
        }
        return $pricingMap;
    }


    /**
     * List all users
     */
    public static function list_price_template($array_condition, $input_paging, $limit)
    {
        ci()->load->model('price/pricing_template_m');
        ci()->load->model("addresses/location_m");

        $query_result = ci()->pricing_template_m->get_price_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);
        
        $total = $query_result ['total'];
        $datas = $query_result ['data'];

        $response = self::get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);
        $datas_mobile = array();
        $i = 0;
        foreach ($datas as $row) {
            // Gets number location used.
            $number_location = ci()->location_m->count_by_many(array(
                "pricing_template_id" => $row->id
            ));
            
            $response->rows [$i] ['id'] = $row->id;
            $response->rows [$i] ['cell'] = array(
                $row->id,
                $row->name,
                $number_location,
                $row->pricing_type,
                $row->description,
                $row->id
            );
            
            $datas_mobile[$i] = $row;

            $i++;
        }

        return  array(
            "mobile_list_price_template" => $datas_mobile,
            "web_list_price_template"    => $response
        );
        
    }
    
    /**
     * List all users
     */
    public static function list_phone_number_price($array_condition, $input_paging, $limit)
    {
        ci()->load->model('phones/pricing_phones_number_m');
        ci()->lang->load('phones/phones');
        
        $query_result = ci()->pricing_phones_number_m->get_price_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);
        
        $total = $query_result ['total'];
        $datas = $query_result ['data'];

        $response = self::get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);
        $i = 0;
        foreach ($datas as $row) {
            $response->rows [$i] ['id'] = $row->id;
            $response->rows [$i] ['cell'] = array(
                $row->id,
                $row->country_name,
                lang('number_type_'.$row->type),
                APUtils::number_format($row->one_time_fee, 2). ' ' .$row->currency,
                APUtils::number_format($row->one_time_fee_upcharge, 2). ' ' .$row->currency,
                APUtils::number_format($row->recurring_fee, 2). ' ' .$row->currency,
                APUtils::number_format($row->recurring_fee_upcharge, 0). '%',
                APUtils::number_format($row->per_min_fee, 2). ' ' .$row->currency,
                APUtils::number_format($row->per_min_fee_upcharge, 2). ' ' .$row->currency,
                lang('recurrence_interval_'.$row->recurrence_interval),
                $row->id
            );
            $i++;
        }

        return $response;
        
    }
    
    /**
     * List all users
     */
    public static function list_phone_number_price_for_customer($array_condition, $customer_id, $input_paging, $limit)
    {
        ci()->load->model('phones/pricing_phones_number_customer_m');
        ci()->lang->load('phones/phones');
        ci()->load->library('phones/phones_api');
        
        // Check existing default pricing
        $total_record = ci()->pricing_phones_number_customer_m->count_by_many(array(
            "customer_id" => $customer_id
        ));
        if ($total_record == 0) {
            // Call method to init default data
            phones_api::init_phone_number_price($customer_id);
        }
        
        $array_condition['pricing_phones_number_customer.customer_id'] = $customer_id;
        $query_result = ci()->pricing_phones_number_customer_m->get_price_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);
        
        $total = $query_result ['total'];
        $datas = $query_result ['data'];

        $response = self::get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);
        $i = 0;
        foreach ($datas as $row) {
            $response->rows [$i] ['id'] = $row->id;
            $response->rows [$i] ['cell'] = array(
                $row->id,
                $row->country_name,
                lang('number_type_'.$row->type),
                APUtils::number_format($row->one_time_fee, 2). ' ' .$row->currency,
                APUtils::number_format($row->one_time_fee_upcharge, 2). ' ' .$row->currency,
                APUtils::number_format($row->recurring_fee, 2). ' ' .$row->currency,
                APUtils::number_format($row->recurring_fee_upcharge, 0). '%',
                APUtils::number_format($row->per_min_fee, 2). ' ' .$row->currency,
                APUtils::number_format($row->per_min_fee_upcharge, 2). ' '.$row->currency,
                lang('recurrence_interval_'.$row->recurrence_interval),
                $row->id
            );
            $i++;
        }

        return $response;
        
    }
    
    /**
     * List all users
     */
    public static function list_outbound_call_price($array_condition, $input_paging, $limit)
    {
        ci()->load->model('phones/pricing_phones_outboundcalls_m');
        ci()->lang->load('phones/phones');
        
        $query_result = ci()->pricing_phones_outboundcalls_m->get_price_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);
        
        $total = $query_result ['total'];
        $datas = $query_result ['data'];

        $response = self::get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);
        $i = 0;
        foreach ($datas as $row) {
            $response->rows [$i] ['id'] = $row->id;
            $response->rows [$i] ['cell'] = array(
                $row->id,
                $row->pricing_name,
                APUtils::number_format($row->usage_fee, 2). ' ' .$row->currency,
                APUtils::number_format($row->usage_fee_upcharge, 0). '%',
                $row->id
            );
            $i++;
        }

        return $response;
        
    }
    
    /**
     * List all users
     */
    public static function list_outbound_call_price_for_customer($array_condition, $customer_id, $input_paging, $limit)
    {
        ci()->load->model('phones/pricing_phones_outboundcalls_customer_m');
        ci()->lang->load('phones/phones');
        ci()->load->library('phones/phones_api');
        
        // Check existing default pricing
        $total_record = ci()->pricing_phones_outboundcalls_customer_m->count_by_many(array(
            "customer_id" => $customer_id
        ));
        if ($total_record == 0) {
            // Call method to init default data
            phones_api::init_customer_outboundcalls_price($customer_id);
        }
        
        $array_condition['pricing_phones_outboundcalls_customer.customer_id'] = $customer_id;
        $query_result = ci()->pricing_phones_outboundcalls_customer_m->get_price_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);
        
        $total = $query_result ['total'];
        $datas = $query_result ['data'];

        $response = self::get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);
        $i = 0;
        foreach ($datas as $row) {
            $response->rows [$i] ['id'] = $row->id;
            $response->rows [$i] ['cell'] = array(
                $row->id,
                $row->pricing_name,
                APUtils::number_format($row->usage_fee, 2). ' ' .$row->currency,
                APUtils::number_format($row->usage_fee_upcharge, 0). '%',
                $row->id
            );
            $i++;
        }
        return $response;
        
    }
    
    /**
     * This method return the original pricing value setting by ClevverMail administrator.
     * It was not included the upcharge value.
     * The upcharge value only use when generate the reporting and invoices for enduser enterprise customer.
     * 
     * @param type $customer_id - The customer id
     * @param type $location_id - The location id
     * @return type
     */
    public static function getPricingModelByCusotomerAndLocationID($customer_id, $location_id)
    {
        ci()->load->library('addresses/addresses_api');
        $location = addresses_api::getLocationByID($location_id);
        $customer_location = addresses_api::getEnterpriseLocationByID($location_id);
        $customer = CustomerUtils::getCustomerByID($customer_id);
        $is_enterprise_customer = $customer != null && $customer->account_type == APConstants::ENTERPRISE_CUSTOMER;
        $is_owner_location = $customer_location != null && !empty($customer_location->parent_customer_id) && $customer_location->parent_customer_id == $customer->parent_customer_id;
        $parent_customer_id = $customer->parent_customer_id;
        $pricing_type = APConstants::CUSTOMER_PRICING_TYPE;
        if (!empty($parent_customer_id)) {
            $pricing_type = AccountSetting::get($parent_customer_id, APConstants::CUSTOMER_PRICING_TYPE);
        }
        
        
        $pricing_template_id = APConstants::DEfAULT_PRICING_MODEL_TEMPLATE;
        // Case 1: Standard customer
        if (!$is_enterprise_customer) {
            // Case 1.1: Location is clevvermail location
            if ($customer_location == null) {
                $pricing_template_id = $location->pricing_template_id;
            }
            // Case 1.2: Location is enterprise open location
            else {
                if ($is_owner_location) {
                    $pricing_template_id = $location->enterprise_pricing_template_id;
                    if (empty($pricing_template_id)) {
                        $pricing_template_id = $location->pricing_template_id;
                    } 
                } else {
                    $pricing_template_id = $location->pricing_template_id;
                }
            }
        }
        // Case 2: Enterprise customer
        else {
            // Case 2.1: Location is clevvermail location
            if ($customer_location == null) {
                $pricing_template_id = $location->pricing_template_id;
            }
            // Case 2.2: Location is enterprise open location
            else if (($customer_location != null) && $location->share_external_flag == APConstants::ON_FLAG) {
                if ($is_owner_location) {
                    $pricing_template_id = $location->enterprise_pricing_template_id;
                    if (empty($pricing_template_id)) {
                        $pricing_template_id = $location->pricing_template_id;
                    } 
                } else {
                    $pricing_template_id = $location->pricing_template_id;
                }
            }
            // Case 2.3: Location is enterprise closed location
            else if (($customer_location != null) && $location->share_external_flag != APConstants::ON_FLAG) {
                $pricing_template_id = $location->pricing_template_id;
            }
        }
        
        // Make sure always had pricing template id
        if (empty($pricing_template_id)) {
            $log_message = 'Can not get pricing template of customer id: '. $customer_id. ' and location id: '.$location_id;
            log_audit_message(APConstants::LOG_ERROR, $log_message, null, 'get_pricing');
            $pricing_template_id = APConstants::DEfAULT_PRICING_MODEL_TEMPLATE;
        } 
        // Get pricing template data
        $pricingTemplate = self::getPricingModelByID($pricing_template_id);
        
        
        // Convert pricing value to map object
        // We should use item_value, item_value_owner, item_value_special or item_value_owner_special depend on the pricing type
        $pricingMap = self::buildPricingMapFromPricingModelAndPricingType($pricingTemplate, $pricing_type, $is_owner_location);

        return $pricingMap;
    }
}