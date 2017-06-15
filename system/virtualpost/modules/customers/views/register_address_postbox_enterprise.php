<?php
$submit_url = base_url() . 'customers/register_address_postbox_enterprise_user';
?>
<form id="registerAddressPostboxEnterpriseUserForm" method="post" class="dialog-form" action="<?php echo $submit_url ?>" style="margin-top: 0px;">
    <h2 style="font-size: 14px">Your enterprise account has a minimum of 10 users. Each user comes with a first postbox. 
        You can enter the name of the receipt, the email, a name and a company name under 
        which the user can receive mail and also select a location for the users postbox.</h2>
    <table style="margin-top: 2px;">
        <thead>
            <tr>
                <th>#</th>
                <th>Customer name</th>
                <th>Email</th>
                <th>Name for postbox</th>
                <th>Company name for postbox</th>
                <th>Location</th>
                <th>Status</th>
            </tr>
        </thead>
        
        <tbody>
            <?php $index = 0; ?>
            <?php foreach($list_user_not_activated as $user){?>
            <?php $index ++ ; 
            $readonly = "";
            $readonly_class = "";
            if($user->email == $customer->email){
                $readonly = 'readonly="readonly"';
                $readonly_class = "readonly";
            }
            ?>
            <tr>
                <td style="vertical-align: middle">
                    <span style="position: relative;top: 5px;"><?php echo $index; ?></span>
                    <input type="hidden" id="registerAddressPostboxEnterpriseUserForm_customer_id<?php echo $index ?>" name="customer_id[]" value="<?php echo $user->customer_id ?>" 
                           class="input-width" maxlength="255" style="width:120px" />
                </td>
                <td><input type="text" id="registerAddressPostboxEnterpriseUserForm_user_name<?php echo $index ?>" data-index="<?php echo $index ?>" 
                           name="user_name[]" value="<?php echo $user->user_name ?>" <?php echo $readonly ?>
                           class="input-width registerAddressPostboxEnterpriseUserForm_user_name <?php echo $readonly_class?>" maxlength="255" style="width:120px" /></td>
                <td><input type="text" id="registerAddressPostboxEnterpriseUserForm_email<?php echo $index ?>" name="email[]"   <?php echo $readonly ?>  
                           value="<?php echo $user->email ?>" class="input-width  <?php echo $readonly_class?>" maxlength="255" style="width:200px;" /></td>
                <td><input type="text" id="registerAddressPostboxEnterpriseUserForm_postbox_name<?php echo $index ?>" name="postbox_name[]"  value="<?php echo $user->postbox->name ?>" 
                           class="input-width " maxlength="255" style="width: 180px;background: #d3f5d9;" /></td>
                <td><input type="text" id="registerAddressPostboxEnterpriseUserForm_postbox_company<?php echo $index ?>" name="postbox_company[]"  value="<?php echo $user->postbox->company ?>"
                           class="input-width" maxlength="255" style="width: 180px;background: #d3f5d9;" /></td>
                <td>
                    <?php echo my_form_dropdown(array(
                    "data" => $locations,
                    "value_key" => 'id',
                    "label_key" => 'location_name',
                    "value" => $user->postbox->location_available_id,
                    "name" => 'location[]',
                    "id"    => 'location'.$index,
                    "clazz" => 'input-width',
                    "style" => 'width: 100px',
                    'show_only_express_shipping' => "1",
                    "has_empty" => false
                    ));?>
                </td>
                <td>
                    <select class="input-width" id="registerAddressPostboxEnterpriseUserForm_status<?php echo $index ?>" name="status[]"  style = "width:110px;">
                        <option value="0" <?php if ($user->status == '0') {?> selected="selected" <?php }?>>Not Activated</option>
                        <option value="1" <?php if ($user->status == '1') {?> selected="selected" <?php }?>>Activated</option>
                    </select>
                    <input type="hidden" id="registerAddressPostboxEnterpriseUserForm_postbox_id" name="postbox_id[]" value="<?php echo $user->postbox->postbox_id ?>" /> 
                </td>
            </tr>
            <?php }?>
        </tbody>
        
    </table>
    
    <h2 style="font-size: 14px">please be aware, that the name and the company name for each postbox have to be verified with 2 IDs and company document.
        The verification process depends on the selected location of the postbox.</h2>
</form>
