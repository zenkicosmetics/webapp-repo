<!DOCTYPE html>
<html>
    <head>

        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="0">

        <title>ClevverMail</title>
        <link type="image/png" href="<?php echo APContext::getAssetPath() ?>images/favicon2.png" rel="icon">
        <meta name="robots" content="noindex, nofollow" />
        <?php Asset::css('jquery-ui-1.8.20.custom.css'); ?>
        <?php Asset::css('Aristo.css'); ?>
        <?php Asset::css('login.css'); ?>
        <?php Asset::css('styles.css'); ?>

        <?php Asset::js('jquery-1.7.2.min.js'); ?>
        <?php Asset::js('jquery.blockUI.js'); ?>
        <?php Asset::js('jquery-ui-1.8.20.custom.min.js'); ?>
        <?php Asset::js('jquery.checkbox.min.js'); ?>
        <?php Asset::js('jquery.common.js'); ?>

        <?php echo Asset::render(); ?>
        <?php $ci = get_instance(); ?>
    </head>

    <body id="login">

        <div class="login-box" style="background: none;height: 230px">
            <h2> 
            <?php
                if (empty($title)) {
                    echo "Login to ClevverMail";
                } else {
                    echo $title;
                }
            ?>
            </h2>
            <div class="login-form" >
                <div class="error">
                    <?php if (validation_errors()): ?>
                        <?php echo validation_errors(); ?>
                    <?php endif; ?>
                </div>
                <form id="loginForm" action="<?php echo base_url() ?>customers/login" method="post">
                    <ul>
                        <li><label>E-mail</label>
                            <span> <input type="text" value="" id="user_name" placeholder="" name="user_name" class="username input-width" style="margin: 0px" /></span>
                        </li>
                        <li><label>Password</label> <span>
                                <input type="password" value="" placeholder="" id="password" name="password" class="password input-width" style="margin: 0px" autocomplete="off" />
                            </span></li>

                        <li style="text-align: center; clear:both; margin-top: 10px">
                             <?php
                                if (empty($button_text)) {
                                    $button_text = "Login";
                                }
                            ?>                               

                            <label style="text-align: left; float:left;margin-left:60px">
                                <input id="btnLogin" class="input-btn c yl submit-login" type="submit" style="width: 150px;" value="<?php echo $button_text;?>" />
                            </label>
                        </li>
                    </ul>
                    <input type="hidden" name="login_type" value="widget" />
                    <input type="hidden" name="token" value="<?php echo $token;?>" />
                    <div class="clear"></div>
                </form>
            </div>
            <div class="clear"></div>
        </div>
        <script type="text/javascript">
            var IMAGE_PATH = '<?php echo APContext::getImagePath() ?>';
            $(document).ready(function () {
                // Apply common control by jQuery UI
                $.initPage();

                $('#btnLogin').live('click', function () {
                    $('#loginForm').submit();
                });
            });
        </script>

    </body>
</html>