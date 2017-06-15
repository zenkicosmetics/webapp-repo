<?php
    $submit_url = base_url().'customers/register';
?>
<form id="customerRegisterNewUserForm" method="post" class="dialog-form"
	action="<?php echo $submit_url?>">
	<h2 style="font-size: 16px; padding-bottom: 10px;">Register now for free</h2>
	<table>
		<tr>
			<th style="text-align: left;">E-mail </th>
			<td><input type="text" id="customerRegisterNewUserForm_email" name="email"
				value="<?php echo $customer->email?>"
				class="input-width custom_autocomplete" maxlength=50 /></td>
		</tr>
		<tr>
			<th style="text-align: left;">Password </th>
			<td><input type="password" id="customerRegisterNewUserForm_password" name="password"
				value="<?php echo $customer->password?>"
				class="input-width custom_autocomplete" maxlength=50 /></td>
		</tr>
		<tr>
			<th>&nbsp;</th>
			<td>
			    
			    <input id="customerRegisterNewUserForm_termsOfService" type="checkbox" class="customCheckbox" name="agree_flag" value="1" />
			    <span>I hereby agree to the <a href="#" id="termsOfService">Terms of Service/Terms and conditions</a></span>
			 </td>
		</tr>
	</table>
	<input type="hidden" id="h_partner_code" name="p" value="" /> 
	<input type="hidden" id="h_partner_website" name="p_website" value="" /> 
</form>
<!-- Content for dialog -->
<div class="hide">
	<div id="termOfServiceWindow" title="Term Of Service" class="input-form dialog-form">
	</div>
</div>
<script type="text/javascript">
$(document).ready( function() {
	// Apply checkbox style
    // $('input:checkbox.customCheckbox').checkbox({cls:'jquery-safari-checkbox'});

    var partner_code = getCookie("partner_referrer_code");
    var partner_website = getCookie("partner_referrer_website");
    $('#h_partner_code').val(partner_code);
    $('#h_partner_website').val(partner_website);
    console.log("partner_code", partner_code);

    console.log("partner_website", partner_website);

	$('#termsOfService').live('click', function() {
		// Open new dialog
		$('#termOfServiceWindow').openDialog({
			autoOpen: false,
			height: 620,
			width: 800,
			modal: true,
			open: function() {
				$(this).load("<?php echo base_url() ?>customers/term_of_service", function() {
				});
			},
			buttons: {
				'Close': function () {
					$(this).dialog('close');
				}
			}
		});
		$('#termOfServiceWindow').dialog('option', 'position', 'center');
		$('#termOfServiceWindow').dialog('open');
		return false;
	});

	function getCookie(cname) {
	    var name = cname + "=";
	    var ca = document.cookie.split(';');
	    for(var i=0; i<ca.length; i++) {
	        var c = ca[i];
	        while (c.charAt(0)==' ') c = c.substring(1);
	        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
	    }
	    return "";
	}
});
</script>