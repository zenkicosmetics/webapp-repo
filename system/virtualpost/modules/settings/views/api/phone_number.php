<div class="header">
    <h2 style="font-size: 20px; margin-bottom: 10px"><?php admin_language_e('setting_view_api_phonenumber_BreadScrum'); ?></h2>
</div>
<form id="usesrSearchForm" method="post"
      action="<?php echo base_url() ?>settings/api/phone_number">
    <div class="input-form">
        <table class="settings">
            <tr>
                <th class="input-width-200"><?php admin_language_e('setting_view_api_phonenumber_APIEndpoint'); ?></th>
                <td><input type="text" id="SONETEL_API_ENDPOINT" name="SONETEL_API_ENDPOINT"
                           value="<?php echo Settings::get(APConstants::SONETEL_API_ENDPOINT) ?>"
                           class="input-width" /></td>
            </tr>
            <tr>
                <th class="input-width-200"><?php admin_language_e('setting_view_api_phonenumber_APIKey'); ?></th>
                <td><input type="text" id="SONETEL_API_KEY" name="SONETEL_API_KEY"
                           value="<?php echo Settings::get(APConstants::SONETEL_API_KEY) ?>"
                           class="input-width" /></td>
            </tr>
            <tr>
                <th class="input-width-200"><?php admin_language_e('setting_view_api_phonenumber_APIToken'); ?></th>
                <td><input type="text" id="SONETEL_API_TOKEN"
                           name="SONETEL_API_TOKEN"
                           value="<?php echo Settings::get(APConstants::SONETEL_API_TOKEN) ?>"
                           class="input-width" /></td>
            </tr>
            <tr>
                <th class="input-width-200" style="vertical-align: top;">&nbsp;</th>
                <td>
                    <button id="savePhoneNumberButton" class="admin-button"><?php admin_language_e('setting_view_api_phonenumber_SaveBtn'); ?></button>
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