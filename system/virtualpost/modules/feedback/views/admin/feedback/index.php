<div class="button_container">
    <div class="input-form">
        <form id="frmSearchForm" method="post">
        	<table>
        		<tr>
        			<th><?php echo lang('feedback.keyword'); ?></th>
        			<td><input type="text" id="keyword" name="keyword" value=""
        				class="input-width custom_autocomplete" maxlength=50 /></td>
        			<th><?php echo lang('feedback.status_label'); ?></th>
        			<td>
        			    <?php echo code_master_form_dropdown(array(
        			         "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                             "value" => '',
                             "name" => 'status',
                             "id"	=> 'status',
                             "clazz" => 'input-width',
                             "style" => '',
        			         "has_empty" => true
        			     ));?>
        			</td>
        			<td><button id="searchfeedbackButton" class="admin-button">Search</button></td>
        		</tr>
        	</table>
        </form>
    </div>
    <div class="button-func">
        <button id="addButton" class="admin-button">Add</button>
    </div>
</div>

<div class="button_container">
    <div class="input-form">
	<div id="searchTableResult" style="margin: 10px;">
    	<table id="dataGridResult"></table>
    	<div id="dataGridPager"></div>
    </div>
    <div class="clear-height"></div>


	</div>
</div>

<script type="text/javascript">
$(document).ready( function() {
	
	// Call search method
	searchfeedbacks();
	/**
	 * Search data
	 */
	function searchfeedbacks() {
		$("#dataGridResult").jqGrid('GridUnload');
		var url = '<?php echo base_url() ?>admin/feedback';
		
        $("#dataGridResult").jqGrid({
        	url: url,
        	postData: $('#frmSearchForm').serializeObject(),
            mtype: 'POST',
        	datatype: "json",
            height: "100%",
            width: DATAGRID_WIDTH,
            rowNum: '<?php echo Settings::get(APConstants::NUMBER_RECORD_PER_PAGE_CODE);?>',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            pager: "#dataGridPager",
            sortname: 'Name',
            viewrecords: true,
            shrinkToFit:false,
            captions: '',
            colNames:['ID','Status','Name','Subject', 'Message', ''],
            colModel:[
               {name:'FeedbackID',index:'FeedbackID', hidden: true},
               {name:'Status',index:'Status', width:50, align:"center", formatter: statusFormater },
               {name:'Name',index:'Name', width:200},
               {name:'Subject',index:'Subject', width:300},
               {name:'Message',index:'Message', width:380},
               {name:'FeedbackID',index:'FeedbackID', width:75, sortable: false, align:"center", formatter: actionFormater}
            ],
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
        		var data_row = $('#dataGridResult').jqGrid("getRowData",row_id);
        		console.log(data_row);
            }
        });
	}

	function statusFormater(cellvalue, options, rowObject) {
		if (cellvalue == '1') {
			return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-tick">Check</span></span>';
		} else {
			return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete">UnCheck</span></span>';
		}
	}
	
	function actionFormater(cellvalue, options, rowObject) {
		return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-edit" data-id="' + cellvalue + '"></span></span>'
		      + '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete" data-id="' + cellvalue + '"></span></span>';
	}

	/**
	 * Process when user click to search button
	 */
	$('#searchfeedbackButton').click(function(e) {
		searchfeedbacks();
		e.preventDefault();
	});
	
	/**
	 * Process when user click to add button.
	 */
	$('#addButton').live('click', function() {
	    var id = $(this).data('id');
	    window.location = "<?php echo base_url() ?>admin/feedback/add";
	});

	/**
	 * Process when user click to edit icon.
	 */
	$('.managetables-icon-edit').live('click', function() {
	    var id = $(this).data('id');
	    window.location = "<?php echo base_url() ?>admin/feedback/edit/"+id;
	});

	/**
	 * Process when user click to delete icon.
	 */
	$('.managetables-icon-delete').live('click', function() {
	    var id = $(this).data('id');

		 // Show confirm dialog
        $.confirm({
            message: 'Are you sure you want to delete?',
            yes: function() {
            	var submitUrl = '<?php echo base_url()?>admin/feedback/delete/' + id;
                $.ajaxExec({
                     url: submitUrl,
                     success: function(data) {
                         if (data.status) {
                             // Reload data grid
                        	 searchfeedbacks();
                         } else {
                         	$.displayError(data.message);
                         }
                     }
                 });
            }
        });
	});
});
</script>
