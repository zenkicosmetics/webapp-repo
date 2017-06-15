<?php
$submit_url = base_url () . 'partner/admin/edit_marketing';
?>
<form id="addEditWidgetForm" method="post" action="<?php echo $submit_url?>" autocomplete="on">
	<table>
		<tr>
			<td>
				<table>
					<tr>
						<th style="width: 160px">Partner name <span class="required">*</span></th>
						<td><input type="text" id="partner_name" name="partner_name" value="<?php echo $partner->partner_name?>"
							class="input-txt readonly" readonly="readonly" maxlength="50" /></td>
					</tr>
					<tr>
						<th>Partner code <span class="required">*</span></th>
						<td><input type="text" id="partner_code" name="partner_code" value="<?php echo $partner->partner_code?>"
							class="input-txt readonly" readonly="readonly" maxlength="50" /></td>
					</tr>
					<tr>
						<th>Width</th>
						<td><input type="text" id="width" name="width" value="<?php echo $partner->width ? $partner->width : '420'; ?>"
							class="input-txt " style="width: 90%" maxlength="50" /> (px)</td>
					</tr>
					<tr>
						<th>Height</th>
						<td><input type="text" id="height" name="height" value="<?php echo $partner->height? $partner->height : '300';?>"
							class="input-txt " style="width: 90%"  maxlength="50" /> (px)</td>
					</tr>
					<tr>
						<th>Title</th>
						<td><input type="text" id="title" name="title" value="<?php echo $partner->title?>"
							class="input-txt " maxlength="2150" /></td>
					</tr>
					<tr>
						<th>Session Catch</th>
						<td><textarea rows="" cols="" style="width: 80%" readonly="readonly"  id="session_catch" name="session_catch"
							 class="input-txt readonly"><?php echo htmlentities($partner->session_catch)?></textarea> <span
							style="display: inline-block;">
								<button id="generateSessionButton" type="button" style="margin-left: 5px;">Generate widget</button></td>
					</tr>
					<tr>
						<th>Registration widget</th>
						<td><textarea rows="" cols="" style="width: 80%" readonly="readonly"  id="script_widget" name="script_widget"
							 class="input-txt readonly"><?php echo htmlentities($partner->script_widget)?></textarea> <span
							style="display: inline-block;">
								<button id="generateWidgetButton" type="button" style="margin-left: 5px;">Generate widget</button></td>
					</tr>
					<tr>
						<th>Clevvermail landingpage</th>
						<td><textarea rows="" cols="" style="width: 80%" readonly="readonly"  id="script_landing_page" name="script_landing_page"
						      class="input-txt readonly"><?php echo htmlentities($partner->script_landing_page)?></textarea>  <span
							style="display: inline-block;">
								<button id="generateLandingPageButton"  type="button" style="margin-left: 5px;">Generate landing page</button></td>
					</tr>
				</table>
			</td>

		</tr>
	</table>
	<input type="hidden" id="h_type" name="type" value="" /> 
	<input type="hidden" id="h_token" name="token" value="" /> 
	<input type="hidden" id="h_partner_id" name="id" value="<?php echo $partner->partner_id?>" /> <input type="hidden"
		id="h_partner_code" name="partner_code" value="<?php echo $partner->partner_code?>" />
</form>
<script type="text/javascript">

$(document).ready( function() {
	$('#generateWidgetButton, #generateLandingPageButton, #generateSessionButton').button({
      icons: {
          primary: "ui-icon-gear"
        },
        text: false
	});
    
    

    $('#generateLandingPageButton').click(function(){
    	$('#h_type').val("1");
    	var submitUrl = '<?php echo base_url()?>partner/admin/generate_widget';
        $.ajaxSubmit({
             url: submitUrl,
             formId: "addEditWidgetForm",
             success: function(data) {
                 if (data.status) {
                     // Reload data grid
                	 $("#script_landing_page").val(data.data.html);
                 } else {
                 	$.displayError(data.message);
                 }
             }
         });
    });

    $('#generateWidgetButton').click(function(){
        $('#h_type').val("2");
        
    	var submitUrl = '<?php echo base_url()?>partner/admin/generate_widget';
        $.ajaxSubmit({
             url: submitUrl,
             formId: "addEditWidgetForm",
             success: function(data) {
                 if (data.status) {
                     // Reload data grid
                	 $("#script_widget").val(data.data.html);
                	 $("#h_token").val(data.data.code);
                 } else {
                 	$.displayError(data.message);
                 }
             }
         });
    });

    $("#generateSessionButton").click(function(){
    	$('#h_type').val("3");
    	var submitUrl = '<?php echo base_url()?>partner/admin/generate_widget';
    	$.ajaxSubmit({
			url: submitUrl,
			formId: "addEditWidgetForm",
			success: function(data) {
				if (data.status) {
                    // Reload data grid
					$("#session_catch").val(data.data.html);
                } else {
                	$.displayError(data.message);
                }
			}
		});
    });
});
</script>