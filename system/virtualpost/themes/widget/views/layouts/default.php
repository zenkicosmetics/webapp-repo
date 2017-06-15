<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">

<!-- You can use .htaccess and remove these lines to avoid edge case issues. -->
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

<title><?php echo Settings::get(APConstants::SITE_NAME_CODE).' - '.$template['title'];?></title>
<link type="image/png" href="<?php echo APContext::getAssetPath()?>images/favicon2.png" rel="icon">

<base href="<?php echo base_url(); ?>" />

<!-- Mobile viewport optimized -->
<meta name="viewport" content="width=960, initial-scale=1, maximum-scale=1" />
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">

<!-- metadata needs to load before some stuff -->
<?php file_partial('metadata'); ?>
</head>
<body id="user-page">
	<div id="MainPageId" style="text-align: center">
		<?php echo $template['body']; ?>
	</div>
</body>
</html>