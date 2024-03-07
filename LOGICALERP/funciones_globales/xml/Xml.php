<?php
require_once 'ClassFacturaVenta.php';
include("../../../configuracion/conectar.php");
include("../../../configuracion/define_variables.php");

$codigoFactura = 116238;
$facturaVentaXML = new ClassFacturaVenta($mysql,$codigoFactura);
$facturaVentaXML->createXML($codigoFactura);
$facturaVentaXML->signXML();
$facturaVentaXML->addSignXML();

?>
