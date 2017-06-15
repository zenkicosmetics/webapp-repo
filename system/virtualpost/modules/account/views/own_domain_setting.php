<!--<form id="saveOwnDomainForm" action="<?php echo base_url() . 'account/setting/save_own_domain_setting'; ?>" method="post" >-->
    <div class="ym-grid">
        <div class="ym-gl">
            <h4 class="COLOR_063">Own domain for your user login</h4>
        </div>
        <div class="ym-gr">
            <?php 
            $CUSTOMER_OWN_DOMAIN_KEY = AccountSetting::get_alias02($customer_id, APConstants::CUSTOMER_OWN_DOMAIN_KEY);
            $OwnDomainClass = "btn-grey";
            if($CUSTOMER_OWN_DOMAIN_KEY == 1){
                $supportSettingClass = "input-btn btn-yellow";
            }
            ?>
            <!--<input style="margin: 1.125em 0 0 0" type="button" id="saveOwnDomainButton" class="btn-grey btn-yellow value="Save Domain Setting" />-->
        </div>
    </div>

    <div class="ym-grid" style="padding-top: 20px">
        <input type="checkbox" <?php if($CUSTOMER_OWN_DOMAIN_KEY == "1"){ echo "checked='checked'"; }?>
               class="customCheckbox" id="own_domain_checkbox" name="own_domain_checkbox" value="1" /> <label> Use your own domain to set up login for your users</label>
        <span class="managetables-icon icon_help tipsy_tooltip" original-title="If you activate this function, your domain will be added to our server configuration 
                                so that you can place our login widget on your own domain and users will not be redirected to the clevvermail domain to use the system."></span>
    </div>
    <div class="ym-clearfix"></div>
    <div class="ym-grid" style="padding-top: 20px" id="ownDomainDivContainer">
        <label style="display: inline-block">Your domain <span class="required">*</span>: </label>
        <input type="text" style="width: 450px;" class="input-txt" name="own_domain" id="saveOwnDomainForm_own_domain"
               value="<?php echo AccountSetting::get($customer_id, APConstants::CUSTOMER_OWN_DOMAIN_KEY) ?>" />
        <a class="main_link_color" id="ownDomainSetting" style="text-decoration: none;  ">
        <span style="display:inline-block;"><span class="managetables-icon managetables-setting-icon" title="Setting Owner Domain"></span></span>
        </a>
    </div>
<!--</form>-->
<script type="text/javascript">
$(document).ready(function(){
    $('#saveOwnDomainForm_own_domain').live('change', function() {
        var ownDomain = $('#saveOwnDomainForm_own_domain').val();
        if (ownDomain == '') {
            //$.displayError('Your domain is required input.');
            return;
        }
        openOwnDomainWidgetSetting();
    });
    
    $("#ownDomainSetting").click(function(){
        var ownDomain = $('#saveOwnDomainForm_own_domain').val();
        if (ownDomain == '') {
            //$.displayError('Your domain is required input.');
            return;
        }
        openOwnDomainWidgetSetting();
    });
    
    // Open own domain widget setting
    function openOwnDomainWidgetSetting() {
        var windowId = '#OwnDomainWidgetSettingWindow';
        var loadUrl = "<?php echo base_url()?>account/setting/own_domain_widget_setting";
        $.openDialog(windowId, {
            height: 450,
            width: 920,
            openUrl: loadUrl,
            title: "Use this code to implement the login on your own website",
            show_only_close_button: false,
            buttons: [{
                'text': 'Copy Code To Clipboard',
                'id': 'copyCodeToClipboardButton'
            }],
            callback: function(){
                //location.reload();
            }
        });
    };
});
</script>