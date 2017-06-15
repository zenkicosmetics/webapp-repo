<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 * @author DungNT
 */
class CaseUtils
{
    /**
     * Settings cache
     *
     * @var array
     */
    private static $cache_ship = array();

    /**
     * The Settings Construct
     */
    public function __construct()
    {
        ci()->load->helper('text');
        $this->ci = &get_instance();

        ci()->load->model(array(
            'settings/countries_m',
            'customers/customer_m',
            'addresses/customers_address_m',
            'mailbox/postbox_m',
            'addresses/location_m',
            'cases/cases_m',
            "cases/cases_product_m",
            "cases/cases_milestone_m",
            "cases/cases_milestone_instance_m",
            "cases/cases_taskname_m",
            "cases/cases_taskname_instance_m",
            "cases/cases_verification_personal_identity_m",
            "cases/cases_verification_usps_m",
            "cases/cases_verification_company_hard_m",
            "cases/cases_product_base_taskname_m",
            "cases/cases_instance_m",
            "cases/cases_contract_m",
            "cases/cases_proof_business_m",
            "cases/cases_company_ems_m",
            "cases/cases_verification_history_m",
            "cases/case_phone_number_m",
            "phones/phone_number_m",
        ));

        ci()->lang->load('cases/cases');
        ci()->load->library('form_validation');
    }

    /**
     * Start verification case.
     *
     * @param string $customer_id
     * @param boolean $by_admin
     */
    public static function start_verification_case($customer_id, $by_admin = false)
    {
        log_audit_message(APConstants::LOG_DEBUG, "Start start_verification_case for customer:" . $customer_id);
        $customer = APContext::getCustomerByID($customer_id);
        if ($customer->required_verification_flag != APConstants::ON_FLAG) {
            log_audit_message(APConstants::LOG_DEBUG, "required_verification_flag != 1 of customer:" . $customer_id);
            return array();
        }

        // Get all postbox
        $list_postbox = ci()->postbox_m->get_many_by_many(array(
            'customer_id' => $customer_id,
            'deleted' => APConstants::OFF_FLAG,
            'completed_delete_flag' => APConstants::OFF_FLAG
        ));

        $result = array();
        $obj = CaseUtils::start_case_verification_by_postbox($by_admin, $customer);

        if ($obj != null) {
            $result[$obj['case_id']] = $obj;
        } else {
            // check verification case.
            if ($customer->activated_flag == APConstants::ON_FLAG) {
                // completed case verification.
                ci()->cases_m->update_by_many(array(
                    "postbox_id is null" => null,
                    "customer_id" => $customer_id,
                    "status <> '2'" => null,
                    "deleted_flag" => APConstants::OFF_FLAG,
                    "target_type" => APConstants::CASE_PRODUCT_TYPE_ADDRESS
                ), array(
                    "status" => "2"
                ));
                
                // Update: must not verify for this customer.
                ci()->customers_address_m->update_by_many(array(
                    "customer_id" => $customer_id
                ), array(
                    "invoice_address_verification_flag" => APConstants::ON_FLAG
                ));
            }
        }

        foreach ($list_postbox as $postbox) {
            log_audit_message(APConstants::LOG_DEBUG, "Start start_verification_case for customer:" . $customer_id . ', Postbox:' . $postbox->postbox_code);
            $obj = CaseUtils::start_case_verification_by_postbox($by_admin, $customer, $postbox);
            if ($obj != null) {
                $result[$obj['case_id']] = $obj;
            } else {
                // check verification case.
                if ($customer->activated_flag == APConstants::ON_FLAG) {
                    // completed case verification.
                    ci()->cases_m->update_by_many(array(
                        "postbox_id" => $postbox->postbox_id,
                        "customer_id" => $customer_id,
                        "status <> '2'" => null,
                        "deleted_flag" => APConstants::OFF_FLAG,
                        "target_type" => APConstants::CASE_PRODUCT_TYPE_POSTBOX
                    ), array(
                        "status" => "2"
                    ));
                    
                    // Update: must not verify for this customer.
                    ci()->postbox_m->update_by_many(array(
                        "postbox_id" => $postbox->postbox_id
                    ), array(
                        "name_verification_flag" => APConstants::ON_FLAG,
                        "company_verification_flag" => APConstants::ON_FLAG
                    ));
                }
            }
        }
        
        // gets phone case verification
        $list_phone_number = ci()->phone_number_m->get_many_by_many(array(
            "customer_id" => $customer_id,
            "is_verification_flag" => APConstants::OFF_FLAG
        ));

        foreach($list_phone_number as $phone){
            $obj2 = CaseUtils::start_case_verification_by_phone($by_admin, $customer, $phone);

            if ($obj2 != null) {
                $result[$obj2['case_id']] = $obj2;
            } else {
                // check verification case.
                if ($customer->activated_flag == APConstants::ON_FLAG) {
                    // completed case verification.
                    ci()->cases_m->update_by_many(array(
                        "target_type" => APConstants::CASE_PRODUCT_TYPE_PHONE,
                        "customer_id" => $customer_id,
                        "status <> '2'" => null,
                        "deleted_flag" => APConstants::OFF_FLAG,
                        "target_type" => APConstants::CASE_PRODUCT_TYPE_PHONE
                    ), array(
                        "status" => "2"
                    ));

                    // Update: must not verify for this customer.
                    ci()->phone_number_m->update_by_many(array(
                        "id" => $phone->id
                    ), array(
                        "is_verification_flag" => APConstants::ON_FLAG
                    ));
                }
            }
        }

        return $result;
    }

    /**
     * Start case verification by posbox
     *
     * @param bool $by_admin
     * @param string $customer_id
     * @param unknown_type $postbox
     */
    public static function start_case_verification_by_postbox($by_admin = false, $customer, $postbox = null)
    {
        $is_personal_identification = true;
        $is_company_verification = true;
        $is_company_shareholder_verification = true;
        $is_USPS_form_1583_verify = true;
        $is_general_cmra_verify = true;
        $is_tc_contract_verify = true;
        $is_proof_address_verify = true;
        $is_company_verification_ems_verify = true;
        $is_california_mailbox_verify = true;

        $is_personal_status = '';
        $is_company_status = '';
        $is_company_hard_status = '';
        $is_USPS_form_1583_status = '';
        $is_general_cmra_status = '';
        $is_tc_contract_status = '';
        $is_company_verification_ems_status = '';
        $is_proof_address_status = '';
        $is_california_mailbox_status = '';

        $customer_id = $customer->customer_id;
        $customer_code = $customer->customer_code;
        $postbox_id = null;
        $postbox_code = null;
        $new_postbox = null;
        
        $target_type = APConstants::CASE_PRODUCT_TYPE_POSTBOX;
        if(empty($postbox)){
            $target_type = APConstants::CASE_PRODUCT_TYPE_ADDRESS;
        }

        if ($customer->required_verification_flag != APConstants::ON_FLAG) {
            return null;
        }

        $list_case_number = array();
        if (!empty($postbox)) {
            $postbox_id = $postbox->postbox_id;
            $postbox_code = $postbox->postbox_code;
            $list_case_number = APUtils::get_list_case($customer_id, $postbox_id);
        } else {
            $list_case_number = APUtils::get_list_case_invoice_address($customer_id);
        }

        $list_case_number_by_customer = array();
        foreach ($list_case_number as $case_number) {
            if (!in_array($case_number, $list_case_number_by_customer)) {
                $list_case_number_by_customer[] = $case_number;
            }
        }

        log_audit_message(APConstants::LOG_ERROR, "List case of customer:" . $customer_id . ', Postbox:' . $postbox_code . '==>' . json_encode($list_case_number_by_customer));
        if (count($list_case_number_by_customer) == 0) {
            // xoa tat ca cac cases
            CaseUtils::deleteUnusedCaseBy($customer_id, '', $postbox_id, '', $list_case_number_by_customer);
            return null;
        }

        // Get list base task name
        $list_base_task_name = ci()->cases_instance_m->get_base_task_name($list_case_number_by_customer);

        // Check cases
        CaseUtils::checkValidCases($is_personal_identification, $is_company_verification, $is_company_shareholder_verification
                , $is_USPS_form_1583_verify, $is_general_cmra_verify, $is_proof_address_verify
                , $is_company_verification_ems_verify, $is_tc_contract_verify,$is_california_mailbox_verify, $list_base_task_name);
        
        $debug_message = 'Customer ID:' . $customer_id . ', Postbox: ' . $postbox_code;
        $debug_message = $debug_message . 'personal_identification:' . $is_personal_identification . ',';
        $debug_message = $debug_message . 'company_verification:' . $is_company_verification . ',';
        $debug_message = $debug_message . 'company_shareholder_verification:' . $is_company_shareholder_verification . ',';
        $debug_message = $debug_message . 'USPS_form_1583_verify:' . $is_USPS_form_1583_verify . ',';
        $debug_message = $debug_message . 'general_cmra_verify:' . $is_general_cmra_verify;
        $debug_message = $debug_message . 'company_verification_ems_verify:' . $is_company_verification_ems_verify;
        $debug_message = $debug_message . 'proof_address_verify:' . $is_proof_address_verify;
        $debug_message = $debug_message . 'tc_contract_verify:' . $is_tc_contract_verify;
        $debug_message = $debug_message . 'california_mailbox_verify:' . $is_california_mailbox_verify;
        log_audit_message(APConstants::LOG_DEBUG, $debug_message, false, 'checkValidCases');

        if (!empty($postbox)) {
            $case_identifier = $postbox_code . '_VRPO01';
            $group_name = "Verify postbox " . end(explode('_', $postbox_code)) . ' - '. $customer_code;
            if(!empty($postbox->name)){
                $group_name .= " - ".$postbox->name;
            }
            
            if(!empty($postbox->company)){
                $group_name .= " - ".$postbox->company;
            }
            $case_exist_01 = ci()->cases_m->get_by_many(array(
                'postbox_id' => $postbox_id,
                'deleted_flag' => APConstants::OFF_FLAG,
                'target_type' => $target_type
            ));
        } else {
            $case_identifier = $customer_code . '_VRAD01';
            $group_name = "Verify invoice address - ". $customer_code;
            $case_exist_01 = ci()->cases_m->get_by_many(array(
                'case_identifier' => $case_identifier,
                'deleted_flag' => APConstants::OFF_FLAG,
                'target_type' => $target_type
            ));
        }

        
        $case_id_01 = $case_exist_01 != null ? $case_exist_01->id : 0;

        // Reset case status if admin request.
        if ($by_admin) {
            CaseUtils::resetCaseByAdmin($case_id_01);
        }
        
        // Reset case if change name or company of postbox
        if($postbox_id){
            
            // Gets new postbox
            $new_postbox = ci()->postbox_m->get($postbox_id);

            // Reset personal case.
            if($new_postbox->name != $postbox->name){
                $case_task_name = ci()->cases_taskname_instance_m->get_many_by_many(array(
                    "case_id" => $case_id_01,
                    "base_task_name IN ('verification_personal_identification', 'verification_special_form_PS1583','verification_california_mailbox', 'verification_General_CMRA', 'TC_contract_MS')" => null
                ));

                if($case_task_name){
                    $milestone_instance_ids = array();
                    foreach($case_task_name as $c){
                        $milestone_instance_ids[] = $c->milestone_instance_id;
                    }
                    CaseUtils::resetPersonalCase($case_id_01, $milestone_instance_ids);
                    
                    ci()->postbox_m->update_by_many(array(
                        "postbox_id" => $postbox_id
                    ), array(
                        "name_verification_flag" => APConstants::OFF_FLAG
                    ));
                }
            }
            // reset compnay case.
            if($new_postbox->company != $postbox->company){
                $case_task_name = ci()->cases_taskname_instance_m->get_many_by_many(array(
                    "case_id" => $case_id_01,
                    "base_task_name IN( 'verification_company_identification_soft', 'verification_company_identification_hard','verification_california_mailbox', 'verification_special_form_PS1583', 'verification_General_CMRA', 'company_verification_E_MS', 'TC_contract_MS')" => null
                ));
                if($case_task_name){
                    $milestone_instance_ids = array();
                    foreach($case_task_name as $c){
                        $milestone_instance_ids[] = $c->milestone_instance_id;
                    }
                    CaseUtils::resetCompanyCase($case_id_01, $milestone_instance_ids);
                    
                    ci()->postbox_m->update_by_many(array(
                        "postbox_id" => $postbox_id
                    ), array(
                        "company_verification_flag" => APConstants::OFF_FLAG
                    ));
                }
            }
        }

        // Check case status.
        CaseUtils::checkCaseStatus($case_id_01, $is_personal_identification, $is_personal_status, $is_company_verification
                , $is_company_status, $is_company_shareholder_verification, $is_company_hard_status, $is_USPS_form_1583_verify
                , $is_general_cmra_verify, $is_proof_address_verify , $is_company_verification_ems_verify, $is_tc_contract_verify
                , $is_USPS_form_1583_status, $is_general_cmra_status, $is_proof_address_status, $is_company_verification_ems_status
                , $is_tc_contract_status, $is_california_mailbox_verify, $is_california_mailbox_status);

        $log_data = array(
            'is_personal_identification' => $is_personal_identification,
            'is_company_verification' => $is_company_verification,
            'is_company_shareholder_verification' => $is_company_shareholder_verification,
            'is_USPS_form_1583_verify' => $is_USPS_form_1583_verify,
            'is_general_cmra_verify' => $is_general_cmra_verify,
            "is_company_verification_ems_verify" => $is_company_verification_ems_verify,
            "is_proof_address_verify" => $is_proof_address_verify,
            "is_tc_contract_verify" => $is_tc_contract_verify,
            'Postbox' => $postbox_code,
            'CustomerID' => $customer_id,
            'is_personal_status' => $is_personal_status,
            'is_company_status' => $is_company_status,
            'is_company_hard_status' => $is_company_hard_status,
            'is_USPS_form_1583_status' => $is_USPS_form_1583_status,
            'is_general_cmra_status' => $is_general_cmra_status,
            'is_company_verification_ems_status' => $is_company_verification_ems_status,
            'is_proof_address_status' => $is_proof_address_status,
            'is_tc_contract_status' => $is_tc_contract_status,
            'is_california_mailbox_verify' => $is_california_mailbox_verify,
            'is_california_mailbox_status' => $is_california_mailbox_status
        );
        
        log_audit_message(APConstants::LOG_DEBUG, json_encode($log_data), false, 'checkCaseStatus');
        
        // All verify process completed
        if ($is_personal_identification 
                && $is_company_verification 
                && $is_company_shareholder_verification 
                && ($is_USPS_form_1583_verify && $is_general_cmra_verify)
                && $is_company_verification_ems_verify
                && $is_proof_address_verify
                && $is_tc_contract_verify
                && $is_california_mailbox_verify) {
            
            $is_completed_verify = true;
            // delete unuse case.
            CaseUtils::deleteUnusedCaseBy($customer_id, $list_base_task_name, $postbox_id, $case_id_01, $list_case_number_by_customer);
            return null;
        }

        // Check exist this case
        //$case_exist = ci()->cases_m->get_by_many(array(
        //    'case_identifier' => $case_identifier,
        //    'target_type' => $target_type
        //));

        $product_id = '5';
        if (empty($case_exist_01)) {
            $customer_address = ci()->customers_address_m->get_by_many(array(
                'customer_id' => $customer_id
            ));
            
            if(!empty($customer_address)){
                $case_country_id = $customer_address->invoicing_country;
            }
            if (!empty($postbox)) {
                $case_country_id = APUtils::getCountryIDOfPostbox($postbox_id);
            }

            // Insert new case
            $data = array(
                "customer_id" => $customer_id,
                "postbox_id" => $postbox_id,
                "opening_date" => now(),
                "case_identifier" => $case_identifier,
                "product_id" => $product_id,
                "country" => $case_country_id,
                "status" => '0',
                "target_type" => ($postbox_id == null) ? APConstants::CASE_PRODUCT_TYPE_ADDRESS : APConstants::CASE_PRODUCT_TYPE_POSTBOX,
                "description" => 'Case Verification',
                "created_date" => time()
            );

            $case_id = ci()->cases_m->insert($data);
        } else {
            $case_id = $case_exist_01->id;
            ci()->cases_m->update_by_many(array(
                'id' => $case_id
            ), array(
                'deleted_flag' => APConstants::OFF_FLAG,
                "case_identifier" => $case_identifier,
            ));
        }
        
//        $debug_message = 'Customer ID:' . $customer_id . ', Postbox: ' . $postbox_code;
//        $debug_message = $debug_message . 'personal_identification:' . $is_personal_identification . ',';
//        $debug_message = $debug_message . 'company_verification:' . $is_company_verification . ',';
//        $debug_message = $debug_message . 'company_shareholder_verification:' . $is_company_shareholder_verification . ',';
//        $debug_message = $debug_message . 'USPS_form_1583_verify:' . $is_USPS_form_1583_verify . ',';
//        $debug_message = $debug_message . 'general_cmra_verify:' . $is_general_cmra_verify;
//        log_audit_message(APConstants::LOG_DEBUG, $debug_message);

        // insert milestone and taskname
        CaseUtils::insert_milestone($case_id, $product_id, $postbox_id, $is_personal_identification, $is_company_verification,
                $is_company_shareholder_verification, $is_USPS_form_1583_verify, $is_general_cmra_verify,
                $is_proof_address_verify , $is_company_verification_ems_verify, $is_tc_contract_verify,
                $is_california_mailbox_verify, $list_case_number_by_customer);

        // Update invoice address
        if (empty($postbox)) {
            ci()->customers_address_m->update_by_many(array(
                'customer_id' => $customer_id
            ), array(
                'invoice_address_verification_flag' => APConstants::OFF_FLAG
            ));
        } else {
            $company_verification_flag = 1;
            $name_verification_flag = 1;
            
            // only update verification company if not completed.
            if ( (!$is_company_verification && $is_company_status !=2 )
                    || (!$is_company_verification_ems_verify && $is_company_verification_ems_status !=2 )
                    || (!$is_company_shareholder_verification && $is_company_hard_status != 2 )) {
                $company_verification_flag = 0;
            }

            // only update verification personal if not completed.
            if ( !$is_personal_identification && $is_personal_status != 2 ) {
                $name_verification_flag = 0;
            }

            if ( (!$is_USPS_form_1583_verify && $is_USPS_form_1583_status != "2") 
                    || ($is_general_cmra_status !="2" && !$is_general_cmra_verify )
                    || ($is_tc_contract_status != "2" && !$is_tc_contract_verify )
                    || ($is_california_mailbox_status != "2" && !$is_california_mailbox_verify)) {
                $name_verification_flag = 0;
                $company_verification_flag = 0;
            }
            
            ci()->postbox_m->update_by_many(array(
                'postbox_id' => $postbox_id
            ), array(
                'name_verification_flag' => $name_verification_flag,
                'company_verification_flag' => $company_verification_flag
            ));
        }

        // xoa case khong can check.
        CaseUtils::deleteUnusedCaseBy($customer_id, $list_base_task_name, $postbox_id, $case_id, $list_case_number_by_customer);
        
        $return_data = array(
            'is_personal_identification' => $is_personal_identification,
            'is_company_verification' => $is_company_verification,
            'is_company_shareholder_verification' => $is_company_shareholder_verification,
            'is_USPS_form_1583_verify' => $is_USPS_form_1583_verify,
            'is_general_cmra_verify' => $is_general_cmra_verify,
            "is_company_verification_ems_verify" => $is_company_verification_ems_verify,
            "is_proof_address_verify" => $is_proof_address_verify,
            "is_tc_contract_verify" => $is_tc_contract_verify,
            'group_name' => $group_name,
            'case_id' => $case_id,
            'is_personal_status' => $is_personal_status,
            'is_company_status' => $is_company_status,
            'is_company_hard_status' => $is_company_hard_status,
            'is_USPS_form_1583_status' => $is_USPS_form_1583_status,
            'is_general_cmra_status' => $is_general_cmra_status,
            'is_company_verification_ems_status' => $is_company_verification_ems_status,
            'is_proof_address_status' => $is_proof_address_status,
            'is_tc_contract_status' => $is_tc_contract_status,
            'is_california_mailbox_verify' => $is_california_mailbox_verify,
            'is_california_mailbox_status' => $is_california_mailbox_status
        );
        
        log_audit_message(APConstants::LOG_DEBUG, 'Customer ID:' . $customer_id . ', Postbox: ' . $postbox_code . 'data: ' . json_encode($return_data), false, 'start_case_verification_by_postbox');

        return $return_data;
    }

    /**
     * Insert mile stone and task detail.
     *
     * @param unknown_type $case_id
     * @param unknown_type $product_id
     */
    public static function insert_milestone($case_id, $product_id, $postbox_id, $is_personal_identification, 
            $is_company_verification,$is_company_shareholder_verification, $is_USPS_form_1583_verify, $is_general_cmra_verify,
            $is_proof_address_verify , $is_company_verification_ems_verify, $is_tc_contract_verify,$is_california_mailbox_verify, $list_case_number_by_customer)
    {
        // Insert milestone instance
        $array_condition = array();
        $array_condition['cases_milestone.product_id'] = $product_id;
        $milestone_list_id = ci()->cases_instance_m->get_list_milestone_id($list_case_number_by_customer);
        $array_condition["cases_milestone.id IN ('" . implode("','", $milestone_list_id) . "')"] = null;
        $query_result = ci()->cases_milestone_m->get_milestone_paging($array_condition, 0, 1000, null, null);
        // Process output data
        $milestone_datas = $query_result['data'];

        // Get all base task name
        $all_tasks = ci()->cases_product_base_taskname_m->get_many_by_many(array(
            'activate_flag' => APConstants::ON_FLAG
        ));
        $map_base_taskname = array();
        foreach ($all_tasks as $task) {
            $map_base_taskname[$task->base_taskname] = $task->base_taskname;
        }

        // insert
        foreach ($milestone_datas as $milestone) {
            // Insert milestone task instance (for case verification, each milestone have only one task)
            $task = ci()->cases_taskname_m->get_by_many(array(
                "milestone_id" => $milestone->id
            ));

            // Only insert activate task
            if (!array_key_exists($task->base_task_name, $map_base_taskname)) {
                continue;
            }

            if ($task->base_task_name == 'verification_personal_identification' && $is_personal_identification) {
                continue;
            }
            if ($task->base_task_name == 'verification_company_identification_soft' && $is_company_verification) {
                continue;
            }
            if ($task->base_task_name == 'verification_company_identification_hard' && $is_company_shareholder_verification) {
                continue;
            }
            if ($task->base_task_name == 'verification_special_form_PS1583' && $is_USPS_form_1583_verify) {
                continue;
            }
            if ($task->base_task_name == 'verification_General_CMRA' && $is_general_cmra_verify) {
                continue;
            }
            if ($task->base_task_name == 'verification_california_mailbox' && $is_california_mailbox_verify) {
                continue;
            }
            // check tc contract
            if ($task->base_task_name == 'TC_contract_MS' && $is_tc_contract_verify) {
                continue;
            }
            // check company EMS
            if ($task->base_task_name == 'company_verification_E_MS' && $is_company_verification_ems_verify) {
                continue;
            }
            // check proof address
            if ($task->base_task_name == 'proof_of_address_MS' && $is_proof_address_verify) {
                continue;
            }

            $tmp = ci()->cases_milestone_instance_m->get_by_many(array(
                "case_id" => $case_id,
                "milestone_id" => $milestone->id,
                "partner_id" => $milestone->partner_id,
            ));
            if (empty($tmp)) {
                $milestone_instance_id = ci()->cases_milestone_instance_m->insert(array(
                    "case_id" => $case_id,
                    "milestone_id" => $milestone->id,
                    "partner_id" => $milestone->partner_id,
                    "status" => 0,
                    "created_date" => now()
                ));

                $tmp2 = ci()->cases_taskname_instance_m->get_by_many(array(
                    "milestone_instance_id" => $milestone_instance_id,
                    "base_task_name" => $task->base_task_name,
                    "case_id" => $case_id
                ));
                if (empty($tmp2)) {
                    ci()->cases_taskname_instance_m->insert(array(
                        "milestone_instance_id" => $milestone_instance_id,
                        "base_task_name" => $task->base_task_name,
                        "case_id" => $case_id,
                        "task_name" => $task->task_name,
                        "status" => 0,
                        "created_date" => now()
                    ));
                }
            }
        }
    }

    /**
     * Get list case by customer
     *
     * @param unknown_type $customer_id
     * @return multitype:unknown
     */
    public static function get_list_case_by_customer($customer_id)
    {
        // Get all postbox
        $list_postbox = ci()->postbox_m->get_many_by_many(array(
            'customer_id' => $customer_id,
            'deleted' => APConstants::OFF_FLAG,
            'completed_delete_flag' => APConstants::OFF_FLAG
        ));
        $list_case_number = array();
        foreach ($list_postbox as $postbox) {
            $list_case_number_by_postbox = APUtils::get_list_case($customer_id, $postbox->postbox_id);
            foreach ($list_case_number_by_postbox as $case_number) {
                if (!in_array($case_number, $list_case_number)) {
                    $list_case_number[] = $case_number;
                }
            }
        }
        return $list_case_number;
    }

    /**
     * Get list postbox id need to apply case
     *
     * @param unknown_type $customer_id
     * @return multitype:unknown
     */
    public static function get_list_postboxid_by_customer($customer_id)
    {
        // Get all postbox
        $list_postbox = ci()->postbox_m->get_many_by_many(array(
            'customer_id' => $customer_id,
            'deleted' => APConstants::OFF_FLAG,
            'completed_delete_flag' => APConstants::OFF_FLAG
        ));
        $list_postbox_id = array();
        foreach ($list_postbox as $postbox) {
            $list_case_number_by_postbox = APUtils::get_list_case($customer_id, $postbox->postbox_id);
            if (count($list_case_number_by_postbox) > 0) {
                $list_postbox_id[] = $postbox->postbox_id;
            }
        }
        return $list_postbox_id;
    }

    /**
     * Cancel verification case.
     *
     * @param unknown_type $customer_id
     */
    public static function cancel_verification_case($customer_id, $customerStatus = 0)
    {
        //$customer = APContext::getCustomerByID($customer_id);

        // update status all case is complete
        $cases_list = ci()->cases_m->get_many_by_many(array(
            'customer_id' => $customer_id,
            'product_id' => 5,
            'deleted_flag' => APConstants::OFF_FLAG
        ));

        foreach ($cases_list as $cases) {
            $case_id = $cases->id;
            // triger to table cases_milestone_instance_m and cases
            ci()->cases_taskname_instance_m->update_by_many(array(
                'case_id' => $case_id
            ), array(
                'status' => 2
            ));
            ci()->cases_milestone_instance_m->update_by_many(array(
                'case_id' => $case_id
            ), array(
                'status' => 2
            ));
            ci()->cases_verification_personal_identity_m->update_by_many(array(
                'case_id' => $case_id
            ), array(
                'status' => 2
            ));
            ci()->cases_verification_usps_m->update_by_many(array(
                'case_id' => $case_id
            ), array(
                'status' => 2
            ));
            ci()->cases_verification_company_hard_m->update_by_many(array(
                'case_id' => $case_id
            ), array(
                'status' => 2
            ));
        }

        // $case_id = $case_exist1->id;
        // ci()->cases_m->delete_by('id', $case_id);

        // Update invoice address
        if(!$customerStatus){
            ci()->customers_address_m->update_by_many(array(
                'customer_id' => $customer_id
            ), array(
                'invoice_address_verification_flag' => APConstants::ON_FLAG
            ));
            $list_postbox_id = CaseUtils::get_list_postboxid_by_customer($customer_id);
            foreach ($list_postbox_id as $case_postbox_id) {

                    ci()->postbox_m->update_by_many(array(
                        'customer_id' => $customer_id,
                        'postbox_id' => $case_postbox_id
                    ), array(
                        'name_verification_flag' => APConstants::ON_FLAG,
                        'company_verification_flag' => APConstants::ON_FLAG
                    ));
            }
        }
    }

    /**
     * Cancel verification case.
     *
     * @param unknown_type $customer_id
     */
    public static function check_case_verification_completed($customer_id)
    {
        ci()->load->model(array(
            "mailbox/postbox_m",
            "addresses/customers_address_m"
        ));
        
        // Update invoice address
        $number_cases = ci()->customers_address_m->get_by_many(array(
            'customer_id' => $customer_id,
            'invoice_address_verification_flag' => APConstants::OFF_FLAG
        ));
        if ($number_cases) {
            return false;
        }

        $number_postbox_cases = ci()->postbox_m->get_by_many(array(
            'customer_id' => $customer_id,
            'deleted' => 0,
            "( ((name_verification_flag = '0') && (name <> '')) OR ( (company_verification_flag = '0') && (company <> '')) )" => null
        ));
        if ($number_postbox_cases) {
            return false;
        }
        return true;
    }


    /**
     * Check this module enable or not
     */
    public static function is_enable_link_cases($product_id)
    {
        if (empty($product_id)) {
            return false;
        }

        // Get customer login
        $customer = APContext::getCustomerByID(APContext::getCustomerCodeLoggedIn());
        if (empty($customer)) {
            return false;
        }

        // Check exists case
        $cases_check = ci()->cases_m->get_many_by_many(array(
            'customer_id' => $customer->customer_id,
            'product_id' => $product_id,
            'deleted_flag' => APConstants::OFF_FLAG
        ));

        return !empty($cases_check);
    }

    /**
     * Check this module enable or not
     */
    public static function is_enable_link_verification()
    {
        // Get customer login
        $customer = APContext::getCustomerByID(APContext::getCustomerCodeLoggedIn());
        if (empty($customer)) {
            return false;
        }
        $customer_id = $customer->customer_id;
        $result = $customer->required_verification_flag == APConstants::ON_FLAG;
        if (!$result) {
            return $result;
        }

        // Get customer address
        $customer_address = ci()->customers_address_m->get_by_many(array(
            'customer_id' => $customer_id
        ));
        if (empty($customer_address)) {
            return false;
        }
        $result = $result && $customer_address->invoice_address_verification_flag == APConstants::ON_FLAG;

        // Check if case verification not yet completed
        $list_postbox = ci()->postbox_m->get_many_by_many(array(
            'customer_id' => $customer_id,
            'deleted' => APConstants::OFF_FLAG
        ));
        foreach ($list_postbox as $postbox) {
            $result = $result && $postbox->name_verification_flag == APConstants::ON_FLAG;
            $result = $result && $postbox->company_verification_flag == APConstants::ON_FLAG;
        }
        return $result;
    }

    /**
     * List case number config.
     */
    public static function get_list_cases_number_config()
    {
        $cases = ci()->cases_instance_m->get_many_by_many(array(
            'product_id' => '5'
        ));
        $result = array();
        foreach ($cases as $case) {
            $tmp = new stdClass();
            $tmp->id = $case->id;
            $tmp->name = $case->case_instance_name;
            $result[] = $tmp;
            unset($tmp);
        }

        return $result;
    }

    /**
     * Creating a case by customer should only be possible for cases that are triggered but not completed for this customer.
     * Do not show the other cases.
     * If no case is possible at all, there should not be a â€œcreate new caseâ€� button
     * @param unknown_type $product_id
     * @param unknown_type $customer_id
     */
    public static function isEnableCreateCase($product_id, $customer_id)
    {
        // Case verification
        if ($product_id == '5') {
            ci()->load->model('cases/cases_verification_settings_m');
            ci()->load->model('addesses/customers_address_m');
            ci()->load->model('mailbox/postbox_m');

            $customer = APContext::getCustomerByID($customer_id);
            if ($customer->required_verification_flag != APConstants::ON_FLAG) {
                return false;
            }


            // Get all postbox
            $list_postbox = ci()->postbox_m->get_many_by_many(array(
                'customer_id' => $customer_id,
                'deleted' => APConstants::OFF_FLAG,
                'completed_delete_flag' => APConstants::OFF_FLAG
            ));

            $list_case_number = APUtils::get_list_case_invoice_address($customer_id);
            if (count($list_case_number) > 0) {
                // Check if case not completed
                $customer_address = ci()->customers_address_m->get_by_many(array(
                    'customer_id' => $customer_id
                ));
                $customer_code = $customer->customer_code;
                $case_identifier_invoice = $customer_code . '_VRAD01';
                $case_invoice_check = ci()->cases_m->get_by_many(array(
                    'case_identifier' => $case_identifier_invoice,
                    'target_type' => APConstants::CASE_PRODUCT_TYPE_ADDRESS
                ));
                // Only create new case if have aleast one case did not create
                if (!empty($customer_address) && empty($case_invoice_check)) {
                    return true;
                }
            }
            foreach ($list_postbox as $postbox) {
                $list_case_number = APUtils::get_list_case($customer_id, $postbox->postbox_id);
                if (count($list_case_number) > 0) {
                    $postbox_code = $postbox->postbox_code;
                    $case_identifier_postbox = $postbox_code . '_VRPO01';
                    $case_postbox_check = ci()->cases_m->get_by_many(array(
                        'case_identifier' => $case_identifier_postbox,
                        'target_type' => APConstants::CASE_PRODUCT_TYPE_POSTBOX
                    ));
                    // Only create new case if have aleast one case did not create
                    if (empty($case_postbox_check)) {
                        return true;
                    }
                }
            }
            return false;
        }

        // Other case will implement later
        return false;
    }

    /**
     * check verification address
     * @param unknown $customer_id
     */
    public static function isVerifiedAddress($customer_id)
    {
        ci()->load->model('addesses/customers_address_m');
        $address = ci()->customers_address_m->get_by_many(array(
            'customer_id' => $customer_id
        ));

        if ($address && $address->invoice_address_verification_flag == APConstants::ON_FLAG) {
            return true;
        }

        return false;
    }

    public static function isVerifiedPostboxAddress($postbox_id, $customer_id)
    {
        ci()->load->model('mailbox/postbox_m');
        if(empty($postbox_id)){
            return true;
        }
        $postbox = ci()->postbox_m->get_by_many(array(
            //'customer_id' => $customer_id,
            'postbox_id' => $postbox_id,
            'deleted' => 0,
        ));

        if ($postbox && $postbox->name_verification_flag == APConstants::ON_FLAG
            && $postbox->company_verification_flag == APConstants::ON_FLAG
        ) {
            return true;
        }

        if ($postbox && ($postbox->name == '') && ($postbox->company_verification_flag == APConstants::ON_FLAG)) {
            return true;
        }

        if ($postbox && ($postbox->name_verification_flag == APConstants::ON_FLAG) && ($postbox->company == '')) {
            return true;
        }


        return false;
    }

    public static function getUploadFileNameBy($base_task_name, $field_in_milestone, $running_number = '001', $case_id)
    {
        $customer_id = APContext::getCustomerByCase($case_id);
        
        ci()->load->model('mailbox/postbox_m');

        ci()->postbox_m->db->select('cases.*, customers.customer_code, milestone_name');
        ci()->postbox_m->db->from('cases');
        ci()->postbox_m->db->join('cases_taskname_instance', 'cases_taskname_instance.case_id = cases.id', 'inner');
        ci()->postbox_m->db->join('cases_milestone_instance', 'cases_milestone_instance.id = cases_taskname_instance.milestone_instance_id', 'inner');
        ci()->postbox_m->db->join('cases_milestone', 'cases_milestone.id = cases_milestone_instance.milestone_id', 'inner');
        ci()->postbox_m->db->join('customers', 'customers.customer_id = cases.customer_id', 'inner');
        ci()->postbox_m->db->where('lower(cases_taskname_instance.base_task_name)', strtolower($base_task_name));
        ci()->postbox_m->db->where('cases.customer_id', $customer_id);

        $result = ci()->postbox_m->db->get()->row();
        $filename = '';
        if ($result) {
            $filename = $result->customer_code . '_' . str_replace(array('-', '  ', ' '), array('', ' ', '_'), strtolower($result->milestone_name)) . '_' . $field_in_milestone . '_' . $running_number;
        }
        return $filename;
    }

    public static function getRunningNumberBy($filename)
    {
        $tmp = explode('.', $filename);
        $tmp2 = explode('_', $tmp[0]);

        // get current running number.
        $current_running = intval($tmp2[count($tmp2) - 1]) + 1;

        $result = substr('000' . $current_running, -3);

        return $result;
    }

    public static function getBeforeRunningNumberBy($filename)
    {
        $tmp = explode('.', $filename);
        $tmp2 = explode('_', $tmp[0]);

        // get current running number.
        $current_running = intval($tmp2[count($tmp2) - 1]) - 1;

        $result = substr('000' . $current_running, -3);

        return $result;
    }

    /**
     * xoa cac case khong con can phai verify nua.
     *
     * @param unknown $customer_id
     * @param unknown $list_base_task_name
     * @param unknown $postbox_id
     */
    public static function deleteUnusedCaseBy($customer_id, $list_base_task_name, $postbox_id, $case_id, $list_case_number_by_customer, $type = '')
    {
        // determine target type of case.
        $target_type = $type;
        if(empty($target_type)){
            if(empty($postbox_id)){
                $target_type = APConstants::CASE_PRODUCT_TYPE_ADDRESS;
            }else{
                $target_type = APConstants::CASE_PRODUCT_TYPE_POSTBOX;
            }
        }
            
        // get list unused cases.
        // delete logic cases: invoice address verification
        if (empty ($list_base_task_name) || count($list_base_task_name) == 0) {
            // determine the target type
            ci()->cases_m->update_by_many(array(
                "customer_id" => $customer_id,
                "postbox_id" => $postbox_id,
                "target_type" => $target_type
            ), array(
                'deleted_flag' => APConstants::ON_FLAG
            ));
            return;
        } else {
            // delete logic cases: postbox address 
            $instance_list_id = ci()->cases_instance_m->get_list_milestone_id($list_case_number_by_customer);
            $milestones = ci()->cases_milestone_m->get_many_by_many(array(
                "id IN ('" . implode("','", $instance_list_id) . "')" => null
            ));
            $list_milestone_ids = array();
            foreach($milestones as $el){
                $list_milestone_ids[] = $el->id;
            }
            
            if(count($list_milestone_ids) > 0){
                $milestone_instances = ci()->cases_milestone_instance_m->get_many_by_many(array(
                    "milestone_id NOT IN ('".implode("','", $list_milestone_ids)."')" => null,
                    "case_id" => $case_id
                ));
                
                if($milestone_instances){
                    foreach($milestone_instances as $instance){
                        ci()->cases_taskname_instance_m->delete_by_many(array(
                            "milestone_instance_id" => $instance->id,
                            "case_id" => $case_id
                        ));
                        
                        ci()->cases_milestone_instance_m->delete_by_many(array(
                            "id" => $instance->id,
                            "case_id" => $case_id
                        ));
                    }
                }
            }
        }
    }

    /**
     * Gets milestone name by base taskname.
     *
     * @param unknown $case_id
     * @param unknown $base_taskname
     * @return string
     */
    public static function get_milestone_name($case_id, $base_taskname)
    {
        ci()->load->model(array(
            "cases_milestone_m"
        ));

        $milestone_name = "";
        if (empty($case_id) || empty($base_taskname)) {
            return $milestone_name;
        }
        ci()->cases_milestone_m->db->select("cases_milestone.milestone_name");
        ci()->cases_milestone_m->db->from("cases_taskname_instance");
        ci()->cases_milestone_m->db->join("cases_milestone_instance", "cases_taskname_instance.milestone_instance_id = cases_milestone_instance.id");
        ci()->cases_milestone_m->db->join("cases_milestone", "cases_milestone_instance.milestone_id = cases_milestone.id");
        ci()->cases_milestone_m->db->where("cases_taskname_instance.case_id", $case_id);
        ci()->cases_milestone_m->db->where("cases_taskname_instance.base_task_name", $base_taskname);
        $temp = ci()->cases_milestone_m->db->get()->row();

        if (!empty($temp)) {
            $milestone_name = $temp->milestone_name;
        }
        return $milestone_name;
    }

    /**
     * Gets case name by base taskname.
     *
     * @param unknown $case_id
     * @param unknown $base_taskname
     * @return string
     */
    public static function get_case_name($case_id, $base_taskname)
    {
        ci()->load->model("cases_milestone_m");

        if (empty($case_id) || empty($base_taskname)) {
            return "";
        }

        ci()->cases_milestone_m->db->select("cases_milestone.milestone_name, cases_milestone.id");
        ci()->cases_milestone_m->db->from("cases_taskname_instance");
        ci()->cases_milestone_m->db->join("cases_milestone_instance", "cases_taskname_instance.milestone_instance_id = cases_milestone_instance.id");
        ci()->cases_milestone_m->db->join("cases_milestone", "cases_milestone_instance.milestone_id = cases_milestone.id");
        ci()->cases_milestone_m->db->where("cases_taskname_instance.case_id", $case_id);
        ci()->cases_milestone_m->db->where("cases_taskname_instance.base_task_name", $base_taskname);
        $temp = ci()->cases_milestone_m->db->get()->row();

        if (!empty($temp)) {
            ci()->cases_milestone_m->db->select("cases_instance.*");
            ci()->cases_milestone_m->db->from("cases_instance");
            ci()->cases_milestone_m->db->where("cases_instance.list_milestone_id", $temp->id);
            $result = ci()->cases_milestone_m->db->get()->row();

            if ($result) {
                return $result->case_instance_name;
            }
        }

        return "";
    }

    /**
     * get comment content of case.
     * @param unknown $case_id
     * @param unknown $base_taskname
     */
    public static function getCommentOfCase($caseId, $baseTaskname)
    {
        ci()->load->library("cases/cases_api");

        $comment = cases_api::getCommentOfCaseBy($caseId, $baseTaskname);

        return $comment;
    }
    
    /**
     * get comment date of case.
     * @param unknown $case_id
     * @param unknown $base_taskname
     */
    public static function getCommentDateOfCase($caseId, $baseTaskname)
    {
    	ci()->load->library("cases/cases_api");
    
    	$date = cases_api::getCommentDateOfCaseBy($caseId, $baseTaskname);
    
    	return $date;
    }

    private static function checkValidCases(&$is_personal_identification, &$is_company_verification
            , &$is_company_shareholder_verification, &$is_USPS_form_1583_verify, &$is_general_cmra_verify
            , &$is_proof_address_verify, &$is_company_verification_ems_verify, &$is_tc_contract_verify,&$is_california_mailbox_verify, $list_base_task_name)
    {
        // Case 1
        if (in_array('verification_personal_identification', $list_base_task_name)) {
            $is_personal_identification = false;
        }

        // Case 2
        if (in_array('verification_company_identification_soft', $list_base_task_name)) {
            $is_company_verification = false;
        }

        // Case 3
        if (in_array('verification_company_identification_hard', $list_base_task_name)) {
            $is_company_shareholder_verification = false;
        }

        // Case 4
        if (in_array('verification_special_form_PS1583', $list_base_task_name)) {
            $is_USPS_form_1583_verify = false;
        }

        // Case 5
        if (in_array('verification_General_CMRA', $list_base_task_name)) {
            $is_general_cmra_verify = false;
        }
        
        // Case 6
        if (in_array('proof_of_address_MS', $list_base_task_name)) {
            $is_proof_address_verify = false;
        }
        
        // Case 7
        if (in_array('company_verification_E_MS', $list_base_task_name)) {
            $is_company_verification_ems_verify = false;
        }
        
        // Case 7
        if (in_array('TC_contract_MS', $list_base_task_name)) {
            $is_tc_contract_verify = false;
        }
        
        // Case 8
        if (in_array('verification_california_mailbox', $list_base_task_name)) {
            $is_california_mailbox_verify = false;
        }
    }

    private static function resetCaseByAdmin($case_id_01)
    {
        if(empty($case_id_01)){
            return;
        }
        
        // start transaction.
        ci()->cases_m->db->trans_begin();
        
        // triger to table cases_milestone_instance_m and cases
        ci()->cases_taskname_instance_m->update_by_many(array(
            'case_id' => $case_id_01
        ), array(
            'status' => 0
        ));
        ci()->cases_milestone_instance_m->update_by_many(array(
            'case_id' => $case_id_01
        ), array(
            'status' => 0
        ));
        ci()->cases_verification_personal_identity_m->update_by_many(array(
            'case_id' => $case_id_01
        ), array(
            'status' => 0,
            "comment_content" => "",
            "deleted_flag" => APConstants::OFF_FLAG
        ));
        ci()->cases_verification_usps_m->update_by_many(array(
            'case_id' => $case_id_01
        ), array(
            'status' => 0,
            "comment_content" => "",
            "deleted_flag" => APConstants::OFF_FLAG
        ));
        ci()->cases_verification_company_hard_m->update_by_many(array(
            'case_id' => $case_id_01
        ), array(
            'status' => 0,
            "comment_content" => "",
            "deleted_flag" => APConstants::OFF_FLAG
        ));
        // reset new case.
        ci()->cases_contract_m->update_by_many(array(
            'case_id' => $case_id_01
        ), array(
            'status' => 0,
            "comment_content" => "",
            "deleted_flag" => APConstants::OFF_FLAG
        ));
        ci()->cases_proof_business_m->update_by_many(array(
            'case_id' => $case_id_01
        ), array(
            'status' => 0,
            "comment_content" => "",
            "deleted_flag" => APConstants::OFF_FLAG
        ));
        ci()->cases_company_ems_m->update_by_many(array(
            'case_id' => $case_id_01
        ), array(
            'status' => 0,
            "comment_content" => "",
            "deleted_flag" => APConstants::OFF_FLAG
        ));
        ci()->cases_m->update_by_many(array(
            'id' => $case_id_01
        ), array(
            'status' => 0,
        ));
        
        // commit transaction
        if(ci()->cases_m->db->trans_status() == FALSE){
            ci()->cases_m->db->trans_rollback();
        }else{
            ci()->cases_m->db->trans_commit();
        }
    }

    private static function checkCaseStatus($case_id_01, &$is_personal_identification, &$is_personal_status, &$is_company_verification
            , &$is_company_status, &$is_company_shareholder_verification, &$is_company_hard_status, &$is_USPS_form_1583_verify
            , &$is_general_cmra_verify, &$is_proof_address_verify , &$is_company_verification_ems_verify, &$is_tc_contract_verify
            , &$is_USPS_form_1583_status, &$is_general_cmra_status, &$is_proof_address_status, &$is_company_verification_ems_status
            , &$is_tc_contract_status, &$is_california_mailbox_verify, &$is_california_mailbox_status)
    {
        // check personal verification.
        if(!$is_personal_identification){
            $cases_verification_personal_identity = ci()->cases_verification_personal_identity_m->get_by_many(array(
                "case_id" => $case_id_01,
                "type" => 1
            ));
            if (!empty($cases_verification_personal_identity) && ($cases_verification_personal_identity->status == "2")) {
                $is_personal_identification = true;
            }
            if ($cases_verification_personal_identity) {
                $is_personal_status = $cases_verification_personal_identity->status;
            }
        }

        // check company soft
        if(!$is_company_verification){
            $cases_verification_company_identity = ci()->cases_verification_personal_identity_m->get_by_many(array(
                "case_id" => $case_id_01,
                "type" => 2
            ));
            if (!empty($cases_verification_company_identity) && ($cases_verification_company_identity->status == "2")) {
                $is_company_verification = true;
            }
            if ($cases_verification_company_identity) {
                $is_company_status = $cases_verification_company_identity->status;
            }
        }

        // check company hard
        if(!$is_company_shareholder_verification){
            $cases_verification_company_hard = ci()->cases_verification_company_hard_m->get_by_many(array(
                "case_id" => $case_id_01
            ));
            if (!empty($cases_verification_company_hard) && ($cases_verification_company_hard->status == "2")) {
                $is_company_shareholder_verification = true;
            }
            if ($cases_verification_company_hard) {
                $is_company_hard_status = $cases_verification_company_hard->status;
            }
        }

        // Check USPS 1583 form
        if(!$is_USPS_form_1583_verify || !$is_general_cmra_verify ||!$is_california_mailbox_verify){
            $cases_verification_usps = ci()->cases_verification_usps_m->get_by_many(array(
                "case_id" => $case_id_01
            ));
            if (!empty($cases_verification_usps) && ($cases_verification_usps->status == "2")) {
                $is_USPS_form_1583_verify = true;
                $is_general_cmra_verify = true;
                $is_california_mailbox_verify = true;
            }
            if ($cases_verification_usps) {
                $is_USPS_form_1583_status = $cases_verification_usps->status;
                $is_general_cmra_status = $cases_verification_usps->status;
                $is_california_mailbox_status = $cases_verification_usps->status;
            }
        }
        
        // Check tc contract
        if(!$is_tc_contract_verify){
            $case_contract = ci()->cases_contract_m->get_by("case_id", $case_id_01);
            if(!empty($case_contract) && $case_contract->status == APConstants::CASE_COMPLETED_STATUS){
                $is_tc_contract_verify = true;
            }
            if($case_contract){
                $is_tc_contract_status = $case_contract->status;
            }
        }
        
        // check proof address
        if(!$is_proof_address_verify){
            $case_check = ci()->cases_proof_business_m->get_by("case_id", $case_id_01);
            if($case_check && $case_check->status == APConstants::CASE_COMPLETED_STATUS){
                $is_proof_address_verify = true;
            }
            if($case_check){
                $is_proof_address_status = $case_check->status;
            }
        }
        
        // check company EMS
        if(!$is_company_verification_ems_verify){
            $case_check = ci()->cases_company_ems_m->get_by("case_id", $case_id_01);
            if($case_check && $case_check->status == APConstants::CASE_COMPLETED_STATUS){
                $is_company_verification_ems_verify = true;
            }
            if($case_check){
                $is_company_verification_ems_status = $case_check->status;
            }
        }
    }
    
    private static function resetPersonalCase($case_id_01, $milestone_instance_ids)
    {
        // start transaction.
        ci()->cases_m->db->trans_begin();
        
        if($milestone_instance_ids){
            // triger to table cases_milestone_instance_m and cases
            ci()->cases_taskname_instance_m->update_by_many(array(
                'case_id' => $case_id_01,
                "milestone_instance_id IN (".implode(',', $milestone_instance_ids).")" => null
            ), array(
                'status' => 0
            ));
            ci()->cases_milestone_instance_m->update_by_many(array(
                'case_id' => $case_id_01,
                "id IN (".implode(',', $milestone_instance_ids).")" => null
            ), array(
                'status' => 0
            ));
        }
        ci()->cases_verification_personal_identity_m->update_by_many(array(
            'case_id' => $case_id_01,
            "type" => 1
        ), array(
            'status' => 0,
            "comment_content" => "",
            "deleted_flag" => APConstants::OFF_FLAG,
        ));
        ci()->cases_verification_usps_m->update_by_many(array(
            'case_id' => $case_id_01
        ), array(
            'status' => 0,
            "comment_content" => "",
            "comment_date" => null,
            "deleted_flag" => APConstants::OFF_FLAG
        ));
        
        ci()->cases_contract_m->update_by_many(array(
            'case_id' => $case_id_01
        ), array(
            'status' => 0,
            "comment_content" => "",
            "comment_date" => null,
            "deleted_flag" => APConstants::OFF_FLAG
        ));
        ci()->cases_m->update_by_many(array(
            'id' => $case_id_01
        ), array(
            'status' => 1,
        ));
        
        // commit transaction
        if(ci()->cases_m->db->trans_status() == FALSE){
            ci()->cases_m->db->trans_rollback();
        }else{
            ci()->cases_m->db->trans_commit();
        }
    }
    
    private static function resetCompanyCase($case_id_01, $milestone_instance_ids)
    {
        // start transaction.
        ci()->cases_m->db->trans_begin();
        
        // triger to table cases_milestone_instance_m and cases
        ci()->cases_taskname_instance_m->update_by_many(array(
            'case_id' => $case_id_01,
            "milestone_instance_id IN (".implode(',', $milestone_instance_ids).")" => null
        ), array(
            'status' => 0
        ));
        ci()->cases_milestone_instance_m->update_by_many(array(
            'case_id' => $case_id_01,
            "id IN (".implode(',', $milestone_instance_ids).")" => null
        ), array(
            'status' => 0
        ));
        ci()->cases_verification_personal_identity_m->update_by_many(array(
            'case_id' => $case_id_01,
            "type" => 2
        ), array(
            'status' => 0,
            "comment_content" => "",
            "comment_date" => null,
            "deleted_flag" => APConstants::OFF_FLAG,
        ));
        ci()->cases_verification_usps_m->update_by_many(array(
            'case_id' => $case_id_01
        ), array(
            'status' => 0,
            "comment_content" => "",
            "comment_date" => null,
            "deleted_flag" => APConstants::OFF_FLAG
        ));
        ci()->cases_verification_company_hard_m->update_by_many(array(
            'case_id' => $case_id_01
        ), array(
            'status' => 0,
            "comment_content" => "",
            "comment_date" => null,
            "deleted_flag" => APConstants::OFF_FLAG
        ));
        
        ci()->cases_contract_m->update_by_many(array(
            'case_id' => $case_id_01
        ), array(
            'status' => 0,
            "comment_content" => "",
            "comment_date" => null,
            "deleted_flag" => APConstants::OFF_FLAG
        ));
        
        ci()->cases_company_ems_m->update_by_many(array(
            'case_id' => $case_id_01
        ), array(
            'status' => 0,
            "comment_content" => "",
            "deleted_flag" => APConstants::OFF_FLAG
        ));
        ci()->cases_m->update_by_many(array(
            'id' => $case_id_01
        ), array(
            'status' => 1,
        ));
        
        // commit transaction
        if(ci()->cases_m->db->trans_status() == FALSE){
            ci()->cases_m->db->trans_rollback();
        }else{
            ci()->cases_m->db->trans_commit();
        }
    }
    
    public static function get_base_task_name($postbox_id){
        
        ci()->db->select("ct.base_task_name, cases_milestone.milestone_name, ct.case_id, cm.status");
        ci()->db->from("cases");
        ci()->db->join('cases_taskname_instance ct', 'ct.case_id = cases.id', 'inner');
        ci()->db->join('cases_milestone_instance cm', 'cm.id = ct.milestone_instance_id', 'inner');
        ci()->db->join('cases_milestone', 'cases_milestone.id = cm.milestone_id', 'inner');
        ci()->db->where("cases.postbox_id", $postbox_id);
        $list_base_task_name = ci()->db->get()->result();
        return $list_base_task_name;
    }
    
    public static function get_list_customer_verification_report($location_id,$start_date, $end_date){
        //,p.postbox_id, p.name_verification_flag,p.company_verification_flag,cases.status
        ci()->db->select("cases.customer_id")->distinct();
        
        ci()->db->from("cases");
        ci()->db->join('postbox p', 'p.postbox_id = cases.postbox_id', 'inner');
        ci()->db->join('customers c', 'c.customer_id = cases.customer_id', 'inner');
        
        ci()->db->where("cases.status", 2);
        ci()->db->where('UNIX_TIMESTAMP(cases.modified_date) >=', $start_date);
        ci()->db->where('UNIX_TIMESTAMP(cases.modified_date) <=', $end_date);
        ci()->db->where("p.location_available_id", $location_id);
        ci()->db->where("p.name_verification_flag", APConstants::ON_FLAG);
        ci()->db->where("p.company_verification_flag", APConstants::ON_FLAG);
        ci()->db->where("c.status <> 1", null);
        ci()->db->order_by('cases.modified_date','DESC');
        $list_customer_report = ci()->db->get()->result();
        
        return $list_customer_report;
    }
    
    public static function addCaseVerificationHistory($case_id, $base_task_name, $activity_type, $activity_content){
        $data = array (
            'case_id' => $case_id,
            'base_task_name' => $base_task_name,
            'activity_type' => $activity_type,
            'activity_content' => $activity_content,
            'activity_by' => APContext::getAdminIdLoggedIn(),
            'activity_date' => now()
        );
        
        ci()->cases_verification_history_m->insert($data);
    }
    
    public static function getCaseVerificationHistory($case_id, $base_task_name){
        $verification_history = $this->cases_verification_history_m->get_many_by_many(array(
            "case_id" => $case_id,
            "base_task_name" => "company_information"
        ), 'activity_type, activity_content, activity_date', true, array('activity_date' => 'DESC'));
        
        
    }


    public static function convertCaseVerificationToString($status){
        $result = '';
        switch ($status) {
            case 0:
                $result = 'Created';
                break;
            case 1:
                $result = 'Incomplete';
                break;
            case 2:
                $result = 'Completed';
                break;
        }
        return $result;
    }
    
    /**
     * start case of phone verification.
     * 
     * @param type $by_admin
     * @param type $customer
     * @param type $phone
     * @return type
     */
    public static function start_case_verification_by_phone($by_admin, $customer, $phone){
        // validate
        if ($customer->required_verification_flag != APConstants::ON_FLAG || empty($phone)) {
            return null;
        }

        // declare param
        $customer_id = $customer->customer_id;
        $phone_id = $phone->id;
        $phone_code= $phone->phone_code;
        $target_type = APConstants::CASE_PRODUCT_TYPE_PHONE;
        
        $is_company_verification = true;
        $is_personal_verification = true;
        
        $is_company_status = '';
        $is_personal_status = '';
        

        // 1. gets list case setting.
        $list_case_number = self::get_list_case_phonenumber($customer_id, $phone_id);
        $list_case_number_by_customer = array();
        foreach ($list_case_number as $case_number) {
            if (!in_array($case_number, $list_case_number_by_customer)) {
                $list_case_number_by_customer[] = $case_number;
            }
        }
        log_audit_message(APConstants::LOG_INFOR, "List phone case of customer:" . $customer_id . ', phone_id:' . $phone_id . '==>' . json_encode($list_case_number_by_customer));
        if (count($list_case_number_by_customer) == 0) {
            // xoa tat ca cac cases
            CaseUtils::deleteUnusedCaseBy($customer_id, '', '', '', $list_case_number_by_customer, APConstants::CASE_PRODUCT_TYPE_PHONE);
            return null;
        }
        // 2.Get list base task name
        $list_base_task_name = ci()->cases_instance_m->get_base_task_name($list_case_number_by_customer);
        
        // check valid case
        // Case 1
        if (in_array('phone_number_company', $list_base_task_name)) {
            $is_company_verification = false;
        }

        // Case 2
        if (in_array('phone_number_for_personal', $list_base_task_name)) {
            $is_personal_verification = false;
        }
        
        // 3.Gets case identifier
        $case_identifier = $phone_code . '_VRPH01';
        $group_name = "Verify phone " . end(explode('_', $phone_code)) . ' - '. $customer->customer_code;
        
        $case_exist_01 = ci()->cases_m->get_by_many(array(
            'case_identifier' => $case_identifier,
            'deleted_flag' => APConstants::OFF_FLAG,
            'target_type' => $target_type
        ));
        $case_id_01 = $case_exist_01 != null ? $case_exist_01->id : 0;
        // Reset case status if admin request.
        if ($by_admin) {
            CaseUtils::resetPhoneCaseByAdmin($case_id_01);
        }
        
        // 4.check case status
        // - company verification.
        if(!$is_company_verification){
            $cases_verification = ci()->case_phone_number_m->get_by_many(array(
                "case_id" => $case_id_01,
                "type" => 2
            ));
            
            if(!empty($cases_verification)){
                $is_company_status = $cases_verification->status;
                
                if($cases_verification->status == "2"){
                    $is_company_verification = true;
                }
            }
        }

        // - personal case
        if(!$is_personal_verification){
            $cases_verification = ci()->case_phone_number_m->get_by_many(array(
                "case_id" => $case_id_01,
                "type" => 1
            ));
            
            if(!empty($cases_verification)){
                $is_personal_status = $cases_verification->status;
                
                if($cases_verification->status == "2"){
                    $is_personal_verification = true;
                }
            }
        }
        
        // All verify process completed
        if ($is_personal_verification && $is_company_verification) {
            // delete unuse case.
            CaseUtils::deleteUnusedCaseBy($customer_id, $list_base_task_name, '', $case_id_01, $list_case_number_by_customer, $target_type);
            return null;
        }
        
        // 5. Check exist this case
        //$case_exist = ci()->cases_m->get_by_many(array(
        //    'case_identifier' => $case_identifier
        //));

        // update case information.
        $product_id = '5';
        if (empty($case_exist_01)) {
            $country = ci()->countries_m->get_by_many(array(
                'country_code_3' => $phone->country_code
            ));
            $case_country_id = $country->id;

            // Insert new case
            $data = array(
                "customer_id" => $customer_id,
                "opening_date" => now(),
                "case_identifier" => $case_identifier,
                "product_id" => $product_id,
                "country" => $case_country_id,
                "status" => '0',
                "target_type" => $target_type,
                "target_id" => $phone_id,
                "description" => 'Case phone Verification',
                "created_date" => now()
            );

            $case_id = ci()->cases_m->insert($data);
        } else {
            $case_id = $case_exist_01->id;
            ci()->cases_m->update_by_many(array(
                'id' => $case_id
            ), array(
                'deleted_flag' => APConstants::OFF_FLAG
            ));
        }

        // 6. insert milestone and taskname
        $array_condition = array();
        $array_condition['cases_milestone.product_id'] = $product_id;
        $milestone_list_id = ci()->cases_instance_m->get_list_milestone_id($list_case_number_by_customer);
        $array_condition["cases_milestone.id IN ('" . implode("','", $milestone_list_id) . "')"] = null;
        $query_result = ci()->cases_milestone_m->get_milestone_paging($array_condition, 0, 1000, null, null);
        // Process output data
        $milestone_datas = $query_result['data'];

        // Get all base task name
        $all_tasks = ci()->cases_product_base_taskname_m->get_many_by_many(array(
            'activate_flag' => APConstants::ON_FLAG
        ));
        $map_base_taskname = array();
        foreach ($all_tasks as $task) {
            $map_base_taskname[$task->base_taskname] = $task->base_taskname;
        }

        // insert
        foreach ($milestone_datas as $milestone) {
            // Insert milestone task instance (for case verification, each milestone have only one task)
            $task = ci()->cases_taskname_m->get_by_many(array(
                "milestone_id" => $milestone->id
            ));

            // Only insert activate task
            if (!array_key_exists($task->base_task_name, $map_base_taskname)) {
                continue;
            }
            
            if ($task->base_task_name == 'phone_number_for_personal' && $is_personal_verification) {
                continue;
            }
            if ($task->base_task_name == 'phone_number_company' && $is_company_verification) {
                continue;
            }

            $tmp = ci()->cases_milestone_instance_m->get_by_many(array(
                "case_id" => $case_id,
                "milestone_id" => $milestone->id,
                "partner_id" => $milestone->partner_id,
            ));
            if (empty($tmp)) {
                $milestone_instance_id = ci()->cases_milestone_instance_m->insert(array(
                    "case_id" => $case_id,
                    "milestone_id" => $milestone->id,
                    "partner_id" => $milestone->partner_id,
                    "status" => 0,
                    "created_date" => now()
                ));
            }else{
                $milestone_instance_id = $tmp->id;
            }
            
            $tmp2 = ci()->cases_taskname_instance_m->get_by_many(array(
                "milestone_instance_id" => $milestone_instance_id,
                "base_task_name" => $task->base_task_name,
                "case_id" => $case_id
            ));
            if (empty($tmp2)) {
                ci()->cases_taskname_instance_m->insert(array(
                    "milestone_instance_id" => $milestone_instance_id,
                    "base_task_name" => $task->base_task_name,
                    "case_id" => $case_id,
                    "task_name" => $task->task_name,
                    "status" => 0,
                    "created_date" => now()
                ));
            }
        }
        
        // 7. update phone status verification
        ci()->phone_number_m->update_by_many(array(
            'id' => $phone_id
        ), array(
            'is_verification_flag' => APConstants::OFF_FLAG
        ));
        
        // 8. xoa case khong can check.
        CaseUtils::deleteUnusedCaseBy($customer_id, $list_base_task_name, '', $case_id, $list_case_number_by_customer, $target_type);
        
        // 9. return result
        return array(
            'is_personal_phone_identification' => $is_personal_verification,
            'is_company_phone_verification' => $is_company_verification,
            'group_name' => $group_name,
            'case_id' => $case_id,
            'is_personal_phone_status' => $is_personal_status,
            'is_company_phone_status' => $is_company_status,
            
            // optional
            'is_personal_identification' => true,
            'is_company_verification' => true,
            'is_company_shareholder_verification' => true,
            'is_USPS_form_1583_verify' => true,
            'is_general_cmra_verify' => true,
            "is_company_verification_ems_verify" => true,
            "is_proof_address_verify" => true,
            "is_tc_contract_verify" => true,
            'is_personal_status' => '',
            'is_company_status' => '',
            'is_company_hard_status' => '',
            'is_USPS_form_1583_status' => '',
            'is_general_cmra_status' => '',
            'is_company_verification_ems_status' => '',
            'is_proof_address_status' => '',
            'is_tc_contract_status' => '',
            'is_california_mailbox_verify' => true,
            'is_california_mailbox_status' => ''
        );
    }
    
    /**
     * Get list case need to apply for one customer.
     *
     * @param unknown_type $customer_id
     * @return the list of case should be apply for this customer.
     *         (0: That mean need to apply verification for invoice address
     *         case)
     */
    public static function get_list_case_phonenumber($customer_id, $phone_id) {
        ci()->load->model('cases/cases_verification_settings_m');

        // Get all cases setting
        $list_cases_settings = ci()->cases_verification_settings_m->get_many_by_many( array(
            'setting_type' => '3'
        ));

        // get phone
        $phone = ci()->phone_number_m->get($phone_id);        
        
        // Get customer information
        $customer = APContext::getCustomerByID($customer_id);
        if (empty($phone) || empty($customer)) {
            return array();
        }
        
        // get country by country_code3
        $country = ci()->countries_m->get_by_many(array(
            "country_code_3" => $phone->country_code
        ));
        if (empty($country) ) {
            return array();
        }

        // Condition 1
        $country_code= $country->country_code;

        // Condition 2
        $is_user_company = ($customer->customer_type == APConstants::CUSTOMER_TYPE_COMPANY)? true : false ;

        $array_result = array();
        // For each condition is settings table
        foreach ($list_cases_settings as $case_setting) {
            $case_country = $case_setting->country_code;
            $case_user_company = $case_setting->is_user_company;
            
            $list_case_number = $case_setting->list_case_number;
            if (empty($list_case_number)) {
                continue;
            }
            
            // 1. check country
            $condition1 = empty($case_country) || (!empty($case_country) && $case_country == $country_code) ;

            // 2. check user company
            $condition2 = ($case_user_company == 0) || (($case_user_company == 2 && $is_user_company) || ($case_user_company == 1 && !$is_user_company));

            $list_case_number_arr = explode(',', $list_case_number);
            if( $condition1 && $condition2){
                // Apply for this case
                foreach ($list_case_number_arr as $case_number) {
                    if (!in_array($case_number, $array_result)) {
                        $array_result[] = $case_number;
                    }
                }
            }
        }

        return $array_result;
    }
    
    /**
     * Reset phone case verification by admin.
     * @param type $case_id_01
     */
    private static function resetPhoneCaseByAdmin($case_id_01)
    {
        if(empty($case_id_01)){
            return;
        }
        // start transaction.
        ci()->cases_m->db->trans_begin();
        
        // triger to table cases_milestone_instance_m and cases
        ci()->cases_taskname_instance_m->update_by_many(array(
            'case_id' => $case_id_01
        ), array(
            'status' => 0
        ));
        ci()->cases_milestone_instance_m->update_by_many(array(
            'case_id' => $case_id_01
        ), array(
            'status' => 0
        ));
        ci()->case_phone_number_m->update_by_many(array(
            'case_id' => $case_id_01
        ), array(
            'status' => 0,
            "comment_content" => "",
            "deleted_flag" => APConstants::OFF_FLAG
        ));
        ci()->cases_m->update_by_many(array(
            'id' => $case_id_01
        ), array(
            'status' => 0,
        ));
        
        // commit transaction
        if(ci()->cases_m->db->trans_status() == FALSE){
            ci()->cases_m->db->trans_rollback();
        }else{
            ci()->cases_m->db->trans_commit();
        }
    }
}