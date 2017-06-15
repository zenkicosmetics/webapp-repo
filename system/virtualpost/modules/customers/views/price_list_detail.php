<?php
//$account_type = $account->account_type;
//if ($highline && $account_type != APConstants::ENTERPRISE_CUSTOMER) {
//    $account_type = 3;
//}
$readonly = 'readonly="readonly"';
$readonlyClass = "readonly";
?>
<div class="button_container" style="margin-top: 10px; margin-left: 10px; margin-bottom: 20px;">
    <b>
        All prices exclude VAT, if you have an EU VAT number you can enter it in your account setting<br/>
    </b>
    
    <form id="priceSettingForm" method="post" action="">
        <?php
        if(!empty($is_price_list_detail) && $is_price_list_detail == "1" && !empty($locate)){
            echo my_form_dropdown(array(
                "data" => $locate,
                "value_key" => 'id',
                "label_key" => 'location_name',
                "value" => $locationId,
                "name" => 'location_id',
                "id" => 'location_id',
                "clazz" => 'input-width',
                "style" => 'margin-top:10px; margin-bottom:10px;width: 250px;',
                "has_empty" => false
            ));
        }
        ?>
    </form>
    <div id="priceListDetailDivContainer">
    <?php include 'system/virtualpost/modules/addresses/views/admin/price_partial.php';?>
    </div>
</div>
<div class="clear-height"></div>

<script type="text/javascript">
    $("#location_id").live('change', function(){
        var location_id = $("#location_id").val();
        $.pageBlock();
        $("#priceListDetailDivContainer").load("<?php echo base_url() ?>customers/load_price_list_detail?hide_flag=1&location_id="+location_id+"&type=5", function() {
            $.pageUnblock();
        });
    });
</script>