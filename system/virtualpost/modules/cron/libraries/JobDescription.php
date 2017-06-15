<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class JobDescription
{
    const EVERY_TIME_SYMBOL = '*';

    private $jobName;
    private $minute;
    private $hour;
    private $day;
    private $month;
    private $dayOfWeek;

    public function __construct()
    {
        date_default_timezone_set('CET');
    }

    public function setJobName($jobName)
    {
        $this->jobName = $jobName;
    }

    public function setJobTimeSettings($timeSettings)
    {
        $arrayTimeSettings = $this->convertToArray($timeSettings);
        if (count($arrayTimeSettings) != 5) {
            throw new Exception('The time settings for the cron-job is not valid!');
        }
        $arrayTimeSettings = array_map('trim', $arrayTimeSettings);
        $this->minute = $arrayTimeSettings[0];
        $this->hour = $arrayTimeSettings[1];
        $this->day = $arrayTimeSettings[2];
        $this->month = $arrayTimeSettings[3];
        $this->dayOfWeek = $arrayTimeSettings[4];
    }

    public function isDailyJob()
    {
        return ($this->minute != self::EVERY_TIME_SYMBOL) && ($this->hour != self::EVERY_TIME_SYMBOL) &&
        ($this->day == self::EVERY_TIME_SYMBOL) && ($this->month == self::EVERY_TIME_SYMBOL) &&
        ($this->dayOfWeek == self::EVERY_TIME_SYMBOL);
    }

    public function isMonthlyJob()
    {
        return ($this->minute != self::EVERY_TIME_SYMBOL) && ($this->hour != self::EVERY_TIME_SYMBOL) &&
        ($this->day != self::EVERY_TIME_SYMBOL) && ($this->month == self::EVERY_TIME_SYMBOL) &&
        ($this->dayOfWeek == self::EVERY_TIME_SYMBOL);
    }

    public function isWeeklyJob()
    {
        return ($this->minute != self::EVERY_TIME_SYMBOL) && ($this->hour != self::EVERY_TIME_SYMBOL) &&
        ($this->day == self::EVERY_TIME_SYMBOL) && ($this->month == self::EVERY_TIME_SYMBOL) &&
        ($this->dayOfWeek != self::EVERY_TIME_SYMBOL);
    }

    public function hasJobName($jobName)
    {
        return ($jobName == $this->jobName);
    }

    public function hasJobStartTime($jobHour, $jobMinute)
    {
        return ($jobHour == $this->hour && $jobMinute == $this->minute);
    }

    public function getJobName()
    {
        return $this->jobName;
    }

    public function getJobMinute()
    {
        return $this->minute;
    }

    public function getJobHour()
    {
        return $this->hour;
    }

    public function getJobDay()
    {
        return $this->day;
    }

    public function getJobMonth()
    {
        return $this->month;
    }

    public function getJobDayOfWeek()
    {
        return $this->dayOfWeek;
    }

    public function getJobStartTime()
    {
        $hour = ($this->hour < 10) ? '0' . $this->hour : '' . $this->hour;
        $minute = ($this->minute) < 10 ? '0' . $this->minute : '' . $this->minute;
        $timeDescription = '';
        if ($this->isDailyJob()) {
            $timeDescription = "EVERY DAY: {$hour}:{$minute} (time)";
        } elseif ($this->isMonthlyJob()) {
            $timeDescription = "EVERY MONTH: $this->day (day), {$hour}:{$minute} (time)";
        } elseif ($this->isWeeklyJob()) {
            $arrayDaysOfWeek = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
            $dayOfWeek = $arrayDaysOfWeek[$this->dayOfWeek];
            $timeDescription = "EVERY WEEK: $dayOfWeek, {$hour}:{$minute} (time)";
        }

        return $timeDescription;
    }

    /**
     * @param type $jobName
     * @param type $timeSettings
     * @return JobDescription
     * Simple factory to JobDescription to allow calling methods directly at initiation
     */
    public static function create()
    {
        $obj = new ReflectionClass('JobDescription');

        return $obj->newInstanceArgs(func_get_args());
    }

    private function convertToArray($timeSettings)
    {
        $arrayTimeSettings = explode(' ', $timeSettings);
        if (count($arrayTimeSettings) > 5) {
            $arrayTimeSettings = array_filter($arrayTimeSettings, function ($element) {
                return (trim($element) == '') ? false : true;
            });
            $arrayTimeSettings = array_values($arrayTimeSettings);
        }

        return $arrayTimeSettings;
    }
}