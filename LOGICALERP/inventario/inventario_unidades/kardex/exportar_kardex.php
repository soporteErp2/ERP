<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	header('Content-type: application/vnd.ms-excel;');
   	header("Content-Disposition: attachment; filename=kardex_inventario_".date('Y_m_d').".xls");
   	header("Pragma: no-cache");
   	header("Expires: 0");

	$id_sucursal = $_SESSION['SUCURSAL'];
	$id_empresa  = $_SESSION['EMPRESA'];
	$acumSaldo   = 0;
	$fechaIniRegistros = '';

	//=======================// VENTAS //=======================//
	//**********************************************************//
	$sqlVentas = "SELECT VF.cliente AS tercero,
							VF.numero_factura_completo AS consecutivo,
							VF.fecha_inicio AS fecha,
						 	REPLACE(VF.fecha_inicio,'-','') AS indice,
						 	REPLACE(VF.hora_inicio,':','') AS hora,
							VF.id_saldo_inicial,
							VFI.costo_inventario,
							SUM(VFI.cantidad) as cantidad
					FROM ventas_facturas_inventario AS VFI,
						ventas_facturas AS VF
					WHERE VF.fecha_inicio >= '$fecha_consulta'
						AND VF.id_empresa='$id_empresa'
						AND VF.id_sucursal='$id_sucursal'
						AND VF.id_bodega='$filtro_bodega'
						AND (VF.estado = 1 OR VF.estado=4)
						AND VF.id=VFI.id_factura_venta
						AND VF.activo=1
						AND VFI.id_inventario='$id_item'
						AND VFI.activo=1
					GROUP BY VFI.id_factura_venta, VFI.costo_inventario
					ORDER BY VF.fecha_inicio ASC";
	$queryVentas = mysql_query($sqlVentas, $link);
	while ($rowVentas = mysql_fetch_assoc($queryVentas)) {

		$indice    = $rowVentas['indice']*1;
		$horaVenta = $rowVentas['hora']*1;
		$arrayKardex[$indice][$horaVenta][] = array('cantidad'=>$rowVentas['cantidad'],
														'costo'=>$rowVentas['costo_inventario'],
														'tipo'=>'out',
														'saldo_inicial'=>$rowVentas['id_saldo_inicial'],
														'tipoDocumento'=>'FV',
														'numeroDocumento'=>$rowVentas['consecutivo'],
														'descripcion'=>'',
														'tercero'=>$rowVentas['tercero'],
														'fecha'=>$rowVentas['fecha']);
		$prueba .= '+'.$rowVentas['cantidad'];
		$acumSaldo += $rowVentas['cantidad'];
		// echo '<br>'.$acumSaldo;
	}

	$sqlVentas = "SELECT VF.cliente AS tercero,
							VF.consecutivo,
							VF.fecha_inicio AS fecha,
							VFI.costo_inventario,
							SUM(VFI.cantidad) as cantidad
					FROM ventas_remisiones_inventario AS VFI,
						ventas_remisiones AS VF
					WHERE VF.fecha_inicio >= '$fecha_consulta'
						AND VF.id_empresa='$id_empresa'
						AND VF.id_sucursal='$id_sucursal'
						AND VF.id_bodega='$filtro_bodega'
						AND (VF.estado = 1 OR VF.estado=2 OR VF.estado=4)
						AND VF.id=VFI.id_remision_venta
						AND VF.activo=1
						AND VFI.id_inventario='$id_item'
						AND VFI.activo=1
					GROUP BY VFI.id_remision_venta, VFI.costo_inventario
					ORDER BY VF.fecha_inicio ASC";
	$queryVentas = mysql_query($sqlVentas, $link);
	while ($rowVentas = mysql_fetch_assoc($queryVentas)) {

		$indice    = $rowVentas['indice']*1;
		$horaVenta = $rowVentas['hora']*1;
		$arrayKardex[$indice][$horaVenta][] = array('cantidad'=>$rowVentas['cantidad'],
														'costo'=>$rowVentas['costo_inventario'],
														'tipo'=>'out',
														'saldo_inicial'=>$rowVentas['id_saldo_inicial'],
														'tipoDocumento'=>'RV',
														'numeroDocumento'=>$rowVentas['consecutivo'],
														'descripcion'=>'',
														'tercero'=>$rowVentas['tercero'],
														'fecha'=>$rowVentas['fecha']);
		$prueba .= '+'.$rowVentas['cantidad'];
		$acumSaldo += $rowVentas['cantidad'];
		// echo '<br>'.$acumSaldo;
	}

	//=======================// COMPRAS //=======================//
	//***********************************************************//
	$sqlCompras = "SELECT CF.proveedor AS tercero,
							CF.prefijo_factura,
							CF.numero_factura,
							CF.fecha_inicio AS fecha,
						 	REPLACE(CF.fecha_inicio,'-','') AS indice,
						 	REPLACE(CF.hora_generacion,':','') AS hora,
							CF.id_saldo_inicial,
							CFI.costo_unitario AS costo_inventario,
							SUM(CFI.cantidad) as cantidad
					FROM compras_facturas_inventario AS CFI,
						compras_facturas AS CF
					WHERE CF.fecha_inicio >= '$fecha_consulta'
						AND CF.id_empresa='$id_empresa'
						AND CF.id_sucursal='$id_sucursal'
						AND CF.id_bodega='$filtro_bodega'
						AND (CF.estado = 1 OR CF.estado=4)
						AND CF.id=CFI.id_factura_compra
						AND CF.activo=1
						AND CFI.id_inventario='$id_item'
						AND CFI.check_opcion_contable=''
						AND CFI.activo=1
					GROUP BY CFI.id_factura_compra, CFI.costo_unitario
					ORDER BY CF.fecha_inicio ASC";
	$queryCompras = mysql_query($sqlCompras, $link);
	while ($rowCompras = mysql_fetch_assoc($queryCompras)) {

		if($fechaIniRegistros == ''){ $fechaIniRegistros = $rowCompras['fecha']; }
		$consecutivo = ($rowCompras['prefijo_factura'] == '')? $rowCompras['numero_factura']: $rowCompras['prefijo_factura'].' '.$rowCompras['numero_factura'];

		$whereFC .= ($whereFC=='')? "id_factura_compra=".$rowCompras['id'] : " OR id_factura_compra=".$rowCompras['id'] ;
		$indice     = $rowCompras['indice']*1;
		$horaCompra = $rowCompras['hora']*1;
		$arrayKardex[$indice][$horaCompra][] = array('cantidad'=>$rowCompras['cantidad'],
														'costo'=>$rowCompras['costo_inventario'],
														'tipo'=>'in',
														'saldo_inicial'=>$rowCompras['id_saldo_inicial'],
														'tipoDocumento'=>'FC',
														'numeroDocumento'=>$consecutivo,
														'descripcion'=>'',
														'tercero'=>$rowCompras['tercero'],
														'fecha'=>$rowCompras['fecha']);
		$prueba .= '-'.$rowCompras['cantidad'];
		$acumSaldo -= $rowCompras['cantidad'];
		// echo '<br>'.$acumSaldo;
	}

	// CONSULTAR LAS ENTRADAS DE ALMACEN QUE NO SE DEBEN MOSTRAR POR QUE SE FACTURARON
	$sql="SELECT id_consecutivo_referencia FROM compras_facturas_inventario WHERE activo=1 AND ($whereFC) AND id_consecutivo_referencia > 0";
	$query= mysql_query($sql, $link);
	while ($row=mysql_fetch_array($query)) {
		$whereEA .= ($whereEA=='')? "CF.id<>".$row['id_consecutivo_referencia'] : " AND CF.id<>".$row['id_consecutivo_referencia'] ;
	}

	$whereEA =($whereEA<>'')? " $whereEA " : "" ;

	$sqlCompras = "SELECT CF.proveedor AS tercero,
							CF.consecutivo,
							CF.fecha_registro AS fecha,
							CFI.costo_unitario AS costo_inventario,
							SUM(CFI.cantidad) as cantidad
					FROM compras_entrada_almacen_inventario AS CFI,
						compras_entrada_almacen AS CF
					WHERE CF.fecha_registro >= '$fecha_consulta'
						AND CF.id_empresa='$id_empresa'
						AND CF.id_sucursal='$id_sucursal'
						AND CF.id_bodega='$filtro_bodega'
						AND (CF.estado = 1 OR CF.estado=2 OR CF.estado=4)
						AND CF.id=CFI.id_entrada_almacen
						AND CF.activo=1
						AND CFI.id_inventario='$id_item'
						AND CFI.activo=1
						$whereEA
					GROUP BY CFI.id_entrada_almacen, CFI.costo_unitario
					ORDER BY CF.fecha_registro ASC";
	$queryCompras = mysql_query($sqlCompras, $link);
	while ($rowCompras = mysql_fetch_assoc($queryCompras)) {

		if($fechaIniRegistros == ''){ $fechaIniRegistros = $rowCompras['fecha']; }
		$consecutivo = $rowCompras['consecutivo'];

		$indice     = $rowCompras['indice']*1;
		$horaCompra = $rowCompras['hora']*1;
		$arrayKardex[$indice][$horaCompra][] = array('cantidad'=>$rowCompras['cantidad'],
														'costo'=>$rowCompras['costo_inventario'],
														'tipo'=>'in',
														'saldo_inicial'=>$rowCompras['id_saldo_inicial'],
														'tipoDocumento'=>'EA',
														'numeroDocumento'=>$consecutivo,
														'descripcion'=>'',
														'tercero'=>$rowCompras['tercero'],
														'fecha'=>$rowCompras['fecha']);
		$prueba .= '-'.$rowCompras['cantidad'];
		$acumSaldo -= $rowCompras['cantidad'];
		// echo '<br>'.$acumSaldo;
	}

	//==================// DEVOLUCIONES VENTAS //==================//
	//*************************************************************//
	$sqlDevVentas = "SELECT DV.cliente AS tercero,
							DV.documento_venta,
							DV.numero_documento_venta,
							DV.consecutivo,
							DV.fecha_finalizacion AS fecha,
						 	REPLACE(DV.fecha_finalizacion,'-','') AS indice,
						 	REPLACE(DV.hora_finalizacion,':','') AS hora,
							DVI.costo_inventario AS costo_inventario,
							SUM(DVI.cantidad) as cantidad
					FROM devoluciones_venta_inventario AS DVI,
						devoluciones_venta AS DV
					WHERE DV.fecha_finalizacion >= '$fecha_consulta'
						AND DV.id_empresa='$id_empresa'
						AND DV.id_sucursal='$id_sucursal'
						AND DV.id_bodega='$filtro_bodega'
						AND (DV.estado = 1 OR DV.estado=4)
						AND DV.id=DVI.id_devolucion_venta
						AND DV.activo=1
						AND DVI.id_inventario='$id_item'
						AND DVI.activo=1
					GROUP BY DVI.id_devolucion_venta, DVI.costo_inventario
					ORDER BY DV.fecha_finalizacion ASC";
	$queryDevVentas = mysql_query($sqlDevVentas, $link);
	while ($rowVentas = mysql_fetch_assoc($queryDevVentas)) {

		$documento = ($rowVentas['documento_venta'] == 'Remision')? 'NDRV': 'NDFV';
		$indice    = $rowVentas['indice']*1;
		$horaVenta = $rowVentas['hora']*1;
		$arrayKardex[$indice][$horaVenta][] = array('cantidad'=>$rowVentas['cantidad'],
														'costo'=>$rowVentas['costo_inventario'],
														'tipo'=>'in',
														'tipoDocumento'=>$documento,
														'numeroDocumento'=>$rowVentas['consecutivo'],
														'descripcion'=>'Cruce '.substr($documento, 2).' #'.$rowVentas['numero_documento_venta'],
														'tercero'=>$rowVentas['tercero'],
														'fecha'=>$rowVentas['fecha']);
		$prueba .= '-'.$rowVentas['cantidad'];
		$acumSaldo += $rowVentas['cantidad'];
		// echo '<br>'.$acumSaldo;
	}

	//=================// DEVOLUCIONES COMPRAS //==================//
	//*************************************************************//
	$sqlDevCompras = "SELECT DC.proveedor AS tercero,
							DC.documento_compra,
							DC.numero_documento_compra,
							DC.consecutivo,
							DC.fecha_finalizacion AS fecha,
						 	REPLACE(DC.fecha_finalizacion,'-','') AS indice,
						 	REPLACE(DC.hora_finalizacion,':','') AS hora,
							DCI.costo_unitario AS costo_inventario,
							SUM(DCI.cantidad) as cantidad
					FROM devoluciones_compra_inventario AS DCI,
						devoluciones_compra AS DC
					WHERE DC.fecha_finalizacion >= '$fecha_consulta'
						AND DC.id_empresa='$id_empresa'
						AND DC.id_sucursal='$id_sucursal'
						AND DC.id_bodega='$filtro_bodega'
						AND (DC.estado = 1 OR DC.estado=4)
						AND DC.id=DCI.id_devolucion_compra
						AND DC.activo=1
						AND DCI.id_inventario='$id_item'
						AND DCI.activo=1
					GROUP BY DCI.id_devolucion_compra, DCI.costo_unitario
					ORDER BY DC.fecha_finalizacion ASC";
	$queryDevCompras = mysql_query($sqlDevCompras, $link);
	while ($rowCompras = mysql_fetch_assoc($queryDevCompras)) {

		$documento  = ($rowCompras['documento_venta'] == 'Remision')? 'NDRC': 'NDFC';
		$indice     = $rowCompras['indice']*1;
		$horaCompra = $rowCompras['hora']*1;
		$arrayKardex[$indice][$horaCompra][] = array('cantidad'=>$rowCompras['cantidad'],
														'costo'=>$rowCompras['costo_inventario'],
														'tipo'=>'out',
														'tipoDocumento'=>$documento,
														'numeroDocumento'=>$rowCompras['consecutivo'],
														'descripcion'=>'Cruce '.substr($documento, 2).' #'.$rowCompras['numero_documento_venta'],
														'tercero'=>$rowCompras['tercero'],
														'fecha'=>$rowCompras['fecha']);
		$prueba .= '+'.$rowCompras['cantidad'];
		$acumSaldo -= $rowCompras['cantidad'];
		// echo '<br>'.$acumSaldo;
	}

	//======================// TRASLADOS //======================//
	//***********************************************************//
	$sqlTraslados   = "SELECT fecha AS dateTime, cantidad, consecutivo, costo, IF(id_sucursal_origen='$id_sucursal' AND id_bodega_origen='$filtro_bodega','out','in') AS tipo
						FROM inventario_totales_traslados
						WHERE activo=1
							AND id_equipo='$id_item'
							AND id_empresa='$id_empresa'
							AND (id_sucursal_origen='$id_sucursal' AND id_bodega_origen='$filtro_bodega'
								OR
								id_sucursal_destino='$id_sucursal' AND id_bodega_destino='$filtro_bodega')";
	$queryTraslados = mysql_query($sqlTraslados,$link);
	while ($row = mysql_fetch_assoc($queryTraslados)) {
		$arrayDateTime = explode(' ', $row['dateTime']);
		$date = $arrayDateTime[0];
		$time = $arrayDateTime[1];

		$indice = str_replace('-', '', $date);
		$hora   = str_replace(':', '', $time);

		$arrayKardex[$indice][$hora][] = array('cantidad'=>$row['cantidad'],
												'costo'=>$row['costo'],
												'tipo'=>$row['tipo'],
												'tipoDocumento'=>'Traslado',
												'numeroDocumento'=>$row['consecutivo'],
												'descripcion'=>'',
												'tercero'=>$row['tercero'],
												'fecha'=>$date);

		if($row['tipo'] == 'in'){
			$prueba .= '+'.($row['cantidad']>0)?$row['cantidad']:0;
			$acumSaldo += ($row['cantidad']>0)?$row['cantidad']:0;
			// echo '<br>'.$acumSaldo;
		}
		else{
			$prueba .= '-'.($row['cantidad']>0)?$row['cantidad']:0;
			$acumSaldo -= ($row['cantidad']>0)?$row['cantidad']:0;
			// echo '<br>'.$acumSaldo;
		}
	}

	//====================// SALDO INICIAL //====================//
	//***********************************************************//
	$sqlTotal = "SELECT IT.familia,
						IT.grupo,
						IT.subgrupo,
						IT.cantidad,
						I.costos,
						I.codigo,
						I.unidad_medida,
						I.code_bar,
						I.nombre_equipo,
						I.fecha_creacion_en_inventario,
						I.precio_venta
				FROM inventario_totales AS IT,
					items AS I
				WHERE IT.id_item='$id_item'
					AND IT.activo=1
					AND IT.id_empresa='$id_empresa'
					AND IT.id_sucursal='$id_sucursal'
					AND IT.id_ubicacion='$filtro_bodega'
					AND IT.inventariable='true'
					AND IT.id_item=I.id
				GROUP BY I.id
				LIMIT 0,1";
	$queryTotal = mysql_query($sqlTotal,$link);
	$arrayTotal = mysql_fetch_assoc($queryTotal);

	$fechaIniKardex = (strtotime($fecha_consulta) >= strtotime($arrayTotal['fecha_creacion_en_inventario']));

	ksort($arrayKardex);

	$contFilas  = 0;
	$bodyInsert = '';
	foreach ($arrayKardex as $indiceDate => $arrayDate) {

		ksort($arrayDate);
		foreach ($arrayDate as $time => $arrayTime) {
			foreach ($arrayTime as $time => $arrayFila) {
				$contFilas++;
				$inCantidad  = '';
				$inValor     = '';
				$inCosto     = '';

				$outCantidad = '';
				$outValor    = '';
				$outCosto    = '';

				//COSTO TOTAL KARDEX
				$costo_total = $arrayFila['cantidad']*1 * $arrayFila['costo']*1;

				$dateDocumento = $arrayFila['fecha'];
				if($arrayFila['tipo'] == 'in'){
					$inCantidad = $arrayFila['cantidad']*1;
					$inValor    = $arrayFila['costo']*1;
					$inCosto    = $costo_total;

				}
				else if($arrayFila['tipo'] == 'out'){
					$outCantidad = $arrayFila['cantidad']*1;
					$outValor    = $arrayFila['costo']*1;
					$outCosto    = $costo_total;
				}

				$descripcion = '<span style="color:blue;">'.$arrayFila['tipoDocumento'].' #'.$arrayFila['numeroDocumento'].'</span> '.$arrayFila['descripcion'].' <span style="font-weight:bold;">'.$arrayFila['tercero'].'</span>';
				$bodyInsert .= '<tr>
									<td >'.$dateDocumento.'</td>
									<td >'.$descripcion.'</td>
									<td >'.$inCantidad.'</td>
									<td >'.$inCosto.'</td>
									<td >'.$outCantidad.'</td>
									<td >'.$outCosto.'</td>
									<td >'.$saldoCantidad.'</td>
									<td >'.$saldoValor.'</td>
									<td >'.$saldoUnitario.'</td>
								</tr>';
			}
		}
	}

?>

<style>
	table{
		font-family : arial,sans-serif;
		font-size   : 12px;
	}

	.title{
		background-color : #2A80B9;
		color            : #fff;
		padding          : 5px;
		font-size        : 14px;
		text-align       : center;
	}
</style>
	<table align="center" style="text-align:center;margin-bottom:10px;">
	    <tr><td style="text-align:center;"><?php echo $_SESSION['NOMBREEMPRESA']?></td></tr>
	    <tr><td style="font-size:13px;text-align:center;"><b>NIT</b> <?php echo $_SESSION['NITEMPRESA']; ?></td></tr>
	    <tr><td style="text-align:center;"><?php echo $_SESSION['NOMBRESUCURSAL']?></td></tr>
	    <tr><td style="width:100%; font-weight:bold; font-size:14px; text-align:center;">KARDEX DE INVENTARIO</td></tr>
        <tr><td style="font-size:11px;text-align:center;">Desde <?php echo $fecha_consulta ?> a la fecha</td></tr>
	</table>

	<table>
		<tr>
			<td class="title">Codigo</td><td><?php echo $arrayTotal['codigo'] ?></td> <td class="title">Codigo de Barras</td><td><?php echo $arrayTotal['code_bar'] ?></td>
		</tr>
		<tr>
			<td class="title">Item</td><td><?php echo $arrayTotal['nombre_equipo'] ?></td> <td class="title">Udidad</td><td><?php echo $arrayTotal['unidad_medida'] ?></td>
		</tr>
		<tr>
			<td class="title">Familia</td><td><?php echo $arrayTotal['familia'] ?></td> <td class="title">Grupo</td><td><?php echo $arrayTotal['grupo'] ?></td>
		</tr>
		<tr>
			<td class="title">Subgrupo</td><td><?php echo $arrayTotal['subgrupo'] ?></td> <td class="title">Precio de Venta</td><td><?php echo $arrayTotal['precio_venta'] ?></td>
		</tr>
	</table>
	<br>
	<table >
		<tr>
			<td rowspan="2" class="title">Fecha</td>
			<td rowspan="2" class="title">Descripcion</td>
			<td colspan="2" class="title">Entradas</td>
			<td colspan="2" class="title">Salidas</td>
		</tr>
		<tr>
			<td class="title" >Cantidad</td>
			<td class="title" >Valor</td>
			<td class="title" >Cantidad</td>
			<td class="title" >Valor</td>
		</tr>
		<?php echo $bodyInsert ?>
	</table>
