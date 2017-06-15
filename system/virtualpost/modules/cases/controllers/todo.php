<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Admin controller for the users module
 */
class Todo extends Admin_Controller
{
    
    public function __construct()
    {
        parent::__construct();

        // Model
        $this->load->model(array(
            'cases_m',
            'settings/countries_m',
            "cases_product_m",
            "cases_milestone_m",
            "cases_milestone_instance_m",
            "cases_taskname_m",
            "cases_taskname_instance_m",
            "cases/cases_additional_information_m",
            "cases/cases_company_information_m",
            "cases/cases_personal_identity_m",
            "cases/cases_registration_document_m",
            "cases/cases_taskname_instance_m",
            "cases_verification_personal_identity_m",
            "cases_verification_usps_m",
            "cases_verification_company_hard_m",
            'mailbox/postbox_m',
            'addresses/location_m',
            "addresses/customers_address_m",
            "addresses/customers_address_hist_m",
            'customers/customer_m',
            'payment/payment_m',
            'mailbox/postbox_m',
            "case_usps_mail_receiver_m",
            "case_usps_officer_m",
            "case_usps_business_license_m",
            "cases_contract_m",
            "case_resource_m",
            "cases_proof_business_m",
            "cases_company_ems_m",
            "cases_verification_history_m",
            "cases/case_phone_number_m",
        ));

        $this->lang->load('cases');

        $this->load->library(array(
            "cases_todo_api",
            "settings/settings_api"
        ));
        
        $this->load->library(
            'form_validation',
            'S3'
        );
        
    }

    /**
     * Display all case in the system
     */
    public function index()
    {
        $status = $this->input->get_post('status');

        // Build filter condition
        $array_condition = array();
        if (!empty($status)) {
            $array_condition['cases.status'] = $status;
        }

        // Only filter task of this user login
        if (APContext::isServiceParner() || APContext::isAdminLocation()) {
            $array_condition['cases_milestone.partner_id'] = APContext::getParnerIDLoggedIn();
        }

        if ($this->is_ajax_request() || false) {
            // update limit into user_paging.
            $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);

            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging['limit'] = $limit;

            // Call search method
            $query_result = $this->cases_m->get_cases_paging($array_condition, $input_paging['start'], $input_paging['limit'], $input_paging['sort_column'], $input_paging['sort_type']);

            // Process output data
            $total = $query_result['total'];
            $datas = $query_result['data'];

            // Get output response
            $response = $this->get_paging_output($total, $input_paging['limit'], $input_paging['page']);

            $i = 0;
            foreach ($datas as $row) {
                $response->rows[$i]['id'] = $row->id;
                $response->rows[$i]['cell'] = array(
                    $row->id,
                    APUtils::convert_timestamp_to_date($row->opening_date),
                    $row->case_identifier,
                    $row->description,
                    $row->product_name,
                    $row->country_name,
                    $row->status == 1 ? "Pending" : (($row->status == 2) ? "Completed" : "Open"),
                    $row->id
                );
                $i++;
            }
            echo json_encode($response);
        } else {
            // list all
            $this->template->build('todo/index');
        }
    }

    /**
     * show all task of this case case.
     */
    public function show_tasklist()
    {
        if ($this->is_ajax_request() || false) {
            ci()->load->library('cases/cases_api');
            $case_id = $this->input->get_post("case_id", '');
            $status = $this->input->get_post("status", '');

            $array_condition = array();
            if (empty($status)) {
                // status is processing
                $array_condition['cases_taskname_instance.status IN (1)'] = null;
            } else {
                $array_condition["cases_taskname_instance.status IN (" . $status . ")"] = null;
            }

            // Only filter task of this user login
            if (APContext::isServiceParner()) {
                $array_condition['cases_milestone.partner_id'] = APContext::getParnerIDLoggedIn();
            }

            // update limit into user_paging.
            $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);

            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging['limit'] = $limit;
            $query_result = cases_api::getListTaskName($array_condition, $input_paging['start'], $input_paging['limit'], $input_paging['sort_column'], $input_paging['sort_type']);

            // Process output data
            $total = $query_result['total'];
            $datas = $query_result['data'];

            //echo "<pre>";print_r($datas);exit;
            
            // Get output response
            $response = $this->get_paging_output($total, $input_paging['limit'], $input_paging['page']);

            $i = 0;
            foreach ($datas as $row) {
                $response->rows[$i]['case_id'] = $row->case_id;
                $arrResult = array(
                    $row->case_id,
                    $row->base_task_name,
                    $row->milestone_name,
                    $row->customer_code,
                    $row->c_email,
                    lang('service_partner_view_status_' . $row->status),
                    ($row->status == '1' || $row->status == '2') ? $row->partner_name : $row->c_name,
                    ($row->status == '1' || $row->status == '2') ? $row->email : $row->c_email,
                    lang('service_partner_task_' . $row->status)
                );
                $response->rows[$i]['cell'] = $arrResult;
                $i++;
            }
            echo json_encode($response);
        }
    }

    /**
     * create case function
     * @createdBy: d3jsexperts
     */
    public function review_company_information()
    {
        // Check access
        $case_id = $this->input->get_post('case_id');

        // Check exist
        $cases_company_information_check = $this->cases_company_information_m->get_by('case_id', $case_id);

        // If post
        if ($_POST and $this->input->is_ajax_request()) {
            $validation_company_information_rules = array(
                array(
                    'field' => 'status',
                    'label' => 'status',
                    'rules' => 'required|trim|max_length[50]'
                ),
                array(
                    'field' => 'comment_content',
                    'label' => 'comment',
                    'rules' => 'required|trim' // #1330 remove character limit in verification comment
                )
            );

            $input = $this->input->post();
            $this->form_validation->set_rules($validation_company_information_rules);
            if ($this->form_validation->run()) {
                $status = $input['status'];
                $comment_content = $input['comment_content'];

                // Insert main director
                $this->cases_company_information_m->update_by_many(array(
                    "case_id" => $case_id
                ), array(

                    "updated_date" => now(),
                    "status" => $status,
                    "comment_date" => now(),
                    "comment_content" => $comment_content,
                    "updated_by" => APContext::getAdminIdLoggedIn()
                ));

                $task_status = '1';
                if ($status == '3') {
                    $task_status = '0';
                } else {
                    $task_status = $status;
                }

                // Update task instance status
                $this->cases_taskname_instance_m->update_by_many(array(
                    "case_id" => $case_id,
                    "base_task_name" => "company_information",
                    "status" => "1"
                ), array(
                    "status" => $task_status
                ));
                
                $activity = ($status == APConstants::CASE_COMPLETED_STATUS) ? APConstants::CASE_ACTIVITY_COMPLETED : APConstants::CASE_ACTIVITY_REJECT;
                CaseUtils::addCaseVerificationHistory($case_id, "company_information", $activity, $comment_content);

                $this->sendEmailNotifyStatusVerification($case_id,$status, $comment_content);

                $message = lang('save_company_information_success');
                $this->success_output($message);
                return;
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }

        // Get all countries
        $countries = $this->countries_m->get_many_by_many(array(), '', false, array(
            'country_name' => 'ASC'
        ));

        $this->template->append_js('jquery.upload-1.0.0.min.js');
        $this->template->set("countries", $countries);
        $this->template->set("company_information", $cases_company_information_check);
        $this->template->set("case_id", $case_id);

        $this->template->build("todo/review_company_information");
    }

    /**
     * create case function
     * @createdBy: d3jsexperts
     */
    public function review_personal_identify()
    {
        // Check access
        $case_id = $this->input->get_post('case_id');

        $cases_personal_identity_01 = $this->cases_personal_identity_m->get_by_many(array(
            'case_id' => $case_id,
            'director_number' => 1
        ));

        $cases_personal_identity_02 = $this->cases_personal_identity_m->get_by_many(array(
            'case_id' => $case_id,
            'director_number' => 2
        ));

        // Get all countries
        $countries = $this->countries_m->get_many_by_many(array(), '', false, array(
            'country_name' => 'ASC'
        ));

        // If post
        if ($_POST and $this->input->is_ajax_request()) {
            $validation_personal_identify_information_rules = array(
                array(
                    'field' => 'status',
                    'label' => 'status',
                    'rules' => 'required|trim|max_length[50]'
                ),
                array(
                    'field' => 'comment_content',
                    'label' => 'comment',
                    'rules' => 'required|trim' // #1330 remove character limit in verification comment
                )
            );

            $input = $this->input->post();
            $this->form_validation->set_rules($validation_personal_identify_information_rules);
            if ($this->form_validation->run()) {
                $status = $input['status'];
                $comment_content = $input['comment_content'];

                // Insert main director
                $this->cases_personal_identity_m->update_by_many(array(
                    "case_id" => $case_id
                ), array(
                    "updated_date" => now(),
                    "status" => $status,
                    "comment_date" => now(),
                    "comment_content" => $comment_content,
                    "updated_by" => APContext::getAdminIdLoggedIn()
                ));

                $task_status = '1';
                if ($status == '3') {
                    $task_status = '0';
                } else {
                    $task_status = $status;
                }

                // Update task instance status
                $this->cases_taskname_instance_m->update_by_many(array(
                    "case_id" => $case_id,
                    "base_task_name" => "personal_identify",
                    "status" => "1"
                ), array(
                    "status" => $task_status
                ));
                
                $activity = ($status == APConstants::CASE_COMPLETED_STATUS) ? APConstants::CASE_ACTIVITY_COMPLETED : APConstants::CASE_ACTIVITY_REJECT;
                CaseUtils::addCaseVerificationHistory($case_id, "personal_identify", $activity, $comment_content);

                $this->sendEmailNotifyStatusVerification($case_id,$status, $comment_content);

                $message = lang('save_personal_identity_success');
                $this->success_output($message);
                return;
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }

        $this->template->append_js('jquery.upload-1.0.0.min.js');
        $this->template->set("countries", $countries);
        // $this->template->set("case_name", $case_name);
        $this->template->set("personal_identity_01", $cases_personal_identity_01);
        $this->template->set("personal_identity_02", $cases_personal_identity_02);
        $this->template->set("case_id", $case_id);

        $this->template->build("todo/review_personal_identify");
    }

    /**
     * Upload personal identity document.
     *
     * @param
     *            : doc_type (1: identification document (passport) | 2: Notarized birth certificate)
     * @param
     *            : case_id: The case identity
     * @param
     *            : director_number (1: The first director | 2: The second director)
     */
    public function view_personal_identity_document()
    {
        // Does not use layout
        $this->template->set_layout(FALSE);
        $case_id = $this->input->get_post('case_id');
        $director_number = $this->input->get_post('director_number');

        $personal_identity = $this->cases_personal_identity_m->get_by_many(array(
            'case_id' => $case_id,
            'director_number' => $director_number
        ));

        $doc_type = $this->input->get_post('doc_type');
        $local_file_name = '';
        if (empty($personal_identity)) {
            return;
        }

        // Get document file name by doc type
        if ($doc_type == '1') {
            $local_file_name = $personal_identity->passport_local_file_path;
        } else
            if ($doc_type == '2') {
                $local_file_name = $personal_identity->birth_certificate_local_file_path;
            }

        if (empty($local_file_name)) {
            return;
        }
        // Get extends file
        header('Content-Disposition: inline');
        $ext = substr($local_file_name, strrpos($local_file_name, '.') + 1);
        $ext = strtolower($ext);
        switch ($ext) {
            case 'jpg':
                header('Content-type: image/jpeg');
                break;
            case 'jpge':
                header('Content-type: image/jpeg');
                break;
            case 'png':
                header('Content-type: image/png');
                break;
            case 'tiff':
                header('Content-type: image/tiff');
                break;
            case 'pdf':
                header('Content-type: application/pdf');
                break;
        }

        $seconds_to_cache = APConstants::CACHED_SECONDS;

        $ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";
        header("Expires: $ts");
        header("Pragma: cache");
        header("Cache-Control: max-age=$seconds_to_cache");

        readfile($local_file_name);
    }
    
     /**
     * Upload contract identity document.
     *
     * @param
     *            : doc_type (1: identification document)
     * @param
     *            : case_id: The case identity
     * @param
     *            : director_number (1: The first director )
     */
    public function view_resource()
    {
        // Does not use layout
        $this->template->set_layout(FALSE);
        $file_id = $this->input->get_post('file_id');
        $type = $this->input->get_post('type');

        if($type && $type == "officer"){
            // Gets resource
            $case_resource = $this->case_usps_officer_m->get($file_id);
            
            // Get document file name
            $local_file_name = $case_resource->officer_local_path;
        }else{
            // Gets resource
            $case_resource = $this->case_resource_m->get($file_id);

            if (empty($case_resource)) {
                echo "file not found";
                return;
            }
            
            // Get document file name
             $local_file_name = $case_resource->local_file_path;
        }
   
        // Get extends file
        $this->render_file_view($local_file_name);
    }
    
    /**
     * render file view to client.
     * @param type $local_file_name
     * @return type
     */
    private function render_file_view($local_file_name){
        if(empty($local_file_name)){
            echo "not file.";
            return ;
        }
        
        // Get extends file
        header('Content-Disposition: inline');
        $ext = substr($local_file_name, strrpos($local_file_name, '.') + 1);
        $ext = strtolower($ext);
        switch ($ext) {
            case 'jpg':
                header('Content-type: image/jpeg');
                break;
            case 'jpge':
                header('Content-type: image/jpeg');
                break;
            case 'png':
                header('Content-type: image/png');
                break;
            case 'tiff':
                header('Content-type: image/tiff');
                break;
            case 'pdf':
                header('Content-type: application/pdf');
                break;
        }

        $seconds_to_cache = APConstants::CACHED_SECONDS;

        $ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";
        header("Expires: $ts");
        header("Pragma: cache");
        header("Cache-Control: max-age=$seconds_to_cache");

        readfile($local_file_name);
    }


    /**
     * Review company registration.
     * @createdBy: d3jsexperts
     */
    public function review_document_of_company_registration()
    {
        // Check access
        $case_id = $this->input->get_post('case_id');
        // Update data to database
        $company_registration_document = $this->cases_registration_document_m->get_by_many(array(
            'case_id' => $case_id
        ));

        if (empty($company_registration_document)) {
            return;
        }

        // If post
        if ($_POST and $this->input->is_ajax_request()) {
            $validation_document_of_company_registration_rules = array(
                array(
                    'field' => 'status',
                    'label' => 'status',
                    'rules' => 'required|trim|max_length[50]'
                ),
                array(
                    'field' => 'comment_content',
                    'label' => 'comment',
                    'rules' => 'required|trim'//#1330 remove character limit in verification comment
                )
            );

            $input = $this->input->post();
            $this->form_validation->set_rules($validation_document_of_company_registration_rules);
            if ($this->form_validation->run()) {
                $status = $input['status'];
                $comment_content = $input['comment_content'];

                // Insert main director
                $this->cases_registration_document_m->update_by_many(array(
                    "case_id" => $case_id
                ), array(
                    "updated_date" => now(),
                    "status" => $status,
                    "comment_date" => now(),
                    "comment_content" => $comment_content,
                    "updated_by" => APContext::getAdminIdLoggedIn()
                ));

                $task_status = '1';
                if ($status == '3') {
                    $task_status = '0';
                } else {
                    $task_status = $status;
                }

                // Update task instance status
                $this->cases_taskname_instance_m->update_by_many(array(
                    "case_id" => $case_id,
                    "base_task_name" => "document_of_company_registration",
                    "status" => "1"
                ), array(
                    "status" => $task_status
                ));
                
                $activity = ($status == APConstants::CASE_COMPLETED_STATUS) ? APConstants::CASE_ACTIVITY_COMPLETED : APConstants::CASE_ACTIVITY_REJECT;
                CaseUtils::addCaseVerificationHistory($case_id, "document_of_company_registration", $activity, $comment_content);

                $this->sendEmailNotifyStatusVerification($case_id,$status, $comment_content);

                $message = lang('save_document_of_company_registration_success');
                $this->success_output($message);
                return;
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }
 
        $this->template->append_js('jquery.upload-1.0.0.min.js');
        $this->template->set("case_id", $case_id);
        $this->template->set("company_registration_document", $company_registration_document);

        $this->template->build("todo/review_company_registration_document");
    }

    /**
     * View company registration document.
     * Only support doc type (1: Company registration document | 2: translation of document of registration)
     */
    public function view_company_registration_document()
    {
        // Does not use layout
        $this->template->set_layout(FALSE);
        $case_id = $this->input->get_post('case_id');
        $cases_registration_document_check = $this->cases_registration_document_m->get_by_many(array(
            'case_id' => $case_id
        ));

        $doc_type = $this->input->get_post('doc_type');
        $local_file_name = '';
        if (empty($cases_registration_document_check)) {
            return;
        }

        // Get document file name by doc type
        if ($doc_type == '1') {
            $local_file_name = $cases_registration_document_check->registraton_document_local_file_path;
        } else
            if ($doc_type == '2') {
                $local_file_name = $cases_registration_document_check->translate_registraton_document_local_file_path;
            }

        if (empty($local_file_name)) {
            return;
        }
        // Get extends file
        header('Content-Disposition: inline');
        $ext = substr($local_file_name, strrpos($local_file_name, '.') + 1);
        $ext = strtolower($ext);
        switch ($ext) {
            case 'jpg':
                header('Content-type: image/jpeg');
                break;
            case 'jpge':
                header('Content-type: image/jpeg');
                break;
            case 'png':
                header('Content-type: image/png');
                break;
            case 'tiff':
                header('Content-type: image/tiff');
                break;
            case 'pdf':
                header('Content-type: application/pdf');
                break;
        }

        $seconds_to_cache = APConstants::CACHED_SECONDS;

        $ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";
        header("Expires: $ts");
        header("Pragma: cache");
        header("Cache-Control: max-age=$seconds_to_cache");

        readfile($local_file_name);
    }

    // *************************************************************************************************
    // Admin for verification
    // *************************************************************************************************

    /**
     * Review company registration.
     * @createdBy: d3jsexperts
     */
    public function review_verification_personal_identification()
    {
        // Check access
        $case_id = $this->input->get_post('case_id');
        $this->load->library("customers/customers_api");
        $this->load->library("email/email_api");
        $cases = $this->cases_m->get($case_id);
        $customer_id = $cases->customer_id;
    
        $cases_verification_personal_identity_check = $this->cases_verification_personal_identity_m->get_by_many(array(
            'case_id' => $case_id,
            "type" => 1
        ));

        if (empty($cases_verification_personal_identity_check) || empty($cases)) {
            redirect("/cases/todo");
        }

        // If post
        if ($_POST and $this->input->is_ajax_request()) {
            $rules = array(
                array(
                    'field' => 'status',
                    'label' => 'status',
                    'rules' => 'required|trim|max_length[50]'
                ),
                array(
                    'field' => 'comment_content',
                    'label' => 'comment',
                    'rules' => 'required|trim' //#1330 remove character limit in verification comment
                )
            );

            $this->form_validation->set_rules($rules);
            if (!$this->form_validation->run()) {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }

            $input = $this->input->post();
            $status = $input['status'];
            $comment_content = $input['comment_content'];

            // Update task instance status
            $this->cases_verification_personal_identity_m->update_by_many(array(
                "case_id" => $case_id,
                "type" => 1
            ), array(
                "updated_date" => now(),
                "status" => $status,
                "comment_date" => now(),
                "comment_content" => $comment_content,
                "updated_by" => APContext::getAdminIdLoggedIn()
            ));

            // Update task instance status
            $this->cases_taskname_instance_m->update_by_many(array(
                "case_id" => $case_id,
                "base_task_name" => "verification_personal_identification"
            ),
            array(
                
                "status" => $status
            ));

            // Update verification data
            $this->update_verification_info($case_id);
            
            $activity = ($status == APConstants::CASE_COMPLETED_STATUS) ? APConstants::CASE_ACTIVITY_COMPLETED : APConstants::CASE_ACTIVITY_REJECT;
            CaseUtils::addCaseVerificationHistory($case_id, "verification_personal_identification", $activity, $comment_content);

            $this->sendEmailNotifyStatusVerification($case_id,$status, $comment_content);

            $this->success_output("");
            return;
        }

        // load customer info.
        $this->load_customer_info($cases, $customer_id, "verification_personal_identification");

        $data = $this->get_customer_info($case_id);

        $postbox_id = $cases->postbox_id;
        $is_invoicing_address_verification = true;
        if (!empty($postbox_id)) {
            $case_postbox = $this->postbox_m->get($postbox_id);
            $is_invoicing_address_verification = false;
            $this->template->set('case_postbox', $case_postbox);
        }

        $milestone_name = CaseUtils::get_milestone_name($case_id, 'verification_personal_identification');
        $case_name = CaseUtils::get_case_name($case_id, 'verification_personal_identification');
        $this->template->set('milestone_name', $milestone_name);
        $this->template->set('case_name', $case_name);
        $this->template->set('is_invoicing_address_verification', $is_invoicing_address_verification);

        $data['company_soft'] = $cases_verification_personal_identity_check;
        $data['view'] = ("View" == $this->input->get_post("op"));
        $this->template->build("todo/review_verification_personal_identification", $data);
    }

    public function review_verification_company_identification_soft()
    {
        // Check access
        $case_id = $this->input->get_post('case_id');

        $cases_verification_personal_identity_check = $this->cases_verification_personal_identity_m->get_by_many(array(
            'case_id' => $case_id,
            "type" => 2
        ));

        if (empty($cases_verification_personal_identity_check)) {
            redirect("/cases/todo");
        }

        // If post
        if ($_POST and $this->input->is_ajax_request()) {
            $rules = array(
                array(
                    'field' => 'status',
                    'label' => 'status',
                    'rules' => 'required|trim|max_length[50]'
                ),
                array(
                    'field' => 'comment_content',
                    'label' => 'comment',
                    'rules' => 'required|trim' //#1330 remove character limit in verification comment 
                )
            );

            $this->form_validation->set_rules($rules);
            if (!$this->form_validation->run()) {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }

            $input = $this->input->post();
            $status = $input['status'];
            $comment_content = $input['comment_content'];

            // Update task instance status
            $this->cases_verification_personal_identity_m->update_by_many(array(
                "case_id" => $case_id,
                "type" => 2
            ), array(
                "updated_date" => now(),
                "status" => $status,
                "comment_date" => now(),
                "comment_content" => $comment_content,
                "updated_by" => APContext::getAdminIdLoggedIn()
            ));

            // Update task instance status
            $this->cases_taskname_instance_m->update_by_many(array(
                "case_id" => $case_id,
                "base_task_name" => "verification_company_identification_soft"
            ),
            array(
                
                "status" => $status
            ));
            
            $activity = ($status == APConstants::CASE_COMPLETED_STATUS) ? APConstants::CASE_ACTIVITY_COMPLETED : APConstants::CASE_ACTIVITY_REJECT;
            CaseUtils::addCaseVerificationHistory($case_id, "verification_company_identification_soft", $activity, $comment_content);

            $this->sendEmailNotifyStatusVerification($case_id,$status, $comment_content);

            // Update verification data
            $this->update_verification_info($case_id);

            $this->success_output("");
            return;
        }

        $cases = $this->cases_m->get($case_id);
        if(empty($cases)){
            redirect("/cases/todo");
        }
        $customer_id = $cases->customer_id;

        // load customer info.
        $this->load_customer_info($cases, $customer_id, "verification_company_identification_soft");

        $milestone_name = CaseUtils::get_milestone_name($case_id, 'verification_company_identification_soft');
        $case_name = CaseUtils::get_case_name($case_id, 'verification_company_identification_soft');
        $this->template->set('milestone_name', $milestone_name);
        $this->template->set('case_name', $case_name);

        $is_invoicing_address_verification = true;
        if (strpos($cases->case_identifier, 'VRAD') == false) {
            $is_invoicing_address_verification = false;
        }
        $this->template->set('is_invoicing_address_verification', $is_invoicing_address_verification);

        $data = $this->get_customer_info($case_id);
        $data['company_soft'] = $cases_verification_personal_identity_check;
        $data['view'] = ("View" == $this->input->get_post("op"));
        $this->template->build("todo/review_verification_company_identification_soft", $data);
    }

    public function review_verification_company_identification_hard()
    {
        // Check access
        $case_id = $this->input->get_post('case_id');

        $cases_verification_company_hard_check = $this->cases_verification_company_hard_m->get_by_many(array(
            'case_id' => $case_id
        ));

        if (empty($cases_verification_company_hard_check)) {
            redirect("/cases/todo");
        }

        // If post
        if ($_POST and $this->input->is_ajax_request()) {
            $rules = array(
                array(
                    'field' => 'status',
                    'label' => 'status',
                    'rules' => 'required|trim|max_length[50]'
                ),
                array(
                    'field' => 'comment_content',
                    'label' => 'comment',
                    'rules' => 'required|trim' //#1330 remove character limit in verification comment 
                )
            );

            $this->form_validation->set_rules($rules);
            if (!$this->form_validation->run()) {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }

            $input = $this->input->post();
            $status = $input['status'];
            $comment_content = $input['comment_content'];

            // Update task instance status
            $this->cases_verification_company_hard_m->update_by_many(array(
                "case_id" => $case_id
            ), array(
                "updated_date" => now(),
                "status" => $status,
                "comment_date" => now(),
                "comment_content" => $comment_content,
                "updated_by" => APContext::getAdminIdLoggedIn()
            ));

            // Update task instance status
            $this->cases_taskname_instance_m->update_by_many(array(
                "case_id" => $case_id,
                "base_task_name" => "verification_company_identification_hard"
            ), array(
                "status" => $status
            ));

            // Update verification data
            $this->update_verification_info($case_id);
            
            $activity = ($status == APConstants::CASE_COMPLETED_STATUS) ? APConstants::CASE_ACTIVITY_COMPLETED : APConstants::CASE_ACTIVITY_REJECT;
            CaseUtils::addCaseVerificationHistory($case_id, "verification_company_identification_hard", $activity, $comment_content);

            $this->sendEmailNotifyStatusVerification($case_id,$status, $comment_content);

            $this->success_output("");
            return;
        }

        $cases = $this->cases_m->get($case_id);
        if(empty($cases)){
            redirect("/cases/todo");
        }
        $customer_id = $cases->customer_id;

        // load customer info.
        $this->load_customer_info($cases, $customer_id, "verification_company_identification_hard");

        $milestone_name = CaseUtils::get_milestone_name($case_id, 'verification_company_identification_hard');
        $case_name = CaseUtils::get_case_name($case_id, 'verification_company_identification_hard');
        $this->template->set('milestone_name', $milestone_name);
        $this->template->set('case_name', $case_name);

        $is_invoicing_address_verification = true;
        if (strpos($cases->case_identifier, 'VRAD') == false) {
            $is_invoicing_address_verification = false;
        }
        $this->template->set('is_invoicing_address_verification', $is_invoicing_address_verification);

        $data = $this->get_customer_info($case_id);
        $data['company_hard'] = $cases_verification_company_hard_check;
        $data['view'] = ("View" == $this->input->get_post("op"));
        $this->template->build("todo/review_verification_company_identification_hard", $data);
    }

    public function review_verification_special_form_PS1583()
    {
        // Check access
        $case_id = $this->input->get_post('case_id');
        $type = $this->input->get_post('type', '');

        $cases_verification_usps_check = $this->cases_verification_usps_m->get_by_many(array(
            'case_id' => $case_id
        ));

        if (empty($cases_verification_usps_check)) {
            redirect("/cases/todo");
        }

        $base_taskname = 'verification_special_form_PS1583';
        if ($type == 'general') {
            $base_taskname = 'verification_General_CMRA';
        }else if($type == 'california'){
            $base_taskname = "verification_california_mailbox";
        }

        // If post
        if ($_POST and $this->input->is_ajax_request()) {
            $rules = array(
                array(
                    'field' => 'status',
                    'label' => 'status',
                    'rules' => 'required|trim|max_length[50]'
                ),
                array(
                    'field' => 'comment_content',
                    'label' => 'comment',
                    'rules' => 'required|trim' // #1330 remove character limit in verification comment 
                )
            );

            $this->form_validation->set_rules($rules);
            if (!$this->form_validation->run()) {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }

            $input = $this->input->post();
            $status = $input ['status'];
            $comment_content = $input ['comment_content'];

            $udate ["status"] = $status;
            $udate ["updated_date"] = now();
            $udate ["comment_date"] = now();
            $udate ["comment_content"] = $comment_content;
            $udate ["updated_by"] = APContext::getAdminIdLoggedIn();
            if ($status == '3') {
                $udate ["verification_amazon_file_path"] = "";
            }

            // Update task instance status
            $this->cases_verification_usps_m->update_by_many(array(
                "case_id" => $case_id
            ), $udate);

            // Update task instance status
            $this->cases_taskname_instance_m->update_by_many(array(
                "case_id" => $case_id,
                "base_task_name" => $base_taskname
            ), array(
                "status" => $status
            ));

            // Update verification data
            $this->update_verification_info($case_id);
            
            $activity = ($status == APConstants::CASE_COMPLETED_STATUS) ? APConstants::CASE_ACTIVITY_COMPLETED : APConstants::CASE_ACTIVITY_REJECT;
            CaseUtils::addCaseVerificationHistory($case_id, $base_taskname, $activity, $comment_content);

            $this->sendEmailNotifyStatusVerification($case_id,$status, $comment_content);

            $this->success_output("");
            return;
        }

        $cases = $this->cases_m->get($case_id);
        if (empty($cases)) {
            redirect("/cases/todo");
        }
        $customer_id = $cases->customer_id;

        // load customer info.
        $this->load_customer_info($cases, $customer_id, $base_taskname);

        // get country
        $countries = $this->countries_m->get_all();

        // Get main postbox name
        $postbox = $this->postbox_m->get_by_many(array(
            'customer_id' => $customer_id,
            'is_main_postbox' => APConstants::ON_FLAG
        ));

        // Get postbox location information
        $location = $this->location_m->get_by_many(array(
            'id' => $postbox->location_available_id
        ));
        
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

        $milestone_name = CaseUtils::get_milestone_name($case_id, $base_taskname);
        $case_name = CaseUtils::get_case_name($case_id, $base_taskname);
        $this->template->set('milestone_name', $milestone_name);
        $this->template->set('case_name', $case_name);

        $data = array(
            'special' => $cases_verification_usps_check,
            'location' => $location,
            'countries' => $countries
        );
        $data['view'] = ("View" == $this->input->get_post("op"));
        $this->template->set('type', $type);
        $this->template->build("todo/review_verification_special_form_PS1583", $data);
    }

    public function review_verification_california_mailbox()
    {
        $case_id = $this->input->get_post('case_id');

        redirect('cases/todo/review_verification_special_form_PS1583?type=california&case_id=' . $case_id);
    }
    
    public function review_verification_General_CMRA()
    {
        $case_id = $this->input->get_post('case_id');

        redirect('cases/todo/review_verification_special_form_PS1583?type=general&case_id=' . $case_id);
    }
    
    // #1148 change in verification forms - urgent!!! 
     public function review_TC_contract_MS()
    {
        // Check access
        $case_id = $this->input->get_post('case_id');

        $cases_verification_contract_check = $this->cases_contract_m->get_by_many(array(
            'case_id' => $case_id
        ));
        
        if (empty($cases_verification_contract_check)) {
            redirect("/cases/todo");
        }
        // get resource
         $cases_resource_check = $this->case_resource_m->get_by_many(array(
            'case_id' => $case_id,
            "base_taskname" => "TC_contract_MS"
        ));
         
        // If post
        if ($_POST and $this->input->is_ajax_request()) {
            $rules = array(
                array(
                    'field' => 'status',
                    'label' => 'status',
                    'rules' => 'required|trim|max_length[50]'
                ),
                array(
                    'field' => 'comment_content',
                    'label' => 'comment',
                    'rules' => 'required|trim' // #1330 remove character limit in verification comment 
                )
            );

            $this->form_validation->set_rules($rules);
            if (!$this->form_validation->run()) {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }

            $input = $this->input->post();
            $status = $input['status'];
            $comment_content = $input['comment_content'];

            // Update contract status
            $this->cases_contract_m->update_by_many(array(
                "case_id" => $case_id
            ), array(
                "update_date" => now(),
                "status" => $status,
                "comment_date" => now(),
                "comment_content" => $comment_content,
                "update_by" => APContext::getAdminIdLoggedIn()
            ));
           
            // Update task instance status
            $this->cases_taskname_instance_m->update_by_many(array(
                "case_id" => $case_id,
                "base_task_name" => "TC_contract_MS"
            ), array(
                "status" => $status
            ));
            
            $activity = ($status == APConstants::CASE_COMPLETED_STATUS) ? APConstants::CASE_ACTIVITY_COMPLETED : APConstants::CASE_ACTIVITY_REJECT;
            CaseUtils::addCaseVerificationHistory($case_id, "TC_contract_MS", $activity, $comment_content);

            // Update verification data
            $this->update_verification_info($case_id);

            $this->sendEmailNotifyStatusVerification($case_id,$status, $comment_content);

            $this->success_output("");
            return;
        }
        // get term and condition
        $terms_and_conditions = settings_api::getTermAndCondition();
        
        $this->template->set('terms_and_conditions', $terms_and_conditions);
        
        $cases = $this->cases_m->get($case_id);
        if(empty($cases)){
            redirect("/cases/todo");
        }
        
        $customer_id = $cases->customer_id;

        // load customer info.
        $this->load_customer_info($cases, $customer_id, "TC_contract_MS");

        $milestone_name = CaseUtils::get_milestone_name($case_id, 'TC_contract_MS');
        $case_name = CaseUtils::get_case_name($case_id, 'TC_contract_MS');
        $this->template->set('milestone_name', $milestone_name);
        $this->template->set('case_name', $case_name);

        $is_invoicing_address_verification = true;
        if (strpos($cases->case_identifier, 'VRAD') == false) {
            $is_invoicing_address_verification = false;
        }
        $this->template->set('is_invoicing_address_verification', $is_invoicing_address_verification);

        $data = $this->get_customer_info($case_id);
        $data['contract'] = $cases_verification_contract_check;
        $data['resource'] = $cases_resource_check;
        $data['view'] = ("View" == $this->input->get_post("op"));
        $this->template->build("todo/review_tc_contract_ms", $data);
    }
    
     // #1148 change in verification forms - urgent!!! 
    public function review_proof_of_address_MS(){
        
        // Check access
        $case_id = $this->input->get_post('case_id');

        $cases_verification_proof_of_address_check = $this->cases_proof_business_m->get_by_many(array(
            'case_id' => $case_id
        ));
        
        if (empty($cases_verification_proof_of_address_check)) {
            redirect("/cases/todo");
        }
        // get resource
         $cases_resource_check = $this->case_resource_m->get_by_many(array(
            'case_id' => $case_id,
            "base_taskname" => "proof_of_address_MS"
        ));
         
        // If post
        if ($_POST and $this->input->is_ajax_request()) {
            $rules = array(
                array(
                    'field' => 'status',
                    'label' => 'status',
                    'rules' => 'required|trim|max_length[50]'
                ),
                array(
                    'field' => 'comment_content',
                    'label' => 'comment',
                    'rules' => 'required|trim' //#1330 remove character limit in verification comment 
                )
            );

            $this->form_validation->set_rules($rules);
            if (!$this->form_validation->run()) {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }

            $input = $this->input->post();
            $status = $input['status'];
            $comment_content = $input['comment_content'];

            // Update contract status
            $this->cases_proof_business_m->update_by_many(array(
                "case_id" => $case_id
            ), array(
                "updated_date" => now(),
                "status" => $status,
                "comment_date" => now(),
                "comment_content" => $comment_content,
                "update_by" => APContext::getAdminIdLoggedIn()
            ));
           
            // Update task instance status
            $this->cases_taskname_instance_m->update_by_many(array(
                "case_id" => $case_id,
                "base_task_name" => "proof_of_address_MS"
            ), array(
                "status" => $status
            ));

            // Update verification data
            $this->update_verification_info($case_id);
            
            $activity = ($status == APConstants::CASE_COMPLETED_STATUS) ? APConstants::CASE_ACTIVITY_COMPLETED : APConstants::CASE_ACTIVITY_REJECT;
            CaseUtils::addCaseVerificationHistory($case_id, "proof_of_address_MS", $activity, $comment_content);

            $this->sendEmailNotifyStatusVerification($case_id,$status, $comment_content);

            $this->success_output("");
            return;
        }
        
        $cases = $this->cases_m->get($case_id);
        if(empty($cases)){
            redirect("/cases/todo");
        }
        $customer_id = $cases->customer_id;

        // load customer info.
        $this->load_customer_info($cases, $customer_id, "proof_of_address_MS");

        $milestone_name = CaseUtils::get_milestone_name($case_id, 'proof_of_address_MS');
        $case_name = CaseUtils::get_case_name($case_id, 'proof_of_address_MS');
        $this->template->set('milestone_name', $milestone_name);
        $this->template->set('case_name', $case_name);

        $is_invoicing_address_verification = true;
        if (strpos($cases->case_identifier, 'VRAD') == false) {
            $is_invoicing_address_verification = false;
        }
        $this->template->set('is_invoicing_address_verification', $is_invoicing_address_verification);
        
        $data = $this->get_customer_info($case_id);
        $data['proof_of_address'] = $cases_verification_proof_of_address_check;
        $data['resource'] = $cases_resource_check;
        $data['view'] = ("View" == $this->input->get_post("op"));
        $this->template->build("todo/review_proof_of_address_ms", $data);
    }
    
    /**
     * Review company verifiction E MS case.
     * @return type
     */
    public function review_company_verification_E_MS() {
        
        // Check access
        $case_id = $this->input->get_post('case_id');
        $base_taskname = "company_verification_E_MS";
        
        $case_check = $this->cases_company_ems_m->get_by('case_id', $case_id);
        if (empty($case_check)) {
            redirect("/cases/todo");
        }
        
         // Check exist this case
        $case_exist = $this->cases_m->get_many($case_id);
        
        // get customer address
        $postbox = $this->postbox_m->get_by('postbox_id', $case_exist[0]->postbox_id);
        
        if($postbox && $postbox->location_available_id){
            $location_name_postbox = $this->location_m->get_by('id', $postbox->location_available_id);
            $this->template->set('location_name_postbox', $location_name_postbox);
        }

        // get resource
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
         
        // If post
        if ($_POST and $this->input->is_ajax_request()) {
            $rules = array(
                array(
                    'field' => 'status',
                    'label' => 'status',
                    'rules' => 'required|trim|max_length[50]'
                ),
                array(
                    'field' => 'comment_content',
                    'label' => 'comment',
                    'rules' => 'required|trim'
                )
            );

            $this->form_validation->set_rules($rules);
            if (!$this->form_validation->run()) {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }

            $input = $this->input->post();
            $status = $input['status'];
            $comment_content = $input['comment_content'];
           
            // Update contract status
            $this->cases_company_ems_m->update_by_many(array(
                "case_id" => $case_id
            ), array(
                "updated_date" => now(),
                "status" => $status,
                "comment_date" => now(),
                "comment_content" => $comment_content,
                "update_by" => APContext::getAdminIdLoggedIn()
            ));
           
            // Update task instance status
            $this->cases_taskname_instance_m->update_by_many(array(
                "case_id" => $case_id,
                "base_task_name" => $base_taskname
            ), array(
                "status" => $status
            ));
            
            $activity = ($status == APConstants::CASE_COMPLETED_STATUS) ? APConstants::CASE_ACTIVITY_COMPLETED : APConstants::CASE_ACTIVITY_REJECT;
            CaseUtils::addCaseVerificationHistory($case_id, $base_taskname, $activity, $comment_content);

            // Update verification data
            $this->update_verification_info($case_id);

            $this->sendEmailNotifyStatusVerification($case_id,$status, $comment_content);

            $this->success_output("");
            return;
        }
        
        $cases = $this->cases_m->get($case_id);
        if(empty($cases)){
            redirect('cases/todo');
        }
        $customer_id = $cases->customer_id;

        // load customer info.
        $this->load_customer_info($cases, $customer_id, $base_taskname);

        $milestone_name = CaseUtils::get_milestone_name($case_id, $base_taskname);
        $case_name = CaseUtils::get_case_name($case_id, $base_taskname);
        $this->template->set('milestone_name', $milestone_name);
        $this->template->set('case_name', $case_name);

        $is_invoicing_address_verification = true;
        if (strpos($cases->case_identifier, 'VRAD') == false) {
            $is_invoicing_address_verification = false;
        }
        $this->template->set('is_invoicing_address_verification', $is_invoicing_address_verification);

        $data = $this->get_customer_info($case_id);
        $this->template->set("officers", $officers);
        $this->template->set("case_resource", $case_resource);
        $this->template->set("mailReceivers", $mailReceivers);
        $this->template->set('postbox', $postbox);
        
        $data['company_verification_ems'] = $case_check;
        $data['view'] = ("View" == $this->input->get_post("op"));
        $this->template->build("todo/review_company_verification_ems", $data);
    }

    public function view_file()
    {
        $this->template->set_layout(FALSE);
        
        // get file path
        $case_id = $this->input->get_post('case_id');
        $type = $this->input->get_post('type');
        $id = $this->input->get_post('id');
        $op = $this->input->get_post('op');
        
        // Gest locatl file path.
        $local_file_path = cases_todo_api::getSpecialLocalFileName($case_id, $type, $op, $id);
        
        // Get extends file
        $this->render_file_view($local_file_path);
    }

    /**
     * get_customer_info.
     *
     * @param
     *            $case_id
     */
    private function get_customer_info($case_id)
    {
        $customer_id = $this->cases_m->get($case_id)->customer_id;
        $customer = APContext::getCustomerByID($customer_id);
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
        $data = array(
            'customer' => $customer,
            'customer_addresses' => $customer_addresses
        );
        return $data;
    }

    /**
     * Update verification information after admin accept verification case.
     *
     * @param unknown_type $case_id
     */
    function update_verification_info($case_id)
    {
        // Check case exist
        $cases = $this->cases_m->get_by_many(array(
            'id' => $case_id
        ));
        if (empty($cases)) {
            return;
        }
        
        // call start case again
        CaseUtils::start_verification_case($cases->customer_id);
        return;
        /*
        // Check case 1 (verification_personal_identification)
        $verification_personal_identification_approved = false;
        $verification_personal_identification = $this->cases_verification_personal_identity_m->get_by_many(array(
            "case_id" => $case_id,
            "type" => 1,
            "deleted_flag" => APConstants::OFF_FLAG
        ));
        $taskname_personal_identification = $this->cases_taskname_instance_m->get_by_many(array(
            "base_task_name" => "verification_personal_identification",
            "case_id" => $case_id,
            "deleted_flag" => APConstants::OFF_FLAG
        ));
        // Status 2: Completed | 3: Rejected
        if ((empty ($verification_personal_identification) || $verification_personal_identification->status == '2')
            && (!empty($taskname_personal_identification) && $taskname_personal_identification->status == '2')
        ) {
            $verification_personal_identification_approved = true;
        }

        // Check case 2 (verification_company_identification_soft)
        $verification_company_identification_soft_approved = false;
        $verification_company_identification_soft = $this->cases_verification_personal_identity_m->get_by_many(array(
            "case_id" => $case_id,
            "type" => 2,
            "deleted_flag" => APConstants::OFF_FLAG
        ));
        $taskname_company_soft = $this->cases_taskname_instance_m->get_by_many(array(
            "base_task_name" => "verification_company_identification_soft",
            "case_id" => $case_id,
            "deleted_flag" => APConstants::OFF_FLAG
        ));
        // Status 2: Completed | 3: Rejected
        if ((empty ($verification_company_identification_soft) || $verification_company_identification_soft->status == '2') &&
            (!empty ($taskname_company_soft) && $taskname_company_soft->status == "2")
        ) {
            $verification_company_identification_soft_approved = true;
        }

        // Check case 3 (verification_company_identification_hard)
        $verification_company_identification_hard_approved = false;
        $cases_verification_company_hard = $this->cases_verification_company_hard_m->get_by_many(array(
            "case_id" => $case_id,
            "deleted_flag" => APConstants::OFF_FLAG
        ));
        $taskname_company_hard = $this->cases_taskname_instance_m->get_by_many(array(
            "base_task_name" => "verification_company_identification_hard",
            "case_id" => $case_id,
            "deleted_flag" => APConstants::OFF_FLAG
        ));
        // Status 2: Completed | 3: Rejected
        if ((empty ($cases_verification_company_hard) || $cases_verification_company_hard->status == '2')
            && (!empty($taskname_company_hard) && $taskname_company_hard->status == '2')
        ) {
            $verification_company_identification_hard_approved = true;
        }

        // Check case 4 (verification_special_form_PS1583)
        $verification_special_form_PS1583_approved = false;
        $verification_special_form_PS1583 = $this->cases_verification_usps_m->get_by_many(array(
            "case_id" => $case_id,
            "deleted_flag" => APConstants::OFF_FLAG
        ));
        // Status 2: Completed | 3: Rejected
        if (!empty($verification_special_form_PS1583) && $verification_special_form_PS1583->status == '2') {
            $verification_special_form_PS1583_approved = true;
        }

        // Check case verification_contract_identification
        $verification_contract_identification_approved = false;
        $verification_contract_identification = $this->cases_contract_m->get_by_many(array(
            "case_id" => $case_id,
            "deleted_flag" => APConstants::OFF_FLAG
        ));
        
        $taskname_contract_identification = $this->cases_taskname_instance_m->get_by_many(array(
            "base_task_name" => "TC_contract_MS",
            "case_id" => $case_id,
            "deleted_flag" => APConstants::OFF_FLAG
        ));
        // Status 2: Completed | 3: Rejected
        if ((empty ($verification_contract_identification) || $verification_contract_identification->status == '2')
            && (!empty($taskname_contract_identification) && $taskname_contract_identification->status == '2')
        ) {
            $verification_contract_identification_approved = true;
        }
        
         // Check case verification_proof_of_address_identification
        $verification_proof_of_address_identification_approved = false;
        $verification_proof_of_address_identification_identification = $this->cases_proof_business_m->get_by_many(array(
            "case_id" => $case_id,
            "deleted_flag" => APConstants::OFF_FLAG
        ));
        
        $taskname_proof_of_address_identification = $this->cases_taskname_instance_m->get_by_many(array(
            "base_task_name" => "proof_of_address_MS",
            "case_id" => $case_id,
            "deleted_flag" => APConstants::OFF_FLAG
        ));
        // Status 2: Completed | 3: Rejected
        if ((empty ($verification_proof_of_address_identification_identification) || $verification_proof_of_address_identification_identification->status == '2')
            && (!empty($taskname_proof_of_address_identification) && $taskname_proof_of_address_identification->status == '2')
        ) {
            $verification_proof_of_address_identification_approved = true;
        }
        
        // Check case verification_company_verification_ems_identification
        $company_verification_ems_approved = false;
        $company_verification_ems_identification = $this->cases_company_ems_m->get_by_many(array(
            "case_id" => $case_id,
            "deleted_flag" => APConstants::OFF_FLAG
        ));
        
        $taskname_company_verification_ems_identification = $this->cases_taskname_instance_m->get_by_many(array(
            "base_task_name" => "company_verification_E_MS",
            "case_id" => $case_id,
            "deleted_flag" => APConstants::OFF_FLAG
        ));
        // Status 2: Completed | 3: Rejected
        if ((empty ($company_verification_ems_identification) || $company_verification_ems_identification->status == '2')
            && (!empty($taskname_company_verification_ems_identification) && $taskname_company_verification_ems_identification->status == '2')
        ) {
            $company_verification_ems_approved = true;
        }
        
        // Update data in postbox and customer addresses
        $verification_flag = APConstants::OFF_FLAG;
        if ($verification_personal_identification_approved && $verification_company_identification_soft_approved
                && $verification_company_identification_hard_approved && $verification_special_form_PS1583_approved
                && $company_verification_ems_approved && $verification_proof_of_address_identification_approved 
                && $verification_contract_identification_approved
        ) {
            $verification_flag = APConstants::ON_FLAG;
        }

        // Update case verification status
        // Get customer identity
        $customer_id = $cases->customer_id;
        $postbox_id = $cases->postbox_id;
        if (empty ($postbox_id)) {
            $this->customers_address_m->update_by_many(array(
                "customer_id" => $customer_id
            ), array(
                "invoice_address_verification_flag" => $verification_flag
            ));
        } else {
            if ($verification_personal_identification_approved) {
                $this->postbox_m->update_by_many(array(
                    "postbox_id" => $postbox_id
                ), array(
                    "name_verification_flag" => APConstants::ON_FLAG
                ));
            }

            if ($verification_company_identification_soft_approved && $verification_company_identification_hard_approved) {
                $this->postbox_m->update_by_many(array(
                    "postbox_id" => $postbox_id
                ), array(
                    "company_verification_flag" => APConstants::ON_FLAG
                ));
            }
            
            if ($verification_special_form_PS1583_approved) {
                $this->postbox_m->update_by_many(array(
                    "postbox_id" => $postbox_id
                ), array(
                    "company_verification_flag" => APConstants::ON_FLAG,
                    "name_verification_flag" => APConstants::ON_FLAG
                ));
            }
            
            if ($verification_contract_identification_approved ) {
                $this->postbox_m->update_by_many(array(
                    "postbox_id" => $postbox_id
                ), array(
                    "name_verification_flag" => APConstants::ON_FLAG
                ));
            }
            
             if ($verification_proof_of_address_identification_approved ) {
                $this->postbox_m->update_by_many(array(
                    "postbox_id" => $postbox_id
                ), array(
                    "company_verification_flag" => APConstants::ON_FLAG
                ));
            }
            
             if ($company_verification_ems_approved ) {
                $this->postbox_m->update_by_many(array(
                    "postbox_id" => $postbox_id
                ), array(
                    "company_verification_flag" => APConstants::ON_FLAG
                ));
            }
        }*/
    }

    public function view()
    {
        $this->template->set_layout(FALSE);
        // get file path
        $case_id = $this->input->get_post('case_id');
        $type = $this->input->get_post('type');
        $op = $this->input->get_post('op');
        $id = $this->input->get_post('id');

        // Gets local file path.
        $local_file_path = cases_todo_api::getSpecialLocalFileName($case_id, $type, $op, $id);
        
        // Read file
        if (empty($local_file_path)) {
            echo "Not file";
            return;
        }

        $ext = substr($local_file_path, strrpos($local_file_path, '.') + 1);
        $ext = strtolower($ext);

        $this->template->set('ext', $ext);
        $this->template->set("id", $id);
        $this->template->set('case_id', $case_id);
        $this->template->set('type', $type);
        $this->template->set('op', $op);
        $this->template->build('todo/view_file');
    }

    /**
     * load customer info: invoicing address + postbox address.
     * @param unknown $case_id
     */
    private function load_customer_info($cases, $customer_id, $base_task_name ='')
    {
        $case_id = $cases->id;
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

        // Gets selected postbox
        $postboxes = $this->postbox_m->get_list_postboxes($customer_id);
        $this->template->set('postboxes', $postboxes);

        $postbox = $this->postbox_m->get_postbox($cases->postbox_id);
        $this->template->set('postbox', $postbox);
        $this->template->set('customer_addresses', $customer_addresses);
        
        $verification_history = $this->cases_verification_history_m->getCaseVerificationHistory($case_id, $base_task_name);
        $this->template->set("verification_history", $verification_history);
    }
    /*
     * #1070 new e-mail trigger after a postbox is fully verified
     * param: $case_id, $status, $comment
     */
    public function sendEmailNotifyStatusVerification($case_id,$status, $comment){
    	
        // load model 
    	$this->load->model('mailbox/postbox_m');
    	$this->load->model('email/email_m');
    	$this->load->model('settings/countries_m');
        // load library
        $this->load->library("email/email_api");
        $this->load->library("customers/customers_api");
        $this->load->library("email/email_api");
        $this->load->library('addresses/addresses_api');
        
		// get cases 
        $cases = $this->cases_m->get($case_id);
        
        // Get info customer 
        if($cases->customer_id){
            $customer_id = $cases->customer_id;
            $customer = customers_api::getCustomerByID($customer_id);
        }
        
        // Get info postbox detail
        if($cases->postbox_id){
            $postbox_detail = $this->postbox_m->get_by_many ( array (
              "postbox_id" => $cases->postbox_id
            ));
        
            // Get info location
            $location = $this->addresses_api->getLocationByID ($postbox_detail->location_available_id);
            
        }else{
            $postbox_detail = $this->postbox_m->get_by_many ( array (
        		"customer_id" => $cases->customer_id
            ));
        
            // Get info location
            $location = $this->addresses_api->getLocationByID ($postbox_detail->location_available_id);
        }
      
        
        // Get info country
        $country = $this->countries_m->get_by_many(array(
            'id' => $location->country_id
        ));
        
        /* 
         * When admin reject verification --> system send email to customer: Notification about your verification status
         * Verification case (3: Incomplete)
         */
        if($status == 3){
            $email_template = email_api::getEmail('email_notified_status_verification');
            if(is_object($email_template)){
                $data = array(
                    'full_name' => $customer->user_name,
                    'status' => "Incomplete",
                    'reason' => $comment,
                    "slug" => "email_notified_status_verification",
                    "to_email" => $customer->email,
                    "email" => $customer->email,
                    "site_url" => APContext::getFullBalancerPath()
                );
                MailUtils::sendEmailByTemplate($data);
            }
        }
        
        /* 
         *  When full completed verification --> system send email to customer: Notification about your postbox  verification status
         *  Verification case (2: Complete) with:
         *   + Check name_verification_flag and(or) company_verification_flag are APConstants::ON_FLAG
         *   + Check verification_special_form_PS1583
         */
        if( ($status == 2) && ($cases->status == 2) ){
            $data = array(
                "postbox_name" => $postbox_detail->postbox_name,
                "name" => $postbox_detail->name,
                "company" => $postbox_detail->company,
                "street" => $location->street,
                "postcode" => $location->postcode,
                "city" => $location->city,
                "region" => $location->region,
                "country" => $country->country_name,
                'full_name' => $customer->user_name,
                'status' => "Incomplete",
                'reason' => $comment,
                "slug" => APConstants::email_notified_postbox_successfully_verification_status,
                "to_email" => $customer->email,
                "email" => $customer->email,
                "site_url" => APContext::getFullBalancerPath()
            );
            MailUtils::sendEmailByTemplate($data);
        }
    }
    
   /**
     * #1149 verification reporting for each customer
     * Create verification report for one customer
     */
    public function view_verification_detail(){
        ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 1200);
        $this->load->library('pdf');
        $customer_id = (int) $this->input->get('cid');
        
        if(empty($customer_id)){
            return '';
        }
        
        $file_output = cases_todo_api::process_report_for_each_customer($customer_id);
        //$file_des = 'uploads/temp/view_verification_detail_'.$customer_id.".pdf";
        //copy($file_output, $file_des);
        //redirect($file_des);
        header('Content-Type: application/pdf');
        //header('Content-Disposition: attachment; filename="view_verification_detail_'.$customer_id.'.pdf"');
        readfile($file_output);
        exit;
    }
    
    /**
     * #1054 verification reporting 
     * Create verification report by location
     */
    public function verification_report() {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 1200);        
        $location_id = $this->input->get_post("location");
        $location_name = $this->input->get_post("location_name");
        $location_name = str_replace(" ", "_", $location_name);
       
        $start_date  = $this->input->get_post('startDate');
        $end_date    = $this->input->get_post('endDate');
        if( empty($location_id) || empty($start_date) || empty($end_date) ){
            echo "Invalid query";
        }
        $start_date  = strtotime($start_date);
        $end_date    = strtotime($end_date);

        $list_customer = CaseUtils::get_list_customer_verification_report($location_id,$start_date, $end_date);
        //echo "<pre>";print_r($list_customer);exit;
        $list_files = array();
        if(count($list_customer)){
            
            foreach ($list_customer as $customer) {
                
                $file = cases_todo_api::process_report_for_each_customer($customer->customer_id, $postbox_verify = 1, $location_id);
                if(!empty($file)){
                    $list_files[] = $file;
                }
            }
            
            //$file_name = "./uploads/temp/verification_report_".$location_name."_".date("d_m_Y",$start_date)."_".date("d_m_Y",$end_date).".pdf";
            $file_name = "verification_report_".$location_name."_".date("d_m_Y",$start_date)."_".date("d_m_Y",$end_date).".pdf";
            
            $destination_path = Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . "cases/".$location_name."/";
            if (!is_dir($destination_path)) {
                mkdir($destination_path, 0777, TRUE);
                chmod($destination_path, 0777);
            }
            $destination_file = $destination_path.$file_name;
            APUtils::mergePDFfiles($list_files, $destination_file,'P');
            header('Content-Type: application/pdf');
            readfile($destination_file);
            //redirect($verification_report);
            exit;
        }
        else{
            echo "No customer at report";
        }
                
    }
 
    /**
     * review phone number company case.
     */
    public function review_phone_number_company() {
        $this->review_phone_number_case('2', 'phone_number_company');
    }
    
    /**
     * review phone number for personal case.
     */
    public function review_phone_number_for_personal(){
        $this->review_phone_number_case('1', 'phone_number_for_personal');
    }
    
    /**
     * review phone number case verficiation
     */
    private function review_phone_number_case($type, $base_task_name){
        // Check access
        $case_id = $this->input->get_post('case_id');
        $cases_check = $this->case_phone_number_m->get_by_many(array(
            'case_id' => $case_id,
            "type" => $type
        ));
        $cases = $this->cases_m->get($case_id);
        
        if (empty($cases_check) || empty($cases)) {
            redirect("/cases/todo");
        }
        
        $customer_id = $cases->customer_id;
        
        // get resource
        $personal_cases_resource = $this->case_resource_m->get_many_by_many(array(
            'case_id' => $case_id,
            "base_taskname" => $base_task_name,
            "seq_number" => "01"
        ));
        
        $company_cases_resource = $this->case_resource_m->get_many_by_many(array(
            'case_id' => $case_id,
            "base_taskname" => $base_task_name,
            "seq_number" => "02"
        ));
         
        // If post
        if ($_POST and $this->input->is_ajax_request()) {
            $rules = array(
                array(
                    'field' => 'status',
                    'label' => 'status',
                    'rules' => 'required|trim|max_length[50]'
                ),
                array(
                    'field' => 'comment_content',
                    'label' => 'comment',
                    'rules' => 'required|trim' // #1330 remove character limit in verification comment 
                )
            );

            $this->form_validation->set_rules($rules);
            if (!$this->form_validation->run()) {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }

            $input = $this->input->post();
            $status = $input['status'];
            $comment_content = $input['comment_content'];

            // Update contract status
            $this->case_phone_number_m->update_by_many(array(
                "id" => $cases_check->id,
            ), array(
                "updated_date" => now(),
                "status" => $status,
                "comment_date" => now(),
                "comment_content" => $comment_content,
                "update_by" => APContext::getAdminIdLoggedIn()
            ));
           
            // Update task instance status
            $this->cases_taskname_instance_m->update_by_many(array(
                "case_id" => $case_id,
                "base_task_name" => $base_task_name
            ), array(
                "status" => $status
            ));
            
            $activity = ($status == APConstants::CASE_COMPLETED_STATUS) ? APConstants::CASE_ACTIVITY_COMPLETED : APConstants::CASE_ACTIVITY_REJECT;
            CaseUtils::addCaseVerificationHistory($case_id, $base_task_name, $activity, $comment_content);

            // Update verification data
            $this->update_verification_info($case_id);

            $this->sendEmailNotifyStatusVerification($case_id,$status, $comment_content);

            $this->success_output("");
            return;
        }

        // load customer info.
        $this->load_customer_info($cases, $customer_id, $base_task_name);

        $milestone_name = CaseUtils::get_milestone_name($case_id, $base_task_name);
        $case_name = CaseUtils::get_case_name($case_id, $base_task_name);
        $this->template->set('milestone_name', $milestone_name);
        $this->template->set('case_name', $case_name)->set('case_id', $case_id);

        $data = $this->get_customer_info($case_id);
        $data['contract'] = $cases_check;
        $data['personal_resources'] = $personal_cases_resource;
        $data['company_resources'] = $company_cases_resource;
        $data['view'] = ("View" == $this->input->get_post("op"));
        $this->template->set('base_task_name', $base_task_name)->set('type', $type);
        $this->template->build("todo/review_phone_number_case", $data);
    }
}