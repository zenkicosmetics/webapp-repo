<?php
$submit_url = base_url() . 'account/users/change_postbox_location';
?>
<form id="changePostboxLocationForm" method="post" class="dialog-form"
      action="<?php echo $submit_url ?>">
    <table>
        <tr>
            <th>Postbox Code</th>
            <td><input type="text" id="changePostboxLocationForm_postbox_code" name="postbox_code"
                       value="<?php echo $postbox->postbox_code ?>" readonly="readonly"
                       class="input-width readonly" maxlength=50 /></td>
        </tr>
        <tr>
            <th>Name</th>
            <td><input type="text" id="changePostboxLocationForm_name" name="name"
                       value="<?php echo $postbox->name ?>"
                       class="input-width" maxlength=50 /></td>
        </tr>
        <tr>
            <th>Company</th>
            <td><input type="text" id="changePostboxLocationForm_company" name="company"
                       value="<?php echo $postbox->company ?>" 
                       class="input-width" maxlength=50 /></td>
        </tr>
        <tr>
            <th>Verification status</th>
            <td><span style="margin-left: 10px;">
                <?php 
                if($customer->required_verification_flag == 0){
                    echo "None";
                } else{
                    if($postbox->name_verification_flag == 0 || $postbox->company_verification_flag == 0){
                        echo "Incomplete";
                    }else{
                        echo "Completed";
                    }
                }
                ?></span>
            </td>
        </tr>
        <tr>
            <th>Current Location</th>
            <td><input type="text" id="changePostboxLocationForm_location" name="current_location"
                       value="<?php echo $postbox->current_location ?>" readonly="readonly"
                       class="input-width readonly" maxlength=50 /></td>
        </tr>
        <tr>
            <th>New Location <span class="required">*</span></th>
            <td>
                <?php echo my_form_dropdown(array(
                    "data" => $locations,
                    "value_key" => 'id',
                    "label_key" => 'location_name',
                    "value" => $postbox->location_available_id,
                    "name" => 'location_id',
                    "id"    => 'changePostboxLocationForm_location_id',
                    "clazz" => 'input-width',
                    "style" => 'width: 130px',
                    'show_only_express_shipping' => "1",   
                    "has_empty" => false
                 ));?>
            </td>
        </tr>
    </table>
    <input type="hidden" id="changePostboxLocationForm_postbox_id" name="postbox_id"
           value="<?php echo $postbox->postbox_id ?>" />
    <input type="hidden" id="changePostboxLocationForm_current_location_id" name="current_location_id"
           value="<?php echo $postbox->location_available_id ?>" />
    <input type="hidden" id="changePostboxLocationForm_postbox_user_id" name="postbox_user_id"
           value="<?php echo $postbox_user_id ?>" />
</form>
<script type="text/javascript">
$(document).ready(function(){
    $('#ChangeUserPostboxLocationWindow_changePostboxLocationButton').click(function(){
        var submitUrl = $('#changePostboxLocationForm').attr('action');
        if ($.isEmpty(submitUrl)) {
            return;
        }
        var current_location_id = $('#changePostboxLocationForm_current_location_id').val();
        if (current_location_id == $('#changePostboxLocationForm_location_id').val()) {
            $.displayError("Please select the new location.");
            return;
        }
        
        var name = $("#changePostboxLocationForm_name").val();
        var company = $("#changePostboxLocationForm_company").val();
        if(name == "" && company == ""){
            $.displayError("Name or company field is must required!");
            return;
        }
        
        var warningMessage = 'If switched, all items from the old postbox will be trashed and must be verification again';
        // Show confirm dialog
        $.confirm({
            message: warningMessage,
            yes: function () {
                $.ajaxSubmit({
                    url: submitUrl,
                    formId: 'changePostboxLocationForm',
                    success: function (response) {
                        if (response.status) {
                            $.displayInfor(response.message, '', function (){
                                // call from user list
                                if ($('#changePostboxLocationForm_postbox_user_id').val() != '') {
                                    GeneralUsers.searchUser();
                                    GeneralUsers.searchProducts();
                                }
                                $('#ChangeUserPostboxLocationWindow').dialog('close');
                                $("#ChangeUserPostboxLocationWindow").find('.ui-dialog-titlebar-close').click();
                            }); 
                        } else {
                            $.displayError(response.message);
                        }
                    }
                });
            }
        });
    });
});
</script>