<?php defined('BASEPATH') or exit('No direct script access allowed');

class Shipping_apis_m extends My_Model
{
    /**
     * Responsable for auto load the database
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->set_table_name('shipping_apis');
        $this->_table = $this->profile_table = $this->db->dbprefix('shipping_apis');
        $this->primary_key = 'id';
    }

    /**
     * Get shipping api by his is
     * @param int $shipping_api_id
     * @return array
     */
    public function get_shipping_api_by_id($id)
    {
        $this->db->select('*');
        $this->db->from('shipping_apis');
        $this->db->where('id', $id);

        $query = $this->db->get();

        return $query->result_array();
    }

    /**
     * Fetch shipping apis data from the database
     * possibility to mix search, filter and order
     * @param string $search_string
     * @param strong $order
     * @param string $order_type
     * @param int $limit_start
     * @param int $limit_end
     * @return array
     */
    public function get_shipping_apis($search_string = null, $order = null, $order_type = 'Asc', $limit_start = null, $limit_end = null)
    {
        $this->db->select('*');
        $this->db->from('shipping_apis');

        if ($search_string) {
            $this->db->like('name', $search_string);
        }
        $this->db->group_by('id');

        if ($order) {
            $this->db->order_by($order, $order_type);
        } else {
            $this->db->order_by('id', $order_type);
        }

        if ($limit_start && $limit_end) {
            $this->db->limit($limit_start, $limit_end);
        }

        if ($limit_start != null) {
            $this->db->limit($limit_start, $limit_end);
        }

        $query = $this->db->get();

        return $query->result_array();
    }

    /**
     * Count the number of rows
     * @param int $search_string
     * @param int $order
     * @return int
     */
    function count_shipping_apis($search_string = null, $order = null)
    {
        $this->db->select('*');
        $this->db->from('shipping_apis');
        if ($search_string) {
            $this->db->like('name', $search_string);
        }
        if ($order) {
            $this->db->order_by($order, 'Asc');
        } else {
            $this->db->order_by('id', 'Asc');
        }
        $query = $this->db->get();
        return $query->num_rows();
    }

    /**
     * Store the new item into the database
     * @param array $data - associative array with data to store
     * @return boolean
     */
    function create_shipping_api($data)
    {
        $id = $this->db->insert('shipping_apis', $data);

        return $id;
    }

    /**
     * Update shipping apis
     * @param array $data - associative array with data to store
     * @return boolean
     */
    function update_shipping_api($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('shipping_apis', $data);
        $report = array();
        $report['error'] = $this->db->_error_number();
        $report['message'] = $this->db->_error_message();
        if ($report !== 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Delete shipping api
     * @param int $id - shipping api id
     * @return boolean
     */
    function delete_shipping_api($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('shipping_apis');
    }
    
    public function get_shipping_api_info($id)
    {
        $this->db->select('shipping_apis.*, shipping_carriers.code');
        $this->db->from('shipping_apis');
        $this->db->join('shipping_carriers', 'shipping_carriers.id = shipping_apis.carrier_id');
        $this->db->where('shipping_apis.id', $id);

        $query = $this->db->get();

        return $query->row();
    }
    
    public function get_shipping_apis_name($ids) {
        $string_ids = APUtils::convertIdsInputToString($ids);
        $this->db->select('name');
        $this->db->where('id IN (' . $string_ids . ')');
        //Exec query
        $query = $this->db->get($this->_table)->result_array();
        return $query;
    }
}