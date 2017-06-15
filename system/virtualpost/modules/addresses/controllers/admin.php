<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Admin controller for the users module
 */
class Admin extends Admin_Controller
{
    /**
     * Validation for basic profile data. The rest of the validation is built by streams.
     *
     * @var array
     */
    private $validation_rules = array(
        array(
            'field' => 'location_name',
            'label' => 'lang:location_name',
            'rules' => 'required|validname|max_length[60]'
        ),
        array(
            'field' => 'partner_id',
            'label' => 'lang:partner_id',
            'rules' => 'required'
        ),
        array(
            'field' => 'pricing_template_id',
            'label' => 'lang:pricing_template_id',
            'rules' => 'required'
        ),
        array(
            'field' => 'street',
            'label' => 'lang:street',
            'rules' => 'required|validname|max_length[255]'
        ),
        array(
            'field' => 'postcode',
            'label' => 'lang:postcode',
            'rules' => 'required|postcode|max_length[10]'
        ),
        array(
            'field' => 'city',
            'label' => 'lang:city',
            'rules' => 'required|validname|max_length[255]'
        ),
        array(
            'field' => 'region',
            'label' => 'lang:region',
            'rules' => 'required|validname|max_length[255]'
        ),
        array(
            'field' => 'country',
            'label' => 'lang:country',
            'rules' => 'required|validname|max_length[255]'
        ),
        array(
            'field' => 'image_path',
            'label' => 'lang:image_path',
            'rules' => ''
        ),
        array(
            'field' => 'language',
            'label' => 'lang:language',
            'rules' => 'required|max_length[50]'
        )
    );

    /**
     * Constructor method
     */
    public function __construct()
    {
        parent::__construct();

        // Load the required classes
        $this->load->model('location_m');
        $this->load->model('location_pricing_m');
        $this->load->model('mailbox/postbox_m');
        $this->load->model('price/pricing_template_m');
        $this->load->model('partner/partner_m');
        $this->load->model('device/digital_devices_m');
        $this->load->model('price/pricing_m');
        $this->load->library('form_validation');

        $this->lang->load('address');
    }

    /**
     * List all users
     */
    public function index()
    {
        // Get input condition
        $array_condition = array();

        // If current request is ajax
        if ($this->is_ajax_request()) {

            // update limit into user_paging.
            $limit = isset($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);

            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging ['limit'] = $limit;

            // Call search method
            $query_result = $this->location_m->get_location_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);

            // Process output data
            $total = $query_result ['total'];
            $datas = $query_result ['data'];

            // Get output response
            $response = $this->get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);

            $i = 0;
            foreach ($datas as $row) {
                $response->rows [$i] ['id'] = $row->id;
                $response->rows [$i] ['cell'] = array(
                    $row->id,
                    $row->location_name,
                    $row->partner_code,
                    $row->partner_name,
                    $row->pricing_template_name,
                    $row->street,
                    $row->postcode,
                    $row->city,
                    $row->region,
                    $row->country_name,
                    $row->language,
                    $row->id
                );
                $i++;
            }

            echo json_encode($response);
        } else {
            // Display the current page
            $this->template->build('admin/index');
        }
    }

    /**
     * Method for handling different form actions
     */
    public function add()
    {
        $location = new stdClass();
        $location->id = '';
        $this->template->set_layout(FALSE);

        if ($_POST) {
            $this->form_validation->set_rules($this->validation_rules);
            $this->load->library('files/files');

            $location_name = $this->input->post('location_name');
            $street = $this->input->post('street');
            $postcode = $this->input->post('postcode');
            $city = $this->input->post('city');
            $region = $this->input->post('region');
            $country = $this->input->post('country');
            $image_path_data = $this->input->post('imagepath_filename');
            $partner_id = $this->input->post('partner_id');
            $pricing_template_id = $this->input->post('pricing_template_id');
            $language = $this->input->post('language');

            $image_path = '';
            if (!empty($image_path_data)) {
                if (!APUtils::endsWith($image_path_data, 'png') && !APUtils::endsWith($image_path_data, 'jpg')) {
                    $message = lang('image_location_invalid');
                    $this->error_output($message);
                    return;
                } else {
                    $image_path = Files::upload('location');
                }
            }

            if ($this->form_validation->run()) {
                // Insert data to database
                $this->location_m->insert(array(
                    "location_name" => $location_name,
                    "street" => $street,
                    "postcode" => $postcode,
                    "city" => $city,
                    "region" => $region,
                    "country" => $country,
                    "image_path" => $image_path,
                    "partner_id" => $partner_id,
                    "pricing_template_id" => $pricing_template_id,
                    'language' => $language
                ));

                $message = lang('add_location_success');
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
            $location->{$rule ['field']} = set_value($rule ['field']);
        }

        // Display the current page
        $list_partner = $this->partner_m->get_all();
        $pricing_templates = $this->pricing_template_m->get_all_public_template();
        $digital_devices = $this->digital_devices_m->get_all();

        $this->template->set('pricing_templates', $pricing_templates);
        $this->template->set('list_partner', $list_partner);
        $this->template->set('digital_devices', $digital_devices);
        $this->template->set('location', $location)->set('action_type', 'add')->build('admin/form');
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
        $location = $this->location_m->get_by("id", $id);
        if ($_POST) {
            // Set the validation rules
            $this->form_validation->set_rules($this->validation_rules);
            $this->load->library('files/files');

            if ($this->form_validation->run() === true) {
                $location_name = $this->input->post('location_name');
                $street = $this->input->post('street');
                $postcode = $this->input->post('postcode');
                $city = $this->input->post('city');
                $region = $this->input->post('region');
                $country = $this->input->post('country');
                $partner_id = $this->input->post('partner_id');
                $pricing_template_id = $this->input->post('pricing_template_id');
                $device_id = $this->input->post('device_id');
                $language = $this->input->post('language');

                $image_path_data = $this->input->post('imagepath_filename');
                $image_path = '';
                if ($image_path_data) {
                    $image_path = Files::upload('location');
                }
                // Insert data to database
                if ($image_path) {
                    $data = array(
                        "location_name" => $location_name,
                        "street" => $street,
                        "postcode" => $postcode,
                        "city" => $city,
                        "region" => $region,
                        "country" => $country,
                        "image_path" => $image_path,
                        "partner_id" => $partner_id,
                        "pricing_template_id" => $pricing_template_id,
                        'device_id' => $device_id,
                        'language' => $language
                    );
                } else {
                    $data = array(
                        "location_name" => $location_name,
                        "street" => $street,
                        "postcode" => $postcode,
                        "city" => $city,
                        "region" => $region,
                        "country" => $country,
                        "partner_id" => $partner_id,
                        "pricing_template_id" => $pricing_template_id,
                        'device_id' => $device_id,
                        'language' => $language
                    );
                }
                $this->location_m->update_by_many(array(
                    "id" => $id
                ), $data);

                $message = lang('edit_location_success');
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
            if ($this->input->post($rule ['field']) !== false) {
                $location->{$rule ['field']} = set_value($rule ['field']);
            }
        }
        $list_partner = $this->partner_m->get_all();
        $pricing_templates = $this->pricing_template_m->get_all_public_template();
        $digital_devices = $this->digital_devices_m->get_all();

        $this->template->set('digital_devices', $digital_devices);
        $this->template->set('pricing_templates', $pricing_templates);
        $this->template->set('list_partner', $list_partner);
        // Display the current page
        $this->template->set('location', $location)->set('action_type', 'edit')->build('admin/form');
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
        $id = $this->input->get_post("id");

        // 20141007 Start fix #408
        // gets location used.
        $location = $this->postbox_m->get_by_many(array(
            "location_available_id" => $id
        ));
        if ($location) {
            // output error message
            $message = lang('delete_location_fail');
            $this->error_output($message);
        } else {
            // delete location
            $this->location_m->delete_by("id", $id);

            // output message.
            $message = lang('delete_location_success');
            $this->success_output($message);
        }
        // 20141007 End fix #408
        return;
    }

    public function devices()
    {
        // Get input condition
        $list_access_location = APUtils::loadListAccessLocation();
        $list_access_location_id = array();
        if ($list_access_location && count($list_access_location) > 0) {
            foreach ($list_access_location as $location) {
                $list_access_location_id [] = $location->id;
            }
        }

        $array_condition = array(
            "location_id IN (" . implode(",", $list_access_location_id) . ")" => null
        );
		
        #1058 add multi dimension capability for admin
        $date_format = APUtils::get_date_format_in_user_profiles();
        
        // If current request is ajax
        if ($this->is_ajax_request()) {

            // update limit into user_paging.
            $limit = isset($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);

            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging ['limit'] = $limit;

            // Call search method
            $query_result = $this->digital_devices_m->get_device_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);

            // Process output data
            $total = $query_result ['total'];
            $rows = $query_result ['data'];

            // Get output response
            $response = $this->get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);

            $i = 0;
            foreach ($rows as $row) {
                $deviceLastPing = strtotime($row->last_ping_received);
                $status = 'online';
                if ($deviceLastPing < (time() - (10 * 60))) {
                    if ($deviceLastPing > $row->created_date) {
                        $deviceLastPing = $row->created_date;
                    }
                    $status = 'offline since ' . APUtils::viewDateFormat($deviceLastPing, $date_format.APConstants::TIMEFORMAT_OUTPUT02);
                }
                $response->rows [$i] ['id'] = $row->id;
                $response->rows [$i] ['cell'] = array(
                    $row->id,
                    $row->panel_code,
                    $row->location_name,
                    $row->description,
                    $status,
                );
                $i++;
            }

            echo json_encode($response);
        } else {
            // Display the current page
            $this->template->build('admin/location_devices');
        }
    }

    /**
     * location pricing.
     */
    public function location_pricing()
    {
        $this->load->model('addresses/location_customers_m');
        $this->load->library('addresses/addresses_api');
        $location_id = $this->input->get_post("location_id");
        
        $list_location_pricing = addresses_api::location_pricing(null, 0);
        if (!empty($location_id)) {
            $location = $this->location_m->get($location_id);
            $location_customer = $this->location_customers_m->get_by_many(array('location_id' => $location_id));
            $is_enterprise_location_open = $location_customer != null && $location->share_external_flag == APConstants::ON_FLAG;
            $this->template->set("is_enterprise_location_open", $is_enterprise_location_open);
            
            // $list_enterprise_location_pricing = addresses_api::location_pricing('Enterprise', 0);
            $this->template->set("enterprise_pricing_templates", $list_location_pricing['pricing_templates']);
        }
        

        $this->template->set('list_access_location', $list_location_pricing['list_access_location']);
        $this->template->set('location_id', $location_id);
        $this->template->set("pricing_templates", $list_location_pricing['pricing_templates']);
        
        $this->template->set("pricing_template_id", $list_location_pricing['pricing_template_id']);
        $this->template->set('pricing_map', $list_location_pricing['pricing_map']);
        $this->template->set('name_pricing_template', $list_location_pricing['name_pricing_template'] );
        $this->template->set('status', $list_location_pricing['status']);

        $this->template->build('admin/prices');
    }

    public function save_location()
    {
        $location_id = $this->input->get_post("location_id");
        $pricing_template_id = $this->input->get_post("pricing_template_id");
        
        if(!APContext::isSupperAdminUser() && !APContext::isAdminUser()){
            $this->error_output("You don't have permission to change it.");
            exit();
        }

        $result = $this->location_m->update_by_many(
            array(
                "id" => $location_id
            ),
            array(
                "pricing_template_id" => $pricing_template_id
            ));
        $this->success_output("");
        return;
    }

    public function change_pricing_template()
    {
        if ($this->is_ajax_request()) {
            $this->load->library('addresses/addresses_api');

            $locationID = $this->input->get_post('location_id');
            $pricingTemplateID = $this->input->get_post("pricing_template_id");
            $enterprisePricingTemplateID = $this->input->get_post("enterprise_pricing_template_id");
            
            $array_data =  array('next_pricing_template_id' => $pricingTemplateID);
            if (!empty($enterprisePricingTemplateID)) {
                $array_data['next_enterprise_pricing_template_id'] = $enterprisePricingTemplateID;
            }
            addresses_api::updateLocationByID($locationID,$array_data);

            $message = lang('apply_new_pricing_template_success');
            $this->success_output($message);
            return true;
        } else {
            $this->error_output('Invalid request');
            return false;
        }
    }
}