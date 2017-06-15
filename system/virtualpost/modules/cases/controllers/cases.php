<?php defined('BASEPATH') or exit ('No direct script access allowed');

class cases extends CaseSystem_Controller
{
    private $validate_rule = array(
        array(
            'field' => 'opening_date',
            'label' => 'lang:opening_date',
            'rules' => 'required|trim|max_length[10]'
        ),
        array(
            'field' => 'case_identifier',
            'label' => 'lang:case_identifier',
            'rules' => 'required|trim|max_length[10]'
        ),
        array(
            'field' => 'product',
            'label' => 'lang:product',
            'rules' => 'trim'
        ),
        array(
            'field' => 'country',
            'label' => 'lang:country',
            'rules' => 'trim'
        ),
        array(
            'field' => 'status',
            'label' => 'lang:status',
            'rules' => 'trim'
        )
    );

    /**
     * Loads the gazillion of stuff, in Flash Gordon speed.
     *
     * @todo Document properly please.
     */
    public function __construct()
    {
        parent::__construct();
        
        // Model
        $this->load->model(array(
            'cases_m',
            'settings/countries_m',
            'users/user_m',
            "cases_product_m",
            "cases_milestone_m",
            "cases_milestone_instance_m",
            "cases_taskname_m",
            "cases_taskname_instance_m",
            "addresses/customers_address_m",
            "cases/cases_product_base_taskname_m"
        ));
        
        $this->lang->load('cases');
    }

    /**
     * Display all current case
     */
    public function index()
    {
        $isPrimaryEnterpriseCustomer = APContext::isPrimaryCustomerUser();
        if($isPrimaryEnterpriseCustomer){
            $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
            $current_user_id = CustomerUtils::getListCustomerIdOfEnterpriseCustomer($parent_customer_id);
            $current_user_id[] = $parent_customer_id;
        }else{
            $current_user_id = APContext::getCustomerCodeLoggedIn();
        }
        $product_id = $this->input->get_post('product_id');
        $this->template->set("product_id", $product_id);
        $this->template->set("case_id", $this->input->get_post('case_id', ''));
        
        $array_condition = array(
            //'cases.customer_id' => $current_user_id,
            'cases.deleted_flag' => APConstants::OFF_FLAG
        );
        
        if($isPrimaryEnterpriseCustomer){
            $array_condition["cases.customer_id IN ('".  implode("','", $current_user_id)."')"] = null;
        }else{
            $array_condition["cases.customer_id"] = $current_user_id;
        }
        
        if (! empty($product_id)) {
            $array_condition['cases.product_id'] = $product_id;
        }
        
        if ($this->is_ajax_request() || false) {
            // update limit into user_paging.
            $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);
            
            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging['limit'] = $limit;
            
            // Call search method
            $query_result = $this->cases_m->get_cases_paging($array_condition, $input_paging['start'], $input_paging['limit'], $input_paging['sort_column'], $input_paging['sort_type']);
            
            // Process output data
            $total = $query_result['total'];
            $datas = $query_result['data'];
            
            // Get output response
            $response = $this->get_paging_output($total, $input_paging['limit'], $input_paging['page']);
            $get_partner_list = $this->cases_milestone_instance_m->get_partner_list();
            
            $i = 0;
            foreach ($datas as $row) {
                $response->rows[$i]['id'] = $row->id;
                $response->rows[$i]['cell'] = array(
                    $row->id,
                    APUtils::convert_timestamp_to_date($row->opening_date),
                    $row->case_identifier,
                    $row->description,
                    $row->user_name,
                    $this->get_partner_name($row->id, $get_partner_list),
                    $row->country_name,
                    $row->status == 1 ? "Pending" : (($row->status == 2) ? "Completed" : "Open"),
                    $row->status
                );
                $i ++;
            }
            echo json_encode($response);
        } else {
            // $products = $this->cases_product_m->get_all();
            $products = $this->cases_product_m->get_cases_is_active(array());
            $this->template->set("products", $products);
            // list all
            $this->template->build('index');
        }
    }

    /**
     * Show case detail
     */
    public function show()
    {
        if (! isset($_GET['case']) || ! is_numeric($_GET['case']) || $_GET['case'] < 1) {
            $this->error_output('You don\'t have the permissions to access this case!');
            return;
        }
        $case_id = $_GET['case'];
        $case = $this->cases_m->get_by('id', $case_id);
        // @todo: remove
        $case->data = json_encode(array(
            'step' => '0',
            'step_0_data' => array()
        ));
        $current_user_id = $this->current_user->id;
        if ($case->customer_id != $current_user_id) {
            $this->error_output('You don\'t have the permissions to access this case!');
            return;
        }
        $case_type_info = $this->cases_m->cases_config[$case->case_type];
        $case_data = json_decode($case->data);

        $helper_method = $case_type_info['milestones'][$case_data->step]['method'];
        $this->load->helper($helper_method[0]);
        $case_helper = new $helper_method[0]($this, $case_data);
        $case_helper->$helper_method[1]();
    }

    /**
     * show all task of this case case.
     */
    public function show_tasklist()
    {
        if ($this->is_ajax_request() || false) {
            $case_id = $this->input->get_post("case_id", '');
            $array_condition = array(
                'cases_taskname_instance.case_id' => $case_id
            );
            // Only filter task of this user login
            if (APContext::isServiceParner()) {
                // $array_condition['cases_milestone.partner_id'] = APContext::getParnerIDLoggedIn();
            }
            
            // update limit into user_paging.
            $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);
            
            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging['limit'] = $limit;
            
            // Call search method
            $query_result = $this->cases_taskname_instance_m->get_tasklist_paging($array_condition, $input_paging['start'], $input_paging['limit'], $input_paging['sort_column'], $input_paging['sort_type']);
            
            // Process output data
            $total = $query_result['total'];
            $datas = $query_result['data'];
            
            // Get output response
            $response = $this->get_paging_output($total, $input_paging['limit'], $input_paging['page']);
            
            $i = 0;
            foreach ($datas as $row) {
                // Row for customer
                $response->rows[$i]['id'] = $row->id;
                $arrResult = array(
                    $row->id,
                    $row->case_id,
                    $row->product_id,
                    $row->milestone_name,
                    $row->base_task_name,
                    lang('status_' . $row->status),
                    $row->status,
                    $row->c_name,
                    $row->c_email,
                    lang($row->status == 1 ? 'your_task_c_2' : 'your_task_c_' . $row->status)
                );
                $response->rows[$i]['cell'] = $arrResult;
                $i ++;
                
                // Row for admin
                $response->rows[$i]['id'] = $row->id;
                $arrResult = array(
                    $row->id,
                    $row->case_id,
                    $row->product_id,
                    sprintf('Confirm "%1$s" ', $row->milestone_name),
                    $row->base_task_name,
                    lang('status_' . $row->status),
                    - 1,
                    $row->partner_name,
                    $row->email,
                    lang($row->status == 0 || $row->status == 3 ? 'your_task_c_w' : 'your_task_c_' . $row->status)
                );
                $response->rows[$i]['cell'] = $arrResult;
                $i ++;
            }
            echo json_encode($response);
        }
    }

    /**
     * show all check of this case case.
     */
    public function show_checklist()
    {
        if ($this->is_ajax_request() || false) {
            $case_id = $this->input->get_post("case_id", '');
            
            // update limit into user_paging.
            $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);
            
            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging['limit'] = $limit;
            
            // Call search method
            $query_result = $this->cases_milestone_instance_m->get_check_list_paging($case_id, $input_paging['start'], $input_paging['limit'], $input_paging['sort_column'], $input_paging['sort_type']);
            // Process output data
            $total = $query_result['total'];
            $datas = $query_result['data'];
            
            // Get output response
            $response = $this->get_paging_output($total, $input_paging['limit'], $input_paging['page']);
            
            $i = 0;
            foreach ($datas as $row) {
                $response->rows[$i]['id'] = $row->id;
                $arrResult = array(
                    $row->id,
                    $row->case_id,
                    $row->status,
                    $row->milestone_name,
                    lang('status_' . $row->status),
                    lang("your_task_c_" . $row->status),
                    $row->comment_content,
                    $row->base_task_name
                );
                $response->rows[$i]['cell'] = $arrResult;
                $i ++;
            }
            echo json_encode($response);
        }
    }

    /**
     * create case function
     * @createdBy: d3jsexperts
     */
    public function create()
    {
        $this->template->set_layout(FALSE);
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $customer = APContext::getCustomerByID($customer_id);
        
        // Get total number case
        $number_case = $this->cases_m->count_by_many(array(
            "customer_id" => $customer_id
        ));
        $new_case_id = sprintf('C%1$04d', $number_case + 1);
        
        // Generate auto case id
        $case_id = $customer->customer_code . '_' . $new_case_id;
        $this->template->set("case_id", $case_id);
        $product_id = $this->input->get_post('product_id');
        $this->template->set("product_id", $product_id);
        
        if ($_POST) {
            $input = $this->input->post();
            // If this is verification
            $product_id = $input['product_id'];
            if ($product_id == '5') {
                CaseUtils::start_verification_case($customer_id);
                // Return success message
                $message = lang('create_case_success');
                $this->success_output($message, array(
                    "product_id" => $product_id
                ));
                return;
            }
            
            $customer_address = $this->customers_address_m->get_by_many(array(
                'customer_id' => $customer_id
            ));
            $data = array(
                "customer_id" => $customer_id,
                "opening_date" => now(),
                "case_identifier" => $new_case_id,
                "product_id" => $input['product_id'],
                "country" => $customer_address->invoicing_country,
                "status" => '0',
                "description" => '',
                "created_date" => time()
            );
            
            // open transaction
            $this->cases_m->db->trans_begin();
            
            // Insert milestone instance
            $array_condition = array();
            $array_condition['cases_milestone.product_id'] = $input['product_id'];
            $query_result = $this->cases_milestone_m->get_milestone_paging($array_condition, 0, 1000, null, null);
            // Process output data
            $milestone_datas = $query_result['data'];
            
            // Get all base task name
            $all_tasks = $this->cases_product_base_taskname_m->get_many_by_many(array(
                'activate_flag' => APConstants::ON_FLAG
            ));
            $map_base_taskname = array();
            foreach ($all_tasks as $task) {
                $map_base_taskname[$task->base_taskname] = $task->base_taskname;
            }
            
            foreach ($milestone_datas as $milestone) {
                $milestone_instance_id = $this->cases_milestone_instance_m->insert(array(
                    "case_id" => $id,
                    "milestone_id" => $milestone->id,
                    "partner_id" => $milestone->partner_id,
                    "status" => 0,
                    "created_date" => now()
                ));
                
                // Insert milestone task instance
                $list_tasks = $this->cases_taskname_m->get_many_by_many(array(
                    "milestone_id" => $milestone->id
                ));
                foreach ($list_tasks as $task) {
                    // Only insert activate task
                    if (! array_key_exists($task->base_task_name, $map_base_taskname)) {
                        continue;
                    }
                    
                    $this->cases_taskname_instance_m->insert(array(
                        "milestone_instance_id" => $milestone_instance_id,
                        "base_task_name" => $task->base_task_name,
                        "case_id" => $id,
                        "task_name" => $task->task_name,
                        "status" => 0,
                        "created_date" => now()
                    ));
                }
            }
            
            // commit transaction.
            if ($this->cases_m->db->trans_status() === TRUE) {
                $this->cases_m->db->trans_commit();
                $message = lang('create_case_success');
                $this->success_output($message, array(
                    "product_id" => $product_id
                ));
                return;
                // redirect('/cases');
            } else {
                $this->cases_m->db->trans_rollback();
            }
            
            // Return success message
            $message = lang('create_case_success');
            $this->success_output($message, array(
                "product_id" => $product_id
            ));
            return;
        }
        
        $products = $this->cases_product_m->get_all ();
        // $products = $this->cases_product_m->get_cases_is_active(array());
        
        // Get all countries
        $countries = $this->countries_m->get_many_by_many(array(), '', false, array(
            'country_name' => 'ASC'
        ));
        $this->template->set("countries", $countries);
        $this->template->set("products", $products);
        
        $this->template->build("form");
    }

    /**
     * create case function
     * @createdBy: d3jsexperts
     */
    public function under_construction()
    {
        $this->template->build("page_construction");
    }

    /**
     * create case function
     * @createdBy: d3jsexperts
     */
    public function verification()
    {
        $this->template->build("page_construction");
    }

    /**
     * Get partner name by case_id.
     *
     * @param unknown $case_id            
     * @param unknown $list            
     * @return string
     */
    private function get_partner_name($case_id, $list)
    {
        $partner_name = "";
        foreach ($list as $row) {
            if ($case_id == $row->case_id) {
                $partner_name = $row->partner_name;
                break;
            }
        }
        return $partner_name;
    }
}