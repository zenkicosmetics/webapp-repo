<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Group model
 *
 *
 */
class cron_job_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->_table = $this->profile_table = $this->db->dbprefix('cron_job');
        $this->primary_key = 'id';
    }

    public function getAllExecutedJobs($year, $month, $day)
    {
        $arrayWhere = array(
            'year' => $year,
            'month' => intval($month),
            'day' => intval($day)
        );
        $arrayOrderBy = array(
            'job_name' => 'ASC',
            'hour' => 'ASC',
            'minute' => 'ASC',
            'second' => 'ASC'
        );
        $allJobs = $this->get_many_by_many($arrayWhere, '', false, $arrayOrderBy);

        return $allJobs;
    }

    public function getDistinctExecutedJobs($year, $month, $day)
    {
        $prevDay = intval($day) - 1;
        $arrayWhere = array(
            'year' => $year,
            'month' => intval($month),
            "(day = {$day}) OR (day = {$prevDay} AND hour >= 23)" => null
        );
        $arrayOrderBy = array(
            'job_name' => 'ASC'
        );
        $distinctJobs = $this->get_many_by_many($arrayWhere, 'job_name,job_status', true, $arrayOrderBy);

        return $distinctJobs;
    }

    public function getExecutedJob($jobName, array $jobTimes)
    {
        // Get today's job
        $arrayWhere = array(
            'job_name' => $jobName,
            'year' => $jobTimes['curYear'],
            'month' => $jobTimes['curMonth'],
            'day' => $jobTimes['curDay'],
        );

        // Only get the latest record (job)
        $arrayOrderBy = array(
            'year' => 'DESC',
            'month' => 'DESC',
            'day' => 'DESC',
            'hour' => 'DESC',
            'minute' => 'DESC',
            'second' => 'DESC'
        );

        // If not existed, get yesterday's job with the condition: hour = 23
        $job = $this->get_by_many_order($arrayWhere, $arrayOrderBy);
        if (empty($job)) {
            $arrayWhere = array(
                'job_name' => $jobName,
                'year' => $jobTimes['prevYear'],
                'month' => $jobTimes['prevMonth'],
                'day' => $jobTimes['prevDay'],
                'hour IN (22,23)' => null
            );
            $job = $this->get_by_many_order($arrayWhere, $arrayOrderBy);
        }

        return $job;
    }
}