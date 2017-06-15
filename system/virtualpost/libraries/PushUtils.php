<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 * @author DungNT
 */
class PushUtils {

    /**
     * Send ios push message.
     *
     * @param unknown_type $push_id
     * @param unknown_type $body
     */
    public static function sendIOSPush($push_id, $body) {
        $passphrase = Settings::get(APConstants::PUSH_IOS_PEM_PASSWORD);
        $pem_file_path = Settings::get(APConstants::PUSH_IOS_PEM_FILE_PATH);

        ////////////////////////////////////////////////////////////////////////////////

        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', $pem_file_path);
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

        // Open a connection to the APNS server
        $fp = stream_socket_client(
            'ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

        if (!$fp) {
            log_message(APConstants::LOG_ERROR, "Send push notification error. Can not open socket");
            return;
        }
        // Create the payload body
        $body['aps'] = $body;

        // Encode the payload as JSON
        $payload = json_encode($body);

        // Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*', $push_id) . pack('n', strlen($payload)) . $payload;

        // Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));
        $sent_result = false;
        if (!$result) {
            log_message(APConstants::LOG_ERROR, "Send push notification error. Can not not message." . json_encode($result));
            $sent_result = false;
        } else {
            $sent_result = true;
        }
        // Close the connection to the server
        fclose($fp);
        return $sent_result;
    }

    public static function sendAndroidPush($push_id, $body) {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $android_key = Settings::get(APConstants::PUSH_ANDROID_KEY);

        if (!$body) {
            return;
        }

        $fields = array(
            'registration_ids' => array($push_id),
            'data' => $body,
            'content_available' => true,
            'notification' => array(
                'body' => $body['body'],
                'title' => 'ClevverMail',
                'icon' => 'icon_notification',
                'sound' => 'default'
            )
        );

        $headers = array(
            'Authorization: key='. $android_key,
            'Content-Type: application/json'
        );

        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            echo('Curl failed: ' . curl_error($ch));
        }

        // Close connection
        curl_close($ch);
        
         return $result;
    }

}
