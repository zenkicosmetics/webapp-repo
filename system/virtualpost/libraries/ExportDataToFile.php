<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . "/third_party/php-export-data/php-export-data.class.php";

class ExportDataToFile extends ExportDataFactory { 
    public function __construct() {
    	parent::__construct(); 
    }
}