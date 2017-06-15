<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ClevverMail</title>
<link type="image/png" href="<?php echo APContext::getAssetPath()?>images/favicon2.png" rel="icon">
<meta name="robots" content="noindex, nofollow" />
<?php Asset::css('jquery-ui-1.8.20.custom.css'); ?>
<?php Asset::css('Aristo.css'); ?>
<?php //Asset::css('frame.css');?>
<?php //Asset::css('style.css');?>
<?php //Asset::css('login.css');?>
<?php Asset::css('login.css');?>
<?php Asset::css('styles.css');?>

<?php Asset::js('jquery-1.7.2.min.js'); ?>
<?php Asset::js('jquery.blockUI.js'); ?>
<?php Asset::js('jquery-ui-1.8.20.custom.min.js'); ?>
<?php Asset::js('jquery.checkbox.min.js'); ?>
<?php Asset::js('jquery.common.js'); ?>

<?php echo Asset::render();?>

</head>


<body id="login">

	<div class="login-container">
		<div class="login-box">
			<h2>Login</h2>
			<div class="login-form">
				<div class="error">
				    <?php if (validation_errors()): ?>
                    	<?php echo validation_errors(); ?>
                    <?php endif; ?>
				</div>
				<form id="loginForm" action="<?php echo base_url()?>admin/login" method="post">
					<ul>
						<li><label for="userName">Email</label>
							<span><input type="text" id="email" name="email"
								class="input-width tooltip" title="User name" value="" size="30" style="width: 250px"
								maxlength="2048" tabindex="1" /></span>
						</li>
						<li><label>Password</label> <span>
							<input type="password" id="password" tabindex="2"
								name="password" class="input-width tooltip" title="Password" style="width: 250px"
								value="" size="30" maxlength="2048" autocomplete="off" />
						</span></li>
						<li><label> <span> <input type="checkbox" id="remember_me"
									name="remember_me" value="1" tabindex="3"
									class="input-checkbox customCheckbox" /> Remember Me
							</span>
						</label></li>
						<li style="text-align: center;">
						    <label style="text-align: left; float: left;"> 
								<!--<?php //echo base_url()?>users/reset_pass -->
								<a id="forgotPassword" href="#" class="tooltip">Forgot your password?</a>
						    </label>
						    <label style="text-align: left; float: right;margin-right:22px">
						        <input id="btnLogin" class="input-btn c yl submit-login" type="submit" style="width: 80px;" value="Login" />
						    </label>
						</li>
					</ul>
					<div class="clear"></div>
				</form>
			</div>
		</div>
		<div class="clear"></div>
	</div>

	<!-- Content for dialog -->
    <div class="hide">
    	<div id="resetPasswordWindow" style="width: auto; min-height: 0px; padding-top: 26px; height: 54px;" title="Reset Password" class="input-form dialog-form">
    	</div>
	</div>

</body>

<script type="text/javascript">
var IMAGE_PATH = '<?php echo APContext::getImagePath()?>';
$(document).ready( function() {
	
	$('#forgotPassword').click(function(){
    	// Clear control of all dialog form
	    $('.dialog-form').html('');

	    // Open new dialog
		$('#resetPasswordWindow').openDialog({
			autoOpen: false,
			height: 180,
			width: 400,
			modal: true,
			open: function() {
				$(this).load("<?php echo base_url()?>users/reset_pass", function() {
					$('#customerForgotPasswordForm_email').focus();
				});
			},
			buttons: {
				'Submit': function() {
					submitForgotPass();
				},
				'Cancel': function () {
					$(this).dialog('close');
				}
			}
		});
		$('#resetPasswordWindow').dialog('option', 'position', 'center');
		$('#resetPasswordWindow').dialog('open');

		// 20141003 DuNT Start fixbug #409
		$('#resetPasswordWindow').keypress(function(e) {
	        if (e.keyCode == $.ui.keyCode.ENTER) {
	        	submitForgotPass();
	        	return;
	        }
	    });
		// 20141003 DuNT End fixbug #409
    });
	/**
	 * Submit forgot password
	 */
	function submitForgotPass() {
		var submitUrl = $('#userForgotPasswordForm').attr('action');
		$.ajaxSubmit({
			url: submitUrl,
			formId: 'userForgotPasswordForm',
			success: function(data) {
				if (data.status) {
					$.displayInfor(data.message);
					$('#resetPasswordWindow').dialog('close');
				} else {
					$.displayError(data.message);
				}
			}
		});
	}

	// Check current main screen
    if ($('#hiddenCheckCurrentMainScreen').val() == '1' || window.parent.$('#hiddenCheckCurrentMainScreen').val() == '1') {
    	window.parent.document.location = '<?php echo base_url()?>admin/logout';
    }
    
	// Apply common control by jQuery UI
    $.initPage();

    // Apply checkbox style
    $('input:checkbox.customCheckbox').checkbox({cls:'jquery-safari-checkbox'});
    $('#email').focus();

    if (localStorage.chkbx && localStorage.chkbx != '') {
        $('#remember_me').attr('checked', 'checked');
        $('#email').val(localStorage.usrname);
        $('#password').val(localStorage.pass);
    } else {
        $('#remember_me').removeAttr('checked');
        $('#email').val('');
        $('#password').val('');
    }

    /**
     * User click login button
     */
    $('#btnLogin').live('click', function() {
    	if ($('#remember_me').is(':checked')) {
            // save username and password
            localStorage.usrname = $('#email').val();
            localStorage.pass = $('#password').val();
            localStorage.chkbx = $('#remember_me').val();
        } else {
            localStorage.usrname = '';
            localStorage.pass = '';
            localStorage.chkbx = '';
        }
        $('#loginForm').submit();
    });

	var change_pass = '<?php echo $this->input->get("change_pass");; ?>';
	console.log("change_pass: "+change_pass);
	if(change_pass){
		<?php ci()->lang->load('users/user'); ?>
		$.displaySuccess("<?php echo lang('reset_pass_successful'); ?>");
	}
    
});
</script>
</html>