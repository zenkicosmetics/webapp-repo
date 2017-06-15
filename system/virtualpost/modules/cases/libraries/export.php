<?php defined('BASEPATH') or exit('No direct script access allowed');

class export
{
    /**
     * export cases completed. (#1054 verification reporting)
     *
     * @param int      $location
     * @param string   $start_date
     * @param string   $end_date
     */
    public function export_cases_completed($location, $start_date, $end_date)
    {
    	// Check exist file
    	$cases_file_path = Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . 'cases/CasesCompleted_' . $location . '_' . $start_date . '_' .$end_date . '.pdf';
    	
    	if (!is_dir(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . "cases/")) {
    		mkdir(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . "cases/", 0777, TRUE);
    		chmod(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . "cases/", 0777);
    	}
    	
    	ci()->load->model('cases_m');
   		$array_condition = array(
    			'postbox.location_available_id' => $location,
    			'cases.modified_date >=' =>  $start_date,
    			'cases.modified_date <=' =>  $end_date
    		);
    	//get complete cases from input start date and end date are approved date of customer by admin
    	$complete_cases =  ci()->cases_m->get_cases_by($array_condition);
    	// Count postbox by customer
    	$count_postbox = ci()->cases_m->count_postbox_by_customer($array_condition);
    	
    	if($complete_cases){
    		// Load pdf library
    		ci()->load->library('pdf');
    		
    		// create new PDF document
    		$pdf = ci()->pdf->createObject();
    		$pdf->setFontSubsetting(true);
    		$pdf->SetFont('freeserif', '', 10, '', 'false');
    		
    		// set document information
    		// Set common information
    		$pdf->SetTitle(Settings::get(APConstants::INSTANCE_OWNER_COMPANY_CODE));
    		$pdf->SetAuthor(Settings::get(APConstants::INSTANCE_OWNER_COMPANY_CODE));
    		
    		// disable header and footer
    		ci()->pdf->setPrintHeader(true);
    		ci()->pdf->setPrintFooter(false);
    		
    		// set default header data
    		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
    		
    		// set header and footer fonts
    		$pdf->setHeaderFont(Array(
    				PDF_FONT_NAME_MAIN,
    				'',
    				PDF_FONT_SIZE_MAIN
    				));
    		$pdf->setFooterFont(Array(
    				PDF_FONT_NAME_DATA,
    				'',
    				PDF_FONT_SIZE_DATA
    				));
    		
    		// set default monospaced font
    		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    		
    		// set auto page breaks
    		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    		
    		// image scale
    		$pdf->setImageScale(1.3);
    		
    		// set margins
    		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    		$html = ci()->load->view("cases/template_cases_completed", array(
    				'complete_cases' => $complete_cases,
    				'count_postbox' => $count_postbox,
    				'count_postbox_02' => $count_postbox
    		), TRUE);
    		
    		$pdf->AddPage();
    		$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, 'J', true);
    		
    		//Use I for "inline" to send the PDF to the browser, opposed to F to save it as a file
    		$pdf->Output($cases_file_path, 'I');
    		
    		return $cases_file_path;
    	}
    }
}