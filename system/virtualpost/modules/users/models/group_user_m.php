<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *
 * @author		PyroCMS Dev Team
 * @package		PyroCMS\Core\Modules\Users\Models
 */
class Group_user_m extends MY_Model {

    function __construct() {
        parent::__construct();

        $this->_table = $this->db->dbprefix('group_users');
        $this->primary_key = "id";
    }
    
    public function get_selected_group_by($user_id){
        $this->db->select("group_users.*, groups.name, groups.description");
        $this->db->from("group_users");
        $this->db->join("groups", "group_users.group_id= groups.id", 'inner');
        $this->db->where("group_users.user_id", $user_id);
        
        return $this->db->get()->result();
    }

}
