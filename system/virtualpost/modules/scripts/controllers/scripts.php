<?php defined('BASEPATH') or exit('No direct script access allowed');

class Scripts extends Admin_Controller
{
    public function __construct()
    {
        ini_set('max_execution_time', 86400);
        error_reporting(E_ALL);
        ini_set('display_errors', '1');

        parent::__construct();

        $this->load->model('script_m');
        $this->load->library('SQLConfigs');
        $this->load->helper('functions');
		$this->load->model('scans/envelope_m');
        $this->load->model(array(
            'mailbox/postbox_setting_m',
            'scans/envelope_shipping_tracking_m'
        ));
        $this->load->library('scans/scans_api');
        $this->load->model('scans/envelope_pdf_content_m');
        $this->load->model('email/email_m');
        $this->load->model('mailbox/postbox_history_activity_m');
        $this->load->model('customers/customer_m');
    }

    public function index()
    {
    }

    public function correct_data_cases_verification_usps()
    {
        $fields = array(
            'verification_local_file_path',
            'id_of_applicant_local_file_path',
            'license_of_applicant_local_file_path',
            'additional_local_file_path'
        );
        $replace = array('/mnt/nfs-share/webapp' => '/var/www/clevvermail_webapp/shared');
        $temporaryTable = 'temp_correct_data_cases_verification_usps';
        $this->script_m->createTempTableFromSQLQuery($temporaryTable, SQLConfigs::SQL_correct_data_cases_verification_usps, true);
        $rows = $this->script_m->executeQuery("SELECT * FROM {$temporaryTable}", true);
        if ($rows) {
            foreach ($rows as $row) {
                $sqlFormat = "UPDATE cases_verification_usps SET %s WHERE id = %d";
                $setClause = '';
                foreach ($fields as $field) {
                    $$field = $row->{$field};
                    foreach ($replace as $key => $value) {
                        if (strpos($$field, $key) !== false) {
                            $$field = str_replace($key, $value, $$field);
                            $setClause .= "{$field} = '{$$field}',";
                        }
                    }
                }
                $setClause = substr($setClause, 0, strlen($setClause) - 1);
                $sqlUpdate = sprintf($sqlFormat, $setClause, $row->id);
                echo $sqlUpdate . '<br>';
                $this->script_m->executeQuery($sqlUpdate);
            }
            echo "There are totally " . count($rows) . " records affected!";
        }
    }

    public function search_invoice_payment()
    {
        $this->template->build('deleted_customer/search_invoice_and_payment');
    }

    /**
     * Delete Postbox
     * @link: scripts/deletePostbox?postbox_id=1011&customer_id=32972&key=703ade60727f264936f60d0968d65676
     * @link: scripts/deletePostbox?key=703ade60727f264936f60d0968d65676
     */
    public function deletePostbox()
    {
        $key = $this->input->get('key');
        if ($key != '703ade60727f264936f60d0968d65676') {
            echo "ERROR";
            exit;
        }
        $this->load->library('scripts/scripts_api');
        $this->load->library('mailbox/mailbox_api');
        $listCustomer = scripts_api::getListCustomers();
        //echo "<pre>";print_r($listCustomer);exit;
        $i = 0;
        foreach ($listCustomer as $customer) {
            // echo "Customer ID: ".$customer->customer_id. "<br/>";
            $listPostbox = mailbox_api::getPostboxByCustomer($customer->customer_id);
            if (count($listPostbox)) {
                foreach ($listPostbox as $postbox) {
                    APUtils::deletePostbox($postbox->postbox_id, $customer->customer_id, true);
                    $i++;
                    echo "Customer ID: " . $customer->customer_id . " Postbox ID: " . $postbox->postbox_id . "<br/>";
                }
            }


        }
        echo "<br> Number of postbox processing: " . $i;
        exit;
        /*
        $postbox_id = $this->input->get('postbox_id');
        $customer_id = $this->input->get('customer_id');
        $key = $this->input->get('key');
        if ($key == '703ade60727f264936f60d0968d65676') {
            APUtils::deletePostbox($postbox_id, $customer_id, true);
            echo "Finish";
        } else {
            echo "Error";
        }
        */

    }

    public function change_db_charset()
    {
        $this->load->library('scripts/DbCollation');

        $startTime = time();
        $this->dbcollation->changeCharacterSet();
        $endTime = time();
        $timeDiffInMinutes = round(($endTime - $startTime) / 60);
        echo "IT TAKES TIME: $timeDiffInMinutes (minutes)";
    }

    public function get_customers_without_email()
    {
        $temporaryTable = 'temp_customers_without_email';
        $sql = "SELECT * FROM customers WHERE user_name IS NULL";
        $this->script_m->createTempTableFromSQLQuery($temporaryTable, $sql, true);

        $sql = "SELECT customer_id FROM {$temporaryTable}";
        $customers = $this->script_m->executeQuery($sql, true);
        if ($customers) {
            $list = array();
            foreach ($customers as $customer) {
                array_push($list, $customer->customer_id);
            }
            $list = implode(',', $list);
            $sqlSelect = "<br>SELECT * FROM customers WHERE customer_id IN ({$list})";
            echo $sqlSelect;
        }
    }

    public function recover_email_for_deleted_accounts()
    {
        $this->load->library('scripts/scripts_api');

        //$sqlSelect = "SELECT customer_id, user_name, email FROM customer_blacklist";
        $sqlSelect = "SELECT customer_id, user_name, email FROM temp_customers_without_email";
        $sqlUpdate = "UPDATE customers SET user_name = '%s', email = '%s' WHERE customer_id = %d";
        $blacklistCustomers = scripts_api::executeQuery($sqlSelect, true, true);
        if ($blacklistCustomers) {
            echo '-----------------recover_email_for_deleted_accounts-------------------<br>';
            $i = 0;
            foreach ($blacklistCustomers as $customer) {
                ++$i;
                $sql = sprintf($sqlUpdate, $customer['user_name'], $customer['email'], $customer['customer_id']);
                echo $sql . '<br>';
                scripts_api::executeQuery($sql);
            }
            echo "==> Total: {$i} customers have been updated for his email";
        }
    }

    public function test()
    {
        $params = array(
            'temporary_table' => 'temp_envelope_files',
            'target_table' => 'envelope_files',
            'sql_filter' => 'SELECT * FROM envelope_files WHERE customer_id = 32537'
        );
        try {
            $this->load->library('scripts/SimpleTemporaryTable', $params);
            $data = $this->simpletemporarytable->getDataFromTemporaryTable();
            echo "<pre>";
            print_r($data);
            echo "</pre>";
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

    /**
     * Add common fields to all tables in DB
     * Execute URI:
     *      scripts/add_common_fields
     *      scripts/add_common_fields?rename=1
     */
    public function add_common_fields()
    {
        $this->load->library('scripts/CommonFields');
        $rename = $this->input->get_post('rename');
        if ($rename) {
            $this->commonfields->renameCommonFields();
        } else {
            $this->commonfields->addCommonFields();
        }
    }

    /**
     * Recover for rollback envelope status voi customer_id = 33188
     */
    public function recover_rollback_envelope_status()
    {
        $updateFields = array(
            'envelope_scan_flag' => "envelope_scan_flag = %d",
            'item_scan_flag' => "item_scan_flag = %d",
            'direct_shipping_flag' => "direct_shipping_flag = %d",
            'collect_shipping_flag' => "collect_shipping_flag = %d",
            'trash_flag' => "trash_flag = %d",
            'trash_date' => "trash_date = %d",
            'completed_flag' => "completed_flag = %d",
            'completed_date' => "completed_date = %d"
        );
        $temporaryTable = 'rollback_envelope_status_33188';
        $envelopes = $this->script_m->getDataFromTempTable($temporaryTable);
        if ($envelopes) {
            foreach ($envelopes as $envelope) {
                $setFields = array();
                foreach ($updateFields as $field => $update) {
                    $setFields[] = sprintf($update, $envelope->{$field});
                }
                $strSetFields = implode(', ', $setFields);
                $sql = "UPDATE envelopes SET {$strSetFields} WHERE id = {$envelope->id}";
                $this->script_m->executeQuery($sql);
                echo $sql . '<br>';
                unset($setFields);
            }
        }
    }

    /**
     * Rollback envelope status voi customer_id = 33188
     */
    public function rollback_envelope_status()
    {
        $temporaryTable = 'rollback_envelope_status_33188';

        //$sql = "SELECT * FROM envelopes WHERE to_customer_id = 33188";
        //$this->script_m->createTempTableFromSQLQuery($temporaryTable, $sql, true);

        $sql = "SELECT id, trash_date FROM temp_{$temporaryTable}";
        $envelopes = $this->script_m->executeQuery($sql, true);
        $total = 0;
        echo "<br/><hr/>";
        if ($envelopes) {
            foreach ($envelopes as $envelope) {
                $envelopeID = $envelope->id;

                $sql = str_replace('?', $envelopeID, SQLConfigs::SQL_rollback_envelope_status);
                $activities = $this->script_m->executeQuery($sql, true);

                // Loop through activities of each envelope in [envelopes_completed] (activity history)

                foreach ($activities as $activity) {
                    $activityID = $activity->activity_id;
                    $completedDate = $activity->completed_date;
                    $strCompletedDate = $activity->str_completed_date;
                    $sql = null;

                    // If activity = TRASH_ORDER_BY_SYSTEM (19) of the date '03/03/2016' => ignore it!
                    if ($activityID == APConstants::TRASH_ORDER_BY_SYSTEM_ACTIVITY_TYPE && $strCompletedDate == '03/03/2016') {
                        continue;
                    }
                    switch ($activityID) {
                        case APConstants::SCAN_ENVELOPE_COMPLETED_ACTIVITY_TYPE: // 1
                            $sql = "UPDATE envelopes SET envelope_scan_flag = 1, completed_flag = 1 WHERE id = {$envelopeID}";
                            break;
                        case APConstants::SCAN_ITEM_COMPLETED_ACTIVITY_TYPE: // 2
                            $sql = "UPDATE envelopes SET item_scan_flag = 1, completed_flag = 1 WHERE id = {$envelopeID}";
                            break;
                        case APConstants::DIRECT_FORWARDING_COMPLETED_ACTIVITY_TYPE: // 3
                            $sql = "UPDATE envelopes SET direct_shipping_flag = 1 WHERE id = {$envelopeID}";
                            break;
                        case APConstants::COLLECT_FORWARDING_COMPLETED_ACTIVITY_TYPE: // 4
                            $sql = "UPDATE envelopes SET collect_shipping_flag = 1 WHERE id = {$envelopeID}";
                            break;
                        case APConstants::TRASH_COMPLETED_ACTIVITY_TYPE: // 5
                            if ($envelope->trash_date == 0) {
                                $sql = "UPDATE envelopes SET trash_flag = 6, completed_flag = 1, trash_date = {$completedDate}, completed_date = {$completedDate} WHERE id = {$envelopeID}";
                            } else {
                                $sql = "UPDATE envelopes SET trash_flag = 6, completed_flag = 1, completed_date = {$completedDate} WHERE id = {$envelopeID}";
                            }
                            break;
                        case APConstants::SCAN_BOTH_ACTIVITY_TYPE: // 7
                            $sql = "UPDATE envelopes SET envelope_scan_flag = 0, item_scan_flag = 0 WHERE id = {$envelopeID}";
                            break;
                        case APConstants::TRASH_AFTER_SCAN_ACTIVITY_TYPE: // 6
                        case APConstants::TRASH_ORDER_BY_CUSTOMER_ACTIVITY_TYPE: // 8
                        case APConstants::TRASH_ORDER_BY_SYSTEM_ACTIVITY_TYPE: // 19

                            if ($envelope->trash_date == 0) {
                                $sql = "UPDATE envelopes SET trash_flag = 0, trash_date = {$completedDate} WHERE id = {$envelopeID}";
                            } else {
                                $sql = "UPDATE envelopes SET trash_flag = 0 WHERE id = {$envelopeID}";
                            }
                            break;

                        case APConstants::SCAN_ENVELOPE_ORDER_BY_CUSTOMER_ACTIVITY_TYPE: // 11
                        case APConstants::SCAN_ENVELOPE_ORDER_BY_SYSTEM_ACTIVITY_TYPE: // 15
                            $sql = "UPDATE envelopes SET envelope_scan_flag = 0 WHERE id = {$envelopeID}";
                            break;
                        case APConstants::SCAN_ITEM_ORDER_BY_CUSTOMER_ACTIVITY_TYPE: // 12
                        case APConstants::SCAN_ITEM_ORDER_BY_SYSTEM_ACTIVITY_TYPE: // 16
                            $sql = "UPDATE envelopes SET item_scan_flag = 0 WHERE id = {$envelopeID}";
                            break;
                        case APConstants::DIRECT_FORWARDING_ORDER_BY_CUSTOMER_ACTIVITY_TYPE: // 13
                        case APConstants::DIRECT_FORWARDING_ORDER_BY_SYSTEM_ACTIVITY_TYPE: // 17
                            $sql = "UPDATE envelopes SET direct_shipping_flag = 0 WHERE id = {$envelopeID}";
                            break;
                        case APConstants::COLLECT_FORWARDING_ORDER_BY_CUSTOMER_ACTIVITY_TYPE: // 14
                        case APConstants::COLLECT_FORWARDING_ORDER_BY_SYSTEM_ACTIVITY_TYPE: // 18
                            $sql = "UPDATE envelopes SET collect_shipping_flag = 0 WHERE id = {$envelopeID}";
                            break;

                    }
                    if ($sql) {
                        $this->script_m->executeQuery($sql);
                        $total++;
                        echo "Envelope_ID: " . $envelope->id . "<br/>Trash date: " . $envelope->trash_date . "<br/>Activity ID: " . $activityID . "<br/>completedDate: " . $completedDate . "<br/> strCompletedDate: " . $strCompletedDate . "<br/>SQL: " . $sql . '<hr/>';
                        log_message(APConstants::LOG_DEBUG, "Envelope_ID: " . $envelope->id . " <=> Trash date: " . $envelope->trash_date . " <=> Activity ID: " . $activityID . " <=> completedDate: " . $completedDate . " <=> strCompletedDate: " . $strCompletedDate . " <=> SQL: " . $sql);
                    }
                }
            }
        }
        echo "<hr/>Total Update: " . $total;
    }

    public function update_envelope_file()
    {
        $this->load->model('scans/envelope_file_m');

        $this->script_m->createTempTableFromSQLQuery('dynamic_file_path_for_envelope_files', SQLConfigs::SQL_ENVELOPES_UPDATE_PATH_FILE, true);

        // Get the records from this temporary table to process
        $envelope_files = $this->script_m->getDataFromTempTable('dynamic_file_path_for_envelope_files');

        echo "<br/>Begin<br/>";
        $j = 0;
        foreach ($envelope_files as $envelope_file) {

            $file_name = $envelope_file->file_name;
            $public_file_name = $envelope_file->public_file_name;

            $arr = array("scans", "uploads", "mailbox");
            for ($i = 0; $i < count($arr); $i++) {

                if (strpos($envelope_file->file_name, $arr[$i])) {
                    $file_name = "https://node2.eu.clevvermail.com/app/" . substr($envelope_file->file_name, strpos($envelope_file->file_name, $arr[$i]), strlen($envelope_file->file_name));
                }
                if (strpos($envelope_file->public_file_name, $arr[$i])) {

                    $public_file_name = "https://node2.eu.clevvermail.com/app/" . substr($envelope_file->public_file_name, strpos($envelope_file->public_file_name, $arr[$i]), strlen($envelope_file->public_file_name));
                }

            }

            $this->envelope_file_m->update_by_many(array(
                "id" => $envelope_file->id
            ), array(
                "file_name" => $file_name,
                "public_file_name" => $public_file_name
            ));
            $j++;
            if ($i <= 10) {
                echo $file_name . "<br/>";
                echo $public_file_name . "<br/>";
            }
        }
        echo "Total record: " . count($envelope_files) . " Update successful: " . $j;

    }

    public function rollback_update_envelope_file()
    {
        $this->load->model('scans/envelope_file_m');

        // Get the records from this temporary table to process
        $envelope_files = $this->script_m->getDataFromTempTable('dynamic_file_path_for_envelope_files');

        echo "<br/>Begin<br/>";

        foreach ($envelope_files as $envelope_file) {

            $file_name = $envelope_file->file_name;
            $public_file_name = $envelope_file->public_file_name;

            $this->envelope_file_m->update_by_many(array(
                "id" => $envelope_file->id
            ), array(
                "file_name" => $file_name,
                "public_file_name" => $public_file_name
            ));
        }

        echo "Finish";
    }

    /**
     *  Update [number_page] field value for Envelope_files without number_page after Item scanning
     */
    public function update_envelopes_without_number_pages()
    {
        // Create a temporary table to store records
        $this->script_m->createTempTableFromSQLQuery('envelopes_without_number_page', SQLConfigs::SQL_ENVELOPES_WITHOUT_NUMBER_PAGE, true);

        // Get the records from this temporary table to process
        $rows = $this->script_m->getDataFromTempTable('envelopes_without_number_page');
        if ($rows) {
            echo '<br>-------> BEGIN: update_envelopes_without_number_pages <br>';

            foreach ($rows as $row) {
                $id = $row->id;
                //$envelope_id = $row->envelope_id;

                // Local file name: /var/www/clevvermail_webapp/shared/data/filescan/33820/C00033820_BER01_290116_004_02.pdf
                $local_file_path = $row->local_file_name;

                // PDF Diectory information : /var/www/clevvermail_webapp/shared/tools/pdfinfo
                // Refer to [http://stackoverflow.com/questions/14644353/get-the-number-of-pages-in-a-pdf-document]
                $number_page = CountPdf::getTotalPageByExternalTool($local_file_path);

                $updateSql = "UPDATE envelope_files SET number_page = {$number_page} WHERE id = {$id}";
                echo $updateSql . '<br>';
                $this->script_m->query($updateSql);
            }

            echo '-------> END: update_envelopes_without_number_pages <br>';
        }
    }

    /**
     *  Update main_postbox for customers without main_postbox
     */
    public function update_customer_without_main_postbox()
    {
        echo "----> BEGIN : update_customer_without_main_postbox <br>";
        $rows = $this->script_m->executeQuery(SQLConfigs::SQL_CUSTOMERS_WITHOUT_MAIN_POSTBOX, true);
        $totalCustomers = count($rows);
        if ($rows) {
            echo "Affected customers: $totalCustomers <br>";
            foreach ($rows as $row) {
                $customerID = $row->customer_id;
                $sql = "SELECT * FROM postbox WHERE customer_id = {$customerID} AND first_location_flag = 1 ORDER BY type DESC LIMIT 1";
                $postboxes = $this->script_m->executeQuery($sql, true);
                foreach ($postboxes as $p) {
                    $postboxID = $p->postbox_id;
                    echo "Update MAIN_POSTBOX for customer_id = {$customerID} AND postbox_id = {$postboxID} <br>";
                    $sql = "UPDATE postbox SET is_main_postbox = 1 WHERE postbox_id = {$postboxID};";
                    $this->script_m->executeQuery($sql);
                }
            }
        }
        echo "----> END : update_customer_without_main_postbox <br>";
    }

    /**
     * Ticket: #840 CUSTOMER_SUPPORT Help customer to delete Free postbox AARON
     *
     * A customer with ID:C00037145 wants to delete the free postbox AARDON.
     * Can you please do that, the system requires to pay the open balance, the customer wants to keep his business postbox.
     */
    public function delete_postbox_requested_by_customer()
    {
        $customerCode = 'C00037145';
        $sql = "SELECT p.* FROM postbox AS p INNER JOIN customers AS c ON p.customer_id = c.customer_id WHERE c.customer_code = '%s' ORDER BY p.type ASC";
        $sql = sprintf($sql, $customerCode);

        // Update information on [postbox]
        $this->script_m->createTempTableFromSQLQuery('postbox_840', $sql, true);
        $rows = $this->script_m->getDataFromTempTable('postbox_840');
        if ($rows) {
            foreach ($rows as $postbox) {
                //$customerID = $postbox->customer_id;
                $postboxID = $postbox->postbox_id;
                $postboxType = $postbox->type;
                switch ($postboxType) {
                    case APConstants::FREE_TYPE:
                        /*
                        $updateData = array(
                            'is_main_postbox' => 0,
                            'deleted' => 1,
                            'completed_delete_flag' => 1
                        );
                        */
                        $sql = "DELETE FROM postbox WHERE postbox_id = {$postboxID}";
                        $this->script_m->executeQuery($sql);
                        break;
                    case APConstants::PRIVATE_TYPE:
                        break;
                    case APConstants::BUSINESS_TYPE:
                        $sql = "UPDATE postbox SET is_main_postbox = 1 WHERE postbox_id = {$postboxID}";
                        $this->script_m->executeQuery($sql);
                        break;
                }
            }
        }

        // Update information on [invoice_summary]
        //$sql = "SELECT * FROM invoice_summary WHERE customer_id = {$customerID}";
        //$this->script_m->createTempTableFromSQLQuery('invoice_summary_840', $sql, true);
        //$rows = $this->script_m->getDataFromTempTable('invoice_summary_840');
    }

    /**
     * Get the list of deleted accounts that have an open balance unequal zero
     */
    public function get_deleted_customers_with_open_balance_unequal_zero()
    {
        $this->load->model('customers/customer_m');
        $tempTableName = 'temp_deleted_customers_with_open_balance_unequal_zero';

        $this->script_m->executeQuery("TRUNCATE TABLE $tempTableName");

        $customers = $this->customer_m->get_many_by_many(array("status" => 1));
        foreach ($customers as $customer) {

            $currency = $this->customer_m->get_standard_setting_currency($customer->customer_id);
            //$decimal_separator = $this->customer_m->get_standard_setting_decimal_separator($customer->customer_id);
            $decimal_separator = '.';
            if (empty($currency)) {
                $currency = $this->currencies_m->get_by(array('currency_short' => 'EUR'));
            }

            $open_balance = APUtils::getCurrentBalance($customer->customer_id);
            $open_balance_this_month = APUtils::getCurrentBalanceThisMonth($customer->customer_id);

            if (abs($open_balance) < 0.01) $open_balance = 0;
            else $open_balance = APUtils::convert_currency($open_balance, $currency->currency_rate, 2, $decimal_separator);

            $open_balance_this_month = APUtils::convert_currency($open_balance_this_month, $currency->currency_rate, 2, $decimal_separator);

            $total_balance = number_format(($open_balance + $open_balance_this_month), 2, '.', '');

            if ($total_balance != 0) {

                $ql = $this->db->select('id')->from($tempTableName)->where('customer_id', "32321")->get();

                //customer id is not exists
                if (!($ql->num_rows() > 0)) {

                    $this->db->insert($tempTableName,
                        array(
                            'open_blance_due' => $open_balance,
                            'open_blance_this_month' => $open_balance_this_month,
                            'gross_open_balance' => $total_balance,
                            'customer_id' => $customer->customer_id,
                            'customer_code' => $customer->customer_code,
                            'user_name' => $customer->user_name,
                            'email' => $customer->email,
                            'status' => $customer->status,
                            'currence' => $currency->currency_short
                        )

                    );
                } // end if;
            }
        }

        echo "Successful.";
    }

    /**
     * Get all deleted customer with open balance not equal 0
     * Reset open_balance of these customers to 0
     *
     * => Run the terminal credit note process
     */
    public function reset_openbalance_with_credit_notes_for_deleted_customers()
    {
        $this->load->model('customers/customer_m');

        $customers = $this->customer_m->get_many_by_many(array("status" => 1));

        if ($customers) {
            foreach ($customers as $customer) {
                $customerId = $customer->customer_id;
                $total = CustomerUtils::getAdjustOpenBalanceDue($customerId);
                $total_balance = $total['OpenBalanceDue'] + $total['OpenBalanceThisMonth'];

                if ($total_balance != 0 && abs($total_balance) > 0.1) {
                    echo $customer->customer_code . '|' . number_format($total_balance, 2) . '<br/>';

                    // Delete customer again to create credit note or invoice
                    CustomerUtils::deleteCustomer($customerId, true, '1');
                    flush();
                    ob_flush();
                }
            }
        }

        echo "Successful.";
    }

    /*
     * Des: add incomming by scripts to customer
     * example: https://dev.eu.clevvermail.com/scripts/scripts/add_incomming_item?customer_id=xxx&postbox_id=xxx
     */
	public function add_incomming_item(){

        $envelope = new stdClass();
        $envelope->id = '';

		$postbox_id = $this->input->get_post('postbox_id');
		$customer_id = $this->input->get_post('customer_id');

		if (empty($invoice_flag)) {

		}
		$invoice_flag = 0;

		$postbox_setting = $this->postbox_setting_m->get_by_many(
			array(
				'customer_id' => $customer_id,
				"postbox_id" => $postbox_id
			));

                // Get setting of customer id
                $postbox = $this->postbox_m->get_by_many(
                    array(
                        "postbox_id" => $postbox_id
                    ));

                if($postbox->deleted == APConstants::ON_FLAG ){
                    $this->error_output("This postbox has been deleted. You can not add item in this postbox.");
                    return;
                }

				for($i=0;$i < 100;$i++){

                // Get setting of customer id
                $customer_setting = $this->customer_m->get_by_many(
                    array(
                        'customer_id' => $customer_id
                    ));
                if (empty($postbox_setting)) {
                    $postbox_setting = new stdClass();
                    $postbox_setting->always_scan_envelope = '0';
                    $postbox_setting->always_scan_envelope_vol_avail = '0';
                    $postbox_setting->always_scan_incomming = '0';
                    $postbox_setting->always_scan_incomming_vol_avail = '0';
                    $postbox_setting->email_notification = '0';
                    $postbox_setting->always_forward_directly = '0';
                    $postbox_setting->always_forward_collect = '0';
                    $postbox_setting->always_mark_invoice = '0';
                    $postbox_setting->invoicing_cycle = '0';
                    $postbox_setting->collect_mail_cycle = '0';
                    $postbox_setting->weekday_shipping = '2';
                }

                $envelope_scan_flag = null;
                $item_scan_flag = null;
                $new_notification_flag = APConstants::ON_FLAG;

                // Ticket #563 Case Management
                $verification_completed_flag = APConstants::OFF_FLAG;
                if (CaseUtils::isVerifiedAddress($customer_id) && CaseUtils::isVerifiedPostboxAddress($postbox_id, $customer_id)) {
                    $verification_completed_flag = APConstants::ON_FLAG;
                }


                if ($customer_setting->activated_flag == APConstants::ON_FLAG
                    && $verification_completed_flag == APConstants::ON_FLAG
                    && ($postbox_setting->always_scan_envelope_vol_avail === '1' || $postbox_setting->always_scan_incomming_vol_avail ===
                        '1')
                ) {
                    $postbox = $this->postbox_m->get_by('postbox_id', $postbox_id);
                    // Tinh toan dung luong hien tai
                    $current_volumn = $this->envelope_file_m->sum_by_many(
                        array(
                            "customer_id" => $customer_id
                        ), 'file_size');

                    $pricings = $this->pricing_m->get_all();
                    $pricing_map = array();
                    foreach ($pricings as $price) {
                        if (!array_key_exists($price->account_type, $pricing_map)) {
                            $pricing_map[$price->account_type] = array();
                        }
                        $pricing_map[$price->account_type][$price->item_name] = $price->item_value;
                    }
                    $max_volumn = $pricing_map[$postbox->type]['storage'];
                    if (($postbox->type === APConstants::FREE_TYPE && $current_volumn < $max_volumn * 1024 * 1024 * 1024) ||
                        $postbox->type !== APConstants::FREE_TYPE
                    ) {
                        if ($postbox_setting->always_scan_envelope_vol_avail === '1') {
                            // Set trang thai la request envelope scan
                            $envelope_scan_flag = '0';
                            $new_notification_flag = APConstants::OFF_FLAG;
                        }

                        if ($postbox_setting->always_scan_incomming_vol_avail === '1') {
                            // Set trang thai la request item scan
                            $item_scan_flag = '0';
                            $new_notification_flag = APConstants::OFF_FLAG;
                        }
                    }
                }

                // #522: temporary disable auto forward and auto scan functions
                // for deactivated accounts. reactivate auto mechanism after
                // account reactivation
                if ($customer_setting->activated_flag == APConstants::ON_FLAG && $verification_completed_flag == APConstants::ON_FLAG
                    && $postbox_setting->always_scan_envelope === '1'
                ) {
                    // Set trang thai la request envelope scan
                    $envelope_scan_flag = '0';
                    $new_notification_flag = APConstants::OFF_FLAG;
                }
                if ($customer_setting->activated_flag == APConstants::ON_FLAG && $verification_completed_flag == APConstants::ON_FLAG
                    && $postbox_setting->always_scan_incomming === '1'
                ) {
                    // Set trang thai la request item scan
                    $item_scan_flag = '0';
                    $new_notification_flag = APConstants::OFF_FLAG;
                }

                $incomming_date_only = date('dmy');
                $envelope_code = $postbox->postbox_code . '_' . $incomming_date_only;

                // Count all envelope of current day
                $number_envelope = $this->envelope_m->get_max_envelope_code(
                    array(
                        'to_customer_id' => $customer_id,
                        'postbox_id' => $postbox_id,
                        'incomming_date_only' => $incomming_date_only
                    ));
                $number_envelope += 1;
                $envelope_code = $envelope_code . '_' . sprintf('%1$03d', $number_envelope);

                // Mark auto scan envelope/item
                $auto_envelope_scan_flag = APConstants::OFF_FLAG;
                if ($envelope_scan_flag == '0' && $verification_completed_flag == APConstants::ON_FLAG) {
                    $auto_envelope_scan_flag = APConstants::ON_FLAG;
                }
                $auto_item_scan_flag = APConstants::OFF_FLAG;
                if ($item_scan_flag == '0' && $verification_completed_flag == APConstants::ON_FLAG) {
                    $auto_item_scan_flag = APConstants::ON_FLAG;
                }

                // Insert information to envelope table
                $id = $this->envelope_m->insert(
                    array(
                        'from_customer_name' => "Test1",
                        'to_customer_id' => $customer_id,
                        'postbox_id' => $postbox_id,
                        'envelope_code' => $envelope_code,
                        'envelope_type_id' => "C5",
                        'weight' => 10, #1058 add multi dimension capability for admin
                        'weight_unit' => 'g',
                        'last_updated_date' => now(),
                        'incomming_date' => now(),
                        'incomming_date_only' => $incomming_date_only,
                        'completed_flag' => APConstants::OFF_FLAG,
                        'category_type' => null,
                        'invoice_flag' => APConstants::OFF_FLAG,
                        "envelope_scan_flag" => $envelope_scan_flag,
                        "item_scan_flag" => $item_scan_flag,
                        "email_notification_flag" => APConstants::OFF_FLAG,
                        "new_notification_flag" => $new_notification_flag,
                        "location_id" => $postbox->location_available_id,
                        "auto_envelope_scan_flag" => $auto_envelope_scan_flag,
                        "auto_item_scan_flag" => $auto_item_scan_flag
                    ));

                // trigger storage nubmer report.
                scans_api::updateStorageStatus($id, $customer_id, $postbox_id, APUtils::getCurrentYear(), APUtils::getCurrentMonth(), $postbox->location_available_id, APConstants::ON_FLAG);

                // Prepare data to send email
                $send_prepayment_email = false;
                $open_balance_due = 0;
                $open_balance_this_month = 0;
                $total_prepayment_cost = 0;

                // #1012 Pre-Payment Process
                if ($envelope_scan_flag == '0') {
                    $check_prepayment_data = CustomerUtils::checkApplyScanPrepayment(APConstants::TRIGGER_ACTION_TYPE_SYSTEM,
                            'envelope', array($id), $customer_id, false);

                    // Only request if pass pre-paymnet
                    if ($check_prepayment_data['prepayment'] == true) {
                        $send_prepayment_email = true;
                        $open_balance_due = $check_prepayment_data['open_balance_due'];
                        $open_balance_this_month = $check_prepayment_data['open_balance_this_month'];
                        $total_prepayment_cost += $check_prepayment_data['estimated_cost'];
                        $this->envelope_m->update_by_many(array(
                            "id" => $id
                        ), array(
                            "auto_envelope_scan_flag" => APConstants::OFF_FLAG,
                            "envelope_scan_flag" => NULL
                        ));

                        mailbox_api::requestEnvelopeScanToQueue($id, $customer_id);
                    } else {
                        if ($verification_completed_flag == APConstants::ON_FLAG) {
                            scans_api::completeItem($id, APConstants::SCAN_ENVELOPE_ORDER_BY_SYSTEM_ACTIVITY_TYPE);
                        }
                    }
                }
                if ($item_scan_flag == '0') {
                    $check_prepayment_data = CustomerUtils::checkApplyScanPrepayment(APConstants::TRIGGER_ACTION_TYPE_SYSTEM,
                            'item', array($id), $customer_id, false);
                    // Only request if pass pre-paymnet
                    if ($check_prepayment_data['prepayment'] == true) {
                        $send_prepayment_email = true;
                        $open_balance_due = $check_prepayment_data['open_balance_due'];
                        $open_balance_this_month = $check_prepayment_data['open_balance_this_month'];
                        $total_prepayment_cost += $check_prepayment_data['estimated_cost'];

                        $this->envelope_m->update_by_many(array(
                            "id" => $id
                        ), array(
                            "auto_item_scan_flag" => APConstants::OFF_FLAG,
                            "item_scan_flag" => NULL
                        ));

                        mailbox_api::requestItemScanToQueue($id, $customer_id);
                    } else {
                        // Insert completed activity (Scan item ordered by system)
                        if ($verification_completed_flag == APConstants::ON_FLAG) {
                            scans_api::completeItem($id, APConstants::SCAN_ITEM_ORDER_BY_SYSTEM_ACTIVITY_TYPE);
                        }
                    }
                }

                $package_type = Settings::get_alias01(APConstants::ENVELOPE_TYPE_CODE, "C5");

                // Insert completed activity (Registered incoming)
                scans_api::completeItem($id, APConstants::REGISTERED_INCOMMING_ACTIVITY_TYPE);

                // Insert information to envelope_properties table (if
                // envelope_type is 'Package')
                $type_actual_value = $this->input->post('type');
                $type_label_value = $this->input->get('labelValue');
                if (!empty($type_actual_value)) {
                    $envelope_type = $this->settings_m->get_by_many(
                        array(
                            'SettingCode' => APConstants::ENVELOPE_TYPE_CODE,
                            'LabelValue' => $type_label_value,
                            'ActualValue' => $type_actual_value
                        ));
                    // if envelope_type is 'Package'
                    if ($envelope_type && $envelope_type->Alias02 == 'Package') {
                        $envelope_properties_id = $this->envelope_properties_m->insert(
                            array(
                                // #1058 add multi dimension capability for admin
                                'width' => "10",
                                'height' => "10",
                                'length' => "10",
                                'envelope_id' => $id
                            ));
                    }
                }

                $envelope_content = '';
                //#1058 add multi dimension capability for admin
                $date_format = APUtils::get_date_format_in_user_profiles();
                $envelope_content = $envelope_content . ' ' . $this->input->post('from_customer_name');
                $envelope_content = $envelope_content . ' ' . 10;
                $envelope_content = $envelope_content . ' ' . APUtils::viewDateFormat($incomming_date_only, $date_format);
                $envelope_content = $envelope_content . ' ' . Settings::get_label(APConstants::ENVELOPE_TYPE_CODE, $this->input->post('type'));
                $this->envelope_pdf_content_m->insert(
                    array(
                        "envelope_id" => $id,
                        "customer_id" => $customer_id,
                        "postbox_id" => $postbox_id,
                        "created_date" => now(),
                        "envelope_content" => $envelope_content
                    ));

                // Insert incomming number to [envelope_summary_table]
                //$this->invoices->cal_incomming_invoices($customer_id, $postbox_id, $id);

                $activated_flag = $customer_setting->activated_flag;
                if ($postbox_setting->email_notification === '1' && $activated_flag == '1') {
                    $to_email = $customer_setting->email;
                    $from_email = $this->config->item('EMAIL_FROM');

                    // Send email confirm for user
                    $activated_flag = $customer_setting->activated_flag;
                    if ($activated_flag == '1') {
                        $email_template_code = APConstants::new_incomming_notification;
                    } else {
                        $email_template_code = APConstants::new_incomming_notification_for_notactivated;
                    }

                    // Get location
                    $location = $this->location_m->get_by_many(
                        array(
                            'id' => $postbox->location_available_id
                        ));
                    $location_name = '';
                    if ($location) {
                        $location_name = $location->location_name;
                    }

                    // Get customer name
                    $customer_name = '';
                    $customer_address = $this->customers_address_m->get_by_many(
                        array(
                            'customer_id' => $customer_id
                        ));
                    if (!empty($customer_address)) {
                        if (!empty($customer_address->invoicing_address_name)) {
                            $customer_name = $customer_address->invoicing_address_name;
                        } else if (!empty($customer_address->invoicing_company)) {
                            $customer_name = $customer_address->invoicing_company;
                        }
                    } else {
                        $customer_name = $postbox->postbox_name;
                    }

                    $type = Settings::get_label(APConstants::ENVELOPE_TYPE_CODE, "C5");
                    $data = array(
                        "slug" => $email_template_code,
                        "to_email" => $to_email,
                        // Replace content
                        "full_name" => $customer_name,
                        "site_url" => APContext::getFullBalancerPath(),
                        "locations" => $location_name,
                        "from" => $this->input->post('from_customer_name'),
                        "type" => $type,
                        "weight" => $this->input->post('weight') . "g"
                    );

                    try {
                        MailUtils::sendEmailByTemplate($data);
                    } catch (Exception $e) {
                        log_message($e);
                    }

                    // Update trang thai send email
                    $this->envelope_m->update_by("id", $id,
                        array(
                            "email_notification_flag" => APConstants::ON_FLAG
                        ));

                    // Register push message (IOS#35)
                    $this->lang->load('api/api');
                    $message = lang('push.new_incomming');
                    scans_api::registerPushMessage($customer_id, $postbox_id, $id, $message, APConstants::PUSH_MESSAGE_INCOMMING_TYPE);
                }

                // Get customer address
                $address = $this->customers_address_m->get_by('customer_id', $customer_id);
                $eu_member_flag = '1';
                if ($address) {
                    $eu_member_flag = $address->eu_member_flag;
                }

                $customs_process_flag = EnvelopeUtils::check_customs_flag($customer_id, $postbox_id, $id);

                // Auto mark redirect or collect
                if ($postbox_setting->always_forward_directly === '1' && $verification_completed_flag == APConstants::ON_FLAG) {
                    $check_prepayment_data = CustomerUtils::checkApplyShippingPrepayment(APConstants::TRIGGER_ACTION_TYPE_SYSTEM,
                        APConstants::SHIPPING_SERVICE_NORMAL, APConstants::SHIPPING_TYPE_DIRECT, array($id), $customer_id, false);

                    // Only request if pass pre-paymnet
                    if ($check_prepayment_data['prepayment'] == true) {
                        $send_prepayment_email = true;
                        $open_balance_due = $check_prepayment_data['open_balance_due'];
                        $open_balance_this_month = $check_prepayment_data['open_balance_this_month'];
                        $total_prepayment_cost += $check_prepayment_data['estimated_cost'];

                        mailbox_api::requestDirectShippingToQueue($id, $customer_id);
                    } else {
                        // Update trang thai send email
                        $this->envelope_m->update_by("id", $id,
                            array(
                                "direct_shipping_flag" => APConstants::OFF_FLAG,
                                "direct_shipping_date" => now(),
                                "new_notification_flag" => APConstants::OFF_FLAG
                            ));

                        // Insert completed activity (Direct forwarding ordered by system)
                        scans_api::completeItem($id, APConstants::DIRECT_FORWARDING_ORDER_BY_SYSTEM_ACTIVITY_TYPE);

                        if ($customs_process_flag == APConstants::ON_FLAG && $package_type == '1') {
                            // Register to envelope_customs table
                            $envelope_customs_check = $this->envelope_customs_m->get_by_many(
                                array(
                                    "customer_id" => $customer_id,
                                    "envelope_id" => $id,
                                    "postbox_id" => $postbox_id
                                ));
                            if (!$envelope_customs_check) {
                                $this->envelope_customs_m->insert(
                                    array(
                                        "customer_id" => $customer_id,
                                        "envelope_id" => $id,
                                        "postbox_id" => $postbox_id,
                                        "process_flag" => APConstants::OFF_FLAG,
                                        "shipping_type" => '1'
                                    ));

                                // Insert completed activity
                                scans_api::completeItem($id, APConstants::WAITING_FOR_CUSTOMS_DECLARITON_ACTIVITY_TYPE);
                            }

                            // $this->send_email_declare_customs($customer_setting);
                        }
                    }
                } else if ($postbox_setting->always_forward_collect === '1' && $verification_completed_flag == APConstants::ON_FLAG) {

                    $this->envelope_m->update_by("id", $id,
                        array(
                            "collect_shipping_flag" => APConstants::OFF_FLAG,
                            "collect_shipping_date" => now(),
                            "new_notification_flag" => APConstants::OFF_FLAG
                        ));

                    // Insert completed activity (trigger collect forwarding ordered by system)
                    scans_api::completeItem($id, APConstants::COLLECT_FORWARDING_ORDER_BY_SYSTEM_ACTIVITY_TYPE);

                    if ($customs_process_flag == APConstants::ON_FLAG && $package_type == '1') {
                        // Regist to envelope_customs table
                        $envelope_customs_check = $this->envelope_customs_m->get_by_many(
                            array(
                                "customer_id" => $customer_id,
                                "envelope_id" => $id,
                                "postbox_id" => $postbox_id
                            ));
                        if (!$envelope_customs_check) {
                            $this->envelope_customs_m->insert(
                                array(
                                    "customer_id" => $customer_id,
                                    "envelope_id" => $id,
                                    "postbox_id" => $postbox_id,
                                    "process_flag" => APConstants::OFF_FLAG,
                                    "shipping_type" => '2'
                                ));

                            // Insert completed activity
                            scans_api::completeItem($id, APConstants::WAITING_FOR_CUSTOMS_DECLARITON_ACTIVITY_TYPE);
                        }


                    }

                }
			}

        echo "Successfull.";
    }

    public function get_list_customer_wrong_vat(){

        $data = $this->script_m->get_list_customer_vat();
        //echo "<pre>";print_r($data);exit;
        $arr_customer = array();
        foreach ($data as $k => $invoice_summary) {
           //echo "<pre>";print_r($invoice_summary);exit;
           if(!empty($invoice_summary->customer_id) && ($invoice_summary->total > 1) ){

                $invoice_detail = $this->script_m->get_invoice_detail($invoice_summary->customer_id);
               //echo "<pre>";print_r($invoice_detail);exit;
               //var_dump(!empty($invoice_detail));exit;

               if(!empty($invoice_detail) && is_array($invoice_detail)){

                    if( ($invoice_detail[0]->total != $invoice_summary->total) && ( ! in_array($invoice_detail[0]->total, $arr_customer)) )
                       {
                        $arr_customer[] = $invoice_detail[0]->customer_id;
                        echo $invoice_detail[0]->customer_id."<br/>";
                       }
               }

           }


        }

        //echo "<pre>";print_r($arr_customer);exit;


    }

    /**
     * convert the forwarding charge fee from envelope_shipping to invoice_summary and invoice_summary_by_location.
     */
    public function convert_forwarding_charge_fee(){
        $this->load->model('scans/envelope_shipping_m');
        $this->load->model('invoices/invoice_summary_m');
        $this->load->model('invoices/invoice_summary_by_location_m');

        $year_month = $this->input->get_post('ym');
        $envelope_shippings = $this->envelope_shipping_m->summary_all_shipping_fees($year_month);

        $this->envelope_shipping_m->db->trans_begin();
        foreach($envelope_shippings as $envelope){
            $this->invoice_summary_m->update_by_many(array(
                "customer_id" => $envelope->customer_id,
                "left(invoice_month, 6)" => $year_month,
                "(invoice_type is null OR invoice_type <> 2)" => null
            ), array(
                "forwarding_charges_fee" => $envelope->forwarding_charges_fee,
                "forwarding_charges_postal" => $envelope->forwarding_charges_postal,
            ));
        }

        // commit transaction
        if(ci()->envelope_shipping_m->db->trans_status() == FALSE){
            ci()->envelope_shipping_m->db->trans_rollback();
        }else{
            ci()->envelope_shipping_m->db->trans_commit();
        }

        $envelope_shippings = $this->envelope_shipping_m->summary_all_shipping_fees($year_month, true);

        $this->envelope_shipping_m->db->trans_begin();
        foreach($envelope_shippings as $envelope){
            $this->invoice_summary_by_location_m->update_by_many(array(
                "customer_id" => $envelope->customer_id,
                "left(invoice_month, 6)" => $year_month,
                "(invoice_type is null OR invoice_type <> 2)" => null,
                "location_id" => $envelope->location_available_id
            ), array(
                "forwarding_charges_fee" => $envelope->forwarding_charges_fee,
                "forwarding_charges_postal" => $envelope->forwarding_charges_postal,
            ));
        }

        // commit transaction
        if(ci()->envelope_shipping_m->db->trans_status() == FALSE){
            ci()->envelope_shipping_m->db->trans_rollback();
        }else{
            ci()->envelope_shipping_m->db->trans_commit();
        }
    }

    public function chart_export(){
        $this->template->set_layout(false);

        $this->load->library('pChart/pChartBasic');

        /* Create your dataset object */
        $chart = new pChartBasic();

//        $chart->setYData(array(6,20,90, 100, 200));
//        $chart->setXData(array("Sep 18", "Otc 19", "Nov 19", 'Dec 19', 'Jan 20'));
//        $chart->setSerieWeight("Time Range (minutes)",2);
//        $chart->setAbscissa("Labels");
//        $chart01 = new pChartBasic(700,230,$chart);
//        $chart->setGraphArea(60,40,670,190,array(6,20,90, 100, 200), 'Months',array("Sep 15", "Otc 18", "Nov 18", 'Dec 18', 'Jan 16'),"Labels","Time Range (minutes)",2,"Labels");

        $chart->renderImageBarChartLocationReport("uploads/example.png",60,40,670,190,array(6,130,90, 100, 200), 'Months',array("Sep 15", "Otc 14", "Nov 16", 'Dec 16', 'Jan 16'),"Labels","Time Range (minutes)",2,"Labels");
//        $MyData = new pData();
//
//        /* Add data in your dataset */
//        $MyData->addPoints(array(6,20,90, 100, 200), 'Months');
//        $MyData->setSerieWeight("Time Range (minutes)",2);
//        $MyData->addPoints(array("Sep 15", "Otc 15", "Nov 15", 'Dec 15', 'Jan 16'),"Labels");
//        $MyData->setSerieDescription("Labels","Months");
//        $MyData->setAbscissa("Labels");
//        $MyData->SetXAxisName(1,"Range of Data");
//
//
//        /* Create a pChart object and associate your dataset */
//        $myPicture = new pImage(700,230,$MyData);
//
//        /* Choose a nice font */
//        //$myPicture->setFontProperties(array("FontName"=>"fonts/Forgotte.ttf","FontSize"=>11));
//
//        /* Define the boundaries of the graph area */
//        $myPicture->setGraphArea(60,40,670,190);
//
//        /* Draw the scale, keep everything automatic */
//        $myPicture->drawScale();
//
//        /* Draw the scale, keep everything automatic */
//        $myPicture->drawSplineChart();
//
//        /* Render the picture (choose the best way) */
//        $myPicture->render("uploads/example.png");
//        //var_dump($test);die();
    }

    public function chart_sample(){
        $this->template->set_layout(false);

        // export chart to file.
        $this->chart_export();

        // Load pdf library
        ci()->load->library('pdf');
        $pdf = ci()->pdf->createObject();

        $pdf->setFontSubsetting(true);
        $pdf->SetFont('freeserif', '', 10, '', 'false');

        $pdf->setFontSubsetting(true);
        $pdf->SetFont('freeserif', '', 10, '', 'false');

        // set document information
        // Set common information
        $pdf->SetTitle(Settings::get(APConstants::INSTANCE_OWNER_COMPANY_CODE));
        $pdf->SetAuthor(Settings::get(APConstants::INSTANCE_OWNER_COMPANY_CODE));

        // disable header and footer
        ci()->pdf->setPrintHeader(true);
        ci()->pdf->setPrintFooter(true);

        // set default header data
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

        // set header and footer fonts
        $pdf->setHeaderFont(Array(
            PDF_FONT_NAME_MAIN,
            '',
            PDF_FONT_SIZE_MAIN
        ));
        $pdf->setFooterFont(Array(
            PDF_FONT_NAME_DATA,
            '',
            PDF_FONT_SIZE_DATA
        ));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // image scale
        $pdf->setImageScale(1.3);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);


        $html = ci()->load->view("chart_sample", array(), TRUE);

        $pdf->AddPage();
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, 'J', true);

        $pdf->Output("uploads/chart_sample2.pdf", 'I');



    }

    public function update_envelope_shipping_tracking(){

        $list_envelope_shipping_tracking = ci()->envelope_shipping_tracking_m->get_all();

        if(!empty($list_envelope_shipping_tracking)){
            foreach ($list_envelope_shipping_tracking as $envelope_shipping_tracking) {
                $envelope = ci()->envelope_m->get_by("id",$envelope_shipping_tracking->envelope_id);
                if(!empty($envelope)){
                    ci()->envelope_shipping_tracking_m->update_by_many(array("envelope_id" => $envelope_shipping_tracking->envelope_id), array( "package_id" => $envelope->package_id ));
                }
            }
        }
        echo "Finish";
    }

    // Script for data in histort postbox
    public function insert_postbox_history_activity (){
        ini_set('memory_limit', '-1');
        $customers = $this->customer_m->get_all();
        foreach ($customers as $customer){
//             var_dump($customer);die();
             $this->postbox_history_activity_m->insert_into($customer->customer_id);
        }

       echo 'Finish';
    }


    public function check_s3_fileinfor(){
        $this->load->model(array(
            "cases/cases_product_m",
            "cases/cases_milestone_m",
            "cases/cases_milestone_instance_m",
            "cases/cases_taskname_m",
            "cases/cases_taskname_instance_m",
            "cases/cases_verification_personal_identity_m",
            "cases/cases_verification_usps_m",
            "cases/cases_verification_company_hard_m",
            "cases/case_usps_mail_receiver_m",
            "cases/case_usps_officer_m",
            "cases/case_usps_business_license_m",
            "cases/cases_contract_m",
            "cases/case_resource_m",
            "cases/cases_proof_business_m",
            "cases/cases_company_ems_m",
        ));

        ci()->load->library('S3');

        $list_company_hards = $this->cases_verification_company_hard_m->get_all();
        $default_bucket_name = ci()->config->item('default_bucket');

        foreach($list_company_hards as $file){
            if(!empty($file->verification_local_file_path)){
                try{
                    $result = S3::getObjectInfo($default_bucket_name, $file->verification_amazon_file_path);
                } catch (Exception $ex) {
                    var_dump($file);
                    var_dump($ex);
                    var_dump($result);
                    die();
                }
            }
        }
    }

    public function cal_incoming_invoice(){
        ci()->load->library(array(
            'invoices/invoices',
        ));

        ci()->load->model(array(
            'scans/envelope_m',
        ));

        $envelopes = ci()->envelope_m->get_many_by_many(array(
            "incomming_date_only" => '170317',
            "right(envelope_code, 4)!= '_000'" => null
        ));

        foreach($envelopes as $el){
            echo "customer_id: ".$el->to_customer_id.", postbox_id: ".$el->postbox_id.", envelope ID: ".$el->id;
            echo "<br/>";
            // Insert incomming number to [envelope_summary_table]
            ci()->invoices->cal_incomming_invoices($el->to_customer_id, $el->postbox_id, $el->id);
        }

        echo "DONE===================================";
    }


    public function test_getCustomerInfoForPayOne(){
        ci()->load->model("customers/customer_m");
        $customer_id = 270;
        $result = ci()->customer_m->getCustomerInfoForPayOne($customer_id);
        print_r($result);
    }


    public function insertLanguageData(){
        ci()->load->model("settings/language_text_m");
        $result = ci()->language_text_m->insertLanguageData();
        echo $result;
    }

   public function revenuePerMonth($invoice_month)
   {
        $this->load->model(array(
            "report/report_by_location_m",
            "customers/customer_m"
        ));
        $this->load->library(array(
            "invoices/invoices_api"
        ));
        $locations_access = APUtils::loadListAccessLocation();
        for ($i = 0; $i < count($locations_access); $i ++) {
            for ($j = $i + 1; $j < count($locations_access); $j ++) {
                if (strcmp($locations_access[$i]->location_name, $locations_access[$j]->location_name ) > 0) {
                    $tmp = $locations_access[$j];
                    $locations_access[$j] = $locations_access[$i];
                    $locations_access[$i] = $tmp ;
                }
            }
        }
        echo '<table>';
        echo '<tr>';
        echo '<th>Location ID</th>';
        echo '<th>Location Name</th>';
        echo '<th>Total revenue</th>';
        echo '<th>Total revenue per user</th>';
        echo '</tr>';
        foreach ($locations_access as $la) {
            $location_id = $la->id;
            $invoice_by_location = $this->report_by_location_m->get_by_many(array(
                "left(invoice_month, 6) =" => $invoice_month,
                "location_id" => $location_id
            ));
            if (empty($invoice_by_location)) {
                invoices_api::updateInvoiceSummaryTotalByLocation($invoice_month, $location_id, false);

                $invoice_by_location = $this->report_by_location_m->get_by_many(array(
                    "left(invoice_month, 6) =" => $invoice_month,
                    "location_id" => $location_id
                ));
            }
            echo '<tr>';
            echo '<td>' . $location_id . '</td>';
            echo '<td>' . $la->location_name . '</td>';
            if ($invoice_by_location) {
                // Calculate total rev share
                $rev_share_total = 0;
                // amount share account
                $rev_share_total += round($invoice_by_location->free_postboxes_amount_share, 2);
                $rev_share_total += round($invoice_by_location->private_postboxes_amount_share, 2);
                $rev_share_total += round($invoice_by_location->business_postboxes_amount_share, 2);
                // incomming items caculate
                $rev_share_total += round($invoice_by_location->incomming_items_free_account_share, 2);
                $rev_share_total += round($invoice_by_location->incomming_items_private_account_share, 2);
                $rev_share_total += round($invoice_by_location->incomming_items_business_account_share, 2);
                // scan evalope charge
                $rev_share_total += round($invoice_by_location->envelope_scan_free_account_share, 2);
                $rev_share_total += round($invoice_by_location->envelope_scan_private_account_share, 2);
                $rev_share_total += round($invoice_by_location->envelope_scan_business_account_share, 2);
                // scan item
                $rev_share_total += round($invoice_by_location->item_scan_free_account_share, 2);
                $rev_share_total += round($invoice_by_location->item_scan_private_account_share, 2);
                $rev_share_total += round($invoice_by_location->item_scan_business_account_share, 2);
                // scan page
                $rev_share_total += round($invoice_by_location->additional_pages_scanning_free_amount_share, 2);
                $rev_share_total += round($invoice_by_location->additional_pages_scanning_private_amount_share, 2);
                $rev_share_total += round($invoice_by_location->additional_pages_scanning_business_amount_share, 2);
                // forwarding charge
                $rev_share_total += round($invoice_by_location->forwarding_charges_fee_share, 2);
                // storing letter charge
                $rev_share_total += round($invoice_by_location->storing_letters_free_account_share, 2);
                $rev_share_total += round($invoice_by_location->storing_letters_private_account_share, 2);
                $rev_share_total += round($invoice_by_location->storing_letters_business_account_share, 2);
                // storing packages
                $rev_share_total += round($invoice_by_location->storing_packages_free_account_share, 2);
                $rev_share_total += round($invoice_by_location->storing_packages_private_account_share, 2);
                $rev_share_total += round($invoice_by_location->storing_packages_business_account_share, 2);
                // out going charge
                $rev_share_total += round($invoice_by_location->custom_declaration_outgoing_price_01_amount_share, 2);
                $rev_share_total += round($invoice_by_location->custom_declaration_outgoing_price_02_amount_share, 2);
                // delivery charge
                $rev_share_total += round($invoice_by_location->cash_payment_free_for_item_delivery_amount_share, 2);
                // import custom, extra charge for process
                $rev_share_total += round($invoice_by_location->customs_cost_import_amount_share, 2);
                $rev_share_total += round($invoice_by_location->customs_handling_fee_import_amount_share, 2);
                $rev_share_total += round($invoice_by_location->address_verification_amount_share, 2);
                $rev_share_total += round($invoice_by_location->special_service_fee_in_15min_intervalls_amount_share, 2);
                $rev_share_total += round($invoice_by_location->personal_pickup_charge_amount_share, 2);
                // paypal charge. payment charge fee
                $rev_share_total += round($invoice_by_location->paypal_fee_share, 2);
                $rev_share_total += round($invoice_by_location->other_local_invoice_share, 2);
                $rev_share_total += round($invoice_by_location->credit_note_given_share, 2);

                // cash upfront
                $total_cash_upfront = 0;
                $numbers = 1;
                $numbers = $this->customer_m->count_customers_with_or_statements([
                        'status != 1' => null,
                        "postbox.location_available_id = $location_id" => null,
                    ],
                        [
                            'deleted > 1562385599' => null,
                            'status is null' => null
                        ]
                    );
                // Location earning
                $location_earning = $rev_share_total - $total_cash_upfront;
                if ($numbers) {
                    echo "<td>$location_earning</td>";
                    echo "<td>".round($location_earning / $numbers, 2) . "</td>";
                } else {
                    echo "<td>$location_earning</td>";
                    echo '<td>There are no customer !</td>';
                }
            } else {
                echo '<td>' . 0 . '</td>';
                echo '<td>' . 0 . '</td>';
            }
            echo '</tr>';
        }
        echo '</table>';

   }
    public function migrateDropboxAccount(){
        $TOKEN_FROM_OAUTH1 = "https://api.dropboxapi.com/2/auth/token/from_oauth1";
        $BASE64_ENCODE_APPKEY_APPSECRET = base64_encode(Settings::get(APConstants::DROPBOX_APP_KEY) . ":" . Settings::get(APConstants::DROPBOX_APP_SECRET));

        ci()->load->model("cloud/customer_cloud_m");

        $customers_cloud = ci()->customer_cloud_m->get_many_by(['cloud_id' => APConstants::CLOUD_DROPBOX_CODE]);
        $count = 0;
        foreach($customers_cloud as $cus_cloud){
            $setting = json_decode($cus_cloud->settings, true);
            if(empty($setting)){
                continue;
            }else if(!empty($setting['oauth_token']) && !empty($setting['oauth_token_secret'])){
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $TOKEN_FROM_OAUTH1,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => '{"oauth1_token":"' . $setting['oauth_token'] . '","oauth1_token_secret":"' . $setting['oauth_token_secret'] . '"}',
                    CURLOPT_HTTPHEADER => array(
                        "authorization: Basic $BASE64_ENCODE_APPKEY_APPSECRET",
                        "cache-control: no-cache",
                        "content-type: application/json",
                    ),
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);

                curl_close($curl);

                if ($err) {
                    throw new Exception($err);
                } else {
                    $result = json_decode($response, true);
                    $access_token = empty($result['oauth2_token']) ? '' : $result['oauth2_token'];
                    unset($setting['oauth_token']);
                    unset($setting['oauth_token_secret']);
                    if(!empty($access_token)){
                        $setting['access_token'] = $access_token;
                    }

                    ci()->customer_cloud_m->update_by_many(
                        array(
                            "cloud_id" => $cus_cloud->cloud_id,
                            "customer_id" => $cus_cloud->customer_id
                        ), array(
                        "settings" => json_encode($setting)
                    ));
                    $count++;
                }
            }
        }
        echo "$count customers has been updated dropbox setting";
    }
}