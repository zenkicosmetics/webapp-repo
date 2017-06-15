<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// If you need to parse XLS files, include php-excel-reader
require_once APPPATH . "/third_party/spreadsheet-reader/php-excel-reader/excel_reader2.php";

require_once APPPATH . "/third_party/spreadsheet-reader/SpreadsheetReader.php";
 
class ExcelReader extends SpreadsheetReader { 
    public function __construct($Filepath, $OriginalFilename = false, $MimeType = false) { 
        parent::__construct($Filepath, $OriginalFilename, $MimeType); 
    } 
}