<style>
    .input-width {
        border: 1px solid #cccccc;
        border-radius: 3px;
        box-shadow: 0 1px 0 #eeeeee inset, 0 1px 0 #ffffff;
        font-size: 13px;
        margin: 0;
        padding: 5px;
        width: 250px;
    }
    #addMutilPostboxForm table th, td {
        line-height: 16px;
        padding: 5px 0.5em;
        vertical-align: top;
    }
    
</style>
<div id="addMutilPostboxForm">
    <h2 style="font-size: 14px; padding: 0 0 10px 10px; font-weight: bold;">Please confirm the locations you want to add to your account:
    </h2>
    
    <div style="overflow: auto; height: 315px; width: 100%">
        <table  style="width: 100%;border: none; padding: 5px;" >
            <tr>
                <td style="text-align: left;width:15px;">
                    
                </td>
                <td style="text-align: left; font-weight: bold;width:150px;">Location</td>
                <td style="text-align: left;  font-weight: bold;width:50px;">
                    Type
                    <span class="managetables-icon icon_help tipsy_tooltip" data-tooltip="invoicing_address_tipsy_tooltip" title="Only at your primary location you can have AS YOU GO or PRIVATE postboxes. Every other location must be a BUSINESS postbox."></span>
                </td>
                <td style="text-align: right;  font-weight: bold;width:100px;">
                    Price
                    <span class="managetables-icon icon_help tipsy_tooltip" data-tooltip="invoicing_address_tipsy_tooltip" title="The price is calculated as partial amount for the remaining days in this month."></span>
                </td>
            </tr>
            <?php $total = 0; ?>
            <?php foreach ($locations as $location) { ?>
            <tr>
                <td style="text-align: left;">
                    <input type="checkbox" id="addMutilPostboxForm_checked_<?php echo $location->id;?>"
                          <?php if ($location->selected) { ?> checked="checked" <?php } ?>
                          class="addMutilPostboxForm_select_postbox" value="<?php echo $location->id;?>"/>
                </td>
                <td style="text-align: left;width: 250px;"><?php echo $location->location_name?><?php echo ($location->only_express_shipping_flag == "1") ? " <span>(only express shipping)</span>":"";  ?></td>
                <td style="text-align: left;">
                    <?php if ($primary_location_id == $location->id) { ?>
                        <?php echo code_master_form_dropdown(array(
                            "code" => APConstants::ACCOUNT_TYPE,
                            "value" => '3',
                            "name" => 'account_type',
                            "id"    => 'addMutilPostboxForm_account_type',
                            "clazz" => 'input-width',
                            "style" => 'width: 130px',
                            "has_empty" => false
                        ));?>
                    <?php } else {?>
                        BUSINESS
                    <?php }?>
                </td>
                <td style="text-align: right;">
                    <?php echo APUtils::number_format($location->price,2)?> EUR
                    <input type="hidden" id="addMutilPostboxForm_hidden_price_<?php echo $location->id;?>" 
                           value="<?php echo number_format($location->price, 2); ?>">
                </td>
                <?php $total += floatval(number_format($location->price, 2));?>
            </tr>
            <?php } ?>
            
        </table>
    </div>
    <div>
        <table  style="width: 100%;border: none; padding: 5px;">
            <tr>
                <td style="text-align: left;width:15px;">&nbsp;</td>
                <td style="text-align: left;width:150px;">&nbsp;</td>
                <td style="text-align: right; font-weight: bold;width:60px;">Total:</td>
                <td style="text-align: right; font-weight: bold;width:100px; padding-right: 20px;"><span id="addMutilPostboxForm_totalAmount"><?php echo APUtils::number_format(0,2).' EUR'?></span></td>
            </tr>
            <tr>
                <td colspan="4" style="text-align: center">
                    <button id="addMutilPostboxForm_confirmButton" style="width: 100px;">Confirm</button>
                </td>
            </tr>
        </table>
        
    </div>
    <div style="padding: 0 0 10px 10px; margin-top: 10px;">
        The charge is partial for the rest of this month. 
        Contracts are monthly and postboxes can be deleted separately from each other also monthly. 
        <a href="https://www.clevvermail.com/terms-and-conditions.html" target="_bank" style="text-decoration: underline; color: blue">Our Terms & Conditions </a> apply. 
        <a href="https://www.clevvermail.com/pricing.html" target="_bank" style="text-decoration: underline; color: blue">See full location pricing…</a>
    </div>
    <input type="hidden" id="addMutilPostboxForm_amount" name="type" value="" /> 
</div>
<script type="text/javascript">
$(document).ready(function () {
    $('#addMutilPostboxForm .tipsy_tooltip').tipsy({gravity: 'sw'});
    $("#addMutilPostboxForm .tipsy_tooltip" ).each(function( index ) {
            $(this).tipsy("show");
    });
    setTimeout(function() {
            $("#addMutilPostboxForm .tipsy_tooltip" ).each(function( index ) {
                    $(this).tipsy("hide");
            });
    },2000);
    $('#addMutilPostboxForm_confirmButton').button();
    changeSelectedCheckedBox();
    
    $('.addMutilPostboxForm_select_postbox').change(function() {
        changeSelectedCheckedBox();
    });
    
    function changeSelectedCheckedBox() {
        var listSelectedLocationId = [];
        var totalSelectedCost = 0;
        // For each item in selected postbox
        $('.addMutilPostboxForm_select_postbox').each(function () {
            var location_id = $(this).val();
            if($(this).is(':checked')) {
                listSelectedLocationId.push(location_id);
                totalSelectedCost += parseFloat($('#addMutilPostboxForm_hidden_price_' + location_id).val());
            }
        });
        $('#addMutilPostboxForm_totalAmount').html(totalSelectedCost.toFixed(2).toString().replace('.', ',') + ' EUR');
    }
    
    // When user click to confirm button
    $('#addMutilPostboxForm_confirmButton').live('click', function() {
        var listSelectedLocationId = [];
        var totalSelectedCost = 0;
        // For each item in selected postbox
        $('.addMutilPostboxForm_select_postbox').each(function () {
            var location_id = $(this).val();
            if($(this).is(':checked')) {
                listSelectedLocationId.push(location_id);
                totalSelectedCost += parseFloat($('#addMutilPostboxForm_hidden_price_' + location_id).val());
            }
        });
        $('#addMutilPostboxForm_totalAmount').html(totalSelectedCost.toFixed(2).toString().replace('.', ',') + ' EUR');
        var totalCost = parseFloat($('#h_prepaymentForm_total_cost').val());
        $('#prepayment_add_more_postbox').html(totalSelectedCost.toFixed(2).toString().replace('.', ',') + ' EUR');
        $('#prepaymentForm_total_cost_1').html((totalSelectedCost + totalCost).toFixed(2).toString().replace('.', ',') + ' EUR');
        $('#prepaymentForm_total_cost_2').html((totalSelectedCost + totalCost).toFixed(2).toString().replace('.', ',') + ' EUR');
        $('#h_prepaymentForm_total_amount').val((totalSelectedCost + totalCost));
        
        var rate = parseFloat($('#h_prepaymentForm_currency_rate').val());
        var total_other_currency = (totalSelectedCost + totalCost) * rate;
        $('#prepaymentForm_total_cost_other_1').html(total_other_currency.toFixed(2).toString().replace('.', ','));
        $('#prepaymentForm_total_cost_other_2').html(total_other_currency.toFixed(2).toString().replace('.', ','));
        
        $('#h_prepaymentForm_list_add_more_location_id').val(listSelectedLocationId.toString());
        $('#prepaymentForm_AddMoreBusinessPostbox').dialog('close');
        return false;
    });
});
</script>