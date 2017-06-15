<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * User controller for the users module (frontend)
 *
 * @author		 Phil Sturgeon
 * @author		PyroCMS Dev Team
 * @package		PyroCMS\Core\Modules\Users\Controllers
 */
class Users extends Public_Controller
{

	protected $errors = array();

	/**
	 * Constructor method
	 *
	 * @return \Users
	 */
	public function __construct()
	{
		parent::__construct();

		// Load the required classes
		$this->load->model('user_m');
		$this->load->helper('user');
		$this->lang->load('user');
		$this->load->library('form_validation');
		ci()->lang->load('users/user');
		$this->errors = array();
	}

	/**
	 * Show the current user's profile
	 */
	public function index()
	{
		if (isset($this->current_user->id))
		{
			$this->view($this->current_user->id);
		}
		else
		{
			redirect('users/login/users');
		}
	}

	/**
	 * View a user profile based on the ID
	 *
	 * @param int|string $id The Username or ID of the user
	 */
	public function view($id = null)
	{
		$user = ($this->current_user && $id == $this->current_user->id) ? $this->current_user : $this->ion_auth->get_user($id);

		// No user? Show a 404 error. Easy way for now, instead should show a custom error message
		$user or show_404();

		$this->template->build('profile/view', array(
			'_user' => $user,
		));
	}

	/**
	 * Let's login, shall we?
	 */
	public function login()
	{
		// Check post and session for the redirect place
		$redirect_to = ($this->input->post('redirect_to')) 
			? trim(urldecode($this->input->post('redirect_to')))
			: $this->session->userdata('redirect_to');

		// Any idea where we are heading after login?
		if ( ! $_POST AND $args = func_get_args())
		{
			$this->session->set_userdata('redirect_to', $redirect_to = implode('/', $args));
		}

		// Get the user data
		$user = (object)array(
			'email' => $this->input->post('email'),
			'password' => $this->input->post('password')
		);

		$validation = array(
			array(
				'field' => 'email',
				'label' => lang('user_email_label'),
				'rules' => 'required|trim|callback__check_login'
			),
			array(
				'field' => 'password',
				'label' => lang('user_password_label'),
				'rules' => 'required|min_length[6]|max_length[20]'
			),
		);

		// Set the validation rules
		$this->form_validation->set_rules($validation);

		// If the validation worked, or the user is already logged in
		if ($this->form_validation->run() or $this->current_user)
		{
			// Kill the session
			$this->session->unset_userdata('redirect_to');

			// Deprecated.
			$this->hooks->_call_hook('post_user_login');

			// trigger a post login event for third party devs
			Events::trigger('post_user_login');

			if ($this->input->is_ajax_request())
			{
				$user = $this->ion_auth->get_user_by_email($user->email);
				$user->password = '';
				$user->salt = '';

				exit(json_encode(array('status' => true, 'message' => lang('user_logged_in'), 'data' => $user)));
			}
			else
			{
				$this->session->set_flashdata('success', lang('user_logged_in'));
			}

			// Don't allow protocols or cheeky requests
			if (strpos($redirect_to, ':') !== FALSE)
			{
				// Just login to the homepage
				redirect('');
			}

			// Passes muster, on your way
			else
			{
				redirect($redirect_to ? $redirect_to : '');
			}
		}

		if ($_POST and $this->input->is_ajax_request())
		{
			exit(json_encode(array('status' => false, 'message' => validation_errors())));
		}

		$this->template
			->build('login', array(
				'_user' => $user,
				'redirect_to' => $redirect_to,
			));
	}

	/**
	 * Method to log the user out of the system
	 */
	public function logout()
	{
		// allow third party devs to do things right before the user leaves
		Events::trigger('pre_user_logout');

		$this->ion_auth->logout();

		if ($this->input->is_ajax_request())
		{
			exit(json_encode(array('status' => true, 'message' => lang('user_logged_out'))));
		}
		else
		{
			$this->session->set_flashdata('success', lang('user_logged_out'));
			redirect('');
		}
	}

	/**
	 * Method to register a new user
	 */
	public function register()
	{
		if (isset($this->current_user->id))
		{
			$this->session->set_flashdata('notice', lang('user_already_logged_in'));
			redirect();
		}

		/* show the disabled registration message */
		if ( ! Settings::get('enable_registration'))
		{
			$this->template
				->title(lang('user_register_title'))
				->build('disabled');
			return;
		}

		// Validation rules
		$validation = array(
			array(
				'field' => 'password',
				'label' => lang('user_password'),
				'rules' => 'required|min_length[6]|max_length[20]'
			),
			array(
				'field' => 'email',
				'label' => lang('user_email'),
				'rules' => 'required|max_length[60]|valid_email|callback__email_check',
			),
			array(
				'field' => 'username',
				'label' => lang('user_username'),
				'rules' => Settings::get('auto_username') ? '' : 'required|alpha_dot_dash|min_length[3]|max_length[20]|callback__username_check',
			),
		);

		// --------------------------------
		// Merge streams and users validation
		// --------------------------------
		// Why are we doing this? We need
		// any fields that are required to
		// be filled out by the user when
		// registering.
		// --------------------------------

		// Get the profile fields validation array from streams
		$this->load->driver('Streams');
		$profile_validation = $this->streams->streams->validation_array('profiles', 'users');

		// Remove display_name
		foreach ($profile_validation as $key => $values)
		{
			if ($values['field'] == 'display_name')
			{
				unset($profile_validation[$key]);
				break;
			}
		}

		// Set the validation rules
		$this->form_validation->set_rules(array_merge($validation, $profile_validation));

		// Get user profile data. This will be passed to our
		// streams insert_entry data in the model.
		$assignments = $this->streams->streams->get_assignments('profiles', 'users');

		// This is the required profile data we have from
		// the register form
		$profile_data = array();

		// Get the profile data to pass to the register function.
		foreach ($assignments as $assign)
		{
			if ($assign->field_slug != 'display_name')
			{
				if (isset($_POST[$assign->field_slug]))
				{
					$profile_data[$assign->field_slug] = $this->input->post($assign->field_slug);
				}
			}
		}

		// --------------------------------

		// Set the validation rules
		$this->form_validation->set_rules($validation);

		// Set default values as empty or POST values
		foreach ($validation as $rule)
		{
			$user->{$rule['field']} = $this->input->post($rule['field']) ? $this->input->post($rule['field']) : null;
		}

		// Are they TRYing to submit?
		if ($_POST)
		{
			if ($this->form_validation->run())
			{
				// Check for a bot usin' the old fashioned
				// don't fill this input in trick.
				if ($this->input->post('d0ntf1llth1s1n') !== ' ')
				{
					$this->session->set_flashdata('error', lang('user_register_error'));
					redirect(current_url());
				}

				$email = $this->input->post('email');
				$password = $this->input->post('password');

				// --------------------------------
				// Auto-Username
				// --------------------------------
				// There are no guarantees that we 
				// will have a first/last name to
				// work with, so if we don't, use
				// an alternate method.
				// --------------------------------

				if (Settings::get('auto_username'))
				{
					if ($this->input->post('first_name') and $this->input->post('last_name'))
					{
						$this->load->helper('url');
						$username = url_title($this->input->post('first_name').'.'.$this->input->post('last_name'), '-', true);
					}
					else
					{
						// If there is no first name/last name combo specified, let's
						// user the identifier string from their email address
						$email_parts = explode('@', $email);
						$username = $email_parts[0];
					}

					// Usernames absolutely need to be unique, so let's keep
					// trying until we get a unieque one
					$i = 1;

					do
					{
						$i > 1 and $username .= $i;

						++$i;
					}
					while ($this->db->where('username', $username)
						->count_all_results('users') > 0);
				}
				else
				{
					// The user specified a username, so let's use that.
					$username = $this->input->post('username');
				}

				// --------------------------------

				// Do we have a display name? If so, let's use that.
				// Othwerise we can use the username.
				if ( ! isset($profile_data['display_name']) or ! $profile_data['display_name'])
				{
					$profile_data['display_name'] = $username;
				}

				// We are registering with a null group_id so we just
				// use the default user ID in the settings.
				$id = $this->ion_auth->register($username, $password, $email, null, $profile_data);

				// Try to create the user
				if ($id > 0)
				{
					// Convert the array to an object
					$user->username = $username;
					$user->display_name = $username;
					$user->email = $email;
					$user->password = $password;

					// trigger an event for third party devs
					Events::trigger('post_user_register', $id);

					/* send the internal registered email if applicable */
					if (Settings::get('registered_email'))
					{
						$this->load->library('user_agent');

						Events::trigger('email', array(
							'name' => $user->display_name,
							'sender_ip' => $this->input->ip_address(),
							'sender_agent' => $this->agent->browser().' '.$this->agent->version(),
							'sender_os' => $this->agent->platform(),
							'slug' => 'registered',
							'email' => Settings::get('contact_email'),
						), 'array');
					}

					/* show the you need to activate page while they wait for there email */
					if (Settings::get('activation_email'))
					{
						$this->session->set_flashdata('notice', $this->ion_auth->messages());
						redirect('users/activate');
					}

					elseif (Settings::get('registered_email')
					)
						/* show the admin needs to activate you email */
					{
						$this->session->set_flashdata('notice', lang('user_activation_by_admin_notice'));
						redirect('users/register'); /* bump it to show the flash data */
					}
				}

				// Can't create the user, show why
				else
				{
					$this->template->error_string = $this->ion_auth->errors();
				}
			}
			else
			{
				// Return the validation error
				$this->template->error_string = $this->form_validation->error_string();
			}
		}

		// Is there a user hash?
		else {
			if (($user_hash = $this->session->userdata('user_hash')))
			{
				// Convert the array to an object
				$user->email = ( ! empty($user_hash['email'])) ? $user_hash['email'] : '';
				$user->username = $user_hash['nickname'];
			}
		}

		// --------------------------------
		// Create profile fields.
		// --------------------------------

		// Anything in the post?

		$this->template->set('profile_fields', $this->streams->fields->get_stream_fields('profiles', 'users', $profile_data));

		// --------------------------------

		$this->template
			->title(lang('user_register_title'))
			->set('_user', $user)
			->build('register');
	}

	// --------------------------------------------------------------------------

	/**
	 * Activate a user
	 *
	 * @param int $id The ID of the user
	 * @param string $code The activation code
	 *
	 * @return void
	 */
	public function activate($id = 0, $code = NULL)
	{
		// Get info from email
		if ($this->input->post('email'))
		{
			$this->template->activate_user = $this->ion_auth->get_user_by_email($this->input->post('email'));
			$id = $this->template->activate_user->id;
		}

		$code = ($this->input->post('activation_code')) ? $this->input->post('activation_code') : $code;

		// If user has supplied both bits of information
		if ($id AND $code)
		{
			// Try to activate this user
			if ($this->ion_auth->activate($id, $code))
			{
				$this->session->set_flashdata('activated_email', $this->ion_auth->messages());

				// Deprecated
				$this->hooks->_call_hook('post_user_activation');

				// trigger an event for third party devs
				Events::trigger('post_user_activation', $id);

				redirect('users/activated');
			}
			else
			{
				$this->template->error_string = $this->ion_auth->errors();
			}
		}

		$this->template
			->title(lang('user_activate_account_title'))
			->set_breadcrumb(lang('user_activate_label'), 'users/activate')
			->build('activate');
	}

	/**
	 * Activated page.
	 *
	 * Shows an activated messages and a login form.
	 */
	public function activated()
	{
		//if they are logged in redirect them to the home page
		if ($this->current_user)
		{
			redirect(base_url());
		}

		$this->template->activated_email = ($email = $this->session->flashdata('activated_email')) ? $email : '';

		$this->template
			->title(lang('user_activated_account_title'))
			->build('activated');
	}

	/**
	public function reset_pass($code = FALSE)
	{
		//if user is logged in they don't need to be here. and should use profile options
		if ($this->current_user)
		{
			$this->session->set_flashdata('error', lang('user_already_logged_in'));
			redirect('admin/dashboard');
		}

		if ($_POST)
		{
			$uname = $this->input->post('user_name');
			$email = $this->input->post('email');

			if ( ! ($user_meta = $this->ion_auth->get_user_by_email($email)))
			{
				$user_meta = $this->ion_auth->get_user_by_username($uname);
			}

			// have we found a user?
			if ($user_meta)
			{
				$new_password = $this->ion_auth->forgotten_password($user_meta->email);

				if ($new_password)
				{
					//set success message
					$this->template->success_string = lang('forgot_password_successful');
				}
				else
				{
					// Set an error message explaining the reset failed
					$this->template->error_string = $this->ion_auth->errors();
				}
			}
			else
			{
				//wrong username / email combination
				$this->template->error_string = lang('user_forgot_incorrect');
			}
		}

		// code is supplied in url so lets try to reset the password
		if ($code)
		{
			// verify reset_code against code stored in db
			$reset = $this->ion_auth->forgotten_password_complete($code);

			// did the password reset?
			if ($reset)
			{
				redirect('users/reset_complete');
			}
			else
			{
				// nope, set error message
				$this->template->error_string = $this->ion_auth->errors();
			}
		}

		$this->template
			->title(lang('user_reset_password_title'))
			->set_layout(FALSE)
			->build('reset_pass');
	}
	 */
	/**
	 * Reset a user's password
	 *
	 * @param bool $code
	 */
	public function reset_pass()
	{
		//if user is logged in they don't need to be here. and should use profile options
		$this->template->set_layout(FALSE);
		if ($this->current_user)
		{
			echo '<script>parent.window.location.reload();</script>';
		}

		if ($this->input->server('REQUEST_METHOD') == 'POST')
		{

			$email = $this->input->post('email');
			$this->form_validation->set_rules(
				array( array(
					'field' => 'email',
					'label' => lang('user_email'),
					'rules' => 'required|max_length[60]|valid_email',
				)
				)
			);

			if ($this->form_validation->run()) {

				ci()->load->library('users/users_api');
				
				$request_reset = users_api::request_reset_pass($email);
				
				if($request_reset["status"]){

					$this->success_output($request_reset["message"]);
					return;
				}
				else {

					$this->error_output($request_reset["message"]);
					return;
				}
				
			}
			else {
				$errors = $this->form_validation->error_json();
				echo json_encode($errors);
				return;
			}
		}
		$this->template->set_layout(FALSE);
		$this->template->build('reset_pass');
	}

	public function reset_pass_complete(){

		ci()->load->library('users/users_api');
		$key   = $this->input->get_post("key");
		$email = $this->input->get_post('email');
		if(empty($key) || empty($email))
		{
			$this->session->sess_destroy();
			redirect(APContext::getAssetPath().'admin');
		}


		$user = users_api::getUser($email);
		$full_url = APContext::getAssetPath()."users/reset_pass_complete?email=".$user->email."&key=".$key;
		if( is_object($user) && ($user->forgotten_password_code == $key) && ($user->forgotten_password_time >= now()))
		{
			if ($this->input->server('REQUEST_METHOD') == 'POST') {

				$this->form_validation->set_rules(array(
					array(
						'field' => 'new_password',
						'label' => lang('user_reset_new_password'),
						'rules' => 'required|min_length[6]|max_length[20]'
					)
				));
				if ($this->form_validation->run())
				{
					$new_pass         = $this->input->post("new_password");
					$confirm_new_pass = $this->input->post("confirm_new_password");

					if($new_pass != $confirm_new_pass){
						$this->session->set_flashdata('error_string',lang('reset_pass_new_pass_not_same_confirm_new_passs'));
						redirect($full_url);
					}

					$check = users_api::updatePassword($new_pass,$user);
					if(!$check) {
						$this->session->set_flashdata('error_string',lang('reset_pass_error'));
						redirect($full_url);
					}

					redirect(APContext::getAssetPath().'admin/login?change_pass=1');
				}
				else {
					$errors = $this->form_validation->error_json();
					$this->session->set_flashdata('error_string',$errors['message']);
					redirect($full_url);
				}
			}
			$this->template->set("email",$email);
			$this->template->set("key", $key);
			$this->template->title(lang('user_reset_password_title'));
			$this->template->set_layout(FALSE);
			$this->template->build('reset_pass_complete');

		}
		else {
			$this->session->sess_destroy();
			redirect(APContext::getAssetPath().'admin');
		}

	}
	/**
	 * Password reset is finished
	 */
	public function reset_complete()
	{
		//if user is logged in they don't need to be here. and should use profile options
		if ($this->current_user)
		{
			$this->session->set_flashdata('error', lang('user_already_logged_in'));
			redirect('admin/dashboard');
		}

		$this->template
			->title(lang('user_password_reset_title'))
			->build('reset_pass_complete');
	}

	/**
	 * Edit Profile
	 *
	 * @param int $id
	 */
	public function edit($id = 0)
	{
		if ($this->current_user AND $this->current_user->GroupName === 'admin' AND $id > 0)
		{
			$user = $this->user_m->get(array('id' => $id));
		}
		else
		{
			$user = $this->current_user or redirect('users/login/users/edit'.(($id > 0) ? '/'.$id : ''));
		}

		$profile_data = array(); // For our form

		// Get the profile data
		$profile_row = $this->db->limit(1)
			->where('user_id', $this->current_user->id)->get('profiles')->row();

		// If we have API's enabled, load stuff
		if (Settings::get('api_enabled') and Settings::get('api_user_keys'))
		{
			$this->load->model('api/api_key_m');
			$this->load->language('api/api');

			$api_key = $this->api_key_m->get_active_key($user->id);
		}

		$this->validation_rules = array(
			array(
				'field' => 'email',
				'label' => lang('user_email'),
				'rules' => 'required|xss_clean|valid_email'
			),
			array(
				'field' => 'display_name',
				'label' => lang('profile_display_name'),
				'rules' => 'required|xss_clean'
			)
		);

		// --------------------------------
		// Merge streams and users validation
		// --------------------------------

		// Get the profile fields validation array from streams
		$this->load->driver('Streams');
		$profile_validation = $this->streams->streams->validation_array('profiles', 'users', 'edit', array(), $profile_row->id);

		// Set the validation rules
		$this->form_validation->set_rules(array_merge($this->validation_rules, $profile_validation));

		// Get user profile data. This will be passed to our
		// streams insert_entry data in the model.
		$assignments = $this->streams->streams->get_assignments('profiles', 'users');

		// --------------------------------

		// Settings valid?
		if ($this->form_validation->run())
		{
			show_error(lang('global:demo_restrictions'));

			// Get our secure post
			$secure_post = $this->input->post();

			$user_data = array(); // Data for our user table
			$profile_data = array(); // Data for our profile table

			// --------------------------------
			// Deal with non-profile fields
			// --------------------------------
			// The non-profile fields are:
			// - email
			// - password
			// The rest are streams
			// --------------------------------

			$user_data['email'] = $secure_post['email'];

			// If password is being changed (and matches)
			if ($secure_post['password'])
			{
				$user_data['password'] = $secure_post['password'];
				unset($secure_post['password']);
			}

			// --------------------------------
			// Set the language for this user
			// --------------------------------

			if (isset($secure_post['lang']) and $secure_post['lang'])
			{
				$this->ion_auth->set_lang($secure_post['lang']);
				$_SESSION['lang_code'] = $secure_post['lang'];
			}

			// --------------------------------
			// The profile data is what is left
			// over from secure_post.
			// --------------------------------

			$profile_data = $secure_post;

			if ($this->ion_auth->update_user($user->id, $user_data, $profile_data) !== FALSE)
			{
				Events::trigger('post_user_update');
				$this->session->set_flashdata('success', $this->ion_auth->messages());
			}
			else
			{
				$this->session->set_flashdata('error', $this->ion_auth->errors());
			}

			redirect('users/edit'.(($id > 0) ? '/'.$id : ''));
		}
		else
		{
			// --------------------------------
			// Grab user data
			// --------------------------------
			// Currently just the email.
			// --------------------------------		

			if (isset($_POST['email']))
			{
				$user->email = $_POST['email'];
			}
		}

		// --------------------------------
		// Grab user profile data
		// --------------------------------

		foreach ($assignments as $assign)
		{
			if (isset($_POST[$assign->field_slug]))
			{
				$profile_data[$assign->field_slug] = $this->input->post($assign->field_slug);
			}
			else
			{
				$profile_data[$assign->field_slug] = $profile_row->{$assign->field_slug};
			}
		}

		// --------------------------------
		// Run Stream Events
		// --------------------------------

		$profile_stream_id = $this->streams_m->get_stream_id_from_slug('profiles', 'users');
		$this->fields->run_field_events($this->streams_m->get_stream_fields($profile_stream_id), array());

		// --------------------------------

		// Render the view
		$this->template->build('profile/edit', array(
			'_user' => $user,
			'display_name' => $profile_row->display_name,
			'profile_fields' => $this->streams->fields->get_stream_fields('profiles', 'users', $profile_data),
			'api_key' => isset($api_key) ? $api_key : null,
		));
	}

	/**
	 * Callback method used during login
	 *
	 * @param str $email The Email address
	 *
	 * @return bool
	 */
	public function _check_login($email)
	{
		$remember = false;
		if ($this->input->post('remember') == 1)
		{
			$remember = true;
		}

		if ($this->ion_auth->login($email, $this->input->post('password'), $remember))
		{
			return true;
		}

		$this->form_validation->set_message('_check_login', $this->ion_auth->errors());
		return false;
	}

	/**
	 * Username check
	 *
	 * @author Ben Edmunds
	 *
	 * @param string $username The username to check.
	 *
	 * @return bool
	 */
	public function _username_check($username)
	{
		if ($this->ion_auth->username_check($username))
		{
			$this->form_validation->set_message('_username_check', lang('user_error_username'));
			return false;
		}

		return true;
	}

	/**
	 * Email check
	 *
	 * @author Ben Edmunds
	 *
	 * @param string $email The email to check.
	 *
	 * @return bool
	 */
	public function _email_check($email)
	{
		if ($this->ion_auth->email_check($email))
		{
			$this->form_validation->set_message('_email_check', lang('user_error_email'));
			return false;
		}

		return true;
	}

}