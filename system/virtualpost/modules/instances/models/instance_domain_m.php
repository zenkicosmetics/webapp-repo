<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 * @author TienNH
 */
class instance_domain_m extends MY_SupperAdminModel {
    // Declare supprt admin database instance
    private $supperadmin_db;
    
    function __construct() {
        parent::__construct();
        $this->supperadmin_db = $this->load->database('supper_admin', TRUE);
        $this->_table = $this->profile_table = $this->supperadmin_db->dbprefix('instance_domain');
        $this->primary_key = 'instance_id';
    }
    
    
}