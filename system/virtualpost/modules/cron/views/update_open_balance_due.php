<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>
    table#notify_locaton_admin th, table#notify_locaton_admin td{
        border: solid 1px #ccc;padding: 6px 8px;text-align: center;
    }
</style>
</head>

<body>
    <h2>The list of  update Open Balance Due </h2>    
   
<table id="notify_locaton_admin" style=" border-collapse: collapse;" cellpadding="10px" cellspacing="0">
    <thead>
        <tr style="">
            <th style="">#</th>
            <th style="">Customer ID</th>
            <th style="">Open Balance Due</th>
            <th style="">Open Balance This Month</th>
        </tr>
    </thead>
    <tbody>
    <?php     
        $i=0;
        foreach ($result_update as $row) { $i++;
        /*
        $currency = $this->customer_m->get_standard_setting_currency($row['customer_id']);
        $decimal_separator = $this->customer_m->get_standard_setting_decimal_separator($row['customer_id']);
        if (empty($currency)) {
            $currency = $this->currencies_m->get_by(array('currency_short' => 'EUR'));
        }
        */
    ?>          
        <tr>
            <td style=""><?php echo $i; ?></td>
            <td style=""><?php echo $row['customer_id']; ?> </td>
            <td style=""><?php 
                //echo APUtils::convert_currency($row['OpenBalanceDue'], $currency->currency_rate, 2, $decimal_separator).' '.$currency->currency_short; 
                echo number_format($row['OpenBalanceDue'], 2, ".", ",");
            ?>
            </td>
            <td style=""><?php 
                //echo APUtils::convert_currency($row['OpenBalanceThisMonth'], $currency->currency_rate, 2, $decimal_separator).' '.$currency->currency_short; 
                echo number_format($row['OpenBalanceThisMonth'], 2, ".", ",");
                ?></td>
        </tr>
    <?php } ?>    
    </tbody>
</table>
</body>
</html>