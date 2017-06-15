<?php
$submit_url = base_url () . 'account/set_delete_postbox';
?>
<form id="delPostboxConfirmForm" method="post" class="dialog-form" action="<?php echo $submit_url?>">
	<div class="ym-grid" style="margin-left: 20px; width: 400px">
		<div style="margin: 10px 0 0 0px; width: 450px">
			<h2 style="font-size: 18px;">Do you really want to delete your postbox?</h2>
		</div>
	</div>

	<div class="ym-clearfix"></div>

	<div class="ym-grid" style="margin-left: 10px; margin-top: 30px;">
		<div class="ym-gl input-cols" style="">
			<div class="ym-gl register_label" style="width: 100%; float: left;">
				<div style="color: red">
					<div>If you choose "Delete All Items", All items will be deleted right now.</div>
					<div>- All current activities from this postbox will be cancelled</div>
					<div>- All items in this postbox will be permanently destroyed and the digital files deleted</div>
                                        <!--<div>- your postbox will be automatically deleted at the end of your current invoicing period (end of month).</div>-->
					<div>
						<strong>This cannot be undone!</strong>
					</div>
				</div>
			</div>
			<!--<div class="ym-gl register_label" style="width: 220px; float: left; margin-left: 10px;">
				<div style="height: 140px; color: red">
                    All items will be deleted right now.<br/>
					If you choose this option, your postbox will be automatically deleted at the end of your current invoicing period
					(end of month). You can receive your account at any time until it is deleted. <br /> The account will continue
					working until end of month
				</div>
			</div>
			<div class="ym-gl register_label" style="width: 220px; float: left; margin-left: 10px;">
				<div style="height: 140px; visibility: hidden;">
					If you choose this option, your account will be deleted right now.<br /> - Your login will not be working <br /> -
					Your settings and all files will be deleted <br /> This cannot be undone!
				</div>
			</div>-->
			<div class="ym-clearfix"></div>
    		<?php if($primary_location == '1'):?>
    		<div class="ym-gl input-cols" style="margin-top: 6px;">
				<label>Please select a new primary location: </label> 
    		       <?php
                    echo my_form_dropdown ( array (
                            "data" => $location_list,
                            "value_key" => 'location_available_id',
                            "label_key" => 'location_name',
                            "value" => '',
                            "name" => 'new_primary_location',
                            "id" => 'new_primary_location',
                            "clazz" => 'input-width',
                            "style" => 'width: 150px',
                            "has_empty" => true 
                    ) );
                    ?>
    		</div>
    		<?php endif;?>
		</div>
	</div>

	<div class="ym-grid" style="margin-left: 10px; margin-top: 10px;">
		<div class="ym-gl register_label" style="width: 220px; float: left;">
			<button id="deleteMainPostboxAccount"
				style="margin-top: 10px; width: 180px; background: #F27724; border: 1px solid #F27724; color: #FFFFFF; box-shadow: none">Delete All Items</button>
		</div>
		<!--<div class="ym-gl register_label" style="width: 220px; float: left; margin-left: 10px;">
			<button id="cancelDeleteMainPostboxAccount"
				style="margin-top: 10px; width: 180px; background: #F27724; border: 1px solid #F27724; color: #FFFFFF; box-shadow: none">Cancel
				at month-end</button>
		</div>-->
		<div class="ym-gl register_label" style="width: 220px; float: left; margin-left: 10px;">
			<button id="backDeleteMainPostboxAccount"
				style="margin-top: 10px; width: 180px; background: rgb(114, 179, 71); border: 1px solid rgb(114, 179, 71); color: #FFFFFF; box-shadow: none">Cancel</button>
		</div>
		<div class="ym-clearfix"></div>
		<div class="ym-clearfix"></div>
		<input type="hidden" name="postbox_id" value="<?php echo $postbox_id?>" />
	</div>
	<input type="hidden" name="delete_type" id="delPostboxConfirmForm_deleteType" value="">
	<input type="hidden" name="direct_delete" id="delPostboxConfirmForm_deleteDirect" value="">
</form>
<script type="text/javascript">
$(document).ready( function() {
    $('button').button();

    /**
     * Delete on the end of cycle.
     */
    /*
    $('#cancelDeleteMainPostboxAccount').click(function(){
    	$('#delPostboxConfirmForm_deleteType').val('1');
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
    */    
    /**
     * Direct delete postbox account
     */
    $('#deleteMainPostboxAccount').click(function(){
        var primary_location = "<?php echo $primary_location; ?>";
        if($("#new_primary_location").val() == "" && primary_location == '1'){
            $.displayInfor("Please select one new primary location before delete primary location");
            return false;
        }
    	$('#delPostboxConfirmForm_deleteDirect').val('1');
    	var submitUrl = $('#delPostboxConfirmForm').attr('action');
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'delPostboxConfirmForm',
            success: function(data) {
                if (data.status) {
                    $.displayInfor(data.message, null, function(){
                        location.reload();
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