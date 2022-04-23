<?php

echo "<pre>";
require '../vendor/autoload.php';
//use GuzzleHttp\Client;
use Google\Cloud\Storage\StorageClient;

//putenv('GOOGLE_APPLICATION_CREDENTIALS=credentials_landing.json');
putenv('GOOGLE_APPLICATION_CREDENTIALS=credentials_video.json');

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
            ['name' => $cloudPath.'/video.csv']
    );

    // is it succeed ?
    return $storageObject != null;
}


$date = date('Y-m-d');
$date_yesterday = date("Y-m-d",strtotime($date . "-1 days"));


$file = "in/cameras_". $date_yesterday .".csv";

$projectId = "dfdp-meldm-lmcustomers";
$bucketName = "dfdp-meldm-lmcustomers_gcs2bq_landing";
$fileContent = file_get_contents($file);
$cloudPath = "prod/customerTransform/countcustomers";
//$fileContent = fopen('test_upload_no_quote.csv', 'r');

if (empty($fileContent)) {
  echo "ERROR: file $file doesn't exist";
  exit();
}

$dd = uploadFile($bucketName, $fileContent, $cloudPath, $projectId);


print_r($dd);

if (!empty($dd)) {

     $date_time_log = date('d_m_Y');
		 rename($file, "archive/cameras_". $date_yesterday .".csv_OK");

}



 ?>
