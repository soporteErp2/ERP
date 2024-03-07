<?php
$htmlcontent =  ob_get_contents(); ob_end_clean();

if(!isset($PDF_GUARDA)){
	$PDF_GUARDA = "I"; //LO MUESTRA EN EL NAVEGADOR
} 

if(!isset($IMPRIME_PDF)){
	$IMPRIME_PDF = 'true';
}

if($_SESSION['ID_PROPIEDAD'] == 0){
 	$cual_sucu = "";
}else{
	$cual_sucu = $_SESSION['ID_PROPIEDAD'];
}

if($IMPRIME_PDF == 'false'){
	echo $htmlcontent;
}else{
	$pdf->writeHTML($htmlcontent, true, 0, true, 0);
	$pdf->lastPage();
	$pdf->Output("../../ARCHIVOS_PROPIOS/adjuntos/cotizacion".$cual_sucu."/".$nombre_archivo.".pdf", $PDF_GUARDA);
} 
?>