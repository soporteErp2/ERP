<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

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
							VF.fecha_registro AS fecha,
							REPLACE(VF.fecha_registro,'-','') AS indice,
							VFI.costo_unitario,
							SUM(VFI.cantidad) as cantidad
					FROM ventas_remisiones_inventario AS VFI,
						ventas_remisiones AS VF
					WHERE VF.fecha_registro >= '$fecha_consulta'
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
		$horaVenta = '000000';
		$arrayKardex[$indice][$horaVenta][] = array('cantidad'=>$rowVentas['cantidad'],
														'costo'=>$rowVentas['costo_unitario'],
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

	//=== POS
	$sqlVentas = "SELECT
						VP.cliente AS tercero,
						VP.consecutivo,
						VP.fecha_documento AS fecha,
					 	REPLACE(VP.fecha_documento,'-','') AS indice,
					 	REPLACE(VP.hora_documento,':','') AS hora,
						VPI.costo,
						SUM(VPI.cantidad) as cantidad
					FROM ventas_pos_inventario_receta AS VPI,
						ventas_pos AS VP
					WHERE
						VP.fecha_documento       >='$fecha_consulta'
						AND VP.id_empresa     = '$id_empresa'
						AND VP.id_sucursal    = '$id_sucursal'
						AND VP.id_bodega      = '$filtro_bodega'
						AND (VP.estado        = 1 OR VP.estado=4)
						AND VP.id             = VPI.id_factura_venta
						AND VP.activo         = 1
						AND VPI.id_inventario = '$id_item'
						AND VPI.activo        = 1
					GROUP BY VPI.id_factura_venta, VPI.costo_inventario
					ORDER BY VP.fecha_inicio ASC";
	$queryVentas = mysql_query($sqlVentas, $link);
	while ($rowVentas = mysql_fetch_assoc($queryVentas)) {

		$indice    = $rowVentas['indice']*1;
		$horaVenta = $rowVentas['hora']*1;
		$arrayKardex[$indice][$horaVenta][] = array(
														'cantidad'        => $rowVentas['cantidad'],
														'costo'           => $rowVentas['costo_inventario'],
														'tipo'            => 'out',
														'saldo_inicial'   => $rowVentas['id_saldo_inicial'],
														'tipoDocumento'   => 'FV',
														'numeroDocumento' => $rowVentas['consecutivo'],
														'descripcion'     => '',
														'tercero'         => $rowVentas['tercero'],
														'fecha'           => $rowVentas['fecha']
													);
		$prueba .= '+'.$rowVentas['cantidad'];
		$acumSaldo += $rowVentas['cantidad'];
		// echo '<br>'.$acumSaldo;
	}
	//=======================// COMPRAS //=======================//
	//***********************************************************//
	$sqlCompras = "SELECT
							CF.id,
							CF.proveedor AS tercero,
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

	$whereEA =($whereEA<>'')? " AND $whereEA " : "" ;

	$sqlCompras = "SELECT CF.proveedor AS tercero,
							CF.consecutivo,
							CF.fecha_registro AS fecha,
							CFI.costo_unitario AS costo_inventario,
							SUM(CFI.cantidad) as cantidad,
						 	REPLACE(CF.fecha_registro,'-','') AS indice
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
						$whereEA
						AND CFI.activo=1
					GROUP BY CFI.id_entrada_almacen, CFI.costo_unitario
					ORDER BY CF.fecha_registro ASC";
	$queryCompras = mysql_query($sqlCompras, $link);
	while ($rowCompras = mysql_fetch_assoc($queryCompras)) {

		if($fechaIniRegistros == ''){ $fechaIniRegistros = $rowCompras['fecha']; }
		$consecutivo = $rowCompras['consecutivo'];

		$indice     = $rowCompras['indice']*1;
		$horaCompra = '000000';
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


	//----------------- POS --------------------//
	$select_pos_tempo = "SELECT id_pos, 
					cantidad, costo
					FROM ventas_pos_inventario_receta
					WHERE activo = 1
					AND id_item = '$id_item'
					AND id_empresa = '$id_empresa'";
	$sql_pos_tempo = "CREATE TEMPORARY TABLE ventas_pos_inventario_receta_tempo ".$select_pos_tempo;
	$query_pos_tempo = mysql_query($sql_pos_tempo, $link);


	$select_log_tempo = "SELECT id_documento
					FROM
						inventario_totales_log
					WHERE
						activo = 1
						AND tipo_documento='POS'
						AND id_item = '$id_item'
						AND id_empresa = '$id_empresa'
						AND id_ubicacion = '$filtro_bodega'";
	$sql_log_tempo = "CREATE TEMPORARY TABLE inventario_totales_log_tempo ".$select_log_tempo;
	$query_log_tempo = mysql_query($sql_log_tempo, $link);
	
	$sql_pos	  = "SELECT 
					SUM(VPIRT.cantidad) as cantidad,
					VP.consecutivo,
					VP.prefijo,
					VPIRT.costo,
					REPLACE(VP.fecha_documento,'-','') AS indice,
					REPLACE(VP.hora_documento,':','') AS hora,
					VP.fecha_documento AS fecha,
					VP.usuario
					FROM
						ventas_pos AS VP
						INNER JOIN ventas_pos_inventario_receta_tempo AS VPIRT ON VPIRT.id_pos = VP.id
						INNER JOIN inventario_totales_log_tempo AS ITLT ON VP.id = ITLT.id_documento
					WHERE
						VP.fecha_documento >= '$fecha_consulta'
						AND (VP.estado = 1 OR VP.estado = 2)
					GROUP BY VP.id";
	//Falta agregar el filtro por bodega
	$query_pos = mysql_query($sql_pos, $link);

	while ($row_pos = mysql_fetch_assoc($query_pos)) {

		$documento  = ($row_pos['prefijo'] != '')? 'POS': 'Cheque cuenta';
		$indice     = $row_pos['indice']*1;
		$hora_pos = $row_pos['hora']*1;
		$arrayKardex[$indice][$hora_pos][] = array('cantidad'=>$row_pos['cantidad'],
														'costo'=>$row_pos['costo'],
														'tipo'=>'out',
														'tipoDocumento'=>$documento,
														'numeroDocumento'=>$row_pos['consecutivo'],
														'descripcion'=>'',
														'tercero'=>$row_pos['usuario'],
														'fecha'=>$row_pos['fecha']);
		$prueba .= '+'.$row_pos['cantidad'];
		$acumSaldo -= $row_pos['cantidad'];
		// echo '<br>'.$acumSaldo;
	}
	
	$sql_pos_tempo = "DROP TEMPORARY TABLE ventas_pos_inventario_receta_tempo";
	$queryPosTempoTable = mysql_query($sql_pos_tempo,$link);

	$sql_log_tempo = "DROP TEMPORARY TABLE inventario_totales_log_tempo";
	$queryLogTempoTable = mysql_query($sql_log_tempo,$link);


	//======================// TRASLADOS //======================//
	//***********************************************************//
	$sql="SELECT
				IT.id,
				IT.fecha_documento,
				REPLACE(IT.fecha_documento,'-','') AS indice,
				IT.usuario,
				IT.consecutivo,
				ITU.costo_unitario,
				SUM(ITU.cantidad) AS cantidad
			FROM inventario_traslados AS IT INNER JOIN inventario_traslados_unidades AS ITU ON ITU.id_traslado=IT.id
			WHERE IT.activo=1
			AND IT.id_empresa=$id_empresa
			AND IT.id_sucursal=$id_sucursal
			AND IT.id_bodega=$filtro_bodega
			AND IT.fecha_documento>='$fecha_consulta'
			AND IT.estado=1
			AND ITU.id_inventario=$id_item
			GROUP BY IT.id,ITU.costo_unitario
			ORDER BY IT.fecha_documento,IT.consecutivo DESC
			";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_assoc($query)) {
		$indice     = $row['indice']*1;
		$horaCompra = '000000';
		$arrayKardex[$indice][$horaCompra][] = array(
														'cantidad'        =>$row['cantidad'],
														'costo'           =>$row['costo_unitario'],
														'tipo'            =>'out',
														'tipoDocumento'   =>'TDI',
														'numeroDocumento' =>$row['consecutivo'],
														'descripcion'     =>'Traslado de inventario',
														'tercero'         =>$row['usuario'],
														'fecha'           =>$row['fecha_documento']
													);
	}

	$sql="SELECT
				IT.id,
				IT.fecha_documento,
				REPLACE(IT.fecha_documento,'-','') AS indice,
				IT.usuario,
				IT.consecutivo,
				ITU.costo_unitario,
				SUM(ITU.cantidad) AS cantidad
			FROM inventario_traslados AS IT INNER JOIN inventario_traslados_unidades AS ITU ON ITU.id_traslado=IT.id
			WHERE IT.activo=1
			AND IT.id_empresa=$id_empresa
			AND IT.id_sucursal_traslado=$id_sucursal
			AND IT.id_bodega_traslado=$filtro_bodega
			AND IT.fecha_documento>='$fecha_consulta'
			AND IT.estado=1
			AND ITU.id_inventario=$id_item
			GROUP BY IT.id,ITU.costo_unitario
			ORDER BY IT.fecha_documento,IT.consecutivo DESC
			";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_assoc($query)) {
		$indice     = $row['indice']*1;
		$horaCompra = '000000';
		$arrayKardex[$indice][$horaCompra][] = array(
														'cantidad'        =>$row['cantidad'],
														'costo'           =>$row['costo_unitario'],
														'tipo'            =>'in',
														'tipoDocumento'   =>'TDI',
														'numeroDocumento' =>$row['consecutivo'],
														'descripcion'     =>'Traslado de inventario',
														'tercero'         =>$row['usuario'],
														'fecha'           =>$row['fecha_documento']
													);
	}

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


	echo '<script>
			var prueba = 0'.$prueba.';
			console.log(prueba)
		</script>';

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
	// echo $saldoKardex = ($arrayTotal['cantidad'] * 1) + $acumSaldo;
	// echo '<br>'.$acumSaldo;

	ksort($arrayKardex);
	//echo json_encode($arrayKardex);
	$contFilas  = 0;
	$bodyInsert = '';
	foreach ($arrayKardex as $indiceDate => $arrayDate) {
		//echo json_encode($arrayKardex);
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
				$costo_total = $arrayFila['costo']*1;

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

				$unidadesIngresadas += $inCantidad;
				$unidadesSacadas    += $outCantidad;

				$descripcion = '<span style="color:blue;">'.$arrayFila['tipoDocumento'].' #'.$arrayFila['numeroDocumento'].'</span> '.$arrayFila['descripcion'].' <span style="font-weight:bold;">'.$arrayFila['tercero'].'</span>';
				$bodyInsert .= '<div style="overflow:hidden;">
									<div class="campo0" id="cont_fila_grilla_'.$nameGrilla.'_'.$idFilaGrilla.'">'.$contFilas.'</div>
									<div class="campo4" style="width:75px;">'.$dateDocumento.'</div>
									<div class="campo3" style="width:250px; text-align:">'.$descripcion.'</div>
									<div class="campo1" style="padding-right:2px; width:77px;">'.$inCantidad.'</div>
									<div class="campo1" style="padding-right:2px; width:78px;">'.$inCosto.'</div>
									<div class="campo1" style="padding-right:2px; width:77px;">'.$outCantidad.'</div>
									<div class="campo1" style="padding-right:2px; width:78px;">'.$outCosto.'</div>
									<div class="campo1" style="padding-right:2px; width:77px;">'.$saldoCantidad.'</div>
									<div class="campo1" style="padding-right:2px; width:78px;">'.$saldoValor.'</div>
									<div class="campo1" style="padding-right:2px; width:90px;">'.$saldoUnitario.'</div>
								</div>';
			}
		}
	}

	echo'<div style="overflow:hidden; margin:15px; width:calc(100% - 30px); height:100px;">
			<div class="campoHead">
				<div><b>Item</b></div>
				<div>'.$arrayTotal['nombre_equipo'].'</div>
			</div>
			<div class="campoHead">
				<div><b>Codigo</b></div>
				<div>'.$arrayTotal['codigo'].'</div>
			</div>
			<div class="campoHead">
				<div style="width:106px;"><b>Codigo de barras</b></div>
				<div style="width:94px;">'.$arrayTotal['code_bar'].'</div>
			</div>

			<div class="campoHead">
				<div><b>Familia</b></div>
				<div>'.$arrayTotal['familia'].'</div>
			</div>
			<div class="campoHead">
				<div><b>Grupo</b></div>
				<div>'.$arrayTotal['grupo'].'</div>
			</div>
			<div class="campoHead">
				<div style="width:65px;"><b>SubGrupo</b></div>
				<div style="width:135px;">'.$arrayTotal['subgrupo'].'</div>
			</div>

			<div class="campoHead">
				<div style="width:106px;"><b>Unidad de Medida</b></div>
				<div style="width:94px;">'.$arrayTotal['unidad_medida'].'</div>
			</div>
			<div class="campoHead">
				<div style="width:106px;"><b>Precio de Venta</b></div>
				<div style="width:94px;">'.$arrayTotal['precio_venta'].'</div>
			</div>
			<div class="campoHead">
				<div style="width:106px;"><b>Total ingresos </b></div>
				<div style="width:94px;">'.$unidadesIngresadas.'</div>
			</div>
			<div class="campoHead">
				<div style="width:106px;"><b>Total Salidas</b></div>
				<div style="width:94px;">'.$unidadesSacadas.'</div>
			</div>
			<div class="campoHead">
				<div style="width:106px;"><b>Saldo unidades</b></div>
				<div style="width:94px;">'.($unidadesIngresadas-$unidadesSacadas).'</div>
			</div>
		</div>';


?>

<style>
	#contenedor_formulario{
		overflow    : hidden;
		width       : calc(100% - 30px);
		height      : calc(100% - 120px);
		margin      : 15px;
		margin-top  : 0px;
		font-family : Tahoma, Geneva, sans-serif;
	}

	/*TOOLBAR DE LA BUSQUEDA DE LA GRILLA MANUAL*/
	.toolbar_grilla_manual{
		overflow   : hidden;
		width      : 100%;
		height     : 34px;
		margin-top : 5px;
		background-repeat : no-repeat;
	}

	.div_render_fila{
		float       : left;
		margin-left : -21px;
		width       : 20px;
		height      : 20px;
		overflow    : hidden;
	}

	.div_input_busqueda_grilla_manual{
		height                 : 28px;
		padding-top            : 5px;
		padding-left           : 7px;
		border-top-left-radius : 5;
		float                  : left;
		background-color       : #F3F3F3;
		border                 : 1px solid #b3b3b3;
		width                  : 220px;
	}

	.div_input_busqueda_grilla_manual>input{
		border            : 1px solid #D4D4D4;
		background-image  : url(../../temas/clasico/images/BotonesTabs/buscar16.png);
		background-repeat : no-repeat;
		padding-left      : 0 0 0 27px;
		border-radius     : 3px;
		font-size         : 12px;
		min-height        : 22px;
		width             : 215px;
		height            : 20px;
		margin-top        : 3px;
	}

	.div_img_actualizar_datos_grilla_manual{
		float  : left;
		width  : 35px;
		height : 40px;
		border: 1px solid #b3b3b3;
		border-left: none;
		border-top-right-radius: 10px;
		background-color: #F3F3F3;
	}

	.div_img_actualizar_datos_grilla_manual>img{
		padding : 5px 0 0 5px;
		cursor  : pointer;
		margin  : 3px;
	}

	.contenedor_tabla_boletas{
		overflow-x              : auto;
		overflow-y              : hidden;
		width                   : calc(100% - 4px);
		height                  : 97%;
		border                  : 1px solid #b3b3b3;
		border-bottom           : none;
		border-top-left-radius  : 4px;
		border-top-right-radius : 4px;
		background-color        : #F3F3F3;
		webkit-box-shadow       : 2px 2px 4px #666;
		-moz-box-shadow         : 2px 2px 2px #666;
		box-shadow              : 2px 2px 2px #666;
	}

	.fila_formulario{
		min-height : 30px;
		margin-top : 5px;
		overflow   : hidden;
	}

	.divLabelFormulario{
		float : left;
		width : 100px;
	}

	.divInputFormulario{
		float       : left;
		width       : calc(100% - 100px - 20px - 10px);
		margin-left : 5px;
	}

	.divInputFormulario input, .divInputFormulario select, .divInputFormulario textarea{
		min-height            : 22px;
		width                 : 100%;
		border                : 0px solid #999;
		-webkit-border-radius : 2px;
		border-radius         : 2px;
		-webkit-box-shadow    : 0px 0px 3px #666;
		-moz-box-shadow       : 1px 1px 3px #999;
		box-shadow            : 1px 1px 3px #999;
	}

	.loadSaveFormulario{
		overflow : hidden;
		width    : 100%;
		height   : 20px;
	}

	.divLoadCedula, .divLoadBoleta{
		float : left;
		width : 20px;
	}

	.headTablaBoletas{
		overflow      : hidden;
		font-weight   : bold;
		width         : 100%;
		border-bottom : 1px solid #d4d4d4;
		height        : 42px;
		min-width     : 950px;
	}

	.headTablaBoletas div{
		background-color : #F3F3F3;
		padding-top      : 1px;
		font-size        : 10.5px;
		border-color: #D4D4D4;
	}

	.bodyTablaBoletas{
		overflow-x       : hidden;
		overflow-y       : auto;
		width            : 100%;
		min-width        : 950px;
		height           : calc(100% - 43px);
		background-color : #FFF;
		border-bottom    : 1px solid #d4d4d4;
	}

	.bodyTablaBoletas > div{
		overflow      : hidden;
		height        : 22px;
		border-bottom : 1px solid #d4d4d4;
	}

	.bodyTablaBoletas > div > div { height: 18px; padding-top: 4px; }
	.bodyTablaBoletas >  div:hover { background-color: #E3EBFC; }

	.filaGrilla{ cursor: hand; }

	.filaGrilla input[type=text]{
		border : 0px;
		width  : 90%;
		height : 100%;
	}

	.filaGrilla input[type=text]:focus { background: #FFF; }

	.campo0{
		height           : 100%;
		overflow         : hidden;
		float            : left;
		width            : 28px;
		text-indent      : 5px;
		border-right     : 1px solid #d4d4d4;
		background-color : #F3F3F3;
		white-space      : nowrap;
		text-overflow    : ellipsis;
	}

	.campo1{
		height        : 100%;
		overflow      : hidden;
		float         : left;
		width         : 100px;
		text-indent   : 5px;
		border-right  : 1px solid #d4d4d4;
		white-space   : nowrap;
		text-overflow : ellipsis;
		text-align    : right;
	}

	.campo3{
		height           : 100%;
		overflow         : hidden;
		float            : left;
		width            : 28px;
		text-indent      : 5px;
		border-right     : 1px solid #d4d4d4;
		white-space      : nowrap;
		text-overflow    : ellipsis;
	}

	.campo4{
		height: 100%;
		overflow     : hidden;
		float        : left;
		width        : 60px;
		text-align   : center;
		border-right : 1px solid #d4d4d4;
	}

	.divObs{ width: 20px; }

	.contenedorPaginacion{
		float                      : left;
		width                      : calc(100% - 4px);
		background-color           : #F3F3F3;
		border                     : 1px solid #b3b3b3;
		border-top                 : 1px solid #D4D4D4;
		border-bottom-right-radius : 4px;
		border-bottom-left-radius  : 4px;
		webkit-box-shadow          : 2px 2px 4px #666;
		-moz-box-shadow            : 2px 2px 2px #666;
		box-shadow                 : 2px 2px 2px #666;
	}

	.subTitle1{
		float              : left;
		width              : 80px;
		height             : 50%;
		border-right-width : 1px;
		border-right-style : solid;
		box-sizing         : border-box;
		-moz-box-sizing    : border-box;
		-webkit-box-sizing : border-box;
	}

	.subTitle{
		float  : left;
		width  : 80px;
		height : 50%;
	}

	.title{
		width               : 100%;
		height              : 50%;
		float               : left;
		border-bottom-width : 1px;
		border-bottom-style : solid;
	}

	.campoHead{ float:left; width:208px; overflow:hidden; margin:3px; height: 20px;}
	.campoHead > div:nth-child(1){
		float            : left;
		width            : 56px;
		background-color : #d4d4d4;
		padding          : 2px;
		height           : 100%;
	}
	.campoHead > div:nth-child(2){
		float            : left;
		width            : 144px;
		background-color : #E7E5E5;
		padding          : 2px;
		height           : 100%;
	}
	.x-panel-body{ border-top-width: 1px; }



</style>


<div id="contenedor_formulario">
	<div class="contenedor_tabla_boletas">
		<div class="headTablaBoletas">
			<div class="campo0">&nbsp;</div>
			<div class="campo4" style="width:75px; padding-top: 10px;">Fecha</div>
			<div class="campo4" style="width:250px; padding-top: 10px;">Descripcion</div>
			<div class="campo4" style="width:160px;">
				<div class="title">ENTRADAS</div>
				<div class="subTitle1">Cantidad</div>
				<div class="subTitle">Valor</div>
			</div>
			<div class="campo4" style="width:160px;">
				<div class="title">SALIDAS</div>
				<div class="subTitle1">Cantidad</div>
				<div class="subTitle">Valor</div>
			</div>
			<div class="campo4" style="width:160px;" >
				<div class="title">SALDOS</div>
				<div class="subTitle1">Cantidad</div>
				<div class="subTitle">Valor</div>
			</div>
			<div class="campo4" style="width:92px; padding-top: 10px;">Costo<br>Unitario</div>
		</div>
		<div id="bodyTablaBoletas<?php echo $nameGrilla; ?>" class="bodyTablaBoletas"><?php echo $bodyInsert; ?></div>
	</div>
</div>

<script>

	function descargarExcelKardex(){
		var fecha_consulta = document.getElementById('filtro_fecha_kardex').value;
        window.open("inventario_unidades/kardex/exportar_kardex.php?id=<?php echo $id ?>&id_item=<?php echo $id_item ?>&filtro_bodega=<?php echo $filtro_bodega ?>&fecha_consulta=<?php echo $fecha_consulta ?>");
    }

    // descargarExcel();

</script>
