<?php
// archivo: proxy.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $url = 'http://facse.eastus2.cloudapp.azure.com:8092/Nomina/Documento';
    $postData = file_get_contents('php://input');

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

    $response = curl_exec($ch);
    curl_close($ch);

    header('Content-Type: application/json');
    echo $response;
}
?>