<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Code here is run before frontend controllers
 */
class Widget_Controller extends MY_Controller {
    /**
     * Loads the gazillion of stuff, in Flash Gordon speed.
     */
    public function __construct() {
        parent::__construct();
        
        // Load admin themes helper
        $this->load->helper('admin_theme');

        $frontend_theme = Settings::get(APConstants::WIDGET_THEMES_CODE);
        $web_path = APPPATH . 'themes/' . $frontend_theme . '/';
        
        // Set the location of assets
        Asset::add_path('theme', $web_path);
        Asset::set_path('theme');
        
        // Template configuration
        $this->template->enable_parser(FALSE)->set_theme($frontend_theme)->set_layout('default', '');
    }
}
