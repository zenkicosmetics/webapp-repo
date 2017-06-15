<?php defined('BASEPATH') or exit('No direct script access allowed');

class export {
    public function __construct() {
        ci()->load->model(array(
            'invoices/invoice_detail_m',
            'settings/countries_m',
            'customers/customer_m',
            'invoices/invoice_summary_m',
            'addresses/customers_address_m',
            'payment/payment_m',
            'invoices/invoices_pdf_job_hist_m',
            'invoices/invoice_summary_by_location_m',
            'invoices/invoice_summary_by_user_m',
            'phones/phone_invoice_by_location_m',
        ));
        
        ci()->load->library(array(
            'account/account_api',
            "addresses/addresses_api"
        ));
        
        ci()->load->library('S3');
        
        ci()->lang->load('invoices/invoices');
    }
    
    /**
     * export pdf.
     *
     * @param unknown_type $target_date
     */
    public function export_invoice($invoice_code, $customer_id = '') {
        if (empty($customer_id)) {
            $customer_id = APContext::getCustomerCodeLoggedIn();
        }
        // Gets customer infor.
        $customer = ci()->customer_m->get_by('customer_id', $customer_id);

        if (empty($customer)) {
            return "Customer is not existed";
        }

        // chekc enterprise customer
        $list_customer_id = array($customer_id);
        $is_enterprise_customer = ($customer->account_type == APConstants::ENTERPRISE_CUSTOMER)? true : false ;
        
        // Gets invoice data.
        if($is_enterprise_customer){
            if(empty($customer->parent_customer_id)){
                // Gets all users of customer enterprise
                $list_customer_ids = array($customer_id);
                $list_user = ci()->customer_m->get_many_by_many(array(
                    'parent_customer_id' => $customer_id,
                    //'activated_flag' => APConstants::ON_FLAG
                ));
                foreach($list_user as $user){
                    $list_customer_ids[] = $user->customer_id;
                }

                //$list_customer_ids = account_api::getListUserIdOfCustomer($customer_id);
                $list_customer_id = implode(',', $list_customer_ids);
                $parent_invoice_summary = ci()->invoice_summary_m->get_by_many(array(
                    'invoice_code' => $invoice_code,
                    'customer_id' => $customer_id
                ));
                if (empty($parent_invoice_summary)) {
                    return "Invoice summary does not exist";
                }

                $invoice_month = substr($parent_invoice_summary->invoice_month, 0, 6);
                $invoice_type = $parent_invoice_summary->invoice_type;

                // if invoice is manual
                if($invoice_type == "2"){
                    // Gets next invoice
                    $rows = ci()->invoice_summary_m->get_many_by_many(array(
                        'invoice_code' => $invoice_code,
                        'customer_id' => $customer_id
                    ));

                    // get phone invoice
                    $phone_invoices = ci()->phone_invoice_by_location_m->get_invoice_paging(array(
                        'invoice_code' => $invoice_code,
                        'customer_id' => $customer_id
                    ), 0, 10000, '','', 'location_id');
                }else{
                    // Gets next invoice
                    $rows = ci()->invoice_summary_m->get_many_by_many(array(
                        "LEFT(invoice_month,6) = '".$invoice_month."' " => null,
                        'customer_id IN ('.$list_customer_id.')' => null,
                        "(invoice_type is null OR invoice_type <> 2)" => null
                    ));

                    //$rows = array_merge($parent_invoice_summary, $invoice_summary_by_user);
                    // get phone invoice
                    $phone_invoices = ci()->phone_invoice_by_location_m->get_invoice_paging(array(
                        "LEFT(invoice_month,6) = '".$invoice_month."' " => null,
                        'customer_id IN ('.$list_customer_id.')' => null
                    ), 0, 10000, '','', 'location_id');
                }
            }
            // if customer is user of enterprise.
            else{
                // Gets next invoice
                $rows = ci()->invoice_summary_by_user_m->get_many_by_many(array(
                    'invoice_code' => $invoice_code,
                    'customer_id' => $customer_id
                ));

                // get phone invoice
                $phone_invoices = ci()->phone_invoice_by_location_m->get_invoice_paging(array(
                    'invoice_code' => $invoice_code,
                    'customer_id' => $customer_id
                ), 0, 10000, '','', 'location_id');
            }
        }
        // normal customer of clevvermail
        else{
            // Gets next invoice
            $rows = ci()->invoice_summary_m->get_many_by_many(array(
                'invoice_code' => $invoice_code,
                'customer_id' => $customer_id
            ));

            // get phone invoice
            $phone_invoices = ci()->phone_invoice_by_location_m->get_invoice_paging(array(
                'invoice_code' => $invoice_code,
                'customer_id' => $customer_id
            ), 0, 10000, '','', 'location_id');
        }

        // gets decimal separator
        $decimal_separator = ci()->customer_m->get_standard_setting_decimal_separator($customer_id);
        if (empty($rows) && empty($phone_invoices)) {
            return "Invoice summary does not exist";
        }

        $invoice_file_path = Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'invoices/' . $customer->customer_code . '_' . $invoice_code . '.pdf';

        if (!is_dir(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . "invoices/")) {
            mkdir(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . "invoices/", 0777, TRUE);
            chmod(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . "invoices/", 0777);
        }

        // Check exist file
        $invoice_job_check = ci()->invoices_pdf_job_hist_m->get_by_many(array(
            'invoice_code' => $invoice_code,
            'customer_id' => $customer_id
        ));
        if (!empty($invoice_job_check)) {

            $invoice_file_path = '';
            if (!empty($invoice_job_check->local_filepath)) {
                $invoice_file_path = $invoice_job_check->local_filepath;
                log_message(APConstants::LOG_DEBUG, "Get invoice file path from local file:" . $invoice_file_path);
                ci()->invoice_summary_m->update_by_many(array(
                    'invoice_code' => $invoice_code,
                    'customer_id' => $customer_id
                ), array(
                    "invoice_file_path" => $invoice_file_path,
                    "update_flag" => 0
                ));
                return $invoice_file_path;
            }
            if (!empty($invoice_job_check->amazon_filepath)) {
                APUtils::download_amazon_file_tolocal($invoice_job_check->amazon_filepath, $invoice_file_path);
                log_message(APConstants::LOG_DEBUG, "Get invoice file path from amazon file:" . $invoice_file_path);
                ci()->invoice_summary_m->update_by_many(array(
                    'invoice_code' => $invoice_code,
                    'customer_id' => $customer_id
                ), array(
                    "invoice_file_path" => $invoice_file_path,
                    "update_flag" => 0
                ));
                return $invoice_file_path;
            }
        }

        // Gets result data.
        $result_data =  $this->get_invoice_datas($customer, $invoice_code, $invoice_file_path, $rows, $phone_invoices);
        $result_data['decimal_separator'] = $decimal_separator;
        $result_data['is_enterprise_customer'] = $is_enterprise_customer;
        
        // Gets template html
        $html = ci()->load->view("invoices/template", $result_data, TRUE);
        
        // output pdf file.
        $this->output_pdf_file($invoice_file_path, $html);
        
        // upload S3
        $default_bucket_name = ci()->config->item('default_bucket');
        $amazon_relate_path = $customer->customer_id . '/invoices/' . $customer->customer_code . '_' . $invoice_code . '.pdf';
        // $amazon_relate_path = $customer->customer_id . '/' . $customer->customer_code . '_' . $invoice_code . '.pdf';
        log_message(APConstants::LOG_DEBUG, "Default bucket name: " . $default_bucket_name . '. Upload to folder:' . $amazon_relate_path);
        try {
            $result = S3::putObjectFile($invoice_file_path, $default_bucket_name, $amazon_relate_path, S3::ACL_PRIVATE);
            log_audit_message(APConstants::LOG_DEBUG, json_encode($result));

            $invoice_pdf_job_check = ci()->invoices_pdf_job_hist_m->get_by_many(array(
                "invoice_code" => $invoice_code
            ));

            // Insert data to job hist
            if (empty($invoice_pdf_job_check)) {
                ci()->invoices_pdf_job_hist_m->insert(array(
                    "invoice_code" => $invoice_code,
                    "customer_id" => $customer_id,
                    "local_filepath" => $invoice_file_path,
                    "amazon_filepath" => $amazon_relate_path,
                    "created_date" => now(),
                    "upload_s3_date" => now(),
                    "send_invoice_flag" => '0',
                    "send_invoice_date" => null
                ));
            }
        } catch (Exception $e) {
            $message = 'Have error when upload file to S3';
            if ($e) {
                $message = $message . $e->getMessage();
            }
            log_message(APConstants::LOG_ERROR, $message);
        }
        ci()->invoice_summary_m->update_by_many(array(
            'invoice_code' => $invoice_code,
            'customer_id' => $customer_id
        ), array(
            "invoice_file_path" => $invoice_file_path,
            "update_flag" => 0
        ));
        return $invoice_file_path;
    }
    
    /**
     * export pdf.
     *
     * @param unknown_type $target_date
     */
    public function export_invoice_user($invoice_code, $customer_id) {
        // Gets customer infor.
        $customer = APContext::getCustomerByID($customer_id);
        if (empty($customer)) {
            echo "Customer is not existed";
            return ;
        }
        
        // Gets next invoice
        $rows = ci()->invoice_summary_by_user_m->get_many_by_many(array(
            'invoice_code' => $invoice_code,
            'customer_id' => $customer_id
        ));

        // get phone invoice
        $phone_invoices = ci()->phone_invoice_by_location_m->get_invoice_paging(array(
            'invoice_code' => $invoice_code,
            'customer_id' => $customer_id
        ), 0, 10000, '','', 'location_id');

        // gets decimal separator
        $decimal_separator = ci()->customer_m->get_standard_setting_decimal_separator($customer_id);
        if (empty($rows) && empty($phone_invoices)) {
            echo "not found";
            return "Invoice summary does not exist";
        }

        $invoice_file_path = Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'invoices/' . $customer->customer_code . '_' . $invoice_code . '.pdf';

        if (!is_dir(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . "invoices/")) {
            mkdir(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . "invoices/", 0777, TRUE);
            chmod(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . "invoices/", 0777);
        }

        // Check exist file
        $invoice_job_check = ci()->invoices_pdf_job_hist_m->get_by_many(array(
            'invoice_code' => $invoice_code,
            'customer_id' => $customer_id
        ));
        if (!empty($invoice_job_check)) {
            $invoice_file_path = '';
            if (!empty($invoice_job_check->local_filepath)) {
                $invoice_file_path = $invoice_job_check->local_filepath;
                log_message(APConstants::LOG_DEBUG, "Get invoice file path from local file:" . $invoice_file_path);
                ci()->invoice_summary_m->update_by_many(array(
                    'invoice_code' => $invoice_code,
                    'customer_id' => $customer_id
                ), array(
                    "invoice_file_path" => $invoice_file_path,
                    "update_flag" => 0
                ));
                return $invoice_file_path;
            }
            if (!empty($invoice_job_check->amazon_filepath)) {
                APUtils::download_amazon_file_tolocal($invoice_job_check->amazon_filepath, $invoice_file_path);
                log_message(APConstants::LOG_DEBUG, "Get invoice file path from amazon file:" . $invoice_file_path);
                ci()->invoice_summary_m->update_by_many(array(
                    'invoice_code' => $invoice_code,
                    'customer_id' => $customer_id
                ), array(
                    "invoice_file_path" => $invoice_file_path,
                    "update_flag" => 0
                ));
                return $invoice_file_path;
            }
        }

        // Gets address of customer
        $address = ci()->customers_address_m->get_by('customer_id', $customer_id);
        if ($address && is_numeric($address->invoicing_country)) {
            $country = ci()->countries_m->get($address->invoicing_country);
            if (!empty($country)) {
                $address->invoicing_country = $country->country_name;
            }
        }
        
        $invoices = array();
        $target_date = "";
        foreach($rows as $row){
            $location = addresses_api::getLocationByID($row->location_id);
            $row->location_name = $location->location_name;
            $target_date = APUtils::displayDate(APUtils::getLastDayOfMonth($row->invoice_month."01"));
            $invoices[] = $row;
        }
        $period_of_service = date("F, Y", strtotime($target_date));
            
        // Gets result data.
        $result_data = array(
            'invoices' => $invoices,
			'phone_invoices' => $phone_invoices,
            'target_date' => $target_date,
            'customer' => $customer,
            'address' => $address,
            'invoice_code' => $invoice_code,
            'period_of_service' => $period_of_service
        );
        
        //$result_data =  $this->get_invoice_datas($customer, $invoice_code, $invoice_file_path, $rows, $phone_invoices);
        $result_data['decimal_separator'] = $decimal_separator;
        
        // Gets template html
        $html = ci()->load->view("invoices/template_user_enteprise", $result_data, TRUE);
        
        // output pdf file.
        $this->output_pdf_file($invoice_file_path, $html);
        
        // upload S3
        $default_bucket_name = ci()->config->item('default_bucket');
        $amazon_relate_path = $customer->customer_id . '/invoices/' . $customer->customer_code . '_' . $invoice_code . '.pdf';
        // $amazon_relate_path = $customer->customer_id . '/' . $customer->customer_code . '_' . $invoice_code . '.pdf';
        log_message(APConstants::LOG_DEBUG, "Default bucket name: " . $default_bucket_name . '. Upload to folder:' . $amazon_relate_path);
        try {
            $result = S3::putObjectFile($invoice_file_path, $default_bucket_name, $amazon_relate_path, S3::ACL_PRIVATE);
            log_audit_message(APConstants::LOG_DEBUG, json_encode($result));

            $invoice_pdf_job_check = ci()->invoices_pdf_job_hist_m->get_by_many(array(
                "invoice_code" => $invoice_code
            ));

            // Insert data to job hist
            if (empty($invoice_pdf_job_check)) {
                ci()->invoices_pdf_job_hist_m->insert(array(
                    "invoice_code" => $invoice_code,
                    "customer_id" => $customer_id,
                    "local_filepath" => $invoice_file_path,
                    "amazon_filepath" => $amazon_relate_path,
                    "created_date" => now(),
                    "upload_s3_date" => now(),
                    "send_invoice_flag" => '0',
                    "send_invoice_date" => null
                ));
            }
        } catch (Exception $e) {
            $message = 'Have error when upload file to S3';
            if ($e) {
                $message = $message . $e->getMessage();
            }
            log_message(APConstants::LOG_ERROR, $message);
        }
        ci()->invoice_summary_m->update_by_many(array(
            'invoice_code' => $invoice_code,
            'customer_id' => $customer_id
        ), array(
            "invoice_file_path" => $invoice_file_path,
            "update_flag" => 0
        ));
        return $invoice_file_path;
    }

    /**
     * export refund report.
     *
     * @param unknown $invoice_id
     */
    public function export_credit_by_location($invoice_code, $customer_id, $location_id) {
        // Gets customer infor.
        $customer = ci()->customer_m->get_by('customer_id', $customer_id);
        if (empty($customer) || empty($location_id)) {
            return "Customer is not existed";
        }

        $is_enterprise_customer = ($customer->account_type == APConstants::ENTERPRISE_CUSTOMER)? true : false ;
        
        // Gets next invoice
        $rows = ci()->invoice_summary_m->get_many_by_many(array(
            'invoice_code' => $invoice_code,
            'customer_id' => $customer_id
        ));

        // gets decimal separator
        $decimal_separator = ci()->customer_m->get_standard_setting_decimal_separator($customer_id);

        if (!is_dir("uploads/temp/")) {
            mkdir("uploads/temp/", 0777, TRUE);
            chmod("uploads/temp/", 0777);
        }
        $invoice_file_path = "uploads/temp/" . $customer->customer_code.'_'.$location_id . '_' . uniqid() . '.pdf';

        // Gets result data invoice
        $result_data = $this->get_invoice_datas($customer, $invoice_code, $invoice_file_path, $rows, null, $location_id);
        $result_data['decimal_separator'] = $decimal_separator;
        $result_data['is_enterprise_customer'] = $is_enterprise_customer;
        
        // Gets template html
        $html = ci()->load->view("invoices/template", $result_data, TRUE);
        
        // output pdf file.
        $this->output_pdf_file($invoice_file_path, $html);

        return $invoice_file_path;
    }

    /**
     * Export all pdf file
     */
    public function export_all_pdf($customer_id = '') {
        // Load pdf library
        ci()->load->model('invoices/invoices_pdf_job_hist_m');
        $list_invoice_summary = ci()->invoices_pdf_job_hist_m->getAllPendingInvoice($customer_id);

        foreach ($list_invoice_summary as $invoice) {
            // export invoice.
            echo $invoice->invoice_code . "==> Start generate invoice file:" . $invoice->invoice_code . '<br/>';
            $invoice_file_path = $this->export_invoice($invoice->invoice_code, $invoice->customer_id);
            echo $invoice->invoice_code . "==> End generate invoice File: " . $invoice_file_path . '<br/>';
            ob_flush();
            flush();
        }
    }

    /**
     * export cusoms invoices.
     *
     * @param unknown_type $target_date
     */
    public function export_custom_invoice($envelope_id, $list_tracking_services = array(), $override_flag = false) {
        ci()->load->model('mailbox/postbox_m');
        ci()->load->model('addresses/location_m');
        ci()->load->model('scans/envelope_m');
        ci()->load->model('settings/countries_m');
        ci()->load->model('customers/customer_m');
        ci()->load->model('addresses/customers_address_m');

        ci()->load->model('mailbox/envelope_customs_m');
        ci()->load->model('mailbox/envelope_customs_detail_m');

        ci()->lang->load('invoices/invoices');
        
        ci()->load->library('scans/scans_api');
        ci()->load->library('addresses/addresses_api');

        $envelope = ci()->envelope_m->get_by('id', $envelope_id);
        if (empty($envelope)) {
            return "Envelope is not existed";
        }
        $customer_id = $envelope->to_customer_id;
        $envelope_code = $envelope->envelope_code;
        $postbox_id = $envelope->postbox_id;

        if (empty($customer_id)) {
            return "Customer is not existed";
        }
        // Gets customer infor.
        $customer = ci()->customer_m->get_by('customer_id', $customer_id);
        if (empty($customer)) {
            return "Customer is not existed";
        }

        // Get customer address information
        $address = scans_api::getSelectedForwardingAddressOfEnvelopes($customer_id, $envelope->shipping_address_id);
        if (empty($address)) {
            return "Customer address is not existed";
        }
        if (is_numeric($address->shipment_country)) {
            $country = ci()->countries_m->get($address->shipment_country);
            if ($country) {
                $address->shipment_country = $country->country_name;
            }
        }
        
        // get invoice address
        $invoice_address = addresses_api::getCustomerAddress($customer_id);
        if (empty($invoice_address)) {
            return "Customer address is not existed";
        }
        if (is_numeric($invoice_address->invoicing_country)) {
            $country = ci()->countries_m->get($invoice_address->invoicing_country);
            if ($country) {
                $invoice_address->invoicing_country = $country->country_name;
            }
        }

        $list_custom_items = '';
        
        // Get envelope customs
        $envelope_customs = EnvelopeUtils::getEnvelopeCustoms($envelope_id);
        if($envelope_customs){
            $customs_id = $envelope_customs->id;
            $list_custom_items = ci()->envelope_customs_detail_m->get_custom_detail_by($customs_id);
        }
        
        if(empty($list_custom_items)){
            echo "there is no data.";
            return;
        }

        // Check exist file
        $invoice_file_path = Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'invoices/CustomerInvoice_' . $envelope_code . '.pdf';
        if (!$override_flag && file_exists($invoice_file_path)) {
            //return $invoice_file_path;
        }

        if (!is_dir(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . "invoices/")) {
            mkdir(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . "invoices/", 0777, TRUE);
            chmod(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . "invoices/", 0777);
        }

        // Target date
        $target_date = APUtils::convert_timestamp_to_date(now(), 'd.m.Y');

        // Get location of postbox
        $location_item = '';
        // Get postbox information
        $postboxs = ci()->postbox_m->get_postbox($postbox_id);
        $postbox = $postboxs [0];
        if (empty($postbox)) {
            return 'Postbox did not exist.';
        }

        $location_available_id = $postbox->location_available_id;
        if (empty($location_available_id)) {
            return 'Location of postbox did not exist.';
        }

        // Get detail location information
        $location = ci()->location_m->get_by_many(array(
            "id" => $location_available_id
        ));
        if (!empty($location)) {
            $location_item = $location->location_name;
        }

        // Load pdf library
        ci()->load->library('pdf02');

        // create new PDF document
        // $pdf = new Tocpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf = ci()->pdf02->createObject();
        $pdf->setFontSubsetting(true);
        $pdf->SetFont('arial', '', 9, '', 'false');

        // set document information
        // Set common information
        $pdf->SetTitle(Settings::get(APConstants::INSTANCE_OWNER_COMPANY_CODE));
        $pdf->SetAuthor(Settings::get(APConstants::INSTANCE_OWNER_COMPANY_CODE));

        // disable header and footer
        ci()->pdf02->setPrintHeader(true);
        ci()->pdf02->setPrintFooter(false);

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

        $html = ci()->load->view("invoices/template_custom_invoice", array(
            'location_item' => $location_item,
            'list_custom_items' => $list_custom_items,
            'target_date' => $target_date,
            'customer' => $customer,
            'address' => $address,
            "invoice_address" => $invoice_address,
            "envelope_customs" => $envelope_customs,
            "postbox" => $postbox,
            "list_tracking_services" => $list_tracking_services
        ), TRUE);

        $pdf->AddPage();
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, 'J', true);

        $invoice_file_path_01 = Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'invoices/CustomerInvoice_' . $envelope_code . '_01.pdf';
        $invoice_file_path_02 = Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'invoices/CustomerInvoice_' . $envelope_code . '_02.pdf';
        $invoice_file_path_03 = Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'invoices/CustomerInvoice_' . $envelope_code . '_03.pdf';
        $pdf->Output($invoice_file_path_01, 'F');
        copy($invoice_file_path_01, $invoice_file_path_02);
        copy($invoice_file_path_01, $invoice_file_path_03);
        
        $list_file_output = array($invoice_file_path_01, $invoice_file_path_02, $invoice_file_path_03);
        // Merge file
        APUtils::mergePDFfiles($list_file_output, $invoice_file_path, 'P');
        
        // Delete old file
        foreach ($list_file_output as $item) {
            if (file_exists($item)) {
                unlink($item);
            }
        }
        
        return $invoice_file_path;
    }
    
    /**
     * export report location (#1072 add location report generation)
     *
     * @param int       $location_id
     * @param string    $year
     * @param string    $month
     * @param int       $cost_of_location_advertising
     * @param int       $hardware_amortization
     * @param int       $location_external_receipts
     * @param int       $current_open_balance
     * 
     */
    public function export_location_report($location_id,$year,$month, 
            $cost_of_location_advertising, $hardware_amortization, $location_external_receipts, $current_open_balance,
            $total_invoiceable_so_far, $total_invoiced_so_far,$invoices_written_this_month, $total_payments_made_till_end_of_this_month)
    {
    	// load model
    	ci()->load->model('report/location_report_m');
        ci()->load->model('addresses/location_m');
        ci()->load->model('report/report_by_location_m');
        ci()->load->model('report/report_by_total_m');
        ci()->load->model('invoices/invoice_summary_m');

        // load libraries
        ci()->load->library(array(
            'invoices/invoices_api',
            'report/report_api',
            'settings/settings',
            'pdf01',
            'pChart/pChartBasic',
            'price/price_api',
        ));
        
        // Check exist file for location report total case
        $location_name = '';
        
        if(empty($location_id)){
            $location_id = 0;
            $location_name = 'Total'; 
            $admin_location_report_file_path = Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'admin_report/location/TotalReport_' . $year .$month . '.pdf';  
        }else{
             // Check exist file for location report case
            $location= ci()->location_m->get_by('id', $location_id);
            $location_name = ucfirst(strtolower(preg_replace('/\s+/', '', $location->location_name)));
            $admin_location_report_file_path = Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'admin_report/location/Report_' . $location_name . '_' . $year .$month . '.pdf';   
        }
       
        // Set permission for folder for save file's location report
        if (!is_dir(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . "admin_report/location/")) {
    		mkdir(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . "admin_report/location/", 0777, TRUE);
    		chmod(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . "admin_report/location/", 0777);
    	}

        // Get period
        $year_month =  $year . "-" . $month ; 
        $period = date("F Y", strtotime($year_month));
        
   		$array_condition = array(
    			'location_id' => $location_id,
    			'year' =>  $year,
    			'month' =>  $month );

    	//get info location report
    	$location_reports =  ci()->location_report_m->get_by_many($array_condition);
        
        /* 
        * Save(add or update) data into location_report table
        */
        //If exists $location reports then update data for change, if not add new record data
        if(!empty($location_reports)){
            ci()->location_report_m->update_by_many(array(
                    'id' => $location_reports->id,
                    'location_id' => $location_reports->location_id,
                    'year' => $location_reports->year,
                    'month' => $location_reports->month,
                ), array(
                    'advertising_cost' =>$cost_of_location_advertising,
                    'hardware_cost' => $hardware_amortization,
                    'location_external_cost' => $location_external_receipts,
                    'current_open_balance' => $current_open_balance,
                    'total_invoiceable_so_far' => $total_invoiceable_so_far,
                    'total_invoiced_so_far' => $total_invoiced_so_far,
                    'invoices_written_this_month' => $invoices_written_this_month,
                    'total_payments_made_till_end_of_this_month' => $total_payments_made_till_end_of_this_month,
                    'file_path' => $admin_location_report_file_path
            ));
        } else {
            ci()->location_report_m->insert(array(
                'location_id' => $location_id,
                'year' => $year,
                'month' => $month,
                'advertising_cost' =>$cost_of_location_advertising,
                'hardware_cost' => $hardware_amortization,
                'location_external_cost' => $location_external_receipts,
                'current_open_balance' => $current_open_balance,
                'total_invoiceable_so_far' => $total_invoiceable_so_far,
                'total_invoiced_so_far' => $total_invoiced_so_far,
                'invoices_written_this_month' => $invoices_written_this_month,
                'total_payments_made_till_end_of_this_month' => $total_payments_made_till_end_of_this_month,
                'file_path' => $admin_location_report_file_path,
                'created_date' => now()
             ));
        } 
        
        $locations = array();
        // Gets locations
        if(APContext::isAdminUser()){
            $firstItem = new stdClass();
            $firstItem->id = "";
            $firstItem->location_name = "Total";
            $locations[] = $firstItem;
        }
        $locations_access = APUtils::loadListAccessLocation();
        foreach($locations_access as $la){
            $locations[] = $la;
        }
        
        // get month for report
        $report_month = $year . $month;
        if (empty($report_month)) {
            $report_month = date("Ym", now());
        }
        
        if (empty($location_id)) {
            foreach ($locations as $item_location) {
                $location_id = $item_location->id;
                break;
            }
        }
        
        $selected_location = null;
        foreach ($locations as $item_location) {
            if ($item_location->id == $location_id) {
                $selected_location = $item_location;
            }
        }
        
        // get receipt month.
        $receiptMonth = $month . '.' . $year;
        if (empty($month) && empty($year)) {
            $receiptMonth = date("m.Y", now());
        }
        $partner_id = "";
        if(!empty($selected_location) && !empty($selected_location->partner_id)){
            $partner_id = $selected_location->partner_id;
        }
        
        if (!empty($location_id)) {
            // #481 location selection.
            APContext::updateLocationUserSetting($location_id);
            
            // do re-calculate
            //if($report_month == date("Ym", now()) || (APUtils::isFirstDayOfMonth() && $report_month == date('Ym', strtotime('last month')) )){
            //    invoices_api::updateInvoiceSummaryTotalByLocation($report_month, '', false);
            //}
        }else{
            // do re-calculate
            //if($report_month == date("Ym", now()) || (APUtils::isFirstDayOfMonth() && $report_month == date('Ym', strtotime('last month')) )){
            //    invoices_api::updateInvoiceSummaryTotalByLocation($report_month, '', true);
            //}
        }
        
        // Location report total
        $invoice = null;
        $invoice_by_total = null;
        if(empty($location_id)){
            $invoice_by_location = ci()->report_by_location_m->get_invoice_by_month($report_month);
            $invoice_by_total = ci()->report_by_total_m->get_by('invoice_month', $report_month);
            
            // get summary total
            $total_invoice = ci()->invoice_summary_m->sum_by_many(array(
                "left(invoice_month,6)" => $report_month
            ), 'total_invoice');

             // count all manual invoice 
            $total_manual_invoice = ci()->invoice_summary_m->count_by_many(array(
                "left(invoice_month,6)" => $report_month,
                "invoice_type" => 2,
                "total_invoice <>" => 0
            ));
        }else{
            $invoice_by_location = ci()->report_by_location_m->get_by_many(array(
                "left(invoice_month, 6) =" => $report_month,
                "location_id" => $location_id
            ));

            $invoice = ci()->invoice_summary_by_location_m->summary_by_location($location_id, $report_month, true);

            // count all manual invoice 
            $total_manual_invoice = ci()->invoice_summary_by_location_m->count_manual_invoices_by($report_month, $location_id, true);
        }

        // Get currency rate and currency short
        $currency_short = APUtils::get_currency_short_in_user_profiles();
        $currency_rate = APUtils::get_currency_rate_in_user_profiles();
        $decimal_separator = APUtils::get_decimal_separator_in_user_profiles();
        $currency = APUtils::get_currency_user_profiles();
        $last_update_date = APUtils::viewDateFormat($currency->last_updated_date,APConstants::DATE_TIME_06);
      
        // file name char bar
        if(empty($location_id)){
            //var/www/clevvermail_webapp/shared/uploads
            $tmp_image_chart_file_name = "uploads/images/tmp/bar_chart_location_report_total_". $year .$month .".png";
        }else{
            ///var/www/clevvermail_webapp/shared/
             $tmp_image_chart_file_name = "uploads/images/tmp/bar_chart_location_report_". $location_name . '_' . $year .$month  .".png";
        }

        // Data of chart bar
        $enddate = strtotime($report_month.'28');
        
        $start = date("F Y", strtotime('-5 Months',$enddate));
        
        $report_month1 = date('Ym',strtotime('-5 Months',$enddate));
        $report_month2 = date('Ym',strtotime('-4 Months',$enddate));
        $report_month3 = date('Ym',strtotime('-3 Months',$enddate));
        $report_month4 = date('Ym',strtotime('-2 Months',$enddate));
        $report_month5 = date('Ym',strtotime('-1 Months',$enddate));
        $report_month6 = date('Ym', $enddate);
        
        // Count number of customers
        $numberCustomersRegistered = invoices_api::getNumberOfCustomerBy($report_month, $location_id);
        $numberCustomersRegistered01 = invoices_api::getNumberOfCustomerBy($report_month1, $location_id);
        $numberCustomersRegistered02 = invoices_api::getNumberOfCustomerBy($report_month2, $location_id);
        $numberCustomersRegistered03 = invoices_api::getNumberOfCustomerBy($report_month3, $location_id);
        $numberCustomersRegistered04 = invoices_api::getNumberOfCustomerBy($report_month4, $location_id);
        $numberCustomersRegistered05 = invoices_api::getNumberOfCustomerBy($report_month5, $location_id);
        
        // data y
        $data_y = array(
            $numberCustomersRegistered01,
            $numberCustomersRegistered02,
            $numberCustomersRegistered03,
            $numberCustomersRegistered04,
            $numberCustomersRegistered05,
            $numberCustomersRegistered
        );
        
        // data x
        $report_month1 = date('M y',strtotime('-5 Months',$enddate));
        $report_month2 = date('M y',strtotime('-4 Months',$enddate));
        $report_month3 = date('M y',strtotime('-3 Months',$enddate));
        $report_month4 = date('M y',strtotime('-2 Months',$enddate));
        $report_month5 = date('M y',strtotime('-1 Months',$enddate));
        $report_month6 = date('M y', $enddate);
        $data_x = array($report_month1, $report_month2, $report_month3, $report_month4,$report_month5,$report_month6);
        
        $arrImg= array(800,230);
        $arrText = array(380,25,'Location Customer Growth',18);
        $arrGrapArea = array(60,40,670,190);
        $arrName = array('Months','Customer');
        $arrSeries = array("Time Range (months)","Customer");
        $arrFillRectangle = array(60,40,660,190,-150,5);
        
        /* Create your dataset object */ 
        $chart = new pChartBasic();
    
        // Render the picture of bar chart's location report
        $chart->renderImageBarChartLocationReport($tmp_image_chart_file_name, $arrImg, $arrText, $arrGrapArea, $data_y, $data_x, $arrName, $arrSeries, $arrFillRectangle,2);
    
        // create new PDF document
        $pdf = ci()->pdf01->createObject();
        $pdf->setFontSubsetting(true);
        $pdf->SetFont('arial', '', 9, '', 'false');
    		
        // set document information
        // Set common information
        $pdf->SetTitle(Settings::get(APConstants::INSTANCE_OWNER_COMPANY_CODE));
        $pdf->SetAuthor(Settings::get(APConstants::INSTANCE_OWNER_COMPANY_CODE));

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

        // get pricing
        $pricing = price_api::getPricingMapByLocationId($location_id);
        
        // prepare data.
        $data = array(
            'location_name' => $location_name,
            'location_id'=> $location_id,
            'start' =>$start,
            'period' =>  $period,
            'select_year' => $year,
            'select_month'=> $month,
            'advertising_cost' =>$cost_of_location_advertising,
            'hardware_cost' => $hardware_amortization,
            'location_external_cost' => $location_external_receipts,
            'current_open_balance' => $current_open_balance,
            'total_invoiceable_so_far' => $total_invoiceable_so_far,
            'total_invoiced_so_far' => $total_invoiced_so_far,
            'invoices_written_this_month' => $invoices_written_this_month,
            'total_payments_made_till_end_of_this_month' => $total_payments_made_till_end_of_this_month,
            'number_customers' => $numberCustomersRegistered,
            "invoice_by_total" => $invoice_by_total,
            "invoice_by_location" => $invoice_by_location,
            "invoice" => $invoice,
            'total_invoice' => (isset($total_invoice)) ? $total_invoice : 0,
            'total_manual_invoice' =>$total_manual_invoice,
            'currency_short' => $currency_short,
            'currency_rate' => $currency_rate,
            'decimal_separator' => $decimal_separator,
            'last_update_date' => $last_update_date,
            'check_detail_template' => true,
            "pricing" => $pricing
        );
 
        // Load for template overview
         $html01 = ci()->load->view("admin/location_report_template", $data, TRUE);
         
        if(empty($location_id)){
           // Load for template location  report total detail
            $html02 = ci()->load->view("admin/partial/location_report_total_detail", $data, TRUE);
        }else{
            // Load for template location report  detail
            $html02 = ci()->load->view("admin/partial/location_report_detail", $data, TRUE);
        }

        $pdf->AddPage('P', 'A4');
        $pdf->writeHTMLCell(0, 0, '', '', $html01, 0, 0, 0, true, 'L', true);
        $pdf->AddPage('P', 'A4');
        $pdf->writeHTMLCell(0, 0, '', '', $html02, 0, 0, 0, true, 'L', true);

        //Use I for "inline" to send the PDF to the browser, opposed to F to save it as a file
        $pdf->Output($admin_location_report_file_path, 'F');
        
        // Upload to S3
        ci()->load->library('S3');

        $default_bucket_name = ci()->config->item('default_bucket');
        
        if(empty($location_id)){
            // Location report total
            $amazon_relate_path = 'admin_report/location/TotalReport_' . $year .$month . '.pdf';
        }else{
            // Location report 
            $amazon_relate_path = 'admin_report/location/Report_' . $location_name . '_' . $year .$month . '.pdf';
        }
        
        log_message(APConstants::LOG_DEBUG, "Default bucket name: " . $default_bucket_name . '. Upload to folder:' . $amazon_relate_path);
        try {
            $result = S3::putObjectFile($admin_location_report_file_path, $default_bucket_name, $amazon_relate_path, S3::ACL_PRIVATE);
            //log_audit_message(APConstants::LOG_DEBUG, json_encode($result));
            
            // update data $amazon_relate_path
            if(!empty($location_reports)){
                ci()->location_report_m->update_by_many(array(
                        'id' => $location_reports->id,
                        'location_id' => $location_reports->location_id,
                        'year' => $location_reports->year,
                        'month' => $location_reports->month,
                    ), array(
                        'amazone_filepath' => $amazon_relate_path
                ));
            } 
        } catch (Exception $e) {
            $message = 'Have error when upload file to S3';
            if ($e) {
                $message = $message . $e->getMessage();
            }
            log_message(APConstants::LOG_ERROR, $message);
        }
      
        return $admin_location_report_file_path;
    }
    
    /**
     * Gets invoice data for export.
     * @param type $rows
     * @return array
     */
    private function get_invoice_datas($customer, $invoice_code, $invoice_file_path, $rows, $phone_invoices= null, $location_id=''){        
        $customer_id = $customer->customer_id;
        $invoices = array();
        $invoices_transaction = array();
        $target_date = "";
        $card_number = "";
        $period_of_service = "";
        foreach ($rows as $row) {
            // check manual invoice => get target date.
            if($row->invoice_type == '2'){
                $target_date = $row->invoice_month;
            }else{
                // target date is last day of month.
                $target_date = APUtils::getLastDayOfMonth($row->invoice_month);
            }
            
            // get period month year.
            $period_of_service = date("F, Y", strtotime($target_date));

            // get VAT of user enteprise
            if(!empty($customer->parent_customer_id) && $customer->account_type == APConstants::ENTERPRISE_CUSTOMER){
                $enteprise_customer = APContext::getCustomerByID($customer->parent_customer_id);
                $vat = !empty($enteprise_customer->vat_rate) ? $enteprise_customer->vat_rate : 0;
                $row->vat = $vat;
            }else{
                $vat = $row->vat;
            }

            // gross total.
            $net_price = $row->total_invoice;
            $row->net_price = $net_price;
            $row->gross_price = $net_price * (1 + $vat);
            
            // Gets list postbox fee by location.
            $row->postbox_fee = ci()->invoice_summary_by_location_m->get_list_postbox_fee_by($row->customer_id, $row->invoice_month, $location_id);

            // Auto payment and invoices
            if ($row->invoice_type != '2') {
                // Khong hien thi don gia khi thuc hien shipping
                $row->direct_shipping_free_netprice = 0;
                $row->direct_shipping_private_netprice = 0;
                $row->direct_shipping_business_netprice = 0;
                $row->collect_shipping_free_netprice = 0;
                $row->collect_shipping_private_netprice = 0;
                $row->collect_shipping_business_netprice = 0;

                if(empty($location_id)){
                    ci()->invoice_summary_m->update_by_many(array(
                        'id' => $row->id,
                        'customer_id' => $customer_id
                    ), array(
                        "invoice_file_path" => $invoice_file_path,
                        "update_flag" => 0
                    ));
                }
            } // Manual payment and invoices
            else {
                ci()->load->model('invoices/invoice_detail_manual_m');
                $invoices_transaction = ci()->invoice_detail_manual_m->get_manual_credit_note_by($row->id , $location_id);
            }

            $customer_payments = ci()->payment_m->get_many_by('customer_id', $customer_id);
            if (empty($customer_payments) || count($customer_payments) == 0) {
                log_message(APConstants::LOG_DEBUG, 'Customer payment information of customer id: ' . $customer_id . ' does not exist');

                // Change card number by invoice_code
                $card_number = $customer->invoice_code;
            } else {
                $customer_payment = $customer_payments [0];
                $card_number = $customer_payment->card_number;
            }

            // Gets address of customer
            $address = ci()->customers_address_m->get_by('customer_id', $customer_id);
            if ($address && is_numeric($address->invoicing_country)) {
                $country = ci()->countries_m->get($address->invoicing_country);
                if (!empty($country)) {
                    $address->invoicing_country = $country->country_name;
                }
            }

            $row->target_year = substr($target_date, 0, 4);
            $row->target_month = substr($target_date, 4);

            array_push($invoices, $row);
        }
        
        // Gets invoice by location by target date
        $invoices_by_location = array();
        if($rows){
            // Gets invoice by location by target date
            $array_condition = array(
                "invoice_month" => $target_date,
                "customer_id" => $customer_id
            );
            if(!empty($location_id)){
                $array_condition['location_id'] = $location_id;
            }
            $invoices_by_location = ci()->invoice_summary_by_location_m->get_many_by_many($array_condition);
        }

		if(empty($target_date) &&!empty($phone_invoices)){
            $target_date = $phone_invoices[0]->invoice_month . '01';
            $period_of_service = date("F, Y", strtotime($target_date));
            $card_number = $invoice_code;
        }
        
        // calculate target date
        if(!empty($target_date)){
            if($customer->status == APConstants::ON_FLAG && substr($target_date, 0, 6) == date("Ym", $customer->deleted_date)){
                $target_date = APUtils::convert_timestamp_to_date($customer->deleted_date);
            }else{
                $target_date = APUtils::convert_timestamp_to_date(strtotime($target_date));
            }
        }

       $result_data = array(
            'invoices' => $invoices,
			'phone_invoices' => $phone_invoices,
            'invoices_transaction' => $invoices_transaction,
            "invoices_by_location" => $invoices_by_location,
            'target_date' => $target_date,
            'customer' => $customer,
            'address' => $address,
            'vat' => 0,
            'total' => 0,
            'net_total' => 0,
            'card_number' => $card_number,
            'invoice_code' => $invoice_code,
            "location_id" => $location_id,
            'period_of_service' => $period_of_service
        );
        
        return $result_data;
    }
    
    /**
     * output pdf file.
     */
    private function output_pdf_file($invoice_file_path, $html, $type='F'){
        // Load pdf library
        ci()->load->library('pdf');

        // create new PDF document
        // $pdf = new Tocpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf = ci()->pdf->createObject();
        
        $pdf->setFontSubsetting(true);
        $pdf->SetFont('arial', '', 9, '', 'false');

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


        $pdf->AddPage();
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, 'J', true);

        $pdf->Output($invoice_file_path, $type);
    }
}