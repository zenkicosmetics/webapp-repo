<div class="header">
    <h2 style="font-size: 20px; margin-bottom: 10px"><?php admin_language_e('setting_view_term_term_SettingsTermsTerm'); ?></h2>
</div>
<form id="usesrSearchForm" method="post" action="">
    <?php 
    if($type == 'enterprise'){
        echo "Customer: ";
        echo my_form_dropdown(array(
            "data" => $list_enterprise_customer,
            "value_key" => 'customer_id',
            "label_key" => 'customer_code',
            "value" => !empty($list_enterprise_customer) ? $list_enterprise_customer[0]->customer_id : "",
            "name" => 'customer_id',
            "id"    => 'customer_id',
            "clazz" => 'input-width',
            "style" => 'width: 130px',
            "has_empty" => false
        ));
    } else { ?>
    <button id="addTermsServiceButton" type="button"><?php admin_language_e('setting_view_term_term_AddTermBtn'); ?></button>
    <?php }?>
</form>

<br />
<!-- Term of service-->
<div id="searchTableTermsServiceResult" style="margin-top: 10px;">
	<table id="dataGridTermsServiceResult"></table>
	<div id="dataGridTermsServicePager"></div>
</div>
<script type="text/javascript">
$(document).ready( function() {
	searchTermsService();
    
    $("#customer_id").live('change', function(){
        searchTermsService();
        return false;
    });
    
	/**
	 * Search data for terms and service
	 */
	function searchTermsService() {
		$("#dataGridTermsServiceResult").jqGrid('GridUnload');
		var url = '<?php echo base_url() ?>settings/terms?type=1';
		var tableH = $.getTableHeight() - 30; //#1297 check all tables in the system to minimize wasted space
        $("#dataGridTermsServiceResult").jqGrid({
        	url: url,
            postData: $('#usesrSearchForm').serializeObject(),
            mtype: 'POST',
        	datatype: "json",
            height: tableH, //#1297 check all tables in the system to minimize wasted space,
            width: ($( window ).width()- 40), //#1297 check all tables in the system to minimize wasted space
            rowNum: '<?php echo APContext::getAdminPagingSetting();?>',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            pager: "#dataGridTermsServicePager",
            sortname: 'created_date',
            sortorder: "desc",
            viewrecords: true,
            shrinkToFit:false,
            multiselect: true,
            multiselectWidth: 40,
            captions: '',
            colNames:[
                '<?php admin_language_e('setting_view_term_term_ID'); ?>',
                '<?php admin_language_e('setting_view_term_term_URL'); ?>', 
                '<?php admin_language_e('setting_view_term_term_CreatedDate'); ?>', 
                '<?php admin_language_e('setting_view_term_term_Current'); ?>',
                '<?php admin_language_e('setting_view_term_term_Action'); ?>'
            ],
            colModel:[
               {name:'id',index:'id', hidden: true},
               {name:'file_name',index:'file_name', width:830},
               {name:'created_date',index:'created_date', width:500, align:"center"},
               {name:'use_flag',index:'use_flag', width:200, sortable: false, align:"center", formatter: activeFormater},
               {name:'id',index:'id', width:250, sortable: false, align:"center", formatter: actionFormater}
            ],
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
        		// var data_row = $('#dataGridResult').jqGrid("getRowData",row_id);
        		// console.log(data_row);
            },
            loadComplete: function() {
                 $.autoFitScreen($( window ).width()- 40); //#1297 check all tables in the system to minimize wasted space
            }
        });
	}

	function activeFormater(cellvalue, options, rowObject) {
		if (cellvalue == '1') {
			return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-tick"><?php admin_language_e('setting_view_term_term_Check'); ?></span></span>';
		} else {
			return '';
		}
	}
	
	function actionFormater(cellvalue, options, rowObject) {
		return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-edit view_terms" title="<?php admin_language_e('setting_view_term_term_ViewTerms'); ?>" data-id="' 
                + cellvalue + '"></span></span>';
	}

	/**
	 * Add terms of service
	 */
	$('#addTermsServiceButton').button().click(function(){
	    window.location = "<?php echo base_url() ?>settings/terms/add_terms";
	});
	
	/**
	 * Process when user click to view icon.
	 */
	$('.view_terms').live('click', function() {
		var id = $(this).data('id');
		window.location = "<?php echo base_url() ?>settings/terms/edit_terms?id=" + id;
	});
	
});
</script>