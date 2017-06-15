<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once dirname(__FILE__) . '/tcpdf/tcpdf.php';

class Pdf01 extends TCPDF
{
    function __construct()
    {
        parent::__construct();
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
        // Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)
        // $this->Image($image_file, 10, 10, 35, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        $html_image = '<div style="align:left">
                <img src="'. $image_file .'" alt="ClevverMail logo" width="150">
              </div><br>';
        $this->writeHTML($html_image, true,false, false,false, 'L');
        // Title
        // writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)
        //writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
        $html = '<table style="border-top:1px solid blue;border-bottom:1px solid black;" width="100%">
               <tr><td><span style="align:center;font-size:65px;"> ClevverMail-Partner Reporting </span> </td></tr>
              </table><table><tr><td></td></tr></table>';
        $this->writeHTML($html, true,false, false,false, 'C');
        
        // print an ending header line
//        $this->SetLineStyle(array('width' => 0.85 / $this->k, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
//        $imgy = $this->getImageRBY();
//        $this->SetY((2.835 / $this->k) + max($imgy, $this->y));
//        if ($this->rtl) {
//            $this->SetX($this->original_rMargin);
//        } else {
//            $this->SetX($this->original_lMargin);
//        }
//        $this->Cell(($this->w - $this->original_lMargin - $this->original_rMargin + 3), 0, '', 'T', 0, 'C');
    }
    
    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-22);
        // Set font
        $this->SetFont('arial', '', 8);
        //$this->writeHTMLCell(0, 0, '', '', '<hr />', 0, 1, 0, true, 'J', true);
        
        $footerHTML = '<hr /><div align="center"><table>
                        	<tr>
                        		<td ><a href="'.Settings::get(APConstants::INSTANCE_OWNER_WEBSITE_CODE).'">'.Settings::get(APConstants::INSTANCE_OWNER_WEBSITE_CODE). '</a> - ' .'
                        		<a href="'.Settings::get(APConstants::INSTANCE_OWNER_MAIL_SALES_CODE).'">'.Settings::get(APConstants::INSTANCE_OWNER_MAIL_SALES_CODE).'</a> - '.
                                Settings::get(APConstants::INSTANCE_OWNER_TEL_SUPPORT_CODE).'</td>	
                        	</tr>
                        </table></div>';
        
        // Page number
//        $this->writeHTMLCell(0, 0, '', '', $footerHTML, 0, 1, 0, true, 'J', true);
        $this->setCellPaddings(0,-10,0,0);
        $this->writeHTML($footerHTML, true, false, true, false, "");
        
        //$this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
    
    public function createObject(){
        return new Pdf01();
    }
}
