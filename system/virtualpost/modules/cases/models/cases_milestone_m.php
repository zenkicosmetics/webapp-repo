<?php defined('BASEPATH') or exit('No direct script access allowed');

class cases_milestone_m extends MY_Model
{
    public function __construct ()
    {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('cases_milestone');
        $this->primary_key = 'id';
    }

    /**
     * Get all paging data
     * 
     * @param unknown_type $array_where
     *            The array of condition (array ('name' => 'DungNT', 'age' => 30))
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
    public function get_milestone_paging ($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC')
    {
        // Count all record with input condition
        $total_record = $this->count_by_milestone_paging($array_where);
        if ($total_record == 0) {
            return array(
                    "total" => 0,
                    "data" => array()
            );
        }
        
        $this->db->select('cases_milestone.*')->distinct();
        $this->db->select(
                'cases_service_partner.partner_name, cases_service_partner.main_contact_point, cases_service_partner.email, cases_service_partner.phone');
        $this->db->select('cases_product.product_name, cases_product_base_taskname.base_taskname, cases_product_base_taskname.taskname');
        
        $this->db->join('cases_product', 'cases_product.id = cases_milestone.product_id', 'inner');
        $this->db->join('cases_service_partner', 'cases_service_partner.partner_id = cases_milestone.partner_id', 'left');
        $this->db->join('partner_partner', 'partner_partner.partner_id = cases_milestone.partner_id', 'left');
        $this->db->join('cases_taskname', 'cases_milestone.id = cases_taskname.milestone_id', 'inner');
        $this->db->join('cases_product_base_taskname', 'cases_taskname.base_task_name = cases_product_base_taskname.base_taskname', 'inner');
        
        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            }
            else {
                $this->db->where($key);
            }
        }
        $this->db->limit($limit);
        if (! empty($sort_column)) {
            $this->db->order_by($sort_column, $sort_type);
        }
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
    public function count_by_milestone_paging ($array_where)
    {
        $this->db->select('COUNT(DISTINCT(cases_milestone.id)) AS total_record');
        $this->db->from('cases_milestone');
        $this->db->join('cases_product', 'cases_product.id = cases_milestone.product_id', 'inner');
        $this->db->join('cases_service_partner', 'cases_service_partner.partner_id = cases_milestone.partner_id', 'left');
        $this->db->join('partner_partner', 'partner_partner.partner_id = cases_milestone.partner_id', 'left');
        $this->db->join('cases_taskname', 'cases_milestone.id = cases_taskname.milestone_id', 'inner');
        $this->db->join('cases_product_base_taskname', 'cases_taskname.base_task_name = cases_product_base_taskname.base_taskname', 'inner');
        
        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            }
            else {
                $this->db->where($key);
            }
        }
        $result = $this->db->get()->row();
        return $result->total_record;
    }
}