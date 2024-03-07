<?php

	// INFORMACION ACADEMICA
	$sql="SELECT id,tipo_estudio,institucion,ciudad,grado,fecha_inicio,fecha_fin,ciclo,modalidad FROM empleados_estudios WHERE id_empleado=$id_empleado";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$bodyTableAcademico .= '<tr ondblclick="ventana_agregar_editar_academicos('.$row['id'].')" id="row_informacion_academica_'.$row['id'].'">
									<td>'.$row['tipo_estudio'].'</td>
									<td>'.$row['institucion'].'</td>
									<td>'.$row['ciudad'].'</td>
									<td>'.$row['grado'].'</td>
									<td>'.$row['fecha_inicio'].'</td>
									<td>'.$row['fecha_fin'].'</td>
									<td>'.$row['ciclo'].'</td>
									<td>'.$row['modalidad'].'</td>
								</tr>';
	}

	// INFORMACION DE IDIOMAS
	$sql="SELECT id,idioma,nativo,institucion,ciudad,lectura,escritura,habla FROM empleados_idiomas WHERE id_empleado=$id_empleado";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$bodyTableIdiomas .= '<tr ondblclick="ventana_agregar_editar_idiomas('.$row['id'].')" id="row_informacion_idiomas_'.$row['id'].'">
								<td>'.$row['idioma'].'</td>
								<td>'.$row['nativo'].'</td>
								<td>'.$row['institucion'].'</td>
								<td>'.$row['ciudad'].'</td>
								<td>'.$row['lectura'].'</td>
								<td>'.$row['escritura'].'</td>
								<td>'.$row['habla'].'</td>
							</tr>';
	}

?>

<style>
	.ssss{
		margin-top: 20px;padding: 5px;width: 94%;float: left;text-align: center;background-color: #2A80B9;color: #fff;font-size: 14px;
	}
</style>
<div class="content-personal-info">
	<div class="separator-body" >ACADEMICOS</div>
	<div class="buttom-content">
		<button class="button" data-value="new" onclick="ventana_agregar_editar_academicos()">Nuevo</button>
	</div>

	<table class="table-grilla">
		<tr class="thead">
			<td>ESTUDIO</td>
			<td>INSTITUCION</td>
			<td>CIUDAD</td>
			<td>GRADO</td>
			<td>FECHA INICIO</td>
			<td>FECHA FIN</td>
			<td>CICLO</td>
			<td>MODALIDAD</td>
		</tr>

		<tbody class="tbody" id="body_grilla_info_academica">
			<?php echo $bodyTableAcademico; ?>
		</tbody>

	</table>
	<div class="separator-body" >IDIOMAS</div>
	<div class="buttom-content">
		<button class="button" data-value="new" onclick="ventana_agregar_editar_idiomas()">Nuevo</button>
	</div>

	<table class="table-grilla">
		<tr class="thead">
			<td>IDIOMA</td>
			<td>NATIVO</td>
			<td>INSTITUCION</td>
			<td>CIUDAD</td>
			<td>LECTURA</td>
			<td>ESCRITURA</td>
			<td>HABLA</td>
		</tr>

		<tbody class="tbody" id="body_grilla_info_idiomas">
			<?php echo $bodyTableIdiomas; ?>
		</tbody>

	</table>

</div>
<script>

	// VENTANA AGREGAR O MODIFICAR UN REGISTRO
	function ventana_agregar_editar_academicos(id) {
		var title = (id>0)? 'Editar Informacion Academica' : 'Nuevo Informacion Academica' ;
		Win_Ventana_agregar_editar_academicos = new Ext.Window({
		    width       : 350,
		    height      : 450,
		    id          : 'Win_Ventana_agregar_editar_academicos',
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : 'perfil_empleado/informacion_academica/agregar_informacion_academica.php',
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

	// VENTANA AGREGAR O MODIFICAR UN REGISTRO
	function ventana_agregar_editar_idiomas(id) {
		var title = (id>0)? 'Editar Idioma' : 'Nuevo Idioma' ;
		Win_Ventana_agregar_editar_idioma = new Ext.Window({
		    width       : 350,
		    height      : 450,
		    id          : 'Win_Ventana_agregar_editar_idioma',
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : 'perfil_empleado/informacion_academica/agregar_informacion_idiomas.php',
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