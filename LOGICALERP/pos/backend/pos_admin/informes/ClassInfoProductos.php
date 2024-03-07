<?php
	// include('../../../../../configuracion/conectar.php');
	// include('../../../../../configuracion/define_variables.php');

	/**
	 * ClassInfoProductos informe de productos
	 */
	class ClassInfoProductos
	{

		public $id_ambiente;
		public $fechaInicio;
		public $fechaFin;
		public $id_empresa;
		public $mysql;

		function __construct($id_empresa,$mysql){
			// $this->id_ambiente = $id_ambiente;
			// $this->fechaInicio = $fechaInicio;
			// $this->fechaFin    = $fechaFin;
			$this->id_empresa  = $id_empresa;
			$this->mysql       = $mysql;
		}

		/**
		 * getData Consultar los datos de las ventas tipo cheque cuenta
		 * @return [type] [description]
		 */
		public function getData(){
			$sql   = "SELECT
							I.familia,
							I.grupo,
							I.subgrupo,
							I.nombre_equipo,
							I.precio_venta_1,
							I.precio_venta_2,
							I.precio_venta_3,
							I.precio_venta_4,
							I.precio_venta_5,
							IMP.impuesto,
							IMP.valor
						FROM
							items AS I
						LEFT JOIN impuestos AS IMP ON IMP.id = I.id_impuesto
						WHERE
							I.activo = 1
							AND I.modulo_pos='true'
						";
			$query = $this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)) {
				$labelImp = ($row['impuesto']<>'')? "$row[impuesto] ($row[valor])" : "" ;
				$bodyReturn .= "<tr>
									<td>$row[familia]</td>
									<td>$row[grupo]</td>
									<td>$row[subgrupo]</td>
									<td>".htmlentities($row['nombre_equipo'])."</td>
									<td>$row[precio_venta_1]</td>
									<td>$row[precio_venta_2]</td>
									<td>$row[precio_venta_3]</td>
									<td>$row[precio_venta_4]</td>
									<td>$row[precio_venta_5]</td>
									<td>$labelImp</td>
								</tr>";
			}
			return $bodyReturn;
		}

		public function getView(){
			?>
			<link rel="stylesheet" type="text/css" href="../../../backend/pos_admin/informes/index.css">
			<div class="title">Informe Productos</div>
			<!-- <div class="subtitle">Desde: <?= $this->fechaInicio; ?> Hasta: <?= $this->fechaFin; ?> </div> -->
			<table id="InformeData">
				<thead>
					<tr>
						<td>Familia</td>
						<td>Grupo</td>
						<td>Subgrupo</td>
						<td>Producto</td>
						<td>Precio 1</td>
						<td>Precio 2</td>
						<td>Precio 3</td>
						<td>Precio 4</td>
						<td>Precio 5</td>
						<td>Impuesto</td>
					</tr>
				</thead>
				<tbody> <?= $this->getData(); ?>
				</tbody>
			</table>
			<script>
				document.getElementById('InformeFile_InformesProductos_InfoProductos').setAttribute("style","width:1024px !important;margin-right:10px;");
			</script>
			<?php
		}

		public function generate(){
			$this->getView();
		}



	}
?>

