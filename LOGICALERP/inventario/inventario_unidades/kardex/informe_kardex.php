<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$id_empresa  =$_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

class KardexReport
{
	public function __construct($id_bodega,$id_empresa,$id_sucursal,$fecha_corte,$isDetallado, $mysql){
		$this->idBodega      	= $id_bodega;
		$this->idEmpresa     	= $id_empresa;
		$this->mysql         	= $mysql;
		$this->fechaCorte    	= $fecha_corte;
		$this->isDetallado	 	= $isDetallado;
		$this->idItem		 	= 12;
		$this->warehouseName 	= $this->setWarehouseName();
		$this->bodyTable  	 	= $this->setBodyTable();
		$this->previousQuantity = $this->setPreviousQuantity();


	}
	private function setWarehouseName()
	{
		$warehouseNameSql="SELECT nombre FROM empresas_sucursales_bodegas WHERE activo=1 AND id = $this->idBodega";
		$query=mysql_query($warehouseNameSql,$this->mysql);			
		return mysql_result($query,0,'nombre');
	}

	private function setBodyTable()
	{
		return ($this->isDetallado)? $this->generateDetailedReport() : '';
	}

	private function setPreviousQuantity()
	{
		$previousQuantitySql="SELECT 
									MIN(id) as id,
									cantidad_anterior 
								FROM 
									logs_inventario 
								WHERE 
								fecha_documento >= $this->fechaCorte
								AND id_item = $this->idItem
							 	AND id_bodega = $this->idBodega 
							 	AND activo=1";
		$query=mysql_query($previousQuantitySql,$this->mysql);			
		return mysql_result($query,0,'cantidad_anterior');
	}

	private function generateDetailedReport()
	{
			// Search all documents with inventory movements
		$logDocumentsSql= "SELECT
								id,
							 	tipo_documento,
								consecutivo_documento,
								fecha_documento,
								accion_documento,
								fecha_movimiento,
								hora_movimiento,
								costo,
								cantidad,
								usuario
							 FROM
							 	logs_inventario
							 WHERE 
							 	fecha_documento >= $this->fechaCorte
								AND id_item = $this->idItem
							 	AND id_bodega = $this->idBodega 
							 	AND activo=1
							 ORDER BY id ASC";
		
		$logDocumentsQuery=mysql_query($logDocumentsSql,$this->mysql);

		//Create an array from the query
		$bodyTable = '';
		while ($row = mysql_fetch_assoc($logDocumentsQuery)) {

			$bodyTable .= "<tr>
							<td>$row[fecha_movimiento]</td>
							<td>$row[hora_movimiento]</td>
							<td>$row[tipo_documento]</td>
							<td>$row[consecutivo_documento]</td>
							<td>$row[accion_documento]</td>
							<td>$row[costo]</td>
							<td>$row[cantidad]</td>
							<td>$row[usuario]</td>
						  </tr>";			
		}
		return $bodyTable;
	}

}	
	$informeKardex = new KardexReport($id_bodega,$id_empresa,$id_sucursal,$fecha_corte, TRUE, $link);
	header('Content-type: application/vnd.ms-excel');
 	header("Content-Disposition: attachment; filename=listado_de_inventario_".(date("Y-m-d")).".xls");
 	header("Pragma: no-cache");
 	header("Expires: 0");
?>

<html>
<body>

	<table>
		<tr>
			<td colspan="8" style="border:none; text-align:center;font-size: 15px;font-weight : bold;"><?php echo $_SESSION['NOMBREEMPRESA'] ?></td>
		</tr>
		<tr>
			<td colspan="8" style="border:none; text-align:center;font-size:14px">Kardex</td>
		</tr>
		<tr>
			<td colspan="8" style="border:none; text-align:center;font-size:14px"><?php echo $_SESSION['NOMBRESUCURSAL'] ?></td>
		</tr>
		<tr>
			<td colspan="8" style="border:none; text-align:center;font-size:14px"><?php echo $informeKardex->warehouseName; ?></td>
		</tr>
		<tr>
			<td colspan="8" style="border:none; text-align:center;font-size:14px">Saldo cantidad: <?php echo $informeKardex->previousQuantity; ?></td>
		</tr>
	</table>

	<table border="1">
		<tr>
			<td style="height: 30px; text-align:center; vertical-align: middle; font-size: 14px;background-color : #2A80B9;color: #fff;">fecha movimiento</td>
			<td style="height: 30px; text-align:center; vertical-align: middle; font-size: 14px;background-color : #2A80B9;color: #fff;">hora movimiento</td>
			<td style="height: 30px; text-align:center; vertical-align: middle; font-size: 14px;background-color : #2A80B9;color: #fff;">tipo documento</td>
			<td style="height: 30px; text-align:center; vertical-align: middle; font-size: 14px;background-color : #2A80B9;color: #fff;">consecutivo documento</td>
			<td style="height: 30px; text-align:center; vertical-align: middle; font-size: 14px;background-color : #2A80B9;color: #fff;">accion documento</td>
			<td style="height: 30px; text-align:center; vertical-align: middle; font-size: 14px;background-color : #2A80B9;color: #fff;">costo</td>
			<td style="height: 30px; text-align:center; vertical-align: middle; font-size: 14px;background-color : #2A80B9;color: #fff;">cantidad</td>
			<td style="height: 30px; text-align:center; vertical-align: middle; font-size: 14px;background-color : #2A80B9;color: #fff;">usuario</td>
		</tr>
		<tbody>
			<?php echo $informeKardex->bodyTable; ?>
		</tbody>
	</table>

</body>
</html>
