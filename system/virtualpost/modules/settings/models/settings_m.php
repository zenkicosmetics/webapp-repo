<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * PyroCMS Settings Model
 *
 * Allows for an easy interface for site settings
 *
 * @author		Dan Horrigan <dan@dhorrigan.com>
 * @author		PyroCMS Dev Team
 * @package		PyroCMS\Core\Modules\Settings\Models
 */

class Settings_m extends MY_Model {
    
    function __construct() {
        parent::__construct();
        $this->profile_table = $this->db->dbprefix('Settings');
    }

	/**
	 * Get
	 *
	 * Gets a setting based on the $where param.  $where can be either a string
	 * containing a slug name or an array of WHERE options.
	 *
	 * @access	public
	 * @param	mixed	$where
	 * @return	object
	 */
	public function get($where)
	{
		if ( ! is_array($where))
		{
			$where = array('SettingKey' => $where);
		}

		return $this->db
			->select("*, CASE WHEN ActualValue = '' THEN DefaultValue ELSE ActualValue END AS ActualValue", FALSE)
			->where($where)
			->get($this->_table)
			->row();
	}

	/**
	 * Get Many By
	 *
	 * Gets all settings based on the $where param.  $where can be either a string
	 * containing a module name or an array of WHERE options.
	 *
	 * @access	public
	 * @param	mixed	$where
	 * @return	object
	 */
	public function get_many_by($where = array())
	{
		if ( ! is_array($where))
		{
			$where = array('module' => $where);
		}

		return $this
			->select("*, CASE WHEN ActualValue = '' THEN DefaultValue ELSE ActualValue END AS ActualValue", FALSE)
			->where($where)
			->order_by('SettingOrder', 'DESC')
			->get_all();
	}

	/**
	 * Update
	 *
	 * Updates a setting for a given $slug.
	 *
	 * @access	public
	 * @param	string	$slug
	 * @param	array	$params
	 * @return	bool
	 */
	public function update($key = '', $params = array())
	{
		return $this->db->update($this->_table, $params, array('SettingCode' => $key));
	}

	/**
	 * Sections
	 *
	 * Gets all the sections (modules) from the settings table.
	 *
	 * @access	public
	 * @return	array
	 */
	public function sections()
	{
		$sections = $this->select('ModuleName')
			->distinct()
			->where('ModuleName != ""')
			->get_all();

		$result = array();

		foreach ($sections as $section)
		{
			$result[] = $section->ModuleName;
		}

		return $result;
	}

}

/* End of file settings_m.php */