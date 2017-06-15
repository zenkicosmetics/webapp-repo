<!-- CSS -->
<?php Asset::css('yaml/core/base.css'); ?>
<?php Asset::css('yaml/navigation/vlist.css'); ?>
<?php Asset::css('yaml/navigation/hlist.css'); ?>
<?php Asset::css('yaml/forms/gray-theme.css'); ?>
<?php Asset::css('yaml/add-ons/accessible-tabs/tabs.css'); ?>
<?php Asset::css('yaml/screen/typography.css'); ?>
<?php Asset::css('jquery.fancybox-1.3.4.css'); ?>
<?php Asset::css('jquery-ui-1.8.20.custom.css'); ?>
<?php Asset::css('ui.jqgrid.css'); ?>
<?php Asset::css('Aristo.css'); ?>
<?php Asset::css('tipsy.css'); ?>
<?php Asset::css('styles.css'); ?>
<?php Asset::css('layout.css'); ?>

<!-- JS -->
<?php Asset::js('jquery-1.7.2.min.js'); ?>
<?php Asset::js('jquery.blockUI.js'); ?>
<?php Asset::js('jquery-ui-1.8.20.custom.min.js'); ?>
<?php Asset::js('grid.locale-en.js'); ?>
<?php Asset::js('jquery.jqGrid.min.js'); ?>
<?php Asset::js('jquery.common.js'); ?>
<?php Asset::js('jquery.checkbox.min.js'); ?>
<?php Asset::js('jquery.fancybox-1.3.4.pack.js'); ?>
<?php Asset::js('jquery-idleTimeout.js'); ?>
<?php Asset::js('jquery.tipsy.js'); ?>
<?php Asset::js('jquery.layout.js'); ?>
<?php Asset::js('jquery.slimscroll.min.js'); ?>
<?php Asset::js('jquery.cookie.js'); ?>
<?php Asset::js('jquery.slides.min.js'); ?>

<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<?php echo Asset::render(); ?>
<?php echo $template['metadata']; ?>
