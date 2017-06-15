<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Group model
 *
 *
 */
class cron_job_data_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->_table = $this->profile_table = $this->db->dbprefix('cron_job_data');
        $this->primary_key = 'id';
    }
   
}