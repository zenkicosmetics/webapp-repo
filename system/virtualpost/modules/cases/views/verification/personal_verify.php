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

.upload-success {
    color: #fff;
    background: #336699;
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
            <h2 style="font-size: 20px; margin-bottom: 10px"><?php language_e('cases_view_verification_personal_verify_VerificationsRequired'); ?>:</h2>
        </div>
        <div class="ym-grid">
            <div class="ym-gl ym-g80 bd">
                <div class="ym-grid">
                    <div class="bd-header"><?php language_e('cases_view_verification_personal_verify_PersonalIdentification'); ?></div>
                </div>
                <form id="personalVerificationForm" action="#" method="post">
                    <div class="ym-grid">
                        <div class="ym-gl ym-g100 bd-content">
                            <div class="ym-grid">
                                <div class="ym-gl ym-g80"><?php
                                    $type = $is_invoicing_address_verification? "invoicing" : "postbox";
                                    language_e('cases_view_verification_personal_verify_YourNameAsEnteredInYour', ['type' => $type]);
                                ?>:</div>
                            </div>
                            <?php if($is_invoicing_address_verification):?>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g80">
                                    <?php if (!empty($customer_addresses->invoicing_address_name)) {?>
                                    <div class="description">
                                        <strong><?php echo $customer_addresses->invoicing_address_name?></strong><a href="<?php echo base_url()?>addresses" class="main_link_color">change</a>
                                    </div>
                                    <?php } ?>
                                    <?php if (empty($customer_addresses->invoicing_company) && $is_invoicing_address_verification) {?>
                                    <div class="description">
                                        <strong><?php echo $customer_addresses->invoicing_street?></strong><a href="<?php echo base_url()?>addresses" class="main_link_color">change</a>
                                    </div>
                                    <div class="description">
                                        <strong><?php echo $customer_addresses->invoicing_postcode?></strong><a href="<?php echo base_url()?>addresses" class="main_link_color">change</a>
                                    </div>
                                    <div class="description">
                                        <strong><?php echo $customer_addresses->invoicing_city?></strong><a href="<?php echo base_url()?>addresses" class="main_link_color">change</a>
                                    </div>
                                    <div class="description">
                                        <strong><?php echo $customer_addresses->invoicing_region?></strong><a href="<?php echo base_url()?>addresses" class="main_link_color">change</a>
                                    </div>
                                    <div class="description">
                                        <strong><?php echo $customer_addresses->invoicing_country_name?></strong><a href="<?php echo base_url()?>addresses" class="main_link_color">change</a>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php else:?>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g80">
                                    <?php if (!empty($postbox->name)) {?>
                                    <div class="description">
                                        <strong><?php echo $postbox->name?></strong><a href="<?php echo base_url()?>addresses" class="main_link_color">change</a>
                                    </div>
                                    <?php } ?>
                                    <?php if (!empty($postbox->company)) {?>
                                    <div class="description">
                                        <strong><?php echo $postbox->company?></strong><a href="<?php echo base_url()?>addresses" class="main_link_color">change</a>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php endif;?>
                            <?php if (!empty($customer_addresses->invoicing_company)) {?>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g80">
                                    <div style="width: 15px; float: left;">
                                        <input type="checkbox" id="confirm_check_box">
                                    </div>
                                    <div class="description" style="margin-top: 0px !important;"><?php
                                    $type = $is_invoicing_address_verification ? "invoicing" : "postbox";
                                    language_e('cases_view_verification_personal_verify_IHerebyConfirmThatICanEnterInt', ['type' => $type]); ?>:</div>
                                </div>
                            </div>
                            <?php if($is_invoicing_address_verification):?>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g80">
                                    <div class="description">
                                        <strong><?php echo $customer_addresses->invoicing_company?></strong><a href="<?php echo base_url()?>addresses" class="main_link_color">change</a>
                                    </div>
                                    <div class="description">
                                        <strong><?php echo $customer_addresses->invoicing_street?></strong><a href="<?php echo base_url()?>addresses" class="main_link_color">change</a>
                                    </div>
                                    <div class="description">
                                        <strong><?php echo $customer_addresses->invoicing_postcode?></strong><a href="<?php echo base_url()?>addresses" class="main_link_color">change</a>
                                    </div>
                                    <div class="description">
                                        <strong><?php echo $customer_addresses->invoicing_city?></strong><a href="<?php echo base_url()?>addresses" class="main_link_color">change</a>
                                    </div>
                                    <div class="description">
                                        <strong><?php echo $customer_addresses->invoicing_region?></strong><a href="<?php echo base_url()?>addresses" class="main_link_color">change</a>
                                    </div>
                                    <div class="description">
                                        <strong><?php echo $customer_addresses->invoicing_country_name?></strong><a href="<?php echo base_url()?>addresses" class="main_link_color">change</a>
                                    </div>
                                </div>
                            </div>
                            <?php endif;?>
                            <?php } ?>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g80">
                                <?php language_e('cases_view_verification_personal_verify_PleaseUploadTwoPersonalIdentif'); ?>
                                </div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g80">
                                    <input type="file" id="passport_verification_file" name="personal_verification" style="display: none">
                                    <input type="text" name="passport_verification_txt"
                                        value="<?php echo empty($cases_verification)?"": basename($cases_verification->verification_local_file_path)?>"
                                        id="passport_verification_txt" class="input-txt" style="width: 300px; margin-left: 0px !important;">
                                        <input type="hidden" value="0" id="passport_verification_change" name="passport_verification_change" class="input-txt">
                                    <button id="passport_verification_btn">Upload</button>
                                    <button id="passport_view_btn"
                                        class="<?php echo empty($cases_verification)||empty($cases_verification->verification_local_file_path)?"":"upload-success" ?>">View</button>
                                </div>
                            </div>

                            <div class="ym-grid">
                                <div class="ym-gl ym-g80">
                                    <input type="file" id="driver_license_file" name="driver_license_file" style="display: none">
                                    <input type="text" name="driver_license_file_txt"
                                        value="<?php echo empty($cases_verification)?"": basename($cases_verification->driver_license_document_local_file_path)?>"
                                        id="driver_license_file_txt" class="input-txt" style="width: 300px; margin-left: 0px !important;">
                                    <input type="hidden" value="0" id="driver_license_file_change" class="input-txt" name="driver_license_file_change">
                                    <button id="driver_license_file_btn">Upload</button>
                                    <button id="driver_license_view_btn"
                                        class="<?php echo empty($cases_verification)||empty($cases_verification->driver_license_document_local_file_path)?"":"upload-success" ?>">View</button>
                                </div>
                            </div>
                             <?php if($cases_verification && $cases_verification->status == 3){?>
                             <div class="ym-grid">
                                <div class="ym-gl ym-g60"><?php language_e('cases_view_verification_personal_verify_CommentForRegistration'); ?></div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g60">
                                    <textarea rows="6" cols="6" class="input-txt" name="comment_for_registration_content"></textarea>
                                </div>
                            </div>
                            <?php } ?>

                            <div class="ym-grid">
                                <div class="ym-gl ym-g80"></div>
                            </div>
                        </div>
                        <input type="hidden" id="case_id" name="case_id" value="<?php echo $case_id;?>" />
                        <input type="hidden" id="check_resubmit" name="check_resubmit" value="1" />
                    </div>
                </form>
            </div>
        </div>
        <div class="ym-grid">
            <div class="ym-gl ym-g100">
                <a class="input-btn" id="submitButton" href="<?php echo base_url()?>cases/verification/personal_verify"><?php language_e('cases_view_verification_personal_verify_IHaveCompletedThisVerification'); ?></a><a
                    href="#" id="backBtn">Back</a>
            </div>
        </div>
    </div>
</div>
<div class="hide" style="display: none;">
    <a id="view_verification_file" class="iframe" href="<?php echo base_url()?>cases/verification/comp_soft_view_file?type=1&case_id=<?php echo $case_id?>">
        Preview file</a> <a id="view_driver_license_file" class="iframe"
        href="<?php echo base_url()?>cases/verification/comp_soft_view_file?type=1&op=08&case_id=<?php echo $case_id?>"> <?php language_e('cases_view_verification_personal_verify_PreviewFile'); ?></a>
</div>

<div class="hide">
    <div id="reSubmitPersonVerifyWindow" title="Confirm Submit Verification" class="input-form dialog-form"></div>
</div>

<script type="text/javascript">
$(function(){
    /**
     * When user click submit button
     */
    $('#submitButton').click(function() {
        <?php if (!empty($customer_addresses->invoicing_company)) {?>

        if($("#confirm_check_box").is(":checked")){

        <?php }?>

            var submitUrl = '<?php echo base_url()?>cases/verification/verification_personal_identification';

            $.ajaxSubmit({

                url: submitUrl,
                formId: 'personalVerificationForm',
                success: function(obj) {

                    if (obj.status) {
                        $.infor({
                            message: obj.message,
                            ok:function(){
                                document.location.href = '<?php echo base_url()?>cases?product_id=5&case_id=<?php echo $case_id;?>';

                            }
                        });
                    } else {

                        console.log("Response: "+JSON.stringify(obj));

                        $.each( obj.data.message, function( key, value ){
                            $("#personalVerificationForm").find("[name='" + key + "']").addClass("input-error").attr("title",value);
                        });
                        $("#personalVerificationForm").find(".input-error").tipsy({gravity: 'sw'});
                        if(obj.data.code=="1"){

                            $.displayError(obj.message);
                        }
                        else if(obj.data.code=="0") {

                            $("#reSubmitPersonVerifyWindow").html("<p style='color: #d14b4b;font-weight: bold; margin-top: 16px;'>"+obj.data.message+"</p>");
                            $('#reSubmitPersonVerifyWindow').openDialog({
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
                            $('#reSubmitPersonVerifyWindow').dialog('option', 'position', 'center');
                            $('#reSubmitPersonVerifyWindow').dialog('open');
                        }
                        else {
                            $.displayError(obj.message);
                        }
                        return;

                    }
                }
            });
        <?php if (!empty($customer_addresses->invoicing_company)) {?>
        }else{
            $.displayError("Please click confirm check box.");
        }
        <?php }?>
        return false;
    });

    $("#passport_verification_btn").click(function(event){
        $("#passport_verification_file").click();
        //$('#passport_verification_txt').val('');
        return false;
    });

    $('#passport_verification_file').change(function(click) {
        myfile= $( this ).val();
        var ext = myfile.split('.').pop();
        if((ext.toUpperCase() != "PDF")
                && (ext.toUpperCase() != "JPG")
                && (ext.toUpperCase() != "TIF")
                && (ext.toUpperCase() != "BMP")
                && (ext.toUpperCase() != "PNG")){
           $.displayError("<?php language_e('cases_view_verification_personal_verify_PleaseSelectPDFJPGTIFBMPPNGFil'); ?>");
            return;
        }

        $("#passport_verification_change").val(1);
       // Upload data here
       $.ajaxFileUpload({
           id: 'passport_verification_file',
           data: {
               case_id: '<?php echo $case_id; ?>',
               type: '1',
               input_file_client_name: 'personal_verification'
           },
           url: '<?php echo base_url()?>cases/verification/company_soft_upload_file',
           resetFileValue:true,
           success: function(obj) {
               $('#passport_verification_txt').val($("#passport_verification_file").val().split('\\').pop());
               $('#passport_view_btn').addClass('upload-success');
           }
       });
    });

    $("#passport_view_btn").click(function(){
        $('#view_verification_file').click();
        return false;
    });

    $('#view_verification_file').fancybox({
        width: 1000,
        height: 800
    });

    // Upload driver license file.
    $("#driver_license_file_btn").click(function(event){
        $("#driver_license_file").click();
        return false;
    });

    $('#driver_license_file').change(function(click) {
        myfile= $( this ).val();
        var ext = myfile.split('.').pop();
        if((ext.toUpperCase() != "PDF")
                && (ext.toUpperCase() != "JPG")
                && (ext.toUpperCase() != "TIF")
                && (ext.toUpperCase() != "BMP")
                && (ext.toUpperCase() != "PNG")){
           $.displayError("<?php language_e('cases_view_verification_personal_verify_PleaseSelectPDFJPGTIFBMPPNGFil'); ?>");
            return;
        }
        $("#driver_license_file_change").val(1);
       // Upload data here
       $.ajaxFileUpload({
           id: 'driver_license_file',
           data: {
               case_id: '<?php echo $case_id; ?>',
               type: '1',
               input_file_client_name: 'driver_license_file'
           },
           url: '<?php echo base_url()?>cases/verification/company_soft_upload_file',
           resetFileValue:true,
           success: function(obj) {
               $('#driver_license_file_txt').val($("#driver_license_file").val().split('\\').pop());
               $('#driver_license_view_btn').addClass('upload-success');
           }
       });
    });

    $("#driver_license_view_btn").click(function(){
        $('#view_driver_license_file').click();
        return false;
    });

    $('#view_driver_license_file').fancybox({
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

    var submitUrl = '<?php echo base_url()?>cases/verification/verification_personal_identification';

    $.ajaxSubmit({

        url: submitUrl,
        formId: 'personalVerificationForm',
        success: function(data) {

            if (data.status) {
                $.infor({
                    message: data.message,
                    ok:function(){
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

}

</script>