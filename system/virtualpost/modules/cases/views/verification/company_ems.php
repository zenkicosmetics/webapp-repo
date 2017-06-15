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
input.input-txt{
    background: #fff none repeat scroll 0 0 !important;
}
.input-error {
    border: 1px #800 solid !important;
    color: #800;
}
</style>
<div class="ym-grid content" id="case-body-wrapper">
    <div class="cloud-body-wrapper xx">
        <div class="ym-grid">
            <h2 style="font-size: 20px; margin-bottom: 10px"><?php language_e('cases_view_verification_company_ems_VerificationsRequired'); ?>:</h2>
        </div>
        <div class="ym-grid">
            <div class="ym-gl ym-g80 bd">
                <div class="ym-grid">
                    <div class="bd-header"><?php language_e('cases_view_verification_company_ems_ProofOfBusiness'); ?></div>
                </div>
                <form id="contractVerificationForm" action="#" method="post">
                    <div class="ym-grid">
                        <div class="ym-gl ym-g100 bd-content">
                            <div class="ym-grid">
                                <div class="ym-gl ym-g80"><?php language_e('cases_view_verification_company_ems_YourCompanyInformation'); ?>: </div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g80">
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
                                </div>
                            </div>

                            <div class="ym-grid">
                                <div class="ym-gl ym-g80">
                                    <div><?php language_e('cases_view_verification_company_ems_PleaseDescribeMsg'); ?></div>
                                    <div>
                                        <textarea id="description" class="input-txt" name="description" rows="4" style="width: 680px"
                                                  maxlength="500"><?php echo $case_check ? $case_check->description :""; ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="ym-grid">
                                <div class="ym-gl ym-g80">
                                    <div><?php language_e('cases_view_verification_company_ems_PleaseUploadYourBusinessLicens'); ?></div>
                                    <input type="text" name="tmp_file_name_id" id="tmp_file_name_id" class="input-txt input-file-name" readonly
                                        value="<?php echo empty($case_resource)? "" : basename($case_resource->local_file_path); ?>"
                                        style="width: 300px; margin-left: 0px !important;border-color:#a8a8a8" />
                                    <button type='button' class="upload-button upload-business-document"><?php language_e('cases_view_verification_company_ems_Upload'); ?></button>
                                    <button type='button' class="<?php echo $case_resource ? "upload-success" : "" ?> view-pdf"  data-op="business"
                                            data-id="<?php echo $case_resource ? $case_resource->id :"";  ?>"><?php language_e('cases_view_verification_company_ems_View'); ?></button>
                                </div>
                            </div>

                            <br />
                            <div class="ym-grid">
                                <div class="ym-gl ym-g100"><?php language_e('cases_view_verification_company_ems_PleaseNameAllOfficersMsg'); ?>:</div>
                            </div>
                            <div id="divMailReceiverContainer">
                                <?php
                                $total_count = 4;
                                if(count($mailReceivers) >= $total_count){
                                    $total_count = count($mailReceivers);
                                    $mail_receivers = $mailReceivers;
                                }else{
                                    $mail_receivers = array();
                                    foreach ($mailReceivers as $obj){
                                        $mail_receivers[] = $obj;
                                    }
                                    for($i = count($mailReceivers); $i<$total_count; $i++){
                                        $mail_receivers[] = "";
                                    }
                                }
                                $index = 0;
                                ?>
                                <?php foreach($mail_receivers as $mr){ $index ++;?>
                                <div class="ym-grid">
                                        <div class="">
                                            <?php language_e('cases_view_verification_company_ems_Name'); ?>: <input type="text" name="mail_receiver_name[]" class="input-txt" style="width:350px"
                                                         value="<?php echo isset($mr->name) ? $mr->name : ""; ?>" maxlength="100" />
                                            <input type="text" class="input-txt input-file-name" style="width: 150px; margin-left: 0px !important;border-color:#a8a8a8"
                                                   value="<?php echo $mr ? basename($mr->local_file_path) : ""; ?>" maxlength="100" readonly />
                                            <input type="hidden" name="mail_receiver_id[]" value="<?php echo isset($mr->id) ? $mr->id : ""; ?>"
                                                   class="input-txt input-file-id office-file-id" style="width:50px" />
                                            <button class="upload-button" data-op="officer" data-old-data="<?php echo $mr ? "1" : ""; ?>" data-id="<?php echo $mr ? $mr->id : "" ?>"
                                                    type="button"><?php language_e('cases_view_verification_company_ems_Upload'); ?></button>
                                            <button type="button" data-op="officer" data-id="<?php echo $mr ? $mr->id : "" ?>"
                                                    class="<?php echo $mr ? "upload-success" : "" ?> view-pdf" ><?php language_e('cases_view_verification_company_ems_View'); ?></button>
                                            <!-- #1189 New Function: need to add a "remove" icon -->
                                            <button type="button"  class="delete-button" data-id="<?php echo $mr ? $mr->id : "" ?>" data-op="delete_officer"><?php language_e('cases_view_verification_company_ems_Delete'); ?></button>
                                        </div>
                                    </div>
                                <?php }?>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g75" style="width: 85%"><a class="ym-gr main_link_color" href="#" id="addOfficerBtn">Add officer</a></div>
                            </div>

                            <br />
                            <div class="ym-grid">
                                <div class="ym-gl ym-g100"><?php language_e('cases_view_verification_company_ems_PleaseNameAnyBeneficialOwner'); ?>:</div>
                            </div>
                            <div id="divOfficerContainer">
                                <?php
                                $total_count = 4;
                                if(count($officers) >= $total_count){
                                    $total_count = count($officers);
                                    $officer_owners = $officers;
                                }else{
                                    $officer_owners = array();
                                    foreach ($officers as $obj){
                                        $officer_owners[] = $obj;
                                    }
                                    for($i = count($officers); $i<$total_count; $i++){
                                        $officer_owners[] = "";
                                    }
                                }
                                ?>
                                <?php foreach($officer_owners as $office){ ?>
                                <div class="ym-grid">
                                        <div class="">
                                            <?php language_e('cases_view_verification_company_ems_Name'); ?>: <input type="text" name="officer_name[]" class="input-txt office-name" style="width:320px"
                                                         value="<?php echo $office ? $office->name : ""; ?>" maxlength="100" />
                                            <input type="text" name="officer_rate[]" value="<?php echo ($office) ? $office->rate : ""; ?>"
                                                   class="input-txt office-rate" style="width:50px" /> %
                                            <input type="hidden" name="officer_file_id[]" value="<?php echo $office ? $office->id : ""; ?>"
                                                   class="input-txt input-file-id  owner-file-id"  />
                                            <input type="text"  class="input-txt input-file-name" style="width:80px;border-color:#a8a8a8" readonly
                                                   value="<?php echo $office ? basename($office->officer_local_path) : ""; ?>" />
                                            <button class="upload-button"  data-id="<?php echo $office ? $office->id : "" ?>"
                                                    data-op="owner" data-old-data="<?php echo $office ? "1" : ""; ?>"><?php language_e('cases_view_verification_company_ems_Upload'); ?></button>
                                            <button class="<?php echo $office ? "upload-success" : "" ?> view-pdf"
                                                    data-id="<?php echo $office ? $office->id : "" ?>" data-op="owner"><?php language_e('cases_view_verification_company_ems_View'); ?></button>
                                            <!-- #1189 New Function: need to add a "remove" icon -->
                                            <button type="button"  class="delete-button" data-id="<?php echo $office ? $office->id : "" ?>" data-op="delete_owner"><?php language_e('cases_view_verification_company_ems_Delete'); ?></button>
                                        </div>
                                    </div>
                                <?php }?>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g75" style="width: 85%"><a class="ym-gr main_link_color" href="#" id="addOwnerBtn" >Add owner</a></div>
                            </div>
                            <!--#1333 Add more Comment in the customer verification for ALL Verification templates-->
                            <?php if($case_check && $case_check->status == 3){?>
                             <div class="ym-grid">
                                <div class="ym-gl ym-g60">Comment for ClevverMail verification team: </div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g60">
                                    <textarea rows="6" style="width: 680px" class="input-txt" name="comment_for_registration_content"></textarea>
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
                <div class='ym-gl'><a href="#" id="backBtn"><?php language_e('cases_view_verification_company_ems_Back'); ?></a></div>
                <div class='ym-gr'><a class="input-btn" id="submitButton" ><?php language_e('cases_view_verification_company_ems_IHaveCompletedThisVerification'); ?></a></div>
            </div>
        </div>
    </div>
</div>


<div style="display: none">
    <a id="view_verification_file" class="iframe"><?php language_e('cases_view_verification_company_ems_PreviewFile'); ?></a>

    <form method="post">
        <input name="upload_file_input" id="upload_file_input" value="" type="file" />
    </form>

    <!-- mock of officer -->
    <div class="officer_mock">
        <div class="ym-grid">
            <div class="">
                <?php language_e('cases_view_verification_company_ems_Name'); ?>: <input type="text" name="officer_name[]" class="input-txt office-name" style="width:320px"
                             value="" maxlength="100" />
                <input type="text" name="officer_rate[]" value="" class="input-txt office-rate" style="width:50px" /> %
                <input type="hidden" name="officer_file_id[]" value="" class="input-txt input-file-id owner-file-id" style="width:50px" />
                <input type="text"  class="input-txt input-file-name" style="width:80px;border-color:#a8a8a8" value="" readonly />
                <button type="button" class="upload-button" data-id="" data-op="owner"><?php language_e('cases_view_verification_company_ems_Upload'); ?></button>
                <button type="button" class="view-pdf" data-id="" data-op="owner"><?php language_e('cases_view_verification_company_ems_View'); ?></button>
                <!-- #1189 New Function: need to add a "remove" icon -->
                <button type="button"  class="delete-button" data-id="" data-op="delete_owner"><?php language_e('cases_view_verification_company_ems_Delete'); ?></button>
            </div>
        </div>
    </div>

    <!-- mail receiver mock -->
    <div class="mail_receiver_mock">
        <div class="ym-grid">
            <div class="">
                <?php language_e('cases_view_verification_company_ems_Name'); ?>: <input type="text" name="mail_receiver_name[]" class="input-txt mail_receiver_name" style="width:350px" value="" maxlength="100" />
                <input type="text"  class="input-txt input-file-name" readonly style="width: 150px; margin-left: 0px !important;border-color:#a8a8a8" value="" maxlength="100" />
                <input type="hidden" name="mail_receiver_id[]" value="" class="input-txt input-file-id office-file-id" style="width:50px" />
                <button type="button" class="upload-button" data-id="" data-op="officer"><?php language_e('cases_view_verification_company_ems_Upload'); ?></button>
                <button type="button" class="view-pdf" data-op="officer" data-id=""><?php language_e('cases_view_verification_company_ems_View'); ?></button>
                <!-- #1189 New Function: need to add a "remove" icon -->
                <button type="button"  class="delete-button" data-id="" data-op="delete_officer"><?php language_e('cases_view_verification_company_ems_Delete'); ?></button>

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
    $(".bd-content").slimScroll({height:($(window).height() - 338)+'px'});

    var item_click;
    $(".upload-button").live('click',function(e){
        e.preventDefault;

        item_click = $(this);
        // do upload function
        $("#upload_file_input").click();

        return false;
    });

    // Button delete (#1189 New Function: need to add a "remove" icon )
    var item_delete_click
    $(".delete-button").live('click',function(e){
        e.preventDefault;

        item_delete_click = $(this);
//        item_click = $(item_delete_click).parent().find(".upload-button");

        // do delete function
        var op = $(item_delete_click).data('op');
        var time = $.now();
        var submitUrl = "<?php echo base_url()?>cases/verification/";
        var id;
        if(op == "delete_officer"){
            //$("#change_usps_mail_receiver_flag").val("1");
            submitUrl += "delete_resource?t="+time;
            op = "delete_officer";
            id = $(item_delete_click).parent().find(".office-file-id").val();
        }else if(op == "delete_owner"){
            //$("#change_usps_officer_flag").val("1");
            submitUrl += "delete_resource?t="+time;
            op = "delete_onwer";
            id = $(item_delete_click).parent().find(".owner-file-id").val();
        }else{
            submitUrl += "delete_resource?t="+time;
            op = "delete_business_document";
        }

        // Delete data here
        $.ajaxExec({
            data: {
                case_id: '<?php echo $case_id; ?>',
                id : id,
                op: op
            },
            url: submitUrl,
            resetFileValue:true,
            success: function(response) {
                $(item_delete_click).parent().parent().remove();

            }
        });
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
            $.displayError('Please select PDF, JPG, TIF, BMP, PNG file to upload.');
            return;
        }
        var op = $(item_click).data('op');
        var time = $.now();

        var seq_number = "01";
        var submitUrl = "<?php echo base_url()?>cases/verification/";
        if(op == "officer"){
            //$("#change_usps_mail_receiver_flag").val("1");
            seq_number = "02";
            submitUrl += "upload_resource?t="+time;
        }else if(op == "owner"){
            //$("#change_usps_officer_flag").val("1");
            seq_number = "03";
            submitUrl += "upload_special_document?t="+time;
            op = "officer_onwer";
        }else{
            submitUrl += "upload_resource?t="+time;
        }

        // Upload data here
        $.ajaxFileUpload({
            id: 'upload_file_input',
            data: {
                case_id: '<?php echo $case_id; ?>',
                id : $(item_click).data("id"),
                op: op,
                type: 'company_verification_E_MS',
                input_file_client_name: "upload_file_input",
                seq_number: seq_number,
                base_taskname: "company_verification_E_MS"
            },
            url: submitUrl,
            resetFileValue:true,
            success: function(response) {
                if(op == "officer_onwer"){
                    $(item_click).parent().find('.view-pdf').data('id', response.data.response_id);
                    $(item_click).data("id", response.data.response_id);
                    $(item_click).parent().find(".input-file-id").val(response.data.response_id);
                }else{
                    $(item_click).parent().find('.view-pdf').data('id', response.data.file_id);
                    $(item_click).data("id", response.data.file_id);
                    $(item_click).parent().find(".input-file-id").val(response.data.file_id);
                }

                $(item_click).parent().find('.view-pdf').addClass('upload-success');
                $(item_click).parent().find(".input-file-name").val(myfile.split('\\').pop());
            }
        });

        return false;
    });

    /**
     * view all pdf file.
     */
    $(".view-pdf").live("click",function(){
        var op = $(this).data('op');
        var url = "<?php echo base_url()?>cases/verification/";
        if(op == "owner"){
            url += "special_view_file?case_id=<?php echo $case_id?>";
        }else{
            url += "view_resource?case_id=<?php echo $case_id?>";
        }
        url += "&id=" + $(this).data('id');
        url += "&op=" + op;
        url += "&file_id=" + $(this).data('id');

        $('#view_verification_file').attr('href',url);
        $('#view_verification_file').click();
        return false;
    });

    // submit button
    $("#submitButton").click(function(e){

        e.preventDefault();

         // defined url
        var submitUrlData = '<?php echo base_url()?>cases/verification/company_verification_E_MS';

        // Call ajaxSubmit
        $.ajaxSubmit({
            url: submitUrlData,
            formId: 'contractVerificationForm',
            success: function(response) {

                // if status is true
                if(response.status){

                     // #1328 add message after verification is submited by customer
                    // Popup message after when submitted true
                    $.infor({
                        message: response.message,
                        ok:function(){
                            // Show case in your case of screen
                        	document.location.href = '<?php echo base_url()?>cases/verification/';
                        }
                    });

                }else{
                    // validate officer owner
                    validate_officer_owner();

                    // Popup message after when submitted error
                    $.displayError(response.data.message);

                }// end if-else status

            }// end success

        });// end call ajaxsubmit

        return false;
    });// end click submit's button

    $("#addOwnerBtn").click(function(e){
        e.preventDefault();

        var content = $(".officer_mock").html();
        $("#divOfficerContainer").append(content);
        return false;
    });

    $("#addOfficerBtn").click(function(e){
        e.preventDefault();

        var content = $(".mail_receiver_mock").html();
        $("#divMailReceiverContainer").append(content);
        return false;
    });

    function validate_officer_owner(){
        var flag_check = true;

        $("input").removeClass("input-error");
        // validate owner
        $('.office-file-id').each(function(index){
            var parent = $(this).parent();
            var file_id = $.trim($(this).val());
            var name = $.trim(parent.find(".office-name").val());
            var rate = $.trim(parent.find(".office-rate").val());

            if(file_id != "" && name != "" ){
                flag_check = true;
            }else if(file_id != "" || name != "" || rate != ""){

                flag_check = false;
                if(name == ""){
                    parent.find(".office-name").addClass("input-error").attr("title", "please input the name.");
                }
                if(rate == "" ){
                    parent.find(".office-rate").addClass("input-error").attr("title", "Please input the rate and rate > 25%.");
                }

                if(file_id == ""){
                    parent.find(".upload-button").addClass("input-error").attr("title", "Please upload the file.");
                }
                parent.find(".office-name").focus();
            }
        });

        if($("#tmp_file_name_id").val() == ""){
            flag_check = false;
            $('.upload-business-document').addClass("input-error").attr("title", "Please upload the file.")
        }

        var decsription = $("#description").val();
        if(decsription == "" || decsription.length < 50){
            flag_check = false;
            $("#description").addClass("input-error").attr("title", "Please upload the file.")
        }

        return flag_check;
    }

});
</script>