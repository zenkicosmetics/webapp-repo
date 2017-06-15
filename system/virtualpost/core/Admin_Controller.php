<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * This is the basis for the Admin class that is used throughout PartsDB.
 * 
 * Code here is run before admin controllers
 * 
 * @package DungNT
 */
class Admin_Controller extends MY_Controller {

	/**
	 * Admin controllers can have sections, normally an arbitrary string
	 *
	 * @var string 
	 */
	protected $section = NULL;

	/**
	 * Load language, check flashdata, define https, load and setup the data 
	 * for the admin theme
	 */
	public function __construct()
	{
		parent::__construct();
		
		// Load admin themes helper
		$this->load->helper('admin_theme');
		
		// Show error and exit if the user does not have sufficient permissions
		
		if ( ! self::_check_access())
		{
		    $this->session->set_flashdata('error', lang('cp_access_denied'));
		    redirect('admin/login');
		}
		
		$admin_theme = Settings::get(APConstants::ADMIN_THEMES_CODE);
		$web_path = APPPATH.'themes/'.$admin_theme.'/';
		
		// Set the location of assets
		Asset::add_path('theme', $web_path);
		Asset::set_path('theme');
		// Template configuration
		$this->template
			->enable_parser(FALSE)
			->set_theme($admin_theme)
			->set_layout('default', '');
	}

	/**
	 * Checks to see if a user object has access rights to the admin area.
	 *
	 * @return boolean 
	 */
	private function _check_access()
	{
		// These pages get past permission checks
		$ignored_pages = array('admin/login', 'admin/logout', 'admin/help', 'scans/todo/execute_scan');

		// Check if the current page is to be ignored
		$current_page = $this->uri->segment(1, '') . '/' . $this->uri->segment(2, 'index');

		// By pass execute scan method
		if ($this->controller === 'todo' && $this->method === 'execute_scan') {
		    return TRUE;
		}
		
		// Check if this customer is supper admin
		if (APContext::isSupperAdminUser()) {
		    return TRUE;
		}

		// Dont need to log in, this is an open page
		if (in_array($current_page, $ignored_pages))
		{
			return TRUE;
		}
		else if ( ! $this->current_user)
		{
		    if ($this->is_ajax_request()) {
		        // redirect('customers/logout_ajax');
		        redirect('admin/ajax_login');
		    } else {
		        log_message(APConstants::LOG_DEBUG, '>>>>>> Redirect to admin/login page');
		        redirect('admin/login');
		    }
		}

		// Admins can go straight in
		else if (APContext::isAdminUser() || 
		        APContext::isAdminParner() ||
		        APContext::isAdminLocation() ||
		        APContext::isSupperAdminUser ()||
                APContext::isAdminServiceParner() ||
                APContext::isWorkerAdmin() ){
			return TRUE;
		}
		
		// Well they at least better have permissions!
		else if ($this->current_user)
		{
			// We are looking at the index page. Show it if they have ANY admin access at all
			if ($current_page == 'admin/index' && $this->permissions)
			{
				return TRUE;
			}
			else
			{
			    if(empty($this->module) && $current_page == 'admin/dashboard'){
			        return TRUE;
			    }
				// Check if the current user can view that page
				return $this->_check_key_in_array($this->module, $this->permissions);
			}
		}

		// god knows what this is... erm...
		return FALSE;
	}
	
	/**
	 * Check key exist in array (case-sensitive)
	 * 
	 * @param unknown_type $key_check
	 * @param unknown_type $myArray
	 * @return boolean
	 */
	private function _check_key_in_array($key_check , $myArray){
	    $keys = array_keys($myArray);
	    foreach($keys as $key)
	    {
	    	if(strtolower($key) == $key_check){
	    	    return TRUE;
	    	}
	    }
	    return FALSE;
	}

}