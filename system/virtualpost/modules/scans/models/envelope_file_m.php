<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Group model
 */
class Envelope_file_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->profile_table = $this->db->dbprefix('envelope_files');
        $this->primary_key = 'id';
    }

    /**
     * Get total page number
     * @param unknown_type $customer_id
     * @param unknown_type $year_month
     * @param unknown_type $type
     * @return multitype:number multitype: |multitype:unknown
     */
    public function get_total_page_number($customer_id, $year_month, $type = '')
    {
        $query = ' SELECT SUM(envelope_files.number_page) AS total_page_number';
        $query = $query . " FROM envelope_files ";
        $query = $query . " WHERE from_unixtime(created_date, '%Y%m') = '" . $year_month . "' ";
        $query = $query . " AND customer_id = '" . $customer_id . "' ";

        if (!empty($type)) {
            $query = $query . " AND type = '" . $type . "' ";
        }

        $result = $this->db->query($query)->row();
        return $result->total_page_number;
    }

    /**
     * Get number of total scanned pages of current month
     *
     * @param $scanType (1: Envelope ; 2: Document)
     * @param $customerID The ID of customer
     */
    public function getTotalPagesScannedOfCurrentMonth($customerID, $scanType = 0)
    {
        $yearMonth = date('Ym');

        $sql = <<<SQL
SELECT
        SUM(number_page) AS total_pages
FROM
        envelope_files
WHERE
        FROM_UNIXTIME(created_date, '%Y%m') = '{$yearMonth}'
    AND customer_id = {$customerID}
SQL;
        if ($scanType) {
            $sql .= PHP_EOL . "      AND type = {$scanType}";
        }
        $row = $this->db->query($sql)->row();

        return $row->total_pages;
    }
}