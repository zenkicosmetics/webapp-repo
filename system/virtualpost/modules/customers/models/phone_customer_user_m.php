<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 * @author
 */
class phone_customer_user_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->_table = $this->profile_table = $this->db->dbprefix('phone_customer_users');
        $this->primary_key = 'id';
    }
    
    public function get_list_phone_user($array_where) {
        $this->db->select('customers.*, phone_customer_users.phone_user_id');
        $this->db->join('customers', 'phone_customer_users.customer_id = customers.customer_id');
        
        // Search all data with input condition
        foreach ($array_where as $key => $value) {
            $this->db->where($key, $value);
        }
        return $this->db->get($this->_table)->result();
    }
    
    public function get_active_customer_by_account($email, $password)
    {
        $this->db->where('email', $email);
        $this->db->where('password', $password);
        $this->db->where('(status is NULL or status <> 1)', null);
        return $this->db->get($this->_table)->row();
    }
}
