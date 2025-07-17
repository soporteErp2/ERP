<?php

	/**
	 * ClassInfoChequeCuenta informe de chequecuentas
	 */
	class ClassInfoFacturaVenta
	{

		public $id_ambiente;
		public $fechaInicio;
		public $fechaFin;
		public $tipo;
		public $numFactura;
		public $id_empresa;
		public $mysql;

		function __construct($id_empresa,$ambiente,$fechaInicio,$fechaFin,$tipo,$numFactura,$mysql){
			$this->id_ambiente = $ambiente;
			$this->fechaInicio = $fechaInicio;
			$this->fechaFin    = $fechaFin;
			$this->tipo        = $tipo;
			$this->numFactura  = $numFactura;
			$this->id_empresa  = $id_empresa;
			$this->mysql       = $mysql;
		}

		/**
		 * getData Consultar los datos de las ventas tipo cheque cuenta
		 * @return [type] [description]
		 */
		public function getData(){
			$where .= ($this->id_ambiente<>'' && $this->id_ambiente<>'Todos')? " AND VP.id_seccion=$this->id_ambiente" : "" ;
			if ($this->tipo<>'' && $this->tipo<>'Todos') {
				if ($this->tipo<>'Facturas') {
					$where .= " AND CP.tipo = '$this->tipo' ";
				}
				else{
					$where .= " AND (CP.tipo <> 'Cheque Cuenta' AND CP.tipo <> 'Cortesia' )";
				}
			}
			$where = ($this->numFactura<>'')? " AND VP.consecutivo='$this->numFactura' " : $where ;
			$sql   = "SELECT
							VP.id,
							VP.consecutivo,
							VP.fecha_documento,
							VP.hora_documento,
							VP.seccion,
							VP.mesa,
							VP.documento_cliente,
							VP.cliente,
							VP.usuario,
							IF(CP.tipo='Cheque Cuenta','Transferencia Cuentas',IF(CP.tipo='Cortesia','Cortesias','Facturas')) AS tipo,
							VPP.forma_pago,
							SUM(VPP.valor) AS valor,
							VP.valor_propina,
							VP.valor_descuento,
							VP.estado
						FROM
							ventas_pos AS VP
						INNER JOIN ventas_pos_formas_pago AS VPP ON VPP.id_pos = VP.id
						INNER JOIN configuracion_cuentas_pago_pos AS CP ON CP.id = VPP.id_forma_pago
						WHERE
							VP.activo = 1
						AND VP.fecha_documento >= '$this->fechaInicio'
						AND VP.fecha_documento <= '$this->fechaFin'
						AND VPP.activo = 1
						$where
						GROUP BY VP.id";
			$query = $this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)){
				switch ($row['estado']) {
					case '1':
						$estado = "Generada";
						break;
					case '2':
						$estado = "Bloqueada";
						break;
					case '3':
						$estado = "Anulada";
						break;
					case '500':
						$estado = "En Error";
						break;
				}
				$arrayPos[$row['id']] = array(
											'fecha_documento'   => $row['fecha_documento'],
											'hora_documento'   => $row['hora_documento'],
											'tipo'              => $row['tipo'],
											'consecutivo'       => $row['consecutivo'],
											'documento_cliente' => $row['documento_cliente'],
											'cliente'           => htmlentities($row['cliente']),
											'valor_metodo'      => $row['valor'],
											'valor_propina'     => $row['valor_propina'],
											'valor_descuento'   => $row['valor_descuento'],
											'estado'            => $estado,
										);

			}

			$wherePos = "id_pos='".implode("' OR id_pos='", array_keys($arrayPos))."'";
			$sql = "SELECT
						id_pos,
						id_item,
						codigo,
						nombre,
						cantidad,
						precio_venta,
						valor_impuesto
					FROM ventas_pos_inventario
					WHERE activo=1 AND id_empresa=$this->id_empresa AND ($wherePos)";
			$query = $this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)){
				$arrayPos[$row['id_pos']]['contItems']++;
				$arrayPos[$row['id_pos']]['items'][] = array(
																'id_pos'       => $row['id_pos'],
																'id_item'      => $row['id_item'],
																'cantidad'     => $row['cantidad'],
																'codigo'       => $row['codigo'],
																'nombre'       => $row['nombre'],
																'precio_venta' => $row['precio_venta'],
																'impuesto'     => $row['valor_impuesto'],
															);
				$arrTaxes[$row['id_pos']][$row['id_item']] = $row['valor_impuesto'];
			}
			//echo json_encode($arrayPos);
			foreach ($arrayPos as $id_pos => $arrayResult){
				$acumNeto     = 0;
				$acumImpuesto = 0;
				$acumExento   = 0;
				
				foreach ($arrayResult['items'] as $key => $arrayResultItems){
					$taxPercent = 0;
					$neto       = 0;
					$subtotal = $arrayResultItems['precio_venta']*$arrayResultItems['cantidad'];
					$acumCantidad += $arrayResultItems['cantidad'];
					$acumTotal    += $subtotal;
					$labelSubtotal = number_format($subtotal,$this->decimalesMoneda,",",".");
    				if ($arrayResult['valor_descuento']>0) {
    					$subtotal = $subtotal-($arrayResult['valor_descuento']/$arrayResult['contItems']);
    				}
					$taxPercent   = ( $arrTaxes[$id_pos][$arrayResultItems['id_item']] * 0.01 )+1;
					$neto         = ROUND($subtotal/$taxPercent);
					$acumNeto     += $neto;
					$acumImpuesto += ROUND(($neto*$arrTaxes[$id_pos][$arrayResultItems['id_item']])/100);

					$acumExento += ($arrTaxes[$id_pos][$arrayResultItems['id_item']]==0 || $arrTaxes[$id_pos][$arrayResultItems['id_item']]==null)?  $arrayResultItems['precio_venta']*$arrayResultItems['cantidad'] : 0;
    				// echo $arrayResultItems['nombre']." tx: ".$arrTaxes[$arrayResultItems['id_item']]." acumExento: $acumExento subtotal: $subtotal taxPercent: $taxPercent neto: $neto <b>acumNeto</b>: $acumNeto acumImpuesto :$acumImpuesto<br>";
				}
				// echo "--------------------------- <br>";
				// echo "exento $acumExento<br>";
				// echo "acumNeto $acumNeto acumImpuesto: $acumImpuesto valor_propina ".$arrayResult['valor_propina']." ".($acumNeto+$acumImpuesto+$arrayResult['valor_propina'])."<br>";

				// echo "valor_metodo: ".$arrayResult['valor_metodo']." total: ".round(($acumNeto+$acumImpuesto+$arrayResult['valor_propina']),2)."<br> ";
				
				if ($arrayResult['valor_metodo']<> round($acumNeto+$acumImpuesto+$arrayResult['valor_propina']) && $acumImpuesto>0){
					$acumNeto = ($arrayResult['valor_metodo']-$acumExento-$arrayResult['valor_propina'])/1.08;
					$acumImpuesto = $acumNeto * 0.08;
					$acumNeto += $acumExento;
				}
				// echo "acumNeto $acumNeto <br>";
				$bodyReturn .= "<tr>
									<td>$arrayResult[fecha_documento]</td>
									<td>$arrayResult[hora_documento]</td>
									<td>$arrayResult[tipo]</td>
									<td>$arrayResult[consecutivo] </td>
									<td>
										<i onclick='prinTDoc($id_pos)' class='material-icons' style='cursor:pointer;' title='Imprimir documento' >print</i>
									</td>
									<td>$arrayResult[documento_cliente]</td>
									<td>$arrayResult[cliente]</td>
									<td>".number_format($acumNeto,0,",",".")."</td>
									<td>".number_format($arrayResult['valor_propina'],0,",",".")."</td>
									<td>".number_format($acumImpuesto,0,",",".")."</td>
									<td>".number_format(($acumNeto+$acumImpuesto+$arrayResult['valor_propina']),0,",",".")."</td>
									<td>$arrayResult[estado]</td>
								</tr>";

				$totalNeto     += $acumNeto;
				$totalImpuesto += $acumImpuesto;
				$acumPropina   += $arrayResult['valor_propina'];
				$total         += $acumNeto+$acumImpuesto+$arrayResult['valor_propina'];
			}

			$bodyReturn .= "<tr>
								<td><b>TOTAL</td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td><b>".number_format($totalNeto,0,",",".")." </td>
								<td><b>".number_format($acumPropina,0,",",".")." </td>
								<td><b>".number_format($totalImpuesto,0,",",".")."</td>
								<td><b>".number_format($total,0,",",".")."</td>
								<td></td>
							</tr>";

			return $bodyReturn;
		}

		public function getView(){
			?>
			<link rel="stylesheet" type="text/css" href="../../../backend/pos_admin/informes/index.css">
			<div class="title">Informe Factura de Venta</div>
			<div class="subtitle">Desde: <?= $this->fechaInicio; ?> Hasta: <?= $this->fechaFin; ?> </div>
			<table id="InformeData">
				<thead>
					<tr>
						<td>Fecha</td>
						<td>Hora</td>
						<td>Tipo</td>
						<td>Numero</td>
						<td></td>
						<td>Identificacion</td>
						<td>Cliente</td>
						<td>Valor Sin Imp.</td>
						<td>Propina</td>
						<td>Impuesto</td>
						<td>Total</td>
						<td>Estado</td>
					</tr>
				</thead>
				<tbody> <?= $this->getData(); ?>
				</tbody>
			</table>
			<script>
				document.getElementById('InformeFile_InformesMovimientos_InfoFacturas').setAttribute("style","width:1024px !important;margin-right:10px;");
			</script>
			<?php
		}

		public function generate(){
			$this->getView();
		}



	}
?>

