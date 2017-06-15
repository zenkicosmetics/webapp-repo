<?php
$customer_id = APContext::getParentCustomerCodeLoggedIn();

$support_email = AccountSetting::get($customer_id, APConstants::CUSTOMER_SUPPORT_EMAIL_KEY);
if(empty($support_email)){
    $support_email = "";
}
$support_phone = AccountSetting::get($customer_id, APConstants::CUSTOMER_SUPPORT_PHONE_KEY);
if(empty($support_phone)){
    $support_phone = "";
}
?>
<div style="text-align: center;font-size: 16px;">For support or feedback, please contact us:</div>
<br />
<?php if(!empty($support_email)){ ?>
<div style="font-size: 14px; text-align: center">E-Mail: <a style='color: #0e76bc;font-size: 14px;' href='mailto:<?php echo $support_email ?>'><?php echo $support_email ?></a></div>
<?php }?>
<?php if(!empty($support_phone)){ ?>
<div style="font-size: 14px; text-align: center; margin-top: 10px;">Phone: <a style='color: #0e76bc;font-size: 14px;' href="tel:<?php echo $support_phone ?>"><?php echo $support_phone ?></a></div>
<?php }?>
<br />
<br />
<br />
<br />
<br />
<br />
<div style="text-align: center;"><a style="color: #0e76bc;font-size: 12px;" id="freshWidgetTechniqueSupportUserButton" href="#">For technical questions regarding the system click here..</a></div>