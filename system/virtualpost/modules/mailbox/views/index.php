<?php
$current_method = $method;
$enable_action = true;
$trash_folder = '0';
$customer_id = $customer->customer_id;

if ($current_method === 'instore') {
    $enable_action = true;
}
if ($current_method === 'trash') {
    $trash_folder = '1';
    $enable_action = false;
}

if (!empty($list_pending_envelope_customs) && $declare_customs_flag == '1') {
    $pending_envelope_id = $list_pending_envelope_customs[0]->envelope_id;
} else {
    $pending_envelope_id = 0;
}
$only_has_welcome_letter = 0;
if (APUtils::onlyHasWelcomeLetter($customer_id)) {
    $only_has_welcome_letter = 1;
}

$direct_access_customer_view_flag = APContext::isAdminDirectAccessCustomerView();

$postbox_id = isset($current_postbox) ? $current_postbox : 0;
$postbox_verification_flag = '0';

if (CaseUtils::isVerifiedAddress($customer_id) && CaseUtils::isVerifiedPostboxAddress($postbox_id, $customer_id)) {
    $postbox_verification_flag = '1';
}

$enable_fedex_shipping_func = false;
if (Settings::get(APConstants::ENABLE_FEDEX_SHIPPING_KEY) == APConstants::ON_FLAG) {
    $enable_fedex_shipping_func = true;
}

// Gets list envelopes
$list_envelope_ids = array();
foreach ($envelopes as $element) {
    $list_envelope_ids[] = $element->id;
}

// List all label.
$list_label_settings = Settings::get_list(APConstants::ENVELOPE_TYPE_CODE);

// Gets setting label.
$setting_label_list = array();
foreach ($envelopes as $element) {
    $setting_label_list[$element->id] = '';
    foreach ($list_label_settings as $el) {
        if ($el->ActualValue == $element->envelope_type_id) {
            $setting_label_list[$element->id] = $el->LabelValue;
            break;
        }
    }
}

// Gets package code.
$setting_alias_code_list = array();
foreach ($envelopes as $element) {
    $setting_alias_code_list[$element->id] = '';
    foreach ($list_label_settings as $el) {
        if ($el->ActualValue == $element->envelope_type_id) {
            $setting_alias_code_list[$element->id] = $el->Alias01;
            break;
        }
    }
}

// Gets category code list.
$category_code_list = Settings::get_list(APConstants::CATEGORY_TYPE_CODE);

// get dropbox setting.
$dropboxV2 = APContext::getDropbox();
$dropbox_setting = $dropboxV2->getSetting();
$has_collect_envelope_pending_customs = false;

$isUserEnterprise = APContext::isUserEnterprise($customer_id);
?>
<style>
    span#effect_date {
        position: absolute;
        right: 40px;
        font-weight: normal !important;
        top: 12px;
    }
</style>
<div class="items">
    <table id="mainMailboxTable" style="width: 100%">
        <thead>
            <tr>
                <th style="width:40px;"><a id="chkAllLink"><?php language_e('mailbox_view_index_MainTableAll')?></a></th>
                <th class="left-align"><a><?php language_e('mailbox_view_index_MainTableFrom')?></a></th>
                <th style="width:50px;"><a><?php language_e('mailbox_view_index_MainTableType')?></a></th>
                <th style="width:60px;"><a><?php language_e('mailbox_view_index_MainTableWeight')?></a></th>
                <th style="width:70px;"><a><?php language_e('mailbox_view_index_MainTableDate')?></a></th>
                <th style="width:55px;"><?php language_e('mailbox_view_index_MainTableEnvelopeScan')?></th>
                <th style="width:55px;"><?php language_e('mailbox_view_index_MainTableItemScan')?></th>
                <th style="width:40px;"><?php language_e('mailbox_view_index_MainTableMailInterface')?></th>
                <th style="width:100px;"><?php language_e('mailbox_view_index_MainTableCategory')?></th>
                <th style="width:55px;"><?php language_e('mailbox_view_index_MainTableCloud')?></th>
                <th style="width:55px;" class="tipsy_tooltip-forwarding" title='<?php language_e('mailbox_view_index_TooltipDirectForwarding')?>'>
                    <?php language_e('mailbox_view_index_MainTableDirectForwarding')?></th>
                <th style="width:55px;" class="tipsy_tooltip-forwarding" title='<?php language_e('mailbox_view_index_TooltipCollectForwarding')?>'>
                    <?php language_e('mailbox_view_index_MainTableCollectForwarding')?></th>
                <th style="width:40px;"><?php language_e('mailbox_view_index_MainTableDelete')?></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($envelopes as $envelope) { ?>
                <?php
                $envelope_class = 'envelop';
                $item_class = 'scan_email';
                $cloud_class = 'cloud';
                $send_class = 'send';
                $collect_class = 'collect';
                $mark_invoice_class = 'unmark-invoice';
                // check declare custom
                $isPendingDeclareCustomsDirectShipment = APUtils::isPendingForDeclareCustoms($envelope->id, $list_pending_envelope_customs, '1');
                $isPendingDeclareCustomsCollectShipment = APUtils::isPendingForDeclareCustoms($envelope->id, $list_pending_envelope_customs, '2');

                // Setting class for envelope icon
                if ($envelope->envelope_scan_flag == null) {
                    $envelope_class = 'envelop';
                } else if ($envelope->envelope_scan_flag == '0') {
                    $envelope_class = 'envelop-yellow';
                } else if ($envelope->envelope_scan_flag == '1') {
                    $envelope_class = 'envelop-blue';
                } else if ($envelope->envelope_scan_flag == '2') {
                    $envelope_class = 'envelop-orange';
                }

                // Setting class for item scan
                if ($envelope->item_scan_flag == null) {
                    $item_class = 'scan_email';
                } else if ($envelope->item_scan_flag == '0') {
                    $item_class = 'scan_email-yellow';
                } else if ($envelope->item_scan_flag == '1') {
                    $item_class = 'scan_email-blue';
                } else if ($envelope->item_scan_flag == '2') {
                    $item_class = 'scan_email-orange';
                }

                // Setting class for cloud icon
                if ($envelope->sync_cloud_flag == '1') {
                    $cloud_class = 'cloud-blue';
                } else {
                    $cloud_class = 'cloud';
                }

                // Setting class for send icon
                if ($envelope->direct_shipping_flag == '2') {
                    $send_class = 'send-orange';
                } else if ($envelope->direct_shipping_flag == null) {
                    $send_class = 'send';
                    // Append red icon
                    if ($isPendingDeclareCustomsDirectShipment) {
                        $send_class = $send_class . ' envelop_direct_red';
                    }
                } else if ($envelope->direct_shipping_flag == '0') {
                    $send_class = 'send-yellow';
                    // Append red icon
                    if ($isPendingDeclareCustomsDirectShipment) {
                        $send_class = $send_class . ' envelop_direct_red';
                    }
                } else if ($envelope->direct_shipping_flag == '1') {
                    $send_class = 'send-blue';
                }

                // Setting class for collect forwarding
                if ($envelope->collect_shipping_flag == '2') {
                    $collect_class = 'collect-orange';
                } else if ($envelope->collect_shipping_flag == null) {
                    $collect_class = 'collect';
                    // Append red icon (for collect shipment if has at least on envelop required, we should trigger all)
                    if ($isPendingDeclareCustomsCollectShipment) {
                        $has_collect_envelope_pending_customs = true;
                        $collect_class = $collect_class . ' envelop_collect_red';
                    }
                } else if ($envelope->collect_shipping_flag == '0') {
                    $collect_class = 'collect-yellow';
                    // Append red icon (for collect shipment if has at least on envelop required, we should trigger all)
                    if ($isPendingDeclareCustomsCollectShipment) {
                        $has_collect_envelope_pending_customs = true;
                        $collect_class = $collect_class . ' envelop_collect_red';
                    }
                } else if ($envelope->collect_shipping_flag == '1') {
                    $collect_class = 'collect-blue';
                }

                $new_notification_flag = '';
                if ($envelope->new_notification_flag === APConstants::ON_FLAG) {
                    $new_notification_flag = 'new_notification';
                    if ($envelope->envelope_scan_flag !== null || $envelope->item_scan_flag != null ||
                            $envelope->sync_cloud_flag === '1' || $envelope->direct_shipping_flag != null || $envelope->collect_shipping_flag != null) {
                        $new_notification_flag = '';
                    }
                }

                $tracking_number_url = "";
                if (!empty($envelope->tracking_number_url) && (substr(trim($envelope->tracking_number_url), -1) == "=")) {

                    $tracking_number_url = trim($envelope->tracking_number_url) . $envelope->tracking_number;
                } else if (!empty($envelope->tracking_number_url)) {

                    $tracking_number_url = $envelope->tracking_number_url;
                }
                ?>
                <tr id="row_<?php echo $envelope->id ?>" class="mailbox_row_action <?php echo $new_notification_flag ?>">
                    <td class="center-align"><input type="checkbox" id="checkbox_<?php echo $envelope->id ?>" class="mailbox_selected customCheckbox" data-id="<?php echo $envelope->id ?>"/></td>
                    <td><?php echo $envelope->from_customer_name; ?></td>
                    <td class="center-align"><?php echo $setting_label_list[$envelope->id]; ?></td>
                    <td class="center-align"><?php echo number_format($envelope->weight, 0) . $envelope->weight_unit ?> </td>
                    <td class="center-align"><?php echo APUtils::convert_timestamp_to_date($envelope->incomming_date, 'd.m.Y') ?> </td>
                    <td>
                        <?php include ("system/virtualpost/modules/mailbox/views/partial/envelope_scan.php"); ?>
                    </td>
                    <td class="center-align">
                        <?php include ("system/virtualpost/modules/mailbox/views/partial/document_scan.php"); ?>
                    </td>
                    <td class="center-align">
                        <?php include ("system/virtualpost/modules/mailbox/views/partial/send_invoice.php"); ?>
                    </td>
                    <td class="center-align">
                        <?php
                        echo my_form_dropdown(array(
                            "data" => $category_code_list,
                            "value_key" => 'ActualValue',
                            "label_key" => 'LabelValue',
                            "value" => $envelope->category_type,
                            "name" => 'category_type',
                            "id" => 'category_type_' . $envelope->id,
                            "clazz" => 'input-text select_category_type',
                            "style" => '',
                            "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <!-- cloud sync -->
                        <?php include ("system/virtualpost/modules/mailbox/views/partial/cloud_sync.php"); ?>
                    </td>
                    <td>
                        <!-- direct shipment -->
                        <?php include ("system/virtualpost/modules/mailbox/views/partial/direct_shippment_item.php"); ?>
                    </td>
                    <td>
                        <!-- collect shipment -->
                        <?php include ("system/virtualpost/modules/mailbox/views/partial/collect_shippment_item.php"); ?>
                    </td>
                    <td>
                        <!-- delete item  -->
                        <?php include ("system/virtualpost/modules/mailbox/views/partial/delete_item.php"); ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <div class="ym-clearfix"></div>

    <div id="paginationContainer" class="pagination" style="border: 1px solid #CDCDCD;">
        <div class="wrap">
            <?php echo $page_link; ?>
        </div>
    </div>
    <form id="downloadEmailForm" action="<?php echo base_url() ?>mailbox/saveas" method="post">
        <input type="hidden" id="downloadEmailForm_envelope_id" name="envelope_id" />
    </form>
    <div class="ym-clearfix"></div>
</div>
<?php
//$step = '0';

//if ($customer_product_setting['shipping_address_completed'] != 1 || $customer_product_setting['invoicing_address_completed'] != 1) {
//    $step = '1';
//} else if ($customer_product_setting['postbox_name_flag'] != 1 || $customer_product_setting['name_comp_address_flag'] != 1 || $customer_product_setting['city_address_flag'] != 1) {
//    $step = '2';
//} else if ($customer_product_setting['payment_detail_flag'] != 1) {
//    $step = '3';
//}
?>
<div id="notification_container" class="hide" style="display: none;">
    <?php if ($customer->status != '1'){ ?>
        <?php if ($customer->activated_flag != '1'): ?>
            <?php if ($customer->deactivated_type == 'auto') {?>
                <div class="joss-notification-account-deactivated">
                    <span style="color: #000;">
                        <?php if($isUserEnterprise){ ?>
                        <?php language_e('mailbox_view_index_AutoDeactiavetedUserAccountNotificationMessage');?>
                        <?php } else {?>
                            <strong style="font-size: 16px;"><?php language_e('mailbox_view_index_AutoDeactiavetedAccountHeader');?></strong><br><br>
                            <?php language_e('mailbox_view_index_AutoDeactiavetedAccountNotification');?><br><br>

                            <?php if($total_open_balance >= 0.01 && $customer_product_setting['payment_detail_flag'] != 1) { ?>
                                <?php language_e('mailbox_view_index_PositiveOpenBalanceNotification', array('invoice_url' => base_url() . 'invoices')) ?>
                            <?php } ?>

                            <?php if(isset($customer) && ($customer->accept_terms_condition_flag == '0')) { ?>
                                <?php language_e('mailbox_view_index_ConfirmTAndCNotification') ?>
                            <?php } ?>

                            <?php if( ($customer_product_setting['shipping_address_completed'] != '1') | ($customer_product_setting['invoicing_address_completed'] != '1')
                                    || ($customer_product_setting['postbox_name_flag'] != '1') || ($customer_product_setting['name_comp_address_flag'] != '1')
                                    || ($customer_product_setting['city_address_flag'] != '1') || ($customer_product_setting['payment_detail_flag'] != '1')
                                    || ($customer_product_setting['email_confirm_flag'] != '1') ) {
                                language_e('mailbox_view_index_AutoDeactivatedAccountUncompletedRegistrationProcessNotification');
                            } ?>
                        <?php }?>
                    </span>
                </div>
            <?php } else { ?>
                <div class="joss-notification-account-deactivated">
                    <span>
                        <?php language_e('mailbox_view_index_DeactivatedAccountUncompletedRegistrationProcessNotification') ?>
                    </span>
                </div>
            <?php } ?>
        <?php endif; ?>
    <?php } ?>
</div>

<div class="hide" style="display: none;">
    <form id="hiddenSubmitEnvelopeForm" action="<?php echo base_url() . 'mailbox/' . $method ?>" method="get">
        <input type="hidden" id="hiddenSubmitEnvelopeForm_current_postbox_id" name="p" value="<?php echo $current_postbox ?>" />
        <input type="hidden" name="skip" value="<?php echo APContext::getSessionValue(APConstants::SESSION_SKIP_CUS_KEY) ?>" id="hiddenSubmitEnvelopeForm_skip" />
        <input type="hidden" name="fullTextSearchFlag" value="<?php echo $fullTextSearchFlag ?>" />
        <input type="hidden" name="fullTextSearchFlag" value="<?php echo $fullTextSearchValue ?>" />
        <input type="hidden" id="hiddenSubmitEnvelopeForm_first_regist" value="<?php echo $first_regist ?>" />
        <input type="hidden" id="hiddenSubmitEnvelopeForm_declare_customs" name="declare_customs" value="0" />
    </form>

    <div id="welcomeWindow" title="<?php language_e('mailbox_view_index_WelcomeWindowTooltip') ?>" class="input-form dialog-form">
        <?php language_e('mailbox_view_index_WelcomeNotification') ?>
    </div>
    <div id="registedAddressWindow" title="<?php language_e('mailbox_view_index_RegistedAddressWindowTooltip') ?>" class="input-form dialog-form">
    </div>
    <div id="registedPostboxNameWindow" title="<?php language_e('mailbox_view_index_RegistedPostboxNameWindowTooltip') ?>" class="input-form dialog-form">
    </div>
    <div id="registedPaymentWindow" title="<?php language_e('mailbox_view_index_RegistedPaymentWindowTooltip') ?>" class="input-form dialog-form">
    </div>
    <div id="thankingWindow" title="<?php language_e('mailbox_view_index_ThankingWindowTooltip') ?>" class="input-form dialog-form">
    </div>
    <?php
        $effective_date = !empty($terms_service->effective_date)? date("m.d.Y", $terms_service->effective_date) : "";
    ?>
    <div id="changtermsWindow" title='<?php language_e('mailbox_view_index_ChangeTermsWindowTooltip', array('effective_date' => $effective_date)) ?>'
         class="input-form dialog-form">
    </div>
    <div id="openTermsWarningWindow" title="<?php language_e('mailbox_view_index_OpenTermsWarningWindowTooltip') ?>" class="input-form dialog-form">
        <p style="color:#000;font-size: 20px;text-align: center;"><?php language_e('mailbox_view_index_TermWarningNotification') ?>
            <?php if(!$isUserEnterprise){
                language_e('mailbox_view_index_NormalUserTermWarningNotification', array('effective_date' => $effective_date));
            }?>
        </p>
        <div style="padding-left: 168px;margin-top: 16px;">
            <button id="decline_tc"><?php language_e('mailbox_view_index_DeclineTCButton') ?></button> &nbsp; &nbsp; &nbsp; &nbsp;
            <button id="accept_tc" style=""><?php language_e('mailbox_view_index_AcceptTCButton') ?></button>
        </div>
    </div>
    <div id="comp_setup_process" title="<?php language_e('mailbox_view_index_SetupProcessTooltip') ?>Your Setup Process" class="input-form dialog-form">
        <p><?php language_e('mailbox_view_index_NotFullyActivateNotification') ?></p>
        <ul>
            <?php if ($customer->email_confirm_flag != "1") { ?>
                <li><?php language_e('mailbox_view_index_ConfirmEmailLink') ?></li>
            <?php } ?>
            <?php if ($total_open_balance > 0.01 && (APContext::isStandardCustomer() || APContext::isAdminCustomerUser() || APContext::isPrimaryCustomerUser()) ) { ?>
                <li><strong><a id="make_payment_now" href="javascript:void()" style="color: #0e76bc;"><?php language_e('mailbox_view_index_MakePaymentLink') ?></a></strong></li>
            <?php } ?>
            <?php if ($customer->accept_terms_condition_flag != "1") { ?>
                <li><strong><a class="accept_terms_condition" href="javascript:void()" style="color: #0e76bc;"><?php language_e('mailbox_view_index_ConfirmTermLink') ?></a></strong></li>
            <?php } ?>
        </ul>
    </div>
    <div id="window_make_payment_now" title="<?php language_e('mailbox_view_index_MakePaymentNowWindowTooltip') ?>Make Payment Now" class="input-form dialog-form">
        <div style="margin-top: 12px;margin-left: 12px;" id="traddEditPaymentMethod_account_type" class="ym-grid input-item">
            <div style="margin-top: 15px;" class="ym-gl ym-g33 register_label">
                <label><?php language_e('mailbox_view_index_PaymentSelection') ?><span class="required">*</span></label>
            </div>
            <div class="ym-gl ">
                <select id="make_payment_type" name="account_type" class="input-txt-none" style="line-height: 24px">
                    <option value="0"><?php language_e('mailbox_view_index_SelectPaymentMethod') ?></option>
                    <option value="<?php echo APConstants::PAYMENT_CREDIT_CARD_ACCOUNT ?>"><?php language_e('mailbox_view_index_CreditCardMethod') ?></option>
                    <option value="<?php echo APConstants::PAYMENT_DIRECT_DEBIT_ACCOUNT ?>"><?php language_e('mailbox_view_index_InvoiceMethod') ?></option>
                </select>
            </div>
        </div>
    </div>
    <div id="createDirectChargeWithoutInvoice" title="<?php language_e('mailbox_view_index_CreateDirectChargeWithoutInvoiceTooltip') ?>" class="input-form dialog-form"></div>

    <div id="confirmEmailWindow" title="Confirm Email" class="input-form dialog-form">
        <?php language_e('mailbox_view_index_ConfirmEmailNotification') ?>
    </div>
    <div id="priceInfoWindow" title="<?php language_e('mailbox_view_index_PriceInfoWindowTooltip') ?>" class="input-form dialog-form">
    </div>
    <div id="declareCustomsWindow" title="<?php language_e('mailbox_view_index_DeclareCustomsWindowTooltip') ?>" class="input-form dialog-form">
    </div>
    <div id="confirmCustomsDeclareWindow" title="<?php language_e('mailbox_view_index_ConfirmCustomsDeclareWindowTooltip') ?>" class="input-form dialog-form">
    </div>
    <div id="invoicePaymentWindow" title="<?php language_e('mailbox_view_index_InvoicePaymentWindowTooltip') ?>" class="input-form dialog-form">
    </div>
    <div id="paypalPaymentWindow" title="<?php language_e('mailbox_view_index_PaypalPaymentWindowTooltip') ?>" class="input-form dialog-form">
    </div>
    <div id="creditCardPaymentWindow" title="<?php language_e('mailbox_view_index_CreditCardPaymentWindowTooltip') ?>" class="input-form dialog-form">
    </div>
    <div id="paymentWithPaypalWindow" title="<?php language_e('mailbox_view_index_PaymentWithPaypalWindowTooltip') ?>" class="input-form dialog-form">
    </div>
    <div id="calculateShippingRateWindow" title="<?php language_e('mailbox_view_index_CalculateShippingRateWindowTooltip') ?>" class="input-form dialog-form">
    </div>
    <div id="changeForwardAddressWindow" title="<?php language_e('mailbox_view_index_ChangeForwardAddressWindowTooltip') ?>" class="input-form dialog-form">
    </div>
    <div id="collectChangeForwardAddressWindow" title="<?php language_e('mailbox_view_index_CollectChangeForwardAddressWindowTooltip') ?>" class="input-form dialog-form">
    </div>
    <div id="calculateShippingRateDetailWindow" title="<?php language_e('mailbox_view_index_CalculateShippingRateDetailWindowTooltip') ?>" class="input-form dialog-form">
    </div>
    <div id="collectShipmentWindow" title="<?php language_e('mailbox_view_index_CollectShipmentWindowTooltip') ?>" class="input-form dialog-form">
        <!--Include-->
        <?php include ("system/virtualpost/modules/mailbox/views/partial/confirm_collect_shipment.php"); ?>
    </div>

    <div id="confirmRetryPaymentWindow" title="<?php language_e('mailbox_view_index_ConfirmRetryPaymentWindowTooltip') ?>" class="input-form dialog-form">
        <p style="font-size: 14px;text-align: left; color:red; font-weight: bold;"><?php language_e('mailbox_view_index_RetryPaymentUnsuccessfullNotification') ?></p>
    </div>

    <div id="openPostboxEnterpriseSetupWindow" title="<?php language_e('mailbox_view_index_OpenPostboxEnterpriseSetupWindowTooltip') ?>" class="input-form dialog-form"></div>

    <a id="display_document_full" class="iframe" href="#"><?php language_e('mailbox_view_index_DocumentView') ?></a>
    <a id="display_envelope_full" class="iframe" href="#"><?php language_e('mailbox_view_index_EnvelopeView') ?></a>

    <input type="hidden" id="hide_panes" value="<?php echo $hide_panes; ?>" />
    <input type="hidden" id="check_active_button_accept_new_terms" value="0" />

    <a id="display_payment_confirm" class="iframe" href="#">Goto payment view</a>
    <a id="display_ios_popup" class="iframe" href="<?php echo base_url() ?>customers/mobile_adv_popup" style="display: none;"><?php language_e('mailbox_view_index_GoToIos') ?></a>
    <div id="forward_address" title="Forwarding Address Book" class="input-form dialog-form"></div>

    <div id="new_forward_address" title="Address Book" class="input-form dialog-form"></div>
    <div id="make_prepayment_dialog" title="Make a Deposit/Pre-Payment" class="input-form dialog-form"></div>

    <div id="selectClevvermailProductWindow" title="Please select your first Clevver product" class="input-form dialog-form"></div>
    <div id="confirmEmailDialogWindow" title="Email confirmation" class="input-form dialog-form"></div>
    <div id="upgradeEnterpriseCustomerConfirmWindow" title="Account Upgrade" class="input-form dialog-form"></div>
    <div id="addPhoneNumberWindow" title="Add a worldwide phone number" class="input-form dialog-form"></div>
    <div id="addAnotherClevverProductWindow" title="Add another product to your Clevver Account" class="input-form dialog-form"></div>
</div>
<?php
    // get parent customer if standard account or enterprise account.
    if( APContext::isPrimaryCustomerUser()){
        $customer = APContext::getParentCustomerLoggedIn();
    }
?>
<?php if ($customer->activated_flag != '1') { ?>
    <script type="text/javascript" src="https://secure.pay1.de/client-api/js/ajax.js"></script>
<?php } ?>
<script type="text/javascript">

//Call back function
    function processPayoneResponse(response) {
        if (response.get('status') == 'VALID') {
            $('#addEditPaymentMethod_pseudocardpan').val(response.get('pseudocardpan'));
            $('#addEditPaymentMethod_truncatedcardpan').val(response.get('truncatedcardpan'));
            $('#addEditPaymentMethod_card_number').val('');
            $('#addEditPaymentMethod_cvc').val('');

            // Call submit method
            $('#addEditPaymentMethodForm').submit();
        } else {
            $.displayError(response.get('customermessage'));
        }
    }

    // Get config
    var merchant_id = "<?php echo $this->config->item('payone.merchant-id'); ?>";
    var portal_id = "<?php echo $this->config->item('payone.portal-id'); ?>";
    var sub_account_id = "<?php echo $this->config->item('payone.sub-account-id'); ?>";
    var mode = "<?php echo $this->config->item('payone.mode'); ?>";
    var encoding = "<?php echo $this->config->item('payone.encoding'); ?>";
    var total_open_balance = "<?php echo $total_open_balance; ?>";

    var pending_envelope_id = "<?php echo $pending_envelope_id ?>";
    var shipping_type = "<?php if (!empty($list_pending_envelope_customs)) { echo $list_pending_envelope_customs[0]->shipping_type; } else { echo '0'; } ?>";
    var display_declare_customs = "<?php if (!empty($list_pending_envelope_customs)) { echo '1'; } else { echo '0';} ?>";

    var list_envelope_id = [];
    var search_flag = '<?php echo $fullTextSearchFlag; ?>';
    var total_envelopes = '<?php echo count($envelopes); ?>';
    var deactivated_type = '<?php echo APContext::getCustomerLoggedIn()->deactivated_type; ?>';

    var total_weight_collect = '<?php echo number_format($total_weight_collect, 0) . $unit ?>';
    var total_weight_collect_storage = '<?php echo number_format($total_weight_storage + $total_weight_collect, 0) . $unit ?>';
    var payment_detail_flag = '<?php echo isset($customer_product_setting['payment_detail_flag']) ? $customer_product_setting['payment_detail_flag']: 0; ?>';
    <?php foreach ($envelopes as $envelope) { ?>
        list_envelope_id.push('<?php echo $envelope->id; ?>');
    <?php } ?>
    if (search_flag == '1' && total_envelopes == 0) {
        $.displayError('No records found');
    }

    // show notification message
    var only_has_welcome_letter = '<?php echo $only_has_welcome_letter ?>';
    var postbox_id = '<?php echo $postbox_id; ?>';
    <?php if ($customer->activated_flag != '0' && $customer->deactivated_type == 'auto'): ?>
        if (total_envelopes == 0) {
            if (only_has_welcome_letter == '1' || postbox_id == 0) {
                var html = '<div class="joss-notification-account-deactivated"><span>';
                html += 'this folder is currently empty.</span></div>';
                $("#content-body-wrapper").append(html);
            } else {
                var html = '<div class="joss-notification-account-deactivated"><span>';
                html += 'This is your post box for incoming items. Currently it is empty. To demonstrate to you how your inbox works, we will put our welcome letter in it shortly. You will be notified by mail.</span></div>';

                $("#content-body-wrapper").append(html);
            }
        }
    <?php endif; ?>

    $('.tipsy_tooltip').tipsy({gravity: 'sw'});
    $('.tipsy_tooltip-forwarding').tipsy({gravity: 'se', width: 1000});
    var iw = $('body').innerWidth();
    $('#display_document_full').fancybox({
        width: 1100,
        height: 800,
        autoScale: true
    });
    $('#display_envelope_full').fancybox({
        width: 1100,
        height: 800,
        autoScale: true
    });
    $('#display_ios_popup').fancybox({
        width: 550,
        height: 450,
        type: 'iframe',
        onClosed: function () {
            $.ajaxExec({
                url: '<?php echo APContext::getFullBasePath(); ?>mailbox/update_session_mobile_adv_popup',
                data: {'flag': '1'},
                success: function (data) {
                    parent.$.fancybox.close();
                }
            });
        }
    });

    jQuery(document).ready(function ($) {
        var show_mobile_avd_flag = '<?php echo ($customer->activated_flag == 1) && ($customer->show_mobile_adv_flag == 0) && (APContext::getSessionValue(APConstants::SESSION_SHOW_MOBILE_ADV_FIRST_LOGIN) != 1) ? 0 : 1; ?>';
        // Only show if user not selected do not show
        if (show_mobile_avd_flag == '0') {
            $('#display_ios_popup').click();
        }
    });

    $('#notification_bar').css('left', (iw - 400) / 2);

    $('#display_payment_confirm').fancybox({
        width: 600,
        height: 400
    });

    $('input:checkbox.customCheckbox').checkbox({cls: 'jquery-safari-checkbox'});
    $('#paginationContainer').css('width', $('#mainMailboxTable').width() - 22);

    $(".change_fw_address").live('click', function () {

        $('#changeForwardAddressWindow').html('');
        $('.scan-popup').hide();
        var envelope_id = $(this).attr('rel');
        $('#changeForwardAddressWindow').openDialog({
            autoOpen: false,
            height: 380,
            width: 630,
            modal: true,
            closeOnEscape: false,
            open: function (event, ui) {
                $(this).load("<?php echo base_url() ?>customers/direct_change_forward_address?envelope_id=" + envelope_id, function () {});
            },
        });
        $('#changeForwardAddressWindow').dialog('option', 'position', 'center');
        $('#changeForwardAddressWindow').dialog('open');

        return false;
    });

    $(".collect_change_fw_address").live('click', function () {
        $('#collectChangeForwardAddressWindow').html('');
        $('.scan-popup').hide();
        var envelope_id = $(this).attr('rel');
        $('#collectChangeForwardAddressWindow').openDialog({
            autoOpen: false,
            height: 400,
            width: 635,
            modal: true,
            closeOnEscape: false,
            open: function (event, ui) {
                $(this).load("<?php echo base_url() ?>customers/collect_change_forward_address?from_flag=2&envelope_id=" + envelope_id + "&arr_package="
                        + JSON.stringify(<?php echo json_encode($arr_package); ?>), function () {});

            },
        });
        $('#collectChangeForwardAddressWindow').dialog('option', 'position', 'center');
        $('#collectChangeForwardAddressWindow').dialog('open');

        return false;
    });

    $("#collectShipmentWindow_manageForwardingAddressButton").live("click", function () {
        $('#collectChangeForwardAddressWindow').html('');
        var include_all = $("#collectShipmentWindow_includeAllStorage").val();

        var envelope_id = $(".item-collect-id.green-item-collect-id:first").data("id");

        if (include_all == 1 && envelope_id === undefined) {
            envelope_id = $(".item-collect-id.storage-item-collect:first").data("id");
        }

        if (envelope_id === undefined) {
            $.displayInfor("There is no item to marked collect shippment.");
            return false;
        }

        $('#collectChangeForwardAddressWindow').openDialog({
            autoOpen: false,
            height: 380,
            width: 630,
            modal: true,
            closeOnEscape: false,
            open: function (event, ui) {
                $(this).load("<?php echo base_url() ?>customers/collect_change_forward_address?green_flag=1&hide_flag=1&envelope_id=" + envelope_id
                        + '&from_flag=1', function () {

                });
            },
        });
        $('#collectChangeForwardAddressWindow').dialog('option', 'position', 'center');
        $('#collectChangeForwardAddressWindow').dialog('open');

        return false;
    });

// Display popup
    $('div.icon_popup_container').hover(function () {
        var d1 = new Date();
        var d2 = new Date();
        while (true) {
            d2 = new Date();
            if (d2.valueOf() >= d1.valueOf() + 200) {
                // 20141013 Start fixbug: #422
                if ($("input[type=checkbox]:checked").length > 1 && $('.scan-popup ul li a', this).hasClass("yes")) {
                    $('.scan-popup h2', this).html("Do you really want to perform this action for the " + $("input[type=checkbox]:checked").length + " selected items?");
                }
                $('.scan-popup', this).show();
                // 20141013 End fixbug: #422
                break;
            }

        }
    }, function () {
        $('.scan-popup', this).hide();
    });
    $("#make_payment_now").click(function () {
        $('#comp_setup_process').dialog('destroy');
        // Open new dialog
        $('#window_make_payment_now').openDialog({
            autoOpen: false,
            height: 180,
            width: 550,
            modal: true,
            open: function () {
            },
            buttons: {
                'Close': function () {
                    $('#hiddenSubmitEnvelopeForm_skip').val('1');
                    $('#window_make_payment_now').dialog('destroy');
                    //openPriceInfoWindow();
                }
            }
        });
        $('#window_make_payment_now').dialog('option', 'position', 'center');
        $('#window_make_payment_now').dialog('open');
    });

    $("#make_payment_type").live("change", function () {
        $('#window_make_payment_now').dialog('destroy');
        if ($(this).val() == '30') {
            createDirectCharge(0);
        }
        if ($(this).val() == '10') {
            paypalPayment();
        }
        $("#make_payment_type").val(0);
    });
    
    // Trigger screen to open balance
    // Base on the flag first_regist=1
    function openPaymentPopupAfterAddNewCard() {
        <?php $open_balance_due = APUtils::getCurrentBalance(APContext::getCustomerCodeLoggedIn()); ?>
        var openBalanceDue = <?php echo $open_balance_due ?>;
        if (openBalanceDue > 0.1) {
            // Display message
            var openBalanceFormat = "<?php echo APUtils::number_format($open_balance_due); ?>";
            var message = "Thank you. For your account to reactivate we will need to charge your payment instrument with your outstanding balance of: " + openBalanceFormat + " EUR";
            // Show confirm dialog
            $.confirm({
                message: message,
                yes: function () {
                    createDirectCharge(openBalanceDue);
                }
            });
        } else {
            createDirectCharge(openBalanceDue);
        }
    }
    /**
     * Create direct charge
     */
    function createDirectCharge(charge_amount) {
        // Clear control of all dialog form
        $('#createDirectChargeWithoutInvoice').html('');

        // Open new dialog
        $('#createDirectChargeWithoutInvoice').openDialog({
            autoOpen: false,
            height: 400,
            width: 720,
            modal: true,
            open: function () {
                $(this).load("<?php echo base_url() ?>customers/create_direct_charge_without_invoice?charge_amount="+charge_amount, function () {});
            },
            buttons: {
                'Submit': function () {
                    saveDirectChargeWithoutInvoice();
                }
            }
        });
        $('#createDirectChargeWithoutInvoice').dialog('option', 'position', 'center');
        $('#createDirectChargeWithoutInvoice').dialog('open');
    }
    ;

    /**
     * Paypal payment
     */
    function paypalPayment() {

        $('#paymentWithPaypalWindow').openDialog({
            autoOpen: false,
            height: 332,
            width: 710,
            modal: true,
            closeOnEscape: false,
            open: function (event, ui) {
                $(this).load("<?php echo base_url() ?>customers/paypal_payment_invoice", function () {
                });
            }
        });

        $('#paymentWithPaypalWindow').dialog('option', 'position', 'center');
        $('#paymentWithPaypalWindow').dialog('open');

    }


    /**
     * When user click declare customs process.
     */
    $('#declare_customs_process').click(function () {
        // Reload data grid
        $('#hiddenSubmitEnvelopeForm_declare_customs').val('1');
        $('#hiddenSubmitEnvelopeForm').submit();
    });

    /**
     * When user click to button send
     */
    $(".yes_sendmail").live('click', function () {
        $('.scan-popup').hide();
        var envelope_id = $(this).data('id');
        var selectedValue = $.getAllSelectedCheckboxValue('mailbox_selected', 'data-id');
        var listId = [];
        for (var i = 0; i < selectedValue.length; i++) {
            if ($('#yes_sendmail_' + selectedValue[i]).hasClass('yes_sendmail')) {
                listId.push(parseInt(selectedValue[i]));
            }
        }
        if ($.inArray(envelope_id, listId) <= -1) {
            listId.push(envelope_id);
        }

        // Will trigger the declare custom here
        var submitUrl = '<?php echo base_url() ?>mailbox/send?envelope_id=' + listId;
        $.ajaxExec({
            url: submitUrl,
            success: function (data) {
                // Submit hidden form to trigger declare customs dialog
                if (data != null && data.data != null && data.data.declare_customs_flag == '1') {
                    // Reload data grid
                    $('#hiddenSubmitEnvelopeForm_declare_customs').val('1');
                    $('#hiddenSubmitEnvelopeForm').submit();
                } else {
                    if (data.prepayment === true) {
                        openEstimateCostDialog(listId, 'direct', 'shipping');
                    } else if (data.status) {
                        // Reload data grid
                        $('#hiddenSubmitEnvelopeForm').submit();
                    } else {
                        $.displayError(data.message);
                    }
                }
            }
        });
    });

    /**
     * When user click to button collect
     */
    $(".yes_collectmail").live('click', function () {
        $('.scan-popup').hide();
        var envelope_id = $(this).data('id');
        requestCollectShipping(envelope_id);
    });

    function requestCollectShipping(envelope_id) {

        //var packagetype = $(this).data('packagetype');
        var selectedValue = $.getAllSelectedCheckboxValue('mailbox_selected', 'data-id');
        var listId = [];
        for (var i = 0; i < selectedValue.length; i++) {
            if ($('#yes_collectmail_' + selectedValue[i]).hasClass('yes_collectmail')) {
                listId.push(parseInt(selectedValue[i]));
            }
        }
        if ($.inArray(envelope_id, listId) <= -1) {
            listId.push(envelope_id);
        }

        var submitUrl = '<?php echo base_url() ?>mailbox/collect?envelope_id=' + listId;
        $.ajaxExec({
            url: submitUrl,
            success: function (data) {
                if (data.status) {
                    // Reload data grid
                    $('#hiddenSubmitEnvelopeForm').submit();
                } else {
                    $.displayError(data.message);
                }
            }
        });
    }

    /**
     * When user click to button collect
     */
    $(".yes_collectmail_requestprepayment").live('click', function () {
        $('.scan-popup').hide();
        var envelope_id = $(this).data('id');
        var packagetype = $(this).data('packagetype');
        var selectedValue = $.getAllSelectedCheckboxValue('mailbox_selected', 'data-id');
        var listId = [];
        for (var i = 0; i < selectedValue.length; i++) {
            if ($('#yes_collectmail_' + selectedValue[i]).hasClass('yes_collectmail')) {
                listId.push(parseInt(selectedValue[i]));
            }
        }
        if ($.inArray(envelope_id, listId) <= -1) {
            listId.push(envelope_id);
        }

        // Open prepayment screen
        openEstimateCostDialog(listId, 'collect', 'shipping');
    });


    /**
     * When user click to button calculte shipping rate (direct shipping only)
     */
    $(".calculate_shipping_rate_link").live('click', function (e) {
        e.preventDefault();
        var shipping_type = $(this).data('shipping_type');
        var envelope_id = $(this).data('id');
        openCalculateShippingRate(shipping_type, envelope_id, 0);
        return  false;
    });

    /**
     * When user click to button calculte shipping rate (collect forwarding only)
     */
    $("#collectShipmentWindow_calculateShippingRateButton").live('click', function () {
        var shipping_type = '2';
        var envelope_id = $(".green-item-collect-id:first").data("id");
        var includedAllFlag = $("#collectShipmentWindow_includeAllStorage").val();
        if (!envelope_id) {
            envelope_id = $(".item-collect-id:first").data("id");
        }
        if (!envelope_id) {
            $.displayInfor("There is no item to marked collect shippment.");
            return false;
        }

        openCalculateShippingRate(shipping_type, envelope_id, includedAllFlag);
    });

    function openCalculateShippingRate(shipping_type, envelope_id, includedAllFlag) {
        $('#calculateShippingRateWindow').html('');
        $('#changeForwardAddressWindow').dialog('destroy');

        var current_postbox_id = $('#hiddenSubmitEnvelopeForm_current_postbox_id').val();
        // Open new dialog
        $('#calculateShippingRateWindow').openDialog({
            autoOpen: false,
            height: 340,
            width: 750,
            modal: true,
            closeOnEscape: false,
            open: function (event, ui) {
                var url = "<?php echo base_url() ?>mailbox/calculate_all_shipping?envelope_id=" + envelope_id
                url += "&shipping_type=" + shipping_type + "&postbox_id=" + current_postbox_id;
                url += "&included_all_flag=" + includedAllFlag;

                $.ajaxExec({
                    url: url,
                    success: function (response) {
                        $("#calculateShippingRateWindow").html(response.data);
                    }
                });
            }
        });
        $('#calculateShippingRateWindow').dialog('option', 'position', 'center');
        $('#calculateShippingRateWindow').dialog('open');
        return false;
    }

    $(".confirmCalculateShipping").live('click', function () {
        var shipping_rate = $(this).data('shipping_rate');
        var shipping_rate_id = $(this).data('shipping_rate_id');
        var envelope_id = $(this).data('id');
        var shipping_type = $(this).data('shipping_type');

        var raw_postal_charge = $(this).data('raw_postal_charge');
        var raw_customs_handling = $(this).data('raw_customs_handling');
        var raw_handling_charges = $(this).data('raw_handling_charges');
        var number_parcel = $(this).data('number_parcel');
        var tracking_information_flag = $(this).data('tracking_information_flag');

        if (tracking_information_flag == '0') {
            // Display confirm of no tracking dialog information
            // Display confirm
            var messageConfirm = 'This shipping service does not provide any tracking information. All risk on items lost or damaged in the shipping process are on customer side.';
            // Show confirm dialog
            $.confirm({
                message: messageConfirm,
                title: 'Warning',
                okText: 'I confirm to take this risk',
                yes: function () {
                    submitConfirmCalculateShipping(shipping_type, envelope_id, shipping_rate_id, shipping_rate, raw_postal_charge, raw_customs_handling, raw_handling_charges, number_parcel);
                }
            });
        } else {
            // Display confirm
            var messageConfirm = 'Do you want to confirm the shipment?';
            // Show confirm dialog
            $.confirm({
                message: messageConfirm,
                yes: function () {
                    submitConfirmCalculateShipping(shipping_type, envelope_id, shipping_rate_id, shipping_rate, raw_postal_charge, raw_customs_handling, raw_handling_charges, number_parcel);
                }
            });
        }

    });

// Submit after confirm shipping service
    function submitConfirmCalculateShipping(shipping_type, envelope_id, shipping_rate_id, shipping_rate, raw_postal_charge, raw_customs_handling, raw_handling_charges, number_parcel) {
        // DIRECT SHIPPING
        if (shipping_type == '1') {
            // Close
            $('#calculateShippingRateWindow').dialog('close');

            // Will trigger the declare custom here
            var submitUrl = '<?php echo base_url() ?>mailbox/send?envelope_id=' + envelope_id;
            submitUrl += '&shipping_rate_id=' + shipping_rate_id;
            submitUrl += '&shipping_rate=' + shipping_rate;
            submitUrl += '&raw_postal_charge=' + raw_postal_charge;
            submitUrl += '&raw_customs_handling=' + raw_customs_handling;
            submitUrl += '&raw_handling_charges=' + raw_handling_charges;
            submitUrl += '&number_parcel=' + number_parcel;

            // Send direct
            $.ajaxExec({
                url: submitUrl,
                success: function (data) {
                    // Submit hidden form to trigger declare customs dialog
                    if (data != null && data.data != null && data.data.declare_customs_flag == '1') {
                        // Reload data grid
                        $('#hiddenSubmitEnvelopeForm_declare_customs').val('1');
                        $('#hiddenSubmitEnvelopeForm').submit();
                    } else {
                        if (data.prepayment === true) {
                            openEstimateCostDialog(envelope_id, 'direct', 'shipping');
                        } else if (data.status) {
                            // Reload data grid
                            $('#hiddenSubmitEnvelopeForm').submit();
                        } else {
                            $.displayError(data.message);
                        }
                    }
                }
            });
            // Collect shipping
        } else if (shipping_type == '2') {
            $('#collectShipmentWindow_selected_shipping_rate_id').val(shipping_rate_id);
            $('#collectShipmentWindow_selected_shipping_rate').val(shipping_rate);
            $('#collectShipmentWindow_selected_raw_postal_charge').val(raw_postal_charge);
            $('#collectShipmentWindow_selected_raw_customs_handling').val(raw_customs_handling);
            $('#collectShipmentWindow_selected_raw_handling_charges').val(raw_handling_charges);
            $('#collectShipmentWindow_selected_number_parcel').val(number_parcel);
            $('#calculateShippingRateWindow').dialog('close');

            // Trigger collect shipment
            $("#collectShipmentWindow_YesButton").click();
        }
    }

    /**
     * When user click link Manage forwarding addresses
     */

    $("a.new_forward_address").live('click', function () {
        var envelope_id = $(this).attr('rel');
        var option = $(this).data('option');
        $('.scan-popup').hide();
        // Open new dialog
        $('#new_forward_address').html('');
        $('#new_forward_address').openDialog({
            autoOpen: false,
            height: 480,
            width: 550,
            modal: true,
            open: function (event, ui) {
                $(this).load("<?php echo base_url() ?>customers/newForwardAddress?envelope_id=" + envelope_id, function () {
                });
            },
            buttons: {
                'Confirm': function () {
                    if ($(".new_shipment_street").val() == '') {
                        $(".new_shipment_street").addClass('error');
                        return;
                    }
                    if ($(".new_shipment_postcode").val() == '') {
                        $(".new_shipment_postcode").addClass('error');
                        return;
                    }
                    if ($(".new_shipment_city").val() == '') {
                        $(".new_shipment_city").addClass('error');
                        return;
                    }
                    var submitUrl = $('#save_New_Forward_AddressForm').attr('action');
                    $.ajaxSubmit({
                        url: submitUrl,
                        formId: 'save_New_Forward_AddressForm',
                        success: function (data) {
                            if (data.status) {
                                $('#new_forward_address').dialog('close');
                                $.infor({
                                    message: data.message,
                                    ok: function () {

                                        var envelope_id = data.data.envelope_id, shipping_address_id = data.data.shipping_address_id;

                                        if (!envelope_id || !shipping_address_id) {
                                            $.displayInfor("There is no item or shipping address selected to direct shipment.");
                                            return false;
                                        }
                                        if (option == 'direct') {

                                            $.ajaxExec({
                                                url: "<?php echo base_url() ?>mailbox/save_shipping_address?shipping_address_id=" + shipping_address_id + '&envelope_id=' + envelope_id,
                                                success: function (data) {
                                                    if (!data) {
                                                        $.displayError("System error occurs. Please contact System Administrator.");
                                                        return;
                                                    }

                                                    $("#changeForwardAddressWindow").dialog("destroy");
                                                    $('#calculateShippingRateWindow').dialog("destroy");

                                                    // show the shipping rate dialog.
                                                    var shipping_type = '1'; //direct
                                                    var includedAllFlag = 0;
                                                    openCalculateShippingRate(shipping_type, envelope_id, includedAllFlag);
                                                }
                                            });

                                        } else if (option == 'collect') {
                                            var from_flag = $("#from_flag").val();
                                            //From button collect forwarding
                                            if (from_flag == 1) {

                                                var include_all_flag = $("#collectShipmentWindow_includeAllStorage").val();
                                                var green_flag = $("#collect_shipping_green_flag").val();

                                                $.ajaxExec({
                                                    url: "<?php echo base_url() ?>mailbox/save_shipping_address?green_flag="+green_flag+"&shipping_address_id="
                                                            + shipping_address_id + '&envelope_id=' + envelope_id + "&include_all_flag=" + include_all_flag,
                                                    success: function (data) {
                                                        if (!data) {
                                                            $.displayError("System error occurs. Please contact System Administrator.");
                                                            return;
                                                        }
                                                        $('#changeForwardAddressWindow').dialog('close');
                                                        $('#collectChangeForwardAddressWindow').dialog('close');
                                                    }
                                                });

                                            }
                                            //From hover popup in main table
                                            else if (from_flag == 2) {
                                                $('.scan-popup').hide();
                                                requestCollectShipping(envelope_id);
                                            }

                                        }
                                    }
                                });

                            } else {
                                $('#new_forward_address').dialog('close');
                                $.displayError(data.message);
                            }
                        }
                    });

                }
            }
        });
        $('#new_forward_address').dialog('option', 'position', 'center');
        $('#new_forward_address').dialog('open');
        return false;
    });

    /**
     * When user click to button Confirm in the calculate shipping rate
     */
    $(".confirm_calculate_shipping_rate_link").live('click', function () {
        $('.scan-popup').hide();
        $('#calculateShippingRateWindow').dialog('close');
        $('#calculateShippingRateDetailWindow').html('');

        var envelope_id = $(this).data('envelope_id');
        var shipping_service_id = $(this).data('shipping_service_id');

        var postal_charge = $(this).data('postal_charge');
        var customs_handling = $(this).data('customs_handling');
        var handling_charges = $(this).data('handling_charges');
        var shipping_type = $(this).data('shipping_type');

        var detail_load_url = "<?php echo base_url() ?>mailbox/calculate_shipping_rate_detail?envelope_id=" + envelope_id;
        detail_load_url += '&shipping_service_id=' + shipping_service_id;
        detail_load_url += '&postal_charge=' + postal_charge;
        detail_load_url += '&customs_handling=' + customs_handling;
        detail_load_url += '&handling_charges=' + handling_charges;
        detail_load_url += '&shipping_type=' + shipping_type;

        // Open new dialog
        $('#calculateShippingRateDetailWindow').openDialog({
            autoOpen: false,
            height: 500,
            width: 850,
            modal: true,
            closeOnEscape: false,
            open: function (event, ui) {
                $(this).load(detail_load_url, function () {
                });
            },
            buttons: {
                'Confirm shipment': function () {
                    var submitUrl = '<?php echo base_url() ?>mailbox/calculate_shipping_rate_detail';
                    $.ajaxSubmit({
                        url: submitUrl,
                        formId: 'calculate_shipping_rate_detail_form',
                        success: function (data) {
                            if (data.status) {
                                $('#calculateShippingRateDetailWindow').dialog('close');
                                // Reload data grid
                                $('#hiddenSubmitEnvelopeForm').submit();
                            } else {
                                $.displayError(data.message);
                            }
                        }
                    });
                }
            }
        });

        $('#calculateShippingRateDetailWindow').dialog('option', 'position', 'center');
        $('#calculateShippingRateDetailWindow').dialog('open');

        return false;
    });

    /**
     * Open popup form to declare customs
     * shipping_type = 1 (Direct forwarding)
     * shipping_type = 2 (Collect forwarding)
     */
    function openDeclareCustoms(envelope_id) {
        $('#declareCustomsWindow').html('');
        // Open popup allow customer declare customs information
        var submitUrl = '<?php echo base_url() ?>mailbox/declare_customs?envelope_id=' + envelope_id;
        // Open new dialog
        $('#declareCustomsWindow').openDialog({
            autoOpen: false,
            height: 600,
            width: 920,
            modal: true,
            closeOnEscape: false,
            open: function (event, ui) {
                $(this).load(submitUrl, function () {
                });
            },
            close: function () {
                $(this).dialog('destroy');
            },
            buttons: {
                'Confirm customs declaration': function () {
                    if ($.trim($("#phone_number").val()) == '') {
                        $.displayError("Please input your phone number.");
                    } else {
                        confirmCustomDeclare();
                    }
                }
            }
        });

        $('#declareCustomsWindow').dialog('option', 'position', 'center');
        $('#declareCustomsWindow').dialog('open');

    }

    var selected_column = ["material_name", "quantity", "cost"];
    var meta_data_column = {
        material_name: {data_type: 'string', allow_null: false, max_length: 255, display_name: 'Material name'},
        quantity: {data_type: 'integer', allow_null: false, max_length: 0, display_name: 'Quantity'},
        cost: {data_type: 'double', allow_null: false, max_length: 0, display_name: 'Cost'}
    };

    /**
     * Open popup confirm custom declare
     */
    function confirmCustomDeclare() {
        var submitData = validateData();
        if (submitData == '') {
            return false;
        }
        // Open popup allow customer declare customs information
        var submitUrl = '<?php echo base_url() ?>mailbox/confirm_customs_declare';
        // Open new dialog
        $('#confirmCustomsDeclareWindow').openDialog({
            autoOpen: false,
            height: 600,
            width: 1000,
            modal: true,
            closeOnEscape: false,
            open: function (event, ui) {
                $(this).load(submitUrl, function () {
                });
            },
            buttons: {
                'Confirm power of attorney': function () {
                    saveDeclareCustoms(submitData);
                }
            }
        });

        $('#confirmCustomsDeclareWindow').dialog('option', 'position', 'center');
        $('#confirmCustomsDeclareWindow').dialog('open');
    }

    /**
     * When user click save button
     */
    function saveDeclareCustoms(submitData) {
        var envelope_id = $('#declare_customs_envelope_id').val();
        var customs_data = JSON.stringify(submitData);

        // Submit to server and refresh
        var submitUrl = '<?php echo base_url() ?>mailbox/save_declare_customs?envelope_id=' + envelope_id;
        $.ajaxExec({
            url: submitUrl,
            data: {customs_data: customs_data, phone_number: $("#phone_number").val()},
            success: function (data) {
                if (data.status) {
                    // Reload data grid
                    $('#hiddenSubmitEnvelopeForm_declare_customs').val('1');
                    $('#hiddenSubmitEnvelopeForm').submit();
                } else {
                    $.displayError(data.message);
                }
            }
        });
    }
    ;

    /**
     * Validate data input.
     */
    function validateData() {
        var gridData = $("#dataGridResult").jqGrid('getGridParam', 'data');
        var submitData = [];
        var lengthData = gridData.length;
        for (var i = 0; i < lengthData; i++) {
            var data_row = gridData[i];
            if (data_row.material_name != '') {
                var valid_data = validateDataRow(data_row, i + 1);
                if (valid_data) {
                    submitData.push(data_row);
                } else {
                    $.displayError('Data row ' + (i + 1) + ' is invalid. Please correct it before submit.');
                    return '';
                }
            }
        }
        if (submitData.length == 0) {
            $.displayError('Please declare customs information.');
            return '';
        }
        return submitData;
    }

    /**
     * Validate data row input.
     */
    function validateDataRow(data_row, row_index) {

        var row_error = false;
        var column_error = false;
        // For each data column

        $.each(selected_column, function (i, column) {
            var data_type = meta_data_column[column].data_type;
            var allow_null = meta_data_column[column].allow_null;
            var max_length = meta_data_column[column].max_length;
            var cell_value = data_row[column];
            column_error = false;

            // Validate required
            if (!allow_null) {
                if ($.isEmpty(cell_value)) {
                    // Log message
                    row_error = true;
                    column_error = true;

                    // Highlight cell color
                    highlightError(row_index, column);
                }
            }

            // Validate data type
            if ($.isNotEmpty(cell_value)) {
                if (data_type == "integer") {
                    if (!$.isValidInt(cell_value)) {
                        // Log message
                        row_error = true;
                        column_error = true;

                        // Highlight cell color
                        highlightError(row_index, column);
                    }
                } else if (data_type == "double") {
                    if (!$.isValidNumber(cell_value)) {
                        // Log message
                        row_error = true;
                        column_error = true;

                        // Highlight cell color
                        highlightError(row_index, column);
                    }
                }
            }

            // Validate max length
            if ($.isNotEmpty(cell_value) && max_length > 0) {
                if (data_type == "string") {
                    if (cell_value.length > max_length) {
                        // Log message
                        row_error = true;
                        column_error = true;

                        // Highlight cell color
                        highlightError(row_index, column);
                    }
                }
            }

            // Remove cell hightlight if cell ok
            if (!column_error) {
                $("#dataGridResult").jqGrid('setCell', row_index, column, "", {color: '#000'});
            } else {
                $("#dataGridResult").jqGrid('setCell', row_index, column, "", {color: 'red'});
            }
        });

        // Remove highlight if no error occur
        if (!row_error) {
            removeHighlightError(row_index, '');
        } else {
            highlightError(row_index, '');
        }
        return !row_error;
    }
    /**
     * Highlight cell color & background of row color
     */
    function highlightError(row_id, column_name) {
        $("#dataGridResult").jqGrid('setCell', row_id, column_name, "", {color: 'red'});
        $('#' + (row_id)).addClass('ui-state-error');
    }
    /**
     * Remove highlight error.
     */
    function removeHighlightError(row_id, column_name) {
        $("#dataGridResult").jqGrid('setCell', row_id, column_name, "", {color: '#000'});
        $('#' + (row_id)).removeClass('ui-state-error');
    }

    /**
     * When user click to button collect
     */
    $(".yes_setting_cloud").live('click', function () {
        document.location = '<?php base_url() ?>cloud';
    });

    /**
     * When user click to button collect
     */
    $(".yes_cloud").live('click', function () {
        $('.scan-popup').hide();
        var id = $(this).attr('data-id');

        // Get all selected item
        var selectedValue = $.getAllSelectedCheckboxValue('mailbox_selected', 'data-id');
        var listId = [];
        for (var i = 0; i < selectedValue.length; i++) {
            if ($('#yes_cloud_' + selectedValue[i]).hasClass('yes_cloud')) {
                listId.push(parseInt(selectedValue[i]));
            }
        }
        if ($.inArray(id, listId) <= -1) {
            listId.push(id);
        }

        var postbox_id = $(this).attr('data-postbox_id');
        var submitUrl = '<?php echo base_url() ?>mailbox/cloud_dropbox?id=' + listId + '&postbox_id=' + postbox_id;
        var requestUrl = '<?php echo base_url() ?>mailbox/request_dropbox';
        $.ajaxExec({
            url: submitUrl,
            success: function (data) {
                if (data.status) {
                    if (data.message == 'login') {
                        // Reload data grid
                        $('#hiddenSubmitEnvelopeForm').attr('action', requestUrl);
                        $('#hiddenSubmitEnvelopeForm').submit();
                    } else {
                        $.displayInfor(data.message, null, function () {
                            $('#hiddenSubmitEnvelopeForm').submit();
                        });
                    }
                } else {
                    $.displayError(data.message);
                }
            }
        });
    });

    /**
     * Process when user click Yes on envelope scan popup
     */
    $('.yes_envelope_scan').live('click', function () {
        $('.scan-popup').hide();
        var id = $(this).attr('id').substr(18);

        // Get all selected item
        var selectedValue = $.getAllSelectedCheckboxValue('mailbox_selected', 'data-id');
        var listId = [];
        for (var i = 0; i < selectedValue.length; i++) {
            if ($('#yes_envelope_scan_' + selectedValue[i]).hasClass('yes_envelope_scan')) {
                listId.push(parseInt(selectedValue[i]));
            }
        }
        if ($.inArray(id, listId) <= -1) {
            listId.push(id);
        }
        var submitUrl = '<?php echo base_url() ?>mailbox/request_envelope_scan?id=' + listId;
        $.ajaxExec({
            url: submitUrl,
            success: function (data) {
                if (data.status) {
                    // Reload data grid
                    $('#hiddenSubmitEnvelopeForm').submit();
                } else {
                    if (data.prepayment == true) {
                        openEstimateCostDialog(listId, 'envelope', 'scanning');
                    } else {
                        $.displayError(data.message);
                    }
                }
            }
        });
    });

    /**
     * Process when user click Yes on envelope scan popup
     */

    $('.yes_open_envelope_scan').live('click', function () {
        $('.scan-popup').hide();
        var id = $(this).attr('id').substr(18);
        var submitUrl = '<?php echo base_url() ?>mailbox/get_file_scan?type=1&envelope_id=' + id;
        $("#display_envelope_full").attr('href', submitUrl);
        $("#display_envelope_full").click();
        return false;

        return;
    });

    /**
     * Process when user click Yes on envelope scan popup
     */
    $('#envelopeItemPreviewFile').live('click', function () {
        var envelope_id = $(this).attr('data-envelope_id');
        var submitUrl = '<?php echo base_url() ?>mailbox/open_envelope_scan?id=' + envelope_id;
        $('#display_envelope_full').attr('href', submitUrl);
        $('#display_envelope_full').click();
    });

    /**
     * Process when user click Yes on envelope scan popup
     */
    $('.yes_open_scan').live('click', function () {
        $('.scan-popup').hide();
        var id = $(this).attr('id').substr(9);
        var submitUrl = '<?php echo base_url() ?>mailbox/get_file_scan?type=2&envelope_id=' + id;
        $("#display_document_full").attr('href', submitUrl);
        $("#display_document_full").click();
        return;
    });

    /**
     * Process when user click Yes on scan popup
     */
    $('.yes_scan').live('click', function () {
        $('.scan-popup').hide();
        var id = $(this).attr('id').substr(9);
        // Get all selected item
        var selectedValue = $.getAllSelectedCheckboxValue('mailbox_selected', 'data-id');
        var listId = [];
        for (var i = 0; i < selectedValue.length; i++) {
            if ($('#yes_scan_' + selectedValue[i]).hasClass('yes_scan')) {
                listId.push(parseInt(selectedValue[i]));
            }
        }
        if ($.inArray(id, listId) <= -1) {
            listId.push(id);
        }

        var submitUrl = '<?php echo base_url() ?>mailbox/request_scan?id=' + listId;
        $.ajaxExec({
            url: submitUrl,
            success: function (data) {
                if (data.status) {
                    // Reload data grid
                    $('#hiddenSubmitEnvelopeForm').submit();
                } else {
                    if (data.prepayment == true) {
                        openEstimateCostDialog(listId, 'item', 'scanning');
                    } else {
                        $.displayError(data.message);
                    }
                }
            }
        });
    });

    /**
     * Process when user click to row
     */
    $('tr.mailbox_row_action').live('click', function (e) {
        var openPaneFlag = $("td:nth-child(6) > div:first", this).hasClass("envelop-blue") || $("td:nth-child(7) > div:first", this).hasClass("scan_email-blue");
        if (openPaneFlag) {
            open_pane_layout();
        }

        var classList = e.target.classList;
        if (classList && classList.length > 0 && (classList.item(0) == 'yes' || classList.item(0) == 'scan-popup')) {
            return;
        }
        var envelope_id = $(this).attr('id').substring(4);

        if ($('#checkbox_' + envelope_id).is(':checked')) {
            $('#checkbox_' + envelope_id).prop('checked', false);
        } else {
            $('#checkbox_' + envelope_id).prop('checked', true);
        }
        checkboxChange($('#checkbox_' + envelope_id), e);
    });

    /**
     * hide layout panes.
     */
    function hide_pane(o) {
        var openPaneFlag = $("td:nth-child(6) > div:first", o).hasClass("envelop-blue") || $("td:nth-child(7) > div:first", o).hasClass("scan_email-blue");
        var flag = false;

        if ($(window).width() <= 1600) {
            if (openPaneFlag) {
                if (myLayout.state.east.isClosed) {
                    flag = true;
                }
            }
        } else {
            if (openPaneFlag) {
                flag = true;
            }
        }

        if (!myLayout.state.east.isClosed) {
            flag = true;
        }

        if (flag) {
            myLayout.open("east");
        } else {
            myLayout.close("east");
        }
    }

    function open_pane_layout() {
        if (myLayout.state.east.isClosed) {
            $(".ui-layout-toggler").click();
        }
    }

    /**
     * Process when user click to check box icon.
     */
    $('.mailbox_selected').live('change', function (e) {
        // Show right side bar when evelop is scanned (envelop-blue )
        hide_pane(this);

        checkboxChange(this, e);
    });

    /**
     * Process when check box change
     */
    function checkboxChange(me, e) {
        // Get list of selected checkbox
        var selectedValue = $.getAllSelectedCheckboxValue('mailbox_selected', 'data-id');

        // comment out: khong cna giu Ctrl de chon envelope.
        if (ctrlDown || shiftDown) {
            // Will mark all item
            if (shiftDown) {
                var first_envelope_id = $('input.mailbox_selected:checkbox:checked:visible:first').attr('data-id');
                var last_envelope_id = $('input.mailbox_selected:checkbox:checked:visible:last').attr('data-id');
                //var last_envelope_id = $(me).attr('data-id');
                var first_index = -1;
                var last_index = -1;

                for (var i = 0; i < list_envelope_id.length; i++) {
                    if (first_envelope_id === list_envelope_id[i]) {
                        first_index = i;
                    }
                    if (last_envelope_id === list_envelope_id[i]) {
                        last_index = i;
                    }
                }

                if (first_index > -1) {
                    for (var i = first_index; i < last_index; i++) {
                        $('#checkbox_' + list_envelope_id[i]).prop('checked', true);
                        $('#row_' + list_envelope_id[i]).addClass('selected');
                    }
                }
            }
        } else {
            // Uncheck all other checkbox
            $.uncheckedAll('mailbox_selected', $(me).attr('id'));
        }


        if ($(me).is(':checked')) {
            var envelope_id = $(me).data('id');
            loadItemByEnveloperId(envelope_id);
            $('#row_' + envelope_id).addClass('selected');
        } else {
            var envelope_id = $(me).data('id');
            $('#mailbox_envelope_image').html('');
            $('#mailbox_document_image').html('');
            $('#saveAsButton').addClass('disable');
            $('#mailSaveAsForm_envelope_id').val('');
            $('#saveAsButton').removeClass('yl');
            $('#row_' + envelope_id).removeClass('selected');

            if (selectedValue.length > 0) {
                var envelope_id = selectedValue[0];
                loadItemByEnveloperId(envelope_id);
            } else {
                $('.mailbox_selected').each(function () {
                    $('#row_' + $(this).data('id')).removeClass('selected');
                });
            }
        }
    }

    /**
     * Load item of envelope id
     */
    function loadItemByEnveloperId(envelope_id) {
        var envelope_image_url = '<?php echo base_url() ?>mailbox/view_envelope_image?envelope_id=' + envelope_id;
        var document_image_url = '<?php echo base_url() ?>mailbox/view_document_image?envelope_id=' + envelope_id;
        // $('#mailbox_envelope_image').load(envelope_image_url);
        $('#mailbox_envelope_image').html("<iframe id='mailbox_envelope_image_iframe' src='" + envelope_image_url + "' style='height:100%; width:250px;overflow: hidden;'><iframe>");

        $('#mailbox_document_image').html("<iframe id='mailbox_document_image_iframe' src='" + document_image_url + "' style='height:100%; width:250px;overflow: hidden;'><iframe>");
        $('#saveAsButton').addClass('yl');
        $('#saveAsButton').removeClass('disable');
        $('#mailSaveAsForm_envelope_id').val(envelope_id);
    }

    /**
     * Process when user click this user
     */
    $('#saveAsButton').click(function () {
        var envelope_id = $('#mailSaveAsForm_envelope_id').val();
        if (envelope_id == '') {
            return;
        }
        var submitUrl = '<?php echo base_url() ?>mailbox/check_file_exist?envelope_id=' + envelope_id;
        $.ajaxExec({
            url: submitUrl,
            success: function (data) {
                if (data.status) {
                    if (data.message == '1') {
                        $('#mailSaveAsForm').submit();
                    } else {
                        $.displayError('Your request file does not exist or deleted.');
                    }
                } else {
                    $.displayError(data.message);
                }
            }
        });
    });


    /**
     * Process when user click to delete icon.
     */
    $('.yes_deletemail').live('click', function () {
        $.delayTime(300);
        $('.scan-popup').hide();
        var envelope_id = $(this).data('id');

        //open_pane_layout();

        // Get all selected item
        var selectedValue = $.getAllSelectedCheckboxValue('mailbox_selected', 'data-id');
        var listId = [];
        for (var i = 0; i < selectedValue.length; i++) {
            if ($('#yes_deletemail_' + selectedValue[i]).hasClass('yes_deletemail')) {
                listId.push(parseInt(selectedValue[i]));
            }
        }

        if ($.inArray(envelope_id, listId) <= -1) {
            listId.push(envelope_id);
        }

        var trash_folder = '<?php echo $trash_folder ?>';
        var delete_type = $(this).data('delete_type');
        var submitUrl = '<?php echo base_url() ?>mailbox/request_delete_envelope?id=' + listId + '&delete_type=' + delete_type;
        submitUrl += '&trash_folder=' + trash_folder;
        $.ajaxExec({
            url: submitUrl,
            success: function (data) {
                if (data.status) {
                    // Reload data grid
                    $('#hiddenSubmitEnvelopeForm').submit();
                } else {
                    $.displayError(data.message);
                }
            }
        });
    });

    /**
     * When user click change on category
     */
    $('.select_category_type').live('change', function () {
        var envelope_id = $(this).attr('id').substring(14);
        var category_type = $(this).val();
        var submitUrl = '<?php echo base_url() ?>mailbox/change_category_type?envelope_id=' + envelope_id + '&category_type=' + category_type;
        $.ajaxExec({
            url: submitUrl,
            success: function (data) {
                if (data.status) {
                } else {
                    $.displayError(data.message);
                }
            }
        });
    });


    $("#collectShipmentWindow_includeAllStorageButton").click(function () {
        var checked = $("#collectShipmentWindow_includeAllStorage").val();
        if (checked == 1) {
            $('#collectShipmentWindow_includeAllStorageButton').html('<span class="ui-button-text"><?php language_e('mailbox_view_index_IncludeAllItems')?></span>');
            $('#collectShipmentWindow_includeAllStorageButton').button();
            $("#collectShipmentWindow_includeAllStorage").val(0);
            $(".storage-item-collect").hide();
            $('#collectShipmentWindow_totalWeight').html(total_weight_collect);
        } else {
            $('#collectShipmentWindow_includeAllStorageButton').html('<span class="ui-button-text"><?php language_e('mailbox_view_index_ExcludeAllItems')?></span>');
            $('#collectShipmentWindow_includeAllStorageButton').button();
            $("#collectShipmentWindow_includeAllStorage").val(1);
            $(".storage-item-collect").show();
            $('#collectShipmentWindow_totalWeight').html(total_weight_collect_storage);
        }
    });

    /**
     * Customer click collect forwarding button.
     */
    $('#customerCollectShippingButton').live('click', function () {
        $('#collectShipmentWindow_includeAllStorage').prop('checked', false);
        // Open new dialog
        $('#collectShipmentWindow').openDialog({
            autoOpen: false,
            height: 400,
            width: 700,
            modal: true,
        });

        $('#collectShipmentWindow').dialog('option', 'position', 'center');
        $('#collectShipmentWindow').dialog('open');

        return false;
    });

    $("#collectShipmentWindow_YesButton").click(function () {
        var submitUrl = '<?php echo base_url() ?>mailbox/collect_shipment?p=' + $('#hiddenSubmitEnvelopeForm_current_postbox_id').val();
        var includeAllStoreFlag = $("#collectShipmentWindow_includeAllStorage").val();

        submitUrl += '&includeAllStoreFlag=' + includeAllStoreFlag;
        submitUrl += '&shipping_rate_id=' + $("#collectShipmentWindow_selected_shipping_rate_id").val();
        submitUrl += '&shipping_rate=' + $("#collectShipmentWindow_selected_shipping_rate").val();
        submitUrl += '&raw_postal_charge=' + $("#collectShipmentWindow_selected_raw_postal_charge").val();
        submitUrl += '&raw_customs_handling=' + $("#collectShipmentWindow_selected_raw_customs_handling").val();
        submitUrl += '&raw_handling_charges=' + $("#collectShipmentWindow_selected_raw_handling_charges").val();
        submitUrl += '&number_parcel=' + $("#collectShipmentWindow_selected_number_parcel").val();

        $.ajaxExec({
            url: submitUrl,
            success: function (data) {
                if (data.status) {
                    $('#collectShipmentWindow').dialog('close');
                    // Submit hidden form to trigger declare customs dialog
                    if (data != null && data.data != null && data.data.declare_customs_flag == '1') {
                        // Reload data grid
                        $('#hiddenSubmitEnvelopeForm_declare_customs').val('1');
                        $('#hiddenSubmitEnvelopeForm').submit();
                    } else if (data.prepayment == true) {
                        openEstimateCostDialog(data.list_envelope_id, 'collect', 'shipping');
                    } else {
                        $.infor({
                            message: data.message,
                            ok: function () {
                                location.reload();
                            }
                        });
                    }
                } else {
                    if (data.prepayment == true) {
                        openEstimateCostDialog(data.list_envelope_id, 'collect', 'shipping');
                    } else {
                        $('#collectShipmentWindow').dialog('close');
                        $.displayError(data.message);
                    }
                }
            },
            timeout: 2400000
        });
    });

    $("#collectShipmentWindow_NoButton").click(function () {
        $('#collectShipmentWindow').dialog('close');
    });

    /*
     * Unmark collect shipping when it still has not been triggered collect shipping
     */
     $('a.unmark-collect-shipping').live('click', function () {
        $('.scan-popup').hide();
        //Get envelope id of this popup
        var id = $(this).data('id');
        // Get all selected envelope in case customer perform multiple select
        var selectedValue = $.getAllSelectedCheckboxValue('mailbox_selected', 'data-id');
        //Create array to contain all envelope id need to process
        var listId = [];
        for (var i = 0; i < selectedValue.length; i++) {
            if ($('#unmark-collect-shipping-' + selectedValue[i]).hasClass('unmark-collect-shipping')) {
                listId.push(parseInt(selectedValue[i]));
            }
        }
        //Push current select envelope id to array
        if ($.inArray(id, listId) <= -1) {
            listId.push(id);
        }
        var submitUrl = '<?php echo base_url() ?>mailbox/unmark_collect';
        $.ajaxExec({
            url: submitUrl,
            data: {
                listId: JSON.stringify(listId)
            },
            success: function (response) {
                if (response.status) {
                    // Reload data grid
                    location.reload();
                } else {
                    $.displayError(response.message);
                }
            }
        });
    });

    // 20141013 Start added: fixbug #422
    var checked_all = false;
    $("#chkAllLink").live("click", function () {
        if (checked_all == false) {
            $(".mailbox_selected").prop('checked', 'checked');
            checked_all = true;
            $(".mailbox_row_action").addClass("selected");
        } else {
            $(".mailbox_selected").prop('checked', '');
            checked_all = false;
            $(".mailbox_row_action").removeClass("selected");
        }
    });
    // 20141013 End added: fixbug #422

    /**
     * Open invoice payment window
     */
    function openInvoicePaymentWindow() {
        // Open new dialog
        $('#invoicePaymentWindow').openDialog({
            autoOpen: false,
            height: 300,
            width: 450,
            modal: true,
            closeOnEscape: false,
            open: function (event, ui) {
                $(this).load("<?php echo base_url() ?>customers/invoice_payment", function () { });
            }
        });

        $('#invoicePaymentWindow').dialog('option', 'position', 'center');
        $('#invoicePaymentWindow').dialog('open');
    }

    /**
     * Open paypal payment window
     */
    function openPaypalPaymentWindow() {
        // Open new dialog
        $('#paypalPaymentWindow').openDialog({
            autoOpen: false,
            height: 350,
            width: 450,
            modal: true,
            closeOnEscape: false,
            open: function (event, ui) {
                $(this).load("<?php echo base_url() ?>customers/paypal_payment", function () { });
            }
        });

        $('#paypalPaymentWindow').dialog('option', 'position', 'center');
        $('#paypalPaymentWindow').dialog('open');
    }

    /**
     * Open credit card payment window
     */
    function openCreditCardPaymentWindow() {
        // Open new dialog
        $('#creditCardPaymentWindow').openDialog({
            autoOpen: false,
            height: 350,
            width: 650,
            modal: true,
            closeOnEscape: false,
            open: function (event, ui) {
                $(this).load("<?php echo base_url() ?>customers/creditcard_payment", function () {
                    //$('#addEditLocationForm_LocationName').focus();
                });
            }
        });

        $('#creditCardPaymentWindow').dialog('option', 'position', 'center');
        $('#creditCardPaymentWindow').dialog('open');
    }

    /**
     *  When user click [declare for customs] on each item
     */
    $('.item_declare_customs').live('click', function () {
        var customs_envelope_id = $(this).attr('id').substring(21);
        openDeclareCustoms(customs_envelope_id);
    });
    $(function () {
        $('.managetables-icon').tipsy({gravity: 'se'});
    });

    /**
     *  Open estimated cost dialog
     */
    function openEstimateCostDialog(list_envelope_id, action_type, type) {

        var url = "<?php echo base_url() ?>customers/estimate_fee_pre_payment?";
        url += "list_envelope_id=" + list_envelope_id;
        url += "&action_type=" + action_type;
        url += "&type=" + type;

        // Open new dialog
        $('#make_prepayment_dialog').openDialog({
            autoOpen: false,
            height: 480,
            width: 600,
            modal: true,
            closeOnEscape: false,
            open: function (event, ui) {
                $(this).load(url, function () {
                });
            }
        });

        $('#make_prepayment_dialog').dialog('option', 'position', 'center');
        $('#make_prepayment_dialog').dialog('open');
    }

    $('div#make_prepayment_dialog') .bind('dialogclose', function (event) {
        $('#hiddenSubmitEnvelopeForm').submit();
    });

    /**
     * Change mark invoice status for envelopes
     */
    $('a.mark-invoice-envelope').live('click', function () {
        $('.scan-popup').hide();
        //Get envelope id of this popup
        var id = $(this).data('id');
        // Get all selected envelope in case customer perform multiple select
        var selectedValue = $.getAllSelectedCheckboxValue('mailbox_selected', 'data-id');
        //Create array to contain all envelope id need to process
        var listId = [];
        for (var i = 0; i < selectedValue.length; i++) {
            if ($('#mark-invoice-envelope-' + selectedValue[i]).hasClass('mark-invoice-envelope')) {
                listId.push(parseInt(selectedValue[i]));
            }
        }
        //Push current select envelope id to array
        if ($.inArray(id, listId) <= -1) {
            listId.push(id);
        }
        var submitUrl = '<?php echo base_url() ?>mailbox/mark_invoice_envelope';
        $.ajaxExec({
            url: submitUrl,
            data: {
                listId: JSON.stringify(listId),
                status: status
            },
            success: function (response) {
                if (response.status) {
                    // Reload data grid
                    location.reload();
                } else {
                    $.displayError(response.message);
                }
            }
        });
    });

    /**
     * Change mark invoice status for envelopes
     */
    $('a.send-invoice-envelope').live('click', function () {
        $('.scan-popup').hide();
        //Get envelope id of this popup
        var id = $(this).data('id');
        var email = $(this).data('email');
        // Get all selected envelope in case customer perform multiple select
        var selectedValue = $.getAllSelectedCheckboxValue('mailbox_selected', 'data-id');
        //Create array to contain all envelope id need to process
        var listId = [];
        for (var i = 0; i < selectedValue.length; i++) {
            if ($('#send-invoice-envelope-' + selectedValue[i]).hasClass('send-invoice-envelope')) {
                listId.push(parseInt(selectedValue[i]));
            }
        }
        //Push current select envelope id to array
        if ($.inArray(id, listId) <= -1) {
            listId.push(id);
        }
        var submitUrl = '<?php echo base_url() ?>mailbox/send_accounting_email';
        $.ajaxExec({
            url: submitUrl,
            data: {
                listId: JSON.stringify(listId),
                email: email
            },
            success: function (response) {
                if (response.status) {
                    $.displaySuccess(response.message);
                    // Reload data grid
                    location.reload();
                } else {
                    $.displayError(response.message);
                }
            }
        });
    });



/// =================================================================================================
///
///                                     REGISTRATION SETUP PROCESS REGION
///
/// =================================================================================================
    <?php include ("system/virtualpost/modules/mailbox/js/setup_process_js.php"); ?>

// ======================================= END REGISTRATION PROCESSS==============================================

</script>