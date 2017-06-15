<table>
        <tr>
            <td>Customer: Invoicing name: <?php if ($customer_shipping_address) { echo $customer_shipping_address->invoicing_address_name;}?> - 
              Invoicing company: <?php if ($customer_shipping_address) { echo $customer_shipping_address->invoicing_company;}?>
            </td>
        </tr>
        <tr>
            <td> Status: <?php echo $customer_status; ?></td>
        </tr>
        <tr> 
            <td>
                <?php
                    $sign = "";
                    if($open_balance > 0){
                        $sign = "+";
                    }
                ?>
                Open balance due: <?php echo $sign.APUtils::convert_currency($open_balance, $currency->currency_rate, 2, $decimal_separator).' '.$currency->currency_short;?></td>
        </tr>
        <tr>
            <td>
                <?php
                    $sign = "";
                    if($open_balance_this_month > 0){
                        $sign = "+";
                    }
                ?>
                Open balance current month: <?php echo $sign.APUtils::convert_currency($open_balance_this_month, $currency->currency_rate, 2, $decimal_separator)." ".$currency->currency_short; ?></td>
        </tr>
        <tr>
            <td>Remaining deposit: <?php if($open_balance_this_month < 0){
                       echo APUtils::convert_currency($open_balance_this_month, $currency->currency_rate, 2, $decimal_separator)." ".$currency->currency_short;
                    } ?></td>
        </tr>
       
</table>

<script>
jQuery(document).ready(function() {

});
</script>
