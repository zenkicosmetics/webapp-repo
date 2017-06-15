<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Feedback module
 *
 * @author Tommy Bui
 * @author Bui Duc Tien <tienbd@gmail.com>
 * @website http://www.flightpedia.org
 * @package Addons\Shared_addons\Modules\Feedback
 */
class Module_Feedback extends Module
{
	public $version = '1.0.0';

	public function info()
	{
		return array(
			'name' => array(
				'en' => 'Feedback Management',
			),
			'description' => array(
				'en' => 'Module for management Feedback.',
			),
			'frontend' => TRUE,
			'backend' => TRUE,
			'menu' => 'content',
			'roles' => array(
				'index','publish', 'edit', 'delete'
			),
			'sections' => array(
				'feedback' => array(
					'name' => 'feedback.list',
					'uri' => 'admin/feedback',
					'shortcuts' => array(
						array(
						    'name' => 'feedback.add',
						    'uri' => 'admin/feedback/create',
						    'class' => 'add'
						),
					),
				),
			),
		);
	}

	public function install()
	{
		$this->dbforge->drop_table('feedback');
		
		$this->db->delete('settings', array('module' => 'feedback'));
		
		$tables = array(
			'feedback' => array(
				'feedbackid' => array('type' => 'INT', 'constraint' => 4, 'null' => false, 'auto_increment' => true, 'primary' => true),
				'name' => array('type' => 'VARCHAR', 'constraint' => 127, 'null' => false),
				'subject' => array('type' => 'VARCHAR', 'constraint' => 127, 'null' => false),
				'status' => array('type' => 'ENUM', 'constraint' => array('draft', 'live'), 'default' => 'draft',),
				'message' => array('type' => 'TEXT', 'constraint' => 16, 'null' => true),
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
