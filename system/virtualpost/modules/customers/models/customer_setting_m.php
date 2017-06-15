<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 * @author DuNT
 */
class customer_setting_m extends MY_Model {
    public function __construct() {
        parent::__construct();

        $this->_table = $this->profile_table = $this->db->dbprefix('customers_setting');
        $this->primary_key = 'id';
    }

}
