<div class="ym-grid content services" id="case-body-wrapper">
    <div id="go-back">
        <span><a id="backButton" href="<?php echo base_url()?>cases/bankaccount/document_of_company_registration?case_id=<?php echo $case_id?>"><?php language_e('cases_view_admin_bankaccount_add_infor_Back'); ?></a></span>
    </div>
    <div class="ym-clearfix"></div>
    <div class="header">
        <h2 style="font-size: 20px; margin-bottom: 10px"><?php language_e('cases_view_admin_bankaccount_add_infor_PleaseFillOutAllRequiredInform'); ?></h2>
    </div>
    <div class="ym-clearfix"></div>
    <div class="ym-grid">
        <h2><?php language_e('cases_view_admin_bankaccount_add_infor_AdditionalInformation'); ?></h2>
        <form id="additional_information_form"
            action="<?php echo base_url()?>cases/bankaccount/additional_information"
            method="post">
            <input type="hidden" id="additional_information_form_case_id"
                name="case_id" value="<?php echo $case_id;?>" />

            <table style="width: 600px;">
                <tr>
                    <th width="100px;"><?php language_e('cases_view_admin_bankaccount_add_infor_DeclarationOfDirector'); ?><span class="required">*</span></th>
                    <td>
                        <textarea rows="4" cols="65" name="declaration_of_director" class="input-txt" style="height: 100%; background: #FFF;"><?php echo $cases_additional_information->declaration_of_director?></textarea>
                    </td>
                </tr>
                <tr>
                    <th><?php language_e('cases_view_admin_bankaccount_add_infor_Confirm'); ?><span class="required">*</span></th>
                    <td><input type="checkbox" name="confirm_flag" value="1" <?php if ($cases_additional_information->confirm_flag == "1") {?> checked="checked" <?php }?> style="margin-left: 10px;" /></td>
                </tr>
                <tr>
                    <th><?php language_e('cases_view_admin_bankaccount_add_infor_TransactionLimit'); ?></th>
                    <td><input type="text" name="transaction_limit" value="<?php echo $cases_additional_information->transaction_limit?>"
                        class="input-txt" /></td>
                </tr>
                <tr>
                    <th><?php language_e('cases_view_admin_bankaccount_add_infor_PlannedNumberOfTransactionsPer'); ?></th>
                    <td><input type="text" name="transaction_peryear" value="<?php echo $cases_additional_information->transaction_peryear?>"
                        class="input-txt" /></td>
                </tr>
                <tr>
                    <th><?php language_e('cases_view_admin_bankaccount_add_infor_PlannedTotalTransactionValuePe'); ?></th>
                    <td><input type="text" name="transaction_value_peryear" value="<?php echo $cases_additional_information->transaction_value_peryear?>" class="input-txt" /></td>
                </tr>
                <tr>
                    <th><?php language_e('cases_view_admin_bankaccount_add_infor_TransactionValueLimit'); ?></th>
                    <td><input type="text" name="transaction_value_limit" value="<?php echo $cases_additional_information->transaction_value_limit?>" class="input-txt" /></td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: right;">
                        <button id="submitButton" class="input-btn"><?php language_e('cases_view_admin_bankaccount_add_infor_NextStep'); ?></button>
                    </td>
                </tr>
            </table>

        </form>
    </div>
    <div class="ym-clearfix"></div>
</div>
<script type="text/javascript">
$(document).ready( function() {
    /**
     * When user click submit button
     */
    $('#submitButton').click(function(){
        var submitUrl = $('#additional_information_form').attr('action');
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'additional_information_form',
            success: function(data) {
                if (data.status) {
                    $.displayInfor(data.message, null, function() {
                        document.location.href = '<?php echo base_url()?>cases?product_id=1';
                    });
                } else {
                    $.displayError(data.message);
                }
            }
        });
        return false;
    });
});
</script>