<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

/**
 * Admin controller for the users module
 */
class Widget extends Widget_Controller {
    
    /**
     * Constructor method
     */
    public function __construct() {
        parent::__construct ();
        
        // Load the required classes
        $this->load->library ( 'form_validation' );
        
        // load model.
        $this->load->model ( 'settings/terms_service_m' );
        $this->load->model ( 'partner/partner_marketing_profile_m' );
        
        // load language
        $this->lang->load ( 'widget' );
    }
    
    /**
     * List all users
     */
    public function index() {
        $p = $this->input->get_post('p','');
        $t = $this->input->get_post('t','');

        $partner = $this->partner_marketing_profile_m->get_by_many(array("token"=> $p));

        $this->template->set("partner_id", $partner->partner_id);
        $this->template->set("partner", $partner);
        $this->template->build ( 'index' );
    }
    
    /**
     * Display term of service
     */
    public function term_of_service() {
        $this->template->set_layout ( FALSE );
        
        $content = settings_api::getTermAndCondition();
        
        $this->template->set ( 'content', $content );
        $this->template->build ( 'view_content_inline' );
    }
}