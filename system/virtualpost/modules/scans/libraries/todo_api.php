<?php defined('BASEPATH') or exit('No direct script access allowed');

class todo_api extends Core_BaseClass
{
    public function __construct() {
        // model
        ci()->load->model(array(
            'scans/envelope_m',
            'settings/settings_m',
            'scans/envelope_file_m',
            'scans/envelope_pdf_content_m',
            'scans/envelope_m',
            'cloud/customer_cloud_m',
            'scans/envelope_shipping_tracking_m',
            'scans/envelope_file_m',
            'scans/envelope_pdf_content_m',
            'scans/package_price_m',
            'scans/envelope_completed_m',
            'addresses/customers_address_m',
            'email/email_m',
            'addresses/customers_address_m',
            'mailbox/envelope_customs_m',
            'mailbox/envelope_customs_detail_m',
            'addresses/customers_forward_address_m',
            'addresses/location_m',
            'settings/countries_m',
            'scans/envelope_shipping_m'
        ));

        // library
        ci()->load->library(array(
            "scans/scans_api",
            "invoices/invoices_api",
            'settings/settings_api',
            'addresses/addresses_api',
            'shipping/shipping_api',
            "customers/customers_api"
        ));
        
        //lang
        ci()->lang->load(array(
            "scans/scans",
            'mailbox/delete_permission',
            'mailbox/activity_permission',
            'addresses/address'
        ));
    }
    
    /**
     * Gest list todo
     * @param type $list_filter_location_id
     * @param type $input_paging
     * @return type
     */
    public static function get_todo_list($list_filter_location_id, $input_paging){
        #1058 add multi dimension capability for admin
        $date_format = APUtils::get_date_format_in_user_profiles();
        $decimal_separator = APUtils::get_decimal_separator_in_user_profiles();
        $length_unit =  APUtils::get_length_unit_in_user_profiles();
        $weight_unit = APUtils::get_weight_unit_in_user_profiles();
        $currency_short = APUtils::get_currency_short_in_user_profiles();
        $currency_rate = APUtils::get_currency_rate_in_user_profiles();
        
        $array_condition = array();
        $array_condition ['envelopes.location_id IN ' . "(" . implode(",", $list_filter_location_id) . ")"] = null;

        // Do not display item of delted customer
        $array_condition ["(customers.status <> '1' OR customers.status IS NULL OR (customers.status = 1 AND (envelopes.trash_flag = '0') OR (envelopes.trash_flag = '".APConstants::ENVELOPE_TRASH_BY_CUSTOMER_IN_TRASH_FOLDER."')))"] = null;
            
        // Call search method
        $query_result = scans_api::getEnvelopePagingInTodoList($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);
        
        // Process output data
        $total = $query_result ['total'];
        $rows = $query_result ['data'];

        // Get output response
        $response = self::get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);

        $i = 0;
        $by_pass_records = 0;
        $mobile_result = array();
        foreach ($rows as $row) {
            $activity = '';
            if ($row->envelope_scan_flag === '0') {
                if ($row->item_scan_flag === '0') {
                    $activity = lang('envelope.activity_7'); //Scan Both
                } else {
                    $activity = lang('envelope.activity_1'); //Scan Envelope
                }
            } else if ($row->trash_flag === '0' || $row->trash_flag === '1' || $row->trash_flag === APConstants::ENVELOPE_TRASH_BY_CUSTOMER_IN_TRASH_FOLDER) {
                $delete_key_sign = APUtils::build_delete_sign($row->envelope_scan_flag, $row->item_scan_flag, $row->direct_shipping_flag, $row->collect_shipping_flag);
                $delete_activity = lang("activity_" . $delete_key_sign);

                if ($delete_activity == 'Trash' || $delete_activity == 'Delete') {
                    $activity = lang('envelope.activity_5'); //Trash
                } else if ($delete_activity == 'Trash After Scan') {
                    $activity = lang('envelope.activity_6'); //Trash after scan
                }
            } else if ($row->item_scan_flag === '0') {
                $activity = lang('envelope.activity_2'); //Scan Item
            } else if ($row->direct_shipping_flag === '0') {
                $activity = lang('envelope.activity_3'); //Direct Shipping
            } else if ($row->collect_shipping_flag === '0' && !empty($row->package_id)) {
                $activity = lang('envelope.activity_4'); //Collect Shipping
            }
            else if ( $row->tracking_number_flag === '0' && ($row->direct_shipping_flag === '1' OR $row->collect_shipping_flag === '1') ) {
                $activity = lang('envelope.activity_10'); //Collect Shipping
            }

            // Gets open balnace.
            $open_balance = APUtils::getAdjustOpenBalanceDue($row->to_customer_id);
            
            $open_balance_due_all = $open_balance['OpenBalanceDue'];
            $open_balance_this_month = $open_balance['OpenBalanceThisMonth'];

            $sign = "";
            if ($open_balance_due_all > 0) {
                $sign = "+";
            }
            
            // Get account status
            $row->customer_id = $row->to_customer_id;
            $account_status = customers_api::getCustomerStatus($row);

            $verified_flag = 'None';
            if ($row->required_verification_flag == '1') {
                if ($row->invoice_address_verification_flag == 1 && $row->name_verification_flag == 1 && $row->company_verification_flag == 1) {
                    $verified_flag = 'OK';
                }
            }
            //#1058 add multi dimension capability for admin 
            $weight = APUtils::view_convert_number_in_weight($row->weight, $weight_unit, $decimal_separator);

            // gets information
            $envelope_type_label = Settings::get_label(APConstants::ENVELOPE_TYPE_CODE, $row->envelope_type_id);
            $category_type_label = Settings::get_label(APConstants::CATEGORY_TYPE_CODE, $row->category_type);
            $last_updated_date=  APUtils::viewDateFormat($row->last_updated_date, $date_format . APConstants::TIMEFORMAT_OUTPUT01);
            $open_balance_all = abs($open_balance_due_all) > 0.01 ? $sign . APUtils::view_convert_number_in_currency($open_balance_due_all, $currency_short, $currency_rate, $decimal_separator) . ' ' . $currency_short : '0';
            $open_balance_current_month = $open_balance_this_month > 0 ? "+" . APUtils::view_convert_number_in_currency($open_balance_this_month, $currency_short, $currency_rate,$decimal_separator) . ' ' . $currency_short : APUtils::number_format($open_balance_this_month, 2, $decimal_separator) . ' ' .$currency_short;
            
            
            $response->rows [$i] ['id'] = $row->id;
            $response->rows [$i] ['cell'] = array(
                $row->id,
                $row->envelope_code,
                $row->from_customer_name,
                $row->to_customer_id,
                $row->to_customer_name,
                $row->to_customer_name,
                $row->envelope_type_id,
                $envelope_type_label,
                $weight,
                $row->category_type,
                $category_type_label,
                $account_status,
                $verified_flag,
                $row->invoice_flag,
                $last_updated_date,
                $row->registration_month,
                $row->id,
                $open_balance_all,
                $open_balance_current_month,
                $activity,
                $row->remarked_flag,
                $row->comment,
                $row->envelope_scan_flag,
                $row->item_scan_flag,
                $row->direct_shipping_flag,
                $row->collect_shipping_flag,
                $row->trash_flag,
                $row->package_id,
                $row->postbox_id,
                $row->completed_flag,
                $row->status,
                $row->incomming_date,
                $row->tracking_number_flag
            );
            
            // mobile data row.
            $row->last_updated_date =  $last_updated_date;
            $row->category_type_label = $category_type_label;
            $row->envelope_type_label = $envelope_type_label;
            $row->weight = $weight;
            $row->open_balance_due_all = $open_balance_all;
            $row->open_balance_this_month = $open_balance_current_month;
            $row->account_status = $account_status;
            $row->verified_flag = $verified_flag;
            $row->activity = $activity;
            $mobile_result[$i] = $row;
            
            $i++;
        }
        $response->records = $response->records - $by_pass_records;
        
        return array(
            "web_result" => $response,
            "mobile_result" => $mobile_result
        );
    }
    
    /**
     * exec envelope and item scan.
     * @param type $customer
     * @param type $envelope
     * @param type $action_type
     * @param type $scan_type
     * @param type $number_page
     * @return boolean
     */
    public static function execute_scan($customer, $envelope, $action_type, $scan_type, $number_page){
        
        $customer_id = $customer->customer_id;
        $fileTempName = '';
        $file_name = '';
        $fileSize = 0;
        $envelope_id = $envelope->id;
        $page_number = $number_page;
        $ocr_flag = APConstants::OFF_FLAG;
        $pdf_content = '';
        if ($action_type === 'upload') {
            $config ['upload_path'] = './uploads/temp/';
            $config ['allowed_types'] = 'pdf|png';
            $config ['overwrite'] = TRUE;
            ci()->load->library('upload', $config);

            if (!ci()->upload->do_upload('imagepath')) {
                //ci()->error_output('Upload file error. Please contact with administrator.');
                return array(
                    'status' => false,
                    'message' => ci()->upload->display_errors()
                );
            } else {
                $upload_data = ci()->upload->data();
                $fileTempName = $upload_data ['full_path'];
                $file_name = $upload_data ['file_name'];
                $fileSize = $upload_data ['file_size'];

                // Integrate count pdf number
                try {
                    $page_number = CountPdf::getTotalPageByExternalTool($fileTempName);
                    log_message(APConstants::LOG_DEBUG, 'Count page number from pdf file:' . $page_number);
                } catch (Exception $ex) {
                    log_message(APConstants::LOG_ERROR, 'Count pdf number error.');
                }
                
                // Convert file to searchable PDF
                ci()->load->library('OCRUtils');
                $ocr_result = OCRUtils::convertPDFToSearchable($fileTempName, '');
                if (!empty($ocr_result)) {
                    $ocr_flag = APConstants::ON_FLAG;
                    $fileTempName = $ocr_result['pdf'];
                    if ($scan_type == '2') {
                        $pdf_content = $ocr_result['text'];
                    }
                }
            }
        } else {
            $fileTempName = $_FILES ['RemoteFile'] ['tmp_name'];
            $fileSize = $_FILES ['RemoteFile'] ['size'];
            $file_name = $_FILES ['RemoteFile'] ['name'];
        }

        // Check scan type
        if ($scan_type === '1') {
            // $fileName = 'EC' . $envelope_id . '_C' . $customer->customer_id . '_F';
            $fileName = $envelope->envelope_code . '_01';
        } else {
            // $fileName = 'DC' . $envelope_id . '_C' . $customer->customer_id . '_F';
            $fileName = $envelope->envelope_code . '_02';
        }

        $ext = strtolower(substr($file_name, strrpos($file_name, '.') + 1));
        $fileName = $fileName . "." . $ext;

        // If upload pdf with text format
        // if ($ext == 'txt') {
        // Read file content
        // $fp = fopen($fileTempName, "r");
        // $pdf_content = fread($fp, filesize($fileTempName));
        // fclose($fp);
        
        if ($scan_type == '2') {
            // Update to database
            $pdf_content_file = ci()->envelope_pdf_content_m->get_by_many(array(
                "envelope_id" => $envelope_id,
                "customer_id" => $customer->customer_id
            ));
            $envelope_content = '';
            $envelope_content = $envelope_content . ' ' . $envelope->from_customer_name;
            $envelope_content = $envelope_content . ' ' . $envelope->weight;
            $envelope_content = $envelope_content . ' ' . APUtils::convert_timestamp_to_date($envelope->incomming_date, 'd.m.Y');
            $envelope_content = $envelope_content . ' ' . Settings::get_label(APConstants::ENVELOPE_TYPE_CODE, $envelope->envelope_type_id);
            $envelope_content = $envelope_content . ' ' . Settings::get_label(APConstants::CATEGORY_TYPE_CODE, $envelope->category_type);

            if ($pdf_content_file) {
                ci()->envelope_pdf_content_m->update_by_many(array(
                    "envelope_id" => $envelope_id,
                    "customer_id" => $customer->customer_id
                ), array(
                    "pdf_content" => $pdf_content,
                    "envelope_content" => $envelope_content
                ));
            } else {
                ci()->envelope_pdf_content_m->insert(array(
                    "envelope_id" => $envelope_id,
                    "customer_id" => $customer->customer_id,
                    "postbox_id" => $envelope->postbox_id,
                    "created_date" => now(),
                    "pdf_content" => $pdf_content,
                    "envelope_content" => $envelope_content
                ));
            }
        }

        // return array(
        //    'status' => true,
        //    'message' => '',
        //    "private_path" => ''
        // );
        // }

        if (!is_dir(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'filescan')) {
            mkdir(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'filescan', 0777, TRUE);
            chmod(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'filescan', 0777);
        }
        if (!is_dir(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'filescan/' . $customer->customer_id)) {
            mkdir(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'filescan/' . $customer->customer_id, 0777, TRUE);
            chmod(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'filescan/' . $customer->customer_id, 0777);
        }

        $local_file_name = Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'filescan/' . $customer->customer_id . '/' . $fileName;
        if (file_exists($local_file_name)) {
            unlink($local_file_name);
        }
        copy($fileTempName, $local_file_name);

        //$private_path = APContext::getFullBasePath() . 'scans/todo/get_file_scan?envelope_id=' . $envelope_id . '&type=' . $scan_type;
        //$public_file_path = APContext::getFullBasePath() . 'mailbox/get_file_scan?envelope_id=' . $envelope_id . '&type=' . $scan_type;

        $private_path = 'scans/todo/get_file_scan?envelope_id=' . $envelope_id . '&type=' . $scan_type;
        $public_file_path = 'mailbox/get_file_scan?envelope_id=' . $envelope_id . '&type=' . $scan_type;

        // Upload file to S3
        $default_bucket_name = ci()->config->item('default_bucket');
        $amazon_relate_path = $customer_id . '/' . $fileName;
        log_message(APConstants::LOG_DEBUG, "Default bucket name: " . $default_bucket_name);
        $amazon_path = '';
        $sync_amazon_flag = APConstants::OFF_FLAG;
        if (Settings::get(APConstants::FLAG_UPLOAD_S3) == APConstants::ON_FLAG) {
            $result = S3::putObjectFile($local_file_name, $default_bucket_name, $amazon_relate_path, S3::ACL_PRIVATE);

            log_message(APConstants::LOG_DEBUG, "Upload file to Amazon: " . $local_file_name);
            $amazon_path = sprintf("http://%s.s3.amazonaws.com/%s/%s", $default_bucket_name, $customer_id, $fileName);
            if (!$result) {
                log_message(APConstants::LOG_DEBUG, "Can not upload file to Amazon: " . $amazon_path);
                // return;
                $sync_amazon_flag = APConstants::OFF_FLAG;
            } else {
                $sync_amazon_flag = APConstants::ON_FLAG;
            }

        }

        // Update file to database
        $scan_file = ci()->envelope_file_m->get_by_many(array(
            "envelope_id" => $envelope_id,
            "customer_id" => $customer->customer_id,
            "type" => $scan_type
        ));

        // Truong hop la insert
        if (!$scan_file) {
            ci()->envelope_file_m->insert(array(
                "envelope_id" => $envelope_id,
                "customer_id" => $customer->customer_id,
                "file_size" => $fileSize,
                "file_name" => $private_path,
                "public_file_name" => $public_file_path,
                "local_file_name" => $local_file_name,
                "created_date" => now(),
                "type" => $scan_type,
                "number_page" => $page_number,
                "amazon_path" => $amazon_path,
                "amazon_relate_path" => $amazon_relate_path,
                "sync_amazon_flag" => $sync_amazon_flag,
                "ocr_flag" => $ocr_flag
            ));
        } // Truong hop la update
        else {
            ci()->envelope_file_m->update_by_many(array(
                "envelope_id" => $envelope_id,
                "customer_id" => $customer->customer_id,
                "type" => $scan_type
            ), array(
                "file_size" => $fileSize,
                "file_name" => $private_path,
                "public_file_name" => $public_file_path,
                "local_file_name" => $local_file_name,
                "updated_date" => now(),
                "type" => $scan_type,
                "number_page" => $page_number,
                "amazon_path" => $amazon_path,
                "amazon_relate_path" => $amazon_relate_path,
                "sync_amazon_flag" => $sync_amazon_flag,
                "ocr_flag" => $ocr_flag
            ));
        }
        
        return array(
            'status' => true,
            'message' => '',
            "private_path" => $private_path
        );
    }
    
    /**
     * Mark complete for item activity
     * @param type $envelope
     * @param type $customer
     * @param type $current_scan_type : activity type will be marked complete
     * @param type $invoice_flag
     * @param type $category_type
     * @param type $check_page_item_flag
     * @return type
     */
    public static function mark_completed($envelope, $customer, $current_scan_type, $invoice_flag, $category_type, $check_page_item_flag){
        $envelope_id = $envelope->id;
        $customer_id = $customer->customer_id;
        $postbox_id = $envelope->postbox_id;
        $postbox_setting = APContext::getPostboxSetting($customer_id, $postbox_id);

        $item_scan_flag = $envelope->item_scan_flag;
        $envelope_scan_flag = $envelope->envelope_scan_flag;
        $direct_shipping_flag = $envelope->direct_shipping_flag;
        $collect_shipping_flag = $envelope->collect_shipping_flag;

        $completed_by = APContext::getAdminIdLoggedIn();
        
        //Mark complete for envelope scan request
        if ($current_scan_type == '1') {
            
            //Complete envelope scan flag
            $envelope_scan_flag = 1;
            
            //Check permission
            $envelope_key_sign = APUtils::build_delete_sign($envelope_scan_flag, $item_scan_flag, $direct_shipping_flag, $collect_shipping_flag);
            $activity_permission = lang("envelope_" . $envelope_key_sign);
            if ($activity_permission == '0') {
                return array(
                    "status" => false,
                    "message" => lang('scans.not_possible')
                );
            }
            
            // Update complete status of envelope
            ci()->envelope_m->update_by_many(array(
                "id" => $envelope_id,
                "to_customer_id" => $customer_id
            ), array(
                "envelope_scan_flag" => $envelope_scan_flag,
                "envelope_scan_date" => now(),
                'last_updated_date' => now()
            ));

            if ($check_page_item_flag != 1) {
                invoices_api::calculateCostForEnvelopeScan($customer_id, $envelope->postbox_id, $envelope->id);
                //ci()->invoices->calculate_invoice($customer_id);
            }

            // Insert data to completed table
            scans_api::insertCompleteItem($envelope_id, APConstants::SCAN_ENVELOPE_COMPLETED_ACTIVITY_TYPE, APConstants::TRIGGER_BY_ADMIN, $completed_by);
            
        
        } // Completed item scan
        else if ($current_scan_type == '2') {
            //Mark complete for item scan request
            //Complete item scan flag
            $item_scan_flag = 1;
            $envelope_key_sign = APUtils::build_delete_sign($envelope_scan_flag, $item_scan_flag, $direct_shipping_flag, $collect_shipping_flag);
            $activity_permission = lang("envelope_" . $envelope_key_sign);
            if ($activity_permission == '0') {
                return array(
                    "status" => false,
                    "message" => lang('scans.not_possible')
                );
            }
            
            // Update complete item scan status of envelope
            ci()->envelope_m->update_by_many(array(
                "id" => $envelope_id,
                "to_customer_id" => $customer_id
            ), array(
                //"invoice_flag" => $invoice_flag,
                "item_scan_flag" => $item_scan_flag,
                "item_scan_date" => now(),
                "last_updated_date" => now(),
                "category_type" => $category_type
            ));

            //Mark the date to send mail late
            if ($envelope->invoice_flag == APConstants::ON_FLAG){

                $accounting_email = EnvelopeUtils::get_accounting_interface_by_postbox($postbox_id)['email'];
                if (!empty($accounting_email)){
                    $email_data = array(
                            'to_email' => $accounting_email,
                            'slug' => APConstants::accounting_invoice_email,
                            'send_date' => strtotime('+24 hours'),
                            'attachments' => EnvelopeUtils::get_item_scan_of_envelope(array($envelope_id)),
                            'data' => array('customer_id' => $customer_id, 'item_id' => $envelope_id)
                    );

                    //Add to email queue
                    MailUtils::addEmailQueue($email_data);
                }
            }

            // Sync with cloud if this could marked auto sync
            $customer_setting = ci()->customer_cloud_m->get_by_many(array(
                "cloud_id" => APConstants::CLOUD_DROPBOX_CODE,
                "customer_id" => $customer_id
            ));
            
            if (!empty($customer_setting)) {
                if ($customer_setting->auto_save_flag == '1' && !empty($customer_setting->settings)) {
                    // Decode cloud setting
                    $setting = json_decode($customer_setting->settings, true);

                    // Sync file to clod
                    $dropboxV2 = APContext::getDropbox($setting);
                    if (!empty($dropboxV2->getAccessToken())) {
                        $envelope_file = ci()->envelope_file_m->get_by_many_order(array(
                            "envelope_id" => $envelope_id,
                            "customer_id" => $customer_id,
                            "type" => '2'
                        ), array(
                            "updated_date" => "ASC",
                            "created_date" => "ASC"
                        ));

                        $document_file_name = '';
                        $local_file_name = '';
                        if ($envelope_file && !empty($envelope_file->local_file_name)) {
                            $document_file_name = $envelope_file->local_file_name;

                            // Download from S3
                            $default_bucket_name = ci()->config->item('default_bucket');
                            APUtils::download_amazon_file($envelope_file);
                            $local_file_name = $document_file_name;
                        }

                        // Gets postbox and location name
                        $postbox_location = ci()->postbox_m->get_postbox($postbox_id);
                        $subfolder_name = $postbox_location ? '/' . $postbox_location[0]->location_name : '/';
                        $parent_folder = $dropboxV2->getFolderName();
                        $dropboxV2->create_folder($parent_folder . $subfolder_name);
                        if (!empty($local_file_name)) {
                            $dropboxV2->add($parent_folder . $subfolder_name, $local_file_name, array(
                                "mode" => "overwrite"
                            ));

                            // Update sysc dropbox status
                            ci()->envelope_m->update_by_many(array(
                                "id" => $envelope_id,
                                "to_customer_id" => $customer_id
                            ), array(
                                "sync_cloud_flag" => APConstants::ON_FLAG,
                                "sync_cloud_date" => now(),
                                'last_updated_date' => now()
                            ));
                        }
                    }
                }
            }

            if ($check_page_item_flag != 1) {
                invoices_api::calculateCostForItemScan($customer_id, $envelope->postbox_id, $envelope->id);
                //ci()->invoices->calculate_invoice($customer_id);
            }

            // Send email notification
            if ($postbox_setting->email_scan_notification == APConstants::ON_FLAG) {
                // Send email confirm for user
                $data = array(
                    "slug" => APConstants::scan_item_completed_notification,
                    "to_email" => $customer->email,
                    // Replace content
                    "full_name" => $customer->email,
                    "envelope_code" => $envelope->envelope_code
                );
                MailUtils::sendEmailByTemplate($data);

                // Register push message (IOS#35)
                ci()->lang->load('api/api');
                $message = lang('push.item_scans');
                scans_api::registerPushMessage($customer_id, $postbox_id, $envelope_id, $message, APConstants::PUSH_MESSAGE_SCANS_TYPE);
            }

            // Insert data to completed table
            scans_api::insertCompleteItem($envelope_id, APConstants::SCAN_ITEM_COMPLETED_ACTIVITY_TYPE, APConstants::TRIGGER_BY_ADMIN, $completed_by);
        } elseif ($current_scan_type == '5') {
            // Marked completed for destroy item
            // Insert data to completed table
            $envelope = ci()->envelope_m->get_by_many(array(
                "id" => $envelope_id,
                "to_customer_id" => $customer_id
            ));

            if (!$envelope) {
                return array(
                    "status" => true,
                    'message' => ''
                );
            }

            //Cancel item scan flag
            if ($envelope->item_scan_flag == APConstants::OFF_FLAG) {
                scans_api::insertCompleteItem($envelope_id, APConstants::CANCEL_ITEM_SCAN_BY_SYSTEM_ACTIVITY_TYPE);
            } else {
                //Trash complete by admin
                scans_api::insertCompleteItem($envelope_id, APConstants::TRASH_COMPLETED_ACTIVITY_TYPE, APConstants::TRIGGER_BY_ADMIN, $completed_by);
            }

            // Remove request scan envelope activity
            if ($envelope->envelope_scan_flag === APConstants::OFF_FLAG) {
                ci()->envelope_m->update_by_many(array(
                    'id' => $envelope_id,
                    'to_customer_id' => $customer_id,
                    'envelope_scan_flag' => APConstants::OFF_FLAG
                ), array(
                    "envelope_scan_flag" => null,
                    'last_updated_date' => now()
                ));
            }

            // trigger storage nubmer report.
            scans_api::updateStorageStatus($envelope_id, $customer_id, $postbox_id, APUtils::getCurrentYear(), APUtils::getCurrentMonth(), $envelope->location_id, APConstants::OFF_FLAG);

            // Remove item scan envelope activity
            if ($envelope->item_scan_flag === APConstants::OFF_FLAG) {
                // Remove scan item activity
                ci()->envelope_m->update_by_many(array(
                    'id' => $envelope_id,
                    'to_customer_id' => $customer_id,
                    'item_scan_flag' => APConstants::OFF_FLAG
                ), array(
                    "item_scan_flag" => null,
                    "item_scan_date" => null,
                    'last_updated_date' => now()
                ));
            }

            // Remove direct shipping activity
            if ($envelope->direct_shipping_flag !== APConstants::ON_FLAG) {
                ci()->envelope_m->update_by_many(array(
                    'id' => $envelope_id,
                    'to_customer_id' => $customer_id,
                    'direct_shipping_flag' => APConstants::OFF_FLAG
                ), array(
                    "direct_shipping_flag" => null,
                    'last_updated_date' => now()
                ));
            }

            // Remove direct shipping activity
            if ($envelope->collect_shipping_flag !== APConstants::ON_FLAG) {
                ci()->envelope_m->update_by_many(array(
                    'id' => $envelope_id,
                    'to_customer_id' => $customer_id,
                    'collect_shipping_flag' => APConstants::OFF_FLAG
                ), array(
                    "collect_shipping_flag" => null,
                    "package_id" => null,
                    'last_updated_date' => now()
                ));
            }

            // Remove customs event activity
            ci()->envelope_customs_m->delete_by_many(array(
                'customer_id' => $customer_id,
                'envelope_id' => $envelope_id,
                'process_flag' => APConstants::OFF_FLAG
            ));

            // Call method to update trash flag
            if($envelope->trash_flag == APConstants::ENVELOPE_TRASH_BY_CUSTOMER_IN_TRASH_FOLDER){
                ci()->envelope_m->update_by_many(array(
                    'id' => $envelope_id,
                    'to_customer_id' => $customer_id
                ), array(
                    "trash_flag" => APConstants::ON_FLAG,
                    "trash_date" => now(),
                    'last_updated_date' => now(),
                    "storage_flag" => APConstants::OFF_FLAG,
                    "storage_date" => NULL,
                    "completed_flag" => APConstants::ON_FLAG,
                    "completed_date" => now()
                ));
            }else {
                ci()->envelope_m->update_by_many(array(
                    'id' => $envelope_id,
                    'to_customer_id' => $customer_id
                ), array(
                    "trash_flag" => APConstants::ENVELOPE_COMPLETED_TRASH_BY_ADMIN,
                    "trash_date" => now(),
                    'last_updated_date' => now(),
                    "storage_flag" => APConstants::OFF_FLAG,
                    "storage_date" => NULL,
                    "completed_flag" => APConstants::ON_FLAG,
                    "completed_date" => now()
                ));
            }
        }

        return array(
            "status" => true,
            'message' => ''
        );
    }
    
   /**
    * Save tracking number in todo list screen
    * @param type $envelope_id
    * @param type $tracking_number
    * @param type $shipping_services_id
    * @param type $no_tracking_flag
    * @param type $list_envelopes
    * @param type $isMarkCompletedShipping
    */
    public static function save_tracking_number($envelope_id, $tracking_number, $shipping_services_id, $no_tracking_flag = "0", $list_envelopes = null, $isMarkCompletedShipping = false){
        ci()->load->model('shipping/shipping_services_m');
        $envelope = ci()->envelope_m->get($envelope_id);
        $completed_by = APContext::getAdminIdLoggedIn();
        //-------Admin input tracking number and uncheck no tracking number flag checkbox-------
        if( (isset($tracking_number) && !empty($tracking_number)) && $no_tracking_flag == "0" ){
            //If this envelope is a collect shipping
            if(!empty($envelope->package_id)){
                //Get tracking number infor by package
                $check_shipping_tracking = ci()->envelope_shipping_tracking_m->get_by_many(array("package_id" => $envelope->package_id));
                //Get list envelope by package
                if(empty($list_envelopes)){
                    $envelopes = ci()->envelope_m->get_many_by_many(array(
                        "package_id" => $envelope->package_id
                    ));
                }
                else{
                    $envelopes = $list_envelopes;
                }
                
                //Add tracking number for each envelope in package    
                if($envelopes){
                    foreach ($envelopes as $row) {
                        //Save tracking number
                        ci()->envelope_shipping_tracking_m->saveTrackingNumber($row->id, $shipping_services_id, $tracking_number, $envelope->package_id);                       
                        //Update has tracking number in table envelope_m
                        ci()->envelope_m->update($row->id, array(
                            "tracking_number_flag" => APConstants::ON_FLAG
                        ));
                    }
                }
            }
            //This envelope is direct shipping
            else {
                //Save tracking number
                ci()->envelope_shipping_tracking_m->saveTrackingNumber($envelope_id, $shipping_services_id, $tracking_number, null); 
                //Update has tracking number in table envelope_m
                ci()->envelope_m->update($envelope_id, array(
                    "tracking_number_flag" => APConstants::ON_FLAG
                ));
            }
            //Add activity complete tracking number by admin
            scans_api::insertCompleteItem($envelope_id, APConstants::COMPLETED_TRACKING_NUMBER_ACTIVITY_TYPE, APConstants::TRIGGER_BY_ADMIN, $completed_by);
            //Get shipping info of envelope
            $item = ci()->envelope_m->getInforItemTracking($envelope_id, $shipping_services_id);
            //If does not existe tracking number infor of package
            if( empty($check_shipping_tracking) ){    
            //Send email about tracking number for this envelope
            if(!empty($item)){
                $tracking_number_url = "";
                //Replace tracking number to place holder in tracking number url. If does not existe place holder, add tracking number to the end of url
                if( !empty($item->tracking_number_url) && (strpos($item->tracking_number_url, '{{placeholder}}') !== false) ) {
                    $tracking_number_url = "<a href= '".str_replace('{{placeholder}}', $tracking_number, $item->tracking_number_url)."'>".str_replace('{{placeholder}}', $tracking_number, $item->tracking_number_url)."</a>";
                }
                else if( !empty($item->tracking_number_url) ){
                    $tracking_number_url = "<a href= '".trim($item->tracking_number_url)."'>".trim($item->tracking_number_url)."</a>";
                }
                //Send email with tracking number url to customer
                $data_send_email = array(
                    "slug" => APConstants::envelope_shipping_tracking_number,
                    "to_email" => $item->email,
                    // Replace content
                    "full_name"        => $item->email,
                    "from_customer"    => $item->from_customer_name,
                    "shipping_service" => $item->shipping_service_name,
                    "tracking_number"  => $tracking_number,
                    "tracking_number_url" => $tracking_number_url
                );
                // Send email
                MailUtils::sendEmailByTemplate($data_send_email);
            }
        //If existe tracking number infor of package but old tracking number is not equal new tracking number that admin input   
        } else if( !empty($check_shipping_tracking) && ($check_shipping_tracking->tracking_number != $tracking_number) ) {
            //Add activity update new tracking number
            scans_api::insertCompleteItem($envelope_id, APConstants::UPDATE_TRACKING_NUMBER_ACTIVITY_TYPE, APConstants::TRIGGER_BY_ADMIN, $completed_by);
            //Send email about tracking number for this envelope
            if(!empty($item)){
                $old_shipping_service = ci()->shipping_services_m->get_by("id",$check_shipping_tracking->shipping_services_id);
                //Replace tracking number to place holder in tracking number url. If does not existe place holder, add tracking number to the end of url
                $tracking_number_url = "";
                if( !empty($item->tracking_number_url) && (strpos($item->tracking_number_url, '{{placeholder}}') !== false) ) {
                    $tracking_number_url = "<a href= '".str_replace('{{placeholder}}', $tracking_number, $item->tracking_number_url)."'>".str_replace('{{placeholder}}', $tracking_number, $item->tracking_number_url)."</a>";
                }
                else if( !empty($item->tracking_number_url) ){
                    $tracking_number_url = "<a href= '".$item->tracking_number_url."'>".$item->tracking_number_url."</a>";
                }	

                $data_send_email = array(
                    "slug" => APConstants::update_shipping_tracking_number,
                    "to_email" => $item->email,
                    // Replace content
                    "full_name"        => $item->email,
                    "from_customer"    => $item->from_customer_name,
                    "shipping_service" => $item->shipping_service_name,
                    "old_shipping_service" => is_object($old_shipping_service) ? $old_shipping_service->name : "",
                    "tracking_number"  => $tracking_number,
                    "old_tracking_number"  => $check_shipping_tracking->tracking_number,
                    "tracking_number_url" => $tracking_number_url
                );
                // Send email
                MailUtils::sendEmailByTemplate($data_send_email);
            }
        }
        //--------Admin check no_tracking_number checkbox---------
        } else if ($no_tracking_flag == "1") {
            //Get shipping service info
            $shipping_service = shipping_api::getShippingServiceInfo($shipping_services_id);
            $haveTrackingInfo = empty($shipping_service->tracking_information_flag) ? false : true;
            //If this is collect shipping        
            if(!empty($envelope->package_id)){
                //Get list envelope of this package
                if(empty($list_envelopes)){
                    $envelopes = ci()->envelope_m->get_many_by_many(array(
                        "package_id" => $envelope->package_id
                    ));
                }
                else{
                    $envelopes = $list_envelopes;
                }
                //Add tracking number for each envelopes in package
                if(!empty($envelopes)){
                    foreach ($envelopes as $row) {
                        //Save tracking number
                        ci()->envelope_shipping_tracking_m->saveTrackingNumber($row->id, $shipping_services_id, '', $envelope->package_id); 
                        //Update tracking number flag
                        ci()->envelope_m->update($row->id, array(
                            "tracking_number_flag" => ($isMarkCompletedShipping && $haveTrackingInfo) ? APConstants::OFF_FLAG : APConstants::ON_FLAG
                        ));
                    }
                }
            }
            //If this is direct shipping
            else {
                //Save tracking number
                ci()->envelope_shipping_tracking_m->saveTrackingNumber($envelope_id, $shipping_services_id, '', null); 
                //Update tracking number flag
                ci()->envelope_m->update($envelope_id, array(
                    "tracking_number_flag" => ($isMarkCompletedShipping && $haveTrackingInfo) ? APConstants::OFF_FLAG : APConstants::ON_FLAG
                ));
            }
            //Add tracking number activity
            if (!$isMarkCompletedShipping) {
                scans_api::insertCompleteItem($envelope_id, APConstants::NO_TRACKING_NUMBER_ACTIVITY_TYPE, APConstants::TRIGGER_BY_ADMIN, $completed_by);
            }
        }
    }
    
    /*
     * @param: $envelope is object of envelope
     * Return array of shipping address and list shipping services
     */
    public static function get_shipping_services_by_envelope($envelope){
        
        if(empty($envelope)){
            return array("customer_address" => "", "listShippingServices" => "");
        }
        
        $customer_address = shipping_api::getShippingAddressByEnvelope($envelope->to_customer_id, $envelope->id);
        $envelope_shipping_check = ci()->envelope_shipping_m->get_by_many(array(
            "customer_id" => $envelope->to_customer_id,
            "envelope_id" => $envelope->id,
            "postbox_id" => $envelope->postbox_id
        ));
        // Override shipping address
        if (!empty($envelope_shipping_check) && !empty($envelope_shipping_check->shipping_country)) {
            $customer_address->shipment_address_name = $envelope_shipping_check->shipping_name;
            $customer_address->shipment_city = $envelope_shipping_check->shipping_city;
            $customer_address->shipment_company = $envelope_shipping_check->shipping_company;
            $customer_address->shipment_country = $envelope_shipping_check->shipping_country;
            $customer_address->shipment_postcode = $envelope_shipping_check->shipping_postcode;
            $customer_address->shipment_region = $envelope_shipping_check->shipping_region;
            $customer_address->shipment_street = $envelope_shipping_check->shipping_street;
            $customer_address->shipment_phone_number = $envelope_shipping_check->shipment_phone_number;
        }
        
        $listShippingServices = array();
        if($envelope->location_id){
            $location = addresses_api::getLocationByID($envelope->location_id);
            $shippingServiceIDs = isset($location->available_shipping_services) ? $location->available_shipping_services : '';
            $shippingServiceIDs = explode(',', $shippingServiceIDs);
            $shipping_service_type = '';
            if ($customer_address->shipment_country == $location->country_id) {
                $shipping_service_type = APConstants::SHIPPING_SERVICE_TYPE_NATIONAL;
            } else {
                $shipping_service_type = APConstants::SHIPPING_SERVICE_TYPE_INTERNATIONAL;
            }
            $listShippingServices = shipping_api::getListShippingServicesByIDs($shippingServiceIDs, $shipping_service_type, true);
        }
        
        return array("customer_address" => $customer_address, "listShippingServices" => $listShippingServices);
        
    }

}