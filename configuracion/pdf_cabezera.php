<?php
include('define_variables.php');

set_time_limit(0);
ini_set("memory_limit","100M");

if($is_informe == 'true'){
	require_once ('../../../misc/tcpdf_php4/config/lang/eng.php');
	require_once ('../../../misc/tcpdf_php4/tcpdf.php');
}else{
	require_once ('../../misc/tcpdf_php4/config/lang/eng.php');
	require_once ('../../misc/tcpdf_php4/tcpdf.php');
}

if(!isset($escala_imagen)){
	$escala_imagen =  3;
}

if(isset($TAM)){
	if($TAM == 1){$HOJA = 'LETTER';}
	if($TAM == 2){$HOJA = 'LEGAL';}
}else{
	$HOJA = 'LETTER';
}

if(!isset($ORIENTACION)){
 $ORIENTACION = 'P';
}

if(isset($MARGENES)){
	list($MS, $MD, $MI, $ML) = split( ',', $MARGENES );
}else{
	$MS = 10;
	$MD = 10;
	$MI = 10;
	$ML = 10;
}

if(!isset($TAMANO_ENCA)){ //TAMANO DE LA LETRA DEL ENCABEZADO
	$TAMANO_ENCA = 12 ;
}


$pdf = new TCPDF($ORIENTACION, "mm", $HOJA, true); //(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true) create new PDF document
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor("Asiste");
$pdf->SetTitle("Asiste");
$pdf->SetSubject($titulo);
$pdf->SetKeywords($titulo ." - Generado por Asiste");

if(file_exists('../../ARCHIVOS_PROPIOS/cabezera_cotizacion.php') && $titulo == 'Cotizacion'){
	include('../../ARCHIVOS_PROPIOS/cabezera_cotizacion.php');
	$prueba = utf8_encode($enca);
	$pdf->SetHeaderData('', '', $prueba);
	$pdf->setPrintHeader(true);  //poner cabezera
}
if(file_exists('../../ARCHIVOS_PROPIOS/cabezera_pedido.php') && $titulo == 'Pedido'){
	include('../../ARCHIVOS_PROPIOS/cabezera_pedido.php');
	$prueba = utf8_encode($enca);
	$pdf->SetHeaderData('', '', $prueba);
	$pdf->setPrintHeader(true);  //poner cabezera
}

if($is_informe == 'true'){
	include('../../../../informes/cabezera_pdf_informes.php');
	$prueba = utf8_encode($enca);
	$pdf->SetHeaderData('', '', $prueba);
	$pdf->setPrintHeader(true);  //poner cabezera
}
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);// set default header data
$pdf->setPrintFooter(true);  //poner pie de pagina
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', $TAMANO_ENCA));// set header and footer fonts
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));// set header and footer fonts
$pdf->SetMargins($MS, $MD, $MI, $ML);//set margins
$pdf->SetHeaderMargin(2);//(PDF_MARGIN_HEADER);//set margins
$pdf->SetFooterMargin(5);//(PDF_MARGIN_FOOTER);//set margins
$pdf->SetAutoPageBreak(TRUE, 5);//(TRUE, PDF_MARGIN_BOTTOM);//set auto page breaks
$pdf->setImageScale($escala_imagen); //set image scale factor
$pdf->setLanguageArray($l); //set some language-dependent strings
$pdf->SetFont("helvetica", "", 8);
$pdf->AliasNbPages();//initialize document
$pdf->AddPage();// add a page

ob_start(); 
?>
