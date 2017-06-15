<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Admin controller for the settings module
 */
class Admin extends Admin_Controller
{
    /**
     * Validation array of country
     *
     * @var array
     */
    private $country_validation_rules = array(
        array(
            'field' => 'country_name',
            'label' => 'settings_controllers_admin_CountryName',
            'rules' => 'trim|required|min_length[3]|max_length[50]|callback__check_country_name'
        ),
        array(
            'field' => 'country_code',
            'label' => 'settings_controllers_admin_CountryCode',
            'rules' => 'trim|required|min_length[2]|max_length[2]|callback__check_country_code'
        ),
        array(
            'field' => 'currency_id',
            'label' => 'settings_controllers_admin_Currency',
            'rules' => 'trim|required|integer|callback__check_currency'
        ),
        array(
            'field' => 'letter_national_price',
            'label' => 'settings_controllers_admin_LetterNationalPrice',
            'rules' => 'trim|required|callback__check_numeric_with_format'
        ),
        array(
            'field' => 'letter_international_price',
            'label' => 'settings_controllers_admin_LetterInternationalPrice',
            'rules' => 'trim|required|callback__check_numeric_with_format'
        ),
        array(
            'field' => 'package_national_price',
            'label' => 'settings_controllers_admin_PackageNationalPrice',
            'rules' => 'trim|required|callback__check_numeric_with_format'
        ),
        array(
            'field' => 'package_international_price',
            'label' => 'settings_controllers_admin_PackageInternationalPrice',
            'rules' => 'trim|required|callback__check_numeric_with_format'
        )
    );

    /**
     * Validation array of currency
     *
     * @var array
     */
    private $currency_validation_rules = array(
        array(
            'field' => 'currency_name',
            'label' => 'lang:currency.currency_name',
            'rules' => 'trim|required|min_length[3]|max_length[50]|callback__check_currency_name'
        ),
        array(
            'field' => 'currency_short',
            'label' => 'lang:currency.currency_short',
            'rules' => 'trim|required|min_length[3]|max_length[3]|callback__check_currency_short'
        ),
        array(
            'field' => 'currency_sign',
            'label' => 'lang:currency.currency_sign',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'currency_rate',
            'label' => 'lang:currency.currency_rate',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'active_flag',
            'label' => 'active_flag',
            'rules' => 'trim'
        ),
    );

    /**
     * Constructor method
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('settings_m');
        $this->load->model('countries_m');
        $this->load->model('currencies_m');
        $this->load->library('settings');

        $this->load->library('form_validation');

        $this->lang->load('settings');

        $this->form_validation->overwrite_validation_messages();
    }

    /**
     * Index method, lists all generic settings
     *
     * @return void
     */
    public function index()
    {
        // If user submit data
        if ($_POST) {
            $SITE_NAME_CODE = $this->input->post('SITE_NAME_CODE');
            if (!empty($SITE_NAME_CODE)) {
                Settings::set(APConstants::SITE_NAME_CODE, $SITE_NAME_CODE);
            }

            $SITE_LOGO_CODE = $this->input->post('SITE_LOGO_CODE');
            if (!empty($SITE_LOGO_CODE)) {
                Settings::set(APConstants::SITE_LOGO_CODE, $SITE_LOGO_CODE);
            }

            $SITE_LOGO_WHITE_CODE = $this->input->post('SITE_LOGO_WHITE_CODE');
            if (!empty($SITE_LOGO_WHITE_CODE)) {
                Settings::set(APConstants::SITE_LOGO_WHITE_CODE, $SITE_LOGO_WHITE_CODE);
            }

            $FIRST_LETTER_KEY = $this->input->post('FIRST_LETTER_KEY');
            if (!empty($FIRST_LETTER_KEY)) {
                Settings::set(APConstants::FIRST_LETTER_KEY, $FIRST_LETTER_KEY);
            }

            $FIRST_ENVELOPE_KEY = $this->input->post('FIRST_ENVELOPE_KEY');
            if (!empty($FIRST_ENVELOPE_KEY)) {
                Settings::set(APConstants::FIRST_ENVELOPE_KEY, $FIRST_ENVELOPE_KEY);
            }

            $this->session->set_flashdata('success', lang('success'));
            redirect('admin/settings/index');
        }

        // Render the layout
        $this->template->append_js('jquery.upload-1.0.0.min.js');
        $this->template->set('header_title', lang('header:list_general_title'))->build('admin/general');
    }

    /**
     * countries method, lists all country settings
     *
     * @return void
     */
    public function countries()
    {
        // If current request is ajax
        if ($this->is_ajax_request()) {

            // update limit into user_paging.
            $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);

            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging['limit'] = $limit;

            // Call search method
            $query_result = $this->countries_m->get_list_countries_paging(array(), $input_paging['start'], $input_paging['limit'], $input_paging ['sort_column'], $input_paging['sort_type']);
            $total = $query_result['total'];
            $rows = $query_result['data'];

            // Get output response
            $response = $this->get_paging_output($total, $input_paging['limit'], $input_paging['page']);

            $i = 0;
            foreach ($rows as $row) {
                $currency = $this->currencies_m->get($row->currency_id);
                $response->rows[$i]['id'] = $row->id;
                $response->rows[$i]['cell'] = array(
                    $row->id,
                    $row->country_name,
                    $row->country_code,
                    $row->eu_member_flag,
                    $row->language,
                    is_object($currency) ? $currency->currency_short : '',
                    $row->decimal_separator,
                    $row->risk_class,
                    APUtils::number_format($row->letter_national_price),
                    APUtils::number_format($row->letter_international_price),
                    APUtils::number_format($row->package_national_price),
                    APUtils::number_format($row->package_international_price),
                    $row->id
                );
                $i++;
            }
            echo json_encode($response);
        } else {
            $this->template->set('header_title', lang('header:list_country_title'))->build('admin/countries');
        }
    }

    /**
     * Method for handling different form actions
     */
    public function add_country()
    {
        $this->load->model('settings/language_code_m');

        $country = new stdClass();
        $this->template->set_layout(FALSE);

        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            // Set the validation rules
            $this->form_validation->set_rules($this->country_validation_rules);

            if ($this->form_validation->run()) {
                $this->countries_m->insert(array(
                    'country_name' => $this->input->post('country_name'),
                    'country_code' => strtoupper($this->input->post('country_code')),
                    'eu_member_flag' => ($this->input->post('eu_member_flag') == '1') ? 1 : 0,
                    'language' => $this->input->post('language_code'),
                    'currency_id' => $this->input->post('currency_id'),
                    'decimal_separator' => $this->input->post('decimal_separator'),
                    'risk_class' => $this->input->post('risk_class'),
                    'letter_national_price' => $this->input->post('letter_national_price'),
                    'letter_international_price' => $this->input->post('letter_international_price'),
                    'package_national_price' => $this->input->post('package_national_price'),
                    'package_international_price' => $this->input->post('package_international_price')
                ));

                $this->success_output(lang('country.add_successful'));
                return;
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }

        } else {

            // Loop through each validation rule
            foreach ($this->country_validation_rules as $rule) {
                $country->{$rule['field']} = set_value($rule['field']);
            }
            $country->id = '';
            $country->language = 'English';
            $country->currency = 'EUR';
            $country->decimal_separator = ',';
            $country->eu_member_flag = APConstants::OFF_FLAG;
            $country->risk_class = 3;
            $country->invoice_address_verification = APConstants::OFF_FLAG;

            // Get all currencies
            $currencies = $this->currencies_m->get_all();
            $this->template->set('currencies', $currencies);

            // Get all languages
            $languages = $this->language_code_m->getActiveLanguages();
            $this->template->set('languages', $languages);

            // Display the current page
            $this->template->set('country', $country)->set('action_type', 'add')->build('admin/form_country');
        }
    }

    /**
     * Edit an existing country
     *
     * @param int $id The id of the country.
     */
    public function edit_country()
    {
        $this->load->model('settings/language_code_m');

        $this->template->set_layout(FALSE);
        $country_id = $this->input->get_post("country_id");

        // Get country's data
        $country = $this->countries_m->get($country_id);
        if (empty($country)) {
            $this->session->set_flashdata('error', lang('country.not_found'));
            echo lang('country.not_found');
            return;
        }

        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            // Set the validation rules
            $this->form_validation->set_rules($this->country_validation_rules);

            if ($this->form_validation->run()) {
                $country_data = array(
                    'country_name' => $this->input->post('country_name'),
                    'country_code' => strtoupper($this->input->post('country_code')),
                    'eu_member_flag' => ($this->input->post('eu_member_flag') == '1') ? 1 : 0,
                    'language' => $this->input->post('language_code'),
                    'currency_id' => $this->input->post('currency_id'),
                    'decimal_separator' => $this->input->post('decimal_separator'),
                    'risk_class' => $this->input->post('risk_class'),
                    'letter_national_price' => $this->input->post('letter_national_price'),
                    'letter_international_price' => $this->input->post('letter_international_price'),
                    'package_national_price' => $this->input->post('package_national_price'),
                    'package_international_price' => $this->input->post('package_international_price')
                );
                $result = $this->countries_m->update($country_id, $country_data);
                if ($result) {
                    $this->success_output(lang('country.edit_successful'));
                    return;
                } else {
                    $this->error_output(lang('country.edit_not_successful'));
                    return;
                }
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }

        } else {

            // Loop through each validation rule
            foreach ($this->country_validation_rules as $rule) {
                if ($this->input->post($rule['field']) !== false) {
                    $country->{$rule['field']} = set_value($rule['field']);
                }
            }

            // Get all currencies
            $currencies = $this->currencies_m->get_all();
            $this->template->set('currencies', $currencies);

            // Get all languages
            $languages = $this->language_code_m->getActiveLanguages();
            $this->template->set('languages', $languages);

            // Display the current page
            $this->template->set('country', $country)->set('action_type', 'edit')->build('admin/form_country');
        }
    }

    /**
     * Delete country
     *
     * @param int $id The id of the country.
     */
    public function delete_country()
    {
        $country_id = $this->input->get_post("country_id");
        $success = $this->countries_m->delete($country_id);
        if ($success) {
            $message = lang('country.delete_success');
            $this->success_output($message);
            return;
        } else {
            $message = lang('country.delete_error');
            $this->error_output($message);
            return;
        }
    }

    /**
     * currencies method, lists all currency settings
     *
     * @return void
     */
    public function currencies()
    {
        // If current request is ajax
        if ($this->is_ajax_request()) {

            // update limit into user_paging.
            $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);

            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging['limit'] = $limit;

            // Call search method
            $query_result = $this->currencies_m->get_list_currencies_paging(array(), $input_paging['start'], $input_paging['limit'], $input_paging ['sort_column'], $input_paging['sort_type']);
            $total = $query_result['total'];
            $datas = $query_result['data'];

            // Get output response
            $response = $this->get_paging_output($total, $input_paging['limit'], $input_paging['page']);
            #1058 add multi dimension capability for admin
            $date_format = APUtils::get_date_format_in_user_profiles();
            $decimal_separator = APUtils::get_decimal_separator_in_user_profiles();

            $i = 0;
            foreach ($datas as $row) {
                $response->rows[$i]['id'] = $row->currency_id;
                $response->rows[$i]['cell'] = array(
                    $row->currency_id,
                    $row->currency_name,
                    $row->currency_short,
                    $row->currency_sign,
                    APUtils::viewDateFormat($row->last_updated_date, $date_format),
                    APUtils::number_format($row->currency_rate, 4,$decimal_separator),
                    $row->active_flag,
                    $row->currency_id
                );
                $i++;
            }

            echo json_encode($response);

        } else {
            $this->template->set('header_title', lang('header:list_currency_title'))->build('admin/currencies');
        }
    }

    /**
     * Method for handling different form actions
     */
    public function add_currency()
    {
        $currency = new stdClass();
        $this->template->set_layout(FALSE);

        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            // Set the validation rules
            $this->form_validation->set_rules($this->currency_validation_rules);

            if ($this->form_validation->run()) {
                $current_date = date('Y-m-d');
                $currency_rate = str_replace(',', '.', $this->input->post('currency_rate'));

                $this->currencies_m->insert(array(
                    'currency_name' => $this->input->post('currency_name'),
                    'currency_short' => strtoupper($this->input->post('currency_short')),
                    'currency_sign' => $this->input->post('currency_sign'),
                    'currency_rate' => $currency_rate,
                    'created_date' => APUtils::convert_date_to_timestamp($current_date),
                    'last_updated_date' => APUtils::convert_date_to_timestamp($current_date),
                    'active_flag' => $this->input->post('currency_active')
                ));

                $this->success_output(lang('currency.add_successful'));
                return;
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        } else {

            // Loop through each validation rule
            foreach ($this->currency_validation_rules as $rule) {
                $currency->{$rule['field']} = set_value($rule['field']);
            }
            $currency->currency_id = '';

            // Display the current page
            $this->template->set('currency', $currency)->set('action_type', 'add')->build('admin/form_currency');
        }
    }

    /**
     * Edit an existing currency
     *
     * @param int $id The id of the currency.
     */
    public function edit_currency()
    {
        $this->template->set_layout(FALSE);
        $currency_id = $this->input->get_post("currency_id");

        // Get currency's data
        $currency = $this->currencies_m->get($currency_id);
        if (empty($currency)) {
            $this->session->set_flashdata('error', lang('currency.not_found'));
            echo lang('currency.not_found');
            return;
        } else {
            $currency->currency_rate = str_replace('.', ',', $currency->currency_rate);
        }

        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            // Set the validation rules
            $this->form_validation->set_rules($this->currency_validation_rules);

            if ($this->form_validation->run()) {
                $currency_rate = str_replace(',', '.', $this->input->post('currency_rate'));
                $country_data = array(
                    'currency_name' => $this->input->post('currency_name'),
                    'currency_short' => strtoupper($this->input->post('currency_short')),
                    'currency_sign' => $this->input->post('currency_sign'),
                    'currency_rate' => $currency_rate,
                    'last_updated_date' => APUtils::convert_date_to_timestamp(date('Y-m-d')),
                    'active_flag' => $this->input->post('currency_active')
                );
                $result = $this->currencies_m->update($currency_id, $country_data);
                if ($result) {
                    $this->success_output(lang('currency.edit_successful'));
                    return;
                } else {
                    $this->error_output(lang('currency.edit_not_successful'));
                    return;
                }
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        } else {

            // Loop through each validation rule
            foreach ($this->currency_validation_rules as $rule) {
                if ($this->input->post($rule['field']) !== false) {
                    $currency->{$rule['field']} = set_value($rule['field']);
                }
            }

            // Display the current page
            $this->template->set('currency', $currency)->set('action_type', 'edit')->build('admin/form_currency');
        }
    }

    /**
     * Delete currency
     *
     * @param int $id The id of the currency.
     */
    public function delete_currency()
    {
        $currency_id = $this->input->get_post("currency_id");
        $success = $this->currencies_m->delete($currency_id);
        if ($success) {
            $message = lang('currency.delete_success');
            $this->success_output($message);
            return;
        } else {
            $message = lang('currency.delete_error');
            $this->error_output($message);
            return;
        }
    }

    /**
     * Index method, lists all generic settings
     *
     * @return void
     */
    public function design()
    {
        // If user submit data
        if ($_POST) {
            // Seting color list
            for($i=1; $i<= 100; $i++){
                $tmp = '0000'.$i;
                $label_key = 'COLOR_'.substr($tmp, -3);

                $color_param = $this->input->post($label_key, true);
                if(!empty($color_param)){
                    Settings::setByLabel(APConstants::COLORS_LIST_KEY, $label_key, $color_param);
                }
            }
            
            // clear settings
            ci()->pyrocache->delete_all('settings_m');

            $this->session->set_flashdata('success', lang('success'));
            redirect('admin/settings/design');
        }

        // Render the layout
        $this->template->append_js('jquery.upload-1.0.0.min.js');
        $this->template->set('header_title', lang('header:list_general_title'))->build('admin/design');
    }

    /**
     * Index method, lists all generic settings
     *
     * @return void
     */
    public function payone()
    {
        // If user submit data
        if ($_POST) {
            // For LIVE system
            $MERCHANT_ID_CODE = $this->input->post('MERCHANT_ID_CODE');
            if (!empty($MERCHANT_ID_CODE)) {
                Settings::set(APConstants::MERCHANT_ID_CODE, $MERCHANT_ID_CODE);
            }

            $PORTAL_ID_CODE = $this->input->post('PORTAL_ID_CODE');
            if (!empty($PORTAL_ID_CODE)) {
                Settings::set(APConstants::PORTAL_ID_CODE, $PORTAL_ID_CODE);
            }

            $PORTAL_KEY_CODE = $this->input->post('PORTAL_KEY_CODE');
            if (!empty($PORTAL_KEY_CODE)) {
                Settings::set(APConstants::PORTAL_KEY_CODE, $PORTAL_KEY_CODE);
            }

            $SUB_ACCOUNT_ID_CODE = $this->input->post('SUB_ACCOUNT_ID_CODE');
            if (!empty($SUB_ACCOUNT_ID_CODE)) {
                Settings::set(APConstants::SUB_ACCOUNT_ID_CODE, $SUB_ACCOUNT_ID_CODE);
            }

            // For test/dev system
            $TEST_MERCHANT_ID_CODE = $this->input->post('TEST_MERCHANT_ID_CODE');
            if (!empty($TEST_MERCHANT_ID_CODE)) {
                Settings::set(APConstants::TEST_MERCHANT_ID_CODE, $TEST_MERCHANT_ID_CODE);
            }

            $TEST_PORTAL_ID_CODE = $this->input->post('TEST_PORTAL_ID_CODE');
            if (!empty($TEST_PORTAL_ID_CODE)) {
                Settings::set(APConstants::TEST_PORTAL_ID_CODE, $TEST_PORTAL_ID_CODE);
            }

            $TEST_PORTAL_KEY_CODE = $this->input->post('TEST_PORTAL_KEY_CODE');
            if (!empty($TEST_PORTAL_KEY_CODE)) {
                Settings::set(APConstants::TEST_PORTAL_KEY_CODE, $TEST_PORTAL_KEY_CODE);
            }

            $TEST_SUB_ACCOUNT_ID_CODE = $this->input->post('TEST_SUB_ACCOUNT_ID_CODE');
            if (!empty($TEST_SUB_ACCOUNT_ID_CODE)) {
                Settings::set(APConstants::TEST_SUB_ACCOUNT_ID_CODE, $TEST_SUB_ACCOUNT_ID_CODE);
            }

            $this->session->set_flashdata('success', lang('success'));
            redirect('admin/settings');
        }

        // Render the layout
        $this->template->append_js('jquery.upload-1.0.0.min.js');
        $this->template->set('header_title', lang('header:list_general_title'))->build('admin/index');
    }

    /**
     * Index method, lists all generic settings
     *
     * @return void
     */
    public function estamp()
    {
        // If user submit data
        if ($_POST) {
            $ESTAMP_USER = $this->input->post('ESTAMP_USER');
            if (!empty($ESTAMP_USER)) {
                Settings::set(APConstants::ESTAMP_USER, $ESTAMP_USER);
            }

            $ESTAMP_PASSWORD = $this->input->post('ESTAMP_PASSWORD');
            if (!empty($ESTAMP_PASSWORD)) {
                Settings::set(APConstants::ESTAMP_PASSWORD, $ESTAMP_PASSWORD);
            }

            $ESTAMP_LINK = $this->input->post('ESTAMP_LINK');
            if (!empty($ESTAMP_LINK)) {
                Settings::set(APConstants::ESTAMP_LINK, $ESTAMP_LINK);
            }

            $ESTAMP_PARTNER_ID = $this->input->post('ESTAMP_PARTNER_ID');
            if (!empty($ESTAMP_PARTNER_ID)) {
                Settings::set(APConstants::ESTAMP_PARTNER_ID, $ESTAMP_PARTNER_ID);
            }

            $ESTAMP_KEY_PHASE = $this->input->post('ESTAMP_KEY_PHASE');
            if (!empty($ESTAMP_KEY_PHASE)) {
                Settings::set(APConstants::ESTAMP_KEY_PHASE, $ESTAMP_KEY_PHASE);
            }

            $ESTAMP_SCHLUESSEL_DPWN_MARKTPLATZ = $this->input->post('ESTAMP_SCHLUESSEL_DPWN_MARKTPLATZ');
            if (!empty($ESTAMP_SCHLUESSEL_DPWN_MARKTPLATZ)) {
                Settings::set(APConstants::ESTAMP_SCHLUESSEL_DPWN_MARKTPLATZ, $ESTAMP_SCHLUESSEL_DPWN_MARKTPLATZ);
            }

            $ESTAMP_NAMESPACE = $this->input->post('ESTAMP_NAMESPACE');
            if (!empty($ESTAMP_NAMESPACE)) {
                Settings::set(APConstants::ESTAMP_NAMESPACE, $ESTAMP_NAMESPACE);
            }

            $this->session->set_flashdata('success', lang('success'));
            redirect('admin/settings');
        }

        // Render the layout
        $this->template->append_js('jquery.upload-1.0.0.min.js');
        $this->template->set('header_title', lang('header:list_general_title'))->build('admin/index');
    }

    /**
     * Upload logo
     */
    public function upload_first_letter()
    {
        $this->load->library('files/files');

        $imagepath = Files::upload('envelope', 'imagepath');

        $this->success_output($imagepath);
        return;
    }

    /**
     * Upload logo
     */
    public function upload_main_logo()
    {
        $this->load->library('files/files');
        $imagepath = Files::upload('logo', 'imagepath');
        $this->success_output($imagepath);
        return;
    }

    /**
     * Upload logo
     */
    public function upload_white_logo()
    {
        $this->load->library('files/files');
        $imagepath = Files::upload('logo', 'imagepath');
        $this->success_output($imagepath);
        return;
    }

    /**
     * Index method, lists all generic settings
     *
     * @return void
     */
    public function email()
    {
        // If user submit data
        if ($_POST) {
            $MAIL_CONTACT_CODE = $this->input->post('MAIL_CONTACT_CODE');
            if (!empty($MAIL_CONTACT_CODE)) {
                Settings::set(APConstants::MAIL_ALIAS_NAME_CODE, $MAIL_CONTACT_CODE);
            }

            $MAIL_ALIAS_NAME_CODE = $this->input->post('MAIL_ALIAS_NAME_CODE');
            if (!empty($MAIL_ALIAS_NAME_CODE)) {
                Settings::set(APConstants::MAIL_ALIAS_NAME_CODE, $MAIL_ALIAS_NAME_CODE);
            }

            $MAIL_SERVER_CODE = $this->input->post('MAIL_SERVER_CODE');
            if (!empty($MAIL_SERVER_CODE)) {
                Settings::set(APConstants::MAIL_SERVER_CODE, $MAIL_SERVER_CODE);
            }

            $MAIL_PROTOCOL_CODE = $this->input->post('MAIL_PROTOCOL_CODE');
            if (!empty($MAIL_PROTOCOL_CODE)) {
                Settings::set(APConstants::MAIL_PROTOCOL_CODE, $MAIL_PROTOCOL_CODE);
            }

            $MAIL_SMTP_HOST_CODE = $this->input->post('MAIL_SMTP_HOST_CODE');
            if (!empty($MAIL_SMTP_HOST_CODE)) {
                Settings::set(APConstants::MAIL_SMTP_HOST_CODE, $MAIL_SMTP_HOST_CODE);
            }

            $MAIL_SMTP_PASS_CODE = $this->input->post('MAIL_SMTP_PASS_CODE');
            if (!empty($MAIL_SMTP_PASS_CODE)) {
                Settings::set(APConstants::MAIL_SMTP_PASS_CODE, $MAIL_SMTP_PASS_CODE);
            }

            $MAIL_SMTP_PORT_CODE = $this->input->post('MAIL_SMTP_PORT_CODE');
            if (!empty($MAIL_SMTP_PORT_CODE)) {
                Settings::set(APConstants::MAIL_SMTP_PORT_CODE, $MAIL_SMTP_PORT_CODE);
            }

            $MAIL_SMTP_USER_CODE = $this->input->post('MAIL_SMTP_USER_CODE');
            if (!empty($MAIL_SMTP_USER_CODE)) {
                Settings::set(APConstants::MAIL_SMTP_USER_CODE, $MAIL_SMTP_USER_CODE);
            }

            $MAIL_SENDMAIL_PATH_CODE = $this->input->post('MAIL_SENDMAIL_PATH_CODE');
            if (!empty($MAIL_SENDMAIL_PATH_CODE)) {
                Settings::set(APConstants::MAIL_SENDMAIL_PATH_CODE, $MAIL_SENDMAIL_PATH_CODE);
            }
            $this->session->set_flashdata('success', lang('success'));
            redirect('admin/settings/email');
        }

        // Render the layout
        $this->template->set('header_title', lang('header:list_email_title'))->build('admin/email');
    }

    /**
     * Index method, lists all generic settings
     *
     * @return void
     */
    public function payment()
    {
        if ($_POST) {
            // Paypal
            $PAYMENT_PAYPAL_USERNAME_CODE = $this->input->post('PAYMENT_PAYPAL_USERNAME_CODE');
            if (!empty($PAYMENT_PAYPAL_USERNAME_CODE)) {
                Settings::set(APConstants::PAYMENT_PAYPAL_USERNAME_CODE, $PAYMENT_PAYPAL_USERNAME_CODE);
            }

            $PAYMENT_PAYPAL_PASSWORD_CODE = $this->input->post('PAYMENT_PAYPAL_PASSWORD_CODE');
            if (!empty($PAYMENT_PAYPAL_PASSWORD_CODE)) {
                Settings::set(APConstants::PAYMENT_PAYPAL_PASSWORD_CODE, $PAYMENT_PAYPAL_PASSWORD_CODE);
            }

            $PAYMENT_PAYPAL_SIGNATURE_CODE = $this->input->post('PAYMENT_PAYPAL_SIGNATURE_CODE');
            if (!empty($PAYMENT_PAYPAL_SIGNATURE_CODE)) {
                Settings::set(APConstants::PAYMENT_PAYPAL_SIGNATURE_CODE, $PAYMENT_PAYPAL_SIGNATURE_CODE);
            }

            // EWAY
            $PAYMENT_EWAY_CUSTOMERID_CODE = $this->input->post('PAYMENT_EWAY_CUSTOMERID_CODE');
            if (!empty($PAYMENT_EWAY_CUSTOMERID_CODE)) {
                Settings::set(APConstants::PAYMENT_EWAY_CUSTOMERID_CODE, $PAYMENT_EWAY_CUSTOMERID_CODE);
            }
        }
        // Render the layout
        $this->template->set('header_title', lang('header:list_payment_title'))->build('admin/payment');
    }

    /**
     * Validate country_name
     *
     * @param $country_name string
     * @return boolean (true: valid; false: invalid)
     */
    public function _check_country_name($country_name)
    {
        $country_id = $this->input->post('country_id');

        // Only check in the case of adding a new country
        if (empty($country_id)) {
            $country = $this->countries_m->get_by(array('country_name' => $country_name));
            if ($country) {
                $message = admin_language('settings_controllers_admin_CountryNameDuplicated');
                $this->form_validation->set_message("_check_country_name", $message);
                return false;
            }
        }

        return true;
    }

    /**
     * Validate country_code
     *
     * @param $country_code string
     * @return boolean (true: valid; false: invalid)
     */
    public function _check_country_code($country_code)
    {
        $country_id = $this->input->post('country_id');

        // Only check in the case of adding a new country
        if (empty($country_id)) {
            $country_code = strtoupper(trim($country_code));
            $country = $this->countries_m->get_by(array('country_code' => $country_code));
            if ($country) {
                $message = admin_language('settings_controllers_admin_CountryCodeDuplicated');
                $this->form_validation->set_message("_check_country_code", $message);
                return false;
            }
        }

        return true;
    }

    /**
     * Validate currency_id not existed
     *
     * @param $currency_id string
     * @return boolean (true: valid; false: invalid)
     */
    public function _check_currency($currency_id)
    {
        $currency = $this->currencies_m->get($currency_id);
        if (empty($currency)) {
            $message = admin_language('settings_controllers_admin_CurrencyNotExisted');
            $this->form_validation->set_message("_check_currency", $message);
            return false;
        }

        return true;
    }

    /**
     * Validate currency_name duplicated
     *
     * @param $currency_name string
     * @return boolean (true: valid; false: invalid)
     */
    public function _check_currency_name($currency_name)
    {
        $currency_id = $this->input->post('currency_id');

        // Only check in the case of adding a new currency
        if (empty($currency_id)) {
            $currency = $this->currencies_m->get_by(array('currency_name' => $currency_name));
            if ($currency) {
                $message = admin_language('settings_controllers_admin_CurrencyNameDuplicated');
                $this->form_validation->set_message("_check_currency_name", $message);
                return false;
            }
        }

        return true;
    }

    /**
     * Validate currency_short duplicated
     *
     * @param $currency_short string
     * @return boolean (true: valid; false: invalid)
     */
    public function _check_currency_short($currency_short)
    {
        $currency_id = $this->input->post('currency_id');

        // Only check in the case of adding a new currency
        if (empty($currency_id)) {
            $currency_short = strtoupper(trim($currency_short));
            $currency = $this->currencies_m->get_by(array('currency_short' => $currency_short));
            if ($currency) {
                $message = admin_language('settings_controllers_admin_CurrencyShortDuplicated');
                $this->form_validation->set_message("_check_currency_short", $message);
                return false;
            }
        }

        return true;
    }

    /**
     * Get list language keys and value by search condition
     */
    public function languages()
    {
        $this->load->model('language_text_m');
        $this->load->model('language_code_m');
        $this->load->model('language_key_m');

        // If current request is ajax
        if ($this->is_ajax_request()) {

            //Get search text
            $array_where = array();
            $textSearch = $this->input->post('textSearch');
            if (!empty($textSearch)) {
                $array_where = array('language_keys.key LIKE "%'. $textSearch . '%" OR language_text.value LIKE "%'. $textSearch . '%"'=> null);
            }

            // update limit into user_paging.
            $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);

            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging['limit'] = $limit;

            //Get paging data
            $query_result = $this->language_key_m->get_list_language_paging($array_where, $input_paging['start'], $input_paging['limit'], $input_paging ['sort_column'], $input_paging['sort_type']);
            $total = $query_result['total'];
            $rows = $query_result['data'];


            // Get output response
            $response = $this->get_paging_output($total, $input_paging['limit'], $input_paging['page']);

            //Get language in other languages
            //Get all language in system
            $language_codes = $this->language_code_m->get_all();



            $i = 0;
            foreach ($rows as $row) {
                //Get language in other languages of this language key
                $cell_data = array($row->id, $row->key);
                foreach ($language_codes as $language_code) {
                    $language_text = $this->language_text_m->get_language($language_code->id, $row->id);
                    $cell_data[] = empty($language_text) ? '' : trim($language_text->value);
                }
                $response->rows[$i]['id'] = $row->id;
                $response->rows[$i]['cell'] = $cell_data;
                $i++;
            }
            echo json_encode($response);
        } else {
            $this->template->set('header_title', lang('header:list_country_title'))->build('admin/languages');
        }
    }

    /**
     * Add new language to system
     * @return type
     */
    public function addLanguage()
    {
        $this->load->model('language_code_m');
        $this->load->model('settings/countries_m');
        $this->template->set_layout(FALSE);

        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            // Set the validation rules
            $this->form_validation->set_rules(array(
                array(
                    'field' => 'language',
                    'label' => 'Language',
                    'rules' => 'required|callback__checkLanguage'
                )
            ));

            if ($this->form_validation->run()) {
                $this->language_code_m->insert(array(
                    'code' => $this->input->post('language'),
                    'status' => $this->input->post('language_status')
                ));

                $this->success_output(lang('country.add_successful'));
                return;
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }

        } else {

            // Display the current page
            $this->template->build('admin/form_language');
        }
    }

    /***
     * Import from excel file
     */
    public function importExcel()
    {
        $this->load->model('language_code_m');
        $this->load->model('settings/countries_m');
        $this->template->set_layout(FALSE);
        $insertNumber  = $updateNumber = 0;
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $this->load->model('settings/language_key_m');
            $this->load->model('settings/language_text_m');
            $this->load->library('files/files');
            $name = APContext::getAdminLoggedIn()->id;
            $name .= '-' .time(). '-' .$_FILES["file"]['name'];
            $path = $this->files->upload_file_with_name( 'uploads/languages/', 'file', $name);
            if ($path['status']) {
                $this->load->library('PHPExcel');
                $local_file_path = $path['local_file_path'];
                //  Read your Excel workbook
                try {
                    $inputFileType = PHPExcel_IOFactory::identify($local_file_path);
                    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($local_file_path);
                    //  Get worksheet dimensions
                    $sheet = $objPHPExcel->getSheet(0);
                    $highestRow = $sheet->getHighestRow();
                    $highestColumn = $sheet->getHighestColumn();
                    $header = [];
                    $updates = [];
                    $inserts = [];
                    $message = '';
                    //  Loop through each row of the worksheet in turn
                    for ($row = 1; $row <= $highestRow; $row++){
                        //  Read a row of data into an array
                        $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                                                        NULL, TRUE, FALSE);
                        // If First row: read header to get language code
                        if ($row == 1) {
                            $row_header = $rowData[0];
                            $language_codes = $this->language_code_m->get_all();
                            $exist_languages = [];
                            foreach ($row_header as $key => $value) {
                                foreach ($language_codes as $l_key => $l_value) {
                                    if (strval($value) == strval($l_value->code)) {
                                        $header[$key] = $l_value->id;
                                        $exist_languages[] = $l_value->code;
                                    }
                                }
                            }
                            unset($rowData[0][0]);
                            $exist_languages = array_unique($exist_languages);
                            // Check new language
                            $new_languages = array_diff($rowData[0], $exist_languages);
                            if (!empty($new_languages)) {
                                $language_country = $this->countries_m->getAllLanguagesForDropDownList();
                                // add new languages code. Valid languages
                                foreach ($new_languages as $n_key => $n_value) {
                                    foreach ($language_country as $ll_key => $ll_value) {
                                        if (strval($n_value) == strval($ll_value->language)) {
                                            // Insert new languages
                                            $this->language_code_m->insert(array(
                                                'code' => $n_value,
                                                'status' => 1
                                            ));
                                            $insert_id = $this->language_code_m->insert_id();
                                            foreach ($row_header as $key => $value) {
                                                if ($value == $n_value) {
                                                    $header[$key] = $insert_id;
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            if (empty($header)) {
                                break;
                            }
                            continue;
                        } else {
                            $data = $rowData[0];
                            $key_exists = $this->language_key_m->get_by(["`key` = '$data[0]'" => null]);
                            if (! preg_match("/^[a-zA-Z0-9_@?]+$/", $data[0])) {
                                continue;
                            }
                            if (empty($key_exists)) {
                                // insert new
                                $insert_id = NULL;
                                $first = true;
                                $header_keys = array_keys($header);
                                for($j = 0; $j < count($header_keys); $j++) {
                                    $i = $header_keys[$j];
                                    if ($data[$i]) {
                                        if ($first) {
                                            $insert = [
                                                "$data[0]" => $data[$i]
                                            ];
                                            $insert_id = $this->language_text_m->addToDbLanguages($insert, $header[$i]);
                                            $insert_id = $insert_id[0];
                                            $first = false;
                                            $insertNumber ++;
                                        } else {
                                            $inserts[] = [
                                                'key_id'    =>  $insert_id,
                                                'code_id'   =>  $header[$i],
                                                'value'     =>  $data[$i]
                                            ];
                                        }
                                    }
                                }
                            } else {
                                // update record
                                $updateNumber ++;
                                $header_keys = array_keys($header);
                                for($j = 0; $j < count($header_keys); $j++) {
                                    $i = $header_keys[$j];
                                    $text_exists = $this->language_text_m->get_by(["`key_id` = '$key_exists->id'" => null, "`code_id` = $header[$i]" => null]);
                                    if ($data[$i]) {
                                        // check text exist
                                        if (empty($text_exists)) {
                                            $inserts[] = [
                                                'key_id'   =>  $key_exists->id,
                                                'code_id'   =>  $header[$i],
                                                'value' =>  $data[$i]
                                            ];
                                        } else {
                                            $updates[] = [
                                                'key_id'   =>  $key_exists->id,
                                                'code_id'   =>  $header[$i],
                                                'value' =>  $data[$i]
                                            ];
                                        }
                                    } else {
                                        if ($text_exists) {
                                            // delete
                                            $this->language_text_m->delete($text_exists->id);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $this->language_text_m->insert_many($inserts);
                    $this->language_text_m->update_batch_languages($updates, ['key_id', 'code_id']);
                } catch(Exception $e) {
                    $this->error_output('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
                }
                $message = 'Success Upload Import File. ' . admin_language('settings_controller_admin_ThereAreNumberKeyInsertUpdate', ['insert' => $insertNumber, 'update' => $updateNumber]);
                $this->success_output($message);
            } else {
                $this->error_output('Something wrong');
            }
        } else {
            // Display the current page
            $this->template->build('admin/form_upload_excel');
        }
    }

    /**
     * Check duplicate language
     * @param type $language_code
     * @return boolean
     */
    public function _checkLanguage($language_code)
    {
        if (!empty($language_code)) {
            $language = $this->language_code_m->get_by(array('code' => $language_code));
            if ($language) {
                $message = admin_language('settings_controllers_admin_LanguageExisted');
                $this->form_validation->set_message("_checkLanguage", $message);
                return false;
            }
        }

        return true;
    }

    /**
     * Build columns for language data grid
     */
    public function loadLanguageGridSetting(){
        $this->load->model('language_code_m');
        $colNames = array('ID', 'Variable Name');
        $colModel = array(
            array('name' => 'ID', 'index' => 'id' ,'width' => 20, 'align' => 'center'),
            array('name' => 'Variable Name', 'index' => 'key', 'width' => 100)
        );
        $language_codes = $this->language_code_m->get_all();
        if (!empty($language_codes)) {
            foreach ($language_codes as $language_code){
                $status = ($language_code->status == 1) ? 'Active' : 'Inactive';
                $colNames[] = $language_code->code . '&nbsp;(' . $status . ')';
                $colModel[] = array(
                    'name' => $language_code->code,
                    'editable' => true,
                    'sortable' => false
                );
            }
        }
        echo json_encode(array('colNames' => $colNames, 'colModel' => $colModel));
    }

    /**
     * Save language text value
     */
    public function saveLanguage(){
        $this->load->model('language_code_m');
        $this->load->model('language_text_m');
        $key_id = $this->input->post('id');
        //Get all languages
        $language_codes = $this->language_code_m->get_all();
        //Save value to corresponding key, code

        if (!empty($key_id) && !empty($language_codes)) {
            foreach ($language_codes as $language_code){
                $language_value = $this->input->post($language_code->code);
                $language_text = $this->language_text_m->get_by_many(array('key_id' => $key_id, 'code_id' => $language_code->id));
                if (empty($language_value)) {
                    $this->language_text_m->delete_by_many(["key_id = $key_id" => null, "code_id = $language_code->id" => null]);
                    continue;
                }
                if (empty($language_text)) {
                    //Add new
                    $this->language_text_m->insert(array('key_id' => $key_id, 'code_id' => $language_code->id, 'value' => $language_value));
                } else {
                    //Update
                    $this->language_text_m->update_by_many(array('key_id' => $key_id, 'code_id' => $language_code->id),
                        array('value' => $language_value));
                }
            }
        }
    }

    /**
     * Display change language status page
     */
    public function editLanguage()
    {
        $this->template->set_layout(FALSE);
        $this->template->build('admin/language_status');
    }

    /**
     * Load language status grid
     */
    public function loadLanguageStatus(){
        $this->load->model('language_code_m');
        //Get all languages
        $language_codes = $this->language_code_m->get_all();
        $i = 0;
        foreach ($language_codes as $language_code) {
            $response->rows[$i]['id'] = $language_code->id;
            $response->rows[$i]['cell'] = array(
                $language_code->id,
                $language_code->code,
                $language_code->status
            );
            $i++;
        }

        echo json_encode($response);
    }

    /**
     * Update language status
     */
    public function saveLanguageStatus(){
        $this->load->model('language_code_m');
        $languages = $this->input->get_post('data');
        if (!empty($languages)) {
            $languages = json_decode($languages, true);
            foreach ($languages as $language) {
                //Update status
                $this->language_code_m->update($language['id'], array('status' => $language['status']));
            }
        }
        return $this->success_output('');
    }

    /**
    * Get Export languages to Excel file
    */
    public function languagesToExcell($backup = NULL)
    {
        $search = $this->input->post('search');
        $filename = 'languages_full_list';
        $this->load->model('language_text_m');
        $this->load->model('language_code_m');
        $this->load->model('language_key_m');
        $this->load->library('PHPExcel');
        $object = $this->phpexcel;
        $language_codes = $this->language_code_m->get_all();
        if ($search) {
            $array_where = array('language_keys.key LIKE "%'. $search . '%" OR language_text.value LIKE "%'. $search . '%"'=> null);
            $rows = $this->language_key_m->get_full_list($array_where);
        } else {
            $rows = $this->language_key_m->get_full_list();
        }
        $export_data = [];
        $i = 0;
        $header = ['key(beware it directly import)'];
        foreach ($language_codes as $key => $value) {
            $header[] = $value->code;
        }
        foreach ($rows as $row) {
            //Get language in other languages of this language key
            $cell_data = array($row->id, $row->key);
            foreach ($language_codes as $language_code) {
                $language_text = $this->language_text_m->get_language($language_code->id, $row->id);
                $cell_data[] = empty($language_text) ? '' : trim($language_text->value);
            }
            unset($cell_data[0]);
            $export_data[] = $cell_data;
            $i++;
        }

        $object->setActiveSheetIndex(0);
        $sheet = $object->getActiveSheet();
        $row = '1';
        $col = "A";
        // Print title
        foreach ($header as $key => $value) {
            $sheet->setCellValue($col.$row, $value);
            $col ++;
        }
        $row = '2';
        $col = 'A';
        // Print cottent
        foreach($export_data as $row_cells) {
            if(!is_array($row_cells)) { continue; }
                foreach($row_cells as $cell) {
                    $sheet->setCellValue($col.$row, $cell);
                    $col++;
                }
            $row += 1;
            $col = "A";
        }
        $higher = $row - 1;
        $object->getActiveSheet()->getStyle("A1:A$higher")->getFill()
        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FFE8E5E5');
        $objWriter = new PHPExcel_Writer_Excel2007($object);
        if ($backup) {
            if (!is_dir('uploads/languages')) {
                mkdir('uploads/languages', 0777, TRUE);
                chmod('uploads/languages', 0777);
            }
            if (!is_dir('uploads/languages/backup')) {
                mkdir('uploads/languages/backup', 0777, TRUE);
                chmod('uploads/languages/backup', 0777);
            }
            $name = APContext::getAdminLoggedIn()->id;
            $name .= '-' .time(). '-' .$filename;
            $objWriter->save('uploads/languages/backup/' . $name);
        } else {
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
            header('Cache-Control: max-age=0');
            $objWriter->save('php://output');
            exit;
        }
    }

    /**
     * Delete languages key
     * Given language_key_id
     */
    public function delete_language()
    {
        $language_id = $this->input->post('language_id');
        $this->load->model('language_text_m');
        $this->load->model('language_key_m');
        $this->language_text_m->delete_by_many(["key_id = $language_id" => null]);
        $this->language_key_m->delete($language_id);
        $this->success_output('Success');
    }

    /**
     * check number with separator
     * @param $numberStr
     * @return bool
     */
    public function _check_numeric_with_format($numberStr){
        $this->load->model('users/user_profiles_m');
        $dec_point = APUtils::get_decimal_separator_in_user_profiles();
        $thousands_sep = '.';
        if($dec_point != ','){
            $thousands_sep = $dec_point;
            $dec_point = '.';
        }
        $number = str_replace($dec_point, $thousands_sep, str_replace($thousands_sep, '', $numberStr));

        if (!is_numeric($number)) {
            $message = $this->form_validation->get_error_message('numeric');
            $this->form_validation->set_message("_check_numeric_with_format", $message);
            return false;
        }

        return true;
    }
}