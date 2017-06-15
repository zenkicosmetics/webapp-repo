<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Roles controller for the groups module
 */
class Incoming extends Admin_Controller
{
    /**
     * Constructor method
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->library(array(
            'price/price_api',
            'mailbox/mailbox_api',
            'scans/scans_api',
            'scans/incoming_api',
            'invoices/invoices',
            'form_validation'
        ));

        // Load model
        $this->load->model('envelope_m');
        $this->load->model('email/email_m');
        $this->load->model('customers/customer_m');
        $this->load->model('addresses/customers_address_m');
        $this->load->model('mailbox/postbox_m');
        $this->load->model('mailbox/postbox_setting_m');
        $this->load->model('scans/envelope_file_m');
        $this->load->model('scans/envelope_summary_month_m');
        $this->load->model('price/pricing_m');
        $this->load->model('scans/setting_m');
        $this->load->model('scans/envelope_properties_m');
        $this->load->model('envelope_pdf_content_m');
        $this->load->model('addresses/location_m');
        $this->load->model('mailbox/envelope_customs_m');
        $this->load->model('settings/countries_m');

        // Load language
        $this->lang->load('scans');

        // Validation rules
        $this->validation_rules = array(
            array(
                'field' => 'from_customer_name',
                'label' => 'From Address',
                'rules' => 'trim|required|max_length[255]'
            ),
            array(
                'field' => 'customer_id',
                'label' => 'To Address',
                'rules' => 'trim|required|number'
            ),
            array(
                'field' => 'postbox_id',
                'label' => 'To Address',
                'rules' => 'trim|number'
            ),
            array(
                'field' => 'type',
                'label' => 'Type',
                'rules' => 'trim|required|max_length[255]'
            ),
            array(
                'field' => 'weight',
                'label' => 'Weight',
                'rules' => 'trim|required|max_length[10]|numeric|greater_than[0]'
            )
        );
    }

    /**
     * Display all incomming envelope.
     */
    public function index()
    {
        // Gets location
        $location = $this->input->get_post("location_id", "");

        #1058 add multi dimension capability for admin
        $length_unit = APUtils::get_length_unit_in_user_profiles(FALSE);
        $weight_unit = APUtils::get_weight_unit_in_user_profiles(FALSE);

        $checkTypeAvailable = array();
        // #481: location selection.
        if ($location || $_POST) {
            APContext::updateLocationUserSetting($location);
            $checkTypeAvailable = Settings::get_list_by_location($location);

        } else {
            $location = APContext::getLocationUserSetting();
        }

        $list_access_location = APUtils::loadListAccessLocation();
        $this->template->set('list_access_location', $list_access_location);
        $this->template->set("location_id", $location);
        $this->template->set("checkTypeAvailable", $checkTypeAvailable);
        $this->template->set("length_unit", $length_unit);
        $this->template->set("weight_unit", $weight_unit);

        // Display the current page
        $this->template->set('header_title', lang('header:incomming_title'))->build('incoming/index');
    }

    /**
     * Using for lookup customer_name, postbox name, postbox company
     */
    public function auto_postbox()
    {
        ci()->load->library(array(
            'scans/incoming_api'
        ));

        $term = trim(strip_tags($_GET['term']));
        $location_id = $this->input->get_post("location_id", "");

        $matches = incoming_api::auto_postbox($term, $location_id);

        print json_encode($matches);
    }

    /**
     * Create a new group role
     */
    public function search()
    {
        // If current request is ajax
        if ($this->is_ajax_request()) {
            $customer_id = $this->input->post('customer_id');
            $from_customer_name = $this->input->post('from_customer_name');
            $type = $this->input->post('type');
            $weight = $this->input->post('weight');
            $term = "'%" . $this->input->post("customer_id_auto") . "%'";
            $input_location_id = $this->input->post('location_available_id');
            $list_access_location = APUtils::loadListAccessLocation();
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

            // update limit into user_paging.
            $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);

            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging['limit'] = $limit;

            $response = scans_api::getIncomingList($customer_id, $from_customer_name, $type, $weight, $term, $list_filter_location_id, $input_paging, $limit);
            echo json_encode($response['web_incomming_list']);
        } else {
            // Display the current page
            $this->template->set('header_title', lang('header:list_group_title'))->build('admin/index');
        }
    }

    /**
     * Add a new incoming item (into the table "envelopes")
     */
    public function add()
    {
        ini_set('max_execution_time', 600);
        $this->template->set_layout(FALSE);

        $envelope = new stdClass();
        $envelope->id = '';
        if ($_POST) {
            $this->form_validation->set_rules($this->validation_rules);
            $invoice_flag = $this->input->post('invoice_flag');
            $postbox_id = $this->input->post('postbox_id');
            if (empty($invoice_flag)) {
                $invoice_flag = 0;
            }
            if ($this->form_validation->run()) {

                $customer_id        = $this->input->post('customer_id');
                $from_customer_name = $this->input->post('from_customer_name');
                $to                 = $this->input->post('to');
                $weight             = $this->input->post('weight');
                $type               = $this->input->post('type');
                $width              = (float)$this->input->post('width');
                $height             = (float)$this->input->post('height');
                $length             = (float)$this->input->post('length');
                $labelValue         = $this->input->get_post('labelValue');

                $envelopeType  = incoming_api::get_type($type, $labelValue);
                if($envelopeType['data']->Alias02 == 'Package'){
                    $message = '';
                    if(empty($width) ){
                        $message .= 'Width field is required and must be a number.<br/>';
                    }

                    if(empty($height)){
                        $message .= 'Height field is required and must be a number.<br/>';
                    }

                    if(empty($length) ){
                        $message .= 'Length field is required and must be a number.<br/>';
                    }

                    if($message){
                        $this->error_output($message);
                        return;
                    }
                }

                $result = incoming_api::add_incomming($customer_id, $from_customer_name, $to,  $postbox_id, $type, $labelValue, $width, $height, $weight, $length);

                if($result['status']){

                    $message = sprintf(lang('incomming.add_success'), $this->input->post('to'));
                    $this->success_output($message, array('id' => $result['result']->envelope_id, 'envelope_code' => $result['result']->envelope_code));
                    return;
                }
                else{

                    $message = sprintf(lang('incomming.add_error'), $this->input->post('to'));
                    $this->error_output($message);
                    return;
                }

            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }

        // Loop through each validation rule
        foreach ($this->validation_rules as $rule) {
            $envelope->{$rule['field']} = set_value($rule['field']);
        }

        $this->template->set('envelope', $envelope)
            ->set('action_type', 'add')
            ->build('admin/form');
    }

    /**
     * Send email declare customs
     */
    private function send_email_declare_customs($customer_setting)
    {
        $to_email = $customer_setting->email;
        $from_email = $this->config->item('EMAIL_FROM');

        // Send email confirm for user
        $email_template = $this->email_m->get_by('slug', APConstants::declare_customs_notification);

        $data = array(
            "full_name" => $customer_setting->user_name,
            "site_url" => APContext::getFullBalancerPath() . 'mailbox/index?declare_customs=1'
        );

        $content = APUtils::parserString($email_template->content, $data);
        try {
            MailUtils::sendEmail($from_email, $to_email, $email_template->subject, $content);
        } catch (Exception $e) {
            log_message($e);
        }
    }

    /**
     * Delete an incoming item (into the table "envelope")
     */
    public function delete()
    {
        ci()->load->library('scans/scans_api');
        ci()->load->library('invoices/invoices');

        $id = $this->input->get_post("id");
        $envelope = $this->envelope_m->get_by_many(array(
            "id" => $id
        ));
        if (empty($envelope)) {
            $message = sprintf(lang('envelope.delete_success'));
            $this->success_output($message);
            return;
        }
        $customer_id = $envelope->to_customer_id;
        $admin_id = APContext::getAdminIdLoggedIn();
        // Insert completed activity (Registered incoming)
        scans_api::insertCompleteItem($id, APConstants::INCOMMING_DELETED_ACTIVITY_TYPE, APConstants::TRIGGER_BY_ADMIN, $admin_id);

        // Revert charge for customer
        APUtils::revert_envelope_incomming($id);
        APUtils::revert_envelope_scan($id);
        APUtils::revert_envelope_shipping($id);

        // Delete document
        APUtils::delete_envelope_by_id($id, $admin_id);

        // completed delete envelopes.
        $this->envelope_m->update_by_many(array(
            "id" => $id
        ), array(
            "trash_flag" => APConstants::ON_FLAG,
            "trash_date" => now(),
            "deleted_flag" => APConstants::ON_FLAG,
            "completed_flag" => APConstants::ON_FLAG,
            "completed_by" => $admin_id,
            "current_storage_charge_fee_day" => 0,
            "completed_date" => now(),
            "collect_shipping_flag" => null,
            'direct_shipping_flag' => null,
            'package_id' => null,
            'envelope_scan_flag' => null,
            'item_scan_flag' => null
        ));

        // Update
        ci()->invoices->cal_storage_summary($customer_id);

        $message = sprintf(lang('envelope.delete_success'));
        $this->success_output($message);
        return;
    }

    public function get_type()
    {
        $this->template->set_layout(FALSE);
        $actualValue = $this->input->get('actualValue');
        $labelValue  = $this->input->get('labelValue');

        $result  = incoming_api::get_type($actualValue, $labelValue);

        if ($result['status']) {
            $this->success_output('', $result['data']);
        } else {
            $this->error_output('');
        }
        return;
    }
}
