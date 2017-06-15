<?php
$submit_url = base_url().'payment/add';

?>

<form action="https://secure.pay1.de/frontend/" method="POST">
    <input type="hidden" name="portalid" value="<?php echo $portal_id?>">
    <input type="hidden" name="aid" value="<?php echo $sub_account_id?>">
    <input type="hidden" name="mode" value="<?php echo $mode?>">
    <input type="hidden" name="request" value="<?php echo $request?>">
    <input type="hidden" name="clearingtype" value="cc">
    <input type="hidden" name="currency" value="<?php echo $currency?>">
    <input type="hidden" name="reference" value="<?php echo $reference?>">
    <input type="hidden" name="productid" value="<?php echo $productid?>">
    <input type="hidden" name="autosubmit" value="yes">
    <input type="hidden" name="hash" value="<?php echo $hash?>">
    <input type="hidden" name="bankcountry" value="DE">
<table>
    <tr><td>Card Holder</td><td><input type="text" name="cardholder" value="Christian Hemmrich"></td></tr>
    <tr><td>Card number</td><td><input type="text" name="cardpan" value="5486225329055904"></td></tr>
    <tr><td>Card type</td><td><input type="text" name="cardtype" value="M"></td></tr>
    <tr><td>Expiry year</td><td><input type="text" name="cardexpireyear" value="2017"></td></tr>
    <tr><td>Expiry month</td><td><input type="text" name="cardexpiremonth" value="01"></td></tr>
    <tr><td>Credit card security number</td><td><input type="text" name="cardcvc2" value="738"></td></tr>
</table>
    <input type="submit" value="Buy now!">
</form>