<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 * @author DungNT
 */
class GoogleOCR {

    /**
     * Send ios push message.
     *
     * @param unknown_type $push_id
     * @param unknown_type $body
     */
    public static function getTextContent($file_path) {
        $api_key = Settings::get(APConstants::SERVER_OCR_API_KEY);
        $cvurl =  Settings::get(APConstants::SERVER_OCR_API_ENDPOINT). "images:annotate?key=" . $api_key;
        $type = "TEXT_DETECTION";

        if (empty($file_path) && !file_exists($file_path)) {
            return '';
        }
        
        $valid_file = true;
        if(filesize($file_path) > (4024000)) {
            $valid_file = false;
            log_message(APConstants::LOG_ERROR, 'The file size of '.$file_path. ' is greater than 4024000');
            return '';
        }

        //convert it to base64
        $fname = $file_path;
        $data = file_get_contents($fname);
        $base64 = base64_encode($data);
        
        $r_json ='{
            "requests": [
                {
                  "image": {
                    "content":"' . $base64. '"
                  },
                  "features": [
                      {
                        "type": "' .$type. '",
                        "maxResults": 200
                      }
                  ]
                }
            ]
        }';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $cvurl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER , false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST , false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $r_json);
        $json_response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ( $status != 200 ) {
            log_message(APConstants::LOG_ERROR, "Error: $cvurl failed status $status, Error detail:". json_encode($json_response));
            return '';
        }

        return $json_response;
    }
}
