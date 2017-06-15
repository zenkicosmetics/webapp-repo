<div class="button_container">
    <br>
    <span>Customer: <?php echo $customer->email;?></span>
    <div class="clear"></div>
    <form action="#" id="createDirectChargeWithoutInvoiceForm" class="dialog-form">
        <table style="border: 0px solid #dadada;margin: 5px 0px;width: 400px">
            <tr>
                <td width="100px">Amount:</td>
                <td><input type="text" style="width: 100px" name="tranAmount" id="recordExternalPaymentForm_amount" value="" class="input-width"></td>
            </tr>
        </table>
    </form>
    <p>
        <strong>Open balance due: <?php echo $open_balance . ' ' . $currency_short ;?> </strong>
        <br>balance current month: &euro; <?php echo $open_balance_this_month . ' ' . $currency_short; ?>
    </p>
</div>
<div class="clear-height"></div>
<div class="hide">
    <input type="hidden" id="createDirectChargeWithoutInvoice_customer_id" value="<?php echo $customer_id;?>" />
</div>
<script type="text/javascript">
$(document).ready( function() {
	
});
</script>