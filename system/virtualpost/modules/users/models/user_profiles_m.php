<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Group model
 */
class User_profiles_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->set_table_name('user_profiles');
        $this->profile_table = $this->db->dbprefix('user_profiles');
        $this->primary_key = 'user_id';
    }
    
    public function getAllUserId(){
    	
    	$this->db->select('user_id')->distinct();
    	$this->db->from('user_profiles');
    	
    	$query = $this->db->get();
    	$rows = $query->result_array();
    	
    	return $rows;
    	
    }

}