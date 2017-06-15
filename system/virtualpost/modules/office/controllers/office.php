<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Office extends AccountSetting_Controller {

    /**
     * Validation for basic profile data.
     * The rest of the validation is built by streams.
     *
     * @var array
     */
    private $validation_rules = array(
        array(
            'field' => 'your_name',
            'label' => 'Your name',
            'rules' => 'required|max_length[100]'
        ),
        array(
            'field' => 'your_email',
            'label' => 'Your email',
            'rules' => 'required|valid_email|max_length[100]'
        ),
        array(
            'field' => 'your_phone',
            'label' => 'Your phone',
            'rules' => 'required|trim|max_length[30]'
        ),
        array(
            'field' => 'booking_request',
            'label' => 'Booking request',
            'rules' => 'required'
        )
    );

    /**
     * Loads the gazillion of stuff, in Flash Gordon speed.
     * 
     * @todo Document properly please.
     */
    public function __construct() {
        parent::__construct();

        // load the theme_example view
        $this->load->model('addresses/location_m');
        $this->load->model('office/location_office_m');
        $this->load->model('office/location_office_feature_m');
        $this->load->model('office/location_office_booking_request_m');
        $this->load->model('settings/countries_m');
        $this->load->model('email/email_m');
        $this->load->model('partner/partner_m');
        $this->load->model('users/user_m');
        $this->lang->load('office/message');
    }

    /**
     * Index Page for this controller.
     * Maps to the following URL
     * http://example.com/index.php/welcome
     * - or -
     * http://example.com/index.php/welcome/index
     * - or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * 
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    public function index() {
        $this->load_location();
        // load the theme_example view
        $this->template->build('index');
    }

    /**
     * Display book request form
     */
    public function book_request_form() {
        $this->template->set_layout(FALSE);
        $location_id = $this->input->get_post('location_id');
        $location = $this->location_m->get_by('id', $location_id);
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $customer = APContext::getCustomerLoggedIn();
        $customer_address = CustomerUtils::getCustomerAddressByID($customer_id);

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $this->form_validation->set_rules($this->validation_rules);
            if ($this->form_validation->run()) {
                $your_name = $this->input->get_post('your_name');
                $your_email = $this->input->get_post('your_email');
                $your_phone = $this->input->get_post('your_phone');
                $booking_request = $this->input->get_post('booking_request');
                
                // Dang ky thong tin vao postbox
                $this->location_office_booking_request_m->insert(array(
                    "customer_id" => $customer_id,
                    "location_id" => $location_id,
                    "your_name" => $your_name,
                    "your_email" => $your_email,
                    "your_phone" => $your_phone,
                    "booking_request" => $booking_request,
                    "created_date" => now()
                ));

                // Send email confirm for user
                $data = array(
                    "slug" => APConstants::send_booking_request,
                    // Replace content
                    "customer" => $customer,
                    "location_name" => $location->location_name,
                    "your_name" => $your_name,
                    "your_email" => $your_email,
                    "your_phone" => $your_phone,
                    "booking_request" => $booking_request
                );
                // Clevvermail admin
                $admin_clevvermail = 'admin@clevvermail.com';
                
                // Get partner email
                $partner_email = $location->booking_email_address;
                
                $data['to_email'] = $admin_clevvermail;
                // Send email
                MailUtils::sendEmailByTemplate($data);
                if (!empty($partner_email)) {
                    $data['to_email'] = $partner_email;
                    // Send email
                    MailUtils::sendEmailByTemplate($data);
                }

                $message = lang('book_request_success');
                $this->success_output($message);
                return;
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }

        // Display the current page
        $this->template->set('customer_address', $customer_address);
        $this->template->set('customer', $customer)->set('location', $location)->build('book_request_form');
    }


    /**
     * Get location
     */
    private function load_location() {
        // Load main location
        $list_location = $this->location_m->get_many_by_many(array(
            //'location.public_flag' => APConstants::ON_FLAG,
            'location.shared_office_space_flag' => APConstants::ON_FLAG,
            'location.office_space_active_flag' => APConstants::ON_FLAG
        ));
        $this->template->set('list_location', $list_location);
        
        if (empty($list_location) || count($list_location) == 0) {
            return;
        }
        
        // Load feature
        foreach ($list_location as $item) {
            $location_office = $this->location_office_m->get_by_many(array(
                'location_id' => $item->id
            ));
            $item->location_office = $location_office;
            
            $country = $this->countries_m->get_by_many(array(
                'id' => $item->country_id
            ));
            $item->country = $country;
            
            if (!empty($location_office)) {
                $list_location_office_feature = $this->location_office_feature_m->get_many_by_many(array(
                    'office_id' => $location_office->id
                ), '', '' , array('order_id' => 'asc'));
                $item->list_location_office_feature = $list_location_office_feature;
            }
        }
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */