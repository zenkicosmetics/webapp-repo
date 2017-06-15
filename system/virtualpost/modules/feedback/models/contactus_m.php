<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 * @author DungNT
 */
class contactus_m extends MY_Model {
    function __construct() {
        parent::__construct();
        $this->_table = 'ContactUs';
    }
    
    
}