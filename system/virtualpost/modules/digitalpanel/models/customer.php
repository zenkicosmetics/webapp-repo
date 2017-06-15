<?php
if (! defined('BASEPATH')) exit('No direct script access allowed');

class customer extends CI_Model {

	private $data = null;

	function __construct() {
        parent::__construct();
    }

    public function loadData($location) {
        $query = $this->db->query('
			SELECT
				customers.customer_id AS id,
				customers_address.shipment_company AS company,
				customers_address.shipment_address_name AS name
			FROM
				customers JOIN customers_address
			ON
				customers.customer_id = customers_address.customer_id
			WHERE
				customers.activated_flag = 1
            AND
				customers.account_type = ' . APConstants::BUSINESS_TYPE . '
			ORDER BY
				customers_address.shipment_company');

        foreach ($query->result() as $row) {
			$this->data[] = array (
				'id'        => $row->id,
				'company'   => $row->company,
				'name'      => $row->name
			);
		}
    }

    public function getData() {
		return 	$this->data;
    }
}