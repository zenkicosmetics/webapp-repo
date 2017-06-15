<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once dirname(__FILE__) . '/tcpdf/tcpdf.php';

class Pdf02 extends TCPDF
{
    function __construct()
    {
        parent::__construct();
    }
    
    public function createObject(){
        return new Pdf02();
    }
    
    //Page header
    public function Header() {
    	// Logo
    	// $image_file = APContext::getAssetPath().'/images/invoice-pdf-header.png';
    	$image_file = Settings::get(APConstants::SITE_LOGO_WHITE_CODE);
    	if (!empty($image_file)) {
    		if (APUtils::startsWith($image_file, '/')) {
    			$image_file = substr($image_file, 1);
    		}
    	} else {
    		$image_file = APContext::getAssetPath().'/images/invoice-pdf-header.png';
    	}
    	$this->Image($image_file, 15, 10, 50, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
    
    	// print an ending header line
    	$this->SetLineStyle(array('width' => 0.85 / $this->k, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
    	$imgy = $this->getImageRBY();
    	$this->SetY((2.835 / $this->k) + max($imgy, $this->y));
    	if ($this->rtl) {
    		$this->SetX($this->original_rMargin);
    	} else {
    		$this->SetX($this->original_lMargin);
    	}
    	$this->Cell(($this->w - $this->original_lMargin - $this->original_rMargin + 3), 0, '', 'T', 0, 'C');
    }
}
