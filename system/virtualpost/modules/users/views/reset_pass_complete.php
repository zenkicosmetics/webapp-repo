<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ClevverMail - Reset Password</title>

<meta name="robots" content="noindex, nofollow" />
<?php Asset::css('jquery-ui-1.8.20.custom.css'); ?>
<?php Asset::css('Aristo.css'); ?>
<?php Asset::css('login.css');?>
<?php Asset::css('styles.css');?>

<?php Asset::js('jquery-1.7.2.min.js'); ?>
<?php Asset::js('jquery.blockUI.js'); ?>
<?php Asset::js('jquery-ui-1.8.20.custom.min.js'); ?>
<?php Asset::js('jquery.checkbox.min.js'); ?>
<?php Asset::js('jquery.common.js'); ?>

<?php echo Asset::render();?>
<?php $ci = get_instance();?>
</head>

<body>

	<div class="login-container reset_pass_complete">
		<div class="login-box">
			<h2><?php echo lang('user_reset_title_form') ?></h2>
			<div class="login-form">
				<div class="error">
				    <?php
					if(!empty($this->session->flashdata('error_string'))){

						echo $this->session->flashdata('error_string');
					}
					else if(!empty($this->session->flashdata('success_string'))){
						echo $this->session->flashdata('success_string');
					}
                  ?>
				</div>
				<form id="resetComplete" action="<?php echo base_url()?>users/reset_pass_complete" method="post" autocomplete="off">
					<ul style="margin-top: 20px;">
						<li><label><?php echo lang('user_reset_email') ?></label> <span>
							<input type="text" value="<?php echo $email;?>" disabled="disabled" class="input-width" />
						</li>
						<li><label><?php echo lang('user_reset_new_password') ?></label> <span>
							<input type="password" value="" id="new_password" name="new_password" class="new_password input-width" />
						</li>
						<li><label><?php echo lang('user_reset_confirm_new_password') ?></label>
							<input type="password" id="confirm_new_password"  name="confirm_new_password" class="confirm_new_password input-width" />
						</li>
						<li style="text-align: center; clear:both; margin-top: 12px">
						    <label style="text-align: left; float:left;margin-left:58px">
								<input id="btnresetComplete" class="input-btn c yl" type="button" style="width: 150px;" value="Reset Password" />
						    </label>
						</li>
					</ul>
					<div class="clear"></div>
					<input type="hidden"  name="key" value="<?php echo $key; ?>" />
					<input type="hidden"  name="email" value="<?php echo $email; ?>" />
				</form>
			</div>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
	</div>
	<div class="hide">
		<div id="resetPasswordWindow" title="Reset Password" class="input-form dialog-form">
		</div>
	</div>
<script type="text/javascript">
$(document).ready( function() {

	$('#confirm_new_password').keypress(function(e) {
		if (e.keyCode == 13) {
			$("#btnresetComplete").click();
		}
	});
	$("#btnresetComplete").click(function(){

		var new_password         = $("#new_password").val();
		var confirm_new_password = $("#confirm_new_password").val();
		if((new_password.length < 6) || (new_password.length < 6) ){
			$("div.error").text('<?php echo lang('user_reset_new_password_length_error'); ?>');
			return false;
		}
		else if((new_password != confirm_new_password)){

			console.log("new_password: "+new_password);
			console.log("new_password: "+confirm_new_password);

			$("div.error").text('<?php echo lang('reset_pass_new_pass_not_same_confirm_new_passs'); ?>');
			return false;
		}

		$("#resetComplete").submit();
	});
	/*
	var error_string = '<?php //echo $this->session->flashdata('error_string'); ?>';
	if(error_string !=''){
		$.displayError(error_string);
	}
	*/
});
</script>

</body>
</html>