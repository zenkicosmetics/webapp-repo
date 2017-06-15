<?php
$submit_url = base_url () . 'customers/register';
?>
<form id="customerRegisterNewUserForm" method="post" class="dialog-form" action="<?php echo $submit_url?>">
	<h2 class="title"><img src="<?php echo APContext::getAssetPath()?>images/favicon2.png" /><label><?php echo $partner ? $partner->title : "Register now for free" ?></label></h2>
	<table style="display: inline;">
		<tr>
			<th>E-mail</th>
			<td><input type="text" id="customerRegisterNewUserForm_email" name="email" value=""
				class="input-width custom_autocomplete" maxlength=50 /></td>
		</tr>
		<tr>
			<th>Password</th>
			<td><input type="password" id="customerRegisterNewUserForm_password" name="password" value=""
				class="input-width custom_autocomplete" maxlength=50 /></td>
		</tr>
		<tr>
			<th>&nbsp;</th>
			<td><input id="customerRegisterNewUserForm_termsOfService" type="checkbox" class="customCheckbox" name="agree_flag"
				value="1" /> <span>I hereby agree to the <a href="#" id="termsOfService">Terms of Service/Terms and conditions</a></span>
			</td>
		</tr>
	</table>
	<div class="clear-fix"></div>
	<div class="register"><button type="button" type="button" id="registerBtn" class="input-btn">Register</button></div>
	<input type="hidden" name="partner_id" value="<?php echo $partner_id?>" />
	<input type="hidden" name="p" value="<?php echo $partner->token?>" />
</form>

<div style="display: none;" >
	<form method="post" id="login-form-hidden">
		<input type="hidden" name="user_name" id="login-form-hidden-username" />
		<input type="hidden" name="password" id="login-form-hidden-password" />
		<input type="hidden" value="20140123" name="key" />
	</form>
</div>

<!-- Content for dialog -->
<div class="hide">
	<div id="termOfServiceWindow" title="Term Of Service" class="input-form dialog-form"></div>
</div>
<script type="text/javascript">
$(document).ready( function() {

	$('button').button();
	
	$('#termsOfService').click( function() {
		// Open new dialog
		$('#termOfServiceWindow').openDialog({
			autoOpen: false,
			height: 250,
			width: 300,
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
		$('#termOfServiceWindow').dialog('open');
		return false;
	});

	$('#registerBtn').click( function() {
		if($("#customerRegisterNewUserForm_termsOfService").prop('checked') != true){
			$.error({message: "In order to use our services, you must agree to ClevverMail's Terms of Service."});
	        return;
		}
		var submitUrl = $('#customerRegisterNewUserForm').attr('action');
		$.ajaxSubmit({
			url: submitUrl,
			formId: 'customerRegisterNewUserForm',
			success: function(data) {
				if (data.status) {
					//var message = data.message + "<br/> Please login our clevvermail with below link to setup your account. <br> <?php echo APContext::getFullBasePath();?>/customers";
					//$.displayInfor(message);
					$('#login-form-hidden-username').val($('#customerRegisterNewUserForm_email').val());
					$('#login-form-hidden-password').val($('#customerRegisterNewUserForm_password').val());
					$.ajaxSubmit({
						url: '<?php echo base_url()?>customers/login',
						formId: 'login-form-hidden',
						success: function(data) {
							window.top.location.href="<?php echo base_url()?>mailbox";
						}
					});
// 					$.ajax({
// 				      type: "POST",
//				      url: '<?php echo base_url()?>/customers/login',
// 				      data: $("#login-form-hidden").serializeArray(),
// 				      dataType: "json",
// 				      success: function(data){
// 				        //if (data.status) {
//				    	  window.top.location.href="<?php echo base_url()?>/customers";
// 				        //}else {
// 				        //	$.displayError(data.message);
// 				        //}
// 				      }
// 				    });
				} else {
					$.displayError(data.message);
				}
			}
		});
		return false;
	});
});
</script>