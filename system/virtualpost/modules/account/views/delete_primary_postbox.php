<?php
    $submit_url = base_url().'account/delete_postbox';
?>
<form id="delPostboxConfirmForm" method="post" class="dialog-form" action="<?php echo $submit_url?>">
	<div class="ym-grid" style="margin-left: 20px; width: 720px; margin-top: 30px;">
		<div class="ym-gl input-cols" style="width: 720px">
			<div class="ym-gl register_label" style="width: 220px; float: left;">
				<div style="height: 140px;color: red">
				    <div>If you choose this option, your postbox will be deleted right now.</div>
				    <div>- All current activities from this postbox will be cancelled </div>
				    <div>- All items in this postbox will be permanently destroyed and the digital files deleted</div>
				    <div><strong>This cannot be undone!</strong></div>
				</div>
				<button id="cancelDeleteMainPostboxAccount" style="margin-top: 20px; width: 180px; background: #F27724; border: 1px solid #F27724;color: #FFFFFF;box-shadow:none">Cancel account</button>
			</div>
			<div class="ym-gl register_label" style="width: 220px; float: left; margin-left: 20px;">
			    <div style="height: 140px;color: red">
                 All items will be deleted right now.<br/>   
			     If you choose this option, your postbox will be automatically deleted at the end of your current invoicing period (end of month). You can receive your account at any time until it is deleted.
			     <br />
			     The account will continue working until end of month
                 </div>
				<button id="deleteMainPostboxAccount" style="margin-top: 20px; width: 180px; background: #F27724; border: 1px solid #F27724;color: #FFFFFF;box-shadow:none">Delete account</button>
			</div>
			<div class="ym-gl register_label" style="width: 220px; float: left; margin-left: 20px;">
				<div style="height: 140px; visibility: hidden;">If you choose this option, your account will be deleted right now.<br/>
                        - Your login will not be working <br/>
                        - Your settings and all files will be deleted <br/>
                        This cannot be undone!</div>
				<button id="backDeleteMainPostboxAccount" style="margin-top: 20px; width: 180px; background: rgb(114, 179,71); border: 1px solid rgb(114, 179,71);color: #FFFFFF;box-shadow:none">Back</button>
			</div>
			<div class="ym-clearfix"></div>
		</div>
	</div>
	
	<div><label>Please select a new primary location: </label> <select><option>test</option></select></div>
	
	<div class="ym-grid" style="margin-left: 20px; width: 720px; margin-top: 30px;">
		<div class="ym-gl input-cols" style="width: 720px">
				<button id="cancelDeleteMainPostboxAccount" style="margin-top: 20px; width: 180px; background: #F27724; border: 1px solid #F27724;color: #FFFFFF;box-shadow:none">Cancel account</button>
			</div>
			<div class="ym-gl register_label" style="width: 220px; float: left; margin-left: 20px;">
				<button id="deleteMainPostboxAccount" style="margin-top: 20px; width: 180px; background: #F27724; border: 1px solid #F27724;color: #FFFFFF;box-shadow:none">Delete account</button>
			</div>
			<div class="ym-gl register_label" style="width: 220px; float: left; margin-left: 20px;">
				<button id="backDeleteMainPostboxAccount" style="margin-top: 20px; width: 180px; background: rgb(114, 179,71); border: 1px solid rgb(114, 179,71);color: #FFFFFF;box-shadow:none">Back</button>
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
     * Delete on the end of cycle.
     */
    $('#cancelDeleteMainPostboxAccount').click(function(){
    	$('#delPostboxConfirmForm_deleteType').val('1');
    	var submitUrl = $('#delPostboxConfirmForm').attr('action');
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'delPostboxConfirmForm',
            success: function(data) {
                
                if (data.status) {
                    $.displayInfor(data.message);
                } else {
                    $.displayError(data.message);
                }
                $('#delPostboxConfirmWindow').dialog('close');
            }
        });
    	return false;
    });

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