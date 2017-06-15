<div class="header">
    <h2 style="font-size:  20px; margin-bottom: 10px"><?php admin_language_e('cases_view_service_partner_index_ServicePartnerManagerment'); ?></h2>
</div>
<div id="searchTableResult" style="margin: 10px;">
    <button id="addServicePartnerButton" class="admin-button">Add</button>
    <div class="clear-height"></div>
    <table id="dataGridResult"></table>
    <div id="dataGridPager"></div>
</div>
<div class="clear-height"></div>

<!-- Content for dialog -->
<div class="hide">
    <div id="divAddServicePartner" title="Add Service Partner" class="input-form dialog-form">
    </div>
    <div id="divEditServicePartner" title="Edit Service Partner" class="input-form dialog-form">
    </div>
</div>

<script type="text/javascript">
$(document).ready( function() {
    $('#addServicePartnerButton').button();

    // Call search method
    searchServicePartner();
    /**
     * Search data
     */
    function searchServicePartner() {
        $("#dataGridResult").jqGrid('GridUnload');
        var url = '<?php echo base_url() ?>cases/service_partner/index';
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
            colNames:[
                'ID',
                '<?php admin_language_e('cases_view_service_partner_index_PartnerName'); ?>',
                '<?php admin_language_e('cases_view_service_partner_index_ContactName'); ?>',
                '<?php admin_language_e('cases_view_service_partner_index_Email'); ?>',
                '<?php admin_language_e('cases_view_service_partner_index_PhoneNumber'); ?>',
                '<?php admin_language_e('cases_view_service_partner_index_CreatedDate'); ?>',
                '<?php admin_language_e('cases_view_service_partner_index_Action'); ?>'
            ],
            colModel:[
               {name:'partner_id',index:'partner_id', hidden: true},
               {name:'partner_name',index:'partner_name', width:200},
               {name:'main_contact_point',index:'main_contact_point', width:250},
               {name:'email',index:'email', width:200},
               {name:'phone',index:'phone', width:150},
               {name:'created_date',index:'created_date', width:150},
               {name:'partner_id',index:'partner_id', width:100, sortable: false, align:"center", formatter: actionFormater}
            ],
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
                var data_row = $('#dataGridResult').jqGrid("getRowData",row_id);
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
        return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-edit" data-id="' + cellvalue + '" title="Edit"></span></span>'
              + '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete" data-id="' + cellvalue + '" title="Delete"></span></span>';
    }

    /**
     * Process when user click to add group button
     */
    $('#addServicePartnerButton').click(function() {
        // Clear control of all dialog form
        $('.dialog-form').html('');
        // Open new dialog
        $('#divAddServicePartner').openDialog({
            autoOpen: false,
            height: 350,
            width: 450,
            modal: true,
            open: function() {
                $(this).load("<?php echo base_url() ?>cases/service_partner/add", function() {
                });
            },
            buttons: {
                'Save': function() {
                    saveServicePartner();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#divAddServicePartner').dialog('option', 'position', 'center');
        $('#divAddServicePartner').dialog('open');
    });

    /**
     * Process when user click to edit icon.
     */
    $('.managetables-icon-edit').live('click', function() {
        var service_partner_id = $(this).data('id');

         // Clear control of all dialog form
        $('.dialog-form').html('');

        // Open new dialog
        $('#divEditServicePartner').openDialog({
            autoOpen: false,
            height: 350,
            width: 450,
            modal: true,
            open: function() {
                $(this).load("<?php echo base_url() ?>cases/service_partner/edit?id=" + service_partner_id, function() {
                });
            },
            buttons: {
                'Save': function() {
                    saveServicePartner();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#divEditServicePartner').dialog('option', 'position', 'center');
        $('#divEditServicePartner').dialog('open');
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
                var submitUrl = '<?php echo base_url()?>cases/service_partner/delete?id=' + location_id;
                $.ajaxExec({
                     url: submitUrl,
                     success: function(data) {
                         if (data.status) {
                             // Reload data grid
                             searchServicePartner();
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
    function saveServicePartner() {
        var submitUrl = $('#addEditServicePartnerForm').attr('action');
        var action_type = $('#h_action_type').val();

        $.ajaxSubmit({
            url: submitUrl,
            formId: "addEditServicePartnerForm",
            success: function(data) {
                if (data.status) {
                    if (action_type == 'add') {
                        $('#divAddServicePartner').dialog('close');
                    } else if (action_type == 'edit') {
                        $('#divEditServicePartner').dialog('close');
                    }
                    $.displayInfor(data.message, null,  function() {
                        // Reload data grid
                        searchServicePartner();
                    });

                } else {
                    $.displayError(data.message);
                }
            }
        });
    }
});
</script>