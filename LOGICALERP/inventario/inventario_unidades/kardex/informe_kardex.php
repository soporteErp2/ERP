<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	$id_sucursal       = $_SESSION['SUCURSAL'];
	$id_empresa        = $_SESSION['EMPRESA'];
	$acumSaldo         = 0;
	$fechaIniRegistros = '';

	/**
	* kardex informe del kardex de todos los items
	*/
	class kardex
	{
		public $fecha_corte            = '';
		public $detallado              = '';
		public $id_bodega              = '';
		public $id_sucursal            = '';
		public $id_empresa             = '';
		public $mysql                  = '';
		public $arrayKardexDetallado   = '';
		public $arrayKardexConsolidado = '';
		public $arrayItems             = '';

		function __construct($fecha_corte,$detallado,$id_bodega,$id_sucursal,$id_empresa,$mysql)
		{
			$this->fecha_corte = $fecha_corte;
			$this->detallado   = $detallado;
			$this->id_bodega   = $id_bodega;
			$this->id_sucursal = $id_sucursal;
			$this->id_empresa  = $id_empresa;
			$this->mysql       = $mysql;
		}

		public function getItems()
		{
			$sql="SELECT id,codigo,nombre_equipo,inventariable FROM items WHERE activo=1 AND id_empresa=$this->id_empresa";
			$query=$this->mysql->query($sql,$mysql->link);
			while ($row = $this->mysql->fetch_assoc($query)) {
				$this->arrayItems[$row['id']] = array('codigo' => $row['codigo'], 'nombre' => $row['nombre_equipo'], 'inventariable' => $row['inventariable'] );
			}

			$this->getItemsCantidades();
		}

		public function getItemsCantidades()
		{
			$sql="SELECT id_item,cantidad FROM inventario_totales WHERE activo=1 AND id_empresa=$this->id_empresa AND id_ubicacion=$this->id_bodega";
			$query=$this->mysql->query($sql,$mysql->link);
			while ($row = $this->mysql->fetch_assoc($query)) {
				$this->arrayItems[$row['id_item']]['cantidad'] += $row['cantidad'];
			}
		}

		public function getEntradas()
		{
			$sql = "SELECT
							CF.id,
							CF.nit,
							CF.proveedor AS tercero,
							CF.prefijo_factura,
							CF.numero_factura,
							CF.fecha_inicio AS fecha,
						 	REPLACE(CF.fecha_inicio,'-','') AS indice,
						 	REPLACE(CF.hora_generacion,':','') AS hora,
							CF.id_saldo_inicial,
							CFI.costo_unitario AS costo_inventario,
							SUM(CFI.cantidad) as cantidad,
							CFI.id_inventario,
							CFI.codigo,
							CFI.nombre
					FROM compras_facturas_inventario AS CFI,
						compras_facturas AS CF
					WHERE CF.fecha_inicio <= '$this->fecha_corte'
						AND CF.id_empresa='$this->id_empresa'
						AND CF.id_sucursal='$this->id_sucursal'
						AND CF.id_bodega='$this->id_bodega'
						AND (CF.estado = 1 OR CF.estado=4)
						AND CF.id=CFI.id_factura_compra
						AND CF.activo=1
						AND CFI.activo=1
						AND CFI.check_opcion_contable=''
					GROUP BY CFI.id_factura_compra,CFI.id_inventario, CFI.costo_unitario, CFI.id_inventario
					ORDER BY CF.fecha_inicio ASC";
			$query = $this->mysql->query($sql, $this->mysql->link);
			while ($rowCompras = $this->mysql->fetch_assoc($query)) {

				if($fechaIniRegistros == ''){ $fechaIniRegistros = $rowCompras['fecha']; }
				$consecutivo = ($rowCompras['prefijo_factura'] == '')? $rowCompras['numero_factura']: $rowCompras['prefijo_factura'].' '.$rowCompras['numero_factura'];

				$whereFC .= ($whereFC=='')? "id_factura_compra=".$rowCompras['id'] : " OR id_factura_compra=".$rowCompras['id'] ;

				$indice     = $rowCompras['indice']*1;
				$horaCompra = $rowCompras['hora']*1;
				$this->arrayKardexDetallado[$indice][$horaCompra][$rowCompras['id_inventario']][] = array(
																							'cantidad'       =>$rowCompras['cantidad'],
																							'costo'          =>$rowCompras['costo_inventario'],
																							'tipo'           =>'in',
																							'saldo_inicial'  =>$rowCompras['id_saldo_inicial'],
																							'tipoDocumento'  =>'FC',
																							'numeroDocumento'=>$consecutivo,
																							'descripcion'    =>'',
																							'tercero'        =>$rowCompras['tercero'],
																							'fecha'          =>$rowCompras['fecha'],
																							'nit'            =>$rowCompras['nit'],
																							'item'           =>$rowCompras['nombre']
																						);

				if ( isset($this->arrayKardexConsolidado[$rowCompras['id_inventario']]) ){
				    $this->arrayKardexConsolidado[$rowCompras['id_inventario']]['in'] += $rowCompras['cantidad'];
				}
				else{
					$this->arrayKardexConsolidado[$rowCompras['id_inventario']] = array(
																					'costo' => $rowCompras['costo_inventario'],
																					'in'    => $rowCompras['cantidad'],
																					'item'  => $rowCompras['nombre']
																				);
				}

				// $prueba .= '-'.$rowCompras['cantidad'];
				// $acumSaldo -= $rowCompras['cantidad'];
				// echo '<br>'.$acumSaldo;
			}

			// CONSULTAR LAS ENTRADAS DE ALMACEN QUE NO SE DEBEN MOSTRAR POR QUE SE FACTURARON
			$sql="SELECT id_consecutivo_referencia FROM compras_facturas_inventario WHERE activo=1 AND ($whereFC) AND id_consecutivo_referencia > 0";
			$query= $this->mysql->query($sql, $this->mysql->link);
			while ($row=mysql_fetch_array($query)) {
				$whereEA .= ($whereEA=='')? "CF.id<>".$row['id_consecutivo_referencia'] : " AND CF.id<>".$row['id_consecutivo_referencia'] ;
			}

			$whereEA =($whereEA<>'')? " $whereEA " : "" ;

			$sqlCompras = "SELECT CF.proveedor AS tercero,
									CF.nit,
									CF.consecutivo,
									CF.fecha_registro AS fecha,
									CFI.costo_unitario AS costo_inventario,
									SUM(CFI.cantidad) as cantidad,
									CFI.id_inventario,
									CFI.codigo,
									CFI.nombre
							FROM compras_entrada_almacen_inventario AS CFI,
								compras_entrada_almacen AS CF
							WHERE CF.fecha_registro <= '$this->fecha_corte'
								AND CF.id_empresa='$this->id_empresa'
								AND CF.id_sucursal='$this->id_sucursal'
								AND CF.id_bodega='$this->id_bodega'
								AND (CF.estado = 1 OR CF.estado=2 OR CF.estado=4)
								AND CF.id=CFI.id_entrada_almacen
								AND CF.activo=1
								$whereEA
								AND CFI.activo=1
							GROUP BY CFI.id_entrada_almacen, CFI.id_inventario, CFI.costo_unitario
							ORDER BY CF.fecha_registro ASC";
			$queryCompras = $this->mysql->query($sqlCompras, $this->mysql->link);
			while ($rowCompras = $this->mysql->fetch_assoc($queryCompras)) {

				if($fechaIniRegistros == ''){ $fechaIniRegistros = $rowCompras['fecha']; }
				$consecutivo = $rowCompras['consecutivo'];

				$indice     = $rowCompras['indice']*1;
				$horaCompra = $rowCompras['hora']*1;
				$this->arrayKardexDetallado[$indice][$horaCompra][$rowCompras['id_inventario']][] = array(
																							'cantidad'       =>$rowCompras['cantidad'],
																							'costo'          =>$rowCompras['costo_inventario'],
																							'tipo'           =>'in',
																							'saldo_inicial'  =>$rowCompras['id_saldo_inicial'],
																							'tipoDocumento'  =>'EA',
																							'numeroDocumento'=>$consecutivo,
																							'descripcion'    =>'',
																							'tercero'        =>$rowCompras['tercero'],
																							'fecha'          =>$rowCompras['fecha'],
																							'nit'            =>$rowCompras['nit'],
																							'item'           =>$rowCompras['nombre']

																						);

				if (isset($this->arrayKardexConsolidado[$rowCompras['id_inventario']])) {
				    $this->arrayKardexConsolidado[$rowCompras['id_inventario']]['in'] += $rowCompras['cantidad'];
				}
				else{
					$this->arrayKardexConsolidado[$rowCompras['id_inventario']] = array(
																					'in'    =>$rowCompras['cantidad'],
																					'costo' =>$rowCompras['costo_inventario'],
																					'item'  =>$rowCompras['nombre']
																				);
				}

				$prueba .= '-'.$rowCompras['cantidad'];
				$acumSaldo -= $rowCompras['cantidad'];
				// echo '<br>'.$acumSaldo;
			}

			//==================// DEVOLUCIONES VENTAS //==================//
			//*************************************************************//
			$sqlDevVentas = "SELECT DV.cliente AS tercero,
									DV.nit,
									DV.documento_venta,
									DV.numero_documento_venta,
									DV.consecutivo,
									DV.fecha_finalizacion AS fecha,
								 	REPLACE(DV.fecha_finalizacion,'-','') AS indice,
								 	REPLACE(DV.hora_finalizacion,':','') AS hora,
									DVI.costo_inventario AS costo_inventario,
									SUM(DVI.cantidad) as cantidad,
									DVI.id_inventario,
									DVI.codigo,
									DVI.nombre
							FROM devoluciones_venta_inventario AS DVI,
								devoluciones_venta AS DV
							WHERE DV.fecha_finalizacion <= '$this->fecha_corte'
								AND DV.id_empresa='$this->id_empresa'
								AND DV.id_sucursal='$this->id_sucursal'
								AND DV.id_bodega='$this->id_bodega'
								AND (DV.estado = 1 OR DV.estado=4)
								AND DV.id=DVI.id_devolucion_venta
								AND DV.activo=1
								AND DVI.activo=1
							GROUP BY DVI.id_devolucion_venta, DVI.id_inventario, DVI.costo_inventario
							ORDER BY DV.fecha_finalizacion ASC";
			$queryDevVentas = $this->mysql->query($sqlDevVentas, $this->mysql->link);
			while ($rowVentas = $this->mysql->fetch_assoc($queryDevVentas)) {

				$documento = ($rowVentas['documento_venta'] == 'Remision')? 'NDRV': 'NDFV';
				$indice    = $rowVentas['indice']*1;
				$horaVenta = $rowVentas['hora']*1;
				$this->arrayKardexDetallado[$indice][$horaVenta][$rowVentas['id_inventario']][] = array(
																							'cantidad'       => $rowVentas['cantidad'],
																							'costo'          => $rowVentas['costo_inventario'],
																							'tipo'           => 'in',
																							'tipoDocumento'  => $documento,
																							'numeroDocumento'=> $rowVentas['consecutivo'],
																							'descripcion'    => 'Cruce '.substr($documento, 2).' #'.$rowVentas['numero_documento_venta'],
																							'tercero'        => $rowVentas['tercero'],
																							'fecha'          => $rowVentas['fecha'],
																							'nit'            =>$rowVentas['nit'],
																							'item'           =>$rowVentas['nombre']
																						);

				if (isset($this->arrayKardexConsolidado[$rowVentas['id_inventario']])) {
				    $this->arrayKardexConsolidado[$rowVentas['id_inventario']]['in'] += $rowVentas['cantidad'];
				}
				else{
					$this->arrayKardexConsolidado[$rowVentas['id_inventario']] = array(
																					'in'    =>$rowVentas['cantidad'],
																					'costo' =>$rowVentas['costo_inventario'],
																					'item'  =>$rowVentas['nombre']
																				);
				}

				$prueba .= '-'.$rowVentas['cantidad'];
				$acumSaldo += $rowVentas['cantidad'];
				// echo '<br>'.$acumSaldo;
			}
		}

		public function getSalidas()
		{
			$sqlVentas = "SELECT VF.cliente AS tercero,
							VF.nit,
							VF.numero_factura_completo AS consecutivo,
							VF.fecha_inicio AS fecha,
						 	REPLACE(VF.fecha_inicio,'-','') AS indice,
						 	REPLACE(VF.hora_inicio,':','') AS hora,
							VF.id_saldo_inicial,
							VFI.costo_inventario,
							SUM(VFI.cantidad) as cantidad,
							VFI.id_inventario,
							VFI.codigo,
							VFI.nombre
					FROM ventas_facturas_inventario AS VFI,
						ventas_facturas AS VF
					WHERE VF.fecha_inicio <= '$this->fecha_corte'
						AND VF.id_empresa='$this->id_empresa'
						AND VF.id_sucursal='$this->id_sucursal'
						AND VF.id_bodega='$this->id_bodega'
						AND (VF.estado = 1 OR VF.estado=4)
						AND VF.id=VFI.id_factura_venta
						AND VF.activo=1
						AND VFI.activo=1
					GROUP BY VFI.id_factura_venta, VFI.id_inventario, VFI.costo_inventario
					ORDER BY VF.fecha_inicio ASC";
			$queryVentas = $this->mysql->query($sqlVentas, $this->mysql->link);
			while ($rowVentas = $this->mysql->fetch_assoc($queryVentas)) {

				$indice    = $rowVentas['indice']*1;
				$horaVenta = $rowVentas['hora']*1;
				$this->arrayKardexDetallado[$indice][$horaVenta][$rowVentas['id_inventario']][] = array(
																							'cantidad'       =>$rowVentas['cantidad'],
																							'costo'          =>$rowVentas['costo_inventario'],
																							'tipo'           =>'out',
																							'saldo_inicial'  =>$rowVentas['id_saldo_inicial'],
																							'tipoDocumento'  =>'FV',
																							'numeroDocumento'=>$rowVentas['consecutivo'],
																							'descripcion'    =>'',
																							'tercero'        =>$rowVentas['tercero'],
																							'fecha'          =>$rowVentas['fecha'],
																							'nit'            =>$rowVentas['nit'],
																							'item'           =>$rowVentas['nombre']
																						);

				if (isset($this->arrayKardexConsolidado[$rowVentas['id_inventario']])) {
				    $this->arrayKardexConsolidado[$rowVentas['id_inventario']]['out'] += $rowVentas['cantidad'];
				}
				else{
					$this->arrayKardexConsolidado[$rowVentas['id_inventario']] = array(
																					'out'   =>$rowVentas['cantidad'],
																					'costo' =>$rowVentas['costo_inventario'],
																					'item'  =>$rowVentas['nombre']
																				);
				}

				$prueba .= '+'.$rowVentas['cantidad'];
				$acumSaldo += $rowVentas['cantidad'];
				// echo '<br>'.$acumSaldo;
			}

			$sqlVentas = "SELECT VF.cliente AS tercero,
									VF.nit,
									VF.consecutivo,
									VF.fecha_inicio AS fecha,
									VFI.costo_inventario,
									SUM(VFI.cantidad) as cantidad,
									VFI.id_inventario,
									VFI.codigo,
									VFI.nombre
							FROM ventas_remisiones_inventario AS VFI,
								ventas_remisiones AS VF
							WHERE VF.fecha_inicio <= '$this->fecha_corte'
								AND VF.id_empresa='$this->id_empresa'
								AND VF.id_sucursal='$this->id_sucursal'
								AND VF.id_bodega='$this->id_bodega'
								AND (VF.estado = 1 OR VF.estado=2 OR VF.estado=4)
								AND VF.id=VFI.id_remision_venta
								AND VF.activo=1
								AND VFI.activo=1
							GROUP BY VFI.id_remision_venta, VFI.id_inventario, VFI.costo_inventario
							ORDER BY VF.fecha_inicio ASC";
			$queryVentas = $this->mysql->query($sqlVentas, $this->mysql->link);
			while ($rowVentas = $this->mysql->fetch_assoc($queryVentas)) {

				$indice    = $rowVentas['indice']*1;
				$horaVenta = $rowVentas['hora']*1;
				$this->arrayKardexDetallado[$indice][$horaVenta][$rowVentas['id_inventario']][] = array(
																						'cantidad'       =>$rowVentas['cantidad'],
																						'costo'          =>$rowVentas['costo_inventario'],
																						'tipo'           =>'out',
																						'saldo_inicial'  =>$rowVentas['id_saldo_inicial'],
																						'tipoDocumento'  =>'RV',
																						'numeroDocumento'=>$rowVentas['consecutivo'],
																						'descripcion'    =>'',
																						'tercero'        =>$rowVentas['tercero'],
																						'fecha'          =>$rowVentas['fecha'],
																						'nit'            =>$rowVentas['nit'],
																						'item'           =>$rowVentas['nombre']

																					);
				if (isset($this->arrayKardexConsolidado[$rowVentas['id_inventario']])) {
				    $this->arrayKardexConsolidado[$rowVentas['id_inventario']]['out'] += $rowVentas['cantidad'];
				}
				else{
					$this->arrayKardexConsolidado[$rowVentas['id_inventario']] = array(
																					'out'   =>$rowVentas['cantidad'],
																					'costo' =>$rowVentas['costo_inventario'],
																					'item'  =>$rowVentas['nombre']
																				);
				}
				$prueba .= '+'.$rowVentas['cantidad'];
				$acumSaldo += $rowVentas['cantidad'];
				// echo '<br>'.$acumSaldo;
			}

			$sqlDevCompras = "SELECT DC.proveedor AS tercero,
							DC.nit,
							DC.documento_compra,
							DC.numero_documento_compra,
							DC.consecutivo,
							DC.fecha_finalizacion AS fecha,
						 	REPLACE(DC.fecha_finalizacion,'-','') AS indice,
						 	REPLACE(DC.hora_finalizacion,':','') AS hora,
							DCI.costo_unitario AS costo_inventario,
							SUM(DCI.cantidad) as cantidad,
							DCI.id_inventario,
							DCI.codigo,
							DCI.nombre
					FROM devoluciones_compra_inventario AS DCI,
						devoluciones_compra AS DC
					WHERE DC.fecha_finalizacion <= '$this->fecha_corte'
						AND DC.id_empresa='$this->id_empresa'
						AND DC.id_sucursal='$this->id_sucursal'
						AND DC.id_bodega='$this->id_bodega'
						AND (DC.estado = 1 OR DC.estado=4)
						AND DC.id=DCI.id_devolucion_compra
						AND DC.activo=1
						AND DCI.activo=1
					GROUP BY DCI.id_devolucion_compra, DCI.id_inventario, DCI.costo_unitario
					ORDER BY DC.fecha_finalizacion ASC";
				$queryDevCompras = $this->mysql->query($sqlDevCompras, $this->mysql->link);
			while ($rowCompras = $this->mysql->fetch_assoc($queryDevCompras)) {

				$documento  = ($rowCompras['documento_venta'] == 'Remision')? 'NDRC': 'NDFC';
				$indice     = $rowCompras['indice']*1;
				$horaCompra = $rowCompras['hora']*1;
				$this->arrayKardexDetallado[$indice][$horaCompra][$rowCompras['id_inventario']][] = array(
																						'cantidad'       =>$rowCompras['cantidad'],
																						'costo'          =>$rowCompras['costo_inventario'],
																						'tipo'           =>'out',
																						'tipoDocumento'  =>$documento,
																						'numeroDocumento'=>$rowCompras['consecutivo'],
																						'descripcion'    =>'Cruce '.substr($documento, 2).' #'.$rowCompras['numero_documento_venta'],
																						'tercero'        =>$rowCompras['tercero'],
																						'fecha'          =>$rowCompras['fecha'],
																						'nit'            =>$rowCompras['nit'],
																						'item'           =>$rowCompras['nombre']

																						);
				if (isset($this->arrayKardexConsolidado[$rowCompras['id_inventario']])) {
				    $this->arrayKardexConsolidado[$rowCompras['id_inventario']]['out'] += $rowCompras['cantidad'];
				}
				else{
					$this->arrayKardexConsolidado[$rowCompras['id_inventario']] = array(
																					'out'   =>$rowCompras['cantidad'],
																					'costo' =>$rowCompras['costo_inventario'],
																					'item'  =>$rowCompras['nombre']
																				);
				}

				$prueba .= '+'.$rowCompras['cantidad'];
				$acumSaldo -= $rowCompras['cantidad'];
				// echo '<br>'.$acumSaldo;
			}
		}

		public function getTraslados()
		{
			$sqlTraslados   = "SELECT
									id_equipo,
									fecha AS dateTime,
									cantidad,
									consecutivo,
									costo,
									IF(id_sucursal_origen='$this->id_sucursal' AND id_bodega_origen='$this->id_bodega','out','in') AS tipo,
									nombre_equipo,
									(SELECT codigo FROM items WHERE activo=1 AND id=id_equipo) AS codigo
								FROM inventario_totales_traslados
								WHERE activo=1
								AND id_empresa='$this->id_empresa'
								AND (id_sucursal_origen='$this->id_sucursal' AND id_bodega_origen='$this->id_bodega'
									OR
									id_sucursal_destino='$this->id_sucursal' AND id_bodega_destino='$this->id_bodega')";
			$queryTraslados = $this->mysql->query($sqlTraslados,$this->mysql->link);
			while ($row = $this->mysql->fetch_assoc($queryTraslados)) {
				$arrayDateTime = explode(' ', $row['dateTime']);
				$date = $arrayDateTime[0];
				$time = $arrayDateTime[1];

				$indice = str_replace('-', '', $date);
				$hora   = str_replace(':', '', $time);

				$this->arrayKardexDetallado[$indice][$hora][$row['id_equipo']] = array(
																					'cantidad'        =>$row['cantidad'],
																					'costo'           =>$row['costo'],
																					'tipo'            =>$row['tipo'],
																					'tipoDocumento'   =>'Traslado',
																					'numeroDocumento' =>$row['consecutivo'],
																					'descripcion'     =>'',
																					'tercero'         =>$row['tercero'],
																					'fecha'           =>$date
																				);

				if($row['tipo'] == 'in'){
					if (isset($this->arrayKardexConsolidado[$row['id_equipo']])) {
					    $this->arrayKardexConsolidado[$row['id_equipo']]['in'] += $row['cantidad'];
					}
					else{
						$this->arrayKardexConsolidado[$row['id_equipo']] = array(
																						'in'   =>$row['cantidad'],
																						'costo' =>$row['costo_inventario'],
																						'item'  =>$row['nombre']
																					);
					}
				}
				else{
					if (isset($this->arrayKardexConsolidado[$row['id_equipo']])) {
					    $this->arrayKardexConsolidado[$row['id_equipo']]['out'] += $row['cantidad'];
					}
					else{
						$this->arrayKardexConsolidado[$row['id_equipo']] = array(
																						'out'   =>$row['cantidad'],
																						'costo' =>$row['costo_inventario'],
																						'item'  =>$row['nombre']
																					);
					}
				}


			}
		}

		public function getAjustesNotas()
		{
			# inventario_movimiento_notas
			$sql="SELECT id FROM nota_contable_general WHERE activo=1 AND id_empresa=$this->id_empresa AND id_sucursal=$this->id_sucursal AND fecha_nota <= $this->fecha_corte ";
			$query=$this->mysql->query($sql,$mysql->link);
			while ($row = $this->mysql->fetch_assoc($query)) {
				$whereIdNotas .=($whereIdNotas=='')? " id_nota " : "";
			}
		}

		public function getDetailReport()
		{
			foreach ($this->arrayKardexDetallado as $indiceDate => $arrayDate){

				ksort($arrayDate);
				foreach ($arrayDate as $time => $arrayTime) {
					foreach ($arrayTime AS $id_inventario => $arrayItem){
						// print_r($arrayItem);
						foreach ($arrayItem as $key => $arrayFila) {
								if ($this->arrayItems[$id_inventario]['inventariable']=='false') { continue; }
								// print_r($arrayFila);
								$contFilas++;
								$inCantidad  = '';
								$inValor     = '';
								$inCosto     = '';

								$outCantidad = '';
								$outValor    = '';
								$outCosto    = '';

								//COSTO TOTAL KARDEX
								$costo_total = $arrayFila['cantidad']*1 * $arrayFila['costo']*1;

								$dateDocumento = $arrayFila['fecha'];
								if($arrayFila['tipo'] == 'in'){
									$inCantidad = $arrayFila['cantidad']*1;
									$inValor    = $arrayFila['costo']*1;
									$inCosto    = $costo_total;

								}
								else if($arrayFila['tipo'] == 'out'){
									$outCantidad = $arrayFila['cantidad']*1;
									$outValor    = $arrayFila['costo']*1;
									$outCosto    = $costo_total;
								}

								$unidadesIngresadas += $inCantidad;
								$unidadesSacadas    += $outCantidad;

								$bodyTable .= "<tr>
												<td>".$this->arrayItems[$id_inventario]['codigo']."</td>
												<td>".$this->arrayItems[$id_inventario]['nombre']."</td>
												<td>$dateDocumento</td>
												<td>$arrayFila[tipoDocumento]</td>
												<td>$arrayFila[numeroDocumento]</td>
												<td>$arrayFila[nit]</td>
												<td>$arrayFila[tercero]</td>
												<td>$inCantidad</td>
												<td>$inCosto</td>
												<td>$outCantidad</td>
												<td>$outCosto</td>
											</tr>";
						}
					}
				}
			}

			?>
				<style>
					table{
						font-family     : arial,sans-serif;
						margin-top      : 20px;
						font-size       : 12px;
						margin-left     : 10px;
						border-collapse : collapse;
					}
					.title {
						background-color : #2A80B9;
						color            : #FFF;
						text-align       : center;
						font-size        : 14px;
					}
				</style>
				<table>
					<tr>
						<td rowspan="2"  class="title">CODIGO</td>
						<td rowspan="2"  class="title">ITEM</td>
						<td rowspan="2"  class="title">FECHA</td>
						<td rowspan="2"  class="title">TIPO DOCUMENTO</td>
						<td rowspan="2"  class="title">CONSECUTIVO</td>
						<td rowspan="2"  class="title">NIT</td>
						<td rowspan="2"  class="title">TERCERO</td>
						<td colspan="2"  class="title">ENTRADAS</td>
						<td colspan="2"  class="title">SALIDAS</td>
					</tr>
					<tr>
						<td class="title"  class="title">Cantidad</td>
						<td class="title"  class="title">Valor</td>
						<td class="title"  class="title">Cantidad</td>
						<td class="title"  class="title">Valor</td>
					</tr>
					<?php echo $bodyTable; ?>
				</table>
			<?php
		}

		public function getconsolidatedReport()
		{
			asort($this->arrayKardexConsolidado);
			foreach ($this->arrayKardexConsolidado as $id_inventario => $arrayResult) {
				if ($this->arrayItems[$id_inventario]['inventariable']=='false') { continue; }
				$bodyTable .= "<tr>
								<td>".$this->arrayItems[$id_inventario]['codigo']."</td>
								<td>".$this->arrayItems[$id_inventario]['nombre']."</td>
								<td>$arrayResult[in]</td>
								<td>$arrayResult[out]</td>
								<td>".($arrayResult['in']-$arrayResult['out'])."</td>
								<td>".$this->arrayItems[$id_inventario]['cantidad']."</td>
								<td>".(($arrayResult['in']-$arrayResult['out'])-$this->arrayItems[$id_inventario]['cantidad'])."</td>
							</tr>";
			}

			?>
				<style>
					table{
						font-family     : arial,sans-serif;
						margin-top      : 20px;
						font-size       : 12px;
						margin-left     : 10px;
						border-collapse : collapse;
					}
					.title {
						background-color : #2A80B9;
						color            : #FFF;
						text-align       : center;
						font-size        : 14px;
					}
				</style>
				<table>
					<tr>
						<td rowspan="2" class="title" >CODIGO</td>
						<td rowspan="2" class="title" >ITEM</td>
						<td class="title">ENTRADAS</td>
						<td class="title">SALIDAS</td>
						<td class="title">SALDO</td>
						<td rowspan="2" class="title">CANT. INVENTARIO</td>
						<td rowspan="2" class="title">DIFERENCIA</td>
					</tr>
					<tr>
						<td class="title">Cantidad</td>
						<td class="title">Cantidad</td>
						<td class="title">Cantidad</td>
					</tr>
					<?php echo $bodyTable; ?>
				</table>
			<?php
		}

		public function createReport()
		{
			$this->getItems();
			$this->getEntradas();
			$this->getSalidas();
			$this->getTraslados();
			//header('Content-type: application/vnd.ms-excel;');
   			//header("Content-Disposition: attachment; filename=kardex_inventario_".date('Y_m_d').".xls");
   			//header("Pragma: no-cache");
   			//header("Expires: 0");

			if ($this->detallado=='Si') { $this->getDetailReport(); }
			else{ $this->getconsolidatedReport(); }

		}
	}

	$objeto = new kardex($fecha_corte,$detallado,$id_bodega,$id_sucursal,$id_empresa,$mysql);
	$objeto->createReport();
