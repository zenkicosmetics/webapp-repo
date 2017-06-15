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
            <h2 style="font-size: 20px; margin-bottom: 10px"><?php language_e('cases_view_verification_tc_contract_VerificationsRequired'); ?></h2>
        </div>
        <div class="ym-grid">
            <div class="ym-gl ym-g80 bd">
                <div class="ym-grid">
                    <div class="bd-header"><?php language_e('cases_view_verification_tc_contract_ContractOfTermsConditions'); ?></div>
                </div>
                <form id="contractVerificationForm" action="#" method="post">
                    <div class="ym-grid">
                        <div class="ym-gl ym-g100 bd-content">
                            <div class="ym-grid">
                                <div class="ym-gl ym-g80"><?php language_e('cases_view_verification_tc_contract_TheCurrentClevverMailTerms'); ?></div>
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
                                    <button type="button" id="btnCreatePdf"><?php language_e('cases_view_verification_tc_contract_CreatePrintablePDF'); ?></button>
                                </div>
                            </div>

                            <div class="ym-grid">
                                <div class="ym-gl ym-g80">
                                    <div>Please</div>
                                    <ol start="1" style='margin-top: 0px'>
                                        <li><?php language_e('cases_view_verification_tc_contract_PrintThisContract'); ?></li>
                                        <li>Sign it</li>
                                        <li>Scan it</li>
                                        <li><?php language_e('cases_view_verification_tc_contract_UploadTheScannedDocumentSignIt'); ?></li>
                                    </ol>
                                </div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g80">
                                    <input type="text" name="tmp_file_name_id"
                                        value="<?php echo empty($case_resource)? "" : basename($case_resource->local_file_path); ?>"
                                        id="tmp_file_name_id" class="input-txt" style="width: 300px; margin-left: 0px !important;" />
                                    <button type='button' id="uploadButton" data-id="<?php echo $case_resource ? $case_resource->id :"";  ?>">Upload</button>
                                    <button type='button' id="viewButton" data-id="<?php echo $case_resource ? $case_resource->id :"";  ?>"
                                        class="<?php echo !empty($case_resource) && !empty($case_resource->local_file_path)?"upload-success" : ""; ?>">View</button>
                                </div>
                            </div>
                            <?php if($case_check && $case_check->status == 3){?>
                             <div class="ym-grid">
                                <div class="ym-gl ym-g60">Comment for ClevverMail verification team: </div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g60">
                                    <textarea rows="6" style="width: 750px" class="input-txt" name="comment_for_registration_content"></textarea>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                        <input type="hidden" id="case_id" name="case_id" value="<?php echo $case_id;?>" />
                    </div>
                </form>
            </div>
        </div>
        <div class="ym-grid">
            <div class="ym-g100" style='width: 84%'>
                <div class='ym-gl'><a href="#" id="backBtn">Back</a></div>
                <div class='ym-gr'><a class="input-btn" id="submitButton"><?php language_e('cases_view_verification_tc_contract_IHaveCompletedThisVerification'); ?></a></div>
            </div>
        </div>
    </div>
</div>


<div style="display: none">
    <a id="view_verification_file" class="iframe"><?php language_e('cases_view_verification_tc_contract_PreviewFile'); ?></a>

    <form method="post">
        <input name="upload_file_input" id="upload_file_input" value="" type="file" />
    </form>
</div>

<script type="text/javascript">
$(document).ready(function(){
    // back button.
    $("#backBtn").click(function(){
        history.back(-1);
        return  false;
    });

    $('#view_verification_file').fancybox({
        width: 1000,
        height: 800
    });

    // slim scroll.
    $(".bd-content").slimScroll({height:($(window).height() - 338)+'px'});

    // upload click button
    $("#uploadButton").live('click',function(e){
        e.preventDefault;

        // do upload function
        $("#upload_file_input").click();

        return false;
    });

    $('#upload_file_input').change(function(e) {
        e.preventDefault();
        myfile= $( this ).val();
        var ext = myfile.split('.').pop();
        if((ext.toUpperCase() != "PDF")
                && (ext.toUpperCase() != "JPG")
                && (ext.toUpperCase() != "TIF")
                && (ext.toUpperCase() != "BMP")
                && (ext.toUpperCase() != "PNG")){
            $.displayError("<?php language_e('cases_view_verification_tc_contract_PleaseSelectPDFJPGTIFBMPPNGFil'); ?>");
            return;
        }
        var time = $.now();

        // Upload data here
        $.ajaxFileUpload({
            id: 'upload_file_input',
            data: {
                case_id: $("#case_id").val(),
                base_taskname : "TC_contract_MS",
                input_file_client_name: "upload_file_input",
                id: $('#viewButton').data('id')
            },
            url: '<?php echo base_url()?>cases/verification/upload_resource?t='+time,
            resetFileValue:true,
            success: function(response) {
                if(response.status){
                    $("#viewButton").addClass('upload-success');
                    $("#viewButton").data('id', response.data.file_id);
                    $("#tmp_file_name_id").val(myfile.split('\\').pop());
                }else{
                    $.displayError(response.message);
                }
            }
        });

        return false;
    });

     /**
     * view all pdf file.
     */
    $("#viewButton").click(function(e){
        e.preventDefault();
        var url = "<?php echo base_url()?>cases/verification/view_resource?file_id="+$(this).data('id');
        $('#view_verification_file').attr('href',url);
        $('#view_verification_file').click();
        return false;
    });


    $("#btnCreatePdf").click(function(e){
        e.preventDefault();

        var submitUrl = '<?php echo base_url()?>cases/verification/create_resource_pdf';
        $.ajaxExec({
            url: submitUrl,
            data: {
                base_taskname: "TC_contract_MS",
                case_id: $("#case_id").val()
            },
            success: function(obj) {
                if (obj.status) {
                    document.location.href = '<?php echo base_url()?>cases/verification/special_file_export';
                } else {
                    $.displayError(obj.message);
                }
            }
        });

        return false;
    });

    // submit button
    $("#submitButton").click(function(e){

        e.preventDefault();

        // defined url
        var submitUrlData = '<?php echo base_url()?>cases/verification/TC_contract_MS';

        // Call ajaxSubmit
        $.ajaxSubmit({
            url: submitUrlData,
            formId: 'contractVerificationForm',
            success: function(response) {

                // if status is true
                if(response.status){

                    //#1328 add message after verification is submited by customer
                    // Popup message after when submitted true
                    $.infor({
                        message: response.message,
                        ok:function(){
                            // Show case in your case of screen
                        	document.location.href = '<?php echo base_url()?>cases/verification/';
                        }
                    });
                }else{
                    // Popup message after when submitted error
                    $.displayError(response.message);

                }// end if-else status

            }// end success

        });// end call ajaxsubmit

        return false;

    });// end click submit's button

});
</script>