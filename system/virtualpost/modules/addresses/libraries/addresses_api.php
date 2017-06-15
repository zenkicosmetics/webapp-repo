<?php defined('BASEPATH') or exit('No direct script access allowed');

class addresses_api extends base_api
{
    public function __construct() {

        ci()->load->model(array(
            'addresses/location_m',
            'price/pricing_template_m',
            'addresses/location_pricing_m',
            'price/pricing_m'
            
        ));
        ci()->lang->load(array(
            'addresses/address'
        ));
        
    }

    public static function getLocationByID($locationID)
    {
        ci()->load->model('addresses/location_m');

        $location = ci()->location_m->get($locationID);

        return $location;
    }
    
    public static function getEnterpriseLocationByID($locationID)
    {
        ci()->load->model('addresses/customer_location_m');

        $location = ci()->customer_location_m->get($locationID);

        return $location;
    }

    public static function getLocationPricingByID($locationID)
    {
        ci()->load->model('addresses/location_pricing_m');

        $locationPricing = ci()->location_pricing_m->get_by(array("location_id" => $locationID));

        return $locationPricing;
    }

    public static function deleteAlternativeAddress($id){

        ci()->load->model('addresses/customers_forward_address_m');

        $resultDelete = ci()->customers_forward_address_m->delete($id);

        return $resultDelete;
    }

    public static function getExtraCustomerAddress($customer_id)
    {
        ci()->load->model('addresses/customers_address_m');

        $customer_address = ci()->customers_address_m->get_customer_address($customer_id);

        return $customer_address;
    }

    public static function getCustomerAddress($customerID)
    {
        ci()->load->model('addresses/customers_address_m');

        $customersAddress = ci()->customers_address_m->get_invoicing_address_by($customerID);

        return $customersAddress;
    }

    public static function getAllLocations()
    {
        ci()->load->model('addresses/location_m');

        $rows = ci()->location_m->get_all();

        return $rows;
    }

    public static function getAllLocationsForDropDownList()
    {
        ci()->load->model('addresses/location_m');

        $rows = ci()->location_m->getAllLocationsForDropDownList();

        return $rows;
    }

    public static function createLocation(array $paramNames, array $paramValues)
    {
        ci()->load->model('addresses/location_m');

        $params = self::getArrayParams($paramNames, $paramValues);

        $locationID = ci()->location_m->insert($params);

        return $locationID;
    }

    public static function getLocationPublic($sent_daily_reminder_flag = 0)
    {
        ci()->load->model('addresses/location_m');
        ci()->load->model('addresses/location_customers_m');
        
        $array_condition = array();
        $array_condition['location.public_flag'] = 1;
        $array_condition['location.share_external_flag'] = 1;
        if($sent_daily_reminder_flag){
            $array_condition['location.sent_daily_reminder_flag'] = $sent_daily_reminder_flag;
        }
        $locationPublic = ci()->location_m->get_public_location($array_condition);
        $map_location_public = array();
        foreach ($locationPublic as $location) {
            $map_location_public[$location->id] = $location->id;
        }
        
        // Get owner enterprise locations
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        if (!empty($parent_customer_id)) {
            $owner_locations = ci()->location_m->get_my_enterprise_location($parent_customer_id);
            if (!empty($owner_locations) && count($owner_locations) > 0) {
                foreach ($owner_locations as $location) {
                    // Check duplicate
                    if (!array_key_exists($location->id, $map_location_public)) {
                        $locationPublic[] = $location;
                    }
                }
            }
        }
        
        // Don't show enterprise customer code
        /**
        $enterprise_locations = ci()->location_customers_m->get_all_enterprise_location();
        $map_enterprise_locations = array();
        foreach ($enterprise_locations as $item) {
            if (!array_key_exists($item->location_id, $map_enterprise_locations)) {
                $map_enterprise_locations[$item->location_id] = $item->customer_code;
            }
        }
        
        // Change location name
        foreach ($locationPublic as $location) {
            $item_name = $location->location_name;
            if (array_key_exists($location->id, $map_enterprise_locations)) {
                $item_name = $item_name.' Enterprise '.$map_enterprise_locations[$location->id];
            }
            $location->location_name = $item_name;
        }
        */ 
        return $locationPublic;
    }
    
    public static function getMyLocation($customer_id)
    {
        ci()->load->model('addresses/location_m');
        $locationPublic = ci()->location_m->get_all_location($customer_id);
        return $locationPublic;
    }

    public static function deleteLocationPricingById($id)
    {
        ci()->load->model('addresses/location_pricing_m');

        ci()->location_pricing_m->delete_by("location_id", $id);
    }

    public static function createLocationPricing($location_id, array $pricing_template_id)
    {
        ci()->load->model('addresses/location_pricing_m');

        $rows = array();
        foreach ($pricing_template_id as $value) {
            $row = array(
                'location_id' => $location_id,
                'pricing_template_id' => $value
            );
            array_push($rows, $row);
        }
        ci()->location_pricing_m->insert_many($rows);
    }

    public static function updateLocationByID($id, array $data)
    {
        ci()->load->model('addresses/location_m');

        ci()->location_m->update($id, $data);
    }

    /**
     * Delete location by id.
     * @param unknown $id
     */
    public static function deleteLocationById($id)
    {
        ci()->load->model('addresses/location_m');
        ci()->load->model('addresses/location_pricing_m');

        // delete location
        ci()->location_m->delete_by("id", $id);

        ci()->location_pricing_m->delete_by("location_id", $id);
    }

    public static function get_list_address_customer($customer_id)
    {
        ci()->load->model('addresses/customers_address_m');
        
        $list_address = ci()->customers_address_m->get_customer_address($customer_id);
        
        return $list_address;
        
    }

    public static function save_forward_address($customer_forward_id, $data){
        ci()->load->model('addresses/customers_forward_address_m');
        $customer_id = $data['customer_id'];
        $check = ci()->customers_forward_address_m->get_by_many(array(
            'id' => $customer_forward_id,
            'customer_id' => $customer_id
        ));

        if ($check) {
            ci()->customers_forward_address_m->update($customer_forward_id, $data);
        } else {
            $data['created_date'] = now(); 
            $customer_forward_id = ci()->customers_forward_address_m->insert($data);
        }
        
        return $customer_forward_id;
    }

    public  static function get_envelope_type_list($location_id = null)
    {
        
        ci()->load->model('addresses/location_envelope_types_m');
        if(!empty($location_id)){

            $all_list_type   = ci()->location_envelope_types_m->getAvailbleTypeByLocation($location_id);
            $list_type = array();
            $i=0;
            foreach ($all_list_type as $row) {
                
                $obj_type                = new stdClass;

                $obj_type->location_id   = $row->id;
                $obj_type->location_name = $row->location_name;
                $obj_type->ActualValue   = $row->ActualValue;
                $obj_type->LabelValue    = $row->LabelValue;
                $obj_type->Alias01       = $row->Alias01;
                $obj_type->Alias02       = $row->Alias02;
                $obj_type->Alias03       = $row->Alias03;
                $obj_type->Alias04       = $row->Alias04;
                $obj_type->Alias05       = $row->Alias05;

                $list_type[$i] = $obj_type;
                $i++;
            }
        }
        else {
            $list_type = Settings::get_list(APConstants::ENVELOPE_TYPE_CODE);

        }
        
        return $list_type;
    }

    /**
     * location pricing.
     */
    public static function location_pricing($pricing_type, $api_mobile = 0)
    {
        $response = array();
        if($api_mobile){

            $list_access_location = APUtils::mobileLoadListAccessLocation();    
        }
        else{

            $list_access_location = APUtils::loadListAccessLocation();
        }

        if (ci()->input->server('REQUEST_METHOD') == 'POST') {

            $location_id = ci()->input->get_post("location_id", 0);
            if($location_id == 0){
                $pricing_template_id = APConstants::DEfAULT_PRICING_MODEL_TEMPLATE;
            }else{
                $location = addresses_api::getLocationByID($location_id);
                $pricing_template_id = $pricing_type == 'Enterprise' ? $location->enterprise_pricing_template_id : $location->pricing_template_id;
            }

        } else {

            $location_id = 0;
            $pricing_template_id = APConstants::DEfAULT_PRICING_MODEL_TEMPLATE;
            
            if($api_mobile == 0){

                if (APContext::isAdminLocation() || (APContext::isWorkerAdmin()) ) {

                    $location_id = $list_access_location[0]->id;
                    $location = ci()->location_m->get_by('id', $location_id);
                    $pricing_template_id = $pricing_type == 'Enterprise' ? $location->enterprise_pricing_template_id : $location->pricing_template_id;
                }
            }
            else {

                if (MobileContext::isAdminLocation() || (MobileContext::isWorkerAdmin()) ) {

                    $location_id = $list_access_location[0]->id;
                    $location = ci()->location_m->get_by('id', $location_id);
                    $pricing_template_id = $pricing_type == 'Enterprise' ? $location->enterprise_pricing_template_id : $location->pricing_template_id;
                }
            }
        }

        $response['list_access_location'] = $list_access_location;
        $response['location_id'] = $location_id;

        // Gets all pricing templates
        $filter_condition = array('pricing_template.deleted_flag' => APConstants::OFF_FLAG);
        if (!empty($pricing_type)) {
            $filter_condition['pricing_type'] = $pricing_type;
        }
        $pricing_templates = ci()->pricing_template_m->get_many_by_many($filter_condition);
        if ($location_id == 0) {
            $templates = $pricing_templates;
        } else {
            $location_pricing_templates = ci()->location_pricing_m->get_many_by(array("location_id" => $location_id));
            $templates = array();
            foreach ($location_pricing_templates as $location_pricing_template) {
                foreach ($pricing_templates as $pricing_template) {
                    if ($location_pricing_template->pricing_template_id == $pricing_template->id) {
                        array_push($templates, $pricing_template);
                    }
                }
            }
        }

        $response['pricing_templates'] = $templates;
        $response['pricing_template_id'] = $pricing_template_id;

        // Get unit price of all account types
        $pricing_map = array();
        if ($pricing_template_id != null) {
            $pricings = ci()->pricing_m->get_many_by(array('pricing_template_id' => $pricing_template_id));

            foreach ($pricings as $price) {
                if (!array_key_exists($price->account_type, $pricing_map)) {
                    $pricing_map [$price->account_type] = array();
                }
                $pricing_map [$price->account_type] [$price->item_name] = $price;
            }
        }
        $response['pricing_map'] = $pricing_map;
       
        if(ci()->input->get_post("status") && ci()->input->get_post("status") === "active"){

            $status = ci()->input->get_post("status");
            $id = ci()->input->get_post("pricing_template_id");
            $name_pricing_template = ci()->pricing_template_m->get_many_by(array('id' => $id));
        }else{
            $status = "";
            $location_id = ci()->input->get_post("location_id", 0);
            if($location_id == 0){
                $name_pricing_template = "";
            }else {

                $objectLocation = ci()->location_m->get($location_id);
                $next_pricing_template_id = $objectLocation->next_pricing_template_id;
                $name_pricing_template = ci()->pricing_template_m->get_many_by(array('id' => $next_pricing_template_id));
            }
        }
        $response['name_pricing_template'] = $name_pricing_template;
        $response['status'] = $status;
        return $response;
    }


}