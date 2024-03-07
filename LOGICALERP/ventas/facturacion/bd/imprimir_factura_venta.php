<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../../bd/functions_imprimir.php");

	if(!isset($tablaPrincipal)){ include("../../config_var_global.php"); }

	if(!isset($_SESSION['EMPRESA']) || $_SESSION['EMPRESA']==''){ exit; }

	if(file_exists("../../../../ARCHIVOS_PROPIOS/empresa_".$_SESSION['ID_HOST']."/panel_de_control/formato_documentos/formato_factura.php")){
		include("../../../../ARCHIVOS_PROPIOS/empresa_".$_SESSION['ID_HOST']."/panel_de_control/formato_documentos/formato_factura.php");
	}
	else{

		$id_empresa       = $_SESSION['EMPRESA'];
		$documento        = 'Factura';
		$estilo           = 'background-color: #DFDFDF;';
		$subTotalFactura  = 0.00;
		$acumuDescFactura = 0.00;
		$ivaFactura       = 0.00;
		$valorRetencion   = 0.00;
		$totalFactura     = 0.00;

		//================================ GRUPOS ================================//
		//AGREGAR LOS GRUPOS
		$sql = "SELECT
							id,
							codigo,
							nombre,
							cantidad,
							costo_unitario,
							observaciones,
							descuento,
							porcentaje_impuesto,
							valor_impuesto
			 			FROM
							ventas_facturas_grupos
						WHERE
							activo = 1
						AND
							id_empresa = $id_empresa
						AND
							id_factura_venta = $id";

		$query = $mysql->query($sql,$mysql->link);

		while($row=$mysql->fetch_array($query)){
			$id_grupo = $row['id'];
			$arrayGrupos[$id_grupo] =  array(
																				'codigo'         				=> $row['codigo'],
																				'nombre'         				=> $row['nombre'],
																				'cantidad'       				=> $row['cantidad'],
																				'costo_unitario' 				=> $row['costo_unitario'],
																				'observaciones'  				=> $row['observaciones'],
																				'descuento'      				=> $row['descuento'],
																				'porcentaje_impuesto'		=> $row['porcentaje_impuesto'],
																				'valor_impuesto' 				=> $row['valor_impuesto']
																			);
			$whereGrupo .= ($whereGrupo == '')? "id_grupo_factura_venta=$id_grupo " : " OR id_grupo_factura_venta=$id_grupo" ;
		}

		// CONSULTAR LOS ITEMS RELACIOANDOS A ESOS GRUPOS
		$sql = "SELECT
							id_grupo_factura_venta,
							id_inventario_factura_venta
 				    FROM
							ventas_facturas_inventario_grupos
						WHERE
							($whereGrupo)";

		$query = $mysql->query($sql,$mysql->link);

		while ($row=$mysql->fetch_array($query)) {
			$id_grupo      								 = $row['id_grupo_factura_venta'];
			$id_inventario 								 = $row['id_inventario_factura_venta'];
			$arrayInvGrupo[$id_inventario] = $id_grupo;
			$groupByG 										 = "id,";
		}

		$sql   			= "SELECT T.id,T.exento_iva,T.dv,T.tipo_identificacion AS tipo_nit FROM terceros AS T, $tablaPrincipal AS DV WHERE T.id=DV.id_cliente AND DV.id=$id";
		$query 			= mysql_query($sql,$link);
		$dv         = mysql_result($query,0,'dv');
		$exento_iva = mysql_result($query,0,'exento_iva');
		$tipo_nit   = mysql_result($query,0,'tipo_nit');

		$tipo_nit   = ($tipo_nit == '')? "NIT": $tipo_nit;

		$sql   = "SELECT * FROM $tablaPrincipal WHERE id='$id' AND activo=1 AND id_empresa='$id_empresa'";
		$query = mysql_query($sql,$link);

		if (!$query){ die('no valido informe'.mysql_error()); }
		while($row = mysql_fetch_array($query)){
			$nitEmpresa          = ($dv > 0)? $row["nit"].'-'.$dv: $row["nit"];
			$id_sucursal         = $row['id_sucursal'];
			$id_sucursal_cliente = $row['id_sucursal_cliente'];
			$orden_compra        = $row['orden_compra'];
			$estado              = $row['estado'];
			$observacion         = $row['observacion'];
			$cliente             = $row["cliente"];
			$id_resolucion       = $row["id_configuracion_resolucion"];
		}

		if ($estado==0) { echo '<center><h2><i>Documento no Generado</i></h2></center>'; exit; } //DOUMENTO NO GENERADO

		// AGREGAR LOS GRUPOS DE LA FACTURA
		foreach ($arrayGrupos as $id_grupo => $arrayResult) {
			$subTotal = $arrayResult["costo_unitario"]-$arrayResult['descuento'];
			$articulos .=  "<table class='StyleTableArticulos' width='740' border='0' cellspacing='0' cellpadding='0'>
												<tr>
													<td valign='top' width='80'>$arrayResult[codigo]</td>
													<td valign='top' width='280'><b>$arrayResult[nombre]</b></td>
													<td valign='top' width='80' align='right'>Unidad x 1</td>
													<td valign='top' width='70' align='right'>$arrayResult[cantidad]</td>
													<td valign='top' width='80' align='right'>".number_format($arrayResult["costo_unitario"],$_SESSION['DECIMALESMONEDA'])."</td>
													<td valign='top' width='70' align='right'>$$arrayResult[descuento]</td>
													<td valign='top' width='80' align='right'>".number_format($subTotal,$_SESSION['DECIMALESMONEDA'])."</td>
												</tr>".(($arrayResult["observaciones"] != '')?
											 "<tr>
													<td>&nbsp;</td>
													<td colspan='6' style='font-size:9px;width:100%;'>".str_replace("\n",'<br>',$arrayResult['observaciones'])."</td>
												</tr>
											</table>":
										 "</table>" );
		}

		//================================ GRUPOS ================================//
		//************************************************************************//
		// AGREGAR LOS ARTICULOS
		$marcaAgua = ($estado == 3)? 'true': 'false';
		$sqlArticulos =  "SELECT
												*,
												SUM(cantidad) AS cantidad_total,COUNT(id) AS cant_filas
											FROM
												$tablaInventario
											WHERE
												$idTablaPrincipal = '$id'
											GROUP BY
												$groupByG
												id_inventario,
												observaciones,
												tipo_descuento,
												descuento,
												costo_unitario";

		$queryArticulos = mysql_query($sqlArticulos,$link);

		while($array = mysql_fetch_array($queryArticulos)){

			// ARMAR EL ARRAY CON LA INFORMACION DE LOS ARTICULOS DE LA FACTURA, PERO AGRUPADOS
			$id_row         = $array['id'];
			$id_inventario  = $array['id_inventario'];
			$observaciones  = $array['observaciones'];
			$tipo_descuento = $array['tipo_descuento'];
			$descuento      = $array['descuento'];
			$cant_filas     = $array['cant_filas'];
			$costo_unitario = $array['costo_unitario'];
			$valor_impuesto = $array['valor_impuesto'];
			$styleBorder    = ($array["observaciones"] == '')? 'border-bottom:1px solid #000;': 'border-bottom:1px dotted #000;';
			if($array["tipo_descuento"] == 'porcentaje'){
				$subTotal = ($array["cantidad_total"] * $array["costo_unitario"]) - (($array["cantidad_total"] * $array["costo_unitario"]) * $descuento / 100);
			} else{
				$subTotal = ($array["cantidad_total"] * $array["costo_unitario"]) - ($descuento*$cant_filas);
			}
			$tipoDescuento  = ($array["tipo_descuento"] =='porcentaje')? ' %': ' $';

			//CALCULO DEL IVA POR ARTICULO
			$iva = ($exento_iva=='Si')? 0 :	($array["valor_impuesto"] > 0)? $array["valor_impuesto"] * $subTotal / 100: 0;

			$arrayIvaDocumento[$array['id_impuesto']] += $iva;
			$arrayDatosIva[$array['id_impuesto']]      = array('nombre' => $array['impuesto'],'valor_impuesto'=> $array['valor_impuesto']*1);

			$subTotalFactura  = $subTotal+$subTotalFactura;
			$ivaFactura       = $ivaFactura+$iva;
			// $acumuDescFactura = $acumuDescFactura+$descuento;

			// SI LA FILA ESTA RELACIONADA A UN GRUPO, ENTONCES SALTAR LA FILA
			if (array_key_exists($array['id'], $arrayInvGrupo)) { continue; }

			// VERIFICAR SI EL ARRAY YA EXISTEN LAS POSICIONES
			if ( isset($arrayItems[$id_inventario][$costo_unitario][$tipo_descuento][$descuento][$observaciones]) ) {
				$arrayItems[$id_inventario][$costo_unitario][$tipo_descuento][$descuento][$observaciones]['cantidad']       += $array["cantidad_total"];
				$arrayItems[$id_inventario][$costo_unitario][$tipo_descuento][$descuento][$observaciones]['descuento']      += ($tipoDescuento == '$')? $descuento : '';
				$arrayItems[$id_inventario][$costo_unitario][$tipo_descuento][$descuento][$observaciones]['valor_impuesto'] += $array["valor_impuesto"];
				$arrayItems[$id_inventario][$costo_unitario][$tipo_descuento][$descuento][$observaciones]['subTotal']       += $subTotal;;
			}
			else{
				 $arrayItems[$id_inventario]
										[$costo_unitario]
										[$tipo_descuento]
										[$descuento]
										[$observaciones] = array(
																							'descuento'              => $descuento,
																							'cantidad'               => $array["cantidad_total"],
																							'codigo'                 => $array["codigo"],
																							'nombre'                 => $array["nombre"],
																							'nombre_unidad_medida'   => $array["nombre_unidad_medida"],
																							'cantidad_unidad_medida' => $array["cantidad_unidad_medida"],
																							'valor_impuesto'         => $array["valor_impuesto"],
																							'subTotal'               => $subTotal,
																							'tipo_descuento'				 => $tipoDescuento
																						);
			}

		}

		foreach ($arrayItems as $id_inventario => $arrayItems1) {
			foreach ($arrayItems1 as $costo_unitario => $arrayItems2){
				foreach ($arrayItems2 as $tipo_descuento => $arrayItems3){
					foreach ($arrayItems3 as $descuento => $arrayItems4){
						foreach ($arrayItems4 as $observaciones => $arrayResult){

							$estilo = ($estilo!='')? '': 'background-color: #F2F2F2;';
							$articulos .=  "<table class='StyleTableArticulos' width='740' border='0' cellspacing='0' cellpadding='0'>
												    		<tr>
																	<td valign='top' width='80'>$arrayResult[codigo]</td>
																	<td valign='top' width='280'><b>$arrayResult[nombre]</b></td>
																	<td valign='top' width='80' align='right'>$arrayResult[nombre_unidad_medida] x $arrayResult[cantidad_unidad_medida]</td>
																	<td valign='top' width='70' align='right'>$arrayResult[cantidad]</td>
																	<td valign='top' width='80' align='right'>".number_format($costo_unitario,$_SESSION['DECIMALESMONEDA'])."</td>
																	<td valign='top' width='70' align='right'>$arrayResult[tipo_descuento] $arrayResult[descuento]</td>
																	<td valign='top' width='80' align='right'>".number_format($arrayResult['subTotal'],$_SESSION['DECIMALESMONEDA'])."</td>
																</tr>".(($observaciones != '')?
																"<tr>
																	<td>&nbsp;</td>
																	<td colspan='6' style='font-size:9px;width:100%;'>".str_replace("\n",'<br>',$observaciones)."</td>
																</tr>
															</table>":
														 "</table>" );
						}// FIN ULTIMO FOR EACH
					}
				}
			}
		}

		//================================// IVA //===============================//
		//************************************************************************//
		$div_iva = '';
		foreach ($arrayIvaDocumento as $id_impuesto => $valor_impuesto) {
			if($valor_impuesto>0){
				$div_iva .=  '<tr>
												<td style="font-weight:bold">'.$arrayDatosIva[$id_impuesto]['nombre'].' ('.$arrayDatosIva[$id_impuesto]['valor_impuesto'].'%)</td>
												<td>'.$_SESSION["SIMBOLOMONEDA"].' '.number_format ($valor_impuesto,$_SESSION['DECIMALESMONEDA']).'</td>
											</tr>';
			}
		}
		$subTotalFactura = ROUND($subTotalFactura ,$_SESSION['DECIMALESMONEDA']);
		//============================// RETENCIONES //===========================//
		//************************************************************************//
		$div_retenciones    = '';
		$valorRetencion     = 0;
		$listadoRetenciones = '';
		$simboloRetencion   = '';
		$valoresRetenciones = '';
		$roundRetencion     = 0;

		$sqlRetenciones   = "SELECT retencion,valor,tipo_retencion,base FROM $tablaRetenciones WHERE $idTablaPrincipal='$id' AND activo=1";
		$queryRetenciones = mysql_query($sqlRetenciones,$link);

		while ($arrayRetenciones = mysql_fetch_array($queryRetenciones)) {

			$roundRetencion = 0;
			$arrayRetenciones["valor"] = $arrayRetenciones["valor"]*1;

			if ($arrayRetenciones["tipo_retencion"] == 'ReteIva') {

				$roundRetencion = ROUND($ivaFactura * $arrayRetenciones["valor"]/100, $_SESSION['DECIMALESMONEDA']);

				if ($arrayRetenciones['base'] > $ivaFactura) { continue; }
				$valorRetencion += $roundRetencion;
				$div_retenciones .=  '<tr>
																<td style="font-weight:bold">'.$arrayRetenciones["retencion"].' ('.$arrayRetenciones["valor"].' %)</td>
																<td>'.$_SESSION["SIMBOLOMONEDA"].' '.number_format ($roundRetencion, $_SESSION['DECIMALESMONEDA']).'</td>
															</tr>';
			}
			else if ($arrayRetenciones["tipo_retencion"] == 'AutoRetencion') { continue; }
			else{
				if ($arrayRetenciones['base'] > $subTotalFactura) { continue; }

				$roundRetencion = ROUND($subTotalFactura * $arrayRetenciones["valor"]/100,$_SESSION['DECIMALESMONEDA']);

				$valorRetencion += $roundRetencion;
				$div_retenciones .=  '<tr>
																<td style="font-weight:bold">'.$arrayRetenciones["retencion"].' ('.$arrayRetenciones["valor"].' %)</td>
																<td>'.$_SESSION["SIMBOLOMONEDA"].' '.number_format ($roundRetencion, $_SESSION['DECIMALESMONEDA']).'</td>
															</tr>';
			}
		}

		//=============================// ANTICIPO //=============================//
		//************************************************************************//
		$div_anticipo = '';

		$sqlAnticipos   = "SELECT SUM(valor) AS valorAnticipos FROM anticipos WHERE id_documento='$id' AND activo=1 AND tipo_documento='FV' AND id_empresa='$id_empresa'";
		$queryAnticipos = mysql_query($sqlAnticipos,$link);
		$totalAnticipo  = mysql_result($queryAnticipos, 0, 'valorAnticipos');

		if($totalAnticipo > 0){
			$totalAnticipo *= 1;

			$div_anticipo .= '<tr>
													<td style="font-weight:bold">ANTICIPO</td>
													<td>'.$_SESSION["SIMBOLOMONEDA"].' '.number_format($totalAnticipo).'</td>
												</tr>';
		}

		$totalFactura = ($subTotalFactura - $valorRetencion)+$ivaFactura;
		if($totalAnticipo > 0){ $totalFactura -= $totalAnticipo; }

		$arrayReplaceString = array("\n", "\r");
		$observacion = str_replace($arrayReplaceString, "<br/>", $observacion );


		//==============================// TERCERO //=============================//
		//************************************************************************//
		$sqlDireccion      = "SELECT direccion,ciudad,telefono1 FROM terceros_direcciones WHERE id = '$id_sucursal_cliente' AND activo = 1 LIMIT 0,1";
		$queryDireccion    = mysql_query($sqlDireccion,$link);
		$ciudad_cliente    = mysql_result($queryDireccion,0,'ciudad');
		$direccion_cliente = mysql_result($queryDireccion,0,'direccion');
		$telefono_cliente  = mysql_result($queryDireccion,0,'telefono1');

		//=========================// FACTURA DE COMPRA //========================//
		//************************************************************************//
		$contenido = '<style>
							.StyleTableCabecera{
								font-size		:12px;
								font-family		:"Segoe UI Light","Helvetica Neue Light","Segoe UI","Helvetica Neue","Trebuchet MS",Helvetica,"Droid Sans",Tahoma,Geneva,sans-serif;
							}
							.StyleTableArticulosTitulo{
								font-size		:11px;
								font-family		:"Segoe UI Light","Helvetica Neue Light","Segoe UI","Helvetica Neue","Trebuchet MS",Helvetica,"Droid Sans",Tahoma,Geneva,sans-serif;
								background-color:#000;
								color			:#FFF;
							}
							.StyleTableArticulos{
								font-size		:11px;
								font-family		:"Segoe UI Light","Helvetica Neue Light","Segoe UI","Helvetica Neue","Trebuchet MS",Helvetica,"Droid Sans",Tahoma,Geneva,sans-serif;
							}
							.StyleTableValores{
								font-size		:11px;
								font-family		:"Segoe UI Light","Helvetica Neue Light","Segoe UI","Helvetica Neue","Trebuchet MS",Helvetica,"Droid Sans",Tahoma,Geneva,sans-serif;
							}
							.StyleTableValoresContent{
								border-top		:1px solid #000;
							}
							.StyleTableObservaciones{
								font-size		:12px;
								font-family		:"Segoe UI Light","Helvetica Neue Light","Segoe UI","Helvetica Neue","Trebuchet MS",Helvetica,"Droid Sans",Tahoma,Geneva,sans-serif;
								border			:1px solid #000;
							}

						</style>

						<table class="StyleTableCabecera" width="740" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td width="88" style="font-weight:bold">Empresa:</td>
								<td width="394">'.$cliente.'</td>
								<td width="144" style="font-weight:bold">Fecha de Emision:</td>
								<td width="114"><span style="background-color: rgb(255, 0, 0);">[FV_FECHA_INICIO]</span></td>
							</tr>
							<tr>
								<td style="font-weight:bold">'.$tipo_nit.':</td>
								<td>'.$nitEmpresa.'</td>
								<td style="font-weight:bold">Fecha de Vencimiento:</td>
								<td><span style="background-color: rgb(255, 0, 0);">[FV_FECHA_VENCIMIENTO]</span></td>
							</tr>
							<tr>
								<td style="font-weight:bold">Direccion:</td>
								<td>'.$direccion_cliente.'</td>
								<td style="font-weight:bold">Orden de Compra:</td>
								<td>'.$orden_compra.'</td>
							</tr>
							<tr>
								<td style="font-weight:bold">Telefono:</td>
								<td>'.$telefono_cliente.'</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<td style="font-weight:bold">Ciudad:</td>
								<td>'.$ciudad_cliente.'</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
						</table>
						<table class="StyleTableArticulosTitulo" width="740" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td width="80">CODIGO</td>
								<td width="280">ITEM</td>
								<td width="80" align="right">UNIDAD</td>
								<td width="70" align="right">CANTIDAD</td>
								<td width="80" align="right">COSTO UNIT.</td>
								<td width="70" align="right">DESCUENTO</td>
								<td width="80" align="right">SUBTOTAL</td>
							</tr>
						</table>

						'.$articulos.'

						<table width="740" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td height="20">&nbsp;</td>
							</tr>
						</table>

						<table class="StyleTableValoresContent" width="740" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td width="370">
									<table class="StyleTableValores" width="370" border="0" cellspacing="0" cellpadding="0">
										<tr>
											<td width="270" style="font-weight:bold">SUBTOTAL</td>
											<td width="100">'.$_SESSION["SIMBOLOMONEDA"].' '.number_format($subTotalFactura,$_SESSION['DECIMALESMONEDA']).'</td>
										</tr>
										'.$div_iva.'
										'.$div_retenciones.'
										'.$div_anticipo.'
									</table>
								</td>
								<td width="370">
									<table class="StyleTableValores" width="370" border="0" cellspacing="0" cellpadding="0">
										<tr>
											<td width="100" style="font-weight:bold">NETO A PAGAR</td>
											<td width="260" style="font-size:16px">'.$_SESSION["SIMBOLOMONEDA"].' '.number_format($totalFactura,$_SESSION['DECIMALESMONEDA']).'</td>
										</tr>
										<tr>
											<td width="100" valign="top" style="font-weight:bold">EN LETRAS</td>
											<!--<td width="260" valign="top">'.utf8_decode(num2letras(round($totalFactura,$_SESSION['DECIMALESMONEDA']))).'</td>-->
											<td width="260" valign="top">'.utf8_decode(num2letras(str_replace(',', '', number_format($totalFactura,$_SESSION['DECIMALESMONEDA'])))) .'</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>

						<table width="740" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td height="20">&nbsp;</td>
							</tr>
						</table>

						<table class="StyleTableObservaciones" width="740" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td width="80"><b>Observaciones: </b>'.$observacion.'</td>
							</tr>
						</table>';

		$sqlConfig   = "SELECT COUNT(id) AS contConfig, id FROM configuracion_documentos_erp WHERE id_empresa = '$id_empresa' AND id_sucursal = '$id_sucursal' AND tipo='FV'";
		$queryConfig = mysql_query($sqlConfig,$link);
		$contConfig  = mysql_result($queryConfig, 0, 'contConfig');

		if($contConfig == 0){
			$sqlInsert   = "INSERT INTO configuracion_documentos_erp (nombre,tipo,id_empresa,id_sucursal) VALUES ('Factura de venta','FV','$id_empresa','$id_sucursal')";
			$queryInsert = mysql_query($sqlInsert,$link);
		}
	}

	$formato = cargaFormatoDocumento($id_empresa,$id_sucursal,'FV');
	$texto   = reemplazarVariables($formato,$contenido,$id_empresa,$id_sucursal,$id,$id_resolucion,'factura');

	// echo $texto; exit;

	// VALIDACION MARCA DE AGUA FACTURACION ELECTRONICA
	$sql="SELECT
          VFC.tipo
        FROM
          ventas_facturas_configuracion AS VFC
        LEFT JOIN
          ventas_facturas  AS VF
        ON 
          VF.id_configuracion_resolucion = VFC.id 
        WHERE
          VFC.activo = 1
        AND
          VFC.id_empresa = $id_empresa
        AND
          VF.id = $id";
	$query  = $mysql->query($sql,$mysql->link);
	$tipoFE = $mysql->result($query,0,'tipo');

	//////////////////////////////////////////////////////////////////////////////

	if(isset($TAM)){ $HOJA = $TAM; }
	else{ $HOJA = 'LETTER'; }

	if(!isset($ORIENTACION)){ $ORIENTACION = 'P'; }
	if(!isset($PDF_GUARDA)){ $PDF_GUARDA = false; }
	if(!isset($IMPRIME_PDF)){ $IMPRIME_PDF = 'true'; }

	if(isset($MARGENES)){ list($MT,$MR,$ML,$MB)= explode(',', $MARGENES ); }
	else{ $MT=50; $ML=10; $MR=10; $MB=10; }

	if(!isset($TAMANO_ENCA)){ $TAMANO_ENCA = 12; }

	if($IMPRIME_PDF=="true"){
		include("../../../../misc/MPDF54/mpdf.php");
		$mpdf = new mPDF(
			'utf-8',   			// mode - default ''
			$HOJA,					// format - A4, for example, default ''
			12,							// font size - default 0
			'',							// default font family
			$ML,						// margin left
			$MR,						// margin right
			$MT,						// margin top
			$MB,						// margin bottom
			2,							// margin header
			2,							// margin footer
			$ORIENTACION		// L - landscape, P - portrait
		);

		// $mpdf->SetProtection(array('print'));
		if($marcaAgua == 'true'){
			$mpdf->SetWatermarkText('ANULADO');
			$mpdf->showWatermarkText = true;
		}

		if($tipoFE == "FE"){
			$mpdf->SetWatermarkText('NO VALIDA COMO FACTURA');
			$mpdf->showWatermarkText = true;
		}

		$mpdf->SetAutoPageBreak(TRUE, 15);
		$mpdf->SetTitle($documento);
		$mpdf->SetAuthor($_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA']);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetHeader("");
		// $mpdf->WriteHTML(utf8_encode($texto));
		$mpdf->WriteHTML(utf8_encode($texto));

		if($PDF_GUARDA=="F"){   	///OUTPUT A ARCHIVO
			$serv = $_SERVER['DOCUMENT_ROOT']."/";
			$url  = $serv.'ARCHIVOS_PROPIOS/adjuntos_ventas/facturas_venta/';
			if(!file_exists($url)){ mkdir ($url); }

			$url = $url.'empresa_'.$_SESSION['ID_HOST'].'/';
			if(!file_exists($url)){ mkdir ($url); }

			$mpdf->Output($url."factura_venta_".$id.".pdf",'F');  	///OUTPUT A ARCHIVO
		}
		else{ $mpdf->Output($documento.".pdf",'I'); } 		///OUTPUT A VISTA

		exit;
	}
	else{
		echo $texto;
	}
?>
