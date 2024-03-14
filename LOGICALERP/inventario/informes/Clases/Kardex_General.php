<?php
	include('../../../../configuracion/conectar.php');
	include('../../../../configuracion/define_variables.php');

	/**
	 * Kardex_General informe 
	 */
	class Kardex_General
	{

		public $id_bodega;
		public $fechaInicio;
		public $fechaFin;
		public $id_empresa;
		public $mysql;
		public $itemInfo;

		function __construct($fechaInicio,$fechaFin,$id_bodega,$mysql,$cod_item)
		{
			if ($fechaInicio == "" || $fechaFin == "") {
				exit("los campos fecha y bodega son obligatorios");
			}

			// Convertir la cadena de fecha a un objeto DateTime
			$fecha_objeto = DateTime::createFromFormat('Y-m-d', $fechaInicio);
					
			// Fecha de referencia '2023-01-25'
			$fecha_referencia = DateTime::createFromFormat('Y-m-d', '2024-01-24');

			if ($fecha_objeto < $fecha_referencia) {                
				?>
					<div style="display: flex;justify-content: space-around;align-items: center;margin-top:2rem" >
						<span style="color:#f44336;font-weight:bold;" >
							La fecha debe ser mayor al 2024-01-24
						</span>
					</div>
				<?php
				exit;
			}

			if ($fechaInicio == "" || $fechaFin == "") {
				exit("los campos fecha y bodega son obligatorios");
			}
			$this->id_bodega   = $id_bodega;
			$this->fechaInicio = $fechaInicio;
			$this->fechaFin    = $fechaFin;
			$this->id_empresa  = $_SESSION["EMPRESA"];
			$this->mysql       = $mysql;
			$this->setItemInfo($cod_item);
		}

		public function getWarehouseInfo()
		{
			$sql = "SELECT nombre FROM empresas_sucursales_bodegas WHERE id=".$this->id_bodega;
			$query = mysql_query($sql,$this->mysql);
			return mysql_result($query,0,'nombre');
		}

		private function setItemInfo($cod_item)
		{	

			$sql = "SELECT id, nombre_equipo as nombre FROM items WHERE codigo=$cod_item AND activo=1";
			$query = mysql_query($sql,$this->mysql);
			while ($row = mysql_fetch_assoc($query)) {
			$this->itemInfo=array(
									id_item => 	$row['id'],
									codigo	=>	$cod_item,
									nombre	=>	$row['nombre']
								);
			}
		}

		private function getPreviousQuantity()
		{
			$previousQuantitySql="SELECT 
										cantidad_anterior 
									FROM 
										logs_inventario 
									WHERE 
									accion_documento = 'Generar'
									AND fecha_documento >= '$this->fechaInicio'
									AND id_item = ".$this->itemInfo['id_item']."
									AND id_bodega = $this->id_bodega 
									AND activo=1
									ORDER BY id ASC
									LIMIT 1";
			$query=mysql_query($previousQuantitySql,$this->mysql);			
			return mysql_result($query,0,'cantidad_anterior');
		}

		public function getData()
		{
			$logTempoSql= "CREATE TEMPORARY TABLE tempologs	SELECT
							MAX(id) AS max_id,
							cantidad as cantidad,
							costo
						FROM 
							logs_inventario
						WHERE 
						  	accion_documento = 'Generar'
						  	AND id_item = ".$this->itemInfo['id_item']."
							AND id_bodega = $this->id_bodega 
						  	AND fecha_documento BETWEEN '$this->fechaInicio' AND '$this->fechaFin'
							AND tipo_documento <> 'POS'
							GROUP BY id_documento";
			$logDocumentsQuery=mysql_query($logTempoSql,$this->mysql);
			
			$logTempoSql= "INSERT INTO tempologs (max_id,cantidad,costo) SELECT
								LI.id AS max_id,
								SUM(LI.cantidad) as cantidad,
								LI.costo_nuevo as costo
							FROM 
								logs_inventario AS LI INNER JOIN ventas_pos as VP ON VP.id = LI.id_documento
							WHERE 
								VP.estado IN(1,2)
								AND LI.id_item = ".$this->itemInfo['id_item']."
								AND LI.id_bodega = $this->id_bodega 
							  	AND LI.fecha_documento BETWEEN '$this->fechaInicio' AND '$this->fechaFin'
								AND LI.tipo_documento = 'POS'
								GROUP BY LI.id_documento";
			$logDocumentsQuery=mysql_query($logTempoSql,$this->mysql);

			$logDocumentsSql= "SELECT
									LI.tipo_documento,
									LI.consecutivo_documento,
									LI.fecha_documento,
									LI.codigo,
									LI.item,
									TL.costo,
									TL.cantidad,
									LI.accion_inventario
								FROM
									logs_inventario as LI INNER JOIN tempologs as TL ON LI.id = TL.max_id
								ORDER BY LI.id";

			$logDocumentsQuery=mysql_query($logDocumentsSql,$this->mysql);

			//Create an array from the query
			$bodyTable = '';
			$saldo = $this->getPreviousQuantity();
				while ($row = mysql_fetch_assoc($logDocumentsQuery)) {
					//Valida si el movimiento es una salida, si es salida el valor es TRUE
					$isSalida = (stripos($row['accion_inventario'], "salida") !== false);
					$saldo = ($isSalida)?$saldo-$row['cantidad']:$saldo+$row['cantidad'];
					$bodyTable .= "<tr>
									<td style='text-align:center;'>$row[codigo]</td> 
									<td style='text-align:center;'>$row[item]</td>
									<td style='text-align:center;'>$row[fecha_documento]</td>
									<td style='text-align:center;'>$row[tipo_documento] $row[consecutivo_documento]</td>
									<td style='text-align:center;'>".((!$isSalida)?$row['cantidad']:'')."</td>
									<td style='text-align:center;'>".((!$isSalida)?$row['costo']:'')."</td>
									<td style='text-align:center;'>".(($isSalida)?$row['cantidad']:'')."</td>
									<td style='text-align:center;'>".(($isSalida)?$row['costo']:'')."</td>
									<td style='text-align:center;'>".$saldo."</td>
								</tr>";
				}
			//DROP TEMPORARAY TABLE
			$dropTempoSql= "DROP TABLE tempologs";
			$dropTempoQuery=mysql_query($dropTempoSql,$this->mysql);
			
			//Agregar al body tableuna fila de totales y saldo actual
			$bodyTable .= 	"<tfoot>
							<tr>
								<td colspan='4' style='text-align:right;'> Saldo actual </td>
								<td colspan='5' style='text-align:right;'>".$saldo."</td>
							</tr>
							</tfoot>";			
		return $bodyTable;
		}

		public function getView()
		{
			?>
			<link rel="stylesheet" type="text/css" href="../../pos/backend/pos_admin/informes/index.css">
			<div class="title">Kardex <?= $this->itemInfo['nombre']; ?></div>
			<div class="subtitle">Bodega: <?= $this->getWarehouseInfo(); ?></div>
			<div class="subtitle">Desde: <?= $this->fechaInicio; ?>  Hasta: <?= $this->fechaFin; ?></div>
			<div class="subtitle">Saldo anterior: <?= $this->getPreviousQuantity(); ?> </div>
			<table id="InformeData">
				<thead>
					<tr>
						<td rowspan="2" style='text-align:center; vertical-align: middle;'>Codigo</td>
						<td rowspan="2" style='text-align:center; vertical-align: middle;'>Nombre</td>
						<td rowspan="2" style='text-align:center; vertical-align: middle;'>Fecha</td>
						<td rowspan="2" style='text-align:center; vertical-align: middle;'>Documento</td>
						<td colspan="2" style='text-align:center;'>Entradas</td>
						<td colspan="2" style='text-align:center;'>Salidas</td>
						<td rowspan="2" style='text-align:center; vertical-align: middle;'>Saldos</td>
					</tr>
					<tr>
						<td style='text-align:center;'>Cantidad</td>
						<td style='text-align:center;'>Costo</td>
						<td style='text-align:center;'>Cantidad</td>
						<td style='text-align:center;'>Costo</td>
					</tr>
				</thead>
				<tbody> <?= $this->getData(); ?>
				</tbody>
			</table>
			<?php
		}

		public function generate()
		{
			$this->getView();
		}



	}

	$obj = new Kardex_General($fechaInicio,$fechaFin,$bodega,$link,$cod_item);
	$obj->generate();
?>

