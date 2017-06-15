<style>
    .view-detail h2{
        color:#000;
        font-size:18px;
    }
    .view-detail h3{
        color:#000;
        font-size:14px;
    }
    .view-detail h4{
        color:#000;
        font-weight: bold;
    }
    .view-detail h5{
        color:#636262;
    }
    
    .view-detail .customer-infor{
        border: 1px solid #BEBEBE;
        border-radius: 
        5px;background-color: 
        #F2F2F2; height:50px; 
        vertical-align: middle;
    }
    table.tbody tr td{
        text-align:center;
    }
    
    .detail table thead th {
        border-bottom: 1px solid #BEBEBE;
    }
    
    .right-dotted{
        border-right: 1px dotted #BEBEBE;
    }
    
</style>

<div class="view-detail">
    <table>
        <tbody>
            <tr>
                <td width="300px">
                    <div style="padding-top:10px"><h3><a id="goto_customer_frontend" data-id="<?php echo $customer->customer_id;?>" href="#"><?php echo $customer->customer_code;?></a></h3></div>
                    <?php if (!empty($main_postbox->name)) {?>
                    <div style="padding-top:10px"><h3><?php echo $main_postbox->name;?></h3></div>
                    <?php }?>
                    <?php if (!empty($main_postbox->company)) {?>
                    <div style="padding-top:10px"><h3><?php echo $main_postbox->company;?></h3></div>
                    <?php }?>
                    <div style="padding-top:10px"><h3><?php echo $customer->email;?></h3></div>
                    <div style="padding-top:10px"><h5>Next Invoicing Date: <?php 
                    //echo APUtils::displayDateFormat(APUtils::getLastDayOfCurrentMonth(),$date_format)
                    echo $next_invoices_date;
                    ?></h5></div>
                </td>
                <td align="center" class="customer-infor">
                    <table>
                        <tbody>
                        <tr>
                            <td width="25%" align="center"><h4>Account Type: <?php 
                            //echo lang('account_type_'.$customer->account_type)
                            echo $account_type;
                            ?></h4></td>
                            <td width="25%" align="center"><h4>#postboxes: <?php echo ($free_postbox_count + $private_postbox_count+ $business_postbox_count);?></h4></td>
                            <td width="25%" align="center"><h4>status: <?php echo $customer_status;?></h4>
                            </td>
                            <td width="25%" align="center"><h4>$: <?php echo $customer_charge_fee; ?></h4></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div class="clearfix"></div>

<div class="detail">
    <table>
        <thead>
            <tr>
            <th class="right-dotted">Current Invoicing Details</th>
            <th class="right-dotted">Shipping Information</th>
            <th class="right-dotted">Invoicing Information</th>
            <th class="right-dotted">Customer Information</th>
            <th class="right-dotted">Activation Information</th>
            </tr>
        </thead>
        <tbody>
            <tr>
            <td  class="right-dotted">
                <?php if($next_invoices){?>
                	<table>
                		<tbody>
                			<tr>
                				<td>Postboxes</td>
                				<td class="right-align" style="width: 200px;"><?php echo $postboxes; ?></td>
                			</tr>
                			<tr>
                				<td>Envelope scanning</td>
                				<td class="right-align"><?php echo $envelope_scanning;?></td>
                			</tr>
                			<tr>
                				<td>Scanning</td>
                				<td class="right-align"><?php echo $scanning; ?></td>
                			</tr>
                			<tr>
                				<td>Additional items</td>
                				<td class="right-align"><?php echo $additional_items;?></td>
                			</tr>
                			<tr>
                				<td>Additional scanning items</td>
                				<td class="right-align"><?php echo $additional_scanning_items; ?></td>
                			</tr>
                			<tr>
                				<td>Shipping&amp;handling</td>
                				<td class="right-align" ><?php echo $shipping_handling; ?></td>
                			</tr>
                			<tr>
                				<td>Storing items</td>
                				<td class="right-align"><?php echo $storing_items; ?></td>
                			</tr>
                		</tbody>
                		<tfoot>
                			<tr>
                				<td class="left-align">Current Total</td>
                				<td class="right-align"><?php 
                                    echo $current_total;
                				?></td>
                			</tr>
                		</tfoot>	
                	</table>
                	<?php }?>
            </td>
            <td  class="right-dotted">
                <table>
            		<tbody>
            			<tr>
            			    <td style="width: 150px;">Shipping name</td>
            				<td class="right-align"><?php if ($customer_shipping_address) { echo $customer_shipping_address->shipment_address_name;} else { echo 'N/A';}?></td>
            			</tr>
            			<tr>
            			    <td style="width: 150px;">Shipping company</td>
            				<td class="right-align"><?php if ($customer_shipping_address) { echo $customer_shipping_address->shipment_company;} else { echo 'N/A';}?></td>
            			</tr>
            			<tr>
            			    <td style="width: 150px;">Shipping street</td>
            				<td class="right-align"><?php if ($customer_shipping_address) { echo $customer_shipping_address->shipment_street;} else { echo 'N/A';}?></td>
            			</tr>
            			<tr>
            			    <td style="width: 150px;">Shipping postcode</td>
            				<td class="right-align"><?php if ($customer_shipping_address) { echo $customer_shipping_address->shipment_postcode;} else { echo 'N/A';}?></td>
            			</tr>
            			<tr>
            			    <td style="width: 150px;">Shipping city</td>
            				<td class="right-align"><?php if ($customer_shipping_address) { echo $customer_shipping_address->shipment_city;} else { echo 'N/A';}?></td>
            			</tr>
            			<tr>
            			    <td style="width: 150px;">Shipping region</td>
            				<td class="right-align"><?php if ($customer_shipping_address) { echo $customer_shipping_address->shipment_region;} else { echo 'N/A';}?></td>
            			</tr>
            			<tr>
            			    <td style="width: 150px;">Shipping country</td>
            				<td class="right-align"><?php if ($customer_shipping_address) { echo $customer_shipping_address->shipment_country;} else { echo 'N/A';}?></td>
            			</tr>
            		</tbody>
            		
            	</table>
            </td>
            <td  class="right-dotted">
                <table>
            		<tbody>
            			<tr>
            			    <td style="width: 150px;">Invoicing name</td>
            				<td class="right-align"><?php if ($customer_shipping_address) { echo $customer_shipping_address->invoicing_address_name;} else { echo 'N/A';}?></td>
            			</tr>
            			<tr>
            			    <td style="width: 150px;">Invoicing company</td>
            				<td class="right-align"><?php if ($customer_shipping_address) { echo $customer_shipping_address->invoicing_company;} else { echo 'N/A';}?></td>
            			</tr>
            			<tr>
            			    <td style="width: 150px;">Invoicing street</td>
            				<td class="right-align"><?php if ($customer_shipping_address) { echo $customer_shipping_address->invoicing_street;} else { echo 'N/A';}?></td>
            			</tr>
            			<tr>
            			    <td style="width: 150px;">Invoicing postcode</td>
            				<td class="right-align"><?php if ($customer_shipping_address) { echo $customer_shipping_address->invoicing_postcode;} else { echo 'N/A';}?></td>
            			</tr>
            			<tr>
            			    <td style="width: 150px;">Invoicing city</td>
            				<td class="right-align"><?php if ($customer_shipping_address) { echo $customer_shipping_address->invoicing_city;} else { echo 'N/A';}?></td>
            			</tr>
            			<tr>
            			    <td style="width: 150px;">Invoicing region</td>
            				<td class="right-align"><?php if ($customer_shipping_address) { echo $customer_shipping_address->invoicing_region;} else { echo 'N/A';}?></td>
            			</tr>
            			<tr>
            			    <td style="width: 150px;">Invoicing country</td>
            				<td class="right-align"><?php if ($customer_shipping_address) { echo $customer_shipping_address->invoicing_country;} else { echo 'N/A';}?></td>
            			</tr>
            		</tbody>
            		
            	</table>
            </td>
            <td class="right-dotted">
                <table>
            		<tbody>
            			<tr>
            				<td style="width: 150px;">Account Type</td>
            				<td class="right-align"><?php echo $account_type; ?></td>
            			</tr>
            			<tr>
            				<td style="width: 150px;">Standard payment method</td>
            				<td class="right-align" ><?php echo $standard_payment_method; ?></td>
            			</tr>
            			<tr>
            				<td style="width: 150px;">Number of AS YOU GO postboxes</td>
            				<td class="right-align"><?php echo $free_postbox_count?></td>
            			</tr>
            			<tr>
            				<td style="width: 150px;">Number of PRIVATE postboxes</td>
            				<td class="right-align"><?php echo $private_postbox_count?></td>
            			</tr>
            			<tr>
            				<td style="width: 150px;">Number of BUSINESS postboxes</td>
            				<td class="right-align"><?php echo $business_postbox_count?></td>
            			</tr>
            			<tr>
            				<td style="width: 150px;">Active/inactive</td>
            				<td class="right-align" ><?php echo $customer_activated; ?>
                            </td>
            			</tr>
            			<tr>
            				<td style="width: 150px;">Charge/no charge</td>
            				<td class="right-align" ><?php echo $customer_charge_fee;?>
                            </td>
            			</tr>
            			<tr>
            				<td style="width: 150px;">Envelope scans/m</td>
            				<td class="right-align" ><?php echo $scan_item ['envelope_scan_number']?></td>
            			</tr>
            			<tr>
            				<td style="width: 150px;">Scans/m</td>
            				<td class="right-align" ><?php echo $scan_item ['document_scan_number']?></td>
            			</tr>
            			<tr>
            				<td style="width: 150px;">Shipments/m</td>
            				<td class="right-align" ><?php echo $scan_item ['shipping_number']?></td>
            			</tr>
            			
            			<tr>
            				<td style="width: 150px;">Invoice code</td>
            				<td class="right-align" ><?php echo $customer->invoice_code?></td>
            			</tr>
            			<?php if (!empty($customer_shipping_address->vat_number)) {?>
            			<tr>
            				<td style="width: 150px;">VAT Number</td>
            				<td class="right-align" ><?php echo $customer_shipping_address->vat_number?></td>
            			</tr>
            			<?php }?>

            			<tr>
            				<td style="width: 150px;">VAT Case</td>
            				<td class="right-align" ><?php echo $vat->vat_case; ?></td>
            			</tr>
            			<tr>
            				<td style="width: 150px;">VAT Rate</td>
            				<td class="right-align" ><?php echo $vat_rate; ?></td>
            			</tr>
            		</tbody>
            	</table>
            </td>
            <td  class="right-dotted left_jbar jbar-top" style="width:200px">
                <ul style="text-align: left;">
            <?php if ($active_flag['shipping_address_completed'] == 1) {?>
            <li class="completed">Shipping Address</li>
            <?php } else {?>
            <li class="not_completed">Shipping Address</li>
            <?php } ?>
            
            <?php if ($active_flag['invoicing_address_completed'] == 1) {?>
            <li class="completed">Invoicing Address</li>
            <?php } else {?>
            <li class="not_completed">Invoicing Address</li>
            <?php }?>
            
            <?php if ($active_flag['postbox_name_flag'] == 1) {?>
            <li class="completed">Postbox Name</li>
            <?php } else {?>
            <li class="not_completed">Postbox Name</li>
            <?php }?>
            
            <?php if ($active_flag['name_comp_address_flag'] == 1) {?>
            <li class="completed">Name/company in Address</li>
            <?php } else {?>
            <li class="not_completed">Name/company in Address</li>
            <?php }?>
            
            <?php if ($active_flag['city_address_flag'] == 1) {?>
            <li class="completed">City for Address</li>
            <?php } else {?>
            <li class="not_completed">City for Address</li>
            <?php }?>
            
            <?php if ($active_flag['payment_detail_flag'] == 1) {?>
            <li class="completed">Payment details</li>
            <?php } else {?>
            <li class="not_completed">Payment details</li>
            <?php }?>
            
            <?php if ($active_flag['email_confirm_flag'] == 1) {?>
            <li class="completed">E-Mail confirmation</li>
            <?php } else {?>
            <li class="not_completed">E-Mail confirmation</li>
            <?php }?>
            
            <?php if ($customer_cloud) {?>
            <li class="completed">Cloud information</li>
            <?php } else {?>
            <li class="not_completed">Cloud information</li>
            <?php }?>
        </ul>
            </td>
            </tr>
        </tbody>
    </table>
</div>
<div class="hide">
<form id="hiddenAccessCustomerSiteForm02" target="blank" action="<?php echo base_url()?>admin/customers/view_site" method="post">
	    <input type="hidden" id="hiddenAccessCustomerSiteForm02_customer_id" name="customer_id" value="" />
</form>
</div>
<script type="text/javascript">
$(document).ready( function() {
	/**
	 * Access the customer site
	 */
	$('#goto_customer_frontend').live('click', function() {
	    var customer_id = $(this).attr('data-id');
	    $('#hiddenAccessCustomerSiteForm02_customer_id').val(customer_id);
	    $('#hiddenAccessCustomerSiteForm02').submit();
	    return false;
	});
	
});
</script>