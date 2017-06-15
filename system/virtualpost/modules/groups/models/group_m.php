<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Group model
 *
 *
 */
class Group_m extends MY_Model
{
	/**
	 * Check a rule based on it's role
	 *
	 * @access public
	 * @param string $role The role
	 * @param array $location
	 * @return mixed
	 */
	public function check_rule_by_role($role, $location)
	{
		// No more checking to do, admins win
		if ( $role == 1 )
		{
			return TRUE;
		}

		// Check the rule based on whatever parts of the location we have
		if ( isset($location['module']) )
		{
			 $this->db->where('(module = "'.$location['module'].'" or module = "*")');
		}

		if ( isset($location['controller']) )
		{
			 $this->db->where('(controller = "'.$location['controller'].'" or controller = "*")');
		}

		if ( isset($location['method']) )
		{
			 $this->db->where('(method = "'.$location['method'].'" or method = "*")');
		}

		// Check which kind of user?
		$this->db->where('g.id', $role);

		$this->db->from('Permissions p');
		$this->db->join('Groups as g', 'g.id = p.group_id');

		$query = $this->db->get();

		return $query->num_rows() > 0;
	}

	/**
	 * Return an array of groups
	 *
	 * @access public
	 * @param array $params Optional parameters
	 * @return array
	 */
	public function get_all($params = array())
	{
		if ( isset($params['except']) )
		{
			$this->db->where_not_in('Name', $params['except']);
		}

		return parent::get_all();
	}

	/**
	 * Add a group
	 *
	 * @access public
	 * @param array $input The data to insert
	 * @return array
	 */
	public function insert($input = array())
	{
		return parent::insert(array(
			'Name'			        => $input['Name'],
			'Description'	        => $input['Description'],
		));
	}

	/**
	 * Update a group
	 *
	 * @access public
	 * @param int $id The ID of the role
	 * @param array $input The data to update
	 * @return array
	 */
	public function update($id = 0, $input = array())
	{
		return parent::update($id, array(
			'Name'					=> $input['Name'],
			'Description'			=> $input['Description'],
		));
	}

	/**
	 * Delete a group
	 *
	 * @access public
	 * @param int $id The ID of the group to delete
	 * @return
	 */
	public function delete($id = 0)
	{
		$this->load->model('users/user_m');
		
		// don't delete the group if there are still users assigned to it
		if ($this->user_m->count_by_many(array('GroupID' => $id)) > 0)
		{
			return FALSE;
		}

		// Dont let them delete the "admin" group or the "user" group.
		// The interface does not have a delete button for these, this is just insurance
		$this->db->where_not_in('Name', array('user', 'admin'));

		return parent::delete($id);
	}
	
	/**
	 * Gets al groups by partner
	 * @param unknown $params
	 */
	public function get_group_by_partner($params){
	    $this->db->where_in("id", $params);
	    return parent::get_all();
	}
}