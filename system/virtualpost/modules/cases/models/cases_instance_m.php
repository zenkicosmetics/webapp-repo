<?php
defined('BASEPATH') or exit('No direct script access allowed');

class cases_instance_m extends MY_Model
{

    function __construct()
    {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('cases_instance');
        $this->primary_key = 'id';
    }

    /**
     *
     * @param unknown $array_where            
     */
    public function get_list_milestone_id($list_case_id)
    {
        $this->db->select('cases_instance.list_milestone_id');
        $this->db->where_in("cases_instance.id", $list_case_id);
        $list_cases_milestone_ids = $this->get_all();
        $result = array();
        if ($list_cases_milestone_ids != null) {
            foreach ($list_cases_milestone_ids as $list_cases_milestone_id) {
                if (empty($list_cases_milestone_id->list_milestone_id)) {
                    continue;
                }
                $arrList = explode(',', $list_cases_milestone_id->list_milestone_id);
                foreach ($arrList as $case_milestome_id) {
                    if (! in_array($case_milestome_id, $result)) {
                        $result[] = $case_milestome_id;
                    }
                }
            }
        }
        return $result;
    }

    /**
     *
     * @param unknown $array_where            
     */
    public function get_base_task_name($list_case_id)
    {
        $result = array();
        $list_milestone_id = $this->get_list_milestone_id($list_case_id);
        if (count($list_milestone_id) == 0) {
            return $result;
        }
        
        $this->db->select('cases_taskname.base_task_name');
        $this->db->from("cases_taskname");
        $this->db->join("cases_milestone", "cases_milestone.id = cases_taskname.milestone_id", "inner");
        $this->db->where_in("cases_milestone.id", $list_milestone_id);
        $list_basetask_name = $this->get_all();
        
        if ($list_basetask_name != null) {
            foreach ($list_basetask_name as $basetask_name) {
                if (! in_array($basetask_name->base_task_name, $result)) {
                    $result[] = $basetask_name->base_task_name;
                }
            }
        }
        return $result;
    }
}