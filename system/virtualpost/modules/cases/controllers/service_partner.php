<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Admin controller for the users module
 */
class service_partner extends Admin_Controller
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
            'field' => 'partner_name',
            'label' => 'lang:partner_name',
            'rules' => 'required|validname|max_length[250]'
        ),
        array(
            'field' => 'email',
            'label' => 'lang:email',
            'rules' => 'required|email|max_length[250]'
        ),
        array(
            'field' => 'phone',
            'label' => 'lang:phone',
            'rules' => 'required|phone_number|max_length[20]'
        ),
        array(
            'field' => 'main_contact_point',
            'label' => 'lang:main_contact_point',
            'rules' => 'required|max_length[250]'
        )
    );

    /**
     * Constructor method
     */
    public function __construct()
    {
        parent::__construct();

        // Load the required classes
        $this->load->model('cases/cases_service_partner_m');

        $this->load->library('form_validation');
        $this->lang->load('cases/cases');
    }

    /**
     * List all devices.
     * Using for device panel
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
            $query_result = $this->cases_service_partner_m->get_paging($array_condition, $input_paging['start'], $input_paging['limit'],
                $input_paging['sort_column'], $input_paging['sort_type']);

            // Process output data
            $total = $query_result['total'];
            $datas = $query_result['data'];

            // Get output response
            $response = $this->get_paging_output($total, $input_paging['limit'], $input_paging['page']);

            $i = 0;
            foreach ($datas as $row) {
                $response->rows[$i]['id'] = $row->partner_id;
                $response->rows[$i]['cell'] = array(
                    $row->partner_id,
                    $row->partner_name,
                    $row->main_contact_point,
                    $row->email,
                    $row->phone,
                    date('Y-m-d H:i:s', $row->created_date),
                    $row->partner_id
                );
                $i++;
            }

            echo json_encode($response);
        } else {
            // Display the current page
            $this->template->build('service_partner/index');
        }
    }

    /**
     * Method for handling different form actions
     */
    public function add()
    {
        $ServicePartner = new stdClass();
        $ServicePartner->id = '';
        $this->template->set_layout(FALSE);

        if ($_POST) {
            $this->form_validation->set_rules($this->validation_rules);

            $partner_name = $this->input->post('partner_name');
            $main_contact_point = $this->input->post("main_contact_point");
            $email = $this->input->post("email");
            $phone = $this->input->post("phone");

            if ($this->form_validation->run()) {
                // Insert data to database
                $id = $this->cases_service_partner_m->insert(
                    array(
                        "partner_name" => $partner_name,
                        "main_contact_point" => $main_contact_point,
                        "email" => $email,
                        "phone" => $phone,
                        "created_date" => now()
                    ));

                $message = lang('add_servicepartner_success');
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
            $ServicePartner->{$rule['field']} = set_value($rule['field']);
        }

        // Display the current page
        $this->template->set('ServicePartner', $ServicePartner)
            ->set('action_type', 'add')
            ->build('service_partner/form');
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
        $ServicePartner = $this->cases_service_partner_m->get_by("partner_id", $id);
        if ($_POST) {
            // Set the validation rules
            $this->form_validation->set_rules($this->validation_rules);

            if ($this->form_validation->run()) {
                $partner_name = $this->input->post('partner_name');
                $main_contact_point = $this->input->post("main_contact_point");
                $email = $this->input->post("email");
                $phone = $this->input->post("phone");

                // Save data to database
                $restul = $this->cases_service_partner_m->update_by_many(array(
                    "partner_id" => $id
                ),
                    array(
                        "partner_name" => $partner_name,
                        "main_contact_point" => $main_contact_point,
                        "email" => $email,
                        "phone" => $phone,
                        "updated_date" => now()
                    ));

                $message = lang('edit_servicepartner_success');
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
                $ServicePartner->{$rule['field']} = set_value($rule['field']);
            }
        }

        // Display the current page
        $this->template->set('ServicePartner', $ServicePartner)
            ->set('action_type', 'edit')
            ->build('service_partner/form');
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
        $this->cases_service_partner_m->delete_by("partner_id", $id);

        // output message.
        $message = lang('delete_servicepartner_success');
        $this->success_output($message);
        return;
    }
}