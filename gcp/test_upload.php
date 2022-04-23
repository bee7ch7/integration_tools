<?php
echo "<pre>";
require 'vendor/autoload.php';
use GuzzleHttp\Client;

$fileName = 'upload_csv_data.csv';
$driveID = "dd"; //1MDiELnFYW9WgtKwFvzh1MjmgyP2fdPh2

putenv('GOOGLE_APPLICATION_CREDENTIALS=credentials.json');

$httpClient = new Client(['verify' => false]);
$client = new Google_Client(['teamDriveId' => $driveID]);

$client->setHttpClient($httpClient);
$client->useApplicationDefaultCredentials();
$client->addScope(Google_Service_Drive::DRIVE);
$driveService = new Google_Service_Drive($client);

$client->setApplicationName("price-monitoring");



// the value below possible to get from google drive by clicking on file and "share" button, the ID will be directly in the link
$file_id = "xx"; 



$new_data = $data_on_google . "$text_received\n";

$emptyFile = new Google_Service_Drive_DriveFile();

$data = file_get_contents('upload_csv_data.csv');


           $write_append = $driveService->files->update($file_id, $emptyFile, array(
            //   'data' => file_get_contents($fileName),
               'data' => $data,
               'mimeType' => 'application/csv',
               'uploadType' => 'multipart',
               'supportsAllDrives' => true,
               'supportsTeamDrives' => true,
               'fields' => 'id'
           ));


      //      print_r($write_append);

exit();

?>
