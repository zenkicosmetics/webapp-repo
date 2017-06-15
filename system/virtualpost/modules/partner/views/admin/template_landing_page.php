<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">

<!-- You can use .htaccess and remove these lines to avoid edge case issues. -->
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

<title>ClevverMail DEV - Widget</title>
<link type="image/png" href="<?php echo APContext::getAssetPath(); ?>images/favicon2.png" rel="icon">

<base href="<?php echo APContext::getAssetPath(); ?>" />

<!-- Mobile viewport optimized -->
<meta name="viewport" content="width=960, initial-scale=1, maximum-scale=1" />
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">

<!-- metadata needs to load before some stuff -->
<!-- CSS -->

<!-- JS -->


<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<link rel="stylesheet" href="<?php echo APContext::getAssetPath(); ?>/system/virtualpost/themes/widget/css/jquery-ui.min.css" />
<link rel="stylesheet" href="<?php echo APContext::getAssetPath(); ?>/system/virtualpost/themes/widget/css/Aristo.css" />
<link rel="stylesheet" href="<?php echo APContext::getAssetPath(); ?>/system/virtualpost/themes/widget/css/styles.css" />
<script src="<?php echo APContext::getAssetPath(); ?>/system/virtualpost/themes/widget/js/jquery2.1.3.min.js"></script>
<script src="<?php echo APContext::getAssetPath(); ?>/system/virtualpost/themes/widget/js/jquery-ui.min.js"></script>
<script src="<?php echo APContext::getAssetPath(); ?>/system/virtualpost/themes/widget/js/jquery.blockUI.js"></script>
<script src="<?php echo APContext::getAssetPath(); ?>/system/virtualpost/themes/widget/js/jquery.common.js"></script>
<script src="<?php echo APContext::getAssetPath(); ?>/system/virtualpost/themes/widget/js/jquery.slimscroll.min.js"></script>
</head>
<body id="user-page">
	<div id="MainPageId" style="text-align: center">
		<form id="customerRegisterNewUserForm" method="post" class="dialog-form" action="/clevvermail/customers/register">
	<h2 class="title"><img src="<?php echo APContext::getAssetPath(); ?>/images/favicon2.png" /><label>Register now for free</label></h2>
	<table style="display: inline">
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
	<input type="hidden" name="partner_id" value="" />
</form>
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
				$(this).load("<?php echo APContext::getFullBasePath()?>customers/term_of_service", function() {
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
					var message = data.message + "<br/> Please login our clevvermail with below link to setup your account. <br> <?php echo APContext::getFullBasePath(); ?>customers";
					$.displayInfor(message);
				} else {
					$.displayError(data.message);
				}
			}
		});
		return false;
	});
});
</script>	
</div>
</body>
</html>