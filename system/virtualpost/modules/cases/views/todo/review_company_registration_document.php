<div class="ym-grid content services" id="case-body-wrapper">
    <div class="ym-grid">
        <div class="header">
            <h2 style="font-size: 20px; margin-bottom: 10px"><?php admin_language_e('cases_view_todo_review_company_regis_CompanyRegistrationDocument'); ?></h2>
        </div>
        <form id="company_registration_document_form"
            action="<?php echo base_url()?>cases/todo/review_document_of_company_registration"
            method="post">
            <input type="hidden" id="company_registration_document_form_case_id"
                name="case_id" value="<?php echo $case_id;?>" />
            <table>
                <tr>
                    <td>
                        <table style="width: 450px;">
                            <tr>
                                <th colspan="2"><?php admin_language_e('cases_view_todo_review_company_regis_NotarizedDocumentOfRegistraton'); ?></th>
                            </tr>
                            <tr>
                                <td colspan="2"><input type="text"
                                    id="input_registraton_document" class="input-txt"
                                    style="width: 340px; margin-left: 0px;"
                                    value="<?php echo $company_registration_document->registraton_document_local_file_path?>">
                                    <button  id="button_registraton_document"><?php admin_language_e('cases_view_todo_review_company_regis_Preview'); ?>
                                    </button></td>
                            </tr>
                            <tr>
                                <th colspan="2"><?php admin_language_e('cases_view_todo_review_company_regis_NotarizedTranslationOfDocument'); ?></th>
                            </tr>
                            <tr>
                                <td colspan="2"><input type="text"
                                    id="input_translate_registraton_document" class="input-txt"
                                    style="width: 340px; margin-left: 0px;"
                                    value="<?php echo $company_registration_document->translate_registraton_document_local_file_path?>">
                                    <button id="button_translate_registraton_document"><?php admin_language_e('cases_view_todo_review_company_regis_Preview'); ?></button></td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table style="width: 50%;">
                            <tr>
                                <th width="100px;"><?php admin_language_e('cases_view_todo_review_company_regis_Status'); ?> <span class="required">*</span></th>
                                <td><select class="input-width" name="status">
                                        <option value="2"><?php admin_language_e('cases_view_todo_review_company_regis_Completed'); ?></option>
                                        <option value="3"><?php admin_language_e('cases_view_todo_review_company_regis_Incomplete'); ?></option>
                                </select></td>
                            </tr>
                            <tr>
                                <th><?php admin_language_e('cases_view_todo_review_company_regis_Comment'); ?> <span class="required">*</span></th>

                                <td><textarea rows="3" cols="65" name="comment_content" class="input-txt" style="height: 100%; background: #FFF;"><?php echo $company_registration_document->comment_content?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><button id="submitButton"><?php admin_language_e('cases_view_todo_review_company_regis_Submit'); ?></button>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <div class="ym-clearfix"></div>
</div>
<div class="hide" style="display: none;">
    <a id="link_registraton_document" class="iframe"
        href="<?php echo base_url()?>cases/todo/view_company_registration_document?doc_type=1&case_id=<?php echo $case_id?>"><?php admin_language_e('cases_view_todo_review_company_regis_PreviewRegistrationDocument'); ?></a> <a id="link_translate_registraton_document"
        class="iframe"
        href="<?php echo base_url()?>cases/todo/view_company_registration_document?doc_type=2&case_id=<?php echo $case_id?>"><?php admin_language_e('cases_view_todo_review_company_regis_PreviewTranslateRegistrationDo'); ?></a>
</div>
<script type="text/javascript">
$(document).ready( function() {
    $('#submitButton, #button_registraton_document,#button_translate_registraton_document').button();
    $('#link_translate_registraton_document, #link_registraton_document').fancybox({
        width: 1000,
        height: 800
    });

    $("#button_registraton_document").click(function(){
        $('#link_registraton_document').click();
        return false;
    });
    $("#button_translate_registraton_document").click(function(){
        $('#link_translate_registraton_document').click();
        return false;
    });

    /**
     * When user click submit button
     */
    $('#submitButton').click(function(){
        var submitUrl = $('#company_registration_document_form').attr('action');
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'company_registration_document_form',
            success: function(data) {
                if (data.status) {
                    $.displayInfor(data.message);
                } else {
                    $.displayError(data.message);
                }
            }
        });
        return false;
    });
});
</script>