<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . "third_party/MX/Controller.php";

class MY_Controller extends MX_Controller
{
    /**
     * No longer used globally
     *
     * @deprecated remove in 2.2
     */
    protected $data;

    /**
     * The name of the module that this controller instance actually belongs to.
     *
     * @var string
     */
    public $module;

    /**
     * The name of the controller class for the current class instance.
     *
     * @var string
     */
    public $controller;

    /**
     * The name of the method for the current request.
     *
     * @var string
     */
    public $method;

    /**
     * Load and set data for some common used libraries.
     */
    public function __construct()
    {
        parent::__construct();

        // Add TienNH 2013-01-03
        $this->load->library(array(
            'users/ion_auth',
            'pyrocache',
            "Exceptions/BusinessException",
            "Exceptions/SystemException",
            "Exceptions/DAOException",
            "Exceptions/ThirdPartyException",
        ));

        // Get user data
        $this->template->current_user = ci()->current_user = $this->current_user = $this->ion_auth->get_user();

        // Work out module, controller and method and make them accessable
        // throught the CI instance
        ci()->module = $this->module = $this->router->fetch_module();
        ci()->controller = $this->controller = $this->router->fetch_class();
        ci()->method = $this->method = $this->router->fetch_method();

        // Loaded after $this->current_user is set so that data can be used
        // everywhere
        $this->load->model(array(
            'modules/module_m',
            'groups/permission_m',
            'instances/instance_m'
        ));

        // List available module permissions for this user
        ci()->permissions = $this->permissions = $this->current_user ? $this->permission_m->get_group($this->current_user->group_id) : array();
        // Get meta data for the module
        $this->template->module_details = ci()->module_details = $this->module_details = $this->module_m->get($this->module);

        // If the module is disabled, then show a 404.
        empty($this->module_details ['enableflag']) and show_404();

        if ($this->module and isset($this->module_details ['path'])) {
            Asset::add_path('module', $this->module_details ['path'] . '/');
        }

        $list_instances = array();
        if (APContext::isSupperAdminUser()) {
            $list_instances = $this->instance_m->get_all();
        }
        $this->template->set('list_instances', $list_instances);
        $this->template->set('module', $this->module)->set('controller', $this->controller)->set('method', $this->method);
        // End TienNH 2013-01-03
        
        // Add web request
        $this->_log_web_request();
    }

    /**
     * Output data to json.
     * Enter description here ...
     *
     * @param unknown_type $data
     */
    protected function outputJson($data)
    {
        if (!isset($data)) {
            return json_encode(array(
                'status' => false
            ));
        }

        if (!isset($data ['status'])) {
            return json_encode($data);
        }
        return json_encode($data);
    }

    /**
     * Get paging input.
     *
     * @return array() object
     */
    protected function get_paging_input()
    {
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10;
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : '';
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'ASC';

        $start = $limit * $page - $limit;
        $start = ($start < 0) ? 0 : $start;

        return array(
            "page" => $page,
            "start" => $start,
            "limit" => $limit,
            "sort_type" => $sord,
            "sort_column" => $sidx
        );
    }

    /**
     * Get paging input.
     *
     * @return array() object
     */
    protected function post_paging_input()
    {
        $page = isset($_POST ['page']) ? $_POST ['page'] : 1;
        $limit = isset($_POST ['rows']) ? $_POST ['rows'] : 10;
        $sidx = isset($_POST ['sidx']) ? $_POST ['sidx'] : '';
        $sord = isset($_POST ['sord']) ? $_POST ['sord'] : 'ASC';
        $start = $limit * $page - $limit;
        $start = ($start < 0) ? 0 : $start;

        return array(
            "page" => $page,
            "start" => $start,
            "limit" => $limit,
            "sort_type" => $sord,
            "sort_column" => $sidx
        );
    }

    /**
     * Output JSON success message
     *
     * @param unknown_type $message
     */
    protected function success_output($message, $data = array())
    {
        echo json_encode(array(
            "status" => TRUE,
            "message" => $message,
            "data" => $data
        ));
    }

    /**
     * Output JSON success message
     *
     * @param unknown_type $message
     */
    protected function error_output($message, $data = array())
    {
        echo json_encode(array(
            "status" => FALSE,
            "message" => $message,
            "data" => $data
        ));
    }

    /**
     * Get paging input.
     *
     * @return array() object
     */
    protected function get_paging_output($total, $limit, $page)
    {
        $response = new stdClass();
        $response->page = $page;
        if ($total > 0) {
            $total_pages = ceil($total / $limit);
        } else {
            $total_pages = 0;
        }
        $response->total = $total_pages;
        $response->records = $total;

        return $response;
    }

    /**
     * Check current request is ajax or not.
     *
     * @return boolean
     */
    protected function is_ajax_request()
    {
        return (isset($_SERVER ['HTTP_X_REQUESTED_WITH']) && $_SERVER ['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') ? TRUE : FALSE;
    }
    
    // Request Parameter Log function
    protected function _log_web_request() {
        if(ENVIRONMENT == 'production'){
            return true;
        }
        
        $request_method = $this->input->server('REQUEST_METHOD');
        $request_param = '';
        if ($request_method == 'GET') {
            $request_param = json_encode($this->input->get());
        } else {
            $post_data = $this->input->post();
            $request_param = json_encode($post_data);
        }
        $isCustomer = APContext::isCustomerLoggedIn();
        return $this->web_message_log_m->insert(array(
                    'request_from' => $isCustomer ? 'customer' : '',
                    'request_by' => APContext::getCustomerCodeLoggedIn(),
                    'uri' => $this->uri->uri_string(),
                    'uri_param' => $_SERVER['QUERY_STRING'],
                    'request_method' => $request_method,
                    'request_header' => json_encode($this->input->request_headers()),
                    'request_param' => $request_param,
                    'request_date' => function_exists('now') ? now() : time(),
                    'ip_address' => $this->input->ip_address()
        ));
    }
}

/**
 * Returns the CodeIgniter object.
 * Example: ci()->db->get('table');
 *
 * @return \CI_Controller
 */
function ci()
{
    return get_instance();
}
