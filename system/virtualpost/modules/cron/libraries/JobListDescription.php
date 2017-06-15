<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class JobListDescription
{
    const JOB_TYPE_DAILY = 'DAILY';
    const JOB_TYPE_MONTHLY = 'MONTHLY';
    const JOB_TYPE_WEEKLY = 'WEEKLY';

    private $jobList;

    public function __construct()
    {
        ci()->load->library('cron/JobDescription');

        $this->jobList = array();

        // 22h~23h (PM)
        $this->jobList[] = $this->getJobDescriptionInstance('cancel_verification_cases', '50 22 * * *');
        $this->jobList[] = $this->getJobDescriptionInstance('update_currency_exchange_rate', '0 23 * * *');
        $this->jobList[] = $this->getJobDescriptionInstance('send_first_letter', '10 23 * * *');
        $this->jobList[] = $this->getJobDescriptionInstance('sync_customer_mailchimp', '20 23 * * *');
        $this->jobList[] = $this->getJobDescriptionInstance('update_post_code', '30 23 * * *');
        $this->jobList[] = $this->getJobDescriptionInstance('send_general_accounts_reporting_job', '40 23 * * *');
        $this->jobList[] = $this->getJobDescriptionInstance('auto_trash', '35 23 * * *');
        $this->jobList[] = $this->getJobDescriptionInstance('send_notify_new_terms_condition', '40 23 * * *');
        $this->jobList[] = $this->getJobDescriptionInstance('check_customer_accept_new_terms_condition', '55 23 * * *');

        // 0h~1h (AM)
        $this->jobList[] = $this->getJobDescriptionInstance('update_pricing_template', '0 0 1 * *');
        $this->jobList[] = $this->getJobDescriptionInstance('createBonusCreditnoteOfPartner', '5 0 1 * *');
        $this->jobList[] = $this->getJobDescriptionInstance('collect_shipping_envelope', '10 0 * * *');
        $this->jobList[] = $this->getJobDescriptionInstance('delete_envelope_old30', '20 0 * * *');
        $this->jobList[] = $this->getJobDescriptionInstance('storage_envelope_old30', '30 0 * * *');
        $this->jobList[] = $this->getJobDescriptionInstance('calculate_invoices', '40 0 * * *');
        $this->jobList[] = $this->getJobDescriptionInstance('send_email_notify', '50 0 * * *');
        $this->jobList[] = $this->getJobDescriptionInstance('calculate_total_invoice', '0 1 * * *');
        $this->jobList[] = $this->getJobDescriptionInstance('update_open_balance_due', '10 1 * * *');
        $this->jobList[] = $this->getJobDescriptionInstance('notify_email_new_and_delete_customers', '30 1 * * *');
        $this->jobList[] = $this->getJobDescriptionInstance('notify_todo_number_to_location_admin', '35 1 * * *');

        // 2h~3h (AM)
        $this->jobList[] = $this->getJobDescriptionInstance('delete_postbox', '42 0 1 * *');
        $this->jobList[] = $this->getJobDescriptionInstance('check_account_registration', '20 2 * * *');
        $this->jobList[] = $this->getJobDescriptionInstance('check_account_registration', '40 2 * * 6');
        $this->jobList[] = $this->getJobDescriptionInstance('delete_plan_customer', '30 2 * * *');
        $this->jobList[] = $this->getJobDescriptionInstance('generate_invoice_pdf', '40 2 * * *');

        // 0h~4h (AM)
        $this->jobList[] = $this->getJobDescriptionInstance('check_card_expire_date', '0 0 * * 2');
        $this->jobList[] = $this->getJobDescriptionInstance('send_email_job', '5 0 * * *');
        $this->jobList[] = $this->getJobDescriptionInstance('apply_new_account', '10 0 1 * *');
        $this->jobList[] = $this->getJobDescriptionInstance('send_email_notify_open_balance_due', '10 1 1 * *');
        $this->jobList[] = $this->getJobDescriptionInstance('send_email_notify_deactivate_open_balance_due', '20 1 10 * *');
        $this->jobList[] = $this->getJobDescriptionInstance('send_invoice_monthly_report', '50 3 1 * *');
        $this->jobList[] = $this->getJobDescriptionInstance('update_location_report', '50 2 * * *');
        
        $this->jobList[] = $this->getJobDescriptionInstance('account_deletion_and_remain', '40 2 * * 7');
    }

    public function getExpectedExecJobs()
    {
        $curDay = intval(date('d'));
        $curDayOfWeek = date('w');

        // Get daily jobs first!
        $jobs = $this->getJobsByType(self::JOB_TYPE_DAILY);

        // Get monthly jobs that have the same date as current date
        $monthlyJobs = $this->getJobsByType(self::JOB_TYPE_MONTHLY);
        foreach ($monthlyJobs as $job) {
            if ($job->getJobDay() == $curDay) {
                $jobs[] = $job;
            }
        }

        // Get weekly jobs that have the same date of week as the date of current week.
        $weeklyJobs = $this->getJobsByType(self::JOB_TYPE_WEEKLY);
        foreach ($weeklyJobs as $job) {
            if ($job->getJobDayOfWeek() == $curDayOfWeek) {
                $jobs[] = $job;
            }
        }

        return $this->sortJobsByStartTime($jobs);
    }

    private function getJobsByType($jobType = self::JOB_TYPE_DAILY)
    {
        $jobs = array();

        switch ($jobType) {
            case self::JOB_TYPE_DAILY:
                foreach ($this->jobList as $job) {
                    if ($job->isDailyJob()) {
                        $jobs[] = $job;
                    }
                }
                break;
            case self::JOB_TYPE_MONTHLY:
                foreach ($this->jobList as $job) {
                    if ($job->isMonthlyJob()) {
                        $jobs[] = $job;
                    }
                }
                break;
            case self::JOB_TYPE_WEEKLY:
                foreach ($this->jobList as $job) {
                    if ($job->isWeeklyJob()) {
                        $jobs[] = $job;
                    }
                }
                break;
        }
        $job = $this->jobList[12];

        return $jobs;
    }

    private function sortJobsByName(array $jobs)
    {
        $sortedJobs = array();

        $jobNames = array();
        foreach ($jobs as $job) {
            $jobNames[] = $job->getJobName();
        }
        sort($jobNames);

        foreach ($jobNames as $jobName) {
            foreach ($jobs as $job) {
                if ($job->hasJobName($jobName)) {
                    $sortedJobs[] = $job;
                    break;
                }
            }
        }

        return $sortedJobs;
    }

    private function sortJobsByStartTime(array $jobs)
    {
        $sortedJobs = array();

        // Get an array of sorted job hours
        $todayJobHours = array();
        $yesterdayJobHours = array();
        foreach ($jobs as $job) {
            $jobHour = $job->getJobHour();
            if ($jobHour == 22 || $jobHour == 23) {
                $yesterdayJobHours[] = $jobHour;
            } else {
                $todayJobHours[] = $jobHour;
            }
        }
        $yesterdayJobHours = array_unique($yesterdayJobHours);
        $todayJobHours = array_unique($todayJobHours);
        sort($yesterdayJobHours);
        sort($todayJobHours);
        $jobHours = array_merge($yesterdayJobHours, $todayJobHours);

        // Get an array of sorted job minutes
        $jobMinutes = array();
        foreach ($jobs as $job) {
            $jobMinutes[] = $job->getJobMinute();
        }
        $jobMinutes = array_unique($jobMinutes);
        sort($jobMinutes);

        // Sort the job list by job hour and job minute
        foreach ($jobHours as $jobHour) {
            foreach ($jobMinutes as $jobMinute) {
                foreach ($jobs as $job) {
                    // Two jobs cannot have the same start time (hour & minute)
                    if ($job->hasJobStartTime($jobHour, $jobMinute)) {
                        $sortedJobs[] = $job;
                        break;
                    }
                }
            }
        }

        return $sortedJobs;
    }

    private function getJobDescriptionInstance($jobName, $timeSettings)
    {
        $obj = new JobDescription();
        $obj->setJobName($jobName);
        $obj->setJobTimeSettings($timeSettings);

        return $obj;
    }
}