<?php
if (! defined('BASEPATH')) exit('No direct script access allowed');

class digitalpanel extends Public_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->model('digitalpanel/customer');
        $this->load->helper('download');
    }

	public function generate($location) {
        if (!$location) {
            die('Could not load customers');
        }

        $customers = new customer();
        $customers->loadData($location);
        $customerData = $customers->getData();

        $panelHtml = $this->template
            ->title('Digital Panel')
            ->set_theme('digitalpanel')
            ->set_partial('bootstrap'           , 'partials/css_bootstrap')
            ->set_partial('jqueryui'            , 'partials/css_jqueryui')
            ->set_partial('jquerykeyboard'      , 'partials/css_jquery_keyboard')
            ->set_partial('datatable'           , 'partials/css_datatable')
            ->set_partial('styles'              , 'partials/css_styles')
            ->set_partial('modernizr'           , 'partials/js_modernizr')
            ->set_partial('jquery'              , 'partials/js_jquery')
            ->set_partial('jsbootstrap'         , 'partials/js_bootstrap')
            ->set_partial('jsjqueryui'          , 'partials/js_jqueryui')
            ->set_partial('jsjquerykeyboard'    , 'partials/js_jquery_keyboard')
            ->set_partial('jsdatatable'         , 'partials/js_datatables')
            ->set_partial('scripts'             , 'partials/js_scripts')
            ->set('customerData'                , $customerData)
            ->build('generate', '', true);

        if (!file_exists('./download')) {
            mkdir('./download', 0755);
        }
        if (!file_exists('./download/digitalpanel')) {
            mkdir('./download/digitalpanel', 0755);
        }

        if (!write_file('./download/digitalpanel/panel_' . $location . '.html', $panelHtml)) {
            die('Could not create file');
        }
    }

	public function download($location) {
        if ($location) {
            if (file_exists('./download/digitalpanel/panel_' . $location . '.html')) {
                $data = file_get_contents('./download/digitalpanel/panel_' . $location . '.html');
                $name = 'index.html';
                force_download($name, $data);
            }
        }
    }
}