<?php defined('BASEPATH') or exit('No direct script access allowed');

class CasesValidator {
    public static $special_rules = array(
        array(
            'field' => 'name_of_applicant',
            'label' => '"Name of Applicant"',
            'rules' => 'trim|required|max_length[255]|xss_clean'
        ),
        array(
            'field' => 'street_of_applicant',
            'label' => '"Street Address of Applicant"',
            'rules' => 'trim|required|max_length[255]|xss_clean'
        ),
        array(
            'field' => 'city_of_applicant',
            'label' => '"City Address of Applicant"',
            'rules' => 'trim|required|max_length[255]|xss_clean'
        ),
        array(
            'field' => 'region_of_applicant',
            'label' => '"Region Address of Applicant"',
            'rules' => 'trim|required|max_length[255]|xss_clean'
        ),
        array(
            'field' => 'postcode_of_applicant',
            'label' => '"Postcode Address of Applicant"',
            'rules' => 'trim|required|max_length[12]|xss_clean'
        ),
        array(
            'field' => 'phone_of_applicant',
            'label' => '"Applicant Telephone Number"',
            'rules' => 'trim|required|max_length[30]|xss_clean'
        ),
        array(
            'field' => 'id_of_applicant',
            'label' => '"ID of Applicant"',
            'rules' => 'trim|required|max_length[50]|xss_clean'
        ),
        array(
            'field' => 'id_of_applicant_verification_txt',
            'label' => '"Verification file for ID of Applicant"',
            'rules' => 'trim|required|xss_clean'
        ),
        array(
            'field' => 'license_of_applicant',
            'label' => '"License of Applicant"',
            'rules' => 'trim|required|max_length[50]|xss_clean'
        ),
        array(
            'field' => 'license_of_applicant_verification_txt',
            'label' => '"Verification file for License of Applicant"',
            'rules' => 'trim|required|xss_clean'
        ),
        array(
            'field' => 'street_of_corporation',
            'label' => '"Street Address of Corporation"',
            'rules' => 'trim|max_length[255]|xss_clean'
        ),
        array(
            'field' => 'city_of_corporation',
            'label' => '"City Address of Corporation"',
            'rules' => 'trim|max_length[255]|xss_clean'
        ),
        array(
            'field' => 'region_of_corporation',
            'label' => '"Region Address of Corporation"',
            'rules' => 'trim|max_length[255]|xss_clean'
        ),
        array(
            'field' => 'postcode_of_corporation',
            'label' => '"Postcode Address of Corporation"',
            'rules' => 'trim|max_length[255]|xss_clean'
        ),
        array(
            'field' => 'phone_of_corporation',
            'label' => '"Business Telephone Number"',
            'rules' => 'trim|max_length[255]|xss_clean'
        ),
//        array(
//            'field' => 'business_type_of_corporation',
//            'label' => '"Type of Business"',
//            'rules' => 'trim|max_length[255]|xss_clean'
//        )
//        array(
//            'field' => 'note1',
//            'label' => '"Note 01"',
//            'rules' => 'trim|max_length[1000]|xss_clean'
//        ),
//        array(
//            'field' => 'note2',
//            'label' => '"Note 02"',
//            'rules' => 'trim|max_length[1000]|xss_clean'
//        ),
//        array(
//            'field' => 'note3',
//            'label' => '"Note 03"',
//            'rules' => 'trim|max_length[1000]|xss_clean'
//        )
        );

    public static $sub_rules = array(
        array(
            'field' => 'street_of_corporation',
            'label' => '"Street Address of Corporation"',
            'rules' => 'required'
        ),
        array(
            'field' => 'city_of_corporation',
            'label' => '"City Address of Corporation"',
            'rules' => 'required'
        ),
        array(
            'field' => 'region_of_corporation',
            'label' => '"Region Address of Corporation"',
            'rules' => 'required'
        ),
        array(
            'field' => 'postcode_of_corporation',
            'label' => '"Postcode Address of Corporation"',
            'rules' => 'required'
        ),
        array(
            'field' => 'phone_of_corporation',
            'label' => '"Business Telephone Number"',
            'rules' => 'required'
        ),
//        array(
//            'field' => 'business_type_of_corporation',
//            'label' => '"Type of Business"',
//            'rules' => 'required'
//        )
//        array(
//            'field' => 'note1',
//            'label' => '"Note 01"',
//            'rules' => 'required'
//        )
        );
    
    public static $person_verify_rules = array(
    	array(
            'field' => 'passport_verification_txt',
            'label' => '"passport verification"',
            'rules' => 'required'
        ),
    	array(
            'field' => 'driver_license_file_txt',
            'label' => '"driver license file"',
            'rules' => 'required'
        ));
    
    public static $company_hard_verify_rules = array(
    	array(
    		'field' => 'passport_verification_txt',
    		'label' => 'upload company registration document please,',
    		'rules' => 'required'
    	));
    
    public static $sub_company_hard_verify_rules_01 = array(
    	array(
    		'field' => 'shareholders[1][rate]',
    		'label' => 'Shareholder rate 01',
    		'rules' => 'required|numeric|greater_than[24.9]'
    	),
    	array(
    		'field' => 'shareholders_file_name_txt_01',
    		'label' => 'file name 01',
    		'rules' => 'required'
    	),
    	array(
    		'field' => 'shareholders[2][rate]',
    		'label' => 'Shareholder rate 02',
    		'rules' => 'required|numeric|greater_than[24.9]'
    	),
    	array(
    		'field' => 'shareholders_file_name_txt_02',
    		'label' => 'file name 02',
    		'rules' => 'required'
    	),
    	array(
    		'field' => 'shareholders[3][rate]',
    		'label' => 'Shareholder rate 03',
    		'rules' => 'required|numeric|greater_than[24.9]'
        ),
    	array(
    		'field' => 'shareholders_file_name_txt_03',
    		'label' => 'file name 03',
    		'rules' => 'required'
    	),
     	array(
    		'field' => 'shareholders[4][rate]',
    		'label' => 'Shareholder rate 04',
    		'rules' => 'required|numeric|greater_than[24.9]'
    	),
        array(
    		'field' => 'shareholders_file_name_txt_04',
    		'label' => 'file name 04',
    		'rules' => 'required'
    	));
    
     public static $sub_company_hard_verify_rules_02 = array(
    	array(
    		'field' => 'shareholders[1][name]',
    		'label' => 'Shareholder name 01',
    		'rules' => 'required'
    	),
    	array(
    		'field' => 'shareholders[2][name]',
    		'label' => 'Shareholder name 02',
    		'rules' => 'required'
    	),
    	array(
    		'field' => 'shareholders[3][name]',
    		'label' => 'Shareholder name 03',
    		'rules' => 'required'
        ),
     	array(
    		'field' => 'shareholders[4][name]',
    		'label' => 'Shareholder name 04',
    		'rules' => 'required'
    	));
     
    public static  $company_soft_verify_rules = array(
    	array(
    		'field' => 'passport_verification_txt',
    		'label' => 'upload company registration document please,',
    		'rules' => 'required'
    	));
    
    
    /**
     * default contructor
     */
    function __construct() {
              
    }
    
    /**
     * validate for officer owner input.
     * @return boolean
     */
    public static function _validate_officer_owner($is_compnay_ems=false){
        // update officer
        $officer_names = ci()->input->get_post('officer_name');
        $officer_types = ci()->input->get_post('officer_type');
        $officer_rates = ci()->input->get_post('officer_rate');
        $officer_file_ids = ci()->input->get_post('officer_file_id');
        $flag = true;
        if(empty($officer_file_ids) && empty($officer_names)&& empty($officer_types)&& empty($officer_rates)){
            return true;
        }
        
        $index = 0;
        if($is_compnay_ems){
            foreach($officer_file_ids as $file_id){
                if($file_id && $officer_names[$index] != ""){
                    $flag = true;
                }else if($file_id || $officer_names[$index] || $officer_rates[$index]){
                    $flag = false;
                }

                $index ++;
            }
        }else{
            foreach($officer_file_ids as $file_id){
                if($file_id && $officer_names[$index] != "" && $officer_types[$index]!="" ){
                    $flag = true;
                }else if($file_id || $officer_names[$index]  || $officer_types[$index] || $officer_rates[$index]){
                    $flag = false;
                }

                $index ++;
            }
        }
        

        return $flag;
    }
    
    /**
     * validate for mail receiver input.
     * @return boolean
     */
    public static function _validate_mail_receiver($is_compnay_ems=false){
        // update mail_receiver
        $mail_receiver_file_name = ci()->input->get_post('input_file_name');
        $mail_receiver_name = ci()->input->get_post('mail_receiver_name');
        $mail_receiver_ids = ci()->input->get_post('mail_receiver_id');
        $flag = true;
        
        
        $index = 0;
        if($is_compnay_ems){
            if(empty($mail_receiver_ids) && empty($mail_receiver_name) && empty($mail_receiver_file_name)){
                return true;
            }
        
            foreach($mail_receiver_ids as $file_id){
                if($file_id && $mail_receiver_file_name[$index] != "" && $mail_receiver_name[$index] != ""){
                    $flag = true;
                }else {
                    $flag = false;
                }
                $index ++;
            }
        }else{
            if(empty($mail_receiver_name)){
                return true;
            }
            foreach($mail_receiver_ids as $file_id){
                if(empty($mail_receiver_name[$index])){
                    $index ++;
                    continue;
                }
                
                if($file_id && $mail_receiver_file_name[$index] != "" && $mail_receiver_name[$index] != ""){
                    $flag = true;
                }else {
                    $flag = false;
                }
                $index ++;
            }
        }
        
        return $flag;
    }
    
    /**
     * validate for business company input.
     * @return boolean
     */
    public static function _validate_business_company($verify_postbox=null, $is_compnay_ems=false){
        // update business company
        $business_license_name = ci()->input->get_post('business_license_name');
        $business_license_ids = ci()->input->get_post('business_license_ids');
        
        $flag = true;
        if(empty($business_license_ids) && empty($business_license_name) && (empty($verify_postbox) || empty($verify_postbox->company))){
            return true;
        }
        
        $index = 0;
        if($is_compnay_ems){
            if(!empty($business_license_ids)){
                foreach($business_license_ids as $file_id){
                    if($file_id && $business_license_name[$index] != "" ){
                        $flag = true;
                    }else {
                        $flag = false;
                    }

                    $index ++;
                }
            }
        }else{
            if((empty($verify_postbox) || empty($verify_postbox->company))){
                return true;
            }
            if(!empty($business_license_ids)){
                foreach($business_license_ids as $file_id){
                    if($file_id && $business_license_name[$index] != ""){
                        $flag = true;
                    }else {
                        $flag = false;
                    }

                    $index ++;
                }
            }
        }
        
        return $flag;
    }
}
