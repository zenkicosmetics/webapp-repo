<?php defined('BASEPATH') or exit('No direct script access allowed');

class phones_api
{
    /**
     * Load envelopes in mailbox
     */
    public static function loadPhoneCallHistory($parent_customer_id, $customer_id, $phone_number, $start, $limit){
        ci()->load->model('phones/phone_call_history_m');
        $array_where = array();
        $array_where['phone_call_history.customer_id'] = $customer_id;
        $array_where['phone_call_history.parent_customer_id'] = $parent_customer_id;
        $array_where['phone_call_history.phone_number'] = $phone_number;
        $output = ci()->phone_call_history_m->get_phone_call_paging($array_where, $start, $limit, 'phone_call_history.created_date', 'DESC');

        return $output;
    }
    
    /**
     * Gets call history from sontel of customer.
     * @param type $customer_id
     */
    public static function getCallHistoryFromSontel($parent_customer_id, $account_id){
        ci()->load->library('phones/sonetel');
        ci()->load->model(array(
            "phones/phone_call_history_m",
            "phones/phone_number_m",
            'settings/currencies_m',
            'invoices/invoice_summary_m',
            "phones/phone_invoice_by_location_m",
            "phones/phone_invoice_detail_m",
            "phones/pricing_phones_number_m",
            "phones/pricing_phones_number_customer_m",
            "phones/pricing_phones_outboundcalls_m",
            "phones/pricing_phones_outboundcalls_customer_m"
        ));

        if(empty($account_id)){
            return 0;
        }

        try{
            if(ENVIRONMENT == 'production'){
                $result1 = ci()->sonetel->get_list_inbound_records($account_id);
                $result2 = ci()->sonetel->get_list_outbound_records($account_id);

                $result = array_merge($result1, $result2);
            }else{
                // make dummy data.
                $result = self::makeDummyDataForCallHistory($parent_customer_id, $account_id);
            }
        }catch(Exception $e){
            throw new ThirdPartyException($e->getMesage());
        }
        if(empty($result)){
            return 0;
        }

        // Gets all currencies
        $currencies = ci()->currencies_m->get_all();
        $phone_list = array();
        foreach($result as $row){
            $phone_list[] = empty($row->usage_details->call->caller_id) ? 0 : str_replace("+", "", $row->usage_details->call->caller_id);
        }
        $list_phone_users = array();
        $list_users = array();
        $list_country_code = array();
        $phone_users = ci()->phone_number_m->get_many_by_many(array(
            "phone_number IN (".  implode(",", $phone_list).")" => null
        ));

        foreach($phone_users as $p){
            $list_phone_users[$p->phone_number] = $p->phone_number;
            $list_users[$p->phone_number] = $p->customer_id;
            $list_country_code[$p->phone_number] = $p->country_code;
        }

        // invoice summary check
        $invoice_summary = ci()->invoice_summary_m->get_by_many(array(
            'customer_id' => $parent_customer_id,
            'invoice_month' => APUtils::getCurrentYearMonth()
        ));
        
        // gets vat of customer
        //$vat = APUTils::getVatRateOfCustomer($customer_id);
        $phone_vat = APUTils::getVatRateOfDigitalGoodBy($parent_customer_id);
        
        if(empty($invoice_summary) ){
            $invoice_code = APUtils::generateInvoiceCodeById('');
            ci()->invoice_summary_m->insert(array(
                'customer_id' => $parent_customer_id,
                'invoice_month' => APUtils::getCurrentYearMonth(),
                "total_invoice" => 0,
                "vat_case" => $phone_vat->vat_case_id,
                "vat" => $phone_vat->rate,
                'invoice_code' => $invoice_code
            ));
        }else{
            $invoice_code = $invoice_summary->invoice_code;
        }

        // insert call history and charge
        $total_record = 0;
        foreach($result as $row){
            $tmp_phone_number = str_replace("+", "", $row->usage_details->call->caller_id);
            
            //if(!isset($list_phone_users[$tmp_phone_number]) || empty($list_phone_users[$tmp_phone_number])){
            //    continue;
            //}
            $customer_id = isset($list_users[$tmp_phone_number]) ? $list_users[$tmp_phone_number] : 0;
            $country_code = isset($list_country_code[$tmp_phone_number]) ? $list_country_code[$tmp_phone_number] : 0;
            
            $data = array();
            $data['record_id'] = $row->record_id;
            $data['parent_customer_id'] = $parent_customer_id;
            $data['customer_id'] = $customer_id;
            $data['phone_number'] = $tmp_phone_number;
            $data['activity'] = $row->services;
            $data['target_phone_number'] = $row->usage_details->call->to;
            $data['duration'] = $row->usage_details->call->call_length;
            $data['call_status'] = $row->usage_details->call->call_length > 0 ? 1: 0;
            $data['call_start_time'] = strtotime($row->usage_details->call->start_time);
            
            $base_usage_fixed = 0;
            if (isset($row->charges) && isset($row->charges->usage_fixed)) {
                $base_usage_fixed = $row->charges->usage_fixed;
            }
            $base_usage_time = 0;
            if (isset($row->charges) && isset($row->charges->usage_time)) {
                $base_usage_time = $row->charges->usage_time;
            }
            
            $cost_usage_fixed = $base_usage_fixed;
            $cost_usage_time = $base_usage_time;
            // Add upcharge value
            if ($row->services == 'inbound_call') {
                $per_call_fee_cl_upcharge = 0;
                $per_min_fee_cl_upcharge = 0;
                // Get clevvermail upcharge value
                $price_phone_number = ci()->pricing_phones_number_m->get_by('country_code_3', $country_code);
                if (!empty($price_phone_number)) {
                    $per_call_fee_cl_upcharge = $price_phone_number->per_call_fee_upcharge;
                    $per_min_fee_cl_upcharge = $price_phone_number->per_min_fee_upcharge;
                }

                // Get enterprise upchage value
                $per_call_fee_enterprise_upcharge = 0;
                $per_min_fee_enterprise_upcharge = 0;
                if (APContext::isEnterpriseCustomerByID($parent_customer_id)) {
                    $price_phone_number_enterprise = ci()->pricing_phones_number_customer_m->get_by_many(array(
                        'country_code_3' => $country_code,
                        'customer_id' => $parent_customer_id
                    ));
                    if (!empty($price_phone_number_enterprise)) {
                        $per_call_fee_enterprise_upcharge = $price_phone_number_enterprise->per_call_fee_upcharge;
                        $per_min_fee_enterprise_upcharge = $price_phone_number_enterprise->per_min_fee_upcharge;
                    }
                }
                
                // Calculate cost upcharge
                $cost_usage_fixed = ($base_usage_fixed * (1  + $per_call_fee_cl_upcharge / 100)) * (1 + $per_call_fee_enterprise_upcharge / 100);
                $cost_usage_time = ($base_usage_fixed * (1  + $per_min_fee_cl_upcharge / 100)) * (1 + $per_min_fee_enterprise_upcharge / 100);
            } else if ($row->services == 'outbound_call') {
                $per_call_fee_cl_upcharge = 0;
                $per_min_fee_cl_upcharge = 0;
                // Get clevvermail upcharge value
                $pricing_phones_outboundcalls = ci()->pricing_phones_outboundcalls_m->get_by('country_code_3', $country_code);
                if (!empty($pricing_phones_outboundcalls)) {
                    $per_call_fee_cl_upcharge = $pricing_phones_outboundcalls->per_call_fee_upcharge;
                    $per_min_fee_cl_upcharge = $pricing_phones_outboundcalls->usage_fee_upcharge;
                }

                // Get enterprise upchage value
                $per_call_fee_enterprise_upcharge = 0;
                $per_min_fee_enterprise_upcharge = 0;
                if (APContext::isEnterpriseCustomerByID($parent_customer_id)) {
                    $pricing_phones_outboundcalls_enterprise = ci()->pricing_phones_outboundcalls_customer_m->get_by_many(array(
                        'country_code_3' => $country_code,
                        'customer_id' => $parent_customer_id
                    ));
                    if (!empty($pricing_phones_outboundcalls_enterprise)) {
                        $per_call_fee_enterprise_upcharge = $pricing_phones_outboundcalls_enterprise->per_call_fee_upcharge;
                        $per_min_fee_enterprise_upcharge = $pricing_phones_outboundcalls_enterprise->usage_fee_upcharge;
                    }
                }
                
                // Calculate cost upcharge
                $cost_usage_fixed = ($base_usage_fixed * (1  + $per_call_fee_cl_upcharge / 100)) * (1 + $per_call_fee_enterprise_upcharge / 100);
                $cost_usage_time = ($base_usage_fixed * (1  + $per_min_fee_cl_upcharge / 100)) * (1 + $per_min_fee_enterprise_upcharge / 100);
            }
            
            $cost_upcharge = $cost_usage_fixed + $cost_usage_time;
            $data['cost'] = $raw_cost = self::convertSontelCurrency($currencies, $row->charges->currency, $cost_upcharge);
            // check insert call history
            $check_record = ci()->phone_call_history_m->get_by_many(array(
                "record_id" => $row->record_id,
                //"parent_customer_id" => $parent_customer_id,
                //"user_id" => isset($list_users[$tmp_phone_number]) ? $list_users[$tmp_phone_number] : 0
            ));
            
            if(empty($check_record)){
                $total_record ++;
                ci()->phone_call_history_m->insert($data);
            }else{
                // no need to update.
                //ci()->phone_number_m->update_by_many(array(
                //    "record_id" => $row->record_id,
                //    "customer_id" => $customer_id,
                //    "user_id" => $user_id
                //), $data);
            }
            
            // update invoice phone detail
            self::insertActivityPhoneInvoiceDetail($parent_customer_id, $customer_id, $data['activity'], APConstants::USAGE_PHONE_NUMBER_AT,
                        1, $data['cost'], $data['call_start_time'], false, $tmp_phone_number, '');
            unset($data);
        }
        
        // calculate invoice of phone call history.
        self::calculatePhoneInvoiceSummary($parent_customer_id);
        
        return $total_record;
    }
    
    /**
     * make dummy data for call history.
     */
    private static function makeDummyDataForCallHistory($customer_id, $account_id){
        $phone_user = ci()->phone_number_m->get_by_many(array(
            //"user_id" => $user_id,
            "parent_customer_id" => $customer_id
        ));

        $phone_number = "";
        if($phone_user){
            $phone_number = $phone_user->phone_number;
        }
        $data = array();
        for($i=1; $i<=20; $i++){
            $record_id = $i + $account_id;
            $data[] = '{
                        "record_id": "'.$record_id.'",
                        "timestamp": "'.date('Ymd').'T03:'.$i.':'.$i.'GMT",
                        "services" : "inbound_call",
                        "account_id" : "34231",
                        "usage_details": {
                            "call": {
                            "start_time" : "'.date('Ymd').'T0'.($i%10).':15:'.($i+10).'GMT",
                            "end_time" : "'.date('Ymd').'T0'.($i%10).':16:'.($i+10).'GMT", 
                            "call_length" : "65",
                            "from_type" : "phonenumber", 
                            "from" : "'.$phone_number.'", 
                            "caller_id" : "'.$phone_number.'", 
                            "to_type" : "phonenumber", 
                            "to" : "+121460070'.($i+10).'", 
                            "to_orig" : "+12146007000"
                            }
                        },
                        "charges":{
                            "priceplan" : "regular",
                            "currency" : "USD",
                            "usage_fixed": "0.01",
                            "usage_time": "0.06",
                            "subscription_recurring" : "10.15"
                        }
                       }';
        }
        
        $result = "[".implode(',', $data)."]";
        $result = json_decode($result);
        return $result;
    }
    
    /**
     * convert the currency from sontel to EUR in our system.
     * @param type $currencies
     * @param type $current_short
     * @param type $cost
     * @return type
     */
    private static function convertSontelCurrency($currencies, $current_short, $cost){
        $result = $cost;
        
        foreach ($currencies as $c){
            if(strtoupper($current_short) == $c->currency_short && $c->currency_rate <> 0){
                $result = $result / $c->currency_rate;
                break;
            }
        }
        return $result;
    }
    
    /**
     * canculate invoice of phone number.
     * 
     * @param type $customer_id
     */
    public static function calculatePhoneInvoiceSummary($parent_customer_id){
        ci()->load->model(array(
            "phones/phone_call_history_m",
            "phones/phone_number_m",
            'settings/currencies_m',
            'invoices/invoice_summary_m',
            "phones/phone_invoice_by_location_m",
            "phones/phone_invoice_detail_m"
        ));
        
        // Gets all activity
        $invoice_details = ci()->phone_invoice_detail_m->get_many_by_many(array(
            "parent_customer_id" => $parent_customer_id,
            "SUBSTRING(activity_date, 1, 6)='".APUtils::getCurrentYearMonth()."'" => null,
            "item_amount > 0" => null
        ));
        
        // get VAT phone.
        $customer_vat = APUTils::getVatRateOfDigitalGoodBy($parent_customer_id);
        
        $invoice_datas = array();
        $list_location_id =  array();
        foreach($invoice_details as $invoice){
            $tmp_key = $invoice->customer_id.'_' . $invoice->location_id;
            if(!in_array($tmp_key, $list_location_id)){
                array_push($list_location_id, $tmp_key);
            }
        }
        
        // init data invoice
        foreach($list_location_id as $location_id){
            $tmp = new stdClass();
            $tmp->location_id = "";
            $tmp->customer_id = "";
            $tmp->parent_customer_id = $parent_customer_id;
            
            $tmp->incomming_quantity = 0;
            $tmp->incomming_amount = 0;
            
            $tmp->outcomming_quantity = 0;
            $tmp->outcomming_amount = 0;
            
            $tmp->phone_subscription_quantity = 0;
            $tmp->phone_subscription_amount = 0;
            
            $tmp->phone_recurring_quantity = 0;
            $tmp->phone_recurring_amount = 0;
            
            $tmp->setup_fee_quantity = 0;
            $tmp->setup_fee_amount = 0;
            
            $tmp->total_invoice = 0;
            
            $invoice_datas[$location_id] = $tmp;
            unset($tmp);
        }

        // calculate invoice
        foreach($invoice_details as $invoice){
            $invoice_tmp = $invoice_datas[$invoice->customer_id.'_' . $invoice->location_id];
            
            $invoice_tmp->location_id = $invoice->location_id;
            $invoice_tmp->customer_id = $invoice->customer_id;
            $invoice_tmp->parent_customer_id = $invoice->parent_customer_id;
            
            if($invoice->activity == 'inbound_call'){
                $invoice_tmp->incomming_quantity += 1;
                $invoice_tmp->incomming_amount += $invoice->item_amount;
            }
            
            if($invoice->activity == 'outbound_call'){
                $invoice_tmp->outcomming_quantity += 1;
                $invoice_tmp->outcomming_amount += $invoice->item_amount;
            }
            
            if($invoice->activity_type == APConstants::SUBCRIBE_PHONE_ACCOUNT_ACTIVITY_TYPE){
                $invoice_tmp->phone_subscription_quantity += 1;
                $invoice_tmp->phone_subscription_amount += $invoice->item_amount;
            }
            
            if($invoice->activity_type == APConstants::SUBCRIBE_PHONE_NUMBER_ACTIVITY_TYPE){
                $invoice_tmp->setup_fee_quantity += 1;
                $invoice_tmp->setup_fee_amount += $invoice->item_amount;
            }
            
            if($invoice->activity_type == APConstants::RECURRING_PHONE_NUMBER_ACTIVITY_TYPE){
                $invoice_tmp->phone_recurring_quantity += 1;
                $invoice_tmp->phone_recurring_amount += $invoice->item_amount;
            }
            
            $invoice_tmp->total_invoice += $invoice->item_amount;
            $invoice_datas[$invoice->customer_id.'_' . $invoice->location_id] = $invoice_tmp;
            unset($invoice_tmp);
        }
        
        // open transaction to update invoice data.
        ci()->phone_call_history_m->db->trans_begin();
        
        $total_invoice = 0;
        foreach($invoice_datas as $data){
            $total_invoice = $data->incomming_amount + $data->outcomming_amount + $data->phone_subscription_amount;
            $total_invoice += $data->phone_recurring_amount;
            
            ci()->phone_invoice_by_location_m->update_by_many(array(
                'parent_customer_id' => $parent_customer_id,
                'invoice_month' => APUtils::getCurrentYearMonth(),
                "location_id" => $data->location_id,
                "customer_id" => $data->customer_id,
            ), array(
                "incomming_quantity" => $data->incomming_quantity,
                "incomming_amount" => $data->incomming_amount,
                
                "outcomming_quantity" => $data->outcomming_quantity,
                "outcomming_amount" => $data->outcomming_amount,
                
                "phone_subscription_quantity" => $data->phone_subscription_quantity,
                "phone_subscription_amount" => $data->phone_subscription_amount,
                
                "phone_recurring_quantity" => $data->phone_recurring_quantity,
                "phone_recurring_amount" => $data->phone_recurring_amount,
                
                "setup_fee_quantity" => $data->setup_fee_quantity,
                "setup_fee_amount" => $data->setup_fee_amount,
                
                "vat" => $customer_vat->rate,
                "vat_case" => $customer_vat->vat_case_id,
                
                "total_invoice" => $total_invoice
            ));
        }
        
        // commit transaction
        if(ci()->phone_call_history_m->db->trans_status() == FALSE){
            $message = ci()->phone_call_history_m->db->_error_message();
            throw new DAOException($message);
        }else{
            ci()->phone_call_history_m->db->trans_commit();
        }
    }
    
    /**
     * insert new activity to phone invoice detail.
     * 
     * @param type $activity
     * @param type $quantity
     * @param type $amount
     * @param type $activity_date
     * @param type $customer_id
     * @param type $location_id
     * @param type $update_invoice_summary_flag
     */
    public static function insertActivityPhoneInvoiceDetail($parent_customer_id, $customer_id, $activity, $activity_type, $quantity, $amount, 
                        $activity_date, $update_invoice_summary_flag = true, $phone_number, $reference ){
        ci()->load->model(array(
            "phones/phone_call_history_m",
            "phones/phone_number_m",
            'settings/currencies_m',
            'invoices/invoice_summary_m',
            "phones/phone_invoice_by_location_m",
            "phones/phone_invoice_detail_m"
        ));
        
        // invoice summary check
        $invoice_summary = ci()->invoice_summary_m->get_by_many(array(
            'customer_id' => $customer_id,
            'invoice_month' => APUtils::getCurrentYearMonth()
        ));
        
        // gets vat of customer
        $vat = APUTils::getVatRateOfCustomer($customer_id);
        $phone_vat = APUTils::getVatRateOfDigitalGoodBy($customer_id);
        
        
        // gets invoice code from invoice summary
        if(empty($invoice_summary) ){
            $invoice_code = APUtils::generateInvoiceCodeById('');
            ci()->invoice_summary_m->insert(array(
                'customer_id' => $customer_id,
                'invoice_month' => APUtils::getCurrentYearMonth(),
                "total_invoice" => 0,
                "vat_case" => $vat->vat_case_id,
                "vat" => $vat->rate,
                'invoice_code' => $invoice_code
            ));
        }else{
            $invoice_code = $invoice_summary->invoice_code;
        }
        
        // Gets location id
        $location_id = APContext::getLocationIDUserPhone($parent_customer_id, $customer_id);

        // Gets invoice summary
        $invoice_phone_summary_check = ci()->phone_invoice_by_location_m->get_by_many(array(
            'customer_id' => $customer_id,
            'invoice_month' => APUtils::getCurrentYearMonth(),
            "location_id" => $location_id,
            "parent_customer_id" => $parent_customer_id,
        ));
        if(empty($invoice_phone_summary_check)){
            $invoice_summary_id = ci()->phone_invoice_by_location_m->insert(array(
                'customer_id' => $customer_id,
                'invoice_month' => APUtils::getCurrentYearMonth(),
                "location_id" => $location_id,
                "parent_customer_id" => $parent_customer_id,
                "invoice_code" => $invoice_code,
                "total_invoice" => 0,
                "vat" => $phone_vat->rate,
                "vat_case" => $phone_vat->vat_case_id,
                "created_date" => time()
            ));
        }else{
            $invoice_summary_id = $invoice_phone_summary_check->id;
        }

        // check insert call invoice_detail
        /**
        $invoice_check = ci()->phone_invoice_detail_m->get_by_many(array(
            "customer_id" => $customer_id,
            "parent_customer_id" => $parent_customer_id,
            "activity" => $activity,
            "activity_date" => $activity_date,
            "location_id" => $location_id
        ));
        */

        ci()->phone_invoice_detail_m->insert(array(
            "customer_id" => $customer_id,
            "parent_customer_id" => $parent_customer_id,
            "activity" => $activity,
            "activity_type" => $activity_type,
            "activity_date" => $activity_date,
            "location_id" => $location_id,
            "item_number" => $quantity,
            "item_amount" => $amount,
            'create_date' => time(),
            'invoice_summary_id' => $invoice_summary_id,
            'phone_number' => $phone_number,
            'reference' => $reference
        ));
        
        // calculate invoice summary of phone.
        if($update_invoice_summary_flag){
            self::calculatePhoneInvoiceSummary($customer_id);
        }
        
        return true;
    }
    
    /**
     * Get next phone code of customer with format C00037802_PHN05
     * @param type $customer_id
     */
    public static function getNextPhoneCode($customer_id) {
        ci()->load->model(array(
            "phones/phone_number_m"
        ));
        $phone_code = sprintf('C%1$08d', $customer_id);
        $max_phone_code = ci()->phone_number_m->get_max_phone_code($customer_id);
        $phone_count = $max_phone_code + 1;
        $phone_code = $phone_code . '_PHN'. sprintf('%1$02d', $phone_count);
        return $phone_code;
    }
    
    /**
     * Delete phones object by id.
     * 
     * @param type $id
     * @param type $customer_id
     */
    public static function deletePhonesById($id, $parent_customer_id) {
        ci()->load->model(array(
            "phones/phone_m"
        ));
        ci()->load->library(array(
            "customers/customers_api",
            "phones/sonetel"
        ));
        $phone = ci()->phone_m->get_by_many(array(
            'id' => $id,
            'parent_customer_id' => $parent_customer_id
        ));
        if (empty($phone)) {
            return false;
        }
        $phone_id = $phone->phone_id;
        $customer_id = $phone->customer_id;
        
        // Get phone user id
        $phone_user_id = customers_api::getUserPhoneIdByCLUserId($parent_customer_id, $customer_id);
        $account_id = APContext::getSubAccountId($parent_customer_id);
        if (!empty($phone_id)) {
            ci()->sonetel->delete_phones($account_id, $phone_user_id, $phone_id);
        }
        
        // Delete phone in database
        ci()->phone_m->delete_by_many(array(
            'id' => $id,
            'parent_customer_id' => $parent_customer_id
        ));
        return true;
    }
    
    /**
     * Sync phone number price from Sonetel to [pricing_phones_number_latest] table
     */
    public static function sync_phone_number_price() {
        ci()->load->model(array(
            "phones/pricing_phones_number_latest_m"
        ));
        ci()->load->library(array(
            "phones/sonetel"
        ));
        
        // Get all phone number price from sonetel
        $all_prices_object = ci()->sonetel->get_phone_number_price();
        if (empty($all_prices_object)) {
            return;
        }
        
        // Get all response
        $all_prices_response = $all_prices_object->response;
        if (empty($all_prices_response) || count($all_prices_response) == 0) {
            return;
        }
        
        // Sync to database
        $created_date = now();
        ci()->pricing_phones_number_latest_m->truncate();
        foreach ($all_prices_response as $item) {
            $phonenumbers = $item->phonenumbers;
            if (empty($phonenumbers) || count($phonenumbers) == 0) {
                continue;
            }
            
            // For each item in phone number
            $phonenumber = $phonenumbers[0];
            $phonenumber_prices = $phonenumber->price;
            if (empty($phonenumber_prices) || count($phonenumber_prices) == 0) {
                continue;
            }
            
            // Delete all old data
            $country_code_3 = $item->country;
            // Insert new data
            foreach ($phonenumber_prices as $phonenumber_price) {
                ci()->pricing_phones_number_latest_m->insert(array(
                    "country_code_3" =>  $country_code_3,
                    "type" => $phonenumber->type,
                    "currency" => $phonenumber_price->currency,
                    "range" => $phonenumber_price->range,
                    "price_category"  => $phonenumber_price->price_category,
                    "one_time_fee"  => $phonenumber_price->one_time_fee,
                    "recurring_fee"  => $phonenumber_price->recurring_fee,
                    "recurrence_interval"  => $phonenumber_price->recurrence_interval,
                    "per_call_fee"  => $phonenumber_price->per_call_fee,
                    "per_min_fee"  => $phonenumber_price->per_min_fee,
                    "charge_interval"  => $phonenumber_price->charge_interval,
                    "per_sms_fee"  => $phonenumber_price->per_sms_fee,
                    "per_fax_fee"  => $phonenumber_price->per_fax_fee,
                    "created_date" => $created_date,
                    "last_sync_date" => $created_date
                ));
            }
        }
    }
    
    /**
     * Apply new phone number price from table [pricing_phones_number_latest] to [pricing_phones_number] and [pricing_phones_number_customer]
     */
    public static function apply_phone_number_price() {
        ci()->load->model(array(
            "phones/pricing_phones_number_m",
            "phones/pricing_phones_number_latest_m",
            "phones/pricing_phones_number_customer_m"
        ));
        
        // Get all records from [pricing_phones_number_latest_m]
        $default_range = 1;
        $default_currency = 'EUR';
        $detault_price_category = 'regular';
        
        $all_prices = ci()->pricing_phones_number_latest_m->get_many_by_many(array(
            "range" => $default_range,
            "currency" => $default_currency,
            "price_category" => $detault_price_category
        ));
        
        // For each records
        if (empty($all_prices) || count($all_prices) == 0) {
            return;
        }
        $created_date = now();
        foreach($all_prices as $price) {
            // Sync to pricing_phones_number
            $pricing_phones_number_check = ci()->pricing_phones_number_m->get_by_many(array(
                "country_code_3" => $price->country_code_3,
                "type" => $price->type,
                "range" => $price->range,
                "currency" => $price->currency,
                "price_category" => $price->price_category,
                "recurrence_interval" => $price->recurrence_interval
            ));
            
            // Insert
            if (empty($pricing_phones_number_check)) {
                // Insert new
                $array_price = APUtils::convertObjectToArray($price);
                unset($array_price['id']);
                $array_price['created_date'] = $created_date;
                $array_price['one_time_fee_upcharge'] = 2; // 2 EUR
                $array_price['recurring_fee_upcharge'] = 20; // 20%
                $array_price['per_call_fee_upcharge'] = 0;
                $array_price['per_min_fee_upcharge'] = 20; // 20%
                $array_price['per_sms_fee_upcharge'] = 0;
                $array_price['per_fax_fee_upcharge'] = 0;
                ci()->pricing_phones_number_m->insert($array_price);
            }
            // Update
            else {
                // Update general
                ci()->pricing_phones_number_m->update_by_many(array(
                    "country_code_3" => $price->country_code_3,
                    "type" => $price->type,
                    "range" => $price->range,
                    "currency" => $price->currency,
                    "price_category" => $price->price_category,
                    "recurrence_interval" => $price->recurrence_interval
                ), array(
                    "one_time_fee" => $price->one_time_fee,
                    "recurring_fee" => $price->recurring_fee,
                    "per_call_fee" => $price->per_call_fee,
                    "per_min_fee" => $price->per_min_fee,
                    "per_sms_fee" => $price->per_sms_fee,
                    "per_fax_fee" => $price->per_fax_fee,
                    "charge_interval" => $price->charge_interval,
                    "last_modified_date" => $created_date,
                    "last_sync_date" => $price->last_sync_date,
                    "is_latest_fee" => $price->is_latest_fee
                ));
                
                // Update general
                ci()->pricing_phones_number_customer_m->update_by_many(array(
                    "country_code_3" => $price->country_code_3,
                    "type" => $price->type,
                    "range" => $price->range,
                    "currency" => $price->currency,
                    "price_category" => $price->price_category,
                    "recurrence_interval" => $price->recurrence_interval
                ), array(
                    "one_time_fee" => $price->one_time_fee + $price->one_time_fee_upcharge,
                    "recurring_fee" => $price->recurring_fee * ( 1 + $price->recurring_fee_upcharge / 100),
                    "per_call_fee" => $price->per_call_fee * ( 1 + $price->per_call_fee_upcharge / 100),
                    "per_min_fee" => $price->per_min_fee + $price->per_min_fee_upcharge,
                    "per_sms_fee" => $price->per_sms_fee * ( 1 + $price->per_sms_fee_upcharge / 100),
                    "per_fax_fee" => $price->per_fax_fee * ( 1 + $price->per_fax_fee_upcharge / 100),
                    "charge_interval" => $price->charge_interval,
                    "last_modified_date" => $created_date,
                    "last_sync_date" => $price->last_sync_date,
                    "is_latest_fee" => $price->is_latest_fee
                ));
            }
        }
    }
    
    /**
     * Apply default phone number price from table [pricing_phones_number] to [pricing_phones_number_customer]
     */
    public static function init_phone_number_price($customer_id) {
        ci()->load->model(array(
            "phones/pricing_phones_number_m",
            "phones/pricing_phones_number_customer_m"
        ));
        
        // Get all records from [pricing_phones_number_latest_m]
        $default_range = 1;
        $default_currency = 'EUR';
        $detault_price_category = 'regular';
        
        $all_prices = ci()->pricing_phones_number_m->get_many_by_many(array(
            "range" => $default_range,
            "currency" => $default_currency,
            "price_category" => $detault_price_category
        ));
        
        // For each records
        if (empty($all_prices) || count($all_prices) == 0) {
            return;
        }
        $created_date = now();
        foreach($all_prices as $price) {
            // Insert new
            $array_price = APUtils::convertObjectToArray($price);
            unset($array_price['id']);
            
            $array_price['one_time_fee'] = $price->one_time_fee + $price->one_time_fee_upcharge;
            $array_price['recurring_fee'] = $price->recurring_fee * ( 1 + $price->recurring_fee_upcharge / 100);
            $array_price['per_call_fee'] = $price->per_call_fee * ( 1 + $price->per_call_fee_upcharge / 100);
            $array_price['per_min_fee'] = $price->per_min_fee + $price->per_min_fee_upcharge;
            $array_price['per_sms_fee'] = $price->per_sms_fee * ( 1 + $price->per_sms_fee_upcharge / 100);
            $array_price['per_fax_fee'] = $price->per_fax_fee * ( 1 + $price->per_fax_fee_upcharge / 100);
            
            $array_price['one_time_fee_upcharge'] = 0; // 2 EUR
            $array_price['recurring_fee_upcharge'] = 0; // 20%
            $array_price['per_call_fee_upcharge'] = 0;
            $array_price['per_min_fee_upcharge'] = 0; // 0 EUR
            $array_price['per_sms_fee_upcharge'] = 0;
            $array_price['per_fax_fee_upcharge'] = 0;
            
            $array_price['customer_id'] = $customer_id;
            $array_price['created_date'] = $created_date;
            ci()->pricing_phones_number_customer_m->insert($array_price);
        }
    }
    
    /**
     * Sync phone number price from Sonetel to [pricing_phones_number_latest] table
     */
    public static function sync_outboundcalls_price() {
        ci()->load->model(array(
            "phones/pricing_phones_outboundcalls_latest_m"
        ));
        ci()->load->library(array(
            "phones/sonetel"
        ));
        
        // Get all phone number price from sonetel
        $all_prices_object = ci()->sonetel->get_outboundcalls_price();
        if (empty($all_prices_object)) {
            return;
        }
        
        // Get all response
        $all_prices_response = $all_prices_object->response;
        if (empty($all_prices_response) || count($all_prices_response) == 0) {
            return;
        }
        
        // Sync to database
        $created_date = now();
        ci()->pricing_phones_outboundcalls_latest_m->truncate();
        foreach ($all_prices_response as $item) {
            // Delete all old data
            $country_code_3 = $item->country;
            // Insert new data
            ci()->pricing_phones_outboundcalls_latest_m->insert(array(
                "country_code_3" =>  $country_code_3,
                "pricing_name" => $item->name,
                "currency" => $item->currency,
                "per_call_fee" => $item->per_call_fee,
                "usage_fee" => $item->usage_fee,
                "charge_interval" => $item->charge_interval,
                "price_plan" => $item->price_plan,
                "dialcode_list" => $item->dialcode_list,
                "created_date" => $created_date,
                "last_sync_date" => $created_date
            ));
        }
    }
    
    /**
     * Apply new phone number price from table [pricing_phones_outboundcalls_latest] to [pricing_phones_outboundcalls] and [pricing_phones_outboundcalls_customer]
     */
    public static function apply_outboundcalls_price() {
        ci()->load->model(array(
            "phones/pricing_phones_outboundcalls_m",
            "phones/pricing_phones_outboundcalls_latest_m",
            "phones/pricing_phones_outboundcalls_customer_m"
        ));
        
        // Get all records from [pricing_phones_number_latest_m]
        $default_currency = 'EUR';
        $detault_price_plan = 'regular';
        
        $all_prices = ci()->pricing_phones_outboundcalls_latest_m->get_many_by_many(array(
            "currency" => $default_currency,
            "price_plan" => $detault_price_plan
        ));
        
        // For each records
        if (empty($all_prices) || count($all_prices) == 0) {
            return;
        }
        $created_date = now();
        foreach($all_prices as $price) {
            // Sync to pricing_phones_number
            $pricing_phones_outboundcalls_check = ci()->pricing_phones_outboundcalls_m->get_by_many(array(
                "country_code_3" => $price->country_code_3,
                "currency" => $price->currency,
                "price_plan" => $price->price_plan,
                "pricing_name" => $price->pricing_name,
            ));
            
            // Insert
            if (empty($pricing_phones_outboundcalls_check)) {
                // Insert new
                $array_price = APUtils::convertObjectToArray($price);
                unset($array_price['id']);
                $array_price['created_date'] = $created_date;
                $array_price['per_call_fee_upcharge'] = 0; // 0 EUR
                $array_price['usage_fee_upcharge'] = 20; // 20%
                ci()->pricing_phones_outboundcalls_m->insert($array_price);
            }
            // Update
            else {
                // Update general
                ci()->pricing_phones_outboundcalls_m->update_by_many(array(
                    "country_code_3" => $price->country_code_3,
                    "currency" => $price->currency,
                    "price_plan" => $price->price_plan,
                    "pricing_name" => $price->pricing_name,
                ), array(
                    "per_call_fee" => $price->per_call_fee,
                    "usage_fee" => $price->usage_fee,
                    "charge_interval" => $price->charge_interval,
                    "last_modified_date" => $created_date,
                    "last_sync_date" => $price->last_sync_date,
                    "is_latest_fee" => $price->is_latest_fee
                ));
                
                // Update general
                ci()->pricing_phones_outboundcalls_customer_m->update_by_many(array(
                    "country_code_3" => $price->country_code_3,
                    "currency" => $price->currency,
                    "price_plan" => $price->price_plan,
                    "pricing_name" => $price->pricing_name,
                ), array(
                    "per_call_fee" => $price->per_call_fee + $price->per_call_fee_upcharge,
                    "usage_fee" => $price->usage_fee * (1 + $price->usage_fee_upcharge / 100 ),
                    "charge_interval" => $price->charge_interval,
                    "last_modified_date" => $created_date,
                    "last_sync_date" => $price->last_sync_date,
                    "is_latest_fee" => $price->is_latest_fee
                ));
            }
        }
    }
    
    /**
     * Apply default phone number price from table [pricing_phones_outboundcalls] to [pricing_phones_outboundcalls_customer]
     */
    public static function init_customer_outboundcalls_price($customer_id) {
        ci()->load->model(array(
            "phones/pricing_phones_outboundcalls_m",
            "phones/pricing_phones_outboundcalls_customer_m"
        ));
        
        // Get all records from [pricing_phones_number_latest_m]
        $default_currency = 'EUR';
        $detault_price_plan = 'regular';
        
        $all_prices = ci()->pricing_phones_outboundcalls_m->get_many_by_many(array(
            "currency" => $default_currency,
            "price_plan" => $detault_price_plan
        ));
        
        // For each records
        if (empty($all_prices) || count($all_prices) == 0) {
            return;
        }
        $created_date = now();
        foreach($all_prices as $price) {
            // Insert new
            $array_price = APUtils::convertObjectToArray($price);
            unset($array_price['id']);
            
            $array_price['per_call_fee'] = $price->per_call_fee + $price->per_call_fee_upcharge;
            $array_price['usage_fee'] = $price->usage_fee * (1 + $price->usage_fee_upcharge / 100 );
            
            $array_price['per_call_fee_upcharge'] = 0; // 0 EUR
            $array_price['usage_fee_upcharge'] = 0; // 20%
            
            $array_price['customer_id'] = $customer_id;
            $array_price['created_date'] = $created_date;
            ci()->pricing_phones_outboundcalls_customer_m->insert($array_price);
        }
    }
    
    /**
     * get phone pricing caluclator.
     */
    public static function get_phone_pricing($minutes, $number, $country_code, $area_code, $country_forwarding){
        ci()->load->model(array(
            'phones/pricing_phones_number_m',
            'phones/pricing_phones_outboundcalls_m',
        ));
        
        // get normal forwarding pricing.
        $list_forwarding = ci()->pricing_phones_outboundcalls_m->get_by_many(array(
            "country_code_3" => $country_forwarding
        ));
        
        // get all pricing plan
        $price_plan = ci()->pricing_phones_number_m->get_by_many(array(
            "country_code_3" => $country_code
        ));
        
        $upcharge = !empty($list_forwarding)? (1 + ($list_forwarding->usage_fee_upcharge/ 100)) : 1;
        
        $result = array();
        $result['setup_fee'] = !empty($price_plan)? $price_plan->one_time_fee : 0;
        $result['monthly_fee'] = !empty($price_plan)? $price_plan->recurring_fee : 0;
        $result['call_forwarding_fee'] = !empty($list_forwarding)? $minutes * $list_forwarding->usage_fee * $upcharge : 0;
        $result['estimated_cost'] = $result['setup_fee'] + $result['monthly_fee']  + $result['call_forwarding_fee'];
        $result['currency'] = "EUR";
        
        return $result;
    }
    
    /**
     * Gets all phone country.
     */
    public static function get_phone_country(){
        ci()->load->model('settings/countries_m');
        $list_country = ci()->countries_m->get_many_by_many(array(
            "country_code_3 <> ''" => null,
            "country_code_3 is not null" => null
        ));
        
        return $list_country;
    }
    
    /**
     * Gets area code by country.
     * @param type $country_code
     * @return \stdClass
     */
    public static function get_phone_area_code($country_code){
        ci()->load->model('settings/countries_m');
        ci()->load->model('phones/phone_area_code_m');
        
        // Get list country
        $country = ci()->countries_m->get_by('country_code_3', $country_code);
        $country_id = $country->id;
        $list_area = ci()->phone_area_code_m->get_many_by_many(array(
            'country_id' => $country_id
        ));
        $list_data = array();
        foreach ($list_area as $item) {
            $obj = new stdClass();
            $obj->key = $item->area_code;
            $obj->label = $item->area_name;
            $list_data[] = $obj;
        }
        
        return $list_data;
    }
    
    /**
     * Gets list phone number by country and area.
     * @param type $country_code
     * @param type $area_code
     */
    public static function get_list_phone_number_by($country_code, $area_code){
        ci()->load->library(array(
            'phones/sonetel'
        ));
        
        $list_data = array();
        $list_result = ci()->sonetel->get_list_available_phonenumber($country_code, $area_code);
        if (empty($list_result) || $list_result->status != 'success') {
            return $list_data;
        }

        $list_response = $list_result->response;
        if ($list_response == 'No entries found') {
            return $list_data;
        }

        foreach ($list_response as $row) {
            $obj = new stdClass();
            $obj->key = $row->phnum;
            $obj->label = $row->phnum;
            $list_data[] = $obj;
        }
        
        return $list_data;
    }
    
    /**
     * Create new voice app.
     * 
     * @param type $app_name
     * @param type $app_type
     * @param type $addional_setting
     */
    public static function create_new_voiceapp($parent_customer_id, $app_name, $app_type, $addional_setting) {
        ci()->load->library('phones/sonetel');
        ci()->lang->load('account/user');
        ci()->load->model('phones/phone_voiceapp_m');
        ci()->load->library('account/account_api');
        $account_id = APContext::getSubAccountId($parent_customer_id);
        if (empty($account_id)) {
            $account_id = account_api::create_sub_account($parent_customer_id);
            // Check empty
            if (empty($account_id)) {
                return false;
            }
        }
                
        // Create new user
        try {
            $app_id = ci()->sonetel->create_new_voiceapp($account_id, $app_name, $app_type, $addional_setting);
        } catch (Exception $e) {
            // $message = lang('users.message.add_voiceapp_fail_02');
            return false;
        }

        $data = array(
            "sub_account_id" => $account_id,
            "parent_customer_id" => $parent_customer_id,
            "name" => $app_name,
            "app_id" => $app_id,
            "app_type" => $app_type,
            "use_flag" => APConstants::OFF_FLAG,
            "data_setting" => json_encode($addional_setting),
            "created_date" => time()
        );

        // save voice app.
        $id = ci()->phone_voiceapp_m->insert($data);
        return $id;
    }
    
    /**
     * Delete voice app
     * @param type $parent_customer_id
     * @param type $id
     * @return boolean
     */
    public static function delete_voiceapp($parent_customer_id, $id = '') {
        ci()->load->library('phones/sonetel');
        ci()->load->model('phones/phone_voiceapp_m');
        $phone_user_voiceapp = ci()->phone_voiceapp_m->get_by_many(array(
            'parent_customer_id' => $parent_customer_id,
            'id' => $id
        ));
        if (empty($phone_user_voiceapp)) {
            return false;
        }
        // Check exist sub account of this customer
        $account_id = APContext::getSubAccountId($parent_customer_id);
        // Delete
        try {
            ci()->sonetel->delete_voiceapp($account_id, $phone_user_voiceapp->app_id);
        } catch (Exception $e) {
            return false;
        }
        ci()->phone_voiceapp_m->delete_by_many(array(
            'parent_customer_id' => $parent_customer_id,
            'id' => $id
        ));
        return true;
    }
    
    public static function delete_phone($parent_customer_id, $clevver_target_id = '') {
        // Get target id
        ci()->load->library('phones/sonetel');
        ci()->load->model('phones/phone_target_m');
        ci()->load->model('phones/phone_m');
        ci()->load->model('customers/phone_customer_user_m');
        
        $phone = ci()->phone_m->get_by_many(array(
            'parent_customer_id' => $parent_customer_id,
            'target_id' => $clevver_target_id
        ));
        if (empty($phone)) {
            return false;
        }
        
        $customer_id = $phone->customer_id;
        $phone_id = $phone->phone_id;
        if (!empty($phone_id) && !empty($customer_id)) {
            $phone_user = ci()->phone_customer_user_m->get_by_many(array(
                "parent_customer_id" => $parent_customer_id,
                "customer_id" => $customer_id
            ));
            
            $phone_user_id = !empty($phone_user) ? $phone_user->phone_user_id : '';
            if (!empty($phone_user_id)) {
                // Check exist sub account of this customer
                $account_id = APContext::getSubAccountId($parent_customer_id);
                ci()->sonetel->delete_phones($account_id, $phone_user_id, $phone_id);
            }
        }
        ci()->phone_m->delete_by_many(array(
            'parent_customer_id' => $parent_customer_id,
            'target_id' => $clevver_target_id
        ));
        return true;
    }
    
    /**
     * Get number interval based one one year contract.
     * 
     * @param type $recurrence_interval
     */
    public static function getNumberIntervalOneYearContract($recurrence_interval) {
        if ($recurrence_interval == '15d') {
            return 24;
        } else if ($recurrence_interval == '1m') {
            return 12;
        } else if ($recurrence_interval == '3m') {
            return 4;
        } else if ($recurrence_interval == '6m') {
            return 2;
        } else if ($recurrence_interval = '1y') {
            return 1;
        } else if ($recurrence_interval == '3y') {
            return 1/3;
        } else {
            return 1;
        }
    }
    
    /**
     * calculate the recurring fee for phone number
     */
    public static function autoCalculateRecurringFeePhoneNumber() {
        ci()->load->model('phones/pricing_phones_number_m');
        ci()->load->model('phones/pricing_phones_number_customer_m');
        ci()->load->model('phones/phone_number_m');
        ci()->load->library('phones/sonetel');
        ci()->load->model(array(
            'invoices/invoice_summary_m',
            "phones/phone_invoice_by_location_m",
            "phones/phone_invoice_detail_m"
        ));
        
        $current_date = now();
        // Get all phone number still have contract
        $all_phone_number = ci()->phone_number_m->get_many_by_many(array(
            "end_contract_date >= " => $current_date
        ));
        
        // For each phone
        if (empty($all_phone_number) || count($all_phone_number) == 0) {
            return;
        }
        
        // Foreach record
        foreach($all_phone_number as $item) {
            $parent_customer_id = $item->parent_customer_id;
            $customer_id = $item->customer_id;
            $phone_number = $item->phone_number;
            $end_contract_date = $item->end_contract_date;
            $country_code = $item->country_code;
            
            // Check existing current charge
            $phone_invoice_detail = ci()->phone_invoice_detail_m->get_by_many(array(
               'parent_customer_id' =>  $parent_customer_id,
               'customer_id' =>  $customer_id,
               'phone_number' => $phone_number,
               'activity_type' => APConstants::RECURRING_PHONE_NUMBER_ACTIVITY_TYPE,
               'reference' => date("Ymd", $end_contract_date)
            ));
            if (!empty($phone_invoice_detail)) {
                continue;
            }
            
            // Insert new charge fee
            $account_id = APContext::getSubAccountId($parent_customer_id);
            if (empty($account_id)) {
                continue;
            }
            $response_obj = ci()->sonetel->get_phonenumbersubscription_byphonenumber($account_id, $phone_number);
            // $base_one_time_fee = $response_obj->one_time_fee;
            $base_recurring_fee = $response_obj->recurring_fee;
            $recurrence_interval  = $response_obj->recurrence_interval;
            $number_interval_one_year = phones_api::getNumberIntervalOneYearContract($recurrence_interval);

            // Get clevvermail upcharge value
            $one_time_fee_cl_upcharge = 0;
            $recurring_fee_cl_upchage = 0;
            $price_phone_number = ci()->pricing_phones_number_m->get_by('country_code_3', $country_code);
            if (!empty($price_phone_number)) {
                $one_time_fee_cl_upcharge = $price_phone_number->one_time_fee_upcharge;
                $recurring_fee_cl_upchage = $price_phone_number->recurring_fee_upcharge;
            }

            // Get enterprise upchage value
            $one_time_fee_enterprise_upcharge = 0;
            $recurring_fee_enterprise_upchage = 0;
            if (APContext::isEnterpriseCustomerByID($parent_customer_id)) {
                $price_phone_number_enterprise = ci()->pricing_phones_number_customer_m->get_by_many(array(
                    'country_code_3' => $country_code,
                    'customer_id' => $parent_customer_id
                ));
                if (!empty($price_phone_number_enterprise)) {
                    $one_time_fee_enterprise_upcharge = $price_phone_number_enterprise->one_time_fee_upcharge;
                    $recurring_fee_enterprise_upchage = $price_phone_number_enterprise->recurring_fee_upcharge;
                }
            }

            // $one_time_fee = $base_one_time_fee + $one_time_fee_cl_upcharge + $one_time_fee_enterprise_upcharge;
            $recurring_fee = ($base_recurring_fee * (1  + $recurring_fee_cl_upchage / 100)) * (1 + $recurring_fee_enterprise_upchage / 100);
            $total_recurring_fee = $recurring_fee * $number_interval_one_year;
            
            $activity_date = APUtils::getCurrentYearInvoice() . APUtils::getCurrentMonthInvoice() . APUtils::getCurrentDayInvoice();
            $reference = date("Ymd", $end_contract_date);
            phones_api::insertActivityPhoneInvoiceDetail($parent_customer_id, $customer_id, APConstants::RECURRING_PHONE_NUMBER_AT,
                APConstants::RECURRING_PHONE_NUMBER_ACTIVITY_TYPE, 1, $total_recurring_fee, $activity_date, true, $phone_number, $reference);
                
        }
    }
    
    /**
     * Auto extend phone number contract
     */
    public static function autoExtendPhoneNumberContract() {
        ci()->load->model('phones/phone_number_m');
        $current_date = now();
            
        ci()->phone_number_m->update_by_many(array(
            "end_contract_date < " => $current_date,
            "auto_renewal" => APConstants::ON_FLAG
        ), array(
            "end_contract_date = end_contract_date + 365 * 24 * 60 * 60" => null
        ));
    }
    
    /**
     * get all countries support phones
     */
    public static function get_all_countries(){
        ci()->load->model('settings/countries_m');
        $all_countries = ci()->countries_m->get_many_by_many(array(
            'is_support_phonenumber' => APConstants::ON_FLAG
        ));
        return $all_countries;
    } 
    
    /**
     * sync phone area code.
     */
    public static function sync_phone_area_code(){
        ci()->load->library("phones/sonetel");
        ci()->load->model('settings/countries_m');
        ci()->load->model('phones/phone_area_code_latest_m');
        
        // Truncatre table phone_area_code_latest
        ci()->phone_area_code_latest_m->truncate();
        
        // Get all countries support
        $list_support_countries = ci()->sonetel->get_countries();
        if(!empty($list_support_countries->response) && $list_support_countries->response != 'No entries found'){
            $stmt_update = 'UPDATE country SET is_support_phonenumber = 0';
            ci()->countries_m->db->query($stmt_update);
            foreach($list_support_countries->response as $country){
                $country_code = $country->country;
                ci()->countries_m->update_by_many(array(
                    'country_code_3' => $country_code
                ), array(
                    'is_support_phonenumber' => APConstants::ON_FLAG
                ));
            }
        }
        $all_countries = ci()->countries_m->get_many_by_many(array(
            'is_support_phonenumber' => APConstants::ON_FLAG
        ));
        foreach($all_countries as $country){
            $country_code = $country->country_code_3;
            if(!empty($country_code)){
                $list_phones = ci()->sonetel->get_availble_phonenumber($country_code);
                if(!empty($list_phones->response) && $list_phones->response != 'No entries found'){
                    $insert_data = array();
                    foreach($list_phones->response as $phonenumber){
                        $insert_data[] = array(
                            "country_id" => $country->id,
                            "area_code" => $phonenumber->area_code,
                            "area_name" => $phonenumber->city
                        );
                    }
                    
                    // insert into phone area latest.
                    ci()->phone_area_code_latest_m->insert_many($insert_data);
                    unset($insert_data);
                }
            }
        }
        
        // sync with phone area table
        $stmt = " INSERT INTO phone_area_code (country_id, area_code, area_name)
                (
                SELECT p1.country_id, p1.area_code, p1.area_name FROM phone_area_code_latest p1
                LEFT JOIN phone_area_code p2 ON p1.country_id=p2.country_id AND p1.area_code=p2.area_code
                WHERE p2.id is null and p1.area_code <> ''
                GROUP BY p1.country_id, p1.area_code
                )";
        ci()->phone_area_code_latest_m->db->query($stmt);
    }
}