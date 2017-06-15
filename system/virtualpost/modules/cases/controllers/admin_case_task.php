<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Admin controller for the users module
 */
class admin_case_task extends Admin_Controller
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
            'field' => 'taskname',
            'label' => 'lang:base_taskname',
            'rules' => 'required|validname|max_length[250]'
        ),
        array(
            'field' => 'activate_flag',
            'label' => 'lang:activated',
            'rules' => 'trim'
        ),
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
        $this->load->model('cases_product_base_taskname_m');

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
        $list_products = $this->cases_product_m->get_cases_is_active(array());

        if (!empty($product_id)) {
            $array_condition['product_id'] = $product_id;
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
            $query_result = $this->cases_product_base_taskname_m->get_paging($array_condition, $input_paging['start'], $input_paging['limit'], $input_paging['sort_column'], $input_paging['sort_type']);

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
                    $row->id,
                    $row->taskname,
                    $row->activate_flag,
                    $row->id
                );
                $i++;
            }

            echo json_encode($response);
        } else {

            $this->template->set('list_products', $list_products);
            $this->template->set('product_id', $product_id);

            // Display the current page
            $this->template->build('admin/case_task/index');
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

            $taskname = $this->input->post('taskname');
            $activate_flag = $this->input->post('activate_flag');
            $product_id = $this->input->post("product_id");
            if ($this->form_validation->run()) {
                // Insert data to database
                $id = $this->cases_product_base_taskname_m->insert(array(
                    "taskname" => $taskname,
                    "base_taskname" => str_replace(' ', '_', $taskname),
                    "product_id" => $product_id,
                    "activate_flag" => $activate_flag
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
        $casetask = new stdClass();
        $casetask->id = '';
        foreach ($this->validation_rules as $rule) {
            $casetask->{$rule['field']} = set_value($rule['field']);
        }
        $casetask->product_id = $product_id;

        // Display the current page
        $this->template->set('action_type', 'add')->set('casetask', $casetask)
            ->set('product_id', $product_id)
            ->build('admin/case_task/form');
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
        $casetask = $this->cases_product_base_taskname_m->get_by("id", $id);
        if ($_POST) {
            // Set the validation rules
            $this->form_validation->set_rules($this->validation_rules);

            if ($this->form_validation->run()) {

                // Save data to database
                $restul = $this->cases_product_base_taskname_m->update_by_many(array(
                    "id" => $id
                ), array(
                    "taskname" => $this->input->post('taskname'),
                    "activate_flag" => $this->input->post('activate_flag'),
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

        // Display the current page
        $this->template->set('casetask', $casetask)
            ->set('action_type', 'edit')
            ->build('admin/case_task/form');
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

        $this->cases_product_base_taskname_m->delete_by("id", $id);

        // output message.
        $message = lang('delete_milestone_success');
        $this->success_output($message);
        return;
    }
}