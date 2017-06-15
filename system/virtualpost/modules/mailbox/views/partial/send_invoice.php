<?php 
$customer_check_user_id = APContext::getCustomerCodeLoggedInMailbox(array($envelope->id));
$customer_user_check = APContext::getCustomerByID($customer_check_user_id);
    
$accounting_settings = EnvelopeUtils::get_accounting_interface_by_postbox($envelope->postbox_id);
$accounting_email = $accounting_settings['email']; 
$interface_id = $accounting_settings['interface_id']; 
$mark_invoice_class = empty($accounting_email) ? 'plus-gray' : (($envelope->invoice_flag == APConstants::ON_FLAG) ? 'check-blue' : 'check-gray') ;
?>
<div class="<?php echo $mark_invoice_class; ?> wrap icon_popup_container">
    <?php if ($enable_action) { ?>
        
        <?php if (empty($accounting_email)) { ?>
                <div class="scan-popup scan-popup-new">
                    <h2><?php language_e('mailbox_view_part_send_invoice_DoesNotSetupConnectionYet') ?></h2>
                    <div class="ym-clearfix"></div>	
                    <div class="popup-button">
                        <a href="<?php echo base_url() ?>cloud" class="yes" style="font-weight: normal;"><?php language_e('mailbox_view_part_send_invoice_SetUpTheConnection') ?></a>
                    </div>
                </div>            
        <?php } else { ?>
            <?php if ($envelope->direct_shipping_flag == APConstants::ON_FLAG || $envelope->collect_shipping_flag == APConstants::ON_FLAG) { ?>
                    <div class="scan-popup scan-popup-new">
                        <h2><?php language_e('mailbox_view_part_send_invoice_CompleteForwarding') ?></h2>
                        <div class="ym-clearfix"></div>	
                    </div>  
                <?php } elseif ($envelope->direct_shipping_flag == APConstants::OFF_FLAG || ($envelope->collect_shipping_flag == APConstants::OFF_FLAG && !empty($envelope->package_id))){ ?>
                    <?php if ($envelope->invoice_flag == APConstants::ON_FLAG) { ?>  
                        <?php if ($envelope->item_scan_flag == APConstants::ON_FLAG){ ?>  
                            <div class="scan-popup scan-popup-new">
                                <h2><?php language_e('mailbox_view_part_send_invoice_CompleteScanMarkInvoice', array('interface_id' => '<i>'.$interface_id.'</i>')) ?></h2>
                                <div class="ym-clearfix"></div>	
                            </div>  
                        <?php } else { ?>
                                <div class="scan-popup scan-popup-new">
                                    <h2><?php language_e('mailbox_view_part_send_invoice_NotCompleteScanMarkInvoice', array('interface_id' => '<i>'.$interface_id.'</i>')) ?></h2>
                                    <div class="ym-clearfix"></div>	
                                </div> 
                        <?php } ?>
                    <?php } else { ?>
                        <div class="scan-popup scan-popup-new">
                            <h2><?php language_e('mailbox_view_part_send_invoice_RequestForwardingNotMarkInvoice') ?></h2>
                            <div class="ym-clearfix"></div>	
                        </div> 
                    <?php } ?>
                <?php } else { ?>
                    <?php if ($envelope->invoice_flag == APConstants::ON_FLAG) { ?>  
                        <?php if ($envelope->item_scan_flag == APConstants::ON_FLAG){ ?>  
                            <div class="scan-popup scan-popup-new">
                                <h2><?php language_e('mailbox_view_part_send_invoice_CompleteScanMarkInvoice', array('interface_id' => '<i>'.$interface_id.'</i>')) ?></h2>
                                <div class="ym-clearfix"></div>	
                            </div>  
                        <?php } else { ?>
                                <div class="scan-popup scan-popup-new">
                                    <h2><?php language_e('mailbox_view_part_send_invoice_NotCompleteScanMarkInvoice', array('interface_id' => '<i>'.$interface_id.'</i>')) ?></h2>
                                    <div class="ym-clearfix"></div>	
                                </div> 
                        <?php } ?>
                    <?php } else { ?>
                        <?php if ($envelope->item_scan_flag == APConstants::ON_FLAG){ ?>  
                            <div class="scan-popup scan-popup-new">
                                <h2><?php language_e('mailbox_view_part_send_invoice_CompleteScanNotMarkInvoice', array('interface_id' => '<i>'.$interface_id.'</i>')) ?></h2>
                                <div class="ym-clearfix"></div>	
                                <div class="popup-button">
                                    <a id="send-invoice-envelope-<?php echo $envelope->id ?>" data-id="<?php echo $envelope->id ?>" data-email="<?php echo $accounting_email ?>" 
                                           class="yes send-invoice-envelope" class="yes" style="font-weight: normal;">Confirm</a>
                                </div>
                            </div> 
                        <?php } elseif ($envelope->item_scan_flag == APConstants::OFF_FLAG) { ?>
                            <div class="scan-popup scan-popup-new">
                                <h2><?php language_e('mailbox_view_part_send_invoice_MarkInvoiceForItemHasScanRequest', array('interface_id' => '<i>'.$interface_id.'</i>')) ?></h2>
                                <div class="ym-clearfix"></div>	
                                <div class="popup-button">
                                        <a id="mark-invoice-envelope-<?php echo $envelope->id ?>" data-id="<?php echo $envelope->id ?>"
                                           class="yes mark-invoice-envelope" style="font-weight: normal;">Confirm</a>
                                </div>
                            </div> 
                        <?php } else {?>
                            <div class="scan-popup scan-popup-new">
                                <h2><?php language_e('mailbox_view_part_send_invoice_MarkInvoiceForNewItem', array('interface_id' => '<i>'.$interface_id.'</i>')) ?></h2>
                                <div class="ym-clearfix"></div>	
                                <div class="popup-button">
                                        <a id="mark-invoice-envelope-<?php echo $envelope->id ?>" data-id="<?php echo $envelope->id ?>"
                                           class="yes mark-invoice-envelope" style="font-weight: normal;">Confirm</a>
                                </div>
                            </div> 
                        <?php } ?>
                    <?php } ?>
            <?php } ?>
        <?php } ?>
    <?php } ?>
</div>