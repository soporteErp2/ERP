<?php

	/**
	 * ClassInfoDiarioProductos
	 */
	class ClassInfoDiarioProductos
	{

		public $id_ambiente;
		public $fechaInicio;
		public $fecha;
		public $tipo;
		public $numFactura;
		public $id_empresa;
		public $mysql;

		function __construct($id_empresa,$ambiente,$fecha,$mysql){
			$this->id_ambiente = $ambiente;
			$this->fecha       = $fecha;
			$this->id_empresa  = $id_empresa;
			$this->mysql       = $mysql;
		}

		/**
		 * getData Consultar los datos de las ventas tipo cheque cuenta
		 * @return [type] [description]
		 */
		public function getData(){
			$where .= ($this->id_ambiente<>'' && $this->id_ambiente<>'Todos')? " AND VP.id_seccion=$this->id_ambiente" : "" ;
			$sql   = "SELECT
							VP.id,
							VP.consecutivo,
							VP.fecha_documento,
							VP.seccion,
							VP.mesa,
							VP.documento_cliente,
							VP.cliente,
							VP.usuario,
							IF(CP.tipo='Cheque Cuenta','Transferencia Cuentas',IF(CP.tipo='Cortesia','Cortesias','Facturas')) AS tipo,
							VPP.forma_pago,
							VPP.valor,
							VP.valor_propina,
							VP.seccion,
							VP.valor_descuento
						FROM
							ventas_pos AS VP
						INNER JOIN ventas_pos_formas_pago AS VPP ON VPP.id_pos = VP.id
						INNER JOIN configuracion_cuentas_pago_pos AS CP ON CP.id = VPP.id_forma_pago
						WHERE
							VP.activo = 1
						AND VP.estado<>3
						AND VP.fecha_documento = '$this->fecha'
						AND VPP.activo = 1
						$where
						GROUP BY VP.id";
			$query = $this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)){
				$arrayPos[$row['id']] = array(
											'fecha_documento'   => $row['fecha_documento'],
											'tipo'              => $row['tipo'],
											'consecutivo'       => $row['consecutivo'],
											'documento_cliente' => $row['documento_cliente'],
											'cliente'           => $row['cliente'],
											'valor_propina'     => $row['valor_propina'],
											'valor_descuento'   => $row['valor_descuento'],
											'usuario'           => $row['usuario'],
											'seccion'           => $row['seccion'],
										);

			}

			$wherePos = "id_pos='".implode("' OR id_pos='", array_keys($arrayPos))."'";
			$sql = "SELECT
						id_pos,
						id_item,
						cantidad,
						nombre,
						precio_venta
					FROM ventas_pos_inventario
					WHERE activo=1 AND id_empresa=$this->id_empresa AND ($wherePos)";
			$query = $this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)){
				$whereIdItems .= ($whereIdItems=='')? " I.id=$row[id_item] " : " OR I.id=$row[id_item] " ;
				$arrayPos[$row['id_pos']]['items'][] = array(
																'id_pos'       => $row['id_pos'],
																'id_item'      => $row['id_item'],
																'cantidad'     => $row['cantidad'],
																'precio_venta' => $row['precio_venta'],
																'nombre'       => $row['nombre'],
															);
			}

			$sql   = "SELECT
						I.id,
						I.id_impuesto,
						IM.valor
					FROM
						items AS I
					INNER JOIN impuestos AS IM ON IM.id = I.id_impuesto
					WHERE
						I.activo = 1
					AND($whereIdItems) ";
			$query = $this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)){
				$arrayItems[$row['id']] = $row['valor'];
			}

			foreach ($arrayPos as $id_pos => $arrayResult){

				foreach ($arrayResult['items'] as $key => $arrayResultItems){
					$subtotal = $arrayResultItems['precio_venta']*$arrayResultItems['cantidad'];
					$acumCantidad += $arrayResultItems['cantidad'];
					$acumTotal    += $subtotal;
					$labelSubtotal = number_format($subtotal,$this->decimalesMoneda,",",".");
    				if ($arrayResult['valor_descuento']>0) {
    					$subtotal = $subtotal-($arrayResult['valor_descuento']/$contItems);
    				}
					$taxPercent   = ( $arrayItems[$arrayResultItems['id_item']] * 0.01 )+1;
					$neto         = ROUND($subtotal/$taxPercent);
					$impuesto     = ROUND(($neto*$arrayItems[$arrayResultItems['id_item']])/100);
					$acumNeto     += $neto;
					$acumImpuesto += $impuesto;

					if ( is_array($arrayInfo[$arrayResult['seccion']][$arrayResultItems['id_item']]) ) {
						$arrayInfo[$arrayResult['seccion']][$arrayResultItems['id_item']]['cantidad']   += $arrayResultItems['cantidad'];
						$arrayInfo[$arrayResult['seccion']][$arrayResultItems['id_item']]['valor_neto'] += $neto;
						$arrayInfo[$arrayResult['seccion']][$arrayResultItems['id_item']]['impuesto']   += $impuesto;
						$arrayInfo[$arrayResult['seccion']][$arrayResultItems['id_item']]['total']      += $neto+$impuesto;
					}
					else{
						$arrayInfo[$arrayResult['seccion']][$arrayResultItems['id_item']] = array(
																								"nombre"     => $arrayResultItems["nombre"],
																								"cantidad"   => $arrayResultItems['cantidad'],
																								"valor_neto" => $neto,
																								"impuesto"   => $impuesto,
																								"total"      => $neto+$impuesto,
																								"usuario"    => $arrayResult['usuario'],
																							);
					}

				}

			}

			foreach ($arrayInfo as $ambiente => $arrayInfoResult) {
				foreach ($arrayInfoResult as $id_item => $arrayResult) {
					$bodyReturn .= "<tr>
										<td>$ambiente</td>
										<td>$arrayResult[nombre]</td>
										<td>".number_format($arrayResult['cantidad'],0,",",".")."</td>
										<td>".number_format($arrayResult['valor_neto'],0,",",".")."</td>
										<td>".number_format($arrayResult['impuesto'],0,",",".")."</td>
										<td>".number_format($arrayResult['total'],0,",",".")."</td>
										<td>$arrayResult[usuario]</td>
									</tr>";
				}
			}

			return $bodyReturn;
		}

		public function getView(){
			?>
			<link rel="stylesheet" type="text/css" href="../../../backend/pos_admin/informes/index.css">
			<div class="title">Informe Diario Productos</div>
			<div class="subtitle">Fecha: <?= $this->fecha; ?></div>
			<table id="InformeData">
				<thead>
					<tr>
						<td>Ambiente</td>
						<td>Producto</td>
						<td>Cantidad Vendida</td>
						<td>Valor neto</td>
						<td>Impuesto</td>
						<td>Total</td>
						<td>Usuario</td>
					</tr>
				</thead>
				<tbody>
					<?= $this->getData(); ?>
				</tbody>
			</table>
			<?php
		}

		public function generate(){
			$this->getView();
		}



	}
?>

