<?php
if ($action_type == 'add') {
    $submit_url = base_url() . 'settings/locations/add';
} else {
    $submit_url = base_url() . 'settings/locations/edit';
}
$location_admin_page = 1;
?>
<?php include 'system/virtualpost/modules/settings/views/locations/form_partial.php';?>

<script src="<?php echo APContext::getAssetPath(); ?>system/virtualpost/modules/settings/js/LocationForm.js"></script>
<script>
jQuery(document).ready(function() {
    //$("#addEditLocationForm input[type='checkbox']").checkbox();
    $('#pricing_template_id > option').prop('selected', true);
    if($("#shared_office_space_flag").is(':checked')){
        //$(".invisible").show();
    }
    else {
       // $(".invisible").hide();
    }

    $("#shared_office_space_flag").click(function(){
        if($("#shared_office_space_flag").is(':checked')){
            $("#shared_office_space_flag").val(1);
            //$(".invisible").fadeIn(2000);
        }
        else {
            $("#shared_office_space_flag").val(0);
            //$(".invisible").fadeOut(500);
        }
    });  

    $("#office_space_active_flag").click(function(){
        if($("#office_space_active_flag").is(':checked')){
            $("#office_space_active_flag").val(1);
            //$(".invisible").fadeIn(2000);
        }
        else {
            $("#office_space_active_flag").val(0);
            //$(".invisible").fadeOut(500);
        }
    });  

    var baseUrl = '<?php echo base_url(); ?>';
    var location_id = $("#h_location_id").val();
    LocationForm.init(baseUrl, location_id);
    LocationForm.loadStandardShippingService();
});
</script>