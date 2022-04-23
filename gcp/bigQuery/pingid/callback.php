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



if (!empty($uid)) {

  include('../auth_validator.php');

} else {

  header('Location: ../error.html');

}


?>
