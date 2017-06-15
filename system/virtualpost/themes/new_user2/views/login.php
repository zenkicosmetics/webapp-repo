<!DOCTYPE html>
<html>
<head>

<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">

<title>ClevverMail</title>
<link type="image/png" href="<?php echo APContext::getAssetPath()?>images/favicon2.png" rel="icon">
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
				<form id="loginForm" action="<?php echo base_url()?>customers/login" method="post">
					<ul>
						<li><label>Username</label>
							<span> <input type="text" value="" id="user_name" placeholder="" name="user_name" class="username input-width" /></span>
						</li>
						<li><label>Password</label> <span>
							<input type="password" value="" placeholder=""  id="password" name="password" class="password input-width" autocomplete="off" />
						</span></li>
						<li><label style="line-height: 26px;">
						    <input type="checkbox" id="remember_me"
									name="remember_me" value="1" tabindex="3"
									class="input-checkbox customCheckbox" />
						    <span> 
						         Remember Me
							</span>
						</label></li>
						<li style="text-align: center;">
						    <label style="text-align: left; float: left;"> <a id="forgotPassword"
								href="#" class="tooltip">Forgot your password?</a>
						    </label>
						    
						</li>
						<li style="text-align: left; margin-top:-12px;float:left; clear:both">
						    <label style="text-align: left;"> <a id="registerNewUser" href="#" class="tooltip">Register</a></label>
						    
						</li>
						<li style="text-align: center; clear:both">
						    <label style="text-align: left; float:left;margin-left:60px">
								<input id="btnLogin" class="input-btn c yl submit-login" type="submit" style="width: 150px;" value="Login" />
						    </label>
						</li>
					</ul>
					<div class="clear"></div>
				</form>
			</div>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
	</div>
	
	<!-- Content for dialog -->
    <div class="hide">
    	<div id="resetPasswordWindow" title="Reset Password" class="input-form dialog-form">
    	</div>
    	<div id="registerNewCustomerWindow" title="Register New Customer" class="input-form dialog-form">
    	</div>
    </div>
    
<script type="text/javascript">
var IMAGE_PATH = '<?php echo APContext::getImagePath()?>';
$(document).ready( function() {
	// Apply common control by jQuery UI
    $.initPage();

    // Check current main screen
    if ($('#hiddenCheckCurrentMainScreen').val() == '1' || window.parent.$('#hiddenCheckCurrentMainScreen').val() == '1') {
    	window.parent.document.location = '<?php echo base_url()?>admin/logout';
    }

    // Apply checkbox style
    $('input:checkbox.customCheckbox').checkbox({cls:'jquery-safari-checkbox'});
    $('#user_name').focus();

    if (localStorage.chkbx && localStorage.chkbx != '') {
        $('#remember_me').attr('checked', 'checked');
        $('#user_name').val(localStorage.usrname);
        $('#password').val(localStorage.pass);
    } else {
        $('#remember_me').removeAttr('checked');
        $('#user_name').val('');
        $('#password').val('');
    }

    /**
     * User click login button
     */
    $('#btnLogin').live('click', function() {
    	if ($('#remember_me').is(':checked')) {
            // save username and password
            localStorage.usrname = $('#user_name').val();
            localStorage.pass = $('#password').val();
            localStorage.chkbx = $('#remember_me').val();
        } else {
            localStorage.usrname = '';
            localStorage.pass = '';
            localStorage.chkbx = '';
        }
        $('#loginForm').submit();
    });

    /**
     * Process when user click to forgot password.
     */
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
				$(this).load("<?php echo base_url() ?>customers/forgot_pass", function() {
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
		var submitUrl = $('#customerForgotPasswordForm').attr('action');
		$.ajaxSubmit({
			url: submitUrl,
			formId: 'customerForgotPasswordForm',
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

	/**
	 * Register new user
	 */
	$('#registerNewUser, #reRegisterNewUser').live('click', function(){
		$(".ui-dialog-content").dialog("close");
		// Clear control of all dialog form
	    $('.dialog-form').html('');

	    // Open new dialog
		$('#registerNewCustomerWindow').openDialog({
			autoOpen: false,
			height: 300,
			width: 420,
			modal: true,
			open: function() {
				$(this).load("<?php echo base_url() ?>customers/register", function() {
					$('#customerRegisterNewUserForm_email').focus();
				});
			},
			buttons: {
				'Submit': function() {
					submitRegisterNewUser();
				},
				'Cancel': function () {
					$(this).dialog('close');
				}
			}
		});
		$('#registerNewCustomerWindow').dialog('option', 'position', 'center');
		$('#registerNewCustomerWindow').dialog('open');
	});

	/**
	 * Submit forgot password
	 */
	function submitRegisterNewUser() {
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
					$.displayInfor(data.message, null, function() {
					    document.location = '<?php echo base_url()?>mailbox';
					});
					$('#registerNewCustomerWindow').dialog('close');
				} else {
					$.displayError(data.message);
				}
			}
		});
	}
});
</script>

</body>
</html>