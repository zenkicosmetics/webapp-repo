<div class="ym-grid">
    <div id="invoice-body-wrapper">
        <h2>Payment</h2>
        <div class="ym-clearfix" style="height: 35px;"></div>
        <div>
            <div class="items">
                <table>
                    <thead>
                        <tr>
                            <th>Standard</th>
                            <th>Type</th>
                            <th>Name</th>
                            <th>Card No.</th>
                            <th>Exp. Date</th>
                            <th>3D Secure</th>
                            <th>Valid</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $flag = true;
                        if ($customer->invoice_type == '2' && !empty($customer->invoice_code)):
                            $flag = false;
                            ?>
                            <tr>
                                <td class="center-align"><input class="customCheckbox select_primarycard" type="radio" data-id="" checked="checked" /></td>
                                <td>Invoice</td>
                                <td><?php echo $address ? $address->invoicing_address_name . ' - ' . $address->invoicing_company : ""; ?></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        <?php endif; ?>

                        <?php if ($all_accounts) { ?>
                            <?php foreach ($all_accounts as $account) { ?>
                                <tr>
                                    <td class="center-align"><input class="customCheckbox select_primarycard"
                                                                    type="radio" data-id="<?php echo $account->payment_id ?>" <?php if ($flag && $account->primary_card == '1') { ?> checked="checked" <?php } ?> /></td>
                                    <td class="left-align"><?php
                                        if ($account->account_type == APConstants::PAYMENT_CREDIT_CARD_ACCOUNT) {
                                            switch ($account->card_type) {
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
                                        } else if ($account->account_type == APConstants::PAYMENT_PAYPAL_ACCOUNT) {
                                            echo "Paypal Account";
                                        }
                                        ?></td>
                                    <td class="left-align"><?php echo $account->card_name; ?></td>
                                    <td class="left-align"><?php echo $account->card_number; ?></td>
                                    <td class="left-align">
                                        <?php
                                        if (empty($account->expired_month) && empty($account->expired_year)) {
                                            echo 'No information';
                                        } else {
                                            // 20141029 fixbug : #403
                                            $month = APUtils::getCurrentMonth();
                                            $year = APUtils::getCurrentYearShort();
                                            if ($year > $account->expired_year || ($year == $account->expired_year && $month > intval($account->expired_month))) {
                                                echo "Expired";
                                            } else {
                                                echo ($account->expired_month . '/' . ($account->expired_year + 2000));
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td class="center-align">
                                        <?php
                                        if ($account->secure_3d_flag == APConstants::ON_FLAG) {
                                            echo 'Yes';
                                        } else {
                                            echo 'No';
                                        }
                                        ?>
                                    </td>
                                    <td class="center-align">
                                        <?php
                                        if ($account->card_charge_flag == APConstants::CARD_CHARGE_OK) {
                                            echo 'OK';
                                        } elseif ($account->card_charge_flag == APConstants::CARD_CHARGE_FAIL) {
                                            echo 'FAIL';
                                        } else {
                                            echo 'N.A.';
                                        }
                                        ?>
                                    </td>
                                    <td class="center-align"><a
                                            class="delete managetables-icon-delete" title="Delete"
                                            data-id="<?php echo $account->payment_id ?>">&nbsp;</a></td>
                                </tr>
                                <?php } ?>
                        <?php } else if ($customer->invoice_type == '2' && empty($customer->invoice_code)) { ?>
                            <tr>
                            <?php $customer = APContext::getCustomerLoggedIn(); ?>
                            <?php if ($customer->activated_flag == '1') { ?>
                                    <td class="center-align"></td>
                                    <td class="center-align">Invoice</td>
                                    <td class="center-align"></td>
                                    <td class="center-align"></td>
                                    <td class="center-align"></td>
                                    <td class="center-align"></td>
                                    <td class="center-align"></td>
                                    <td class="center-align"></td>
                            <?php } else { ?>
                                    <td class="center-align" colspan="7">There is no account.</td>
                            <?php } ?>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <div class="ym-clearfix"></div>
                <!--  
            <div class="pagination">
                <div class="wrap">
                <?php echo $page_link; ?>
                </div>
            </div>
                -->
            </div>
        </div>
        <div class="ym-clearfix" style="height: 35px;"></div>

        <div>
            <div class="ym-grid">
                <h4 class="COLOR_063">Add New Payment Method</h4>
                <br /> 
                <button type="button" id="add_payment_method" class="input-btn btn-yellow">Add payment method</button>
                <br />
                <br />
            </div>
        </div>
        
        <div class='ym-grid'>
            <div class='ym-gl'>
                <h4 class="COLOR_063">Make a deposit payment into your account</h4>
                <a id="paymentPayoneButton">
                    <img alt="Check out with Payone by VISA card" src="<?php echo APContext::getImagePath()?>/visa.png" />
                    <img alt="Check out with Payone by Master card" src="<?php echo APContext::getImagePath()?>/mastercard.png" />
                </a>
                <a id="paymentPayPalButton">
                    <img src="<?php echo APContext::getImagePath()?>/paypal.gif" alt="Check out with PayPal" style="width: 120px" />
                </a>
            </div>
        </div>
        
        <!-- insert automatic charge setting -->
        <br />
        <br />
        <?php 
            if($is_valid_payment_method && APContext::isPrimaryCustomerUser()){
                include ("system/virtualpost/modules/account/views/automatic_charge.php");
            }
        ?>

    </div>
</div>
<!-- Content for dialog -->
<div class="hide">
    <div id="addPaymentMethod" title="Add Payment Method" class="input-form dialog-form"></div>
</div>
<div style="display: none">
    <a id="display_payment_confirm" class="iframe" href="#">Goto payment view</a>

    <div id="confirmRetryPaymentWindow" title="Payment Error" class="input-form dialog-form">
        <p style="font-size: 14px;text-align: left; color:red; font-weight: bold;">A Charge to your payment method was unsuccessful.</p>
    </div>
    
    <div id="paymentWithPaypalWindow" title="Payment With PayPal" class="input-form dialog-form"></div>
    <div id="createDirectChargeWithoutInvoice" title="Make a deposit from credit card" class="input-form dialog-form"></div>
</div>
<script type="text/javascript" src="https://secure.pay1.de/client-api/js/ajax.js"></script>

<script type="text/javascript">
// Call back function
    function processPayoneResponse(response) {
        if (response.get('status') == 'VALID') {
            $('#addEditPaymentMethod_pseudocardpan').val(response.get('pseudocardpan'));
            $('#addEditPaymentMethod_truncatedcardpan').val(response.get('truncatedcardpan'));
            $('#addEditPaymentMethod_card_number').val('');
            $('#addEditPaymentMethod_cvc').val('');

            // Submit form
            $('#addEditPaymentMethodForm').submit();
        } else {
            $.displayError(response.get('customermessage'));
        }
    }
    jQuery(document).ready(function ($) {
        // Get config
        var merchant_id = "<?php echo $this->config->item('payone.merchant-id'); ?>";
        var portal_id = "<?php echo $this->config->item('payone.portal-id'); ?>";
        var sub_account_id = "<?php echo $this->config->item('payone.sub-account-id'); ?>";
        var mode = "<?php echo $this->config->item('payone.mode'); ?>";
        var encoding = "<?php echo $this->config->item('payone.encoding'); ?>";
        var add_payment = "<?php if ($add_payment) {
    echo $add_payment;
} ?>";

        $('input:checkbox.customCheckbox').checkbox({cls: 'jquery-safari-checkbox'});
        $('span.jquery-safari-checkbox').css('height', '15px');
        $('#display_payment_confirm').fancybox({
            width: 500,
            height: 300
        });

        /**
         * Process when user click to add group button
         */
        $('#add_payment_method').click(function () {
            addPaymentMethod();
            return false;
        });

        // Auto open popup
        if (add_payment == "1") {
            addPaymentMethod();
        }

        /**
         * Process when user click to delete icon.
         */
        $('.managetables-icon-delete').live('click', function () {
            var payment_id = $(this).data('id');

            $.ajaxExec({
                url: '<?php echo base_url() ?>payment/check_valid_card',
                success: function (data) {
                    // Show confirm dialog
                    $.confirm({
                        message: data.message,
                        yes: function () {
                            var submitUrl = '<?php echo base_url() ?>payment/delete?id=' + payment_id;
                            $.ajaxExec({
                                url: submitUrl,
                                success: function (data) {
                                    if (data.status) {
                                        if (data.message == '') {
                                            // Reload data grid
                                            document.location = "<?php echo base_url() ?>payment";
                                        } else {
                                            $.displayInfor(data.message, null, function () {
                                                // Reload data grid
                                                document.location = "<?php echo base_url() ?>payment";
                                            });
                                        }
                                    } else {
                                        $.displayError(data.message);
                                    }
                                }
                            });
                        }
                    });
                }
            });


        });

        $('.select_primarycard').live('click', function () {
            var payment_id = $(this).data('id');
            var submitUrl = '<?php echo base_url() ?>payment/set_primarycard?id=' + payment_id;
            $.ajaxExec({
                url: submitUrl,
                success: function (data) {
                    if (data.status) {
                        // Reload data grid
                        document.location = "<?php echo base_url() ?>payment";
                    } else {
                        $.displayError(data.message);
                    }
                }
            });
        });

        function addPaymentMethod() {
            // Clear control of all dialog form
            $('#addPaymentMethod').html('');

            // Open new dialog
            $('#addPaymentMethod').openDialog({
                autoOpen: false,
                height: 510,
                width: 550,
                modal: true,
                open: function () {
                    $(this).load("<?php echo base_url() ?>payment/add", function () {
                        //$('#addEditLocationForm_LocationName').focus();
                    });
                },
                buttons: {
                    'Save': function () {
                        var accountType = $('#addEditPaymentMethod_account_type').val();
                        if (accountType == '30') {
                            var invoice_code = $('#addEditPaymentMethod_card_number').val();
                            if (invoice_code.length == 10) {
                                $('#addEditPaymentMethod_invoice_type').val('2');
                                $('#addEditPaymentMethod_invoice_code').val(invoice_code);
                            } else if (invoice_code.length > 10) {
                                $('#addEditPaymentMethod_invoice_type').val('1');
                            } else {
                                $.displayError('Card number is invalid.');
                                return;
                            }

                            if ($('#addEditPaymentMethod_invoice_type').val() == '1') {
                                client_check_credit_card();
                            } else if ($('#addEditPaymentMethod_invoice_type').val() == '2') {
                                saveInvoiceMethod();
                            }
                        } else if (accountType == '20') {
                            savePaypalMethod();
                        } else if (accountType == '10') {
                            saveDepositInvoiceMethod();
                        }
                    },
                    'Cancel': function () {
                        $(this).dialog('close');
                    }
                }
            });

            $('#addPaymentMethod').dialog('option', 'position', 'center');
            $('#addPaymentMethod').dialog('open');
        }

        /**
         * Save invoice method
         */
        function saveDepositInvoiceMethod() {
            var submitUrl = '<?php echo APContext::getFullBasePath() ?>payment/add_deposit_invoice_method';
            $.ajaxSubmit({
                url: submitUrl,
                formId: 'addEditPaymentMethodForm',
                success: function (data) {
                    if (data.status) {
                        // Reload data grid
                        document.location = "<?php echo base_url() ?>payment";
                    } else {
                        $.displayError(data.message);
                    }
                }
            });
        }

        /**
         * Save invoice method
         */
        function saveInvoiceMethod() {
            var submitUrl = '<?php echo APContext::getFullBasePath() ?>payment/add_invoice_method';
            $.ajaxSubmit({
                url: submitUrl,
                formId: 'addEditPaymentMethodForm',
                success: function (data) {
                    if (data.status) {
                        $.displayInfor(data.message, null, function () {
                            // Reload data grid
                            document.location = "<?php echo base_url() ?>payment";
                        });
                    } else {
                        $.displayError(data.message);
                    }
                }
            });
        }

        /**
         * Save invoice method
         */
        function savePaypalMethod() {
            var submitUrl = '<?php echo APContext::getFullBasePath() ?>payment/add_paypal_method';
            $.ajaxSubmit({
                url: submitUrl,
                formId: 'addEditPaymentMethodForm',
                success: function (data) {
                    if (data.status) {
                        $.displayInfor(data.message, null, function () {
                            // Reload data grid
                            document.location = "<?php echo base_url() ?>payment";
                        });
                    } else {
                        $.displayError(data.message);
                    }
                }
            });
        }

        // Check credit card
        function client_check_credit_card() {
            // validate input
            if ($('#addEditPaymentMethod_card_name').val() == "") {
                $.displayError('Enter the Cardholder Name.');
                return;
            }
            if ($('#addEditPaymentMethod_card_number').val() == "") {
                $.displayError('Enter the Card Number.');
                return;
            }
            if ($('#addEditPaymentMethod_card_type').val() == "") {
                $.displayError('Enter the Card Type.');
                return;
            }
            if ($('#addEditPaymentMethod_cvc').val() == "") {
                $.displayError('Enter the CVC.');
                return;
            }
            if ($('#addEditPaymentMethod_expired_year').val() == "" || $('#addEditPaymentMethod_expired_month').val() == "") {
                $.displayError('Enter the expiration date.');
                return;
            }

            var hash = '<?php echo $hash ?>';
            // Prepare data to check credit card
            var data = {
                request: 'creditcardcheck',
                responsetype: 'JSON',
                mode: mode,
                mid: merchant_id,
                aid: sub_account_id,
                portalid: portal_id,
                encoding: encoding,
                storecarddata: 'yes',
                hash: hash,
                cardholder: $('#addEditPaymentMethod_card_name').val(),
                cardpan: $('#addEditPaymentMethod_card_number').val(),
                cardtype: $('#addEditPaymentMethod_card_type').val(),
                cardcvc2: $('#addEditPaymentMethod_cvc').val(),
                cardexpiredate: $('#addEditPaymentMethod_expired_year').val() + "" + $('#addEditPaymentMethod_expired_month').val(),
                language: 'en'
            };
            var options = {
                return_type: 'object',
                callback_function_name: 'processPayoneResponse'
            };

            var request = new PayoneRequest(data, options);
            request.checkAndStore();
        }

        /**
         * Submit form
         */
        $('#addEditPaymentMethodForm').live('submit', function () {
            /**
            // Check open balance to display message confirm
            <?php $open_balance = APUtils::getCurrentBalance(APContext::getCustomerCodeLoggedIn()); ?>
            var openBalance = <?php echo $open_balance ?>;
            if (openBalance > 0.1) {
                // Display message
                var openBalanceFormat = "<?php echo APUtils::number_format($open_balance); ?>";
                var message = "Thank you. For your account to reactivate we will need to charge your payment instrument with your outstanding balance of: " + openBalanceFormat + " EUR";
                // Show confirm dialog
                $.confirm({
                    message: message,
                    yes: function () {
                        savePaymentMethod();
                    }
                });
            } else {
                savePaymentMethod();
            }
            */
            savePaymentMethod();
            return false;
        });

        /**
         * Save payment method
         */
        function savePaymentMethod() {
            // Call check 3D secure
            var submitUrl = '<?php echo APContext::getFullBasePath() ?>payment/check_3dcredit_card';
            $.ajaxSubmit({
                url: submitUrl,
                formId: 'addEditPaymentMethodForm',
                success: function (data) {
                    if (data.status) {
                        // 3D
                        if (data.message == '1') {
                            var payment_info = "<?php echo lang("payment_information") ?>";
                            $.confirm({
                                okText: 'I understand',
                                class: 'understand',
                                message: payment_info,
                                yes: function () {
                                    callSavePaymentMethod();
                                }
                            });
                        }
                        // None 3D
                        else {
                            callSavePaymentMethod();
                        }
                    } else {
                        $.displayError(data.message);
                    }
                }
            });
        }

        /**
         * Call save payment method
         */
        function callSavePaymentMethod() {
            var submitUrl = $('#addEditPaymentMethodForm').attr('action');
            $.ajaxSubmit({
                url: submitUrl,
                formId: 'addEditPaymentMethodForm',
                success: function (data) {
                    if (data.status) {
                        $('#addPaymentMethod').dialog('close');
                        if (data.redirect) {
                            var submitUrl = data.message;
                            $('#display_payment_confirm').attr('href', submitUrl);
                            $('#display_payment_confirm').click();
                        } else {
                            $.displayInfor(data.message, null, function () {
                                // Reload data grid
                                document.location = "<?php echo base_url() ?>payment";
                            });
                        }

                    } else {
                        // $.displayError(data.message);
                        openConfirmRetryPayment();
                    }
                }
            });
        }

        // Open confirm retry payment
        function openConfirmRetryPayment() {
            // Display dialo confirm retry again
            $('#confirmRetryPaymentWindow').openDialog({
                autoOpen: false,
                height: 160,
                width: 550,
                modal: true,
                buttons: {
                    'Change payment method': function () {
                        $(this).dialog('destroy');
                    },
                    'Retry': function () {
                        addPaymentMethod();
                    }
                }
            });

            $('#confirmRetryPaymentWindow').dialog('option', 'position', 'center');
            $('#confirmRetryPaymentWindow').dialog('open');
        }
        
        /**
        * Payone payment
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
       $('#paymentPayPalButton').live('click', function() {
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
        
    });
</script>