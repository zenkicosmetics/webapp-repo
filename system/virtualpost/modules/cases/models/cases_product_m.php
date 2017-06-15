<?php
defined('BASEPATH') or exit('No direct script access allowed');

class cases_product_m extends MY_Model
{

    function __construct()
    {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('cases_product');
        $this->primary_key = 'id';
    }

    /**
     *
     * @param unknown $array_where            
     */
    public function get_cases_is_active($array_where)
    {
        $this->db->select('cases_product.*, (case when exists(select id from cases_product_base_taskname where product_id = cases_product.id and activate_flag =' . APConstants::ON_FLAG . ' ) then ' . APConstants::ON_FLAG . ' else ' . APConstants::OFF_FLAG . ' end) as flag');
        
        // $this->db->join ( "cases_product_base_taskname", "cases_product.id = cases_product_base_taskname.product_id", "inner" );
        // $this->db->where ( "cases_product_base_taskname.activate_flag", APConstants::ON_FLAG );
        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }
        
        return $this->get_all();
    }
}