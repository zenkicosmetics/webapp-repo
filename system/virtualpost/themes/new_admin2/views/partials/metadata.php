<?php $page = $this->uri->segment(1)."/".$this->uri->segment(2)."/".$this->uri->segment(3);
?>
<!-- CSS -->
<?php Asset::css('yaml/core/base.css'); ?>
<?php Asset::css('yaml/navigation/vlist.css'); ?>
<?php Asset::css('yaml/navigation/hlist.css'); ?>
<?php Asset::css('yaml/forms/gray-theme.css'); ?>
<?php Asset::css('yaml/add-ons/accessible-tabs/tabs.css'); ?>
<?php Asset::css('yaml/screen/typography.css'); ?>

<?php Asset::css('jquery-ui-1.8.20.custom.css'); ?>
<?php Asset::css('jquery.fancybox-1.3.4.css'); ?>
<?php Asset::css('Aristo.css'); ?>

<?php Asset::css('ui.jqgrid.css'); ?>
<?php Asset::css('grid.css'); ?>
<?php Asset::css('tipsy.css'); ?>
<?php Asset::css('layout.css'); ?>
<?php Asset::css('menusm.css'); ?>
<?php if($page != "scans/completed/check_item"){ 
      Asset::css('styles.css'); 
}?>
<?php Asset::css('jqlayout.css'); ?>

<!-- JS -->
<?php Asset::js('jquery-1.7.2.min.js'); ?>
<?php Asset::js('jquery.blockUI.js'); ?>
<?php Asset::js('jquery-ui-1.8.20.custom.min.js'); ?>

<?php Asset::js('grid.locale-en.js'); ?>
<?php Asset::js('jquery.jqGrid.min.js'); ?>
<?php Asset::js('jquery.common.js'); ?>
<?php Asset::js('jquery.checkbox.min.js'); ?>
<?php Asset::js('jquery.customSelect.min.js'); ?>
<?php Asset::js('jquery.fancybox-1.3.4.pack.js'); ?>
<?php Asset::js('jquery-idleTimeout.js'); ?>
<?php Asset::js('menusm.js'); ?>
<?php Asset::js('plugins.js'); ?>
<?php Asset::js('scripts.js'); ?>

<?php Asset::js('jquery.tipsy.js'); ?>
<?php Asset::js('jquery.upload-1.0.0.min.js'); ?>
<?php Asset::js('jquery.slimscroll.min.js'); ?>
<?php Asset::js('jquery.cookie.js'); ?>

<?php Asset::js('jquery.tabs.js'); ?>

<?php Asset::js('site.js'); ?>

<?php Asset::js('jquery.layout.min.js'); ?>

<?php Asset::js('colorpicker/jquery.colorpicker.js'); ?>
<?php Asset::js('colorpicker/i18n/jquery.ui.colorpicker-nl.js'); ?>
<?php Asset::js('colorpicker/swatches/jquery.ui.colorpicker-pantone.js'); ?>
<?php Asset::js('colorpicker/parts/jquery.ui.colorpicker-rgbslider.js'); ?>
<?php Asset::js('colorpicker/parts/jquery.ui.colorpicker-memory.js'); ?>
<?php Asset::js('colorpicker/parsers/jquery.ui.colorpicker-cmyk-parser.js'); ?>
<?php Asset::js('colorpicker/parsers/jquery.ui.colorpicker-cmyk-percentage-parser.js'); ?>

<?php Asset::css('colorpicker/jquery.colorpicker.css'); ?>

<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places"></script>

<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

<?php echo Asset::render(); ?>
    
<?php echo $template['metadata']; ?>
