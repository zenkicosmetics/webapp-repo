<?php
defined('BASEPATH') or exit('No direct script access allowed');

class UkVerificationServicesController extends CasesController
{

	public function personalDataForm()
	{
		// list all
		$this->parent->template->build('admin/index');
	}
}