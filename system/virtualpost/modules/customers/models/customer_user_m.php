<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 * @author
 */
class customer_user_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->_table = $this->profile_table = $this->db->dbprefix('customers');
        $this->primary_key = 'customer_id';
    }
    
    /**
     * Create a new customer
     */
    public function insert($input = array())
    {
        $raw_customer_id = parent::insert($input);

        // Get customer code and update again
        $customer_code = sprintf('C%1$08d', $raw_customer_id);
        parent::update($raw_customer_id, array(
            'customer_code' => $customer_code
        ));
        return $raw_customer_id;
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
    public function get_user_paging($array_where, $product_type, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC', $group_by = '')
    {
        // Count all record with input condition
        $total_record = $this->count_by_user_paging($array_where, $product_type);
        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }
        $this->db->select('customers.*');
        if ($product_type == 'postbox') {
            $this->db->join('postbox_customer_users', 'postbox_customer_users.customer_id = customers.customer_id', 'left');
        } else if ($product_type == 'phone') {
            $this->db->join('phone_customer_users', 'phone_customer_users.customer_id = customers.customer_id');
        }
        
        // Search all data with input condition
        foreach ($array_where as $key => $value) {
            $this->db->where($key, $value);
        }
        //$this->db->where('customers.status', 0);
        
        $this->db->limit($limit, $start);
        if (!empty($sort_column)) {
            $this->db->order_by($sort_column, $sort_type);
        }
        if (!empty($group_by)) {
            $this->db->group_by($group_by);
        }
        $this->db->group_by("customers.customer_id");
        
        $data = $this->db->get($this->_table, $limit, $start)->result();

        return array(
            "total" => $total_record,
            "data" => $data
        );
    }
    
    /**
     * Count customer
     *
     * @param unknown_type $array_where
     */
    public function count_by_user_paging($array_where, $product_type)
    {
        $this->db->select('COUNT(DISTINCT(customers.customer_id)) AS total_record');
        $this->db->from('customers');

        if ($product_type == 'postbox') {
            $this->db->join('postbox_customer_users', 'postbox_customer_users.customer_id = customers.customer_id', 'left');
        } else if ($product_type == 'phone') {
            $this->db->join('phone_customer_users', 'phone_customer_users.customer_id = customers.customer_id');
        }
        
        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }
        //$this->db->where('customers.status', 0);
        $result = $this->db->get()->row();
        return $result->total_record;
    }
    
    public function get_list_users_by_customer_id($parent_customer_id)
    {
        $this->db->where('parent_customer_id', $parent_customer_id);
        $this->db->where('(deleted_flag <> 1)', null);
        return $this->db->get($this->_table)->result();
    }
    
    public function get_active_customer_by_account($email, $password)
    {
        $this->db->where('email', $email);
        $this->db->where('password', $password);
        $this->db->where('(deleted_flag <> 1)', null);
        return $this->db->get($this->_table)->row();
    }

    public function get_list_postbox_byuser($parent_customer_id, $customer_id)
    {
        $this->db->select('postbox_customer_users.id, postbox.postbox_id, postbox.postbox_code, postbox.postbox_name, postbox.location_available_id, postbox.created_date')->distinct();
        $this->db->select('location.location_name, postbox.type, postbox.deleted, postbox.name_verification_flag, postbox.company_verification_flag');
        $this->db->select('postbox.name, postbox.company');
        
        $this->db->from('postbox_customer_users');
        if ($parent_customer_id != $customer_id) {
            $this->db->join('customers', 'customers.parent_customer_id = postbox_customer_users.parent_customer_id and customers.customer_id = postbox_customer_users.customer_id');
        } else {
            $this->db->join('customers', 'customers.customer_id = postbox_customer_users.parent_customer_id and customers.customer_id = postbox_customer_users.customer_id');
        }
        $this->db->join('postbox', 'postbox.postbox_id = postbox_customer_users.postbox_id');
        $this->db->join('location', 'location.id = postbox.location_available_id');
        
        $this->db->where('customers.customer_id', $customer_id);
        //$this->db->where('(customers.parent_customer_id='.$parent_customer_id.' OR customers.customer_id='.$parent_customer_id.")", null);
        $this->db->where('postbox.deleted', '0');

        return $this->db->get()->result();
    }
    
    public function get_list_postbox_by($parent_customer_id, $list_postbox_id){
        $this->db->select('postbox.postbox_id as id, postbox.postbox_id, postbox.postbox_code, postbox.postbox_name, postbox.location_available_id, postbox.created_date');
        $this->db->select('location.location_name, postbox.type, postbox.deleted, postbox.name_verification_flag, postbox.company_verification_flag');
        $this->db->select('postbox.name, postbox.company');
        
        $this->db->from('postbox');
        $this->db->join('location', 'location.id = postbox.location_available_id');
        $this->db->join('customers', 'customers.customer_id = postbox.customer_id');
        
        $this->db->where('(customers.parent_customer_id='.$parent_customer_id.' OR customers.customer_id='.$parent_customer_id.")", null);
        $this->db->where_in('postbox.postbox_id', $list_postbox_id);
        $this->db->where('postbox.deleted', '0');

        return $this->db->get()->result();
    }
}
