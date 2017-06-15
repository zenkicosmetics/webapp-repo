<?php defined('BASEPATH') or exit('No direct script access allowed');

class cases_milestone_instance_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->_table = $this->profile_table = $this->db->dbprefix('cases_milestone_instance');
        $this->primary_key = 'id';
    }

    /**
     * Get all paging data
     *
     * @param unknown_type $array_where
     *            The array of condition (array ('name' => 'DungNT', 'age' => 30))
     * @param unknown_type $start
     *            The offset paging
     * @param unknown_type $limit
     *            The number of record per page
     * @param unknown_type $sort_column
     *            The sort column
     * @param unknown_type $sort_type
     *            The sort type
     * @return The array object array('total' => '9999', 'data' => '');
     */
    public function get_milestone_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC')
    {
        // Count all record with input condition
        $total_record = $this->count_by_milestone_paging($array_where);
        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }
        $this->db->select('cases_milestone_instance.*')->distinct();
        $this->db->select('cases_service_partner.partner_name, cases_service_partner.main_contact_point, cases_service_partner.email, cases_service_partner.phone');
        $this->db->select('cases_milestone.product_id, cases_milestone.milestone_name');
        $this->db->select('cases_taskname_instance.base_task_name');
        $this->db->select('customers.user_name as c_name, customers.email as c_email');
        $this->db->select('users.username as last_confirmed_by');

        $this->db->join("cases_taskname_instance", "cases_taskname_instance.milestone_instance_id = cases_milestone_instance.id and cases_taskname_instance.case_id = cases_milestone_instance.case_id", "inner");
        $this->db->join("cases_milestone", "cases_milestone_instance.milestone_id = cases_milestone.id", "inner");
        $this->db->join('cases_product', 'cases_product.id = cases_milestone.product_id', 'inner');
        $this->db->join('cases_service_partner', 'cases_service_partner.partner_id = cases_milestone_instance.partner_id', 'inner');
        $this->db->join('cases', 'cases_milestone_instance.case_id = cases.id', 'left');
        $this->db->join('customers', 'cases.customer_id = customers.customer_id', 'left');
        $this->db->join('users', 'users.id = cases_milestone_instance.updated_by', 'left');
        $this->db->where('cases.deleted_flag', APConstants::OFF_FLAG);

        foreach ($array_where as $key => $value) {
            if (stripos($key, ".") < 1) {
                $key = "cases_milestone_instance." . $key;
            }
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
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
     * Count customer
     *
     * @param unknown_type $array_where
     */
    public function count_by_milestone_paging($array_where)
    {
        $this->db->select('COUNT(DISTINCT(cases_milestone_instance.id)) AS total_record');
        $this->db->from('cases_milestone_instance');
        $this->db->join("cases_taskname_instance", "cases_taskname_instance.milestone_instance_id = cases_milestone_instance.id and cases_taskname_instance.case_id = cases_milestone_instance.case_id", "inner");
        $this->db->join("cases_milestone", "cases_milestone_instance.milestone_id = cases_milestone.id", "inner");
        $this->db->join('cases_product', 'cases_product.id = cases_milestone.product_id', 'inner');
        $this->db->join('cases_service_partner', 'cases_service_partner.partner_id = cases_milestone_instance.partner_id', 'inner');

        foreach ($array_where as $key => $value) {
            if (stripos($key, ".") < 1) {
                $key = "cases_milestone_instance." . $key;
            }
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }
        $result = $this->db->get()->row();
        return $result->total_record;
    }

    /**
     * get_partner_list.
     */
    public function get_partner_list()
    {
        $query = "SELECT Sub1.case_id,cases_service_partner.*
                FROM
                    (SELECT A.case_id,B.partner_id
                    FROM cases_milestone_instance A
                    LEFT JOIN (
                            SELECT case_id, partner_id, COUNT(case_id) c
                            FROM (SELECT DISTINCT case_id,partner_id
                                FROM cases_milestone_instance ) sub
                            GROUP BY case_id
                            HAVING c=1
                    ) B on A.case_id=B.case_id
                    GROUP BY case_id ,partner_id) Sub1
                LEFT JOIN cases_service_partner ON Sub1.partner_id=cases_service_partner.partner_id";
        return $this->db->query($query)->result();
    }

    /**
     * get_check_list.
     *
     * @param unknown $case_id
     */
    public function get_check_list_paging($case_id, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC')
    {
        // Count all record with input condition
        $total_record = $this->count_check_list_paging($case_id);
        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }
        $query = "(SELECT DISTINCT cp.id,cmi.case_id,cm.milestone_name,cti.base_task_name,
                    IFNULL(CASE cti.base_task_name
                        WHEN 'personal_identify' then cpi.status
                        WHEN 'company_information' then cci.status
                        WHEN 'document_of_company_registration' then crd.status
                        WHEN 'verification_personal_identification' then cvpi.status
                        WHEN 'verification_company_identification_soft' then cvcs.status
                        WHEN 'verification_company_identification_hard' then cvch.status
                        WHEN 'verification_special_form_PS1583' then cvu.status
                        WHEN 'verification_General_CMRA' then cvu.status
                        WHEN 'verification_california_mailbox' then cvu.status
                        WHEN 'TC_contract_MS' then cc.status
                        WHEN 'proof_of_address_MS' then cpb.status
                        WHEN 'company_verification_E_MS' then cce.status
                    END,0) as status,
                    IFNULL(CASE cti.base_task_name
                        WHEN 'personal_identify' then cpi.comment_content
                        WHEN 'company_information' then cci.comment_content
                        WHEN 'document_of_company_registration' then crd.comment_content
                        WHEN 'verification_personal_identification' then cvpi.comment_content
                        WHEN 'verification_company_identification_soft' then cvcs.comment_content
                        WHEN 'verification_company_identification_hard' then cvch.comment_content
                        WHEN 'verification_special_form_PS1583' then cvu.comment_content
                        WHEN 'verification_General_CMRA' then cvu.comment_content
                        WHEN 'verification_california_mailbox' then cvu.comment_content
                        WHEN 'TC_contract_MS' then cc.comment_content
                        WHEN 'proof_of_address_MS' then cpb.comment_content
                        WHEN 'company_verification_E_MS' then cce.comment_content
                    END,'') as comment_content,
                    IFNULL(CASE cti.base_task_name
                        WHEN 'personal_identify' then cpi.updated_by
                        WHEN 'company_information' then cci.updated_by
                        WHEN 'document_of_company_registration' then crd.updated_by
                        WHEN 'verification_personal_identification' then cvpi.updated_by
                        WHEN 'verification_company_identification_soft' then cvcs.updated_by
                        WHEN 'verification_company_identification_hard' then cvch.updated_by
                        WHEN 'verification_special_form_PS1583' then cvu.updated_by
                        WHEN 'verification_General_CMRA' then cvu.updated_by
                        WHEN 'verification_california_mailbox' then cvu.updated_by
                        WHEN 'TC_contract_MS' then cc.update_by
                        WHEN 'proof_of_address_MS' then cpb.update_by
                        WHEN 'company_verification_E_MS' then cce.update_by
                    END,'') as updated_by
                FROM cases_milestone_instance cmi
                INNER JOIN cases_taskname_instance cti
                    ON cti.milestone_instance_id = cmi.id AND cti.case_id = cmi.case_id
                INNER JOIN cases_milestone cm
                    ON cmi.milestone_id = cm.id
                INNER JOIN cases_product cp
                    ON cm.product_id = cp.id
                LEFT JOIN cases_verification_company_hard cvch
                    ON cvch.case_id=cmi.case_id
                LEFT JOIN cases_verification_personal_identity cvpi
                    ON cvpi.case_id=cmi.case_id and cvpi.type=1
                LEFT JOIN cases_verification_personal_identity cvcs
                    ON cvcs.case_id=cmi.case_id and cvcs.type=2
                LEFT JOIN cases_verification_usps cvu
                ON cvu.case_id=cmi.case_id
                LEFT JOIN cases_company_information cci
                    ON cci.case_id=cmi.case_id
                LEFT JOIN cases_personal_identity cpi
                    ON cpi.case_id=cmi.case_id
                LEFT JOIN cases_registration_document crd
                    ON crd.case_id=cmi.case_id
                LEFT JOIN cases_contracts cc
                    ON cc.case_id=cmi.case_id
                LEFT JOIN cases_proof_business cpb
                    ON cpb.case_id=cmi.case_id
                LEFT JOIN cases_company_ems cce
                    ON cce.case_id=cmi.case_id
                WHERE cmi.status IN ('0','3') AND
                    cmi.case_id=" . $case_id;
        if (!empty($sort_column)) {
            $query .= " ORDER BY " . $sort_column . " " . $sort_type;
        }
        $query .= ") LIMIT " . $start . "," . $limit;
        $data = $this->db->query($query)->result();
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
    public function count_check_list_paging($case_id)
    {
        $query = "SELECT COUNT(cp.id) AS total_record
                FROM cases_milestone_instance cmi
                INNER JOIN cases_taskname_instance cti
                    ON cti.milestone_instance_id = cmi.id AND cti.case_id = cmi.case_id
                INNER JOIN cases_milestone cm
                    ON cmi.milestone_id = cm.id
                INNER JOIN cases_product cp
                    ON cm.product_id = cp.id
                WHERE cmi.status IN ('0','3') AND
                    cmi.case_id=" . $case_id;

        $result = $this->db->query($query)->row();
        return $result->total_record;
    }

    public function update_by_many($array_where, $data)
    {
        // Check exits row update
        $update_row = parent::get_by_many($array_where);

        if (empty($update_row)) {
            return false;
        }

        // open transaction
        $this->db->trans_begin();

        // 1. Update table cases_milestone_instance
        $result = parent::update_by_many($array_where, $data);

        // 2. Update table cases
        $case_id = $update_row->case_id;
        $cases_milestones = parent::get_many_by_many(array(
            'case_id' => $case_id
        ));

        $all_status = 2;
        foreach ($cases_milestones as $row) {
            if ($row->status != '2') {
                $all_status = 3;
                break;
            }
        }

        if ($all_status != 2) {
            foreach ($cases_milestones as $row) {
                if ($row->status != '3') {
                    $all_status = 1;
                    break;
                }
            }
        }

        $CI = &get_instance();
        $CI->load->model("cases/cases_m");
        $CI->cases_m->update($case_id, array(
            'status' => $all_status,
            'modified_date' => date("Y-m-d H.i.s")
        ));

        // commit transaction.
        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
        } else {
            $this->db->trans_rollback();
        }

        return $result;
    }
}