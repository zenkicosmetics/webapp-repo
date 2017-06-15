<?php
   $submit_url = base_url () . 'report/admin/create_location_report';
?>
<style>
.input-error {
    border: 1px #800 solid !important;
    color: #800;
}
</style>
<form id="createReportForm" method="post" action="<?php echo $submit_url?>">
	<table>
        <!-- Cost of location advertising -->
        <tr>
            <td><?php admin_language_e('report_views_admin_form_TblHdNewCost'); ?></td>
            <td>
                <input type="text" style="width:200px" name="costOfLocationAdvertising" id="createReportForm_costOfLocationAdvertising" value="<?php echo $cost_of_location_advertising?>" class="input-width" >
            </td>
            <td><span id="errorMessage01" style="color:red; font-size: 11px"></span></td>
        </tr>
        
         <!-- Hardware amortization -->
        <tr>
            <td><?php admin_language_e('report_views_admin_form_TblHdNewHardware'); ?></td>
            <td>
              <input type="text"   style="width:200px" name="hardwareAmortization" id="createReportForm_hardwareAmortization" value="<?php echo $hardware_amortization?>" class="input-width" >
            </td>
             <td><span id="errorMessage02" style="color:red; font-size: 11px"></span></td>
        </tr>
        
         <!-- Location external receipts -->
        <tr>
            <td><?php admin_language_e('report_views_admin_form_TbtHdNewLocal'); ?></td>
            <td>
              <input type="text"  style="width:200px" name="locationExternalReceipts" id="createReportForm_locationExternalReceipts" value="<?php echo $location_external_receipts?>" class="input-width">
            </td>
             <td><span id="errorMessage03" style="color:red; font-size: 11px"></span></td>
        </tr>
        
        <!-- Current open balance -->
        <tr>
            <td><?php admin_language_e('report_views_admin_form_TbtHdOpenBal'); ?></td>
            <td>
              <input type="text"  style="width:200px" name="currentOpenBalance" id="createReportForm_currentOpenBalance" value="<?php echo $current_open_balance?>" class="input-width">
            </td>
             <td><span id="errorMessage04" style="color:red;font-size: 11px"></span></td>
        </tr>
        
        <!-- Total invoiceable so far -->
         <tr>
            <td><?php admin_language_e('report_views_admin_form_TbtHdTotalInvoiceable'); ?></td>
            <td>
              <input type="text"  style="width:200px" name="totalInvoiceableSoFar" id="createReportForm_totalInvoiceableSoFar" value="<?php echo $total_invoiceable_so_far ?>" class="input-width">
            </td>
             <td><span id="errorMessage05" style="color:red;font-size: 11px"></span></td>
        </tr>
        
        <!-- Total invoiced so far -->
         <tr>
            <td><?php admin_language_e('report_views_admin_form_TbtHdTotalInvoiced'); ?></td>
            <td>
              <input type="text"  style="width:200px" name="totalInvoicedSoFar" id="createReportForm_totalInvoicedSoFar" value="<?php echo $total_invoiced_so_far ?>" class="input-width">
            </td>
             <td><span id="errorMessage06" style="color:red;font-size: 11px"></span></td>
        </tr>
        
        <!-- Invoices written this month -->
         <tr>
            <td><?php admin_language_e('report_views_admin_form_TbtHdInvMonth'); ?></td>
            <td>
              <input type="text"  style="width:200px" name="invoicesWrittenThisMonth" id="createReportForm_invoicesWrittenThisMonth" value="<?php echo $invoices_written_this_month ?>" class="input-width">
            </td>
             <td><span id="errorMessage07" style="color:red;font-size: 11px"></span></td>
        </tr>
        
        <!-- Total payments made till end of this month -->
         <tr>
            <td><?php admin_language_e('report_views_admin_form_TbtHdTotalPayment'); ?></td>
            <td>
              <input type="text"  style="width:200px" name="totalPaymentsMadeTillEndOfThisMonth" id="createReportForm_totalPaymentsMadeTillEndOfThisMonth" value="<?php echo $total_payments_made_till_end_of_this_month ?>" class="input-width">
            </td>
             <td><span id="errorMessage08" style="color:red;font-size: 11px"></span></td>
        </tr>
   </table>
    <input type="hidden" name="location_id" id="location_id" value="<?php echo $location_id?>">
    <input type="hidden" name="year" id="year" value="<?php echo $year?>">
    <input type="hidden" name="month" id="month" value="<?php echo $month?>">
</form>
<script>

    $('#createReportForm_costOfLocationAdvertising').change(function(click){   
        check_number_positive_negative($(this),$('#errorMessage01'));
    });

    $('#createReportForm_locationExternalReceipts').change(function(click){
        check_number_positive_negative($(this),$('#errorMessage03'));
    });

    $('#createReportForm_hardwareAmortization').change(function(click){
        check_number_positive_negative($(this),$('#errorMessage02'));
    });

    $('#createReportForm_currentOpenBalance').change(function(click){
        
         check_number($(this),$('#errorMessage04'));
    });
    
     $('#createReportForm_totalInvoiceableSoFar').change(function(click){
        
         check_number_positive_negative($(this),$('#errorMessage05'));
    });
    
     $('#createReportForm_totalInvoicedSoFar').change(function(click){
        
         check_number_positive_negative($(this),$('#errorMessage06'));
    });
    
     $('#createReportForm_invoicesWrittenThisMonth').change(function(click){
        
         check_number_positive_negative($(this),$('#errorMessage07'));
    });
    
     $('#createReportForm_totalPaymentsMadeTillEndOfThisMonth').change(function(click){
        
         check_number_positive_negative($(this),$('#errorMessage08'));
    });
    
     /*
     * Check number 
     */
    function check_number(id_value, id_message) {
        var x = id_value.val();
        
        // Check not numberic or numeric
        if (isNumber(x)) {
            // if numberic remove message error
             id_message.text('');
             id_value.removeClass('input-error');

        }else {
            // if not numberic show  message error
            id_value.addClass('input-error');
            id_message.text('<?php admin_language_e('report_views_admin_form_CheckNumberError'); ?>');
         }
    }
    
    /*
     * Check number ( positive and negative )
     */
    function check_number_positive_negative(id_value, id_message) {
        var x = id_value.val();
        
        // Check not numberic or numeric
        if (isNumber(x)) {
            
             // if not numberic remove message error
             id_message.text('');
             
             // Check numberic is positive and negative
             if( x < 0 ) {
                 
                // if numberic is negative show message error
                id_value.addClass('input-error');
                id_message.text('<?php admin_language_e('report_views_admin_form_CheckNumberPosNeg'); ?>');
                
            }else{
                 // if numberic remove message error
                id_value.removeClass("input-error");
                id_message.text('');
                
            }
        }else {
            // if not numberic show  message error
            id_value.addClass('input-error');
            id_message.text('<?php admin_language_e('report_views_admin_form_CheckNumberError'); ?>');
        }
    }
    /*
     *  isNumber
     *  return true if number 
     */
    
    function isNumber(n) {
        n = n.replace(/\./g, '').replace(',', '.');
        return !isNaN(parseFloat(n)) && isFinite(n);
    }
</script>