<?php
	// error_reporting(E_ALL);
	include '../../../funciones_globales/Clases/ClassInventory.php';

/**
* @class ClassDocumentFunctions
* @param arr array con los items para la entrada de almacen
* @param arr array con los items para la remision de venta
*/
class ClassDocumentFunctions
{

	public $arrayHeadAjuste       = '';
	public $arrayInsertItemsEA    = '';
	public $arrayInsertItemsRV    = '';
	public $idAjuste              = '';
	public $id_empresa            = '';
	public $mysql                 = '';
	public $arrayCuentasItems     = '';
	public $arrayCuentasItemsNiif = '';
	public $arrayDatosRV = array(
								"campo_fecha"             => "fecha_inicio",
								"tablaPrincipal"          => "ventas_remisiones",
								"campos_tabla_inventario" => " id_inventario AS id_item ",
								"documento"               => "RV",
								"descripcion_documento"   => "Remision de Venta",
								);
	public $arrayDatosEA = array(
								"campo_fecha"             => "fecha_inicio",
								"tablaPrincipal"          => "compras_entrada_almacen",
								"campos_tabla_inventario" => " id_inventario AS id_item ",
								"documento"               => "EA",
								"descripcion_documento"   => "Entrada de Almacen",
								);


	function __construct($idAjuste,$id_empresa,$mysql)
	{
		$this->idAjuste   = $idAjuste;
		$this->id_empresa = $id_empresa;
		$this->mysql      = $mysql;
		$this->objectInventory = new ClassInventory($mysql);
	}

	/**
	* @method itemProcess verificar si se realizaron salidas y consultar los que se deben sacar, consultar items y sus respectivas cuentas, sino no se continua con el proceso,
	*/
	public function itemProcess()
	{
		$sql="SELECT
					id,
					id_inventario,
					codigo,
					id_unidad_medida,
					nombre_unidad_medida,
					cantidad_unidad_medida,
					nombre,
					cantidad_inventario,
					costo_inventario,
					cantidad,
					costo_unitario,
					observaciones,
					inventariable
				FROM inventario_ajuste_detalle WHERE activo=1  AND id_ajuste_inventario = $this->idAjuste";
		$query=$this->mysql->query($sql,$this->mysql->link);
		while ($row=$this->mysql->fetch_array($query)) {
			$id_inventario       = $row['id_inventario'];
			$cantidad_inventario = $row['cantidad_inventario'];
			$cantidad            = $row['cantidad'];
			$diferencia          = $cantidad_inventario - $cantidad;
			$whereIdInvetario    .=($whereIdInvetario == '' )? "id_items=$id_inventario" : " OR id_items=$id_inventario";

			if ($cantidad == $cantidad_inventario) { continue; }

			// si el inventario esta en negativo, entonces primero se pone en 0 para hacer el ingreso 
			// (cuando el inventario es negativo, no se puede poner mas negativo)
			if ($cantidad_inventario<0) {
				$this->arrayInsertItemsEA[$row['id']]  = array(
															'id_inventario'          => $row['id_inventario'],
															'codigo'                 => $row['codigo'],
															'id_unidad_medida'       => $row['id_unidad_medida'],
															'nombre_unidad_medida'   => $row['nombre_unidad_medida'],
															'cantidad_unidad_medida' => $row['cantidad_unidad_medida'],
															'nombre'                 => $row['nombre'],
															'cantidad_inventario'    => $row['cantidad_inventario'],
															'costo_inventario'       => $row['costo_inventario'],
															'cantidad'               => abs($cantidad_inventario),
															'costo_unitario'         => $row['costo_unitario'],
															'observaciones'          => $row['observaciones'],
															'inventariable'          => $row['inventariable'],
														);
			}

			// Si se realiza salida de items
			if ($cantidad < $cantidad_inventario) {
				// $this->itemsValidateRV[$row['id_inventario']] = $row['nombre'];
				$this->arrayInsertItemsRV[$row['id']]  = array(
																'id_inventario'          => $row['id_inventario'],
																'codigo'                 => $row['codigo'],
																'id_unidad_medida'       => $row['id_unidad_medida'],
																'nombre_unidad_medida'   => $row['nombre_unidad_medida'],
																'cantidad_unidad_medida' => $row['cantidad_unidad_medida'],
																'nombre'                 => $row['nombre'],
																'cantidad_inventario'    => $row['cantidad_inventario'],
																'costo_inventario'       => $row['costo_inventario'],
																'cantidad'               => $row['cantidad'],
																'costo_unitario'         => $row['costo_unitario'],
																'observaciones'          => $row['observaciones'],
																'inventariable'          => $row['inventariable'],
															);
			}

			// si se realiza entrada de items
			if ($cantidad > $cantidad_inventario) {
				// $this->itemsValidateEA[$row['id_inventario']] = $row['nombre'];
				$this->arrayInsertItemsEA[$row['id']]  = array(
															'id_inventario'          => $row['id_inventario'],
															'codigo'                 => $row['codigo'],
															'id_unidad_medida'       => $row['id_unidad_medida'],
															'nombre_unidad_medida'   => $row['nombre_unidad_medida'],
															'cantidad_unidad_medida' => $row['cantidad_unidad_medida'],
															'nombre'                 => $row['nombre'],
															'cantidad_inventario'    => $row['cantidad_inventario'],
															'costo_inventario'       => $row['costo_inventario'],
															'cantidad'               => $row['cantidad'],
															'costo_unitario'         => $row['costo_unitario'],
															'observaciones'          => $row['observaciones'],
															'inventariable'          => $row['inventariable'],
														);
			}
		}

		// cuentas de los items
		$sql="SELECT
						IC.id_puc,
						IC.puc,
						IC.tipo AS estado,
						IC.descripcion,
						IC.id_items,
						P.centro_costo
					FROM
						items_cuentas AS IC , puc AS P
					WHERE
						($whereIdInvetario)
					AND P.id = IC.id_puc
					AND IC.activo = 1
					AND IC.estado = 'venta'
					AND (
						IC.descripcion = 'costo'
						OR IC.descripcion = 'contraPartida_costo'
					);";
		$query=$this->mysql->query($sql,$this->mysql->link);
		while ($row=$this->mysql->fetch_array($query)) {
			$id_items = $row['id_items'];
			$puc      = $row['puc'];
			$this->arrayCuentasItems[$id_items][$puc]  = array(
																'id_puc'       => $row['id_puc'],
																'estado'       => $row['estado'],
																'descripcion'  => $row['descripcion'],
																'centro_costo' => $row['centro_costo'],
															);
		}
		$sql="SELECT
						IC.id_puc,
						IC.puc,
						IC.tipo AS estado,
						IC.descripcion,
						IC.id_items,
						P.centro_costo
					FROM
						items_cuentas_niif AS IC , puc_niif AS P
					WHERE
						($whereIdInvetario)
					AND P.id = IC.id_puc
					AND IC.activo = 1
					AND IC.estado = 'venta'
					AND (
						IC.descripcion = 'costo'
						OR IC.descripcion = 'contraPartida_costo'
					);";
		$query=$this->mysql->query($sql,$this->mysql->link);
		while ($row=$this->mysql->fetch_array($query)) {
			$id_items = $row['id_items'];
			$puc      = $row['puc'];
			$this->arrayCuentasItemsNiif[$id_items][$puc]  = array(
																'id_puc'      => $row['id_puc'],
																'estado'      => $row['estado'],
																'descripcion' => $row['descripcion'],
																'centro_costo' => $row['centro_costo'],
															);
		}

	}

	/**
	* @method getAjusteHead consultar la cabecera del documento ajuste
	*/
	public function getAjusteHead()
	{
		$sql="SELECT * FROM inventario_ajuste WHERE activo=1 AND id_empresa=$this->id_empresa AND id=$this->idAjuste";
		$query=$this->mysql->query($sql,$this->mysql->link);

		$this->arrayHeadAjuste['id_sucursal']                 = $this->mysql->result($query,0,'id_sucursal');
		$this->arrayHeadAjuste['id_bodega']                   = $this->mysql->result($query,0,'id_bodega');
		$this->arrayHeadAjuste['consecutivo']                 = $this->mysql->result($query,0,'consecutivo');
		$this->arrayHeadAjuste['fecha_registro']              = $this->mysql->result($query,0,'fecha_registro');
		$this->arrayHeadAjuste['fecha_documento']             = $this->mysql->result($query,0,'fecha_documento');
		$this->arrayHeadAjuste['id_usuario']                  = $this->mysql->result($query,0,'id_usuario');
		$this->arrayHeadAjuste['documento_usuario']           = $this->mysql->result($query,0,'documento_usuario');
		$this->arrayHeadAjuste['usuario']                     = $this->mysql->result($query,0,'usuario');
		$this->arrayHeadAjuste['id_tercero']                  = $this->mysql->result($query,0,'id_tercero');
		$this->arrayHeadAjuste['cod_tercero']                 = $this->mysql->result($query,0,'cod_tercero');
		$this->arrayHeadAjuste['nit']                         = $this->mysql->result($query,0,'nit');
		$this->arrayHeadAjuste['tercero']                     = $this->mysql->result($query,0,'tercero');
		$this->arrayHeadAjuste['observacion']                 = $this->mysql->result($query,0,'observacion');
		$this->arrayHeadAjuste['estado']                      = $this->mysql->result($query,0,'estado');
		$this->arrayHeadAjuste['id_centro_costo']             = $this->mysql->result($query,0,'id_centro_costo');
		$this->arrayHeadAjuste['codigo_centro_costo']         = $this->mysql->result($query,0,'codigo_centro_costo');
		$this->arrayHeadAjuste['centro_costo']                = $this->mysql->result($query,0,'centro_costo');
		$this->arrayHeadAjuste['id_remision_venta']           = $this->mysql->result($query,0,'id_remision_venta');
		$this->arrayHeadAjuste['consecutivo_remision_venta']  = $this->mysql->result($query,0,'consecutivo_remision_venta');
		$this->arrayHeadAjuste['id_entrada_almacen']          = $this->mysql->result($query,0,'id_entrada_almacen');
		$this->arrayHeadAjuste['consecutivo_entrada_almacen'] = $this->mysql->result($query,0,'consecutivo_entrada_almacen');
	}

	/**
	* @method inventoryMovement consultar la cabecera del documento ajuste
	* @param str movimiento a realizar sobre el inventario
	* @param str tabla del inventario del documento a realizar el movimiento
	* @param str id que relaciona la tabla de inventario con la tabla principal
	* @param str id de la tabla principal o cabecera de documento
	* @param str bodega donde se realizara el movimiento de inventario
	* @return boolean si se ejecuta o no el movimiento de inventario
	*/
	public function inventoryMovement($action,$tablaInventario,$idTablaPrincipal,$id,$document_action)
	{

		$tablaPrincipal =  $tablaInventario== 'ventas_remisiones_inventario' ? 'ventas_remisiones' : 'compras_entrada_almacen' ;
		$sql = "SELECT id_sucursal,sucursal,id_bodega,bodega,id_empresa,consecutivo,fecha_inicio
					FROM $tablaPrincipal WHERE id=$id";

		$query = $this->mysql->query($sql);
		$id_empresa  = $this->mysql->result($query,0,"id_empresa");
		$id_sucursal = $this->mysql->result($query,0,"id_sucursal");
		$sucursal    = $this->mysql->result($query,0,"sucursal");
		$id_bodega   = $this->mysql->result($query,0,"id_bodega");
		$bodega      = $this->mysql->result($query,0,"bodega");
		$consecutivo = $this->mysql->result($query,0,"consecutivo");
		$fecha       = $this->mysql->result($query,0,"fecha_inicio");

		// consultar los items de ese documento pero solo los que generan movimiento de inventario
		// $tabla_inventario = ($tablaInventario=="factura")? "ventas_facturas_inventario" : "ventas_remisiones_inventario" ;
		// $campo_id = ($tablaInventario=="factura")? "id_factura_venta" : "id_remision_venta" ;
		$sql = "SELECT 
						id_inventario AS id,
						codigo,
						nombre,
						nombre_unidad_medida AS unidad_medida,
						cantidad_unidad_medida AS cantidad_unidades,
						costo_unitario AS costo,
						cantidad
					FROM $tablaInventario 
					WHERE $idTablaPrincipal=$id
					AND activo=1 
					AND inventariable='true' ";
		$query = $this->mysql->query($sql);
		$index = 0;
		$items = array();
		while ($row = $this->mysql->fetch_assoc($query)) {
			$items[$index]                = $row;
			$items[$index]["empresa_id"]  = $id_empresa;
			$items[$index]["empresa"]     = NULL;
			$items[$index]["sucursal_id"] = $id_sucursal;
			$items[$index]["sucursal"]    = $sucursal;
			$items[$index]["bodega_id"]   = $id_bodega;
			$items[$index]["bodega"]      = $bodega;
			
			$index++;
		}
		include_once '../../Clases/Inventory.php';

		$params = [ 
			"documento_id"          => $id,
			"documento_tipo"        => (($tablaInventario=="ventas_remisiones_inventario")? "RV" : "EA"),
			"documento_consecutivo" => $consecutivo,
			"fecha"                 => $fecha,
			"accion_inventario"     => $action,
			"accion_documento"      => $document_action,    // accion del documento, generar, editar, etc
			"items"                 => $items,
			"mysql"                 => $this->mysql
		];
		$obj = new Inventario_pp();
		$process = $obj->UpdateInventory($params);
		return true;

		// $arrayDatos = ($tablaInventario == 'ventas_remisiones_inventario')? $this->arrayDatosRV : $this->arrayDatosEA ;
		// $arrayDatos["id_documento"]     = "$id";
		// $arrayDatos["tablaInventario"]  = "$tablaInventario";
		// $arrayDatos["idTablaPrincipal"] = "$idTablaPrincipal";

		// if ($action=='add') {
		// 	$sql   = "SELECT SUM(cantidad) AS cantidad_total,
		// 					costo_unitario,
		// 						IF(descuento > 0,
		// 								IF(tipo_descuento = 'porcentaje',
		// 									SUM(cantidad * costo_unitario) - SUM(cantidad * costo_unitario * descuento) / 100 ,
		// 									SUM( (cantidad * costo_unitario) - descuento)
		// 									),
		// 						SUM(cantidad * costo_unitario) ) AS costo_total,
		// 						id_inventario AS id_item
		// 					FROM $tablaInventario
		// 					WHERE $idTablaPrincipal = '$id'
		// 					AND activo = 1
		// 					AND inventariable = 'true'
		// 					GROUP BY id_inventario";
		// }
		// else if ($action=='remove') {
		// 	$sql   = "SELECT SUM(cantidad) AS cantidad_total,
		// 					costo_unitario,
		// 					IF(descuento>0,
		// 							IF(tipo_descuento='porcentaje',
		// 								SUM(cantidad * costo_unitario)-SUM(cantidad * costo_unitario * descuento)/100 ,
		// 								SUM( (cantidad * costo_unitario) - descuento)
		// 								),
		// 					SUM(cantidad * costo_unitario) ) AS costo_total,
		// 					id_inventario AS id_item
		// 				FROM $tablaInventario
		// 				WHERE $idTablaPrincipal='$id'
		// 					AND activo=1
		// 					AND inventariable='true'
		// 				GROUP BY id_inventario ";

		// }

		// // echo $sql;
		// $params['sqlItems']              = $sql;
		// $params['id_bodega']             = $idBodega;
		// $params['event']                 = $action;
		// $params['id_documento']          = $id;
		// $params['nombre_documento']      = ($tablaInventario == 'ventas_remisiones_inventario')? 'Remision de Venta (Ajuste de Inventario)' : 'Entrada de Almacen (Ajuste de Inventario)' ;
		// $params['consecutivo_documento'] = '';
		// $this->objectInventory->updateInventory($params);
		return true;

	}

	/**
	* @method removeCounts consultar la cabecera del documento ajuste
	* @param tipo de documento a eliminar de los asientos
	* @param id del documento a eliminar de los asientos
	* @param sucursal del documento a eliminar de los asientos
	*/
	public function removeCounts($tipoDocumento,$idDocumento,$idSucursal)
	{
		$sql   = "DELETE FROM asientos_colgaap WHERE id_documento='$idDocumento' AND tipo_documento='$tipoDocumento' AND id_sucursal='$idSucursal' AND id_empresa='$this->id_empresa'";
		$query=$this->mysql->query($sql,$this->mysql->link);
		$sql   = "DELETE FROM asientos_niif WHERE id_documento='$idDocumento' AND tipo_documento='$tipoDocumento' AND id_sucursal='$idSucursal' AND id_empresa='$this->id_empresa'";
		$query=$this->mysql->query($sql,$this->mysql->link);
	}

	/**
	 * insertLog insertar el log de los documentos relacionados al ajuste
	 * @param  [String] $action   tipo de accion, generar, editar, cancelar, restaurar
	 * @param  [String] $prefix   prefijo de documento EA = entrada de almacen, etc
	 * @param  [String] $document nombre extendido del documento
	 */
	public function insertLog($action,$prefix,$document)
	{
		$fecha_actual = date('Y-m-d');
		$hora_actual  = date('H:i:s');

		// LOG DE EVENTOS
		$sql = "INSERT INTO log_documentos_contables
					(
						id_documento,
						id_usuario,
						usuario,
						actividad,
						tipo_documento,
						descripcion,
						id_sucursal,
						id_empresa,
						ip,
						fecha,
						hora
					)
					VALUES
					(
						$id,
						'".$_SESSION['IDUSUARIO']."',
						'".$_SESSION['NOMBREUSUARIO']."',
						'Editar',
						'AI',
						'Ajuste Inventario',
						'".$_SESSION['SUCURSAL']."',
						'".$_SESSION['EMPRESA']."',
						'".$_SERVER['REMOTE_ADDR']."',
						'$fecha_actual',
						'$hora_actual'
					)";

		$query=$this->mysql->query($sql,$this->mysql->link);
	}

}


 ?>