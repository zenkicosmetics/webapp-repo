<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Geolocation {
     public static $iplocation = 'iplocation';
     public static $default_country_code = 'VN';
     public static $default_country = 'Vietnam';
     public static $ipdbKey = 'a53daa0f8cb74df6855a90c880158b6bf3feac83';
     public static $ipdbApiUrl = 'http://api.db-ip.com/addrinfo';
     public static $cookieDaysStorage = 6;

     public function __construct() {

     }

     public static function getCurrentIPAddress(){
          return ci()->input->ip_address();
     }

     public static function getCountryInfoFromGeoLocationIPAddress(){
          /*$iplong = ip2long(self::getCurrentIPAddress());
          return ci()->db->select('code, country')->from(self::$iplocation)
               ->where( 'ip_long_from <=',$iplong)->where('ip_long_to >=', $iplong)->limit(1)->get()->row();*/
          ci()->load->helper('cookie');
          $cookie = get_cookie('geolocation');
          if(!$cookie){
               $visitorGeolocation = file_get_contents(self::$ipdbApiUrl.'?addr='.self::getCurrentIPAddress().'&api_key='.self::$ipdbKey);
               if ($visitorGeolocation) {
                    $visitorGeolocation = json_decode($visitorGeolocation);
                    $data = base64_encode(serialize($visitorGeolocation));
                    //setcookie("geolocation", $data, time() + 3600*24* self::$cookieDaysStorage); //set cookie for 1 week
                    set_cookie('geolocation', $data, self::$cookieDaysStorage * 86500 + time());
               }else{
                    return null;
               }
          }else{
               $visitorGeolocation = unserialize(base64_decode($cookie));
          }
          return $visitorGeolocation;
     }

     public static function getCountryCode(){
          $info = self::getCountryInfoFromGeoLocationIPAddress();
          return ($info) ? $info->country : self::$default_country_code;
     }

     public static function getCountry(){
          return null;
          /*$info = self::getCountryInfoFromGeoLocationIPAddress();
          return ($info) ? $info->country : self::$default_country;*/
     }

}