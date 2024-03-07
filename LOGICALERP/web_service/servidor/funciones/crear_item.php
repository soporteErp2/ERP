<?php

	//===================== VERIFICAR QUE EL ARTICULO EXISTA EN LAS BODEGAS ======================//
	function verificaArticuloBodegas($idItem,$id_empresa,$conexion){
		$cuentasColgaap = validacionCuentasDefault($idItem,$id_empresa,$conexion);
		$cuentaNiif     = validacionCuentasNiifDefault($idItem,$id_empresa,$conexion);

		$sql   = "SELECT COUNT(id_item) AS cont FROM inventario_totales WHERE id_item='$idItem' AND id_empresa='$id_empresa' AND activo=1";
		$query = mysql_query($sql,$conexion);

		if(mysql_result($query,0,'cont') > 0){ array("estado" => 'errorContabilizar', 'typeError' => $cuentaNiif); }

		$valueInsert         = '';
		$sqlSucursalBodega   = "SELECT id, id_sucursal FROM empresas_sucursales_bodegas WHERE id_empresa='$id_empresa' AND activo=1 GROUP BY id";
		$querySucursalBodega = mysql_query($sqlSucursalBodega,$conexion);
		while ($row = mysql_fetch_array($querySucursalBodega)){ $valueInsert .= "('$idItem','".$row['id_sucursal']."','".$row['id']."'),"; }

		if($valueInsert != ''){
			$valueInsert         = substr($valueInsert, 0, -1);
			$sqlInsertArticulo   = "INSERT INTO inventario_totales (id_item,id_sucursal,id_ubicacion) VALUES $valueInsert";
			$queryInsertArticulo = mysql_query($sqlInsertArticulo, $conexion);
		}

		return array("estadoContable" => 'true');
	}

	function validacionCuentasDefault($idItem,$id_empresa,$conexion){

		//=============================// CONSULTA ITEM //=============================//
		$sqlItems   = "SELECT inventariable,id_impuesto,opcion_costo,opcion_gasto,opcion_activo_fijo,estado_compra,estado_venta,id_grupo
						FROM items
						WHERE id='$idItem' AND id_empresa='$id_empresa' AND activo=1 LIMIT 0,1";
		$queryItems = mysql_query($sqlItems,$conexion);

		$inventariable      = mysql_result($queryItems,0,'inventariable');
		$id_impuesto        = mysql_result($queryItems,0,'id_impuesto');
		$opcion_gasto       = mysql_result($queryItems,0,'opcion_gasto');
		$opcion_costo       = mysql_result($queryItems,0,'opcion_costo');
		$opcion_activo_fijo = mysql_result($queryItems,0,'opcion_activo_fijo');
		$estadoCompra       = mysql_result($queryItems,0,'estado_compra');
		$estadoVenta        = mysql_result($queryItems,0,'estado_venta');
		$idGrupo            = mysql_result($queryItems,0,'id_grupo');

		//==================// CONSULTA CUENTAS POR DEFECTO EN GRUPO //==================//
		$sqlCuentasGrupo = "SELECT descripcion,estado,cuenta
							FROM asientos_colgaap_default_grupos
							WHERE id_grupo=$idGrupo AND id_empresa='$id_empresa' AND activo=1";
		$queryCuentasGrupo = mysql_query($sqlCuentasGrupo,$conexion);

		while ($rowGrupo = mysql_fetch_assoc($queryCuentasGrupo)) {

			$descripcion = str_replace('items_', '', $rowGrupo['descripcion']);
			$arrayGrupo[$descripcion] = Array('estado' => $rowGrupo['estado'], 'cuenta' => $rowGrupo['cuenta']);
		}

		//==================// CONSULTA ASIENTOS POR DEFECTO EN ITEM //==================//
		$sqlCuentasDefault   = "SELECT descripcion, estado, cuenta FROM asientos_colgaap_default WHERE id_empresa='$id_empresa' AND descripcion LIKE 'items_%'";
		$queryCuentasDefault = mysql_query($sqlCuentasDefault,$conexion);

		while ($rowCuentasDefault = mysql_fetch_array($queryCuentasDefault)) {
			$descripcion = str_replace('items_', '', $rowCuentasDefault['descripcion']);
			$arrayCuentasDefault[$descripcion] = Array('estado' => $rowCuentasDefault['estado'], 'cuenta' => $rowCuentasDefault['cuenta']);

			if(strlen($arrayGrupo[$descripcion]['cuenta']) > 4){
				$arrayCuentasDefault[$descripcion] = Array('estado' => $arrayGrupo[$descripcion]['estado'], 'cuenta' => $arrayGrupo[$descripcion]['cuenta']);
			}
		}

		$sqlCuentasItems = "SELECT id,descripcion,estado FROM items_cuentas WHERE id_items='$idItem' AND activo=1";
		$queryCuentasItems = mysql_query($sqlCuentasItems,$conexion);

		while ($rowCuentasItems = mysql_fetch_array($queryCuentasItems)) {
			$estado      = $rowCuentasItems['estado'];
			$descripcion = $rowCuentasItems['descripcion'];

			$arrayCuentasItems[$estado.'_'.$descripcion] = 'true';
		}

		$whereDelete        = "";
		$valueInsertCuentas = "";

		//======================== COMPRA =======================//
		if($estadoCompra == 'true'){

			// WHERE DELETE CUENTAS QUE NO PERTENECEN A LA CONFIGURACION DEL ITEM
			$whereDelete .= "CONCAT(estado,'_',descripcion)<>'compra_precio' AND CONCAT(estado,'_',descripcion)<>'compra_contraPartida_precio' AND ";
			if($id_impuesto > 0){ $whereDelete .= "CONCAT(estado,'_',descripcion)<>'compra_impuesto' AND "; }
			if($opcion_gasto == 'true'){ $whereDelete .= "CONCAT(estado,'_',descripcion)<>'compra_gasto' AND "; }
			if($opcion_activo_fijo == 'true'){ $whereDelete .= "CONCAT(estado,'_',descripcion)<>'compra_activo_fijo' AND "; }
			if($opcion_costo == 'true'){ $whereDelete .= "CONCAT(estado,'_',descripcion)<>'compra_costo' AND "; }


			if($arrayCuentasItems['compra_precio'] != 'true'){													// PRECIO
				$cuenta = $arrayCuentasDefault['compra_precio']['cuenta'];
				$tipo   = $arrayCuentasDefault['compra_precio']['estado'];

				$valueInsertCuentas .= "('precio','$idItem','$cuenta','$tipo','$id_empresa','compra'),";
			}
			if($arrayCuentasItems['compra_contraPartida_precio'] != 'true'){									// CONTRA-PARTIDA PRECIO
				$cuenta = $arrayCuentasDefault['compra_contraPartida_precio']['cuenta'];
				$tipo   = $arrayCuentasDefault['compra_contraPartida_precio']['estado'];

				$valueInsertCuentas .= "('contraPartida_precio','$idItem','$cuenta','$tipo','$id_empresa','compra'),";
			}
			if($arrayCuentasItems['compra_impuesto'] != 'true'){												// IMPUESTO
				$cuenta = $arrayCuentasDefault['compra_impuesto']['cuenta'];
				$tipo   = $arrayCuentasDefault['compra_impuesto']['estado'];

				$valueInsertCuentas .= "('impuesto','$idItem','$cuenta','$tipo','$id_empresa','compra'),";
			}

			if($opcion_gasto == 'true' && $arrayCuentasItems['compra_gasto'] != 'true'){
				$cuenta = $arrayCuentasDefault['compra_gasto']['cuenta'];
				$tipo   = $arrayCuentasDefault['compra_gasto']['estado'];

				$valueInsertCuentas .= "('gasto','$idItem','$cuenta','$tipo','$id_empresa','compra'),";
			}

			if($opcion_costo == 'true' && $arrayCuentasItems['compra_costo'] != 'true'){
				$cuenta = $arrayCuentasDefault['compra_costo']['cuenta'];
				$tipo   = $arrayCuentasDefault['compra_costo']['estado'];

				$valueInsertCuentas .= "('costo','$idItem','$cuenta','$tipo','$id_empresa','compra'),";
			}
			if($opcion_activo_fijo == 'true' && $arrayCuentasItems['compra_activo_fijo'] != 'true'){
				$cuenta = $arrayCuentasDefault['compra_activo_fijo']['cuenta'];
				$tipo   = $arrayCuentasDefault['compra_activo_fijo']['estado'];

				$valueInsertCuentas .= "('activo_fijo','$idItem','$cuenta','$tipo','$id_empresa','compra'),";
			}
		}

		//======================== VENTA =======================//
		if($estadoVenta == 'true'){

			// WHERE DELETE CUENTAS QUE NO PERTENECEN A LA CONFIGURACION DEL ITEM
			$whereDelete .= "CONCAT(estado,'_',descripcion)<>'venta_precio' AND CONCAT(estado,'_',descripcion)<>'venta_contraPartida_precio' AND ";
			if($id_impuesto > 0){ $whereDelete .= "CONCAT(estado,'_',descripcion)<>'venta_impuesto' AND "; }
			if($inventariable == 'true'){ $whereDelete .= "CONCAT(estado,'_',descripcion)<>'venta_costo' AND CONCAT(estado,'_',descripcion)<>'venta_contraPartida_costo' AND "; }

			if($arrayCuentasItems['venta_precio'] != 'true'){													// PRECIO
				$cuenta = $arrayCuentasDefault['venta_precio']['cuenta'];
				$tipo   = $arrayCuentasDefault['venta_precio']['estado'];

				$valueInsertCuentas .= "('precio','$idItem','$cuenta','$tipo','$id_empresa','venta'),";
			}
			if($arrayCuentasItems['venta_contraPartida_precio'] != 'true'){										// CONTRA-PARTIDA PRECIO
				$cuenta = $arrayCuentasDefault['venta_contraPartida_precio']['cuenta'];
				$tipo   = $arrayCuentasDefault['venta_contraPartida_precio']['estado'];

				$valueInsertCuentas .= "('contraPartida_precio','$idItem','$cuenta','$tipo','$id_empresa','venta'),";
			}
			if($arrayCuentasItems['venta_impuesto'] != 'true'){													// IMPUESTO
				$cuenta = $arrayCuentasDefault['venta_impuesto']['cuenta'];
				$tipo   = $arrayCuentasDefault['venta_impuesto']['estado'];

				$valueInsertCuentas .= "('impuesto','$idItem','$cuenta','$tipo','$id_empresa','venta'),";
			}
			if($inventariable == 'true'){																		// COSTO
				if($arrayCuentasItems['venta_costo'] != 'true'){
					$cuenta = $arrayCuentasDefault['venta_costo']['cuenta'];
					$tipo   = $arrayCuentasDefault['venta_costo']['estado'];

					$valueInsertCuentas .= "('costo','$idItem','$cuenta','$tipo','$id_empresa','venta'),";
				}

				if($arrayCuentasItems['venta_contraPartida_costo'] != 'true'){
					$cuenta = $arrayCuentasDefault['venta_contraPartida_costo']['cuenta'];
					$tipo   = $arrayCuentasDefault['venta_contraPartida_costo']['estado'];

					$valueInsertCuentas .= "('contraPartida_costo','$idItem','$cuenta','$tipo','$id_empresa','venta'),";
				}
			}
		}

		if($valueInsertCuentas != ""){																			// INSERT CUENTAS DEFAULT
			$valueInsertCuentas        = substr($valueInsertCuentas, 0, -1);
			$sqlInsertCuentasDefault   = "INSERT INTO items_cuentas (descripcion,id_items,puc,tipo,id_empresa,estado) VALUES $valueInsertCuentas";
			$queryInsertCuentasDefault = mysql_query($sqlInsertCuentasDefault,$conexion);
			// if(!$queryInsertCuentasDefault){ echo 'false_7'; exit; }
		}

		if($whereDelete != ""){ $whereDelete = "AND ".substr($whereDelete, 0, -4); }
		$sqlDeleteCuentas   = "DELETE FROM items_cuentas WHERE id_empresa='$id_empresa' AND id_items='$idItem' $whereDelete";
		$queryDeleteCuentas = mysql_query($sqlDeleteCuentas,$conexion);
		// if(!$queryDeleteCuentas){ echo 'false_8'; exit; }
	}

	function validacionCuentasNiifDefault($idItem,$id_empresa,$conexion){

		//=============================// CONSULTA ITEM //=============================//
		$sqlItems   = "SELECT inventariable,id_impuesto,opcion_costo,opcion_gasto,opcion_activo_fijo,estado_compra,estado_venta,id_grupo
						FROM items
						WHERE id='$idItem' AND activo=1 AND id_empresa='$id_empresa' LIMIT 0,1";
		$queryItems = mysql_query($sqlItems,$conexion);

		$inventariable      = mysql_result($queryItems,0,'inventariable');
		$id_impuesto        = mysql_result($queryItems,0,'id_impuesto');
		$opcion_gasto       = mysql_result($queryItems,0,'opcion_gasto');
		$opcion_costo       = mysql_result($queryItems,0,'opcion_costo');
		$opcion_activo_fijo = mysql_result($queryItems,0,'opcion_activo_fijo');
		$estadoCompra       = mysql_result($queryItems,0,'estado_compra');
		$estadoVenta        = mysql_result($queryItems,0,'estado_venta');
		$idGrupo            = mysql_result($queryItems,0,'id_grupo');


		//==================// CONSULTA CUENTAS POR DEFECTO EN GRUPO //==================//
		$sqlCuentasGrupo = "SELECT descripcion,estado,cuenta
							FROM asientos_niif_default_grupos
							WHERE id_grupo=$idGrupo AND id_empresa='$id_empresa' AND activo=1";
		$queryCuentasGrupo = mysql_query($sqlCuentasGrupo,$conexion);

		while ($rowGrupo = mysql_fetch_assoc($queryCuentasGrupo)) {

			$descripcion = str_replace('items_', '', $rowGrupo['descripcion']);
			$arrayGrupo[$descripcion] = Array('estado' => $rowGrupo['estado'], 'cuenta' => $rowGrupo['cuenta']);
		}

		//==================// CONSULTA ASIENTOS POR DEFECTO EN ITEM //==================//
		$sqlCuentasDefault   = "SELECT descripcion, estado, cuenta FROM asientos_niif_default WHERE id_empresa='$id_empresa' AND descripcion LIKE 'items_%'";
		$queryCuentasDefault = mysql_query($sqlCuentasDefault,$conexion);

		while ($rowCuentasDefault = mysql_fetch_array($queryCuentasDefault)) {
			$descripcion = str_replace('items_', '', $rowCuentasDefault['descripcion']);
			$arrayCuentasDefault[$descripcion] = Array('estado' => $rowCuentasDefault['estado'], 'cuenta' => $rowCuentasDefault['cuenta']);

			if(strlen($arrayGrupo[$descripcion]['cuenta']) > 4){
				$arrayCuentasDefault[$descripcion] = Array('estado' => $arrayGrupo[$descripcion]['estado'], 'cuenta' => $arrayGrupo[$descripcion]['cuenta']);
			}
		}

		$sqlCuentasItems = "SELECT id,descripcion,estado FROM items_cuentas_niif WHERE id_items='$idItem' AND activo=1";
		$queryCuentasItems = mysql_query($sqlCuentasItems,$conexion);
		// if(!$queryCuentasItems){ echo 'false_6'; exit; }

		while ($rowCuentasItems = mysql_fetch_array($queryCuentasItems)) {
			$estado      = $rowCuentasItems['estado'];
			$descripcion = $rowCuentasItems['descripcion'];

			$arrayCuentasItems[$estado.'_'.$descripcion] = 'true';
		}

		$whereDelete        = "";
		$valueInsertCuentas = "";

		//======================== COMPRA =======================//
		if($estadoCompra == 'true'){

			// WHERE DELETE CUENTAS QUE NO PERTENECEN A LA CONFIGURACION DEL ITEM
			$whereDelete .= "CONCAT(estado,'_',descripcion)<>'compra_precio' AND CONCAT(estado,'_',descripcion)<>'compra_contraPartida_precio' AND ";
			if($id_impuesto > 0){ $whereDelete .= "CONCAT(estado,'_',descripcion)<>'compra_impuesto' AND "; }
			if($opcion_gasto == 'true'){ $whereDelete .= "CONCAT(estado,'_',descripcion)<>'compra_gasto' AND "; }
			if($opcion_costo == 'true'){ $whereDelete .= "CONCAT(estado,'_',descripcion)<>'compra_costo' AND "; }
			if($opcion_activo_fijo == 'true'){ $whereDelete .= "CONCAT(estado,'_',descripcion)<>'compra_activo_fijo' AND "; }


			if($arrayCuentasItems['compra_precio'] != 'true'){													// PRECIO
				$cuenta = $arrayCuentasDefault['compra_precio']['cuenta'];
				$tipo   = $arrayCuentasDefault['compra_precio']['estado'];

				$valueInsertCuentas .= "('precio','$idItem','$cuenta','$tipo','$id_empresa','compra'),";
			}
			if($arrayCuentasItems['compra_contraPartida_precio'] != 'true'){									// CONTRA-PARTIDA PRECIO
				$cuenta = $arrayCuentasDefault['compra_contraPartida_precio']['cuenta'];
				$tipo   = $arrayCuentasDefault['compra_contraPartida_precio']['estado'];

				$valueInsertCuentas .= "('contraPartida_precio','$idItem','$cuenta','$tipo','$id_empresa','compra'),";
			}
			if($arrayCuentasItems['compra_impuesto'] != 'true'){												// IMPUESTO
				$cuenta = $arrayCuentasDefault['compra_impuesto']['cuenta'];
				$tipo   = $arrayCuentasDefault['compra_impuesto']['estado'];

				$valueInsertCuentas .= "('impuesto','$idItem','$cuenta','$tipo','$id_empresa','compra'),";
			}

			if($opcion_gasto == 'true' && $arrayCuentasItems['compra_gasto'] != 'true'){
				$cuenta = $arrayCuentasDefault['compra_gasto']['cuenta'];
				$tipo   = $arrayCuentasDefault['compra_gasto']['estado'];

				$valueInsertCuentas .= "('gasto','$idItem','$cuenta','$tipo','$id_empresa','compra'),";
			}
			if($opcion_costo == 'true' && $arrayCuentasItems['compra_costo'] != 'true'){
				$cuenta = $arrayCuentasDefault['compra_costo']['cuenta'];
				$tipo   = $arrayCuentasDefault['compra_costo']['estado'];

				$valueInsertCuentas .= "('costo','$idItem','$cuenta','$tipo','$id_empresa','compra'),";
			}

			if($opcion_activo_fijo == 'true' && $arrayCuentasItems['compra_activo_fijo'] != 'true'){
				$cuenta = $arrayCuentasDefault['compra_activo_fijo']['cuenta'];
				$tipo   = $arrayCuentasDefault['compra_activo_fijo']['estado'];

				$valueInsertCuentas .= "('activo_fijo','$idItem','$cuenta','$tipo','$id_empresa','compra'),";
			}
		}

		//======================== VENTA =======================//
		if($estadoVenta == 'true'){

			// WHERE DELETE CUENTAS QUE NO PERTENECEN A LA CONFIGURACION DEL ITEM
			$whereDelete .= "CONCAT(estado,'_',descripcion)<>'venta_precio' AND CONCAT(estado,'_',descripcion)<>'venta_contraPartida_precio' AND ";
			if($id_impuesto > 0){ $whereDelete .= "CONCAT(estado,'_',descripcion)<>'venta_impuesto' AND "; }
			if($inventariable == 'true'){ $whereDelete .= "CONCAT(estado,'_',descripcion)<>'venta_costo' AND CONCAT(estado,'_',descripcion)<>'venta_contraPartida_costo' AND "; }


			if($arrayCuentasItems['venta_precio'] != 'true'){													// PRECIO
				$cuenta = $arrayCuentasDefault['venta_precio']['cuenta'];
				$tipo   = $arrayCuentasDefault['venta_precio']['estado'];

				$valueInsertCuentas .= "('precio','$idItem','$cuenta','$tipo','$id_empresa','venta'),";
			}
			if($arrayCuentasItems['venta_contraPartida_precio'] != 'true'){										// CONTRA-PARTIDA PRECIO
				$cuenta = $arrayCuentasDefault['venta_contraPartida_precio']['cuenta'];
				$tipo   = $arrayCuentasDefault['venta_contraPartida_precio']['estado'];

				$valueInsertCuentas .= "('contraPartida_precio','$idItem','$cuenta','$tipo','$id_empresa','venta'),";
			}
			if($arrayCuentasItems['venta_impuesto'] != 'true'){													// IMPUESTO
				$cuenta = $arrayCuentasDefault['venta_impuesto']['cuenta'];
				$tipo   = $arrayCuentasDefault['venta_impuesto']['estado'];

				$valueInsertCuentas .= "('impuesto','$idItem','$cuenta','$tipo','$id_empresa','venta'),";
			}

			if($inventariable == 'true'){																		// COSTO
				if($arrayCuentasItems['venta_costo'] != 'true'){
					$cuenta = $arrayCuentasDefault['venta_costo']['cuenta'];
					$tipo   = $arrayCuentasDefault['venta_costo']['estado'];

					$valueInsertCuentas .= "('costo','$idItem','$cuenta','$tipo','$id_empresa','venta'),";
				}

				if($arrayCuentasItems['venta_contraPartida_costo'] != 'true'){
					$cuenta = $arrayCuentasDefault['venta_contraPartida_costo']['cuenta'];
					$tipo   = $arrayCuentasDefault['venta_contraPartida_costo']['estado'];

					$valueInsertCuentas .= "('contraPartida_costo','$idItem','$cuenta','$tipo','$id_empresa','venta'),";
				}
			}
		}

		if($valueInsertCuentas != ""){																			// INSERT CUENTAS DEFAULT
			$valueInsertCuentas        = substr($valueInsertCuentas, 0, -1);
			$sqlInsertCuentasDefault   = "INSERT INTO items_cuentas_niif (descripcion,id_items,puc,tipo,id_empresa,estado) VALUES $valueInsertCuentas";
			$queryInsertCuentasDefault = mysql_query($sqlInsertCuentasDefault,$conexion);
			// if(!$queryInsertCuentasDefault){ echo 'false_7'; exit; }
		}

		if($whereDelete != ""){ $whereDelete = "AND ".substr($whereDelete, 0, -4); }
		$sqlDeleteCuentas   = "DELETE FROM items_cuentas_niif WHERE id_empresa='$id_empresa' AND id_items='$idItem' $whereDelete";
		$queryDeleteCuentas = mysql_query($sqlDeleteCuentas,$conexion);
		// if(!$queryDeleteCuentas){ echo 'false_8'; exit; }
	}

?>