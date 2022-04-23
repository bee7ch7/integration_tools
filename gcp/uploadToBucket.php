<?php

echo "<pre>";
require 'vendor/autoload.php';
//use GuzzleHttp\Client;
use Google\Cloud\Storage\StorageClient;

//putenv('GOOGLE_APPLICATION_CREDENTIALS=credentials_landing.json');
putenv('GOOGLE_APPLICATION_CREDENTIALS=credentials_test.json');

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
            ['name' => $cloudPath.'/2test_upload_no_quote.csv']
    );

    // is it succeed ?
    return $storageObject != null;
}

$projectId = "dfdp-test-data";
$bucketName = "dfdp-test-data_gcs2bq_landing";
$fileContent = file_get_contents('test_upload_no_quote.csv');
$cloudPath = "prod/lmorderTest/testtitle";
//$fileContent = fopen('test_upload_no_quote.csv', 'r');

$dd = uploadFile($bucketName, $fileContent, $cloudPath, $projectId);


print_r($dd);



 ?>
