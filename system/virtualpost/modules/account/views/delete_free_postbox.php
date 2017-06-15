<?php
    $submit_url = base_url().'account/set_delete_postbox';
?>
<form id="delPostboxConfirmForm" method="post" class="dialog-form" action="<?php echo $submit_url?>">
	<div class="ym-grid" style="margin-left: 10px; margin-top: 20px;">
		<div class="ym-gl input-cols">
			<div class="ym-gl register_label">
				<div style="color: red">
				    <div>If you choose this option, your postbox will be deleted right now.</div>
				    <div>- All current activities from this postbox will be cancelled </div>
				    <div>- All items in this postbox will be permanently destroyed and the digital files deleted</div>
				    <div><strong>This cannot be undone!</strong></div>
				</div>
			</div>
		</div>
		<div class="ym-clearfix"></div>
		<br />
		<?php if($primary_location == '1'):?>
		<div class="ym-gl input-cols"><label>Please select a new primary location: </label> 
		       <?php echo my_form_dropdown(array(
                                     "data" => $location_list,
                                     "value_key" => 'location_available_id',
                                     "label_key" => 'location_name',
                                     "value" => '',
                                     "name" => 'new_primary_location',
                                     "id"    => 'new_primary_location',
                                     "clazz" => 'input-width',
                                     "style" => 'width: 150px',
                                     "has_empty" => true
                                 ));?>
		</div>
		<?php endif;?>
	</div>
	
	<div class="ym-grid" style="margin-left: 10px; margin-top: 30px;">
			<div class="ym-gl register_label" style="width: 250px; float: left;">
				<button id="deleteMainPostboxAccount" style="margin-top: 20px; width: 250px; background: #F27724; border: 1px solid #F27724;color: #FFFFFF;box-shadow:none">Delete postbox now</button>
			</div>
			<div class="ym-gl register_label" style="width: 180px; float: left; margin-left: 20px;">
				<button id="backDeleteMainPostboxAccount" style="margin-top: 20px; width: 180px; background: rgb(114, 179,71); border: 1px solid rgb(114, 179,71);color: #FFFFFF;box-shadow:none">Cancel</button>
			</div>
			<div class="ym-clearfix"></div>
			<input type="hidden" name="postbox_id" value="<?php echo $postbox_id?>" />
		</div>
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
                    	document.location = '<?php echo base_url()?>account';
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