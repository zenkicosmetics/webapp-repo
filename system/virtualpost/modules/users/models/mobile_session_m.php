<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author		PyroCMS Dev Team
 * @package		PyroCMS\Core\Modules\Users\Models
 */
class mobile_session_m extends MY_Model
{
    function __construct() {
    	parent::__construct();
    
    	$this->_table = $this->profile_table = $this->db->dbprefix('mobile_sessions');
    	$this->primary_key = 'session_key';
    }
}