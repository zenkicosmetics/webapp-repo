<div class="header">
    <h2 style="font-size: 20px; margin-bottom: 10px"><?php admin_language_e('report_views_admin_email_send_hist_Header'); ?></h2>
</div>
<div class="ym-grid mailbox">
    <form id="emailSendHistReportingSearchForm" action="#" method="post">
        <div class="ym-gl">
            <div class="ym-grid input-item">
                <div class="ym-g70 ym-gl">
                    <input type="text" id="customerEmailForm_enquiry" name="enquiry" style="width: 250px"
                                value="" class="input-txt" maxlength=255 />
                    <button style="margin-left: 30px" id="emailSendHistReportingButton" class="admin-button"><?php admin_language_e('report_views_admin_email_send_hist_BtnSearch'); ?></button>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="button_container">
    <div class="button-func"></div>
</div>
<div id="gridwraper" style="margin: 0px;">
    <div id="searchTableResult" style="margin-top: 10px;">
        <table id="dataGridResult"></table>
        <div id="dataGridPager"></div>
    </div>
</div>
<div class="clear-height"></div>
<script type="text/javascript">
$(document).ready( function() {
    $('button').button();
    
	// Call search method
    emailSendHistReportings();

    /**
	 * Process when user click to search button
	 */
	$('#emailSendHistReportingButton').live('click', function(e) {
		emailSendHistReportings();
		e.preventDefault();
	});

	/**
	 * Search data
	 */
	function emailSendHistReportings() {
		$("#dataGridResult").jqGrid('GridUnload');
		var url = '<?php echo base_url() ?>admin/report/email_send_hist_search';
		var tableH = $.getTableHeight();
        $("#dataGridResult").jqGrid({
        	url: url,
        	postData: $('#emailSendHistReportingSearchForm').serializeObject(),
            mtype: 'POST',
        	datatype: "json",
        	width: ($( window ).width()- 40), //#1297 check all tables in the system to minimize wasted space
            height:tableH, //#1297 check all tables in the system to minimize wasted space 
            rowNum: '10',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            pager: "#dataGridPager",
            sortname: 'sent_date',
            sortorder: 'desc',
            viewrecords: true,
            shrinkToFit:false,
            rownumbers: true,
            captions: '',
            colNames:['',
                '<?php admin_language_e('report_views_admin_email_send_hist_TbtColToEmail'); ?>',
                '<?php admin_language_e('report_views_admin_email_send_hist_TbtColSentDate'); ?>',
                '<?php admin_language_e('report_views_admin_email_send_hist_TbtColSubject'); ?>',
                '<?php admin_language_e('report_views_admin_email_send_hist_TbtColContent'); ?>'],
            colModel:[
               {name:'id',index:'id', hidden: true},
               {name:'to_email',index:'to_email', width: 200, sortable: false},
               {name:'sent_date',index:'sent_date', width: 110, align:"center", sortable: false},
               {name:'subject',index:'subject', width: 600, sortable: false},
               {name:'content',index:'content', width: 900, sortable: false},
            ],
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
            },
            loadComplete: function() {
                $.autoFitScreen(($( window ).width()- 40));  
            }
        });
	}
});
</script>