<?php defined('BASEPATH') or exit('No direct script access allowed');

class Shipping_credentials_m extends My_Model
{
    /**
     * Responsable for auto load the database
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->set_table_name('shipping_credentials');
        $this->_table = $this->profile_table = $this->db->dbprefix('shipping_credentials');
        $this->primary_key = 'id';
    }
    
}