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
            <h2 style="font-size: 20px; margin-bottom: 10px"><?php language_e('cases_view_verification_company_soft_VerificationsRequired'); ?>:</h2>
        </div>
        <div class="ym-grid">
            <div class="bd">
                <div class="ym-grid">
                    <div class="ym-gl ym-g80 bd-header"><?php language_e('cases_view_verification_company_soft_CompanyIdentification'); ?></div>
                </div>
                <form id="personalVerificationForm" action="#"
                    method="post">
                    <div class="ym-grid">
                        <div class="ym-gl ym-g100 bd-content">
                            <div class="ym-grid">
                                <div class="ym-gl ym-g80"><?php
                                $type = $is_invoicing_address_verification ? "invoicing" : "postbox";
                                    language_e('cases_view_verification_company_soft_YourCompanyNameAndAddressMsg', ['type' => $type]);
                                ?>:</div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g80">
                                    <div class="description">
                                        <strong><?php echo $customer_addresses->invoicing_company?></strong><a class="main_link_color"
                                            href="<?php echo base_url()?>addresses"><?php language_e('cases_view_verification_company_soft_Change'); ?></a>
                                    </div>
                                    <div class="description">
                                        <strong><?php echo $customer_addresses->invoicing_street?></strong><a class="main_link_color"
                                            href="<?php echo base_url()?>addresses"><?php language_e('cases_view_verification_company_soft_Change'); ?></a>
                                    </div>
                                    <div class="description">
                                        <strong><?php echo $customer_addresses->invoicing_postcode?></strong><a class="main_link_color"
                                            href="<?php echo base_url()?>addresses"><?php language_e('cases_view_verification_company_soft_Change'); ?></a>
                                    </div>
                                    <div class="description">
                                        <strong><?php echo $customer_addresses->invoicing_city?></strong><a class="main_link_color"
                                            href="<?php echo base_url()?>addresses"><?php language_e('cases_view_verification_company_soft_Change'); ?></a>
                                    </div>
                                    <div class="description">
                                        <strong><?php echo $customer_addresses->invoicing_region?></strong><a class="main_link_color"
                                            href="<?php echo base_url()?>addresses"><?php language_e('cases_view_verification_company_soft_Change'); ?></a>
                                    </div>
                                    <div class="description">
                                        <strong><?php echo $customer_addresses->invoicing_country_name?></strong><a class="main_link_color"
                                            href="<?php echo base_url()?>addresses"><?php language_e('cases_view_verification_company_soft_Change'); ?></a>
                                    </div>
                                </div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g80"><?php language_e('cases_view_verification_company_soft_PleaseUploadMsg'); ?></div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g80">
                                    <input type="file"
                                        id="passport_verification_file"
                                        name="business_registration_verification"
                                        style="display: none"> <input
                                        type="text"
                                        value="<?php echo empty($cases_verification)?"": basename($cases_verification->verification_local_file_path)?>"
                                        id="passport_verification_txt" name="passport_verification_txt"
                                        class="input-txt"
                                        style="width: 300px; margin-left: 0px !important;">
                                    <button
                                        id="passport_verification_btn">Upload</button>
                                    <button id="passport_view_btn"
                                        class="<?php echo empty($cases_verification)||empty($cases_verification->verification_local_file_path)?"":"upload-success" ?>">View</button>
                                </div>
                            </div>
                             <?php if($cases_verification && $cases_verification->status == 3){?>
                             <div class="ym-grid">
                                <div class="ym-gl ym-g60"><?php language_e('cases_view_verification_company_soft_CommentForRegistration'); ?></div>
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
                    href="<?php echo base_url()?>cases/verification/company_verify"><?php language_e('cases_view_verification_company_soft_IHaveCompletedThisVerification'); ?>...</a><a href="#"
                    id="backBtn"><?php language_e('cases_view_verification_company_soft_Back'); ?></a>
            </div>
        </div>
    </div>
</div>
<div class="hide" style="display: none;">
    <a id="view_verification_file" class="iframe"
        href="<?php echo base_url()?>cases/verification/comp_soft_view_file?type=2&case_id=<?php echo $case_id?>">
        <?php language_e('cases_view_verification_company_soft_PreviewFile'); ?></a>
</div>

<div class="hide">
    <div id="reSubmitCompanySoftWindow" title="Confirm Submit Verification" class="input-form dialog-form"></div>
</div>

<script type="text/javascript">
$(function(){
    /**
     * When user click submit button
     */
    $('#submitButton').click(function() {
        var submitUrl = '<?php echo base_url()?>cases/verification/verification_company_identification_soft';
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'personalVerificationForm',
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
                    if(data.data.code=="1"){
                         $.each( data.data.message, function( key, value ){
                             $("#personalVerificationForm").find("[name='" + key + "']").addClass("input-error").attr("title",value);
                         });
                         $.displayError(data.message);
                         return;
                    }else if (data.data.code=="0"){
                         $("#reSubmitCompanySoftWindow").html("<p style='color: #d14b4b;font-weight: bold; margin-top: 16px;'>"+data.message+"</p>");
                         $('#reSubmitCompanySoftWindow').openDialog({
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
                         $('#reSubmitCompanySoftWindow').dialog('option', 'position', 'center');
                         $('#reSubmitCompanySoftWindow').dialog('open');
                         return;
                     } else{

                         $.displayError(data.message);
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

    $('#passport_verification_file').change(function(click) {
        myfile= $( this ).val();
        var ext = myfile.split('.').pop();
        if((ext.toUpperCase() != "PDF")
                && (ext.toUpperCase() != "JPG")
                && (ext.toUpperCase() != "TIF")
                && (ext.toUpperCase() != "BMP")
                && (ext.toUpperCase() != "PNG")){
           $.displayError('Please select PDF, JPG, TIF, BMP, PNG file to upload.');
            return;
        }

        // Upload data here
        $.ajaxFileUpload({
            id: 'passport_verification_file',
            data: {
                case_id: '<?php echo $case_id; ?>',
                type: '2',
                input_file_client_name: 'business_registration_verification'
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

    $("#backBtn").click(function(){
        history.back(-1);
        return  false;
    });

    $(".bd-content").slimScroll({height:($(window).height() - 338)+'px'});
});


function resubmitVerification(){

    $("#check_resubmit").val(0);

    var submitUrl = '<?php echo base_url()?>cases/verification/verification_company_identification_soft';
    $.ajaxSubmit({
        url: submitUrl,
        formId: 'personalVerificationForm',
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
                $.displayError(data.message);
            }
        }
    });
    return false;

}



</script>