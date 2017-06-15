<nav class="topnav" id="navAdminSiteId">
    <ul class="menusm">
        <?php if (APContext::isSupperAdminUser() || APContext::isAdminUser() || APContext::isAdminParner() || APContext::isAdminLocation() || APContext::isWorkerAdmin()) { ?>
            <li>
                <a href="<?php echo base_url()?>scans/incoming" <?php if ($controller === 'incoming') { ?> class="act" <?php }?>>
                    <span class="has_sub_menu"><?php admin_language_e('them_admi_view_part_navi_Inco')?></span></a>
            </li>
            <li>
                <a href="<?php echo base_url()?>scans/todo" <?php if ($controller === 'todo' && $module == 'scans') { ?> class="act" <?php }?>>
                    <?php admin_language_e('them_admi_view_part_navi_ToDo')?></a>
            </li>
            <li><a href="javascript:void(0)"><span class="has_sub_menu"><?php admin_language_e('them_admi_view_part_navi_UserAdmi')?></span></a>
                <ul>
                    <li><a href="<?php echo base_url()?>admin/customers"  
                            <?php if ($controller === 'admin' && $module == 'customers' && $method =='index') { ?> class="act" <?php }?>>
                                <?php admin_language_e('them_admi_view_part_navi_CustList')?></a>
                    </li>
                    <li><a href="<?php echo base_url()?>admin/customers/postboxlist"
                            <?php if ($controller === 'admin' && $module == 'customers' && $method =='postboxlist') { ?> class="act" <?php }?>>
                            <?php admin_language_e('them_admi_view_part_navi_PostList')?></a>
                    </li>
                    <li><a href="<?php echo base_url()?>admin/customers/blacklist"
                            <?php if ($controller === 'admin' && $module == 'customers' && $method =='blacklist') { ?> class="act" <?php }?>>
                            <?php admin_language_e('them_admi_view_part_navi_BlacList')?></a>
                    </li>
                </ul>
            </li>
            <li><a href="javascript:void(0)" <?php if ($controller === 'completed') { ?> class="act" <?php }?>><?php admin_language_e('them_admi_view_part_navi_CompList')?></a>
                <ul>
                    <li><a href="<?php echo base_url()?>scans/completed" <?php if ($controller === 'completed' && $method=='index') { ?> class="act" <?php }?>>
                            <?php admin_language_e('them_admi_view_part_navi_CompList')?></a></li>
                    <li>
                        <a href="<?php echo base_url()?>scans/completed/check_item" 
                            <?php if ($controller === 'completed' && $method =='check_item') { ?> class="act" <?php }?>><?php admin_language_e('them_admi_view_part_navi_ChecItem')?></a>
                    </li>
                    <li>
                        <a href="<?php echo base_url()?>admin/customers/postboxhistorylist"
                            <?php if ($controller === 'admin' && $module == 'customers' && $method =='postboxhistorylist') { ?> class="act" <?php }?>>
                            <?php admin_language_e('them_admi_view_part_navi_PostHistList')?></a>
                    </li>
                    <li>
                        <a href="<?php echo base_url()?>scans/completed/shipment_list" 
                                <?php if ($controller === 'completed' && $method =='shipment_list') { ?> class="act" <?php }?>>
                                    <?php admin_language_e('them_admi_view_part_navi_ListOfShip')?></a>
                    </li>
                    <li>
                        <a href="<?php echo base_url()?>admin/customers/customerhistorylist"
                                <?php if ($controller === 'admin' && $module =='customers' && $method =='customerhistorylist') { ?> class="act" <?php }?>>
                                    <?php admin_language_e('them_admi_view_part_navi_AccHisList')?></a>
                    </li>
                </ul>
            </li>
        <?php }?>
        
        <?php if (APContext::isSupperAdminUser() ||  APContext::isAdminUser() || APContext::isAdminParner() || APContext::isAdminLocation()) { ?>
            <li><a href="javascript:void(0)" <?php if ($controller === 'addresses' || ($controller === 'admin' && $module == 'addresses') 
                    || ($controller === 'admin' && $module == 'users') ) { ?> class="act" <?php }?>><span><?php admin_language_e('them_admi_view_part_navi_Loca')?></span></a>
                <ul>
                    <li>
                        <a href="<?php echo base_url()?>addresses/admin/location_pricing" 
                            <?php if ($controller === 'admin' && $module == 'addresses' && $method =='location_pricing') { ?> class="act" <?php }?>>
                            <?php admin_language_e('them_admi_view_part_navi_LocaPric')?></a>
                    </li>
                    <li><a href="<?php echo base_url()?>admin/users" <?php if ($controller === 'admin' && $module == 'users') { ?> class="act" <?php }?>>
                            <?php admin_language_e('them_admi_view_part_navi_WorkAdmi')?></a></li>
                    <li><a href="<?php echo base_url()?>addresses/admin/devices" 
                             <?php if ($controller === 'admin' && $module == 'addresses' && $method =='devices') { ?> class="act" <?php }?>>
                            <?php admin_language_e('them_admi_view_part_navi_Devi')?></a>
                    </li>
                </ul>
            </li>
        <?php }?>
        
        <?php if (APContext::isSupperAdminUser() ||  APContext::isAdminUser()|| APContext::isAdminParner() || APContext::isAdminLocation()) { ?>
            <li><a href="javascript:void(0)" <?php if ($controller === 'report' || ($controller === 'admin' && $module == 'report')) { ?> class="act" <?php }?>>
                    <span class="has_sub_menu"><?php admin_language_e('them_admi_view_part_navi_Repo')?></span></a>
                <ul>
                    <?php if (APContext::isSupperAdminUser() || APContext::isAdminUser()|| APContext::isAdminParner()) { ?>
                        <li><a href="<?php echo base_url()?>admin/report/overview"
                            <?php if ($controller === 'admin' && $module == 'report' && $method =='overview') { ?> class="act" <?php }?>>
                                <?php admin_language_e('them_admi_view_part_navi_Over')?></a>
                        </li>
                    <?php }?>
                    <?php if (APContext::isSupperAdminUser() || APContext::isAdminUser()|| APContext::isAdminParner()) { ?>
                        <li><a href="<?php echo base_url()?>admin/report/invoices"
                            <?php if ($controller === 'admin' && $module == 'report'&& $method =='invoices') { ?> class="act" <?php }?>>
                                <?php admin_language_e('them_admi_view_part_navi_Invo')?></a>
                        </li>
                    <?php }?>
                    <?php if (APContext::isSupperAdminUser() || APContext::isAdminUser()|| APContext::isAdminParner()) { ?>
                        <li><a href="<?php echo base_url()?>admin/report/monthly_report"
                            <?php if ($controller === 'admin' && $module == 'report'&& $method =='monthly_report') { ?> class="act" <?php }?>>
                                <?php admin_language_e('them_admi_view_part_navi_MontRepo')?></a>
                        </li>
                    <?php }?>
                    <?php if (APContext::isSupperAdminUser() || APContext::isAdminUser()|| APContext::isAdminParner()) { ?>
                        <li><a href="<?php echo base_url()?>admin/report/accounting_report"
                            <?php if ($controller === 'admin' && $module == 'report'&& $method =='accounting_report') { ?> class="act" <?php }?>>
                                <?php admin_language_e('them_admi_view_part_navi_Acco')?></a>
                        </li>
                    <?php }?>
                    <?php if (APContext::isSupperAdminUser() || APContext::isAdminUser()|| APContext::isAdminParner()) { ?>
                        <li><a href="<?php echo base_url()?>admin/report/transaction"
                            <?php if ($controller === 'admin' && $module == 'report'&& $method =='transaction') { ?> class="act" <?php }?>>
                                <?php admin_language_e('them_admi_view_part_navi_Tran')?></a>
                        </li>
                    <?php }?>
                    <?php if (APContext::isSupperAdminUser() || APContext::isAdminUser()|| APContext::isAdminParner()) { ?>
                        <li><a href="<?php echo base_url()?>admin/report/open_balance"
                            <?php if ($controller === 'admin' && $module == 'report'&& $method =='open_balance') { ?> class="act" <?php }?>>
                                <?php admin_language_e('them_admi_view_part_navi_OpenBala')?></a>
                        </li>
                    <?php }?>
                    <?php if (APContext::isSupperAdminUser() || APContext::isAdminUser()) { ?>
                        <li><a href="<?php echo base_url()?>admin/report/manage_receipts"
                            <?php if ($controller === 'admin' && $module == 'report'&& $method =='manage_receipts') { ?> class="act" <?php }?>>
                                <?php admin_language_e('them_admi_view_part_navi_EnteRece')?></a>
                        </li>
                    <?php }?>
                    <?php if (APContext::isSupperAdminUser() || APContext::isAdminUser()|| APContext::isAdminParner()) { ?>
                        <li><a href="<?php echo base_url()?>admin/report/storage_fee"
                            <?php if ($controller === 'admin' && $module == 'report'&& $method =='storage_fee') { ?> class="act" <?php }?>>
                                <?php admin_language_e('them_admi_view_part_navi_StorFee')?></a>
                        </li>
                    <?php }?>
                    <?php if (APContext::isSupperAdminUser() || APContext::isAdminUser()|| APContext::isAdminParner() || APContext::isAdminLocation() ) { ?>
                        <li><a href="<?php echo base_url()?>admin/report/location_report"
                            <?php if ($controller === 'admin' && $module == 'report'&& $method =='location_report') { ?> class="act" <?php }?>>
                                <?php admin_language_e('them_admi_view_part_navi_LocaRepo')?></a>
                        </li>
                    <?php }?>
                    <?php if (APContext::isSupperAdminUser() || APContext::isAdminUser()|| APContext::isAdminParner()) { ?>
                        <li><a href="<?php echo base_url()?>admin/report/email_send_hist"
                            <?php if ($controller === 'admin' && $module == 'report'&& $method =='email_send_hist') { ?> class="act" <?php }?>>
                                <?php admin_language_e('them_admi_view_part_navi_EmaiList')?></a>
                        </li>
                    <?php }?>
                    <?php if (APContext::isAdminUser()) { ?>
                        <li><a href="<?php echo base_url()?>admin/report/marketing_partner"
                            <?php if ($controller === 'admin' && $module == 'report'&& $method =='marketing_partner') { ?> class="act" <?php }?>>
                                <?php admin_language_e('them_admi_view_part_navi_MarkPart')?></a>
                        </li>
                    <?php }?>
                </ul>
            </li>
        <?php }?>
        
        <?php if (APContext::isSupperAdminUser() || APContext::isAdminUser()|| APContext::isAdminParner() ) { ?>
            <li><a href="javascript:void(0)" <?php if ($controller === 'category' || ($controller === 'admin' && $module == 'partner') 
                    || ($controller === 'admin' && $module == 'price') || ($controller === 'admin' && $module == 'email') || ($controller === 'admin' && $module == 'device')
                    || ($controller === 'admin' && $module == 'scan')|| ($controller === 'locations' && $module == 'settings')|| ($controller === 'admin' && $module == 'email')
                    || ($controller === 'terms' && $module == 'settings') || ($controller === 'admin' && $module == 'products')|| ($controller === 'servers' && $module == 'settings')
                    || ($controller === 'api' && $module == 'settings')|| ($controller === 'locations' && $module == 'settings')|| ($controller === 'admin' && $module == 'settings')
                    || ($controller === 'type')|| (($controller === 'service_partner' || $controller === 'milestone') && $module == 'cases') ) { ?> class="act" <?php }?>>
                    <span class="has_sub_menu"><?php admin_language_e('them_admi_view_part_navi_Sett')?></span></a>
                <ul>
                    <?php if (APContext::isSupperAdminUser() || APContext::isAdminUser() || APContext::isAdminParner()) { ?>
                        <li><a href="javascript:void(0)" <?php if (($controller === 'service_partner' || $controller === 'milestone') && $module == 'cases') { ?> 
                               class="act" <?php }?>><span class="has_sub_menu"><?php admin_language_e('them_admi_view_part_navi_Case')?></span></a>
                            <ul>
                                <li><a href="<?php echo base_url()?>cases/milestone/index" <?php if ($controller === 'milestone' && $module == 'cases') { ?> class="act" <?php }?>>
                                        <?php admin_language_e('them_admi_view_part_navi_Mile')?></a>
                                </li>
                                <li><a href="<?php echo base_url()?>cases/admin_verification/index" <?php if ($controller === 'admin_verification' && $module == 'cases') { ?> 
                                        class="act" <?php }?>><?php admin_language_e('them_admi_view_part_navi_Trig')?></a>
                                </li>
                                <li><a href="<?php echo base_url()?>cases/admin_case_setting/index" <?php if ($controller === 'admin_case_setting' && $module == 'cases') { ?> 
                                       class="act" <?php }?>><?php admin_language_e('them_admi_view_part_navi_Case')?></a>
                                </li>
                                <li><a href="<?php echo base_url()?>cases/admin_case_task/index" <?php if ($controller === 'admin_case_task' && $module == 'cases') { ?> 
                                       class="act" <?php }?>><?php admin_language_e('them_admi_view_part_navi_TypeOfMile')?></a>
                                </li>
                            </ul>
                        </li>

                        <li><a href="<?php echo base_url()?>admin/partner" <?php if ($controller === 'admin' && $module == 'partner') { ?> class="act" <?php }?>>
                                <?php admin_language_e('them_admi_view_part_navi_Part')?></a></li>
                        <li><a href="<?php echo base_url()?>admin/price" <?php if ($controller === 'admin' && $module == 'price' && $method=='index') { ?> class="act" <?php }?>>
                                <?php admin_language_e('them_admi_view_part_navi_PricTemp')?></a>
                        </li>
                        <li><a href="<?php echo base_url()?>admin/price/phones" <?php if ($controller === 'admin' && $module == 'price' && $method=='phones') { ?> 
                               class="act" <?php }?>><?php admin_language_e('them_admi_view_part_navi_PhonSett')?></a>
                        </li>
                        <li><a href="<?php echo base_url()?>admin/device" <?php if ($controller === 'admin' && $module == 'device') { ?> class="act" <?php }?>>
                                <?php admin_language_e('them_admi_view_part_navi_Devi')?></a></li>
                        <li><a href="<?php echo base_url()?>settings/locations" <?php if ($controller === 'locations' && $module == 'settings') { ?> class="act" <?php }?>>
                                <?php admin_language_e('them_admi_view_part_navi_Loca')?></a>
                        </li>
                        <li><a href="<?php echo base_url()?>admin/email" <?php if ($controller === 'admin' && $module == 'email') { ?> class="act" <?php }?>>
                                <?php admin_language_e('them_admi_view_part_navi_EmaiTemp')?></a></li>
                        <li><a href="javascript:void(0)" <?php if ($controller === 'terms' && $module == 'settings') { ?> class="act" <?php }?>>
                                <?php admin_language_e('them_admi_view_part_navi_TermAndCond')?></a>
                            <ul>
                                <li><a href="<?php echo base_url()?>settings/terms/terms_service" 
                                    <?php if ($controller === 'terms' && $module == 'settings' && $method=="terms_service") { ?> class="act" <?php }?>>
                                        <?php admin_language_e('them_admi_view_part_navi_TermAndCond')?></a>
                                </li>
                                <li><a href="<?php echo base_url()?>settings/terms/privacy"
                                    <?php if ($controller === 'terms' && $module == 'settings' && $method=="privacy") { ?> class="act" <?php }?>>
                                        <?php admin_language_e('them_admi_view_part_navi_Priv')?></a>
                                </li>
                                <li><a href="<?php echo base_url()?>settings/terms/enterprise_tc" 
                                    <?php if ($controller === 'terms' && $module == 'settings' && $method=="enterprise_tc") { ?> class="act" <?php }?>>
                                        <?php admin_language_e('them_admi_view_part_navi_EnteTAndC')?></a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="javascript:void(0)" <?php if (($controller === 'admin' && $module == 'products') || ($controller === 'api' && $module == 'settings' 
                                  && $method=="shipping_credentials")) { ?> class="act" <?php }?>><?php admin_language_e('them_admi_view_part_navi_ProdAndServ')?></a>
                            <ul>
                                <li><a href="<?php echo base_url()?>admin/products/product_matrix"
                                        <?php if ($controller === 'admin' && $module == 'products' && $method=="product_matrix") { ?> class="act" <?php }?>>
                                        <?php admin_language_e('them_admi_view_part_navi_LocaProdMatr')?></a>
                                </li>
                                <li><a href="<?php echo base_url()?>admin/products/shipping_services"
                                        <?php if ($controller === 'admin' && $module == 'products' && $method=="shipping_services") { ?> class="act" <?php }?>>
                                        <?php admin_language_e('them_admi_view_part_navi_ShipServ')?></a>
                                </li>
                                <li><a href="<?php echo base_url()?>admin/products/shipping_carriers"
                                        <?php if ($controller === 'admin' && $module == 'products' && $method=="shipping_carriers") { ?> class="act" <?php }?>>
                                        <?php admin_language_e('them_admi_view_part_navi_ShipCarr')?></a>
                                </li>
                                <li><a href="<?php echo base_url()?>settings/api/shipping_credentials"
                                        <?php if ($controller === 'api' && $module == 'settings' && $method=="shipping_credentials") { ?> class="act" <?php }?>>
                                        <?php admin_language_e('them_admi_view_part_navi_ShipCred')?></a>
                                </li>
                                <li><a href="<?php echo base_url()?>admin/products/shipping_standards"
                                        <?php if ($controller === 'admin' && $module == 'products' && $method=="shipping_standards") { ?> class="act" <?php }?>>
                                        <?php admin_language_e('them_admi_view_part_navi_ShipStan')?></a>
                                </li>
                                <li><a href="<?php echo base_url()?>admin/products/vat_eu"
                                        <?php if ($controller === 'admin' && $module == 'products' && $method=="vat_eu") { ?> class="act" <?php }?>>
                                        <?php admin_language_e('them_admi_view_part_navi_VatEu')?></a>
                                </li>
                                <li><a href="<?php echo base_url()?>admin/products/vat_case"
                                        <?php if ($controller === 'admin' && $module == 'products' && $method=="vat_case") { ?> class="act" <?php }?>>
                                        <?php admin_language_e('them_admi_view_part_navi_VatCaseAdmi')?></a>
                                </li>
                            </ul>
                        </li>
                        <!--
                                <li><a href="javascript:void(0)" <?php //if ($controller === 'servers' && $module == 'settings') { ?> class="act" <?php //}?>><span class="has_sub_menu">Server</span></a>
                          <ul>
                              <li><a href="<?php //echo base_url()?>settings/servers"
                                      <?php //if ($controller === 'servers' && $module == 'settings' && $method=="index") { ?> class="act" <?php //}?>>Server</a></li>
                              <li><a href="<?php //echo base_url()?>settings/servers/domain"
                                      <?php //if ($controller === 'servers' && $module == 'settings' && $method=="domain") { ?> class="act" <?php //}?>>Domain</a></li>

                              <li><a href="<?php //echo base_url()?>settings/servers/database"
                                      <?php //if ($controller === 'servers' && $module == 'settings' && $method=="database") { ?> class="act" <?php //}?>>Database</a></li>
                              <li><a href="<?php //echo base_url()?>settings/servers/storage"
                                      <?php //if ($controller === 'servers' && $module == 'settings' && $method=="storage") { ?> class="act" <?php //}?>>Storage</a></li>
                          </ul>
                        </li>-->
                        <li><a href="javascript:void(0)" <?php if ($controller === 'api' && $module == 'settings'  && $method !="shipping_credentials") { ?> class="act" <?php }?>>
                                <?php admin_language_e('them_admi_view_part_navi_Apis')?></a>
                            <ul>
                                <li><a href="<?php echo base_url()?>settings/api/paypal"
                                        <?php if ($controller === 'api' && $module == 'settings' && $method=="paypal") { ?> class="act" <?php }?>>
                                        <?php admin_language_e('them_admi_view_part_navi_PayP')?></a>
                                </li>
                                <li><a href="<?php echo base_url()?>settings/api/payone"
                                        <?php if ($controller === 'api' && $module == 'settings' && $method=="payone") { ?> class="act" <?php }?>>
                                        <?php admin_language_e('them_admi_view_part_navi_PayO')?></a>
                                </li>
                                <li><a href="<?php echo base_url()?>settings/api/mailchimp"
                                        <?php if ($controller === 'api' && $module == 'settings' && $method=="mailchimp") { ?> class="act" <?php }?>>
                                        <?php admin_language_e('them_admi_view_part_navi_MailChim')?></a>
                                </li>
                                <li><a href="<?php echo base_url()?>settings/api/shipping_apis"
                                        <?php if ($controller === 'api' && $module == 'settings' && $method=="shipping_apis") { ?> class="act" <?php }?>>
                                        <?php admin_language_e('them_admi_view_part_navi_ShipApis')?></a>
                                </li>
                                <li><a href="<?php echo base_url()?>settings/api/google_adwords"
                                        <?php if ($controller === 'api' && $module == 'settings' && $method=="google_adwords") { ?> class="act" <?php }?>>
                                        <?php admin_language_e('them_admi_view_part_navi_GoogAdwo')?></a>
                                </li>
                                <?php if (APContext::isSupperAdminUser()) { ?>
                                    <li><a href="<?php echo base_url() ?>settings/api/partners"
                                        <?php if ($controller === 'api' && $module == 'settings' && $method == "partners") { ?> class="act" <?php } ?>>
                                            <?php admin_language_e('them_admi_view_part_navi_Part')?></a>
                                    </li>
                                <?php }?>
                                <li><a href="<?php echo base_url()?>settings/api/phone_number"
                                    <?php if ($controller === 'api' && $module == 'settings' && $method=="phone_number") { ?> class="act" <?php }?>>
                                        <?php admin_language_e('them_admi_view_part_navi_PhonApi')?></a>
                                </li>
                                <li><a href="<?php echo base_url()?>settings/api/server_ocr"
                                    <?php if ($controller === 'api' && $module == 'settings' && $method=="server_ocr") { ?> class="act" <?php }?>>
                                        <?php admin_language_e('them_admi_view_part_navi_ServerOCR')?></a>
                                </li>
                            </ul>
                        </li>
                        <li><a href="javascript:void(0)" <?php if ($controller === 'admin' && $module == 'settings') { ?> class="act" <?php }?>><span class="has_sub_menu">
                                    <?php admin_language_e('them_admi_view_part_navi_Inst')?></span></a>
                            <ul>
                                <li><a href="<?php echo base_url()?>scans/type" <?php if ($controller === 'type' && $module == 'scans') { ?> class="act" <?php }?>>
                                        <?php admin_language_e('them_admi_view_part_navi_ItemType')?></a></li>
                                <li><a href="<?php echo base_url()?>admin/settings/countries" <?php if ($controller === 'admin' && $module == 'settings' 
                                        && $method == 'countries') { ?> class="act" <?php }?>><?php admin_language_e('them_admi_view_part_navi_Coun')?></a></li>
                                <li><a href="<?php echo base_url()?>admin/settings/currencies" <?php if ($controller === 'admin' && $module == 'settings' 
                                        && $method == 'currencies') { ?> class="act" <?php }?>><?php admin_language_e('them_admi_view_part_navi_Curr')?></a></li>
                                <li><a href="<?php echo base_url()?>admin/settings/languages" <?php if ($controller === 'admin' && $module == 'settings' 
                                        && $method == 'languages') { ?> class="act" <?php }?>><?php admin_language_e('them_admi_view_part_navi_Lang')?></a></li>      
                                <li><a href="<?php echo base_url()?>admin/settings" <?php if ($controller === 'admin' && $module == 'settings' 
                                        && $method == 'index') { ?> class="act" <?php }?>><?php admin_language_e('them_admi_view_part_navi_GeneSett')?></a></li>
                                <li><a href="<?php echo base_url()?>admin/settings/instance_owner" <?php if ($controller === 'admin' && $module == 'settings' 
                                        && $method=='instance_owner') { ?> class="act" <?php }?>><?php admin_language_e('them_admi_view_part_navi_InstOwne')?></a></li>
                                <li><a href="<?php echo base_url()?>admin/settings/design" <?php if ($controller === 'admin' && $module == 'settings' 
                                        && $method=='design'){ ?> class="act" <?php }?> ><?php admin_language_e('them_admi_view_part_navi_Desi')?></a></li>
                                <li><a href="<?php echo base_url()?>scans/category" <?php if ($controller === 'category' && $module == 'scans') { ?> 
                                       class="act" <?php }?>><?php admin_language_e('them_admi_view_part_navi_Cate')?></a></li>
                                <li><a href="<?php echo base_url()?>settings/customs" <?php if ($controller === 'customs' && $module == 'settings') { ?> 
                                       class="act" <?php }?>><?php admin_language_e('them_admi_view_part_navi_CustDecl')?></a></li>
                            </ul>
                        </li>
                    <?php }?>
                </ul>
            </li>
        <?php } ?>

        <?php if (APContext::isSupperAdminUser() || APContext::isAdminUser()|| APContext::isServiceParner() ) { ?>
            <li><a href="javascript:void(0)"
                      <?php if (($controller === 'admin' || $controller === 'todo') && $module == 'cases') { ?> class="act" <?php }?>><span class="has_sub_menu">
                        <?php admin_language_e('them_admi_view_part_navi_Case')?></span></a>
                <ul>
                    <li><a href="<?php echo base_url()?>cases/admin/index"
                          <?php if ($controller === 'admin' && $module == 'cases' && $method == 'index') { ?> class="act" <?php }?>>
                              <?php admin_language_e('them_admi_view_part_navi_Over')?></a>
                    </li>
                    <li><a href="<?php echo base_url()?>cases/todo/index"
                          <?php if ($controller === 'todo' && $module == 'cases' && $method == 'index') { ?> class="act" <?php }?>>
                              <?php admin_language_e('them_admi_view_part_navi_ToDo')?></a>
                    </li>
                </ul>
            </li>
        <?php }?>
        
    </ul>
    
    <ul id="right_menu" class="menusm" style="float:right; width: 120px; right: 20px; position: relative;">
        <?php if (APContext::isSupperAdminUser()) { ?>
            <li><a href="javascript:void(0)"
                      <?php if ($controller === 'admin' && $module == 'instances') { ?> class="act" <?php }?>><span class="has_sub_menu">
                        <?php admin_language_e('them_admi_view_part_navi_Inst')?></span></a>
                <ul style="left: -90px; ">
                    <li><a style="width: 116%;text-align:right;" href="<?php echo base_url()?>admin/instances"
                          <?php if ($controller === 'admin' && $module == 'instances' && $method == 'index') { ?> class="act" <?php }?>>
                            <?php admin_language_e('them_admi_view_part_navi_ManaInst')?></a>
                    </li>
                    <li><a style="width: 116%;text-align:right;" href="<?php echo base_url()?>admin/instances/instance_owner"
                          <?php if ($controller === 'admin' && $module == 'instances' && $method == 'instance_owner') { ?> class="act" <?php }?>>
                            <?php admin_language_e('them_admi_view_part_navi_InstOwne')?></a>
                    </li>
                    <li><a style="width: 116%;text-align:right;" href="<?php echo base_url()?>admin/instances/super_admin"
                          <?php if ($controller === 'admin' && $module == 'instances' && $method == 'super_admin') { ?> class="act" <?php }?>>
                            <?php admin_language_e('them_admi_view_part_navi_SupeAdmi')?></a>
                    </li>
                </ul>
            </li>
        <?php }?>
    </ul>
</nav>