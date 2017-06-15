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
		var url = '<?php echo base_url() ?>account/setting/hitory_term_condition?type=1';
		
        $("#dataGridTermsServiceResult").jqGrid({
        	url: url,
            postData: $('#usesrSearchForm').serializeObject(),
            mtype: 'POST',
        	datatype: "json",
            height: 350,
            width: 750, 
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
            colNames:['ID','URL', 'Creted Date', 'Current','Action'],
            colModel:[
               {name:'id',index:'id', hidden: true},
               {name:'file_name',index:'file_name', width:350},
               {name:'created_date',index:'created_date', width:120, align:"center"},
               {name:'use_flag',index:'use_flag', width:100, sortable: false, align:"center", formatter: activeFormater},
               {name:'id',index:'id', width:100, sortable: false, align:"center", formatter: actionFormater}
            ],
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
        		// var data_row = $('#dataGridResult').jqGrid("getRowData",row_id);
        		// console.log(data_row);
            },
            loadComplete: function() {
                 $.autoFitScreen(750); 
            }
        });
	}

	function activeFormater(cellvalue, options, rowObject) {
		if (cellvalue == '1') {
			return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-tick">Check</span></span>';
		} else {
			return '';
		}
	}
	
	function actionFormater(cellvalue, options, rowObject) {
        if(rowObject[3] == 1){
            return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-edit view_terms" title="View Terms Of Service" data-id="' + cellvalue + '"></span></span>';
        }
        return "";
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
		// Clear control of all dialog form
        $('#showAddEditTermConditionWindow').html('');

        // Open new dialog
        $('#showAddEditTermConditionWindow').openDialog({
            autoOpen: false,
            height: 550,
            width: 950,
            modal: true,
            open: function () {
                $(this).load(Account.ajaxUrls.add_term_and_condition_url + "?id=" + id, function () {
                });
            },
            buttons: {
                'Save': function(){
                    Account.saveUploadTermConditionEnterprise();
                },
                'Close': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#showAddEditTermConditionWindow').dialog('option', 'position', 'center');
        $('#showAddEditTermConditionWindow').dialog('open');
        return false;
	});
	
});
</script>