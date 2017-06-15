<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Admin controller for the users module
 */
class Admin extends Admin_Controller
{
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
            'field' => 'name',
            'label' => 'lang:name',
            'rules' => 'required|validname|max_length[100]'
        ),
        array(
            'field' => 'description',
            'label' => 'lang:description',
            'rules' => 'required|max_length[1000]'
        ),
        array(
            'field' => 'pricing_type',
            'label' => 'lang:pricing_type',
            'rules' => 'trim'
        ),
    );
    
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

        // Load the required classes
        $this->load->model('pricing_template_m');
        $this->load->model('price/pricing_m');
        $this->load->model('addresses/location_m');

        $this->load->library(array(
            'form_validation',
            'price/price_api',
            'phones/phones_api'
        ));

        $this->lang->load('price');
        $this->lang->load('addresses/address');
    }

    /**
     * List all users
     */
    public function index()
    {
        $type = $this->input->get_post('type');
        $array_condition = array();
        if(!empty($type)){
            $array_condition["pricing_type"] = $type;
        }
        if ($this->is_ajax_request()) {

            $limit = isset($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);
            
            $input_paging = $this->get_paging_input();
            $input_paging ['limit'] = $limit;
            $result = price_api::list_price_template($array_condition, $input_paging, $limit);
            echo json_encode($result['web_list_price_template']);

        } else {
            
            $this->template->build('admin/index');
        }
    }

    /**
     * Method for handling different form actions
     */
    public function add()
    {
        $price_template = new stdClass();
        $price_template->id = "";
        $this->template->set_layout(FALSE);

        if ($_POST) {
            $this->form_validation->set_rules($this->validation_rules);

            $name = $this->input->post('name');
            $description = $this->input->post('description');
            $rev_share_in_percent = $this->input->post("rev_share_in_percent");

            if ($this->form_validation->run()) {
                // Insert data to database
                $pricing_template_id = $this->pricing_template_m->insert(array(
                    "name" => $name,
                    "pricing_type" => $this->input->post('pricing_type'),
                    "description" => $description,
                ));

                // clone pricing data.
                $pricings = $this->pricing_m->get_many_by("pricing_template_id", APConstants::DEfAULT_PRICING_MODEL_TEMPLATE);
                foreach ($pricings as $price) {
                    $this->pricing_m->insert(array(
                        "account_type" => $price->account_type,
                        "item_name" => $price->item_name,
                        "item_value" => $price->item_value,
                        "item_description" => $price->item_description,
                        "item_unit" => $price->item_unit,
                        "pricing_template_id" => $pricing_template_id,
                        "type" => $price->type,
                        "rev_share_in_percent" => $price->rev_share_in_percent,
                        "billing_period" => $price->billing_period,
                        "contract_terms" => $price->contract_terms,
                        "show_customer_flag" => $price->show_customer_flag
                    ));
                }

                $message = lang('add_template_success');
                $this->success_output($message);
                return;
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }

        // Loop through each validation rule
        foreach ($this->validation_rules as $rule) {
            $price_template->{$rule ['field']} = set_value($rule ['field']);
        }

        // Display the current page
        $this->template->set('price_template', $price_template)->set('action_type', 'add')->build('admin/form');
    }

    /**
     * Edit an existing user
     *
     * @param int $id
     *            The id of the user.
     */
    public function edit()
    {
        $this->template->set_layout(FALSE);
        $id = $this->input->get_post("id");

        // Get the user's data
        $pricing_template = $this->pricing_template_m->get_by("id", $id);

        if ($_POST) {
            // Set the validation rules
            $this->form_validation->set_rules($this->validation_rules);

            if ($this->form_validation->run()) {
                $id = $this->input->post('id');
                $name = $this->input->post('name');
                $description = $this->input->post('description');
                $rev_share_in_percent = $this->input->post("rev_share_in_percent");

                // Save data to database
                $restul = $this->pricing_template_m->update_by_many(array(
                    "id" => $id
                ), array(
                    "name" => $name,
                    "pricing_type" => $this->input->post('pricing_type'),
                    "description" => $description,
                ));

                $message = lang('edit_template_success');
                $this->success_output($message);
                return;
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }

        // Display the current page
        $this->template->set('price_template', $pricing_template)->set('action_type', 'edit')->build('admin/form');
    }

    /**
     * Edit an existing user
     *
     * @param int $id
     *            The id of the user.
     */
    public function delete()
    {
        $this->template->set_layout(FALSE);
        $id = $this->input->get_post("id", 0);

        // Check pricing template is used.
        $number_uses = $this->location_m->count_by_many(array(
            "pricing_template_id" => $id
        ));

        // can not delete default template.
        if ($id == APConstants::DEfAULT_PRICING_MODEL_INVOICE || $number_uses > 0 || $id == 0) {
            // output message.
            $message = lang('delete_template_fail');
            $this->error_output($message);
            return;
        } else {
            // delete pricing
            //$this->pricing_m->delete_by("pricing_template_id", $id);

            // delete template
            //$this->pricing_template_m->delete_by("id", $id);
            
            // only delete logic the pricing template
            $this->pricing_template_m->update_by_many(array(
                "id" => $id
            ), array(
                "deleted_flag" => APConstants::ON_FLAG
            ));
        }

        // output message.
        $message = lang('delete_template_success');
        $this->success_output($message);
        return;
    }

    /**
     * edit prices
     */
    public function prices()
    {
        // Check authenticate.
        if (!APContext::isAdminUser()) {
            redirect('settings/prices');
        }

        // Gets pricing template id.
        $template_id = $this->input->get_post("id");
        $message = 'Contract terms and billing period are required.';
        $valid_data = true;
        
        // If user submit data
        if ($_POST) {
            $inputs = $this->input->post();
            // Validate input
            foreach ($inputs as $input_key => $input_value) {
                $arr_keys = explode('_', $input_key);
                $account_type = $arr_keys[count($arr_keys) - 1];
               
                // contract_terms or billing_period
                if ($account_type == "8" || $account_type == "9") {
                    if (empty($input_value)) {
                        $valid_data = false;
                        break;
                    }
                } 
            }
            
            // Only submit if valid data
            if ($valid_data) {
                $pricing_template_id = $inputs ['pricing_template_id'];
                foreach ($inputs as $input_key => $input_value) {
                    $arr_keys = explode('_', $input_key);
                    $account_type = $arr_keys[count($arr_keys) - 1];
                    $item_name = substr($input_key, 0, strlen($input_key) - strlen($account_type) - 1);

                    // Convert number from format #,## to #.##
                    $input_value = str_replace(',', '.', $input_value);
                    $input_value = str_replace('%', '', $input_value);

                    // Convert Unlimited to number
                    if ($item_name == 'trashing_items') {
                        if (strtolower($input_value) == 'unlimited') {
                            $input_value = '-1';
                        }
                    } else if ($item_name == 'storage') {
                        if (strtolower($input_value) == 'unlimited') {
                            $input_value = '0';
                        }
                    }

                    // Convert empty to No
                    if ($item_name == 'name_on_the_door') {
                        if (strtolower($input_value) == '') {
                            $input_value = 'No';
                        }
                    }

                    if ($account_type == "4") {
                        $this->pricing_m->update_by_many(array(
                            "item_name" => $item_name,
                            "pricing_template_id" => $pricing_template_id
                        ), array(
                            "type" => $input_value
                        ));
                    } else if ($account_type == "6") {
                        $this->pricing_m->update_by_many(array(
                            "item_name" => $item_name,
                            "pricing_template_id" => $pricing_template_id
                        ), array(
                            "rev_share_in_percent" => $input_value
                        ));
                    } else if ($account_type == "7") {
                        $this->pricing_m->update_by_many(array(
                            "item_name" => $item_name,
                            "pricing_template_id" => $pricing_template_id
                        ), array(
                            "show_customer_flag" => $input_value
                        ));
                    } else if ($account_type == "8") {
                        $this->pricing_m->update_by_many(array(
                            "item_name" => $item_name,
                            "pricing_template_id" => $pricing_template_id
                        ), array(
                            "contract_terms" => $input_value
                        ));
                    } else if ($account_type == "9") {
                        $this->pricing_m->update_by_many(array(
                            "item_name" => $item_name,
                            "pricing_template_id" => $pricing_template_id
                        ), array(
                            "billing_period" => $input_value
                        ));
                    } else if ($account_type == "10") {
                        $this->pricing_m->update_by_many(array(
                            "item_name" => $item_name,
                            "pricing_template_id" => $pricing_template_id
                        ), array(
                            "item_value_owner" => $input_value
                        ));
                    } else if ($account_type == "11") {
                        $this->pricing_m->update_by_many(array(
                            "item_name" => $item_name,
                            "pricing_template_id" => $pricing_template_id
                        ), array(
                            "item_value_special" => $input_value
                        ));
                    } else if ($account_type == "12") {
                        $this->pricing_m->update_by_many(array(
                            "item_name" => $item_name,
                            "pricing_template_id" => $pricing_template_id
                        ), array(
                            "item_value_owner_special" => $input_value
                        ));
                    } else {
                        $check = $this->pricing_m->get_by_many(array(
                            "account_type" => $account_type,
                            "item_name" => $item_name,
                            "pricing_template_id" => $pricing_template_id
                        ));
                        if(empty($check)){
                            $this->pricing_m->insert(array(
                                "account_type" => $account_type,
                                "item_name" => $item_name,
                                "pricing_template_id" => $pricing_template_id,
                                "item_value" => $input_value
                            ));
                        }else{
                            $this->pricing_m->update_by_many(array(
                                "account_type" => $account_type,
                                "item_name" => $item_name,
                                "pricing_template_id" => $pricing_template_id
                            ), array(
                                "item_value" => $input_value
                            ));
                        }
                    }
                }

                $this->session->set_flashdata('success', lang('success'));
                redirect('admin/price');
            }
        }
        $this->template->set("valid_data", $valid_data);
        $this->template->set("message", $message);
        // set data
        $type_data = APUtils::getVATListByCustomer();

        $this->template->set("type_data", $type_data);

        // Get don gia cua tat ca cac loai account type
        $pricings = $this->pricing_m->get_many_by("pricing_template_id", $template_id);
        $pricing_template = $this->pricing_template_m->get_by('id', $template_id);
        $pricing_map = array();
        foreach ($pricings as $price) {
            if (!array_key_exists($price->account_type, $pricing_map)) {
                $pricing_map [$price->account_type] = array();
            }
            $pricing_map [$price->account_type] [$price->item_name] = $price;
        }

        // Render the layout
        $this->template->set('pricing_map', $pricing_map)->set("pricing_template", $pricing_template)->build('admin/prices');
    }
    
    /**
     * List all users
     */
    public function phones() {
        $this->load->model('settings/countries_m');
        $array_condition = array();
        $country_code = $this->input->get_post("country_code");
        $price_type = $this->input->get_post("price_type");
        
        if ($price_type == 'phone_number') {
            if (!empty($country_code)) {
                $array_condition['pricing_phones_number.country_code_3'] = $country_code;
            }
        } else {
            if (!empty($country_code)) {
                $array_condition['pricing_phones_outboundcalls.country_code_3'] = $country_code;
            }
        }
        
        if ($this->is_ajax_request()) {

            $limit = isset($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);
            
            $input_paging = $this->get_paging_input();
            $input_paging ['limit'] = $limit;
            if ($price_type == 'phone_number') {
                $result = price_api::list_phone_number_price($array_condition, $input_paging, $limit);
            } else {
                $result = price_api::list_outbound_call_price($array_condition, $input_paging, $limit);
            }
            echo json_encode($result);

        } else {
            $list_country = phones_api::get_all_countries();
            $this->template->set("list_country", $list_country);
            $this->template->build('admin/prices_phone');
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
        $this->load->model('phones/pricing_phones_number_m');
        $id = $this->input->get_post("id");
        // Get the user's data
        $price_phone_number = $this->pricing_phones_number_m->get_by("id", $id);

        if ($_POST) {
            // Set the validation rules
            $this->form_validation->set_rules($this->validation_price_phone_number_rules);

            if ($this->form_validation->run()) {
                // Save data to database
                $this->pricing_phones_number_m->update_by_many(array(
                    "id" => $id
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
        $this->template->set('price_phone_number', $price_phone_number)->build('admin/prices_phone_number_form');
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
        $this->load->model('phones/pricing_phones_outboundcalls_m');
        $id = $this->input->get_post("id");
        // Get the user's data
        $price_outbound_call = $this->pricing_phones_outboundcalls_m->get_by("id", $id);

        if ($_POST) {
            // Set the validation rules
            $this->form_validation->set_rules($this->validation_price_outbound_call_rules);

            if ($this->form_validation->run()) {
                // Save data to database
                $this->pricing_phones_outboundcalls_m->update_by_many(array(
                    "id" => $id
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
        $this->template->set('price_outbound_call', $price_outbound_call)->build('admin/prices_outbound_call_form');
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