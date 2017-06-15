<?php
if ($action_type == 'edit') {
    $submit_url = base_url() . 'settings/customs/edit';
}
?>

<form id="addEditCustomForm" method="post" action="<?php echo $submit_url ?>" enctype="multipart/form-data" autocomplete="on">
    <table cellpadding="0">
      <tr>
      	<th width="35%"> Check custom declare </th>
         	<td> 
         		<input type="checkbox" id="custom_flag" name="custom_flag" value="1" <?php if ($custom_flag == '1') { ?>checked="checked" <?php } ?>   /> 
            </td>
      </tr>
   </table>
    	<input type= "hidden" name="from_country" value="<?php echo $from_country?>" />
     	<input type= "hidden" name="to_country" value="<?php echo $to_country?>" />
     	<input type="hidden" id="h_action_type" name="h_action_type" value="<?php echo $action_type; ?>"/>

</form>

