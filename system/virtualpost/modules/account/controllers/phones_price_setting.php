<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Admin controller for the users module
 */
class phones_price_setting extends AccountSetting_Controller
{
    private $validation_price_phone_number_rules = array(
        array(
            'field' => 'one_time_fee_upcharge',
            'label' => 'Upcharge 1',
            'rules' => 'required'
        ),
        array(
            'field' => 'recurring_fee_upcharge',
            'label' => 'Upcharge 2',
            'rules' => 'required'
        ),
        array(
            'field' => 'per_min_fee_upcharge',
            'label' => 'Upcharge 3',
            'rules' => 'required'
        ),
    );
    private $validation_price_phone_number_remark_rules = array(
        array(
            'field' => 'remarks',
            'label' => 'remarks',
            'rules' => 'max_length[4000]'
        ),
    );
    
    
    private $validation_price_outbound_call_rules = array(
        array(
            'field' => 'usage_fee_upcharge',
            'label' => 'Upcharge 4',
            'rules' => 'required'
        )
    );

    
    
    /**
     * Constructor method
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->library(array(
            'form_validation',
            'price/price_api',
            'phones/phones_api'
        ));
        
        $this->lang->load('price/price');
    }
    
    /**
     * List all users
     */
    public function index() {
        $this->load->model('settings/countries_m');
        $array_condition = array();
        $country_code = $this->input->get_post("country_code");
        $price_type = $this->input->get_post("price_type");
        $isEnterpriseCustomer = APContext::isEnterpriseCustomer();
        
        if ($price_type == 'phone_number') {
            if (!empty($country_code)) {
                if ($isEnterpriseCustomer) {
                    $array_condition['pricing_phones_number_customer.country_code_3'] = $country_code;
                } else {
                    $array_condition['pricing_phones_number.country_code_3'] = $country_code;
                }
                
            }
        } else {
            if (!empty($country_code)) {
                if ($isEnterpriseCustomer) {
                    $array_condition['pricing_phones_outboundcalls_customer.country_code_3'] = $country_code;
                } else {
                    $array_condition['pricing_phones_outboundcalls.country_code_3'] = $country_code;
                }
            }
        }
        
        if ($this->is_ajax_request()) {
            $limit = isset($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);
            $customer_id = APContext::getParentCustomerCodeLoggedIn();
            $input_paging = $this->get_paging_input();
            $input_paging ['limit'] = $limit;
            if ($price_type == 'phone_number') {
                if ($isEnterpriseCustomer) {
                    $result = price_api::list_phone_number_price_for_customer($array_condition, $customer_id, $input_paging, $limit);
                } else {
                    $result = price_api::list_phone_number_price($array_condition, $input_paging, $limit);
                }
            } else {
                if ($isEnterpriseCustomer) {
                    $result = price_api::list_outbound_call_price_for_customer($array_condition, $customer_id, $input_paging, $limit);
                } else {
                    $result = price_api::list_outbound_call_price($array_condition, $input_paging, $limit);
                }
            }
            echo json_encode($result);

        } else {
            $list_country = phones_api::get_all_countries();
            $this->template->set("list_country", $list_country);
            $this->template->build('phones/prices_setting');
        }
    }
    
    /**
     * Edit an existing user
     *
     * @param int $id
     *            The id of the user.
     */
    public function edit_price_phone_number()
    {
        $this->template->set_layout(FALSE);
        $this->load->model('phones/pricing_phones_number_customer_m');
        $id = $this->input->get_post("id");
        $customer_id = APContext::getParentCustomerCodeLoggedIn();
        // Get the user's data
        $price_phone_number = $this->pricing_phones_number_customer_m->get_by_many(array(
            "id" => $id,
            "customer_id" => $customer_id
        ));

        if ($_POST) {
            // Set the validation rules
            $this->form_validation->set_rules($this->validation_price_phone_number_rules);

            if ($this->form_validation->run()) {
                // Save data to database
                $this->pricing_phones_number_customer_m->update_by_many(array(
                    "id" => $id,
                    "customer_id" => $customer_id
                ), array(
                    "one_time_fee_upcharge" => $this->input->get_post("one_time_fee_upcharge"),
                    "recurring_fee_upcharge" => $this->input->get_post("recurring_fee_upcharge"),
                    "per_min_fee_upcharge" => $this->input->get_post("per_min_fee_upcharge"),
                    "last_modified_date" => now()
                ));

                $message = lang('edit_success');
                $this->success_output($message);
                return;
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }

        // Display the current page
        $this->template->set('price_phone_number', $price_phone_number)->build('phones/prices_phone_number_form');
    }
    
    /**
     * Edit an existing user
     *
     * @param int $id
     *            The id of the user.
     */
    public function edit_price_phone_number_remark()
    {
        $this->template->set_layout(FALSE);
        $this->load->model('phones/pricing_phones_number_m');
        $id = $this->input->get_post("id");
        // Get the user's data
        $price_phone_number = $this->pricing_phones_number_m->get_by("id", $id);

        if ($_POST) {
            // Set the validation rules
            $this->form_validation->set_rules($this->validation_price_phone_number_remark_rules);

            if ($this->form_validation->run()) {
                // Save data to database
                $this->pricing_phones_number_m->update_by_many(array(
                    "id" => $id
                ), array(
                    "remarks" => $this->input->get_post("remarks"),
                    "last_modified_date" => now()
                ));

                $message = lang('edit_success');
                $this->success_output($message);
                return;
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }
        // Display the current page
        $this->template->set('price_phone_number', $price_phone_number)->build('admin/prices_phone_number_remark_form');
    }
    
    /**
     * Edit an existing user
     *
     * @param int $id
     *            The id of the user.
     */
    public function edit_price_outbound_call()
    {
        $this->template->set_layout(FALSE);
        $this->load->model('phones/pricing_phones_outboundcalls_customer_m');
        $id = $this->input->get_post("id");
        $customer_id = APContext::getParentCustomerCodeLoggedIn();
        
        // Get the user's data
        $price_outbound_call = $this->pricing_phones_outboundcalls_customer_m->get_by_many(array(
            "id" => $id,
            "customer_id" => $customer_id
        ));

        if ($_POST) {
            // Set the validation rules
            $this->form_validation->set_rules($this->validation_price_outbound_call_rules);

            if ($this->form_validation->run()) {
                // Save data to database
                $this->pricing_phones_outboundcalls_customer_m->update_by_many(array(
                    "id" => $id,
                    "customer_id" => $customer_id
                ), array(
                    "usage_fee_upcharge" => $this->input->get_post("usage_fee_upcharge"),
                    "last_modified_date" => now()
                ));

                $message = lang('edit_success');
                $this->success_output($message);
                return;
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }

        // Display the current page
        $this->template->set('price_outbound_call', $price_outbound_call)->build('phones/prices_outbound_call_form');
    }
    
    /**
     * Edit an existing user
     *
     * @param int $id
     *            The id of the user.
     */
    public function edit_price_outbound_call_remark()
    {
        $this->template->set_layout(FALSE);
        $this->load->model('phones/pricing_phones_outboundcalls_m');
        $id = $this->input->get_post("id");
        // Get the user's data
        $price_outbound_call = $this->pricing_phones_outboundcalls_m->get_by("id", $id);

        if ($_POST) {
            // Set the validation rules
            $this->form_validation->set_rules($this->validation_price_phone_number_remark_rules);

            if ($this->form_validation->run()) {
                // Save data to database
                $this->pricing_phones_outboundcalls_m->update_by_many(array(
                    "id" => $id
                ), array(
                    "remarks" => $this->input->get_post("remarks"),
                    "last_modified_date" => now()
                ));

                $message = lang('edit_success');
                $this->success_output($message);
                return;
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }

        // Display the current page
        $this->template->set('price_outbound_call', $price_outbound_call)->build('admin/prices_outbound_call_remark_form');
    }
}