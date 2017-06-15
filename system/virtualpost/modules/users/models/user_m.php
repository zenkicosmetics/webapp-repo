<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 * @author		PyroCMS Dev Team
 * @package		PyroCMS\Core\Modules\Users\Models
 */
class User_m extends MY_Model
{
    function __construct()
    {
        parent::__construct();
        $this->_table = $this->profile_table = $this->db->dbprefix('users');
        $this->primary_key = "id";
        ci()->load->model(array(         
            'addresses/location_users_m' 
        ));
    }
    
    /**
     * Get all paging data
     *
     * @param unknown_type $array_where
     *            The array of condition (array ('name' => 'DungNT', 'age' =>
     *            30))
     * @param unknown_type $start
     *            The offset paging
     * @param unknown_type $limit
     *            The number of record per page
     * @param unknown_type $sort_column
     *            The sort column
     * @param unknown_type $sort_type
     *            The sort type
     * @return The array object array('total' => '9999', 'data' => '');
     */
    public function get_user_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC') {
        // Count all record with input condition
        $total_record = $this->count_user_paging($array_where);
        if ($total_record == 0) {
            return array (
                    "total" => 0,
                    "data" => array ()
            );
        }
    
        $this->db->select('users.*, p.partner_name, p.partner_code, p.partner_id');
        $this->db->select('l.location_name');

        $this->db->join('group_users', 'group_users.user_id = users.id');
        $this->db->join('partner_partner as p', 'p.partner_id = users.partner_id', 'left');
        $this->db->join('location_users', 'users.id=location_users.user_id', 'left');
        $this->db->join('location as l', 'location_users.location_id = l.id', 'left');
        // Search all data with input condition
        foreach ( $array_where as $key => $value ) {
            $this->db->where($key, $value);
        }

        $this->db->limit($limit);
        if (! empty($sort_column)) {
            $this->db->order_by($sort_column, $sort_type);
        }
        $this->db->group_by("users.id");
        
        $data = $this->db->get($this->_table, $limit, $start)->result();
    
        return array (
                "total" => $total_record,
                "data" => $data
        );
    }
    
    public function count_user_paging($array_where) {

    	$this->db->select('COUNT(users.id) AS TotalRecord');
    	$this->db->join('group_users', 'group_users.user_id = users.id');
        $this->db->join('partner_partner as p', 'p.partner_id = users.partner_id', 'left');
        $this->db->join('location_users', 'users.id=location_users.user_id', 'left');
        $this->db->join('location as l', 'location_users.location_id = l.id', 'left');
        // Search all data with input condition
        foreach ( $array_where as $key => $value ) {
            $this->db->where($key, $value);
        }
        $this->db->group_by("users.id");

    	$row = $this->db->get($this->_table)->result();
    	return count($row);
    }
    
	// --------------------------------------------------------------------------

    /**
     * Get a specified (single) user
     *
     * @access 	public
     * @param 	array
     * @return 	obj
     */
    public function get($params)
    {
    	if (isset($params['id']))
    	{
    		$this->db->where('users.id', $params['id']);
    	}

    	if (isset($params['email']))
    	{
    		$this->db->where('LOWER('.$this->db->dbprefix('users.email').')', strtolower($params['email']));
    	}

    	if (isset($params['role']))
    	{
    		$this->db->where('users.group_id', $params['role']);
    	}

    	$this->db
			->select($this->profile_table.'.*, users.*')
			->limit(1)
			->join('profiles', 'profiles.user_id = users.id', 'left');

    	return $this->db->get('users')->row();
    }

	// --------------------------------------------------------------------------

    /**
     * Get recent users
     *
     * @acces 	public
     * @param 	int - limit - defaults to 10
     * @return 	obj
     */
    public function get_recent($limit = 10)
    {
		$this->db->order_by('users.created_on', 'desc');
		$this->db->limit($limit);
		return $this->get_all();
    }

	// --------------------------------------------------------------------------

    public function get_all()
    {
    	$this->db
			->select($this->profile_table.'.*, g.description as group_name, users.*')
			->join('groups g', 'g.id = users.group_id')
			->join('profiles', 'profiles.user_id = users.id', 'left')
			->group_by('users.id');
	
    	return parent::get_all();
    }

	// --------------------------------------------------------------------------

    /**
     * Create a new user
     */
    public function add($input = array())
    {
		$this->load->helper('date');

        return parent::insert(array(
			'email'				=> $input->email,
			'password'			=> $input->password,
			'salt'				=> $input->salt,
			'role' 				=> empty($input->role) ? 'user' : $input->role,
			'is_active' 		=> 0,
			'lang'				=> $this->config->item('default_language'),
			'activation_code' 	=> $input->activation_code,
			'created_on' 		=> now(),
			'last_login'		=> now(),
			'ip' 				=> $this->input->ip_address()
        ));
    }

	// --------------------------------------------------------------------------

    /**
     * Update the last login time
     */
    public function update_last_login($id)
    {
        $this->db->update('users', array('last_login' => now()), array('id' => $id));
    }

	// --------------------------------------------------------------------------

    /**
     * Activate a newly created user
     */
    function activate($id)
    {
        return parent::update($id, array('is_active' => 1, 'activation_code' => ''));
    }

	// --------------------------------------------------------------------------

    /**
     * Count by
     */
    public function count_by($params = array())
    {
		$this->db->from($this->_table)->join('profiles', 'users.id = profiles.user_id', 'left');

		if ( ! empty($params['active']))
		{
		    $params['active'] = $params['active'] === 2 ? 0 : $params['active'] ;
		    $this->db->where('users.active', $params['active']);
		}

		if ( ! empty($params['group_id']))
		{
		    $this->db->where('group_id', $params['group_id']);
		}

		if ( ! empty($params['name']))
		{
		    $this->db
				->like('users.username', trim($params['name']))
				->or_like('users.email', trim($params['name']))
				->or_like('profiles.first_name', trim($params['name']))
				->or_like('profiles.last_name', trim($params['name']));
		}

		return $this->db->count_all_results();
    }

	public function forgotten_password($email = '') {
		if (empty($email)) {
			return FALSE;
		}
		ci()->load->model('users/ion_auth_model');
		$key = ci()->ion_auth_model->hash_password(microtime() . $email);

		$this->db->update('users', array (
			'forgotten_password_code' => $key,
			'forgotten_password_time' => (now() + 86400)
		), array (
			'email' => $email
		));
		return $key;
	}

	// --------------------------------------------------------------------------

    /**
     * Get by many
     */
    public function get_many_by($params = array())
    {
		if ( ! empty($params['active']))
		{
			$params['active'] = $params['active'] === 2 ? 0 : $params['active'] ;
			$this->db->where('active', $params['active']);
		}

		if ( ! empty($params['group_id']))
		{
			$this->db->where('group_id', $params['group_id']);
		}

		if ( ! empty($params['name']))
		{
		    $this->db
			->or_like('users.username', trim($params['name']))
			->or_like('users.email', trim($params['name']));
		}

		return $this->get_all();
    }
    
    /**
     * Get user infomation
     *
     * @access 	public
     * @param 	array
     * @return 	obj
     */
    public function get_user_info($array_where) {
    	$this->db->select('*');
    	$this->db->join('user_profiles', 'user_profiles.user_id = users.id','left');
    	// Search all data with input condition
    	foreach ( $array_where as $key => $value ) {
    		if ($value != '') {
    			$this->db->where($key, $value);
    		}
    		else {
    			$this->db->where($key);
    		}
    	}
    	$this->db->group_by("users.id");
    
    	$row = $this->db->get($this->_table)->row();
    	return $row;
    }
    
    /*
     * Des: Get list location admin
     */
    public function get_all_location_admin_users_by($list_location_id) {
      
        $this->db->select('u.id as user_id, u.username, u.display_name, u.email');
        $this->db->distinct();
        $this->db->from('users u');
        $this->db->join('group_users gu', 'gu.user_id = u.id','inner');
        $this->db->join('location_users lu', 'lu.user_id=u.id','inner');
        $this->db->where("gu.group_id", APConstants::GROUP_LOCATION_ADMIN);
        $this->db->where("u.delete_flag <> 1", null);
        $this->db->where_in("lu.location_id", $list_location_id);
        $data = $this->db->get()->result();
        return $data;
   
    }
    
    /*
     * Des: Get list location admin
     */
    public function get_user_location($user_id, $list_location_id) {
      
        $this->db->select('lu.user_id as user_id, u.username, u.display_name, u.email, l.id as location_id, l.location_name');
        $this->db->from('location_users lu');
        $this->db->join('users u', 'u.id = lu.user_id','inner');
        $this->db->join('location l', 'l.id = lu.location_id','inner');
        
        $this->db->where("u.delete_flag <> 1", null);
        $this->db->where('u.id',$user_id);
        $this->db->where_in("lu.location_id", $list_location_id);
        $data = $this->db->get()->result();
        return $data;
   
    }
    
    /*
     * Des: Get list user get notify about new and delete customer at location of users
     */
    public function get_user_sent_notification_customer() {
      
        $this->db->select('u.id as user_id, u.username, u.display_name, u.email,u.sent_notification_customer_flag,
            u.info_email'
        );
        $this->db->from('users u');
        $this->db->where("u.sent_notification_customer_flag", APConstants::ON_FLAG);
        $data = $this->db->get()->result();
        return $data;
    }
    
    /*
     * Des: Get list user get notify about new and delete customer at location of users
     */
    public function notify_email_new_and_delete_customers() {
      
        $this->db->select('u.id as user_id, u.username, u.display_name, u.email,u.sent_notification_customer_flag,
            u.info_email'
        );
        $this->db->from('users u');
        $this->db->join('location_users lu', 'lu.user_id = u.id','inner');
        $this->db->where("u.sent_notification_customer_flag", APConstants::ON_FLAG);
        $data = $this->db->get()->result();
        return $data;
    }
  
    
    
}