<div class="ym-grid mailbox" style="width: 1300px;margin-top: -4px;">

    <table id="Table_01" width="1293" height="218" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <th width="1293" height="29" colspan="5" align="left">
                <h2 style="font-size: 18px; margin-bottom: 8px;margin-top: 2px"><?php admin_language_e('scan_view_completed_checkitem_CheckItemPage'); ?></h2></th>
        </tr>

        <tr>
            <td valign="top" style="border:solid 1px #bebebe;">
                <form id="customerSearchForm" action="<?php echo base_url() ?>scans/completed/check_item" method="post">        
                    <table id="Table_01" width="479" height="189" border="0" cellpadding="0" cellspacing="0">    
                        <tr style="background:#dbe9f4;" class="title">
                            <td width="165" height="40" style="padding-left:6px;"><?php admin_language_e('scan_view_completed_checkitem_EnterItemID'); ?></td>
                            <td width="338"  valign="middle">
                                <input type="text" id="item_id" name="item_id" class="input-txt" value="" maxlength=255 />&nbsp;
                                <input type="button" id="searchButton" class="" value="<?php admin_language_e('scan_view_completed_checkitem_Search'); ?>" />

                            </td>
                            <td width="4">&nbsp;</td>
                        </tr>
                </form>
                <form id="item_update_extra" action="javascript:void(0)" method="post">
                    <tr>
                        <td height="38" style="padding-left:6px;"><?php admin_language_e('scan_view_completed_checkitem_From'); ?></td>
                        <td><input type="text" id="item_from" name="item_from" style=""
                                   value="" class="input-txt" maxlength=255 /></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td height="38" style="padding-left:6px;"><?php admin_language_e('scan_view_completed_checkitem_ItemLWHcmWeightG'); ?></td>
                        <td><input class="input-txt dimension" type="text" id="item_length" name="item_length" style="" value="" maxlength=255 /> 
                            <input class="input-txt dimension" type="text" id="item_width" name="item_width" style="" value=""  maxlength=255 />

                            <input class="input-txt dimension" type="text" id="item_height" name="item_height"
                                   value=""  maxlength=255 /> -  <input class="input-txt dimension" type="text" id="item_weight" name="item_weight" style="" value="" maxlength=255 />
                        </td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td height="38" style="padding-left:6px;"><?php admin_language_e('scan_view_completed_checkitem_TrackingNumber'); ?></td>
                        <td><input type="text" id="tracking_number" name="tracking_number" style=""
                                   value="" class="input-txt" maxlength=255 /></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding-left:6px;"><?php admin_language_e('scan_view_completed_checkitem_ShipmentServices'); ?></td>
                        <td id="list_shipping_service_available">
                            <select  name="shipping_services" id="shipping_services" class="input-width tracking_disable">
                                <option value="0">&nbsp;</option>
                            </select>
                        </td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="3" height="3">
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="2">
                            <a style="display: none;" class="achange" href="#" id="change_envelope"><?php admin_language_e('scan_view_completed_checkitem_ExchangEnvelopePDF'); ?></a>
                            <a style="display: none;" class="achange" href="#" id="change_item"><?php admin_language_e('scan_view_completed_checkitem_ExchangeItemPDF'); ?></a>
                            <input type="hidden" value="" name="item_update_id" id="item_update_id" />
                            <input type="button" id="item_update" class="admin-button" value="<?php admin_language_e('scan_view_completed_checkitem_Save'); ?>" />
                        </td>
                    </tr>

                    <tr>  <td colspan="1">&nbsp;</td>
                        <td colspan="2"><span id='msg' style="margin-left: 0px;font-weight: bold;"></td>
                    </tr>
    </table>
</form>
</td>
<td width="17">&nbsp;</td>
<td width="386" height="189" valign="top" style="border:solid 1px #bebebe;vertical-align: top;">
    <table id="Table_02" width="386" height="189" border="0" cellpadding="0" cellspacing="0">
        <tr style="background:#dbe9f4;">
            <td style="padding-left:10px;border-bottom:solid 1px #bebebe;" width="113" height="34"><?php admin_language_e('scan_view_completed_checkitem_Status'); ?></td>
            <td style="border-bottom:solid 1px #bebebe;" colspan="2">
                <div id="envelope_class" class="envelope status_item">&nbsp;</div>
                <div id="scan_class" class="scan status_item">&nbsp;</div>
                <div id="cloud_class" class="cloud status_item">&nbsp;</div>
                <div id="send_class" class="send status_item">&nbsp;</div>
                <div id="collect_class" class="collect status_item">&nbsp;</div>
                <div id="trash_class" class="trash status_item">&nbsp;</div>
            </td>
        </tr>
        <tr>
            <td style="padding-left:10px;border-bottom:solid 1px #bebebe;" width="113" height="30"><?php admin_language_e('scan_view_completed_checkitem_Storage'); ?></td>
            <td style="border-bottom:solid 1px #bebebe;" width="233" height="30">
                <label class="ym-gl" id="storage_label"></label></td>
            <td style="border-bottom:solid 1px #bebebe;" width="40" height="30">
                <div id="storage" class="">&nbsp;</div>
            </td>
        </tr>
        <tr>
            <td style="padding-left:10px;" width="113" height="30">&nbsp;<?php admin_language_e('scan_view_completed_checkitem_AccountStatus'); ?></td>
            <td colspan="2"><label class="ym-gl" id="account_status"></label></td>
        </tr>
        <tr>
            <td style="padding-left:10px;" width="113" height="30"> <?php admin_language_e('scan_view_completed_checkitem_VerificationStatus'); ?></td>
            <td colspan="2" height="25"><label class="ym-gl" id="verified_status"></label></td>
        </tr>
        <tr>
            <td style="padding-left:10px;" width="113" height="30"> &nbsp;<?php admin_language_e('scan_view_completed_checkitem_LastActivity'); ?></td>
            <td colspan="2"><label class="ym-gl" id="last_activity"></label></td>
        </tr>
    </table></td>
<td width="17">&nbsp;</td>
<td width="394" height="189" style="border: solid 1px #ccc;vertical-align: top;" >
    <table width="394" height="189" border="0" align="center" cellpadding="0" cellspacing="0" id="Table_03">
        <tr align="center">
            <td width="191" height="42" align="right" style="text-align: right;">
                <input type="button" id="scanEnvelopeButton"
                       class="actionButton input-btn-disable c" value="<?php admin_language_e('scan_view_completed_checkitem_ScanEnvelope'); ?>"
                       style="" />
            </td>
            <td width="203" height="44">
                <input type="button" id="cancelEnvelopeScanButton" class="actionButton  c input-btn-disable"
                       value="<?php admin_language_e('scan_view_completed_checkitem_CancelScanEnvelope'); ?>"  /> 
            </td>
        </tr>
        <tr align="center" valign="top">
            <td width="191" height="42" align="right" style="text-align: right;">
                <input type="button"  id="scanItemButton" class="actionButton input-btn-disable c"
                       value="<?php admin_language_e('scan_view_completed_checkitem_ScanItem'); ?>"  />
            </td>
            <td width="203" height="36">
                <input
                    type="button" id="cancelItemScanButton"  class="actionButton  c input-btn-disable" value="<?php admin_language_e('scan_view_completed_checkitem_CancelScanItem'); ?>"
                    style="" />
            </td>
        </tr>
        <tr align="center" valign="top">
            <td width="191" height="42" align="right" style="text-align: right;">
                <input type="button" id="shippingEnvelopeButton" class="actionButton input-btn-disable c" value="<?php admin_language_e('scan_view_completed_checkitem_PrepareShipping'); ?>"  /> 
            </td>
            <td width="203" height="36">
                <input type="button"  id="cancelDirectShippingButton" class="actionButton c input-btn-disable" value="<?php admin_language_e('scan_view_completed_checkitem_CancelShipping'); ?>"
                       style="" />
            </td>
        </tr>
        <tr align="center" valign="top">
            <td width="191" height="42" align="right" style="text-align: right;">
                <input type="button" id="completeTrash" class="actionButton input-btn-disable c" value="<?php admin_language_e('scan_view_completed_checkitem_CompleteTrash'); ?>"  />
            </td>
            <td width="203" height="36"><input type="button" id="disablePrepaymentButton" class="actionButton input-btn-disable c" value="<?php admin_language_e('scan_view_completed_checkitem_DisablePrepayment'); ?>"  />
            </td>
        </tr>
        <tr align="center" valign="top">
            <td width="191" height="42" align="right" style="text-align: right;">
                <input type="button" id="markCompletedButton" class="actionButton input-btn-disable c"
                       value="<?php admin_language_e('scan_view_completed_checkitem_MarkCompleted'); ?>"  />
            </td>
            <td width="203" height="42">
            </td>
        </tr>
    </table></td>
</tr>

</table>
</div>

<div class="ym-clearfix"></div>
<br />
<div id="divErrorMessage" class="required" style="font-size: 18pt;margin-top: 0px;"></div>
<div class="ym-clearfix"></div>

<div id="searchTableResult" style="margin: 0px 10px 10px 0;">
    <table id="dataGridResult"></table>
    <div id="dataGridPager"></div>
</div>
<div class="clear-height"></div>
<div class="hide">
    <input type="hidden" name="envelope_id" id="envelope_id" value="" />
    <input type="hidden" name="envelope_ID" id="envelope_ID" value="" />
    <input type="hidden" name="customer_id" id="customer_id" value="" />
    <input type="hidden" name="token_key" id="token_key" value="" />
    <input type="hidden" name="scan_type" id="scan_type" value="" />
    <input type="hidden" name="item_scan_status" id="item_scan_status" value="" />
    <input type="hidden" name="envelope_scan_status" id="envelope_scan_status" value="" />
    <input type="hidden" id="scan_type_id" value="1" />
    <input type="hidden" id="current_scan_type" value="" />
    <input type="hidden" id="package_id" value="" />
    <input type="hidden" id="postbox_id" value="" />
    <input type="hidden" id="shipping_type" value="" />
    <input type="hidden" id="envelope_ID" value="" />
    <input type="hidden" id="to_ID" value="" />
    <input type="hidden" id="nextToDoForm_postbox_id" value="" />
    <input type="hidden" id="nextToDoForm_package_id" value="" />
    <input type="hidden" id="scanItemTemporaryFlag_id" value="" />

    <!--1: upload file; 2: scan file -->
    <input type="hidden" id="documentType" value="" />

    <!--1: exchange envelope; 2: exchange scan file -->
    <input type="hidden" id="changeItemTypeId" value="" /> <a
        id="dynaScanLink" href="<?php echo base_url() ?>scans/todo/scan"
        title="<?php admin_language_e('scan_view_completed_checkitem_ScanEnvelope'); ?>"><?php admin_language_e('scan_view_completed_checkitem_ScanLink'); ?></a>
    <div id="scanEnvelopeWindow" title="<?php admin_language_e('scan_view_completed_checkitem_ScanEnvelope'); ?>Scan Envelope"
         class="input-form dialog-form"></div>
    <div id="shippingEnvelopeWindow" title="<?php admin_language_e('scan_view_completed_checkitem_AddressLabelPrintInterface'); ?>"
         class="input-form dialog-form"></div>
    <input type="file" id="imagepath" name="imagepath"  style="display: none; visibility: hidden;" />
    <a  id="linkViewUploadFile" href="#" class="iframe" style="display: none"><?php admin_language_e('scan_view_completed_checkitem_View'); ?></a>

    <div id="viewDetailCustomer" class="input-form dialog-form"></div>
    <div id="createDirectCharge" class="input-form dialog-form"></div>
    <div id="recordExternalPayment" class="input-form dialog-form"></div>
    <div id="recordRefundPayment" class="input-form dialog-form"></div>
    <div id="createDirectChargeWithoutInvoice" class="input-form dialog-form"></div>
    <div id="createDirectInvoice" class="input-form dialog-form"></div>
    <div id="envelopeCommentWindow" title="<?php admin_language_e('scan_view_completed_checkitem_Comment'); ?>" class="input-form dialog-form"></div>
</div>

<div class="hide" style="display: none;">
    <a id="display_pdf_invoice" class="iframe" href="#"><?php admin_language_e('scan_view_completed_checkitem_DisplayProformaInvoice'); ?></a>
</div>

<form id="hiddenAccessCustomerSiteForm" target="blank" action="<?php echo base_url() ?>admin/customers/view_site" method="post">
    <input type="hidden" id="hiddenAccessCustomerSiteForm_customer_id" name="customer_id" value="" />
</form>

<div id="loading" class="loadingIcon"
     style="display: none; height: 50px; width: 50px; margin-top: 45px; margin-left: 5px;"></div>

<?php
Asset::css('styles_new.css');
Asset::css("styles_check_item_page.css");
?>
<script src="<?php echo APContext::getAssetPath() ?>system/virtualpost/modules/scans/js/CheckItem.js"></script>
<script>
    $(document).ready(function () {

        var _baseUrl = '<?php echo base_url(); ?>',
                _rowNum = '<?php echo APContext::getAdminPagingSetting(); ?>',
                _rowList = [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE); ?>];
        /** START SOURCE TO VIEW CUSTOMER DETAIL AND DIRECT CHARGE */
        <?php include 'system/virtualpost/modules/customers/js/js_customer_info.php'; ?>
        /** END SOURCE TO VIEW CUSTOMER DETAIL AND DIRECT CHARGE */

        CheckItem.init(_baseUrl, _rowNum, _rowList);
        /*
         * #1363 BUG: BUG 48805 - we cannot get the customs declaration by pressing 'yes' on the check item page, 
         * it takes me to the customer's Postbox
         */
        $('#display_pdf_invoice').fancybox({
            width: 900,
            height: 700,
            'onClosed': function () {
                $("#fancybox-inner").empty();
            }
        });

        /**
         * View detail cusoms
         */
        $('.view_detail_customs').live('click', function () {
            var invoices_href = this.href;
            $('#display_pdf_invoice').attr('href', invoices_href);
            $('#display_pdf_invoice').click();
            return false;
        });
    });
</script>

