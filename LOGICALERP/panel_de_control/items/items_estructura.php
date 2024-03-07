<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_familia  = 0;
	$id_grupo    = 0;
	$id_subgrupo = 0;

	$sql = "SELECT F.codigo AS codigo_familia, F.nombre AS familia, F.id AS id_familia,
					FG.codigo AS codigo_grupo, FG.nombre AS grupo, FG.id AS id_grupo,
					FGS.codigo AS codigo_subgrupo, FGS.nombre AS subgrupo, FGS.id AS id_subgrupo
			FROM items_familia AS F,items_familia_grupo AS FG,items_familia_grupo_subgrupo AS FGS
			WHERE FGS.id_empresa = '$id_empresa'
				AND FGS.activo=1
				AND FGS.id_grupo = FG.id
				AND FGS.id_familia = F.id
				AND FG.id_empresa='$id_empresa'
				AND FG.activo=1
				AND F.activo=1
				AND F.id_empresa='$id_empresa'
				GROUP BY FGS.id
				ORDER BY F.codigo ASC, FG.codigo ASC, FGS.codigo ASC";
	$query = mysql_query($sql,$link);

	$contenido =   '<div style="text-align:center; margin:20px 0 10px 0;"><b>ESTRUCTURA DE FAMILIA - GRUPOS - SUBGRUPOS  ITEMS</b></div>
					<div style="margin: 10px 30px 10px 30px;">';


	while($row = mysql_fetch_array($query)){
		if($id_familia != $row['id_familia']){

			$id_familia = $row['id_familia'];
			$contenido .=	'<div style="width:90%; overflow:hidden; font-weight:bold; font-size:12px; margin-top:10px;">
								<div style="width:20px; float:left">'.$row['codigo_familia'].'.</div>
								<div style="float:left;">'.$row['familia'].'</div>
							</div>';
		}

		if($id_grupo != $row['id_grupo']){

			$id_grupo   = $row['id_grupo'];
			$contenido .=		'<div style="width:90%; overflow:hidden; margin: 5px 25px; font-size:11px;">
									<div style="width:20px; float:left">'. $row['codigo_grupo'].'.</div>
									<div style="float:left">'.$row['grupo'].'</div>
								</div>';
		}

		if($id_subgrupo != $row['id_subgrupo']){

			$id_subgrupo = $row['id_subgrupo'];
			$contenido  .=			'<div style="width:80%; overflow:hidden; margin: 5px 50px; font-style: italic; font-size:11px;">
										<div style="width:20px; float:left">'. $row['codigo_subgrupo'].'.</div>
										<div style="float:left">'.$row['subgrupo'].'</div>
									</div>';
		}
	}

	$contenido  .= '</div>';


	if($opc=="ver"){ 	echo $contenido;	}
	if($opc=="imprimir"){

	 	$texto= $contenido;

		if(isset($TAM)){$HOJA = $TAM;}else{	$HOJA = 'LETTER';}
		if(!isset($ORIENTACION)){$ORIENTACION = 'P';}
		if(!isset($PDF_GUARDA)){$PDF_GUARDA = false;}
		if(!isset($IMPRIME_PDF)){$IMPRIME_PDF = 'true';}
		if(isset($MARGENES)){
			list($MS, $MD, $MI, $ML) = split( ',', $MARGENES );
		}else{
			$MS = 18;$MD = 10;$MI = 15;$ML = 10;
		}
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
			if($PDF_GUARDA){
				$mpdf->Output($documento.".pdf",'D');   	///OUTPUT A ARCHIVO
			}else{
				$mpdf->Output($documento.".pdf",'I');		///OUTPUT A VISTA
			}
			exit;
		}else{
			echo $texto;
		}
	}
?>