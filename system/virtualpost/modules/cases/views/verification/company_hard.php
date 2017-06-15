<style>
.xx .input-btn {
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
    width: 300px;
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

.xx .bd {
    border: 1px solid #a5a5a5;
    padding: 20px !important;
    /*     max-height: 460px; */
    /*     height: 460px; */
    overflow-y: auto;
}

.xx .bd-header {
    border-bottom: 1px solid #a5a5a5;
    padding-bottom: 12px !important;
    font-size: 1.2em;
    font-weight: bold;
}

.xx .description strong {
    margin-right: 10px;
}

input.input-txt {
    margin-left: 0px !important;
}

.upload-success {
    color: #fff;
    background: #336699;
}

.xx td, th {
    padding: .45em .15em !important;
}

.xx table .ui-button-text {
    padding: .5em .4em !important;
}
textarea.input-txt {
    background: #fff;
    border: 1px solid #DADADA;
    border-radius: 3px 3px 3px 3px;
    font-size: 13px;
    height: 120px;
    margin-left: 0px;
    text-indent: 5px;
    width: 98%;
    line-height: 25px;
    padding: 10px;
}

.input-error {
    border: 1px #800 solid !important;
    color: #800;
}
</style>

<div class="ym-grid content" id="case-body-wrapper">
    <div class="cloud-body-wrapper xx">
        <div class="ym-grid">
            <h2 style="font-size: 20px; margin-bottom: 10px"><?php language_e('cases_view_verification_company_hard_VerificationsRequired'); ?>:</h2>
        </div>
        <div class="ym-grid">
            <div class="ym-gl ym-g100 bd">
                <div class="ym-grid">
                    <div class="bd-header"><?php language_e('cases_view_verification_company_hard_CompanyampShareholderVerification'); ?></div>
                </div>
                <form id="compHardVerificationForm" action="#"
                    method="post">
                    <div class="ym-grid">
                        <div class="ym-gl ym-g100 bd-content">
                            <div class="ym-grid">
                                <div class="ym-gl ym-g50">
                                    <div class="ym-grid">
                                        <div class="ym-gl ym-g100">
                                        <?php
                                            $type = $is_invoicing_address_verification ? "invoice" : "postbox";
                                            language_e('cases_view_verification_company_hard_YourCompanyNameAndAddressMsg', ['type'=> $type]);
                                        ?>
                                        </div>
                                    </div>
                                    <div class="ym-grid">
                                        <div class="ym-gl ym-g80">


                                            <div class="description">
                                                <strong><?php echo $postbox[0]->name?></strong><a
                                                    href="<?php echo base_url()?>account" class="main_link_color">change</a>
                                            </div>


                                           <div class="description">
                                                <strong><?php echo $postbox[0]->company?></strong><a
                                                    href="<?php echo base_url()?>account" class="main_link_color">change</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ym-grid">
                                        <div class="ym-gl ym-g100"><?php language_e('cases_view_verification_company_hard_PleaseUploadMsg'); ?></div>
                                    </div>
                                    <div class="ym-grid">
                                        <div class="ym-gl ym-g100">
                                            <input type="text"
                                                id="passport_verification_txt" name="passport_verification_txt"
                                                class="input-txt"
                                                value="<?php echo empty($cases_verification)?"": basename($cases_verification->verification_local_file_path)?>"
                                                style="width: 200px; margin-left: 0px !important;">
                                                <input type="hidden" id="passport_verification_change" name="passport_verification_change" value="0" >
                                            <button type="button"
                                                id="passport_verification_btn"><?php language_e('cases_view_verification_company_hard_Browse'); ?></button>
                                            <button type="button"
                                                class="<?php echo empty($cases_verification)||empty($cases_verification->verification_local_file_path)?"":"upload-success" ?>"
                                                id="passport_view_btn"><?php language_e('cases_view_verification_company_hard_View'); ?></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="ym-gl ym-g50">
                                    <div class="ym-grid">
                                        <div class="ym-gl ym-g100"><?php language_e('cases_view_verification_company_hard_PleaseListAllNaturalPersons'); ?>:</div>
                                    </div>
                                    <div class="ym-grid">
                                        <div class="ym-gl ym-g100">
                                            <table>
                                                <colgroup>
                                                    <col width="40%">
                                                    <col width="10%">
                                                    <col width="20%">
                                                    <col width="10%">
                                                    <col width="10%">
                                                    <col width="10%">
                                                </colgroup>
                                                <tr>
                                                    <th><?php language_e('cases_view_verification_company_hard_Name'); ?>:</th>
                                                    <th>%</th>
                                                    <th><?php language_e('cases_view_verification_company_hard_IdentificationDocument'); ?></th>
                                                    <th colspan="3"></th>
                                                </tr>
                                                <tr>
                                                    <td><input
                                                        type="text"
                                                        value="<?php echo empty($cases_verification)?"":$cases_verification->shareholders_name_01?>"
                                                        name="shareholders[1][name]" id="shareholders_1_name"
                                                        class="input-txt">

                                                        </td>
                                                    <td><input
                                                        type="text"
                                                        value="<?php echo empty($cases_verification)||($cases_verification->shareholders_rate_01==0)?"":$cases_verification->shareholders_rate_01?>"
                                                        name="shareholders[1][rate]" id="shareholders_1_rate"
                                                        class="input-txt"></td>
                                                    <td><input
                                                        type="text"
                                                        class="input-txt"
                                                        value="<?php echo empty($cases_verification)?"": basename($cases_verification->shareholders_local_file_path_01)?>"
                                                        id="shareholders_file_name_txt_01" name="shareholders_file_name_txt_01" readonly>

                                                        <input type="hidden" value="0" id="shareholders_file_name_change_01" name="shareholders_file_name_change_01">

                                                        </td>
                                                    <td><button
                                                            type="button"
                                                            id="shareholders_browse_btn_01"><?php language_e('cases_view_verification_company_hard_Upload'); ?></button>
                                                     <button id="shareholders_01_delete_btn" style="display: none;"><?php language_e('cases_view_verification_company_hard_Delete'); ?></button></td>
                                                    <td><button
                                                            type="button"
                                                            class="<?php echo empty($cases_verification)||empty($cases_verification->shareholders_local_file_path_01)?"":"upload-success" ?>"
                                                            id="shareholders_view_btn_01"><?php language_e('cases_view_verification_company_hard_View'); ?></button></td>
                                                </tr>
                                                <tr>
                                                    <td><input
                                                        type="text"
                                                        value="<?php echo empty($cases_verification)?"":$cases_verification->shareholders_name_02?>"
                                                        name="shareholders[2][name]" id="shareholders_2_name"
                                                        class="input-txt">
                                                        </td>
                                                    <td><input
                                                        type="text"
                                                        value="<?php echo empty($cases_verification)||($cases_verification->shareholders_rate_02==0)?"":$cases_verification->shareholders_rate_02?>"
                                                        name="shareholders[2][rate]" id="shareholders_2_rate"
                                                        class="input-txt"></td>
                                                    <td><input
                                                        type="text"
                                                        class="input-txt"
                                                        value="<?php echo empty($cases_verification)?"": basename($cases_verification->shareholders_local_file_path_02)?>"
                                                        id="shareholders_file_name_txt_02" name="shareholders_file_name_txt_02" readonly>
                                                        <input type="hidden" value="0" id="shareholders_file_name_change_02" name="shareholders_file_name_change_02">
                                                        </td>
                                                    <td><button
                                                            type="button"
                                                            id="shareholders_browse_btn_02"><?php language_e('cases_view_verification_company_hard_Upload'); ?></button>
                                                    <button id="shareholders_02_delete_btn" style="display: none;"><?php language_e('cases_view_verification_company_hard_Delete'); ?></button></td>
                                                    <td><button
                                                            type="button"
                                                            class="<?php echo empty($cases_verification)||empty($cases_verification->shareholders_local_file_path_02)?"":"upload-success" ?>"
                                                            id="shareholders_view_btn_02"><?php language_e('cases_view_verification_company_hard_View'); ?></button></td>
                                                </tr>
                                                <tr>
                                                    <td><input
                                                        type="text"
                                                        value="<?php echo empty($cases_verification)?"":$cases_verification->shareholders_name_03?>"
                                                        name="shareholders[3][name]" id="shareholders_3_name"
                                                        class="input-txt">
                                                        </td>
                                                    <td><input
                                                        type="text"
                                                        value="<?php echo empty($cases_verification)||($cases_verification->shareholders_rate_03==0)?"":$cases_verification->shareholders_rate_03?>"
                                                        name="shareholders[3][rate]" id="shareholders_3_rate"
                                                        class="input-txt"></td>
                                                    <td><input
                                                        type="text"
                                                        class="input-txt"
                                                        value="<?php echo empty($cases_verification)?"": basename($cases_verification->shareholders_local_file_path_03)?>"
                                                        id="shareholders_file_name_txt_03" name="shareholders_file_name_txt_03" readonly>
                                                        <input type="hidden" value="0" id="shareholders_file_name_change_03" name="shareholders_file_name_change_03">
                                                        </td>
                                                    <td><button
                                                            type="button"
                                                            id="shareholders_browse_btn_03"><?php language_e('cases_view_verification_company_hard_Upload'); ?></button>
                                                    <button id="shareholders_03_delete_btn" style="display: none;"><?php language_e('cases_view_verification_company_hard_Delete'); ?></button></td>
                                                    <td><button
                                                            type="button"
                                                            class="<?php echo empty($cases_verification)||empty($cases_verification->shareholders_local_file_path_03)?"":"upload-success" ?>"
                                                            id="shareholders_view_btn_03"><?php language_e('cases_view_verification_company_hard_View'); ?></button></td>
                                                </tr>
                                                <tr>
                                                    <td><input
                                                        type="text"
                                                        value="<?php echo empty($cases_verification)?"":$cases_verification->shareholders_name_04?>"
                                                        name="shareholders[4][name]" id="shareholders_4_name"
                                                        class="input-txt">
                                                        </td>
                                                    <td><input
                                                        type="text"
                                                        value="<?php echo empty($cases_verification)||($cases_verification->shareholders_rate_04==0)?"":$cases_verification->shareholders_rate_04?>"
                                                        name="shareholders[4][rate]" id="shareholders_4_rate"
                                                        class="input-txt"></td>
                                                    <td><input
                                                        type="text"
                                                        class="input-txt"
                                                        value="<?php echo empty($cases_verification)?"": basename($cases_verification->shareholders_local_file_path_04)?>"
                                                        id="shareholders_file_name_txt_04"  name="shareholders_file_name_txt_04" readonly>
                                                        <input type="hidden" value="0" id="shareholders_file_name_change_04" name="shareholders_file_name_change_04">
                                                        </td>
                                                    <td><button
                                                            type="button"
                                                            id="shareholders_browse_btn_04"><?php language_e('cases_view_verification_company_hard_Upload'); ?></button>
                                                    <button id="shareholders_04_delete_btn" style="display: none;"><?php language_e('cases_view_verification_company_hard_Delete'); ?></button></td>
                                                    <td><button
                                                            type="button"
                                                            class="<?php echo empty($cases_verification)||empty($cases_verification->shareholders_local_file_path_04)?"":"upload-success" ?>"
                                                            id="shareholders_view_btn_04"><?php language_e('cases_view_verification_company_hard_View'); ?></button></td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 200%; float: left;">
                                                        <div style="width: 15px; float: left;">
                                                            <input type="checkbox" id="confirm_check_box" name="confirm_check_box" value="1">
                                                        </div>
                                                        <div class="description" style="margin-top: 0px !important;"> <?php language_e('cases_view_verification_company_hard_NoIndividualOwnsMoreThan249OfT'); ?></div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g80">
                                    <input type="file"
                                        id="passport_verification_file"
                                        name="business_registration_verification"
                                        style="display: none"> <input
                                        type="file"
                                        id="shareholders_verification_file_01"
                                        name="shareholders_verification_01"
                                        style="display: none"><input
                                        type="file"
                                        id="shareholders_verification_file_02"
                                        name="shareholders_verification_02"
                                        style="display: none"><input
                                        type="file"
                                        id="shareholders_verification_file_03"
                                        name="shareholders_verification_03"
                                        style="display: none"><input
                                        type="file"
                                        id="shareholders_verification_file_04"
                                        name="shareholders_verification_04"
                                        style="display: none">
                                </div>
                            </div>
                             <?php if($cases_verification && $cases_verification->status == 3){?>
                             <div class="ym-grid">
                                <div class="ym-gl ym-g60"><?php language_e('cases_view_verification_company_hard_CommentForClevverMailVerification'); ?>: </div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g60">
                                    <textarea rows="6" cols="6" class="input-txt" name="comment_for_registration_content"></textarea>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                    <input type="hidden" id="case_id" name="case_id"
                        value="<?php echo $case_id;?>" />

                    <input type="hidden" id="check_resubmit" name="check_resubmit" value="1" />

                </form>
            </div>
        </div>
        <div class="ym-grid">
            <div class="ym-gl ym-g100">
                <a class="input-btn" id="submitButton"
                    href="<?php echo base_url()?>cases/verification/company_verify"><?php language_e('cases_view_verification_company_hard_IHaveCompletedThisVerification'); ?>...</a><a href="#"
                    id="backBtn"><?php language_e('cases_view_verification_company_hard_Back'); ?></a>
            </div>
        </div>
    </div>
</div>
<div class="hide" style="display: none;">
    <a id="view_verification_file" class="iframe"><?php language_e('cases_view_verification_company_hard_PreviewFile'); ?></a>
</div>

<div class="hide">
    <div id="reSubmitCompanyHardWindow" title="Confirm Submit Verification" class="input-form dialog-form"></div>
</div>

<script type="text/javascript">
$(function(){
    $('#submitButton').click(function() {
        var submitUrl = '<?php echo base_url()?>cases/verification/verification_company_identification_hard';
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'compHardVerificationForm',
            success: function(obj) {
                if (obj.status) {
                    $.infor({
                        message: obj.message,
                        ok:function(){
                            // de hien thi case duoc chon trong man hinh your case.
                            document.location.href = '<?php echo base_url()?>cases?product_id=5&case_id=<?php echo $case_id;?>';
                        }
                    });
                } else {
                    console.log("Respondata: "+JSON.stringify(obj));
                    // Output error shareholders
                    if(obj.data.code=="1"){
                        $.each( obj.data.message, function( key, value ){
                            $("#compHardVerificationForm").find("[name='" + key + "']").addClass("input-error").attr("title",value);
                        });
                        $("#compHardVerificationForm").find(".input-error").tipsy({gravity: 'sw'});
                        $.displayError(obj.message);
                        return;
                    }
                    else if(obj.data.code=="0") { // Output error shareholders when submit
                        if(obj.data.code_status=="0"){
                            $.each( obj.data.message, function( key, value ){
                                $("#compHardVerificationForm").find("[name='" + key + "']").addClass("input-error").attr("title",value);
                            });
                            $("#compHardVerificationForm").find(".input-error").tipsy({gravity: 'sw'});
                            $.displayError(obj.message);
                            return;
                        }
                        // resbumit
                        $("#reSubmitCompanyHardWindow").html("<p style='color: #d14b4b;font-weight: bold; margin-top: 16px;'>"+obj.data.message+"</p>");
                        $('#reSubmitCompanyHardWindow').openDialog({
                            autoOpen: false,
                            height: 200,
                            width: 500,
                            modal: false,
                            open: function () {},
                            buttons: {
                                'Cancel and correct data': function () {
                                    $(this).dialog('close');
                                },
                                'Re-submit with same data': function () {
                                    $(this).dialog('close');
                                    resubmitVerification();
                                }
                            }
                        });
                        $('#reSubmitCompanyHardWindow').dialog('option', 'position', 'center');
                        $('#reSubmitCompanyHardWindow').dialog('open');
                        return;
                    }
                    else{

                        $.displayError(obj.message);
                        return;
                    }
                    //$.displayError(data.message);
                }
            }
        });
        return false;
    });

    $("#passport_verification_btn").click(function(event){
        $("#passport_verification_file").click();
        return false;
    });

    $("#shareholders_browse_btn_01").click(function(event){
        $("#shareholders_verification_file_01").click();
        return false;
    });

    $("#shareholders_browse_btn_02").click(function(event){
        $("#shareholders_verification_file_02").click();
        return false;
    });

    $("#shareholders_browse_btn_03").click(function(event){
        $("#shareholders_verification_file_03").click();
        return false;
    });

    $("#shareholders_browse_btn_04").click(function(event){
        $("#shareholders_verification_file_04").click();
        return false;
    });

    $('#shareholders_verification_file_01').change(function(click) {
        $('#shareholders_file_name_txt_01').val($( this ).val().split('\\').pop());
        myfile= $( this ).val();
        var ext = myfile.split('.').pop();
        if((ext.toUpperCase() != "PDF")
                && (ext.toUpperCase() != "JPG")
                && (ext.toUpperCase() != "TIF")
                && (ext.toUpperCase() != "BMP")
                && (ext.toUpperCase() != "PNG")){
           $.displayError('Please select pdf file to upload.');
           $('#shareholders_file_name_txt_01').val('');
            return;
        }

        $('#shareholders_file_name_change_01').val('1');
        // Upload data here
        $.ajaxFileUpload({
            id: 'shareholders_verification_file_01',
            data: {
                case_id: '<?php echo $case_id; ?>',
                input_file_client_name: 'shareholders_verification_01'
            },
            url: '<?php echo base_url()?>cases/verification/company_hard_upload_file_shareholder',
            resetFileValue:true,
            success: function(obj) {
                $('#shareholders_file_name_txt_01').val($("#shareholders_verification_file_01").val().split('\\').pop());
                $('#shareholders_browse_btn_01').hide();
                $('#shareholders_01_delete_btn').show();
                $('#shareholders_view_btn_01').addClass('upload-success');
                $('#shareholders_file_name_txt_01').removeClass("input-error");
            },
            error: function(obj) {
                $('#shareholders_file_name_txt_01').val('');
            }
        });
        return;
    });

    $("#shareholders_01_delete_btn").click(function() {
        $.ajaxExec({
            url: '<?php echo base_url() ?>cases/verification/delete_company_hard_file_shareholder',
            data: { case_id: '<?php echo $case_id; ?>' , file_type: 'shareholders_01' },
            success: function(data) {
                if (data.status) {
                    $('#shareholders_browse_btn_01').show();
                    $('#shareholders_01_delete_btn').hide();
                    $('#shareholders_view_btn_01').removeClass('upload-success');
                    $('#shareholders_file_name_change_01').val('0');
                    $('#shareholders_file_name_txt_01').val('');
                } else {
                    console.log('Failed to delete additional_verification_file');
                }
            }
        });
        return false;
    });

    if( $('#shareholders_file_name_txt_01').val() != ''){
        $('#shareholders_browse_btn_01').hide();
        $('#shareholders_01_delete_btn').show();
     }
    $('#shareholders_1_name').change(function(click){
       checkNameForRemoveClass($(this));
    });

     $('#shareholders_1_rate').change(function(click){
       checkRateForRemoveClass($(this));
    });

    $('#shareholders_verification_file_02').change(function(click) {
        $('#shareholders_file_name_txt_02').val($( this ).val().split('\\').pop());
        myfile= $( this ).val();
        var ext = myfile.split('.').pop();
        if((ext.toUpperCase() != "PDF")
                && (ext.toUpperCase() != "JPG")
                && (ext.toUpperCase() != "TIF")
                && (ext.toUpperCase() != "BMP")
                && (ext.toUpperCase() != "PNG")){
           $.displayError('Please select pdf file to upload.');
           $('#shareholders_file_name_txt_02').val('');
            return;
        }

        // Upload data here
        $.ajaxFileUpload({
            id: 'shareholders_verification_file_02',
            data: {
                case_id: '<?php echo $case_id; ?>',
                input_file_client_name: 'shareholders_verification_02'
            },
            url: '<?php echo base_url()?>cases/verification/company_hard_upload_file_shareholder',
            resetFileValue:true,
            success: function(obj) {
                $('#shareholders_file_name_txt_02').val($("#shareholders_verification_file_02").val().split('\\').pop());
                $('#shareholders_browse_btn_02').hide();
                $('#shareholders_02_delete_btn').show();
                $('#shareholders_view_btn_02').addClass('upload-success');
                $('#shareholders_file_name_txt_02').removeClass("input-error");
            },
            error: function(obj) {
                $('#shareholders_file_name_txt_02').val('');
            }
        });
        return;
    });


    $("#shareholders_02_delete_btn").click(function() {
        $.ajaxExec({
            url: '<?php echo base_url()?>cases/verification/delete_company_hard_file_shareholder',
            data: { case_id: '<?php echo $case_id; ?>' , file_type: 'shareholders_02' },
            success: function(data) {
                if (data.status) {
                    $('#shareholders_browse_btn_02').show();
                    $('#shareholders_02_delete_btn').hide();
                    $('#shareholders_view_btn_02').removeClass('upload-success');
                    $('#shareholders_file_name_change_02').val('0');
                    $('#shareholders_file_name_txt_02').val('');
                } else {
                    console.log('Failed to delete additional_verification_file');
                }
            }
        });
        return false;
    });

    if( $('#shareholders_file_name_txt_02').val() != ''){
        $('#shareholders_browse_btn_02').hide();
        $('#shareholders_02_delete_btn').show();
    }

    $('#shareholders_2_name').change(function(click){
       checkNameForRemoveClass($(this));
    });

     $('#shareholders_2_rate').change(function(click){
       checkRateForRemoveClass($(this));
    });

    $('#shareholders_verification_file_03').change(function(click) {
        $('#shareholders_file_name_txt_03').val($( this ).val().split('\\').pop());
        myfile= $( this ).val();
        var ext = myfile.split('.').pop();
        if((ext.toUpperCase() != "PDF")
                && (ext.toUpperCase() != "JPG")
                && (ext.toUpperCase() != "TIF")
                && (ext.toUpperCase() != "BMP")
                && (ext.toUpperCase() != "PNG")){
           $.displayError('Please select pdf file to upload.');
           $('#shareholders_file_name_txt_03').val('');
            return;
        }
        $('#shareholders_file_name_change_03').val('1');
        // Upload data here
        $.ajaxFileUpload({
            id: 'shareholders_verification_file_03',
            data: {
                case_id: '<?php echo $case_id; ?>',
                input_file_client_name: 'shareholders_verification_03'
            },
            url: '<?php echo base_url()?>cases/verification/company_hard_upload_file_shareholder',
            resetFileValue:true,
            success: function(obj) {
                $('#shareholders_file_name_txt_03').val($("#shareholders_verification_file_03").val().split('\\').pop());
                $('#shareholders_browse_btn_03').hide();
                $('#shareholders_03_delete_btn').show();
                $('#shareholders_view_btn_03').addClass('upload-success');
                 $('#shareholders_file_name_txt_03').removeClass("input-error");
            },
            error: function(obj) {
                $('#shareholders_file_name_txt_03').val('');
            }
        });
        return;
    });

   $("#shareholders_03_delete_btn").click(function() {
        $.ajaxExec({
            url: '<?php echo base_url()?>cases/verification/delete_company_hard_file_shareholder',
            data: { case_id: '<?php echo $case_id; ?>' , file_type: 'shareholders_03' },
            success: function(data) {
                if (data.status) {
                    $('#shareholders_browse_btn_03').show();
                    $('#shareholders_03_delete_btn').hide();
                    $('#shareholders_view_btn_03').removeClass('upload-success');
                    $('#shareholders_file_name_change_03').val('0');
                    $('#shareholders_file_name_txt_03').val('');
                } else {
                    console.log('Failed to delete additional_verification_file');
                }
            }
        });
        return false;
    });

    if( $('#shareholders_file_name_txt_03').val() != ''){
        $('#shareholders_browse_btn_03').hide();
        $('#shareholders_03_delete_btn').show();
    }

    $('#shareholders_3_name').change(function(click){
       checkNameForRemoveClass($(this));
    });

     $('#shareholders_3_rate').change(function(click){
       checkRateForRemoveClass($(this));
    });

    $('#shareholders_verification_file_04').change(function(click) {
        $('#shareholders_file_name_txt_04').val($( this ).val().split('\\').pop());
        myfile= $( this ).val();
        var ext = myfile.split('.').pop();
        if((ext.toUpperCase() != "PDF")
                && (ext.toUpperCase() != "JPG")
                && (ext.toUpperCase() != "TIF")
                && (ext.toUpperCase() != "BMP")
                && (ext.toUpperCase() != "PNG")){
           $.displayError('Please select pdf file to upload.');
           $('#shareholders_file_name_txt_04').val('');
            return;
        }
        $('#shareholders_file_name_change_04').val('1');
        // Upload data here
        $.ajaxFileUpload({
            id: 'shareholders_verification_file_04',
            data: {
                case_id: '<?php echo $case_id; ?>',
                input_file_client_name: 'shareholders_verification_04'
            },
            url: '<?php echo base_url()?>cases/verification/company_hard_upload_file_shareholder',
            resetFileValue:true,
            success: function(obj) {
                $('#shareholders_file_name_txt_04').val($("#shareholders_verification_file_04").val().split('\\').pop());
                $('#shareholders_browse_btn_04').hide();
                $('#shareholders_04_delete_btn').show();
                $('#shareholders_view_btn_04').addClass('upload-success');
                $('#shareholders_file_name_txt_04').removeClass("input-error");
            },
            error: function(obj) {
                $('#shareholders_file_name_txt_04').val('');
            }
        });
        return;
    });


    $("#shareholders_04_delete_btn").click(function() {
        $.ajaxExec({
            url: '<?php echo base_url()?>cases/verification/delete_company_hard_file_shareholder',
            data: { case_id: '<?php echo $case_id; ?>' , file_type: 'shareholders_04' },
            success: function(data) {
                if (data.status) {
                    $('#shareholders_browse_btn_04').show();
                    $('#shareholders_04_delete_btn').hide();
                    $('#shareholders_view_btn_04').removeClass('upload-success');
                    $('#shareholders_file_name_change_04').val('0');
                    $('#shareholders_file_name_txt_04').val('');
                } else {
                    console.log('Failed to delete additional_verification_file');
                }
            }
        });
        return false;
    });

     if( $('#shareholders_file_name_txt_04').val() != ''){
        $('#shareholders_browse_btn_04').hide();
        $('#shareholders_04_delete_btn').show();
    }

    $('#shareholders_4_name').change(function(click){
       checkNameForRemoveClass($(this));
    });

     $('#shareholders_4_rate').change(function(click){
       checkRateForRemoveClass($(this));
    });

    /* Check name for remove input-error class*/
    function checkNameForRemoveClass(id){
       var name =  id.val();
       if( name != ''){
        id.removeClass("input-error");
       }
    }

     /* Check rate for remove input-error class*/
    function checkRateForRemoveClass(id){
       var rate =  id.val();
       if( rate != ''){
        id.removeClass("input-error");
       }
    }

    $('#passport_verification_file').change(function(click) {
        myfile= $( this ).val();
         var ext = myfile.split('.').pop();
         if((ext.toUpperCase() != "PDF")
                 && (ext.toUpperCase() != "JPG")
                 && (ext.toUpperCase() != "TIF")
                 && (ext.toUpperCase() != "BMP")
                 && (ext.toUpperCase() != "PNG")){
            $.displayError('Please select pdf file to upload.');
             return;
         }

        $("#passport_verification_change").val(1);
        // Upload data here
        $.ajaxFileUpload({
            id: 'passport_verification_file',
            data: {
                case_id: '<?php echo $case_id; ?>',
                input_file_client_name: 'business_registration_verification'
            },
            url: '<?php echo base_url()?>cases/verification/company_hard_upload_file',
            resetFileValue:true,
            success: function(obj) {
                $('#passport_verification_txt').val($("#passport_verification_file").val().split('\\').pop());
                $('#passport_view_btn').addClass('upload-success');
            }
        });
      });

    $("#passport_view_btn").click(function(){
        $('#view_verification_file').attr('href',"<?php echo base_url()?>cases/verification/comp_hard_view_file?case_id=<?php echo $case_id?>&op=00");
        $('#view_verification_file').click();
        return false;
    });

    $("#shareholders_view_btn_01").click(function(){
        $('#view_verification_file').attr('href',"<?php echo base_url()?>cases/verification/comp_hard_view_file?case_id=<?php echo $case_id?>&op=01");
        $('#view_verification_file').click();
        return false;
    });

    $("#shareholders_view_btn_02").click(function(){
        $('#view_verification_file').attr('href',"<?php echo base_url()?>cases/verification/comp_hard_view_file?case_id=<?php echo $case_id?>&op=02");
        $('#view_verification_file').click();
        return false;
    });

    $("#shareholders_view_btn_03").click(function(){
        $('#view_verification_file').attr('href',"<?php echo base_url()?>cases/verification/comp_hard_view_file?case_id=<?php echo $case_id?>&op=03");
        $('#view_verification_file').click();
        return false;
    });

    $("#shareholders_view_btn_04").click(function(){
        $('#view_verification_file').attr('href',"<?php echo base_url()?>cases/verification/comp_hard_view_file?case_id=<?php echo $case_id?>&op=04");
        $('#view_verification_file').click();
        return false;
    });

    $('#view_verification_file').fancybox({
        width: 1000,
        height: 800
    });

    $("#backBtn").click(function(){
        history.back(-1);
        return  false;
    });

    $(".bd-content").slimScroll({height:($(window).height() - 338)+'px'});
});

function resubmitVerification(){

    $("#check_resubmit").val(0);

    var submitUrl = '<?php echo base_url()?>cases/verification/verification_company_identification_hard';
    $.ajaxSubmit({
        url: submitUrl,
        formId: 'compHardVerificationForm',
        success: function(data) {

            if (data.status) {
                $.infor({
                    message: data.message,
                    ok:function(){
                        // de hien thi case duoc chon trong man hinh your case.
                        document.location.href = '<?php echo base_url()?>cases?product_id=5&case_id=<?php echo $case_id;?>';
                    }
                });
            } else {

                if(data.message.code=="1"){
                    $.displayError(data.message.message);
                }
                else {
                     $.displayError(data.message);
                }
            }
        }
    });
    return false;

}

</script>