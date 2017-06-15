<form enctype="multipart/form-data" id="ImportForm" method="post" action="<?php echo base_url().'admin/settings/importExcel'?>" enctype="multipart/form-data">
    <div class="ym-gl">
        <div class="group-input">
            <input type="file" id="languages-import-file" name="languages-import-file" style="display: none">
            <input type="text" name="languages-import-txt" id="languages-import-txt" class="input-txt" style="width: 220px; margin-left: 0px !important;">
        </div>
        <div class="group-input">
            <button type="button" id="importExcelBtn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only right" role="button" aria-disabled="false" type="submit">
                <span class="ui-button-text"><?php admin_language_e('settings_view_admin_formupload_UploadBtn'); ?></span></button>
        </div>
</form>