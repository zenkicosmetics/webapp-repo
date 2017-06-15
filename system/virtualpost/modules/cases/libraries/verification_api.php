<?php defined('BASEPATH') or exit('No direct script access allowed');

class verification_api
{
    /**
     * default contructor
     */
    function __construct() {
        // load library
        ci()->load->library(array(
            "cases/cases_api"
        ));
        
        ci()->load->model(array(
            'cases/cases_contract_m',
            "cases/cases_proof_business_m",
            "cases/cases_company_ems_m",
            "cases/case_phone_number_m",
            "cases/case_resource_m",
            "cases/case_usps_officer_m"
        ));
        
        ci()->lang->load('cases/cases');
    }
    
    /**
     * Gets list case verifications
     * @param type $list_cases
     */
    public static function get_list_cases_verification($list_cases){
        // declare case verification.
        $is_personal_identification = true;
        $is_company_verification = true;
        $is_company_shareholder_verification = true;
        $is_USPS_form_1583_verify = true;
        $is_general_cmra_verify = true;
        $is_company_ems_verify = true;
        $is_proof_address_verify = true;
        $is_tc_contract_verify = true;
        $is_california_mailbox_verify = true;
        
        $is_personal_identification_started = true;
        $is_company_verification_started = true;
        $is_company_shareholder_verification_started = true;
        $is_USPS_form_1583_verify_started = true;
        $is_general_crma_verify_started = true;
        $is_company_ems_verify_started = true;
        $is_proof_address_verify_started = true;
        $is_tc_contract_verify_started = true;
        $is_california_mailbox_verify_started = true;
        
        // phone verification
        $is_phone_personal_verify = true;
        $is_phone_company_verify = true;
        
        $is_phone_personal_verification_started = true;
        $is_phone_company_verification_started = true;
        
        // completed flag
        $is_completed_verify = true;
        $list_case_result = array();
        foreach ($list_cases as $case_id => $_cases) {
            $is_personal_identification = $_cases['is_personal_identification'];
            $is_company_verification = $_cases['is_company_verification'];
            $is_company_shareholder_verification = $_cases['is_company_shareholder_verification'];
            $is_USPS_form_1583_verify = $_cases['is_USPS_form_1583_verify'];
            $is_general_cmra_verify = $_cases['is_general_cmra_verify'];
            $is_company_ems_verify = $_cases['is_company_verification_ems_verify'];
            $is_proof_address_verify = $_cases['is_proof_address_verify'];
            $is_tc_contract_verify = $_cases['is_tc_contract_verify'];
            $is_california_mailbox_verify = $_cases['is_california_mailbox_verify'];
            
            // check phone verificaiton status
            $is_phone_personal_verify = isset($_cases['is_personal_phone_identification']) ? $_cases['is_personal_phone_identification'] : true;
            $is_phone_company_verify = isset($_cases['is_company_phone_verification']) ? $_cases['is_company_phone_verification'] : true;
            
            $is_completed_verify_group = ($is_personal_identification 
                    && $is_company_verification 
                    && $is_company_shareholder_verification
                    && $is_USPS_form_1583_verify 
                    && $is_general_cmra_verify
                    && $is_company_ems_verify
                    && $is_proof_address_verify
                    && $is_tc_contract_verify
                    && $is_california_mailbox_verify
                    && $is_phone_personal_verify
                    && $is_phone_company_verify);
            if ($is_completed_verify) {
                $is_completed_verify = $is_completed_verify_group;
            }

            // base taskname
            $base_taskname = '';

            // cases verification personal identity
            if (!$is_personal_identification) {
                $cases_verification_personal_identity = cases_api::getPersonalOrCompanyVerificationCase($case_id, 1);
                $is_personal_identification_started = !(empty($cases_verification_personal_identity) || empty($cases_verification_personal_identity->status));
            }

            // cases verification company identity
            if (!$is_company_verification) {
                $cases_verification_company_identity = cases_api::getPersonalOrCompanyVerificationCase($case_id, 2);
                $is_company_verification_started = !(empty($cases_verification_company_identity) || empty($cases_verification_company_identity->status));
            }

            // cases verification company hard identity
            if (!$is_company_shareholder_verification) {
                $cases_verification_company_hard = cases_api::getCompanyHardVerificationCase($case_id);
                $is_company_shareholder_verification_started = !(empty($cases_verification_company_hard) || empty($cases_verification_company_hard->status));
            }

            // cases verification special
            if (!$is_USPS_form_1583_verify) {
                $cases_verification_usps = cases_api::getUSPSFormVerificationCase($case_id);
                $is_USPS_form_1583_verify_started = !(empty($cases_verification_usps) || empty($cases_verification_usps->status));
            }

            // cases verification: general CRMA
            if (!$is_general_cmra_verify) {
                $cases_verification_usps = cases_api::getUSPSFormVerificationCase($case_id);
                $is_general_crma_verify_started = !(empty($cases_verification_usps) || empty($cases_verification_usps->status));
            }
            
            // cases verification: california mailbox
            if (!$is_california_mailbox_verify) {
                $cases_verification_usps = cases_api::getUSPSFormVerificationCase($case_id);
                $is_california_mailbox_verify_started = !(empty($cases_verification_usps) || empty($cases_verification_usps->status));
            }
            
            // case company EMS 
            if(!$is_company_ems_verify_started){
                $check_case = ci()->cases_company_ems_m->get_by('case_id', $case_id);
                $is_company_ems_verify_started = !(empty($check_case) || empty($check_case->status));
            }
            
            // case proof address
            if(!$is_proof_address_verify_started){
                $check_case = ci()->cases_proof_business_m->get_by('case_id', $case_id);
                $is_proof_address_verify_started = !(empty($check_case) || empty($check_case->status));
            }
            
            // case TC contract.
            if(!$is_tc_contract_verify_started){
                $check_case = ci()->cases_contract_m->get_by('case_id', $case_id);
                $is_tc_contract_verify_started = !(empty($check_case) || empty($check_case->status));
            }
            
            // case personal phone verification.
            if(!$is_phone_personal_verify){
                $check_case = ci()->case_phone_number_m->get_by_many(array(
                    'case_id' => $case_id,
                    "type" => 1
                ));
                $is_phone_personal_verification_started = !(empty($check_case) || empty($check_case->status));
            }
            
            // case company phone verification.
            if(!$is_phone_company_verify){
                $check_case = ci()->case_phone_number_m->get_by_many(array(
                    'case_id' => $case_id,
                    "type" => 2
                ));
                $is_phone_company_verification_started = !(empty($check_case) || empty($check_case->status));
            }
            
            // get base taskname
            $_cases["is_personal_identification_started"] = $is_personal_identification_started;
            if (!$is_personal_identification_started) {
                $base_taskname = 'verification_personal_identification';
            }
            $_cases["is_company_verification_started"] = $is_company_verification_started;
            if (!$is_company_verification_started) {
                $base_taskname = 'verification_company_identification_soft';
            }
            $_cases["is_company_shareholder_verification_started"] = $is_company_shareholder_verification_started;
            if (!$is_company_shareholder_verification_started) {
                $base_taskname = 'verification_company_identification_hard';
            }
            $_cases["is_USPS_form_1583_verify_started"] = $is_USPS_form_1583_verify_started;
            if (!$is_USPS_form_1583_verify_started) {
                $base_taskname = 'verification_special_form_PS1583';
            }
            $_cases["is_general_cmra_verify"] = $is_general_cmra_verify;
            $_cases["is_general_cmra_verify_started"] = $is_general_crma_verify_started;
            if (!$is_general_cmra_verify) {
                $base_taskname = 'verification_General_CMRA';
            }
            
            $_cases["is_california_mailbox_verify"] = $is_california_mailbox_verify;
            $_cases["is_california_mailbox_verify_started"] = $is_california_mailbox_verify_started;
            if (!$is_california_mailbox_verify) {
                $base_taskname = 'verification_california_mailbox';
            }
            
            // check company EMS
            $_cases["is_company_ems_verification_started"] = $is_company_ems_verify_started;
            if (!$is_company_ems_verify) {
                $base_taskname = 'company_verification_E_MS';
            }
            
            // check proof address
            $_cases["is_proof_address_verification_started"] = $is_proof_address_verify_started;
            if (!$is_proof_address_verify) {
                $base_taskname = 'proof_of_address_MS';
            }
            
            // check tc contract
            $_cases["is_tc_contract_verification_started"] = $is_tc_contract_verify_started;
            if (!$is_tc_contract_verify) {
                $base_taskname = 'TC_contract_MS';
            }

            // check phone personal verification
            $_cases["is_phone_personal_verification_started"] = $is_phone_personal_verification_started;
            if (!$is_phone_personal_verify) {
                $base_taskname = 'phone_number_for_personal';
            }
            
            // check phone company verification
            $_cases["is_phone_company_verification_started"] = $is_phone_company_verification_started;
            if (!$is_phone_company_verify) {
                $base_taskname = 'phone_number_company';
            }
            
            // case completed flag
            $_cases["is_completed_verify_group"] = $is_completed_verify_group;
            $_cases['base_taskname'] = $base_taskname;

            $list_case_result[$case_id] = $_cases;
        }
        
        // return result.
        return array(
            "is_completed_verify" => $is_completed_verify,
            "list_case_result" => $list_case_result
        );
    }
    
    /**
     * Update case phone number status
     * @param type $customer_id
     * @param type $case_id
     * @param type $type
     * @param type $status
     * @return boolean
     */
    public static function update_case_phone_status($customer_id, $case_id, $type, $status){
        ci()->load->model('cases/case_phone_number_m');
        $case_check = ci()->case_phone_number_m->get_by_many(array(
            "type" => $type,
            "case_id" => $case_id
        ));
        if(!empty($case_check)){
            ci()->case_phone_number_m->update_by_many(array(
                "id" => $case_check->id,
            ), array(
                "status" => $status,
                "updated_date" => now(),
                "update_by" => $customer_id,
                "type" => $type
            ));
        }else{
            ci()->case_phone_number_m->insert(array(
                "status" => $status,
                "case_id" => $case_id,
                "created_date" => now(),
                "update_by" => $customer_id,
                "type" => $type
            ));
        }
        
        return true;
    }
    
    /**
     * Gets list result case by customer.
     */
    public static function get_list_case_result_verification($customer_id){
        ci()->load->library(array(
            "cases/cases_api",
            "settings/settings_api",
            "addresses/addresses_api",
            "cases/CasesValidator",
        ));
        // get list cases trigger
        $list_case_result = array();
        
        $customer = APContext::getCustomerByID($customer_id);
        $activated_flags = CustomerProductSetting::get_activate_flags($customer_id);
        
        // if customer is deleted or not activated
        if($customer->status == APConstants::ON_FLAG 
                || ($customer->activated_flag == APConstants::OFF_FLAG && $activated_flags['postbox_name_flag'] == APConstants::OFF_FLAG)){
            return array();
        }
        
        // get list user id of enterprise customer
        $list_customer_id = CustomerUtils::getListCustomerIdOfEnterpriseCustomer($customer_id);
        if(!empty($list_customer_id)){
            foreach($list_customer_id as $user_id){
                CaseUtils::start_verification_case($user_id);
            }
        }
        
        // get case of customer logged in
        $list_cases = CaseUtils::start_verification_case($customer_id);

        // update list case result.
        $list_case_result[$customer_id] = verification_api::get_list_cases_verification($list_cases);

        if($customer->account_type == APConstants::ENTERPRISE_TYPE && empty($customer->parent_customer_id)){
            // Gets list customer id
            $customer_ids = CustomerUtils::getListCustomerIdOfEnterpriseCustomer(APContext::getParentCustomerCodeLoggedIn());
            
            foreach ($customer_ids as $cid){
                $list_cases = CaseUtils::start_verification_case($cid);
                $result = verification_api::get_list_cases_verification($list_cases);
                if(!empty($result['list_case_result'])){
                    $list_case_result[$cid] = $result;
                }
            }
        }
        
        return $list_case_result;
    }
    
    /**
     * upload file.
     * $type =1: personal identification | type =2: company soft verification.
     */
    public static function upload_company_soft_verification($case_id, $type, $input_file_client_name){
        ci()->load->model(array(
            "cases/cases_verification_personal_identity_m",
        ));
        
        ci()->load->library(array(
            "cases/cases_api",
            "files/files"
        ));
        
        // Gets check  case
        $cases_registration_document_check = ci()->cases_verification_personal_identity_m->get_by_many(array(
            'case_id' => $case_id,
            'type' => $type
        ));

        $running_number = '001';
        if (!empty($cases_registration_document_check)) {
            $check_filename = $cases_registration_document_check->verification_local_file_path;
            if ($input_file_client_name == 'driver_license_file') {
                $check_filename = $cases_registration_document_check->driver_license_document_local_file_path;
            }
            $running_number = CaseUtils::getRunningNumberBy($check_filename);
        }

        $server_filename = '';
        if ($input_file_client_name == 'personal_verification') {
            $server_filename = CaseUtils::getUploadFileNameBy('verification_personal_identification', '01', $running_number, $case_id);
        } else if ($input_file_client_name == 'business_registration_verification') {
            $server_filename = CaseUtils::getUploadFileNameBy('verification_company_identification_soft', '01', $running_number, $case_id);
        } else if ($input_file_client_name == 'driver_license_file') {
            $type = 1;
            $server_filename = CaseUtils::getUploadFileNameBy('verification_personal_identification', '02', $running_number, $case_id);
        }

        $upload_info = cases_api::upload_file_case_verification($case_id, $input_file_client_name, $server_filename);

        if (!$upload_info['status']) {
            return array(
                "status" => false,
                "message" => $upload_info['message'],
                "file_path" => ""
            );
        }

        // Update data to database
        $cases_registration_document_id = '';
        if (empty($cases_registration_document_check)) {
            $cases_registration_document_id = ci()->cases_verification_personal_identity_m->insert(array(
                'case_id' => $case_id,
                'status' => 0,
                'type' => $type,
                'created_date' => now()
            ));
        } else {
            $cases_registration_document_id = $cases_registration_document_check->id;
        }

        $update_data['updated_date'] = now();
        if ($input_file_client_name == 'driver_license_file') {
            $update_data['driver_license_document_local_file_path'] = $upload_info['local_file_path'];
            $update_data['driver_license_document_amazon_file_path'] = $upload_info['amazon_file_path'];
        } else {
            $update_data['verification_local_file_path'] = $upload_info['local_file_path'];
            $update_data['verification_amazon_file_path'] = $upload_info['amazon_file_path'];
        }

        ci()->cases_verification_personal_identity_m->update_by_many(array(
            'id' => $cases_registration_document_id,
            'type' => $type
        ), $update_data);

        return array(
            "status" => true,
            "message" => "",
            "file_path" => $upload_info['local_file_path']
        );
    }
    
    /**
     * create pdf term & condition.
     * @param type $customer
     * @return string
     */
    public static function create_term_condition_pdf($customer){
        $customer_id = $customer->customer_id;
        $html = settings_api::getTermAndCondition();

        // add information for sign
        $address = ci()->customers_address_m->get($customer_id);
        $name = $address->invoicing_address_name;
        $company = $address->invoicing_company;

        $html_contract = ci()->load->view('cases/verification/template_tc_contract', array(
            "name" => $name,
            "company" => $company,
            'term_and_condition' => $html
        ), true);
        $filename = Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'downloads/verification/'.$customer->customer_code . '_tc_contract.pdf';

        // do create pdf.
        if (!is_dir(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'downloads/verification/')) {
            mkdir(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'downloads/verification/');
            chmod(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'downloads/verification/', 0777);
        }

        // export pdf.
        cases_api::create_pdf_resource_by($html_contract, $filename);

        ci()->session->set_userdata('SPECIAL_FILE_PATH', $filename);
        return $filename;
    }
    
    /**
     * save company ems verification.
     */
    public static function save_company_ems($customer_id, $case_id, $description, $comment_for_registration_content, $comment_for_registration_date,
            $mail_receiver_name, $mail_receiver_ids, $officer_file_ids, $officer_names, $officer_rates){
        $case_check = ci()->cases_company_ems_m->get_by('case_id', $case_id);
        if($case_check){
            ci()->cases_company_ems_m->update_by_many(array(
                "id" => $case_check->id,
            ), array(
                "status" => 1,
                "description" => $description,
                "comment_for_registration_content" => $comment_for_registration_content,
                "comment_for_registration_date" => $comment_for_registration_date,
                "updated_date" => now(),
                "update_by" => $customer_id
            ));
        }else{
            ci()->cases_company_ems_m->insert(array(
                "status" => 1,
                "case_id" => $case_id,
                "created_date" => now(),
                "description" => $description,
                "comment_for_registration_content" => $comment_for_registration_content,
                "comment_for_registration_date" => $comment_for_registration_date,
                "update_by" => $customer_id
            ));
        }
        // update mail receiver
        if($mail_receiver_ids){
            $index = 0;
            foreach ($mail_receiver_ids as $file_id){
                if($file_id){
                    $check_mail_receiver = ci()->case_resource_m->get_by('id', $file_id);

                    if($check_mail_receiver){
                        ci()->case_resource_m->update_by_many(array(
                            "id" => $check_mail_receiver->id,
                            'case_id' => $case_id
                        ), array(
                            "name" => $mail_receiver_name[$index]
                        ));
                    }
                }
                $index ++;
            }
        }

        // update business license, mail receiver, officer.
        if($officer_file_ids){
            $index = 0;
            foreach ($officer_file_ids as $file_id){
                if($file_id){
                    $check_officer = ci()->case_usps_officer_m->get_by('id', $file_id);
                    $tmp_data = array(
                        "name" => $officer_names[$index],
                        "rate" => $officer_rates[$index],
                        "type" => 1, // owner
                    );
                    if($check_officer){
                        ci()->case_usps_officer_m->update_by_many(array(
                            "id" => $check_officer->id,
                            'case_id' => $case_id
                        ), $tmp_data);
                    }
                }
                $index ++;
            }
        }
    }
}
