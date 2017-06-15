var PricingTemplateList = {
    /*
     * Ajax URLs
     */
    ajaxUrls: {
        index: null,
        add: null,
        edit: null,
        delete: null,
        setting_value: null
    },

    configs: {
        rowNum: null,
        rowList: null,
        default_pricing_model_invoice:0
    },

    /*
     *  Initialize interface
     */
    init: function (baseUrl, rowNum, rowList, DEFAULT_PRICING_MODEL_INVOICE) {
        this.initAjaxUrls(baseUrl);
        this.configs.rowNum = rowNum;
        this.configs.rowList = rowList;
        this.configs.default_pricing_model_invoice = DEFAULT_PRICING_MODEL_INVOICE;

        // Call search method
        this.searchPricingTemplates();

        this.initScreen();

        this.initEventListeners();
    },

    initAjaxUrls: function (baseUrl) {
        this.ajaxUrls.index = baseUrl + 'price/admin';
        this.ajaxUrls.add = baseUrl + 'price/admin/add';
        this.ajaxUrls.edit = baseUrl + 'price/admin/edit';
        this.ajaxUrls.delete = baseUrl + 'price/admin/delete';
        this.ajaxUrls.setting_value = baseUrl + 'price/admin/prices';
    },

    initScreen: function () {
        $('#addPartnerButton').button();
    },

    initEventListeners: function () {
        // Process when user click on add partner button
        $('#addPartnerButton').click(function () {
            PricingTemplateList.addPartner();
        });

        // Process when user click on edit icon.
        $('.managetables-icon-edit').live('click', function () {
            PricingTemplateList.editPartner(this);
        });

        // Process when user click on delete icon
        $('.managetables-icon-delete').live('click', function () {
            PricingTemplateList.deletePartner(this);
        });

        // setting value click.
        $(".managetables-setting-icon").live("click", function () {
            var id = $(this).data('id');
            location.href = PricingTemplateList.ajaxUrls.setting_value + "?id=" + id;
        });
        
        $("#ddlPricingType").live("change", function(){
            PricingTemplateList.searchPricingTemplates();
        });
    },

    searchPricingTemplates: function () {
        $("#dataGridResult").jqGrid('GridUnload');

        var tableH = $.getTableHeight() - 100;
        var time = new Date();
        var current = time.getUTCSeconds();
        var type = $("#ddlPricingType").val();
        $("#dataGridResult").jqGrid({
            url: PricingTemplateList.ajaxUrls.index + "?type="+type+"&t="+current,
            postData: $('#usesrSearchForm').serializeObject(),
            mtype: 'POST',
            datatype: "json",
            height: tableH, //#1297 check all tables in the system to minimize wasted space 
            width: ($( window ).width()- 100), //#1297 check all tables in the system to minimize wasted space
            rowNum: PricingTemplateList.configs.rowNum,
            rowList: PricingTemplateList.configs.rowList,
            pager: "#dataGridPager",
            sortname: 'name',
            viewrecords: true,
            shrinkToFit: false,
            multiselect: true,
            multiselectWidth: 40,
            captions: '',
            colNames: ['ID', 'Name', '# of uses','Type', 'Description', 'Action'],
            colModel: [
                {name: 'id', index: 'id', hidden: true},
                {name: 'Name', index: 'name', width: 200},
                {name: '# of uses', index: 'number_uses', width: 210, align:"center"},
                {name: '# of uses', index: 'number_uses', width: 120, align:"center"},
                {name: 'Description', index: 'description', width: 300},
                {
                    name: 'id',
                    index: 'id',
                    width: 120,
                    sortable: false,
                    align: "center",
                    formatter: PricingTemplateList.actionFormater
                }
            ],

            // When double click to row
            ondblClickRow: function (row_id, iRow, iCol, e) {
                var data_row = $('#dataGridResult').jqGrid("getRowData", row_id);
                console.log(data_row);
            },
            loadComplete: function () {
                $.autoFitScreen(($( window ).width()- 100)); //#1297 check all tables in the system to minimize wasted space
            }
        });
    },

    activeFormater: function (cellvalue, options, rowObject) {
        if (cellvalue == '1') {
            return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-tick">Check</span></span>';
        } else {
            return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete">UnCheck</span></span>';
        }
    },

    actionFormater: function (cellvalue, options, rowObject) {
        if (cellvalue == 0) {
            return '<span style="display:inline-block;"><span class="managetables-icon managetables-setting-icon" data-id="' + cellvalue + '" title="Setting Template Pricing Values"></span></span>';
        } else {
            return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-edit" data-id="' + cellvalue + '" title="Edit"></span></span>'
                + '<span style="display:inline-block;"><span class="managetables-icon managetables-setting-icon" data-id="' + cellvalue + '" title="Setting Template Pricing Values"></span></span>'
                + '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete" data-id="' + cellvalue + '" title="Delete"></span></span>';
        }
    },

    addPartner: function () {
        // Clear control of all dialog form
        $('.dialog-form').html('');

        // Open new dialog
        $('#divAddPartner').openDialog({
            autoOpen: false,
            height: 350,
            width: 450,
            modal: true,
            open: function () {
                $(this).load(PricingTemplateList.ajaxUrls.add, function () {
                    $('#addEditPartnerForm_LocationName').focus();
                });
            },
            buttons: {
                'Save': function () {
                    PricingTemplateList.savePricingTemplate();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#divAddPartner').dialog('option', 'position', 'center');
        $('#divAddPartner').dialog('open');
    },

    editPartner: function (elem) {
        var location_id = $(elem).data('id');

        // Clear control of all dialog form
        $('.dialog-form').html('');

        // Open new dialog
        $('#divEditPartner').openDialog({
            autoOpen: false,
            height: 350,
            width: 450,
            modal: true,
            open: function () {
                var loadUrl = PricingTemplateList.ajaxUrls.edit + '?id=' + location_id;
                $(this).load(loadUrl, function () {
                    $('#addEditPartnerForm_LocationName').focus();
                });
            },
            buttons: {
                'Save': function () {
                    PricingTemplateList.savePricingTemplate();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#divEditPartner').dialog('option', 'position', 'center');
        $('#divEditPartner').dialog('open');
    },

    deletePartner: function (elem) {
        var location_id = $(elem).data('id');
        if (location_id == PricingTemplateList.configs.default_pricing_model_invoice) {
            $.displayError("You can not delete this template. <br>Because this is default pricing template.");
            return false;
        }

        // Show confirm dialog
        $.confirm({
            message: 'Do you sure want to delete?',
            yes: function () {
                var submitUrl = PricingTemplateList.ajaxUrls.delete + '?id=' + location_id;
                $.ajaxExec({
                    url: submitUrl,
                    success: function (data) {
                        if (data.status) {
                            // Reload data grid
                            PricingTemplateList.searchPricingTemplates();
                        } else {
                            $.displayError(data.message);
                        }
                    }
                });
            }
        });
    },

    savePricingTemplate: function () {
        var submitUrl = $('#addEditPartnerForm').attr('action');
        var action_type = $('#h_action_type').val();

        $.ajaxSubmit({
            url: submitUrl,
            formId: "addEditPartnerForm",
            success: function (data) {
                if (data.status) {
                    if (action_type == 'add') {
                        $('#divAddPartner').dialog('close');
                    } else if (action_type == 'edit') {
                        $('#divEditPartner').dialog('close');
                    }
                    $.displayInfor(data.message, null, function () {
                        // Reload data grid
                        PricingTemplateList.searchPricingTemplates();
                    });

                } else {
                    $.displayError(data.message);
                }
            }
        });
    }
}
