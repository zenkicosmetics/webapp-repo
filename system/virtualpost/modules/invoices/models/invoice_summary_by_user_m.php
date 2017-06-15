<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 * @author DuNT
 */
class invoice_summary_by_user_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->_table = $this->profile_table = $this->db->dbprefix('invoice_summary_by_user');
        $this->primary_key = 'id';
    }
    
    public function get_invoice_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC', $group_by=''){
        // count all record.
        $total_record = $this->count_invoice_paging($array_where);
        if($total_record == 0){
            return array (
                    "total" => 0,
                    "data" => array ()
            );
        }
        
        $this->db->select("invoice_summary_by_user.*, c.user_name, c.email");
        $this->db->join('customers c', 'c.customer_id=invoice_summary_by_user.customer_id', 'left');
        
        // Search all data with input condition
        foreach ( $array_where as $key => $value ) {
            $this->db->where($key, $value);
        }

        if (! empty($sort_column)) {
            $this->db->order_by($sort_column, $sort_type);
        }
        if(!empty($group_by)){
            $this->db->group_by($group_by);
        }
        $data = $this->db->get($this->_table, $limit, $start)->result();
        
        return array (
                "total" => $total_record,
                "data" => $data
        );
    }
    
     public function count_invoice_paging($array_where){
        $this->db->select("count(*) as total");
        $this->db->join('customers c', 'c.customer_id=invoice_summary_by_user.customer_id', 'left');
        
        // Search all data with input condition
        foreach ( $array_where as $key => $value ) {
            $this->db->where($key, $value);
        }
        $data = $this->db->get($this->_table)->row();
        return $data->total;
    }
}