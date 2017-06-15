<div class="header">
    <h2 style="font-size: 20px; margin-bottom: 10px"><?php admin_language_e('report_views_admin_storage_fee_report_Header'); ?></h2>
</div>
<div class="ym-grid mailbox">
    <form id="storageFeeReportingSearchForm" action="#" method="post">
        <div class="ym-gl">
            <div class="ym-grid input-item">
                <div class="ym-g30 ym-gl" style="width: 130px">
                    <label style="text-align: left;"><?php admin_language_e('report_views_admin_storage_fee_report_LblLocation'); ?></label>
                </div>
                <div class="ym-g70 ym-gl">
                    <?php
                    echo my_form_dropdown(array (
                            "data" => $locations,
                            "value_key" => 'id',
                            "label_key" => 'location_name',
                            "value" => APContext::getLocationUserSetting(),
                            "name" => 'location_available_id',
                            "id" => 'location_available_id',
                            "clazz" => 'input-txt',
                            "style" => 'width: 450px',
                            "has_empty" => true 
                    ));
                    ?>
                </div>
            </div>
            <!--#1298 replace search for customer id in storage report with full search-->
             <div class="ym-grid input-item">
                <div class="ym-g30 ym-gl" style="width: 130px">
                    <label style="text-align: left;"><?php admin_language_e('report_views_admin_storage_fee_report_LblSearch'); ?></label>
                </div>
                 <div class="ym-g70 ym-gl">
                     <input type="text" id="storageFeeReportingSearchForm_enquiry" name="enquiry" 
                            placeholder="<?php admin_language_e('report_views_admin_storage_fee_report_PlaceholderSearch'); ?>"
                            style="width: 450px" value="" class="input-txt" maxlength=255 />
                     <button style="margin-left: 30px" id="storageFeeReportingButton" class="admin-button">Search</button>
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
<div class="hide">
    <form id="hiddenAccessCustomerSiteForm" target="blank" action="<?php echo base_url()?>admin/customers/view_site" method="post">
        <input type="hidden" id="hiddenAccessCustomerSiteForm_customer_id" name="customer_id" value="" />
    </form>
</div>
<div class="clear-height"></div>
<script type="text/javascript">
$(document).ready( function() {
    $('button').button();
    
	// Call search method
    storageFeeReportings();

    /**
	 * Process when user click to search button
	 */
	$('#storageFeeReportingButton').live('click', function(e) {
		storageFeeReportings();
		e.preventDefault();
	});

	/**
	 * Search data
	 */
	function storageFeeReportings() {
		$("#dataGridResult").jqGrid('GridUnload');
		var url = '<?php echo base_url() ?>admin/report/storage_fee_search';
		var tableH = $.getTableHeight() + 10;
        $("#dataGridResult").jqGrid({
        	url: url,
        	postData: $('#storageFeeReportingSearchForm').serializeObject(),
            mtype: 'POST',
        	datatype: "json",
        	width: ($( window ).width()- 40), //#1297 check all tables in the system to minimize wasted space
            height:tableH, //#1297 check all tables in the system to minimize wasted space 
            rowNum: '<?php echo APContext::getAdminPagingSetting();?>',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            pager: "#dataGridPager",
            sortname: 'customer_code',
            sortorder: 'desc',
            viewrecords: true,
            shrinkToFit:false,
            rownumbers: true,
            captions: '',
            colNames:['', '',
                '<?php admin_language_e('report_views_admin_storage_fee_report_ColCusId'); ?>',
                '<?php admin_language_e('report_views_admin_storage_fee_report_ColEmail'); ?>',
                '<?php admin_language_e('report_views_admin_storage_fee_report_ColEnvId'); ?>',
                '<?php admin_language_e('report_views_admin_storage_fee_report_ColEnvType'); ?>',
                '<?php admin_language_e('report_views_admin_storage_fee_report_ColIncommingDate'); ?>',
                '<?php admin_language_e('report_views_admin_storage_fee_report_ColSentOutOn'); ?>',
                '<?php admin_language_e('report_views_admin_storage_fee_report_ColTrashOn'); ?>',
                '<?php admin_language_e('report_views_admin_storage_fee_report_ColPreTotal'); ?>',
                '<?php admin_language_e('report_views_admin_storage_fee_report_ColCurrentMonth'); ?>',
                '<?php admin_language_e('report_views_admin_storage_fee_report_ColPrice'); ?>',
                '<?php admin_language_e('report_views_admin_storage_fee_report_ColAmount'); ?>'],
            colModel:[
               {name:'id',index:'id', hidden: true},
               {name:'customer_id',index:'customer_id', hidden: true},
               {name:'customer_code',index:'customer_code', width: 150, sortable: false, formatter: toCustomerFormater02},
               {name:'email',index:'email', width: 300, sortable: false},
               {name:'envelope_code',index:'envelope_code', width: 300, sortable: false},
               {name:'envelope_type',index:'envelope_type', width: 100, align: "center", sortable: false},
               {name:'incomming_date',index:'incomming_date', width: 120, align:"center", sortable: false},
               {name:'sent_out_on',index:'sent_out_on', width: 110, sortable: false},
               {name:'trash_on',index:'trash_on', width: 110, sortable: false},
               {name:'previous_total',index:'previous_total', width: 150, align:"center", sortable: false},
               {name:'current_month',index:'current_month', width: 150, align:"center", sortable: false},
               {name:'price',index:'price', width: 150, sortable: false},
               {name:'amount',index:'amount', width: 150, sortable: false}
            ],
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
            },
            loadComplete: function() {
                $.autoFitScreen(($( window ).width()- 40));   //#1297 check all tables in the system to minimize wasted space
            }
        });
	}

	function toCustomerFormater02(cellvalue, options, rowObject) {
		if(typeof rowObject.cell === "undefined"){
		    if($.type(rowObject)=="object"){
		        return '<a class="access_customer_site" data-id="' + rowObject.customer_id + '" style="text-decoration: underline;"  >' + rowObject.customer_code + '</a>';
		    }else{
		        return '<a class="access_customer_site" data-id="' + rowObject[1] + '" style="text-decoration: underline;"  >' + rowObject[2] + '</a>';
		    }
		}else{
		    return '<a class="access_customer_site" data-id="' + rowObject.cell[1] + '" style="text-decoration: underline;"  >' + rowObject.cell[2] + '</a>';
		}
	}

	/**
	 * Access the customer site
	 */
	$('.access_customer_site').live('click', function() {
	    var customer_id = $(this).attr('data-id');
	    $('#hiddenAccessCustomerSiteForm_customer_id').val(customer_id);
	    $('#hiddenAccessCustomerSiteForm').submit();
	});
});
</script>