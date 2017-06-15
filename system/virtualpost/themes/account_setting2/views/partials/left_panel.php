<?php
    $isStandardCustomer = APContext::isStandardCustomer();
    $isEnterpriseCustomer = APContext::isEnterpriseCustomer();
    $isStandardUser = $isStandardCustomer && APContext::isNormalCustomerUser();
    $isStandardAdminUser = APContext::isAdminCustomerUser();
    $isPrimaryCustomer =  APContext::isPrimaryCustomerUser();
?>

<!-- section -->
<section style="width: 200px">
    <ul class="left-nav">
        <li class="header header2"><a style="font-weight: bold;"
                href="#" onclick="javascript: return false;"><?php language_e('them_acco_view_part_left_panel_Gene')?></a>
        </li>
        <div style="" class="hide_title">
            <?php if ($module != 'info') { ?>
                <li <?php if ($controller == 'account' && $method == 'index') { ?>
                        class="act" <?php } ?>><a href="<?php echo base_url() ?>account"><?php language_e('them_acco_view_part_left_panel_Acco')?></a>
                </li>
                <?php if ($isPrimaryCustomer || $isStandardAdminUser) {?>
                <li <?php if ($controller == 'users' && $method == 'general_users') { ?>
                        class="act" <?php } ?>><a href="<?php echo base_url() ?>account/users/general_users"><?php language_e('them_acco_view_part_left_panel_User')?></a>
                </li>
                <?php } ?>
                <?php if ($isStandardCustomer || $isPrimaryCustomer || $isStandardAdminUser) {?>
                <li <?php if ($controller == 'invoices') { ?> class="act" <?php } ?>><a
                        href="<?php echo base_url() ?>invoices"><?php language_e('them_acco_view_part_left_panel_Invo')?></a>
                </li>
                <?php } ?>
                <?php if ($isStandardCustomer || $isPrimaryCustomer) {?>
                <li <?php if ($controller == 'payment') { ?> class="act" <?php } ?>><a
                        href="<?php echo base_url() ?>payment"><?php language_e('them_acco_view_part_left_panel_Paym')?></a>
                </li>
                <?php } ?>
                <?php if ($isPrimaryCustomer  || $isStandardAdminUser) {?>
                <li <?php if ($controller == 'setting' && $method == 'price') { ?> class="act" <?php } ?>><a
                        href="<?php echo base_url() ?>account/setting/price"><?php language_e('them_acco_view_part_left_panel_PostPric')?></a>
                </li>
                <?php } ?>
                <?php if ($isPrimaryCustomer || $isStandardAdminUser) {?>
                <li <?php if ($controller == 'reporting') { ?> class="act" <?php } ?>><a
                        href="<?php echo base_url() ?>account/reporting"><?php language_e('them_acco_view_part_left_panel_Repo')?></a>
                </li>
                <?php } ?>
                <?php if ($isPrimaryCustomer || $isStandardAdminUser) {?>
                <li <?php if ($controller == 'setting' && $method == 'design') { ?> class="act" <?php } ?>><a
                        href="<?php echo base_url() ?>account/setting/design"><?php language_e('them_acco_view_part_left_panel_Desi')?></a>
                </li>
                <?php } ?>
                <?php if ( $isPrimaryCustomer || $isStandardAdminUser) {?>
                <li <?php if ($controller == 'setting' && $method == 'invoice_setup') { ?> class="act" <?php } ?>><a
                        href="<?php echo base_url() ?>account/setting/invoice_setup"><?php language_e('them_acco_view_part_left_panel_InvoSetu')?></a>
                </li>
                <?php } ?>
                <?php if ($isPrimaryCustomer || $isStandardAdminUser) {?>
                <li <?php if ($module == 'account' && $controller == 'location') { ?> class="act" <?php } ?>><a
                        href="<?php echo base_url() ?>account/location"><?php language_e('them_acco_view_part_left_panel_Loca')?></a>
                </li>
                <?php } ?>
                <?php if ($isPrimaryCustomer || $isStandardAdminUser) {?>
                <li <?php if ($module == 'account' && $controller == 'email_template') { ?> class="act" <?php } ?>><a
                        href="<?php echo base_url() ?>account/email_template"><?php language_e('them_acco_view_part_left_panel_EmaiTemp')?></a>
                </li>
                <?php } ?>
            <?php } else { ?>
                <li
                <?php if ($controller == 'info' && $method == 'how_it_works') { ?>
                        class="act" <?php } ?>><a
                        href="<?php echo base_url() ?>info/how_it_works"><?php language_e('them_acco_view_part_left_panel_HowItWork')?></a></li>
                <li <?php if ($controller == 'info' && $method == 'view_pricing_inline') { ?>
                        class="act" <?php } ?>><a
                        href="<?php echo base_url() ?>info/view_pricing_inline"><?php language_e('them_acco_view_part_left_panel_PostPrcg')?></a></li>
                <li <?php if ($controller == 'info' && $method == 'phone_pricing') { ?>
                        class="act" <?php } ?>><a href="<?php echo base_url() ?>info/phone_pricing"><?php language_e('them_acco_view_part_left_panel_PhonPrcg')?></a>
                </li>
                <li <?php if ($controller == 'info' && $method == 'shipping_calculator') { ?>class="act"<?php } ?>><a
                        href="<?php echo base_url() ?>info/shipping_calculator"><?php language_e('them_acco_view_part_left_panel_ShipCalc')?></a></li>
                <li <?php if ($controller == 'info' && $method == 'view_term_inline') { ?>
                        class="act" <?php } ?>><a
                        href="<?php echo base_url() ?>info/view_term_inline"><?php language_e('them_acco_view_part_left_panel_TermAndCond')?></a></li>
                <li <?php if ($controller == 'info' && $method == 'view_privacy_inline') { ?>
                        class="act" <?php } ?>><a
                        href="<?php echo base_url() ?>info/view_privacy_inline"><?php language_e('them_acco_view_part_left_panel_PrivAndDataProt')?></a></li>
                <li <?php if ($controller == 'info' && $method == 'api_info') { ?>
                        class="act" <?php } ?>><a href="<?php echo base_url() ?>info/api_info"><?php language_e('them_acco_view_part_left_panel_ApiInfo')?></a></li>
            <?php } ?>
        </div>
        <?php if ($module != 'info') { ?>
            <li class="header header2"><a style="font-weight: bold;"
                    href="#" onclick="javascript: return false;"><?php language_e('them_acco_view_part_left_panel_Post')?></a>
            </li>
            <div style="" class="hide_title">
                <li
                <?php if ($controller == 'account' && $method == 'postbox_setting') { ?>
                        class="act" <?php } ?>><a href="<?php echo base_url() ?>account/postbox_setting"><?php language_e('them_acco_view_part_left_panel_PostSett')?></a>
                </li>
                <?php if ($isPrimaryCustomer || $isStandardAdminUser) {?>
                <li <?php if ($controller == 'users' && $method == 'postbox_users') { ?>
                        class="act" <?php } ?>><a href="<?php echo base_url() ?>account/users/postbox_users"><?php language_e('them_acco_view_part_left_panel_User')?></a>
                </li>
                <?php } ?>
                <li <?php if ($controller == 'addresses') { ?> class="act" <?php } ?>><a
                        href="<?php echo base_url() ?>addresses"><?php language_e('them_acco_view_part_left_panel_Posts')?></a></li>
                <li <?php if ($controller == 'cloud') { ?> class="act" <?php } ?>><a
                        href="<?php echo base_url() ?>cloud"><?php language_e('them_acco_view_part_left_panel_Inte')?></a></li>
            </div>
            <li class="header header2"><a style="font-weight: bold;"
                    href="#" onclick="javascript: return false;"><?php language_e('them_acco_view_part_left_panel_Phon')?></a>
            </li>
            <div style="" class="hide_title">
                <?php if ($isStandardCustomer || $isPrimaryCustomer || $isStandardAdminUser) {?>
                <li
                <?php if ($controller == 'account' && $method == 'phone_setting') { ?>
                        class="act" <?php } ?>><a href="<?php echo base_url() ?>account/phone_setting"><?php language_e('them_acco_view_part_left_panel_PhonSett')?></a>
                </li>
                <?php } ?>
                <?php if ($isPrimaryCustomer || $isStandardAdminUser) {?>
                <li
                <?php if ($controller == 'phones_price_setting' && $method == 'index') { ?>
                        class="act" <?php } ?>><a href="<?php echo base_url() ?>account/phones_price_setting"><?php language_e('them_acco_view_part_left_panel_PhonPric')?></a>
                </li>
                <?php } ?>
                <?php if ($isStandardCustomer || $isPrimaryCustomer || $isStandardAdminUser) {?>
                <li <?php if ($controller == 'users' && $method == 'phone_users') { ?> class="act" <?php } ?>><a
                        href="<?php echo base_url() ?>account/users/phone_users"><?php language_e('them_acco_view_part_left_panel_User')?></a>
                </li>
                <?php } ?>
                <li <?php if ($controller == 'number') { ?> class="act" <?php } ?>><a
                        href="<?php echo base_url() ?>account/number"><?php language_e('them_acco_view_part_left_panel_Numb')?></a></li>
                <li <?php if ($controller == 'voiceapp') { ?> class="act" <?php } ?>><a
                        href="<?php echo base_url() ?>account/users/handling_rules"><?php language_e('them_acco_view_part_left_panel_HandRule')?></a></li>
                <li <?php if ($controller == 'phones') { ?> class="act" <?php } ?>><a
                        href="<?php echo base_url() ?>account/target"><?php language_e('them_acco_view_part_left_panel_Targ')?></a></li>
            </div>
        <?php } ?>
    </ul>
    <div class="ym-clearfix"></div>
</section>