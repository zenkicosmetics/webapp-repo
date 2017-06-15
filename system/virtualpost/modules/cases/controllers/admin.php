<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Admin controller for the users module
 */
class admin extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Model
        $this->load->model(array(
            'cases_m',
            'settings/countries_m',
            "cases_milestone_instance_m"
        ));
        $this->load->library('invoices/export');
        $this->lang->load('cases');
    }

    /**
     * Display all case in the system
     */
    public function index()
    {
        $this->load->library("cases/cases_api");
        $status = $this->input->get_post('status');
        $enquiry = $this->input->get_post('enquiry');
        
        // Build filter condition
        $array_condition = array();
        if (! empty($status)) {
            $array_condition['cases.status'] = $status;
        } else {
            $array_condition["cases.status in ('1','2')"] = null;
        }
        
        // searches for customer ID and email or parts of it?
        if (! empty($enquiry)) {
            $array_condition ["(customers.customer_id LIKE '%{$enquiry}%' OR customers.email LIKE '%" . $enquiry . "%')"] = null;
        }
        
        // Only filter task of this user login
        /*
        if (APContext::isServiceParner()) {
            $array_condition['cases_milestone.partner_id'] = APContext::getParnerIDLoggedIn();
        }
        */
        
        if ($this->is_ajax_request() || false) {
            // update limit into user_paging.
            $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);
            
            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging['limit'] = $limit;
            
            // Call search method
            $query_result = cases_api::getCasesPaging($array_condition, $input_paging);
            
            // Process output data
            $total = $query_result['total'];
            $datas = $query_result['data'];
            
            // Get output response
            $response = $this->get_paging_output($total, $input_paging['limit'], $input_paging['page']);
            
            // #1058 add multi dimension capability for admin
            $date_format = APUtils::get_date_format_in_user_profiles();
            
            $i = 0;
            foreach ($datas as $row) {
                $last_activity = APUtils::viewDateFormat(strtotime($row->modified_date), $date_format.APConstants::TIMEFORMAT_OUTPUT02);
                $response->rows[$i]['id'] = $row->id;
                $status = "Waiting";
                if($row->status == 3){
                    $status = "Waiting";
                } else if($row->status == 2){
                    $status = "Completed";
                } else if($row->has_to_do == 1){
                    $status = "Pending";
                }
                
                $response->rows[$i]['cell'] = array(
                    $row->id,
                    $row->customer_id,
                    $row->customer_code,
                    APUtils::viewDateFormat($row->opening_date,$date_format),
                    $row->case_identifier,
                    $row->email,
                    $row->description,
                    $row->product_name,
                    $row->country_name,
                    $status,
                    $last_activity,
                    $row->status,
                    $row->has_to_do
                );
                $i ++;
            }
            echo json_encode($response);
        } else {
            // list all
            $this->template->build('admin/index');
        }
    }

    /**
     * show checklist case.
     */
    public function show_checklist()
    {
        // #1058 add multi dimension capability for admin
        $date_format = APUtils::get_date_format_in_user_profiles();
        
        if ($this->is_ajax_request() || false) {
            $case_id = $this->input->get_post("case_id", '');
            $array_condition = array(
                'case_id' => $case_id
            );
            // Only filter task of this user login
            if (APContext::isServiceParner()) {
                $array_condition['partner_id'] = APContext::getParnerIDLoggedIn();
            }
            
            // update limit into user_paging.
            $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);
            
            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging['limit'] = $limit;
            
            // Call search method
            $query_result = $this->cases_milestone_instance_m->get_milestone_paging($array_condition, $input_paging['start'], $input_paging['limit'], $input_paging['sort_column'], $input_paging['sort_type']);
            // Process output data
            $total = $query_result['total'];
            $datas = $query_result['data'];
            
            // Get output response
            $response = $this->get_paging_output($total, $input_paging['limit'], $input_paging['page']);
            
            $i = 0;
            foreach ($datas as $row) {
                $response->rows[$i]['id'] = $row->id;
                $arrResult = array(
                    $row->product_id,
                    $row->case_id,
                    $row->status,
                    $row->base_task_name,
                    $row->milestone_name,
                    $row->created_date ? APUtils::viewDateFormat($row->created_date, $date_format) : '',
                    $row->updated_date ? APUtils::viewDateFormat($row->updated_date, $date_format) : '',
                    lang('status_' . $row->status),
                    ($row->status == '1' || $row->status == '2') ? $row->partner_name : $row->c_name,
                    $row->last_confirmed_by,
                    lang('service_partner_task_' . $row->status)
                );
                $response->rows[$i]['cell'] = $arrResult;
                $i ++;
            }
            echo json_encode($response);
        }
    }
    
    public function verification(){
        $case_id = $this->input->get_post('case_id');
        
        if(empty($case_id)){
            redirect('/cases/admin/');
        }
        
        $array_condition = array(
                'case_id' => $case_id
        );
        
        // Call search method
        $query_result = $this->cases_milestone_instance_m->get_milestone_paging($array_condition, 0, 10, '', '');
        // Process output data
        $datas = $query_result['data'];
        $objData = $datas[0];
        $objResult = $datas[0];
        foreach($datas as $obj){
            if($obj->status == 1){
                $objResult = $obj;
                break;
            }
        }
        
        $redirect_url = "";
        switch($objResult->base_task_name){
            case 'verification_personal_identification': 
                $redirect_url = '/cases/todo/review_verification_personal_identification?case_id='.$case_id;
                break;
            case 'verification_company_identification_soft':
                $redirect_url = '/cases/todo/review_verification_company_identification_soft?case_id='.$case_id;
                break;
            case 'verification_company_identification_hard':
                $redirect_url = '/cases/todo/review_verification_company_identification_hard?case_id='.$case_id;
                break;
            case 'verification_General_CMRA':
            case 'verification_california_mailbox':
            case 'verification_special_form_PS1583':
                $redirect_url = '/cases/todo/review_verification_special_form_PS1583?case_id='.$case_id;
                break;
        }
        
        // redirect todo page.
        redirect($redirect_url);
    }
    
    /**
     * #1054 verification reporting 
     *Create verification report by location
     */
    public function create_verification_report() {
        
    	$this->template->set_layout(FALSE);
    	$locations = APUtils::loadListAccessLocation();
    	$this->template->set('locations', $locations);
    	$this->template->build('admin/form');
    }
  
}