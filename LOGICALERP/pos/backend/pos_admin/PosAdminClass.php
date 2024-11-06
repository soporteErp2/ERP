<?php
	// error_reporting(E_ALL);
	/**
	 * PosAdminClass Clase para manejar las opciones adicionales del pos
	 */
	class PosAdminClass
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
		 * getToken Actualizar y consultar el token del usuario para el acceso al POS
		 * @param  Int 		$id_usuario Id del usuario a actualizar el token
		 * @return Array             	Json con la respuesta de la peticion, incluye el token
		 */
		public function getToken($id_usuario){
			$token = password_hash(date("Y-m-d H:i:s")."PASSWORD_DEFAULT", PASSWORD_DEFAULT );
			$sql   = "UPDATE empleados SET token_pos='$token' WHERE activo=1 AND id_empresa=$this->id_empresa AND id=$id_usuario ";
			$query = $this->mysql->query($sql);
			if ($query) {
				$arrayResponse = array('status' => "success", "token"=> $token );
			}
			else{
				$arrayResponse = array('status' => "failed", "message"=> "No se pudo actualizar el token de seguridad para el acceso " );
			}
			echo json_encode($arrayResponse);
		}

		/**
		 * anularComanda Anular un pedido comandado en el POS
		 * @param  Int 	  $id_comanda Id de la comanda a anular
		 * @param  String 	  	$observacion Observacion de la anulacion
		 * @return Array              Json con la respuesta de la peticion
		 */
		public function anularComanda($id_comanda,$observacion){
			$sql   = "UPDATE
							ventas_pos_comanda
						SET estado                  = 3,
							id_usuario_anulacion        = '$_SESSION[IDUSUARIO]',
							documento_usuario_anulacion = '$_SESSION[CEDULAFUNCIONARIO]',
							usuario_anulacion           = '$_SESSION[NOMBREFUNCIONARIO]',
							fecha_anulacion             = '".date("Y-m-d")."',
							hora_anulacion              = '".date("H:i:s")."',
							observacion_anulacion       = '$observacion'
						WHERE activo=1
						AND id_empresa=$this->id_empresa
						AND id=$id_comanda ";
			$query = $this->mysql->query($sql);
			if ($query) {
				$arrayResponse = array('status' => "success", "message"=> "Comanda anulada correctamente" );
			}
			else{
				$arrayResponse = array('status' => "failed", "message"=> "No se pudo anular la comanda ","debug"=>$sql );
			}
			echo json_encode($arrayResponse);
		}

		/**
		 * anularComanda Anular un pedido comandado en el POS
		 * @param  Int 	  		$id_factura Id de la comanda a anular
		 * @param  String 	  	$observacion Observacion de la anulacion
		 * @return Array        Json con la respuesta de la peticion
		 */
		public function anularFactura($id_documento,$observacion){
			$sql  = "SELECT estado,id_entrada_almacen FROM ventas_pos WHERE id=$id_documento";
			$query = $this->mysql->query($sql);
			$estado = $this->mysql->result($query,0,'estado');
			$idRemision = $this->mysql->result($query,0,'id_entrada_almacen');

			if ($estado=='500') {
				$sql  = "UPDATE estado=3 FROM ventas_pos WHERE id=$id_documento";
				$query = $this->mysql->query($sql);
				if (!$query) { return array('status' => 'failed','message'=>"No se actualizo el documento","debug"=>$sql); }
				else{
					return array('status' => 'success','message'=>"se anulo la factura correctamente","debug"=>$sql);
				}
			}

			$resultAccounts = $this->rollBackAccounts($id_documento);
			if(!$resultAccounts['status']){
				echo json_encode(array('status' => 'failed', 'message'=>$resultAccounts['message'],"debug"=>$resultAccounts['debug']) );
				return;
			}
			$resultInv = $this->rollBackInventory($id_documento,$idRemision);
			if(!$resultInv['status']){
				echo json_encode(array('status' => 'failed', 'message'=>$resultInv['message']));
				return;
			}

			$sql   = "UPDATE ventas_pos SET estado='3',detalle_estado='$observacion' WHERE id=$id_documento ";
			$query = $this->mysql->query($sql);
			if (!$query) {
				echo json_encode(array('status' => 'failed', 'message'=>'actualizo el documento'));
				return;
			}
			else{
				$sql="INSERT INTO log_documentos_contables
						(
							id_documento,
							id_usuario,
							usuario,
							documento_usuario,
							nombre_usuario,
							fecha,
							hora,
							actividad,
							tipo_documento,
							descripcion,
							id_sucursal,
							sucursal,
							id_empresa,
							ip
							)
						VALUES
						(
							'$id_documento',
							'$_SESSION[IDUSUARIO]',
							'$_SESSION[NOMBREFUNCIONARIO]',
							'$_SESSION[CEDULAFUNCIONARIO]',
							'$_SESSION[NOMBREFUNCIONARIO]',
							'".date("Y-m-d")."',
							'".date("H:i:s")."',
							'Cancelar',
							'POS',
							'POS',
							'$_SESSION[SUCURSAL]',
							'$_SESSION[NOMBRESUCURSAL]',
							'$_SESSION[EMPRESA]',
							'$_SESSION[REMOTE_ADDR]'

						) ";
				$query = $this->mysql->query($sql);

				echo json_encode(array('status' => 'success', 'message'=>'se anulo la factura correctamente'));
			}
		}

		/**
		 * rollBackAccounts Eliminar las cuentas contables de la factura POS
		 * @param  Int 		$id_documento 	Id de la factura a anular
		 * @return Array               		Array json con la respuesta de la peticion
		 */
		public function rollBackAccounts($id_documento){
			$sql  = "DELETE FROM asientos_colgaap
						WHERE activo=1
							AND id_documento   = $id_documento
							AND tipo_documento = 'POS'
							AND id_empresa     = $this->id_empresa
							AND id_sucursal    = $this->id_sucursal
					";
			$query = $this->mysql->query($sql);
			if (!$query) { return array('status' => false,'message'=>"No se eliminaron las cuentas colgaap","debug"=>$sql); }

			$sql   = "DELETE FROM asientos_niif
						WHERE activo=1
							AND id_documento   = $id_documento
							AND tipo_documento = 'POS'
							AND id_empresa     = $this->id_empresa
							AND id_sucursal    = $this->id_sucursal
					";
			$query = $this->mysql->query($sql);
			if (!$query) { return array('status' => false,'message'=>"No se eliminaron las cuentas Niif","debug"=>$sql); }

			return array('status' => true);
		}

		/**
		 * rollBackInventory reversar el inventario de la factura POS
		 * @param  Int 		$id_documento 	Id de la factura a anular
		 * @return Array               		Array json con la respuesta de la peticion
		 */
		public function rollBackInventory($id_documento,$idRemision=''){
			//Si es un ticket con una RV relacionada, no devuelve inv
			if($idRemision){return array('status' => true);}
    		$accion = ($params['accion'=='aumentar'])? " + " : " - " ;
    		$accion = " + ";
    		$sql="SELECT
    					id,
    					id_item,
    					cantidad,
    					precio_venta
    				FROM
    					ventas_pos_inventario
    				WHERE
    					activo=1
					AND id_pos=$id_documento
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
					AND id_pos=$id_documento
    					";
    		$query=$this->mysql->query($sql);
    		while ($row=$this->mysql->fetch_array($query)) {
    			$arrayItems[$row['id_item']][] = $row['cantidad'];
    			$whereIdItems .= ($whereIdItems=="")? " id=$row[id_item] " : " OR id=$row[id_item] " ;
    		}

    		// CONSULTAR LA INFORMACION REQUERIDA PARA EL MOVIMIENTO DE INVENTARIO
    		$sql="SELECT id,codigo,id_bodega_produccion,inventariable
    				FROM items
    				WHERE activo=1 AND id_empresa=$this->id_empresa AND ($whereIdItems)";
    		$query=$this->mysql->query($sql);
    		while ($row=$this->mysql->fetch_array($query)) {
    			$arrayInfoItems[$row['id']]  = array(
													'codigo'        => $row['codigo'],
													'id_bodega'     => $row['id_bodega_produccion'],
													'inventariable' => $row['inventariable'],
    											);
    		}

    		$sql="SELECT id_seccion FROM ventas_pos WHERE id=$id_documento";
			$query=$this->mysql->query($sql);
			$id_seccion = $this->mysql->result($query,0,'id_seccion');


    		$sql="SELECT
						id_bodega,
						id_centro_costos,
						codigo_centro_costos,
						centro_costos
					FROM ventas_pos_secciones
					WHERE activo=1 AND id_empresa=$this->id_empresa AND id=$id_seccion";
			$query=$this->mysql->query($sql);
			$id_bodega_ambiente = $this->mysql->result($query,0,'id_bodega');

    		if (count($arrayItems)>0) {
    			foreach ($arrayItems as $id_item => $arrayItemsResul) {
    				foreach ($arrayItemsResul as $key => $cantidad) {
    					// SI EL ITEM NO ES INVENTARIABLE, ENTONCES NO SE DEBE DESCONTAR DEL INVENTARIO
    					if ($arrayInfoItems[$id_item]['inventariable']<>'true') { continue; }
    					$id_bodega = ($arrayInfoItems[$id_item]['id_bodega']>0)? $arrayInfoItems[$id_item]['id_bodega'] : $id_bodega_ambiente ;
    					$sql="UPDATE inventario_totales SET cantidad=cantidad $accion $cantidad
								WHERE activo=1 AND id_item=$id_item AND id_ubicacion=$id_bodega";
						$query=$this->mysql->query($sql);
						if (!$query) {
							$messageError .= "Se produjo un error al actualizar el item id $id_item $sql<br/>";
						}

    				}
    			}
    			if($messageError<>''){
    				return array('status' => false,'message'=>$messageError,"debug"=>$sql);
				}
				else{
					return array('status' => true);
				}
    		}
    		else{ return array('status' => true); }

		}

		/**
		 * restaurarComanda Crear comanda de nuevo para refacturarla
		 * @param  Int $id_documento Id de la factura de venta POS
		 * @return Array             Json con la respuesta de la peticion
		 */
		public function restaurarComanda($id_documento){
			$sql="SELECT
						id_caja,
						caja,
						id_mesa,
						codigo_mesa,
						mesa,
						id_seccion,
						seccion
					FROM ventas_pos
					WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento";
			$query=$mysql->query($sql,$mysql->link);
			$id_mesa = $this->mysql->result($query,0,'id_mesa');

			$sql   = "SELECT
							id,
							id_comanda,
							id_row_item_cuenta,
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
							inventariable,
							activo
						FROM ventas_pos_inventario
						WHERE activo=1 AND id_empresa=$this->id_empresa AND id_pos=$id_documento";
			$query = $this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)) {
				# code...
			}

			$sql   = "SELECT
							id,
							id_pos,
							id_item_producto,
							id_item,
							codigo,
							id_unidad_medida,
							nombre_unidad_medida,
							cantidad_unidad_medida,
							nombre,
							cantidad,
							id_empresa,
							activo
						FROM ventas_pos_inventario_receta
						WHERE activo=1 AND id_empresa=$this->id_empresa AND id_pos=$id_documento";
			$query = $this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)) {
				# code...
			}

		}

		/**
		 * cerrarCaja Cerrar caja
		 * @param  Int 	$id_row id de la fila a actualizar
		 * @return Array         Json con la respuesta de la peticion
		 */
		public function cerrarCaja($id_row){
			$fecha_cierre = date("Y-m-d");
			$hora_cierre  = date("H:i:s");
			$sql="UPDATE ventas_pos_cajas_movimientos
					SET estado         	   = 'Cerrada',
						fecha_cierre       = '$fecha_cierre',
						hora_cierre        = '$hora_cierre',
						observacion_cierre = 'Cerrado por auditoria'
					WHERE id=$id_row ";
			$query=$this->mysql->query($sql);
			if ($query) {
				$arrayResponse = array('status' => "success", "message"=> "Se cerro correctamente la caja" );
			}
			else{
				$arrayResponse = array('status' => "failed", "message"=> "No se cerro la caja","sql"=>$sql );
			}

			echo json_encode($arrayResponse);
		}

		/**
		 * generarPrecierre Generar el Precierre de la auditoria
		 * @param  Date   $fecha Fecha del Precierre de la auditoria
		 * @return Array         Json con la respuesta de la peticion
		 */
		public function generarPrecierre($fecha){
			$sql="INSERT INTO ventas_pos_auditoria_precierre
						(
							fecha,
							hora,
							id_usuario,
							documento_usuario,
							usuario,
							estado,
							id_sucursal,
							id_empresa
						)
					VALUES
						(
							'$fecha',
							'".date("H:i:s")."',
							'$_SESSION[IDUSUARIO]',
							'$_SESSION[CEDULAFUNCIONARIO]',
							'$_SESSION[NOMBREFUNCIONARIO]',
							'1',
							'$this->id_sucursal',
							'$this->id_empresa'
						)
					";
			$query=$this->mysql->query($sql);
			if ($query) {
				$arrayResponse = array('status' => "success", "message"=> "Se genero correctamente el precierre para el dia $fecha" );
			}
			else{
				$arrayResponse = array('status' => "failed", "message"=> "No se genero el precierre","sql"=>$sql );
			}

			echo json_encode($arrayResponse);
		}

		/**
		 * generarCierre Generar el Precierre de la auditoria
		 * @param  Date   $fecha Fecha del cierre de la auditoria
		 * @return Array         Json con la respuesta de la peticion
		 */
		public function generarCierre($fecha){
			$sql="INSERT INTO ventas_pos_auditoria_cierre
						(
							fecha,
							hora,
							id_usuario,
							documento_usuario,
							usuario,
							id_sucursal,
							id_empresa
						)
					VALUES
						(
							'$fecha',
							'".date("H:i:s")."',
							'$_SESSION[IDUSUARIO]',
							'$_SESSION[CEDULAFUNCIONARIO]',
							'$_SESSION[NOMBREFUNCIONARIO]',
							'$this->id_sucursal',
							'$this->id_empresa'
						)
					";
			$query=$this->mysql->query($sql);
			if ($query) {
				$this->lockDocs($fecha);
				$this->lockPrecierre();
				$arrayResponse = array('status' => "success", "message"=> "Se genero correctamente el cierre para el dia $fecha" );
			}
			else{
				$arrayResponse = array('status' => "failed", "message"=> "No se genero el cierre","sql"=>$sql );
			}
			echo json_encode($arrayResponse);
		}

		/**
		 * lockDocs Bloquear lod documentos al hacer la auditoria
		 * @param  Date   $fecha Fecha del cierre de la auditoria
		 * @return Array         Json con la respuesta de la peticion
		 */
		public function lockDocs($fecha){
			$sql   = "UPDATE ventas_pos SET fecha_auditoria='$fecha',hora_auditoria='".(date("H:i:s"))."',estado='2'
						WHERE activo=1 AND estado=1 AND (fecha_auditoria='' OR ISNULL(fecha_auditoria) ) ";
			$query = $this->mysql->query($sql);
		}

		/**
		 * lockPrecierre Cerrar los precierres
		 */
		public function lockPrecierre(){
			$sql = "UPDATE ventas_pos_auditoria_precierre SET estado=2 WHERE activo=1 AND id_empresa=$this->id_empresa AND estado=1";
			$query = $this->mysql->query($sql);
		}

		/**
		 * setPin Actualizar el pin del usuario
		 * @param Int $pin   Nuevo pin a actualizar
		 */
		public function setPin($pin){
			// VALIDAR EL PIN
			$sql    ="SELECT pin FROM empleados WHERE activo=1 AND id_empresa=$this->id_empresa AND pin='$pin' ";
			$query  =$this->mysql->query($sql);
			$pinAnt = $this->mysql->result($query,0,'pin');

			if ($pin==$pinAnt) {
				$arrayResponse = array('status' => "failed", "message"=> "Pin repetido, por favor digite uno diferente" );
				echo json_encode($arrayResponse);
				return;
			}


			$sql="UPDATE empleados SET pin='$pin' WHERE id='$_SESSION[IDUSUARIO]' ";
			$query=$this->mysql->query($sql);
			if ($query) {
				$arrayResponse = array('status' => "success", "message"=> "Se actualizo correctamente el pin" );
			}
			else{
				$arrayResponse = array('status' => "failed", "message"=> "No se actualizo el pin","sql"=>$sql );
			}

			echo json_encode($arrayResponse);
		}
	}

 ?>