<?php defined('BASEPATH') or exit('No direct script access allowed');

class scripts_api
{
    public static function executeQuery($sql, $returnResult = false, $resultArray = false)
    {
        ci()->load->model('scripts/script_m');

        $result = ci()->script_m->executeQuery($sql, $returnResult, $resultArray);

        return $result;
    }

    public static function outputBuffer()
    {
        flush();
        ob_flush();
    }

    /**
     * @Description: get list customers have been deleted but remain postbox is not deleted
     */
    public  static function getListCustomers()
    {
        ci()->load->model('scripts/script_m');

        $result = ci()->script_m->getListCustomers();

        return $result;
    }
}