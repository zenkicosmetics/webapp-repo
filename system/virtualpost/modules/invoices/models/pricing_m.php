<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 * @author TienNH
 */
class pricing_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->_table = $this->profile_table = $this->db->dbprefix('pricing');
        $this->primary_key = 'id';
    }

    /**
     * Gets pricing by tempalte.
     *
     * @param unknown $location_id
     */
    public function get_pricing_by_template($template_id)
    {
        $this->db->select('pricing.*');
        $this->db->where("pricing_template_id", $template_id);

        return parent::get_all();
    }
}