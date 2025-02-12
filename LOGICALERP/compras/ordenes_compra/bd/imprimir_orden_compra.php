<?php
function generarPDF($id, $conexionBD){
	include($_SERVER['DOCUMENT_ROOT']."/ERP/LOGICALERP/compras/bd/functions_imprimir.php");

	if($_SESSION['EMPRESA'] == ''){ echo "USUARIO NO REGISTRADO"; return; }
	$id_empresa          = $_SESSION['EMPRESA'];
	$displayTablaTotales = "";
	$titulo              = 'ORDEN DE COMPRA';
	$consecutivo         = $row['consecutivo'];
	$tipo_documento      = 'OC';
	$tablaPrincipal      = 'compras_ordenes';
	$idTablaPrincipal    = 'id_orden_compra';
	$tablaInventario     = 'compras_ordenes_inventario';

	if(file_exists($_SERVER['DOCUMENT_ROOT']."/ERP/ARCHIVOS_PROPIOS/empresa_".$_SESSION['ID_HOST']."/panel_de_control/formato_documentos/formato_orden_compra.php")){
		include($_SERVER['DOCUMENT_ROOT']."/ERP/ARCHIVOS_PROPIOS/empresa_".$_SESSION['ID_HOST']."/panel_de_control/formato_documentos/formato_orden_compra.php");
	}
	else{

		$subtotalOC       = 0.00;
		$ivaOC            = 0.00;
		$retefuenteOC     = 0.00;
		$valorRetencion   = 0.00;
		$totalOC          = 0.00;

		$SQLMoneda        = "SELECT * FROM configuracion_moneda WHERE activo=1 AND id = $id_moneda";
		$consul_moneda    = mysql_query($SQLMoneda,$conexionBD);
		$simbolo_moneda   = mysql_result($consul_moneda,0,'simbolo');
		$decimales_moneda = mysql_result($consul_moneda,0,'decimales');
		$descripcion      = mysql_result($consul_moneda,0,'descripcion');

		if($descripcion == 'Euro'){
			$simbolo_moneda = '&#8364;';
		}

		//ALMACENAR EN LA ORDEN DE COMPRA LA MONEDA Y TASA DE CAMBIO PARA GENERAR PROXIMOS DOCUMENTOS

		$sql_orden_compra = "UPDATE $tablaPrincipal SET id_moneda = $id_moneda, tasa_cambio = $tasa_cambio WHERE id = $id";
		mysql_query($sql_orden_compra,$conexionBD);

		//CONSULTAR LA INFORMACION DE LA EMPRESA
		$sqlEmpresa   = "SELECT nombre,tipo_documento_nombre,documento, pais,ciudad,direccion,razon_social,telefono,celular,tipo_regimen FROM empresas WHERE id='$id_empresa' LIMIT 0,1";
		$queryEmpresa = mysql_query($sqlEmpresa,$conexionBD);

		//CONSULTAR EL LOGO O IMAGEN DE LA EMPRESA QUE SE CARGO DESDE EL PANEL DE CONTROL
		$sqlImagen   = "SELECT nombre,ext FROM configuracion_imagenes_documentos WHERE activo=1 AND id_empresa='$id_empresa'";
		$queryImagen = mysql_query($sqlImagen,$conexionBD);
		$nombre      = mysql_result($queryImagen,0,'nombre');
		$ext         = mysql_result($queryImagen,0,'ext');

		if ($nombre==''){ $imagen=' &nbsp;'; }
		else{ $imagen='<img src="../../../ARCHIVOS_PROPIOS/imagenes_empresas/empresa_'.$id_empresa."/logos/".$nombre.'.'.$ext.'" width="150px" height="100px" >'; }

		$nombre_empresa        = mysql_result($queryEmpresa,0,'nombre');
		$tipo_documento_nombre = mysql_result($queryEmpresa,0,'tipo_documento_nombre');
		$documento_empresa     = mysql_result($queryEmpresa,0,'documento');
		$ubicacion_empresa     = mysql_result($queryEmpresa,0,'ciudad').' - '.mysql_result($queryEmpresa,0,'pais');
		$direccion_empresa     = mysql_result($queryEmpresa,0,'direccion');
		$razon_social          = mysql_result($queryEmpresa,0,'razon_social');
		$telefonos             = mysql_result($queryEmpresa,0,'telefono').' - '.mysql_result($queryEmpresa,0,'celular');
		$tipo_regimen          = mysql_result($queryEmpresa,0,'tipo_regimen');

		//============================ QUERY NOMBRE PROVEEDOR ============================//
		$sqlNombreEmpresa = "SELECT nombre FROM empresas WHERE id = '$id_empresa'";
		$nombre_empresa   = mysql_result(mysql_query($sqlNombreEmpresa,$conexionBD),0,'nombre');

		//============================== QUERY ORDEN COMPRA ==============================//
		$SQLComprasOrdenes   = "SELECT O.*,
									T.direccion,
									T.telefono1,
									T.departamento,
									T.ciudad,
									T.tercero_tributario,
									IF(T.dv > 0, CONCAT(T.numero_identificacion,' - ',T.dv), T.numero_identificacion) AS nit_tercero
								FROM $tablaPrincipal AS O LEFT JOIN terceros AS T ON O.id_proveedor = T.id
								WHERE O.id='$id'
									AND O.activo=1
									AND O.id_empresa='$id_empresa'
								LIMIT 0,1";
		$queryComprasOrdenes = mysql_query($SQLComprasOrdenes,$conexionBD);

		$idOC              = mysql_result($queryComprasOrdenes,0,'id');
		$estadoOC          = mysql_result($queryComprasOrdenes,0,'estado');

		$tercero_dpto      = mysql_result($queryComprasOrdenes,0,'departamento');
		$tercero_ciudad    = mysql_result($queryComprasOrdenes,0,'ciudad');
		$tercero_regimen   = mysql_result($queryComprasOrdenes,0,'tercero_tributario');

		$tercero_nit       = mysql_result($queryComprasOrdenes,0,'nit_tercero');
		$tercero_telefono  = mysql_result($queryComprasOrdenes,0,'telefono1');
		$tercero_direccion = mysql_result($queryComprasOrdenes,0,'direccion');

		$id_sucursal       = mysql_result($queryComprasOrdenes,0,'id_sucursal');
		$consecutivo       = mysql_result($queryComprasOrdenes,0,'consecutivo');
		$proveedor         = mysql_result($queryComprasOrdenes,0,'proveedor');
		$sucursal          = mysql_result($queryComprasOrdenes,0,'sucursal');
		$bodega            = mysql_result($queryComprasOrdenes,0,'bodega');
		$fecha             = mysql_result($queryComprasOrdenes,0,'fecha_inicio');
		$fecha_vencimiento = mysql_result($queryComprasOrdenes,0,'fecha_vencimiento');
		$forma_pago        = mysql_result($queryComprasOrdenes,0,'forma_pago');

		$observaciones     = mysql_result($queryComprasOrdenes,0,'observacion');
		$autorizado        = mysql_result($queryComprasOrdenes,0,'autorizado');
		//SI NO EXISTE LA ORDEN DE COMPRA RETURN-->
		

		if(!$queryComprasOrdenes || $idOC == ''){ return; }
		if ($estadoOC==0) { echo '<center><h2><i>Documento no Generado</i></h2></center>'; exit; }
		$marcaAgua = 'false';
	    if($estadoOC==3){
	        $marcaAgua = 'true';
	    }

		$simbolo   = $_SESSION["SIMBOLOMONEDA"];
		$decimales = $_SESSION['DECIMALESMONEDA'];

		if($id_moneda > 1){
			$simbolo   = $simbolo_moneda;
			$decimales = $decimales_moneda;
	    }

		//======================= QUERY ARTICULOS ORDEN COMPRA =========================//
		$signoDescuento = '';
		$estilo         = 'background-color: #F2F2F2;';
		$sqlArticulos   = "SELECT *,
								SUM(cantidad) AS cantidad_total
							FROM $tablaInventario
							WHERE $idTablaPrincipal='$id'
								AND activo = 1
							GROUP BY id_inventario,observaciones,id_impuesto,tipo_descuento,descuento,costo_unitario";
		$queryArticulos = mysql_query($sqlArticulos,$conexionBD);



		while ($array = mysql_fetch_array($queryArticulos)) {

			//SE ELIMINAN ESTAS COLUMNAS SOLO EN LA REQUISICION

			$estilo         = ($estilo != '')? '': 'background-color: #F2F2F2;';
			$signoDescuento = ($array["tipo_descuento"]=='porcentaje')? '%': '$';

			//========================= INICIO DEL CALCULO DE LA ORDEN DE COMPRA ================================//
			$tipoDesc = $array["tipo_descuento"];

			//variables para los calculos
			$subtotal         = 0;
			// $subtotal1        = 0;
			$valorIva         = 0;
			$descuentoTotal   = 0;
			$descuentoMostrar = 0;
			$costo_unitario = ($id_moneda>1 && $tasa_cambio>0)? ROUND($array["costo_unitario"]/$tasa_cambio, $decimales): $array["costo_unitario"];

			//calculamos el subtotal por articulo
			$subtotal= $array["cantidad_total"]*$costo_unitario;


			if ($tipoDesc=='porcentaje') {
					$valorDescuento   =(($subtotal*$array["descuento"])/100);
					$descuentoMostrar =$array["descuento"];
					$tipodesart = '%';
			}
			else if($tipoDesc=='pesos' && $id_moneda > 1){
					$valorDescuento   =ROUND($array["descuento"]/$tasa_cambio, $decimales);
					$descuentoMostrar = $valorDescuento;
					$tipodesart       = $simbolo_moneda;
			}
			else if($tipoDesc=='pesos'){
					$valorDescuento   =$array["descuento"];
					$descuentoMostrar = $valorDescuento;
					$tipodesart       = '$';
			}


			$temp = $subtotal-$valorDescuento;

			//generamos el costo del iva para ese articulo

			$valorIva = 0;
			if($array["valor_impuesto"] > 0){ $valorIva = ROUND(($array["valor_impuesto"]*$temp/100), $decimales); }

			$subtotalOC += $temp;			// ACUMULADOR SUBTOTAL
			$ivaOC      += $valorIva;					// ACUMULADOR IVA

			$arrayIvaDocumento[$array['id_impuesto']]+=$valorIva;
			$arrayDatosIva[$array['id_impuesto']]= array('nombre' => $array['impuesto'],'valor_impuesto'=> $array['valor_impuesto']*1, );

			$costo_unitario = $array["costo_unitario"];

			if($id_moneda > 1){

				$costo_unitario = round($array["costo_unitario"] / $tasa_cambio,2);
				$simbolo = $simbolo_moneda;
			}

			$filasItems  = '<td valign="top" width="80" style="">'.$array["codigo"].'</td>
							<td valign="top" width="280" align="left">'.$array["nombre"].'</td>
							<td valign="top" width="80" align="left">'.$array["nombre_unidad_medida"].' x '.$array["cantidad_unidad_medida"].'</td>
							<td valign="top" width="70" align="right">'.$array["cantidad_total"].'</td>
							<td valign="top" width="70" align="right">'.$descuentoMostrar.' '.$tipodesart.'</td>
						    <td valign="top" width="80" align="right">'.number_format ($costo_unitario,$decimales).'</td>
						    <td valign="top" width="80" align="right">'.number_format ($temp,$decimales).'</td>';

			$articulos .= '<table class="StyleTableArticulos" width="740" border="0" cellspacing="0" cellpadding="0">
							<tr style="'.$estilo.'">
								'.$filasItems.'
							</tr>';

			if($array["observaciones"] != ''){
				$articulos .= '<tr style="'.$estilo.'">
									<td>&nbsp;</td>
									<td colspan="6" style="font-size:9px;" width="339">'.str_replace("\n",'<br>',$array["observaciones"]).'</td>
								</tr>';
			}
			$articulos .= '</table>';
		}

		//=================================// VALOR DEL IVA SOBRE LA FACTURA //=================================//
		//******************************************************************************************************//

		$contenido_iva = '';
		if($ivaOC > 0){
			foreach ($arrayIvaDocumento as $id_impuesto => $valor_impuesto) {
				if($valor_impuesto == 0) continue;
				$contenido_iva .= '<tr>
										<td style="font-weight:bold">'.$arrayDatosIva[$id_impuesto]['nombre'].' ('.$arrayDatosIva[$id_impuesto]['valor_impuesto'].'%)</td>
										<td>'.$simbolo.' '.number_format ($valor_impuesto,$decimales).'</td>
									</tr>';
			}
		}
		else{
			$contenido_iva .= '<tr>
									<td style="font-weight:bold">&nbsp;</td>
									<td>&nbsp;</td>
								</tr>';
		}

		//======================// CALCULAMOS EL VALOR DE LA RETENCION  SOBRE LA FACTURA //=====================//
		//******************************************************************************************************//
		$valorRetencion     = 0;
		$listadoRetenciones = '';
		$simboloRetencion   = '';
		$valoresRetenciones = '';

		$sqlRetenciones = "SELECT R.retencion, R.valor
							FROM retenciones AS R INNER JOIN compras_ordenes_retenciones AS COR ON R.id=COR.id_retencion
							WHERE COR.id_orden_compra='$id'
							GROUP BY R.id";

		$queryRetenciones = mysql_query($sqlRetenciones,$conexionBD);

		while ($arrayRetenciones=mysql_fetch_array($queryRetenciones)) {

			if ($arrayRetenciones["retencion"]=='reteiva') {
				$acum1 = ($ivaOC * $arrayRetenciones["valor"])/100;
				$valorRetencion += $acum1;

				$acum1_tc = $acum1;

				if($id_moneda > 1){ $acum1_tc =  $acum1 / $tasa_cambio; }

			    $mostrarRetenciones .= '<tr>
											<td style="font-weight:bold">'.$arrayRetenciones["retencion"].' ('.$arrayRetenciones["valor"].' %) </td>
											<td>'.$simbolo.' '.number_format ($acum1_tc,$decimales).'</td>
										</tr>';
			}
			else{

				$acum2 = ($subtotalOC * $arrayRetenciones["valor"])/100;

				$valorRetencion += $acum2;

				$acum2_tc = $acum2;

				if($id_moneda > 1){ $acum2_tc =  $acum2 / $tasa_cambio; }

				$mostrarRetenciones .= '<tr>
											<td style="font-weight:bold">'.$arrayRetenciones["retencion"].' ('.$arrayRetenciones["valor"].' %) </td>
											<td>'.$simbolo.' '.number_format ($acum2_tc,$decimales).'</td>
										</tr>';

			}
		}

		$totalOC = ($subtotalOC -$valorRetencion)+$ivaOC;

		//======================================= ARMA CABECERA INFORME ORDEN DE COMPRA ====================================//
		$msjEstado = '';
		if($estadoOC == 0){ $msjEstado = 'Sin generar'; }
		elseif($estadoOC == 1){ $msjEstado = 'Generada'; }
		elseif($estadoOC == 2){ $msjEstado = 'Facturada (Cerrada)'; }

		$contenido = '<div id="body_pdf" style="width:100%; font-style:normal; font-size:11px;">
						<div style="overflow: hidden; width:100%;margin-top:20px;">
							<div style="float:left; width:90%; margin:0px 5px 0px 10px">
								<div style="float:left; width:15%;"><b>Proveedor:</b></div>
								<div style="float:left; width:45%;">'.$proveedor.'</div>
								<div style="float:left; width:15%;"><b>Departamento:</b></div>
								<div style="float:left; width:25%;">'.$tercero_dpto.'</div>
							</div>

							<div style="float:left; width:90%; margin:0px 5px 0px 10px;">
								<div style="float:left; width:15%;"><b>Nit:</b></div>
								<div style="float:left; width:45%;">'.$tercero_nit.'</div>
								<div style="float:left; width:15%;"><b>Ciudad:</b></div>
								<div style="float:left; width:25%;">'.$tercero_ciudad.'</div>
							</div>

							<div style="float:left; width:90%; margin:0px 5px 0px 10px;">
								<div style="float:left; width:15%;"><b>Direccion:</b></div>
								<div style="float:left; width:45%;">'.$tercero_direccion.'</div>
								<div style="float:left; width:15%;"><b>Regimen:</b></div>
								<div style="float:left; width:25%;">'.$tercero_regimen.'</div>

							</div>

							<div style="float:left; width:90%; margin:0px 5px 0px 10px;">
								<div style="float:left; width:15%;"><b>Telefono:</b></div>
								<div style="float:left; width:45%;">'.$tercero_telefono.'</div>
								<div style="float:left; width:15%;"><b>Forma de Pago:</b></div>
								<div style="float:left; width:25%;">'.$forma_pago.'</div>
							</div>

						</div>
					</div>';

		$subtotalOC_tc = $subtotalOC;
		$totalOC_tc    = $totalOC;
		$texto_tc      = 'EN LETRAS';

	error_log($_SERVER['DOCUMENT_ROOT']. "Error con linea 333");
	  $numeros_en_letras = utf8_decode(num2letras(round($totalOC_tc,$decimales)));

		if($id_moneda > 1){
			$subtotalOC_tc     = $subtotalOC / $tasa_cambio;
			$totalOC_tc        = $totalOC_tc / $tasa_cambio;
			$numeros_en_letras = '';
			$texto_tc          = '';
		}

		//SE ELIMINAN ESTAS COLUMNAS SOLO EN LA REQUISICION

	    $cabeceraItems = '<td style="width:80px;">CODIGO</td>
						  <td style="width:280px;">ARTICULO</td>
						  <td style="width:80px;">UNIDAD</td>
						  <td style="width:70px;">CANTIDAD</td>
			              <td style="width:70px;">DESCUENTO</td>
						  <td style="width:80px;">UNITARIO</td>
						  <td style="width:80px;">SUBTOTAL</td>';

		$contenido .= '<table class="StyleTableArticulosTitulo" width="740" border="0" cellspacing="0" cellpadding="0">
							<tr>
								'.$cabeceraItems.'
							</tr>
						</table>
						'.$articulos.'
						<table width="740" border="0" cellspacing="0" cellpadding="0">
	                        <tr>
								<td height="20">&nbsp;</td>
							</tr>
						</table>
	                    <div style="'.$displayTablaTotales.'">
							<table class="StyleTableValoresContent" width="740" border="0" cellspacing="0" cellpadding="0">
								<tr>
		                            <td width="370">
		                            	<table class="StyleTableValores" width="370" border="0" cellspacing="0" cellpadding="0">
		                                    <tr>
		                                        <td width="270" style="font-weight:bold">SUBTOTAL</td>
		                                        <td width="100">'.$simbolo.' '.number_format ($subtotalOC,$decimales).'</td>
		                                    </tr>
											'.$contenido_iva.'
											'.$mostrarRetenciones.'
		                                </table>
		                            </td>
		                            <td width="370">
		                            	<table class="StyleTableValores" width="370" border="0" cellspacing="0" cellpadding="0">
		                                    <tr>
		                                        <td width="100" style="font-weight:bold">NETO A PAGAR</td>
		                                        <td width="270" style="font-size:16px">'.$simbolo.' '.number_format($totalOC,$decimales).'</td>
		                                    </tr>
		                                    <tr>
		                                        <td valign="top" width="100" style="font-weight:bold">'.$texto_tc.'</td>
		                                        <td valign="top" width="270">'.$numeros_en_letras .'</td>
		                                    </tr>
		                                </table>
		                            </td>
		                        </tr>
		                    </table>
		                 </div>
	                    <table width="740" border="0" cellspacing="0" cellpadding="0">
	                        <tr>
								<td height="20">&nbsp;</td>
							</tr>
						</table>

						<table class="StyleTableObservaciones" width="740" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td width="80"><b>Observaciones: </b><br>'.$observaciones.'</td>
							</tr>
						</table>

					<style>
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
						table{ border-collapse:collapse; }
					</style>';

		$sqlConfig   = "SELECT COUNT(id) AS contConfig, id FROM configuracion_documentos_erp WHERE id_empresa = '$id_empresa' AND id_sucursal = '$id_sucursal' AND tipo='$tipo_documento' AND activo=1";
		$queryConfig = mysql_query($sqlConfig,$conexionBD);
		$contConfig  = mysql_result($queryConfig, 0, 'contConfig');

		if($contConfig == 0){

			$sqlConfig   = "SELECT COUNT(id) AS contConfig, id FROM configuracion_documentos_erp WHERE id_empresa = '$id_empresa' AND tipo='$tipo_documento' AND activo=1";
			$queryConfig = mysql_query($sqlConfig,$conexionBD);
			$contConfig  = mysql_result($queryConfig, 0, 'contConfig');

			if($contConfig > 0){

				$sqlInsert   = "INSERT INTO configuracion_documentos_erp (nombre,tipo,id_empresa,id_sucursal) VALUES ('Orden de Compra','$tipo_documento','$id_empresa','$id_sucursal')";
				$queryInsert = mysql_query($sqlInsert,$conexionBD);
			}
			else{

				$sqlConfig   = "SELECT COUNT(id) AS contConfig, id, texto FROM configuracion_documentos_erp WHERE id_empresa = '0' AND tipo='$tipo_documento' AND activo=1";
				$queryConfig = mysql_query($sqlConfig,$conexionBD);
				$contConfig  = mysql_result($queryConfig, 0, 'contConfig');
				$textoConfig = mysql_result($queryConfig, 0, 'texto');

				if ($contConfig > 0) {
					$sqlInsert   = "INSERT INTO configuracion_documentos_erp (nombre,tipo,id_empresa,id_sucursal,texto) VALUES ('Orden de Compra','$tipo_documento','$id_empresa','$id_sucursal','$textoConfig')";
					$queryInsert = mysql_query($sqlInsert,$conexionBD);
				}
			}
		}
	}

	$documento      = "$titulo $consecutivo - $sucursal - $bodega";
	$textoPlantilla = cargaFormatoDocumento($id_empresa,$id_sucursal,$tipo_documento);
	$texto          = reemplazarVariables($textoPlantilla,$contenido,$id_empresa,$id_sucursal,$id);

	if(isset($TAM)){ $HOJA = $TAM; }
	else{ $HOJA = 'LETTER'; }

	if(!isset($ORIENTACION)){ $ORIENTACION = 'P'; }
	if(!isset($IMPRIME_PDF)){ $IMPRIME_PDF = 'true'; }

	if(isset($MARGENES)){ list($MS, $MD, $MI, $ML) = split( ',', $MARGENES ); }
	else{ $M[0] = 50; $M[1] = 10; $M[2] = 10; $M[3] = 10; }

	if(!isset($TAMANO_ENCA)){ $TAMANO_ENCA = 12; }

	include($_SERVER['DOCUMENT_ROOT']."/ERP/misc/MPDF54/mpdf.php");
	$mpdf = new mPDF(
		'utf-8',   			// mode - default ''
		$HOJA,					// format - A4, for example, default ''
		12,							// font size - default 0
		'',							// default font family
		$M[2],					// margin_left
		$M[1],					// margin right
		$M[0],					// margin top
		$M[3],					// margin bottom
		2,							// margin header
		2,							// margin footer
		$ORIENTACION		// L - landscape, P - portrait
	);

	$mpdf->SetProtection(array('print'));
	if($marcaAgua == 'true'){
      $mpdf->SetWatermarkText('ANULADO');
      $mpdf->showWatermarkText = true;
    }

	$mpdf->SetAutoPageBreak(TRUE, 15);
	$mpdf->SetTitle($documento);
	$mpdf->SetAuthor($_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA']);
	$mpdf->SetDisplayMode('fullpage');

	if($autorizado == 'false'){
		$mpdf->showWatermarkText = true;
		$mpdf->SetWatermarkText('NO AUTORIZADA. NO VALIDA PARA COMPRA.');
	}

	$mpdf->WriteHTML(utf8_encode($texto));

	return $mpdf->Output($documento.".pdf",'S');
}
?>
