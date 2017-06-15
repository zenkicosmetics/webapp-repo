<?php defined('BASEPATH') or exit('No direct script access allowed');

class cases_m extends MY_Model
{
    public $cases_config = array(
        'bank_account' => array(
            'cost' => 495.00,
            'milestones' => array(
                array(
                    'responsible' => 'customer',
                    'type' => 'payment',
                    'price' => 495.00,
                    'method' => array(
                        'BankAccountServicesController',
                        'payment'
                    )
                ),
                'personal_identity' => array(
                    'responsible' => 'customer',
                    'type' => 'form',
                    'method' => array(
                        'BankAccountServicesController',
                        'personalIdentityForm'
                    )
                ),
                'company_information' => array(
                    'responsible' => 'customer',
                    'type' => 'form',
                    'method' => array(
                        'BankAccountServicesController',
                        'companyInformationForm'
                    )
                )
            )
        ),
        'uk_verification' => array(
            'prefix' => 'VUK',
            'description' => 'Personal Data Verification',
            'partner' => 'ClevverMail UK',
            'country' => 'United Kingdom',
            'cost' => 0,
            'milestones' => array(
                array(
                    'responsible' => 'customer',
                    'type' => 'form',
                    'method' => array(
                        'UkVerificationServicesController',
                        'personalDataForm'
                    )
                )
            )
        )
    );

    public function __construct()
    {
        parent::__construct();

        $this->_table = $this->profile_table = $this->db->dbprefix('cases');
        $this->primary_key = 'id';
    }

    /**
     * Gets devices template paging.
     *
     * @param unknown $array_where
     * @param number $start
     * @param number $limit
     * @param unknown $sort_column
     * @param string $sort_type
     * @return multitype:number multitype: |multitype:unknown multitype:
     */
    public function get_cases_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC')
    {
        // Count all record with input condition
        $total_record = $this->count_cases_paging($array_where);
        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }
        $this->db->select('cases.*, customers.email, customers.user_name,customers.customer_code, country.country_name as country_name, cases_product.product_name' . ', EXISTS(SELECT 1 FROM cases_milestone_instance 
            WHERE cases.id = cases_milestone_instance.case_id AND cases_milestone_instance.status=1) AS has_to_do')->distinct();
        $this->db->join("customers", "customers.customer_id = cases.customer_id", "left");
        $this->db->join("country", "cases.country = country.id", "left");
        $this->db->join("cases_product", "cases_product.id = cases.product_id", "left");
        //$this->db->where("cases.deleted_flag", APConstants::OFF_FLAG);
        // Search all data with input condition
        foreach ($array_where as $key => $value) {
            $this->db->where($key, $value);
        }
        //$this->db->where('(cases.deleted_flag = 0 )', NULL);

        $this->db->limit($limit);
        if (!empty($sort_column)) {
            $this->db->order_by($sort_column, $sort_type);
        } else {
            $this->db->order_by('cases.created_date', 'DESC');
        }
        $data = $this->db->get($this->_table, $limit, $start)->result();

        return array(
            "total" => $total_record,
            "data" => $data
        );
    }

    /**
     * Count customer
     *
     * @param unknown_type $array_where
     */
    public function count_cases_paging($array_where)
    {
        $this->db->select('COUNT(DISTINCT(cases.id)) AS total_record');

        $this->db->from('cases');
        $this->db->join("country", "cases.country = country.id", "left");
        $this->db->join("cases_product", "cases_product.id = cases.product_id", "left");

        $this->db->join("cases_milestone_instance", "cases_milestone_instance.case_id = cases.id", "left");
        $this->db->join("cases_milestone", "cases_milestone_instance.milestone_id = cases_milestone.id", "left");

        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }
        $this->db->where('(cases.deleted_flag = 0  )', NULL);

        $result = $this->db->get()->row();
        return $result->total_record;
    }

    /**
     * Gets devices template paging.
     *
     * @param unknown $array_where
     * @param number $start
     * @param number $limit
     * @param unknown $sort_column
     * @param string $sort_type
     * @return multitype:number multitype: |multitype:unknown multitype:
     */
    public function get_admin_cases_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC')
    {
        // Count all record with input condition
        $total_record = $this->count_admin_cases_paging($array_where);
        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }

        $this->db->select('cases.*, customers.email,customers.customer_code, country.country_name as country_name, cases_product.product_name' 
            . ', EXISTS(SELECT 1 FROM cases_milestone_instance
            WHERE cases.id = cases_milestone_instance.case_id AND cases_milestone_instance.status=1) AS has_to_do')->distinct();
        $this->db->join("customers", "customers.customer_id = cases.customer_id", "left");
        $this->db->join("country", "cases.country = country.id", "left");
        $this->db->join("cases_product", "cases_product.id = cases.product_id", "left");
        // Search all data with input condition
        foreach ($array_where as $key => $value) {
            $this->db->where($key, $value);
        }
        $this->db->where('(cases.deleted_flag = 0 )', NULL);

        $this->db->limit($limit);
        if (!empty($sort_column)) {
            $this->db->order_by($sort_column, $sort_type);
        } else {
            $this->db->order_by('cases.created_date', 'DESC');
        }
        $data = $this->db->get($this->_table, $limit, $start)->result();

        return array(
            "total" => $total_record,
            "data" => $data
        );
    }

    /**
     * Count customer
     *
     * @param unknown_type $array_where
     */
    public function count_admin_cases_paging($array_where)
    {
        $this->db->select('COUNT(DISTINCT(cases.id)) AS total_record');

        $this->db->from('cases');
        $this->db->join("customers", "customers.customer_id = cases.customer_id", "left");
        $this->db->join("country", "cases.country = country.id", "left");
        $this->db->join("cases_product", "cases_product.id = cases.product_id", "left");

        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }
        $this->db->where('(cases.deleted_flag = 0 )', NULL);

        $result = $this->db->get()->row();
        return $result->total_record;
    }

    public function get_case_name_by($base_taskname)
    {
        $this->db->select('cases_taskname.milestone_id');
        $this->db->from('cases_taskname');
        $this->db->where('cases_taskname.base_task_name', $base_taskname);
        $milestone = $this->db->get()->row();

        $case_name = '';
        if ($milestone) {
            $milestone_id = $milestone->milestone_id;
            $this->db->select('cases_instance.*');
            $this->db->from('cases_instance');
            $this->db->like('list_milestone_id', $milestone_id);
            $case_instance = $this->db->get()->row();

            if ($case_instance) {
                $case_name = $case_instance->case_instance_name;
            }
        }

        return $case_name;
    }

    public function get_milestone_name_by($base_taskname)
    {
        $this->db->select('cases_taskname.milestone_id');
        $this->db->from('cases_taskname');
        $this->db->where('cases_taskname.base_task_name', $base_taskname);
        $milestone = $this->db->get()->row();

        $case_name = '';
        if ($milestone) {
            $milestone_id = $milestone->milestone_id;
            $this->db->select('cases_taskname.*');
            $this->db->from('cases_taskname');
            $this->db->where('milestone_id', $milestone_id);
            $case_milestone = $this->db->get()->row();

            if ($case_milestone) {
                $case_name = $case_milestone->task_name;
            }
        }

        return $case_name;
    }
    
    /**
     * 
     * Gets info customer in  cases verification was completed 
     * @param int $location_id
     * @param int $start_date
     * @param int $end_date
     * 
     * @return multitype:number multitype: |multitype:unknown multitype:
     */
    public function get_info_customer_in_cases_verification_completed_by($location_id, $start_date, $end_date)
    {
        // customer_id
        $tmp_arr_customer_id = array();
//        $tmp_customer_id = '';
        $arr_customer_id = $this->count_postbox_in_cases_verification_completed_of_customer_by($location_id, $start_date, $end_date);
//         var_dump($arr_customer_id);
        foreach($arr_customer_id as $arr){
            if($arr->count_postbox > 1){
                $tmp_arr_customer_id[] = $arr->customer_id;
            }
        }
       $tmp_customer_id = implode(",",$tmp_arr_customer_id);
//       var_dump($tmp_arr_customer_id);
        $this->db->select('*');
        $this->db->from('customers_address');
        $this->db->where_in('customer_id', $tmp_customer_id);
        $query = $this->db->get();
        $results = $query->result();
       
        return $results;
    }
    
     /**
     * 
     * Gets postbox in cases verification was completed 
     * @param int $location_id
     * @param int $start_date
     * @param int $end_date
     * 
     * @return multitype:number multitype: |multitype:unknown multitype:
     */
    public function get_postbox_in_cases_verification_completed_by($location_id, $start_date, $end_date)
    {
        // customer_id
        $tmp_arr_customer_id = array();
        $tmp_customer_id = '';
        $arr_customer_id = $this->count_postbox_in_cases_verification_completed_of_customer_by($location_id, $start_date, $end_date);
        foreach($arr_customer_id as $arr){
            
            $tmp_arr_customer_id[] = $arr->customer_id;
        }
       $tmp_customer_id = implode(",",$tmp_arr_customer_id);

        $SQL = ' SELECT * 
                 FROM (SELECT cases_postbox_customer.customer_id, cases_postbox_customer.case_identifier, cases_postbox_customer.country,
                            cases_postbox_customer.postbox_id,
                            cases_postbox_customer.updated_date_1,cases_postbox_customer.updated_date_2,cases_postbox_customer.updated_date_3,
                            cases_postbox_customer.postbox_code, cases_postbox_customer.postbox_name, 
                            cases_postbox_customer.location_available_id, cases_postbox_customer.name, cases_postbox_customer.company,
                            cases_postbox_customer.deleted,cases_postbox_customer.completed_delete_flag, 
                            customers.status
                        FROM 
                            (SELECT all_cases.customer_id, all_cases.case_identifier, all_cases.country, all_cases.postbox_id,
                                    all_cases.updated_date_1,all_cases.updated_date_2,all_cases.updated_date_3,
                                    postbox.postbox_code, postbox.postbox_name, postbox.location_available_id, postbox.name, postbox.company,
                                    postbox.deleted,postbox.completed_delete_flag
                            FROM 
                                (SELECT c.customer_id, c.case_identifier, c.country, c.postbox_id,
                                        c1.updated_date as updated_date_1,
                                        c2.updated_date as updated_date_2,  
                                        c3.updated_date as updated_date_3,
                                        c1.status as status_verification_company_hard,
                                        c2.status as status_verification_personal_identity, 
                                        c3.status as status_verification_usps 
                                FROM (select * from cases ) c
                                LEFT JOIN (select * from cases_verification_company_hard where cases_verification_company_hard.`status` = 2) c1
                                    ON c.id = c1.case_id 
                                LEFT JOIN (select * from cases_verification_personal_identity where cases_verification_personal_identity.`status` = 2) c2
                                    ON c.id = c2.case_id 
                                LEFT JOIN (select * from cases_verification_usps where cases_verification_usps.`status` = 2) c3
                                    ON c.id = c3.case_id 
                                WHERE c1.status = 2 or c2.status = 2 or c3.status = 2) all_cases
                            LEFT JOIN postbox 
                                ON all_cases.postbox_id = postbox.postbox_id
                            WHERE (postbox.deleted = 1 OR postbox.deleted IS NULL)
                                   AND (postbox.completed_delete_flag = 1 OR postbox.completed_delete_flag IS NULL) 
                                   AND postbox.location_available_id =' .$location_id .
                                  ' AND((updated_date_1 >' .$start_date . ' AND updated_date_1 <'.$end_date . ') OR'.
                                  '(updated_date_2 >'.$start_date .' AND updated_date_2 <'.$end_date . ') OR'.
                                  '(updated_date_3 >'.$start_date .' AND updated_date_3 <'.$end_date . '))'.
                        ')  cases_postbox_customer 
                        LEFT JOIN customers
                            ON cases_postbox_customer.customer_id = customers.customer_id
                        WHERE customers.status = 1)  complete_cases_postbox_customer
                    WHERE complete_cases_postbox_customer.customer_id in(' . $tmp_customer_id .')';
    	
       $query = $this->db->query($SQL);

       $results = $query->result();
       
       return $results;
    }
    
    /* 
     * Count postbox in cases verification completed of customer
     * @param int $location_id
     * @param int $start_date
     * @param int $end_date
     * 
     * @return multitype:number multitype: |multitype:unknown multitype
     */
    public function count_postbox_in_cases_verification_completed_of_customer_by($location_id, $start_date, $end_date)
    {
    	 $SQL = ' SELECT customer_id,count(customer_id) as count_postbox
                FROM (SELECT cases_postbox_customer.customer_id, cases_postbox_customer.case_identifier, cases_postbox_customer.country,
                            cases_postbox_customer.postbox_id,
                            cases_postbox_customer.updated_date_1,cases_postbox_customer.updated_date_2,cases_postbox_customer.updated_date_3,
                            cases_postbox_customer.postbox_code, cases_postbox_customer.postbox_name, 
                            cases_postbox_customer.location_available_id, cases_postbox_customer.name, cases_postbox_customer.company,
                            cases_postbox_customer.deleted,cases_postbox_customer.completed_delete_flag, 
                            customers.status
                        FROM 
                            (SELECT all_cases.customer_id, all_cases.case_identifier, all_cases.country, all_cases.postbox_id,
                                    all_cases.updated_date_1,all_cases.updated_date_2,all_cases.updated_date_3,
                                    postbox.postbox_code, postbox.postbox_name, postbox.location_available_id, postbox.name, postbox.company,
                                    postbox.deleted,postbox.completed_delete_flag
                            FROM 
                                (SELECT c.customer_id, c.case_identifier, c.country, c.postbox_id,
                                        c1.updated_date as updated_date_1,
                                        c2.updated_date as updated_date_2,  
                                        c3.updated_date as updated_date_3,
                                        c1.status as status_verification_company_hard,
                                        c2.status as status_verification_personal_identity, 
                                        c3.status as status_verification_usps 
                                FROM (select * from cases ) c
                                LEFT JOIN (select * from cases_verification_company_hard where cases_verification_company_hard.`status` = 2) c1
                                    ON c.id = c1.case_id 
                                LEFT JOIN (select * from cases_verification_personal_identity where cases_verification_personal_identity.`status` = 2) c2
                                    ON c.id = c2.case_id 
                                LEFT JOIN (select * from cases_verification_usps where cases_verification_usps.`status` = 2) c3
                                    ON c.id = c3.case_id 
                                WHERE c1.status = 2 or c2.status = 2 or c3.status = 2) all_cases
                            LEFT JOIN postbox 
                                ON all_cases.postbox_id = postbox.postbox_id
                            WHERE (postbox.deleted = 1 OR postbox.deleted IS NULL)
                                   AND (postbox.completed_delete_flag = 1 OR postbox.completed_delete_flag IS NULL) 
                                   AND postbox.location_available_id =' .$location_id .
                                  ' AND((updated_date_1 >' .$start_date . ' AND updated_date_1 <'.$end_date . ') OR'.
                                  '(updated_date_2 >'.$start_date .' AND updated_date_2 <'.$end_date . ') OR'.
                                  '(updated_date_3 >'.$start_date .' AND updated_date_3 <'.$end_date . '))'.
                        ')  cases_postbox_customer 
                        LEFT JOIN customers
                            ON cases_postbox_customer.customer_id = customers.customer_id
                        WHERE customers.status = 1)  complete_cases_postbox_customer
                GROUP BY  complete_cases_postbox_customer.customer_id';
        $query = $this->db->query($SQL);

       $results = $query->result();
       
       return $results;
    }
}