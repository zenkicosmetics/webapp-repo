<?php defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . 'libraries/nusoap/class.wsdlcache' . EXT);

/**
 * Roles controller for the groups module
 */
class Todo extends Admin_Controller
{
    private $validation_rules = array(
        array(
            'field' => 'shipment_address_name',
            'label' => 'lang:shipment_address_name',
            'rules' => 'validname'
        ),
        array(
            'field' => 'shipment_city',
            'label' => 'lang:shipment_city',
            'rules' => 'required|validname'
        ),
        array(
            'field' => 'shipment_company',
            'label' => 'lang:shipment_company',
            'rules' => 'valid_companyname|callback__check_shipment_company'
        ),
        array(
            'field' => 'shipment_country',
            'label' => 'lang:shipment_country',
            'rules' => 'required'
        ),
        array(
            'field' => 'shipment_postcode',
            'label' => 'lang:shipment_postcode',
            'rules' => 'required|postcode'
        ),
        array(
            'field' => 'shipment_region',
            'label' => 'lang:shipment_region',
            'rules' => 'validname'
        ),
        array(
            'field' => 'shipment_street',
            'label' => 'lang:shipment_street',
            'rules' => 'validname'
        )
    );
    private $comment_validation_rules = array(
        array(
            'field' => 'text',
            'label' => 'Comment',
            'rules' => 'required'
        )
    );

    /**
     * Constructor method
     */
    public function __construct()
    {
        parent::__construct();

        // Load the required classes
        $this->load->library('form_validation');
        $this->load->library('S3');
        $this->load->library('Nusoap_lib');
        $this->load->library('invoices/invoices');
        $this->load->library('invoices/invoices_api');
        $this->load->library('scans/scans_api');

        // Load model
        $this->load->model('envelope_m');
        $this->load->model('scans/envelope_shipping_tracking_m');
        $this->load->model('envelope_file_m');
        $this->load->model('envelope_pdf_content_m');
        $this->load->model('envelope_completed_m');
        $this->load->model('customers/customer_m');
        $this->load->model('email/email_m');
        $this->load->model('scans/package_price_m');
        $this->load->model('addresses/customers_address_m');
        $this->load->model('settings/countries_m');
        $this->load->model('cloud/customer_cloud_m');

        $this->load->model('mailbox/envelope_customs_m');
        $this->load->model('mailbox/envelope_customs_detail_m');
        $this->load->model('addresses/location_m');

        ci()->load->library(array(
            'settings/settings_api',
            'addresses/addresses_api',
            'shipping/shipping_api',
            "mailbox/mailbox_api",
            "scans/todo_api"
        ));
        
        $this->load->model('addresses/customers_forward_address_m');

        // Load language
        $this->lang->load('scans');
        $this->lang->load(array(
            'mailbox/delete_permission',
            'mailbox/activity_permission'
        ));
        $this->lang->load('addresses/address');
    }

    /**
     * Display all incomming envelope.
     */
    public function index()
    {
        // Get data from incomming screen
        $envelope = new stdClass();
        $envelope->from = $this->input->post('from');
        $envelope->to_customer_id = $this->input->post('to_customer_id');
        $envelope->to_customer_name = $this->input->post('to_customer_name');
        $envelope->type_id = $this->input->post('type_id');
        $envelope->type = $this->input->post('type');
        $envelope->weight = $this->input->post('weight');

        // Gets location
        $location = $this->input->get_post("location_id", "");
        // #481: location selection.
        if ($location || $_POST) {
            APContext::updateLocationUserSetting($location);
        } else {
            $location = APContext::getLocationUserSetting();
        }

        $list_access_location = APUtils::loadListAccessLocation();
        //echo "<pre>";print_r($list_access_location);exit;
        $this->template->set('list_access_location', $list_access_location);
        $this->template->set("location_id", $location);

        // Display the current page
        $this->template->set('envelope', $envelope);
        $this->template->set('header_title', lang('header:incomming_title'))->build('todo/index');
    }

    /**
     * Search item for to do list
     */
    public function search()
    {
        $this->load->library('scans/todo_api');

        // If current request is ajax
        if ($this->is_ajax_request()) {
            $input_location_id = $this->input->get_post('location_available_id');

            // #481: location selection.
            APContext::updateLocationUserSetting($input_location_id);
            $list_access_location = APUtils::loadListAccessLocation();
            $list_access_location_id = array();
            if ($list_access_location && count($list_access_location) > 0) {
                foreach ($list_access_location as $location) {
                    $list_access_location_id [] = $location->id;
                }
            }

            $list_filter_location_id = array(0);
            if (empty($input_location_id)) {
                $list_filter_location_id = $list_access_location_id;
            } else {
                if (in_array($input_location_id, $list_access_location_id)) {
                    $list_filter_location_id [] = $input_location_id;
                }
            }

            // Gets input paging
            $input_paging = $this->get_paging_input();
            
            $limit = isset($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);
            $input_paging ['limit'] = $limit;

            // get result list.
            $result = todo_api::get_todo_list($list_filter_location_id, $input_paging);
            echo json_encode($result['web_result']);
        } else {
            // Display the current page
            $this->template->set('header_title', lang('header:list_group_title'))->build('admin/index');
        }
    }

    /**
     * Search item for shipping
     */
    public function search_shipping()
    {
        $envelope_id = $this->input->get_post('envelope_id');
        $customer_id = $this->input->get_post('customer_id');
        $postbox_id = $this->input->get_post('postbox_id');
        $package_id = $this->input->get_post('package_id');
        
        #1058 add multi dimension capability for admin
        $date_format = APUtils::get_date_format_in_user_profiles();
        $decimal_separator = APUtils::get_decimal_separator_in_user_profiles();
        $weight_unit = APUtils::get_weight_unit_in_user_profiles();

        // If current request is ajax
        if ($this->is_ajax_request()) {
            $selected_envelope = $this->envelope_m->get_by_many(array('id' => $envelope_id));
            $array_condition = array(
                "to_customer_id" => $customer_id,
                // "postbox_id" => $postbox_id,
                'trash_flag IS NULL' => null
            );

            if (!empty($package_id) && $selected_envelope->item_scan_flag != '0'
                && $selected_envelope->envelope_scan_flag != '0'
            ) {
                $array_condition ['envelopes.package_id'] = $package_id;
            } else {
                $array_condition ['envelopes.id'] = $envelope_id;
            }

            // update limit into user_paging.
            $limit = isset($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);

            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging ['limit'] = $limit;

            // Call search method
            $query_result = $this->envelope_m->get_envelope_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);

            // Process output data
            $total = $query_result ['total'];
            $rows = $query_result ['data'];

            // Get output response
            $response = $this->get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);
            
            $i = 0;
            foreach ($rows as $row) {
                $scan_flag = 'No Scan';
                if ($row->item_scan_flag === '1') {
                    $scan_flag = 'PDF Scan';
                }
                $customs_flag = '';
                $customs = $this->envelope_customs_m->get_by_many(array(
                    "envelope_id" => $envelope_id,
                    "customer_id" => $customer_id
                ));
                if ($customs) {
                    $customs_flag = '1';
                }
                
                $response->rows [$i] ['id'] = $row->id;
                $response->rows [$i] ['cell'] = array(
                    $row->id,
                    $row->envelope_code,
                    APUtils::viewDateFormat($row->incomming_date, $date_format),
                    Settings::get_label(APConstants::ENVELOPE_TYPE_CODE, $row->envelope_type_id),
                    APUtils::view_convert_number_in_weight( $row->weight, $weight_unit, $decimal_separator),  #1058 add multi dimension capability for admin
                    APUtils::view_convert_number_in_weight( $row->weight, $weight_unit, $decimal_separator, FALSE),  #1058 add multi dimension capability for admin
                    $scan_flag,
                    $customs_flag,
                    $row->item_scan_flag,
                    $row->envelope_scan_flag
                );
                $i++;
            }

            echo json_encode($response);
        } else {
            // Display the current page
            $this->template->set('header_title', lang('header:list_group_title'))->build('admin/index');
        }
    }

    /**
     * Search item for shipping. This method using for popup interface
     */
    public function search_shipping_popup()
    {
        $this->load->library('scans/scans_api');
        $this->load->model('scans/envelope_properties_m');

        $envelope_id = $this->input->get_post('envelope_id');
        $customer_id = $this->input->get_post('customer_id');
        $package_id = $this->input->get_post('package_id');
        
        #1058 add multi dimension capability for admin
		$date_format = APUtils::get_date_format_in_user_profiles();
        $decimal_separator = APUtils::get_decimal_separator_in_user_profiles();
        $length_unit = APUtils::get_length_unit_in_user_profiles();
        $weight_unit = APUtils::get_weight_unit_in_user_profiles();
        
        // If current request is ajax
        if ($this->is_ajax_request()) {
            $array_condition = array(
                "to_customer_id" => $customer_id,
                'trash_flag IS NULL' => null
            );

            if (!empty($package_id)) {
                $array_condition ['envelopes.package_id'] = $package_id;
            } else {
                $array_condition ['envelopes.id'] = $envelope_id;
            }

            // update limit into user_paging.
            $limit = isset($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);

            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging ['limit'] = $limit;

            // Call search method
            $query_result = scans_api::getEnvelopePagingInPrepareShippingPopup($array_condition, $input_paging['start'], $input_paging['limit'], $input_paging['sort_column'], $input_paging['sort_type']);

            // Process output data
            $total = $query_result['total'];
            $rows = $query_result['data'];

            // Get output response
            $response = $this->get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);

            $i = 0;
            foreach ($rows as $row) {
                $envelope_row_id = $row->id;
                $scan_flag = 'No Scan';
                if ($row->item_scan_flag === '1') {
                    $scan_flag = 'PDF Scan';
                }
                $customs_flag = '';
                $customs = $this->envelope_customs_m->get_by_many(array(
                    "envelope_id" => $envelope_row_id,
                    "customer_id" => $customer_id
                ));
                if ($customs) {
                    $customs_flag = '1';
                }

                // Get dimension
                $dimension_l = '';
                $dimension_w = '';
                $dimension_h = '';
                $dimension = $this->envelope_properties_m->get_by_many(array(
                    "envelope_id" => $envelope_row_id
                ));
                #1058 add multi dimension capability for admin
                if (!empty($dimension)) {
                	$dimension_l = APUtils::view_convert_number_in_length( $dimension->length, $length_unit, $decimal_separator);
                	
                	$dimension_w = APUtils::view_convert_number_in_length( $dimension->width,  $length_unit, $decimal_separator);
                	
                	$dimension_h = APUtils::view_convert_number_in_length( $dimension->height, $length_unit, $decimal_separator);
                }
                
                $response->rows[$i]['id'] = $row->id;
                $response->rows[$i]['cell'] = array(
                    $row->id,
                    $row->prepare_shipping_flag,
                    $row->envelope_code,
                    APUtils::viewDateFormat($row->incomming_date, $date_format),
                    Settings::get_label(APConstants::ENVELOPE_TYPE_CODE, $row->envelope_type_id),
                    APUtils::view_convert_number_in_weight( $row->weight, $weight_unit, $decimal_separator),    #1058 add multi dimension capability for admin
                    APUtils::view_convert_number_in_weight( $row->weight, $weight_unit, $decimal_separator, FALSE),  #1058 add multi dimension capability for admin
                    $dimension_l,
                    $dimension_w,
                    $dimension_h,
                    $row->item_scan_flag,
                    $scan_flag,
                    $customs_flag
                );
                $i++;
            }

            echo json_encode($response);
        } else {
            // Display the current page
            $this->template->set('header_title', lang('header:list_group_title'));
            $this->template->build('admin/index');
        }
    }

    /**
     * Save marked items for shipping (in the Prepare Shipping popup)
     */
    public function save_marked_items_for_shipping()
    {
        if ($this->is_ajax_request()) {
            $this->load->library('scans/scans_api');

            $markedEnvelopeIDs = $this->input->get_post('marked_envelope_ids');
            $unmarkedEnvelopeIDs = $this->input->get_post('unmarked_envelope_ids');
            if ($markedEnvelopeIDs || $unmarkedEnvelopeIDs) {
                $markedEnvelopeIDs = explode(',', $markedEnvelopeIDs);
                $unmarkedEnvelopeIDs = explode(',', $unmarkedEnvelopeIDs);
                scans_api::markEnvelopesForPrepareShipping($markedEnvelopeIDs, $unmarkedEnvelopeIDs);
            }
            $this->success_output('');
            return true;
        } else {
            $this->error_output('Invalid request');
            return false;
        }
    }

    /**
     * Search item for shipping
     */
    public function package_letter_size()
    {
        // Does not use layout
        $this->template->set_layout(FALSE);
        // Display the current page
        $this->template->build('todo/package_letter_size');
    }

    /**
     * Search ppl
     */
    public function get_package_ppl()
    {
        $file_name = APPPATH . 'config/ppl_v300.csv';
        $this->load->library('CSVReader');
        $rows = CSVReader::parse_file($file_name, false);
        $response = array();
        #1058 add multi dimension capability for admin
        $decimal_separator = APUtils::get_decimal_separator_in_user_profiles();
        $length_unit = APUtils::get_length_unit_in_user_profiles();
        $weight_unit = APUtils::get_weight_unit_in_user_profiles();
        $currency_short = APUtils::get_currency_short_in_user_profiles();
        $currency_rate = APUtils::get_currency_rate_in_user_profiles();
        
        foreach ($rows as $row) {
            $length   = $row [17];
            $heigh    = $row [18]; 
            $width    = $row [19]; 
            #1058 add multi dimension capability for admin
            $currency = APUtils::view_currency_with_currency_short_unit($row[5], $currency_short, $currency_rate, $decimal_separator);
            $weight   = APUtils::view_convert_number_in_weight($row [21], $weight_unit, $decimal_separator);
            $x =  APUtils::view_convert_number_in_weight( ($length / 100), $weight_unit, $decimal_separator);
            $y =  APUtils::view_convert_number_in_weight( ($heigh / 100),  $weight_unit, $decimal_separator);
            $z =  APUtils::view_convert_number_in_weight( ($width / 100),  $weight_unit, $decimal_separator);

            $text = $currency . '; ' . $weight  . '; ' . $x . 'x' . $y . 'x' . $z ;

            $response [] = array(
                $row [2],
                $text,
                $row [21],
                $row [5]
            );
        }
        return $response;
    }

    /**
     * Search item for shipping
     */
    public function search_package_letter_size()
    {
    	#1058 add multi dimension capability for admin
    	$decimal_separator = APUtils::get_decimal_separator_in_user_profiles();
    	$weight_unit = APUtils::get_weight_unit_in_user_profiles();
    	$currency_sign = APUtils::get_currency_sign_in_user_profiles();
    	$currency_short = APUtils::get_currency_short_in_user_profiles();
    	$currency_rate = APUtils::get_currency_rate_in_user_profiles();
    	
        // If current request is ajax
        if ($this->is_ajax_request()) {
            //$array_condition = array();

            // update limit into user_paging.
            $limit = isset($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);

            $file_name = APPPATH . 'config/ppl_v300.csv';
            $this->load->library('CSVReader');
            $data = CSVReader::parse_file($file_name, false);

            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging ['limit'] = $limit;

            // Call search method
            // $query_result = $this->package_price_m->get_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);


            // Process output data
            $total = count($data);
            $rows = $data;

            // Get output response
            $response = $this->get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);

            $i = 0;
            foreach ($rows as $row) {
                $response->rows [$i] ['id'] = $row [2];
                $response->rows [$i] ['cell'] = array(
                    // PPL
                    $row [2],
                    $row [4],
                    APUtils::view_convert_number_in_weight($row [21], $weight_unit, $decimal_separator), #1058 add multi dimension capability for admin
                    $row [21],
                    APUtils::view_currency_with_currency_sign_unit($row [5], $currency_short,$currency_rate, $decimal_separator)  #1058 add multi dimension capability for admin
                );
                $i++;
            }

            echo json_encode($response);
        } else {
            // Display the current page
            $this->template->build('todo/package_letter_size');
        }
    }

    /**
     * Get default package size
     */
    function get_package_letter_size()
    {
        $this->template->set_layout(FALSE);
        $weight = $this->input->get_post('weight');
        $file_name = APPPATH . 'config/ppl_v300.csv';
        $this->load->library('CSVReader');
        $data = CSVReader::parse_file($file_name, false);
        
        #1058 add multi dimension capability for admin
        $decimal_separator = APUtils::get_decimal_separator_in_user_profiles();
        $weight_unit = APUtils::get_length_unit_in_user_profiles();
        $currency_sign = APUtils::get_currency_sign_in_user_profiles();
    	$currency_short = APUtils::get_currency_short_in_user_profiles();
    	$currency_rate = APUtils::get_currency_rate_in_user_profiles();

        $package_prices = $this->package_price_m->get_many_by_many(array(
            "weight >= " => $weight
        ), null, false, array(
            "weight" => 'asc'
        ));
        if (empty($package_prices)) {
            echo json_encode(array(
                "package_id" => "",
                "package_text" => ""
            ));
            return;
        }
        $package_price = $package_prices [0];
        
        #1058 add multi dimension capability for admin
        $package_text = $package_price->name . '; ' . APUtils::view_convert_number_in_weight($package_price->weight, $weight_unit, $decimal_separator) .  '; ' . $package_price->size . '; ' . APUtils::view_currency_with_currency_short_unit($package_price->price, $currency_short, $currency_rate, $decimal_separator);
        echo json_encode(array(
            "package_id" => $package_price->id,
            "package_text" => $package_text
        ));
        return;
    }

    /**
     * Display all incomming envelope.
     */
    public function execute_scan()
    {
        $this->load->library('scans/todo_api');
        $envelope_id = $this->input->post('envelope_id');
        $action_type = $this->input->post('action_type');

        // Su dung de phan biet la envelope scan hay item scan
        $scan_type = $this->input->post('scan_type');
        $customer_token_key = $this->input->post('customer_token_key');
        $number_page = $this->input->post('number_page', 0);

        // Check if empty token 
        if (empty($customer_token_key)) {
            log_message(APConstants::LOG_ERROR, 'Customer token key is invalid.');
            return;
        }
        // Get customer information
        $customer = $this->customer_m->get_by_many(array(
            "token_key" => $customer_token_key
        ));

        // Get envelope information
        $envelope = $this->envelope_m->get_by("id", $envelope_id);

        // Check valid customer
        if (empty($customer)) {
            log_message(APConstants::LOG_ERROR, 'Can not find customer with key: ' + $customer_token_key);
            return;
        }

        $result = todo_api::execute_scan($customer, $envelope, $action_type, $scan_type, $number_page);
        
        if($result['status']){
            $this->success_output('', array('private_path' => $result['private_path']));
        }else{
            $this->error_output($result['message']);
        }
        return;
    }

    /**
     * Display file scan with envelope id and file type (ENVELOPE or ITEM scan)
     */
    public function get_file_scan()
    {
        
        // Does not use layout
        $this->template->set_layout(FALSE);
        $envelope_id = $this->input->get_post('envelope_id');
        $item_type = $this->input->get_post('type');
        // Get envelope information
        $envelope = $this->envelope_m->get_by("id", $envelope_id);
        if (empty($envelope)) {
            log_message('debug', 'todo: envelope: empty`');
            return;
        }

        // Get file information
        $preview_file = $this->envelope_file_m->get_by_many_order(array(
            "envelope_id" => $envelope_id,
            "customer_id" => $envelope->to_customer_id,
            "type" => $item_type
        ), array(
            "updated_date" => "ASC",
            "created_date" => "ASC"
        ));
        if (empty($preview_file)) {
            log_message('debug', 'todo: preview_file: empty');
            echo "not file";
            return;
        }
        // Get local file name
        $local_file_name = $preview_file->local_file_name;
        $amazon_path = $preview_file->amazon_path;

        // Get extends file
        header('Content-Disposition: inline');
        $ext = substr($local_file_name, strrpos($local_file_name, '.') + 1);
        $ext = strtolower($ext);
        switch ($ext) {
            case 'jpg' :
                header('Content-type: image/jpeg');
                break;
            case 'jpge' :
                header('Content-type: image/jpeg');
                break;
            case 'png' :
                header('Content-type: image/png');
                break;
            case 'tiff' :
                header('Content-type: image/tiff');
                break;
            case 'pdf' :
                header('Content-type: application/pdf');
                break;
        }
        if (!file_exists($local_file_name)) {
            APUtils::download_amazon_file($preview_file);
        }
        readfile($local_file_name);
    }

    /**
     * Display estamp image
     */
    public function preview_estamp_image()
    {
        $envelope_code = $this->input->get('code');
        // Get extends file
        header('Content-Disposition: inline');
        $local_file_name = 'downloads/internetmarken/' . $envelope_code . '/0.png';
        $ext = substr($local_file_name, strrpos($local_file_name, '.') + 1);
        $ext = strtolower($ext);
        switch ($ext) {
            case 'jpg' :
                header('Content-type: image/jpeg');
                break;
            case 'jpge' :
                header('Content-type: image/jpeg');
                break;
            case 'png' :
                header('Content-type: image/png');
                break;
            case 'tiff' :
                header('Content-type: image/tiff');
                break;
            case 'pdf' :
                header('Content-type: application/pdf');
                break;
        }
        readfile($local_file_name);
    }

    /**
     * Load image preview
     */
    public function preview_image()
    {
        $this->load->library('common/common_api');

        // Does not use layout
        $this->template->set_layout(FALSE);
        $customer_id = $this->input->get_post('customer_id');
        $envelope_id = $this->input->get_post('envelope_id');
        $has_scan_item_type = $this->input->get_post('has_scan_item_type');

        $envelope = $this->envelope_m->get_by_many(array(
            "id" => $envelope_id,
            "to_customer_id" => $customer_id
        ));

        // Check item scan        
        $preview_file = $this->envelope_file_m->get_by_many_order(array(
            "envelope_id" => $envelope_id,
            "customer_id" => $customer_id,
            "type" => $has_scan_item_type
        ), array(
            "updated_date" => "ASC",
            "created_date" => "ASC"
        ));

        $preview_file = common_api::setDynamicPathEnvelopeFile($preview_file);

        $this->template->set('envelope', $envelope);
        $this->template->set('has_scan_item_type', $has_scan_item_type);
        $this->template->set('preview_file', $preview_file)->build('todo/preview');
    }

    /**
     * Load image preview
     */
    public function preview_label_file()
    {
        // Does not use layout
        $this->template->set_layout(FALSE);
        $filePath = $this->input->get_post('filePath');
        $this->template->set('filePath', $filePath);
        $this->template->set('filePath', $filePath)->build('todo/preview_estamp');
    }

    /**
     * Display preview estamp with local file name
     */
    public function get_preview_estamp()
    {
        // Does not use layout
        $this->template->set_layout(FALSE);
        $local_file_name = $this->input->get_post('filePath');

        // Get extends file
        header('Content-Disposition: inline');
        $ext = substr($local_file_name, strrpos($local_file_name, '.') + 1);
        $ext = strtolower($ext);
        switch ($ext) {
            case 'jpg' :
                header('Content-type: image/jpeg');
                break;
            case 'jpge' :
                header('Content-type: image/jpeg');
                break;
            case 'png' :
                header('Content-type: image/png');
                break;
            case 'tiff' :
                header('Content-type: image/tiff');
                break;
            case 'pdf' :
                header('Content-type: application/pdf');
                break;
        }
        readfile($local_file_name);
    }

    /**
     * Display all incomming envelope.
     */
    public function scan()
    {
        // Does not use layout
        $this->template->set_layout(FALSE);
        $customer_id = $this->input->get_post('customer_id');
        $envelope_id = $this->input->get_post('envelope_id');
        $scan_type = $this->input->get_post('scan_type');

        // Generate new token key for this request
        $token_key = md5(APContext::getCustomerCodeLoggedIn() . '#' . APUtils::generateRandom(64));

        // Update last access date
        $this->customer_m->update_by_many(array(
            "customer_id" => $customer_id
        ), array(
            "token_key" => $token_key
        ));

        // Delete temporary file 
        $this->load->model('scans/envelope_shipping_m');
        $this->envelope_file_m->delete_by_many(array(
            "envelope_id" => $envelope_id,
            "customer_id" => $customer_id,
            "type" => $scan_type
        ));

        // #577 add check page item.
        $check_page_flag = $this->input->get_post('check_page', '');
        $this->template->set("check_page_flag", $check_page_flag);

        // Display the current page
        $this->template->append_js('jquery.upload-1.0.0.min.js');
        $this->template->set('scan_type', $scan_type);
        $this->template->set('token_key', $token_key)->set('envelope_id', $envelope_id)->build('todo/form');
    }

    /**
     * Shipping function
     */
    public function shipping_check()
    {
        $this->load->model('scans/envelope_shipping_m');
        $this->template->set_layout(FALSE);
        $this->form_validation->set_rules($this->validation_rules);
        if (!$this->form_validation->run()) {
            $errors = $this->form_validation->error_json();
            echo json_encode($errors);
            return;
        }
        
        $envelope_id = $this->input->get_post('envelope_id');
        $postbox_id = $this->input->get_post('postbox_id');
        $customer_id = $this->input->get_post('customer_id');
        
        // Save address information
        $array_insert = array();
        $array_insert ['customer_id'] = $this->input->get_post('customer_id');
        $array_insert ['envelope_id'] = $envelope_id;
        $array_insert ['postbox_id'] = $postbox_id;
        $array_insert ['shipping_name'] = $this->input->get_post('shipment_address_name');
        $array_insert ['shipping_city'] = $this->input->get_post('shipment_city');
        $array_insert ['shipping_company'] = $this->input->get_post('shipment_company');
        $array_insert ['shipping_country'] = $this->input->get_post('shipment_country');
        $array_insert ['shipping_postcode'] = $this->input->get_post('shipment_postcode');
        $array_insert ['shipping_region'] = $this->input->get_post('shipment_region');
        $array_insert ['shipping_street'] = $this->input->get_post('shipment_street');
        
        // Get envelope shipping
        $envelope_shipping_check = $this->envelope_shipping_m->get_by_many(array(
            "customer_id" => $customer_id,
            "envelope_id" => $envelope_id,
            "postbox_id" => $postbox_id
        ));
        if (empty($envelope_shipping_check)) {
            $this->envelope_shipping_m->insert($array_insert);
        } else {
            $this->envelope_shipping_m->update_by_many(array(
                "customer_id" => $customer_id,
                "envelope_id" => $envelope_id,
                "postbox_id" => $postbox_id
            ), $array_insert);
        }
        
        $envelope_shipping_tracking = $this->envelope_shipping_tracking_m->get_by("envelope_id",$envelope_id);
        $tracking_number = "";
        if(!empty($envelope_shipping_tracking)){
            $tracking_number = $envelope_shipping_tracking->tracking_number;
        }
        $this->success_output('', array("envelope_id" => $envelope_id, "tracking_number" => $tracking_number));
    }

    /**
     * Shipping function pending scan item/envelope by package id
     */
    public function shipping_check_scan_pending()
    {
        $this->template->set_layout(FALSE);
        $this->form_validation->set_rules($this->validation_rules);
        $package_id = $this->input->get_post('package_id');
        $customer_id = $this->input->get_post('customer_id');
        
        if (empty($package_id)) {
            $this->success_output('');
            return;
        }

        // Get list envelopes from database
        $list_collect_shipping_envelope = $this->envelope_m->get_many_by_many(array(
            "to_customer_id" => $customer_id,
            'trash_flag IS NULL' => null,
            'package_id' => $package_id
        ));

        $allItemCompletedScan = true;
        // For each item
        $list_envelope_code = array();
        foreach ($list_collect_shipping_envelope as $collect_envelope) {
            if ($collect_envelope->envelope_scan_flag == '0' || $collect_envelope->item_scan_flag == '0') {
                //$allItemCompletedScan = false;
                $list_envelope_code[] = $collect_envelope->envelope_code;
            }
        }

        if (empty($list_envelope_code)) {
            $this->success_output('');
        } else {
            $list_error = implode("<br/>", $list_envelope_code);
            $this->error_output('', $list_error);
        }
        return;
    }

    /**
     * Internal check shipping
     */
    private function shipping_check_scan_pending_internal($row)
    {
        $customer_id = $row->to_customer_id;
        $package_id = $row->package_id;
        if (empty($package_id)) {
            return true;
        }
        if ($row->envelope_scan_flag == APConstants::OFF_FLAG
            || $row->item_scan_flag == APConstants::OFF_FLAG
        ) {
            return true;
        }
        // Get list envelopes from database
        $list_collect_shipping_envelope = $this->envelope_m->get_many_by_many(array(
            "to_customer_id" => $customer_id,
            'trash_flag IS NULL' => null,
            'package_id' => $package_id
        ));

        $allItemCompletedScan = true;
        // For each item
        foreach ($list_collect_shipping_envelope as $collect_envelope) {
            if ($collect_envelope->envelope_scan_flag == '0' || $collect_envelope->item_scan_flag == '0') {
                $allItemCompletedScan = false;
            }
        }
        return $allItemCompletedScan;
    }

    /**
     * This function using display shipping screen, call shipping_check and mark completed for envelope. Shipping function
     */
    public function get_shipping_cost()
    {
        $this->template->set_layout(FALSE);
        $this->load->library("mailbox/mailbox_api");
        // Get customer address
        $customer_id = $this->input->get_post('customer_id');
        $envelope_id = $this->input->get_post('envelope_id');
        
        #1058 add multi dimension capability for admin
        $decimal_separator = APUtils::get_decimal_separator_in_user_profiles();

        // Replace "," by "."
        $shipping_fee = $this->convert_decimal_number($this->input->get_post('shipping_fee', 0));
        $insurance_customs_cost = $this->convert_decimal_number($this->input->get_post('insurance_customs_cost'));
        $customs_process_flag = $this->convert_decimal_number($this->input->get_post('customs_process_flag'));
        $special_service_fee = $this->convert_decimal_number($this->input->get_post('special_service_fee', 0));
        $shipping_fee = !empty($shipping_fee) ? $shipping_fee : 0;
        $special_service_fee = !empty($special_service_fee) ? $special_service_fee : 0;
        if (empty($insurance_customs_cost)) {
            $insurance_customs_cost = mailbox_api::get_total_customs_cost($customer_id, $envelope_id);
        }
        if ($customs_process_flag == '0') {
            $insurance_customs_cost = 0;
        }
        // Get envelope
        $envelope = $this->envelope_m->get_by_many(array(
            "id" => $envelope_id,
            "to_customer_id" => $customer_id
        ));

        // Direct shipping
        $shipping_cost_obj = array(
            'total_shipping_fee' => 0,
            'customs_handling' => 0
        );
        
        if ($envelope->direct_shipping_flag == '0') {
            $shipping_cost_obj = $this->invoices->get_shipping_cost($customer_id, $envelope->postbox_id, 
                    $envelope->id, '1', 1, $shipping_fee, $insurance_customs_cost);
        } // Collect shipping
        else if ($envelope->collect_shipping_flag == '0') {
            $shipping_cost_obj = $this->invoices->get_shipping_cost($customer_id, $envelope->postbox_id, 
                    $envelope->id, '2', 1, $shipping_fee, $insurance_customs_cost);
        }

        // Gets vat of customer
        // $customerVat = APUtils::getVatRateOfShippingByCustomer($customer_id, $shipping_country_id);
        $customerVat = APUtils::getVatRateOfCustomer($customer_id);
        $vat = $customerVat->rate;
        // fixbug #498
        $shipping_cost = APUtils::number_format($shipping_cost_obj['total_shipping_fee'] * (1 + $vat), 2, $decimal_separator);
        
        // Customs handling
        $customs_handling_net = APUtils::number_format($shipping_cost_obj['customs_handling'], 2, $decimal_separator);
        $customs_handling_gross = APUtils::number_format($shipping_cost_obj['customs_handling'] * (1 + $vat), 2, $decimal_separator);
        
        // Handling charge
        $handling_charge_net = APUtils::number_format($shipping_cost_obj['handling_charge'], 2, $decimal_separator);
        $handling_charge_gross = APUtils::number_format($shipping_cost_obj['handling_charge'] * (1 + $vat), 2, $decimal_separator);
        
        // Special service cost
        $special_service_fee_net = APUtils::number_format($special_service_fee, 2, $decimal_separator);
        $special_service_fee_gross = APUtils::number_format($special_service_fee * (1 + $vat), 2, $decimal_separator);
        
        // Charge for shipment
        $charge_for_shipment_net = APUtils::number_format($shipping_cost_obj['shipping_fee'], 2, $decimal_separator);
        $charge_for_shipment_gross = APUtils::number_format($shipping_cost_obj['shipping_fee'] * (1 + $vat), 2, $decimal_separator);
        
        // Total
        $total_shipping_cost_net = APUtils::number_format($shipping_cost_obj['total_shipping_fee'] + $special_service_fee, 2, $decimal_separator);
        $total_shipping_cost_gross = APUtils::number_format(($shipping_cost_obj['total_shipping_fee'] + $special_service_fee) * (1 + $vat), 2, $decimal_separator);
        
        
        $returnObj = array(
            "status" => TRUE,
            "message" => '',
            "data" => $shipping_cost,
            "customs_handling_net" => $customs_handling_net,
            "customs_handling_gross" => $customs_handling_gross,
            "special_service_fee_net" => $special_service_fee_net,
            "special_service_fee_gross" => $special_service_fee_gross,
            "charge_for_shipment_net" => $charge_for_shipment_net,
            "charge_for_shipment_gross" => $charge_for_shipment_gross,
            "handling_charge_net" => $handling_charge_net,
            "handling_charge_gross" => $handling_charge_gross,
            "total_shipping_cost_net" => $total_shipping_cost_net,
            "total_shipping_cost_gross" => $total_shipping_cost_gross,
            "vat" => $vat * 100
        );
        echo json_encode($returnObj);
    }

    /**
     * Summary weight envelopes
     */
    public function sumWeightEnvelope()
    {
        $customer_id = $this->input->get_post('customer_id');
        $package_id = $this->input->get_post('package_id');
        $envelope_id = $this->input->get_post('envelope_id');

        // Get list envelopes from database
        if (!empty($package_id)) {
            $list_collect_shipping_envelope = $this->envelope_m->get_many_by_many(array(
                "to_customer_id" => $customer_id,
                'trash_flag IS NULL' => null,
                'package_id' => $package_id
            ));
        } else {
            $list_collect_shipping_envelope = $this->envelope_m->get_many_by_many(array(
                "to_customer_id" => $customer_id,
                'trash_flag IS NULL' => null,
                'id' => $envelope_id
            ));
        }

        $total_weight = 0;
        $decimal_separator = APUtils::get_decimal_separator_in_user_profiles();
        $weight_unit = APUtils::get_weight_unit_in_user_profiles();
        
        if (!empty($list_collect_shipping_envelope)) {
            foreach ($list_collect_shipping_envelope as $item_collect_shipping_envelope) {
                $total_weight += $item_collect_shipping_envelope->weight;
            }
        }
        echo json_encode(array(
             'total_weight' =>  APUtils::view_convert_number_in_weight($total_weight, $weight_unit, $decimal_separator)
        ));
    }

    /**
     * This function using display shipping screen, call shipping_check and mark completed for envelope. Shipping function
     */
    public function shipping()
    {
        ini_set('max_execution_time', 600);
        $this->template->set_layout(FALSE);
        
        $this->load->model('scans/envelope_properties_m');
        $this->load->model('scans/envelope_shipping_request_m');
        $this->load->model('scans/envelope_shipping_m');
        $this->load->library('customers/customers_api');
        
        // Get customer address
        $customer_id = $this->input->get_post('customer_id');
        $envelope_id = $this->input->get_post('envelope_id');
        $postbox_id = $this->input->get_post('postbox_id');
        $package_id = $this->input->get_post('package_id');
        $shipping_type = $this->input->get_post('shipping_type');
        $shipping_service_id = $this->input->get_post('shipping_service_id');
        $current_scan_type = $this->input->get_post('current_scan_type');
       
        // Get select shipping service id
        $selected_shipping_service_obj = shipping_api::getShippingServiceIdByEnvelope($envelope_id);
        
        $message = '{customer_id:'. $customer_id;
        $message = $message. ', postbox_id: '. $postbox_id;
        $message = $message. ', package_id: '. $package_id.'}';
        log_audit_message(APConstants::LOG_INFOR, $message, FALSE, 'todo-shipping');
        
        $envelope = $this->envelope_m->get($envelope_id);
        $estamp_link = '';
        if ($_POST) {
            // #1058 add multi dimension capability for admin
            $currency_short = APUtils::get_currency_short_in_user_profiles();
            $currency_rate = APUtils::get_currency_rate_in_user_profiles();
        
            $tracking_number = $this->input->get_post('tracking_number');
            $shipping_services = $this->input->get_post('shipping_services');
            $no_tracking_number = $this->input->get_post('no_tracking_number');
            $shipping_api_id = $this->input->get_post('shipping_api_id');
            $shipping_credential_id = $this->input->get_post('shipping_credential_id');

            //If no tracking number input, auto check no_tracking_number flag
            if(empty($tracking_number)) {
                $no_tracking_number = 1;
            } 
            // Upload packages in shipping (collect shipping with large volume)
            $limit = 10;
            $start = $this->input->get_post('start', 0);
            if($package_id){
                $array_condition = array(
                    "envelopes.package_id" => $package_id,
                    "envelopes.to_customer_id" => $customer_id,
                    'envelopes.trash_flag IS NULL' => null
                );
            }else{
                $array_condition = array(
                    "envelopes.id" => $envelope_id,
                    "envelopes.to_customer_id" => $customer_id,
                    'envelopes.trash_flag IS NULL' => null
                );
            }
            // Result envelopes 
            $query_result = $this->envelope_m->get_envelope_paging($array_condition, $start, $limit, null, null);
            $total = $query_result ['total'];
            $rows = $query_result ['data'];
            
            //Insert tracking number
            todo_api::save_tracking_number($envelope_id, $tracking_number, $shipping_services, $no_tracking_number, $rows, true);

            // Process output data
            $envelope_ids = array();
            foreach($rows as $row){
                $envelope_ids[] = $row->id;
            }
            
            //Get admin id
            $completed_by = APContext::getAdminIdLoggedIn();
            
            // save customer shipping report
            if($start + $limit >= $total){
                $other_fee1 = APUtils::convert_number_in_currency($this->input->get_post('other_package_price_fee'), $currency_short, $currency_rate);
                $shipping_fee1 = str_replace(',', '.', $other_fee1);
                $postal_charge = $shipping_fee1 + $this->convert_decimal_number($this->input->get_post('special_service_fee'));
                $shipping_service = shipping_api::getShippingServiceInfo($shipping_service_id);
                
                // save shipping report.
                customers_api::save_customer_shipping_report($customer_id, $envelope, $shipping_service, $tracking_number, $postal_charge, $completed_by, $shipping_api_id, $shipping_credential_id);
            }
            
            // Output packages follow start, total, limit, if envelope_id = 0
            if(count($envelope_ids) == 0){
                $this->success_output("", array(
                    "start" => $start + $limit,
                    "total" => $total,
                    "limit" => $limit
                ));
                return;
            }

            $current_date = now();
            $this->form_validation->set_rules($this->validation_rules);
            if (!$this->form_validation->run()) {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
			
            $number_collect_envelopes = $total;
            log_message('debug', 'Number_collect_envelopes: ' . $number_collect_envelopes);
            $cur_process_item = 0;
            foreach ($envelope_ids as $temp_envelope_id) {
                $cur_process_item++;
                $envelope = $this->envelope_m->get_by_many(array(
                    "id" => $temp_envelope_id,
                    "to_customer_id" => $customer_id
                ));
                $postbox_id = $envelope->postbox_id;
                $array_insert = array();
                $array_insert ['customer_id'] = $this->input->get_post('customer_id');
                $array_insert ['envelope_id'] = $temp_envelope_id;
                $array_insert ['postbox_id'] = $postbox_id;
                $array_insert ['shipping_name'] = $this->input->get_post('shipment_address_name');
                $array_insert ['shipping_city'] = $this->input->get_post('shipment_city');
                $array_insert ['shipping_company'] = $this->input->get_post('shipment_company');
                $array_insert ['shipping_country'] = $this->input->get_post('shipment_country');
                $array_insert ['shipping_postcode'] = $this->input->get_post('shipment_postcode');
                $array_insert ['shipping_region'] = $this->input->get_post('shipment_region');
                $array_insert ['shipping_street'] = $this->input->get_post('shipment_street');
                $array_insert ['estamp_url'] = $this->input->get_post('estamp_url');
                $array_insert ['lable_size_id'] = $this->input->get_post('lable_size');
                $array_insert ['package_letter_size'] = $this->input->get_post('package_letter_size');
                $array_insert ['package_letter_size_id'] = $this->input->get_post('package_letter_size');

                $package_price = $this->input->get_post('package_price');
                $other_package_price_flag = $this->input->get_post('other_package_price_flag');
                
                $other_package_price_fee = APUtils::convert_number_in_currency($this->input->get_post('other_package_price_fee'), $currency_short, $currency_rate);
                
                if ($other_package_price_flag == '1') {
                	
                    $array_insert ['shipping_fee'] = str_replace(',', '.', $other_package_price_fee);
                    $array_insert ['package_letter_size'] = '';
                    $array_insert ['package_letter_size_id'] = 0;
                } else {
                    // $array_insert ['shipping_fee'] = str_replace(',', '.', $other_package_price_fee);
                    $array_insert ['shipping_fee'] = str_replace(',', '.', $package_price);
                }

                $array_insert ['shipment_phone_number'] = $this->input->get_post('shipment_phone_number');
                $array_insert ['printer_id'] = $this->input->get_post('select_printer_id');
                $array_insert ['shipping_type_id'] = $this->input->get_post('shipping_type_id');
                $array_insert ['shipping_service_id'] = $shipping_service_id;
                $array_insert ['shipping_date'] = $current_date;
                $array_insert ['insurance_customs_cost'] = $this->convert_decimal_number($this->input->get_post('insurance_customs_cost'));
                
                $array_insert ['customs_handling_fee'] = $this->convert_decimal_number($this->input->get_post('customs_handling'));
                $array_insert ['special_service_fee'] = $this->convert_decimal_number($this->input->get_post('special_service_fee'));
                $array_insert ['forwarding_charges_fee'] = $this->convert_decimal_number($this->input->get_post('handling_charge'));
                $array_insert ['forwarding_charges_postal'] = $array_insert ['special_service_fee'] + $array_insert ['shipping_fee'];
                
                // Write log to tracking
                log_audit_message(APConstants::LOG_INFOR, 'envelope_shipping for all item:'. json_encode($array_insert), 'todo-shipping');
                
                $array_insert ['customs_handling_fee'] = $array_insert ['customs_handling_fee'] / $number_collect_envelopes;
                $array_insert ['special_service_fee'] = $array_insert ['special_service_fee'] / $number_collect_envelopes;
                $array_insert ['forwarding_charges_fee'] = $array_insert ['forwarding_charges_fee'] / $number_collect_envelopes;
                $array_insert ['forwarding_charges_postal'] = $array_insert ['forwarding_charges_postal'] / $number_collect_envelopes;
                
                // Write log tracking
                log_audit_message(APConstants::LOG_INFOR, 'envelope_shipping for each item:'. json_encode($array_insert), 'todo-shipping');
                                
                $shipping_type = $this->input->get_post('shipping_type');
                $include_estamp = $this->input->get_post('include_estamp');
                if (empty($include_estamp)) {
                    $include_estamp = '0';
                }

                // Get envelope shipping
                $envelope_shipping_check = $this->envelope_shipping_m->get_by_many(array(
                    "customer_id" => $customer_id,
                    "envelope_id" => $temp_envelope_id,
                    "postbox_id" => $postbox_id
                ));
                if (empty($envelope_shipping_check)) {
                    $this->envelope_shipping_m->insert($array_insert);
                } else {
                    $this->envelope_shipping_m->update_by_many(array(
                        "customer_id" => $customer_id,
                        "envelope_id" => $temp_envelope_id,
                        "postbox_id" => $postbox_id
                    ), $array_insert);
                }
                $envelope_shipping = $this->envelope_shipping_m->get_by_many(array(
                    "customer_id" => $customer_id,
                    "envelope_id" => $temp_envelope_id,
                    "postbox_id" => $postbox_id
                ));
                $message = '{get envelope_shipping:'. json_encode($envelope_shipping);
                log_audit_message(APConstants::LOG_INFOR, $message, FALSE, 'todo-shipping');
                
                $shipping_fee = 0;
                if ($envelope_shipping) {
                    $shipping_fee = $envelope_shipping->shipping_fee;
                }

                // Direct shipping
                if ($envelope->direct_shipping_flag == '0') {
                    $current_scan_type = '3';
                    $direct_shipping_flag = 1;
                    
                    // Update status of envelope
                    $this->envelope_m->update_by_many(array(
                        "id" => $temp_envelope_id,
                        "to_customer_id" => $customer_id
                    ), array(
                        "direct_shipping_flag" => $direct_shipping_flag,
                        "direct_shipping_date" => now(),
                        "last_updated_date" => $current_date,
                        "collect_shipping_flag" => null,
                        "storage_flag" => APConstants::OFF_FLAG,
                        "storage_date" => NULL,
                        "completed_flag" => APConstants::ON_FLAG,
                        "completed_by" => $completed_by,
                        "completed_date" => $current_date
                    ));

                    // Insert data to completed table
                    $this->invoices->cal_shipping_invoices($customer_id, $postbox_id, $temp_envelope_id, '1', 1, $shipping_fee);
                    scans_api::insertCompleteItem($envelope->id, APConstants::DIRECT_FORWARDING_COMPLETED_ACTIVITY_TYPE, APConstants::TRIGGER_BY_ADMIN, $completed_by);

                } // Collect shipping
                else if ($envelope->collect_shipping_flag == '0') {
                    $current_scan_type = '4';
                    $collect_shipping_flag = 1;
                    //$number_collect_envelopes = count($envelope_ids);
                    log_message('debug', 'Collect shipping fee ');

                    // Update status of envelope
                    $this->envelope_m->update_by_many(array(
                        "id" => $temp_envelope_id,
                        "to_customer_id" => $customer_id
                    ), array(
                        "collect_shipping_flag" => $collect_shipping_flag,
                        "collect_shipping_date" => now(),
                        "last_updated_date" => $current_date,
                        "direct_shipping_flag" => null,
                        "storage_flag" => APConstants::OFF_FLAG,
                        "storage_date" => NULL,
                        "completed_flag" => APConstants::ON_FLAG,
                        "completed_by" => $completed_by,
                        "completed_date" => now()
                    ));
                    $this->invoices->cal_shipping_invoices($customer_id, $envelope->postbox_id, $envelope->id, '2', $number_collect_envelopes, $shipping_fee);
                    
                    // Add customs handing fee for first item
                    if ($start == 0 && $cur_process_item == 1) {
                        $this->invoices->cal_customs_handing_fee($customer_id, $postbox_id, $temp_envelope_id);
                    }
                    
                    // Insert data to completed table
                    scans_api::insertCompleteItem($envelope->id, APConstants::COLLECT_FORWARDING_COMPLETED_ACTIVITY_TYPE, APConstants::TRIGGER_BY_ADMIN, $completed_by);
                }
                // Update status of envelope
                $this->envelope_m->update_by_many(array(
                    "id" => $temp_envelope_id,
                    "to_customer_id" => $customer_id
                ), array(
                    "shipping_type" => $shipping_type,
                    "include_estamp_flag" => $include_estamp
                ));
                
                // trigger storage nubmer report.
                scans_api::updateStorageStatus($envelope->id, $customer_id, $postbox_id, APUtils::getCurrentYear(), APUtils::getCurrentMonth(), $envelope->location_id, APConstants::OFF_FLAG);
            }

            //Output packages follow start, total, limit
            $this->success_output("", array(
                "start" => $start + $limit,
                "total" => $total,
                "limit" => $limit
            ));
            return;
        }

        // Get all countries
        $countries = $this->countries_m->get_many_by_many(array(), '', false, array(
            'country_name' => 'ASC'
        ));
        $ppl = $this->get_package_ppl();
        
        $shipping_services_available = todo_api::get_shipping_services_by_envelope($envelope);
        $customer_address = $shipping_services_available['customer_address'];
        $listShippingServices = $shipping_services_available['listShippingServices'];
        
        #1058 add multi dimension capability for admin
        $currency_short = APUtils::get_currency_short_in_user_profiles();
        $weight_unit = APUtils::get_weight_unit_in_user_profiles();
        
        $this->template->set('selected_shipping_service_id', $selected_shipping_service_obj['shipping_service_id']);
        // 0: Customer selected | 1 : Deutsche Post | 2: DHL | 3: Fedex
        $this->template->set('selected_shipping_service_type', $selected_shipping_service_obj['shipping_service_type']);
        
        $this->template->set('package_id', $package_id);
        $this->template->set('countries', $countries);
        $this->template->set('ppl', $ppl);
        $this->template->set('postbox_id', $postbox_id);
        $this->template->set('shipping_type', $shipping_type);
        $this->template->set('estamp_link', $estamp_link);
        $this->template->set('customer_address', $customer_address);
        $this->template->set('customer_id', $customer_id);
        $this->template->set('envelope_id', $envelope_id);
        $this->template->set('envelope', $envelope);
        $this->template->set('location_id', $envelope->location_id);
        $this->template->set('servicesAvailbale', $listShippingServices);
        $this->template->set("weight_unit", $weight_unit);
        $this->template->set("currency_short", $currency_short);

        // Display the current page
        $this->template->build('todo/shipping');
    }

    /**
     * Load shipping service form
     */
    public function shipping_service_form()
    {
        $this->template->set_layout(FALSE);
        $this->load->library("mailbox/mailbox_api");
        $this->load->model('scans/envelope_properties_m');
        $this->load->model('scans/envelope_shipping_request_m');
        $this->load->library('shipping/ShippingConfigs');
        $this->load->library('shipping/shipping_api');
        $this->load->library('settings/settings_api');
        $this->load->library('customers/customers_api');
        $this->load->library('addresses/addresses_api');
        $this->load->library('price/price_api');
        $this->lang->load('shipping/shipping');
        
        // Get customer address
        $customer_id = $this->input->get_post('customer_id');
        $envelope_id = $this->input->get_post('envelope_id');
        $shipping_service_id = $this->input->get_post('shipping_service_id');
        $shipping_service_template = $this->input->get_post('shipping_service_template');
        $shipment_type_id = $this->input->get_post('shipment_type_id');
        
        $shipping_service = shipping_api::getShippingServiceInfo($shipping_service_id);
        // $shipping_service = shipping_api::getShippingServiceByID($shipping_service_id);
        $this->template->set('customer_id', $customer_id);
        $this->template->set('envelope_id', $envelope_id);
        $this->template->set('shipment_service_id', $shipping_service_id);
        $this->template->set('shipping_service', $shipping_service);
        $this->template->set('shipment_type_id', $shipment_type_id);
        
        $envelope = $this->envelope_m->get($envelope_id);
        $this->template->set('envelope', $envelope);
        $this->template->set('location_id', $envelope->location_id);
        
        $currency_short = APUtils::get_currency_short_in_user_profiles();
        $weight_unit = APUtils::get_weight_unit_in_user_profiles();
        $length_unit = APUtils::get_length_unit_in_user_profiles();
        $decimal_separator = APUtils::get_decimal_separator_in_user_profiles();
        
        $this->template->set("weight_unit", $weight_unit);
        $this->template->set("currency_short", $currency_short);
        
        // $pending_envelope_customs = mailbox_api::getEnvelopeCustomsByEnvelopeId($customer_id, $envelope->id);
        $pending_envelope_customs = EnvelopeUtils::getEnvelopeCustoms($envelope->id);
        $is_pending_declare_customs =  $pending_envelope_customs != null;
        $total_customs_cost = 0;
        if ($is_pending_declare_customs) {
            $total_customs_cost = mailbox_api::get_total_customs_cost($customer_id, $envelope->id);
        }
        $this->template->set('total_customs_cost', $total_customs_cost);
        $this->template->set('is_pending_declare_customs', $is_pending_declare_customs);
        
        if ($shipping_service_template === APConstants::SHIPPING_SERVICE_TEMPLATE_DEFAULT) {
            // Display the current page
            $this->template->build('todo/shipping_service_default');
        }
        else if ($shipping_service_template === APConstants::SHIPPING_SERVICE_TEMPLATE_DPB) {
            $ppl = $this->get_package_ppl();
            $this->template->set('ppl', $ppl);
            
            $estamp_link = '';
            $this->template->set('estamp_link', $estamp_link);
            
            // Display the current page
            $this->template->build('todo/shipping_service_dpbrief');
        } else {
            // Check exist shipping request
            $envelope_shipping_request = $this->envelope_shipping_request_m->get_by_many(
                array(
                    "customer_id" => $customer_id,
                    "envelope_id" => $envelope->id
                ));
            
            // Calculate number of parcel
            $number_of_parcels = !empty($envelope_shipping_request) ? $envelope_shipping_request->number_parcel : 1;
            $customer_address = shipping_api::getShippingAddressByEnvelope($customer_id, $envelope->id);
            $shipment_street = $customer_address->shipment_street;
            $shipment_postcode = $customer_address->shipment_postcode;
            $shipment_city = $customer_address->shipment_city;
            $shipment_region = $customer_address->shipment_region;
            $shipment_country_id = $customer_address->shipment_country;
            $shippingInfo = array(
                ShippingConfigs::CUSTOMER_ID => $customer_id,
                ShippingConfigs::LOCATION_ID => $envelope->location_id,
                ShippingConfigs::SERVICE_ID => $shipping_service_id,
                ShippingConfigs::SHIPPING_TYPE => $shipment_type_id == '4' ? '2': '1',
                ShippingConfigs::CUSTOMS_VALUE => $total_customs_cost,
                ShippingConfigs::STREET => $shipment_street,
                ShippingConfigs::POSTAL_CODE => $shipment_postcode,
                ShippingConfigs::CITY => $shipment_city,
                ShippingConfigs::REGION => $shipment_region,
                ShippingConfigs::COUNTRY_ID => $shipment_country_id
            );
            
            $number_item_in_package = 1;
            // Calculate number of parcel (COLLECT)
            if ($shipment_type_id == '4') {
                // Get all envelopes
                $package_id = $envelope->package_id;
                $package_envelopes = $this->envelope_m->get_envelope_properties_by($customer_id, $package_id);
                $number_item_in_package = count($package_envelopes);
                $shipment_packages = array();
                // For each item in the package
                
                $total_dimension_we = 0;
                $total_width = 0;
                $total_height = 0;
                $total_length = 0;
                $number_shipment = 1;
                foreach ($package_envelopes as $envelope_item) {
                    $dimension_we = $envelope_item->weight;
                    $total_dimension_we += $dimension_we;
                    
                    $dimension_l =  $envelope_item->length;
                    $dimension_w =  $envelope_item->width;
                    $dimension_h =  $envelope_item->height;
                    $total_width += $envelope_item->width;
                    $total_height += $envelope_item->height;
                    $total_length += $envelope_item->length;
                
                    $package = array(
                        ShippingConfigs::PACKAGE_LENGTH => $dimension_l,
                        ShippingConfigs::PACKAGE_WIDTH => $dimension_w,
                        ShippingConfigs::PACKAGE_HEIGHT => $dimension_h,
                        ShippingConfigs::PACKAGE_WEIGHT => (float)($dimension_we / 1000),
                        ShippingConfigs::PACKAGE_NUMBERSHIPMENT => $number_shipment,
                    );
                    
                    $shipment_packages[] = $package;
                }
                try{
                    $number_collect_shippment = shipping_api::separatePackagesForCollectShipment($shipment_packages, $shippingInfo);

                    $number_of_parcels = count($number_collect_shippment);
                    $total_weight = 0;
                    $number_shipment = 1;
                    foreach($number_collect_shippment as $index => $item) {
                        $total_weight += $item['Weight'] * 1000;
                        $item[ShippingConfigs::PACKAGE_NUMBERSHIPMENT] = $number_shipment;
                        $number_shipment++;        
                    }
                    $this->template->set("number_collect_shippment", $number_collect_shippment);
                    $this->template->set("dimension_we", APUtils::view_convert_number_in_weight($total_dimension_we, $weight_unit, $decimal_separator, false));
                    $this->template->set("volumn_weight",$total_weight);
                    
                    if ($number_of_parcels > 1) {
                        $this->template->set("dimension_l", '~');
                        $this->template->set("dimension_w", '~');
                        $this->template->set("dimension_h", '~');
                    } else {
                        $this->template->set("dimension_l", APUtils::view_convert_number_in_length( $number_collect_shippment[0]['Length'], $length_unit, $decimal_separator, false));
                        $this->template->set("dimension_w", APUtils::view_convert_number_in_length( $number_collect_shippment[0]['Width'], $length_unit, $decimal_separator, false));
                        $this->template->set("dimension_h", APUtils::view_convert_number_in_length( $number_collect_shippment[0]['Height'], $length_unit, $decimal_separator, false));
                    }
                }catch (BusinessException $e) {
                    $this->template->set("dimension_l", '');
                    $this->template->set("dimension_w", '');
                    $this->template->set("dimension_h", '');
                    $this->template->set("number_collect_shippment", 0);
                    $this->template->set("dimension_we", 0);
                    $this->template->set("volumn_weight",0);
                    $this->template->set('error_message', $e->getMessage());
                }
            } 
            // DIRECT SHIPPING
            else {
                $number_item_in_package = 1;
                // Get dimension
                $dimension_l = '';
                $dimension_w = '';
                $dimension_h = '';
                $dimension_we = $envelope->weight;
                $total_dimension_we = $dimension_we;
                $dimension = $this->envelope_properties_m->get_by_many(array(
                    "envelope_id" => $envelope->id
                ));
                if (!empty($dimension)) {
                    #1058 add multi dimension capability for admin
                    $dimension_l =  floatval($dimension->length) == 0 ? '' : $dimension->length;
                    $dimension_w =  floatval($dimension->width) == 0 ? '' : $dimension->width;
                    $dimension_h =  floatval($dimension->height) == 0 ? '' : $dimension->height;
                }
                $shipment_packages = array();
                $package = array(
                        ShippingConfigs::PACKAGE_LENGTH => $dimension_l,
                        ShippingConfigs::PACKAGE_WIDTH => $dimension_w,
                        ShippingConfigs::PACKAGE_HEIGHT => $dimension_h,
                        ShippingConfigs::PACKAGE_WEIGHT => (float)($dimension_we / 1000),
                        ShippingConfigs::PACKAGE_NUMBERSHIPMENT => 1
                    );
                $shipment_packages[] = $package;
                $number_collect_shippment = array();
                $number_collect_shippment[0][ShippingConfigs::PACKAGE_LENGTH] = $dimension_l;
                $number_collect_shippment[0][ShippingConfigs::PACKAGE_WIDTH] = $dimension_w;
                $number_collect_shippment[0][ShippingConfigs::PACKAGE_HEIGHT] = $dimension_h;
                $number_collect_shippment[0][ShippingConfigs::PACKAGE_WEIGHT] = $dimension_we;
                $number_collect_shippment[0][ShippingConfigs::PACKAGE_NUMBERSHIPMENT] = 1;
                $this->template->set("number_collect_shippment", $number_collect_shippment);
                $number_of_parcels = count($number_collect_shippment);
                $this->template->set("dimension_we", APUtils::view_convert_number_in_weight($dimension_we, $weight_unit, $decimal_separator, false));
                $this->template->set("volumn_weight", number_format($dimension_we, 0, ',', ''));
                $this->template->set("dimension_l", APUtils::view_convert_number_in_length( $dimension_l, $length_unit, $decimal_separator, false));
                $this->template->set("dimension_w", APUtils::view_convert_number_in_length( $dimension_w, $length_unit, $decimal_separator, false));
                $this->template->set("dimension_h", APUtils::view_convert_number_in_length( $dimension_h, $length_unit, $decimal_separator, false));
            }
            $shipping_fee = (!empty($envelope_shipping_request) ? $envelope_shipping_request->shipping_fee : 0);
            $postal_charge = (!empty($envelope_shipping_request) ? $envelope_shipping_request->postal_charge : 0);
            $customs_handling = (!empty($envelope_shipping_request) ? $envelope_shipping_request->customs_handling : 0);
            $handling_charges = (!empty($envelope_shipping_request) ? $envelope_shipping_request->handling_charges : 0);
            if (empty($shipping_fee)) {
                $shipping_fee = 0;
            }
            // Get customs handling charge
            if (empty($customs_handling)) {
                $postbox = mailbox_api::getPostBoxByID($envelope->postbox_id);
                $location_id = $postbox->location_available_id;
                // $pricingMap = price_api::getPricingModelByPostboxID($envelope->postbox_id, $postbox->type);
                $pricing_maps = price_api::getPricingModelByCusotomerAndLocationID($customer_id, $location_id);
                $pricingMap = $pricing_maps[$postbox->type];
                
                if ($total_customs_cost > 1000) {
                    $customs_handling = $pricingMap['custom_declaration_outgoing_01'];
                } else {
                    $customs_handling = $pricingMap['custom_declaration_outgoing_02'];
                }
            }
            
            $this->template->set('number_of_parcels', $number_of_parcels);
            $this->template->set('shipping_fee', $shipping_fee);
            $this->template->set('postal_charge', $postal_charge);
            $this->template->set('customs_handling', $customs_handling);
            $this->template->set('handling_charges', $handling_charges);
            
            // Display the current page
            $this->template->build('todo/shipping_service_standard');
        }
    }
    /**
     * Process when user click preview estamp request
     */
    public function previewEstampRequest()
    {
        $output_file = $this->generatePdfEstampPreview();
        return $this->success_output($output_file);
    }

    /**
     * Process when user click buy estamp request
     */
    public function buyEstampRequest()
    {
        $estamp = $this->get_stamp();
        $output_file = $this->generatePdfEstampPreview($estamp ['local_path']);
        return $this->success_output($output_file);
    }

    /**
     * Generate PDF Estamp preview
     */
    public function generatePdfEstampPreview($estamp_file_path = '')
    {
        $shipping_type = $this->input->get_post('shipping_type');
        list ($i_width, $i_height) = explode("x", $shipping_type);
        $customer_id = $this->input->get_post('customer_id');
        $customer = $this->customer_m->get_by_many(array(
            "customer_id" => $customer_id
        ));

        // Load pdf library
        $this->load->library('pdf');
        $width = $i_width;
        $height = $i_height;
        if ($i_height > $i_width) {
            $width = $i_height;
            $height = $i_width;
        }
        if ($width < 101) {
            $width = 101;
        }
        if ($height < 50) {
            $height = 50;
        }

        // create new PDF document
        $pdf = new TCPDF("L", "mm", array(
            $width,
            $height
        ), true, 'UTF-8', false);
        $pdf->SetFont('times', 'N', 11, '', 'false');
        $pdf->SetMargins(0, PDF_MARGIN_TOP, 0);
        $pdf->SetHeaderMargin(0);
        $pdf->SetFooterMargin(0);

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        //set auto page breaks
        $pdf->SetAutoPageBreak(false, 0);

        //set image scale factor
        $pdf->setImageScale(1);

        // add a page
        $pdf->AddPage();

        $pdf->SetAutoPageBreak(false, 0);

        // new style
        $style = array(
            'border' => false,
            'padding' => 'auto',
            'fgcolor' => array(
                0,
                0,
                0
            ),
            'bgcolor' => false
        );

        $setting_logo_file = Settings::get(APConstants::SITE_LOGO_WHITE_CODE);
        if (empty($setting_logo_file)) {
            $image_file = APContext::getAssetPath() . '/images/invoice-pdf-header.png';
        } else {
            if (APUtils::startsWith($setting_logo_file, '/')) {
                $setting_logo_file = substr($setting_logo_file, 1);
            }
            $image_file = $setting_logo_file;
        }
        $pdf->Image($image_file, 5, 5, 35);

        if (empty($estamp_file_path)) {
            $image_file = Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'estamp/0.png';
            $pdf->Image($image_file, 45, 5, '', 20);
        } else {
            $pdf->Image($estamp_file_path, 45, 5, '', 20);
        }
        $start = 15;
        $i = 5;
        $name = $this->input->get_post('shipment_address_name');
        if (!empty($name)) {
            $pdf->writeHTMLCell(0, 0, 5, $start + $i, $name, 0, 1, 0, true, 'J', true);
            $i += 5;
        }

        $company = $this->input->get_post('shipment_company');
        if (!empty($company)) {
            $pdf->writeHTMLCell(0, 0, 5, $start + $i, $company, 0, 1, 0, true, 'J', true);
            $i += 5;
        }

        $street = $this->input->get_post('shipment_street');
        if (!empty($company)) {
            $pdf->writeHTMLCell(0, 0, 5, $start + $i, $street, 0, 1, 0, true, 'J', true);
            $i += 5;
        }

        $postcode = $this->input->get_post('shipment_postcode');
        $city = $this->input->get_post('shipment_city');
        $pdf->writeHTMLCell(0, 0, 5, $start + $i, $postcode . ', ' . $city, 0, 1, 0, true, 'J', true);
        $i += 5;

        $country = $this->input->get_post('shipment_country');
        $pdf->writeHTMLCell(0, 0, 5, $start + $i, $country, 0, 1, 0, true, 'J', true);

        // Close and output PDF document
        // $tmp = ini_get('upload_tmp_dir');
        if (!is_dir(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'estamp')) {
            mkdir(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'estamp', 0777, TRUE);
            chmod(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'estamp', 0777);
        }
        if (!is_dir(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'estamp/' . $customer->customer_id)) {
            mkdir(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'estamp/' . $customer->customer_id, 0777, TRUE);
            chmod(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'estamp/' . $customer->customer_id, 0777);
        }
        $tmp = Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'estamp/' . $customer->customer_id;

        $envelope_id = $this->input->get_post('envelope_id');
        $package_id = $this->input->get_post('package_id');
        if (empty($package_id)) {
            $package_id = 0;
        }
        $ppl = $this->input->get_post('ppl');
        if (empty($ppl)) {
            $ppl = 0;
        }
        $pdf_file_code = $customer->customer_code . '_' . $envelope_id . '_' . $package_id . '_' . $ppl;
        $output_file = $tmp . '/' . $pdf_file_code . '.pdf';
        $pdf->Output($output_file, 'F');

        // Upload this file to S3
        // S3 folder: estamp/<customer_id>/$envelope_code/0.png
        $default_bucket_name = $this->config->item('default_bucket');
        $amazon_relate_path = 'estamp/' . $customer_id . '/' . $pdf_file_code . '.pdf';

        try {
            $result = S3::putObjectFile($output_file, $default_bucket_name, $amazon_relate_path, S3::ACL_PRIVATE);
        } catch (Exception $e) {
        }

        return $output_file;
    }

    /**
     * Completed. $current_scan_type 1 : Envelope scan complete $current_scan_type 2 : Item scan complete $current_scan_type 5 : Trash complete
     */
    public function completed()
    {
        $this->load->library('scans/todo_api');
        $this->template->set_layout(FALSE);

        // Get customer address
        $customer_id = $this->input->get_post('customer_id');
        $envelope_id = $this->input->get_post('envelope_id');
        $current_scan_type = $this->input->get_post('current_scan_type');
        $invoice_flag = $this->input->get_post('invoice_flag');
        $category_type = $this->input->get_post('category_type');
        $check_page_item_flag = $this->input->get_post("check_page_flag", '');

        $envelope = $this->envelope_m->get_by_many(array(
            "id" => $envelope_id,
            "to_customer_id" => $customer_id
        ));
        
        $customer = $this->customer_m->get_by_many(array(
            "customer_id" => $customer_id
        ));

        // If this envelope does not exist.
        if (!$envelope || !$customer) {
            $this->success_output('');
            return;
        }
        
        // mark completed.
        $result = todo_api::mark_completed($envelope, $customer, $current_scan_type, $invoice_flag, $category_type, $check_page_item_flag);
        
        if($result['status']){
            $this->success_output('');
        }else{
            $this->error_output($result['message']);
        }
        return;
    }

    /**
     * Get e-stamp information.
     *
     * @return Ambigous <mixed, boolean, string, unknown, NULL>|multitype:
     */
    public function get_stamp()
    {
        $image_id = $this->input->get_post('image_id');
        $ppl = $this->input->get_post('ppl');
        $customer_id = $this->input->get_post('customer_id');
        $package_price = $this->input->get_post('package_price');
        $customer = $this->customer_m->get($customer_id);

        $envelope_id = $this->input->get_post('envelope_id');
        $package_id = $this->input->get_post('package_id');
        if (empty($package_id)) {
            $package_id = 0;
        }
        $envelope_code = $customer->customer_code . '_' . $envelope_id . '_' . $package_id . '_' . $ppl;
        // Kiem tra neu da mua estamp roi se ko mua lai
        $internetmarken_folder = Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'estamp/internetmarken/' . $envelope_code . '/';
        if (file_exists($internetmarken_folder . '0.png')) {
            $estamp_link = APContext::getFullBasePath() . 'scans/todo/preview_estamp_image?code=' . $envelope_code;
            return array(
                'estamp_link' => $estamp_link,
                'local_path' => $internetmarken_folder . '0.png'
            );
        } else {
            // Download from S3
            $default_bucket_name = ci()->config->item('default_bucket');
            $amazon_relate_path = 'estamp/' . $customer_id . '/' . $envelope_code . '.png';
            try {
                S3::getObject($default_bucket_name, $amazon_relate_path, $internetmarken_folder . '0.png');
                if (file_exists($internetmarken_folder . '0.png')) {
                    $estamp_link = APContext::getFullBasePath() . 'scans/todo/preview_estamp_image?code=' . $envelope_code;
                    return array(
                        'estamp_link' => $estamp_link,
                        'local_path' => $internetmarken_folder . '0.png'
                    );
                }
            } catch (Exception $e) {
            }
        }

        if (empty($image_id)) {
            // Will get image id depend on package size
            $image_id = '79929186';
        }
        $nusoap_client = new nusoap_client(Settings::get(APConstants::ESTAMP_LINK), 'wsdl');

        if ($nusoap_client->fault) {
            $text = 'Error: ' . $nusoap_client->fault;
        } else {
            if ($nusoap_client->getError()) {
                $text = 'Error: ' . $nusoap_client->getError();
            } else {
                $partner_id = Settings::get(APConstants::ESTAMP_PARTNER_ID);
                $request_timestamp = date('dmY-His', now() + 2 * 60 * 60);

                $key_phase = Settings::get(APConstants::ESTAMP_KEY_PHASE);
                $signature = Settings::get(APConstants::ESTAMP_SCHLUESSEL_DPWN_MARKTPLATZ);
                // Get checksum
                $check_sum = $partner_id . '::' . $request_timestamp . '::' . $key_phase . '::' . $signature;
                $check_sum_hash = md5($check_sum);
                $header = array(
                    'PARTNER_ID' => Settings::get(APConstants::ESTAMP_PARTNER_ID),
                    'REQUEST_TIMESTAMP' => $request_timestamp,
                    'KEY_PHASE' => Settings::get(APConstants::ESTAMP_KEY_PHASE),
                    'PARTNER_SIGNATURE' => substr($check_sum_hash, 0, 8)
                );
                $nusoap_client->setHeaders($header);

                // Set login parameters
                $login_param = array(
                    'username' => Settings::get(APConstants::ESTAMP_USER),
                    'password' => Settings::get(APConstants::ESTAMP_PASSWORD)
                );

                $proxy = $nusoap_client->getProxy();
                $authenuser = $proxy->authenticateUser($login_param);
                // var_dump($authenuser);
                if ($authenuser) {
                    $nusoap_client->setHeaders($header);
                    $proxy = $nusoap_client->getProxy();
                    $usertoken_param = array(
                        'userToken' => $authenuser ['userToken']
                    );
                    $checkoutShoppingCartPNG_positions_param = array(
                        'productCode' => $ppl,
                        'imageID' => '79929186',
                        'voucherLayout' => 'AddressZone'
                    );
                    $package_price = ((doubleval(str_replace(',', '.', $package_price)))) * 100;
                    $checkoutShoppingCartPNG_param = array(
                        'userToken' => $authenuser ['userToken'],
                        'ppl' => $ppl,
                        'positions' => $checkoutShoppingCartPNG_positions_param,
                        'total' => $package_price
                    );
                    $checkout_result = $proxy->checkoutShoppingCartPNG($checkoutShoppingCartPNG_param);

                    if ($checkout_result && !empty($checkout_result ['link'])) {
                        $link = $checkout_result ['link'];
                        $internetmarken_folder = Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'estamp/internetmarken/' . $envelope_code . '/';
                        if (!is_dir($internetmarken_folder)) {
                            mkdir($internetmarken_folder, 0777, TRUE);
                            chmod($internetmarken_folder, 0777);
                        }
                        APUtils::download($link, $internetmarken_folder . '/INTERNETMARKEN.zip');

                        // Unzip file
                        $zip = new ZipArchive();
                        if ($zip->open($internetmarken_folder . '/INTERNETMARKEN.zip') === TRUE) {
                            $zip->extractTo($internetmarken_folder);
                            $zip->close();
                        }

                        // Build internal local file
                        $estamp_link = APContext::getFullBasePath() . 'scans/todo/preview_estamp_image?code=' . $envelope_code;
                        // $this->success_output($estamp_link);
                        // Crop image
                        $this->load->library('files/files');
                        Files::crop_image($internetmarken_folder . '0.png', '400', '60', '480', '160');
                        Files::rezise_image($internetmarken_folder . '0.png', 480, 160);

                        // Upload to S3 folder (local_path: $internetmarken_folder . '0.png')
                        // S3 folder: estamp/<customer_id>/$envelope_code/0.png
                        $default_bucket_name = $this->config->item('default_bucket');
                        $amazon_relate_path = 'estamp/' . $customer_id . '/' . $envelope_code . '.png';
                        try {
                            $result = S3::putObjectFile($internetmarken_folder . '0.png', $default_bucket_name, $amazon_relate_path, S3::ACL_PRIVATE);
                        } catch (Exception $e) {
                        }

                        return array(
                            'estamp_link' => $estamp_link,
                            'local_path' => $internetmarken_folder . '0.png'
                        );
                    }
                }
            }
        }
    }

    /**
     * Get e-stamp information.
     *
     * @return Ambigous <mixed, boolean, string, unknown, NULL>|multitype:
     */
    public function test_estamp()
    {
        $nusoap_client = new nusoap_client(Settings::get(APConstants::ESTAMP_LINK), 'wsdl');

        if ($nusoap_client->fault) {
            $text = 'Error: ' . $nusoap_client->fault;
            return;
        }
        if ($nusoap_client->getError()) {
            $text = 'Error: ' . $nusoap_client->getError();
            return;
        }
        $partner_id = Settings::get(APConstants::ESTAMP_PARTNER_ID);
        $request_timestamp = date('dmY-His', now() + 2 * 60 * 60);

        $key_phase = Settings::get(APConstants::ESTAMP_KEY_PHASE);
        $signature = Settings::get(APConstants::ESTAMP_SCHLUESSEL_DPWN_MARKTPLATZ);
        // Get checksum
        $check_sum = $partner_id . '::' . $request_timestamp . '::' . $key_phase . '::' . $signature;
        $check_sum_hash = md5($check_sum);
        $header = array(
            'PARTNER_ID' => Settings::get(APConstants::ESTAMP_PARTNER_ID),
            'REQUEST_TIMESTAMP' => $request_timestamp,
            'KEY_PHASE' => Settings::get(APConstants::ESTAMP_KEY_PHASE),
            'PARTNER_SIGNATURE' => substr($check_sum_hash, 0, 8)
        );
        $nusoap_client->setHeaders($header);

        // Set login parameters
        $login_param = array(
            'username' => Settings::get(APConstants::ESTAMP_USER),
            'password' => Settings::get(APConstants::ESTAMP_PASSWORD)
        );

        $proxy = $nusoap_client->getProxy();
        $authenuser = $proxy->authenticateUser($login_param);
        // var_dump($authenuser);
        if ($authenuser) {
            $nusoap_client->setHeaders($header);
            $proxy = $nusoap_client->getProxy();
            $usertoken_param = array(
                'userToken' => $authenuser ['userToken']
            );
            $result = $proxy->createShopOrderId($usertoken_param);
            // Process image output
            $retrievePageFormats_result = $proxy->retrievePageFormats(array());
            // var_dump($retrievePageFormats_result);
            var_dump($result);

            //$retrievePublicGallery_result = $proxy->retrievePublicGallery(array());
            //var_dump($retrievePublicGallery_result['items'][0]);


            $checkoutShoppingCartPNG_positions_param = array(
                'productCode' => 1,
                'imageID' => '79929186',
                'voucherLayout' => 'AddressZone'
            );
            $checkoutShoppingCartPNG_param = array(
                'userToken' => $authenuser ['userToken'],
                'ppl' => 1,
                'positions' => $checkoutShoppingCartPNG_positions_param,
                'total' => 60
            );
            $checkout_result = $proxy->checkoutShoppingCartPNG($checkoutShoppingCartPNG_param);
            var_dump($checkout_result);
            var_dump($checkout_result ['detail'] ['ShoppingCartValidationException']);
        }
    }

    /**
     * Callback From: check_company()
     *
     * @param string $email
     *            The Email address to validate
     * @return bool
     */
    public function _check_shipment_company($shipment_company_name)
    {
        $shipment_address_name = $this->input->get_post('shipment_address_name');
        if (empty($shipment_address_name) && empty($shipment_company_name)) {
            $this->form_validation->set_message('_check_shipment_company', lang('addresses.shipment_company_required'));
            return false;
        }
        return true;
    }

    /**
     * Shipping function
     */
    public function view_customs()
    {
        $this->template->set_layout(FALSE);
        $envelope_id = $this->input->get_post('envelope_id');

        // Get envelope information
        $envelope = $this->envelope_m->get($envelope_id);

        // Display the current page
        $this->template->set('envelope_id', $envelope_id);
        $this->template->set('envelope', $envelope);
        $this->template->build('scans/todo/view_customs');
    }
    
    /**
     * Shipping function
     */
    public function edit_customs()
    {
        $this->template->set_layout(FALSE);
        $envelope_id = $this->input->get_post('envelope_id');
        
        $this->load->library('price/price_api');
        $this->load->model('mailbox/postbox_m');
        
        // Get envelope information
        $envelope = $this->envelope_m->get($envelope_id);
        
        $shipping_type =1; 
        if($envelope->collect_shipping_flag == "0"){
            $shipping_type =2;
            $envelopes = $this->envelope_m->get_many_by_many(array(
                "package_id" => $envelope->package_id
            ));
            
            $weight = 0;
            foreach($envelopes as $e){
                $weight += $e->weight;
            }
            $this->template->set('weight', $weight);
            
            // display the number if there is one collect shipping item.
            if(count($envelopes) == 1){
                $shipping_type =1; 
            }
        }
        
        $postbox = $this->postbox_m->get($envelope->postbox_id);
        $location_id = $postbox->location_available_id;
        $customer_id = $envelope->to_customer_id;
        // get pricing of envelopes.
        // $pricing_map = price_api::getPricingModelByPostboxID($envelope->postbox_id, $postbox->type);
        $pricing_maps = price_api::getPricingModelByCusotomerAndLocationID($customer_id, $location_id);
        $pricing_map = $pricing_maps[$postbox->type];

        // get all countries
        $countries = $this->countries_m->get_all();
        $this->template->set("countries", $countries);
        
        // Display the current page
        $this->template->set("shipping_type", $shipping_type);
        $this->template->set('envelope_id', $envelope_id);
        $this->template->set('envelope', $envelope);
        $this->template->set("pricing_map", $pricing_map);
        $this->template->build('scans/todo/edit_customs');
    }
    
    /**
     * Admin save declare custom
     */
    public function save_declare_customs()
    {
        $this->template->set_layout(FALSE);
        $this->load->library("mailbox/mailbox_api");
        $envelope_id = $this->input->get_post('envelope_id');
        $declare_customs = json_decode($this->input->get_post('customs_data'));

        // Get envelope information
        $envelope = $this->envelope_m->get($envelope_id);
        $postbox_id = $envelope->postbox_id;
        $customer_id = $envelope->to_customer_id;
        
        if($envelope->direct_shipping_flag == APConstants::OFF_FLAG){

            $envelope_customs = $this->envelope_customs_m->get_by_many(array(
                "envelope_id" => $envelope_id,
                "customer_id" => $customer_id
            ));
            $envelope_customs_id = 0;
            
            //If does not register custom to table envelope_customer, create new record
            if(empty($envelope_customs)){
                mailbox_api::regist_envelope_customs($customer_id, $envelope_id, $postbox_id, APConstants::DIRECT_FORWARDING, '');
                $envelope_customs = $this->envelope_customs_m->get_by_many(array(
                    "envelope_id" => $envelope_id,
                    "customer_id" => $customer_id
                ));
                $envelope_customs_id = $envelope_customs->id;
            } else {
                $envelope_customs_id = $envelope_customs->id;
            }
            
            //Delete old custom detail
            $this->envelope_customs_detail_m->delete_by_many(array(
                "customs_id" => $envelope_customs_id
            ));
            
            //Save new custom detail
            foreach ($declare_customs as $custom) {
                $country = $this->countries_m->get_by_many(array('country_name' => trim(isset($custom->country) ?$custom->country : null )));
                
                ci()->envelope_customs_detail_m->insert( array(
                    "customs_id" => $envelope_customs_id,
                    "material_name" => $custom->material_name,
                    "quantity" => $custom->quantity,
                    "cost" => $custom->cost,
                    'hs_code' => $custom->hs_code,
                    'country_origin' => !empty($country) ? $country->id: null
                ));
            }
            
            // Update process flag in envelope custom
            $this->envelope_customs_m->update_by_many(
                array(
                    "customer_id" => $customer_id,
                    "envelope_id" => $envelope_id
                ),
                array(
                    "process_flag" => APConstants::ON_FLAG
            ));
            
        } elseif($envelope->collect_shipping_flag == APConstants::OFF_FLAG) {
            //Select register custom
            $envelope_customs = $this->envelope_customs_m->get_many_by_many(array(
                "package_id" => $envelope->package_id,
                "customer_id" => $customer_id
            ));
            
            $envelope_customs_id = 0;
            
            //If does not register custom, register it and update new package_id
            if(empty($envelope_customs)){
                // Get all envelope in package
                $envelopes = $this->envelope_m->get_many_by_many(array(
                    "package_id" => $envelope->package_id,
                    "to_customer_id" => $customer_id
                ));
                foreach ($envelopes as $item) {
                    mailbox_api::regist_envelope_customs($customer_id, $item->id, $postbox_id, APConstants::COLLECT_FORWARDING, $item->package_id);
                }
            }
            
            //Update new custom data
            $envelope_customs = $this->envelope_customs_m->get_many_by_many(array(
                "package_id" => $envelope->package_id,
                "customer_id" => $customer_id
            ));
            
            if($envelope_customs){
                $customs_id = 0;
                
                //Delete old custom detail
                foreach($envelope_customs as $c){
                    $this->envelope_customs_detail_m->delete_by_many(array(
                        "customs_id" => $c->id
                    ));
                    $customs_id = $c->id;
                }
                
                
                //Save new custom detail
                foreach ($declare_customs as $custom) {
                    $country = $this->countries_m->get_by_many(array('country_name' => trim(isset($custom->country) ?$custom->country : null )));
                    ci()->envelope_customs_detail_m->insert( array(
                        "customs_id" => $customs_id,
                        "material_name" => $custom->material_name,
                        "quantity" => $custom->quantity,
                        "cost" => $custom->cost,
                        'hs_code' => $custom->hs_code,
                        'country_origin' => !empty($country) ? $country->id: null
                    ));
                }
            }
            
            
            //Update process flag
            $this->envelope_customs_m->update_by_many(
                array(
                    "customer_id" => $customer_id,
                    "package_id" => $envelope->package_id
                ),
                array(
                    "process_flag" => APConstants::ON_FLAG,
                ));
        }
        
        $this->success_output('You have updated customs data successfull!');
    }

    /**
     * Load envelope customs
     */
    public function load_declare_customs()
    {
        $this->template->set_layout(FALSE);
        // Get input condition
        $envelope_id = $this->input->get_post("envelope_id");
        $envelope_customs = EnvelopeUtils::getEnvelopeCustoms($envelope_id);
        $customs_id = 0;
        if (!empty($envelope_customs)) {
            $customs_id = $envelope_customs->id;
        }

        $array_condition = array();
        $array_condition ['envelope_customs_detail.customs_id'] = $customs_id;

        // If current request is ajax
        if ($this->is_ajax_request()) {

            // update limit into user_paging.
            $limit = isset($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APContext::getAdminPagingSetting();

            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging ['limit'] = $limit;

            // Call search method
            $query_result = $this->envelope_customs_detail_m->get_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);

            // Process output data
            $total = $query_result ['total'];
            $datas = $query_result ['data'];

            // Get output response
            $response = $this->get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);

            $i = 0;
            foreach ($datas as $row) {
                // get country
                $country = $this->countries_m->get($row->country_origin);
                
                $response->rows [$i] ['id'] = $row->id;
                $response->rows [$i] ['cell'] = array(
                    $row->id,
                    $row->material_name,
                    $row->hs_code,
                    !empty($country)? $country->country_name : '',
                    $row->quantity,
                    $row->cost
                );
                $i++;
            }
            echo json_encode($response);
        }
    }

    /**
     * Add & View envelope comment
     */
    public function comment_detail() {
        $this->template->set_layout(FALSE);
        $this->load->model('scans/envelope_comment_m');
        $envelope_id = $this->input->get_post('envelope_id');
        $envelope_comment = $this->envelope_comment_m->get_by_many(array(
            "envelope_id" => $envelope_id
        ));
        if ($_POST) {
            $this->form_validation->set_rules($this->comment_validation_rules);
            if ($this->form_validation->run()) {

                try {

                    $envelope = $this->envelope_m->get_by_many(array(
                        "id" => $envelope_id
                    ));
                    $customer_id = $envelope->to_customer_id;

                    $text = $this->input->post('text');

                    $del = $this->input->post('del');

                    if ($del == 'del' && !empty($envelope_comment)) {
                        // Delete comment
                        $this->envelope_comment_m->delete_by_many(array(
                            "envelope_id" => $envelope_id
                        ));
                         $message = lang('envelope_comment.delete_success');
                    } else if (empty($envelope_comment)) {
                        // Dang ky thong tin vao postbox
                        $this->envelope_comment_m->insert(array(
                            "customer_id" => $customer_id,
                            "envelope_id" => $envelope_id,
                            "created_date" => now(),
                            "created_by" => APContext::getAdminIdLoggedIn(),
                            "text" => $text
                        ));
                        $message = lang('envelope_comment.add_success');
                    } else {
                        // Dang ky thong tin vao postbox
                        $this->envelope_comment_m->update_by_many(array(
                            "customer_id" => $customer_id,
                            "envelope_id" => $envelope_id
                                ), array(
                            "last_updated_date" => now(),
                            "last_updated_by" => APContext::getAdminIdLoggedIn(),
                            "text" => $text
                        ));
                        $message = lang('envelope_comment.update_success');
                    }
                    $this->success_output($message);
                    return;
                } catch (Exception $e) {
                    $message = lang('envelope_comment.add_error');
                    $this->error_output($message);
                    return;
                }
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }

        if (empty($envelope_comment)) {
            $envelope_comment = new stdClass();
            $envelope_comment->id = '';
            // Loop through each validation rule
            foreach ($this->comment_validation_rules as $rule) {
                $envelope_comment->{$rule ['field']} = set_value($rule ['field']);
            }
        }

        // Display the current page
        $this->template->set('envelope_id', $envelope_id);
        $this->template->set('envelope_comment', $envelope_comment)->build('todo/envelope_comment');
    }

    /**
     * update remarked flag for all admin. ( #590)
     */
    public function update_remarked_flag()
    {
        $this->template->set_layout(FALSE);
        if ($this->is_ajax_request()) {
            $id = $this->input->get_post("id");
            $value = $this->input->get_post("value");
            $id = $this->envelope_m->update_by_many(array("id" => $id), array("remarked_flag" => $value));
            $this->success_output('');
        }
    }

    public  function get_list_shipping_service_available(){

        if ($this->is_ajax_request()) {
            
            if($_POST){
                $envelope_id = $this->input->get_post('envelope_id');
                $envelope = $this->envelope_m->get($envelope_id);
                
                //Some item have not in table envelopes, get latest status of items from history(envelope_completed)
                if(empty($envelope)){
                    $envelope = $this->envelope_completed_m->get_by_many_order(array("envelope_id" => $envelope_id),array("id" => "DESC"));
                }   
                
                $selected_shipping_service_obj = shipping_api::getShippingServiceIdByEnvelope($envelope_id);
                $shipping_services_available = todo_api::get_shipping_services_by_envelope($envelope);
                $servicesAvailbale = $shipping_services_available['listShippingServices'];
                $selected_shipping_service_id = $selected_shipping_service_obj['shipping_service_id'];
                
                $html = dropdown_list_shipping_service_by_location(array(
                    "data" => $servicesAvailbale,
                    "value" => $selected_shipping_service_id,
                    "value_key" => 'id',
                    "label_key" => 'name',
                    "name"  => 'shipping_services',
                    "id"    => 'shipping_services',
                    "clazz" => 'input-width tracking_disable',
                    "style" => 'width:240px;',
                    "html_option" => 'disabled="disabled"',
                    "has_empty" => false
            ));
            echo $html;exit;
            
            }
        }
        else echo "";exit;
        
    }

    public function get_info_item(){

        if ($this->is_ajax_request()) {
            if($_POST){

                $envelope_id = $this->input->get_post('envelope_id');
                
                if(!empty($envelope_id)){

                    $item = $this->envelope_completed_m->get_info_item($envelope_id);
                   // echo "<pre>";print_r($item);exit;
                    
                    if(is_object($item)){
                        $item->shipping_date = date("d.m.Y H:i",$item->shipping_date);
                        $item->completed_by  = ucwords( empty($item->admin_name) ? $item->shipment_address_name : $item->admin_name );
                        echo json_encode($item); exit;
                    }
                }
            
            }
        }
        echo "";exit;
    }

    public function save_tracking_number(){
        $this->load->library('customers/customers_api');
        
        if ($this->is_ajax_request()) {
            if($_POST){
                $envelope_id = $this->input->get_post('envelope_id');
                $shipping_services = $this->input->get_post('shipping_services');
                $tracking_number = $this->input->get_post('tracking_number');
                $type = $this->input->get_post('type');
                $no_tracking_flag = ($type == "no_tracking" || empty($tracking_number) ) ? 1 : 0;
                //Insert tracking number
                todo_api::save_tracking_number($envelope_id, $tracking_number, $shipping_services, $no_tracking_flag);
                
                // save tracking number of customer shipping report.
                $envelope = $this->envelope_m->get($envelope_id);
                customers_api::save_tracking_number_customer_shipping_report($envelope->to_customer_id, $envelope, $tracking_number);
                
                $this->success_output("");
                return;
            }
        }
    }
    
    /**
     * Calculate shipping rate
     * @return boolean
     */
    public function shipping_calculator()
    {
        $this->load->library('shipping/ShippingConfigs');
        $this->load->library('common/common_api');
        $this->load->library('shipping/shipping_api');
        $this->load->library('settings/settings_api');
        $this->load->library('customers/customers_api');
        $this->load->library('addresses/addresses_api');
        
        $this->template->set_layout(FALSE);

        $customer_id = $this->input->get_post("customer_id");
        $envelope_id = $this->input->get_post("envelope_id");
        
        $envelope = $this->envelope_m->get($envelope_id);
        $customer = $this->customer_m->get($customer_id);
        
        // Get shipping from screen UI
        // $customer_address = shipping_api::getShippingAddressByEnvelope($customer_id, $envelope_id);
        
        $location_id = $envelope->location_id;
        $shipment_service_id = $this->input->get_post("shipment_service_id");
        
        // Check shipping service id
        $service = shipping_api::getShippingServiceInfo($shipment_service_id);
        // If this service don't have API
        if (empty($service)) {
            $data = array(
                'currency_short' => '',
                'postal_charge' => '',
                'customs_handling' => '',
                'handling_charges' => '',
                'total_vat' => '',
                'total_charge' => '',
                'raw_postal_charge' => '',
                'raw_customs_handling' => '',
                'raw_handling_charges' => '',
                'raw_total_charge' => '',
                'number_parcel' => '',
                'service_available_flag' => APConstants::OFF_FLAG
            );
            $this->success_output('', $data);
            return;
        }
        
        $shipment_type_id = $this->input->get_post("shipment_type_id");
        if ($shipment_type_id == '3') {
            $shipment_type_id = "1";
        } else if ($shipment_type_id == '4') {
            $shipment_type_id = "2";
        }
        $total_insured_value = $this->convert_decimal_number($this->input->get_post("customs_insurance_value"));
        $shipment_street = $this->input->get_post('shipment_street');
        $shipment_postcode = $this->input->get_post('shipment_postcode');
        $shipment_city = $this->input->get_post('shipment_city');
        $shipment_region = $this->input->get_post('shipment_region');
        $shipment_country_id = $this->input->get_post('shipment_country');
        $shipment_phone_number = $this->input->get_post('shipment_phone_number');
        $shipment_address_name = $this->input->get_post('shipment_address_name');
        $shipment_company = $this->input->get_post('shipment_company');
        $customs_process_flag = $this->input->get_post('customs_process_flag');
        if ($customs_process_flag == APConstants::OFF_FLAG) {
            $total_insured_value = 0;
        }

        $shippingInfo = array(
            ShippingConfigs::CUSTOMER_ID => $customer_id,
            ShippingConfigs::LOCATION_ID => $location_id,
            ShippingConfigs::SERVICE_ID => $shipment_service_id,
            ShippingConfigs::SHIPPING_TYPE => $shipment_type_id,
            ShippingConfigs::CUSTOMS_VALUE => $total_insured_value,
            ShippingConfigs::STREET => $shipment_street,
            ShippingConfigs::POSTAL_CODE => $shipment_postcode,
            ShippingConfigs::CITY => $shipment_city,
            ShippingConfigs::REGION => $shipment_region,
            ShippingConfigs::COUNTRY_ID => $shipment_country_id,
            ShippingConfigs::NAME => $shipment_address_name,
            ShippingConfigs::PHONE_NUMBER => $shipment_phone_number,
            ShippingConfigs::EMAIL => $customer->email,
            ShippingConfigs::COMPANY_NAME => $shipment_company
        );
        $separate_package_flag = false;
        // With Direct case we use the original Lenght, Weight, Width in the screen so need to calculate to Volumn Size
        // With Collect case we use the Volumn Size in the screen
        if ($shipment_type_id == '1') {
            $separate_package_flag = true;
        }
        $result = shipping_api::shipping_calculator(
                $customer_id,
                $shippingInfo,
                $this->input->get_post('number_of_parcels'),
                $this->convert_decimal_number($this->input->get_post('length')),
                $this->convert_decimal_number($this->input->get_post('width')),
                $this->convert_decimal_number($this->input->get_post('height')),
                $this->convert_decimal_number($this->input->get_post('volumn_weight')),
                $this->input->get_post('multiple_quantity'),
                $this->input->get_post('multiple_number_shipment', ''),
                $this->input->get_post('multiple_length'),
                $this->input->get_post('multiple_width'),
                $this->input->get_post('multiple_height'),
                $this->input->get_post('multiple_weight'),
                $this->input->get_post('currency_id'),
                $separate_package_flag );
        
        //Build select label option drop downlist
        if (!empty($result['data']['carrier_code'])){
            $carrier_code = $result['data']['carrier_code'];
            //Fedex
            if ($carrier_code == APConstants::FEDEX_CARRIER) {
                $lable_code = APConstants::SHIPPING_TYPE_FEDEX_LABEL_SIZE;
            } 
            // Canada post
            else if ($carrier_code == APConstants::CANADAPOST_CARRIER) {
                $lable_code = APConstants::SHIPPING_TYPE_CANADAPOST_LABEL_SIZE;
            }
            // Shippo
            else if ($carrier_code == APConstants::SHIPPO_CARRIER) {
                $lable_code = APConstants::SHIPPING_TYPE_SHIPPO_LABEL_SIZE;
            }
            $this->load->helper('common');
            $result['data']['label_dropdown_list'] = code_master_form_dropdown(array(
                        "code" => $lable_code,
                        "value" => '',
                        "name" => 'lable_size',
                        "id"    => 'lable_size',
                        "clazz" => 'input-width',
                        "style" => 'width: 200px',
                        "has_empty" => false
                    ));
        }

        if($result['status']){
            $this->success_output('', $result['data']);
        }else{
            $this->error_output('System cannot calculate shipping fee automatically for this shipping service.', $result['data']);
        }
        return ;
    }
    
    /**
     * Show the form view of input parcels information
     */
    public function input_parcels_info()
    {
        $this->template->set_layout(FALSE);

        $mode = $this->input->get_post('mode');
        $parcelsData = json_decode($this->input->get_post('parcelsData'));
        $lines = $this->input->get_post('lines', 1);

        // Display the current page
        $this->template->set('mode', $mode);
        $this->template->set('parcels', $parcelsData);
        $this->template->set('lines', $lines);

        $this->template->build('todo/input_parcels_info');
    }

    /**
     * Recalculate shipping values in other currency
     */
    public function convert_currency()
    {
        $this->load->model('settings/currencies_m');
        $this->load->model('customers/customer_m');
        $this->template->set_layout(FALSE);

        $converted_currency_id = $this->input->get_post('converted_currency_id');
        $base_postal_charge = $this->input->get_post('base_postal_charge', 0);
        $base_customs_handling = $this->input->get_post('base_customs_handling', 0);
        $base_handling_charges = $this->input->get_post('base_handling_charges', 0);
        $base_VAT = $this->input->get_post('base_VAT', 0);
        $base_total_charge = $this->input->get_post('base_total_charge', 0);

        $currency = $this->currencies_m->get($converted_currency_id);
        $currency_rate = $currency->currency_rate;
        $decimal_separator = $this->customer_m->get_standard_setting_decimal_separator();

        $converted_postal_charge = APUtils::convert_currency($base_postal_charge, $currency_rate, 2, $decimal_separator);
        $converted_customs_handling = APUtils::convert_currency($base_customs_handling, $currency_rate, 2, $decimal_separator);
        $converted_handling_charges = APUtils::convert_currency($base_handling_charges, $currency_rate, 2, $decimal_separator);
        $converted_VAT = APUtils::convert_currency($base_VAT, $currency_rate, 2, $decimal_separator);
        $converted_total_charge = APUtils::convert_currency($base_total_charge, $currency_rate, 2, $decimal_separator);

        $this->success_output(lang('convert_currency_success'), array(
            'postal_charge' => $converted_postal_charge,
            'customs_handling' => $converted_customs_handling,
            'handling_charges' => $converted_handling_charges,
            'VAT' => $converted_VAT,
            'total_charge' => $converted_total_charge
        ));
        return true;
    }
    
    /**
     * Call API to create label for preparing shipping
     */
    public function create_label(){
        $this->template->set_layout(false);
        $this->lang->load('shipping/shipping');
        $this->load->model('scans/envelope_shipping_m');
        $this->load->library(array(
            'shipping/ShippingConfigs',
            'shipping/ShippingLabel',
            'invoices/export',
            'shipping/shipping_api',
            'shipping/CanadaPostShippingLabel',
            'shipping/ShippoLabel'
        ));
            
        // get param
        $envelope_id = $this->input->get_post('envelope_id');
        
        // Gets envelope
        $envelope = $this->envelope_m->get($envelope_id);
        $customer_id = $envelope->to_customer_id;
        $customer = $this->customer_m->get($customer_id);
        
        // get number of parcel
        $number_of_parcels = $this->input->get_post('number_of_parcels');
        
        // get multiple
        $multiple_quantity = $this->input->get_post('multiple_quantity');
        $multiple_length = $this->input->get_post('multiple_length');
        $multiple_width = $this->input->get_post('multiple_width');
        $multiple_height = $this->input->get_post('multiple_height');
        $multiple_weight = $this->input->get_post('multiple_weight');
        $multiple_number_shipment = $this->input->get_post('multiple_number_shipment', '');
        
        // Gets selection forwarding address.
        $shipping_from = $this->location_m->getLocationInfo($envelope->location_id);
        $shipping_to = shipping_api::getShippingAddressByEnvelope($customer_id, $envelope_id);
        
        $location_id = $envelope->location_id;
        $shipment_service_id = $this->input->get_post("shipment_service_id", "1");
        $shipment_type_id = $this->input->get_post("shipment_type_id");
        if ($shipment_type_id == '3') {
            $shipment_type_id = 1;
        } else if ($shipment_type_id == '4') {
            $shipment_type_id = 2;
        }
        $total_insured_value = $this->convert_decimal_number($this->input->get_post("total_insured_value"));
        $shipment_street = $this->input->get_post('shipment_street');
        $shipment_postcode = $this->input->get_post('shipment_postcode');
        $shipment_city = $this->input->get_post('shipment_city');
        $shipment_region = $this->input->get_post('shipment_region');
        $shipment_country_id = $this->input->get_post('shipment_country');
        $customs_process_flag = $this->input->get_post('customs_process_flag');
        $shipment_phone_number = $this->input->get_post('shipment_phone_number');
        $shipment_address_name = $this->input->get_post('shipment_address_name');
        $shipment_company = $this->input->get_post('shipment_company');
        if ($customs_process_flag == APConstants::OFF_FLAG) {
            $total_insured_value = 0;
        }
        
        $shipping_to->shipment_address_name = $shipment_address_name;
        $shipping_to->shipment_company = $shipment_company;
        $shipping_to->shipment_street = $shipment_street;
        $shipping_to->shipment_postcode = $shipment_postcode;
        $shipping_to->shipment_city = $shipment_city;
        $shipping_to->shipment_region = $shipment_region;
        $shipping_to->shipment_country = $shipment_country_id;
        $shipping_to->shipment_phone_number = $shipment_phone_number;
        $shipping_to->shipment_email = $customer->email;

        $shippingInfo = array(
            ShippingConfigs::CUSTOMER_ID => $customer_id,
            ShippingConfigs::LOCATION_ID => $location_id,
            ShippingConfigs::SERVICE_ID => $shipment_service_id,
            ShippingConfigs::SHIPPING_TYPE => $shipment_type_id,
            ShippingConfigs::CUSTOMS_VALUE => $total_insured_value,
            ShippingConfigs::STREET => $shipment_street,
            ShippingConfigs::POSTAL_CODE => $shipment_postcode,
            ShippingConfigs::CITY => $shipment_city,
            ShippingConfigs::REGION => $shipment_region,
            ShippingConfigs::COUNTRY_ID => $shipment_country_id,
            ShippingConfigs::NAME => $shipment_address_name,
            ShippingConfigs::PHONE_NUMBER => $shipment_phone_number,
            ShippingConfigs::EMAIL => $customer->email,
            ShippingConfigs::COMPANY_NAME => $shipment_company,
        );
        
        $service = shipping_api::getShippingServiceInfo($shipment_service_id);
        if (empty($service)) {
            $message = lang('shipping_service.not_allow');
            $this->error_output($message);
            return;
        }
        // EUR default currency       
        $list_shipment_package = [];
        // check collect shipment
        if($number_of_parcels > 1){
            $arrayQuantity = explode(ShippingConfigs::GROUP_PACKAGE_SEPARATOR, $multiple_quantity);
            $arrayNumberShipment = explode(ShippingConfigs::GROUP_PACKAGE_SEPARATOR, $multiple_number_shipment);
            $arrayLength = explode(ShippingConfigs::GROUP_PACKAGE_SEPARATOR, $multiple_length);
            $arrayWidth = explode(ShippingConfigs::GROUP_PACKAGE_SEPARATOR, $multiple_width);
            $arrayHeight = explode(ShippingConfigs::GROUP_PACKAGE_SEPARATOR, $multiple_height);
            $arrayWeight = explode(ShippingConfigs::GROUP_PACKAGE_SEPARATOR, $multiple_weight);
            
            // Call from customer information
            if (count($arrayNumberShipment) == 0) {
                $list_shipment_package = shipping_api::getAutoListShipmentPackage($arrayQuantity, $arrayNumberShipment, 
                        $arrayLength, $arrayWidth, $arrayHeight, $arrayWeight);
            }
            // Call from admin shipping UI
            else {
                $list_shipment_package = shipping_api::getManualListShipmentPackage($arrayQuantity, $arrayNumberShipment, 
                        $arrayLength, $arrayWidth, $arrayHeight, $arrayWeight, $service);
            }
        }else{
            $package_info['width'] =  $this->convert_decimal_number($this->input->get_post('width'));
            $package_info['height'] =  $this->convert_decimal_number($this->input->get_post('height'));
            $package_info['length'] =  $this->convert_decimal_number($this->input->get_post('length'));
            $package_info['weight'] =  $this->convert_decimal_number($this->input->get_post('volumn_weight'));
            $package_type = APConstants::ENVELOPE_TYPE_PACKAGE;
            if (empty($package_info['width']) || empty($package_info['height']) || empty($package_info['length'])) {
                $package_type = APConstants::ENVELOPE_TYPE_LETTER;
            }
            $package_info['width'] = empty($package_info['width']) ? 1 : $package_info['width'];
            $package_info['height'] = empty($package_info['height']) ? 1 : $package_info['height'];
            $package_info['length'] = empty($package_info['length']) ? 1 : $package_info['length'];
            $package_info['weight'] = empty($package_info['weight']) ? 1 : $package_info['weight'];
            $package = array(
                ShippingConfigs::PACKAGE_LENGTH => $package_info['length'],
                ShippingConfigs::PACKAGE_WIDTH => $package_info['width'],
                ShippingConfigs::PACKAGE_HEIGHT => $package_info['height'],
                ShippingConfigs::PACKAGE_WEIGHT => $package_info['weight'] / 1000,
                ShippingConfigs::PACKAGE_TYPE => $package_type
            );
            $list_shipment_package[] = $package;
        }
        
        // Validate list of shipment
        foreach ($list_shipment_package as $package) {
            $validLimit = !shipping_api::exceedLimitPackage($service, $package);
            if (!$validLimit) {
                $message = lang('over_weight_error_message');
                $this->error_output($message);
                return;
            }
        }
        
        // Check folder and get file name
        if (!is_dir(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'fedex')) {
            mkdir(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'fedex', 0777, TRUE);
            chmod(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'fedex', 0777);
        }
        if (!is_dir(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'fedex/' . $customer_id)) {
            mkdir(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'fedex/' . $customer_id, 0777, TRUE);
            chmod(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'fedex/' . $customer_id, 0777);
        }
        $folder = Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'fedex/' . $customer_id;
        $filename = $customer->customer_code. '_' . $envelope_id.'_'.date('Ymd').'.pdf';
        $output_file_name = $folder.'/' .$filename;
        
        // Delete old file
        if (file_exists($output_file_name)) {
            unlink($output_file_name);
        }
        
        try{
            $list_file_output = array();
            // create label shipping.
            $result = false;
            $file_index = 1;
            $tracking_number = array();
            foreach ($list_shipment_package as $package) {
                $currency_id = 1;
                $separate_package_flag = false;
                $fedex_total = shipping_api::shipping_calculator(
                        $customer_id,
                        $shippingInfo,
                        1 ,
                        $package[ShippingConfigs::PACKAGE_LENGTH],
                        $package[ShippingConfigs::PACKAGE_WIDTH],
                        $package[ShippingConfigs::PACKAGE_HEIGHT],
                        $package[ShippingConfigs::PACKAGE_WEIGHT] * 1000,
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        $currency_id,
                        $separate_package_flag);

                $fedex_data = array();
                if ($fedex_total['status'] == false) {
                    $fedex_data['postal_charge'] = 0;
                    $fedex_data['customs_handling'] = 0;
                    $fedex_data['handling_charges'] = 0;
                    $fedex_data['total_vat'] = 0;
                    $fedex_data['total_charge'] = 0;
                    $fedex_data['total_charge_no_vat'] = 0;
                } else {
                    $fedex_data = $fedex_total['data'];
                }
                $package_info = array();
                $package_info['currency'] =  'EUR';
                $package_info['label_size'] =  $this->input->get_post('label_size');
                $package_info['postal_charge'] =  str_replace(",", ".", $fedex_data['postal_charge']);
                $package_info['customs_handling'] =  str_replace(",", ".", $fedex_data['customs_handling']);
                $package_info['handling_charges'] =  str_replace(",", ".", $fedex_data['handling_charges']);
                $package_info['total_vat'] =  str_replace(",", ".", $fedex_data ['total_vat']);
                $package_info['total_charge'] =  str_replace(",", ".", $fedex_data['total_charge']);
                $package_info['total_charge_no_vat'] =  str_replace(",", ".", $fedex_data['total_charge_no_vat']);

                $package_info['width'] =  $package[ShippingConfigs::PACKAGE_WIDTH];
                $package_info['height'] =  $package[ShippingConfigs::PACKAGE_HEIGHT];
                $package_info['length'] =  $package[ShippingConfigs::PACKAGE_LENGTH];
                $package_info['weight'] =  $package[ShippingConfigs::PACKAGE_WEIGHT];
                $package_info[ShippingConfigs::PACKAGE_TYPE] =  $package[ShippingConfigs::PACKAGE_TYPE];
                $package_info['total_insured_value'] = $total_insured_value;
                $package_info['carrier'] = !empty($fedex_data['carrier']) ? $fedex_data['carrier'] : null;//For shippo label
                //$package_info['service'] = empty($fedex_data['carrier']) ? $service->api_svc_code1 : $service->api_svc_code2;

                //Add more info to service to call API create label
                $service->api = $fedex_data['api_code'];
                $service->credential = $fedex_data['api_credential'];
                
                // Write autdit log
                $message = '{shipping_from:'. json_encode($shipping_from).',';
                $message = $message. 'shipping_to:'. json_encode($shipping_to).',';
                $message = $message. 'package_info:'. json_encode($package_info).',';
                $message = $message.  'service:'. json_encode($service).'}';
                log_audit_message(APConstants::LOG_INFOR, $message, FALSE, 'create_label');

                $filename_item = $customer->customer_code. '_' . $envelope_id.'_'.date('Ymd').'_'.$file_index.'.pdf';
                $output_file_name_item = $folder.'/' .$filename_item;
                $output_full_file_name_item = $folder.'/' .$customer->customer_code. '_' . $envelope_id.'_'.date('Ymd').'_'.$file_index.'_Full.pdf';
                //Remove old lable file
                if (file_exists($output_full_file_name_item)) {
                    unlink($output_full_file_name_item);
                }
                
                //Call API to create lable
                if (!empty($fedex_data['carrier_code'])){
                    $carrier_code = $fedex_data['carrier_code'];
                     // fedex shipping label.
                    if ($carrier_code == APConstants::FEDEX_CARRIER) {
                        $label = new ShippingLabel();
                        $label->init_label($shipping_from, $shipping_to, $package_info, $service);
                        $label->setEndPoint(empty($service->api->site_id)  ? ShippingConfigs::FEDEX_PRODUCTION_URL.'/ship' : $service->api->site_id.'/ship');
                        $result = $label->create($output_full_file_name_item);
                        $tracking_number[] = $label->getTrackingNumber();
                        APUtils::getFirstPdfPage($output_full_file_name_item, $output_file_name_item, "P");
                        if (!empty($output_file_name_item)) {
                            $list_file_output[] = $output_file_name_item;
                        }
                        if (file_exists($output_full_file_name_item)) {
                            unlink($output_full_file_name_item);
                        }
                    } else if($carrier_code == APConstants::CANADAPOST_CARRIER){
                        $label = new CanadaPostShippingLabel();
                        $label->init_label($shipping_from, $shipping_to, $package_info, $service);
                        $result = $label->create($output_full_file_name_item);
                        $tracking_number[] = $label->getTrackingNumber();
                        if (!empty($output_file_name_item)) {
                            $list_file_output[] = $output_full_file_name_item;
                        }
                    } else if($carrier_code == APConstants::SHIPPO_CARRIER){
                        $label = new ShippoLabel();
                        $label->init_label($shipping_from, $shipping_to, $package_info, $service);
                        $result = $label->create($output_full_file_name_item);
                        APUtils::download($result, $output_full_file_name_item);
                        $tracking_number[] = $label->getTrackingNumber();
                        if (!empty($output_file_name_item)) {
                            $list_file_output[] = $output_full_file_name_item;
                        }
                    }
                }    
                
                $file_index++;
                
            }
            
            // Clone one more label
            $filename_01 = $customer->customer_code. '_' . $envelope_id.'_'.date('Ymd').'_01.pdf';
            $output_file_name_01 = $folder.'/' .$filename_01;
            $filename_02 = $customer->customer_code. '_' . $envelope_id.'_'.date('Ymd').'_02.pdf';
            $output_file_name_02 = $folder.'/' .$filename_02;
            
            // Merge file
            APUtils::mergePDFfiles($list_file_output, $output_file_name_01, "P");
            APUtils::mergePDFfiles($list_file_output, $output_file_name_02, "P");
            APUtils::mergePDFfiles(array($output_file_name_01, $output_file_name_02), $output_file_name, "P");
            // Delete old file
            foreach ($list_file_output as $item) {
                if (file_exists($item)) {
                    unlink($item);
                }
            }
            if (file_exists($output_file_name_01)) {
                unlink($output_file_name_01);
            }
            if (file_exists($output_file_name_02)) {
                unlink($output_file_name_02);
            }

            // Generate proforma invoices again with tracking number
            $list_track_service = array();
            foreach ($tracking_number as $number) {
                $list_track_service[] = array(
                    'service_name' => $service->service_name,
                    'track_number' => $number
                );
            }
            // Only include tracking number had more than 1 shipment
            if (count($tracking_number) > 1) {
                $this->export->export_custom_invoice($envelope_id, $list_track_service, true);
            }

            if($result){
                // Upload this file to S3
                // S3 folder: estamp/<customer_id>/$envelope_code/0.png
                $default_bucket_name = $this->config->item('default_bucket');
                $amazon_relate_path = 'fedex/' . $customer_id . '/' . $filename . '.pdf';
                try {
                    S3::putObjectFile($output_file_name, $default_bucket_name, $amazon_relate_path, S3::ACL_PRIVATE);
                } catch (Exception $e) {
                }

                // check envelope shipping
                $envelope_shipping = $this->envelope_shipping_m->get_by_many(array(
                    "envelope_id" => $envelope_id,
                    "customer_id" => $customer_id
                ));

                if($envelope_shipping){
                    $this->envelope_shipping_m->update_by_many(array(
                        "envelope_id" => $envelope_id,
                        "customer_id" => $customer_id
                    ), array(
                        "estamp_url" => $output_file_name
                    ));
                }else{
                    $this->envelope_shipping_m->insert(array(
                        "envelope_id" => $envelope_id,
                        "customer_id" => $customer_id,
                        "postbox_id" => $envelope->postbox_id,
                        "estamp_url" => $output_file_name
                    ));
                }

                $this->success_output("", array(
                    "shipping_service_id" => $shipment_service_id,
                    "tracking_number" => implode(",", $tracking_number)
                ));
            }else{
                $this->error_output($label->getMessage());
            }
        }catch (ThirdPartyException $e){
            $this->error_output($e->getMessage());
        }
        return;
    }
    
    public function preview_fedex_file(){
        $this->template->set_layout(false);
        $this->load->model('scans/envelope_shipping_m');
        
        $envelope_id = $this->input->get_post('envelope_id');
        
        $envelope_shipping = $this->envelope_shipping_m->get_by_many(array("envelope_id"=> $envelope_id));
        $fedex_file = '';
        if($envelope_shipping && $envelope_shipping->estamp_url){
            $fedex_file = $envelope_shipping->estamp_url;
        }
        
        if($fedex_file){
            // Get extends file
            header('Content-Disposition: inline');
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . filesize($fedex_file));
            header('Accept-Ranges: bytes');
            header('Content-Type: application/pdf');

            readfile($fedex_file);
        }
        
        return;
    }
    
    private function convert_decimal_number($number){
        if (empty($number)) {
            return 0;
        }
        //$number_str = str_replace(".", "", $number);
        return str_replace (",", ".", $number);
    }
}
