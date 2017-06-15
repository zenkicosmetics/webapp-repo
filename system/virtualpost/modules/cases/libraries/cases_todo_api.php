<?php defined('BASEPATH') or exit('No direct script access allowed');

class cases_todo_api
{
    /**
     * default contructor
     */
    function __construct() {
        // load library
        ci()->load->model(array(
            "cases/cases_verification_personal_identity_m",
            "cases/cases_verification_company_hard_m",
            "cases/cases_verification_usps_m",
            "cases/case_usps_mail_receiver_m",
            "cases/case_usps_officer_m",
            "cases/case_usps_business_license_m",
            "cases/case_resource_m",
            "customers/customer_m",
            "addresses/customers_address_m",
            "addresses/location_m",
            "mailbox/postbox_m",
            "payment/payment_m"
        ));
        
        ci()->load->library(array(
            "pdf05"
        ));
        
    }
    
    /**
     * gets special local file path.
     * @param type $case_id
     * @param type $type
     * @param type $op
     * @param type $id
     * @return type
     */
    public static function getSpecialLocalFileName($case_id, $type, $op, $id){
        // get file path
        $cases_verification_check = '';
        switch ($type) {
            case "VR01":
                $cases_verification_check = ci()->cases_verification_personal_identity_m->get_by_many(array(
                    'case_id' => $case_id,
                    'type' => 1
                ));
                break;
            case "VR02":
                $cases_verification_check = ci()->cases_verification_personal_identity_m->get_by_many(array(
                    'case_id' => $case_id,
                    'type' => 2
                ));
                break;
            case "VR03":
                $cases_verification_check = ci()->cases_verification_company_hard_m->get_by_many(array(
                    'case_id' => $case_id
                ));
                break;
            case "VR04":
                $cases_verification_check = ci()->cases_verification_usps_m->get_by_many(array(
                    'case_id' => $case_id
                ));
                break;
            case "mail_receiver":
                $cases_verification_check = ci()->case_usps_mail_receiver_m->get_by_many(array(
                    "case_id" =>$case_id,
                    "id" => $id
                ));
                break;
            case "officer_onwer":
                $cases_verification_check = ci()->case_usps_officer_m->get_by_many(array(
                    "case_id" =>$case_id,
                    "id" => $id
                ));
                break;
            case "business_license":
                $cases_verification_check = ci()->case_usps_business_license_m->get_by_many(array(
                    "case_id" =>$case_id,
                    "id" => $id
                ));
                break;
        }

        $local_file_path = "";
        if (!empty($cases_verification_check)) {
            switch ($op) {
                case "01":
                    $local_file_path = $cases_verification_check->shareholders_local_file_path_01;
                    break;
                case "02":
                    $local_file_path = $cases_verification_check->shareholders_local_file_path_02;
                    break;
                case "03":
                    $local_file_path = $cases_verification_check->shareholders_local_file_path_03;
                    break;
                case "04":
                    $local_file_path = $cases_verification_check->shareholders_local_file_path_04;
                    break;
                case "05":
                    $local_file_path = $cases_verification_check->id_of_applicant_local_file_path;
                    break;
                case "06":
                    $local_file_path = $cases_verification_check->license_of_applicant_local_file_path;
                    break;
                case "07":
                    $local_file_path = $cases_verification_check->additional_local_file_path;
                    break;
                case "08":
                    $local_file_path = $cases_verification_check->driver_license_document_local_file_path;
                    break;
                case "09":
                    $local_file_path = $cases_verification_check->additional_company_local_file_path;
                    break;    
                case "10":
                    $local_file_path = $cases_verification_check->receiver_local_path;
                    break;
                case "11": 
                    $local_file_path = $cases_verification_check->officer_local_path;
                    break;
                case "12": 
                    $local_file_path = $cases_verification_check->business_license_local_file_path;
                    break;
                default:
                    $local_file_path = $cases_verification_check->verification_local_file_path;
                    break;
            }
        }
        
        return $local_file_path;
    }
    
    /*
     * Processing for cmra and usps
     */
    public static function process_personal_case($base_task_name,$customer, $location_id){
        
        $list_case_verification = ci()->cases_verification_personal_identity_m->get_many_by_many(array(
            "case_id" => $base_task_name->case_id,
            "type" => 1,
        ));
        
        $files = array();                        
        if(!empty($list_case_verification)){
            
            foreach($list_case_verification as $case_verification){

                if( !empty($case_verification->verification_local_file_path) && !file_exists($case_verification->verification_local_file_path) && (!empty($case_verification->verification_amazon_file_path)) ){
                    
                    $local_file_path = self::get_local_file_path($case_verification->verification_local_file_path);
                    if(!is_dir($local_file_path)){
                            mkdir($local_file_path,777);
                    }
                    $file = new stdClass();
                    $file->amazon_relate_path = $case_verification->verification_amazon_file_path;
                    $file->local_file_path = $local_file_path;
                    APUtils::download_amazon_file($file);
                }
                if( !empty($case_verification->verification_local_file_path) && file_exists($case_verification->verification_local_file_path)){

                    $file_type = strtolower(substr($case_verification->verification_local_file_path,-3));
                    $file_des = 'uploads/temp/'. self::get_file_name($case_verification->verification_local_file_path,$base_task_name->case_id);
                    
                    if($file_type == 'pdf'){
                        cases_todo_api::convertPDFToJPEG($case_verification->verification_local_file_path, $file_des, $location_id);
                    }
                    else { 
                        copy($case_verification->verification_local_file_path, $file_des);
                        
                    }
                    $files[] = $file_des;
                }
 
                if( !empty($case_verification->driver_license_document_local_file_path) && !file_exists($case_verification->driver_license_document_local_file_path) && (!empty($case_verification->driver_license_document_amazon_file_path)) ){
                    
                    $local_file_path = self::get_local_file_path($case_verification->driver_license_document_local_file_path);
                    if(!is_dir($local_file_path)){
                            mkdir($local_file_path,777);
                    }
                    $file = new stdClass();
                    $file->amazon_relate_path = $case_verification->driver_license_document_amazon_file_path;
                    $file->local_file_path = $local_file_path;
                    APUtils::download_amazon_file($file);
                }

                if( !empty($case_verification->driver_license_document_local_file_path) && file_exists($case_verification->driver_license_document_local_file_path)){

                    $file_type = strtolower(substr($case_verification->driver_license_document_local_file_path,-3));
                    
                    $file_des = 'uploads/temp/'. self::get_file_name($case_verification->driver_license_document_local_file_path,$base_task_name->case_id);
                    
                    if($file_type == 'pdf'){
                        cases_todo_api::convertPDFToJPEG($case_verification->driver_license_document_local_file_path, $file_des,$location_id);
                    }
                    else {
                        copy($case_verification->driver_license_document_local_file_path, $file_des);
                    }
                    $files[] = $file_des;
                }
            }
        }
        return $files;
    }
    public static function get_file_name($str,$case_id){
        
        $origin_file = substr($str,(strrpos($str,"/"))+1, strlen($str));
        $new_file = $case_id."_".substr($origin_file,0,strrpos($origin_file,"."));
        return $new_file.".jpg";
    }
    /*
     * Processing for cmra and usps
     */
    public static  function process_company_soft_case($base_task_name,$customer,$location_id){
        
        $list_case_verification = ci()->cases_verification_personal_identity_m->get_many_by_many(array(
            "case_id" => $base_task_name->case_id,
            "type" => 2,
        ));
        
        $files = array();
        if(!empty($list_case_verification)){
            
            foreach($list_case_verification as $case_verification){
                
                if( !empty($case_verification->verification_local_file_path) && !file_exists($case_verification->verification_local_file_path) && (!empty($case_verification->verification_amazon_file_path)) ){
                    
                    $local_file_path = self::get_local_file_path($case_verification->verification_local_file_path);
                    if(!is_dir($local_file_path)){
                            mkdir($local_file_path,777);
                    }
                    $file = new stdClass();
                    $file->amazon_relate_path = $case_verification->verification_amazon_file_path;
                    $file->local_file_path = $local_file_path;
                    APUtils::download_amazon_file($file);
                }
            
                if( !empty($case_verification->verification_local_file_path) && file_exists($case_verification->verification_local_file_path)){

                    $file_type = strtolower(substr($case_verification->verification_local_file_path,-3));
                    $file_des = 'uploads/temp/'. self::get_file_name($case_verification->verification_local_file_path, $base_task_name->case_id);
                    
                    if($file_type == 'pdf'){
                        cases_todo_api::convertPDFToJPEG($case_verification->verification_local_file_path, $file_des,$location_id);
                    }
                    else {
                        copy($case_verification->verification_local_file_path, $file_des);
                    }
                    $files[] = $file_des;
                }
                if( !empty($case_verification->driver_license_document_local_file_path) && !file_exists($case_verification->driver_license_document_local_file_path) && (!empty($case_verification->driver_license_document_amazon_file_path)) ){
                    
                    $local_file_path = self::get_local_file_path($case_verification->driver_license_document_local_file_path);
                    if(!is_dir($local_file_path)){
                            mkdir($local_file_path,777);
                    }
                    $file = new stdClass();
                    $file->amazon_relate_path = $case_verification->driver_license_document_amazon_file_path;
                    $file->local_file_path = $local_file_path;
                    APUtils::download_amazon_file($file);
                }
            
                if( !empty($case_verification->driver_license_document_local_file_path) && file_exists($case_verification->driver_license_document_local_file_path)){

                    $file_type = strtolower(substr($case_verification->driver_license_document_local_file_path,-3));
                    $file_des = 'uploads/temp/'. self::get_file_name($case_verification->driver_license_document_local_file_path, $base_task_name->case_id);
                    
                    if($file_type == 'pdf'){
                       cases_todo_api::convertPDFToJPEG($case_verification->driver_license_document_local_file_path, $file_des,$location_id);
                    }
                    else {
                        copy($case_verification->driver_license_document_local_file_path, $file_des);
                    }
                    $files[] = $file_des;
                }
            }
        }
        return $files;
    }
    
    /*
     * Processing for cmra and usps
     */
    public static function process_company_hard_case($base_task_name,$customer, $location_id){
        
        $list_case_verification = ci()->cases_verification_company_hard_m->get_many_by_many(array(
            "case_id" => $base_task_name->case_id
        ));
        
        $files = array();
        if(!empty($list_case_verification)){
            
            foreach($list_case_verification as $case_verification){
                
                if( !empty($case_verification->verification_local_file_path) && (!empty($case_verification->verification_amazon_file_path)) ){
                    
                    //If file does not existe in local, copy from amazon
                    if (!file_exists($case_verification->verification_local_file_path)) {
                        $local_file_path = self::get_local_file_path($case_verification->verification_local_file_path);
                        if(!is_dir($local_file_path)){
                                mkdir($local_file_path,777);
                        }
                        $file = new stdClass();
                        $file->amazon_relate_path = $case_verification->verification_amazon_file_path;
                        $file->local_file_path = $local_file_path;
                        APUtils::download_amazon_file($file);
                    }

                    $file_type = strtolower(substr($case_verification->verification_local_file_path,-3));
                    $file_des = 'uploads/temp/'. self::get_file_name($case_verification->verification_local_file_path, $base_task_name->case_id);

                    if($file_type == 'pdf'){
                       cases_todo_api::convertPDFToJPEG($case_verification->verification_local_file_path, $file_des,$location_id);
                    }
                    else {
                        copy($case_verification->verification_local_file_path, $file_des);
                    }
                    $files[] = $file_des;
                    
                }
                
                //Get share holder upload files
                if ($case_verification->no_shareholder_flag != APConstants::ON_FLAG){
                    //Share holder array
                    $share_holders = array('01', '02', '03', '04');
                    foreach ($share_holders as $share_holder){
                        $share_holder_local_file = 'shareholders_local_file_path_' . $share_holder;
                        $share_holder_amazon_file = 'shareholders_amazon_file_path_' . $share_holder;
                        
                        if( !empty($case_verification->$share_holder_local_file) && (!empty($case_verification->$share_holder_amazon_file)) ){
                    
                            //If file does not existe in local, copy from amazon
                            if (!file_exists($case_verification->$share_holder_local_file)) {
                                $local_file_path = self::get_local_file_path($case_verification->$share_holder_local_file);
                                if(!is_dir($local_file_path)){
                                        mkdir($local_file_path,777);
                                }
                                $file = new stdClass();
                                $file->amazon_relate_path = $case_verification->$share_holder_amazon_file;
                                $file->local_file_path = $local_file_path;
                                APUtils::download_amazon_file($file);
                            }

                            $file_type = strtolower(substr($case_verification->$share_holder_local_file,-3));
                            $file_des = 'uploads/temp/'. self::get_file_name($case_verification->$share_holder_local_file, $base_task_name->case_id);

                            if($file_type == 'pdf'){
                               cases_todo_api::convertPDFToJPEG($case_verification->$share_holder_local_file, $file_des,$location_id);
                            }
                            else {
                                copy($case_verification->$share_holder_local_file, $file_des);
                            }
                            $files[] = $file_des;

                        }
                        
                    }
                }
                
            }
            
        }
        return $files;
    }
    /*
     * Processing process tc contract ms
     */
    public static function process_tc_contract_ms($base_task_name,$customer, $location_id){
        
        $list_case_verification = ci()->case_resource_m->get_many_by_many(array(
            "case_id" => $base_task_name->case_id,
            "base_taskname" => $base_task_name->base_task_name,
        ));
        
        $files = array();                        
        if(!empty($list_case_verification)){
            
            foreach($list_case_verification as $case_verification){
                
                if( !empty($case_verification->local_file_path) && !file_exists($case_verification->local_file_path) && (!empty($case_verification->amazon_file_path)) ){
                    
                    $local_file_path = self::get_local_file_path($case_verification->local_file_path);
                    if(!is_dir($local_file_path)){
                            mkdir($local_file_path,777);
                    }
                    $file = new stdClass();
                    $file->amazon_relate_path = $case_verification->amazon_file_path;
                    $file->local_file_path = $local_file_path;
                    APUtils::download_amazon_file($file);
                }
                
                if( !empty($case_verification->local_file_path) && file_exists($case_verification->local_file_path)){

                    $file_type = strtolower(substr($case_verification->local_file_path,-3));
                    $file_des = 'uploads/temp/'. self::get_file_name($case_verification->local_file_path, $base_task_name->case_id);
                    
                    if($file_type == 'pdf'){
                         cases_todo_api::convertPDFToJPEG($case_verification->local_file_path, $file_des, $location_id);
                    }
                    else { 
                        copy($case_verification->local_file_path, $file_des);
                    }
                    $files[] = $file_des;
                }
            }
        }
        return $files;
    }
    
    /*
     * Processing for company verification EMS
     */
    public static function process_company_verification_ems($base_task_name,$customer, $location_id){
        
        $list_resource = ci()->case_resource_m->get_many_by_many(array(
            "case_id" => $base_task_name->case_id,
            "base_taskname" => $base_task_name->base_task_name,
            "seq_number" => "01"
        ));
        /*
        $list_usps_officer = ci()->case_usps_officer_m->get_many_by_many(array(
            "case_id" => $base_task_name->case_id,
            "base_taskname" => $base_task_name->base_task_name,
        ));
        */
        $files = array();                        
        if(!empty($list_resource)){
            
            foreach($list_resource as $case_verification){
                
                if( !empty($case_verification->local_file_path) && !file_exists($case_verification->local_file_path) && (!empty($case_verification->amazon_file_path)) ){
                    
                    $local_file_path = self::get_local_file_path($case_verification->local_file_path);
                    if(!is_dir($local_file_path)){
                            mkdir($local_file_path,777);
                    }
                    $file = new stdClass();
                    $file->amazon_relate_path = $case_verification->amazon_file_path;
                    $file->local_file_path = $local_file_path;
                    APUtils::download_amazon_file($file);
                }
                
                if( !empty($case_verification->local_file_path) && file_exists($case_verification->local_file_path)){

                    $file_type = strtolower(substr($case_verification->local_file_path,-3));
                    $file_des = 'uploads/temp/'. self::get_file_name($case_verification->local_file_path,$base_task_name->case_id);
                    
                    if($file_type == 'pdf'){
                        cases_todo_api::convertPDFToJPEG($case_verification->local_file_path, $file_des, $location_id);
                    }
                    else { 
                        copy($case_verification->local_file_path, $file_des);
                        
                    }
                    $files[] = $file_des;
                }
            }
        }
        /*
        if(!empty($list_usps_officer)){
            
            foreach($list_usps_officer as $case_verification){
                
                if( !empty($case_verification->officer_local_path) && !file_exists($case_verification->officer_local_path) && (!empty($case_verification->officer_amazon_path)) ){
                    
                    $local_file_path = self::get_local_file_path($case_verification->officer_local_path);
                    if(!is_dir($local_file_path)){
                            mkdir($local_file_path,777);
                    }
                    $file = new stdClass();
                    $file->amazon_relate_path = $case_verification->officer_amazon_path;
                    $file->local_file_path = $local_file_path;
                    APUtils::download_amazon_file($file);
                }
                
                if( !empty($case_verification->officer_local_path) && file_exists($case_verification->officer_local_path)){

                    $file_type = strtolower(substr($case_verification->officer_local_path,-3));
                    $file_des = 'uploads/temp/'. self::get_file_name($case_verification->officer_local_path, $base_task_name->case_id);
                    
                    if($file_type == 'pdf'){
                        cases_todo_api::convertPDFToJPEG($case_verification->officer_local_path, $file_des, $location_id);
                    }
                    else { 
                        //$files[] = $case_verification->verification_local_file_path;
                        copy($case_verification->officer_local_path, $file_des);
                        
                    }
                    $files[] = $file_des;
                }
            }
        }
         * 
         */
        return $files;
    }
    
    public static function convertPDFToJPEG($source_file, $des_file, $location_id = 0){
        $img = new imagick();
        if($location_id == 0){
            $img->setResolution(150, 150);
        }
        else{
            $img->setResolution(100, 100);
        }
        
        $img->readImage("{$source_file}[0]");
        $img->setImageCompression(Imagick::COMPRESSION_JPEG);
        $img->setImageCompressionQuality(100);
        $img->setImageFormat('jpg');
        $img->writeImages($des_file,true);
        $img->clear();
        $img->destroy();
    }
    
    /*
     * Processing for cmra and usps
     */
    public static function process_cmra_case($base_task_name,$customer){
        
        $list_case_verification = ci()->cases_verification_usps_m->get_many_by_many(array(
            "case_id" => $base_task_name->case_id
        ));
        
        $list_resource_file = ci()->case_resource_m->get_many_by_many(array(
            "case_id" => $base_task_name->case_id
        ));
        
        $list_usps_officer_file = ci()->case_usps_officer_m->get_many_by_many(array(
            "case_id" => $base_task_name->case_id
        ));
       
        $files = array();
        if(!empty($list_case_verification)){

            foreach($list_case_verification as $case_verification){
                
                if( !empty($case_verification->id_of_applicant_local_file_path) && !file_exists($case_verification->id_of_applicant_local_file_path) && (!empty($case_verification->id_of_applicant_amazon_file_path)) ){
                    
                    $local_file_path = self::get_local_file_path($case_verification->id_of_applicant_local_file_path);
                    if(!is_dir($local_file_path)){
                            mkdir($local_file_path,777);
                    }
                    $file = new stdClass();
                    $file->amazon_relate_path = $case_verification->id_of_applicant_amazon_file_path;
                    $file->officer_local_path = $local_file_path;
                    APUtils::download_amazon_file($file);
                }
                
                if( !empty($case_verification->id_of_applicant_local_file_path) && file_exists($case_verification->id_of_applicant_local_file_path)){
                    
                    $file_type = strtolower(substr($case_verification->id_of_applicant_local_file_path,-3));
                    $file_des = 'uploads/temp/'. self::get_file_name($case_verification->id_of_applicant_local_file_path,$base_task_name->case_id);
                    if($file_type == 'pdf'){
                       cases_todo_api::convertPDFToJPEG($case_verification->id_of_applicant_local_file_path, $file_des);
                    }
                    else {
                        copy($case_verification->id_of_applicant_local_file_path, $file_des);
                    }
                    $files[] = $file_des;
                }
                if( !empty($case_verification->license_of_applicant_local_file_path) && !file_exists($case_verification->license_of_applicant_local_file_path) && (!empty($case_verification->license_of_applicant_amazon_file_path)) ){
                    
                    $local_file_path = self::get_local_file_path($case_verification->license_of_applicant_local_file_path);
                    if(!is_dir($local_file_path)){
                            mkdir($local_file_path,777);
                    }
                    $file = new stdClass();
                    $file->amazon_relate_path = $case_verification->license_of_applicant_amazon_file_path;
                    $file->officer_local_path = $local_file_path;
                    APUtils::download_amazon_file($file);
                }
                
                if( !empty($case_verification->license_of_applicant_local_file_path) && file_exists($case_verification->license_of_applicant_local_file_path)){
                    $file_type = strtolower(substr($case_verification->license_of_applicant_local_file_path,-3));
                    $file_des = 'uploads/temp/'. self::get_file_name($case_verification->license_of_applicant_local_file_path,$base_task_name->case_id);
                    if($file_type == 'pdf'){
                       cases_todo_api::convertPDFToJPEG($case_verification->license_of_applicant_local_file_path, $file_des);
                    }
                    else {
                        copy($case_verification->license_of_applicant_local_file_path, $file_des);
                    }
                    $files[] = $file_des; 
                }
                
            }
        }
        
        if(!empty($list_resource_file)){
            foreach($list_resource_file as $resource){
                if( !empty($resource->local_file_path) && !file_exists($resource->local_file_path) && (!empty($resource->amazon_file_path)) ){
                    
                    $local_file_path = self::get_local_file_path($resource->local_file_path);
                    if(!is_dir($local_file_path)){
                            mkdir($local_file_path,777);
                    }
                    $file = new stdClass();
                    $file->amazon_relate_path = $resource->amazon_file_path;
                    $file->local_file_path = $local_file_path;
                    APUtils::download_amazon_file($file);
                }
                
                if( !empty($resource->local_file_path) && file_exists($resource->local_file_path)){
                    $file_type = strtolower(substr($resource->local_file_path,-3));
                    $file_des = 'uploads/temp/'. self::get_file_name($resource->local_file_path,$resource->case_id);
                    if($file_type == 'pdf'){
                       cases_todo_api::convertPDFToJPEG($resource->local_file_path, $file_des);
                    }
                    else {
                        copy($resource->local_file_path, $file_des);
                    }
                    $files[] = $file_des; 
                }
            }
        }
        
        if(!empty($list_usps_officer_file)){
            foreach($list_usps_officer_file as $usps_officer){
                if( !empty($usps_officer->officer_local_path) && !file_exists($usps_officer->officer_local_path) && (!empty($usps_officer->officer_amazon_path)) ){
                    
                    $officer_local_path = self::get_local_file_path($usps_officer->officer_local_path);
                    if(!is_dir($officer_local_path)){
                            mkdir($officer_local_path,777);
                    }
                    $file = new stdClass();
                    $file->officer_amazon_path = $usps_officer->officer_amazon_path;
                    $file->officer_local_path = $officer_local_path;
                    APUtils::download_amazon_file($file);
                }
                
                if( !empty($usps_officer->officer_local_path) && file_exists($usps_officer->officer_local_path)){
                    $file_type = strtolower(substr($usps_officer->officer_local_path,-3));
                    $file_des = 'uploads/temp/'. self::get_file_name($usps_officer->officer_local_path,$usps_officer->case_id);
                    if($file_type == 'pdf'){
                       cases_todo_api::convertPDFToJPEG($usps_officer->officer_local_path, $file_des);
                    }
                    else {
                        copy($usps_officer->officer_local_path, $file_des);
                    }
                    $files[] = $file_des; 
                }
            }
        }
        
        return $files;
    }
    
    public static function get_local_file_path($path){
        $local_file_path = (substr($path,0,strrpos($path,"/")+1));
        return $local_file_path;
    }
    
    public static function file_path($path,$case_id = ''){
        
        if(!empty($case_id)){
            $file_name = substr(basename($path),0,-4)."_".$case_id.".jpg";
        }
        else{
            $file_name = substr(basename($path),0,-4)."_".time().".jpg";   
        }
        $file_path = realpath(substr($path,0,strrpos($path,"/")+1));
        $file_path .= "/".$file_name;
        return $file_path;
    }
    
    public static function output_file_pdf($content, $file_output){
        
        $pdf = new Pdf05();
        $pdf->setFontSubsetting(true);
        $pdf->SetFont('freeserif', '', 11, '', 'false');
        $pdf->SetTitle(Settings::get(APConstants::INSTANCE_OWNER_COMPANY_CODE));
        $pdf->SetAuthor(Settings::get(APConstants::INSTANCE_OWNER_COMPANY_CODE));
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->SetTopMargin(28);
        $pdf->AddPage();
        $pdf->writeHTML($content, true, false, true, false, ''); 
        $pdf->Output($file_output,'F');
        return $file_output;
    }

   /*
     * Process in report about verfication for one customer
     */
    public static function process_report_for_each_customer($customer_id, $postbox_verify = 0, $location_id = 0){
        
        if(empty($customer_id)){
            return '';
        }
        
        $customer = ci()->customer_m->get_by('customer_id', $customer_id);
        $customer_address = ci()->customers_address_m->get_by('customer_id', $customer_id);
        
        if(!empty($customer_address)){
            $customer_address->shipment_country_name  = '';
            $customer_address->invoicing_country_name = '';
            if(!empty($customer_address->shipment_country)){
                $country = settings_api::getCountryByID($customer_address->shipment_country);
                $customer_address->shipment_country_name = !empty($country) ? $country->country_name: "";
            }
            if(!empty($customer_address->invoicing_country)){
                $country = settings_api::getCountryByID($customer_address->invoicing_country);
                $customer_address->invoicing_country_name = !empty($country) ? $country->country_name: "";
            }   
        }
        
        $customer_path = (Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . "cases/".$customer->customer_code."/postbox");
        
        if (!is_dir($customer_path)) {
            mkdir($customer_path, 0777, TRUE);
            chmod($customer_path, 0777);
        }
        
        $array_condition = array();
        $array_condition['deleted']     = APConstants::OFF_FLAG;
        $array_condition['customer_id'] = $customer_id;
        
        $location_name = "";
        if($location_id > 0){
            $array_condition['location_available_id'] = $location_id;
            $location = ci()->location_m->get_by("id",$location_id);
            $location_name = $location->location_name;
        }
        
        $listPostboxes = ci()->postbox_m->get_many_by_many($array_condition);
        
        $list_payment_method = ci()->payment_m->get_many_by_many(array(
            "customer_id" => $customer_id,
            "card_confirm_flag" => APConstants::ON_FLAG 
        ));
        
        $data_postbox = array();
        if(count($listPostboxes)){
            
            foreach ($listPostboxes as $postbox) {
                
                $check_verify_postbox = CaseUtils::isVerifiedPostboxAddress($postbox->postbox_id, $customer_id);
                //($postbox_verify == 1)&& 
                if(($postbox_verify == 1)&&(!$check_verify_postbox) ){
                    continue;
                }
                
                $info_postbox = "<br/>Info Postbox: <br/><strong>Type: </strong>";
                switch ($postbox->type) {
                    case 1:
                        $info_postbox .= "AS YOU GO <br/>";
                    break;
                    
                    case 2:
                        $info_postbox .= "PRIVATE <br/>";
                    break;
                
                    case 3:
                        $info_postbox .= "BUSINESS <br/>";
                    break;
                }
                
                if(!empty($postbox->name)){
                    $info_postbox .= "<strong>Name: </strong>".ucwords(strtolower(strtolower($postbox->name)))."<br/>";
                }
                if(!empty($postbox->company)){
                    $info_postbox .= " <strong>Company: </strong>".ucwords(strtolower($postbox->company))."<br/>";
                }
                $info_postbox .= " <strong>Postbox Code: </strong>".$postbox->postbox_code."<br/>";
                
                if($check_verify_postbox){
                    $info_postbox .= " <strong>Status of Verification: </strong> Completed";
                }
                else{
                    $info_postbox .= " <strong>Status of Verification: </strong> <span style='color: #ff0000'>Incomplete</span>";
                }
                
                $list_base_task_name = CaseUtils::get_base_task_name($postbox->postbox_id);
                $arr_file_id = array();
                $arr_postbox = array();
                if(!empty($list_base_task_name)){
                    $i=0;
                    foreach ($list_base_task_name as $base_task_name) {
                        
                        $arr_postbox[$i]['case_id'] = $base_task_name->case_id;
                        $arr_postbox[$i]['base_task_name'] = $base_task_name->base_task_name;
                        $arr_postbox[$i]['milestone_name'] = $base_task_name->milestone_name;
                        $arr_postbox[$i]['status']         = "";
                        
                        switch($base_task_name->status){
                            case "2":
                                $arr_postbox[$i]['status'] = "Completed";
                                break;
                            case "1":
                            case "3":
                            default :    
                                $arr_postbox[$i]['status'] = "<b style='color: rgb(246, 11, 11);'>Incomplete</b>";
                                break;
                        }
                        if($base_task_name->status == 2){
                          
                        $arr_postbox[$i]['list_file_id']   = array();
                        
                        switch ($base_task_name->base_task_name) {
                            case "verification_personal_identification":
                                $files = cases_todo_api::process_personal_case($base_task_name, $customer, $location_id);
                                if(!empty($files)){
                                    $arr_postbox[$i]['list_file_id'] = array_merge($arr_postbox[$i]['list_file_id'],$files);
                                }
                                break;
                            case "verification_company_identification_soft":
                                $files = cases_todo_api::process_company_soft_case($base_task_name, $customer, $location_id);
                                if(!empty($files)){
                                    $arr_postbox[$i]['list_file_id'] = array_merge($arr_postbox[$i]['list_file_id'], $files);
                                }
                                break;
                            case "verification_company_identification_hard":
                                $files = cases_todo_api::process_company_hard_case($base_task_name,$customer, $location_id);
                                if(!empty($files)){
                                    $arr_postbox[$i]['list_file_id'] = array_merge($arr_postbox[$i]['list_file_id'],$files);
                                }
                                break;
                            case "verification_special_form_PS1583":
                            case "verification_General_CMRA":
                            case "verification_california_mailbox":    
                                $files = cases_todo_api::process_cmra_case($base_task_name,$customer, $location_id);
                                if(!empty($files)){
                                    $arr_postbox[$i]['list_file_id'] = array_merge($arr_postbox[$i]['list_file_id'],$files);
                                }
                                break;
                            case "TC_contract_MS":
                            case "proof_of_address_MS":    
                                $files = cases_todo_api::process_tc_contract_ms($base_task_name,$customer, $location_id);
                                if(!empty($files)){
                                    $arr_postbox[$i]['list_file_id'] = array_merge($arr_postbox[$i]['list_file_id'],$files);
                                }
                                break;
                            case "company_verification_E_MS":           
                                $files = cases_todo_api::process_company_verification_ems($base_task_name,$customer, $location_id);
                                if(!empty($files)){
                                    $arr_postbox[$i]['list_file_id'] = array_merge($arr_postbox[$i]['list_file_id'],$files);
                                }
                                break;    
                        }
                     }

                        $i++;
                    }
                    
                }
                $data_postbox[] = array(
                    'info_postbox' => $info_postbox,
                    'arr_postbox'  => $arr_postbox
                );
            } // End list postbox
        }
        //echo "<pre>";print_r($data_postbox);exit;
        $head_content = ci()->load->view("todo/view_verification_detail", 
            array(
                'customer' => $customer,
                'customer_address' => $customer_address,
                'listPostboxes' => $listPostboxes,
                'list_payment_method' => $list_payment_method,
                'data_postbox' => $data_postbox,
                'location_name' => $location_name
            ), true);
        //echo $head_content;echo "<hr/>";
        
        $file_output = $customer_path."/verification_detail_report_head_content.pdf"; 
        $file_output = cases_todo_api::output_file_pdf($head_content, $file_output);
        return $file_output;
        
        
    }
    

}
