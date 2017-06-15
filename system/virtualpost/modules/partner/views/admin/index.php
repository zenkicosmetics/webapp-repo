<div class="header">
    <h2 style="font-size: 20px; margin-bottom: 10px"><?php admin_language_e('partner_view_admin_index_PartnerManagement'); ?></h2>
</div>
<div id="searchTableResult" style="margin: 0px;">
    <button id="addPartnerButton" class="admin-button"><?php admin_language_e('partner_view_admin_index_AddBtn'); ?></button>
    <div class="clear-height"></div>
    <table id="dataGridResult"></table>
    <div id="dataGridPager"></div>
</div>
<div class="clear-height"></div>

<!-- Content for dialog -->
<div class="hide">
    <div id="divAddPartner" class="input-form dialog-form">
    </div>
    <div id="divEditPartner" class="input-form dialog-form">
    </div>
</div>

<script type="text/javascript">
$(document).ready( function() {
    //#1297 check all tables in the system to minimize wasted space
    var tableH = $.getTableHeight() - 18;
    
    // Button 
    $('#addPartnerButton').button();
    
    // Call search method
    searchPartner();
    
    /**
     * Search data partner
     * function searchPartner()
     */
    function searchPartner() {
        $("#dataGridResult").jqGrid('GridUnload');
        
        // Url defined
        var url = '<?php echo base_url() ?>partner/admin';
        
        // Search 
        $("#dataGridResult").jqGrid({
            url: url,
            postData: $('#usesrSearchForm').serializeObject(),
            mtype: 'POST',
            datatype: "json",
            height: tableH,
            width: ($( window ).width()- 40), //#1297 check all tables in the system to minimize wasted space
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
                '<?php admin_language_e('partner_view_admin_index_ID'); ?>', 
                '<?php admin_language_e('partner_view_admin_index_PartnerType'); ?>',
                '<?php admin_language_e('partner_view_admin_index_PartnerCode'); ?>', 
                '<?php admin_language_e('partner_view_admin_index_PartnerName'); ?>', 
                '<?php admin_language_e('partner_view_admin_index_CompanyName'); ?>', 
                '<?php admin_language_e('partner_view_admin_index_Type'); ?>', 
                '<?php admin_language_e('partner_view_admin_index_ZipCode'); ?>', 
                '<?php admin_language_e('partner_view_admin_index_Street'); ?>',
                '<?php admin_language_e('partner_view_admin_index_City'); ?>',
                '<?php admin_language_e('partner_view_admin_index_Region'); ?>', 
                '<?php admin_language_e('partner_view_admin_index_Country'); ?>',
                '<?php admin_language_e('partner_view_admin_index_DurationRevShare'); ?>', 
                '<?php admin_language_e('partner_view_admin_index_RevShare'); ?>',
                '<?php admin_language_e('partner_view_admin_index_Discount'); ?>', 
                '<?php admin_language_e('partner_view_admin_index_Domain'); ?>', 
                '<?php admin_language_e('partner_view_admin_index_Action'); ?>'
            ],
            colModel:[
               {name:'id',index:'id', hidden: true},
               {name:'partner_type',index:'partner_type', hidden: true},
               {name:'Partner Code',index:'partner_code', width:150},
               {name:'Partner Name',index:'partner_name', width:180},
               {name:'Company Name',index:'company_name', width:180},
               {name:'partner_type',index:'partner_type', width:100},
               {name:'Zipcode',index:'location_zipcode', width:120, sortable: false},
               {name:'Street',index:'location_street', width:120, sortable: false},
               {name:'City',index:'location_location_city', width:120, sortable: false},
               {name:'Region',index:'location_region', width:120, sortable: false},
               {name:'Country',index:'location_country', width:120, sortable: false},
               {name:'duration_rev_share',index:'duration_rev_share', width:95, sortable: false},
               {name:'Rev-share',index:'rev_share', width:120, sortable: false},
               {name:'Discount',index:'Discount', width:150, sortable: false},
               {name:'Domain',index:'Domain', width:100, sortable: false},
               {name:'id',index:'id', width:90, sortable: false, align:"center", formatter: actionFormater}
            ],
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
                var data_row = $('#dataGridResult').jqGrid("getRowData",row_id);
                console.log(data_row);
            },
            loadComplete: function() {
                $.autoFitScreen($( window ).width()- 40);
            }
        });
    }

    function activeFormater(cellvalue, options, rowObject) {
        if (cellvalue == '1') {
            return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-tick"><?php admin_language_e('partner_view_admin_index_Check'); ?></span></span>';
        } else {
            return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete"><?php admin_language_e('partner_view_admin_index_UnCheck'); ?></span></span>';
        }
    }
    
    function actionFormater(cellvalue, options, rowObject) {
        if(rowObject[1] == '<?php echo APConstants::PARTNER_MARKETING_TYPE ?>'){
            return '<span style="display:inline-block;"><span class="managetables-icon managetables-setting-icon" data-id="' + cellvalue 
                    + '" title="<?php admin_language_e('partner_view_admin_index_WidgetsSetting'); ?>"></span></span>'
              + '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-edit" data-id="' + cellvalue 
              + '" title="<?php admin_language_e('partner_view_admin_index_Edit'); ?>"></span></span>'
              + '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete" data-id="' + cellvalue 
              + '" title="<?php admin_language_e('partner_view_admin_index_Delete'); ?>"></span></span>';
        }else{
              return '<span style="display:inline-block;"><span class="managetables-icon" ></span>&nbsp;&nbsp;&nbsp;&nbsp;</span>'
              +'<span style="display:inline-block;"><span class="managetables-icon managetables-icon-edit" data-id="' + cellvalue 
              + '" title="<?php admin_language_e('partner_view_admin_index_Edit'); ?>"></span></span>'
              + '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete" data-id="' + cellvalue
              + '" title="<?php admin_language_e('partner_view_admin_index_Delete'); ?>"></span></span>';
        }
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
            height: 550,
            width: 900,
            modal: true,
            title:'<?php admin_language_e('partner_view_admin_index_AddPartner'); ?>',
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
                    $(this).dialog('destroy');
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
            height: 550,
            width: 900,
            title:'<?php admin_language_e('partner_view_admin_index_EditPartner'); ?>',
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
                    $(this).dialog('destroy');
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
            message: '<?php admin_language_e('partner_view_admin_index_DeleteConfirmMessage'); ?>',
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

    // setting value click.
    $(".managetables-setting-icon").live("click", function(){
        var id = $(this).data('id');
        
         // Clear control of all dialog form
        $('.dialog-form').html('');

        // Open new dialog
        $('#divEditPartner').openDialog({
            autoOpen: false,
            height: 600,
            width: 650,
            modal: true,
            title: '<?php admin_language_e('partner_view_admin_index_WidgetSetting'); ?>',
            open: function() {
                $(this).load("<?php echo base_url() ?>partner/admin/edit_marketing?id=" + id, function() {
                    $('#addEditPartnerForm_LocationName').focus();
                });
            },
            buttons: {
                'Save': function() {
                    saveWidget();
                },
                'Cancel': function () {
                    $(this).dialog('destroy');
                }
            }
        });
        $('#divEditPartner').dialog('option', 'position', 'center');
        $('#divEditPartner').dialog('open');
    });
    
    /**
     * Save group
     */
    function savePartner() {
        var submitUrl = $('#addEditPartnerForm').attr('action');
        var action_type = $('#h_action_type').val();

        // validate partner martketting.
        /*if($("#partner_type").val() == "1"){
            $message = "";
            if($.trim($("#customer_discount").val()) == ""){
                $message += "The Customer discount field is required." + "<br/>";
            }
            
            if($.trim($("#duration_rev_share").val()) == ""){
                $message += "The Duration rev-share field is required.";
            }
            
            if($message != ''){
                $.displayError($message);
                return;
            }
        }*/

        $.ajaxSubmit({
            url: submitUrl,
            formId: "addEditPartnerForm",
            success: function(data) {
                if (data.status) {
                    if (action_type == 'add') {
                        $('#divAddPartner').dialog('destroy');
                    } else if (action_type == 'edit') {
                        $('#divEditPartner').dialog('destroy');
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

    /**
     * Save group
     */
    function saveWidget() {
        var submitUrl = $('#addEditWidgetForm').attr('action');

        $.ajaxSubmit({
            url: submitUrl,
            formId: "addEditWidgetForm",
            success: function(data) {
                if (data.status) {
                    $('#divEditPartner').dialog('destroy');
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