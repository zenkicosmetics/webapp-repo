<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Admin controller for the users module
 */
class Admin extends Admin_Controller {
    
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
                    'field' => 'panel_code',
                    'label' => 'lang:panel_code',
                    'rules' => 'required|validname|max_length[50]' 
            ),
            array (
                    'field' => 'description',
                    'label' => 'lang:description',
                    'rules' => 'validname|max_length[500]'
            ),
            array (
                    'field' => 'location_id',
                    'label' => 'lang:location',
                    'rules' => 'required'
            ),
            array (
                    'field' => 'message_title',
                    'label' => 'lang:message_title',
                    'rules' => ''
            ),
            array (
                    'field' => 'message_summary',
                    'label' => 'lang:message_summary',
                    'rules' => ''
            ),
            array (
                    'field' => 'message_fulltext',
                    'label' => 'lang:message_fulltext',
                    'rules' => ''
            ),
            array (
                    'field' => 'wifi_ssid',
                    'label' => 'lang:wifi_ssid',
                    'rules' => ''
            ),
            array (
                    'field' => 'wifi_password',
                    'label' => 'lang:wifi_password',
                    'rules' => ''
            )
    );
    
    /**
     * Constructor method
     */
    public function __construct() {
        parent::__construct();
        
        // Load the required classes
	$this->load->model('addresses/location_m');
        $this->load->model('addresses/location_customers_m');
        $this->load->model('digital_devices_m');
        $this->load->model('digital_devices_setting_m');
        $this->load->library('form_validation');
        $this->lang->load('devices');
    }
    
    /**
     * List all devices. Using for device panel
     */
    public function index() {
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
            $query_result = $this->digital_devices_m->get_device_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);

            // Process output data
            $total = $query_result['total'];
            $datas = $query_result['data'];
            
            // Get output response
            $response = $this->get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);
            
            #1058 add multi dimension capability for admin
            $date_format = APUtils::get_date_format_in_user_profiles();
            
            $i = 0;
            foreach ( $datas as $row ) {
                $deviceLastPing = strtotime($row->last_ping_received);
                $status = 'online';
                if ($deviceLastPing < (time() - (10 * 60))) {
                    if ($deviceLastPing < $row->created_date) {
                        $deviceLastPing = $row->created_date;
                    }
                    $status = 'offline since ' . APUtils::viewDateFormat($deviceLastPing,$date_format.APConstants::TIMEFORMAT_OUTPUT02);
                }
                $response->rows[$i]['id'] = $row->id;
                $response->rows[$i]['cell'] = array (
                    $row->id,
                    $row->panel_code,
                    $row->type,
                    $row->location_name,
                    $row->description,
                    $row->timezone,
                    $row->current_revision,
                    APUtils::viewDateFormat(strtotime($row->last_data_update),$date_format.APConstants::TIMEFORMAT_OUTPUT02),
                    $status,
                    $row->id
                );
                $i ++;
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
    public function add() {
        $device = new stdClass();
        $device->id = '';
        $device->type = '';
        $this->template->set_layout(FALSE);

        if ($_POST) {
            $this->form_validation->set_rules($this->validation_rules);

            $panel_code = $this->input->post('panel_code');
            $description = $this->input->post("description");
            $location_id = $this->input->post("location_id");
            $timezone = $this->input->post("timezone");
            
            $message_title = $this->input->post("message_title");
            $message_summary = $this->input->post("message_summary");
            $message_fulltext = $this->input->post("message_fulltext");
            $wifi_ssid = $this->input->post("wifi_ssid");
            $wifi_password = $this->input->post("wifi_password");
            
            if ($this->form_validation->run()) {
                // Insert data to database
                $id = $this->digital_devices_m->insert(array (
                        "panel_code" => $panel_code,
                        "description" => $description,
                        "location_id" => $location_id,
                        "created_date" => time(),
                        "timezone" => $timezone
                ));
                
                // Insert or update panel code
                $device_setting = $this->digital_devices_setting_m->get_by("panel_code", $panel_code);
                if (empty($device_setting)) {
                    $this->digital_devices_setting_m->insert(array (
                        "panel_code" => $panel_code,
                        "message_title" => $message_title,
                        "message_summary" => $message_summary,
                        "message_fulltext" => $message_fulltext,
                        "wifi_ssid" => $wifi_ssid,
                    	"wifi_password" => $wifi_password
                    ));
                } else {
                    $this->digital_devices_setting_m->update_by_many(array (
                        "panel_code" => $panel_code,
                    ), array (
                        "message_title" => $message_title,
                        "message_summary" => $message_summary,
                        "message_fulltext" => $message_fulltext,
                        "wifi_ssid" => $wifi_ssid,
                    	"wifi_password" => $wifi_password
                    ));
                }
                
                $message = lang('add_device_success');
                $this->success_output($message);
                return;
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }
        
        // Loop through each validation rule
        foreach ( $this->validation_rules as $rule ) {
            $device->{$rule ['field']} = set_value($rule ['field']);
        }
        
        $locations = $this->location_m->get_location_by_partner(false);
        $enterprise_locations = $this->location_customers_m->get_all_enterprise_location();
        $this->template->set('enterprise_locations', $this->get_enterprise_locations($enterprise_locations));
        // Display the current page
        $this->template->set('device', $device)->set('action_type', 'add')->set('locations', $locations)->set('timezones', timezone_identifiers_list())->build('admin/form');
    }
    
    /**
     * Call clevverhub to update wifi name and password
     * 1. http://10.12.0.3/api/v0.1/wifi?ssid=ClevverTaler&psk=27488891729406525274
     * @param unknown_type $ip
     * @param unknown_type $ssid
     * @param unknown_type $pass
     */
    private function broadcastSettingToDevce($ip, $ssid, $pass) {
        if (empty($ip) || empty($ssid) || empty($pass)) {
            return false;
        }
        $url = "http://".$ip."/api/v0.1/wifi";
        
        $proxy_url = 'http://proxy.clevvermail.com/proxy?method=get&destination='.$url;
        $proxy_url = $proxy_url.'&api_key=Sna9074Ans9Ha7134Isn013HSansIUSm1Na21';
        $proxy_url = $proxy_url.'&body=test&ssid='.urlencode($ssid).'&psk='.urlencode($pass);

        log_message(APConstants::LOG_DEBUG, 'Call URL:'.$proxy_url);
        
        APUtils::callRemoteUrl($proxy_url);
        return true;
    }
    
    /**
     * Edit an existing user
     *
     * @param int $id
     *            The id of the user.
     */
    public function edit() {
        $this->template->set_layout(FALSE);
        $id = $this->input->get_post("id");
        
        // Get the user's data
        $device = $this->digital_devices_m->get_by("id", $id);
        $device_type = $device->type;
        $panel_code = '';
        if (!empty($device)) {
            $panel_code = $device->panel_code;
        }
        $device_setting = $this->digital_devices_setting_m->get_by("panel_code", $panel_code);
        if (!empty($device_setting)) {
            $device->message_title = $device_setting->message_title;
            $device->message_summary = $device_setting->message_summary;
            $device->message_fulltext = $device_setting->message_fulltext;
            $device->wifi_ssid = $device_setting->wifi_ssid;
            $device->wifi_password = $device_setting->wifi_password;
        } else {
            $device->message_title = '';
            $device->message_summary = '';
            $device->message_fulltext = '';
            $device->wifi_ssid = '';
            $device->wifi_password = '';
        }
        
        if ($_POST) {
            // Set the validation rules
            $this->form_validation->set_rules($this->validation_rules);
            
            if ($this->form_validation->run()) {
                $panel_code = $this->input->post('panel_code');
                $description = $this->input->post("description");
	            $location_id = $this->input->post("location_id");
	            $timezone = $this->input->post("timezone");
	            
	            $message_title = $this->input->post("message_title");
                $message_summary = $this->input->post("message_summary");
                $message_fulltext = $this->input->post("message_fulltext");
                $wifi_ssid = $this->input->post("wifi_ssid");
                $wifi_password = $this->input->post("wifi_password");

                // Save data to database
                $restul = $this->digital_devices_m->update_by_many(array (
                        "id" => $id,
                ), array (
                        "panel_code" => $panel_code,
                        "description" => $description,
                        "location_id" => $location_id,
	                    "timezone" => $timezone,
                ));
                
                // Insert or update panel code
                $device_setting = $this->digital_devices_setting_m->get_by("panel_code", $panel_code);
                if (empty($device_setting)) {
                    $this->digital_devices_setting_m->insert(array (
                        "panel_code" => $panel_code,
                        "message_title" => $message_title,
                        "message_summary" => $message_summary,
                        "message_fulltext" => $message_fulltext,
                        "wifi_ssid" => $wifi_ssid,
                    	"wifi_password" => $wifi_password
                    ));
                } else {
                    
                    if ($device_type == 'clevverhub') {
                        $this->digital_devices_setting_m->update_by_many(array (
                        	"panel_code" => $panel_code,
                        ), array (
                            "wifi_ssid" => $wifi_ssid,
                        	"wifi_password" => $wifi_password
                        ));
                    } else {
                        $this->digital_devices_setting_m->update_by_many(array (
                            "panel_code" => $panel_code,
                        ), array (
                            "message_title" => $message_title,
                            "message_summary" => $message_summary,
                            "message_fulltext" => $message_fulltext
                        ));
                    }
                }
                
                if ($device_type == 'clevverhub') {
                    // Check if IP is not NULL and password alread change
                    if (!empty($device->ip) && !empty($wifi_ssid) && !empty($wifi_password)
                        && ($device_setting->wifi_ssid != $wifi_ssid
                          || $device_setting->wifi_password != $wifi_password)) {
                        log_message(APConstants::LOG_DEBUG, 'Send broad cast message to device'.$device->ip. '| SID: '.$wifi_ssid.'| Pass: '.$wifi_password);
                        $this->broadcastSettingToDevce($device->ip, $wifi_ssid, $wifi_password);
                    }
                }
                
                $message = lang('edit_device_success');
                $this->success_output($message);
                return;
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }
        
        // Display the current page
        $locations = $this->location_m->get_location_by_partner(false);
        $enterprise_locations = $this->location_customers_m->get_all_enterprise_location();
        $this->template->set('enterprise_locations', $this->get_enterprise_locations($enterprise_locations));
        $this->template->set('device', $device)->set('action_type', 'edit')->set('locations', $locations)->set('timezones', timezone_identifiers_list())->build('admin/form');
    }
    
    /**
     * Get mapping enterprise location.
     * 
     * @param type $enterprise_locations
     */
    private function get_enterprise_locations($enterprise_locations) {
        $map_enterprise_locations = array();
        foreach ($enterprise_locations as $item) {
            if (!array_key_exists($item->location_id, $map_enterprise_locations)) {
                $map_enterprise_locations[$item->location_id] = $item->customer_code;
            }
        }
        return $map_enterprise_locations;
    }
    
    /**
     * Edit an existing user
     *
     * @param int $id
     *            The id of the user.
     */
    public function delete() {
        $this->template->set_layout(FALSE);
        $id = $this->input->get_post("id");
        
        // delete partner
        $this->digital_devices_m->delete_by("id", $id);
        
        // output message.
        $message = lang('delete_device_success');
        $this->success_output($message);
        return;
    }
}