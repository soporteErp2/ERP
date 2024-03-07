<?php

	// VALIDACION QUE TODOS LOS ARTICULOS INVENTARIABLES TENGAN CONFIGURADO LA CUENTA INVENTARIO Y COSTOS
	$contNoContabilizacion = 0;
	$consultaCuentas = "SELECT COUNT(CEI.id) AS cont
						FROM compras_entrada_almacen_inventario AS CEI, items AS I
						WHERE CEI.activo = 1
							AND CEI.id_entrada_almacen = '$idDocumento'
							AND CEI.id_inventario= I.id
							AND I.inventariable= 'true'
							AND id_inventario NOT IN (
									SELECT id_items
									FROM items_cuentas
									WHERE activo=1
										AND id_empresa='$id_empresa'
										AND estado='venta'
										AND (descripcion='costo' OR descripcion='contraPartida_costo')
								)
						GROUP BY CEI.activo=1";

	$contNoContabilizacion = mysql_result(mysql_query($consultaCuentas,$link),0,'cont');
	if($contNoContabilizacion > 0){ echo'<script>alert("Aviso!\n Hay articulos inventariables que no tiene configuracion contable.");</script>'; exit; }

	// $sqlConsecutivo      = "SELECT consecutivo,id_cliente FROM ventas_remisiones WHERE activo=1 AND id='$idDocumento' LIMIT 0,1";
	// $queryConsecutivo    = mysql_query($sqlConsecutivo,$link);
	// $id_tercero           = mysql_result($queryConsecutivo,0,'id_cliente');
	// $consecutivo = mysql_result($queryConsecutivo,0,'consecutivo');

	//================================ CONTABILIZACION CUENTAS COLGAAP ================================//
	/***************************************************************************************************/
	$consultaCuentasItems = "SELECT CEI.id,CEI.id_inventario, IC.id_puc, IC.puc, IC.tipo AS estado, CEI.costo_unitario AS costo, CEI.cantidad, IC.descripcion
							FROM compras_entrada_almacen_inventario AS CEI, items_cuentas AS IC
							WHERE CEI.activo = 1
								AND CEI.id_entrada_almacen = '$idDocumento'
								AND CEI.id_inventario = IC.id_items
								AND IC.activo         = 1
								AND IC.estado         = 'venta'
								AND (IC.descripcion   ='costo' OR IC.descripcion='contraPartida_costo')";
	$queryCuentasItems = mysql_query($consultaCuentasItems,$link);
	// $valueInsertContabilizacion = '';
	while ($rowCuentaItems = mysql_fetch_array($queryCuentasItems)) {
		$cuenta          = $rowCuentaItems['puc'];
		$id_item         = $rowCuentaItems['id_inventario'];
		$idDocInventario = $rowCuentaItems['id'];
		$id_puc          = $rowCuentaItems['id_puc'];
		$estado          = $rowCuentaItems['estado'];
		$costo           = $rowCuentaItems['costo'] * $rowCuentaItems['cantidad'];

		$whereCuenta .= ($whereCuenta=='')? " cuenta='$cuenta' " : " OR cuenta='$cuenta' " ;

		$estadoAsiento = ($estado=='debito')? 'haber'  : 'debe' ;

		if(is_nan($arrayAsiento[$cuenta][$estadoAsiento])){ $arrayAsiento[$cuenta][$estadoAsiento] = 0; }
		$arrayAsiento[$cuenta][$estadoAsiento] += $costo;

		$arrayCuenta['debito']  = 0;
		$arrayCuenta['credito'] = 0;

		// $valueInsertContabilizacion .= "('$id_item',
		// 								'$id_puc',
		// 								'$cuenta',
		// 								'".$rowCuentaItems['estado']."',
		// 								'".$rowCuentaItems['descripcion']."',
		// 								'$idDocumento',
		// 								'EA',
		// 								'$id_empresa',
		// 								'$id_sucursal',
		// 								'$idBodega'),";
	}

	// CONSULTAR SI LAS CUENTAS MUEVEN CENTRON DE COSTO CENTRO DE COSTOS
	$sql   = "SELECT cuenta,centro_costo FROM puc WHERE activo=1 AND id_empresa=$id_empresa AND ($whereCuenta)";
	$query = mysql_query($sql,$link);
	while ($row=mysql_fetch_array($query)) {
		$cuenta = $row['cuenta'];
		$ccos   = $row['centro_costo'];
		$arrayCuentaCcos[$cuenta]=$ccos;
	}

	$contAsientos  = 0;
	$globalDebito  = 0;
	$globalCredito = 0;
	$valueInsertAsientos = '';
	foreach ($arrayAsiento as $cuenta => $arrayCuenta) {
		$contAsientos++;
		$globalDebito  += $arrayCuenta['debe'];
		$globalCredito += $arrayCuenta['haber'];
		$idCcos = ($arrayCuentaCcos[$cuenta]=='Si')? $id_centro_costo_EA : '' ;

		$valueInsertAsientos .= "('$idDocumento',
									'$consecutivo',
									'EA',
									'Entrada de Almacen (Ajuste de Inventario)',
									'$idDocumento',
									'$consecutivo',
									'EA',
									'$fecha',
									'".$arrayCuenta['debe']."',
									'".$arrayCuenta['haber']."',
									'$cuenta',
									'$id_tercero',
									'$idCcos',
									'$id_sucursal',
									'$id_empresa'
								),";
	}
	$globalDebito = round($globalDebito,$_SESSION['DECIMALESMONEDA']);
	$globalCredito = round($globalCredito,$_SESSION['DECIMALESMONEDA']);
	if($contAsientos == 0){ return; }
	else if($globalDebito != $globalCredito){
		echo '<script>
				alert("Aviso.\nNo se cumple doble partida, Confirme su configuracion en el modulo panel de control.")
				document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
			</script>';
			exit;
		}

	//INSERT ASIENTOS_COLGAAP Y ASIENTOS_POR_ARTICULO
	$valueInsertAsientos = substr($valueInsertAsientos, 0, -1);
	$sqlInsertColgaap   = "INSERT INTO asientos_colgaap (
								id_documento,
								consecutivo_documento,
								tipo_documento,
								tipo_documento_extendido,
								id_documento_cruce,
								numero_documento_cruce,
								tipo_documento_cruce,
								fecha,
								debe,
								haber,
								codigo_cuenta,
								id_tercero,
								id_centro_costos,
								id_sucursal,
								id_empresa)
							VALUES $valueInsertAsientos";
	$queryInsertColgaap = mysql_query($sqlInsertColgaap,$link);
	if(!$queryInsertColgaap){
		echo'<script>
				alert("Error!\nNo se insertaron los asientos COLGAAP, intentelo de nuevo, si el problema continua, comuniquese con el administrador del sistema");
				document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
			</script>';
			exit;
		}

	// CUENTAS SIMULTANEAS DE LAS CUENTAS DEL DOCUMENTO
	contabilizacionSimultanea($idDocumento,'EA',$id_sucursal,$id_empresa,$link);

	// $valueInsertContabilizacion = substr($valueInsertContabilizacion, 0, -1);
	// $sqlContabilizar     = "INSERT INTO contabilizacion_compra_venta (id_item,id_puc,codigo_puc,caracter,descripcion,id_documento,tipo_documento,id_empresa,id_sucursal,id_bodega) VALUES $valueInsertContabilizacion";
	// $queryContabilizar   = mysql_query($sqlContabilizar,$link);


	//================================ CONTABILIZACION CUENTAS NIIF ================================//
	/************************************************************************************************/
	$consultaCuentasItems = "SELECT CEI.id,CEI.id_inventario, IC.id_puc, IC.puc, IC.tipo AS estado, CEI.costo_inventario AS costo, CEI.cantidad, IC.descripcion
							FROM compras_entrada_almacen_inventario AS CEI, items_cuentas_niif AS IC
							WHERE CEI.activo = 1
								AND CEI.id_entrada_almacen = '$idDocumento'
								AND CEI.id_inventario = IC.id_items
								AND IC.activo         = 1
								AND IC.estado         = 'venta'
								AND (IC.descripcion   ='costo' OR IC.descripcion='contraPartida_costo')";
	$queryCuentasItems = mysql_query($consultaCuentasItems,$link);
	$whereCuenta = '';
	while ($rowCuentaItems = mysql_fetch_array($queryCuentasItems)) {
		$cuenta          = $rowCuentaItems['puc'];
		$id_item         = $rowCuentaItems['id_inventario'];
		$idDocInventario = $rowCuentaItems['id'];
		$id_puc          = $rowCuentaItems['id_puc'];
		$estado          = $rowCuentaItems['estado'];
		$costo           = $rowCuentaItems['costo'] * $rowCuentaItems['cantidad'];

		$estadoAsiento = ($estado=='debito')? 'debe' : 'haber';
		$whereCuenta .= ($whereCuenta=='')? " cuenta='$cuenta' " : " OR cuenta='$cuenta' " ;

		if(is_nan($arrayAsientoNiif[$cuenta][$estadoAsiento])){ $arrayAsientoNiif[$cuenta][$estadoAsiento] = 0; }
		$arrayAsientoNiif[$cuenta][$estadoAsiento] += $costo;

		$arrayCuenta['debito']  = 0;
		$arrayCuenta['credito'] = 0;

		// $valueInsertContabilizacion .= "('$id_item',
		// 								'$id_puc',
		// 								'$cuenta',
		// 								'".$rowCuentaItems['estado']."',
		// 								'".$rowCuentaItems['descripcion']."',
		// 								'$idDocumento',
		// 								'EA',
		// 								'$id_empresa',
		// 								'$id_sucursal',
		// 								'$idBodega'),";
	}

	// CONSULTAR SI LAS CUENTAS MUEVEN CENTRON DE COSTO CENTRO DE COSTOS
	$sql   = "SELECT cuenta,centro_costo FROM puc_niif WHERE activo=1 AND id_empresa=$id_empresa AND ($whereCuenta)";
	$query = mysql_query($sql,$link);
	while ($row=mysql_fetch_array($query)) {
		$cuenta = $row['cuenta'];
		$ccos   = $row['centro_costo'];
		$arrayCuentaCcosNiif[$cuenta]=$ccos;
	}

	$contAsientos        = 0;
	$globalDebito        = 0;
	$globalCredito       = 0;
	$valueInsertAsientos = '';
	$idCcos              = '';

	foreach ($arrayAsientoNiif as $cuenta => $arrayCuenta) {
		$contAsientos++;
		$globalDebito  += $arrayCuenta['debe'];
		$globalCredito += $arrayCuenta['haber'];
		$idCcos = ($arrayCuentaCcosNiif[$cuenta]=='Si')? $id_centro_costo_EA : '' ;

		$valueInsertAsientos .= "('$idDocumento',
									'$consecutivo',
									'EA',
									'Entrada de Almacen (Ajuste de Inventario)',
									'$idDocumento',
									'$consecutivo',
									'EA',
									'$fecha',
									'".$arrayCuenta['debe']."',
									'".$arrayCuenta['haber']."',
									'$cuenta',
									'$id_tercero',
									'$idCcos',
									'$id_sucursal',
									'$id_empresa'
								),";
	}
	$globalDebito = round($globalDebito,$_SESSION['DECIMALESMONEDA']);
	$globalCredito = round($globalCredito,$_SESSION['DECIMALESMONEDA']);
	if($contAsientos == 0){
		echo'<script>
				alert("Aviso!\nLos articulos no tienen una configuracion contable.");
				document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
			</script>';
		exit;
	}
	else if($globalDebito != $globalCredito){
		echo '<script>
				alert("Aviso.\nNo se cumple doble partida, Confirme su configuracion en el modulo panel de control.")
				document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
			</script>';
			exit;
	}

	//INSERT ASIENTOS_COLGAAP Y ASIENTOS_POR_ARTICULO
	$valueInsertAsientos = substr($valueInsertAsientos, 0, -1);
	$sqlInsertColgaap   = "INSERT INTO asientos_niif (
								id_documento,
								consecutivo_documento,
								tipo_documento,
								tipo_documento_extendido,
								id_documento_cruce,
								numero_documento_cruce,
								tipo_documento_cruce,
								fecha,
								debe,
								haber,
								codigo_cuenta,
								id_tercero,
								id_centro_costos,
								id_sucursal,
								id_empresa)
							VALUES $valueInsertAsientos";
	$queryInsertColgaap = mysql_query($sqlInsertColgaap,$link);
	if(!$queryInsertColgaap){
		echo'<script>
				alert("Error!\nNo se insetaron los asientos NIIF, intentelo de nuevo, si el problema continua comuniquese con eol administrador del sistema);
			</script>';
		exit;
	}

	// $valueInsertContabilizacion = substr($valueInsertContabilizacion, 0, -1);
	// $sqlContabilizar   = "INSERT INTO contabilizacion_compra_venta_niif (id_item,id_puc,codigo_puc,caracter,descripcion,id_documento,tipo_documento,id_empresa,id_sucursal,id_bodega)
	// 						VALUES $valueInsertContabilizacion";
	// $queryContabilizar = mysql_query($sqlContabilizar,$link);

?>
