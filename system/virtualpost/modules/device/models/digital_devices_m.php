<?php

defined('BASEPATH') or exit('No direct script access allowed');

class digital_devices_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->_table = $this->profile_table = $this->db->dbprefix('partner_digital_devices');
        $this->primary_key = 'id';
    }

    /**
     * Gets devices template paging.
     *
     * @param unknown $array_where
     * @param number $start
     * @param number $limit
     * @param unknown $sort_column
     * @param string $sort_type
     * @return multitype:number multitype: |multitype:unknown multitype:
     */
    public function get_device_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC')
    {
        // Count all record with input condition
        $total_record = $this->count_by_many($array_where);

        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }

        $this->db->select('partner_digital_devices.*, location.location_name');
        $this->db->join('location', 'location.id = partner_digital_devices.location_id', "left");

        // Search all data with input condition
        foreach ($array_where as $key => $value) {
            $this->db->where($key, $value);
        }

        $this->db->limit($limit);
        if (!empty($sort_column)) {
            $this->db->order_by($sort_column, $sort_type);
        }
        $data = $this->db->get($this->_table, $limit, $start)->result();

        return array(
            "total" => $total_record,
            "data" => $data
        );
    }
}