<?php
$account_type = $account->account_type;
$currency_id = $selected_currency->currency_id;
$currency_rate = $selected_currency->currency_rate;
$currency_short = $selected_currency->currency_short;
?>
<div class="button_container" style="margin-top: 10px; margin-left: 10px; margin-bottom: 20px;">
    <b>
        All prices exclude VAT, if you have an EU VAT number you can enter it in your account setting<br/>
    </b>

    <form id="priceSearchForm" method="get" action="<?php echo base_url() ?>account/setting/price">
        Location: <?php
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
        ?>
    </form>
    <form id="priceSettingForm" method="post" action="<?php echo base_url() ?>account/setting/price">
        <div class="input-form">
            <table class="priceSettingFormTable" style="width: 1080px">
                <tr style="background: rgb(68,84,106);">
                    <td style="width: 500px;">&nbsp;</td>
                    <th style="width: 200px; color: #FFFFFF">ClevverMail Enterprise Prices (<?php echo $currency_short; ?>)</th>
                    <th style="width: 160px; color: #FFFFFF">Dimension</th>
                    <th style="width: 160px; color: #FFFFFF">Your upcharge/earning</th>
                    <th style="width: 160px; color: #FFFFFF">End price</th>
                </tr>
                <tr>
                    <?php $postbox_fee = AccountSetting::get_alias01($customer_id, "postbox_fee", $location_id); ?>
                    <td>Postbox Fee</td>
                    <td class="pricing"><?php echo APUtils::convert_currency($pricing_map[5]['postbox_fee']->item_value, $currency_rate, 2, $decimal_separator); ?></td>
                    <td><?php echo $currency_short; ?>/month</td>
                    <td ><input type="text" class="input-width upcharges input-number" style="width: 100px"
                                name="postbox_fee" value="<?php echo APUtils::convert_currency($postbox_fee, $currency_rate, 2, $decimal_separator); ?>" /></td>
                    <td class="end-price"><?php echo APUtils::convert_currency($pricing_map[5]['postbox_fee']->item_value + $postbox_fee, $currency_rate, 2, $decimal_separator); ?></td>
                </tr>

                <tr style="background: rgb(217,217,217);">
                    <th>Included Feature</th>
                    <td >&nbsp;</td>
                    <td >&nbsp;</td>
                    <td >&nbsp;</td>
                    <td >&nbsp;</td>
                </tr>
                <tr>
                    <td>Included incoming items</td>
                    <td ><?php echo $pricing_map[5]['included_incomming_items']->item_value; ?></td>
                    <td>pieces</td>
                    <td >&nbsp;</td>
                    <td ><?php echo $pricing_map[5]['included_incomming_items']->item_value; ?></td>
                </tr>
                <tr>
                    <td>Hand sorting of advertising</td>
                    <td ><?php echo $pricing_map[5]['hand_sorting_of_advertising']->item_value; ?></td>
                    <td>no/yes</td>
                    <td >&nbsp;</td>
                    <td ><?php echo $pricing_map[5]['hand_sorting_of_advertising']->item_value; ?></td>
                </tr>
                <tr>
                    <td>Envelope scanning (front)</td>
                    <td ><?php echo $pricing_map[5]['envelope_scanning_front']->item_value; ?></td>
                    <td>pieces</td>
                    <td >&nbsp;</td>
                    <td ><?php echo $pricing_map[5]['envelope_scanning_front']->item_value; ?></td>
                </tr>
                <tr>
                    <td>Item scan</td>
                    <td ><?php echo $pricing_map[5]['included_opening_scanning']->item_value; ?></td>
                    <td>pieces</td>
                    <td >&nbsp;</td>
                    <td ><?php echo $pricing_map[5]['included_opening_scanning']->item_value; ?></td>
                </tr>
                <tr>
                    <td>Storing items free period (letters)</td>
                    <td ><?php echo $pricing_map[5]['storing_items_letters']->item_value; ?></td>
                    <td>days</td>
                    <td >&nbsp;</td>
                    <td ><?php echo $pricing_map[5]['storing_items_letters']->item_value; ?></td>
                </tr>
                <tr>
                    <td>Storing items free period (packages)</td>
                    <td ><?php echo $pricing_map[5]['storing_items_packages']->item_value; ?></td>
                    <td>days</td>
                    <td >&nbsp;</td>
                    <td ><?php echo $pricing_map[5]['storing_items_packages']->item_value; ?></td>
                </tr>
                <tr style="background: rgb(217,217,217);">
                    <th>Additional Activities</th>
                    <td >&nbsp;</td>
                    <td >&nbsp;</td>
                    <td >&nbsp;</td>
                    <td >&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <?php  $additional_incomming_items = AccountSetting::get_alias01($customer_id, "additional_incomming_items", $location_id); ?>
                    <td>Additional incoming items</td>
                    <td class="pricing"><?php echo APUtils::convert_currency($pricing_map[5]['additional_incomming_items']->item_value, $currency_rate, 2, $decimal_separator); ?></td>
                    <td><?php echo $currency_short; ?></td>
                    <td ><input type="text" class="input-width upcharges" style="width: 100px"
                                name="additional_incomming_items" value="<?php echo APUtils::convert_currency($additional_incomming_items, $currency_rate, 2, $decimal_separator); ?>" /></td>
                    <td  class="end-price"><?php echo APUtils::convert_currency($pricing_map[5]['additional_incomming_items']->item_value + $additional_incomming_items, $currency_rate, 2, $decimal_separator); ?></td>
                </tr>
                <tr>
                    <?php $envelope_scanning = AccountSetting::get_alias01($customer_id, "envelop_scanning", $location_id); ?>
                    <td>Envelope scanning</td>
                    <td class="pricing"><?php echo APUtils::convert_currency($pricing_map[5]['envelop_scanning']->item_value, $currency_rate, 2, $decimal_separator); ?></td>
                    <td><?php echo $currency_short; ?></td>
                    <td ><input type="text" class="input-width upcharges" style="width: 100px"
                                name="envelop_scanning" value="<?php echo APUtils::convert_currency($envelope_scanning, $currency_rate, 2, $decimal_separator); ?>" /></td>
                    <td  class="end-price"><?php echo APUtils::convert_currency($pricing_map[5]['envelop_scanning']->item_value + $envelope_scanning, $currency_rate, 2, $decimal_separator); ?></td>
                </tr>
                <tr>
                    <?php $opening_and_scanning = AccountSetting::get_alias01($customer_id, "opening_scanning", $location_id); ?>
                    <td>Opening and scanning</td>
                    <td class="pricing"><?php echo APUtils::convert_currency($pricing_map[5]['opening_scanning']->item_value, $currency_rate, 2, $decimal_separator); ?></td>
                    <td><?php echo $currency_short; ?></td>
                    <td ><input type="text" class="input-width upcharges" style="width: 100px"
                                name="opening_scanning" value="<?php echo APUtils::convert_currency($opening_and_scanning, $currency_rate, 2, $decimal_separator) ?>" /></td>
                    <td  class="end-price"><?php echo APUtils::convert_currency($pricing_map[5]['opening_scanning']->item_value + $opening_and_scanning, $currency_rate, 2, $decimal_separator); ?></td>
                </tr>
                <tr>
                    <?php $direct_forwarding_fee = AccountSetting::get_alias01($customer_id, "send_out_directly", $location_id); ?>
                    <td>Direct forwarding fee (charge per incident)</td>
                    <td class="pricing"><?php echo APUtils::convert_currency($pricing_map[5]['send_out_directly']->item_value, $currency_rate, 2, $decimal_separator); ?></td>
                    <td><?php echo $currency_short; ?></td>
                    <td ><input type="text" class="input-width upcharges" style="width: 100px"
                                name="send_out_directly" value="<?php echo APUtils::convert_currency($direct_forwarding_fee, $currency_rate, 2, $decimal_separator) ?>" /></td>
                    <td  class="end-price"><?php echo APUtils::convert_currency($pricing_map[5]['send_out_directly']->item_value + $direct_forwarding_fee, $currency_rate, 2, $decimal_separator); ?></td>
                </tr>
                <tr>
                    <?php $postal_charge = AccountSetting::get_alias01($customer_id, "shipping_plus", $location_id); ?>
                    <td>Direct forwarding fee (charge based on postal charge)</td>
                    <td class="pricing"><?php echo $pricing_map[5]['shipping_plus']->item_value; ?><?php echo $pricing_map[5]['shipping_plus']->item_unit; ?></td>
                    <td>percentage</td>
                    <td ><input type="text" class="input-width upcharges" style="width: 100px"
                                name="shipping_plus" value="<?php echo APUtils::convert_currency($postal_charge, $currency_rate, 2, $decimal_separator); ?>" /></td>
                    <td  class="end-price"><?php echo APUtils::convert_currency($pricing_map[5]['shipping_plus']->item_value + $postal_charge, $currency_rate, 2, $decimal_separator); ?></td>
                </tr>
                <tr>
                    <?php $collect_forwarding = AccountSetting::get_alias01($customer_id, "send_out_collected", $location_id);?>
                    <td>Collect forwarding(charge per incident)</td>
                    <td class="pricing"><?php echo APUtils::convert_currency($pricing_map[5]['send_out_collected']->item_value, $currency_rate, 2, $decimal_separator); ?></td>
                    <td><?php echo $currency_short; ?></td>
                    <td ><input type="text" class="input-width upcharges" style="width: 100px"
                                name="send_out_collected" value="<?php echo APUtils::convert_currency($collect_forwarding, $currency_rate, 2, $decimal_separator); ?>" /></td>
                    <td  class="end-price"><?php echo APUtils::convert_currency($pricing_map[5]['shipping_plus']->item_value + $collect_forwarding, $currency_rate, 2, $decimal_separator); ?></td>
                </tr>
                <tr>
                    <?php $colect_shipping_postal_charge = AccountSetting::get_alias01($customer_id, "collect_shipping_plus", $location_id); ?>
                    <td>Collect forwarding (charge based on postal charge)</td>
                    <td class="pricing"><?php echo $pricing_map[5]['collect_shipping_plus']->item_value; ?><?php echo $pricing_map[5]['collect_shipping_plus']->item_unit; ?></td>
                    <td>percentage</td>
                    <td ><input type="text" class="input-width upcharges" style="width: 100px"
                                name="collect_shipping_plus" value="<?php echo APUtils::convert_currency($colect_shipping_postal_charge, $currency_rate, 2, $decimal_separator); ?>" /></td>
                    <td  class="end-price"><?php echo APUtils::convert_currency($pricing_map[5]['collect_shipping_plus']->item_value + $colect_shipping_postal_charge, $currency_rate, 2, $decimal_separator); ?></td>
                </tr>
                <tr>
                    <?php $storing_letter_fee = AccountSetting::get_alias01($customer_id, "storing_items_over_free_letter", $location_id); ?>
                    <td>Storing items over free period (letters)</td>
                    <td class="pricing"><?php echo APUtils::convert_currency($pricing_map[5]['storing_items_over_free_letter']->item_value, $currency_rate, 2, $decimal_separator); ?></td>
                    <td><?php echo $currency_short; ?>/day</td>
                    <td ><input type="text" class="input-width upcharges" style="width: 100px" name="storing_items_over_free_letter"
                                value="<?php echo APUtils::convert_currency($storing_letter_fee, $currency_rate, 2, $decimal_separator) ?>" /></td>
                    <td  class="end-price"><?php echo APUtils::convert_currency($pricing_map[5]['storing_items_over_free_letter']->item_value + $storing_letter_fee, $currency_rate, 2, $decimal_separator); ?></td>
                </tr>
                <tr>
                    <?php $storing_package_fee = AccountSetting::get_alias01($customer_id, "storing_items_over_free_packages", $location_id); ?>
                    <td>Storing items over free period (packages)</td>
                    <td class="pricing"><?php echo APUtils::convert_currency($pricing_map[5]['storing_items_over_free_packages']->item_value, $currency_rate, 2, $decimal_separator); ?></td>
                    <td><?php echo $currency_short; ?>/day</td>
                    <td ><input type="text" class="input-width" style="width: 100px" name="storing_items_over_free_packages"
                                value="<?php echo APUtils::convert_currency($storing_package_fee, $currency_rate, 2, $decimal_separator); ?>" /></td>
                    <td  class="end-price"><?php echo APUtils::convert_currency($pricing_map[5]['storing_items_over_free_packages']->item_value + $storing_package_fee, $currency_rate, 2, $decimal_separator); ?></td>
                </tr>
                <tr>
                    <td>Paypal transaction fee</td>
                    <td class="pricing"><?php echo APUtils::number_format($pricing_map[5]['paypal_transaction_fee']->item_value, 2, $decimal_separator); ?><?php echo $pricing_map[5]['paypal_transaction_fee']->item_unit; ?></td>
                    <td>percentage</td>
                    <td>&nbsp;</td>
                    <td ><?php echo APUtils::number_format($pricing_map[5]['paypal_transaction_fee']->item_value, 2, $decimal_separator); ?><?php echo $pricing_map[5]['paypal_transaction_fee']->item_unit; ?></td>
                </tr>
                <tr>
                    <?php $included_page = AccountSetting::get_alias01($customer_id, "additional_included_page_opening_scanning", $location_id); ?>
                    <td>Included pages for opening and scanning</td>
                    <td class="pricing"><?php echo $pricing_map[5]['additional_included_page_opening_scanning']->item_value; ?></td>
                    <td>pieces</td>
                    <td ><input type="text" class="input-width upcharges" style="width: 100px" name="additional_included_page_opening_scanning"
                                value="<?php echo APUtils::convert_currency($included_page, $currency_rate, 2, $decimal_separator) ?>" /></td>
                    <td  class="end-price"><?php echo APUtils::convert_currency($pricing_map[5]['additional_included_page_opening_scanning']->item_value, $currency_rate, 2, $decimal_separator); ?></td>
                </tr>
                <tr>
                    <?php $custom_outgoing_01 = AccountSetting::get_alias01($customer_id, "custom_declaration_outgoing_01", $location_id); ?>
                    <td>Customs documents outgoing (value &gt; <?php echo APUtils::number_format(1000 * $currency_rate, 0) . ' ' . $currency_short ?>)</td>
                    <td class="pricing"><?php echo APUtils::convert_currency($pricing_map[5]['custom_declaration_outgoing_01']->item_value, $currency_rate, 2, $decimal_separator); ?></td>
                    <td><?php echo $currency_short; ?></td>
                    <td ><input type="text" class="input-width upcharges" style="width: 100px" name="custom_declaration_outgoing_01"
                                value="<?php echo APUtils::convert_currency($custom_outgoing_01, $currency_rate, 2, $decimal_separator); ?>" /></td>
                    <td  class="end-price"><?php echo APUtils::convert_currency($pricing_map[5]['custom_declaration_outgoing_01']->item_value, $currency_rate, 2, $decimal_separator); ?></td>
                </tr>
                <tr>
                    <?php $custom_outgoing_02 = AccountSetting::get_alias01($customer_id, "custom_declaration_outgoing_02", $location_id); ?>
                    <td>Customs documents outgoing (value &lt; <?php echo APUtils::number_format(1000 * $currency_rate, 0) . ' ' . $currency_short ?>)</td>
                    <td class="pricing"><?php echo APUtils::convert_currency($pricing_map[5]['custom_declaration_outgoing_02']->item_value, $currency_rate, 2, $decimal_separator); ?></td>
                    <td><?php echo $currency_short; ?></td>
                    <td ><input type="text" class="input-width upcharges" style="width: 100px" name="custom_declaration_outgoing_02"
                                value="<?php echo APUtils::convert_currency($custom_outgoing_02, $currency_rate, 2, $decimal_separator); ?>" /></td>
                    <td  class="end-price"><?php echo APUtils::convert_currency($pricing_map[5]['custom_declaration_outgoing_02']->item_value, $currency_rate, 2, $decimal_separator); ?></td>
                </tr>
                <tr>
                    <?php $custom_handling = AccountSetting::get_alias01($customer_id, "custom_handling_import", $location_id); ?>
                    <td>Customs handling import</td>
                    <td class="pricing"><?php echo $pricing_map[5]['custom_handling_import']->item_value; ?><?php echo $pricing_map[5]['custom_handling_import']->item_unit; ?></td>
                    <td>percentage on occuring cost</td>
                    <td ><input type="text" class="input-width upcharges" style="width: 100px" name="custom_handling_import"
                                value="<?php echo APUtils::convert_currency($custom_handling, $currency_rate, 2, $decimal_separator); ?>" /></td>
                    <td class="end-price"><?php echo APUtils::convert_currency($pricing_map[5]['custom_handling_import']->item_value, $currency_rate, 2, $decimal_separator); ?></td>
                </tr>
                <!--<tr>
                    <td>Cash payment for item on delivery or cash expenditure (percentage)</td>
                    <td <?php if ($account_type == '1') { ?> class="cell_red"  <?php } ?>><?php echo $pricing_map[1]['cash_payment_on_delivery_percentage']->item_value; ?><?php echo $pricing_map[1]['cash_payment_on_delivery_percentage']->item_unit; ?></td>
                    <td ><?php echo $pricing_map[2]['cash_payment_on_delivery_percentage']->item_value; ?><?php echo $pricing_map[2]['cash_payment_on_delivery_percentage']->item_unit; ?></td>
                    <td ><?php echo $pricing_map[3]['cash_payment_on_delivery_percentage']->item_value; ?><?php echo $pricing_map[3]['cash_payment_on_delivery_percentage']->item_unit; ?></td>
                    <td ><?php echo $pricing_map[5]['cash_payment_on_delivery_percentage']->item_value; ?><?php echo $pricing_map[5]['cash_payment_on_delivery_percentage']->item_unit; ?></td>
                    <td>percentage</td>
                </tr>
                <tr>
                    <td>Cash payment for item on delivery or cash expenditure (minimum cost)</td>
                    <td <?php if ($account_type == '1') { ?> class="cell_red"  <?php } ?>><?php echo APUtils::convert_currency($pricing_map[1]['cash_payment_on_delivery_mini_cost']->item_value, $currency_rate, 2, $decimal_separator); ?></td>
                    <td ><?php echo APUtils::convert_currency($pricing_map[2]['cash_payment_on_delivery_mini_cost']->item_value, $currency_rate, 2, $decimal_separator); ?></td>
                    <td ><?php echo APUtils::convert_currency($pricing_map[3]['cash_payment_on_delivery_mini_cost']->item_value, $currency_rate, 2, $decimal_separator); ?></td>
                    <td ><?php echo APUtils::convert_currency($pricing_map[5]['cash_payment_on_delivery_mini_cost']->item_value, $currency_rate, 2, $decimal_separator); ?></td>
                    <td><?php echo $currency_short; ?></td>
                </tr>
                <tr>
                    <td>Pickup charge (only with confirmed appointment)</td>
                    <td ><?php echo APUtils::convert_currency($pricing_map[1]['pickup_charge']->item_value * $currency_rate, $currency_rate, 2, $decimal_separator); ?></td>
                    <td ><?php echo APUtils::convert_currency($pricing_map[2]['pickup_charge']->item_value * $currency_rate, $currency_rate, 2, $decimal_separator); ?></td>
                    <td ><?php echo APUtils::convert_currency($pricing_map[3]['pickup_charge']->item_value * $currency_rate, $currency_rate, 2, $decimal_separator); ?></td>
                    <td ><?php echo APUtils::convert_currency($pricing_map[5]['pickup_charge']->item_value * $currency_rate, $currency_rate, 2, $decimal_separator); ?></td>
                    <td><?php echo $currency_short; ?></td>
                </tr>
                <tr>
                    <td>Scan of additional pages</td>
                    <td ><?php echo APUtils::convert_currency($pricing_map[1]['additional_pages_scanning_price']->item_value, $currency_rate, 2, $decimal_separator); ?></td>
                    <td ><?php echo APUtils::convert_currency($pricing_map[2]['additional_pages_scanning_price']->item_value, $currency_rate, 2, $decimal_separator); ?></td>
                    <td ><?php echo APUtils::convert_currency($pricing_map[3]['additional_pages_scanning_price']->item_value, $currency_rate, 2, $decimal_separator); ?></td>
                    <td ><?php echo APUtils::convert_currency($pricing_map[5]['additional_pages_scanning_price']->item_value, $currency_rate, 2, $decimal_separator); ?></td>
                    <td><?php echo $currency_short; ?></td>
                </tr>
                <tr>
                    <td>Special requests, charged by time</td>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" style="border-bottom: 2px solid red;"  <?php } ?>><?php echo APUtils::convert_currency($pricing_map[1]['special_requests_charge_by_time']->item_value, $currency_rate, 2, $decimal_separator); ?></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" style="border-bottom: 2px solid red;" <?php } ?>><?php echo APUtils::convert_currency($pricing_map[2]['special_requests_charge_by_time']->item_value, $currency_rate, 2, $decimal_separator); ?></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" style="border-bottom: 2px solid red;" <?php } ?>><?php echo APUtils::convert_currency($pricing_map[3]['special_requests_charge_by_time']->item_value, $currency_rate, 2, $decimal_separator); ?></td>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" style="border-bottom: 2px solid red;" <?php } ?>><?php echo APUtils::convert_currency($pricing_map[5]['special_requests_charge_by_time']->item_value, $currency_rate, 2, $decimal_separator); ?></td>
                    <td><?php echo $currency_short; ?>/hour</td>
                </tr>-->
            </table>
            <input type="hidden" id="h_location_id" name="h_location_id" value="<?php echo $location_id; ?>" />
            <div>
                <button class="input-btn  btn-yellow" id="savePrice" type="button">Use this pricing for all locations</button> 
                <button class="input-btn  btn-yellow" id="savePriceLocation" type="button" style="margin-left: 10px;">Save for this location</button>
            </div>
        </div>
    </form>
</div>
<div class="clear-height"></div>

<script type="text/javascript">
    $(document).ready(function () {
        
        var decimal_separtor = '<?php echo $decimal_separator; ?>';
        var digit_separator = ".";
        if(decimal_separtor == '.'){
            digit_separator = ",";
        }
            
        $("#submitButton").button().click(function () {
            $('#priceSettingForm').submit();
            return false;
        });

        $("#location_id").live("change", function () {
            $("#priceSearchForm").submit();
            return false;
        });

        $("#currency_id").live("change", function () {
            $("#priceSearchForm").submit();
            return false;
        });
        
        $("#savePrice").click(function(){
            $("#h_location_id").val($("#location_id").val());
            
            $.ajaxSubmit({
                url: '<?php echo base_url() ?>account/setting/price',
                formId: 'priceSettingForm',
                success: function(data) {
                    if (data.status) {
                        $.displayInfor(data.message);
                    } else {
                        $.displayError(data.message);
                    }
                }
            });
            return false;
        });
        
        $("#savePriceLocation").click(function(){
            $("#h_location_id").val('all');
            
            $.ajaxSubmit({
                url: '<?php echo base_url() ?>account/setting/price',
                formId: 'priceSettingForm',
                success: function(data) {
                    if (data.status) {
                        $.displayInfor(data.message);
                    } else {
                        $.displayError(data.message);
                    }
                }
            });
            return false;
        });
        
        $(".upcharges").change(function(){
            var upcharge = $(this).val();
            upcharge = upcharge.replace('%', '').replace(',', '.');
            var parent = $(this).parent().parent();
            var price = parent.find('.pricing').html();
            price = price.replace("%", '').replace(',', '.');
            
            var end_price = (parseFloat(upcharge) + parseFloat(price)).toFixed(2);
            parent.find('.end-price').html(numberWithCommas(end_price));
        });

        function numberWithCommas(x) {
            var parts = x.toString().split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, digit_separator);
            return parts.join(decimal_separtor);
        }
    });
</script>