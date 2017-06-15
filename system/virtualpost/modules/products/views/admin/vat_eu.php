<div class="header">
    <h2 style="font-size: 20px; margin-bottom: 10px"><?php admin_language_e('product_view_admin_vateu_ProductsServicesVATEU'); ?></h2>
</div>

<div class="button_container" style="width:400px">
    <form id="vatSettingForm" method="post" action="<?php echo base_url()?>admin/products/save_vat_eu">
        <div class="input-form">
            <table  class="settings" style="width: 70%">
            <thead>
                <tr>
                    <th style="width:50px"><?php admin_language_e('product_view_admin_vateu_EUCountry'); ?></th>
                    <th style="width:30px"><?php admin_language_e('product_view_admin_vateu_Rate'); ?></th>
                </tr>
            </thead>
                <?php foreach($countries as $country): ?>
                <tr>
                    <td>
                        <input type="text" class="input-txt-none readonly" readonly="readonly" style="width:95%" value="<?php echo $country->country_name?>" />
                        <input type="hidden" name="country_id-<?php echo $country->country_id?>" value="<?php echo $country->country_id?>" />
                    </td>
                    <td><input type="text" class="input-txt-none" style="width:95%" name="rate-<?php echo $country->country_id?>" value="<?php echo $country->rate?>" /></td>
                </tr>
                <?php endforeach;?>
                <tr>
                    <td colspan="4"><button id="submitButton" class="ui-button"><?php admin_language_e('product_view_admin_vateu_SaveBtn'); ?></button></td>
                </tr>
            </table>
        </div>
    </form>
</div>



<script type="text/javascript">
$(document).ready( function() {
    $("#submitButton").button().click(function() {
		$('#vatSettingForm').submit();
        return false;
    });
});
</script>
