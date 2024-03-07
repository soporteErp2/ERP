<?php

	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../../bd/functions_imprimir.php");

	if (!isset($_SESSION['EMPRESA']) || $_SESSION['EMPRESA']==''){ exit; }

	$id_empresa       = $_SESSION['EMPRESA'];
	$documento        = 'Factura';
	$estilo           = 'background-color: #DFDFDF;';
	$subTotalFactura  = 0.00;
	$acumuDescFactura = 0.00;
	$ivaFactura       = 0.00;
	$valorRetencion   = 0.00;
	$totalFactura     = 0.00;

	$sql   = "SELECT T.id,T.exento_iva,T.dv,T.tipo_identificacion AS tipo_nit FROM terceros AS T, $tablaPrincipal AS DV WHERE T.id=DV.id_cliente AND DV.id=$id";
	$query = mysql_query($sql,$link);
	$dv         = mysql_result($query,0,'dv');
	$exento_iva = mysql_result($query,0,'exento_iva');
	$tipo_nit   = mysql_result($query,0,'tipo_nit');

	$tipo_nit   = ($tipo_nit == '')? "NIT": $tipo_nit;

	$SQL    = "SELECT * FROM $tablaPrincipal WHERE id='$id' AND activo=1 AND id_empresa='$id_empresa'";
	$consul = mysql_query($SQL,$link);

	if (!$consul){ die('no valido informe'.mysql_error()); }
	while($row = mysql_fetch_array($consul)){

		$nitEmpresa          = ($dv > 0)? $row["nit"].'-'.$dv: $row["nit"];
		$id_sucursal         = $row['id_sucursal'];
		$id_sucursal_cliente = $row['id_sucursal_cliente'];

		if ($row['estado']==0) { echo '<center><h2><i>Documento no Generado</i></h2></center>'; exit; } //DOUMENTO NO GENERADO
		else if ($row['estado']==3) { echo '<center><h2><i>Documento Cancelado</i></h2></center>'; exit; }	//DOCUMENTO CANCELADO

		$labelConsecutivo = 'Consecutivo Nro';
		$titulo           = 'FACTURA DE VENTA';
		$labelConsecutivo = 'NUMERO';
		$consecutivo      = '<font style="font-size:18;">'.$row['prefijo']." ".$row['numero_factura'].'</font>';

		$sqlArticulos   = "SELECT *, SUM(cantidad) AS cantidad_total
							FROM $tablaInventario
							WHERE $idTablaPrincipal='$id'
							GROUP BY id_inventario,
								observaciones,
								tipo_descuento,
								descuento,
								costo_unitario";
		$queryArticulos = mysql_query($sqlArticulos,$link);

		while ($array= mysql_fetch_array($queryArticulos)) {
			$estilo = ($estilo!='')? '': 'background-color: #F2F2F2;';

			$array["descuento"]      = $array["descuento"] * 1;
			$array["cantidad_total"] = $array["cantidad_total"] * 1;
			$array["costo_unitario"] = $array["costo_unitario"] * 1;

			// consultamos la unidad del articulo
			$sqlUnidad     = 'SELECT inventario_unidades.nombre  FROM inventario_unidades INNER JOIN items ON inventario_unidades.id=items.id_unidad_medida WHERE items.id="'.$array["id_inventario"].'"';
			$queryUnidad   = mysql_query($sqlUnidad,$link);
			$unidad_nombre = mysql_result($queryUnidad,0,'nombre');

			$styleBorder   = ($array["observaciones"] == '')? 'border-bottom:1px solid #000;': 'border-bottom:1px dotted #000;';
			$descuento     = ($array["tipo_descuento"] == 'porcentaje')? ((($array["cantidad_total"] * $array["costo_unitario"]) * $array["descuento"]) / 100) : $array["descuento"];
			$subTotal      = ($array["cantidad_total"] * $array["costo_unitario"]) - $descuento;
			$tipoDescuento = ($array["tipo_descuento"]=='porcentaje')? ' %': ' $';

			if ($array["descuento"]==0){ $tipoDescuento=''; }

			$articulos.='<table class="StyleTableArticulos" width="740" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td valign="top" width="80">'.$array["codigo"].'</td>
								<td valign="top" width="280"><b>'.$array["nombre"].'</b></td>
								<td valign="top" width="80" align="right">'.$array["nombre_unidad_medida"].' x '.$array["cantidad_unidad_medida"].'</td>
								<td valign="top" width="70" align="right">'.$array["cantidad_total"].'</td>
								<td valign="top" width="70" align="right">'.$array["descuento"].$tipoDescuento.'</td>
								<td valign="top" width="80" align="right">'.number_format($array["costo_unitario"],$_SESSION['DECIMALESMONEDA']).'</td>
								<td valign="top" width="80" align="right">'.number_format($subTotal,$_SESSION['DECIMALESMONEDA']).'</td>
							</tr>';

			if($array["observaciones"] != ''){

            	$articulos .= '<tr>
									<td>&nbsp;</td>
									<td colspan="6" style="font-size:9px;">'.str_replace("\n",'<br>',$array["observaciones"]).'</td>
								</tr>';
			}
			$articulos .= '</table>';

			//CALCULO DEL IVA POR ARTICULO
			$iva = ($exento_iva=='Si')? 0 :
						($array["valor_impuesto"] > 0)? ROUND($array["valor_impuesto"] * $subTotal / 100, $_SESSION['DECIMALESMONEDA']): 0;

			$arrayIvaDocumento[$array['id_impuesto']] += $iva;
			$arrayDatosIva[$array['id_impuesto']]      = array('nombre' => $array['impuesto'],'valor_impuesto'=> $array['valor_impuesto']*1);

			$subTotalFactura  = $subTotal+$subTotalFactura;
			$ivaFactura       = $ivaFactura+$iva;
			$acumuDescFactura = $acumuDescFactura+$descuento;
		}

		//=================================// VALOR DEL IVA SOBRE LA FACTURA //=================================//
		//******************************************************************************************************//
		$contenido_iva = '';
		foreach ($arrayIvaDocumento as $id_impuesto => $valor_impuesto) {
			if($valor_impuesto>0){
				$contenido_iva .= '<tr>
										<td style="font-weight:bold">'.$arrayDatosIva[$id_impuesto]['nombre'].' ('.$arrayDatosIva[$id_impuesto]['valor_impuesto'].'%)</td>
										<td>'.$_SESSION["SIMBOLOMONEDA"].' '.number_format ($valor_impuesto,$_SESSION['DECIMALESMONEDA']).'</td>
                                    </tr>';
			}
		}

		//======================// CALCULAMOS EL VALOR DE LA RETENCION  SOBRE LA FACTURA //=====================//
		//******************************************************************************************************//
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
				$mostrarRetenciones .= '<tr>
                                            <td style="font-weight:bold">'.$arrayRetenciones["retencion"].' ('.$arrayRetenciones["valor"].' %)</td>
                                            <td>'.$_SESSION["SIMBOLOMONEDA"].' '.number_format ($roundRetencion, $_SESSION['DECIMALESMONEDA']).'</td>
                                        </tr>';
			}
			else if ($arrayRetenciones["tipo_retencion"] == 'AutoRetencion') { continue; }
			else{
				if ($arrayRetenciones['base'] > $subTotalFactura) { continue; }

				$roundRetencion = ROUND($subTotalFactura * $arrayRetenciones["valor"]/100,$_SESSION['DECIMALESMONEDA']);

				$valorRetencion += $roundRetencion;
				$mostrarRetenciones .= '<tr>
                                            <td style="font-weight:bold">'.$arrayRetenciones["retencion"].' ('.$arrayRetenciones["valor"].' %)</td>
                                            <td>'.$_SESSION["SIMBOLOMONEDA"].' '.number_format ($roundRetencion, $_SESSION['DECIMALESMONEDA']).'</td>
                                        </tr>';
			}
		}

		$totalFactura = ($subTotalFactura - $valorRetencion)+$ivaFactura;

		$arrayReplaceString = array("\n", "\r");
		$row['observacion'] = str_replace($arrayReplaceString, "<br/>", $row['observacion'] );

		//========================================== CONSULTAMOS LOS DATOS DEL TERCERO =======================================//
		// $sqlCliente   = "SELECT direccion,telefono1,ciudad FROM terceros WHERE id_empresa ='$id_empresa' AND id=".$row["id_cliente"];
		// $queryCliente = mysql_query($sqlCliente,$link);

		// $direccion_cliente = mysql_result($queryCliente,0,'direccion');
		// $telefono_cliente  = mysql_result($queryCliente,0,'telefono1');
		// $ciudad_cliente    = mysql_result($queryCliente,0,'ciudad');

		//======================================== CONSULTAMOS LA DIRECCION DEL TERCERO =====================================//
		$sqlDireccion      = "SELECT direccion,ciudad,telefono1 FROM terceros_direcciones WHERE id='$id_sucursal_cliente' AND activo=1 LIMIT 0,1";
		$queryDireccion    = mysql_query($sqlDireccion,$link);
		$ciudad_cliente    = mysql_result($queryDireccion, 0, 'ciudad');
		$direccion_cliente = mysql_result($queryDireccion, 0, 'direccion');
		$telefono_cliente  = mysql_result($queryDireccion, 0, 'telefono1');

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
								<td>&nbsp;</td>
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
								<td width="70" align="right">DESCUENTO</td>
								<td width="80" align="right">UNITARIO</td>
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
                                            <td width="100">'.$_SESSION["SIMBOLOMONEDA"].' '.number_format ($subTotalFactura,$_SESSION['DECIMALESMONEDA']).'</td>
                                        </tr>
										'.$contenido_iva.'
										'.$mostrarRetenciones.'
                                    </table>
                                </td>
                                <td width="370">
                                	<table class="StyleTableValores" width="370" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td width="100" style="font-weight:bold">NETO A PAGAR</td>
                                            <td width="270" style="font-size:16px">'.$_SESSION["SIMBOLOMONEDA"].' '.number_format($totalFactura,$_SESSION['DECIMALESMONEDA']).'</td>
                                        </tr>
                                        <tr>
                                            <td valign="top" style="font-weight:bold">EN LETRAS</td>
                                            <td valign="top">'.utf8_decode(num2letras(round($totalFactura,$_SESSION['DECIMALESMONEDA']))).'</td>
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


	$sqlConfig   = "SELECT COUNT(id) AS contConfig, id FROM configuracion_documentos_erp WHERE id_empresa = '$id_empresa' AND id_sucursal = '$id_sucursal' AND tipo='FV'";
	$queryConfig = mysql_query($sqlConfig,$link);
	$contConfig  = mysql_result($queryConfig, 0, 'contConfig');

	if($contConfig == 0){
		$sqlInsert   = "INSERT INTO configuracion_documentos_erp (nombre,tipo,id_empresa,id_sucursal)
						VALUES ('Factura de venta','FV','$id_empresa','$id_sucursal')";
		$queryInsert = mysql_query($sqlInsert,$link);
	}

	$textoPlantilla = cargaFormatoDocumento($id_empresa,$id_sucursal,'FV');
	//$arrayTexto     = reemplazarVariables($textoPlantilla,$contenido,$id_empresa,$id_sucursal,$id);
	//$header = $arrayTexto[0];
	//$texto  = $arrayTexto[1];
	$texto     = reemplazarVariables($textoPlantilla,$contenido,$id_empresa,$id_sucursal,$id);

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if(isset($TAM)){ $HOJA = $TAM; }
	else{ $HOJA = 'LETTER'; }

	if(!isset($ORIENTACION)){ $ORIENTACION = 'P'; }
	if(!isset($PDF_GUARDA)){ $PDF_GUARDA = false; }
	if(!isset($IMPRIME_PDF)){ $IMPRIME_PDF = 'true'; }

	if(isset($MARGENES)){ $M= explode(',', $MARGENES ); }
	else{ $M[0] = 50; $M[1] = 10; $M[2] = 10; $M[3] = 10; }

	if(!isset($TAMANO_ENCA)){ $TAMANO_ENCA = 12; }

	if($IMPRIME_PDF=="true"){
		include("../../../../misc/MPDF54/mpdf.php");
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

		$mpdf->SetProtection(array('print'));
		$mpdf->SetAutoPageBreak(TRUE, 15);
		$mpdf->SetTitle($documento);
		$mpdf->SetAuthor($_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA']);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetHeader("");
		$mpdf->WriteHTML(utf8_encode($texto));

		if($PDF_GUARDA){ $mpdf->Output($documento.".pdf",'D'); }    	///OUTPUT A ARCHIVO
		else{ $mpdf->Output($documento.".pdf",'I'); } 		///OUTPUT A VISTA

		exit;
	}
	else{ echo $texto; }

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

?>