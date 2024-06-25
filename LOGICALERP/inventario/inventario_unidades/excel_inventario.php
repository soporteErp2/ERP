<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$id_empresa  =$_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	if ($fecha_inventario<>'') {
		$sql="SELECT * FROM inventario_totales_log_mensual WHERE id_empresa=$id_empresa AND id_sucursal=$id_sucursal AND id_bodega=$filtro_ubicacion AND inventariable='true'  ";
		$query=$mysql->query($sql,$mysql->link);
		while ($row=$mysql->fetch_array($query)) {
			$costos = number_format($row['costo'],2,$separador_decimales,$separador_miles);
			$bodyTable .=  "<tr>
								<td >$row[familia]</td>
								<td >$row[grupo]</td>
								<td >$row[subgrupo]</td>
								<td style='mso-number-format:\"\@\";'>$row[codigo]</td>
								<td ></td>
								<td >$row[nombre]</td>
								<td >$row[cantidad]</td>
								<td >$costos</td>
								<td >$row[precio_venta]</td>
							</tr>";
		}
	}
	else{
		$sql="SELECT * FROM inventario_totales WHERE activo=1 AND id_empresa=$id_empresa AND id_sucursal=$id_sucursal AND id_ubicacion=$filtro_ubicacion AND inventariable='true' ";
		$query=$mysql->query($sql,$mysql->link);
		while ($row=$mysql->fetch_array($query)) {
			$costos = number_format($row['costos'],2,$separador_decimales,$separador_miles);
			$bodyTable .=  "<tr>
												<td >$row[familia]</td>
												<td >$row[grupo]</td>
												<td >$row[subgrupo]</td>
												<td style='mso-number-format:\"\@\";'>$row[codigo]</td>
												<td >$row[code_bar]</td>
												<td >$row[nombre_equipo]</td>
												<td >$row[unidad_medida]</td>
												<td >$row[cantidad]</td>
												<td >$costos</td>
												<td >$row[precio_venta]</td>
											</tr>";
		}

		$sql="SELECT nombre FROM empresas_sucursales_bodegas WHERE id=$filtro_ubicacion";
		$query=$mysql->query($sql,$mysql->link);
		$nombre_bodega = $mysql->result($query,0,'nombre');

	}
	
	header('Content-type: application/vnd.ms-excel');
 	header("Content-Disposition: attachment; filename=listado_de_inventario_".(date("Y-m-d")).".xls");
 	header("Pragma: no-cache");
 	header("Expires: 0");
?>

<html>
<body>

	<table>
		<tr>
			<td colspan="10"><?php echo $aqui; ?></td>
		</tr>
		<tr>
			<td colspan="10" style="border:none; text-align:center;font-size: 15px;font-weight : bold;"><?php echo $_SESSION['NOMBREEMPRESA'] ?></td>
		</tr>
		<tr>
			<td colspan="10" style="border:none; text-align:center;font-size:14px">Listado de Inventario</td>
		</tr>
		<tr>
			<td colspan="10" style="border:none; text-align:center;font-size:14px"><?php echo $_SESSION['NOMBRESUCURSAL'] ?></td>
		</tr>
		<tr>
			<td colspan="10" style="border:none; text-align:center;font-size:14px"><?php echo $nombre_bodega; ?></td>
		</tr>
		<tr>
			<td style="border:none;">&nbsp;</td>
		</tr>
	</table>

	<table border="1">
		<tr>
			<td style="height: 30px; text-align:center; vertical-align: middle; font-size: 14px;background-color : #2A80B9;color: #fff;">FAMILIA</td>
			<td style="height: 30px; text-align:center; vertical-align: middle; font-size: 14px;background-color : #2A80B9;color: #fff;">GRUPO</td>
			<td style="height: 30px; text-align:center; vertical-align: middle; font-size: 14px;background-color : #2A80B9;color: #fff;">SUBGRUPO</td>
			<td style="height: 30px; text-align:center; vertical-align: middle; font-size: 14px;background-color : #2A80B9;color: #fff;">CODIGO INTERNO</td>
			<td style="height: 30px; text-align:center; vertical-align: middle; font-size: 14px;background-color : #2A80B9;color: #fff;">CODIGO DE BARRAS</td>
			<td style="height: 30px; text-align:center; vertical-align: middle; font-size: 14px;background-color : #2A80B9;color: #fff;">ARTICULO</td>
			<td style="height: 30px; text-align:center; vertical-align: middle; font-size: 14px;background-color : #2A80B9;color: #fff;">UNIDAD DE MEDIDA</td>
			<td style="height: 30px; text-align:center; vertical-align: middle; font-size: 14px;background-color : #2A80B9;color: #fff;">CANTIDAD</td>
			<td style="height: 30px; text-align:center; vertical-align: middle; font-size: 14px;background-color : #2A80B9;color: #fff;">COSTO</td>
			<td style="height: 30px; text-align:center; vertical-align: middle; font-size: 14px;background-color : #2A80B9;color: #fff;">PRECIO VENTA</td>
		</tr>
		<tbody>
			<?php echo $bodyTable; ?>
		</tbody>
	</table>

</body>
</html>
