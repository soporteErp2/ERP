<?php
/**
* Created by PhpStorm.
* User: garrapato <garrapato@me.com>
* Date: 26/08/14
* Time: 06:41
*/

require_once('nusoap/nusoap.php');

// date_default_timezone_set('America/Mexico_City');

$oSoapClient = new nusoap_client('http://obiee.banrep.gov.co/analytics/saw.dll?wsdl', 'wsdl');


if ($sError = $oSoapClient->getError()) {
echo "No se pudo realizar la operación [" . $sError . "]";
die();
}

$aParametros = array('name' => 'publico', 'password'=> 'publico');
$sessionID = $oSoapClient->call("logon", $aParametros, "", "", "", "", "rpc", "http://schemas.xmlsoap.org/soap/encoding/", "encoded");
echo '<pre>';
print_r($oSoapClient);
echo '</pre>';
 exit;

// Alguno de los dos parámetros (reportPath o reportXml) debe ser diferente de null
$reportRef = array (
'reportPath' => '/shared/Consulta Series Estadisticas desde Excel/1. Tasa de Cambio Peso Colombiano/1.1 TRM - Disponible desde el 27 de noviembre de 1991/TRM para un dia',
'reportXml' => null
);

$xmlOpts = array (
'async' => 'false', // Importante, debe ser false
'maxRowsPerPage' => '100',
'refresh' => 'true',
'presentationInfo' => 'true'
);

$aParametros = array(
'report' => $reportRef,
'outputFormat' => 'SAWRowsetData',
'executionOptions' => $xmlOpts,
$sessionID
);

$query = $oSoapClient->call("executeXMLQuery", $aParametros, "", "", "", "", "rpc", "http://schemas.xmlsoap.org/soap/encoding/", "encoded");

$xml = new SimpleXMLElement($query['return']['rowset'], 0, false);

//var_dump($xml);

$fecha=(string)$xml->Row->Column0;
$tc=(string)$xml->Row->Column2;

$oSoapClient->call("logoff", $sessionID, "", "", "", "", "rpc", "http://schemas.xmlsoap.org/soap/encoding/", "encoded");

// Existe alguna falla en el servicio?
if ($oSoapClient->fault) { // Si
echo 'No se pudo completar la operación';
die();
} else { // No
$sError = $oSoapClient->getError();
// Hay algun error ?
if ($sError) { // Si
echo 'Error:' . $sError;
die();
}

echo "<!DOCTYPE html>";
echo "<html>";
echo "<head>";
echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
echo "<title>Tipo de Cambio</title>";
echo "</head>";
echo "<body>";
echo "Fecha: $fecha<br/>";
echo "Tipo de cambio: $tc<br/>";
echo "</body>";
echo "</html>";
}
?>