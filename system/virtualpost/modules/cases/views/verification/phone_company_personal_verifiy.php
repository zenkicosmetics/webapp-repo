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
            <h2 style="font-size: 20px; margin-bottom: 10px"><?php language_e('cases_view_verification_phone_comp_person_verify_VerificationsRequired'); ?>:</h2>
        </div>
        <div class="ym-grid">
            <div class="ym-gl ym-g80 bd">
                <form id="contractVerificationForm" action="#" method="post">
                    <div class="ym-grid bg-content">
                        <div class="ym-grid">
                            <?php
                            $title = "Personal Identification";
                            if($type == "2"){
                                $title = "Personal Identification of company's authorized representative";
                            }
                            ?>
                            <div class="bd-header"><?php echo $title; ?></div>
                        </div>
                        <br />
                        <div class="ym-grid">
                            <div class="ym-gl ym-g100">
                                <div class="ym-grid">
                                    <div class="ym-gl ym-g80">
                                        <div><?php language_e('cases_view_verification_phone_comp_person_verify_PleaseUploadAPersonalIdentific'); ?></div>
                                        <div><?php language_e('cases_view_verification_phone_comp_person_verify_DrivingLicensePassportNational'); ?></div>
                                    </div>
                                </div>

                                <div id="divPersonalIdentificationContainer">
                                    <?php
                                    if(empty($personal_identification_name)){
                                        $personal_identification_name = array("");
                                    }
                                    ?>
                                    <?php foreach($personal_identification_name as $b){?>
                                    <div class="ym-grid">
                                        <div class="ym-gl ym-g40">
                                            <input type="text" name="personal_identification_name[]" class="input-txt input-file-name personal_identification_name" style="border-color:#a8a8a8; width: 300px;"
                                                   readonly value="<?php echo $b ? basename($b->local_file_path) : ""; ?>" maxlength="100" />
                                            <input type="hidden" name="personal_identification_id[]" value="<?php echo $b ? $b->id : ""; ?>" class="input-txt input-file-id" style="width:50px" />
                                        </div>
                                        <div class="ym-gl ym-g60">
                                            <button class="upload-button" data-id="<?php echo $b ? $b->id : ""; ?>"
                                                    data-op="personal_identification" data-old-data="<?php echo $b ? "1" : ""; ?>">Upload</button>
                                            <button class="<?php echo $b ? "upload-success" : "" ?> view-pdf" data-id="<?php echo $b ? $b->id : ""; ?>" data-op="personal_identification">View</button>
                                            <!-- #1189 New Function: need to add a "remove" icon -->
                                            <button type="button"  class="delete-button" data-id="<?php echo $b ? $b->id : ""; ?>" data-op="personal_identification" >Delete</button>
                                            <input type="hidden" name="personal_identification_ids[]" value="<?php echo $b ? $b->id : ""; ?>" class="input-txt input-file-id persona-file-id"  />
                                        </div>
                                    </div>
                                    <?php }?>
                                </div>
                                <div class="ym-grid">
                                    <div class="ym-gl ym-g66"><a class="ym-gr" href="#" class="main_link_color" id="addPersonalDocument">Add document</a></div>
                                </div>

                            </div>
                        </div>
                        <div class="ym-clearfix"></div>



                        <?php if($type == "2"){ ?>
                        <div class="ym-grid">
                            <div class="bd-header">Company verification</div>
                        </div>
                        <br />

                        <div class="ym-grid">
                            <div class="ym-gl ym-g100 ">
                                <div class="ym-grid">
                                    <div class="ym-gl ym-g80">
                                        <div><?php language_e('cases_view_verification_phone_comp_person_verify_YourCompanyNameAsEnteredInYour'); ?></div>
                                        <div><?php language_e('cases_view_verification_phone_comp_person_verify_PleaseUploadYourBusinessLicens'); ?></div>
                                    </div>
                                </div>

                                <div id="divBusinesssLicenseContainer">
                                    <?php
                                    if(empty($business_licenses)){
                                        $business_licenses = array("");
                                    }
                                    ?>
                                    <?php foreach($business_licenses as $b){?>
                                    <div class="ym-grid">
                                            <div class="ym-gl ym-g40">
                                                <input type="text" name="business_license_name[]" class="input-txt input-file-name business_license_name" style="border-color:#a8a8a8; width: 300px;"
                                                       readonly value="<?php echo $b ? basename($b->local_file_path) : ""; ?>" maxlength="100" />
                                                <input type="hidden" name="business_license_id[]" value="<?php echo $b ? $b->id : ""; ?>" class="input-txt input-file-id" style="width:50px" />
                                            </div>
                                            <div class="ym-gl ym-g60">
                                                <button class="upload-button business-license-name" data-id="<?php echo $b ? $b->id : ""; ?>"
                                                        data-op="business_license" data-old-data="<?php echo $b ? "1" : ""; ?>">Upload</button>
                                                <button class="<?php echo $b ? "upload-success" : "" ?> view-pdf" data-id="<?php echo $b ? $b->id : ""; ?>" data-op="business_license">View</button>
                                                <!-- #1189 New Function: need to add a "remove" icon -->
                                                <button type="button"  class="delete-button" data-id="<?php echo $b ? $b->id : ""; ?>" data-op="business_license" >Delete</button>
                                                <input type="hidden" name="business_license_ids[]" value="<?php echo $b ? $b->id : ""; ?>" class="input-txt input-file-id business-license-file-id"  />
                                            </div>
                                        </div>
                                    <?php }?>
                                </div>
                                <div class="ym-grid">
                                    <div class="ym-gl ym-g66"><a class="ym-gr" href="#" class="main_link_color" id="addBusinessLicenseDocument">Add document</a></div>
                                </div>
                            </div>
                        </div>
                        <?php }?>

                        <!-- input hidden fields. -->
                        <input type="hidden" id="case_id" name="case_id" value="<?php echo $case_id;?>" />
                    </div>
                </form>
            </div>
        </div>
        <div class="ym-grid">
            <div class="ym-g100" style='width: 84%'>
                <div class='ym-gl'><a href="#" id="backBtn">Back</a></div>
                <div class='ym-gr'><a class="input-btn" id="submitButton" ><?php language_e('cases_view_verification_phone_comp_person_verify_IHaveCompletedThisVerification'); ?>...</a></div>
            </div>
        </div>
    </div>
</div>


<div style="display: none">
    <a id="view_verification_file" class="iframe"><?php language_e('cases_view_verification_phone_comp_person_verify_PreviewFile'); ?></a>

    <form method="post">
        <input name="upload_file_input" id="upload_file_input" value="" type="file" />
    </form>

    <!-- peronsal company mock -->
    <div class="personal_identification_mock">
        <div class="ym-grid">
            <div class="ym-gl ym-g40">
                <input type="text" name="personal_identification_name[]" class="input-txt input-file-name personal_identification_name"
                       style="border-color:#a8a8a8; width: 300px;" readonly value="" maxlength="100" />
                <input type="hidden" name="personal_identification_id[]" value="" class="input-txt input-file-id" style="width:50px" />
            </div>
            <div class="ym-gl ym-g60">
                <button class="upload-button" data-id=""
                        data-op="personal_identification" data-old-data="">Upload</button>
                <button class=" view-pdf" data-id="" data-op="personal_identification">View</button>
                <!-- #1189 New Function: need to add a "remove" icon -->
                <button type="button"  class="delete-button" data-id="" data-op="personal_identification" >Delete</button>
                <input type="hidden" name="personal_identification_ids[]" value="" class="input-txt input-file-id persona-file-id"  />
            </div>
        </div>

    </div>

    <!-- business company mock -->
    <div class="business_license_mock">
        <div class="ym-grid">
            <div class="ym-gl ym-g40">
                <input type="text" style="border-color:#a8a8a8; width: 300px;" name="business_license_name[]" class="input-txt input-file-name business_license_name"
                       readonly value="" maxlength="100" />
                <input type="hidden" name="business_license_id[]" value="" class="input-txt input-file-id"/>
            </div>
            <div class="ym-gl ym-g60">
                <button class="upload-button business-license-name" data-id="" data-op="business_license">Upload</button>
                <button class="view-pdf" data-id="" data-op="business_license">View</button>
                <!-- #1189 New Function: need to add a "remove" icon -->
                <button type="button"  class="button-subtract delete-button" data-id="" data-op="business_license">Delete</button>
                <input type="hidden" name="business_license_ids[]" value="" class="input-txt input-file-id business-license-file-id" />
            </div>
        </div>
    </div>
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
    $(".bd-content").slimScroll({height:($(window).height() - 250)+'px'});

    // upload click button
    $("#uploadButton").live('click',function(e){
        e.preventDefault;

        // do upload function
        $("#upload_file_input").click();

        return false;
    });

    $("#addPersonalDocument").click(function(e){
        e.preventDefault();
        var content = $(".personal_identification_mock").html();
        $("#divPersonalIdentificationContainer").append(content);
        return false;
    });

    $("#addBusinessLicenseDocument").click(function(e){
        e.preventDefault();
        var content = $(".business_license_mock").html();
        $("#divBusinesssLicenseContainer").append(content);
        return false;
    });

    var item_click;
    $(".upload-button").live('click',function(e){
        e.preventDefault;

        item_click = $(this);
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
            $.displayError("<?php language_e('cases_view_verification_phone_comp_person_verify_PleaseSelectPDFJPGTIFBMPPNGFil'); ?>");
            return;
        }
        var op = $(item_click).data('op');
        var time = $.now();

        // default op = 'personal_identification'
        var seq_number = "01";
        var submitUrl = '<?php echo base_url()?>cases/verification/upload_resource?t='+time;
        if(op == "business_license"){
            seq_number = "02";
        }

        // Upload data here
        $.ajaxFileUpload({
            id: 'upload_file_input',
            data: {
                case_id: '<?php echo $case_id; ?>',
                id : $(item_click).data("id"),
                op: op,
                seq_number: seq_number,
                type: '<?php echo $type; ?>',
                input_file_client_name: "upload_file_input",
                base_taskname: '<?php echo $base_taskname ?>'
            },
            url: submitUrl,
            resetFileValue:true,
            success: function(response) {
                $(item_click).parent().find('.view-pdf').addClass('upload-success');
                $(item_click).parent().parent().find(".input-file-name").val(myfile.split('\\').pop());
                $(item_click).parent().find('.view-pdf').data('id', response.data.response_id);
                $(item_click).data("id", response.data.response_id);
                $(item_click).parent().parent().find(".input-file-id").val(response.data.response_id);
            }
        });

        return false;
    });

    /**
     * view all pdf file.
     */
    $(".view-pdf").live('click',function(e){
        e.preventDefault();
        var url = "<?php echo base_url()?>cases/verification/";
        var op = $(this).data('op');
        url += "view_resource?case_id=<?php echo $case_id?>";
        url += "&id=" + $(this).data('id');
        url += "&op=" + op;
        url += "&file_id=" + $(this).data('id');

        $('#view_verification_file').attr('href',url);
        $('#view_verification_file').click();
        return false;
    });

    // Button delete (#1189 New Function: need to add a "remove" icon )
    var item_delete_click;
    $(".delete-button").live('click',function(e){
        e.preventDefault;

        item_delete_click = $(this);

        // do delete function
        var time = $.now();
        var submitUrl = "<?php echo base_url()?>cases/verification/";
        var id;
        submitUrl += "delete_resource?t="+time;
        id = $(item_delete_click).parent().find(".input-file-id").val();

        // Delete data here
        $.ajaxExec({
            data: {
                case_id: '<?php echo $case_id; ?>',
                id : id,
                op: 'delete_business_document'
            },
            url: submitUrl,
            resetFileValue:true,
            success: function(response) {
               $(item_delete_click).parent().parent().remove();
            }
        });
        return false;
    });

    // submit button
    $("#submitButton").click(function(e){
        e.preventDefault();
        var submitUrlData = '<?php echo base_url()?>cases/verification/<?php echo $base_taskname ?>';
        $.ajaxSubmit({
            url: submitUrlData,
            formId: 'contractVerificationForm',
            success: function(response) {
                if(response.status){
                    document.location.href = '<?php echo base_url()?>cases/verification/';
                }else{
                    $.displayError(response.message);
                }
            }
        });

        return false;
    });
});
</script>