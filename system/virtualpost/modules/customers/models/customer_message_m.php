<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 * @author DuNT
 */
class customer_message_m extends MY_Model {
    public function __construct() {
        parent::__construct();

        $this->_table = $this->profile_table = $this->db->dbprefix('customer_message');
        $this->primary_key = 'id';
    }
}
