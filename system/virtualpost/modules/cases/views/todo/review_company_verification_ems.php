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
/*             max-height: 460px;
             height: 460px; */
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
             padding-left: 10px;
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
    <h2 style="font-size: 1.3em; font-weight: bold; margin-bottom: 10px;"><?php printf('%1$s > %2$s >Review %3$s', $case_name, $this->router->fetch_class(), $milestone_name) ?></h2>
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
                            <?php echo $is_invoicing_address_verification? (is_object($customer_addresses) ? $customer_addresses->invoicing_country_name:'') : (!empty($location_name_postbox) ? $location_name_postbox->location_name:'');?> </strong></div>
                    </div>
                    <div class="ym-grid">
                        <div class="ym-gl ym-g100 bd-content">
                           <div class="ym-grid">
                                <div class="ym-gl ym-g80"><?php admin_language_e('cases_view_todo_review_company_verification_ems_YourCompanyInformation'); ?></div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g80">
                                    <div class="ym-grid">
                                        <div class="ym-gl ym-g80">
                                            <?php if (!empty($postbox->name)) {?>
                                            <div class="description">
                                                <strong><?php echo $postbox->name?></strong><a href="<?php echo base_url()?>addresses">change</a>
                                            </div>
                                            <?php } ?>
                                            <?php if (!empty($postbox->company)) {?>
                                            <div class="description">
                                                <strong><?php echo $postbox->company?></strong><a href="<?php echo base_url()?>addresses">change</a>
                                            </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ym-grid">
                                <div><?php admin_language_e('cases_view_todo_review_company_verification_ems_PleaseDescriptionTheNatureOfYo'); ?></div>
                                <div class="ym-gl ym-g80">
                                    <?php if($company_verification_ems && $company_verification_ems->description){ ?>
                                    <div>
                                        <textarea id="description" class="input-txt" name="description" rows="4" style="width: 100%;background-color : #A9A9A9;" maxlength="500" readonly><?php echo $company_verification_ems->description ?></textarea>
                                    </div>
                                    <?php }else{ ?>
                                        <div>
                                            <textarea id="description" class="input-txt" name="description" rows="4" style="width: 100%" maxlength="500" readonly></textarea>
                                        </div>
                                    <?php } ?>

                                </div>
                            </div>
                            <div class="ym-grid">
                                <div><?php admin_language_e('cases_view_todo_review_company_verification_ems_PleaseUploadYourBusinessLicens'); ?></div>
                                <div class="ym-gl ym-g80">
                                    <a class="view-pdf" data-id="<?php echo $case_resource->id ? $case_resource->id: "" ?>"
                                       data-op="case_resource" href="#" ><strong><?php echo basename($case_resource->local_file_path) ?></strong></a>
                                </div>
                            </div>
                            <?php
                                if (!empty($mailReceivers)) {
                            ?>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g100">
                                    <?php admin_language_e('cases_view_todo_review_company_verification_ems_PleaseNameAllOfficersOfTheComp'); ?>
                                </div>
                            </div>

                            <div class="ym-grid">
                                <div class="ym-gl ym-g100">
                                    <table>
                                        <colgroup>
                                           <col width="50%">
                                           <col width="">
                                       </colgroup>
                                       <tr>
                                           <th>Name</th>
                                           <th>File</th>
                                       </tr>
                                       <?php foreach($mailReceivers as $mailReceiver) { ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo $mailReceiver->name ?></strong>
                                                </td>

                                                <td><a class="view-pdf" data-id="<?php echo $mailReceiver->id ? $mailReceiver->id: "" ?>"
                                                       data-op="mail_receiver" href="#" ><strong><?php echo basename($mailReceiver->local_file_path) ?></strong></a></td>
                                            </tr>
                                            <?php } ?>
                                       </table>
                                </div>
                            </div>
                            <?php } ?>
                            <?php
                                if (!empty($officers)) {
                            ?>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g100">
                                    <?php admin_language_e('cases_view_todo_review_company_verification_ems_PleaseNameAnyBeneficialOwner'); ?>
                                </div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g100">

                                 <table>
                                    <colgroup>
                                        <col width="35%">
                                        <col width="12%">
                                        <col width="50%">
                                    </colgroup>
                                    <tr>
                                        <th>Name</th>
                                        <th>Rate(%)</th>
                                        <th>File</th>
                                    </tr>
                                    <?php foreach($officers as $officer) { ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo $officer->name ?></strong>
                                                </td>
                                                <td>
                                                    <strong><?php echo $officer->rate ?></strong>
                                                </td>
                                                <td><a class="view-pdf" data-id="<?php echo $officer->id ? $officer->id: "" ?>"
                                                       data-op="officer" href="#" ><strong><?php echo basename($officer->officer_local_path) ?></strong></a></td>
                                            </tr>
                                         <?php } ?>
                                    </table>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- View file -->
        <div class="ym-gl ym-g30" style="width: 35%; margin: 0 10px">
            <div class="ym-grid">
                <div id="tabs">
                    <ul class="tab-menu">
                        <li><a href="#verification-file-tab"><span><?php admin_language_e('cases_view_todo_review_company_verification_ems_Verification'); ?></span></a></li>

                       <?php foreach($mailReceivers as $mailReceiver) { ?>
                         <li><a href="#verification-file-mail-<?php echo $mailReceiver->id?>"><span><?php echo admin_language('cases_view_todo_review_company_verification_ems_Verification') . substr(basename($mailReceiver->local_file_path), -11,-4) ?></span></a></li>
                       <?php } ?>

                       <?php foreach($officers as $officer) { ?>
                          <li><a href="#verification-file-officer-<?php echo $officer->id?>"><span><?php echo admin_language('cases_view_todo_review_company_verification_ems_Verification') . substr(basename($officer->officer_local_path), -11,-4) ?></span></a></li>
                        <?php } ?>
                    </ul>

                    <iframe  id="verification-file-tab"
                        style="width: 100%;height:80%; position: absolute;border: none"
                        src="<?php echo base_url() ?>cases/todo/view_resource?file_id=<?php echo $case_resource->id?>">
                    </iframe>

                    <?php foreach($mailReceivers as $mailReceiver) { ?>
                    <iframe id="verification-file-mail-<?php echo $mailReceiver->id?>"
                        style="width:100%;height:80%; position: absolute;border: none"
                        src="<?php echo base_url() ?>cases/todo/view_resource?file_id=<?php echo $mailReceiver->id?>">
                    </iframe>
                    <?php } ?>

                    <?php foreach($officers as $officer) { ?>
                    <iframe id="verification-file-officer-<?php echo $officer->id?>"
                        style=" width: 100%;height:80%; border: none; position: absolute;"
                        src="<?php echo base_url() ?>cases/todo/view_resource?file_id=<?php echo $officer->id?>&type=officer">
                    </iframe>

                     <?php } ?>

                </div>
            </div>
        </div>
        <!-- Admin verification -->
        <div class="ym-gl ym-g20" style="margin: 0 10px;">
            <form id="companyEMSVerificationForm" action="#" method="post">
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
                                <?php echo $company_verification_ems->status == "2" ? "selected" : ""; ?>
                                value="2">Completed
                            </option>
                            <option
                                <?php echo $company_verification_ems->status == "3" ? "selected" : ""; ?>
                                value="3">Incomplete
                            </option>
                        </select>
                    </div>
                </div>
                 <?php if ($company_verification_ems->comment_for_registration_content != '0' ){?>
                 <div class="ym-grid">
                    <div class="ym-gl ym-g100">
                        <textarea rows="3" cols="65" disabled="disabled"
                                  name="comment_for_registration_content" class="input-txt"
                                  ><?php echo $company_verification_ems->comment_for_registration_content; ?></textarea>
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
                        <label><?php admin_language_e('cases_view_todo_review_company_verification_ems_LastConfirmed'); ?></label>
                        <span><?php echo APUtils::convert_timestamp_to_date($company_verification_ems->comment_date); ?></span>
                    </div>
                </div>
                <div class="ym-grid">
                    <div class="ym-gl ym-g100">
                        <textarea rows="3" cols="65"
                            <?php echo $view ? 'disabled' : ''; ?>
                                  name="comment_content" class="input-txt"
                                  style="background: #FFF;"><?php echo $company_verification_ems->comment_content ?></textarea>
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
                       value="<?php echo $company_verification_ems->case_id; ?>"/>
            </form>

            <!-- <Verification history -->
            <?php include ("system/virtualpost/modules/cases/views/todo/verification_history.php"); ?>
            <!-- End Verification history -->

            <div class="info-content">
                <!-- invoice address -->
                <div class="ym-grid">
                    <div class="ym-gl ym-g100">
                        <h3 style="font-size: 16px"><?php admin_language_e('cases_view_todo_review_company_verification_ems_InvoicingAddress'); ?></h3>
                    </div>
                </div>
                <div class="ym-grid">
                    <table border="0" style="border: 0px; margin:0px">
                        <tr>
                            <th style="padding: 0px; width: 100px;"><label><?php admin_language_e('cases_view_todo_review_company_verification_ems_Name'); ?> </label></th>
                            <td style="padding: 0px"><?php echo $customer_addresses ? $customer_addresses->invoicing_address_name : ""; ?></td>
                        </tr>
                        <tr>
                            <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_company_verification_ems_Company'); ?> </label></th>
                            <td style="padding: 0px"><?php echo $customer_addresses ? $customer_addresses->invoicing_company : ""; ?></td>
                        </tr>
                        <tr>
                            <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_company_verification_ems_Street'); ?> </label></th>
                            <td style="padding: 0px"><?php echo $customer_addresses ? $customer_addresses->invoicing_street : ""; ?></td>
                        </tr>
                        <tr>
                            <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_company_verification_ems_PostCode'); ?> </label></th>
                            <td style="padding: 0px"><?php echo $customer_addresses ? $customer_addresses->invoicing_postcode : ""; ?></td>
                        </tr>
                        <tr>
                            <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_company_verification_ems_City'); ?> </label></th>
                            <td style="padding: 0px"><?php echo $customer_addresses ? $customer_addresses->invoicing_city : ""; ?></td>
                        </tr>
                        <tr>
                            <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_company_verification_ems_Region'); ?> </label></th>
                            <td style="padding: 0px"><?php echo $customer_addresses ? $customer_addresses->invoicing_region : ""; ?></td>
                        </tr>
                        <tr>
                            <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_company_verification_ems_Country'); ?> </label></th>
                            <td style="padding: 0px"><?php echo $customer_addresses ? $customer_addresses->invoicing_country_name : ""; ?></td>
                        </tr>
                        <tr>
                            <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_company_verification_ems_PhoneNo'); ?> </label></th>
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
                                <th style="padding: 0px; width: 100px;"><label><?php admin_language_e('cases_view_todo_review_company_verification_ems_PostboxID'); ?> </label></th>
                                <td style="padding: 0px"><?php echo $select_postbox->postbox_code; ?></td>
                            </tr>
                            <tr>
                                <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_company_verification_ems_Type'); ?> </label></th>
                                <td style="padding: 0px"><?php echo $select_postbox->postbox_type; ?></td>
                            </tr>
                            <tr>
                                <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_company_verification_ems_Name'); ?> </label></th>
                                <td style="padding: 0px"><?php echo $select_postbox->name; ?></td>
                            </tr>
                            <tr>
                                <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_company_verification_ems_Company'); ?></label></th>
                                <td style="padding: 0px"><?php echo $select_postbox->company; ?></td>
                            </tr>
                            <tr>
                                <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_company_verification_ems_Location'); ?> </label></th>
                                <td style="padding: 0px"><?php echo $select_postbox->location_name; ?></td>
                            </tr>
                        </table>
                    </div>
                <?php endforeach; ?>
            </div><!-- end of info -->

        </div>

        <div class="hide" style="display: none;">
            <a id="view_verification_file" class="iframe"><?php admin_language_e('cases_view_todo_review_company_verification_ems_PreviewFile'); ?></a>
        </div>
    </div>
    <script type="text/javascript">
        $(function () {
            $(".bd-content").slimScroll({height: ($(window).height() - 320) + 'px'});
            $(".info-content").slimScroll({height: ($(window).height() - 480) + 'px'});
            $("#tabs").attr("style", 'height:' + ($(window).height() - $("#tabs").offset().top - 75) + 'px');
            $("#tabs").tabs();

            $("#submitButton").click(function () {
                var submitUrl = '<?php echo base_url()?>cases/todo/review_company_verification_E_MS';
                $.ajaxSubmit({
                    url: submitUrl,
                    formId: 'companyEMSVerificationForm',
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

            $("#view_verification_file").fancybox({
                width: 1000,
                height: 800
            });

            /**
            * view all pdf file.
            */
            $(".view-pdf").click(function(){
                var op = $(this).data('op');
                var url = "<?php echo base_url() ?>cases/todo/view_resource";

                url += "?file_id=" + $(this).data('id');
                url += "&op=" + op;

                $('#view_verification_file').attr('href',url);
                $('#view_verification_file').click();
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