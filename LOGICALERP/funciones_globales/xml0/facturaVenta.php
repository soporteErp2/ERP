<?php
require_once 'nuSoap/nusoap.php';
// require_once '../../web_service/nuSoap/nusoap.php';

//Formato de zona para las fechas
date_default_timezone_set("America/Bogota");

//Creamos un cliente soap
$client = new nusoap_client('https://facturaelectronica.dian.gov.co/habilitacion/B2BIntegrationEngine/FacturaElectronica/facturaElectronica.wsdl', TRUE);

//Datos del header
$username = '69d4f0b6-4924-4479-bdc6-26ef726eaee3';
$password = openssl_digest("logical235","sha256");
$nonce    = base64_encode(rand(10000000000,99999999999));
$created  = ( new DateTime() )->format('Y-m-d\TH:i:s\Z');

//Datos del body
$date = date('Y-m-d\TH:i:s');
$content = file_get_contents('ws_f0900467785000000000f.zip');
$base = base64_encode($content);

//Construccion del header
$header =  '<wsse:Security soapenv:mustUnderstand="1" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
              <wsse:UsernameToken>
                <wsse:Username>'.$username.'</wsse:Username>
                <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordDigest">'.$password.'</wsse:Password>
                <wsse:Nonce EncodingType="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary">'.$nonce.'</wsse:Nonce>
                <wsu:Created>'.$created.'</wsu:Created>
              </wsse:UsernameToken>
            </wsse:Security>';

//Enviando el header a la peticion soap
$client->setHeaders($header);
$client->soap_defencoding = 'UTF-8';
$client->http_encoding = 'gzip,deflate';
$client->useHTTPPersistentConnection();
// $client->namespaces = array(
//                         'soapenv'=>"http://schemas.xmlsoap.org/soap/envelope/",
//                         // "SOAP-ENV"=>"http://schemas.xmlsoap.org/soap/envelope/",
//                         "rep"=>"http://www.dian.gov.co/servicios/facturaelectronica/ReportarFactura"
//                       );
// $client->namespaces[] = ('soapenv','http://schemas.xmlsoap.org/soap/envelope/');

//Construccion del body
$body =  '<rep:EnvioFacturaElectronicaPeticion>
            <rep:NIT>900467785</rep:NIT>
            <rep:InvoiceNumber>990000001</rep:InvoiceNumber>
            <rep:IssueDate>'.$date.'</rep:IssueDate>
            <rep:Document>'.$base.'</rep:Document>
          </rep:EnvioFacturaElectronicaPeticion>';

//Se llama a la funcion para enviar la peticion soap
$result = $client->call('EnvioFacturaElectronica',$body);
$error = $client->getError();

if($error){
  echo "Error en el envio<br>" . $error . "<br>";
  echo $client->response;
}

// echo '<h2>Cabecera</h2><pre>'. $header .'</pre>';
echo '<h2>Request</h2><pre>'. htmlspecialchars($client->request, ENT_QUOTES) .'</pre>';
echo '<h2>Response</h2><pre>'. htmlspecialchars($client->response, ENT_QUOTES) .'</pre>';
echo '<h2>Debug</h2><pre>'. htmlspecialchars($client->debug_str, ENT_QUOTES) .'</pre>';

?>
