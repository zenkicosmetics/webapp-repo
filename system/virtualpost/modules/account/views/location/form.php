<?php
if ($action_type == 'add') {
    $submit_url = base_url() . 'account/location/add';
} else {
    $submit_url = base_url() . 'account/location/edit';
}
$location_admin_page = false;
?>

<?php include 'system/virtualpost/modules/settings/views/locations/form_partial.php';?>

<script>
jQuery(document).ready(function() {
    $('.tipsy_tooltip').tipsy({gravity: 'sw'});
    
    LocationCustomer.loadStandardShippingService();
});
</script>