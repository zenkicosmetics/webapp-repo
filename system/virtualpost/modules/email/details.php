<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Email module
 *
 * @author Tommy Bui
 * @author Bui Duc Tien <tienbd@gmail.com>
 * @website http://www.flightpedia.org
 * @package Addons\Shared_addons\Modules\Email
 */
class Module_Email extends Module
{
	public $version = '1.0.0';

	public function info()
	{
		return array(
			'name' => array(
				'en' => 'Email Management',
			),
			'description' => array(
				'en' => 'Module for management Email.',
			),
			'frontend' => TRUE,
			'backend' => TRUE,
			'menu' => 'content',
			'roles' => array(
				'index','publish', 'edit', 'delete'
			),
			'sections' => array(
				'email' => array(
					'name' => 'email.list',
					'uri' => 'admin/email',
					'shortcuts' => array(
						array(
						    'name' => 'email.add',
						    'uri' => 'admin/email/create',
						    'class' => 'add'
						),
					),
				),
			),
		);
	}

	public function install()
	{
		$this->dbforge->drop_table('email');
		
		$this->db->delete('settings', array('module' => 'email'));
		
		$tables = array(
			'email' => array(
				'emailid' => array('type' => 'INT', 'constraint' => 4, 'null' => false, 'auto_increment' => true, 'primary' => true),
				'title' => array('type' => 'VARCHAR', 'constraint' => 127, 'null' => false),
				'slug' => array('type' => 'VARCHAR', 'constraint' => 127, 'null' => false),
				'status' => array('type' => 'ENUM', 'constraint' => array('draft', 'live'), 'default' => 'draft',),
				'description' => array('type' => 'VARCHAR', 'constraint' => 500, 'null' => true),
				'content' => array('type' => 'TEXT', 'constraint' => 16, 'null' => true),
				'createdon' => array('type' => 'INT', 'constraint' => 4, 'null' => true),
				'updatedon' => array('type' => 'INT', 'constraint' => 4, 'null' => true),
				'authorid' => array('type' => 'INT', 'constraint' => 4, 'null' => true),
			),
		);
 		if ( ! $this->install_tables($tables))
		{
			return false;
		}
		return true;
	}

	public function uninstall()
	{
		return true;
	}

	public function upgrade($old_version)
	{
		return true;
	}
}
/* End of file details.php */
