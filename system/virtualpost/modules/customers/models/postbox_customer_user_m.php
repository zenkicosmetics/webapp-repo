<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 * @author
 */
class postbox_customer_user_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->_table = $this->profile_table = $this->db->dbprefix('postbox_customer_users');
        $this->primary_key = 'id';
    }
}
