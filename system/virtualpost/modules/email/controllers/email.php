<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @copyright Copyright (c) 2012-2013 
 * @author Bui Duc Tien <tienbd@gmail.com>
 * @website http://www.flightpedia.org
 * @package Addons\Shared_addons\Modules\Email\Controllers
 * @created 2/19/2013
 */

class Email extends Public_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('email_m'));
		$this->lang->load('email');
	}

	public function index() {
	}
	/**
	 * View a country
	 *
	 * @param string $slug The slug of the country.
	 */
	public function view($slug = '')
	{
		if ( ! $slug or ! $email = $this->email_m->get_by('slug', $slug))
		{
			redirect('home');
		}

		if ($email->status != 'live' && ! $this->ion_auth->is_admin())
		{
			redirect('home');
		}

		$this->template->title($email->name)
			->set_metadata('description', $email->description)
			//->set_breadcrumb(lang('email.list'), 'email')
			;

		$this->template
			->set_breadcrumb($email->name)
			->set('email', $email)
			->build('view');
	}
}

/* End of file email.php */
