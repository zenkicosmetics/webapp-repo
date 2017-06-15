<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 * @author 
 */
class location_users_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->_table = $this->profile_table = $this->db->dbprefix('location_users');
        $this->primary_key = 'id';

        $this->load->model('addresses/location_m');
    }

    public function get_location_users_available($user_id){

    	$this->db->select('location.*');
    	$this->db->from('location_users');
        $this->db->join('location', 'location.id = location_users.location_id', "inner");
        $this->db->where("user_id", $user_id);
        return $this->db->get()->result();
    }

}