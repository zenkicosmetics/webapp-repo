<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class device extends AccountSetting_Controller
{
    /**
     * Loads the gazillion of stuff, in Flash Gordon speed.
     *
     * @todo Document properly please.
     */
    public function __construct()
    {
        parent::__construct();

        // Model
        $this->load->model('digital_devices_m');
        $this->load->model('mailbox/postbox_m');
        $this->load->model('mailbox/customer_m');
        $this->load->model('digital_devices_setting_m');
        
        // load library (#1250 HOTFIX: the Hamburg panel shows this customer but this cusotmer has already been deleted. )
        $this->load->library('customers/customers_api');
    }

    public function ping()
    {
        $device_id = $this->input->get_post('device_id');
        $secret_key = $this->input->get_post('secret_key');
        $current_internal_ip = $this->input->get_post('current_internal_ip');
        $revision = $this->input->get_post('revision');
        $last_data_update = $this->input->get_post('last_data_update');

        log_message(APConstants::LOG_DEBUG, ">>>>>>>> Device->ping: DeviceID:".$device_id."|Secret Key:".$secret_key."|Last Updated Date:".$last_data_update);
        
        if (empty($device_id) || empty($secret_key)) {
            log_message(APConstants::LOG_DEBUG, ">>>>>>>> Device->ping: DeviceID is empty or Secret key is empty ");
            echo 'false';
            exit;
        }

        $devices_entry = $this->digital_devices_m->get_by('panel_code', $device_id);
        if (!$devices_entry || !isset($devices_entry->secure_key) || $devices_entry->secure_key != $secret_key) {
            log_message(APConstants::LOG_DEBUG, ">>>>>>>> Device->ping: DeviceID is invalid. DeviceID input:".$device_id);
            exit();
        }

        $format_last_data_update = date('Y-m-d H:i:s', strtotime(substr($last_data_update,0,10).' '.substr($last_data_update,12,8)));
        $this->digital_devices_m->update($device_id, array('last_ping_received' => date('Y-m-d H:i:s'), 'ip' => $current_internal_ip, 'last_data_update' => $format_last_data_update, 'current_revision' => $revision,));
        log_message(APConstants::LOG_DEBUG, ">>>>>>>> Device->ping: DeviceID is invalid. DeviceID input:".$device_id);
        echo 'true';
        exit;
    }

    /**
     * Requested URL: https://www.clevvermail.com/app/index.php/device/setup?type=clevverhub
     *
     * type = 'clevverhub' or 'clevverboard'
     */
    public function setup()
    {
        $device_type = strtolower($this->input->get_post('type'));
        $device_type = ($device_type == 'clevverhub') ? 'clevverhub' : 'clevverboard';
        $devices_entry_id = $this->digital_devices_m->insert(array(
            'created_date' => APUtils::convert_date_to_timestamp(date('Y-m-d')),
            'secure_key' => $this->generate_secure_key(),
            'type' => $device_type
        ));
        $devices_entry = $this->digital_devices_m->get($devices_entry_id);
        $devices_entry->panel_code = str_pad($devices_entry->id, 4, '0', STR_PAD_LEFT);
        $this->digital_devices_m->update($devices_entry_id, array('panel_code' => $devices_entry->panel_code));
        $data = array(
            'device_id' => $devices_entry->panel_code,
            'device_secret' => $devices_entry->secure_key
        );
        echo json_encode($data);
        exit;
    }

    public function get_data() {
        $device_id = $this->input->get_post('device_id');
        $secret_key = $this->input->get_post('secret_key');
        if (empty($device_id) || empty($secret_key)) {
            echo 'false';
            exit;
        }
        $devices_entry = $this->digital_devices_m->get_by('panel_code', $device_id);
        if (
                !$devices_entry ||
                !isset($devices_entry->secure_key) ||
                $devices_entry->secure_key != $secret_key ||
                !isset($devices_entry->location_id) ||
                $devices_entry->location_id <= 0
        ) {
            exit();
        }

        //#1250 HOTFIX: the Hamburg panel shows this customer but this cusotmer has already been deleted. 
        $postboxes = $this->customer_m->get_postbox_paging(array("location_available_id" => $devices_entry->location_id,
            "customers.status != 1" => NULL,
            "customers.activated_flag" => APConstants::ON_FLAG,
            "(p.completed_delete_flag = '0' OR p.completed_delete_flag IS NULL)" => NULL), 0, PHP_INT_MAX, '');
        if (!is_array($postboxes)) {
            exit;
        }
        $data = array();
        foreach ($postboxes['data'] as $postbox) {
            if ($postbox->activated_flag == APConstants::ON_FLAG || $postbox->deactivated_type != APConstants::MANUAL_INACTIVE_TYPE) {
                $data[] = array(
                    htmlspecialchars($postbox->postbox_company),
                    htmlspecialchars($postbox->postbox_name)
                );
            }
        }

        // Get device setting data
        $device_setting = $this->digital_devices_setting_m->get_by('panel_code', $device_id);
        $message = array("title" => "", "summary" => "", "full_text" => "");
        if (!empty($device_setting)) {
            $message["title"] = $device_setting->message_title;
            $message["summary"] = $device_setting->message_summary;
            $message["full_text"] = $device_setting->message_fulltext;
        }
        $device_data = array(
            'timezone' => $devices_entry->timezone ? $devices_entry->timezone : 'Europe/Berlin'
        );
        echo json_encode(array('last_modified' => date('c'), 'device' => $device_data, 'data' => $data, 'message' => $message));
        exit;
    }

    public function get_updates()
    {
        $device_id = $this->input->get_post('device_id');
        $secret_key = $this->input->get_post('secret_key');
        $last_revision = $this->input->get_post('last_revision');

        if (empty($device_id) || empty($secret_key) || empty($last_revision)) {
            header('HTTP/1.0 404 Not Found');
            echo 'false';
            exit;
        }

        $devices_entry = $this->digital_devices_m->get_by('panel_code', $device_id);
        if (
            !$devices_entry ||
            !isset($devices_entry->secure_key) ||
            $devices_entry->secure_key != $secret_key
        ) {
            exit();
        }

        $file_prefix = 'update_';
        $file_suffix = '.zip';
        $dir = __DIR__ . '/updates/' . $devices_entry->type . '/';
        if (!file_exists($dir)) {
            header('HTTP/1.0 404 Not Found');
            echo 'false';
            exit;
        }
        $files = array();
        if ($handle = opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && strpos($file, $file_prefix) === 0) {
                    $files[] = $file;
                }
            }
            closedir($handle);
        }
        if (empty($files)) {
            header('HTTP/1.0 404 Not Found');
            echo 'false';
            exit;
        }
        $filesArr = array();
        foreach ($files as $key => $filename) {
            $filesArr[substr($filename, strlen($file_prefix), strlen($file_suffix) * -1)] = $filename;
        }
        krsort($filesArr);
        $file = array_shift($filesArr);
        $file_revision = substr($file, strlen($file_prefix), strlen($file_suffix) * -1);

        if ($file_revision > $last_revision) {
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=\"update.zip\"");
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: " . filesize($dir . $file));
            ob_end_flush();
            @readfile($dir . $file);
            exit;
        }

        exit;
    }

    protected function generate_secure_key()
    {
        return md5(microtime() . md5('clevver' . time()) . rand(0, 10000) . rand(0, 10000));
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */