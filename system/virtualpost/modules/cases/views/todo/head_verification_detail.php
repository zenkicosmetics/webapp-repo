<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>
#tbl_payment_method{width: 100%;}
#tbl_payment_method tfoot td{
    border-top:1px solid #dadada;
}
#tbl_payment_method thead th {
    border: solid 1px #ddd;
}
#tbl_payment_method tbody td {
    text-align: left;
    vertical-align: middle;
    border: solid 1px #ddd;
}
</style>
</head>

<body>

<table width="100%" border="0" cellpadding="6" cellspacing="0">
    <tr>
        <th style="border: 1px solid #ccc;" colspan="2" align="center">
            CUSTOMER ID: <?php echo $customer->customer_code; ?> – Email: <?php echo $customer->email; ?> <?php if(!empty($customer_address)){?> – <?php echo ucwords(strtolower($customer_address->invoicing_address_name)); ?> – <?php echo ucwords(strtolower($customer_address->invoicing_company)); ?> <?php } ?></th>
    </tr>
     <tr>
          <td colspan="2" style="line-height: 6px;"><?php echo $info_postbox;?>
          </td>
      </tr>
    <?php if(count($arr_file_id)){  ?>
    <tr>
        <td colspan="2" style="text-align: center;">
            <?php foreach ($arr_file_id as $file) {  ?>
            <img style="width: 300px;" src="<?php echo base_url().$file;?>"/><br/>
            <?php }  ?>
        </td>
    </tr>
     <?php }  ?>
</table>

</body>
</html>