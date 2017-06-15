<style>
.xx .input-btn {
    padding: 5px 15px;
    color: #000;
    background: #c9c9c9;
    border: 1px solid #a5a5a5;
    border-radius: 4px;
    cursor: pointer;
    text-align: center;
    width: 300px;
}

.xx .input-btn span {
    text-decoration: underline;
}

.xx .ym-grid {
    margin-bottom: 12px !important;
    margin-top: 0px !important;
}

.xx a:HOVER {
    text-decoration: none;
}
.btn-comment{
    color: red;
    border: 1px solid #a5a5a5;
    border-radius: 4px;
    width: 500px;
    padding: 5px 15px;
}
</style>
<div class="ym-grid content" id="case-body-wrapper">
    <?php if (empty($message)) { ?>
    <div class="cloud-body-wrapper xx">
        <!--         <div class="ym-grid"> -->
        <!--             <div class="ym-gl ym-g100">Verifications required:</div> -->
        <!--         </div> -->
        <div class="header">
            <h2 style="font-size: 20px; margin-bottom: 10px"><?php language_e('cases_view_verification_index_VerificationsRequired'); ?>:</h2>
        </div>

        <?php foreach ($list_case_result as $key=>$list_cases){ ?>
        <?php foreach ($list_cases['list_case_result'] as $cases){ ?>
            <?php if (!$cases["is_completed_verify_group"]) {?>
            <div class="ym-grid">
                <div class="ym-gl ym-g4">
                    <strong><?php echo $cases["group_name"];?></strong>
                </div>
            </div>
            <?php }?>
            <?php if (!$cases["is_personal_identification"]) {?>
            <div class="ym-grid">
                <div class="ym-gl ym-g40">
                    <?php
                        $comment = CaseUtils::getCommentOfCase($cases["case_id"], 'verification_personal_identification');
                        $date = CaseUtils::getCommentDateOfCase($cases["case_id"], 'verification_personal_identification');
                        $dateFormat="";
                        if (isset($date) && $date != "" )
                            $dateFormat = date(APConstants::DATE_TIME, $date);
                    ?>
                    <div class="input-btn <?php if($cases["is_personal_status"] != 1){echo "xbtn";} ?>"
                        data-is-started="<?php echo $cases["is_personal_identification_started"];?>"
                        data-case-id="<?php echo $cases["case_id"];?>"
                        data-href='<?php echo base_url()?>cases/verification/verification_personal_identification?case_id=<?php echo $cases["case_id"]?>'>
                        <strong><?php echo CaseUtils::get_milestone_name($cases["case_id"], "verification_personal_identification");?></strong><br />

                        <?php if($cases["is_personal_status"] == 1):?>
                        <samp><?php language_e('cases_view_verification_index_InProcess'); ?></samp>
                        <?php  elseif($comment):?>
                        <span><?php language_e('cases_view_verification_index_CorrectVerification'); ?></span>
                        <?php  else:?>
                        <span><?php language_e('cases_view_verification_index_StartVerificationProcess'); ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if($comment && $cases["is_personal_status"] != 1):?>
                    <div class="ym-gl ym-g60">
                       <table class=" btn-comment" style="margin:-0.3em 0em;width:100%;">
                             <tr>
                                <td> <?php echo $comment;?> </td>
                                <td align="right" width="45%"><?php echo  "Comment Timestamp: " . $dateFormat;?></td>
                             </tr>
                        </table>
                    </div>
                <?php endif;?>
            </div>
            <?php } ?>
            <?php if (!$cases["is_company_verification"]) {?>
            <div class="ym-grid">
                <div class="ym-gl ym-g40">
                    <?php
                        $comment = CaseUtils::getCommentOfCase($cases["case_id"], 'verification_company_identification_soft');
                        $date = CaseUtils::getCommentDateOfCase($cases["case_id"], 'verification_company_identification_soft');
                        $dateFormat="";
                        if (isset($date) && $date != "" )
                            $dateFormat = date(APConstants::DATE_TIME, $date);
                    ?>
                    <div class="input-btn <?php if($cases["is_company_status"] != 1){echo "xbtn";} ?>"
                        data-is-started="<?php echo $cases["is_company_verification_started"];?>"
                        data-case-id="<?php echo $cases["case_id"];?>"
                        data-href='<?php echo base_url()?>cases/verification/verification_company_identification_soft?case_id=<?php echo $cases["case_id"]?>'>
                        <strong><?php echo CaseUtils::get_milestone_name($cases["case_id"], "verification_company_identification_soft"); ?></strong><br />

                        <?php if($cases["is_company_status"] == 1):?>
                        <samp><?php language_e('cases_view_verification_index_InProcess'); ?></samp>
                        <?php  elseif($comment):?>
                        <span><?php language_e('cases_view_verification_index_CorrectVerification'); ?></span>
                        <?php  else:?>
                        <span><?php language_e('cases_view_verification_index_StartVerificationProcess'); ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if($comment && $cases["is_company_status"] != 1):?>
                <div class="ym-gl ym-g60">
                    <table class=" btn-comment" style="margin:-0.3em 0em;width:100%;">
                        <tr>
                            <td> <?php echo $comment;?> </td>
                            <td align="right" width="45%"><?php echo  "Comment Timestamp: " . $dateFormat;?></td>
                        </tr>
                    </table>
                </div>
                <?php endif;?>
            </div>
            <?php } ?>
            <?php if (!$cases["is_company_shareholder_verification"]) {?>
            <div class="ym-grid">
                <div class="ym-gl ym-g40">
                    <?php
                        $comment = CaseUtils::getCommentOfCase($cases["case_id"], 'verification_company_identification_hard');
                        $date = CaseUtils::getCommentDateOfCase($cases["case_id"], 'verification_company_identification_hard');
                        $dateFormat="";
                        if (isset($date) && $date != "" )
                            $dateFormat = date(APConstants::DATE_TIME, $date);
                    ?>
                    <div class="input-btn <?php if($cases["is_company_hard_status"] != 1){echo "xbtn";} ?>"
                        data-is-started="<?php echo $cases["is_company_shareholder_verification_started"];?>"
                        data-case-id="<?php echo $cases["case_id"];?>"
                        data-href='<?php echo base_url()?>cases/verification/verification_company_identification_hard?case_id=<?php echo $cases["case_id"]?>'>
                        <strong><?php echo CaseUtils::get_milestone_name($cases["case_id"], "verification_company_identification_hard"); ?></strong><br />

                        <?php if($cases["is_company_hard_status"] == 1):?>
                        <samp><?php language_e('cases_view_verification_index_InProcess'); ?></samp>
                        <?php  elseif($comment):?>
                        <span><?php language_e('cases_view_verification_index_CorrectVerification'); ?></span>
                        <?php  else:?>
                        <span><?php language_e('cases_view_verification_index_StartVerificationProcess'); ?></span>
                        <?php endif; ?>

                    </div>
                </div>
                <?php if($comment && $cases["is_company_hard_status"] != 1):?>
                    <div class="ym-gl ym-g60">
                        <table class=" btn-comment" style="margin:-0.3em 0em;width:100%;">
                            <tr>
                                <td> <?php echo $comment;?> </td>
                                <td align="right" width="45%"><?php echo  "Comment Timestamp: " . $dateFormat;?></td>
                             </tr>
                        </table>
                    </div>
                <?php endif;?>
            </div>
            <?php } ?>
            <?php if (!$cases["is_general_cmra_verify"]) {?>
            <div class="ym-grid">
                <div class="ym-gl ym-g40">
                    <?php
                        $comment = CaseUtils::getCommentOfCase($cases["case_id"], 'verification_General_CMRA');
                        $date = CaseUtils::getCommentDateOfCase($cases["case_id"], 'verification_General_CMRA');
                        $dateFormat="";
                        if (isset($date) && $date != "" )
                            $dateFormat = date(APConstants::DATE_TIME, $date);
                    ?>
                    <div class="input-btn <?php if($cases["is_general_cmra_status"] != 1){echo "xbtn";} ?>"
                        data-is-started="<?php echo $cases["is_general_cmra_verify_started"];?>"
                        data-case-id="<?php echo $cases["case_id"];?>"
                        data-href='<?php echo base_url()?>cases/verification/verification_General_CMRA?case_id=<?php echo $cases["case_id"]?>'>
                        <strong><?php echo CaseUtils::get_milestone_name($cases["case_id"], "verification_General_CMRA"); ?></strong><br />

                        <?php if($cases["is_general_cmra_status"] == 1):?>
                        <samp><?php language_e('cases_view_verification_index_InProcess'); ?></samp>
                        <?php  elseif($comment):?>
                        <span><?php language_e('cases_view_verification_index_CorrectVerification'); ?></span>
                        <?php  else:?>
                        <span><?php language_e('cases_view_verification_index_StartVerificationProcess'); ?></span>
                        <?php endif; ?>
                    </div>
                </div>

               <?php if($comment && $cases["is_general_cmra_status"] != 1):?>
               <div class="ym-gl ym-g60">
                    <table class=" btn-comment" style="margin:-0.3em 0em;width:100%;">
                        <tr>
                            <td> <?php echo $comment;?> </td>
                            <td align="right" width="45%"><?php echo  "Comment Timestamp: " . $dateFormat;?></td>
                        </tr>
                    </table>
               </div>
                <?php endif;?>
            </div>
            <?php } else if (!$cases["is_USPS_form_1583_verify"]) {?>
            <div class="ym-grid">
                <div class="ym-gl ym-g40">
                    <?php
                        $comment = CaseUtils::getCommentOfCase($cases["case_id"], 'verification_special_form_PS1583');
                        $date = CaseUtils::getCommentDateOfCase($cases["case_id"], 'verification_special_form_PS1583');
                        $dateFormat="";
                        if (isset($date) && $date != "" )
                            $dateFormat = date(APConstants::DATE_TIME, $date);
                    ?>
                    <div class="input-btn <?php if($cases["is_USPS_form_1583_status"] != 1){echo "xbtn";} ?>"
                        data-is-started="<?php echo $cases["is_USPS_form_1583_verify_started"];?>"
                        data-case-id="<?php echo $cases["case_id"];?>"
                        data-href='<?php echo base_url()?>cases/verification/verification_special_form_PS1583?case_id=<?php echo $cases["case_id"]?>'>
                        <strong><?php echo CaseUtils::get_milestone_name($cases["case_id"], "verification_special_form_PS1583"); ?></strong><br />

                        <?php if($cases["is_USPS_form_1583_status"] == 1):?>
                        <samp><?php language_e('cases_view_verification_index_InProcess'); ?></samp>
                        <?php  elseif($comment):?>
                        <span><?php language_e('cases_view_verification_index_CorrectVerification'); ?></span>
                        <?php  else:?>
                        <span><?php language_e('cases_view_verification_index_StartVerificationProcess'); ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if($comment && $cases["is_USPS_form_1583_status"] != 1):?>
                <div class="ym-gl ym-g60">
                    <table class=" btn-comment" style="margin:-0.3em 0em;width:100%;">
                        <tr>
                            <td> <?php echo $comment;?> </td>
                            <td align="right" width="45%"><?php echo  "Comment Timestamp: " . $dateFormat;?></td>
                        </tr>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <?php }else if(!$cases["is_california_mailbox_verify"]){ ?>
            <div class="ym-grid">
                <div class="ym-gl ym-g40">
                    <?php
                        $comment = CaseUtils::getCommentOfCase($cases["case_id"], 'verification_california_mailbox');
                        $date = CaseUtils::getCommentDateOfCase($cases["case_id"], 'verification_california_mailbox');
                        $dateFormat="";
                        if (isset($date) && $date != "" )
                            $dateFormat = date(APConstants::DATE_TIME, $date);
                    ?>
                    <div class="input-btn <?php if($cases["is_california_mailbox_verify"] != 1){echo "xbtn";} ?>"
                        data-is-started="<?php echo $cases["is_california_mailbox_verify_started"];?>"
                        data-case-id="<?php echo $cases["case_id"];?>"
                        data-href='<?php echo base_url()?>cases/verification/verification_california_mailbox?case_id=<?php echo $cases["case_id"]?>'>
                        <strong><?php echo CaseUtils::get_milestone_name($cases["case_id"], "verification_california_mailbox"); ?></strong><br />

                        <?php if($cases["is_california_mailbox_status"] == 1):?>
                        <samp><?php language_e('cases_view_verification_index_InProcess'); ?></samp>
                        <?php  elseif($comment):?>
                        <span><?php language_e('cases_view_verification_index_CorrectVerification'); ?></span>
                        <?php  else:?>
                        <span><?php language_e('cases_view_verification_index_StartVerificationProcess'); ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if($comment && $cases["is_california_mailbox_status"] != 1):?>
                <div class="ym-gl ym-g60">
                    <table class=" btn-comment" style="margin:-0.3em 0em;width:100%;">
                        <tr>
                            <td> <?php echo $comment;?> </td>
                            <td align="right" width="45%"><?php echo  "Comment Timestamp: " . $dateFormat;?></td>
                        </tr>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <?php }?>

            <!-- BEGIN TC CONTRACT --->
            <?php if (!$cases["is_tc_contract_verify"]) {?>
            <div class="ym-grid">
                <div class="ym-gl ym-g40">
                    <?php
                        $comment = CaseUtils::getCommentOfCase($cases["case_id"], 'TC_contract_MS');
                        $date = CaseUtils::getCommentDateOfCase($cases["case_id"], 'TC_contract_MS');
                        $dateFormat="";
                        if (isset($date) && $date != "" )
                            $dateFormat = date(APConstants::DATE_TIME, $date);
                    ?>
                    <div class="input-btn <?php if($cases["is_tc_contract_status"] != 1){echo "xbtn";} ?>"
                        data-is-started="<?php echo $cases["is_tc_contract_verification_started"];?>"
                        data-case-id="<?php echo $cases["case_id"];?>"
                        data-href='<?php echo base_url()?>cases/verification/TC_contract_MS?case_id=<?php echo $cases["case_id"]?>'>
                        <strong><?php echo CaseUtils::get_milestone_name($cases["case_id"], "TC_contract_MS");?></strong><br />

                        <?php if($cases["is_tc_contract_status"] == 1):?>
                        <samp><?php language_e('cases_view_verification_index_InProcess'); ?></samp>
                        <?php  elseif($comment):?>
                        <span><?php language_e('cases_view_verification_index_CorrectVerification'); ?></span>
                        <?php  else:?>
                        <span><?php language_e('cases_view_verification_index_StartVerificationProcess'); ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if($comment && $cases["is_tc_contract_status"] != 1):?>
                    <div class="ym-gl ym-g60">
                       <table class=" btn-comment" style="margin:-0.3em 0em;width:100%;">
                             <tr>
                                <td> <?php echo $comment;?> </td>
                                <td align="right" width="45%"><?php echo  "Comment Timestamp: " . $dateFormat;?></td>
                             </tr>
                        </table>
                    </div>
                <?php endif;?>
            </div>
            <?php } ?>
            <!-- END TC CONTRACT --->

            <!-- BEGIN proof address --->
            <?php if (!$cases["is_proof_address_verify"]) { ?>

            <div class="ym-grid">
                <div class="ym-gl ym-g40">

                    <?php
                        $comment = CaseUtils::getCommentOfCase($cases["case_id"], 'proof_of_address_MS');
                        $date = CaseUtils::getCommentDateOfCase($cases["case_id"], 'proof_of_address_MS');
                        $dateFormat="";
                        if (isset($date) && $date != "" )
                            $dateFormat = date(APConstants::DATE_TIME, $date);
                    ?>
                    <div class="input-btn <?php if($cases["is_proof_address_status"] != 1){echo "xbtn";} ?>"
                        data-is-started="<?php echo $cases["is_proof_address_verification_started"];?>"
                        data-case-id="<?php echo $cases["case_id"];?>"
                        data-href='<?php echo base_url()?>cases/verification/proof_of_address_MS?case_id=<?php echo $cases["case_id"]?>'>
                        <strong><?php echo CaseUtils::get_milestone_name($cases["case_id"], "proof_of_address_MS");?></strong><br />

                        <?php if($cases["is_proof_address_status"] == 1):?>
                        <samp><?php language_e('cases_view_verification_index_InProcess'); ?></samp>
                        <?php  elseif($comment):?>
                        <span><?php language_e('cases_view_verification_index_CorrectVerification'); ?></span>
                        <?php  else:?>
                        <span><?php language_e('cases_view_verification_index_StartVerificationProcess'); ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if($comment && $cases["is_proof_address_status"] != 1):?>
                    <div class="ym-gl ym-g60">
                       <table class=" btn-comment" style="margin:-0.3em 0em;width:100%;">
                             <tr>
                                <td> <?php echo $comment;?> </td>
                                <td align="right" width="45%"><?php echo  "Comment Timestamp: " . $dateFormat;?></td>
                             </tr>
                        </table>
                    </div>
                <?php endif;?>
            </div>
            <?php } ?>
            <!-- END proof address --->

            <!-- BEGIN company EMS--->
            <?php if (!$cases["is_company_verification_ems_verify"]) { ?>

            <div class="ym-grid">
                <div class="ym-gl ym-g40">

                    <?php
                        $comment = CaseUtils::getCommentOfCase($cases["case_id"], 'company_verification_E_MS');
                        $date = CaseUtils::getCommentDateOfCase($cases["case_id"], 'company_verification_E_MS');
                        $dateFormat="";
                        if (isset($date) && $date != "" )
                            $dateFormat = date(APConstants::DATE_TIME, $date);
                    ?>
                    <div class="input-btn <?php if($cases["is_company_verification_ems_status"] != 1){echo "xbtn";} ?>"
                        data-is-started="<?php echo $cases["is_proof_address_verification_started"];?>"
                        data-case-id="<?php echo $cases["case_id"];?>"
                        data-href='<?php echo base_url()?>cases/verification/company_verification_E_MS?case_id=<?php echo $cases["case_id"]?>'>
                        <strong><?php echo CaseUtils::get_milestone_name($cases["case_id"], "company_verification_E_MS");?></strong><br />

                        <?php if($cases["is_company_verification_ems_status"] == 1):?>
                        <samp>In process...</samp>
                        <?php  elseif($comment):?>
                        <span><?php language_e('cases_view_verification_index_CorrectVerification'); ?></span>
                        <?php  else:?>
                        <span><?php language_e('cases_view_verification_index_StartVerificationProcess'); ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if($comment && $cases["is_company_verification_ems_status"] != 1):?>
                    <div class="ym-gl ym-g60">
                       <table class=" btn-comment" style="margin:-0.3em 0em;width:100%;">
                             <tr>
                                <td> <?php echo $comment;?> </td>
                                <td align="right" width="45%"><?php echo  "Comment Timestamp: " . $dateFormat;?></td>
                             </tr>
                        </table>
                    </div>
                <?php endif;?>
            </div>
            <?php } ?>
            <!-- END company EMS --->


            <!-- START phone personal verification-->
            <?php if ( isset($cases["is_personal_phone_identification"]) && !$cases["is_personal_phone_identification"]) {?>
            <div class="ym-grid">
                <div class="ym-gl ym-g40">
                    <?php
                        $comment = CaseUtils::getCommentOfCase($cases["case_id"], 'phone_number_for_personal');
                        $date = CaseUtils::getCommentDateOfCase($cases["case_id"], 'phone_number_for_personal');
                        $dateFormat="";
                        if (isset($date) && $date != "" )
                            $dateFormat = date(APConstants::DATE_TIME, $date);
                    ?>
                    <div class="input-btn <?php if($cases["is_personal_phone_status"] != 1){echo "xbtn";} ?>"
                        data-is-started="<?php echo $cases["is_phone_personal_verification_started"];?>"
                        data-case-id="<?php echo $cases["case_id"];?>"
                        data-href='<?php echo base_url()?>cases/verification/phone_number_for_personal?type=1&case_id=<?php echo $cases["case_id"]?>'>
                        <strong><?php echo CaseUtils::get_milestone_name($cases["case_id"], "phone_number_for_personal");?></strong><br />

                        <?php if($cases["is_personal_phone_status"] == 1):?>
                        <samp><?php language_e('cases_view_verification_index_InProcess'); ?></samp>
                        <?php  elseif($comment):?>
                        <span><?php language_e('cases_view_verification_index_CorrectVerification'); ?></span>
                        <?php  else:?>
                        <span><?php language_e('cases_view_verification_index_StartVerificationProcess'); ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if($comment && $cases["is_personal_phone_status"] != 1):?>
                    <div class="ym-gl ym-g60">
                       <table class=" btn-comment" style="margin:-0.3em 0em;width:100%;">
                             <tr>
                                <td> <?php echo $comment;?> </td>
                                <td align="right" width="45%"><?php echo  "Comment Timestamp: " . $dateFormat;?></td>
                             </tr>
                        </table>
                    </div>
                <?php endif;?>
            </div>
            <?php } ?>
            <!-- END phone personal verification-->

            <!-- START phone company verification-->
            <?php if (isset($cases["is_company_phone_verification"]) && !$cases["is_company_phone_verification"]) {?>
            <div class="ym-grid">
                <div class="ym-gl ym-g40">
                    <?php
                        $comment = CaseUtils::getCommentOfCase($cases["case_id"], 'phone_number_company');
                        $date = CaseUtils::getCommentDateOfCase($cases["case_id"], 'phone_number_company');
                        $dateFormat="";
                        if (isset($date) && $date != "" )
                            $dateFormat = date(APConstants::DATE_TIME, $date);
                    ?>
                    <div class="input-btn <?php if($cases["is_company_phone_status"] != 1){echo "xbtn";} ?>"
                        data-is-started="<?php echo $cases["is_phone_company_verification_started"];?>"
                        data-case-id="<?php echo $cases["case_id"];?>"
                        data-href='<?php echo base_url()?>cases/verification/phone_number_company?type=2&case_id=<?php echo $cases["case_id"]?>'>
                        <strong><?php echo CaseUtils::get_milestone_name($cases["case_id"], "phone_number_company");?></strong><br />

                        <?php if($cases["is_company_phone_status"] == 1):?>
                        <samp><?php language_e('cases_view_verification_index_InProcess'); ?></samp>
                        <?php  elseif($comment):?>
                        <span><?php language_e('cases_view_verification_index_CorrectVerification'); ?></span>
                        <?php  else:?>
                        <span><?php language_e('cases_view_verification_index_StartVerificationProcess'); ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if($comment && $cases["is_company_phone_status"] != 1):?>
                    <div class="ym-gl ym-g60">
                       <table class=" btn-comment" style="margin:-0.3em 0em;width:100%;">
                             <tr>
                                <td> <?php echo $comment;?> </td>
                                <td align="right" width="45%"><?php echo  "Comment Timestamp: " . $dateFormat;?></td>
                             </tr>
                        </table>
                    </div>
                <?php endif;?>
            </div>
            <?php } ?>
            <!-- END phone company verification-->


        <?php } // end foreach loop list case?>
        <?php } // end foreach loop list case-result?>

        <?php if (!$is_completed_verify) {?>
        <div class="ym-grid">
            <div class="ym-gl ym-g60">
                <?php language_e('cases_view_verification_index_WeAreRequiredByLawToVerifySome'); ?>
            </div>
        </div>
        <div class="ym-grid">
            <div class="ym-gl ym-g100">
                <?php language_e('cases_view_verification_index_GetHelpWithVerificationWriteUs'); ?><a href="mailto:mail@clevver.io">mail@clevver.io</a>
            </div>
        </div>
        <?php } ?>
        <?php if (count($list_case_result) == 0 ) {?>
        <div class="ym-grid">
            <div class="ym-gl ym-g60"><?php language_e('cases_view_verification_index_thereAreCurrentlyNoVerificatio'); ?></div>
        </div>
        <?php } ?>
        <div class="ym-clearfix"></div>
    </div>
    <?php  } else { ?>
     <div class="ym-grid">
        <div class="ym-gl ym-g60">
                <?php echo $message;?>
            </div>
    </div>
    <div class="ym-clearfix"></div>
    <?php } ?>
</div>
<script type="text/javascript">
$(function(){
    $(".xbtn").click(function(){
        var _this =$(this);
        $.confirm({
            message:"Do you want to start the verification?"
            ,yes: function(){
                window.location.href = _this.data("href");
            }
        });
    });
});
</script>