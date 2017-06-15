<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Test controller for the social module (frontend)
 * 
 * @author DuNT
 * @package PyroCMS
 * @subpackage Widget module
 * @category Modules
 */
?>
<div class="left column" style="margin-right: 26px; width: 180px">
    <p>
        <label class="normal"> Make </label>
    </p>
    <p>
        <select  title="Maker" class="input-text multiple-selectbox" id="vehicle_make" name="vehicle_make" size="8" style="width: 180px">
                <option disabled>SELECT</option>
                <?php foreach ($makes as $make) {?>
        		<option value="<?php echo $make->ID?>"><?php echo $make->Make?></option>
        		<?php }?>
        </select>
    </p>
</div>
<div class="left column" style="margin-right: 26px; width: 180px;">
    <p>
        <label class="normal"> Model </label>
    </p>
    <p>
    <select  title="Select model" class="input-text multiple-selectbox" id="vehicle_model" name="vehicle_model" size="8" style="width: 180px">
        <option disabled>SELECT</option>
    </select>
    </p>
</div>
<div class="left column input-width-100" style="margin-right: 26px">
    <p>
    <label class="normal"> Year </label>
    </p>
    <p>
    <select  title="Select year" class="input-text input-width-100 multiple-selectbox" id="vehicle_year" name="vehicle_year" size="8">
        <option disabled>SELECT</option>
    </select>
    </p>
</div>
<div class="left column" style="width: 240px">
    <p>
    <label class="normal"> Series (optional) </label>
    </p>
    <p>
    <select  title="Select series" class="input-text multiple-selectbox" id="vehicle_series" style="width: 240px" name="vehicle_series" size="8">
        <option disabled>SELECT</option>
    </select>
    </p>
</div>
<div class="hide">
    <form id="MakeModelHiddenForm" action="#" method="post">
        <input type="hidden" id="MakeModelHiddenForm_MakeID" value="<?php if (!empty($MakeID)) {echo $MakeID;}?>" />
        <input type="hidden" id="MakeModelHiddenForm_Model" value="<?php if (!empty($Model)) {echo $Model;}?>" />
        <input type="hidden" id="MakeModelHiddenForm_Series" value="<?php if (!empty($Series)) {echo $Series;}?>" />
        <input type="hidden" id="MakeModelHiddenForm_SearchYear" value="<?php if (!empty($SearchYear)) {echo $SearchYear;}?>" />
    </form>
</div>
<script type="text/javascript">
$(document).ready( function() {

	// Select make id
	if ($('#MakeModelHiddenForm_MakeID').val() != '') {
		$('#vehicle_make').val($('#MakeModelHiddenForm_MakeID').val());
		vehicle_make_change();
	}

	function vehicle_make_change() {
		$('#vehicle_model').empty();
    	$('#vehicle_model').append('<option value="" disabled>SELECT</option>');
    	$('#vehicle_year').empty();
    	$('#vehicle_year').append('<option value="" disabled>SELECT</option>');
    	$('#vehicle_series').empty();
    	$('#vehicle_series').append('<option value="" disabled>SELECT</option>');
    	$.bindSelect('<?php echo  base_url()?>common/vehicles', {make:$('#vehicle_make').val()}, 'vehicle_model', 'SELECT', '', function() {
    		if ($('#MakeModelHiddenForm_Model').val() != '') {
    			$('#vehicle_model').val($('#MakeModelHiddenForm_Model').val());
    			$('#vehicle_model').trigger('change');
    		}
        });
	}

	function vehicle_model_change() {
		$('#vehicle_year').empty();
    	$('#vehicle_year').append('<option value="" disabled>SELECT</option>');
    	$('#vehicle_series').empty();
    	$('#vehicle_series').append('<option value="" disabled>SELECT</option>');
    	$.bindSelect('<?php echo  base_url()?>common/vehicles', {make:$('#vehicle_make').val(), model:$('#vehicle_model').val()}, 'vehicle_year', 'SELECT', '', function() {
    	    var search_year = $('#MakeModelHiddenForm_SearchYear').val();
    		if (search_year != '' && search_year.length <= 4) {
    			$('#vehicle_year').val(search_year);
    			$('#vehicle_year').trigger('change');
    		}
        });
	}

	function vehicle_year_change() {
		$('#vehicle_series').empty();
    	$('#vehicle_series').append('<option value="" disabled>SELECT</option>');
    	$.bindSelect('<?php echo  base_url()?>common/vehicles', {make:$('#vehicle_make').val(), model:$('#vehicle_model').val(), year:$('#vehicle_year').val()}, 'vehicle_series', 'SELECT', '', function(){
    		if ($('#MakeModelHiddenForm_Series').val() != '') {
    			$('#vehicle_series').val($('#MakeModelHiddenForm_Series').val());
    			$('#vehicle_series').trigger('change');
    		}
        });
	}
	
	// Binding when make vehicle is change
    $('#vehicle_make').live('change', function() {
    	vehicle_make_change();
    });

    // Binding when make vehicle is change
    $('#vehicle_model').live('change', function() {
    	vehicle_model_change();
    });

    // Binding when make year is change
    $('#vehicle_year').live('change', function() {
    	vehicle_year_change();
    });
});
</script>