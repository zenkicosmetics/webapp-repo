<div class="button_container" style="margin-top: 10px; margin-left: 10px; margin-bottom: 20px;">
    <b>Your AS YOU GO account is now active. <br/>
    You can change your account type to private or business at any time<br/>
    All prices exclude VAT, if you have a EU VAT number you can enter it in your account settings<br/>
    </b>
	<form id="priceSettingForm" method="post"
		action="<?php echo base_url()?>settings/prices">
		<div class="input-form">
			<table class="priceSettingFormTable" style="width: 1080px">
			    <tr style="background: rgb(68,84,106);">
					<td style="width: 490px;">&nbsp;</td>
					<th class="cell_red" style="width: 160px; border-top: 2px solid red; color: #FFFFFF">AS YOU GO</th>
					<th style="width: 160px;color: #FFFFFF">Private</th>
					<th style="width: 160px;color: #FFFFFF">Business</th>
				</tr>
				<tr>
				    <td>Postbox Fee</td>
					<td class="cell_red"><?php echo APUtils::number_format($pricing_map[1]['postbox_fee']->item_value); ?> (<?php echo $pricing_map[1]['postbox_fee']->item_unit; ?>)</td>
					<td><?php echo APUtils::number_format($pricing_map[2]['postbox_fee']->item_value); ?> (<?php echo $pricing_map[1]['postbox_fee']->item_unit; ?>)</td>
					<td><?php echo APUtils::number_format($pricing_map[3]['postbox_fee']->item_value); ?> (<?php echo $pricing_map[1]['postbox_fee']->item_unit; ?>)</td>
				</tr>
                <tr>
				    <td>AS YOU GO fee</td>
					<td class="cell_red"><?php echo APUtils::number_format($pricing_map[1]['postbox_fee_as_you_go']->item_value); ?> (<?php echo $pricing_map[1]['postbox_fee_as_you_go']->item_unit; ?>)</td>
					<td><?php echo APUtils::number_format($pricing_map[2]['postbox_fee_as_you_go']->item_value); ?> (<?php echo $pricing_map[1]['postbox_fee_as_you_go']->item_unit; ?>)</td>
					<td><?php echo APUtils::number_format($pricing_map[3]['postbox_fee_as_you_go']->item_value); ?> (<?php echo $pricing_map[1]['postbox_fee_as_you_go']->item_unit; ?>)</td>
				</tr>
                <tr>
				    <td>AS YOU GO duration</td>
					<td class="cell_red"><?php echo APUtils::number_format($pricing_map[1]['as_you_go']->item_value); ?> (<?php echo $pricing_map[1]['as_you_go']->item_unit; ?>)</td>
					<td><?php echo APUtils::number_format($pricing_map[2]['as_you_go']->item_value); ?> (<?php echo $pricing_map[1]['as_you_go']->item_unit; ?>)</td>
					<td><?php echo APUtils::number_format($pricing_map[3]['as_you_go']->item_value); ?> (<?php echo $pricing_map[1]['as_you_go']->item_unit; ?>)</td>
				</tr>
                
				<tr style="background: rgb(217,217,217);">
					<th>Included Feature</th>
					<td class="cell_red">&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>Address</td>
					<td class="cell_red"><?php echo $pricing_map[1]['address_number']->item_value; ?></td>
					<td><?php echo $pricing_map[2]['address_number']->item_value; ?></td>
					<td><?php echo $pricing_map[3]['address_number']->item_value; ?></td>
				</tr>
				<tr>
					<td>Included incoming items</td>
					<td class="cell_red"><?php echo $pricing_map[1]['included_incomming_items']->item_value; ?></td>
					<td><?php echo $pricing_map[2]['included_incomming_items']->item_value; ?></td>
					<td><?php echo $pricing_map[3]['included_incomming_items']->item_value; ?></td>
				</tr>
				<tr>
					<td>Storage</td>
					<td class="cell_red">
					    <?php if ($pricing_map[1]['storage']->item_value != 0) { ?>
					    <?php echo $pricing_map[1]['storage']->item_value; ?> (<?php echo $pricing_map[1]['storage']->item_unit; ?>)
					    <?php } else { ?>
					    Unlimited
					    <?php } ?>
					</td>
					<td>
					    <?php if ($pricing_map[2]['storage']->item_value != 0) { ?>
					    <?php echo $pricing_map[2]['storage']->item_value; ?> (<?php echo $pricing_map[2]['storage']->item_unit; ?>)
					    <?php } else { ?>
					    Unlimited
					    <?php } ?>
					</td>
					<td>
					    <?php if ($pricing_map[3]['storage']->item_value != 0) { ?>
					    <?php echo $pricing_map[3]['storage']->item_value; ?> (<?php echo $pricing_map[3]['storage']->item_unit; ?>)
					     <?php } else { ?>
					    Unlimited
					    <?php } ?>
					</td>
				</tr>
				<tr>
					<td>Hand sorting of advertising</td>
					<td class="cell_red"><?php echo $pricing_map[1]['hand_sorting_of_advertising']->item_value; ?></td>
					<td><?php echo $pricing_map[2]['hand_sorting_of_advertising']->item_value; ?></td>
					<td><?php echo $pricing_map[3]['hand_sorting_of_advertising']->item_value; ?></td>
				</tr>
				<tr>
					<td>Envelope scanning (front)</td>
					<td class="cell_red"><?php echo $pricing_map[1]['envelope_scanning_front']->item_value; ?></td>
					<td><?php echo $pricing_map[2]['envelope_scanning_front']->item_value; ?></td>
					<td><?php echo $pricing_map[3]['envelope_scanning_front']->item_value; ?></td>
				</tr>
				<tr>
					<td>Item scan (10 pages, front and back included; additional pages: 10c)</td>
					<td class="cell_red"><?php echo $pricing_map[1]['included_opening_scanning']->item_value; ?></td>
					<td><?php echo $pricing_map[2]['included_opening_scanning']->item_value; ?></td>
					<td><?php echo $pricing_map[3]['included_opening_scanning']->item_value; ?></td>
				</tr>
				<tr>
					<td>Storing items (letters)</td>
					<td class="cell_red"><?php echo $pricing_map[1]['storing_items_letters']->item_value; ?> (<?php echo $pricing_map[1]['storing_items_letters']->item_unit; ?>)</td>
					<td><?php echo $pricing_map[2]['storing_items_letters']->item_value; ?> (<?php echo $pricing_map[2]['storing_items_letters']->item_unit; ?>)</td>
					<td><?php echo $pricing_map[3]['storing_items_letters']->item_value; ?> (<?php echo $pricing_map[3]['storing_items_letters']->item_unit; ?>)</td>
				</tr>
				<tr>
					<td>Storing items (packages)</td>
					<td class="cell_red"><?php echo $pricing_map[1]['storing_items_packages']->item_value; ?> (<?php echo $pricing_map[1]['storing_items_packages']->item_unit; ?>)</td>
					<td><?php echo $pricing_map[2]['storing_items_packages']->item_value; ?> (<?php echo $pricing_map[2]['storing_items_packages']->item_unit; ?>)</td>
					<td><?php echo $pricing_map[3]['storing_items_packages']->item_value; ?> (<?php echo $pricing_map[3]['storing_items_packages']->item_unit; ?>)</td>
				</tr>
				<tr>
				    <td>Storing items digitally</td>
					<td class="cell_red"><?php echo $pricing_map[1]['storing_items_digitally']->item_value; ?> (<?php echo $pricing_map[1]['storing_items_digitally']->item_unit; ?>)</td>
					<td><?php echo $pricing_map[2]['storing_items_digitally']->item_value; ?> (<?php echo $pricing_map[2]['storing_items_digitally']->item_unit; ?>)</td>
					<td><?php echo $pricing_map[3]['storing_items_digitally']->item_value; ?> (<?php echo $pricing_map[3]['storing_items_digitally']->item_unit; ?>)</td>
				</tr>
				<tr>
				    <td>Trashing items</td>
					<td class="cell_red">
					    <?php if ($pricing_map[1]['trashing_items']->item_value != -1) {?>
					    <?php echo $pricing_map[1]['trashing_items']->item_value; ?>
					    <?php } else { ?>
					    Unlimited
					    <?php } ?>
					</td>
					<td>
					    <?php if ($pricing_map[2]['trashing_items']->item_value != -1) {?>
					    <?php echo $pricing_map[2]['trashing_items']->item_value; ?>
					    <?php } else { ?>
					    Unlimited
					    <?php } ?>
					</td>
					<td>
					    <?php if ($pricing_map[3]['trashing_items']->item_value != -1) {?>
					    <?php echo $pricing_map[3]['trashing_items']->item_value; ?>
					    <?php } else { ?>
					    Unlimited
					    <?php } ?>
					</td>
				</tr>
				<tr>
				    <td>Cloud service connection</td>
					<td class="cell_red"><?php echo $pricing_map[1]['cloud_service_connection']->item_value; ?></td>
					<td><?php echo $pricing_map[2]['cloud_service_connection']->item_value; ?></td>
					<td><?php echo $pricing_map[3]['cloud_service_connection']->item_value; ?></td>
				</tr>
				<tr style="background: rgb(217,217,217);">
					<th>Additional Activities</th>
					<td class="cell_red">&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
				    <td>Additional incoming items</td>
					<td class="cell_red"><?php echo APUtils::number_format($pricing_map[1]['additional_incomming_items']->item_value); ?>(<?php echo $pricing_map[1]['additional_incomming_items']->item_unit; ?>)</td>
					<td><?php echo APUtils::number_format($pricing_map[2]['additional_incomming_items']->item_value); ?>(<?php echo $pricing_map[2]['additional_incomming_items']->item_unit; ?>)</td>
					<td><?php echo APUtils::number_format($pricing_map[3]['additional_incomming_items']->item_value); ?>(<?php echo $pricing_map[3]['additional_incomming_items']->item_unit; ?>)</td>
				</tr>
				<tr>
				    <td>Envelope scanning</td>
					<td class="cell_red"><?php echo APUtils::number_format($pricing_map[1]['envelop_scanning']->item_value); ?>(<?php echo $pricing_map[1]['envelop_scanning']->item_unit; ?>)</td>
					<td><?php echo APUtils::number_format($pricing_map[2]['envelop_scanning']->item_value); ?>(<?php echo $pricing_map[2]['envelop_scanning']->item_unit; ?>)</td>
					<td><?php echo APUtils::number_format($pricing_map[3]['envelop_scanning']->item_value); ?>(<?php echo $pricing_map[3]['envelop_scanning']->item_unit; ?>)</td>
				</tr>
				<tr>
				    <td>Opening and scanning (10 pages, front and back included;additional pages: 10c)</td>
					<td class="cell_red"><?php echo APUtils::number_format($pricing_map[1]['opening_scanning']->item_value); ?>(<?php echo $pricing_map[1]['opening_scanning']->item_unit; ?>)</td>
					<td><?php echo APUtils::number_format($pricing_map[2]['opening_scanning']->item_value); ?>(<?php echo $pricing_map[2]['opening_scanning']->item_unit; ?>)</td>
					<td><?php echo APUtils::number_format($pricing_map[3]['opening_scanning']->item_value); ?>(<?php echo $pricing_map[3]['opening_scanning']->item_unit; ?>)</td>
				</tr>
				<tr>
				    <td>Shipping directly to delivery address</td>
					<td class="cell_red">
					    <?php echo APUtils::number_format($pricing_map[1]['send_out_directly']->item_value); ?>(<?php echo $pricing_map[1]['send_out_directly']->item_unit; ?>)+
					    <?php echo $pricing_map[1]['shipping_plus']->item_value; ?><?php echo $pricing_map[1]['shipping_plus']->item_unit; ?> of postage 
					</td>
					<td>
					    <?php echo APUtils::number_format($pricing_map[2]['send_out_directly']->item_value); ?>(<?php echo $pricing_map[2]['send_out_directly']->item_unit; ?>)+
					    <?php echo $pricing_map[2]['shipping_plus']->item_value; ?><?php echo $pricing_map[2]['shipping_plus']->item_unit; ?> of postage
					</td>
					<td>
					    <?php echo APUtils::number_format($pricing_map[3]['send_out_directly']->item_value); ?>(<?php echo $pricing_map[3]['send_out_directly']->item_unit; ?>)+
					    <?php echo $pricing_map[3]['shipping_plus']->item_value; ?><?php echo $pricing_map[3]['shipping_plus']->item_unit; ?> of postage
					</td>
				</tr>
				<tr>
				    <td>Shipping collected to delivery address</td>
					<td class="cell_red">
					    <?php echo APUtils::number_format($pricing_map[1]['send_out_collected']->item_value); ?>(<?php echo $pricing_map[1]['send_out_collected']->item_unit; ?>)+
					    <?php echo $pricing_map[1]['shipping_plus']->item_value; ?><?php echo $pricing_map[1]['shipping_plus']->item_unit; ?> of postage
					</td>
					<td>
					     <?php echo APUtils::number_format($pricing_map[2]['send_out_collected']->item_value); ?>(<?php echo $pricing_map[2]['send_out_collected']->item_unit; ?>)+
					     <?php echo $pricing_map[2]['shipping_plus']->item_value; ?><?php echo $pricing_map[2]['shipping_plus']->item_unit; ?> of postage
					</td>
					<td>
					     <?php echo APUtils::number_format($pricing_map[3]['send_out_collected']->item_value); ?>(<?php echo $pricing_map[3]['send_out_collected']->item_unit; ?>)+
					     <?php echo $pricing_map[3]['shipping_plus']->item_value; ?><?php echo $pricing_map[3]['shipping_plus']->item_unit; ?> of postage
					</td>
				</tr>
				<tr>
				    <td>Storing items over free period (letters)</td>
					<td class="cell_red"><?php echo APUtils::number_format($pricing_map[1]['storing_items_over_free_letter']->item_value); ?> (<?php echo $pricing_map[1]['storing_items_over_free_letter']->item_unit; ?>)</td>
					<td><?php echo APUtils::number_format($pricing_map[2]['storing_items_over_free_letter']->item_value); ?> (<?php echo $pricing_map[1]['storing_items_over_free_letter']->item_unit; ?>)</td>
					<td><?php echo APUtils::number_format($pricing_map[3]['storing_items_over_free_letter']->item_value); ?> (<?php echo $pricing_map[1]['storing_items_over_free_letter']->item_unit; ?>)</td>
				</tr>
				<tr>
				    <td>Storing items over free period (packages)</td>
					<td class="cell_red"><?php echo APUtils::number_format($pricing_map[1]['storing_items_over_free_packages']->item_value); ?> (<?php echo $pricing_map[1]['storing_items_over_free_packages']->item_unit; ?>)</td>
					<td><?php echo APUtils::number_format($pricing_map[2]['storing_items_over_free_packages']->item_value); ?> (<?php echo $pricing_map[1]['storing_items_over_free_packages']->item_unit; ?>)</td>
					<td><?php echo APUtils::number_format($pricing_map[3]['storing_items_over_free_packages']->item_value); ?> (<?php echo $pricing_map[1]['storing_items_over_free_packages']->item_unit; ?>)</td>
				</tr>
				<!-- <tr>
				    <td>Additional postbox</td>
					<td class="cell_red"><?php echo APUtils::number_format($pricing_map[1]['additional_private_mailbox']->item_value); ?>(<?php echo $pricing_map[1]['additional_private_mailbox']->item_unit; ?>)</td>
					<td><?php echo APUtils::number_format($pricing_map[2]['additional_private_mailbox']->item_value); ?> (<?php echo $pricing_map[1]['additional_private_mailbox']->item_unit; ?>)</td>
					<td><?php echo APUtils::number_format($pricing_map[3]['additional_private_mailbox']->item_value); ?> (<?php echo $pricing_map[1]['additional_private_mailbox']->item_unit; ?>)</td>
				</tr> -->
				<!-- <tr>
				    <td>Additional postbox business</td>
					<td class="cell_red" style="border-bottom: 2px solid red;"><?php echo APUtils::number_format($pricing_map[1]['additional_business_mailbox']->item_value); ?>(<?php echo $pricing_map[1]['additional_business_mailbox']->item_unit; ?>)</td>
					<td><?php echo APUtils::number_format($pricing_map[2]['additional_business_mailbox']->item_value); ?> (<?php echo $pricing_map[1]['additional_business_mailbox']->item_unit; ?>)</td>
					<td><?php echo APUtils::number_format($pricing_map[3]['additional_business_mailbox']->item_value); ?> (<?php echo $pricing_map[1]['additional_business_mailbox']->item_unit; ?>)</td>
				</tr> -->
				<tr>
                	<td>Included pages for opening and scanning (Additional)</td>
                	<td  class="cell_red" style="border-bottom: 2px solid red;"  ><?php echo $pricing_map[1]['additional_included_page_opening_scanning']->item_value; ?></td>
                	<td ><?php echo $pricing_map[2]['additional_included_page_opening_scanning']->item_value; ?></td>
                	<td ><?php echo $pricing_map[3]['additional_included_page_opening_scanning']->item_value; ?></td>
                </tr>
                <tr>
                	<td>Price of additional pages for opening and scanning</td>
                	<td  class="cell_red" style="border-bottom: 2px solid red;"  ><?php echo $pricing_map[1]['additional_included_page_opening_scanning']->item_value; ?></td>
                	<td><?php echo $pricing_map[2]['additional_included_page_opening_scanning']->item_value; ?></td>
                	<td><?php echo $pricing_map[3]['additional_included_page_opening_scanning']->item_value; ?></td>
                </tr>
                <tr>
                	<td>Customs declaration outgoing</td>
                	<td  class="cell_red" style="border-bottom: 2px solid red;"  ><?php echo $pricing_map[1]['custom_declaration_outgoing']->item_value; ?></td>
                	<td  ><?php echo $pricing_map[2]['custom_declaration_outgoing']->item_value; ?></td>
                	<td  ><?php echo $pricing_map[3]['custom_declaration_outgoing']->item_value; ?></td>
                </tr>
                <tr>
                	<td>Customs handling import</td>
                	<td  class="cell_red" style="border-bottom: 2px solid red;"  ><?php echo $pricing_map[1]['custom_handling_import']->item_value; ?></td>
                	<td ><?php echo $pricing_map[2]['custom_handling_import']->item_value; ?></td>
                	<td ><?php echo $pricing_map[3]['custom_handling_import']->item_value; ?></td>
                </tr>
                <tr>
                	<td>Cash payment for item on delivery (percentage)</td>
                	<td  class="cell_red" style="border-bottom: 2px solid red;"  ><?php echo $pricing_map[1]['cash_payment_on_delivery_percentage']->item_value; ?></td>
                	<td><?php echo $pricing_map[2]['cash_payment_on_delivery_percentage']->item_value; ?></td>
                	<td><?php echo $pricing_map[3]['cash_payment_on_delivery_percentage']->item_value; ?></td>
                </tr>
                <tr>
                	<td>Cash payment for item on delivery (minimum cost)</td>
                	<td  class="cell_red" style="border-bottom: 2px solid red;"  ><?php echo $pricing_map[1]['cash_payment_on_delivery_mini_cost']->item_value; ?></td>
                	<td><?php echo $pricing_map[2]['cash_payment_on_delivery_mini_cost']->item_value; ?></td>
                	<td><?php echo $pricing_map[3]['cash_payment_on_delivery_mini_cost']->item_value; ?></td>
                </tr>
<!--                <tr>
                	<td>Official address verification</td>
                	<td  class="cell_red" style="border-bottom: 2px solid red;"  ><?php echo $pricing_map[1]['official_address_verification']->item_value; ?></td>
                	<td><?php echo $pricing_map[2]['official_address_verification']->item_value; ?></td>
                	<td><?php echo $pricing_map[3]['official_address_verification']->item_value; ?></td>
                </tr>-->
			</table>
		</div>
	</form>
</div>
<div class="clear-height"></div>

<script type="text/javascript">
$(document).ready( function() {
// 	$("#submitButton").button().click(function() {
// 		$('#priceSettingForm').submit();
//         return false;
//     });
});
</script>