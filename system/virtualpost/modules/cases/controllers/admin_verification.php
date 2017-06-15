<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Admin controller for the users module
 */
class admin_verification extends Admin_Controller {

    /**
     * Validation for basic profile
     * data.
     * The rest of the validation is
     * built by streams.
     * 
     * @var array
     */
    private $validation_rules = array(
        array(
            'field' => 'country_code',
            'label' => 'Country of Postbox location',
            'rules' => ''
        ),
        array(
            'field' => 'risk_class',
            'label' => 'Risk',
            'rules' => 'required'
        ),
        array(
            'field' => 'invoice_address_verification',
            'label' => 'Verification Address',
            'rules' => ''
        ),
        array(
            'field' => 'private_postbox_verification',
            'label' => 'Verification Private Postbox',
            'rules' => ''
        ),
        array(
            'field' => 'business_postbox_verification',
            'label' => 'Verification Business Postbox',
            'rules' => ''
        ),
        array(
            'field' => 'location_id',
            'label' => 'Postbox Location',
            'rules' => ''
        ),
        array(
            'field' => 'setting_type',
            'label' => 'Setting Type',
            'rules' => ''
        ),
        array(
            'field' => 'list_case_number',
            'label' => 'Cases Number',
            'rules' => 'required'
        ),
        array(
            'field' => 'is_user_company',
            'label' => 'User company',
            'rules' => 'trim'
        )
    );

    /**
     * Constructor method
     */
    public function __construct() {
        parent::__construct();

        // Load the required classes
        $this->load->model('settings/countries_m');
        $this->load->model('addresses/location_m');
        $this->load->model('cases/cases_verification_settings_m');
        $this->load->library('form_validation');
        $this->lang->load('cases/cases');
    }

    /**
     * List all devices.
     * Using for device panel
     */
    public function index() {
        $enquiry = $this->input->get_post('enquiry');
        $setting_type = $this->input->get_post('setting_type');
        // If current request is ajax
        if ($this->is_ajax_request()) {
            $array_condition = array();
            if (!empty($enquiry)) {
                $array_condition['country_name LIKE '] = '%' . $enquiry . '%';
            }
            if (!empty($setting_type)) {
                $array_condition['setting_type'] = $setting_type;
            }

            // update limit into user_paging.
            $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);

            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging['limit'] = $limit;

            if ($input_paging['sort_column'] == 'location_name') {
                $input_paging['sort_column'] = 'location_id';
            }

            // Call search method
            $query_result = $this->cases_verification_settings_m->get_cases_paging($array_condition, $input_paging['start'], $input_paging['limit'], $input_paging['sort_column'], $input_paging['sort_type']);

            // Process output data
            $total = $query_result['total'];
            $datas = $query_result['data'];

            // Get output response
            $response = $this->get_paging_output($total, $input_paging['limit'], $input_paging['page']);
            $locations = $this->location_m->get_all();
            $map_locations = array();
            foreach ($locations as $location) {
                $map_locations[$location->id] = $location->location_name;
            }
            $i = 0;
            foreach ($datas as $row) {
                $response->rows[$i]['id'] = $row->id;
                $location_name = '';
                if (array_key_exists($row->location_id, $map_locations)) {
                    $location_name = $map_locations[$row->location_id];
                }
                
                $user_company = $row->is_user_company == '2' ? "Yes" : ($row->is_user_company == '1') ? "No" : "Any";
                $response->rows[$i]['cell'] = array(
                    $row->id,
                    $row->country_name,
                    $user_company,
                    $location_name,
                    $row->risk_class,
                    $row->invoice_address_verification,
                    $row->postbox_name_filled,
                    $row->private_postbox_verification,
                    $row->business_postbox_verification,
                    $row->setting_type,
                    $this->get_case_instance_name_from_id($row->list_case_number),
                    $row->id
                );
                $i ++;
            }

            echo json_encode($response);
        } else {

            // Display the current page
            $this->template->build('admin/verification/index');
        }
    }

    /**
     * Edit an existing user
     * 
     * @param int $id
     *            The id of the user.
     */
    public function edit() {
        $this->template->set_layout(FALSE);
        $id = $this->input->get_post("id");
        $setting_type = $this->input->get_post('setting_type');
        // Get the user's data
        $country = $this->cases_verification_settings_m->get_by("id", $id);
        if (empty($country)) {
            $country = new stdClass();
            $country->id = 0;
            $this->template->set('action_type', 'add');
            // Loop through each validation rule
            foreach ($this->validation_rules as $rule) {
                $country->{$rule['field']} = set_value($rule['field']);
            }
            $country->setting_type = $setting_type;
        } else {
            $this->template->set('action_type', 'edit');
        }
        if ($_POST) {
            // Set the validation rules
            $this->form_validation->set_rules($this->validation_rules);

            $list_case_number_input = $this->input->get_post("list_case_number");
            $list_case_number = '';
            if (!empty($list_case_number_input)) {
                $list_case_number = implode(",", $list_case_number_input);
            }

            if ($this->form_validation->run()) {
                $country_check = $this->cases_verification_settings_m->get_by("id", $id);
                $invoice_address_verification = $this->input->get_post("invoice_address_verification");
                $private_postbox_verification = $this->input->get_post("private_postbox_verification");
                $business_postbox_verification = $this->input->get_post("business_postbox_verification");
                $postbox_name_filled = $this->input->get_post("postbox_name_filled");
                $location_id = $this->input->get_post("location_id");
                $setting_type = $this->input->get_post("setting_type");
                
                $country_code = $this->input->get_post("country_code");
                $phone_country_code = $this->input->get_post("phone_country_code");
                $is_user_company = $this->input->get_post("is_user_company");

                // Postbox
                if ($setting_type == '2') {
                    if (empty($location_id)) {
                        $this->error_output('Location postbox is required input');
                        return;
                    }
                }

                if (empty($location_id)) {
                    $location_id = -1;
                }
                
                $data = array(
                    "risk_class" => $this->input->get_post("risk_class"),
                    "country_code" => $country_code,
                    "invoice_address_verification" => $invoice_address_verification,
                    "private_postbox_verification" => $private_postbox_verification,
                    "business_postbox_verification" => $business_postbox_verification,
                    "postbox_name_filled" => $postbox_name_filled,
                    "location_id" => $location_id,
                    "setting_type" => $setting_type,
                    "list_case_number" => $list_case_number
                );
                
                // change field if type is phone number.
                if($setting_type == '3'){
                    $data['country_code'] = $phone_country_code;
                    $data['is_user_company'] = $is_user_company;
                }
                
                if (!empty($country_check)) {
                    $this->cases_verification_settings_m->update_by_many(array(
                        "id" => $id 
                    ), $data);
                } else {
                    $this->cases_verification_settings_m->insert( $data);
                }
                $message = lang('save_country_success');
                $this->success_output($message);
                return;
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }

        // Get list country
        $countries = $this->countries_m->get_many_by_many(array(
            'country_code <>' => "''"
        ));
        $locations = $this->location_m->get_all();

        // Gets list case selected.
        $list_cases = array();
        if ($country->list_case_number) {
            $arrList = explode(',', $country->list_case_number);
            $this->db->from("cases_instance");
            $this->db->where_in("id", $arrList);
            $cases_instances = $this->db->get()->result();
            foreach ($cases_instances as $r) {
                $tmp = new stdClass();
                $tmp->id = $r->id;
                $tmp->name = $r->case_instance_name;
                $list_cases[] = $tmp;
                unset($tmp);
            }
        }

        // List case number config
        $list_case_config = CaseUtils::get_list_cases_number_config();
        $list_case_config_new = array();
        $list_tmp = array();
        foreach ($list_cases as $case) {
            $list_tmp[] = $case->id;
        }
        foreach ($list_case_config as $l) {
            if (!in_array($l->id, $list_tmp)) {
                $list_case_config_new[] = $l;
            }
        }

        // get country of phone
        // Get list country
        $phone_countries = $this->countries_m->get_many_by_many(array(
            "country_code_3 <> " => "''",
            "country_code_3 is not null" => null
        ));

        $this->template->set('list_case_config', $list_case_config_new);
        $this->template->set('list_cases', $list_cases);
        $this->template->set('countries', $countries);
        $this->template->set('locations', $locations);
        $this->template->set('country', $country);
        $this->template->set('phone_countries', $phone_countries);

        // Display the current page
        $this->template->build('admin/verification/form');
    }

    /**
     * Delete condition
     */
    public function delete() {
        $condition_id = $this->input->get_post("id");

        $this->cases_verification_settings_m->delete_by_many(array(
            'id' => $condition_id
        ));

        $message = sprintf(lang('delete_country_success'));
        $this->success_output($message);
    }

    /**
     * get case instance name from string id
     * @param unknown $string_id
     * @return string
     */
    private function get_case_instance_name_from_id($string_id) {
        $string_result = "";
        if (!empty($string_id)) {
            $arrList = explode(',', $string_id);
            $this->db->select("case_instance_name");
            $this->db->from("cases_instance");
            $this->db->where_in("id", $arrList);
            $cases_instances = array();
            foreach ($this->db->get()->result() as $row) {
                $cases_instances[] = $row->case_instance_name;
            }
            $string_result = implode(", ", $cases_instances);
        }

        return $string_result;
    }

}
