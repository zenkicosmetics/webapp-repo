<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Group model
 */
class state_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->set_table_name('state');
        $this->profile_table = $this->db->dbprefix('state');
        $this->primary_key = 'id';
    }
}