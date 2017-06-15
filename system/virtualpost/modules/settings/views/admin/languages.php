<style>
.group-input {
    float: left;
    margin-top: 10px;
    margin-left: 5px;
}
button.loadding:hover {
    cursor: not-allowed;
}
</style>
<div class="header">
    <h2 style="font-size:  20px; margin-bottom: 10px"><?php admin_language_e('settings_view_admin_languages_Languages'); ?></h2>
</div>
<div class = "mailbox">
    <div class="ym-grid">
        <div class="ym-g20 ym-gl" style="width: 100px; text-align: left;">
            <label style="text-align: left;"><?php admin_language_e('settings_view_admin_languages_Languages'); ?></label>
        </div>
        <div class="ym-g80 ym-gl">
            <button id="addLanguageButton" class="admin-button"><?php admin_language_e('settings_view_admin_languages_AddNewLanguages'); ?></button>
            <button id="editLanguageButton" class="admin-button"><?php admin_language_e('settings_view_admin_languages_ChangeStatus'); ?></button>
            <button id="exportToExcel" class="admin-button" data-url="<?php echo base_url() ?>admin/settings/languagesToExcell"><?php admin_language_e('settings_view_admin_languages_ExportExcel'); ?></button>
            <button id="importToDb" class="admin-button"><?php admin_language_e('settings_view_admin_languages_Import'); ?></button>
        </div>
    </div>
    <div class="ym-grid"  style="margin-top: 10px;">
        <div class="ym-g80 ym-gl">
            <form id="languageSearchForm" action="" method="post">
                <div class="ym-grid">
                   <div class="ym-g20 ym-gl" style="width: 100px; text-align: left;">
                       <label style="text-align: left;"><?php admin_language_e('settings_view_admin_languages_Search'); ?></label>
                   </div>
                   <div class="ym-g80 ym-gl">
                       <input type="text" id="searchLanguageForm_text" name="textSearch" style="width: 250px" value="" class="input-txt" maxlength=255/>
                       <button id="searchLanguageButton" class="admin-button"><?php admin_language_e('settings_view_admin_languages_Search'); ?></button>
                   </div>
               </div>
            </form>
        </div>
    </div>
</div>
<div id="searchTableResult" style="margin-top: 10px;">
    <table id="dataGridResult"></table>
    <div id="dataGridPager"></div>
</div>
<div class="clear-height"></div>

<!-- Content for dialog -->
<div class="hide">
    <div id="addLanguage" title="<?php admin_language_e('settings_view_admin_languages_AddNewLanguagePopup'); ?>" class="input-form dialog-form"></div>
    <div id="editLanguage" title="<?php admin_language_e('settings_view_admin_languages_ChangeLanguageStatusPopup'); ?>" class="input-form dialog-form"></div>
    <div id="importExcel" title="<?php admin_language_e('settings_view_admin_languages_ImportLanguagesPopup'); ?>" class="input-form dialog-form"></div>
</div>

<script>
$(document).ready(function() {
    $('button').button();
    loadLanguageGrid(); // Call search method

    /**
     * Process when user click to search button
     */
    $('#searchLanguageButton').click(function(e) {
        e.preventDefault();
        loadLanguageGrid();
    });

    /**
     * Process when user click to add group button
     */
    $('#addLanguageButton').click(function() {

        // Clear control of all dialog form
        $('.dialog-form').html('');

        // Open new dialog
        $('#addLanguage').openDialog({
            autoOpen: false,
            height: 230,
            width: 350,
            modal: true,
            open: function() {
                $(this).load("<?php echo base_url() ?>" + "admin/settings/addLanguage", function() {
                    //$('#country_name').focus();
                });
            },
            buttons: {
                '<?php admin_language_e('settings_view_admin_languages_SaveBtn'); ?>': function() {
                   saveLanguage();
                   loadLanguageGrid();
                },
                '<?php admin_language_e('settings_view_admin_languages_CancelBtn'); ?>': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#addLanguage').dialog('option', 'position', 'center');
        $('#addLanguage').dialog('open');
    });

    function saveLanguage() {
        var submitUrl = $('#languageForm').attr('action');
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'languageForm',
            success: function(data) {
                if (data.status) {
                    $('#addLanguage').dialog('close');
                    $.displayInfor(data.message, null,  function() {
                        // Reload data grid
                        //loadLanguageGrid();
                    });
                } else {
                    $.displayError(data.message);
                }
            }
        });
    };

    function loadGridData(settings) {
        $("#dataGridResult").jqGrid('GridUnload');

        var url = '<?php echo base_url() ?>' + 'admin/settings/languages';
        var lastSel;
        var tableH = $.getTableHeight()+ 45;//#1297 check all tables in the system to minimize wasted space,

        $("#dataGridResult").jqGrid({
            url: url,
            editurl: '<?php echo base_url() ?>' + 'admin/settings/saveLanguage',
            postData: $('#languageSearchForm').serializeObject(),
            mtype: 'POST',
            datatype: "json",
            height:tableH, //#1297 check all tables in the system to minimize wasted space,
            width: ($( window ).width()- 40), //#1297 check all tables in the system to minimize wasted space
            rowNum: '<?php echo APContext::getAdminPagingSetting();?>',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            pager: "#dataGridPager",
            sortname: 'id',
            viewrecords: true,
            shrinkToFit: true,
            //multiselect: true,
            //multiselectWidth: 40,
            captions: '',
            colNames: settings.colNames,
            colModel: settings.colModel,
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
                //var data_row = $('#dataGridResult').jqGrid("getRowData", row_id);
                //console.log(data_row);
//                if(row_id && row_id!==lastSel){
//                   // jQuery('#dataGridResult').restoreRow(lastSel);
//                    $("#dataGridResult").jqGrid('saveRow',lastSel);
//                    lastSel=row_id;
//                }
                $('#dataGridResult').jqGrid('editRow',row_id, true);
            },
            onSelectRow: function(row_id){
                if(row_id && row_id!==lastSel){
                   $("#dataGridResult").jqGrid('saveRow',lastSel);
                    lastSel=row_id;
                }
             },
            loadComplete: function() {
                $.autoFitScreen($( window ).width()- 40); //#1297 check all tables in the system to minimize wasted space
            }
        });
    };

    function loadLanguageGrid (){
        var submitUrl = '<?php echo base_url() ?>' + 'admin/settings/loadLanguageGridSetting';
        $.ajax({
            url:submitUrl,
            dataType: 'json',
            //data: {'postbox_id' : $(this).val()},
            success: function (response) {
                actionModels = {
                    name:'action',index:'id', width:'50%', sortable: false, align:"center", formatter: actionFormater
                };
                response.colNames.push('Action');
                response.colModel.push(actionModels);
                loadGridData(response);
            }
        });
    };

    function actionFormater(cellvalue, options, rowObject) {
        return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-edit managetables-icon-edit-milestone" data-id="' + options.rowId
                + '" title="<?php admin_language_e('settings_view_admin_languages_Edit'); ?>" id="language-btn-edit" data-k="k"></span></span>'
        + '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete" data-id="' + options.rowId
        + '" title="<?php admin_language_e('settings_view_admin_languages_Delete'); ?>" id="language-btn-del"></span></span>';
    }

    $('#searchTableResult').on('click', '#language-btn-edit',function(e) {
        row_id = $(e.target).data('id');
        $('#dataGridResult').jqGrid('editRow',row_id, true);
    });

    $('#searchTableResult').on('click', '#language-btn-del',function(e) {
        var language_id = $(e.target).data('id');
        $.confirm({
                message: "<?php admin_language_e('settings_view_admin_languages_DoYouWantDeleteLanguagesKey'); ?>",
                yes: function () {
                    deleteLanguageKey(language_id);
                }
            });
    });

    $('#editLanguageButton').click(function() {

        // Clear control of all dialog form
        $('.dialog-form').html('');

        // Open new dialog
        $('#editLanguage').openDialog({
            autoOpen: false,
            height: 390,
            width: 520,
            modal: true,
            open: function() {
                $(this).load('<?php echo base_url() ?>' + "admin/settings/editLanguage", function() {
                });
            },
            buttons: {
                'Save': function() {
                    //Save row
                    var statusGrid = $("#dataGridStatus"),
                        ids = statusGrid.jqGrid('getDataIDs'),
                        i, l = ids.length;
                    for (i = 0; i < l; i++) {
                        statusGrid.jqGrid('saveRow', ids[i], false, 'clientArray');
                    }
                    //Then get row data
                    var gridData = $("#dataGridStatus").jqGrid('getRowData');
                    saveLanguageStatus(gridData);
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#editLanguage').dialog('option', 'position', 'center');
        $('#editLanguage').dialog('open');
    });

    $('#exportToExcel').on('click', function() {
        var exportUrl = $(this).data('url');
        var search = $('#searchLanguageForm_text').val();
        var params = "search=" + search;
        var xhr = new XMLHttpRequest();
        // Declare post method
        xhr.open('POST', exportUrl, true);
        xhr.responseType = 'arraybuffer';
        xhr.onload = function () {
            if (this.status === 200) {
                var filename = "";
                var disposition = xhr.getResponseHeader('Content-Disposition');
                if (disposition && disposition.indexOf('attachment') !== -1) {
                    var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                    var matches = filenameRegex.exec(disposition);
                    if (matches != null && matches[1]) filename = matches[1].replace(/['"]/g, '');
                }
                var type = xhr.getResponseHeader('Content-Type');

                var blob = new Blob([this.response], { type: type });
                if (typeof window.navigator.msSaveBlob !== 'undefined') {
                    // IE workaround for "HTML7007: One or more blob URLs were revoked by closing the blob for which they were created. These URLs will no longer resolve as the data backing the URL has been freed."
                    window.navigator.msSaveBlob(blob, filename);
                } else {
                    var URL = window.URL || window.webkitURL;
                    var downloadUrl = URL.createObjectURL(blob);

                    if (filename) {
                        // use HTML5 a[download] attribute to specify filename
                        var a = document.createElement("a");
                        // safari doesn't support this yet
                        if (typeof a.download === 'undefined') {
                            window.location = downloadUrl;
                        } else {
                            a.href = downloadUrl;
                            a.download = filename;
                            document.body.appendChild(a);
                            a.click();
                        }
                    } else {
                        window.location = downloadUrl;
                    }

                    setTimeout(function () { URL.revokeObjectURL(downloadUrl); }, 100); // cleanup
                }
            }
        };
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.send(params);
    });
    $("#importExcel").on('click', '#importExcelBtn', function(event){
        $("#languages-import-file").click();
        if (event.stopPropagation) {
          event.stopPropagation();   // W3C model
        } else {
          event.cancelBubble = true; // IE model
        }
        return false;
    });
    $("#importExcel").on('change', '#languages-import-file', function(click) {
        myfile= $( this ).val();
        var ext = myfile.split('.').pop();
        if((ext.toUpperCase() != "XLS")
                && (ext.toUpperCase() != "CSV")
                && (ext.toUpperCase() != "XLSX")){
           $.displayError("<?php language_e('settings_view_admin_languages_PleaseSelectExcelFile'); ?>");
            return;
        }
        $("#languages-import-change").val(1);
        $('#languages-import-txt').val($("#languages-import-file").val().split('\\').pop());
        // Back up languages data
        var backup_url = "<?php echo base_url() ?>" + "admin/settings/languagesToExcell/1";
        $.ajax({
                    url: backup_url, // point to server-side PHP script
                    dataType: 'text',  // what to expect back from the PHP script, if anything
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: {
                        backup: 1
                    },
                    type: 'post',
                    success: function(php_script_response){
                        // To Do
                    }
         });
    });
    $('#importToDb').on('click', function() {
        // Open Modal Form upload excel file
        // Clear control of all dialog form
        $('.dialog-form').html('');

        // Open new dialog
        $('#importExcel').openDialog({
            autoOpen: false,
            height: 230,
            width: 350,
            modal: true,
            open: function() {
                $(this).load("<?php echo base_url() ?>" + "admin/settings/importExcel", function() {
                    //$('#country_name').focus();
                });
            },
            buttons: {
                'Import': function() {
                    if ($('#languages-import-txt').val() == '') {
                        $.displayError("<?php admin_language_e('settings_view_admin_languages_PleaseSelectFileForUpload') ;?>");
                        return;
                    }
                    $('#importToDb').attr('disabled','disabled');
                    $('#importToDb').addClass('loadding');
                    var submitUrl = $('#ImportForm').attr('action');
                    var file_data = $('#languages-import-file').prop('files')[0];
                    var form_data = new FormData();
                    form_data.append('file', file_data);
                    $('#importExcel').dialog('close');
                    $.displayInfor("<?php admin_language_e('settings_view_admin_languages_ImportLanguagesMsg');?>", null,  function() {
                        $.ajax({
                            url: submitUrl, // point to server-side PHP script
                            dataType: 'text',  // what to expect back from the PHP script, if anything
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: form_data,
                            type: 'post',
                            success: function(data){
                                data = JSON.parse(data);
                                if (data.status) {
                                    $.displayInfor(data.message, null,  function() {
                                        loadLanguageGrid();
                                        $('#importToDb').removeAttr('disabled');
                                        $('#importToDb').removeClass('loadding');
                                    });
                                    console.log(data.message);
                                } else {
                                    $.displayError(data.message);
                                }
                            }
                         });
                    });
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#importExcel').dialog('option', 'position', 'center');
        $('#importExcel').dialog('open');
    });
    function saveLanguageStatus(data) {
        var submitUrl = '<?php echo base_url() ?>' + 'admin/settings/saveLanguageStatus';
        $.ajax({
            url:submitUrl,
            dataType: 'json',
            data: {data: JSON.stringify(data)},
            success: function (response) {
                 if (response.status) {
                    $('#editLanguage').dialog('close');
                    loadLanguageGrid();

                }
            }
        });
    };

    // Delete language key
    function deleteLanguageKey(language_id)
    {
        var submitUrl = '<?php echo base_url()?>admin/settings/delete_language';
        $.ajaxExec({
            url: submitUrl,
            dataType: 'json',  // what to expect back from the PHP script, if anything
            cache: false,
            contentType: false,
            processData: false,
            type: 'post',
            data: {
                language_id: language_id
            },
            success: function (data) {
                if (data.status) {
                    var message = "<?php admin_language_e('settings_view_admin_languages_DeleteSucessfull'); ?>";
                    $.infor({
                        message: message,
                        ok: function(){
                            // Reload data grid
                            loadLanguageGrid();
                        }
                    });
                } else {
                    $.displayError(data.message);
                }
            }
        });
    }
  });



</script>