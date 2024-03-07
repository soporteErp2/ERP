<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	header('Content-Type: text/html; charset=utf-8');

	// INFORMACION ACADEMICA
	$sql="SELECT id,tipo_estudio,institucion,ciudad,grado,fecha_inicio,fecha_fin,ciclo,modalidad FROM empleados_estudios WHERE id_empleado=$id_empleado AND id=$id";
	$query=$mysql->query($sql,$mysql->link);

	$tipo_estudio = $mysql->result($query,0,'tipo_estudio');
	$institucion  = $mysql->result($query,0,'institucion');
	$ciudad       = $mysql->result($query,0,'ciudad');
	$grado        = $mysql->result($query,0,'grado');
	$fecha_inicio = $mysql->result($query,0,'fecha_inicio');
	$fecha_fin    = $mysql->result($query,0,'fecha_fin');
	$ciclo        = $mysql->result($query,0,'ciclo');
	$modalidad    = $mysql->result($query,0,'modalidad');

	if ($tipo_estudio <> '') {
		$acumscript .= "document.getElementById('informacion_academica_tipo_estudio').value='$tipo_estudio';";
	}
	if ($ciclo <> '') {
		$acumscript .= "document.getElementById('informacion_academica_ciclo').value='$ciclo';";
	}
	if ($modalidad <> '') {
		$acumscript .= "document.getElementById('informacion_academica_modalidad').value='$modalidad';";
	}

?>

<style>

</style>
<div class="content" >
	<div class="separator"><?php echo $title; ?><div class="close" onclick="Win_Ventana_agregar_editar_academicos.close();"></div></div>

	<div class="buttom-content" style="height: 55px;">
		<button class="button" data-value="save" onclick="agregar_editar_informacion_academica()" style="padding-top: 37px;height: 37px;width: 50px;background-size: contain;background-position: center;">Guardar</button>
		<?php
			if ($title=='Editar Informacion Academica') {
				?>
					<button class="button" data-value="delete" onclick="eliminar_informacion_academica()" style="padding-top: 37px;height: 37px;width: 50px;background-size: contain;background-position: center;">Eliminar</button>
				<?php
			}
		?>
	</div>

	<table class="table-form" style="width:90%;" >
		<tr>
			<td>Estudio</td>
			<td>
				<select id="informacion_academica_tipo_estudio" style="width:150px" data-requiere="true">
					<option value="">Seleccione...</option>
					<option value="Primaria">Primaria</option>
					<option value="Secundaria">Secundaria</option>
					<option value="Universitario Pregrado">Universitario Pregrado</option>
					<option value="Universitario Diplomado">Universitario Diplomado</option>
					<option value="Universitario Especializacion">Universitario Especializacion</option>
					<option value="Universitario Maestrias">Universitario Maestrias</option>
					<option value="Otro">Otro</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Titulo Obtenido</td>
			<td><input type="text" value="<?php echo $grado; ?>" style="width:150px;" data-requiere="true" id="informacion_academica_titulo_obtenido"></td>
		</tr>
		<tr>
			<td>Institucion</td>
			<td><input type="text" style="width:150px;" value="<?php echo $institucion; ?>" data-requiere="true" id="informacion_academica_institucion"></td>
		</tr>
		<tr>
			<td>Fecha Inicio</td>
			<td><input type="text" value="<?php echo $fecha_inicio; ?>" style="width:150px;" id="informacion_academica_fecha_inicio"></td>
		</tr>
		<tr>
			<td>Fecha Finalizacion</td>
			<td><input type="text" value="<?php echo $fecha_fin; ?>" style="width:150px;" id="informacion_academica_fecha_fin"></td>
		</tr>
		<tr>
			<td>Ciclo</td>
			<td>
				<select id="informacion_academica_ciclo" style="width:150px" data-requiere="true">
					<option value="">Seleccione...</option>
					<option value="Diurno">Diurno</option>
					<option value="Nocturno">Nocturno</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Modalidad</td>
			<td>
				<select id="informacion_academica_modalidad" style="width:150px" data-requiere="true">
					<option value="">Seleccione...</option>
					<option value="Presencial">Presencial</option><option value="Online">Online</option><option value="Otro">Otro</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Ciudad</td>
			<td><input type="text" value="<?php echo $ciudad; ?>" style="width:150px;" id="informacion_academica_ciudad" data-requiere="true"></td>
		</tr>
	</table>
</div>
<script>
	<?php echo $acumscript; ?>

	new Ext.form.DateField({
	    format     : 'Y-m-d',               //FORMATO
	    width      : 150,                   //ANCHO
	    showToday  : false,
	    applyTo    : 'informacion_academica_fecha_inicio',
	    editable   : false,
	    allowBlank : false,
	    listeners  : { select: function() {   } }
	});

	new Ext.form.DateField({
	    format     : 'Y-m-d',               //FORMATO
	    width      : 150,                   //ANCHO
	    showToday  : false,
	    applyTo    : 'informacion_academica_fecha_fin',
	    editable   : false,
	    allowBlank : false,
	    listeners  : { select: function() {   } }
	});

	//AGREGAR O ACTUALIZAR LA INFORMACION DE CONTACTO
	function agregar_editar_informacion_academica() {
		var tipo_estudio    = document.getElementById('informacion_academica_tipo_estudio').value
		,	titulo_obtenido = document.getElementById('informacion_academica_titulo_obtenido').value
		,	institucion     = document.getElementById('informacion_academica_institucion').value
		,	fecha_inicio    = document.getElementById('informacion_academica_fecha_inicio').value
		,	fecha_fin       = document.getElementById('informacion_academica_fecha_fin').value
		,	ciclo           = document.getElementById('informacion_academica_ciclo').value
		,	modalidad       = document.getElementById('informacion_academica_modalidad').value
		,	ciudad          = document.getElementById('informacion_academica_ciudad').value

		if (tipo_estudio == '' || titulo_obtenido == '' || institucion == '' || fecha_inicio == '' || fecha_fin == '' || ciclo == '' || modalidad == '' || ciudad == '') {
			alert("Todos los campos son obligatorios");
			return;
		}

		MyLoading2('on');
		Ext.get('loadForm').load({
			url     : 'perfil_empleado/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc             : 'agregar_editar_informacion_academica',
				id              : '<?php echo $id; ?>',
				id_empleado     : '<?php echo $id_empleado; ?>',
				tipo_estudio    : tipo_estudio,
				titulo_obtenido : titulo_obtenido,
				institucion     : institucion,
				fecha_inicio    : fecha_inicio,
				fecha_fin       : fecha_fin,
				ciclo           : ciclo,
				modalidad       : modalidad,
				ciudad          : ciudad,
			}
		});
	}

	// ELIMINAR EL REGISTRO DE CONTACTO
	function eliminar_informacion_academica(){
		if ( confirm("Aviso\nRealmente desea eliminar el registro?") ) {
			MyLoading2('on');
			Ext.get('loadForm').load({
				url     : 'perfil_empleado/bd.php',
				scripts : true,
				nocache : true,
				params  :
				{
					opc : 'eliminar_informacion_academica',
					id  : '<?php echo $id; ?>',
				}
			});
		}
	}

</script>