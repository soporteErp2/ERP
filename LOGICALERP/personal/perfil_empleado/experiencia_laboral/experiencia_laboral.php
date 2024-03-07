<?php

	$sql="SELECT id,empresa,nombre_empresa,ciudad,cargo,telefono,fecha_inicio,fecha_fin FROM empleados_experiencia_laboral WHERE activo=1 AND id_empleado=$id_empleado";
	$query=$mysql->query($sql,$mysql->link);
	$style='';
	while($row = $mysql->fetch_array($query)) {
		$bodyTableEL.='<tr '.$style.' ondblclick="ventana_agregar_editar_experiencia_laboral('.$row['id'].')" id="row_informacion_experiencia_laboral_'.$row['id'].'">
						<td>'.$row['nombre_empresa'].'</td>
						<td>'.$row['empresa'].'</td>
						<td>'.$row['cargo'].'</td>
						<td>'.$row['fecha_inicio'].'</td>
						<td>'.$row['fecha_fin'].'</td>
						<td>'.$row['telefono'].'</td>
					</tr>';
		$style = ($style=='')? 'style="background-color:#EAF4FA;" ' : '' ;
	}

?>

<style>

</style>
<div class="content-personal-info">
	<div class="buttom-content">
		<button class="button" data-value="new" onclick="ventana_agregar_editar_experiencia_laboral()">Nuevo</button>
	</div>

	<table class="table-grilla">
		<tr class="thead">
			<td>EMPRESA</td>
			<td>TRABAJO</td>
			<td>CARGO</td>
			<td>FECHA INICIO</td>
			<td>FECHA FIN</td>
			<td>TELEFONO</td>
		</tr>

		<tbody class="tbody" id="body_grilla_info_experiencia_laboral">
			<?php echo $bodyTableEL; ?>
		</tbody>

	</table>

</div>
<script>

	// VENTANA AGREGAR O MODIFICAR UN REGISTRO
	function ventana_agregar_editar_experiencia_laboral(id) {
		var title = (id>0)? 'Editar Registro' : 'Nuevo Registro' ;
		Win_ventana_agregar_editar_experiencia_laboral = new Ext.Window({
		    width       : 350,
		    height      : 580,
		    id          : 'Win_ventana_agregar_editar_experiencia_laboral',
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : 'perfil_empleado/experiencia_laboral/agregar_experiencia_laboral.php',
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