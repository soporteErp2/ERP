<?php

	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	//consultar el nombre de la empresa
	$consulta_nombre=mysql_query("SELECT nombre_empresa FROM inventario_totales_traslados WHERE id=".$id,$link);
	$nombre_empresa=mysql_fetch_array($consulta_nombre);

	$titulo=' TRASLADO DE INVENTARIO ';
	$tabla='inventario_totales_traslados';


	$SQL = "SELECT * FROM inventario_totales_traslados WHERE activo=1 AND id=".$id." AND id_empresa=".$_SESSION["EMPRESA"];
	$consul=mysql_query($SQL,$link);
	if (!$consul){die('no valido informe'.mysql_error());}
	while($row = mysql_fetch_array($consul)){


	/*---------------------------------------------------------------formulario original------------------------------------------------------------------*/
	$documento = "No.'".str_pad($id, 12, "0", STR_PAD_LEFT)."'";
	$contenido ='<div id="body_pdf" style="width:780px; font-style:normal; font-size:11px; height:50%; border-bottom: 3px dotted;" >
					<div style="float:left; width:90%; margin:0px 10px 20px 20px; text-align:center; font-weight:bold;">
						<div style="float:right; width:60px;">Original</div>
						'.$nombre_empresa["nombre_empresa"].'<br> '.$titulo.'<br>
						'.$documento.'
					</div><br>';

	$contenido.='	<div style="overflow: hidden; width:100%; margin-bottom:15px;">
						<div style="float:left; width:90%; margin:0px 5px 0px 10px">
								<div style="clear:both; float:left; width:40%;">Equipo</div>
								<div style="float:right; width:60%;">'.$row["nombre_equipo"].'</div>
						</div>
						<div style="float:left; width:90%; margin:0px 5px 0px 10px;">
								<div style="clear:both; float:left; width:40%;">Codigo</div>
								<div style="float:right; width:60%;">'.$row["codigo"].'</div>
						</div>
						<div style="float:left; width:90%; margin:0px 5px 0px 10px;">
								<div style="clear:both; float:left; width:40%;">Cantidad</div>
								<div style="float:right; width:60%;">'.$row["cantidad"].'</div>
						</div>
						<div style="float:left; width:90%; margin:0px 5px 0px 10px">
								<div style="float:left; width:40%;">Fecha y Hora de '.$funcion.'</div>
								<div style="float:left; width:60%;">'.$row["fecha"].'</div>
						</div>
					</div>

					<table style="width:100%; font-style:normal; font-size:11px; margin:10px 5px 0px 10px; border-collapse:collapse;" >
						<tr>
							<td style="border-right: 1px solid;"> </td>
							<td style="border: 1px solid; font-weight:bold;"> Origen</td>
							<td style="border: 1px solid; font-weight:bold;"> Destino</td>
						</tr>
						<tr>
							<td style="border: 1px solid; font-weight:bold;"> Sucursal</td>
							<td style="border: 1px solid;"> '.$row["nombre_sucursal_origen"].'</td>
							<td style="border: 1px solid;"> '.$row["nombre_sucursal_destino"].'</td>
						</tr>
						<tr>
							<td style="border: 1px solid; font-weight:bold;"> Bodega</td>
							<td style="border: 1px solid;"> '.$row["nombre_bodega_origen"].'</td>
							<td style="border: 1px solid;"> '.$row["nombre_bodega_destino"].'</td>
						</tr>
					</table>

					<div style="overflow: hidden; width:100%; margin:5px 5px 20px 0px; padding:0px 7px 0px 0px;">
						<div style="float:left; width:90%; margin:5px 5px 0px 10px; font-weight:bold;">
							Observaciones del '.$funcion.'
						</div>
						<div style="float:left; width:100%; margin:3px 200px 5px 10px; padding:5px 10px 5px 10px; border: 1px solid; height:40px;">
							'.$row["observaciones"].'
						</div>
					</div>

					<div style="overflow: hidden; width:100%;">
						<div style="float:left; width:90%; margin:5px 5px 0px 0px">
							<div style="float:left; width:30%; margin:0px 10px 10px 10px;">'.$row["nombre_usuario"].'</div>
							<div style="float:left; width:30%; margin:0px 10px 10px 10px">	&nbsp;&nbsp;</div>
							<div style="float:left; width:30%; margin:0px 10px 10px 10px">	&nbsp;&nbsp;</div>
						</div>
						<div style="float:left; width:90%; margin:0px 5px 20px 0px">
							<div style="float:left; width:30%; margin:0px 30px 20px 10px; border-top: 1px solid #000;">
								Firma Usuario Que Realiza El '.$funcion.'
							</div>
							<div style="float:left; width:30%; margin:0px 10px 20px 10px; border-top: 1px solid #000;">
								Firma Funcionario Que Entrega
							</div>
							<div style="float:left; width:30%; margin:0px 10px 20px 10px; border-top: 1px solid #000;">
								Firma Funcionario Que Recibe
							</div>
						</div>
					</div>
				</div>';

	/*---------------------------------------------------------------formulario copia------------------------------------------------------------------*/
	$contenido .='<div id="body_pdf" style="width:780px; font-style:normal; font-size:11px; margin-top:40px;" >
					<div style="float:left; width:90%; margin:10px 10px 20px 20px; text-align:center; font-weight:bold;">
						<div style="float:right; width:60px;">Copia</div>
						'.$nombre_empresa["nombre_empresa"].'<br> '.$titulo.'<br>
						'.$documento.'
					</div><br>';

	$contenido.='
					<div style="overflow: hidden; width:100%;  margin-bottom:15px;">
						<div style="float:left; width:90%; margin:0px 5px 0px 10px">
								<div style="clear:both; float:left; width:40%;">Equipo</div>
								<div style="float:right; width:60%;">'.$row["nombre_equipo"].'</div>
						</div>
						<div style="float:left; width:90%; margin:0px 5px 0px 10px;">
								<div style="clear:both; float:left; width:40%;">Codigo</div>
								<div style="float:right; width:60%;">'.$row["codigo"].'</div>
						</div>
						<div style="float:left; width:90%; margin:0px 5px 0px 10px;">
								<div style="clear:both; float:left; width:40%;">Cantidad</div>
								<div style="float:right; width:60%;">'.$row["cantidad"].'</div>
						</div>
						<div style="float:left; width:90%; margin:0px 5px 0px 10px">
								<div style="float:left; width:40%;">Fecha y Hora de '.$funcion.'</div>
								<div style="float:left; width:60%;">'.$row["fecha"].'</div>
						</div>
					</div>

					<table style="width:100%; font-style:normal; font-size:11px; margin:10px 5px 0px 10px; border-collapse:collapse;" >
						<tr>
							<td style="border-right: 1px solid; border-buttom: 1px solid;"> </td>
							<td style="border: 1px solid; font-weight:bold;"> Origen</td>
							<td style="border: 1px solid; font-weight:bold;"> Destino</td>
						</tr>
						<tr>
							<td style="border: 1px solid; font-weight:bold;"> Sucursal</td>
							<td style="border: 1px solid;"> '.$row["nombre_sucursal_origen"].'</td>
							<td style="border: 1px solid;"> '.$row["nombre_sucursal_destino"].'</td>
						</tr>
						<tr>
							<td style="border: 1px solid; font-weight:bold;"> Bodega</td>
							<td style="border: 1px solid;"> '.$row["nombre_bodega_origen"].'</td>
							<td style="border: 1px solid;"> '.$row["nombre_bodega_destino"].'</td>
						</tr>
					</table>

					<div style="overflow: hidden; width:100%; margin:5px 5px 0px 0px; padding:0px 7px 0px 0px;">
						<div style="float:left; width:90%; margin:5px 5px 0px 10px; font-weight:bold;">
							Observaciones del '.$funcion.'
						</div>
						<div style="float:left; width:100%; margin:3px 200px 5px 10px; padding:5px 10px 5px 10px; border: 1px solid; height:40px;">
							'.$row["observaciones"].'
						</div>
					</div>

					<div style="overflow: hidden; width:100%;">
						<div style="float:left; width:90%; margin:5px 5px 0px 0px">
							<div style="float:left; width:30%; margin:0px 10px 10px 10px;">'.$row["nombre_usuario"].'</div>
							<div style="float:left; width:30%; margin:0px 10px 10px 10px">	&nbsp;&nbsp;</div>
							<div style="float:left; width:30%; margin:0px 10px 10px 10px">	&nbsp;&nbsp;</div>
						</div>
						<div style="float:left; width:90%; margin:0px 5px 20px 0px">
							<div style="float:left; width:30%; margin:0px 30px 0px 10px; border-top: 1px solid #000;">
								Firma Usuario Que Realiza El '.$funcion.'
							</div>
							<div style="float:left; width:30%; margin:0px 10px 0px 10px; border-top: 1px solid #000;">
								Firma Funcionario Que Entrega
							</div>
							<div style="float:left; width:30%; margin:0px 10px 0px 10px; border-top: 1px solid #000;">
								Firma Funcionario Que Recibe
							</div>
						</div>
					</div>
				</div>';
	}

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

?>