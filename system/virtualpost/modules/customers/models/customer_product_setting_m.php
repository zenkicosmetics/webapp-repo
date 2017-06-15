<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 * @author
 */
class customer_product_setting_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->_table = $this->profile_table = $this->db->dbprefix('customer_product_settings');
        $this->primary_key = 'id';
    }
    
    
}
