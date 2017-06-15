<?php
$readonly = 'readonly="readonly"';
$readonlyClass = "readonly";

$account_type = $account->account_type;
if ($highline && $account_type != APConstants::ENTERPRISE_CUSTOMER) {
    $account_type = 3;
}
?>
<style>
    input.input-txt-none, select.input-txt-none{
        width: 100%;
        margin-top: 0px;
        line-height:  20px;
    }
</style>
<div class="button_container" style="margin-top: 10px; margin-left: 10px; margin-bottom: 20px;">
    <b>
        All prices exclude VAT, if you have an EU VAT number you can enter it in your account setting<br/>
    </b>
    <br/>
    <?php include 'system/virtualpost/modules/addresses/views/admin/price_partial.php';?>

</div>
<div class="clear-height"></div>

<script type="text/javascript">

</script>