<div class="header">
    <h2 style="font-size: 20px; margin-bottom: 10px"><?php admin_language_e('setting_view_term_privacy_SettingsTermsPrivacy'); ?></h2>
</div>
<button id="addPrivacyStatementButton"><?php admin_language_e('setting_view_term_privacy_AddPrivacyBtn'); ?></button>
<br />
<div id="searchTablePrivacyResult" style="margin-top: 10px;">
	<table id="dataGridPrivacyResult"></table>
	<div id="dataGridPrivacyPager"></div>
</div>
<script type="text/javascript">
$(document).ready( function() {
	searchPrivacyStatement();
	/**
	 * Search data for terms and service
	 */
	function searchPrivacyStatement() {
		$("#dataGridPrivacyResult").jqGrid('GridUnload');
		var url = '<?php echo base_url() ?>settings/terms?type=2';
		var tableH = $.getTableHeight() - 30; //#1297 check all tables in the system to minimize wasted space
        $("#dataGridPrivacyResult").jqGrid({
        	url: url,
        	// postData: $('#usesrSearchForm').serializeObject(),
            mtype: 'POST',
        	datatype: "json",
            height:  tableH, //#1297 check all tables in the system to minimize wasted space,
            width: ($( window ).width()- 40), //#1297 check all tables in the system to minimize wasted space
            rowNum: '<?php echo APContext::getAdminPagingSetting();?>',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            pager: "#dataGridPrivacyPager",
            sortname: 'created_date',
            sortorder: "desc",
            viewrecords: true,
            shrinkToFit:false,
            multiselect: true,
            multiselectWidth: 40,
            captions: '',
            colNames:[
                '<?php admin_language_e('setting_view_term_privacy_ID'); ?>',
                '<?php admin_language_e('setting_view_term_privacy_URL'); ?>', 
                '<?php admin_language_e('setting_view_term_privacy_CreatedDate'); ?>', 
                '<?php admin_language_e('setting_view_term_privacy_Current'); ?>',
                '<?php admin_language_e('setting_view_term_privacy_Action'); ?>'
            ],
            colModel:[
               {name:'id',index:'id', hidden: true},
               {name:'file_name',index:'file_name', width:900},
               {name:'created_date',index:'created_date', width:500, align:"center"},
               {name:'use_flag',index:'use_flag', sortable: false, align:"center", width:200, formatter: activeFormater},
               {name:'id',index:'id', width:200, sortable: false, align:"center", formatter: actionPrivacyFormater}
            ],
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
        		// var data_row = $('#dataGridResult').jqGrid("getRowData",row_id);
        		// console.log(data_row);
            },
            loadComplete: function() {
                 $.autoFitScreen($( window ).width()- 40);//#1297 check all tables in the system to minimize wasted space
            }
        });
	}

	function actionPrivacyFormater(cellvalue, options, rowObject) {
		return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-edit view_privacy" title="<?php admin_language_e('setting_view_term_privacy_ViewPrivacy'); ?>" data-id="' 
                + cellvalue + '"></span></span>';
	}
	function activeFormater(cellvalue, options, rowObject) {
		if (cellvalue == '1') {
			return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-tick"><?php admin_language_e('setting_view_term_privacy_Check'); ?></span></span>';
		} else {
			return '';
		}
	}

	/**
	 * Add terms of service
	 */
	$('#addPrivacyStatementButton').button().click(function(){
		window.location = "<?php echo base_url() ?>settings/terms/add_privacy";
	});

	/**
	 * Process when user click to view icon.
	 */
	$('.view_privacy').live('click', function() {
		var id = $(this).data('id');
		window.location = "<?php echo base_url() ?>settings/terms/edit_privacy?id=" + id;
	});
	
});
</script>