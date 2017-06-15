<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once dirname(__FILE__) . '/tcpdf/tcpdf.php';

class Pdf04 extends TCPDF
{
    function __construct()
    {
        parent::__construct();
    }
    
    //Page header
    public function Header() {
        // Title
        foreach($info_customers as $info){
            $html = '<table style="background-color:#d7d7c1; border: 1px solid black;"><tr>
                   <td>Customer ID – Email – invoicing name – invoicing company name</td></tr></table>';
            $this->writeHTML($html, true,false, false,false, $align='C');
        }
     
    }
        
    public function createObject(){
        return new Pdf04();
    }
}
