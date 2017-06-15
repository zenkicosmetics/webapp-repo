<?php
    $submit_url = base_url().'account/confirm_delete_postbox';
?>
<form id="delPostboxConfirmForm" method="post" class="dialog-form"
    action="<?php echo $submit_url?>">
    <div class="ym-grid" style="margin-left: 20px; width: 380px;">
        <div style="margin: 10px 0 0 0px;">
    	    <h2 style="font-size: 18px;">Do you really want to delete your account?</h2>
    	</div>
    </div>

	<div class="ym-clearfix"></div>

	<div class="ym-grid" style="margin-left: 20px;margin-top: 8px;width: 380px;">
		<div class="ym-gl input-cols" style="">
			<div class="ym-gl register_label" style="float: left;">
				<div style="color: red">
				    <div>If you choose this option, your postbox will be deleted right now.</div>
				    <div>- Your account will be delete immediately</div>
                                    <div>- All current activities from this postbox will be cancelled </div>
				    <div>- All items in this postbox will be permanently destroyed and the digital files deleted</div>
				    <div><strong>This cannot be undone!</strong></div>
				</div>
				<button id="deleteMainPostboxAccount" style="margin-top: 20px; width: 180px; background: #F27724; border: 1px solid #F27724;color: #FFFFFF;box-shadow:none">Delete Immediately</button>
                                <button id="backDeleteMainPostboxAccount" style="float: right;margin-top: 20px; background: rgb(114, 179,71); border: 1px solid rgb(114, 179,71);color: #FFFFFF;box-shadow:none">Back</button>
			</div>
			
			<div class="ym-clearfix"></div>
		</div>
	</div>
	<input type="hidden" name="delete_type" id="delPostboxConfirmForm_deleteType" value="">
</form>
<script type="text/javascript">
$(document).ready( function() {
    $('button').button();
    /**
     * Direct delete postbox account
     */
    $('#deleteMainPostboxAccount').click(function(){
    	$('#delPostboxConfirmForm_deleteType').val('2');
    	var submitUrl = $('#delPostboxConfirmForm').attr('action');
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'delPostboxConfirmForm',
            success: function(data) {
                if (data.status) {
                    $.displayInfor(data.message, null, function(){
                    	document.location = '<?php echo base_url()?>customers/logout';
                    });
                } else {
                    $.displayError(data.message);
                }
                $('#delPostboxConfirmWindow').dialog('close');
            }
        });
    	return false;
    });

    /**
     * Back delete postbox account
     */
    $('#backDeleteMainPostboxAccount').click(function(){
    	$('#delPostboxConfirmWindow').dialog('close');
    	return false;
    });
});
</script>