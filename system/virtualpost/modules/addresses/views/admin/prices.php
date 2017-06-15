<div class="header">
    <h2 style="font-size: 20px; margin-bottom: 10px"><?php admin_language_e('address_view_admin_prices_LocationPricesModel'); ?></h2>
</div>
<?php
// Check authenticate.
$readonly = 'readonly="readonly"';
$readonlyClass = "readonly";
$is_location_admin_page = 'location_pricing';
?>
<div class="ym-grid mailbox">
    <div class="ym-g70 ym-gl">
        <table>
            <?php if (!empty($is_enterprise_location_open) && $is_enterprise_location_open) {?>
            <tr>
                <th>Location Name</th>
                <th>Pricing Template (Internal)</th>
                <th>Pricing Template (External)</th>
            </tr>
            <?php } else {?>
            <tr>
                <th>Location Name</th>
                <th>Pricing Template</th>
            </tr>
            <?php } ?>
            <tr>
                <td style="width:10px;">
                <form id="locationForm" action="<?php echo base_url()?>addresses/admin/location_pricing" method="post">
                 <?php 
                 // check access for instance owner.
                 //if(APContext::isAdminUser()){
                     echo my_form_dropdown(array(
                             "data" => $list_access_location,
                             "value_key" => 'id',
                             "label_key" => 'location_name',
                             "value" => $location_id,
                             "name" => 'location_id',
                             "id"	=> 'location_id',
                             "clazz" => 'input-width',
                             "style" => '',
                             "has_empty" => (APContext::isAdminLocation() || APContext::isWorkerAdmin()) ? false : true
                     ));
                 //}
                 ?>
                </form>
                </td>
                
                <form id="pricingTemplateForm" action="<?php echo base_url()?>addresses/admin/location_pricing" method="post">
                <td style="width:10px;">
                    <?php 
                    if (APContext::isAdminLocation() || APContext::isWorkerAdmin()) { 
                        echo my_form_dropdown(array(
                             "data" => $pricing_templates,
                             "value_key" => 'id',
                             "label_key" => 'name',
                             "value" => $pricing_template_id,
                             "name" => 'pricing_template_id',
                             "id"   => 'pricing_template_id_tmp',
                             "clazz" => 'input-width readonly',
                             "style" => '',
                             "has_empty" => false,
                             "html_option" => 'disabled="disabled"',
                     ));
                    }
                    else { 
                        echo my_form_dropdown(array(
                             "data" => $pricing_templates,
                             "value_key" => 'id',
                             "label_key" => 'name',
                             "value" => $pricing_template_id,
                             "name" => 'pricing_template_id',
                             "id"   => 'pricing_template_id',
                             "clazz" => 'input-width',
                             "style" => '',
                             "has_empty" => false
                        ));
                    }

                 ?>
                <input type="hidden" name="status" value="active">
                <input type="hidden" name="location_id" value="<?php echo $location_id?>">
                </td>
                <?php if (!empty($is_enterprise_location_open) && $is_enterprise_location_open) {?>
                <td style="width:10px;">
                <?php 
                    if (APContext::isAdminLocation() || APContext::isWorkerAdmin()) { 
                        echo my_form_dropdown(array(
                             "data" => $enterprise_pricing_templates,
                             "value_key" => 'id',
                             "label_key" => 'name',
                             "value" => $pricing_template_id,
                             "name" => 'enterprise_pricing_template_id',
                             "id"   => 'enterprise_pricing_template_id_tmp',
                             "clazz" => 'input-width readonly',
                             "style" => '',
                             "has_empty" => false,
                             "html_option" => 'disabled="disabled"',
                     ));
                    }
                    else { 
                        echo my_form_dropdown(array(
                             "data" => $enterprise_pricing_templates,
                             "value_key" => 'id',
                             "label_key" => 'name',
                             "value" => $pricing_template_id,
                             "name" => 'enterprise_pricing_template_id',
                             "id"   => 'enterprise_pricing_template_id',
                             "clazz" => 'input-width',
                             "style" => '',
                             "has_empty" => false
                        ));
                    }
                 ?>
                <?php } ?>
                </td>
                </form>
                <td align="center">
                <?php if($name_pricing_template && $name_pricing_template != NULL && $status == "active"){ ?>
                <span style="color:red;font-size:15px;"><?php admin_language_e('address_view_admin_prices_TemplateAppliedNextMonth'); ?></span>
                <?php foreach (@$name_pricing_template as $name){?>
                        <span style="color:red; font-weight: bold; font-size:15px;"><?php echo $name->name?> </span>
                <?php }?>
                <?php }elseif ($name_pricing_template && $name_pricing_template != NULL){ ?>
                        <span style="color:red;font-size:15px;"><?php admin_language_e('address_view_admin_prices_TemplateAppliedNextMonth'); ?></span>
                <?php foreach (@$name_pricing_template as $name){?>
                        <span style="color:red; font-weight: bold; font-size:15px;"><?php echo $name->name?> </span>
                <?php }?>
                <?php }?>
                </td>
            </tr>
        </table>
    </div>
</div>
<div class="hide">
	<div id="confirmPricingTemplate" title="<?php admin_language_e('address_view_admin_prices_ConfirmPricingTemplate'); ?>" class="input-form dialog-form"></div>
	<div id="successPricingTemplate" title="<?php admin_language_e('address_view_admin_prices_SuccessPricingTemplate'); ?>" class="input-form dialog-form"></div>
</div>

<?php include 'system/virtualpost/modules/addresses/views/admin/price_partial.php';?>

<div class="clear-height"></div>
<script type="text/javascript">
var change_pricing_template_confirm_1 = '<?php admin_language_e('address_view_admin_prices_ChangePricingTemplateConfirm1'); ?>';
var change_pricing_template_confirm_2 = '<?php admin_language_e('address_view_admin_prices_ChangePricingTemplateConfirm2'); ?>';
var change_pricing_template_success = '<?php admin_language_e('address_view_admin_prices_ChangePricingTemplateSuccess'); ?>';
</script>
<script src="<?php echo $this->config->item('asset_url'); ?>system/virtualpost/modules/addresses/js/LocationPricing.js"></script>
<script type="text/javascript">
    $(document).ready( function() {
        var baseUrl = '<?php echo base_url(); ?>';
        LocationPricing.init(baseUrl);
    });
</script>