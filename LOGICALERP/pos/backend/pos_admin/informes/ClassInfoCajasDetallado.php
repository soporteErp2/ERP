<?php

	/**
	 * ClassInfoCajas informe de chequecuentas
	 */
	class ClassInfoCajasDetallado
	{

		public $id_ambiente;
		public $fecha_inicio;
		public $cajero;
		public $fecha_final;
		public $tipo;
		public $numFactura;
		public $id_empresa;
		public $mysql;

		function __construct($id_empresa,$ambiente,$fecha_inicio,$fecha_final,$cajero,$mysql){
			$this->id_ambiente  = $ambiente;
			$this->fecha_inicio = $fecha_inicio;
			$this->fecha_final  = $fecha_final;
			$this->cajero       = $cajero;
			$this->fechaFin     = $fechaFin;
			$this->tipo         = $tipo;
			$this->numFactura   = $numFactura;
			$this->id_empresa   = $id_empresa;
			$this->mysql        = $mysql;
		}

		/**
		 * getData Consultar los datos de las ventas tipo cheque cuenta
		 * @return [type] [description]
		 */
		public function getData(){
			$where .= ($this->id_ambiente<>'' && $this->id_ambiente<>'Todos')? " AND VP.id_seccion=$this->id_ambiente" : "" ;
			$where .= ($this->cajero<>'' && $this->cajero<>'Todos')? " AND VP.id_usuario=$this->cajero" : "" ;
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
							VP.valor_descuento,
							CP.tipo AS tipo_doc
						FROM
							ventas_pos AS VP
						INNER JOIN ventas_pos_formas_pago AS VPP ON VPP.id_pos = VP.id
						INNER JOIN configuracion_cuentas_pago_pos AS CP ON CP.id = VPP.id_forma_pago
						WHERE
							VP.activo = 1
						AND VP.estado<>3
						AND VP.fecha_documento BETWEEN  '$this->fecha_inicio' AND '$this->fecha_final'
						AND VPP.activo = 1
						$where";
						
			$query = $this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)){
				if ($row['tipo_doc']=='Cortesia') {
					$arrayFormasPago[$row['forma_pago']] += $row['valor'];
					$row['valor']=0;
				}
				$bodyReturn .= "<tr>
									<td>$row[seccion]</td>
									<td>$row[fecha_documento]</td>
									<td>".htmlentities($row[usuario])."</td>
									<td>$row[tipo]</td>
									<td>$row[consecutivo]</td>
									<td>$row[forma_pago]</td>
									<td>".number_format($row['valor'],0,",",".")."</td>
									<td>
										<i onclick='prinTDoc($row[id])' class='material-icons' style='cursor:pointer;' title='Imprimir documento' >print</i>
									</td>
								</tr>";
				$totalNeto     += $acumNeto;
				$totalImpuesto += $acumImpuesto;
				$total         += $row['valor'];
				$arrayFormasPago[$row['forma_pago']] += $row['valor'];

			}


			$bodyReturn .= "<tr>
								<td><b>TOTAL</td>
								<td></td>
								<td></td>
								<td></td>
								<td> </td>
								<td> </td>
								<td><b>".number_format($total,0,",",".")."</td>
								<td></td>
							</tr>
							<tr>
								<td>&nbsp;</td>
							</tr>
							";

			foreach ($arrayFormasPago as $forma_pago => $valor) {
				$bodyReturn .= "<tr>
								<td colspan='2'><b>TOTAL $forma_pago</td>
								<td><b>".number_format($valor,0,",",".")."</td>
								<td></td>
							</tr>";
			}
			return $bodyReturn;
		}

		public function getView(){
			?>
			<link rel="stylesheet" type="text/css" href="../../../backend/pos_admin/informes/index.css">
			<div class="title">Informe Caja Detallado</div>
			<div class="subtitle">Desde: <?= $this->fecha_inicio; ?> Hasta: <?= $this->fecha_final; ?></div>
			<table id="InformeData">
				<thead>
					<tr>
						<td>Ambiente</td>
						<td>Fecha</td>
						<td>Cajero</td>
						<td>Tipo</td>
						<td>Numero</td>
						<td>Forma de pago</td>
						<td>Total</td>
						<td></td>
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

