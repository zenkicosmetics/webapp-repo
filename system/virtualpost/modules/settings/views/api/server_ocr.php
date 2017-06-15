<div class="header">
    <h2 style="font-size: 20px; margin-bottom: 10px">Settings > APIs >
        OCR API Setting</h2>
</div>
<form id="usesrSearchForm" method="post"
      action="<?php echo base_url() ?>settings/api/server_ocr">
    <div class="input-form">
        <table class="settings">
            <tr>
                <th class="input-width-200">API Endpoint</th>
                <td><input type="text" id="SERVER_OCR_API_ENDPOINT" name="SERVER_OCR_API_ENDPOINT"
                           value="<?php echo Settings::get(APConstants::SERVER_OCR_API_ENDPOINT) ?>"
                           class="input-width" /></td>
            </tr>
            <tr>
                <th class="input-width-200">API Key</th>
                <td><input type="text" id="SERVER_OCR_API_KEY" name="SERVER_OCR_API_KEY"
                           value="<?php echo Settings::get(APConstants::SERVER_OCR_API_KEY) ?>"
                           class="input-width" /></td>
            </tr>
            <tr>
                <th class="input-width-200" style="vertical-align: top;">&nbsp;</th>
                <td>
                    <button id="savePhoneNumberButton" class="admin-button">Save</button>
                </td>
            </tr>
        </table>
    </div>
</form>
<script type="text/javascript">
    $(document).ready(function () {
        $('.admin-button').button();
    });
</script>