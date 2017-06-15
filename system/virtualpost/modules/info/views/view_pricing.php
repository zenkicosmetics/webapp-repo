<?php
$account_type = $account->account_type;
$currency_id = $selected_currency->currency_id;
$currency_rate = $selected_currency->currency_rate;
$currency_short = $selected_currency->currency_short;

// Check authenticate.
$readonly = 'readonly="readonly"';
$readonlyClass = "readonly";
?>
<div class="button_container" style="margin-top: 10px; margin-left: 10px; margin-bottom: 20px;">
    <b>
        All prices exclude VAT, if you have an EU VAT number you can enter it in your account setting<br/>
    </b>

    <form id="priceSettingForm" method="post" action="<?php echo base_url() ?>info/view_pricing_inline">
        <?php
        echo my_form_dropdown(array(
            "data" => $list_access_location,
            "value_key" => 'id',
            "label_key" => 'location_name',
            "value" => $location_id,
            "name" => 'location_id',
            "id" => 'location_id',
            "clazz" => 'input-width',
            "style" => 'margin-top:10px',
            "has_empty" => true
        ));

        /*echo my_form_dropdown(array(
            "data" => $list_currencies,
            "value_key" => 'currency_id',
            "label_key" => 'currency_short',
            "value" => $currency_id,
            "name" => 'currency_id',
            "id" => 'currency_id',
            "clazz" => 'input-width',
            "style" => 'margin-top:10px; margin-left: 15px; width: 100px;',
            "has_empty" => false
        ));*/
        ?>
    </form>
    
    <?php include 'system/virtualpost/modules/addresses/views/admin/price_partial.php';?>
</div>
<div class="clear-height"></div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#submitButton").button().click(function () {
            $('#priceSettingForm').submit();
            return false;
        });

        $("#location_id").live("change", function () {
            $("#priceSettingForm").submit();
            return false;
        });

        $("#currency_id").live("change", function () {
            $("#priceSettingForm").submit();
            return false;
        });
    });
</script>