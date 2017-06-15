<?php defined('BASEPATH') or exit('No direct script access allowed');

class cases_taskname_instance_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->_table = $this->profile_table = $this->db->dbprefix('cases_taskname_instance');
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
    public function get_tasklist_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC')
    {
        // Count all record with input condition
        
        $total_record = $this->count_by_tasklist_paging($array_where);
        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }
        
        $this->db->select('cases_taskname_instance.id, cases_taskname_instance.case_id, cases_taskname_instance.base_task_name, cases_taskname_instance.status')->distinct();
        $this->db->select('cases_service_partner.partner_name, cases_service_partner.main_contact_point, cases_service_partner.email');
        $this->db->select('cases_milestone.product_id, cases_milestone.milestone_name');
        $this->db->select('customers.customer_code, customers.user_name as c_name, customers.email as c_email');

        $this->db->join('cases_milestone_instance', 'cases_milestone_instance.id = cases_taskname_instance.milestone_instance_id and cases_milestone_instance.case_id = cases_taskname_instance.case_id', 'inner');
        $this->db->join("cases_milestone", "cases_milestone_instance.milestone_id = cases_milestone.id", "inner");
        $this->db->join('cases_product', 'cases_product.id = cases_milestone.product_id', 'inner');
        $this->db->join('cases_service_partner', 'cases_service_partner.partner_id = cases_milestone_instance.partner_id', 'inner');
        $this->db->join('cases', 'cases_taskname_instance.case_id = cases.id', 'left');
        $this->db->join('customers', 'cases.customer_id = customers.customer_id', 'left');
        $this->db->where('cases.deleted_flag', APConstants::OFF_FLAG);

        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
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

    public function get_tasklist_paging_for_admin($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC')
    {
        // Count all record with input condition
        
        $total_record = $this->count_by_tasklist_paging_for_admin($array_where);
        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }
        

        $this->db->select('cases_taskname_instance.case_id,cases_taskname_instance.status, cases_taskname_instance.base_task_name')->distinct();
        $this->db->select('cases_service_partner.partner_name, cases_service_partner.email');
        $this->db->select('cases_milestone.milestone_name');
        $this->db->select('customers.customer_code, customers.email as c_email');

        $this->db->join('cases_milestone_instance', 'cases_milestone_instance.id = cases_taskname_instance.milestone_instance_id and cases_milestone_instance.case_id = cases_taskname_instance.case_id', 'inner');
        $this->db->join("cases_milestone", "cases_milestone_instance.milestone_id = cases_milestone.id", "inner");
        $this->db->join('cases_product', 'cases_product.id = cases_milestone.product_id', 'inner');
        $this->db->join('cases_service_partner', 'cases_service_partner.partner_id = cases_milestone_instance.partner_id', 'inner');
        $this->db->join('cases', 'cases_taskname_instance.case_id = cases.id', 'left');
        $this->db->join('customers', 'cases.customer_id = customers.customer_id', 'left');
        $this->db->where('cases.deleted_flag', APConstants::OFF_FLAG);

        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
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


    /**
     * Count task instance
     *
     * @param unknown_type $array_where
     */
    
    public function count_by_tasklist_paging($array_where)
    {
        $this->db->select('COUNT(DISTINCT(cases_taskname_instance.id)) AS total_record');
        $this->db->from('cases_taskname_instance');

        $this->db->join('cases_milestone_instance', 'cases_milestone_instance.id = cases_taskname_instance.milestone_instance_id and cases_milestone_instance.case_id = cases_taskname_instance.case_id', 'inner');
        $this->db->join("cases_milestone", "cases_milestone_instance.milestone_id = cases_milestone.id", "inner");
        $this->db->join('cases_product', 'cases_product.id = cases_milestone.product_id', 'inner');
        $this->db->join('cases_service_partner', 'cases_service_partner.partner_id = cases_milestone_instance.partner_id', 'inner');
        $this->db->join('cases', 'cases_taskname_instance.case_id = cases.id', 'left');
        $this->db->join('customers', 'cases.customer_id = customers.customer_id', 'left');
        $this->db->where('cases.deleted_flag', APConstants::OFF_FLAG);

        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }
        
        $result = $this->db->get()->row();
        return $result->total_record;
    }
    
    public function count_by_tasklist_paging_for_admin($array_where)
    {
        $this->db->select('COUNT(cases_taskname_instance.case_id) AS total_record');
        $this->db->from('cases_taskname_instance');

        $this->db->join('cases_milestone_instance', 'cases_milestone_instance.id = cases_taskname_instance.milestone_instance_id and cases_milestone_instance.case_id = cases_taskname_instance.case_id', 'inner');
        $this->db->join("cases_milestone", "cases_milestone_instance.milestone_id = cases_milestone.id", "inner");
        $this->db->join('cases_product', 'cases_product.id = cases_milestone.product_id', 'inner');
        $this->db->join('cases_service_partner', 'cases_service_partner.partner_id = cases_milestone_instance.partner_id', 'inner');
        $this->db->join('cases', 'cases_taskname_instance.case_id = cases.id', 'left');
        $this->db->join('customers', 'cases.customer_id = customers.customer_id', 'left');
        $this->db->where('cases.deleted_flag', APConstants::OFF_FLAG);

        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }
        $result = $this->db->get()->row();
        return $result->total_record;
    }
    
    public function update_by_many($array_where, $data)
    {
        // Check exits row update
        $update_row = parent::get_by_many($array_where);

        if (empty($update_row)) {
            return false;
        }

        // open transaction
        $this->db->trans_begin();

        // 1. Update table cases_taskname_instance
        $result = parent::update_by_many($array_where, $data);

        // 2. Update table cases_milestone_instance
        $case_id = $update_row->case_id;
        $milestone_instance_id = $update_row->milestone_instance_id;
        $cases_tasknames = parent::get_many_by_many(array(
            'case_id' => $case_id,
            'milestone_instance_id' => $milestone_instance_id
        ));

        $all_status = 2;
        foreach ($cases_tasknames as $row) {
            if ($row->status != '2') {
                $all_status = 3;
                break;
            }
        }

        if ($all_status != 2) {
            foreach ($cases_tasknames as $row) {
                if ($row->status != '3') {
                    $all_status = 1;
                    break;
                }
            }
        }

        $CI = &get_instance();
        $CI->load->model("cases/cases_milestone_instance_m");
        $updated_by = APContext::getAdminIdLoggedIn();
        if (empty($updated_by)) {
            $updated_by = APContext::getCustomerCodeLoggedIn();
        }
        if (empty($updated_by)) {
            $updated_by = null;
        }
        $CI->cases_milestone_instance_m->update_by_many(array(
            'case_id' => $case_id,
            'id' => $milestone_instance_id
        ), array(
            'status' => $all_status,
            'updated_date' => now(),
            'updated_by' => $updated_by
        ));

        // commit transaction.
        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
        } else {
            $this->db->trans_rollback();
        }

        return $result;
    }
}