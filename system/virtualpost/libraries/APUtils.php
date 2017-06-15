<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 * @author DungNT
 */
class APUtils
{
    const STRING_SPECIAL_CHAR = 'Ë À Ì Â Í Ã Î Ä Ï Ç Ò È Ó É Ô Ê Õ Ö ê Ù ë Ú î Û ï Ü ô Ý õ â û ã ÿ ç';

    const STRING_NORMALIZE_CHARS = array(
        'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
        'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
        'Ï'=>'I', 'Ñ'=>'N', 'Ń'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
        'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
        'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
        'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ń'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
        'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f',
        'ă'=>'a', 'î'=>'i', 'â'=>'a', 'ș'=>'s', 'ț'=>'t', 'Ă'=>'A', 'Î'=>'I', 'Â'=>'A', 'Ș'=>'S', 'Ț'=>'T',
    );

    /**
     * Settings cache
     *
     * @var array
     */
    private static $cache_ship = array();

    /**
     * The Settings Construct
     */
    public function __construct()
    {
        ci()->load->helper('text');

        $this->ci = &get_instance();
    }

    /**
     * Format number display to: 999.999,00 OR 999,999.00 depending on decimal separator
     *
     * @param float $number
     * @param integer $decimals
     * @param string $decimal_separator
     *
     * @return string the formatted value
     */
        public static function number_format($number, $decimals = 2, $decimal_separator = ',')
    {
    	if ($number === "" || $number === NULL) {
    		return '';
    	}
        $number_format = floatval($number);
        if ($decimal_separator == APConstants::DECIMAL_SEPARATOR_COMMA) {
            return number_format($number_format, $decimals, ",", ".");
        } else if ($decimal_separator == APConstants::DECIMAL_SEPARATOR_DOT) {
            return number_format($number_format, $decimals, ".", ",");
        } else {
            return number_format($number_format, $decimals, ",", ".");
        }
    }

    public static function autoHidenTextUTF8($str, $startPosition = 0, $encoding = 'UTF-8', $numberLastCharacter = 2, $strCompare=", "){

            if(empty($str)) return '';

            if( mb_substr($str, (mb_strlen($str,$encoding) - $numberLastCharacter), mb_strlen($str, $encoding), $encoding) == $strCompare){

                   $str = mb_substr($str, $startPosition,(mb_strlen($str,$encoding) - $numberLastCharacter), $encoding);
            }
            return $str;
    }

    /**
     * Generate random charactor.
     *
     * @param unknown_type $length
     */
    public static function generateRandom($length = 8)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $size = strlen($chars);
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[rand(0, $size - 1)];
        }
        return $str;
    }

    /**
     * Get timeout value from config file
     */
    public static function getConfigTimeout()
    {
        return ci()->config->item("sess_expiration");
    }

    /**
     * Generate password with raw data and md5 encode password.
     *
     * @param unknown_type $length
     */
    public static function generatePassword($length = 8)
    {
        $raw_data = APUtils::generateRandom($length);
        return array(
            "raw_pass" => $raw_data,
            "encoded" => md5($raw_data)
        );
    }

    /**
     * Generates an UUID.
     *
     * @param
     *            string an optional prefix
     * @return string the formatted uuid
     */
    public static function uuid($prefix = '')
    {
        $chars = md5(uniqid(mt_rand(), true));
        $uuid = substr($chars, 0, 8) . '-';
        $uuid .= substr($chars, 8, 4) . '-';
        $uuid .= substr($chars, 12, 4) . '-';
        $uuid .= substr($chars, 16, 4) . '-';
        $uuid .= substr($chars, 20, 12);
        return $prefix . $uuid;
    }

    /**
     * convert string to time stamp.
     * Ex: 2012-12-12 => 1254366523.
     *
     * @param unknown_type $time
     * @return number
     */
    public static function convert_date_to_timestamp($time)
    {
        return strtotime($time);
    }

    /**
     * convert timestamp to date with format.
     * Ex: 1254366523=>2012.12.12
     *
     * @param unknown_type $time
     * @param unknown_type $format
     */
    public static function convert_timestamp_to_date($timestamp, $format = 'd.m.Y')
    {
        if ($timestamp) return date($format, $timestamp);
        else
            return false;
    }

    /**
     * convert timestamp to date with format.
     * Ex: 1254366523=>2012.12.12
     *
     * @param unknown_type $time
     * @param unknown_type $format
     */
    public static function remove_time($timestamp)
    {
        if ($timestamp == 0) {
            return 0;
        }
        return APUtils::convert_date_to_timestamp(APUtils::convert_timestamp_to_date($timestamp, 'Y-m-d'));
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
    public static function convert_currency($amount_in_EUR, $exchange_rate, $decimals = 2, $decimal_separator = ',')
    {
        $exchange_amount = floatval($amount_in_EUR) * floatval($exchange_rate);

        if(abs($exchange_amount) < 0.005){
            return number_format(0, $decimals, ',', '.');
        }else if ($decimal_separator == APConstants::DECIMAL_SEPARATOR_COMMA) {
            return number_format($exchange_amount, $decimals, ',', '.');
        } elseif ($decimal_separator == APConstants::DECIMAL_SEPARATOR_DOT) {
            return number_format($exchange_amount, $decimals, '.', ',');
        } else {
            return number_format($exchange_amount, $decimals, ',', '.');
        }
    }

    /*
     * Convert a money amount in EUR to another currency once!
     * Notice: This method should only be used in the case of one-time conversion because of performance-related reason!
     */
    public static function convert_currency_once($amount_in_EUR, $decimals = 2)
    {
        ci()->load->model('customers/customer_m');

        $customer_id = APContext::getCustomerCodeLoggedIn();
        $decimal_separator = ci()->customer_m->get_standard_setting_decimal_separator($customer_id);
        $currency = ci()->customer_m->get_standard_setting_currency($customer_id);
        $exchange_rate = $currency->currency_rate;

        return self::convert_currency($amount_in_EUR, $exchange_rate, $decimals, $decimal_separator);
    }

    /**
     * Convert string to date.
     *
     * @param unknown_type $date_string
     * @param unknown_type $format
     */
    public static function parse_date_from_string($date_string, $format = 'Y.m.d H:i:s.S')
    {
        $dateInfo = date_parse_from_format($format, $date_string);
        return $dateInfo['day'] . '/' . $dateInfo['month'] . '/' . $dateInfo['year'];
    }

    /**
     * Check start with.
     * Enter description here ...
     *
     * @param unknown_type $haystack
     * @param unknown_type $needle
     * @return boolean
     */
    public static function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    /**
     * Check end with Enter description here .
     * ..
     *
     * @param unknown_type $haystack
     * @param unknown_type $needle
     * @return boolean
     */
    public static function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }

    /**
     * gets first day of week from date string.
     *
     * @param unknown_type $string_date
     * @return number
     */
    public static function getFirstDayOfWeek($string_date)
    {
        $day_of_week = date('N', strtotime($string_date));
        return strtotime($string_date . " - " . ($day_of_week - 1) . " days");
    }

    /**
     * Gets common message.
     *
     * @param unknown_type $message_key
     * @param unknown_type $replace_value
     * @return string
     */
    public static function getMessage($message_key, $replace_value)
    {
        return sprintf($message_key, $replace_value);
    }

    /**
     * Convert array to object Enter description here .
     * ..
     *
     * @param unknown_type $array_object
     */
    public static function convertArrayToObject($array_input)
    {
        $result = new stdClass;
        foreach ($array_input as $key => $value) {
            $result->$key = $value;
        }
        return $result;
    }

    /**
     * Convert array to object Enter description here .
     * ..
     *
     * @param unknown_type $array_object
     */
    public static function convertObjectToArray($object)
    {
        if (!is_object($object) && !is_array($object)) {
            return $object;
        }
        if (is_object($object)) {
            $object = get_object_vars($object);
        }
        return $object;
    }

    /**
     * Check value exist in array Enter description here .
     * ..
     *
     * @param unknown_type $key
     * @param unknown_type $array
     */
    public static function is_array_exist($val, $array)
    {
        foreach ($array as $item) {
            if ($val == $item) {
                return true;
            }
        }
        return false;
    }

    /**
     * Gets end day of week.
     *
     * @param unknown_type $string_date
     * @return number
     */
    public static function getEndDayOfWeek($string_date)
    {
        $ts = strtotime($string_date);
        $start = (date('w', $ts) == 0) ? $ts : strtotime('last sunday', $ts);
        return strtotime('next saturday', $start);
    }





    /**
     * Replace all key by array of value.
     *
     * @param unknown_type $content :
     *            E.g: Please replace thhis value: {{user_name}}
     * @param unknown_type $data :
     *            The array of data: array("user_name" => "DungNT")
     */
    public static function parserString($content, $data)
    {
        return ci()->parser->parse_string(str_replace('&quot;', '"', $content), $data, TRUE);
    }

    /**
     * Break line with level.
     *
     * @param unknown_type $text
     * @param unknown_type $level
     */
    public static function breakLine($text, $level = 1)
    {
        $break_length = 32;
        if ($level === 2) {
            $break_length = 29;
        } else if ($level === 3) {
            $break_length = 26;
        } else if ($level === 4) {
            $break_length = 23;
        }
        $start = 0;
        $result = '';
        $text_arr = explode(' ', $text);
        foreach ($text_arr as $word) {
            $temp = $word;
            if ($start + strlen($word) > $break_length) {
                if (!empty($result)) {
                    $result = $result . '</br>';
                } else {
                    $temp = $temp . '</br>';
                }
                $start = 0;
            } else {
                $start += strlen($word) + 1;
            }
            $result = $result . $temp . ' ';
        }

        return $result;
    }

    /**
     * Check the string contains key.
     *
     * @param unknown_type $key
     * @param unknown_type $text
     */
    public static function contains($key, $text)
    {
        $text = str_replace(' ', '', $text);
        $key = str_replace(' ', '', $key);
        if (strpos(strtoupper($text), strtoupper($key)) !== false) {
            return true;
        }
        return false;
    }

    /**
     * Highlight text math
     *
     * @param unknown_type $ProductNr
     * @param unknown_type $text_match
     */
    public static function highlightProductMatch($ProductNr, $text_match)
    {
        return highlight_phrase($ProductNr, $text_match, '<span style="background-color:#FFF200">', '</span>');
    }

    /**
     * Auto hidden text and display html tooltip
     *
     * @param unknown_type $str
     * @param unknown_type $max_len
     */
    public static function autoHidenText($str = "", $max_len = 15, $display = true)
    {
        if (strlen($str) <= ($max_len + 3)) {
            return $str;
        }
        if ($display) {
            return '<span title="' . $str . '">' . substr($str, 0, $max_len) . '...</span>';
        } else {
            return '<span>' . substr($str, 0, $max_len) . '...</span>';
        }
    }

    /**
     * Auto hidden text and display html tooltip
     *
     * @param unknown_type $str
     * @param unknown_type $max_len
     */
    public static function autoHidenTextByLabel($str = "", $max_len = 15)
    {
        if (strlen($str) <= ($max_len + 3)) {
            return $str;
        }

        return '<label title="' . $str . '">' . substr($str, 0, $max_len) . '...</label>';
    }

    /**
     * get target month to invoice
     */
    public static function getTargetMonthInvoice()
    {
        // return date("m", strtotime("last month"));
        return date("m", now());
    }

    /**
     * get target month to invoice
     */
    public static function getPreviousMonth()
    {
        return date("m", strtotime("last month"));
    }

    /**
     * get target month to invoice
     */
    public static function getCurrentMonthInvoice()
    {
        return date("m", now());
    }

    /**
     * get target year to invoice Return format yyyy
     */
    public static function getTargetYearInvoice()
    {
        return date("Y", now());
    }

    /**
     * get target year to invoice.
     * Return format yyyy
     */
    public static function getCurrentYearInvoice()
    {
        return date("Y", now());
    }

    /**
     * get target year to invoice Return format dd.
     */
    public static function getCurrentDayInvoice()
    {
        return date("d", now());
    }

    /**
     * Get current year.
     * Return format yyyy
     */
    public static function getCurrentYear()
    {
        return date("Y", now());
    }

    /**
     * Get current year.
     * Return format yy
     */
    public static function getCurrentYearShort()
    {
        return date("y", now());
    }

    /**
     * Get current year.
     * Return format MM
     */
    public static function getCurrentMonth()
    {
        return date("m", now());
    }

    /**
     * Get current year.
     * Return format yyyyMM
     */
    public static function getCurrentYearMonth()
    {
        return date("Ym", now());
    }

    /**
     * Get current year.
     * Return format yyyyMMdd.
     */
    public static function getCurrentYearMonthDate()
    {
        return date("Ymd", now());
    }

    /**
     * Check current date is last day of current month.
     */
    public static function isLastDayOfMonth()
    {
        $current_date = APUtils::getCurrentYearMonthDate();
        $last_date_of_month = date('Ymd', strtotime(APUtils::getLastDayOfCurrentMonth()));
        return $current_date === $last_date_of_month;
    }

    /**
     * Check current date is last day of current month.
     */
    public static function isFirstDayOfMonth()
    {
        $current_day = APUtils::getCurrentDayInvoice();
        return $current_day === '01';
    }

    /**
     * Format month display by 2 digit
     *
     * @param unknown_type $month
     */
    public static function formatMonth($month)
    {
        if ($month < 10) {
            return '0' . $month;
        }
        return $month;
    }

    /**
     * Check current date is last day of current quart.
     */
    public static function isLastDayOfQuart()
    {
        $current_date = APUtils::getCurrentYearMonthDate();
        $current_month = date('m', now());
        $last_date_of_month = date('Ymd', strtotime(APUtils::getLastDayOfCurrentMonth()));
        return $current_date === $last_date_of_month && ($current_month === '03' || $current_month === '06' || $current_month === '09' ||
            $current_month === '12');
    }

    /**
     * Check is current day = next day of quart.
     */
    public static function isNextDayOfQuart($date_setting)
    {
        $flag_day = false;
	if( (date("t") < date("d",strtotime($date_setting))) && (date("d") == date("t") ) ){
            $flag_day = true;
	}
	if(date("d") == date("d",strtotime($date_setting))){
            $flag_day = true;
	}
	$diff_m = (date("Y") - date("Y",strtotime($date_setting))) * 12 + (date("m") - date("m",strtotime($date_setting)));
	if( $diff_m%3 == 0 ) {
            if($diff_m == 0){
                $current_time = mktime(0, 0, 0, date('m'), date("d"), date('Y'));
                $next_time    = strtotime("+3 month", $current_time);
                if( (date("Y-m") == date("Y-m",$next_time)) && $flag_day ){
                    return true;
                }
            }
            else{
                if($flag_day){
                    return true;
                }
            }

	}
	if( $diff_m%3 == 1 ) {

            $current_time = mktime(0, 0, 0, date('m'), date("d"), date('Y'));
            $next_time    = strtotime("+2 month", $current_time);
            if( (date("Y-m") == date("Y-m",$next_time)) && $flag_day ){
                return true;
            }
	}
	if( $diff_m%3 == 2 ) {
            $current_time = mktime(0, 0, 0, date('m'), date("d"), date('Y'));
            $next_time    = strtotime("+1 month", $current_time);
            if( (date("Y-m") == date("Y-m",$next_time)) && $flag_day ){
                return true;
            }
	}
	return false;
    }

    /*
     * Get next day of quart, show on page account of customer
    */
    public static function nextDayOfQuart($date_setting){

	$diff_m = (date("Y") - date("Y",strtotime($date_setting))) * 12 + (date("m") - date("m",strtotime($date_setting)));
	//echo $diff_m;exit;
	if( $diff_m%3 == 0 ) {
		if($diff_m == 0){
			$current_time = mktime(0, 0, 0, date('m'), date("d"), date('Y'));
			$next_time    = strtotime("+3 month", $current_time);
			if( (date("t",$next_time) < date("d",strtotime($date_setting))) ){
				return date("t-m-Y",$next_time);
			}
			return date("d",strtotime($date_setting))."-".date("m-Y",$next_time);
		}
		else{
			if( (date("t") < date("d",strtotime($date_setting))) ){
				return date("t-m-Y");
			}
			return date("d",strtotime($date_setting))."-".date("m-Y");
		}

	}
	if( $diff_m%3 == 1 ) {

		$current_time = mktime(0, 0, 0, date('m'), date("d"), date('Y'));
		$next_time    = strtotime("+2 month", $current_time);
		if( (date("t",$next_time) < date("d",strtotime($date_setting))) ){
			return date("t-m-Y",$next_time);
		}
		return date("d",strtotime($date_setting))."-".date("m-Y",$next_time);
	}
	if( $diff_m%3 == 2 ) {
		$current_time = mktime(0, 0, 0, date('m'), date("d"), date('Y'));
		$next_time    = strtotime("+1 month", $current_time);
		if( (date("t",$next_time) < date("d",strtotime($date_setting))) ){
			return date("t-m-Y",$next_time);
		}
		return date("d",strtotime($date_setting))."-".date("m-Y",$next_time);
	}
	return '';
    }

    /*
     * Get next day of monthly, show on page account of customer
    */
    public static function nextDayOfMonthly($day_of_week){

        switch ($day_of_week) {
            case "2":
                $text_day = "Mon";
                break;
            case "3":
                $text_day = "Tue";
                break;
            case "4":
                $text_day = "Wed";
                break;
            case "5":
                $text_day = "Thu";
                break;
            case "6":
                $text_day = "Fri";
                break;
            default:
                $text_day = "";
                break;
        }
        if(empty($text_day)){
            return "";
        }
        $next_time = date("d.m.Y", strtotime("last $text_day of this month"));
        if($next_time < date("d.m.Y")){
                $next_time = date("d.m.Y", strtotime("last $text_day of next month"));
        }
        return $next_time;
    }

    /**
     * Check current date is last day of current quart.
     */
    public static function isFirstDayOfQuart()
    {
        $current_date = APUtils::getCurrentYearMonthDate();
        $current_month = date('m', now());
        return $current_date === '01' && ($current_month === '03' || $current_month === '06' || $current_month === '09' || $current_month === '12');
    }

    /**
     * Check current date is last weeken day
     */
    public static function isLastWeekenDay()
    {
        $weekDay = date('w', now());
        $weekHour = date('H', now());
        return ($weekDay == 0);
    }

    /**
     * Check current date is last weeken day
     */
    public static function isEndOfDay()
    {
        $weekHour = date('H', now());
        return ($weekHour == 23 || $weekHour <= 1);
    }

    /**
     * get target month to invoice $yearmonth = '201302'
     */
    public static function getFirstDayOfMonth($yearmonth)
    {
        return $yearmonth . '01';
    }

    /**
     * get first day of current month
     */
    public static function getFirstDayOfCurrentMonth()
    {
        return APUtils::getFirstDayOfMonth(APUtils::getCurrentYearMonth());
    }

    /**
     * get first day of current month
     */
    public static function getFirstDayOfPreviousMonth()
    {
        return APUtils::getFirstDayOfMonth(date("Ym", strtotime("last month")));
    }

    /**
     * get first day of current month
     */
    public static function getFirstDayOfNextMonth()
    {
        $end_date = date('Ymd', strtotime('first day of next month'));
        return $end_date;
    }

    /**
     * get last day of current month
     */
    public static function getLastDayOfCurrentMonth()
    {
        return APUtils::getLastDayOfMonth(APUtils::getFirstDayOfMonth(APUtils::getCurrentYearMonth()));
    }

    /**
     * get last day of current month
     */
    public static function getLastDayOfPreviousMonth()
    {
        return APUtils::getLastDayOfMonth(date("Ym", strtotime("last month")));
    }

    /**
     * get last day of current month
     */
    public static function getLastDayOfNextMonth()
    {
        $end_date = date('Ym', strtotime('first day of next month'));
        return APUtils::getLastDayOfMonth($end_date);
    }

    /**
     * get target month to invoice
     */
    public static function getLastDayOfMonth($fisrtDayOfMonth)
    {
        if (strlen($fisrtDayOfMonth) == 6) {
            $fisrtDayOfMonth = $fisrtDayOfMonth . '01';
        }
        return date("Ymt", strtotime($fisrtDayOfMonth));
    }

    /**
     * return date string: 'May 2013' $date = '20130507'
     *
     * @param unknown_type $date
     * @return string
     */
    public static function getMonthYearName($date)
    {
        return date("F Y", strtotime($date));
    }

    /**
     * Display date to format: dd.MM.yyyy
     *
     * @param unknown_type $strDate :
     *            Have format yyyy.MM.dd
     */
    public static function displayDate($strDate)
    {
        return date('d.m.Y', strtotime($strDate));
    }

    /**
     * Display date to format: dd/MM/yyyy (MM/dd/yyyy)
     *
     * @param unknown_type $strDate : Have format yyyy.MM.dd
     * @param string $format : 'd/m/y' or 'm/d/y'
     *
     */
    public static function displayDateFormat($strDate, $format)
    {
    	return date($format, strtotime($strDate));
    }

    /**
     * Get date diffirent from start date and end date.
     * yyyy and mm are same.
     *
     * @param unknown_type $startDate
     *            yyyymmdd
     * @param unknown_type $endDate
     *            yyyymmdd
     */
    public static function getDateDiff($startDate, $endDate)
    {
        $diff = abs(strtotime($endDate) - strtotime($startDate));
        return ceil($diff / (60 * 60 * 24)) + 1;
    }

    /**
     * Get date diffirent from start date and end date.
     * yyyy and mm are same.
     *
     * @param unknown_type $startDate
     *            yyyymmdd
     * @param unknown_type $endDate
     *            yyyymmdd
     */
    public static function getMongthDiff($startDate, $endDate)
    {
        $year1 = date('Y', $startDate);
        $year2 = date('Y', $endDate);

        $month1 = date('m', $startDate);
        $month2 = date('m', $endDate);

        $diff = (($year2 - $year1) * 12) + ($month2 - $month1);
    }

    /**
     * convert str to date: 2013.02.12
     *
     * @param unknown_type $strDate
     */
    public static function strToDateFormat($strDate)
    {
        $len = strlen($strDate);
        if ($len <= 4) {
            return $strDate;
        }

        if ($len <= 6) {
            return substr($strDate, 0, 4) . '.' . substr($strDate, 4, 2);
        }

        if ($len <= 8) {
            return substr($strDate, 0, 4) . '.' . substr($strDate, 4, 2) . '.' . substr($strDate, 6, 2);
        }
    }

    /**
     * convert str to number: 0,12 ==> 0.12
     *
     * @param unknown_type $strDate
     */
    public static function strToNumber($string_number)
    {
        $number = floatval(str_replace(',', '.', $string_number));
        return $number;
    }

    public static function xml_post($post_xml, $url, $port)
    {
        ci()->load->library('user_agent');
        $user_agent = ci()->agent->agent_string();

        // initialize curl handle
        $ch = curl_init();

        // set url to post to
        curl_setopt($ch, CURLOPT_URL, $url);
        // Fail on errors
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        // allow redirects
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        // return into a variable
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // Set the port number
        curl_setopt($ch, CURLOPT_PORT, $port);

        // times out after 15s
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        // add POST fields
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_xml);
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
        if ($port == 443) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        }
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    /**
     * Download file from amazone to local file
     *
     * @param unknown_type $preview_file
     */
    public static function download_amazon_file($preview_file)
    {
        ci()->load->library('S3');
        if (APUtils::is_amazon_file_changed($preview_file)) {
            $default_bucket_name = ci()->config->item('default_bucket');
            if (!empty($preview_file->amazon_relate_path)) {
                $res = S3::getObject($default_bucket_name, $preview_file->amazon_relate_path, $preview_file->local_file_name);
            }
        }
    }

    /**
     * Download file from amazone to local file
     *
     * @param unknown_type $preview_file
     */
    public static function download_amazon_file_tolocal($amazone_file_path, $local_file_path)
    {
        ci()->load->library('S3');
        $default_bucket_name = ci()->config->item('default_bucket');
        if (!empty($preview_file->amazon_relate_path)) {
            $res = S3::getObject($default_bucket_name, $amazone_file_path, $local_file_path);
        }
    }

    /**
     * Download file from amazone to local file
     *
     * @param unknown_type $preview_file
     */
    public static function is_amazon_file_changed($preview_file)
    {
        ci()->load->library('S3');
        $amazone_file_size = 0;
        $default_bucket_name = ci()->config->item('default_bucket');
        if (!empty($preview_file->amazon_relate_path)) {
            $res = S3::getObjectInfo($default_bucket_name, $preview_file->amazon_relate_path);
            $amazone_file_size = $res['size'];
        }
        $local_file_size = 0;
        if (!empty($preview_file->local_file_name) && file_exists($preview_file->local_file_name)) {
            $local_file_size = filesize($preview_file->local_file_name);
        }

        if ($amazone_file_size > 0 && $amazone_file_size != $local_file_size) {
            return true;
        }
        return false;
    }

    /**
     * Build delete sign message key.
     *
     * @param unknown_type $envelope
     * @param unknown_type $item
     * @param unknown_type $direct
     * @param unknown_type $collect
     *
     * @return key format: grey_grey_grey_grey
     */
    public static function build_delete_sign($envelope, $item, $direct, $collect, $package_id = '-1')
    {
        $result = APUtils::get_key_sign($envelope);
        $result = $result . '_' . APUtils::get_key_sign($item);
        $result = $result . '_' . APUtils::get_key_sign($direct);
        if( ($package_id =='' || $package_id == 0 ) && ($collect == '0') ){
            $result = $result . '_green' ;
        }else{
            $result = $result . '_' . APUtils::get_key_sign($collect);
        }

        return $result;
    }

    /**
     * Get key sign
     *
     * @param unknown_type $key
     */
    public static function get_key_sign($key)
    {
        if ($key == null) {
            return "grey";
        } else if ($key == '0') {
            return "yellow";
        } else if ($key == '1') {
            return "blue";
        }

        return "grey";
    }

    /**
     * Delete envelope by customer id.
     *
     * @param unknown_type $envelope_id
     */
    public static function delete_envelope_by_id($envelope_id, $deleted_by)
    {
        // Get all envelope file
        ci()->load->model('scans/envelope_file_m');
        $files = ci()->envelope_file_m->get_many_by_many(array(
            "envelope_id" => $envelope_id
        ));

        // Delete file content in amazone
        if ($files) {
            ci()->load->library('S3');
            $default_bucket_name = ci()->config->item('default_bucket');
            foreach ($files as $preview_file) {
                $res = S3::deleteObject($default_bucket_name, $preview_file->amazon_relate_path);
            }
        }

        // #452: log history before delete
        LogUtils::log_delete_envelope_by_id($envelope_id, $deleted_by);

        // Delete file
        ci()->envelope_file_m->delete_by_many(array(
            "envelope_id" => $envelope_id
        ));

        //ci()->load->model('mailbox/envelope_customs_m');
        //ci()->envelope_customs_m->delete_by_many(array(
        //    "envelope_id" => $envelope_id
        //));
    }

    /**
     * Revert charge scan for this envelope.
     * (envelope scan & item scan)
     *
     * @param unknown_type $envelope_id
     */
    public static function revert_envelope_shipping($envelope_id)
    {
        // Get detail envelope information
        // Get all envelope file
        ci()->load->model('scans/envelope_m');
        ci()->load->model('scans/envelope_summary_month_m');
        ci()->load->model('invoices/invoice_detail_m');
        ci()->load->library('invoices/invoices');
        $envelope = ci()->envelope_m->get_by_many(array(
            "id" => $envelope_id
        ));

        // Check exist envelope
        if (empty($envelope)) {
            return;
        }
        $customer_id = $envelope->to_customer_id;
        $postbox_id = $envelope->postbox_id;

        // Get target month
        $target_month = APUtils::getCurrentMonthInvoice();
        $target_year = APUtils::getCurrentYearInvoice();
        $current_summary = ci()->envelope_summary_month_m->get_by_many(
            array(
                "envelope_id" => $envelope_id,
                "customer_id" => $customer_id,
                "postbox_id" => $postbox_id,
                "year" => $target_year,
                "month" => $target_month
            ));

        // Truong hop da ton tai thong tin thi update
        if (!empty($current_summary)) {
            ci()->envelope_summary_month_m->update_by_many(
                array(
                    "envelope_id" => $envelope_id,
                    "customer_id" => $customer_id,
                    "postbox_id" => $postbox_id,
                    "year" => $target_year,
                    "month" => $target_month
                ),
                array(
                    "direct_shipping_number" => 0,
                    "direct_shipping_price" => 0,
                    "collect_shipping_number" => 0,
                    "collect_shipping_price" => 0
                ));
        }

        // Delete invoice detail [invoice_detail]
        ci()->invoice_detail_m->delete_by_many(
            array(
                "customer_id" => $customer_id,
                "envelope_id" => $envelope_id,
                "activity" => "Shipping&Handling"
            ));

        // Recalculate fee
        ci()->invoices->cal_invoice_summary($customer_id, $target_year, $target_month);
    }

    /**
     * Revert charge scan for this envelope.
     * (envelope scan & item scan)
     *
     * @param unknown_type $envelope_id
     */
    public static function revert_envelope_scan($envelope_id)
    {
        // Get detail envelope information
        // Get all envelope file
        ci()->load->model('scans/envelope_m');
        ci()->load->model('scans/envelope_summary_month_m');
        ci()->load->model('invoices/invoice_detail_m');
        ci()->load->library('invoices/invoices');
        $envelope = ci()->envelope_m->get_by_many(array(
            "id" => $envelope_id
        ));

        // Check exist envelope
        if (empty($envelope)) {
            return;
        }
        $customer_id = $envelope->to_customer_id;
        $postbox_id = $envelope->postbox_id;

        // Get target month
        $target_month = APUtils::getCurrentMonthInvoice();
        $target_year = APUtils::getCurrentYearInvoice();
        $current_summary = ci()->envelope_summary_month_m->get_by_many(
            array(
                "envelope_id" => $envelope_id,
                "customer_id" => $customer_id,
                "postbox_id" => $postbox_id,
                "year" => $target_year,
                "month" => $target_month
            ));

        // Truong hop da ton tai thong tin thi update
        if (!empty($current_summary)) {
            ci()->envelope_summary_month_m->update_by_many(
                array(
                    "envelope_id" => $envelope_id,
                    "customer_id" => $customer_id,
                    "postbox_id" => $postbox_id,
                    "year" => $target_year,
                    "month" => $target_month
                ),
                array(
                    "envelope_scan_number" => 0,
                    "envelope_scan_price" => 0,
                    "document_scan_number" => 0,
                    "document_scan_price" => 0
                ));
        }

        // Delete invoice detail [invoice_detail]
        ci()->invoice_detail_m->delete_by_many(
            array(
                "customer_id" => $customer_id,
                "envelope_id" => $envelope_id,
                "activity" => "Envelope scanning"
            ));
        // Delete invoice detail [invoice_detail]
        ci()->invoice_detail_m->delete_by_many(
            array(
                "customer_id" => $customer_id,
                "envelope_id" => $envelope_id,
                "activity" => "Scanning"
            ));

        // Recalculate fee
        ci()->invoices->cal_invoice_summary($customer_id, $target_year, $target_month);
    }

    /**
     * Revert charge for this envelope.
     *
     * @param unknown_type $envelope_id
     */
    public static function revert_envelope_incomming($envelope_id)
    {
        // Get detail envelope information
        // Get all envelope file
        ci()->load->model('scans/envelope_m');
        ci()->load->model('scans/envelope_shipping_m');
        ci()->load->model('scans/envelope_summary_month_m');
        ci()->load->model('invoices/invoice_detail_m');
        ci()->load->library('invoices/invoices');
        ci()->load->library('scans/scans_api');
        $envelope = ci()->envelope_m->get_by_many(array(
            "id" => $envelope_id
        ));

        // Check exist envelope
        if (empty($envelope)) {
            return;
        }
        $customer_id = $envelope->to_customer_id;
        $postbox_id = $envelope->postbox_id;

        // Update incoming number
        // Get target month
        $target_month = APUtils::getCurrentMonthInvoice();
        $target_year = APUtils::getCurrentYearInvoice();
        $current_summary = ci()->envelope_summary_month_m->get_by_many(
            array(
                "envelope_id" => $envelope_id,
                "customer_id" => $customer_id,
                "postbox_id" => $postbox_id,
                "year" => $target_year,
                "month" => $target_month
            ));

        // Truong hop da ton tai thong tin thi update
        if (!empty($current_summary)) {
            ci()->envelope_summary_month_m->update_by_many(
                array(
                    "envelope_id" => $envelope_id,
                    "customer_id" => $customer_id,
                    "postbox_id" => $postbox_id,
                    "year" => $target_year,
                    "month" => $target_month
                ),
                array(
                    "incomming_number" => 0,
                    "incomming_price" => 0,
                    "additional_incomming_flag" => '0'
                ));
        }

        // delete envelope_shippping
        ci()->envelope_shipping_m->delete_by_many( array(
            "customer_id" => $customer_id,
            "envelope_id" => $envelope_id,
            "from_unixtime(shipping_date, '%Y%m' )= '".$target_year.$target_month."'" => null
        ));

        ci()->invoice_detail_m->delete_by_many( array(
            "customer_id" => $customer_id,
            "envelope_id" => $envelope_id,
            "(activity_date <= '".$target_year.$target_month."31"."') " => null,
            "(activity_date >= '".$target_year.$target_month."00"."') " => null,
        ));

        // remove storage
        scans_api::updateStorageStatus($envelope_id, $customer_id, $postbox_id, $target_year, $target_month, $envelope->location_id, APConstants::OFF_FLAG);

        // Recalculate fee
        ci()->invoices->cal_storage_summary($customer_id);
        ci()->invoices->cal_invoice_summary($customer_id, $target_year, $target_month);
    }

    /**
     * Download
     *
     * @param str $url ,
     *            $path
     * @return bool || void
     */
    public static function download($url, $path)
    {
        // open file to write
        $fp = fopen($path, 'w+');
        // start curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // set return transfer to false
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // increase timeout to download big file
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        // write data to local file
        curl_setopt($ch, CURLOPT_FILE, $fp);
        // execute curl
        curl_exec($ch);
        // close curl
        curl_close($ch);
        // close local file
        fclose($fp);

        if (filesize($path) > 0) return true;
    }

    /**
     * Call remote url
     *
     * @param unknown_type $url
     * @return mixed
     */
    public static function callRemoteUrl($request,$postargs)
    {
        //The curl session is initialized using just the URL prefix as a parameter:
        $session = curl_init($request);

        // Tell curl to use HTTP POST
        curl_setopt ($session, CURLOPT_POST, true);
        // Tell curl that this is the body of the POST
        curl_setopt ($session, CURLOPT_POSTFIELDS, $postargs);
        // Tell curl not to return headers, but do return the response
        curl_setopt($session, CURLOPT_HEADER, false);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

        // execute the session
        $response = curl_exec($session);

        $url = $request . $postargs;

        log_message(APConstants::LOG_DEBUG, 'Call url: ' . $url . '| Response: ' . json_encode($response));

       //close the session
        curl_close($session);

        return $response;
    }

    /**
     * Postcode Validation Callback
     */
    public static function validname02($str)
    {
        if (empty($str)) {
            return array(
                "status" => TRUE,
                "message" => ''
            );
        }
        if (preg_match('/[\'^!£$%&*()}{@#~?><>,|=+¬]/', $str)) {

            return array(
                "status" => FALSE,
                "message" => 'Please enter valid %s.'
            );
        }
        $firstchar = substr($str, 0, 1);
        if (!preg_match('/^([a-zA-Z \+_\-]+)$/', $firstchar)) {
            return array(
                "status" => FALSE,
                "message" => 'Please enter valid %s.'
            );
        }
        return array(
            "status" => TRUE,
            "message" => ''
        );
    }

    /**
     * Postcode Validation Callback
     */
    public static function validname($str)
    {
        return array(
            "status" => true,
            "message" => ""
        );
    }

    /**
     * Postcode Validation Callback
     */
    public static function required($str)
    {
        if (empty($str)) {
            return array(
                "status" => FALSE,
                "message" => '%s is required input.'
            );
        }
        return array(
            "status" => TRUE,
            "message" => ''
        );
    }

    /**
     * CHeck first location of postbox.
     *
     * @param unknown $postbox_id
     * @param unknown $location_id
     */
    public static function isFirstLocationOfPostbox($postbox_id, $location_id)
    {
        // Load model
        ci()->load->model('mailbox/postbox_m');

        // Gets postbox by id
        $postbox = ci()->postbox_m->get($postbox_id);

        if ($postbox->location_available_id != $location_id && $postbox->first_location_flag == APConstants::OFF_FLAG) {
            return array(
                "status" => FALSE,

                // "message" => 'This %s can not change location
                // type.<br/>Because this location is additional.'
                "message" => "if you change the location of this postbox, the postbox type needs to be upgraded to \"Business\" type.<br/>Do you want to proceed?"
            );
        } else {
            return array(
                "status" => TRUE,
                "message" => ''
            );
        }
    }

    /**
     * Update account type
     *
     * @param unknown_type $customer_id
     */
    public static function updateAccountType($customer_id, $new_primary_location = '')
    {
        // Load model
        ci()->load->model('customers/customer_m');
        ci()->load->model('mailbox/postbox_m');
        ci()->load->model('email/email_m');

        $customer = ci()->customer_m->get($customer_id);
        if (empty($customer) || $customer->account_type == APConstants::ENTERPRISE_TYPE) {
            return false;
        }
        // Get customer information

        $postbox = ci()->postbox_m->get_max_postboxtype($customer_id);
        $postbox_type = $postbox ? ($postbox->MaxPostboxType ? $postbox->MaxPostboxType : APConstants::FREE_TYPE) : APConstants::FREE_TYPE;

        log_message('debug', '----------------------MAX POSTBOX TYPE:' . $postbox_type, FALSE);
        log_message('debug', '----------------------NEW MAIN POSTBOX ID :' . $postbox->postbox_id, FALSE);

        // Get max postbox type
        // Update account type of current customer
        //ci()->customer_m->update_by_many(array(
        //    "customer_id" => $customer_id
        //),
        //    array(
        //        // "account_type" => $postbox_type,
        //        "new_account_type" => null,
        //        "plan_date_change_account_type" => null
        //    ));

        // Update main postbox
        ci()->postbox_m->update_by_many(array(
            "customer_id" => $customer_id
        ), array(
            "is_main_postbox" => APConstants::OFF_FLAG,
            "first_location_flag" => APConstants::OFF_FLAG
        ));

        // check new primary location
        $flag_update = true;
        if ($new_primary_location) {
            // chekc first location postbox.
            $postbox_check = ci()->postbox_m->get_by_many(
                array(
                    "customer_id" => $customer_id,
                    "location_available_id" => $new_primary_location
                ));
            if ($postbox_check) {
                $flag_update = false;

                // reset primary location.
                ci()->postbox_m->update_by_many(array(
                    'customer_id' => $customer_id
                ), array(
                    "first_location_flag" => APConstants::OFF_FLAG,
                    "is_main_postbox" => APConstants::OFF_FLAG
                ));

                // Update main postbox
                ci()->postbox_m->update_by_many(
                    array(
                        "postbox_id" => $postbox_check->postbox_id,
                        'customer_id' => $customer_id
                    ),
                    array(
                        "is_main_postbox" => APConstants::ON_FLAG,
                        "first_location_flag" => APConstants::ON_FLAG
                    ));

                // Update primary location.
                ci()->postbox_m->update_by_many(
                    array(
                        'customer_id' => $customer_id,
                        'location_available_id' => $postbox_check->location_available_id
                    ), array(
                        "first_location_flag" => APConstants::ON_FLAG,
                        "is_main_postbox" => APConstants::ON_FLAG,
                ));
            }
        } else {
            // auto set primary location
            // chekc first location postbox.
            $postbox_check = ci()->postbox_m->get_by_many(
                array(
                    "customer_id" => $customer_id,
                    "first_location_flag" => 1
                ));

            if ($postbox_check) {
                $flag_update = false;

                // Update main postbox
                ci()->postbox_m->update_by_many(
                    array(
                        "postbox_id" => $postbox->postbox_id,
                        'customer_id' => $customer_id
                    ), array(
                    "is_main_postbox" => APConstants::ON_FLAG
                ));
            }
        }

        if ($flag_update) {
            // Update main postbox
            ci()->postbox_m->update_by_many(
                array(
                    "postbox_id" => $postbox->postbox_id,
                    'customer_id' => $customer_id
                ),
                array(
                    "is_main_postbox" => APConstants::ON_FLAG,

                    // update first location when postbox deleted.
                    "first_location_flag" => APConstants::ON_FLAG
                ));

            // update all same location as first
            $postbox_first = ci()->postbox_m->get_by_many(
                array(
                    "customer_id" => $customer_id,
                    "first_location_flag" => 1
                ));
            ci()->postbox_m->update_by_many(
                array(
                    "location_available_id" => $postbox_first->location_available_id,
                    'customer_id' => $customer_id
                ),
                array(

                    // update first location when postbox deleted.
                    "first_location_flag" => APConstants::ON_FLAG
                ));
        }
    }

    /**
     * update first location flag.
     *
     * @param unknown $customer_id
     */
    public static function updateFirstLocationOfPostbox($customer_id)
    {
        // Get customer information
        $customer = ci()->customer_m->get($customer_id);
        $postbox = ci()->postbox_m->get_max_postboxtype($customer_id);
        $postbox_type = $postbox ? ($postbox->MaxPostboxType ? $postbox->MaxPostboxType : APConstants::FREE_TYPE) : APConstants::FREE_TYPE; // default
        // is
        // free
        // account.
        log_message('debug', '----------------------MAX POSTBOX TYPE:' . $postbox_type, FALSE);
        log_message('debug', '----------------------NEW MAIN POSTBOX ID :' . $postbox->postbox_id, FALSE);

        // Update first location flag
        ci()->postbox_m->update_by_many(array(
            "postbox_id" => $postbox->postbox_id
        ), array(
            "first_location_flag" => APConstants::ON_FLAG
        ));

        // Update all same postox location is first
        $p = ci()->postbox_m->get_by("postbox_id", $postbox->postbox_id);
        ci()->postbox_m->update_by_many(
            array(
                "postbox_id" => $postbox->postbox_id,
                "location_available_id" => $p->location_available_id
            ), array(
            "first_location_flag" => APConstants::ON_FLAG
        ));
    }

    /**
     * Convert date format from yyyyMMdd to dd.MM.yyyy
     *
     * @param unknown_type $dateFormatFrom
     */
    public static function convertDateFormat01($dateFormatFrom)
    {
        if (empty($dateFormatFrom)) {
            return '';
        }
        $year = '';
        if (strlen($dateFormatFrom) >= 4) {
            $year = substr($dateFormatFrom, 0, 4);
        }
        $month = '';
        if (strlen($dateFormatFrom) >= 6) {
            $month = substr($dateFormatFrom, 4, 2);
        }
        $date = '';
        if (strlen($dateFormatFrom) >= 8) {
            $date = substr($dateFormatFrom, 6, 2);
        }
        return $date . '.' . $month . '.' . $year;
    }

    /**
     * Convert date format from dd.MM.yyyy => yyyyMMdd
     *
     * @param unknown_type $dateFormatFrom
     */
    public static function convertDateFormat02($dateFormatFrom)
    {
    	if (empty($dateFormatFrom)) {
    		return '';
    	}

    	$date = '';
    	if (strlen($dateFormatFrom) >= 2) {
    		$date = substr($dateFormatFrom, 0, 2);
    	}
    	$month = '';
    	if (strlen($dateFormatFrom) >= 5) {
    		$month = substr($dateFormatFrom, 3, 2);
    	}
    	$year = '';
    	if (strlen($dateFormatFrom) >= 10) {
    		$year = substr($dateFormatFrom, 6, 4);
    	}
    	return $year . $month . $date;
    }

    /**
     * Get current balance of customer.
     *
     * @param unknown_type $customer_id
     * @return number
     */
    public static function getCurrentBalance($customer_id)
    {
        $result = APUtils::getAdjustOpenBalanceDue($customer_id);
        return $result['OpenBalanceDue'];
    }

    /**
     * Get current balance of customer.
     *
     * @param unknown_type $customer_id
     * @return number
     */
    public static function getActualOpenBalanceDue($customer_id)
    {
        $result = APUtils::getAdjustOpenBalanceDue($customer_id);
        return $result['ActualOpenBalanceDue'];
    }

    /**
     * Get open balance due after adjust.
     * ( gia tri tra ve da bao gom VAT- gross total)
     *
     * @param unknown_type $customer_id
     *            The customer ID
     */
    public static function getAdjustOpenBalanceDue($customer_id)
    {
        if (empty($customer_id)) {
            return array(
                'OpenBalanceDue' => 0,
                'RemainChargeAmount' => 0,
                'ActualOpenBalanceDue' => 0,
                'OpenBalanceThisMonth' => 0
            );
        }
        // Check enterprise customer
        $customer = APContext::getCustomerByID($customer_id);
        if ($customer->account_type == APConstants::ENTERPRISE_CUSTOMER) {
            return self::getAdjustOpenBalanceDueOfEnterprise($customer_id);
        }

        // Standard customer
        ci()->load->model('invoices/invoice_summary_m');
        ci()->load->model('payment/external_tran_hist_m');
        ci()->load->model('payment/payone_tran_hist_m');
        $target_month = APUtils::getCurrentMonthInvoice();
        $target_year = APUtils::getCurrentYearInvoice();
        $current_invoice_month = $target_year . $target_month;
        $current_invoice_month_first_date = $target_year . $target_month . '01';

        // Gets gross price.
        $gross_price = ci()->invoice_summary_m->get_gross_price_of_customer($customer_id, $current_invoice_month);

        // Gets all external payment
        $external_charge_previous_01 = ci()->external_tran_hist_m->sum_by_many(
            array(
                'customer_id' => $customer_id,
                "status" => "OK",
                "tran_date < " => $current_invoice_month_first_date
            ), 'tran_amount');
        $external_charge_previous_02 = ci()->external_tran_hist_m->sum_by_many(
            array(
                'customer_id' => $customer_id,
                "status" => "OK",
                "tran_date >= " => $current_invoice_month_first_date,
                "tran_amount < " => "0"
            ), 'tran_amount');
        $external_charge_previous = $external_charge_previous_01 + $external_charge_previous_02;

        // gets credit note.
        // DungNT update 03.03.2017 (Remove condition LEFT(invoice_month,6) < {$current_invoice_month})
        // The credit note will consider same as payment
        $credit_charge = ci()->invoice_summary_m->get_credit_note_summary_openbalance_due($customer_id);

        $payone_charge = ci()->payone_tran_hist_m->sum_by_many(
            array(
                'customer_id' => $customer_id,
                "(txaction = 'paid')" => null
            ), 'amount');

        // added refund charge
        $payone_charge_refund = ci()->payone_tran_hist_m->sum_by_many(
            array(
                'customer_id' => $customer_id,
                "(txaction = 'refund' OR txaction = 'debit')" => null
            ), 'amount');
        $payone_charge = $payone_charge - abs($payone_charge_refund);

        $external_charge_previous = $external_charge_previous ? $external_charge_previous : 0;
        // #472: change gross -> net TOTAL.
        // $open_balance = abs($gross_price) - (abs($credit_charge) +
        // abs($external_charge) + abs($payone_charge));
        $open_balance = abs($gross_price) + ($external_charge_previous) - (abs($credit_charge) + abs($payone_charge));

        // Calculate open balance this month
		// DungNT update 03.03.2017 (Add condition total_invoice > 0)
        $gross_price_this_month = ci()->invoice_summary_m->sum_by_many(
            array(
                'customer_id' => $customer_id,

                // "(invoice_type = 'auto' OR invoice_type IS NULL)" =>
                // null,
                "LEFT(invoice_month,6) " => $current_invoice_month,
                "total_invoice > 0" => null
            ), 'total_invoice * (1 + vat)');
        $external_charge_current_this_month = ci()->external_tran_hist_m->sum_by_many(
            array(
                'customer_id' => $customer_id,
                "status" => "OK",
                "tran_date >= " => $current_invoice_month_first_date,
                "tran_amount > " => "0"
            ), 'tran_amount');
        $open_balance_this_month_gross = $gross_price_this_month + $external_charge_current_this_month;

       //Always display open balance, open balance this month >= 0 for customer
        //Open balance
        $adjust_open_balance_due = 0;
        //If > 0 => customer need pay for system, if < 0 => customer has a deposit in system
        $remain_charge_amount = $open_balance + $open_balance_this_month_gross;

        if ($open_balance >= 0) {
            $adjust_open_balance_due = $open_balance;
        } else {
            $adjust_open_balance_due = 0;
            $open_balance_this_month_gross = $open_balance_this_month_gross + $open_balance;
        }

        return array(
            'OpenBalanceDue' => $adjust_open_balance_due,
            'RemainChargeAmount' => $remain_charge_amount,
            'ActualOpenBalanceDue' => $open_balance,
            'OpenBalanceThisMonth' => $open_balance_this_month_gross
        );
    }

    /**
     * Get open balance due after adjust.
     * ( gia tri tra ve da bao gom VAT- gross total)
     *
     * @param unknown_type $customer_id
     *            The customer ID
     */
    private static function getAdjustOpenBalanceDueOfEnterprise($customer_id) {
        ci()->load->model('invoices/invoice_summary_m');
        ci()->load->model('payment/external_tran_hist_m');
        ci()->load->model('payment/payone_tran_hist_m');
        ci()->load->model('customers/customer_m');

        $target_month = APUtils::getCurrentMonthInvoice();
        $target_year = APUtils::getCurrentYearInvoice();
        $current_invoice_month = $target_year . $target_month;
        $current_invoice_month_first_date = $target_year . $target_month . '01';

        // Gets list users of enterprise customer.
        $list_customers = ci()->customer_m->get_many_by_many(array(
            "parent_customer_id = ".$customer_id." OR customer_id = ".$customer_id => null
        ));

        $list_customer_id = array();
        foreach($list_customers as $row){
            if(!empty($row->customer_id)){
                $list_customer_id[] = $row->customer_id;
            }
        }
        if (empty($list_customer_id) || count($list_customer_id) == 0) {
            $list_customer_id[] = 0;
        }
        $list_id = implode(',', $list_customer_id);

        // Gets gross price.
        $gross_price = ci()->invoice_summary_m->get_gross_price_of_customer($list_id, $current_invoice_month);

        // Gets all external payment
        $external_charge_previous_01 = ci()->external_tran_hist_m->sum_by_many( array(
                'customer_id IN ('.$list_id.')' => null,
                "status" => "OK",
                "tran_date < " => $current_invoice_month_first_date
            ), 'tran_amount');
        $external_charge_previous_02 = ci()->external_tran_hist_m->sum_by_many( array(
                'customer_id IN ('.$list_id.')' => null,
                "status" => "OK",
                "tran_date >= " => $current_invoice_month_first_date,
                "tran_amount < " => "0"
            ), 'tran_amount');
        $external_charge_previous = $external_charge_previous_01 + $external_charge_previous_02;

        // gets credit note.
        $credit_charge = ci()->invoice_summary_m->get_credit_note_summary_openbalance_due($list_id);

        $payone_charge = ci()->payone_tran_hist_m->sum_by_many( array(
                'customer_id IN ('.$list_id.')' => null,
                "(txaction = 'paid')" => null
            ), 'amount');

        // added refund charge
        $payone_charge_refund = ci()->payone_tran_hist_m->sum_by_many( array(
                'customer_id IN ('.$list_id.')' => null,
                "(txaction = 'refund' OR txaction = 'debit')" => null
            ), 'amount');
        $payone_charge = $payone_charge - abs($payone_charge_refund);
        $external_charge_previous = $external_charge_previous ? $external_charge_previous : 0;

        // open balance
        $open_balance = abs($gross_price) + ($external_charge_previous) - (abs($credit_charge) + abs($payone_charge));

        // Calculate open balance this month
        $gross_price_this_month = ci()->invoice_summary_m->sum_by_many( array(
            'customer_id IN ('.$list_id.')' => null,
            "LEFT(invoice_month,6) " => $current_invoice_month,
            "total_invoice > 0" => null
        ), 'total_invoice * (1 + vat)');
        $external_charge_current_this_month = ci()->external_tran_hist_m->sum_by_many( array(
            'customer_id IN ('.$list_id.')' => null,
            "status" => "OK",
            "tran_date >= " => $current_invoice_month_first_date,
            "tran_amount > " => "0"
        ), 'tran_amount');
        $open_balance_this_month_gross = $gross_price_this_month + $external_charge_current_this_month;

        //Always display open balance, open balance this month >= 0 for customer
        //Open balance
        $adjust_open_balance_due = 0;
        //If > 0 => customer need pay for system, if < 0 => customer has a deposit in system
        $remain_charge_amount = $open_balance + $open_balance_this_month_gross;

        if ($open_balance >= 0) {
            $adjust_open_balance_due = $open_balance;
        } else {
            $adjust_open_balance_due = 0;
            $open_balance_this_month_gross = $open_balance_this_month_gross + $open_balance;
        }

        return array(
            'OpenBalanceDue' => $adjust_open_balance_due,
            'RemainChargeAmount' => $remain_charge_amount,
            'ActualOpenBalanceDue' => $open_balance,
            'OpenBalanceThisMonth' => $open_balance_this_month_gross
        );
    }

    /**
     * Gets current balance of customer in this month.
     *
     * @param unknown $customer_id
     */
    public static function getCurrentBalanceThisMonth($customer_id)
    {
        $result = APUtils::getAdjustOpenBalanceDue($customer_id);
        $open_balance_this_month_gross = $result['OpenBalanceThisMonth'];
        return $open_balance_this_month_gross;
    }

    /**
     * Calculate total invoice
     *
     * @param unknown_type $invoice_summary
     */
    public static function calculateTotalInvoice($invoice_summary, $is_user_enterprise= false)
    {
        // $customer_id = $invoice_summary->customer_id;
        // #472: comment out.
        // $vat = APUtils::getVatFeeByCustomer($customer_id);
        // $vat_total = 1.19;

        // Tinh de luu lai thong tin
        $amount1 = $invoice_summary->free_postboxes_amount;
        $amount1 += $invoice_summary->private_postboxes_amount;
        $amount1 += $invoice_summary->business_postboxes_amount;
        // comment: fixbug #473 change gross price -> net price
        // $amount1 = $amount1 / ($vat_total - $vat);

        $amount2 = $invoice_summary->incomming_items_free_account;
        $amount2 += $invoice_summary->incomming_items_private_account;
        $amount2 += $invoice_summary->incomming_items_business_account;
        $amount2 += $invoice_summary->envelope_scan_free_account;
        $amount2 += $invoice_summary->envelope_scan_private_account;
        $amount2 += $invoice_summary->envelope_scan_business_account;
        $amount2 += $invoice_summary->item_scan_free_account;
        $amount2 += $invoice_summary->item_scan_private_account;
        $amount2 += $invoice_summary->item_scan_business_account;
        $amount2 += $invoice_summary->storing_letters_free_account;
        $amount2 += $invoice_summary->storing_letters_private_account;
        $amount2 += $invoice_summary->storing_letters_business_account;
        $amount2 += $invoice_summary->storing_packages_free_account;
        $amount2 += $invoice_summary->storing_packages_private_account;
        $amount2 += $invoice_summary->storing_packages_business_account;

        $amount2 += $invoice_summary->additional_pages_scanning_free_amount;
        $amount2 += $invoice_summary->additional_pages_scanning_private_amount;
        $amount2 += $invoice_summary->additional_pages_scanning_business_amount;
        // comment: fixbug #473 change gross price -> net price
        // $amount2 = $amount2 / ($vat_total - $vat);

        $amount3 = 0;
        $amount3 += $invoice_summary->direct_shipping_free_account;
        $amount3 += $invoice_summary->direct_shipping_private_account;
        $amount3 += $invoice_summary->direct_shipping_business_account;
        $amount3 += $invoice_summary->collect_shipping_free_account;
        $amount3 += $invoice_summary->collect_shipping_private_account;
        $amount3 += $invoice_summary->collect_shipping_business_account;
        // // comment: fixbug #473 change gross price -> net price
        // $amount3 = $amount3 * (1 + $vat);

        $amount3 += $invoice_summary->custom_declaration_outgoing_quantity_01 * $invoice_summary->custom_declaration_outgoing_price_01;
        $amount3 += $invoice_summary->custom_declaration_outgoing_quantity_02 * $invoice_summary->custom_declaration_outgoing_price_02;

        $amount4 = 0;
        if(!$is_user_enterprise){
            $amount4 += $invoice_summary->api_access_amount;
            $amount4 += $invoice_summary->own_location_amount;
            $amount4 += $invoice_summary->touch_panel_own_location_amount;
            $amount4 += $invoice_summary->own_mobile_app_amount;
            $amount4 += $invoice_summary->clevver_subdomain_amount;
            $amount4 += $invoice_summary->own_subdomain_amount;
        }
        
        $total_invoice = $amount1 + $amount2 + $amount3 + $amount4;
        return $total_invoice;
    }

    /**
     * Calculate total invoice
     *
     * @param unknown_type $invoice_summary
     */
    public static function genetateReference($invoice_summary)
    {
        $customer_id = $invoice_summary->customer_id;
        $invoice_id = 'INV_' . $invoice_summary->id . '_' . $customer_id . '_' . APUtils::generateRandom(4);
        return $invoice_id;
    }

    /**
     * Calculate total invoice
     *
     * @param unknown_type $invoice_summary
     */
    public static function genetateReferenceForOpenBalance($customer_id)
    {
        $invoice_id = 'INV_SUM_' . $customer_id . '_' . APUtils::generateRandom(4);
        return $invoice_id;
    }

    /**
     * Convert array to CSV file.
     *
     * @param unknown_type $list_item
     */
    public static function arrayToCsv($list_item)
    {
        $output = '';
        foreach ($list_item as $item) {
            $output .= APUtils::arrayToLineCsv($item) . PHP_EOL;
        }
        return $output;
    }

    /**
     * Formats a line (passed as a fields array) as CSV and returns the CSV as a
     * string.
     * Adapted from
     * http://us3.php.net/manual/en/function.fputcsv.php#87120
     */
    public static function arrayToLineCsv(array &$fields, $delimiter = ';', $enclosure = '"', $encloseAll = false, $nullToMysqlNull = false)
    {
        $delimiter_esc = preg_quote($delimiter, '/');
        $enclosure_esc = preg_quote($enclosure, '/');

        $output = array();
        foreach ($fields as $field) {
            if ($field === null && $nullToMysqlNull) {
                $output[] = 'NULL';
                continue;
            }

            // Enclose fields containing $delimiter, $enclosure or whitespace
            if ($encloseAll || preg_match("/(?:${delimiter_esc}|${enclosure_esc}|\s)/", $field)) {
                $output[] = $enclosure . str_replace($enclosure, $enclosure . $enclosure, $field) . $enclosure;
            } else {
                $output[] = $field;
            }
        }

        return implode($delimiter, $output);
    }

    /**
     * Gets instance code of partner
     */
    public static function getInstanceCode()
    {
        return "01";
    }

    /**
     * fill zero into code.
     *
     * @param unknown $id
     * @param unknown $len
     */
    public static function fillZeroIntoCode($id, $len = 3)
    {
        if (strlen($id) > $len) {
            return $id;
        }

        $flag = true;
        $result = $id;
        while ($flag) {
            $result = "0" . $result;
            if (strlen($result) >= $len) {
                break;
            }
        }

        return $result;
    }

    /**
     * Generate partner code.
     *
     * @param unknown $partner_id
     */
    public static function generatePartnerCode($partner_id)
    {
        $code = substr(md5($partner_id), 0, 3) . APUtils::generateRandom(4);
        return $code;
    }

    /**
     * Load list access locations by admin role
     */
    public static function loadListAccessLocation()
    {
        ci()->load->model('addresses/location_m');
        ci()->load->model('addresses/location_users_m');
        ci()->load->library('addresses/addresses_api');
        ci()->load->model('addresses/location_customers_m');
        $user = APContext::getAdminLoggedIn();

        $list_access_location = array();
        if (APContext::isAdminUser()) {

            $list_access_location = ci()->location_m->get_all();
            // $list_access_location = addresses_api::getLocationPublic();
        }
        else if (APContext::isAdminParner()) {

            $list_access_location = ci()->location_users_m->get_location_users_available($user->id);
        } else if (APContext::isAdminLocation()) {

            $list_access_location = ci()->location_users_m->get_location_users_available($user->id);

        } else if (APContext::isWorkerAdmin()) {

            $list_access_location = ci()->location_users_m->get_location_users_available($user->id);
        }
        $enterprise_locations = ci()->location_customers_m->get_all_enterprise_location();
        $map_enterprise_locations = array();
        foreach ($enterprise_locations as $item) {
            if (!array_key_exists($item->location_id, $map_enterprise_locations)) {
                $map_enterprise_locations[$item->location_id] = $item->customer_code;
            }
        }

        // Change location name
        foreach ($list_access_location as $location) {
            $item_name = $location->location_name;
            if (array_key_exists($location->id, $map_enterprise_locations)) {
                $item_name = $item_name.' Enterprise '.$map_enterprise_locations[$location->id];
            }
            $location->location_name = $item_name;
        }

        return $list_access_location;
    }


    public static function mobileLoadListAccessLocation()
    {
        ci()->load->model('addresses/location_m');
        ci()->load->model('addresses/location_users_m');

        $user = MobileContext::getAdminLoggedIn();

        $list_access_location = array();

        if (MobileContext::isAdminUser()) {

            $list_access_location = ci()->location_m->get_all();
        }
        else if (MobileContext::isAdminParner()) {

            $list_access_location = ci()->location_users_m->get_location_users_available($user->id);

        } else if (MobileContext::isAdminLocation()) {

            $list_access_location = ci()->location_users_m->get_location_users_available($user->id);

        } else if (MobileContext::isWorkerAdmin()) {

            $list_access_location = ci()->location_users_m->get_location_users_available($user->id);
        }

        return $list_access_location;
    }

    /**
     * load array list access location.
     * @return type
     */
    public static function loadArrayListAccessLocationOnMobile(){
        $list_access_location = APUtils::mobileLoadListAccessLocation();
        $list_access_location_id = array();

        if ($list_access_location && count($list_access_location) > 0) {
            foreach ($list_access_location as $location) {
                $list_access_location_id[] = $location->id;
            }
        }
        return $list_access_location_id;
    }

    /**
     * return list of ids from array object.
     *
     * @param unknown $locations
     */
    public static function getListIdsOfObjectArray($arr_obj, $key = 'id')
    {
        $result = array();

        if ($arr_obj) {
            foreach ($arr_obj as $obj) {
                $result[] = $obj->$key;
            }
        }

        return $result;
    }

    /**
     * Get paypal transaction fee
     */
    public static function includePaypalTransactionFee($amount, $customer_id)
    {
        ci()->load->library('price/price_api');

        $total_amount = $amount;
        $pricing_map = price_api::getDefaultPricingModel();
        $paypal_transaction_fee_rate = $pricing_map[1]['paypal_transaction_fee'];
        $paypal_transaction_vat = APUtils::getVatRateOfCustomer($customer_id);

        $paypal_transaction_vat_rate = $paypal_transaction_vat->rate;

        $paypal_transaction_fee = round($amount * ($paypal_transaction_fee_rate / 100), 2);
        $paypal_transaction_vat_value = round($paypal_transaction_fee * $paypal_transaction_vat_rate, 2);
        $total_amount += $paypal_transaction_fee + $paypal_transaction_vat_value;
        return array(
            'total_amount' => $total_amount,
            'paypal_transaction_fee' => $paypal_transaction_fee,
            'paypal_transaction_vat' => $paypal_transaction_vat_value
        );
    }

    /**
     * Crewate vat case list.
     *
     * @param unknown $vat_cases
     */
    public static function createVatCaseList($vat_cases)
    {
        $vat_cases_list = '';
        $first = true;
        foreach ($vat_cases as $case) {
            if ($first) {
                $vat_cases_list .= $case->vat_case_id . "," . $case->rate . ":" . $case->text . " - " . ($case->rate * 100) . "%";
                $first = false;
            } else {
                $vat_cases_list .= ";" . $case->vat_case_id . "," . $case->rate . ":" . $case->text . " - " . ($case->rate * 100) . "%";
            }
        }

        return $vat_cases_list;
    }

    /**
     * Get vat rate of customer.
     *
     * @param unknown $customer_id
     * @return number
     */
    public static function getVatRateOfCustomer($customer_id)
    {
        if (APUtils::isPrivateCustomer($customer_id)) {
            return APUtils::getVatCustomerByType($customer_id, APConstants::CUSTOMER_TYPE_PRIVATE);
        } else if (APUtils::isEnterpriseCustomer($customer_id)) {
            return APUtils::getVatCustomerByType($customer_id, APConstants::CUSTOMER_TYPE_ENTERPRISE);
        }
    }

    /**
     * Get VAT of enterprise customer (Case 39, 40, 41)
     *
     * @param unknown_type $customer_id
     */
    public static function getVatCustomerByType($customer_id, $customer_type)
    {
        ci()->load->model('invoices/vatcase_m');
        ci()->load->model('customers/customer_m');
        ci()->load->model('addresses/customers_address_m');

        $result = new stdClass();
        $result->rate = 0;
        $result->vat_case_id = 0;
        $result->vat_case = 0;

        // Get customer information
        $customer = ci()->customer_m->get_by_many(array(
            "customer_id" => $customer_id
        ));

        // Check exist customer
        if (empty($customer)) {
            return $result;
        }

        // Get customer address
        $customers_address = ci()->customers_address_m->get_by_many(array(
            "customer_id" => $customer_id
        ));
        if (empty($customers_address)) {
            return $result;
        }

        // Get vat from vat_table
        $vat_row = ci()->vatcase_m->get_by_many(
            array(
                "product_type" => APConstants::VAT_PRODUCT_LOCAL_SERVICE,
                "customer_type" => $customer_type,
                "baseon_country_id" => $customers_address->invoicing_country
            ));
        if (!empty($vat_row)) {
            $result->rate = $vat_row->rate;
            $result->vat_case_id = $vat_row->vat_id;
            $result->vat_case = $vat_row->vat_case_id;
            return $result;
        }

        // Get vat from vat_table
        $vat_row = ci()->vatcase_m->get_by_many(
            array(
                "product_type" => APConstants::VAT_PRODUCT_LOCAL_SERVICE,
                "customer_type" => $customer_type,
                "baseon_country_id" => 0
            ));
        if (!empty($vat_row)) {
            $result->rate = $vat_row->rate;
            $result->vat_case_id = $vat_row->vat_id;
            $result->vat_case = $vat_row->vat_case_id;
            return $result;
        }

        return $result;
    }

    /**
     * Check customer is private type or enterprise
     */
    public static function isPrivateCustomer($customer_id)
    {
        return !APUtils::isEnterpriseCustomer($customer_id);
    }

    /**
     * Check customer is private type or enterprise
     */
    public static function isEnterpriseCustomer($customer_id)
    {
        ci()->load->model('invoices/vatcase_m');
        ci()->load->model('customers/customer_m');
        ci()->load->model('addresses/customers_address_m');
        // Get customer information
        $customer = ci()->customer_m->get_by_many(array(
            "customer_id" => $customer_id
        ));

        // Check exist customer
        if (empty($customer)) {
            return false;
        }

        // Get customer address
        $customers_address = ci()->customers_address_m->get_by_many(array(
            "customer_id" => $customer_id
        ));
        if (empty($customers_address)) {
            return false;
        }

        // if from EU country
        if ($customers_address->eu_member_flag == '1') {
            if (APUtils::isValidEUVatNumber($customer_id)) {
                return true; // enterprise
            } else {
                return false; // private
            }
        } // from Germany
        else if ($customers_address->invoicing_country == APConstants::GERMANY_COUNTRY_ID) {
            if (APUtils::isValidEUVatNumber($customer_id)) {
                return true; // enterprise
            } else {
                return false; // private
            }
        } // from outsize EU
        else {
            if (empty($customers_address->invoicing_company)) {
                return false; // private
            } else {
                return true; // enterprise
            }
        }

        return false;
    }

    /**
     * Get vat rate of shipping case.
     *
     * @param unknown $envelope_id
     * @param string $shipping_type
     *            default is direct shipping.
     * @return number
     */
    public static function getVatRateOfShipping($envelope_id, $shipping_type = "1")
    {
        ci()->load->model('invoices/vatcase_m');
        ci()->load->model('addresses/customers_address_m');
        ci()->load->model('scans/envelope_shipping_m');

        $result = new stdClass();
        $result->rate = 0;
        $result->vat_case_id = 0;
        $result->vat_case = 0;

        $envelope_shipping_obj = ci()->envelope_shipping_m->get_by_many(
            array(
                "envelope_id" => $envelope_id
            ));
        if (empty($envelope_shipping_obj)) {
            return $result;
        }
        $customer_id = $envelope_shipping_obj->customer_id;
        $customer_address_obj = ci()->customers_address_m->get_by_many(
            array(
                "customer_id" => $customer_id
            ));
        if (empty($customer_address_obj)) {
            return $result;
        }

        // Get source location & target location
        $source_location = $customer_address_obj->invoicing_country;
        $target_location = $envelope_shipping_obj->shipping_country;
        $customer_type = APConstants::CUSTOMER_TYPE_PRIVATE;

        // Get shipping case
        $shipping_case = 0;

        if (APUtils::isPrivateCustomer($customer_id)) {
            $shipping_case = 1;
            $customer_type = APConstants::CUSTOMER_TYPE_PRIVATE;
        } else if (APUtils::isEnterpriseCustomer($customer_id)) {
            $customer_type = APConstants::CUSTOMER_TYPE_ENTERPRISE;

            // re-calculate shipping case when customer is enterprise
            // Case 1: Shipping from DE to DE
            if (APConstants::GERMANY_COUNTRY_ID == $source_location && APConstants::GERMANY_COUNTRY_ID == $target_location) {
                $shipping_case = 1;
            } // Case 2: EU to EU
            else if (APUtils::isEUCountry($source_location) && APUtils::isEUCountry($target_location)) {
                $shipping_case = 2;
            } // Case 3: Other to other
            else if (!APUtils::isEUCountry($source_location) && !APUtils::isEUCountry($target_location)) {
                $shipping_case = 3;
            } // Case 4: From EU to DE
            else if (APUtils::isEUCountry($source_location) && APConstants::GERMANY_COUNTRY_ID == $target_location) {
                $shipping_case = 4;
            } // Case 5: From DE to EU
            else if (APConstants::GERMANY_COUNTRY_ID == $source_location && APUtils::isEUCountry($target_location)) {
                $shipping_case = 5;
            } // Case 8: From DE to Other
            else if (APConstants::GERMANY_COUNTRY_ID == $source_location && !APUtils::isEUCountry($target_location)) {
                $shipping_case = 8;
            } // Case 9: From Other to DE
            else if (!APUtils::isEUCountry($source_location) && APConstants::GERMANY_COUNTRY_ID == $target_location) {
                $shipping_case = 9;
            } // Case 6: From EU to Other
            else if (APUtils::isEUCountry($source_location) && !APUtils::isEUCountry($target_location)) {
                $shipping_case = 6;
            } // Case 7: Other to EU
            else if (!APUtils::isEUCountry($source_location) && APUtils::isEUCountry($target_location)) {
                $shipping_case = 7;
            }
        }

        // Get vat from vat_table
        $vat_row = ci()->vatcase_m->get_by_many(
            array(
                "product_type" => APConstants::VAT_PRODUCT_SHIPPING,
                "customer_type" => $customer_type,
                "baseon_country_id" => $shipping_case
            ));
        if (!empty($vat_row)) {
            $result->rate = $vat_row->rate;
            $result->vat_case_id = $vat_row->vat_id;
            $result->vat_case = $vat_row->vat_case_id;
        }

        return $result;
    }

    /**
     * Get vat rate of shipping case.
     *
     * @param unknown $envelope_id
     * @param string $shipping_type
     *            default is direct shipping.
     * @return number
     */
    public static function getVatRateOfShippingByCustomer($customer_id, $target_location = "")
    {
        ci()->load->model('invoices/vatcase_m');
        ci()->load->model('addresses/customers_address_m');
        ci()->load->model('scans/envelope_shipping_m');

        $result = new stdClass();
        $result->vat_case_id = 0;
        $result->rate = 0;
        $result->vat_case = 0;

        $customer_address_obj = ci()->customers_address_m->get_by_many(
            array(
                "customer_id" => $customer_id
            ));
        if (empty($customer_address_obj)) {
            return $result;
        }

        // Get source location & target location
        $source_location = $customer_address_obj->invoicing_country;
        $customer_type = APConstants::CUSTOMER_TYPE_PRIVATE;

        // Get shipping case
        $shipping_case = 0;

        if (APUtils::isPrivateCustomer($customer_id)) {
            $shipping_case = 1;
            $customer_type = APConstants::CUSTOMER_TYPE_PRIVATE;
        } else if (APUtils::isEnterpriseCustomer($customer_id)) {
            $customer_type = APConstants::CUSTOMER_TYPE_ENTERPRISE;

            // re-calculate shipping case when customer is enterprise.
            // Case 1: Shipping from DE to DE
            if (APConstants::GERMANY_COUNTRY_ID == $source_location && APConstants::GERMANY_COUNTRY_ID == $target_location) {
                $shipping_case = 1;
            } // Case 2: EU to EU
            else if (APUtils::isEUCountry($source_location) && APUtils::isEUCountry($target_location)) {
                $shipping_case = 2;
            } // Case 3: Other to other
            else if (!APUtils::isEUCountry($source_location) && !APUtils::isEUCountry($target_location)) {
                $shipping_case = 3;
            } // Case 4: From EU to DE
            else if (APUtils::isEUCountry($source_location) && APConstants::GERMANY_COUNTRY_ID == $target_location) {
                $shipping_case = 4;
            } // Case 5: From DE to EU
            else if (APConstants::GERMANY_COUNTRY_ID == $source_location && APUtils::isEUCountry($target_location)) {
                $shipping_case = 5;
            } // Case 8: From DE to Other
            else if (APConstants::GERMANY_COUNTRY_ID == $source_location && !APUtils::isEUCountry($target_location)) {
                $shipping_case = 8;
            } // Case 9: From Other to DE
            else if (!APUtils::isEUCountry($source_location) && APConstants::GERMANY_COUNTRY_ID == $target_location) {
                $shipping_case = 9;
            } // Case 6: From EU to Other
            else if (APUtils::isEUCountry($source_location) && !APUtils::isEUCountry($target_location)) {
                $shipping_case = 6;
            } // Case 7: Other to EU
            else if (!APUtils::isEUCountry($source_location) && APUtils::isEUCountry($target_location)) {
                $shipping_case = 7;
            }
        }

        // Get vat from vat_table
        $vat_row = ci()->vatcase_m->get_by_many(
            array(
                "product_type" => APConstants::VAT_PRODUCT_SHIPPING,
                "customer_type" => $customer_type,
                "baseon_country_id" => $shipping_case
            ));
        if (!empty($vat_row)) {
            $result->vat_case_id = $vat_row->vat_id;
            $result->rate = $vat_row->rate;
            $result->vat_case = $vat_row->vat_case_id;
        }
        return $result;
    }

    /**
     * Gets vat rate of digital good.
     *
     * @param unknown $customer_id
     * @return number
     */
    public static function getVatRateOfDigitalGoodBy($customer_id)
    {
        if (APUtils::isPrivateCustomer($customer_id)) {
            return APUtils::getVatCustomerOfDigitalGoodByType($customer_id, APConstants::CUSTOMER_TYPE_PRIVATE);
        } else if (APUtils::isEnterpriseCustomer($customer_id)) {
            return APUtils::getVatCustomerOfDigitalGoodByType($customer_id, APConstants::CUSTOMER_TYPE_ENTERPRISE);
        }
    }

    /**
     * Get VAT of enterprise customer (Case 39, 40, 41)
     *
     * @param unknown_type $customer_id
     */
    public static function getVatCustomerOfDigitalGoodByType($customer_id, $customer_type)
    {
        ci()->load->model('invoices/vatcase_m');
        ci()->load->model('customers/customer_m');
        ci()->load->model('addresses/customers_address_m');

        $result = new stdClass();
        $result->vat_case_id = 0;
        $result->rate = 0;
        $result->vat_case = 0;

        // Get customer information
        $customer = ci()->customer_m->get_by_many(array(
            "customer_id" => $customer_id
        ));

        // Check exist customer
        if (empty($customer)) {
            return $result;
        }

        // Get customer address
        $customers_address = ci()->customers_address_m->get_by_many(array(
            "customer_id" => $customer_id
        ));
        if (empty($customers_address)) {
            return $result;
        }

        // Get vat from vat_table
        $vat_row = ci()->vatcase_m->get_by_many(
            array(
                "product_type" => APConstants::VAT_PRODUCT_DIGITAL_GOOD,
                "customer_type" => $customer_type,
                "baseon_country_id" => $customers_address->invoicing_country
            ));
        if (!empty($vat_row)) {
            $result->vat_case_id = $vat_row->vat_id;
            $result->rate = $vat_row->rate;
            $result->vat_case = $vat_row->vat_case_id;
            return $result;
        }

        // Get vat from vat_table
        $vat_row = ci()->vatcase_m->get_by_many(
            array(
                "product_type" => APConstants::VAT_PRODUCT_DIGITAL_GOOD,
                "customer_type" => $customer_type,
                "baseon_country_id" => 0
            ));
        if (!empty($vat_row)) {
            $result->vat_case_id = $vat_row->vat_id;
            $result->rate = $vat_row->rate;
            $result->vat_case = $vat_row->vat_case_id;
        }

        return $result;
    }

    /**
     * Is EU Country.
     *
     * @param unknown_type $country_id
     */
    public static function isEUCountry($country_id)
    {
        ci()->load->model('settings/countries_m');

        $country = ci()->countries_m->get_by_many(array(
            'id' => $country_id
        ));
        if (empty($country)) {
            return false;
        }

        return $country->eu_member_flag;
    }

    /**
     * Get VAT list by customer.
     */
    public static function getVATListByCustomer()
    {
        $type_data = array();
        $local_service = new stdClass();
        $local_service->id = "1";
        $local_service->label = APConstants::VAT_LOCAL_SERVICE_LABEL;
        array_push($type_data, $local_service);
        unset($local_service);

        $local_service = new stdClass();
        $local_service->id = "2";
        $local_service->label = APConstants::VAT_DIGITAL_GOOD_LABEL;
        array_push($type_data, $local_service);
        unset($local_service);

        $local_service = new stdClass();
        $local_service->id = "3";
        $local_service->label = APConstants::VAT_SHIPPING_LABEL;
        array_push($type_data, $local_service);

        return $type_data;
    }

    /**
     * Get credit card by transaction id
     */
    public static function getPaypalAccount($customer_id)
    {
        ci()->load->model('payment/payment_m');

        $array_condition = array();
        $array_condition['customer_id'] = $customer_id;
        $array_condition['primary_card'] = APConstants::ON_FLAG;
        $array_condition['account_type'] = APConstants::PAYMENT_PAYPAL_ACCOUNT;

        $customer_payments = ci()->payment_m->get_many_by_many($array_condition);
        if (empty($customer_payments) || count($customer_payments) == 0) {
            log_message(APConstants::LOG_DEBUG,
                'Customer payment information of customer id: ' . $customer_id . ' does not exist');
            return null;
        }
        $customer_payment = $customer_payments[0];

        return $customer_payment;
    }

    /**
     * calculate vat rate from vat string submited.
     *
     * @param unknown $vat_param
     */
    public static function calcVatFromParamSubmit($customer_id, $vat_param)
    {
        if (isset($vat_param)) {
            if ($vat_param == APConstants::VAT_LOCAL_SERVICE_LABEL) {
                return APUtils::getVatRateOfCustomer($customer_id);
            } else if ($vat_param == APConstants::VAT_SHIPPING_LABEL) {
                return APUtils::getVatRateOfShippingByCustomer($customer_id);
            } else if ($vat_param == APConstants::VAT_DIGITAL_GOOD_LABEL) {
                return APUtils::getVatRateOfDigitalGoodBy($customer_id);
            }
        }

        return 0;
    }

    /**
     * Generate invoice code by invoice id.
     *
     * @param unknown $invoice_id
     */
    public static function generateInvoiceCodeById($invoice_id, $is_credit_note = false)
    {
        if (!$invoice_id) {
            ci()->load->model('common/auto_sequence_m');

            $invoice_id = ci()->auto_sequence_m->get_next_id('invoice_summary');
        }

        // update invoice code.
        if($is_credit_note){
            $invoice_code = "CN";
        }else{
            $invoice_code = "IN";
        }

        $invoice_code .= APUtils::getCurrentYearShort() . APUtils::getCurrentMonth();
        $invoice_code .= sprintf('%1$06d', $invoice_id + 321500);

        return $invoice_code;
    }

    /**
     * Check customer has: standard payment is credit card.
     *
     * @param unknown $customer_id
     */
    public static function isCreditCardPaymentStandard($customer_id)
    {
        ci()->load->model('payment/payment_m');

        $paymentMethod = ci()->payment_m->get_by_many(
            array(
                "customer_id" => $customer_id,
                "account_type" => APConstants::PAYMENT_CREDIT_CARD_ACCOUNT
            ));

        if ($paymentMethod) {
            return true;
        }

        return false;
    }

    /**
     * check customer has: standard payment is paypal.
     *
     * @param unknown $customer_id
     * @return boolean
     */
    public static function isPaypalPaymentStandard($customer_id)
    {
        ci()->load->model('payment/payment_m');

        $paymentMethod = ci()->payment_m->get_by_many(
            array(
                "customer_id" => $customer_id,
                "account_type" => APConstants::PAYMENT_PAYPAL_ACCOUNT
            ));

        if ($paymentMethod) {
            return true;
        }

        return false;
    }

    /**
     * check invoice payment method.
     *
     * @param unknown $customer_id
     */
    public static function isInvoicePaymentMethod($customer_id)
    {
        ci()->load->model('customers/customer_m');

        $invoice = ci()->customer_m->get_by_many(
            array(
                "invoice_type" => "2", // manual
                "customer_id" => $customer_id
            ));

        if ($invoice && !empty($invoice->invoice_code)) {
            return true;
        }

        return false;
    }

    /**
     * Gets country of vat case.
     *
     * @param unknown $customer_id
     * @param unknown $vat_case
     */
    public static function getCountryByVat($vat_case)
    {
        $result = new stdClass();
        $result->vat_case = "";
        $result->country = "";
        $result->product_type = "";
        $result->invoice_notes = "";

        if ($vat_case == 0 || $vat_case == "") {
            return $result;
        }

        ci()->load->model('invoices/vatcase_m');

        // Get customer address
        $vat_case = ci()->vatcase_m->get_by_many(array(
            "vat_id" => $vat_case
        ));
        if ($vat_case) {
            $result->vat_case = $vat_case->vat_case_id;
            $result->invoice_notes = $vat_case->notes;
            $result->country = $vat_case->text;
            if ($vat_case->product_type == APConstants::VAT_PRODUCT_LOCAL_SERVICE) {
                $result->product_type = "LS";
            } else if ($vat_case->product_type == APConstants::VAT_PRODUCT_DIGITAL_GOOD) {
                $result->product_type = "DG";
            } else if ($vat_case->product_type == APConstants::VAT_PRODUCT_SHIPPING) {
                $result->product_type = "S";
            }
        }

        return $result;
    }

    /**
     * Chekc valid EU vat nubmer.
     *
     * @param unknown $customer
     */
    public static function isValidEUVatNumber($customer_id)
    {
        ci()->load->model('customers/customer_m');
        ci()->load->model('addresses/customers_address_m');

        $customer_address = ci()->customers_address_m->get_by_many(array(
            "customer_id" => $customer_id
        ));
        $customer = ci()->customer_m->get_by_many(array(
            "customer_id" => $customer_id
        ));

        if ($customer && $customer_address) {
            if (!empty($customer->vat_number) && !empty($customer_address->invoicing_company)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Calculate total invoice
     *
     * @param unknown_type $customer_id
     */
    public static function updateTotalInvoiceOfInvoiceSummary($customer_id)
    {
        ci()->load->model('invoices/invoice_summary_m');
        $list_invoice_summary = ci()->invoice_summary_m->get_many_by_many(
            array(
                'customer_id' => $customer_id,
                "(invoice_type = 0 OR invoice_type IS NULL OR invoice_type =1)" => null,
                "substr( invoice_month, 1, 6 ) = '" . APUtils::getCurrentYear() . APUtils::getCurrentMonth() . "'" => null
            ));
        foreach ($list_invoice_summary as $invoice_summary) {
            $total_invoice = APUtils::calculateTotalInvoice($invoice_summary);
            // Total invoice
            ci()->invoice_summary_m->update_by_many(
                array(
                    'customer_id' => $customer_id,
                    'id' => $invoice_summary->id
                ),
                array(
                    'total_invoice' => $total_invoice,
                    "update_flag" => 0
                ));
        }
    }

    /**
     * Calculate total invoice
     *
     * @param unknown_type $customer_id
     */
    public static function updateTotalInvoiceOfInvoiceSummaryTargetMonth($customer_id, $target_year, $target_month)
    {
        ci()->load->model('invoices/invoice_summary_m');
        $list_invoice_summary = ci()->invoice_summary_m->get_many_by_many(
            array(
                'customer_id' => $customer_id,
                "(invoice_type = 0 OR invoice_type IS NULL OR invoice_type =1)" => null,
                "substr( invoice_month, 1, 6 ) = '" . $target_year . $target_month . "'" => null
            ));
        foreach ($list_invoice_summary as $invoice_summary) {
            $total_invoice = APUtils::calculateTotalInvoice($invoice_summary);
            // Total invoice
            ci()->invoice_summary_m->update_by_many(
                array(
                    'customer_id' => $customer_id,
                    'id' => $invoice_summary->id
                ),
                array(
                    'total_invoice' => $total_invoice,
                    "update_flag" => 0
                ));
        }
    }

    /**
     * Calculate total invoice
     *
     * @param unknown_type $customer_id
     */
    public static function updateTotalInvoiceOfInvoiceSummaryByLocation($customer_id)
    {
        ci()->load->model('invoices/invoice_summary_by_location_m');
        $list_invoice_summary = ci()->invoice_summary_by_location_m->get_many_by_many(
            array(
                'customer_id' => $customer_id,
                "(invoice_type = 0 OR invoice_type IS NULL OR invoice_type =1)" => null,
                "substr( invoice_month, 1, 6 ) = '" . APUtils::getCurrentYear() . APUtils::getCurrentMonth() . "'" => null
            ));
        foreach ($list_invoice_summary as $invoice_summary) {
            $total_invoice = APUtils::calculateTotalInvoice($invoice_summary);
            // Total invoice
            ci()->invoice_summary_by_location_m->update_by_many(
                array(
                    'customer_id' => $customer_id,
                    'id' => $invoice_summary->id
                ), array(
                'total_invoice' => $total_invoice
            ));
        }
    }

    /**
     * Calculate total invoice
     *
     * @param unknown_type $customer_id
     */
    public static function updateTotalInvoiceUserEnterprise($customer_id)
    {
        ci()->load->model('invoices/invoice_summary_by_user_m');
        $list_invoice_summary = ci()->invoice_summary_by_user_m->get_many_by_many( array(
            'customer_id' => $customer_id,
            "invoice_type" => "1",
            "substr( invoice_month, 1, 6 ) = '" . APUtils::getCurrentYear() . APUtils::getCurrentMonth() . "'" => null
        ));

        if(!empty($list_invoice_summary)){
            foreach ($list_invoice_summary as $invoice_summary) {
                $total_invoice = APUtils::calculateTotalInvoice($invoice_summary, true);
                // Total invoice
                ci()->invoice_summary_by_user_m->update_by_many(
                    array(
                        'customer_id' => $customer_id,
                        'id' => $invoice_summary->id
                    ), array(
                        'total_invoice' => $total_invoice
                ));
            }
        }
    }

    /**
     * Calculate storage fee per envelope.
     *
     * @param unknown_type $free_storage_duration :
     *            The number of week to store free
     * @param unknown_type $base_line_days :
     *            The last number of days to calculate and save to
     *            invoice_summary
     * @param unknown_type $charge_date :
     *            The charge data (current date)
     * @param unknown_type $incomming_date :
     *            The incomming date
     * @param unknown_type $send_out_on :
     *            The sent out date
     * @param unknown_type $trashed_on :
     *            The trash date
     */
    public static function calculateStorageDayToChargePerEnvelope($free_storage_duration, $base_line_days, $input_charge_date, $input_incomming_date,
                                                                  $input_send_out_on, $input_trashed_on)
    {
        $ONE_DAY = 60 * 60 * 24;
        $charge_date = APUtils::remove_time($input_charge_date);
        $incomming_date = APUtils::remove_time($input_incomming_date);
        $send_out_on = APUtils::remove_time($input_send_out_on);
        $trashed_on = APUtils::remove_time($input_trashed_on);

        // Get end of fee day
        // #904 Change Free Storage period from weeks to days
        $end_of_free_date = $incomming_date + $free_storage_duration * $ONE_DAY;

        // Calculate free day in current month
        $end_of_expired = $send_out_on;
        if ($trashed_on > 0 && ($trashed_on < $send_out_on || $send_out_on == 0)) {
            $end_of_expired = $trashed_on;
        }

        // If already sent out or trash before
        if ($end_of_expired > 0 && $end_of_expired <= $end_of_free_date) {
            return 0;
        }

        $baseline_storage_fee_date_setting = Settings::get(APConstants::STORAGE_FEE_BASELINE_DATE);
        $baseline_storage_fee_date = 0;
        if (!empty($baseline_storage_fee_date_setting)) {
            $baseline_storage_fee_date = APUtils::convert_date_to_timestamp($baseline_storage_fee_date_setting);
        }
        if ($end_of_expired > 0 && $end_of_expired <= $baseline_storage_fee_date) {
            return 0;
        }

        // Calculate day to charge
        $days_to_charge = 0;
        $end_to_charge_date = $charge_date;
        // Only calculate charge if this envelope is over
        if ($charge_date > $end_of_expired && $end_of_expired > 0) {
            $end_to_charge_date = $end_of_expired;
        }
        // echo "Free Duration:".$free_storage_duration."End to
        // charge:".APUtils::convert_timestamp_to_date($end_to_charge_date,
        // APConstants::DATEFORMAT_OUTPUT_PDF) .'==> End to
        // fee:'.APUtils::convert_timestamp_to_date($end_of_free_date,
        // APConstants::DATEFORMAT_OUTPUT_PDF).'<br/>';
        if ($end_to_charge_date > $end_of_free_date) {
            $days_to_charge = $end_to_charge_date - $end_of_free_date;
        }

        // Convert timestamp to day
        if ($days_to_charge == 0) {
            return 0;
        } else {
            $days_to_charge = $days_to_charge / $ONE_DAY;
            $days_to_charge = round($days_to_charge);
            return ($days_to_charge - $base_line_days);
        }
    }

    /**
     * Check if this customer only have welcome letter
     *
     * @param unknown_type $customer_id
     */
    public static function onlyHasWelcomeLetter($customer_id)
    {
        ci()->load->model('scans/envelope_m');
        $total = ci()->envelope_m->count_by_many(
            array(
                'to_customer_id' => $customer_id,
                "envelope_code LIKE '%000'" => null
            ));
        $total_envelope = ci()->envelope_m->count_by_many(array(
            'to_customer_id' => $customer_id
        ));

        $pre_created_date = now() - 24 * 60 * 60;
        $customer = APContext::getCustomerLoggedIn();
        $sent_incomming_letter = $customer->created_date < $pre_created_date;

        return ($total == 1 && $total_envelope == 1) || ($total == 0 && $total_envelope == 0 && $sent_incomming_letter);
    }

    /**
     * Check if this customer only have welcome letter
     *
     * @param unknown_type $customer_id
     */
    public static function getTotalEnvelope($customer_id)
    {
        ci()->load->model('scans/envelope_m');
        $isPrimaryCustomer = APContext::isPrimaryCustomerUser();
        if (!$isPrimaryCustomer) {
            $total_envelope = ci()->envelope_m->count_by_many(array(
                'to_customer_id' => $customer_id
            ));
            return $total_envelope;
        } else {
            $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
            $total_envelope = ci()->envelope_m->count_by_parent_customer($parent_customer_id);
            return $total_envelope;
        }
    }

    /**
     * Gets primary payment method.
     *
     * @param unknown $customer
     */
    public static function getPrimaryPaymentMethod($customer_id)
    {
        ci()->load->model('payment/payment_m');

        $all_account = ci()->payment_m->get_payment_account($customer_id, 0, 1000);
        if (empty($all_account)) {
            return 'Invoice';
        }

        $list_card = array(
            "A",
            "V",
            "M",
            "J"
        );
        foreach ($all_account as $account) {
            if ($account->primary_card == "1") {
                if (in_array($account->account_type, $list_card)) {
                    return "Credit Card";
                } else if ($account->account_type == APConstants::PAYMENT_PAYPAL_ACCOUNT) {
                    return "Paypal";
                }
            }
        }

        return "Invoice";
    }

    /**
     * Get reverse_charge by id.
     *
     * @param unknown_type $vat_id
     */
    public static function getReverseCharge($vat_id)
    {
        ci()->load->model('invoices/vatcase_m');
        $vat = ci()->vatcase_m->get_by_many(array(
            'vat_id' => $vat_id
        ));
        if ($vat) {
            return $vat->reverse_charge;
        }
        return '';
    }

    /**
     * Check the current envelope id pending
     */
    public static function isPendingForDeclareCustoms($envelope_id, $pending_envelope_customs, $shipping_type)
    {
        if (empty($pending_envelope_customs) || count($pending_envelope_customs) == 0) {
            return false;
        }
        foreach ($pending_envelope_customs as $item) {
            if ($item->envelope_id == $envelope_id && $shipping_type == $item->shipping_type) {
                return true;
            }
        }
        return false;
    }

    /**
     * Common function to process delete postbox.
     * @param unknown $postbox_id
     * @param unknown $customer_id
     */
    public static function deletePostbox($postbox_id, $customer_id, $action, $from_admin = false)
    {
        ci()->load->library('scans/scans_api');

        // Load model
        ci()->load->model('scans/envelope_m');
        ci()->load->model('scans/envelope_file_m');
        ci()->load->model('mailbox/postbox_m');
        ci()->load->model('customers/customer_m');
        ci()->load->model('cases/cases_m');
        ci()->load->model('scans/envelope_storage_month_m');

        //Cancel all activity and trash item. Add log cancel activity and trash item for envelopes
        EnvelopeUtils::cancelActivityAndTrashItemBy($customer_id, $postbox_id);

        // delete all file on S3
        $envelopes = ci()->envelope_m->get_many_by_many(
            array(
                "postbox_id" => $postbox_id,
                "to_customer_id" => $customer_id
            ));
        foreach ($envelopes as $envelope) {
            $files = ci()->envelope_file_m->get_many_by_many(
                array(
                    "envelope_id" => $envelope->id
                ));

            // Delete file content in amazone
            if ($files) {
                ci()->load->library('S3');
                $default_bucket_name = ci()->config->item('default_bucket');
                foreach ($files as $preview_file) {
                    $res = S3::deleteObject($default_bucket_name, $preview_file->amazon_relate_path);
                }
            }
        }

        ci()->cases_m->update_by_many(
            array(
                "postbox_id" => $postbox_id,
                "customer_id" => $customer_id
            ),
            array(
                "deleted_flag" => 1,
                "last_modified_date" => date("Y-m-d H:i:s")
            ));

        if ($from_admin) {
            ci()->postbox_m->update_by_many(
                array(
                    "postbox_id" => $postbox_id,
                    "customer_id" => $customer_id,
                ),
                array(
                    "deleted" => APConstants::ON_FLAG,
                    "completed_delete_flag" => APConstants::ON_FLAG,
                    "deleted_date" => time(),
                    "updated_date" => time()
                ));
        } else {
            // #596
            $total = ci()->postbox_m->count_by_many(
                array(
                    'customer_id' => $customer_id,
                    "deleted <> 1 " => null
                ));

            if ($total == 1) {
                // delete postbox
                ci()->postbox_m->update_by_many(
                    array(
                        "postbox_id" => $postbox_id,
                        "customer_id" => $customer_id
                    ),
                    array(
                        "deleted" => APConstants::ON_FLAG,
                        "completed_delete_flag" => APConstants::ON_FLAG,
                        "is_main_postbox" => APConstants::ON_FLAG,
                        "first_location_flag" => APConstants::ON_FLAG,
                        "deleted_date" => time(),
                        "updated_date" => time()
                    ));
            } else {
                // delete postbox
                ci()->postbox_m->update_by_many(
                    array(
                        "postbox_id" => $postbox_id,
                        "customer_id" => $customer_id
                    ),
                    array(
                        "deleted" => APConstants::ON_FLAG,
                        "completed_delete_flag" => APConstants::ON_FLAG,
                        "is_main_postbox" => APConstants::OFF_FLAG,
                        "first_location_flag" => APConstants::OFF_FLAG,
                        "deleted_date" => time(),
                        "updated_date" => time()
                    ));
            }
        }

        // update main postbox
        $postbox_check = ci()->postbox_m->count_by_many(array(
            "customer_id" => $customer_id,
            "is_main_postbox" => 1,
            'deleted' => 0
        ));
        if(empty($postbox_check)){
            $first_postbox = ci()->postbox_m->get_by_many( array(
                "customer_id" => $customer_id,
                "first_location_flag" => 1,
                'deleted' => 0
            ));

            if ($first_postbox) {
                // Update main postbox
                ci()->postbox_m->update_by_many( array(
                    "postbox_id" => $first_postbox->postbox_id,
                    'customer_id' => $customer_id
                ), array(
                    "is_main_postbox" => APConstants::ON_FLAG
                ));
            }else{
                $postbox = ci()->postbox_m->get_by_many(array(
                    "customer_id" => $customer_id,
                    'deleted' => 0
                ));
                if(!empty($postbox)){
                    ci()->postbox_m->update_by_many( array(
                        "postbox_id" => $postbox->postbox_id,
                        'customer_id' => $customer_id
                    ), array(
                        "is_main_postbox" => APConstants::ON_FLAG,
                        "first_location_flag" => 1,
                    ));
                }
            }
        }

       /*
         * #1180 create postbox history page like check item page
         *  Activity: delete ordered by customer, delete ordered by system, delete
         */
        customers_api::addPostboxHistory($postbox_id, $action, "");
        // CustomerUtils::actionPostboxHistoryActivity($postbox_id, $action, time(), "", APConstants::INSERT_POSTBOX);

    }

    public static function getAccountTypeBy($customer_id)
    {
        ci()->load->model('mailbox/postbox_m');

        $postbox = ci()->postbox_m->get_by_many(
            array(
                "customer_id" => $customer_id,
                "is_main_postbox" => 1,
                "deleted" => APConstants::OFF_FLAG
            ));

        // always postbox required.
        return $postbox->type;
    }

    /**
     * Update open balance to database
     *
     * @param unknown_type $customer_id
     */
    public static function updateOpenBalanceToDB($customer_id)
    {
        ci()->load->model('invoices/customer_openbalance_m');

        // Get open balance due
        $result = APUtils::getAdjustOpenBalanceDue($customer_id);
        $open_balance_due = $result['OpenBalanceDue'];
        $open_balance_this_month = $result['OpenBalanceThisMonth'];

        $customer_open_balance_check = ci()->customer_openbalance_m->get_by_many(
            array(
                'customer_id' => $customer_id
            ));
        if (empty($customer_open_balance_check)) {
            ci()->customer_openbalance_m->insert(
                array(
                    'customer_id' => $customer_id,
                    'open_balance_due' => $open_balance_due,
                    'open_balance_month' => $open_balance_this_month,
                    'last_updated_date' => now()
                ));
        } else {
            ci()->customer_openbalance_m->update_by_many(array(
                'customer_id' => $customer_id
            ),
                array(
                    'open_balance_due' => $open_balance_due,
                    'open_balance_month' => $open_balance_this_month,
                    'last_updated_date' => now()
                ));
        }
        return $result;
    }

    /**
     * get primary location by customer id
     */
    public static function getPrimaryLocationBy($customer_id)
    {
        ci()->load->model('mailbox/postbox_m');

        $primary_location = ci()->postbox_m->getFirstLocationBy($customer_id);

        return $primary_location ? $primary_location->location_available_id : 0;
    }

    /**
     * get primary location by postbox id
     */
    public static function getLocationIdBy($postbox_id)
    {
        ci()->load->model('mailbox/postbox_m');

        $primary_location = ci()->postbox_m->get_by_many(array(
            'postbox_id' => $postbox_id
        ));

        if ($primary_location) {
            return $primary_location->location_available_id;
        }

        return 0;
    }

    /**
     * get country list of postbox by customer
     */
    public static function getCountryCodeListOfPostboxByCustomer($customer_id)
    {
        ci()->load->model('addresses/location_m');

        $list_locations = ci()->location_m->get_all_location_for_verify($customer_id);

        $list_result = array();
        // Get list location
        foreach ($list_locations as $location) {
            $list_result[] = $location->country_id;
        }
        return $list_result;
    }

    /**
     * get location by envelope id
     */
    public static function getLocationIdByEnvelope($envelop_id)
    {
        ci()->load->model('scans/envelope_m');

        $envelope = ci()->envelope_m->get_by_many(array(
            'id' => $envelop_id
        ));

        if ($envelope) {
            return APUtils::getLocationIdBy($envelope->postbox_id);
        }

        return 0;
    }

    /**
     * Insert or update data to summary by location
     *
     * @param unknown_type $data
     */
    public static function insertInvoiceSummaryByLocation($data)
    {
        ci()->load->model('invoices/invoice_summary_by_location_m');

        // Build array key
        $array_key = array(
            "customer_id" => '',
            "invoice_month" => '',
            "invoice_type" => '',
            "location_id" => ''
        );
        if (isset($data['customer_id'])) {
            $array_key['customer_id'] = $data['customer_id'];
        }
        if (isset($data['invoice_month'])) {
            $array_key['invoice_month'] = $data['invoice_month'];
        }
        if (isset($data['invoice_type'])) {
            $array_key['invoice_type'] = $data['invoice_type'];
        }
        if (isset($data['location_id'])) {
            $array_key['location_id'] = $data['location_id'];
        }

        // Required location id
        if (empty($array_key['location_id'])) {
            log_audit_message(APConstants::LOG_ERROR, '>>>>>>>>> Location ID is NULL. Ignore this update request:' . json_encode($array_key));
            return;
        }

        unset($data['payment_1st_flag']);
        unset($data['payment_1st_amount']);
        unset($data['payment_2st_flag']);
        unset($data['payment_2st_amount']);
        unset($data['send_invoice_flag']);
        unset($data['send_invoice_date']);
        unset($data['invoice_flag']);
        unset($data['invoice_file_path']);
        unset($data['payment_type']);
        unset($data['id']);
        //unset($data['payment_transaction_id']);
        //unset($data['private_postboxes_amount']);
        //unset($data['business_postboxes_amount']);
        //unset($data["private_postboxes_quantity"]);
        //unset($data["private_postboxes_netprice"]);
        //unset($data["business_postboxes_quantity"]);
        //unset($data["business_postboxes_netprice"]);
        //unset($data["total_invoice"]);

        // Check existing invoice
        $check_invoice = ci()->invoice_summary_by_location_m->get_by_many($array_key);

        // If exist we will update
        if ($check_invoice) {
            ci()->invoice_summary_by_location_m->update_by_many($array_key, $data);
        } else {
            ci()->invoice_summary_by_location_m->insert($data);
        }
    }

    /**
     * Check email exist in black list or not
     *
     * @param unknown_type $email
     */
    public static function existBlackListEmail($email)
    {
        ci()->load->model('customers/customer_blacklist_m');
        $customer_black_list = ci()->customer_blacklist_m->get_by_many(array(
            "email" => $email
        ));
        if (!empty($customer_black_list)) {
            return true;
        }

        // Check match email doamin
        $email_domain = APUtils::getDomainFromEmail($email);
        $customer_black_list = ci()->customer_blacklist_m->get_by_many(array(
            "email" => $email_domain
        ));
        if (!empty($customer_black_list)) {
            return true;
        }

        return false;
    }

    /**
     * Get domain name of email
     *
     * @param unknown_type $email
     * @return string
     */
    public static function getDomainFromEmail($email)
    {
        // Get the data after the @ sign
        $domain = substr(strrchr($email, "@"), 1);

        return $domain;
    }

    /**
     * send email invoices monthly report of customer.
     *
     * @param unknown $customer
     * @param unknown $file_export
     * @param $invoice_type (1:Invoice
     *            | 2: Credit Note)
     */
    public static function send_email_invoices_monthly_report($customer, $file_export, $invoice_type, $invoice_code)
    {
        // Send email confirm for user
        if ($invoice_type == '1') {
            $email_template_code = APConstants::email_invoices_report_by_monthly;
        } else if (($invoice_type == '2')) {
            $email_template_code = APConstants::email_invoices_report_for_creditnote;
        }

        $data = array(
            "slug" => $email_template_code,
            "to_email" => $customer->email,
            // Replace content
            "full_name" => $customer->user_name,
            'invoice_id' => $invoice_code,
            'attachments' => array(
                'file' => $file_export
            )
        );

        // Send email
        MailUtils::sendEmailByTemplate($data);
        return true;
    }

    /**
     * Get total page number of specify customer and month
     *
     * @param unknown_type $customer_id
     * @param unknown_type $year_month
     * @param unknown_type $type
     */
    public static function get_total_page_number($customer_id, $year_month, $type = '')
    {
        ci()->load->model('scan/envelope_file_m');
        return ci()->envelope_file_m->get_total_page_number($customer_id, $year_month, $type);
    }

    /**
     * only update account type.
     *
     * @param unknown $customer_id
     */
    public static function updateOnlyAccountType($customer_id)
    {
        // we dont need update account type following postbox type.
        return true;
        // Load model
        ci()->load->model('customers/customer_m');
        ci()->load->model('mailbox/postbox_m');

        // Get customer information
        $customer = ci()->customer_m->get($customer_id);
        $postbox = ci()->postbox_m->get_max_postboxtype($customer_id);
        $postbox_type = $postbox ? ($postbox->MaxPostboxType ? $postbox->MaxPostboxType : APConstants::FREE_TYPE) : APConstants::FREE_TYPE;

        // Get max postbox type
        // Update account type of current customer
        ci()->customer_m->update_by_many(array(
            "customer_id" => $customer_id
        ), array(
            "account_type" => $postbox_type
        ));
    }

    /**
     * Check this module enable or not
     */
    public static function IsEnableCaseFunction()
    {
        // Get customer login
        $customer = APContext::getCustomerByID(APContext::getCustomerCodeLoggedIn());
        if (!empty($customer)) {
            return $customer->required_verification_flag == APConstants::ON_FLAG;
        }
        return false;
    }

    /**
     * Get list case need to apply for one customer.
     *
     * @param unknown_type $customer_id
     * @return the list of case should be apply for this customer.
     *         (0: That mean need to apply verification for invoice address
     *         case)
     */
    public static function getCountryIDOfPostbox($postbox_id)
    {
        ci()->load->model('mailbox/postbox_m');
        ci()->load->model('addesses/location_m');

        // Get list postbox
        $postbox = ci()->postbox_m->get_by('postbox_id', $postbox_id);
        if (empty($postbox)) {
            return '';
        }
        // Get location id
        $location_id = $postbox->location_available_id;
        $location = ci()->location_m->get_by('id', $location_id);
        if (empty($location)) {
            return '';
        }
        return $location->country_id;
    }

    /**
     * Get list case need to apply for one customer.
     *
     * @param unknown_type $customer_id
     * @return the list of case should be apply for this customer.
     *         (0: That mean need to apply verification for invoice address
     *         case)
     */
    public static function get_list_case($customer_id, $postbox_id)
    {
        ci()->load->model('cases/cases_verification_settings_m');
        ci()->load->model('addesses/customers_address_m');
        ci()->load->model('mailbox/postbox_m');
        ci()->load->model('settings/countries_m');
        ci()->load->model('addesses/location_m');

        // Get all cases setting
        $list_cases_settings = ci()->cases_verification_settings_m->get_many_by_many(
            array(
                'setting_type' => '2'
            ));

        // Get customer information
        $customer = APContext::getCustomerByID($customer_id);
        if (empty($customer)) {
            return array();
        }
        // Get list postbox
        $postbox = ci()->postbox_m->get_by('postbox_id', $postbox_id);

        // Get location id
        $location_id = $postbox->location_available_id;
        //$location = ci()->location_m->get_by('id', $location_id);
        //$postbox_country = ci()->countries_m->get_by('id', $location->country_id);
        // Condition 1
        //$postbox_country_code = $postbox_country->country_code;

        // Get invoice address of customer
        //$invoice_address_country = ci()->countries_m->get_by('id', $customer_address->invoicing_country);
        // Condition 2
        //$invoice_address_country_risk_class = $invoice_address_country->risk_class;

        // Condition 3
        //$invoice_address_has_company_name_filled = !empty($customer_address->invoicing_company);

        // Condition 4
        $postbox_name_or_company_name_filled = !empty($postbox->name) || !empty($postbox->company);

        // Condition 5
        $postbox_company_name_filled = !empty($postbox->company);

        $postbox_name_filled = !empty($postbox->name);

        $array_result = array();

        // For each condition is settings table
        foreach ($list_cases_settings as $case_setting) {
            $case_country_code = $case_setting->country_code;
            $case_risk_class = $case_setting->risk_class;
            $case_invoice_address_verification = $case_setting->invoice_address_verification == 1;
            $case_private_postbox_verification = $case_setting->private_postbox_verification == 1;
            $case_business_postbox_verification = $case_setting->business_postbox_verification == 1;
            $postbox_name_filled_verification = $case_setting->postbox_name_filled == 1;

            $list_case_number = $case_setting->list_case_number;
            if (empty($list_case_number)) {
                continue;
            }
            $list_case_number_arr = explode(',', $list_case_number);
            // Check match condition
            if ((($postbox_name_or_company_name_filled && $case_private_postbox_verification) || $case_setting->private_postbox_verification == '-1')
                && (($postbox_company_name_filled && $case_business_postbox_verification) || $case_setting->business_postbox_verification == '-1')
                && (($postbox->location_available_id == $case_setting->location_id) || $case_setting->location_id == '-1')
                && (($postbox_name_filled && $postbox_name_filled_verification) || $case_setting->postbox_name_filled == '-1')
            ) {
                // Apply for this case
                foreach ($list_case_number_arr as $case_number) {
                    if (!in_array($case_number, $array_result)) {
                        $array_result[] = $case_number;
                    }
                }
            }
        }

        return $array_result;
    }

    /**
     * Get list case need to apply for one customer.
     *
     * @param unknown_type $customer_id
     * @return the list of case should be apply for this customer.
     *         (0: That mean need to apply verification for invoice address
     *         case)
     */
    public static function get_list_case_invoice_address($customer_id)
    {
        ci()->load->model('cases/cases_verification_settings_m');
        ci()->load->model('addesses/customers_address_m');
        ci()->load->model('mailbox/postbox_m');
        ci()->load->model('settings/countries_m');
        ci()->load->model('addesses/location_m');

        // Get all cases setting
        $list_cases_settings = ci()->cases_verification_settings_m->get_many_by_many(
            array(
                'setting_type' => '1'
            ));

        // Get customer information
        $customer = APContext::getCustomerByID($customer_id);
        $customer_address = ci()->customers_address_m->get_by_many(array(
            'customer_id' => $customer_id
        ));
        if (empty($customer) || empty($customer_address)) {
            return array();
        }

        // Get invoice address of customer
        $invoice_address_country = ci()->countries_m->get_by('id', $customer_address->invoicing_country);
        if(empty($invoice_address_country)){
            return array();
        }

        // Condition 2
        $invoice_address_country_risk_class = $invoice_address_country->risk_class;
        $invoice_address_country_code = $invoice_address_country->country_code;

        // Condition 3
        $invoice_address_has_company_name_filled = !empty($customer_address->invoicing_company);

        $array_result = array();

        // For each condition is settings table
        foreach ($list_cases_settings as $case_setting) {
            $case_country_code = $case_setting->country_code;
            $case_invoice_address_verification = $case_setting->invoice_address_verification == 1;
            $case_risk_class = $case_setting->risk_class;

            $list_case_number = $case_setting->list_case_number;
            if (empty($list_case_number)) {
                continue;
            }
            $list_case_number_arr = explode(',', $list_case_number);

            if (($invoice_address_country_code == $case_country_code || empty($case_country_code)) && (($invoice_address_has_company_name_filled &&
                        $case_invoice_address_verification) || $case_setting->invoice_address_verification == '-1')
                && ($invoice_address_country_risk_class == $case_risk_class || empty($case_risk_class))
            ) {
                // Apply for this case
                foreach ($list_case_number_arr as $case_number) {
                    if (!in_array($case_number, $array_result)) {
                        $array_result[] = $case_number;
                    }
                }
            }
        }

        return $array_result;
    }

    /**
     * we need to calculate a customer lifetime (CLTV) value after 75 days after
     * the
     * account was created (calculation for this value should be the following
     * (net
     * revenue of first 75 days x 25) e.g.
     * if the net revenue after 75 days is a sum of 30 EUR, the CLTV is 750 EUR
     *
     * @param unknown $customer_id
     * @return number
     */
    public static function calculate_customer_lifetime($customer_id)
    {
        return 1;
    }

    /**
     * revert all envelopes total count when customer change postbox type driectly.
     *
     * @param unknown $customer_id
     * @param unknown $year
     * @param unknown $month
     */
    public static function revert_all_envelopes($customer_id, $postbox_id, $year, $month)
    {
        ci()->load->model('scans/envelope_summary_month_m');

        ci()->envelope_summary_month_m->update_by_many(array(
            "customer_id" => $customer_id,
            "postbox_id" => $postbox_id,
            "year" => $year,
            "month" => $month
        ), array(
            "incomming_number" => 0,
            "envelope_scan_number" => 0,
            "document_scan_number" => 0,
            "direct_shipping_number" => 0,
            "collect_shipping_number" => 0
        ));
    }

    /**
     * #1058 add multi dimension capability for admin
     * get date format information in user profiles.
     *
     * @return date_format
     */

    public static function get_date_format_in_user_profiles()
    {
    	$user_profile = APContext::getAdminLoggedIn();
    	$date_format =  !empty($user_profile->date_format) ? $user_profile->date_format : APConstants::DATEFORMAT_DEFAULT;
    	return $date_format;
    }

    /**
     * #1058 add multi dimension capability for admin
     * get decimal separator information in user profiles.
     *
     * @return decimal_separator
     */

    public static function get_decimal_separator_in_user_profiles()
    {
    	return APConstants::DECIMAL_SEPARATOR_COMMA;
//     	$user_profile = APContext::getAdminLoggedIn();
//     	$decimal_separator = (isset($user_profile->decimal_separator) && !empty($user_profile->decimal_separator)) ? $user_profile->decimal_separator : APConstants::DECIMAL_SEPARATOR_COMMA;
//     	return $decimal_separator;
    }

    /**
     * #1058 add multi dimension capability for admin
     * get length unit information in user profiles.
     *
     * @return length_unit
     */

    public static function get_length_unit_in_user_profiles($check= TRUE)
    {
        if($check== FALSE){
            $length_unit = APConstants::LENGTH_UNIT;
        }else{
            $user_profile = APContext::getAdminLoggedIn();
            $length_unit = !empty($user_profile->length_unit) ? $user_profile->length_unit : APConstants::LENGTH_UNIT;
        }

    	return $length_unit;
    }

    /**
     * #1058 add multi dimension capability for admin
     * get weight_unit information in user profiles.
     *
     * @return weight_unit
     */

    public static function get_weight_unit_in_user_profiles($check= TRUE)
    {
        if($check== FALSE){
            $weight_unit = APConstants::WEIGH_UNIT;
        }else{
            $user_profile = APContext::getAdminLoggedIn();
            $weight_unit =  !empty($user_profile->weight_unit) ? $user_profile->weight_unit : APConstants::WEIGH_UNIT;
        }

    	return $weight_unit;
    }

    /**
     * #1058 add multi dimension capability for admin
     * get currency of user profiles.
     *
     * @return currency
     */

    public static function get_currency_user_profiles()
    {
    	ci()->load->model('settings/currencies_m');
    	$user_profile = APContext::getAdminLoggedIn();

    	if($user_profile && $user_profile->currency_id){
    		$currency = ci()->currencies_m->get_by('currency_id',$user_profile->currency_id);

    	}else{
    		$currency = ci()->currencies_m->get_by('currency_short',APConstants::MONEY_SHORT);
    	}

        return $currency;
    }

    /**
     * #1058 add multi dimension capability for admin
     * get currency_short information in user profiles.
     *
     * @return currency_short
     */

    public static function get_currency_short_in_user_profiles()
    {
    	$currency = APUtils::get_currency_user_profiles();
    	$currency_short =  !empty($currency->currency_short) ? $currency->currency_short : APConstants::MONEY_SHORT;
    	return $currency_short;
    }

    /**
     * #1058 add multi dimension capability for admin
     * get currency_sign information in user profiles.
     *
     * @return currency_sign
     */

    public static function get_currency_sign_in_user_profiles()
    {
    	$currency = APUtils::get_currency_user_profiles();
    	$currency_sign = !empty($currency->currency_sign) ? $currency->currency_sign : APConstants::MONEY_UNIT;
    	return $currency_sign;
    }

    /**
     * #1058 add multi dimension capability for admin
     * get currency_rate information in user profiles.
     *
     * @return currency_rate
     */

    public static function get_currency_rate_in_user_profiles()
    {
    	$currency = APUtils::get_currency_user_profiles();
    	$currency_rate= !empty($currency->currency_rate) ? $currency->currency_rate : APConstants::CURRENCY_RATE;
    	return $currency_rate;
    }

    /**
     * #1058 add multi dimension capability for admin
     * Convert number in currency.
     * @param number_currency
     * @return number
     */

    public static function convert_number_in_currency($number_currency, $currency_short, $currency_rate)
    {
    	return $number_currency;
//     	$number = ($currency_short == APConstants::MONEY_SHORT ) ? $number_currency : ($number_currency / $currency_rate);
//     	return $number;
    }


    /**
     * #1058 add multi dimension capability for admin
     * View convert number in currency.
     * @param number_currency
     * @return number
     */

    public static function view_convert_number_in_currency($number_currency, $currency_short, $currency_rate,$decimal_separator)
    {
        if (empty($number_currency) || $number_currency == 0){
            return  APUtils::number_format(0, 2, $decimal_separator);
        }else{
            $tmp_currency = $number_currency * $currency_rate;
            return $number = ($currency_short == APConstants::MONEY_SHORT ) ? APUtils::number_format(round($number_currency,2), 2, $decimal_separator) : APUtils::number_format(round($tmp_currency,2), 2, $decimal_separator);
        }

    }

    /**
     * #1058 add multi dimension capability for admin
     * View convert number in currency with currency short unit .
     * @param number_currency
     * @return number
     */

    public static function view_currency_with_currency_short_unit($number_currency, $currency_short, $currency_rate,$decimal_separator)
    {
    	return  APUtils::view_convert_number_in_currency($number_currency, $currency_short, $currency_rate, $decimal_separator). ' ' . $currency_short;
    }

    /**
     * #1058 add multi dimension capability for admin
     * View convert number in currency with currency sign unit .
     * @param number_currency
     * @return number
     */

    public static function view_currency_with_currency_sign_unit($number_currency, $currency_short, $currency_sign, $currency_rate,$decimal_separator)
    {
    	return  $currency_sign . ' ' . APUtils::view_convert_number_in_currency($number_currency, $currency_short, $currency_rate, $decimal_separator);
    }
    /**
     * #1058 add multi dimension capability for admin
     * Convert number in weight.
     * @param number_weight
     * @return number
     */

    public static function convert_number_in_weight($number_weight, $weight_unit)
    {
    	return $number_weight;
//     	$item = Settings::get( APConstants::POUND_NUMBER_PER_GRAM_CODE );

//     	$number = ($weight_unit == APConstants::WEIGH_UNIT ) ? $number_weight : ($number_weight/$item);
//     	return $number;
    }

    /**
     * #1058 add multi dimension capability for admin
     * Convert number in length.
     * @param number_length
     * @return number
     */

    public static function convert_number_in_length($number_length, $length_unit)
    {
    	return $number_length;
//     	$item = Settings::get( APConstants::INCH_NUMBER_PER_CENTIMET_CODE );

//     	$number = ($length_unit == APConstants::LENGTH_UNIT ) ? $number_length : ($number_length / $item);
//     	return $number;
    }

    /**
     * #1058 add multi dimension capability for admin
     * Convert view number in weight.
     * @param number_weight
     * @return number
     */

    public static function view_convert_number_in_weight($number_weight, $weight_unit , $decimal_separator, $check= TRUE)
    {
    	$item = Settings::get( APConstants::POUND_NUMBER_PER_GRAM_CODE );

    	if($check == FALSE){
    		if(empty($number_weight) || $number_weight == 0)
    			return APUtils::number_format(0, 0, $decimal_separator);
    		else
    			$number = ($weight_unit == APConstants::WEIGH_UNIT ) ? APUtils::number_format($number_weight, 0, $decimal_separator) : APUtils::number_format(($number_weight * $item), 2, $decimal_separator);
    	}else{
    		if(empty($number_weight) || $number_weight == 0)
    			return APUtils::number_format(0, 0, $decimal_separator). ' ' . $weight_unit;
    		else
    			$number = ($weight_unit == APConstants::WEIGH_UNIT ) ? APUtils::number_format($number_weight, 0, $decimal_separator). ' ' . $weight_unit  : APUtils::number_format(($number_weight * $item), 2, $decimal_separator). ' ' . $weight_unit;
    	}
        return $number;
    }

    /**
     * #1058 add multi dimension capability for admin
     * Convert view  number in length.
     * @param number_length
     * @return number
     */

    public static function view_convert_number_in_length($number_length, $length_unit, $decimal_separator, $check= TRUE)
    {
    	$item = Settings::get( APConstants::INCH_NUMBER_PER_CENTIMET_CODE );

    	if($check == FALSE){
    		if(empty($number_length) || $number_length == 0)
    			return APUtils::number_format(0, 0, $decimal_separator);
    		else
    			$number = ($length_unit == APConstants::LENGTH_UNIT ) ? APUtils::number_format($number_length, 0, $decimal_separator) : APUtils::number_format(($number_length * $item), 2, $decimal_separator);
    	}else {
    		if(empty($number_length) || $number_length == 0)
    			return APUtils::number_format(0, 0, $decimal_separator). ' ' . $length_unit;
    		else
    			$number = ($length_unit == APConstants::LENGTH_UNIT ) ? APUtils::number_format($number_length, 0, $decimal_separator). ' ' . $length_unit : APUtils::number_format(($number_length * $item), 2, $decimal_separator). ' ' . $length_unit ;
    	}

    	return $number;
    }

    /**
     *  #1058 add multi dimension capability for admin
     * Convert date format from yyyyMMdd (yyyyddMM) => dd/MM/yyyy (MM/dd/yyyy)
     *
     * @param unknown_type $dateFormatFrom
     */
    public static function convertDateFormatFrom($dateFormatFrom)
    {
    	if (empty($dateFormatFrom)) {
    		return '';
    	}

    	$date_format = APUtils::get_date_format_in_user_profiles();
    	$year = '';
    	$month = '';
    	$date = '';
    	if ($date_format == APConstants::DATEFORMAT_DEFAULT){
    		if (strlen($dateFormatFrom) >= 4) {
    			$year = substr($dateFormatFrom, 0, 4);
    		}

    		if (strlen($dateFormatFrom) >= 6) {
    			$date = substr($dateFormatFrom, 4, 2);
    		}

    		if (strlen($dateFormatFrom) >= 8) {
    			$month = substr($dateFormatFrom, 6, 2);
    		}
    		return $month . '/' . $date . '/' . $year;
    	}else{
    		if (strlen($dateFormatFrom) >= 4) {
    			$year = substr($dateFormatFrom, 0, 4);
    		}

    		if (strlen($dateFormatFrom) >= 6) {
    			$month = substr($dateFormatFrom, 4, 2);
    		}

    		if (strlen($dateFormatFrom) >= 8) {
    			$date = substr($dateFormatFrom, 6, 2);
    		}

    		return $date . '/' . $month . '/' . $year;
    	}
    }

    /**
     *  #1058 add multi dimension capability for admin
     * Convert date format from dd/MM/yyyy(MM/dd/yyyy) => yyyyMMdd
     *
     * @param unknown_type $dateFormatFrom
     */
    public static function convertDateFormatFrom02($dateFormatFrom)
    {
    	if (empty($dateFormatFrom)) {
    		return '';
    	}

    	$date = '';
    	$month = '';
    	$year = '';
    	$date_format = APConstants::DATEFORMAT_DEFAULT;//APUtils::get_date_format_in_user_profiles();
    	if ($date_format == APConstants::DATEFORMAT_DEFAULT){
    		if (strlen($dateFormatFrom) >= 2) {
    			$date = substr($dateFormatFrom, 0, 2);
    		}

    		if (strlen($dateFormatFrom) >= 5) {
    			$month = substr($dateFormatFrom, 3, 2);
    		}

    		if (strlen($dateFormatFrom) >= 10) {
    			$year = substr($dateFormatFrom, 6, 4);
    		}
    	}else{
    		if (strlen($dateFormatFrom) >= 2) {
    			$date = substr($dateFormatFrom, 0, 2);
    		}

    		if (strlen($dateFormatFrom) >= 5) {
    			$month = substr($dateFormatFrom, 3, 2);
    		}

    		if (strlen($dateFormatFrom) >= 10) {
    			$year = substr($dateFormatFrom, 6, 4);
    		}
    	}
    	return $year . $month . $date;
    }

    /**
     *  #1054 add multi dimension capability for admin
     * Convert date format from dd/MM/yyyy(MM/dd/yyyy) => yyyy-MM-dd(yyyy-dd-MM)
     *
     * @param unknown_type $dateFormatFrom
     */
    public static function convertDateFormatFrom03($dateFormatFrom)
    {
    	if (empty($dateFormatFrom)) {
    		return '';
    	}

    	$date = '';
    	$month = '';
    	$year = '';
    	$date_format = APUtils::get_date_format_in_user_profiles();
    	if ($date_format == APConstants::DATEFORMAT_DEFAULT){
    		if (strlen($dateFormatFrom) >= 2) {
    			$month = substr($dateFormatFrom, 0, 2);
    		}

    		if (strlen($dateFormatFrom) >= 5) {
    			$date = substr($dateFormatFrom, 3, 2);
    		}

    		if (strlen($dateFormatFrom) >= 10) {
    			$year = substr($dateFormatFrom, 6, 4);
    		}
    		return $year . '-' . $date . '-' . $month;
    	}else{
    		if (strlen($dateFormatFrom) >= 2) {
    			$date = substr($dateFormatFrom, 0, 2);
    		}

    		if (strlen($dateFormatFrom) >= 5) {
    			$month = substr($dateFormatFrom, 3, 2);
    		}

    		if (strlen($dateFormatFrom) >= 10) {
    			$year = substr($dateFormatFrom, 6, 4);
    		}
    		return $year . '-' . $month . '-' . $date;
    	}
    }

    /**
     *  #1058 add multi dimension capability for admin
     * Convert date format from dd/MM/yyyy(MM/dd/yyyy) => yyyyMMdd
     *
     * @param unknown_type $dateFormatFrom
     */
    public static function convertDateFormatFrom04($dateFormatFrom)
    {
    	if (empty($dateFormatFrom)) {
    		return '';
    	}

    	$month = '';
    	$year = '';
    	$date = '';
    	$date_format = APConstants::DATEFORMAT_DEFAULT;//APUtils::get_date_format_in_user_profiles();
    	if ($date_format == APConstants::DATEFORMAT_DEFAULT){
    		if (strlen($dateFormatFrom) >= 2) {
    			$date = substr($dateFormatFrom, 0, 2);
    		}

    		if (strlen($dateFormatFrom) >= 5) {
    			$month = substr($dateFormatFrom, 3, 2);
    		}

    		if (strlen($dateFormatFrom) >= 10) {
    			$year = substr($dateFormatFrom, 6, 4);
    		}
    	}else{
    		if (strlen($dateFormatFrom) >= 2) {
    			$date = substr($dateFormatFrom, 0, 2);
    		}

    		if (strlen($dateFormatFrom) >= 5) {
    			$month = substr($dateFormatFrom, 3, 2);
    		}

    		if (strlen($dateFormatFrom) >= 10) {
    			$year = substr($dateFormatFrom, 6, 4);
    		}
    	}
    	return $year . $month. $date;
    }

    /**
     *  #1054 add multi dimension capability for admin
     * Convert date format from dd/MM/yyyy(MM/dd/yyyy) => yyyy-MM-dd
     *
     * @param unknown_type $dateFormatFrom
     */
    public static function convertDateFormatFrom05($dateFormatFrom)
    {
    	if (empty($dateFormatFrom)) {
    		return '';
    	}

    	$date = '';
    	$month = '';
    	$year = '';
    	$date_format = APUtils::get_date_format_in_user_profiles();
    	if ($date_format == APConstants::DATEFORMAT_DEFAULT){
    		if (strlen($dateFormatFrom) >= 2) {
    			$month = substr($dateFormatFrom, 0, 2);
    		}

    		if (strlen($dateFormatFrom) >= 5) {
    			$date = substr($dateFormatFrom, 3, 2);
    		}

    		if (strlen($dateFormatFrom) >= 10) {
    			$year = substr($dateFormatFrom, 6, 4);
    		}
    	}else{
    		if (strlen($dateFormatFrom) >= 2) {
    			$date = substr($dateFormatFrom, 0, 2);
    		}

    		if (strlen($dateFormatFrom) >= 5) {
    			$month = substr($dateFormatFrom, 3, 2);
    		}

    		if (strlen($dateFormatFrom) >= 10) {
    			$year = substr($dateFormatFrom, 6, 4);
    		}
    	}
    	return $year . '-' . $month . '-' . $date;
    }

    /**
     * Display date to format: dd/MM/yyyy (MM/dd/yyyy)
     *
     * @param unknown_type $paramConvert : Have string or int(timestamp)
     * @param string $format : 'd/m/y' or 'm/d/y'
     * @param string $type: 'xxx'
     */
    public static function viewDateFormat($timestamp, $format)
    {
    		return date($format, $timestamp);
    }

    /**
     * Merge multiple pdf file.
     * @param array $files
     * @return temparory file on upload folder.
     */
    public static function mergePDFfiles(array $files, $destination_file='', $orientation = 'L'){
        if(empty($files)){
            return null;
        }

        ci()->load->library(array(
            "FPDF/fpdf",
            "FPDI/fpdi"
        ));

        $pdf = new FPDI();
        for ($i = 0; $i < count($files); $i++ ) {
            $pagecount = $pdf->setSourceFile($files[$i]);
            for($j = 0; $j < $pagecount ; $j++)
            {
                $tplidx = $pdf->importPage(($j +1), '/MediaBox'); // template index.
                $pdf->addPage($orientation,'A4');// orientation can be P|L
                $pdf->useTemplate($tplidx, 0, 0, 0, 0, TRUE);
            }
        }

        // set the metadata.
        $pdf->SetAuthor("Clevvermail");
        //$pdf->SetCreator('Clevvermail');
        //$pdf->SetTitle('PDF, created: '.date("Y-m-d"));
        //$pdf->SetSubject('PDF subject !');

        if(empty($destination_file)){
            $tmp_merge_file_name = "uploads/".now().".pdf";
        }else{
            $tmp_merge_file_name = $destination_file;
        }
        $output = $pdf->Output($tmp_merge_file_name, 'F');

        return $tmp_merge_file_name;
    }

    /**
     * Merge multiple pdf file.
     * @param array $files
     * @return temparory file on upload folder.
     */
    public static function mergePDFfilesDefault(array $files, $destination_file){
        if(empty($files)){
            return null;
        }

        ci()->load->library(array(
            "FPDF/fpdf",
            "FPDI/fpdi"
        ));

        $pdf = new FPDI();
        for ($i = 0; $i < count($files); $i++ ) {
            $pagecount = $pdf->setSourceFile($files[$i]);
            for($j = 0; $j < $pagecount ; $j++)
            {
                $tplidx = $pdf->importPage(($j +1), '/MediaBox'); // template index.
                $pdf->addPage();// orientation can be P|L
                $pdf->useTemplate($tplidx, 0, 0, 0, 0, TRUE);
            }
        }

        // set the metadata.
        $pdf->SetAuthor("Clevvermail");
        $pdf->Output($destination_file, 'F');

        return $destination_file;
    }

    /**
     * Get first pdf page
     */
    public static function getFirstPdfPage($file, $destination_file='', $orientation = 'L') {
        if(empty($file)){
            return null;
        }

        ci()->load->library(array(
            "FPDF/fpdf",
            "FPDI/fpdi"
        ));

        $pdf = new FPDI();
        $pagecount = $pdf->setSourceFile($file);
        if ($pagecount < 1) {
            return null;
        }

        $tplidx = $pdf->importPage(1, '/MediaBox'); // template index.
        $pdf->addPage($orientation,'A4');// orientation can be P|L
        $pdf->useTemplate($tplidx, 0, 0, 0, 0, TRUE);

        // set the metadata.
        $pdf->SetAuthor("Clevvermail");
        //$pdf->SetCreator('Clevvermail');
        //$pdf->SetTitle('PDF, created: '.date("Y-m-d"));
        //$pdf->SetSubject('PDF subject !');

        if(empty($destination_file)){
            $tmp_merge_file_name = "uploads/".now().".pdf";
        }else{
            $tmp_merge_file_name = $destination_file;
        }
        $pdf->Output($tmp_merge_file_name, 'F');

        return $tmp_merge_file_name;
    }

    /**
     * Get value in json string by key. Return string value
     */
    public static function get_json_by_key($json_string, $json_key){
        if (APUtils::isValidJson($json_string)){
            $json_array = json_decode($json_string, true);
            foreach ($json_array as $key => $value){
                if ($key == $json_key) return $value;
            }
        } else {
            return null;
        }
    }

    /**
     * Remove json value by key in json string. Return json after remove data
     */
    public static function delete_json_by_key($json_string, $json_key){
        if (APUtils::isValidJson($json_string)){
            $json_array = json_decode($json_string, true);
                foreach ($json_array as $key => $value){
                   if ($key == $json_key){
                       unset($json_array[$key]);
                   }
               }
            return (empty($json_array) ? null : json_encode($json_array));
        } else {
            return 'error';
        }
    }

    /**
     * Check json string is valid json
     */
    public static function isValidJson($string){
        $json = json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }

    public static function convertToEnChar($input_str) {
        return strtr($input_str, APUtils::STRING_NORMALIZE_CHARS);
    }

    /**
     * sanitizing sql wild character.
     * @param type $str
     */
    public static function sanitizing($str){
        return str_replace(array("'", '"', '%', '\\'), array('', '', '', ''), $str);
    }

    /**
     * build template code
     */
    public static function buildTemplateCode($code, $customer_id, $language){
        $result = $code;
        if(!empty($customer_id)){
            $result .= '-C' .substr('0000000' .$customer_id, -8);
        }else{
            $result .= '-C0000000';
        }

        $result .= '-'.strtoupper(substr($language, 0, 2));

        return $result;
    }

    public static function removeKeyOfArray($array_input){
        if(empty($array_input)){
            return $array_input;
        }

        $result = array();
        foreach($array_input as $key=>$value){
            $result[] = $value;
        }

        return $result;
    }
    /**
     * Get IP Address
     */
     public static function getIPAddress() {
        return ci()->input->ip_address();
    }

    /**
     * Get IP Address
     */
    public static function getUserAgent() {
    	return ci()->input->user_agent();
    }

    /**
     * Convert Ids input param to a string, separator is ','
     */
    public static function convertIdsInputToString ($ids) {
        $ids_string = '';
        //Check empty input param
        if (empty($ids)) {
            return '';
        }

        if (is_array($ids)) {
            $ids_string = implode(',', $ids);
        } else {
            $ids_string = $ids;
        }

        return $ids_string;
    }

     /**
     * Convert Ids input param to a array
     */
    public static function convertIdsInputToArray ($ids) {
        $ids_array = array();
        //Check empty input param
        if (empty($ids)) {
            return array();
        }

        if (is_array($ids)) {
            $ids_array = $ids;
        } else {
            $ids_array = explode(',', $ids);
        }

        return $ids_array;
    }

    public static function removeSpecialCharacterForPayone($input_str) {
        if ($input_str == null || empty($input_str)) {
            return '';
        }
        if (preg_match("/['^!£$%*()}{@~<>,|=+¬]/", $input_str))
        {
            return '';
        }

        $firstchar = substr($input_str, 0, 1);
        if ( ! preg_match('/^([a-zA-Z\+_\-]+)$/', $firstchar) || $firstchar == '-')
        {
            return '';
        }
        $str = str_replace(' ', '-', $input_str); // Replaces all spaces with hyphens.
        $str = preg_replace('/[^A-Za-z0-9\-]/', '', $str); // Removes special chars.
        return $str;
    }
}
