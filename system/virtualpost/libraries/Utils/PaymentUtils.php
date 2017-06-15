<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PaymentUtils
{
    /**
     * verify expired date of credit card
     *
     * @param unknown $customer_id
     * @param unknown $payment_id
     */
    public static function changeCreditCardHasExpiredDate($customer_id, $payment_id, $to_email, $from_email, $data)
    {
        // Load model
        ci()->load->model('payment/payment_m');
        ci()->load->model('email/email_m');
        ci()->load->model('customers/customer_m');

        // change another credit card.
        // Gets another credit card.
        $creditCard = ci()->payment_m->get_by_many(
            array(
                "customer_id" => $customer_id,
                "payment_id <> {$payment_id}" => null
            ));

        // if there is no legal payment method, deactivate customer and send
        // mail
        // deactivated.
        if (!$creditCard) {
            // Deactive account
            ci()->customer_m->update_by_many(array(
                "customer_id" => $customer_id
            ),
                array(
                    "activated_flag" => APConstants::OFF_FLAG,
                    "payment_detail_flag" => APConstants::OFF_FLAG,
                    "last_updated_date" => now()
                ));
            // update: convert registration process flag to customer_product_setting.
            CustomerProductSetting::set($customer_id, APConstants::CLEVVERMAIL_PRODUCT, 'payment_detail_flag', APConstants::OFF_FLAG);

            // Send email confirm for user
            $data['slug'] = APConstants::deactived_customer_notification;
            $data['to_email'] = $to_email;
            // Send email
            MailUtils::sendEmailByTemplate($data);
        } else {
            // switch payment method if have. and send email "new payment method
            // selected as standard".
            ci()->payment_m->update_by_many(
                array(
                    "customer_id" => $customer_id,
                    "payment_id" => $payment_id
                ), array(
                "primary_card" => APConstants::OFF_FLAG
            ));

            ci()->payment_m->update_by_many(
                array(
                    "customer_id" => $customer_id,
                    "payment_id" => $creditCard->payment_id
                ), array(
                "primary_card" => APConstants::ON_FLAG
            ));

            $data['slug'] = APConstants::email_change_new_payment_method_standard;
            $data['to_email'] = $to_email;
            // Send email
            MailUtils::sendEmailByTemplate($data);
        }
    }
    
    /**
     * Get last payment information
     * @param type $customer_id
     */
    public static function get_last_payment_info($customer_id) {
        ci()->load->model('payment/payone_tran_hist_m');
        ci()->load->model('payment/external_tran_hist_m');
        
        // Get all payone status
        $array_condition = array(
            'customer_id' => $customer_id,
            "(txaction = 'paid')" => null
        );
        $payone_payment = ci()->payone_tran_hist_m->get_by_many_order($array_condition, array('last_update_date' => 'DESC'));
        $last_payment_date = $payone_payment != null ? $payone_payment->last_update_date : 0;
        $last_payment_type = $last_payment_date > 0 ? 'credit' : '';

        $external_array_condition = array(
            'customer_id' => $customer_id
        );
        $external_payment = ci()->external_tran_hist_m->get_by_many_order($external_array_condition, array('created_date' => 'DESC'));
        if (!empty($external_payment)) {
            if ($external_payment->created_date > $last_payment_date) {
                $last_payment_date = $external_payment->created_date;
                $last_payment_type = 'bank';
            }
        }
        return array('last_payment_date' => $last_payment_date, 'last_payment_type' => $last_payment_type);
    }
}