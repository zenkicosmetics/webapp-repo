<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: thain
 * Date: 4/10/2017
 * Time: 13:51
 */
class customer_history_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->_table = $this->profile_table = $this->db->dbprefix('customer_history');
        $this->primary_key = 'id';
    }

    /**
     * Get account history with pagination
     * @param $array_where
     * @param int $start
     * @param int $limit
     * @param $sort_column
     * @param string $sort_type
     * @param array $list_access_location_id
     */
    public function get_customer_history_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC', $list_access_location_id = array()){
        $this->db->select('customer_history.*, cus.customer_code');
        $this->db->select('cus.customer_id, cus.user_name, cus.email');
        $this->db->join('customers cus', 'customer_history.customer_id = cus.customer_id', 'inner');

        // Search all data with input condition
        if (count($list_access_location_id) > 0) {
            $this->db->where('p.location_available_id', $list_access_location_id[0]);
        }
        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }

        // Clone to get total records
        $clone_query = clone $this->db;
        $total_record = $clone_query->count_all_results($this->_table);

        // Get the records by pagination
        if (!empty($sort_column)) {
            $this->db->order_by($sort_column, $sort_type);
        }
        return ($total_record == 0) ?
            array(
                "total" => $total_record,
                "data" => array()
            ) : array(
                "total" => $total_record,
                "data" => $this->db->get($this->_table, $limit, $start)->result(),
                'test' => $this->db->last_query()
            );
    }

    public function getLastestRecord($where){
        $this->db->where($where);
        $this->db->order_by('created_date', 'DESC');
        $this->db->order_by('id', 'DESC');
        return $this->db->get($this->_table, 1, 0)->row();
    }
}