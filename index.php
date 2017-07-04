<?php 

require 'vendor/autoload.php';

use \Curl\Curl;

const JUST_IP_API = array(
  'ipconfig.io', 
  'ifconfig.io', 
  'api.ipify.org'
); 

function getGeo($ip, $json = false, $en = false) {
  //$api_host = JUST_IP_API[time() % 3]; 
  //var_dump(Flight::request()->query); 
  $format = Flight::request()->query['format']; 
  $lang = Flight::request()->query['lang']; 
  if (0 == strcmp($format, 'json')) 
    $json = true; 
  if (0 == strcmp($lang, 'en')) 
    $en = true; 
  $request_url = ($en ? 'ipinfo.io/' : 'ip.cn/').$ip; 
  $curl = new Curl(); 
  $curl->setUserAgent('curl/*'); 
  //$curl->setOpt(CURLOPT_PROXY, 'localhost:9050');
  //$curl->get($api_host.'/'.$ip); 
  //$curl->get('ip.cn/'.$ip); 
  $curl->get($request_url); 
  if (!$curl->error) {
    if ($en) {
      $response = $curl->response; 
      $location = $response->city.', '.$response->region.', '.$response->country; 
    } else {
      $location = explode('来自：', $curl->response)[1]; 
      $location = str_replace("\n", '', $location); 
    }
    if ($json) {
      echo json_encode(array(
        "ip" => $ip, 
        "location" => $location
      )); 
    } else {
      echo $location; 
    }
  }
}

Flight::route('/', function() {
  $ip = Flight::request()->proxy_ip; 
  getGeo($ip); 
}); 

Flight::route('/@ip', function($ip) {
  getGeo($ip); 
}); 

Flight::start(); 
