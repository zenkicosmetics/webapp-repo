<div class="ym-grid">
    <div id="cloud-body-wrapper">
        <h2 style="border-bottom: 1px solid #dadada; font-size: 23px; padding-bottom: 10px;">API Info</h2>
        <div class="ym-clearfix" style="height: 15px;"></div>
        <div>
            As enterprise customer, you can enable the API functionality of Clevver. With the API you can controll your Clevver account, your users and features
        </div>
        <div style="float: left; margin-top: 10px;">
            Current Version of API: 1.0 ( 31.05.2017 )
            </br>
            <a href="<?php echo base_url()?>images/ClevverMail_EntepriseAPI.pdf" target="_blank" style="text-decoration: underline">Download PDF</a>
        </div>
        <div style="float: right; margin-top: 10px;">
            <?php if ($customer->account_type == APConstants::ENTERPRISE_TYPE
                    && empty($customer->parent_customer_id)
                    && AccountSetting::get($customer->customer_id, APConstants::CUSTOMER_API_ACCESS_SETTING) != "1") { ?>
            <button type="button" id="btnUpgradeEnterprise" class="input-btn btn-yellow">Upgrade to enterprise</button>
            <?php } ?>
        </div>
        <div class="ym-clearfix" style="height: 5px;"></div>
        <!--
        <div style="margin-top:10px; height: 500px;">
            <iframe src="<?php echo base_url()?>images/ClevverMail_EntepriseAPI.pdf" style="width: 900px;height:500px;">
            <iframe>
        </div>
        <div class="ym-clearfix" style="height: 5px;"></div>
        -->
    </div>
</div>
<script type="text/javascript">
$(document).ready(function(){
    $('#btnUpgradeEnterprise').button();
});
</script>