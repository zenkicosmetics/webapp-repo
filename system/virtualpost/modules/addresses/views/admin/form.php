<?php

if ($action_type == 'add') {
    $submit_url = base_url() . 'addresses/admin/add';
}
else {
    $submit_url = base_url() . 'addresses/admin/edit';
}
?>

<form id="addEditLocationForm" method="post"
	action="<?php echo $submit_url?>" enctype="multipart/form-data"
	autocomplete="on">
	<table>
		<tr>
			<th>Location Name <span class="required">*</span></th>
			<td><input type="text" id="addEditLocationForm_LocationName"
				name="location_name" value="<?php echo $location->location_name?>"
				class="input-width custom_autocomplete" maxlength="60" /></td>

		</tr>
		<tr>
			<th>Partner Name <span class="required">*</span></th>
			<td>
			    <?php echo my_form_dropdown(array(
                 "data" => $list_partner,
                 "value_key" => 'partner_id',
                 "label_key" => 'partner_name',
                 "value" => $location->partner_id,
                 "name" => 'partner_id',
                 "id"    => 'partner_id',
                 "clazz" => 'input-txt-none',
                 "style" => 'width: 260px;',
                 "has_empty" => true
             ));?>
			</td>
		</tr>
		<tr>
			<th>Pricing Template <span class="required">*</span></th>
			<td>
			    <?php echo my_form_dropdown(array(
                 "data" => $pricing_templates,
                 "value_key" => 'id',
                 "label_key" => 'name',
                 "value" => $location->pricing_template_id,
                 "name" => 'pricing_template_id',
                 "id"    => 'pricing_template_id',
                 "clazz" => 'input-txt-none',
                 "style" => 'width: 260px;',
                 "has_empty" => false
             ));?>
			</td>
		</tr>
		<tr>
			<th>Street <span class="required">*</span></th>
			<td><input type="text" id="route" name="street"
				value="<?php echo $location->street?>"
				class="input-width custom_autocomplete" maxlength=255 /></td>
		</tr>
		<tr>
			<th>Postcode <span class="required">*</span></th>
			<td><input type="text" id="postal_code" name="postcode"
				value="<?php echo $location->postcode?>"
				class="input-width custom_autocomplete" maxlength=10 /></td>
		</tr>
		<tr>
			<th>City <span class="required">*</span></th>
			<td><input type="text" id="locality" name="city"
				value="<?php echo $location->city?>"
				class="input-width custom_autocomplete" maxlength=255 /></td>
		</tr>
		<tr>
			<th>Region <span class="required">*</span></th>
			<td><input type="text" id="administrative_area_level_1" name="region"
				value="<?php echo $location->region?>"
				class="input-width custom_autocomplete" maxlength=255 /></td>
		</tr>
		<tr>
			<th>Country <span class="required">*</span></th>
			<td><input type="text" id="country" name="country"
				value="<?php echo $location->country_id?>"
				class="input-width custom_autocomplete" maxlength=255 /></td>
		</tr>
		<tr>
			<th>Language <span class="required">*</span></th>
			<td><input type="text" id="language" name="language"
				value="<?php echo $location->language?>"
				class="input-width custom_autocomplete" maxlength=255 /></td>
		</tr>
		<tr>
			<th>Image</th>
			<td><?php
$data = array (
        'name' => 'imagepath',
        'id' => 'imagepath',
        'value' => $location->image_path 
);
echo form_upload($data);
?>
			<br /> Select PNG file or JPG file (size 300 x 100) to upload.</td>
		</tr>


	</table>
	<input type="hidden" id="h_action_type" name="h_action_type"
		value="<?php echo $action_type?>" /> <input type="hidden"
		id="h_user_id" name="id" value="<?php echo $location->id?>" /> <input
		type="hidden" id="imagepath_id" name="imagepath_filename" value="" />


</form>
<script type="text/javascript">

$(document).ready( function() {

	/**
     * When select file
     */
    
     var autocomplete;
     autocomplete = new google.maps.places.Autocomplete(
          (document.getElementById('addEditLocationForm_LocationName')),
          { types: ['geocode'] });
     google.maps.event.addListener(autocomplete, 'place_changed', function() {
          var place = autocomplete.getPlace();

          var componentForm = {
               route: 'long_name',
               locality: 'long_name',
               administrative_area_level_1: 'short_name',
               country: 'long_name',
               postal_code: 'short_name'
          };

          for (var component in componentForm) {
               document.getElementById(component).value = '';
               document.getElementById(component).disabled = false;
          }

          // Get each component of the address from the place details
          // and fill the corresponding field on the form.
          for (var i = 0; i < place.address_components.length; i++) {
               var addressType = place.address_components[i].types[0];
               if (componentForm[addressType]) {
                    var val = place.address_components[i][componentForm[addressType]];
                    document.getElementById(addressType).value = val;
               }
          }

     });
    
     $('#imagepath').live('change', function(){
    	myfile= $( this ).val();
 	    var ext = myfile.split('.').pop();
   	    ext = ext.toUpperCase();
  	    if(ext != "PNG" && ext != "JPG"){
  	       $.displayError('Please select PNG or JPG file to upload.', null, function() {
   	    	  $('#imagepath_id').val('');
  	 	   });
  	       return;
  	    }
  	    $('#imagepath_id').val(($('#imagepath').val()));
    });


});
</script>