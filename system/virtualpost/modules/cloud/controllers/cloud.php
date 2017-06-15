<?php

if (! defined('BASEPATH'))
    exit('No direct script access allowed');
class cloud extends AccountSetting_Controller {
    /**
     * Validation for basic profile
     * data.
     * The rest of the validation is
     * built by streams.
     * 
     * @var array
     */
    private $validation_rules = array (
            
            array (
                    'field' => 'folder_name',
                    'label' => 'lang:cloud.folder_name',
                    'rules' => 'required' 
            ),
            array (
                    'field' => 'auto_save_flag',
                    'label' => 'lang:cloud.auto_save',
                    'rules' => '' 
            ) 
    );
    
    /**
     * Loads the gazillion of stuff, in Flash Gordon speed.
     * 
     * @todo Document properly please.
     */
    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        
        // Load language
        $this->lang->load('cloud');
        
        // load the theme_example view
        $this->load->model('cloud/cloud_m');
        $this->load->model('cloud/customer_cloud_m');
        $this->load->library('cloud/cloud_api');
    }
    
    /**
     * Index Page for this controller.
     * Maps to the following URL
     * http://example.com/index.php/welcome
     * - or -
     * http://example.com/index.php/welcome/index
     * - or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * 
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    public function index() {
        // Get all cloud service of customer
        $list_customer_id = array();
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $isPrimaryCustomer = APContext::isPrimaryCustomerUser();
        if ($isPrimaryCustomer) {
            $list_customer_id = CustomerUtils::getListCustomerIdOfEnterpriseCustomer($customer_id);
            $list_customer_id[] = $customer_id;
        } else {
            $list_customer_id[] = $customer_id;
        }
        $customer_cloud_service = $this->customer_cloud_m->get_all_cloud($list_customer_id);

        $customer = $this->customer_m->get_current_customer_info();
        $this->template->set('customer', $customer);

        // load the theme_example view
        $this->template->set('customer_cloud_service', $customer_cloud_service)->build('index');
    }
    
    /**
     * Delete customer cloud service.
     */
    public function delete() {
        $this->template->set_layout(FALSE);
        $customer_id = "";
        $cloud_id = $this->input->get_post('cloud_id');
        $postbox_id = $this->input->get_post('postbox_id');
        
        if(APContext::isEnterpriseCustomer()){
            if(!empty($postbox_id)){
                $customer_id = APContext::getCustomerCodeLoggedInMailboxByPostbox($postbox_id);
            }
        }

        if(empty($customer_id)){
            // Gets customerid logged in.
            $customer_id = APContext::getCustomerCodeLoggedIn();
        }
        
        // delete interface by id.
        cloud_api::delete_interface($customer_id, $cloud_id, $postbox_id);
        
        //Return json success
        $this->success_output('');
    }
    
    /**
     * Default page for 404 error. (Tam thoi khong su dung)
     */
    public function add() {
        $this->template->set_layout(FALSE);
        
        // Get all cloud service of customer
        $list_cloud_service = $this->cloud_m->get_many_by_many(array (
                "active_flag" => '1' 
        ));

        $setting = null;
        // When user submit data - now not run to this conditon code
        if ($_POST) {
            // Get cloud id
            $cloud_id = $this->input->get_post('cloud_id');
            $auto_save_flag = $this->input->get_post('auto_save');
            if (empty($auto_save_flag)) {
                $auto_save_flag = '0';
            }
            
            // Check exist cloud id
            $customer_cloud = $this->customer_cloud_m->get_by_many(array (
                    'cloud_id' => $cloud_id,
                    'customer_id' => APContext::getCustomerCodeLoggedIn() 
            ));
            
            if (! empty($customer_cloud)) {
                $this->error_output(lang("cloud.already_exist_error"));
                return;
            }
            
            // Add cloud
            $this->customer_cloud_m->insert(array (
                    'cloud_id' => $cloud_id,
                    'customer_id' => APContext::getCustomerCodeLoggedIn(),
                    "auto_save_flag" => $auto_save_flag 
            ));
            
            
            $customer_id = APContext::getCustomerCodeLoggedIn();
            $folder_name = $this->input->get_post('folder_name');
            $auto_save = $this->input->get_post('auto_save_flag');

            $setting['access_token'] = $this->session->userdata('access_token');
            $setting['folder_name'] = $folder_name;
            
            $this->customer_cloud_m->update_by_many(array (
                    "cloud_id" => APConstants::CLOUD_DROPBOX_CODE,
                    "customer_id" => $customer_id 
            ), array (
                    "auto_save_flag" => empty($auto_save) ? '0' : $auto_save,
                    "settings" => json_encode($setting)
            ));
            
            //Add cloud history
            LogUtils::log_customer_cloud_history($customer_id);
            $this->success_output(lang("cloud.add_success"));
            return;
        }

        $this->template->set('list_cloud_service', $list_cloud_service)->build('add');
    }
    
    /**
     * Method for handling different form actions
     */
    public function dropbox_setting() {
        $this->template->set_layout(FALSE);

        // Get dropbox setting of current customer
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $customer_setting = $this->customer_cloud_m->get_by_many(array (
                "cloud_id" => APConstants::CLOUD_DROPBOX_CODE,
                "customer_id" => $customer_id
        ));

        $setting = null;
        if (! empty($customer_setting)) {
            if (! empty($customer_setting->settings)) {
                // Decode cloud setting
                $setting = json_decode($customer_setting->settings, true);
            }
            $setting['auto_save_flag'] = $customer_setting->auto_save_flag;
        }

        if ($_POST) {
            $this->form_validation->set_rules($this->validation_rules);
            if ($this->form_validation->run()) {
                $folder_name = $this->input->get_post('folder_name');
                $auto_save = $this->input->get_post('auto_save_flag');
                if (empty($auto_save)) {
                    $auto_save = '0';
                }

                $setting['folder_name'] = $folder_name;

                $this->customer_cloud_m->update_by_many(array (
                        "cloud_id" => APConstants::CLOUD_DROPBOX_CODE,
                        "customer_id" => $customer_id
                ), array (
                        "auto_save_flag" => $auto_save,
                        "settings" => json_encode($setting)
                ));

                //Add cloud history
                LogUtils::log_customer_cloud_history($customer_id);

                // Apply to current setting
                if (!empty($setting)){
                    $this->session->set_userdata(APConstants::SESSION_CLOUD_CUSTOMER_KEY, $setting);
                }

                $message = lang('cloud.save_success');
                $this->success_output($message);
                return;
            }
            else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }

        // Loop through each validation rule
        foreach ( $this->validation_rules as $rule ) {
            if (empty($setting["{$rule ['field']}"])) {
                $setting["{$rule ['field']}"] = set_value($rule ['field']);
            }
        }
        // Display the current page
        $this->template->set('setting', $setting)->build('dropbox_setting');
    }

    /**
     * Display select folder screen
     */
    public function select_folder() {
        $this->template->set_layout(FALSE);
        // Display the current page
        $this->template->build('dropbox_tree_folder');
    }
    
    /**
     * New select folder screen
     */
    public function new_folder() {
        $this->template->set_layout(FALSE);
        $parent_folder_name = $this->input->get_post('parent_folder_name');

        if ($_POST) {
            $dropboxV2 = APContext::getDropbox();
            $new_folder_name = $this->input->get_post('folder_name');
            $dropboxV2->create_folder("$parent_folder_name/$new_folder_name");

            return $this->success_output('');
        }
        else {
            // Display the current page
            $this->template->set('parent_folder_name', $parent_folder_name)->build('new_folder');
        }
    }

    /**
     * Build folder tree of dropbox
     */
    public function dropbox_folder_tree() {
        $this->template->set_layout(FALSE);
        $key = $this->input->get_post('key');
        
        $root_folder = '/';
        if (!empty($key)) {
            $root_folder = $key;
        }
        
        // Response array tree structure from database
        $rs = $this->loadChildNode($root_folder);
        
        // Response json format
        echo json_encode($rs);
    }
    /**
     * Load all child node.
     * 
     * @param unknown_type $root            
     * @return multitype:multitype:boolean NULL
     */
    private function loadChildNode($root) {
        $dropboxV2 = APContext::getDropbox();
        $data = $dropboxV2->getList($root, APConstants::DROPBOX_FOLDER_TAG);
        if(!isset($data)){
            return null;
        }

        // Node id selected
        $node_id = $this->session->userdata(APConstants::SESSION_TREE_NODE_ID);

        // Response array tree structure from database
        $result = array ();

        foreach ( $data as $node ) {
            $is_selected = ($node_id == $node['path_lower']) ? TRUE : FALSE;
            // Add node to the response array
            $result [] = array (
                    'id' => $node['id'],
                    'title' => $node['name'],
                    'nodeId' => $node['path_lower'],
                    'key' => $node['path_display'],
                    'isLazy' => TRUE,
                    'expand' => $is_selected ? TRUE : FALSE,
                    'select' => $is_selected ? TRUE : FALSE,
            );
        }
        return $result;
    }

     /**
     * Display register accounting email for postbox screen
     */
    public function accounting_email()
    {
        $this->template->set_layout(FALSE);
        $customer_id = APContext::getCustomerCodeLoggedIn();
        if(APContext::isPrimaryCustomerUser()){
            $list_customer_id = CustomerUtils::getListCustomerIdOfEnterpriseCustomer($customer_id);
            $list_customer_id[] = $customer_id;
            $postboxes = $this->postbox_m->get_postboxes_by_list_customer($list_customer_id);
        } else {
            $postboxes = $this->postbox_m->get_postboxes_by($customer_id);
        }
        $auto_send_pdf = APConstants::OFF_FLAG;
        $accounting_interface = null;
        $postbox_id = $this->input->get('postbox_id'); 
        if (!empty($postbox_id)) {
           $this->load->model('mailbox/postbox_setting_m');
           $accounting_interface = EnvelopeUtils::get_accounting_interface_by_postbox($postbox_id);
           $auto_send_pdf = $this->postbox_setting_m->get($postbox_id)->always_mark_invoice;
        } 
        // Display register accounting interface
        $this->template->set('postboxes', $postboxes);
        $this->template->set('postbox_id', $postbox_id);
        $this->template->set('auto_send_pdf', $auto_send_pdf);
        $this->template->set('accounting_interface', $accounting_interface);
        $this->template->build('accounting_email');
    }
    
    /**
     * Load accounting email of a postbox
     */
    public function load_accounting_email(){
        $this->template->set_layout(FALSE);
        if (!empty($this->input->get('postbox_id'))) {
           $postbox_id = $this->input->get('postbox_id'); 
           $accounting_interface = EnvelopeUtils::get_accounting_interface_by_postbox($postbox_id);
           $auto_send_pdf = APConstants::OFF_FLAG;
           if (!empty($postbox_id)) {
                $this->load->model('mailbox/postbox_setting_m');
                $auto_send_pdf = $this->postbox_setting_m->get($postbox_id)->always_mark_invoice;
           }

           $this->success_output('', array_merge($accounting_interface, array('auto_send_pdf' => $auto_send_pdf)));
           return;
        }
        $this->error_output('');
        return;
    }
    
    /**
     * Add, update accounting email for postbox
     */
    public function set_accounting_email()
    {
        $this->template->set_layout(FALSE);

        if ($_POST) {
            
            

            $accounting_email_validation_rules = array(
                array(
                    'field' => 'postbox_name',
                    'label' => 'lang:cloud.postbox',
                    'rules' => 'required'
                ),
                 array(
                    'field' => 'accounting_email',
                    'label' => 'lang:cloud.email',
                    'rules' => 'required|valid_email|max_length[255]'
                )
            );
            
            $customer_id = "";
            $this->form_validation->set_rules($accounting_email_validation_rules);
           
            if ($this->form_validation->run()) {
                
                $accountingEmail   = $this->input->post('accounting_email'); 
                $postbox_id = $this->input->get_post('postbox_name', '');
                $interface_id   = $this->input->post('interface_id');
                $auto_send_pdf   = $this->input->post('auto_send_pdf');
                $auto_send_pdf = $auto_send_pdf=='on' ? 1 : 0;
                if(APContext::isEnterpriseCustomer()){
                    $customer_id = APContext::getCustomerCodeLoggedInMailboxByPostbox($postbox_id);
                    
                }
                
                if(empty($customer_id)){
                    // Gets customerid logged in.
                    $customer_id = APContext::getCustomerCodeLoggedIn();
                }
                
                cloud_api::save_accounting_email($customer_id, $postbox_id, $accountingEmail, $interface_id, $auto_send_pdf);
                
                //Return success result
                $this->success_output('');
                return;
            } else {
                  $errors = $this->form_validation->error_json();
                  echo json_encode($errors);
                  return;
            }
        }
    }
    
}

