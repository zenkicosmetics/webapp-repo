<!-- CSS -->
<?php Asset::css('jquery-ui.min.css'); ?>
<?php Asset::css('Aristo.css'); ?>
<?php Asset::css('styles.css'); ?>

<!-- JS -->
<?php Asset::js('jquery2.1.3.min.js'); ?>
<?php Asset::js('jquery-ui.min.js'); ?>
<?php Asset::js('jquery.blockUI.js'); ?>
<?php Asset::js('jquery.common.js'); ?>
<?php Asset::js('jquery.slimscroll.min.js'); ?>


<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<?php echo Asset::render(); ?>
<?php echo $template['metadata']; ?>
