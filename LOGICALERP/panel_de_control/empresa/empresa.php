<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$id_empresa = $_SESSION['EMPRESA'];
	$id_pais    = $_SESSION['PAIS'];

	// CONSULTAR LA INFORMACION DE LA EMPRESA
	$sql = "SELECT
				    nombre,
						TRIM(tipo_regimen) AS tipo_regimen,
						razon_social,
						actividad_economica,
						direccion,
						id_departamento,
						id_ciudad,
						telefono,
						celular,
						email,
						tipo_persona_codigo
					FROM
						empresas
					WHERE
						activo = 1
					AND
						id = $id_empresa";
	$query = $mysql->query($sql,$mysql->link);

	$nombre                     = $mysql->result($query,0,'nombre');
	$tipo_regimen               = $mysql->result($query,0,'tipo_regimen');
	$razon_social               = $mysql->result($query,0,'razon_social');
	$actividad_economica        = $mysql->result($query,0,'actividad_economica');
	$direccion                  = $mysql->result($query,0,'direccion');
	$id_departamento            = $mysql->result($query,0,'id_departamento');
	$id_ciudad                  = $mysql->result($query,0,'id_ciudad');
	$telefono                   = $mysql->result($query,0,'telefono');
	$celular                    = $mysql->result($query,0,'celular');
	$email                      = $mysql->result($query,0,'email');
	$tipo_persona_codigo        = $mysql->result($query,0,'tipo_persona_codigo');

	// CONSULTAR LOS DEPARTAMENTOS
	$sql = "SELECT id,departamento
					FROM ubicacion_departamento
					WHERE activo = 1 AND id_pais = $id_pais
					ORDER BY departamento ASC";
	$query = $mysql->query($sql,$mysql->link);
	while($row = $mysql->fetch_array($query)){
		$selected = ($id_departamento == $row['id'])? 'selected' : '' ;
		$departamentos .= '<option value="'.$row['id'].'" '.$selected.'>'.$row['departamento'].'</option>';
	}

	// CONSULTAR LOS REGIMENES
	$sql = "SELECT id,nombre
					FROM terceros_tributario
					WHERE activo = 1 AND id_pais = $id_pais";
	$query = $mysql->query($sql,$mysql->link);
	while($row = $mysql->fetch_array($query)) {
		$selected = ($tipo_regimen==$row['nombre'])? 'selected' : '';
		$regimenes .= "<option value='$row[nombre]' $selected>$row[nombre]</option>";
	}
?>
<div id="toolbar_correo"></div>
<div class="content" style="height: calc(100% - 80px)">
	<table class="table-form" style="width:90%;">
		<tr>
			<td>Nombre</td>
			<td><input type="text" value="<?php echo $nombre; ?>" style="width:190px;" data-requiere="true" id="nombre_empresa" data-value=""></td>
		</tr>
		<tr>
			<td>Razon Social</td>
			<td><input type="text" value="<?php echo $razon_social; ?>" style="width:190px;" data-requiere="true" id="razon_social" data-value=""></td>
		</tr>
		<tr>
			<td>Regimen</td>
			<td colspan="2">
				<select style="width:190px;"  id="regimen">
					<option value="">Seleccione...</option>
					<?php echo $regimenes; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Actividad Economica</td>
			<td><input type="text" value="<?php echo $actividad_economica; ?>" style="width:190px;" data-requiere="true" id="actividad_economica" data-value=""></td>
		</tr>
		<tr>
			<td>Departamento (Provincia - Estado)</td>
			<td colspan="2">
				<select style="width:190px;" data-requiere="true" id="id_departamento" onchange="buscar_ciudad(this.value)">
					<option value="">Seleccione...</option>
					<?php echo $departamentos; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Ciudad (Condado)</td>
			<td colspan="2" id="loadCiudad">
				<select style="width:190px;" data-requiere="true" id="id_ciudad">
					<option value="">Seleccione...</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Email</td>
			<td><input type="text" value="<?php echo $email; ?>" style="width:190px;" data-requiere="true" id="email" data-value=""></td>
		</tr>
		<tr>
			<td>Direccion</td>
			<td><input type="text" value="<?php echo $direccion; ?>" style="width:190px;" data-requiere="true" id="direccion" data-value=""></td>
		</tr>
		<tr>
			<td>Telefono</td>
			<td><input type="text" value="<?php echo $telefono; ?>" style="width:190px;" data-requiere="true" id="telefono" data-value=""></td>
		</tr>
		<tr>
			<td>Celular</td>
			<td><input type="text" value="<?php echo $celular; ?>" style="width:190px;" data-requiere="true" id="celular" data-value=""></td>
		</tr>
		<tr>
			<td>Tipo Persona</td>
			<td>
				<select style="width:190px;" data-requiere="true" id="tipo_persona" data-value="">
					<option value="">Seleccione...</option>
					<option <?php if($tipo_persona_codigo == '1'){echo("selected");}?> value="1">Persona Juridica</option>
					<option <?php if($tipo_persona_codigo == '2'){echo("selected");}?> value="2">Persona Natural</option>
				</select>
			</td>
		</tr>
	</table>
	<div id="loadForm" style="display:none;"></div>
</div>
<script>
	new Ext.Panel(
		{
			renderTo : 'toolbar_correo',
			frame		 : false,
			border	 : false,
			tbar		 :
			[
				{
					xtype		: 'buttongroup',
					columns	: 3,
					title		: 'Opciones',
					items		:
					[
						{
							xtype			: 'button',
							text			: 'Actualizar',
							scale			: 'large',
							iconCls		: 'guardar',
							iconAlign	: 'top',
							handler 	: function(){BloqBtn(this); actualiza_info_empresa();}
						}
					]
				}
			]
		}
	);

	function actualiza_info_empresa(){
		var nombre_empresa    = document.getElementById('nombre_empresa').value
		,	razon_social        = document.getElementById('razon_social').value
		,	regimen             = document.getElementById('regimen').value
		,	actividad_economica = document.getElementById('actividad_economica').value
		,	id_departamento     = document.getElementById('id_departamento').value
		,	id_ciudad           = document.getElementById('id_ciudad').value
		,	direccion           = document.getElementById('direccion').value
		,	telefono            = document.getElementById('telefono').value
		,	celular             = document.getElementById('celular').value
		, email               = document.getElementById('email').value
		, tipo_persona_codigo = document.getElementById('tipo_persona').value
		, tipo_persona_nombre = document.getElementById('tipo_persona').options[document.getElementById('tipo_persona').selectedIndex].innerHTML

		if(nombre_empresa == "" || razon_social == "" || actividad_economica == "" || id_departamento == "" || id_ciudad == "" || direccion == "" || telefono == "" || celular == "" || email == ""){
			alert('Aviso\nFaltan algunos campos obligatorios');
			return;
		}

		MyLoading2('on');

		Ext.get('loadForm').load({
			url     : 'empresa/bd/bd.php',
			scripts : true,
			nocache : true,
			text 		: 'actualizando datos...',
			params  :	{
									opc                 : 'actualiza_info_empresa',
									nombre_empresa      : nombre_empresa,
									razon_social        : razon_social,
									regimen             : regimen,
									actividad_economica : actividad_economica,
									id_departamento     : id_departamento,
									id_ciudad           : id_ciudad,
									direccion           : direccion,
									telefono            : telefono,
									celular             : celular,
									email               : email,
									tipo_persona_codigo : tipo_persona_codigo,
									tipo_persona_nombre : tipo_persona_nombre
								}
		});
	}

	buscar_ciudad('<?php echo $id_departamento; ?>');

	function buscar_ciudad(id_departamento){
		Ext.get('loadCiudad').load({
			url     : 'empresa/bd/bd.php',
			scripts : true,
			nocache : true,
			text 		: 'Cargando las Ciudades...',
			params  : {
									opc             : 'buscar_ciudad',
									id_departamento : id_departamento,
									id_ciudad       : '<?php echo $id_ciudad; ?>',
								}
		});
	}
</script>
