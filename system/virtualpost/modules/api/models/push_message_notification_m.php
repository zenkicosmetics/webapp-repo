<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 * @author DungNT
 */
class push_message_notification_m extends MY_Model {
    function __construct() {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('push_message_notification');
        $this->primary_key = 'id';
    }
    
}