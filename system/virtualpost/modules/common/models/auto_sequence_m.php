<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Group model
 *
 *
 */
class auto_sequence_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->_table = $this->profile_table = $this->db->dbprefix('auto_sequence');
        $this->primary_key = 'id';
    }
    
    /**
     * Get next record id
     */
    public function get_next_id($table_name)
    {
        $this->db->trans_start();
        $insert_id = $this->insert(array('table_name' => $table_name));
        $this->db->trans_complete();
        return $insert_id;
    }
   
}