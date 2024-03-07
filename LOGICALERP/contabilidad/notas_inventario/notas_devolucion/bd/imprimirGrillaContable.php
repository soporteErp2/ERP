<?php
	include("../../../../../configuracion/conectar.php");
	include("../../../../../configuracion/define_variables.php");
	include("../config_var_global.php");

	if (!isset($_SESSION['EMPRESA']) || $_SESSION['EMPRESA']==''){ exit; }

	ob_start();

	$id_empresa       = $_SESSION['EMPRESA'];
	$documento        = "Nota Devolucion";
	$estilo           = 'background-color: #DFDFDF;';
	$idFactura        = '';
	$sucursal         = '';
	$subTotalFactura  = 0.00;
	$acumuDescFactura = 0.00;
	$ivaFactura       = 0.00;
	$valorRetencion   = 0.00;
	$totalFactura     = 0.00;
	$campoTercero     = ($opcGrillaContable=='DevolucionCompra')? "id_proveedor": "id_cliente";

	$sql    = "SELECT TP.*, T.dv
				FROM $tablaPrincipal AS TP LEFT JOIN terceros AS T ON(
						T.activo=1
						AND T.id=TP.$campoTercero
					)
				WHERE TP.id='$id'
					AND TP.activo=1
					AND TP.id_empresa='$id_empresa'";
	$query = $mysql->query($sql,$mysql->link);

	$nit_tercero   = ($mysql->result($query,0,'dv') != '')? $mysql->result($query,0,'nit').'-'.$mysql->result($query,0,'dv'): $mysql->result($query,0,'nit');
	$sucursal      = $mysql->result($query,0,'sucursal');
	$bodega        = $mysql->result($query,0,'bodega');
	$fecha         = $mysql->result($query,0,'fecha_registro');
	$nit           = $mysql->result($query,0,'nit');
	$dv            = $mysql->result($query,0,'dv');
	$observacionGeneral = $mysql->result($query,0,'observacion');
	$estado        = $mysql->result($query,0,'estado');
  $exento_iva    = $mysql->result($query,0,'exento_iva');
	$marcaAgua     = ($estado==3)? 'true' : 'false' ;

	switch ($opcGrillaContable) {
		case 'DevolucionCompra':
			$tercero     =  $mysql->result($query,0,'proveedor');
			$titulo      = 'NOTA DEVOLUCION FACTURA DE COMPRA';
			$consecutivo = $mysql->result($query,0,'consecutivo');
			$idFactura   = $mysql->result($query,0,'id_documento_compra');
			$docCruce    = 'FC'.$mysql->result($query,0,'numero_documento_venta');
			break;

		case 'DevolucionVenta':
			$tercero     =  $mysql->result($query,0,'cliente');
			$idFactura   = $mysql->result($query,0,'id_documento_venta');
			$titulo      = ($opcCargar=='remisionVenta')? 'NOTA DEVOLUCION REMISION DE VENTA' : 'NOTA DEVOLUCION FACTURA DE VENTA' ;
			$consecutivo = $mysql->result($query,0,'consecutivo');
			$docCruce    = $mysql->result($query,0,'numero_documento_venta');

			// CONSULTAR SI LA FACTURA CARGADA TIENE GRUPOS
			if ($opcCargar=='facturaVenta') {
				$sql="SELECT
							id_fila_grupo_factura_venta AS idGrupoFv,
							codigo,
							nombre,
							cantidad,
							costo_unitario,
							observaciones,
							descuento,
							id_impuesto,
							nombre_impuesto,
							codigo_impuesto,
							porcentaje_impuesto,
							valor_impuesto
 						FROM devoluciones_venta_grupos WHERE activo=1 AND id_empresa=$id_empresa AND id_devolucion_venta=$id";
				$query=$mysql->query($sql,$mysql->link);
				while ($row=$mysql->fetch_array($query)) {
					$idGrupoFv = $row['idGrupoFv'];
					$whereIdGrupoFv .= ($whereIdGrupoFv=='')? "id_grupo_factura_venta=$idGrupoFv" : " OR id_grupo_factura_venta=$idGrupoFv" ;
					$arrayGruposFv[$idGrupoFv] = array(
												'codigo'              => $row['codigo'],
												'nombre'              => $row['nombre'],
												'cantidad'            => $row['cantidad'],
												'costo_unitario'      => $row['costo_unitario'],
												'observaciones'       => $row['observaciones'],
												'descuento'           => $row['descuento'],
												'id_impuesto'         => $row['id_impuesto'],
												'nombre_impuesto'     => $row['nombre_impuesto'],
												'codigo_impuesto'     => $row['codigo_impuesto'],
												'porcentaje_impuesto' => $row['porcentaje_impuesto'],
												'valor_impuesto'      => $row['valor_impuesto'],
											);
				}

				$sql="SELECT id_inventario_factura_venta,id_grupo_factura_venta
						FROM ventas_facturas_inventario_grupos WHERE id_factura_venta=$idFactura AND ($whereIdGrupoFv) ";
				$query=$mysql->query($sql,$mysql->link);
				while ($row=$mysql->fetch_array($query)) {
					$id_grupo      = $row['id_grupo_factura_venta'];
					$id_inventario = $row['id_inventario_factura_venta'];
					$arrayInvGrupo[$id_inventario] = $id_grupo;
					$groupByG = "id_fila_cargada,";

					// $arrayItemsGroup = array(
					// 							'id_inventario_factura_venta' => $row['id_inventario_factura_venta'],
					// 							'id_grupo_factura_venta'      => $row['id_grupo_factura_venta'],
					// 						);
				}

			}
			break;
	}

	// $sucursal      = $row['sucursal'];
	// $bodega        = $row['bodega'];
	// $fecha         = $row['fecha_registro'];
	// $nit_tercero   = ($row["dv"] != '')? $row["nit"].'-'.$row["dv"]: $row["nit"];
	// $observaciones = $row['observacion'];

	if ($estado==0) { echo '<center><h2><i>Documento no Generado</i></h2></center>'; exit; } //DOUMENTO NO GENERADO
	$marcaAgua = 'false';
    if($estado==3){
    	$marcaAgua = 'true';
    }

    // AGREGAR LOS GRUPOS DE LA FACTURA
	foreach ($arrayGruposFv as $id_grupo => $arrayResult) {
		$subTotal = $arrayResult["costo_unitario"]-$arrayResult['descuento'];
		$articulos.="<tr>
						<td valign='top' width='80'>$arrayResult[codigo]</td>
						<td valign='top' width='280'><b>$arrayResult[nombre]</b></td>
						<td valign='top' width='80' align='right'>Unidad x 1</td>
						<td valign='top' width='70' align='right'>$arrayResult[cantidad]</td>
						<td valign='top' width='70' align='right'>$arrayResult[descuento]</td>
						<td valign='top' width='80' align='right'>".number_format($arrayResult["costo_unitario"],$_SESSION['DECIMALESMONEDA'])."</td>
						<td valign='top' width='80' align='right'>".number_format($subTotal,$_SESSION['DECIMALESMONEDA'])."</td>
					</tr>".(($arrayResult["observaciones"] != '')?
					"<tr>
						<td>&nbsp;</td>
						<td colspan='6' style='font-size:9px;width:100%;'>".str_replace("\n",'<br>',$arrayResult['observaciones'])."</td>
					</tr>
						":
						"" );
	}

	//CONSULTAR LOS ARTICULOS DEL DOCUMENTO
	$sql   = "SELECT *, SUM(cantidad) AS cantidad_total,COUNT(id) AS cant_filas FROM $tablaInventario WHERE $idTablaPrincipal='$id' GROUP BY $groupByG id_inventario,observaciones,tipo_descuento,descuento,costo_unitario";
	$query = $mysql->query($sql,$mysql->link);

	while ($array = mysql_fetch_array($query)) {
		$estilo = ($estilo!='')? '': 'background-color: #DFDFDF;';

		$id_row         = $array['id'];
		$id_inventario  = $array['id_inventario'];
		$observaciones  = $array['observaciones'];
		$tipo_descuento = $array['tipo_descuento'];
		$descuento      = $array['descuento'];
		$costo_unitario = $array['costo_unitario'];

		$cantidad           =  $array["cantidad_total"] * 1;
		$styleBorder        = ($array["observaciones"] == '')? 'border-bottom:1px solid #000;': 'border-bottom:1px dotted #000;';
		$descuento          = ($array["tipo_descuento"] == 'porcentaje')? $cantidad * $array["costo_unitario"]  * $array["descuento"] / 100 : $array["descuento"]*$array["cant_filas"];
		$subTotal           = ($cantidad * $array["costo_unitario"]) - $descuento;
		$tipoDescuento      = ($array["tipo_descuento"]=='porcentaje')? '%': '$';
		$array["descuento"] = $array["descuento"] * 1;

		//CALCULO DEL IVA POR ARTICULO
		$iva = ($array["valor_impuesto"] > 0)? $array["valor_impuesto"] * $subTotal / 100: 0;

		if($exento_iva == "No"){
      $arrayIvaDocumento[$array['id_impuesto']] += $iva;  
    }
    
		$arrayDatosIva[$array['id_impuesto']]= array('nombre' => $array['impuesto'],'valor_impuesto'=> $array['valor_impuesto']*1, );

		$subTotalFactura  = $subTotal+$subTotalFactura;
    if($exento_iva == "No"){
      $ivaFactura       = $ivaFactura+$iva;  
    }
		
		$acumuDescFactura = $acumuDescFactura+$descuento;

		if ($array["descuento"]==0) { $tipoDescuento=''; }

		if (array_key_exists($array['id_fila_cargada'], $arrayInvGrupo)) { /*echo $array["nombre"].'<br>';*/ continue; }
		if ( isset($arrayItems[$id_inventario][$costo_unitario][$tipo_descuento][$descuento][$observaciones]) ) {
			$arrayItems[$id_inventario][$costo_unitario][$tipo_descuento][$descuento][$observaciones]['cantidad']      += $array["cantidad_total"];
			$arrayItems[$id_inventario][$costo_unitario][$tipo_descuento][$descuento][$observaciones]['descuento']      += $descuento;
			$arrayItems[$id_inventario][$costo_unitario][$tipo_descuento][$descuento][$observaciones]['valor_impuesto'] += $array["valor_impuesto"];
			$arrayItems[$id_inventario][$costo_unitario][$tipo_descuento][$descuento][$observaciones]['subTotal']       += $subTotal;
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
												);
		}

		// $articulos .= '<tr style="'.$estilo.' '.$styleBorder.'">
		// 					<td style="width: 55px;">'.$array["codigo"].'</td>
		// 					<td style="width: 230px; text-align:left; padding-left:5px;">'.$array["nombre"].'</td>
		// 					<td style="width: 80px; text-align:left; padding-left:5px;">'.$array["nombre_unidad_medida"].' x '.$array["cantidad_unidad_medida"].'</td>
		// 					<td style="width: 60px; text-align:right;">'.$cantidad.'</td>
		// 					<td style="width: 80px; text-align:right;">'.$array["descuento"].' '.$tipoDescuento.'</td>
		// 					<td style="width: 80px; text-align:right;">'.number_format ($array["costo_unitario"],$_SESSION['DECIMALESMONEDA']).'</td>
		// 				 	<td style="width: 80px; text-align:right;">'.number_format ($subTotal,$_SESSION['DECIMALESMONEDA']).'</td>
		// 				</tr>';

		// if($array["observaciones"] != ''){
		// 	$articulos .= '<tr style="'.$estilo.'">
		// 					 	<td width="55">&nbsp;</td>
		// 					 	<td colspan="6" style="font-size:9px;">'.str_replace("\n",'<br>',$array["observaciones"]).'</td>
		// 					</tr>';
		// }


	}

	foreach ($arrayItems as $id_inventario => $arrayItems1) {
			foreach ($arrayItems1 as $costo_unitario => $arrayItems2){
				foreach ($arrayItems2 as $tipo_descuento => $arrayItems3){
					foreach ($arrayItems3 as $descuento => $arrayItems4){
						foreach ($arrayItems4 as $observaciones => $arrayResult){

							$estilo = ($estilo!='')? '': 'background-color: #F2F2F2;';
							$articulos.="<tr>
													<td valign='top' width='80'>$arrayResult[codigo]</td>
													<td valign='top' width='280'><b>$arrayResult[nombre]</b></td>
													<td valign='top' width='80' align='right'>$arrayResult[nombre_unidad_medida] x $arrayResult[cantidad_unidad_medida]</td>
													<td valign='top' width='70' align='right'>$arrayResult[cantidad]</td>
													<td valign='top' width='70' align='right'>$arrayResult[descuento]</td>
													<td valign='top' width='80' align='right'>".number_format($costo_unitario,$_SESSION['DECIMALESMONEDA'])."</td>
													<td valign='top' width='80' align='right'>".number_format($arrayResult['subTotal'],$_SESSION['DECIMALESMONEDA'])."</td>
												</tr>".(($observaciones != '')?
												"<tr>
													<td>&nbsp;</td>
													<td colspan='6' style='font-size:9px;width:100%;'>".str_replace("\n",'<br>',$observaciones)."</td>
												</tr>
											":
											"" );

						}// FIN ULTIMO FOR EACH
					}
				}
			}
		}

	//=================================// VALOR DEL IVA SOBRE LA FACTURA //=================================//
	//******************************************************************************************************//
	$contenido_iva = '';
	foreach ($arrayIvaDocumento as $id_impuesto => $valor_impuesto) {
		if($valor_impuesto>0){
			$contenido_iva.='<tr>
								<td>'.$arrayDatosIva[$id_impuesto]['nombre'].' ('.$arrayDatosIva[$id_impuesto]['valor_impuesto'].'%)</td>
								<td>$</td>
								<td style="text-align:right;">'.number_format ($valor_impuesto,$_SESSION['DECIMALESMONEDA']).'</td>
							</tr>';
		}
	}

	//=========================// CALCULAMOS EL VALOR DE LA RETENCION  SOBRE LA FACTURA //=========================//
	//*************************************************************************************************************//

	$valorRetencion     = 0;
	$listadoRetenciones = '';
	$simboloRetencion   = '';
	$valoresRetenciones = '';

	$sqlRetenciones   = "SELECT retencion,valor,tipo_retencion,base FROM $tablaRetenciones WHERE $campoTablaRetenciones='$idFactura' AND activo=1";
	$queryRetenciones = mysql_query($sqlRetenciones,$link);

	while ($arrayRetenciones=mysql_fetch_array($queryRetenciones)) {

		if(!is_nan($arrayRetenciones["valor"])){ $arrayRetenciones["valor"] = $arrayRetenciones["valor"]*1; }

		if ($arrayRetenciones["tipo_retencion"]=='ReteIva') {
			if ($arrayRetenciones['base']>$ivaFactura) { continue; }
			$valorRetencion +=($ivaFactura * $arrayRetenciones["valor"])/100;
			$mostrarRetenciones .= '<tr>
										<td>'.$arrayRetenciones["retencion"].' ( '.$arrayRetenciones["valor"].' %) </td>
										<td>$</td>
										<td style="text-align:right;"> '.number_format ((($ivaFactura * $arrayRetenciones["valor"])/100),$_SESSION['DECIMALESMONEDA']).'</td>
									</tr>';

		}
		else if ($arrayRetenciones["tipo_retencion"] == 'AutoRetencion') { continue; }
		else{
			if ($arrayRetenciones['base']>$subTotalFactura) { continue; }
			$valorRetencion +=($subTotalFactura * $arrayRetenciones["valor"])/100;
			$mostrarRetenciones .= '<tr>
										<td>'.$arrayRetenciones["retencion"].' ('.$arrayRetenciones["valor"].' %) </td>
										<td>$</td>
										<td style="text-align:right;"> '.number_format ((($subTotalFactura * $arrayRetenciones["valor"])/100),$_SESSION['DECIMALESMONEDA']).'</td>
									</tr>';

		}
	}

	$totalFactura=($subTotalFactura - $valorRetencion)+$ivaFactura;

	$arrayReplaceString = array("\n", "\r");
	$observaciones = str_replace($arrayReplaceString, "<br/>", $observaciones );

?>

<style>
	.my_informe_Contenedor_Titulo_informe{
        float       : left;
        width       : 100%;
        margin      : 0 0 10px 0;
        font-size   : 11px;
        font-family : "Segoe UI Light","Helvetica Neue Light","Segoe UI","Helvetica Neue","Trebuchet MS",Helvetica,"Droid Sans",Tahoma,Geneva,sans-serif;
	}
	.my_informe_Contenedor_Titulo_informe_label{
        float       : left;
        width       : 130px;
        font-weight : bold;
	}
	.my_informe_Contenedor_Titulo_informe_detalle{
        float         :	left;
        width         :	210px;
        padding       :	0 0 0 5px;
        white-space   : nowrap;
        overflow      : hidden;
        text-overflow : ellipsis;
	}
	.my_informe_Contenedor_Titulo_informe_Empresa{
        float     :	left;
        width     :	100%;
        font-size : 16px;
	}
    .my_informe_Contenedor_Titulo_informe td{ padding-left : 2px; }
    .tablaPiePagina td{ padding-left : 2px; }

    td{
        font-size   : 11px;
        font-family :"Segoe UI Light","Helvetica Neue Light","Segoe UI","Helvetica Neue","Trebuchet MS",Helvetica,"Droid Sans",Tahoma,Geneva,sans-serif;
    }

    /*div, td{ color: #000; }*/

</style>

<body>
    <div style="float:left; width:100%">

        <!-- INFORMACION DE LA EMPRESA -->
        <table style="text-align:center; margin-left:auto; margin-right:auto; font-size:12px; float:left; width:100%; margin-bottom:20px;">
            <tr><td style="font-size: 15px;font-weight: bold;"><?php echo $_SESSION['NOMBREEMPRESA']; ?></td></tr>
            <tr><td>Nit. <?php echo $_SESSION['NITEMPRESA']; ?></td></tr>
            <tr><td>Sucursal: <?php echo $sucursal; ?></td></tr>
            <tr><td>Bodega: <?php echo $bodega; ?></td></tr>
        </table>

        <!-- INFORMACION DEL TERCERO -->
        <div style="float:left; width:48%; font-size: 11px;">
            <div style="float:left; width:100%;">
                <div style="float:left; width:25%;">CANCELADO A:</div>
                <div style="float:left; width:75%;"><b><?php echo $tercero; ?></b></div>
            </div>
            <div style="float:left; width:100%;">
                <div style="float:left; width:25%;">NIT:</div>
                <div style="float:left; width:75%;"><?php echo $nit_tercero; ?></div>
            </div>
        </div>

        <!-- NOMBRE DEL DOCUMENTO Y CONSECUTIVO -->
        <div style="float:left; width:50%;">
                <div style="float:left; width:100%; font-size:15px;"><b><?php echo $titulo; ?></b> <?php echo $consecutivo; ?></div>
                <div style="float:left; width:100%; font-size:11px;">FECHA: <?php echo fecha_larga($fecha); ?></div>
                <div style="float:left; width:100%; font-size:11px;">DOCUMENTO CRUCE: <?php echo $docCruce ?></div>
            </table>
        </div>
        <br>
    </div>

    <div class="my_informe_Contenedor_Titulo_informe">

        <!-- CUERPO DEL INFORME -->
        <table style="border-collapse: collapse; width:100%;">
        	<thead>
	            <tr style="background-color:#000;">
					<td style="width:55px; color:#fff; text-align:center;">CODIGO</td>
					<td style="width:230px; color:#fff; text-align:center;">ITEM</td>
					<td style="width:80px; color:#fff; text-align:center;">UNIDAD</td>
					<td style="width:60px; color:#fff; text-align:center;">CANTIDAD</td>
					<td style="width:80px; color:#fff; text-align:center;">DESCUENTO</td>
					<td style="width:80px; color:#fff; text-align:center;">VALOR</td>
					<td style="width:80px; color:#fff; text-align:center;">SUBTOTAL</td>
	            </tr>
	        </thead>

			<tbody><?php echo $articulos ?></tbody>
		</table>
		<div style=" float:left">
			<br>
			<table style="1px font-style:normal; font-size:11px; margin-left:5px; border-collapse:collapse;" align="right">
				<tr>
					<td>SUBTOTAL</td>
					<td style="width:10px;">$</td>
					<td style="text-align:right; padding-left:20px; font-weight:bold;"> <?php echo number_format ($subTotalFactura,$_SESSION['DECIMALESMONEDA']); ?></td>
				</tr>
				<?php echo $contenido_iva; ?>
				<?php echo $mostrarRetenciones; ?>
				<tr style="font-weight:bold;">
					<td style="text-align:center; font-weight:bold; border-top:1px solid #000; border-bottom:1px solid #000;">TOTAL</td>
					<td style="width:10px; font-weight:bold; border-top:1px solid #000; border-bottom:1px solid #000;">$</td>
					<td style="text-align:right; font-weight:bold; padding-left:20px; border-top:1px solid #000; border-bottom:1px solid #000;"><?php echo number_format ($totalFactura,$_SESSION['DECIMALESMONEDA']); ?></td>
				</tr>
			</table>
			<br>

			<div style="overflow: hidden; width:100%; margin:5px 5px 20px 0px; padding:0px 7px 0px 0px; font-size:12px;">
		        <div style="border:1px solid #000; margin-top:20px; width:740px; padding:5px;">
		            <b>Observaciones: </b><?php echo $observacionGeneral; ?>
		        </div>
	    	</div>
	    </div>
    </div>

</body>

<?php

	$texto = ob_get_contents(); ob_end_clean();

	if(isset($TAM)){ $HOJA = $TAM; }
	else{ $HOJA = 'LETTER'; }

	if(!isset($ORIENTACION)){ $ORIENTACION = 'P'; }
	if(!isset($PDF_GUARDA)){ $PDF_GUARDA = false; }
	if(!isset($IMPRIME_PDF)){ $IMPRIME_PDF = 'true'; }

	if(isset($MARGENES)){ list($MS, $MD, $MI, $ML) = split( ',', $MARGENES ); }
	else{ $MS=10; $MD=10; $MI=10; $ML=10; }

	if(!isset($TAMANO_ENCA)){ $TAMANO_ENCA = 12; }
	if($IMPRIME_PDF){
		include("../../../../../misc/MPDF54/mpdf.php");
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
        // $mpdf-> debug = true;
		$mpdf->useSubstitutions = true;
		$mpdf->SetProtection(array('print'));
		if($marcaAgua=='true'){
			$mpdf->SetWatermarkText('ANULADO');
			$mpdf->showWatermarkText = true;
		}
        // $mpdf->simpleTables = true;
        $mpdf->packTableData = true;
		$mpdf->SetAutoPageBreak(TRUE, 15);
		// $mpdf->SetTitle ( $documento );
		$mpdf->SetAuthor ( $_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA'] );
		$mpdf->SetDisplayMode ( 'fullpage' );
        $mpdf->SetHtmlFooter('<div style="width:96%; font-size:9px; text-align:right;">Pagina {PAGENO}/{nb}</div>');
		$mpdf->WriteHTML(utf8_encode($texto));

		if($PDF_GUARDA){ $mpdf->Output($documento.".pdf",'D'); }   	///OUTPUT A ARCHIVO
		else{ $mpdf->Output($documento.".pdf",'I'); }		///OUTPUT A VISTA
		exit;
	}
	else{ echo $texto; }

?>
