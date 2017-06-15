<div class="header">
    <h2 style="font-size: 20px; margin-bottom: 10px"><?php admin_language_e('report_views_admin_manage_receipt_Header'); ?></h2>
</div>
<div class="ym-grid mailbox">
    <form id="partnerReceiptSearchForm" action="#" method="post">
        <div class="ym-gl">
            <div class="ym-grid input-item">
                <div class="ym-g70 ym-gl">
                    <input type="text" id="partnerReceiptSearchForm_enquiry" name="enquiry" style="width: 250px"
                           placeholder="partner name, description, location"
                           value="" class="input-txt" maxlength=255 />
                    <button style="margin-left: 30px" id="searchPartnerReceiptButton" class="admin-button"><?php admin_language_e('report_views_admin_manage_receipt_BtnSearch'); ?></button>
                    <button id="addPartnerReceiptButton" class="admin-button"><?php admin_language_e('report_views_admin_manage_receipt_BtnAdd'); ?></button>
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
<!-- Content for dialog -->
<div class="hide">
    <div id="addPartnerReceipt" title="<?php admin_language_e('report_views_admin_manage_receipt_TitAddReceipt'); ?>" class="input-form dialog-form mailbox">
    </div>
    <div id="editPartnerReceipt" title="<?php admin_language_e('report_views_admin_manage_receipt_TitEditReceipt'); ?>" class="input-form dialog-form mailbox">
    </div>
</div>
<!-- display none --> 
<!--#1296 add receipt scan/upload to receipts--> 
<div style="display: none">
    <a id="view_receipt_file" class="iframe"><?php admin_language_e('report_views_admin_manage_receipt_TitPreviewFile'); ?></a>
</div>
<!-- end display none --> 
<div class="clear-height"></div>

<style>
     .ui-jqgrid tr.jqgrow td {
        white-space: normal !important;
    }
</style>
<script type="text/javascript">
    $(document).ready(function () {
        
        var tableH = $.getTableHeight() + 13;
        
        $('button').button();

        // Call search method
        partnerReceiptReportings();

        /**
         * Process when user click to search button
         */
        $('#searchPartnerReceiptButton').live('click', function (e) {
            partnerReceiptReportings();
            e.preventDefault();
        });

        /**
         * Search data
         */
        function partnerReceiptReportings() {
            $("#dataGridResult").jqGrid('GridUnload');
            var url = '<?php echo base_url() ?>admin/report/manage_receipts_search';
            
            $("#dataGridResult").jqGrid({
                url: url,
                postData: $('#partnerReceiptSearchForm').serializeObject(),
                mtype: 'POST',
                datatype: "json",
                width: ($(window).width() - 40), //#1297 check all tables in the system to minimize wasted space
                height: tableH, //#1297 check all tables in the system to minimize wasted space 
                rowNum: '10',
                rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE); ?>],
                pager: "#dataGridPager",
                sortname: 'date_of_receipt',
                sortorder: 'desc',
                viewrecords: true,
                shrinkToFit: false,
                rownumbers: true,
                captions: '',
                colNames: ['',
                    '<?php admin_language_e('report_views_admin_manage_receipt_ColPartnerName'); ?>',
                    '<?php admin_language_e('report_views_admin_manage_receipt_ColDateReceipt'); ?>',
                    '<?php admin_language_e('report_views_admin_manage_receipt_ColNetValue'); ?>',
                    '<?php admin_language_e('report_views_admin_manage_receipt_ColDes'); ?>',
                    '<?php admin_language_e('report_views_admin_manage_receipt_ColPdf'); ?>', ''],
                colModel: [
                    {name: 'id', index: 'id', hidden: true},
                    {name: 'partner_name', index: 'partner_name', width: 350, sortable: false},
                    {name: 'date_of_receipt', index: 'date_of_receipt', width: 175, align: "center",},
                    {name: 'net_value', index: 'net_value', width: 300, sortable: false},
                    {name: 'desciption', index: 'desciption', width: 800, sortable: false},
                    {name: 'local_file_path', index: 'local_file_path', width: 90, sortable: false, align: "center", formatter: actionFormater02},
                    {name: 'receipt_id', index: 'receipt_id', width: 100, sortable: false, align: "center", formatter: actionFormater}
                ],
                // When double click to row
                ondblClickRow: function (row_id, iRow, iCol, e) {
                },
                loadComplete: function () {
                    $.autoFitScreen(($(window).width() - 40));  //#1297 check all tables in the system to minimize wasted space
                }
            });
        }

        function actionFormater(cellvalue, options, rowObject) {
            if (cellvalue !== -1) { //if not deleted yet
                return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-edit" data-id="' + cellvalue + '" title="<?php admin_language_e('report_views_admin_manage_receipt_TitEdit'); ?>"></span></span>'
                        + '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete" data-id="' + cellvalue + '" title="<?php admin_language_e('report_views_admin_manage_receipt_TitDelete'); ?>"></span></span>';
            } else {
                return '';
            }
        }

        /**
         * Process when user click to delete icon.
         */
        $('.managetables-icon-delete').live('click', function () {
            var id = $(this).attr('data-id');

            // Show confirm dialog
            $.confirm({
                message: '<?php admin_language_e('report_views_admin_manage_receipt_ConfirmMess'); ?>',
                yes: function () {
                    var submitUrl = '<?php echo base_url() ?>admin/report/delete_partner_receipt?id=' + id;
                    $.ajaxExec({
                        url: submitUrl,
                        success: function (data) {
                            if (data.status) {
                                // Reload data grid
                                partnerReceiptReportings();
                            } else {
                                $.displayError(data.message);
                            }
                        }
                    });
                }
            });
        });

        /**
         * Process when user click to add group button
         */
        $('#addPartnerReceiptButton').click(function () {
            // Clear control of all dialog form
            $('.dialog-form').html('');

            // Open new dialog
            $('#addPartnerReceipt').openDialog({
                autoOpen: false,
                height: 520, //#1296 add receipt scan/upload to receipts 
                width: 650, //#1296 add receipt scan/upload to receipts 
                modal: true,
                open: function () {
                    $(this).load("<?php echo base_url() ?>admin/report/add_partner_receipt", function () {
                    });
                },
                buttons: {
                    '<?php admin_language_e('report_views_admin_manage_receipt_BtnSaveDlg'); ?>': function () {
                        savePartnerReceipt();
                    },
                    '<?php admin_language_e('report_views_admin_manage_receipt_BtnCancelDlg'); ?>': function () {
                        $(this).dialog('close');
                    }
                }
            });
            $('#addPartnerReceipt').dialog('option', 'position', 'center');
            $('#addPartnerReceipt').dialog('open');
            return false;
        });

        /**
         * Process when user click to edit icon.
         */
        $('.managetables-icon-edit').live('click', function () {
            var id = $(this).attr('data-id');

            // Clear control of all dialog form
            $('.dialog-form').html('');

            // Open new dialog
            $('#editPartnerReceipt').openDialog({
                autoOpen: false,
                height: 520, //#1296 add receipt scan/upload to receipts 
                width: 650, //#1296 add receipt scan/upload to receipts 
                modal: true,
                open: function () {
                    $(this).load("<?php echo base_url() ?>admin/report/edit_partner_receipt?id=" + id, function () {
                    });
                },
                buttons: {
                    '<?php admin_language_e('report_views_admin_manage_receipt_BtnSaveDlg'); ?>': function () {
                        savePartnerReceipt();
                    },
                    '<?php admin_language_e('report_views_admin_manage_receipt_BtnCancelDlg'); ?>': function () {
                        $(this).dialog('close');
                    }
                }
            });
            $('#editPartnerReceipt').dialog('option', 'position', 'center');
            $('#editPartnerReceipt').dialog('open');
        });

        /**
         * Save Customer
         */
        function savePartnerReceipt() {
            var submitUrl = $('#addEditPartnerReceiptForm').attr('action');
            var action_type = $('#h_action_type').val();
            $.ajaxSubmit({
                url: submitUrl,
                formId: 'addEditPartnerReceiptForm',
                success: function (data) {
                    if (data.status) {
                        if (action_type == 'add') {
                            $('#addPartnerReceipt').dialog('close');
                        } else if (action_type == 'edit') {
                            $('#editPartnerReceipt').dialog('close');
                        }
                        $.displayInfor(data.message, null, function () {
                            // Reload data grid
                            partnerReceiptReportings();
                        });

                    } else {
                        $.displayError(data.message);
                    }
                }
            });
        }
    });

    //#1296 add receipt scan/upload to receipts
    $('#view_receipt_file').fancybox({
        'onComplete' : function() {
            $('#fancybox-frame').load(function() { // wait for frame to load and then gets it's height
                var v_height = $('#fancybox-content').height();
                var v_width = $('#fancybox-content').width();
                $(this).contents().find('body').find('img').height(v_height);
                $(this).contents().find('body').find('img').width(v_width);
            });
        }
    });
   
    function actionFormater02(cellvalue, options, rowObject) {
        if (cellvalue !== -1) {
            return '<a class="view-pdf pdf" data-id="' + cellvalue + '" href=""></a>';
        } else {
            return '';
        }
    }

    /**
     * #1296 add receipt scan/upload to receipts
     * view all pdf file.
     */
    $(".view-pdf").live("click", function () {

        // Url's file view  
        var url = "<?php echo base_url() ?>admin/report/";
        url += "view_resource?";
        url += "id=" + $(this).data('id');

        // click open link file view
        $('#view_receipt_file').attr('href', url);
        $('#view_receipt_file').click();

        // return 
        return false;
    });

</script>
