<?php defined('BASEPATH') or exit('No direct script access allowed');

class CronUtils
{
    /**
     * Check this job already start or not
     *
     * @param unknown_type $jobName
     * @param unknown_type $year
     * @param unknown_type $month
     * @param unknown_type $day
     * @param unknown_type $hour
     * @param unknown_type $minute
     * @param unknown_type $second
     *
     * @return bool (true: If the cronjob was executed already; false: otherwise)
     */
    public static function isJobExecuted($jobName, $year, $month, $day, $hour = 0, $minute = 0, $second = 0)
    {
        ci()->load->model('cron/cron_job_m');

        $cronJob = ci()->cron_job_m->get_by_many(
            array(
                "job_name" => $jobName,
                "year" => $year,
                "month" => $month,
                "day" => $day,
                "hour" => $hour,
                "minute" => $minute,
                "second" => $second
            )
        );
        if ($cronJob) {
            return false;
        } else {
            // If the cronjob has not been executed
            ci()->cron_job_m->insert(
                array(
                    "job_name" => $jobName,
                    "year" => $year,
                    "month" => $month,
                    "day" => $day,
                    "hour" => $hour,
                    "minute" => $minute,
                    "second" => $second
                )
            );
            return true;
        }
    }

    public static function insertJob($jobName, $year, $month, $day, $hour = 0, $minute = 0, $second = 0)
    {
        ci()->load->model('cron/cron_job_m');

        $jobID = ci()->cron_job_m->insert(
            array(
                "job_name" => $jobName,
                "year" => $year,
                "month" => $month,
                "day" => $day,
                "hour" => $hour,
                "minute" => $minute,
                "second" => $second
            )
        );

        return $jobID;
    }

    public static function getRunningJobID($jobName, $year, $month, $day, $hour = 0, $minute = 0, $second = 0)
    {
        ci()->load->model('cron/cron_job_m');

        $cronJob = ci()->cron_job_m->get_by_many(
            array(
                "job_name" => $jobName,
                "year" => $year,
                "month" => $month,
                "day" => $day,
                "hour" => $hour,
                "minute" => $minute,
                "second" => $second
            )
        );
        $jobID = ($cronJob) ? $cronJob->id : 0;

        return $jobID;
    }

    public static function logJobTimeExecution($jobID, $startTime, $endTime)
    {
        ci()->load->model('cron/cron_job_m');

        $t1 = strtotime($startTime);
        $t2 = strtotime($endTime);
        $differenceInSeconds = $t2 - $t1;
        $differenceInMinutes = round((float)$differenceInSeconds / 60);

        // Build job status message.
        $jobStatusMessage = sprintf(CronConfigs::MSG_JOB_STATUS, $startTime, $endTime, $differenceInMinutes);

        // update job status.
        ci()->cron_job_m->update($jobID, array('job_status' => $jobStatusMessage));
    }

    public static function checkJobStatus($jobStatus)
    {
        if ($jobStatus) {
            $arrayJobStatus = explode('.', $jobStatus);
            if (count($arrayJobStatus) == 2) {
                $jobTimes = $arrayJobStatus[0];
                $arrayJobTimes = explode(' - ', $jobTimes);
                if (count($arrayJobTimes) == 2) {
                    $startTime = \DateTime::createFromFormat('Y-m-d H:i:s', $arrayJobTimes[0]);
                    $endTime = \DateTime::createFromFormat('Y-m-d H:i:s', $arrayJobTimes[1]);
                    if ($startTime !== false && $endTime !== false) {
                        return 'SUCCESS';
                    }
                }
            }
        }

        return 'ERROR';
    }

    public static function getJobTimesArray()
    {
        // Current day
        $datetime = new DateTime();
        $curJobTimes = array(
            'curYear' => $datetime->format('Y'),
            'curMonth' => intval($datetime->format('m')),
            'curDay' => intval($datetime->format('d'))
        );

        // Previous day
        $interval = new DateInterval('P1D');
        $datetime->sub($interval);
        $prevJobTimes = array(
            'prevYear' => $datetime->format('Y'),
            'prevMonth' => intval($datetime->format('m')),
            'prevDay' => intval($datetime->format('d'))
        );

        $jobTimes = array_merge($curJobTimes, $prevJobTimes);

        return $jobTimes;
    }

    public static function buildHtmlTableOfDeletedAccounts(array $neverActivatedAccounts, array $deletedInactiveAccounts, array $deletedAutoDeactivatedAccounts)
    {
        $length1 = count($neverActivatedAccounts);
        $length2 = count($deletedInactiveAccounts);
        $length3 = count($deletedAutoDeactivatedAccounts);
        $maxLength = ($length1 > $length2) ? $length1 : $length2;
        $maxLength = ($maxLength > $length3) ? $maxLength : $length3;
        $borderColor = 'border: solid 1px #988F9E;';
        $table = <<<HTML
<table style="{$borderColor} border-collapse: collapse;" cellpadding="10px" cellspacing="0">
    <thead>
        <tr style="{$borderColor}">
            <th style="{$borderColor} font-weight: bold; text-align: center;">#</th>
            <th style="{$borderColor} font-weight: bold; text-align: left;">Auto-deactivation due to failed email confirmation</th>
            <th style="{$borderColor} font-weight: bold; text-align: left;">Deleted due to 70 days of auto-deactivation ( Never activate)</th>
            <th style="{$borderColor} font-weight: bold; text-align: left;">Deleted due to 70 days of auto-deactivation</th>
        </tr>
    </thead>
    <tbody>
HTML;
        for ($i = 0; $i < $maxLength; $i++) {
            $line = $i + 1;
            $col1 = isset($neverActivatedAccounts[$i]) ? $neverActivatedAccounts[$i] : '&nbsp;';
            $col2 = isset($deletedInactiveAccounts[$i]) ? $deletedInactiveAccounts[$i] : '&nbsp;';
            $col3 = isset($deletedAutoDeactivatedAccounts[$i]) ? $deletedAutoDeactivatedAccounts[$i] : '&nbsp;';
            $tr = <<<HTML
        <tr style="{$borderColor}">
            <td style="{$borderColor}">{$line}</td>
            <td style="{$borderColor}">{$col1}</td>
            <td style="{$borderColor}">{$col2}</td>
            <td style="{$borderColor}">{$col3}</td>
        </tr>
HTML;
            $table .= $tr;
        }
        $ending = <<<HTML
    </tbody>
</table>
HTML;
        $table .= $ending;

        return $table;
    }

    protected static function getFormatCustomerCodeAndCharge(array $customers)
    {
        $result = [];
        foreach ($customers as $key => $cust) {
            // Gets current balance
            $balance = CustomerUtils::getAdjustOpenBalanceDue($cust->customer_id);
            $totalBalance = $balance["OpenBalanceDue"] + $balance['OpenBalanceThisMonth'];
            $textBalance = ' OpenBalance: ' . round($balance["OpenBalanceDue"], 2) . ' ; ' . round($balance['OpenBalanceThisMonth'], 2);
            $result[] = 'Customer-Code: '. $cust->customer_code . ' - ' . $textBalance;
        }
        return $result;
    }

    public static function buildHtmlTableOfDeletedAndRemainAccounts(array $byCustomers, array $neverActivatedDelete, array $deleteDueToAutoDeactive, array $byAdmins, array $remainNeverActive, array $remainAutoDeactived, array $remainManualDeactived)
    {
        $maxLength = max([count($byCustomers), count($neverActivatedDelete), count($deleteDueToAutoDeactive),
                count($byAdmins), count($remainNeverActive), count($remainAutoDeactived), count($remainManualDeactived)]);
        $args = func_get_args();
        foreach ($args as $key => $arg) {
            $args[$key] = self::getFormatCustomerCodeAndCharge($arg);
        }
        $borderColor = 'border: solid 1px #988F9E;';
        $table = <<<HTML
<table style="{$borderColor} border-collapse: collapse;" cellpadding="10px" cellspacing="0">
    <thead>
        <tr style="{$borderColor}">
            <th style="{$borderColor} font-weight: bold; text-align: center;">#</th>
            <th style="{$borderColor} font-weight: bold; text-align: left;">Deleted by customer </th>
            <th style="{$borderColor} font-weight: bold; text-align: left;">Deleted due to Never Activated</th>
            <th style="{$borderColor} font-weight: bold; text-align: left;">Deleted due to 70 days of Deactivation</th>
            <th style="{$borderColor} font-weight: bold; text-align: left;">Deleted manually by admin</th>
            <th style="{$borderColor} font-weight: bold; text-align: left;">Remaining never activated</th>
            <th style="{$borderColor} font-weight: bold; text-align: left;">Remaining deactivated &lt;70 days</th>
            <th style="{$borderColor} font-weight: bold; text-align: left;">Remaining manually deactivated</th>
        </tr>
    </thead>
    <tbody>
HTML;
        for ($i = 0; $i < $maxLength; $i++) {
            $line = $i + 1;
            $col1 = isset($args[0][$i]) ? $args[0][$i] : '&nbsp;';
            $col2 = isset($args[1][$i]) ? $args[1][$i] : '&nbsp;';
            $col3 = isset($args[2][$i]) ? $args[2][$i] : '&nbsp;';
            $col4 = isset($args[3][$i]) ? $args[3][$i] : '&nbsp;';
            $col5 = isset($args[4][$i]) ? $args[4][$i] : '&nbsp;';
            $col6 = isset($args[5][$i]) ? $args[5][$i] : '&nbsp;';
            $col7 = isset($args[6][$i]) ? $args[6][$i] : '&nbsp;';
            $tr = <<<HTML
        <tr style="{$borderColor}">
            <td style="{$borderColor}">{$line}</td>
            <td style="{$borderColor}">{$col1}</td>
            <td style="{$borderColor}">{$col2}</td>
            <td style="{$borderColor}">{$col3}</td>
            <td style="{$borderColor}">{$col4}</td>
            <td style="{$borderColor}">{$col5}</td>
            <td style="{$borderColor}">{$col6}</td>
            <td style="{$borderColor}">{$col7}</td>
        </tr>
HTML;
            $table .= $tr;
        }
        $ending = <<<HTML
    </tbody>
</table>
HTML;
        $table .= $ending;
        return $table;
    }

    public static function buildHtmlTableOfPlanDeleteCustomers(array $customers)
    {
        $length = count($customers);
        $borderColor = 'border: solid 1px #988F9E;';
        $table = <<<HTML
<h2>The list of all customers deleted by plan delete date (= yesterday)</h2>
<table style="{$borderColor} border-collapse: collapse;" cellpadding="10px" cellspacing="0">
    <thead>
        <tr style="{$borderColor}">
            <th style="{$borderColor} font-weight: bold; text-align: center;">#</th>
            <th style="{$borderColor} font-weight: bold; text-align: left;">Customer code</th>
            <th style="{$borderColor} font-weight: bold; text-align: left;">Open Balance Due</th>
            <th style="{$borderColor} font-weight: bold; text-align: left;">Open Balance This Month</th>
        </tr>
    </thead>
    <tbody>
HTML;
        for ($i = 0; $i < $length; $i++) {
            $line = $i + 1;
            $item1 = $customers[$i]['CustomerCode'];
            $item2 = APUtils::number_format($customers[$i]['OpenBalanceDue']);
            $item3 = APUtils::number_format($customers[$i]['OpenBalanceThisMonth']);
            $tr = <<<HTML
        <tr style="{$borderColor}">
            <td style="{$borderColor}">{$line}</td>
            <td style="{$borderColor}">{$item1}</td>
            <td style="{$borderColor}">{$item2}</td>
            <td style="{$borderColor}">{$item3}</td>
        </tr>
HTML;
            $table .= $tr;
        }
        $ending = <<<HTML
    </tbody>
</table>
HTML;
        $table .= $ending;

        return $table;
    }

    public static function buildHtmlTableOfCustomerInvoices(array $customers)
    {
        $length = count($customers);
        $borderColor = 'border: solid 1px #988F9E;';
        $table = <<<HTML
<h2>The list of all customers' calculated invoices</h2>
<table style="{$borderColor} border-collapse: collapse;" cellpadding="10px" cellspacing="0">
    <thead>
        <tr style="{$borderColor}">
            <th style="{$borderColor} font-weight: bold; text-align: center;">#</th>
            <th style="{$borderColor} font-weight: bold; text-align: left;">Customer ID</th>
        </tr>
    </thead>
    <tbody>
HTML;
        for ($i = 0; $i < $length; $i++) {
            $line = $i + 1;
            $item = $customers[$i];
            $tr = <<<HTML
        <tr style="{$borderColor}">
            <td style="{$borderColor}">{$line}</td>
            <td style="{$borderColor}">{$item}</td>
        </tr>
HTML;
            $table .= $tr;
        }
        $ending = <<<HTML
    </tbody>
</table>
HTML;
        $table .= $ending;

        return $table;
    }

    public static function buildHtmlTableOfCustomersCaseVerifyAddress(array $customers)
    {
        $length = count($customers);
        $borderColor = 'border: solid 1px #988F9E;';
        $table = <<<HTML
<h2>The list of all customers case verification address</h2>
<table style="{$borderColor} border-collapse: collapse;" cellpadding="10px" cellspacing="0">
    <thead>
        <tr style="{$borderColor}">
            <th style="{$borderColor} font-weight: bold; text-align: center;">#</th>
            <th style="{$borderColor} font-weight: bold; text-align: left;">Customer ID</th>
        </tr>
    </thead>
    <tbody>
HTML;
        for ($i = 0; $i < $length; $i++) {
            $line = $i + 1;
            $item = $customers[$i];
            $tr = <<<HTML
        <tr style="{$borderColor}">
            <td style="{$borderColor}">{$line}</td>
            <td style="{$borderColor}">{$item}</td>
        </tr>
HTML;
            $table .= $tr;
        }
        $ending = <<<HTML
    </tbody>
</table>
HTML;
        $table .= $ending;

        return $table;
    }

    public static function buildHtmlTableOfNewCustomersRegisteredIn24h($customers)
    {
        $borderColor = 'border: solid 1px #988F9E;';
        $table = <<<HTML
<h2>The list of all new customers have just registered in 24 hours</h2>
<table style="{$borderColor} border-collapse: collapse;" cellpadding="10px" cellspacing="0">
    <thead>
        <tr style="{$borderColor}">
            <th style="{$borderColor} font-weight: bold; text-align: center;">#</th>
            <th style="{$borderColor} font-weight: bold; text-align: left;">Customer ID</th>
            <th style="{$borderColor} font-weight: bold; text-align: left;">Postbox ID</th>
        </tr>
    </thead>
    <tbody>
HTML;
        foreach ($customers as $index => $customer) {
            $line = $index + 1;
            $customerID = $customer->customer_id;
            $postboxID = $customer->postbox_id;
            $tr = <<<HTML
        <tr style="{$borderColor}">
            <td style="{$borderColor}">{$line}</td>
            <td style="{$borderColor}">{$customerID}</td>
            <td style="{$borderColor}">{$postboxID}</td>
        </tr>
HTML;
            $table .= $tr;
        }
        $ending = <<<HTML
    </tbody>
</table>
HTML;
        $table .= $ending;

        return $table;
    }

    public static function buildHtmlTableOfUpdatePostboxCode($postboxes)
    {
        $borderColor = 'border: solid 1px #988F9E;';
        $table = <<<HTML
<h2>The list of all postboxes that have been updated postbox code</h2>
<table style="{$borderColor} border-collapse: collapse;" cellpadding="10px" cellspacing="0">
    <thead>
        <tr style="{$borderColor}">
            <th style="{$borderColor} font-weight: bold; text-align: center;">#</th>
            <th style="{$borderColor} font-weight: bold; text-align: left;">Customer ID</th>
            <th style="{$borderColor} font-weight: bold; text-align: left;">Location ID</th>
            <th style="{$borderColor} font-weight: bold; text-align: left;">Postbox ID</th>
        </tr>
    </thead>
    <tbody>
HTML;
        foreach ($postboxes as $index => $postbox) {
            $line = $index + 1;
            $customerID = $postbox->customer_id;
            $locationID = $postbox->location_available_id;
            $postboxID = $postbox->postbox_id;
            $tr = <<<HTML
        <tr style="{$borderColor}">
            <td style="{$borderColor}">{$line}</td>
            <td style="{$borderColor}">{$customerID}</td>
            <td style="{$borderColor}">{$locationID}</td>
            <td style="{$borderColor}">{$postboxID}</td>
        </tr>
HTML;
            $table .= $tr;
        }
        $ending = <<<HTML
    </tbody>
</table>
HTML;
        $table .= $ending;

        return $table;
    }

    //delete_envelope_old30
    public static function buildHtmlTableOfDeleteEnvelopeOld30($envelopes)
    {
        $borderColor = 'border: solid 1px #988F9E;';
        $table = <<<HTML
<h2>The list of all envelopes deleted today.</h2>
<table style="{$borderColor} border-collapse: collapse;" cellpadding="10px" cellspacing="0">
    <thead>
        <tr style="{$borderColor}">
            <th style="{$borderColor} font-weight: bold; text-align: center;">#</th>
            <th style="{$borderColor} font-weight: bold; text-align: left;">Customer ID</th>
            <th style="{$borderColor} font-weight: bold; text-align: left;">Envelope ID</th>
        </tr>
    </thead>
    <tbody>
HTML;
        foreach ($envelopes as $index => $envelope) {
            $line = $index + 1;
            $customerID = $envelope->to_customer_id;
            $envelopeID = $envelope->id;
            $tr = <<<HTML
        <tr style="{$borderColor}">
            <td style="{$borderColor}">{$line}</td>
            <td style="{$borderColor}">{$customerID}</td>
            <td style="{$borderColor}">{$envelopeID}</td>
        </tr>
HTML;
            $table .= $tr;
        }
        $ending = <<<HTML
    </tbody>
</table>
HTML;
        $table .= $ending;

        return $table;
    }

    //update_pricing_template
    public static function buildHtmlTableOfUpdatePricingTemplate(array $reportData)
    {
        $borderColor = 'border: solid 1px #988F9E;';
        $table = <<<HTML
<h2>The list of all locations that have pricing template changed for the current month.</h2>
<table style="{$borderColor} border-collapse: collapse;" cellpadding="10px" cellspacing="0">
    <thead>
        <tr style="{$borderColor}">
            <th style="{$borderColor} font-weight: bold; text-align: center;">#</th>
            <th style="{$borderColor} font-weight: bold; text-align: left;">Location ID</th>
            <th style="{$borderColor} font-weight: bold; text-align: left;">Location Name</th>
            <th style="{$borderColor} font-weight: bold; text-align: left;">Pricing Template ID of last month</th>
            <th style="{$borderColor} font-weight: bold; text-align: left;">Pricing Template Name of last month</th>
            <th style="{$borderColor} font-weight: bold; text-align: left;">Pricing Template ID of current month</th>
            <th style="{$borderColor} font-weight: bold; text-align: left;">Pricing Template Name of current month</th>
        </tr>
    </thead>
    <tbody>
HTML;
        foreach ($reportData as $index => $item) {
            $line = $index + 1;
            $tr = <<<HTML
        <tr style="{$borderColor}">
            <td style="{$borderColor}">{$line}</td>
            <td style="{$borderColor}">{$item[0]}</td>
            <td style="{$borderColor}">{$item[1]}</td>
            <td style="{$borderColor}">{$item[2]}</td>
            <td style="{$borderColor}">{$item[3]}</td>
            <td style="{$borderColor}">{$item[4]}</td>
            <td style="{$borderColor}">{$item[5]}</td>
        </tr>
HTML;
            $table .= $tr;
        }
        $ending = <<<HTML
    </tbody>
</table>
HTML;
        $table .= $ending;

        return $table;
    }

    public static function buildHtmlTableOfNotifyLocationAdminOpenActivity($message)
    {
        $html = ci()->load->view("notify_location_admin", array('message'=> $message), true);

        return $html;
    }

    public static function buildHtmlTableOfNotifyIncommingItem($list_notify_daily, $list_notify_weekly, $list_notify_monthly)
    {
        $data = array(
            'list_notify_daily' => $list_notify_daily,
            'list_notify_weekly' => $list_notify_weekly,
            'list_notify_monthly' => $list_notify_monthly
        );

        $html = ci()->load->view("notify_incomming_item", $data, true);

        return $html;
    }

    public static function buildHtmlUpdateCurrencyExchangeRate($result_update)
    {
        $html = ci()->load->view("update_currency_exchange_rate", array("result_update" => $result_update), true);

        return $html;
    }

    public static function buildHtmlUpdateOpenBalanceDue($result_update)
    {
        /*
        ci()->load->model(array(
            'customers/customer_m',
            'settings/currencies_m'
        ));
         */
        $html = ci()->load->view("update_open_balance_due", array("result_update" => $result_update), true);

        return $html;
    }

}