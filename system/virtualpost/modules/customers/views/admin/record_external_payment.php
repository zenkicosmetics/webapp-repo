<div class="button_container">
    <h1 style="font-size: 18px; font-weight: bold;">Record payment made outside of system:</h1><br/>
    <span>Customer: <?php echo $customer->email;?></span>
    <div class="clear"></div>
    <form action="#" id="recordExternalPaymentForm" class="dialog-form">
        <table style="border: 0px solid #dadada;margin: 5px 0px;width: 400px">
            <tr>
                <td width="100px">Date:</td>
                <td width="200px">Transaction ID:</td>
                <td width="100px">Amount:</td>
            </tr>
            <tr>
                <td><input type="text" style="width: 100px" name="tranDate" id="recordExternalPaymentForm_tranDate" value="" class="input-width datepicker"></td>
                <td><input type="text" style="width: 200px" name="tranId" id="recordExternalPaymentForm_tranId" value="" class="input-width"></td>
                <td><input type="text" style="width: 100px" name="tranAmount" id="recordExternalPaymentForm_amount" value="" class="input-width"></td>
            </tr>
        </table>
    </form>
</div>
<div class="clear-height"></div>
<div class="hide">
    <input type="hidden" id="createDirectCharge_customer_id" value="<?php echo $customer_id;?>" />
</div>
<script type="text/javascript">
$(document).ready( function() {
	var date_format = "<?php echo APConstants::DATEFORMAT_05;//($date_format == APConstants::DATEFORMAT_DEFAULT)?  APConstants::DATEFORMAT_04 : APConstants::DATEFORMAT_05 ?>";
	$(".datepicker").datepicker();
    $(".datepicker").datepicker("option", "dateFormat", date_format);
});
</script>