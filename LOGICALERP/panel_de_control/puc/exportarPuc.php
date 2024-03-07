<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	header('Content-type: application/vnd.ms-excel;');
    header("Content-Disposition: attachment; filename=puc_local_".date("Y-m-d").".xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    // CONSULTAR LAS CUENTAS CONTABLES
	$sql="SELECT cuenta,descripcion,cuenta_niif,departamento,ciudad,centro_costo,tipo FROM puc WHERE activo=1 AND id_empresa=$_SESSION[EMPRESA]  ORDER BY CAST(cuenta AS CHAR) ASC ";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$bodyTable .= "<tr>
							<td>$row[cuenta]</td>
							<td>$row[descripcion]</td>
							<td>$row[cuenta_niif]</td>
							<td>$row[departamento]</td>
							<td>$row[ciudad]</td>
							<td>$row[centro_costo]</td>
							<td>$row[tipo]</td>
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
			<td style="background-color: #2A80B9;color: #FFF;padding: 5px;font-weight: bold;">CUENTA</td>
			<td style="background-color: #2A80B9;color: #FFF;padding: 5px;font-weight: bold;">DESCRIPCION</td>
			<td style="background-color: #2A80B9;color: #FFF;padding: 5px;font-weight: bold;">CUENTA NIIF</td>
			<td style="background-color: #2A80B9;color: #FFF;padding: 5px;font-weight: bold;">DEPARTAMENTO</td>
			<td style="background-color: #2A80B9;color: #FFF;padding: 5px;font-weight: bold;">CIUDAD</td>
			<td style="background-color: #2A80B9;color: #FFF;padding: 5px;font-weight: bold;">CENTRO DE COSTOS</td>
			<td style="background-color: #2A80B9;color: #FFF;padding: 5px;font-weight: bold;">TIPO</td>
		</tr>
	</thead>
	<tbody>
		<?php echo $bodyTable; ?>
	</tbody>

</table>