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
            'field' => 'partner_name',
            'label' => 'lang:partner_name',
            'rules' => 'required|validname|max_length[50]'
        ),
        array(
            'field' => 'company_name',
            'label' => 'lang:compnay_name',
            'rules' => 'required|validname|max_length[50]'
        ),
        array(
            'field' => 'partner_type',
            'label' => 'lang:partner_type',
            'rules' => ''
        ),
        array(
            'field' => 'invoicing_street',
            'label' => 'lang:invoicing_street',
            'rules' => 'required|validname|max_length[255]'
        ),
        array(
            'field' => 'invoicing_zipcode',
            'label' => 'lang:invoicing_zipcode',
            'rules' => 'required|max_length[20]'
        ),
        array(
            'field' => 'invoicing_city',
            'label' => 'lang:invoicing_city',
            'rules' => 'required|validname|max_length[60]'
        ),
        array(
            'field' => 'invoicing_region',
            'label' => 'lang:invoicing_region',
            'rules' => 'required|validname|max_length[255]'
        ),
        array(
            'field' => 'invoicing_country',
            'label' => 'lang:invoicing_country',
            'rules' => 'required|validname|max_length[30]'
        ),
        array(
            'field' => 'threhold_for_direct_prepay_charge',
            'label' => 'lang:threhold_for_direct_prepay_charge',
            'rules' => 'required|numeric'
        ),
        array(
            'field' => 'duration_rev_share',
            'label' => 'lang:duration_rev_share',
            'rules' => 'trim|numeric'
        ),
        array(
            'field' => 'customer_discount',
            'label' => 'lang:customer_discount',
            'rules' => 'trim|numeric'
        ),
        array(
            'field' => 'partner_domain',
            'label' => 'lang:partner_domain',
            'rules' => 'trim'
        ),
        array(
            'field' => 'rev_share_in_percent',
            'label' => 'lang:rev_share_in_percent',
            'rules' => 'trim'
        )
    );

    /**
     * Constructor method
     */
    public function __construct()
    {
        parent::__construct();

        // Load the required classes
        $this->load->model('partner_m');
        $this->load->model('price/pricing_template_m');
        $this->load->model('settings/countries_m');
        $this->load->model('addresses/location_m');

        $this->load->library('form_validation');

        $this->lang->load('partner');
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
            $query_result = $this->partner_m->get_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);

            // Process output data
            $total = $query_result ['total'];
            $datas = $query_result ['data'];

            // Get output response
            $response = $this->get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);

            $i = 0;
            foreach ($datas as $row) {
                $response->rows [$i] ['id'] = $row->partner_id;
                $partner_type = '';
                if ($row->partner_type == 1) {
                    $partner_type = "Marketing partner";
                } else if ($row->partner_type == 2) {
                    $partner_type = "Service partner";
                } else {
                    $partner_type = "Location partner";
                }

                $response->rows [$i] ['cell'] = array(
                    $row->partner_id,
                    $row->partner_code,
                    $row->partner_name,
                    $row->company_name,
                    $partner_type,
                    $row->invoicing_zipcode,
                    $row->invoicing_street,
                    $row->invoicing_city,
                    $row->invoicing_region,
                    $row->invoicing_country,
                    $row->threhold_for_direct_prepay_charge,
                    $row->duration_rev_share,
                    $row->rev_share_in_percent,
                    $row->customer_discount,
                    $row->partner_domain,
                    $row->partner_id
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
        $partner = new stdClass();
        $partner->partner_id = '';
        $partner->partner_code = '';
        $this->template->set_layout(FALSE);

        // Get all countries
        $countries = $this->countries_m->get_many_by_many(array(), '', false, array(
            'country_name' => 'ASC'
        ));

        if ($_POST) {
            $this->form_validation->set_rules($this->validation_rules);

            $name = $this->input->post('partner_name');
            $company_name = $this->input->post('company_name');

            $location_street = $this->input->post('location_street');
            $location_zipcode = $this->input->post('location_zipcode');
            $location_city = $this->input->post('location_city');
            $location_region = $this->input->post('location_region');
            $location_country = $this->input->post('location_country');

            $invoicing_street = $this->input->post('invoicing_street');
            $invoicing_zipcode = $this->input->post('invoicing_zipcode');
            $invoicing_city = $this->input->post('invoicing_city');
            $invoicing_region = $this->input->post('invoicing_region');
            $invoicing_country = $this->input->post('invoicing_country');

            $price_model = 0;
            $threhold_for_direct_prepay_charge = $this->input->post("threhold_for_direct_prepay_charge");

            if ($this->form_validation->run()) {
                // Insert data to database
                $partner_id = $this->partner_m->insert(array(
                    "partner_name" => $name,
                    "company_name" => $company_name,
                    "location_country" => $location_country,
                    "invoicing_street" => $invoicing_street,
                    "invoicing_zipcode" => $invoicing_zipcode,
                    "invoicing_city" => $invoicing_city,
                    "invoicing_region" => $invoicing_region,
                    "invoicing_country" => $invoicing_country,
                    "price_model" => $price_model,
                    "partner_type" => $this->input->post('partner_type'),
                    "threhold_for_direct_prepay_charge" => $threhold_for_direct_prepay_charge,
                    "rev_share_in_percent" => $this->input->post("rev_share_in_percent"),
                    "duration_rev_share" => $this->input->post("duration_rev_share"),
                    "customer_discount" => $this->input->post("customer_discount"),
                    "partner_domain" => $this->input->post("partner_domain")
                ));

                $partner_code = APUtils::generatePartnerCode($partner_id);
                $this->partner_m->update_by_many(array(
                    "partner_id" => $partner_id
                ), array(
                    "partner_code" => $partner_code
                ));

                $message = lang('add_partner_success');
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
            $partner->{$rule ['field']} = set_value($rule ['field']);
        }

        // Gets all pricing template
        $price_model = $this->pricing_template_m->get_all_public_template();

        // Display the current page
        $this->template->set('partner', $partner)->set("price_model", $price_model)->set("countries", $countries)->set('action_type', 'add')->build('admin/form');
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
        $partner = $this->partner_m->get_by("partner_id", $id);

        // Get all countries
        $countries = $this->countries_m->get_many_by_many(array(), '', false, array(
            'country_name' => 'ASC'
        ));

        if ($_POST) {
            // Set the validation rules
            $this->form_validation->set_rules($this->validation_rules);

            if ($this->form_validation->run()) {
                $partner_id = $this->input->post('partner_id');
                $name = $this->input->post('partner_name');
                $company_name = $this->input->post('company_name');

                $location_street = $this->input->post('location_street');
                $location_zipcode = $this->input->post('location_zipcode');
                $location_city = $this->input->post('location_city');
                $location_region = $this->input->post('location_region');
                $location_country = $this->input->post('location_country');

                $invoicing_street = $this->input->post('invoicing_street');
                $invoicing_zipcode = $this->input->post('invoicing_zipcode');
                $invoicing_city = $this->input->post('invoicing_city');
                $invoicing_region = $this->input->post('invoicing_region');
                $invoicing_country = $this->input->post('invoicing_country');

                $price_model = $this->input->post("price_model");
                $threhold_for_direct_prepay_charge = $this->input->post("threhold_for_direct_prepay_charge");

                // Save data to database
                $restul = $this->partner_m->update_by_many(array(
                    "partner_id" => $partner_id
                ), array(
                    "partner_name" => $name,
                    "company_name" => $company_name,
                    "location_street" => $location_street,
                    "location_zipcode" => $location_zipcode,
                    "location_city" => $location_city,
                    "location_region" => $location_region,
                    "location_country" => $location_country,
                    "invoicing_street" => $invoicing_street,
                    "invoicing_zipcode" => $invoicing_zipcode,
                    "invoicing_city" => $invoicing_city,
                    "invoicing_region" => $invoicing_region,
                    "invoicing_country" => $invoicing_country,
                    "price_model" => $price_model,
                    "partner_type" => $this->input->post('partner_type'),
                    "threhold_for_direct_prepay_charge" => $threhold_for_direct_prepay_charge,
                    "rev_share_in_percent" => $this->input->post("rev_share_in_percent"),
                    "duration_rev_share" => $this->input->post("duration_rev_share"),
                    "customer_discount" => $this->input->post("customer_discount"),
                    "partner_domain" => $this->input->post("partner_domain")
                ));

                $message = lang('edit_partner_success');
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
                $member->{$rule ['field']} = set_value($rule ['field']);
            }
        }

        // Gets all pricing template
        $price_model = $this->pricing_template_m->get_all_public_template();

        // Display the current page
        $this->template->set('partner', $partner)->set("price_model", $price_model)->set("countries", $countries)->set('action_type', 'edit')->build('admin/form');
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

        // delete partner
        $this->partner_m->delete_by("partner_id", $id);

        // output message.
        $message = lang('delete_partner_success');
        $this->success_output($message);
        return;
    }

    /**
     * Edit an existing user
     *
     * @param int $id
     *            The id of the user.
     */
    public function get_list_location_bypartner()
    {
        $this->template->set_layout(FALSE);
        $partner_id = $this->input->get_post("partner_id");

        // delete partner
        $locations = $this->location_m->get_many_by_many(array("partner_id" => $partner_id));

        // output message.
        echo json_encode($locations);
        return;
    }
}