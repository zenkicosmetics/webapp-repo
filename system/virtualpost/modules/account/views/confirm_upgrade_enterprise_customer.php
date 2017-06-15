<style>
#upgradeCustomerTypeForm a{
	color:rgb(33,66,99);
}
</style>
<?php
    $submit_url = base_url().'account/upgrade_customer_type';
?>
<form id="upgradeCustomerTypeForm" method="post" class="dialog-form"
    action="<?php echo $submit_url?>">
    <div class="ym-grid">
        <div style="margin: 0px auto;">
    	    <h2 style="font-size: 18px; text-align: center">Do you want to upgrade your account to Enterprise type?</h2>
    	</div>
        <div style="margin: 0px auto; text-align: center; margin-top: 20px;">
            Contact us for a more information<br/>
            Call: +49 30 467 260 777<br/>
            E-Mail: mail@clevvermail.com<br/>
    	</div>
    </div>
    <div class="ym-clearfix"></div>

    <div class="ym-grid" style="margin-top: 30px;">
        <div style="margin: 0px auto; text-align: left; margin-left: 100px;">
            <div style="margin-left: 10px; font-weight: bold;">Additional benefits of the enterprise accounts:</div>
            <ul style="margin-top: 0px;">
                <li>
                    Manage your own dependent users (minimum: 10 users)
                    <ul>
                        <li>Central invoicing and payment</li>
                        <li>Users have separate login into the system</li>
                        <li>Assign administrative to users</li>
                        <li>Prepared invoices to send out to your users</li>
                    </ul>
                </li>
                <li>Use your own corporate Design and your Name for the system</li>
                <li>Use your own pricing for our services for your users</li>
                <li>Every postbox 19,95 EUR regardless of location (see full pricing <a href="#" id="upgradeCustomerTypeForm_seePricingList">here ...</a>)</li>
                <li>Yearly contracts</li>
            </ul>
    	</div>
        <div style="margin: 0px auto; text-align: center; width: 500px; margin-top: 15px;">
            <h2 style="font-size: 14px; background-color: #ffeea4; line-height: 30px; border: 1px solid #d39e00 ">
                Warning: The upgrade changes your account type permanently
            </h2>
            <div style="margin-top: 15px;">
                <input id="upgradeCustomerTypeForm_acceptTermAndCondition" type="checkbox" />Please confirm the Enterprise
                <a href="#" id="upgradeCustomerTypeForm_termAndCondition">Terms and Condition</a>
            </div>
        </div>
        <div style="width: 550px; margin: 0px auto; margin-top: 25px;">
            <div style="float: left">
                <button id="cancelUpgradeCustomerType" class="input-btn" style="width: 200px; background-color: #dadada; color: #595959;">Cancel</button>
            </div>
            <div style="float: right">
                <button id="confirmUpgradeCustomerType" class="input-btn btn-yellow" style="width: 200px; background-color: #FFC20C; color: #595959; border: 1px solid #d39e00">Upgrade to Enterprise</button>
            </div>
        </div>
    </div>

    <div class="hide" style="display: none">
        <div id="upgradeEnterpriseCustomerConfirmPostboxWindow" title="Confirmation" class="input-form dialog-form">
            <div style="margin: 0px auto; margin-top: 10px;"></div>
            <p>With your new enterprise account, you will start with 10 user accounts.
                You can now decide, if your current postboxes should be assigned only to the first account or if you want each postbox to be assigned to a different user</p>
            <input type="radio" name="separatePostboxType" id="upgradeEnterpriseCustomerConfirmPostboxWindow_separatePostbox_02" checked="checked" value="1" />
            <span>Do you want to add all postboxes for the first user?</span>
            <p></p>
            <input type="radio" name="separatePostboxType" id="upgradeEnterpriseCustomerConfirmPostboxWindow_separatePostbox_01" value="2" />
            <span>or do you want to make an individual user out of every current postbox?</span>

            <div style="margin: 0px auto; margin-top: 25px;float: right">
                <button id="upgradeEnterpriseCustomerConfirmPostboxWindowSubmit">Submit</button>
                <button id="upgradeEnterpriseCustomerConfirmPostboxWindowClose">Cancel</button>
            </div>
        </div>
    </div>
</form>

<div style="display: none">
    <input type="hidden" name="upgradeCustomerTypeForm_setup_flag" id="upgradeCustomerTypeForm_setup_flag" value="<?php echo $setup_flag;?>" />

    <div id="upgradeCustomerTypeForm_PriceListDetailDiv" title="Price list detail" class="input-form dialog-form"></div>
    <div id="upgradeCustomerTypeForm_TermAndConditionDiv" title="Term & Condition" class="input-form dialog-form"></div>
</div>
<script type="text/javascript">
$(document).ready( function() {
    $('#cancelUpgradeCustomerType, #confirmUpgradeCustomerType').button();
    $('#upgradeEnterpriseCustomerConfirmPostboxWindowSubmit, #upgradeEnterpriseCustomerConfirmPostboxWindowClose').button();
    /**
     * Back delete postbox account
     */
    $('#cancelUpgradeCustomerType').click(function(){
    	$('#upgradeEnterpriseCustomerConfirmWindow').dialog('close');
    	return false;
    });

    /**
     * Back delete postbox account
     */
    $('#confirmUpgradeCustomerType').click(function(){
        // Check checkbox value
        if ($('#upgradeCustomerTypeForm_acceptTermAndCondition:checked').length == 0) {
            $.displayError('Please accept the terms and conditions.');
            return false;
        }

        // call upgrade enterprise now if upgrade from setup process.
        if($("#upgradeCustomerTypeForm_setup_flag").val() == 1){
            saveUpgradeEnterpriseCustomer();
            return false;
        }

        $('#upgradeEnterpriseCustomerConfirmPostboxWindow').openDialog({
            autoOpen: false,
            height: 230,
            width: 700,
            modal: true,
            open: function () {
            }
        });
        $('#upgradeEnterpriseCustomerConfirmPostboxWindow').dialog('option', 'position', 'center');
        $('#upgradeEnterpriseCustomerConfirmPostboxWindow').dialog('open');
    });

    /**
     * Back delete postbox account
     */
    $('#upgradeEnterpriseCustomerConfirmPostboxWindowClose').bind('click',function(){
        $('#upgradeEnterpriseCustomerConfirmPostboxWindow').dialog('close');
        $('#upgradeEnterpriseCustomerConfirmWindow').dialog('close');
    	return false;
    });

    /**
     * Back delete postbox account
     */
    $('#upgradeEnterpriseCustomerConfirmPostboxWindowSubmit').bind('click', function(){
        $('#upgradeEnterpriseCustomerConfirmPostboxWindow').dialog('close');
        saveUpgradeEnterpriseCustomer();
        $('#upgradeEnterpriseCustomerConfirmWindow').dialog('close');
    	return false;
    });

    // Submit the change
    function saveUpgradeEnterpriseCustomer() {
        var separatePostboxType = $('input[name="separatePostboxType"]:checked').val();
        var setup_flag = $('#upgradeCustomerTypeForm_setup_flag').val();
        console.log('Start saveUpgradeEnterpriseCustomer');
        console.log(setup_flag);
        $.ajaxExec({
            url: "<?php echo base_url() ?>account/upgrade_customer_type" + '?separatePostboxType=' + separatePostboxType,
            data: {
                setup_flag: setup_flag
            },
            success: function (data) {
                if (data.status) {
                    $.displayInfor(data.message, null, function () {
                        document.location = "<?php echo base_url() ?>mailbox/index";
                    });
                } else {
                    $.displayError(data.message);
                }
            }
        });
        return false;
    }

    // see full pricing.
    $("#upgradeCustomerTypeForm_seePricingList").live('click', function(){
        $("#upgradeCustomerTypeForm_PriceListDetailDiv").html("");
        var location_id = 1;

        // Open new dialog
        $('#upgradeCustomerTypeForm_PriceListDetailDiv').openDialog({
            autoOpen: false,
            height: 550,
            width: 1200,
            modal: true,
            closeOnEscape: false,
            open: function(event, ui) {
                $(this).load("<?php echo base_url() ?>customers/load_price_list_detail?location_id="+location_id+"&type=<?php echo APConstants::ENTERPRISE_CUSTOMER ?>", function() {
                });
            }
        });

        $('#upgradeCustomerTypeForm_PriceListDetailDiv').dialog('option', 'position', 'center');
        $('#upgradeCustomerTypeForm_PriceListDetailDiv').dialog('open');
    });

    $("#upgradeCustomerTypeForm_termAndCondition").live('click', function(){
        $("#upgradeCustomerTypeForm_TermAndConditionDiv").html("");

        // Open new dialog
        $('#upgradeCustomerTypeForm_TermAndConditionDiv').openDialog({
            autoOpen: false,
            height: 620,
            width: 1100,
            modal: true,
            open: function() {
                $(this).load("<?php echo base_url() ?>customers/term_of_service", function() {
                });
            },
            buttons: {
                'Close': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#upgradeCustomerTypeForm_TermAndConditionDiv').dialog('option', 'position', 'center');
        $('#upgradeCustomerTypeForm_TermAndConditionDiv').dialog('open');
        return false;
    });
});
</script>