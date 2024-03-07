<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	header('Content-Type: text/html; charset=utf-8');

	$sql="SELECT id,nombre,unico FROM configuracion_tipos_contacto WHERE activo=1";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$option.='<option value="'.$row['id'].'">'.$row['nombre'].'</option>';
	}

	$sql="SELECT id_parentesco,nombres,apellidos,ocupacion,direccion,telefono,celular FROM empleados_informacion_contacto WHERE activo=1 AND id=$id";
	$query=$mysql->query($sql,$mysql->link);

	$id_parentesco = $mysql->result($query,0,'id_parentesco');
	$nombres       = $mysql->result($query,0,'nombres');
	$apellidos     = $mysql->result($query,0,'apellidos');
	$ocupacion     = $mysql->result($query,0,'ocupacion');
	$direccion     = $mysql->result($query,0,'direccion');
	$telefono      = $mysql->result($query,0,'telefono');
	$celular       = $mysql->result($query,0,'celular');

	if ($id_parentesco<>'') {
		$acumscript = "document.getElementById('id_parentesco').value=$id_parentesco;";
	}

?>

<style>

</style>
<div class="content" >
	<div class="separator"><?php echo $title; ?><div class="close" onclick="Win_Ventana_agregar_editar.close();"></div></div>

	<div class="buttom-content" style="height: 55px;">
		<button class="button" data-value="save" onclick="agregar_editar_informacion_academico()" style="padding-top: 37px;height: 37px;width: 50px;background-size: contain;background-position: center;">Guardar</button>
		<?php
			if ($title=='Editar Registro') {
				?>
					<button class="button" data-value="delete" onclick="eliminar_informacion_familiar()" style="padding-top: 37px;height: 37px;width: 50px;background-size: contain;background-position: center;">Eliminar</button>
				<?php
			}
		?>
	</div>

	<table class="table-form" style="width:90%;" >
		<tr>
			<td>Parentesco</td>
			<td><select style="width:150px;" data-requiere="true" id="id_parentesco" ><option value="0">Seleccione...</option><?php echo $option; ?></select></td>
		</tr>
		<tr>
			<td>Nombres</td>
			<td><input type="text" value="<?php echo $nombres; ?>" style="width:150px;" data-requiere="true" id="nombres_info"></td>
		</tr>
		<tr>
			<td>Apellidos</td>
			<td><input type="text" style="width:150px;" value="<?php echo $apellidos; ?>" data-requiere="true" id="apellidos_info"></td>
		</tr>
		<tr>
			<td>Ocupacion</td>
			<td><input type="text" value="<?php echo $ocupacion; ?>" style="width:150px;"  id="ocupacion_info"></td>
		</tr>
		<tr>
			<td>Direccion</td>
			<td><input type="text" value="<?php echo $direccion; ?>" style="width:150px;" id="direccion_info"></td>
		</tr>
		<tr>
			<td>Telefono</td>
			<td><input type="text" value="<?php echo $telefono; ?>" style="width:150px;" id="telefono_info" onkeyup="validate_int(this)"></td>
		</tr>
		<tr>
			<td>Celular</td>
			<td><input type="text" value="<?php echo $celular; ?>" style="width:150px;" id="celular_info" onkeyup="validate_int(this)"></td>
		</tr>

	</table>
</div>
<script>
	<?php echo $acumscript; ?>

	//AGREGAR O ACTUALIZAR LA INFORMACION DE CONTACTO
	function agregar_editar_informacion_academico() {
		var id_parentesco = document.getElementById('id_parentesco').value
		,	nombres       = document.getElementById('nombres_info').value
		,	apellidos     = document.getElementById('apellidos_info').value
		,	ocupacion     = document.getElementById('ocupacion_info').value
		,	direccion     = document.getElementById('direccion_info').value
		,	telefono      = document.getElementById('telefono_info').value
		,	celular       = document.getElementById('celular_info').value

		if (id_parentesco== '' || id_parentesco==0 || nombres== '' || apellidos=='') {
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
				opc           : 'agregar_editar_informacion_familiar',
				id            : '<?php echo $id; ?>',
				id_empleado   : '<?php echo $id_empleado; ?>',
				id_parentesco : id_parentesco,
				nombres       : nombres,
				apellidos     : apellidos,
				ocupacion     : ocupacion,
				direccion     : direccion,
				telefono      : telefono,
				celular       : celular,
			}
		});

	}

	// ELIMINAR EL REGISTRO DE CONTACTO
	function eliminar_informacion_familiar(){
		if ( confirm("Aviso\nRealmente desea eliminar el registro?") ) {
			MyLoading2('on');
			Ext.get('loadForm').load({
				url     : 'perfil_empleado/bd.php',
				scripts : true,
				nocache : true,
				params  :
				{
					opc           : 'eliminar_informacion_familiar',
					id            : '<?php echo $id; ?>',
				}
			});
		}
	}

</script>