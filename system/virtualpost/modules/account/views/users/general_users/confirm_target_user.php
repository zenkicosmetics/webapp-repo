<?php
$submit_url = base_url() . 'account/users/delete_general_users';
?>
<form id="confirmTargetUserForm" method="post" class="dialog-form" action="<?php echo $submit_url ?>">
    <h3 style="font-size: 16px;font-weight: bold">Select the target users for your products</h3>
    <br />
    <table style="width: 99%; ">
        <thead>
            <tr>
                <th width="360px" style="border-bottom: 1px #d3d3d3 solid;">Product</th>
                <th style="border-bottom: 1px #d3d3d3 solid;">Renew</th>
            </tr>
        </thead>
    </table>
    <div style="height: 180px; overflow: auto">
        <table style="width: 99%; ">
            <tbody>
                <?php foreach($list_product as $p){ 
                    if($p->action == '0'){ ?>
                        <tr>
                            <td width="360px" style="vertical-align: middle">
                                <?php echo $p->description; ?>
                                <input type="hidden" name="product_id[]" value="<?php echo $p->product_id; ?>" />
                                <input type="hidden" name="product_type[]" value="<?php echo $p->type; ?>" />
                                <input type="hidden" name="actions[]" value="<?php echo $p->action; ?>" />
                                
                                <select name="user_ids[]" class="input-width" style='width:100px; float: right'>
                                    <?php foreach($list_customer as $customer){ ?>
                                    <option value="<?php echo $customer->customer_id ?>"><?php echo $customer->customer_code ?></option>
                                    <?php }?>
                                </select>
                            </td>
                            <td style="vertical-align: middle">
                                <input type="checkbox" name="renewal[]" value="<?php echo $p->product_id; ?>" checked="checked" />
                            </td>
                        </tr>
                    <?php }else{?>
                            <input type="hidden" name="product_id[]" value="<?php echo $p->product_id; ?>" />
                            <input type="hidden" name="product_type[]" value="<?php echo $p->type; ?>" />
                            <input type="hidden" name="actions[]" value="<?php echo $p->action; ?>" />
                    <?php }?>
                <?php }?>
            </tbody>
        </table>
    </div>
    <input type="hidden" id="confirmTargetUserForm_user_id" name="user_id" value="<?php echo $user_id ?>" />
    <br />
    <p>If postbox has at least 1 item, the postbox can not assign to another. it will be deleted.</p>
</form>
<script type="text/javascript">
    $(document).ready(function(){
        $("#confirmTargetUserWindow_saveBtn").unbind('click').live('click', function(e){
            var submitUrl = $('#confirmTargetUserForm').attr('action');
            $.ajaxSubmit({
                url: submitUrl,
                formId: 'confirmTargetUserForm',
                success: function (data) {
                    if (data.status) {
                        $.displayInfor(data.message, null, function () {
                            $('#confirmDeleteUserWindow').dialog('close');
                            $('#confirmTargetUserWindow').dialog('close');
                            location.reload();
                        });
                    } else {
                        $.displayError(data.message);
                    }
                }
            });
            return false;
        });
    });
</script>