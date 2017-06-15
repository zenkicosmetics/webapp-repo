var is_enteprise_customer = '<?php echo APContext::isPrimaryCustomerUser(); ?>';
var activated_flag = '<?php echo $customer->activated_flag ?>';
var is_user_enterprise = '<?php echo APContext::isUserEnterprise($customer->customer_id); ?>';

// setup registration flag
var selection_clevvermail_product = "<?php echo isset($customer_product_setting['SELECTION_CLEVVER_PRODUCT'])? $customer_product_setting['SELECTION_CLEVVER_PRODUCT']: 1; ?>"; // default is postbox clevvermail = 1, 2 is clevverphone, 3 is enterprise account.;
if(is_enteprise_customer == '1' || is_user_enterprise == '1'){
    // change selection to "enterprise" option.
    selection_clevvermail_product = "enterprise";
}


var shipping_address_completed_flag = '<?php echo isset($customer_product_setting['shipping_address_completed']) ? $customer_product_setting['shipping_address_completed']: 0; ?>';
var postbox_name_completed_flag = '<?php echo isset($customer_product_setting['postbox_name_flag']) ? $customer_product_setting['postbox_name_flag']: 0; ?>';
var invoicing_address_completed_flag = '<?php echo isset($customer_product_setting['invoicing_address_completed']) ? $customer_product_setting['invoicing_address_completed']: 0; ?>';
var name_comp_address_completed_flag = '<?php echo isset($customer_product_setting['name_comp_address_flag']) ? $customer_product_setting['name_comp_address_flag']: 0; ?>';
var city_address_completed_flag = '<?php echo isset($customer_product_setting['city_address_flag']) ? $customer_product_setting['city_address_flag']: 0; ?>';
var payment_detail_completed_flag = '<?php echo isset($customer_product_setting['payment_detail_flag']) ? $customer_product_setting['payment_detail_flag']: 0; ?>';
var postboxes_enterprise_completed_flag = '<?php echo isset($customer_product_setting['activate_10_postbox_enterprise_customer']) ? $customer_product_setting['activate_10_postbox_enterprise_customer']: 0; ?>';
var first_add_product_completed_flag = true;
var activate_add_phone_number_completed_flag = "<?php echo isset($customer_product_setting['activate_add_phone_number']) ? $customer_product_setting['activate_add_phone_number']: 0; ?>";
var email_confirmation_flag = '<?php echo isset($customer_product_setting['email_confirm_flag']) ? $customer_product_setting['email_confirm_flag']: 0; ?>';
var add_another_product_window = '<?php echo isset($customer_product_setting['add_another_product_window']) ? $customer_product_setting['add_another_product_window']: 0; ?>';

var skip = $('#hiddenSubmitEnvelopeForm_skip').val();
var accept_terms_condition_flag = '<?php echo $customer->accept_terms_condition_flag ?>';
var direct_access_customer_view_flag = '0';//'<?php echo $direct_access_customer_view_flag ?>';
var decline_tc_flag = $('#hiddenSubmitEnvelopeForm_skip').val();//"<?php echo isset($_COOKIE['decline_tc_flag_' . $customer_id]) ? $_COOKIE['decline_tc_flag_' . $customer_id] : 0 ?>";
//if ((accept_terms_condition_flag == '0') && (decline_tc_flag != '1')) {
//if ((accept_terms_condition_flag == '0') && (skip != '1')) {
//    openChangeTermsWindow();
//}

var payment_method = "<?php echo $customer->invoice_type ?>";
var enterprise_status_completed = (activated_flag == 1 && is_enteprise_customer && postboxes_enterprise_completed_flag == 0 ) ? false : true;
if ( (activated_flag != '1' || !enterprise_status_completed) && direct_access_customer_view_flag != '1') {
    $('#notification_container').show();

    if (skip != 1){
        if(shipping_address_completed_flag != "1" && invoicing_address_completed_flag != "1" && payment_detail_completed_flag != "1"
            && (name_comp_address_completed_flag != "1" || activate_add_phone_number_completed_flag != "1") ){
            openWelcomeWindow();
        } else{
            openNextStepSetup(selection_clevvermail_product);
        }
    } else if(is_enteprise_customer && postboxes_enterprise_completed_flag == 0){
        if ($('#hiddenSubmitEnvelopeForm_skip').val() != 1){
            openNextStepSetup(selection_clevvermail_product);
        }
    }
} else {
    $('#notification_container').hide();
    // Open popup declare envelope customs for shipping
    if (display_declare_customs == '1') {
        $('#notification_container').show();
        //$('#left_notification_bar').hide();
    }
    var first_regist = $('#hiddenSubmitEnvelopeForm_first_regist').val();
    var trigger_charge_open_balance_due = '<?php echo isset($trigger_charge_open_balance_due) ? $trigger_charge_open_balance_due : 0; ?>';
    if (first_regist == '1' && trigger_charge_open_balance_due == '1') {
        openPaymentPopupAfterAddNewCard();
    }
    else if (pending_envelope_id > 0) {
        openDeclareCustoms(pending_envelope_id);
    }
}

/**
* Process when close jquery window, 
*/
$('div#registedAddressWindow, div#registedPostboxNameWindow, div#openPostboxEnterpriseSetupWindow, div#registedPaymentWindow, div#welcomeWindow, div#confirmEmailWindow, div#selectClevvermailProductWindow, div#addPhoneNumberWindow, div#confirmEmailDialogWindow') .bind('dialogclose', function(event) {
    $('#hiddenSubmitEnvelopeForm_skip').val('1');
    $('#hiddenSubmitEnvelopeForm').submit();
    //window.location.reload();
});
$('div#comp_setup_process').bind('dialogclose', function(event) {
    $('#hiddenSubmitEnvelopeForm_skip').val('1');
    $('#hiddenSubmitEnvelopeForm').submit();
    //window.location.reload();
});
$('div#createDirectChargeWithoutInvoice').bind('dialogclose', function(event) {
    $('#hiddenSubmitEnvelopeForm_skip').val('1');
    $('#hiddenSubmitEnvelopeForm').submit();
});

/**
* Process when close jquery window, 
*/
$('div#thankingWindow, div#priceInfoWindow') .bind('dialogclose', function(event) {
    $('#hiddenSubmitEnvelopeForm_skip').val('1');
    $('#hiddenSubmitEnvelopeForm').submit();
});

/**
* When user click setup process.
*/
$('#setup_process').click(function(){
    if(shipping_address_completed_flag != "1" && invoicing_address_completed_flag != "1" && payment_detail_completed_flag != "1"
        && (name_comp_address_completed_flag != "1" || activate_add_phone_number_completed_flag != "1" || postboxes_enterprise_completed_flag != '1') ){
        openWelcomeWindow();
    } else{
        openNextStepSetup(selection_clevvermail_product);
    }
});

$('#shipping_not_completed, #invoicing_not_completed').click(function() {
    openAddressWindow();
});

$('#postnoxname_not_completed, #name_comp_address_not_completed, #city_address_not_completed').click(function() {
    <?php if (APContext::isPrimaryCustomerUser()) { ?>
        openPostboxEnterpriseSetupWindow();
    <?php } else { ?>
        openPostboxNameWindow();
    <?php } ?>
});

$("input[name='address_company_name'], input[name='address_name']").live("keyup",function(){
    checkCompanyName();
});

$('#payment_detail_not_completed').click(function() {
    openPaymentWindow();
});

$('#register_phone_number_product').click(function() {
    addNewPhoneNumberWindow();
});

$('#complete_ten_postboxes_enterprise').click(function() {
    openPostboxEnterpriseSetupWindow();
});

/**
* Submit form
*/
$('#addEditPaymentMethodForm').live('submit', function(){
    // Check open balance to display message confirm
    <?php //$open_balance = APUtils::getCurrentBalance(APContext::getCustomerCodeLoggedIn()); ?>
    var openBalance = <?php echo $open_balance ?>;
    if (openBalance > 0.1) {
        // Display message
        var openBalanceFormat = "<?php echo APUtils::number_format($open_balance); ?>";
        var message = "Thank you. For your account to reactivate we will need to charge your payment instrument with your outstanding balance of: " + openBalanceFormat + " EUR";
        // Show confirm dialog
        $.confirm({
           message: message,
           yes: function() {
               savePaymentMethod();
           }
        });
    } else {
        savePaymentMethod();
    }
    return false;
});

$(".accept_terms_condition").click(function(){
    $('#comp_setup_process').dialog('destroy');
    openChangeTermsWindow();
});

$("#decline_tc").click(function(){
    <?php //setcookie("decline_tc_flag_" . $customer_id, 1, time() + 7200) ?> 
    $('#openTermsWarningWindow, #changtermsWindow').dialog('destroy');
    $('#hiddenSubmitEnvelopeForm_skip').val('1');
    $('#hiddenSubmitEnvelopeForm').submit();
});

$("#accept_tc").click(function(){ 
    if( $("#check_active_button_accept_new_terms").val()=="0" ){
        $.displayInfor("Please read the terms and conditions first");
        return false;
    }
    else{
        accept_terms_condition();
    }
});

$("#changtermsWindow").scroll(function() {
    var scrolled_val = $("#changtermsWindow").scrollTop().valueOf();
    if(scrolled_val >= 3400){
        if(! $(".btn_accept_terms").hasClass("btn-yellow")){
            $(".btn_accept_terms").addClass("btn-yellow");
            $("#check_active_button_accept_new_terms").val(1);
            $(".btn_accept_terms_active").focus();
        }
    } else if( $(".btn_accept_terms").hasClass("btn-yellow")){
        $(".btn_accept_terms").removeClass("btn-yellow");
        $("#check_active_button_accept_new_terms").val(0);
    }
});

// email confirm
$("#email_confirmation_not_completed").live('click', function(e){
    showEmailConfirmationWindow();
});

// open balance link.
$("#open_balance_link").live("click", function(){
    //window.open("<?php echo base_url() ?>invoices");
    //return;

    var invoice_type = '<?php echo $customer->invoice_type ?>';
    
    if(invoice_type == '1'){
        // open credit card window
        openThankingWindow();
    }else{
        // open bank account window.
        openThankingWindowForDepositInvoice();
    }
});

// Show payment method window
var open_balance = parseFloat(<?php echo $open_balance;//APUtils::getCurrentBalance(APContext::getCustomerCodeLoggedIn()) ?>);
if (open_balance > 0.01 && activated_flag == '1') {
<?php if ($customer->invoice_type === '1') { ?>
    // PAYPAL (Dung TODO)
    // openInvoicePaymentWindow();
<?php } else if ($customer->invoice_type === '2') { ?>
    // openPaypalPaymentWindow();
<?php } else if ($customer->invoice_type === '3') { ?>
    // openCreditCardPaymentWindow();
<?php } ?>
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
 * Save invoice method
 */
function saveInvoiceMethod() {
    var submitUrl = '<?php echo APContext::getFullBasePath() ?>payment/add_invoice_method';
    $.ajaxSubmit({
        url: submitUrl,
        formId: 'addEditPaymentMethodForm',
        success: function (data) {
            if (data.status) {
                $('#registedPaymentWindow').dialog('destroy');

                payment_detail_completed_flag = "1";
                openNextStepSetup(selection_clevvermail_product);
            } else {
                $.displayError(data.message);
            }
        }
    });
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
                $('#registedPaymentWindow').dialog('destroy');
                //openThankingWindowForDepositInvoice();

                payment_detail_completed_flag = "1";
                openNextStepSetup(selection_clevvermail_product);
            } else {
                $.displayError(data.message);
            }
        }
    });
}

/**
 * Save paypal method
 */
function savePaypalMethod() {
    var submitUrl = '<?php echo APContext::getFullBasePath() ?>payment/add_paypal_method';
    $.ajaxSubmit({
        url: submitUrl,
        formId: 'addEditPaymentMethodForm',
        success: function (data) {
            if (data.status) {
                $('#registedPaymentWindow').dialog('destroy');
                // openThankingWindow();
                payment_detail_completed_flag = "1";
                openNextStepSetup(selection_clevvermail_product);
            } else {
                $.displayError(data.message);
            }
        }
    });
}

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
                    $.confirmPayment({
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
                    // fix
                    //$('#registedPaymentWindow').dialog('close');
                    $(this).dialog('destroy');
                    openThankingWindow();
                }
            } else {
                // $.displayError(data.message);
                // Display dialo confirm retry again
                openConfirmRetryPayment();
            }
        }
    });
}

/**
* Open welcome screen
*/
function openWelcomeWindow() {
   // Open new dialog
   $('#welcomeWindow').openDialog({
       autoOpen: false,
       height: 250,
       width: 650,
       modal: true,
       open: function() {
       },
       buttons: {
           'Start': function() {
               $(this).dialog('destroy');
               // openAddressWindow();
               // openPostboxNameWindow();
               // openNextStepSetup(selection_clevvermail_product);
               openSelectProductWindow();
           },
           'Skip': function () {
               $.confirm({
                   message: 'Do you want to skip the setup process. Your account will not work properly until you do?',
                   yes: function() {
                       $('#welcomeWindow').dialog('close');
                   }
               });
           }
       }
   });

   $('#welcomeWindow').dialog('option', 'position', 'center');
   $('#welcomeWindow').dialog('open');
}

function showEmailConfirmationWindow(){
    $("#confirmEmailDialogWindow").html("");
    // Open new dialog
    $('#confirmEmailDialogWindow').openDialog({
        autoOpen: false,
        height: 250,
        width: 450,
        modal: true,
        open: function () {
            $(this).load("<?php echo base_url() ?>customers/load_email_confirmation", function () {
            });
        }
    });

    $('#confirmEmailDialogWindow').dialog('option', 'position', 'center');
    $('#confirmEmailDialogWindow').dialog('open');
}

/**
* Open address screen.
*/
function openAddressWindow() {
   // Open new dialog
   $('#registedAddressWindow').openDialog({
       autoOpen: false,
       height: 550,
       width: 950,
       modal: true,
       closeOnEscape: false,
       open: function(event, ui) {
           $(this).load("<?php echo base_url() ?>customers/register_address", function() {
               $('#shipment_address_name_id').focus();
           });
       },
       buttons: {
           'Next': function() {
               var submitUrl = $('#saveAddressForm').attr('action');
               $.ajaxSubmit({
                   url: submitUrl,
                   formId: 'saveAddressForm',
                   success: function(data) {
                       if (data.status) {
                            $('#registedAddressWindow').dialog('destroy');

                            shipping_address_completed_flag = "1";
                            invoicing_address_completed_flag = "1";

                            // open next step.
                            openNextStepSetup(selection_clevvermail_product);
                       } else {
                           $.displayError(data.message);
                       }
                   }
               });
           },
           'Skip': function () {
               $.confirm({
                   message: 'Do you want to skip the setup process. Your account will not work properly until you do?',
                   yes: function() {
                       $('#registedAddressWindow').dialog('close');
                   }
               });
           }
       }
   });

   $('#registedAddressWindow').dialog('option', 'position', 'center');
   $('#registedAddressWindow').dialog('open');
}

/**
* check company name input
*/
function checkCompanyName() {
    var companyName = $("input[name='address_company_name']").val();
    var name = $("input[name='address_name']").val();
    if(companyName.toLowerCase() == name.toLowerCase()) {
        $("input[name='address_company_name']").addClass("error");
        return false;
    } else {
        $("input[name='address_company_name").removeClass("error");
        return false;
    }
    return true;
}

/**
* Open postbox
*/
function openPostboxNameWindow() {
    // register clevvermail postbox product.
    selection_clevvermail_product = "1";

    // Open new dialog
    $('#registedPostboxNameWindow').openDialog({
         autoOpen: false,
         height: 420,
         width: 750,
         modal: true,
         closeOnEscape: false,
         open: function(event, ui) {
             $(this).load("<?php echo base_url() ?>customers/register_postboxname", function() {
                 //$('#addEditLocationForm_LocationName').focus();
             });
         },
         buttons: {
              'Next': function() {
                   var companyName = $("input[name='address_company_name']").val();
                   var name = $("input[name='address_name']").val();
                   if(companyName.toLowerCase() == name.toLowerCase()) {
                       $.displayError("<?php echo lang('error_company_same_name') ?>");
                       $("input[name='address_company_name']").addClass("error");
                       return ;
                   }
                   var submitUrl = $('#saveRegisterPostboxNameForm').attr('action');
                   $.ajaxSubmit({
                        url: submitUrl,
                        formId: 'saveRegisterPostboxNameForm',
                        success: function(data) {
                             if (data.status) {
                                 $('#registedPostboxNameWindow').dialog('destroy');

                                 postbox_name_completed_flag = "1";
                                 name_comp_address_completed_flag = "1";
                                 city_address_completed_flag = "1";

                                 // open next step.
                                 openNextStepSetup(selection_clevvermail_product);
                             } else {
                                 $.displayError(data.message);
                             }
                        }
                   });
              },
              'Skip': function () {
                  $.confirm({
                      message: 'Do you want to skip the setup process. Your account will not work properly until you do?',
                      yes: function() {
                          $('#registedPostboxNameWindow').dialog('close');
                      }
                  });
              }
         }
    });

   $('#registedPostboxNameWindow').dialog('option', 'position', 'center');
   $('#registedPostboxNameWindow').dialog('open');
}

// Display message to nofity customer
function openReactivateConfirmWindow() {
   var message = 'Your account has been deactivated due to failed payment with your standard payment method. Please provide new payment details to reactivate your account.';
   // Show confirm dialog
   $.confirm({
       message: message,
       yes: function() {
           openPaymentWindow();
       }
   });
}

/**
* Open postbox
*/
function openPaymentWindow() {
   // Open new dialog
   $('#registedPaymentWindow').openDialog({
       autoOpen: false,
       height: 530,
       width: 550,
       modal: true,
       closeOnEscape: false,
       open: function(event, ui) {
           $(this).load("<?php echo base_url() ?>customers/register_payment", function() {
               //$('#addEditLocationForm_LocationName').focus();
           });
       },
       buttons: {
           'Next': function() {
               var accountType = $('#addEditPaymentMethod_account_type').val();
               console.log("Account Type:" + accountType);
               if (accountType === '30') {
                    var invoice_code = $('#addEditPaymentMethod_card_number').val();
                    if (invoice_code.length == 10) {
                        $('#addEditPaymentMethod_invoice_type').val('2');
                        $('#addEditPaymentMethod_invoice_code').val(invoice_code);
                    } else  if (invoice_code.length > 10) {
                        $('#addEditPaymentMethod_invoice_type').val('1');
                    } else {
                        $.displayError('Card number is invalid.');
                        return;
                    }

                    if ($('#addEditPaymentMethod_invoice_type').val() == '1'){
                        client_check_credit_card();
                    } else if ($('#addEditPaymentMethod_invoice_type').val() == '2'){
                        saveInvoiceMethod();
                    }
               } else if (accountType === '20') {
                    console.log('Call save paypal method');
                    savePaypalMethod();
               } else if (accountType === '10') {
                    console.log('Call save invoice method');
                    saveDepositInvoiceMethod();
                    //complete_setup_process();
               }
           },
           'Skip': function () {
               $.confirm({
                   message: 'Do you want to skip the setup process. Your account will not work properly until you do?',
                   yes: function() {
                       $('#registedPaymentWindow').dialog('close');
                   }
               });
           }
       }
   });

   $('#registedPaymentWindow').dialog('option', 'position', 'center');
   $('#registedPaymentWindow').dialog('open');
}

/**
* Open Accept Terms and Condition
*/
function openChangeTermsWindow() {
    // Open new dialog
    $('#changtermsWindow').openDialog({
         autoOpen: false,
         height: 500,
         width: 800,
         modal: true,
         open: function() {
             $(this).load("<?php echo base_url() ?>info/view_term_inline?popup_flag=1", function() {
             });
         },
         close : function (){
             openTermsWarningWindow();
         },       
         buttons: {
             "accept" : {
                 click: function () {
                     if($("#check_active_button_accept_new_terms").val()=="0"){
                         $.displayInfor("Please read the terms and conditions first");
                         return false;
                     }
                     else{
                         accept_terms_condition();
                     }
                 },
                 text: 'Accept',
                 class: 'btn_accept_terms'
             }
         }
    });

    $('#changtermsWindow').dialog('option', 'position', 'center');
    $('#changtermsWindow').dialog('open');
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
                openPaymentWindow();
            }
        }
    });

    $('#confirmRetryPaymentWindow').dialog('option', 'position', 'center');
    $('#confirmRetryPaymentWindow').dialog('open');
}

/**
* Open email confirm screen
*/
// TODO- DUNT: DO NOT THIS FUNCTION AT THE MOMENT
function openConfirmEmailWindow() {
    // Open new dialog
    $('#confirmEmailWindow').openDialog({
        autoOpen: false,
        height: 200,
        width: 550,
        modal: true,
        open: function () {

        },
        buttons: {
            'Finish': function () {
                $(this).dialog('destroy');
                //openThankingWindow();
                openNextStepSetup(selection_clevvermail_product);
            },
            'Skip': function () {
                $.confirm({
                    message: 'Do you want to skip the setup process. Your account will not work properly until you do?',
                    yes: function () {
                        $('#confirmEmailWindow').dialog('close');
                    }
                });
            }
        }
    });

    $('#confirmEmailWindow').dialog('option', 'position', 'center');
    $('#confirmEmailWindow').dialog('open');
}

/**
 * Open welcome screen
 */
function openThankingWindow() {
    // Open new dialog
    $('#thankingWindow').openDialog({
        autoOpen: false,
        height: 400,
        width: 650,
        modal: true,
        open: function () {
            $(this).load("<?php echo base_url() ?>addresses/thanking_page", function () {
                var addressDiv = $("#thankingWindow_addressDivContainer").html();
                addressDiv = $.trim(addressDiv.replace(/<p>/g, "").replace(/<\/p>/g, "").replace(" ", ""));

                if(addressDiv != ''){
                    $("#thankingWindow").dialog("option", "height", 450);
                }else{
                    $("#thankingWindow").dialog("option", "height", 330);
                }
            });
        },
        buttons: {
            'Finish': function () {
                $(this).dialog('destroy');

                location.reload();
                //openPriceInfoWindow();
            }
        }
    });

    $('#thankingWindow').dialog('option', 'position', 'center');
    $('#thankingWindow').dialog('open');
}
/*
 * Des:
 */
function complete_setup_process() {

    // Open new dialog
    $('#comp_setup_process').openDialog({
        autoOpen: false,
        height: 220,
        width: 550,
        modal: true,
        open: function () {
            $('#changtermsWindow').dialog('destroy');
        },
        buttons: {
            'Close': function () {
                $('#comp_setup_process').dialog('destroy');
                $('#hiddenSubmitEnvelopeForm_skip').val('1');
                $('#hiddenSubmitEnvelopeForm').submit();
            }
        }
    });
    $('#comp_setup_process').dialog('option', 'position', 'center');
    $('#comp_setup_process').dialog('open');
}

/**
 * Open welcome screen
 */
function openThankingWindowForDepositInvoice() {
    $('#thankingWindow').html("");
    // Open new dialog
    $('#thankingWindow').openDialog({
        autoOpen: false,
        height: 600,
        width: 650,
        modal: true,
        open: function () {
            $(this).load("<?php echo base_url() ?>addresses/thanking_page_deposit_invoice", function () {
                
            });
        },
        buttons: {
            'Finish': function () {
                $(this).dialog('destroy');
                openPriceInfoWindow();
            }
        }
    });

    $('#thankingWindow').dialog('option', 'position', 'center');
    $('#thankingWindow').dialog('open');
}

/**
 * Open price information
 */
function openPriceInfoWindow() {
    // Open new dialog
    $('#priceInfoWindow').openDialog({
        autoOpen: false,
        height: 640,
        width: 1150,
        modal: true,
        closeOnEscape: false,
        open: function (event, ui) {
            $(this).load("<?php echo base_url() ?>customers/view_pricing", function () {
            });
        },
        buttons: {
            'Close': function () {
                //$('#priceInfoWindow').dialog('close');
                $(this).dialog('destroy');
                $('#hiddenSubmitEnvelopeForm_skip').val('1');
                if(payment_detail_completed_flag == 0 || email_confirmation_flag == 0 || accept_terms_condition_flag == 0){
                    complete_setup_process();
                }
            }
        }
    });

    $('#priceInfoWindow').dialog('option', 'position', 'center');
    $('#priceInfoWindow').dialog('open');
}

/**
* Open Confirm Close Terms and Conditions
*/
function openTermsWarningWindow() {
       openChangeTermsWindow();

       if($("#check_active_button_accept_new_terms").val()=="1"){
           $(".btn_accept_terms").addClass("btn_accept_terms_active");
           $(".btn_accept_terms_active").focus();
       }
       else{
           $(".btn_accept_terms").removeClass("btn_accept_terms_active");
       }

   // Open new dialog
   $('#openTermsWarningWindow').openDialog({
       autoOpen: false,
       height: 210,
       width: 600,
       modal: true,
       open: function() {}
   });

   $('#openTermsWarningWindow').dialog('option', 'position', 'center');
   $('#openTermsWarningWindow').dialog('open');
}

/**
* accept terms and condition.
*/
function accept_terms_condition(){
    var submitUrl = '<?php echo base_url() ?>customers/accept_terms_condition';
    $.ajaxSubmit({
        url:submitUrl,
        success: function (data) {
           $('#openTermsWarningWindow, #changtermsWindow').dialog('destroy');
           window.location.reload();
        }
    });
}

/**
* Show postbox address, name, company setup for enterprise customer.
* @returns {undefined}
*/
function openPostboxEnterpriseSetupWindow(){
    $("#openPostboxEnterpriseSetupWindow").html("");

    // Open new dialog
    $('#openPostboxEnterpriseSetupWindow').openDialog({
        autoOpen: false,
        height: 605,
        width: 1150,
        modal: true,
        closeOnEscape: true,
        open: function(event, ui) {
            $(this).load("<?php echo base_url() ?>customers/register_address_postbox_enterprise_user", function() { });
        },
        close: function(){
            $(this).dialog('destroy');
        }, 
        buttons: {
            "Save & Next": function() {
                // do validate 
                var is_valid = validateSavePostboxEnteprise();
                if(!is_valid){
                    $.displayError("Please update the dummy data.");
                    return false;
                }
                
                // do save function.
                var submitUrl = '<?php echo base_url() ?>customers/save_register_address_postbox_enterprise_user';
                $.ajaxSubmit({
                    url:submitUrl,
                    formId: "registerAddressPostboxEnterpriseUserForm",
                    success: function (data) {
                       if(data.status){
                           $('#openPostboxEnterpriseSetupWindow').dialog("destroy");
                           $.displayInfor(data.message, '', function(){
                               $('#hiddenSubmitEnvelopeForm').submit();
                               location.reload();
                           });
                       }else{
                           $.displayError(data.message);
                       }
                    }
                });
            },
            "Skip": function(){
                $.confirm({
                    message: 'Do you want to skip the setup process. Your account will not work properly until you do?',
                    yes: function() {
                        $('#openPostboxEnterpriseSetupWindow').dialog('destroy');
                        $('#hiddenSubmitEnvelopeForm_skip').val('1');
                        $('#hiddenSubmitEnvelopeForm').submit();
                    }
                });
            }
        }
    });

    $('#openPostboxEnterpriseSetupWindow').dialog('option', 'position', 'center');
    $('#openPostboxEnterpriseSetupWindow').dialog('open');
}

/**
* validate dummy data when save 10 postbox of enterprise.
*/
function validateSavePostboxEnteprise(){
    return true;
    var result = true;
    $(".registerAddressPostboxEnterpriseUserForm_user_name").each(function(){
        var index = $(this).data('index');
        var user_name = $(this).parent().parent().find("#registerAddressPostboxEnterpriseUserForm_user_name" + index).val();
        var postbox_name = $(this).parent().parent().find("#registerAddressPostboxEnterpriseUserForm_postbox_name" + index).val();
        var email = $(this).parent().parent().find("#registerAddressPostboxEnterpriseUserForm_email" + index).val();
        var fake_email = user_name + "@clevvermail.com";
        
        if(user_name == postbox_name || email == fake_email){
            result = false;
        }
    });

    return result;
}

/**
* open select product window
*/
function openSelectProductWindow(){
    // Open new dialog
    $('#selectClevvermailProductWindow').openDialog({
        autoOpen: false,
        height: 500,
        width: 650,
        modal: true,
        open: function() {
            $(this).load("<?php echo base_url() ?>customers/load_select_product_register", function(data) { });
        },
        buttons: {
            'Next': function() {
                $(this).dialog('destroy');

                // save product selection.
                $.ajaxExec({
                    url: '<?php echo base_url() ?>customers/save_selection_product_register',
                    data: {
                        product_type: $("#selectClevvermailProductWindow_selectProduct").val()
                    },
                    success: function (response) {
                        // select phone product.
                        if(is_enteprise_customer){
                            selection_clevvermail_product = 'enterprise';
                        }else if($("#selectClevvermailProductWindow_selectProduct").val() == "<?php echo APConstants::CLEVVERPHONE_PRODUCT ?>"){
                            //addNewPhoneNumberWindow();
                            selection_clevvermail_product = "<?php echo APConstants::CLEVVERPHONE_PRODUCT ?>";
                        }else{
                            // default: clevvermail product.
                            //openPostboxNameWindow();
                            selection_clevvermail_product = "<?php echo APConstants::CLEVVERMAIL_PRODUCT ?>";
                        }

                        openNextStepSetup(selection_clevvermail_product);
                    }
                });

            },
            'Skip': function () {
                $.confirm({
                    message: 'Do you want to skip the setup process. Your account will not work properly until you do?',
                    yes: function() {
                        $('#selectClevvermailProductWindow').dialog('destroy');
                    }
                });
            }
        }
    });

    $('#selectClevvermailProductWindow').dialog('option', 'position', 'center');
    $('#selectClevvermailProductWindow').dialog('open');
}

/**
* open add phone number window
* @returns {Boolean}     */
function addNewPhoneNumberWindow(){
    // register clevver phone product.
    selection_clevvermail_product = "2";

    // Clear control of all dialog form
    $('#addPhoneNumberWindow').html('');

    // Open new dialog
    $('#addPhoneNumberWindow').openDialog({
        autoOpen: false,
        height: 500,
        width: 850,
        modal: true,
        open: function () {
            $(this).load('<?php base_url() ?>account/number/add', function () {
                
            });
        }
        /**
        buttons: {
            'Save': function () {
                savePhoneNumber();
            },
            'Cancel': function () {
                $(this).dialog('close');
            }
        }
        */
    });
    $('#addPhoneNumberWindow').dialog('option', 'position', 'center');
    $('#addPhoneNumberWindow').dialog('open');
    return false;
}

    // Click to book select phone number
    $('#bookSelectedNumberButton').live('click', function(){
        if($("#addEditNumberForm_confirm_terms_condition").prop('checked') != true){
            $.error({message: "In order to use our services, you must agree to ClevverMail's Terms of Service."});
            return;
        }
        savePhoneNumber();
        return false;
     });

/**
* save my phone number.
* @returns {undefined}     */
function savePhoneNumber() {
    var $selRadio = $('input[name=radio_dataGridResult_phonelist]:checked');
    if ($selRadio.length  == 0) {
        $.displayError('Please select phone number in the list.');
        return;
    }
    var selectPhoneNumber = $selRadio.val();
    var range = $selRadio.attr('data-range');
    var initial_amount = $selRadio.attr('data-initial_amount'); 
    $('#addEditNumberForm_phone_number').val(selectPhoneNumber);
    $('#addEditNumberForm_range').val(initial_amount);
    $('#addEditNumberForm_initial_amount').val(range);
    var submitUrl = $('#addEditNumberForm').attr('action');
    var action_type = $('#h_action_type').val();
    $.ajaxSubmit({
        url: submitUrl,
        formId: 'addEditNumberForm',
        success: function (data) {
            if (data.status) {
                if (action_type === 'add') {
                    $('#addNumberWindow').dialog('close');
                }
            } else {
                $.displayError(data.message);
            }
        }
    });
}

/**
* open add phone number window
* @returns {Boolean}     */
function addAnotherClevvermailProductWindow(){
    add_another_product_window = 1;
    
    // Clear control of all dialog form
    $('#addAnotherClevverProductWindow').html('');

    // Open new dialog
    $('#addAnotherClevverProductWindow').openDialog({
        autoOpen: false,
        height: 600,
        width: 800,
        modal: true,
        open: function () {
            $(this).load('<?php base_url() ?>customers/add_another_clevver_product', function () {
            });
        },
        buttons: {
            'Continue': function () {
                $('#addAnotherClevverProductWindow').dialog('destroy');

                first_add_product_completed_flag = false;

                // open next step.
                openNextStepSetup(selection_clevvermail_product);
            }
        }
    });
    $('#addAnotherClevverProductWindow').dialog('option', 'position', 'center');
    $('#addAnotherClevverProductWindow').dialog('open');
    return false;
}














// ================================================================================ 
//                         MAIN FUNCTION CONTROL
//=================================================================================

// open next step of clevvermail postbox product
function openClevverMailProductNextStepSetup(){
    // open postbox register window
    if(postbox_name_completed_flag != '1' || name_comp_address_completed_flag != '1' || city_address_completed_flag != "1"){
        openPostboxNameWindow();
        return;
    }

    // open address window
    if(invoicing_address_completed_flag != '1' || shipping_address_completed_flag != '1'){
        openAddressWindow();
        return;
    }

    // open add new product window.
    if(add_another_product_window != "1"){
        addAnotherClevvermailProductWindow();
        return;
    }

    // open payment window.
    if(payment_detail_completed_flag != "1"){
        openPaymentWindow();
        return;
    }
    
    // open email confirmation window.
    if(email_confirmation_flag == 0){
        showEmailConfirmationWindow();
        return;
    }
    
    if (accept_terms_condition_flag != '1') {
        openChangeTermsWindow();
        return;
    }

    // open thanking window.
    if(payment_method == "2" || $("#addEditPaymentMethod_account_type").val() == '10'){
        openThankingWindowForDepositInvoice();
    }else{
        openThankingWindow();
    }
    return;
}

// open next step of phone produt
function openClevverPhoneProductNextStepSetup(){
    // open address window
    if(activate_add_phone_number_completed_flag != '1'){
        addNewPhoneNumberWindow();
        return;
    }

    // open address window
    if(invoicing_address_completed_flag != '1' || shipping_address_completed_flag != '1'){
        openAddressWindow();
        return;
    }

    // open add new product window.
    if(add_another_product_window != "1"){
        addAnotherClevvermailProductWindow();
        return;
    }

    // open payment window.
    if(payment_detail_completed_flag != "1"){
        openPaymentWindow();
        return;
    }
    
    // open email confirmation window.
    if(email_confirmation_flag == 0){
        showEmailConfirmationWindow();
        return;
    }

    if (accept_terms_condition_flag != '1') {
        openChangeTermsWindow();
        return;
    }
    
    // open thanking window.
    if(payment_method == "2" || $("#addEditPaymentMethod_account_type").val() == '10'){
        openThankingWindowForDepositInvoice();
    }else{
        openThankingWindow();
    }
    return;
}

// open next step of enterprise customer.
function openEnterpriseNextStepSetup(){
    // open address window
    if(invoicing_address_completed_flag != '1' || shipping_address_completed_flag != '1'){
        openAddressWindow();
        return;
    }

    // open 10 postbox registered window
    if(postboxes_enterprise_completed_flag != '1' && is_user_enterprise != '1'){
        openPostboxEnterpriseSetupWindow();
        return;
    }

    // open payment window.
    if(payment_detail_completed_flag != '1' && is_user_enterprise != '1'){
        openPaymentWindow();
        return;
    }
    
    // open email confirmation window.
    if(email_confirmation_flag == 0){
        showEmailConfirmationWindow();
        return;
    }
    
    if (accept_terms_condition_flag != '1') {
        openChangeTermsWindow();
        return;
    }
}

/**
* Open next step when registration window or first login.
* */
function openNextStepSetup(type){
    if(type == "1"){
        // clevverMail product
        openClevverMailProductNextStepSetup();
    } else if(type == "2"){
        // clevverPhone product.
        openClevverPhoneProductNextStepSetup();
    } else if (type == "enterprise"){
        // Enterprise account
        openEnterpriseNextStepSetup();
    }
}
