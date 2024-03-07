<?php

	/**
	 * ClassInventory Gestionar el movimiento de inventario
	 * @param  $mysql 							Conexion mysql a la base de datos
	 * @param  $params 							Parametros de la consulta
	 *         $params[sqlItems]				Sql para consultar los items del documento
	 *         									debe retornar campos con los alias (id_item,cantidad_total,costo_unitario,costo_total)
	 *         									ademas de que debe ser consolidado por item
	 *         $params[id_bodega]				Id de la bodega a actualizar el movimiento de inventario
	 *         $params[id_documento]			id del documento que realiza el movimiento
	 *         $params[nombre_documento]		Nombre o referencia del documento que realiza el documento
	 *         $params[consecutivo_documento]	Consecutivo del documento que realiza el movimiento
	 *
	 */
	class ClassInventory
	{

		public $mysql;
		public $params;

		function __construct($mysql){
			$this->mysql  = $mysql;
		}

		/**
		 * getItems Consultar los items del documento
		 * @return Array Listado de los items del documento y de inventario
		 */
		public function getItems(){
			$query = $this->mysql->query($this->params['sqlItems']);
			// var_dump($this->params);
			// var_dump($query);
			while ($row=$this->mysql->fetch_array($query)) {
				// echo $row['id_item']."<br>";
				$arrayItems['documento'][$row['id_item']] = array(
															'cantidad_total' => $row['cantidad_total'],
															'costo_unitario' => $row['costo_unitario'],
															'costo_total'    => $row['costo_total'],
														);
			}
			$whereItems = " id_item='".implode("' OR id_item='", array_keys($arrayItems['documento']))."' ";
			$sql   = "SELECT id_item,costos,cantidad
						FROM inventario_totales
						WHERE activo=1
						AND id_ubicacion=".$this->params['id_bodega']."
						AND ($whereItems) ";
			$query = $this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)) {
				$arrayItems['inventario'][$row['id_item']] = array(
																'cantidad_total' => $row['cantidad'],
																'costo_unitario' => $row['costos'],
																'costo_total'    => $row['cantidad']*$row['costos'],
															);
			}
			// echo $sql;
			// var_dump( $arrayItems);
			return $arrayItems;
		}

		/**
		 *  updateInventory Actualizar unidades y costo del inventario
		 * @param Array $param Parametros necesarios para el movimiento de inventario
		 */
		public function updateInventory($params){
			$this->params = $params;
			$arrayItems = $this->getItems();
			// print_r($arrayItems);
			foreach ($arrayItems['documento'] as $id_item => $arrayResult){
				$cantidadInv   = $arrayItems['inventario'][$id_item]['cantidad_total'];
				$costoUnitInv  = $arrayItems['inventario'][$id_item]['costo_unitario'];
				$costoInv      = $arrayItems['inventario'][$id_item]['costo_total'];
				$costoUnitDoc  = $arrayResult['costo_unitario'];
				$costoTotalDoc = $arrayResult['costo_total'];
				$cantidadDoc   = $arrayResult['cantidad_total'];

				// CALCULAR EL COSTO POR PROMEDIO DE CADA ITEM

				if ($cantidadInv<=0 || $costoInv<=0 ){
					$costo = $costoUnitDoc;
				}
				if ($this->params['event']<>'add') {
					$costo = $costoUnitInv;
				}
				if (($this->params['nombre_documento']=='Remision de Venta (Ajuste de Inventario)') || ($this->params['nombre_documento']=='Entrada de Almacen (Ajuste de Inventario)')) {
					$costo = $costoUnitDoc;
				}
				else{
					$costo = ($costoInv+$costoTotalDoc)/($cantidadInv+$cantidadDoc);
				}

				// echo "<script>console.log('cantidadInv = $cantidadInv ');</script>";
				// echo "<script>console.log('cantidadDoc = $cantidadDoc ');</script>";
				if($this->params['id_documento'] != 0){
				$event = ($this->params['event']=='add')? ' + ' : ' - ';
				$sql="UPDATE inventario_totales
							SET costos                   = $costo,
							cantidad                     = cantidad $event $cantidadDoc,
							id_documento_update          = '".$this->params['id_documento']."',
							tipo_documento_update        = '".$this->params['nombre_documento']."',
							consecutivo_documento_update = '".$this->params['consecutivo_documento']."'
						WHERE
								id_item = $id_item
		 					AND activo = 1
		 					AND id_ubicacion = '".$this->params['id_bodega']."'";
				$query=$this->mysql->query($sql);
				}
			}

		}

		//
	}


 ?>