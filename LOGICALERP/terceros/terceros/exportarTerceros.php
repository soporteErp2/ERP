<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	header('Content-type: application/vnd.ms-excel;');
    header("Content-Disposition: attachment; filename=tercero_".date("Y-m-d").".xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    // CONSULTAR LOS EMAIL DE LOS TERCEROS
    $sql="SELECT
			TD.id_tercero,
			TDE.email 
		FROM
			terceros_direcciones_email AS TDE
			INNER JOIN terceros_direcciones AS TD ON TD.id = TDE.id_direccion 
			INNER JOIN terceros AS T ON T.id=TD.id_tercero
		WHERE
			TDE.activo = 1 
			AND T.activo = 1
		GROUP BY
			TD.id_tercero";

    $query=$mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)) {
    	$id_tercero = $row['id_tercero'];
    	$arrayEmail[$id_tercero][] = $row['email'];
    }

    // CONSULTAR LOS TERCEROS
	$sql = "SELECT id,tipo_identificacion,numero_identificacion,dv,ciudad_identificacion,nombre,nombre_comercial,direccion,telefono1,telefono2,celular1,celular2,email,pais,departamento,ciudad,representante_legal,sector_empresarial,tercero_tributario,tipo_cliente,tipo_proveedor,exento_iva
					FROM terceros WHERE activo = 1 AND id_empresa = ".$_SESSION['EMPRESA'];
	$query = $mysql->query($sql,$mysql->link);
	while($row = $mysql->fetch_array($query)){
		$emails = '';
		$cont = 0;
		foreach($arrayEmail[$row['id']] as $key => $email){
			$emails .= "<td>$email</td>";
			$cont++;
		}
		$contHead = ($cont>$contHead)? $cont : $contHead;
		$bodyTable .=  "<tr>
											<td>$row[tipo_identificacion]</td>
											<td>&nbsp;$row[numero_identificacion]</td>
											<td>$row[dv]</td>
											<td>$row[ciudad_identificacion]</td>
											<td>$row[nombre]</td>
											<td>$row[nombre_comercial] </td>
											<td>$row[direccion]</td>
											<td>$row[telefono1]</td>
											<td>$row[telefono2]</td>
											<td>$row[celular1]</td>
											<td>$row[celular2]</td>
											<td>$row[email]</td>
											<td>$row[pais]</td>
											<td>$row[departamento]</td>
											<td>$row[ciudad]</td>
											<td>$row[representante_legal]</td>
											<td>$row[sector_empresarial]</td>
											<td>$row[tercero_tributario]</td>
											<td>$row[tipo_cliente]</td>
											<td>$row[tipo_proveedor]</td>
											<td>$row[exento_iva]</td>
											$emails
										</tr>";
	}

?>

<style>
	table{
		font-size: 12px;
		/* font-style: arial,sans-serif; */
		border-collapse: collapse;
	}
</style>
<table>
	<thead>
		<tr>
			<td style="background-color: #2A80B9;color: #FFF;padding: 5px;">TIPO IDENTIFICACION</td>
			<td style="background-color: #2A80B9;color: #FFF;padding: 5px;">NUMERO DE IDENTIFICACION</td>
			<td style="background-color: #2A80B9;color: #FFF;padding: 5px;">DIGITO DE VERIFICACION</td>
			<td style="background-color: #2A80B9;color: #FFF;padding: 5px;">CIUDAD IDENTIFICACION</td>
			<td style="background-color: #2A80B9;color: #FFF;padding: 5px;">NOMBRE</td>
			<td style="background-color: #2A80B9;color: #FFF;padding: 5px;">NOMBRE COMERCIAL</td>
			<td style="background-color: #2A80B9;color: #FFF;padding: 5px;">DIRECCION</td>
			<td style="background-color: #2A80B9;color: #FFF;padding: 5px;">TELEFONO 1</td>
			<td style="background-color: #2A80B9;color: #FFF;padding: 5px;">TELEFONO 2</td>
			<td style="background-color: #2A80B9;color: #FFF;padding: 5px;">CELULAR 1</td>
			<td style="background-color: #2A80B9;color: #FFF;padding: 5px;">CELULAR 2</td>
			<td style="background-color: #2A80B9;color: #FFF;padding: 5px;">EMAIL</td>
			<td style="background-color: #2A80B9;color: #FFF;padding: 5px;">PAIS</td>
			<td style="background-color: #2A80B9;color: #FFF;padding: 5px;">DEPARTAMENTO</td>
			<td style="background-color: #2A80B9;color: #FFF;padding: 5px;">CIUDAD</td>
			<td style="background-color: #2A80B9;color: #FFF;padding: 5px;">REPRESENTANTE LEGAL</td>
			<td style="background-color: #2A80B9;color: #FFF;padding: 5px;">SECTOR EMPRESARIAL</td>
			<td style="background-color: #2A80B9;color: #FFF;padding: 5px;">REGIMEN</td>
			<td style="background-color: #2A80B9;color: #FFF;padding: 5px;">CLIENTE</td>
			<td style="background-color: #2A80B9;color: #FFF;padding: 5px;">PROVEEDOR</td>
			<td style="background-color: #2A80B9;color: #FFF;padding: 5px;">EXENTO IVA</td>
			<?php for ($i=0; $i <= $contHead; $i++) {
			?>
			<td style="background-color: #2A80B9;color: #FFF;padding: 5px;font-weight: bold;">EMAIL <?php echo ($i+1) ?></td>
			<?php
			}?>
		</tr>
	</thead>
	<tbody>
		<?php echo $bodyTable; ?>
	</tbody>

</table>
