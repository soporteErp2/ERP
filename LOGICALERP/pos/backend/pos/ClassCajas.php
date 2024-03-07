<?php
	error_reporting(E_ERROR | E_PARSE);

	/**
	 * Class Cajas clase backend para la administracion de las cajas del pos
	 */
	class Cajas
	{
		public $id_sucursal;
		public $id_empresa;
		public $mysql;

		function __construct($id_sucursal,$id_empresa,$mysql){
			$this->id_sucursal = $id_sucursal;
			$this->id_empresa  = $id_empresa;
			$this->mysql       = $mysql;
		}

		/**
		 * getCashRegister Consultar las cajas configuradas de un restaurante
		 * @param  Int $id_restaurante id del restaurante del cual se consultaran las cajas
		 * @return Array Json con las cajas registradoras configuradas para ese restaurante o mensaje indicando que no tiene configuradas
		 */
		public function getCashRegister($id_restaurante){
			$sql="SELECT
						id,
						id_caja,
						nombre_caja,
						id_seccion,
						seccion
					FROM ventas_pos_cajas_secciones
					WHERE activo=1
					AND id_empresa=$this->id_empresa
					AND id_seccion=$id_restaurante";
			$query=$this->mysql->query($sql);
			$estadoCaja = 'Cerrada';
			while ($row=$this->mysql->fetch_array($query)) {
				$arrayRest[] = array('id' => $row['id_caja'], 'nombre' => $row['nombre_caja'] , 'id_seccion' => $row['id_seccion'] );
			}
			if (count($arrayRest)>0) {
				if (count($arrayRest)==1) {
					// $jsonEstado = $this->getCashRegisterState($arrayRest[0]['id']);
					// $jsonEstado = json_decode($jsonEstado,true);
					$sql   = "SELECT estado
								FROM ventas_pos_cajas_movimientos
								WHERE activo=1
								AND id_caja = ".$arrayRest[0]['id']."
								AND id_empresa=$this->id_empresa
								AND estado='Abierta' ";
					$query=$this->mysql->query($sql);
					$estadoCaja = $this->mysql->result($query,0,'estado');
				}
				// print_r($arrayRest);
				$arrayResult = array('status' => 'success', 'cajas'=> $arrayRest, 'estado_caja_unica'=> $estadoCaja, 'id_caja_unica'=>$arrayRest[0]['id'], 'nombre_caja_unica' =>$arrayRest[0]['nombre'] , 'id_seccion' =>$arrayRest[0]['id_seccion'] );
			}
			else{
				$arrayResult = array('status' => 'failed', 'message'=>'No hay cajas configuradas' );
			}
			echo json_encode($arrayResult);
		}

		/**
		 * getCashRegisterState Consultar la informacion de la caja si esta abierta o cerrada
		 * @param  int $id_caja id de la caja a consultar
		 * @return Array Json con la informacion de la caja
		 */
		public function getCashRegisterState($id_caja){
			$sql   = "SELECT
							id_caja,
							nombre_caja,
							id_usuario,
							documento_usuario,
							nombre_usuario,
							estado,
							fecha_apertura,
							hora_apertura,
							provision,
							fecha_cierre,
							hora_cierre,
							valor_cierre
						FROM ventas_pos_cajas_movimientos
						WHERE activo=1
						AND id_caja = $id_caja
						AND id_empresa=$this->id_empresa
						AND estado='Abierta' ";
			$query = $this->mysql->query($sql);
			if (!$query) {
				$arrayResponde = array('status' => 'failed', 'message'=>'Se produjo un error en la consulta','sql'=>$sql);
			}
			else{
				$rows = $this->mysql->num_rows($query);
				if($rows>0){
					$arrayResponde = array(
											'status'            => 'success',
											'estado'            => $this->mysql->result($query,0,'estado'),
											'id_usuario'        => $this->mysql->result($query,0,'id_usuario'),
											'documento_usuario' => $this->mysql->result($query,0,'documento_usuario'),
											'nombre_usuario'    => $this->mysql->result($query,0,'nombre_usuario'),
											'provision'         => $this->mysql->result($query,0,'provision'),
											'nombre_caja'       => $this->mysql->result($query,0,'nombre_caja')
										);

				}
				else{
					$arrayResponde = array('status' => 'success', 'estado'=>'Cerrada', 'sql'=>$sql);
				}
			}
			echo json_encode($arrayResponde);
		}

		/**
		 * openCashRegister Abrir caja para iniciar la operacion
		 * @param  Json $params Parametros requeridos para la apertura de caja
		 * @return Array Json con la informacion de la caja
		 */
		public function openCashRegister($params){
			$sql="INSERT INTO ventas_pos_cajas_movimientos
					(
						id_caja,
						nombre_caja,
						id_usuario,
						documento_usuario,
						nombre_usuario,
						estado,
						fecha_apertura,
						hora_apertura,
						provision,
						observacion_apertura,
						id_empresa
					)
					VALUES
					(
						'$params[id_caja]',
						'$params[nombre_caja]',
						'$params[id_usuario]',
						'$params[documento_usuario]',
						'$params[nombre_usuario]',
						'$params[estado]',
						'".date("Y-m-d")."',
						'".date("H:i:s")."',
						'$params[provision]',
						'$params[observacion_apertura]',
						'$this->id_empresa'
					) ";
			$query=$this->mysql->query($sql);
			if ($query) {
				$lastId = $this->mysql->insert_id();
				$arrayResponde = array(
										'status'   => 'success',
										'message'  => 'Se inserto el registro en la base de datos',
										'insertId' => $lastId
									);
			}
			else{
				$arrayResponde = array('status' => 'failed', 'message'=>'Se produjo un error al guardar el registro', 'sql'=> $sql );
			}
			echo json_encode($arrayResponde);
		}

		/**
		 * getPagosCaja Consultar todos los pagos realizados a una caja antes de cerrarla
		 * @return Array Json con la informacion de los pagos realizados
		 */
		public function getPagosCaja($id_caja,$id_usuario){
			$sql   = "SELECT
							VP.id,
							VP.usuario,
							VP.consecutivo,
							FP.forma_pago,
							FP.valor,
							FP.id_forma_pago,
							CP.tipo,
							IF(CP.tipo='Cheque Cuenta','Transferencia Cuentas',IF(CP.tipo='Cortesia','Cortesias','Facturas')) AS tipo_fv
						FROM
							ventas_pos AS VP
						INNER JOIN ventas_pos_formas_pago AS FP ON FP.id_pos = VP.id
						INNER JOIN configuracion_cuentas_pago_pos AS CP ON CP.id = FP.id_forma_pago
						WHERE
							VP.activo = 1
						AND VP.id_caja = $id_caja
						AND VP.fecha_documento = '".(date("Y-m-d"))."'
						AND (VP.estado=1 OR VP.estado=500)
						AND VP.id_usuario=$id_usuario
						";
			$query = $this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)){
				if ($row['tipo']=='Cortesia') {
					$row['valor']=0;
				}
				$arrayDetallado[] = array(
										'forma_pago'  => $row['forma_pago'],
										'valor'       => $row['valor'],
										'tipo'        => $row['tipo'],
										'usuario'     => $row['usuario'],
										'consecutivo' => $row['consecutivo'],
									);
				if (is_array($arrayResumido[$row['id']])) {
					$arrayResumido[$row['id']]['valor'] += $row['valor'];
				}
				else{
					$arrayResumido[$row['id']]= array(
													'forma_pago'  => $row['forma_pago'],
													'valor'       => $row['valor'],
													'tipo'        => $row['tipo_fv'],
													'usuario'     => $row['usuario'],
													'consecutivo' => $row['consecutivo'],
												);
				}

			}
			$arrayReturn['fecha']     = date("Y-m-d");
			$arrayReturn['detallado'] = $arrayDetallado;
			$arrayReturn['resumido']  = $arrayResumido;
			$arrayReturn['debug']     = $sql;
			echo json_encode($arrayReturn);
		}

		/**
		 * cerrarCaja Cerrar caja del pos
		 * @param  Array $params  Parametros necesarios para realizar el cierre de la caja
		 * @return Array          Json con la respuesta de la peticion
		 */
		public function cerrarCaja($params){
			$sql   = "UPDATE ventas_pos_cajas_movimientos
						SET estado               = 'Cerrada',
						id_usuario_cierre        = '$params[id_empleado]',
						documento_usuario_cierre = '$params[documento]',
						nombre_usuario_cierre    = '$params[nombre]'
						WHERE activo = 1
						AND id_caja  = $params[id_caja]
						AND estado   ='Abierta'
						 ";
			$query = $this->mysql->query($sql);
			if ($query) {
				$arrayResponde = array(
										'status'   => 'success',
										'message'  => 'Se cerro la caja correctamente',
									);
			}
			else{
				$arrayResponde = array('status' => 'failed', 'message'=>'Se produjo un error al cerrar la caja', 'sql'=> $sql );
			}
			echo json_encode($arrayResponde);
		}

	}

?>