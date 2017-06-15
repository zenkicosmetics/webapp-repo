<style>
.disable-selectbox {
    pointer-events: none;
    cursor: not-allowed;
}
</style>

<?php  $submit_url = base_url().'cloud/set_accounting_email'; ?>
<div class="ym-grid">
    <div class="ym-gbox">
        <table>
            <tr><td><h2><?php language_e('cloud_view_account_mail_YouCanAddAnEmailAddressOfYourA'); ?>.</h2></td></tr>
            <tr><td style="text-align: center;"> <img src="<?php echo APContext::getAssetPath() ?>images/accounting_interface.png" style="width: 80%; height: 70px; margin: 10px 0;"></td></tr>
        </table>
    </div>
</div>
<form id="accountingEmailForm" method="post" action="<?php echo $submit_url?>">
    <div class="ym-grid" style="margin-left: 10px; width: 500px">

        <div class="ym-grid input-item" style="margin-bottom: 10px">
            <div class="ym-gl ym-g30 register_label">
                <label ><?php language_e('cloud_view_dropbox_setting_Postbox'); ?> <span class="required">*</span></label>
            </div>
            <div class="ym-gl ym-g70">
                    <?php echo my_form_dropdown(array(
                         "data" => $postboxes,
                         "value_key" => 'postbox_id',
                         "label_key" => 'label',
                         "value" => $postbox_id,
                         "name" => 'postbox_name',
                         "id"    => "accountingPostbox",
                         "clazz" => 'input-width'. (empty($accounting_interface['email']) ? "" : " disable-selectbox"),
                         "style" => 'width: 100%; margin: 0;',
                         "has_empty" => true,
                         "option_default" => "---Select Postbox---",
                     ));?>
            </div>
        </div>
        <div class="ym-clearfix"></div>
        <div class="ym-grid input-item" style="margin-bottom: 10px">
            <div class="ym-gl ym-g30 register_label">
                    <label ><?php language_e('cloud_view_account_mail_InterfaceID'); ?></label>
            </div>
            <div class="ym-gl ym-g70">
                <input style="margin-left: 0; width: 99%;" type="text" id="interfaceId" name="interface_id" value="<?php echo $accounting_interface['interface_id'] ?>" class="input-txt-none" maxlength=255 />
            </div>
        </div>
        <div class="ym-clearfix"></div>
        <div class="ym-grid input-item" style="margin-bottom: 10px">
            <div class="ym-gl ym-g30 register_label">
                    <label ><?php language_e('cloud_view_dropbox_setting_EMail'); ?> <span class="required">*</span></label>
            </div>
            <div class="ym-gl ym-g70">
                <input style="margin-left: 0; width: 99%;" type="text" id="accountingEmailId" name="accounting_email" value="<?php echo $accounting_interface['email'] ?>" class="input-txt-none" maxlength=255 />
            </div>
        </div>
        <div class="ym-clearfix"></div>
        <div class="ym-grid input-item" >
            <div class="ym-gl ym-g30 register_label">
                    <label ><?php language_e('cloud_view_dropbox_setting_Automate'); ?></label>
            </div>
            <div class="ym-gl ym-g70">
                <input type="checkbox" id="autoSendPDF" name="auto_send_pdf" class="customCheckbox" <?php echo empty($auto_send_pdf) ? "" : "checked" ?>/>
            </div>
        </div>
    </div>
</form>
<script type="text/javascript">
$(document).ready( function() {
        // Process when postbox setting change.
        $('select#accountingPostbox').change(function () {
            var submitUrl = '<?php echo base_url()?>cloud/load_accounting_email';
            $.ajax({
                    url:submitUrl,
                    dataType: 'json',
                    data: {'postbox_id' : $(this).val()},
                    success: function (response) {
                        if (response.status) {
                            $('#accountingEmailId').val(response.data.email);
                            $('#interfaceId').val(response.data.interface_id);
                            $('#autoSendPDF').prop('checked', response.data.auto_send_pdf == 1);
                        } else {
                            $('#accountingEmailId').val('');
                            $('#interfaceId').val('');
                            $('#autoSendPDF').prop('checked',false);
                        }
                    }
            });
        });
});
</script>