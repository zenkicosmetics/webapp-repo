<form id="saveAPIAccessSettingForm" action="<?php echo base_url() . 'account/setting/save_api_access_setting'; ?>" method="post" >
    <div class="ym-grid">
        <h4 class="COLOR_063"><?php language_e('account_view_index_APIAccess'); ?></h4>
    </div>

    <div class="ym-grid" style="padding-top: 20px">
        <input type="checkbox" <?php if($api_access['api_access_flag'] == '1'){ echo "checked='checked'"; }?>
               class="customCheckbox" id="active_api_access_checkbox" name="active_api_access_checkbox" value="1" />
        <input type="hidden" id="api_access_flag_hidden" value="<?php echo $api_access['api_access_flag'];?>" />
        <input type="hidden" id="active_api_access_selected_hidden" value="" />
        <label>
            <?php $api_access_cost = $pricing_map[5]['api_access']->item_value;?>
            <?php language_e('account_view_index_AddAPIAccessTo'); ?> (+ <?php echo APUtils::number_format($api_access_cost); ?> EUR / month) <a href="<?php echo base_url() . 'info/api_info'; ?>" target="_blank" class="main_link_color"><?php language_e('account_view_index_seefulldescriptionhere');?></a>
        </label>
    </div>
    <div class="ym-clearfix"></div>
    <div class="ym-grid" style="padding-top: 20px; <?php if($api_access['api_access_flag'] != '1') { echo "display: none;";} ?>" id="apiAccessDivContainer">
        <label style="width: 60px;display: inline-block"><?php language_e('account_view_index_AppCode'); ?></label>
        <input type="text" style="width: 490px;" class="input-txt" name="app_code" id="app_code"
               value="<?php echo $api_access['app_code'];?>" />
        <label style="width: 60px;display: inline-block"><?php language_e('account_view_index_AppKey'); ?></label>
        <input type="text" style="width: 490px;" class="input-txt" name="app_key" id="app_key"
                value="<?php echo $api_access['app_key'];?>"/>
    </div>
</form>