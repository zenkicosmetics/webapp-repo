<?php defined('BASEPATH') or exit('No direct script access allowed');

class device_api
{
    public static function getDeviceAll()
    {
        ci()->load->model('device/digital_devices_m');

        return ci()->digital_devices_m->get_all();
    }
}