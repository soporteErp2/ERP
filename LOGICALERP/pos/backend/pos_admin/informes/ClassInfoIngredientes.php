<?php
	// include('../../../../../configuracion/conectar.php');
	// include('../../../../../configuracion/define_variables.php');

	/**
	 * ClassInfoIngredientes informe de chequecuentas
	 */
	class ClassInfoIngredientes
	{

		public $fechaInicio;
		public $fechaFin;
		public $id_empresa;
		public $mysql;

		function __construct($id_empresa,$ambiente,$cod_item,$fechaInicio,$fechaFin,$mysql){
			$this->id_ambiente = $ambiente;
			$this->cod_item    = $cod_item;
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
			$where .= ($this->cod_item<>'' )? " AND VPIR.codigo='$this->cod_item' " : "" ;

			$sql   = "SELECT
							VP.consecutivo,
							VP.fecha_documento,
							VP.seccion,
							VP.mesa,
							VP.cliente,
							VP.usuario,
							VPIR.codigo,
							VPIR.nombre,
							SUM(VPIR.cantidad) AS cantidad,
							SUM(VPIR.costo)AS costo,
							items.unidad_medida
						FROM
							ventas_pos AS VP
						INNER JOIN ventas_pos_inventario_receta AS VPIR ON VPIR.id_pos = VP.id
						INNER JOIN items ON items.id = VPIR.id_item
						WHERE
							VP.activo = 1
						AND VP.fecha_documento >= '$this->fechaInicio'
						AND VP.fecha_documento <= '$this->fechaFin'
						AND VPIR.activo = 1
						$where
						GROUP BY VPIR.id_item";
			$query = $this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)) {
				$bodyReturn .= "<tr>
									<td>$row[codigo]</td>
									<td>".htmlentities($row['nombre'])."</td>
									<td>".number_format($row['cantidad'],2,',','.')."</td>
									<td>$row[unidad_medida]</td>
									<td>".round(($row['cantidad']/$row['costo']),2)."</td>
									<td>".number_format($row['costo'],2,',','.')."</td>
								</tr>";
				$acumCant += $row['cantidad'];
				$acumProm += $row['cantidad']/$row['costo'];
				$acumTot  += $row['costo'];
			}

			$bodyReturn .= "<tr>
									<td><b>TOTALES</td>
									<td></td>
									<td><b>".number_format($acumCant,2,',','.')."</td>
									<td><b>$row[unidad_medida]</td>
									<td><b>".round($acumProm,2)."</td>
									<td><b>".number_format($acumTot,2,',','.')."</td>
								</tr>";

			return $bodyReturn;
		}

		public function getView(){
			?>
			<link rel="stylesheet" type="text/css" href="../../../backend/pos_admin/informes/index.css">
			<div class="title">Informe Ingredientes</div>
			<div class="subtitle">Desde: <?= $this->fechaInicio; ?> Hasta: <?= $this->fechaFin; ?> </div>
			<table id="InformeData">
				<thead>
					<tr>
						<td>Codigo</td>
						<td>Producto</td>
						<td>Cant. Consumida</td>
						<td>Unidad</td>
						<td>Costo (Promedio)</td>
						<td>Valor total</td>
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

