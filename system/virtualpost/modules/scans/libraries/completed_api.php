<?php defined('BASEPATH') or exit('No direct script access allowed');

class completed_api extends Admin_Controller
{
    
    public function __construct() {

        ci()->load->model(array(
            'scans/envelope_m',
            'scans/envelope_completed_m',
            'customers/customer_m',
            'invoices/invoice_detail_m',
            'invoices/invoice_summary_m',
            'scans/envelope_properties_m',
            'scans/envelope_shipping_tracking_m',
            'email/email_m',
            'shipping/shipping_services_m'
        ));
        
        $this->load->library(array(
            "invoices/invoices",
            "mailbox/mailbox_api",
            "scans/scans_api",
            "scans/todo_api"
        ));

        ci()->lang->load(array(
            'scans/scans'
        ));
    }

    /**
     * Get item information in check page item page
     * @param type $envelope_code
     * @return type
     */
   public static function check_item($envelope_code)
    {

        $envelope = new stdClass();
        $envelope->activity_id = '';
        $envelope_completed = '';
        
        #1058 add multi dimension capability for admin
        $date_format = APUtils::get_date_format_in_user_profiles();
        $decimal_separator = APUtils::get_decimal_separator_in_user_profiles();
        $length_unit = APUtils::get_length_unit_in_user_profiles();
        $weight_unit = APUtils::get_weight_unit_in_user_profiles();
        //GET envelope information
		if ($envelope_code) {
            //Get envelope info from tbl envelope
			$envelope = ci()->envelope_m->get_envelope_by_code($envelope_code);
            //Get envelope info from tbl envelope_complete
			$envelope_completed = ci()->envelope_completed_m->get_envelope_complete_by_code($envelope_code);
            
            //If does not existe envelope data in table envelope but existe data in table envelope_complete
            if (!$envelope && $envelope_completed) {
                $envelope = $envelope_completed;
                $envelope->id = $envelope_completed->envelope_id;
                $envelope->envelope_code = $envelope_completed->activity_code;
                $envelope->activity_id2 = $envelope_completed->activity_id;
                $envelope->activity = lang('envelope.completed_activity_' . $envelope->activity_id);
                $envelope->last_updated_date = APUtils::viewDateFormat($envelope->last_updated_date,$date_format );
                $envelope->completed_date = $envelope->completed_date ? APUtils::viewDateFormat($envelope->completed_date, $date_format) : "";
                $envelope->completed_by = ($envelope->admin_name) ? $envelope->admin_name : ($envelope->shipment_address_name ? $envelope->shipment_address_name : "");
                $envelope->category_type = Settings::get_label(APConstants::CATEGORY_TYPE_CODE, $envelope->category_type);
                $envelope->status_envelope = "not_in_storage";
			} else {
                //If existe envelope data in table envelope
                if ($envelope) {
                    $item_scan_flag = $envelope->item_scan_flag;
                    $envelope_scan_flag = $envelope->envelope_scan_flag;
                    $direct_shipping_flag = $envelope->direct_shipping_flag;
                    $collect_shipping_flag = $envelope->collect_shipping_flag;
                    $envelope_key_sign = APUtils::build_delete_sign($envelope_scan_flag, $item_scan_flag, $direct_shipping_flag, $collect_shipping_flag);
                    $envelope->activity_id = lang("envelope_" . $envelope_key_sign);
                    $envelope->activity_id2 = '';
                    $envelope->activity = !empty(lang('envelope.completed_activity_' . $envelope->activity_id)) ? lang('envelope.completed_activity_' . $envelope->activity_id) : "";
                    $envelope->activity_code = $envelope->envelope_code . '_' . sprintf('%1$02d', $envelope->activity_id);
                    $envelope->last_updated_date = "";
                    $envelope->completed_date = "";
                    $envelope->completed_by = "";
                    $envelope->category_type = Settings::get_label(APConstants::CATEGORY_TYPE_CODE, $envelope->category_type);

                    if ($envelope->activity_id2 == '5' || $envelope->trash_flag == '6'  || $envelope->trash_flag == '1' || $envelope->direct_shipping_flag == '1' || $envelope->collect_shipping_flag == '1') {

                        $envelope->status_envelope = "not_in_storage";

                    } else if ($envelope->trash_flag == '0' || $envelope->trash_flag == '5' || $envelope->direct_shipping_flag == '0' || ($envelope->collect_shipping_flag == '0'  && $envelope->package_id > '0')
                        || $envelope->envelope_scan_flag == '0' || $envelope->item_scan_flag == '0') {

                        $envelope->status_envelope = "requesting";

                    } else if ($envelope->direct_shipping_flag == '1' || $envelope->collect_shipping_flag == '1' 
                            || $envelope->completed_flag == "1" || $envelope->envelope_scan_flag == '1' || $envelope->item_scan_flag == '1'
                            || ($envelope->direct_shipping_flag == null && $envelope->collect_shipping_flag == null 
                                && $envelope->envelope_scan_flag == null && $envelope->item_scan_flag == null)
                            || ($envelope->collect_shipping_flag == '0'  && ( $envelope->package_id == '0' || $envelope->package_id == null)) ) {

                        $envelope->status_envelope = "sent";
                    }

                }
			}
		}
        
		if(!$envelope_completed){
			$envelope_completed = $envelope;
		}
        //GET envelope activity
		if ($envelope) {
			$activity = '';
			if ($envelope->trash_flag === '0' || $envelope->trash_flag === '1' 
					|| $envelope->trash_flag === APConstants::ENVELOPE_TRASH_BY_CUSTOMER_IN_TRASH_FOLDER 
				) {
				$delete_key_sign = APUtils::build_delete_sign($envelope->envelope_scan_flag, $envelope->item_scan_flag, $envelope->direct_shipping_flag, $envelope->collect_shipping_flag);
				$delete_activity = lang("activity_" . $delete_key_sign);

				if ($delete_activity == 'Trash') {
					$activity = lang('envelope.activity_5');
				} else if ($delete_activity == 'Trash After Scan') {
					$activity = lang('envelope.activity_6');
				}
			} else if ($envelope->envelope_scan_flag === '0') {
				if ($envelope->item_scan_flag === '0') {
					$activity = lang('envelope.activity_7');
				} else {
					$activity = lang('envelope.activity_1');
				}
			} else if ($envelope->item_scan_flag === '0') {
				$activity = lang('envelope.activity_2');
			} else if ($envelope->direct_shipping_flag === '0') {
				$activity = lang('envelope.activity_3');
			} else if ($envelope->collect_shipping_flag === '0' && !empty($envelope->package_id)) {
				$activity = lang('envelope.activity_4');
			} else if (empty($envelope->collect_shipping_flag) && empty($envelope->direct_shipping_flag)
				&& empty($envelope->envelope_scan_flag) && empty($envelope->item_scan_flag)
			) {
				$activity = "";
			}

			$envelope->activity_item = $activity;
			$envelope_completed->last_activity = !empty(lang('envelope.completed_activity_' . $envelope_completed->activity_id)) ? lang('envelope.completed_activity_' . $envelope_completed->activity_id) : "";

			// Get customer key
			$customer = ci()->customer_m->get_by_many(array(
				"customer_id" => $envelope->to_customer_id
			));

			// Gets envelopes information.
			$envelope_info = ci()->envelope_completed_m->get_item(array(
				'envelope_id' => $envelope->id,
				'completed_flag' => APConstants::ON_FLAG
			));
			#1058 add multi dimension capability for admin
            if(empty($envelope_info)){
                $weight =  APUtils::number_format(0, 0, $decimal_separator);
                $length =  APUtils::number_format(0, 0, $decimal_separator);
                $width  =  APUtils::number_format(0, 0, $decimal_separator);
                $height =  APUtils::number_format(0, 0, $decimal_separator);
            }else{
                $weight = intval($envelope_info->weight);// APUtils::view_convert_number_in_weight( $envelope_info->weight, $weight_unit, $decimal_separator, FALSE);
                $length = intval($envelope_info->length);//APUtils::view_convert_number_in_length( $envelope_info->length, $length_unit, $decimal_separator, FALSE);
                $width  = intval($envelope_info->width);//APUtils::view_convert_number_in_length( $envelope_info->width,  $length_unit, $decimal_separator, FALSE);
                $height = intval($envelope_info->height);//APUtils::view_convert_number_in_length( $envelope_info->height, $length_unit, $decimal_separator, FALSE);
            }
			
			$shipping_tracking = ci()->envelope_shipping_tracking_m->get_by('envelope_id', $envelope->id);
                        
            $check_envelope_customs = EnvelopeUtils::check_envelope_customs($envelope->id);
                        
            $response = array(
                "account_status" => (completed_api::getAccountStatus($customer)), // Get account information
                "verified_status" => (completed_api::getPostboxVerifiedStatus($envelope)), // Get postbox verification status
                "envelope" => $envelope,
                "envelope_completed" => $envelope_completed,
                "envelope_info" => $envelope_info,
                "weight" =>$weight,
                "width" =>$width,
                "height" =>$height,
                "length" =>$length,
                "token_key" => $customer->token_key,
                "tracking_number" => !empty($shipping_tracking) ? $shipping_tracking->tracking_number:'',
                "shipping_services_id" => !empty($shipping_tracking) ? $shipping_tracking->shipping_services_id:'',
                "envelope_customs" => $check_envelope_customs
			);

			$data_response['status'] = true;
			$data_response['message'] = $response;
			
		} else {
			$data_response['status'] = false;
			$data_response['message'] = "This item does not exist in the system.";
			
		}
		$data_response['envelope_completed'] = $envelope_completed;
		$data_response['envelope'] = $envelope;
		return $data_response;
        
    }
	
public static function getAccountStatus($customer)
    {
        ci()->load->library('account/account_api');

        $accountDuration = account_api::getAccountDuration($customer->created_date);
        $accountStatus = ($accountDuration > 1) ? "{$accountDuration} (months)" : "{$accountDuration} (month)";
        if (account_api::isActiveAccount($customer)) {
            $accountStatus .= ' - Active';
        } elseif (account_api::isInactiveAccount($customer)) {
            $accountStatus .= ' - Inactive';
            $inactiveDays = account_api::getAccountInactiveDays($customer->deactivated_date);
            if ($inactiveDays > 0) {
                if ($inactiveDays > 1) {
                    $accountStatus .= " ({$inactiveDays} days)";
                } else {
                    $accountStatus .= " ({$inactiveDays} day)";
                }
            }
        } else {
            $accountStatus .= ' - Deleted';
        }

        return $accountStatus;
    }

	public static function getPostboxVerifiedStatus($envelope)
    {
        ci()->load->library('mailbox/mailbox_api');

        $verifiedStatus = mailbox_api::getPostboxVerifiedStatus($envelope->postbox_id);
        if ($verifiedStatus == APConstants::VERIFIED_STATUS_INCOMPLETE) {
            $verifiedStatus = "<span style='color: red; font-weight: bold;'>{$verifiedStatus}</span>";
        }

        return $verifiedStatus;
    }

    /**
     * Get item activity in check item page
     * @param type $envelope_code
     * @param type $input_paging
     * @return type
     */
    public static  function search_complated_activities_check_item($envelope_code,$input_paging)
    {
       
        ci()->load->model(array(
        	'mailbox/envelope_customs_m',
        	'scans/envelope_completed_m'
        ));
        
        #1058 add multi dimension capability for admin
        $date_format       = APUtils::get_date_format_in_user_profiles();
        $decimal_separator = APUtils::get_decimal_separator_in_user_profiles();
        $weight_unit       = APUtils::get_weight_unit_in_user_profiles();
        $currency_short    = APUtils::get_currency_short_in_user_profiles();
        $currency_rate     = APUtils::get_currency_rate_in_user_profiles();
            
        $array_condition = array(
            "completed_flag" => APConstants::ON_FLAG,
            "activity_code LIKE '" . $envelope_code . "%'" => null
        );

        // Call search method to get list activity of this envelope
        $query_result = ci()->envelope_completed_m->get_envelope_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);

        // Process output data
        $total = $query_result ['total'];
        $datas = $query_result ['data'];

        // Get output response
        $response = parent::get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);

        $datas_mobile = array();
        $i = 0;
        
        foreach ($datas as $row) {
            $customs_flag = '';
            $customs = ci()->envelope_customs_m->get_by_many(array(
                "envelope_id" => $row->envelope_id,
                "customer_id" => $row->to_customer_id
            ));
            if ($customs) {
                $customs_flag = '1';
            }
            $response->rows [$i] ['id'] = $row->id;

            $cost = 0;
            $vat = 0;
            $cost_obj = completed_api::getEnvelopeCostById($row->envelope_id, $row);
            
            #1058 add multi dimension capability for admin
            $weight = APUtils::view_convert_number_in_weight( $row->weight, $weight_unit, $decimal_separator);

            $labelValue = Settings::get_label(APConstants::ENVELOPE_TYPE_CODE, $row->envelope_type_id);
            $last_updated_date = APUtils::viewDateFormat($row->last_updated_date, $date_format . APConstants::TIMEFORMAT_OUTPUT01);
            $completed_date = APUtils::viewDateFormat($row->completed_date, $date_format. APConstants::TIMEFORMAT_OUTPUT01);
            $activity = lang('envelope.completed_activity_' . $row->activity_id);
            $admin_name = completed_api::get_user_name_by_type($row->to_customer_id, $row->completed_by, $row->created_by_type);

            $response->rows [$i] ['cell'] = array(
                $row->id,
                $row->activity_code,
                $row->from_customer_name,
                $row->to_customer_id,
                $row->to_customer_name,
                $row->invoicing_address_name,
                $row->invoicing_company,
                $row->shipment_address_name,
                $row->shipment_company,
                $row->envelope_type_id,
                $labelValue,
                $weight,
                $last_updated_date,
                $completed_date,
                $row->id,
                $activity,
                $row->name,
                $row->postbox_company_name,
                $row->completed_by,
                $admin_name,
                APUtils::view_convert_number_in_currency($cost_obj ['cost'], $currency_short, $currency_rate, $decimal_separator),
       			APUtils::number_format($cost_obj ['vat'],0, $decimal_separator). '%',
                $customs_flag,
                $row->id,
                $row->envelope_id,
                $row->incomming_date
            );

            //Data for mobile
            $row->labelValue        = $labelValue;
            $row->weight            = $weight;
            $row->last_updated_date = $last_updated_date;
            $row->completed_date    = $completed_date;
            $row->activity          = $activity;
            $row->admin_name        = $admin_name;
            $row->cost              = $cost_obj ['cost'];
            $row->vat               = $cost_obj ['vat']. '%';
            $row->customs_flag      = $customs_flag;
            
            $datas_mobile[$i]       = $row;

            $i++;
        }
        
        return  array(
                    "mobile_activities_check_item" => $datas_mobile,
                    "web_activities_check_item"    => $response
                );
        
    }


    public static function getEnvelopeCostById($envelope_id, $row = '')
    {

        if(isset($row->activity_id)){

            $activity_type = 1;

            switch ($row->activity_id) {

                case '1':
                case '2':
                case '3':
                case '4':
                case '10':

                    $envelope_cost = 0;
                    // Envelope scan
                    if ($row->activity_id == 1) {
                        $activity_type = 2;
                    } // Item scan
                    else if ($row->activity_id == 2) {
                        $activity_type = 3;
                    } // Direct shipping
                    else if ($row->activity_id == 3) {
                        $activity_type = 4;
                    } // Collect shiping
                    else if ($row->activity_id == 4) {
                        $activity_type = 5;
                    }
                    $envelope_cost_row = ci()->invoice_detail_m->get_by_many(array(
                        "envelope_id" => $envelope_id,
                        "customer_id" => $row->to_customer_id,
                        "activity_type" => $activity_type
                    ));

                    $vat = 0;
                    if ($envelope_cost_row) {
                        $envelope_cost = $envelope_cost_row->item_amount;
                        $invoice_summary_id = $envelope_cost_row->invoice_summary_id;

                        $invoice_summary_row = ci()->invoice_summary_m->get_by_many(array(
                            "id" => $invoice_summary_id,
                            "customer_id" => $row->to_customer_id
                        ));

                        if ($invoice_summary_row) {
                            $vat = $invoice_summary_row->vat;
                            $vat = $vat * 100;
                        }
                    }
                    $cost = $envelope_cost;
                    return array(
                        'cost' => $cost,
                        'vat'  => $vat
                    );
                    break;
                
                default:

                    $envelope_cost_row = ci()->invoice_detail_m->get_by_many(array(
                        "envelope_id" => $envelope_id,
                        "customer_id" => $row->to_customer_id,
                        "activity_type" => $activity_type
                    ));

                    $vat = 0;
                    if ($envelope_cost_row) {
                        
                        $invoice_summary_id = $envelope_cost_row->invoice_summary_id;

                        $invoice_summary_row = ci()->invoice_summary_m->get_by_many(array(
                            "id" => $invoice_summary_id,
                            "customer_id" => $row->to_customer_id
                        ));

                        if ($invoice_summary_row) {
                            $vat = $invoice_summary_row->vat;
                            $vat = $vat * 100;
                        }
                    }

                    return array(
                        'cost' => APUtils::number_format(0, 2),
                        'vat'  => $vat
                    );
                    break;
            }
        }
        else{

            return array(
                'cost' => APUtils::number_format(0, 2),
                'vat'  => APUtils::number_format(0, 0)
            );
        }

        
    }

    /**
     * Delete customer
     */
    public static function delete($id)
    {
        
        $envelope_completed = ci()->envelope_completed_m->get($id);
        if (empty ($envelope_completed)) {
            return;
        }
        $activity_type = '';
        if ($envelope_completed->activity_id == 1) {
            $activity_type = 2;
        }         // Item scan
        else if ($envelope_completed->activity_id == 2) {
            $activity_type = 3;
        }         // Direct shipping
        else if ($envelope_completed->activity_id == 3) {
            $activity_type = 4;
        } // Collect shiping
        else if ($envelope_completed->activity_id == 4) {
            $activity_type = 5;
        }

        $target_month = APUtils::getCurrentMonthInvoice();
        $target_year = APUtils::getCurrentYearInvoice();
        $activity_date = date('Ym', $envelope_completed->completed_date);
        $activity_month = $activity_date;
        if ($activity_month == $target_year . $target_month) {
            // Delete the invoice activity and recalculate
            ci()->invoice_detail_m->delete_by_many(array(
                "envelope_id" => $envelope_completed->envelope_id,
                "customer_id" => $envelope_completed->to_customer_id,
                "activity_type" => $activity_type
            ));

            // Cal invoice class to recalculate
            ci()->invoices->calculate_invoice($envelope_completed->to_customer_id);
        }

        $success = ci()->envelope_completed_m->delete($id);

        // 20141105 Start hotfix: reset status of envelope
        if ($envelope_completed->envelope_scan_flag == APConstants::ON_FLAG) {
            ci()->envelope_m->update_by_many(array(
                "id" => $envelope_completed->envelope_id,
                "to_customer_id" => $envelope_completed->to_customer_id
            ), array(
                "completed_flag" => APConstants::OFF_FLAG,
                "envelope_scan_flag" => APConstants::OFF_FLAG,
                "last_updated_date" => time()
            ));
        }
        if ($envelope_completed->item_scan_flag == APConstants::ON_FLAG) {
            ci()->envelope_m->update_by_many(array(
                "id" => $envelope_completed->envelope_id,
                "to_customer_id" => $envelope_completed->to_customer_id
            ), array(
                "completed_flag" => APConstants::OFF_FLAG,
                "item_scan_flag" => APConstants::OFF_FLAG,
                "item_scan_date" => null,
                "last_updated_date" => time()
            ));
        }
        if ($envelope_completed->direct_shipping_flag == APConstants::ON_FLAG) {
            ci()->envelope_m->update_by_many(array(
                "id" => $envelope_completed->envelope_id,
                "to_customer_id" => $envelope_completed->to_customer_id
            ), array(
                "completed_flag" => APConstants::OFF_FLAG,
                "direct_shipping_flag" => APConstants::OFF_FLAG,
                "direct_shipping_date" => null,
                "last_updated_date" => time()
            ));
        }
        if ($envelope_completed->collect_shipping_flag == APConstants::ON_FLAG) {
            ci()->envelope_m->update_by_many(array(
                "id" => $envelope_completed->envelope_id,
                "to_customer_id" => $envelope_completed->to_customer_id
            ), array(
                "completed_flag" => APConstants::OFF_FLAG,
                "collect_shipping_flag" => APConstants::OFF_FLAG,
                "collect_shipping_date" => null,
                "last_updated_date" => time()
            ));
        }

        $response = array();
        if ($success) {

            $message = sprintf(lang('envelope.delete_success'));
            $response['status'] = true;
            $response['message'] = $message;

        } else {

            $message = sprintf(lang('envelope.delete_error'));
            $response['status'] = false;
            $response['message'] = $message;
        }
        return $response;
    }

    /*
    *  Des: Save info Item on check Item page
    */
    public static function save_item_info($data)
    {
        ci()->load->model('scans/envelope_m');
        #1058 add multi dimension capability for admin
        $decimal_separator = APUtils::get_decimal_separator_in_user_profiles();
        $length_unit = APUtils::get_length_unit_in_user_profiles();
        $weight_unit = APUtils::get_weight_unit_in_user_profiles();

        #1058 add multi dimension capability for admin
        $weight = APUtils::convert_number_in_weight($data['weight'], $weight_unit);
        $item_height = !empty($data['height']) ? $data['height'] : null;
        $item_width = !empty($data['width']) ? $data['width'] : null;
        $item_length = !empty($data['length']) ? $data['length'] : null;
        $width =  APUtils::convert_number_in_length($item_width,  $length_unit);
        $height = APUtils::convert_number_in_length($item_height, $length_unit);
        $length = APUtils::convert_number_in_length($item_length, $length_unit);
        
        $envelope = ci()->envelope_m->get($data['envelope_id']);
        
        if($envelope->direct_shipping_flag == 1 || $envelope->collect_shipping_flag == 1){
            $no_tracking_number = empty($data['tracking_number']) ? 1 : 0;
            //Insert tracking number
            todo_api::save_tracking_number($data['envelope_id'], $data['tracking_number'], $data['shipping_services'], $no_tracking_number);
        }
        $update_dimension = array(
            'envelope_id' => (int)$data['envelope_id'],
            'width' => $width,
            'height' => $height,
            'length' => $length,
        );

        $rs_ue = ci()->envelope_m->update_by_many(array(
            "id" => $data['envelope_id']
        ), array(
            'weight' => $data['weight'],
            'from_customer_name' => $data['from_customer_name']
        ));

        $rs_uec = ci()->envelope_completed_m->update_by_many(array(
            "envelope_id" => $data['envelope_id']
        ), array(
            'from_customer_name' => $data['from_customer_name'],
            'weight' => $data['weight']
        ));

        $rs_uep = ci()->envelope_properties_m->update_envelope_properties($update_dimension);
        
        return ($rs_ue && $rs_uec && $rs_uep);
        
    }

   
    /**
     * Cancel request in check item page.
     * @param type $envelope_id
     * @param type $type
     * @return type
     */
    public static function cancel_request($envelope_id, $type)
    {
        
        $response = array(
            'status'  => false,
            'message' => ''
        );

        if ($envelope_id && $type) {
            
            $completed_by = APContext::getAdminIdLoggedIn();

            if ($type == 1) {
                self::cancel_item_scan_request($envelope_id);
                // Insert completed activity
                scans_api::insertCompleteItem($envelope_id, APConstants::CANCEL_ITEM_SCAN_REQUEST_BY_ADMIN_ACTIVITY_TYPE, APConstants::TRIGGER_BY_ADMIN, $completed_by);
            } else if ($type == 2) {
                self::cancel_envelope_scan_request($envelope_id);
                // Insert completed activity
                scans_api::insertCompleteItem($envelope_id, APConstants::CANCEL_ENVELOPE_SCAN_REQUEST_BY_ADMIN_ACTIVITY_TYPE, APConstants::TRIGGER_BY_ADMIN, $completed_by);
            } else if ($type == 3) {
                self::cancel_direct_shipping_request($envelope_id);
                // Insert completed activity
                scans_api::insertCompleteItem($envelope_id, APConstants::CANCEL_DIRECT_FORWARDING_REQUEST_BY_ADMIN_ACTIVITY_TYPE, APConstants::TRIGGER_BY_ADMIN, $completed_by);
            } else if ($type == 4) {
                self::cancel_collect_shipping_request($envelope_id);
                // Insert completed activity
                scans_api::insertCompleteItem($envelope_id, APConstants::CANCEL_COLLECT_SHIPPING_REQUEST_BY_ADMIN_ACTIVITY_TYPE, APConstants::TRIGGER_BY_ADMIN, $completed_by);
            }
            
            $response['status'] = true;
            $response['message'] = '';
        } else {
            $response['status'] = false;
            $response['message'] = lang('cancel_request.error_message');
        }

        return $response;
    }


    public static function cancel_item_scan_request($envelop_id)
    {
        ci()->envelope_m->update_by_many(array(
            "id" => $envelop_id
        ), array(
            "item_scan_flag" => null,
            "item_scan_date" => null
        ));
    }

    public static function cancel_envelope_scan_request($envelop_id)
    {
        ci()->envelope_m->update_by_many(array(
            "id" => $envelop_id
        ), array(
            "envelope_scan_flag" => null
        ));
    }

    public static function cancel_direct_shipping_request($envelop_id)
    {
        ci()->load->model('scans/envelope_m');
        ci()->load->model('mailbox/envelope_customs_m');
        ci()->envelope_m->update_by_many(array(
            "id" => $envelop_id
        ), array(
            "direct_shipping_flag" => null,
            "direct_shipping_date" => null,
            "package_id" => null
        ));
        
        //ci()->envelope_customs_m->delete_by_many(array(
        //    "envelope_id" => $envelop_id
        //));
    }

    public static function cancel_collect_shipping_request($envelop_id)
    {
        ci()->load->model('scans/envelope_m');
        ci()->load->model('mailbox/envelope_customs_m');
        
        ci()->envelope_m->update_by_many(array(
            "id" => $envelop_id
        ), array(
            "collect_shipping_flag" => null,
            "collect_shipping_date" => null,
            "package_id" => 0
        ));
        
        //ci()->envelope_customs_m->delete_by_many(array(
        //    "envelope_id" => $envelop_id
        //));
    }

    /**
     * Disable prepayment in check item page
     * @param type $envelop_id
     * @param type $customer_id
     * @param type $api_mobile
     * @return boolean
     */
    public static function disable_prepayment($envelop_id, $customer_id, $api_mobile = 0){
            
        $envelope = ci()->envelope_m->get($envelop_id);
        
        $completed_by = APContext::getAdminIdLoggedIn();
        scans_api::insertCompleteItem($envelop_id, APConstants::DISABLE_PREPAYMENT_REQUEST_BY_ADMIN_ACTIVITY_TYPE, APConstants::TRIGGER_BY_ADMIN, $completed_by);
        
        if($envelope->direct_shipping_flag == "2"){
            // And insert activity:REQUEST_TRACKING_NUMBER = '29'
            // Save address forwarding
            mailbox_api::requestDirectShipping($envelop_id, $customer_id, $api_mobile);
            scans_api::insertCompleteItem($envelop_id, APConstants::REQUEST_TRACKING_NUMBER_ACTIVITY_TYPE);
        }

        if($envelope->collect_shipping_flag == "2"){

            mailbox_api::requestCollectShippingAfterPrepayment($envelop_id, $customer_id, $api_mobile);
        }

        if($envelope->item_scan_flag == "2"){
            // Request item scan is successful
            // Update item_scan_flag = 0 (yellow) 
            mailbox_api::requestItemScan($envelop_id, $customer_id, $api_mobile);
        }

        if($envelope->envelope_scan_flag == "2"){
            // Envelope scan request is successful
            // Update envelope_scan_flag = 0 (yellow)
            mailbox_api::requestEnvelopeScan($envelop_id, $customer_id, $api_mobile);
        }

        return true;
    
    }
    
    public static function delete_activity_by_envelope($envelope_id, $activity_id){
        ci()->envelope_completed_m->delete_by_many(array(
            'envelope_id' => $envelope_id,
            'activity_id' => $activity_id
        ));
    }
    
    /**
     * Get user name of admin or customer that already trigger activity
     * @param type $id
     * @param type $type
     */
    public static function get_user_name_by_type($customer_id, $admin_id, $type) {
        ci()->load->model('users/user_m');
        ci()->load->model('customers/customer_m');
        
        $user_name = 'SYSTEM';
        
        if ($type == APConstants::TRIGGER_BY_ADMIN || (is_null($type) && !empty($admin_id))) {

            $user = ci()->user_m->get(array('id' => $admin_id));
            $user_name = !empty($user->display_name) ? $user->display_name : $user->user_name ;

        } elseif ( $type == APConstants::TRIGGER_BY_CUSTOMER || (is_null($type) && empty($admin_id))) {
        
            $user = ci()->customer_m->get($customer_id);
            $user_name = !empty($user->user_name) ? $user->user_name : $user->email  ;
            
        }

        return $user_name;
        
    }

    
}