<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Admin controller for the customer module
 */
class Admin extends Admin_Controller
{

    /**
     * Constructor method
     */
    public function __construct ()
    {
        parent::__construct();
    }

    /**
     * List all customer
     */
    public function index ()
    {
        // Display the current page
        $this->template->set('header_title', 'Paypal IPN Manual')->build('admin/paypal_ipn_manual');
    }
}