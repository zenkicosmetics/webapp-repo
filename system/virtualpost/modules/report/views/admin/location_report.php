<style>
.input-error {
    border: 1px #800 solid !important;
    color: #800;
}
</style>
<div class="header">
    <h2 style="font-size: 20px; margin-bottom: 10px"><?php admin_language_e('report_views_admin_location_report_Header'); ?></h2>
</div>
<div class="ym-grid mailbox">
    <form id="locationReportingSearchForm"
          action="<?php echo base_url() ?>admin/report/location_report"
          method="post">
        <div class="ym-gl">
            <div class="ym-grid input-item">
                <div class="ym-g30 ym-gl" style="width: 100px">
                    <label style="text-align: left;"><?php admin_language_e('report_views_admin_location_report_LblLocation'); ?></label>
                </div>
                <div class="ym-g70 ym-gl">
                    <?php
                    echo my_form_dropdown(array(
                        "data" => $locations,
                        "value_key" => 'id',
                        "label_key" => 'location_name',
                        "value" => $location_id,
                        "name" => 'location_available_id',
                        "id" => 'location_available_id',
                        "clazz" => 'input-txt',
                        "style" => 'width: 150px',
                        "has_empty" => false
                    ));
                    ?>

                    <?php
                    echo my_form_dropdown(array(
                        "data" => $list_year,
                        "value_key" => 'id',
                        "label_key" => 'label',
                        "value" => $select_year,
                        "name" => 'year',
                        "id" => 'year',
                        "clazz" => 'input-txt',
                        "style" => 'width: 70px',
                        "has_empty" => false
                    ));
                    ?>
                    <?php
                    echo my_form_dropdown(array(
                        "data" => $list_month,
                        "value_key" => 'id',
                        "label_key" => 'label',
                        "value" => $select_month,
                        "name" => 'month',
                        "id" => 'month',
                        "clazz" => 'input-txt',
                        "style" => 'width: 50px',
                        "has_empty" => false
                    ));
                    ?>
                    <button style="margin-left: 30px"
                            id="locationReportingButton" class="admin-button"><?php admin_language_e('report_views_admin_location_report_BtnSearch'); ?></button>
                    <?php if(APContext::isSupperAdminUser()): ?>
                        <button id="generateReport" type="button"  class="admin-button"><?php admin_language_e('report_views_admin_location_report_BtnGenReport'); ?></button>
<!--                    <button style="margin-left: 30px" id="generateInvoiceTotalButton" type="button" class="admin-button">Update total rev share current month </button>
                    <button style="margin-left: 30px" id="generateInvoiceTotalButton2" type="button" class="admin-button">Update total rev share last month</button>-->
                    <?php endif;?>
                </div>
            </div>
        </div>
    </form>
</div>

<?php
/** START SOURCE TO VIEW LOCATION REPORT DETAIL AND DIRECT CHARGE */
    include 'system/virtualpost/modules/report/views/admin/partial/location_report_detail.php';
/** END SOURCE TO VIEW LOCATION REPORT DETAIL AND DIRECT CHARGE */
 ?>
<div class="clear-height"></div>
<!-- Content for dialog -->
<div class="hide">
	<div id="createReport" title="<?php admin_language_e('report_views_admin_location_report_TitGenReport'); ?>" class="input-form dialog-form">
	</div>
    <div id="divCreditNoteContainer" title="<?php admin_language_e('report_views_admin_location_report_TitCreditNote'); ?>" class="input-form dialog-form"></div>
</div>
<div class="hide" style="display: none;">
    <a id="view_report_file" class="iframe"><?php admin_language_e('report_views_admin_location_report_PreviewFile'); ?></a>
    <a id="display_pdf_invoice" class="iframe" href="#"><?php admin_language_e('report_views_admin_location_report_DisPdfInv'); ?></a>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('button').button();
        
         /**
         * #1072 add location report generation 
         */
        $('#generateReport').click(function() {
            // Clear control of all dialog form
            $('.dialog-form').html('');
            // Open new dialog
            $('#createReport').openDialog({
                autoOpen: false,
                height: 500,
                width: 730,
                modal: true,
                open: function() {
                    var v_location_id = $('#location_available_id').val();
                    var v_year = $('#year').val();
                    var v_month = $('#month').val();
                    
                    $(this).load("<?php echo base_url() ?>report/admin/create_location_report?location_id=" +v_location_id + "&year=" + v_year + "&month=" + v_month, function(){
                        
                    });
                },
                buttons: {
                    '<?php admin_language_e('report_views_admin_location_report_BtnGenReportDlg'); ?>': function() {
                        createReport();
                    }
                   
                }
            });
            $('#createReport').dialog('option', 'position', 'center');
            $('#createReport').dialog('open');

        });
        /**
        * Create report
        */
        function createReport() {
            var submitUrl = $('#createReportForm').attr('action');
            var base_url = '<?php echo base_url()?>';
            $.ajaxSubmit({
                url: submitUrl,
                formId: 'createReportForm',
                success: function(data) {
                    if (data.status) {
                        $('#createReport').dialog('close');
                        $('#view_report_file').attr("href", base_url + 'report/admin/view_pdf_report?location_id=' + data.data[0] +  '&year=' + data.data[1] + '&month=' + data.data[2] +
                                '&costOfLocationAdvertising=' + data.data[3] + '&hardwareAmortization=' + data.data[4] + 
                                 '&locationExternalReceipts=' + data.data[5] + '&currentOpenBalance=' + data.data[6] +
                                   '&totalInvoiceableSoFar=' + data.data[7] + '&hardwareAmortization=' + data.data[8] + 
                                 '&locationExternalReceipts=' + data.data[9] + '&currentOpenBalance=' + data.data[10]) ;
                         $.displayInfor(data.message, null, function(){
                            // $.get(base_url + "cases/admin/view_report", {location:data.data[0], startDate:data.data[1], endDate:data.data[2]});
                             $('#view_report_file').click();
                         });
                    } else {
                        console.log("Response: "+JSON.stringify(data));
                        
                        $.each( data.data.message, function( key, value ){
                            $("#createReportForm").find("[name='" + key + "']").addClass("input-error").attr("title",value);
                        });
                        $("#createReportForm").find(".input-error").tipsy({gravity: 'sw'});
                        $.displayError(data.message);
                    }
                }
            });
        }

        $('#view_report_file').fancybox({
            width: 1000,
            height: 800
        });
        $(".datepicker").datepicker();
        $(".datepicker").datepicker("option", "dateFormat", 'dd.mm.yy');

        /**
         * Process when user click to search button
         */
        $('#locationReportingButton').live('click', function (e) {
            $('#locationReportingSearchForm').submit();
            e.preventDefault();
        });
        
        $("#generateInvoiceTotalButton").click(function(){
            location.href = "<?php echo base_url() ?>admin/report/generate_invoice_total_by_location?ym=<?php echo date("Ym", now()); ?>";
        });
        
        $("#generateInvoiceTotalButton2").click(function(){
            location.href = "<?php echo base_url() ?>admin/report/generate_invoice_total_by_location?ym=<?php echo date("Ym", strtotime("last month")); ?>";
        });
        
        $("#creditNoteListButton").click(function(e){
            e.preventDefault();
            
            // Gets location_id
            var location_id = $("#location_available_id").val();
            
            // get year month
            var year = $("#year").val();
            var month = $("#month").val();
            
            // Clear control of all dialog form
            $('.dialog-form').html('');

            // Open new dialog
            $('#divCreditNoteContainer').openDialog({
                autoOpen: false,
                height: 550,
                width: 950,
                modal: true,
                open: function () {
                    $(this).load("<?php echo base_url() ?>admin/invoices/list_creditnote_by_location?location_id="+location_id+"&ym="+year+month, function () {
                        // do nothing
                    });
                },
                buttons: {
                    '<?php admin_language_e('report_views_admin_location_report_BtnGenReportClose'); ?>': function () {
                        $(this).dialog('close');
                    }
                }
            });
            $('#divCreditNoteContainer').dialog('option', 'position', 'center');
            $('#divCreditNoteContainer').dialog('open');
            return false;
        });
        
        $('#display_pdf_invoice').fancybox({
            width: 900,
            height: 700,
            'onClosed': function() {
                $("#fancybox-inner").empty();
            }
        });
        
        /**
         * When user click pdf icon
         */
        $("a.pdf").live('click', function() {
            var invoices_href = this.href;
            $('#display_pdf_invoice').attr('href', invoices_href);
            $('#display_pdf_invoice').click();

            return false;
        });
    });
</script>