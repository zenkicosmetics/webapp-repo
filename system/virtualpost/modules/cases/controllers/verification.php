<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Verification extends CaseSystem_Controller
{

    /**
     * Loads the gazillion of stuff, in Flash Gordon speed.
     *
     * @todo Document properly please.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model(array(
            'cases/cases_m',
            "addresses/customers_address_m",
            'settings/countries_m',
            'mailbox/postbox_m',
            'addresses/location_m',
            "cases_product_m",
            "cases_milestone_m",
            "cases_milestone_instance_m",
            "cases_taskname_m",
            "cases_taskname_instance_m",
            "cases_verification_personal_identity_m",
            "cases_verification_usps_m",
            "cases_verification_company_hard_m",
            "partner/partner_m",
            "case_usps_mail_receiver_m",
            "case_usps_officer_m",
            "case_usps_business_license_m",
            "cases_contract_m",
            "case_resource_m",
            "cases_proof_business_m",
            "cases_company_ems_m",
            'settings/terms_service_m',
            "cases/case_phone_number_m",
        ));

        $this->load->library(array(
            "cases_api",
            "settings/settings_api",
            "addresses/addresses_api",
            "cases/CasesValidator",
            'verification_api'
        ));
        
        $this->lang->load('cases');
        $this->load->library('form_validation');
        $this->load->library('S3');
    }

    /**
     * index
     */
    public function index()
    {
        $this->load->library("cases/verification_api");

        // get list cases trigger
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $list_case_result = verification_api::get_list_case_result_verification($customer_id);

        $this->template->set("is_completed_verify", $list_case_result[$customer_id]['is_completed_verify']);
        $this->template->set("list_case_result", $list_case_result);

        $this->template->build('verification/index');
    }

    /**
     * Personal identification init screen.
     */
    public function verification_personal_identification()
    {
        $case_id = $this->input->get_post("case_id", '');
        // Check exist this case
        $case_exist = $this->cases_m->get_many($case_id);
        if (empty($case_exist)) {
            redirect('cases/verification');
        }
        
        $customer_id = APContext::getCustomerByCase($case_id);
        
        // Gets passport file
        $cases_registration_document_check = $this->cases_verification_personal_identity_m->get_by_many(array(
        	'case_id' => $case_id,
        	'type' => '1'
        ));
        
        // If this is post method
        if ($_POST) {
            $response['code'] = 0;
            $this->load->library('form_validation');
            $this->form_validation->set_rules(CasesValidator::$person_verify_rules);
            $error_messages = '';
            if ($this->form_validation->run() == FALSE) {
            	$error_messages = $this->form_validation->error_array();
            }
			
            $cases_registration_document_id = '';
            if (empty($cases_registration_document_check)
                || empty($cases_registration_document_check->verification_local_file_path)
                || empty($cases_registration_document_check->driver_license_document_local_file_path)
            ) {
            	// valid input
            	if (!empty($error_messages)) {
            		$response['code'] = 1;
            		$response['message'] =$error_messages ;
                    $message = 'Please upload personal identification document.';
            		$this->error_output($message, $response );
            		return;
            	}
            } else {
                $cases_registration_document_id = $cases_registration_document_check->id;
            }

            if(!empty($cases_registration_document_check->comment_content) && ($_POST['check_resubmit'] == '1' ) ){

                $check_change_data = $this->check_change_data_verification_personal_identification($cases_registration_document_check);

                if(! $check_change_data){
                    $response['code'] = 0;
                    $response['message'] = language('case_controller_verification_ResubmitVerificationWithoutChangeDataMess');
                    $this->error_output( validation_errors(),$response );
                    return;
                }

            }

            $update_data['updated_date'] = now();
            $update_data['status'] = '1';
            $update_data['comment_for_registration_content'] = $this->input->get_post('comment_for_registration_content');
            if(isset($update_data['comment_for_registration_content'])){
            	$update_data['comment_for_registration_date'] = now();
            }

            $this->cases_verification_personal_identity_m->update_by_many(array(
                'id' => $cases_registration_document_id,
                'type' => '1'
            ), $update_data);
            
            $this->update_status_cases_taskname_instance($case_id, 'verification_personal_identification');
            
            //#1328 add message after verification is submited by customer 
            $this->success_output(language('case_controller_verification_ThanksForSubmitVerificationMess'));
            return;
        }

        $is_invoicing_address_verification = true;
        $customer_addresses = null;
        if (strpos($case_exist[0]->case_identifier, 'VRAD') == false) {
            $is_invoicing_address_verification = false;
        } else{
            $customer_addresses = $this->customers_address_m->get_by_many(array(
                'customer_id' => $customer_id
            ));
            $customer_addresses->invoicing_country_name = '';
            if (!empty($customer_addresses) && !empty($customer_addresses->invoicing_country)) {
                $invoicing_country_obj = $this->countries_m->get_by_many(array(
                    "id" => $customer_addresses->invoicing_country
                ));
                if (!empty($invoicing_country_obj)) {
                    $customer_addresses->invoicing_country_name = $invoicing_country_obj->country_name;
                }
            }
        }

        $postbox = '';
        if (!$is_invoicing_address_verification) {
            $postbox = $this->postbox_m->get_by('postbox_id', $case_exist[0]->postbox_id);
        }

        $this->template->set('postbox', $postbox);
        $this->template->set('is_invoicing_address_verification', $is_invoicing_address_verification);
        $this->template->set("cases_verification", $cases_registration_document_check);
        $this->template->set("customer_addresses", $customer_addresses);
        $this->template->set("case_id", $case_id);
        $this->template->build('verification/personal_verify');
    }
    
    public function check_change_data_verification_personal_identification($cases_registration_document_check){

        $check_driver_license_document_local_file_path = false;
        $verification_local_file_path = false;

        if($cases_registration_document_check->updated_date <= $cases_registration_document_check->comment_date){
            return false;
        }

        $case_id = $cases_registration_document_check->case_id;
        if ( !empty($cases_registration_document_check->driver_license_document_local_file_path) ) {
            
            if($_POST['driver_license_file_change'] == "1"){

                $current_file = $cases_registration_document_check->driver_license_document_local_file_path;
                $current_file_info = pathinfo($current_file);
                $running_number = CaseUtils::getBeforeRunningNumberBy($current_file);

                $server_filename = CaseUtils::getUploadFileNameBy('verification_personal_identification', '02', $running_number, $case_id);

                $previous_file = $current_file_info['dirname']."/".$server_filename.".".$current_file_info['extension'];

                if(!file_exists($previous_file)){
                    
                    $check_driver_license_document_local_file_path = true;

                } else if( file_exists($previous_file) && (filesize($previous_file) != filesize($current_file)) ){
                    
                    $check_driver_license_document_local_file_path = true;
                }
            }
        } 

        if( !empty($cases_registration_document_check->verification_local_file_path))
        {  
            if($_POST['passport_verification_change'] == "1"){

                $current_file = $cases_registration_document_check->verification_local_file_path;
                $current_file_info = pathinfo($current_file);
                $running_number  = CaseUtils::getBeforeRunningNumberBy($current_file);
                $server_filename = CaseUtils::getUploadFileNameBy('verification_personal_identification', '01', $running_number, $case_id);

                $previous_file = $current_file_info['dirname']."/".$server_filename.".".$current_file_info['extension'];
                
                if(!file_exists($previous_file)){
                    
                   $verification_local_file_path = true;

                } else if( file_exists($previous_file) && (filesize($previous_file) != filesize($current_file)) ){
                    $verification_local_file_path = true;
                }
            }
        }

        if( ! ( $check_driver_license_document_local_file_path || $verification_local_file_path ) ){
            return false;
        }

        return true; 
    }

    /**
     * Company identification init screen.
     */
    public function verification_company_identification_soft()
    {
        $case_id = $this->input->get_post("case_id", '');
        // Check exist this case
        $case_exist = $this->cases_m->get_many($case_id);
        if (empty($case_exist)) {
            redirect('cases/verification');
        }
        
        $customer_id = APContext::getCustomerByCase($case_id);
        $customer_addresses = $this->customers_address_m->get_by_many(array(
            'customer_id' => $customer_id
        ));
        $customer_addresses->invoicing_country_name = '';
        if (!empty($customer_addresses) && !empty($customer_addresses->invoicing_country)) {
            $invoicing_country_obj = $this->countries_m->get_by_many(array(
                "id" => $customer_addresses->invoicing_country
            ));
            if (!empty($invoicing_country_obj)) {
                $customer_addresses->invoicing_country_name = $invoicing_country_obj->country_name;
            }
        }
        
		$cases_registration_document_check = $this->cases_verification_personal_identity_m->get_by_many(array(
			'case_id' => $case_id,
			'type' => '2'
		));

        // If this is post method
        if ($_POST) {
        	$this->load->library('form_validation');
        	$this->form_validation->set_rules(CasesValidator::$company_soft_verify_rules);
        	$error_messages = '';
        	if ($this->form_validation->run() == FALSE) {
        		$error_messages = $this->form_validation->error_array();
        	}
        	
        	$cases_registration_document_id = '';
        	if (empty($cases_registration_document_check)) {
        		if (!empty($error_messages)) {
        			$response['code'] = 1;
        			$response['message'] = $error_messages;
        			$this->error_output(validation_errors(), $response );
        			return;
        		}
        	} else {
        		$cases_registration_document_id = $cases_registration_document_check->id;
        	}
        	
            if(!empty($cases_registration_document_check->comment_content) && ($_POST['check_resubmit'] == '1' ) ){
                $check_change_data = $this->check_change_data_verification_company_identification_soft($cases_registration_document_check);
                if(! $check_change_data){
                	$response['code'] = 0;
                	$response['message'] = language('case_controller_verification_ResubmitVerificationWithoutChangeDataMess');
                	$this->error_output( validation_errors(), $response );
                	return;
                }
            }

            $update_data['updated_date'] = now();
            $update_data['status'] = '1';
            $update_data['comment_for_registration_content'] = $this->input->get_post('comment_for_registration_content');
            if(isset($update_data['comment_for_registration_content'])){
            	$update_data['comment_for_registration_date'] = now();
            }

            $this->cases_verification_personal_identity_m->update_by_many(array(
                'id' => $cases_registration_document_id,
                'type' => '2'
            ), $update_data);

            $this->update_status_cases_taskname_instance($case_id, 'verification_company_identification_soft');
            
            //#1328 add message after verification is submited by customer 
            $this->success_output(language('case_controller_verification_ThanksForSubmitVerificationMess'));
            
            return;
        }

        $is_invoicing_address_verification = true;
        if (strpos($case_exist[0]->case_identifier, 'VRAD') == false) {
            $is_invoicing_address_verification = false;
        }
        $this->template->set('is_invoicing_address_verification', $is_invoicing_address_verification);
        $this->template->set("cases_verification", $cases_registration_document_check);
        $this->template->set("customer_addresses", $customer_addresses);
        $this->template->set("case_id", $case_id);
        $this->template->build('verification/company_soft');
    }

    public function check_change_data_verification_company_identification_soft($cases_registration_document_check){
        $check_verification_local_file_path = false;
    
        if($cases_registration_document_check->updated_date <= $cases_registration_document_check->comment_date){
            return false;
        }

        if ( !empty($cases_registration_document_check->verification_local_file_path) ) {
           
            $current_file = $cases_registration_document_check->verification_local_file_path;
            $current_file_info = pathinfo($current_file);
            $running_number = CaseUtils::getBeforeRunningNumberBy($current_file);
            $server_filename = CaseUtils::getUploadFileNameBy('verification_company_identification_soft', '01', $running_number, $case_id);
            $previous_file = $current_file_info['dirname']."/".$server_filename.".".$current_file_info['extension'];
            
            if( !file_exists($previous_file) ){

                $check_verification_local_file_path = true;

            } else if( file_exists($previous_file) && (filesize($previous_file) != filesize($current_file)) ){
                
                $check_verification_local_file_path = true;
            }

        } 

        if( ! ( $check_verification_local_file_path ) ){
            return false;
        }

        return true; 
    }

    /**
     * Company identification init screen.
     */
    public function verification_company_identification_hard()
    {
        $case_id = $this->input->get_post("case_id", '');

        // Check exist this case
        $case_exist = $this->cases_m->get_many($case_id);

        if (empty($case_exist)) {
            redirect('cases/verification');
        }
        
        $customer_id = APContext::getCustomerByCase($case_id);
        $customer_addresses = $this->customers_address_m->get_by_many(array(
            'customer_id' => $customer_id
        ));
        $customer_addresses->invoicing_country_name = '';
        if (!empty($customer_addresses) && !empty($customer_addresses->invoicing_country)) {
            $invoicing_country_obj = $this->countries_m->get_by_many(array(
                "id" => $customer_addresses->invoicing_country
            ));
            if (!empty($invoicing_country_obj)) {
                $customer_addresses->invoicing_country_name = $invoicing_country_obj->country_name;
            }
        }
        
		$cases_verification_company_hard_check = $this->cases_verification_company_hard_m->get_by_many(array(
			'case_id' => $case_id
		));
		
        // If this is post method
        if ($_POST) {
			$confirm_check_box_value = $this->input->get_post('confirm_check_box');
			
            $response['code'] = 0;
            
            $this->load->library('form_validation');
            $this->form_validation->set_rules(CasesValidator::$company_hard_verify_rules);
            $error_messages = '';
            if ($this->form_validation->run() == FALSE) {
            	$error_messages = $this->form_validation->error_array();
            }
            
            // Check resubmit
            if(!empty($cases_verification_company_hard_check->comment_content) && ($_POST['check_resubmit'] == '1') ){
                 // check shareholders when resubmit
                $shareholders_input = $this->input->get_post('shareholders');
                $shareholders_input[1]['file'] = $cases_verification_company_hard_check->shareholders_local_file_path_01;
                $shareholders_input[2]['file'] = $cases_verification_company_hard_check->shareholders_local_file_path_02;
                $shareholders_input[3]['file'] = $cases_verification_company_hard_check->shareholders_local_file_path_03;
                $shareholders_input[4]['file'] = $cases_verification_company_hard_check->shareholders_local_file_path_04;

                $rule = array();
                $i = 0;
                for ($index = 1; $index <= 4; $index++) {
                    if (!empty($shareholders_input[$index]['rate']) && ($shareholders_input[$index]['rate'] < 25 || 100 < $shareholders_input[$index]['rate'])) {
                        $rule[$index + $i - 1] = CasesValidator::$sub_company_hard_verify_rules_01[$index + $i - 1];
                        $rule[$index + $i] = CasesValidator::$sub_company_hard_verify_rules_01[$index + $i];
                        $this->form_validation->set_rules($rule);
                        if ($this->form_validation->run() == FALSE) {
                            $error_messages = $this->form_validation->error_array();
                        }
                    }

                    if ((!empty($shareholders_input[$index]['name']) || !empty($shareholders_input[$index]['rate']) || !empty($shareholders_input[$index]['file']))
                        && (empty($shareholders_input[$index]['file']) || empty($shareholders_input[$index]['rate']))
                    ) {
                        $rule[$index + $i - 1] = CasesValidator::$sub_company_hard_verify_rules_01[$index + $i - 1];
                        $rule[$index + $i] = CasesValidator::$sub_company_hard_verify_rules_01[$index + $i];
                        $this->form_validation->set_rules($rule);
                        if ($this->form_validation->run() == FALSE) {
                            $error_messages = $this->form_validation->error_array();
                        }
                    }

                    if (empty($shareholders_input[$index]['name']) && (!empty($shareholders_input[$index]['file']) || !empty($shareholders_input[$index]['rate']))) {

                        $rule[$index - 1] = CasesValidator::$sub_company_hard_verify_rules_02[$index - 1];
                        $this->form_validation->set_rules($rule);
                        if ($this->form_validation->run() == FALSE) {
                            $error_messages = $this->form_validation->error_array();
                        }
                    }
                    $i++;
                }
                
                 // Output error shareholders when resubmit
                if (!empty($error_messages)) {
                    $response['code'] = 0;
                    $response['code_status'] = 0;
                    $response['message'] = $error_messages;
                    $this->error_output(validation_errors(), $response);
                    return;
                }
                
                // Check change data
                $check_change_data = $this->check_change_data_verification_company_identification_hard($cases_verification_company_hard_check);
                if(! $check_change_data){
                    $response['code'] = 0;
                    $response['message'] = language('case_controller_verification_ResubmitVerificationWithoutChangeDataMess');
                    $this->error_output( validation_errors(), $response );
                    return;
                }
                
            }

            $cases_verification_company_hard_id = '';
            if (empty($cases_verification_company_hard_check)) {
            	// valid input
            	if (!empty($error_messages)) {
            		$response['code'] = 1;
            		$response['message'] = $error_messages;
            		$this->error_output(validation_errors(), $response );
            		return;
            	}
            } else {
                $cases_verification_company_hard_id = $cases_verification_company_hard_check->id;
            }            
            
            $shareholders_file_name_txt_04 = $this->input->get_post("shareholders_file_name_txt_04"); 
            
            // check shareholders
            $shareholders_input = $this->input->get_post('shareholders');
            
            $shareholders_input[1]['file'] = $cases_verification_company_hard_check->shareholders_local_file_path_01;
            $shareholders_input[2]['file'] = $cases_verification_company_hard_check->shareholders_local_file_path_02;
            $shareholders_input[3]['file'] = $cases_verification_company_hard_check->shareholders_local_file_path_03;
            $shareholders_input[4]['file'] = $cases_verification_company_hard_check->shareholders_local_file_path_04;

            $rule = array();
            $i = 0;
            for ($index = 1; $index <= 4; $index++) {
                if (!empty($shareholders_input[$index]['rate']) && ($shareholders_input[$index]['rate'] < 25 || 100 < $shareholders_input[$index]['rate'])) {
                	$rule[$index + $i - 1] = CasesValidator::$sub_company_hard_verify_rules_01[$index + $i - 1];
                	$rule[$index + $i] = CasesValidator::$sub_company_hard_verify_rules_01[$index + $i];
                	$this->form_validation->set_rules($rule);
                	if ($this->form_validation->run() == FALSE) {
                		$error_messages = $this->form_validation->error_array();
                	}
                }

                if ((!empty($shareholders_input[$index]['name']) || !empty($shareholders_input[$index]['rate']) || !empty($shareholders_input[$index]['file']))
                    && (empty($shareholders_input[$index]['file']) || empty($shareholders_input[$index]['rate']))) {
                	$rule[$index + $i - 1] = CasesValidator::$sub_company_hard_verify_rules_01[$index + $i - 1];
                	$rule[$index + $i] = CasesValidator::$sub_company_hard_verify_rules_01[$index + $i];
                	$this->form_validation->set_rules($rule);
                	if ($this->form_validation->run() == FALSE) {
                		$error_messages = $this->form_validation->error_array();
                	}
                }
                
                if (empty($shareholders_input[$index]['name']) && (!empty($shareholders_input[$index]['file']) || !empty($shareholders_input[$index]['rate']))) {
                    
                    $rule[$index - 1] = CasesValidator::$sub_company_hard_verify_rules_02[$index - 1];
                	$this->form_validation->set_rules($rule);
                	if ($this->form_validation->run() == FALSE) {
                		$error_messages = $this->form_validation->error_array();
                	}
                }
                $i++;
            }
            
            // Output error shareholders 
            if (!empty($error_messages)) {
                $response['code'] = 1;
                $response['message'] = $error_messages;
                $this->error_output(validation_errors(), $response);
                return;
            }
            
            $update_data['shareholders_name_01'] = $shareholders_input[1]['name'];
            $update_data['shareholders_rate_01'] = $shareholders_input[1]['rate'];
            if (empty ($shareholders_input[1]['name']) && empty($shareholders_input[1]['rate'])) {
                $update_data['shareholders_local_file_path_01'] = '';
            }
            $update_data['shareholders_name_02'] = $shareholders_input[2]['name'];
            $update_data['shareholders_rate_02'] = $shareholders_input[2]['rate'];
            if (empty ($shareholders_input[2]['name']) && empty($shareholders_input[2]['rate'])) {
                $update_data['shareholders_local_file_path_02'] = '';
            }
            $update_data['shareholders_name_03'] = $shareholders_input[3]['name'];
            $update_data['shareholders_rate_03'] = $shareholders_input[3]['rate'];
            if (empty ($shareholders_input[3]['name']) && empty($shareholders_input[3]['rate'])) {
                $update_data['shareholders_local_file_path_03'] = '';
            }
            $update_data['shareholders_name_04'] = $shareholders_input[4]['name'];
            $update_data['shareholders_rate_04'] = $shareholders_input[4]['rate'];
            if (empty ($shareholders_input[4]['name']) && empty($shareholders_input[4]['rate'])) {
                $update_data['shareholders_local_file_path_04'] = '';
            }

            $update_data['updated_date'] = now();
            $update_data['status'] = '1';
            $update_data['comment_for_registration_content'] = $this->input->get_post('comment_for_registration_content');
            if(isset($update_data['comment_for_registration_content'])){
            	$update_data['comment_for_registration_date'] = now();
            }
            // check box
            if($confirm_check_box_value == '1'){
            	$update_data['no_shareholder_flag'] = 1;
            }else{
            	$update_data['no_shareholder_flag'] = 0;
            }

            $this->cases_verification_company_hard_m->update_by_many(array(
                'id' => $cases_verification_company_hard_id
            ), $update_data);
            
            $this->update_status_cases_taskname_instance($case_id, 'verification_company_identification_hard');
            
            //#1328 add message after verification is submited by customer 
            $this->success_output(language('case_controller_verification_ThanksForSubmitVerificationMess'));

            return;
        }

        $is_invoicing_address_verification = true;
        if (strpos($case_exist[0]->case_identifier, 'VRAD') == false) {
            $is_invoicing_address_verification = false;
        }

        $postbox = $this->postbox_m->get_postbox($case_exist[0]->postbox_id);

        $this->template->set('is_invoicing_address_verification', $is_invoicing_address_verification);
        $this->template->set("cases_verification", $cases_verification_company_hard_check);
        $this->template->set("customer_addresses", $customer_addresses);
        $this->template->set("message_not_change_data", language('case_controller_verification_ResubmitVerificationWithoutChangeDataMess'));
        $this->template->set("postbox", $postbox);
        $this->template->set("case_id", $case_id);
        $this->template->build('verification/company_hard');
    }
    
    public function check_change_data_verification_company_identification_hard($cases_verification_company_hard_check)
    {
        $shareholders_name1  = false;
        $shareholders_name2  = false;
        $shareholders_name3  = false;
        $shareholders_name4  = false;
        //verification_local_file_path 
        $local_file_path  = false;

        if($cases_verification_company_hard_check->updated_date <= $cases_verification_company_hard_check->comment_date)
        {
            return false;
        }
        
        $case_id = $cases_verification_company_hard_check->case_id;
        if ( !empty($cases_verification_company_hard_check->verification_local_file_path) ) {
           if($_POST["passport_verification_change"] == "1"){
                $current_file = $cases_verification_company_hard_check->verification_local_file_path;
                $current_file_info = pathinfo($current_file);
                $running_number = CaseUtils::getBeforeRunningNumberBy($current_file);

                $server_filename = CaseUtils::getUploadFileNameBy('verification_company_identification_hard', '01', $running_number, $case_id);

                $previous_file = $current_file_info['dirname']."/".$server_filename.".".$current_file_info['extension'];
                
                if(!file_exists($previous_file)){
                    
                    $local_file_path = true;

                } else if( file_exists($previous_file) && (filesize($previous_file) != filesize($current_file)) ){
                    
                    $local_file_path = true;
                }
            }
        } 

        if( !empty($cases_verification_company_hard_check->shareholders_local_file_path_01) )
        {  
            if($_POST["shareholders_file_name_change_01"] == "1"){
                $current_file = $cases_verification_company_hard_check->shareholders_local_file_path_01 ;
                $current_file_info = pathinfo($current_file);
                $running_number  = CaseUtils::getBeforeRunningNumberBy($current_file);
                $server_filename = CaseUtils::getUploadFileNameBy('verification_company_identification_hard', '02', $running_number, $case_id);

                $previous_file = $current_file_info['dirname']."/".$server_filename.".".$current_file_info['extension'];
                
                if( !file_exists($previous_file) ){

                    $shareholders_name1 = true;

                } else if( file_exists($previous_file) && (filesize($previous_file) != filesize($current_file)) ){
                    
                    $shareholders_name1 = true;
                }
            }
        }

        if( !empty($cases_verification_company_hard_check->shareholders_local_file_path_02) )
        {  
            if($_POST["shareholders_file_name_change_02"] == "1"){
                $current_file = $cases_verification_company_hard_check->shareholders_local_file_path_02 ;
                $current_file_info = pathinfo($current_file);
                $running_number  = CaseUtils::getBeforeRunningNumberBy($current_file);
                $server_filename = CaseUtils::getUploadFileNameBy('verification_company_identification_hard', '03', $running_number, $case_id);

                $previous_file = $current_file_info['dirname']."/".$server_filename.".".$current_file_info['extension'];
                
                if( !file_exists($previous_file) ){

                    $shareholders_name2 = true;

                } else if( file_exists($previous_file) && (filesize($previous_file) != filesize($current_file)) ){
                    
                    $shareholders_name2 = true;
                }
            }
            
        }

        if( !empty($cases_verification_company_hard_check->shareholders_local_file_path_03) )
        {  
            if($_POST["shareholders_file_name_change_03"] == "1"){
                $current_file = $cases_verification_company_hard_check->shareholders_local_file_path_03 ;
                $current_file_info = pathinfo($current_file);
                $running_number  = CaseUtils::getBeforeRunningNumberBy($current_file);
                $server_filename = CaseUtils::getUploadFileNameBy('verification_company_identification_hard', '04', $running_number, $case_id);

                $previous_file = $current_file_info['dirname']."/".$server_filename.".".$current_file_info['extension'];

                if( !file_exists($previous_file) ){

                    $shareholders_name3 = true;

                } else if( file_exists($previous_file) && (filesize($previous_file) != filesize($current_file)) ){
                    
                    $shareholders_name3 = true;
                }
            }
            
        }

        if( !empty($cases_verification_company_hard_check->shareholders_local_file_path_04) )
        {  
            if($_POST["shareholders_file_name_change_04"] == "1"){
                $current_file = $cases_verification_company_hard_check->shareholders_local_file_path_04 ;
                $current_file_info = pathinfo($current_file);
                $running_number  = CaseUtils::getBeforeRunningNumberBy($current_file);
                $server_filename = CaseUtils::getUploadFileNameBy('verification_company_identification_hard', '05', $running_number, $case_id);

                $previous_file = $current_file_info['dirname']."/".$server_filename.".".$current_file_info['extension'];
                
                if( !file_exists($previous_file) ){

                    $shareholders_name4 = true;

                } else if( file_exists($previous_file) && (filesize($previous_file) != filesize($current_file)) ){
                    
                    $shareholders_name4 = true;
                }
            }
        }

        if( !($shareholders_name1 || $shareholders_name2  || $shareholders_name3 || $shareholders_name4 || $local_file_path)) {
            return false;
        }

        return true; 

    }
    
    
    /**
     * Display and submit for special identification (USPS, CMRA, California).
     */
    public function verification_special_form_PS1583()
    {
        ci()->load->library('partner/partner_api');
        ci()->load->library('cases/cases_api');
        ci()->load->library('mailbox/mailbox_api');
        ci()->load->library('settings/settings_api');
        ci()->lang->load('cases/cases');

        // Check exist this case
        $case_id = $this->input->get_post("case_id", '');
        $type = $this->input->get_post("type", '');   
        $case_exist = cases_api::getCaseVerification($case_id);
        if (empty($case_exist)) {
            redirect('cases/verification');
        }
        
        $customer_id = APContext::getCustomerByCase($case_id);
        $customer = APContext::getCustomerByID($customer_id);
        $customer_addresses = addresses_api::getCustomerAddress($customer_id);
        
        // Get status verification special
        $obj_case = cases_api::getUSPSFormVerificationCase($case_id);

        // Gets base_taskname 
        $base_taskname = 'verification_special_form_PS1583';
        if ($type == 'general') {
            $base_taskname = 'verification_General_CMRA';
        }else if($type == 'california'){
            $base_taskname = 'verification_california_mailbox';
        }
    
        $verify_postbox_id = $case_exist[0]->postbox_id;
        if (!empty($verify_postbox_id)) {
            $verify_postbox = mailbox_api::getPostBoxByID($verify_postbox_id);
            $this->template->set("verify_postbox", $verify_postbox);
        }

        $check_exist_detail_case = cases_api::getCaseVerificationUSPS($case_id);
        
        // get country
        $countries = settings_api::getAllCountries();

        // If this is post method
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $response['code'] = 0;
            
            //Load library
            $this->load->helper(array(
                'form',
                'url'
            ));
            $this->load->library('form_validation');
            
            // Submit data
            // Update data to database
            $update_data['case_id'] = $case_id;
            $update_data['name_to_delivery'] = $verify_postbox->name .($verify_postbox->name && $verify_postbox->company ?', ' : "") . $verify_postbox->company;
            $update_data['name_of_applicant'] = $this->input->get_post('name_of_applicant');
            $update_data['street_of_applicant'] = $this->input->get_post('street_of_applicant');
            $update_data['city_of_applicant'] = $this->input->get_post('city_of_applicant');
            $update_data['region_of_applicant'] = $this->input->get_post('region_of_applicant');
            $update_data['postcode_of_applicant'] = $this->input->get_post('postcode_of_applicant');
            $update_data['country_of_applicant'] = $this->input->get_post('country_of_applicant');
            $update_data['phone_of_applicant'] = $this->input->get_post('phone_of_applicant');
            $update_data['id_of_applicant'] = $this->input->get_post('id_of_applicant');
            $update_data['license_of_applicant'] = $this->input->get_post('license_of_applicant');
            $update_data['name_of_corporation'] = $verify_postbox->company;
            $update_data['street_of_corporation'] = $this->input->get_post('street_of_corporation');
            $update_data['city_of_corporation'] = $this->input->get_post('city_of_corporation');
            $update_data['region_of_corporation'] = $this->input->get_post('region_of_corporation');
            $update_data['postcode_of_corporation'] = $this->input->get_post('postcode_of_corporation');
            $update_data['country_of_corporation'] = $this->input->get_post('country_of_corporation');
            $update_data['phone_of_corporation'] = $this->input->get_post('phone_of_corporation');
            $update_data['business_type_of_corporation'] = $this->input->get_post('business_type_of_corporation');


            //Set rules for form validation
            $this->form_validation->set_rules(CasesValidator::$special_rules);
            if ($this->input->get_post('name_of_corporation', '') != '') {
                $this->form_validation->set_rules(CasesValidator::$sub_rules);
            }

            $error_messages = '';
            if ($this->form_validation->run() == FALSE) {
                $error_messages = $this->form_validation->error_array();
            }

            if(!empty($check_exist_detail_case) && !empty($check_exist_detail_case->comment_content)){
               if($_POST['check_click_save_btn'] == '0'){

                    $check_change_data = $this->check_change_data($type,$check_exist_detail_case, $update_data);
                    if(! $check_change_data){
                        $response['code'] = 0;
                        $response['message'] = language('case_controller_verification_ResubmitVerificationWithoutChangeDataMess');
                        $this->error_output( $response );
                        return;
                    }
               }
            }
            
            // valid input
            if (!empty($error_messages)) {
                $response['code'] = 1;
                $response['message'] = $error_messages;
                $this->error_output(validation_errors(), $response );
                return;
            }
            
            // update officer
            if(!CasesValidator::_validate_officer_owner()){
                $error_messages .= language('case_controller_verification_ValidateOfficeOwnerMess'). "<br/>";
            }
            
            // update mail receiver
            if(!CasesValidator::_validate_mail_receiver()){
                $error_messages .= language('case_controller_verification_ValidateMailReceiverMess')."<br/>";
            }
            
            // update  business company
            if (!empty($verify_postbox_id) && $verify_postbox->company) {
                if(!CasesValidator::_validate_business_company($verify_postbox)){
                    $error_messages .= language('case_controller_verification_ValidateBusinessCompanyMess')."<br/>";
                }
                
                if(empty($this->input->get_post('business_type_of_corporation'))){
                    $error_messages .= language('case_controller_verification_RequireTypeOfCorporationMess')."<br/>";
                }
            }
            
       
            if(!empty($error_messages)){
                $this->error_output($error_messages, array("message" =>$error_messages));
                return;
            }

//            $update_data['note1'] = $this->input->get_post('note1');
//            $update_data['note2'] = $this->input->get_post('note2');
//            $update_data['note3'] = $this->input->get_post('note3');
            $update_data['created_date'] = now();
            $update_data['updated_date'] = now();
            $update_data['status'] = '0';
            $update_data['comment_for_registration_content'] = $this->input->get_post('comment_for_registration_content');
            if(isset($update_data['comment_for_registration_content'])){
            	$update_data['comment_for_registration_date'] = now();
            }

            //Update or Insert
            if (!empty($check_exist_detail_case)) {
                cases_api::updateCaseVerificationUSPS($case_id, $update_data);
            } else {
                cases_api::createCaseVerificationUSPS($update_data);
            }

            // Get partner name and address
            $partner = partner_api::getPartnerNameAndAddress($case_id, $base_taskname, $verify_postbox_id);
            $update_data = array_merge($update_data, $partner);
            $update_data['xx'] = $this->input->get_post('xx');

            // If this case is using to verify postbox
            if (!empty($verify_postbox_id)) {
                $update_data['verify_postbox'] = $verify_postbox;
            }

            // get country name
            $country_applicant_name = '';
            $country_corporation_name = '';
            foreach ($countries as $c) {
                if ($c->id == $update_data['country_of_applicant']) {
                    $country_applicant_name = $c->country_name;
                }
                if ($c->id == $update_data['country_of_corporation']) {
                    $country_corporation_name = $c->country_name;
                }
            }
            $update_data['country_applicant_name'] = $country_applicant_name;
            $update_data['country_corporation_name'] = $country_corporation_name;
            
            // update officer
            $officer_names = $this->input->get_post('officer_name');
            $officer_types = $this->input->get_post('officer_type');
            $officer_rates = $this->input->get_post('officer_rate');
            $officer_file_ids = $this->input->get_post('officer_file_id');
            if($officer_file_ids){
                $index = 0;
                foreach($officer_file_ids as $file_id){
                    $this->case_usps_officer_m->update_by_many(array(
                        "id" => $file_id,
                        "case_id" => $case_id
                    ), array(
                        "name" => $officer_names[$index],
                        "type" => $officer_types[$index],
                        "rate" => $officer_rates[$index],
                    ));
                    
                    $index ++;
                }
            }

            $this->session->set_userdata('SPECIAL_FILE_PATH', cases_api::create_usps_pdf_document($update_data, $base_taskname, $customer));
            
            //#1328 add message after verification is submited by customer 
            $this->success_output(language('case_controller_verification_ThanksForSubmitVerificationMess'));

            return;
        }
        $postbox = mailbox_api::getMainPostboxByCustomerID($customer_id);

        if( empty($check_exist_detail_case->comment_date) ) {
            $first_submit = 1;
        }
        else {
            $first_submit = 0;
        }
        
        // Gets officer owner.
        $officers = $this->case_usps_officer_m->get_many_by_many(array(
            "case_id" => $case_id
        ));
        $this->template->set("officers", $officers);
        
        // Gets mail receiver.
        $mailReceivers = $this->case_resource_m->get_many_by_many(array(
            "case_id" => $case_id,
            "base_taskname" => $base_taskname,
            "seq_number" => "01"
        ));
        $this->template->set("mailReceivers", $mailReceivers);
        
        // Gets business license
        $business_licenses = $this->case_resource_m->get_many_by_many(array(
            "case_id" => $case_id,
            "base_taskname" => $base_taskname,
            "seq_number" => "02"
        ));
        $this->template->set("business_licenses", $business_licenses);
        
        $this->template->set("first_submit", $first_submit);
        $this->template->set("postbox", $postbox);

        $location = addresses_api::getLocationByID($postbox->location_available_id);
        $this->template->set("location", $location);
        $this->template->set('countries', $countries);
        $this->template->set('type', $type);
        $this->template->set("cases_verification", $check_exist_detail_case);
        $this->template->set("customer_addresses", $customer_addresses);
        $this->template->set("case_id", $case_id);
        $this->template->set("status_verification_special", $obj_case);
        $this->template->set("base_taskname", $base_taskname);
        $this->template->set("milestone_name", CaseUtils::get_milestone_name($case_id, $base_taskname));
        $this->template->build('verification/special');
    }
    
    
    /**
     * Check resubmit verification is changed data or not
     * @param type $type : USPS, CMRA, California
     * @param type $cases_verification_usps_check
     * @return boolean : True : Change data, False :  Does not change data
     */
    public function check_change_data($type,$cases_verification_usps_check, $input_text_data) {
        
        if($cases_verification_usps_check->updated_date <= $cases_verification_usps_check->comment_date){
            return false;
        }

        $case_id = $cases_verification_usps_check->case_id;
        $general_crma_flag = false;

        //Get base task name
        $base_taskname = 'verification_special_form_PS1583';
        if ($type == 'general') {
            $general_crma_flag = true;
            $base_taskname = "verification_General_CMRA";
        } elseif ($type == 'california') {
            $base_taskname = "verification_california_mailbox";
        }

        //Check change input text information
        $array_cases_verification_usps_check = json_decode(json_encode($cases_verification_usps_check), true);
        if (!empty(array_diff($input_text_data, $array_cases_verification_usps_check))) {
            return true;
        }

        //Check change file for Signed Scan Document
        if( !empty($cases_verification_usps_check->verification_local_file_path))
        {  
            if($_POST['special_verification_file_change'] == '1'){

                $current_file = $cases_verification_usps_check->verification_local_file_path;
                $current_file_info = pathinfo($current_file);
                $running_number = CaseUtils::getBeforeRunningNumberBy($current_file);
                $server_filename = CaseUtils::getUploadFileNameBy($base_taskname, '04', $running_number, $case_id);
                $previous_file = $current_file_info['dirname']."/".$server_filename.".".$current_file_info['extension'];

                if( !file_exists($previous_file) ){
                    return true;
                } elseif( file_exists($previous_file) && (filesize($previous_file) != filesize($current_file)) ){
                    return true;
                }
            }
        } 
            
        //First identification document
        if( !empty($cases_verification_usps_check->id_of_applicant_local_file_path) ) 
        {
            if($_POST['id_of_applicant_verification_change'] == '1'){

                $current_file = $cases_verification_usps_check->id_of_applicant_local_file_path;
                $current_file_info = pathinfo($current_file);
                $running_number = CaseUtils::getBeforeRunningNumberBy($current_file);
                $server_filename = CaseUtils::getUploadFileNameBy($base_taskname, '01', $running_number, $case_id);
                $previous_file = $current_file_info['dirname']."/".$server_filename.".".$current_file_info['extension'];

                if( !file_exists($previous_file) ){
                    return true;
                } elseif( file_exists($previous_file) && (filesize($previous_file) != filesize($current_file)) ){
                   return true;
                }
            }
        }

        //Second identification document
        if( !empty($cases_verification_usps_check->license_of_applicant_local_file_path) )
        {
            if($_POST['license_of_applicant_verification_change'] == '1'){

                $current_file = $cases_verification_usps_check->license_of_applicant_local_file_path;
                $current_file_info = pathinfo($current_file);
                $running_number = CaseUtils::getBeforeRunningNumberBy($current_file);
                $server_filename = CaseUtils::getUploadFileNameBy($base_taskname, '02', $running_number, $case_id);
                $previous_file = $current_file_info['dirname']."/".$server_filename.".".$current_file_info['extension'];

                if( !file_exists($previous_file) ){
                    return true;

                } elseif( file_exists($previous_file) && (filesize($previous_file) != filesize($current_file)) ){
                    return true;
                }
            }
        }

        /*if( !empty($cases_verification_usps_check->additional_local_file_path) )
        {
            if($_POST['additional_verification_change'] == '1'){

                $current_file = $cases_verification_usps_check->additional_local_file_path;
                $current_file_info = pathinfo($current_file);
                $running_number = CaseUtils::getBeforeRunningNumberBy($current_file);
                $server_filename = CaseUtils::getUploadFileNameBy($base_taskname, '03', $running_number, $general_crma_flag);
                $previous_file = $current_file_info['dirname']."/".$server_filename.".".$current_file_info['extension'];
                if( file_exists($previous_file) && (filesize($previous_file) != filesize($current_file)) ){
                    $check_additional_local_file_path = true;
                }
            }
        }*/

        /*if( !empty($cases_verification_usps_check->additional_company_local_file_path) )
        {
            if($_POST['additional_company_verification_change'] == '1'){
                $current_file = $cases_verification_usps_check->additional_company_local_file_path;
                $current_file_info = pathinfo($current_file);
                $running_number = CaseUtils::getBeforeRunningNumberBy($current_file);
                $server_filename = CaseUtils::getUploadFileNameBy($base_taskname, '05', $running_number, $general_crma_flag);
                $previous_file = $current_file_info['dirname']."/".$server_filename.".".$current_file_info['extension'];
                if( file_exists($previous_file) && (filesize($previous_file) != filesize($current_file)) ){
                    $check_additional_company_local_file_path = true;
                }
            }
        }*/
            
        // check mail receiver
        if($_POST['change_usps_mail_receiver_flag'] == '1'){
            $check_created = $this->case_usps_mail_receiver_m->get_by_many(array(
                "case_id" =>$case_id,
                "created_date >"=>now()-10*60 // check file has been uploaded on 10 minutes
            ));

            if($check_created){
                return true;
            } else {
                $mail_receivers = $this->case_usps_mail_receiver_m->get_by_many(array(
                    "case_id" =>$case_id,
                    "updated_date >"=>now()-10*60 // check file has been uploaded on 10 minutes
                ));
                
                foreach ($mail_receivers as $receiver){
                    $current_file = $receiver->receiver_local_path;
                    $current_file_info = pathinfo($current_file);
                    $running_number = CaseUtils::getBeforeRunningNumberBy($current_file);
                    $server_filename = CaseUtils::getUploadFileNameBy($base_taskname, '06', $running_number, $case_id);
                    $previous_file = $current_file_info['dirname']."/".$server_filename.".".$current_file_info['extension'];

                    if( !file_exists($previous_file) ){
                        return true;

                    } elseif( file_exists($previous_file) && (filesize($previous_file) != filesize($current_file)) ){
                        return true;
                    }
                }
            }
        }
            
        // check officer owner
        if($_POST['change_usps_officer_flag'] == '1'){
            $check_created = $this->case_usps_officer_m->get_by_many(array(
                "case_id" =>$case_id,
                "created_date >"=>now()-10*60 // check file has been uploaded on 10 minutes
            ));

            if($check_created){
                return true;
            } else {
                $mail_receivers = $this->case_usps_officer_m->get_by_many(array(
                    "case_id" =>$case_id,
                    "updated_date >"=>now()-10*60 // check file has been uploaded on 10 minutes
                ));
                
                foreach ($mail_receivers as $receiver){
                    $current_file = $receiver->officer_local_path;
                    $current_file_info = pathinfo($current_file);
                    $running_number = CaseUtils::getBeforeRunningNumberBy($current_file);
                    $server_filename = CaseUtils::getUploadFileNameBy($base_taskname, '07', $running_number, $case_id);
                    $previous_file = $current_file_info['dirname']."/".$server_filename.".".$current_file_info['extension'];

                    if( !file_exists($previous_file) ){
                        return true;
                    } elseif ( file_exists($previous_file) && (filesize($previous_file) != filesize($current_file)) ){
                        return true;
                    }
                }
            }
        }
            
        // check business license
        if($_POST['change_usps_business_license_flag'] == '1'){
            $check_created = $this->case_usps_business_license_m->get_by_many(array(
                "case_id" =>$case_id,
                "created_date >"=>now()-10*60 // check file has been uploaded on 10 minutes
            ));

            if($check_created){
                return true;
            } else {
                $mail_receivers = $this->case_usps_business_license_m->get_by_many(array(
                    "case_id" =>$case_id,
                    "updated_date >"=>now()-10*60 // check file has been uploaded on 10 minutes
                ));
                
                foreach ($mail_receivers as $receiver){
                    $current_file = $receiver->business_license_local_file_path;
                    $current_file_info = pathinfo($current_file);
                    $running_number = CaseUtils::getBeforeRunningNumberBy($current_file);
                    $server_filename = CaseUtils::getUploadFileNameBy($base_taskname, '08', $running_number, $case_id);
                    $previous_file = $current_file_info['dirname']."/".$server_filename.".".$current_file_info['extension'];

                    if( !file_exists($previous_file) ){
                        return true;
                    } elseif( file_exists($previous_file) && (filesize($previous_file) != filesize($current_file)) ){
                        return true;
                    }
                }
            }
        }
            
        return false;
    }
    
    /**
     * special_file_export
     */
    public function special_file_export()
    {
        $invoice_file_path = $this->session->userdata('SPECIAL_FILE_PATH');
        $this->session->unset_userdata('SPECIAL_FILE_PATH');
        if (empty($invoice_file_path))
            return;
        header('Content-Description: File Transfer');
        header('Content-type: application/pdf');
        header('Content-Disposition: attachment; filename=' . basename($invoice_file_path));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($invoice_file_path));
        readfile($invoice_file_path);
    }

    /**
     * This function was called by ajax to submit data again
     */
    public function verification_special_form_PS1583_submit()
    { 
        $case_id = $this->input->get_post("case_id", '');
        $type = $this->input->get_post('type', '');

        // Gets base_taskname
        $base_taskname = 'verification_special_form_PS1583';
        if ($type == 'general') {
            $base_taskname = 'verification_General_CMRA';
        }else if($type == 'california'){
            $base_taskname = 'verification_california_mailbox';
        }

        // Check exist this case
        $case_exist = $this->cases_m->get_many($case_id);
        if (empty($case_exist)) {
            redirect('cases/verification');
        }
        
        $customer_id = APContext::getCustomerByCase($case_id);
        $customer_addresses = $this->customers_address_m->get_by_many(array(
            'customer_id' => $customer_id
        ));


        // If this is post method
        if ($_POST) {
            
            //Load libraries
            $this->load->helper(array(
                'form',
                'url'
            ));
            $this->load->library('form_validation');
            
            // Submit data
            $update_data['case_id'] = $case_id;
            $update_data['name_to_delivery'] = $this->input->get_post('name_to_delivery');
            $update_data['name_of_applicant'] = $this->input->get_post('name_of_applicant');
            $update_data['street_of_applicant'] = $this->input->get_post('street_of_applicant');
            $update_data['city_of_applicant'] = $this->input->get_post('city_of_applicant');
            $update_data['region_of_applicant'] = $this->input->get_post('region_of_applicant');
            $update_data['postcode_of_applicant'] = $this->input->get_post('postcode_of_applicant');
            $update_data['country_of_applicant'] = $this->input->get_post('country_of_applicant');
            $update_data['phone_of_applicant'] = $this->input->get_post('phone_of_applicant');
            $update_data['id_of_applicant'] = $this->input->get_post('id_of_applicant');
            $update_data['license_of_applicant'] = $this->input->get_post('license_of_applicant');
            $update_data['name_of_corporation'] = $this->input->get_post('name_of_corporation');
            $update_data['street_of_corporation'] = $this->input->get_post('street_of_corporation');
            $update_data['city_of_corporation'] = $this->input->get_post('city_of_corporation');
            $update_data['region_of_corporation'] = $this->input->get_post('region_of_corporation');
            $update_data['postcode_of_corporation'] = $this->input->get_post('postcode_of_corporation');
            $update_data['country_of_corporation'] = $this->input->get_post('country_of_corporation');
            $update_data['phone_of_corporation'] = $this->input->get_post('phone_of_corporation');
            $update_data['business_type_of_corporation'] = $this->input->get_post('business_type_of_corporation');
            
            $this->form_validation->set_rules(CasesValidator::$special_rules);
            if ($this->input->get_post('name_of_corporation', '') != '') {
                $this->form_validation->set_rules(CasesValidator::$sub_rules);
            }

            if ($this->form_validation->run() == FALSE) {
                $error_messages = $this->form_validation->error_array();
                $this->error_output(validation_errors(), $error_messages);
                return;
            }
            
            $error_message = "";            
            // update officer
            if(!CasesValidator::_validate_officer_owner()){
                $error_messages .= language('case_controller_verification_ValidateOfficeOwnerMess') . "<br/>";
            }
            
            // update mail receiver
            if(!CasesValidator::_validate_mail_receiver()){
                $error_messages .= language('case_controller_verification_ValidateMailReceiverMess') . "<br/>";
            }
            
            // update  business company
            if (!empty($verify_postbox_id) && $verify_postbox->company) {
                if(!CasesValidator::_validate_business_company($verify_postbox)){
                    $error_messages .= language('case_controller_verification_ValidateBusinessCompanyMess') . "<br/>";
                }
                
                if(empty($this->input->get_post('business_type_of_corporation'))){
                    $error_messages .= language('case_controller_verification_RequireTypeOfCorporationMess') . "<br/>";
                }
            }

            if(!empty($error_message)){
                $this->error_output("", array("message" =>$error_message));
                return;
            }

            // Check exist case id
            $check_exist_detail_case = $this->cases_verification_usps_m->get_by_many(array(
                'case_id' => $case_id
            ));
            if (empty($check_exist_detail_case)) {
                $message = language('case_controller_verification_CreateVerificationPDFMess');
                $this->error_output($message);
                return;
            } else {
                if (empty($check_exist_detail_case->verification_amazon_file_path)) {
                    $message = language('case_controller_verification_RequiredUploadSignedScanMess');
                    $this->error_output($message);
                    return;
                }

                if (empty($check_exist_detail_case->name_to_delivery)) {
                    $message = language('case_controller_verification_CreateVerificationPDFMess');
                    $this->error_output($message);
                    return;
                }
                
                if(!empty($check_exist_detail_case) && !empty($check_exist_detail_case->comment_content)){
                   $this->check_change_data($type, $check_exist_detail_case, $update_data);
                }
            }

//            $update_data['note1'] = $this->input->get_post('note1');
//            $update_data['note2'] = $this->input->get_post('note2');
//            $update_data['note3'] = $this->input->get_post('note3');
            $update_data['created_date'] = now();
            $update_data['updated_date'] = now();
            $update_data['status'] = '1';
            $update_data['comment_for_registration_content'] = $this->input->get_post('comment_for_registration_content');
            if(isset($update_data['comment_for_registration_content'])){
            	$update_data['comment_for_registration_date'] = now();
            }

            // Update
            if (!empty($check_exist_detail_case)) {
                $this->cases_verification_usps_m->update_by_many(array(
                    'case_id' => $case_id
                ), $update_data);
            }
            
            // update business license, mail receiver, officer.
            $officer_file_ids = $this->input->get_post("officer_file_id");
            $officer_names = $this->input->get_post("officer_name");
            $officer_rates = $this->input->get_post("officer_rate");
            $officer_types = $this->input->get_post("officer_type");
            if($officer_file_ids){
                $index = 0;
                
                foreach ($officer_file_ids as $file_id){
                    if($file_id){
                        $check_officer = $this->case_usps_officer_m->get_by('id', $file_id);
                        $tmp_data = array(
                            "name" => $officer_names[$index],
                            "rate" => $officer_rates[$index],
                            "type" => $officer_types[$index],
                        );
                        if($check_officer){
                            $this->case_usps_officer_m->update_by_many(array(
                                "id" => $check_officer->id,
                                'case_id' => $case_id
                            ), $tmp_data);
                        }
                    }
                    $index ++;
                }
            }
            
            // update mail receiver
            $mail_receiver_name = $this->input->get_post("mail_receiver_name");
            $mail_receiver_ids = $this->input->get_post("mail_receiver_id");
            if($mail_receiver_ids){
                $index = 0;
                foreach ($mail_receiver_ids as $file_id){
                    if($file_id){
                        $check_mail_receiver = $this->case_resource_m->get_by('id', $file_id);

                        if($check_mail_receiver){
                            $this->case_resource_m->update_by_many(array(
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

            $this->update_status_cases_taskname_instance($case_id, $base_taskname);
            
            //#1328 add message after verification is submited by customer 
            $this->success_output(language('case_controller_verification_ThanksForSubmitVerificationMess'));
            
            return;
        }

        // Get main postbox name
        $postbox = $this->postbox_m->get_by_many(array(
            'customer_id' => $customer_id,
            'is_main_postbox' => APConstants::ON_FLAG
        ));
        $this->template->set("postbox", $postbox);

        // Get postbox location information
        $location = $this->location_m->get_by_many(array(
            'id' => $postbox->location_available_id
        ));
        $this->template->set("location", $location);
        
        $this->template->set("customer_addresses", $customer_addresses);
        $this->template->set("case_id", $case_id);
        $this->template->build('verification/special');
    }

    /**
     * special_upload_file
     */
    public function special_upload_file()
    {
        $case_id = $this->input->get_post('case_id');
        $input_file_client_name = $this->input->get_post('input_file_client_name');
        // Check data
        $cases_verification_usps_check = $this->cases_verification_usps_m->get_by_many(array(
            'case_id' => $case_id
        ));

        // Check general CMRA case.
        $type = $this->input->get_post('type');
        $general_crma_flag = false;
        $base_taskname = 'verification_special_form_PS1583';
        if ($type == 'general') {
            $general_crma_flag = true;
            $base_taskname = "verification_General_CMRA";
        }else if($type == 'california'){
            $base_taskname = 'verification_california_mailbox';
        }
   
        $running_number = '001';
        if (!empty($cases_verification_usps_check)) {
            switch ($input_file_client_name) {
                case "special_verification_file":
                    $running_number = CaseUtils::getRunningNumberBy($cases_verification_usps_check->verification_local_file_path);
                    break;
                case "id_of_applicant_verification":
                    $running_number = CaseUtils::getRunningNumberBy($cases_verification_usps_check->id_of_applicant_local_file_path);
                    break;
                case "license_of_applicant_verification":
                    $running_number = CaseUtils::getRunningNumberBy($cases_verification_usps_check->license_of_applicant_local_file_path);
                    break;
                case "additional_verification":
                    $running_number = CaseUtils::getRunningNumberBy($cases_verification_usps_check->additional_local_file_path);
                    break;
                case "additional_company_verification":
                    $running_number = CaseUtils::getRunningNumberBy($cases_verification_usps_check->additional_company_local_file_path);
                    break;    
            }

        }
        $server_filename = '';
        if ($input_file_client_name == 'id_of_applicant_verification') {
            $server_filename = CaseUtils::getUploadFileNameBy($base_taskname, '01', $running_number, $case_id);
        } else if ($input_file_client_name == 'license_of_applicant_verification') {
            $server_filename = CaseUtils::getUploadFileNameBy($base_taskname, '02', $running_number, $case_id);
        } else if ($input_file_client_name == 'additional_verification') {
            $server_filename = CaseUtils::getUploadFileNameBy($base_taskname, '03', $running_number, $case_id);
        } else if ($input_file_client_name == 'special_verification_file') {
            $server_filename = CaseUtils::getUploadFileNameBy($base_taskname, '04', $running_number, $case_id);
        }else if ($input_file_client_name == 'additional_company_verification') {
            $server_filename = CaseUtils::getUploadFileNameBy($base_taskname, '05', $running_number, $case_id);
        }
        
        $cases_verification_usps_id = '';
        if (empty($cases_verification_usps_check)) {
            $cases_verification_usps_id = $this->cases_verification_usps_m->insert(array(
                'case_id' => $case_id,
                'status' => '0',
                'created_date' => now()
            ));
        } else {
            $cases_verification_usps_id = $cases_verification_usps_check->id;
        }

        // upload file
        $upload_info = cases_api::upload_file_case_verification($case_id, $input_file_client_name, $server_filename);
        
        if (!$upload_info['status']) {
            $this->error_output($upload_info['message']);
            return;
        }

        // Update data to database
        $update_data['status'] = '0';
        $update_data['updated_date'] = now();
        switch ($input_file_client_name) {
            case "special_verification_file":
                $update_data['verification_local_file_path']  = $upload_info['local_file_path'];
                $update_data['verification_amazon_file_path'] = $upload_info['amazon_file_path'];
                break;
            case "id_of_applicant_verification":
                $update_data['id_of_applicant_local_file_path'] = $upload_info['local_file_path'];
                $update_data['id_of_applicant_amazon_file_path'] = $upload_info['amazon_file_path'];
                break;
            case "license_of_applicant_verification":
                $update_data['license_of_applicant_local_file_path'] = $upload_info['local_file_path'];
                $update_data['license_of_applicant_amazon_file_path'] = $upload_info['amazon_file_path'];
                break;
            case "additional_verification":
                $update_data['additional_local_file_path'] = $upload_info['local_file_path'];
                $update_data['additional_amazon_file_path'] = $upload_info['amazon_file_path'];
                break;
            case "additional_company_verification":
                $update_data['additional_company_local_file_path'] = $upload_info['local_file_path'];
                $update_data['additional_company_amazon_file_path'] = $upload_info['amazon_file_path'];
                break;    
        }

        $filename = $upload_info['local_file_path'];

        $this->cases_verification_usps_m->update_by_many(array(
            'id' => $cases_verification_usps_id
        ), $update_data);

        $this->success_output('');
        exit;
    }

    /**
     * Delete additional verification file
     */
    public function delete_additional_verification_file()
    {
        if ($this->is_ajax_request()) {
            $case_id = $this->input->get_post('case_id');
            $file_type = $this->input->get_post('file_type');
            $cases_verification_usps = $this->cases_verification_usps_m->get_by(array('case_id' => $case_id));
            if ($cases_verification_usps) {
                switch ($file_type) {
                    case 'additional_company_verification':
                        // Delete additional amazon file
                        if ($cases_verification_usps->additional_company_amazon_file_path) {
                            ci()->load->library('S3');
                            $default_bucket_name = ci()->config->item('default_bucket');
                            $res = S3::deleteObject($default_bucket_name, $cases_verification_usps->additional_company_amazon_file_path);
                        }

                        // Delete additional local file
                        if (file_exists($cases_verification_usps->additional_company_local_file_path)) {
                            @unlink($cases_verification_usps->additional_company_local_file_path);
                        }

                        // Update the record
                        $this->cases_verification_usps_m->update($cases_verification_usps->id, array(
                            'additional_company_local_file_path' => '',
                            'additional_company_amazon_file_path' => ''
                        ));
                        break;
                    
                    default:
                        // Delete additional amazon file
                        if ($cases_verification_usps->additional_amazon_file_path) {
                            ci()->load->library('S3');
                            $default_bucket_name = ci()->config->item('default_bucket');
                            $res = S3::deleteObject($default_bucket_name, $cases_verification_usps->additional_amazon_file_path);
                        }

                        // Delete additional local file
                        if (file_exists($cases_verification_usps->additional_local_file_path)) {
                            @unlink($cases_verification_usps->additional_local_file_path);
                        }

                        // Update the record
                        $this->cases_verification_usps_m->update($cases_verification_usps->id, array(
                            'additional_local_file_path' => '',
                            'additional_amazon_file_path' => ''
                        ));
                        break;
                }
                
            }
            $this->success_output('');
            return true;
        } else {
            $this->error_output('Invalid request');
            return false;
        }
    }

    /**
     * company_hard_upload_file.
     */
    public function company_hard_upload_file()
    {
        $case_id = $this->input->get_post('case_id');
        $input_file_client_name = $this->input->get_post('input_file_client_name');

        // get document
        $cases_registration_document_check = $this->cases_verification_company_hard_m->get_by_many(array(
            'case_id' => $case_id
        ));

        $server_filename = '';
        if ($input_file_client_name == 'business_registration_verification') {
            $running_number = '001';
            if (!empty($cases_registration_document_check)) {
                $filename = $cases_registration_document_check->verification_local_file_path;
                $running_number = CaseUtils::getRunningNumberBy($filename);
            }
            $server_filename = CaseUtils::getUploadFileNameBy('verification_company_identification_hard', '01', $running_number, $case_id);
        }
        $upload_info =cases_api::upload_file_case_verification($case_id, $input_file_client_name, $server_filename);

        if (!$upload_info['status']) {
            $this->error_output($upload_info['message']);
            return;
        }

        // Update data to database
        $cases_registration_document_id = '';
        if (empty($cases_registration_document_check)) {
            $cases_registration_document_id = $this->cases_verification_company_hard_m->insert(array(
                'case_id' => $case_id,
                'status' => '0',
                'created_date' => now()
            ));
        } else {
            $cases_registration_document_id = $cases_registration_document_check->id;
        }

        $update_data['updated_date'] = now();
        $update_data['verification_local_file_path'] = $upload_info['local_file_path'];
        $update_data['verification_amazon_file_path'] = $upload_info['amazon_file_path'];

        $this->cases_verification_company_hard_m->update_by_many(array(
            'id' => $cases_registration_document_id
        ), $update_data);

        $this->success_output('');
    }

    /**
     * company_hard_upload_file_shareholder.
     */
    public function company_hard_upload_file_shareholder()
    {
        $case_id = $this->input->get_post('case_id');
        $input_file_client_name = $this->input->get_post('input_file_client_name');
        $index = end(explode('_', $input_file_client_name));

        // gets document
        $cases_registration_document_check = $this->cases_verification_company_hard_m->get_by_many(array(
            'case_id' => $case_id
        ));

        $running_number = '001';
        if (!empty($cases_registration_document_check)) {
            $filename = $cases_registration_document_check->{'shareholders_local_file_path_' . $index};
            $running_number = CaseUtils::getRunningNumberBy($filename);
        }

        $server_filename = '';
        if ($input_file_client_name == 'shareholders_verification_01') {
            $server_filename = CaseUtils::getUploadFileNameBy('verification_company_identification_hard', '02', $running_number, $case_id);
        } else if ($input_file_client_name == 'shareholders_verification_02') {
            $server_filename = CaseUtils::getUploadFileNameBy('verification_company_identification_hard', '03', $running_number, $case_id);
        } else if ($input_file_client_name == 'shareholders_verification_03') {
            $server_filename = CaseUtils::getUploadFileNameBy('verification_company_identification_hard', '04', $running_number, $case_id);
        } else if ($input_file_client_name == 'shareholders_verification_04') {
            $server_filename = CaseUtils::getUploadFileNameBy('verification_company_identification_hard', '05', $running_number, $case_id);
        }
        $upload_info = cases_api::upload_file_case_verification($case_id, $input_file_client_name, $server_filename);

        if (!$upload_info['status']) {
            $this->error_output($upload_info['message']);
            return;
        }

        // Update data to database
        $cases_registration_document_id = '';
        if (empty($cases_registration_document_check)) {
            $cases_registration_document_id = $this->cases_verification_company_hard_m->insert(array(
                'case_id' => $case_id,
                'status' => '0',
                'created_date' => now()
            ));
        } else {
            $cases_registration_document_id = $cases_registration_document_check->id;
        }

        $update_data['updated_date'] = now();
        $update_data['shareholders_local_file_path_' . $index] = $upload_info['local_file_path'];
        $update_data['shareholders_amazon_file_path_' . $index] = $upload_info['amazon_file_path'];

        $this->cases_verification_company_hard_m->update_by_many(array(
            'id' => $cases_registration_document_id
        ), $update_data);

        $this->success_output('');
    }
    
      /**
     * Delete company hard file shareholder
     */
    public function delete_company_hard_file_shareholder()
    {
        if ($this->is_ajax_request()) {
            $case_id = $this->input->get_post('case_id');
            $file_type = $this->input->get_post('file_type');
            
            ci()->load->library('S3');
            $default_bucket_name = ci()->config->item('default_bucket');
            
            $cases_verification_company_hard = $this->cases_verification_company_hard_m->get_by(array('case_id' => $case_id));
            if ($cases_verification_company_hard) {
                switch ($file_type) {
                     case 'passport_verification':
                        // Delete additional amazon file
                        if ($cases_verification_company_hard->verification_amazon_file_path) {
                            $res = S3::deleteObject($default_bucket_name, $cases_verification_company_hard->verification_amazon_file_path);
                        }

                        // Delete additional local file
                        if (file_exists($cases_verification_company_hard->verification_local_file_path)) {
                            unlink($cases_verification_company_hard->verification_local_file_path);
                        }

                        // Update the record
                        $this->cases_verification_company_hard_m->update($cases_verification_company_hard->id, array(
                            'verification_local_file_path' => '',
                            'verification_amazon_file_path' => ''
                        ));
                        break;
                    case 'shareholders_01':
                        // Delete additional amazon file
                        if ($cases_verification_company_hard->shareholders_amazon_file_path_01) {
                            $res = S3::deleteObject($default_bucket_name, $cases_verification_company_hard->shareholders_amazon_file_path_01);
                        }

                        // Delete additional local file
                        if (file_exists($cases_verification_company_hard->shareholders_local_file_path_01)) {
                            unlink($cases_verification_company_hard->shareholders_local_file_path_01);
                        }

                        // Update the record
                        $this->cases_verification_company_hard_m->update($cases_verification_company_hard->id, array(
                            'shareholders_local_file_path_01' => '',
                            'shareholders_amazon_file_path_01' => ''
                        ));
                        break;
                    case 'shareholders_02':
                        // Delete additional amazon file
                        if ($cases_verification_company_hard->shareholders_amazon_file_path_02) {
                            $res = S3::deleteObject($default_bucket_name, $cases_verification_company_hard->shareholders_amazon_file_path_02);
                        }

                        // Delete additional local file
                        if (file_exists($cases_verification_company_hard->shareholders_local_file_path_02)) {
                            unlink($cases_verification_company_hard->shareholders_local_file_path_02);
                        }

                        // Update the record
                        $this->cases_verification_company_hard_m->update($cases_verification_company_hard->id, array(
                            'shareholders_local_file_path_02' => '',
                            'shareholders_amazon_file_path_02' => ''
                        ));
                        break;
                    case 'shareholders_03':
                        // Delete additional amazon file
                        if ($cases_verification_company_hard->shareholders_amazon_file_path_03) {
                            $res = S3::deleteObject($default_bucket_name, $cases_verification_company_hard->shareholders_amazon_file_path_03);
                        }

                        // Delete additional local file
                        if (file_exists($cases_verification_company_hard->shareholders_local_file_path_03)) {
                            unlink($cases_verification_company_hard->shareholders_local_file_path_03);
                        }

                        // Update the record
                        $this->cases_verification_company_hard_m->update($cases_verification_company_hard->id, array(
                            'shareholders_local_file_path_03' => '',
                            'shareholders_amazon_file_path_03' => ''
                        ));
                        break;
                    case 'shareholders_04':
                        // Delete additional amazon file
                        if ($cases_verification_company_hard->shareholders_amazon_file_path_04) {
                            $res = S3::deleteObject($default_bucket_name, $cases_verification_company_hard->shareholders_amazon_file_path_04);
                        }

                        // Delete additional local file
                        if (file_exists($cases_verification_company_hard->shareholders_local_file_path_04)) {
                            unlink($cases_verification_company_hard->shareholders_local_file_path_04);
                        }

                        // Update the record
                        $this->cases_verification_company_hard_m->update($cases_verification_company_hard->id, array(
                            'shareholders_local_file_path_04' => '',
                            'shareholders_amazon_file_path_04' => ''
                        ));
                        break;
                    default:
                        break;
                }
                
            }
            $this->success_output('');
            return true;
        } else {
            $this->error_output('Invalid request');
            return false;
        }
    }

    /**
     * company_soft_upload_file.
     */
    public function company_soft_upload_file()
    {
        $case_id = $this->input->get_post('case_id');
        $type = $this->input->get_post('type');
        $input_file_client_name = $this->input->get_post('input_file_client_name');
        
        $result = verification_api::upload_company_soft_verification($case_id, $type, $input_file_client_name);
        
        if(!$result['status']){
            $this->error_output($result['message']);
        }else{
            $this->success_output('');
        }
        return;
    }

    public function comp_soft_view_file()
    {
        $case_id = $this->input->get_post('case_id');
        $type = $this->input->get_post('type');
        $op = $this->input->get_post('op');
        $cases_registration_document_check = $this->cases_verification_personal_identity_m->get_by_many(array(
            'case_id' => $case_id,
            'type' => $type
        ));

        $local_file_patch = "";
        if (!empty($cases_registration_document_check)) {
            if ($op == '08') {
                $local_file_patch = $cases_registration_document_check->driver_license_document_local_file_path;
            } else {
                $local_file_patch = $cases_registration_document_check->verification_local_file_path;
            }
        }

        $this->view_verification_file($local_file_patch);
    }

    public function comp_hard_view_file()
    {
        $case_id = $this->input->get_post('case_id');
        $op = $this->input->get_post('op');
        $cases_registration_document_check = $this->cases_verification_company_hard_m->get_by_many(array(
            'case_id' => $case_id
        ));

        $local_file_patch = "";
        if (!empty($cases_registration_document_check)) {
            switch ($op) {
                case "00":
                    $local_file_patch = $cases_registration_document_check->verification_local_file_path;
                    break;
                case "01":
                    $local_file_patch = $cases_registration_document_check->shareholders_local_file_path_01;
                    break;
                case "02":
                    $local_file_patch = $cases_registration_document_check->shareholders_local_file_path_02;
                    break;
                case "03":
                    $local_file_patch = $cases_registration_document_check->shareholders_local_file_path_03;
                    break;
                case "04":
                    $local_file_patch = $cases_registration_document_check->shareholders_local_file_path_04;
                    break;
            }
        }

        $this->view_verification_file($local_file_patch);
    }

    public function special_view_file()
    {
        $case_id = $this->input->get_post('case_id');
        $op = $this->input->get_post('op');
        
        if($op =='owner'){
            // check only for company EMS case.
            $cases_verification_usps_check = 1;
        }else{
            $cases_verification_usps_check = $this->cases_verification_usps_m->get_by_many(array(
                'case_id' => $case_id
            ));
        }

        $local_file_patch = "";
        if (!empty($cases_verification_usps_check)) {
            switch ($op) {
                case "id":
                    $local_file_patch = $cases_verification_usps_check->id_of_applicant_local_file_path;
                    break;
                case "license":
                    $local_file_patch = $cases_verification_usps_check->license_of_applicant_local_file_path;
                    break;
                case "additional":
                    $local_file_patch = $cases_verification_usps_check->additional_local_file_path;
                    break;
                case "company":
                    $local_file_patch = $cases_verification_usps_check->additional_company_local_file_path;
                    break;
                case "mail_receiver":
                    $mail_receiver = $this->case_usps_mail_receiver_m->get_by_many(array(
                        "case_id" => $case_id,
                        "id" => $this->input->get_post('id')
                    ));
                    $local_file_patch = $mail_receiver? $mail_receiver->receiver_local_path : "";
                    break;
                case "owner":
                case "officer_onwer":
                    $mail_receiver = $this->case_usps_officer_m->get_by_many(array(
                        "case_id" => $case_id,
                        "id" => $this->input->get_post('id')
                    ));
                    $local_file_patch = $mail_receiver? $mail_receiver->officer_local_path : "";
                    break;
                case "business_license":
                    $mail_receiver = $this->case_usps_business_license_m->get_by_many(array(
                        "case_id" => $case_id,
                        "id" => $this->input->get_post('id')
                    ));
                    $local_file_patch = $mail_receiver? $mail_receiver->business_license_local_file_path : "";
                    break;
            }
        }

        $this->view_verification_file($local_file_patch);
    }

    /**
     * get_file_name from object.
     *
     * @param unknown $object
     * @param unknown $prop
     */
    public function get_file_name($object, $prop)
    {
        $file_name = '';

        if (!empty($object) && !empty($prop) && !empty($object->$prop)) {
            $file_name = end(explode('/', $object->$prop));
        }
        return $file_name;
    }

    public function verification_General_CMRA()
    {
        $case_id = $this->input->get_post('case_id');

        redirect('cases/verification/verification_special_form_PS1583?type=general&case_id=' . $case_id);
    }

    private function view_verification_file($local_file_path)
    {
        if (empty($local_file_path)) {
            echo "Not file";
            return;
        }
        // Does not use layout
        $this->template->set_layout(FALSE);

        // Get extends file
        header('Content-Disposition: inline');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($local_file_path));
        header('Accept-Ranges: bytes');

        $ext = substr($local_file_path, strrpos($local_file_path, '.') + 1);
        $ext = strtolower($ext);
        switch ($ext) {
            case 'jpg':
                header('Content-Type: image/jpeg');
                break;
            case 'jpeg':
                header('Content-Type: image/jpeg');
                break;
            case 'png':
                header('Content-Type: image/png');
                break;
            case 'tiff':
                header('Content-Type: image/tiff');
                break;
            case 'pdf':
                header('Content-Type: application/pdf');
                break;
        }
        
        readfile($local_file_path);
    }

    /**
     * update_status_cases_taskname_instance from 0 to 1.
     *
     * @param unknown_type $case_id
     * @param unknown_type $base_task_name
     */
    private function update_status_cases_taskname_instance($case_id, $base_task_name)
    {
        $this->cases_taskname_instance_m->update_by_many(array(
            'case_id' => $case_id,
            'base_task_name' => $base_task_name
        ), array(
            'status' => '1',
            'updated_date' => now()
        ));
    }
    
    public function upload_special_document(){
        $this->template->set_layout(false);
        
        $input_file_client_name = $this->input->get_post("input_file_client_name");
        $op = $this->input->get_post("op");
        $case_id = $this->input->get_post('case_id');
        $id = $this->input->get_post('id');

        // Check general CMRA case.
        $type = $this->input->get_post('type');
        $general_crma_flag = false;
        $base_taskname = 'verification_special_form_PS1583';
        if ($type == 'general') {
            $general_crma_flag = true;
            $base_taskname = "verification_General_CMRA";
        } else if($type == 'company_verification_E_MS'){
            $base_taskname = "company_verification_E_MS";
        } else if($type == 'california'){
            $base_taskname = 'verification_california_mailbox';
        }
        
        // upload file.
        $result = cases_api::uploadSpecialDocument($id, $case_id, $op,$input_file_client_name, $base_taskname, $general_crma_flag);
        
        if($result['status']){
            $this->success_output('', $result);
        }else{
            $this->error_output($result['message'], $result);
        }
        return;
    }
    
    /**
     * tc contract milestone.
     * @return type
     */
    public function TC_contract_MS(){
        $case_id = $this->input->get_post("case_id", '');
        $base_taskname = "TC_contract_MS";

        // Check exist this case
        $case_exist = $this->cases_m->get_many($case_id);
        if (empty($case_exist)) {
            redirect('cases/verification');
        }
        
        $customer_id = APContext::getCustomerByCase($case_id);
        
        $case_check = $this->cases_contract_m->get_contract_by($case_id);
        $case_resource = $this->case_resource_m->get_by_many(array(
            "case_id" => $case_id,
            "base_taskname" => $base_taskname
        ));
        
        // get term and condition
        $terms_and_conditions = settings_api::getTermAndCondition();

        // If this is post method
        if ($_POST) {
            $comment_for_registration_content = $this->input->post("comment_for_registration_content");
            $comment_for_registration_date = null;
            
            if(!empty($comment_for_registration_content)){
                $comment_for_registration_date = now();
            }
            
            if(empty($case_resource)){
                $this->error_output(lang('request_upload_file_message'));
                return;
            }
            
            if($case_check){
                $this->cases_contract_m->update_by_many(array(
                    "id" => $case_check->id,
                ), array(
                    "status" => 1,
                    "comment_for_registration_content" => $comment_for_registration_content,
                    "comment_for_registration_date" => $comment_for_registration_date,
                    "update_date" => now(),
                    "update_by" => $customer_id
                ));
            }else{
                $this->cases_contract_m->insert(array(
                    "status" => 1,
                    "case_id" => $case_id,
                    "comment_for_registration_content" => $comment_for_registration_content,
                    "comment_for_registration_date" => $comment_for_registration_date,
                    "created_date" => now(),
                    "update_by" => $customer_id
                ));
            }
            
            // update status case.
            $this->update_status_cases_taskname_instance($case_id, $base_taskname);
            
            //#1328 add message after verification is submited by customer 
            $this->success_output(language('case_controller_verification_ThanksForSubmitVerificationMess'));
            
            return;
        }

        $this->template->set("case_resource", $case_resource);
        $this->template->set('terms_and_conditions', $terms_and_conditions);
        $this->template->set("case_id", $case_id);
        $this->template->set("case_check", $case_check);
        $this->template->build('verification/tc_contract');
    }
    
    /**
     * proof address.
     * @return type
     */
    public function proof_of_address_MS(){
        $case_id = $this->input->get_post("case_id", '');
        $base_taskname = "proof_of_address_MS";
        
        // Check exist this case
        $case_exist = $this->cases_m->get_many($case_id);
        if (empty($case_exist)) {
            redirect('cases/verification');
        }
        
        $customer_id = APContext::getCustomerByCase($case_id);

        $case_check = $this->cases_proof_business_m->get_by('case_id', $case_id);
        $case_resource = $this->case_resource_m->get_by_many(array(
            "case_id" => $case_id,
            "base_taskname" => $base_taskname
        ));
        
        // get customer address
        $customer_addresses = addresses_api::getCustomerAddress($customer_id);

        // If this is post method
        if ($_POST) {
            $comment_for_registration_content = $this->input->post("comment_for_registration_content");
            if(!empty($comment_for_registration_content)){
                $comment_for_registration_date = now();
            }
            
            if(empty($case_resource)){
                $this->error_output(lang('request_upload_file_message'));
                return;
            }
            
            if($case_check){
                $this->cases_proof_business_m->update_by_many(array(
                    "id" => $case_check->id,
                ), array(
                    "status" => 1,
                    "updated_date" => now(),
                    "comment_for_registration_content" => $comment_for_registration_content,
                    "comment_for_registration_date" => $comment_for_registration_date,
                    "update_by" => $customer_id
                ));
            }else{
                $this->cases_proof_business_m->insert(array(
                    "status" => 1,
                    "case_id" => $case_id,
                    "created_date" => now(),
                    "update_by" => $customer_id,
                    "comment_for_registration_content" => $comment_for_registration_content,
                    "comment_for_registration_date" => $comment_for_registration_date,
                ));
            }
            
            // update status case.
            $this->update_status_cases_taskname_instance($case_id, $base_taskname);
            $this->success_output("");
            return;
        }

        $this->template->set("case_resource", $case_resource);
        $this->template->set('customer_addresses', $customer_addresses);
        $this->template->set("case_id", $case_id);
        $this->template->set("case_check", $case_check);
        $this->template->build('verification/proof_address');
    }
    
    /**
     * proof address.
     * @return type
     */
    public function company_verification_E_MS(){
        $case_id = $this->input->get_post("case_id", '');
        $base_taskname = "company_verification_E_MS";
        
        // Check exist this case
        $case_exist = $this->cases_m->get_many($case_id);
        if (empty($case_exist)) {
            redirect('cases/verification');
        }
        
        $customer_id = APContext::getCustomerByCase($case_id);
        
        /**
         * seq_number:
         * 01: business license
         * 02: officers of company
         */
        $case_check = $this->cases_company_ems_m->get_by('case_id', $case_id);
        $case_resource = $this->case_resource_m->get_by_many(array(
            "case_id" => $case_id,
            "base_taskname" => $base_taskname,
            "seq_number" => "01"
        ));
        
        $mailReceivers = $this->case_resource_m->get_many_by_many(array(
            "case_id" => $case_id,
            "base_taskname" => $base_taskname,
            "seq_number" => "02"
        ));
        
        // Gets officer owner.
        $officers = $this->case_usps_officer_m->get_many_by_many(array(
            "case_id" => $case_id
        ));
        $this->template->set("officers", $officers);
        
        // get customer address
        $postbox = $this->postbox_m->get_by('postbox_id', $case_exist[0]->postbox_id);

        // If this is post method
        if ($_POST) {
            $description = $this->input->post("description");
            $comment_for_registration_content = $this->input->post("comment_for_registration_content");
            $error_message = "";
            
            if(trim($description) == ""){
                $error_message .= language('case_controller_verification_RequirePostboxDescriptionMess')."<br/>";
            }else if(strlen($description) < 50){
                $error_message .= language('case_controller_verification_Require50CharactersPostboxDescriptionMess')."<br/>";
            }
            
            if(empty($case_resource)){
                $error_message .= language('case_controller_verification_RequireUploadBusinessDocumentMess')."<br/>";
            }
            
            // update officer
            if(!CasesValidator::_validate_officer_owner(true)){
                $error_message .= language('case_controller_verification_RequireCorrectBusinessOwnerInformationMess')."";
            }

            if(!empty($error_message)){
                $this->error_output("", array("message" =>$error_message));
                return;
            }
            
            $comment_for_registration_date = null;
            if(!empty($comment_for_registration_content)){
                $comment_for_registration_date = now();
            }
            
            // get mail receiver
            $mail_receiver_name = $this->input->get_post("mail_receiver_name");
            $mail_receiver_ids = $this->input->get_post("mail_receiver_id");
            
            // get business license, mail receiver, officer.
            $officer_file_ids = $this->input->get_post("officer_file_id");
            $officer_names = $this->input->get_post("officer_name");
            $officer_rates = $this->input->get_post("officer_rate");
            
            // save ems verification
            verification_api::save_company_ems($customer_id, $case_id, $description, $comment_for_registration_content, 
                    $comment_for_registration_date, $mail_receiver_name, $mail_receiver_ids, $officer_file_ids, $officer_names, $officer_rates);
        
            // update status case.
            $this->update_status_cases_taskname_instance($case_id, $base_taskname);
            
            //#1328 add message after verification is submited by customer 
            $this->success_output(language('case_controller_verification_ThanksForSubmitVerificationMess'));

            return;
        }

        $this->template->set("officers", $officers);
        $this->template->set("case_resource", $case_resource);
        $this->template->set("mailReceivers", $mailReceivers);
        $this->template->set('postbox', $postbox);
        $this->template->set("case_id", $case_id);
        $this->template->set("case_check", $case_check);
        $this->template->build('verification/company_ems');
    }
    
    public function verification_california_mailbox() {
        $case_id = $this->input->get_post('case_id');

        redirect('cases/verification/verification_special_form_PS1583?type=california&case_id=' . $case_id);
    }
    
    /**
     * create pdf resouce.
     */
    public function create_resource_pdf(){
        $this->template->set_layout(false);
        
        $case_id = $this->input->get_post('case_id');
        $base_taskname = $this->input->get_post('base_taskname');
        $customer_id = APContext::getCustomerByCase($case_id);
        $customer = APContext::getCustomerByID($customer_id);
        
        verification_api::create_term_condition_pdf($customer);
        $this->success_output('');
    }
    
    /**
     * upload resource file.
     * @return type
     */
    public function upload_resource(){
        $this->template->set_layout(false);
        
        $case_id = $this->input->get_post("case_id");
        $file_id = $this->input->get_post("id");
        $base_taskname = $this->input->get_post('base_taskname');
        $input_file_client_name = $this->input->get_post("input_file_client_name");
        $seq_number = $this->input->get_post('seq_number', "");
        
        $customer_id = APContext::getCustomerByCase($case_id);
        $customer = APContext::getCustomerByID($customer_id);
        
        $result = cases_api::uploadResourceDocument($customer, $case_id, $base_taskname, $input_file_client_name, $seq_number, $file_id);
        if($result['message']){
            $this->error_output($result['message'], array("file_id"=> $result['id']));
        }else{
            $this->success_output("", array("file_id"=> $result['id']));
        }
        return;        
    }
    
    
     /**
     * #1189 New Function: need to add a "remove" icon  
     * delete resource file.
     * @return type
     */
    public function delete_resource(){
        $this->template->set_layout(false);
        
        $case_id = $this->input->get_post("case_id");
        $file_id = $this->input->get_post("id");
        $op = $this->input->get_post("op");
        
        $result = cases_api::deleteResourceDocument($file_id, $case_id, $op);
        if($result){
            $this->success_output("");
            return true;
        }else{
            $this->error_output('Invalid request');
            return false;
        }     
    }
    
     /**
     * #1189 New Function: need to add a "remove" icon  
     * delete specical resource file.
     * @return type
     */
    public function delete_specical_resource(){
        $this->template->set_layout(false);
        
        $case_id = $this->input->get_post("case_id");
        $id = $this->input->get_post("id");
        $op = $this->input->get_post("op");
        
        $result = cases_api::deleteUSPSSpecialDocument($id, $case_id, $op);
        if($result){
            $this->success_output("");
            return true;
        }else{
            $this->error_output('Invalid request');
            return false;
        }     
    }
    
    /**
     * view resourse file
     * @return type
     */
    public function view_resource(){
        $this->template->set_layout(false);
        $file_id = $this->input->get_post("file_id");
        $case_resource = $this->case_resource_m->get($file_id);

        if($case_resource){
            $this->view_verification_file($case_resource->local_file_path);
        }else{
            echo "not file";
        }
        return;
    }
    
    /**
     * verificatoin of personal form
     */
    public function phone_number_company(){
        $this->phone_verification("2", "phone_number_company");
    }
    
    /**
     * verificatoin of company form
     */
    public function phone_number_for_personal(){
        $this->phone_verification("1", "phone_number_for_personal");
    }
    
    /**
     * verification phone number.
     * @return type
     */
    private function phone_verification($type, $base_taskname){
        $case_id = $this->input->get_post("case_id", '');

        // Check exist this case
        $case_exist = $this->cases_m->get($case_id);
        if (empty($case_exist)) {
            redirect('cases/verification');
        }
        
        $customer_id = APContext::getCustomerByCase($case_id);
        
        // get file documents
        $personal_identification_name = $this->case_resource_m->get_many_by_many(array(
            "case_id" => $case_id,
            "base_taskname" => $base_taskname,
            "seq_number" => "01"
        ));
        $business_licenses = $this->case_resource_m->get_many_by_many(array(
            "case_id" => $case_id,
            "base_taskname" => $base_taskname,
            "seq_number" => "02"
        ));

        // If this is post method
        if ($_POST) {
            if(($type == "1" && empty($personal_identification_name)) 
                    || ($type == "2" && (empty($personal_identification_name) || empty($business_licenses)))){
                $this->error_output(lang('request_upload_file_message'));
                return;
            }
            
            //  update case phone number status.
            verification_api::update_case_phone_status($customer_id, $case_id, $type, 1);
            
            // update status case.
            $this->update_status_cases_taskname_instance($case_id, $base_taskname);
            $this->success_output("");
            return;
        }

        $this->template->set("personal_identification_name", $personal_identification_name);
        $this->template->set("business_licenses", $business_licenses);
        $this->template->set("type", $type);
        $this->template->set("case_id", $case_id);
        $this->template->set("base_taskname", $base_taskname);
        $this->template->build('verification/phone_company_personal_verifiy');
    }
}
