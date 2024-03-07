<?php



	/**
	* @class depuraDocumentos Clase para depurara los documentos en niif descuadros
	*/
	class depuraDocumentos
	{

		private $mysql;
		private $id_empresa;
		private $arrayDocumentosDescuadrados;
		public $arrayDevolucionesVenta;
		public $arrayDevolucionesCompra;

		/**
		* @method __construct constructor de la clase
		* @param obj objeto de conexion mysql
		* @param int id de empresa a depurar
		*/
		function __construct($mysql,$id_empresa)
		{
			$this->mysql      = $mysql;
			$this->id_empresa = $id_empresa;
		}

		/**
		* @method getDocumentos consulta documentos descuadrados
		*/
		public function getDocumentos()
		{

			set_time_limit(0);
			ini_set("memory_limit", "1024M");

			$sql="SELECT
						SUM(debe-haber) AS diferencia,
						SUM(debe) AS debe,
						SUM(haber) AS haber,
						codigo_cuenta,
						cuenta,
						id_documento,
						consecutivo_documento,
						tipo_documento,
						tipo_documento_extendido,
						nit_tercero,
						tercero,
						id_documento_cruce,
						tipo_documento_cruce,
						numero_documento_cruce,
						sucursal
					FROM asientos_colgaap
					WHERE activo=1
					AND (tipo_documento='NDFV' OR tipo_documento='NDFC' )
					AND id_empresa=$this->id_empresa
					GROUP BY id_documento,tipo_documento,codigo_cuenta
					;
					";
			$query=$this->mysql->query($sql,$this->mysql->link);
			while ($row=$this->mysql->fetch_array($query)) {
				$id_documento  = $row['id_documento'];
				$codigo_cuenta = $row['codigo_cuenta'];
				$tipo_documento = $row['tipo_documento'];

				if ($tipo_documento=='NCG') {
					# code...
				}
				else if ($tipo_documento=='NDFV') {
					$this->arrayDocumentosColgaap[$id_documento][$tipo_documento][$codigo_cuenta] = array(
																							'diferencia'               => $row['diferencia'],
																							'debe'                     => $row['debe'],
																							'haber'                    => $row['haber'],
																							'consecutivo_documento'    => $row['consecutivo_documento'],
																							'tipo_documento'           => $row['tipo_documento'],
																							'tipo_documento_extendido' => $row['tipo_documento_extendido'],
																							'nit_tercero'              => $row['nit_tercero'],
																							'tercero'                  => $row['tercero'],
																							'codigo_cuenta'            => $row['codigo_cuenta'],
																							'cuenta'                   => $row['cuenta'],
																							'sucursal'                 => $row['sucursal'],
																						);
				}

			}

			$sql="SELECT
						SUM(debe-haber) AS diferencia,
						SUM(debe) AS debe,
						SUM(haber) AS haber,
						codigo_cuenta,
						cuenta,
						id_documento,
						consecutivo_documento,
						tipo_documento,
						tipo_documento_extendido,
						nit_tercero,
						tercero,
						id_documento_cruce,
						tipo_documento_cruce,
						numero_documento_cruce
					FROM asientos_niif
					WHERE activo=1
					AND (tipo_documento='NDFV' OR tipo_documento='NDFC' )
					AND id_empresa=$this->id_empresa
					GROUP BY id_documento,tipo_documento,codigo_cuenta
					;
					";
			$query=$this->mysql->query($sql,$this->mysql->link);
			while ($row=$this->mysql->fetch_array($query)) {
				$id_documento   = $row['id_documento'];
				$codigo_cuenta  = $row['codigo_cuenta'];
				$tipo_documento = $row['tipo_documento'];

				if ($tipo_documento=='NCG') {
					# code...
				}
				else if ($tipo_documento=='NDFV') {
					$this->arrayDocumentosNiif[$id_documento][$tipo_documento][$codigo_cuenta] = array(
																							'diferencia'               => $row['diferencia'],
																							'debe'                     => $row['debe'],
																							'haber'                    => $row['haber'],
																							'consecutivo_documento'    => $row['consecutivo_documento'],
																							'tipo_documento'           => $row['tipo_documento'],
																							'tipo_documento_extendido' => $row['tipo_documento_extendido'],
																							'nit_tercero'              => $row['nit_tercero'],
																							'tercero'                  => $row['tercero'],
																							'codigo_cuenta'            => $row['codigo_cuenta'],
																							'cuenta'                   => $row['cuenta'],
																						);
				}

			}

			foreach ($this->arrayDocumentosColgaap as $id_documento => $arrayDoc) {

				foreach ($arrayDoc as $tipo_documento => $arrayDoc2) {
					$debe      = 0;
					$haber     = 0;
					$debeNiif  = 0;
					$haberNiif = 0;
					$cont      = 0;

					foreach ($arrayDoc2 as $cuenta => $arrayColgaap) {
						$indice = substr($cuenta,0,1);
						$debe  = $this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['debe'];
						$haber = $this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['haber'];

						if ($indice=='1' || $indice=='2' || $indice=='3') {
							if ($debe <> $arrayColgaap['debe'] || $haber <> $arrayColgaap['haber']) {
								$this->arrayDocumentosDescuadrados[$id_documento][$tipo_documento] = array(
																										'consecutivo_documento'    => $arrayColgaap['consecutivo_documento'],
																										'tipo_documento'           => $arrayColgaap['tipo_documento'],
																										'tipo_documento_extendido' => $arrayColgaap['tipo_documento_extendido'],
																									);
								if ($tipo_documento=='NDFV') {
									$whereNotasDevolucionVenta .= ($whereNotasDevolucionVenta=='')? " id=$id_documento " : " OR id=$id_documento " ;
								}
								else if ($tipo_documento=='NDFC') {
									$whereNotasDevolucionCompra .= ($whereNotasDevolucionCompra=='')? " id=$id_documento " : " OR id=$id_documento " ;
								}
							}
						}

					}

				}

			}

			foreach ($this->arrayDocumentosDescuadrados as $id_documento => $arrayResul) {
				foreach ($arrayResul as $tipo_documento => $arrayResul2) {
					$this->whereDocumentosColgaap .= ($this->whereDocumentosColgaap=='')? "(A.id_documento=$id_documento AND A.tipo_documento='$tipo_documento')" : " OR (A.id_documento=$id_documento AND A.tipo_documento='$tipo_documento')" ;
				}
			}

			// CONSULTAR LAS CUENTAS DE PAGO DE LA EMPRESA
			$sql="SELECT id,cuenta_niif,estado FROM configuracion_cuentas_pago WHERE id_empresa=$this->id_empresa ";
			$query=$this->mysql->query($sql,$this->mysql->link);
			while ($row=$this->mysql->fetch_array($query)) {
				$arrayCuentasPago[$row['id']] = array('cuenta_niif'=>$row['cuenta_niif'],'estado'=>$row['estado']);
			}

			// CONSULTAR LAS NOTAS DE DEVOLUCION EN VENTA
			$sql="SELECT
					id,
					consecutivo,
					id_documento_venta,
					documento_venta,
					numero_documento_venta,
					id_empresa,
					id_sucursal,
					id_bodega,
					fecha_registro,
					id_centro_costos,
					id_cliente,
					(SELECT exento_iva FROM terceros WHERE activo=1 AND id_empresa=$this->id_empresa AND id=id_cliente) AS exento_iva,
					( SELECT id_configuracion_cuenta_pago FROM ventas_facturas WHERE activo=1 AND id_empresa= $this->id_empresa ANd id=id_documento_venta) AS id_cuenta_pago
					FROM devoluciones_venta WHERE activo=1 AND id_empresa=$this->id_empresa AND documento_venta='Factura' AND ($whereNotasDevolucionVenta)";
			$query=$this->mysql->query($sql,$this->mysql->link);
			while ($row=$this->mysql->fetch_array($query)) {
				$id_nota = $row['id'];
				$this->arrayDevolucionesVenta[$id_nota] = array(
															'consecutivo'            => $row['consecutivo'],
															'idCcos'                 => $row['id_centro_costos'],
															'idBodega'               => $row['id_bodega'],
															'idSucursal'             => $row['id_sucursal'],
															'idEmpresa'              => $this->id_empresa,
															'idFactura'              => $row['id_documento_venta'],
															'idCliente'              => $row['id_cliente'],
															'exento_iva'             => $row['exento_iva'],
															'fecha'                  => $row['fecha_registro'],
															'numero_documento_cruce' => $row['numero_documento_venta'],
															'arrayCuentaPago'        => array(
																								'cuentaNiif' =>$arrayCuentasPago[$row['id_cuenta_pago']]['cuenta_niif'],
																								'estado'     =>$arrayCuentasPago[$row['id_cuenta_pago']]['estado'],
																							),
														);
			}

			// CONSULTAR LAS NOTAS DE DEVOLUCION EN COMPRA
			$sql="SELECT
					id,
					consecutivo,
					id_documento_compra,
					documento_compra,
					numero_documento_compra,
					id_empresa,
					id_sucursal,
					id_bodega,
					fecha_registro,
					id_proveedor,
					( SELECT id_configuracion_cuenta_pago FROM compras_facturas WHERE activo=1 AND id_empresa= $this->id_empresa ANd id=id_documento_venta) AS id_cuenta_pago
					FROM devoluciones_compra WHERE activo=1 AND id_empresa=$this->id_empresa AND documento_compra='FC' AND ($whereNotasDevolucionCompra)";
			$query=$this->mysql->query($sql,$this->mysql->link);
			while ($row=$this->mysql->fetch_array($query)) {
				$id_nota = $row['id'];
				$this->arrayDevolucionesCompra[$id_nota] = array(
															'consecutivo'            => $row['consecutivo'],
															'estadoCuentaPago'       => $arrayCuentasPago[$row['id_cuenta_pago']]['estado'],
															'idNota'                 => $id_nota,
															'idBodega'               => $row['id_bodega'],
															'idSucursal'             => $row['id_sucursal'],
															'idEmpresa'              => $this->id_empresa,
															'idFactura'              => $row['id_documento_compra'],
															'idProveedor'            => $row['id_proveedor'],
															'fecha'                  => $row['fecha_registro'],
															'numero_documento_cruce' => $row['numero_documento_compra'],
														);
			}

			// print_r($this->arrayDevolucionesVenta);
		}

		public function contabilizaDevolucionCompra($estadoCuentaPago,$idNota,$consecutivo,$idBodega,$idSucursal,$idEmpresa,$idFactura,$idProveedor,$fecha,$numero_documento_cruce){
			$typeDocumento   = 'NDFC';
			$decimalesMoneda = 0;

			//===================== QUERY CUENTAS ====================//
	    	/**********************************************************/
			$ivaAcumulado      = 0;
			$costoAcumulado    = 0;
			$precioAcumulado   = 0;
			$impuestoAcumulado = 0;

			$arrayRemisiones   = '';
			$contRemisiones    = 0;
			$acumIdRemisiones  = '';		//CONDICIONAL GLOBAL WHERE SQL IDS REMISIONES

			$whereIdItemsCuentas = '';

			$sqlDoc = "SELECT D.id,
							D.id_inventario AS id_item,
							D.codigo,
							D.cantidad,
							D.costo_unitario AS precio,
							D.descuento,
							D.tipo_descuento,
							D.id_impuesto,
							D.valor_impuesto,
							D.inventariable,
							D.id_centro_costos,
							D.check_opcion_contable,
							if(I.cuenta_compra_devolucion_niif > 0, I.cuenta_compra_devolucion_niif, I.cuenta_compra_niif) AS cuenta_iva
						FROM devoluciones_compra_inventario AS D LEFT JOIN impuestos AS I ON(
								D.id_impuesto = I.id
								AND I.activo=1
							)
						WHERE D.id_devolucion_compra='$idNota' AND D.activo=1
						GROUP BY D.id";
			$queryDoc = $this->mysql->query($sqlDoc,$this->mysql->link);
			while($rowDoc = $this->mysql->fetch_array($queryDoc)){

				//CALCULO DEL PRECIO
				$precio = $rowDoc['precio'] * $rowDoc['cantidad'];
				$costo  = $rowDoc['costo'] * $rowDoc['cantidad'];

				if($rowDoc['descuento'] > 0){ $precio = ($rowDoc['tipo_descuento'] == 'porcentaje')? $precio - ROUND(($rowDoc['descuento']*$precio)/100, $decimalesMoneda) : $precio-$rowDoc['descuento']; }

				$impuesto = 0;
				if($rowDoc['id_impuesto'] > 0 && $rowDoc['valor_impuesto'] > 0){ $impuesto = ROUND($precio * $rowDoc['valor_impuesto']/100, $decimalesMoneda); }

				$whereIdItemsCuentas .= ($whereIdItemsCuentas != '')? ' OR ': ' ';
				$whereIdItemsCuentas .= 'id_item = '.$rowDoc['id_item'];

				$arrayInventarioFactura[$rowDoc['id']]  = array('id_factura_inventario' =>$rowDoc['id'],
																'codigo'                =>$rowDoc['codigo'],
																'impuesto'              =>$impuesto,
																'precio'                =>$precio,
																'id_items'              =>$rowDoc['id_item'],
																'inventariable'         =>$rowDoc['inventariable'],
																'cantidad'              =>$rowDoc['cantidad'],
																'id_centro_costos'      =>$rowDoc['id_centro_costos'],
																'check_opcion_contable' =>$rowDoc['check_opcion_contable'],
																'cuenta_iva'            =>$rowDoc['cuenta_iva']);
			}

			$sqlItemsCuentas = "SELECT id, id_item,descripcion, codigo_puc, caracter
								FROM contabilizacion_compra_venta_niif
								WHERE id_empresa='$idEmpresa' AND tipo_documento='FC' AND id_documento='$idFactura' AND ($whereIdItemsCuentas)
								ORDER BY id_item ASC";
			$queryItemsCuentas = $this->mysql->query($sqlItemsCuentas,$this->mysql->link);

			$whereCuentaCcos = "";
	 		while ($rowCuentasItems = $this->mysql->fetch_array($queryItemsCuentas)) {
				$arrayCuentasItems[$rowCuentasItems['id_item']][$rowCuentasItems['descripcion']] = array('estado' => $rowCuentasItems['caracter'], 'cuenta' => $rowCuentasItems['codigo_puc']);

				$cuenta      = $rowCuentasItems['codigo_puc'];
				$descripcion = $rowCuentasItems['descripcion'];

				if($descripcion == 'precio' || $descripcion == 'gasto' || $descripcion == 'costo' || $descripcion == 'activo_fijo'){ $whereCuentaCcos .= "OR cuenta='$cuenta' "; }
			}

			//CONSULTA CUENTAS CCOS
			$whereCuentaCcos = substr($whereCuentaCcos, 3, -1);
			$sqlCcos   = "SELECT cuenta,centro_costo FROM puc_niif WHERE id_empresa='$idEmpresa' AND activo=1 AND ($whereCuentaCcos)";
			$queryCcos = $this->mysql->query($sqlCcos, $this->mysql->link);

			while ($row = $this->mysql->fetch_assoc($queryCcos)) {
				$cuenta = $row['cuenta'];
				$cCos   = $row['centro_costo'];

				$arrayCuentaCcos[$cuenta] = $cCos;
			}

			$arrayGlobalEstado['debito']  = 0;
			$arrayGlobalEstado['credito'] = 0;

			$arrayItemEstado['debito']  = 0;
			$arrayItemEstado['credito'] = 0;

			$acumSubtotal = 0;
			$acumImpuesto = 0;
			$valueInsertArticulosCuentas = '';

			$contActivosFijos        = 0;
			$valueInsertActivosFijos = '';

			foreach ($arrayInventarioFactura AS $valArrayInventario) {

				$totalContabilizacionItem   = 0;
				$arrayItemEstado['debito']  = 0;
				$arrayItemEstado['credito'] = 0;

				$idItemArray       = $valArrayInventario['id_items'];										//ID ITEM
				$descripcionCuenta = $valArrayInventario['check_opcion_contable'];							//GASTO, COSTO, ACTIVO FIJO
				$cuentaOpcional    = $arrayCuentasItems[$idItemArray][$descripcionCuenta]['cuenta'];		//CUENTA OPCION CONTABILIZACION

				$cuentaPrecio   = ($descripcionCuenta == '')? $arrayCuentasItems[$idItemArray]['precio']['cuenta']: $cuentaOpcional;
				$contraPrecio   = $arrayCuentasItems[$idItemArray]['contraPartida_precio']['cuenta'];
				$cuentaImpuesto = ($valArrayInventario['cuenta_iva'] > 0)? $valArrayInventario['cuenta_iva']: $arrayCuentasItems[$idItemArray]['impuesto']['cuenta'];

				//========================= CONTABILIZACION MERCANCIA PARA LA VENTA, COSTO, GASTO =========================//
				//*********************************************************************************************************//
				//CONDICIONAL SI TIENE CCOS
				$cCosPrecio = 0;
				if($arrayCuentaCcos[$cuentaPrecio] == 'Si'){
					if($valArrayInventario['id_centro_costos'] > 0){ $cCosPrecio = $valArrayInventario['id_centro_costos']; }
					else{ $msjErrorCcosto = '\n'.$valArrayInventario['codigo'].' '.$valArrayInventario['nombre_item']; }
				}

				if($descripcionCuenta != 'activo_fijo'){

					//======================================= CALC PRECIO =====================================//
					if($cuentaPrecio > 0){
						$estado = $arrayCuentasItems[$idItemArray]['precio']['estado'];

						//ARRAY ASIENTO CONTABLE
						if($arrayAsiento[$cCosPrecio][$cuentaPrecio][$estado] > 0){ $arrayAsiento[$cCosPrecio][$cuentaPrecio][$estado] += $valArrayInventario['precio']; }
						else{ $arrayAsiento[$cCosPrecio][$cuentaPrecio][$estado] = $valArrayInventario['precio']; }

						$arrayGlobalEstado[$estado] += $valArrayInventario['precio'];
						$arrayItemEstado[$estado]   += $valArrayInventario['precio'];
						$acumSubtotal               += $valArrayInventario['precio'];

						//===================================== CALC IMPUESTO ========================================//
						if($cuentaImpuesto > 0 && $valArrayInventario['impuesto'] > 0){
							$estado = $arrayCuentasItems[$idItemArray]['impuesto']['estado'];

							//ARRAY ASIENTO CONTABLE
							if($arrayAsiento[0][$cuentaImpuesto][$estado] > 0){ $arrayAsiento[0][$cuentaImpuesto][$estado] += $valArrayInventario['impuesto']; }
							else{ $arrayAsiento[0][$cuentaImpuesto][$estado] = $valArrayInventario['impuesto']; }

							$arrayGlobalEstado[$estado] += $valArrayInventario['impuesto'];
							$arrayItemEstado[$estado]   += $valArrayInventario['impuesto'];
							$acumImpuesto               += $valArrayInventario['impuesto'];
						}

						//===================================== CUENTA COSTO ========================================//
						if($descripcionCuenta == 'costo' || $descripcionCuenta == 'gasto'){
							//CUENTA PRECIO POR CENTRO DE COSTO
							$arrayAsiento[0][$cuentaPrecio]['typeCuenta'] = $descripcionCuenta == 'costo'? 'Costo': 'Gasto';
							$idCentroCostos = $valArrayInventario['id_centro_costos'];

							if($arrayCuentaPrecio[$idCentroCostos][$cuentaPrecio]['precio'] == ''){ $arrayCuentaPrecio[$idCentroCostos][$cuentaPrecio]['precio'] = 0; }
							$arrayCuentaPrecio[$idCentroCostos][$cuentaPrecio]['precio'] += $valArrayInventario['precio'];
							$arrayCuentaPrecio[$idCentroCostos][$cuentaPrecio]['estado']  = $arrayCuentasItems[$idItemArray][$descripcionCuenta]['estado'];
						}

						//============================== CALC CONTRA PARTIDA PRECIO =================================//
						if($contraPrecio > 0){
							$estado = $arrayCuentasItems[$idItemArray]['contraPartida_precio']['estado'];

							$contraSaldo = ($arrayItemEstado['debito'] > $arrayItemEstado['credito'])? $arrayItemEstado['debito'] - $arrayItemEstado['credito']
											: $arrayItemEstado['credito'] - $arrayItemEstado['debito'];

							//ARRAY ASIENTO CONTABLE
							if($arrayAsiento[0][$contraPrecio][$estado] > 0){ $arrayAsiento[0][$contraPrecio][$estado] += $contraSaldo; }
							else{ $arrayAsiento[0][$contraPrecio][$estado] = $contraSaldo; }

							$arrayGlobalEstado[$estado] += $contraSaldo;
							$arrayItemEstado[$estado]   += $contraSaldo;

							$acumCuentaClientes 		 = $contraPrecio;
							$acumEstadoCuentaClientes	 = $estado;
						}
					}
					else if($valArrayInventario['inventariable'] == 'false'){
						echo '<script>alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' No se ha configurado en la contabilizacion")</script>'; exit;
					}

					if($arrayItemEstado['debito'] != $arrayItemEstado['credito']){
						echo '<script>alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' No establece doble partida por favor revise la configuracion de contabilizacion")</script>'; exit;
					}
					else if($arrayItemEstado['debito'] == 0 || $arrayItemEstado['credito']==0){
						echo '<script>alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' Establece doble partida con valor 0, por favor revise la configuracion de contabilizacion")</script>';
					}
				}
				//======================================= CONTABILIZACION ACTIVO FIJO ======================================//
				//**********************************************************************************************************//
				else{																								//CONTABILIZACION ACTIVO FIJO Y GASTOS
					if($cuentaPrecio == ''){
						echo '<script>alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' no tiene asignado una cuenta en configuracion items")</script>'; exit;
					}

					//======================================= CALC PRECIO =======================================//
					if($cuentaPrecio > 0){
						$totalContabilizacionItem += $valArrayInventario['precio'];

						//==================================== INSERT ACTIVOS FIJOS =======================================//
						if($descripcionCuenta == 'activo_fijo'){ $contActivosFijos++; }

						//===================================== CALC IMPUESTO ========================================//
						if($cuentaImpuesto > 0 && $valArrayInventario['impuesto'] > 0){
							$estado                     = $arrayCuentasItems[$idItemArray]['impuesto']['estado'];
							$acumImpuesto               += $valArrayInventario['impuesto'];
							$totalContabilizacionItem   += $valArrayInventario['impuesto'];
						}

						//============================== CALC CONTRA PARTIDA PRECIO =================================//
						if($contraPrecio > 0){
							$estado = $arrayCuentasItems[$idItemArray]['contraPartida_precio']['estado'];

							$arrayGlobalEstado[$estado] += $totalContabilizacionItem;
							$arrayItemEstado[$estado]   += $totalContabilizacionItem;

							if($arrayAsiento[0][$contraPrecio][$estado] > 0){ $arrayAsiento[0][$contraPrecio][$estado] += $totalContabilizacionItem; }
							else{ $arrayAsiento[0][$contraPrecio][$estado] = $totalContabilizacionItem; }

							//ARRAY ASIENTO CONTABLE PRECIO
							$estadoPrecio                     = $arrayCuentasItems[$idItemArray][$descripcionCuenta]['estado'];
							$arrayGlobalEstado[$estadoPrecio] += $totalContabilizacionItem;
							$arrayItemEstado[$estadoPrecio]   += $totalContabilizacionItem;
							$acumSubtotal                     += $valArrayInventario['precio'];

							//ARRAY ASIENTO CONTABLE ACTIVO FIJO O GASTO
							if($arrayAsiento[$cCosPrecio][$cuentaPrecio][$estadoPrecio] > 0){ $arrayAsiento[$cCosPrecio][$cuentaPrecio][$estadoPrecio] += $totalContabilizacionItem; }
							else{ $arrayAsiento[$cCosPrecio][$cuentaPrecio][$estadoPrecio] = $totalContabilizacionItem; }

							$acumCuentaClientes       = $contraPrecio;
							$acumEstadoCuentaClientes = $estado;
						}
					}
					else if($valArrayInventario['inventariable'] == 'false'){
						echo '<script>alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' No se ha configurado en la contabilizacion")</script>'; exit;
					}

					if($arrayItemEstado['debito'] != $arrayItemEstado['credito']){
						echo '<script>alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' No establece doble partida por favor revise la configuracion de contabilizacion")</script>'; exit;
					}
					else if($arrayItemEstado['debito'] == 0 || $arrayItemEstado['credito']==0){
						echo '<script>alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' Establece doble partida con valor 0, por favor revise la configuracion de contabilizacion")</script>'; exit;
					}
				}
				if($msjErrorCcosto != ''){ echo '<script>alert("Aviso.\nLos siguientes items no tienen centro de costo \n'.$msjErrorCcosto.'")</script>'; exit; }
			}

			$arrayGlobalEstado['debito'] = round($arrayGlobalEstado['debito']);
			$arrayGlobalEstado['credito'] = round($arrayGlobalEstado['credito']);

			if($arrayGlobalEstado['debito'] != $arrayGlobalEstado['credito']){
				echo '<script>alert("Aviso.\nHa ocurrido un problema de contabilizacion, favor revise la configuracion de contabilizacion.\nSi el problema persiste consulte al administrador del sistema")</script>'; exit;
			}
			else if($arrayGlobalEstado['debito'] == 0 || $arrayGlobalEstado['credito'] == 0 || $arrayGlobalEstado['debito'] =='' || $arrayGlobalEstado['credito'] ==''){
				echo '<script>alert("Aviso.\nContabilizacion en saldo 0.\nSi el problema persiste consulte al administrador del sistema")</script>'; exit;
			}

			//==================== QUERY RETENCIONES =================//
	    	/**********************************************************/
			$acumRetenciones  = 0;
			$contRetencion    = 0;
			$estadoRetencion  = $acumEstadoCuentaClientes; 													//ESTADO CONTRARIO DE LAS RETENCIONES A LA CUENTA CLIENTES
			$sqlRetenciones   = "SELECT valor,codigo_cuenta_niif,tipo_retencion,cuenta_autoretencion_niif,base FROM compras_facturas_retenciones WHERE id_factura_compra='$idFactura' AND activo=1";
			$queryRetenciones = $this->mysql->query($sqlRetenciones,$this->mysql->link);

			while($rowRetenciones = $this->mysql->fetch_array($queryRetenciones)){
				// $valorBase           = $rowRetenciones['base'];
				if ($tipoRetencion=='ReteIva') {
	            	$valorBase =($acumImpuesto>=$rowRetenciones['base'])? 0 : $rowRetenciones['base'] ;
		        }
		        else{
		            $valorBase =($acumSubtotal>=$rowRetenciones['base'])? 0 : $rowRetenciones['base'] ;
		        }
				$valorRetencion      = $rowRetenciones['valor'];
				$codigoRetencion     = $rowRetenciones['codigo_cuenta_niif'];
				$tipoRetencion       = $rowRetenciones['tipo_retencion'];
				$cuentaAutoretencion = $rowRetenciones['cuenta_autoretencion_niif'];

				if(is_nan($arrayAsiento[0][$codigoRetencion][$estadoRetencion])){ $arrayAsiento[0][$codigoRetencion][$estadoRetencion] = 0; }

				if($tipoRetencion == "ReteIva"){ 																		//CALCULO RETEIVA
					if($acumImpuesto<$valorBase) { continue; }													//VALIDACION BASE

					$acumRetenciones += ROUND($acumImpuesto * $valorRetencion/100, $decimalesMoneda);
					$arrayAsiento[0][$codigoRetencion][$estadoRetencion] += ROUND($acumImpuesto * $valorRetencion/100, $decimalesMoneda);
				}
				else{ 																									//CALCULO RETE Y RETEICA
					if($acumSubtotal<$valorBase) { continue; }													//VALIDACION BASE

					$acumRetenciones += ROUND($acumSubtotal*$valorRetencion/100, $decimalesMoneda);
					$arrayAsiento[0][$codigoRetencion][$estadoRetencion] += ROUND($acumSubtotal*$valorRetencion/100, $decimalesMoneda);
				}

				if($tipoRetencion == "AutoRetencion"){ 																	//SI ES AUTORETENCION NO SE DESCUENTA A CLIENTES

					if(is_nan($cuentaAutoretencion)|| $cuentaAutoretencion < 10000){ echo '<script>alert("Aviso.\nNo se ha configurado la cuenta Niif Autorretencion.")</script>'; exit; }

					if(is_nan($arrayAsiento[0][$cuentaAutoretencion][$acumEstadoCuentaClientes])){ $arrayAsiento[0][$cuentaAutoretencion][$acumEstadoCuentaClientes] = 0; }
					$estadoReteCree  = ($estadoRetencion != 'debito')? 'debito' : 'credito';
					$acumRetenciones -= ROUND($acumSubtotal*$valorRetencion/100, $decimalesMoneda);
					$arrayAsiento[0][$cuentaAutoretencion][$estadoReteCree] += $arrayAsiento[0][$codigoRetencion][$estadoRetencion];
				}
			}

			$arrayAsiento[0][$acumCuentaClientes][$acumEstadoCuentaClientes] -= $acumRetenciones;

			$totalDebito  = 0;
			$totalCredito = 0;
			$tablaDebug   = '<div style="float:left; width:80px;">Debito</div>
							<div style="float:left; width:80px;">Credito</div>
							<div style="float:left; width:80px;">PUC</div>
							<div style="float:left; width:150px;">Id Centro Costos</div><br>';

			//CUENTA COSTO POR CENTRO DE COSTO
			foreach ($arrayAsiento AS $idCcos => $arrayCuenta) {
				foreach ($arrayCuenta AS $cuenta => $arrayCampo) {

					$totalDebito  += ($arrayCampo['credito'] > 0)? $arrayCampo['credito'] : 0;
					$totalCredito += ($arrayCampo['debito'] > 0)? $arrayCampo['debito'] : 0;

					if(is_nan($cuenta) || $cuenta==0){ continue; }
					$cuenta = $cuenta * 1;

					if($estadoCuentaPago == 'Credito' && $arrayCampo['type']=='cuentaPago'){
						$saldoGlobalNotaSinAbono += ($arrayCampo['debito'] > $arrayCampo['credito'])? $arrayCampo['debito']: $arrayCampo['credito'];
					}

					$valueInsertAsientos .= "('$idNota',
											'$consecutivo',
											'NDFC',
											'Nota Devolucion Factura de Compra',
											'$idFactura',
											'FC',
											'$numero_documento_cruce',
											'$fecha',
											'".$arrayCampo['credito']."',
											'".$arrayCampo['debito']."',
											'$cuenta',
											'$idProveedor',
											'$idSucursal',
											'$idEmpresa',
											'$idCcos'),";

					$tablaDebug .= '<div style="float:left; width:80px;">*'.$arrayCampo['credito'].'</div>
									<div style="float:left; width:80px;">-'.$arrayCampo['debito'].'</div>
									<div style="float:left; width:80px;">-'.$cuentaPrecio.'</div>
									<div style="float:left; width:150px;">-'.$idCentroCosto.'</div><br>';
				}
			}

			$saldoGlobalNotaSinAbono = ROUND($saldoGlobalNotaSinAbono, $decimalesMoneda);

			$totalDebito  = round($totalDebito,$_SESSION['DECIMALESMONEDA']);
			$totalCredito = round($totalCredito,$_SESSION['DECIMALESMONEDA']);

			$tablaDebug .= '<div style="float:left; width:80px; border-top:1px solid">-'.$totalCredito.'</div><div style="float:left; width:80px; border-top:1px solid">'.$totalDebito.'</div><br>';
			if($totalDebito != $totalCredito){ echo '<script>alert("Aviso.\nLa contabilizacion ingresada no cumple doble partida, Confirme su configuracion en el modulo panel de control.")</script>'; exit; }
			//echo $tablaDebug; exit;

			$valueInsertAsientos = substr($valueInsertAsientos, 0, -1);
			$sqlInsertCuentas = "INSERT INTO asientos_niif (
											id_documento,
											consecutivo_documento,
											tipo_documento,
											tipo_documento_extendido,
											id_documento_cruce,
											tipo_documento_cruce,
											numero_documento_cruce,
											fecha,
											debe,
											haber,
											codigo_cuenta,
											id_tercero,
											id_sucursal,
											id_empresa,
											id_centro_costos)
										VALUES $valueInsertAsientos";
			$queryInsertCuentas = $this->mysql->query($sqlInsertCuentas,$this->mysql->link);
	    }

		public function contabilizaDevolucionVenta($arrayCuentaPago,$idCcos,$idNota,$consecutivo,$idBodega,$idSucursal,$idEmpresa,$idFactura,$idCliente,$exento_iva,$fecha,$numero_documento_cruce){
			$decimalesMoneda  = 0;
			$cuentaPagoNiif   = $arrayCuentaPago['cuentaNiif'];
			$estadoCuentaPago = $arrayCuentaPago['estado'];

			//===================== QUERY CUENTAS ====================//
	    	/**********************************************************/
			$ivaAcumulado       = 0;
			$costoAcumulado     = 0;
			$precioAcumulado    = 0;
			$impuestoAcumulado  = 0;

			$arrayRemisiones    = '';
			$contRemisiones     = 0;
			$acumIdRemisiones   = '';		//CONDICIONAL GLOBAL WHERE SQL IDS REMISIONES

			$whereIdItemsCuentas = '';

			$sqlDoc = "SELECT D.id,
							D.id_inventario AS id_item,
							D.codigo,
							D.cantidad,
							D.costo_unitario AS precio,
							D.costo_inventario AS costo,
							D.descuento,
							D.tipo_descuento,
							D.id_impuesto,
							D.valor_impuesto,
							D.inventariable,
							if(I.cuenta_venta_devolucion_niif > 0, I.cuenta_venta_devolucion_niif, I.cuenta_venta_niif) AS cuenta_iva
						FROM devoluciones_venta_inventario AS D LEFT JOIN impuestos AS I ON(
								D.id_impuesto = I.id
								AND I.activo=1
							)
						WHERE D.id_devolucion_venta='$idNota' AND D.activo=1;";
			$queryDoc = $this->mysql->query($sqlDoc,$this->mysql->link);

			while($rowDoc = $this->mysql->fetch_array($queryDoc)){

				//CALCULO DEL PRECIO
				$precio = $rowDoc['precio']* $rowDoc['cantidad'];
				$costo  = $rowDoc['costo']* $rowDoc['cantidad'];
				if($rowDoc['descuento'] > 0){
					$precio = ($rowDoc['tipo_descuento'] == 'porcentaje')? $precio-(($rowDoc['descuento']*$precio)/100) : $precio-$rowDoc['descuento'];
				}

				$impuesto = 0;
				if($rowDoc['id_impuesto'] > 0 && $rowDoc['valor_impuesto'] > 0){ $impuesto = ROUND($precio*$rowDoc['valor_impuesto']/100,$decimalesMoneda); }

				$whereIdItemsCuentas .= ($whereIdItemsCuentas != '')? ' OR ': ' ';
				$whereIdItemsCuentas .='id_item = '.$rowDoc['id_item'];

				$arrayInventarioFactura[$rowDoc['id']]  = Array('id_factura_inventario' =>$rowDoc['id'],
																'precio'                =>$precio,
																'codigo'                =>$rowDoc['codigo'],
																'impuesto'              =>$impuesto,
																'costo'                 =>$costo,
																'id_items'              =>$rowDoc['id_item'],
																'inventariable'         =>$rowDoc['inventariable'],
																'cuenta_iva'            =>$rowDoc['cuenta_iva']);
			}

			$sqlItemsCuentas = "SELECT id, id_item, descripcion, codigo_puc, caracter
								FROM contabilizacion_compra_venta_niif
								WHERE id_empresa='$idEmpresa' AND tipo_documento='FV' AND id_documento='$idFactura' AND ($whereIdItemsCuentas)
								ORDER BY id_item ASC";

			$queryItemsCuentas = $this->mysql->query($sqlItemsCuentas,$this->mysql->link);

			while ($rowCuentasItems = $this->mysql->fetch_array($queryItemsCuentas)) {
				if($rowCuentasItems['descripcion'] == 'contraPartida_precio'){ $rowCuentasItems['codigo_puc'] = $cuentaPagoNiif; }
				else if($rowCuentasItems['descripcion'] == 'impuesto'){ $rowCuentasItems['codigo_puc'] = 24080201; }

				// CAMBIO CUENTA 4135 POR CUENTA DEVOLUCION 4175
				// $cuenta4 = substr($rowCuentasItems['codigo_puc'], 0, 4);
				// if($cuenta4 == 4135){ $rowCuentasItems['codigo_puc'] = 41750101; }

				$arrayCuentasItems[$rowCuentasItems['id_item']][$rowCuentasItems['descripcion']]= array('estado' => $rowCuentasItems['caracter'], 'cuenta'=> $rowCuentasItems['codigo_puc']);
			}

			$arrayGlobalEstado['debito']  = 0;
			$arrayGlobalEstado['credito'] = 0;

			$arrayItemEstado['debito']    = 0;
			$arrayItemEstado['credito']   = 0;

			$acumSubtotal = 0;
			$acumImpuesto = 0;

			foreach ($arrayInventarioFactura AS $valArrayInventario) {

				$arrayItemEstado['debito']  = 0;
				$arrayItemEstado['credito'] = 0;

				$idItemArray    = $valArrayInventario['id_items'];
				$cuentaPrecio   = $arrayCuentasItems[$idItemArray]['precio']['cuenta'];
				$contraPrecio   = $arrayCuentasItems[$idItemArray]['contraPartida_precio']['cuenta'];
				$cuentaImpuesto = ($valArrayInventario['cuenta_iva'] > 0)? $valArrayInventario['cuenta_iva']: $arrayCuentasItems[$idItemArray]['impuesto']['cuenta'];

				$cuentaCosto = $arrayCuentasItems[$idItemArray]['costo']['cuenta'];
				$contraCosto = $arrayCuentasItems[$idItemArray]['contraPartida_costo']['cuenta'];

				//======================================= CALC PRECIO =====================================//
				if($cuentaPrecio > 0){
					$estado = $arrayCuentasItems[$idItemArray]['precio']['estado'];

					//ARRAY ASIENTO CONTABLE
					if($arrayAsiento[$cuentaPrecio][$estado] > 0){ $arrayAsiento[$cuentaPrecio][$estado] += $valArrayInventario['precio']; }
					else{ $arrayAsiento[$cuentaPrecio][$estado] = $valArrayInventario['precio']; }
					$arrayAsiento[$cuentaPrecio]['idCcos'] = $idCcos;

					$arrayGlobalEstado[$estado] += $valArrayInventario['precio'];
					$arrayItemEstado[$estado]   += $valArrayInventario['precio'];
					$acumSubtotal               += $valArrayInventario['precio'];

					//===================================== CALC IMPUESTO ========================================//
					if($cuentaImpuesto > 0){
						$estado = $arrayCuentasItems[$idItemArray]['impuesto']['estado'];

						//ARRAY ASIENTO CONTABLE
						if($arrayAsiento[$cuentaImpuesto][$estado] > 0){ $arrayAsiento[$cuentaImpuesto][$estado] += $valArrayInventario['impuesto']; }
						else{ $arrayAsiento[$cuentaImpuesto][$estado] = $valArrayInventario['impuesto']; }
						$arrayAsiento[$cuentaImpuesto]['idCcos'] = 0;

						$arrayGlobalEstado[$estado] += $valArrayInventario['impuesto'];
						$arrayItemEstado[$estado]   += $valArrayInventario['impuesto'];
						$acumImpuesto               += $valArrayInventario['impuesto'];
					}

					//============================== CALC CONTRA PARTIDA PRECIO =================================//
					if($contraPrecio > 0){
						$estado = $arrayCuentasItems[$idItemArray]['contraPartida_precio']['estado'];

						$contraSaldo = ($arrayItemEstado['debito'] > $arrayItemEstado['credito'])? $arrayItemEstado['debito'] - $arrayItemEstado['credito']
										: $arrayItemEstado['credito'] - $arrayItemEstado['debito'];

						//ARRAY ASIENTO CONTABLE
						if($arrayAsiento[$contraPrecio][$estado] > 0){ $arrayAsiento[$contraPrecio][$estado] += $contraSaldo; }
						else{ $arrayAsiento[$contraPrecio][$estado] = $contraSaldo; }
						$arrayAsiento[$contraPrecio]['idCcos'] = 0;

						$arrayGlobalEstado[$estado] += $contraSaldo;
						$arrayItemEstado[$estado]   += $contraSaldo;

						$acumCuentaClientes 		 = $arrayCuentasItems[$idItemArray]['contraPartida_precio']['cuenta'];
						$acumEstadoCuentaClientes	 = $estado;
					}
				}
				else if($valArrayInventario['inventariable'] == 'false'){
					echo '<script>alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' No se ha configurado en la contabilizacion")</script>'; exit;
				}

				//======================================= CALC COSTO ===========================================//
				if( $cuentaCosto > 0  && $contraCosto > 0 ){

					$estado = $arrayCuentasItems[$idItemArray]['costo']['estado'];

					//ARRAY ASIENTO CONTABLE
					if($arrayAsiento[$cuentaCosto][$estado] > 0){ $arrayAsiento[$cuentaCosto][$estado] += $valArrayInventario['costo']; }
					else{ $arrayAsiento[$cuentaCosto][$estado] = $valArrayInventario['costo']; }
					$arrayAsiento[$cuentaCosto]['idCcos'] = 0;

					$arrayGlobalEstado[$estado] += $valArrayInventario['costo'];
					$arrayItemEstado[$estado]   += $valArrayInventario['costo'];

					$estado = $arrayCuentasItems[$idItemArray]['contraPartida_costo']['estado'];

					//ARRAY ASIENTO CONTABLE
					if($arrayAsiento[$contraCosto][$estado] > 0){ $arrayAsiento[$contraCosto][$estado] += $valArrayInventario['costo']; }
					else{ $arrayAsiento[$contraCosto][$estado] = $valArrayInventario['costo']; }
					$arrayAsiento[$contraCosto]['idCcos'] = $idCcos;

					$arrayGlobalEstado[$estado] += $valArrayInventario['costo'];
					$arrayItemEstado[$estado]   += $valArrayInventario['costo'];
				}
				else if($valArrayInventario['inventariable'] == 'true'){
					echo '<script>alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' No se ha configurado el manejo del costo en la contabilizacion")</script>'; exit;
				}

				if($arrayItemEstado['debito'] != $arrayItemEstado['credito']){
					echo '<script>alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' No establece doble partida por favor revise la configuracion de contabilizacion")</script>'; exit;
				}
				else if($arrayItemEstado['debito'] == 0 || $arrayItemEstado['credito']==0){
					echo '<script>alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' Establece doble partida con valor 0, por favor revise la configuracion de contabilizacion")</script>'; exit;
				}
			}
			$arrayGlobalEstado['debito']=round($arrayGlobalEstado['debito'],$_SESSION['DECIMALESMONEDA']);
			$arrayGlobalEstado['credito'] = round($arrayGlobalEstado['credito'],$_SESSION['DECIMALESMONEDA']);

			if($arrayGlobalEstado['debito'] != $arrayGlobalEstado['credito']){
				echo '<script>alert("Aviso.\nHa ocurrido un problema de contabilizacion, favor revise la configuracion de contabilizacion.\nSi el problema persiste consulte al administrador del sistema")</script>'; exit;
			}

			//==================== QUERY RETENCIONES =================//
	    	/**********************************************************/
			$acumRetenciones  = 0;
			$contRetencion    = 0;
			$estadoRetencion  = $acumEstadoCuentaClientes; 													//ESTADO CONTRARIO DE LAS RETENCIONES A LA CUENTA CLIENTES
			$sqlRetenciones   = "SELECT valor,codigo_cuenta,tipo_retencion,cuenta_autoretencion_niif,base FROM ventas_facturas_retenciones WHERE id_factura_venta='$idFactura' AND activo=1";
			$queryRetenciones = $this->mysql->query($sqlRetenciones,$this->mysql->link);


			while($rowRetenciones = $this->mysql->fetch_array($queryRetenciones)){
				$valorBase           = $rowRetenciones['base'];
				$valorRetencion      = $rowRetenciones['valor'];
				$codigoRetencion     = $rowRetenciones['codigo_cuenta'];
				$tipoRetencion       = $rowRetenciones['tipo_retencion'];
				$cuentaAutoretencion = $rowRetenciones['cuenta_autoretencion_niif'];

				if(is_nan($arrayAsiento[$codigoRetencion][$estadoRetencion])){ $arrayAsiento[$codigoRetencion][$estadoRetencion] = 0; }

				if($tipoRetencion == "ReteIva"){ 																		//CALCULO RETEIVA
					if ($exento_iva=='Si' || $acumImpuesto<$valorBase) { continue; }									//EXCENTO RETEIVA O NO CUMPLE BASE

					$acumRetenciones += ROUND($acumImpuesto * $valorRetencion/100, $decimalesMoneda);
					$arrayAsiento[$codigoRetencion][$estadoRetencion] += ROUND($acumImpuesto * $valorRetencion/100, $decimalesMoneda);
				}
				else{ 																									//CALCULO RETE, RETECREE Y RETEICA
					if ($acumSubtotal<$valorBase) { continue; }

					$acumRetenciones += ROUND($acumSubtotal*$valorRetencion/100, $decimalesMoneda);
					$arrayAsiento[$codigoRetencion][$estadoRetencion] += ROUND($acumSubtotal*$valorRetencion/100, $decimalesMoneda);
				}

				if($tipoRetencion == "AutoRetencion"){ 																		//SI ES RETECREE NO SE DESCUENTA RETENCION  A CLIENTES
					if(is_nan($cuentaAutoretencion)|| $cuentaAutoretencion < 10000){ echo '<script>alert("Aviso.\nNo se ha configurado la cuenta Niif Autorretencion.")</script>'; exit; }

					if(is_nan($arrayAsiento[$cuentaAutoretencion][$acumEstadoCuentaClientes])){ $arrayAsiento[$cuentaAutoretencion][$acumEstadoCuentaClientes] = 0; }
					$estadoReteCree  = ($estadoRetencion != 'debito')? 'debito' : 'credito';
					$acumRetenciones -= ROUND($acumSubtotal*$valorRetencion/100, $decimalesMoneda);
					$arrayAsiento[$cuentaAutoretencion][$estadoReteCree] += $arrayAsiento[$codigoRetencion][$estadoRetencion];
				}
			}

			$arrayAsiento[$acumCuentaClientes][$acumEstadoCuentaClientes] -= $acumRetenciones;

			$totalDebito  = 0;
			$totalCredito = 0;

			$tablaDebug = '<div style="float:left; width:80px;">Debito</div><div style="float:left; width:80px;">Credito</div><div style="float:left; width:80px;">PUC</div><br>';

			foreach ($arrayAsiento AS $cuenta => $arrayCampo) {

				if ($arrayCampo['debito']==0 && $arrayCampo['credito']==0) { continue; }

				$totalDebito  += $arrayCampo['credito'];
				$totalCredito += $arrayCampo['debito'];

				$valueInsertAsientos .= "('$idNota',
										'$consecutivo',
										'NDFV',
										'Nota Devolucion Factura de Venta',
										'$fecha',
										'$idFactura',
										'FV',
										'$numero_documento_cruce',
										'".$arrayCampo['credito']."',
										'".$arrayCampo['debito']."',
										'$cuenta',
										'$idCliente',
										'".$arrayCampo['idCcos']."',
										'$idSucursal',
										'$idEmpresa',
										'Notas Descuadradas'),";

				$tablaDebug  .='<div style="float:left; width:80px;">-'.$arrayCampo['credito'].'</div><div style="float:left; width:80px;">-'.$arrayCampo['debito'].'</div><div style="float:left; width:80px;">-'.$cuenta.'</div><br>';
			}

			$tablaDebug .= '<div style="float:left; width:80px; border-top:1px solid">-'.$totalDebito.'</div><div style="float:left; width:80px; border-top:1px solid">'.$totalCredito.'</div><br>';
			// echo $tablaDebug; exit;

			$totalDebito  = ROUND($totalDebito,$decimalesMoneda);
			$totalCredito = ROUND($totalCredito,$decimalesMoneda);
			if($totalDebito != $totalCredito){ echo '<script>alert("Aviso.\nLa contabilizacion ingresada no cumple doble partida, Confirme su configuracion en el modulo panel de control.")</script>'; exit; }

			// ELIMINAR LOS ASIENTOS
			$sql="UPDATE asientos_niif SET activo=0,observacion='Notas Descuadradas Drop' WHERE id_empresa=$idEmpresa AND id_sucursal=$idSucursal AND id_documento=$idNota AND tipo_documento='NDFV' ";
			$query=$this->mysql->query($sql,$this->mysql->link);

			$valueInsertAsientos = substr($valueInsertAsientos, 0, -1);
			$sqlInsertCuentasColgaap = "INSERT INTO asientos_niif (
											id_documento,
											consecutivo_documento,
											tipo_documento,
											tipo_documento_extendido,
											fecha,
											id_documento_cruce,
											tipo_documento_cruce,
											numero_documento_cruce,
											debe,
											haber,
											codigo_cuenta,
											id_tercero,
											id_centro_costos,
											id_sucursal,
											id_empresa,
											observacion)
										VALUES $valueInsertAsientos";
			$queryInsertCuentasColgaap =$this->mysql->query($sqlInsertCuentasColgaap,$this->mysql->link);

			if(!$queryInsertCuentasColgaap){ '<script>alert("Aviso No 3.\nSin conexion con la bade de datos, intente de nuevo si el problema persiste consulte al administrador del sistema")</script>'; exit; }
	    }

		/**
		* @method depurar depurar los documentos
		*/
		public function muestraDocumentosDepurar()
		{
			$this->getDocumentos();
			$id_doc_render = 0;
			// print_r($this->arrayDocumentosDescuadrados);

			foreach ($this->arrayDocumentosDescuadrados as $id_documento => $arrayResul) {
				foreach ($arrayResul as $tipo_documento => $arrayDoc2) {
						foreach ($this->arrayDocumentosColgaap[$id_documento][$tipo_documento] as $cuenta => $arrayColgaap) {
							if ($id_doc_render==$id_documento) { continue;}
							else{ $id_doc_render=$id_documento; }
							// if ($this->arrayDocumentosNiif[$id_documento][$cuenta]['debe']<>$arrayColgaap['debe'] && $this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['haber']<>$arrayColgaap['haber']) {
								// if ($this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['nit_tercero']=='') { continue; }
								$bodyTable .= "<tr>
							 					<td style='$style' >$id_documento $arrayColgaap[consecutivo_documento]</td>
							 					<td style='$style' >$arrayColgaap[tipo_documento] $arrayColgaap[sucursal]</td>
							 					<td style='$style' >$arrayColgaap[tipo_documento_extendido]</td>
							 					<td style='$style' >$arrayColgaap[debe]</td>
							 					<td style='$style' >$arrayColgaap[haber]</td>
							 					<td style='$style' >$arrayColgaap[nit_tercero]</td>
							 					<td style='$style' >$arrayColgaap[tercero]</td>
							 					<td style='$style' >$cuenta</td>
							 					<td style='$style' >$arrayColgaap[cuenta]</td>
							 					<td>&nbsp;</td>
							 					<!--<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$cuenta][debe]."</td>-->
							 					<td style='$style' >$arrayColgaap[consecutivo_documento]</td>
							 					<td style='$style' >$arrayColgaap[tipo_documento]</td>
							 					<td style='$style' >$arrayColgaap[tipo_documento_extendido]</td>
							 					<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['debe']."</td>
							 					<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['haber']."</td>
							 					<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['nit_tercero']."</td>
							 					<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['tercero']."</td>
							 					<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['codigo_cuenta']."</td>
							 					<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['cuenta']."</td>
							 				</tr>";
							// }
						// }
					}
				}
			}


			// foreach ($this->arrayDocumentosColgaap as $id_documento => $arrayDoc) {
			// 	foreach ($arrayDoc as $tipo_documento => $arrayDoc2) {
			// 		foreach ($arrayDoc2 as $cuenta => $arrayColgaap) {

			// 			if ($this->arrayDocumentosNiif[$id_documento][$cuenta]['debe']<>$arrayColgaap['debe'] && $this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['haber']<>$arrayColgaap['haber']) {
			// 				if ($this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['nit_tercero']=='') { continue; }
			// 				$bodyTable .= "<tr>
			// 			 					<td style='$style' >$arrayColgaap[consecutivo_documento]</td>
			// 			 					<td style='$style' >$arrayColgaap[tipo_documento]</td>
			// 			 					<td style='$style' >$arrayColgaap[tipo_documento_extendido]</td>
			// 			 					<td style='$style' >$arrayColgaap[debe]</td>
			// 			 					<td style='$style' >$arrayColgaap[haber]</td>
			// 			 					<td style='$style' >$arrayColgaap[nit_tercero]</td>
			// 			 					<td style='$style' >$arrayColgaap[tercero]</td>
			// 			 					<td style='$style' >$cuenta</td>
			// 			 					<td style='$style' >$arrayColgaap[cuenta]</td>
			// 			 					<td>&nbsp;</td>
			// 			 					<!--<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$cuenta][debe]."</td>-->
			// 			 					<td style='$style' >$arrayColgaap[consecutivo_documento]</td>
			// 			 					<td style='$style' >$arrayColgaap[tipo_documento]</td>
			// 			 					<td style='$style' >$arrayColgaap[tipo_documento_extendido]</td>
			// 			 					<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['debe']."</td>
			// 			 					<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['haber']."</td>
			// 			 					<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['nit_tercero']."</td>
			// 			 					<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['tercero']."</td>
			// 			 					<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['codigo_cuenta']."</td>
			// 			 					<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['cuenta']."</td>
			// 			 				</tr>";
			// 			}
			// 		}
			// 	}
			// }

			// foreach ($this->arrayDocumentosColgaapNDFVColgaap as $id_documento => $arrayDoc) {
			// 	foreach ($arrayDoc as $tipo_documento => $arrayDoc2) {
			// 		foreach ($arrayDoc2 as $cuenta => $arrayResult) {

			// 			if ($this->arrayDocumentosColgaapNDFVNiif[$id_documento][$tipo_documento][$cuenta]['debe']<>$arrayResult['debe'] && $this->arrayDocumentosColgaapNDFVNiif[$id_documento][$tipo_documento][$cuenta]['haber']<>$arrayResult['haber']) {
			// 				// if ($this->arrayDocumentosColgaapNDFVNiif[$id_documento][$tipo_documento][$cuenta]['nit_tercero']=='') { continue; }
			// 				$bodyTable .= "<tr>
			// 			 					<td style='$style' >$arrayResult[consecutivo_documento]</td>
			// 			 					<td style='$style' >$arrayResult[tipo_documento]</td>
			// 			 					<td style='$style' >$arrayResult[tipo_documento_extendido]</td>
			// 			 					<td style='$style' >$arrayResult[debe]</td>
			// 			 					<td style='$style' >$arrayResult[haber]</td>
			// 			 					<td style='$style' >$arrayResult[nit_tercero]</td>
			// 			 					<td style='$style' >$arrayResult[tercero]</td>
			// 			 					<td style='$style' >$cuenta</td>
			// 			 					<td style='$style' >$arrayResult[cuenta]</td>
			// 			 					<td>&nbsp;</td>
			// 			 					<td style='$style' >".$this->arrayDocumentosColgaapNDFVNiif[$id_documento][$tipo_documento][$cuenta]['consecutivo_documento']."</td>
			// 			 					<td style='$style' >".$this->arrayDocumentosColgaapNDFVNiif[$id_documento][$tipo_documento][$cuenta]['tipo_documento']."</td>
			// 			 					<td style='$style' >".$this->arrayDocumentosColgaapNDFVNiif[$id_documento][$tipo_documento][$cuenta]['tipo_documento_extendido']."</td>
			// 			 					<td style='$style' >".$this->arrayDocumentosColgaapNDFVNiif[$id_documento][$tipo_documento][$cuenta]['debe']."</td>
			// 			 					<td style='$style' >".$this->arrayDocumentosColgaapNDFVNiif[$id_documento][$tipo_documento][$cuenta]['haber']."</td>
			// 			 					<td style='$style' >".$this->arrayDocumentosColgaapNDFVNiif[$id_documento][$tipo_documento][$cuenta]['nit_tercero']."</td>
			// 			 					<td style='$style' >".$this->arrayDocumentosColgaapNDFVNiif[$id_documento][$tipo_documento][$cuenta]['tercero']."</td>
			// 			 					<td style='$style' >".$this->arrayDocumentosColgaapNDFVNiif[$id_documento][$tipo_documento][$cuenta]['codigo_cuenta']."</td>
			// 			 					<td style='$style' >".$this->arrayDocumentosColgaapNDFVNiif[$id_documento][$tipo_documento][$cuenta]['cuenta']."</td>
			// 			 				</tr>";
			// 			}
			// 		}
			// 	}
			// }

			// print_r($this->arrayDocumentosColgaapNDFVColgaap);
			$bodyTable= "<table>
							<thead>
								<tr>
									<td >consecutivo</td>
				 					<td >tipo</td>
				 					<td >documento</td>
									<td >debe COLGAAP</td>
									<td >haber COLGAAP</td>
									<td >nit_tercero COLGAAP</td>
									<td >tercero COLGAAP</td>
									<td >codigo_cuenta COLGAAP</td>
									<td >cuenta COLGAAP</td>
									<td>&nbsp;</td>
									<td >consecutivo</td>
				 					<td >tipo</td>
				 					<td >documento</td>
									<td >debe NIIF</td>
									<td >haber NIIF</td>
									<td >nit_tercero NIIF</td>
									<td >tercero NIIF</td>
									<td >codigo_cuenta NIIF</td>
									<td >cuenta NIIF</td>
								</tr>
							</thead>
							<tbody>
								$bodyTable
							</tbody>
						</table>";
			echo $bodyTable;
		}

		/**
		* @method depurar depurar los documentos
		*/
		public function muestraDocumentosDepurarXls()
		{
			$this->getDocumentos();
			foreach ($this->arrayDocumentosColgaap as $id_documento => $arrayDoc) {
				foreach ($arrayDoc as $tipo_documento => $arrayDoc2) {
					foreach ($arrayDoc2 as $cuenta => $arrayColgaap) {

						if ($this->arrayDocumentosNiif[$id_documento][$cuenta]['debe']<>$arrayColgaap['debe'] && $this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['haber']<>$arrayColgaap['haber']) {
							if ($this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['nit_tercero']=='') { continue; }
							$bodyTable .= "<tr>
						 					<td style='$style' >$arrayColgaap[consecutivo_documento]</td>
						 					<td style='$style' >$arrayColgaap[tipo_documento]</td>
						 					<td style='$style' >$arrayColgaap[tipo_documento_extendido]</td>
						 					<td style='$style' >$arrayColgaap[debe]</td>
						 					<td style='$style' >$arrayColgaap[haber]</td>
						 					<td style='$style' >$arrayColgaap[nit_tercero]</td>
						 					<td style='$style' >$arrayColgaap[tercero]</td>
						 					<td style='$style' >$cuenta</td>
						 					<td style='$style' >$arrayColgaap[cuenta]</td>
						 					<td>&nbsp;</td>
						 					<!--<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$cuenta][debe]."</td>-->
						 					<td style='$style' >$arrayColgaap[consecutivo_documento]</td>
						 					<td style='$style' >$arrayColgaap[tipo_documento]</td>
						 					<td style='$style' >$arrayColgaap[tipo_documento_extendido]</td>
						 					<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['debe']."</td>
						 					<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['haber']."</td>
						 					<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['nit_tercero']."</td>
						 					<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['tercero']."</td>
						 					<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['codigo_cuenta']."</td>
						 					<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['cuenta']."</td>
						 				</tr>";
						}
					}
				}
			}

			$bodyTable= "<table>
							<thead>
								<tr>
									<td >consecutivo</td>
				 					<td >tipo</td>
				 					<td >documento</td>
									<td >debe COLGAAP</td>
									<td >haber COLGAAP</td>
									<td >nit_tercero COLGAAP</td>
									<td >tercero COLGAAP</td>
									<td >codigo_cuenta COLGAAP</td>
									<td >cuenta COLGAAP</td>
									<td>&nbsp;</td>
									<td >consecutivo</td>
				 					<td >tipo</td>
				 					<td >documento</td>
									<td >debe NIIF</td>
									<td >haber NIIF</td>
									<td >nit_tercero NIIF</td>
									<td >tercero NIIF</td>
									<td >codigo_cuenta NIIF</td>
									<td >cuenta NIIF</td>
								</tr>
							</thead>
							<tbody>
								$bodyTable
							</tbody>
						</table>";

			header('Content-type: application/vnd.ms-excel');
   			header("Content-Disposition: attachment; filename=documentos depurados_$this->id_empresa.xls");
   			header("Pragma: no-cache");
   			header("Expires: 0");

			echo $bodyTable;
		}

		/**
		* @method depurar depurar los documentos
		*/
		public function depurar()
		{
			$this->getDocumentos();
			$this->muestraDocumentosDepurarXls();
			// $this->getAsientosDocumentos();
			// $this->setAsientos();
			// print_r($this->arrayDevolucionesVenta);

			if(!is_null($this->arrayDevolucionesVenta)){
				foreach ($this->arrayDevolucionesVenta as $idNota => $arrayResult) {
					$this->contabilizaDevolucionVenta($arrayResult['arrayCuentaPago'],$arrayResult['idCcos'],$idNota,$arrayResult['consecutivo'],$arrayResult['idBodega'],$arrayResult['idSucursal'],$arrayResult['idEmpresa'],$arrayResult['idFactura'],$arrayResult['idCliente'],$arrayResult['exento_iva'],$arrayResult['fecha'],$arrayResult['numero_documento_cruce']);
				}
			}
			if(!is_null($this->arrayDevolucionesCompra)){
				foreach ($this->arrayDevolucionesVenta as $idNota => $arrayResult) {
					$this->contabilizaDevolucionCompra($arrayResult['estadoCuentaPago'],$idNota,$arrayResult['consecutivo'],$arrayResult['idBodega'],$arrayResult['idSucursal'],$arrayResult['idEmpresa'],$arrayResult['idFactura'],$arrayResult['idProveedor'],$arrayResult['fecha'],$arrayResult['numero_documento_cruce']);
				}
			}

		}

	}




?>