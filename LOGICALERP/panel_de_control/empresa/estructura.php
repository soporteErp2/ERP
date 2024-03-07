<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$id_empresa     = $_SESSION['EMPRESA'];
	$contSucursales = 0;
	$contBodegas    = 0;

	$contenido='	<div style="text-align:center; margin-top:20px;"><b>EMPRESA '.$_SESSION['NOMBREEMPRESA'].'<br>ESTRUCTURA SUCURSALES - BODEGAS EMPRESA</b></div>
					<div style="margin-top: 20px;">';

	/*-- ciclo que muestra el 1° nivel sucursales--*/
	$b = mysql_query("SELECT id,nombre FROM empresas_sucursales WHERE id_empresa = '$id_empresa' AND activo=1");
	while($rowb = mysql_fetch_array($b)){
		$id_sucursal = $rowb['id'];
		$contenido .=	'<div style="margin: 0px 30px 10px 20px;">
							<div style="width:90%; overflow:hidden; font-weight:bold; font-size:11px">
								<div style="float:left">'.$rowb['nombre'].'</div>
							</div>';

		/*-- ciclo que muestra el 2° nivel Bodegas--*/
		$c= mysql_query("SELECT id,nombre FROM empresas_sucursales_bodegas WHERE id_sucursal = '$id_sucursal' AND activo=1");
		while($rowc = mysql_fetch_array($c)){
			$contenido.=	'<div style="width:90%; overflow:hidden; margin: 0px 18px 5px 18px; font-size:11px">
								<div style="float:left"><i>'.$rowc['nombre'].'</i></div>
							</div>';
		}

		$contenido.=	'</div>';
	}
	$contenido.='</div>';


	if($opc=="ver"){ echo $contenido; }
	if($opc=="imprimir"){
		$texto = $contenido;

		if(isset($TAM)){$HOJA = $TAM; }else{ $HOJA = 'LETTER'; }
		if(!isset($ORIENTACION)){ $ORIENTACION = 'P'; }
		if(!isset($PDF_GUARDA)){ $PDF_GUARDA = false; }
		if(!isset($IMPRIME_PDF)){ $IMPRIME_PDF = 'true'; }
		if(isset($MARGENES)){ list($MS, $MD, $MI, $ML) = split( ',', $MARGENES ); } 
		else{ $MS = 18;$MD = 10;$MI = 15;$ML = 10; }
		if(!isset($TAMANO_ENCA)){$TAMANO_ENCA = 12 ; }

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

			if($PDF_GUARDA){ $mpdf->Output($documento.".pdf",'D'); }   	///OUTPUT A ARCHIVO
			else{ $mpdf->Output($documento.".pdf",'I'); }		///OUTPUT A VISTA

			exit;
		}
		else{ echo $texto; }
	}

?>