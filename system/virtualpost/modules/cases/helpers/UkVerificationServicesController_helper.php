<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once('CasesController.php');
class UkVerificationServicesController extends CasesController
{

	public function personalDataForm()
	{
		var_dump($this->case_data);
		// list all
		$this->parent->template->build('user_verification/personal_data_form');
	}
}
