<div class="ym-grid">
    <div class="ym-gl ym-g25" style="text-align: center"> <span class="clevvermail-product">&nbsp;</span>
        <br/> <h3 style="font-size: 18px">ClevverMail</h3>
        <br/> Your digital postbox at worldwide locations
    </div>

    <div class="ym-gl  ym-g25"  style="text-align: center" title="Coming soon..."> <span class="clevverphone-product">&nbsp;</span>
        <br/> <h3 style="font-size: 18px">ClevverPhone</h3>
        <br/>International Phone Numbers
    </div>
    
    <div class="ym-gl  ym-g25"  style="text-align: center" title="Coming soon..."> <span class="clevvercompany-product">&nbsp;</span>
        <br/> <h3 style="font-size: 18px">ClevverCompany</h3>
        <br/>Incorporate a new international company
    </div>
    
    <div class="ym-gl  ym-g25"  style="text-align: center" title="Coming soon..."> <span class="clevverbanking-product">&nbsp;</span>
        <br/> <h3 style="font-size: 18px">ClevverBank</h3>
        <br/>Open a Business Bank Account
    </div>
</div>

<br />
<div class="ym-clearfix"></div>
<div class="ym-grid" style="text-align:center">
    <h3 style="font-size: 18px;font-weight: bold">Your Selection</h3>
</div>

<br />
<div class="ym-clearfix"></div>
<div class="ym-grid ym-text-center"  style="text-align:center">
    <select id="selectClevvermailProductWindow_selectProduct" name="selectProduct" class="input-width" style="width: 200px;padding: 5px">
        <option value="<?php echo APConstants::CLEVVERMAIL_PRODUCT?>">ClevverMail</option>
        <option value="<?php echo APConstants::CLEVVERPHONE_PRODUCT?>" disabled="">ClevverPhone (Coming soon...)</option>
        <option value="<?php echo APConstants::CLEVVERCOMPANY_PRODUCT?>" disabled="">ClevverCompany (Coming soon...)</option>
        <option value="<?php echo APConstants::CLEVVERBANK_PRODUCT?>" disabled="">ClevverBank* (Coming soon...)</option>
    </select>
</div>

<br />
<div class="ym-clearfix"></div>
<div class="ym-grid ym-text-center" style="text-align:center">
    <h4 style="font-size: 16px">More products can be added at any time in your account settings</h4>
    <!--<br />
    To open a Clevver Enterprise Account, click <a href="#" style="color: blue" id="selectClevvermailProductWindow_openEnterpriseAccount">here…</a> -->
</div>
<script src="<?php echo $this->config->item('asset_url'); ?>system/virtualpost/modules/account/js/Account.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    $("#selectClevvermailProductWindow_openEnterpriseAccount").bind('click', function(e){
        e.preventDefault();
        
        // close select product window.
        $('#selectClevvermailProductWindow').dialog('destroy');
        
        // open upgrade enterprise customer window.
        openUpgradeEnterpriseCustomerConfirm();
        
        return false;
    });
    
    $(".clevverphone-product, .clevvercompany-product, .clevverbanking-product").click(function(){
        $.displayInfor("This product will be coming soon.", null, function () {
            // do nothing.
        });
    });
    
    function openUpgradeEnterpriseCustomerConfirm() {
        // Clear control of all dialog form
        $('#upgradeEnterpriseCustomerConfirmWindow').html('');
        // Open new dialog
        $('#upgradeEnterpriseCustomerConfirmWindow').openDialog({
            autoOpen: false,
            height: 550,
            width: 700,
            modal: true,
            open: function () {
                $(this).load('<?php echo base_url() ?>account/upgrade_customer_type?setup_flag=1', function () {
                });
            }
        });
        $('#upgradeEnterpriseCustomerConfirmWindow').dialog('option', 'position', 'center');
        $('#upgradeEnterpriseCustomerConfirmWindow').dialog('open');
    }
    
    Account.init('<?php echo base_url(); ?>');
});
</script>
