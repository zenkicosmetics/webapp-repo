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
                        <div class="ym-gl ym-g100 bd-header"><strong><?php echo $milestone_name; ?> - <?php admin_language_e('cases_view_todo_review_phone_number_case_TriggeredByPhoneNumber'); ?></div>
                    </div>
                    <div class="ym-grid">
                        <div class="ym-gl ym-g100">
                            <div class="ym-grid">
                                <?php
                                $title = "Personal Identification";
                                if($type == "2"){
                                    $title = "Personal Identification of company's authorized representative";
                                }
                                ?>
                                <div><?php echo $title; ?></div>
                            </div>
                            <?php foreach($personal_resources as $b){?>
                                <div class="ym-grid">
                                    <div class="ym-gl ym-g80">
                                        <a class="view-file" data-id="<?php echo $b->id ?>" data-type="personal_identification" data-case-id ="<?php echo $case_id?>"
                                           data-op="01" href="#"><strong><?php echo basename($b->local_file_path)?></strong>
                                        </a>
                                    </div>
                                </div>
                            <?php }?>

                            <?php if($type == "2"){?>
                                <div class="ym-grid">
                                    <div><?php admin_language_e('cases_view_todo_review_phone_number_case_CompanyVerification'); ?></div>
                                </div>
                                <?php foreach($company_resources as $b){?>
                                    <div class="ym-grid">
                                        <div class="ym-gl ym-g80">
                                            <a class="view-file" data-id="<?php echo $b->id ?>" data-type="company_identification" data-case-id ="<?php echo $case_id?>"
                                               data-op="02" href="#"><strong><?php echo basename($b->local_file_path)?></strong>
                                            </a>
                                        </div>
                                    </div>
                                <?php }?>
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
                        <?php foreach($personal_resources as $b){?>
                        <li><a href="#verification-file-tab-<?php echo $b->id?>"><span><?php echo "Verification" .  substr(basename($b->local_file_path),  -11, -4) ?></span></a></li>
                        <?php }?>

                        <?php if($type == "2"){?>
                        <?php foreach($company_resources as $b){?>
                        <li><a href="#verification-file-tab-<?php echo $b->id?>"><span><?php echo "Verification" .  substr(basename($b->local_file_path),  -11, -4) ?></span></a></li>
                        <?php }?>
                        <?php } ?>
                    </ul>
                    <?php foreach($personal_resources as $b){?>
                    <iframe id="verification-file-tab-<?php echo $b->id?>"
                        style="width: 100%; position: absolute; height: 100%; border: none"
                        src="<?php echo base_url() ?>cases/todo/view_resource?file_id=<?php echo $b->id?>">
                    </iframe>
                    <?php }?>

                    <?php if($type == "2"){?>
                        <?php foreach($company_resources as $b){?>
                        <iframe id="verification-file-tab-<?php echo $b->id?>"
                            style="width: 100%; position: absolute; height: 100%; border: none"
                            src="<?php echo base_url() ?>cases/todo/view_resource?file_id=<?php echo $b->id?>">
                        </iframe>
                        <?php }?>
                    <?php } ?>
                </div>
            </div>
        </div>
        <!-- Admin verification -->
        <div class="ym-gl ym-g20" style="margin: 0 10px;">
            <form id="contractVerificationForm" action="#" method="post">
                <div class="ym-grid">
                    <div class="ym-gl ym-g100">
                        Status <span class="required">*</span>
                        <a href="customer_address_info_partial.php"></a>
                    </div>
                </div>
                <div class="ym-grid">
                    <div class="ym-gl ym-g100">
                        <select class="input-width" name="status"
                            <?php echo $view ? 'disabled' : ''; ?>>
                            <option
                                <?php echo $contract->status == "2" ? "selected" : ""; ?>
                                value="2"><?php admin_language_e('cases_view_todo_review_phone_number_case_Complete'); ?>  </option>
                            <option
                                <?php echo $contract->status == "3" ? "selected" : ""; ?>
                                value="3"><?php admin_language_e('cases_view_todo_review_phone_number_case_Incomplete'); ?>
                            </option>
                        </select>
                    </div>
                </div>
                <div class="ym-grid">
                    <div class="ym-gl ym-g100">
                        Comment <span class="required">*</span>
                    </div>
                </div>
                 <div class="ym-grid">
                    <div class="ym-gl ym-g100">
                        <label><?php admin_language_e('cases_view_todo_review_phone_number_case_LastConfirmed'); ?>:</label>
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

            <!-- customer address info -->
            <?php include ("system/virtualpost/modules/cases/views/todo/customer_address_info_partial.php"); ?>
            <!-- customer address info -->

        </div>

        <div class="hide" style="display: none;">
            <a id="view_verification_file" class="iframe"  href=""><?php admin_language_e('cases_view_todo_review_phone_number_case_PreviewFile'); ?></a>
        </div>
    </div>
    <script type="text/javascript">
        $(function () {
            $(".bd-content").slimScroll({height: ($(window).height() - 320) + 'px'});
            $(".info-content").slimScroll({height: ($(window).height() - 480) + 'px'});
            $("#tabs").attr("style", 'height:' + ($(window).height() - $("#tabs").offset().top - 75) + 'px');
            $("#tabs").tabs();

            $("#submitButton").click(function () {
                var submitUrl = '<?php echo base_url()?>cases/todo/review_<?php echo $base_task_name ?>';
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

            $(".view-file").live("click", function(e){
                e.preventDefault();

                var id = $(this).data("id");
                var case_id = $(this).data("case-id");
                var time = $.now();
                var op = $(this).data("op");
                var url = "<?php echo base_url()?>cases/todo/";
                url += "view_resource?file_id="+id+"&type=phone_number&case_id="+case_id+"&t="+time+"&op="+op;

                $("#view_verification_file").attr("href", url);
                $("#view_verification_file").click();

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