<?php

	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../../bd/functions_imprimir.php");

	if (!isset($_SESSION['EMPRESA']) || $_SESSION['EMPRESA']==''){ exit; }
	$id_empresa = $_SESSION['EMPRESA'];

	// CONSULTAR LA INFORMACION DE LA EMPRESA
	$sqlEmpresa   = "SELECT nombre,tipo_documento_nombre,documento, pais,ciudad,direccion,razon_social,telefono,celular,tipo_regimen FROM empresas WHERE id='$id_empresa' LIMIT 0,1";
	$queryEmpresa = mysql_query($sqlEmpresa,$link);

	$nombre_empresa        = mysql_result($queryEmpresa,0,'nombre');
	$tipo_documento_nombre = mysql_result($queryEmpresa,0,'tipo_documento_nombre');
	$documento_empresa     = mysql_result($queryEmpresa,0,'documento');
	$ubicacion_empresa     = mysql_result($queryEmpresa,0,'ciudad').' - '.mysql_result($queryEmpresa,0,'pais');
	$direccion_empresa     = mysql_result($queryEmpresa,0,'direccion');
	$razon_social          = mysql_result($queryEmpresa,0,'razon_social');
	$telefonos             = mysql_result($queryEmpresa,0,'telefono').' - '.mysql_result($queryEmpresa,0,'celular');
	$tipo_regimen          = mysql_result($queryEmpresa,0,'tipo_regimen');

	$subtotalFactura = 0.00;
	$ivaFactura      = 0.00;
	$ivaFactura1     = 0.00;
	$valorRetencion  = 0.00;
	$totalFactura    = 0.00;

	//==================================// QUERY //==================================//
	//*******************************************************************************//
	$sqlFc    = "SELECT F.*,
					T.direccion,
					T.telefono1,
					T.departamento,
					T.ciudad,
					T.tercero_tributario,
					IF(T.dv > 0, CONCAT(T.numero_identificacion,' - ',T.dv), T.numero_identificacion) AS nit_tercero
				FROM compras_facturas AS F LEFT JOIN terceros AS T ON(
							F.id_proveedor = T.id
						)
				WHERE F.id='$id'
					AND F.activo=1
				LIMIT 0,1";
	$queryFc = mysql_query($sqlFc,$link);
	if (!$queryFc){ 'Informe no valido '.mysql_error(); exit; }

	$estado      = mysql_result($queryFc, 0, 'estado');
	$consecutivo = mysql_result($queryFc, 0, 'consecutivo');
	$proveedor   = mysql_result($queryFc, 0,'proveedor');
	$cuenta_pago = mysql_result($queryFc, 0, 'cuenta_pago');
	$observacion = mysql_result($queryFc, 0, 'observacion');
	$id_sucursal = mysql_result($queryFc, 0, 'id_sucursal');
	$contabilidad_manual = mysql_result($queryFc, 0, 'contabilidad_manual');

	$tercero_dpto      = mysql_result($queryFc, 0,'departamento');
	$tercero_ciudad    = mysql_result($queryFc, 0,'ciudad');
	$tercero_regimen   = mysql_result($queryFc, 0,'tercero_tributario');

	$tercero_nit       = mysql_result($queryFc, 0,'nit_tercero');
	$tercero_telefono  = mysql_result($queryFc, 0,'telefono1');
	$tercero_direccion = mysql_result($queryFc, 0,'direccion');

	if ($estado==0) { echo '<center><h2><i>Documento no Generado</i></h2></center>'; exit; }
    $marcaAgua = ($row['estado']==3)? 'true': 'false';

	//=======================// ITEMS (CUENTAS) //=======================//
	//*******************************************************************//

	$sqlArticulos   = "SELECT *, SUM(cantidad) AS cantidad_total
						FROM compras_facturas_inventario
						WHERE id_factura_compra='$id'
						GROUP BY id_inventario, id_impuesto, costo_unitario, observaciones, tipo_descuento, descuento";
	$queryArticulos = mysql_query($sqlArticulos,$link);

	$estilo = 'background-color: #EEE;';
	while ($array= mysql_fetch_array($queryArticulos)) {
		$estilo = ($estilo!='')? '': 'background-color: #EEE;';


		// consultamos la unidad del articulo
		$sqlUnidad     = 'SELECT inventario_unidades.nombre  FROM inventario_unidades INNER JOIN items ON inventario_unidades.id=items.id_unidad_medida WHERE items.id="'.$array["id_inventario"].'"';
		$queryUnidad   = mysql_query($sqlUnidad,$link);
		$unidad_nombre = mysql_result($queryUnidad,0,'nombre');

		if ($array["descuento"]>0) {
			if ($array["tipo_descuento"]=='porcentaje') {
				$temp      = ($array["cantidad_total"]*$array["costo_unitario"]);
				$descuento = ($temp*$array["descuento"])/100;
				$temp     -= $descuento;
			}
			else{ $temp = ($array["cantidad_total"]*$array["costo_unitario"])-$array["descuento"]; }
		}
		else{ $temp = ($array["cantidad_total"]*$array["costo_unitario"]); }

		$tipodesart = ($array["tipo_descuento"]=='porcentaje')? '%': $tipodesart='$';
		if ($array["descuento"]==0 || $array["descuento"]==0.00) { $tipodesart='&nbsp;&nbsp;'; }

		$articulos.='<tr>
						<td style="'.$estilo.' width: 55px;">'.$array["codigo"].'</td>
						<td style="'.$estilo.' width: 230px; text-align:left; padding-left:5px;">'.$array["nombre"].'</td>
						<td style="'.$estilo.' width: 80px; text-align:left; padding-left:5px;">'.$array["nombre_unidad_medida"].' x '.$array["cantidad_unidad_medida"].'</td>
						<td style="'.$estilo.' width: 60px; text-align:right;">'.$array["cantidad_total"].'</td>
						<td style="'.$estilo.' width: 80px; text-align:right;">'.$array["descuento"].' '.$tipodesart.'</td>
						<td style="'.$estilo.' width: 80px; text-align:right;">'.number_format ($array["costo_unitario"],$_SESSION['DECIMALESMONEDA']).'</td>
					 	<td style="'.$estilo.' width: 80px; text-align:right;">'.number_format ($temp,$_SESSION['DECIMALESMONEDA']).'</td>
					</tr>';

		if($array["observaciones"] != ''){
			$articulos.='<tr style=" border-bottom:1px solid #000; border-top:1px solid;">
							<td style="'.$estilo.' width:100%;" colspan="7">
								<b>Observaciones</b>:<br>
								'.str_replace("\n",'<br>',$array["observaciones"]).'
							</td>
						 </tr>';
		}


		//========================= INICIO DEL CALCULO FACTURA DE COMPRA ==========================//

		$cantidad  = $array["cantidad_total"];
		$descuento = $array["descuento"];
		$costo     = $array["costo_unitario"];
		$tipoDesc  = $array["tipo_descuento"];
		$iva       = $array["valor_impuesto"];

		$subtotal         = 0;
		$descuentoTotal   = 0;
		$descuentoMostrar = 0;

		$subtotal=($cantidad*$costo);

		//verificar el tipo de descuento del articulo, si es en porcentaje se hace el sgt calculoa para convertir en pesos el descuento
		$descuento = ($tipoDesc=='porcentaje')? ($subtotal*$descuento)/100 : $descuento;
		$subtotal  = $subtotal- $descuento;
		$iva       = ($iva*$subtotal)/100;

		$subtotalFactura = $subtotal+$subtotalFactura;	//ACUMULADO SUBTOTAL
		$ivaFactura      = $ivaFactura+$iva;			//ACUMULADO IVA

		$arrayIvaDocumento[$array['id_impuesto']]+=$iva;
		$arrayDatosIva[$array['id_impuesto']]= array('nombre' => $array['impuesto'],'valor_impuesto'=> $array['valor_impuesto']*1, );

	}

	//SI LA CONTABILIDAD ES MANUAL, CONSULTAR LOS SALDOS
	if ($contabilidad_manual=='true') {
		$sql   = "SELECT * FROM compras_facturas_contabilidad_manual WHERE id_factura_compra=$id AND id_empresa='$id_empresa'";
		$query = mysql_query($sql,$link);

		$subtotalFactura = mysql_result($query,0,'subtotal_manual');
		$ivaFactura      = mysql_result($query,0,'iva_manual');
		$total_manual          = mysql_result($query,0,'total_manual');
	}


	//=============================// RETENCIONES //=============================//
	//***************************************************************************//
	$div_retenciones    = '';
	$valorRetencion     = 0;
	$listadoRetenciones = '';
	$simboloRetencion   = '';
	$valoresRetenciones = '';

	$sqlRetenciones = "SELECT retencion,valor,tipo_retencion,base
						FROM compras_facturas_retenciones
						WHERE id_factura_compra='$id'";
	$queryRetenciones=mysql_query($sqlRetenciones,$link);

	while ($arrayRetenciones=mysql_fetch_array($queryRetenciones)) {

		if ($arrayRetenciones["tipo_retencion"] == 'ReteIva') {
			if ($arrayRetenciones['base']>$ivaFactura) { continue; }
			$valorRetencion +=($ivaFactura * $arrayRetenciones["valor"])/100;

			$div_retenciones .= '<tr>
										<td style="width:170px; font-weight:bold; ">'.$arrayRetenciones["retencion"].' ( '.$arrayRetenciones["valor"].' %) </td>
										<td style="width:10px;" >$</td>
										<td style="text-align:right;"> '.number_format ((($ivaFactura * $arrayRetenciones["valor"])/100),$_SESSION['DECIMALESMONEDA']).'</td>
									</tr>';
		}
		else if ($arrayRetenciones["tipo_retencion"] == 'AutoRetencion') { continue; }
		else{
			if ($arrayRetenciones['base']>$subtotalFactura) { continue; }
			$valorRetencion +=($subtotalFactura * $arrayRetenciones["valor"])/100;

			$div_retenciones .= '<tr>
										<td style="width:170px; font-weight:bold; ">'.$arrayRetenciones["retencion"].' ( '.$arrayRetenciones["valor"].' %) </td>
										<td style="width:10px;" >$</td>
										<td style="text-align:right;"> '.number_format ((($subtotalFactura * $arrayRetenciones["valor"])/100),$_SESSION['DECIMALESMONEDA']).'</td>
									</tr>';
		}
	}

	if ($contabilidad_manual=='true') {
		$totalFactura  = $total_manual;
		$div_iva = '<tr>
						<td style="width:170px; font-weight:bold;">Iva</td>
						<td style="width:10px;">$</td>
						<td style="text-align:right;">'.number_format ($ivaFactura,$_SESSION['DECIMALESMONEDA']).'</td>
					</tr>';
	}
	else{

		//=================================// IVA //=================================//
		//***************************************************************************//
		foreach ($arrayIvaDocumento as $id_impuesto => $valor_impuesto) {
			$div_iva .= '<tr>
							<td style="width:170px; font-weight:bold;">'.$arrayDatosIva[$id_impuesto]['nombre'].' ('.$arrayDatosIva[$id_impuesto]['valor_impuesto'].'%)</td>
							<td style="width:10px;">$</td>
							<td style="text-align:right;">'.number_format ($valor_impuesto,$_SESSION['DECIMALESMONEDA']).'</td>
						</tr>';
		}

		//=================================// ANTICIPO //=================================//
		//********************************************************************************//
		$div_anticipo = '';

	    $sqlAnticipos   = "SELECT SUM(valor) AS valorAnticipos FROM anticipos WHERE id_documento='$id' AND activo=1 AND tipo_documento='FC' AND id_empresa='$id_empresa'";
	    $queryAnticipos = mysql_query($sqlAnticipos,$link);
	    $totalAnticipo  = mysql_result($queryAnticipos, 0, 'valorAnticipos');

	    if($totalAnticipo > 0){
	        $totalAnticipo *= 1;

	        $div_anticipo .= '<tr>
									<td style="width:170px; font-weight:bold;">ANTICIPO</td>
									<td style="width:10px;">$</td>
									<td style="text-align:right;">'.number_format($totalAnticipo).'</td>
								</tr>';
	    }

		$totalFactura=($subtotalFactura - $valorRetencion)+$ivaFactura;
		if($totalAnticipo > 0){ $totalFactura -= $totalAnticipo; }
	}


	$arrayReplaceString = array("\n", "\r");
	$observacion = str_replace($arrayReplaceString, "<br/>", $observacion );

	//==================================// FACTURA DE COMPRA //==================================//
	//*******************************************************************************************//

	$contenido='<div id="body_pdf" style="width:100%; font-style:normal; font-size:11px;">
					<div style="overflow: hidden; width:100%; margin-top:20px;">
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
						</div>
					</div>
				</div>
				<div style="width:100%; font-style:normal; font-size:11px; margin:0px 0px 0px 0px; pdding-top:30px;">
					<table class="articlesTable">
						<thead>
							<tr>
								<td style="width:55px;">CODIGO</td>
								<td style="width:230px;">ITEM</td>
								<td style="width:80px;">UNIDAD</td>
								<td style="width:60px;">CANTIDAD</td>
								<td style="width:80px;">DESCUENTO</td>
								<td style="width:80px;">V/UNIDAD</td>
								<td style="width:80px; border-right: 1px solid;">SUBTOTAL</td>
							</tr>
						</thead>
						<tbody style="border-bottom:1px solid #000;">'.$articulos.'</tbody>
					</table>
					<br/>
					</div>

					<div style=" float:left">
					<table align="right" style="width:50%; font-style:normal; font-size:11px; margin:10px 5px 0px 10px; border-collapse:collapse;" >
						<tr>
							<td style="width:170px; font-weight:bold;">SUBTOTAL</td>
							<td style="width:10px;">$</td>
							<td style="text-align:right;">'.number_format ($subtotalFactura,$_SESSION['DECIMALESMONEDA']).'</td>
						</tr>
						'.$div_iva.'
						'.$div_retenciones.'
						'.$div_anticipo.'
					</table>
					<table align="right" style="width:50%; border-top: 1px solid; font-style:normal; font-size:11px; margin-left:5px;">
						<tr style="font-weight:bold;">
							<td style="width:170px; font-weight:bold;">TOTAL</td>
							<td style="width:10px; font-weight:bold;">$</td>
							<td style="text-align:right; font-weight:bold;">'.number_format ($totalFactura,$_SESSION['DECIMALESMONEDA']).'</td>
						</tr>
					</table>

					<br>
					<div style="overflow: hidden; width:100%; font-size:12px;">
						<div style="float:left; width:90%; margin:5px 5px 0px 0px;">Observaciones</div>
						<div style="float:left; width:100%; margin:3px 200px 5px 0px; padding:5px 10px 5px 10px; border: 1px solid; height:40px;">'.$observacion.'</div>
					</div>
				</div>

				<style>
					.articlesTable{
						font-size       : 10px;
						border-collapse : collapse;
						border-bottom   : 1px solid #000;
						width           : 100%;
					}

					.articlesTable td{ border-collapse : collapse; }
					.articlesTable tbody tr{ border : none; }

					.articlesTable thead td{
						text-align       : center;
						font-size        : 11px;
						font-family      : "Segoe UI Light","Helvetica Neue Light","Segoe UI","Helvetica Neue","Trebuchet MS",Helvetica,"Droid Sans",Tahoma,Geneva,sans-serif;
						background-color : #000;
						color            : #FFF;
					}

				</style>';

	//======================================// COPIA MODELOS DE IMPRESION //======================================//
	//************************************************************************************************************//
	$sqlConfig   = "SELECT COUNT(id) AS contConfig, id FROM configuracion_documentos_erp WHERE id_empresa = '$id_empresa' AND id_sucursal = '$id_sucursal' AND tipo='FC' AND activo=1";
	$queryConfig = mysql_query($sqlConfig,$link);
	$contConfig  = mysql_result($queryConfig, 0, 'contConfig');

	if($contConfig == 0){

		$sqlConfig   = "SELECT COUNT(id) AS contConfig, id FROM configuracion_documentos_erp WHERE id_empresa = '$id_empresa' AND tipo='FC' AND activo=1";
		$queryConfig = mysql_query($sqlConfig,$link);
		$contConfig  = mysql_result($queryConfig, 0, 'contConfig');

		if($contConfig > 0){

			$sqlInsert   = "INSERT INTO configuracion_documentos_erp (nombre,tipo,id_empresa,id_sucursal) VALUES ('Factura de Compra','FC','$id_empresa','$id_sucursal')";
			$queryInsert = mysql_query($sqlInsert,$link);
		}
		else{

			$sqlConfig   = "SELECT COUNT(id) AS contConfig, id, texto FROM configuracion_documentos_erp WHERE tipo='FC' AND activo=1";
			$queryConfig = mysql_query($sqlConfig,$link);
			$contConfig  = mysql_result($queryConfig, 0, 'contConfig');
			$textoConfig = mysql_result($queryConfig, 0, 'texto');

			if ($contConfig > 0) {
				$sqlInsert   = "INSERT INTO configuracion_documentos_erp (nombre,tipo,id_empresa,id_sucursal,texto) VALUES ('Factura de Compra','FC','$id_empresa','$id_sucursal','$textoConfig')";
				$queryInsert = mysql_query($sqlInsert,$link);
			}
		}
	}

	$documento      = "Factura_de_Compra";
	$textoPlantilla = cargaFormatoDocumento($id_empresa,$id_sucursal,'FC');
	$texto          = reemplazarVariables($textoPlantilla,$contenido,$id_empresa,$id_sucursal,$id);
	// echo $texto; exit;
	if(isset($TAM)){ $HOJA = $TAM; }
	else{ $HOJA = 'LETTER'; }

	if(!isset($ORIENTACION)){ $ORIENTACION = 'P'; }
	if(!isset($PDF_GUARDA)){ $PDF_GUARDA = false; }
	if(!isset($IMPRIME_PDF)){ $IMPRIME_PDF = 'true'; }

	if(isset($MARGENES)){ list($MS, $MD, $MI, $ML) = split( ',', $MARGENES ); }
	else{ $MS = 50; $MD = 10; $MI = 10; $ML = 10; }

	if(!isset($TAMANO_ENCA)){ $TAMANO_ENCA = 12; }

	if($IMPRIME_PDF){
		include("../../../../misc/MPDF54/mpdf.php");
		$mpdf = new mPDF(
			'utf-8',   					// mode - default ''
			$HOJA,						// format - A4, for example, default ''
			12,							// font size - default 0
			'',							// default font family
			$MI,						// margin_left
			$MD,						// margin right
			$MS,						// margin top
			$ML,						// margin bottom
			2,							// margin header
			2,							// margin footer
			$ORIENTACION				// L - landscape, P - portrait
		);

		$mpdf->SetProtection(array('print'));
		if($marcaAgua=='true'){
            $mpdf->SetWatermarkText('ANULADO');
            $mpdf->showWatermarkText = true;
        }
		$mpdf->SetAutoPageBreak(TRUE, 15);
		$mpdf->SetTitle ($documento);
		$mpdf->SetAuthor ($_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA'] );
		$mpdf->SetDisplayMode ('fullpage');
		$mpdf->SetHTMLHeader(utf8_encode($header));
		$mpdf->WriteHTML(utf8_encode($texto));

		if($PDF_GUARDA){ $mpdf->Output($documento.".pdf",'D'); } 	///OUTPUT A ARCHIVO
		else{ $mpdf->Output($documento.".pdf",'I');	}	///OUTPUT A VISTA

		exit;
	}
	else{ echo $texto; }
?>