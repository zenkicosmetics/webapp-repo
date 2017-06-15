<div class="ym-grid">
    <div id="cloud-body-wrapper" style="width: 1070px;">
        <h2>Handing rules</h2>
        <div class="ym-clearfix" style="height:1px;"></div>
    </div>
</div>
<div class="clearfix"></div>
<div id="account-body-wrapper" style="margin:10px 0 0 40px">
    <br />
    <div class="clearfix"></div>
    <div class="ym-grid">
        <div id="gridwraperHandlingRule">
            <div id="searchTableHandlingRuleResult">
                <table id="dataGridHandlingRuleResult"></table>
                <div id="dataGridHandlingRulePager"></div>
            </div>
        </div>
        <div class="clear-height"></div>
    </div>
    <input type="hidden" id="addEditUserForm_id" name="customer_id" value="<?php echo $customer_id; ?>" />
</div>

<!-- Content for dialog -->
<div class="hide">
    <div id="callToSettingWindow" title="Change Call Setting" class="input-form dialog-form"></div>
    <div id="callOutGoingWindow" title="Change Call Outgoing" class="input-form dialog-form"></div>
    <div id="changePhoneNumberSettingWindow" title="Change Phone Number Setting" class="input-form dialog-form"></div>
</div>

<script type="text/javascript" src="<?php echo $this->config->item('asset_url'); ?>system/virtualpost/modules/account/js/HandlingRules.js"></script>
<script>
    jQuery(document).ready(function ($) {
        $('input:checkbox.customCheckbox').checkbox({cls: 'jquery-safari-checkbox'});
        $('.jquery-safari-checkbox').tipsy({gravity: 'sw', html: true, live: true});

        HandlingRules.init('<?php echo base_url(); ?>', 10, '<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>');
    });

</script>