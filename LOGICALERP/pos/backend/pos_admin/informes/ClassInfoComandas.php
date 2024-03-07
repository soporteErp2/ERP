<?php

	/**
	 * ClassInfoComandas informe de comandas
	 */
	class ClassInfoComandas
	{

		public $id_ambiente;
		public $fechaInicio;
		public $fechaFin;
		public $estado;
		public $id_empresa;
		public $mysql;

		function __construct($id_empresa,$id_ambiente,$fechaInicio,$fechaFin,$estado,$mysql){
			$this->id_ambiente = $id_ambiente;
			$this->fechaInicio = $fechaInicio;
			$this->fechaFin    = $fechaFin;
			$this->estado      = $estado;
			$this->id_empresa  = $id_empresa;
			$this->mysql       = $mysql;
		}

		/**
		 * getData Consultar los datos de las ventas tipo cheque cuenta
		 * @return [type] [description]
		 */
		public function getData(){
			$where .= ($this->id_ambiente<>'' && $this->id_ambiente<>'Todos')? " AND M.id_seccion=$this->id_ambiente" : "" ;
			$where .= ($this->estado>0)? " AND C.estado=$this->estado" : "" ;
			$sql   = "SELECT
							C.id,
							C.usuario,
							C.fecha,
							IF(C.estado=1,'Abierta',IF(C.estado=2,'Cerrada','Anulada')) AS estado,
							MC.nombre_mesa,
							M.codigo,
							M.nombre,
							M.id_seccion,
							M.seccion
						FROM
							ventas_pos_comanda AS C
						INNER JOIN ventas_pos_mesas_cuenta AS MC ON MC.id = C.id_cuenta
						INNER JOIN ventas_pos_mesas AS M ON M.id = MC.id_mesa
						WHERE
							C.activo = 1
						AND C.fecha >= '$this->fechaInicio'
						AND C.fecha <= '$this->fechaFin'
						$where
						";
			$query = $this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)) {
				$arrayComandas[$row['id']] = array(
													'usuario' => htmlentities($row['usuario']),
													'fecha'   => $row['fecha'],
													'estado'  => $row['estado'],
													'seccion' => $row['seccion'],
												);

			}
			$whereComandas = "id_comanda='".implode("' OR id_comanda='", array_keys($arrayComandas))."'";
			$sql="SELECT
						id_comanda,
						id_item,
						cantidad,
						precio
					FROM ventas_pos_mesas_cuenta_items WHERE activo=1 AND ($whereComandas) ";
			$query = $this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)){
				$arrayComandas[$row['id_comanda']]['items'][] = array(
																	'id_item'  => $row['id_item'],
																	'cantidad' => $row['cantidad'],
																	'precio'   => $row['precio'],
																);
			}

			foreach ($arrayComandas as $id_comanda => $arrayResult) {
				$valor = 0;
				foreach ($arrayResult['items'] as $key => $arrayResultItems) {
					$valor += $arrayResultItems['cantidad']*$arrayResultItems['precio'];
				}
				$bodyReturn .= "<tr>
									<td>$arrayResult[seccion]</td>
									<td>$id_comanda</td>
									<td>$arrayResult[usuario]</td>
									<td>$arrayResult[fecha]</td>
									<td>".number_format($valor,0,'.','.') ."</td>
									<td>$arrayResult[estado]</td>
									<td>
										<i onclick='prinTComanda($id_comanda)' class='material-icons' style='cursor:pointer;' title='Imprimir documento' >print</i>
									</td>
								</tr>";
			}



			return $bodyReturn;
		}

		public function getView(){
			?>
			<link rel="stylesheet" type="text/css" href="../../../backend/pos_admin/informes/index.css">
			<div class="title">Informe Comandas</div>
			<div class="subtitle">Desde: <?= $this->fechaInicio; ?> Hasta: <?= $this->fechaFin; ?> </div>
			<table id="InformeData">
				<thead>
					<tr>
						<td>Ambiente</td>
						<td>Numero Cta</td>
						<td>Cajero</td>
						<td>Fecha</td>
						<td>Valor</td>
						<td>Estado</td>
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

