<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link type="image/png" href="<?php echo APContext::getAssetPath()?>images/favicon2.png" rel="icon">
<meta name="robots" content="noindex, nofollow" />
<?php Asset::css('jquery-ui-1.8.20.custom.css'); ?>
<?php //Asset::css('Aristo.css'); ?>
<?php //Asset::css('frame.css');?>
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
						<li><label for="userName">Username <span class="required">*</span></label>
							<span> <input type="text" value="" id="user_name" placeholder="" name="user_name" class="username input-width" /></span>
						</li>
						<li><label>Password <span class="required">*</span></label> <span>
							<input type="password" value="" placeholder=""  id="password" name="password" class="password input-width" />
						</span></li>
						<li><label> <span> <input type="checkbox" id="remember_me"
									name="remember_me" value="1" tabindex="3"
									class="input-checkbox tooltip" /> Remember Me
							</span>
						</label></li>
						<li style="text-align: center;">
						    <label style="text-align: left; float: left;"> <a id="forgotPassword"
								href="<?php echo base_url()?>users/reset_pass" class="tooltip">Forgot your password?</a>
						    </label>
						    <label style="text-align: left; float: right;padding-right: 22px;">
						        <input id="btnLogin" type="submit" name="submit" value=""
											class="submit-login" />
								
						    </label>
						</li>
					</ul>
					<div class="clear"></div>
				</form>
			</div>
		</div>
		<div class="clear"></div>
	</div>
</body>



<script type="text/javascript">
var IMAGE_PATH = '<?php echo APContext::getImagePath()?>';
$(document).ready( function() {
	// Apply common control by jQuery UI
    $.initPage();

    // Apply checkbox style
    //$('input:checkbox.customCheckbox').checkbox({cls:'jquery-safari-checkbox'});
    //$('span.jquery-safari-checkbox').css('height', 'inherit');

    $('#user_name').focus();
});
</script>
</html>