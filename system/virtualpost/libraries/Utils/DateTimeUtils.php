<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DateTimeUtils
{
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
     * Return format MM
     */
    public static function getCurrentMonth()
    {
        return date("m", now());
    }

    /**
     * Get current day
     * Return format dd.
     */
    public static function getCurrentDay()
    {
        return date("d", now());
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
     * Return format yyyyMMdd.
     */
    public static function getCurrentYearMonthDate()
    {
        return date("Ymd", now());
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
     * get target month to invoice $yearmonth = '201302'
     */
    public static function getFirstDayOfMonth($yearmonth)
    {
        return $yearmonth . '01';
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
     * get first day of current month
     */
    public static function getFirstDayOfCurrentMonth()
    {
        return self::getFirstDayOfMonth(self::getCurrentYearMonth());
    }

    /**
     * get last day of current month
     */
    public static function getLastDayOfCurrentMonth()
    {
        return self::getLastDayOfMonth(self::getFirstDayOfMonth(self::getCurrentYearMonth()));
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
     * get first day of current month
     */
    public static function getFirstDayOfNextMonth()
    {
        $end_date = date('Ym', strtotime('+1 month'));
        return self::getFirstDayOfMonth($end_date);
    }
}