<?php

	$sql="SELECT id,parentesco,nombre_completo,direccion,telefono,celular,ocupacion FROM empleados_informacion_contacto WHERE activo=1 AND id_empleado=$id_empleado";
	$query=$mysql->query($sql,$mysql->link);
	$style='';
	while($row=$mysql->fetch_array($query)) {
		$bodyTable.='<tr '.$style.' ondblclick="ventana_agregar_editar('.$row['id'].')" id="row_informacion_familiar_'.$row['id'].'">
						<td>'.$row['parentesco'].'</td>
						<td>'.$row['nombre_completo'].'</td>
						<td>'.$row['direccion'].'</td>
						<td>'.$row['telefono'].'</td>
						<td>'.$row['celular'].'</td>
						<td>'.$row['ocupacion'].'</td>
					</tr>';
		$style = ($style=='')? 'style="background-color:#EAF4FA;" ' : '' ;
	}

?>

<style>

</style>
<div class="content-personal-info">
	<div class="buttom-content">
		<button class="button" data-value="new" onclick="ventana_agregar_editar()">Nuevo</button>
	</div>

	<table class="table-grilla">
		<tr class="thead">
			<td>PARENTESCO</td>
			<td>NOMBRE</td>
			<td>DIRECCION</td>
			<td>TELEFONO</td>
			<td>CELULAR</td>
			<td>OCUPACION</td>
		</tr>

		<tbody class="tbody" id="body_grilla_info_familiar">
			<?php echo $bodyTable; ?>
		</tbody>

	</table>

</div>
<script>

	// VENTANA AGREGAR O MODIFICAR UN REGISTRO
	function ventana_agregar_editar(id) {
		var title = (id>0)? 'Editar Registro' : 'Nuevo Registro' ;
		Win_Ventana_agregar_editar = new Ext.Window({
		    width       : 350,
		    height      : 450,
		    id          : 'Win_Ventana_agregar_editar',
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : 'perfil_empleado/informacion_familiar/agregar_informacion_familiar.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
					title       : title,
					id          : id,
					id_empleado : '<?php echo $id_empleado; ?>',
		        }
		    }

		}).show();
	}



</script>