<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Admin controller for the users module
 */
class milestone extends Admin_Controller
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
            'field' => 'milestone_name',
            'label' => 'lang:milestone_name',
            'rules' => 'required|validname|max_length[250]'
        ),
        array(
            'field' => 'partner_id',
            'label' => 'lang:partner',
            'rules' => 'required'
        ),
        array(
            'field' => 'depend_milestone_id',
            'label' => 'dependency milestone',
            'rules' => 'trim'
        ),
        array(
            'field' => 'product_id',
            'label' => 'lang:product',
            'rules' => 'required'
        ),
        array(
            'field' => 'base_taskname',
            'label' => 'Base taskname',
            'rules' => 'required'
        ),
        array(
            'field' => 'cmra',
            'label' => 'CMRA',
            'rules' => 'trim'
        )
    );

    /**
     * Validation for basic profile
     * data.
     * The rest of the validation is
     * built by streams.
     *
     * @var array
     */
    private $validation_rules02 = array(
        array(
            'field' => 'task_name',
            'label' => 'lang:task_name',
            'rules' => 'required|validname|max_length[250]'
        ),
        array(
            'field' => 'base_task_name',
            'label' => 'lang:base_task_name',
            'rules' => 'required'
        ),
        array(
            'field' => 'milestone_id',
            'label' => 'lang:milestone_id',
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
        $this->load->model('cases/cases_milestone_m');
        //$this->load->model('cases/cases_service_partner_m');
        $this->load->model('cases/cases_product_m');
        $this->load->model('cases/cases_product_base_taskname_m');
        $this->load->model('cases/cases_taskname_m');

        $this->load->model('partner/partner_m');

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
        $product_id = $this->input->get_post('product_id');
        $array_condition = array();
        // $list_products = $this->cases_product_m->get_all();
        $list_products = $this->cases_product_m->get_cases_is_active(array());

        // condition
        if (!empty($product_id)) {
            $array_condition['cases_milestone.product_id'] = $product_id;
        }

        // If current request is ajax
        if ($this->is_ajax_request()) {

            // update limit into user_paging.
            $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);

            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging['limit'] = $limit;

            // Call search method
            $query_result = $this->cases_milestone_m->get_milestone_paging($array_condition, $input_paging['start'], $input_paging['limit'], $input_paging['sort_column'], $input_paging['sort_type']);

            // Process output data
            $total = $query_result['total'];
            $datas = $query_result['data'];

            // Get output response
            $response = $this->get_paging_output($total, $input_paging['limit'], $input_paging['page']);

            $i = 0;
            foreach ($datas as $row) {
                $response->rows[$i]['id'] = $row->id;
                $response->rows[$i]['cell'] = array(
                    $row->id,
                    $row->product_name,
                    $row->milestone_name,
                    $row->taskname,
                    $row->main_contact_point,
                    $row->email,
                    $row->phone,
                    $row->id
                );
                $i++;
            }

            echo json_encode($response);
        } else {

            $this->template->set('list_products', $list_products);
            $this->template->set('product_id', $product_id);

            // Display the current page
            $this->template->build('milestone/index');
        }
    }

    /**
     * Method for handling different form actions
     */
    public function add()
    {
        $product_id = $this->input->get_post('product_id');
        $this->template->set_layout(FALSE);
        $list_products = $this->cases_product_m->get_all();

        if ($_POST) {
            $this->form_validation->set_rules($this->validation_rules);

            $milestone_name = $this->input->post('milestone_name');
            $product_id = $this->input->post("product_id");
            $partner_id = $this->input->post("partner_id");
            $depend_milestone_id = $this->input->post("depend_milestone_id");
            $base_taskname = $this->input->post("base_taskname");
            $cmra = $this->input->post("cmra");

            if ($this->form_validation->run()) {

                // Insert data to database
                $id = $this->cases_milestone_m->insert(array(
                    "milestone_name" => $milestone_name,
                    "product_id" => $product_id,
                    "partner_id" => $partner_id,
                    "depend_milestone_id" => $depend_milestone_id,
                    "cmra" => $cmra,
                    "created_date" => now()
                ));

                $temp = $this->cases_product_base_taskname_m->get_by_many(array(
                    "base_taskname" => $base_taskname
                ));
                $this->cases_taskname_m->insert(array(
                    "task_name" => $temp->taskname,
                    "base_task_name" => $temp->base_taskname,
                    "milestone_id" => $id
                ));

                $message = lang('add_milestone_success');
                $this->success_output($message);
                return;
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }

        // Loop through each validation rule
        $milestone = new stdClass();
        $milestone->id = '';
        foreach ($this->validation_rules as $rule) {
            $milestone->{$rule['field']} = set_value($rule['field']);
        }

        $task = new stdClass();
        $task->base_taskname = '';

        $list_milestone = $this->cases_milestone_m->get_many_by_many(array(
            "product_id" => $product_id
        ));
        $list_base_taskname = $this->cases_product_base_taskname_m->get_many_by_many(array(
            "product_id" => $product_id
        ));

        //only Service Partners, Location Partners and ClevverMail
        $list_service_partner = $this->partner_m->get_many_by_many(array("partner_type in ('0', '2')" => null));

        $milestone->product_id = $product_id;
        // Display the current page
        $this->template->set('task', $task)
            ->set('list_milestone', $list_milestone)
            ->set('list_base_taskname', $list_base_taskname)
            ->set('milestone', $milestone)
            ->set('action_type', 'add')
            ->set('list_service_partner', $list_service_partner)
            ->set('product_id', $product_id)
            ->build('milestone/form');
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
        $list_service_partner = $this->partner_m->get_many_by_many(array("partner_type in ('0','2')" => null));
        // Get the user's data
        $milestone = $this->cases_milestone_m->get_by("id", $id);
        if ($_POST) {
            // Set the validation rules
            $this->form_validation->set_rules($this->validation_rules);

            if ($this->form_validation->run()) {
                $milestone_name = $this->input->post('milestone_name');
                $product_id = $this->input->post("product_id");
                $partner_id = $this->input->post("partner_id");
                $depend_milestone_id = $this->input->post("depend_milestone_id");
                $base_taskname = $this->input->post("base_taskname");
                $cmra = $this->input->post("cmra");

                // Save data to database
                $restul = $this->cases_milestone_m->update_by_many(array(
                    "id" => $id
                ), array(
                    "milestone_name" => $milestone_name,
                    "partner_id" => $partner_id,
                    "depend_milestone_id" => $depend_milestone_id,
                    "cmra" => $cmra,
                    "updated_date" => now()
                ));

                $temp = $this->cases_product_base_taskname_m->get_by_many(array(
                    "base_taskname" => $base_taskname
                ));
                $this->cases_taskname_m->update_by_many(array(
                    "milestone_id" => $id
                ), array(
                    "task_name" => $temp->taskname,
                    "base_task_name" => $temp->base_taskname
                ));

                $message = lang('edit_milestone_success');
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
                $milestone->{$rule['field']} = set_value($rule['field']);
            }
        }

        $list_milestone = $this->cases_milestone_m->get_many_by_many(array(
            "product_id" => $milestone->product_id
        ));
        $list_base_taskname = $this->cases_product_base_taskname_m->get_many_by_many(array(
            "product_id" => $milestone->product_id
        ));
        $this->template->set('list_milestone', $list_milestone);
        $this->template->set('list_base_taskname', $list_base_taskname);

        $temp = $this->cases_taskname_m->get_by_many(array(
            "milestone_id" => $milestone->id
        ));
        $task = new stdClass();
        $task->base_taskname = $temp->base_task_name;

        // Display the current page
        $this->template->set('task', $task)
            ->set('milestone', $milestone)
            ->set('action_type', 'edit')
            ->set('list_service_partner', $list_service_partner)
            ->build('milestone/form');
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
        $this->cases_taskname_m->delete_by_many(array(
            'milestone_id' => $id
        ));
        $this->cases_milestone_m->delete_by("id", $id);

        // output message.
        $message = lang('delete_milestone_success');
        $this->success_output($message);
        return;
    }

    // ********************************************************************************************************************
    // Task method
    // ********************************************************************************************************************
    /**
     * List all devices.
     * Using for device panel
     */
    public function show_tasklist()
    {
        // Get input condition
        $milestone_id = $this->input->get_post('milestone_id');
        $array_condition = array();
        $array_condition['cases_taskname.milestone_id'] = $milestone_id;

        // If current request is ajax
        if ($this->is_ajax_request()) {

            // update limit into user_paging.
            $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);

            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging['limit'] = $limit;

            // Call search method
            $query_result = $this->cases_taskname_m->get_task_paging($array_condition, $input_paging['start'], $input_paging['limit'], $input_paging['sort_column'], $input_paging['sort_type']);

            // Process output data
            $total = $query_result['total'];
            $datas = $query_result['data'];

            // Get output response
            $response = $this->get_paging_output($total, $input_paging['limit'], $input_paging['page']);

            $i = 0;
            foreach ($datas as $row) {
                $response->rows[$i]['id'] = $row->id;
                $response->rows[$i]['cell'] = array(
                    $row->id,
                    $row->task_name,
                    $row->partner_name,
                    $row->main_contact_point,
                    $row->email,
                    $row->phone,
                    $row->id
                );
                $i++;
            }

            echo json_encode($response);
        } else {
            // Display the current page
            $this->template->build('milestone/index');
        }
    }

    /**
     * Method for handling different form actions
     */
    public function add_task()
    {
        $milestone_id = $this->input->get_post('milestone_id');
        $task = new stdClass();
        $task->id = '';
        $this->template->set_layout(FALSE);

        if ($_POST) {
            $this->form_validation->set_rules($this->validation_rules02);

            $task_name = $this->input->post('task_name');
            $base_task_name = $this->input->post('base_task_name');
            $milestone_id = $this->input->post("milestone_id");

            if ($this->form_validation->run()) {
                // Insert data to database
                $id = $this->cases_taskname_m->insert(array(
                    "task_name" => $task_name,
                    "base_task_name" => $base_task_name,
                    "milestone_id" => $milestone_id
                ));

                $message = lang('add_task_success');
                $this->success_output($message);
                return;
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }

        // Loop through each validation rule
        foreach ($this->validation_rules02 as $rule) {
            $task->{$rule['field']} = set_value($rule['field']);
        }

        $milestone = $this->cases_milestone_m->get_by_many(array(
            "id" => $milestone_id
        ));
        $list_base_taskname = $this->cases_product_base_taskname_m->get_many_by_many(array(
            "product_id" => $milestone->product_id
        ));
        $this->template->set('list_base_taskname', $list_base_taskname);

        $task->milestone_id = $milestone_id;
        // Display the current page
        $this->template->set('task', $task)
            ->set('action_type', 'add')
            ->build('milestone/form_task');
    }

    /**
     * Edit an existing user
     *
     * @param int $id
     *            The id of the user.
     */
    public function edit_task()
    {
        $this->template->set_layout(FALSE);
        $id = $this->input->get_post("id");
        $list_service_partner = $this->cases_taskname_m->get_all();
        // Get the user's data
        $task = $this->cases_taskname_m->get_by("id", $id);
        if ($_POST) {
            // Set the validation rules
            $this->form_validation->set_rules($this->validation_rules02);

            if ($this->form_validation->run()) {
                $task_name = $this->input->post('task_name');
                $milestone_id = $this->input->post("milestone_id");

                // Save data to database
                $restul = $this->cases_taskname_m->update_by_many(array(
                    "id" => $id
                ), array(
                    "task_name" => $task_name
                ));

                $message = lang('edit_task_success');
                $this->success_output($message);
                return;
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }

        // Loop through each validation rule
        foreach ($this->validation_rules02 as $rule) {
            if ($this->input->post($rule['field']) !== false) {
                $task->{$rule['field']} = set_value($rule['field']);
            }
        }

        $milestone = $this->cases_milestone_m->get_by_many(array(
            "id" => $task->milestone_id
        ));
        $list_base_taskname = $this->cases_product_base_taskname_m->get_many_by_many(array(
            "product_id" => $milestone->product_id
        ));
        $this->template->set('list_base_taskname', $list_base_taskname);

        // Display the current page
        $this->template->set('task', $task)
            ->set('action_type', 'edit')
            ->set('list_service_partner', $list_service_partner)
            ->build('milestone/form_task');
    }

    /**
     * Edit an existing user
     *
     * @param int $id
     *            The id of the user.
     */
    public function delete_task()
    {
        $this->template->set_layout(FALSE);
        $id = $this->input->get_post("id");

        // delete partner
        $this->cases_taskname_m->delete_by_many(array(
            'id' => $id
        ));

        // output message.
        $message = lang('delete_task_success');
        $this->success_output($message);
        return;
    }
}