<?php defined('BASEPATH') or exit('No direct script access allowed');

class shipping_services_m extends MY_Model
{
    /**
     * Responsable for auto load the database
     *
     * @return void
     */
    public function __construct ()
    {
        parent::__construct();

        $this->_table = $this->profile_table = $this->db->dbprefix('shipping_services');
        $this->primary_key = 'id';
    }

    /**
     * Fetch shipping services data from the database
     * possibility to mix search, filter and order
     *
     * @param int $shipping_api_id
     * @param string $search_string
     * @param strong $order
     * @param string $order_type
     * @param int $limit_start
     * @param int $limit_end
     * @return array
     */
    public function get_shipping_services_paging ($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC')
    {
        // Count all record with input condition
        $total_record = $this->count_shipping_services_paging($array_where);
        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }

        $this->db->select('shipping_services.id');
        $this->db->select('shipping_services.name');
        $this->db->select('shipping_services.short_desc');
        $this->db->select('shipping_services.long_desc');
        $this->db->select('shipping_services.logo');
        $this->db->select('shipping_services.factor_a');
        $this->db->select('shipping_services.factor_b');
        $this->db->select('shipping_apis.name as shipping_api_name');

        $this->db->join('shipping_apis', 'shipping_services.api_acc_id = shipping_apis.id', 'left');

        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            }
            else {
                $this->db->where($key);
            }
        }
        $this->db->limit($limit);
        if (! empty($sort_column)) {
            if ($sort_column == 'user_name') {
                $this->db->order_by('p.name', $sort_type);
                $this->db->order_by('p.company', $sort_type);
            }
            else {
                $this->db->order_by($sort_column, $sort_type);
            }
        }
        $data = $this->db->get($this->_table, $limit, $start)->result();

        return array(
            "total" => $total_record,
            "data" => $data
        );
    }

    /**
     * Count the number of rows
     *
     * @param int $shipping_api_id
     * @param int $search_string
     * @param int $order
     * @return int
     */
    function count_shipping_services_paging ($array_where)
    {
        $this->db->select('COUNT(DISTINCT(shipping_services.id)) AS total_record');
        $this->db->from('shipping_services');
        $this->db->join('shipping_apis', 'shipping_services.api_acc_id = shipping_apis.id', 'left');

        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            }
            else {
                $this->db->where($key);
            }
        }
        $result = $this->db->get()->row();
        return $result->total_record;
    }

    /**
     * Fetch shipping services data from the database
     * possibility to mix search, filter and order
     * @param int $shipping_api_id
     * @param string $search_string
     * @param strong $order
     * @param string $order_type
     * @param int $limit_start
     * @param int $limit_end
     * @return array
     */
    public function get_shipping_services($shipping_api_id, $search_string=null, $order=null, $order_type='Asc', $limit_start=null, $limit_end=null)
    {

        $this->db->select('shipping_services.id');
        $this->db->select('shipping_services.name');
        $this->db->select('shipping_services.short_desc');
        $this->db->select('shipping_services.long_desc');
        $this->db->select('shipping_services.logo');
        $this->db->select('shipping_services.factor_a');
        $this->db->select('shipping_services.factor_b');
        $this->db->select('shipping_apis.name as shipping_api_name');
        $this->db->from('shipping_services');
        if($shipping_api_id != null && $shipping_api_id != 0){
            $this->db->where('api_acc_id', $shipping_api_id);
        }
        if($search_string){
            $this->db->like('description', $search_string);
        }

        $this->db->join('shipping_apis', 'shipping_services.api_acc_id = shipping_apis.id', 'left');
        $this->db->group_by('shipping_services.id');

        if($order){
            $this->db->order_by($order, $order_type);
        }else{
            $this->db->order_by('id', $order_type);
        }

        if(!is_null($limit_start) && !is_null($limit_end))
            $this->db->limit($limit_start, $limit_end);

        $query = $this->db->get();

        return $query->result_array();
    }

    /**
     * Get shipping services by field name
     * @param string $field_name
     * @param array $field_values
     * @return array of arrays OR array of objects
     */
    public function get_shipping_services_by($field_name, $field_values, $shipping_service_type, $isArrayObjects = true)
    {
        $this->db->select('shipping_services.*, shipping_carriers.name as carrier_name');
        $this->db->from('shipping_services');
        $this->db->join("shipping_carriers", "shipping_services.carrier_id = shipping_carriers.id", "left");
        $this->db->where_in($field_name, $field_values);
        if (!empty($shipping_service_type)) {
            $this->db->where("(shipping_services.service_type='0' OR shipping_services.service_type='".$shipping_service_type."')", null);
        }
        $this->db->order_by('shipping_services.name', 'asc');
        $query = $this->db->get();

        if ($isArrayObjects) {
            return $query->result();
        } else {
            return $query->result_array();
        }
    }

    /**
     * Get shipping services by field name
     * @param string $field_name
     * @param array $field_value
     * @return array
     */
    public function get_shipping_service_by($field_name, $field_value)
    {
        $this->db->select('*');
        $this->db->from('shipping_services');
        $this->db->where_in($field_name, $field_value);
        $query = $this->db->get();

        return ($query->num_rows() > 0) ? $query->row() : false;
    }

    public function get_shipping_services_exclude_by(array $shipping_service_ids)
    {
        $excludes = '(' . implode(',', $shipping_service_ids) . ')';
        $this->db->select('id, name');
        $this->db->from('shipping_services');
        $this->db->where('(id NOT IN '. $excludes . ')', null);
        $this->db->order_by('shipping_services.name', 'asc');
        $query = $this->db->get();

        return $query->result();
    }

    public function getShippingServiceInfo($shippingServiceID)
    {
        $sql = <<<SQL
SELECT
        svc.id,
        svc.name as service_name,
        -- svc.api_svc_code1,
        -- svc.api_svc_code2,
        svc.api_acc_id,
        svc.logo,
        svc.factor_a,
        svc.factor_b,
        svc.weight_limit,
        svc.dimension_limit,
        svc.service_type,
        svc.carrier_id,
        svc.packaging_type,
        svc.show_calculation_fails,
        svc.tracking_information_flag,
        -- api.site_id,
        -- api.account_no,
        -- api.meter_no,
        -- api.username,
        -- api.auth_key,
        -- api.password,
        -- api.price_includes_vat,
        -- api.percental_partner_upcharge,
        car.code,
        car.name
FROM
        shipping_services AS svc
-- INNER JOIN
    --    shipping_apis AS api
-- ON
   --     svc.api_acc_id = api.id
INNER JOIN
        shipping_carriers AS car
ON
        svc.carrier_id = car.id
WHERE
        svc.id = ?
SQL;
        $query = $this->db->query($sql, array($shippingServiceID));
        $row = $query->row();

        return $row;
    }
}