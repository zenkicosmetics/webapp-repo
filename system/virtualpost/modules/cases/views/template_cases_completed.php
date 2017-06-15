<!-- cases compeleted information  -->
<style type="text/css">
table, tr {
    border: 1px solid black;
}
</style>
<?php
ci()->load->model('settings/countries_m');
ci()->load->model('settings/customer_m');
$i = 1;
$j = 1;
?>
<table style="size: 8px;">
		<tr>
		 	<th> <?php language_e('cases_view_template_case_InvoicingAddress'); ?> </th>
		 	<th> <?php language_e('cases_view_template_case_ForwardingAddress'); ?></th>
		</tr>
	<?php if($complete_cases){?>
		<?php // With a customer has many postbox --> show full information Invoice address anf Forwarding Address ?>
		<?php if($count_postbox){?>
		<?php foreach ($count_postbox as $count){  ?>
		<?php if ($count->total > 1){ ?>
		 <?php
		 	$customer_addr = ci()->customer_m->get_customer_by(array('customer_id' => $count->customer_id));
		 	foreach ($customer_addr as $addr){
		 ?>
		<tr>
			<td style="width:50%; padding-left: 0px; margin-left: 0px">
				<table>
					<tr>
						<td> <?php echo $i?>. <?php language_e('cases_view_template_case_Name'); ?><?php echo $addr->invoicing_address_name; ?></td>
					</tr>
					<tr>
						<td><?php language_e('cases_view_template_case_Company'); ?><?php echo $addr->invoicing_company; ?></td>
					</tr>
					<tr>
						<td><?php language_e('cases_view_template_case_Street'); ?><?php echo $addr->invoicing_street; ?></td>
					</tr>
					<tr>
						<td><?php language_e('cases_view_template_case_Postcode'); ?><?php echo $addr->invoicing_postcode; ?></td>
					</tr>
					<tr>
						<td><?php language_e('cases_view_template_case_City'); ?><?php echo $addr->invoicing_city; ?></td>
					</tr>
					<tr>
						<td><?php language_e('cases_view_template_case_Region'); ?><?php echo  $addr->invoicing_region; ?></td>
					</tr>
					<tr>
						<td><?php language_e('cases_view_template_case_Country'); ?><?php echo $country = ci()->countries_m->get_country_by(array('id' => $addr->invoicing_country)); ?></td>
					</tr>
				</table>
				<br>
			</td>

			<td style="width: 50%; padding-left: 0px; margin-left: 0px">
				<table>
					<tr>
						<td> <?php echo $i?>. <?php language_e('cases_view_template_case_Name'); ?><?php echo $addr->shipment_address_name; ?></td>
					</tr>
					<tr>
						<td><?php language_e('cases_view_template_case_Company'); ?><?php echo  $addr->shipment_company; ?></td>
					</tr>
					<tr>
						<td><?php language_e('cases_view_template_case_Street'); ?><?php echo $addr->shipment_street; ?></td>
					</tr>
					<tr>
						<td><?php language_e('cases_view_template_case_Postcode'); ?><?php echo $addr->shipment_postcode; ?></td>
					</tr>
					<tr>
						<td><?php language_e('cases_view_template_case_City'); ?><?php echo $addr->shipment_city; ?></td>
					</tr>
					<tr>
						<td><?php language_e('cases_view_template_case_Region'); ?><?php echo  $addr->shipment_region; ?></td>
					</tr>
					<tr>
						<td><?php language_e('cases_view_template_case_Country'); ?><?php echo $country = ci()->countries_m->get_country_by(array('id' => $addr->shipment_country)); ?></td>
					</tr>
				</table>
				<br >
			</td>

		</tr>
		<?php
			$i++; }
		?>
		<?php }?>
		<?php }?>
		<?php }?>
		<br>
		<?php foreach ($complete_cases as $complete){?>
		<tr>
			<td style="width: 60%; padding-left: 0px; margin-left: 0px">
				<table>
					<tr>
						<td><?php echo $j?>. <?php language_e('cases_view_template_case_NamePostbox'); ?><?php echo $complete->postbox_name; ?></td>
					</tr>
					<tr>
						<td><?php language_e('cases_view_template_case_Company'); ?><?php echo $complete->company; ?></td>
					</tr>
					<tr>
						<td><?php language_e('cases_view_template_case_CaseNameOfPostbox'); ?><?php  echo $complete->case_identifier; ?></td>
					</tr>
				</table>
			</td>
		</tr>
		<br>
		<?php
			$j++; }
		?>
	<?php }?>
</table>