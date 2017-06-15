<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 * @author TienNH
 */
class postbox_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->_table = $this->profile_table = $this->db->dbprefix('postbox');
        $this->primary_key = 'postbox_id';
    }

    /**
     * Insert a new record into the database, calling the before and after create callbacks.
     *
     * @author Jamie Rumbelow
     * @author Dan Horrigan
     * @param array $data
     *            Information
     * @param boolean $skip_validation
     *            Whether we should skip the validation of the data.
     * @return integer true insert ID
     */
    public function insert($data)
    {
        $postbox_id = parent::insert($data);
        $customer_id = $data ['customer_id'];

        // Get location
        if (!empty($data ['location_available_id'])) {
            $customer_code = sprintf('C%1$08d', $customer_id);
            $location_rec = $this->db->where('id', $data ['location_available_id'])->get('location')->row();
            $short_location_name = strtoupper(substr($location_rec->location_name, 0, 3));
            $box_count = $this->count_by_customer_cityname($customer_code, $short_location_name) + 1;

            // Get customer code and update again
            $postbox_code = sprintf('C%1$08d', $customer_id);
            $postbox_code = $postbox_code . '_' . $short_location_name . sprintf('%1$02d', $box_count);
            $result = parent::update_by_many(array(
                "postbox_id" => $postbox_id,
                "(postbox_code is null or postbox_code = '')" => null
            ), array(
                'postbox_code' => $postbox_code
            ));
            if($result){
                $data['postbox_code'] = $postbox_code;
            }
        }

        $this->update_postbox_history($data, $actionType = "Insert");

        return $postbox_id;
    }

    /**
     * Insert a new record into the database, calling the before and after create callbacks.
     *
     * @author Jamie Rumbelow
     * @author Dan Horrigan
     * @param array $data
     *            Information
     * @param boolean $skip_validation
     *            Whether we should skip the validation of the data.
     * @return integer true insert ID
     */
    public function update($primary_value, $data)
    {
        $result = parent::update($primary_value, $data);

        // Get location
        if (!empty($data ['location_available_id']) && !empty($data ['customer_id'])) {
            $customer_id = $data ['customer_id'];
            $customer_code = sprintf('C%1$08d', $customer_id);
            $location_rec = $this->db->where('id', $data ['location_available_id'])->get('location')->row();
            $short_location_name = strtoupper(substr($location_rec->location_name, 0, 3));
            $box_count = $this->count_by_customer_cityname($customer_code, $short_location_name) + 1;

            // Get customer code and update again
            $postbox_code = sprintf('C%1$08d', $customer_id);
            $postbox_code = $postbox_code . '_' . $short_location_name . sprintf('%1$02d', $box_count);
            parent::update_by_many(array(
                "postbox_id" => $primary_value,
                //"(postbox_code is null or postbox_code = '')" => null
            ), array(
                'postbox_code' => $postbox_code
            ));
        }

        $ojbPostbox = $this->get($primary_value);;
        $postbox = APUtils::convertObjectToArray($ojbPostbox);
        $this->update_postbox_history($postbox, $actionType = "Update");

        return $result;
    }

    /**
     * Update a record, specified by $key and $val. The function accepts ghost parameters, fetched via func_get_args(). Those are: 1. string `$key`
     * The key to update with. 2. string `$value` The value to match. 3. array `$data` The data to update with. The first two are used in the query in
     * the where statement something like: <code>UPDATE {table} SET {$key}={$data} WHERE {$key}={$value}</code>
     *
     * @author Jamie Rumbelow
     * @return boolean
     */
    public function update_by_many($array_where, $data)
    {
        $return = parent::update_by_many($array_where, $data);

        // Get location
        if (!empty($data ['location_available_id']) && !empty($array_where ['customer_id'])) {
            $customer_id = $array_where ['customer_id'];
            $customer_code = sprintf('C%1$08d', $customer_id);
            $location_rec = $this->db->where('id', $data ['location_available_id'])->get('location')->row();
            $short_location_name = strtoupper(substr($location_rec->location_name, 0, 3));
            $box_count = $this->count_by_customer_cityname($customer_code, $short_location_name) + 1;

            // Get customer code and update again
            $postbox_code = sprintf('C%1$08d', $customer_id);
            $postbox_code = $postbox_code . '_' . $short_location_name . sprintf('%1$02d', $box_count);
            $array_where_tmp = $array_where;
            //$array_where_tmp["(postbox_code is null or postbox_code = '')"] = null;
            $return = parent::update_by_many($array_where_tmp, array(
                'postbox_code' => $postbox_code
            ));
        }
        $list_postbox = $this->get_many_by_many($array_where);
        if (count($list_postbox)) {
            foreach ($list_postbox as $objPostbox) {
                $postbox = APUtils::convertObjectToArray($objPostbox);
                $this->update_postbox_history($postbox, $actionType = "Update");
            }
        }

        return $return;
    }

    /**
     * Count all postbox start with $customer_code_$city_code
     *
     * @param unknown_type $customer_code
     * @param unknown_type $city_code
     */
    public function count_by_customer_cityname($customer_code, $city_code)
    {
        $sql = "SELECT COUNT(*) box_count FROM postbox WHERE postbox_code LIKE '" . $customer_code . '_' . $city_code . "%' AND ((name IS NOT NULL AND name != '') OR (company IS NOT NULL AND company != ''))";
        $query = $this->db->query($sql, array());
        $result = $query->result();
        if (!empty($result) && count($result) > 0) {
            return $result [0]->box_count;
        }
        return 0;
    }

    /**
     * Get all website and detail information of Customer.
     *
     * @param $condition : The
     *            search condition value.
     */
    public function get_all_postbox($condition)
    {
        $this->db->select('postbox.*, customers.customer_id, customers.activated_flag, customers.email')->distinct();
        $this->db->from('postbox');

        $this->db->like("postbox.postbox_name", $condition);
        $this->db->or_like('postbox.name', $condition);
        $this->db->or_like('postbox.company', $condition);
        $this->db->or_like('location.location_name', $condition);
        $this->db->or_like('customers.email', $condition);
        $this->db->where("postbox.deleted <> '1'");
        //$this->db->where(" ");
        $this->db->join('location', 'location.id = postbox.location_available_id', 'left');
        $this->db->join('customers', 'customers.customer_id = postbox.customer_id AND (customers.status is NULL OR customers.status <> 1)', 'inner');
        $this->db->order_by('postbox.postbox_name');

        $result = $this->db->get()->result();
        log_message('debug', "get_all_postbox: executed");

        return $result;
    }

    /**
     * Get all website and detail information of Customer.
     *
     * @param $condition : The
     *            search condition value.
     */
    public function get_main_postbox_address($customer_id)
    {
        $this->db->select('location.*')->distinct();
        $this->db->from('postbox');
        $this->db->join('location', 'location.id = postbox.location_available_id', 'left');

        $this->db->where("postbox.deleted <> '1'");
        $this->db->where("postbox.customer_id", $customer_id);
        $this->db->where("postbox.is_main_postbox", APConstants::ON_FLAG);

        return $this->db->get()->row();
    }

    /**
     * Get all website and detail information of Customer.
     *
     * @param $condition : The
     *            search condition value.
     */
    public function get_max_postboxtype($customer_id)
    {
        $this->db->select('postbox_id, MAX(postbox.type) as MaxPostboxType')->distinct();
        $this->db->from('postbox');
        $this->db->where("postbox.deleted <> '1'");
        // 20141029 Start hotfix: Main postbox is null
        //$this->db->where("apply_date IS NOT NULL");
        // 20141029 END hotfix: Main postbox is null
        $this->db->where("postbox.customer_id", $customer_id);

        return $this->db->get()->row();
    }

    /**
     * Get all postbox number of customer.
     *
     * @param unknown_type $customer_id
     * @return multitype:
     */
    public function get_postbox_count_by_customer($customer_id = null, $list_customer_id = null)
    {
        if (!isset($customer_id)){
            return array();
        }
        
        $this->db->select("type, COUNT(*) box_count");
        $this->db->from("postbox");
        $this->db->where("deleted <>", APConstants::ON_FLAG);
        $this->db->where("(postbox_name IS NOT NULL AND postbox_name !='')", null);
        $this->db->where("((name IS NOT NULL AND name != '') OR (company IS NOT NULL AND company != ''))", null);
        if(!empty($list_customer_id)){
            $list_customer_id[] = $customer_id;
            $this->db->where("customer_id IN ('".implode("','", $list_customer_id)."')");
        }else{
            $this->db->where("customer_id", $customer_id);
        }
        $this->db->group_by('postbox.type');

        return $this->db->get()->result();
    }

    /**
     * Get all postbox number of customer.
     *
     * @param unknown_type $customer_id
     * @return multitype:
     */
    public function count_by_customer($customer_id = null)
    {
        if (!isset($customer_id)) {
            return array();
        }
        $sql = "SELECT COUNT(*) box_count FROM postbox WHERE customer_id = ? AND deleted <> '1' AND apply_date IS NULL";
        $query = $this->db->query($sql, array(
            $customer_id
        ));

        return $query->row()->box_count;
    }
    
	/**
     * Get all postbox number of customer.
     *
     * @param unknown_type $customer_id
     * @return multitype:
     */
    public function count_by_customer_postbox_type($customer_id, $postbox_type)
    {
        if (!isset($customer_id)) {
            return array();
        }
        $sql = "SELECT COUNT(*) box_count FROM postbox WHERE customer_id = ? AND deleted <> '1' AND type=?";
        $query = $this->db->query($sql, array(
            $customer_id,
            $postbox_type
        ));

        return $query->row()->box_count;
    }

    /**
     * Get all postbox number of customer.
     *
     * @param unknown_type $customer_id
     * @return multitype:
     */
    public function count_by_customer_not_include_new($customer_id = null)
    {
        if (!isset($customer_id)) {
            return array();
        }
        $sql = "SELECT type, COUNT(*) box_count FROM postbox WHERE customer_id = ? AND deleted <> '1' AND apply_date IS NULL GROUP BY type";
        $query = $this->db->query($sql, array(
            $customer_id
        ));

        return $query->result();
    }

    /**
     * Get all postbox number of customer.
     *
     * @param unknown_type $customer_id
     * @return multitype:
     */
    public function get_all_by_customer_not_include_new($customer_id = null)
    {
        if (!isset($customer_id)) {
            return array();
        }
        $sql = "SELECT P.*, PS.invoicing_cycle FROM postbox P LEFT JOIN postbox_settings PS ON P.postbox_id = PS.postbox_id  WHERE P.customer_id = ? AND P.deleted <> '1' AND P.apply_date IS NULL AND (P.name <> '' OR P.company <> '')";
        $query = $this->db->query($sql, array(
            $customer_id
        ));

        return $query->result();
    }

    /**
     * Gets customer with locations
     *
     * @param string $customer_id
     * @return multitype:
     */
    public function get_all_by_customer_not_include_new_with_pricing_template($customer_id = null)
    {
        if (!isset($customer_id)) {
            return array();
        }
        $sql = "SELECT P.*, PS.invoicing_cycle , ifnull(location.pricing_template_id, 1) as pricing_template_id
                FROM postbox P
                LEFT JOIN postbox_settings PS ON P.postbox_id = PS.postbox_id
                LEFT JOIN location ON location.id=P.location_available_id
                WHERE P.customer_id = ? 
                    AND ( P.type IN (2,3) )
                    AND completed_delete_flag <> 1
                    AND P.deleted <> '1' AND P.apply_date IS NULL AND (P.name <> '' OR P.company <> '')";
        $query = $this->db->query($sql, array(
            $customer_id
        ));

        return $query->result();
    }

    /**
     * Get all postbox number of customer.
     *
     * @param unknown_type $customer_id
     * @return multitype:
     */
    public function count_by_customer_include_new($customer_id = null)
    {
        if (!isset($customer_id)) {
            return array();
        }
        $sql = "SELECT P.*, PS.invoicing_cycle FROM postbox P INNER JOIN postbox_settings PS ON P.postbox_id = PS.postbox_id WHERE P.customer_id = ? AND P.deleted <> '1' AND P.apply_date IS NOT NULL ORDER BY type";
        $query = $this->db->query($sql, array(
            $customer_id
        ));

        return $query->result();
    }

    /**
     * Gets all postboxes with template pricing.
     *
     * @param string $customer_id
     * @return multitype:
     */
    public function count_by_customer_include_new_with_pricing_template($customer_id = null)
    {
        if (!isset($customer_id)) {
            return array();
        }
        $sql = "SELECT 
                P.postbox_id
                , P.postbox_code
                , P.customer_id
                , P.postbox_name
                , P.type
                , P.name
                , P.company
                , P.deleted
                , P.is_main_postbox
                , P.plan_deleted_date
                , P.updated_date
                , P.plan_date_change_postbox_type
                , P.new_postbox_type
                , IF(P.apply_date IS NULL, DATE_FORMAT(FROM_UNIXTIME(P.created_date) ,'%Y%m%d'), P.apply_date) as apply_date
                , P.first_location_flag
                , P.created_date
                , P.location_available_id
                , P.plan_deleted_date
                , PS.invoicing_cycle
                , ifnull(location.pricing_template_id, 1) as pricing_template_id
                FROM postbox P 
                LEFT JOIN postbox_settings PS ON P.postbox_id = PS.postbox_id 
                LEFT JOIN location ON location.id=P.location_available_id
                WHERE P.customer_id = ? 
                AND ( P.type IN (2,3) )
                AND ((P.deleted <> '1' AND P.apply_date IS NOT NULL AND P.apply_date >= DATE_FORMAT(NOW() ,'%Y%m01') ) 
                        OR (P.deleted = '1' AND (FROM_UNIXTIME(P.updated_date) >= DATE_FORMAT(NOW() ,'%Y-%m-01')))
                    ) 
                AND completed_delete_flag <> 1
                ORDER BY type";
        $query = $this->db->query($sql, array(
            $customer_id
        ));

        return $query->result();
    }

    /**
     * Get all website and detail information of Customer.
     *
     * @param $condition : The
     *            search condition value.
     */
    public function get_all_postbox_by_location($condition, $location_id)
    {
        $new_condition = APUtils::sanitizing($condition);
        $this->db->select('postbox.*, customers.customer_id, customers.status, customers.customer_code, customers.activated_flag, customers.email')->distinct();
        $this->db->from('postbox');

        // #998: change six month to 1 year ago.
        $six_month_ago = time() - 365 * 86400;
        $this->db->where("(postbox.postbox_name='{$new_condition}' "
        . " OR customers.customer_id like '%{$new_condition}%' "
        . " OR postbox.name like '%{$new_condition}%' "
        . " OR postbox.company like '%{$new_condition}%'"
        . " OR customers.email like '%{$new_condition}%'"
        . " OR customers_address.invoicing_address_name like '%{$new_condition}%'"
        . " OR customers_address.invoicing_company like '%{$new_condition}%'"
        . " OR customers_address.shipment_address_name like '%{$new_condition}%'"
        . " OR customers_address.shipment_company like '%{$new_condition}%')", null);
        
        $this->db->where("(postbox.deleted <> '1' OR (postbox.deleted ='1' AND updated_date > {$six_month_ago}) )", null);
        if ($location_id) {
            $this->db->where("postbox.location_available_id", $location_id);
        }

        //$this->db->or_where("");

        $this->db->join('location', 'location.id = postbox.location_available_id', 'left');
        //$this->db->join('customers', 'customers.customer_id = postbox.customer_id AND (customers.status is NULL OR customers.status <> 1)', 'inner');
        $this->db->join('customers', 'customers.customer_id = postbox.customer_id', 'inner');
        $this->db->join('customers_address', 'customers_address.customer_id = postbox.customer_id', 'LEFT');
        $this->db->order_by('postbox.postbox_name');

        $result = $this->db->get()->result();
        log_message('debug', "get_all_postbox: executed");
        return $result;
    }

    public function getPostboxAndLocation($postboxID){

        $this->db->select("postbox.*, location.location_name");
        $this->db->from("postbox");
        $this->db->join("location", "location.id=postbox.location_available_id", "left");
        $this->db->where("postbox.postbox_id", $postboxID);
        $postbox = $this->db->get()->result();
        return isset($postbox[0]) ? $postbox[0] : 0;

    }

    public function get_postboxes_by($customer_id)
    {
        $this->db->select("postbox.*, location.location_name");
        $this->db->from("postbox");
        $this->db->join("location", "location.id=postbox.location_available_id", "left");
        $this->db->where("postbox.customer_id", $customer_id);
        $this->db->where("postbox.completed_delete_flag <> '1'", null);
        $this->db->where("postbox.deleted <> '1'", null);

        $result = $this->db->get()->result();

        $data = array();
        foreach ($result as $r) {
            $tmp = $r;
            if ($r->type == APConstants::BUSINESS_TYPE) {
                $tmp->label = $r->postbox_name . " - " . $r->location_name . " - BUSINESS";
                $tmp->postbox_type = "BUSINESS";
            } else if ($r->type == APConstants::PRIVATE_TYPE) {
                $tmp->label = $r->postbox_name . " - " . $r->location_name . " - PRIVATE";
                $tmp->postbox_type = "PRIVATE";
            } else if ($r->type == APConstants::FREE_TYPE){
                $tmp->label = $r->postbox_name . " - " . $r->location_name . " - AS YOU GO";
                $tmp->postbox_type = "AS YOU GO";
            } else if ($r->type == APConstants::ENTERPRISE_CUSTOMER){
                $tmp->label = $r->postbox_name . " - " . $r->location_name . " - ENTERPRISE";
                $tmp->postbox_type = "ENTERPRISE";
            }

            array_push($data, $tmp);
        }

        return $data;
    }
    
    public function get_postboxes_by_list_customer($list_customer_id)
    {
        $this->db->select("postbox.*, location.location_name")->distinct();
        $this->db->from("postbox");
        $this->db->join("location", "location.id=postbox.location_available_id", "left");
        $this->db->where_in("postbox.customer_id", $list_customer_id);
        $this->db->where("postbox.completed_delete_flag <> '1'", null);
        $this->db->where("postbox.deleted <> '1'", null);

        $result = $this->db->get()->result();

        $data = array();
        foreach ($result as $r) {
            $tmp = $r;
            if ($r->type == APConstants::BUSINESS_TYPE) {
                $tmp->label = $r->postbox_name . " - " . $r->location_name . " - BUSINESS";
                $tmp->postbox_type = "BUSINESS";
            } else if ($r->type == APConstants::PRIVATE_TYPE) {
                $tmp->label = $r->postbox_name . " - " . $r->location_name . " - PRIVATE";
                $tmp->postbox_type = "PRIVATE";
            } else if ($r->type == APConstants::FREE_TYPE){
                $tmp->label = $r->postbox_name . " - " . $r->location_name . " - AS YOU GO";
                $tmp->postbox_type = "AS YOU GO";
            } else if ($r->type == APConstants::ENTERPRISE_CUSTOMER){
                $tmp->label = $r->postbox_name . " - " . $r->location_name . " - ENTERPRISE";
                $tmp->postbox_type = "ENTERPRISE";
            }

            array_push($data, $tmp);
        }

        return $data;
    }
    

    /**
     * Gets list of postbox for delete postbox action
     * @param unknown $customer_id
     * @return multitype:
     */
    public function get_list_postboxes($customer_id)
    {
        $this->db->select("postbox.*, location.location_name");
        $this->db->from("postbox");
        $this->db->join("location", "location.id=postbox.location_available_id", "left");
        $this->db->where("postbox.customer_id", $customer_id);
        $this->db->where("postbox.deleted <> '1'", null);
        $this->db->where("(postbox.postbox_name IS NOT NULL AND postbox.postbox_name !='') ", null);
        $this->db->where("((postbox.name IS NOT NULL AND postbox.name != '') OR (postbox.company IS NOT NULL AND postbox.company != '')) ", null);
        $this->db->order_by("postbox.is_main_postbox", 'desc');
        $result = $this->db->get()->result();

        $data = array();
        foreach ($result as $r) {
            $tmp = $r;
            if ($r->type == APConstants::BUSINESS_TYPE) {
                $tmp->label = $r->postbox_name . " - " . $r->location_name . " - BUSINESS";
                $tmp->postbox_type = "BUSINESS";
            } else if ($r->type == APConstants::PRIVATE_TYPE) {
                $tmp->label = $r->postbox_name . " - " . $r->location_name . " - PRIVATE";
                $tmp->postbox_type = "PRIVATE";
            } else if ($r->type == APConstants::FREE_TYPE){
                $tmp->label = $r->postbox_name . " - " . $r->location_name . " - AS YOU GO";
                $tmp->postbox_type = "AS YOU GO";
            } else if ($r->type == APConstants::ENTERPRISE_CUSTOMER){
                $tmp->label = $r->postbox_name . " - " . $r->location_name . " - ENTERPRISE";
                $tmp->postbox_type = "ENTERPRISE";
            }

            array_push($data, $tmp);
        }

        return $data;
    }
    
    /**
     * Get list postbox of enterprise customer.
     * 
     * @param type $parent_customer_id
     */
    public function getListAvailPostboxOfEnterpriseCustomer($parent_customer_id) {
        $this->db->select("postbox.*, location.location_name");
        $this->db->from("postbox");
        $this->db->join("location", "location.id = postbox.location_available_id", "left");
        $this->db->join("customers", "postbox.customer_id = customers.customer_id", "inner");
        $this->db->where("(customers.parent_customer_id = ".$parent_customer_id ." OR customers.customer_id=".$parent_customer_id.")", null);
        $this->db->where("postbox.deleted <> '1'", null);
        $this->db->where("(postbox.postbox_name IS NOT NULL AND postbox.postbox_name !='') ", null);
        $this->db->where("((postbox.name IS NOT NULL AND postbox.name != '') OR (postbox.company IS NOT NULL AND postbox.company != '')) ", null);

        $result = $this->db->get()->result();

        $data = array();
        foreach ($result as $r) {
            $tmp = $r;
            if ($r->type == APConstants::BUSINESS_TYPE) {
                $tmp->label = $r->postbox_name . " - " . $r->location_name . " - BUSINESS";
                $tmp->postbox_type = "BUSINESS";
            } else if ($r->type == APConstants::PRIVATE_TYPE) {
                $tmp->label = $r->postbox_name . " - " . $r->location_name . " - PRIVATE";
                $tmp->postbox_type = "PRIVATE";
            } else if ($r->type == APConstants::FREE_TYPE){
                $tmp->label = $r->postbox_name . " - " . $r->location_name . " - AS YOU GO";
                $tmp->postbox_type = "AS YOU GO";
            } else if ($r->type == APConstants::ENTERPRISE_CUSTOMER){
                $tmp->label = $r->postbox_name . " - " . $r->location_name . " - ENTERPRISE";
                $tmp->postbox_type = "ENTERPRISE";
            }

            array_push($data, $tmp);
        }

        return $data;
    }

    public function getFirstLocationBy($customer_id)
    {
        $this->db->select("postbox.*, location.location_name, location.id, location.business_postbox_text");
        $this->db->from("postbox");
        $this->db->join("location", "location.id=postbox.location_available_id", "left");
        $this->db->join("customers", "customers.customer_id=postbox.customer_id", "left");
        $this->db->where("postbox.customer_id", $customer_id);
        $this->db->where("postbox.first_location_flag", 1);
        $this->db->where("((postbox.completed_delete_flag = 0 AND customers.status <> 1) OR (customers.status = 1))", null);

        $result = $this->db->get()->result();
        if ($result) {
            return $result[0];
        }
        return '';
    }

    public function getLocationListBy($customer_id)
    {
        $this->db->select("postbox.*, location.location_name");
        $this->db->from("postbox");
        $this->db->join("location", "location.id=postbox.location_available_id", "left");
        $this->db->where("postbox.customer_id", $customer_id);
        $this->db->where("postbox.completed_delete_flag <> '1'", null);
        $this->db->group_by("postbox.location_available_id");

        $data = $this->db->get()->result();
        if ($data) {
            $result = array();
            foreach ($data as $d) {
                $tmp = new stdClass();
                $tmp->location_id = $d->location_available_id;
                $tmp->location_name = $d->location_name;
                array_push($result, $tmp);
            }

            return $result;
        }
        return array();
    }

    /**
     * @return location and postbox info by postbox_id
     */
    public function get_postbox($postbox_id)
    {
        $this->db->select("postbox.name, postbox.company, location.location_name, postbox.location_available_id, c2.country_name as partner_country");
        $this->db->select("cases_service_partner.phone as company_telephone, partner_partner.company_name, partner_partner.invoicing_city, partner_partner.invoicing_street ,partner_partner.invoicing_zipcode");
        $this->db->select("location.street, location.postcode, location.city, location.region, location.country_id, country.country_name");
        $this->db->from("postbox");
        $this->db->join("location", "location.id=postbox.location_available_id", "left");
        $this->db->join("country", "location.country_id = country.id", "left");
        $this->db->join("partner_partner", "location.partner_id = partner_partner.partner_id", "left");
        $this->db->join("country as c2", "partner_partner.invoicing_country = c2.id", "left");
        $this->db->join('cases_service_partner', 'cases_service_partner.partner_id = partner_partner.partner_id', "left");
        $this->db->where("postbox.postbox_id", $postbox_id);

        $data = $this->db->get()->result();
        return $data;
    }

    /**
     * Get all location of customer
     * @param unknown_type $customer_id
     */
    public function get_all_location($customer_id)
    {
        $this->db->select("postbox.location_available_id")->distinct();
        $this->db->from("postbox");
        $this->db->join("location", "location.id=postbox.location_available_id", "left");
        $this->db->where("postbox.customer_id", $customer_id);
        $this->db->where("postbox.completed_delete_flag <> '1'", null);

        return $this->db->get()->result();
    }

    /**
     * gets all message by customer.
     * @param unknown $customer_id
     */
    public function get_messages_by($customer_id)
    {
        $sql = <<<SQL
SELECT
        postbox_name,
        updated_date,
        postbox_id,
        new_postbox_type,
        (CASE
              WHEN plan_date_change_postbox_type IS NOT NULL AND (STR_TO_DATE(plan_date_change_postbox_type, '%Y%m%d') > CURDATE()) THEN 'change_postbox_type'
              WHEN plan_deleted_date IS NOT NULL AND (STR_TO_DATE(plan_deleted_date, '%Y%m%d') > CURDATE()) THEN 'delete_postbox'
              ELSE NULL
         END) AS action_type,        
        (CASE
              WHEN plan_date_change_postbox_type IS NOT NULL AND (STR_TO_DATE(plan_date_change_postbox_type, '%Y%m%d') > CURDATE()) THEN plan_date_change_postbox_type
              WHEN plan_deleted_date IS NOT NULL AND (STR_TO_DATE(plan_deleted_date, '%Y%m%d') > CURDATE()) THEN plan_deleted_date
              ELSE NULL
         END) AS plan_date
FROM
        postbox
WHERE
        customer_id = ?
    AND deleted <> 1                
UNION 
SELECT
        postbox_name,
        updated_date,
        postbox_id,
        new_postbox_type,
        (CASE
              WHEN plan_deleted_date IS NOT NULL AND (STR_TO_DATE(plan_deleted_date, '%Y%m%d') > CURDATE()) THEN 'delete_postbox'
              WHEN plan_date_change_postbox_type IS NOT NULL AND (STR_TO_DATE(plan_date_change_postbox_type, '%Y%m%d') > CURDATE()) THEN 'change_postbox_type'
              ELSE NULL
         END) AS action_type,       
        (CASE
              WHEN plan_date_change_postbox_type IS NOT NULL AND (STR_TO_DATE(plan_date_change_postbox_type, '%Y%m%d') > CURDATE()) THEN plan_date_change_postbox_type
              WHEN plan_deleted_date IS NOT NULL AND (STR_TO_DATE(plan_deleted_date, '%Y%m%d') > CURDATE()) THEN plan_deleted_date
              ELSE NULL
         END) AS plan_date
FROM
        postbox
WHERE
        customer_id = ?
    AND deleted <> 1
                
ORDER BY updated_date DESC        
   
SQL;
        $query = $this->db->query($sql, array($customer_id,$customer_id,));

        return $query->result();
    }

    /**
     * Gets list others location of customer.
     *
     * @param unknown $customer_id
     * @param unknown $postbox_id
     */
    public function get_all_others_location($customer_id, $postbox_id)
    {
        $this->db->select("postbox.location_available_id, location.location_name")->distinct();
        $this->db->from("postbox");
        $this->db->join("location", "location.id=postbox.location_available_id", "left");
        $this->db->where("postbox.location_available_id NOT IN (SELECT p.location_available_id FROM postbox p WHERE p.postbox_id ={$postbox_id}) ", null);
        $this->db->where("postbox.customer_id", $customer_id);
        $this->db->where("postbox.deleted <> 1 ", null);
        $this->db->group_by("postbox.location_available_id");

        return $this->db->get()->result();
    }

    /**
     * Gets max psotbox code
     * @param unknown $customer_id
     * @param unknown $location_id
     */
    public function get_max_postbox_code_by($customer_id, $location_id)
    {
        $sql = "SELECT
                    MAX(SUBSTR(postbox_code, 14, 10))  AS code
                    FROM postbox
                    WHERE customer_id=? AND location_available_id=?";
        $query = $this->db->query($sql, array(
            $customer_id,
            $location_id
        ));

        return $query->result();
    }

    /**
     * @param  $postbox : array postbox record to insert
     * @param  $actionType : type action to change table postbox
     */
    public function update_postbox_history($postbox, $actionType = null)
    {
        if ($postbox && is_array($postbox)) {
            ci()->load->model('mailbox/postbox_history_m');
            //$postbox['updated_date'] = now();
            $postbox['action_type'] = $actionType;
            ci()->postbox_history_m->insert($postbox);
        }
    }
    
    /**
     * count postbox registered by month.
     * @param unknown $yearMonth
     * @param unknown $locationId
     */
    public function countPostboxesRegisteredByMonth($yearMonth, $locationId, $charge_flag=true)
    {
        $this->db->select("postbox.type, count(*) as total");
        $this->db->join("customers", "customers.customer_id=postbox.customer_id", "inner");
        $this->db->where("from_unixtime(postbox.created_date, '%Y%m') <= '" . $yearMonth . "'", null);
        if(!empty($locationId)){
            $this->db->where("postbox.location_available_id IN (".$locationId.")", null);
        }
        
        if($charge_flag){
            $this->db->where("customers.charge_fee_flag", APConstants::ON_FLAG);
        }
        
        // only calculate normal account.
        $this->db->where("customers.account_type", APConstants::NORMAL_CUSTOMER);
        $this->db->where("(postbox.completed_delete_flag = 0 OR (postbox.completed_delete_flag = 1 AND from_unixtime(postbox.deleted_date, '%Y%m') >'".$yearMonth."' ) )", null);
        $this->db->where("(customers.status is null OR customers.status <> 1)", null);
        $this->db->group_by("postbox.type");
        
        $result = $this->db->get($this->_table)->result();
        
        return $result;
    }
    
    /*
     * count all postbox by condition.
     */
    public function count_all_postboxes_of_customer_by($array_where, $group_by='')
    {
        $this->db->select("postbox.*, customers.charge_fee_flag, customers.activated_flag, customers.customer_code");
        $this->db->from("postbox");
        $this->db->join("customers", "customers.customer_id=postbox.customer_id", "inner");
        $this->db->where("(customers.status is null OR customers.status <> 1)", null);
        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }
        
        if($group_by){
            $this->db->group_by($group_by);
        }
        
        $result = $this->db->get()->result();
        
        return count($result);
    }
    
    /**
     * gets all free postbox by customers.
     * @param type $customer_id
     */
    public function get_all_free_postboxes_by($customer_id)
    {
        if (!isset($customer_id)) {
            return array();
        }
        $sql = "SELECT P.*, PS.invoicing_cycle , ifnull(location.pricing_template_id, 1) as pricing_template_id
                FROM postbox P
                LEFT JOIN postbox_settings PS ON P.postbox_id = PS.postbox_id
                LEFT JOIN location ON location.id=P.location_available_id
                WHERE 
                    P.customer_id = ? 
                    AND (P.deleted <> '1' OR (deleted = 1 AND FROM_UNIXTIME(P.updated_date) >= DATE_FORMAT(NOW() ,'%Y-%m-01') ) ) 
                    AND completed_delete_flag <> 1
                    AND P.type = 1";
        $query = $this->db->query($sql, array(
            $customer_id
        ));
        

        return $query->result();
    }
    
    /**
     * Gets postbox and location address
     * @param type $postbox_id
     * @return type
     */
    public function get_postbox_location_by($postbox_id)
    {

        $this->db->select("postbox.postbox_id, postbox.postbox_name, postbox.name, postbox.company, country.country_name, location.*");
        $this->db->from("postbox");
        $this->db->join("location", "location.id=postbox.location_available_id", "left");
        $this->db->join("country", "location.country_id=country.id", "left");
        $this->db->where("postbox.postbox_id", $postbox_id);

        $data = $this->db->get()->row();
        return $data;
    }
    
    public function get_list_name_company($customer_ids){
        if(!$customer_ids){
            return  null;
        }
        
        $this->db->select("postbox.*");
        $this->db->from("postbox");
        $this->db->where("first_location_flag", APConstants::ON_FLAG);
        $this->db->where_in("customer_id", $customer_ids);
        $this->db->group_by("customer_id");
        $data = $this->db->get()->result();
        
        $result = array();
        foreach($data as $d){
            $tmp = new stdClass();
            $tmp->name = $d->name;
            $tmp->company = $d->company;
            $result[$d->customer_id]= $tmp;
        }
        
        return $result;
    }
    
    /**
     * #1113 check for identical Name in postbox name field 
     * Gets list name of a customer by same location.
     */
  	public function get_name_of_customer_in_location_by($array_where)
    {
        $this->db->select("*");
        $this->db->from("postbox");
        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }
        
        $result = $this->db->get()->result();
        
        return $result;
    }
    
     /**
     * Get all name location of a customer .
     * @param unknown $ids
     */
    public function get_all_name_location_by($array_where)
    {
        $this->db->select('location.location_name');
        //$this->_table
        $this->db->from('postbox');
        $this->db->join('location', 'postbox.location_available_id = location.id', 'left');
        
        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }

        $query = $this->db->get();
        $rows = $query->result();

        return $rows;
    }
    
    /**
     * count postbox registered by month of partner marketting
     * @param unknown $yearMonth
     * @param unknown $locationId
     */
    public function countPostboxesRegisteredByMonthOfPartner($yearMonth, $partner_id)
    {
        $this->db->select("postbox.type, count(DISTINCT(postbox.postbox_id)) as total");
        $this->db->join("customers", "customers.customer_id=postbox.customer_id", "inner");
        $this->db->join('partner_customers', 'partner_customers.customer_id = customers.customer_id', 'inner');
        $this->db->where("from_unixtime(postbox.created_date, '%Y%m') <= '" . $yearMonth . "'", null);
        $this->db->where("partner_customers.partner_id", $partner_id);
        $this->db->where("partner_customers.end_flag", APConstants::OFF_FLAG);
        $this->db->where("(postbox.completed_delete_flag = 0 OR (postbox.completed_delete_flag = 1 AND from_unixtime(postbox.deleted_date) >'".$yearMonth."' ) )", null);
        $this->db->where("(customers.status is null OR customers.status <> 1)", null);
        $this->db->group_by("postbox.type");
        
        $result = $this->db->get($this->_table)->result();
        
        return $result;
    }
    
    /**
     * Gets all enterprise postbox of customer
     * @param type $customer_id
     * @return type
     */
    public function get_all_enterprise_postboxes_by($customer_id) {
        if (!isset($customer_id)) {
            return array();
        }
        $sql = "SELECT P.*, PS.invoicing_cycle , ifnull(location.pricing_template_id, 1) as pricing_template_id
                FROM postbox P
                LEFT JOIN postbox_settings PS ON P.postbox_id = PS.postbox_id
                LEFT JOIN location ON location.id=P.location_available_id
                WHERE 
                    P.customer_id = ? 
                    AND completed_delete_flag =0
                    AND P.type = 5";
        $query = $this->db->query($sql, array(
            $customer_id
        ));
        

        return $query->result();
    }
    
    /**
     * gets all free postbox by customers.
     * @param type $customer_id
     */
    public function get_all_postboxes_for_invoices($customer_id) {
        if (empty($customer_id)) {
            return array();
        }
        
        $this->db->select("postbox.*, location.pricing_template_id");
        $this->db->join('location','location.id = postbox.location_available_id','left');
        $this->db->where('postbox.customer_id', $customer_id);
        $this->db->where('completed_delete_flag', 0);
        
        $result = $this->db->get($this->_table)->result();
        
        return $result;
    }
    
    /**
     * gets all free postbox by customers.
     * @param type $parent_customer_id
     */
    public function get_all_postboxes_by_enterprise_customer($parent_customer_id) {
        if (empty($parent_customer_id)) {
            return array();
        }
        
        $this->db->select("postbox.postbox_id, customers.user_name");
        $this->db->join('postbox_customer_users','postbox.postbox_id = postbox_customer_users.postbox_id','inner');
        $this->db->join('customers','postbox_customer_users.customer_id = customers.customer_id','inner');
        
        $this->db->where('postbox_customer_users.parent_customer_id', $parent_customer_id);
        
        $result = $this->db->get($this->_table)->result();
        
        return $result;
    }
}