<form id="saveTermConditionSettingForm" action="<?php echo base_url() . 'account/setting/save_term_condition_setting'; ?>" method="post" >
    <div class="ym-grid">
        <div class="ym-gl">
            <h4 class="COLOR_063"><?php language_e('account_view_index_AddYourOwnTermsAndConditions'); ?></h4>
        </div>
        <?php 
        $CUSTOMER_TERM_CONDITION_SETTING = AccountSetting::get($customer_id, APConstants::CUSTOMER_TERM_CONDITION_SETTING);
        ?>
    </div>

    <div class="ym-grid" style="padding-top: 20px">
        <input type="checkbox" <?php if(AccountSetting::get($customer_id, APConstants::CUSTOMER_TERM_CONDITION_SETTING) == "1"){ echo "checked='checked'"; }?>
               class="customCheckbox" id="CUSTOMER_TERM_CONDITION_SETTING" name="CUSTOMER_TERM_CONDITION_SETTING"  />
        <label> <?php language_e('account_view_index_AddYourTermsCondition'); ?> </label>
        <span class="managetables-icon icon_help tipsy_tooltip" original-title="By adding your own terms & conditions to the Clevver terms & conditions,
              your users will enter into a double contract.
              Your terms & conditions will be the contract between your company (as specified in your invoice address and in your terms &  conditions)
              and the individual user. Also the standard Clevver terms & conditions will constitute a contract between the user
              and Clevver directly for the use of the Clevver software and services."></span>
        <a href="#" id="see_term_condition_history" class="main_link_color" style="margin-left: 15px"><?php language_e('account_view_index_SeeHistory'); ?></a>
    </div>
    <div class="ym-gr" id="uploadTermConditionDiv" >
        <input style="margin: 1.125em 0 0 0;width: 170px;" type="button" id="uploadTermConditionBtn" class="btn-grey btn-yellow" value="<?php language_e('account_view_index_UploadTermCondition'); ?>"/>
    </div>
    <div class="ym-clearfix"></div>

</form>