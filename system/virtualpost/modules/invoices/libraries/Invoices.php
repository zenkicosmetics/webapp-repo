<?php defined('BASEPATH') or exit('No direct script access allowed');
class Invoices {
    public function __construct() {
        ci()->load->model(array(
            'customers/customer_m',
            'mailbox/postbox_m',
            'mailbox/postbox_fee_month_m',
            'invoices/invoice_summary_m',
            'invoices/invoice_summary_by_location_m',
            'invoices/invoice_summary_by_user_m',
            'invoices/invoice_detail_m',
            'scans/envelope_summary_month_m',
            'scans/envelope_shipping_m',
            'mailbox/envelope_customs_m',
            'mailbox/envelope_customs_detail_m',
            'invoices/envelope_storage_fee_m',
            "customers/customer_setting_m",
            
        ));
        
        ci()->load->library(array(
            'invoices/invoices_api',
            'price/price_api',
            'invoices/InvoiceSummaryByLocation',
            "mailbox/mailbox_api",
            'scans/scans_api',
            
        ));        
    }
    /**
     * Tinh toan chi phi cho khach hang den thoi diem hien tai.
     */
    public function calculate_invoice($customer_id, $pricings='', $is_calculate_total_invoice = true)
    {
        // Get customer information
        $customer = ci()->customer_m->get_by_many(array(
            "customer_id" => $customer_id
        ));

        // Gets active flag
        $customer_product_setting = CustomerProductSetting::get_activate_flags($customer_id);
        // Check if customer already deleted
        if ($customer->status == APConstants::ON_FLAG 
                || ($customer_product_setting['postbox_name_flag'] == 0 &&  $customer_product_setting['invoicing_address_completed'] == 0 && $customer->account_type == APConstants::NORMAL_CUSTOMER)) {
            return;
        }

        // Call method to calculate postbox fee
        // $this->cal_postbox_invoices($customer, $pricings);
        $this->cal_postbox_invoices($customer);

        // Tong hop invoice tu invoice_detail sang invoice_summary
        // $target_month = APUtils::getCurrentMonthInvoice();
        // $target_year = APUtils::getCurrentYearInvoice();
        // $this->cal_invoice_summary($customer->customer_id, $target_year, $target_month);
        
        if($is_calculate_total_invoice){
            APUtils::updateTotalInvoiceOfInvoiceSummary($customer_id);
            APUtils::updateTotalInvoiceOfInvoiceSummaryByLocation($customer_id);
            
            if($customer->account_type == APConstants::ENTERPRISE_CUSTOMER){
                APUtils::updateTotalInvoiceUserEnterprise($customer_id);
            }
        }
        // Call method to calculate scan fee
        // $this->cal_store_invoices();
    }

    /**
     * Tinh toan chi phi de duy tri hom thu hang thang. Dua tren ngay base_line date de bat dau tinh toan (Dang tinh ngay base line la cuoi thang)
     * Thoi gian thinh phi la tu dau thang den cuoi thang (yyyyMM01 ~ yyyyMM31)
     */
    public function cal_postbox_invoices($customer)
    {
        $target_month = APUtils::getCurrentMonthInvoice();
        $target_year = APUtils::getCurrentYearInvoice();
        $customer_id = $customer->customer_id;
        $account_type = $customer->account_type;
        if (empty($account_type)) {
            $account_type = APConstants::FREE_TYPE;
        }

        $start_day_of_month = APUtils::getFirstDayOfCurrentMonth();
        $end_day_of_month = APUtils::getLastDayOfCurrentMonth();
        $number_of_day_month = APUtils::getDateDiff($start_day_of_month, $end_day_of_month);
        $currentDate = APUtils::getCurrentYearMonthDate();
        $defaultUnitCurrency = Settings::get(APConstants::CURRENTCY_CODE);

        // Gets vat of customer.
        if(!empty($customer->parent_customer_id) && $customer->account_type == APConstants::ENTERPRISE_TYPE){
            // Gets VAT of enteprrise customer.
            $customerVat = APUtils::getVatRateOfCustomer($customer->parent_customer_id);
        }else{
            $customerVat = APUtils::getVatRateOfCustomer($customer_id);
        }
        // start transaction for calculate postbox fee.
        ci()->postbox_m->db->trans_begin();

        // reset posbox fee data.
        if(true){
            ci()->invoice_summary_m->update_by_many(array(
                'invoice_month' => $target_year . $target_month,
                'customer_id' => $customer_id
            ), array(
                "free_postboxes_amount" => 0,
                "free_postboxes_quantity" => 0,
                "free_postboxes_netprice" => 0,

                "private_postboxes_amount" => 0,
                "private_postboxes_quantity" => 0,
                "private_postboxes_netprice" => 0,

                "business_postboxes_amount" => 0,
                "business_postboxes_quantity" => 0,
                "business_postboxes_netprice" => 0,

                "additional_free_postbox_amount" => 0,
                "additional_free_postbox_quantity" => 0,
                "additional_free_postbox_quantity" => 0,

                "additional_private_postbox_amount" => 0,
                "additional_private_postbox_quantity" => 0,
                "additional_private_postbox_netprice" => 0,

                "additional_business_postbox_amount" => 0,
                "additional_business_postbox_quantity" => 0,
                "additional_business_postbox_netprice" => 0
            ));

            // reset invoice by location.
            ci()->invoice_summary_by_location_m->update_by_many(array(
                "customer_id" => $customer_id,
                "invoice_month" => $target_year . $target_month
            ), array(
                "free_postboxes_amount" => 0,
                "free_postboxes_quantity" => 0,
                "free_postboxes_netprice" => 0,

                "private_postboxes_amount" => 0,
                "private_postboxes_quantity" => 0,
                "private_postboxes_netprice" => 0,

                "business_postboxes_amount" => 0,
                "business_postboxes_quantity" => 0,
                "business_postboxes_netprice" => 0
            ));

            ci()->invoice_summary_by_user_m->update_by_many(array(
                "customer_id" => $customer_id,
                "invoice_month" => $target_year . $target_month
            ), array(
                "free_postboxes_amount" => 0,
                "free_postboxes_quantity" => 0,
                "free_postboxes_netprice" => 0,

                "private_postboxes_amount" => 0,
                "private_postboxes_quantity" => 0,
                "private_postboxes_netprice" => 0,

                "business_postboxes_amount" => 0,
                "business_postboxes_quantity" => 0,
                "business_postboxes_netprice" => 0
            ));
        }
        
        // gets invoice_summary
        $invoice_summary = ci()->invoice_summary_m->get_by_many(array(
            "customer_id" => $customer_id,
            "invoice_type <> 2" => null,
            "invoice_month" => $target_year . $target_month
        ));
        $invoice_summary_id = 0;
        $invoice_code = '';
        if(!empty($invoice_summary)){
            $invoice_summary_id = $invoice_summary->id;
            $invoice_code = $invoice_summary->invoice_code;
        }else{
            // init invoice summary
            $invoice_code = APUtils::generateInvoiceCodeById($invoice_summary_id);
            $invoice_summary_id = ci()->invoice_summary_m->insert(array(
                "customer_id" => $customer_id,
                "invoice_type" => 1,
                "invoice_code" => $invoice_code,
                "vat" => $customerVat->rate,
                "vat_case" => $customerVat->vat_case,
                "invoice_month" => $target_year . $target_month,
                "created_date" => now()
            ));
        }
            
        // insert postbox activity into invoice detail.
        if(true){
            // Gets number day of month.
            $start_day_of_month = APUtils::getFirstDayOfCurrentMonth();
            $end_day_of_month = APUtils::getLastDayOfCurrentMonth();

            // tinh so ngay can tinh toan invoice. set la 1 neu chua tinh invoice tung ngay.
            $number_of_day_month = APUtils::getDateDiff($start_day_of_month, $end_day_of_month);
            
            $postboxes = ci()->postbox_m->get_all_postboxes_for_invoices($customer_id);
            foreach($postboxes as $postbox) {
                $postbox_type = $postbox->type;
                $location_id = $postbox->location_available_id;
                $postbox->pricing_template_id = empty($postbox->pricing_template_id) ? 0 : $postbox->pricing_template_id;

                // Get pricings by template 
                // $pricing_map = $pricings[$postbox->pricing_template_id];
                // Update #1438. Get the pricing map based on customer_id and location_id
                $pricing_map = price_api::getPricingModelByCusotomerAndLocationID($customer_id, $location_id);
                
                // postbox fee net prices
                $free_postbox_netprice = $pricing_map [APConstants::FREE_TYPE] ['postbox_fee_as_you_go'];
                $private_postbox_netprice = $pricing_map [APConstants::PRIVATE_TYPE] ['postbox_fee'];
                $business_postbox_netprice = $pricing_map [APConstants::BUSINESS_TYPE] ['postbox_fee'];
                $enterprise_postbox_netprice = $pricing_map [APConstants::ENTERPRISE_TYPE] ['postbox_fee'];
                
                // as you go duration
                $as_you_go_duration = $pricing_map [APConstants::FREE_TYPE] ['as_you_go'];
                
                // customer is not create postbox.
                if(empty($postbox->created_date)){
                    continue;
                }
                
                // postbox fee
                $postbox_fee = 0;
                
                // $where_condition
                $where_condition = array(
                    "customer_id" => $customer_id,
                    "product_id" => $postbox->postbox_id,
                    "start_invoice_date" => $start_day_of_month,
                    "end_invoice_date" => $end_day_of_month,
                    "postbox_type" => $postbox_type
                );
                // update data.
                $data_postbox = array(
                    "activity" => "Postbox fee",
                    "activity_type" => APConstants::INVOICE_ACTIVITY_TYPE_POSTBOX_FEE,
                    "activity_date" => $currentDate,
                    "item_number" => 1,
                    "unit_price" => $free_postbox_netprice,
                    "item_amount" => $postbox_fee,
                    "unit" => $defaultUnitCurrency,
                    "invoice_summary_id" => $invoice_summary_id,
                    "created_date" => now(),
                    "location_id" => $location_id,
                    "show_flag" => 0
                );
                
                // case 1. as you go postbox fee
                $postbox_created_date = date("Ymd",$postbox->created_date);
                if($postbox_type == APConstants::FREE_TYPE){
                    // get days from start day of month: we will calculate postbox fee from 183 days more.
                    $number_day_end_of_month = APUtils::getDateDiff($end_day_of_month, $postbox_created_date) - 1;
                    $number_day_must_be_calculated_fee = 0;
                    if($number_day_end_of_month > $as_you_go_duration ){
                        $number_day_must_be_calculated_fee = ($number_day_end_of_month - $as_you_go_duration <= $number_of_day_month) ? $number_day_end_of_month - $as_you_go_duration : $number_of_day_month;

                        $postbox_fee = $free_postbox_netprice * $number_day_must_be_calculated_fee / $number_of_day_month;
                    }
                    
                    // if this postbox has postbox fee.
                    if($postbox_fee > 0){
                        $data_postbox['item_amount'] = $postbox_fee;
                        $this->updateInvoiceDetailActivity($where_condition, $data_postbox);
                    }
                }
                // case 2: postbox upgrade to private or business in this month.
                else if(!empty($postbox->apply_date)) {
                    // TODO: enterprise customer has full postbox fee
                    $postbox_netprice = 0;
                    if ($postbox_type == APConstants::ENTERPRISE_TYPE){
                        $postbox_netprice = $private_postbox_netprice;
                        // TODO: enterprise customer has full postbox fee
                        //$postbox_fee = $enterprise_postbox_netprice * $number_day_must_be_calculated_fee / $number_of_day_month;
                        $postbox_fee = $enterprise_postbox_netprice;
                    } 
                    // truong hop standard customer
                    else {
                        // tinh toan phi cua as you go fee.
                        if(true){
                            $postbox_apply_date = $postbox->apply_date;
                            $number_days_duration = APUtils::getDateDiff($postbox_created_date, $postbox_apply_date) -1;
                            $actual_days_calculation_as_you_go_fee = APUtils::getDateDiff(APUtils::getFirstDayOfCurrentMonth(), $postbox_apply_date)-1;
                            $number_day_must_be_calculated_as_you_go_fee = 0;
                            if($number_days_duration >= $as_you_go_duration){
                                $number_day_must_be_calculated_as_you_go_fee = $number_days_duration - $as_you_go_duration;

                                if($number_day_must_be_calculated_as_you_go_fee >= $actual_days_calculation_as_you_go_fee){
                                    $number_day_must_be_calculated_as_you_go_fee = $actual_days_calculation_as_you_go_fee;
                                }
                                // donot calculate free postbox 
                                if($number_day_must_be_calculated_as_you_go_fee > 0){
                                    $postbox_fee = $free_postbox_netprice * $number_day_must_be_calculated_as_you_go_fee / $number_of_day_month;
                                }
                            }
                            // update postbox fee for as you go activity.
                            if($postbox_fee > 0){
                                $data_postbox['unit_price'] = $free_postbox_netprice;
                                $data_postbox['item_amount'] = $postbox_fee;
                                $where_condition['postbox_type'] = APConstants::FREE_TYPE;
                                $this->updateInvoiceDetailActivity($where_condition, $data_postbox);
                            }
                        }
                        
                        // tinh toan phan phi con lai trong thang duoc uprade len private/enterprise
                        $postbox_netprice = 0;
                        $where_condition['postbox_type'] = $postbox_type;
                        if(true){
                            $number_day_must_be_calculated_fee = APUtils::getDateDiff($postbox_apply_date, $end_day_of_month);
                            $number_day_must_be_calculated_fee = ($number_day_must_be_calculated_fee <= $number_of_day_month) ? $number_day_must_be_calculated_fee : $number_of_day_month;
                            if($postbox_type == APConstants::PRIVATE_TYPE){
                                $postbox_netprice = $private_postbox_netprice;
                                $postbox_fee = $private_postbox_netprice * $number_day_must_be_calculated_fee / $number_of_day_month;
                            } else if ($postbox_type == APConstants::BUSINESS_TYPE){
                                $postbox_netprice = $business_postbox_netprice;
                                $postbox_fee = $business_postbox_netprice * $number_day_must_be_calculated_fee / $number_of_day_month;
                            }
                        }

                        // update postbox activity
                        if($postbox_fee > 0){
                            $data_postbox['unit_price'] = $postbox_netprice;
                            $data_postbox['item_amount'] = $postbox_fee;
                            $this->updateInvoiceDetailActivity($where_condition, $data_postbox);
                        }
                    }
                } 
                // normal case: postbox does not has upgrade activity and belongs to (private, buisness, enterprise)
                else {
                    $number_day_must_be_calculated_fee = $number_of_day_month;
                    if ($postbox_created_date > $start_day_of_month) {
                        $number_day_must_be_calculated_fee = APUtils::getDateDiff($postbox_created_date, $end_day_of_month);
                    }
                    $number_day_must_be_calculated_fee = ($number_day_must_be_calculated_fee <= $number_of_day_month) ? $number_day_must_be_calculated_fee : $number_of_day_month;
                    
                    $postbox_netprice = 0;
                    if($postbox_type == APConstants::PRIVATE_TYPE){
                        $postbox_netprice = $private_postbox_netprice;
                        $postbox_fee = $private_postbox_netprice * $number_day_must_be_calculated_fee / $number_of_day_month;
                    } else if ($postbox_type == APConstants::BUSINESS_TYPE){
                        $postbox_netprice = $business_postbox_netprice;
                        $postbox_fee = $business_postbox_netprice * $number_day_must_be_calculated_fee / $number_of_day_month;
                    }else if ($postbox_type == APConstants::ENTERPRISE_TYPE){
                        $postbox_netprice = $enterprise_postbox_netprice;
                        // TODO: enterprise customer has full postbox fee
                        //$postbox_fee = $enterprise_postbox_netprice * $number_day_must_be_calculated_fee / $number_of_day_month;
                        $postbox_fee = $enterprise_postbox_netprice;
                    }
                    // update activity
                    if($postbox_fee > 0){
                        $data_postbox['unit_price'] = $postbox_netprice;
                        $data_postbox['item_amount'] = $postbox_fee;
                        $this->updateInvoiceDetailActivity($where_condition, $data_postbox);
                    }
                }
            }
        }
        
        // =============================================================================
        //              TINH TOAN TONG HOP PHI POSTBOX 
        // =============================================================================
        // Gets all invoice_detail activity
        if($account_type == APConstants::ENTERPRISE_TYPE){
            $invoice_activities = ci()->invoice_detail_m->get_many_by_many(array(
                "customer_id" => $customer_id,
                //"product_type" => "Postbox",
                "start_invoice_date" => $start_day_of_month,
                "end_invoice_date" => $end_day_of_month,
                "postbox_type" => APConstants::ENTERPRISE_TYPE,
                "activity_type" => APConstants::INVOICE_ACTIVITY_TYPE_POSTBOX_FEE
            ));
        }else{
            $invoice_activities = ci()->invoice_detail_m->get_many_by_many(array(
                "customer_id" => $customer_id,
                //"product_type" => "Postbox",
                "start_invoice_date" => $start_day_of_month,
                "end_invoice_date" => $end_day_of_month,
                "activity_type" => APConstants::INVOICE_ACTIVITY_TYPE_POSTBOX_FEE
            ));
        }
        //$this->cal_invoice_summary($customer_id, $target_year, $target_month, $customerVat);
                
        // tong hop postbox fee vao invoice summary
        $this->cal_invoice_summary_postbox_fee($customer,$customerVat, $target_year.$target_month, $invoice_activities, $invoice_summary_id, $invoice_code);

        // commit transaction
        if(ci()->postbox_m->db->trans_status() == FALSE){
            ci()->postbox_m->db->trans_rollback();
        }else{
            ci()->postbox_m->db->trans_commit();
        }

        // truong hop la cuoi thang thi xoa apply date di.
        invoices_api::resetApplyDateOfPostboxAtEndOfMonth($customer_id);
        // end of: truong hop cuoi thang thi.
    }
    
    /**
     * calculate postbox fee of invoice summary.
     * @param type $customer
     * @param type $customerVat
     * @param type $targetYM
     */
    private function cal_invoice_summary_postbox_fee($customer,$customerVat, $targetYM, $invoice_activities, $invoice_summary_id, $invoice_code){
        // calculate data by location
        $location_datas = array();
        foreach ($invoice_activities as $activity){
            $location_id = $activity->location_id;
            $tmp = empty($location_datas[$location_id]) ? new stdClass() : $location_datas[$location_id];
            $tmp->item_number = empty($tmp->item_number) ? 1 : $tmp->item_number + 1;
            $tmp->location_id = $location_id;
            $tmp->item_amount = empty($tmp->item_amount) ? $activity->item_amount :  $tmp->item_amount + $activity->item_amount;
            $tmp->postbox_type = $activity->postbox_type;
            $tmp->unit_price = $activity->unit_price;
            $tmp->customer_id = $activity->customer_id;
            
            $location_datas[$location_id] = $tmp;
            unset($tmp);
        }
        
        $free_postbox_amount = 0;
        $private_postbox_amount = 0;
        $business_postbox_amount = 0;
        
        $free_postboxes_quantity = 0;
        $private_postboxes_quantity = 0;
        $business_postboxes_quantity = 0;
        
        $free_postboxes_netprice = 0;
        $private_postboxes_netprice = 0;
        $business_postboxes_netprice = 0;
        
        $upcharge = 0;
        $parent_customer_id = $customer->parent_customer_id;
        $customer_setting = array();
        if($customer->account_type == APConstants::ENTERPRISE_TYPE){
            if(empty($parent_customer_id)){
                $parent_customer_id = $customer->customer_id;
            }
            // Gets customers setting
            $customer_setting = AccountSetting::get_list_setting_by($customer->parent_customer_id);
        }

        // update invoice by location + invoice by user
        foreach($location_datas as $postbox_location){
            // data by location
            $free_postbox_amount_by_location = 0;
            $private_postbox_amount_by_location = 0;
            $business_postbox_amount_by_location = 0;

            $free_postboxes_quantity_by_location = 0;
            $private_postboxes_quantity_by_location = 0;
            $business_postboxes_quantity_by_location = 0;
        
            if($postbox_location->postbox_type == APConstants::FREE_TYPE){
                $free_postbox_amount += $postbox_location->item_amount;
                $free_postboxes_quantity += $postbox_location->item_number;
                $free_postboxes_netprice = $postbox_location->unit_price;
                
                $free_postbox_amount_by_location = $postbox_location->item_amount;
                $free_postboxes_quantity_by_location = $postbox_location->item_number;
            } else if($postbox_location->postbox_type == APConstants::PRIVATE_TYPE){
                $private_postbox_amount += $postbox_location->item_amount;
                $private_postboxes_quantity += $postbox_location->item_number;
                $private_postboxes_netprice = $postbox_location->unit_price;
                
                $private_postbox_amount_location = $postbox_location->item_amount;
                $private_postboxes_quantity_location = $postbox_location->item_number;
            } else if($postbox_location->postbox_type == APConstants::BUSINESS_TYPE || $postbox_location->postbox_type == APConstants::ENTERPRISE_TYPE){
                $business_postbox_amount += $postbox_location->item_amount;
                $business_postboxes_quantity += $postbox_location->item_number;
                $business_postboxes_netprice = $postbox_location->unit_price;
                
                $business_postbox_amount_by_location = $postbox_location->item_amount;
                $business_postboxes_quantity_by_location = $postbox_location->item_number;
            }
            
            $invoice_check = ci()->invoice_summary_by_location_m->get_by_many(array(
                "location_id" => $postbox_location->location_id,
                "customer_id" =>$postbox_location->customer_id,
                "invoice_month" => $targetYM,
                "invoice_type" => 1
            ));
            if(empty($invoice_check)){
                ci()->invoice_summary_by_location_m->insert(array(
                    "location_id" => $postbox_location->location_id,
                    "customer_id" =>$postbox_location->customer_id,
                    "invoice_month" => $targetYM,
                    "invoice_type" => 1,
                    "vat" => $customerVat->rate,
                    "vat_case" => $customerVat->vat_case,
                    "created_date" => now(),
                    "invoice_summary_id" => $invoice_summary_id,
                    "invoice_code" => $invoice_code,
                    
                    "free_postboxes_amount" => $free_postbox_amount_by_location,
                    "private_postboxes_amount" => $private_postbox_amount_by_location,
                    "business_postboxes_amount" => $business_postbox_amount_by_location,

                    "free_postboxes_quantity" => $free_postboxes_quantity_by_location,
                    "private_postboxes_quantity" => $private_postboxes_quantity_by_location,
                    "business_postboxes_quantity" => $business_postboxes_quantity_by_location,

                    "free_postboxes_netprice" => $free_postboxes_netprice,
                    "private_postboxes_netprice" => $private_postboxes_netprice,
                    "business_postboxes_netprice" => $business_postboxes_netprice,
                ));
            }else{
                ci()->invoice_summary_by_location_m->update_by_many(array(
                    "location_id" => $postbox_location->location_id,
                    "customer_id" =>$postbox_location->customer_id,
                    "invoice_month" => $targetYM,
                    "invoice_type" => 1
                ), array(
                    "free_postboxes_amount" => $free_postbox_amount_by_location,
                    "private_postboxes_amount" => $private_postbox_amount_by_location,
                    "business_postboxes_amount" => $business_postbox_amount_by_location,

                    "free_postboxes_quantity" => $free_postboxes_quantity_by_location,
                    "private_postboxes_quantity" => $private_postboxes_quantity_by_location,
                    "business_postboxes_quantity" => $business_postboxes_quantity_by_location,

                    "free_postboxes_netprice" => $free_postboxes_netprice,
                    "private_postboxes_netprice" => $private_postboxes_netprice,
                    "business_postboxes_netprice" => $business_postboxes_netprice,
                    
                    "invoice_summary_id" => $invoice_summary_id,
                    "invoice_code" => $invoice_code,
                ));
            }
            
            // insert invoice by user if enteprise case.
            if($customer->account_type == APConstants::ENTERPRISE_TYPE){
                // get upcharge
                $upcharge = invoices_api::get_customer_setting_by_key($customer_setting, 'postbox_fee', $postbox_location->location_id);
        
                $invoice_user_check = ci()->invoice_summary_by_user_m->get_by_many(array(
                    "customer_id" => $customer->customer_id,
                    "invoice_month" => $targetYM,
                    "invoice_code" => $invoice_code,
                    "location_id" => $postbox_location->location_id
                ));
                if(empty($invoice_user_check)){
                    ci()->invoice_summary_by_user_m->insert(array(
                        "customer_id" => $customer->customer_id,
                        "invoice_month" => $targetYM,
                        "invoice_code" => $invoice_code,
                        "location_id" => $postbox_location->location_id,
                        
                        "business_postboxes_amount" => $business_postbox_amount_by_location + ($upcharge * $business_postboxes_quantity_by_location),
                        "business_postboxes_quantity" => $business_postboxes_quantity_by_location,
                        "business_postboxes_netprice" => $business_postboxes_netprice + $upcharge,

                        "invoice_type" => '1',
                        "vat" => $customerVat->rate,
                        "vat_case" => $customerVat->vat_case,
                        "postbox_fee_upcharge" => $upcharge
                    ));
                }else{
                    ci()->invoice_summary_by_user_m->update_by_many(array(
                        "customer_id" => $customer->customer_id,
                        "invoice_month" => $targetYM,
                        "invoice_code" => $invoice_code,
                        "location_id" => $postbox_location->location_id,
                    ), array(
                        "business_postboxes_amount" => $business_postbox_amount_by_location + ($upcharge * $business_postboxes_quantity_by_location),
                        "business_postboxes_quantity" => $business_postboxes_quantity_by_location,
                        "business_postboxes_netprice" => $business_postboxes_netprice + $upcharge,

                        "invoice_type" => '1',
                        "vat" => $customerVat->rate,
                        "vat_case" => $customerVat->vat_case,
                        "postbox_fee_upcharge" => $upcharge
                    ));
                }
            }//  end for enterprise by user
        }//end for loop location.
        
        // update invoice summary
        ci()->invoice_summary_m->update_by_many(array(
            "id" =>$invoice_summary_id,
            "customer_id" => $customer->customer_id,
            "invoice_month" => $targetYM,
            "invoice_type" => 1
        ), array(
            "free_postboxes_amount" => $free_postbox_amount,
            "private_postboxes_amount" => $private_postbox_amount,
            "business_postboxes_amount" => $business_postbox_amount,
            
            "free_postboxes_quantity" => $free_postboxes_quantity,
            "private_postboxes_quantity" => $private_postboxes_quantity,
            "business_postboxes_quantity" => $business_postboxes_quantity,
            
            "free_postboxes_netprice" => $free_postboxes_netprice,
            "private_postboxes_netprice" => $private_postboxes_netprice,
            "business_postboxes_netprice" => $business_postboxes_netprice,
        ));
        
        
    }
    
    /**
     * insert/update invoice detail activity.
     * @param type $where_condition
     * @param type $data
     */
    private function updateInvoiceDetailActivity($where_condition, $data){
        // insert into invoice_detail activity.
        $invoice_detail_check = ci()->invoice_detail_m->get_by_many($where_condition);
        
        if(empty($invoice_detail_check)){
            $update_data = array_merge($where_condition, $data);
            ci()->invoice_detail_m->insert($update_data);
        }else{
            ci()->invoice_detail_m->update_by_many($where_condition, $data);
        }
    }

    /**
     * Tinh toan chi phi add incomming items theo tung post_box_id. Chuc nang nay duoc goi khi add worker tao moi mot incomming.
     *
     * @param unknown_type $customer_id
     * @param unknown_type $postbox_id
     * @param unknown_type $envelope_id
     */
    public function cal_incomming_invoices($customer_id, $postbox_id, $envelope_id)
    {
        // Get target month
        $target_month = APUtils::getCurrentMonthInvoice();
        $target_year = APUtils::getCurrentYearInvoice();

        // Get postbox information
        $postbox = ci()->postbox_m->get($postbox_id);
        $postbox_type = $postbox->type;
        $location_id = $postbox->location_available_id;
        
        // Get don gia cua tat ca cac loai account type
        // $pricing_map = price_api::getPricingModelByPostboxID($postbox_id);
        $pricing_map = price_api::getPricingModelByCusotomerAndLocationID($customer_id, $location_id);
        
        $price_free = $pricing_map [APConstants::FREE_TYPE];

        $current_summary = ci()->envelope_summary_month_m->get_by_many(array(
            "envelope_id" => $envelope_id,
            "customer_id" => $customer_id,
            "postbox_id" => $postbox_id,
            "year" => $target_year,
            "month" => $target_month
        ));

        $incomming_price = 0;
        $additional_incomming_flag = '0';
        // flag xem envelope nay la loai additional hay mien phi
        $incomming_flag = 1;
        // Truong loai postbox la free thi se tinh don gia cho tat ca cac incomming items
        if ($postbox_type === APConstants::FREE_TYPE) {
            $incomming_price = $price_free ['additional_incomming_items'];
            $additional_incomming_flag = '1';
            $incomming_flag = 0;
        } // Truong hop la private hoac business postbox thi chi tinh don gia cho cac items vuot qua (included incomming items)
        else {
            // Tinh toan so item incomming hien tai trong thang
            $current_incomming_number = ci()->envelope_summary_month_m->sum_by_many(array(
                "customer_id" => $customer_id,
                "postbox_id" => $postbox_id,
                "year" => $target_year,
                "month" => $target_month,
                "incomming_number" => 1
            ), "incomming_number");
            $included_incomming_items = $pricing_map [$postbox_type] ['included_incomming_items'];
            if ($current_incomming_number + 1 > $included_incomming_items) {
                $incomming_price = $pricing_map [$postbox_type] ['additional_incomming_items'];
                $additional_incomming_flag = '1';
                $incomming_flag = 0;
            }
        }

        // Truong hop da ton tai thong tin thi update
        if (!empty($current_summary)) {
            ci()->envelope_summary_month_m->update_by_many(array(
                "envelope_id" => $envelope_id,
                "customer_id" => $customer_id,
                "postbox_id" => $postbox_id,
                "year" => $target_year,
                "month" => $target_month
            ), array(
                "incomming_number" => $incomming_flag,
                "incomming_price" => $incomming_price,
                "additional_incomming_flag" => $additional_incomming_flag
            ));
        } // Dang ky moi thong tin
        else {
            ci()->envelope_summary_month_m->insert(array(
                "envelope_id" => $envelope_id,
                "customer_id" => $customer_id,
                "postbox_id" => $postbox_id,
                "year" => $target_year,
                "month" => $target_month,
                "incomming_number" => $incomming_flag,
                "incomming_price" => $incomming_price,
                "additional_incomming_flag" => $additional_incomming_flag
            ));
        }

        // Gets vat of customer.
        $customerVat = APUtils::getVatRateOfCustomer($customer_id);
        $vat = $customerVat->rate;
        $vat_case_id = $customerVat->vat_case_id;

        // Gets invoice id.
        $currInvoiceMonth = APUtils::getCurrentYear() . APUtils::getCurrentMonth();
        $invoice = ci()->invoice_summary_m->get_by_many(array(
            "customer_id" => $customer_id,
            //"vat" => $vat,
            "invoice_month" => $currInvoiceMonth
        ));
        $invoice_summary_id = "";
        if ($invoice) {
            $invoice_summary_id = $invoice->id;
        } else {
            // insert new invoice with same invoice_code.
            $invoice_summary_id = ci()->invoice_summary_m->insert(array(
                "invoice_month" => $currInvoiceMonth,
                "vat" => $vat,
                "vat_case" => $vat_case_id,
                "customer_id" => $customer_id,
                "update_flag" => 0
            ));
            
            $invoice_code = APUtils::generateInvoiceCodeById($invoice_summary_id);
            ci()->invoice_summary_m->update_by_many(array(
                "id" => $invoice_summary_id
            ),array(
                'invoice_code' => $invoice_code
            ));
        }

        // Dang ky thong tin vao bang [invoice_detail]
        ci()->invoice_detail_m->insert(array(
            "customer_id" => $customer_id,
            "envelope_id" => $envelope_id,
            "activity" => "Incoming",
            "activity_date" => APUtils::getCurrentYearInvoice() . APUtils::getCurrentMonthInvoice() . APUtils::getCurrentDayInvoice(),
            "item_number" => 1,
            "unit_price" => $incomming_price,
            "item_amount" => 1 * $incomming_price,
            "unit" => Settings::get(APConstants::CURRENTCY_CODE),
            "activity_type" => APConstants::INCOMMING_ACTIVITY_TYPE,
            "postbox_type" => $postbox_type,
            "invoice_summary_id" => $invoice_summary_id
        ));

        // Summary for invoice
        $this->cal_invoice_summary($customer_id, $target_year, $target_month, $customerVat);
    }

    /**
     * Tinh toan chi phi shipping cho envelope. $shipping_type = 1: Direct forwarding, $shipping_type = 2: Collect forwarding
     *
     * @param unknown_type $customer_id
     * @param unknown_type $postbox_id
     * @param unknown_type $envelope_id
     */
    public function get_shipping_cost($customer_id, $postbox_id, $envelope_id, $shipping_type = '1', 
                $number_item = 1, $shipping_fee = 0, $insurance_customs_cost = 0)
    {
        // Get target month
        $target_month = APUtils::getCurrentMonthInvoice();
        $target_year = APUtils::getCurrentYearInvoice();

        // Get postbox information
        $postbox = ci()->postbox_m->get($postbox_id);
        if (empty($postbox)) {
            return;
        }
        $postbox_type = $postbox->type;
        $location_id = $postbox->location_available_id;
        
        // Get don gia cua tat ca cac loai account type
        // Update #1438
        // $pricing_map = price_api::getPricingModelByPostboxID($postbox_id);
        $pricing_map = price_api::getPricingModelByCusotomerAndLocationID($customer_id, $location_id);

        $current_summary = ci()->envelope_summary_month_m->get_by_many(array(
            "envelope_id" => $envelope_id,
            "customer_id" => $customer_id,
            "postbox_id" => $postbox_id,
            "year" => $target_year,
            "month" => $target_month
        ));

        if ($shipping_fee === 0) {
            // Get envelope shipping
            $envelope_shipping = ci()->envelope_shipping_m->get_by_many(array(
                "customer_id" => $customer_id,
                "envelope_id" => $envelope_id,
                "postbox_id" => $postbox_id
            ));
            if ($envelope_shipping) {
                $shipping_fee = $envelope_shipping->shipping_fee;
            }
        }

        $shipping_plus = $pricing_map [$postbox_type] ['shipping_plus'] / 100;
        // $shipping_plus = Settings::get(APConstants::SHIPPING_HANDDING_KEY);
        $shipping_plus_fee = $shipping_plus * $shipping_fee;

        // Get shipping fee default
        $shipping_number_price = 0;
        if ($shipping_type === '1') {
            $shipping_number_price = $pricing_map [$postbox_type] ['send_out_directly'];
        } else if ($shipping_type === '2') {
            $shipping_number_price = $pricing_map [$postbox_type] ['send_out_collected'];
        }

        // Calculate handling charge
        $handling_charge = $shipping_number_price + $shipping_plus_fee;
        
        // customs_handling
        // Apply declare customs fee
        $envelope_customs = ci()->envelope_customs_m->get_by_many(array(
            "envelope_id" => $envelope_id
        ));
        
        $customs_handling = 0;
        if($envelope_customs){
            $total_customs_cost = $insurance_customs_cost;
            if ($total_customs_cost > 1000) {
                $customs_handling = $pricing_map [$postbox_type]['custom_declaration_outgoing_01'];
            } else if ($total_customs_cost > 0 && $total_customs_cost <= 1000) {
                $customs_handling = $pricing_map [$postbox_type]['custom_declaration_outgoing_02'];
            }
        }
        
        $total_shipping_fee = $shipping_fee + $customs_handling + $handling_charge;
        // Add 19% VAT
        //$vat = APUtils::getVatFeeByCustomer($customer_id);
        //$shipping_number_price = $shipping_number_price * (1 + $vat);
        return array(
            'total_shipping_fee' => $total_shipping_fee,
            'shipping_fee' => $shipping_fee,
            'customs_handling' => $customs_handling,
            'handling_charge' => $handling_charge
        );
    }

    /**
     * Tinh toan chi phi shipping cho envelope. $shipping_type = 1: Direct shipping $shipping_type = 2: Collect shipping
     *
     * @param unknown_type $customer_id
     * @param unknown_type $postbox_id
     * @param unknown_type $envelope_id
     */
    public function cal_shipping_invoices($customer_id, $postbox_id, $envelope_id, $shipping_type = '1', $number_item = 1, $shipping_fee = 0)
    {
        // Get target month
        $target_month = APUtils::getCurrentMonthInvoice();
        $target_year = APUtils::getCurrentYearInvoice();

        // Get postbox information
        $postbox = ci()->postbox_m->get($postbox_id);
        if (empty($postbox)) {
            return;
        }
        $postbox_type = $postbox->type;
        
        // Get don gia cua tat ca cac loai account type
        $location_id = $postbox->location_available_id;
        // Update #1438        
        // $pricing_map = price_api::getPricingModelByPostboxID($postbox_id);
        $pricing_map = price_api::getPricingModelByCusotomerAndLocationID($customer_id, $location_id);

        $current_summary = ci()->envelope_summary_month_m->get_by_many(array(
            "envelope_id" => $envelope_id,
            "customer_id" => $customer_id,
            "postbox_id" => $postbox_id,
            "year" => $target_year,
            "month" => $target_month
        ));

        // Get envelope shipping
        $envelope_shipping = ci()->envelope_shipping_m->get_by_many(array(
            "customer_id" => $customer_id,
            "envelope_id" => $envelope_id,
            "postbox_id" => $postbox_id
        ));
        $customs_handling_fee = !empty($envelope_shipping) ? $envelope_shipping->customs_handling_fee : 0;
        if ($shipping_fee === 0) {
            if ($envelope_shipping) {
                $shipping_fee = $envelope_shipping->shipping_fee;
            }
        }
        // Get shipping fee default
        $shipping_number_price = !empty($envelope_shipping) ? ($envelope_shipping->forwarding_charges_fee + $envelope_shipping->forwarding_charges_postal) : 0;

        // Truong hop da ton tai thong tin thi update
        if (!empty($current_summary)) {
            if ($shipping_type === '1') {
                ci()->envelope_summary_month_m->update_by_many(array(
                    "envelope_id" => $envelope_id,
                    "customer_id" => $customer_id,
                    "postbox_id" => $postbox_id,
                    "year" => $target_year,
                    "month" => $target_month
                ), array(
                    "direct_shipping_number" => 1,
                    "direct_shipping_price" => $shipping_number_price
                ));
            } else if ($shipping_type === '2') {
                ci()->envelope_summary_month_m->update_by_many(array(
                    "envelope_id" => $envelope_id,
                    "customer_id" => $customer_id,
                    "postbox_id" => $postbox_id,
                    "year" => $target_year,
                    "month" => $target_month
                ), array(
                    "collect_shipping_number" => 1,
                    "collect_shipping_price" => $shipping_number_price
                ));
            }
        } // Dang ky moi thong tin
        else {
            if ($shipping_type === '1') {
                ci()->envelope_summary_month_m->insert(array(
                    "envelope_id" => $envelope_id,
                    "customer_id" => $customer_id,
                    "postbox_id" => $postbox_id,
                    "year" => $target_year,
                    "month" => $target_month,
                    "direct_shipping_number" => 1,
                    "direct_shipping_price" => $shipping_number_price
                ));
            } else if ($shipping_type === '2') {
                ci()->envelope_summary_month_m->insert(array(
                    "envelope_id" => $envelope_id,
                    "customer_id" => $customer_id,
                    "postbox_id" => $postbox_id,
                    "year" => $target_year,
                    "month" => $target_month,
                    "collect_shipping_number" => 1,
                    "collect_shipping_price" => $shipping_number_price
                ));
            }
        }
        $activity_type = 0;
        if ($shipping_type === '1') {
            $activity_type = APConstants::DIRECT_SHIPPING_ACTIVITY_TYPE;
        } else if ($shipping_type === '2') {
            $activity_type = APConstants::COLLECT_SHIPPING_ACTIVITY_TYPE;
        }

        // #472: get shipping vat
        // $customerVat = APUtils::getVatRateOfShipping($envelope_id);
        $customerVat = APUtils::getVatRateOfCustomer($customer_id);
        $vat = $customerVat->rate;
        $vat_case_id = $customerVat->vat_case_id;

        // Gets invoice id.
        $currInvoiceMonth = APUtils::getCurrentYear() . APUtils::getCurrentMonth();
        $invoice = ci()->invoice_summary_m->get_by_many(array(
            "customer_id" => $customer_id,
            //"vat" => $vat,
            "invoice_month" => $currInvoiceMonth
        ));
        $invoice_summary_id = "";
        if ($invoice) {
            $invoice_summary_id = $invoice->id;
        } else {
            // insert new invoice with same invoice_code.
            $invoice_summary_id = ci()->invoice_summary_m->insert(array(
                "invoice_month" => $currInvoiceMonth,
                "vat" => $vat,
                "vat_case" => $vat_case_id,
                "customer_id" => $customer_id
            ));
            
            $invoice_code = APUtils::generateInvoiceCodeById($invoice_summary_id);
            ci()->invoice_summary_m->update_by_many(array(
                "id" => $invoice_summary_id
            ),array(
                'invoice_code' => $invoice_code
            ));
        }

        // Dang ky thong tin vao bang [invoice_detail]
        ci()->invoice_detail_m->insert(array(
            "customer_id" => $customer_id,
            "activity" => APConstants::SHIPPING_HANDING_INVOICE_DETAIL_AT,
            "activity_date" => APUtils::getCurrentYearInvoice() . APUtils::getCurrentMonthInvoice() . APUtils::getCurrentDayInvoice(),
            "item_number" => 1,
            "unit_price" => $shipping_number_price,
            "item_amount" => 1 * $shipping_number_price,
            "unit" => Settings::get(APConstants::CURRENTCY_CODE),
            "envelope_id" => $envelope_id,
            "activity_type" => $activity_type,
            "postbox_type" => $postbox_type,
            "invoice_summary_id" => $invoice_summary_id
        ));

        // Apply declare customs fee
        $envelope_customs = ci()->envelope_customs_m->get_by_many(array(
            "envelope_id" => $envelope_id
        ));
        
        // Only calculate customs handing fee for direct shipping
        // For collect shipping case will calculate separately
        if ((!empty($envelope_customs) || $customs_handling_fee > 0) && $shipping_type == '1') {
            $shipping_customs_cost_fee = $customs_handling_fee / $number_item;
            $insurance_customs_cost = !empty($envelope_shipping) ? $envelope_shipping->insurance_customs_cost : 0;
            if (empty($insurance_customs_cost)) {
                $insurance_customs_cost = mailbox_api::get_total_customs_cost($customer_id, $envelope_id);
            }
            if ($insurance_customs_cost > 1000) {
                if ($shipping_customs_cost_fee == 0) {
                    $shipping_customs_cost_fee = $pricing_map [$postbox_type] ['custom_declaration_outgoing_01'] / $number_item;
                }
                // Dang ky thong tin vao bang [invoice_detail]
                ci()->invoice_detail_m->insert(array(
                    "customer_id" => $customer_id,
                    "activity" => APConstants::CUSTOMS_DECLARATION_01_INVOICE_DETAIL_AT,
                    "activity_date" => APUtils::getCurrentYearInvoice() . APUtils::getCurrentMonthInvoice() . APUtils::getCurrentDayInvoice(),
                    "item_number" => 1,
                    "unit_price" => $shipping_customs_cost_fee,
                    "item_amount" => 1 * $shipping_customs_cost_fee,
                    "unit" => Settings::get(APConstants::CURRENTCY_CODE),
                    "envelope_id" => $envelope_id,
                    "activity_type" => APConstants::CUSTOMS_DECLARATION_01_ACTIVITY_TYPE,
                    "postbox_type" => $postbox_type,
                    "invoice_summary_id" => $invoice_summary_id
                ));
            } else if ($insurance_customs_cost >= 0 && $insurance_customs_cost <= 1000) {
                if ($shipping_customs_cost_fee == 0) {
                    $shipping_customs_cost_fee = $pricing_map [$postbox_type] ['custom_declaration_outgoing_02'] / $number_item;
                }
                // Dang ky thong tin vao bang [invoice_detail]
                ci()->invoice_detail_m->insert(array(
                    "customer_id" => $customer_id,
                    "activity" => APConstants::CUSTOMS_DECLARATION_02_INVOICE_DETAIL_AT,
                    "activity_date" => APUtils::getCurrentYearInvoice() . APUtils::getCurrentMonthInvoice() . APUtils::getCurrentDayInvoice(),
                    "item_number" => 1,
                    "unit_price" => $shipping_customs_cost_fee,
                    "item_amount" => 1 * $shipping_customs_cost_fee,
                    "unit" => Settings::get(APConstants::CURRENTCY_CODE),
                    "envelope_id" => $envelope_id,
                    "activity_type" => APConstants::CUSTOMS_DECLARATION_02_ACTIVITY_TYPE,
                    "postbox_type" => $postbox_type,
                    "invoice_summary_id" => $invoice_summary_id
                ));
            }
        }

        // Summary for invoice
        $this->cal_invoice_summary($customer_id, $target_year, $target_month, $customerVat);
    }
    
   /**
    * Calculate customs handing fee for collect shipping
    * @param type $customer_id
    * @param type $postbox_id
    * @param type $envelope_id (Only pickup one item from package to calculate)
    */
    public function cal_customs_handing_fee($customer_id, $postbox_id, $envelope_id) {
        
        // Get envelope shipping
        $envelope_shipping = ci()->envelope_shipping_m->get_by_many(array(
            "customer_id" => $customer_id,
            "envelope_id" => $envelope_id,
            "postbox_id" => $postbox_id
        ));
        $customs_handling_fee = !empty($envelope_shipping) ? $envelope_shipping->customs_handling_fee : 0;
        if ($customs_handling_fee == 0) {
            return;
        }
        
        $shipping_customs_cost_fee = $customs_handling_fee;
        $insurance_customs_cost = !empty($envelope_shipping) ? $envelope_shipping->insurance_customs_cost : 0;
        if (empty($insurance_customs_cost)) {
            $insurance_customs_cost = mailbox_api::get_total_customs_cost($customer_id, $envelope_id);
        }
        $customerVat = APUtils::getVatRateOfCustomer($customer_id);
        $vat = $customerVat->rate;
        // Gets invoice id.
        $currInvoiceMonth = APUtils::getCurrentYear() . APUtils::getCurrentMonth();
        $invoice = ci()->invoice_summary_m->get_by_many(array(
            "customer_id" => $customer_id,
            "vat" => $vat,
            "invoice_month" => $currInvoiceMonth
        ));
        if (empty($invoice)) {
            return;
        }
        $invoice_summary_id = $invoice->id;
        
        $postbox = ci()->postbox_m->get($postbox_id);
        if (empty($postbox)) {
            return;
        }
        $postbox_type = $postbox->type;
        if ($insurance_customs_cost > 1000) {
            // Dang ky thong tin vao bang [invoice_detail]
            ci()->invoice_detail_m->insert(array(
                "customer_id" => $customer_id,
                "activity" => APConstants::CUSTOMS_DECLARATION_01_INVOICE_DETAIL_AT,
                "activity_date" => APUtils::getCurrentYearInvoice() . APUtils::getCurrentMonthInvoice() . APUtils::getCurrentDayInvoice(),
                "item_number" => 1,
                "unit_price" => $shipping_customs_cost_fee,
                "item_amount" => 1 * $shipping_customs_cost_fee,
                "unit" => Settings::get(APConstants::CURRENTCY_CODE),
                "envelope_id" => $envelope_id,
                "activity_type" => APConstants::CUSTOMS_DECLARATION_01_ACTIVITY_TYPE,
                "postbox_type" => $postbox_type,
                "invoice_summary_id" => $invoice_summary_id
            ));
        } else if ($insurance_customs_cost >= 0 && $insurance_customs_cost <= 1000) {
            // Dang ky thong tin vao bang [invoice_detail]
            ci()->invoice_detail_m->insert(array(
                "customer_id" => $customer_id,
                "activity" => APConstants::CUSTOMS_DECLARATION_02_INVOICE_DETAIL_AT,
                "activity_date" => APUtils::getCurrentYearInvoice() . APUtils::getCurrentMonthInvoice() . APUtils::getCurrentDayInvoice(),
                "item_number" => 1,
                "unit_price" => $shipping_customs_cost_fee,
                "item_amount" => 1 * $shipping_customs_cost_fee,
                "unit" => Settings::get(APConstants::CURRENTCY_CODE),
                "envelope_id" => $envelope_id,
                "activity_type" => APConstants::CUSTOMS_DECLARATION_02_ACTIVITY_TYPE,
                "postbox_type" => $postbox_type,
                "invoice_summary_id" => $invoice_summary_id
            ));
        }
    }

    /**
     * Tinh toan chi phi store envelope cua customer.
     *
     * @param unknown_type $customer
     * @param unknown_type $pricing_map
     */
    public function cal_store_invoices($input_customer_id = '')
    {
        $is_debug = false;
        $current_date = now();

        $target_month = APUtils::getCurrentMonthInvoice();
        $target_year = APUtils::getCurrentYearInvoice();
        
        $startDayOfMonth = APUtils::getFirstDayOfCurrentMonth();
        $endDayOfMonth = APUtils::getLastDayOfCurrentMonth();
        
        $defaultUnitCurrency = Settings::get(APConstants::CURRENTCY_CODE);
        
        // Reset all storage flag
        ci()->envelope_m->update_all_envelope_not_has_fee_storage();
        
        // Get all postbox of customer
        $all_envelopes = ci()->envelope_m->get_all_customer_has_fee_storage();
        // Get baseline date
        $baseline_storage_fee_date_setting = Settings::get(APConstants::STORAGE_FEE_BASELINE_DATE);
        $baseline_storage_fee_date = 0;
        if (!empty($baseline_storage_fee_date_setting)) {
            $baseline_storage_fee_date = APUtils::convert_date_to_timestamp($baseline_storage_fee_date_setting);
        }
        if ($baseline_storage_fee_date == 0 || $baseline_storage_fee_date > $current_date) {
            return;
        }

        // Get all setting type
        $all_envelope_type = Settings::get_list(APConstants::ENVELOPE_TYPE_CODE);
        $all_envelope_type_letter = array();
        $all_envelope_type_package = array();
        // Get all letter type & package type
        foreach ($all_envelope_type as $envelope_type) {
            if ($envelope_type->Alias02 == 'Letter') {
                $all_envelope_type_letter [] = $envelope_type->ActualValue;
            }
            if ($envelope_type->Alias02 == 'Package') {
                $all_envelope_type_package [] = $envelope_type->ActualValue;
            }
        }

        // tracking total envelopes.
        $total_envelopes_calculation = 0;

        // For each postbox
        foreach ($all_envelopes as $envelope) {
            $customer_id = $envelope->customer_id;
            $postbox_id = $envelope->postbox_id;
            $postbox_type = $envelope->type;
            $envelope_id = $envelope->id;
            $envelope_type_id = $envelope->envelope_type_id;
            $location_id = $envelope->location_available_id;
            $current_storage_charge_fee_day = $envelope->current_storage_charge_fee_day;
            $previous_storage_charge_fee_day = $envelope->previous_storage_charge_fee_day;
            
            // Write log
            log_audit_message(APConstants::LOG_INFOR, 'Calculate Storage Cost of Customer ID:'.$customer_id.', EnvelopeID:'.$envelope_id, false, 'auditlog-cal_store_invoices');

            // DEBUG
            if (!empty($input_customer_id)) {
                if ($customer_id != $input_customer_id) {
                    continue;
                }
            }
            // END DEBUG
            if (empty($postbox_type)) {
                $postbox_type = APConstants::FREE_TYPE;
            }

            // Check if this is first month, will reset current_storage_charge_fee_day = 0 and previous_storage_charge_fee_day = previous_storage_charge_fee_day + current_storage_charge_fee_day
            if (APUtils::isFirstDayOfMonth() && !$is_debug) {
                ci()->envelope_m->update_by_many(array(
                    "to_customer_id" => $customer_id,
                    "id" => $envelope_id,
                    "postbox_id" => $postbox_id
                ), array(
                    "storage_flag" => APConstants::OFF_FLAG,
                    "storage_date" => null,
                    "current_storage_charge_fee_day" => 0,
                    "previous_storage_charge_fee_day" => $current_storage_charge_fee_day + $previous_storage_charge_fee_day
                ));
                $previous_storage_charge_fee_day = $current_storage_charge_fee_day + $previous_storage_charge_fee_day;
                $current_storage_charge_fee_day = 0;
                
                // only update storage number on first day of month.
                if($envelope->trash_flag == '' && $envelope->direct_shipping_flag <> 1 && $envelope->collect_shipping_flag <> 1 ){
                    // update in storage status
                    scans_api::updateStorageStatus($envelope_id, $customer_id, $postbox_id, $target_year, $target_month, $location_id, APConstants::ON_FLAG);
                }
            }

            // Does not calculate welcome envelope
            $envelope_code = $envelope->envelope_code;
            if (APUtils::endsWith($envelope_code, '_000')) {
                log_message(APConstants::LOG_DEBUG, 'Ignore this welcome Envelope ID:' . $envelope_id);
                continue;
            }
            
            // Get price model
            // Update #1438
            // $price_postbox = price_api::getPricingModelByLocationID($location_id, $postbox_type);
            $pricing_map = price_api::getPricingModelByCusotomerAndLocationID($customer_id, $location_id);
            $price_postbox = $pricing_map[$postbox_type];
            
            // Gets net price of envelope by type.
            $price_per_day_per_letter = $price_postbox['storing_items_over_free_letter'];
            $price_per_day_per_package = $price_postbox['storing_items_over_free_packages'];
            $netprice = 0;
            $item_amount = 0;

            $charge_date = $current_date;
            // Days unit
            $free_storage_duration_letters = $price_postbox ['storing_items_letters'];
            // Days unit
            $free_storage_duration_packages = $price_postbox ['storing_items_packages'];

            // Get incomming date
            $incomming_date = $envelope->incomming_date;
            $send_out_on = 0;
            if ($envelope->direct_shipping_date > 0 && $envelope->direct_shipping_flag == APConstants::ON_FLAG) {
                $send_out_on = $envelope->direct_shipping_date;
            } else if ($envelope->collect_shipping_date > 0 && $envelope->collect_shipping_flag == APConstants::ON_FLAG) {
                $send_out_on = $envelope->collect_shipping_date;
            }

            // Trash on
            $trashed_on = 0;
            if ($envelope->trash_date > 0
                && ($envelope->trash_flag == APConstants::ENVELOPE_COMPLETED_TRASH_BY_ADMIN 
                    || $envelope->trash_flag == APConstants::ENVELOPE_TRASH_BY_CUSTOMER_IN_TRASH_FOLDER
                    || $envelope->trash_flag == APConstants::ON_FLAG
                    || $envelope->trash_flag == APConstants::OFF_FLAG)
            ) {
                $trashed_on = $envelope->trash_date;
            }

            // Get end of previsous month (last day to calculate charge)
            $base_line_days = 0;
            // Get storate baseline in database
            $storage_envelope_fee_check = ci()->envelope_storage_fee_m->get_by_many(array(
                "customer_id" => $customer_id,
                "envelope_id" => $envelope_id,
                "postbox_id" => $postbox_id
            ));
            if (!empty($storage_envelope_fee_check)) {
                $base_line_days = $storage_envelope_fee_check->baseline_number_day;
            }
            // One of below value will be 0
            $storing_letter_number_over_day = 0;
            $storing_package_number_over_day = 0;
            $activity_type = "";

            // Check if this envelope is Letter
            $envelope_type_label = '';
            if (in_array($envelope_type_id, $all_envelope_type_letter)) {
                $envelope_type_label = 'Letter';
                $storing_letter_number_over_day = APUtils::calculateStorageDayToChargePerEnvelope($free_storage_duration_letters, 0, $charge_date, $incomming_date, $send_out_on, $trashed_on);
                
                // calculate storage price of envelope
                $netprice = $price_per_day_per_letter;
                $activity_type = APConstants::INVOICE_ACTIVITY_TYPE_STORAGE_FEE_LETTER;
                $item_amount = $current_storage_charge_fee_day * $price_per_day_per_letter;
            } else if (in_array($envelope_type_id, $all_envelope_type_package)) {
                $envelope_type_label = 'Package';
                $storing_package_number_over_day = APUtils::calculateStorageDayToChargePerEnvelope($free_storage_duration_packages, 0, $charge_date, $incomming_date, $send_out_on, $trashed_on);
                
                // calculate storage price of envelope
                $netprice = $price_per_day_per_package;
                $activity_type = APConstants::INVOICE_ACTIVITY_TYPE_STORAGE_FEE_PACKAGE;
                $item_amount = $current_storage_charge_fee_day * $price_per_day_per_package;
            }

            // DEBUG
            if ($is_debug && ($storing_letter_number_over_day + $storing_package_number_over_day > 0)) {
                echo '| Incomming:' . APUtils::convert_timestamp_to_date($incomming_date, 'd.m.Y');
                echo '| Send out on:' . APUtils::convert_timestamp_to_date($send_out_on, 'd.m.Y');
                echo '| Trash on:' . APUtils::convert_timestamp_to_date($trashed_on, 'd.m.Y');
                echo "| EnvelopeCode: " . $envelope->envelope_code . '==>Fee days:' . ($storing_letter_number_over_day + $storing_package_number_over_day) . '<br/>';
                ob_flush();
                flush();
                continue;
            }
            // END DEBUG

            $dayNum = intval(date("d", now()));
            $new_current_storage_charge_fee_day = min($dayNum, $storing_letter_number_over_day + $storing_package_number_over_day);

            $first_day_month = APUtils::convert_date_to_timestamp(APUtils::getFirstDayOfCurrentMonth());
            $completed_date = $envelope->completed_date;
            // Update storage flag and storage date
            if ($storing_letter_number_over_day + $storing_package_number_over_day > 0
                && ($send_out_on == 0 || $completed_date >= $first_day_month)
                && ($trashed_on == 0 || $completed_date >= $first_day_month)
            ) {
                ci()->envelope_m->update_by_many(array(
                    "to_customer_id" => $customer_id,
                    "id" => $envelope_id,
                    "postbox_id" => $postbox_id
                ), array(
                    "storage_flag" => APConstants::ON_FLAG,
                    "storage_date" => $current_date,
                    "current_storage_charge_fee_day" => $new_current_storage_charge_fee_day
                ));
                
                log_audit_message(APConstants::LOG_INFOR, "EnvelopeCode: " . $envelope->envelope_code . '==>Fee days:' . ($storing_letter_number_over_day + $storing_package_number_over_day), false, 'auditlog-cal_store_invoices');
                echo "EnvelopeCode: " . $envelope->envelope_code . '==>Fee days:' . ($storing_letter_number_over_day + $storing_package_number_over_day) . '<br/>';
                ob_flush();
                flush();
            }

            if (empty($storage_envelope_fee_check)) {
                ci()->envelope_storage_fee_m->insert(array(
                    "customer_id" => $customer_id,
                    "envelope_id" => $envelope_id,
                    "postbox_id" => $postbox_id,
                    "baseline_date" => $charge_date,
                    "baseline_number_day" => $storing_letter_number_over_day + $storing_package_number_over_day
                ));
            } else {
                ci()->envelope_storage_fee_m->update_by_many(array(
                    "customer_id" => $customer_id,
                    "envelope_id" => $envelope_id,
                    "postbox_id" => $postbox_id
                ), array(
                    "baseline_date" => $charge_date,
                    "baseline_number_day" => $storing_letter_number_over_day + $storing_package_number_over_day
                ));
            }

            // -----------------------------------------------------------------------------
            //          Insert into invoice detail storage fee by envelope
            // -----------------------------------------------------------------------------
            $invoice_summary_id = 0;
            $invoice_summary = ci()->invoice_summary_m->get_by_many(array(
                "customer_id" => $customer_id,
                "invoice_type <> 2" => null,
                "invoice_month" => $target_year . $target_month
            ));
            if(!empty($invoice_summary)){
                $invoice_summary_id = $invoice_summary->id;
            }else{
                // init invoice summary
                $invoice_code = APUtils::generateInvoiceCodeById($invoice_summary_id);
                $invoice_summary_id = ci()->invoice_summary_m->insert(array(
                    "customer_id" => $customer_id,
                    "invoice_type" => 1,
                    "invoice_code" => $invoice_code,
                    "invoice_month" => $target_year . $target_month,
                    "created_date" => now()
                ));
            }
            $where_condition = array(
                "customer_id" => $customer_id,
                "envelope_id" => $envelope_id,
                "product_id" =>$envelope_id,
                "start_invoice_date" => $startDayOfMonth,
                "end_invoice_date" => $endDayOfMonth,
                "postbox_type" => $postbox_type
            );
            
            $data_storage = array(
                "activity" => "Storage fee",
                "activity_type" => $activity_type,
                "activity_date" => APUtils::getCurrentYearMonthDate(),
                "item_number" => $new_current_storage_charge_fee_day,
                "unit_price" => $netprice,
                "item_amount" => $item_amount,
                "unit" => $defaultUnitCurrency,
                "invoice_summary_id" => $invoice_summary_id,
                "created_date" => now(),
                "location_id" => $location_id,
                "show_flag" => 0
            );
            
            if($item_amount > 0){
                $this->updateInvoiceDetailActivity($where_condition, $data_storage);
            }

            // increment total envelope canculation.
            $total_envelopes_calculation++;
        }

        return $total_envelopes_calculation;
    }
    
    /**
     * Summary all storage fee from envelopes
     */
    public function cal_storage_summary($input_customer_id = '') {
        $startDayOfMonth = APUtils::getFirstDayOfCurrentMonth();
        $endDayOfMonth = APUtils::getLastDayOfCurrentMonth();
        
        // reset storage cost.
        if(empty($input_customer_id)){
            // reset storage cost of customer
            ci()->envelope_m->update_all_customer_not_has_fee_storage();
        
            // reset storage cost of customer by location.
            ci()->invoice_summary_by_location_m->reset_storage_cost(APUtils::getCurrentYearMonth());
        }

        // Gets customers
        $customers = ci()->envelope_m->get_all_customer_id_has_storage_cost($input_customer_id);

        foreach($customers as $customer_list){
            $customer_id = $customer_list->customer_id;
            $customer = APContext::getCustomerByID($customer_id);
            
            // Gets all storage activity of customer.
            $invoice_details = ci()->invoice_detail_m->get_many_by_many(array(
                "customer_id" => $customer->customer_id,
                "start_invoice_date" => $startDayOfMonth,
                "end_invoice_date" => $endDayOfMonth,
                // storage activity
                "activity_type IN (11, 12)" => null
            ));
            
            if(empty($invoice_details)){
                continue;
            }
            
            // Gets vat of enterprise user.
            if(!empty($customer->parent_customer_id) && $customer->account_type == APConstants::ENTERPRISE_TYPE){
                // Gets VAT of enteprrise customer.
                $customerVat = APUtils::getVatRateOfCustomer($customer->parent_customer_id);
            }else{
                // get vat of clevver account.
                $customerVat = APUtils::getVatRateOfCustomer($customer_id);
            }
            $vat = $customerVat->rate;
            $vat_case_id = $customerVat->vat_case_id;

            // init data
            if(true){
                $storing_letters_free_account = 0;
                $storing_packages_free_account = 0;
                $storing_letters_free_quantity = 0;
                $storing_letters_free_netprice = 0;
                $storing_packages_free_quantity = 0;
                $storing_packages_free_netprice = 0;

                $storing_letters_private_account = 0;
                $storing_packages_private_account = 0;
                $storing_letters_private_quantity = 0;
                $storing_letters_private_netprice = 0;
                $storing_packages_private_quantity = 0;
                $storing_packages_private_netprice = 0;

                $storing_letters_business_account = 0;
                $storing_packages_business_account = 0;
                $storing_letters_business_quantity = 0;
                $storing_letters_business_netprice = 0;
                $storing_packages_business_quantity = 0;
                $storing_packages_business_netprice = 0;
            }
            
            // convert data by location.
            $location_datas = array();
            $invoice_summary_id = 0;
            foreach ($invoice_details as $activity){
                $key = $activity->location_id;
                if(empty($location_datas[$key])){
                    $tmp = new stdClass();
                    $tmp->storing_letters_free_account = 0;
                    $tmp->storing_packages_free_account = 0;
                    $tmp->storing_letters_free_quantity = 0;
                    $tmp->storing_letters_free_netprice = 0;
                    $tmp->storing_packages_free_quantity = 0;
                    $tmp->storing_packages_free_netprice = 0;

                    $tmp->storing_letters_private_account = 0;
                    $tmp->storing_packages_private_account = 0;
                    $tmp->storing_letters_private_quantity = 0;
                    $tmp->storing_letters_private_netprice = 0;
                    $tmp->storing_packages_private_quantity = 0;
                    $tmp->storing_packages_private_netprice = 0;

                    $tmp->storing_letters_business_account = 0;
                    $tmp->storing_packages_business_account = 0;
                    $tmp->storing_letters_business_quantity = 0;
                    $tmp->storing_letters_business_netprice = 0;
                    $tmp->storing_packages_business_quantity = 0;
                    $tmp->storing_packages_business_netprice = 0;
                }else{
                    $tmp = $location_datas[$key];
                }
                
                if($activity->activity_type == APConstants::INVOICE_ACTIVITY_TYPE_STORAGE_FEE_LETTER){
                    if($activity->postbox_type == APConstants::FREE_TYPE){
                        $tmp->storing_letters_free_account += $activity->item_amount;
                        $tmp->storing_letters_free_quantity += $activity->item_number;
                        $tmp->storing_letters_free_netprice = $activity->unit_price;
                        
                        $storing_letters_free_account += $activity->item_amount;
                        $storing_letters_free_quantity += $activity->item_number;
                        $storing_letters_free_netprice = $activity->unit_price;
                    } else if($activity->postbox_type == APConstants::PRIVATE_TYPE){
                        $tmp->storing_letters_private_account += $activity->item_amount;
                        $tmp->storing_letters_private_quantity += $activity->item_number;
                        $tmp->storing_letters_private_netprice = $activity->unit_price;
                        
                        $storing_letters_private_account += $activity->item_amount;
                        $storing_letters_private_quantity += $activity->item_number;
                        $storing_letters_private_netprice = $activity->unit_price;
                    }else if($activity->postbox_type == APConstants::BUSINESS_TYPE || $activity->postbox_type == APConstants::ENTERPRISE_TYPE){
                        $tmp->storing_letters_business_account += $activity->item_amount;
                        $tmp->storing_letters_business_quantity += $activity->item_number;
                        $tmp->storing_letters_business_netprice = $activity->unit_price;
                        
                        $storing_letters_business_account += $activity->item_amount;
                        $storing_letters_business_quantity += $activity->item_number;
                        $storing_letters_business_netprice = $activity->unit_price;
                    }
                } else if($activity->activity_type == APConstants::INVOICE_ACTIVITY_TYPE_STORAGE_FEE_PACKAGE){
                    if($activity->postbox_type == APConstants::FREE_TYPE){
                        $tmp->storing_packages_free_account += $activity->item_amount;
                        $tmp->storing_packages_free_quantity += $activity->item_number;
                        $tmp->storing_packages_free_netprice = $activity->unit_price;
                        
                        $storing_packages_free_account += $activity->item_amount;
                        $storing_packages_free_quantity += $activity->item_number;
                        $storing_packages_free_netprice = $activity->unit_price;
                    } else if($activity->postbox_type == APConstants::PRIVATE_TYPE){
                        $tmp->storing_packages_private_account += $activity->item_amount;
                        $tmp->storing_packages_private_quantity += $activity->item_number;
                        $tmp->storing_packages_private_netprice = $activity->unit_price;
                        
                        $storing_packages_private_account += $activity->item_amount;
                        $storing_packages_private_quantity += $activity->item_number;
                        $storing_packages_private_netprice = $activity->unit_price;
                    }else if($activity->postbox_type == APConstants::BUSINESS_TYPE || $activity->postbox_type == APConstants::ENTERPRISE_TYPE){
                        $tmp->storing_packages_business_account += $activity->item_amount;
                        $tmp->storing_packages_business_quantity += $activity->item_number;
                        $tmp->storing_packages_business_netprice = $activity->unit_price;
                        
                        $storing_packages_business_account += $activity->item_amount;
                        $storing_packages_business_quantity += $activity->item_number;
                        $storing_packages_business_netprice = $activity->unit_price;
                    }
                }

                $tmp->location_id = $activity->location_id;
                $tmp->postbox_type = $activity->postbox_type;
                $tmp->unit_price = $activity->unit_price;
                $tmp->customer_id = $activity->customer_id;
                $invoice_summary_id = $activity->invoice_summary_id;

                $location_datas[$key] = $tmp;
                unset($tmp);
            }
            
            $targetYM = APUtils::getCurrentYearMonth();
            
            // update invoice summary
            $invoice_check = ci()->invoice_summary_m->get_by_many(array(
                "customer_id" => $customer_id,
                "id" => $invoice_summary_id,
            ));
            $invoice_code = $invoice_check->invoice_code;
            ci()->invoice_summary_m->update_by_many(array(
                "customer_id" => $customer_id,
                "id" => $invoice_summary_id,
            ), array(
                "storing_letters_free_account" => $storing_letters_free_account,
                "storing_packages_free_account" => $storing_packages_free_account,
                "storing_letters_free_quantity" => $storing_letters_free_quantity,
                "storing_letters_free_netprice" => $storing_letters_free_netprice,
                "storing_packages_free_quantity" => $storing_packages_free_quantity,
                "storing_packages_free_netprice" => $storing_packages_free_netprice,

                "storing_letters_private_account" => $storing_letters_private_account,
                "storing_packages_private_account" => $storing_packages_private_account,
                "storing_letters_private_quantity" => $storing_letters_private_quantity,
                "storing_letters_private_netprice" => $storing_letters_private_netprice,
                "storing_packages_private_quantity" => $storing_packages_private_quantity,
                "storing_packages_private_netprice" => $storing_packages_private_netprice,

                "storing_letters_business_account" => $storing_letters_business_account,
                "storing_packages_business_account" => $storing_packages_business_account,
                "storing_letters_business_quantity" => $storing_letters_business_quantity,
                "storing_letters_business_netprice" => $storing_letters_business_netprice,
                "storing_packages_business_quantity" => $storing_packages_business_quantity,
                "storing_packages_business_netprice" => $storing_packages_business_netprice,
                
                "vat" => $vat,
                "vat_case" => $vat_case_id,
            ));
            
            $upcharge = 0;
            $parent_customer_id = $customer->parent_customer_id;
            $customer_setting = array();
            if($customer->account_type == APConstants::ENTERPRISE_TYPE){
                if(empty($parent_customer_id)){
                    $parent_customer_id = $customer->customer_id;
                }
                // Gets customers setting
                $customer_setting = AccountSetting::get_list_setting_by($customer->parent_customer_id);
            }

            // update invoice by location
            foreach($location_datas as $location_id=>$location){
                $where = array(
                    "customer_id" => $customer_id,
                    "location_id" => $location_id,
                    "invoice_month" => $targetYM,
                    "invoice_type" => 1
                );
                $data = array(
                    "storing_letters_free_account" => $location->storing_letters_free_account,
                    "storing_packages_free_account" => $location->storing_packages_free_account,
                    "storing_letters_free_quantity" => $location->storing_letters_free_quantity,
                    "storing_letters_free_netprice" => $location->storing_letters_free_netprice,
                    "storing_packages_free_quantity" => $location->storing_packages_free_quantity,
                    "storing_packages_free_netprice" => $location->storing_packages_free_netprice,

                    "storing_letters_private_account" => $location->storing_letters_private_account,
                    "storing_packages_private_account" => $location->storing_packages_private_account,
                    "storing_letters_private_quantity" => $location->storing_letters_private_quantity,
                    "storing_letters_private_netprice" => $location->storing_letters_private_netprice,
                    "storing_packages_private_quantity" => $location->storing_packages_private_quantity,
                    "storing_packages_private_netprice" => $location->storing_packages_private_netprice,

                    "storing_letters_business_account" => $location->storing_letters_business_account,
                    "storing_packages_business_account" => $location->storing_packages_business_account,
                    "storing_letters_business_quantity" => $location->storing_letters_business_quantity,
                    "storing_letters_business_netprice" => $location->storing_letters_business_netprice,
                    "storing_packages_business_quantity" => $location->storing_packages_business_quantity,
                    "storing_packages_business_netprice" => $location->storing_packages_business_netprice,
                    
                    "vat" => $vat,
                    "vat_case" => $vat_case_id,
                    "invoice_code" => $invoice_code
                );
                $invoice_check = ci()->invoice_summary_by_location_m->get_by_many($where);
                
                if(empty($invoice_check)){
                    $merge_data = array_merge($where, $data);
                    ci()->invoice_summary_by_location_m->insert($merge_data);
                }else{
                    ci()->invoice_summary_by_location_m->update_by_many($where, $data);
                }
                
                // calculate user enterprise fee.
                if($customer->account_type == APConstants::ENTERPRISE_TYPE){
                    // Add upcharge to postbox fee
                    $upcharge = invoices_api::get_customer_setting_by_key($customer_setting, 'postbox_fee', $location_id);
                    
                    $invoice_user_check = ci()->invoice_summary_by_user_m->get_by_many(array(
                        "customer_id" => $customer->customer_id,
                        "invoice_month" => $targetYM,
                        "invoice_code" => $invoice_code,
                        "location_id" => $location_id
                    ));
                    if(empty($invoice_user_check)){
                        ci()->invoice_summary_by_user_m->insert(array(
                            "customer_id" => $customer->customer_id,
                            "invoice_month" => $targetYM,
                            "invoice_code" => $invoice_code,
                            "location_id" => $location_id,

                            // letter
                            "storing_letters_business_account" => $location->storing_letters_business_account + ($upcharge *  $location->storing_letters_business_quantity),
                            "storing_letters_business_quantity" => $location->storing_letters_business_quantity,
                            "storing_letters_business_netprice" => $location->storing_letters_business_netprice + $upcharge,
                            
                            // package
                            "storing_packages_business_account" => $location->storing_letters_business_account + ($upcharge *  $location->storing_packages_business_quantity),
                            "storing_packages_business_quantity" => $location->storing_packages_business_quantity,
                            "storing_packages_business_netprice" => $location->storing_letters_business_netprice + $upcharge,

                            "invoice_type" => '1',
                            "vat" => $vat,
                            "vat_case" => $vat_case_id,
                            "postbox_fee_upcharge" => $upcharge
                        ));
                    }else{
                        ci()->invoice_summary_by_user_m->update_by_many(array(
                            "customer_id" => $customer->customer_id,
                            "invoice_month" => $targetYM,
                            "invoice_code" => $invoice_code,
                            "location_id" => $location_id,
                        ), array(
                            // letter
                            "storing_letters_business_account" => $location->storing_letters_business_account + ($upcharge *  $location->storing_letters_business_quantity),
                            "storing_letters_business_quantity" => $location->storing_letters_business_quantity,
                            "storing_letters_business_netprice" => $location->storing_letters_business_netprice + $upcharge,
                            
                            // package
                            "storing_packages_business_account" => $location->storing_letters_business_account + ($upcharge *  $location->storing_packages_business_quantity),
                            "storing_packages_business_quantity" => $location->storing_packages_business_quantity,
                            "storing_packages_business_netprice" => $location->storing_letters_business_netprice + $upcharge,

                            "invoice_type" => '1',
                            "vat" => $vat,
                            "vat_case" => $vat_case_id,
                            "postbox_fee_upcharge" => $upcharge
                        ));
                    }
                } // end calculate storage of user enterprise.
            }// end foreach by location.
        }// end customer calculation.

    }
    
    /**
     * summary all storage fee from envelopes
     */
    public function cal_storage_summary_backup($input_customer_id = '') {
        // echo 'Start to call calculate storage summary <br/>';
        // ob_flush();
        // flush();

        $target_month = APUtils::getCurrentMonthInvoice();
        $target_year = APUtils::getCurrentYearInvoice();

        // Get all setting type
        $all_envelope_type = Settings::get_list(APConstants::ENVELOPE_TYPE_CODE);
        $all_envelope_type_letter = array();
        $all_envelope_type_package = array();
        // Get all letter type & package type
        foreach ($all_envelope_type as $envelope_type) {
            if ($envelope_type->Alias02 == 'Letter') {
                $all_envelope_type_letter [] = $envelope_type->ActualValue;
            }
            if ($envelope_type->Alias02 == 'Package') {
                $all_envelope_type_package [] = $envelope_type->ActualValue;
            }
        }

        if(empty($input_customer_id)){
            // reset storage cost of customer
            ci()->envelope_m->update_all_customer_not_has_fee_storage();

            // reset storage cost of customer by location.
            ci()->invoice_summary_by_location_m->reset_storage_cost($target_year.$target_month);
        }

        // echo 'Start to get_all_customer_id_has_storage_cost <br/>';
        // ob_flush();
        // flush();
        $customers = ci()->envelope_m->get_all_customer_id_has_storage_cost($input_customer_id);

        // echo "Total customer: " . count($customers) . '<br/>';
        // ob_flush();
        // flush();

        // tracking total customers calculation.
        $total_customer_calculation = 0;

        // For each customer will can culate store items
        foreach ($customers as $customer) {
            // Get all postbox of this customers
            $customer_id = $customer->customer_id;
            if (!empty($input_customer_id) && $input_customer_id != $customer_id) {
                continue;
            }

            // By pass deleted customer
            //if ($customer->status == APConstants::ON_FLAG) {
            //    continue;
            //}

            $all_postboxs = ci()->postbox_m->get_many_by_many(array(
                'customer_id' => $customer_id
            ));
            if (empty($all_postboxs)) {
                continue;
            }
            $customerVat = APUtils::getVatRateOfCustomer($customer_id);
            $vat = $customerVat->rate;
            $vat_case_id = $customerVat->vat_case_id;

            // Init data
            $storing_letters_free_account = 0;
            $storing_packages_free_account = 0;
            $storing_letters_free_quantity = 0;
            $storing_letters_free_netprice = 0;
            $storing_packages_free_quantity = 0;
            $storing_packages_free_netprice = 0;

            $storing_letters_private_account = 0;
            $storing_packages_private_account = 0;
            $storing_letters_private_quantity = 0;
            $storing_letters_private_netprice = 0;
            $storing_packages_private_quantity = 0;
            $storing_packages_private_netprice = 0;

            $storing_letters_business_account = 0;
            $storing_packages_business_account = 0;
            $storing_letters_business_quantity = 0;
            $storing_letters_business_netprice = 0;
            $storing_packages_business_quantity = 0;
            $storing_packages_business_netprice = 0;

            $location_invoice = array();
            foreach ($all_postboxs as $postbox) {
                $postbox_id = $postbox->postbox_id;
                $location_id = $postbox->location_available_id;
                $postbox_type = $postbox->type;
                if(!isset($location_invoice[$location_id])){
                    $location_invoice[$location_id] = array(
                        "storing_letters_free_account" => 0,
                        "storing_packages_free_account" => 0,
                        "storing_letters_free_quantity" => 0,
                        "storing_letters_free_netprice" => 0,
                        "storing_packages_free_quantity" => 0,
                        "storing_packages_free_netprice" => 0,
                        "storing_letters_private_account" => 0,
                        "storing_packages_private_account" => 0,
                        "storing_letters_private_quantity" => 0,
                        "storing_letters_private_netprice" => 0,
                        "storing_packages_private_quantity" => 0,
                        "storing_packages_private_netprice" => 0,
                        "storing_letters_business_account" => 0,
                        "storing_packages_business_account" => 0,
                        "storing_letters_business_quantity" => 0,
                        "storing_letters_business_netprice" => 0,
                        "storing_packages_business_quantity" => 0,
                        "storing_packages_business_netprice" => 0,
                    );
                }

                // Get price model
                // $price_postbox = price_api::getPricingModelByLocationID($location_id, $postbox_type);
                $pricing_map = price_api::getPricingModelByCusotomerAndLocationID($customer_id, $location_id);
                $price_postbox = $pricing_map[$postbox_type];

                // $/day
                $price_per_day_per_letter = $price_postbox['storing_items_over_free_letter'];
                $price_per_day_per_package = $price_postbox['storing_items_over_free_packages'];

                // Calculate total storage letter day
                $array_condition = array(
                    "to_customer_id" => $customer_id,
                    "postbox_id" => $postbox_id
                );
                
                $storing_letters_quantity = ci()->envelope_m->sum_envelope_storage_fee($array_condition, $all_envelope_type_letter);
                $storing_packages_quantity = ci()->envelope_m->sum_envelope_storage_fee($array_condition, $all_envelope_type_package);

                $storing_letter_amount = $storing_letters_quantity * $price_per_day_per_letter;
                $storing_package_amount = $storing_packages_quantity * $price_per_day_per_package;

                if ($postbox_type === APConstants::FREE_TYPE) {
                    $storing_letters_free_account += $storing_letter_amount;
                    $storing_packages_free_account += $storing_package_amount;
                    $storing_letters_free_quantity += $storing_letters_quantity;
                    $storing_packages_free_quantity += $storing_packages_quantity;
                    $storing_letters_free_netprice = $price_per_day_per_letter;
                    $storing_packages_free_netprice = $price_per_day_per_package;
                    
                    $location_invoice[$location_id]["storing_letters_free_account"] += $storing_letter_amount;
                    $location_invoice[$location_id]["storing_packages_free_account"] += $storing_package_amount;
                    $location_invoice[$location_id]["storing_letters_free_quantity"] += $storing_letters_quantity;
                    $location_invoice[$location_id]["storing_packages_free_quantity"] += $storing_packages_quantity;
                    $location_invoice[$location_id]["storing_letters_free_netprice"] = $price_per_day_per_letter;
                    $location_invoice[$location_id]["storing_packages_free_netprice"] = $price_per_day_per_package;
                } else if ($postbox_type === APConstants::PRIVATE_TYPE) {
                    $storing_letters_private_account += $storing_letter_amount;
                    $storing_packages_private_account += $storing_package_amount;
                    $storing_letters_private_quantity += $storing_letters_quantity;
                    $storing_packages_private_quantity += $storing_packages_quantity;
                    $storing_letters_private_netprice = $price_per_day_per_letter;
                    $storing_packages_private_netprice = $price_per_day_per_package;
                    
                    $location_invoice[$location_id]["storing_letters_private_account"] += $storing_letter_amount;
                    $location_invoice[$location_id]["storing_packages_private_account"] += $storing_package_amount;
                    $location_invoice[$location_id]["storing_letters_private_quantity"] += $storing_letters_quantity;
                    $location_invoice[$location_id]["storing_packages_private_quantity"] += $storing_packages_quantity;
                    $location_invoice[$location_id]["storing_letters_private_netprice"] = $price_per_day_per_letter;
                    $location_invoice[$location_id]["storing_packages_private_netprice"] = $price_per_day_per_package;
                } else if ($postbox_type === APConstants::BUSINESS_TYPE || $postbox_type === APConstants::ENTERPRISE_CUSTOMER) {
                    $storing_letters_business_account += $storing_letter_amount;
                    $storing_packages_business_account += $storing_package_amount;
                    $storing_letters_business_quantity += $storing_letters_quantity;
                    $storing_packages_business_quantity += $storing_packages_quantity;
                    $storing_letters_business_netprice = $price_per_day_per_letter;
                    $storing_packages_business_netprice = $price_per_day_per_package;
                    
                    $location_invoice[$location_id]["storing_letters_business_account"] += $storing_letter_amount;
                    $location_invoice[$location_id]["storing_packages_business_account"] += $storing_package_amount;
                    $location_invoice[$location_id]["storing_letters_business_quantity"] += $storing_letters_quantity;
                    $location_invoice[$location_id]["storing_packages_business_quantity"] += $storing_packages_quantity;
                    $location_invoice[$location_id]["storing_letters_business_netprice"] = $price_per_day_per_letter;
                    $location_invoice[$location_id]["storing_packages_business_netprice"] = $price_per_day_per_package;
                }
            }
            // End for postbox


            // If exist data will update
            $invoice_check = ci()->invoice_summary_m->get_by_many(array(
                'invoice_month' => $target_year . $target_month,
                'customer_id' => $customer_id,
                //"vat" => $vat
            ));

            // Check to INSERT or UPDATE
            if ($invoice_check) {
                $invoice_code = $invoice_check->invoice_code;
                
                ci()->invoice_summary_m->update_by_many(array(
                    'invoice_month' => $target_year . $target_month,
                    'customer_id' => $customer_id,
                ), array(
                    "storing_letters_free_account" => $storing_letters_free_account,
                    "storing_packages_free_account" => $storing_packages_free_account,
                    "storing_letters_free_quantity" => $storing_letters_free_quantity,
                    "storing_letters_free_netprice" => $storing_letters_free_netprice,
                    "storing_packages_free_quantity" => $storing_packages_free_quantity,
                    "storing_packages_free_netprice" => $storing_packages_free_netprice,

                    "storing_letters_private_account" => $storing_letters_private_account,
                    "storing_packages_private_account" => $storing_packages_private_account,
                    "storing_letters_private_quantity" => $storing_letters_private_quantity,
                    "storing_letters_private_netprice" => $storing_letters_private_netprice,
                    "storing_packages_private_quantity" => $storing_packages_private_quantity,
                    "storing_packages_private_netprice" => $storing_packages_private_netprice,

                    "storing_letters_business_account" => $storing_letters_business_account,
                    "storing_packages_business_account" => $storing_packages_business_account,
                    "storing_letters_business_quantity" => $storing_letters_business_quantity,
                    "storing_letters_business_netprice" => $storing_letters_business_netprice,
                    "storing_packages_business_quantity" => $storing_packages_business_quantity,
                    "storing_packages_business_netprice" => $storing_packages_business_netprice,
                    "invoice_type" => '1'
                ));
            } else {
                // Get all postbox of customer
                $invoice_code = invoices_api::generateInvoiceNumber();
        
                ci()->invoice_summary_m->insert(array(
                    'invoice_month' => $target_year . $target_month,
                    'customer_id' => $customer_id,
                    "invoice_code" => $invoice_code,
                    "vat_case" => $vat_case_id,
                    "vat" => $vat,
                    "invoice_type" => '1', //  (1 or Empty is Auto)
                    "storing_letters_free_account" => $storing_letters_free_account,
                    "storing_packages_free_account" => $storing_packages_free_account,
                    "storing_letters_free_quantity" => $storing_letters_free_quantity,
                    "storing_letters_free_netprice" => $storing_letters_free_netprice,
                    "storing_packages_free_quantity" => $storing_packages_free_quantity,
                    "storing_packages_free_netprice" => $storing_packages_free_netprice,

                    "storing_letters_private_account" => $storing_letters_private_account,
                    "storing_packages_private_account" => $storing_packages_private_account,
                    "storing_letters_private_quantity" => $storing_letters_private_quantity,
                    "storing_letters_private_netprice" => $storing_letters_private_netprice,
                    "storing_packages_private_quantity" => $storing_packages_private_quantity,
                    "storing_packages_private_netprice" => $storing_packages_private_netprice,

                    "storing_letters_business_account" => $storing_letters_business_account,
                    "storing_packages_business_account" => $storing_packages_business_account,
                    "storing_letters_business_quantity" => $storing_letters_business_quantity,
                    "storing_letters_business_netprice" => $storing_letters_business_netprice,
                    "storing_packages_business_quantity" => $storing_packages_business_quantity,
                    "storing_packages_business_netprice" => $storing_packages_business_netprice,
                ));
            }
            
            foreach($location_invoice as $key=>$location_data){
                // If exist data will update
                $invoice_location_check = ci()->invoice_summary_by_location_m->get_by_many(array(
                    'invoice_month' => $target_year . $target_month,
                    'customer_id' => $customer_id,
                    "location_id" => $key
                ));

                if(!$invoice_location_check){
                    ci()->invoice_summary_by_location_m->insert(array(
                        'invoice_month' => $target_year . $target_month,
                        'customer_id' => $customer_id,
                        "invoice_code" => $invoice_code,
                        "vat_case" => $vat_case_id,
                        "location_id" => $key,
                        "vat" => $vat,
                        "invoice_type" => '1', //  (1 or Empty is Auto)
                        
                        "storing_letters_free_account" =>  $location_data["storing_letters_free_account"] ,
                        "storing_packages_free_account" =>  $location_data["storing_packages_free_account"] ,
                        "storing_letters_free_quantity" =>  $location_data["storing_letters_free_quantity"] ,
                        "storing_packages_free_quantity" =>  $location_data["storing_packages_free_quantity"] ,
                        "storing_letters_free_netprice" =>  $location_data["storing_letters_free_netprice"] ,
                        "storing_packages_free_netprice" =>  $location_data["storing_packages_free_netprice"] ,
                        
                        "storing_letters_private_account" =>  $location_data["storing_letters_private_account"] ,
                        "storing_packages_private_account" =>  $location_data["storing_packages_private_account"] ,
                        "storing_letters_private_quantity" =>  $location_data["storing_letters_private_quantity"] ,
                        "storing_packages_private_quantity" =>  $location_data["storing_packages_private_quantity"] ,
                        "storing_letters_private_netprice" =>  $location_data["storing_letters_private_netprice"] ,
                        "storing_packages_private_netprice" =>  $location_data["storing_packages_private_netprice"] ,
                        
                        "storing_letters_business_account" =>  $location_data["storing_letters_business_account"] ,
                        "storing_packages_business_account" =>  $location_data["storing_packages_business_account"] ,
                        "storing_letters_business_quantity" =>  $location_data["storing_letters_business_quantity"] ,
                        "storing_packages_business_quantity" =>  $location_data["storing_packages_business_quantity"] ,
                        "storing_letters_business_netprice" =>  $location_data["storing_letters_business_netprice"] ,
                        "storing_packages_business_netprice" =>  $location_data["storing_packages_business_netprice"],

                    ));
                }else{
                    ci()->invoice_summary_by_location_m->update_by_many(array(
                        'invoice_month' => $target_year . $target_month,
                        'customer_id' => $customer_id,
                        "location_id" => $key
                    ), array(
                        "storing_letters_free_account" =>  $location_data["storing_letters_free_account"] ,
                        "storing_packages_free_account" =>  $location_data["storing_packages_free_account"] ,
                        "storing_letters_free_quantity" =>  $location_data["storing_letters_free_quantity"] ,
                        "storing_packages_free_quantity" =>  $location_data["storing_packages_free_quantity"] ,
                        "storing_letters_free_netprice" =>  $location_data["storing_letters_free_netprice"] ,
                        "storing_packages_free_netprice" =>  $location_data["storing_packages_free_netprice"] ,
                        
                        "storing_letters_private_account" =>  $location_data["storing_letters_private_account"] ,
                        "storing_packages_private_account" =>  $location_data["storing_packages_private_account"] ,
                        "storing_letters_private_quantity" =>  $location_data["storing_letters_private_quantity"] ,
                        "storing_packages_private_quantity" =>  $location_data["storing_packages_private_quantity"] ,
                        "storing_letters_private_netprice" =>  $location_data["storing_letters_private_netprice"] ,
                        "storing_packages_private_netprice" =>  $location_data["storing_packages_private_netprice"] ,
                        
                        "storing_letters_business_account" =>  $location_data["storing_letters_business_account"] ,
                        "storing_packages_business_account" =>  $location_data["storing_packages_business_account"] ,
                        "storing_letters_business_quantity" =>  $location_data["storing_letters_business_quantity"] ,
                        "storing_packages_business_quantity" =>  $location_data["storing_packages_business_quantity"] ,
                        "storing_letters_business_netprice" =>  $location_data["storing_letters_business_netprice"] ,
                        "storing_packages_business_netprice" =>  $location_data["storing_packages_business_netprice"],
                        
                        "invoice_code" => $invoice_code,
                    ));
                }
            }

            unset($location_invoice);
            
            // Summary for invoice
            APUtils::updateTotalInvoiceOfInvoiceSummaryTargetMonth($customer_id, $target_year, $target_month);
            // $this->cal_invoice_summary($customer_id, $target_year, $target_month, $vat);

            //echo "Finish to calculate envelope storage for customer id:" . $customer_id . '<br/>';
            //ob_flush();
            //flush();

            // increment tracking total customer calculation.
            $total_customer_calculation++;
        }
        // End for customer

        return $total_customer_calculation;
    }

    /**
     * Summary data from tables [envelope_summary_month] to tables [invoice_summary]. Summary from invoice_detail to invoice_summary
     *
     * @param unknown_type $customer_id
     * @param unknown_type $target_year
     * @param unknown_type $target_month
     */
    public function cal_invoice_summary($customer_id, $target_year, $target_month, $customerVat = null)
    {
        ci()->load->library('invoices/invoices_api');

        invoices_api::calculateInvoiceSummary($customer_id, $target_year, $target_month, $customerVat);
        
        // calculate invoice summary by user enterprise
        if(APContext::isUserEnterprise($customer_id)){
            $this->calc_invoice_summary_by_user($customer_id);
        }
    }

    /**
     * export pdf.
     *
     * @param unknown_type $target_date
     */
    public function export_invoice($target_date)
    {
        // Gets customer infor.
        $customer = APContext::getCustomerLoggedIn();

        // Gets next invoice
        $row = $this->invoice_summary_m->get_by_many(array(
            'invoice_month' => $target_date,
            'customer_id' => APContext::getCustomerCodeLoggedIn()
        ));
        if (empty($row)) {
            return;
        }
        $invoice_file_path = 'system/virtualpost/downloads/invoices/' . $customer->customer_code . '_' . $target_date . '.pdf';
        // Neu file da ton tai va da thuc hien thanh toan thi se doc file du lieu da ton tai. Nguoc lai thi se generate file moi
        if (file_exists($invoice_file_path) && $row->invoice_flag == '1') {
            return $invoice_file_path;
        }

        // Load pdf library
        $this->load->library('pdf');

        // create new PDF document
        // $pdf = new Tocpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf = $this->pdf;
        $pdf->SetFont('helvetica', '', 10, '', 'false');

        // set document information
        // Set common information
        $pdf->SetTitle(Settings::get(APConstants::INSTANCE_OWNER_COMPANY_CODE));
        $pdf->SetAuthor(Settings::get(APConstants::INSTANCE_OWNER_COMPANY_CODE));

        // disable header and footer
        $this->pdf->setPrintHeader(true);
        $this->pdf->setPrintFooter(true);

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

        // Gets last day of month
        $target_first = APUtils::getFirstDayOfMonth($target_date);
        $target_last = APUtils::getLastDayOfMonth($target_first);

        // Gets vat of customer.
        $customerVat = APUtils::getVatRateOfCustomer(APContext::getCustomerCodeLoggedIn());
        $vat = $customerVat->rate;

        $invoice_code = $row->invoice_code;
        if (empty($row->invoice_code)) {
            // Generate new invoice code
            $invoice_code = substr($row->invoice_month, 0, 4) . '-' . substr($row->invoice_month, 4, 2);
            $invoice_code .= '-' . sprintf('%1$06d', $row->id + 321500);
            $this->invoice_summary_m->update_by_many(array(
                'invoice_month' => $target_date,
                'customer_id' => APContext::getCustomerCodeLoggedIn(),
                "vat" => $vat
            ), array(
                "invoice_code" => $invoice_code,
                "update_flag" => 0
            ));
        }

        // Shipping & Handing always charge VAT (#342)
        //  #472 : update gross->net total.
        $row->direct_shipping_free_account = $row->direct_shipping_free_account * (1 + $vat);
        $row->direct_shipping_private_account = $row->direct_shipping_private_account * (1 + $vat);
        $row->direct_shipping_business_account = $row->direct_shipping_business_account * (1 + $vat);
        $row->collect_shipping_free_account = $row->collect_shipping_free_account * (1 + $vat);
        $row->collect_shipping_private_account = $row->collect_shipping_private_account * (1 + $vat);
        $row->collect_shipping_business_account = $row->collect_shipping_business_account * (1 + $vat);

        // Khong hien thi don gia khi thuc hien shipping
        $row->direct_shipping_free_netprice = '';
        $row->direct_shipping_private_netprice = '';
        $row->direct_shipping_business_netprice = '';
        $row->collect_shipping_free_netprice = '';
        $row->collect_shipping_private_netprice = '';
        $row->collect_shipping_business_netprice = '';

        // Get main payment information
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $this->load->model('payment/payment_m');
        $customer_payments = ci()->payment_m->get_many_by('customer_id', $customer_id);
        if (empty($customer_payments) || count($customer_payments) == 0) {
            log_message(APConstants::LOG_DEBUG, 'Customer payment information of customer id: ' . $customer_id . ' does not exist');

            // Change card number by incoice_code
            $card_number = $customer->invoice_code;
        } else {
            $customer_payment = $customer_payments [0];
            $card_number = $customer_payment->card_number;
        }

        // Gets address of customer
        $address = $this->customers_address_m->get_by('customer_id', $customer->customer_id);
        if (is_numeric($address->invoicing_country)) {
            $country = $this->countries_m->get($address->invoicing_country);
            if ($country) {
                $address->invoicing_country = $country->country_name;
            }
        }

        $net_total = $row->private_postboxes_amount + $row->business_postboxes_amount + $row->additional_pages_scanning;
        $net_total += $row->incomming_items_free_account + $row->incomming_items_private_account + $row->incomming_items_business_account;
        $net_total += $row->envelope_scan_free_account + $row->envelope_scan_private_account + $row->envelope_scan_business_account;
        $net_total += $row->item_scan_free_account + $row->item_scan_private_account + $row->item_scan_business_account;
        $net_total += $row->storing_letters_free_account + $row->storing_letters_private_account + $row->storing_letters_business_account;
        $net_total += $row->storing_packages_free_account + $row->storing_packages_private_account + $row->storing_packages_business_account;
        $net_total += $row->additional_private_postbox_amount + $row->additional_business_postbox_amount;

        $ship_total = $row->direct_shipping_free_account + $row->direct_shipping_private_account + $row->direct_shipping_business_account;
        $ship_total += $row->collect_shipping_free_account + $row->collect_shipping_private_account + $row->collect_shipping_business_account;

        // #472: change gross -> net TOTAL.
        //$gross_price = $net_total + $ship_total * (1 + $vat);
        $gross_price = ($net_total + $ship_total) * (1 + $vat);
        $row->target_year = substr($target_date, 0, 4);
        $row->target_month = substr($target_date, 4);
        $row->target_last = $target_last;
        $html = $this->load->view("invoices/template", array(
            'invoice' => $row,
            'target_date' => $target_date,
            'customer' => $customer,
            'address' => $address,
            'vat' => $vat,
            'total' => $gross_price,
            'net_total' => $net_total + $ship_total,
            'card_number' => $card_number,
            'invoice_code' => $invoice_code
        ), TRUE);

        $pdf->AddPage();
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, 'J', true);

        $pdf->Output($invoice_file_path, 'F');

        // Gia ca khi tinh phi da bao gom VAT (19%) nen neu khach hang da mat thue VAT se giam dc tien di 19% phi nay
        // #472: change gross -> net TOTAL.
        //$net_total = $net_total / ($vat_total - $vat);


        $this->invoice_summary_m->update_by_many(array(
            'invoice_month' => $target_date,
            'customer_id' => APContext::getCustomerCodeLoggedIn(),
            'vat' => $vat
        ), array(
            "total_invoice" => $net_total,
            "invoice_file_path" => $invoice_file_path,
            "update_flag" => 0
        ));

        return $invoice_file_path;
    }
    
    /**
     * calculate invoice of user.
     */
    public function calc_invoice_summary_by_user($customer_id=''){
        $message = "<h4>List of customers:</h4><br/>";
        if(empty($customer_id)){
            $customers = ci()->customer_m->get_many_by_many(array(
                "account_type" => APConstants::ENTERPRISE_CUSTOMER,
                "(parent_customer_id IS NULL OR parent_customer_id = '')" => null,
                "status" => APConstants::OFF_FLAG
            ));
        }else{
            $customers = array();
            $customers[] = ci()->customer_m->get($customer_id);
        }
        // do calculate invoice from invoice summary -> invoice_summary_by_user.
        foreach ($customers as $customer){
            // Gets invoice summary by location.
            $invoices = ci()->invoice_summary_by_location_m->get_many_by_many(array(
                "customer_id" => $customer->customer_id,
                "left(invoice_month, 6)=".APUtils::getCurrentYear().APUtils::getCurrentMonth() => null,
                "(invoice_type IS NULL OR invoice_type <> 2)" => null
            ));
            
            $message .= "<div>Customer Id: ".$customer->customer_id."</div>";
            if(empty($invoices)){
                continue;
            }
            
            // Gets current upcharge
            $customer_setting = AccountSetting::get_list_setting_by($customer->parent_customer_id);
            $customer_id = $customer->customer_id;

            foreach($invoices as $invoice){
                $location_id = $invoice->location_id;
                
                // get upcharge fee
                $postbox_upcharge = $this->get_setting_by_key($customer_setting, 'postbox_fee', $location_id);
                $additional_incomming_upcharge = $this->get_setting_by_key($customer_setting, 'additional_incomming_items', $location_id);
                $envelope_scanning_upcharge = $this->get_setting_by_key($customer_setting, 'envelop_scanning', $location_id);
                $item_scan_upcharge = $this->get_setting_by_key($customer_setting, 'opening_scanning', $location_id);
                $direct_shipping_plus_upcharge = $this->get_setting_by_key($customer_setting, 'shipping_plus', $location_id);
                $direct_shipping_directly_upcharge = $this->get_setting_by_key($customer_setting, 'send_out_directly', $location_id);
                $collect_shipping_plus_upcharge = $this->get_setting_by_key($customer_setting, 'collect_shipping_plus', $location_id);
                $collect_shipping_directly_upcharge = $this->get_setting_by_key($customer_setting, 'send_out_collected', $location_id);
                $storing_letter_upcharge = $this->get_setting_by_key($customer_setting, 'storing_items_over_free_letter', $location_id);
                $storing_package_upcharge = $this->get_setting_by_key($customer_setting, 'storing_items_over_free_packages', $location_id);
                $additional_included_page_upcharge = $this->get_setting_by_key($customer_setting, 'additional_included_page_opening_scanning', $location_id);
                $custom_declaration_outgoing_01_upcharge = $this->get_setting_by_key($customer_setting, 'custom_declaration_outgoing_01', $location_id);
                $custom_declaration_outgoing_02_upcharge = $this->get_setting_by_key($customer_setting, 'custom_declaration_outgoing_02', $location_id);
                $custom_handling_import_upcharge = $this->get_setting_by_key($customer_setting, 'custom_handling_import', $location_id);
                
                $vat = $customer->vat_rate;
                // converted data
                $data = array(
                    "postbox_fee_upcharge" => $postbox_upcharge,
                    "additional_incomming_item_upcharge" => $additional_incomming_upcharge,
                    "envelope_scan_upcharge " => $envelope_scanning_upcharge,
                    "item_scan_upcharge " => $item_scan_upcharge,
                    "direct_shipping_upcharge" => $direct_shipping_directly_upcharge,
                    "direct_shipping_postal_upcharge" => $direct_shipping_plus_upcharge,
                    "collect_shipping_upcharge" => $collect_shipping_directly_upcharge,
                    "collect_shipping_postal_upcharge" => $collect_shipping_plus_upcharge,
                    "storing_letter_upcharge " => $storing_letter_upcharge,
                    "storing_package_upcharge" => $storing_package_upcharge,
                    "included_page_scan_upcharge" => $additional_included_page_upcharge,
                    "custom_declaration_outgoing_01_upcharge" => $custom_declaration_outgoing_01_upcharge,
                    "custom_declaration_outgoing_02_upcharge" => $custom_declaration_outgoing_02_upcharge,
                    "custom_handling_import_upcharge" => $custom_handling_import_upcharge,
                    "invoice_code" => $invoice->invoice_code,
                    "location_id" => $location_id,
                    "customer_id" => $customer_id,
                    "invoice_month" => $invoice->invoice_month,
                    "vat_case" => $invoice->vat_case,
                    "vat" => $vat,
                    //"invoice_file_path" => $invoice->invoice_file_path,
                    "payment_transaction_id" => $invoice->payment_transaction_id,
                    //"payment_1st_amount" => $invoice->payment_1st_amount,
                    //"payment_2st_amount" => $invoice->payment_2st_amount,
                    //"custom_declaration_outgoing_quantity_01" => $invoice->custom_declaration_outgoing_quantity_01,
                    //"custom_declaration_outgoing_quantity_02" => $invoice->custom_declaration_outgoing_quantity_02,
                    //"custom_declaration_outgoing_price_01" => $invoice->custom_declaration_outgoing_price_01 + $custom_declaration_outgoing_01_upcharge, 
                    //"custom_declaration_outgoing_price_02" => $invoice->custom_declaration_outgoing_price_02 + $custom_declaration_outgoing_02_upcharge, 
                );
                
                // additional incomming item fee
                $data['incomming_items_free_account'] = $invoice->incomming_items_free_account + ($invoice->incomming_items_free_quantity * $additional_incomming_upcharge);
                $data['incomming_items_private_account'] = $invoice->incomming_items_private_account + ($invoice->incomming_items_private_quantity * $additional_incomming_upcharge);
                $data['incomming_items_business_account'] = $invoice->incomming_items_business_account + ($invoice->incomming_items_business_quantity * $additional_incomming_upcharge);
                
                $data['incomming_items_free_netprice'] = $invoice->incomming_items_free_netprice + $additional_incomming_upcharge;
                $data['incomming_items_private_netprice'] = $invoice->incomming_items_private_netprice + $additional_incomming_upcharge;
                $data['incomming_items_business_netprice'] = $invoice->incomming_items_business_netprice + $additional_incomming_upcharge;
                
                $data['incomming_items_free_quantity'] = $invoice->incomming_items_free_quantity;
                $data['incomming_items_private_quantity'] = $invoice->incomming_items_private_quantity;
                $data['incomming_items_business_quantity'] = $invoice->incomming_items_business_quantity;
                
                // envelope scan item fee
                $data['envelope_scan_free_account'] = $invoice->envelope_scan_free_account + ($invoice->envelope_scan_free_quantity * $envelope_scanning_upcharge);
                $data['envelope_scan_private_account'] = $invoice->envelope_scan_private_account + ($invoice->envelope_scan_private_quantity * $envelope_scanning_upcharge);
                $data['envelope_scan_business_account'] = $invoice->envelope_scan_business_account + ($invoice->envelope_scan_business_quantity * $envelope_scanning_upcharge);
                
                $data['envelope_scan_free_netprice'] = $invoice->envelope_scan_free_netprice + $envelope_scanning_upcharge;
                $data['envelope_scan_private_netprice'] = $invoice->envelope_scan_private_netprice + $envelope_scanning_upcharge;
                $data['envelope_scan_business_netprice'] = $invoice->envelope_scan_business_netprice + $envelope_scanning_upcharge;
                
                $data['envelope_scan_free_quantity'] = $invoice->envelope_scan_free_quantity;
                $data['envelope_scan_private_quantity'] = $invoice->envelope_scan_private_quantity;
                $data['envelope_scan_business_quantity'] = $invoice->envelope_scan_business_quantity;
                
                // item scan item fee
                $data['item_scan_free_account'] = $invoice->item_scan_free_account + ($invoice->item_scan_free_quantity * $item_scan_upcharge);
                $data['item_scan_private_account'] = $invoice->item_scan_private_account + ($invoice->item_scan_private_quantity * $item_scan_upcharge);
                $data['item_scan_business_account'] = $invoice->item_scan_business_account + ($invoice->item_scan_business_quantity * $item_scan_upcharge);
                
                $data['item_scan_free_netprice'] = $invoice->item_scan_free_netprice + $item_scan_upcharge;
                $data['item_scan_private_netprice'] = $invoice->item_scan_private_netprice + $item_scan_upcharge;
                $data['item_scan_business_netprice'] = $invoice->item_scan_business_netprice + $item_scan_upcharge;
                
                $data['item_scan_free_quantity'] = $invoice->item_scan_free_quantity;
                $data['item_scan_private_quantity'] = $invoice->item_scan_private_quantity;
                $data['item_scan_business_quantity'] = $invoice->item_scan_business_quantity;
                
                // storing letter fee
                $data['storing_letters_free_account'] = $invoice->storing_letters_free_account + ($invoice->storing_letters_free_quantity * $storing_letter_upcharge);
                $data['storing_letters_private_account'] = $invoice->storing_letters_private_account + ($invoice->storing_letters_private_quantity * $storing_letter_upcharge);
                $data['storing_letters_business_account'] = $invoice->storing_letters_business_account + ($invoice->storing_letters_business_quantity * $storing_letter_upcharge);
                
                $data['storing_letters_free_netprice'] = $invoice->storing_letters_free_netprice + $storing_letter_upcharge;
                $data['storing_letters_private_netprice'] = $invoice->storing_letters_private_netprice + $storing_letter_upcharge;
                $data['storing_letters_business_netprice'] = $invoice->storing_letters_business_netprice + $storing_letter_upcharge;
                
                $data['storing_letters_free_quantity'] = $invoice->storing_letters_free_quantity;
                $data['storing_letters_private_quantity'] = $invoice->storing_letters_private_quantity;
                $data['storing_letters_business_quantity'] = $invoice->storing_letters_business_quantity;
                
                // storing package fee
                $data['storing_packages_free_account'] = $invoice->storing_packages_free_account + ($invoice->storing_packages_free_quantity * $storing_package_upcharge);
                $data['storing_packages_private_account'] = $invoice->storing_packages_private_account + ($invoice->storing_packages_private_quantity * $storing_package_upcharge);
                $data['storing_packages_business_account'] = $invoice->storing_packages_business_account + ($invoice->storing_packages_business_quantity * $storing_package_upcharge);
                
                $data['storing_packages_free_netprice'] = $invoice->storing_packages_free_netprice + $storing_package_upcharge;
                $data['storing_packages_private_netprice'] = $invoice->storing_packages_private_netprice + $storing_package_upcharge;
                $data['storing_packages_business_netprice'] = $invoice->storing_packages_business_netprice + $storing_package_upcharge;
                
                $data['storing_packages_free_quantity'] = $invoice->storing_packages_free_quantity;
                $data['storing_packages_private_quantity'] = $invoice->storing_packages_private_quantity;
                $data['storing_packages_business_quantity'] = $invoice->storing_packages_business_quantity;
                
                // additional page scanning fee.
                $data['additional_pages_scanning_free_amount'] = $invoice->additional_pages_scanning_free_amount + ($invoice->additional_pages_scanning_free_quantity * $additional_included_page_upcharge);
                $data['additional_pages_scanning_private_amount'] = $invoice->additional_pages_scanning_private_amount + ($invoice->additional_pages_scanning_private_quantity * $additional_included_page_upcharge);
                $data['additional_pages_scanning_business_amount'] = $invoice->additional_pages_scanning_business_amount + ($invoice->additional_pages_scanning_business_quantity * $additional_included_page_upcharge);
                
                $data['additional_pages_scanning_free_netprice'] = $invoice->additional_pages_scanning_free_netprice + $additional_included_page_upcharge;
                $data['additional_pages_scanning_private_netprice'] = $invoice->additional_pages_scanning_private_netprice + $additional_included_page_upcharge;
                $data['additional_pages_scanning_business_netprice'] = $invoice->additional_pages_scanning_business_netprice + $additional_included_page_upcharge;
                
                $data['additional_pages_scanning_free_quantity'] = $invoice->additional_pages_scanning_free_quantity;
                $data['additional_pages_scanning_private_quantity'] = $invoice->additional_pages_scanning_private_quantity;
                $data['additional_pages_scanning_business_quantity'] = $invoice->additional_pages_scanning_business_quantity;
                
                // invoice check
                $invoice_check = ci()->invoice_summary_by_user_m->get_by_many(array(
                    "invoice_code" => $invoice->invoice_code,
                    "location_id" => $location_id,
                    "customer_id" => $customer_id,
                    "invoice_month" => $invoice->invoice_month,
                ));
                
                if(empty($invoice_check)){
                    $data['created_date'] = now();
                    ci()->invoice_summary_by_user_m->insert($data);
                }else{
                    ci()->invoice_summary_by_user_m->update_by_many(array(
                        "invoice_code" => $invoice->invoice_code,
                        "location_id" => $location_id,
                        "customer_id" => $customer_id,
                        "invoice_month" => $invoice->invoice_month,
                    ), $data);
                }
            }// end foreach
            
            // =================================================================
            // calculate shipping fee with upcharge.
            $this->calc_shipping_invoice_of_user_enteprise($customer, $customer_setting);
            
            // update total invoice of customer
            APUtils::updateTotalInvoiceUserEnterprise($customer_id);
        }
        
        return $message;
    }
    
    /**
     * calculate shipping invoice of user enterprise.
     * 
     * @param type $customer
     * @param type $customer_setting
     */
    public function calc_shipping_invoice_of_user_enteprise($customer, $customer_setting=''){
        $customer_id = $customer->customer_id;
        if(empty($customer_setting)){
            $customer_setting = AccountSetting::get_list_setting_by($customer->parent_customer_id);
        }
        
        // get target invoice month.
        $report_month = APUtils::getCurrentYear().APUtils::getCurrentMonth();
        
        $invoice_details = ci()->invoice_detail_m->get_many_by_many(array(
            "customer_id" => $customer_id,
            "activity_type" => "4" // shipping type
        ));
        
        if(empty($invoice_details)){
            return;
        }
        
        foreach($invoice_details as $invoice_detail){
            $location_id = $invoice_detail->location_id;
            $envelope_id = $invoice_detail->envelope_id;
            
            // get envelope shipping
            $envelope_shipping = ci()->envelope_shipping_m->get_by_many(array(
                "envelope_id" => $envelope_id,
                "customer_id" => $customer_id
            ));
            if (empty($envelope_shipping)){
                continue;
            }
            
            // calculate new shipping with upcharge.
            $envelope = ci()->envelope_m->get($envelope_id);
            
            // gets pricing
            // $pricing_map = price_api::getPricingModelByPostboxID($envelope->postbox_id);
            // Update #1438        
            // $pricing_map = price_api::getPricingModelByPostboxID($postbox_id);
            $pricing_map = price_api::getPricingModelByCusotomerAndLocationID($customer_id, $location_id);
            
            // get upcharges
            $direct_shipping_plus_upcharge = $pricing_map[APConstants::ENTERPRISE_CUSTOMER]['shipping_plus'] +  $this->get_setting_by_key($customer_setting, 'shipping_plus', $location_id);
            $direct_shipping_directly_upcharge = $pricing_map[APConstants::ENTERPRISE_CUSTOMER]['send_out_directly'] + $this->get_setting_by_key($customer_setting, 'send_out_directly', $location_id);
            $collect_shipping_plus_upcharge =$pricing_map[APConstants::ENTERPRISE_CUSTOMER]['collect_shipping_plus'] +  $this->get_setting_by_key($customer_setting, 'collect_shipping_plus', $location_id);
            $collect_shipping_directly_upcharge =  $pricing_map[APConstants::ENTERPRISE_CUSTOMER]['send_out_collected'] + $this->get_setting_by_key($customer_setting, 'send_out_collected', $location_id);
            $custom_declaration_outgoing_01_upcharge =  $pricing_map[APConstants::ENTERPRISE_CUSTOMER]['custom_declaration_outgoing_01'] + $this->get_setting_by_key($customer_setting, 'custom_declaration_outgoing_01', $location_id);
            $custom_declaration_outgoing_02_upcharge =  $pricing_map[APConstants::ENTERPRISE_CUSTOMER]['custom_declaration_outgoing_02'] + $this->get_setting_by_key($customer_setting, 'custom_declaration_outgoing_02', $location_id);
            $custom_handling_import_upcharge =  $pricing_map[APConstants::ENTERPRISE_CUSTOMER]['custom_handling_import'] + $this->get_setting_by_key($customer_setting, 'custom_handling_import', $location_id);

            $direct_shipping_fee = 0;
            $collect_shipping_fee = 0;
            $direct_shipping_business_quantity = 0;
            $collect_shipping_business_quantity = 0;
            if(empty($envelope->package_id)){
                // calculate for direct shipping type
                $direct_shipping_business_quantity = 1;
                $forwarding_charge_postal = $envelope_shipping->forwarding_charges_postal;
                $forwarding_charges_fee = $direct_shipping_plus_upcharge + $direct_shipping_directly_upcharge;
                
                // customs_handling
                // Apply declare customs fee
                $envelope_customs = ci()->envelope_customs_m->get_by_many(array(
                    "envelope_id" => $envelope_id
                ));
                $customs_handling = $envelope_shipping->customs_handling_fee;
                if(!empty($envelope_customs)){
                    $total_customs_cost = $envelope_shipping->insurance_customs_cost;
                    if ($total_customs_cost > 1000) {
                        $customs_handling += $custom_declaration_outgoing_01_upcharge;
                    } else if ($total_customs_cost > 0 && $total_customs_cost <= 1000) {
                        $customs_handling += $custom_declaration_outgoing_02_upcharge;
                    }
                }
                
                // total shipping fee
                $direct_shipping_fee = $forwarding_charge_postal + $forwarding_charges_fee + $customs_handling;
            }else{
                // calculate fee for collect shipping fee.
                $collect_shipping_business_quantity = 1;
                $forwarding_charges_fee = $collect_shipping_plus_upcharge + $collect_shipping_directly_upcharge;
                $total_envelope = ci()->envelope_m->count_by_many(array(
                    "package_id" => $envelope->package_id,
                    "to_customer_id" => $customer_id
                ));
                
                $forwarding_charge_postal = $envelope_shipping->shipping_fee + ($envelope_shipping->special_service_fee * $total_envelope);
                
                // Apply declare customs fee
                $envelope_customs = ci()->envelope_customs_m->get_by_many(array(
                    "package_id" => $envelope->package_id,
                    "customer_id" => $customer_id
                ));
                $customs_handling = $envelope_shipping->customs_handling_fee;
                if(!empty($envelope_customs)){
                    $total_customs_cost = $envelope_shipping->insurance_customs_cost;
                    if ($total_customs_cost > 1000) {
                        $customs_handling += $custom_declaration_outgoing_01_upcharge;
                    } else if ($total_customs_cost > 0 && $total_customs_cost <= 1000) {
                        $customs_handling += $custom_declaration_outgoing_02_upcharge;
                    }
                }
                // total shipping fee
                $collect_shipping_fee = $forwarding_charge_postal + $forwarding_charges_fee + $customs_handling;
            }
            
            // update upcharge into invoice_summary_by_user.
            $invoice_check = ci()->invoice_summary_by_user_m->get_by_many(array(
                "customer_id" => $customer_id,
                "invoice_month" => $report_month,
                "invoice_type" => "1",
                "location_id" => $location_id
            ));
            if(empty($invoice_check)){
                ci()->invoice_summary_by_user_m->insert(array(
                    "customer_id" => $customer_id,
                    "invoice_month" => $report_month,
                    "invoice_type" => "1",
                    "direct_shipping_business_account" => $direct_shipping_fee,
                    "collect_shipping_business_account" => $collect_shipping_fee,
                    "direct_shipping_business_quantity" => $direct_shipping_business_quantity,
                    "collect_shipping_business_quantity" => $collect_shipping_business_quantity,
                    "created_date" => now(),
                    "direct_shipping_upcharge" => $direct_shipping_directly_upcharge,
                    "direct_shipping_postal_upcharge" => $direct_shipping_plus_upcharge,
                    "collect_shipping_upcharge" => $collect_shipping_directly_upcharge,
                    "collect_shipping_postal_upcharge" => $collect_shipping_plus_upcharge,
                    "custom_declaration_outgoing_01_upcharge" => $custom_declaration_outgoing_01_upcharge,
                    "custom_declaration_outgoing_02_upcharge" => $custom_declaration_outgoing_02_upcharge,
                    "custom_handling_import_upcharge" => $custom_handling_import_upcharge,
                    "location_id" => $location_id
                ));
            }else{
                // update shipping fee.
                $direct_shipping_fee += $invoice_check->direct_shipping_business_account;
                $collect_shipping_fee += $invoice_check->collect_shipping_business_account;
                $direct_shipping_business_quantity += $invoice_check->direct_shipping_business_quantity;
                $collect_shipping_business_quantity += $invoice_check->collect_shipping_business_quantity;
                
                ci()->invoice_summary_by_user_m->update_by_many(array(
                    "customer_id" => $customer_id,
                    "invoice_month" => $report_month,
                    "invoice_type" => "1",
                    "location_id" => $location_id
                ), array(
                    "direct_shipping_business_account" => $direct_shipping_fee,
                    "collect_shipping_business_account" => $collect_shipping_fee,
                    "direct_shipping_business_quantity" => $direct_shipping_business_quantity,
                    "collect_shipping_business_quantity" => $collect_shipping_business_quantity,
                    "direct_shipping_upcharge" => $direct_shipping_directly_upcharge,
                    "direct_shipping_postal_upcharge" => $direct_shipping_plus_upcharge,
                    "collect_shipping_upcharge" => $collect_shipping_directly_upcharge,
                    "collect_shipping_postal_upcharge" => $collect_shipping_plus_upcharge,
                    "custom_declaration_outgoing_01_upcharge" => $custom_declaration_outgoing_01_upcharge,
                    "custom_declaration_outgoing_02_upcharge" => $custom_declaration_outgoing_02_upcharge,
                    "custom_handling_import_upcharge" => $custom_handling_import_upcharge,
                ));
            }
        }
        
    }
    
    /**
     * Gets account setting value by key.
     * @param type $customer_setting
     * @param type $key
     * @param type $alias
     */
    private function get_setting_by_key($customer_setting, $key, $alias=''){
        $result = '';
        foreach($customer_setting as $row){
            if(!empty($alias) &&( $row->alias01  == 'all' || $row->alias01 == $alias)){
                if($row->setting_key == $key){
                    $result = $row->setting_value;
                    break;
                }
            }else{
                if($row->setting_key == $key){
                    $result = $row->setting_value;
                    break;
                }
            }
        }
        
        return $result;
    }

}