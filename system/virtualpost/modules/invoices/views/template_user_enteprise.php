<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body>
<?php 
    $parent_customer_id = $customer->parent_customer_id;
    if(empty($parent_customer_id)){
        $parent_customer_id = $customer->customer_id;
    }
?>
<!-- Customer information  -->
<table style="size: 8px; width: 675px"  nobr="true">
    <tr nobr="true">
        <td style="width: 50%; text-align: left;padding-left: 0px; margin-left: 0px">
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <?php if ($address) { ?>
                <?php if (!empty($address->invoicing_address_name)) {
                    echo $address->invoicing_address_name . '<br/>';
                } ?>
                <?php if (!empty($address->invoicing_company)) {
                    echo $address->invoicing_company . '<br/>';
                } ?>
                <?php echo $address->invoicing_street ?><br/>
                <?php echo $address->invoicing_postcode  . ', ' . $address->invoicing_city ?><br/>
                <?php echo $address->invoicing_country ?><br/>
                <?php if (!empty($customer->vat_number)) {
                    echo 'VAT Number: ' . $customer->vat_number;
                } ?><br/>
            <?php } ?>
        </td>
        <td style="width: 50%; text-align: right">
            <b><?php echo AccountSetting::get($parent_customer_id, APConstants::INSTANCE_OWNER_COMPANY_CODE) ?></b><br/>
                <?php echo AccountSetting::get($parent_customer_id, APConstants::INSTANCE_OWNER_STREET_CODE) ?> <br/>
                <?php echo AccountSetting::get($parent_customer_id, APConstants::INSTANCE_OWNER_PLZ_CODE) . ' ' . AccountSetting::get($parent_customer_id, APConstants::INSTANCE_OWNER_CITY_CODE); ?>
                <br/>
                <?php echo AccountSetting::get($parent_customer_id, APConstants::INSTANCE_OWNER_COUNTRY_CODE) ?><br/>
                Telefon: <?php echo AccountSetting::get($parent_customer_id, APConstants::INSTANCE_OWNER_TEL_INVOICE_CODE) ?><br/>
                Fax: <?php echo AccountSetting::get($parent_customer_id, APConstants::INSTANCE_OWNER_FAX_CODE) ?><br/>
                <?php echo AccountSetting::get($parent_customer_id, APConstants::INSTANCE_OWNER_MAIL_INVOICE_CODE) ?><br/><br/>

                VAT: <?php echo AccountSetting::get($parent_customer_id, APConstants::INSTANCE_OWNER_VAT_NUM_CODE) ?><br/>
                <?php echo AccountSetting::get($parent_customer_id, APConstants::INSTANCE_OWNER_REGISTERED_NUM_CODE) ?><br/>
                Directors:<br/>
                <?php
                $directors = AccountSetting::get($parent_customer_id, APConstants::INSTANCE_OWNER_DIRECTOR_CODE);
                $director_arr = explode(",", $directors);
                $display_director = implode(', <br/>', $director_arr);
                echo $display_director;
                ?>
                <br/><br/>
                <?php echo AccountSetting::get($parent_customer_id, APConstants::INSTANCE_OWNER_CITY_CODE) ?>,
                &nbsp;<?php echo $target_date; ?><br/>
        </td>
    </tr>
</table>
<!-- invoice billing number -->
<b style="text-align: left;"><?php if($invoices[0]->total_invoice < 0 ){echo 'Credit note';}else{echo 'Invoice';} ?> <?php echo $invoice_code; ?></b>, &nbsp; Period of service: <?php echo $period_of_service; ?>
<br/>
<div>
    <?php
    // calculate net total summary
    $net_total_summary = 0;
    $phone_net_summary = 0;
    $total_net_price = 0;
    $index = 0;
    ?>

    <?php foreach ($invoices as $invoice): ?>
        <?php
        // Gets vat
        $vat = $invoice->vat;
        $i = 0;
        if (abs($invoice->total_invoice) < 0.005) {
            continue;
        }

        // calculate net total summary
        $index++;
        $net_total_summary += $invoice->total_invoice;

        // Gets vat case
        $case = APUtils::getCountryByVat($invoice->vat_case);
        $country = "";
        if ($case->country) {
            $country = $case->country . ", " . $case->product_type;
        }
        $invoice_notes = $case->invoice_notes;
        
        $user_customer = APContext::getCustomerByID($invoice->customer_id);
        $customer_code_label = "Customer code: ".$user_customer->customer_code. ", ";
        $postbox_type_label = "ENTERPRISE";
        $row = $invoice;
        $total_net_price += $invoice->total_invoice;
        ?>
        <h4><?php echo $customer_code_label ?> VAT: <?php echo $invoice->vat * 100 ?>% <?php if ($country) {
                echo ", " . $country;
            } ?></h4>

        <br/>
        <!-- content -->
        <table border="1px" style="size: 7px;"  nobr="true">
            <tr nobr="true">
                <th align="center" style="width: 60px">Position</th>
                <th align="center" style="width: 280px">Description</th>
                <th align="center" style="width: 66px">quantity</th>
                <th align="center" style="width: 66px">Net price</th>
                <th align="center" style="width: 66px">Gross Price</th>
                <th align="center" style="width: 66px">Net total</th>
                <th align="center" style="width: 66px">Gross total</th>
            </tr>
            <!-- postbox fee -->
            <?php if ($row->business_postboxes_amount > 0) { ?>
                <tr nobr="true">
                    <td style="text-align: left;"><?php $i++;
                        echo $i; ?></td>
                    <td align="left">Subscription <?php echo $postbox_type_label; ?> postbox - <?php echo $row->location_name ?></td>
                    <td align="right"><?php echo $row->business_postboxes_quantity ?></td>
                    <td align="right"><?php if ($row->business_postboxes_quantity > 0) {
                            echo APUtils::number_format($row->business_postboxes_netprice, 2, $decimal_separator);
                        } ?></td>
                    <td align="right"><?php if ($row->business_postboxes_netprice > 0) {
                            echo APUtils::number_format($row->business_postboxes_netprice * (1 + $vat), 2, $decimal_separator);
                        } ?></td>
                    <td align="right"><?php echo APUtils::number_format($row->business_postboxes_amount, 2, $decimal_separator) ?></td>
                    <td align="right"><?php echo APUtils::number_format($row->business_postboxes_amount * (1 + $vat), 2, $decimal_separator) ?></td>
                </tr>
            <?php } ?>

            <?php if ($row->incomming_items_business_account > 0) { ?>
                <tr nobr="true">
                    <td style="text-align: left;"><?php $i++;
                        echo $i; ?></td>
                    <td align="left">Incoming items <?php echo $postbox_type_label; ?> - <?php echo $row->location_name ?></td>
                    <td align="right"><?php echo $row->incomming_items_business_quantity ?></td>
                    <td align="right"><?php if ($row->incomming_items_business_quantity > 0) {
                            echo APUtils::number_format($row->incomming_items_business_netprice, 2, $decimal_separator);
                        } ?></td>
                    <td align="right"><?php if ($row->incomming_items_business_netprice > 0) {
                            echo APUtils::number_format($row->incomming_items_business_netprice * (1 + $vat), 2, $decimal_separator);
                        } ?></td>
                    <td align="right"><?php echo APUtils::number_format($row->incomming_items_business_account, 2, $decimal_separator) ?></td>
                    <td align="right"><?php echo APUtils::number_format($row->incomming_items_business_account * (1 + $vat), 2, $decimal_separator) ?></td>
                </tr>
            <?php } ?>

            <?php if ($row->envelope_scan_business_account > 0) { ?>
                <tr nobr="true">
                    <td style="text-align: left;"><?php $i++;
                        echo $i; ?></td>
                    <td align="left">Envelope scan <?php echo $postbox_type_label; ?> - <?php echo $row->location_name ?></td>
                    <td align="right"><?php echo $row->envelope_scan_business_quantity ?></td>
                    <td align="right"><?php if ($row->envelope_scan_business_quantity > 0) {
                            echo APUtils::number_format($row->envelope_scan_business_netprice, 2, $decimal_separator);
                        } ?></td>
                    <td align="right"><?php if ($row->envelope_scan_business_netprice > 0) {
                            echo APUtils::number_format($row->envelope_scan_business_netprice * (1 + $vat), 2, $decimal_separator);
                        } ?></td>
                    <td align="right"><?php echo APUtils::number_format($row->envelope_scan_business_account, 2, $decimal_separator) ?></td>
                    <td align="right"><?php echo APUtils::number_format($row->envelope_scan_business_account * (1 + $vat), 2, $decimal_separator) ?></td>
                </tr>
            <?php } ?>

            <?php if ($row->item_scan_business_account > 0) { ?>
                <tr>
                    <td style="text-align: left;"><?php $i++;
                        echo $i; ?></td>
                    <td align="left">Item scan <?php echo $postbox_type_label; ?> - <?php echo $row->location_name ?></td>
                    <td align="right"><?php echo $row->item_scan_business_quantity ?></td>
                    <td align="right"><?php if ($row->item_scan_business_quantity > 0) {
                            echo APUtils::number_format($row->item_scan_business_netprice, 2, $decimal_separator);
                        } ?></td>
                    <td align="right"><?php if ($row->item_scan_business_netprice > 0) {
                            echo APUtils::number_format($row->item_scan_business_netprice * (1 + $vat), 2, $decimal_separator);
                        } ?></td>
                    <td align="right"><?php echo APUtils::number_format($row->item_scan_business_account, 2, $decimal_separator) ?></td>
                    <td align="right"><?php echo APUtils::number_format($row->item_scan_business_account * (1 + $vat), 2, $decimal_separator) ?></td>
                </tr>
            <?php } ?>

            <?php if ($row->additional_pages_scanning_business_amount > 0) { ?>
                <tr nobr="true">
                    <td style="text-align: left;"><?php $i++;
                        echo $i; ?></td>
                    <td align="left">Additional pages scanning <?php echo $postbox_type_label; ?> - <?php echo $row->location_name ?></td>
                    <td align="right"><?php echo $row->additional_pages_scanning_business_quantity ?></td>
                    <td align="right"><?php if ($row->additional_pages_scanning_business_quantity > 0) {
                            echo APUtils::number_format($row->additional_pages_scanning_business_netprice, 2, $decimal_separator);
                        } ?></td>
                    <td align="right"><?php if ($row->additional_pages_scanning_business_quantity > 0) {
                            echo APUtils::number_format($row->additional_pages_scanning_business_netprice * (1 + $vat), 2, $decimal_separator);
                        } ?></td>
                    <td align="right"><?php echo APUtils::number_format($row->additional_pages_scanning_business_amount, 2, $decimal_separator) ?></td>
                    <td align="right"><?php echo APUtils::number_format($row->additional_pages_scanning_business_amount * (1 + $vat), 2, $decimal_separator) ?></td>
                </tr>
            <?php } ?>

            <?php if ($row->direct_shipping_business_account > 0) { ?>
                <tr nobr="true">
                    <td style="text-align: left;"><?php $i++;
                        echo $i; ?></td>
                    <td align="left">Direct forwarding <?php echo $postbox_type_label; ?> - <?php echo $row->location_name ?></td>
                    <td align="right"><?php echo $row->direct_shipping_business_quantity ?></td>
                    <td align="right"><?php //if ($row->direct_shipping_business_quantity > 0) { echo APUtils::number_format($row->direct_shipping_business_netprice, 2);}?></td>
                    <td align="right"><?php //if ($row->direct_shipping_business_netprice > 0) { echo APUtils::number_format($row->direct_shipping_business_netprice * (1 + $vat), 2);}?></td>
                    <td align="right"><?php echo APUtils::number_format($row->direct_shipping_business_account, 2, $decimal_separator) ?></td>
                    <td align="right"><?php echo APUtils::number_format($row->direct_shipping_business_account * (1 + $vat), 2, $decimal_separator) ?></td>
                </tr>
            <?php } ?>

            <?php if ($row->collect_shipping_business_account > 0) { ?>
                <tr nobr="true">
                    <td style="text-align: left;"><?php $i++;
                        echo $i; ?></td>
                    <td align="left">Collect forwarding <?php echo $postbox_type_label; ?> - <?php echo $row->location_name ?></td>
                    <td align="right"><?php echo $row->collect_shipping_business_quantity ?></td>
                    <td align="right"><?php //if ($row->collect_shipping_business_netprice > 0) { echo APUtils::number_format($row->collect_shipping_business_netprice, 2, $decimal_separator);} ?></td>
                    <td align="right"><?php //if ($row->collect_shipping_business_netprice > 0) { echo APUtils::number_format($row->collect_shipping_business_netprice * (1 + $vat), 2, $decimal_separator); } ?></td>
                    <td align="right"><?php echo APUtils::number_format($row->collect_shipping_business_account, 2, $decimal_separator) ?></td>
                    <td align="right"><?php echo APUtils::number_format($row->collect_shipping_business_account * (1 + $vat), 2, $decimal_separator) ?></td>
                </tr>
            <?php } ?>

            <?php if ($row->storing_letters_business_account > 0) { ?>
                <tr nobr="true">
                    <td style="text-align: left;"><?php $i++;
                        echo $i; ?></td>
                    <td align="left">Storing letters <?php echo $postbox_type_label; ?> (charged storage days) - <?php echo $row->location_name ?></td>
                    <td align="right"><?php echo $row->storing_letters_business_quantity ?></td>
                    <td align="right"><?php if ($row->storing_letters_business_quantity > 0) {
                            echo APUtils::number_format($row->storing_letters_business_netprice, 2, $decimal_separator);
                        } ?></td>
                    <td align="right"><?php if ($row->storing_letters_business_netprice > 0) {
                            echo APUtils::number_format($row->storing_letters_business_netprice * (1 + $vat), 2);
                        } ?></td>
                    <td align="right"><?php echo APUtils::number_format($row->storing_letters_business_account, 2, $decimal_separator); ?></td>
                    <td align="right"><?php echo APUtils::number_format($row->storing_letters_business_account * (1 + $vat), 2, $decimal_separator); ?></td>
                </tr>
            <?php } ?>

            <?php if ($row->storing_packages_business_account > 0) { ?>
                    <tr nobr="true">
                        <td style="text-align: left;"><?php $i++;
                            echo $i; ?></td>
                        <td align="left">Storing packages <?php echo $postbox_type_label; ?> (charged storage days) - <?php echo $row->location_name ?></td>
                        <td align="right"><?php echo $row->storing_packages_business_quantity ?></td>
                        <td align="right"><?php if ($row->storing_packages_business_quantity > 0) {
                                echo APUtils::number_format($row->storing_packages_business_netprice, 2, $decimal_separator);
                            } ?></td>
                        <td align="right"><?php if ($row->storing_packages_business_netprice > 0) {
                                echo APUtils::number_format($row->storing_packages_business_netprice * (1 + $vat), 2, $decimal_separator);
                            } ?></td>
                        <td align="right"><?php echo APUtils::number_format($row->storing_packages_business_account, 2, $decimal_separator); ?></td>
                        <td align="right"><?php echo APUtils::number_format($row->storing_packages_business_account * (1 + $vat), 2, $decimal_separator); ?></td>
                    </tr>
                <?php } ?>
        </table>
        <br/>
        <table style="size: 8px;" border="0px"  nobr="true">
            <tbody>
            <tr nobr="true">
                <td style="text-align: left;width: 100px; border-bottom: 1px solid #000000"><b>Subtotal</b></td>
                <td align="left" style="width: 240px;border-bottom: 1px solid #000000">&nbsp;</td>
                <td align="center" style="width: 66px;border-bottom: 1px solid #000000">&nbsp;</td>
                <td align="right" style="width: 66px;border-bottom: 1px solid #000000">&nbsp;</td>
                <td align="right" style="width: 66px;border-bottom: 1px solid #000000">&nbsp;</td>
                <td align="right"
                    style="width: 66px;border-bottom: 1px solid #000000"><?php echo APUtils::number_format(abs($invoice->total_invoice), 2, $decimal_separator); ?></td>
                <td align="right"
                    style="width: 66px;border-bottom: 1px solid #000000"><?php echo APUtils::number_format(abs($invoice->total_invoice * (1 + $vat)), 2, $decimal_separator); ?></td>
            </tr>
            </tbody>
        </table>
        <div>&nbsp;</div>
        <?php if ($invoice->total_invoice > 0): // only generate for invoice report?>
            <div class="description">
                <?php echo $invoice_notes;?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
        
    <br />
    <!--- display phone number activity--->
    <?php if($phone_invoices){
        $phone_net_price = 0;
        $phone_gross_price = 0;
        ?>
    <h3></h3>
    <table style="size: 8px;" border="1px"  nobr="true">
        <tbody>
            <tr nobr="true">
                <th align="center" style="width: 60px">Position</th>
                <th align="center" style="width: 280px">Description</th>
                <th align="center" style="width: 66px">quantity</th>
                <th align="center" style="width: 66px">Net price</th>
                <th align="center" style="width: 66px">Gross Price</th>
                <th align="center" style="width: 66px">Net total</th>
                <th align="center" style="width: 66px">Gross total</th>
            </tr>
            <?php $i = 0;
            foreach($phone_invoices as $row): 
                $index++;
                $phone_net_price += $row->total_invoice;
                $phone_gross_price += $row->total_invoice * (1 + $row->vat);
            ?>
            <?php if ($row->incomming_amount > 0) { ?>
                <tr nobr="true">
                    <td style="text-align: left;"><?php $i++; echo $i; ?></td>
                    <td align="left">Incoming call - <?php echo $row->location_name ?></td>
                    <td align="right"><?php echo $row->incomming_quantity ?></td>
                    <td align="right">&nbsp;</td>
                    <td align="right">&nbsp;</td>
                    <td align="right"><?php echo APUtils::number_format($row->incomming_amount, 2, $decimal_separator) ?></td>
                    <td align="right"><?php echo APUtils::number_format($row->incomming_amount * (1 + $vat), 2, $decimal_separator) ?></td>
                </tr>
            <?php } ?>
            <?php if ($row->outcomming_amount > 0) { ?>
                <tr nobr="true">
                    <td style="text-align: left;"><?php $i++; echo $i; ?></td>
                    <td align="left">Outcoming call - <?php echo $row->location_name ?></td>
                    <td align="right"><?php echo $row->outcomming_quantity ?></td>
                    <td align="right">&nbsp;</td>
                    <td align="right">&nbsp;</td>
                    <td align="right"><?php echo APUtils::number_format($row->outcomming_amount, 2, $decimal_separator) ?></td>
                    <td align="right"><?php echo APUtils::number_format($row->outcomming_amount * (1 + $vat), 2, $decimal_separator) ?></td>
                </tr>
            <?php } ?>
            <?php if ($row->phone_subscription_amount > 0) { ?>
                <tr nobr="true">
                    <td style="text-align: left;"><?php $i++; echo $i; ?></td>
                    <td align="left">Phone subscription - <?php echo $row->location_name ?></td>
                    <td align="right"><?php echo $row->phone_subscription_quantity ?></td>
                    <td align="right">&nbsp;</td>
                    <td align="right">&nbsp;</td>
                    <td align="right"><?php echo APUtils::number_format($row->phone_subscription_amount, 2, $decimal_separator) ?></td>
                    <td align="right"><?php echo APUtils::number_format($row->phone_subscription_amount * (1 + $vat), 2, $decimal_separator) ?></td>
                </tr>
            <?php } ?>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <br/>
    <br/>
    <table style="size: 8px;" border="0px"  nobr="true">
        <tbody>
        <tr nobr="true">
            <td style="text-align: left;width: 100px; border-bottom: 1px solid #000000"><b>Subtotal</b></td>
            <td align="left" style="width: 240px;border-bottom: 1px solid #000000">&nbsp;</td>
            <td align="center" style="width: 66px;border-bottom: 1px solid #000000">&nbsp;</td>
            <td align="right" style="width: 66px;border-bottom: 1px solid #000000">&nbsp;</td>
            <td align="right" style="width: 66px;border-bottom: 1px solid #000000">&nbsp;</td>
            <td align="right" style="width: 66px;border-bottom: 1px solid #000000"><?php echo APUtils::number_format($phone_net_price, 2, $decimal_separator); ?></td>
            <td align="right" style="width: 66px;border-bottom: 1px solid #000000"><?php echo APUtils::number_format($phone_gross_price, 2, $decimal_separator); ?></td>
            <?php $phone_net_summary += $phone_net_price;?>
        </tr>
        </tbody>
    </table>
    <?php }?>

     <br/>
    <h3>Summary</h3>
    <table style="size: 7px;" border="1px"  nobr="true">
        <tbody>
        <tr nobr="true">
            <td align="left" style="width: 100px" rowspan="<?php echo $index + 1; ?>">Summary</td>
            <td align="left" style="width: 240px">NET total</td>
            <td align="center" style="width: 66px">&nbsp;</td>
            <td align="center" style="width: 66px">&nbsp;</td>
            <td align="center" style="width: 66px">&nbsp;</td>
            <td align="center" style="width: 66px">&nbsp;</td>
            <td align="right" style="width: 66px"><?php echo APUtils::number_format(abs($total_net_price) + $phone_net_summary, 2, $decimal_separator); ?></td>
        </tr>
        <?php $gross_total_summary = 0; ?>
        <?php foreach ($invoices as $invoice): ?>
            <?php
            if (abs($invoice->total_invoice) < 0.005) {
                continue;
            }
            
            // calculate gross total
            $gross_total_summary += abs($invoice->total_invoice) * (1 + $invoice->vat);
            ?>
            <tr nobr="true">
                <td align="left">VAT <?php echo $invoice->vat * 100; ?> %</td>
                <td align="right">&nbsp;</td>
                <td align="right">&nbsp;</td>
                <td align="right">&nbsp;</td>
                <td align="right">&nbsp;</td>
                <td align="right"><?php echo APUtils::number_format(abs($invoice->total_invoice) * ($invoice->vat), 2, $decimal_separator); ?></td>
            </tr>
        <?php endforeach; ?>
        <?php $gross_phone_summary = 0; ?>
        <?php 
        if($phone_invoices){
        foreach($phone_invoices as $row): ?>
            <?php $gross_phone_summary += abs($row->total_invoice) * ($row->vat)* 1; ?>
            <tr nobr="true">
                <td align="left">VAT <?php echo $row->vat * 100; ?> %</td>
                <td align="right">&nbsp;</td>
                <td align="right">&nbsp;</td>
                <td align="right">&nbsp;</td>
                <td align="right">&nbsp;</td>
                <td align="right"><?php echo APUtils::number_format(abs($row->total_invoice) * ($row->vat)* 1, 2, $decimal_separator); ?></td>
            </tr>
            <?php endforeach; 
        }?>
        <tr nobr="true">
            <td style="text-align: left;"><b>Total (EUR)</b></td>
            <td style="text-align: left;">gross total</td>
            <td align="right">&nbsp;</td>
            <td align="right">&nbsp;</td>
            <td align="right">&nbsp;</td>
            <td align="right">&nbsp;</td>
            <td align="right"><?php echo APUtils::number_format($gross_total_summary + $gross_phone_summary, 2, $decimal_separator); ?></td>
        </tr>

        </tbody>
    </table>
    <div>&nbsp;</div>
    <div class="standard_payment">
        <?php
        if($invoices[0]->total_invoice > 0 ){
            if (APUtils::isInvoicePaymentMethod($customer->customer_id)) {
                echo lang('standard_payment_is_invoice_method');
            } else if (APUtils::isPaypalPaymentStandard($customer->customer_id)) {
                echo lang('standard_payment_is_paypal');
            } else if (APUtils::isCreditCardPaymentStandard($customer->customer_id)) {
                echo lang('standard_payment_is_credit_card');
            }
        }
        ?>
    </div>
    <br/><br/>
    Your <?php echo Settings::get(APConstants::INSTANCE_OWNER_COMPANY_CODE) ?> Team<br/>
</div>
</body>
</html>