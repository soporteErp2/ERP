<?php

	/**
	 * MainClass Clase con funciones prnicipales para extender a las demas clases
	 */
	class MainClass
	{
		protected $mysql;

		function __construct($mysql){
			$this->mysql = $mysql;
		}

		/**
    	 * updateInventario Actualizar las unidades de inventario
    	 * @param  Array $params Array con todos los parametros necesarios para la actualizacion del inventario
    	 *                       accion = Accion a realizar al inventario (aumentar:incremetar cantidades en inventario, disminuir: disminuir cantidades en inventario)
    	 *                       campos = String con los campos de la tabla
    	 *                       			cantidad con el Alias cantidad
    	 *                       			id_item con el Alias id_item
    	 *                       tablaInventario =  Nombre de la tabla de inventario a consultar
    	 *                       campoIdDocumento = Nombre del campo Id de la tabla principal (Ejemplo : id_factura, id_pos, etc)
    	 *                       tablaInventarioReceta = nombre de la tabla del inventario de la receta
    	 *                       camposReceta = String con los campos de la tabla
    	 *                       				cantidad con el Alias cantidad
    	 *                       				id_item con el Alias id_item
    	 *                       id_empresa = Id de la empresa
    	 *                       idDocumento = valor del id del documento principal para cargar todos los items de ese documento
    	 *                       where = String con el where adicional en caso de que sea necesario
    	 *                       id_bodega = id de la bodega de donde se descontara el inventario
    	 *                       ingredientes = Array con los ingredientes del item (Solo se envia si fue modificada)
    	 *                       				id_item (key) id del item principal
    	 *                       				id_item = id del item que es ingrediente del principal con el alias de id_item
    	 *                       				cantidad = cantidad del ingrediente con el alias de cantidad
    	 *                       whereReceta = String con el where adicional en caso de que sea necesario
    	 *                       id_seccion = id del ambiente o restaurante de donde se esta realizando la venta
    	 * @return Array  Si se genera un error se retorna array con el detalle del error
    	 */
    	public function updateInventario($params){
    		$accion = ($params['accion'=='aumentar'])? " + " : " - " ;
    		$sql="SELECT
    					id,
    					$params[campos]
    				FROM
    					$params[tablaInventario]
    				WHERE
    					activo=1
					AND $params[campoIdDocumento] = $params[idDocumento]
					AND id_empresa=$params[id_empresa]
					$params[where]
    					";
    		$query=$this->mysql->query($sql);
    		while ($row=$this->mysql->fetch_array($query)) {
    			$arrayItems[$row['id_item']][] = $row['cantidad'];
    			$whereIdItems .= ($whereIdItems=="")? "id=$row[id_item]" : " OR id=$row[id_item] " ;
    		}

    		$sql="SELECT
    					id,
    					$params[camposReceta]
    				FROM
    					$params[tablaInventarioReceta]
    				WHERE
    					activo=1
					AND $params[campoIdDocumento] = $params[idDocumento]
					AND id_empresa=$params[id_empresa]
					$params[whereReceta]
    					";
    		$query=$this->mysql->query($sql);
    		while ($row=$this->mysql->fetch_array($query)) {
    			$arrayItems[$row['id_item']][] = $row['cantidad'];
    			$whereIdItems .= ($whereIdItems=="")? " id=$row[id_item] " : " OR id=$row[id_item] " ;
    		}

    		// CONSULTAR LA BODEGA DEL AMBIENTE
    		$sql="SELECT id_bodega FROM ventas_pos_secciones
    				WHERE activo=1
    				AND id_empresa=$params[id_empresa]
    				AND id=$params[id_seccion]";
    		$query=$mysql->query($sql,$mysql->link);
    		$id_bodega_ambiente = $this->mysql->result($query,0,'id');

    		// CONSULTAR LA INFORMACION REQUERIDA PARA EL MOVIMIENTO DE INVENTARIO
    		$sql="SELECT id,id_bodega_produccion,inventariable
    				FROM items
    				WHERE activo=1 AND id_empresa=$params[id_empresa] AND ($whereIdItems)";
    		$query=$this->mysql->query($sql);
    		while ($row=$this->mysql->fetch_array($query)) {
    			$arrayInfoItems[$row['id']]  = array(
													'id_bodega'     => $row['id_bodega_produccion'],
													'inventariable' => $row['inventariable'],
    											);
    		}

    		// id_bodega_produccion

    		if (count($arrayItems)>0) {
    			foreach ($arrayItems as $id_item => $arrayItemsResul) {
    				foreach ($arrayItemsResul as $key => $cantidad) {
    					$sql="UPDATE inventario_totales SET cantidad=cantidad $accion $cantidad
								WHERE activo=1 AND id_item=$id_item AND id_ubicacion=$params[id_bodega]";
						$query=$this->mysql->query($sql);
						if (!$query) {
							$arrayError[]="Se produjo un error al insertar el items id $id_item";
						}
    				}
    			}
    			if(count($arrayError)>0){
	    			// $this->rollback($id_factura,1);
	        		return array('status'=>false,'detalle'=>$arrayError);
				}
				else{ return array('status' => true); }
    		}
    		else{ return array('status' => true); }
    	}

    	/**
		 * setAsientos Contabilizar la factura en norma local
		 * @param Int $id_factura        Id de la factura a causar
		 * @param Array $arrayCuentaPago Array con la informacion de la cuenta de pago
		 * @return Array Resultado del proceso {status: true o false},{detalle: en caso de ser error se detallara en este campo}
		 */
		public function setAsientos($id_factura,$consecutivo,$arrayCuentaPago){
			global $saldoGlobalfactura, $saldoGlobalFacturaSinAbono;
			$decimalesMoneda  = ($this->decimales_moneda >= 0)? $this->decimales_moneda : 0;
			$cuentaPago       = $arrayCuentaPago['cuentaColgaap'];
			$estadoCuentaPago = $arrayCuentaPago['estado'];

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

			$sql="SELECT fecha_inicio FROM ventas_facturas WHERE activo=1 AND id=$id_factura";
			$query=$this->mysql->query($sql,$this->mysql->link);
			$fechaFactura = $this->mysql->result($query,0,'fecha_inicio');

			$sqlDoc = "SELECT VF.id,
							VF.id_consecutivo_referencia AS id_referencia,
							VF.nombre_consecutivo_referencia AS nombre_referencia,
							VF.id_inventario AS id_item,
							VF.codigo,
							VF.cantidad,
							VF.costo_unitario AS precio,
							VF.costo_inventario AS costo,
							VF.descuento,
							VF.tipo_descuento,
							VF.id_impuesto,
							VF.valor_impuesto,
							VF.inventariable,
							I.cuenta_venta AS cuenta_iva
						FROM ventas_facturas_inventario AS VF LEFT JOIN impuestos AS I ON(
								I.activo=1
								AND I.id=VF.id_impuesto
							)
						WHERE VF.id_factura_venta='$id_factura' AND VF.activo=1";
			$queryDoc = $this->mysql->query($sqlDoc,$this->mysql->link);

			while($rowDoc = $this->mysql->fetch_array($queryDoc)){
				$typeDocumento = 'FV';
				$impuesto      = 0;

				//CALCULO DEL PRECIO
				$precio = $rowDoc['precio'] * $rowDoc['cantidad'];
				$costo  = ABS($rowDoc['costo'] * $rowDoc['cantidad']);
				if($rowDoc['descuento'] > 0){
					$precio = ($rowDoc['tipo_descuento'] == 'porcentaje')? $precio-ROUND(($rowDoc['descuento']*$precio)/100, $decimalesMoneda) : $precio-$rowDoc['descuento'];
				}

				if($rowDoc['id_impuesto'] > 0 && $rowDoc['valor_impuesto'] > 0 && $exento_iva!='Si'){  $impuesto = ROUND($precio*$rowDoc['valor_impuesto']/100, $decimalesMoneda); }

				$whereIdItemsCuentas .= ($whereIdItemsCuentas != '')? ' OR ': ' ';
				$whereIdItemsCuentas .='id_items = '.$rowDoc['id_item'];

				$arrayInventarioFactura[$rowDoc['id']]  = array('id_factura_inventario' =>$rowDoc['id'],
																'precio'                =>$precio,
																'codigo'                =>$rowDoc['codigo'],
																'id_referencia'         =>$rowDoc['id_referencia'],
																'nombre_referencia'     =>$rowDoc['nombre_referencia'],
																'impuesto'              =>$impuesto,
																'costo'                 =>$costo,
																'id_items'              =>$rowDoc['id_item'],
																'inventariable'         =>$rowDoc['inventariable'],
																'cuenta_iva'            =>$rowDoc['cuenta_iva']);
			}

			$sqlItemsCuentas = "SELECT id, id_items,descripcion, puc, tipo
								FROM items_cuentas
								WHERE activo=1
									AND id_empresa='$this->id_empresa'
									AND estado='venta'
									AND ($whereIdItemsCuentas)
								GROUP BY id_items,descripcion
								ORDER BY id_items ASC";
			$queryItemsCuentas = $this->mysql->query($sqlItemsCuentas,$this->mysql->link);

			while ($rowCuentasItems = $this->mysql->fetch_array($queryItemsCuentas)) {

				if($rowCuentasItems['descripcion'] == 'contraPartida_precio'){ $rowCuentasItems['puc'] = $cuentaPago; $rowCuentasItems['tipo'] = 'debito'; }
				if($rowCuentasItems['descripcion'] == 'impuesto'){ $rowCuentasItems['tipo'] = 'credito'; }

				$arrayCuentasItems[$rowCuentasItems['id_items']][$rowCuentasItems['descripcion']]= array('estado' => $rowCuentasItems['tipo'], 'cuenta'=> $rowCuentasItems['puc']);

				$valueInsertContabilizacion .= "('".$rowCuentasItems['id_items']."',
												'".$rowCuentasItems['puc']."',
												'".$rowCuentasItems['tipo']."',
												'".$rowCuentasItems['descripcion']."',
												'$id_factura',
												'FV',
												'$this->id_empresa',
												'$this->id_sucursal',
												'$this->id_bodega'),";
			}

			$arrayGlobalEstado['debito']  = 0;
			$arrayGlobalEstado['credito'] = 0;

			$arrayItemEstado['debito']    = 0;
			$arrayItemEstado['credito']   = 0;

			$acumSubtotal = 0;
			$acumImpuesto = 0;
			// print_r($arrayInventarioFactura);
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
					if($arrayAsiento[$cuentaPrecio][$estado] > 0){ $arrayAsiento[$cuentaPrecio][$estado] += ROUND($valArrayInventario['precio'],$decimalesMoneda); }
					else{ $arrayAsiento[$cuentaPrecio][$estado] = ROUND($valArrayInventario['precio'],$decimalesMoneda); }
					$arrayAsiento[$cuentaPrecio]['idCcos'] = $idCcos;

					$arrayGlobalEstado[$estado] += ROUND($valArrayInventario['precio'],$decimalesMoneda);
					$arrayItemEstado[$estado]   += ROUND($valArrayInventario['precio'],$decimalesMoneda);
					$acumSubtotal               += ROUND($valArrayInventario['precio'],$decimalesMoneda);

					// $arrayGlobalEstado[$estado] += $valArrayInventario['precio'];
					// $arrayItemEstado[$estado]   += $valArrayInventario['precio'];
					// $acumSubtotal               += $valArrayInventario['precio'];

					//===================================== CALC IMPUESTO ========================================//
					if($cuentaImpuesto > 0 && $valArrayInventario['impuesto'] > 0){
						$estado = $arrayCuentasItems[$idItemArray]['impuesto']['estado'];

						//ARRAY ASIENTO CONTABLE
						if($arrayAsiento[$cuentaImpuesto][$estado] > 0){ $arrayAsiento[$cuentaImpuesto][$estado] += $valArrayInventario['impuesto']; }
						else{ $arrayAsiento[$cuentaImpuesto][$estado] = $valArrayInventario['impuesto']; }
						$arrayAsiento[$cuentaImpuesto]['idCcos'] = 0;

						$arrayGlobalEstado[$estado] += ROUND($valArrayInventario['impuesto'],$decimalesMoneda);
						$arrayItemEstado[$estado]   += ROUND($valArrayInventario['impuesto'],$decimalesMoneda);
						$acumImpuesto               += ROUND($valArrayInventario['impuesto'],$decimalesMoneda);

						// $arrayGlobalEstado[$estado] += $valArrayInventario['impuesto'];
						// $arrayItemEstado[$estado]   += $valArrayInventario['impuesto'];
						// $acumImpuesto               += $valArrayInventario['impuesto'];

					}

					//============================== CALC CONTRA PARTIDA PRECIO =================================//
					if($contraPrecio > 0){
						$arrayAsiento[$contraPrecio]['type'] = 'cuentaPago';
						$estado = $arrayCuentasItems[$idItemArray]['contraPartida_precio']['estado'];



						$contraSaldo = ($arrayItemEstado['debito'] > $arrayItemEstado['credito'])? $arrayItemEstado['debito'] - $arrayItemEstado['credito']
										:  $arrayItemEstado['credito'] - $arrayItemEstado['debito'];

						//ARRAY ASIENTO CONTABLE
						if($arrayAsiento[$contraPrecio][$estado] > 0){ $arrayAsiento[$contraPrecio][$estado] += $contraSaldo; }
						else{ $arrayAsiento[$contraPrecio][$estado] = $contraSaldo; }
						$arrayAsiento[$contraPrecio]['idCcos'] = 0;

						$arrayGlobalEstado[$estado] += $contraSaldo;
						$arrayItemEstado[$estado]   += $contraSaldo;

						$acumCuentaClientes   = $contraPrecio;
						$estadoCuentaClientes = $estado;
					}
				}
				else if($valArrayInventario['inventariable'] == 'false'){
					$this->rollBack($id_factura,1);
					return array('status' => false, 'detalle'=> "El item Codigo $valArrayInventario[codigo] No se ha configurado en la contabilizacion" );
					// echo '<script>
					// 		alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' No se ha configurado en la contabilizacion");
					// 		document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					// 	</script>';
					// exit;
				}
				//======================================= CALC COSTO ===========================================//
				if( $cuentaCosto > 0 && $contraCosto > 0 && $valArrayInventario['nombre_referencia'] != 'Remision' ){

					$estado = $arrayCuentasItems[$idItemArray]['costo']['estado'];

					//ARRAY ASIENTO CONTABLE
					if($arrayAsiento[$cuentaCosto][$estado] > 0){ $arrayAsiento[$cuentaCosto][$estado] += $valArrayInventario['costo']; }
					else{ $arrayAsiento[$cuentaCosto][$estado] = $valArrayInventario['costo']; }
					$arrayAsiento[$cuentaCosto]['idCcos'] = 0;

					$arrayGlobalEstado[$estado] += $valArrayInventario['costo'];
					$arrayItemEstado[$estado]   += $valArrayInventario['costo'];

					//ARRAY ASIENTO CONTABLE
					$estado = $arrayCuentasItems[$idItemArray]['contraPartida_costo']['estado'];
					if($arrayAsiento[$contraCosto][$estado] > 0){ $arrayAsiento[$contraCosto][$estado] += $valArrayInventario['costo']; }
					else{ $arrayAsiento[$contraCosto][$estado] = $valArrayInventario['costo']; }
					$arrayAsiento[$contraCosto]['idCcos'] = $idCcos;

					$arrayGlobalEstado[$estado] += $valArrayInventario['costo'];
					$arrayItemEstado[$estado]   += $valArrayInventario['costo'];

				}
				else if($valArrayInventario['inventariable'] == 'true' && $valArrayInventario['nombre_referencia'] != 'Remision'){
					$this->rollBack($id_factura,1);
					return  array('status' => false, 'detalle'=> "El item Codigo $valArrayInventario[codigo] No se ha configurado el manejo del costo en la contabilizacion" );
					// echo '<script>
					// 		alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' No se ha configurado el manejo del costo en la contabilizacion");
					// 		document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					// 	</script>';
					// exit;
				}

			}


			$arrayGlobalEstado['debito']  = ROUND($arrayGlobalEstado['debito'],$decimalesMoneda);
			$arrayGlobalEstado['credito'] = ROUND($arrayGlobalEstado['credito'],$decimalesMoneda);

			// print_r($arrayAsiento);
			if($arrayGlobalEstado['debito'] != $arrayGlobalEstado['credito']){
				$this->rollBack($id_factura,1);
				return  array('status' => false, 'detalle'=> "El saldo debito es diferente al credito, favor revise la configuracion de contabilizacion" );
				// echo '<script>
				// 		alert("Aviso.\nEl saldo debito es diferente al credito, favor revise la configuracion de contabilizacion!");
				// 		console.log("'.$arrayGlobalEstado['debito'].' - '.$arrayGlobalEstado['credito'].'");
				// 		document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				// 	</script>';
				// exit;
			}
			else if($arrayGlobalEstado['debito'] == 0 || $arrayGlobalEstado['credito'] == 0){
				$this->rollBack($id_factura,1);
				return  array('status' => false, 'detalle'=> "El saldo debito y credito en norma colgaap debe ser mayor a 0" );
				// echo '<script>
				// 		alert("Aviso,\nEl saldo debito y credito en norma colgaap debe ser mayor a 0!");
				// 		document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				// 	</script>';
				// exit;
			}

			//==================== QUERY RETENCIONES =================//
	    	/**********************************************************/
			$acumRetenciones  = 0;
			$contRetencion    = 0;
			$estadoRetencion  = $estadoCuentaClientes; 													//ESTADO CONTRARIO DE LAS RETENCIONES A LA CUENTA CLIENTES
			$sqlRetenciones   = "SELECT valor,codigo_cuenta,tipo_retencion,cuenta_autoretencion,base FROM ventas_facturas_retenciones WHERE id_factura_venta='$id_factura' AND activo=1";
			$queryRetenciones = $this->mysql->query($sqlRetenciones,$this->mysql->link);
			while($rowRetenciones = $this->mysql->fetch_array($queryRetenciones)){
				$valorBase           = $rowRetenciones['base'];
				$valorRetencion      = $rowRetenciones['valor'];
				$codigoRetencion     = $rowRetenciones['codigo_cuenta'];
				$tipoRetencion       = $rowRetenciones['tipo_retencion'];
				$cuentaAutoretencion = $rowRetenciones['cuenta_autoretencion'];

				if(is_nan($arrayAsiento[$codigoRetencion][$estadoRetencion])){ $arrayAsiento[$codigoRetencion][$estadoRetencion] = 0; }

				//CALCULO RETEIVA
				if($tipoRetencion == "ReteIva" ){
					if ($exento_iva=='Si' || $acumImpuesto<$valorBase) { continue; }

					$acumRetenciones += ROUND($acumImpuesto * $valorRetencion/100, $decimalesMoneda);
					$arrayAsiento[$codigoRetencion][$estadoRetencion] += ROUND($acumImpuesto * $valorRetencion/100, $decimalesMoneda);
				}
				else{ 																									//CALCULO RETE, RETECREE Y RETEICA
					if ($acumSubtotal<$valorBase) { continue; }

					$acumRetenciones += ROUND($acumSubtotal*$valorRetencion/100, $decimalesMoneda);
					$arrayAsiento[$codigoRetencion][$estadoRetencion] += ROUND($acumSubtotal*$valorRetencion/100, $decimalesMoneda);
				}

				if($tipoRetencion == "AutoRetencion"){ 																	//SI ES AUTORETENCION NO SE DESCUENTA A CLIENTES

					if(is_nan($cuentaAutoretencion)|| $cuentaAutoretencion < 10000){
						$this->rollBack($id_factura,1);
						return  array('status' => false, 'detalle'=> "No se ha configurado la cuenta Colgaap Autorretencion" );
						// echo '<script>
						// 		alert("Aviso.\nNo se ha configurado la cuenta Colgaap Autorretencion.");
						// 		document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						// 	</script>';
						// exit;
					}

					if(is_nan($arrayAsiento[$cuentaAutoretencion][$estadoCuentaClientes])){ $arrayAsiento[$cuentaAutoretencion][$estadoCuentaClientes] = 0; }
					$estadoReteCree  = ($estadoRetencion != 'debito')? 'debito' : 'credito';
					$acumRetenciones -= ROUND($acumSubtotal*$valorRetencion/100, $decimalesMoneda);
					$arrayAsiento[$cuentaAutoretencion][$estadoReteCree] += $arrayAsiento[$codigoRetencion][$estadoRetencion];
				}
			}

			$arrayAsiento[$acumCuentaClientes][$estadoCuentaClientes] -= $acumRetenciones;
			$saldoGlobalfactura = $arrayAsiento[$acumCuentaClientes][$estadoCuentaClientes];												//VARIABLE GLOBAL TOTAL FACTURA

			//=========================// SALDO ANTICIPO //=========================//
			//**********************************************************************//
			$saldoClientes = $arrayAsiento[$acumCuentaClientes][$estadoCuentaClientes];
			$saldoAnticipo = $arrayAnticipo['total'];

			if($saldoAnticipo > 0 && $saldoAnticipo > $saldoClientes){
				$this->rollBack($id_factura,1);
				return  array('status' => false, 'detalle'=> "Los anticipos no pueden ser mayores a la factura de venta" );
				// echo'<script>
				// 		alert("Aviso.\nLos anticipos no pueden ser mayores a la factura de venta!");
				// 		document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				// 	</script>';
				// exit;
			}
			else{ $arrayAsiento[$acumCuentaClientes][$estadoCuentaClientes] -= $saldoAnticipo; }

			foreach ($arrayAnticipo['anticipos'] as $idAnticipo => $datosAnticipo) {
				$arrayCampo['debito']  = 0;
				$arrayCampo['credito'] = 0;

				$arrayCampo[$estadoCuentaClientes] = $datosAnticipo['valor'];

				$totalDebito  += ($arrayCampo['debito'] > 0)? $arrayCampo['debito'] : 0;
				$totalCredito += ($arrayCampo['credito'] > 0)? $arrayCampo['credito'] : 0;
			}

			$totalDebito  = 0;
			$totalCredito = 0;
			$tablaDebug   = '<div style="float:left; width:80px;">Id</div>
							<div style="float:left; width:80px;">Documento</div>
							<div style="float:left; width:80px;">Debito</div>
							<div style="float:left; width:80px;">Credito</div>
							<div style="float:left; width:80px;">PUC</div>
							<div style="float:left; width:150px;">Id Centro Costos</div><br>';

			//=========================// CONTABILIZACION //=========================//
			//***********************************************************************//
			// print_r($arrayAsiento);
			$contAnticipos = 0;
			foreach ($arrayAsiento AS $cuenta => $arrayCampo) {

				if(is_nan($cuenta) || $cuenta==0){ continue; }
				$cuenta = $cuenta * 1;

				$arrayCampo['debito'] = round($arrayCampo['debito'],$decimalesMoneda);
				$arrayCampo['credito'] = round($arrayCampo['credito'],$decimalesMoneda);

				$totalDebito  += ($arrayCampo['debito'] > 0)? $arrayCampo['debito'] : 0;
				$totalCredito += ($arrayCampo['credito'] > 0)? $arrayCampo['credito'] : 0;

				if($arrayCampo['debito'] > 0 || $arrayCampo['credito'] > 0){

					// SALDO POR PAGAR EN FACTURACION
					if($estadoCuentaPago == 'Credito' && $arrayCampo['type']=='cuentaPago'){ $saldoGlobalFacturaSinAbono += ($arrayCampo['debito'] > $arrayCampo['credito'])? $arrayCampo['debito']: $arrayCampo['credito']; } //ACUMULADOR VARIABLE GLOBAL TOTAL FACTURA SIN ABONO

					// BODY INSERT
					$valueInsertAsientos .= "('$id_factura',
											'$consecutivo',
											'FV',
											'Factura de Venta',
											'$id_factura',
											'FV',
											'$consecutivo',
											'$fechaFactura',
											'".$arrayCampo['debito']."',
											'".$arrayCampo['credito']."',
											'$cuenta',
											'$this->id_cliente',
											'".$arrayCampo['idCcos']."',
											'$this->id_sucursal',
											'$this->id_empresa'),";

					$tablaDebug  .='<div style="overflow:hidden;">
										<div style="float:left; width:80px;">-'.$id_factura.'</div>
										<div style="float:left; width:80px;">FV</div>
										<div style="float:left; width:80px;">-'.$arrayCampo['debito'].'</div>
										<div style="float:left; width:80px;">-'.$arrayCampo['credito'].'</div>
										<div style="float:left; width:80px;">-'.$cuenta.'</div>
									</div><br>';
				}

				//=======================// ANTICIPOS //=======================//
				if($acumCuentaClientes == $cuenta){

					foreach ($arrayAnticipo['anticipos'] as $idAnticipo => $datosAnticipo) {
						$contAnticipos++;

						$arrayCampo['debito']  = 0;
						$arrayCampo['credito'] = 0;

						$arrayCampo[$estadoCuentaClientes] = $datosAnticipo['valor'];

						$totalDebito  += ($arrayCampo['debito'] > 0)? $arrayCampo['debito'] : 0;
						$totalCredito += ($arrayCampo['credito'] > 0)? $arrayCampo['credito'] : 0;

						if(is_nan($datosAnticipo['cuenta_colgaap']) || $datosAnticipo['cuenta_colgaap']==0){ continue; }
						$datosAnticipo['cuenta_colgaap'] = $datosAnticipo['cuenta_colgaap'] * 1;

						$valueInsertAsientos .= "('$id_factura',
												'$consecutivo',
												'FV',
												'Factura de Venta',
												'$datosAnticipo[id_anticipo]',
												'$datosAnticipo[tipo_documento]',
												'$datosAnticipo[consecutivo]',
												'$fechaFactura',
												'$arrayCampo[debito]',
												'$arrayCampo[credito]',
												'$datosAnticipo[cuenta_colgaap]',
												'$datosAnticipo[id_tercero]',
												'$idCcos',
												'$this->id_sucursal',
												'$this->id_empresa'),";

						$tablaDebug .= '<div style="overflow:hidden;">
											<div style="float:left; width:80px;">-'.$idAnticipo.'</div>
											<div style="float:left; width:80px;">RC</div>
											<div style="float:left; width:80px;">-'.$arrayCampo['debito'].'</div>
											<div style="float:left; width:80px;">-'.$arrayCampo['credito'].'</div>
											<div style="float:left; width:80px;">-'.$datosAnticipo['cuenta_colgaap'].'</div>
										<div><br>';
					}
				}
			}

			$tablaDebug .= '<div style="overflow:hidden;">
								<div style="float:left; width:80px; border-top:1px solid">-</div>
								<div style="float:left; width:80px; border-top:1px solid">-</div>
								<div style="float:left; width:80px; border-top:1px solid">-'.$totalDebito.'</div>
								<div style="float:left; width:80px; border-top:1px solid">-'.$totalCredito.'</div>
							</div><br>';

			// echo $tablaDebug;
			 // exit;
			$saldoGlobalFacturaSinAbono = ROUND($saldoGlobalFacturaSinAbono,$decimalesMoneda);
			$totalDebito  = ROUND($totalDebito,$decimalesMoneda);
			$totalCredito = ROUND($totalCredito,$decimalesMoneda);
			if($totalDebito != $totalCredito){
				$this->rollBack($id_factura,1);
				return array('status' => false, 'detalle'=> "La contabilizacion ingresada no cumple doble partida, Confirme su configuracion en el modulo panel de control" );
				// echo '<script>
				// 		alert("Aviso.\nLa contabilizacion ingresada no cumple doble partida, Confirme su configuracion en el modulo panel de control.");
				// 		console.log("totalDebito: '.$totalDebito.' totalCredito: '.$totalCredito.'");
				// 		document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				// 	</script>';
				// exit;
			}

			if($contAnticipos>0){
				$sqlAnticipo = "UPDATE recibo_caja_cuentas AS C, anticipos AS A
								SET C.saldo_pendiente=C.saldo_pendiente-A.valor
								WHERE C.id=A.id_cuenta_anticipo
									AND C.activo=1
									AND A.activo=1
									AND A.id_documento='$id_factura'
									AND A.tipo_documento='FV'";
				$queryAnticipo = $this->mysql->query($sqlAnticipo,$this->mysql->link);
			}

			$valueInsertAsientos        = substr($valueInsertAsientos, 0, -1);
			$valueInsertContabilizacion = substr($valueInsertContabilizacion, 0, -1);

			$sqlContabilizar   = "INSERT INTO contabilizacion_compra_venta (
									id_item,
									codigo_puc,
									caracter,
									descripcion,
									id_documento,
									tipo_documento,
									id_empresa,
									id_sucursal,
									id_bodega)
								VALUES $valueInsertContabilizacion";
			$queryContabilizar = $this->mysql->query($sqlContabilizar,$this->mysql->link);
			if(!$queryContabilizar){
				$arrayError[0]='Se produjo un error al insertar la configuracion de contabilidad el documento (Cod. Error 600)';
				$arrayError[1]="Error numero: ".$this->mysql->errno();
    			$arrayError[2]="Error detalle: ".$this->mysql->error();
    			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";

				$this->rollBack($id_factura,1);
        		return array('status'=>false,'detalle'=>$arrayError);

				// return = array('status' => false, 'detalle'=> "Sin conexion con la base de datos, intente de nuevo si el problema persiste consulte al administrador del sistema" );
				// echo'<script>
				// 		alert("Aviso.\nSin conexion con la base de datos, intente de nuevo si el problema persiste consulte al administrador del sistema");
				// 		document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				// 	</script>';
				// exit;
			}

			if($valueInsertAsientos != ''){
				$sql   = "INSERT INTO asientos_colgaap (
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
															id_centro_costos,
															id_sucursal,
															id_empresa
														)
								VALUES $valueInsertAsientos";
				$query = $this->mysql->query($sql,$this->mysql->link);
				if ($query) {
					return array('status'=>true);
				}
				else{
					$arrayError[0]='Se produjo un error al insertar la contabilidad el documento (Cod. Error 601)';
					$arrayError[1]="Error numero: ".$this->mysql->errno();
	    			$arrayError[2]="Error detalle: ".$this->mysql->error();
	    			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";
	    			$this->rollback($id_factura,1);
	        		return array('status'=>false,'detalle'=>$arrayError);
				}
				// contabilizacionSimultanea($id_factura,'FV',$this->id_sucursal,$this->id_empresa,$link);
			}
			else{
    			$this->rollback($id_factura,1);
				return array('status' => false, 'detalle'=> "No hay asientos contables a registrar" );

			}
		}
	}

?>