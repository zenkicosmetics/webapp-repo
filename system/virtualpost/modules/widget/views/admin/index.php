<div class="header">
    <h2 style="font-size:  20px; margin-bottom: 10px">Partner Managerment</h2>
</div>
<div id="searchTableResult" style="margin: 10px;">
    <button id="addPartnerButton" class="admin-button">Add</button>
    <div class="clear-height"></div>
	<table id="dataGridResult"></table>
	<div id="dataGridPager"></div>
</div>
<div class="clear-height"></div>

<!-- Content for dialog -->
<div class="hide">
	<div id="divAddPartner" title="Add Partner" class="input-form dialog-form">
	</div>
	<div id="divEditPartner" title="Edit Partner" class="input-form dialog-form">
	</div>
</div>

<script type="text/javascript">
$(document).ready( function() {
    $('#addPartnerButton').button();
	
	// Call search method
	searchPartner();
	/**
	 * Search data
	 */
	function searchPartner() {
		$("#dataGridResult").jqGrid('GridUnload');
		var url = '<?php echo base_url() ?>partner/admin';
		var tableH = $(document).height() - $($('#user-page').children()[0]).height() - $("#mailbox>.mailbox").height() - 150;
        $("#dataGridResult").jqGrid({
        	url: url,
        	postData: $('#usesrSearchForm').serializeObject(),
            mtype: 'POST',
        	datatype: "json",
            height: tableH,
            width: 1100,
            rowNum: '<?php echo APContext::getAdminPagingSetting();?>',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            pager: "#dataGridPager",
            sortname: 'partner_name',
            viewrecords: true,
            shrinkToFit:false,
            multiselect: true,
            multiselectWidth: 40,
            captions: '',
            colNames:['ID','Partner Code', 'Partner Name', 'Company Name', 'Type', 'ZipCode', 'Street', 'City', 'Region', 'Country', 'Prepay Charge', 'Duration rev-share', 'Rev-share', 'Discount', 'Domain', 'Action'],
            colModel:[
               {name:'id',index:'id', hidden: true},
               {name:'Partner Code',index:'partner_code', width:120},
               {name:'Partner Name',index:'partner_name', width:120},
               {name:'Company Name',index:'company_name', width:120},
               {name:'Type',index:'partner_type', width:120},
               {name:'Zipcode',index:'location_zipcode', width:60},
               {name:'Street',index:'location_street', width:175},
               {name:'City',index:'location_location_city', width:60},
               {name:'Region',index:'location_region', width:100},
               {name:'Country',index:'location_country', width:80},
               {name:'Prepay Charge',index:'threhold_for_direct_prepay_charge', width:120},
               {name:'Duration rev-share',index:'duration_rev_share', width:120},
               {name:'Rev-share',index:'rev_share', width:120},
               {name:'Discount',index:'Discount', width:120},
               {name:'Domain',index:'Domain', width:120},
               {name:'id',index:'id', width:100, sortable: false, align:"center", formatter: actionFormater}
            ],
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
        		var data_row = $('#dataGridResult').jqGrid("getRowData",row_id);
        		console.log(data_row);
            },
            loadComplete: function() {
                $.autoFitScreen(1100);
            }
        });
	}

	function activeFormater(cellvalue, options, rowObject) {
		if (cellvalue == '1') {
			return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-tick">Check</span></span>';
		} else {
			return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete">UnCheck</span></span>';
		}
	}
	
	function actionFormater(cellvalue, options, rowObject) {
		console.log(rowObject);
		return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-edit" data-id="' + cellvalue + '" title="Edit"></span></span>'
		      + '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete" data-id="' + cellvalue + '" title="Delete"></span></span>';
	}

	/**
	 * Process when user click to add group button
	 */
	$('#addPartnerButton').click(function() {
	    // Clear control of all dialog form
	    $('.dialog-form').html('');
	    // Open new dialog
		$('#divAddPartner').openDialog({
			autoOpen: false,
			height: 400,
			width: 900,
			modal: true,
			open: function() {
				$(this).load("<?php echo base_url() ?>admin/partner/add", function() {
					$('#addEditPartnerForm_LocationName').focus();
				});
			},
			buttons: {
				'Save': function() {
					savePartner();
				},
				'Cancel': function () {
					$(this).dialog('close');
				}
			}
		});
		$('#divAddPartner').dialog('option', 'position', 'center');
		$('#divAddPartner').dialog('open');
	});

	/**
	 * Process when user click to edit icon.
	 */
	$('.managetables-icon-edit').live('click', function() {
	    var location_id = $(this).data('id');
	    
		 // Clear control of all dialog form
	    $('.dialog-form').html('');

	    // Open new dialog
		$('#divEditPartner').openDialog({
			autoOpen: false,
			height: 400,
			width: 900,
			modal: true,
			open: function() {
				$(this).load("<?php echo base_url() ?>partner/admin/edit?id=" + location_id, function() {
					$('#addEditPartnerForm_LocationName').focus();
				});
			},
			buttons: {
				'Save': function() {
					savePartner();
				},
				'Cancel': function () {
					$(this).dialog('close');
				}
			}
		});
		$('#divEditPartner').dialog('option', 'position', 'center');
		$('#divEditPartner').dialog('open');
	});

	/**
	 * Process when user click to delete icon.
	 */
	$('.managetables-icon-delete').live('click', function() {
	    var location_id = $(this).data('id');

		// Show confirm dialog
        $.confirm({
            message: 'Do you sure want to delete?',
            yes: function() {
            	var submitUrl = '<?php echo base_url()?>partner/admin/delete?id=' + location_id;
                $.ajaxExec({
                     url: submitUrl,
                     success: function(data) {
                         if (data.status) {
                             // Reload data grid
                        	 searchPartner();
                         } else {
                         	$.displayError(data.message);
                         }
                     }
                 });
            }
        });
	});
	
	/**
	 * Save group
	 */
	function savePartner() {
		var submitUrl = $('#addEditPartnerForm').attr('action');
		var action_type = $('#h_action_type').val();

		$.ajaxSubmit({
			url: submitUrl,
			formId: "addEditPartnerForm",
			success: function(data) {
				if (data.status) {
					if (action_type == 'add') {
					    $('#divAddPartner').dialog('close');
					} else if (action_type == 'edit') {
						$('#divEditPartner').dialog('close');
					}
					$.displayInfor(data.message, null,  function() {
						// Reload data grid
						searchPartner();
					});
									
				} else {
					$.displayError(data.message);
				}
			}
		});
	}
});
</script>