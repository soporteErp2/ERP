<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	header('Content-Type: text/html; charset=utf-8');


	$sql="SELECT empresa,nombre_empresa,ciudad,actividad,cargo,jefe_inmediato,telefono,fecha_inicio,fecha_fin,salario,salario_mensual,otros_ingresos FROM empleados_experiencia_laboral WHERE activo=1 AND id=$id";
	$query=$mysql->query($sql,$mysql->link);

	$empresa         = $mysql->result($query,0,'empresa');
	$nombre_empresa  = $mysql->result($query,0,'nombre_empresa');
	$ciudad          = $mysql->result($query,0,'ciudad');
	$actividad       = $mysql->result($query,0,'actividad');
	$cargo           = $mysql->result($query,0,'cargo');
	$jefe_inmediato  = $mysql->result($query,0,'jefe_inmediato');
	$telefono        = $mysql->result($query,0,'telefono');
	$fecha_inicio    = $mysql->result($query,0,'fecha_inicio');
	$fecha_fin       = $mysql->result($query,0,'fecha_fin');
	$tipo_salario    = $mysql->result($query,0,'salario');
	$salario_mensual = $mysql->result($query,0,'salario_mensual');
	$otros_ingresos  = $mysql->result($query,0,'otros_ingresos');

	if ($empresa<>''){
		$acumscript .= "document.getElementById('experiencia_laboral_empresa').value='$empresa';";
	}
	if ($tipo_salario<>''){
		$acumscript .= "document.getElementById('experiencia_laboral_tipo_salario').value='$tipo_salario';";
	}

?>

<style>

</style>
<div class="content" >
	<div class="separator"><?php echo $title; ?><div class="close" onclick="Win_ventana_agregar_editar_experiencia_laboral.close();"></div></div>

	<div class="buttom-content" style="height: 55px;">
		<button class="button" data-value="save" onclick="agregar_editar_experiencia_laboral()" style="padding-top: 37px;height: 37px;width: 50px;background-size: contain;background-position: center;">Guardar</button>
		<?php
			if ($title=='Editar Registro') {
				?>
					<button class="button" data-value="delete" onclick="eliminar_experiencia_laboral()" style="padding-top: 37px;height: 37px;width: 50px;background-size: contain;background-position: center;">Eliminar</button>
				<?php
			}
		?>
	</div>

	<table class="table-form" style="width:90%;" >
		<tr>
			<td>Trabajo</td>
			<td>
				<select style="width:150px;" data-requiere="true" id="experiencia_laboral_empresa" >
					<option>Seleccione...</option>
					<option value="Anterior">Anterior</option>
					<option value="Actual">Actual</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Empresa</td>
			<td><input type="text" value="<?php echo $nombre_empresa; ?>" style="width:150px;" data-requiere="true" id="experiencia_laboral_nombre_empresa"></td>
		</tr>
		<tr>
			<td>Ciudad</td>
			<td><input type="text" style="width:150px;" value="<?php echo $ciudad; ?>" data-requiere="true" id="experiencia_laboral_ciudad"></td>
		</tr>
		<tr>
			<td>Cargo</td>
			<td><input type="text" value="<?php echo $cargo; ?>" style="width:150px;"  id="experiencia_laboral_cargo" data-requiere="true"></td>
		</tr>
		<tr>
			<td>Actividad</td>
			<td><input type="text" value="<?php echo $actividad; ?>" style="width:150px;" id="experiencia_laboral_actividad"></td>
		</tr>
		<tr>
			<td>Fecha Inicio</td>
			<td><input type="text" value="<?php echo $fecha_inicio; ?>" style="width:150px;" id="experiencia_laboral_fecha_inicio" ></td>
		</tr>
		<tr>
			<td>Fecha Fin</td>
			<td><input type="text" value="<?php echo $fecha_fin; ?>" style="width:150px;" id="experiencia_laboral_fecha_fin" ></td>
		</tr>
		<tr>
			<td>Jefe Inmediato</td>
			<td><input type="text" value="<?php echo $jefe_inmediato; ?>" style="width:150px;" id="experiencia_laboral_jefe_inmediato" ></td>
		</tr>
		<tr>
			<td>Telefono</td>
			<td><input type="text" value="<?php echo $telefono; ?>" style="width:150px;" id="experiencia_laboral_telefono" onkeyup="validate_int(this)" data-requiere="true"></td>
		</tr>
		<tr>
			<td>Tipo Salario</td>
			<td>
				<select  id="experiencia_laboral_tipo_salario" style="width:150px" >
					<option value="">Seleccione...</option>
					<option value="Integral">Integral</option>
					<option value="Otro">Otro</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Valor Mensual</td>
			<td><input type="text" value="<?php echo $salario_mensual; ?>" style="width:150px;" id="experiencia_laboral_valor_mensual" onkeyup="validate_int(this)"></td>
		</tr>
		<tr>
			<td>Otros Ingresos</td>
			<td><input type="text" value="<?php echo $otros_ingresos; ?>" style="width:150px;" id="experiencia_laboral_otros_ingresos" onkeyup="validate_int(this)"></td>
		</tr>

	</table>
</div>
<script>
	<?php echo $acumscript; ?>

	new Ext.form.DateField({
	    format     : 'Y-m-d',               //FORMATO
	    width      : 150,                   //ANCHO
	    showToday  : false,
	    applyTo    : 'experiencia_laboral_fecha_inicio',
	    editable   : false,
	    allowBlank : false,
	    listeners  : { select: function() {   } }
	});

	new Ext.form.DateField({
	    format     : 'Y-m-d',               //FORMATO
	    width      : 150,                   //ANCHO
	    showToday  : false,
	    applyTo    : 'experiencia_laboral_fecha_fin',
	    editable   : false,
	    allowBlank : false,
	    listeners  : { select: function() {   } }
	});

	//AGREGAR O ACTUALIZAR LA INFORMACION DE CONTACTO
	function agregar_editar_experiencia_laboral() {
		var	empresa        = document.getElementById('experiencia_laboral_empresa').value
		,	nombre_empresa = document.getElementById('experiencia_laboral_nombre_empresa').value
		,	ciudad         = document.getElementById('experiencia_laboral_ciudad').value
		,	cargo          = document.getElementById('experiencia_laboral_cargo').value
		,	actividad      = document.getElementById('experiencia_laboral_actividad').value
		,	fecha_inicio   = document.getElementById('experiencia_laboral_fecha_inicio').value
		,	fecha_fin      = document.getElementById('experiencia_laboral_fecha_fin').value
		,	jefe_inmediato = document.getElementById('experiencia_laboral_jefe_inmediato').value
		,	telefono       = document.getElementById('experiencia_laboral_telefono').value
		,	tipo_salario   = document.getElementById('experiencia_laboral_tipo_salario').value
		,	valor_mensual  = document.getElementById('experiencia_laboral_valor_mensual').value
		,	otros_ingresos = document.getElementById('experiencia_laboral_otros_ingresos').value

		if (empresa == '' || nombre_empresa=='' || ciudad=='' || cargo=='' || telefono=='') {
			alert("Faltan los campos obligatorios");
			return;
		}

		MyLoading2('on');
		Ext.get('loadForm').load({
			url     : 'perfil_empleado/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc            : 'agregar_editar_experiencia_laboral',
				id             : '<?php echo $id; ?>',
				id_empleado    : '<?php echo $id_empleado; ?>',
				empresa        : empresa,
				nombre_empresa : nombre_empresa,
				ciudad         : ciudad,
				cargo          : cargo,
				actividad      : actividad,
				fecha_inicio   : fecha_inicio,
				fecha_fin      : fecha_fin,
				jefe_inmediato : jefe_inmediato,
				telefono       : telefono,
				tipo_salario   : tipo_salario,
				valor_mensual  : valor_mensual,
				otros_ingresos : otros_ingresos,
			}
		});

	}

	// ELIMINAR EL REGISTRO DE CONTACTO
	function eliminar_experiencia_laboral(){
		if ( confirm("Aviso\nRealmente desea eliminar el registro?") ) {
			MyLoading2('on');
			Ext.get('loadForm').load({
				url     : 'perfil_empleado/bd.php',
				scripts : true,
				nocache : true,
				params  :
				{
					opc : 'eliminar_experiencia_laboral',
					id  : '<?php echo $id; ?>',
				}
			});
		}
	}

</script>