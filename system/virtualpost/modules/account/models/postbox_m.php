<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 * @author TienNH
 */
class postbox_m extends MY_Model {
    function __construct() {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('postbox');
        $this->primary_key = 'postbox_id';
    }
    
    function delete_postbox($postbox_id=null){
        if(!isset($postbox_id))
            return;
        $data = array(
            'deleted' => 1
        );
        $this->db->where('postbox_id',$postbox_id);
        $this->db->update('postbox',$data);        
    }
    
}