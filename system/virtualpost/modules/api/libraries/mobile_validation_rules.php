<?php defined('BASEPATH') or exit('No direct script access allowed');

class mobile_validation_rules extends MY_Controller
{
    /**
     *add incomming validation rule.
     * @var type 
     */
    public static $add_incomming_validation_rules = array(
        array(
            'field' => 'customer_id',
            'label' => 'customer ID',
            'rules' => 'trim|required|number'
        ),
        array(
            'field' => 'postbox_id',
            'label' => 'postbox ID',
            'rules' => 'trim|number|required|max_length[12]'
        ),
        array(
            'field' => 'from_customer_name',
            'label' => 'From Address',
            'rules' => 'trim|required|max_length[255]'
        ),
        array(
            'field' => 'type',
            'label' => 'Type',
            'rules' => 'trim|required|max_length[255]'
        ),
        array(
            'field' => 'labelValue',
            'label' => 'labelValue',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'weight',
            'label' => 'Weight',
            'rules' => 'trim|required|max_length[10]|numeric|greater_than[0]'
        )
    );

    /**
     *
     * @var type edit customer rule
     */
    public static $edit_customer_rule = array(
        array(
            'field' => 'email',
            'label' => 'lang:email',
            'rules' => 'required|valid_email|max_length[255]|callback__check_email'
        ),
        array(
            'field' => 'status_flag',
            'label' => 'lang:status_flag',
            'rules' => 'required'
        ),
        array(
            'field' => 'charge_fee_flag',
            'label' => 'lang:charge_fee_flag',
            'rules' => 'required'
        ),
        array(
            'field' => 'location_id',
            'label' => 'lang:location_id',
            'rules' => 'required'
        ),
        array(
            'field' => 'invoice_type',
            'label' => 'lang:invoice_type',
            'rules' => 'required'
        ),
        array(
            'field' => 'required_verification_flag',
            'label' => 'Verification Address',
            'rules' => 'required'
        ),
        array(
            'field' => 'shipping_factor_fc',
            'label' => 'Customer based shipping factor FC',
            'rules' => ''
        ),
        array(
            'field' => 'required_prepayment_flag',
            'label' => 'Pre-Payment',
            'rules' => 'required'
        )
    );

    public static $change_customer_password_rule = array(
        array(
            'field' => 'password',
            'label' => 'lang:password',
            'rules' => 'required|trim|matches[repeat_password]|min_length[6]|max_length[255]'
        ),
        array(
            'field' => 'repeat_password',
            'label' => 'lang:repeat_password',
            'rules' => 'required|trim|min_length[6]|max_length[255]'
        )
    );
    
    public static $change_user_password_rule = array (
            array (
                    'field' => 'new_password',
                    'label' => 'lang:new_password',
                    'rules' => 'required|trim|matches[repeat_password]|min_length[6]|max_length[255]' 
            ),
            array (
                    'field' => 'repeat_password',
                    'label' => 'lang:repeat_password',
                    'rules' => 'required|trim|min_length[6]|max_length[255]' 
            ) 
    );
    
    public static $validation_add_partner_receipt_rules = array(
        array(
            'field' => 'partner_id',
            'label' => 'Partner',
            'rules' => 'required'
        ),
        array(
            'field' => 'location_id',
            'label' => 'Location',
            'rules' => 'required'
        ),
        array(
            'field' => 'date_of_receipt',
            'label' => 'Date Of Receipt',
            'rules' => 'required'
        ),
        array(
            'field' => 'amount',
            'label' => 'Net Amount',
            'rules' => 'required'
        ),
        array(
            'field' => 'description',
            'label' => 'Description',
            'rules' => 'required'
        )
    );
    
    
}