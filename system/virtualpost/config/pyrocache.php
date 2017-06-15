<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
if (isset($_SERVER['SERVER_NAME'])){
	$sub_domain_name = isset($_SERVER['HTTP_HOST'])? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
}else{
	$sub_domain_name = "https://dev.eu.clevvermail.com";
}
// we set this here so Pro can set the correct SITE_REF
$config['cache_path'] = APPPATH . 'cache/' . $sub_domain_name . '/codeigniter/';

$config['cache_dir'] = APPPATH.'cache/' . $sub_domain_name . '/';

$config['cache_default_expires'] = 3600; // 1 hours

// Will soon make these options into settings items
// Use Settings::get('product_m') to get this config value
$config['setting_m'] = 3600; // 1 hours

// Make sure all the folders exist
is_dir($config['cache_path']) OR mkdir($config['cache_path'], DIR_WRITE_MODE, TRUE);
is_dir($config['cache_dir']) OR mkdir($config['cache_dir'], DIR_WRITE_MODE, TRUE);
chmod($config['cache_path'], 0777);
chmod($config['cache_dir'], 0777);