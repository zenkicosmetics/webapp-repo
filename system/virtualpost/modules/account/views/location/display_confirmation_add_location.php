<?php
$submit_url = base_url() . 'account/location/display_confirmation_add_location';
?>
<table>
    <tr>
        <td colspan="2" style="text-align: center;">
            Please confirm the order for your new location in <?php echo $end_contract_date;?> <br/>
            Price: <?php echo APUtils::number_format($price); ?> EUR / month <br/>
            Contract term: yearly with automatic renewal <br/>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <input type="checkbox" id="display_confirmation_add_location_terms" />I hereby confirm the Terms & Conditions
        </td>
    </tr>
</table>
<script type="text/javascript">
$(document).ready(function(){
    
});
</script>