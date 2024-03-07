<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	header('Content-Type: text/html; charset=utf-8');

	$sql="SELECT idioma,nativo,institucion,ciudad,lectura,escritura,habla FROM empleados_idiomas WHERE activo=1 AND id=$id";
	$query=$mysql->query($sql,$mysql->link);

	$idioma      = $mysql->result($query,0,'idioma');
	$nativo      = $mysql->result($query,0,'nativo');
	$institucion = $mysql->result($query,0,'institucion');
	$ciudad      = $mysql->result($query,0,'ciudad');
	$lectura     = $mysql->result($query,0,'lectura');
	$escritura   = $mysql->result($query,0,'escritura');
	$habla       = $mysql->result($query,0,'habla');

	if ($idioma <> '') {
		$acumscript .= "document.getElementById('idiomas_idioma').value='$idioma';";
	}
	if ($nativo <> '') {
		$acumscript .= "document.getElementById('idiomas_nativo').value='$nativo';";
	}
	if ($lectura <> '') {
		$acumscript .= "document.getElementById('idiomas_lectura').value='$lectura';";
	}
	if ($escritura <> '') {
		$acumscript .= "document.getElementById('idiomas_escritura').value='$escritura';";
	}
	if ($habla <> '') {
		$acumscript .= "document.getElementById('idiomas_habla').value='$habla';";
	}

?>

<style>

</style>
<div class="content" >
	<div class="separator"><?php echo $title; ?><div class="close" onclick="Win_Ventana_agregar_editar_idioma.close();"></div></div>

	<div class="buttom-content" style="height: 55px;">
		<button class="button" data-value="save" onclick="agregar_editar_informacion_idioma()" style="padding-top: 37px;height: 37px;width: 50px;background-size: contain;background-position: center;">Guardar</button>
		<?php
			if ($title=='Editar Idioma'){
				?>
					<button class="button" data-value="delete" onclick="eliminar_informacion_idioma()" style="padding-top: 37px;height: 37px;width: 50px;background-size: contain;background-position: center;">Eliminar</button>
				<?php
			}
		?>
	</div>

	<table class="table-form" style="width:90%;" >
		<tr>
			<td>Idioma</td>
			<td>
				<select id="idiomas_idioma" style="width:150px" data-requiere="true">
					<option value="">Seleccione...</option>
					<option value="Aleman">Aleman</option>
					<option value="Chino">Chino</option>
					<option value="Espanol">Español</option>
					<option value="Frances">Frances</option>
					<option value="Holandes">Holandes</option>
					<option value="Ingles">Ingles</option>
					<option value="Italiano">Italiano</option>
					<option value="Japones">Japones</option>
					<option value="Mandarin">Mandarin</option>
					<option value="Portugues">Portugues</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Nativo</td>
			<td>
				<select id="idiomas_nativo" style="width:150px" data-requiere="true">
					<option value="">Seleccione...</option>
					<option value="si">Si</option>
					<option value="no">No</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Institución</td>
			<td><input type="text" style="width:150px;" value="<?php echo $institucion; ?>" data-requiere="true" id="idiomas_institucion"></td>
		</tr>
		<tr>
			<td>Ciudad</td>
			<td><input type="text" value="<?php echo $ciudad; ?>" style="width:150px;" data-requiere="true"  id="idiomas_ciudad"></td>
		</tr>
		<tr>
			<td>Lectura</td>
			<td>
				<select id="idiomas_lectura" style="width:150px" data-requiere="true">
					<option value="">Seleccione...</option>
					<option value="si">Si</option>
					<option value="no">No</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Escritura</td>
			<td>
				<select id="idiomas_escritura" style="width:150px" data-requiere="true">
					<option value="">Seleccione...</option>
					<option value="si">Si</option>
					<option value="no">No</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Habla</td>
			<td>
				<select id="idiomas_habla" style="width:150px" data-requiere="true">
					<option value="">Seleccione...</option>
					<option value="si">Si</option>
					<option value="no">No</option>
				</select>
			</td>
		</tr>

	</table>
</div>
<script>
	<?php echo $acumscript; ?>

	//AGREGAR O ACTUALIZAR LA INFORMACION DE CONTACTO
	function agregar_editar_informacion_idioma() {
		var idioma      = document.getElementById('idiomas_idioma').value
		,	nativo      = document.getElementById('idiomas_nativo').value
		,	institucion = document.getElementById('idiomas_institucion').value
		,	ciudad      = document.getElementById('idiomas_ciudad').value
		,	lectura     = document.getElementById('idiomas_lectura').value
		,	escritura   = document.getElementById('idiomas_escritura').value
		,	habla       = document.getElementById('idiomas_habla').value

		if (idioma=='' || nativo == '' || institucion == '' || ciudad == '' || lectura == '' || escritura == '' || habla == '') {
			alert("Faltan campos obligatorios");
			return;
		}

		MyLoading2('on');
		Ext.get('loadForm').load({
			url     : 'perfil_empleado/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc         : 'agregar_editar_informacion_idioma',
				id          : '<?php echo $id; ?>',
				id_empleado : '<?php echo $id_empleado; ?>',
				idioma      : idioma,
				nativo      : nativo,
				institucion : institucion,
				ciudad      : ciudad,
				lectura     : lectura,
				escritura   : escritura,
				habla       : habla,
			}
		});
	}

	// ELIMINAR EL REGISTRO DE CONTACTO
	function eliminar_informacion_idioma(){
		if ( confirm("Aviso\nRealmente desea eliminar el registro?") ) {
			MyLoading2('on');
			Ext.get('loadForm').load({
				url     : 'perfil_empleado/bd.php',
				scripts : true,
				nocache : true,
				params  :
				{
					opc : 'eliminar_informacion_idioma',
					id  : '<?php echo $id; ?>',
				}
			});
		}
	}

</script>