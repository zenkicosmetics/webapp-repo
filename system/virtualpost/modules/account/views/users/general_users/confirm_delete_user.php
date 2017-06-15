<?php
$submit_url = base_url() . 'account/users/delete_general_users';
?>
<form id="confirmDeleteUserForm" method="post" class="dialog-form" action="<?php echo $submit_url ?>">
    <h3 style="font-size: 16px;font-weight: bold">You have deleted a user that had running products attached. Please decide what should happen to those products:</h3>
    <br />
    <table style="width: 99%; ">
        <thead>
            <tr >
                <th width="80px" style="border-bottom: 1px #d3d3d3 solid;">Product</th>
                <th width="200px;" style="border-bottom: 1px #d3d3d3 solid;">Description</th>
                <th width="100px" style="border-bottom: 1px #d3d3d3 solid;">Created date</th>
                <th width="120px" style="border-bottom: 1px #d3d3d3 solid;">End of contract</th>
                <th width="100px" style="border-bottom: 1px #d3d3d3 solid;">Contract term</th>
                <th width="50px" style="border-bottom: 1px #d3d3d3 solid;">Renew</th>
                <th  align="center" style="border-bottom: 1px #d3d3d3 solid;text-align: center">Action*</th>
            </tr>
        </thead>
    </table>
    <div style="height: 300px; overflow: auto">
        <table style="width: 99%; ">
            <tbody>
                <?php foreach($products as $p){ ?>
                <tr>
                    <td width="80px">
                        <?php echo $p->product; ?>
                        <input type="hidden" name="product_id[]" class="product-id" value="<?php echo $p->product_id; ?>" />
                        <input type="hidden" name="product_type[]" class="product-type" value="<?php echo $p->product_type; ?>" />
                        <input type="hidden" name="description[]" class="product-descrition" value="<?php echo $p->description; ?>" />
                    </td>
                    <td width="200px;"><?php echo $p->description; ?></td>
                    <td width="100px"><?php echo $p->created_date; ?></td>
                    <td width="120px"><?php echo $p->contract_date; ?></td>
                    <td width="100px"><?php echo $p->contract_term; ?></td>
                    <td width="50px">
                        <?php echo ($p->renewal == 0) ? "No" : "Auto"; ?>
                        <input type="hidden" name="renewal[]" value="<?php echo $p->renewal; ?>" />
                    </td>
                    <td>
                        <select name="actions[]" class="product-action input-width" style='width:100%;'>
                            <option value="0">Assign to other user</option>
                            <option value="1">Terminate at end of contract</option>
                            <option value="2">Terminate now (lose functionality now)</option>
                        </select>
                    </td>
                </tr>
                <?php }?>
            </tbody>
        </table>
    </div>
    
    <br />
    <p>*Please chose an action for every product to continue...</p>
    <input type="hidden" id="confirmDeleteUserForm_user_id" name="user_id" value="<?php echo $user->customer_id ?>" />

</form>
<div style="display:none;">
    <div id="confirmTargetUserWindow" title="Confirm delete user" class="input-form dialog-form"> </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $("#confirmDeleteUserWindow_saveBtn").unbind('click').live("click", function(){
            var list_product_id = new Array();
            var check_flag = false;
            
            $(".product-action").each(function(){
                var data = ""; 
                //if($(this).val() == 0){
                data = $(this).parent().parent().find(".product-id").val();
                data += "-" + $(this).parent().parent().find(".product-type").val();
                data += "-" + $(this).parent().parent().find(".product-action").val();

                list_product_id.push(data);
                //}
                if($(this).val() == 0){
                    check_flag = true;
                }
            });
            
            if(!check_flag){
                $.confirm({
                    message: 'Are you sure to delete this user? ',
                    yes: function () {
                        delete_action();
                    }
                });
                
                return;
            }

            var submitUrl = "<?php echo base_url() ?>account/users/confirm_target_user?user_id="+$("#confirmDeleteUserForm_user_id").val()+"&list_product_id="+list_product_id;
            $.openDialog('#confirmTargetUserWindow', {
                height: 450,
                width: 500,
                openUrl: submitUrl,
                title: "Confirm selection",
                closeButtonLabel: "Cancel",
                buttons:[
                    {
                        id: "saveBtn",
                        text: "Save"
                    }
                ]
            });
            return false;
        });
        
        $("#confirmDeleteUserForm_cancelBtn").live("click", function(e){
            $('#confirmDeleteUserWindow').dialog('destroy');
            return false;
        });
        
        function delete_action(){
            var submitUrl = $('#confirmDeleteUserForm').attr('action');
            $.ajaxSubmit({
                url: submitUrl,
                formId: 'confirmDeleteUserForm',
                success: function (data) {
                    if (data.status) {
                        $('#confirmDeleteUserWindow').dialog('close');
                        $.displayInfor(data.message, null, function () {
                            // Reload data grid
                            $('#confirmDeleteUserWindow').dialog('destroy');
                            location.reload();
                        });
                    } else {
                        $.displayError(data.message);
                    }
                }
            });
        }
    });
</script>