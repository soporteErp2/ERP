<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$id_empresa = $_SESSION['EMPRESA'];

	$contenido = '<div style="text-align:center; margin-top:20px;"><b>ESTRUCTURA DE GRUPOS Y SUBGRUPOS DE ACTIVOS FIJOS</b></div>
				  <div style="margin-top: 10px">';

	/*-- ciclo q muestra 1° nivel Grupos --*/
	$a = mysql_query("SELECT * FROM inventario_grupo WHERE activo=1 AND id_empresa='$id_empresa'");
	while($rowa = mysql_fetch_array($a)){
		$id_grupo = $rowa['id'];
		$contenido .= '<div style="margin: 0px 30px 10px 20px; font-size:12px;"><div style="width:90%; overflow:hidden; font-weight:bold;"><div style="width:20px; float:left">'.$rowa['codigo_grupo'].'.</div><div style="float:left;">'.$rowa['nombre_grupo'].'</div></div>';

		/*-- ciclo que muestra el 2° nivel Subgrupos --*/
		$b= mysql_query("SELECT * FROM inventario_grupo_subgrupo WHERE id_grupo='$id_grupo' AND activo=1");
		while($rowb = mysql_fetch_array($b)){
			$contenido .= '<div style="width:90%; overflow:hidden; margin: 5px 25px; font-size:11px"><div style="width:20px; float:left">'. $rowb['codigo_subgrupo'].'.</div><div style="float:left">'.$rowb['nombre_subgrupo'].'</div></div>';
		}
		$contenido .= '</div>';
	}
	$contenido .= '</div>';


	if($opc=="ver"){ echo $contenido; exit; }

 	$texto= $contenido;

	if(isset($TAM)){$HOJA = $TAM;}else{	$HOJA = 'LETTER';}
	if(!isset($ORIENTACION)){$ORIENTACION = 'P';}
	if(!isset($PDF_GUARDA)){$PDF_GUARDA = false;}
	if(!isset($IMPRIME_PDF)){$IMPRIME_PDF = 'true';}
	if(isset($MARGENES)){ list($MS, $MD, $MI, $ML) = split( ',', $MARGENES ); }
	else{ $MS = 18;$MD = 10;$MI = 15;$ML = 10; }
	if(!isset($TAMANO_ENCA)){$TAMANO_ENCA = 12 ;}

	if($IMPRIME_PDF){
		include("../../../misc/MPDF54/mpdf.php");
		$mpdf = new mPDF(
			'utf-8',   					// mode - default ''
			$HOJA,						// format - A4, for example, default ''
			12,							// font size - default 0
			'',							// default font family
			$MI,						// margin_left
			$MD,						// margin right
			$MS,						// margin top
			$ML,						// margin bottom
			10,							// margin header
			10,							// margin footer
			$ORIENTACION				// L - landscape, P - portrait
		);

		$mpdf->SetAutoPageBreak(TRUE, 15);
		$mpdf->SetTitle ( $documento );
		$mpdf->SetAuthor ( $_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA'] );
		$mpdf->SetDisplayMode ( 'fullpage' );
		$mpdf->SetHeader("");
		$mpdf->WriteHTML(utf8_encode($texto));
		if($PDF_GUARDA){ $mpdf->Output($documento.".pdf",'D'); }  	///OUTPUT A ARCHIVO
		else{ $mpdf->Output($documento.".pdf",'I');	}	///OUTPUT A VISTA
		
		exit;
	}
	else{ echo $texto; }
?>



