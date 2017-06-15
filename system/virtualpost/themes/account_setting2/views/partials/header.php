<?php
    $list_site_colors = APContext::getListColorOfSite();
    $logo_url = $list_site_colors['logo_url'];
    //$main_color = $list_site_colors['main_color'];
    //$secondary_color = $list_site_colors['secondary_color'];
?>
<header>
    <div class="ym-grid">
        <div class="ym-g50 ym-gl">
            <div id="logo">
                <a href="<?php echo base_url() ?>">
                    <?php
                    if (empty($logo_url)) {
                        $logo_url = APContext::getImagePath() . '/logo_white_beta.png';
                    } else {
                        $logo_url = APContext::getAssetPath() . $logo_url;
                    }
                    ?>
                    <img src="<?php echo $logo_url ?>" height="40" />
                </a>
            </div>
        </div>

        <div class="ym-g50 ym-gr">
            <div class="top-nav-right">
                <?php if (APUtils::IsEnableCaseFunction()): ?>
                    <?php
                    if ($module == 'cases' && ($controller == 'cases' && in_array($method, array('index', 'under_construction', 'verification')) || ($controller == 'services' && $method == 'index'))) {
                        $cssClass = 'selected';
                    } else {
                        $cssClass = '';
                    }
                    ?>
                    <div><a class="<?php echo $cssClass; ?>" href="<?php echo base_url() ?>cases"><?php language_e('them_acco_view_part_head_Serv')?></a></div>
                <?php endif; ?>
                <?php
                    if ($module == 'office' && ($controller == 'office' )) {
                        $cssClass = 'selected';
                    } else {
                        $cssClass = '';
                    }
                ?>
                <div><a class="<?php echo $cssClass; ?>" href="<?php echo base_url() ?>office"><?php language_e('them_acco_view_part_head_OffiSpac')?></a></div>
                <?php
                $show_in_base_of_germany = APContext::basedOnGermany();
                if ($show_in_base_of_germany) { ?>

                <?php
                    if ($module == 'incorporation' && ($controller == 'office' )) {
                        $cssClass = 'selected';
                    } else {
                        $cssClass = '';
                    }
                ?>
                <div><a class="<?php echo $cssClass; ?>" href="<?php echo base_url() ?>incorporation"><?php language_e('them_user_view_part_head_Incorporation')?></a></div>

                <?php } ?>

                <?php
                    if ($module == 'banking' && ($controller == 'banking' )) {
                        $cssClass = 'selected';
                    } else {
                        $cssClass = '';
                    }
                ?>
                <div><a class="<?php echo $cssClass; ?>" href="<?php echo base_url() ?>banking">Banking</a></div>
                <?php
                if ($module == 'phones') {
                    $cssClass = 'selected';
                } else {
                    $cssClass = '';
                }
                ?>
                <div><a class="<?php echo $cssClass; ?>" href="<?php echo base_url()?>phones" ><?php language_e('them_acco_view_part_head_Phon')?></a></div>
                <?php
                if ($module == 'mailbox') {
                    $cssClass = 'selected';
                } else {
                    $cssClass = '';
                }
                ?>
                <div><a class="<?php echo $cssClass; ?>" href="<?php echo base_url()?>mailbox" ><?php language_e('them_acco_view_part_head_Post')?></a></div>
            </div>
            <div class="ym-clearfix"></div>
            <div  class="bottom-nav-right">
                <div id="user-logout">
                    <a class="bottom-nav-item" href="#" id="customerLogoutButton001"><i class="fa fa-power-off" aria-hidden="true"></i>  <?php language_e('them_acco_view_part_head_Logout')?></a>
                </div>
                <?php
                if (($module != 'info') && (($controller == 'account' || in_array($controller, array('index', 'users', 'postbox_setting', 'phone_setting', 'invoices', 'addresses', 'payment', 'cloud', 'settings'))))) {
                    $cssClass = 'selected';
                } else {
                    $cssClass = '';
                }
                ?>
                <div id="user-nav">
                    <a class="<?php echo $cssClass; ?>" href="<?php echo base_url() ?>account" id="myAccountButton"><i class="fa fa-user" aria-hidden="true"></i> <?php language_e('them_acco_view_part_head_MyAcco')?></a>
                </div>
                <?php
                if (($module == 'info') && ($controller == 'info') && in_array($method, array('how_it_works', 'view_pricing_inline', 'shipping_calculator', 'view_term_inline', 'view_privacy_inline'))) {
                    $cssClass = 'selected';
                } else {
                    $cssClass = '';
                }
                ?>
                <div id="user-info">
                    <a class="<?php echo $cssClass; ?>" href="<?php echo base_url() ?>info/how_it_works" id="customerInfoButton"><i class="fa fa-info" aria-hidden="true"></i> <?php language_e('them_acco_view_part_head_Info')?></a>
                </div>
            </div>
            <div id="top-search">
                <input type="text" id="mainSearchTextbox" placeholder="<?php language_e('them_acco_view_part_head_Seac')?>" /><i class="fa fa-search icon-search" aria-hidden="true"></i>
                <input type="submit" value="Search" />
            </div>
        </div>
    </div>
</header>
<div class="ym-grid COLOR_002" style="height: 10px"></div>
<div style="display: none;">
    <input type="hidden" id="hiddenCheckCurrentMainScreen" value="1"/>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        /**
         * Process when user click to logout button.
         */
        $('#customerLogoutButton001').click(function () {
            // Show confirm dialog
            $.confirm({
                message: 'Are you sure you want to logout?',
                yes: function () {
                    document.location = '<?php echo base_url() ?>customers/logout';
                }
            });
            return false;
        });
    });
</script>