<?php

	/**
	 * ClassInfoPropinas
	 */
	class ClassInfoPropinas
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
						AND VP.fecha_documento >= '$this->fechaInicio'
						AND VP.fecha_documento <= '$this->fechaFin'
						AND VPP.activo = 1
						AND VP.valor_propina>0
						$where
						GROUP BY VP.id";
			$query = $this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)){
				$arrayPos[$row['id']] = array(
											'fecha_documento'   => $row['fecha_documento'],
											'tipo'              => $row['tipo'],
											'consecutivo'       => $row['consecutivo'],
											'documento_cliente' => $row['documento_cliente'],
											'cliente'           => utf8_decode($row['cliente']),
											'valor_propina'     => $row['valor_propina'],
											'valor_descuento'   => $row['valor_descuento'],
											'usuario'           => utf8_decode($row['usuario']),
											'seccion'           => $row['seccion'],
										);

			}
			// var_dump($sql);
			$wherePos = "id_pos='".implode("' OR id_pos='", array_keys($arrayPos))."'";
			$sql = "SELECT
						id_pos,
						id_item,
						cantidad,
						precio_venta
					FROM ventas_pos_inventario
					WHERE activo=1 AND id_empresa=$this->id_empresa AND ($wherePos)";
			$query = $this->mysql->query($sql);
			$contItems = 0;
			while ($row=$this->mysql->fetch_array($query)){
				$contItems++;
				$whereIdItems .= ($whereIdItems=='')? " I.id=$row[id_item] " : " OR I.id=$row[id_item] " ;
				$arrayPos[$row['id_pos']]['items'][] = array(
																'id_pos'       => $row['id_pos'],
																'id_item'      => $row['id_item'],
																'cantidad'     => $row['cantidad'],
																'precio_venta' => $row['precio_venta'],
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
			//echo json_encode($arrayPos);
			foreach ($arrayPos as $id_pos => $arrayResult){
				$acumNeto = 0;
				$acumImpuesto = 0;
				foreach ($arrayResult['items'] as $key => $arrayResultItems){
					$subtotal = $arrayResultItems['precio_venta']*$arrayResultItems['cantidad'];
					$acumCantidad += $arrayResultItems['cantidad'];
					// $acumTotal    += $subtotal;
					$labelSubtotal = number_format($subtotal,$this->decimalesMoneda,",",".");
    				if ($arrayResult['valor_descuento']>0) {
    					$subtotal = $subtotal-($arrayResult['valor_descuento']/$contItems);
    				}
					$taxPercent   = ( $arrayItems[$arrayResultItems['id_item']] * 0.01 )+1;
					$neto         = ROUND($subtotal/$taxPercent);
					$acumNeto     += $neto;
					$acumImpuesto += ROUND(($neto*$arrayItems[$arrayResultItems['id_item']])/100);
				}

				$bodyReturn .= "<tr>
									<td>$arrayResult[fecha_documento]</td>
									<td>$arrayResult[seccion]</td>
									<td>$arrayResult[usuario]</td>
									<td>$arrayResult[tipo]</td>
									<td>$arrayResult[consecutivo]</td>
									<td>".number_format(($acumNeto+$acumImpuesto),0,",",".")."</td>
									<td>".number_format($arrayResult['valor_propina'],0,",",".")."</td>
								</tr>";
				$acumTotal   += $acumNeto+$acumImpuesto;
				$acumPropina += $arrayResult['valor_propina'];
			}

			$bodyReturn .= "<tr>
								<td><b>TOTAL</td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td><b>".number_format($acumTotal,0,",",".")."</td>
								<td><b>".number_format($acumPropina,0,",",".")."</td>
							</tr>";

			return $bodyReturn;
		}

		public function getView(){
			?>
			<link rel="stylesheet" type="text/css" href="../../../backend/pos_admin/informes/index.css">
			<div class="title">Informe Propinas</div>
			<div class="subtitle">Desde: <?= $this->fechaInicio; ?> Hasta: <?= $this->fechaFin; ?></div>
			<table id="InformeData">
				<thead>
					<tr>
						<td>Fecha</td>
						<td>Ambiente</td>
						<td>Cajero</td>
						<td>Tipo</td>
						<td>Numero</td>
						<td>Vlr Dococumento</td>
						<td>Vlr Propina</td>
					</tr>
				</thead>
				<tbody> <?= $this->getData(); ?>
				</tbody>
			</table>
			<?php
		}

		public function generate(){
			$this->getView();
		}



	}
?>

