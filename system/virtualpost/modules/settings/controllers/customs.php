<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Admin controller for the settings module
 */
class Customs extends Admin_Controller {

    /**
     * Constructor method
     */
    public function __construct() {
        parent::__construct();

        // Load the required classes
        $this->load->model('settings/customs_matrix_m');
        $this->load->library('settings/settings_api');
    }

    /**
     * List all countries
     */
    public function index() {
        $from_country = $this->input->get_post("from_country");
        $to_country = $this->input->get_post("to_country");

        // All countries
        $from_country_filter = array();
        if (!empty($from_country)) {
            $from_country_filter['from_country'] = $from_country;
        }
        $from_countries = $this->customs_matrix_m->get_country_limit($from_country_filter, 'from_country', 0, 300);

        // To countries limit
        $to_country_filter = array();
        if (!empty($to_country)) {
            $to_country_filter['to_country'] = $to_country;
        }
        $to_countries = $this->customs_matrix_m->get_country_limit($to_country_filter, 'to_country', 0, 300);
        $list_country = $this->customs_matrix_m->get_country_limit(array(), 'from_country', 0, 300);

        $this->template->set('list_country', $list_country);
        $this->template->set('from_countries', $from_countries);
        $this->template->set('to_countries', $to_countries);
        $this->template->set('from_country', $from_country);
        $this->template->set('to_country', $to_country);

        // Search by country list
        if ($_POST) {
            // Get declare setting
            $from_list_country = array();
            if (!empty($from_country)) {
                $from_list_country[] = $from_country;
            }
            $to_list_country = array();
            if (!empty($to_country)) {
                $to_list_country[] = $to_country;
            }
            $list_all_customs = settings_api::getAllCustoms($from_list_country, $to_list_country);
            $map_country_customs = array();
            foreach ($list_all_customs as $item) {
                $map_country_customs[$item->from_country . '_' . $item->to_country] = $item->custom_flag;
            }
            $this->template->set('map_country_customs', $map_country_customs);
        }
        // Display the current page
        $this->template->build('custom/custom');
    }

    public function edit() {
        $this->template->set_layout(FALSE);
        // get from_country, to_country and custom_flag to form 
        $from_country = $this->input->get_post("from");
        $to_country = $this->input->get_post("to");
        $custom_flag = $this->input->get_post("flag");

        // Post
        if ($this->input->post()) {
            $from = $this->input->post("from_country");
            $to = $this->input->post("to_country");
            $flag = $this->input->post("custom_flag");
            $this->customs_matrix_m->update_custom($flag, $from, $to);

            $this->success_output("Save successfull");
            return;
        }

        $this->template->set('action_type', 'edit');
        $this->template->set('from_country', $from_country);
        $this->template->set('to_country', $to_country);
        $this->template->set('custom_flag', $custom_flag);

        $this->template->build('custom/form');
    }

}
