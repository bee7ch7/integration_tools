<?php
session_start();

$code = $_GET['code'];

include('getTokens.php');

$tokens = new TokenController();

$getAuthToken = $tokens->getAuthToken($code);
    $access_token = $getAuthToken['access_token'];
    $expires_in = $getAuthToken['expires_in'];

$getUserProfile = $tokens->getUserAuthToken($access_token);



  $department = $getUserProfile["departmentnumber"][0];
  $department_d = $getUserProfile["departmentnumber"][1];
  $department_t = $getUserProfile["departmentnumber"][2];
  $title = $getUserProfile["title"];
  $cn = $getUserProfile["cn"];
  $uid = $getUserProfile["uid"];
  
  
  echo "<pre>";
  print_r($getUserProfile);

  exit();

if (!empty($uid)) {

  include('../../login.php');

} else {

  header('Location: error2.html');

}

?>
