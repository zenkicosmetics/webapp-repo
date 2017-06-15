<?php Asset::css('jquery-ui-1.8.20.custom.css'); ?>
<?php Asset::css('Aristo.css'); ?>
<?php Asset::css('styles.css'); ?>

<?php Asset::js('jquery-1.7.2.min.js'); ?>
<?php Asset::js('jquery.blockUI.js'); ?>
<?php Asset::js('jquery-ui-1.8.20.custom.min.js'); ?>
<?php Asset::js('jquery.common.js'); ?>


<h2 style="font-size: 20px; color: blue;"><?php language_e('cases_view_payment_payone_DoYouWantToMakeAPayment', ['amount' => $amount]); ?></h2>
<form id="addEditPaymentMethodForm" method="post">

    <table>
        <tr>
            <td><label><?php language_e('cases_view_payment_payone_Cardtype'); ?></label></td>
            <td><select id="addEditPaymentMethod_card_type" name="card_type" class="input-txt" style="line-height: 24px;">
                    <option value="V" <?php if (!empty($payment) && $payment->card_type == 'V') {?> selected="selected" <?php }?>>VISA</option>
                    <option value="M" <?php if (!empty($payment) && $payment->card_type == 'M') {?> selected="selected" <?php }?>>MasterCard</option>
                    <option value="J" <?php if (!empty($payment) && $payment->card_type == 'J') {?> selected="selected" <?php }?>>JCB</option>
            </select></td>
        </tr>
        <tr>
            <td><label><?php language_e('cases_view_payment_payone_CardNumber'); ?></label></td>
            <td><input type="text" id="addEditPaymentMethod_card_number" name="card_number" value="" class="input-txt"
                maxlength=255 /></td>
        </tr>
        <tr>
            <td><label><?php language_e('cases_view_payment_payone_NameOfCardholder'); ?></label></td>
            <td><input type="text" id="addEditPaymentMethod_card_name" name="card_name" value="" class="input-txt"
                maxlength=255 /></td>
        </tr>
        <tr>
            <td><label><?php language_e('cases_view_payment_payone_ExpiredDate'); ?></label></td>
            <td><?php language_e('cases_view_payment_payone_Year'); ?> <select id="addEditPaymentMethod_expired_year" name="expired_year" class="input-txt"
                style="line-height: 24px; width: 75px">
                    <?php $cur_date = now();?>
                    <?php
                    for($i = $cur_date; $i < $cur_date + 20 * (365 * 24 * 60 * 60); $i = $i + (365 * 24 * 60 * 60)) {
                        $val_year = date ( 'y', $i );
                    ?>
                    <option value="<?php echo $val_year?>"><?php echo date('Y', $i);?></option>
                    <?php }?>
                </select> <?php language_e('cases_view_payment_payone_Month'); ?> <select id="addEditPaymentMethod_expired_month" name="expired_month" class="input-txt"
                style="line-height: 24px; width: 75px">
                    <?php
                        for($i = 1; $i < 13; $i ++) {
                            $val_temp = $i;
                            if ($i < 10) {
                                $val_temp = '0' . $i;
                            }
                        ?>
                    <option value="<?php echo $val_temp;?>"><?php echo $i?></option>
                    <?php }?>
                </select>
            </td>
        </tr>
        <tr>
            <td><label><?php language_e('cases_view_payment_payone_CVCCVV'); ?></label></td>
            <td><input type="text" id="addEditPaymentMethod_cvc" name="cvc" value="" class="input-txt"
                maxlength=4 /></td>
        </tr>
    </table>
    <input type="hidden" id="addEditPaymentMethod_pseudocardpan" name="pseudocardpan" value="" /> <input type="hidden"
        id="addEditPaymentMethod_truncatedcardpan" name="truncatedcardpan" value="" />
</form>
<script type="text/javascript">
$(document).ready(function($){
    $('.tipsy_tooltip').tipsy({gravity: 'sw'});
    $(".tipsy_tooltip" ).each(function( index ) {
        $(this).tipsy("show");
    });
    setTimeout(function() {
        $(".tipsy_tooltip" ).each(function( index ) {
            $(this).tipsy("hide");
        });
    },2000);

    $('#trustwaveSealImage').live('click', function(){
        javascript:window.open('https://sealserver.trustwave.com/cert.php?customerId=9b197218d48f4172a58c080d0e223214&size=105x54&style=invert', 'c_TW', 'location=no, toolbar=no, resizable=yes, scrollbars=yes, directories=no, status=no, width=615, height=720');
        return false;
    });
});
</script>