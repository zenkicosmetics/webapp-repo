<?php
    //$customer = APContext::getCustomerLoggedIn();
?>
<style>
.error {
    background:#c88 !important;
    font-weight: bold;
}
.invoice_address_table th, .invoice_address_table td{
    vertical-align: middle;
    padding: 0.3em 0.5em;
}
</style>
<div class="ym-grid">
    <div id="cloud-body-wrapper" style="width: 1070px">
        <h2><?php language_e('account_view_index_AccountDetails'); ?></h2>
        <div class="ym-clearfix" style="height:1px;"></div>
        <?php
            if (!empty($info) && $info->plan_delete_date != null) {
                $delete_message = lang('delete_success02');
                $delete_date = APUtils::displayDate($info->plan_delete_date);
                // $delete_date = $info->plan_delete_date;
                $delete_message =sprintf($delete_message, $delete_date);
        ?>
        <div style="color: red;">
            <h3 style="color: red;font-size: 16px; font-weight: bold; margin-bottom: 10px; line-height: normal;"><?php echo $delete_message?></h3>
        </div>
        <?php } ?>
    </div>
</div>
<div id="account-body-wrapper">
    <div class="ym-grid">
        <div class="ym-g50 ym-gl">
            <div id="left-account" style="min-height: 300px;">
                <div style="margin:18px 0px;">
                    <div style="">
                        <label style="float: left;margin-top: 5px; margin-right: 12px; width: 150px">
                            <?php language_e('account_view_index_CurrentAccountType'); ?>:
                        </label>
                        <div class="ym-gl" style="width: 300px; margin-left: 5px;line-height: 30px;">
                            <div style="float: left">
                                <?php if ($customer->account_type == APConstants::NORMAL_CUSTOMER) { ?>
                                Standard
                                <?php } else {
                                language_e('account_view_index_Enterprise');
                                }?>
                            </div>
                            <div style="float: right">
                                <?php if ($customer->account_type == APConstants::NORMAL_CUSTOMER) { ?>
                                    <a id="btnUpgradeEnterpriseCustomer" class="main_link_color"><?php language_e('account_view_index_ChangeAccountType'); ?></a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="ym-clearfix"></div>
                    <div style="padding-top: 7px;">
                        <label style="float: left;margin-top: 5px; margin-right: 7px; width: 150px">
                            <?php language_e('account_view_index_UsernameMail'); ?>:
                        </label>
                        <input type="text" value="<?php echo $info->email; ?>" readonly="readonly" class="input-txt readonly" style="width: 300px;margin-left: 10px;" />
                        <div class="ym-gl left-0" style="width:33%;" >&nbsp;</div>
                        <div class="ym-gl" style="width:30%;margin-top: 10px;">
                            <a id="changeMyEmailAddressLink" class="main_link_color"><?php language_e('account_view_index_ChangeEmailAddress'); ?></a>
                            <br/>
                            <a id="changeMyPasswordLink"  class="main_link_color"><?php language_e('account_view_index_ChangePassword'); ?></a>
                            <?php if ($customer_product_setting['email_confirm_flag'] == '0') { ?>
                                <br/>
                                <a id="resendEmailConfirm"  class="main_link_color"><?php language_e('account_view_index_ResendEmailConfirm'); ?></a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="ym-clearfix"></div>
                    <div style="padding-top: 7px;">
                        <label style="float: left; margin-top: 5px; margin-right: 7px; width: 150px"><?php language_e('account_view_index_ShowPriceInCurrency'); ?>:</label>
                        <?php
                        echo my_form_dropdown(array(
                            "data" => $currencies,
                            "value_key" => 'currency_id',
                            "label_key" => 'currency_short',
                            "value" => $selected_currency_id,
                            "name" => 'currency_id',
                            "id" => 'currency_id',
                            "clazz" => 'input-width',
                            "style" => 'float:left; width: 110px;',
                            "has_empty" => false
                        ));
                        ?>
                        <p style="float: left; word-wrap: break-word; width: 175px; margin-left: 25px;margin-top: 0px;"><?php language_e('account_view_index_NoteEURCharglesCurrency'); ?></p>
                    </div>
                    <div class="ym-clearfix"></div>
                    <div style="padding-top: 7px;">
                        <label style="float: left; margin-top: 5px; margin-right: 7px; width: 150px"><?php language_e('account_view_index_Language'); ?>:</label>
                        <?php
                            echo my_form_dropdown(array(
                                         "data" => $languages,
                                         "value_key" => 'code',
                                         "label_key" => 'code',
                                         "value" => $language,
                                         "name" => 'language',
                                         "id" => 'language',
                                         "clazz" => 'input-width',
                                         "style" => 'float:left; width: 110px;',
                                         "has_empty" => false ,
                            ));
                        ?>
                    </div>
                    <div class="ym-clearfix"></div>
                    <div style="padding-top: 7px;">
                        <label style="float: left;margin-top: 5px; margin-right: 7px; width: 150px"><?php language_e('account_view_index_DecimalSeparator') ?>:</label>
                        <select id="decimal_separator" name="decimal_separator" class="input-width" style="float:left; width: 110px; margin-left: 10px; margin-bottom: 15px;">
                            <option value="," <?php echo ($decimal_separator == APConstants::DECIMAL_SEPARATOR_COMMA) ? 'selected' : ''; ?>>Comma (,)</option>
                            <option value="." <?php echo ($decimal_separator == APConstants::DECIMAL_SEPARATOR_DOT) ? 'selected' : ''; ?>>Dot (.)</option>
                        </select>
                    </div>
                    <div class="ym-clearfix"></div>
                    <?php if(APContext::isPrimaryCustomerUser() || APContext::isStandardCustomer()){ ?>
                    <div class="ym-gl" style="width:100%;vertical-align: middle;margin-top: 10px" >
                        <input id="auto_send_invoice_flag" type="checkbox" class="customCheckbox" value="1" <?php if ($info->auto_send_invoice_flag == '1') { ?> checked="checked"<?php } ?> >
                        <span><?php language_e('account_view_index_AutoSendInvoice'); ?></span>
                    </div>
                    <div class="ym-clearfix"></div>
                    <?php }?>
                </div>

                <br />
                <?php if(APContext::isStandardCustomer() || APContext::isPrimaryCustomerUser() || APContext::isAdminCustomerUser() ){ ?>
                <div class="ym-grid">
                    <h3 style="padding-left: 0px;"><?php language_e('account_view_index_InvoicingAddress'); ?></h3>
                    <?php if ($address && $address->invoice_address_verification_flag == 0) { ?>
                        <div style="color: red; float: left; margin-top: 22px; margin-left: 5px;">
                            <?php language_e('account_view_index_NeedsVerification'); ?> – <a href="<?php echo base_url() ?>cases/services?case=verification" style="text-decoration: underline; color: red"><?php language_e('account_view_index_verifynow');?> </a>
                        </div>
                    <?php } ?>
                    <?php
                    $customer_id = APContext::getCustomerCodeLoggedIn();
                    $list_case_invoice_number = APUtils::get_list_case_invoice_address($customer_id);
                    $customer = APContext::getCustomerByID($customer_id);
                    ?>
                    <?php if ($address && $address->invoice_address_verification_flag == 1 && count($list_case_invoice_number) > 0 && $customer->required_verification_flag == APConstants::ON_FLAG) {
                        ?>
                        <img title="This address has been verified" class="tipsy_tooltip" style="width: 24px; float: left; margin-top: 13px; margin-left: 5px;" src="<?php echo APContext::getImagePath() ?>/checkmark.png" />
                    <?php } ?>
                </div>
                <!-- invoice address--->
                <form id="saveAddressForm" action="<?php echo base_url() . 'account/save_invoice_address'; ?>" method="post">
                    <div class="ym-grid" >
                        <table style="border: none" border="0" class="invoice_address_table">
                            <tr>
                                <td width="100px"><label>Name:</label></td>
                                <td>
                                    <input class="input-txt" type="text" name="invoicing_address_name" id="invoicing_address_name" value="<?php
                                    if ($address) {
                                        echo $address->invoicing_address_name;
                                    }
                                    ?>" />
                                </td>
                            </tr>

                            <tr>
                                <td width="100px"><label>Company:</label></td>
                                <td>
                                    <input class="input-txt" type="text" name="invoicing_company" id="invoicing_company" value="<?php
                                    if ($address) {
                                        echo $address->invoicing_company;
                                    }
                                    ?>" />
                                </td>
                            </tr>

                            <tr>
                                <td width="100px"><label>Street: <span class="required">*</span></label></td>
                                <td>
                                    <input class="input-txt" type="text" name="invoicing_street" id="invoicing_street" value="<?php
                                    if ($address) {
                                        echo $address->invoicing_street;
                                    }
                                    ?>" />
                                </td>
                            </tr>

                            <tr>
                                <td width="100px"><label>Post Code: <span class="required">*</span></label></td>
                                <td>
                                    <input class="input-txt" type="text" name="invoicing_postcode" id="invoicing_postcode" value="<?php
                                    if ($address) {
                                        echo $address->invoicing_postcode;
                                    }
                                    ?>" />
                                </td>
                            </tr>

                            <tr>
                                <td width="100px"><label>City: <span class="required">*</span></label></td>
                                <td>
                                    <input class="input-txt" type="text" name="invoicing_city" id="invoicing_city" value="<?php
                                    if ($address) {
                                        echo $address->invoicing_city;
                                    }
                                    ?>" />
                                </td>
                            </tr>

                            <tr>
                                <td width="100px"><label>Region: <span class="required">*</span></label></td>
                                <td>
                                    <input class="input-txt" type="text" name="invoicing_region" id="invoicing_region" value="<?php
                                           if ($address) {
                                               echo $address->invoicing_region;
                                           }
                                    ?>" />
                                </td>
                            </tr>

                            <tr>
                                <td width="100px"><label>Country: <span class="required">*</span></label></td>
                                <td>
                                    <select id="invoicing_country" name="invoicing_country" class="input-width" style="width: 99%;margin-left: 0px;">
                                           <?php foreach ($countries as $country) { ?>
                                            <option value="<?php echo $country->id ?>" <?php if (!empty($address) && $address->invoicing_country == $country->id) { ?> selected="selected" <?php } ?>><?php echo $country->country_name ?></option>
                                            <?php } ?>
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td width="100px"><label>Phone Number: </label></td>
                                <td>
                                    <input class="input-txt" name="invoicing_phone_number" id="invoicing_phone_number" type="text" value="<?php
                                            if ($address) {
                                                echo $address->invoicing_phone_number;
                                            }
                                            ?>" />
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2" align="right"><input   style="float: right; width: 170px; margin-right: 5px;" type="button" id="saveAddressButton" class="input-btn btn-yellow" value="Save" /></td>
                            </tr>

                        </table>
                    </div>
                </form>

                <!-- VAT -->
                <div class="ym-grid" >
                    <div><h4 class="COLOR_063"><?php language_e('account_view_index_VATVerification'); ?></h4></div>
                    <br />

                    <!--- vat check. -->
                    <div class="ym-grid">
                        <input type="checkbox"  style="position: relative;"
                               class="customCheckbox" id="checkedVatNumber" <?php if (!empty($customer->vat_number)) { ?> checked="checked" <?php } ?> />&nbsp;
                        <span><?php language_e('account_view_index_IHaveEuropeanVATNumber'); ?>:</span>
                    </div>
                    <div class="ym-clearfix"></div>
                    <br />
                    <div class="ym-grid">
                        <input class="input-txt" type="text" style="width:310px;"
                            <?php if (!empty($customer->vat_number)) { ?> readonly="readonly" <?php } ?> id="vatnumber"
                               value="<?php if (!empty($customer->vat_number)) { echo $customer->vat_number; } ?>" />
                        <input class="input-btn btn-yellow" type="button" id="checkVATButton" value="Check VAT" style="margin-left: 10px; width: 170px" />
                        <?php if (!empty($customer->vat_number)) { ?>
                            <img alt="VAT Number valid" style="width: 32px; float: right;" src="<?php echo APContext::getImagePath() ?>/checkmark.png" />
                        <?php } ?>
                    </div>
                </div>
                
                <?php }?>
            </div>
        </div>
        <div class="ym-g50 ym-gl">
            <div id="right-account">
                <?php if(APContext::isStandardCustomer() || APContext::isPrimaryCustomerUser() || APContext::isAdminCustomerUser() ){ ?>
                <div><h4 class="COLOR_063"><?php language_e('account_view_index_AccountMessages'); ?>: </h4></div>
                <br />
                <div class="messages" style="height: 150px;">
                    <table style="border:0px; padding: 0px;margin: 0px">
                        <?php if ($messages): ?>
                            <?php foreach ($messages as $m): ?>
                                <?php if ($m->action_type == 'delete_postbox') { ?>
                                    <tr>
                                        <td style="width: 100px"><?php echo date("d.m.Y H:i", $m->updated_date) ?></td>
                                        <td>Your postbox <span style="font-weight: bold"><?php echo $m->postbox_name ?></span> will be deleted on
                                            <span style="font-weight: bold;"><?php echo date("d.m.Y", strtotime($m->plan_date)) ?></span></td>
                                    </tr>
                                <?php } else if ($m->action_type == 'change_postbox_type') { ?>
                                    <tr>
                                        <td style="width: 100px"><?php echo date("d.m.Y H:i", $m->updated_date) ?></td>
                                        <td>Your postbox <span style="font-weight: bold"><?php echo $m->postbox_name ?></span> will be changed to type
                                            <span style="font-weight: bold;"><?php echo lang('account_type_' . $m->new_postbox_type); ?></span> on <span style="font-weight: bold;"><?php echo date("d.m.Y", strtotime($m->plan_date)) ?></span></td>
                                    </tr>
                                <?php } else if (isset ($m->created_date)) { ?>
                                    <tr>
                                        <td style="width: 100px"><?php echo date("d.m.Y H:i", $m->created_date) ?></td>
                                        <td><?php echo $m->message;?></td>
                                    </tr>
                                <?php } ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </table>
                </div>
                <?php }?>
                
                <div class="ym-clearfix"></div>

                <?php if(APContext::isPrimaryCustomerUser() || APContext::isAdminCustomerUser()){ ?>
                <!-- Support setting -->
                <?php
                    include ("system/virtualpost/modules/account/views/support_setting.php");
                ?>
                <div class="ym-clearfix"></div>
                <!-- insert automatic charge setting -->
                <?php
                    if($is_valid_payment_method){
                        include ("system/virtualpost/modules/account/views/automatic_charge.php");
                    }
                ?>
                <div class="ym-clearfix"></div>
                <!-- Terms and condition setting -->
                <?php
                    include ("system/virtualpost/modules/account/views/term_condition_setting.php");
                ?>
                
                <div class="ym-clearfix"></div>
                <!-- API Access Cost setting -->
                <?php
                    include ("system/virtualpost/modules/account/views/api_access_setting.php");
                ?>
                
                <div class="ym-clearfix"></div>
                <!-- Own domain setting -->
                <?php
                    include ("system/virtualpost/modules/account/views/own_domain_setting.php");
                ?>
                
                <?php }?>
            </div>
        </div>

    </div>
</div>
<!-- Content for dialog -->
<div class="hide" style="display: none;">
    <div id="changeMyPassWindow" title="Change My Password" class="input-form dialog-form">
    </div>
    <div id="changeMyEmailWindow" title="Change My Email" class="input-form dialog-form">
    </div>
    <div id="changeMyAccountTypeWindow" title="Change Postbox Type" class="input-form dialog-form">
    </div>
    <div id="addPostboxWindow" title="Add Postbox" class="input-form dialog-form">
    </div>
    <div id="delPostboxWindow" title="Delete Postbox" class="input-form dialog-form">
    </div>
    <div id="delPostboxConfirmWindow" title="Confirm Delete Postbox" class="input-form dialog-form">
    </div>
    <div id="deletePrivateAndBusinessPostboxConfirmDialog" title="confirmation" class="input-form dialog-form">
    </div>
    <div id="make_prepayment_dialog" title="Make a Deposit/Pre-Payment" class="input-form dialog-form"></div>

    <div id="upgradeEnterpriseCustomerConfirmWindow" title="Account Upgrade" class="input-form dialog-form">
    </div>
    <div id="showHistoryTermConditionWindow" title="History of term & condition" class="input-form dialog-form"></div>
    <div id="showAddEditTermConditionWindow" title="Term & condition" class="input-form dialog-form"></div>
    <a id="display_payment_confirm" class="iframe" href="#"><?php language_e('account_view_index_GotoPaymentView'); ?></a>
    <div class="hide" style="display: none;">
        <div id="priceInfoWindow" title="Price Information" class="input-form dialog-form">
        </div>
    </div>
    <div id="upgradeEnterpriseCustomerConfirmDivContainer">
        <p><?php language_e('account_view_index_DoUWantUpgradeEnterprise'); ?></p>
        <p><a href="#" id="btnSeePricingEnterpriseAccount"><?php language_e('account_view_index_SeeConditionsOfEnterprise'); ?></a></p>
    </div>
    <div id="enableAPIAccessConfirmationWindow" title="API Access">
        <?php
            $api_access_contract_terms = $pricing_map[5]['api_access']->contract_terms;
            if (empty($api_access_contract_terms)) {
                $api_access_contract_terms = 'yearly';
            }
            $api_currency = 'EUR';
        ?>
        <div style="margin-top: 20px">
        <?php
            $api_cost = APUtils::number_format($api_access_cost);
            language_e('account_view_index_YouWantEnableApiMsg', ['api_cost' => $api_cost, 'api_currency' => $api_currency,  'contract_term' => $api_access_contract_terms]);
        ?>
        </div>
        <p>
        <button id="buttonConfirmEnableAPIAccess" style="margin-top: 20px; width: 180px; background: #F27724; border: 1px solid #F27724;color: #FFFFFF;box-shadow:none; margin-left: 200px;"><?php language_e('account_view_index_IHerebyConfirm'); ?></button>
        </p>
    </div>
    <div id="disableAPIAccessConfirmationWindow" title="API Access">
        <div style="margin-top: 20px"><?php
            language_e('account_view_index_YouWantToDisableTheAPIAccess', ['end_date' => $api_access['end_date']]);
        ?>
        </div>
        <p>
        <button id="buttonConfirmDisableAPIAccessEndContract" style="margin-top: 20px; width: 220px; background: #F27724; border: 1px solid #F27724;color: #FFFFFF;box-shadow:none; margin-left: 20px"><?php language_e('account_view_index_DisableAtEndOfContract'); ?></button>
        <button id="buttonConfirmDisableAPIAccessImmediately" style="margin-top: 20px; width: 220px; background: #F27724; border: 1px solid #F27724;color: #FFFFFF;box-shadow:none; margin-left: 200px"><?php language_e('account_view_index_DisableImmediately'); ?></button>
        </p>
    </div>
</div>
<script src="<?php echo $this->config->item('asset_url'); ?>system/virtualpost/modules/account/js/Account.js"></script>
<script>
    jQuery(document).ready(function($) {
        $('input:checkbox.customCheckbox').checkbox({cls:'jquery-safari-checkbox'});
        $('.jquery-safari-checkbox').tipsy({gravity: 'sw', html: true, live: true});
        var free_postbox_price = '<?php echo $pricing_map[1]['postbox_fee']->item_value;?>';
        var private_postbox_price = '<?php echo $pricing_map[2]['postbox_fee']->item_value;?>';
        var business_postbox_price = '<?php echo $pricing_map[3]['postbox_fee']->item_value;?>';
        $('#display_payment_confirm').fancybox({
            width: 500,
            height: 300
        });

        Account.init('<?php echo base_url(); ?>');

        $('.tipsy_tooltip').tipsy({gravity: 'sw'});
        $('input:checkbox.customCheckbox').checkbox({cls: 'jquery-safari-checkbox'});
        $('span.jquery-safari-checkbox').css('height', '15px');
        $('#saveAddressButton').click(function () {
            var submitUrl = $('#saveAddressForm').attr('action');
            $.ajaxSubmit({
                url: submitUrl,
                formId: 'saveAddressForm',
                success: function (data) {
                    if (data.status) {
                        $.displayInfor(data.message, null, function () {
                            // Reload data grid
                            document.location.href = '<?php echo base_url() ?>account';
                        });
                    } else {
                        $.displayError(data.message);
                    }
                }
            });
            return false;
        });

        // check compnay name.
        function checkCompanyName(postbox_id) {
            var companyName = $("input[name='company" + postbox_id + "']").val();
            var name = $("input[name='name" + postbox_id + "']").val();
            if (companyName.toLowerCase() == name.toLowerCase()) {
                $("input[name='company" + postbox_id + "']").addClass("error");
                return false;
            } else {
                $("input[name='company" + postbox_id + "']").removeClass("error");
                return true;
            }
        }

        // change compnay or name event
        $(".company, .name").live("keyup", function () {
            var postbox_id = $(this).attr('rel');
            checkCompanyName(postbox_id);
        });

        /**
         * Check VAT Number.
         */
        $('#checkedVatNumber').live('change', function () {
            if (this.checked) {
                // Enable read only VAT textbox
                $("#vatnumber").removeAttr("disabled");
                $("#vatnumber").removeAttr("readonly");
            } else {
                // Disable read only VAT textbox
                $("#vatnumber").attr("disabled", "disabled");
                $("#vatnumber").attr("readonly", "readonly");
            }
        });

        // chekc vat button
        $('#checkVATButton').click(function () {
            var vatnum = $('#vatnumber').attr('value');
            var checkVatNumber = $('#checkedVatNumber').prop('checked');
            if (checkVatNumber) {
                if ($.isEmpty(vatnum)) {
                    $.displayError('VAT Number is required input.');
                    return;
                }
                if (vatnum.length <= 2) {
                    $.displayError("VAT Number isn't valid.");
                    return;
                }

                saveVATNumber();
            } else {
                removeVATNumber();
            }
        });

        /**
         * Save VAT number to database
         */
        function saveVATNumber() {
            var vatnum = $('#vatnumber').attr('value');
            var invoicing_company = encodeURIComponent($('#invoicing_company').val());
            var invoicing_street = encodeURIComponent($('#invoicing_street').val());
            var invoicing_postcode = encodeURIComponent($('#invoicing_postcode').val());
            var invoicing_city = encodeURIComponent($('#invoicing_city').val());
            var submitUrl = '<?php base_url() ?>addresses/save_vat?vatnum=' + vatnum;
            submitUrl += '&CompanyName=' + invoicing_company;
            submitUrl += '&Street=' + invoicing_street;
            submitUrl += '&PostCode=' + invoicing_postcode;
            submitUrl += '&City=' + invoicing_city;

            $.ajaxExec({
                url: submitUrl,
                success: function (data) {
                    if (data.status) {
                        $.displayInfor(data.message);
                    } else {
                        $.displayError(data.message);
                    }
                }
            });
        }

        /**
         * Save VAT number to database
         */
        function removeVATNumber() {
            $.confirm({
                message: 'Are you sure want to remove VAT Number?',
                yes: function () {
                    var submitUrl = '<?php base_url() ?>addresses/remove_vat';
                    $.ajaxExec({
                        url: submitUrl,
                        success: function (data) {
                            if (data.status) {
                                $.displayInfor(data.message);
                                document.location.href = '<?php echo base_url() ?>account';
                            } else {
                                $.displayError(data.message);
                            }
                        }
                    });
                }
            });
        }

        // click see more pricing for enterprise account.
        $("#btnSeePricingEnterpriseAccount").live('click', function(){
            $("#priceInfoWindow").html("");
            var location_id = 1;

            // Open new dialog
            $('#priceInfoWindow').openDialog({
                autoOpen: false,
                height: 550,
                width: 1200,
                modal: true,
                closeOnEscape: false,
                open: function(event, ui) {
                    $(this).load("<?php echo base_url() ?>customers/load_price_list_detail?location_id="+location_id+"&type=<?php echo APConstants::ENTERPRISE_CUSTOMER ?>", function() {
                    });
                }
            });

            $('#priceInfoWindow').dialog('option', 'position', 'center');
            $('#priceInfoWindow').dialog('open');
        });
    });
</script>