<?php defined('BASEPATH') or exit('No direct script access allowed');

class cases_verification_history_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->_table = $this->db->dbprefix('cases_verification_history');
        $this->primary_key = 'id';
    }
    
    public function getCaseVerificationHistory($case_id, $base_task_name){
        //Get comment history
        $this->db->join('users', 'users.id = cases_verification_history.activity_by', 'left');
        $verification_history = $this->get_many_by_many(array(
            "case_id" => $case_id,
            "base_task_name" => $base_task_name
        ), 'activity_type, activity_content, activity_date, users.username as activity_by', true, array('activity_date' => 'DESC'));
        
        //Get postbox created date
        $this->db->select('postbox.created_date');
        $this->db->from('postbox');
        $this->db->join('cases', 'postbox.postbox_id = cases.postbox_id');
        $this->db->where('cases.id', $case_id);
        $this->db->distinct();
       
        $postbox_created_date = $this->db->get()->result();
        //Add postbox created date to return array
        if (!empty($postbox_created_date[0]->created_date)){
            $postbox_date_obj = new stdClass();
            $postbox_date_obj->activity_type = APConstants::CASE_ACTIVITY_CREATED;
            $postbox_date_obj->activity_content = "";
            $postbox_date_obj->activity_by = "";
            $postbox_date_obj->activity_date = $postbox_created_date[0]->created_date;
            
            $verification_history[] = $postbox_date_obj;
        }
        //Return array
        return $verification_history;
    }
}