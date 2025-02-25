<?php
	error_reporting(E_ERROR | E_PARSE);
	// include 'ClassGlobalFunctions.php';
	/**
	 * Class Cajas clase backend para la administracion de las cajas del pos
	 */
	class Mesas extends ClassGlobalFunctions
	{
		public $id_sucursal;
		public $id_empresa;
		public $mysql;
		public $arrayEstado;

		function __construct($id_sucursal,$id_empresa,$mysql){
			$this->id_sucursal 		= $id_sucursal;
			$this->id_empresa  		= $id_empresa;
			$this->mysql       		= $mysql;
			$this->arrayEstado      = $this->setArrayEstado();

			parent::__construct($id_sucursal,$id_empresa,$mysql);
		}

		/**
		 * getMesas Consultar las mesas configuradas de un restaurante
		 * @param  Int $id_restaurante id del restaurante del cual se consultaran las mesas
		 * @return Array Json con las mesas registradoras configuradas para ese restaurante o mensaje indicando que no tiene configuradas
		 */
		public function getMesas($id_restaurante){
			// CONSULTAR LOS ESTADOS DE LAS CAJAS
			$sql="SELECT
						id,
						nombre,
						color,
						estado
					FROM ventas_pos_mesas_estados
					WHERE activo=1 AND id_empresa=$this->id_empresa";
			$query=$this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)) {
				$arrayEstado[$row['id']] = array( 
													'nombre' => utf8_encode($row['codigo']), 
													'color'  => utf8_encode($row['color']) ,
													'estado' => utf8_encode($row['estado']) 
												);
				$arrayEstadoDisp[$row['estado']] = array( 'id'=>$row['id'], 'nombre' => $row['codigo'], 'color'=>$row['color']  );
			}

			// CONSULTAR LAS CAJAS
			$sql="SELECT
						id,
						codigo,
						nombre,
						estado
					FROM ventas_pos_mesas
					WHERE activo=1
					AND estado='Acitva'
					AND id_empresa=$this->id_empresa
					AND id_seccion=$id_restaurante";
			$query=$this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)) {
				$arrayMesas[$row['id']] = array(
												"codigo" => utf8_encode($row['codigo']),
												"nombre" => utf8_encode($row['nombre']),
												"color"  => utf8_encode($arrayEstadoDisp['disponible']['color']),
												"estado" => utf8_encode($arrayEstadoDisp['disponible']['nombre'])
											);
				// $arrayRest[] = array('id' => $row['id'], 'nombre' => $row['codigo'] );
				$whereIdMesa .= ($whereIdMesa=='')? "id_mesa=$row[id]" : " OR id_mesa=$row[id]" ;
			}

			// CONSULTAR EL ESTADO DE LAS CAJAS
			$sql = "SELECT
						id_mesa,
						nombre_mesa,
						id_estado,
						descripcion,
						estado,
						color_estado,
						fecha_apertura,
						hora_apertura,
						id_usuario_apertura,
						documento_usuario_apertura,
						nombre_usuario_apertura,
						fecha_cierre,
						hora_cierre,
						id_usuario_cierre,
						documento_usuario_cierre,
						nombre_usuario_cierre
					FROM ventas_pos_mesas_cuenta
					WHERE activo=1
					AND ($whereIdMesa)
					AND estado<>'Cerrada' ";
			$query=$this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)) {
				$arrayMesas[$row['id_mesa']]["color"]  = $arrayEstado[$row['id_estado']]["color"];
				$arrayMesas[$row['id_mesa']]["estado"] = $row['estado'];
				// $arrayRest[] = array('id' => $row['id'], 'nombre' => $row['codigo'] );
				// $whereIdMesa .= ($whereIdMesa=='')? "id_mesa=$row[id]" : " OR id_mesa=$row[id]" ;
			}

			// RECORRER LAS MESAS PARA RETORNAR EL JSON
			foreach ($arrayMesas as $id => $result) {
				$arrayRest[] = array(
									'id'     => $id,
									'codigo' => $result['codigo'],
									'nombre' => $result['nombre'],
									'color'  => $result['color'],
									'estado' => $result['estado'],
								);
			}

			if (count($arrayRest)>0) {
				$arrayResult = array('status' => 'success', 'mesas'=> $arrayRest, 'sql'=>$sql);
			}
			else{
				$arrayResult = array('status' => 'failed', 'message'=>'No hay mesas configuradas' );
			}
			echo json_encode($arrayResult);
		}

		/**
		 * getEstadoMesas Consultar los diferentes estados de las mesas
		 * @return Array Json con los diferentes estados de las mesas
		 */
		public function getEstadoMesas(){
			$sql="SELECT
						id,
						nombre,
						color
					FROM ventas_pos_mesas_estados
					WHERE activo=1
					AND id_empresa=$this->id_empresa";
			$query=$this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)) {
				$arrayRest[] = array('id' => $row['id'], 'nombre' => $row['nombre'], 'color' => $row['color'] );
			}
			if (count($arrayRest)>0) {
				$arrayResult = array('status' => 'success', 'estados'=> $arrayRest);
			}
			else{
				$arrayResult = array('status' => 'failed', 'message'=>'No hay estados configuradas para las mesas' );
			}
			echo json_encode($arrayResult);
		}

		/**
		 * getEstadoMesa Consultar el estado de una mesa
		 * @param  Int $id_restaurante id del restaurante de la mesa
		 * @param  Int $id_caja id de la caja donde se realiza la operacion
		 * @param  Int $id_mesa id de de la mesa a verificar el
		 * @return Array Json con el estado de la mesa
		 */
		public function getEstadoMesa($id_restaurante,$id_caja,$id_mesa){

			// CONSULTAR EL ESTADO ACTUAL DE LA MESA
			$sql=" SELECT
						vm.id,
						vm.id_estado,
						vm.descripcion,
						vm.estado,
						vm.color_estado,						
						COUNT(vc.id) as cantidad
					FROM
						ventas_pos_mesas_cuenta AS vm
					LEFT JOIN ventas_pos_mesas_cuenta_comensales vc ON vc.id_cuenta = vm.id AND vc.tipo<>'Huesped'
					WHERE
						vm.activo = 1
					AND vm.id_empresa=$this->id_empresa
					AND vm.id_mesa = $id_mesa
					AND vm.estado<>'Cerrada'
					GROUP BY
						vc.id_cuenta";
			$query=$this->mysql->query($sql);
			if($query) {
				$num_rows = $this->mysql->num_rows($query);
				$disponible = ($num_rows>0)? false : true ;
				$color = ($num_rows>0)? $this->mysql->result($query,0,'color_estado')   : $this->arrayEstado['estado']['disponible']['color'] ;
				$totalComensales = $this->mysql->result($query,0,'cantidad');
				$id_cuenta = $this->mysql->result($query,0,'id');


				$sql="SELECT
							vc.*
						FROM
							ventas_pos_mesas_cuenta_comensales vc
						WHERE
						vc.tipo='Huesped'
						AND vc.activo=1
						AND vc.id_cuenta=$id_cuenta
						";
				$query=$this->mysql->query($sql);
				while ($row=$this->mysql->fetch_array($query)) {
					$arrayHuespedes[] = $row;
				}

				$arrayHuespedes = isset($arrayHuespedes)?$arrayHuespedes:'';
				//
				// ventas_pos_mesas_cuenta_items_recetas
				// DATOS DE LA CUENTA DE LA MESA
				$sql="SELECT
							VPI.id,
							VPI.id_cuenta,
							VPI.id_item,
							VPI.codigo_item,
							VPI.nombre_item,
							VPI.cantidad,
							VPI.cantidad_pendiente,
							VPI.termino,
							VPI.precio,
							VPI.id_impuesto,
							VPI.nombre_impuesto,
							VPI.porcentaje_impuesto,
							VPI.id_comanda,
							VPC.estado AS estado_comanda,
							VPI.observaciones,
							VPI.id_usuario,
							VPI.documento_usuario,
							VPI.usuario,
							VPI.id_comensal
						FROM ventas_pos_mesas_cuenta_items AS VPI LEFT JOIN ventas_pos_comanda AS VPC ON VPC.id=VPI.id_comanda
						WHERE VPI.activo=1 AND VPI.id_empresa=$this->id_empresa AND VPI.id_cuenta=$id_cuenta ";
				$query=$this->mysql->query($sql);
				while ($row=$this->mysql->fetch_array($query)) {
					if ($row['estado_comanda']==3) { continue; }
					//$arrayReceta = '';
					//$sqlIng="SELECT
					//			id,
					//			id_cuenta,
					//			id_cuenta_item,
					//			id_item,
					//			codigo_item,
					//			nombre_item,
					//			cantidad,
					//			observaciones,
					//			id_usuario,
					//			documento_usuario,
					//			usuario
					//		FROM ventas_pos_mesas_cuenta_items_recetas
					//		WHERE activo=1 AND id_empresa=$this->id_empresa AND id_cuenta=$id_cuenta AND id_cuenta_item=$row[id] ";
					//$queryIng=$this->mysql->query($sqlIng);
					//while ($rowIng=$this->mysql->fetch_array($queryIng)) {
					//	$arrayReceta[] = array(
					//								"id_cuenta"         => $rowIng['id_cuenta'],
					//								"id_cuenta_item"    => $rowIng['id_cuenta_item'],
					//								"id"           => $rowIng['id_item'],
					//								"codigo"       => $rowIng['codigo_item'],
					//								"nombre"       => $rowIng['nombre_item'],
					//								"cantidad"          => $rowIng['cantidad'],
					//								"observaciones"     => $rowIng['observaciones'],
					//								"id_usuario"        => $rowIng['id_usuario'],
					//								"documento_usuario" => $rowIng['documento_usuario'],
					//								"usuario"           => $rowIng['usuario'],
					//							);
					//}

					$cantidadTotal = (int)$row['cantidad'] - (int)$row['cantidad_pendiente'];
					$arrayDetail[] = array(
											"id"                  => $row['id'],
											"id_cuenta"           => $row['id_cuenta'],
											"id_item"             => $row['id_item'],
											"codigo_item"         => $row['codigo_item'],
											"nombre_item"         => $row['nombre_item'],
											"cantidad"            => $cantidadTotal,
											"termino"             => $row['termino'],
											"precio"              => $row['precio'],
											"id_impuesto"         => $row['id_impuesto'],
											"nombre_impuesto"     => $row['nombre_impuesto'],
											"porcentaje_impuesto" => $row['porcentaje_impuesto'],
											"observaciones"       => $row['observaciones'],
											"id_comanda" 		  => $row['id_comanda'],
											"comandado"           => (($row['id_comanda']>0)? true : false ),
											//"receta"              => $arrayReceta,
											"id_usuario"          => $row['id_usuario'],
											"documento_usuario"   => $row['documento_usuario'],
											"usuario"             => $row['usuario'],
											"id_comensal"         => $row['id_comensal'],
											);
				}



				$arrayResult = array(
									'status'          => 'success',
									'id_cuenta'       => $id_cuenta,
									'detalle_cuenta'  => $arrayDetail,
									'huespedes'       => $arrayHuespedes,
									'totalComensales' =>$totalComensales,
									'disponible'      => $disponible,
									'color'           => $color,
									'debug'           =>  $sql,
									// 'color'        => "#db5957"
								);
			}
			else{
				$arrayResult = array('status' => 'failed', 'message'=>'Se produjo un error al consultar' );
			}

			echo json_encode($arrayResult);
		}
		/**
		 * setEstadoMesas Establece el estado de las mesas
		 * @param  Array $params Parametros necesarios para la apertura de la cuenta
		 * @return Json          Respuesta de la peticion en formato JSON
		 */
		public function setArrayEstado(){
			$sql="SELECT
						id,
						nombre,
						color,
						estado
					FROM ventas_pos_mesas_estados
					WHERE activo=1 AND id_empresa=$this->id_empresa";

			$query=$this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)) {
				$arrayEstado['estado'][$row['estado']] = array( 'id'=>$row['id'], 'nombre' => $row['codigo'], 'color'=>$row['color']  );
				$arrayEstado['id'][$row['id']] = array( 'id'=>$row['id'], 'nombre' => $row['codigo'], 'color'=>$row['color']  );
			}

			return $arrayEstado;
		}

		/**
		 * openTableAccount Abrir una cuenta a una mesa libre
		 * @param  Array $params Parametros necesarios para la apertura de la cuenta
		 * @return Json          Respuesta de la peticion en formato JSON
		 */
		public function openTableAccount($params){
			$randomico = $this->randomico();

			if(isset($params['id_cuenta'])){
				$sql = "SELECT id FROM ventas_pos_mesas_cuenta WHERE id=".$params['id_cuenta'];
				$deleteQuery=$this->mysql->query("DELETE FROM ventas_pos_mesas_cuenta_comensales WHERE id_cuenta=".$params['id_cuenta']);

			}
			else{

				$sql = "INSERT INTO ventas_pos_mesas_cuenta
					(
						randomico,
						id_mesa,
						nombre_mesa,
						id_estado,
						descripcion,
						estado_mesa,
						color_estado,
						fecha_apertura,
						hora_apertura,
						id_usuario_apertura,
						documento_usuario_apertura,
						nombre_usuario_apertura,
						estado,
						id_empresa
					)
					VALUES
					(
						'$randomico',
						'$params[id_mesa]',
						'$params[nombre_mesa]',
						'2',
						'".$this->arrayEstado['id']['2']['nombre']."',
						'no_disponible',
						'".$this->arrayEstado['id']['2']['color']."',
						'".date("Y-m-d")."',
						'".date("H:i:s")."',
						'$params[id_usuario]',
						'$params[documento_usuario]',
						'$params[nombre_usuario]',
						'Abierta',
						'$this->id_empresa'
					)";

				$query=$this->mysql->query($sql);
				if (!$query) {
					$arrayResult = array('status' => 'failed', 'message'=>'Se produjo un error al abrir la cuenta de la mesa' );
					echo json_encode($arrayResult);
					return;
				}

				$sql = "SELECT id FROM ventas_pos_mesas_cuenta WHERE randomico='$randomico' ";


			}


			$query=$this->mysql->query($sql);
			$id_cuenta = $this->mysql->result($query,0,'id');



			foreach ($params['comensales'] as $key => $arrayResult) {
				if ($arrayResult['cantidad']<=0) { continue; }

				if($arrayResult['tipo']=='Huesped'){


					foreach ($arrayResult['detalle'] as  $valores) {
						$id_reserva         = $valores['id'];
						$numero_reserva     = $valores['numero_reserva'];
						$numero_habitacion  = $valores['numero_habitacion'];
						$id_comensal        = $valores['guest_id'];
						$documento_comensal = $valores['numero_documento'];
						$comensal           = $valores['primer_nombre'].' '.$valores['segundo_nombre'].' '.$valores['primer_apellido'].' '.$valores['segundo_apellido'];

						$sql = "INSERT INTO ventas_pos_mesas_cuenta_comensales
						(
							id_cuenta,
							tipo,
							cantidad,
							id_reserva,
							numero_reserva,
							numero_habitacion,
							id_comensal,
							documento_comensal,
							comensal,
							id_empresa

						)
						VALUES
						(
							'$id_cuenta',
							'$arrayResult[tipo]',
							'$arrayResult[cantidad]',
							'".$id_reserva."',							
							'".$numero_reserva."',
							'".$numero_habitacion."',
							'".$id_comensal."',
							'".$documento_comensal."',
							'".$comensal."',
							'$this->id_empresa'
						)";



						$query=$this->mysql->query($sql);
					}

				}else{
					$sql = "INSERT INTO ventas_pos_mesas_cuenta_comensales
						(
							id_cuenta,
							tipo,
							cantidad,
							id_reserva,
							numero_habitacion,
							id_comensal,
							documento_comensal,
							comensal,
							id_empresa

						)
						VALUES
						(
							'$id_cuenta',
							'$arrayResult[tipo]',
							'$arrayResult[cantidad]',
							'',
							'',
							'',
							'',
							'',
							'$this->id_empresa'
						)";

						$query=$this->mysql->query($sql);
				}


			}

			if (!$query) {
				$arrayResult = array('status' => 'failed', 'message'=>'Se produjo un error al abrir la cuenta de la mesa' );
				// $arrayResult = array('status' => 'success', 'estados'=> $arrayRest);
			}
			else{

				$sql="SELECT
							vc.*
						FROM
							ventas_pos_mesas_cuenta_comensales vc
						WHERE
						vc.tipo='Huesped'
						AND vc.activo=1
						AND vc.id_cuenta=$id_cuenta
						";
				$query=$this->mysql->query($sql);
				while ($row=$this->mysql->fetch_array($query)) {
					$arrayHuespedes[] = $row;
				}

				$arrayHuespedes = isset($arrayHuespedes)?$arrayHuespedes:'';

				$arrayResult = array('status' => 'success', 'id_cuenta'=>$id_cuenta, 'huespedes'=>$arrayHuespedes );
			}
			echo json_encode($arrayResult);
		}

		/**
		 * saveItem Guardar un item a una cuenta de una mesa
		 * @param  Array $params Parametros necesarios para insertar el item a la cuenta
		 * @return Json          Respuesta de la peticion en formato JSON
		 */
		public function saveItem($params){
			$sql="INSERT INTO ventas_pos_mesas_cuenta_items
					(
						id_cuenta,
						id_item,
						codigo_item,
						nombre_item,
						cantidad,
						termino,
						precio,
						id_impuesto,
						nombre_impuesto,
						porcentaje_impuesto,
						observaciones,
						id_usuario,
						documento_usuario,
						usuario,
						id_bodega_produccion,
						id_empresa
					)
					VALUES
					(
						'$params[id_cuenta]',
						'$params[id_item]',
						'$params[codigo]',
						'$params[nombre]',
						'$params[cantidad]',
						'$params[termino]',
						'$params[precio]',
						'$params[id_impuesto]',
						'$params[nombre_impuesto]',
						'$params[porcentaje_impuesto]',
						'$params[observaciones]',
						'$params[id_usuario]',
						'$params[documento_usuario]',
						'$params[nombre_usuario]',
						'$params[id_bodega_produccion]',
						'$this->id_empresa'
					) ";
			$query=$this->mysql->query($sql);
			$insertReceta = '';
			if ($query) {
				$id_cuenta_item = $this->mysql->insert_id();
				foreach ($params['receta'] as $key => $arrayResult) {
					$insertReceta .= "(
											'$params[id_cuenta]',
											'$id_cuenta_item',
											'$arrayResult[id]',
											'$arrayResult[codigo]',
											'$arrayResult[nombre]',
											'$arrayResult[cantidad]',
											'$params[id_usuario]',
											'$params[documento_usuario]',
											'$params[usuario]',
											'$this->id_empresa'
										),";
				}
				if ($insertReceta<>'') {
					$insertReceta = substr($insertReceta,0,-1);
					$sql="INSERT INTO ventas_pos_mesas_cuenta_items_recetas
								(
									id_cuenta,
									id_cuenta_item,
									id_item,
									codigo_item,
									nombre_item,
									cantidad,
									id_usuario,
									documento_usuario,
									usuario,
									id_empresa
								)
							VALUES $insertReceta";
					$query=$this->mysql->query($sql);
					if ($query) {
						$arrayResult = array('status' => 'success', 'id_cuenta_item'=> $id_cuenta_item);
					}
					else{
						$arrayResult = array('status' => 'failed', 'message'=>'Se produjo un error al insertar la receta del item');
					}
				}
				else{
					$arrayResult = array('status' => 'success', 'id_cuenta_item'=>$id_cuenta_item);
				}

				$sql="UPDATE ventas_pos_mesas_cuenta
					SET id_estado 	  = '2',
						descripcion   = '".$this->arrayEstado['id']['2']['nombre']."',
						estado_mesa   = 'no_disponible',
						color_estado  = '".$this->arrayEstado['id']['2']['color']."'
					WHERE activo=1 AND id_empresa=$this->id_empresa AND id = $params[id_cuenta] AND id_mesa=id_mesa ";
				$query=$this->mysql->query($sql);
				// id
				// codigo
				// nombre,
				// cantidad
				// precio
				// incluido
				# code...
			}
			else{
				$arrayResult = array('status' => 'failed', 'message'=>'Se produjo un error al insertar el item ',"sql"=>$sql);
			}
			echo json_encode($arrayResult);
		}

		/**
		 * deleteItem Eliminar item de la cuenta de la mesa
		 * @param  Int $id_cuenta Id de la cuenta para eliminar el item de la cuenta
		 * @param  Int $id_row    Id de la fila de la bd para eliminar el item de la cuenta
		 * @param  Int $id_item   Id del item para eliminar el item de la cuenta
		 * @return Json           Respuesta de la peticion en formato JSON
		 */
		public function deleteItem($id_cuenta,$id_row,$id_item,$username,$password){
			
			$sql = "SELECT id_comanda FROM ventas_pos_mesas_cuenta_items WHERE id_cuenta=$id_cuenta AND id=$id_row AND id_item=$id_item";
			$query=$this->mysql->query($sql);
			$id_comanda = $this->mysql->result($query,0,'id_comanda');

			if($username!=='' && $password!==''){
				//validar usuario
				$sql="SELECT password,id_rol FROM empleados WHERE username='$username'";
				$query=$this->mysql->query($sql);
				$passBD = $this->mysql->result($query,0,'password');
				$id_rol = $this->mysql->result($query,0,'id_rol');
				
				if($passBD !== md5($password)){
					$arrayResult = array('status' => 'failed', 'message'=>"Credenciales incorrectas");
					echo json_encode($arrayResult);
					return;
				}

				$sql="SELECT id FROM empleados_roles_permisos WHERE id_permiso = '247' AND id_rol='$id_rol'";
				$query=$this->mysql->query($sql);
				$numItems = $this->mysql->num_rows($query);
				if($numItems<=0){
					//si no tiene permisos
					$arrayResult = array('status' => 'failed', 'message'=>"El usuario no tiene permisos válidos", "debug"=>$sql);
					echo json_encode($arrayResult);
					return;
				}

				$sql="DELETE FROM ventas_pos_mesas_cuenta_items WHERE id_cuenta=$id_cuenta AND id=$id_row AND id_item=$id_item";
				$query=$this->mysql->query($sql);
				if ($query) {
					$sql="DELETE FROM ventas_pos_mesas_cuenta_items_recetas WHERE id_cuenta=$id_cuenta AND id_cuenta_item=$id_row ";
					$query=$this->mysql->query($sql);
					if ($query) {
						$arrayResult = array('status' => 'success', 'message'=>"se elimino correctamente","sql"=>$username." ".$password);
					}
					else{
						$arrayResult = array('status' => 'failed', 'message'=>"se produjo un error al eliminar la receta del item","sql"=>$sql);
					}
				}
				else{
					$arrayResult = array('status' => 'failed', 'message'=>"se produjo un error al eliminar el item","sql"=>$sql);
				}

				$sql = "SELECT id FROM ventas_pos_mesas_cuenta_items WHERE id_comanda = $id_comanda";
				$query=$this->mysql->query($sql);
				$numItems = $this->mysql->num_rows($query);
				if($numItems<=0){
					$sql   = "UPDATE
									ventas_pos_comanda
								SET estado                  = 3,
									id_usuario_anulacion        = '$_SESSION[IDUSUARIO]',
									documento_usuario_anulacion = '$_SESSION[CEDULAFUNCIONARIO]',
									usuario_anulacion           = '$_SESSION[NOMBREFUNCIONARIO]',
									fecha_anulacion             = '".date("Y-m-d")."',
									hora_anulacion              = '".date("H:i:s")."',
									observacion_anulacion       = 'Comanda anulada desde POS'
								WHERE activo=1
								AND id_empresa=$this->id_empresa
								AND id=$id_comanda ";
						$query = $this->mysql->query($sql);
						if ($query) {
							$arrayResult = array('status' => "success", "message"=> "Comanda anulada correctamente" );
						}
						else{
							$arrayResult = array('status' => "failed", "message"=> "No se pudo anular la comanda ","debug"=>$sql );
						}
				}
				echo json_encode($arrayResult);
				return;
			}

			$sql="DELETE FROM ventas_pos_mesas_cuenta_items WHERE id_cuenta=$id_cuenta AND id=$id_row AND id_item=$id_item";
			$query=$this->mysql->query($sql);
			if ($query) {
				$sql="DELETE FROM ventas_pos_mesas_cuenta_items_recetas WHERE id_cuenta=$id_cuenta AND id_cuenta_item=$id_row ";
				$query=$this->mysql->query($sql);
				if ($query) {
					$arrayResult = array('status' => 'success', 'message'=>"se elimino correctamente","sql"=>$username." ".$password);
				}
				else{
					$arrayResult = array('status' => 'failed', 'message'=>"se produjo un error al eliminar la receta del item","sql"=>$sql);
				}
			}
			else{
				$arrayResult = array('status' => 'failed', 'message'=>"se produjo un error al eliminar el item","sql"=>$sql);
			}

			echo json_encode($arrayResult);
		}

		/**
		 * solicitarPedido Generar comanda para los items pendientes a pedir
		 * @param  Array $params Parametros necesarios para solicitar el pedido de la cuenta
		 * @return Json           Respuesta de la peticion en formato JSON
		 */
		public function solicitarPedido($params){
			$sql="SELECT id
					FROM ventas_pos_mesas_cuenta_items
					WHERE activo=1 AND id_empresa=$this->id_empresa AND id_cuenta = $params[id_cuenta] AND (id_comanda='' OR id_comanda=0 OR ISNULL(id_comanda) ) ";
			$query=$this->mysql->query($sql);
			$numItems = $this->mysql->num_rows($query);
			if ($numItems<=0) {
				$arrayResult = array('status' => 'failed', 'message' => 'No hay items a comandar', "sql"=>$sql);
				echo json_encode($arrayResult);
				return;
			}

			$randomico = $this->randomico();
			$sql="INSERT INTO ventas_pos_comanda
					(
						id_cuenta,
						fecha,
						hora,
						randomico,
						id_empresa,
						id_usuario,
						documento_usuario,
						usuario,
						id_caja,
						nombre_caja,
						estado
					)
					VALUES
					(
						'$params[id_cuenta]',
						'".(date("Y-m-d"))."',
						'".(date("H:i:s"))."',
						'$randomico',
						'$this->id_empresa',
						'$params[id_usuario]',
						'$params[documento_usuario]',
						'$params[usuario]',
						'$params[id_caja]',
						'$params[nombre_caja]',
						'1'
					)";
			$query=$this->mysql->query($sql);
			if (!$query) {
				$arrayResult = array('status' => 'failed', 'message' => 'No se creo la comanda', "sql"=>$sql);
				echo json_encode($arrayResult);
				return;
			}
			$sql = "SELECT id FROM ventas_pos_comanda WHERE randomico='$randomico' ";
			$query=$this->mysql->query($sql);
			$id_comanda = $this->mysql->result($query,0,'id');
			if ($id_comanda=='' || $id_comanda==0) {
				$arrayResult = array('status' => 'failed', 'message' => 'No se pudo obtener el id de la comanda', "sql"=>$sql);
				echo json_encode($arrayResult);
				return;
			}

			$sql="UPDATE ventas_pos_mesas_cuenta_items
					SET id_comanda=$id_comanda
				WHERE activo=1 AND id_empresa=$this->id_empresa AND id_cuenta = $params[id_cuenta] AND (id_comanda='' OR id_comanda=0 OR ISNULL(id_comanda) ) ";
			$query=$this->mysql->query($sql);
			if (!$query) {
				$arrayResult = array('status' => 'failed', 'message' => 'No se pudo asignar la comanda a los items', "sql"=>$sql);
				echo json_encode($arrayResult);
				return;
			}

			$sql="UPDATE ventas_pos_mesas_cuenta
					SET id_estado 	  = '3',
						descripcion   = '".$this->arrayEstado['id']['3']['nombre']."',
						estado_mesa   = 'no_disponible',
						color_estado  = '".$this->arrayEstado['id']['3']['color']."'
					WHERE activo=1 AND id_empresa=$this->id_empresa AND id = $params[id_cuenta] AND id_mesa=id_mesa ";
			$query=$this->mysql->query($sql);

			$arrayResult = array('status' => 'success', 'id_comanda'=>$id_comanda,"sql"=>$sql);
			echo json_encode($arrayResult);
		}

		/**
		 * printComanda Imprimir la comanda
		 * @param  Int $id_comanda Id de la comanda a imprimir
		 * @return [type]             [description]
		 */
		public function printComanda($id_comanda){
			if(file_exists("../../../../ARCHIVOS_PROPIOS/empresa_".$_SESSION['ID_HOST']."/panel_de_control/formato_documentos/formato_factura.php")){
				include("../../../../ARCHIVOS_PROPIOS/empresa_".$_SESSION['ID_HOST']."/panel_de_control/formato_documentos/formato_factura.php");
			}
			else{
				$sql="SELECT
						id_cuenta,
						fecha,
						hora,
						randomico,
						id_empresa,
						id_usuario,
						documento_usuario,
						usuario,
						nombre_caja,
						estado
					FROM ventas_pos_comanda WHERE activo=1 AND id_empresa=$this->id_empresa AND id=$id_comanda";
				$query=$this->mysql->query($sql);
				$id_cuenta   = $this->mysql->result($query,0,'id_cuenta');
				$fecha       = $this->mysql->result($query,0,'fecha');
				$hora        = $this->mysql->result($query,0,'hora');
				$usuario     = $this->mysql->result($query,0,'usuario');
				$nombre_caja = $this->mysql->result($query,0,'nombre_caja');
				$estado      = $this->mysql->result($query,0,'estado');

				$sql="SELECT
							VPC.id_mesa,
							VPC.nombre_mesa,
							VPM.seccion
						FROM
							ventas_pos_mesas_cuenta AS VPC
						INNER JOIN ventas_pos_mesas AS VPM ON VPM.id = VPC.id_mesa
						WHERE
							VPC.activo = 1
						AND VPC.id_empresa = $this->id_empresa
						AND VPC.id = $id_cuenta";
				$query=$this->mysql->query($sql);
				$id_mesa     = $this->mysql->result($query,0,'id_mesa');
				$nombre_mesa = $this->mysql->result($query,0,'nombre_mesa');
				$seccion     = $this->mysql->result($query,0,'seccion');

				$sql="SELECT
								IP.nombre_item,
								IP.cantidad,
								IP.termino,
								IP.observaciones,
								I.grupo
							FROM
								ventas_pos_mesas_cuenta_items AS IP
							INNER JOIN items AS I ON I.id = IP.id_item
							WHERE
								IP.activo = 1
							AND IP.id_empresa = $this->id_empresa
							AND IP.id_comanda = $id_comanda";
				$query=$this->mysql->query($sql);
				while ($row=$this->mysql->fetch_array($query)) {
					$arrayItems[$row['grupo']][]  = array(
														'nombre_item'   => $row['nombre_item'],
														'cantidad'      => $row['cantidad'],
														'termino'       => $row['termino'],
														'observaciones' => $row['observaciones'],
														'grupo'         => $row['grupo'],
													);

				}
				// print_r($arrayItems); exit;
				foreach ($arrayItems as $grupo => $arrayItemsResult) {
					$bodyTable .= "<thead>
									<tr>
										<td colspan='2' ><b>$grupo</b></td>
									</tr>
									</thead>";
					foreach ($arrayItemsResult as $key => $arrayResult) {
						$contentTermino = ($arrayResult['termino']<>'')?
											"<tr>
												<td>&nbsp;</td>
												<td>$arrayResult[termino]</td>
											</tr>" : "" ;
						$contentObservacion = ($arrayResult['observaciones']<>'')?
											"<tr>
												<td colspan='2' >$arrayResult[observaciones]</td>
											</tr>" : "" ;
						$bodyTable .= "<tr>
											<td><b>$arrayResult[cantidad]</b></td>
											<td>$arrayResult[nombre_item]</td>
										</tr>$contentTermino $contentObservacion";
					}

				}
				$contenido = "<style>
									body{
										font-family: -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,sans-serif;
									}
									.title{
										font-weight   : bold;
										font-size     : 12px;
										width         : 100%;
										text-align    : center;
										margin-bottom : 5px;
									}
									table {
										font-size : 11px;
										width     : 100%;
									}
									.separator{
										border-top     : 1px solid #CCC;
										padding-bottom : 5px;
									}
									.footer {
										width      : 100%;
										font-size  : 11px;
										font-style : italic;
										padding-top : 60px;
									}
								</style>
								<div>
									<div class='title' >
										COMANDA <br> No. $id_comanda
									</div>
									<table>
										<thead>
											<tr>
												<td><b>Ambiente</b></td>
												<td>$seccion</td>
											</tr>
											<tr>
												<td><b>Caja</b></td>
												<td>$nombre_caja</td>
											</tr>
											<tr>
												<td><b>Mesa</b></td>
												<td>$nombre_mesa</td>
											</tr>
											<tr>
												<td><b>Usuario</b></td>
												<td>$usuario</td>
											</tr>
											<tr>
												<td colspan='2' class='separator' >&nbsp;</td>
											</tr>
										<thead>
									</table>
									<table>
										$bodyTable
									</table>
									<div class='footer' >
										".($this->fecha_larga($fecha))." - $hora
									</div>
								</div>";
								// ".($this->fecha_larga($fecha))."
				include("../misc/MPDF54/mpdf.php");

				echo $id_comanda;
				// $mpdf = new mPDF(
				// 	"utf-8",  						// mode - default "
				// 	strtoupper($options["tamano"]),	// format - A4, for example, default "
				// 	12,								// font size - default 0
				// 	"",								// default font family
				// 	$options["margins"]["left"],	// margin_left
				// 	$options["margins"]["right"],	// margin right
				// 	$options["margins"]["top"],		// margin top
				// 	$options["margins"]["bottom"],	// margin bottom
				// 	3,								// margin header
				// 	10,								// margin footer
				//     $orientacion    				// orientacion
				// );
				$mpdf = new mPDF(
					"utf-8",  						// mode - default "
					'A6.5',	// format - A4, for example, default "
					12,								// font size - default 0
					"",								// default font family
					'10', // margin_left
					'10', // margin right
					'10', // margin top
					'10', // margin bottom
					3,								// margin header
					10,								// margin footer
				    $orientacion    				// orientacion
				);
				if ($estado==3) {
					$mpdf->SetWatermarkText('ANULADA');
					$mpdf->showWatermarkText = true;
				}
				$mpdf->SetAutoPageBreak(TRUE, 15);
				$mpdf->SetTitle ("Comanda POS");
				$mpdf->SetAuthor ( "LOGICALSOFT-POS" );
				$mpdf->SetDisplayMode ( "fullpage" );
				$mpdf->SetHeader("");
				// $mpdf->SetFooter("$fecha $hora ");
				$mpdf->WriteHTML(utf8_encode($contenido));
				$mpdf->Output("comanda_$id_comanda.pdf","I");
				// if($options["op"]=="view"){$mpdf->Output($nombre.".pdf","I");}
				// if($options["op"]=="download"){$mpdf->Output($nombre.".pdf","D");}
			}
		}

		/**
		 * printPrecuenta Imprimir la precuenta
		 * @param  Int $id_cuenta Id de la comanda a imprimir
		 * @return [type]             [description]
		 */
		public function printPrecuenta($id_cuenta){
			if(file_exists("../../../../ARCHIVOS_PROPIOS/empresa_".$_SESSION['ID_HOST']."/panel_de_control/formato_documentos/formato_factura.php")){
				include("../../../../ARCHIVOS_PROPIOS/empresa_".$_SESSION['ID_HOST']."/panel_de_control/formato_documentos/formato_factura.php");
			}
			else{
				date_default_timezone_set("America/Bogota");

				if ($_SERVER['REQUEST_METHOD'] === 'POST') {

					$params = json_decode($_POST['data'], true);
					$datosEmpresa = $this->getInfoEmpresa();
					//
					$sql="SELECT
							nombre_mesa
						FROM ventas_pos_mesas_cuenta WHERE activo=1 AND id_empresa=$this->id_empresa AND id=".$params['items'][0]['id_cuenta'];
					$query=$this->mysql->query($sql);
					$nombre_mesa = $this->mysql->result($query,0,'nombre_mesa');

					$groupItems = array();

					foreach ( $params['items'] as $value ) {
						$id = $value['id_item'].$value['precio'];
						if(array_key_exists($id,$groupItems)){
							//Si el key existe, aumenta la cantidad
							$groupItems[$id]['cantidad']++;
						}
						//Si el key no existe, reinicia la cantidad
						else{
							$value['cantidad']=1;
							$groupItems[$id] = $value;
						}

					}

					foreach($groupItems as $items){
						$cantidad 		= $items['cantidad'];
						$taxPercent 	= ($items['porcentaje_impuesto']*0.01)+1;
						$descuento 		= ($params['totales']['descuentoData'])?(1-$params['totales']['descuentoData']['porcentaje']/100):1;
						$subtotal   	= ROUND(($descuento*$cantidad*$items['precio']/$taxPercent),0);
						if ($items['porcentaje_impuesto']>0) {
							$arrayImpuestos[$items['id_impuesto']]['nombre'] = $items['nombre_impuesto'];
							$arrayImpuestos[$items['id_impuesto']]['valor'] += ($subtotal*$items['porcentaje_impuesto'])/100;
						}

						// impuestos  += ((element.precio/taxPercent)*element.porcentaje_impuesto)/100

						$acumCantidad += $cantidad;
						$acumTotal    += ($cantidad*$items['precio']);
						$bodyTable .= "<tr><td colspan='4' >$items[nombre_item]</td></tr>";
						$bodyTable .= "<tr><td colspan='2' style='text-align:right;' >$items[cantidad]</td><td colspan='2' style='text-align:center;'>".number_format($items['precio'],0 ,',', '.')."</td></tr>";
						$bodyTable .= "<tr class='row' ><td colspan='4' style='text-align:right;'>".number_format($items['cantidad']*$items['precio'],0 ,',', '.')."</td></tr>";
					}

					foreach ($arrayImpuestos as $id => $arrayResult) {
						$bodyImp .= "<tr>
										<td>$arrayResult[nombre]</td>
										<td><b>".number_format($arrayResult['valor'],0 ,',', '.')."</b></td>
									</tr>";
					}

					$bodyTable .= "
									<thead>
										<tr>
											<td>Cant. Total:</td>
											<td>$acumCantidad</td>
											<td>Total:</td>
											<td>".number_format($acumTotal,0 ,',', '.') ."</td>
										</tr>
									</thead>
									";

					$contenido = "<html>
									<head>
									<style>
										@page {
											size: auto;
										}
										body{
											font-family: -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,sans-serif;
										}
										.title{
											font-weight   : bold;
											font-size     : 12px;
											width         : 100%;
											text-align    : center;
											margin-bottom : 5px;
										}
										.subtitle{
											font-size     : 11px;
											width         : 100%;
											text-align    : center;
											margin-bottom : 5px;
										}
										.subtitle span{
											width : 100%;
											font-weight   : bold;

										}

										table {
											font-size : 11px;
											width     : 100%;
										}
										.separator{
											/*border-top     : 1px solid #CCC;*/
											padding-bottom : 5px;
										}

										.productos{
											border-collapse:collapse;
											font-size : 10px;
											margin-bottom : 15px;
										}
										.productos thead td{
											border-top: 1px solid;
											border-bottom: 1px solid;
											padding : 5px;
										}
										.row td{
											border-bottom: 1px solid;
										}

										.condiciones{
											width : 100%;
											font-size:9px;
											margin-bottom : 15px;
										}

										.totales{

										}

										.firmas{
											margin-top: 20px;
											/*margin-bottom: 20px*/
										}
										.firmas td{
											padding-bottom: 25px;
										}

									</style>
									</head>
									<body>
									<div>
										<div class='title' >
											$datosEmpresa[nombre] <br>
											NIT. $datosEmpresa[documento]<br>
											$datosEmpresa[direccion]
										</div>
										<div class='subtitle' >
											<span>*** ATENCION ***</span><br>
												CUENTA INFORMATIVA PARA SU VERIFICACIÓN
												No Válida como FACTURA
												POR FAVOR EXIGA SU FACTURA<br>
											<span>*** GRACIAS POR SU VISITA ***</span>
										</div>

										<table>
											<thead>
												<tr>
													<td><b>Fecha</b></td>
													<td>".($this->fecha_larga(date('Y-m-d')))."</td>
													<td>".date('H:i:s')."</td>
												</tr>
												<tr>
													<td><b>Mesa</b></td>
													<td>$nombre_mesa</td>
												</tr>
												<tr>
													<td colspan='2' class='separator' >&nbsp;</td>
												</tr>
											<thead>
										</table>
										<div class='subtitle' >
											<span>LISTADO DE PRODUCTOS CONSUMIDOS</span>
										</div>
										<table class='productos' >
											<thead>
												<tr>
													<td>Producto</td>
													<td>Cantidad</td>
													<td style='text-align:right;' >V/unit</td>
													<td style='text-align:right;' >Valor</td>
											</thead>
											$bodyTable
										</table>
										<div class='condiciones' >
											Se informa a los consumidores que este
											establecimiento de comercio sugiere una propina
											correspondiente al 10% del valor de la cuenta, el
											cual podra ser aceptado, rechazado o modificado
											por usted, de acuerdo a su valoraciòn del servicio
											prestado.
											Al momento de solicitud la cuenta, indìque a la
											persona que lo atiende que dicho valor sea o no
											incluido en la factura o indìque el valor que quiere
											dar como propina.
											En caso que tenga algùn inconveniente con el
											cobro podra comunicarce con la Lìnea de Atenciòn
											al Ciudadano de la Superintencia de Industria y
											Comercio : 592 0404 en Bogotà, Para el resto del
											paìs, lìnea gratuita nacional: 018000-910165, para
											que radique su queja
										</div>
										<table class='totales'>
											<tr>
												<td>Neto:</td>
												<td><b>".number_format($params['totales']['subtotal'],0 ,',', '.')."</b></td>
											</tr>
											$bodyImp
											<tr>
												<td>Descuento:</td>
												<td><b>".number_format($params['totales']['descuentos'],0 ,',', '.')."</b></td>
											</tr>
											<tr>
												<td>Propina:</td>
												<td><b>".number_format($params['totales']['propina'],0 ,',', '.')."</b></td>
											</tr>
											<tr>
												<td>Total:</td>
												<td><b>".number_format($acumTotal-$params['totales']['descuentos']+$params['totales']['propina'],0 ,',', '.')."</b></td>
											</tr>
										</table>
										<table class='firmas' >
											<tr>
												<td>Nombre:</td>
												<td>_________________________________________</td>
											</tr>
											<tr>
												<td>Firma:</td>
												<td>_________________________________________</td>
											</tr>
										</table>

										<div class='subtitle' >
											<span>GRACIAS POR SU COMPRA</span><br>
											<span>*** NO VALIDA COMO FACTURA***</span>
										</div>
									</div>
									</body>
									</html>
									";
									// ".($this->fecha_larga($fecha))."
									
					include("../misc/MPDF54/mpdf.php");

					// echo $sql; exit;
					// echo $id_comanda;
					// $mpdf = new mPDF(
					// 	"utf-8",  						// mode - default "
					// 	strtoupper($options["tamano"]),	// format - A4, for example, default "
					// 	12,								// font size - default 0
					// 	"",								// default font family
					// 	$options["margins"]["left"],	// margin_left
					// 	$options["margins"]["right"],	// margin right
					// 	$options["margins"]["top"],		// margin top
					// 	$options["margins"]["bottom"],	// margin bottom
					// 	3,								// margin header
					// 	10,								// margin footer
					//     $orientacion    				// orientacion
					// );
					$mpdf = new mPDF(
						"utf-8",  						// mode - default "
						'A6.5',	// format - A4, for example, default "
						12,								// font size - default 0
						"",								// default font family
						'10', // margin_left
						'10', // margin right
						'10', // margin top
						'10', // margin bottom
						3,								// margin header
						10,								// margin footer
					    $orientacion    				// orientacion
					);
					$mpdf->SetAutoPageBreak(TRUE, 15);
					$mpdf->SetTitle ("Precuenta POS");
					$mpdf->SetAuthor ( "LOGICALSOFT-POS" );
					$mpdf->SetDisplayMode ( "fullpage" );
					$mpdf->SetHeader("");
					// $mpdf->SetFooter("$fecha $hora ");
					// $mpdf->WriteHTML(utf8_encode($contenido));
					$mpdf->WriteHTML($contenido);
					$mpdf->Output("comanda_$id_comanda.pdf","I");
					// if($options["op"]=="view"){$mpdf->Output($nombre.".pdf","I");}
					// if($options["op"]=="download"){$mpdf->Output($nombre.".pdf","D");}
				}else{
					http_response_code(405); // Método no permitido
					echo json_encode(['error' => 'Only POST method is allowed']);
				}

			}
		}

		/**
		 * solicitarPedido Generar comanda para los items pendientes a pedir
		 * @param  Array $params Parametros necesarios para la actualizacion de la cuenta
		 * @return Json           Respuesta de la peticion en formato JSON
		 */
		public function changeCuentaMesa($params){
			// id_cuenta
			// id_mesa_origen
			// mesa_origen
			// id_usuario
			// documento_usuario
			// usuario
			// id_mesa_destino
			// mesa_destino
			// VALIDAR QUE LA MESA DE DESTINO ESTE DISPONIBLE
			$sql="SELECT id
					FROM ventas_pos_mesas_cuenta
					WHERE activo  = 1
					AND id_empresa = $this->id_empresa
					AND id_mesa    = $params[mesa_destino]
					AND estado     = 'Abierta'
					";
			$query=$this->mysql->query($sql);
			$num_rows = $this->mysql->num_rows($query);
			if ($num_rows>0) {
				$arrayResult = array('status' => 'failed', 'message' => 'La mesa de destino no se encuentra disponible' );
				echo json_encode($arrayResult);
				return;
			}
			$sql="UPDATE ventas_pos_mesas_cuenta
					SET id_mesa=$params[id_mesa_destino],nombre_mesa='$params[mesa_destino]'
					WHERE activo=1 AND id_empresa=$this->id_empresa AND id_mesa=$params[id_mesa_origen] AND id=$params[id_cuenta]";
			$query=$this->mysql->query($sql);
			if (!$query) {
				$arrayResult = array('status' => 'failed', 'message' => 'Se produjo un error al cambiar la mesa' );
				echo json_encode($arrayResult);
				return;
			}

			$sql="INSERT INTO ventas_pos_mesas_traslados
					(
						id_cuenta,
						fecha,
						hora,
						id_usuario,
						documento_usuario,
						usuario,
						id_mesa_origen,
						mesa_origen,
						id_mesa_destino,
						mesa_destino,
						observacion,
						id_empresa

					)
					VALUES
					(
						'$params[id_cuenta]',
						'".date("Y-m-d")."',
						'".date("H:i:s")."',
						'$params[id_usuario]',
						'$params[documento_usuario]',
						'$params[usuario]',
						'$params[id_mesa_origen]',
						'$params[mesa_origen]',
						'$params[id_mesa_destino]',
						'$params[mesa_destino]',
						'$params[observacion]',
						'$this->id_empresa'
					)
					";
			$query=$this->mysql->query($sql);
			if (!$query) {
				$arrayResult = array('status' => 'failed', 'message' => 'Se produjo un error al insertar el log del cambio','sql'=>$sql );
				echo json_encode($arrayResult);
				return;
			}

			$arrayResult = array('status' => 'success', 'message' => 'registro exitoso' );
			echo json_encode($arrayResult);
		}

		/**
		 * closeMesa Cerrar una mesa
		 * @param  Array $params Parametros necesarios para la actualizacion de la mesa
		 * @return Json           Respuesta de la peticion en formato JSON
		 */
		public function closeMesa($params){
			$sqlMesa="SELECT id
					FROM ventas_pos_mesas_cuenta
					WHERE activo  = 1
					AND id_empresa = $this->id_empresa
					AND id_mesa    = $params[id_mesa]
					AND estado     = 'Abierta'
					";
			$query=$this->mysql->query($sqlMesa);
			$id_cuenta = $this->mysql->result($query,0,'id');

			$sql="SELECT
							VPI.id,
							VPI.id_cuenta,
							VPI.id_item,
							VPI.codigo_item,
							VPI.nombre_item,
							VPI.cantidad,
							VPI.cantidad_pendiente,
							VPI.termino,
							VPI.precio,
							VPI.id_impuesto,
							VPI.nombre_impuesto,
							VPI.porcentaje_impuesto,
							VPI.id_comanda,
							VPC.estado AS estado_comanda,
							VPI.observaciones,
							VPI.id_usuario,
							VPI.documento_usuario,
							VPI.usuario
						FROM ventas_pos_mesas_cuenta_items AS VPI LEFT JOIN ventas_pos_comanda AS VPC ON VPC.id=VPI.id_comanda
						WHERE VPI.activo=1 AND VPI.id_empresa=$this->id_empresa AND VPI.id_cuenta=$id_cuenta AND VPC.estado=1";
			$query=$this->mysql->query($sql);
			$cont=0;
			while ($row=$this->mysql->fetch_array($query)) {
				if ($row['estado_comanda']==3) { continue; }
				$cont++;
			}
			if ($cont>0) {
				$arrayResult = array('status' => 'failed', 'message' => 'La mesa no se puede cerrar por que tiene items pendientes, eliminelos y si ya fueron comandados entonces elimine las comandas', 'debug'=>$sql , 'debug2'=>$sqlMesa );
				echo json_encode($arrayResult);
				return;
			}

			$sql="UPDATE ventas_pos_mesas_cuenta
					SET estado     = 'Cerrada'
					WHERE activo  = 1
					AND id_empresa = $this->id_empresa
					AND id_mesa    = $params[id_mesa]";
			$query=$this->mysql->query($sql);
			if ($query) {
				$arrayResult = array('status' => 'success', 'message' => 'Mesa cerrada correctamente' );
			}
			else{
				$arrayResult = array('status' => 'failed', 'message' => 'Se produjo un error al cerrar la mesa','debug'=>$sql );
			}
			echo json_encode($arrayResult);
		}

		/**
		 * getComensales Consultar los comensales de la mesa
		 * @param  Int  $id_mesa Id de la mesa a consultar
		 * @return Json          Respuesta de la peticion en formato JSON
		 */
		public function getComensales($id_cuenta){
			$sql   = "SELECT
						id,
						tipo,
						cantidad,
						numero_habitacion,
						documento_comensal,
						comensal
					FROM ventas_pos_mesas_cuenta_comensales
					WHERE activo=1 AND id_empresa=$this->id_empresa AND id_cuenta=$id_cuenta";
			$query=$this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)){
				$arrayReturn[] = array(
										'id'                 => $row['id'],
										'tipo'               => $row['tipo'],
										'cantidad'           => $row['cantidad'],
										'numero_habitacion'  => $row['numero_habitacion'],
										'documento_comensal' => $row['documento_comensal'],
										'comensal'           => $row['comensal'],
									);
			}
			if (!$query) {
				$arrayResult = array('status' => 'failed', 'message' => 'Se produjo un error al consultar los comensales','sql'=>$sql );
				echo json_encode($arrayResult);
				return;
			}

			$arrayResult = array('status' => 'success', 'comensales' => $arrayReturn );
			echo json_encode($arrayResult);
		}

	}

?>