<?php defined('BASEPATH') or exit('No direct script access allowed');

class mobile_cases_api  {
    public function __construct() {
        ci()->load->model(array(
            'cases/cases_m',
            "addresses/customers_address_m",
            'settings/countries_m',
            'mailbox/postbox_m',
            'addresses/location_m',
            "cases/cases_product_m",
            "cases/cases_milestone_m",
            "cases/cases_milestone_instance_m",
            "cases/cases_taskname_m",
            "cases/cases_taskname_instance_m",
            "cases/cases_verification_personal_identity_m",
            "cases/cases_verification_usps_m",
            "cases/cases_verification_company_hard_m",
            "partner/partner_m",
            "cases/case_usps_mail_receiver_m",
            "cases/case_usps_officer_m",
            "cases/case_usps_business_license_m",
            "cases/cases_contract_m",
            "cases/case_resource_m",
            "cases/cases_proof_business_m",
            "cases/cases_company_ems_m",
            'settings/terms_service_m',
            "cases/case_phone_number_m",
        ));
        
        ci()->load->library(array(
            "cases/cases_api",
            "settings/settings_api",
            "addresses/addresses_api",
            "cases/CasesValidator",
            'cases/verification_api',
            "files/files"
        ));

    }
    
    /**
     * Gets personal verification case.
     * 
     * @param type $case_exist
     */
    public static function start_personal_verification($case_exist, $type = 1){
        $customer_id = $case_exist->customer_id;
        $case_id = $case_exist->id;
        
        $result = array();
        // Gets passport file
        $cases_registration_document_check = ci()->cases_verification_personal_identity_m->get_by_many(array(
        	'case_id' => $case_id,
        	'type' => $type
        ));
        
        $is_invoicing_address_verification = true;
        if (strpos($case_exist->case_identifier, 'VRAD') == false) {
            $is_invoicing_address_verification = false;
        }
        
        $customer_addresses = ci()->customers_address_m->get_by_many(array(
            'customer_id' => $customer_id
        ));
        $customer_addresses->invoicing_country_name = '';
        if (!empty($customer_addresses) && !empty($customer_addresses->invoicing_country)) {
            $invoicing_country_obj = ci()->countries_m->get_by_many(array(
                "id" => $customer_addresses->invoicing_country
            ));
            if (!empty($invoicing_country_obj)) {
                $customer_addresses->invoicing_country_name = $invoicing_country_obj->country_name;
            }
        }
        

        $postbox = '';
        if (!$is_invoicing_address_verification) {
            $postbox = ci()->postbox_m->get_by('postbox_id', $case_exist->postbox_id);
        }
        
        $result['case_document'] = $cases_registration_document_check;
        $result['customer_addresses'] = $customer_addresses;
        $result['postbox'] = $postbox;
        $result['is_invoicing_address_verification'] = $is_invoicing_address_verification;
        $result['case_id'] = $case_id;
        $result['base_taskname'] = 'verification_personal_identification';
        if($type == 2){
            $result['base_taskname'] = 'verification_company_identification_soft';
        }
        
        return $result;
    }
    
    /**
     * save company soft verifcation and personal verifiacation
     */
    public static function save_personal_identification_verification($case_id, $base_taskname, $type, $comment_for_registration_content=''){
        // Gets passport file
        $cases_registration_document_check = ci()->cases_verification_personal_identity_m->get_by_many(array(
        	'case_id' => $case_id,
        	'type' => $type
        ));

        // validation
        if($type == 1){
            // validate for personal identification case
            if (empty($cases_registration_document_check)
                || empty($cases_registration_document_check->verification_local_file_path)
                || empty($cases_registration_document_check->driver_license_document_local_file_path)
            ) {
                return array(
                    "status" => false,
                    "message" => 'Please upload personal identification document.'
                );
            }
        } else if($type == 2){
            // validate company soft verification.
            if (empty($cases_registration_document_check)
                || empty($cases_registration_document_check->verification_local_file_path)
            ) {
                return array(
                    "status" => false,
                    "message" => 'Please upload company identification document.'
                );
            }
        }
        $cases_registration_document_id = $cases_registration_document_check->id;

        $update_data['updated_date'] = now();
        $update_data['status'] = '1';
        $update_data['comment_for_registration_content'] = $comment_for_registration_content;
        if(!empty($update_data['comment_for_registration_content'])){
            $update_data['comment_for_registration_date'] = now();
        }
        
        // update status.
        ci()->cases_verification_personal_identity_m->update_by_many(array(
            'id' => $cases_registration_document_id,
            'type' => $type
        ), $update_data);

        // update status case.
        mobile_cases_api::update_status_cases_taskname_instance($case_id, $base_taskname);

        return array(
            "status" => true,
            "message" => ''
        );
    }
    
    /**
     * upload file for verificationc case.
     */
    public static function upload_verification($case_id, $base_taskname, $input_file_client_name){
        $result = "";
        switch($base_taskname){
            case "verification_personal_identification":
                $result = verification_api::upload_company_soft_verification($case_id, 1, $input_file_client_name);
                break;
            case "verification_company_identification_soft":
                $result = verification_api::upload_company_soft_verification($case_id, 2, $input_file_client_name);
                break;
            case "TC_contract_MS":
            case "proof_of_address_MS":
                $file_id = ci()->input->get_post("file_id");

                $customer_id = APContext::getCustomerByCase($case_id);
                $customer = APContext::getCustomerByID($customer_id);

                $result = cases_api::uploadResourceDocument($customer, $case_id, $base_taskname, $input_file_client_name, '', $file_id);
                break;
            case "company_verification_E_MS":
                $file_id = ci()->input->get_post("file_id");

                $customer_id = APContext::getCustomerByCase($case_id);
                $customer = APContext::getCustomerByID($customer_id);

                $result = cases_api::uploadResourceDocument($customer, $case_id, $base_taskname, $input_file_client_name, '', $file_id);
                break;
        }

        return $result;
        
    }
    
    /**
     * 
     * @param type $case_id
     * @param type $base_taskname
     * @param type $local_file_name
     * @return string
     */
    public static function get_case_document($case_id, $base_taskname, $local_file_name = '', $file_id=''){
        $local_file_path = '';
        
        switch ($base_taskname){
            case "verification_personal_identification":
                $case_check = ci()->cases_verification_personal_identity_m->get_by_many(array(
                    'case_id' => $case_id,
                    'type' => 1
                ));
                if(!empty($case_check)){
                    if($local_file_name == 'driver_license_file'){
                        $local_file_path = $case_check->driver_license_document_local_file_path;
                    }else{
                        $local_file_path = $case_check->verification_local_file_path;
                    }
                }
                break;
            case "verification_company_identification_soft":
                // Gets passport file
                $case_check = ci()->cases_verification_personal_identity_m->get_by_many(array(
                    'case_id' => $case_id,
                    'type' => 2
                ));
                if(!empty($case_check)){
                    $local_file_path = $case_check->verification_local_file_path;
                }
                break;
            case "TC_contract_MS":
            case "proof_of_address_MS":
                $case_resource = ci()->case_resource_m->get($file_id);

                if(!empty($case_resource)){
                    $local_file_path = $case_resource->local_file_path;
                }
                break;
        }
        
        return $local_file_path;
    }
    
    /**
     * start term & condition case.
     */
    public static function start_term_and_condition_case($case_id, $base_taskname){
        $case_check = ci()->cases_contract_m->get_contract_by($case_id);
        $case_resource = ci()->case_resource_m->get_by_many(array(
            "case_id" => $case_id,
            "base_taskname" => $base_taskname
        ));
        
        // get term and condition
        $terms_and_conditions = settings_api::getTermAndCondition();
        
        $result = array(
            "term_condition" => $terms_and_conditions,
            "case_document" => $case_resource,
            "case" => $case_check,
            "case_id" => $case_id,
            "base_taskname" => $base_taskname
        );
        
        return $result;
    }
    
    /**
     * save term & condition case.
     */
    public static function save_term_and_condition_case($case_id, $base_taskname, $customer_id){
        $case_check = ci()->cases_contract_m->get_contract_by($case_id);
        if($case_check){
            ci()->cases_contract_m->update_by_many(array(
                "id" => $case_check->id,
            ), array(
                "status" => 1,
                "update_date" => now(),
                "update_by" => $customer_id
            ));
        }else{
            ci()->cases_contract_m->insert(array(
                "status" => 1,
                "case_id" => $case_id,
                "created_date" => now(),
                "update_by" => $customer_id
            ));
        }

        // update status case.
        mobile_cases_api::update_status_cases_taskname_instance($case_id, $base_taskname);
        return true;
    }
    
    /**
     * start term & condition case.
     */
    public static function start_proof_of_address_MS($case_id, $base_taskname){
        $case_check = ci()->cases_proof_business_m->get_by('case_id', $case_id);
        $case_resource = ci()->case_resource_m->get_by_many(array(
            "case_id" => $case_id,
            "base_taskname" => $base_taskname
        ));
        
        // get customer_id
        $customer_id = APContext::getCustomerByCase($case_id);
        
        // get customer address
        $customer_addresses = addresses_api::getCustomerAddress($customer_id);
        
        $result = array(
            "customer_addresses" => $customer_addresses,
            "case_document" => $case_resource,
            "case" => $case_check,
            "case_id" => $case_id,
            "base_taskname" => $base_taskname
        );
        
        return $result;
    }
    
    public static function save_proof_of_address_MS($case_id, $base_taskname, $customer_id, $comment_for_registration_content){
        $case_check = ci()->cases_proof_business_m->get_by('case_id', $case_id);
        
        $comment_for_registration_date = null;
        if(!empty($comment_for_registration_content)){
            $comment_for_registration_date = now();
        }

        if($case_check){
            ci()->cases_proof_business_m->update_by_many(array(
                "id" => $case_check->id,
            ), array(
                "status" => 1,
                "updated_date" => now(),
                "comment_for_registration_content" => $comment_for_registration_content,
                "comment_for_registration_date" => $comment_for_registration_date,
                "update_by" => $customer_id
            ));
        }else{
            ci()->cases_proof_business_m->insert(array(
                "status" => 1,
                "case_id" => $case_id,
                "created_date" => now(),
                "update_by" => $customer_id,
                "comment_for_registration_content" => $comment_for_registration_content,
                "comment_for_registration_date" => $comment_for_registration_date,
            ));
        }

        // update status case.
        mobile_cases_api::update_status_cases_taskname_instance($case_id, $base_taskname);
    }
    
    public static function start_company_verification_E_MS($case_id, $base_taskname, $postbox_id){
        /**
         * seq_number:
         * 01: business license
         * 02: officers of company
         */
        $case_check = ci()->cases_company_ems_m->get_by('case_id', $case_id);
        $case_resource = ci()->case_resource_m->get_by_many(array(
            "case_id" => $case_id,
            "base_taskname" => $base_taskname,
            "seq_number" => "01"
        ));
        $mailReceivers = ci()->case_resource_m->get_many_by_many(array(
            "case_id" => $case_id,
            "base_taskname" => $base_taskname,
            "seq_number" => "02"
        ));
        
        // Gets officer owner.
        $officers = ci()->case_usps_officer_m->get_many_by_many(array(
            "case_id" => $case_id
        ));
        
        $postbox = $this->postbox_m->get_by('postbox_id', $postbox_id);
         
        // get customer address
        $result = array(
            "case_document" => $case_resource,
            "case" => $case_check,
            "mailReceivers" => $mailReceivers,
            "officers" => $officers,
            "case_id" => $case_id,
            "base_taskname" => $base_taskname,
            "postbox" => $postbox
        );
        
        return $result;
    }
    
    public static function validateCompanyVerification_E_MS($case_id, $base_taskname, $description){
        $case_resource = $this->case_resource_m->get_by_many(array(
            "case_id" => $case_id,
            "base_taskname" => $base_taskname,
            "seq_number" => "01"
        ));
        
        $error_message = "";
        if(trim($description) == ""){
            $error_message .= "Please input the description of your business.\n";
        }else if(strlen($description) < 50){
            $error_message .= "The description field must be at least 50 characters.\n";
        }

        if(empty($case_resource)){
            $error_message .= "Please upload your business document license.\n";
        }

        // update officer
        if(!CasesValidator::_validate_officer_owner(true)){
            $error_message .= "Please correct owner information of your business.";
        }

        return $error_message;
    }
    
    public static function save_company_verification_E_MS($case_id, $base_taskname, $customer_id, $description, $comment_for_registration_content, 
                $comment_for_registration_date, $mail_receiver_name, $mail_receiver_ids, $officer_file_ids, $officer_names, $officer_rates){
        
        // save ems verification
        verification_api::save_company_ems($customer_id, $case_id, $description, $comment_for_registration_content, 
                $comment_for_registration_date, $mail_receiver_name, $mail_receiver_ids, $officer_file_ids, $officer_names, $officer_rates);
        
        // update status case.
        mobile_cases_api::update_status_cases_taskname_instance($case_id, $base_taskname);
    }    
    
    
    
    
    ///=========================== update status task name =====================================
    private static function update_status_cases_taskname_instance($case_id, $base_taskname){
        ci()->cases_taskname_instance_m->update_by_many(array(
            'case_id' => $case_id,
            'base_task_name' => $base_taskname
        ), array(
            'status' => '1',
            'updated_date' => now()
        ));
    }
}