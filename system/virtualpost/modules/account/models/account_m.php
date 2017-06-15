<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 * @author TienNH
 */
class account_m extends MY_Model
{
    function __construct()
    {
        parent::__construct();

        $this->_table = $this->profile_table = $this->db->dbprefix('customers');
        $this->primary_key = 'customer_id';
    }

    /*
     * Get customer by id
     * 
     * @access public
     * @param $cust_id - key of table
     * @return object
     */
    public function get_customer_by_id($cust_id = null)
    {
        $query = $this->db->get_where('customers', array('customer_id' => $cust_id));
        return $query->row();
    }

}