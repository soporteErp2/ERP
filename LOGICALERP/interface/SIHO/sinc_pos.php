<?php
	/**
	* CODIGO SINC POST
	* @var str session
	* @var str $fecha_metodo
	* @var str $nit_empresa
	* @var int $id_pais
	* @var int $id_empresa
	* @var int $id_sucursal
	* @var int $id_empleado
	* @var str $nombre_empleado
	* @var str $documento_empleado
	*/


	//===================// CONFIGURACION DE INFORMACION //===================//
	//************************************************************************//
	// CONSULTA LA EMPRESA COMO TERCERO
	$sqlTercero   = "SELECT COUNT(id) AS contTercero, id, codigo, nombre FROM terceros WHERE activo=1 AND id_empresa='$id_empresa' AND numero_identificacion='$nit_empresa' LIMIT 0,1";
	$queryTercero  = $mysql->query($sqlTercero);
	$contTercero   = $mysql->result($queryTercero, 0, 'contTercero');
	$idTercero     = $mysql->result($queryTercero, 0, 'id');
	$codigoTercero = $mysql->result($queryTercero, 0, 'codigo');
	$nombreTercero = $mysql->result($queryTercero, 0, 'nombre');
	if($contTercero == 0){ response_error(array('estado' => 'error','msj'=>'No se encuentra el tercero de sincronizacion!')); }

	// CONSULTA EL PRIMER ID BODEGA
	// $sqlBodega   = "SELECT COUNT(id) AS contBodega, id FROM empresas_sucursales_bodegas WHERE id_sucursal='$id_sucursal' AND activo=1 LIMIT 0,1";
	// $queryBodega = $mysql->query($sqlBodega);
	// $contBodega  = $mysql->result($queryBodega, 0, 'contBodega');
	// $id_bodega   = $mysql->result($queryBodega, 0, 'id');
	// if($contBodega == 0){ response_error(array('estado' => 'error','msj'=>'No se encontro una bodega de inventario #1!')); }

	$errorItem  = "";
	$arrayItem  = array();

	/**
	* CREACION DEL ARRAY QUE CONTIENE LOS ITEMS
	*/
	foreach ($responseWs as $key => $item) {
		if(!isset($item['codigo'])){
			$errorItem .= '<br/>* '.$item['producto'].' #'.$item['codigo'].'.';
			continue;
		}

		$id_bodega     = $item['id_bodega'];
		$centro_costos = $item['ccosto'];
		$codigo_item   = $item['codigo'];

		// CREAR UN ARRAY PARA LAS REMISIONES POR BODEGA
		if($arrayRemisiones[$id_bodega][$centro_costos][$codigo_item] > 0){ $arrayRemisiones[$id_bodega][$centro_costos][$codigo_item]['cantidad'] += $item['cantidad']; }
		else{
			$arrayRemisiones[$id_bodega][$centro_costos][$codigo_item] = array("type"		    => $item['tipo'],
																			"valor"         => $item['valor'],
																			"nombre"        => $item['producto'],
																			"cantidad"      => $item['cantidad'],
																			"precio"        => $item['valor'],
																			"centro_costos" => $item['ccosto']
																		);
		}


		// if($item['bodega'] > 0){ $id_bodega = $item['bodega']; }
		// echo "<br> - ".$item['producto'].$item['id_bodega'];
		// print_r($item);
		if($arrayItem[$item['codigo']]['cantidad'] > 0){ $arrayItem[$item['codigo']]['cantidad'] += $item['cantidad']; }
		else{
			$arrayItem["$item[codigo]"] = array(
												"type"     => $item['tipo'],
												"valor"    => $item['valor'],
												"nombre"   => $item['producto'],
												"cantidad" => $item['cantidad'],
												"precio"   => $item['valor']
												);
		}


		// VALIDAR SI EL COSTO ES NEGATIVO
		// if ($item['valor']<0) {
		// 	$itemsNegativo[$codigo_item] = array(
		// 											"type"		    => $item['tipo'],
		// 											"valor"         => $item['valor'],
		// 											"nombre"        => $item['producto'],
		// 											"cantidad"      => $item['cantidad'],
		// 											"precio"        => $item['valor'],
		// 											"centro_costos" => $item['ccosto'],
		// 											"id_bodega"     => $item['id_bodega'],
		// 										);
		// }

		// WHERE DE CONSULTA DE VALIDACION DE CENTRO COSTOS (PROCESO EN DESARROLLO A LA ESPERA DE SIHO)
		if ($id_bodega>'') {$arrayBodegasWs["$id_bodega"] = array('cod_bodega'=>$item['cod_bodega'],'desc_bodega'=>$item['desc_bodega']); }
		// WHERE DE CONSULTA DE VALIDACION DE CENTRO COSTOS (PROCESO EN DESARROLLO A LA ESPERA DE SIHO)
		if ($centro_costos<>'') {$arrayCcosWs["$centro_costos"] = array(); }
	}

	// VERIFICAR SI HAY ITEMS EN COSTO NEGATIVO
	// if (!empty($itemsNegativo)) {
	// 	foreach ($itemsNegativo as $codigo => $result) {
	// 		$msj .= "$codigo - $result[producto] = $ $result[precio]";
	// 	}

	// 	response_error(array('estado' => 'error','msj'=>'Aviso, Los siguientes items se estan sincronizando con costo negativo, por favor verifique el costo en SIHO:<br><br>'.$msj));
	// }

	// print_r($arrayRemisiones);
	// CONSULTA DE BODEGAS (VALIDACION - PROCESO EN DESARROLLO A LA ESPERA DE SIHO)
	$whereBodegas = "codigo='".implode("' OR codigo='", array_keys($arrayBodegasWs))."'";
	$sql="SELECT id,codigo FROM empresas_sucursales_bodegas WHERE activo=1 AND id_empresa=$id_empresa AND ($whereBodegas) ";
	$query=$mysql->query($sql,$mysql->link);
	// $arrayCcos[0]='';
	while ($row = $mysql->fetch_object($query)) {
		$arrayBodegas["$row->codigo"] =  $row->id;
	}

	// VALIDACION EXISTE LAS BODEGAS (VALIDACION - PROCESO EN DESARROLLO A LA ESPERA DE SIHO)
	$diffBodegas = array_diff_key($arrayBodegasWs, $arrayBodegas);

	if(COUNT($diffBodegas)){
		$msj = implode("<br>*", array_keys($diffBodegas));
		response_error(array('estado' => 'error','msj'=>'Aviso, Configure las bodegas <br><br>'.$msj));
	}

	// CENTROS DE COSTO
	$whereCcos = "codigo='".implode("' OR codigo='", array_keys($arrayCcosWs))."'";
	$sql="SELECT id,codigo,nombre FROM centro_costos WHERE activo=1 AND id_empresa=$id_empresa AND ($whereCcos) ";
	$query=$mysql->query($sql,$mysql->link);

	while ($row = $mysql->fetch_object($query)){
		$arrayCcos["$row->codigo"]  = array('id' => $row->id, 'nombre' => $row->nombre );
	}

	// VALIDACION EXISTE LOS CENTORS DE COSTO!
	$diffCcos = array_diff_key($arrayCcosWs, $arrayCcos);

	if(COUNT($diffCcos)){
		$msj = implode("<br>*", array_keys($diffCcos));
		response_error(array('estado' => 'error','msj'=>'Aviso, Configure los centros de costo <br><br>'.$msj));
	}
	print_r($arrayItemsWs);
	// VALIDACION DE ITEMS BODEGA POR BODEGA
	foreach ($arrayRemisiones as $id_bodega => $arrayResultCcos) {
		foreach ($arrayResultCcos as $centro_costos => $arrayDoc) {
			$whereItems ='';
			$contItems  =0;
			foreach ($arrayDoc as $codigo_item => $arrayResult) {
				$contItems++;
				$whereItems .= ($whereItems=='')? "codigo='$codigo_item'" : " OR codigo='$codigo_item' ";
				$arrayItemsWs[$codigo_item] =  $arrayResult['nombre'];
			}

			$sql="SELECT * FROM inventario_totales WHERE id_empresa='$id_empresa' AND id_sucursal='$id_sucursal' AND id_ubicacion='$id_bodega' AND ($whereItems) GROUP BY id_item";
			$query=$mysql->query($sql,$mysql->link);
			$num_rows = $mysql->num_rows($query);
			while ($row = $mysql->fetch_assoc($query)) { $arrayItems[ $row["codigo"] ] = $row["codigo"]; }

			// VALIDACION DE QUE EXISTAN LOS ITEMS EN ESA BODEGA
			$diffItems = array_diff_key($arrayItemsWs, $arrayItems);
			if(COUNT($diffItems)){
				$msj = implode("<br>* ", array_keys($diffItems) );
				response_error(array('estado' => 'error','msj'=>"En la bodega <b>".$arrayBodegasWs[$id_bodega]["desc_bodega"]."</b> no estan creados los items con codigos:<br><br> * $msj") );
			}

			$query=$mysql->query($sql,$mysql->link);
			while ($row = $mysql->fetch_assoc($query)) {
				$arrayRemisiones[$id_bodega][$centro_costos][$row["codigo"]]["id_item"]          = $row["id_item"];
				$arrayRemisiones[$id_bodega][$centro_costos][$row["codigo"]]["costo_inventario"] = $row["costos"];
			}
		}
	}

	// echo "<br>";
	// print_r($arrayRemisiones);

	$where = implode("' OR codigo='", array_keys($arrayItem));
	if($where == ""){ response_error(array('estado' => 'error','msj'=>'No hay items para descargar en este dia')); }

	// INSERTAR LAS CABECERAS DE LAS REMISIONES POR CADA BODEGA
	foreach ($arrayRemisiones as $id_bodega => $arrayResultCcos) {
		foreach ($arrayResultCcos as $centro_costos => $arrayDoc) {

			// print_r($arrayDoc);
			// response_error(array('estado' => 'error','msj'=>"debug") );

			// CONSULTA SI YA HA SIDO SINCRONIZADA LA REMISION DE ESA BODEGA EN ESA FECHA, SI ES ASI, SE REALIZA UN CONTINUE, PARA QUE SALTE ESTE Y CONTINUE CON LAS OTRAS
			$sqlRemision   = "SELECT COUNT(id) AS contRemision
								FROM ventas_remisiones
								WHERE activo=1 AND id_empresa='$id_empresa'
								AND id_sucursal='$id_sucursal'
								AND id_bodega='$id_bodega'
								AND fecha_inicio='$fecha_metodo'
								AND codigo_centro_costo = '$centro_costos'
								AND referencia='SIHO' AND activo=1 AND estado!=3 LIMIT 0,1";
			$queryRemision = $mysql->query($sqlRemision);
			$contRemision  = $mysql->result($queryRemision, 0, 'contRemision');
			if($contRemision >= 1){ echo "<br>El inventario del dia $fecha_metodo de la bodega: <b>".$arrayBodegasWs[$id_bodega]["desc_bodega"]."</b> con centro de costo $centro_costos ya ha sido descargado! <br>"; continue;}

			$random = random();

			$sqlRemision   = "INSERT INTO ventas_remisiones(
									id_empresa,
									id_sucursal,
									id_bodega,
									random,
									id_cliente,
									fecha_registro,
									fecha_inicio,
									documento_vendedor,
									nombre_vendedor,
									id_centro_costo,
									codigo_centro_costo,
									centro_costo,
									referencia)
								VALUES('$id_empresa',
									'$id_sucursal',
									'$id_bodega',
									'$random',
									'$idTercero',
									NOW(),
									'$fecha_metodo',
									'$documento_empleado',
									'$nombre_empleado',
									'".$arrayCcos[$centro_costos]['id']."',
									'$centro_costos',
									'".$arrayCcos[$centro_costos]['nombre']."',
									'SIHO')";
			$queryRemision = $mysql->query($sqlRemision);

			// QUERY ID REMISION
			$sqlRemision   = "SELECT id FROM ventas_remisiones WHERE activo=1 AND random='$random' AND id_empresa='$id_empresa' AND id_sucursal='$id_sucursal'";
			$queryRemision = $mysql->query($sqlRemision);
			$idRemision    = $mysql->result($queryRemision, 0, 'id');

			// UPDATE ESTADO GENERADO
			$sqlUpdate     = "UPDATE ventas_remisiones SET estado=1 WHERE id='$idRemision'";
			$queryRemision = $mysql->query($sqlUpdate);

			$arrayIdRemisiones[$id_bodega] = $idRemision;

			// INSERTAR LOS ITEMS DE LA REMISION CREADA
			$sqlInsert = '';
			foreach ($arrayDoc as $codigo_item => $arrayResult) {
				if ($arrayResult['id_item']=='') { continue; } ////////////// SE DEBE BORRAR PARA QUE SE INSERTE TODO
				$sqlInsert .= "( '$idRemision',
									'$arrayResult[id_item]',
									'$arrayResult[cantidad]',
									'$arrayResult[cantidad]',
									'porcentaje',
									'0',
									'$arrayResult[costo_inventario]',
									'$arrayResult[costo_inventario]',
									'SIHO'),";
			}
			// print_r($arrayDoc);
			$sqlInsert = substr($sqlInsert, 0, -1);
			$sqlItems  = "INSERT INTO ventas_remisiones_inventario(
							id_remision_venta,
							id_inventario,
							cantidad,
							saldo_cantidad,
							tipo_descuento,
							descuento,
							costo_unitario,
							costo_inventario,
							tipo)
						VALUES $sqlInsert";
			$queryItems = $mysql->query($sqlItems);

			$sqlInsert = '';

			// CONTABILIZACION REMISION
			moverCuentasDocumento($id_empresa, $idRemision, $id_sucursal, $id_bodega, $arrayDoc, $arrayCcos, $arrayBodegasWs, $mysql );

			// ACTUALIZACION DE UNIDADES EN EL INVENTARIO PROMEDIO PODERADO
			$sqlIT   = "UPDATE inventario_totales AS IT, (
								SELECT SUM(cantidad) AS cantidad,
									SUM(cantidad*costo_inventario) AS costo_item,
									id_inventario AS id_item
								FROM ventas_remisiones_inventario
								WHERE id_remision_venta='$idRemision'
									AND activo=1
									AND inventariable='true'
								GROUP BY id_inventario
							) AS CFI
						SET IT.costos=((IT.cantidad * IT.costos)-(CFI.costo_item))/(IT.cantidad-CFI.cantidad),
							IT.cantidad=IT.cantidad-CFI.cantidad
						WHERE IT.id_item=CFI.id_item
							AND IT.activo = 1
							AND IT.id_ubicacion = '$id_bodega'";

			$queryIT = $mysql->query($sqlIT);

			// INSERTAR LOG DE INVENTARIO
			$arrayDatos = array(
								"campo_fecha"             => "fecha_inicio",
								"tablaPrincipal"          => 'ventas_remisiones',
								"id_documento"            => "$idRemision",
								"campos_tabla_inventario" => " id_inventario AS id_item ",
								"tablaInventario"         => 'ventas_remisiones_inventario',
								"idTablaPrincipal"        => 'id_remision_venta',
								"documento"               => "RV",
								"descripcion_documento"   => "Remision de Venta",
								);
			logInventario($arrayDatos,$mysql);

			echo "<br>* Remision en bodega <b>".$arrayBodegasWs[$id_bodega]["desc_bodega"]."</b> $fecha_metodo Insertada.";
		}
	}

	function moverCuentasDocumento($id_empresa, $idRemision, $id_sucursal, $id_bodega, $arrayCcosItems, $arrayCcos, $arrayBodegasWs, $mysql ){

		// VALIDACION QUE TODOS LOS ARTICULOS INVENTARIABLES TENGAN CONFIGURADO LA CUENTA INVENTARIO Y COSTOS
		$contNoContabilizacion = 0;
		$consultaCuentas = "SELECT COUNT(VRI.id) AS cont
							FROM ventas_remisiones_inventario AS VRI, items AS I
							WHERE VRI.activo = 1
								AND VRI.id_remision_venta = '$idRemision'
								AND VRI.id_inventario= I.id
								AND I.inventariable= 'true'
								AND id_inventario NOT IN (
										SELECT id_items
										FROM items_cuentas
										WHERE activo=1
											AND id_empresa='$id_empresa'
											AND estado='venta'
											AND (descripcion='costo' OR descripcion='contraPartida_costo')
									)
							GROUP BY VRI.activo=1";

		$contNoContabilizacion = $mysql->result($mysql->query($consultaCuentas,$link),0,'cont');
		if($contNoContabilizacion > 0){
			rollback('ventas_remisiones',$idRemision,$id_empresa,$mysql,'Hay articulos inventariables que no tiene configuracion contable'); exit;
			// echo'<script>alert("Aviso!\n .");</script>';
		}

		$sqlConsecutivo      = "SELECT consecutivo,id_cliente FROM ventas_remisiones WHERE activo=1 AND id='$idRemision' LIMIT 0,1";
		$queryConsecutivo    = $mysql->query($sqlConsecutivo);
		$idCliente           = $mysql->result($queryConsecutivo,0,'id_cliente');
		$consecutivoRemision = $mysql->result($queryConsecutivo,0,'consecutivo');
		// print_r($arrayCcosItems);
		// print_r($arrayCcos);
		//================================ CONTABILIZACION CUENTAS COLGAAP ================================//
		/***************************************************************************************************/
		$consultaCuentasItems = "SELECT VRI.id,
										VRI.id_inventario,
										VRI.codigo,
										VRI.nombre,
										IC.id_puc,
										IC.puc,
										IC.tipo AS estado,
										VRI.costo_inventario AS costo,
										VRI.cantidad,
										IC.descripcion,
										(SELECT centro_costo FROM puc WHERE id=IC.id_puc) AS centro_costo
									FROM ventas_remisiones_inventario AS VRI, items_cuentas AS IC
									WHERE VRI.activo = 1
										AND VRI.id_remision_venta = '$idRemision'
										AND VRI.id_inventario = IC.id_items
										AND IC.activo         = 1
										AND IC.estado         = 'venta'
										AND (IC.descripcion   ='costo' OR IC.descripcion='contraPartida_costo')";
		$queryCuentasItems = $mysql->query($consultaCuentasItems);
		$valueInsertContabilizacion = '';
		while ($rowCuentaItems = $mysql->fetch_array($queryCuentasItems)) {
			$cuenta          = $rowCuentaItems['puc'];
			$id_item         = $rowCuentaItems['id_inventario'];
			$codigo          = $rowCuentaItems['codigo'];
			$nombre          = $rowCuentaItems['nombre'];
			$idDocInventario = $rowCuentaItems['id'];
			$id_puc          = $rowCuentaItems['id_puc'];
			$estado          = $rowCuentaItems['estado'];
			$costo           = $rowCuentaItems['costo'] * $rowCuentaItems['cantidad'];
			$centro_costo    = $arrayCcosItems[$codigo]['centro_costos'];
			$arrayTieneCcos[$cuenta] = $rowCuentaItems['centro_costo'];

			// VALIDACION QUE TENGA LA CONFIGURACION DE CUENTAS CADA ITEM
			if ($id_puc=='' || $id_puc=='0') { rollback('ventas_remisiones',$idRemision,$id_empresa,$mysql,"Hay articulos inventariables que no tiene configuracion contable en la bodega: <b>".$arrayBodegasWs[$id_bodega]["desc_bodega"]."</b><br>$codigo - $nombre" ); exit;	}
			$estadoAsiento = ($estado=='debito')? 'debe' : 'haber';

			if(is_nan($arrayAsiento[$cuenta][$centro_costo][$estadoAsiento])){ $arrayAsiento[$cuenta][$centro_costo][$estadoAsiento] = 0; }
			$arrayAsiento[$cuenta][$centro_costo][$estadoAsiento] += $costo;


			$arrayCuenta['debito']  = 0;
			$arrayCuenta['credito'] = 0;

			$valueInsertContabilizacion .= "('$id_item',
											'$id_puc',
											'$cuenta',
											'".$rowCuentaItems['estado']."',
											'".$rowCuentaItems['descripcion']."',
											'$idRemision',
											'RV',
											'$id_empresa',
											'$id_sucursal',
											'$id_bodega'),";
		}
		// print_r($valueInsertContabilizacion);
		$contAsientos        = 0;
		$globalDebito        = 0;
		$globalCredito       = 0;
		$valueInsertAsientos = '';
		// print_r($arrayTieneCcos);
		// print_r($arrayAsiento);
		// print_r($arrayCcosItems);
		foreach ($arrayAsiento as $cuenta => $arrayResult) {
			foreach ($arrayResult as $centro_costo => $arrayCuenta) {

				$contAsientos++;
				$globalDebito     += $arrayCuenta['debe'];
				$globalCredito    += $arrayCuenta['haber'];
				$id_centro_costos = ($arrayTieneCcos[$cuenta]=='Si')? $arrayCcos[$centro_costo]['id'] : '' ;

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
											'$id_centro_costos',
											'$id_sucursal',
											'$id_empresa'
										),";

			}
		}
		// echo $valueInsertAsientos;
		// response_error(array('estado' => 'error','msj'=>'debug'));
		$globalDebito  = ROUND($globalDebito,$_SESSION['DECIMALESMONEDA']);
		$globalCredito = round($globalCredito,$_SESSION['DECIMALESMONEDA']);

		if($contAsientos == 0){
			rollback('ventas_remisiones',$idRemision,$id_empresa,$mysql,"El item $rowCuentaItems[codigo] - $rowCuentaItems[nombre] no tiene una configuracion contable Colgaap en la bodega <b>".$arrayBodegasWs[$id_bodega]["desc_bodega"]."</b>" ); exit;
		}
		else if($globalDebito != $globalCredito){
			rollback('ventas_remisiones',$idRemision,$id_empresa,$mysql,'No se cumple doble partida en las cuentas colgaap Debito:'.$globalDebito.' Credito:'.$globalCredito ); exit;
			// echo '<script>alert("Aviso.\n.")</script>'; exit;
		}

		// INSERT ASIENTOS_COLGAAP Y ASIENTOS_POR_ARTICULO
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
		$queryInsertColgaap = $mysql->query($sqlInsertColgaap);
		if(!$queryInsertColgaap){
			rollback('ventas_remisiones',$idRemision,$id_empresa,$mysql,'No se insertaron los asientos colgaaps'); exit;
			// echo'<script>alert("Error!\nSin conexion con la base de datos. Si el problema persiste comuniquese con el administrador del sistema");</script>'; exit;
		}


		$valueInsertContabilizacion = substr($valueInsertContabilizacion, 0, -1);
		$sqlContabilizar     = "INSERT INTO contabilizacion_compra_venta (id_item,id_puc,codigo_puc,caracter,descripcion,id_documento,tipo_documento,id_empresa,id_sucursal,id_bodega) VALUES $valueInsertContabilizacion";
		$queryContabilizar   = $mysql->query($sqlContabilizar);


		//================================ CONTABILIZACION CUENTAS NIIF ================================//
		/************************************************************************************************/
		$consultaCuentasItems = "SELECT VRI.id,
									VRI.id_inventario,
									VRI.codigo,
									VRI.nombre,
									IC.id_puc,
									IC.puc,
									IC.tipo AS estado,
									VRI.costo_inventario AS costo,
									VRI.cantidad,
									IC.descripcion,
									(SELECT centro_costo FROM puc_niif WHERE id=IC.id_puc) AS centro_costo
									FROM ventas_remisiones_inventario AS VRI, items_cuentas_niif AS IC
									WHERE VRI.activo = 1
										AND VRI.id_remision_venta = '$idRemision'
										AND VRI.id_inventario = IC.id_items
										AND IC.activo         = 1
										AND IC.estado         = 'venta'
										AND (IC.descripcion   ='costo' OR IC.descripcion='contraPartida_costo')";
		$queryCuentasItems = $mysql->query($consultaCuentasItems);
		$valueInsertContabilizacion = '';
		$arrayTieneCcos = '';
		while ($rowCuentaItems = $mysql->fetch_array($queryCuentasItems)) {
			$cuenta                  = $rowCuentaItems['puc'];
			$id_item                 = $rowCuentaItems['id_inventario'];
			$codigo                  = $rowCuentaItems['codigo'];
			$nombre                  = $rowCuentaItems['nombre'];
			$idDocInventario         = $rowCuentaItems['id'];
			$id_puc                  = $rowCuentaItems['id_puc'];
			$estado                  = $rowCuentaItems['estado'];
			$costo                   = $rowCuentaItems['costo'] * $rowCuentaItems['cantidad'];
			$centro_costo            = $arrayCcosItems[$codigo]['centro_costos'];
			$arrayTieneCcos[$cuenta] = $rowCuentaItems['centro_costo'];

			// VALIDACION QUE TENGA LA CONFIGURACION DE CUENTAS CADA ITEM
			if ($id_puc=='' || $id_puc=='0') { rollback('ventas_remisiones',$idRemision,$id_empresa,$mysql,"El item $rowCuentaItems[codigo] - $rowCuentaItems[nombre]  no tiene configuracion contable niif en la bodega: <b>".$arrayBodegasWs[$id_bodega]["desc_bodega"]."</b><br>$codigo - $nombre"); exit;	}

			$estadoAsiento = ($estado=='debito')? 'debe' : 'haber';

			if(is_nan($arrayAsientoNiif[$cuenta][$centro_costo][$estadoAsiento])){ $arrayAsientoNiif[$cuenta][$centro_costo][$estadoAsiento] = 0; }
			$arrayAsientoNiif[$cuenta][$centro_costo][$estadoAsiento] += $costo;

			$arrayCuenta['debito']  = 0;
			$arrayCuenta['credito'] = 0;

			$valueInsertContabilizacion .= "('$id_item',
											'$id_puc',
											'$cuenta',
											'".$rowCuentaItems['estado']."',
											'".$rowCuentaItems['descripcion']."',
											'$idRemision',
											'RV',
											'$id_empresa',
											'$id_sucursal',
											'$id_bodega'),";
		}

		$contAsientos  = 0;
		$globalDebito  = 0;
		$globalCredito = 0;
		$valueInsertAsientos = '';
		foreach ($arrayAsientoNiif as $cuenta => $arrayResult) {
			foreach ($arrayResult as $centro_costo => $arrayCuenta) {
				$contAsientos++;
				$globalDebito  += $arrayCuenta['debe'];
				$globalCredito += $arrayCuenta['haber'];
				$id_centro_costos = ($arrayTieneCcos[$cuenta]=='Si')? $arrayCcos[$centro_costo]['id'] : '' ;

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
											'$id_centro_costos',
											'$id_sucursal',
											'$id_empresa'
										),";
			}
		}

		$globalDebito  = ROUND($globalDebito,$_SESSION['DECIMALESMONEDA']);
		$globalCredito = round($globalCredito,$_SESSION['DECIMALESMONEDA']);

		if($contAsientos == 0){
			rollback('ventas_remisiones',$idRemision,$id_empresa,$mysql,'Los articulos no tienen una configuracion contable Niif' ); exit;
			// echo'<script>alert("Aviso!\nLos articulos no tienen una configuracion contable.");</script>'; exit;
		}
		else if($globalDebito != $globalCredito){
			rollback('ventas_remisiones',$idRemision,$id_empresa,$mysql,'No se cumple doble partida en las cuentas Niif Debito:'.$globalDebito.' Credito:'.$globalCredito ); exit;
			// echo '<script>alert("Aviso.\nNo se cumple doble partida, Confirme su configuracion en el modulo panel de control.")</script>'; exit;
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
		$queryInsertColgaap = $mysql->query($sqlInsertColgaap);
		if(!$queryInsertColgaap){
			rollback('ventas_remisiones',$idRemision,$id_empresa,$mysql,'No se insertaron los asientos Niif'); exit;
			// echo'<script>alert("Error!\nSin conexion con la base de datos. Si el problema persiste comuniquese con el administrador del sistema");</script>'; exit;
		}

		$valueInsertContabilizacion = substr($valueInsertContabilizacion, 0, -1);
		$sqlContabilizar   = "INSERT INTO contabilizacion_compra_venta_niif (id_item,id_puc,codigo_puc,caracter,descripcion,id_documento,tipo_documento,id_empresa,id_sucursal,id_bodega)
								VALUES $valueInsertContabilizacion";
		$queryContabilizar = $mysql->query($sqlContabilizar);

	}

	// ROLLBAK DEL PROCESO DE INSERT
	function rollback($tablaPrincipal,$id_documento,$id_empresa,$mysql,$msj=''){

		$sql   = "DELETE FROM $tablaPrincipal WHERE id=$id_documento AND id_empresa=$id_empresa";		// ELIMINAR LA CABECERA DEL DOCUMENTO GENERADO
		$query = $mysql->query($sql);

		$sql   = "DELETE FROM asientos_colgaap WHERE id_documento=$id_documento  AND tipo_documento='RV' AND id_empresa=$id_empresa";		// ELIMINAR LOS ASIENTO COLGAAP
		$query = $mysql->query($sql);

		$sql   = "DELETE FROM asientos_niif WHERE id_documento=$id_documento  AND tipo_documento='RV' AND id_empresa=$id_empresa";		// ELIMINAR LOS ASIENTO NIIF
		$query = $mysql->query($sql);

		if($msj != ""){ response_error(array('estado' => 'error','msj'=> $msj)); }
	}

	function random(){

		// Si es un Nuevo Documento -->
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

?>