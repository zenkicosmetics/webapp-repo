<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Files {

    protected $_image_path = "";
    protected $_image_url = "";

    public function __construct() {

    }

    /*
     * Function for upload and resize images
     */
    public static function upload($folder, $filename = 'userfile') {
        if (!isset($_FILES[$filename])) {
            return array(
                "status" => false,
                "message" => "You did not select a file to upload.",
                'local_file_path' => ''
            );
        }
        ci()->load->library('common/common_api');

        if (common_api::checkSharedUploadDir()) {
            $image_url = "uploads/images/" . $folder . "/";
            $config = array(
                'upload_path' => $image_url,
                'allowed_types' => '*',
                'max_size' => '200000'
            );

            ci()->load->library("upload", $config);

            if (!ci()->upload->do_upload($filename)) {
                $error = array(
                    ci()->upload->display_errors()
                );

                return $error;
            } else {
                $image_data = ci()->upload->data();
                $path = $image_url . $image_data ['file_name'];
                chmod($image_data['full_path'], 444);

                return $path;
            }
        }
    }

    /*
     * Function for upload and resize images
     */
    public static function upload_all($folder, $ext = 'gif|jpg|png|pdf', $file_name = "imagepath") {
        if (!isset($_FILES[$file_name])) {
            return array(
                "status" => false,
                "message" => "You did not select a file to upload.",
                'local_file_path' => ''
            );
        }

        ci()->load->library('common/common_api');

        if (common_api::checkSharedUploadDir()) {
            $image_url = "uploads/images/" . $folder . "/";
            $config = array(
                'upload_path' => $image_url,
                'allowed_types' => $ext,
                'max_size' => '200000'
            );
            ci()->load->library("upload", $config);

            if (!ci()->upload->do_upload($file_name)) {
                $error = array(
                    ci()->upload->display_errors()
                );
                log_message(APConstants::LOG_ERROR, $error);
            } else {
                $image_data = ci()->upload->data();
                $path = ci()->_image_url . $image_data ['file_name'];
                chmod($image_data ['full_path'], 444);

                return array(
                    "file_name" => $path,
                    'full_path' => $image_data ['full_path']
                );
            }
        }
        return array();
    }

    /*
     * Function for upload case document
     */
    public static function upload_case_document($case_id, $customer_code, $client_file_name = "imagepath", $server_file_name = '', $ext = 'gif|jpg|png|pdf') {
        if (!isset($_FILES[$client_file_name])) {
            return array(
                "status" => false,
                "message" => "You did not select a file to upload.",
                'local_file_path' => ''
            );
        }

        $case_code = sprintf('%1$08d', $case_id);
        if (!is_dir(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . "cases/" . $customer_code . '/' . $case_code)) {
            mkdir(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . "cases/" . $customer_code . '/' . $case_code, 0777, TRUE);
            chmod(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . "cases/" . $customer_code . '/' . $case_code, 0777);
        }

        $_image_url = APContext::getFullBasePath() . "cases/view_doc?case_id=" . $case_id . "&doc_name=" . $server_file_name;
        $_image_path = Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . "cases/" . $customer_code . '/' . $case_code . "/";
        $config = array(
            'upload_path' => $_image_path,
            'allowed_types' => $ext,
            'max_size' => '200000',
            'overwrite' => TRUE
        );

        if (!empty($server_file_name)) {
            $config['file_name'] = $server_file_name;
        }
        $path = "";
        ci()->load->library("upload", $config);
        if (!ci()->upload->do_upload($client_file_name)) {
            $error = array(
                ci()->upload->display_errors()
            );
            // xx comment out
            //log_message(APConstants::LOG_ERROR, $error);
            log_message(APConstants::LOG_ERROR, ci()->upload->display_errors());
            return array();
        } else {
            $image_data = ci()->upload->data();
            chmod($image_data ['full_path'], 444);
            return array(
                "web_url" => $_image_url,
                'local_url' => $image_data['full_path']
            );
        }
    }

    /*
     * Function for upload case document
     */
    public static function upload_case_document_for_ajax($case_id, $customer_code, $client_file_name = "imagepath", $server_file_name = '', $ext = 'gif|jpg|png|pdf|bmp') {
        $case_code = sprintf('%1$08d', $case_id);

        if (!isset($_FILES[$client_file_name])) {
            return array(
                "status" => false,
                "message" => "You did not select a file to upload.",
                'local_file_path' => ''
            );
        }

        if (!is_dir(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . "cases/" . $customer_code . '/' . $case_code)) {
            mkdir(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . "cases/" . $customer_code . '/' . $case_code, 0777, TRUE);
            chmod(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . "cases/" . $customer_code . '/' . $case_code, 0777);
        }
        $_image_path = Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . "cases/" . $customer_code . '/' . $case_code . "/";

        $config = array(
            'upload_path' => $_image_path,
            'allowed_types' => $ext,
            'max_size' => '200000',
            'overwrite' => TRUE
        );


        if (!empty($server_file_name)) {
            $config['file_name'] = $server_file_name;
        }
        ci()->load->library("upload", $config);
        ci()->upload->initialize($config);
        $path = "";
        $message = "";
        $status = ci()->upload->do_upload($client_file_name);
        if (!$status) {
            $message = ci()->upload->display_errors('', PHP_EOL);
            log_message(APConstants::LOG_ERROR, $message);
        } else {
            $image_data = ci()->upload->data();
            chmod($image_data ['full_path'], 444);
            $path = $image_data['full_path'];
        }

        return array(
            "status" => $status,
            "message" => $message,
            'local_file_path' => $path
        );
    }

    /**
     * Crop image
     *
     * @param unknown_type $source_image
     * @param unknown_type $x_axis
     * @param unknown_type $y_axis
     * @param unknown_type $width
     * @param unknown_type $height
     * @return boolean
     */
    public static function crop_image($source_image, $x_axis = 0, $y_axis = 0, $width, $height) {
        $image_config ['image_library'] = 'gd2';
        $image_config ['source_image'] = $source_image;
        $image_config ['new_image'] = $source_image;
        $image_config ['quality'] = "100%";
        $image_config ['maintain_ratio'] = FALSE;
        $image_config ['width'] = $width;
        $image_config ['height'] = $height;
        $image_config ['x_axis'] = $x_axis;
        $image_config ['y_axis'] = $y_axis;

        ci()->load->library("image_lib", array());
        ci()->image_lib->clear();
        ci()->image_lib->initialize($image_config);

        if (!ci()->image_lib->crop()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Crop image
     *
     * @param unknown_type $source_image
     * @param unknown_type $x_axis
     * @param unknown_type $y_axis
     * @param unknown_type $width
     * @param unknown_type $height
     * @return boolean
     */
    public static function rezise_image($source_image, $width, $height) {
        $image_config ['image_library'] = 'gd2';
        $image_config ['source_image'] = $source_image;
        $image_config ['new_image'] = $source_image;
        $image_config ['quality'] = "100%";
        $image_config ['maintain_ratio'] = TRUE;
        if (!empty($width)) {
            $image_config ['width'] = $width;
        }
        if (!empty($height)) {
            $image_config ['height'] = $height;
        }

        ci()->load->library("image_lib", array());
        ci()->image_lib->clear();
        ci()->image_lib->initialize($image_config);

        if (!ci()->image_lib->resize()) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * Function for upload and resize images
     */

    public static function upload_file($server_file_path, $client_file_name = 'imagepath') {
        if (!isset($_FILES[$client_file_name])) {
            return array(
                "status" => false,
                "message" => "You did not select a file to upload.",
                'local_file_path' => ''
            );
        }

        if (!is_dir($server_file_path)) {
            mkdir($server_file_path, 0777, TRUE);
            chmod($server_file_path, 0777);
        }
        $_image_path = $server_file_path;

        $config = array(
            'upload_path' => $_image_path,
            'allowed_types' => '*',
            'max_size' => '200000',
            'overwrite' => TRUE
        );

        ci()->load->library("upload", $config);
        ci()->upload->initialize($config);
        $path = "";
        $message = "";
        $status = ci()->upload->do_upload($client_file_name);
        if (!$status) {
            $message = ci()->upload->display_errors('', PHP_EOL);
        } else {
            $image_data = ci()->upload->data();
            chmod($image_data ['full_path'], 444);
            //$path = $image_data['full_path'];
            $path = $server_file_path . $image_data ['file_name'];
        }

        return array(
            "status" => $status,
            "message" => $message,
            'local_file_path' => $path
        );
    }

    public static function upload_file_with_name($server_file_path, $client_file_name = 'imagepath', $new_name = NULL) {
        if (!$new_name) {
            return self::upload_file($server_file_path, $client_file_name);
        } else {
            if (!isset($_FILES[$client_file_name])) {
                return array(
                    "status" => false,
                    "message" => "You did not select a file to upload.",
                    'local_file_path' => ''
                );
            }

            if (!is_dir($server_file_path)) {
                mkdir($server_file_path, 0777, TRUE);
                chmod($server_file_path, 0777);
            }
            $_image_path = $server_file_path;


            $config = array(
                'upload_path' => $_image_path,
                'allowed_types' => '*',
                'max_size' => '200000',
                'overwrite' => TRUE,
                'file_name' => $new_name
            );

            ci()->load->library("upload", $config);
            ci()->upload->initialize($config);
            $path = "";
            $message = "";
            $status = ci()->upload->do_upload($client_file_name);
            if (!$status) {
                $message = ci()->upload->display_errors('', PHP_EOL);
            } else {
                $image_data = ci()->upload->data();
                chmod($image_data ['full_path'], 444);
                //$path = $image_data['full_path'];
                $path = $server_file_path . $image_data ['file_name'];
            }

            return array(
                "status" => $status,
                "message" => $message,
                'local_file_path' => $path
            );
        }
    }

}
