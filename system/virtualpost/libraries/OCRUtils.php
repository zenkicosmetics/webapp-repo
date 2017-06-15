<?php

if (! defined('BASEPATH'))
    exit('No direct script access allowed');

class OCRUtils {
    function __construct() {
    }
    
    /**
     * Convert PDF file to searchable
     * @param type $fullPathToPDF
     * @param type $destination_file
     */
    public static function convertPDFToSearchable($fullPathToPDF, $destination_file) {
        $listImagesFile = self::convertPDFToJPEG($fullPathToPDF);
        return self::convertImagesToSearchablePDF($listImagesFile, $destination_file);
    }
    
    /**
     * Convert pdf to list image file.
     * 
     * @param type $fullPathToPDF
     */
    public static function convertPDFToJPEG($fullPathToPDF) {
        $physical_file = Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE);
        if (!is_dir( $physical_file. 'temp')) {
            mkdir($physical_file . 'temp', 0777, TRUE);
            chmod($physical_file . 'temp', 0777);
        }
        
        $list_images_files = array();
        // Generate random folder
        $sub_temp_folder = APUtils::generateRandom(10);
        if (!is_dir($physical_file . 'temp/' . $sub_temp_folder)) {
            mkdir($physical_file . 'temp/' . $sub_temp_folder, 0777, TRUE);
            chmod($physical_file . 'temp/' . $sub_temp_folder, 0777);
        }
        $full_sub_temp_folder = $physical_file . 'temp/' . $sub_temp_folder.'/';
        // Check if this file is not PDF (it should be PNG)
        if (!APUtils::endsWith($fullPathToPDF, 'pdf')) {
            $ext = strtolower(substr($fullPathToPDF, strrpos($fullPathToPDF, '.') + 1));
            copy($fullPathToPDF, $full_sub_temp_folder.'0.'.$ext);
            $list_images_files[] = $full_sub_temp_folder.'0.'.$ext;
            return $list_images_files;
        }
        
        $main_im = new Imagick($fullPathToPDF);
        $noOfPagesInPDF = $main_im->getNumberImages();
        $main_im->clear(); 
        $main_im->destroy();
      
        
        if ($noOfPagesInPDF) { 
            for ($i = 0; $i < $noOfPagesInPDF; $i++) { 
                // instantiate Imagick 
                $im = new Imagick();
                $im->setResolution(300,300);
                $url = $fullPathToPDF.'['.$i.']'; 
                $im->readimage($url); 
                $im->setImageFormat('jpeg');
                $image_out_url = $full_sub_temp_folder.$i.'.jpeg';
                $im->writeImage($image_out_url); 
                $im->clear(); 
                $im->destroy();
                
                $list_images_files[] = $image_out_url;
            }
        }
        return $list_images_files;
    }
    
    /**
     * Convert list of image file to searchable PDF.
     * 
     * @param type $listImagesFile
     * @param type $destination_file
     */
    public static function convertImagesToSearchablePDF($listImagesFile, $destination_file) {
        $files = array();
        $physical_file = Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE);
        if (!is_dir( $physical_file. 'temp')) {
            mkdir($physical_file . 'temp', 0777, TRUE);
            chmod($physical_file . 'temp', 0777);
        }
        // Generate random folder
        $sub_temp_folder = APUtils::generateRandom(10);
        if (!is_dir($physical_file . 'temp/' . $sub_temp_folder)) {
            mkdir($physical_file . 'temp/' . $sub_temp_folder, 0777, TRUE);
            chmod($physical_file . 'temp/' . $sub_temp_folder, 0777);
        }
        $full_sub_temp_folder = $physical_file . 'temp/' . $sub_temp_folder.'/';
        $parent_temp_folder = '';
        
        if (!is_dir( $physical_file. 'ocr')) {
            mkdir($physical_file . 'ocr', 0777, TRUE);
            chmod($physical_file . 'ocr', 0777);
        }
        // Generate random folder
        $ocr_temp_folder = APUtils::generateRandom(10);
        if (!is_dir($physical_file . 'ocr/' . $ocr_temp_folder)) {
            mkdir($physical_file . 'ocr/' . $ocr_temp_folder, 0777, TRUE);
            chmod($physical_file . 'ocr/' . $ocr_temp_folder, 0777);
        }
        if (empty($destination_file)) {
            $destination_file = $ocr_temp_folder.$ocr_temp_folder.'.pdf';
        }
        
        $i = 0;
        $text_content = '';
        foreach ($listImagesFile as $imagesFile) {
            $parent_temp_folder = dirname($imagesFile);
            $temp_destination_file = $full_sub_temp_folder.$i;
            $item_text_content = self::convertImageToSearchablePDF($imagesFile, $temp_destination_file);
            $text_content = $text_content.PHP_EOL.$item_text_content;
            $i++;
            $files[] = $temp_destination_file.'.pdf';
        }
        
        // Merge all PDF file to one
        // APUtils::mergePDFfilesDefault($files, $destination_file);
        // Call command line to merge pdf file
        // pdftk *.pdf cat output $destination_file
        $merge_cmd = "pdftk $full_sub_temp_folder/*.pdf cat output $destination_file";
        exec($merge_cmd);
        
        // Delete all temp file
        foreach ($files as $file) {
            unlink($file);
        }
        
        // Delete all images temp file
        foreach ($listImagesFile as $imagesFile) {
            unlink($imagesFile);
        }
        rmdir($full_sub_temp_folder);
        if (!empty($parent_temp_folder)) {
            rmdir($parent_temp_folder);
        }
        
        return array('text' => $text_content, 'pdf' => $destination_file);
    }
    
    /**
     * Convert list of image file to searchable PDF.
     * 
     * @param type $imagesFile
     * @param type $destination_file
     */
    public static function convertImageToSearchablePDF($imagesFile, $destination_file) {
        try {
            $cmd = Settings::get(APConstants::SERVER_OCR_EXE_FILE_PATH);
            // $tess_data = Settings::get(APConstants::SERVER_OCR_TESSDATA_FILE_PATH);
            $config_file = Settings::get(APConstants::SERVER_OCR_CONFIG_FILE_PATH);
            $list_lang = Settings::get(APConstants::SERVER_OCR_LIST_LANGUAGE);
            // $full_cmd = "$cmd --tessdata-dir $tess_data $imagesFile $destination_file -l $list_lang $config_file";
            $full_pdf_cmd = "$cmd $imagesFile $destination_file -l $list_lang $config_file";
            $full_txt_cmd = "$cmd $imagesFile $destination_file -l $list_lang";
            log_message(APConstants::LOG_DEBUG, $full_pdf_cmd);
            
            // Parse entire output
            // Surround with double quotes if file name has spaces
            exec($full_pdf_cmd);
            exec($full_txt_cmd);
            
            // Read text file
            $destination_file_text = $destination_file.'.txt';
            if (file_exists($destination_file_text)) {
                $text_content = file_get_contents($destination_file_text);
                unlink($destination_file_text);
                return $text_content;
            }
            return '';
        } catch ( Exception $e ) {
            log_message(APConstants::LOG_ERROR, $e);
        }
        
    }
    
}
