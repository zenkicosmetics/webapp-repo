<style>
    #addEditUserForm tr td, #addEditUserForm tr th {
        padding: 0.4em 0.5em;
    }
</style>
<form id="addEditUserForm" method="post" style="margin-top: 5px;" class="dialog-form" action="<?php echo $submit_url ?>">
    <table class="user_setting_table" style="margin-top: 5px;">
    	<tr>
            <?php if($action_type == 'edit' && $user->customer_id != APContext::getParentCustomerCodeLoggedIn()) { ?>
            <th>Status</th>
            <td>
                <select class="input-width" id="status_flag" name="status_flag" style = "width: 262px;">
                    <option value="0" <?php echo $user->activated_flag == 0 ? 'selected="selected"' : ''; ?>><?php echo lang('users.status.not_activated') ?></option>
                    <option value="1" <?php echo $user->activated_flag == 1 ? 'selected="selected"' : ''; ?>><?php echo lang('users.status.activated') ?></option>
                </select>
            </td>
            <?php } else { ?>
            <th>Products <span class="required">*</span></th>
            <td>
                <select class="input-width" id="addEditUserForm_product_type" name="product_type" style = "width: 262px;">
                    <?php if($is_enterprise_customer){ ?>
                    <option value="postbox" <?php echo $product_type == 'postbox' ? 'selected="selected"' : ''; ?>>Postbox</option>
                    <?php }?>
                    <option value="phone" <?php echo $product_type == 'phone' ? 'selected="selected"' : ''; ?>>Phone</option>
                </select>
            </td>
            <?php }?>
            <th>Rights <span class="required">*</span></th>
            <td>
                <select class="input-width" id="role_flag" name="role_flag" style = "width: 262px;">
                    <option value="0" <?php echo $user->role_flag == 0 ? 'selected="selected"' : ''; ?>><?php echo lang('users.role_0');?></option>
                    <option value="1" <?php echo $user->role_flag == 1 ? 'selected="selected"' : ''; ?>><?php echo lang('users.role_1');?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th>Name <span class="required">*</span></th>
            <td><input type="text" id="addEditUserForm_name" name="user_name" value="<?php echo $user->user_name ?>" class="input-width" maxlength=50 /></td>
            <th>E-mail <span class="required">*</span></th>
            <td><input type="text" id="addEditUserForm_email" name="email" value="<?php echo $user->email ?>" class="input-width" maxlength=250 <?php if($action_type == 'edit') { ?>disabled="disabled"<?php } ?> /></td>
        </tr>
        
        <?php if($action_type == 'edit'){ ?>
        <tr>
            <td colspan="2"><button type="button" style="width: 43%" id="btnManageForwardingAddress" class="input-btn btn-yellow">Forwarding Address</button>
                <button type="button" style="width: 45%" id="btnManageInvoiceAddress" class="input-btn btn-yellow">Invoice address</button></td>
            <th>&nbsp;</th>
            <td>
                <?php if($action_type == 'edit'){ ?>
                <a href="#" id="addEditUserForm_changeEmailLink" class="main_link_color"  style="margin-left: 10px;">Change email</a>
                <a href="#" id="addEditUserForm_resendEmailLink" class="main_link_color"  style="margin-left: 10px;">Resend email confirmation</a>
                <?php }?>
            </td>
        </tr>
        <?php }?>
        <tr>
            <th>User Type <span class="required">*</span></th>
            <td>
                <?php echo code_master_form_dropdown(array(
                    "code"  => APConstants::CUSTOMER_TYPE_CODE,
                    "value" => (!empty($user->customer_type)) ? $user->customer_type: '',
                    "name"  => 'customer_type',
                    "id"    => 'customer_type',
                    "clazz" => 'input-width',
                    "style" => 'width: 262px;',
                    "has_empty" => false
                ));?>
            </td>
            <th>&nbsp;</th>
            <td>
                <?php if($action_type == 'edit'){ ?>
                <a href="#" id="addEditUserForm_changePasswordLink" class="main_link_color"  style="margin-left: 10px;">Change password</a>
                <?php
                if($user->password == md5($parent_customer_id."@1")){
                    echo "Currently: ".$parent_customer_id."@1";
                } ?>
                <?php }?>
            </td>
        </tr>
        <?php if($action_type == 'edit'): ?>
        <tr>
            <th>Last Login</th>
            <td>
                <span style="margin-left: 10px">
                <?php echo APUtils::convert_timestamp_to_date($user->last_access_date) ?>
                </span>
            </td>
            <?php 
            $current_vat = $vat->rate;
            if(isset($user->vat_rate) && $user->vat_rate != null){
                $current_vat = $user->vat_rate;
            }
            ?>
            <?php if($is_enterprise_customer){ ?>
            <th>VAT (%)</th>
            <td><input type="text" id="addEditUserForm_vat_rate" name="vat_rate" value="<?php echo $current_vat; ?>" class="input-width " maxlength=50 /></td>
            <?php } else {?>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <?php }?>
        </tr>
        <?php endif; ?>
        <?php if($action_type == 'add'): ?>
        <tr>
            <th>Password <span class="required">*</span></th>
            <td><input type="password" id="addEditUserForm_password" name="password" value="" class="input-width custom_autocomplete" maxlength=50 /></td>
            <th>Retype Password <span class="required">*</span></th>
            <td><input type="password" id="addEditUserForm_repeat_password" name="repeat_password" value=""	class="input-width custom_autocomplete" maxlength=50 /></td>
        </tr>
        <?php endif; ?>
        
        <tr>
            <th>Language</th>
            <td>
                <?php echo my_form_dropdown(array(
                    "data" => $languages,
                    "value_key" => 'language',
                    "label_key" => 'language',
                    "value" => (!empty($user->language)) ? $user->language: '',
                    "name" => 'language',
                    "id" => 'language',
                    "clazz" => 'input-width',
                    "style" => 'width: 262px;',
                    "has_empty" => false
                 )); ?>
            </td>
            <th>Currency</th>
            <td>
                <?php echo my_form_dropdown(array(
                    "data" => $currencies,
                    "value_key" => 'currency_id',
                    "label_key" => 'currency_short',
                    "value" =>  (!empty($user->currency_id)) ? $user->currency_id :'',
                    "name" => 'currency_id',
                    "id" => 'currency_id',
                    "clazz" => 'input-width',
                    "style" => 'width: 262px;',
                    "has_empty" => false
                 )); ?>    
            </td>
        </tr>
        <tr>
            <th>Decimal Separator</th>
            <td>
                <?php echo code_master_form_dropdown(array(
                    "code"  => APConstants::DECIMAL_SEPARATOR_CODE,
                    "value" => (!empty($user->decimal_separator)) ? $user->decimal_separator: '',
                    "name"  => 'decimal_separator',
                    "id"    => 'decimal_separator',
                    "clazz" => 'input-width',
                    "style" => 'width: 262px;',
                    "has_empty" => false
                ));?>
            </td>
            <th>Date</th>
            <td>
                <?php echo code_master_form_dropdown(array(
                    "code"  => APConstants::DATE_FROMAT_01_CODE,
                    "value" => (!empty($user->date_format)) ? $user->date_format: '',
                    "name"  => 'date_format',
                    "id"    => 'date_format',
                    "clazz" => 'input-width',
                    "style" => 'width: 262px;',
                    "has_empty" => false
                ));?>
            </td>
        </tr> 
    </table> 
    
    <input type="hidden" id="h_action_type" name="h_action_type" value="<?php echo $action_type ?>" />
    <input type="hidden" id="addEditUserForm_customer_id" name="customer_id" value="<?php echo $user->customer_id; ?>" />
</form>
<div class="hide" style="display: none">
    <div id="changeMyEmailWindow" title="Change My Email" class="input-form dialog-form"></div>
    <div id="assignPostboxUser" title="Add Postbox To User" class="input-form dialog-form"></div>
    <div id="forward_address" title="Manage forwarding address" class="input-form dialog-form"></div>
</div>
<script type="text/javascript">
$("#btnManageForwardingAddress, #btnManageInvoiceAddress").button();

$("#btnManageForwardingAddress").click(function(){
    openManageAddressWindow(function(){
        // do nothing
    }, $("#addEditUserForm_customer_id").val());
});

$("#btnManageInvoiceAddress").click(function(){
    $("#updateInvoiceAddressWindow").html("");
    $.openDialog('#updateInvoiceAddressWindow', {
        height: 550,
        width: 600,
        openUrl: "<?php echo base_url() ?>account/invoice_address?customer_id=" + $("#addEditUserForm_customer_id").val(),
        title: "Invoice address",
        closeButtonLabel: "Cancel",
        show_only_close_button: true,
        callback: function(){
            // do nothing.
        },
        buttons:[
            {
                id: "saveBtn",
                text: "Save"
            }
        ]
    });
});

/** START SOURCE TO manage address */
<?php include 'system/virtualpost/modules/addresses/js/js_manage_address.php'; ?>
/** START SOURCE TO manage address */
        
</script>
