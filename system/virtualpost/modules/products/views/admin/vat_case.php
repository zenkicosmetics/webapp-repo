<div class="header">
	<h2 style="font-size: 20px; margin-bottom: 10px"><?php admin_language_e('product_view_admin_vatcase_ProductsServicesVATCase'); ?></h2>
</div>
<form id="vatSettingForm" method="post" action="<?php echo base_url()?>admin/products/save_vat_case">
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"><?php admin_language_e('product_view_admin_vatcase_TabLocalService'); ?></a></li>
        <li><a href="#tabs-2"><?php admin_language_e('product_view_admin_vatcase_TabShipping'); ?></a></li>
        <li><a href="#tabs-3"><?php admin_language_e('product_view_admin_vatcase_TabDigitalGoods'); ?></a></li>
    </ul>
    <div id="tabs-1" class="button_container">
    	<table style="width: 1050px">
    		<tr>
    		    <th colspan="5" style="text-align: center;"><?php admin_language_e('product_view_admin_vatcase_PrivateCustomer'); ?></th>
    		    <th colspan="5" style="text-align: center;"><?php admin_language_e('product_view_admin_vatcase_EnterpriseCustomer'); ?></th>
    		</tr>
    		<tr>
    		    <th style="width: 40px;"><?php admin_language_e('product_view_admin_vatcase_VatCase'); ?></th>
    		    <th ><?php admin_language_e('product_view_admin_vatcase_CustomerInvoiceLocation'); ?></th>
    		    <th style="width: 120px;"><?php admin_language_e('product_view_admin_vatcase_VatRate'); ?></th>
    		    <th style="width: 150px;"><?php admin_language_e('product_view_admin_vatcase_NoteInvoice'); ?></th>
    		    <th style="width: 150px;"><?php admin_language_e('product_view_admin_vatcase_ReverseCharge'); ?></th>
    		    <th style="width: 150px;"><?php admin_language_e('product_view_admin_vatcase_Description'); ?></th>
    		    <th style="width: 40px;"><?php admin_language_e('product_view_admin_vatcase_VatCase'); ?></th>
    		    <th><?php admin_language_e('product_view_admin_vatcase_CustomerInvoiceLocation'); ?></th>
    		    <th style="width: 120px;"><?php admin_language_e('product_view_admin_vatcase_VatRate'); ?></th>
    		    <th style="width: 150px;"><?php admin_language_e('product_view_admin_vatcase_NoteInvoice'); ?></th>
    		    <th style="width: 150px;"><?php admin_language_e('product_view_admin_vatcase_ReverseCharge'); ?></th>
    		    <th style="width: 150px;"><?php admin_language_e('product_view_admin_vatcase_Description'); ?></th>
    		</tr>
    		<?php foreach ($list_local_service_vat as $vat) {?>
    		<tr>
    		    <td><?php echo $vat->private_vat_case_id?></td>
    		    <td><?php echo $vat->country_name?></td>
    		    <td>
    		        <?php echo my_form_dropdown(array(
                         "data" => $list_standard_vat,
                         "value_key" => 'id',
                         "label_key" => 'name',
                         "value" => $vat->private_type,
                         "name" => 'type-'.$vat->private_vat_id."-private-local-".$vat->baseon_country_id,
                         "id"    => 'type-'.$vat->private_vat_id."-private-local-".$vat->baseon_country_id,
                         "clazz" => 'input-txt-none',
                         "style" => 'width: 95%;',
                         "has_empty" => false
                     ));?>
                     <input type="hidden" name="baseon_country_id-<?php echo $vat->baseon_country_id ?>-private-local" value="<?php echo $vat->baseon_country_id ?>" />
    		    </td>
    		    <td><input type="text" class="input-txt-none" style="width:95%" name="notes-<?php echo $vat->private_vat_id?>-private-local" value="<?php echo $vat->private_notes?>" /></td>
    		    <td>
    		      <?php  echo my_form_dropdown(array(
                         "data" => $reverse_charge_list,
                         "value_key" => 'id',
                         "label_key" => 'name',
                         "value" => $vat->private_reverse_charge,
                         "name" => 'reverse_charge-'.$vat->private_vat_id."-private-local",
                         "id"    => 'reverse_charge-'.$vat->private_vat_id."-private-local",
                         "clazz" => 'input-txt-none',
                         "style" => 'width: 95%;',
                         "has_empty" => false
                     ));?>
    		    </td>
    		    <td><input type="text" class="input-txt-none" style="width:95%" name="text-<?php echo $vat->private_vat_id?>-private-local" value="<?php echo $vat->private_text?>" /></td>
    		    <td><?php echo $vat->enterprise_vat_case_id?></td>
    		    <td><?php echo $vat->country_name?></td>
    		    <td>
    		        <?php echo my_form_dropdown(array(
                         "data" => $list_standard_vat,
                         "value_key" => 'id',
                         "label_key" => 'name',
                         "value" => $vat->enterprise_type,
                         "name" => 'type-'.$vat->enterprise_vat_id."-enterprise-local-".$vat->baseon_country_id,
                         "id"    => 'type-'.$vat->enterprise_vat_id."-enterprise-local-".$vat->baseon_country_id,
                         "clazz" => 'input-txt-none',
                         "style" => 'width: 95%;',
                         "has_empty" => false
                     ));?>
    		    </td>
    		    <td><input type="text" class="input-txt-none" style="width:95%" name="notes-<?php echo $vat->enterprise_vat_id?>-enterprise-local" value="<?php echo $vat->enterprise_notes?>" /></td>
    		    <td>
    		      <?php  echo my_form_dropdown(array(
                         "data" => $reverse_charge_list,
                         "value_key" => 'id',
                         "label_key" => 'name',
                         "value" => $vat->enterprise_reverse_charge,
                         "name" => 'reverse_charge-'.$vat->enterprise_vat_id."-enterprise-local",
                         "id"    => 'reverse_charge-'.$vat->enterprise_vat_id."-enterprise-local",
                         "clazz" => 'input-txt-none',
                         "style" => 'width: 95%;',
                         "has_empty" => false
                     ));?>
    		    </td>
    		    <td><input type="text" class="input-txt-none" style="width:95%" name="text-<?php echo $vat->enterprise_vat_id?>-enterprise-local" value="<?php echo $vat->enterprise_text?>" /></td>
    		</tr>
    		<?php }?>
    	</table>
	</div>
	<div id="tabs-2" class="button_container">
	    <table style="width: 1050px">
    		<tr>
    		    <th style="width: 150px;">&nbsp;</th>
    		    <th colspan="5" style="text-align: center;"><?php admin_language_e('product_view_admin_vatcase_PrivateCustomer'); ?></th>
    		    <th colspan="5" style="text-align: center;"><?php admin_language_e('product_view_admin_vatcase_EnterpriseCustomer'); ?></th>
    		</tr>
    		<tr>
    		    <th style="width: 40px;"><?php admin_language_e('product_view_admin_vatcase_VatCase'); ?></th>
    		    <th style="width: 120px;"><?php admin_language_e('product_view_admin_vatcase_VatRate'); ?></th>
    		    <th style="width: 150px;"><?php admin_language_e('product_view_admin_vatcase_NoteInvoice'); ?></th>
    		    <th style="width: 150px;"><?php admin_language_e('product_view_admin_vatcase_ReverseCharge'); ?></th>
    		    <th style="width: 150px;"><?php admin_language_e('product_view_admin_vatcase_Description'); ?></th>
    		    <th style="width: 40px;"><?php admin_language_e('product_view_admin_vatcase_VatCase'); ?></th>
    		    <th style="width: 120px;"><?php admin_language_e('product_view_admin_vatcase_VatRate'); ?></th>
    		    <th style="width: 150px;"><?php admin_language_e('product_view_admin_vatcase_NoteInvoice'); ?></th>
    		    <th style="width: 150px;"><?php admin_language_e('product_view_admin_vatcase_ReverseCharge'); ?></th>
    		    <th style="width: 150px;"><?php admin_language_e('product_view_admin_vatcase_Description'); ?></th>
    		</tr>
    		<?php $index = 0;?>
    		<?php foreach ($list_shipping_vat as $vat) {?>
    		<?php $index ++;?>
    		<tr>
    		    <?php if($index == 1):?>
    		    <td><?php echo $vat->private_vat_case_id?></td>
    		    <td>
    		        <?php echo my_form_dropdown(array(
                         "data" => $list_standard_vat,
                         "value_key" => 'id',
                         "label_key" => 'name',
                         "value" => $vat->private_type,
                         "name" => 'type-'.$vat->private_vat_id."-private-shipping-".$vat->baseon_country_id,
                         "id"    => 'type-'.$vat->private_vat_id."-private-shipping-".$vat->baseon_country_id,
                         "clazz" => 'input-txt-none',
                         "style" => 'width: 95%;',
                         "has_empty" => false
                     ));?>
                     <input type="hidden" name="baseon_country_id-<?php echo $vat->baseon_country_id ?>-private-shipping" value="<?php echo $vat->baseon_country_id ?>" />
    		    </td>
    		    <td><input type="text" class="input-txt-none" style="width:95%" name="notes-<?php echo $vat->private_vat_id?>-private-shipping" value="<?php echo $vat->private_notes?>" /></td>
    		    <td>
    		      <?php  echo my_form_dropdown(array(
                         "data" => $reverse_charge_list,
                         "value_key" => 'id',
                         "label_key" => 'name',
                         "value" => $vat->private_reverse_charge,
                         "name" => 'reverse_charge-'.$vat->private_vat_id."-private-shipping",
                         "id"    => 'reverse_charge-'.$vat->private_vat_id."-private-shipping",
                         "clazz" => 'input-txt-none',
                         "style" => 'width: 95%;',
                         "has_empty" => false
                     ));?>
    		    </td>
    		    <td><input type="text" class="input-txt-none" style="width:95%" name="text-<?php echo $vat->private_vat_id?>-private-shipping" value="<?php echo $vat->private_text?>" /></td>
    		    <?php else:?>
    		    <td>&nbsp;</td>
    		    <td>&nbsp;</td>
    		    <td>&nbsp;</td>
    		    <td>&nbsp;</td>
    		    <td>&nbsp;</td>
    		    <?php endif;?>
    		    <td><?php echo $vat->enterprise_vat_case_id?></td>
    		    <td>
    		        <?php echo my_form_dropdown(array(
                         "data" => $list_standard_vat,
                         "value_key" => 'id',
                         "label_key" => 'name',
                         "value" => $vat->enterprise_type,
                         "name" => 'type-'.$vat->enterprise_vat_id."-enterprise-shipping-".$vat->baseon_country_id,
                         "id"    => 'type-'.$vat->enterprise_vat_id."-enterprise-shipping-".$vat->baseon_country_id,
                         "clazz" => 'input-txt-none',
                         "style" => 'width: 95%;',
                         "has_empty" => false
                     ));?>
    		    </td>
    		    <td><input type="text" class="input-txt-none" style="width:95%" name="notes-<?php echo $vat->enterprise_vat_id?>-enterprise-shipping" value="<?php echo $vat->enterprise_notes?>" /></td>
    		    <td>
    		      <?php  echo my_form_dropdown(array(
                         "data" => $reverse_charge_list,
                         "value_key" => 'id',
                         "label_key" => 'name',
                         "value" => $vat->enterprise_reverse_charge,
                         "name" => 'reverse_charge-'.$vat->enterprise_vat_id."-enterprise-shipping",
                         "id"    => 'reverse_charge-'.$vat->enterprise_vat_id."-enterprise-shipping",
                         "clazz" => 'input-txt-none',
                         "style" => 'width: 95%;',
                         "has_empty" => false
                     ));?>
    		    </td>
    		    <td><input type="text" class="input-txt-none" style="width:95%" name="text-<?php echo $vat->enterprise_vat_id?>-enterprise-shipping" value="<?php echo $vat->enterprise_text?>" /></td>
    		</tr>
    		<?php }?>
    	</table>
	</div>
	
	<div id="tabs-3" class="button_container">
	   <table style="width: 1050px">
    		<tr>
    		    <th style="width: 150px;">&nbsp;</th>
    		    <th colspan="5" style="text-align: center;"><?php admin_language_e('product_view_admin_vatcase_PrivateCustomer'); ?></th>
    		    <th colspan="5" style="text-align: center;"><?php admin_language_e('product_view_admin_vatcase_EnterpriseCustomer'); ?></th>
    		</tr>
    		<tr>
    		    <th style="width: 40px;"><?php admin_language_e('product_view_admin_vatcase_VatCase'); ?></th>
    		    <th ><?php admin_language_e('product_view_admin_vatcase_CustomerInvoiceLocation'); ?></th>
    		    <th style="width: 120px;"><?php admin_language_e('product_view_admin_vatcase_VatRate'); ?></th>
    		    <th style="width: 150px;"><?php admin_language_e('product_view_admin_vatcase_NoteInvoice'); ?></th>
    		    <th style="width: 150px;"><?php admin_language_e('product_view_admin_vatcase_ReverseCharge'); ?></th>
    		    <th style="width: 150px;"><?php admin_language_e('product_view_admin_vatcase_Description'); ?></th>
    		    <th style="width: 40px;"><?php admin_language_e('product_view_admin_vatcase_VatCase'); ?></th>
    		    <th><?php admin_language_e('product_view_admin_vatcase_CustomerInvoiceLocation'); ?></th>
    		    <th style="width: 120px;"><?php admin_language_e('product_view_admin_vatcase_VatRate'); ?></th>
    		    <th style="width: 150px;"><?php admin_language_e('product_view_admin_vatcase_NoteInvoice'); ?></th>
    		    <th style="width: 150px;"><?php admin_language_e('product_view_admin_vatcase_ReverseCharge'); ?></th>
    		    <th style="width: 150px;"><?php admin_language_e('product_view_admin_vatcase_Description'); ?></th>
    		</tr>
    		<?php foreach ($list_digital_good_vat as $vat) {?>
    		<tr>
    		    <td><?php echo $vat->private_vat_case_id?></td>
    		    <td><?php echo $vat->country_name?></td>
    		    <td>
    		        <?php echo my_form_dropdown(array(
                         "data" => $list_standard_vat,
                         "value_key" => 'id',
                         "label_key" => 'name',
                         "value" => $vat->private_type,
                         "name" => 'type-'.$vat->private_vat_id."-private-dg-".$vat->baseon_country_id,
                         "id"    => 'type-'.$vat->private_vat_id."-private-dg-".$vat->baseon_country_id,
                         "clazz" => 'input-txt-none',
                         "style" => 'width: 95%;',
                         "has_empty" => false
                     ));?>
                     <input type="hidden" name="baseon_country_id-<?php echo $vat->baseon_country_id ?>-private-dg" value="<?php echo $vat->baseon_country_id ?>" />
    		    </td>
    		    <td><input type="text" class="input-txt-none" style="width:95%" name="notes-<?php echo $vat->private_vat_id?>-private-dg" value="<?php echo $vat->private_notes?>" /></td>
    		    <td>
    		      <?php  echo my_form_dropdown(array(
                         "data" => $reverse_charge_list,
                         "value_key" => 'id',
                         "label_key" => 'name',
                         "value" => $vat->private_reverse_charge,
                         "name" => 'reverse_charge-'.$vat->private_vat_id."-private-dg",
                         "id"    => 'reverse_charge-'.$vat->private_vat_id."-private-dg",
                         "clazz" => 'input-txt-none',
                         "style" => 'width: 95%;',
                         "has_empty" => false
                     ));?>
    		    </td>
    		    <td><input type="text" class="input-txt-none" style="width:95%" name="text-<?php echo $vat->private_vat_id?>-private-dg" value="<?php echo $vat->private_text?>" /></td>
    		    <td><?php echo $vat->enterprise_vat_case_id?></td>
    		    <td><?php echo $vat->country_name?></td>
    		    <td>
    		        <?php echo my_form_dropdown(array(
                         "data" => $list_standard_vat,
                         "value_key" => 'id',
                         "label_key" => 'name',
                         "value" => $vat->enterprise_type,
                         "name" => 'type-'.$vat->enterprise_vat_id."-enterprise-dg-".$vat->baseon_country_id,
                         "id"    => 'type-'.$vat->enterprise_vat_id."-enterprise-dg-".$vat->baseon_country_id,
                         "clazz" => 'input-txt-none',
                         "style" => 'width: 95%;',
                         "has_empty" => false
                     ));?>
    		    </td>
    		    <td><input type="text" class="input-txt-none" style="width:95%" name="notes-<?php echo $vat->enterprise_vat_id?>-enterprise-dg" value="<?php echo $vat->enterprise_notes?>" /></td>
    		    <td>
    		      <?php  echo my_form_dropdown(array(
                         "data" => $reverse_charge_list,
                         "value_key" => 'id',
                         "label_key" => 'name',
                         "value" => $vat->enterprise_reverse_charge,
                         "name" => 'reverse_charge-'.$vat->enterprise_vat_id."-enterprise-dg",
                         "id"    => 'reverse_charge-'.$vat->enterprise_vat_id."-enterprise-dg",
                         "clazz" => 'input-txt-none',
                         "style" => 'width: 95%;',
                         "has_empty" => false
                     ));?>
    		    </td>
    		    <td><input type="text" class="input-txt-none" style="width:95%" name="text-<?php echo $vat->enterprise_vat_id?>-enterprise-dg" value="<?php echo $vat->enterprise_text?>" /></td>
    		</tr>
    		<?php }?>
    	</table>
	</div>
</div>
<br />
<div class="button_container">
        <button id="submitButton" class="ui-button">Save</button>
</div>
<p>&nbsp;</p>
</form>
<script type="text/javascript">
$(document).ready( function() {
	$( "#tabs" ).tabs();

	$("#submitButton").button().click(function() {
		$('#vatSettingForm').submit();
        return false;
    });
});
</script>