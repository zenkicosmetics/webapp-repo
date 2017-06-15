<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
    .container tr td, .container tr th,  .payment_method tr td, .payment_method tr th {
        border: solid 1px #ccc;
    }
    .container, .payment_method { width: 100%;}
    .payment_method tr th{
        font-weight: bold;
    }
    td#milestone img{
        width: 100%;
    }
</style>
</head>

<body>
<table class="container" width="100%" border="0" cellpadding="6" cellspacing="0">
      <tr>
          <th style="" colspan="2" align="center">
            CUSTOMER ID: <?php echo $customer->customer_code; ?> - Email: <?php echo $customer->email; ?> <?php if(!empty($customer_address)){?> <?php echo !empty($customer_address->invoicing_address_name) ? " - ". ucwords(strtolower($customer_address->invoicing_address_name)) : "" ; ?>  <?php echo !empty($customer_address->invoicing_company) ? " - ". ucwords(strtolower($customer_address->invoicing_company)): ""; ?> <?php } ?></th>
      </tr>
      <?php if(!empty($customer_address)) { ?>
      <tr>
        <td style="padding: 0px;"><strong><?php admin_language_e('cases_view_todo_view_verifi_detail_ForwardingAddress'); ?>:</strong> <br/><?php  echo ucwords(strtolower($customer_address->shipment_address_name))."<br/>";
                   echo ucwords(strtolower($customer_address->shipment_company))."<br/>";
                   echo ucwords(strtolower($customer_address->shipment_street))."<br/>";
                   echo ucwords(strtolower($customer_address->shipment_postcode))."<br/>";
                   echo ucwords(strtolower($customer_address->shipment_region))."<br/>";
                   echo ucwords(strtolower($customer_address->shipment_country_name))."<br/>";
            ?>
        </td>
         <td style="text-align: right;"><strong>Invoicing address:</strong> <br/>
            <?php  echo ucwords(strtolower($customer_address->invoicing_address_name))."<br/>";
                   echo ucwords(strtolower($customer_address->invoicing_company))."<br/>";
                   echo ucwords(strtolower($customer_address->invoicing_street))."<br/>";
                   echo ucwords(strtolower($customer_address->invoicing_postcode))."<br/>";
                   echo ucwords(strtolower($customer_address->invoicing_region))."<br/>";
                   echo ucwords(strtolower($customer_address->invoicing_country_name))."<br/>";
            ?>
        </td>
      </tr>
    <?php } ?>
      <tr>
          <td colspan="2" style=""><br/><strong><?php admin_language_e('cases_view_todo_view_verifi_detail_ListPostbox'); ?> <?php if(!empty($location_name)){ echo " of customer ".$customer->customer_code." at ".$location_name;} ?>:</strong> <br/>
              <?php $i=0;foreach ($listPostboxes as $postbox) {$i++;

              if($i < 10){
                 $info_postbox = "Postbox $i: &nbsp; Postbox ID: ".$postbox->postbox_id." - Postbox Code: ".$postbox->postbox_code;
              }
              else {
                  $info_postbox = "Postbox $i: Postbox ID: ".$postbox->postbox_id." - Postbox Code: ".$postbox->postbox_code;
              }
              if(!empty($postbox->name)){
                  $info_postbox .= " - Name: ".ucwords(strtolower(strtolower($postbox->name)));
              }
              if(!empty($postbox->company)){
                  $info_postbox .= " - Company: ".ucwords(strtolower($postbox->company));
              }
              $info_postbox .= "<br/>";
              echo $info_postbox;
              } ?>

          </td>
      </tr>
      <tr>
        <td  colspan="2" style="padding: 0px; cellpadding: 0;">
            <table style="margin: 0px;" width="100%" border="0" class="payment_method" id="tbl_payment_method" cellpadding="6" cellspacing="0"> <thead><tr>
                        <th><strong><?php admin_language_e('cases_view_todo_view_verifi_detail_Standard'); ?></strong></th>
                        <th><strong><?php admin_language_e('cases_view_todo_view_verifi_detail_Type'); ?></strong></th>
                        <th><strong><?php admin_language_e('cases_view_todo_view_verifi_detail_Name'); ?></strong></th>
                        <th><strong><?php admin_language_e('cases_view_todo_view_verifi_detail_CardNo'); ?></strong></th>
                        <th><strong><?php admin_language_e('cases_view_todo_view_verifi_detail_ExpDate'); ?></strong></th>
                        <th><strong><?php admin_language_e('cases_view_todo_view_verifi_detail_3DSecure'); ?></strong></th>
                        <th><strong><?php admin_language_e('cases_view_todo_view_verifi_detail_Valid'); ?></strong></th>
                    </tr> </thead>
            <tbody>
            <?php
            if(!empty($customer_address)){
            $flag = true;
                  if($customer->invoice_type == '2' && !empty($customer->invoice_code)):
                  $flag = false;
            ?>
                <tr>
                   <td class="center-align"><input class="customCheckbox select_primarycard" type="radio" data-id="" checked="checked" /></td>
                   <td>Invoice</td>
                   <td><?php echo ucwords(strtolower($customer_address->invoicing_address_name)).' - '.ucwords(strtolower($customer_address->invoicing_company));?></td>
                   <td>&nbsp;</td>
                   <td>&nbsp;</td>
                   <td>&nbsp;</td>
                   <td>&nbsp;</td>

                </tr>
            <?php endif; } ?>

            <?php if($list_payment_method){?>
      <?php foreach($list_payment_method as $payment_method){?>
            <tr>
    <td class="center-align">
                    <input class="customCheckbox select_primarycard" type="radio" data-id="<?php echo $payment_method->payment_id?>" <?php if ($flag && $payment_method->primary_card == '1') { ?> checked="checked" <?php }?> />
                </td>
    <td class="left-align">
                <?php
    if ($payment_method->account_type == APConstants::PAYMENT_CREDIT_CARD_ACCOUNT) {
                    switch ($payment_method->card_type) {
                        case 'V' :
                            echo "VISA";
                            break;
                        case 'M' :
                            echo "MasterCard";
                            break;
                        case 'J' :
                            echo "JCB";
                            break;
                    }
                } else if ($payment_method->account_type == APConstants::PAYMENT_PAYPAL_ACCOUNT) {

                    echo "Paypal Account";
                }
                ?></td>
    <td class="left-align"><?php echo $payment_method->card_name;?></td>
    <td class="left-align"><?php echo $payment_method->card_number;?></td>
    <td class="left-align">
    <?php if (empty($payment_method->expired_month) && empty($payment_method->expired_year)) {
                    echo 'No information';
    } else {
                    $month = APUtils::getCurrentMonth();
                    $year = APUtils::getCurrentYearShort();
                    if($year > $payment_method->expired_year || ($year == $payment_method->expired_year && $month > intval($payment_method->expired_month))){
                        echo "Expired";
                    }else{
                        echo ($payment_method->expired_month.'/'.($payment_method->expired_year + 2000));
                    }
                }?>
    </td>
    <td class="center-align">
                    <?php if ($payment_method->secure_3d_flag == APConstants::ON_FLAG) {
                                echo 'Yes';
                        } else {
                                echo 'No';
                    }?>
    </td>
                <td class="center-align">
                    <?php
                    if ($payment_method->card_charge_flag == APConstants::CARD_CHARGE_OK) {
                        echo 'OK';
                    } elseif ($payment_method->card_charge_flag == APConstants::CARD_CHARGE_FAIL) {
                        echo 'FAIL';
                    } else {
                        echo 'N.A.';
                    }
                    ?>
                </td>

    </tr>
                <?php }?>
                <?php }else if($customer->invoice_type == '2' && empty($customer->invoice_code)){?>
                    <tr>
                            <?php if ($customer->activated_flag == '1') {?>
                            <td class="center-align"></td>
                            <td class="center-align">Invoice</td>
                            <td class="center-align"></td>
                            <td class="center-align"></td>
                            <td class="center-align"></td>
                            <td class="center-align"></td>
                            <td class="center-align"></td>
                            <?php } else {?>
                                        <td class="center-align" colspan="7">There is no account.</td>
                            <?php }?>
                        </tr>
                <?php }?>
            </tbody>
      </table>
          </td>
      </tr>
</table>
<br/><br/>
<table class="container" width="100%" border="0" cellpadding="6" cellspacing="0">
     <?php if(count($data_postbox)) { ?>
      <?php $k = 0;foreach ($data_postbox as $postbox) { $k++; ?>
      <?php if($k>1){ ?>
        <tr><td style="border: none;" colspan="2"></td></tr>
        <tr><td style="border: none;" colspan="2"></td></tr>
      <?php } ?>
      <tr>
        <th style="" colspan="2" align="center">
            CUSTOMER ID: <?php echo $customer->customer_code; ?> - Email: <?php echo $customer->email; ?> <?php if(!empty($customer_address)){?> <?php echo !empty($customer_address->invoicing_address_name) ? " - ". ucwords(strtolower($customer_address->invoicing_address_name)) : "" ; ?>  <?php echo !empty($customer_address->invoicing_company) ? " - ". ucwords(strtolower($customer_address->invoicing_company)): ""; ?> <?php } ?></th>
    </tr>
    <tr>
        <td colspan="2" style="border: none;border-left: solid 1px #ccc;border-right: solid 1px #ccc;"><?php echo $postbox['info_postbox'];?>
        </td>
    </tr>

    <tr>
        <td id="milestone" align="center" colspan="2" style="border: none;border-left: solid 1px #ccc;border-right: solid 1px #ccc;border-bottom: solid 1px #ccc;padding: 0px;">
            <?php if(count($postbox['arr_postbox'])){  ?>
            <table align="center" class="container" width="100%" border="0" cellpadding="0" cellspacing="0">

           <?php
            for($i = 0; $i < count($postbox['arr_postbox']); $i++){ ?>
                <tr><td colspan="2" align="left;" style="border: none;">
                <p style="text-align: left;"><?php echo "Milestone name: ".$postbox['arr_postbox'][$i]['milestone_name']."<br/>";
                //echo "base_task_name: ".$postbox['arr_postbox'][$i]['base_task_name']."<br/>";
                //echo "CASE ID: ".$postbox['arr_postbox'][$i]['case_id']."<br/>";
                echo "Status of milestone: ".$postbox['arr_postbox'][$i]['status']."<br/>"; ?>
                </p></td></tr>
                <?php
                if(isset($postbox['arr_postbox'][$i]['list_file_id'])){
                if(count($postbox['arr_postbox'][$i]['list_file_id'])){

                if(count($postbox['arr_postbox'][$i]['list_file_id']) == 1){
                    $file1 = $postbox['arr_postbox'][$i]['list_file_id'][0];
                    $file_path1 = substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],"/")+1).$file1;
                ?>
                <tr>
                    <td colspan="2" align="left" style="text-align: left;border: none;">
                        <?php if(file_exists($file_path1)){?><img style="width: 320px;" src="<?php echo base_url().$file1;?>"/>
                    <?php } else{ echo "Not found: ".$file_path1."<br/>";  } ?>
                    </td>
                </tr>
                <?php
                }
                else{
                    for($k=0;$k < count($postbox['arr_postbox'][$i]['list_file_id']) ; $k += 2){
                    ?>
                    <?php if( ($k+2) <= count($postbox['arr_postbox'][$i]['list_file_id']) ){
                        $file1 = $postbox['arr_postbox'][$i]['list_file_id'][$k];
                        $file2 = $postbox['arr_postbox'][$i]['list_file_id'][$k+1];
                        $file_path1 = substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],"/")+1).$file1;
                        list($width, $height) = getimagesize($file1);

                        $file_path2 = substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],"/")+1).$file2;
                    ?>
                    <tr>

                    <td valign="bottom" align="center" style="text-align: center;border: none;width: 50%;padding: auto 10 0px;">
                        <?php if(file_exists($file_path1)){ ?>

                        <img style="height: <?php echo $height."px"; ?>; margin-left: 20px;margin-bottom: 0px;" src="<?php echo base_url().$file1;?>"/>
                    <?php } else{ echo "Not found: ".$file_path1."<br/>";  } ?>
                    </td>
                        <td valign="bottom" align="center" style="text-align: center;border: none;width: 50%;padding-left: 10px; padding: auto 10 0px;">
                        <?php if(file_exists($file_path2)){?><img style="height: <?php echo $height."px"; ?>;margin-left: 20px;margin-bottom: 0px;" src="<?php echo base_url().$file2;?>"/>
                    <?php } else{ echo "Not found: ".$file_path2."<br/>";  } ?>
                    </td>

                    </tr>

                    <?php } else if($k == (count($postbox['arr_postbox'][$i]['list_file_id']) - 1)){
                     $file1 = $postbox['arr_postbox'][$i]['list_file_id'][$k];
                      $file_path1 = substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],"/")+1).$file1;
                     ?>
                    <tr>
                        <td colspan="2" align="left" style="text-align: left;border: none;">
                        <?php if(file_exists($file_path1)){?>
                        <img style="width: 320px;" src="<?php echo base_url().$file1;?>"/>
                        <?php } else{ echo "Not found: ".$file_path1."<br/>";  } ?>
                        </td>
                    </tr>
                    <?php
                    }
                    } // end for list file
                } // end if milestone many than one file
                }
                }

            }
            ?>

            </table>

            <?php } //if(count($postbox['arr_file_id']))  ?>
        </td>
    </tr>

    <?php  } ?>

   <?php } //End if(count($data_postbox)) ?>

</table>


</body>
</html>