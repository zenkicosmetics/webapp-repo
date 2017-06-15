<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Group model
 *
 *
 */
class location_office_feature_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->_table = $this->profile_table = $this->db->dbprefix('location_office_feature');
        $this->primary_key = 'id';
    }
}