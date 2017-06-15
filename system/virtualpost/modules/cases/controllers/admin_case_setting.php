<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Admin controller for the users module
 */
class admin_case_setting extends Admin_Controller
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
                    'field' => 'product_id',
                    'label' => 'Case type ',
                    'rules' => ''
            ),
            array(
                    'field' => 'case_instance_name',
                    'label' => 'Case name',
                    'rules' => 'required'
            ),
            array(
                    'field' => 'list_milestone_id',
                    'label' => 'Milestone',
                    'rules' => ''
            )
    );

    /**
     * Constructor method
     */
    public function __construct ()
    {
        parent::__construct();
        
        // Load the required classes
        $this->load->model('settings/countries_m');
        $this->load->model('addresses/location_m');
        $this->load->model('cases/cases_product_base_taskname_m');
        $this->load->model('cases/cases_product_m');
        $this->load->model('cases/cases_milestone_m');
        $this->load->model('cases/cases_instance_m');
        
        $this->load->library('form_validation');
        $this->lang->load('cases/cases');
    }

    /**
     * List all devices.
     * Using for device panel
     */
    public function index ()
    {
        $enquiry = $this->input->get_post('enquiry');
        // If current request is ajax
        if ($this->is_ajax_request()) {
            $array_condition = array();
            
            // update limit into user_paging.
            $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);
            
            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging['limit'] = $limit;
            
            if ($input_paging['sort_column'] == 'product_name') {
                $input_paging['sort_column'] = 'product_name';
            }
            
            // Call search method
            $query_result = $this->cases_instance_m->get_paging($array_condition, $input_paging['start'], $input_paging['limit'], 
                    $input_paging['sort_column'], $input_paging['sort_type']);
            
            // Process output data
            $total = $query_result['total'];
            $datas = $query_result['data'];
            
            // Get output response
            $response = $this->get_paging_output($total, $input_paging['limit'], $input_paging['page']);
            
            $products = $this->cases_product_m->get_all();
            $map_products = array();
            foreach ($products as $product) {
                $map_products[$product->id] = $product->product_name;
            }
            
            $i = 0;
            foreach ($datas as $row) {
                $product_name = '';
                if (array_key_exists($row->product_id, $map_products)) {
                    $product_name = $map_products[$row->product_id];
                }
                
                $response->rows[$i]['id'] = $row->id;
                $response->rows[$i]['cell'] = array(
                        $row->id,
                        $product_name,
                        $row->product_id,
                        $row->case_instance_name,
                        $row->id
                );
                $i ++;
            }
            
            echo json_encode($response);
        }
        else {
            
            // Display the current page
            $this->template->build('admin/case_setting/index');
        }
    }

    /**
     * Edit an existing user
     * 
     * @param int $id
     *            The id of the user.
     */
    public function edit ()
    {
        $this->template->set_layout(FALSE);
        $id = $this->input->get_post("id");
        // Get the user's data
        $case_setting = $this->cases_instance_m->get_by("id", $id);
        if (empty($case_setting)) {
            $this->template->set('action_type', 'add');
            $case_setting = new stdClass();
            $case_setting->id = 0;
            // Loop through each validation rule
            foreach ($this->validation_rules as $rule) {
                $case_setting->{$rule['field']} = set_value($rule['field']);
            }
        }
        else {
            $this->template->set('action_type', 'edit');
            // Gets list case selected.
            $list_selected_milestone_id = array();
            if ($case_setting->list_milestone_id) {
            	$arrList = explode(',', $case_setting->list_milestone_id);
            	foreach ($arrList as $milestone_id) {
            		$list_selected_milestone_id[] = $milestone_id;
            	}
            }
            
            // List case number config
            $list_available_milestone = array();
            $list_selected_milestone = array();
            $list_all_milestone = $this->cases_milestone_m->get_many_by_many(array(
            		'product_id' => $case_setting->product_id
            ));
            foreach ($list_all_milestone as $milestone) {
            	if (in_array($milestone->id, $list_selected_milestone_id)) {
            		$list_selected_milestone[] = $milestone;
            	} else {
            		$list_available_milestone[] = $milestone;
            	}
            }
            
            $this->template->set('list_selected_milestone', $list_selected_milestone);
            $this->template->set('list_available_milestone', $list_available_milestone);
        }
        if ($_POST) {
            // Set the validation rules
            $this->form_validation->set_rules($this->validation_rules);
            
            if ($this->form_validation->run()) {
                $case_setting_check = $this->cases_instance_m->get_by("id", $id);
                $product_id = $this->input->get_post("product_id");
                $case_instance_name = $this->input->get_post("case_instance_name");
                $list_milestone_id_input = $this->input->get_post("list_milestone_id");
                $list_milestone_id = '';
                if (! empty($list_milestone_id_input)) {
                	$list_milestone_id = implode(",", $list_milestone_id_input);
                }
                
                if (! empty($case_setting_check)) {
                    $this->cases_instance_m->update_by_many(array(
                            "id" => $id
                    ), 
                            array(
                                    "case_instance_name" => $case_instance_name,
                                    "list_milestone_id" => $list_milestone_id,
                                    "updated_date" => now()
                            ));
                }
                else {
                    $this->cases_instance_m->insert(
                            array(
                                    "product_id" => $product_id,
                                    "case_instance_name" => $case_instance_name,
                                    "list_milestone_id" => $list_milestone_id,
                                    "created_date" => now()
                            ));
                }
                $message = lang('save_case_setting_success');
                $this->success_output($message);
                return;
            }
            else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }
        
        $products = $this->cases_product_m->get_all();
        $this->template->set('products', $products);
        $this->template->set('case_setting', $case_setting);
        
        // Display the current page
        $this->template->build('admin/case_setting/form');
    }

    /**
     * Delete condition
     */
    public function delete ()
    {
        $condition_id = $this->input->get_post("id");
        
        $this->cases_instance_m->delete_by_many(array(
                'id' => $condition_id
        ));
        
        $message = sprintf(lang('delete_case_setting_success'));
        $this->success_output($message);
    }
}