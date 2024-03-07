<?php

	//====================================== FUNCION UNICO RANDOMICO ==============================================//
	//*************************************************************************************************************//
	function responseUnicoRanomico(){

		//Si es un Nuevo Documento Maestro -->
        $random1 = time();             //GENERA PRIMERA PARTE DEL ID UNICO

        $chars = array(
                'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H',
                'I', 'J', 'K', 'L', 'M', 'N', 'O',
                'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W',
                'X', 'Y', 'Z', '1', '2', '3', '4', '5',
                '6', '7', '8', '9', '0'
                );
        $max_chars = count($chars) - 1;
        srand((double) microtime()*1000000);
        $random2 = '';
        for($i=0; $i < 6; $i++){ $random2 = $random2 . $chars[rand(0, $max_chars)]; }

    	$randomico = $random1.''.$random2; // ID UNICO
    	return $randomico;
	}

	//=========================== FUNCION PARA VALIDAR LA CANTIDAD DE ARTICULOS ===================================//
	//*************************************************************************************************************//
	function validaCantidadArticulos($idRemision,$idItem,$id_sucursal,$idBodega,$saldoItem,$conexion){
		//CONSULTAMOS SI HAY ARTICULOS REPETIDOS, SI LOS HAY, LOS AGRUPAMOS SACANDO LA SUMA DE SUS CANTIDADES
		$sqlArticulo = "SELECT
							COUNT(IT.id),
							IT.id_item,
							IT.cantidad AS cantidad_inventario,
							VRI.cantidad
						FROM
							ventas_remisiones_inventario AS VRI,
							inventario_totales AS IT
						WHERE VRI.activo = 1
							AND VRI.id_remision_venta = '$idRemision'
							AND VRI.id_inventario = $idItem
							AND IT.inventariable = 'true'
							AND IT.id_item = $idItem
							AND IT.id_sucursal = '$id_sucursal'
							AND IT.id_ubicacion = '$idBodega'
						LIMIT 0,1";

		$queryArticulo       = mysql_query($sqlArticulo,$conexion);
		$cantidad_documento  = mysql_result($queryArticulo,0,'cantidad');
		$cantidad_inventario = mysql_result($queryArticulo,0,'cantidad_inventario');

		if ($cantidad_documento > $cantidad_inventario || !$queryArticulo){ return false; }
		return true;
	}

	//================================== FUNCIONA CONTABILIZACION REMISION ========================================//
	//*************************************************************************************************************//
	function contabilidad($idRemision,$id_sucursal,$idBodega,$accion,$idEmpresa,$link){
		if ($accion=='contabilizar') {		//LA REMISION MUEVE LAS CUENTAS  1435 -> CREDITO Y 6135 -> DEBITO
			$sqlConsecutivo      = "SELECT consecutivo,id_cliente FROM ventas_remisiones WHERE activo=1 AND id='$idRemision' LIMIT 0,1";
			$queryConsecutivo    = mysql_query($sqlConsecutivo,$link);
			$idCliente           = mysql_result($queryConsecutivo,0,'id_cliente');
			$consecutivoRemision = mysql_result($queryConsecutivo,0,'consecutivo');

			//================================ CONTABILIZACION CUENTAS COLGAAP ================================//
			/***************************************************************************************************/
			$consultaCuentasItems = "SELECT VRI.id,VRI.id_inventario, IC.id_puc, IC.puc, IC.tipo AS estado, VRI.costo_inventario AS costo, VRI.cantidad, IC.descripcion
									FROM ventas_remisiones_inventario AS VRI, items_cuentas AS IC
									WHERE VRI.activo = 1
										AND VRI.id_remision_venta = '$idRemision'
										AND VRI.id_inventario = IC.id_items
										AND IC.activo         = 1
										AND IC.estado         = 'venta'
										AND (IC.descripcion   ='costo' OR IC.descripcion='contraPartida_costo')";
			$queryCuentasItems = mysql_query($consultaCuentasItems,$link);
			$valueInsertContabilizacion = '';

			$arrayAsiento = array();
			while ($rowCuentaItems = mysql_fetch_array($queryCuentasItems)) {
				$cuenta          = $rowCuentaItems['puc'];
				$id_item         = $rowCuentaItems['id_inventario'];
				$idDocInventario = $rowCuentaItems['id'];
				$id_puc          = $rowCuentaItems['id_puc'];
				$estado          = $rowCuentaItems['estado'];
				$costo           = $rowCuentaItems['costo'] * $rowCuentaItems['cantidad'];

				$estadoAsiento = ($estado=='debito')? 'debe' : 'haber';

				if(!isset($arrayAsiento[$cuenta]['debe'])){ $arrayAsiento[$cuenta]['debe'] = 0; }
				if(!isset($arrayAsiento[$cuenta]['haber'])){ $arrayAsiento[$cuenta]['haber'] = 0; }

				$arrayAsiento[$cuenta][$estadoAsiento] += $costo;

				$valueInsertContabilizacion .= "('$id_item',
												'$id_puc',
												'$cuenta',
												'".$rowCuentaItems['estado']."',
												'".$rowCuentaItems['descripcion']."',
												'$idRemision',
												'RV',
												'$idEmpresa',
												'$id_sucursal',
												'$idBodega'),";
			}
			$contAsientos  = 0;
			$globalDebito  = 0;
			$globalCredito = 0;
			$valueInsertAsientos = '';
			foreach ($arrayAsiento as $cuenta => $arrayCuenta) {
				$contAsientos++;
				$globalDebito  += $arrayCuenta['debe'];
				$globalCredito += $arrayCuenta['haber'];

				$valueInsertAsientos .= "('$idRemision',
											'$consecutivoRemision',
											'RV',
											'Remision de Venta',
											'$idRemision',
											'$consecutivoRemision',
											'RV',
											NOW(),
											'".$arrayCuenta['debe']."',
											'".$arrayCuenta['haber']."',
											'$cuenta',
											'$idCliente',
											'$id_sucursal',
											'$idEmpresa'
										),";
			}

			if($contAsientos == 0){ return array("estado" => true); }
			else if($globalDebito != $globalCredito){ return deleteRemisionError($conexion,'ErrorContabilidadColgaap',$idRemision, 'No se cumple doble partida la contabilidad Colgaap'); }

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
										id_sucursal,
										id_empresa)
									VALUES $valueInsertAsientos";
			$queryInsertColgaap = mysql_query($sqlInsertColgaap,$link);
			if(!$queryInsertColgaap){ return deleteRemisionError($conexion,'ErrorContabilidadColgaap',$idRemision, 'No se almaceno la contabilidad Colgaap'); }


			$valueInsertContabilizacion = substr($valueInsertContabilizacion, 0, -1);
			$sqlContabilizar     = "INSERT INTO contabilizacion_compra_venta (id_item,id_puc,codigo_puc,caracter,descripcion,id_documento,tipo_documento,id_empresa,id_sucursal,id_bodega) VALUES $valueInsertContabilizacion";
			$queryContabilizar   = mysql_query($sqlContabilizar,$link);
			if(!$queryContabilizar){ return deleteRemisionError($conexion,'ErrorContabilidadColgaap',$idRemision, 'No se almaceno la contabilidad de respaldo Colgaap'); }


			//================================ CONTABILIZACION CUENTAS NIIF ================================//
			/************************************************************************************************/
			$consultaCuentasItems = "SELECT VRI.id,VRI.id_inventario, IC.id_puc, IC.puc, IC.tipo AS estado, VRI.costo_inventario AS costo, VRI.cantidad, IC.descripcion
									FROM ventas_remisiones_inventario AS VRI, items_cuentas_niif AS IC
									WHERE VRI.activo = 1
										AND VRI.id_remision_venta = '$idRemision'
										AND VRI.id_inventario = IC.id_items
										AND IC.activo         = 1
										AND IC.estado         = 'venta'
										AND (IC.descripcion   ='costo' OR IC.descripcion='contraPartida_costo')";
			$queryCuentasItems = mysql_query($consultaCuentasItems,$link);
			$valueInsertContabilizacion = '';

			$arrayAsientoNiif = array();
			while ($rowCuentaItems = mysql_fetch_array($queryCuentasItems)) {
				$cuenta          = $rowCuentaItems['puc'];
				$id_item         = $rowCuentaItems['id_inventario'];
				$idDocInventario = $rowCuentaItems['id'];
				$id_puc          = $rowCuentaItems['id_puc'];
				$estado          = $rowCuentaItems['estado'];
				$costo           = $rowCuentaItems['costo'] * $rowCuentaItems['cantidad'];

				$estadoAsiento = ($estado == 'debito')? 'debe' : 'haber';

				if(!isset($arrayAsientoNiif[$cuenta]['debe'])){ $arrayAsientoNiif[$cuenta]['debe'] = 0; }
				if(!isset($arrayAsientoNiif[$cuenta]['haber'])){ $arrayAsientoNiif[$cuenta]['haber'] = 0; }

				$arrayAsientoNiif[$cuenta][$estadoAsiento] += $costo;

				$arrayCuenta['debito']  = 0;
				$arrayCuenta['credito'] = 0;

				$valueInsertContabilizacion .= "('$id_item',
												'$id_puc',
												'$cuenta',
												'".$rowCuentaItems['estado']."',
												'".$rowCuentaItems['descripcion']."',
												'$idRemision',
												'RV',
												'$idEmpresa',
												'$id_sucursal',
												'$idBodega'),";
			}

			$contAsientos  = 0;
			$globalDebito  = 0;
			$globalCredito = 0;
			$valueInsertAsientos = '';
			foreach ($arrayAsientoNiif as $cuenta => $arrayCuenta) {
				$contAsientos++;
				$globalDebito  += $arrayCuenta['debe'];
				$globalCredito += $arrayCuenta['haber'];

				$valueInsertAsientos .= "('$idRemision',
											'$consecutivoRemision',
											'RV',
											'Remision de Venta',
											'$idRemision',
											'$consecutivoRemision',
											'RV',
											NOW(),
											'".$arrayCuenta['debe']."',
											'".$arrayCuenta['haber']."',
											'$cuenta',
											'$idCliente',
											'$id_sucursal',
											'$idEmpresa'
										),";
			}

			if($contAsientos == 0){ return array("estado" => true); }
			else if($globalDebito != $globalCredito){ return deleteRemisionError($conexion,'ErrorContabilidadNiif',$idRemision, 'No se cumple doble partida la contabilidad Niif'); }

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
										id_sucursal,
										id_empresa)
									VALUES $valueInsertAsientos";
			$queryInsertColgaap = mysql_query($sqlInsertColgaap,$link);
			if(!$queryInsertColgaap){ return deleteRemisionError($conexion,'ErrorContabilidadNiif',$idRemision, 'No se almaceno la contabilidad Niif'); }

			$valueInsertContabilizacion = substr($valueInsertContabilizacion, 0, -1);
			$sqlContabilizar   = "INSERT INTO contabilizacion_compra_venta_niif (id_item,id_puc,codigo_puc,caracter,descripcion,id_documento,tipo_documento,id_empresa,id_sucursal,id_bodega)
									VALUES $valueInsertContabilizacion";
			$queryContabilizar = mysql_query($sqlContabilizar,$link);
			if (!$queryContabilizar) {
				return deleteRemisionError($conexion,'ErrorContabilidadNiif',$idRemision, 'No se almaceno la contabilidad de respaldo Colgaap');
			}

			return array("estado" => true);

		}
		//DESCONTABILIZAR DOCUMENTO
		else if ($accion=='descontabilizar') {
			//SE ELIMINAN TODOS LOS REGISTROS CONTABLES DE ESE DOCUMENTO
			$sqlAsiento   = "DELETE FROM asientos_colgaap WHERE id_documento='$idRemision' AND tipo_documento='RV' AND id_sucursal='$id_sucursal' AND id_empresa='$idEmpresa'";
			$queryAsiento = mysql_query($sqlAsiento,$link);

			$sqlContabilidad   = "DELETE FROM contabilizacion_compra_venta WHERE id_documento='$idRemision' AND tipo_documento='RV' AND id_sucursal='$id_sucursal' AND id_bodega='$idBodega' AND id_empresa='$idEmpresa'";
			$queryContabilidad = mysql_query($sqlContabilidad,$link);

			$sqlAsiento   = "DELETE FROM asientos_niif WHERE id_documento='$idRemision' AND tipo_documento='RV' AND id_sucursal='$id_sucursal' AND id_empresa='$idEmpresa'";
			$queryAsiento = mysql_query($sqlAsiento,$link);

			$sqlContabilidad   = "DELETE FROM contabilizacion_compra_venta_niif WHERE id_documento='$idRemision' AND tipo_documento='RV' AND id_sucursal='$id_sucursal' AND id_bodega='$idBodega' AND id_empresa='$idEmpresa'";
			$queryContabilidad = mysql_query($sqlContabilidad,$link);

			if (!$queryAsiento || !$queryContabilidad) {
				echo '<script>alert("Error!\nNo se descontabilizo el documento!\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; return;
			}
		}
	}

	//=========================== FUNCION DAR MOVER EL INVENTARIO =======================================//
	//***************************************************************************************************//
	function updateInventario($idRemision,$id_sucursal,$idBodega,$opc,$id_empresa,$link){
		$cont = true;

		if ($opc=='descontarInventario' || $opc=='agregarInventario') {

			$signo = $opc=='descontarInventario'? "-": "+";
			$sql   = "UPDATE inventario_totales AS IT, (
														SELECT SUM(cantidad) AS total_remision, id_inventario AS id_item
														FROM ventas_remisiones_inventario
														WHERE id_remision_venta='$idRemision'
															AND activo=1
															AND inventariable='true'
														GROUP BY id_inventario) AS CFI
						SET IT.cantidad = IT.cantidad $signo CFI.total_remision
						WHERE IT.id_item = CFI.id_item
	 						AND IT.activo = 1
	 						AND IT.id_ubicacion = '$idBodega'";
			$query = mysql_query($sql,$link);

			if(!$query){ $cont = false; }
		}
		else if ($opc=='costosArticulosVenta') {

			$sqlArticulos = "SELECT VFI.id_inventario,
								IT.nombre_equipo,
								VFI.cantidad AS cantidad_factura,
								IT.id_item,
								IT.cantidad AS cantidad_inventario,
								IT.costos
							FROM ventas_remisiones_inventario AS VFI,
								inventario_totales AS IT
							WHERE VFI.activo=1
								AND VFI.id_remision_venta='$idRemision'
								AND IT.id_item=VFI.id_inventario
								AND IT.id_sucursal='$id_sucursal'
								AND VFI.inventariable='true'
								AND IT.id_ubicacion='$idBodega'
								AND IT.id_empresa='$id_empresa'";
			$queryArticulos=mysql_query($sqlArticulos,$link);

			//RECORREMOS EL RESULTADO Y SUMAMOS LOS COSTOS DE LOS ARTICULOS QUE ESTAN EN EL DOCUMENTO
			while ($rowArticulos=mysql_fetch_array($queryArticulos)) {
				$cont+=($rowArticulos['costos']*$rowArticulos['cantidad_factura']);
			}
		}
		return $cont;
	}

	function deleteRemisionError($conexion, $estadoError, $idRemision=0, $msjError=''){
		$sql   = "DELETE FROM ventas_remisiones WHERE id='$idRemision'";
		$query = mysql_query($sql,$conexion);
		if($estadoError == 'ErrorValidateReplace')return array("estado" => "error", "msj" => "$msjError");

		$sql   = "DELETE FROM ventas_remisiones_inventario WHERE id_remision_venta='$idRemision'";
		$query = mysql_query($sql,$conexion);
		if($estadoError == 'ErrorValidateItem')return array("estado" => "error", "msj" => "$msjError");
		else if($estadoError == 'ErrorUpdateInventario')return array("estado" => "error", "msj" => "$msjError");

		$sql   = "DELETE FROM contabilizacion_compra_venta WHERE id_documento='$idRemision' AND tipo_documento='RV'";
		$query = mysql_query($sql,$conexion);

		$sql   = "DELETE FROM asientos_colgaap WHERE id_documento='$idRemision'";
		$query = mysql_query($sql,$conexion);
		if($estadoError == 'ErrorContabilidadColgaap')return array("estado" => "msj", "msj" => "$msjError");

		$sql   = "DELETE FROM asientos_niif WHERE id_documento='$idRemision'";
		$query = mysql_query($sql,$conexion);
		if($estadoError == 'ErrorContabilidadNiif')return array("estado" => "msj", "msj" => "$msjError");

	}

	// ELIMINAR LA ORDEN DE COMPRA
	function deleteOrdenCompraError($conexion, $estadoError, $idOrden=0, $msjError=''){
		$sql   = "DELETE FROM compras_ordenes WHERE id='$idOrden'";
		$query = mysql_query($sql,$conexion);
		if($estadoError == 'ErrorValidateReplace')return array("estado" => "error", "msj" => "$msjError");

		$sql   = "DELETE FROM compras_ordenes_inventario WHERE id_orden_compra='$idOrden'";
		$query = mysql_query($sql,$conexion);
		if($estadoError == 'ErrorValidateItem')return array("estado" => "error", "msj" => "$msjError");
		else if($estadoError == 'ErrorUpdateInventario')return array("estado" => "error", "msj" => "$msjError");

	}

?>