<?php defined('BASEPATH') or exit('No direct script access allowed');

class pricing_template_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->_table = $this->profile_table = $this->db->dbprefix('pricing_template');
        $this->primary_key = 'id';
    }

    /**
     * Gets price template paging.
     *
     * @param unknown $array_where
     * @param number $start
     * @param number $limit
     * @param unknown $sort_column
     * @param string $sort_type
     * @return multitype:number multitype: |multitype:unknown multitype:
     */
    public function get_price_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC')
    {
        // Count all record with input condition
        $array_where['pricing_template.deleted_flag'] = 0;
        $total_record = $this->count_by_many($array_where);
        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }

        $this->db->select('pricing_template.*, count(location_pricing.location_id) as number_uses');
        $this->db->join('location_pricing', 'location_pricing.pricing_template_id = pricing_template.id', "left");

        // Search all data with input condition
        foreach ($array_where as $key => $value) {
            $this->db->where($key, $value);
        }
        
        $this->db->group_by("pricing_template.id");

        $this->db->limit($limit);
        if (!empty($sort_column)) {
            $this->db->order_by($sort_column, $sort_type);
        }
        $data = $this->db->get($this->_table, $limit, $start)->result();

        return array(
            "total" => $total_record,
            "data" => $data
        );
    }

    /**
     * Count all users using default template.
     * @return unknown
     */
    public function count_number_user_of_default_template()
    {
        $this->db->select('count(location_id) as number_uses');
        $this->db->from("location_pricing");
        $this->db->where("pricing_template_id", APConstants::DEfAULT_PRICING_MODEL_TEMPLATE);
        
        $data = $this->db->get()->result();

        if ($data) {
            return $data[0]->number_uses;
        }

        return 0;
    }

    /**
     * Gets all pricing template by location.
     *
     * @param unknown $location
     */
    public function get_all_template_by($location_id)
    {
        $this->db->where("(pricing_template.id IN (SELECT pricing_template_id FROM location_pricing WHERE location_id='{$location_id}') )", null);
        $this->db->where("pricing_template.deleted_flag", APConstants::OFF_FLAG);
        $result = $this->db->get($this->_table)->result();

        return $result;
    }

    /**
     * Gets all pricing templates exclude by location.
     * @param unknown $location_id
     */
    public function get_all_templates_exclude($location_id = 0)
    {
        $this->db->where("(pricing_template.id NOT IN (SELECT pricing_template_id FROM location_pricing WHERE location_id='{$location_id}') )", null);
        $this->db->where("pricing_template.deleted_flag", APConstants::OFF_FLAG);
        $result = $this->db->get($this->_table)->result();

        return $result;
    }


    public function get_default_template()
    {
        $this->db->where("id", APConstants::DEfAULT_PRICING_MODEL_TEMPLATE);
        $result = $this->db->get($this->_table)->result();

        return $result;
    }

    public function get_all_template_exclude_default()
    {
        $this->db->where("(pricing_template.id NOT IN (0))", null);
        $this->db->where("pricing_template.deleted_flag", APConstants::OFF_FLAG);
        $result = $this->db->get($this->_table)->result();

        return $result;
    }
    
    public function get_all_public_template(){
        $this->db->where("pricing_template.deleted_flag", APConstants::OFF_FLAG);
        $result = $this->db->get($this->_table)->result();

        return $result;
    }
}