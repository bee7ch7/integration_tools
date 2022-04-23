<?php
session_start();

$allowed_uids = array(

  '90',
  '22',
  '55'

);

if (in_array($uid, $allowed_uids)) {

  $_SESSION['integration_bigquery_google_ads'] = 'access';
  $_SESSION['cn'] = $cn;

  header('Location: ../googleAds.php');
  exit();

} else {

  header('Location: ../error.html');
  exit();
}

exit();

?>
