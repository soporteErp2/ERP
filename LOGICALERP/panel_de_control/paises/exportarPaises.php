<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	header('Content-type: application/vnd.ms-excel;');
    header("Content-Disposition: attachment; filename=listado_ubicaciones.xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    // CONSULTAR LAS CUENTAS CONTABLES
	$sql="SELECT id_pais,pais,id_departamento,departamento,id,ciudad FROM ubicacion_ciudad WHERE activo=1 ORDER BY ciudad ASC ";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$bodyTable .= "<tr>
							<td>$row[id_pais]</td>
							<td>$row[pais]</td>
							<td>$row[id_departamento]</td>
							<td>$row[departamento]</td>
							<td>$row[id]</td>
							<td>$row[ciudad]</td>
						</tr>";
	}

?>

<style>
	table{
		font-size: 12px;
		font-style: arial,sans-serif;
		border-collapse: collapse;
	}

</style>

<table>
	<thead>
		<tr>
			<td style="background-color: #2A80B9;color: #FFF;padding: 5px;font-weight: bold;">ID PAIS</td>
			<td style="background-color: #2A80B9;color: #FFF;padding: 5px;font-weight: bold;">PAIS</td>
			<td style="background-color: #2A80B9;color: #FFF;padding: 5px;font-weight: bold;">ID DEPARTAMENTO</td>
			<td style="background-color: #2A80B9;color: #FFF;padding: 5px;font-weight: bold;">DEPARTAMENTO</td>
			<td style="background-color: #2A80B9;color: #FFF;padding: 5px;font-weight: bold;">ID CIUDAD</td>
			<td style="background-color: #2A80B9;color: #FFF;padding: 5px;font-weight: bold;">CIUDAD</td>
		</tr>
	</thead>
	<tbody>
		<?php echo $bodyTable; ?>
	</tbody>

</table>