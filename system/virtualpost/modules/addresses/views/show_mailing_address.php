<div id="left-content">
	<?php if($postbox){?>
	<table>
		<tbody>
			<tr>
				<td style="width: 50px;">Name</td>
				<td class="left-align" style="width: 75px;"><?php echo $postbox->name;?></td>
			</tr>
			<tr>
				<td>Company</td>
				<td class="left-align"><?php echo $postbox->company;?></td>
			</tr>
			<tr>
				<td>Street</td>
				<td class="left-align"><?php if($location) {echo $location->street;}?></td>
			</tr>
			<tr>
				<td>Postcode</td>
				<td class="left-align"><?php if($location) {echo $location->postcode;}?></td>
			</tr>
			<tr>
				<td>City</td>
				<td class="left-align"><?php if($location) {echo $location->city;}?></td>
			</tr>
			<tr>
				<td>Region</td>
				<td class="left-align" ><?php if($location) {echo $location->region;}?></td>
			</tr>
			<tr>
				<td>Country</td>
				<td class="left-align"><?php if($location) {echo $location->country_name;}?></td>
			</tr>
            <tr>
				<td>Phone</td>
				<td class="left-align" ><?php if($location) {echo $location->phone_number;}?></td>
			</tr>
			<tr>
				<td>Email</td>
				<td class="left-align"><?php if($location) {echo $location->email;}?></td>
			</tr>
		</tbody>
	</table>
	<?php }?>
</div>