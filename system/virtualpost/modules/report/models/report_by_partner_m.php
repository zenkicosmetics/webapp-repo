<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 * @author DuNT
 */
class report_by_partner_m extends MY_Model
{
    public function __construct() {
        parent::__construct();

        $this->_table = $this->profile_table = $this->db->dbprefix('report_by_partner');
        $this->primary_key = 'id';
    }

}