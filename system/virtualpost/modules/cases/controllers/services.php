<?php
if (! defined ( 'BASEPATH' ))
    exit ( 'No direct script access allowed' );
class Services extends CaseSystem_Controller {
    
    /**
     * Loads the gazillion of stuff, in Flash Gordon speed.
     *
     * @todo Document properly please.
     */
    public function __construct() {
        parent::__construct ();
    }
    public function index() {
        $case =  $this->input->get_post('case');
        if ($case == 'verification') {
        	redirect('cases/verification');
        }
        
        // list all
        $this->template->build ( 'services/index' );
    }
    public function start() {
        $page = $this->input->get_post ( 'p', '1' );
        
        switch ($page) {
            case "2" :
                $this->template->build ( 'services/page2' );
                break;
            case "3" :
                $this->template->build ( 'services/page3' );
                break;
            case "4" :
                $this->template->build ( 'services/page4' );
                break;
            case "5" :
                $this->template->build ( 'services/page5' );
                break;
            default :
                $this->template->build ( 'services/index' );
        }
    }
    
    /**
     * payment method.
     */
    public function payment() {
        $this->template->set_layout ( FALSE );
        
        $total = $this->input->get_post ( "total", 0 );
        $this->template->set ( 'amount', $total )->build ( "payment/payment" );
    }
    
    /**
     * bank tranfer payment method.
     */
    public function bank_tranfer() {
        $this->template->set_layout ( FALSE );
        $total = $this->input->get_post ( "total", 0 );
        $this->template->set ( 'amount', $total )->build ( "payment/bank_tranfer" );
    }
    
    /**
     * bank tranfer payment method.
     */
    public function paypal_payment() {
        $this->template->set_layout ( FALSE );
        $total = $this->input->get_post ( "total", 0 );
        $this->template->set ( 'amount', $total )->build ( "payment/paypal_payment" );
    }
    
    /**
     * payone payment.
     */
    public function payone_payment() {
        $this->template->set_layout ( FALSE );
        $total = $this->input->get_post ( "total", 0 );
        $this->template->set ( 'amount', $total )->build ( "payment/payone" );
    }
}