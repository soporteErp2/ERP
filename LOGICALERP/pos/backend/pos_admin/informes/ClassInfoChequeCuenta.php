<?php
	// include('../../../../../configuracion/conectar.php');
	// include('../../../../../configuracion/define_variables.php');

	/**
	 * ClassInfoChequeCuenta informe de chequecuentas
	 */
	class ClassInfoChequeCuenta
	{

		public $id_ambiente;
		public $fechaInicio;
		public $fechaFin;
		public $id_empresa;
		public $mysql;

		function __construct($id_empresa,$id_ambiente,$fechaInicio,$fechaFin,$mysql){
			$this->id_ambiente = $id_ambiente;
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
			$where = ($this->id_ambiente<>'' && $this->id_ambiente<>'Todos')? " AND VP.id_seccion=$this->id_ambiente" : "" ;
			$sql   = "SELECT
							VP.consecutivo,
							VP.fecha_documento,
							VP.seccion,
							VP.mesa,
							VP.cliente,
							VP.usuario,
							CP.tipo,
							VP.valor_propina,
							VPP.forma_pago,
							VPP.valor
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
						AND CP.tipo = 'Cheque Cuenta'";
			$query = $this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)) {
				$bodyReturn .= "<tr>
									<td>$row[consecutivo]</td>
									<td>$row[fecha_documento]</td>
									<td>$row[seccion]</td>
									<td>$row[mesa]</td>
									<td>".htmlentities($row['usuario'])."</td>
									<td>".htmlentities($row['cliente'])."</td>
									<td>".number_format($row['valor_propina'],0,",",".")." </td>
									<td>".number_format($row['valor'],0,",",".")." </td>
								</tr>";
				$acumPropina += $row['valor_propina'];
				$acumTotal   += $row['valor'];
			}

			$bodyReturn .= "<tr>
								<td><b>TOTAL</td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td><b>".number_format($acumPropina,0,",",".")." </td>
								<td><b>".number_format($acumTotal,0,",",".")." </td>
							</tr>";

			return $bodyReturn;
		}

		public function getView(){
			?>
			<link rel="stylesheet" type="text/css" href="../../../backend/pos_admin/informes/index.css">
			<div class="title">Informe Cheque Cuenta</div>
			<div class="subtitle">Desde: <?= $this->fechaInicio; ?> Hasta: <?= $this->fechaFin; ?> </div>
			<table id="InformeData">
				<thead>
					<tr>
						<td>Numero Cta</td>
						<td>Fecha</td>
						<td>Ambiente</td>
						<td>Mesa</td>
						<td>Cajero</td>
						<td>Cliente</td>
						<td>Propina</td>
						<td>Total</td>
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

