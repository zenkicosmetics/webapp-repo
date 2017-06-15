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

    .ui-tabs .ui-tabs-nav li {
        width:32%;
        text-align: center;
    }
    .ui-tabs .ui-tabs-nav li a {
        display: inline-block;
        float: none;
        padding: 5px;
        text-decoration: none;
        width: 100%;
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
        <!-- Company Identification -->
        <div class="ym-gl ym-g40">
            <div class="ym-grid">
                <div class="ym-gl ym-g90 bd">
                    <div class="ym-grid">
                        <div class="ym-gl ym-g100 bd-header">
                            <strong><?php admin_language_e('cases_view_todo_review_verification_company_iden_hard_TriggeredBy', ['milestone_name' => $milestone_name]); ?>
                            <?php echo $is_invoicing_address_verification? "Invoice" :"Postbox";?>,
                            <?php echo $is_invoicing_address_verification? (is_object($customer_addresses) ? $customer_addresses->invoicing_country_name:'') : (!empty($postbox) ? $postbox[0]->location_name:'');?>
                            </strong>
                        </div>
                    </div>
                    <div class="ym-grid">
                        <div class="ym-gl ym-g100 bd-content">
                            <div class="ym-grid">
                                <?php //if ($is_invoicing_address_verification) { ?>
                                    <!--<div class="ym-gl ym-g80">Your company
                                        name and address as entered in your
                                        invoicing information:
                                    </div>-->
                                <?php //} else { ?>
                                    <div class="ym-gl ym-g80"> <?php admin_language_e('cases_view_todo_review_verification_company_iden_hard_YourCompanyNameAsEnteredInYour'); ?>
                                    </div>
                                <?php //} ?>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g80">
                                    <?php if ($is_invoicing_address_verification) { ?>
                                    <div class="description">
                                        <strong><?php echo is_object($customer_addresses) ? $customer_addresses->invoicing_company:''; ?></strong>
                                    </div>
                                    <div class="description">
                                        <strong><?php echo is_object($customer_addresses) ? $customer_addresses->invoicing_street:'' ?></strong>
                                    </div>
                                    <div class="description">
                                        <strong><?php echo is_object($customer_addresses) ? $customer_addresses->invoicing_postcode:'' ?></strong>
                                    </div>
                                    <div class="description">
                                        <strong><?php echo is_object($customer_addresses) ? $customer_addresses->invoicing_city:'' ?></strong>
                                    </div>
                                    <div class="description">
                                        <strong><?php echo is_object($customer_addresses) ? $customer_addresses->invoicing_region:'' ?></strong>
                                    </div>
                                    <div class="description">
                                        <strong><?php echo is_object($customer_addresses) ? $customer_addresses->invoicing_country_name : '' ?></strong>
                                    </div>
                                    <div class="description">
                                        <strong><?php echo (!empty($postbox) ? $postbox[0]->name : '') ?></strong>
                                    </div>
                                    <div class="description">
                                        <strong><?php echo (!empty($postbox) ? $postbox[0]->company : '') ?></strong>
                                    </div>
                                    <?php } else { ?>
                                    <div class="description">
                                        <strong><?php echo (!empty($postbox) ? $postbox[0]->name : '') ?></strong>
                                    </div>
                                    <div class="description">
                                        <strong><?php echo (!empty($postbox) ? $postbox[0]->company: '') ?></strong>
                                    </div>
                                     <?php } ?>

                                </div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g80"><?php admin_language_e('cases_view_todo_review_verification_company_iden_hard_TheFirstPageOfTheCompanyRegist'); ?>
                                </div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g80">
                                    <a id="viewFile"
                                       href="#"><strong><?php echo basename($company_hard->verification_local_file_path) ?></strong></a>
                                </div>
                            </div>
                            <?php
                            if (!empty($company_hard->shareholders_name_01) || !empty($company_hard->shareholders_name_02) || !empty($company_hard->shareholders_name_03) || !empty($company_hard->shareholders_name_04)) {
                                ?>
                                <div class="ym-grid">
                                    <div class="ym-gl ym-g100"><?php admin_language_e('cases_view_todo_review_verification_company_iden_hard_ListOfShareholdersThatOwngt249O'); ?>
                                    </div>
                                </div>
                                <div class="ym-grid">
                                    <div class="ym-gl ym-g100">
                                        <table>
                                            <colgroup>
                                                <col width="40%">
                                                <col width="10%">
                                                <col width="50%">
                                            </colgroup>
                                            <tr>
                                                <th>Name:</th>
                                                <th>%</th>
                                                <th></th>
                                            </tr>
                                            <?php
                                            if (!empty($company_hard->shareholders_name_01)) {
                                                ?>
                                                <tr>
                                                    <td>
                                                        <strong><?php echo $company_hard->shareholders_name_01 ?></strong>
                                                    </td>
                                                    <td>
                                                        <strong><?php echo $company_hard->shareholders_rate_01 ?></strong>
                                                    </td>
                                                    <td><a id="viewFile_01"
                                                           href="#"><strong><?php echo basename($company_hard->shareholders_local_file_path_01) ?></strong>
                                                        </a></td>
                                                </tr>
                                            <?php } ?>
                                            <?php
                                            if (!empty($company_hard->shareholders_name_02)) {
                                                ?>
                                                <tr>
                                                    <td>
                                                        <strong><?php echo $company_hard->shareholders_name_02 ?></strong>
                                                    </td>
                                                    <td>
                                                        <strong><?php echo $company_hard->shareholders_rate_02 ?></strong>
                                                    </td>
                                                    <td><a id="viewFile_02"
                                                           href="#"><strong><?php echo basename($company_hard->shareholders_local_file_path_02) ?></strong>
                                                        </a></td>
                                                </tr>
                                            <?php } ?>
                                            <?php
                                            if (!empty($company_hard->shareholders_name_03)) {
                                                ?>
                                                <tr>
                                                    <td>
                                                        <strong><?php echo $company_hard->shareholders_name_03 ?></strong>
                                                    </td>
                                                    <td>
                                                        <strong><?php echo $company_hard->shareholders_rate_03 ?></strong>
                                                    </td>
                                                    <td><a id="viewFile_03"
                                                           href="#"><strong><?php echo basename($company_hard->shareholders_local_file_path_03) ?></strong>
                                                        </a></td>
                                                </tr>
                                            <?php } ?>
                                            <?php
                                            if (!empty($company_hard->shareholders_name_04)) {
                                                ?>
                                                <tr>
                                                    <td>
                                                        <strong><?php echo $company_hard->shareholders_name_04 ?></strong>
                                                    </td>
                                                    <td>
                                                        <strong><?php echo $company_hard->shareholders_rate_04 ?></strong>
                                                    </td>
                                                    <td><a id="viewFile_04"
                                                           href="#"><strong><?php echo basename($company_hard->shareholders_local_file_path_04) ?></strong>
                                                        </a></td>
                                                </tr>
                                            <?php } ?>
                                        </table>
                                    </div>
                                </div>
                                <?php
                                //check box no No individual owns more than 24,9% of this company
                                } else if($company_hard->no_shareholder_flag == '1'){
                                ?>
                                <div class="ym-grid">
                                    <div class="ym-gl ym-g100">
                                        <span style="color:blue"><strong><?php admin_language_e('cases_view_todo_review_verification_company_iden_hard_NoIndividualOwnsMoreThan249OfT'); ?></strong></span>
                                    </div>
                                </div>
                            <?php } else{?>
                                <span style="color:red"><?php admin_language_e('cases_view_todo_review_verification_company_iden_hard_NoShareholderInformationProvid'); ?> <br/></span>
                            <?php } ?>

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
                        <li><a href="#verification-file-tab"><span><?php admin_language_e('cases_view_todo_review_verification_company_iden_hard_Verification'); ?></span></a></li>
                        <?php if (!empty ($company_hard->shareholders_name_01)) { ?>
                            <li><a href="#shareholders-01-file-tab"><span><?php admin_language_e('cases_view_todo_review_verification_company_iden_hard_Shareholders', ['number' => '01']); ?></span></a></li>
                        <?php }
                        if (!empty ($company_hard->shareholders_name_02)) { ?>
                            <li><a href="#shareholders-02-file-tab"><span><?php admin_language_e('cases_view_todo_review_verification_company_iden_hard_Shareholders', ['number' => '02']); ?></span></a></li>
                        <?php }
                        if (!empty ($company_hard->shareholders_name_03)) { ?>
                            <li><a href="#shareholders-03-file-tab"><span><?php admin_language_e('cases_view_todo_review_verification_company_iden_hard_Shareholders', ['number' => '03']); ?></span></a></li>
                        <?php }
                        if (!empty ($company_hard->shareholders_name_04)) { ?>
                            <li><a href="#shareholders-04-file-tab"><span><?php admin_language_e('cases_view_todo_review_verification_company_iden_hard_Shareholders', ['number' => '04']); ?></span></a></li>
                        <?php } ?>
                    </ul>
                    <iframe id="verification-file-tab"
                        style="width: 100%; position: absolute; height: 100%; border: none"
                        src="<?php echo base_url() ?>cases/todo/view?type=VR03&case_id=<?php echo $company_hard->case_id ?>&t=<?php echo time() ?>">
                    </iframe>
                    <?php if (!empty ($company_hard->shareholders_name_01)) { ?>
                    <iframe scrolling="no" id="shareholders-01-file-tab"
                            style="width: 100%; position: absolute; height: 100%; border: none"
                            src="<?php echo base_url() ?>cases/todo/view?type=VR03&op=01&case_id=<?php echo $company_hard->case_id ?>&t=<?php echo time() ?>"></iframe>

                    <?php }
                    if (!empty ($company_hard->shareholders_name_02)) { ?>
                    <iframe scrolling="no" id="shareholders-02-file-tab"
                            style="width: 100%; position: absolute; height: 100%; border: none"
                            src="<?php echo base_url() ?>cases/todo/view?type=VR03&op=02&case_id=<?php echo $company_hard->case_id ?>&t=<?php echo time() ?>"></iframe>

                    <?php }
                    if (!empty ($company_hard->shareholders_name_03)) { ?>
                    <iframe id="shareholders-03-file-tab"
                        style="width: 100%; position: absolute; height: 100%; border: none"
                        src="<?php echo base_url() ?>cases/todo/view?type=VR03&op=03&case_id=<?php echo $company_hard->case_id ?>&t=<?php echo time() ?>"></iframe>

                    <?php }
                    if (!empty ($company_hard->shareholders_name_04)) { ?>
                    <iframe id="shareholders-04-file-tab"
                        style="width: 100%; position: absolute; height: 100%; border: none"
                        src="<?php echo base_url() ?>cases/todo/view?type=VR03&op=04&case_id=<?php echo $company_hard->case_id ?>&t=<?php echo time() ?>"></iframe>

                    <?php } ?>
                </div>
            </div>
        </div>
        <!-- Admin verifi -->
        <div class="ym-gl ym-g20" style="margin: 0 10px;">
            <form id="personalVerificationForm" action="#" method="post">
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
                                <?php echo $company_hard->status == "2" ? "selected" : ""; ?>
                                value="2"><?php admin_language_e('cases_view_todo_review_verification_company_iden_hard_Complete'); ?>
                            </option>
                            <option
                                <?php echo $company_hard->status == "3" ? "selected" : ""; ?>
                                value="3"><?php admin_language_e('cases_view_todo_review_verification_company_iden_hard_Incomplete'); ?>
                            </option>
                        </select>
                    </div>
                </div>
                 <?php if ($company_hard->comment_for_registration_content != '0' ){?>
                 <div class="ym-grid">
                    <div class="ym-gl ym-g100">
                        <textarea rows="3" cols="65" disabled="disabled"
                                  name="comment_for_registration_content" class="input-txt"
                                  ><?php echo $company_hard->comment_for_registration_content; ?></textarea>
                    </div>
                </div>
                <?php }?>
                <div class="ym-grid">
                    <div class="ym-gl ym-g100">
                        <?php admin_language_e('cases_view_todo_review_verification_company_iden_hard_Comment'); ?> <span class="required">*</span>
                    </div>
                </div>
                <div class="ym-grid">
                    <div class="ym-gl ym-g100">
                        <label><?php admin_language_e('cases_view_todo_review_verification_company_iden_hard_LastConfirmed'); ?></label>
                        <span><?php echo APUtils::convert_timestamp_to_date($company_hard->comment_date); ?></span>
                    </div>
                </div>
                <div class="ym-grid">
                    <div class="ym-gl ym-g100">
                        <textarea rows="3" cols="65"
                            <?php echo $view ? 'disabled' : ''; ?>
                                  name="comment_content" class="input-txt"
                                  style="background: #FFF;"><?php echo $company_hard->comment_content ?></textarea>
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
                       value="<?php echo $company_hard->case_id; ?>"/>
            </form>

            <!-- Verification history -->
           <?php include ("system/virtualpost/modules/cases/views/todo/verification_history.php"); ?>
            <!-- End Verification history -->

            <div class="info-content">
                <!-- invoice address -->
                <div class="ym-grid">
                    <div class="ym-gl ym-g100">
                        <h3 style="font-size: 16px"><?php admin_language_e('cases_view_todo_review_verification_company_iden_hard_InvoicingAddress'); ?></h3>
                    </div>
                </div>
                <div class="ym-grid">
                    <table border="0" style="border: 0px; margin:0px">
                        <tr>
                            <th style="padding: 0px; width: 100px;"><label><?php admin_language_e('cases_view_todo_review_verification_company_iden_hard_Name'); ?> </label></th>
                            <td style="padding: 0px"><?php echo $customer_addresses ? $customer_addresses->invoicing_address_name : ""; ?></td>
                        </tr>
                        <tr>
                            <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_verification_company_iden_hard_Company'); ?> </label></th>
                            <td style="padding: 0px"><?php echo $customer_addresses ? $customer_addresses->invoicing_company : ""; ?></td>
                        </tr>
                        <tr>
                            <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_verification_company_iden_hard_Street'); ?> </label></th>
                            <td style="padding: 0px"><?php echo $customer_addresses ? $customer_addresses->invoicing_street : ""; ?></td>
                        </tr>
                        <tr>
                            <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_verification_company_iden_hard_PostCode'); ?> </label></th>
                            <td style="padding: 0px"><?php echo $customer_addresses ? $customer_addresses->invoicing_postcode : ""; ?></td>
                        </tr>
                        <tr>
                            <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_verification_company_iden_hard_City'); ?> </label></th>
                            <td style="padding: 0px"><?php echo $customer_addresses ? $customer_addresses->invoicing_city : ""; ?></td>
                        </tr>
                        <tr>
                            <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_verification_company_iden_hard_Region'); ?> </label></th>
                            <td style="padding: 0px"><?php echo $customer_addresses ? $customer_addresses->invoicing_region : ""; ?></td>
                        </tr>
                        <tr>
                            <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_verification_company_iden_hard_Country'); ?> </label></th>
                            <td style="padding: 0px"><?php echo $customer_addresses ? $customer_addresses->invoicing_country_name : ""; ?></td>
                        </tr>
                        <tr>
                            <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_verification_company_iden_hard_PhoneNo'); ?> </label></th>
                            <td style="padding: 0px"><?php echo $customer_addresses ? $customer_addresses->invoicing_phone_number : ""; ?></td>
                        </tr>

                    </table>
                </div>

                <!-- postbox address -->
                <div class="ym-grid">
                    <div class="ym-gl ym-g100">
                        <h3 style="font-size: 16px"><?php admin_language_e('cases_view_todo_review_verification_company_iden_hard_Postbox'); ?></h3>
                    </div>
                </div>
                <?php foreach ($postboxes as $select_postbox): ?>
                    <div class="ym-grid">
                        <table border="0" style="border: 0px; margin:0px">
                            <tr>
                                <th style="padding: 0px; width: 100px;"><label><?php admin_language_e('cases_view_todo_review_verification_company_iden_hard_PostboxID'); ?></label></th>
                                <td style="padding: 0px"><?php echo $select_postbox->postbox_code; ?></td>
                            </tr>
                            <tr>
                                <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_verification_company_iden_hard_Type'); ?> </label></th>
                                <td style="padding: 0px"><?php echo $select_postbox->postbox_type; ?></td>
                            </tr>
                            <tr>
                                <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_verification_company_iden_hard_Name'); ?> </label></th>
                                <td style="padding: 0px"><?php echo $select_postbox->name; ?></td>
                            </tr>
                            <tr>
                                <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_verification_company_iden_hard_Company'); ?> </label></th>
                                <td style="padding: 0px"><?php echo $select_postbox->company; ?></td>
                            </tr>
                            <tr>
                                <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_verification_company_iden_hard_Location'); ?> </label></th>
                                <td style="padding: 0px"><?php echo $select_postbox->location_name; ?></td>
                            </tr>
                        </table>
                    </div>
                <?php endforeach; ?>
            </div><!-- end of info -->

        </div>

        <div class="hide" style="display: none;">
            <a id="view_verification_file" class="iframe"
               href="<?php echo base_url() ?>cases/todo/view?type=VR03&case_id=<?php echo $company_hard->case_id ?>&t=<?php echo time() ?>">
                Preview file</a> <a id="view_verification_file_01"
                                    class="iframe"
                                    href="<?php echo base_url() ?>cases/todo/view?type=VR03&op=01&case_id=<?php echo $company_hard->case_id ?>&t=<?php echo time() ?>">
                Preview file</a> <a id="view_verification_file_02"
                                    class="iframe"
                                    href="<?php echo base_url() ?>cases/todo/view?type=VR03&op=02&case_id=<?php echo $company_hard->case_id ?>&t=<?php echo time() ?>">
                Preview file</a> <a id="view_verification_file_03"
                                    class="iframe"
                                    href="<?php echo base_url() ?>cases/todo/view?type=VR03&op=03&case_id=<?php echo $company_hard->case_id ?>&t=<?php echo time() ?>">
                Preview file</a> <a id="view_verification_file_04"
                                    class="iframe"
                                    href="<?php echo base_url() ?>cases/todo/view?type=VR03&op=04&case_id=<?php echo $company_hard->case_id ?>&t=<?php echo time() ?>">
                Preview file</a>
        </div>
    </div>
    <script type="text/javascript">
        $(function () {
            $(".bd-content").slimScroll({height: ($(window).height() - 320) + 'px'});
            $(".info-content").slimScroll({height: ($(window).height() - 480) + 'px'});
            $("#tabs").attr("style", 'height:' + ($(window).height() - $("#tabs").offset().top - 75) + 'px');
            $("#tabs").tabs();

            $("#submitButton").click(function () {
                var submitUrl = '<?php echo base_url()?>cases/todo/review_verification_company_identification_hard';
                $.ajaxSubmit({
                    url: submitUrl,
                    formId: 'personalVerificationForm',
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

            $("a[id^='view_verification_file']").fancybox({
                width: 1000,
                height: 800
            });
            $("#viewFile_01").click(function () {
                $('#view_verification_file_01').click();
                return false;
            });
            $("#viewFile_02").click(function () {
                $('#view_verification_file_02').click();
                return false;
            });
            $("#viewFile_03").click(function () {
                $('#view_verification_file_03').click();
                return false;
            });
            $("#viewFile_04").click(function () {
                $('#view_verification_file_04').click();
                return false;
            });

            $("#reopenButton").click(function () {
                $("textarea[name='comment_content']").removeAttr("disabled");
                $("select[name='status']").removeAttr("disabled");
                $("#submitButton").attr("style", "display:show");
                $(this).attr("style", "display:none");
            });
        });
    </script>