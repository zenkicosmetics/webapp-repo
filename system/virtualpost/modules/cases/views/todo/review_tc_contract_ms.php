<style>
    .VR03 #submitButton, #reopenButton {
        padding: .4em 1em;
        color: #fff;
        background: #336699;
        border: 1px solid #569bdb;
        border-radius: 4px;
        cursor: pointer;
        text-align: center;
        font-style: normal;
        font-size: 14px;
        line-height: 1.4;
    }

    #backBtn {
        padding: .4em 1em;
        color: #6c6c6c;
        background: #fff;
        border: 1px solid #569bdb;
        border-radius: 4px;
        cursor: pointer;
        text-align: center;
        font-style: normal;
        font-size: 14px;
        line-height: 1.4;
        margin-left: 4px;
        font-weight: bold;
    }

    .VR03 .input-btn span {
        text-decoration: underline;
    }

    .VR03 .ym-grid {
        margin-bottom: 12px !important;
        margin-top: 0px !important;
    }

    .VR03 a:HOVER {
        text-decoration: none;
    }

    .VR03 .bd {
        border: 1px solid #a5a5a5;
        padding: 20px !important;
        /*     max-height: 460px;  */
        /*     height: 460px; */
    }

    .VR03 .bd-header {
        border-bottom: 1px solid #a5a5a5;
        padding-bottom: 12px !important;
        font-size: 1.2em;
    }

    .VR03 .description strong {
        margin-right: 10px;
    }

    input.input-txt {
        margin-left: 0px !important;
    }

    .VR03 .description {
        margin: 10px auto auto 20px;
    }

    .VR03 textarea {
        width: 100% !important;
        /*     padding-left: 10px; */
        font-size: 13px !important;
    }

    .VR03 .input-width {
        width: 100% !important;
    }

    .ui-tabs, .ui-tabs-panel {
        padding: 0px !important;
    }

    #mailbox {
        width: 100% !important;
    }

    .ui-layout-center {
        overflow: visible;
    }

    .ui-tabs { overflow: hidden; position: relative; padding: .2em; zoom: 1; }

    .verification-history .ym-grid {
        margin: 0 !important;
        padding: 2px 0 2px 0;
    }

    .verification-history .ym-gbox {
        padding: 0;
        padding-left: 5px;
    }

</style>
<div class="header">
    <h2
        style="font-size: 1.3em; font-weight: bold; margin-bottom: 10px;"><?php printf('%1$s > %2$s >Review %3$s', $case_name, $this->router->fetch_class(), $milestone_name) ?></h2>
</div>
<div class="input-form dialog-form VR03">
    <div class="ym-grid">
        <!-- Contract Identification -->
        <div class="ym-gl ym-g40">
            <div class="ym-grid">
                <div class="ym-gl ym-g90 bd">
                    <div class="ym-grid">
                        <div class="ym-gl ym-g100 bd-header"><strong><?php echo $milestone_name; ?> - triggered by:
                            <?php echo $is_invoicing_address_verification? "Invoice" :"Postbox";?>,
                            <?php echo $is_invoicing_address_verification? (is_object($customer_addresses) ? $customer_addresses->invoicing_country_name:'') : (!empty($postbox) ? $postbox[0]->location_name:'');?></strong></div>
                    </div>
                    <div class="ym-grid">
                        <div class="ym-gl ym-g100 bd-content">
                            <div class="ym-grid">
                                 <div class="ym-gl ym-g80"><?php admin_language_e('cases_view_todo_review_tc_contract_ms_TheCurrentClevverMailTermsCond'); ?> </div>

                            </div>
                             <div class="ym-grid">
                                <div class="ym-gl ym-g100">
                                    <div class="description" style="border: 1px solid #a5a5a5;background-color: #fff;overflow: auto;height: 200px;">
                                        <?php echo $terms_and_conditions; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g80">
                                    <a id="viewFile"
                                       href="#"><strong><?php echo basename($resource->local_file_path) ?></strong></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- View file -->
        <div class="ym-gl ym-g30" style="width: 35%; margin: 0 10px">
            <div class="ym-grid">
                <div id="tabs" style="height: 100%">
                    <ul class="tab-menu">
                        <li><a href="#verification-file-tab"><?php admin_language_e('cases_view_todo_review_tc_contract_ms_SpanVerificationspan'); ?></a></li>

                    </ul>

                    <iframe id="verification-file-tab"
                        style="width: 100%; position: absolute; height: 100%; border: none"
                        src="<?php echo base_url() ?>cases/todo/view_resource?file_id=<?php echo $resource->id?>">
                    </iframe>

                </div>
            </div>
        </div>
        <!-- Admin verification -->
        <div class="ym-gl ym-g20" style="margin: 0 10px;">
            <form id="contractVerificationForm" action="#" method="post">
                <div class="ym-grid">
                    <div class="ym-gl ym-g100">
                        Status <span class="required">*</span>
                    </div>
                </div>
                <div class="ym-grid">
                    <div class="ym-gl ym-g100">
                        <select class="input-width" name="status"
                            <?php echo $view ? 'disabled' : ''; ?>>
                            <option
                                <?php echo $contract->status == "2" ? "selected" : ""; ?>
                                value="2">Completed
                            </option>
                            <option
                                <?php echo $contract->status == "3" ? "selected" : ""; ?>
                                value="3">Incomplete
                            </option>
                        </select>
                    </div>
                </div>
                 <?php if ($contract->comment_for_registration_content != '0' ){?>
                 <div class="ym-grid">
                    <div class="ym-gl ym-g100">
                        <textarea rows="3" cols="65" disabled="disabled"
                                  name="comment_for_registration_content" class="input-txt"
                                  ><?php echo $contract->comment_for_registration_content; ?></textarea>
                    </div>
                </div>
                <?php }?>
                <div class="ym-grid">
                    <div class="ym-gl ym-g100">
                        Comment <span class="required">*</span>
                    </div>
                </div>
                 <div class="ym-grid">
                    <div class="ym-gl ym-g100">
                        <label><?php admin_language_e('cases_view_todo_review_tc_contract_ms_LastConfirmed'); ?></label>
                        <span><?php echo APUtils::convert_timestamp_to_date($contract->comment_date); ?></span>
                    </div>
                </div>
                <div class="ym-grid">
                    <div class="ym-gl ym-g100">
                        <textarea rows="3" cols="65"
                            <?php echo $view ? 'disabled' : ''; ?>
                                  name="comment_content" class="input-txt"
                                  style="background: #FFF;"><?php echo $contract->comment_content ?></textarea>
                    </div>
                </div>
                <div class="ym-grid">
                    <div class="ym-gl ym-g100">
                        <input type="button" class="input-btn"
                               style="<?php echo $view ? 'display:none' : ''; ?>"
                               id="submitButton" value="Submit"><input type="button" class="input-btn"
                                                                       style="<?php echo $view ? '' : 'display:none'; ?>"
                                                                       id="reopenButton" value="ReOpen"><input
                            type="button" id="backBtn" value="Back">
                    </div>
                </div>
                <input type="hidden" id="case_id" name="case_id"
                       value="<?php echo $contract->case_id; ?>"/>
            </form>

            <!-- Verification history -->
            <?php include ("system/virtualpost/modules/cases/views/todo/verification_history.php"); ?>
            <!-- End Verification history -->

            <div class="info-content">
                <!-- invoice address -->
                <div class="ym-grid">
                    <div class="ym-gl ym-g100">
                        <h3 style="font-size: 16px"><?php admin_language_e('cases_view_todo_review_tc_contract_ms_InvoicingAddress'); ?></h3>
                    </div>
                </div>
                <div class="ym-grid">
                    <table border="0" style="border: 0px; margin:0px">
                        <tr>
                            <th style="padding: 0px; width: 100px;"><label><?php admin_language_e('cases_view_todo_review_tc_contract_ms_Name'); ?> </label></th>
                            <td style="padding: 0px"><?php echo $customer_addresses ? $customer_addresses->invoicing_address_name : ""; ?></td>
                        </tr>
                        <tr>
                            <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_tc_contract_ms_Company'); ?> </label></th>
                            <td style="padding: 0px"><?php echo $customer_addresses ? $customer_addresses->invoicing_company : ""; ?></td>
                        </tr>
                        <tr>
                            <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_tc_contract_ms_Street'); ?> </label></th>
                            <td style="padding: 0px"><?php echo $customer_addresses ? $customer_addresses->invoicing_street : ""; ?></td>
                        </tr>
                        <tr>
                            <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_tc_contract_ms_PostCode'); ?> </label></th>
                            <td style="padding: 0px"><?php echo $customer_addresses ? $customer_addresses->invoicing_postcode : ""; ?></td>
                        </tr>
                        <tr>
                            <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_tc_contract_ms_City'); ?> </label></th>
                            <td style="padding: 0px"><?php echo $customer_addresses ? $customer_addresses->invoicing_city : ""; ?></td>
                        </tr>
                        <tr>
                            <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_tc_contract_ms_Region'); ?> </label></th>
                            <td style="padding: 0px"><?php echo $customer_addresses ? $customer_addresses->invoicing_region : ""; ?></td>
                        </tr>
                        <tr>
                            <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_tc_contract_ms_Country'); ?> </label></th>
                            <td style="padding: 0px"><?php echo $customer_addresses ? $customer_addresses->invoicing_country_name : ""; ?></td>
                        </tr>
                        <tr>
                            <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_tc_contract_ms_PhoneNo'); ?> </label></th>
                            <td style="padding: 0px"><?php echo $customer_addresses ? $customer_addresses->invoicing_phone_number : ""; ?></td>
                        </tr>

                    </table>
                </div>

                <!-- postbox address -->
                <div class="ym-grid">
                    <div class="ym-gl ym-g100">
                        <h3 style="font-size: 16px">Postbox</h3>
                    </div>
                </div>
                <?php foreach ($postboxes as $select_postbox): ?>
                    <div class="ym-grid">
                        <table border="0" style="border: 0px; margin:0px">
                            <tr>
                                <th style="padding: 0px; width: 100px;"><label><?php admin_language_e('cases_view_todo_review_tc_contract_ms_PostboxID'); ?></label></th>
                                <td style="padding: 0px"><?php echo $select_postbox->postbox_code; ?></td>
                            </tr>
                            <tr>
                                <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_tc_contract_ms_Type'); ?> </label></th>
                                <td style="padding: 0px"><?php echo $select_postbox->postbox_type; ?></td>
                            </tr>
                            <tr>
                                <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_tc_contract_ms_Name'); ?> </label></th>
                                <td style="padding: 0px"><?php echo $select_postbox->name; ?></td>
                            </tr>
                            <tr>
                                <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_tc_contract_ms_Company'); ?> </label></th>
                                <td style="padding: 0px"><?php echo $select_postbox->company; ?></td>
                            </tr>
                            <tr>
                                <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_tc_contract_ms_Location'); ?> </label></th>
                                <td style="padding: 0px"><?php echo $select_postbox->location_name; ?></td>
                            </tr>
                        </table>
                    </div>
                <?php endforeach; ?>
            </div><!-- end of info -->

        </div>

        <div class="hide" style="display: none;">
            <a id="view_verification_file" class="iframe"
               href="<?php echo base_url() ?>cases/todo/view_resource?file_id=<?php echo $resource->id?>"><?php admin_language_e('cases_view_todo_review_tc_contract_ms_PreviewFile'); ?></a>
        </div>
    </div>
    <script type="text/javascript">
        $(function () {
            $(".bd-content").slimScroll({height: ($(window).height() - 320) + 'px'});
            $(".info-content").slimScroll({height: ($(window).height() - 480) + 'px'});
            $("#tabs").attr("style", 'height:' + ($(window).height() - $("#tabs").offset().top - 75) + 'px');
            $("#tabs").tabs();

            $("#submitButton").click(function () {
                var submitUrl = '<?php echo base_url()?>cases/todo/review_TC_contract_MS';
                $.ajaxSubmit({
                    url: submitUrl,
                    formId: 'contractVerificationForm',
                    success: function (data) {
                        if (data.status) {
                            $.infor({
                                message: "Submit data successfull.", ok: function () {
                                    document.location.href = '<?php echo base_url()?>cases/todo';
                                }
                            });
                        } else {
                            $.displayError(data.message);
                        }
                    }
                });
                return;
            });

            $("#backBtn").click(function () {
                window.history.back();
            });

            $("#viewFile").click(function () {
                $('#view_verification_file').click();
                return false;
            });

            $("#view_verification_file").fancybox({
                width: 1000,
                height: 800
            });


            $("#reopenButton").click(function () {
                $("textarea[name='comment_content']").removeAttr("disabled");
                $("select[name='status']").removeAttr("disabled");
                $("#submitButton").attr("style", "display:show");
                $(this).attr("style", "display:none");
            });

        });
    </script>