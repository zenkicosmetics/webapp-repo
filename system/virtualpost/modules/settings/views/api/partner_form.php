<?php
$submit_url = base_url () . 'settings/api/edit_partners';
?>
<form id="addEditPartnerForm" method="post" action="<?php echo $submit_url?>" autocomplete="on">
    <table>
        <tr>
            <th><?php admin_language_e('setting_view_api_partnerform_AppCode'); ?><span class="required">*</span></th>
            <td><input type="text" id="app_code" name="app_code" value="<?php echo $partner->app_code?>"
                class="input-txt" maxlength="50" /></td>
        </tr>
        <tr>
            <th><?php admin_language_e('setting_view_api_partnerform_AppName'); ?><span class="required">*</span></th>
            <td><input type="text" id="app_name" name="app_name" value="<?php echo $partner->app_name?>"
                class="input-txt" maxlength="50" /></td>
        </tr>
        <tr>
            <th><?php admin_language_e('setting_view_api_partnerform_AppKey'); ?><span class="required">*</span></th>
            <td><input type="text" id="app_key" name="app_key" value="<?php echo $partner->app_key?>" style="width: 80%"
                class="input-txt" maxlength="255" />
            <button id="generateKeyButton" type="button" style="margin-left: 5px;"><?php admin_language_e('setting_view_api_partnerform_GenerateBtn'); ?></button></td>
            </td>
        </tr>
        <tr>
            <th><?php admin_language_e('setting_view_api_partnerform_Version'); ?></th>
            <td><input type="text" id="version" name="version" value="<?php echo $partner->version?>"
                class="input-txt" maxlength="255" /></td>
        </tr>
    </table>
    <input type="hidden" id="id" name="id" value="<?php echo $partner->id;?>" />
</form>
<script type="text/javascript">
$(document).ready( function() {
    $('#generateKeyButton').button({
      icons: {
          primary: "ui-icon-gear"
        },
        text: false
    });
    
    /**
     * generate new app key
     */
    $("#generateKeyButton").click(function(e){
        var submitUrl = '<?php echo base_url() ?>settings/api/generate_app_key';
        $.ajaxExec({
            url: submitUrl,
            success: function (data) {
                if (data.status) {
                    // Reload data grid
                    $("#app_key").val(data.data);
                } else {
                    $.displayError(data.message);
                }
            }
        });
        
        return  false;
    });
});
</script>