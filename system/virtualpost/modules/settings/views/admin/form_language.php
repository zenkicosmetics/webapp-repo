<form id="languageForm" method="post" action="<?php echo base_url().'admin/settings/addLanguage'?>">
    <table>
        <tr>
            <th><?php admin_language_e('settings_view_admin_formlanguage_Languages'); ?><span class="required">*</span></th>
            <td><input type="text" id="language" name="language" value="" class="input-width custom_autocomplete" style="width: 160px;" /></td>
        </tr>
        <tr>
            <th><?php admin_language_e('settings_view_admin_formlanguage_Status'); ?></th>
            <td>
                <select id="language_status" name="language_status" class="input-width" style="width: 110px;">
                    <option value="0"><?php admin_language_e('settings_view_admin_formlanguage_Inactive'); ?></option>
                    <option value="1"><?php admin_language_e('settings_view_admin_formlanguage_Active'); ?></option>
                </select>
            </td>
        </tr>
    </table>
</form>

