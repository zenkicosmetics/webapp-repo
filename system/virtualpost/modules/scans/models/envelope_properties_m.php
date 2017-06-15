<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Group model
 */
class envelope_properties_m extends MY_Model {
    function __construct() {
        parent::__construct();
        $this->profile_table = $this->db->dbprefix('envelope_properties');
        $this->primary_key = 'id';
    }
    
    public function update_envelope_properties($data) {
        //echo "<pre>";print_r($data);exit;
        if (!isset($data['envelope_id'])) {
            return 0;
        }
        $this->db->where(array('envelope_id' => $data['envelope_id']));
        $num = $this->db->count_all_results('envelope_properties');
        //unset($data['envelope_id']);
        if ($num) {
            //echo 1;exit;
            $this->db->where(array('envelope_id' => $data['envelope_id']));
            return $this->db->update('envelope_properties', $data);
        } else {
            //echo 2;exit;
            return $this->db->insert('envelope_properties', $data);
        }
    }
}