<!-- Customer information  -->
<table style="size: 8px; width: 675px">
	<tr>
		<td style="width: 100%; padding-left: 0px; margin-left: 0px">
			<table>
				<tr>
					<td><?php echo Settings::get(APConstants::INSTANCE_OWNER_COMPANY_CODE)?><br />
                           <?php echo Settings::get(APConstants::INSTANCE_OWNER_STREET_CODE)?> <br />
                           <?php echo Settings::get(APConstants::INSTANCE_OWNER_PLZ_CODE).' '.Settings::get(APConstants::INSTANCE_OWNER_CITY_CODE);?><br />
                           <?php echo Settings::get(APConstants::INSTANCE_OWNER_COUNTRY_CODE)?><br />
						Customs Number: <br />
                           <?php echo Settings::get(APConstants::INSTANCE_OWNER_CUSTOMS_NUMBER)?> <br />
					</td>
					<td style="text-align: left;">Acts with power of attorney for
						customs declaration on behalf of:</td>
					<td style="text-align: right;">
                           <?php if (!empty($invoice_address->invoicing_address_name)) {echo $invoice_address->invoicing_address_name.'<br/>';}?>
                           <?php if (!empty($invoice_address->invoicing_company)) {echo $invoice_address->invoicing_company.'<br/>';}?>
                           <?php echo $invoice_address->invoicing_street?><br />
                           <?php echo $invoice_address->invoicing_postcode .', '.$invoice_address->invoicing_city?><br />
                           <?php if (!empty($invoice_address->invoicing_region)) {echo $invoice_address->invoicing_region.'<br/>';}?>
                           <?php echo $invoice_address->invoicing_country?><br />
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table>
				<tr>
					<td style="width: 100%;">To forward shipment to<br/><br/>
                        <table>
                            <tr>
                                <td><strong>Destination:</strong> <br/>
                                    <?php if($address){?>
                                        <?php if (!empty($address->shipment_address_name)) {echo $address->shipment_address_name.'<br/>';}?>
                                        <?php if (!empty($address->shipment_company)) {echo $address->shipment_company.'<br/>';}?>
                                        <?php echo $address->shipment_street?><br />
                                        <?php echo $address->shipment_postcode .', '.$address->shipment_city?><br />
                                        <?php if (!empty($address->shipment_region)) {echo $address->shipment_region.'<br/>';}?>
                                        <?php echo $address->shipment_country?><br />
                                        <?php echo $envelope_customs ? $envelope_customs->phone_number : "";?>
                                    <?php }?>
                                </td>
                                <td style="text-align: left;">&nbsp;<br />from the following location </td>
                                <td style="text-align: right;"><strong>Origin: <br/></strong>
                                       <?php echo $postbox->company_name;?><br />
                                       <?php echo $postbox->invoicing_street?><br />
                                       <?php echo $postbox->invoicing_zipcode?><br />
                                       <?php echo $postbox->invoicing_city?><br />
                                       <?php echo $postbox->partner_country?><br />
                                       <?php echo $postbox->company_telephone?>
                                </td>
                            </tr>
                        </table>
                    </td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style="text-align: right;">
            <?php echo $location_item?>, <?php echo $target_date?>
        </td>
	</tr>
	<tr>
		<td><b style="text-align: left;">Proforma Invoice,</b></td>
	</tr>
</table>
<br />
<div>
	<!-- content -->
	<table border="1px" style="size: 7px; width: 100%">
		<tr>
			<th align="center" style="width: 10%">Item</th>
			<th align="center" style="width: 35%">Description</th>
            <th align="center" style="width: 10%">H.S.Code</th>
            <th align="center" style="width: 15%">Country of origin</th>
            <th align="center" style="width: 10%">Quantity</th>
			<th align="center" style="width: 10%">Value EUR</th>
			<th align="center" style="width: 10%">Total EUR</th>
		</tr>
		<tbody>
            <?php $i = 0; $total_cost = 0;?>
            <?php
            if($list_custom_items){
                foreach ( $list_custom_items as $custom_item ) {
                $i ++;
                $total_cost += $custom_item->cost * $custom_item->quantity;
                ?>
                <tr>
                    <th style="width: 10%"><?php echo $i;?></th>
                    <th style="width: 35%"><?php echo $custom_item->material_name;?></th>
                    <th style="width: 10%"><?php echo $custom_item->hs_code;?></th>
                    <th style="width: 15%"><?php echo $custom_item->country_name;?></th>
                    <th align="right" style="width: 10%"><?php echo $custom_item->quantity;?></th>
                    <th align="right" style="width: 10%"><?php echo APUtils::number_format($custom_item->cost, 2);?></th>
                    <th align="right" style="width: 10%"><?php echo APUtils::number_format($custom_item->cost * $custom_item->quantity, 2);?></th>
                </tr>
                <?php }?>
            <?php }?>
                <tr>
                    <th style="width: 10%">Total Cost</th>
                    <th style="">&nbsp;</th>
                    <th style="width: 10%">&nbsp;</th>
                    <th style="width: 15%">&nbsp;</th>
                    <th style="width: 10%">&nbsp;</th>
                    <th style="width: 10%">&nbsp;</th>
                    <th align="right" style="width: 10%"><?php echo APUtils::number_format($total_cost, 2);?></th>
                </tr>
            </tbody>
	</table>
        <?php if ($list_tracking_services != null && count($list_tracking_services) > 0) { ?>
        <p></p>
        <?php if ($list_tracking_services != null && count($list_tracking_services) > 1) { ?>
        <p>The items from this proforma invoice have been split into <?php echo count($list_tracking_services);?> 
                    shipments. The shipment informations are: </p>
        <?php } ?>
        <?php foreach ($list_tracking_services as $track_service) { ?>
            <p><?php echo $track_service['service_name'].'-'.$track_service['track_number']; ?></p>
        <?php } ?>
        <?php } ?>
</div>
<br />
<br />
<span> If there are any questions, please contact me under +49 30 467
	260 777 or mail@clevvermail.com </span>
