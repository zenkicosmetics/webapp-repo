<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 * @author TienNH
 */
class location_pricing_m extends MY_Model {
    public function __construct() {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('location_pricing');
    }
}