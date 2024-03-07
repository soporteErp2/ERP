<?php


	/**
	 * ClassInfoCubiertos
	 */
	class ClassInfoCubiertos
	{

		public $id_ambiente;
		public $fechaInicio;
		public $fechaFin;
		public $tipo;
		public $numFactura;
		public $id_empresa;
		public $mysql;

		function __construct($id_empresa,$ambiente,$fechaInicio,$fechaFin,$mysql){
			$this->id_ambiente = $ambiente;
			$this->fechaInicio = $fechaInicio;
			$this->fechaFin    = $fechaFin;
			$this->id_empresa  = $id_empresa;
			$this->mysql       = $mysql;
		}

		/**
		 * getData Consultar los datos de las ventas tipo cheque cuenta
		 */
		public function getData(){
			$where .= ($this->id_ambiente<>'' && $this->id_ambiente<>'Todos')? " AND VP.id_seccion=$this->id_ambiente" : "" ;
			$where = ($numFactura<>'')? " AND VP.consecutivo='$numFactura' " : $where ;
			$sql   = "SELECT
							VP.id,
							VP.consecutivo,
							VP.fecha_documento,
							VP.seccion,
							VP.id_mesa,
							VP.mesa,
							VP.documento_cliente,
							VP.cliente,
							VP.usuario,
							IF(CP.tipo='Cheque Cuenta','Transferencia Cuentas',IF(CP.tipo='Cortesia','Cortesias','Facturas')) AS tipo,
							VPP.forma_pago,
							VPP.valor,
							VPI.valor_impuesto,
							VP.valor_propina,
							VP.valor_descuento,
							VP.estado
						FROM
							ventas_pos AS VP
						INNER JOIN ventas_pos_formas_pago AS VPP ON VPP.id_pos = VP.id
						INNER JOIN configuracion_cuentas_pago_pos AS CP ON CP.id = VPP.id_forma_pago
						INNER JOIN ventas_pos_inventario AS VPI ON VPI.id_pos = VPP.id_pos
						WHERE
							VP.activo = 1
						AND VP.fecha_documento >= '$this->fechaInicio'
						AND VP.fecha_documento <= '$this->fechaFin'
						AND VPP.activo = 1
						AND CP.tipo <> 'Cortesia'
						$where
						GROUP BY VP.id";
			$query = $this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)){
				$valor_impuesto = ($row['valor_impuesto'] == null)? 0 : $row['valor_impuesto'];
				$valor_impuesto = $valor_impuesto / 100;


				$arrayMesas[$row['id_mesa']] = $row['id_mesa'];
				$subtotal = $row['valor'] / (1 + $valor_impuesto);
				$impuesto = $subtotal * $valor_impuesto;
				$total    = $subtotal+$impuesto+$row['valor_propina'];

				if ($arrayDocs[$row['fecha_documento']][$row['seccion']]) {
					$arrayDocs[$row['fecha_documento']][$row['seccion']]['subtotal']   += $subtotal;
					$arrayDocs[$row['fecha_documento']][$row['seccion']]['impuestos']  += $impuesto;
					$arrayDocs[$row['fecha_documento']][$row['seccion']]['propina']    += $row['valor_propina'];
					$arrayDocs[$row['fecha_documento']][$row['seccion']]['descuentos'] += $row['valor_descuento'];
				}
				else{
					$arrayDocs[$row['fecha_documento']][$row['seccion']] = array(
																					'subtotal'   => $subtotal,
																					'impuestos'  => $impuesto,
																					'propina'    => $row['valor_propina'],
																					'descuentos' => $row['valor_descuento'],
																				);
				}

			}
			// var_dump($sql);
			$whereMesas = "MC.id_mesa='".implode("' OR MC.id_mesa='", array_keys($arrayMesas))."'";
			$sql = "SELECT
							M.codigo,
							M.nombre,
							M.id_seccion,
							M.seccion,
							MC.id AS id_cuenta,
							MC.fecha_apertura,
							MC.estado,
							MCC.cantidad
						FROM
							ventas_pos_mesas_cuenta AS MC
						INNER JOIN ventas_pos_mesas AS M ON M.id=MC.id_mesa
						INNER JOIN ventas_pos_mesas_cuenta_comensales AS MCC ON MCC.id_cuenta = MC.id
						WHERE
							MC.activo = 1
						AND MC.id_empresa = $this->id_empresa
						AND MC.estado     = 'Cerrada'
						AND ($whereMesas)
						AND MC.fecha_apertura BETWEEN '$this->fechaInicio' AND '$this->fechaFin'
						ORDER BY fecha_apertura ASC";
			$query = $this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)){
				$arrayDocs[$row['fecha_apertura']][$row['seccion']]['cantidad'] += $row['cantidad'];
			}

			foreach ($arrayDocs as $fecha => $arrayDocsResul) {
				foreach ($arrayDocsResul as $seccion => $arrayResult) {
					$total = $arrayResult['subtotal']+$arrayResult['impuestos']+$arrayResult['valor_propina'];
					$bodyReturn .="<tr>
										<td>$fecha</td>
										<td>$seccion</td>
										<td>$arrayResult[cantidad]</td>
										<td>".number_format($arrayResult['subtotal'],0,",",".")."</td>
										<td>".number_format($arrayResult['impuestos'],0,",",".")."</td>
										<td>".number_format($total,0,",",".")."</td>
									</tr>";
					$acumCantidad  += $arrayResult['cantidad'];
					$totalNeto     += $arrayResult['subtotal'];
					$totalImpuesto += $arrayResult['impuestos'];
					$acumTotal     += $total;
				}
			}


			$bodyReturn .= "<tr>
								<td><b>TOTAL</td>
								<td></td>
								<td style='text-align:center;'><b>".number_format($acumCantidad,0,",",".")."</td>
								<td><b>".number_format($totalNeto,0,",",".")." </td>
								<td><b>".number_format($totalImpuesto,0,",",".")."</td>
								<td><b>".number_format($acumTotal,0,",",".")."</td>
								<td></td>
							</tr>";

			return $bodyReturn;
		}

		public function getView(){
			?>
			<link rel="stylesheet" type="text/css" href="../../../backend/pos_admin/informes/index.css">
			<div class="title">Informe de cubiertos</div>
			<div class="subtitle">desde: <?= $this->fechaInicio; ?> hasta: <?= $this->fechaFin; ?></div>
			<table id="InformeData">
				<thead>
					<tr>
						<td>Fecha</td>
						<td>Ambiente</td>
						<td>Cantidad Comensales</td>
						<td>Subtotal</td>
						<td>Impuestos</td>
						<td>Total</td>
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

