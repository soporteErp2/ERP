<?php
	error_reporting(E_ERROR | E_PARSE);

	/**
	 * Class PosFunctions clase backend para las funciones del pos
	 */
	class PosFunctions extends ClassGlobalFunctions
	{
		public $id_sucursal;
		public $id_empresa;
		public $mysql;
		public $id_documento;
		public $datosEmpresa;
		public $decimales_moneda;
		public $fecha_documento;
		public $prefijo;
		public $consecutivo;
		public $id_seccion;
		public $seccion;
		public $id_bodega_ambiente;
		public $idCcos;
		public $codigoCcos;
		public $nombreCcos;
		public $id_propina;
		public $valor_propina;
		public $causaIngreso = false;
		public $id_cliente;
		public $documento_cliente;
		public $cliente;
		public $id_reserva;
		public $id_usuario;
		public $estado;
		public $arrayCuentas;
		public $valor_descuento;
		public $debug;


		function __construct($id_sucursal,$id_empresa,$id_host,$mysql){
			$this->id_sucursal  = $id_sucursal;
			$this->id_empresa   = $id_empresa;
			$this->id_host      = $id_host;
			$this->mysql        = $mysql;
			parent::__construct($id_sucursal,$id_empresa,$mysql);
			$this->getEmpresaInfo();
		}

		/**
		 * getEmpresaInfo Consultar la informacion de la empresa necesaria para el proceso de facturacion y demas
		 */
		public function getEmpresaInfo(){
			$sql="SELECT
					id,
					nombre,
					documento,
					nit_completo,
					razon_social,
					tipo_regimen,
					direccion,
					decimales_moneda
				FROM empresas WHERE activo=1 AND id=$this->id_empresa";
			$query=$this->mysql->query($sql,$this->mysql->link);
			$this->datosEmpresa['nombre']       = $this->mysql->result($query,0,'nombre');
			$this->datosEmpresa['documento']    = $this->mysql->result($query,0,'documento');
			$this->datosEmpresa['nit']          = $this->mysql->result($query,0,'nit_completo');
			$this->datosEmpresa['razon_social'] = $this->mysql->result($query,0,'razon_social');
			$this->datosEmpresa['regimen']      = $this->mysql->result($query,0,'tipo_regimen');
			$this->datosEmpresa['direccion']    = $this->mysql->result($query,0,'direccion');
			$this->decimales_moneda             = $this->mysql->result($query,0,'decimales_moneda');
		} // END FUNCTION

		/**
		 * validateResolucion Validar un numero valido para la resolucion de facturas POS
		 * @return Array Array con los valores de la consulta y validacion
		 */
		public function validateResolucion(){
            $sql = "SELECT id_resolucion,numero_resolucion
            			FROM ventas_pos_configuracion_sucursales
            			WHERE activo=1 AND id_empresa=$this->id_empresa
            			ORDER BY predeterminada DESC LIMIT 1";
            $query=$this->mysql->query($sql);
            $id_resolucion =  $this->mysql->result($query,0,'id_resolucion');

			$sql   = "SELECT
							prefijo,
							consecutivo_inicial,
							consecutivo_final,
							consecutivo_pos,
							id_tercero,
							documento_tercero,
							tercero
						FROM ventas_pos_configuracion
						WHERE activo=1 AND id_empresa=$this->id_empresa AND id=$id_resolucion";
			$queryRes = $this->mysql->query($sql);
			$prefijo             = $this->mysql->result($queryRes,0,'prefijo');
			$consecutivo_inicial = $this->mysql->result($queryRes,0,'consecutivo_inicial');
			$consecutivo_final   = $this->mysql->result($queryRes,0,'consecutivo_final');
			$consecutivo_pos     = $this->mysql->result($queryRes,0,'consecutivo_pos');
			$id_tercero          = $this->mysql->result($queryRes,0,'id_tercero');
			$documento_tercero   = $this->mysql->result($queryRes,0,'documento_tercero');
			$tercero             = $this->mysql->result($queryRes,0,'tercero');

			// $arrayResult = array('status' => 'failed', 'message'=>$sqlRes, "debug" =>$sql);
			// echo json_encode($arrayResult);
			// exit;
            if ($id_resolucion=='') {
            	return array('status' => false, 'message'=> "No hay ninguna resolucion configurada para generar tiquet POS","debug"=>$sqlRes);
            }
    		else{
    			return array(
								'status'            => true,
								'id_resolucion'     => $id_resolucion,
								'prefijo'           => $prefijo,
								'consecutivo_pos'   => $consecutivo_pos,
								'id_tercero'        => $id_tercero,
								'documento_tercero' => $documento_tercero,
								'tercero'           => $tercero,
    						);
    		}
		} // END FUNCTION

		/**
		 * documentInfo Consultar la informacion del tiquet para el proceso de facturacion y demas
		 * @param Int $id_documento Id del tiquet POS
		 * @param Array $arrayResolucion Lista con la informacion de resolucion de facturacion y el tercero por defecto
		 *
		 */
		public function documentInfo($id_documento,$arrayResolucion){
			$sql="SELECT
						fecha_documento,
						prefijo,
						consecutivo,
						id_seccion,
						seccion,
						id_propina,
						valor_propina,
						porcentaje_descuento,
						valor_descuento,
						id_huesped,
						id_cliente,
						documento_cliente,
						cliente,
						id_reserva,
						id_usuario,
						subtotal_pos,
						total_pos,
						id_sucursal,
						id_factura,
						id_entrada_almacen,
						estado
					FROM ventas_pos
					WHERE activo=1 AND id_empresa=$this->id_empresa AND id=$id_documento ";
			$this->debug = "WHERE activo=1 AND id_empresa=$this->id_empresa AND id=$id_documento";
			$query=$this->mysql->query($sql);
			$this->id_documento      	  = $id_documento;
			$this->fecha_documento   	  = $this->mysql->result($query,0,'fecha_documento');
			$this->prefijo           	  = $this->mysql->result($query,0,'prefijo');
			$this->consecutivo       	  = $this->mysql->result($query,0,'consecutivo');
			$this->id_seccion        	  = $this->mysql->result($query,0,'id_seccion');
			$this->seccion           	  = $this->mysql->result($query,0,'seccion');
			$this->id_propina        	  = $this->mysql->result($query,0,'id_propina');
			$this->valor_propina     	  = $this->mysql->result($query,0,'valor_propina');
			$this->porcentaje_descuento   = $this->mysql->result($query,0,'porcentaje_descuento');
			$this->valor_descuento   	  = $this->mysql->result($query,0,'valor_descuento');
			$this->id_huesped        	  = $this->mysql->result($query,0,'id_huesped');
			$this->id_cliente        	  = $this->mysql->result($query,0,'id_cliente');
			$this->documento_cliente 	  = $this->mysql->result($query,0,'documento_cliente');
			$this->cliente           	  = $this->mysql->result($query,0,'cliente');
			$this->id_reserva        	  = $this->mysql->result($query,0,'id_reserva');
			$this->id_usuario        	  = $this->mysql->result($query,0,'id_usuario');
			$this->subtotal_pos      	  = $this->mysql->result($query,0,'subtotal_pos');
			$this->total_pos         	  = $this->mysql->result($query,0,'total_pos');
			$this->estado            	  = $this->mysql->result($query,0,'estado');
			$this->id_factura 		 	  = $this->mysql->result($query,0,'id_factura');
			$this->id_entrada_almacen 	  = $this->mysql->result($query,0,'id_entrada_almacen');
			$this->id_sucursal 		 	  = (!isset($this->id_sucursal))? 
											$this->mysql->result($query,0,'id_sucursal') 
											: $this->id_sucursal;
			// $this->id_sucursal       = $this->mysql->result($query,0,'id_sucursal');

			//get user info for electronic billing
			$sql="SELECT
						username,
						token,
						token_pos
					FROM empleados WHERE activo=1 AND id_empresa=$this->id_empresa AND id=".$this->id_usuario;
			$query=$this->mysql->query($sql);
			$this->username  = $this->mysql->result($query,0,'username');
			$this->token  = $this->mysql->result($query,0,'token');
			$this->token_pos  = $this->mysql->result($query,0,'token_pos');

			// $arrayResult = array('status' => 'failed',
			// 	'message'=>$arrayResolucion,
			// 	"debug" =>var_dump($this->consecutivo)
			// );
			// echo json_encode($arrayResult);
			// exit;

			if ($this->estado==1) {
				$arrayReturn = array('status' => false, "message"=>"El documento ya se encuentra generado");
				echo json_encode($arrayReturn);
				exit;
			}

			// CONSULTAR LAS SECCIONES DEL POS
			$sql="SELECT
						VPS.id_bodega,
						VPS.id_centro_costos,
						VPS.codigo_centro_costos,
						VPS.centro_costos,
						VPS.cuenta_ingreso_colgaap,
						VPS.cuenta_ingreso_niif,
						VPS.codigo_transaccion,
						VPS.cuenta_pago,
						VPS.metodo_pago,
						MP.codigo_metodo_pago_dian,
						CP.cuenta
					FROM ventas_pos_secciones AS VPS
					LEFT JOIN configuracion_metodos_pago AS MP ON MP.id = VPS.metodo_pago
					LEFT JOIN configuracion_cuentas_pago AS CP ON CP.id = VPS.cuenta_pago
					WHERE VPS.activo=1 
						AND VPS.id_empresa=$this->id_empresa 
						AND VPS.id=$this->id_seccion";
			$query=$this->mysql->query($sql);
			$this->id_bodega_ambiente      = $this->mysql->result($query,0,'id_bodega');
			$this->idCcos                  = $this->mysql->result($query,0,'id_centro_costos');
			$this->codigoCcos              = $this->mysql->result($query,0,'codigo_centro_costos');
			$this->nombreCcos              = $this->mysql->result($query,0,'centro_costos');
			$this->cuenta_ingreso_colgaap  = $this->mysql->result($query,0,'cuenta_ingreso_niif');
			$this->cuenta_ingreso_niif     = $this->mysql->result($query,0,'cuenta_ingreso_niif');
			$this->codigo_transaccion      = $this->mysql->result($query,0,'codigo_transaccion');
			$this->id_cuenta_pago     	   = $this->mysql->result($query,0,'cuenta_pago');
			$this->metodo_pago     		   = $this->mysql->result($query,0,'metodo_pago');
			$this->codigo_metodo_pago_dian = $this->mysql->result($query,0,'codigo_metodo_pago_dian');
			$this->cuenta_pago			   = $this->mysql->result($query,0,'cuenta');

			// CONSULTAR LAS PROPINAS DEL POS PARA TOMAR UNA POR DEFECTO
			$sql = "SELECT id
					FROM configuracion_propinas_pos
					WHERE activo=1 AND id_empresa=$this->id_empresa
					ORDER BY id DESC LIMIT 0,1";
			$query=$this->mysql->query($sql);
			$id_propina_default = $this->mysql->result($query,0,'id');

			// ACTUALIZAR EL ESTADO DEL DOCUMENTO A GENERADO
			$sql="UPDATE ventas_pos
						SET estado=1,detalle_estado=''
						WHERE activo=1 AND id_empresa=$this->id_empresa AND id=$this->id_documento";
			$query=$this->mysql->query($sql);

			$arrayCuentasPago = $this->getCuentasPago();
			// echo json_encode(["causaIngreso"=>$this->causaIngreso,"arrayCuentasPago"=>$arrayCuentasPago]);
			// SI NO TIENE CONSECUTIVO ENTONCES ASIGNAR UNO DE LA RESOLUCION
			if ($this->consecutivo==='' || $this->consecutivo==="0" || $this->consecutivo==0 || is_null($this->consecutivo) ){
				// $arrayResult = array('status' => 'failed',
				// 	'message'=>$arrayCuentasPago,
				// 	"debug" =>var_dump($this->consecutivo)
				// );
				// echo json_encode($arrayResult);
				// exit;
				// $arrayReturn = array('status' => false, "message"=>$arrayCuentasPago);
				// echo json_encode($arrayReturn);
				// exit;
				// SI ES UNA CORTESIA O UN CHEQUE CUENTA, NO SE ASIGNA UN CONSECUTIVO DEL POS PUES NO SERA VALIDA COMO FACTURA
				if ($this->causaIngreso == false) {
					
					$consecutivoCompleto = ($arrayResolucion["prefijo"]<>'')? "$arrayResolucion[prefijo] $arrayResolucion[consecutivo_pos]" : $arrayResolucion['consecutivo_pos'];
					$sql="UPDATE ventas_pos
							SET prefijo='$arrayResolucion[prefijo]',consecutivo='$arrayResolucion[consecutivo_pos]',id_configuracion_resolucion='$arrayResolucion[id_resolucion]'
							WHERE activo=1 AND id_empresa=$this->id_empresa AND id=$this->id_documento ";
					// $arrayReturn = array('status' => false, "debug"=>"prefijo: |$this->prefijo| consecutivo : |$this->consecutivo|");
					// echo json_encode($arrayReturn);
					// exit;
					$query=$this->mysql->query($sql);
					if ($query) {
						$sql="UPDATE ventas_pos_configuracion SET consecutivo_pos=consecutivo_pos+1 WHERE id='$arrayResolucion[id_resolucion]' ";
						$query=$this->mysql->query($sql);
						if ($query) {
							$this->consecutivo = $consecutivoCompleto;
						}
						else{
							$arrayReturn = array('status' => false, "message"=>"No se pudo actualizar el consecutivo de configuracion, intentelo de nuevo");
							echo json_encode($arrayReturn);
							exit;
						}
					}
					else{
						$arrayReturn = array('status' => false, "message"=>"No se pudo asignar el consecutivo a la venta");
						echo json_encode($arrayReturn);
						exit;
					}
				}
				else{
					// consecutivo
					foreach ($arrayCuentasPago['consecutivo'] as $tipo => $arrayResult) {
						$sql="UPDATE ventas_pos
							SET consecutivo='$arrayResult[consecutivo]',id_configuracion_resolucion=0
							WHERE activo=1 AND id_empresa=$this->id_empresa AND id=$this->id_documento ";
						$query=$this->mysql->query($sql);
						if ($query) {
							$sql="UPDATE configuracion_cuentas_pago_pos SET consecutivo=consecutivo+1 WHERE id='$arrayResult[id]' ";
							$query=$this->mysql->query($sql);
							if ($query) {
								$this->consecutivo = $arrayResult['consecutivo'];
							}
							else{
								$arrayReturn = array('status' => false, "message"=>"No se pudo actualizar el consecutivo de configuracion, intentelo de nuevo");
								echo json_encode($arrayReturn);
								exit;
							}
						}
						else{
							$arrayReturn = array('status' => false, "message"=>"No se pudo asignar el consecutivo a la venta");
							echo json_encode($arrayReturn);
							exit;
						}
						break;
					}
				}
			}

			// SI EL TIQUET NO TIENE UN TERCERO ASIGNADO, ASIGNAR EL DE LA RESOLUCION (TERCERO POR DEFECTO)
			if ($this->documento_cliente==='' || $this->documento_cliente===0 || $this->documento_cliente==="0" || is_null($this->documento_cliente)) {
				$this->id_cliente        = ($arrayCuentasPago['id_tercero']<>'')? $arrayCuentasPago['id_tercero'] : $arrayResolucion['id_tercero'];
				$this->documento_cliente = ($arrayCuentasPago['nit_tercero']<>'')? $arrayCuentasPago['nit_tercero'] : $arrayResolucion['documento_tercero'];
				$this->cliente           = ($arrayCuentasPago['tercero']<>'')? $arrayCuentasPago['tercero'] : $arrayResolucion['tercero'];

				$sql="UPDATE ventas_pos
						SET
							id_cliente        = '$this->id_cliente',
							documento_cliente = '$this->documento_cliente',
							cliente           = '$this->cliente'
						WHERE activo=1 AND id_empresa=$this->id_empresa AND id=$this->id_documento ";
				$query=$this->mysql->query($sql);
				if (!$query) {
					$arrayReturn = array('status' => false, "message"=>"No se pudo asignar el tercero a la venta");
					echo json_encode($arrayReturn);
					exit;
				}
			}

			// SI NO TIENE PROPINA ASIGNAR UNA POR DEFECTO
			if ($this->id_propina==0) {
				$this->id_propina = $id_propina_default;
				$sql="UPDATE ventas_pos
						SET id_propina=$this->id_propina
						WHERE activo=1 AND id_empresa=$this->id_empresa AND id=$this->id_documento";
				$query=$this->mysql->query($sql);
			}

		} // END FUNCTION

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
    	public function updateInventario(){

			//get document information
			$sql="SELECT id,nombre FROM empresas_sucursales WHERE id_empresa=$this->id_empresa ";
    		$query=$this->mysql->query($sql);
			while ($row =$this->mysql->fetch_assoc($query)) {
				$sucursales[$row["id"]] = $row["nombre"];
			}

			$sql="SELECT id,nombre FROM empresas_sucursales_bodegas WHERE id_empresa=$this->id_empresa ";
    		$query=$this->mysql->query($sql);
			while ($row =$this->mysql->fetch_assoc($query)) {
				$bodegas[$row["id"]] = $row["nombre"];
			}

    		// $accion = ($params['accion'=='aumentar'])? " + " : " - " ;
    		$accion =  " - " ;
    		$sql="SELECT
    					id,
    					id_item,
    					cantidad,
    					precio_venta
    				FROM
    					ventas_pos_inventario
    				WHERE
    					activo=1
					AND id_pos=$this->id_documento
					AND id_empresa=$this->id_empresa ";
    		$query=$this->mysql->query($sql);
    		while ($row=$this->mysql->fetch_array($query)) {
    			$arrayItems[$row['id_item']][] = $row['cantidad'];
    			$whereIdItems .= ($whereIdItems=="")? "id=$row[id_item]" : " OR id=$row[id_item] " ;
    		}

    		$sql="SELECT
    					id,
						id_item,
						cantidad
    				FROM
    					ventas_pos_inventario_receta
    				WHERE
    					activo=1
					AND id_empresa=$this->id_empresa
					AND id_pos=$this->id_documento
    					";
    		$query=$this->mysql->query($sql);
    		while ($row=$this->mysql->fetch_array($query)) {
    			$arrayItems[$row['id_item']][] = $row['cantidad'];
    			$whereIdItems .= ($whereIdItems=="")? " id=$row[id_item] " : " OR id=$row[id_item] " ;
    		}

    		// CONSULTAR LA INFORMACION REQUERIDA PARA EL MOVIMIENTO DE INVENTARIO
    		$sql="SELECT id,codigo,nombre_equipo,unidad_medida,cantidad_unidades,id_bodega_produccion,inventariable
    				FROM items
    				WHERE activo=1 AND id_empresa=$this->id_empresa AND ($whereIdItems)";
    		$query=$this->mysql->query($sql);
    		while ($row=$this->mysql->fetch_array($query)) {
    			$arrayInfoItems[$row['id']]  = array(
													'codigo'       		=> $row['codigo'],
													'id_bodega'     	=> $row['id_bodega_produccion'],
													'nombre_equipo'     => $row['nombre_equipo'],
													'unidad_medida'     => $row['unidad_medida'],
													'cantidad_unidades' => $row['cantidad_unidades'],
													'inventariable' 	=> $row['inventariable'],
    											);
    		}

    		if (count($arrayItems)>0) {
				$index = 0;
    			foreach ($arrayItems as $id_item => $arrayItemsResul) {
    				foreach ($arrayItemsResul as $key => $cantidad) {
    					// SI EL ITEM NO ES INVENTARIABLE, ENTONCES NO SE DEBE DESCONTAR DEL INVENTARIO
    					if ($arrayInfoItems[$id_item]['inventariable']<>'true') { continue; }
    					$id_bodega = ($arrayInfoItems[$id_item]['id_bodega']>0)? $arrayInfoItems[$id_item]['id_bodega'] : $this->id_bodega_ambiente ;
						
						// id_inventario AS id,
						// codigo,
						// nombre,
						// nombre_unidad_medida AS unidad_medida,
						// cantidad_unidad_medida AS cantidad_unidades,
						// costo_unitario AS costo,
						// cantidad

						$items[$index]  = [
							"id" 				=> $id_item,
							"codigo" 			=> $arrayInfoItems[$id_item]['codigo'],
							"nombre" 			=> $arrayInfoItems[$id_item]['nombre_equipo'],
							"unidad_medida" 	=> $arrayInfoItems[$id_item]['unidad_medida'],
							"cantidad_unidades" => $arrayInfoItems[$id_item]['cantidad_unidades'],
							"costo" 			=> 0,
							"cantidad" 			=> $cantidad,
						];
						$items[$index]["empresa_id"]  = $this->id_empresa;
						$items[$index]["empresa"]     = NULL;
						$items[$index]["sucursal_id"] = $this->id_sucursal;
						$items[$index]["sucursal"]    = $sucursales[$this->id_sucursal];
						$items[$index]["bodega_id"]   = $id_bodega;
						$items[$index]["bodega"]      = $bodegas[$id_bodega];
						
						$index++;

						// $sql="UPDATE inventario_totales
    					// 		SET
						// 			cantidad                     =cantidad $accion $cantidad,
						// 			id_documento_update          = '$this->id_documento',
						// 			tipo_documento_update        = 'POS',
						// 			consecutivo_documento_update = '$this->consecutivo'

						// 		WHERE activo=1 AND id_item=$id_item AND id_ubicacion=$id_bodega";
						// $query=$this->mysql->query($sql);

						// if (!$query) {
						// 	$messageError .= "Se produjo un error al actualizar el item id $id_item <br/>";
						// }

    				}
    			}


				// while ($row = $mysql->fetch_assoc($query)) {
				// 	$items[$index]                = $row;
				// 	$items[$index]["empresa_id"]  = $id_empresa;
				// 	$items[$index]["empresa"]     = NULL;
				// 	$items[$index]["sucursal_id"] = $id_sucursal;
				// 	$items[$index]["sucursal"]    = $sucursal;
				// 	$items[$index]["bodega_id"]   = $id_bodega;
				// 	$items[$index]["bodega"]      = $bodega;
					
				// 	$index++;
				// }
				
				include '../../../inventario/Clases/Inventory.php';
				// echo "2";
				// echo '<script>
				// 		 			document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				// 		 		</script>';
				// 		 		return;
				$params = [ 
					"documento_id"          => $this->id_documento,
					"documento_tipo"        => "POS",
					"documento_consecutivo" => $this->consecutivo,
					"fecha"                 => $this->fecha_documento,
					"accion_inventario"     => "salida",
					"accion_documento"      => "Generar",    // accion del documento, generar, editar, etc
					"items"                 => $items,
					"mysql"                 => $this->mysql
				];
				$obj = new Inventario_pp();
				$process = $obj->UpdateInventory($params);

				if($messageError<>''){
    				$params["nivel"]          = 1;
					$params["estado"]         = 500;
					$params["detalle_estado"] = $messageError;
					$this->rollback($params);
	    			// $this->rollback($id_factura,1);
	        		// return array('status'=>false,'detalle'=>$messageError);
				}
				else{
					// $params['id_bodega']               = "";
					// $params['id_sucursal']             = "";
					// $params['campo_fecha']             = "";
					// $params['tablaPrincipal']          = "";
					// $params['id_documento']            = "";
					// $params['campos_tabla_inventario'] = "";
					// $params['tablaInventario']         = "";
					// $params['idTablaPrincipal']        = "";
					// $params['documento']               = "";
					// $params['descripcion_documento']   = "";
					// $this->logInventario($params);
					echo json_encode(array('status' => true,"consecutivo"=>$this->consecutivo,"cuentas"=>$this->arrayCuentas,"process"=>$process) );
				}
    		}
    		else{ echo json_encode(array('status' => true,"consecutivo"=>$this->consecutivo,"cuentas"=>$this->arrayCuentas) ); }
    	} // END FUNCTION

    	/**
    	 * sendCharges Si el tiquet de venta se va para cargo a una habitacion
    	 * @return [type] [description]
    	 */
    	public function sendCharges(){
    		// CARGOS DE VENTA
    		$sql   = "SELECT
							CP.id,
							VP.id_forma_pago,
							CP.cuenta,
							CP.cuenta_niif,
							VP.valor,
							CP.tipo,
							CP.consecutivo
						FROM
							ventas_pos_formas_pago AS VP
						INNER JOIN configuracion_cuentas_pago_pos AS CP ON CP.id = VP.id_forma_pago
						WHERE
							VP.activo = 1
						AND VP.id_pos = $this->id_documento ";
			$query = $this->mysql->query($sql);
			$aditionalPayments = 0;
			while ($row=$this->mysql->fetch_array($query)) {
				if ($row['tipo']=='Cheque Cuenta') {
					$valorCargo += $row['valor'];
				}
				else{
					$aditionalPayments++;
				}
			}

			// VALIDAR SI EL CLIENTE ES EXCENTO DE IMPUESTO
			// $sql   = "SELECT id_cliente,id_huesped,id_seccion FROM ventas_pos WHERE id=$this->id_documento";
			// $query = $this->mysql->query($sql);
			// $id_cliente = $this->mysql->result($query,0,'id_cliente');
			// $id_huesped = $this->mysql->result($query,0,'id_huesped');

			$sql  = "SELECT exento_iva FROM terceros WHERE id=$id_cliente";
			$query = $this->mysql->query($sql);
			$exento_iva = $this->mysql->result($query,0,'exento_iva');

			if ($valorCargo>0) {
				// SI EL VALOR COMPLETO SE PAGA COMO CARGO A UNA HABITACION
				if ($aditionalPayments==0) {

					$arrayItems = $this->getItemsDocumento();
					foreach ($arrayItems['items'] as $key => $arrayResult){
						if ($arrayResult['generaImpuesto']==false) { continue; }
						$arrayCargos[$arrayResult['codigo_transaccion']]['nit']                 = $this->datosEmpresa['documento'];
						// $arrayCargos[$arrayResult['codigo_transaccion']]['nit']              = '2002';
						$arrayCargos[$arrayResult['codigo_transaccion']]['fecha_documento']     = $this->fecha_documento;
						$arrayCargos[$arrayResult['codigo_transaccion']]['id_reserva']          = $this->id_reserva;
						$arrayCargos[$arrayResult['codigo_transaccion']]['id_user']             = $this->id_usuario;
						$arrayCargos[$arrayResult['codigo_transaccion']]['valor']               += $arrayResult['precio_unround'];
						$arrayCargos[$arrayResult['codigo_transaccion']]['porcentaje_impuesto'] =  $arrayResult['porcentaje_impuesto'];
						$arrayCargos[$arrayResult['codigo_transaccion']]['impuesto']            += (($exento_iva=='Si')? 0 : $arrayResult['impuesto_unround']);
						// $arrayCargos[$arrayResult['codigo_transaccion']]['id_ticket']           = $this->id_documento;
						// $arrayCargos[$arrayResult['codigo_transaccion']]['numero_ticket']       = $this->consecutivo;
					}

					// PROPINAS
					$sql = "SELECT
								CP.id,
								VP.valor_propina,
								CP.cod_tx,
								CP.nombre
							FROM
								ventas_pos AS VP
							INNER JOIN configuracion_propinas_pos AS CP ON CP.id = VP.id_propina
							WHERE
								VP.activo = 1
							AND VP.id = $this->id_documento ";
					$query = $this->mysql->query($sql);
					while ($row=$this->mysql->fetch_array($query)) {
						if ($row['valor_propina']==0 || $row['valor_propina']==='') { continue; }

						if ($row['cod_tx']=='') {
							$params["nivel"]          = 1;
							$params["estado"]         = 500;
							$params["detalle_estado"] = "La propina $row[nombre] no tiene configurado un codigo de transaccion para ser sincronizado al pms";
							$this->rollback($params);
						}
						$arrayCargos[$row['cod_tx']]['nit']                 = $this->datosEmpresa['documento'];
						// $arrayCargos[$row['cod_tx']]['nit']                 = 1;
						$arrayCargos[$row['cod_tx']]['fecha_documento']     = $this->fecha_documento;
						$arrayCargos[$row['cod_tx']]['id_reserva']          = $this->id_reserva;
						$arrayCargos[$row['cod_tx']]['id_user']             = $this->id_usuario;
						$arrayCargos[$row['cod_tx']]['valor']               += $row['valor_propina'];
						$arrayCargos[$row['cod_tx']]['porcentaje_impuesto'] =  0;
						$arrayCargos[$row['cod_tx']]['impuesto']            += 0;
					}


					foreach ($arrayCargos as $codTransaccion => $arrayResult) {
						$params['request_url']    = $this->apiHotels['url']."agregarCargoReserva"; // ESTA VARIABLE ES HEREADA DE LA CLASE DE FUNCIONES GLOBALES
						$params['request_method'] = "POST";
						// $params['Authorization']  = "Authorization: $arrayApiInfo[authorization] ".base64_encode("$_SESSION[NOMBREUSUARIO]:$token:".$this->arrayInfoEmpresa['documento']);
						$data = array(
										"nit"             => $arrayResult['nit'],
										"fecha_registro"  => $arrayResult['fecha_documento'],
										"reservacion_id"  => $arrayResult['id_reserva'],
										"id_user"         => $arrayResult['id_user'],
										"cod_transaccion" => $codTransaccion,
										"valor_unitario"  => $arrayResult['valor'],
										"impuesto"        => $arrayResult['porcentaje_impuesto'],
										"valor_impuesto"  => $arrayResult['impuesto'],
										"huesped_id"      => $this->id_huesped,
										"id_ticket"       => $this->id_documento,
										"numero_ticket"   => $this->consecutivo
									);
						$params['data'] = json_encode($data);
						$response = json_decode($this->curlApi($params),true); // ESTA FUNCION ES HEREDADA DE LA CLASE FUNCIONES GLOBALES
						$json_log = $params['data'];

						$sql="UPDATE ventas_pos SET  json_log='$json_log'  WHERE id=$this->id_documento ";
						$query=$this->mysql->query($sql);
					}
					if($response['resp']<>'true'){
						$params["nivel"]          = 1;
						$params["estado"]         = 500;
							if($response['msg'] == '' || is_null($response['msg'])){
							$params["detalle_estado"] = "Error al sincronizar el cargo al PMS : No se ha recibido respuesta del API";
							}
							else{
							$params["detalle_estado"] = "Error al sincronizar el cargo al PMS : ".$response['msg'];
							}
						$this->rollback($params);
					}
					else{
						$sql="UPDATE ventas_pos SET  fecha_documento='$response[fecha_auditoria]' WHERE id=$this->id_documento";
						$query=$this->mysql->query($sql);
					}

				}
				// SI SE PAGA UNA PARTE COMO CH Y OTRA CON OTRO METODO DE PAGO
				else{

					$params['request_url']    = $this->apiHotels['url']."agregarCargoReserva"; // ESTA VARIABLE ES HEREADA DE LA CLASE DE FUNCIONES GLOBALES
					$params['request_method'] = "POST";
					$vUnit = $valorCargo/1.08;
					$vImp  = $vUnit*0.08;

					$data = array(
									"nit"             => $this->datosEmpresa['documento'],
									// "nit"             => '2002',
									"fecha_registro"  => $this->fecha_documento,
									"reservacion_id"  => $this->id_reserva,
									"id_user"         => $this->id_usuario,
									"cod_transaccion" => $this->codigo_transaccion,
									"valor_unitario"  => $vUnit,
									"impuesto"        => 8,
									"valor_impuesto"  => (($exento_iva=='Si')? 0 : $vImp),
									"huesped_id"      => $this->id_huesped,
									"id_ticket"       => $this->id_documento,
									"numero_ticket"   => $this->consecutivo
								);
					$params['data'] = json_encode($data);
					$response = json_decode($this->curlApi($params),true); // ESTA FUNCION ES HEREDADA DE LA CLASE FUNCIONES GLOBALES
					$json_log = $params['data'];

					$sql="UPDATE ventas_pos SET  json_log='$json_log'  WHERE id=$this->id_documento ";
					$query=$this->mysql->query($sql);

					if($response['resp']<>'true'){
						$params["nivel"]          = 1;
						$params["estado"]         = 500;
						$params["detalle_estado"] = "Error al sincronizar el cargo al PMS (CH combined) : ".$response['msg'];
						$this->rollback($params);
					}
					else{
						$sql="UPDATE ventas_pos SET  fecha_documento='$response[fecha_auditoria]' WHERE id=$this->id_documento";
						$query=$this->mysql->query($sql);
					}

				}// END ELSE

			}// END IF VALOR CH >0

    	}

    	/**
		 * setAsientos Contabilizar la factura en norma local
		 * @return Array Resultado del proceso {status: true o false},{detalle: en caso de ser error se detallara en este campo}
		 */
		public function setAsientos(){
			$this->arrayCuentas = $this->getCuentas();
			// echo json_encode($this->arrayCuentas);
			if ($this->arrayCuentas['status']===false) {
				$params["nivel"]          = 1;
				$params["estado"]         = 500;
				$params["detalle_estado"] = $this->arrayCuentas['detalle'];
				$this->rollback($params);
			}

			foreach ($this->arrayCuentas['colgaap'] as $cuenta => $arrayResult) {
				$debito  += $arrayResult['debito'];
				$credito += $arrayResult['credito'];
				$insertString .= "(
									'$this->id_documento',
									'$this->consecutivo',
									'POS',
									'Tiquet de venta POS',
									'$this->id_documento',
									'POS',
									'$this->consecutivo',
									'$this->fecha_documento',
									'".$arrayResult['debito']."',
									'".$arrayResult['credito']."',
									'$cuenta',
									'$this->id_cliente',
									'$this->documento_cliente',
									'$this->cliente',
									'".$arrayResult['idCcos']."',
									'$this->id_sucursal',
									'$this->id_empresa'
								),";
			}

			$debito  = ROUND($debito,$this->decimales_moneda);
			$credito = ROUND($credito,$this->decimales_moneda);

			if ($debito<>$credito) {
				// echo json_encode($this->arrayCuentas);
				// echo "-------";
				$params["nivel"]          = 1;
				$params["estado"]         = 500;
				$params["detalle_estado"] = "El valor en debito y credito para la contabilidad colgapp son diferentes debito: $debito credito: $credito ".json_encode($this->arrayDebug['items']);
				$this->rollback($params);
			}

			$debito  = 0;
			$credito = 0;
			foreach ($this->arrayCuentas['niif'] as $cuenta => $arrayResult) {
				$debito  += $arrayResult['debito'];
				$credito += $arrayResult['credito'];
				$insertStringNiif .= "(
									'$this->id_documento',
									'$this->consecutivo',
									'POS',
									'Tiquet de venta POS',
									'$this->id_documento',
									'POS',
									'$this->consecutivo',
									'$this->fecha_documento',
									'".$arrayResult['debito']."',
									'".$arrayResult['credito']."',
									'$cuenta',
									'$this->id_cliente',
									'$this->documento_cliente',
									'$this->cliente',
									'".$arrayResult['idCcos']."',
									'$this->id_sucursal',
									'$this->id_empresa'
								),";
			}

			$debito  = ROUND($debito,$this->decimales_moneda);
			$credito = ROUND($credito,$this->decimales_moneda);
			if ($debito<>$credito) {
				$params["nivel"]          = 1;
				$params["estado"]         = 500;
				$params["detalle_estado"] = "El valor en debito y credito para la contabilidad niif son diferentes";
				$this->rollback($params);
			}

			$insertString = substr($insertString, 0, -1);
			$sql   = "INSERT INTO asientos_colgaap
						(
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
							nit_tercero,
							tercero,
							id_centro_costos,
							id_sucursal,
							id_empresa
						)
					VALUES $insertString";
			$query = $this->mysql->query($sql);
			// SI NO SE CAUSO LA CONTABILIDAD PUEDE SER POR QUE ES CHEQUE CUENTA Y LO QUE SE VENDIO NO TIENE RECETA
			if (!$query && $this->causaIngreso==false) {
				$params["nivel"]          = 1;
				$params["estado"]         = 500;
				$params["detalle_estado"] = "No se inserto la contabilidad colgaap ".json_encode($this->arrayCuentas);
				$this->rollback($params);
			}

			$insertStringNiif = substr($insertStringNiif, 0, -1);
			$sql   = "INSERT INTO asientos_niif
							(
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
								nit_tercero,
								tercero,
								id_centro_costos,
								id_sucursal,
								id_empresa
							)
						VALUES $insertStringNiif";
			$query = $this->mysql->query($sql);
			if (!$query && $this->causaIngreso==false ) {
				$params["nivel"]          = 1;
				$params["estado"]         = 500;
				$params["detalle_estado"] = "No se inserto la contabilidad niif";
				$this->rollback($params);
			}

			// $params["nivel"]          = 1;
			// 	$params["estado"]         = 500;
			// 	$params["detalle_estado"] = "contrlled error";
			// 	$this->rollback($params);
			// ['niif']
			// echo json_encode( array('status' => true, 'cuentas' => $arrayCuentas, "insert"=>$insertString) );
		} // END FUNCTION

    	/**
    	 * getItemsDocumento Consultar los items del tiquet de venta y calcular los valores a contabilizar
    	 * @return Array Array con los items y sus valores calculados para contabilizacion
    	 *                     items array con la informacion de los items, con los siguientes campos:
		 *                     	     row             	= Id de la fila en la bd
		 *					         cantidad        	= Cantidad del item vendido o usado en la receta
		 *						     precio_venta    	= Precio de venta individual en que se vendio el item
		 *						     precio          	= Precio total del item vendido (precio_venta*cantidad)
		 *						     codigo_transaccion = codigo de transaccion para usarse en el LOGICALHOTELS
		 *						     costo           	= Costo de inventario de ese item, se toma el costo de la bodega de produccion o del ambiente y se multiplica por la cantidad
		 *						     codigo          	= Codigo unico que identifica el item
		 *						     id_impuesto     	= Id del impuesto del item
		 *						     impuesto        	= Valor calculado del impuesto aplicado a ese item
		 *						     cuenta_iva      	= Cuenta en norma local en donde se contabilizara el valor del impuesto
		 *						     cuenta_iva_niif 	= Cuenta en norma NIIF en donde se contabilizara el valor del impuesto
		 *						     id_item         	= Id unico que identifica al item (Se puede reetir aqui por que se pudo vender varias veces)
		 *						     inventariable   	= Define si el articulo en inventariable o no para contabilizar el costo si es inventariable
		 *						idItems array con los id de los item como clave
    	 */
    	public function getItemsDocumento(){
    		$sql="SELECT
    					id,
						id_pos,
						id_item,
						codigo,
						codigo_barras,
						id_unidad_medida,
						nombre_unidad_medida,
						cantidad_unidad_medida,
						nombre,
						cantidad,
						saldo_cantidad,
						precio_venta,
						costo_inventario,
						id_impuesto,
						impuesto,
						valor_impuesto,
						id_empresa,
						id_sucursal,
						id_bodega,
						inventariable
    				FROM ventas_pos_inventario
    				WHERE activo=1 AND id_pos=$this->id_documento";
    		$query=$this->mysql->query($sql);
    		$contItems = 0;
    		while ($row=$this->mysql->fetch_array($query)) {
    			$arrayIdItem[$row['id_item']] = $row['codigo'];
    			$arrayIdImpuestos[$row['id_impuesto']] = $row['impuesto'];
                $arrayItems[]  = array(
										'row'                    => $row['id'],
										'cantidad'               => $row['cantidad'],
										'precio_venta'           => $row['precio_venta'],
										'codigo'                 => $row['codigo'],
										'id_impuesto'            => $row['id_impuesto'],
										// 'porcentaje_impuesto' => $row['impuesto'],
										'id_item'                => $row['id_item'],
										'inventariable'          => $row['inventariable'],
										'subtotal'               => ($row['precio_venta']*$row['cantidad']),
										'generaImpuesto'         => true
                                    );
                $contItems++;
    		}

    		// APLICAR LOS DESCUENTOS
    		$saldo_descuento = $this->valor_descuento;
    		foreach ($arrayItems as $key => $arrayResult){
    			// $this->arrayDebug['descuento_ini'][] = $saldo_descuento;
    			// $this->arrayDebug['item_ini'][$arrayItems[$key]['codigo']][] = $arrayItems[$key]['subtotal'];
    			if ($saldo_descuento<=0) { break; }
    			if (($saldo_descuento-$arrayResult['subtotal'])<0) {
    				// $this->arrayDebug['descuento_process'][] = $arrayResult['subtotal']-$saldo_descuento;
    				$arrayItems[$key]['subtotal'] = $arrayResult['subtotal']-$saldo_descuento;
    				$saldo_descuento = 0;
    			}
    			else{
    				// $arrayItems[$key]['subtotal'] = $arrayResult['subtotal']-$saldo_descuento;
    				$arrayItems[$key]['subtotal'] = 0;
    				$saldo_descuento = $saldo_descuento-$arrayResult['subtotal'];
    			}
    				// $this->arrayDebug['descuento_process'][] = $arrayResult['subtotal']-$saldo_descuento;
    			// $this->arrayDebug['item_value'][$arrayItems[$key]['codigo']][] = $arrayItems[$key]['subtotal'];
    		}

    		$this->countItems = $contItems;
    		$sql="SELECT
    					id,
						id_pos,
						id_item_producto,
						id_item,
						codigo,
						id_unidad_medida,
						nombre_unidad_medida,
						cantidad_unidad_medida,
						nombre,
						cantidad
    				FROM ventas_pos_inventario_receta
    				WHERE activo=1 AND id_empresa=$this->id_empresa AND id_pos=$this->id_documento";
    		$query=$this->mysql->query($sql);
    		while ($row=$this->mysql->fetch_array($query)) {
    			$arrayIdItem[$row['id_item']] = $row['codigo'];
    			$arrayItems[]  = array(
										'row'            => $row['id'],
										'cantidad'       => $row['cantidad'],
										'precio_venta'   => $row['precio_venta'],
										'codigo'         => $row['codigo'],
										'id_impuesto'    => $row['id_impuesto'],
										'id_item'        => $row['id_item'],
										'inventariable'  => $row['inventariable'],
										'generaImpuesto' => false
                                    );
    		}

    		// CONSULTAR EL COD TX DE LA SECCION O AMBIENTE POR DONDE SE ESTA VENDIENDO
    		$sql = "SELECT id_seccion FROM ventas_pos WHERE activo=1 AND id=$this->id_documento";
    		$query=$this->mysql->query($sql);
    		$id_seccion = $this->mysql->result($query,0,'id_seccion');

    		// CONSULTAR EL COD TX CONFIGURADO POR ITEM POR AMBIENTE
    		$sql="SELECT id_item,cod_tx FROM items_cod_tx WHERE activo=1 AND id_seccion=$id_seccion";
    		$query=$this->mysql->query($sql);
    		while ($row=$this->mysql->fetch_array($query)) {
    			$arraySeccionesItemsTx[$row['id_item']] = $row['cod_tx'];
    		}

    		$sql = "SELECT codigo_transaccion FROM ventas_pos_secciones WHERE activo=1 AND id=$id_seccion";
    		$query=$this->mysql->query($sql);
    		$codigo_transaccion = $this->mysql->result($query,0,'codigo_transaccion');

    		// CONSULTAR LA BODEGA DE PRODUCCION CONFIGURADA DE CADA ITEM
			$whereIdItem = "id='".implode("' OR id='", array_keys($arrayIdItem))."'";
    		$sql="SELECT id,id_bodega_produccion,id_impuesto,codigo_transaccion
    				FROM items WHERE activo=1 AND id_empresa=$this->id_empresa AND ($whereIdItem)";
    		$query=$this->mysql->query($sql);
    		while ($row=$this->mysql->fetch_array($query)) {
    			$arrayBodegas[$row['id_bodega_produccion']] = $row['id_bodega_produccion'];
    			// $arrayItemsCodTran[$row['id']] = ($row['codigo_transaccion']==='')? $codigo_transaccion : $row['codigo_transaccion'];
    			$arrayItemsCodTran[$row['id']] = $row['codigo_transaccion'];
    		}

    		// CONSULTAR EL IMPUESTO Y LA CUENTA (SOLO DE LOS ITEMS VENDIDOS NO DE LOS INGREDIENTES)
			$whereIdImpuestos   = "id='".implode("' OR id='", array_keys($arrayIdImpuestos))."'";
    		$sql="SELECT id,valor,cuenta_venta,cuenta_venta_niif
    				FROM impuestos WHERE activo=1 AND venta='Si' AND id_empresa=$this->id_empresa AND ($whereIdImpuestos)";
    		$query=$this->mysql->query($sql);
    		while ($row=$this->mysql->fetch_array($query)) {
    			$arrayImpuestos[$row['id']] = array(
													'valor'             => $row['valor'],
													'cuenta_venta'      => $row['cuenta_venta'],
													'cuenta_venta_niif' => $row['cuenta_venta_niif'],
    												);
    		}


    		// CONSULTAR EL COSTO DE LOS ITEMS DEL INVENTARIO (COSTOS DE INVENTARIO)
			$whereIdItem   = "id_item='".implode("' OR id_item='", array_keys($arrayIdItem))."'";
			$whereIdBodega = "id_ubicacion='".implode("' OR id_ubicacion='", array_keys($arrayBodegas))."'";
    		$sql="SELECT id_ubicacion,id_item,costos FROM inventario_totales
					WHERE activo=1 AND id_empresa=$this->id_empresa AND ($whereIdItem) AND ($whereIdBodega) ";
    		$query=$this->mysql->query($sql);
    		while ($row=$this->mysql->fetch_array($query)) {
				$id_ubicacion = $row['id_ubicacion'];
				$id_item      = $row['id_item'];
				$costos       = $row['costos'];
    			$arrayCostos['bodega_produccion'][$id_item] = array('id_bodega' => $id_ubicacion, 'costo' => $costos, );
    		}

    		// CONSULTAR EL COSTO DE INVENTARIO DEL AMBIENTE (EN CASO DE QUE EL ITEM NO TENGA ONFIGURADA BODEGA)
			$sql   = "SELECT id_ubicacion,id_item,costos FROM inventario_totales
					WHERE activo=1 AND id_empresa=$this->id_empresa AND ($whereIdItem) AND id_ubicacion=$this->id_bodega_ambiente ";
			$query = $this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)) {
				$id_ubicacion = $row['id_ubicacion'];
				$id_item      = $row['id_item'];
				$costos       = $row['costos'];
    			$arrayCostos['bodega_ambiente'][$id_item] = array('id_bodega' => $id_ubicacion, 'costo' => $costos, );
    		}

    		// RECORRER LOS ITEMS PARA ASIGNAR EL COSTO DE INVENTARIO E INFORMACION DE IMPUESTOS
    		foreach ($arrayItems as $key => $arrayResult){
    			// SI EL ITEM TIENE UN COD TX POR EL AMBIENTE
    			if ($arraySeccionesItemsTx[$arrayResult['id_item']]<>'') {
    				$arrayItems[$key]['codigo_transaccion'] = $arraySeccionesItemsTx[$arrayResult['id_item']];
    			}
    			// SI EL AMBIENTE TIENE UN COD TX GLOBAL PARA TODOS
    			else if ($codigo_transaccion<>'') {
    				$arrayItems[$key]['codigo_transaccion'] = $codigo_transaccion;
    			}
    			// SI EL ITEM TIENE UN COD TX PROPIO
    			else{
    				$arrayItems[$key]['codigo_transaccion'] = $arrayItemsCodTran[$arrayResult['id_item']];
    			}

    			// $arrayItems[$key]['codigo_transaccion'] = $arrayItemsCodTran[$arrayResult['id_item']];
    			if ( count( $arrayCostos['bodega_produccion'][$arrayResult['id_item']] ) > 0 ) {
    				$unitCost = $arrayCostos['bodega_produccion'][$arrayResult['id_item']]['costo'];
    				$costoInv = $unitCost*$arrayResult['cantidad'];
    			}
    			else{
    				$unitCost = $arrayCostos['bodega_ambiente'][$arrayResult['id_item']]['costo'];
    				$costoInv = $unitCost*$arrayResult['cantidad'];
    			}
    			$arrayItemsCost[$arrayItems[$key]['id_item']]['costo_unit'] = $unitCost;
    			$arrayItems[$key]['costo'] = $costoInv;
    			if ($arrayItems[$key]['generaImpuesto']==true) {

    				$precio = ROUND($arrayResult['precio_venta']*$arrayResult['cantidad']);
    				if ($this->valor_descuento>0) {
    					$precio = $precio-($this->valor_descuento/$contItems);
    				}
    				// $this->totales['precio'][]=$precio;
					$this->globalPercetTax         = ($this->globalPercetTax        =='')? $arrayImpuestos[$arrayResult['id_impuesto']]['valor'] : $this->globalPercetTax;
					$this->globalAccountTaxColgaap = ($this->globalAccountTaxColgaap=='')? $arrayImpuestos[$arrayResult['id_impuesto']]['cuenta_venta'] : $this->globalAccountTaxColgaap;
					$this->globalAccountTaxNiif    = ($this->globalAccountTaxNiif   =='')? $arrayImpuestos[$arrayResult['id_impuesto']]['cuenta_venta_niif'] : $this->globalAccountTaxNiif;


    				$taxPercent = ($arrayImpuestos[$arrayResult['id_impuesto']]['valor']*0.01)+1;
					// $subtotal   = $precio/$taxPercent;
					$subtotal   = $arrayResult['subtotal']/$taxPercent;
					$impuestos  = (($subtotal/*/$taxPercent*/)*$arrayImpuestos[$arrayResult['id_impuesto']]['valor'])/100;
    				// $this->totales['subtotal'][]=$subtotal;
    				// $this->totales['impuestos'][]=$impuestos;

					// $arrayItems[$key]['impuesto']         = ROUND(($arrayItems[$key]['precio'] * $arrayImpuestos[$arrayResult['id_impuesto']]['valor'])/100,$this->decimalesMoneda);
					$arrayItems[$key]['precio_unround']      = $subtotal;
					$arrayItems[$key]['impuesto_unround']    = $impuestos;
					$arrayItems[$key]['precio']              = ROUND($subtotal,$this->decimalesMoneda);
					$arrayItems[$key]['impuesto']            = ROUND($impuestos,$this->decimalesMoneda);
					$arrayItems[$key]['porcentaje_impuesto'] = $arrayImpuestos[$arrayResult['id_impuesto']]['valor'];
					$arrayItems[$key]['cuenta_iva']          = $arrayImpuestos[$arrayResult['id_impuesto']]['cuenta_venta'];
					$arrayItems[$key]['cuenta_iva_niif']     = $arrayImpuestos[$arrayResult['id_impuesto']]['cuenta_venta_niif'];
    			}
    		}
   			//  		$params["nivel"]          = 1;
			// $params["estado"]         = 500;
			// $params["detalle_estado"] = json_encode($arrayItemsCost);
			// // print_r($this->arrayCuentas);
			// // exit;
			// $this->rollback($params);
			$arrayReturn['items']   = $arrayItems;
			$arrayReturn['idItems'] = $arrayIdItem;
			$arrayReturn['cost']    = $arrayItemsCost;
			$this->arrayDebug ['items']   = $arrayItems;
			// echo json_encode($arrayItems);

    		return $arrayReturn;
    	} // END FUNCTION

    	public function updateItemCost($arrayItems){
    		$sql="SELECT
    					id,
						id_pos,
						id_item_producto,
						id_item,
						codigo,
						id_unidad_medida,
						nombre_unidad_medida,
						cantidad_unidad_medida,
						nombre,
						cantidad
    				FROM ventas_pos_inventario_receta
    				WHERE activo=1 AND id_empresa=$this->id_empresa AND id_pos=$this->id_documento";
    		$query=$this->mysql->query($sql);
    		while ($row=$this->mysql->fetch_array($query)) {
    			$cost = $arrayItems['cost'][$row['id_item']]['costo_unit'];
				$sqlUpdate   = "UPDATE ventas_pos_inventario_receta SET costo=$cost WHERE id=$row[id]";
				$queryUpdate = $this->mysql->query($sqlUpdate);
    		}
    	}

    	/**
    	 * getCuentasPago Consultar las cuentas de pago del tiquet de venta
    	 * @return Array Listado de las cuentas contables de pago con sus respectivos valores
    	 */
    	public function getCuentasPago(){
			$sql   = "SELECT
							CP.id,
							VP.id_forma_pago,
							CP.cuenta,
							CP.cuenta_niif,
							VP.valor,
							CP.tipo,
							CP.consecutivo,
							CP.id_cuenta_costo,
							CP.nombre_cuenta_costo,
							CP.cuenta_costo,
							CP.cuenta_costo_niif,
							CP.id_tercero,
							CP.nit_tercero,
							CP.tercero,
							CP.id_centro_costos,
							CP.cod_centro_costos,
							CP.centro_costos,
							IF(CP.tipo='Cheque Cuenta','1','2') AS tipo_ch
						FROM
							ventas_pos_formas_pago AS VP
						INNER JOIN configuracion_cuentas_pago_pos AS CP ON CP.id = VP.id_forma_pago
						WHERE
							VP.activo = 1
						AND VP.id_pos = $this->id_documento
						ORDER BY tipo_ch ASC";
			$query = $this->mysql->query($sql);
			$tipo = "";
			while ($row=$this->mysql->fetch_array($query)) {
				// SI LA FORMA DE PAGO ES CORTESIA O CHEQUE CUENTA, NO SE DEBEN CAUSAR NI FORMAS DE PAGO NI EL INGRESO
				switch ($row['tipo']) {
					case 'Cortesia':
						$tipo = ($tipo =="" )? 'Cortesia' : $tipo ;
						break;
					case 'Cheque Cuenta':
						$tipo = ($tipo =="" )? 'Cheque Cuenta' : $tipo ;
						break;
					default:
						$tipo = "FV" ;
						break;
				}
				// if ($row['tipo']=='Cortesia' || $row['tipo']=='Cheque Cuenta') { $this->causaIngreso = true; }

				// CONSECUTIVO DE LA FORMA DE PAGO (SOLO CON CHEQUE CUENTA Y CORTESIA)
				$arrayReturn['consecutivo'][$row['tipo']]['id']          = $row['id'];
				$arrayReturn['consecutivo'][$row['tipo']]['consecutivo'] = $row['consecutivo'];

				$arrayReturn['colgapp'][$row['cuenta']]['tipo'] = $row['tipo'];
				$arrayReturn['colgapp'][$row['cuenta']]['valor'] += $row['valor'];

				$arrayReturn['niif'][$row['cuenta_niif']]['tipo']  = $row['tipo'];
				$arrayReturn['niif'][$row['cuenta_niif']]['valor'] += $row['valor'];

				// OPCION COSTO CORTESIA
				$arrayReturn['id_cuenta_costo']     = $row['id_cuenta_costo'];
				$arrayReturn['nombre_cuenta_costo'] = $row['nombre_cuenta_costo'];
				$arrayReturn['cuenta_costo']        = $row['cuenta_costo'];
				$arrayReturn['cuenta_costo_niif']   = $row['cuenta_costo_niif'];
				$arrayReturn['id_tercero']          = $row['id_tercero'];
				$arrayReturn['nit_tercero']         = $row['nit_tercero'];
				$arrayReturn['tercero']             = $row['tercero'];
				$arrayReturn['id_centro_costos']    = $row['id_centro_costos'];
				$arrayReturn['cod_centro_costos']   = $row['cod_centro_costos'];
				$arrayReturn['centro_costos']       = $row['centro_costos'];
				if ($tipo=='Cheque Cuenta') {
					break;
				}
			}
			if ($tipo<>'FV') {
				$this->causaIngreso = true;
			}
			$arrayReturn['sql'] = $sql;
			return $arrayReturn;
    	} // END FUNCTION

    	/**
    	 * getCuentaPropina Consultar las cuentas de la propina del tiquet de venta
    	 * @return Array Listado de las cuentas contables de las propinas
    	 */
    	public function getCuentaPropina(){
    		$arrayReturn = [];
    		$sql="SELECT
    				id,
    				cuenta,
					cuenta_niif
				 FROM configuracion_propinas_pos
				 WHERE activo=1 AND id=$this->id_propina ";
    		$query=$this->mysql->query($sql);
    		while ($row=$this->mysql->fetch_array($query)) {
				$arrayReturn['colgapp'][$row['cuenta']]   = $row['id'];
				$arrayReturn['niif'][$row['cuenta_niif']] = $row['id'];
    		}
    		return $arrayReturn;
    	} // END FUNCTION

    	/**
    	 * getItemsCuentas Consultar las cuentas locales y niif de los items para la causacion
    	 * @param  String $tabla       Nombre de la tabla de las cuentas a consultar
    	 * @param  String $whereIdItem Where con los id de los item a consultar las cuentas
    	 * @return Array               Lista de las cuentas de los items consultados
    	 */
    	public function getItemsCuentas($tabla,$whereIdItem){
    		$sql= "SELECT id,id_items,descripcion, puc, tipo
					FROM $tabla
					WHERE activo=1
						AND id_empresa='$this->id_empresa'
						AND estado='venta'
						AND ($whereIdItem)
					GROUP BY id_items,descripcion
					ORDER BY id_items ASC";
    		$query=$this->mysql->query($sql);
    		while ($row=$this->mysql->fetch_array($query)) {
				if($row['descripcion'] == 'contraPartida_precio'){
					$row['puc'] = $cuentaPago;
					$row['tipo'] = 'debito';
				}
				if($row['descripcion'] == 'impuesto'){
					$row['tipo'] = 'credito';
				}
				$arrayCuentasItems[$row['id_items']][$row['descripcion']]= array(
																				'estado' => $row['tipo'],
																				'cuenta' => $row['puc']
																			);
    		}
			return $arrayCuentasItems;

    	}

        /**
         * getCuentas Consultar las cuentas de los items
         * @return Array Array con las cuentas de los items y sus valores
         */
        public function getCuentas(){
			$arrayItems         = $this->getItemsDocumento();
			// ACTUALIZAR EL COSTO DE LOS ITEMS
			$this->updateItemCost($arrayItems);
			$arrayCuentasPago   = $this->getCuentasPago();
			$arraycuentaPropina = $this->getCuentaPropina();

			$this->cuentasPago = $arrayCuentasPago;
			$this->aditionalPayments = $this->getAditionalPayments();

            // CONSULTAR LAS CUENTAS DE LOS ITEMS (COLGAAP)
			$whereIdItem   = "id_items='".implode("' OR id_items='", array_keys($arrayItems['idItems']))."'";
    		$arrayCuentasItems = $this->getItemsCuentas('items_cuentas',$whereIdItem);
            // CONSULTAR LAS CUENTAS DE LOS ITEMS (NIIF)
    		$arrayCuentasItemsNiif = $this->getItemsCuentas('items_cuentas_niif',$whereIdItem);
			$arrayAsiento['colgaap'] = $this->setLocalAccounts($arrayCuentasPago['colgapp'],$arraycuentaPropina['colgapp'],$arrayItems,$arrayCuentasItems);
			$arrayAsiento['niif']    = $this->setNiifAccounts($arrayCuentasPago['niif'],$arraycuentaPropina['niif'],$arrayItems,$arrayCuentasItems);

			return $arrayAsiento;
        } // END FUNCTION

        /**
         * getAditionalPayments Cuando se genera un Cheque cuenta, pero el pago va parcial al Cheque y otra se paga con otros metodos de pago, se consultan las cuentas de los otros metodos de pagos para contabilizar esos pagos e ingresos
         * @return Array Array con las cuentas de los metodos de pago
         */
        public function getAditionalPayments(){
        	$sql   = "SELECT
							CP.id,
							VP.id_forma_pago,
							CP.cuenta,
							CP.cuenta_niif,
							VP.valor,
							CP.tipo,
							CP.consecutivo
						FROM
							ventas_pos_formas_pago AS VP
						INNER JOIN configuracion_cuentas_pago_pos AS CP ON CP.id = VP.id_forma_pago
						WHERE
							VP.activo = 1
						AND VP.id_pos = $this->id_documento ";
			$query = $this->mysql->query($sql);
			$aditionalPayments = 0;
			while ($row=$this->mysql->fetch_array($query)) {
				if ($row['tipo']=='Cheque Cuenta') {
					$valorCH += $row['valor'];
				}
				else{
					$valorAditionalPay += $row['valor'];

    				$taxPercent = ($this->globalPercetTax*0.01)+1;
					$subtotal   = $row['valor']/$taxPercent;
					$impuestos  = ($subtotal*$this->globalPercetTax)/100;

					// METODOS DE PAGO COLGAAP
					$arrayAsiento['colgaap'][$row['cuenta']]['debito']                  += $row['valor'];
					$arrayAsiento['colgaap'][$this->cuenta_ingreso_colgaap]['credito']  += $subtotal;
					$arrayAsiento['colgaap'][$this->globalAccountTaxColgaap]['credito'] += $impuestos;

					// METODOS DE PAGO NIIF
					$arrayAsiento['niif'][$row['cuenta_niif']]['debito']                += $row['valor'];
					$arrayAsiento['niif'][$this->cuenta_ingreso_niif]['credito']        += $subtotal;
					$arrayAsiento['niif'][$this->globalAccountTaxNiif]['credito']       += $impuestos;
				}
			}
			if ($valorCH>0 && $valorAditionalPay>0) {
				return $arrayAsiento;
			}
        }

        /**
         * setLocalAccounts Crear array con las cuentas contables y sus valores segun los items del tiquet
         * @param Array $arrayCuentasPago   Array con las formas de pago del tiquet de venta
         * @param Array $arraycuentaPropina Array con las cuentas de la propina si se aplico
         * @param Array $arrayItems         Array con todos los items de tiquet de venta
         * @param Array $arrayCuentasItems  Array con las cuentas de los items del itquet
         */
        public function setLocalAccounts($arrayCuentasPago,$arraycuentaPropina,$arrayItems,$arrayCuentasItems){
        	$arrayAsiento = $this->aditionalPayments['colgaap'];
        	// return $arrayCuentasItems;
			// SI ES CHEQUE CUENTA,CORTESIA NO SE GENERA INGRESO NI MOVIMIENTO DE DINERO EN BANCO O CAJA
			if ($this->causaIngreso==false) {
				// ASIGNAR LAS CUENTAS DE PAGO
				// LA NATURALEZA DE LAS CUENTAS DE PAGO SIEMPRE VAN A SER DEBITO
				foreach ($arrayCuentasPago as $cuenta => $arrayResult) {
					// if ($row['tipo']=='Cortesia' || $row['tipo']=='Cheque Cuenta') {
					// 	$resta
					// }
					// else{
						$arrayAsiento[$cuenta]['debito'] += ROUND($arrayResult['valor'],$this->decimalesMoneda);
					// }
				}

				// RECORRER LAS CUENTAS DE LA PROPINA
				// LA NATURALEZA DE LAS PROPINAS SIEMPRE SON CREDITO
				if ($this->valor_propina>0) {
					foreach ($arraycuentaPropina as $cuenta => $cuenta_niif) {
						$arrayAsiento[$cuenta]['credito'] += ROUND($this->valor_propina,$this->decimalesMoneda);
					}
				}
			}

    		// CREAR ARRAY CON LAS CUENTAS A CAUSAR CON LAS CUENTAS DE LOS ITEMS Y SUS VALORES
    		foreach ($arrayItems['items'] AS $arrayResult) {
        		// return $arrayResult;
    			// VARIABLES ACUMULADAS GLOBALES
				$arrayItemEstado['debito']  = 0;
				$arrayItemEstado['credito'] = 0;
				$id_item    = $arrayResult['id_item'];

				// CUENTAS DEL ITEM CONFIGURADAS (COSTO)
				$cuentaCosto = $arrayCuentasItems[$id_item]['costo']['cuenta'];
				$contraCosto = ($this->cuentasPago['cuenta_costo']<>'')? $this->cuentasPago['cuenta_costo'] : $arrayCuentasItems[$id_item]['contraPartida_costo']['cuenta'];
				// $contraCosto = $arrayCuentasItems[$id_item]['contraPartida_costo']['cuenta'];

				// SI EL TIQUET NO SE PAGA  COMO UN CHEQUE CUENTA, ENTONCES SE CAUSA NORMAL, DE OTRO MODO SOLO EL COSTO POR QUE LA CAUSACION DE LOS PRODUCTOS SE HACE DESDE EL PMS (SE ENVIA EL CARGO A LA HABITACION)
				// SI TIENE CUENTA DE INGRESO O PRECIO, AGREGAR AL ARRAY DE CUENTAS
				if ($this->causaIngreso==false) {
					// CUENTAS DEL ITEM CONFIGURADAS (INGRESO)
					$cuentaPrecio   = ($this->cuenta_ingreso_colgaap<>'')? $this->cuenta_ingreso_colgaap : $arrayCuentasItems[$id_item]['precio']['cuenta'];
					$contraPrecio   = $arrayCuentasItems[$id_item]['contraPartida_precio']['cuenta'];
					$cuentaImpuesto = ($arrayResult['cuenta_iva'] > 0)? $arrayResult['cuenta_iva']: $arrayCuentasItems[$id_item]['impuesto']['cuenta'];

					if($cuentaPrecio > 0){
						$estado = $arrayCuentasItems[$id_item]['precio']['estado'];

						// echo json_encode( array('status' => true, ));
						//ARRAY ASIENTO CONTABLE
						if($arrayAsiento[$cuentaPrecio][$estado] > 0){ $arrayAsiento[$cuentaPrecio][$estado] += ROUND($arrayResult['precio'],$this->decimalesMoneda); }
						else{ $arrayAsiento[$cuentaPrecio][$estado] = ROUND($arrayResult['precio'],$this->decimalesMoneda); }

						$arrayAsiento[$cuentaPrecio]['idCcos'] = $this->idCcos;

						$arrayGlobalEstado[$estado] += ROUND($arrayResult['precio'],$this->decimalesMoneda);
						$arrayItemEstado[$estado]   += ROUND($arrayResult['precio'],$this->decimalesMoneda);
						$acumSubtotal               += ROUND($arrayResult['precio'],$this->decimalesMoneda);

						//===================================== CALC IMPUESTO ========================================//
						if($cuentaImpuesto > 0 && $arrayResult['impuesto'] > 0){
							$estado = $arrayCuentasItems[$id_item]['impuesto']['estado'];

							//ARRAY ASIENTO CONTABLE
							if($arrayAsiento[$cuentaImpuesto][$estado] > 0){ $arrayAsiento[$cuentaImpuesto][$estado] += ROUND($arrayResult['impuesto'],$this->decimalesMoneda); }
							else{ $arrayAsiento[$cuentaImpuesto][$estado] = ROUND($arrayResult['impuesto'],$this->decimalesMoneda); }
							$arrayAsiento[$cuentaImpuesto]['idCcos'] = 0;

							$arrayGlobalEstado[$estado] += ROUND($arrayResult['impuesto'],$this->decimalesMoneda);
							$arrayItemEstado[$estado]   += ROUND($arrayResult['impuesto'],$this->decimalesMoneda);
							$acumImpuesto               += ROUND($arrayResult['impuesto'],$this->decimalesMoneda);

							// $arrayGlobalEstado[$estado] += $arrayResult['impuesto'];
							// $arrayItemEstado[$estado]   += $arrayResult['impuesto'];
							// $acumImpuesto               += $arrayResult['impuesto'];

						}

						//============================== CALC CONTRA PARTIDA PRECIO =================================//
						if($contraPrecio > 0){
							$arrayAsiento[$contraPrecio]['type'] = 'cuentaPago';
							$estado = $arrayCuentasItems[$id_item]['contraPartida_precio']['estado'];

							$contraSaldo = ($arrayItemEstado['debito'] > $arrayItemEstado['credito'])? $arrayItemEstado['debito'] - $arrayItemEstado['credito']
											:  $arrayItemEstado['credito'] - $arrayItemEstado['debito'];

							//ARRAY ASIENTO CONTABLE
							if($arrayAsiento[$contraPrecio][$estado] > 0){ $arrayAsiento[$contraPrecio][$estado] += $contraSaldo; }
							else{ $arrayAsiento[$contraPrecio][$estado] = $contraSaldo; }
							// $arrayAsiento[$contraPrecio]['idCcos'] = 0;

							$arrayGlobalEstado[$estado] += $contraSaldo;
							$arrayItemEstado[$estado]   += $contraSaldo;

							$acumCuentaClientes   = $contraPrecio;
							$estadoCuentaClientes = $estado;
						}
					}
					// SI NO TIENE CUENTA DE PRECIO Y NO ES INVENTARIABLE ENTONCES RETORNA ERROR POR QUE NO TIENE CUENTA CONFIGURADA
					else if($arrayResult['inventariable'] == 'false'){
						// $this->rollBack($this->id_documento,1);
						return array('status' => false, 'detalle'=> "El item Codigo $arrayResult[codigo] No se ha configurado en la contabilizacion" );
					}
				}

				// SI EL ITEM ESTA CONFIGURADO COMO INVENTARIABLE, ACUMULAR LAS CUENTAS DE COSTOS
				if( $cuentaCosto > 0 && $contraCosto > 0 ){

					$estado = $arrayCuentasItems[$id_item]['costo']['estado'];

					//ARRAY ASIENTO CONTABLE
					if($arrayAsiento[$cuentaCosto][$estado] > 0){ $arrayAsiento[$cuentaCosto][$estado] += $arrayResult['costo']; }
					else{ $arrayAsiento[$cuentaCosto][$estado] = $arrayResult['costo']; }
					$arrayAsiento[$cuentaCosto]['idCcos'] =  0;

					$arrayGlobalEstado[$estado] += $arrayResult['costo'];
					$arrayItemEstado[$estado]   += $arrayResult['costo'];

					//ARRAY ASIENTO CONTABLE
					$estado = $arrayCuentasItems[$id_item]['contraPartida_costo']['estado'];
					if($arrayAsiento[$contraCosto][$estado] > 0){ $arrayAsiento[$contraCosto][$estado] += $arrayResult['costo']; }
					else{ $arrayAsiento[$contraCosto][$estado] = $arrayResult['costo']; }
					$arrayAsiento[$contraCosto]['idCcos'] = ($this->cuentasPago['id_centro_costos']<>'')? $this->cuentasPago['id_centro_costos'] : $this->idCcos;

					$arrayGlobalEstado[$estado] += $arrayResult['costo'];
					$arrayItemEstado[$estado]   += $arrayResult['costo'];

				}
				// SI EL ITEM ES INVENTARIABLE Y NO TIENE LAS CUENTAS DE COSTOS REQUERIDAS, ENTONCES RETORNAR ERROR
				else if($arrayResult['inventariable'] == 'true'){
					// $this->rollBack($this->id_documento,1);
					return  array('status' => false, 'detalle'=> "El item Codigo $arrayResult[codigo] No se ha configurado el manejo del costo en la contabilizacion" );

				}
				// echo json_encode($arrayResult);
			} // END FOREACH
			return $arrayAsiento;
        } // END FUNCTION

        /**
         * setNiifAccounts Crear array con las cuentas contables y sus valores segun los items del tiquet
         * @param Array $arrayCuentasPago   Array con las formas de pago del tiquet de venta
         * @param Array $arraycuentaPropina Array con las cuentas de la propina si se aplico
         * @param Array $arrayItems         Array con todos los items de tiquet de venta
         * @param Array $arrayCuentasItems  Array con las cuentas de los items del itquet
         */
        public function setNiifAccounts($arrayCuentasPago,$arraycuentaPropina,$arrayItems,$arrayCuentasItems){
        	$arrayAsiento = $this->aditionalPayments['niif'];
			// SI ES CHEQUE CUENTA,CORTESIA NO SE GENERA INGRESO NI MOVIMIENTO DE DINERO EN BANCO O CAJA
			if ($this->causaIngreso==false) {
				// ASIGNAR LAS CUENTAS DE PAGO
				// LA NATURALEZA DE LAS CUENTAS DE PAGO SIEMPRE VAN A SER DEBITO
				foreach ($arrayCuentasPago as $cuenta => $arrayResult) {
					$arrayAsiento[$cuenta]['debito'] += ROUND($arrayResult['valor'],$this->decimalesMoneda);
				}

				// RECORRER LAS CUENTAS DE LA PROPINA
				// LA NATURALEZA DE LAS PROPINAS SIEMPRE SON CREDITO
				if ($this->valor_propina>0) {

					foreach ($arraycuentaPropina as $cuenta => $cuenta_niif) {
						$arrayAsiento[$cuenta]['credito'] += ROUND($this->valor_propina,$this->decimalesMoneda);
					}
				}
			}
			// print_r($arrayCuentasItems);
    		// CREAR ARRAY CON LAS CUENTAS A CAUSAR CON LAS CUENTAS DE LOS ITEMS Y SUS VALORES
    		foreach ($arrayItems['items'] AS $arrayResult) {
    			// VARIABLES ACUMULADAS GLOBALES
				$arrayItemEstado['debito']  = 0;
				$arrayItemEstado['credito'] = 0;
				$id_item    = $arrayResult['id_item'];

				// CUENTAS DEL ITEM CONFIGURADAS (COSTO)
				$cuentaCosto = $arrayCuentasItems[$id_item]['costo']['cuenta'];
				$contraCosto = ($this->cuentasPago['cuenta_costo_niif']<>'')? $this->cuentasPago['cuenta_costo_niif'] : $arrayCuentasItems[$id_item]['contraPartida_costo']['cuenta'];
				// $contraCosto = $arrayCuentasItems[$id_item]['contraPartida_costo']['cuenta'];

				// SI EL TIQUET NO SE PAGA  COMO UN CHEQUE CUENTA, ENTONCES SE CAUSA NORMAL, DE OTRO MODO SOLO EL COSTO POR QUE LA CAUSACION DE LOS PRODUCTOS SE HACE DESDE EL PMS (SE ENVIA EL CARGO A LA HABITACION)
				// SI TIENE CUENTA DE INGRESO O PRECIO, AGREGAR AL ARRAY DE CUENTAS
				if ($this->causaIngreso==false) {
					// CUENTAS DEL ITEM CONFIGURADAS (INGRESO)
					$cuentaPrecio   = ($this->cuenta_ingreso_niif<>'')? $this->cuenta_ingreso_niif :$arrayCuentasItems[$id_item]['precio']['cuenta'];
					$contraPrecio   = $arrayCuentasItems[$id_item]['contraPartida_precio']['cuenta'];
					$cuentaImpuesto = ($arrayResult['cuenta_iva'] > 0)? $arrayResult['cuenta_iva']: $arrayCuentasItems[$id_item]['impuesto']['cuenta'];

					if($cuentaPrecio > 0){
						$estado = $arrayCuentasItems[$id_item]['precio']['estado'];

						// echo json_encode( array('status' => true, ));
						//ARRAY ASIENTO CONTABLE
						if($arrayAsiento[$cuentaPrecio][$estado] > 0){ $arrayAsiento[$cuentaPrecio][$estado] += ROUND($arrayResult['precio'],$this->decimalesMoneda); }
						else{ $arrayAsiento[$cuentaPrecio][$estado] = ROUND($arrayResult['precio'],$this->decimalesMoneda); }

						$arrayAsiento[$cuentaPrecio]['idCcos'] = $this->idCcos;

						$arrayGlobalEstado[$estado] += ROUND($arrayResult['precio'],$this->decimalesMoneda);
						$arrayItemEstado[$estado]   += ROUND($arrayResult['precio'],$this->decimalesMoneda);
						$acumSubtotal               += ROUND($arrayResult['precio'],$this->decimalesMoneda);

						//===================================== CALC IMPUESTO ========================================//
						if($cuentaImpuesto > 0 && $arrayResult['impuesto'] > 0){
							$estado = $arrayCuentasItems[$id_item]['impuesto']['estado'];

							//ARRAY ASIENTO CONTABLE
							if($arrayAsiento[$cuentaImpuesto][$estado] > 0){ $arrayAsiento[$cuentaImpuesto][$estado] += ROUND($arrayResult['impuesto'],$this->decimalesMoneda); }
							else{ $arrayAsiento[$cuentaImpuesto][$estado] = ROUND($arrayResult['impuesto'],$this->decimalesMoneda); }
							$arrayAsiento[$cuentaCosto]['idCcos'] = ($this->cuentasPago['id_centro_costos']<>'')? $this->cuentasPago['id_centro_costos'] : 0;
							// $arrayAsiento[$cuentaImpuesto]['idCcos'] = 0;

							$arrayGlobalEstado[$estado] += ROUND($arrayResult['impuesto'],$this->decimalesMoneda);
							$arrayItemEstado[$estado]   += ROUND($arrayResult['impuesto'],$this->decimalesMoneda);
							$acumImpuesto               += ROUND($arrayResult['impuesto'],$this->decimalesMoneda);

							// $arrayGlobalEstado[$estado] += $arrayResult['impuesto'];
							// $arrayItemEstado[$estado]   += $arrayResult['impuesto'];
							// $acumImpuesto               += $arrayResult['impuesto'];

						}

						//============================== CALC CONTRA PARTIDA PRECIO =================================//
						if($contraPrecio > 0){
							$arrayAsiento[$contraPrecio]['type'] = 'cuentaPago';
							$estado = $arrayCuentasItems[$id_item]['contraPartida_precio']['estado'];



							$contraSaldo = ($arrayItemEstado['debito'] > $arrayItemEstado['credito'])? $arrayItemEstado['debito'] - $arrayItemEstado['credito']
											:  $arrayItemEstado['credito'] - $arrayItemEstado['debito'];

							//ARRAY ASIENTO CONTABLE
							if($arrayAsiento[$contraPrecio][$estado] > 0){ $arrayAsiento[$contraPrecio][$estado] += $contraSaldo; }
							else{ $arrayAsiento[$contraPrecio][$estado] = $contraSaldo; }
							// $arrayAsiento[$contraPrecio]['idCcos'] = 0;

							$arrayGlobalEstado[$estado] += $contraSaldo;
							$arrayItemEstado[$estado]   += $contraSaldo;

							$acumCuentaClientes   = $contraPrecio;
							$estadoCuentaClientes = $estado;
						}
					}
					// SI NO TIENE CUENTA DE PRECIO Y NO ES INVENTARIABLE ENTONCES RETORNA ERROR POR QUE NO TIENE CUENTA CONFIGURADA
					else if($arrayResult['inventariable'] == 'false'){
						// $this->rollBack($this->id_documento,1);
						return array('status' => false, 'detalle'=> "El item Codigo $arrayResult[codigo] No se ha configurado en la contabilizacion" );
					}
				}

				// SI EL ITEM ESTA CONFIGURADO COMO INVENTARIABLE, ACUMULAR LAS CUENTAS DE COSTOS
				if( $cuentaCosto > 0 && $contraCosto > 0 ){

					$estado = $arrayCuentasItems[$id_item]['costo']['estado'];

					//ARRAY ASIENTO CONTABLE
					if($arrayAsiento[$cuentaCosto][$estado] > 0){ $arrayAsiento[$cuentaCosto][$estado] += $arrayResult['costo']; }
					else{ $arrayAsiento[$cuentaCosto][$estado] = $arrayResult['costo']; }
					$arrayAsiento[$cuentaCosto]['idCcos'] = 0;

					$arrayGlobalEstado[$estado] += $arrayResult['costo'];
					$arrayItemEstado[$estado]   += $arrayResult['costo'];

					//ARRAY ASIENTO CONTABLE
					$estado = $arrayCuentasItems[$id_item]['contraPartida_costo']['estado'];
					if($arrayAsiento[$contraCosto][$estado] > 0){ $arrayAsiento[$contraCosto][$estado] += $arrayResult['costo']; }
					else{ $arrayAsiento[$contraCosto][$estado] = $arrayResult['costo']; }
					$arrayAsiento[$contraCosto]['idCcos'] = ($this->cuentasPago['id_centro_costos']<>'')? $this->cuentasPago['id_centro_costos'] : $this->idCcos;

					$arrayGlobalEstado[$estado] += $arrayResult['costo'];
					$arrayItemEstado[$estado]   += $arrayResult['costo'];

				}
				// SI EL ITEM ES INVENTARIABLE Y NO TIENE LAS CUENTAS DE COSTOS REQUERIDAS, ENTONCES RETORNAR ERROR
				else if($arrayResult['inventariable'] == 'true'){
					// $this->rollBack($this->id_documento,1);
					return  array('status' => false, 'detalle'=> "El item Codigo $arrayResult[codigo] No se ha configurado el manejo del costo en la contabilizacion" );

				}
				// echo json_encode($arrayResult);
			} // END FOREACH
			return $arrayAsiento;
        } // END FUNCTION

        /**
         * rollback Deshacer los cambios en caso de generarse un error en el proceso
         * @param  Array $params Lista con los parametros necesarios para realizar el rollback
         * @param  Int $params.nivel Numero del nivel a realizar el rollback
         *
         */
        public function rollback($params){
        	if ($params['nivel']==1) {
				$sql   = "UPDATE ventas_pos SET estado='$params[estado]',detalle_estado='$params[detalle_estado]' WHERE id=$this->id_documento ";
				$query = $this->mysql->query($sql);
				$$sql  = "DELETE FROM asientos_colgaap
							WHERE activo=1
								AND id_documento   = $this->id_documento
								AND tipo_documento = 'POS'
								AND id_empresa     = $this->id_empresa
								AND id_sucursal    = $this->id_sucursal
						";
				$query = $this->mysql->query($sql);
				$sql   = "DELETE FROM asientos_niif
							WHERE activo=1
								AND id_documento   = $this->id_documento
								AND tipo_documento = 'POS'
								AND id_empresa     = $this->id_empresa
								AND id_sucursal    = $this->id_sucursal
						";
				$query = $this->mysql->query($sql);
        	}
        	if ($params['nivel']==2) {
        		# code...
        	}
        	// print_r($params);
        	// RETORNAR SIEMPRE SUCCES POR QUE UN ERROR NO DEBE DETENER LA OPERACION
        	echo json_encode(array('status' => true,"consecutivo"=>$this->consecutivo,"cuentas"=>$this->arrayCuentas) );
        	exit; // AL GENERARSE UN ERROR REVERSAR CAMBIOS Y FINALIZAR EL PROCESO
        } // END FUNCTION


	} // END CLASS

?>