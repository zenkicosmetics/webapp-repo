<!-- Customer information  -->
<table style="size: 8px; width: 675px">
    <tr>
        <td style="width: 50%; text-align: left;padding-left: 0px; margin-left: 0px">
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
               <?php if($address){?>
                   <?php if (!empty($address->invoicing_address_name)) {echo $address->invoicing_address_name.'<br/>';}?>
                   <?php if (!empty($address->invoicing_company)) {echo $address->invoicing_company.'<br/>';}?>
                   <?php echo $address->invoicing_street?><br/>
                   <?php echo $address->invoicing_postcode  . ', ' . $address->invoicing_city ?><br/>
                   <?php echo $address->invoicing_country?><br/>
                   <?php if (!empty($customer->vat_number)) { echo 'VAT Number: '.$customer->vat_number;}?><br/>
               <?php }?>
        </td>
        <td style="width: 50%; text-align: right">
               <b><?php echo Settings::get(APConstants::INSTANCE_OWNER_COMPANY_CODE)?></b><br/>
               <?php echo Settings::get(APConstants::INSTANCE_OWNER_STREET_CODE)?> <br/>
               <?php echo Settings::get(APConstants::INSTANCE_OWNER_PLZ_CODE).' '.Settings::get(APConstants::INSTANCE_OWNER_CITY_CODE);?><br/>
               <?php echo Settings::get(APConstants::INSTANCE_OWNER_COUNTRY_CODE)?><br/>
               Telefon: <?php echo Settings::get(APConstants::INSTANCE_OWNER_TEL_INVOICE_CODE)?><br/>
               Fax: <?php echo Settings::get(APConstants::INSTANCE_OWNER_FAX_CODE)?><br/>
               <?php echo Settings::get(APConstants::INSTANCE_OWNER_MAIL_INVOICE_CODE)?><br/><br/>
   
               VAT: <?php echo Settings::get(APConstants::INSTANCE_OWNER_VAT_NUM_CODE)?><br/>
               <?php echo Settings::get(APConstants::INSTANCE_OWNER_REGISTERED_NUM_CODE)?><br/>
               Directors:<br/>
               <?php 
                   $directors = Settings::get(APConstants::INSTANCE_OWNER_DIRECTOR_CODE);
                   $director_arr = explode(",", $directors);
                   $display_director = implode(', <br/>', $director_arr);
                   echo $display_director;
               ?>
               <br/><br/>
               <?php echo Settings::get(APConstants::INSTANCE_OWNER_CITY_CODE)?>, &nbsp;<?php echo date(APConstants::DATEFORMAT_OUTPUT_PDF)?><br/>
        </td>
    </tr>
</table>
<!-- invoice billing number -->
<b style="text-align: left;">Credit Note: <?php echo $row->invoice_code;?></b>
<br/>
<div>
<!-- content -->
<table border="1px" style="size: 7px;">
    <tr>
        <th align="center" style="width: 60px">Position</th>
        <th align="center" style="width: 280px">Description</th>
        <th align="center" style="width: 66px">quantity</th>
        <th align="center" style="width: 66px">Net price</th>
        <th align="center" style="width: 66px">Gross Price</th>
        <th align="center" style="width: 66px">Net total</th>
        <th align="center" style="width: 66px">Gross total</th>
    </tr>
    <tbody>
        <?php $i=0;?>
        <?php foreach ($invoices_transaction as $tran) {?>
        <?php if(abs($tran->quantity * $tran->gross_price) >= 0.01){ ?>
        <tr>
            <td style="text-align: left;"><?php $i++; echo $i;?></td>
            <td align="left"><?php echo $tran->description. " - ". $tran->location_name?></td>
            <td align="right"><?php echo $tran->quantity?></td>
            <td align="right"><?php echo $tran->net_price?></td>
            <td align="right"><?php echo $tran->gross_price?></td>
            <td align="right"><?php echo APUtils::number_format($tran->quantity * $tran->net_price, 2)?></td>
            <td align="right"><?php echo APUtils::number_format($tran->quantity * $tran->gross_price, 2)?></td>
        </tr>
        <?php }?>
        <?php }?>
    </tbody>
</table>
<br/>
<table style="size: 8px;" border="0px">
    <tbody>
        <tr>
            <td style="text-align: left;width: 100px; border-bottom: 1px solid #000000">VAT EUR</td>
            <td align="left" style="width: 270px;border-bottom: 1px solid #000000"><?php echo APUtils::number_format((($vat) * 100), 0) .'%'?></td>
            <td align="center" style="width: 40px;border-bottom: 1px solid #000000">&nbsp;</td>
            <td align="right" style="width: 40px;border-bottom: 1px solid #000000">&nbsp;</td>
            <td align="right" style="width: 40px;border-bottom: 1px solid #000000">&nbsp;</td>
            <td align="right" style="width: 90px;border-bottom: 1px solid #000000">&nbsp;</td>
            <td align="right" style="width: 90px;border-bottom: 1px solid #000000"><?php echo APUtils::number_format(($bruto_total - $net_total), 2)?></td>
        </tr>
        <tr>
            <td style="text-align: left;"><b>Total EUR</b></td>
            <td align="left">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td align="right">&nbsp;</td>
            <td align="right">&nbsp;</td>
            <td align="right"><?php echo APUtils::number_format($net_total, 2)?></td>
            <td align="right"><?php echo APUtils::number_format($bruto_total, 2)?></td>
        </tr>
    </tbody>
</table>
<br/>
Your <?php echo Settings::get(APConstants::INSTANCE_OWNER_COMPANY_CODE)?> Team<br/>
</div>