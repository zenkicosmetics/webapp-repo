<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once dirname(__FILE__) . '/tcpdf/tcpdf.php';

class Pdf extends TCPDF
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
    
    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-22);
        // Set font
        $this->SetFont('arial', '', 8);
        //$this->writeHTMLCell(0, 0, '', '', '<hr />', 0, 1, 0, true, 'J', true);
        
        $footerHTML = '<hr /><table>
                        	<tr>
                        		<td width="20%">
                        			<table>
                        				<tr>
                        					<td>'.Settings::get(APConstants::INSTANCE_OWNER_COMPANY_CODE).'</td>
                        				</tr>
                        				<tr>
                        					<td>'.Settings::get(APConstants::INSTANCE_OWNER_STREET_CODE).'</td>
                        				</tr>
                        				<tr>
                        					<td>'.Settings::get(APConstants::INSTANCE_OWNER_PLZ_CODE).' '.Settings::get(APConstants::INSTANCE_OWNER_CITY_CODE).'</td>
                        				</tr>
                        				<tr>
                        					<td>'.Settings::get(APConstants::INSTANCE_OWNER_COUNTRY_CODE).'</td>
                        				</tr>
                        			</table>
                        		</td>
                        		<td width="25%">
                        			<table>
                        				<tr>
                        					<td>'.Settings::get(APConstants::INSTANCE_OWNER_WEBSITE_CODE).'</td>
                        				</tr>
                        				<tr>
                        					<td>'.Settings::get(APConstants::INSTANCE_OWNER_MAIL_INVOICE_CODE).'</td>
                        				</tr>
                        				<tr>
                        					<td>Telefon: '.Settings::get(APConstants::INSTANCE_OWNER_TEL_INVOICE_CODE).'</td>
                        				</tr>
                        				<tr>
                        					<td>Fax: '.Settings::get(APConstants::INSTANCE_OWNER_FAX_CODE).'</td>
                        				</tr>
                        			</table>
                        		</td>
                        		<td width="25%">
                        			<table>
                        				<tr>
                        					<td>Us-St. ID: '.Settings::get(APConstants::INSTANCE_OWNER_VAT_NUM_CODE).'</td>
                        				</tr>
                        				<tr>
                        					<td>Registered number: '.Settings::get(APConstants::INSTANCE_OWNER_REGISTERED_NUM_CODE).'</td>
                        				</tr>
                        				<tr>
                        					<td>Directors: '.Settings::get(APConstants::INSTANCE_OWNER_DIRECTOR_CODE).'</td>
                        				</tr>
                        			    <tr>
                        					<td>&nbsp;</td>
                        				</tr>
                        			</table>
                        		</td>
                        		<td width="30%">
                        			<table>
                        				<tr>
                        					<td>'.Settings::get(APConstants::INSTANCE_OWNER_PLACE_REGISTRATION_CODE).'</td>
                        				</tr>
                        				<tr>
                        					<td>Konto: '.Settings::get(APConstants::INSTANCE_OWNER_ACCOUNTNUMBER_CODE).', BLZ: '.Settings::get(APConstants::INSTANCE_OWNER_BANKCODE_CODE).'</td>
                        				</tr>
                        				<tr>
                        					<td>IBAN: '.Settings::get(APConstants::INSTANCE_OWNER_IBAN_CODE).'</td>
                        				</tr>
                        				<tr>
                        					<td>BIC:'.Settings::get(APConstants::INSTANCE_OWNER_SWIFT_CODE).'</td>
                        				</tr>
                        			</table>
                        		</td>
                        	</tr>
                        </table>';
        
        // Page number
        //$this->writeHTMLCell(0, 0, '', '', $footerHTML, 0, 1, 0, true, 'J', true);
        $this->setCellPaddings(0,-20,0,0);
        $this->writeHTML($footerHTML, true, false, true, false, "");
        
        //$this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
    
    public function createObject(){
        return new Pdf();
    }
}
