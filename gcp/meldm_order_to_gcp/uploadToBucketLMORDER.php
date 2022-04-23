<?php

echo "<pre>";
require '../vendor/autoload.php';
//use GuzzleHttp\Client;
use Google\Cloud\Storage\StorageClient;

//putenv('GOOGLE_APPLICATION_CREDENTIALS=credentials_landing.json');
putenv('GOOGLE_APPLICATION_CREDENTIALS=credentials_lmbp.json');

function uploadFile($bucketName, $fileContent, $cloudPath, $projectId) {

    $config = [
        'projectId' => $projectId
    ];

    $storage = new StorageClient();
    // set which bucket to work in
    $bucket = $storage->bucket($bucketName);

    // upload/replace file
    $storageObject = $bucket->upload(
            $fileContent,
            ['name' => $cloudPath.'/lmorder.csv']
    );

    // is it succeed ?
    return $storageObject != null;
}


$date = date('Y-m-d');
//$date = date("Y-m-d",strtotime($date . "-1 days")); // today -1


$file = "in/order_status_". $date .".csv";

$projectId = "dfdp-meldm-lmcustomers";
$bucketName = "dfdp-meldm-lmcustomers_gcs2bq_landing";
$fileContent = file_get_contents($file);
$cloudPath = "prod/lmorder/deliveryorderstatus";

if (empty($fileContent)) {
  echo "ERROR: file $file doesn't exist";
  exit();
}

// dfdp-meldm-lmcustomers_gcs2bq_landing/prod/lmorder/deliveryorderstatus


$dd = uploadFile($bucketName, $fileContent, $cloudPath, $projectId);


print_r($dd);

if (!empty($dd)) {

     $date_time_log = date('d_m_Y');
		 rename($file, "archive/order_status_". $date .".csv_OK");

}




 ?>
