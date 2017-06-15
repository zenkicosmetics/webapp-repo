<?php defined('BASEPATH') or exit('No direct script access allowed');

class Module_Files extends Module {

	public $version = '2.0';

	public function info()
	{
		return array(
			'name' => array(
				'en' => 'Files',
			),
			'description' => array(
				'en' => 'Manages files and folders for your site.',
			),
			'frontend' => FALSE,
			'backend' => TRUE,
			'menu' => 'content',
			'roles' => array(
				'wysiwyg', 'upload',
			)
		);
	}

	public function install()
	{
		return true;
	}

	public function uninstall()
	{
		// This is a core module, lets keep it around.
		return false;
	}

	public function upgrade($old_version)
	{
		return true;
	}

}