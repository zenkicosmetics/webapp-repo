<div id="invoice-body-wrapper" style="min-width: 1080px;">
    <h2>Address Management</h2>
    <div class="ym-clearfix" style="height:1px;"></div>
</div>
<div id="account-body-wrapper">
    <div class="ym-grid address-wrapper" style="margin-top: 0px; padding-top: 0px;">
        <div class="ym-grid wrapper-box2 no-border" style="margin-top:10px;min-width: 1080px">
            <form id="savePostboxAddressForm" action="<?php echo base_url() . 'addresses/save_postbox_address'; ?>" method="post">
                <div class="ym-grid ym-gl">
                    <div class="ym-gl ym-g60">
                        <span class="header-text COLOR_063" style="margin-bottom:10px;" >Postbox addresses</span>
                    </div>
                    <div class="ym-gr ym-g40" style="text-align: right; margin-bottom:10px;">
                        <input type="button" id="addNewPostboxAddressButton" class="input-btn" value="Add New Postbox" />
                    </div>
                    <div class="ym-clearfix"></div>
                    <table class="border">
                        <thead class="mn">
                            <tr>
                                <th class="center-align" style="display: none;">ID</th>
                                <th class="center-align">Postbox ID</th>
                                <?php if (APContext::isEnterpriseCustomer()) {?>
                                <th class="center-align">Associated user</th>
                                <?php } else {?>
                                <th class="center-align">Type</th>
                                <?php }?>
                                <th class="center-align">Name</th>
                                <th class="center-align">Company</th>
                                <th class="center-align">Location</th>
                                <th class="center-align" style="width: 130px">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody class="nm">
                            <?php
                            if (count($postbox) > 0) {
                                foreach ($postbox as $p) {
                                    $verification_flag = 1;
                                    if ($p->name_verification_flag == 0 || $p->company_verification_flag == 0) {
                                        $verification_flag = 0;
                                    }
                                    ?>
                                    <tr><td colspan="7"></td></tr>
                                    <tr>
                                        <td style="display: none;">
                                            <input class="input-txt-none" <?php if ($verification_flag == 0) { ?> style="margin-top: 18px;" <?php } ?> type="text" name="postbox_id<?php echo $p->postbox_id; ?>" value="<?php echo $p->postbox_id; ?>" />
                                        </td>
                                        <td class="center-align" style="padding-bottom:13px;">
                                            <input class="input-txt-none" <?php if ($verification_flag == 0) { ?> style="" <?php } ?> type="text" disabled="disabled" readonly="readonly" maxlength="35" 
                                                   name="postbox_name<?php echo $p->postbox_id; ?>" value="<?php echo end(explode('_', $p->postbox_code)); ?>" />
                                        </td>
                                        <td class="center-align" style="padding-bottom:13px;">
                                            <?php if (APContext::isEnterpriseCustomer()) { ?>
                                            <?php if (!empty($map_postbox_username) && array_key_exists($p->postbox_id, $map_postbox_username)) { ?>
                                            <input type="text" readonly="readonly" class="input-txt-none readonly" disabled="disabled" value="<?php echo $map_postbox_username[$p->postbox_id]; ?>" />
                                            <?php  } ?>
                                            <input type="hidden" name="type<?php echo $p->postbox_id ?>" value="<?php echo APConstants::ENTERPRISE_TYPE; ?>" />
                                            <?php } else { ?>
                                            <div class="slb-custom" <?php if ($verification_flag == 0) { ?> style="" <?php } ?>>    
                                                <?php
                                                    echo code_master_form_dropdown(
                                                            array(
                                                                "code" => APConstants::ACCOUNT_TYPE,
                                                                "value" => $p->type,
                                                                "name" => 'type' . $p->postbox_id,
                                                                "id" => 'type' . $p->postbox_id,
                                                                "clazz" => '',
                                                                "style" => 'height: 25px',
                                                                "has_empty" => false
                                                    ));
                                                }
                                                ?>
                                            </div>
                                        </td>
                                        <td style="width: 176px;" class="center-align">
                                            <?php if (($customer->required_verification_flag == 1) && ($p->name_verification_flag != 1) && ($p->name != '')) { ?>
                                                <div class="link_verify_name">Needs verification – <a class="main_link_color" href="<?php echo base_url() ?>cases/services?case=verification">verify now </a></div>
                                            <?php } ?>
                                            <?php
                                            $checkMarginName = false;
                                            if (
                                                    (!(($customer->required_verification_flag == 1) && ($p->name_verification_flag != 1) && ($p->name != '') )) && ( ($customer->required_verification_flag == 1) && ($p->company_verification_flag != 1) && ($p->company != '') ) || ($customer->required_verification_flag == 0)
                                            ) {
                                                $checkMarginName = true;
                                            }

                                            $checkMarginCompany = false;
                                            if (
                                                    ((($customer->required_verification_flag == 1) && ($p->name_verification_flag != 1) && ($p->name != '') )) && (!( ($customer->required_verification_flag == 1) && ($p->company_verification_flag != 1) && ($p->company != '') ))
                                            ) {
                                                $checkMarginCompany = true;
                                            }
                                            ?>
                                            <?php
                                            if (($customer->required_verification_flag == 1) && ($p->name_verification_flag == 1) && ($p->name != '')) {
                                                ?>
                                                <div class="wrapper_name">
                                                    <span class=""><img title="This postbox has been verified" class="tipsy_tooltip" src="<?php echo APContext::getImagePath() ?>/checkmark.png" /></span>
                                                <?php } ?>
                                                <input class="input-txt-none name" <?php if ($checkMarginName) { ?> style="" <?php } ?> type="text" name="name<?php echo $p->postbox_id; ?>" rel="<?php echo $p->postbox_id; ?>" value="<?php echo $p->name; ?>" data-name="<?php echo $p->name; ?>"/>
                                            </div> <!-- end div.warraper-->
                                        </td>
                                        <td style="width: 180px; <?php if ($customer->required_verification_flag == 0) echo "padding-bottom: 13px;" ?>" class="left-align">
                                            <?php
                                            if (($customer->required_verification_flag == 1) && ($p->company_verification_flag != 1) && ($p->company != '')) {
                                                ?>
                                                <div class="link_verify_company">Needs verification – <a class="main_link_color"  href="<?php echo base_url() ?>cases/services?case=verification"> verify now </a></div>
                                            <?php } ?>
                                            <div class="wrapper_company">
                                                <?php
                                                if (($customer->required_verification_flag == 1) && ($p->company_verification_flag == 1) && ($p->company != '')) {
                                                    ?>
                                                    <span><img title="This postbox has been verified" class="tipsy_tooltip" src="<?php echo APContext::getImagePath() ?>/checkmark.png" /></span>
                                                <?php } ?>
                                                <input class="input-txt-none company" type="text" name="company<?php echo $p->postbox_id; ?>" rel="<?php echo $p->postbox_id; ?>" value="<?php echo $p->company; ?>" data-company="<?php echo $p->company; ?>"style="width: 80%;"/>
                                            </div>
                                        </td>
                                        <td class="center-align">
                                            <div>
                                                <?php
                                                $location_name = "";
                                                // Gets location
                                                foreach ($locate as $l) {
                                                    if ($l->id == $p->location_available_id) {
                                                        $location_name = $l->location_name;
                                                        break;
                                                    }
                                                }
                                                ?>
                                                <input type="text" readonly="readonly" class="input-txt-none readonly" disabled="disabled" value="<?php echo $location_name; ?>" />

                                            </div>
                                        </td>
                                        <td>
                                            <input type="button" title="Show Mailing Address" class="input-btn show_mailing_address" value="Show" data-id="<?php echo $p->postbox_id ?>" data-location_available_id="<?php echo $p->location_available_id ?>" />
                                            <?php if (APContext::isEnterpriseCustomer()) { ?>
                                                <input type="button" title="Change Postbox Location" class="input-btn change_postbox_location" value="Edit" data-id="<?php echo $p->postbox_id ?>" data-location_available_id="<?php echo $p->location_available_id ?>" />
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>

                            <?php } ?>
                        </tbody>
                    </table>

                    <div class="ym-clearfix"></div>
                    <div class="ym-gl ym-g60" style="color: red; margin-top: 10px; padding-top: 12px">&nbsp;</div>
                    <div class="ym-gr ym-g40" style="text-align: right;margin-top: 10px;">
                        <input type="button" id="savePostboxAddressButton" class="input-btn" value="Save" />
                    </div>
                </div>
            </form>
        </div>

        <div class="ym-clearfix" style="height: 35px;"></div>

        <div class="ym-grid wrapper-box2 no-border" style="min-width: 1080px;">
            <span class="header-text COLOR_063">Locations available</span>
            <?php
            $cnt = 0;
            for ($i = 0; $i < count($locate); $i ++) {
                $cnt ++;
                $lc = $locate[$i];
                if ($cnt == 1) {
                    ?>
                    <div class="ym-grid">
                        <div class="ym-g33 ym-gl"
                             style="width: 32%; border: 2px solid #dadada; margin-top: 10px;">
                            <div style="padding: 10px 5px">
                                <strong style="font-size: 13px;"><?php
                                    if ($lc) {
                                        echo APUtils::autoHidenText($lc->location_name, 30);
                                    }
                                    ?></strong> <strong rel="<?php echo $lc->id; ?>" class="add" style="color: #4cd864; float: right; font-size: 22px; margin-top: -6px; cursor: pointer;">+</strong>
                            </div>
                            <div>
                                <?php if (empty($lc->image_path)) { ?>
                                    <img src="<?php echo APContext::getAssetPath() ?>uploads/images/location/default_location.png" style="width: 100%; height: 100px;">
                                <?php } else { ?>
                                    <?php
                                    if (substr($lc->image_path, 0, 1) == "/") {
                                        $image_path = substr($lc->image_path, 1, strlen($lc->image_path));
                                    } else {
                                        $image_path = $lc->image_path;
                                    }
                                    ?>
                                    <img src="<?php echo APContext::getAssetPath() . $image_path; ?>" style="width: 100%; height: 100px;">
                                <?php } ?>
                            </div>
                            <div style="padding: 10px 5px">
                                <?php
                                if ($lc) {
                                    //echo APUtils::autoHidenText($lc->street, 30);
                                    echo $lc->street;
                                }
                                ?>
                            </div>
                            <div style="padding: 10px 5px">
                                <?php
                                if ($lc) {
                                    echo APUtils::autoHidenText($lc->postcode, 30);
                                }
                                ?>
                            </div style="padding: 10px 5px">
                            <div style="padding: 10px 5px">
                                <?php
                                if ($lc) {
                                    echo APUtils::autoHidenText($lc->region, 30);
                                }
                                ?>
                            </div>
                            <div style="padding: 10px 5px">
                                <?php
                                if ($lc) {
                                    echo APUtils::autoHidenText($lc->country_name, 30);
                                }
                                ?>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="ym-g33 ym-gl" style="width: 32%; border: 2px solid #dadada; margin-top: 10px; margin-left: 10px;">
                            <div style="padding: 10px 5px">
                                <strong style="font-size: 13px;"><?php
                                    if ($lc) {
                                        echo APUtils::autoHidenText($lc->location_name, 30);
                                    }
                                    ?></strong><strong rel="<?php echo $lc->id; ?>" class="add" style="color: #4cd864; float: right; font-size: 22px; margin-top: -6px; cursor: pointer;">+</strong></div>
                            <div>
                                <?php if (empty($lc->image_path)) { ?>
                                    <img src="<?php echo APContext::getAssetPath() ?>uploads/images/location/default_location.png" style="width: 100%; height: 100px;">
                                <?php } else { ?>
                                    <?php
                                    if (substr($lc->image_path, 0, 1) == "/") {
                                        $image_path = substr($lc->image_path, 1, strlen($lc->image_path));
                                    } else {
                                        $image_path = $lc->image_path;
                                    }
                                    ?>
                                    <img src="<?php echo APContext::getAssetPath() . $image_path; ?>" style="width: 100%; height: 100px;">
                                <?php } ?>
                            </div>
                            <div style="padding: 10px 5px">
                                <?php
                                if ($lc) {
                                    //echo APUtils::autoHidenText($lc->street, 30);
                                    echo $lc->street;
                                }
                                ?>
                            </div>
                            <div style="padding: 10px 5px">
                                <?php
                                if ($lc) {
                                    echo APUtils::autoHidenText($lc->postcode, 30);
                                }
                                ?>
                            </div>
                            <div style="padding: 10px 5px">
                                <?php
                                if ($lc) {
                                    echo APUtils::autoHidenText($lc->region, 30);
                                }
                                ?>
                            </div>
                            <div style="padding: 10px 5px">
                                <?php
                                if ($lc) {
                                    echo APUtils::autoHidenText($lc->country_name, 30);
                                }
                                ?>
                            </div>
                        </div>
                        <?php
                    }
                    // reset $cnt
                    if ($cnt == 3) {
                        $cnt = 0;
                        ?>
                        <!-- close row -->
                    </div>
                    <?php
                }
                ?>
            <?php } ?>

        </div>
    </div>

    <div class="ym-clearfix" style="height: 35px;"></div>
</div>
<!-- Content for dialog -->
<div class="hide">
    <div id="showMailingAddressWindow" title="Show Mailing Address"
         class="input-form dialog-form"></div>
</div>

<div class="hide" style="display: none;">
    <div id="forward_address" title="Forwarding Address Book" class="input-form dialog-form"></div>
</div>
<input type="hidden" value="<?php echo $customer->activated_flag ?>" id="activatedFlagId" name="activatedFlag" />

<div class="hide">
    <div id="addPostboxWindow" title="Add Postbox" class="input-form dialog-form"></div>
    <div id="delPostboxConfirmWindow" title="Confirm Delete Postbox" class="input-form dialog-form"></div>
    <div id="delPostboxWindow" title="Delete Postbox" class="input-form dialog-form"></div>
    <div id="make_prepayment_dialog" title="Make a Deposit/Pre-Payment" class="input-form dialog-form"></div>
</div>
<script src="<?php echo APContext::getAssetPath(); ?>system/virtualpost/modules/account/js/Account.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function ($) {

        Account.init('<?php echo base_url(); ?>');
        $("#manage_multi_address").click(function () {
            openManageAddressWindow();
        });

        /** START SOURCE TO manage address */
<?php include 'system/virtualpost/modules/addresses/js/js_manage_address.php'; ?>
        /** START SOURCE TO manage address */


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
                            document.location.href = '<?php echo base_url() ?>addresses';
                        });
                    } else {
                        $.displayError(data.message);
                    }
                }
            });
            return false;
        });

        function checkCompanyName(postbox_id) {

            var companyName = $("input[name='company" + postbox_id + "']").val().trim();
            var name = $("input[name='name" + postbox_id + "']").val().trim();
            if ((companyName.toLowerCase() == name.toLowerCase())) {
                $("input[name='company" + postbox_id + "']").addClass("error");
                return false;
            } else {
                $("input[name='company" + postbox_id + "']").removeClass("error");
                return true;
            }
        }

        function isChangePostboxAddress() {

            var isChange = false;

            $(".company").each(function () {
                var postbox_id = $(this).attr('rel');
                var companyInput = $("input[name='company" + postbox_id + "']");
                var nameInput = $("input[name='name" + postbox_id + "']");

                if ((nameInput.val().length > 0 && nameInput.val() != nameInput.data('name')) || (companyInput.val().length > 0 && companyInput.val() != companyInput.data('company'))) {
                    isChange = true;
                    return false;
                }
            });

            return isChange;
        }

        $(".company, .name").live("keyup", function () {
            var postbox_id = $(this).attr('rel');
            checkCompanyName(postbox_id);
        });

        function savePostboxAddress() {
            var submitUrl = $('#savePostboxAddressForm').attr('action');
            $.ajaxSubmit({
                url: submitUrl,
                formId: 'savePostboxAddressForm',
                success: function (data) {
                    if (data.status) {
                        $.displayInfor(data.message, null, function () {
                            // Reload data grid
                            document.location.href = '<?php echo base_url() ?>addresses';
                        });
                    } else {
                        // #1012 Pre-payment process
                        if (data.prepayment === true) {
                            var new_postbox_type = data.new_postbox_type;
                            var postbox_id = data.postbox_id;
                            openEstimateCostDialog('change_postbox_type', '', postbox_id, new_postbox_type);
                            return;
                        }

                        // fixbug #399
                        if (!(data.data.error_status)) {
                            $.displayError(data.message);
                            return;
                        }
                        $.confirm({
                            message: data.message,
                            yes: function () {
                                var submitUrl = '<?php base_url() ?>addresses/remove_vat';
                                document.location.href = '<?php echo base_url() ?>account';
                            }
                        });
                    }
                }
            });
        }

        $('#savePostboxAddressButton').click(function () {
            var validate = true;
            $(".company").each(function () {
                var postbox_id = $(this).attr('rel');
                var check = checkCompanyName(postbox_id);
                if (!check) {
                    validate = false;
                    return false;
                }
            });
            if (!validate) {
<?php ci()->lang->load('addresses/address'); ?>
                $.displayError("<?php echo lang('error_company_same_name') ?>");
                $("#company").addClass("error");
                return;
            }
            //Display warning when change name or company name of postbox
            if (isChangePostboxAddress()) {
                // show confirmation popup
                $.confirm({
                    message: 'Warning: please note that a change of the name or company name in your postbox will require a re-verification of this postbox',
                    yes: function () {
                        savePostboxAddress();
                    }
                });
            } else {
                savePostboxAddress();
            }
            ;

            return false;
        });

        /**
         * Show mailling address
         */
        $('.show_mailing_address').live('click', function () {
            // Clear control of all dialog form
            $('.dialog-form').html('');

            var id = $(this).attr('data-id');
            var location_available_id = $(this).attr('data-location_available_id');

            // Open new dialog
            $('#showMailingAddressWindow').openDialog({
                autoOpen: false,
                height: 500,
                width: 450,
                modal: true,
                open: function () {
                    $(this).load("<?php echo base_url() ?>addresses/show_mailing_address?postbox_id=" + id + "&location_available_id=" + location_available_id, function () {
                    });
                },
                buttons: {
                    'Cancel': function () {
                        $(this).dialog('close');
                    }
                }
            });
            $('#showMailingAddressWindow').dialog('option', 'position', 'center');
            $('#showMailingAddressWindow').dialog('open');
        });

        /**
         * Show mailling address
         */
        $('.change_postbox_location').live('click', function () {
            // Clear control of all dialog form
            $('.dialog-form').html('');

            var postbox_id = $(this).attr('data-id');
            var windowId = '#ChangeUserPostboxLocationWindow';
            var loadUrl = "<?php echo base_url() ?>account/users/change_postbox_location?postbox_id=" + postbox_id;
            $.openDialog(windowId, {
                height: 300,
                width: 500,
                openUrl: loadUrl,
                title: "Change Postbox Location",
                show_only_close_button: false,
                buttons: [{
                        'text': 'Save',
                        'id': 'changePostboxLocationButton'
                    }],
                callback: function () {
                    //location.reload();
                }
            });
        });

        /**
         * When user click to copy button
         */
        $('#copyAddressButton').click(function () {
            $('#invoicing_address_name').val($('#shipment_address_name').val());
            $('#invoicing_company').val($('#shipment_company').val());
            $('#invoicing_street').val($('#shipment_street').val());
            $('#invoicing_postcode').val($('#shipment_postcode').val());
            $('#invoicing_city').val($('#shipment_city').val());
            $('#invoicing_region').val($('#shipment_region').val());
            $('#invoicing_country').val($('#shipment_country').val());
            $('#invoicing_phone_number').val($('#shipment_phone_number').val());
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
    });

    /**
     *  Open estimated cost dialog.
     *  Action_Type: add_more_postbox
     */
    function openEstimateCostDialog(type, location_id, postbox_id, postbox_type) {

        var url = '<?php echo base_url() ?>customers/estimate_fee_pre_payment';
        url += "?type=" + type;
        url += "&postbox_type=" + postbox_type;
        url += "&location_id=" + location_id;
        url += "&postbox_id=" + postbox_id;

        // Open new dialog
        $('#make_prepayment_dialog').openDialog({
            autoOpen: false,
            height: 475,
            width: 700,
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
</script>
