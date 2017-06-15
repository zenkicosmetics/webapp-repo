<div class="ym-grid content services" id="case-body-wrapper">
    <div id="go-back">
        <span><a id="backButton" href="<?php echo base_url()?>cases/bankaccount/company_information?case_id=<?php echo $case_id?>">Back</a></span>
    </div>
    <div class="ym-clearfix"></div>
    <div class="header">
        <h2 style="font-size: 20px; margin-bottom: 10px"><?php language_e('cases_view_admin_bankaccount_company_regisster_PleaseFillOutAllRequiredInform'); ?></h2>
    </div>
    <div class="ym-clearfix"></div>
    <div class="ym-grid">
        <h2><?php language_e('cases_view_admin_bankaccount_company_regisster_CompanyRegistrationDocument'); ?>:</h2>
        <form id="company_registration_document_form"
            action="<?php echo base_url()?>cases/bankaccount/document_of_company_registration"
            method="post">
            <input type="hidden" id="company_registration_document_form_case_id"
                name="case_id" value="<?php echo $case_id;?>" />

            <table style="width: 450px;">
                <tr>
                    <th colspan="2"><?php language_e('cases_view_admin_bankaccount_company_regisster_NotarizedDocumentOfRegistraton'); ?></th>
                </tr>
                <tr>
                    <td colspan="2"><input type="file" id="registraton_document"
                        name="registraton_document" style="display: none;"> <input
                        type="text" id="input_registraton_document" class="input-txt"
                        style="width: 340px; margin-left: 0px;" value="<?php echo $company_registration_document->registraton_document_local_file_path?>">
                        <button id="button_registraton_document"><?php language_e('cases_view_admin_bankaccount_company_regisster_Upload'); ?></button></td>
                </tr>
                <tr>
                    <th colspan="2"><?php language_e('cases_view_admin_bankaccount_company_regisster_NotarizedTranslationOfDocument'); ?></th>
                </tr>
                <tr>
                    <td colspan="2"><input type="file" id="translate_registraton_document"
                        name="translate_registraton_document" style="display: none;"> <input
                        type="text" id="input_translate_registraton_document" class="input-txt"
                        style="width: 340px; margin-left: 0px;" value="<?php echo $company_registration_document->translate_registraton_document_local_file_path?>">
                        <button id="button_translate_registraton_document"><?php language_e('cases_view_admin_bankaccount_company_regisster_Upload'); ?></button></td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: right;">
                        <button id="submitButton" class="input-btn"><?php language_e('cases_view_admin_bankaccount_company_regisster_NextStep'); ?></button>
                    </td>
                </tr>
            </table>

        </form>
    </div>
    <div class="ym-clearfix"></div>
</div>
<script type="text/javascript">
$(document).ready( function() {
    $("#button_registraton_document").button().click(function() {
        $('#registraton_document').val('');
        $('#registraton_document').click();
        return false;
    });
    $("#registraton_document").change(function (){
        myfile= $( this ).val();
        var ext = myfile.split('.').pop();
        if(ext.toUpperCase() != "PDF"){
           $('#container').css('visibility', 'hidden');
           $.displayError('Please select pdf file to upload.', null, function() {
              $('#container').css('visibility', '');
               });
            return;
        }

        var case_id = $('#company_registration_document_form_case_id').val();
        //Upload data here
        $.ajaxFileUpload({
            id: 'registraton_document',
            data: {
                doc_type: '1',
                case_id: $('#company_registration_document_form_case_id').val()
            },
            url: '<?php echo base_url()?>cases/bankaccount/upload_company_registration_document?doc_type=1&case_id=' + case_id,
            success: function(data) {
                $('#input_registraton_document').val($("#registraton_document").val());
            }
        });
    });

    $("#button_translate_registraton_document").button().click(function() {
        $('#translate_registraton_document').val('');
        $('#translate_registraton_document').click();
        return false;
    });
    $("#translate_registraton_document").change(function (){
        myfile= $( this ).val();
        var ext = myfile.split('.').pop();
        if(ext.toUpperCase() != "PDF"){
           $('#container').css('visibility', 'hidden');
           $.displayError('Please select pdf file to upload.', null, function() {
              $('#container').css('visibility', '');
               });
            return;
        }
        var case_id = $('#company_registration_document_form_case_id').val();
        // Upload data here
        $.ajaxFileUpload({
            id: 'translate_registraton_document',
            data: {
                doc_type: '2',
                case_id: $('#company_registration_document_form_case_id').val()
            },
            url: '<?php echo base_url()?>cases/bankaccount/upload_company_registration_document?doc_type=2&case_id=' + case_id,
            success: function(data) {
                $('#input_translate_registraton_document').val($("#translate_registraton_document").val());
            }
        });
    });

    /**
     * When user click submit button
     */
    $('#submitButton').click(function(){
        var submitUrl = $('#company_registration_document_form').attr('action');
        var case_id = $('#company_registration_document_form_case_id').val();
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'company_registration_document_form',
            success: function(data) {
                if (data.status) {
                    document.location.href = '<?php echo base_url()?>cases/bankaccount/additional_information?case_id=' + case_id;
                } else {
                    $.displayError(data.message);
                }
            }
        });
        return false;
    });
});
</script>