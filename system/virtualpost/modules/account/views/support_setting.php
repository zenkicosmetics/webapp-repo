<form id="saveSupportSettingForm" action="<?php echo base_url() . 'account/setting/save_support_setting'; ?>" method="post" >
    <div class="ym-grid">
        <div class="ym-gl">
            <h4 class="COLOR_063"><?php language_e('account_view_index_CustomerSupportForYourUsers'); ?></h4>
        </div>
        <div class="ym-gr">
            <?php 
            $active_support_email_user_checkbox = AccountSetting::get_alias02($customer_id, APConstants::CUSTOMER_SUPPORT_EMAIL_KEY);
            $active_support_phone_user_checkbox = AccountSetting::get_alias02($customer_id, APConstants::CUSTOMER_SUPPORT_PHONE_KEY);
            $supportSettingClass = "btn-grey";
            if($active_support_email_user_checkbox == 1 || $active_support_phone_user_checkbox == 1){
                $supportSettingClass = "input-btn btn-yellow";
            }
            ?>
            
        </div>
    </div>

    <div class="ym-grid" style="padding-top: 20px">
        <input type="checkbox" <?php if($active_support_email_user_checkbox == "1"){ echo "checked='checked'"; }?>
               class="customCheckbox" id="active_support_email_user_checkbox" name="active_support_email_user_checkbox" value="1" /> <label> <?php language_e('account_view_index_ActivateSupportEmail'); ?></label>
    </div>
    <div class="ym-clearfix"></div>
    <div class="ym-grid" style="padding-top: 20px" id="supportEmailDivContainer">
        <label style="display: inline-block"><?php language_e('account_view_index_Email'); ?> <span class="required">*</span>: </label>
        <input type="email" style="width: 490px;" class="input-txt" name="active_support_email_user" id="active_support_email_user"
               value="<?php echo AccountSetting::get($customer_id, APConstants::CUSTOMER_SUPPORT_EMAIL_KEY) ?>" />
    </div>
    <div class="ym-clearfix"></div>
    <div class="ym-grid"  style="padding-top: 20px">
        <input type="checkbox" <?php if($active_support_phone_user_checkbox == "1"){ echo "checked='checked'"; }?>
               class="customCheckbox" id="active_support_phone_user_checkbox" name="active_support_phone_user_checkbox" value="1" /> <?php language_e('account_view_index_ActivateSupportPhone'); ?>
    </div>
    <div class="ym-clearfix"></div>
    <div class="ym-grid"  style="padding-top: 20px" id="supportPhoneDivContainer">
        <label style="display: inline-block">Phone <span class="required">*</span>:</label>
        <input type="text"   style="width: 490px;" class="input-txt" name="active_support_phone_user" id="active_support_phone_user"
                value="<?php echo AccountSetting::get($customer_id, APConstants::CUSTOMER_SUPPORT_PHONE_KEY) ?>"/>
    </div>
    <input style="margin: 1.125em 0 0 0; width: 170px;float:right;margin-top: -25px;" type="button" id="saveSupportSettingButton" class="btn-grey btn-yellow" value="<?php language_e('account_view_index_SaveSupportSetting') ?>" />
</form>