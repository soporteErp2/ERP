<?php
	// error_reporting(E_ALL);
	// error_reporting(E_ALL ^ E_NOTICE);
	error_reporting(0);

	include('nuSoap/nusoap.php');
	include('bd/bd.php');

	function connect($nitEmpresa){
		if($nitEmpresa == '' || $nitEmpresa == '0' /*|| is_nan($nitEmpresa)*/){ return array("estado" => "error", "msj" => "Error,\\nNo existe el nit de la empresa en ERP $nitEmpresa"); }

		if($_SERVER['SERVER_NAME'] == 'logicalerp.localhost'){
			$host       = '192.168.8.202';
			$usuario    = 'root';
			$password   = 'serverchkdsk';
			$nameDb     = 'logicalsofterp';
			$nameDbHost = 'erp_bd';
		}
		else{
			$host       = 'localhost';
			$usuario    = 'root';
			$password   = 'serverchkdsk';
			$nameDb     = 'erp';
			$nameDbHost = 'erp_acceso';
		}

		$conexion = mysql_connect($host, $usuario, $password) OR die("Error en la conexion");
		mysql_select_db($nameDbHost,$conexion) OR die("Error al conectar a la base de datos");

		$sqlNameDb   = "SELECT COUNT(id) AS contDb, bd FROM host WHERE activo=1 AND nit_completo='$nitEmpresa'";
		$queryNameDb = mysql_query($sqlNameDb,$conexion);

		$contDb = mysql_result($queryNameDb, 0, 'contDb');
		$nameDb = mysql_result($queryNameDb, 0, 'bd');

		if($contDb == 0){ return array("estado" => "error", "msj" => "No se ha encontrado la Base de datos de la empresa en ERP"); }

		mysql_select_db($nameDb,$conexion) OR die("Error al conectar a la base de datos");
		return array("estado"=>"true","conexion"=>$conexion);
	}

	//====================== METODO WEBSERVICE ======================//
	//***************************************************************//
	// $varJson = '{"{"cont":"1","numeroRemision":"P0000067","nombreEvento":"prueba web service erp","fechaDocumento":"2014-06-17","fechaFinal":"2014-06-25","hora":"11:14:08","idDireccion":"205","idClienteSiip":"1","nitCliente":"94533943","idEmpleadoSiip":"978","nitEmpleado":null,"idEmpresaSiip":"8","idSucursalSiip":"14","idBodegaSiip":"80","idEmpresaErp":"8","idSucursalErp":"14","idBodegaErp":"8","items":{"1":{"idItem":"1","codigo":"1003010001","cantidad":"1","valorUnitario":"1000000.00","descuento":"10.00","tipo_descuento":"porcentaje","impuesto":"16.00"},"2":{"idItem":"1","codigo":"1003010001","cantidad":"1","valorUnitario":"1000000.00","descuento":"10.00","tipo_descuento":"porcentaje","impuesto":"16.00"},"3":{"idItem":"1","codigo":"1003010001","cantidad":"1","valorUnitario":"1000000.00","descuento":"10.00","tipo_descuento":"porcentaje","impuesto":"16.00"},"4":{"idItem":"1","codigo":"1003010001","cantidad":"1","valorUnitario":"1000000.00","descuento":"10.00","tipo_descuento":"porcentaje","impuesto":"16.00"},"5":{"idItem":"1","codigo":"1003010001","cantidad":"1","valorUnitario":"1000000.00","descuento":"10.00","tipo_descuento":"porcentaje","impuesto":"16.00"},"6":{"idItem":"1","codigo":"1003010001","cantidad":"1","valorUnitario":"1000000.00","descuento":"10.00","tipo_descuento":"porcentaje","impuesto":"16.00"},"7":{"idItem":"1","codigo":"1003010001","cantidad":"1","valorUnitario":"1000000.00","descuento":"10.00","tipo_descuento":"porcentaje","impuesto":"16.00"},"8":{"idItem":"10","codigo":"1001010001","cantidad":"1","valorUnitario":"11200.00","descuento":"5.00","tipo_descuento":"porcentaje","impuesto":"16.00"},"9":{"idItem":"10","codigo":"1001010001","cantidad":"24","valorUnitario":"11200.00","descuento":"125000.00","tipo_descuento":"pesos","impuesto":"16.00"}},"saldoItems":{"1":7,"10":25}}';
	// sincRemision($varJson);
	function sincRemision($arrayRemision){

		// VARIABLES
		$idEmpresaSiip   = $arrayRemision['idEmpresaSiip'];
		$nitCliente      = $arrayRemision['nitCliente'];
		$numeroRemision  = $arrayRemision['numeroRemision'];
		$observacion     = $arrayRemision['observacion'];
		$fechaDocumento  = $arrayRemision['fechaDocumento'];
		$fechaInicial    = $arrayRemision['fechaInicial'];
		$fechaFinal      = $arrayRemision['fechaFinal'];
		$idDireccionSiip = $arrayRemision['direccionCliente']['id'];

		$idEmpresaErp    = $arrayRemision['idEmpresaErp'];
		$nitEmpresa      = $arrayRemision['nitEmpresa'];
		$idSucursalErp   = $arrayRemision['idSucursalErp'];
		$idBodegaErp     = $arrayRemision['idBodegaErp'];
		$grupoEmpresa    = $arrayRemision['grupoEmpresa'];
		$nitVendedor     = $arrayRemision['nitVendedor'];

		$codigoCcos      = $arrayRemision['codigo_centro_costos'];

		// VARIABLE DE CONEXION
		$arrayConect = connect($nitEmpresa);
		if($arrayConect['estado'] == 'error'){ return array("estado" => 'error', "msj" => $arrayConect['msj']); }
		$conexion = $arrayConect['conexion'];

		// RANDOMICO REMISION
		$randomico = responseUnicoRanomico();

		//===================================// CONFIGURACION DE TERCERO //===================================//
		$sqlTercero   = "SELECT COUNT(id) AS contTercero, id FROM terceros WHERE numero_identificacion='$nitCliente' AND id_empresa='$idEmpresaErp' AND activo=1 LIMIT 0,1";
		$queryTercero = mysql_query($sqlTercero,$conexion);
		$contTercero  = mysql_result($queryTercero, 0, 'contTercero');
		$idTerceroErp = mysql_result($queryTercero, 0, 'id');

		// TERCERO EXISTE
		if ($contTercero > 0){

			// SI EXISTE LA SUCURSAL CLIENTE EN ERP
			$sqlIdDireccion   = "SELECT COUNT(id) AS contDireccion, id FROM terceros_direcciones WHERE id_tercero='$idTerceroErp' AND id_siip = '$arrayRemision[direccionCliente][id]' LIMIT 0,1";
			$queryIdDireccion = mysql_query($sqlIdDireccion,$conexion);

			$idDireccionErp = mysql_result($queryIdDireccion,0,'id');
			if(is_nan($idDireccionErp) || $idDireccionErp == ''){

				// != SUCURSAL PRINCIPAL = NEW INSERT
				if($arrayRemision['direccionCliente']['direccion_principal'] != 1){			// INSERT SUCURSAL DIRECCION

					$arrayRemision['direccionCliente']['id_tercero'] = $idTerceroErp;
					$arrayRemision['direccionCliente']['id_siip']    = $arrayRemision['direccionCliente']['id'];
					$arrayRemision['direccionCliente']['id']         = '';

					$valueInsertSinc = "";
					$campoInsertSinc = "";
					foreach ($arrayRemision['direccionCliente'] as $campo => $value) {
						if($campo == 'id')continue;
						$valueInsertSinc .= "'$value',";
						$campoInsertSinc .= "$campo,";
					}
					$valueInsertSinc = "($valueInsertSinc)";
					$campoInsertSinc = "($campoInsertSinc)";
					$valueInsertSinc = str_replace(",)", ")", $valueInsertSinc);
					$campoInsertSinc = str_replace(",)", ")", $campoInsertSinc);

					$sqlInsertSincTercero   = "INSERT INTO terceros_direcciones $campoInsertSinc VALUES $valueInsertSinc";
					$queryInsertSincTercero = mysql_query($sqlInsertSincTercero,$conexion);

					$sqlIdDireccion = "SELECT LAST_INSERT_ID()";
					$idDireccionErp = mysql_result(mysql_query($sqlIdDireccion,$conexion),0,0);
				}
				else{
					$sqlUpdateSincTercero   = "UPDATE terceros_direcciones SET id_siip = '$arrayRemision[direccionCliente][id]' WHERE id_tercero='$idTerceroErp' AND direccion_principal=1";
					$queryUpdateSincTercero = mysql_query($sqlUpdateSincTercero,$conexion);

					$sqlIdDireccion   = "SELECT COUNT(id) AS contDireccion,id FROM terceros_direcciones WHERE id_tercero='$idTerceroErp' AND id_siip = '$arrayRemision[direccionCliente][id]' LIMIT 0,1";
					$queryIdDireccion = mysql_query($sqlIdDireccion,$conexion);
					$idDireccionErp   = mysql_result($queryIdDireccion,0,'id');
				}
			}
		}
		// TERCERO NO EXISTE
		else { return array("estado" => "error", "msj" => "Aviso,\\nNo Existe el tercero en el software ERP, por favor dirijase al modulo de terceros y de click al boton actualizar en el tercero de la remision"); }

		$idVendedor     = "";
		$nombreVendedor = "";
		$msg = '';
		if($nitVendedor > 0){
			$sqlVendedor    = "SELECT COUNT(id) AS cont,nombre FROM empleados WHERE activo=1 AND documento='$nitVendedor' LIMIT 0,1";
			$queryVendedor  = mysql_query($sqlVendedor,$conexion);
			$contVendedor   = mysql_result($queryVendedor, 0, 'cont');
			$nombreVendedor = mysql_result($queryVendedor, 0, 'nombre');

			if($contVendedor == 0){ 
				//Validar que esté inactivo
				$sqlVendedor    = "SELECT COUNT(id) AS cont,nombre FROM empleados WHERE documento='$nitVendedor' LIMIT 0,1";
				$queryVendedor  = mysql_query($sqlVendedor,$conexion);
				$contVendedor   = mysql_result($queryVendedor, 0, 'cont');
				$message = 'creado';
				$message2 = 'crear';
				if($contVendedor > 0){ 
					$message = 'activo';
					$message2 = 'activar';
				}
				$msg = '\nEl vendedor con documento '.$nitVendedor.' no se encuentra '.$message.' en el software ERP, debe '.$message2.' el usuario en el pais correspondiente a la remision!'; 
				$nitVendedor = ""; 				
			}
		}

		//INSERT REMISION EN ERP
		$sqlConsulRemision   = "SELECT COUNT(id) AS cont, id, consecutivo
								FROM ventas_remisiones
								WHERE consecutivo_siip='$numeroRemision'
									AND id_empresa='$idEmpresaErp'
									AND id_sucursal='$idSucursalErp'
									AND estado <> 3
									AND activo=1
								LIMIT 0,1";
		$queryConsulRemision = mysql_query($sqlConsulRemision,$conexion);

		$contRemisionErp     = mysql_result($queryConsulRemision, 0, 'cont');
		$idRemisionErpBd     = mysql_result($queryConsulRemision, 0, 'id');
		$RemisionErpBd       = mysql_result($queryConsulRemision, 0, 'consecutivo');
		if($contRemisionErp > 0){ return array("estado" => "ErrorRemisionExiste", "idRemision" => "$idRemisionErpBd", "numeroRemision" => "$RemisionErpBd"); }

		$sqlRemision = "INSERT INTO ventas_remisiones (random,
								id_empresa,
								id_sucursal,
								id_bodega,
								fecha_inicio,
								fecha_finalizacion,
								id_cliente,
								id_sucursal_cliente,
								consecutivo_siip,
								observacion,
								documento_vendedor,
								nombre_vendedor)
						VALUES('$randomico',
								'$idEmpresaErp',
								'$idSucursalErp',
								'$idBodegaErp',
								'$fechaInicial',
								'$fechaFinal',
								'$idTerceroErp',
								'$idDireccionErp',
								'$numeroRemision',
								'$observacion',
								'$nitVendedor',
								'$nombreVendedor')";
		$queryRemision = mysql_query($sqlRemision,$conexion);

		$sqlRemision   = "SELECT COUNT(id) AS cont, id FROM ventas_remisiones WHERE random='$randomico' AND id_empresa='$idEmpresaErp' LIMIT 0,1";
		$queryRemision = mysql_query($sqlRemision,$conexion);
		$idRemisionErp = mysql_result($queryRemision, 0, 'id');

		if(is_nan($idRemisionErp) || $idRemisionErp == 0){ 
			//verificar que el usuario se encuentre creado mas de una vez en el ERP
			$sqlU   = "SELECT COUNT(id) AS total FROM empleados WHERE activo=1 AND id_empresa='$idEmpresaErp' AND documento='$nitVendedor'";
			$queryU = mysql_query($sqlU,$conexion);
			$totalU = mysql_result($queryU, 0, 'total');			
			if($totalU > 1){
				$msg = '\\nEl vendedor se encuentra creado '.$totalU.' veces en el software ERP!';
			}
			return array("estado" => "error", "msj" => "Error,\\nNo se ha sincronizado la remision No. ".$numeroRemision." con el software ERP.".$msg); 
		}

		$valueInsert = "";
		$whereId     = "";
		foreach ($arrayRemision['items'] as $arrayItem) {
			$valueInsert .= "('$idRemisionErp',
								'id_Item_Replace_".$arrayItem['codigo']."',
								'".$arrayItem['cantidad']."',
								'".$arrayItem['cantidad']."',
								'".$arrayItem['valorUnitario']."',
								'".$arrayItem['tipo_descuento']."',
								'".$arrayItem['descuento']."',
								'".$arrayItem['impuesto']."'
							),";

			$whereId .= "|| codigo = '".$arrayItem['codigo']."' ";
		}

		$whereId  = substr($whereId, 3);
		$selectId = "SELECT id,codigo FROM items WHERE activo=1 AND id_empresa='$idEmpresaErp' AND ($whereId)";
		$queryId  = mysql_query($selectId,$conexion);
		while ($row = mysql_fetch_array($queryId)) { $valueInsert = str_replace("id_Item_Replace_".$row['codigo'], $row['id'], $valueInsert); }

		$validateReplace = substr_count($valueInsert, 'id_Item_Replace_');
		if($validateReplace > 0){
			// if($arrayRemision['debug'] == 'true'){ return array("estado" => $validateReplace); }

			$itemError  = '';
			$arrayItems = explode('id_Item_Replace_', $valueInsert);
			for ($i=1; $i <= $validateReplace; $i++) { $itemError .= (substr($arrayItems[$i]." ", 0, 10)).", "; }

			$itemError = substr($itemError, 0, -2);
			return deleteRemisionError($conexion,'ErrorValidateReplace',$idRemisionErp, "Error,\\nNo se ha sincronizado la remision #".$arrayRemision['numeroRemision']."\\nItems no encontrados en ERP:\\n".$itemError);
		}

		$valueInsert = substr($valueInsert, 0, -1);
		$sqlItems    = "INSERT INTO ventas_remisiones_inventario (id_remision_venta,id_inventario,cantidad,saldo_cantidad,costo_unitario,tipo_descuento,descuento,id_impuesto) VALUES $valueInsert";
		$queryItems  = mysql_query($sqlItems,$conexion);

		foreach ($arrayRemision['saldoItems'] as $idItem=>$saldoItem) {
			$validateArticulo = validaCantidadArticulos($idRemisionErp,$idItem,$arrayRemision['idSucursalErp'],$arrayRemision['idBodegaErp'],$saldoItem,$conexion);
			if(!$validateArticulo){ return deleteRemisionError($conexion,'ErrorValidateItem',$idRemisionErp, "Error,\\nNo se ha sincronizado el item Codigo ".$resultSoap['codigo']." con el software ERP"); }
		}

		//CONSULTAMOS EL ID DEL CENTRO DE COSTOS CON EL CODIGO QUE TENEMOS
		$id_Ccos  = 0;

		if($codigoCcos != '' && $codigoCcos > 0){
			$sqlSelCcos = "SELECT id FROM centro_costos WHERE codigo = '$codigoCcos' AND activo = 1 AND id_empresa = '$idEmpresaErp'";
			$queryCcos  = mysql_query($sqlSelCcos,$conexion);

			$id_Ccos    = mysql_result($queryCcos,0,'id');
		}

		//ACTUALIZAMOS LA REMISION PARA DARLA POR TERMINADA
		$sqlUpdate   = "UPDATE ventas_remisiones SET estado='1', pendientes_facturar='$arrayRemision[total_items]',id_centro_costo = '$id_Ccos' WHERE id='$idRemisionErp'";
		$queryUpdate = mysql_query($sqlUpdate,$conexion);
		if(!$queryUpdate){ return deleteRemisionError($conexion,'ErrorValidateItem',$idRemisionErp, "Error,\\nNo se ha actualizado la remision # ".$arrayRemision['numeroRemision']); }

		$sqlNumeroRemision = "SELECT consecutivo FROM ventas_remisiones WHERE id='$idRemisionErp' AND activo=1 LIMIT 0,1";
		$numeroRemision    = mysql_result(mysql_query($sqlNumeroRemision,$conexion), 0, 'consecutivo');

		$updateInventario = updateInventario($idRemisionErp,$arrayRemision['idSucursalErp'],$arrayRemision['idBodegaErp'],'descontarInventario',$arrayRemision['idEmpresaErp'],$conexion);
		if(!$updateInventario){ return deleteRemisionError($conexion,'ErrorUpdateInventario',$idRemisionErp, "Error,\\nNo se ha actualizado el inventario de la remision #".$arrayRemision['numeroRemision']); }		//RETURN FALSE

		//GENERAMOS EL MOVIMIENTO DE LAS CUENTAS PARA LA REMISION
		$contabilizar = contabilidad($idRemisionErp,$arrayRemision['idSucursalErp'],$arrayRemision['idBodegaErp'],'contabilizar',$arrayRemision['idEmpresaErp'],$conexion);
		if($contabilizar["estado"] != true){ return $contabilizar; }

		mysql_close($conexion);
		return array("estado" => "true", "idRemision" => "$idRemisionErp", "numeroRemision" => "$numeroRemision");
	}

	//========================================== GUARDAR ITEM =============================================//
    //*****************************************************************************************************//
	function insertUpdateItem($arrayItem){
		// VARIABLES
		$idEmpresaErp    = $arrayItem['idEmpresaErp'];
		$nitEmpresa      = $arrayItem['nitEmpresa'];
		$codigoItem      = $arrayItem['item']['codigo'];
		$nombreItem      = $arrayItem['item']['nombre'];
		$idImpuestoErp   = $arrayItem['item']['idImpuestoErp'];
		$impuestoItem    = $arrayItem['item']['valorImpuestoErp'];
		$precioVentaItem = $arrayItem['item']['valor'];
		$descripcion1    = $arrayItem['item']['obs_comercial'];
		$descripcion2    = $arrayItem['item']['obs_logistica'];
		$codigoCcos      = $arrayItem['item']['codigoCcos'];
		$nombreCcos      = $arrayItem['item']['nombreCcos'];

		// VARIABLE DE CONEXION
		$arrayConect = connect($nitEmpresa);
		if($arrayConect['estado'] == 'error'){ return array("estado" => 'error', "msj" => $arrayConect['msj']); }
		$conexion = $arrayConect['conexion'];

		$sqlEmpresa   = "SELECT COUNT(id) AS cont,id FROM empresas WHERE id='$idEmpresaErp' AND activo=1 LIMIT 0,1";
		$queryEmpresa = mysql_query($sqlEmpresa,$conexion);
		$contEmpresa  = mysql_result($queryEmpresa, 0, 'cont');
		if(!$queryEmpresa || $contEmpresa == 0 || $contEmpresa == ''){ mysql_close($conexion); return array("estado" => 'error', "msj" => "No se ha Sincronizado la empresa con el software ERP."); }					//ERROR EN LA CONSULTA MYSQL

		$sqlItem      = "SELECT COUNT(id) AS contItem,id  FROM items WHERE codigo='$codigoItem' AND activo=1 AND id_empresa='$idEmpresaErp' LIMIT 0,1";
		$queryItem    = mysql_query($sqlItem,$conexion);
		$arrayItemErp = mysql_fetch_assoc($queryItem);

		if(!$queryItem){ mysql_close($conexion); return array("estado" => 'error', "msj" => "Error en query al item en el software ERP."); }					//ERROR EN LA CONSULTA MYSQL
		else if( $arrayItemErp['contItem'] == 0 || is_nan($arrayItemErp['contItem'])){							//SI NO EXISTE EL ITEM

			$sqlUnidadMedida = "SELECT COUNT(id) AS cont,id FROM inventario_unidades WHERE id_empresa='$idEmpresaErp' AND nombre='Servicio' AND activo=1 LIMIT 0,1";
			$idUnidadMedida = mysql_result(mysql_query($sqlUnidadMedida), 0, 'id');

			$codigoFamilia  = substr($codigoItem, 0,2);
			$codigoGrupo    = substr($codigoItem, 2,2);
			$codigoSubGrupo = substr($codigoItem, 4,2);

			// FAMILIA GRUPO Y SUBGRUPO
			$sqlIdGroup   = "SELECT COUNT(id) AS cont,
								id_familia,
								id_grupo,
								id AS id_subgrupo
							FROM items_familia_grupo_subgrupo
							WHERE activo=1
								AND id_empresa='$idEmpresaErp'
								AND cod_familia='$codigoFamilia'
								AND cod_grupo='$codigoGrupo'
								AND codigo='$codigoSubGrupo'
							LIMIT 0,1";

			$queryIdGroup = mysql_query($sqlIdGroup,$conexion);
			if(!$queryIdGroup){ mysql_close($conexion); return array("estado" => "error", "msj" => "Error en query familia, grupo y subgrupo en el software ERP."); }					//ERROR EN LA CONSULTA MYSQL
			$groupItem = mysql_fetch_assoc($queryIdGroup);
			if($groupItem['cont'] == 0){ mysql_close($conexion); return array("estado" => 'true', "msj" => 'No Se encontro la agrupacion del item Codigo #'.$codigoItem.' en el software ERP'); }					//AVISO LA CLASIFICACION FAMILIA GRUPO SUBGRUPO

			// IMPUESTO
			if($impuestoItem > 0 && ($idImpuestoErp == '' || $idImpuestoErp == 0)){
				$sqlIdImpuesto   = "SELECT COUNT(id) AS cont,id FROM impuestos WHERE activo=1 AND id_empresa='$idEmpresaErp' AND valor='$impuestoItem' LIMIT 0,1";
				$queryIdImpuesto = mysql_query($sqlIdImpuesto,$conexion);
				$idImpuestoErp   = mysql_result($queryIdImpuesto, 0, 'id');
				$contImpuesto    = mysql_result($queryIdImpuesto, 0, 'cont');
				if(!$queryIdImpuesto || is_nan($contImpuesto) || $contImpuesto==0){ return array("estado" => "error", "msj" => "No se encontro el Impuesto en el software ERP."); }		//ERROR EN LA CONSULTA MYSQL
			}

			// CENTRO DE COSTO
			if($codigoCcos != '' && $codigoCcos != 0){
				$sqlCCos   = "SELECT COUNT(id) AS contCCos,id FROM centro_costos WHERE codigo='$codigoCcos' AND activo=1 LIMIT 0,1";
				$queryCCos = mysql_query($sqlCCos,$conexion);
				$contCCos  = mysql_result($queryCCos, 0, 'contCCos');
				$idCCos    = mysql_result($queryCCos, 0, 'id');
				if(!$queryCCos || is_nan($contCCos) || $contCCos==0){ mysql_close($conexion); return array("estado" => "error", "msj" => "No se encontro el Centro de costo en el software ERP."); }		//ERROR EN LA CONSULTA MYSQL
			}
			else{ $idCCos = 0; }

			$sqlInsertItem = "INSERT INTO items (codigo_auto,
												codigo,
												code_bar,
												id_unidad_medida,
												id_empresa,
												id_familia,
												id_grupo,
												id_subgrupo,
												nombre_equipo,
												precio_venta,
												descripcion1,
												descripcion2,
												modulo_pos,
												estado_compra,
												estado_venta,
												inventariable,
												id_centro_costos,
												id_impuesto
												)
								VALUES ('false',
										'$codigoItem',
										'$codigoItem',
										'$idUnidadMedida',
										'$idEmpresaErp',
										'".$groupItem['id_familia']."',
										'".$groupItem['id_grupo']."',
										'".$groupItem['id_subgrupo']."',
										'$nombreItem',
										'$precioVentaItem',
										'$descripcion1',
										'$descripcion2',
										'false',
										'true',
										'true',
										'false',
										'$idCCos',
										'$idImpuestoErp'
										)";
			$queryInsertItem = mysql_query($sqlInsertItem,$conexion);


			$sqlIdItemErp = "SELECT LAST_INSERT_ID()";
			$idItemErp    = mysql_result(mysql_query($sqlIdItemErp,$conexion),0,0);

            include ('funciones/crear_item.php');
            $estadoItem = verificaArticuloBodegas($idItemErp,$idEmpresaErp,$conexion);

			mysql_close($conexion);
			return array("estado" => 'true', "newIdItemErp" => $idItemErp );
		}
		else{

			// IMPUESTO
			if($impuestoItem > 0 && ($idImpuestoErp == '' || $idImpuestoErp == 0)){
				$sqlIdImpuesto   = "SELECT COUNT(id) AS cont,id FROM impuestos WHERE activo=1 AND id_empresa='$idEmpresaErp' AND valor='$impuestoItem' LIMIT 0,1";
				$queryIdImpuesto = mysql_query($sqlIdImpuesto,$conexion);
				$idImpuestoErp   = mysql_result($queryIdImpuesto, 0, 'id');
				$contImpuesto    = mysql_result($queryIdImpuesto, 0, 'cont');
				if(!$queryIdImpuesto || is_nan($contImpuesto) || $contImpuesto==0){ return array("estado" => "error", "msj" => "No se encontro el Impuesto para actualizar el item en el software ERP."); }
			}

			$idItemErp     = $arrayItemErp['id'];
			$sqlUpdateItem = "UPDATE items
								SET precio_venta = '$precioVentaItem',
									descripcion1 = '$descripcion1',
									descripcion2 = '$descripcion2',
									id_centro_costos = '$idCCos',
									id_impuesto = '$idImpuestoErp'
								WHERE activo=1
									AND id = '$idItemErp'
									AND id_empresa = '$idEmpresaErp'";
			$queryUpdateitem = mysql_query($sqlUpdateItem,$conexion);
		}

        include ('funciones/crear_item.php');
		$cuentasColgaap = validacionCuentasDefault($idItemErp,$idEmpresaErp,$conexion);
		$cuentaNiif     = validacionCuentasNiifDefault($idItemErp,$idEmpresaErp,$conexion);

		mysql_close($conexion);
		return array('estado' => 'true', 'arrayItem' => $idItemErp);
	}

	//========================================= CONSULTAR CCOS ============================================//
    //*****************************************************************************************************//
	function consultarCCos($nitEmpresa,$idEmpresaErp){

		// VARIABLE DE CONEXION
		$arrayConect = connect($nitEmpresa);
		if($arrayConect['estado'] == 'error'){ return array("estado" => 'error', "msj" => $arrayConect['msj']); }
		$conexion = $arrayConect['conexion'];

		// $sqlEmpresa   = "SELECT COUNT(id) AS cont,id FROM empresas WHERE documento='$nitEmpresa' AND activo=1 LIMIT 0,1";
		// $queryEmpresa = mysql_query($sqlEmpresa,$conexion);
		// $contEmpresa  = mysql_result($queryEmpresa, 0, 'cont');
		// $idEmpresaErp = mysql_result($queryEmpresa, 0, 'id');
		// if(!$queryEmpresa || $contEmpresa == 0 || $contEmpresa == ''){ return array("estado" => 'error', "msj" => 'No se ha Sincronizado la empresa con el software ERP.'); }					//ERROR EN LA CONSULTA MYSQL

		$sqlCCos   = "SELECT id,codigo,nombre FROM centro_costos WHERE id_empresa='$idEmpresaErp' AND activo=1 ORDER BY CAST(codigo AS CHAR) ASC";
		$queryCCos = mysql_query($sqlCCos,$conexion);

		$contCCos  = -1;
		$arrayCCos = array();
		while ($rowCCos = mysql_fetch_assoc($queryCCos)) {
			$contCCos++;
			$codigo = $rowCCos['codigo'];
			$nombre = $rowCCos['nombre'];
			$arrayCCos["$contCCos"] = array("codigo" => "$codigo", "nombre" => "$nombre");
		}

		mysql_close($conexion);
		return array('estado' => 'true', 'arrayCcos' => $arrayCCos, 'contArray' => $contCCos);
	}

	//========================================= SINCRONIZAR FACTURAS DE VENTA CON LAS OC ============================================//
    //*****************************************************************************************************//
	function sincFacturacionOC($arrayDatos){
		$idEmpresaErp = $arrayDatos['idEmpresaErp'];
		$nitEmpresa   = $arrayDatos['nitEmpresa'];

		// VARIABLE DE CONEXION
		$arrayConect = connect($nitEmpresa);
		if($arrayConect['estado'] == 'error'){ return array("estado" => 'error', "msj" => $arrayConect['msj']); }
		$conexion = $arrayConect['conexion'];

		$sqlEmpresa   = "SELECT COUNT(id) AS cont,id FROM empresas WHERE id='$idEmpresaErp' AND activo=1 LIMIT 0,1";
		$queryEmpresa = mysql_query($sqlEmpresa,$conexion);
		$contEmpresa  = mysql_result($queryEmpresa, 0, 'cont');
		if(!$queryEmpresa || $contEmpresa == 0 || $contEmpresa == ''){ return array("estado" => 'error', "msj" => json_encode($arrayDatos)); }					//ERROR EN LA CONSULTA MYSQL

		//======================================================LOGICA============================================================

		for($i = 0;$i < count($arrayDatos['documentos']);$i++) {
			$id_remision = $arrayDatos['documentos'][$i];
			$whereFacturas=($whereFacturas!='')? $whereFacturas.' OR VFI.id_consecutivo_referencia='.$id_remision : 'VFI.id_consecutivo_referencia='.$id_remision;
		}

		//TRAEMOS LAS FACTURAS RELACIONADAS CON LAS REMISIONES ENVIADAS DEL SIIP
		$sqlRemisiones   = "SELECT VF.numero_factura_completo AS factura,
								   VFI.id_consecutivo_referencia AS id_remision
							FROM ventas_facturas AS VF
							INNER JOIN ventas_facturas_inventario AS VFI ON (
								VF.id = VFI.id_factura_venta
								AND VFI.activo = 1
								AND ($whereFacturas)
								AND VFI.nombre_consecutivo_referencia = 'Remision'
							)
							WHERE VF.estado BETWEEN 1 AND 2
								AND VF.activo = 1
								AND VF.id_empresa = '$idEmpresaErp'";

		$queryRemisiones = mysql_query($sqlRemisiones,$conexion);


		while($rowRemisiones = mysql_fetch_assoc($queryRemisiones)){

			$cont = $rowRemisiones['id_remision'];

			$arrayFacturas["id_remision_$cont"] = $rowRemisiones['factura'];
		}

		mysql_close($conexion);
		return array('estado' => 'true', 'arrayDatos' =>$arrayFacturas);
	}

	//========================================= SINCRONIZAR FACTURAS DE VENTA CON LOS PEDIDOS ============================================//
    //*****************************************************************************************************//
	function sincFacturasVentaPedido($arrayDatos){
		$idEmpresaErp = $arrayDatos['idEmpresaErp'];
		$nitEmpresa   = $arrayDatos['nitEmpresa'];

		// VARIABLE DE CONEXION
		$arrayConect = connect($nitEmpresa);
		if($arrayConect['estado'] == 'error'){ return array("estado" => 'error', "msj" => $arrayConect['msj']); }
		$conexion = $arrayConect['conexion'];

		$sqlEmpresa   = "SELECT COUNT(id) AS cont,id FROM empresas WHERE id='$idEmpresaErp' AND activo=1 LIMIT 0,1";
		$queryEmpresa = mysql_query($sqlEmpresa,$conexion);
		$contEmpresa  = mysql_result($queryEmpresa, 0, 'cont');
		if(!$queryEmpresa || $contEmpresa == 0 || $contEmpresa == ''){ return array("estado" => 'error', "msj" => json_encode($arrayDatos)); }					//ERROR EN LA CONSULTA MYSQL

		//======================================================LOGICA============================================================

		for($i = 0;$i < count($arrayDatos['documentos']);$i++) {
			$id_remision = $arrayDatos['documentos'][$i];
			$whereFacturas=($whereFacturas!='')? $whereFacturas.' OR VFI.id_consecutivo_referencia='.$id_remision : 'VFI.id_consecutivo_referencia='.$id_remision;
		}

		//TRAEMOS LAS FACTURAS RELACIONADAS CON LAS REMISIONES ENVIADAS DEL SIIP
		$sqlRemisiones   = "SELECT VF.numero_factura_completo AS factura,
								   VFI.id_consecutivo_referencia AS id_remision
							FROM ventas_facturas AS VF
							INNER JOIN ventas_facturas_inventario AS VFI ON (
								VF.id = VFI.id_factura_venta
								AND VFI.activo = 1
								AND ($whereFacturas)
								AND VFI.nombre_consecutivo_referencia = 'Remision'
							)
							WHERE VF.estado BETWEEN 1 AND 2
								AND VF.activo = 1
								AND VF.id_empresa = '$idEmpresaErp'";

		$queryRemisiones = mysql_query($sqlRemisiones,$conexion);


		while($rowRemisiones = mysql_fetch_assoc($queryRemisiones)){

			$cont = $rowRemisiones['id_remision'];

			$arrayFacturas["id_remision_$cont"] = $rowRemisiones['factura'];
		}

		mysql_close($conexion);
		return array('estado' => 'true', 'arrayDatos' =>$arrayFacturas);
	}

	//======================================= CONSULTAR IMPUESTO ==========================================//
    //*****************************************************************************************************//
	function consultarImpuesto($nitEmpresa,$idEmpresaErp){

		// VARIABLE DE CONEXION
		$arrayConect = connect($nitEmpresa);
		if($arrayConect['estado'] == 'error'){ return array("estado" => 'error', "msj" => $arrayConect['msj']); }
		$conexion = $arrayConect['conexion'];

		// $sqlEmpresa   = "SELECT COUNT(id) AS cont,id FROM empresas WHERE documento='$nitEmpresa' AND activo=1 LIMIT 0,1";
		// $queryEmpresa = mysql_query($sqlEmpresa,$conexion);
		// $contEmpresa  = mysql_result($queryEmpresa, 0, 'cont');
		// $idEmpresaErp = mysql_result($queryEmpresa, 0, 'id');
		// if(!$queryEmpresa || $contEmpresa == 0 || $contEmpresa == ''){ return array("estado" => 'error', "msj" => "No se ha Sincronizado la empresa con el software ERP $nitEmpresa."); }					//ERROR EN LA CONSULTA MYSQL

		$sqlImpuesto   = "SELECT id,valor,impuesto FROM impuestos WHERE id_empresa='$idEmpresaErp' AND activo=1";
		$queryImpuesto = mysql_query($sqlImpuesto,$conexion);

		$contImpuesto  = -1;
		$arrayImpuesto = array();
		while ($rowImpuesto = mysql_fetch_assoc($queryImpuesto)) {
			$contImpuesto++;
			$idImpuesto = $rowImpuesto['id'];
			$valor      = $rowImpuesto['valor'];
			$nombre     = $rowImpuesto['impuesto'];
			$arrayImpuesto["$contImpuesto"] = array("valor" => "$valor", "nombre" => "$nombre", "idImpuesto" => "$idImpuesto");
		}

		mysql_close($conexion);
		return array('estado' => 'true', 'arrayImpuesto' => $arrayImpuesto, 'contArray' => $contImpuesto);
	}

	//====================================== INSERT UPDATE TERCERO ========================================//
    //*****************************************************************************************************//
	function insertUpdateTercero($arraySoap){

		// VARIABLES
		$whereEmpresa = '';
		$idEmpresaErp = $arraySoap['idEmpresaErp'];
		$nitEmpresa   = $arraySoap['nitEmpresa'];

		//CONEXION A LA BASE DE DATOS DE ACCESO
		if($_SERVER['SERVER_NAME'] == 'logicalerp.localhost'){
			$host       = '192.168.8.202';
			$usuario    = 'root';
			$password   = 'serverchkdsk';
			$nameDb     = 'logicalsofterp';
			$nameDbHost = 'erp_bd';
		}
		else{
			$host       = 'localhost';
			$usuario    = 'root';
			$password   = 'serverchkdsk';
			$nameDb     = 'erp';
			$nameDbHost = 'erp_acceso';
		}

		$conexion = mysql_connect($host, $usuario, $password) OR die("Error en la conexion del Servidor!");
		mysql_select_db($nameDbHost,$conexion) OR die("Error al conectar a la base de datos de acceso!");

		//VERIFICAR SI SE PUEDE SINCRONIZAR EL TERCERO ENTRE TODAS LAS EMPRESAS QUE SINCRONIZAN TERCEROS

		$sqlCheck   = "SELECT sinc_terceros,nombre,id_empresa FROM host WHERE nit_completo='$nitEmpresa'";
		$queryCheck = mysql_query($sqlCheck,$conexion);

		$sincTerceros  = mysql_result($queryCheck, 0, 'sinc_terceros');
		$nombreEmpresa = mysql_result($queryCheck, 0, 'nombre');
		$idEmpresaHost = mysql_result($queryCheck, 0, 'id_empresa');

		if($sincTerceros=='true'){//SINCRONIZACION MASIVA

			$sqlNameDb   = "SELECT nit_completo,id_empresa,nombre FROM host WHERE activo=1 AND sinc_terceros='true'";
			$queryNameDb = mysql_query($sqlNameDb,$conexion);	
			
			while($rowNameDb = mysql_fetch_array($queryNameDb)){
				$nitEmpresa = $rowNameDb['nit_completo'];
				$arraySoap['nombreHost'] = $rowNameDb['nombre'];

				$arrayConect = connect($nitEmpresa);
				if($arrayConect['estado'] == 'error'){ return array("estado" => 'error', "msj" => $arrayConect['msj']); }
				$conexion = $arrayConect['conexion'];
	
				if($idEmpresaErp != $rowNameDb['id_empresa'] && $arraySoap['debug']=='true'){ continue; }
	
				$response = insertUpdateTerceroEmpresa($arraySoap,$rowNameDb['id_empresa'],$conexion);
	
				if($response['estado'] == 'error'){
					mysql_close($conexion);
					return $response;
				}				
			}
		}		
		else{//SOLO SE SINCRONIZA EN SU SESION
			$arraySoap['nombreHost'] = $nombreEmpresa;
			$arrayConect = connect($nitEmpresa);
			if($arrayConect['estado'] == 'error'){ return array("estado" => 'error', "msj" => $arrayConect['msj']); }
			$conexion = $arrayConect['conexion'];

			if($idEmpresaErp != $rowNameDb['id_empresa'] && $arraySoap['debug']=='true'){ 
				//...
			}
			else{
				$response = insertUpdateTerceroEmpresa($arraySoap,$idEmpresaHost,$conexion);

				if($response['estado'] == 'error'){
					mysql_close($conexion);
					return $response;
				}
			}						
		}
		//return array("estado" => 'error', "msj" => $cont);

		mysql_close($conexion);
		return array('estado' => 'true');
	}

	//FUNCION POR CADA UNA DE LAS EMPRESAS
	function insertUpdateTerceroEmpresa($arraySoap,$idEmpresaErp,$conexion){
		$infoTercero = $arraySoap['tercero'];

		//ARRAY TIPOS DE DOCUMENTOS
		$sqlTipoNitTercero   = "SELECT id,codigo FROM tipo_documento WHERE id_empresa='$idEmpresaErp' AND activo=1 AND codigo > 0 AND codigo IS NOT NULL";
		$queryTipoNitTercero = mysql_query($sqlTipoNitTercero,$conexion);
		if(!$queryTipoNitTercero){ return array("estado" => "error", "msj" => 'Los tipo de documento no existe en ERP de '.$arraySoap['nombreHost']); }				//ERROR EN LA CONSULTA MYSQL

		while ($rowTipoDocumento = mysql_fetch_assoc($queryTipoNitTercero)) {
			$codigoDocumento = $rowTipoDocumento['codigo'];
			$tablaTipoDocumento[$codigoDocumento] = $rowTipoDocumento['id'];
		}

		//ARRAY TRATAMIENTO TERCERO
		$sqlTratamiento   = "SELECT id,nombre FROM terceros_tratamiento WHERE id_empresa='$idEmpresaErp' AND activo=1";
		$queryTratamiento = mysql_query($sqlTratamiento,$conexion);
		if(!$queryTratamiento){ return array("estado" => "error", "msj" => 'Los tratamientos de tercero no existe en ERP de '.$arraySoap['nombreHost']); }				//ERROR EN LA CONSULTA MYSQL

		while ($rowTratamiento = mysql_fetch_assoc($queryTratamiento)) {
			$nombre = $rowTratamiento['nombre'];
			$tablaTratamiento[$nombre] = $rowTratamiento['id'];
		}

		$idNitTercero       = $tablaTipoDocumento[$infoTercero['id_tipo_identificacion']];
		$idNitRepresentante = $tablaTipoDocumento[$infoTercero['id_tipo_identificacion_representante']];

		//SECTOR EMPRESARIAL
		$idSectorEmpresa = '';
		if($infoTercero['sector_empresarial'] <> ''){
			$sqlSectorEmpresa   = "SELECT COUNT(id) AS cont,id FROM configuracion_sector_empresarial WHERE nombre='$infoTercero[sector_empresarial]' AND activo=1 AND id_empresa='$idEmpresaErp' LIMIT 0,1";
			$querySectorEmpresa = mysql_query($sqlSectorEmpresa,$conexion);
			$contSectorEmpresa  = mysql_result($querySectorEmpresa, 0, 'cont');
			$idSectorEmpresa    = mysql_result($querySectorEmpresa, 0, 'id');
			if(!$querySectorEmpresa || $contSectorEmpresa == 0){ return array("estado" => "error", "msj" => 'Configuracion del sector empresarial no existe en ERP de '.$arraySoap['nombreHost']); }				//ERROR EN LA CONSULTA MYSQL
		}
		//TERCERO
		$sqlTercero   = "SELECT COUNT(id) AS contTercero, id FROM terceros WHERE numero_identificacion='$infoTercero[numero_identificacion]' AND activo=1 AND id_empresa='$idEmpresaErp' LIMIT 0,1";
		$queryTercero = mysql_query($sqlTercero,$conexion);
		$contTercero  = mysql_result($queryTercero, 0, 'contTercero');
		$idTerceroErp = mysql_result($queryTercero, 0, 'id');

		//TIPO REGIMEN
		$sqlTipoRegimenTercero   = "SELECT id,codigo FROM terceros_tributario WHERE id_pais='$infoTercero[id_pais]' AND activo=1 AND codigo > 0 AND codigo IS NOT NULL";
		$queryTipoRegimenTercero = mysql_query($sqlTipoRegimenTercero,$conexion);
		if(!$queryTipoRegimenTercero){ return array("estado" => "error", "msj" => 'Los tipos de regimen no existen en ERP de '.$arraySoap['nombreHost']); }				//ERROR EN LA CONSULTA MYSQL

		while ($rowTipoRegimen = mysql_fetch_assoc($queryTipoRegimenTercero)) {
			$codigoRegimen = $rowTipoRegimen['codigo'];
			$tablaTipoRegimen[$codigoRegimen] = $rowTipoRegimen['id'];
		}
		
		$idRegimenErp =  $tablaTipoRegimen[$infoTercero['id_tercero_tributario']];

		$infoTercero['tipo_cliente']   = ($infoTercero['tipo_cliente']=='')? 'Si' : $infoTercero['tipo_cliente'];
		$infoTercero['tipo_proveedor'] = ($infoTercero['tipo_proveedor']=='')? 'Si' : $infoTercero['tipo_proveedor'];
		$infoTercero['exento_iva']     = ($infoTercero['exento_iva']!='Si')? 'No' : $infoTercero['exento_iva'] ;

		//FORMA DE PAGO DEL SIIP
		$id_forma_pago = $infoTercero['id_forma_pago'];
		$sqlF = "SELECT id_empresa FROM configuracion_formas_pago WHERE id = '$id_forma_pago'";
		$queryF = mysql_query($sqlF,$conexion);

		$empresa_forma_pago = mysql_result($queryF, 0, 'id_empresa');

		//SI ES DIFERENTE NO LE PASA EL ID DE LA FORMA DE PAGO DEL SIIP
		if($idEmpresaErp != $empresa_forma_pago){
			$id_forma_pago = '';
		}

		//METODO DE PAGO DEL SIIP
		$id_metodo_pago = $infoTercero['id_metodo_pago'];
		$sqlMF = "SELECT id_empresa FROM configuracion_metodos_pago WHERE id = '$id_metodo_pago'";
		$queryMF = mysql_query($sqlMF,$conexion);

		$empresa_metodo_pago = mysql_result($queryMF, 0, 'id_empresa');

		//SI ES DIFERENTE NO LE PASA EL ID DE LA FORMA DE PAGO DEL SIIP
		if($idEmpresaErp != $empresa_metodo_pago){
			$id_metodo_pago = '';
		}

		//SI NO EXISTE INSERT
		if($contTercero == 0){
			$sqlInsertTercero = "INSERT INTO terceros (
									codigo,
									id_empresa,
									id_tipo_identificacion,
									tipo,
									ciudad_identificacion,
									dv,
									numero_identificacion,
									nombre,
									nombre_comercial,
									direccion,
									telefono1,
									telefono2,
									celular1,
									celular2,
									id_pais,
									id_departamento,
									id_ciudad,
									pagina_web,
									representante_legal,
									ciudad_id_representante,
									ciudad_representante,
									id_tipo_identificacion_representante,
									identificacion_representante,
									fecha_creacion,
									id_sector_empresarial,
									tipo_cliente,
									tipo_proveedor,
									exento_iva,
									id_tipo_persona_dian,
									id_tercero_tributario,
									nombre1,
									nombre2,
									apellido1,
									apellido2,
									id_forma_pago,
									id_metodo_pago
								)
								VALUES('$infoTercero[id]',
									'$idEmpresaErp',
									'$idNitTercero',
									'$infoTercero[tipo]',
									'$infoTercero[ciudad_identificacion]',
									'$infoTercero[dv]',
									'$infoTercero[numero_identificacion]',
									'$infoTercero[nombre]',
									'$infoTercero[nombre_comercial]',
									'$infoTercero[direccion]',
									'$infoTercero[telefono1]',
									'$infoTercero[telefono2]',
									'$infoTercero[celular1]',
									'$infoTercero[celular2]',
									'$infoTercero[id_pais]',
									'$infoTercero[id_departamento]',
									'$infoTercero[id_ciudad]',
									'$infoTercero[pagina_web]',
									'$infoTercero[representante_legal]',
									'$infoTercero[ciudad_representante]',
									'$infoTercero[ciudad_representante]',
									'$idNitRepresentante',
									'$infoTercero[identificacion_representante]',
									Now(),
									'$idSectorEmpresa',
									'$infoTercero[tipo_cliente]',
									'$infoTercero[tipo_proveedor]',
									'$infoTercero[exento_iva]',
									'$infoTercero[id_tipo_persona_dian]',
									'$idRegimenErp',
									'$infoTercero[nombre1]',
									'$infoTercero[nombre2]',
									'$infoTercero[apellido1]',
									'$infoTercero[apellido2]',
									'$id_forma_pago',
									'$id_metodo_pago'
								)";

			$queryInsertTercero = mysql_query($sqlInsertTercero,$conexion);
			if(!$queryInsertTercero){ return array("estado" => "error", "msj" => 'No se inserto el tercero en ERP de '.$arraySoap['nombreHost']); }

			$sqlIdTerceroErp    = "SELECT LAST_INSERT_ID()";
			$idTerceroErp       = mysql_result(mysql_query($sqlIdTerceroErp,$conexion),0,0);

			//CONTACTO AUTO
			if($arraySoap['tercero']['idContactoAuto'] > 0){
				$idSiipContacto    = $arraySoap['tercero']['idContactoAuto'];
				$sqlContactoAuto   = "UPDATE terceros_contactos SET id_siip='$idSiipContacto' WHERE id_tercero='$idTerceroErp'";
				$queryContactoAuto = mysql_query($sqlContactoAuto,$conexion);
			}
		}
		//SI EXISTE UPDATE
		else{
			$infoTercero['tipo_cliente']   = ($infoTercero['tipo_cliente']!='No')? 'Si' : $infoTercero['tipo_cliente'];
			$infoTercero['tipo_proveedor'] = ($infoTercero['tipo_proveedor']!='No')? 'Si' : $infoTercero['tipo_proveedor'];
			$infoTercero['exento_iva']     = ($infoTercero['exento_iva']!='Si')? 'No' : $infoTercero['exento_iva'] ;

			$sqlUpdateTercero = "UPDATE terceros
								SET codigo = '$infoTercero[id]',
									tipo = '$infoTercero[tipo]',
									id_tipo_identificacion = '$idNitTercero',
									ciudad_identificacion = '$infoTercero[ciudad_identificacion]',
									dv = '$infoTercero[dv]',
									numero_identificacion = '$infoTercero[numero_identificacion]',
									nombre = '$infoTercero[nombre]',
									nombre_comercial = '$infoTercero[nombre_comercial]',
									direccion = '$infoTercero[direccion]',
									telefono1 = '$infoTercero[telefono1]',
									telefono2 = '$infoTercero[telefono2]',
									celular1 = '$infoTercero[celular1]',
									celular2 = '$infoTercero[celular2]',
									pagina_web = '$infoTercero[pagina_web]',
									representante_legal = '$infoTercero[representante_legal]',
									ciudad_id_representante = '$infoTercero[ciudad_representante]',
									ciudad_representante = '$infoTercero[ciudad_representante]',
									identificacion_representante = '$infoTercero[identificacion_representante]',
									fecha_creacion = Now(),
									tipo_cliente = '$infoTercero[tipo_cliente]',
									tipo_proveedor = '$infoTercero[tipo_proveedor]',
									nombre1 = '$infoTercero[nombre1]',
									nombre2 = '$infoTercero[nombre2]',
									apellido1 = '$infoTercero[apellido1]',
									apellido2 = '$infoTercero[apellido2]',
									id_tipo_persona_dian = '$infoTercero[id_tipo_persona_dian]',
									id_tercero_tributario = '$idRegimenErp',
									exento_iva = '$infoTercero[exento_iva]',
									id_forma_pago = '$id_forma_pago',
									id_metodo_pago = '$id_metodo_pago',
									id_pais = '$infoTercero[id_pais]',
									id_departamento = '$infoTercero[id_departamento]',
									id_ciudad = '$infoTercero[id_ciudad]'
								WHERE numero_identificacion='$infoTercero[numero_identificacion]'
									AND activo=1
									AND id_empresa='$idEmpresaErp'";
			$queryUpdateTercero = mysql_query($sqlUpdateTercero,$conexion);
			if (!$queryUpdateTercero) { return array("estado" => 'error', "msj" => "No se actualizo el tercero de ".$arraySoap['nombreHost']); }

		}

		//===================================================// SINC CONTACTOS //===================================================//
		if($arraySoap['contContactos'] > 0){
			$whereIn = "";
			for ($i=1; $i <= $arraySoap['contContactos']; $i++) {
				$j = $i - 1;

				$subArray       = $arraySoap['arrayContactos'][$j];
				$idContactoSiip = $subArray['id'];

				//TODOS LOS CONTACTOS EN EL SIIP DEL TERCERO
				$whereIn   .= "OR id_siip = $idContactoSiip ";
				$idTratamiento = $tablaTratamiento[$subArray['tratamiento']];
				$idNitContacto = $tablaTipoDocumento[$subArray['id_tipo_identificacion']];

				$arrayContactoInsert[$idContactoSiip] = array("idTercero" => "$idTerceroErp",
														"nombre" => "$subArray[nombre]",
														"telefono1" => "$subArray[telefono1]",
														"telefono2" => "$subArray[telefono2]",
														"celular1" => "$subArray[celular1]",
														"celular2" => "$subArray[celular2]",
														"nacimiento" => "$subArray[nacimiento]",
														"observaciones" => "$subArray[observaciones]",
														"sexo" => "$subArray[sexo]",
														"ContactoAuto" => "$subArray[ContactoAuto]",
														"idTratamiento" => "$idTratamiento",
														"identificacion" => "$subArray[identificacion]",
														"idNitContacto" => "$idNitContacto",
														"cargo" => "$subArray[cargo]",
														"direccion" => "$subArray[direccion]",
														"idEmpresaErp" => "$idEmpresaErp");
			}
			$valueInsert = "";
			$whereIn     = substr($whereIn, 3);

			$sqlContactos   = "SELECT id_siip FROM terceros_contactos WHERE activo=1 AND id_tercero='$idTerceroErp' AND ($whereIn) AND id_empresa='$idEmpresaErp' GROUP BY id_siip";
			$queryContactos = mysql_query($sqlContactos,$conexion);
			while ($rowContactos = mysql_fetch_assoc($queryContactos)) {

				$idContactoSiip = $rowContactos['id_siip'];
				$arrayContactoUpdate[$idContactoSiip] = $arrayContactoInsert[$idContactoSiip];

				unset($arrayContactoInsert[$idContactoSiip]);
			}

			if(COUNT($arrayContactoInsert) > 0){ insertUpdateTerceroContacto($arrayContactoInsert,"INSERT",$conexion); }
			if(COUNT($arrayContactoUpdate) > 0){ insertUpdateTerceroContacto($arrayContactoUpdate,"UPDATE",$conexion); }

			//CONSULTA ID CONTACTOS EN ERP
			$sqlIdErpContactos   = "SELECT id,id_siip FROM terceros_contactos WHERE id_tercero='$idTerceroErp' AND activo=1";
			$queryIdErpContactos = mysql_query($sqlIdErpContactos,$conexion);
			while ($rowContactosErp = mysql_fetch_assoc($queryIdErpContactos)){
				$arrayIdErpContactos[$rowContactosErp['id_siip']] = $rowContactosErp['id'];
			}

			//===================================================// SINC EMAIL //===================================================//
			if($arraySoap['contEmail'] > 0){
				$whereIn = "";
				for ($i=1; $i <= $arraySoap['contEmail']; $i++) {
					$j = $i - 1;

					$idEmailSiip    = $arraySoap['arrayEmail'][$j]['id'];
					$idContactoSiip = $arraySoap['arrayEmail'][$j]['id_contacto'];
					$idContactoErp  = $arrayIdErpContactos[$idContactoSiip];
					$whereIn       .= "OR (id_siip = $idEmailSiip AND id_contacto= '$idContactoErp') ";

					if(is_nan($idContactoErp)){ continue; }

					$arrayEmailInsert[$idEmailSiip] = array("email" => $arraySoap['arrayEmail'][$j]['email'], "idContactoErp" => $arrayIdErpContactos[$idContactoSiip]);
				}
				$valueInsert = "";
				$whereIn     = ($whereIn == '')? '': "AND (".substr($whereIn, 3).")";

				$sqlEmail   = "SELECT id_siip FROM terceros_contactos_email WHERE activo=1 $whereIn GROUP BY id_siip";
				$queryEmail = mysql_query($sqlEmail,$conexion);
				while ($rowEmail = mysql_fetch_assoc($queryEmail)) {

					$idEmailSiip = $rowEmail['id_siip'];
					$arrayEmailUpdate[$idEmailSiip] = $arrayEmailInsert[$idEmailSiip];

					unset($arrayEmailInsert[$idEmailSiip]);
				}

				if(COUNT($arrayEmailInsert) > 0){ insertUpdateTerceroEmail($arrayEmailInsert,"INSERT",$conexion); }
				if(COUNT($arrayEmailUpdate) > 0){ insertUpdateTerceroEmail($arrayEmailUpdate,"UPDATE",$conexion); }

			}
		}

		//===================================================// SINC DIRECCIONES //===================================================//
		if($arraySoap['contDireccion'] > 0){
			$whereIn = "";
			for ($i=1; $i <= $arraySoap['contDireccion']; $i++) {
				$j = $i - 1;

				$idDireccionSiip = $arraySoap['arrayDireccion'][$j]['id'];
				$whereIn        .= "OR id_siip = $idDireccionSiip ";

				$arrayDireccionInsert[$idDireccionSiip] = $arraySoap['arrayDireccion'][$j];
				$arrayDireccionInsert[$idDireccionSiip]["idTerceroErp"] = $idTerceroErp;
			}

			$valueInsert = "";
			$whereIn     = substr($whereIn, 3);

			$sqlDireccion   = "SELECT id_siip FROM terceros_direcciones WHERE activo=1 AND id_tercero='$idTerceroErp' AND ($whereIn) GROUP BY id_siip";
			$queryDireccion = mysql_query($sqlDireccion,$conexion);
			while ($rowDireccion = mysql_fetch_assoc($queryDireccion)) {

				$idDireccionSiipErp = $rowDireccion['id_siip'];
				$arrayDireccionUpdate[$idDireccionSiipErp] = $arrayDireccionInsert[$idDireccionSiipErp];

				unset($arrayDireccionInsert[$idDireccionSiipErp]);
			}

			if(COUNT($arrayDireccionInsert) > 0){ insertUpdateTerceroDirecciones($arrayDireccionInsert,"INSERT",$conexion); }
			if(COUNT($arrayDireccionUpdate) > 0){ insertUpdateTerceroDirecciones($arrayDireccionUpdate,"UPDATE",$conexion); }

			//CONSULTA ID DIRECCIONES EN ERP
			$sqlIdErpDirecciones   = "SELECT id,id_siip FROM terceros_direcciones WHERE id_tercero='$idTerceroErp' AND activo=1";
			$queryIdErpDirecciones = mysql_query($sqlIdErpDirecciones,$conexion);
			while ($rowDireccionesErp = mysql_fetch_assoc($queryIdErpDirecciones)){
				$arrayIdErpDirecciones[$rowDireccionesErp['id_siip']] = $rowDireccionesErp['id'];
			}

			//===================================================// SINC DIRECCIONES EMAIL //===================================================//
			if($arraySoap['contDireccionesEmail'] > 0){
				$whereIn = "";
				for ($i=1; $i <= $arraySoap['contDireccionesEmail']; $i++) {
					$j = $i - 1;

					$idEmailDireccionSiip = $arraySoap['arrayDireccionesEmail'][$j]['id'];
					$idDireccionSiip = $arraySoap['arrayDireccionesEmail'][$j]['id_direccion'];
					$idDireccionErp  = $arrayIdErpDirecciones[$idDireccionSiip];
					$whereIn       .= "OR (id_siip = $idEmailDireccionSiip AND id_direccion= '$idDireccionErp') ";

					if(is_nan($idDireccionErp)){ continue; }

					$arrayEmailDireccionInsert[$idEmailDireccionSiip] = array("contacto" => $arraySoap['arrayDireccionesEmail'][$j]['contacto'],"email" => $arraySoap['arrayDireccionesEmail'][$j]['email'], "idDireccionErp" => $arrayIdErpDirecciones[$idDireccionSiip]);
				}
				$valueInsert = "";
				$whereIn     = ($whereIn == '')? '': "AND (".substr($whereIn, 3).")";

				$sqlEmailDireccion   = "SELECT id_siip FROM terceros_direcciones_email WHERE activo=1 $whereIn GROUP BY id_siip";
				$queryEmailDireccion = mysql_query($sqlEmailDireccion,$conexion);
				while ($rowEmailDireccion = mysql_fetch_assoc($queryEmailDireccion)) {

					$idEmailDireccionSiip = $rowEmailDireccion['id_siip'];
					$arrayEmailDireccionUpdate[$idEmailDireccionSiip] = $arrayEmailDireccionInsert[$idEmailDireccionSiip];

					unset($arrayEmailDireccionInsert[$idEmailDireccionSiip]);
				}

				if(COUNT($arrayEmailDireccionInsert) > 0){ insertUpdateTerceroEmailDireccion($arrayEmailDireccionInsert,"INSERT",$conexion); }
				if(COUNT($arrayEmailDireccionUpdate) > 0){ insertUpdateTerceroEmailDireccion($arrayEmailDireccionUpdate,"UPDATE",$conexion); }

			}
		}
	}

	function insertUpdateTerceroContacto($arrayContacto,$action,$conexion){

		if($action == "INSERT"){

			$idSiipAuto  = 0;
			$valueInsert = "";
			foreach ($arrayContacto as $idSiip => $infoContacto) {
				if($infoContacto['ContactoAuto'] == 1){ $idSiipAuto=$idSiip; continue; }

				$joinContacto  = join("','",$infoContacto);
				$valueInsert  .= "('".$idSiip."','".$joinContacto."'),";
			}

			//FILTRO CONTACTOS POR INSERTAR
			if($valueInsert != ""){
				$valueInsert        = substr($valueInsert, 0, -1);
				$sqlInsertContactos = "INSERT INTO terceros_contactos (
											id_siip,
											id_tercero,
											nombre,
											telefono1,
											telefono2,
											celular1,
											celular2,
											nacimiento,
											observaciones,
											sexo,
											ContactoAuto,
											id_tratamiento,
											identificacion,
											id_tipo_identificacion,
											cargo,
											direccion,
											id_empresa)
										VALUES $valueInsert";
				$queryInsertContactos = mysql_query($sqlInsertContactos,$conexion);
			}

			if($idSiipAuto > 0){
				$idTercero = $arrayContacto[$idSiipAuto]['idTercero'];
				$sqlUpdateContacto   = "UPDATE terceros_contactos SET id_siip='$idSiipAuto' WHERE ContactoAuto=1 AND id_tercero=$idTercero AND activo=1";
				$queryUpdateDireccion = mysql_query($sqlUpdateContacto,$conexion);
			}
		}
		else if($action == "UPDATE"){
			foreach ($arrayContacto as $idSiip => $contacto) {

				$sqlUpdateContacto = "UPDATE terceros_contactos
									SET nombre = '$contacto[nombre]',
										telefono1 = '$contacto[telefono1]',
										telefono2 = '$contacto[telefono2]',
										celular1 = '$contacto[celular1]',
										celular2 = '$contacto[celular2]',
										nacimiento = '$contacto[nacimiento]',
										observaciones = '$contacto[observaciones]',
										sexo = '$contacto[sexo]',
										ContactoAuto = '$contacto[ContactoAuto]',
										id_tratamiento = '$contacto[idTratamiento]',
										identificacion = '$contacto[identificacion]',
										id_tipo_identificacion = '$contacto[idNitContacto]',
										cargo = '$contacto[cargo]',
										direccion = '$contacto[direccion]'
									WHERE id_siip='$idSiip' AND activo=1";
				$queryUpdateContacto = mysql_query($sqlUpdateContacto,$conexion);
			}
		}
	}

	function insertUpdateTerceroEmail($arrayEmail,$action,$conexion){

		if($action == "INSERT"){

			$valueInsert = "";
			foreach ($arrayEmail as $idSiip => $infoEmail) {

				$valueInsert .= "('$infoEmail[idContactoErp]','$infoEmail[email]','$idSiip'),";
			}
			$valueInsert    = substr($valueInsert, 0, -1);
			$sqlInsertEmail = "INSERT INTO terceros_contactos_email (
									id_contacto,
									email,
									id_siip
								)
								VALUES $valueInsert";
			$queryInsertEmail = mysql_query($sqlInsertEmail,$conexion);
		}
		else if($action == "UPDATE"){
			foreach ($arrayEmail as $idSiip => $infoEmail) {
				$sqlUpdateEmail   = "UPDATE terceros_contactos_email SET email = '$infoEmail[email]' WHERE id_siip='$idSiip' AND activo=1";
				$queryUpdateEmail = mysql_query($sqlUpdateEmail,$conexion);
			}
		}
	}

	function insertUpdateTerceroDirecciones($arrayDireccion,$action,$conexion){
		if($action == "INSERT"){
			$valueInsert = "";
			$idSiipAuto  = 0;
			foreach ($arrayDireccion as $idSiip => $infoDireccion) {
				if($infoDireccion['direccion_principal'] == 1){ $idSiipAuto = $idSiip; continue; }

				$valueInsert .= "('$infoDireccion[idTerceroErp]',
									'$infoDireccion[direccion]',
									'$infoDireccion[id_departamento]',
									'$infoDireccion[id_ciudad]',
									'$infoDireccion[telefono1]',
									'$infoDireccion[telefono2]',
									'$infoDireccion[celular1]',
									'$infoDireccion[celular2]',
									'$infoDireccion[nombre]',
									'$infoDireccion[direccion_principal]',
									'$infoDireccion[id_pais]',
									'$idSiip'
									),";
			}

			//FILTRO DIRECCIONES POR INSERTAR
			if($valueInsert != ""){
				$valueInsert    = substr($valueInsert, 0, -1);
				$sqlInsertEmail = "INSERT INTO terceros_direcciones (
										id_tercero,
										direccion,
										id_departamento,
										id_ciudad,
										telefono1,
										telefono2,
										celular1,
										celular2,
										nombre,
										direccion_principal,
										id_pais,
										id_siip
									)
									VALUES $valueInsert";
				$queryInsertEmail = mysql_query($sqlInsertEmail,$conexion);
			}
			if($idSiipAuto > 0){
				$idTercero = $arrayDireccion[$idSiipAuto]['idTerceroErp'];
				$sqlUpdateDireccion   = "UPDATE terceros_direcciones SET id_siip='$idSiipAuto' WHERE direccion_principal=1 AND id_tercero='$idTercero' AND activo=1";
				$queryUpdateDireccion = mysql_query($sqlUpdateDireccion,$conexion);
			}
		}
		else if($action == "UPDATE"){
			foreach ($arrayDireccion as $idSiip => $infoDireccion) {

				$sqlUpdateDireccion   = "UPDATE terceros_direcciones
										SET direccion = '$infoDireccion[direccion]',
											id_departamento = '$infoDireccion[id_departamento]',
											id_ciudad = '$infoDireccion[id_ciudad]',
											telefono1 = '$infoDireccion[telefono1]',
											telefono2 = '$infoDireccion[telefono2]',
											celular1 = '$infoDireccion[celular1]',
											celular2 = '$infoDireccion[celular2]',
											nombre = '$infoDireccion[nombre]',
											direccion_principal = '$infoDireccion[direccion_principal]',
											id_pais = '$infoDireccion[id_pais]'
										WHERE id_siip='$idSiip' AND activo=1 AND id_tercero='$infoDireccion[idTerceroErp]'";
				$queryUpdateDireccion = mysql_query($sqlUpdateDireccion,$conexion);
			}
		}
	}

	function insertUpdateTerceroEmailDireccion($arrayEmailDireccion,$action,$conexion){

		if($action == "INSERT"){

			$valueInsert = "";
			foreach ($arrayEmailDireccion as $idSiip => $infoEmailDireccion) {

				$valueInsert .= "('$infoEmailDireccion[idDireccionErp]','$infoEmailDireccion[contacto]','$infoEmailDireccion[email]','$idSiip'),";
			}
			$valueInsert    = substr($valueInsert, 0, -1);
			$sqlInsertEmail = "INSERT INTO terceros_direcciones_email (
									id_direccion,
									contacto,
									email,
									id_siip
								)
								VALUES $valueInsert";

			//echo $sqlInsertEmail;
			$queryInsertEmail = mysql_query($sqlInsertEmail,$conexion);
		}
		else if($action == "UPDATE"){
			foreach ($arrayEmailDireccion as $idSiip => $infoEmailDireccion) {
				$sqlUpdateEmail   = "UPDATE terceros_direcciones_email SET contacto = '$infoEmailDireccion[contacto]', email = '$infoEmailDireccion[email]' WHERE id_siip='$idSiip' AND activo=1";
				//echo $sqlUpdateEmail;
				$queryUpdateEmail = mysql_query($sqlUpdateEmail,$conexion);
			}
		}
	}

	// SINCRONIZAR LAS ORDENES DE COMPRA VALIDADAS EN EL SIIP, PARA QUE SE CARGUEN EN ERP
	function sincOrdenesCompra($arrayOrden,$conexion){
		// VARIABLES
		$nitProveedor     = $arrayOrden['nitProveedor'];
		$numeroOrden      = $arrayOrden['numeroOrden'];
		$observacion      = $arrayOrden['observacion'];
		$fechaDocumento   = $arrayOrden['fechaDocumento'];
		$fechaFinal       = $arrayOrden['fechaFinal'];

		$idEmpresaErp     = $arrayOrden['idEmpresaErp'];
		$nitEmpresa       = $arrayOrden['nitEmpresa'];
		$idSucursalErp    = $arrayOrden['idSucursalErp'];
		$idBodegaErp      = $arrayOrden['idBodegaErp'];
		$grupoEmpresa     = $arrayOrden['grupoEmpresa'];
		$documentoUsuario = $arrayOrden['documentoUsuario'];

		$codigoCcos       = $arrayOrden['codigo_centro_costos'];
		$idTipoErp        = $arrayOrden['idTipo'];
		$idAreaErp        = $arrayOrden['idArea'];

		// DESCUENTO GLOBAL APLICADO AL PROVEEDOR SI NO TIENE BUENA ENCUESTA
		$descuentoOrden = $arrayOrden['descuentoOrden']*1;
		$totalItems     = $arrayOrden['contItems'];
		$totalOC        = $arrayOrden['totalOC'];

		// VARIABLE DE CONEXION
		$arrayConect = connect($nitEmpresa);
		if($arrayConect['estado'] == 'error'){ return array("estado" => 'error', "msj" => $arrayConect['msj']); }
		$conexion = $arrayConect['conexion'];

		// RANDOMICO REMISION
		$randomico = responseUnicoRanomico();

		// VERIFICAR QUE EL TIPO DE ORDEN DE COMPRA EXISTA
		$sql = "SELECT nombre FROM compras_ordenes_tipos WHERE activo=1 AND id_empresa=$idEmpresaErp AND id=$idTipoErp";
		$query = mysql_query($sql,$conexion);
		$nombreTipoErp = mysql_result($query,0,'nombre');
		if ($nombreTipoErp=='' || is_null($nombreTipoErp)) {
			return array("estado" => "error", "msj" => "Aviso,\\nNo Existe el tipo de la orden de compra relacionada en ERP!");
		}

		//VERIFICAR QUE EL DEPARTAMENTO EXISTA EN ERP
		$sql = "SELECT codigo,nombre FROM costo_departamentos WHERE activo=1 AND id_empresa=$idEmpresaErp AND id=$idAreaErp";
		$query = mysql_query($sql,$conexion);
		$codigoAreaErp = mysql_result($query,0,'codigo');
		$nombreAreaErp = mysql_result($query,0,'nombre');
		if ($codigoAreaErp=='' || is_null($codigoAreaErp)) {
			return array("estado" => "error", "msj" => "Aviso,\\nNo Existe el area de la orden de compra relacionada en ERP!");
		}

		// CONSULTAR SI TIENE AUTORIZACION POR AREA
		$sql="SELECT COUNT(id) AS aut_area FROM costo_autorizadores_ordenes_compra_area WHERE activo=1 AND id_empresa=$id_empresa AND id=$idAreaErp";
		$query = mysql_query($sql,$conexion);
		$aut_area = mysql_result($query,0,'aut_area');
		$autorizacionOrdenErp = ($aut_area>0)? "false" : "true" ;

		//===================================// CONFIGURACION DE TERCERO //===================================//
		$sqlTercero   = "SELECT COUNT(id) AS contTercero, id,numero_identificacion,nombre,codigo
							FROM terceros WHERE numero_identificacion='$nitProveedor' AND id_empresa='$idEmpresaErp' AND activo=1 LIMIT 0,1";
		$queryTercero  = mysql_query($sqlTercero,$conexion);
		$contTercero      = mysql_result($queryTercero, 0, 'contTercero');
		$idTerceroErp     = mysql_result($queryTercero, 0, 'id');
		$nitTerceroErp    = mysql_result($queryTercero,0,'numero_identificacion');
		$terceroErp       = mysql_result($queryTercero,0,'nombre');
		$codigoTerceroErp = mysql_result($queryTercero,0,'codigo');

		// TERCERO EXISTE
		if ($contTercero <= 0 || $contTercero==''){
			return array("estado" => "error", "msj" => "Aviso,\\nNo Existe el tercero en el software ERP, por favor dirijase al modulo de terceros y de click al boton actualizar en el tercero de la orden");
		}

		$idVendedor     = "";
		$nombreUsuario = "";
		if($documentoUsuario > 0){
			$sql    = "SELECT COUNT(id) AS cont,nombre,id FROM empleados WHERE activo=1 AND documento='$documentoUsuario' AND id_empresa = '$idEmpresaErp' LIMIT 0,1";
			$query  = mysql_query($sql,$conexion);
			$contUsuario  = mysql_result($query, 0, 'cont');
			$idUsuario     = mysql_result($query, 0, 'id');
			$nombreUsuario = mysql_result($query, 0, 'nombre');

			if($contUsuario == 0){ $documentoUsuario = ""; }
		}

		//CONSULTAMOS EL ID DEL CENTRO DE COSTOS CON EL CODIGO
		$id_Ccos  = 0;

		if($codigoCcos != '' && $codigoCcos > 0){
			$sqlSelCcos = "SELECT id FROM centro_costos WHERE codigo = '$codigoCcos' AND activo = 1 AND id_empresa = '$idEmpresaErp'";
			$queryCcos  = mysql_query($sqlSelCcos,$conexion);
			$id_Ccos    = mysql_result($queryCcos,0,'id');
		}

		if($codigoCcos == '' || $codigoCcos <= 0 || $id_Ccos==0 || $id_Ccos ==''){
			return array("estado" => "error", "msj" => "Aviso,\\nLa orden de compra debe tener un centro de costos valido para ERP");
		}

		//VERIFICAR SI LA ORDEN EXISTE PARA EN ERP
		$sqlConsulRemision   = "SELECT COUNT(id) AS cont, id, consecutivo
								FROM compras_ordenes
								WHERE consecutivo_siip='$numeroOrden'
									AND id_empresa='$idEmpresaErp'
									AND id_sucursal='$idSucursalErp'
									AND estado <> 3
									AND activo=1
								LIMIT 0,1";
		$queryConsulRemision = mysql_query($sqlConsulRemision,$conexion);

		$contOrdenErp = mysql_result($queryConsulRemision, 0, 'cont');
		$idOrdenErp   = mysql_result($queryConsulRemision, 0, 'id');
		$OrdenErp     = mysql_result($queryConsulRemision, 0, 'consecutivo');
		if($contOrdenErp > 0){ return array("estado" => "ErrorOrdenExiste", "idOrden" => "$idOrdenErp", "numeroOrden" => "$OrdenErp"); }

		$sqlOrden = "INSERT INTO compras_ordenes (
								random,
								id_empresa,
								id_sucursal,
								id_bodega,
								fecha_inicio,
								fecha_vencimiento,
								id_proveedor,
								cod_proveedor,
								nit,
								proveedor,
								consecutivo_siip,
								autorizado,
								observacion,
								id_usuario,
								documento_usuario,
								usuario,
								id_tipo,
								tipo_nombre,
								id_area_solicitante,
								codigo_area_solicitante,
								area_solicitante)
						VALUES('$randomico',
								'$idEmpresaErp',
								'$idSucursalErp',
								'$idBodegaErp',
								'$fechaDocumento',
								'$fechaFinal',
								'$idTerceroErp',
								'$codigoTerceroErp',
								'$nitTerceroErp',
								'$terceroErp',
								'$numeroOrden',
								'$autorizacionOrdenErp',
								'$observacion',
								'$idUsuario',
								'$documentoUsuario',
								'$nombreUsuario',
								'$idTipoErp',
								'$nombreTipoErp',
								'$idAreaErp',
								'$codigoAreaErp',
								'$nombreAreaErp')";
		$queryOrden = mysql_query($sqlOrden,$conexion);

		$sqlOrden2   = "SELECT COUNT(id) AS cont, id FROM compras_ordenes WHERE random='$randomico' AND id_empresa='$idEmpresaErp' LIMIT 0,1";
		$queryOrden2 = mysql_query($sqlOrden2,$conexion);
		$idOrdenErp = mysql_result($queryOrden2, 0, 'id');

		//consultar las retenciones configuradas de ese tercero
		$sql="SELECT id_retencion,retencion,valor FROM terceros_retenciones WHERE activo=1 AND id_proveedor=$idTerceroErp";
		$query = mysql_query($sql,$conexion);
		$insert_retenciones = NULL;
		while ($row = mysql_fetch_array($query)) { 
			// (
			// 	'id_orden_compra'
			// 	'id_tercero'
			// 	'id_retencion'
			// 	'retencion'
			// 	'porcentaje'
			// 	)
			$insert_retenciones .= "(
									'$idOrdenErp',
									'$idTerceroErp',
									'$row[id_retencion]',
									'$row[retencion]',
									'$row[valor]'
									),";
		}

		if($insert_retenciones){
			$insert_retenciones = substr($insert_retenciones, 0, -1);
			$sql="INSERT INTO compras_ordenes_retenciones (id_orden_compra,id_tercero,id_retencion,retencion,porcentaje) VALUES  $insert_retenciones";
			$query = mysql_query($sql,$conexion);
		}


		$valueInsert   = "";
		$whereId       = "";
		$contItems     = 1;
		//$acumDescuento = 0;

		$totalDescOC = $totalOC-$descuentoOrden;//ESTE ES EL TOTAL DE LA OC EN EL ERP SI HAY DESCUENTO

		$acumTotalOC = 0; //EL ACUMULADOR DEL VALOR DE LA OC EN EL ERP CON DESCUENTO

		$ajusteUltimoItem = 'false';

		foreach ($arrayOrden['items'] as $arrayItem) {

			if($descuentoOrden != '' && $descuentoOrden > 0.0){//SOLO SI HAY DESCUENTO
				//ESTO ES PARA CUADRAR LA OC EN EL ULTIMO ITEM CON LOS CENTAVOS FALTANTES O SOBRANTES EN CASO DE QUE LOS HAYA
				if($contItems == $totalItems){
					if($arrayItem['cantidad'] == 1){//SI LA CANTIDAD ES 1 EN EL ULTIMO ITEM ENTONCES A ESTE LE PONE EL AJUSTE
						$totalFaltante = $totalDescOC-$acumTotalOC;//LO QUE FALTA PARA LLEGAR AL TOTAL DE LA OC
						$arrayItem['valorUnitario'] = $totalFaltante;
					}
					else{//SINO LE QUITA UNA UNIDAD PARA CON ESA HACER EL AJUSTE
						$arrayItem['cantidad'] = $arrayItem['cantidad']-1;
						$ajusteUltimoItem = 'true';

						$total_articulo = $arrayItem['cantidad']*$arrayItem['valorUnitario'];//ESTE ES EL TOTAL DEL ARTICULO QUE VIENE DEL SIIP
						$participacion  = $total_articulo/$totalOC;//PORCENTAJE DE PARTICIPACION
						$descuentoIndividual = ($descuentoOrden*$participacion)/$arrayItem['cantidad'];
						$arrayItem['valorUnitario'] = round(($arrayItem['valorUnitario']-$descuentoIndividual),2);
					}
				}
				else{
					$total_articulo = $arrayItem['cantidad']*$arrayItem['valorUnitario'];//ESTE ES EL TOTAL DEL ARTICULO QUE VIENE DEL SIIP

					$participacion = $total_articulo/$totalOC;//PORCENTAJE DE PARTICIPACION
					$descuentoIndividual = ($descuentoOrden*$participacion)/$arrayItem['cantidad'];
					$arrayItem['valorUnitario'] = round(($arrayItem['valorUnitario']-$descuentoIndividual),2);//SIN DECIMALES PARA QUE EN EL ULTIMO ITEM SE AJUSTE LO FALTANTE
				}

				//VA ACUMULANDO EL TOTAL DE LA OC CON EL DESCUENTO
				$acumTotalOC += $arrayItem['cantidad']*$arrayItem['valorUnitario'];
				//return array("estado" => "error", "msj" => "DESCUENTO!!!!");
			}

			$valueInsert .= "('$idOrdenErp',
								'id_Item_Replace_".$arrayItem['codigo']."',
								'".$arrayItem['cantidad']."',
								'".$arrayItem['cantidad']."',
								'".$arrayItem['valorUnitario']."',
								'".$arrayItem['tipo_descuento']."',
								'".$arrayItem['descuento']."',
								'".$arrayItem['impuesto']."',
								'".$arrayItem['observaciones']."',
								'".$id_Ccos."',
								'costo',
								'true'
							),";

			$whereId .= "|| codigo = '".$arrayItem['codigo']."' ";
			$contItems++;

		}
		//SI SE CREA UNA FILA ADICIONAL DE UNA SOLA UNIDAD PARA AJUSTAR CENTAVOS
		if($ajusteUltimoItem == 'true'){// SI SE DEJA UNA UNIDAD APARTE PARA AJUSTAR CENTAVOS
			$totalFaltante = $totalDescOC-$acumTotalOC;//LO QUE FALTA PARA LLEGAR AL TOTAL DE LA OC
			$arrayItem['valorUnitario'] = $totalFaltante;
			$arrayItem['cantidad'] = 1;

			$valueInsert .= "('$idOrdenErp',
								'id_Item_Replace_".$arrayItem['codigo']."',
								'".$arrayItem['cantidad']."',
								'".$arrayItem['cantidad']."',
								'".$arrayItem['valorUnitario']."',
								'".$arrayItem['tipo_descuento']."',
								'".$arrayItem['descuento']."',
								'".$arrayItem['impuesto']."',
								'".$arrayItem['observaciones']."',
								'".$id_Ccos."',
								'costo',
								'true'
							),";

			$whereId .= "|| codigo = '".$arrayItem['codigo']."' ";
		}

		$whereId  = substr($whereId, 3);
		$selectId = "SELECT id,codigo FROM items WHERE activo=1 AND id_empresa='$idEmpresaErp' AND ($whereId)";
		$queryId  = mysql_query($selectId,$conexion);
		while ($row = mysql_fetch_array($queryId)) { $valueInsert = str_replace("id_Item_Replace_".$row['codigo'], $row['id'], $valueInsert); }

		$validateReplace = substr_count($valueInsert, 'id_Item_Replace_');
		if($validateReplace > 0){
			$itemError  = '';
			$arrayItems = explode('id_Item_Replace_', $valueInsert);
			for ($i=1; $i <= $validateReplace; $i++) { $itemError .= (substr($arrayItems[$i]." ", 0, 10)).", "; }

			$itemError = substr($itemError, 0, -2);
			return deleteOrdenCompraError($conexion,'ErrorValidateReplace',$idOrdenErp, "Error,\\nNo se ha sincronizado la orden de compra #".$arrayOrden['numeroOrden']."\\nItems no encontrados en ERP:\\n".$itemError);
		}

		$valueInsert = substr($valueInsert, 0, -1);
		$sqlItems    = "INSERT INTO compras_ordenes_inventario
						(
							id_orden_compra,
							id_inventario,
							cantidad,
							saldo_cantidad,
							costo_unitario,
							tipo_descuento,
							descuento,
							id_impuesto,
							observaciones,
							id_centro_costos,
							check_opcion_contable,
							opcion_costo
						)
						VALUES $valueInsert";
		$queryItems  = mysql_query($sqlItems,$conexion);

		foreach ($arrayOrden['saldoItems'] as $idItem=>$saldoItem) {
			$validateArticulo = validaCantidadArticulos($idOrdenErp,$idItem,$arrayOrden['idSucursalErp'],$arrayOrden['idBodegaErp'],$saldoItem,$conexion);
			if(!$validateArticulo){ return deleteOrdenCompraError($conexion,'ErrorValidateItem',$idOrdenErp, "Error,\\nNo se ha sincronizado el item Codigo ".$resultSoap['codigo']." con el software ERP"); }
		}

		//ACTUALIZAMOS LA ORDEN DE COMPRA PARA DARLA POR TERMINADA
		$sqlUpdate   = "UPDATE compras_ordenes SET estado='1' WHERE id='$idOrdenErp'";
		$queryUpdate = mysql_query($sqlUpdate,$conexion);
		if(!$queryUpdate){ return deleteOrdenCompraError($conexion,'ErrorValidateItem',$idOrdenErp, "Error,\\nNo se ha actualizado la orden de compra # ".$arrayOrden['numeroOrden']); }

		$sqlnumeroOrden = "SELECT consecutivo FROM compras_ordenes WHERE id='$idOrdenErp' AND activo=1 LIMIT 0,1";
		$numeroOrden    = mysql_result(mysql_query($sqlnumeroOrden,$conexion), 0, 'consecutivo');

		$updateInventario = updateInventario($idOrdenErp,$arrayOrden['idSucursalErp'],$arrayOrden['idBodegaErp'],'descontarInventario',$arrayOrden['idEmpresaErp'],$conexion);
		if(!$updateInventario){ return deleteOrdenCompraError($conexion,'ErrorUpdateInventario',$idOrdenErp, "Error,\\nNo se ha actualizado el inventario de la orden de compra #".$arrayOrden['numeroOrden']); }		//RETURN FALSE

		//GENERAMOS EL MOVIMIENTO DE LAS CUENTAS PARA LA REMISION
		$contabilizar = contabilidad($idOrdenErp,$arrayOrden['idSucursalErp'],$arrayOrden['idBodegaErp'],'contabilizar',$arrayOrden['idEmpresaErp'],$conexion);
		if($contabilizar["estado"] != true){ return $contabilizar; }

		// ENVIAR NOTIFICACION PARA LA AUTORIZACION
		if ($aut_area>0) {
			$sql="SELECT email FROM costo_autorizadores_ordenes_compra_area WHERE activo=1 AND id_empresa=$id_empresa AND id_area=$idAreaErp AND orden=1 LIMIT 0,1";
			$query=mysql_query($sql,$conexion);
			$id_empleado = mysql_result($query,0,'email');

			$Subject       = "Orden de Compra Pendiente por Autorizacion";
			$mensaje_email = "Orden de compra a la espera de su Autorizacion";
			enviaEmailAutorizacion($idOrdenErp,$id_empleado,$idEmpresaErp,$Subject,$mensaje_email,$conexion);
		}

		mysql_close($conexion);
		return array("estado" => "true", "idOrden" => "$idOrdenErp", "numeroOrden" => "$numeroOrden");

	}

	// FUNCION PARA ENVIAR LA NOTIFICACION DE LA ORDEN DE COMPRA SINCRONIZADA AL USUARIO QUE DEBE AUTORIZARLA
	function enviaEmailAutorizacion($id_documento,$id_empleado,$id_empresa,$Subject,$mensaje_email,$link){
 		// EVNIAR EMAIL A LOS ENCARGADOS DE AUTORIZAR LAS REQUISICIONES
		include_once('../../../../misc/phpmailer/PHPMailerAutoload.php');
		$mail  = new PHPMailer();
		// echo $mail;
		$sqlConexion   = "SELECT * FROM empresas_config_correo WHERE id_empresa=$id_empresa LIMIT 0,1";
		$queryConexion = mysql_query ($sqlConexion,$link);
		if($row_consulta = mysql_fetch_array($queryConexion)){
			$seguridad     = $row_consulta['seguridad_smtp'];
			$pass          = $row_consulta['password'];
			$user          = $row_consulta['correo'];
			$puerto        = $row_consulta['puerto'];
			$servidor      = $row_consulta['servidor'];
			$from          = $row_consulta['correo'];
			$autenticacion = $row_consulta['autenticacion'];
		}

		if ($user=='') {
			echo '<script>
					alert("No exite ninguna configuracion de correo SMTP!\nConfigure el correo desde el panel de control en el boton configuracion SMTP\nPara que se puedan enviar las notificaciones a las personas encargadas de autorizar el documento");
				</script>';
		}

		//CONSULTAR LA INFORMACION DE LA EMPRESA
		$sqlEmpresa   = "SELECT nombre,tipo_documento_nombre,nit_completo,actividad_economica,pais,ciudad,direccion,razon_social,tipo_regimen,telefono,celular
						FROM empresas
						WHERE id='$id_empresa'
						LIMIT 0,1";
		$queryEmpresa = mysql_query($sqlEmpresa,$link);

		$nombre_empresa        = mysql_result($queryEmpresa,0,'nombre');
		$tipo_documento_nombre = mysql_result($queryEmpresa,0,'tipo_documento_nombre');
		$documento_empresa     = mysql_result($queryEmpresa,0,'nit_completo');
		$ciudad                = mysql_result($queryEmpresa,0,'ciudad');
		$direccion_empresa     = mysql_result($queryEmpresa,0,'direccion');
		$razon_social          = mysql_result($queryEmpresa,0,'razon_social');
		$tipo_regimen          = mysql_result($queryEmpresa,0,'tipo_regimen');
		$telefonos             = mysql_result($queryEmpresa,0,'telefono').' - '.mysql_result($queryEmpresa,0,'celular');
		$actividad_economica   = mysql_result($queryEmpresa,0,'actividad_economica');

		// CONSULTAR LA INFORMACION DEL DOCUMENTO
		$sql   = "SELECT
					consecutivo,
					sucursal,
					bodega,
					fecha_inicio,
					consecutivo,
					nit,
					proveedor,
					observacion,
					documento_usuario,
					usuario,
					id_area_solicitante,
					id_usuario
				FROM compras_ordenes WHERE id='$id_documento'";
		$query = mysql_query($sql,$link);
		$consecutivo         = mysql_result($query,0,'consecutivo');
		$sucursal            = mysql_result($query,0,'sucursal');
		$bodega              = mysql_result($query,0,'bodega');
		$fecha_inicio        = mysql_result($query,0,'fecha_inicio');
		$consecutivo         = mysql_result($query,0,'consecutivo');
		$nit                 = mysql_result($query,0,'nit');
		$proveedor           = mysql_result($query,0,'proveedor');
		$observacion         = mysql_result($query,0,'observacion');
		$documento_usuario   = mysql_result($query,0,'documento_usuario');
		$usuario             = mysql_result($query,0,'usuario');
		$id_area_solicitante = mysql_result($query,0,'id_area_solicitante');
		$id_usuario          = mysql_result($query,0,'id_usuario');

		$mail->IsSMTP();
		$mail->SMTPAuth   = true;                  				// enable SMTP authentication
		$mail->SMTPSecure = $seguridad;                         // sets the prefix to the servier
		$mail->Host       = $servidor;      				    // sets GMAIL as the SMTP server
		$mail->Port       = $puerto;                            // set the SMTP port
		$mail->Username   = $user; // GMAIL username
		$mail->Password   = $pass; // GMAIL password
		$mail->From       = $from;
		$mail->FromName   = "Sistema de Notificaciones Automaticas LOGICALSOFT ERP";
		$mail->Subject    = $Subject;
		$mail->AltBody    = "This is the body when user views in plain text format"; //Text Body
		$mail->WordWrap   = 50; // set word wrap

		$body  = '<font color="black">
				<br>
				<b>'.$razon_social.'</b><br>
				<b>'.$tipo_regimen.'</b><br>
				<b>'.$tipo_documento_nombre.': </b>'.$documento_empresa.'<br>
				<b>Direccion: </b>'.$direccion_empresa.' - <b>'.$ciudad.' </b><br>
				<b>Telefono: </b>'.$telefonos.'<br>

				<br>

				<table>
					<tr>
						<td>Asunto: </td>
						<td>'.$mensaje_email.'</td>
					</tr>
					<tr>
						<td>Consecutivo</td>
						<td style="font-size:24px;font-weight:bold;">'.$consecutivo.'</td>
					</tr>
					<tr>
						<td>Bodega: </td>
						<td> '.$bodega.'</td>
					</tr>
					<tr>
						<td>Sucursal: </td>
						<td>'.$sucursal.'</td>
					</tr>
					<tr>
						<td>Proveedor: </td>
						<td>'.$nit.' - '.$proveedor.' </td>
					</tr>
					<tr>
						<td>Usuario Creador</td>
						<td>'.$documento_usuario.' - '.$usuario.' </td>
					</tr>
				</table>

				<!--El usuario '.$documento_usuario.' - '.$usuario.' creo la Orden de Compra No. <span style="font-size:18px;font-weight:bold;">'.$consecutivo.'</span> en la bodega <b>'.$bodega.'</b> de la sucursal <b>'.$sucursal.'</b> solicitado por '.$documento_solicitante.' - '.$nombre_solicitante.' del area de '.$area_solicitante.'  <br>
				Ingrese a la aplicacion y dirijase al modulo de compras, en la pesta&ntilde;a Ordenes de compra, busque el documento, oprima el boton autorizar y seleccione la opcion de autorizacion que desee<br><br>-->
				<br>
				<br>
				<br>
				Esta es una notificacion automatica generada por el software LogicalSoft ERP, por favor no responda este email.
			</font><br>';

		$mail->Body = $body;
		$mail->MsgHTML($body);

		// CONSULTAR LAS DIRECCIONES DE EMAIL DE LOS ENCARGADOS DE AUTORIZAR EL DOCUMENTO
		$sql="SELECT email_empresa FROM empleados WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_empleado";
		$query=mysql_query($sql,$link);
		$email = mysql_result($query,0,'email_empresa');
		if ($email<>''){
			$mail->AddAddress($email);
			$mail->IsHTML(true); // send as HTML
			if(!$mail->Send()){
				// echo $mail->ErrorInfo.'<script>alert("Se genero la autorizacion pero no se pudo enviar por email las notificaciones a los encargados de autorizar el documento\nSi el problema continua comuniquese con el administrador del sistema");</script>';

				echo '<script>MyLoading2("off",{icono:"fail",texto:"Se genero la autorizacion pero no se envio el email! verifique que todos tengan un email configurado"})</script>';

			}
			$mail->ClearAddresses();

			echo '<script>MyLoading2("off")</script>';
		}
		else{
			echo '<script>MyLoading2("off",{icono:"fail",texto:"El usuario no tiene el email configurado"})</script>';
		}

		// echo '<script>MyLoading2("off")</script>';
		// echo $mail;
 	}

	//=========================================SINCRONIZA LAS OC DEL SIIP CON LAS FACTURAS DE COMPRA DEL ERP============================================================//
	function sincFacturasCompraOC($arrayDatos){
		$idEmpresaErp = $arrayDatos['idEmpresaErp'];
		$nitEmpresa   = $arrayDatos['nitEmpresa'];

		// VARIABLE DE CONEXION
		$arrayConect = connect($nitEmpresa);
		if($arrayConect['estado'] == 'error'){ return array("estado" => 'error', "msj" => $arrayConect['msj']); }
		$conexion = $arrayConect['conexion'];

		$sqlEmpresa   = "SELECT COUNT(id) AS cont,id FROM empresas WHERE id='$idEmpresaErp' AND activo=1 LIMIT 0,1";
		$queryEmpresa = mysql_query($sqlEmpresa,$conexion);
		$contEmpresa  = mysql_result($queryEmpresa, 0, 'cont');
		if(!$queryEmpresa || $contEmpresa == 0 || $contEmpresa == ''){ return array("estado" => 'error', "msj" => json_encode($arrayDatos)); }					//ERROR EN LA CONSULTA MYSQL

		//======================================================LOGICA============================================================

		for($i = 0;$i < count($arrayDatos['documentos']);$i++) {
			$id_orden = $arrayDatos['documentos'][$i];
			$whereFacturas=($whereFacturas!='')? $whereFacturas.' OR CFI.id_consecutivo_referencia='.$id_orden : 'CFI.id_consecutivo_referencia='.$id_orden;
		}

		//TRAEMOS LAS FACTURAS RELACIONADAS CON LAS REMISIONES ENVIADAS DEL SIIP
		$sqlOrdenes   = "SELECT
							CF.consecutivo AS factura,
							CF.documento_usuario AS usuario,
							CONCAT(CF.fecha_generacion,' ',CF.hora_generacion) AS fecha_factura,
							CF.observacion AS observacion,
						 	CFI.id_consecutivo_referencia AS id_orden
						 FROM compras_facturas AS CF
						 INNER JOIN compras_facturas_inventario AS CFI ON (
						 	CF.id = CFI.id_factura_compra
						 	AND CFI.activo = 1
						 	AND ($whereFacturas)
						 	AND CFI.nombre_consecutivo_referencia = 'Orden de Compra'
						 )
						 WHERE CF.estado BETWEEN 1 AND 2
						 	AND CF.activo = 1
						 	AND CF.id_empresa = '$idEmpresaErp'";

			$queryOrdenes = mysql_query($sqlOrdenes,$conexion);

			while($rowOrdenes = mysql_fetch_assoc($queryOrdenes)){

				$cont = $rowOrdenes['id_orden'];

				$arrayFacturas["id_orden_$cont"]['factura']       = $rowOrdenes['factura'];
				$arrayFacturas["id_orden_$cont"]['usuario']       = $rowOrdenes['usuario'];
				$arrayFacturas["id_orden_$cont"]['fecha_factura'] = $rowOrdenes['fecha_factura'];
				$arrayFacturas["id_orden_$cont"]['observacion']   = preg_replace('/[\x00-\x1F]/', '', $rowOrdenes['observacion']);
			}

			mysql_close($conexion);
			return array('estado' => 'true', 'arrayDatos' =>$arrayFacturas); 
	}

	//SINCRONIZA LOS NUMEROS DE REMISION ERP EN LOS PEDIDOS QUE NO LO TENGAN
	function sincPedidosRemisiones($arrayDatos){
		$idEmpresaErp = $arrayDatos['idEmpresaErp'];
		$nitEmpresa   = $arrayDatos['nitEmpresa'];

		// VARIABLE DE CONEXION
		$arrayConect = connect($nitEmpresa);
		
		if($arrayConect['estado'] == 'error'){ return array("estado" => 'error', "msj" => $arrayConect['msj']); }
		$conexion = $arrayConect['conexion'];

		$sqlEmpresa   = "SELECT COUNT(id) AS cont,id FROM empresas WHERE id='$idEmpresaErp' AND activo=1 LIMIT 0,1";
		$queryEmpresa = mysql_query($sqlEmpresa,$conexion);
		$contEmpresa  = mysql_result($queryEmpresa, 0, 'cont');
		if(!$queryEmpresa || $contEmpresa == 0 || $contEmpresa == ''){ return array("estado" => 'error', "msj" => json_encode($arrayDatos)); }					//ERROR EN LA CONSULTA MYSQL

		//======================================================LOGICA============================================================

		//for($i = 0;$i < count($arrayDatos['documentos']);$i++) {
		$codPedidoSiip = $arrayDatos['documentos']['codPedidoSiip'];
		$idSucursalErp = $arrayDatos['documentos']['idSucursalErp'];

		$sqlRemisiones   = "SELECT
							id AS id_remision,
							consecutivo AS consecutivo_remision
						FROM ventas_remisiones
						WHERE
						    estado <> 3
							AND activo = 1
							AND consecutivo_siip='$codPedidoSiip'
							AND id_empresa=$idEmpresaErp
							AND id_sucursal=$idSucursalErp";


		$queryRemisiones = mysql_query($sqlRemisiones,$conexion);

		$arrayRemisiones = array();

		$contador = 0;

		while($rowRemisiones = mysql_fetch_array($queryRemisiones)){

			$cont = $codPedidoSiip.'_'.$idSucursalErp;

			$arrayRemisiones["id_pedido_$cont"]['id_remision']          = $rowRemisiones['id_remision'];
			$arrayRemisiones["id_pedido_$cont"]['consecutivo_remision'] = $rowRemisiones['consecutivo_remision'];

			$contador++;
		}

		if($contador == 0){
			$arrayRemisiones = 'false';
		}

		mysql_close($conexion);
		return array('estado' => 'true', 'arrayDatos' =>$arrayRemisiones);
	}

	//====================== METODOS WEBSERVICE =====================//
	//***************************************************************//
	$post = file_get_contents('php://input');
	// $server->configurewsdl('ApplicationServices',$ns);
	// $server->wsdl->schematargetnamespace=$ns;
	$objSoap = new soap_server();
	$objSoap->register("sincRemision");
	$objSoap->register("insertUpdateItem");
	$objSoap->register("consultarCCos");
	$objSoap->register("sincFacturacionOC");
	$objSoap->register("sincFacturasVentaPedido");
	$objSoap->register("sincOrdenesCompra");
	$objSoap->register("sincFacturasCompraOC");
	$objSoap->register("sincPedidosRemisiones");
	$objSoap->register("consultarImpuesto");
	$objSoap->register("insertUpdateTercero");
	$objSoap->service($post);


?>

