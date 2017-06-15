<?php defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends APIAdmin_Controller
{
    public function __construct() {

        parent::__construct();

        // system lib
        $this->load->library(array("form_validation"));

        // external libs
        $this->load->library(array(
            'users/users_api',
            'users/Ion_auth',
            'scans/scans_api',
            'scans/incoming_api',
            'scans/todo_api',
            'scans/completed_api',
            'addresses/addresses_api',
            'customers/customers_api',
            'mailbox/mailbox_api',
            'email/email_api',
            'payment/payment_api',
            'partner/partner_api',
            'price/price_api',
            'api/mobile_api',
            'api/mobile_validation_rules',
            "files/files"
        ));

        // language
        $this->lang->load(array(
            'api',
            'users/user',
            'account/account',
            'customers/customer',
            'scans/scans'
        ));

        // models
        $this->load->model(array(
            'users/group_user_m',
            'users/mobile_session_m',
            'users/user_m',
            'scans/envelope_m',
            'customers/customer_m',
            'addresses/customers_address_m',
            'mailbox/postbox_m',
            'invoices/invoice_summary_m',
            'cloud/customer_cloud_m',
            'report/partner_receipt_m',
            "addresses/location_m",
            'partner/partner_m',
        ));

        ci()->load->library('S3');
    }

    /**
     * Verify Web Services are working. void
     *
     * @return array {'code' => 1000, 'message' => 'Working'}
     */
    public function index()
    {
        $data = array(
            'code' => 1000,
            'message' => 'Working',
            'result' => 'index'
        );
        $this->api_success_output($data);
        exit();
    }

    /**
     *
     */
    public function login ()
    {

        $response = array(
            'message' => '',
            'result'  => ''
        );

        $email       = $this->input->post('email');
        $password    = $this->input->post('password');
        $remember_me = false;

        //echo "<pre>";print_r($_POST);exit;

        if(empty($email)){

        	$response["message"] = lang('login.empty_email');
        	$this->api_error_output($response);
            exit();
        }
        if(empty($password)){

        	$response["message"] = lang('login.empty_password');
        	$this->api_error_output($response);
            exit();
        }

        $check_login = users_api::check_login ($email, $password, $remember_me);
        if(!$check_login['status']){

        	$response["message"] = strip_tags($check_login['message']);
        	$this->api_error_output($response);
            exit();
        }

        if ( $check_login['status'] OR $this->ion_auth->logged_in() ) {

            $user_login = $this->user_m->get(array(
                'email' => $email
            ));
            $response = mobile_api::refeshUserSession($user_login);
            $this->api_success_output($response);
            exit();

        }
        else {

        	$response["message"] = lang('login.error');
        	$this->api_error_output($response);
            exit();
        }


    }

    /*
    * Des: logout user admin
    */
    public function logout ()
    {
        $session_key = $this->getSessionKey();

        $this->mobile_session_m->delete_by_many(array(
            'session_key ' => $session_key
        ));

        $this->ion_auth->logout();

        $response = array(
            'message'     => lang('user_logged_out'),
            'result'      => ""
        );

        $this->api_success_output($response);
        exit();

    }

    /*
    * Des: Get list of incomming pages
    */
    public  function get_incoming_List(){

    	$customer_id = $this->input->post('customer_id');
        $from_customer_name = $this->input->post('from_customer_name');
        $type = $this->input->post('type');
        $weight = $this->input->post('weight');
        $term = "'%" . $this->input->post("customer_id_auto") . "%'";
        $input_location_id = $this->input->post('location_available_id');
        $list_access_location = APUtils::mobileLoadListAccessLocation();
        $list_access_location_id = array();

        if ($list_access_location && count($list_access_location) > 0) {
            foreach ($list_access_location as $location) {
                $list_access_location_id[] = $location->id;
            }
        }
        $list_filter_location_id = array(
            0
        );
        if (empty($input_location_id)) {
            $list_filter_location_id = $list_access_location_id;
        } else {
            if (in_array($input_location_id, $list_access_location_id)) {
                $list_filter_location_id[] = $input_location_id;
            }
        }

        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : APContext::getAdminPagingSetting();
        APContext::updateAdminPagingSetting($limit);

        $input_paging = $this->get_paging_mobile_input('incomming_date', 'DESC', 1, 10);

        $input_paging['limit'] = $limit;


        $result = scans_api::getIncomingList($customer_id, $from_customer_name, $type, $weight, $term, $list_filter_location_id, $input_paging, $limit);

        $response = array(
                'message' => lang('getIncomingList.successfully'),
                'result'  => $result['mobile_incomming_list']
        );

        $this->api_success_output($response);
        exit();
    }

    public function reset_password()
	{
		$email = $this->input->post('email');

		$this->form_validation->set_rules(
			array( array(
				'field' => 'email',
				'label' => 'email',
				'rules' => 'required|max_length[60]|valid_email',
			)
			)
		);

		$response = array(
            'message' => '',
            'result' => ''
        );

		if ($this->form_validation->run()) {
			$request_reset = users_api::request_reset_pass($email);

			if($request_reset["status"]){
				$response = array(
	                'message' => $request_reset["message"],
	                'result'  => ''
	            );
            	$this->api_success_output($response);
           		exit();
			}
			else {
				$response["message"] = $request_reset["message"];
	        	$this->api_error_output($response);
	            exit();
			}
		} else {
			$errors = $this->form_validation->error_json();
			$response["message"] = strip_tags($errors["message"]);
        	$this->api_error_output($response);
            exit();
		}
	}

    public function add_incomming(){

        $this->form_validation->set_rules(mobile_validation_rules::$add_incomming_validation_rules);

        if ($this->form_validation->run()) {

            $customer_id         = ci()->input->post('customer_id');
            $from_customer_name  = ci()->input->post('from_customer_name');
            $to                  = ci()->input->post('to');
            $postbox_id          = (int) ci()->input->post('postbox_id');
            $type                = ci()->input->post('type');
            $labelValue          = ci()->input->post('labelValue');
            $width               = ci()->input->post('width');
            $height              = ci()->input->post('height');
            $weight              = ci()->input->post('weight');
            $length              = ci()->input->post('length');


            $response = array(
                'message' => '',
                'result'  => ''
            );

            $result = incoming_api::add_incomming($customer_id, $from_customer_name, $to,  $postbox_id,$type, $labelValue, $width, $height, $weight, $length);

            if($result['status']){

                $response['message'] = $result['message'];

                if(isset($result['result']) && !empty($result['result'])) {
                    $response['result'] = $result['result'];
                }

                $this->api_success_output($response);
                exit();
            }
            else{

                $response['message'] = $result['message'];
                $this->api_error_output($response);
                exit();
            }
        }
        else {

            $errors = $this->form_validation->error_json();
            $response["message"] = strip_tags($errors["message"]);
            $this->api_error_output($response);
            exit();
        }
    }

    public  function auto_postbox()
    {

        $term        = $this->input->get_post("term");
        $location_id = $this->input->get_post("location_id", "");
        $term = trim(strip_tags($term));

        $matches = incoming_api::auto_postbox($term, $location_id);

        $response = array(
            'message' => 'successfully',
            'result'  => $matches
        );

        $this->api_success_output($response);
        exit();

    }

    public  function list_location_access()
    {

        $list_location = APUtils::mobileLoadListAccessLocation();
        $response = array(
            'message' => 'successfully',
            'result'  => $list_location
        );

        $this->api_success_output($response);
        exit();

    }

    public function check_item(){

        $envelope_code = $this->input->post("item_id", '');
        $result = completed_api::check_item($envelope_code);

        $data = array(
            'message' => '',
            'result'  => ''
        );

        if($result['status']){

            $data = array(
                'message' => 'Successfully',
                'result'  => $result['message']
            );

            $this->api_success_output($data);
            exit();
        }
        else {

            $data = array(
                'message' => $result['message'],
                'result'  => ''
            );

            $this->api_error_output($data);
            exit();
        }
    }

    public function get_envelope_type_list()
    {

        $location_id = $this->input->post('location_id');

        $list_type = addresses_api::get_envelope_type_list($location_id);

        $data = array(
            'code' => APConstants::API_RETURN_SUCCESS,
            'message' => 'get_envelope_type_list_success',
            'result' => $list_type
        );

        $this->api_success_output($data);
        exit();
    }

    public function search_activities_item()
    {
        $envelope_code = $this->input->post("item_id", '');

        $data_response = array(
            'message' => '',
            'result' => ''
        );

        if(empty($envelope_code)){

            $data_response['message'] = "Empty item_id";
            $data_response['result']  = "";
            $this->api_error_output($data_response);
            exit();
        }

        $limit = isset($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APContext::getAdminPagingSetting();
        APContext::updateAdminPagingSetting($limit);
        $input_paging = $this->get_paging_mobile_input('last_updated_date', 'DESC', 1, 10);
        $input_paging ['limit'] = $limit;


        $response = completed_api::search_complated_activities_check_item($envelope_code,$input_paging);

        $data_response['message'] = "Successfully";
        $data_response['result']  = $response['mobile_activities_check_item'];

        $this->api_success_output($data_response);
        exit();

    }

    /**
     * get todo list api
     */
    public function get_todolist(){
        $input_location_id = $this->input->post('location_id', '');

        $list_access_location = APUtils::loadArrayListAccessLocationOnMobile();
        $list_filter_location_id = array(
            0
        );

        if (!$input_location_id) {
            $list_filter_location_id = $list_access_location;
        } else {
            if (in_array($input_location_id, $list_access_location)) {
                $list_filter_location_id[] = $input_location_id;
            }
        }
        $input_paging = $this->get_paging_mobile_input('incomming_date', 'DESC', 1, 10);
        $result = todo_api::get_todo_list($list_filter_location_id, $input_paging);

        $response = array(
            'message' => '',
            'result'  => $result['mobile_result']
        );

        $this->api_success_output($response);
        exit();
    }

    /**
     * execute scan on todo list.
     */
    public function execute_scan(){
        // get parameter
        $customer_id= $this->input->post('customer_id');
        $envelop_id= $this->input->post('envelope_id');
        $action_type = 'upload';
        $scan_type= $this->input->post('scan_type');

        $customer = $this->customer_m->get($customer_id);
        $envelope = $this->envelope_m->get($envelop_id);
        $result = todo_api::execute_scan($customer, $envelope, $action_type, $scan_type, 0);

        if($result['status']){
            $this->api_success_output('');
        }else{
            $this->api_error_output($result['message']);
        }
        return;
    }

    /**
     * mark completed on todo list api.
     */
    public function mark_completed(){
        // Get customer address
        $customer_id = $this->input->get_post('customer_id');
        $envelope_id = $this->input->get_post('envelope_id');
        $current_scan_type = $this->input->get_post('current_type');
        $invoice_flag = $this->input->get_post('invoice_flag');
        $category_type = $this->input->get_post('category_type');
        $check_page_item_flag = 0;

        $envelope = $this->envelope_m->get_by_many(array(
            "id" => $envelope_id,
            "to_customer_id" => $customer_id
        ));
        $customer = $this->customer_m->get_by_many(array(
            "customer_id" => $customer_id
        ));

        // If this envelope does not exist.
        if (!$envelope) {
            $this->api_success_output('');
            return;
        }

        // mark completed.
        $result = todo_api::mark_completed($envelope, $customer, $current_scan_type, $invoice_flag, $category_type, $check_page_item_flag);

        if($result['status']){
            $this->api_success_output('');
        }else{
            $this->api_error_output($result['message']);
        }
        return;
    }

    /*
    *Description: Delete history activity of envelope
    */
    public function delete_history_activity_envelope(){

        $envelope_completed_id = $this->input->get_post("id");

        $response = array(
                'message' => "",
                'result'  => ""
        );

        if(empty($envelope_completed_id)){
            $response["message"] = "ID empty";
            $this->api_error_output($response);
            exit();
        }

        $result = completed_api::delete($envelope_completed_id);

        if($result['status']){

            $response = array(
                'message' => $result['message'],
                'result'  => ""
            );
            $this->api_success_output($response);
            exit();
        }
        else {

            $response = array(
                'message' => $result['message'],
                'result'  => ""
            );
            $this->api_error_output($response);
            exit();
        }

    }

    /**
     *@Des: List all postbox
     */
    
    public function postboxlist()
    { 
       
        $this->load->library('customers/customers_api');
        
        $enquiry = $this->input->post("enquiry");   
        $hideDeletedPostbox = $this->input->post("hideDeletedPostbox");        
        $response = array(
            'message' => "",
            'result'  => ""
        );
        $input_paging  = $this->get_paging_mobile_input('postbox_id', 'DESC', 1, 10);
        
        //#1250 HOTFIX: the Hamburg panel shows this customer but this cusotmer has already been deleted
        $limit = isset ($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APContext::getAdminPagingSetting();
        APContext::updateAdminPagingSetting($limit);
        $input_paging ['limit'] = $limit;
        
        $location_id   = $this->input->get_post("location_id");
        $list_postbox  = customers_api::postboxlist($enquiry, $hideDeletedPostbox, $input_paging, $location_id, 1);
        $response['message'] = "Successfully";
        $response['result']  = $list_postbox['mobile_postbox_list'];
        $this->api_success_output($response);
        exit();
    }

    /**
     * List all customer
     */
    public function get_list_customer()
    {

        $enquiry = $this->input->get_post("enquiry");
        $hideDeletedCustomer = $this->input->get_post("hideDeletedCustomer");

        $input_paging = $this->get_paging_mobile_input('customer_id', 'DESC', 1, 10);
        $location_id = $this->input->get_post("location_id");

        $response = array(
            'message' => "",
            'result'  => ""
        );

        $list_customer = customers_api::get_list_customer($enquiry, $hideDeletedCustomer, $input_paging, $location_id, $api_mobile = 1);

        $response['message'] = "Successfully";
        $response['result']  = $list_customer['mobile_customer_list'];
        $this->api_success_output($response);

    }

    /**
     * Get list envelope category
     */
    public function get_envelope_category_list()
    {
        $list_data = Settings::get_list(APConstants::CATEGORY_TYPE_CODE);
        $data = array(
            'code' => APConstants::API_RETURN_SUCCESS,
            'message' => 'get_envelope_category_list_success',
            'result' => $list_data
        );
        $this->api_success_output($data);
        exit();
    }

    /**
     * Des: Get info customer to edit
     */
    public function view_customer_info()
    {

        $customerID = $this->input->get_post("customer_id");
        $data_response = array(
            'message' => '',
            'result' => ''
        );

        if(empty($customerID)){

            $data_response['message'] = "Empty customer";
            $this->api_error_output($data_response);
            exit();
        }

        $customer = new stdClass ();

        $conditionNamesCustomer1 = array("customer_id", "invoice_type IS NULL");
        $conditionValuesCustomer1 = array($customerID, $invoice_type = null);
        $dataNamesCustomer1 = array("invoice_type", "invoice_code");
        $dataValuesCustomer1 = array(1, '');

        customers_api::updateCustomer($conditionNamesCustomer1, $conditionValuesCustomer1, $dataNamesCustomer1, $dataValuesCustomer1);

        $customer = customers_api::getCustomerByID($customerID);
        if (!empty ($customer)) {
            $customer->repeat_password = '';
            $customer->password = '';
        }

        $postbox_first = mailbox_api::getFirstLocationBy($customerID);
        $location_list = mailbox_api::getLocationListBy($customerID);

        $data_response['result']['postbox_first'] = $postbox_first;
        $data_response['result']['location_list'] = $location_list;
        // Gets partner code if have.

        $arr_label_status_customer = array();
        $arr_label_status_customer[0]['value'] = 0;
        $arr_label_status_customer[0]['txt'] = lang('customer.status.deleted');

        $arr_label_status_customer[1]['value'] = 1;
        $arr_label_status_customer[1]['txt'] = lang('customer.activated');

        $arr_label_status_customer[2]['value'] = 2;
        $arr_label_status_customer[2]['txt'] = lang('customer.auto_deactivated');

        $arr_label_status_customer[3]['value'] = 3;
        $arr_label_status_customer[3]['txt'] = lang('customer.manu_deactivated');

        $arr_label_status_customer[4]['value'] = 4;
        $arr_label_status_customer[4]['txt'] = lang('customer.never_activated');

        //$label_status_customer = new stdClass;
        $data_response['result']['label_status_customer'] = $arr_label_status_customer;


        $arr_label_invoice_type = array();
        $arr_label_invoice_type[0]['value'] = 1;
        $arr_label_invoice_type[0]['txt'] = lang('customer.credit_card');

        $arr_label_invoice_type[1]['value'] = 2;
        $arr_label_invoice_type[1]['txt'] = lang('customer.invoice_payment');

        $arr_label_invoice_type[2]['value'] = 3;
        $arr_label_invoice_type[2]['txt'] = lang('customer.paypal');

        //$label_status_customer = new stdClass;
        $data_response['result']['label_invoice_type'] = $arr_label_invoice_type;

        $customer_status = customers_api::getCustomerStatus($customer);
        $customer->customer_status = $customer_status;

        switch ($customer_status) {
            case lang('customer.status.deleted'):
                $customer->customer_status_value = 0;
                break;
            case lang('customer.activated'):
                $customer->customer_status_value = 1;
                break;
            case lang('customer.auto_deactivated'):
                $customer->customer_status_value = 2;
                break;

            case lang('customer.manu_deactivated'):
                $customer->customer_status_value = 3;
                break;

            case lang('customer.never_activated'):
                $customer->customer_status_value = 4;
                break;
            default:
                $customer->customer_status_value = '';
                break;
        }

        $partner = partner_api::getPartnerCodeByCustomer($customerID);
        $partner_code = $partner ? $partner->partner_code : "";
        $data_response['result']['partner_code'] = $partner_code;
        $data_response['result']['customer']  = $customer;
        $data_response['message'] = "Successfully";
        //echo "<pre>";print_r($data_response);exit;

        $this->api_success_output($data_response);
        exit();

    }

    /*
    * @Des: Save info of customer
    */
    public function save_customer()
    {
        $customerID = $this->input->post("customer_id");

        $data_response = array(
            'message' => '',
            'result' => ''
        );

        if(empty($customerID)){

            $data_response['message'] = "Empty customer";
            $this->api_error_output($data_response);
            exit();
        }

        $customer = new stdClass ();

        $conditionNamesCustomer1 = array("customer_id", "invoice_type IS NULL");
        $conditionValuesCustomer1 = array($customerID, $invoice_type = null);
        $dataNamesCustomer1 = array("invoice_type", "invoice_code");
        $dataValuesCustomer1 = array(1, '');
        customers_api::updateCustomer($conditionNamesCustomer1, $conditionValuesCustomer1, $dataNamesCustomer1, $dataValuesCustomer1);

        $customer = customers_api::getCustomerByID($customerID);

        if (!empty ($customer)) {
            $customer->repeat_password = '';
            $customer->password = '';
        }

        $this->form_validation->set_rules(mobile_api::$edit_customer_rule);
        if ($this->form_validation->run()) {

            $updateData  = $this->input->post();
            $save_result = customers_api::save_customer($customer, $updateData);

            if($save_result['status']){

                $data_response['message'] = $save_result['message'];
                $this->api_success_output($data_response);
                exit();
            }
            else{

                $data_response['message'] = $save_result['message'];
                $this->api_error_output($data_response);
                exit();
            }

        }  else {

            $errors = $this->form_validation->error_json();
            $data_response['message'] = strip_tags($errors["message"]);
            $this->api_error_output($data_response);
            exit();
        }
    }

    /**
     * Method for handling different form actions
     */
    public function change_pass_customer()
    {
        $customer_id = $this->input->post("customer_id");
        $email = $this->input->post("email");

        $data_response = array(
            'message' => '',
            'result' => ''
        );

        if(empty($customer_id)){
            $data_response['message'] = "Empty customer";
            $this->api_error_output($data_response);
            exit();
        }

        if(empty($email)){
            $data_response['message'] = "Empty email";
            $this->api_error_output($data_response);
            exit();
        }

        $customer = $this->customer_m->get($customer_id);
        $this->form_validation->set_rules(mobile_validation_rules::$change_customer_password_rule);

        if ($this->form_validation->run()) {

            $insert_data = $this->input->post();
            $result  =  customers_api::change_pass_customer($customer, $insert_data);

            if($result['status']){

                $data_response['message'] = $result['message'];
                $this->api_success_output($data_response);
                exit();
            }
            else{

                $data_response['message'] = $result['message'];
                $this->api_error_output($data_response);
                exit();
            }

        } else {

            $errors = $this->form_validation->error_json();
            $data_response['message'] = strip_tags($errors["message"]);
            $this->api_error_output($data_response);
            exit();
        }
    }

    /**
     * View detail information for admin.
     */
    public function view_detail_customer()
    {

        $data_response = array(
            'message' => '',
            'result' => ''
        );

        $customer_id = $this->input->post("customer_id");

        if (empty($customer_id)) {

            $data_response['message'] = "Empty customer";
            $this->api_error_output($data_response);
            exit();
        }
        $data_response = customers_api::view_detail_customer($customer_id);
        $this->api_success_output($data_response);
        exit();
    }

    /**
     * Callback From: check_email()
     *
     * @param string $email
     *            The Email address to validate
     * @return bool
     */
    public function _check_email($email)
    {
        $id = $this->input->get_post("customer_id");
        $email = $this->input->get_post("email");
        // Get user information by email
        $customer = $this->customer_m->get_by_many(array(
            "email" => $email,

            // fixbug #492
            "(status is null OR status = 0)" => null
        ));

        if ($customer && $customer->customer_id != $id) {
            $this->form_validation->set_message('_check_email', lang('email_exist'));
            return false;
        }
        if ($customer && $customer->email != $email) {
            $this->form_validation->set_message('_check_email', lang('email_exist'));
            return false;
        }
        return true;
    }

    /**
     * Generate invoice code
     */
    public function generate_invoice_code()
    {
        $customer_id = $this->input->post("customer_id");
        $data_response = array(
            'message' => '',
            'result' => ''
        );

        $response = customers_api::generate_invoice_code($customer_id);

        if($response['status']){

            $data_response['message'] = $response['message'];
            $data_response['result']['invoice_code'] = $response['invoice_code'];
            $this->api_success_output($data_response);
            exit();
        }
        else {

            $data_response['message'] = $response['message'];
            $this->api_error_output($data_response);
            exit();
        }

    }

    /**
     * List price template
     */
    public function list_price_template()
    {

        $data_response = array(
            'message' => '',
            'result' => ''
        );

        $array_condition = array();

        $limit = isset($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APContext::getAdminPagingSetting();
        APContext::updateAdminPagingSetting($limit);

        $input_paging = $this->get_paging_mobile_input('id', 'DESC', 1, 10);
        $input_paging ['limit'] = $limit;

        $result = price_api::list_price_template($array_condition, $input_paging, $limit);

        $data_response['result'] = $result['mobile_list_price_template'];
        $data_response['message'] = "Successfully";

        $this->api_success_output($data_response);
        exit();
    }

    /**
     * location pricing.
     */
    /*
    public function location_pricing()
    {
        $data_response = array(
            'message' => '',
            'result' => ''
        );

        $response = addresses_api::location_pricing($api_mobile = 1);

        unset($response['list_access_location']);

        $data_response['message'] = "Successfully";
        $data_response['result'] = $response;

        $this->api_success_output($data_response);
        exit();
    }
    */
    public function location_pricing()
    {
        $data_response = array(
            'message' => '',
            'result' => ''
        );

        $location_id = $this->input->post("location_id", 0);

        $location    = $this->location_m->get_by_many(array(
            'id' => $location_id
        ));

        if (empty($location_id) || empty($location)) {

            $data_response['message'] = 'invalid or missing required parameters';
            $this->api_error_output($data_response);
            exit();
        }

        // get pricing by location.
        $pricing_map = mobile_api::view_pricing_info($location);

        // update ticket #1142
        $result = array();
        foreach($pricing_map as $key=>$map){
            $tmp = $map;
            if($key == APConstants::FREE_TYPE){
                $tmp['as_you_go']->text = lang('as_you_go_duration_text');
                $tmp['postbox_fee']->item_value = $tmp['postbox_fee_as_you_go']->item_value;
                $tmp['as_you_go']->item_value = $tmp['as_you_go']->item_value;
                $tmp['as_you_go']->item_unit = "Days";

                unset($tmp['postbox_fee_as_you_go']);
            }
            $result[$key] = $tmp;
        }
        $data_response =array(
            "message" => "Successfully",
            "result" => $result
        );

        $this->api_success_output($data_response);
        exit();
    }

    /**
     * Activities in Current Period
     */
    public function load_current_activities()
    {
        $data_response = array(
            'message' => '',
            'result' => ''
        );

        $customer_id = $this->input->post('customer_id');

        if(empty($customer_id)){

            $data_response['message'] = "Empty customer";
            $this->api_error_output($data_response);
            exit();
        }

        $customer = customers_api::getCustomerByID($customer_id);
        $limit = isset($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APContext::getPagingSetting();
        APContext::updatePagingSetting($limit);

        $input_paging = $this->get_paging_mobile_input('id', 'DESC', 1, 10);
        $input_paging['limit'] = APContext::getPagingSetting();
        $list_current_invoice = InvoiceUtils::load_current_activities($customer,$input_paging);

        $data_response['result'] = $list_current_invoice['mobile_current_invoices'];
        $data_response['message'] = "Successfully";

        $this->api_success_output($data_response);
        exit();

    }

    /*
    *  Des: Save info Item on check Item page
    */
    public  function save_item_info()
    {
        $data = array();

        $data_response = array(
            'message' => '',
            'result' => ''
        );

        $data['weight'] = $this->input->post('weight');
        $data['height'] = $this->input->post('height');
        $data['width']  = $this->input->post('width');

        $data['length'] = $this->input->post('length');
        $data['envelope_id'] = (int) $this->input->post('envelope_id');
        $data['from_customer_name'] = $this->input->post('from_customer_name');

        if(empty($data['envelope_id'])) {

            $data_response['message'] = "Not exists envelope ID: ".$this->input->post('envelope_id');
            $this->api_error_output($data_response);
            exit();
        }

        $result = completed_api::save_item_info($data);

        $data_response = array(
            'message' => '',
            'result' => ''
        );

        if($result){

            $data_response['result'] = "";
            $data_response['message'] = "Successfully";
            $this->api_success_output($data_response);
            exit();
        }
        else {

            $data_response['message'] = "Error occur when save items";
            $this->api_error_output($data_response);
            exit();
        }

    }

    /**
     * Load old invoice
     */
    public function load_old_invoice()
    {

        $data_response = array(
            'message' => '',
            'result'  => ''
        );

        $customer_id = $this->input->post('customer_id');

        $customer = customers_api::getCustomerByID($customer_id);

        if(empty($customer_id) || empty($customer)){

            $data_response['message'] = "Empty customer";
            $this->api_error_output($data_response);
            exit();
        }

        $input_paging = $this->get_paging_mobile_input('id', 'DESC', 1, 10);

        $response = InvoiceUtils::load_old_invoice($customer, $input_paging);

        $data_response['message'] = "Successfully";
        $data_response['result'] = $response['mobile_old_invoices'];

        $this->api_success_output($data_response);
        exit();

    }

    /**
     * cancel request.
     */
    public function cancel_scan_activity()
    {
        $data_response = array(
            'message' => '',
            'result'  => ''
        );

        $envelope_id = $this->input->get_post('envelope_id', '');
        $type = $this->input->get_post('type', '');

        $response = completed_api::cancel_request($envelope_id, $type);

        if($response['status']){
            $this->api_success_output($data_response);
            exit();
        } else {

            $data_response['message'] = $response['message'];
            $this->api_error_output($data_response);
            exit();
        }
    }

    /**
     * Disable prepayment
     */
    public function disable_prepayment(){

        $envelop_id  = $this->input->get_post('envelop_id');

        $customer_id = $this->input->get_post('customer_id');

        $data_response = array(
            'message' => '',
            'result'  => ''
        );

        if(empty($envelop_id)) {
            $data_response['message'] = lang('disable_prepayment.envelope_empty');
            $this->api_error_output($data_response);
            exit();
        }

        if(empty($customer_id)) {

            $data_response['message'] = lang('disable_prepayment.customer_empty');
            $this->api_error_output($data_response);
            exit();
        }

        completed_api::disable_prepayment($envelop_id, $customer_id, 1);
        $data_response['message'] = lang('disable_prepayment.success_message');
        $this->api_success_output($data_response);
        exit();
    }

    /**
     * Get app version
     */
    function get_app_version() {
        $app_code = parent::getAppCode();
        $app_key = parent::getAppKey();
        // Get session information
        $app_check = $this->app_external_m->get_by_many(array (
        	'app_code' => $app_code,
        	'app_key' => $app_key
        ));
        if (empty($app_check)) {
        	$data = array(
                'code' => 1107,
                'message' => 'The app code or app key is invalid.',
            );
            $this->api_error_output($data);
            return;
        }
        $data = array(
            'message' => 'get_app_version_success',
            'result' => json_encode($app_check)
        );
        $this->api_success_output($data);
    }

    /**
     * Method for handling different form actions
     */
    public function change_pass_user() {

        $user_id  = $this->input->post("user_id");
        $new_password = $this->input->post('new_password');
        $old_password = $this->input->post('old_password');

        $data_response = array(
            'message' => '',
            'result' => ''
        );

        if(empty($user_id)){
            $data_response['message'] = "Empty user id";
            $this->api_error_output($data_response);
            exit();
        }

        if(empty($new_password)){
            $data_response['message'] = "Empty new password";
            $this->api_error_output($data_response);
            exit();
        }

        $this->form_validation->set_rules(mobile_validation_rules::$change_user_password_rule);
        if ($this->form_validation->run()) {

            $response = users_api::change_pass($user_id, $new_password);
            if ($response['status']) {
                $data_response['message'] = strip_tags($response['message']);
                $user_login = $this->user_m->get_by("id", $user_id);
                $response = mobile_api::refeshUserSession($user_login);

                $data_response['session_key'] = $response['session_key'];
                $this->api_success_output($data_response);
                exit();
            }
            else {
                $data_response['message'] = strip_tags($response['message']);
                $this->api_error_output($data_response);
                exit();
            }
        }
        else {
            $errors = $this->form_validation->error_json();
            $data_response['message'] = strip_tags($errors["message"]);
            $this->api_error_output($data_response);
            exit();
        }

    }

    /**
     * manage receipt from admin site.
     * we can search by partner name, location name and receipt description.
     * @return type
     */
    public function manage_receipt(){
        // Get input condition
        $enquiry = $this->input->get_post("enquiry");
        $array_condition = array();
        if (!empty($enquiry)) {
            // the search field, search for: partner name, description, location
            $new_enquiry = APUtils::sanitizing($enquiry);
            $array_condition["(partner_partner.partner_name LIKE '%" . $new_enquiry . "%'" .
                    " OR location.location_name LIKE '%" . $new_enquiry . "%'" .
                    " OR partner_receipt.description LIKE '%" . $new_enquiry . "%')"] = null;
        }

        $input_paging = $this->get_paging_mobile_input();
        $limit = isset($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APContext::getAdminPagingSetting();
        APContext::updateAdminPagingSetting($limit);
        $input_paging['limit'] = $limit;

        // Call search method
        $query_result = $this->partner_receipt_m->get_receipt_paging($array_condition, $input_paging['start'], $input_paging['limit'], $input_paging['sort_column'], $input_paging['sort_type']);

        $result = $query_result['data'];
        foreach($result as $row){
            $row->date_of_receipt = APUtils::displayDate($row->date_of_receipt);
        }

        $data = array(
            "message" => "Successfully",
            "total" => $query_result['total'],
            "result" => $result
        );

        $this->api_success_output($data);
        return;
    }

    /**
     * get receipt.
     * @return type
     */
    public function get_receipt(){
        $receipt_id = $this->input->post('receipt_id');

        $list_partners = $this->partner_m->get_all();
        $list_locations = null;
        $receipt_check = null;
        if(empty($receipt_id)){
            if (count($list_partners) > 0) {
                $list_locations = $this->location_m->get_many_by_many( array(
                    'partner_id' => $list_partners[0]->partner_id
                ));
            }
        }else{
            $receipt_check = $this->partner_receipt_m->get($receipt_id);
            if(empty($receipt_check)){
                $this->api_error_output(array(
                    "result" => "",
                    "message" => lang('receipt_partner_not_existed')
                ));
                return;
            }

            $list_locations = $this->location_m->get_many_by_many( array(
                'partner_id' => $receipt_check->partner_id
            ));
        }

        $response = array(
            "message" => '',
            "result" => array(
                "receipt" => $receipt_check,
                "list_partners" => $list_partners,
                "list_locations" => $list_locations
            )
        );

        $this->api_success_output($response);
        return;
    }

    /**
     *
     * get list locations by partner code.
     * @return type
     */
    public function get_list_location_of_partner(){
        $partner_id = $this->input->post('partner_id');

        if(empty($partner_id)){
             $this->api_error_output(array(
                "message" => "Partner must be required."
            ));
            return;
        }
        $list_locations = $this->location_m->get_many_by_many( array(
            'partner_id' => $partner_id
        ));

        $response = array(
            "message" => '',
            "result" => $list_locations
        );
        $this->api_success_output($response);
        return;
    }

    /**
     * add / edit receipt.
     */
    public function update_receipt(){
        $receipt_id = $this->input->post('receipt_id');
        if (empty($receipt_id)){
            $receipt_id = 0;
        }

        $this->form_validation->set_rules(mobile_validation_rules::$validation_add_partner_receipt_rules);
        if ($this->form_validation->run()) {
            $data = array(
                "partner_id" => $this->input->post('partner_id'),
                "location_id" => $this->input->post('location_id'),
                "date_of_receipt" => $this->input->post('date_of_receipt'),
                "net_amount" => $this->input->post('amount'),
                "description" => $this->input->post('description'),
                "local_file_path" => trim($this->input->post('local_file_path'))
            );

            try{
                $result = mobile_api::save_receipt($receipt_id, $data);
                $response = array(
                    "message" => lang('update_receipt_partner_success'),
                    "result" => $result
                );

                $this->api_success_output($response);
                return;
            } catch (ThirdPartyException $ex) {
                $data_response['message'] = $ex->getMessage();
                $this->api_error_output($data_response);
                return;
            }
        }else{
            $errors = $this->form_validation->error_json();
            $data_response['message'] = strip_tags($errors["message"]);
            $this->api_error_output($data_response);
            return;
        }
    }

    /**
     * upload resource file.
     */
    public function upload_file(){
        $type = $this->input->post('type');
        $client_file_name = $this->input->post('client_file_name');

        // upload partner receipt
        if($type == 'partner_receipt'){
            $server_file_path = "uploads/temp/";
            $result = Files::upload_file($server_file_path, $client_file_name);

            if($result['status']){
                $response = array(
                    "message" => $result['message'],
                    "result" => $result['local_file_path']
                );

                $this->api_success_output($response);
                return;
            }else{
                $data_response['message'] = strip_tags($result['message']);
                $this->api_error_output($data_response);
                return;
            }
        }
    }

    /**
     * view file.
     */
    public function view_file(){
        $local_file_name = $this->input->post('local_file_path');

        // Get extends file
        header('Content-Disposition: inline');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($local_file_name));
        header('Accept-Ranges: bytes');

        $ext = substr($local_file_name, strrpos($local_file_name, '.') + 1);
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

        readfile($local_file_name);
    }

    /**
     * delete receipt by id
     * @return type
     */
    public function delete_receipt(){
        $receipt_id = $this->input->post("receipt_id");
        if(empty($receipt_id)){
            $data_response['message'] = "Receipt must be required.";
            $this->api_error_output($data_response);
            return;
        }

        $this->partner_receipt_m->delete_by_many(array(
            "id" => $receipt_id
        ));

        $response = array(
            "message" => "This receipt is deleted successfull.",
            "result" => ""
        );

        $this->api_success_output($response);
        return;
    }

}

?>
