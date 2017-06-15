<div id="invoice-body-wrapper11">
    <div class="ym-grid">
        <div id="invoice-body-wrapper">
        <h2>Invoices</h2>
        <div class="ym-clearfix" style="height:1px;"></div>
        </div>
        <?php
            $customer = APContext::getCustomerLoggedIn();
            if ($customer->charge_fee_flag !== '1') {
        ?>
        <div style="color: red; margin: 10px 0 0 40px;">
            <h3 style="color: red;"><?php echo lang('no_charge_message')?></h3>
        </div>
        <?php } ?>
        <div class="ym-grid"  >
            <div id="invoice-body-wrapper">
                <div id="left-content" class="ym-gl">
                    <h3>Next Invoicing Date: <?php echo APUtils::displayDate(APUtils::getLastDayOfCurrentMonth())?></h3>
                    <?php if(APContext::isPrimaryCustomerUser()){ ?>
                    <h4 class="COLOR_063" style="margin-bottom: 7px; margin-top: 10px;">Enterprise</h4>
                    <table style="height:287px">
                        <tbody>
                            <tr>
                                <td>Own Location</td>
                                <td class="right-align"><?php echo $currency->currency_sign.' ';?> 
                                <?php echo APUtils::convert_currency($next_invoices->own_location_amount, $currency->currency_rate, 2, $decimal_separator);?></td>
                            </tr>
                            <tr>
                                <td>Touch Panel at own location</td>
                                <td class="right-align"><?php echo $currency->currency_sign.' ';?> 
                                <?php echo APUtils::convert_currency($next_invoices->touch_panel_own_location_amount, $currency->currency_rate, 2, $decimal_separator);?></td>
                            </tr>
                            <tr>
                                <td>Own mobile app</td>
                                <td class="right-align"><?php echo $currency->currency_sign.' ';?> 
                                <?php echo APUtils::convert_currency($next_invoices->own_mobile_app_amount, $currency->currency_rate, 2, $decimal_separator);?></td>
                            </tr>
                            <tr>
                                <td>API Access</td>
                                <td class="right-align"><?php echo $currency->currency_sign.' ';?> 
                                <?php echo APUtils::convert_currency($next_invoices->api_access_amount, $currency->currency_rate, 2, $decimal_separator);?></td>
                            </tr>
                            <tr>
                                <td>Clevver Subdomain</td>
                                <td class="right-align"><?php echo $currency->currency_sign.' ';?> 
                                <?php echo APUtils::convert_currency($next_invoices->clevver_subdomain_amount, $currency->currency_rate, 2, $decimal_separator);?></td>
                            </tr>
                            <tr>
                                <td>Own Domain</td>
                                <td class="right-align"><?php echo $currency->currency_sign.' ';?> 
                                <?php echo APUtils::convert_currency($next_invoices->own_subdomain_amount, $currency->currency_rate, 2, $decimal_separator);?></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="left-align">Current Net Total 
                                    <a class="how_it_works" href="javascript:void(0);" title="" style="position: absolute; margin-top: -5px;margin-left:2px;color:#0e76bc;">
                                        <span style="vertical-align: unset;" class="managetables-icon icon_help tipsy_tooltip" original-title="How it works ?"></span></a></td>
                                <td class="right-align"><?php echo $currency->currency_sign.' '; ?> 0.00</td>
                            </tr>
                        </tfoot>
                    </table>
                    <?php }?>
                    <h4 class="COLOR_063">Postbox</h4>
                    <?php  if($customer->status == APConstants::ON_FLAG){?>
                    <table style="height:287px">
                        <tbody>
                            <tr>
                                <td>Postboxes</td>
                                <td class="right-align"><?php echo $currency->currency_sign.' '; ?> 0.00</td>
                            </tr>
                            <tr>
                                <td>Envelope scanning</td>
                                <td class="right-align"><?php echo $currency->currency_sign.' '; ?> 0.00</td>
                            </tr>
                            <tr>
                                <td>Scanning</td>
                                <td class="right-align"><?php echo $currency->currency_sign.' '; ?> 0.00</td>
                            </tr>
                            <tr>
                                <td>Additional items</td>
                                <td class="right-align"><?php echo $currency->currency_sign.' '; ?> 0.00</td>
                            </tr>
                            <tr>
                                <td>Scan of additional pages</td>
                                <td class="right-align"><?php echo $currency->currency_sign.' '; ?> 0.00</td>
                            </tr>
                            <tr>
                                <td>Shipping&amp;handling</td>
                                <td class="right-align" ><?php echo $currency->currency_sign.' '; ?> 0.00</td>
                            </tr>
                            <tr>
                                <td>Storing items</td>
                                <td class="right-align"><?php echo $currency->currency_sign.' '; ?> 0.00</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="left-align">Current Net Total 
                                    <a class="how_it_works" href="javascript:void(0);" title="" style="position: absolute; margin-top: -5px;margin-left:2px;color:#0e76bc;">
                                        <span style="vertical-align: unset;" class="managetables-icon icon_help tipsy_tooltip" original-title="How it works ?"></span></a></td>
                                <td class="right-align"><?php echo $currency->currency_sign.' '; ?> 0.00</td>
                            </tr>
                        </tfoot>
                    </table>
                    <?php } else {?>
                    <?php if($next_invoices){?>
                    <table style="height:287px">
                        <tbody>
                            <tr>
                                <!--<td><a class="showPostboxActvityLink" href="#">Postboxes</a></td>-->
                                <td>Postboxes</td>
                                <td class="right-align"><?php echo $currency->currency_sign.' '; ?>
                                <?php if($next_invoices->postboxes_amount) { echo APUtils::convert_currency($next_invoices->postboxes_amount, $currency->currency_rate, 2, $decimal_separator); } else { echo sprintf('0%s00', $decimal_separator); }?></td>
                            </tr>
                            <tr>
                                <td><a class="showEnvelopeScanActvityLink main_link_color"  href="#">Envelope scanning</a></td>
                                <td class="right-align"><?php echo $currency->currency_sign.' '; ?>
                                <?php if($next_invoices->envelope_scanning_amount) {echo APUtils::convert_currency($next_invoices->envelope_scanning_amount, $currency->currency_rate, 2, $decimal_separator); } else { echo sprintf('0%s00', $decimal_separator); }?></td>
                            </tr>
                            <tr>
                                <td><a class="showItemScanActvityLink main_link_color"  href="#">Scanning</a></td>
                                <td class="right-align"><?php echo $currency->currency_sign.' '; ?>
                                <?php if($next_invoices->scanning_amount) {echo APUtils::convert_currency($next_invoices->scanning_amount, $currency->currency_rate, 2, $decimal_separator);}else{echo sprintf('0%s00', $decimal_separator);}?></td>
                            </tr>
                            <tr>
                                <td><a class="showAdditionalItemActvityLink main_link_color"  href="#">Additional items</a></td>
                                <td class="right-align"><?php echo $currency->currency_sign.' '; ?>
                                <?php if($next_invoices->additional_items_amount) {echo APUtils::convert_currency($next_invoices->additional_items_amount, $currency->currency_rate, 2, $decimal_separator);}else{echo sprintf('0%s00', $decimal_separator);}?></td>
                            </tr>
                            <tr>
                                <td><a class="showAdditionalPageActvityLink main_link_color"  href="#">Scan of additional pages</a></td>
                                <td class="right-align"><?php echo $currency->currency_sign.' '; ?>
                                <?php if($next_invoices->additional_pages_scanning_amount) {echo APUtils::convert_currency($next_invoices->additional_pages_scanning_amount, $currency->currency_rate, 2, $decimal_separator);}else{echo sprintf('0%s00', $decimal_separator);}?></td>
                            </tr>
                            <tr>
                                <td><a class="showShippingActvityLink main_link_color"  href="#">Shipping&amp;handling</a></td>
                                <td class="right-align" ><?php echo $currency->currency_sign.' '; ?>
                                <?php if($next_invoices->shipping_handing_amount) {echo APUtils::convert_currency($next_invoices->shipping_handing_amount, $currency->currency_rate, 2, $decimal_separator);}else{echo sprintf('0%s00', $decimal_separator);}?></td>
                            </tr>
                            <tr>
                                <td><a class="showStoringActvityLink main_link_color"  href="#">Storing items</a></td>
                                <td class="right-align"><?php echo $currency->currency_sign.' '; ?>
                                <?php if($next_invoices->storing_amount) {echo APUtils::convert_currency($next_invoices->storing_amount, $currency->currency_rate, 2, $decimal_separator);}else{echo sprintf('0%s00', $decimal_separator);}?></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="left-align">Current Net Total <a class="how_it_works main_link_color" href="javascript:void(0);" title="" style="position: absolute; margin-top: -5px;margin-left:2px;"><span style="vertical-align: unset;" class="managetables-icon icon_help tipsy_tooltip" original-title="How it works ?"></span></a></td>
                                <td class="right-align">
                                    <?php
                                    echo $currency->currency_sign.' ';
                                    $total = 0;
                                    $total += $next_invoices->postboxes_amount;
                                    $total += $next_invoices->envelope_scanning_amount;
                                    $total += $next_invoices->scanning_amount;
                                    $total += $next_invoices->additional_items_amount;
                                    $total += $next_invoices->shipping_handing_amount;
                                    $total += $next_invoices->storing_amount;
                                    $total += $next_invoices->additional_pages_scanning_amount;

                                    echo APUtils::convert_currency($total, $currency->currency_rate, 2, $decimal_separator);
                                    ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    <?php }?>
                   <?php }?>
                </div>
            </div>
            <div id="right-content" class="ym-gl" style="margin-left: 20px;">
                <div class="ym-grid" style="margin-top: 45px;">
                    <h4 class="ym-gl COLOR_063" style="margin-top: 0px;">Activities in Current Period</h4>
                    <?php if(APContext::isPrimaryCustomerUser()){ ?>
                    <button style="position: relative; top: -10px; right: 10px;float:right" class="input-btn btn-yellow" type="button" id="setupAutomaticChargeButton">Setup an automatic deposit charge to your credit card</button>
                    <?php }?>
                </div>
                
                <table id="dataGridResult" ></table>
                <div id="dataGridPager"></div>
                <div class="clear-height"></div>
            </div>
        </div>
        
        <div class="ym-grid"  >
            <div id="invoice-body-wrapper">
                <div id="left-content" class="ym-gl">
                    <h4 class="COLOR_063">Phone number</h4>
                    <?php  if($customer->status == APConstants::ON_FLAG){?>
                    <table>
                        <tbody>
                            <tr>
                                <td>Setup fees</td>
                                <td class="right-align"><?php echo $currency->currency_sign.' '; ?> 0.00</td>
                            </tr>
                            <tr>
                                <td>Monthly Fees</td>
                                <td class="right-align"><?php echo $currency->currency_sign.' '; ?> 0.00</td>
                            </tr>
                            <tr>
                                <td>Incoming</td>
                                <td class="right-align"><?php echo $currency->currency_sign.' '; ?> 0.00</td>
                            </tr>
                            <tr>
                                <td>Outgoing</td>
                                <td class="right-align"><?php echo $currency->currency_sign.' '; ?> 0.00</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="left-align">Current Net Total <a class="how_it_works" href="javascript:void(0);" title="" style="position: absolute; margin-top: -5px;margin-left:2px;color:#0e76bc;"><span style="vertical-align: unset;" class="managetables-icon icon_help tipsy_tooltip" original-title="How it works ?"></span></a></td>
                                <td class="right-align"><?php echo $currency->currency_sign.' '; ?> 0.00</td>
                            </tr>
                        </tfoot>
                    </table>
                    <?php } else {?>
                    <?php if($next_invoice_phone){?>
                    <table>
                        <tbody>
                            <tr>
                                <td>Setup fees</td>
                                <td class="right-align"><?php echo $currency->currency_sign.' '; ?>
                                <?php if($next_invoice_phone->setup_fee_amount) { echo APUtils::convert_currency($next_invoice_phone->setup_fee_amount, $currency->currency_rate, 2, $decimal_separator); } else { echo sprintf('0%s00', $decimal_separator); }?></td>
                            </tr>
                            <tr>
                                <td>Monthly Fees</td>
                                <td class="right-align"><?php echo $currency->currency_sign.' '; ?>
                                <?php if($next_invoice_phone->phone_recurring_amount) {echo APUtils::convert_currency($next_invoice_phone->phone_recurring_amount, $currency->currency_rate, 2, $decimal_separator);}else{echo sprintf('0%s00', $decimal_separator);}?></td>
                            </tr>
                            <tr>
                                <td>Incoming</td>
                                <td class="right-align"><?php echo $currency->currency_sign.' '; ?>
                                <?php if($next_invoice_phone->incomming_amount) { echo APUtils::convert_currency($next_invoice_phone->incomming_amount, $currency->currency_rate, 2, $decimal_separator); } else { echo sprintf('0%s00', $decimal_separator); }?></td>
                            </tr>
                            <tr>
                                <td>Outgoing</td>
                                <td class="right-align"><?php echo $currency->currency_sign.' '; ?>
                                <?php if($next_invoice_phone->outcomming_amount) {echo APUtils::convert_currency($next_invoice_phone->outcomming_amount, $currency->currency_rate, 2, $decimal_separator); } else { echo sprintf('0%s00', $decimal_separator); }?></td>
                            </tr>
                        <tfoot>
                            <tr>
                                <td class="left-align">Current Net Total </td>
                                <td class="right-align">
                                    <?php
                                    $phone_total = 0;
                                    $phone_total += $next_invoice_phone->incomming_amount;
                                    $phone_total += $next_invoice_phone->outcomming_amount;
                                    $phone_total += $next_invoice_phone->phone_recurring_amount;
                                    $phone_total += $next_invoice_phone->setup_fee_amount;

                                    echo $currency->currency_sign.' '. APUtils::convert_currency($phone_total, $currency->currency_rate, 2, $decimal_separator);
                                    ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    <?php }?>
                   <?php }?>
                </div>
            </div>
            <div id="right-content" class="ym-gl" style="margin-left: 20px;">
                <h4 class="COLOR_063">Activities in Current Period</h4>
                <table id="dataGridResult3" ></table>
                <div id="dataGridPager3"></div>
                <div class="clear-height"></div>
            </div>
        </div>

        <div class="ym-clearfix" style="height:35px;"></div>
        <div style="margin-left:40px; width:100%">
            <div style="float: left;">
                <h4 class="COLOR_063">Old Invoices & Charge</h4>
            </div>
            
            <div style="float: left;margin-left: 70px; margin-top:-5px">
                <a id="bankTranferButton" style="text-decoration: none">
                    <img alt="tranfer by month" height="44px" src="<?php echo APContext::getImagePath()?>/bank-tranfer.png" />
                </a>
            </div>
            
            <div style="float: left;margin-left: 20px; margin-top:-5px">
                <a id="paymentPayoneButton" style="text-decoration: none">
                    <img alt="Check out with Payone by VISA card" src="<?php echo APContext::getImagePath()?>/visa.png" />
                    <img alt="Check out with Payone by Master card" src="<?php echo APContext::getImagePath()?>/mastercard.png" />
                </a>
            </div>
            <div style="float: left;margin-left: 20px; margin-top:-5px">
                <a id="paymentPayPalButton" style="text-decoration: none">
                    <img src="<?php echo APContext::getImagePath()?>/paypal.gif" alt="Check out with PayPal" style="width: 120px" />
                </a>
            </div>
            <div style="float: right;margin-right: 70px; margin-top:-50px;margin-bottom: 10px;width:325px;">
                <h3 style ="text-align: right;margin-right: 17px;">
                <?php
                
                    if($customer->status == APConstants::ON_FLAG){
                        $open_balance = 0;
                        $open_balance_this_month = 0;
                    }
                    $sign = "";
                    if($open_balance > 0){
                        $sign = "+";
                    }
                ?>
                Open balance due: <span id="open_balance"><?php echo $sign.APUtils::convert_currency($open_balance, $currency->currency_rate, 2, $decimal_separator).' '.$currency->currency_short;?></span>
                 <a class="how_it_works" href="javascript:void(0);" title="" style="position: absolute; margin-top: -5px;margin-left:2px;color:#0e76bc;"><span style="vertical-align: unset;margin-top: -5px;" class="managetables-icon icon_help tipsy_tooltip" original-title="How it works ?"></span></a>
                </h3>
                <h4 style="margin-top: 0px;text-align: right;">
                <?php
                    $sign = "";
                    if($open_balance_this_month > 0){
                        $sign = "+";
                    }
                ?>
                balance current month: <span><?php echo $currency->currency_sign . ' ' . $sign.APUtils::convert_currency($open_balance_this_month, $currency->currency_rate, 2, $decimal_separator)?></span>
                </h4>
            </div>
            <div style="clear: both;"></div>
            <table id="dataGridResult2" ></table>
            <div id="dataGridPager2"></div>
            <div class="clear-height"></div>
            <div class="ym-clearfix" style="height:80px"></div>
        </div>
    </div>
    <div class="hide" style="display: none;">
        <a id="display_pdf_invoice" class="iframe" href="#">Display PDF Invoice</a>
        <a id="display_paypal_invoice" class="iframe" href="#">Display PDF Invoice</a>
    </div>
</div>
<div class="hide" style="display: none;">
    <div id="window_how_it_works" title="How it works" class="input-form dialog-form"></div>
    <div id="paymentWithPaypalWindow" title="Payment With PayPal" class="input-form dialog-form"></div>
    <div id="createDirectChargeWithoutInvoice" title="Make a deposit from credit card" class="input-form dialog-form"></div>
    <a id="display_payment_confirm" class="iframe" href="#">Goto payment view</a>
    <div id="bankTranferDivContainer"  class="input-form dialog-form" title="Bank tranfer">
        <div style="text-align: center">
            <div style="margin-top: 10px">For a direct bank transfer please use the following Account Information:</div>
            <div  style="margin-top: 20px"><strong>Account holder: <?php echo Settings::get(APConstants::INSTANCE_OWNER_COMPANY_CODE)?></strong></div>
            <div><strong>IBAN: <?php echo Settings::get(APConstants::INSTANCE_OWNER_IBAN_CODE)?></strong></div>
            <div><strong>BIC: <?php echo Settings::get(APConstants::INSTANCE_OWNER_SWIFT_CODE)?></strong></div>
            <div><strong>Bank name: <?php echo Settings::get(APConstants::INSTANCE_OWNER_BANK_NAME_CODE)?></strong></div>
            <div><strong>Use your account e-mail as reference</strong></div>
            <div style="font-style: italic;font-size:12px;margin-top: 20px;">The money will be credited to your account as soon as it arrives on our bank account.</div>
        </div>
    </div>
        
    <a id="view_verification_file" class="iframe" href="">Preview file</a>
    <div id="setupAutomaticChargeWindow" title="Setup an automatic deposit charge to your credit card" class="input-form dialog-form"></div>
    <div id="showSetupFeeWindow" title="Phone pricing" class="input-form dialog-form"></div>
</div>
<script type="text/javascript">
$(document).ready( function() {
    $('.tipsy_tooltip').tipsy({gravity: 's'});
    var paypal_status = '<?php echo $paypal_status?>';
    var paypal_message = '<?php echo $paypal_message?>';
    if (paypal_status == '1') {
        $.displayInfor(paypal_message);
    } else if (paypal_status == '2') {
        $.displayError(paypal_message);
    }
    var enterprise_customer = "<?php if(APContext::isPrimaryCustomerUser()){ echo '1';} else { echo '0';} ?>";
    $('#display_pdf_invoice').fancybox({
        width: 900,
        height: 700,
        'onClosed': function() {
         $("#fancybox-inner").empty();
        }
    });
    $('#display_paypal_invoice').fancybox({
        width: 700,
        height: 500,
        'onClosed': function() {
         $("#fancybox-inner").empty();
        }
    });
    $('#display_payment_confirm').fancybox({
        width: 500,
        height: 300
    });

    // Call search method
    searchCurrInvoice();
    searchOldInvoices();
    searchCurrInvoicePhoneNumber();

    /**
     * Search data
     */
    function searchCurrInvoice() {
        $("#dataGridResult").jqGrid('GridUnload');
        var url = '<?php echo base_url() ?>invoices/load_current_activities';
        var height = 250;
        if (enterprise_customer == '1') {
            height = 580;
        }
        $("#dataGridResult").jqGrid({
            url: url,
            postData: $('#usesrSearchForm').serializeObject(),
            mtype: 'POST',
            datatype: "json",
            height: height,
            width: 710,
            rowNum: '<?php echo APContext::getPagingSetting();//Settings::get(APConstants::NUMBER_RECORD_PER_PAGE_CODE);?>',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            pager: "#dataGridPager",
            sortname: 'activity_date',
            viewrecords: true,
            shrinkToFit:false,
            sortorder: "desc",
            captions: '',
            colNames:['ID','Activity', 'User', 'Location name', 'Activity date', 'Net price', 'VAT %', 'Gross total'],
            colModel:[
               {name:'id',index:'id', hidden: true},
               {name:'activity',index:'activity', width:170},
               {name:'customer_code',index:'customer_code', width:80, sortable: false},
               {name:'location_name',index:'location_name', width:100, sortable: false},
               {name:'activity_date',index:'activity_date', width:90},
               {name:'item_amount',index:'item_amount', width:80, sortable: false},
               {name:'vat',index:'vat', width:52, sortable: false},
               {name:'gross_total',index:'gross_total', width:85, sortable: false}
            ],
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
                var data_row = $('#dataGridResult').jqGrid("getRowData",row_id);
                console.log(data_row);
            }
        });
    }

    /**
     * Search data
     */
    function searchOldInvoices() {
        $("#dataGridResult2").jqGrid('GridUnload');
        var url = '<?php echo base_url() ?>invoices/load_old_invoice';

        $("#dataGridResult2").jqGrid({
            url: url,
            postData: $('#usesrSearchForm').serializeObject(),
            mtype: 'POST',
            datatype: "json",
            height: 190,
            width: 975,
            rowNum: 100000,
            rowList: [],
            pager: "#dataGridPager2",
            viewrecords: true,
            shrinkToFit:false,
            captions: '',
            colNames:['ID','Transaction', 'Date', 'Transaction ID', 'Net Total', 'Gross Total', 'Status', 'PDF'],
            colModel:[
               {name:'id',index:'id', hidden: true},
               {name:'invoice_name',index:'invoice_name', width:190, sortable: false},
               {name:'tran_id',index:'tran_id', width:170, sortable: false},
               {name:'invoice_date',index:'invoice_date', width:150, sortable: false},
               {name:'net_total',index:'net_total', width:100, sortable: false},
               {name:'brutto_total',index:'brutto_total', width:100, sortable: false},
               {name:'status',index:'status', width:100, sortable: false},
               {name:'id',index:'id', width:110, sortable: false, align:"center", formatter: actionFormater}
            ],
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
                var data_row = $('#dataGridResult').jqGrid("getRowData",row_id);
                console.log(data_row);
            }
        });
    }

    function activeFormater(cellvalue, options, rowObject) {
        if (cellvalue == '1') {
            return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-tick">Check</span></span>';
        } else {
            return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete">UnCheck</span></span>';
        }
    }

    function actionFormater(cellvalue, options, rowObject) {
        if (rowObject[1] == 'Invoice') {
            return '<a class="pdf" target="_blank" href="<?php echo APContext::getFullBasePath()?>invoices/export/'+cellvalue+'?type=invoice" id="'+cellvalue+'">&nbsp;</a>';
        }else if(rowObject[1] == 'Credit Note'){
            return '<a class="pdf" target="_blank" href="<?php echo APContext::getFullBasePath()?>invoices/export/'+cellvalue+'?type=credit" id="'+cellvalue+'">&nbsp;</a>';
        }else {
            return '';
        }
    }
        
    $('#view_verification_file').fancybox({
            width: 1000,
            height: 800
        });

    /**
     * When user click pdf icon
     */
    $("a.pdf, a.temp_pdf").live('click', function() {
        var submitUrl = '<?php echo base_url()?>invoices/check_payment_exist';
        var invoices_href = this.href;

                $('#view_verification_file').attr('href', invoices_href);
                $('#view_verification_file').click();
                return false;
     });

    /**
     * Paypal payment
     */
    $('#paymentPayPalButton').live('click', function() {
        //var display_paypal_url = '<?php echo base_url()?>customers/paypal_payment_invoice';
        //$('#display_paypal_invoice').attr('href', display_paypal_url);
        //$('#display_paypal_invoice').click();
        
        // Open new dialog
        $('#paymentWithPaypalWindow').openDialog({
            autoOpen: false,
            height: 332,
            width: 710,
            modal: true,
            closeOnEscape: false,
            open: function(event, ui) {
                $(this).load("<?php echo base_url() ?>customers/paypal_payment_invoice", function() {
                });
            }
        });
        
        $('#paymentWithPaypalWindow').dialog('option', 'position', 'center');
        $('#paymentWithPaypalWindow').dialog('open');
    });

    $('a.how_it_works').live('click', function() {
        // Open new dialog
        $('.dialog-form').html('');
        $('#window_how_it_works').openDialog({
            autoOpen: false,
            height: 500,
            width: 1100,
            modal: true,
            closeOnEscape: false,
            open: function(event, ui) {
                $(this).load("<?php echo base_url() ?>info/how_it_works?popup_flag=1", function() {
                });
            }
        });
        
        $('#window_how_it_works').dialog('option', 'position', 'center');
        $('#window_how_it_works').dialog('open');
    });

     /**
     * Paypal payment
     */
    $('#paymentPayoneButton').live('click', function() {
        createDirectCharge();
    });

    /**
     * Create direct charge
     */
    function createDirectCharge() {
         // Clear control of all dialog form
        $('#createDirectChargeWithoutInvoice').html('');

        // Open new dialog
        $('#createDirectChargeWithoutInvoice').openDialog({
            autoOpen: false,
            height: 400,
            width: 720,
            modal: true,
            open: function() {
                $(this).load("<?php echo base_url() ?>customers/create_direct_charge_without_invoice", function() {});
            },
            buttons: {
                'Submit': function () {
                    saveDirectChargeWithoutInvoice();
                }
            }
        });
        $('#createDirectChargeWithoutInvoice').dialog('option', 'position', 'center');
        $('#createDirectChargeWithoutInvoice').dialog('open');
    };

    /**
     * Save direct charge without invoice
     */
    function saveDirectChargeWithoutInvoice() {
            var submitUrl = "<?php echo base_url() ?>customers/save_direct_charge_without_invoice";
            $.ajaxSubmit({
                url: submitUrl,
                formId: 'createDirectChargeWithoutInvoiceForm',
                success: function(data) {
                    if (data.status) {
                        if (data.redirect) {
                        var submitUrl = data.message;
                            $('#display_payment_confirm').attr('href', submitUrl);
                            $('#display_payment_confirm').click();
                        } else {
                            $('#createDirectChargeWithoutInvoice').dialog('close');
                            $.displayInfor(data.message, null,  function() {
                            });
                        }
                    } else {
                            $.displayError(data.message);
                    }
                }
            });
    }

    /**
     * Paypal payment
     */
    $('#bankTranferButton').click(function() {
        // Open new dialog
        $('#bankTranferDivContainer').openDialog({
            autoOpen: true,
            height: 250,
            width: 400,
            modal: true,
            closeOnEscape: true
        });
        
        $('#bankTranferDivContainer').dialog('option', 'position', 'center');
        $('#bankTranferDivContainer').dialog('open');
        return false;
    });
    
    /**
     * Search data
     */
    function searchCurrInvoicePhoneNumber() {
        $("#dataGridResult3").jqGrid('GridUnload');
        var url = '<?php echo base_url() ?>invoices/load_current_phone_invoice';

        $("#dataGridResult3").jqGrid({
            url: url,
            postData: $('#usesrSearchForm').serializeObject(),
            mtype: 'POST',
            datatype: "json",
            height: 135,
            width: 710,
            rowNum: '<?php echo APContext::getPagingSetting();//Settings::get(APConstants::NUMBER_RECORD_PER_PAGE_CODE);?>',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            pager: "#dataGridPager3",
            sortname: 'activity_date',
            viewrecords: true,
            shrinkToFit:false,
            sortorder: "desc",
            captions: '',
            colNames:['ID','Activity', 'User', 'Location name', 'Activity date', 'Net price', 'VAT %', 'Gross total'],
            colModel:[
               {name:'id',index:'id', hidden: true},
               {name:'activity',index:'activity', width:170},
               {name:'customer_code',index:'customer_code', width:100, sortable: false},
               {name:'location_name',index:'location_name', width:100, sortable: false},
               {name:'activity_date',index:'activity_date', width:90},
               {name:'item_amount',index:'item_amount', width:80, sortable: false},
               {name:'vat',index:'vat', width:52, sortable: false},
               {name:'gross_total',index:'gross_total', width:85, sortable: false}
            ],
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
                var data_row = $('#dataGridResult3').jqGrid("getRowData",row_id);
                console.log(data_row);
            }
        });
    }
    
    /**
    * setup automatic charge setting page.
     */
    $("#setupAutomaticChargeButton").click(function(){
        $("#setupAutomaticChargeWindow").html("");
        // Open new dialog
        $('#setupAutomaticChargeWindow').openDialog({
            autoOpen: true,
            height: 350,
            width: 450,
            modal: true,
            open: function() {
                $(this).load("<?php echo base_url() ?>account/setting/automatic_charge_account_setting?dialog=1", function() {});
            }
        });

        $('#setupAutomaticChargeWindow').dialog('option', 'position', 'center');
        $('#setupAutomaticChargeWindow').dialog('open');
        return false;
    });
    
    $("#showSetupFeeButton").click(function(){
        $("#showSetupFeeWindow").html("");
        // Open new dialog
        $('#showSetupFeeWindow').openDialog({
            autoOpen: true,
            height: 550,
            width: 1000,
            modal: true,
            open: function() {
                $(this).load("<?php echo base_url() ?>info/phone_pricing?show_dialog=1", function() {});
            }
        });

        $('#showSetupFeeWindow').dialog('option', 'position', 'center');
        $('#showSetupFeeWindow').dialog('open');
        return false;
    });
    
    // show activity lightbox.
    $(".showPostboxActvityLink").click(function(e){
        e.preventDefault();
        
        var loadUrl = "<?php echo base_url()?>invoices/get_postbox_activity";
        $.openDialog('#showPostboxActvityLink', {
            height: 450,
            width: 750,
            openUrl: loadUrl,
            title: "Postbox activity",
            closeButtonLabel: "Close",
            show_only_close_button: true,
            callback: function(){
                //location.reload();
            }
        });
        
        return false;
    });
    
    $(".showEnvelopeScanActvityLink").click(function(e){
        e.preventDefault();
        
        var loadUrl = "<?php echo base_url()?>invoices/get_envelope_scan_activity";
        $.openDialog('#showEnvelopeScanActvityLink', {
            height: 450,
            width: 950,
            openUrl: loadUrl,
            title: "Envelope scan activity",
            closeButtonLabel: "Close",
            show_only_close_button: true,
            callback: function(){
                //location.reload();
            }
        });
        
        return false;
    });
    
    $(".showItemScanActvityLink").click(function(e){
        e.preventDefault();
        
        var loadUrl = "<?php echo base_url()?>invoices/get_item_scan_activity";
        $.openDialog('#showItemScanActvityLink', {
            height: 450,
            width: 950,
            openUrl: loadUrl,
            title: "Item scan activity",
            closeButtonLabel: "Close",
            show_only_close_button: true,
            callback: function(){
                //location.reload();
            }
        });
        
        return false;
    });
    
     $(".showAdditionalItemActvityLink").click(function(e){
        e.preventDefault();
        
        var loadUrl = "<?php echo base_url()?>invoices/get_additional_item_activity";
        $.openDialog('#showAdditionalItemActvityLink', {
            height: 450,
            width: 950,
            openUrl: loadUrl,
            title: "Additional item activity",
            closeButtonLabel: "Close",
            show_only_close_button: true,
            callback: function(){
                //location.reload();
            }
        });
        
        return false;
    });
    
    $(".showAdditionalPageActvityLink").click(function(e){
        e.preventDefault();
        
        var loadUrl = "<?php echo base_url()?>invoices/get_additional_pages_activity";
        $.openDialog('#showAdditionalPageActvityLink', {
            height: 450,
            width: 950,
            openUrl: loadUrl,
            title: "Additional item activity",
            closeButtonLabel: "Close",
            show_only_close_button: true,
            callback: function(){
                //location.reload();
            }
        });
        
        return false;
    });
    
    $(".showShippingActvityLink").click(function(e){
        e.preventDefault();
        
        var loadUrl = "<?php echo base_url()?>invoices/get_shipping_activity";
        $.openDialog('#showShippingActvityLink', {
            height: 450,
            width: 950,
            openUrl: loadUrl,
            title: "Shipping activity",
            closeButtonLabel: "Close",
            show_only_close_button: true,
            callback: function(){
                //location.reload();
            }
        });
        
        return false;
    });
    
    $(".showStoringActvityLink").click(function(e){
        e.preventDefault();
        
        var loadUrl = "<?php echo base_url()?>invoices/get_storing_activity";
        $.openDialog('#showStoringActvityLink', {
            height: 450,
            width: 950,
            openUrl: loadUrl,
            title: "Storing activity",
            closeButtonLabel: "Close",
            show_only_close_button: true,
            callback: function(){
                //location.reload();
            }
        });
        
        return false;
    });
    
});
</script>