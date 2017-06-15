<?php defined('BASEPATH') or exit('No direct script access allowed');

class common_api
{
    public static function setDynamicPathEnvelopeFile($envelope_file = null)
    {
        if (is_object($envelope_file)) {
            $envelope_file->public_file_name = APContext::getAssetPath() . str_replace(array("https://node2.eu.clevvermail.com/app/", "https://dev.eu.clevvermail.com/"), array("", ""), $envelope_file->public_file_name);
            $envelope_file->file_name = APContext::getAssetPath() . str_replace(array("https://node2.eu.clevvermail.com/app/", "https://dev.eu.clevvermail.com/"), array("", ""), $envelope_file->file_name);
        }
        return $envelope_file;
    }

    public static function checkSharedUploadDir()
    {
        $uploadDir = Settings::get(APConstants::ABSOLUTE_PATH_UPLOAD_FILE);
        $uploadDirs = array(
            $uploadDir,
            $uploadDir . 'temp',
            $uploadDir . 'images',
            $uploadDir . 'images/envelope',
            $uploadDir . 'images/location',
            $uploadDir . 'images/logo',
            $uploadDir . 'images/tmp',
        );
        foreach ($uploadDirs as $dir) {
            if (is_dir($dir)) {
                if (!is_writable($dir)) chmod($dir, 0777);
            } else {
                mkdir($dir, 0777);
            }
        }

        return true;
    }

    /**
     * Convert a money amount in EUR to another currency (ex: USD) with a specified decimal separator.
     *
     * @param $amount_in_EUR float The money amount in EUR
     * @param $exchange_rate float The exchange rate from EUR to another currency
     * @param $decimals integer The number of digits after the decimal separator.
     * @param $decimal_separator string One character of either ',' or '.'.
     *
     * @return string The converted amount in another currency
     */
    public static function convertCurrency($amount_in_EUR, $exchange_rate, $decimals = 2, $decimal_separator = ',')
    {
        $exchange_amount = floatval($amount_in_EUR) * floatval($exchange_rate);
        if ($decimal_separator == APConstants::DECIMAL_SEPARATOR_COMMA) {
            return number_format($exchange_amount, $decimals, ',', '.');
        } elseif ($decimal_separator == APConstants::DECIMAL_SEPARATOR_DOT) {
            return number_format($exchange_amount, $decimals, '.', ',');
        } else {
            return number_format($exchange_amount, $decimals, ',', '.');
        }
    }
}