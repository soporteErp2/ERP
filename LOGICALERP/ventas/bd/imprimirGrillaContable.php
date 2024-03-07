<?php

	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("functions_imprimir.php");

	if($PDF_GUARDA == 'F'){ include("../config_var_global.php"); }

	//ESTE CONDICIONAL CONTROLA LA IMPRESION DE ACUERDO AL DOCUMENTO

	if ($opcGrillaContable=='RemisionesVenta') {
		$documento         = 'Remision';
		$titulo            = 'REMISION DE VENTA';
		$consecutivo       = $row['consecutivo'];
		$tipo_documento    = 'RV';
		$dir_adjunto       = 'remisiones';
		$whereIngredientes = " AND id_fila_item_receta = 0 ";
	}
	elseif ($opcGrillaContable=='PedidoVenta') {
		$documento      = 'Pedido';
		$titulo         = 'PEDIDO DE VENTA';
		$consecutivo    = $row['consecutivo'];
		$tipo_documento = 'PV';
		$dir_adjunto    = 'pedidos';
	}
	else{
		$documento      = 'Cotizacion';
		$titulo         ='COTIZACION DE VENTA';
		$consecutivo    = $row['consecutivo'];
		$tipo_documento = 'CV';
		$dir_adjunto    = 'cotizaciones';
	}

	if (!isset($_SESSION['EMPRESA']) || $_SESSION['EMPRESA']==''){ exit; }

	if($tipo_documento == "RV" && file_exists("../../../ARCHIVOS_PROPIOS/empresa_".$_SESSION['ID_HOST']."/panel_de_control/formato_documentos/formato_remision.php")){
		include("../../../ARCHIVOS_PROPIOS/empresa_".$_SESSION['ID_HOST']."/panel_de_control/formato_documentos/formato_remision.php");
	}
	else if($tipo_documento == "CV" && file_exists("../../../ARCHIVOS_PROPIOS/empresa_".$_SESSION['ID_HOST']."/panel_de_control/formato_documentos/formato_cotizacion.php")){
		include("../../../ARCHIVOS_PROPIOS/empresa_".$_SESSION['ID_HOST']."/panel_de_control/formato_documentos/formato_cotizacion.php");
	}
	else{

		$id_empresa         = $_SESSION['EMPRESA'];
		$subTotalDocumento  = 0.00;
		$DescuentoDocumento = 0.00;
		$ivaDocumento       = 0.00;
		$valorRetencion     = 0.00;
		$totalDocumento     = 0.00;

		$sql   = "SELECT T.id,T.exento_iva,T.dv,T.tipo_identificacion AS tipo_nit FROM terceros AS T, $tablaPrincipal AS DV WHERE T.id=DV.id_cliente AND DV.id=$id";
		$query = mysql_query($sql,$link);
		$dv         = mysql_result($query,0,'dv');
		$exento_iva = mysql_result($query,0,'exento_iva');
		$tipo_nit   = mysql_result($query,0,'tipo_nit');

		$tipo_nit   = ($tipo_nit == '')? "NIT": $tipo_nit;

		$SQL    = "SELECT * FROM $tablaPrincipal WHERE id='$id' AND activo=1 AND id_empresa='$id_empresa'";
		$consul = mysql_query($SQL,$link);

		if (!$consul){die('no valido informe'.mysql_error());}
		while($row = mysql_fetch_array($consul)){

			$nitEmpresa  = ($dv > 0)? $row["nit"].'-'.$dv: $row["nit"];
			$id_sucursal = $row['id_sucursal'];

			if ($row['estado']==0) { echo '<center><h2><i>Documento no Generado</i></h2></center>'; exit; }
	        $marcaAgua = ($row['estado']==3)? 'true': 'false';

			$labelConsecutivo='Consecutivo No.';

			// ASIGNAMOS A UNA VARIABLE EL TITULO
			$estilo = 'background-color: #DFDFDF;';

			$sqlArticulos   = "SELECT *, SUM(cantidad) AS cantidad_total,COUNT(id) AS cant_filas
								FROM $tablaInventario WHERE $idTablaPrincipal='$id' $whereIngredientes
								GROUP BY id_inventario,
									observaciones,
									tipo_descuento,
									descuento,
									costo_unitario";
			$queryArticulos = mysql_query($sqlArticulos,$link);

			while ($array= mysql_fetch_array($queryArticulos)) {
				$estilo = ($estilo != '')? '': 'background-color: #DFDFDF;';

				// consultamos la unidad del articulo
				$sqlUnidad     = 'SELECT inventario_unidades.nombre  FROM inventario_unidades INNER JOIN items ON inventario_unidades.id=items.id_unidad_medida WHERE items.id="'.$array["id_inventario"].'"';
				$queryUnidad   = mysql_query($sqlUnidad,$link);
				$unidad_nombre = mysql_result($queryUnidad,0,'nombre');

				$styleBorder   = ($array["observaciones"] == '')? 'border-bottom:1px solid #000;': 'border-bottom:1px dotted #000;';
				$descuento     = ($array["tipo_descuento"] == 'porcentaje')? $array["cantidad_total"] * $array["costo_unitario"]  * $array["descuento"] / 100 : $array["descuento"]*$array['cant_filas'];
				$subTotal      = ($array["cantidad_total"] * $array["costo_unitario"]) - $descuento;
				$tipoDescuento = ($array["tipo_descuento"]=='porcentaje')? '%': '$';

				if ($array["descuento"]==0 || $array["descuento"]==0.00){ $tipoDescuento = ''; }

				$articulos .= '<table class="StyleTableArticulos" width="740" border="0" cellspacing="0" cellpadding="0">
								<tr>
									<td style="width: 55px;">'.$array["codigo"].'</td>
									<td style="width: 305px; text-align:left; padding-left:5px;">'.$array["nombre"].'</td>
									<td style="width: 80px; text-align:left; padding-left:5px;">'.$array["nombre_unidad_medida"].' x '.$array["cantidad_unidad_medida"].'</td>
									<td style="width: 60px; text-align:right;">'.$array["cantidad_total"].'</td>
									<td style="width: 80px; text-align:right;">'.$array["descuento"].' '.$tipoDescuento.'</td>
									<td style="width: 80px; text-align:right;">'.number_format ($array["costo_unitario"],$_SESSION['DECIMALESMONEDA']).'</td>
								 	<td style="width: 80px; text-align:right;">'.number_format ($subTotal,$_SESSION['DECIMALESMONEDA']).'</td>
								</tr>';

				if($array["observaciones"] != ''){
					$articulos .= '<tr>
										<td style="width:55px;">&nbsp;</td>
										<td style="width:685px;padding-left:5px;" colspan="6">'.str_replace("\n",'<br>',$array["observaciones"]).'</td>
									</tr>';
				}

				$articulos .= '</table>';

				//CALCULO DEL IVA POR ARTICULO
				$iva = ($exento_iva=='Si')? 0 :
							($array["valor_impuesto"] > 0)? ROUND($array["valor_impuesto"] * $subTotal / 100, $_SESSION['DECIMALESMONEDA']): 0;

				$arrayIvaDocumento[$array['id_impuesto']] += $iva;
				$arrayDatosIva[$array['id_impuesto']]      = array('nombre' => $array['impuesto'],'valor_impuesto'=> $array['valor_impuesto']*1, );

				$subTotalDocumento  = $subTotal + $subTotalDocumento;			//SUBTOTAL FACTURA
				$ivaDocumento       = $ivaDocumento + $iva;						//IVA FACTURA
				$DescuentoDocumento = $DescuentoDocumento + $descuento;			//DESCUENTO FACTURA
			}

			//=================================// VALOR DEL IVA SOBRE LA FACTURA //=================================//
			//******************************************************************************************************//
			$totalDocumento = ($subTotalDocumento)+$ivaDocumento;
			$contenido_iva  = '';
			foreach ($arrayIvaDocumento as $id_impuesto => $valor_impuesto) {
				if ($valor_impuesto > 0) {
					$contenido_iva .= '<tr>
											<td style="font-weight:bold;">'.$arrayDatosIva[$id_impuesto]['nombre'].' ('.$arrayDatosIva[$id_impuesto]['valor_impuesto'].'%)</td>
											<td>'.$_SESSION["SIMBOLOMONEDA"].' '.number_format ($valor_impuesto,$_SESSION['DECIMALESMONEDA']).'</td>
										</tr>';
				}
			}

			$arrayReplaceString = array("\n", "\r");
			$row['observacion'] = str_replace($arrayReplaceString, "<br/>", $row['observacion'] );

			//========================================== CONSULTAMOS LOS DATOS DEL TERCERO =======================================//

			$sqlCliente   = "SELECT direccion,telefono1,ciudad FROM terceros WHERE id_empresa ='$id_empresa' AND id=".$row["id_cliente"];
			$queryCliente = mysql_query($sqlCliente,$link);

			$direccion = mysql_result($queryCliente,0,'direccion');
			$telefono  = mysql_result($queryCliente,0,'telefono1');
			$ciudad    = mysql_result($queryCliente,0,'ciudad');

			//======================================= ARMAMOS EL DOCUMENTO =============================================//
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
									<td width="394">'.$row["cliente"].'</td>
									<td width="144" style="font-weight:bold">Fecha de Emision:</td>
									<td width="114">'.$row['fecha_inicio'].'</td>
								</tr>
								<tr>
									<td style="font-weight:bold">'.$tipo_nit.':</td>
									<td>'.$nitEmpresa.'</td>
									<td style="font-weight:bold">Fecha de Vencimiento:</td>
									<td>'.$row['fecha_finalizacion'].'</td>
								</tr>
								<tr>
									<td style="font-weight:bold">Direccion:</td>
									<td>'.$direccion.'</td>
									<td style="font-weight:bold"></td>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td style="font-weight:bold">Telefono:</td>
									<td>'.$telefono.'</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td style="font-weight:bold">Ciudad:</td>
									<td>'.$ciudad.'</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
								</tr>
							</table>

							<table class="StyleTableArticulosTitulo" width="740" border="0" cellspacing="0" cellpadding="0">
								<tr>
									<td style="width:65px;">CODIGO</td>
									<td style="width:295px;padding-left:5px;">ITEM</td>
									<td style="width:80px; text-align:center;">UNIDAD</td>
									<td style="width:60px; text-align:center;">CANTIDAD</td>
									<td style="width:80px; text-align:right;">DESCUENTO</td>
									<td style="width:80px; text-align:center;">UNITARIO</td>
									<td style="width:80px; border-right: 1px solid; text-align:center;">SUBTOTAL</td>
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
	                                            <td width="100">'.$_SESSION["SIMBOLOMONEDA"].' '.number_format ($subTotalDocumento,$_SESSION['DECIMALESMONEDA']).'</td>
	                                        </tr>
											'.$contenido_iva.'
	                                    </table>
	                                </td>
	                                <td width="370">
	                                	<table class="StyleTableValores" width="370" border="0" cellspacing="0" cellpadding="0">
	                                        <tr>
	                                            <td width="100" style="font-weight:bold">NETO A PAGAR</td>
	                                            <td width="270" style="font-size:16px">'.$_SESSION["SIMBOLOMONEDA"].' '.number_format($totalDocumento,$_SESSION['DECIMALESMONEDA']).'</td>
	                                        </tr>
	                                        <tr>
	                                            <td valign="top" width="100"  style="font-weight:bold">EN LETRAS</td>
	                                            <td valign="top" width="270" >'.utf8_decode(num2letras(ROUND($totalDocumento,$_SESSION['DECIMALESMONEDA']))).'</td>
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
									<td width="80"><b>Observaciones: </b>'.$row['observacion'].'</td>
								</tr>
							</table>';
		}

		$sqlConfig   = "SELECT COUNT(id) AS contConfig, id FROM configuracion_documentos_erp WHERE id_empresa = '$id_empresa' AND id_sucursal = '$id_sucursal' AND tipo='$tipo_documento' AND activo=1";
		$queryConfig = mysql_query($sqlConfig,$link);
		$contConfig  = mysql_result($queryConfig, 0, 'contConfig');

		if($contConfig == 0){

			$sqlConfig    = "SELECT COUNT(id) AS contConfig, id, texto, nombre FROM configuracion_documentos_erp WHERE id_empresa = '$id_empresa' AND tipo='$tipo_documento' AND activo=1 LIMIT 0,1";
			$queryConfig  = mysql_query($sqlConfig,$link);
			$contConfig   = mysql_result($queryConfig, 0, 'contConfig');
			$nombreConfig = mysql_result($queryConfig, 0, 'nombre');
			$textoConfig  = mysql_result($queryConfig, 0, 'texto');

			if($contConfig > 0){

				$sqlInsert   = "INSERT INTO configuracion_documentos_erp (nombre,tipo,id_empresa,id_sucursal,texto) VALUES ('$nombreConfig','$tipo_documento','$id_empresa','$id_sucursal','$textoConfig')";
				$queryInsert = mysql_query($sqlInsert,$link);
			}
			else{
				$sqlConfig    = "SELECT COUNT(id) AS contConfig, id, texto, nombre FROM configuracion_documentos_erp WHERE id_empresa = '0' AND tipo='$tipo_documento' AND activo=1";
				$queryConfig  = mysql_query($sqlConfig,$link);
				$contConfig   = mysql_result($queryConfig, 0, 'contConfig');
				$nombreConfig = mysql_result($queryConfig, 0, 'nombre');
				$textoConfig  = mysql_result($queryConfig, 0, 'texto');

				if ($contConfig > 0) {
					$sqlInsert   = "INSERT INTO configuracion_documentos_erp (nombre,tipo,id_empresa,id_sucursal,texto) VALUES ('$nombreConfig','$tipo_documento','$id_empresa','$id_sucursal','$textoConfig')";
					$queryInsert = mysql_query($sqlInsert,$link);
				}
			}
		}

		$textoPlantilla = cargaFormatoDocumento($id_empresa,$id_sucursal,"$tipo_documento");
		$texto          = reemplazarVariables($textoPlantilla,$contenido,$id_empresa,$id_sucursal,$id);

		if(isset($TAM)){ $HOJA = $TAM; }
		else{ $HOJA = 'LETTER'; }

		if(!isset($ORIENTACION)){ $ORIENTACION = 'P'; }
		if(!isset($PDF_GUARDA)){ $PDF_GUARDA = false; }
		if(!isset($IMPRIME_PDF)){ $IMPRIME_PDF = 'true'; }

		if(isset($MARGENES)){ list($MS, $MD, $MI, $ML) = split( ',', $MARGENES ); }
		else{ $M[0] = 50; $M[1] = 10; $M[2] = 10; $M[3] = 10; }

		if(!isset($TAMANO_ENCA)){ $TAMANO_ENCA = 12; }

		if($IMPRIME_PDF){
			include("../../../misc/MPDF54/mpdf.php");
			$mpdf = new mPDF(
				'utf-8',   					// mode - default ''
				$HOJA,						// format - A4, for example, default ''
				12,							// font size - default 0
				'',							// default font family
				$M[2],						// margin_left
				$M[1],						// margin right
				$M[0],						// margin top
				$M[3],						// margin bottom
				2,							// margin header
				2,							// margin footer
				$ORIENTACION				// L - landscape, P - portrait
			);

			/*/////// MARCA DE AGUA
			$mpdf->SetWatermarkText('COPIA');
			$mpdf->watermark_font = 'DejaVuSansCondensed';
			$mpdf->showWatermarkText = true;
			*/

			$mpdf->SetProtection(array('print'));
			if($marcaAgua=='true'){
				$mpdf->SetWatermarkText('ANULADO');
				$mpdf->showWatermarkText = true;
			}
			$mpdf->SetAutoPageBreak(TRUE, 15);
			$mpdf->SetTitle( $documento );
			$mpdf->SetAuthor( $_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA'] );
			$mpdf->SetDisplayMode ( 'fullpage' );
			$mpdf->SetHeader("");
			$mpdf->WriteHTML(utf8_encode($texto));

			if($PDF_GUARDA=="F"){
				$serv = $_SERVER['DOCUMENT_ROOT']."/";
			    $url  = $serv.'ARCHIVOS_PROPIOS/adjuntos_ventas/'.$dir_adjunto.'/';
			    if(!file_exists($url)){ mkdir ($url); }

			    $url = $url.'empresa_'.$_SESSION['ID_HOST'].'/';
	    		if(!file_exists($url)){ mkdir ($url); }

				$mpdf->Output($url.$documento.'_'.$id.".pdf",'F');   	///OUTPUT A ARCHIVO
			}else{ $mpdf->Output($documento.".pdf",'I'); }		///OUTPUT A VISTA

			exit;
		}
		else{ echo $texto; }
	}
?>
