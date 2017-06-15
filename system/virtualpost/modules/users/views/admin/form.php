<?php
if ($action_type == 'add') {
    $submit_url = base_url () . 'users/admin/add';
} else {
    $submit_url = base_url () . 'users/admin/edit';
}
?>
<form id="addEditUserForm" method="post" action="<?php echo $submit_url?>">
    <table id="tbl_frm_user">
        <tr>
            <td width="50%">
                <table>
                    <tr>
                        <th>User Name <span class="required">*</span></th>
                        <td><input type="text" id="username" name="username" value="<?php echo $user->username?>"
                            class="input-width txt_username custom_autocomplete" maxlength=50 /></td>
                    </tr>
                    <tr>
                        <th>E-mail <span class="required">*</span></th>
                        <td><input type="text" id="email" name="email" value="<?php echo $user->email?>" class="input-width custom_autocomplete"
                            maxlength=50 /></td>
                    </tr>
                     
                    <tr>
                        <th>Activate</th>
                        <td><input type="checkbox" id="active" name="active" value="1" class="input-width customCheckbox" maxlength=50
                            style="width: 20px" <?php if ($user->active == '1') { ?> checked="checked" <?php }?> /></td>
                    </tr>
                    
                    <tr>
                        <th>Group <span class="required">*</span></th>
                        <td>
                            <table style="width:400px;">
                                <tr>
                                    <td>
                                        <?php echo my_form_dropdown(array(
                                            "data" => $groups,
                                            "value_key" => 'id',
                                            "label_key" => 'description',
                                            "value" => '',
                                            "name" => 'tmp_group_id',
                                            "id" => 'tmp_group_id',
                                            "clazz" => 'input-width-150',
                                            "style" => ' height:100px',
                                            "has_empty" => false ,
                                            "html_option" => 'multiple = "true"'
                                        )); ?>
                                    </td>
                                    <td valign="middle" style="vertical-align: middle">
                                        <button type="button" id="addButton"> &gt;&gt;</button>
                                        <br/>
                                        <button type="button" id="removeButton"> &lt;&lt;</button>
                                    </td>
                                    <td>
                                        <?php echo my_form_dropdown(array(
                                            "data" => $selected_group,
                                            "value_key" => 'group_id',
                                            "label_key" => 'description',
                                            "value" => '',
                                            "name" => 'group_id[]',
                                            "id" => 'group_id',
                                            "clazz" => 'input-width-150',
                                            "style" => ' height:100px',
                                            "has_empty" => false,
                                            "html_option" => 'multiple = "true" required="required"'
                                        )); ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <th>Locations <span class="required">*</span></th>
                        <td>
                            <table style="width:400px;">
                                 <tr>
                                    <td>
                                        <select multiple id="location_users" class="input-txt-none" style="width: 150px; height:100px">
                                            <?php foreach($locations as $location): ?>
                                                <option value="<?php echo $location->id; ?>"><?php echo $location->location_name; ?></option>
                                            <?php endforeach; ?>
                                        </select> 
                                    </td>
                                    <td valign="middle" style="vertical-align: middle">
                                        <button type="button" id="btnAddLocation"> &gt;&gt;</button>
                                        <br/>
                                        <button type="button" id="btnRemoveLocation"> &lt;&lt;</button>
                                    </td>
                                    <td>
                                        <select multiple name="location_users_available[]" id="location_users_available" class="input-txt-none" style="width: 150px; height:100px">
                                            <?php if(!empty($location_users_available)) { 
                                                foreach($location_users_available as $location): ?>
                                                <option selected value="<?php echo $location->id; ?>"><?php echo $location->location_name; ?></option>
                                            <?php endforeach; }?>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
            <td width="50%">
                <table>
                    
                    <tr>
                        <th>First Name <span class="required">*</span></th>
                        <td><input type="text" id="first_name" name="first_name" value="<?php echo $user->first_name?>"
                            class="input-width txt_right custom_autocomplete" maxlength=100 /></td>
                    </tr>
                    <tr>
                        <th>Last Name <span class="required">*</span></th>
                        <td><input type="text" id="last_name" name="last_name" value="<?php echo $user->last_name?>"
                            class="input-width txt_right custom_autocomplete" maxlength=100 /></td>
                    </tr>
                    
                   <?php if ($action_type == 'add') {?>
            		<tr>
                        <th>Password <span class="required">*</span></th>
                        <td><input type="password" id="password" name="password" value="" class="input-width custom_autocomplete txt_right" maxlength=100 /></td>
                    </tr>
                    <tr>
                        <th>Re-type Password <span class="required">*</span></th>
                        <td><input type="password" id="repeat_password" name="repeat_password" value="" class="input-width custom_autocomplete txt_right"
                            maxlength=100 /></td>
                    </tr>
            		<?php } ?>
            		<?php // #1058 add multi dimension capability for admin ?>
                     <tr>
                        <th>Language</th>
                        <td>
                        	 <?php echo my_form_dropdown(array(
                              		 "data" => $languages,
                                     "value_key" => 'code',
                                     "label_key" => 'code',
                                     "value" => (!empty($profile_member->language))  ? $profile_member->language: '',
                                     "name" => 'language',
                                     "id" => 'language',
                                     "clazz" => 'input-txt-none select_right',
                                     "style" => 'height:28px;',
                                     "has_empty" => false ,
                              )); ?>
                         </td>             
                    </tr>
                    <tr>
                        <th>Currency</th>
                        <td>
	                       	 <?php echo my_form_dropdown(array(
	                         		"data" => $currencies,
	                                "value_key" => 'currency_id',
	                                "label_key" => 'currency_short',
	                                "value" =>  (!empty($profile_member->currency_id)) ? $profile_member->currency_id :'',
	                                "name" => 'currency_id',
	                                "id" => 'currency_id',
	                                "clazz" => 'input-txt-none select_right',
	                                "style" => 'height:28px;',
	                          		"has_empty" => false ,
	                          )); ?>    
                        </td>
                    </tr>
                    <tr>
                        <th>Cm/Inch</th>
                        <td>
                         	<?php echo code_master_form_dropdown(array(
                				"code"  => APConstants::LENGTH_UNIT_CODE,
                                "value" => (!empty($profile_member->length_unit)) ? $profile_member->length_unit: '',
                                "name"  => 'length_unit',
                                "id"	=> 'length_unit',
                                "clazz" => 'input-txt-none select_right',
                                "style" => 'height:28px;',
                			    "has_empty" => false
                			));?>
                        </td>
                    </tr>
                    <tr>
                        <th>Ounce/g</th>
                        <td>
                        	<?php echo code_master_form_dropdown(array(
                				"code"  => APConstants::WEIGHT_UNIT_CODE,
                                "value" => (!empty($profile_member->weight_unit)) ? $profile_member->weight_unit: '',
                                "name"  => 'weight_unit',
                                "id"	=> 'weight_unit',
                                "clazz" => 'input-txt-none select_right',
                                "style" => 'height:28px;',
                			    "has_empty" => false
                			));?>
                        </td>
                    </tr>
                    <tr>
                        <th>Decimal Separator</th>
                        <td>
                        	<?php echo code_master_form_dropdown(array(
                				"code"  => APConstants::DECIMAL_SEPARATOR_CODE,
                                "value" => (!empty($profile_member->decimal_separator)) ? $profile_member->decimal_separator: '',
                                "name"  => 'decimal_separator',
                                "id"	=> 'decimal_separator',
                                "clazz" => 'input-txt-none select_right',
                                "style" => 'height:28px;',
                			    "has_empty" => false
                			));?>
                        </td>
                    </tr>
					<tr>
                        <th>Date</th>
                        <td>
                        <?php echo code_master_form_dropdown(array(
                				"code"  => APConstants::DATE_FROMAT_01_CODE,
                                "value" => (!empty($profile_member->date_format)) ? $profile_member->date_format: '',
                                "name"  => 'date_format',
                                "id"	=> 'date_format',
                                "clazz" => 'input-txt-none select_right',
                                "style" => 'height:28px;',
                			    "has_empty" => false
                			));?>
                        </td>
                    </tr> 
                    </tr>
                    <tr>
                        <th style="text-align: left;">Send email for new and deleted customer</th>
                        <td style="vertical-align: middle"><input class="notification_customer_flag" <?php echo (isset($enable) && (!$enable)) ? "disabled":""; ?> <?php echo (isset($user->sent_notification_customer_flag) && $user->sent_notification_customer_flag) ?  "checked":""; ?> type="checkbox" value="<?php echo isset($user->sent_notification_customer_flag) ? $user->sent_notification_customer_flag: '';?>" name="sent_notification_customer_flag" id="sent_notification_customer_flag"> </td>
                    </tr>
                    <tr id="email_notify">
                        <th style="text-align: left;">Info Email</th>
                        <td><input value="<?php echo isset($user->info_email) ? $user->info_email:'';?>" type="text" class="input-width txt_right notification_customer_flag" name="info_email" id="info_email"></td>
                    </tr>
                    <?php // End #1058 add multi dimension capability for admin ?>                   
                </table>
            </td>
        </tr>
    </table>
    <input type="hidden" id="h_action_type" name="h_action_type" value="<?php echo $action_type?>" /> <input type="hidden" id="h_user_id" name="id"
        value="<?php echo $user->id?>" />
</form>
<script type="text/javascript">
$(document).ready( function() {
    $('input:checkbox.customCheckbox').checkbox({cls:'jquery-safari-checkbox'});
    
   if($("#sent_notification_customer_flag").is(':checked')){
        $("#sent_notification_customer_flag").val(1);
        //$("#info_email").attr("disabled",false);
    }
       
    $("#sent_notification_customer_flag").click(function(){

        if($("#sent_notification_customer_flag").is(':checked')){
            $("#sent_notification_customer_flag").val(1);
            //$("#info_email").attr("disabled",false);
        }
        /*
        else {
            $("#info_email").attr("disabled",true);
        }
        */
     
    });
   
    // Event: add, remove a pricing template
    $("#addButton").click( function () {
        $("#tmp_group_id > option:selected").each(function () {
            $(this).remove().appendTo("#group_id");
        });
        $("#group_id > option").each(function () {
            console.log($(this).val());
            var group_id = $(this).val();
            if(group_id == '<?php echo APConstants::GROUP_SUPER_ADMIN ?>' || group_id == '<?php echo APConstants::GROUP_ADMIN ?>' || group_id == '<?php echo APConstants::GROUP_LOCATION_ADMIN ?>'){
                $(".notification_customer_flag").attr("disabled",false);
            }
        });
    });
    $("#removeButton").click( function () {
        // Remove all selected pricing templates
        $("#group_id > option:selected").each(function () {
            $(this).remove().appendTo("#tmp_group_id");
        });
        
        $("#group_id > option").each(function () {
            console.log($(this).val());
            var group_id = $(this).val();
            if(group_id == '<?php echo APConstants::GROUP_SUPER_ADMIN ?>' || group_id == '<?php echo APConstants::GROUP_ADMIN ?>' || group_id == '<?php echo APConstants::GROUP_LOCATION_ADMIN ?>'){
                
                $(".notification_customer_flag").attr("disabled",false);
            }
            else {
                $(".notification_customer_flag").attr("disabled",true);
            }
            $(this).attr("selected",true);
        });
           
    });
/* *************************************************************************** */
    $("#btnAddLocation").click( function () {
        $("#location_users > option:selected").each(function () {
            $(this).remove().appendTo("#location_users_available").attr("selected",true);

        });
    });

    $("#btnRemoveLocation").click( function () {
        $("#location_users_available > option:selected").each(function () {
            $(this).remove().appendTo("#location_users");
        });
        $("#location_users_available > option").each(function () {
            $(this).attr("selected",true);
        });
    });
    
    
});
</script>