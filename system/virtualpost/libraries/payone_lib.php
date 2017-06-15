<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Payone_lib
{
   function Payone_lib()
   {
       define('PAYONE_SDK_LIB', APPPATH. 'libraries');
       set_include_path(PAYONE_SDK_LIB . PATH_SEPARATOR . get_include_path());
       require_once(APPPATH.'libraries/Payone/Bootstrap'.EXT);
   }
}
?>