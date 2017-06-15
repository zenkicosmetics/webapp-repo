<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 * @author TienNH
 */
class location_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->_table = $this->profile_table = $this->db->dbprefix('location');
        $this->primary_key = 'id';
    }

    /**
     * Gets locations
     *
     * @param unknown $array_where
     * @param number $start
     * @param number $limit
     * @param unknown $sort_column
     * @param string $sort_type
     * @return multitype:number multitype: |multitype:unknown multitype:
     */
    public function get_location_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC')
    {
        // Count all record with input condition
        $total_record = $this->count_by_many($array_where);
        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }

        $this->db->select('location.*, pricing_template.id as pricing_template_id, pricing_template.name as pricing_template_name, partner_partner.partner_code, partner_partner.partner_name');
        $this->db->select('country.country_name');
        $this->db->select('partner_digital_devices.status as panel_status, partner_digital_devices.last_ping_received, partner_digital_devices.created_date as panel_created_date');
        $this->db->select('location_customers.parent_customer_id');
        $this->db->join('pricing_template', 'location.pricing_template_id = pricing_template.id', "left");
        $this->db->join('partner_partner', 'location.partner_id = partner_partner.partner_id', "left");
        $this->db->join('country', 'location.country_id = country.id', "left");
        $this->db->join('partner_digital_devices', 'location.id = partner_digital_devices.location_id', "left");
        $this->db->join('location_customers', 'location.id = location_customers.location_id', "left");
        $this->db->group_by(array('location.id'));
        // Search all data with input condition
        foreach ($array_where as $key => $value) {
            $this->db->where($key, $value);
        }

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
     * Gets locations
     *
     * @param unknown $array_where
     * @param number $start
     * @param number $limit
     * @param unknown $sort_column
     * @param string $sort_type
     * @return multitype:number multitype: |multitype:unknown multitype:
     */
    public function get_location_customer_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC')
    {
        // Count all record with input condition
        $total_record = $this->count_location_customer_paging($array_where);
        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }

        $this->db->select('location.*');
        $this->db->select('partner_digital_devices.status as panel_status, partner_digital_devices.last_ping_received, partner_digital_devices.created_date as panel_created_date');
        $this->db->join('location_customers', 'location.id = location_customers.location_id', "inner");
        $this->db->join('partner_digital_devices', 'location.id = partner_digital_devices.location_id', "left");

        // Search all data with input condition
        foreach ($array_where as $key => $value) {
            $this->db->where($key, $value);
        }

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

    public function count_location_customer_paging($array_where)
    {
        $this->db->select('count(location.id) as count_val');
        $this->db->join('location_customers', 'location.id = location_customers.location_id', "inner");
        foreach ($array_where as $key => $value) {
            $this->db->where($key, $value);
        }
        $row = $this->db->get($this->_table)->row();
        return $row->count_val;
    }


    /**
     * Gets location by partner loggedin.
     */
    public function get_location_by_partner($location_id)
    {
        // Gets locations of instance admin
        if (APContext::isAdminUser()) {
            return parent::get_all();
        }

        if ($location_id) {
            $this->db->where("id", $location_id);
        }

        // Gets locations of partner admin.
        if (APContext::isAdminParner()) {
            $partner_id = APContext::getParnerIDLoggedIn();

            $this->db->where("partner_id", $partner_id);

            return parent::get_all();
        }

        // Gets locations of location admin.
        if (APContext::isAdminLocation()) {
            $user = APContext::getAdminLoggedIn();
            $this->db->where("id", $user->location_available_id);

            return parent::get_all();
        }
    }

    /**
     * Get all location of customer
     * @param unknown_type $customer_id
     */
    public function get_all_location($customer_id)
    {
        $this->db->select("location.*")->distinct();
        $this->db->from("location");
        $this->db->join("postbox", "location.id=postbox.location_available_id", "inner");
        $this->db->where("postbox.customer_id", $customer_id);
        $this->db->where("location.public_flag", '1');
        $this->db->where("(postbox.completed_delete_flag <> '1' OR postbox.completed_delete_flag IS NULL)", null);

        return $this->db->get()->result();
    }

    /**
     * Get all location of customer
     * @param unknown_type $customer_id
     */
    public function get_my_enterprise_location($customer_id)
    {
        $this->db->select("location.*")->distinct();
        $this->db->select('country.country_name');
        $this->db->from("location");
        $this->db->join("location_customers", "location.id=location_customers.location_id", "inner");
        $this->db->where("location_customers.parent_customer_id", $customer_id);
        $this->db->where("location.public_flag", '1');
        $this->db->join('country', 'location.country_id = country.id', "inner");
        return $this->db->get()->result();
    }

    /**
     * Get all location of customer
     * @param unknown_type $array_where
     */
    public function get_public_location($array_where)
    {
        $this->db->select('location.*');
        $this->db->select('country.country_name');
        $this->db->join('country', 'location.country_id = country.id', "inner");
        // Search all data with input condition
        foreach ($array_where as $key => $value) {
            $this->db->where($key, $value);
        }
        return $this->db->get($this->_table)->result();
    }

    /**
     * Get all location of customer
     * @param unknown_type $customer_id
     */
    public function get_all_location_for_verify($customer_id)
    {
        $this->db->select("location.*")->distinct();
        $this->db->from("postbox");
        $this->db->join("location", "location.id=postbox.location_available_id", "left");
        $this->db->where("postbox.customer_id", $customer_id);
        $this->db->where("postbox.completed_delete_flag <> '1'", null);
        $this->db->where("(postbox.name_verification_flag = '0' OR postbox.company_verification_flag = '0')", null);
        return $this->db->get()->result();
    }

    /**
     * counts all locations by pricing template id.
     * @param unknown $template_id
     */
    public function count_location_by($pricing_template_id)
    {
        $total_count = $this->count_by_many(array("pricing_template_id" => $pricing_template_id));

        return $total_count;
    }

    /**
     * Get ìnformation on location
     * @param integer $id Location ID
     */
    public function getLocationInfo($locationId)
    {
        $this->db->select('l.*, c.country_name, c.country_code');
        $this->db->from('location l');
        $this->db->join('country c', 'l.country_id = c.id', 'inner');
        $this->db->where('l.id', $locationId);

        return $this->db->get()->row();
    }

    public function getAllLocationsForDropDownList()
    {
        $this->db->select('id, location_name');
        $this->db->from('location');
        $this->db->order_by('location_name', 'ASC');

        $query = $this->db->get();
        $rows = $query->result();

        return $rows;
    }

    /**
     * Get all location ID of customer
     * @param unknown_type $array_where
     */
    public function get_public_location_ids($array_where)
    {
        $this->db->select('location.id');
        $this->db->select('country.country_name');
        $this->db->join('country', 'location.country_id = country.id', "inner");
        // Search all data with input condition
        foreach ($array_where as $key => $value) {
            $this->db->where($key, $value);
        }
        return $this->db->get($this->_table)->result();
    }
}