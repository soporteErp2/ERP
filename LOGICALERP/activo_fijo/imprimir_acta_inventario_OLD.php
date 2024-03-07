<?php

	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");

	$titulo            = "";
	$BETWEEN           = "";
	$campoFechaInforme = '';

	if($opc=='inventario_parcial'){
		$titulo  = 'ACTA PARCIAL DE INVENTARIO';
		$BETWEEN = " AND fecha_creacion_en_inventario BETWEEN '$fecha_ini' AND '$fecha_fin'";
		$campoFechaInforme = '	<div style="float:left; width:90%; margin:0px 5px 0px 5px">
									<div style="float:left; width:20%; font-weight:bold;">Fecha Inicio</div>
									<div style="float:left; width:80%;">'.fecha_larga($fecha_ini).'</div>
								</div>
								<div style="float:left; width:90%; margin:0px 5px 0px 5px">
									<div style="float:left; width:20%; font-weight:bold;">Fecha Finalizacion</div>
									<div style="float:left; width:80%;">'.fecha_larga($fecha_fin).'</div>
								</div>';
	}
	else if($opc=='inventario_completo'){
		$titulo  = 'ACTA DE INVENTARIO';
	}

	//if($opc=='inventario_completo'){
		//$titulo='ACTA DE INVENTARIO';

		$contenidoMioDB = 			'<div style="width:740px; margin:10px 0 0 0; font-weight:bold; font-size:12px;">
										<div style="float:left; width:100%; text-align:center; background:#333; color:#FFF">INVENTARIO PROPIO</div>
										<div style="float:left; width:10%;">CODIGO</div>
										<div style="float:left; width:90%;">ITEM</div>
									</div>';

		$contenidoMioPrestadoDB = 	'<div style=" width:740px; margin:10px 0 0 0; font-weight:bold; font-size:12px;">
										<div style="float:left; width:100%; text-align:center; background:#333; color:#FFF">INVENTARIO EN PRESTAMO A OTRA SUCURSAL</div>
										<div style="float:left; width:10%;">CODIGO</div>
										<div style="float:left; width:22%;">ITEM</div>
										<div style="float:left; width:22%;">EMPRESA DESTINO</div>
										<div style="float:left; width:22%;">SUCURSAL DESTINO</div>
										<div style="float:left; width:22%;">BODEGA DESTINO</div>
									</div>';

		$contenidoMioAjenoDB = 		'<div style=" width:740px; margin:10px 0 0 0; font-weight:bold; font-size:12px;">
										<div style="float:left; width:100%; text-align:center; background:#333; color:#FFF">INVENTARIO QUE ME PRESTO OTRA SUCURSAL</div>
										<div style="float:left; width:10%;">CODIGO</div>
										<div style="float:left; width:22%;">ITEM</div>
										<div style="float:left; width:22%;">EMPRESA ORIGEN</div>
										<div style="float:left; width:22%;">SUCURSAL ORIGEN</div>
										<div style="float:left; width:22%;">BODEGA ORIGEN</div>
									</div>';

		$SQL = "SELECT codigo,
						nombre_equipo,
						empresa,
						sucursal,
						id_ubicacion,
						ubicacion,
						empresa_prestamo,
						sucursal_prestamo,
						bodega_prestamo,
						prestado
			 	FROM inventarios
			 	WHERE 	activo=1
			 	 	AND id_usuario_encargado=0
			 	 	AND (
			 	 			id_ubicacion=$filtro_ubicacion
			 	 			OR 	id_bodega_prestamo=$filtro_ubicacion
			 	 		)
					$BETWEEN";

		$consul=mysql_query($SQL,$link);

		if (!$consul){die('no valido informe'.mysql_error().$SQL);}
		while($row = mysql_fetch_array($consul)){
			if($row["id_ubicacion"]==$filtro_ubicacion){
				$nombre_filtro_empresa=$row["empresa"];
				$nombre_filtro_sucursal=$row["sucursal"];
				$nombre_filtro_ubicacion=$row["ubicacion"];


				if($row["prestado"]=="false"){
					$contenidoMioDB.= 			'<div style="float:left, width:740px; margin:0px 0 0 0; font-size:10px; border-bottom: 1px solid #ccc">
									 				<div style="float:left; width:10%;">'.$row["codigo"].'</div>
									 				<div style="float:left; width:90%;">'.$row["nombre_equipo"].'</div>
									 			</div>';
				}

				else{
					$contenidoMioPrestadoDB.= 	'<div style="float:left, width:740px; margin:0px 0 0 0; font-size:10px; border-bottom: 1px solid #ccc">
												 	<div style="float:left; width:10%;">'.$row["codigo"].'</div>
												 	<div class="LetraAjustada" style="float:left; width:22%;">'.$row["nombre_equipo"].'</div>
												 	<div class="LetraAjustada" style="float:left; width:22%;">'.$row["empresa_prestamo"].'</div>
												 	<div class="LetraAjustada" style="float:left; width:22%;">'.$row["sucursal_prestamo"].'</div>
												 	<div class="LetraAjustada" style="float:left; width:22%;">'.$row["bodega_prestamo"].'</div>
												</div>';
				}
			}
			elseif($row["prestado"]=="true" && $row["id_usuario_encargado"]==0){
				$contenidoMioAjenoDB.= 	'<div style="float:left, width:740px; margin:0px 0 0 0; font-size:10px; border-bottom: 1px solid #ccc">
											<div style="float:left; width:10%;">'.$row["codigo"].'</div>
											<div class="LetraAjustada" style="float:left; width:22%;">'.$row["nombre_equipo"].'</div>
											<div class="LetraAjustada" style="float:left; width:22%;">'.$row["empresa"].'</div>
											<div class="LetraAjustada" style="float:left; width:22%;">'.$row["sucursal"].'</div>
											<div class="LetraAjustada" style="float:left; width:22%;">'.$row["ubicacion"].'</div>
										</div>';
			}
		}

		$contenidoDB=$contenidoMioDB.$contenidoMioPrestadoDB.$contenidoMioAjenoDB;
	//}

	// CORRECCION INVENTARIO PARCIAL CARGA LO MISMO QUE EL INVENTARIO TOTAL PERO CON EL BETWEEN

	// if($opc=='inventario_parcial'){
	// 	$titulo = 'ACTA PARCIAL DE INVENTARIO';
	// 	$between = ' AND fecha_creacion_en_inventario BETWEEN "'.$fecha_ini.'" AND "'.$fecha_fin.'"';
	// 	$contenidoDB = 	'<div style=" width:100%; margin:20px 5px 10px 5px; font-weight:bold">
	// 						<div style="float:left; width:100%; text-align:center; margin:20px 0 20px 0;">INVENTARIO</div>
	// 						<div style="float:left; width:20%;">CODIGO</div>
	// 						<div style="float:left; width:80%;">INVENTARIO</div>
	// 					</div>
	// 					<div style=" width:100%; margin:0px 5px 0px 5px">';

	// 	$SQL = " SELECT codigo, nombre_equipo, empresa, sucursal, ubicacion
	// 			 FROM inventarios
	// 			 WHERE activo=1 AND id_ubicacion=".$filtro_ubicacion.$between;

	// 	$consul=mysql_query($SQL,$link);
	// 	if (!$consul){die('no valido informe'.mysql_error().$SQL);}
	// 	while($row = mysql_fetch_array($consul)){
	// 		$nombre_filtro_empresa=$row["empresa"];
	// 		$nombre_filtro_sucursal=$row["sucursal"];
	// 		$nombre_filtro_ubicacion=$row["ubicacion"];

	// 		$contenidoDB.= '<div style="float:left; width:20%;">'.$row["codigo"].'</div>';
	// 		$contenidoDB.= '<div style="float:left; width:80%;">'.$row["nombre_equipo"].'</div>';
	// 	}
	// 	$contenidoDB.= '</div>';
	// }


	/*---------------------------------------------------------------formulario original------------------------------------------------------------------*/
	$contenido.= '		<style>
							.LetraAjustada{
								white-space   : nowrap;
								overflow      : hidden;
								text-overflow : ellipsis;
							}
						</style>

						<div style="float:left; width:740; text-align:center; font-weight:bold; font-size:16px;">
							'.$titulo.'
						</div>
						<div style=" width:740px; font-size:12px;">
							<div style="float:left; width:90%; margin:0px 5px 0px 5px">
								<div style="float:left; width:20%; font-weight:bold;">Empresa</div>
								<div style="float:right; width:80%;">'.$nombre_filtro_empresa.'</div>
							</div>
							<div style="float:left; width:90%; margin:0px 5px 0px 5px;">
								<div style="float:left; width:20%; font-weight:bold;">Sucursal</div>
								<div style="float:right; width:80%;">'.$nombre_filtro_sucursal.'</div>
							</div>
							<div style="float:left; width:90%; margin:0px 5px 0px 5px">
								<div style="float:left; width:20%; font-weight:bold;">Bodega</div>
								<div style="float:left; width:80%;">'.$nombre_filtro_ubicacion.'</div>
							</div>
							'.$campoFechaInforme .'
						</div>
						'.$contenidoDB.'
						<div style=" width:740px; margin-top:40px; font-size:12px; text-align:center">
							<div style="float:left; width:100%;">
								<div style="float:left; width:30%; margin:0 1.5% 0 1.5%;">'.$_SESSION['NOMBREFUNCIONARIO'].'</div>
								<div style="float:left; width:30%; margin:0 1.5% 0 1.5%;">	&nbsp;&nbsp;</div>
								<div style="float:left; width:30%; margin:0 1.5% 0 1.5%;">	&nbsp;&nbsp;</div>
							</div>
							<div style="float:left; width:100%;">
								<div style="float:left; width:30%; margin:0 1.5% 0 1.5%; border-top: 1px solid #000;">Firma Usuario Que Realiza</div>
								<div style="float:left; width:30%; margin:0 1.5% 0 1.5%; border-top: 1px solid #000;">Firma Funcionario Que Entrega</div>
								<div style="float:left; width:30%; margin:0 1.5% 0 1.5%; border-top: 1px solid #000;">Firma Funcionario Que Recibe</div>
							</div>
						</div>';

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

	if($IMPRIME_PDF == 'true'){
		include("../../misc/MPDF54/mpdf.php");
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

?>