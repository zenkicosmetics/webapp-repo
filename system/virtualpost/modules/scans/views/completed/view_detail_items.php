
<h2 style="font-size: 16px; margin-bottom: 6px; margin-top: 2px; font-weight: lighter;color: #000;"> Shipping Address: </h2>

<textarea readonly="readonly"  style="width: 250px;padding-top: 6px;" onClick="this.select();" rows="6" cols="50"> <?php echo trim(strip_tags(ucwords($fullAddress)));?></textarea> <br/><br/>

<h2 style="margin-bottom: 6px; margin-top: 2px;color: #000;"> 
	<?php  echo (!empty($shipping_tracking) && !empty($shipping_tracking->tracking_number)) ? "Tracking Number: ".$shipping_tracking->tracking_number : "No tracking number"; ?>
</h2>

<h2 style="margin-bottom: 6px; margin-top: 12px;color: #000;"> 
	<?php echo (!empty($shipping_services) && (!empty($shipping_services->name)) ) ? "Shipping Service: ".$shipping_services->name : "No shipping service"; ?>
</h2>


