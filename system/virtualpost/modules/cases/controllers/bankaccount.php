<?php defined('BASEPATH') or exit ('No direct script access allowed');

class bankaccount extends CaseSystem_Controller
{
    private $validation_rules = array(
            array(
                    'field' => 'n1_first_name',
                    'label' => 'lang:first_name',
                    'rules' => 'required|trim|max_length[50]'
            ),
            array(
                    'field' => 'n1_middle_name',
                    'label' => 'lang:middle_name',
                    'rules' => 'required|trim|max_length[50]'
            ),
            array(
                    'field' => 'n1_last_name',
                    'label' => 'lang:last_name',
                    'rules' => 'required|trim|max_length[50]'
            ),
            array(
                    'field' => 'n1_street_address',
                    'label' => 'lang:street_address',
                    'rules' => 'trim'
            ),
            array(
                    'field' => 'n1_post_code',
                    'label' => 'lang:post_code',
                    'rules' => 'trim'
            ),
            array(
                    'field' => 'n1_city',
                    'label' => 'lang:city',
                    'rules' => 'trim'
            ),
            array(
                    'field' => 'n1_region',
                    'label' => 'lang:region',
                    'rules' => 'trim'
            ),
            array(
                    'field' => 'n1_country',
                    'label' => 'lang:country',
                    'rules' => 'trim'
            ),
            array(
                    'field' => 'n1_date_of_birth',
                    'label' => 'lang:date_of_birth',
                    'rules' => 'trim'
            ),
            array(
                    'field' => 'n1_place_of_birth',
                    'label' => 'lang:place_of_birth',
                    'rules' => 'trim'
            ),
            array(
                    'field' => 'n1_country_of_birth',
                    'label' => 'lang:country_of_birth',
                    'rules' => 'trim'
            ),
            array(
                    'field' => 'n1_phone_number',
                    'label' => 'lang:phone_number',
                    'rules' => 'trim'
            ),
            array(
                    'field' => 'n1_mobile_number',
                    'label' => 'lang:mobile_number',
                    'rules' => 'trim'
            ),
            array(
                    'field' => 'n1_email_address',
                    'label' => 'lang:email_address',
                    'rules' => 'trim'
            ),
            array(
                    'field' => 'n1_passport_number',
                    'label' => 'lang:passport_number',
                    'rules' => 'trim'
            ),
            
            // Number 2
            array(
                    'field' => 'n2_first_name',
                    'label' => 'lang:first_name',
                    'rules' => 'trim'
            ),
            array(
                    'field' => 'n2_middle_name',
                    'label' => 'lang:middle_name',
                    'rules' => 'trim'
            ),
            array(
                    'field' => 'n2_last_name',
                    'label' => 'lang:last_name',
                    'rules' => 'trim'
            ),
            array(
                    'field' => 'n2_street_address',
                    'label' => 'lang:street_address',
                    'rules' => 'trim'
            ),
            array(
                    'field' => 'n2_post_code',
                    'label' => 'lang:post_code',
                    'rules' => 'trim'
            ),
            array(
                    'field' => 'n2_city',
                    'label' => 'lang:city',
                    'rules' => 'trim'
            ),
            array(
                    'field' => 'n2_region',
                    'label' => 'lang:region',
                    'rules' => 'trim'
            ),
            array(
                    'field' => 'n2_country',
                    'label' => 'lang:country',
                    'rules' => 'trim'
            ),
            array(
                    'field' => 'n2_date_of_birth',
                    'label' => 'lang:date_of_birth',
                    'rules' => 'trim'
            ),
            array(
                    'field' => 'n2_place_of_birth',
                    'label' => 'lang:place_of_birth',
                    'rules' => 'trim'
            ),
            array(
                    'field' => 'n2_country_of_birth',
                    'label' => 'lang:country_of_birth',
                    'rules' => 'trim'
            ),
            array(
                    'field' => 'n2_phone_number',
                    'label' => 'lang:phone_number',
                    'rules' => 'trim'
            ),
            array(
                    'field' => 'n2_mobile_number',
                    'label' => 'lang:mobile_number',
                    'rules' => 'trim'
            ),
            array(
                    'field' => 'n2_email_address',
                    'label' => 'lang:email_address',
                    'rules' => 'trim'
            ),
            array(
                    'field' => 'n2_passport_number',
                    'label' => 'lang:passport_number',
                    'rules' => 'trim'
            )
    );

    private $validation_company_information_rules = array(
            array(
                    'field' => 'company_legal',
                    'label' => 'lang:company_legal',
                    'rules' => 'required|trim|max_length[50]'
            ),
            array(
                    'field' => 'company_name',
                    'label' => 'lang:company_name',
                    'rules' => 'required|trim|max_length[50]'
            ),
            array(
                    'field' => 'street_address',
                    'label' => 'lang:street_address',
                    'rules' => 'required|trim'
            ),
            array(
                    'field' => 'post_code',
                    'label' => 'lang:post_code',
                    'rules' => 'required|trim'
            ),
            array(
                    'field' => 'city',
                    'label' => 'lang:city',
                    'rules' => 'trim'
            ),
            array(
                    'field' => 'region',
                    'label' => 'lang:region',
                    'rules' => 'trim'
            ),
            array(
                    'field' => 'country',
                    'label' => 'lang:country',
                    'rules' => 'required|trim'
            ),
            array(
                    'field' => 'website',
                    'label' => 'lang:website',
                    'rules' => 'trim'
            ),
            array(
                    'field' => 'purpose_of_company',
                    'label' => 'lang:purpose_of_company',
                    'rules' => 'trim'
            ),
            array(
                    'field' => 'registered_capital',
                    'label' => 'lang:registered_capital',
                    'rules' => 'trim'
            ),
            array(
                    'field' => 'capital_paid',
                    'label' => 'lang:capital_paid',
                    'rules' => 'trim'
            ),
            array(
                    'field' => 'phone_number',
                    'label' => 'lang:phone_number',
                    'rules' => 'trim'
            ),
            array(
                    'field' => 'email_address',
                    'label' => 'lang:email_address',
                    'rules' => 'trim'
            ),
            array(
                    'field' => 'registration_number',
                    'label' => 'lang:registration_number',
                    'rules' => 'trim'
            )
    );

    private $validation_additional_information_rules = array(
            array(
                    'field' => 'declaration_of_director',
                    'label' => 'Declaration of director',
                    'rules' => 'required|trim'
            ),
            array(
            		'field' => 'confirm_flag',
            		'label' => 'Confirm',
            		'rules' => 'required|trim'
            ),
            array(
            		'field' => 'transaction_limit',
            		'label' => 'transaction limit',
            		'rules' => 'trim'
            ),
            array(
            		'field' => 'transaction_peryear',
            		'label' => 'planned number of transactions per year',
            		'rules' => 'trim'
            ),
            array(
            		'field' => 'transaction_value_peryear',
            		'label' => 'planned total transaction value per year',
            		'rules' => 'trim'
            ),
            array(
            		'field' => 'transaction_value_limit',
            		'label' => 'transaction value limit',
            		'rules' => 'trim'
            )
    );

    /**
     * Loads the gazillion of stuff, in Flash Gordon speed.
     * 
     * @todo Document properly please.
     */
    public function __construct ()
    {
        parent::__construct();
        
        // Model
        $this->load->model(
                array(
                        'cases_m',
                        'settings/countries_m',
                        "cases/cases_additional_information_m",
                        "cases/cases_company_information_m",
                        "cases/cases_personal_identity_m",
                        "cases/cases_registration_document_m",
                        "cases/cases_taskname_instance_m"
                ));
        
        $this->lang->load('cases');
        $this->load->library('form_validation');
        $this->load->library('S3');
    }

    /**
     * Check access by case_id
     */
    private function check_access ()
    {
        $customer_id = APContext::getCustomerCodeLoggedIn();
        if (empty($customer_id)) {
            redirect('cases');
        }
        
        $case_id = $this->input->get_post('case_id');
        $case = $this->cases_m->get_by_many(array(
                'id' => $case_id
        ));
        if (empty($case) || $case->customer_id != $customer_id) {
            redirect('cases');
        }
    }

    /**
     * create case function
     * @createdBy: d3jsexperts
     */
    public function personal_identify ()
    {
        // Check access
        $this->check_access();
        $case_id = $this->input->get_post('case_id');
        
        $cases_personal_identity_01 = $this->cases_personal_identity_m->get_by_many(
                array(
                        'case_id' => $case_id,
                        'director_number' => 1
                ));
        
        $cases_personal_identity_02 = $this->cases_personal_identity_m->get_by_many(
                array(
                        'case_id' => $case_id,
                        'director_number' => 2
                ));
        
        // If post
        if ($_POST) {
            $customer_id = APContext::getCustomerCodeLoggedIn();
            $input = $this->input->post();
            
            $this->form_validation->set_rules($this->validation_rules);
            if ($this->form_validation->run()) {
                $data_n1 = array(
                        "case_id" => $case_id,
                        "first_name" => $input['n1_first_name'],
                        "middle_name" => $input['n1_middle_name'],
                        "last_name" => $input['n1_last_name'],
                        "street_address" => $input['n1_street_address'],
                        "post_code" => $input['n1_post_code'],
                        "city" => $input['n1_city'],
                        "region" => $input['n1_region'],
                        "country" => $input['n1_country'],
                        "date_of_birth" => $input['n1_date_of_birth'],
                        "place_of_birth" => $input['n1_place_of_birth'],
                        "country_of_birth" => $input['n1_country_of_birth'],
                        "phone_number" => $input['n1_phone_number'],
                        "mobile_number" => $input['n1_mobile_number'],
                        "email_address" => $input['n1_email_address'],
                        "passport_number" => $input['n1_passport_number'],
                        "created_date" => now(),
                        "passport_local_file_path" => '',
                        "birth_certificate_local_file_path" => '',
                        "birth_certificate_amazon_file_path" => '',
                        "director_number" => 1
                );
                
                if (empty($cases_personal_identity_01)) {
                    // Insert main director
                    $this->cases_personal_identity_m->insert($data_n1);
                }
                else {
                    // Insert main director
                    $this->cases_personal_identity_m->update_by_many(
                            array(
                                    'case_id' => $case_id,
                                    'director_number' => 1
                            ), $data_n1);
                }
                
                $data_n2 = array(
                        "case_id" => $case_id,
                        "first_name" => $input['n2_first_name'],
                        "middle_name" => $input['n2_middle_name'],
                        "last_name" => $input['n2_last_name'],
                        "street_address" => $input['n2_street_address'],
                        "post_code" => $input['n2_post_code'],
                        "city" => $input['n2_city'],
                        "region" => $input['n2_region'],
                        "country" => $input['n2_country'],
                        "date_of_birth" => $input['n2_date_of_birth'],
                        "place_of_birth" => $input['n2_place_of_birth'],
                        "country_of_birth" => $input['n2_country_of_birth'],
                        "phone_number" => $input['n2_phone_number'],
                        "mobile_number" => $input['n2_mobile_number'],
                        "email_address" => $input['n2_email_address'],
                        "passport_number" => $input['n2_passport_number'],
                        "created_date" => now(),
                        "passport_local_file_path" => '',
                        "birth_certificate_local_file_path" => '',
                        "birth_certificate_amazon_file_path" => '',
                        "director_number" => 2
                );
                
                if (empty($cases_personal_identity_02)) {
                    // Insert main director
                    $this->cases_personal_identity_m->insert($data_n2);
                }
                else {
                    // Insert main director
                    $this->cases_personal_identity_m->update_by_many(
                            array(
                                    'case_id' => $case_id,
                                    'director_number' => 2
                            ), $data_n2);
                }
                
                // Update task instance status
                $this->cases_taskname_instance_m->update_by_many(
                        array(
                                "case_id" => $case_id,
                                "base_task_name" => "personal_identify",
                                "status" => "0"
                        ), array(
                                "status" => "1"
                        ));
                
                $message = lang('save_personal_identity_success');
                $this->success_output($message);
                return;
            }
            else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }
        
        // Get all countries
        $countries = $this->countries_m->get_many_by_many(array(), '', false, array(
                'country_name' => 'ASC'
        ));
        
        if (empty($cases_personal_identity_01)) {
            $cases_personal_identity_01 = new stdClass();
            // Loop through each validation rule
            foreach ($this->validation_rules as $rule) {
                if (APUtils::startsWith($rule['field'], "n1_")) {
                    $cases_personal_identity_01->{substr($rule['field'], 3)} = set_value($rule['field']);
                }
            }
        }
        if (empty($cases_personal_identity_02)) {
            $cases_personal_identity_02 = new stdClass();
            // Loop through each validation rule
            foreach ($this->validation_rules as $rule) {
                if (APUtils::startsWith($rule['field'], "n2_")) {
                    $cases_personal_identity_02->{substr($rule['field'], 3)} = set_value($rule['field']);
                }
            }
        }
        
        $this->template->append_js('jquery.upload-1.0.0.min.js');
        $this->template->set("countries", $countries);
        $this->template->set("personal_identity_01", $cases_personal_identity_01);
        $this->template->set("personal_identity_02", $cases_personal_identity_02);
        $this->template->set("case_id", $case_id);
        
        $this->template->build("bankaccount/personal_identify_form");
    }

    /**
     * create case function
     * @createdBy: d3jsexperts
     */
    public function company_information ()
    {
        // Check access
        $this->check_access();
        $case_id = $this->input->get_post('case_id');
        
        // Check exist
        $cases_company_information_check = $this->cases_company_information_m->get_by('case_id', $case_id);
        
        // If post
        if ($_POST) {
            $customer_id = APContext::getCustomerCodeLoggedIn();
            $input = $this->input->post();
            
            $this->form_validation->set_rules($this->validation_company_information_rules);
            if ($this->form_validation->run()) {
                
                // Insert new if this record did not exist
                if (empty($cases_company_information_check)) {
                    // Insert main director
                    $this->cases_company_information_m->insert(
                            array(
                                    "case_id" => $case_id,
                                    "company_legal" => $input['company_legal'],
                                    "company_name" => $input['company_name'],
                                    "street_address" => $input['street_address'],
                                    "post_code" => $input['post_code'],
                                    "city" => $input['city'],
                                    "region" => $input['region'],
                                    "country" => $input['country'],
                                    "website" => $input['website'],
                                    "purpose_of_company" => $input['purpose_of_company'],
                                    "registered_capital" => $input['registered_capital'],
                                    "capital_paid" => $input['capital_paid'],
                                    "phone_number" => $input['phone_number'],
                                    "email_address" => $input['email_address'],
                                    "registration_number" => $input['registration_number'],
                                    "created_date" => now()
                            ));
                }
                else {
                    // Insert main director
                    $this->cases_company_information_m->update_by_many(
                            array(
                                    "case_id" => $case_id
                            ), 
                            array(
                                    
                                    "company_legal" => $input['company_legal'],
                                    "company_name" => $input['company_name'],
                                    "street_address" => $input['street_address'],
                                    "post_code" => $input['post_code'],
                                    "city" => $input['city'],
                                    "region" => $input['region'],
                                    "country" => $input['country'],
                                    "website" => $input['website'],
                                    "purpose_of_company" => $input['purpose_of_company'],
                                    "registered_capital" => $input['registered_capital'],
                                    "capital_paid" => $input['capital_paid'],
                                    "phone_number" => $input['phone_number'],
                                    "email_address" => $input['email_address'],
                                    "registration_number" => $input['registration_number'],
                                    "created_date" => now()
                            ));
                }
                
                // Update task instance status
                $this->cases_taskname_instance_m->update_by_many(
                        array(
                                "case_id" => $case_id,
                                "base_task_name" => "company_information",
                                "status" => "0"
                        ), array(
                                "status" => "1"
                        ));
                
                $message = lang('save_company_information_success');
                $this->success_output($message);
                return;
            }
            else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }
        
        // Get all countries
        $countries = $this->countries_m->get_many_by_many(array(), '', false, array(
                'country_name' => 'ASC'
        ));
        
        // Set default value if this is case add new
        if (empty($cases_company_information_check)) {
            $cases_company_information_check = new stdClass();
            // Loop through each validation rule
            foreach ($this->validation_company_information_rules as $rule) {
                $cases_company_information_check->{$rule['field']} = set_value($rule['field']);
            }
        }
        
        $this->template->append_js('jquery.upload-1.0.0.min.js');
        $this->template->set("countries", $countries);
        $this->template->set("company_information", $cases_company_information_check);
        $this->template->set("case_id", $case_id);
        
        $this->template->build("bankaccount/company_information");
    }

    /**
     * create case function
     * @createdBy: d3jsexperts
     */
    public function document_of_company_registration ()
    {
        // Check access
        $this->check_access();
        $case_id = $this->input->get_post('case_id');
        
        // Update data to database
        $company_registration_document = $this->cases_registration_document_m->get_by_many(array(
                'case_id' => $case_id
        ));
        if (empty($company_registration_document)) {
            $company_registration_document = new stdClass();
            $company_registration_document->registraton_document_local_file_path = '';
            $company_registration_document->translate_registraton_document_local_file_path = '';
        }
        
        // If post request
        if ($_POST) {
            if (empty($company_registration_document) || empty($company_registration_document->registraton_document_local_file_path)) {
                $this->error_output(lang('company_registration_document_required'));
                return;
            }
            if (empty($company_registration_document) || empty($company_registration_document->translate_registraton_document_local_file_path)) {
                $this->error_output(lang('company_registration_translate_document_required'));
                return;
            }
            
            // Update data in database
            $this->cases_registration_document_m->update_by_many(array(
                    "case_id" => $case_id
            ), array(
                    
                    "status" => "1",
                    "created_date" => now()
            ));
            
            // Update task instance status
            $this->cases_taskname_instance_m->update_by_many(
                    array(
                            "case_id" => $case_id,
                            "base_task_name" => "document_of_company_registration",
                            "status" => "0"
                    ), array(
                            "status" => "1"
                    ));
            
            $message = lang('save_document_of_company_registration_success');
            $this->success_output($message);
            return;
        }
        
        $this->template->append_js('jquery.upload-1.0.0.min.js');
        $this->template->set("case_id", $case_id);
        $this->template->set("company_registration_document", $company_registration_document);
        $this->template->build("bankaccount/company_registration_document");
    }

    /**
     * Upload personal identity document.
     * 
     * @param
     *            : doc_type (1: identification document (passport) | 2: Notarized birth certificate)
     * @param
     *            : case_id: The case identity
     * @param
     *            : director_number (1: The first director | 2: The second director)
     */
    public function upload_personal_identity_document ()
    {
        $this->check_access();
        
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $customer = APContext::getCustomerByID($customer_id);
        $customer_code = $customer->customer_code;
        
        $doc_type = $this->input->get_post('doc_type');
        $director_number = $this->input->get_post('director_number');
        if ($doc_type != '1' && $doc_type != '2') {
            $this->error_output('Upload file fail. Input parameter doc type is invalid');
            return;
        }
        if ($director_number != '1' && $director_number != '2') {
            $this->error_output('Upload file fail. Input parameter is invalid');
            return;
        }
        
        $this->load->library('files/files');
        $case_id = $this->input->get_post('case_id');
        $case_code = sprintf('%1$08d', $case_id) . '_' . sprintf('%1$02d', $director_number);
        $upload_info = array();
        $update_data = array();
        
        // Upload file to S3
        $default_bucket_name = $this->config->item('default_bucket');
        $amazon_relate_path = '';
        
        if ($doc_type == '1') {
            $upload_info = Files::upload_case_document($case_id, $customer_code, 'n' . $director_number . '_passport_certificate', 
                    $case_code . '_passport_certificate.pdf');
            $update_data['passport_local_file_path'] = $upload_info['local_url'];
            $update_data['passport_amazon_file_path'] = $customer_id . '/cases/' . $case_code . '_passport_certificate.pdf';
            $amazon_relate_path = $customer_id . '/cases/' . $case_code . '_passport_certificate.pdf';
        }
        else if ($doc_type == '2') {
            $upload_info = Files::upload_case_document($case_id, $customer_code, 'n' . $director_number . '_birth_certificate', 
                    $case_code . '_birth_certificate.pdf');
            $update_data['birth_certificate_local_file_path'] = $upload_info['local_url'];
            $update_data['birth_certificate_amazon_file_path'] = $customer_id . '/cases/' . $case_code . '_birth_certificate.pdf';
            $amazon_relate_path = $customer_id . '/cases/' . $case_code . '_birth_certificate.pdf';
        }
        
        // Upload file to S3
        $result = S3::putObjectFile($upload_info['local_url'], $default_bucket_name, $amazon_relate_path, S3::ACL_PRIVATE);
        log_message(APConstants::LOG_DEBUG, "Upload file to Amazon: " . $upload_info['local_url']);
        if (! $result) {
            log_message(APConstants::LOG_DEBUG, "Can not upload file to Amazon: " . $amazon_relate_path);
            $this->error_output('Upload file fail.');
            return;
        }
        
        $cases_personal_identity_check = $this->cases_personal_identity_m->get_by_many(
                array(
                        'case_id' => $case_id,
                        'director_number' => $director_number
                ));
        $cases_personal_identity_id = '';
        if (empty($cases_personal_identity_check)) {
            $cases_personal_identity_id = $this->cases_personal_identity_m->insert(
                    array(
                            'case_id' => $case_id,
                            'director_number' => $director_number,
                            'created_date' => now()
                    ));
        }
        else {
            $cases_personal_identity_id = $cases_personal_identity_check->id;
        }
        
        $update_data['updated_date'] = now();
        
        // Update document file path
        $this->cases_personal_identity_m->update_by_many(array(
                'id' => $cases_personal_identity_id
        ), $update_data);
        
        $this->success_output('');
    }

    /**
     * Upload company registration document.
     * Only support doc type (1: Company registration document | 2: translation of document of registration)
     */
    public function upload_company_registration_document ()
    {
        $this->check_access();
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $customer = APContext::getCustomerByID($customer_id);
        $customer_code = $customer->customer_code;
        
        $doc_type = $this->input->get_post('doc_type');
        if ($doc_type != '1' && $doc_type != '2') {
            $this->error_output('Upload file fail.');
            return;
        }
        $this->load->library('files/files');
        
        $case_id = $this->input->get_post('case_id');
        $case_code = sprintf('%1$08d', $case_id);
        $upload_info = array();
        $update_data = array();
        
        // Upload file to S3
        $default_bucket_name = $this->config->item('default_bucket');
        $amazon_relate_path = '';
        
        if ($doc_type == '1') {
            $upload_info = Files::upload_case_document($case_id, $customer_code, 'registraton_document', $case_code . '_registraton_document');
            $update_data['registraton_document_local_file_path'] = $upload_info['local_url'];
            $update_data['registraton_document_amazon_file_path'] = $customer_id . '/cases/' . $case_code . '_registraton_document.pdf';
            $amazon_relate_path = $customer_id . '/cases/' . $case_code . '_registraton_document.pdf';
        }
        else if ($doc_type == '2') {
            $upload_info = Files::upload_case_document($case_id, $customer_code, 'translate_registraton_document', 
                    $case_code . '_translate_registraton_document');
            $update_data['translate_registraton_document_local_file_path'] = $upload_info['local_url'];
            $update_data['translate_registraton_document_amazon_file_path'] = $customer_id . '/cases/' . $case_code .
                     '_translate_registraton_document.pdf';
            $amazon_relate_path = $customer_id . '/cases/' . $case_code . '_translate_registraton_document.pdf';
        }
        
        // Upload file to S3
        $result = S3::putObjectFile($upload_info['local_url'], $default_bucket_name, $amazon_relate_path, S3::ACL_PRIVATE);
        log_message(APConstants::LOG_DEBUG, "Upload file to Amazon: " . $upload_info['local_url']);
        if (! $result) {
            log_message(APConstants::LOG_DEBUG, "Can not upload file to Amazon: " . $amazon_relate_path);
            $this->error_output('Upload file fail.');
            return;
        }
        
        // Update data to database
        $cases_registration_document_check = $this->cases_registration_document_m->get_by_many(
                array(
                        'case_id' => $case_id
                ));
        $cases_registration_document_id = '';
        if (empty($cases_registration_document_check)) {
            $cases_registration_document_id = $this->cases_registration_document_m->insert(
                    array(
                            'case_id' => $case_id,
                            'created_date' => now()
                    ));
        }
        else {
            $cases_registration_document_id = $cases_registration_document_check->id;
        }
        
        // Update document file path
        $update_data['updated_date'] = now();
        $this->cases_registration_document_m->update_by_many(array(
                'id' => $cases_registration_document_id
        ), $update_data);
        
        $this->success_output('');
        return;
    }

    /**
     * create case function
     * @createdBy: d3jsexperts
     */
    public function additional_information ()
    {
        // Check access
        $this->check_access();
        $case_id = $this->input->get_post('case_id');
        
        // Check exist
        $cases_additional_information_check = $this->cases_additional_information_m->get_by('case_id', $case_id);
        
        // If post
        if ($_POST) {
            $customer_id = APContext::getCustomerCodeLoggedIn();
            $input = $this->input->post();
            
            $this->form_validation->set_rules($this->validation_additional_information_rules);
            if ($this->form_validation->run()) {
                // Insert new if this record did not exist
                if (empty($cases_additional_information_check)) {
                    // Insert main director
                    $this->cases_additional_information_m->insert(
                            array(
                                    "case_id" => $case_id,
                                    "declaration_of_director" => $input['declaration_of_director'],
                                    "confirm_flag" => $input['confirm_flag'],
                                    "transaction_limit" => $input['transaction_limit'],
                                    "transaction_peryear" => $input['transaction_peryear'],
                                    "transaction_value_peryear" => $input['transaction_value_peryear'],
                                    "transaction_value_limit" => $input['transaction_value_limit'],
                                    "status" => APConstants::OFF_FLAG,
                                    "created_date" => now()
                            ));
                }
                else {
                    // Insert main director
                    $this->cases_additional_information_m->update_by_many(
                            array(
                                    "case_id" => $case_id
                            ), 
                            array(
                                    
                                    "declaration_of_director" => $input['declaration_of_director'],
                                    "confirm_flag" => $input['confirm_flag'],
                                    "transaction_limit" => $input['transaction_limit'],
                                    "transaction_peryear" => $input['transaction_peryear'],
                                    "transaction_value_peryear" => $input['transaction_value_peryear'],
                                    "transaction_value_limit" => $input['transaction_value_limit'],
                                    "updated_date" => now()
                            ));
                }
                
                // Update task instance status
                $this->cases_taskname_instance_m->update_by_many(
                        array(
                                "case_id" => $case_id,
                                "base_task_name" => "power_of_attorney",
                                "status" => "0"
                        ), array(
                                "status" => "1"
                        ));
                
                $message = lang('save_additional_information_success');
                $this->success_output($message);
                return;
            }
            else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }
        
        if (empty($cases_additional_information_check)) {
            $cases_additional_information_check = new stdClass();
            // Loop through each validation rule
            foreach ($this->validation_additional_information_rules as $rule) {
            	$cases_additional_information_check->{$rule['field']} = set_value($rule['field']);
            }
        }
        
        $this->template->append_js('jquery.upload-1.0.0.min.js');
        $this->template->set("case_id", $case_id);
        $this->template->set("cases_additional_information", $cases_additional_information_check);
        $this->template->build("bankaccount/additional_information");
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */