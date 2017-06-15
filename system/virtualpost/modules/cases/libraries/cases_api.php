<?php defined('BASEPATH') or exit('No direct script access allowed');

class cases_api {
    /**
     * get comment content of case.
     *
     * @param unknown $caseId
     * @param unknown $baseTaskname
     * @return string
     */
    public static function getCommentOfCaseBy($caseId, $baseTaskname)
    {
        ci()->load->model(array(
            "cases/cases_verification_company_hard_m",
            "cases/cases_verification_personal_identity_m",
            "cases/cases_verification_usps_m",
            "cases/cases_company_ems_m",
            "cases/cases_proof_business_m",
            "cases/cases_contract_m",
            "cases/case_phone_number_m",
        ));

        $comment = "";
        $data = "";
        if ($baseTaskname == 'verification_personal_identification') {
            $data = ci()->cases_verification_personal_identity_m->get_by_many(array(
                "case_id" => $caseId,
                "type" => 1,
            ));
        } else if ($baseTaskname == 'verification_company_identification_soft') {
            $data = ci()->cases_verification_personal_identity_m->get_by_many(array(
                "case_id" => $caseId,
                "type" => 2,
            ));
        } else if ($baseTaskname == 'verification_company_identification_hard') {
            $data = ci()->cases_verification_company_hard_m->get_by_many(array(
                "case_id" => $caseId
            ));
        } else if ($baseTaskname == 'verification_General_CMRA' 
                || $baseTaskname == 'verification_special_form_PS1583'
                || $baseTaskname == 'verification_california_mailbox') {
            $data = ci()->cases_verification_usps_m->get_by_many(array(
                "case_id" => $caseId
            ));
        } else if ($baseTaskname == 'proof_of_address_MS') {
            $data = ci()->cases_proof_business_m->get_by_many(array(
                "case_id" => $caseId
            ));
        } else if ($baseTaskname == 'company_verification_E_MS') {
            $data = ci()->cases_company_ems_m->get_by_many(array(
                "case_id" => $caseId
            ));
        } else if ($baseTaskname == 'TC_contract_MS') {
            $data = ci()->cases_contract_m->get_by_many(array(
                "case_id" => $caseId
            ));
        } else if ($baseTaskname == 'phone_number_company' || $baseTaskname == 'phone_number_for_personal'){
            $type = "1";
            if($baseTaskname == 'phone_number_company'){
                $type = "2";
            }
            
            $data = ci()->case_phone_number_m->get_by_many(array(
                "case_id" => $caseId,
                "type" => $type
            ));
        }
        
        if ($data) {
            $comment = $data->comment_content;
        }

        return $comment;
    }
    
    /**
     * get comment date of case.
     *
     * @param unknown $caseId
     * @param unknown $baseTaskname
     * @return string
     */
    public static function getCommentDateOfCaseBy($caseId, $baseTaskname)
    {
    	ci()->load->model(array(
            "cases/cases_verification_company_hard_m",
            "cases/cases_verification_personal_identity_m",
            "cases/cases_verification_usps_m",
            "cases/cases_proof_business_m",
            "cases/cases_contract_m",
            "cases/case_phone_number_m",
    	));
    
    	$date = "";
    	$data = "";
    	if ($baseTaskname == 'verification_personal_identification') {
    		$data = ci()->cases_verification_personal_identity_m->get_by_many(array(
    				"case_id" => $caseId,
    				"type" => 1,
    		));
    	} else if ($baseTaskname == 'verification_company_identification_soft') {
    		$data = ci()->cases_verification_personal_identity_m->get_by_many(array(
    				"case_id" => $caseId,
    				"type" => 2,
    		));
    	} else if ($baseTaskname == 'verification_company_identification_hard') {
    		$data = ci()->cases_verification_company_hard_m->get_by_many(array(
    				"case_id" => $caseId
    		));
    	} else if ($baseTaskname == 'verification_General_CMRA' 
                || $baseTaskname == 'verification_special_form_PS1583'
                || $baseTaskname == 'verification_california_mailbox') {
    		$data = ci()->cases_verification_usps_m->get_by_many(array(
    				"case_id" => $caseId
    		));
    	} else if ($baseTaskname == 'proof_of_address_MS') {
            $data = ci()->cases_proof_business_m->get_by_many(array(
                "case_id" => $caseId
            ));
        } else if ($baseTaskname == 'company_verification_E_MS') {
            $data = ci()->cases_company_ems_m->get_by_many(array(
                "case_id" => $caseId
            ));
        } else if ($baseTaskname == 'TC_contract_MS') {
            $data = ci()->cases_contract_m->get_by_many(array(
                "case_id" => $caseId
            ));
        } else if ($baseTaskname == 'phone_number_company' || $baseTaskname == 'phone_number_for_personal'){
            $type = "1";
            if($baseTaskname == 'phone_number_company'){
                $type = "2";
            }
            
            $data = ci()->case_phone_number_m->get_by_many(array(
                "case_id" => $caseId,
                "type" => $type
            ));
        }
    	if ($data) {
    		$date = $data->comment_date;
    	}
    
    	return $date;
    }

    public static function getCasesPaging($condition, $input)
    {
        // Model
        ci()->load->model(array('cases_m'));

        $result = ci()->cases_m->get_admin_cases_paging($condition, $input['start'], $input['limit'], $input['sort_column'], $input['sort_type']);

        return $result;
    }

    public static function getCaseVerification($case_id)
    {
        ci()->load->model('cases/cases_m');

        $case_exist = ci()->cases_m->get_many($case_id);

        return $case_exist;
    }

    public static function getCaseVerificationUSPS($case_id)
    {
        ci()->load->model('cases/cases_verification_usps_m');

        $cases_verification_usps = ci()->cases_verification_usps_m->get_by_many(array(
            'case_id' => $case_id
        ));

        return $cases_verification_usps;
    }

    public static function updateCaseVerificationUSPS($case_id, $update_data)
    {
        ci()->load->model('cases/cases_verification_usps_m');

        $updateResult = ci()->cases_verification_usps_m->update_by_many(array(
            'case_id' => $case_id
        ), $update_data);

        return $updateResult;
    }

    public static function createCaseVerificationUSPS($update_data)
    {
        ci()->load->model('cases/cases_verification_usps_m');

        $idCaseUSPS = ci()->cases_verification_usps_m->insert($update_data);

        return $idCaseUSPS;

    }

    public static function getListTaskName($array_condition, $start, $limit, $sort_column, $sort_type)
    {
        ci()->load->model('cases/cases_taskname_instance_m');

        $query_result = ci()->cases_taskname_instance_m->get_tasklist_paging_for_admin($array_condition, $start, $limit, $sort_column, $sort_type);

        return $query_result;
    }

    /**
     * Gets company hard verification case.
     * @param type $caseId
     * @param type $type
     * @return type
     */
    public static function getPersonalOrCompanyVerificationCase($caseId, $type)
    {
        ci()->load->model("cases/cases_verification_personal_identity_m");

        $cases = ci()->cases_verification_personal_identity_m->get_by_many(array(
            "case_id" => $caseId,
            "type" => $type
        ));

        return $cases;
    }

    /**
     * Gets company hard verfication case.
     * @param type $caseId
     * @param type $type
     * @return type
     */
    public static function getCompanyHardVerificationCase($caseId)
    {
        ci()->load->model("cases/cases_verification_company_hard_m");

        $cases = ci()->cases_verification_company_hard_m->get_by_many(array(
            "case_id" => $caseId,
        ));

        return $cases;
    }

    /**
     * Gets usps verification case.
     * @param type $caseId
     * @return type
     */
    public static function getUSPSFormVerificationCase($caseId)
    {
        ci()->load->model("cases/cases_verification_usps_m");

        $cases = ci()->cases_verification_usps_m->get_by_many(array(
            "case_id" => $caseId,
        ));

        return $cases;
    }
    
    /**
     * upload 
     * @param type $case_id
     * @param type $input_file_client_name
     * @param type $server_filename
     * @return type
     */
    public static function upload_file_case_verification($case_id, $input_file_client_name, $server_filename = ''){
        ci()->load->library('files/files');
        ci()->load->library('S3');
        
        // get customer info.
        $customer_id = APContext::getCustomerByCase($case_id);
        $customer = APContext::getCustomerByID($customer_id);
        $customer_code = $customer->customer_code;
        $case_code = sprintf('%1$08d', $case_id);

        // Upload file to local
        $upload_info = Files::upload_case_document_for_ajax($case_id, $customer_code, $input_file_client_name, $server_filename);
        if ($upload_info['status']) {
           
            // Upload file to S3
            $default_bucket_name = ci()->config->item('default_bucket');
            $ext_file = explode('.', $upload_info['local_file_path']);
            $amazon_relate_path = $upload_info['amazon_file_path'] = $customer_id . '/cases/' . $case_code . '/' . $server_filename . '.' . $ext_file[1];

            log_message(APConstants::LOG_DEBUG, "Upload file to Amazon: " . $upload_info['local_file_path']);
            $upload_info['status'] = S3::putObjectFile($upload_info['local_file_path'], $default_bucket_name, $amazon_relate_path, S3::ACL_PRIVATE);

            if(!$upload_info['status']){
                $upload_info['message'] = 'Can not upload file to S3. Please try it again.';
                log_audit_message(APConstants::LOG_INFOR, "ERROR S3 upload file: customer_id: ".$customer_id.", local file: ". $upload_info['local_file_path'].', server_filename: '.$server_filename.", amazon_relate_path: ".$amazon_relate_path, FALSE, 'upload_file_case_verification');
            }
        }else{
            $upload_info['message'] = lang('upload_file_fail_message');
        }

        return $upload_info;
    }
    
    public static function create_usps_pdf_document($data, $base_taskname = '', $customer){
        // Load pdf library
        ci()->load->library('pdf');

        // create new PDF document
        $pdfObj = ci()->pdf->createObject();

        $pdfObj->setFontSubsetting(true);
        $pdfObj->SetFont('freeserif', '', 8, '', 'false');

        // set document information
        // Set common information
        $pdfObj->SetTitle(Settings::get(APConstants::INSTANCE_OWNER_COMPANY_CODE));
        $pdfObj->SetAuthor(Settings::get(APConstants::INSTANCE_OWNER_COMPANY_CODE));

        // disable header and footer
        $pdfObj->setPrintHeader(false);
        $pdfObj->setPrintFooter(false);

        // set auto page breaks
        $pdfObj->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        // set margins
        $pdfObj->SetMargins(PDF_MARGIN_LEFT, 15, PDF_MARGIN_RIGHT, TRUE);
        $pdfObj->SetHeaderMargin(15);
        $pdfObj->SetFooterMargin(15);

        if ($base_taskname == 'verification_General_CMRA') {
            $html = ci()->load->view("verification/template_general_cmra", $data, TRUE);
        } else if($base_taskname == 'verification_california_mailbox'){
            $html = ci()->load->view("verification/template_california_mailbox", $data, TRUE);
        }else {
            $html = ci()->load->view("verification/template", $data, TRUE);
        }
        // $html .= '<style>' . file_get_contents(APPPATH . 'modules/cases/views/verification/stylesheet.css') . '</style>';

        $pdfObj->AddPage();
        // $pdf->Ln ( 30 );
        $pdfObj->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, 'J', true);
        // $pdf->writeHTML($html, true, false, true, false, '');

        // Check and create path store file
        $invoice_file_path = Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'downloads/verification/';
        if (!is_dir(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'downloads/')) {
            mkdir(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'downloads/');
            chmod(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'downloads/', 0777);
        }
        if (!is_dir(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'downloads/verification/')) {
            mkdir(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'downloads/verification/');
            chmod(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'downloads/verification/', 0777);
        }
        
        if ($base_taskname == 'verification_General_CMRA') {
            $invoice_file_path .= $customer->customer_code . '_general_cmra.pdf';
        } else if($base_taskname == 'verification_california_mailbox'){
            $invoice_file_path .= $customer->customer_code . '_california_mailbox.pdf';
        } else {
            $invoice_file_path .= $customer->customer_code . '_USPS_FORM_1583.pdf';
        }
        $pdfObj->Output($invoice_file_path, 'F');
        return $invoice_file_path;
    }
    
    /**
     * upload special usps document.
     */
    public static function uploadSpecialDocument($id, $case_id, $op,$input_file_client_name, $base_taskname, $general_crma_flag){
        ci()->load->model(array(
            "cases/case_usps_mail_receiver_m",
            "cases/case_usps_officer_m",
            "cases/case_usps_business_license_m",
        ));
        
        $result = array(
            "status" => true,
            "response_id" => $id,
            "response_file" => "",
            "message" => ""
        );
        
        switch($op){
            case 'mail_receiver':
                $running_number = '001';
                $mail_receiver_check = ci()->case_usps_mail_receiver_m->order_by("id", "desc")->get_by("case_id",$case_id);
                if($mail_receiver_check){
                    $running_number = CaseUtils::getRunningNumberBy($mail_receiver_check->receiver_local_path);
                }
                $server_filename = CaseUtils::getUploadFileNameBy($base_taskname, '06', $running_number, $case_id);
                
                // upload file
                $upload_info = self::upload_file_case_verification($case_id, $input_file_client_name, $server_filename);
                
                if (!$upload_info['status']) {
                    $result['status']  = $upload_info['status'];
                    $result['message']  = $upload_info['message'];
                    return $result;
                }
                
                // update file.
                if($id){
                    ci()->case_usps_mail_receiver_m->update_by_many(array(
                        "id" => $id,
                    ), array(
                        "receiver_local_path" => $upload_info['local_file_path'],
                        "receiver_amazon_path" => $upload_info['amazon_file_path'],
                        "base_taskname" => $base_taskname,
                        "updated_date" => now()
                    ));
                }else{
                    $result['response_id'] = ci()->case_usps_mail_receiver_m->insert(array(
                        "receiver_local_path" => $upload_info['local_file_path'],
                        "receiver_amazon_path" => $upload_info['amazon_file_path'],
                        "case_id" => $case_id,
                        "base_taskname" => $base_taskname,
                        "created_date" => now()
                    ));
                }
                break;
            case 'officer_onwer':
                $running_number = '001';
                $case_check = ci()->case_usps_officer_m->order_by("id", "desc")->get_by("case_id",$case_id);
                if($case_check){
                    $running_number = CaseUtils::getRunningNumberBy($case_check->officer_local_path);
                }
                $server_filename = CaseUtils::getUploadFileNameBy($base_taskname, '07', $running_number, $case_id);
                
                // upload file
                $upload_info = self::upload_file_case_verification($case_id, $input_file_client_name, $server_filename);
                if (!$upload_info['status']) {
                    $result['status']  = $upload_info['status'];
                    $result['message']  = $upload_info['message'];
                    return $result;
                }
                
                // update file.
                if($id){
                    ci()->case_usps_officer_m->update_by_many(array(
                        "id" => $id,
                    ), array(
                        "officer_local_path" => $upload_info['local_file_path'],
                        "officer_amazon_path" => $upload_info['amazon_file_path'],
                        "base_taskname" => $base_taskname,
                        "updated_date" => now()
                    ));
                }else{
                    $result['response_id'] = ci()->case_usps_officer_m->insert(array(
                        "officer_local_path" => $upload_info['local_file_path'],
                        "officer_amazon_path" => $upload_info['amazon_file_path'],
                        "base_taskname" => $base_taskname,
                        "case_id" => $case_id,
                        "created_date" => now()
                    ));
                }
                break;
            case "business_license":
                $running_number = '001';
                $case_check = ci()->case_usps_business_license_m->order_by("id", "desc")->get_by("case_id", $case_id);
                if($case_check){
                    $running_number = CaseUtils::getRunningNumberBy($case_check->business_license_local_file_path);
                }
                $server_filename = CaseUtils::getUploadFileNameBy($base_taskname, '07', $running_number, $case_id);
                
                // upload file
                $upload_info = self::upload_file_case_verification($case_id, $input_file_client_name, $server_filename);
                if (!$upload_info['status']) {
                    $result['status']  = $upload_info['status'];
                    $result['message']  = $upload_info['message'];
                    return $result;
                }
                
                // update file.
                if($id){
                    ci()->case_usps_business_license_m->update_by_many(array(
                        "id" => $id,
                    ), array(
                        "business_license_local_file_path" => $upload_info['local_file_path'],
                        "business_license_amazon_file_path" => $upload_info['amazon_file_path'],
                        "base_taskname" => $base_taskname,
                        "updated_date" => now()
                    ));
                }else{
                    $result['response_id'] = ci()->case_usps_business_license_m->insert(array(
                        "business_license_local_file_path" => $upload_info['local_file_path'],
                        "business_license_amazon_file_path" => $upload_info['amazon_file_path'],
                        "base_taskname" => $base_taskname,
                        "case_id" => $case_id,
                        "created_date" => now()
                    ));
                }
                break;
        }
        
        $result['response_file'] = basename($upload_info['local_file_path']);
        return $result;
    }
    
    /**
     * upload resource file
     * @param type $customer
     * @param type $case_id
     * @param type $input_file_client_name
     * @param type $server_filename
     * @return type
     */
    public static function uploadResourceDocument($customer, $case_id, $base_taskname, $input_file_client_name, $seq_number, $file_id=''){
        ci()->load->model(array(
            "cases/case_resource_m",
            "cases/cases_contract_m"
        ));
        
        $case_check = ci()->case_resource_m->order_by("id", "desc")->get_by_many(array(
            "case_id" => $case_id,
            "base_taskname" => $base_taskname
        ));

        $running_number = '001';
        if($case_check){
            $running_number = CaseUtils::getRunningNumberBy($case_check->local_file_path);
        }
        
        $server_filename = $customer->customer_code.'_'.  strtolower($base_taskname)."_";
        if(empty($seq_number)){
            $server_filename .= $running_number;
        }else{
            $server_filename .= $seq_number."_".$running_number;
        }

        // upload file
        $upload_info = self::upload_file_case_verification($case_id, $input_file_client_name, $server_filename);

        $id = "";
        $message = "";
        if ($upload_info['status']) {
            $case_resource = "";
            if($file_id){
                $case_resource = ci()->case_resource_m->get($file_id);
            }
            if($case_resource){
                $id = $case_resource->id;
                ci()->case_resource_m->update_by_many(array(
                    "id" => $file_id
                ), array(
                    "local_file_path" => $upload_info['local_file_path'],
                    "amazon_file_path" => $upload_info['amazon_file_path'],
                    "updated_date" => now()
                ));
            }else{
                $id = ci()->case_resource_m->insert(array(
                    "local_file_path" => $upload_info['local_file_path'],
                    "amazon_file_path" => $upload_info['amazon_file_path'],
                    "case_id" => $case_id,
                    "base_taskname" => $base_taskname,
                    "seq_number" => $seq_number,
                    'created_date' => now()
                ));
            }
        }else{
            $message = $upload_info['message'];
        }
        
        return array(
            "status" => true,
            "file_path" => $upload_info['local_file_path'],
            "id" => $id,
            "message" => $message
        );
    }
    
    /**
     * #1189 New Function: need to add a "remove" icon 
     * Delete resource file
     * @param type $file_id
     * @param type $case_id
     * @return booleanlete resource document.
     */
    public static function deleteResourceDocument($file_id, $case_id, $op){
        // Load library S3 
        ci()->load->library('S3');
        
        // load model 
        ci()->load->model( array(
            "cases/case_resource_m",
            "cases/case_usps_officer_m",
            "cases/cases_verification_personal_identity_m"
        ));
        
        $default_bucket_name = ci()->config->item('default_bucket');
        
        $local_file = "";
        $amazon_file = "";
        $case_check = "";
       
        switch($op){
            case 'delete_officer': 
                $case_check = ci()->case_resource_m->get_by_many(array(
                    "case_id" => $case_id,
                    "id" => $file_id
                ));

                // Check case 
                if($case_check){
                    //  $local_file and amazon file
                   $local_file = $case_check->local_file_path;
                   $amazon_file = $case_check->amazon_file_path;
                   
                    ci()->case_resource_m->delete_by_many(array(
                         "case_id" => $case_id,
                         "id" => $file_id
                    ));
                }
                break;
            case 'delete_onwer':
                $case_check = ci()->case_usps_officer_m->get_by_many(array(
                    "case_id" => $case_id,
                    "id" => $file_id
                ));
 
               // Check case 
                if($case_check){
                   //  $local_file and amazon file
                   $local_file = $case_check->officer_local_path;
                   $amazon_file = $case_check->officer_amazon_path;
                   
                    ci()->case_usps_officer_m->delete_by_many(array(
                        "case_id" => $case_id,
                        "id" => $file_id
                    ));
                }
                break;  
            case 'delete_business_document':
               $case_check = ci()->case_resource_m->get_by_many(array(
                    "case_id" => $case_id,
                    "id" => $file_id
                ));

                // Check case 
                if($case_check){
                     //  $local_file and amazon file
                   $local_file = $case_check->local_file_path;
                   $amazon_file = $case_check->amazon_file_path;
                   
                    ci()->case_resource_m->delete_by_many(array(
                         "case_id" => $case_id,
                         "id" => $file_id
                    ));
                }
                break;   
            case 'delete_person_passport_verification':
                $case_check = ci()->cases_verification_personal_identity_m->get_by_many(array(
                    "case_id" => $case_id,
                    "id" => $file_id
                ));
                
                // Check case 
                if($case_check){
                     //  $local_file and amazon file
                   $local_file = $case_check->verification_local_file_path;
                   $amazon_file = $case_check->verification_amazon_file_path;
                   
                    ci()->cases_verification_personal_identity_m->update_by_many(array(
                         "case_id" => $case_id,
                         "id" => $file_id
                    ), array(
                         'verification_local_file_path' => '',
                         'verification_amazon_file_path' => ''
                    ));
                }
                break;
            case 'delete_company_identification_soft':
                $case_check = ci()->cases_verification_personal_identity_m->get_by_many(array(
                    "case_id" => $case_id,
                    "id" => $file_id
                ));
                
                // Check case 
                if($case_check){
                     //  $local_file and amazon file
                   $local_file = $case_check->verification_local_file_path;
                   $amazon_file = $case_check->verification_amazon_file_path;
                   
                    ci()->cases_verification_personal_identity_m->update_by_many(array(
                         "case_id" => $case_id,
                         "id" => $file_id
                    ), array(
                         'verification_local_file_path' => '',
                         'verification_amazon_file_path' => ''
                    ));
                }
                break;
            case 'delete_person_driver_license_verification':
                $case_check = ci()->cases_verification_personal_identity_m->get_by_many(array(
                    "case_id" => $case_id,
                    "id" => $file_id
                ));
                
                // Check case 
                if($case_check){
                     //  $local_file and amazon file
                   $local_file = $case_check->driver_license_document_local_file_path;
                   $amazon_file = $case_check->driver_license_document_amazon_file_path;
                   
                    ci()->cases_verification_personal_identity_m->update_by_many(array(
                         "case_id" => $case_id,
                         "id" => $file_id
                    ), array(
                         'driver_license_document_local_file_path' => '',
                         'driver_license_document_amazon_file_path' => ''
                    ));
                }
                break;
            case 'proof_of_address_MS':
                $case_check = ci()->case_resource_m->get_by_many(array(
                    "case_id" => $case_id,
                    "id" => $file_id
                ));

                // Check case 
                if($case_check){
                     //  $local_file and amazon file
                   $local_file = $case_check->local_file_path;
                   $amazon_file = $case_check->amazon_file_path;
                   
                    ci()->case_resource_m->delete_by_many(array(
                         "case_id" => $case_id,
                         "id" => $file_id
                    ));
                }   
                break;
                
            case 'TC_contract_MS':
                $case_check = ci()->case_resource_m->get_by_many(array(
                    "case_id" => $case_id,
                    "id" => $file_id
                ));

                // Check case 
                if($case_check){
                     //  $local_file and amazon file
                   $local_file = $case_check->local_file_path;
                   $amazon_file = $case_check->amazon_file_path;
                   
                    ci()->case_resource_m->delete_by_many(array(
                         "case_id" => $case_id,
                         "id" => $file_id
                    ));
                }   
                break;
            default:
                break;
        }
        
         // Delete additional amazon file
        if ($amazon_file) {
            S3::deleteObject($default_bucket_name, $amazon_file);
        }

        // Delete additional local file
        if ($local_file && file_exists($local_file)) {
            unlink($local_file);
        }
                    
        return true;
        
    }

    /**
     * delete special document file
     * @param type $id
     * @param type $case_id
     * @param type $op
     * @return booleanlete special document.
     */
    public static function deleteUSPSSpecialDocument($id, $case_id, $op){
        ci()->load->library('S3');
        ci()->load->model(array(
            "cases/case_resource_m",
            "cases/case_usps_mail_receiver_m",
            "cases/case_usps_officer_m",
            "cases/case_usps_business_license_m",
            "cases/cases_verification_usps_m"
        ));
        $default_bucket_name = ci()->config->item('default_bucket');
        
        $local_file = "";
        $amazon_file = "";
        switch($op){
            case 'mail_receiver':
                $case_check = ci()->case_resource_m->get_by_many(array(
                    "case_id" => $case_id,
                    "id" => $id
                ));
                if($case_check){
                    // Delete additional amazon file
                    $local_file = $case_check->local_file_path;
                    $amazon_file = $case_check->amazon_file_path;

                    ci()->case_resource_m->delete_by_many(array(
                        "case_id" => $case_id,
                        "id" => $id
                    ));
                }
                break;
            case 'officer_onwer':
                $case_check = ci()->case_usps_officer_m->get_by_many(array(
                    "case_id" => $case_id,
                    "id" => $id
                ));
                if($case_check){
                    $local_file = $case_check->officer_local_path;
                    $amazon_file = $case_check->officer_amazon_path;
                    
                    ci()->case_usps_officer_m->delete_by_many(array(
                        "case_id" => $case_id,
                        "id" => $id
                    ));
                }
                break;
            case 'business_license':
                $case_check = ci()->case_resource_m->get_by_many(array(
                    "case_id" => $case_id,
                    "id" => $id
                ));
                if($case_check){
                    $local_file = $case_check->local_file_path;
                    $amazon_file = $case_check->amazon_file_path;

                    ci()->case_resource_m->delete_by_many(array(
                        "case_id" => $case_id,
                        "id" => $id
                    ));
                }
                break;
            case 'delete_id_of_applicant_verification':
                $case_check = ci()->cases_verification_usps_m->get_by_many(array(
                    "case_id" => $case_id,
                    "id" => $id
                ));
                if($case_check){
                    $local_file = $case_check->id_of_applicant_local_file_path;
                    $amazon_file = $case_check->id_of_applicant_amazon_file_path;

                    ci()->cases_verification_usps_m->update_by_many(array(
                         "case_id" => $case_id,
                         "id" => $id
                    ), array(
                         'id_of_applicant_local_file_path' => '',
                         'id_of_applicant_amazon_file_path' => ''
                    ));
                }
                break;
            case 'delete_license_of_applicant_verification':
                $case_check = ci()->cases_verification_usps_m->get_by_many(array(
                    "case_id" => $case_id,
                    "id" => $id
                ));
                if($case_check){
                    $local_file = $case_check->license_of_applicant_local_file_path;
                    $amazon_file = $case_check->license_of_applicant_amazon_file_path;

                    ci()->cases_verification_usps_m->update_by_many(array(
                         "case_id" => $case_id,
                         "id" => $id
                    ), array(
                         'license_of_applicant_local_file_path' => '',
                         'license_of_applicant_amazon_file_path' => ''
                    ));
                }
                break;
        }
        
        // Delete additional amazon file
        if ($amazon_file) {
            S3::deleteObject($default_bucket_name, $amazon_file);
        }

        // Delete additional local file
        if ($local_file && file_exists($local_file)) {
            unlink($local_file);
        }
                    
        return true;
    }
    
    public static function create_pdf_resource_by($html_content, $filename){
        // Load pdf library
        ci()->load->library('pdf');

        // create new PDF document
        $pdfObj = ci()->pdf->createObject();

        $pdfObj->setFontSubsetting(true);
        $pdfObj->SetFont('freeserif', '', 8, '', 'false');

        // set document information
        $pdfObj->SetTitle(Settings::get(APConstants::INSTANCE_OWNER_COMPANY_CODE));
        $pdfObj->SetAuthor(Settings::get(APConstants::INSTANCE_OWNER_COMPANY_CODE));

        // disable header and footer
        $pdfObj->setPrintHeader(false);
        $pdfObj->setPrintFooter(false);

        // set auto page breaks
        $pdfObj->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        // set margins
        $pdfObj->SetMargins(PDF_MARGIN_LEFT, 15, PDF_MARGIN_RIGHT, TRUE);
        $pdfObj->SetHeaderMargin(15);
        $pdfObj->SetFooterMargin(15);

        $pdfObj->AddPage();
        $pdfObj->writeHTMLCell(0, 0, '', '', $html_content, 0, 1, 0, true, 'J', true);

        $pdfObj->Output($filename, 'F');
        return $filename;
    }
    
    /**
     * Assign postbox id into cases table.
     * @param type $postbox_id
     * @param type $customer_id
     */
    public static function changePostboxIdOfCases($old_postbox_id, $old_customer_id, $new_postbox_id, $new_customer_id){
        ci()->load->model(array('cases_m'));
        
        $result = ci()->cases_m->update_by_many(array(
            "target_type" => APConstants::CASE_PRODUCT_TYPE_POSTBOX,
            "postbox_id" => $old_postbox_id,
            "customer_id" => $old_customer_id
        ), array(
            "postbox_id" => $new_postbox_id,
            "customer_id" => $new_customer_id
        ));
        
        return  $result;
    }
}
