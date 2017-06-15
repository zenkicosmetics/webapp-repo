<?php defined('BASEPATH') or exit('No direct script access allowed');

class Cases_contract_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->_table = $this->db->dbprefix('cases_contracts');
        $this->primary_key = 'id';
    }

    public function get_contract_by($case_id, $base_taskname=''){
        $this->db->select("cases_contracts.*");
        $this->db->select("cases_resources.local_file_path, cases_resources.id as file_id");
        $this->db->join("cases_resources", "cases_contracts.case_id=cases_resources.case_id AND cases_resources.base_taskname='TC_contract_MS'", "left");
        
        $this->db->where("cases_contracts.case_id", $case_id);
        if($base_taskname){
            $this->db->where("cases_resources.base_taskname", $base_taskname);
        }

        return $this->db->get($this->_table)->row();
    }
}