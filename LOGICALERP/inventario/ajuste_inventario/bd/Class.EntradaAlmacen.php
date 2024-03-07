<?php
// include 'Class.DocumentFunctions.php';

/**
* @Class ClassRemision generar la salidas del inventario
* @param int id de cabecera del documento de ajuste
* @param int id de la empresa
* @param onj objeto de conexion mysql
*/
class ClassEntradaAlmacen extends ClassDocumentFunctions
{

	public $arrayAsiento = '';

	function __construct($idAjuste,$id_empresa,$mysql)
	{
		// INICIALIZAR EL CONSTRUCTOR DE LA CLASE PADRE
		parent::__construct($idAjuste,$id_empresa,$mysql);
	}

	/**
	* @method insertHead insertar la cabecera del documento
	*/
	public function insertDocumentHead()
	{

	 	$sql="SELECT consecutivo FROM configuracion_consecutivos_documentos WHERE id_empresa=$this->id_empresa AND activo=1 AND documento='entrada_de_almacen' AND modulo='compra' AND id_sucursal=".$this->arrayHeadAjuste['id_sucursal']."  LIMIT 0,1";
	 	$query=$this->mysql->query($sql,$this->mysql->link);
	 	$consecutivo = $this->mysql->result($query,0,'consecutivo');

		$random = date("Y_m_d_h_i_s");
		$sql="INSERT INTO compras_entrada_almacen
				(random,
				id_empresa,
				id_sucursal,
				id_bodega,
				consecutivo,
				fecha_inicio,
				fecha_finalizacion,
				id_usuario,
				documento_usuario,
				usuario,
				id_proveedor,
				cod_proveedor,
				nit,
				proveedor,
				observacion,
				estado,
				id_centro_costo,
				codigo_centro_costo,
				centro_costo,
				tipo_entrada)
				VALUES
				(
					'$random',
					$this->id_empresa,
					".$this->arrayHeadAjuste['id_sucursal'].",
					".$this->arrayHeadAjuste['id_bodega'].",
					'$consecutivo',
					'".$this->arrayHeadAjuste['fecha_documento']."',
					'".$this->arrayHeadAjuste['fecha_documento']."',
					'".$this->arrayHeadAjuste['id_usuario']."',
					'".$this->arrayHeadAjuste['documento_usuario']."',
					'".$this->arrayHeadAjuste['usuario']."',
					'".$this->arrayHeadAjuste['id_tercero']."',
					'".$this->arrayHeadAjuste['cod_tercero']."',
					'".$this->arrayHeadAjuste['nit']."',
					'".$this->arrayHeadAjuste['tercero']."',
					'Documento perteneciente a ajuste de inventario automatico',
					2,
					'".$this->arrayHeadAjuste['id_centro_costo']."',
					'".$this->arrayHeadAjuste['codigo_centro_costo']."',
					'".$this->arrayHeadAjuste['centro_costo']."',
					'AI'
				)
				";
		$query=$this->mysql->query($sql,$this->mysql->link);
		if ($query) {

			$sql="SELECT id,consecutivo FROM compras_entrada_almacen WHERE activo=1 AND id_empresa=$this->id_empresa AND random='$random'";
			$query=$this->mysql->query($sql,$this->mysql->link);
			$this->arrayHeadAjuste['id_entrada_almacen']          = $this->mysql->result($query,0,'id');
			$this->arrayHeadAjuste['consecutivo_entrada_almacen'] = $consecutivo;

			$sql="UPDATE configuracion_consecutivos_documentos SET consecutivo =  $consecutivo + 1 WHERE id_empresa=$this->id_empresa  AND activo=1 AND documento='entrada_de_almacen' AND modulo='compra' AND id_sucursal=".$this->arrayHeadAjuste['id_sucursal']." ";
	 		$query=$this->mysql->query($sql,$this->mysql->link);

	 		$sql="UPDATE inventario_ajuste SET id_entrada_almacen= ".$this->arrayHeadAjuste['id_entrada_almacen'].", consecutivo_entrada_almacen='$consecutivo'
	 				WHERE activo=1 AND id_empresa=$this->id_empresa AND id=$this->idAjuste";
	 		$query=$this->mysql->query($sql,$this->mysql->link);
		}
	}

	/**
	* @method updateDocumentHead actualizar la cabecera del documento
	*/
	public function updateDocumentHead()
	{
		$sql="UPDATE compras_entrada_almacen SET
					fecha_inicio        = '".$this->arrayHeadAjuste['fecha_documento']."',
					fecha_finalizacion  = '".$this->arrayHeadAjuste['fecha_documento']."',
					id_proveedor        = '".$this->arrayHeadAjuste['id_tercero']."',
					cod_proveedor       = '".$this->arrayHeadAjuste['cod_tercero']."',
					nit                 = '".$this->arrayHeadAjuste['nit']."',
					proveedor           = '".$this->arrayHeadAjuste['tercero']."',
					id_centro_costo     = '".$this->arrayHeadAjuste['id_centro_costo']."',
					codigo_centro_costo = '".$this->arrayHeadAjuste['codigo_centro_costo']."',
					centro_costo        = '".$this->arrayHeadAjuste['centro_costo']."'
				WHERE activo=1 AND id_empresa=$this->id_empresa AND id =".$this->arrayHeadAjuste['id_entrada_almacen'];
		$query=$this->mysql->query($sql,$this->mysql->link);
	}

	/**
	* @method clearDocumentBody eliminar los items del cuerpo de la entrada
	*/
	public function clearDocumentBody()
	{
		$sql="DELETE FROM compras_entrada_almacen_inventario WHERE activo=1 AND id_entrada_almacen=".$this->arrayHeadAjuste['id_entrada_almacen'];
		$query=$this->mysql->query($sql,$this->mysql->link);
	}

	/**
	* @method insertItemsBody inserter los items en la entrada de venta
	*/
	public function insertItemsBody()
	{
		foreach ($this->arrayInsertItemsEA as $key => $items) {
			$cantidad = abs($items['cantidad_inventario'] - $items['cantidad']);

			$valueInsert .="(
								'".$this->arrayHeadAjuste['id_entrada_almacen']."',
								'".$items['id_inventario']."',
								$cantidad,
								'".$items['costo_unitario']."',
								'".$items['costo_unitario']."',
								'".$items['observaciones']."'
							),";
			// informacion para la contabilizacion
			$costo   = $cantidad* $items['costo_unitario'];

			foreach ($this->arrayCuentasItems[$items['id_inventario']] as $cuenta => $arrayResult) {
				$idDocInventario = $arrayResult['id'];
				$id_puc          = $arrayResult['id_puc'];
				$estado          = $arrayResult['estado'];

				$estadoAsiento = ($estado=='debito')? 'debe' : 'haber';

				if(is_nan($this->arrayAsiento[$cuenta][$estadoAsiento])){ $this->arrayAsiento[$cuenta][$estadoAsiento] = 0; }
				$this->arrayAsiento[$cuenta][$estadoAsiento] += $costo;
				$this->arrayAsiento[$cuenta]['centro_costo'] = $this->arrayCuentasItems[$items['id_inventario']][$cuenta]['centro_costo'];
			}
			if (count($this->arrayCuentasItems[$items['id_inventario']])<2 ) {
				$this->rollBack('El item '.$items['codigo'].' '.$items['nombre'].' No tiene configuradas sus cuentas Colgaap  o esta errada la configuracion',$mysql);
			}

			foreach ($this->arrayCuentasItemsNiif[$items['id_inventario']] as $cuenta => $arrayResult) {
				$idDocInventario = $arrayResult['id'];
				$id_puc          = $arrayResult['id_puc'];
				$estado          = $arrayResult['estado'];

				$estadoAsiento = ($estado=='debito')? 'debe' : 'haber';

				if(is_nan($this->arrayAsientoNiif[$cuenta][$estadoAsiento])){ $this->arrayAsientoNiif[$cuenta][$estadoAsiento] = 0; }
				$this->arrayAsientoNiif[$cuenta][$estadoAsiento] += $costo;
				$this->arrayAsientoNiif[$cuenta]['centro_costo'] = $this->arrayCuentasItemsNiif[$items['id_inventario']][$cuenta]['centro_costo'];
			}

			if (count($this->arrayCuentasItemsNiif[$items['id_inventario']])<2 ) {
				$this->rollBack('El item '.$items['codigo'].' '.$items['nombre'].' No tiene configuradas sus cuentas NIIF  o esta errada la configuracion',$mysql);
			}

		}

		$valueInsert = substr($valueInsert, 0, -1);
		$sql="INSERT INTO compras_entrada_almacen_inventario
				(
					id_entrada_almacen,
					id_inventario,
					cantidad,
					costo_inventario,
					costo_unitario,
					observaciones
				)
				VALUES
					$valueInsert
				 ";
		$query=$this->mysql->query($sql,$this->mysql->link);
	}

	/**
	* @method generate generar la entrada
	*/
	public function generate()
	{
		parent::itemProcess();
		if (empty($this->arrayInsertItemsEA)) { echo '<script>console.log("Sin items a ingresar!");</script>'; return; }

		parent::getAjusteHead();
		if ($this->arrayHeadAjuste['id_entrada_almacen']>0) {
			$this->updateDocumentHead();
			$this->clearDocumentBody();
		}
		else{
			$this->insertDocumentHead();
		}

		$this->insertItemsBody();
		parent::inventoryMovement('ingreso ajuste','compras_entrada_almacen_inventario','id_entrada_almacen',$this->arrayHeadAjuste['id_entrada_almacen'],"Generar");

				// actualizarCantidadArticulos($id,'ingreso',"Generar");		//ACTUALIZAR LA CANTIDAD DE ARTICULOS
		// public function inventoryMovement($action,$tablaInventario,$idTablaPrincipal,$id,$document_action)


		$this->toAccount();
		parent::insertLog("Generar","EA","Entrada de Almacen (Ajuste de Inventario)");
		echo '<script>console.log("Entrada Insertada!");</script>';
	}

	/**
	* @method toAccount contabilizar el documento
	*/
	public function toAccount()
	{
		$globalDebito        = 0;
		$globalCredito       = 0;
		$valueInsertAsientos = '';
		foreach ($this->arrayAsiento as $cuenta => $arrayCuenta) {
			//$contAsientos++;
			$globalDebito  += $arrayCuenta['debe'];
			$globalCredito += $arrayCuenta['haber'];
			$idCentroCosto = ($arrayCuenta['centro_costo']=='Si')? $this->arrayHeadAjuste['id_centro_costo'] : '';
			$valueInsertAsientos .= "('".$this->arrayHeadAjuste['id_entrada_almacen']."',
										'".$this->arrayHeadAjuste['consecutivo_entrada_almacen']."',
										'EA',
										'Entrada de Almacen (Ajuste de Inventario)',
										'".$this->arrayHeadAjuste['id_entrada_almacen']."',
										'".$this->arrayHeadAjuste['consecutivo_entrada_almacen']."',
										'EA',
										'".$this->arrayHeadAjuste['fecha_documento']."',
										'".$arrayCuenta['haber']."',
										'".$arrayCuenta['debe']."',
										'$cuenta',
										'".$this->arrayHeadAjuste['id_tercero']."',
										'$idCentroCosto',
										'".$this->arrayHeadAjuste['id_sucursal']."',
										'$this->id_empresa'
									),";
		}

		$globalDebito  = round($globalDebito,$_SESSION['DECIMALESMONEDA']);
		$globalCredito = round($globalCredito,$_SESSION['DECIMALESMONEDA']);
		if ($globalDebito != $globalCredito) { $this->rollBack('El debito y credito no son iguales en la contabilidad Colgaap para la entrada',$mysql);	}
		$valueInsertAsientos = substr($valueInsertAsientos, 0, -1);
		$sql   = "INSERT INTO asientos_colgaap (
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
		$query=$this->mysql->query($sql,$this->mysql->link);
		if (!$query) { $this->rollBack('No se inserto la contabilidad Colgaap en la entrada',$mysql);	}

		$globalDebito        = 0;
		$globalCredito       = 0;
		$valueInsertAsientos = '';

		foreach ($this->arrayAsientoNiif as $cuenta => $arrayCuenta) {
			//$contAsientos++;
			$globalDebito  += $arrayCuenta['debe'];
			$globalCredito += $arrayCuenta['haber'];
			$idCentroCosto = ($arrayCuenta['centro_costo']=='Si')? $this->arrayHeadAjuste['id_centro_costo'] : '';
			$valueInsertAsientos .= "('".$this->arrayHeadAjuste['id_entrada_almacen']."',
										'".$this->arrayHeadAjuste['consecutivo_entrada_almacen']."',
										'EA',
										'Entrada de Almacen (Ajuste de Inventario)',
										'".$this->arrayHeadAjuste['id_entrada_almacen']."',
										'".$this->arrayHeadAjuste['consecutivo_entrada_almacen']."',
										'EA',
										'".$this->arrayHeadAjuste['fecha_documento']."',
										'".$arrayCuenta['haber']."',
										'".$arrayCuenta['debe']."',
										'$cuenta',
										'".$this->arrayHeadAjuste['id_tercero']."',
										'$idCentroCosto',
										'".$this->arrayHeadAjuste['id_sucursal']."',
										'$this->id_empresa'
									),";
		}

		$globalDebito  = round($globalDebito,$_SESSION['DECIMALESMONEDA']);
		$globalCredito = round($globalCredito,$_SESSION['DECIMALESMONEDA']);
		if ($globalDebito != $globalCredito) { $this->rollBack('El debito y credito no son iguales en la contabilidad Niif para la entrada',$mysql);	}
		$valueInsertAsientos = substr($valueInsertAsientos, 0, -1);
		$sql   = "INSERT INTO asientos_niif (
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
		$query=$this->mysql->query($sql,$this->mysql->link);
		if (!$query) { $this->rollBack('No se inserto la contabilidad Niif en la entrada',$mysql);	}
	}

	/**
	* @method edit editar o cancelar la entrada
	*/
	public function editCancel()
	{
		parent::getAjusteHead();
		parent::inventoryMovement('reversar ingreso ajuste','compras_entrada_almacen_inventario','id_entrada_almacen',$this->arrayHeadAjuste['id_entrada_almacen'],"Editar");
						// actualizarCantidadArticulos($id,'ingreso',"Generar");		//ACTUALIZAR LA CANTIDAD DE ARTICULOS
		// public function inventoryMovement($action,$tablaInventario,$idTablaPrincipal,$id,$document_action)

		parent::removeCounts('EA',$this->arrayHeadAjuste['id_entrada_almacen'],$this->arrayHeadAjuste['id_sucursal']);
		parent::insertLog("Editar","EA","Entrada de Almacen (Ajuste de Inventario)");
		
		echo '<script>console.log("Entrada editCancel!");</script>';

	}

	/**
	* @method rollBack deshacer el proceso iniciado y arrojar el error
	* @param str mensaje con el error que se presento
	*/
	public function rollBack($msjError,$mysql)
	{
		echo "<script>alert(\"$msjError\");document.getElementById('modal').parentNode.parentNode.removeChild(document.getElementById('modal').parentNode);</script>";
		$sql="DELETE FROM asientos_colgaap WHERE activo=1 AND id_empresa=$this->id_empresa AND tipo_documento = 'EA' AND id_documento='".$this->arrayHeadAjuste['id_entrada_almacen']."' ";
		$query=$this->mysql->query($sql,$this->mysql->link);
		$sql="DELETE FROM asientos_colgaap WHERE activo=1 AND id_empresa=$this->id_empresa AND tipo_documento = 'EA' AND id_documento='".$this->arrayHeadAjuste['id_entrada_almacen']."' ";
		$query=$this->mysql->query($sql,$this->mysql->link);
		parent::inventoryMovement('reversar ingreso ajuste','compras_entrada_almacen_inventario','id_entrada_almacen',$this->arrayHeadAjuste['id_entrada_almacen'],"Editar");
		// actualizarCantidadArticulos($id,'ingreso',"Generar");		//ACTUALIZAR LA CANTIDAD DE ARTICULOS
		// public function inventoryMovement($action,$tablaInventario,$idTablaPrincipal,$id,$document_action)

		// ROLLBACK REMISIONES
		$sql="UPDATE inventario_ajuste SET estado=0 WHERE activo=1 AND id_empresa=$this->id_empresa AND id='".$this->idAjuste."' ";
		$query=$this->mysql->query($sql,$this->mysql->link);
		$sql="DELETE FROM asientos_colgaap WHERE activo=1 AND id_empresa=$this->id_empresa AND tipo_documento = 'RV' AND id_documento='".$this->arrayHeadAjuste['id_remision_venta']."' ";
		$query=$this->mysql->query($sql,$this->mysql->link);
		$sql="DELETE FROM asientos_colgaap WHERE activo=1 AND id_empresa=$this->id_empresa AND tipo_documento = 'RV' AND id_documento='".$this->arrayHeadAjuste['id_remision_venta']."' ";
		$query=$this->mysql->query($sql,$this->mysql->link);
		parent::inventoryMovement('reversar salida ajuste','ventas_remisiones_inventario','id_remision_venta',$this->arrayHeadAjuste['id_remision_venta'],"Editar");
		exit;
	}

}

 ?>