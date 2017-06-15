<?php
if ($action_type == 'add') {
	$submit_url = base_url() . 'device/admin/add';
} else {
	$submit_url = base_url() . 'device/admin/edit';
}
?>
<form id="addEditDeviceForm" method="post" action="<?php echo $submit_url ?>" autocomplete="on">
	<table>
		<tr>
			<th><?php admin_language_e('device_view_admin_form_PanelCode'); ?><span class="required">*</span></th>
			<td><input type="text" class="input-width " id="panel_code" name="panel_code"
			           value="<?php echo $device->panel_code ?>" class="input-width" maxlength="50"/></td>
			<th><?php admin_language_e('device_view_admin_form_Description'); ?></th>
			<td><textarea rows="3" id="description" name="description" class="input-width"
			              maxlength="500"><?php echo $device->description ?></textarea>
		</tr>
		<tr>
			<th><?php admin_language_e('device_view_admin_form_Location'); ?><span class="required">*</span></th>
			<td>
				<select name="location_id" id="location_id" class="input-width" style = "width: 262px;">
                                    <?php
                                    foreach ($locations as $location) {
                                        $additional_params = '';
                                        if ($device->location_id == $location->id) {
                                                $additional_params = ' selected';
                                        }
                                        $item_name = $location->location_name;
                                        if (array_key_exists($location->id, $enterprise_locations)) {
                                            $item_name = $item_name.' - Enterprise '.$enterprise_locations[$location->id];
                                        }
                                        echo '<option value="' . $location->id . '" ' . $additional_params . '>' . $item_name . '</option>';
                                    }
                                    ?>
				</select>
			</td>
			<th><?php admin_language_e('device_view_admin_form_Timezone'); ?><span class="required">*</span></th>
			<td>
				<select name="timezone" id="timezone" class="input-width" style = "width: 262px;">
                                    <?php
                                    foreach ($timezones as $timezone) {
                                        $additional_params = '';
                                        if ($device->timezone == $timezone) {
                                                $additional_params = ' selected';
                                        }
                                        echo '<option value="' . $timezone . '" ' . $additional_params . '>' . $timezone . '</option>';
                                    }
                                    ?>
				</select>
			</td>
		</tr>
		<?php if ($device->type == 'clevverboard') {?>
		<tr>
			<th><?php admin_language_e('device_view_admin_form_MessageTitle'); ?></th>
			<td><input type="text" class="input-width " id="message_title" name="message_title"
			           value="<?php echo $device->message_title ?>" class="input-width" maxlength="100"/></td>
			<th><?php admin_language_e('device_view_admin_form_MessageSummary'); ?></th>
			<td><input type="text" class="input-width" id="message_summary" name="message_summary"
			           value="<?php echo $device->message_summary ?>" class="input-width" maxlength="500"/></td>
		</tr>
		<tr>
			<th><?php admin_language_e('device_view_admin_form_MessageContent'); ?></th>
			<td><textarea rows="3" id="message_fulltext" name="message_fulltext" class="input-width"
			              maxlength="500"><?php echo $device->message_fulltext ?></textarea>
			
			<th></th>
			<td>
			</td>
		</tr>
		<?php } ?>
		<?php if ($device->type == 'clevverhub') {?>
		<tr>
			<th><?php admin_language_e('device_view_admin_form_WifiSSID'); ?></th>
			<td><input type="text" class="input-width" id="wifi_ssid" name="wifi_ssid"
			           value="<?php echo $device->wifi_ssid ?>" class="input-width" maxlength="100"/></td>
			<th><?php admin_language_e('device_view_admin_form_WifiPassword'); ?></th>
			<td><input type="text" class="input-width" id="wifi_password" name="wifi_password"
			           value="<?php echo $device->wifi_password ?>" class="input-width" maxlength="100"/></td>
			
		</tr>
		<?php } ?>
	</table>
	<input type="hidden" id="h_action_type" name="h_action_type" value="<?php echo $action_type ?>"/>
    <input  type="hidden" id="h_id" name="id"	value="<?php echo $device->id ?>"/>
</form>