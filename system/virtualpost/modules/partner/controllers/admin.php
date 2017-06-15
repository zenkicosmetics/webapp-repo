<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Admin controller for the users module
 */
class Admin extends Admin_Controller
{
    /**
     * Validation for basic profile data.
     * The rest of the validation is built by streams.
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
            'rules' => ''
        ),
        array(
            'field' => 'duration_rev_share',
            'label' => 'lang:duration_rev_share',
            'rules' => 'trim'
        ),
        array(
            'field' => 'rev_share_ad',
            'label' => 'lang:rev_share',
            'rules' => 'trim'
        ),
        array(
            'field' => 'customer_discount',
            'label' => 'lang:customer_discount',
            'rules' => 'trim'
        ),
        array(
            'field' => 'duration_customer_discount',
            'label' => 'lang:duration_customer_discount',
            'rules' => 'trim'
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
        ),
        array(
            'field' => 'registration',
            'label' => 'lang:registration',
            'rules' => 'trim|numeric'
        ),
        array(
            'field' => 'activation',
            'label' => 'lang:activation',
            'rules' => 'trim'
        ),
        array(
            'field' => 'email',
            'label' => 'email',
            'rules' => 'trim'
        ),
        array(
            'field' => 'main_contact_point',
            'label' => 'contact person',
            'rules' => 'trim'
        ),
        array(
            'field' => 'bonus_month',
            'label' => 'bonus_month',
            'rules' => 'trim'
        ),
        array(
            'field' => 'bonus_location',
            'label' => 'location_name',
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
        $this->load->model('users/user_m');
        $this->load->model('users/group_user_m');

        $this->load->model('price/pricing_template_m');
        $this->load->model('settings/countries_m');
        $this->load->model('addresses/location_m');
        $this->load->model('partner/partner_marketing_profile_m');
        $this->load->model('cases/cases_service_partner_m');
        $this->load->model('addresses/location_m');

        $this->load->library('form_validation');

        $this->lang->load('partner');
        $this->lang->load('cases/cases');
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
            $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);

            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging['limit'] = $limit;

            // Call search method
            $query_result = $this->partner_m->get_partner_paging($array_condition, $input_paging['start'], $input_paging['limit'],
                $input_paging['sort_column'], $input_paging['sort_type']);

            // Process output data
            $total = $query_result['total'];
            $datas = $query_result['data'];

            // Get output response
            $response = $this->get_paging_output($total, $input_paging['limit'], $input_paging['page']);

            $i = 0;
            foreach ($datas as $row) {
                $response->rows[$i]['id'] = $row->partner_id;
                if ($row->partner_type == 1) {
                    $partner_type = "Marketing partner";
                } else if ($row->partner_type == 2) {
                    $partner_type = "Service partner";
                    
                    $row->duration_rev_share = "";
                    $row->rev_share_in_percent = "";
                    $row->customer_discount = "";
                    $row->partner_domain = "";
                } else {
                    $partner_type = "Location partner";
                    
                    $row->duration_rev_share = "";
                    $row->rev_share_in_percent = "";
                    $row->customer_discount = "";
                    $row->partner_domain = "";
                }

                $response->rows[$i]['cell'] = array(
                    $row->partner_id,
                    $row->partner_type,
                    $row->partner_code,
                    $row->partner_name,
                    $row->company_name,
                    $partner_type,
                    $row->invoicing_zipcode,
                    $row->invoicing_street,
                    $row->invoicing_city,
                    $row->invoicing_region,
                    $row->country_name,
                    $row->duration_rev_share,
                    $row->rev_share_in_percent,
                    $row->customer_discount,
                    $row->partner_domain,
                    $row->partner_id
                );
                $i++;
            }

            echo json_encode($response);
            return;
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
            $partner_type = $this->input->post('partner_type');
            if ($partner_type == APConstants::PARTNER_SERVICE_TYPE) {
                $this->validation_rules[] = array(
                    'field' => 'email',
                    'label' => 'lang:email',
                    'rules' => 'required|email|max_length[250]'
                );
                $this->validation_rules[] = array(
                    'field' => 'phone',
                    'label' => 'lang:phone',
                    'rules' => 'required|phone_number|max_length[20]'
                );
                $this->validation_rules[] = array(
                    'field' => 'main_contact_point',
                    'label' => 'lang:main_contact_point',
                    'rules' => 'required|max_length[250]'
                );
            }
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
            // $threhold_for_direct_prepay_charge = $this->input->post("threhold_for_direct_prepay_charge");

            if ($this->form_validation->run()) {

                // Start transaction
                //$this->partner_m->db->trans_begin();

                try {
                    // Insert data to database
                    $partner_id = $this->partner_m->insert( array(
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
                            "rev_share_in_percent" => $this->input->post("rev_share_in_percent")
                        ));

                    $partner_code = APUtils::generatePartnerCode($partner_id);
                    $this->partner_m->update_by_many(array(
                        "partner_id" => $partner_id
                    ), array(
                        "partner_code" => $partner_code
                    ));

                    if ($partner_type == APConstants::PARTNER_MARKETING_TYPE) {
                        $bonus_month=  $this->input->post("bonus_month");
                        $bonus_location=  $this->input->post("bonus_location");
                        $bonus_flag = !empty($bonus_month) && !empty($bonus_location) ? 1 : 0;
                        $this->partner_marketing_profile_m->insert(
                            array(
                                "partner_id" => $partner_id,
                                "rev_share_ad" => $this->input->post("rev_share_ad"),
                                "duration_rev_share" => $this->input->post("duration_rev_share"),
                                "customer_discount" => $this->input->post("customer_discount"),
                                "duration_customer_discount" => $this->input->post("duration_customer_discount"),
                                "partner_domain" => $this->input->post("partner_domain"),
                                "registration" => $this->input->post("registration"),
                                "activation" => $this->input->post("activation"),
                                "bonus_month" => $bonus_month,
                                "bonus_flag" => $bonus_flag,
                                "bonus_location" => $bonus_location
                            ));
                    } else if ($partner_type == APConstants::PARTNER_SERVICE_TYPE || $partner_type == APConstants::PARTNER_LOCATION_TYPE) {
                        $main_contact_point = $this->input->post("main_contact_point");
                        $email = $this->input->post("email");
                        $phone = $this->input->post("phone");
                        /*
                        $user_name = $this->input->post("user_name");
                        $password = $this->input->post("password");
                        */
                        if (empty($main_contact_point)) {
                            $this->error_output('Main contact point field is required.');
                            return;
                        }
                        if (empty($email)) {
                            $this->error_output('Service partner email field is required.');
                            return;
                        }
                        if (empty($phone)) {
                            $this->error_output('Service partner phone field is required.');
                            return;
                        }
                        /*
                        if (empty($user_name)) {
                            $this->error_output('Service partner user name field is required.');
                            return;
                        }
                        if (empty($password)) {
                            $this->error_output('Service partner password field is required.');
                            return;
                        }
                        */

                        // Insert partner information
                        $this->cases_service_partner_m->insert(
                            array(
                                "partner_id" => $partner_id,
                                "partner_name" => $name,
                                "main_contact_point" => $main_contact_point,
                                "email" => $this->input->post("email"),
                                "phone" => $this->input->post("phone"),
                                "created_date" => now()
                            ));

                        // Insert user login for service partner
                        /*
                        $group_id = APConstants::GROUP_SERVICE_PARTNER_ADMIN;
                        $user_id = $this->ion_auth->register($user_name, $password, $email, $group_id,
                            array(
                                "display_name" => $name,
                                "first_name" => $name,
                                "last_name" => '',
                                "active" => APConstants::ON_FLAG,
                                "partner_id" => $partner_id,
                                "location_available_id" => ''
                            ));

                        // /$update_data ['partner_id'] = $partner_id;
                        $this->user_m->update_by_many(array(
                            'id' => $user_id
                        ), array(
                            "partner_id" => $partner_id,
                            'delete_flag' => APConstants::OFF_FLAG
                        ));
                        
                        $this->group_user_m->insert(array(
                            "user_id" => $user_id,
                            "group_id" => APConstants::GROUP_SERVICE_PARTNER_ADMIN
                        ));
                        */
                    }

                    // commit transaction
                    //$this->partner_m->db->trans_commit();
                } catch (Exception $e) {
                    //$this->partner_m->db->trans_rollback();

                    // output message.
                    $message = $e->getMessage();
                    $this->error_output($message);
                    return;
                }

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
            $partner->{$rule['field']} = set_value($rule['field']);
        }
        $partner->email = '';
        $partner->phone = '';
        $partner->main_contact_point = '';

        // Gets all pricing template
        $price_model = $this->pricing_template_m->get_all_public_template();
        
        // get all public location
        $locations = $this->location_m->get_public_location(array("public_flag" => APConstants::ON_FLAG));
        
        // Display the current page
        $this->template->set('partner', $partner)
            ->set("locations", $locations)
            ->set("price_model", $price_model)
            ->set("countries", $countries)
            ->set('action_type', 'add')
            ->build('admin/form');
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
        $partner = $this->partner_m->get_marketing_profile($id);

        // Get user_name and pass
        /*$partner->user_name = '';
        $partner->password = '';
        $user_partner = $this->user_m->get_by_many(array('partner_id' => $id));
        if (!empty($user_partner)) {
            $partner->user_name = $user_partner->username;
        }
        */

        // Get all countries
        $countries = $this->countries_m->get_many_by_many(array(), '', false, array(
            'country_name' => 'ASC'
        ));

        if ($_POST) {
            // Set the validation rules
            $this->form_validation->set_rules($this->validation_rules);

            if ($this->form_validation->run()) {
                $partner_id = $this->input->post('id');
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
                // $threhold_for_direct_prepay_charge = $this->input->post("threhold_for_direct_prepay_charge");

                //$this->partner_m->db->trans_begin();
                try {
                    // Save data to database
                    $restul = $this->partner_m->update_by_many(
                        array(
                            "partner_id" => $partner_id
                        ),
                        array(
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
                            "rev_share_in_percent" => $this->input->post("rev_share_in_percent")
                        ));

                    if ($this->input->post('partner_type') == APConstants::PARTNER_MARKETING_TYPE) {
                        $partner_marketing = $this->partner_marketing_profile_m->get_by_many( array(
                            "partner_id" => $partner_id
                        ));

                        $bonus_month=  $this->input->post("bonus_month");
                        $bonus_location=  $this->input->post("bonus_location");
                        $bonus_flag = !empty($bonus_month) && !empty($bonus_location) ? 1 : 0;
                        $data_profile = array(
                            "partner_id" => $partner_id,
                            "rev_share_ad" => $this->input->post("rev_share_ad"),
                            "duration_customer_discount" => $this->input->post("duration_customer_discount"),
                            "duration_rev_share" => $this->input->post("duration_rev_share"),
                            "customer_discount" => $this->input->post("customer_discount"),
                            "partner_domain" => $this->input->post("partner_domain"),
                            "registration" => $this->input->post("registration"),
                            "activation" => $this->input->post("activation"),
                            "bonus_month" => $bonus_month,
                            "bonus_flag" => $bonus_flag,
                            "bonus_location" => $bonus_location
                        );
                        
                        if ($partner_marketing) {
                            $this->partner_marketing_profile_m->update_by_many(
                                array(
                                    "partner_id" => $partner_id
                                ),$data_profile);
                        } else {
                            $this->partner_marketing_profile_m->insert($data_profile);
                        }
                    } else if ($this->input->post('partner_type') == APConstants::PARTNER_SERVICE_TYPE || $this->input->post('partner_type') == APConstants::PARTNER_LOCATION_TYPE) {
                        $main_contact_point = $this->input->post("main_contact_point");
                        $email = $this->input->post("email");
                        $phone = $this->input->post("phone");
                        //$user_name = $this->input->post("user_name");
                        //$password = $this->input->post("password");

                        if (empty($main_contact_point)) {
                            $this->error_output('Main contact point field is required.');
                            return;
                        }
                        if (empty($email)) {
                            $this->error_output('Service partner email field is required.');
                            return;
                        }
                        if (empty($phone)) {
                            $this->error_output('Service partner phone field is required.');
                            return;
                        }
                        /*
                        if (empty($user_name)) {
                            $this->error_output('Service partner user name field is required.');
                            return;
                        }
                        //if (empty($password)) {
                        //    $this->error_output('Service partner password field is required.');
                        //    return;
                        //}
                        */
                        $partner_service = $this->cases_service_partner_m->get_by_many(array(
                            "partner_id" => $partner_id
                        ));

                        // Update service partner information
                        if ($partner_service) {
                            $this->cases_service_partner_m->update_by_many( array(
                                "partner_id" => $partner_id
                            ), array(
                                "partner_name" => $name,
                                "main_contact_point" => $this->input->post("main_contact_point"),
                                "email" => $this->input->post("email"),
                                "phone" => $this->input->post("phone"),
                                "updated_date" => now()
                            ));
                        } else {
                            $this->cases_service_partner_m->insert(
                                array(
                                    "partner_id" => $partner_id,
                                    "partner_name" => $name,
                                    "main_contact_point" => $this->input->post("main_contact_point"),
                                    "email" => $this->input->post("email"),
                                    "phone" => $this->input->post("phone"),
                                    "created_date" => now()
                                ));
                        }

                        // Update password
                        /*
                        if (empty($user_partner)) {
                            // Insert user login for service partner
                            $group_id = APConstants::GROUP_SERVICE_PARTNER_ADMIN;
                            $user_id = $this->ion_auth->register($user_name, $password, $email, $group_id,
                                array(
                                    "display_name" => $name,
                                    "first_name" => $name,
                                    "last_name" => '',
                                    "active" => APConstants::ON_FLAG,
                                    "partner_id" => $partner_id,
                                    "location_available_id" => ''
                                ));

                            // /$update_data ['partner_id'] = $partner_id;
                            $this->user_m->update_by_many(array(
                                'id' => $user_id
                            ), array(
                                "partner_id" => $partner_id,
                                'delete_flag' => APConstants::OFF_FLAG
                            ));
                            
                            $check_group_user = $this->group_user_m->get_by_many(array(
                                "user_id" => $user_id,
                            ));

                            if(empty($check_group_user)){
                                $this->group_user_m->insert(array(
                                    "user_id" => $user_id,
                                    "group_id" => APConstants::GROUP_SERVICE_PARTNER_ADMIN
                                ));
                            }
                        } else {
                            // Update password
                            if ($password) {
                                $update_data = array();
                                $update_data ['password'] = $password;
                                $result = $this->ion_auth->update_user($$user_partner->id, $update_data);
                            }
                        }
                        */
                    }

                    // commit transaction
                    //$this->partner_m->db->trans_commit();
                } catch (Exception $e) {
                    //$this->partner_m->db->trans_rollback();
                    // output message.
                    $message = $e->getMessage();
                    $this->error_output($message);
                    return;
                }

                // render success message.
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
            if ($this->input->post($rule['field']) !== false) {
                $member->{$rule['field']} = set_value($rule['field']);
            }
        }

        // Gets all pricing template
        $price_model = $this->pricing_template_m->get_all_public_template();

        // get all public location
        $locations = $this->location_m->get_public_location(array("public_flag" => APConstants::ON_FLAG));
        
        // Display the current page
        $this->template->set('partner', $partner)
            ->set("locations", $locations)
            ->set("price_model", $price_model)
            ->set('countries', $countries)
            ->set('action_type', 'edit')
            ->build('admin/form');
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
        $partnerID = $this->input->get_post("id");
        ci()->load->library('partner/partner_api');
        
        $resultDelete = partner_api::deletePartner($partnerID);
        
        if(!$resultDelete['status']){
            $this->error_output($resultDelete['message']);
            return;
        }
        else {
            $this->success_output($resultDelete['message']);
            return;
        }
    
    }

    /**
     *  Get list locations by partner
     *  function get_list_location_bypartner
     * 
     *  return
     */
    public function get_list_location_bypartner()
    {
        $this->template->set_layout(FALSE);
        $partner_id = $this->input->get_post("partner_id");

        //Get locations of partner 
        if(!empty($partner_id)){
            $locations = $this->location_m->get_many_by_many(array(
                "partner_id" => $partner_id
            ));
        }else{ // All list locations and list partner
            $locations[0] = $this->location_m->get_all();
            $locations[1] = $this->partner_m->get_all();
        }

        // output message.
        echo json_encode($locations);
        return;
    }
    
    /**
     *  Get partner by location
     *  function get_partner_bylocation
     * 
     *  return
     */
    public function get_partner_bylocation()
    {
        $this->template->set_layout(FALSE);
        $location_id= $this->input->get_post("location_id");

        //Get partner by location
        if(!empty($location_id)){
            $partner = $this->partner_m->getPartnerByLocationID($location_id);
        }else{ // All list locations and list partner
            $partner[0] = $this->partner_m->get_all();
            $partner[1] = $this->location_m->get_all();
        }
        

        // output message.
        echo json_encode($partner);
        return;
    }

    /**
     * generate width setting.
     */
    public function edit_marketing()
    {
        $this->template->set_layout(FALSE);
        $partner_id = $this->input->get_post('id');

        // Get the user's data
        $partner = $this->partner_m->get_marketing_profile($partner_id);

        if ($_POST) {
            $script_widget = $this->input->post('script_widget', '');
            $script_landing_page = $this->input->post('script_landing_page', '');

            $this->partner_marketing_profile_m->update_by_many(array(
                "partner_id" => $partner_id
            ),
                array(
                    "script_widget" => $script_widget,
                    "script_landing_page" => $script_landing_page,
                    "session_catch" => $this->input->post("session_catch"),
                    "title" => $this->input->post("title"),
                    "width" => $this->input->post("width"),
                    "height" => $this->input->post("height"),
                    "token" => $this->input->post("token")
                ));

            // output message.
            $message = lang('edit_partner_success');
            $this->success_output($message);
            return;
        }

        // Display the current page
        $this->template->set('partner', $partner)->build('admin/widget_setting');
    }

    /**
     * generate widget.
     */
    public function generate_widget()
    {
        $this->load->library("partner_api");

        $partner_id = $this->input->get_post('id', '0');
        $type = $this->input->get_post('type', '');
        $width = $this->input->get_post("width");
        $height = $this->input->get_post("height");
        $code = $this->input->get_post('token') ? $this->input->get_post('token') : md5(APUtils::generateRandom(12));

        // Gets profile of partner.
        $partnerProfile = partner_api::getPartnerMarketingProfileById($partner_id);
        if ($partnerProfile && $partnerProfile->token) {
            $code = $partnerProfile->token;
        }

        // landing page
        if ($type == 1) {
            // session catch
            $html = '<script type="text/javascript">';
            $html .= 'document.cookie = "partner_referrer_code=' . $code . '"';
            $html .= "</script>";

            partner_api::updatePartnerMarketingLandingPage($partner_id, $html);
        } else if ($type == 2) {
            // registration widget
            $html = $this->load->view('admin/template_widget', array(
                "code" => $code,
                "width" => $width,
                "height" => $height,
                "unit" => 'px',
                "type" => $type,
                "title" => $this->input->post('title')
            ), true);

            partner_api::updatePartnerMarketingRegistrationWidget($partner_id, $html);
        } else {
            // session catch
            $base_url = APContext::getAssetPath() . 'system/virtualpost/themes/widget/js/sessioncatch.js';
            $html = '<script type="text/javascript" src="' . $base_url . '"></script>';

            partner_api::updatePartnerMarketingSessionCatch($partner_id, $html);
        }

        $this->success_output('', array(
            'html' => $html,
            "code" => $code
        ));
    }
}