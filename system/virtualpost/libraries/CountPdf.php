<?php

if (! defined('BASEPATH'))
    exit('No direct script access allowed');

require_once dirname(__FILE__) . '/FPDF/fpdf.php';
require_once dirname(__FILE__) . '/FPDI/fpdi.php';
class CountPdf {
    function __construct() {
    }
    
    //Page header
    public static function getTotalPage($fullPathToPDF) {
        $pdf = new FPDI();
        $pageCount = $pdf->setSourceFile($fullPathToPDF);
        
        return $pageCount;
    }
    
    //Page header
    public static function getTotalPageByExternalTool($fullPathToPDF) {
        try {
            $cmd = Settings::get(APConstants::PDF_INFO_DIR_KEY);
            $full_cmd = "$cmd \"$fullPathToPDF\"";
            log_message(APConstants::LOG_DEBUG, $full_cmd);
            // Parse entire output
            // Surround with double quotes if file name has spaces
            exec($full_cmd, $output);
            
            // Iterate through lines
            $pagecount = 0;
            foreach ( $output as $op ) {
                // Extract the number
                if (preg_match("/Pages:\s*(\d+)/i", $op, $matches) === 1) {
                    $pagecount = intval($matches [1]);
                    break;
                }
            }
            
            return $pagecount;
        } catch (Exception $e ) {
            log_message(APConstants::LOG_ERROR, 'Count pdf number error.'.$e);
            return 1;
        }
    }
}
