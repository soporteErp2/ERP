<?php



	// CONSULTAR LA INFORMACION DEL EMPLEADO
	$sql="SELECT tipo_documento_nombre,
					nombre1,
					nombre2,
					apellido1,
					apellido2,
					documento,
					id_pais,
					id_departamento,
					id_ciudad,
					direccion,
					barrio,
					telefono1,
					telefono2,
					celular1,
					sexo,
					extranjero_obligado_cotizar,
					residente_en_exterior,
					tipo_cotizante,
					subtipo_cotizante,
					codigo_departamento_laboral,
					codigo_municipio_laboral,
					codigo_administradora_pensiones,
					tipo_entidad_salud,
					codigo_EPS_EOC,
					codigo_CCF,
					fecha_nacimiento,
					email_personal,
					email_empresa,
					email_notificaciones,
					id_pais_documento,
					id_departamento_documento,
					id_ciudad_documento,
					id_pais_nacimiento,
					id_departamento_nacimiento,
					id_ciudad_nacimiento
			FROM empleados WHERE activo=1 AND id=$id_empleado";
	$query=$mysql->query($sql,$mysql->link);

	$tipo_documento_nombre           = $mysql->result($query,0,'tipo_documento_nombre');
	$nombres                         = $mysql->result($query,0,'nombre1').' '.$mysql->result($query,0,'nombre2');
	$apellidos                       = $mysql->result($query,0,'apellido1').' '.$mysql->result($query,0,'apellido2');
	$documento                       = $mysql->result($query,0,'documento');
	$direccion                       = $mysql->result($query,0,'direccion');
	$barrio                          = $mysql->result($query,0,'barrio');
	$telefono1                       = $mysql->result($query,0,'telefono1');
	$telefono2                       = $mysql->result($query,0,'telefono2');
	$celular1                        = $mysql->result($query,0,'celular1');
	$empleados                       = $mysql->result($query,0,'empleados');
	$id_pais                         = $mysql->result($query,0,'id_pais');
	$id_departamento                 = $mysql->result($query,0,'id_departamento');
	$id_ciudad                       = $mysql->result($query,0,'id_ciudad');
	$sexo                            = $mysql->result($query,0,'sexo');
	$extranjero_obligado_cotizar     = $mysql->result($query,0,'extranjero_obligado_cotizar');
	$residente_en_exterior           = $mysql->result($query,0,'residente_en_exterior');
	$tipo_cotizante                  = $mysql->result($query,0,'tipo_cotizante');
	$subtipo_cotizante               = $mysql->result($query,0,'subtipo_cotizante');
	$codigo_departamento_laboral     = $mysql->result($query,0,'codigo_departamento_laboral');
	$codigo_municipio_laboral        = $mysql->result($query,0,'codigo_municipio_laboral');
	$codigo_administradora_pensiones = $mysql->result($query,0,'codigo_administradora_pensiones');
	$tipo_entidad_salud              = $mysql->result($query,0,'tipo_entidad_salud');
	$codigo_EPS_EOC                  = $mysql->result($query,0,'codigo_EPS_EOC');
	$codigo_CCF                      = $mysql->result($query,0,'codigo_CCF');
	$fecha_nacimiento                = $mysql->result($query,0,'fecha_nacimiento');
	$email_personal                  = $mysql->result($query,0,'email_personal');
	$email_empresa                   = $mysql->result($query,0,'email_empresa');
	$email_notificaciones            = $mysql->result($query,0,'email_notificaciones');
	$id_pais_documento               = $mysql->result($query,0,'id_pais_documento');
	$id_departamento_documento       = $mysql->result($query,0,'id_departamento_documento');
	$id_ciudad_documento             = $mysql->result($query,0,'id_ciudad_documento');
	$id_pais_nacimiento              = $mysql->result($query,0,'id_pais_nacimiento');
	$id_departamento_nacimiento      = $mysql->result($query,0,'id_departamento_nacimiento');
	$id_ciudad_nacimiento            = $mysql->result($query,0,'id_ciudad_nacimiento');

	$sql="SELECT grupo_sanguineo,factor_rh,cual_alergico_medicamento,cual_toma_medicamento FROM empleados_adicional WHERE id_empleado=$id_empleado";
	$query=$mysql->query($sql,$mysql->link);
	$grupo_sanguineo           = $mysql->result($query,0,'grupo_sanguineo');
	$factor_rh                 = $mysql->result($query,0,'factor_rh');
	$cual_alergico_medicamento = $mysql->result($query,0,'cual_alergico_medicamento');
	$cual_toma_medicamento     = $mysql->result($query,0,'cual_toma_medicamento');

	// CONSULTAR LOS PAISES, DEPARTAMENTOS, CIUDADES
	$sql="SELECT id,pais FROM ubicacion_pais WHERE activo=1";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {

		$selectDocumento  = ($id_pais_documento==$row['id'])? 'selected' : '' ;
		$selectNacimiento = ($id_pais_nacimiento==$row['id'])? 'selected' : '' ;
		$selectResidencia = ($id_pais==$row['id'])? 'selected' : '' ;

		$optionPais_documento .= '<option value="'.$row['id'].'" '.$selectDocumento.'>'.utf8_encode($row['pais']).'</option>';
		$optionPaisNacimiento .= '<option value="'.$row['id'].'" '.$selectNacimiento.'>'.utf8_encode($row['pais']).'</option>';
		$optionPaisResidencia .= '<option value="'.$row['id'].'" '.$selectResidencia.'>'.utf8_encode($row['pais']).'</option>';
	}

	if ($id_pais<>'') {
		$acumscript = 'busca_departamento('.$id_pais.',"residencia");';
	}
	if ($id_pais_documento<>''){
		$acumscript .= 'busca_departamento('.$id_pais_documento.',"documento");';
	}
	if ($id_pais_nacimiento<>''){
		$acumscript .= 'busca_departamento('.$id_pais_nacimiento.');';
	}

	if($sexo<>''){
		$acumscript .= "document.getElementById('sexo').value='$sexo';";
	}
	if($tipo_cotizante<>''){
		$acumscript .= "document.getElementById('tipo_cotizante').value='$tipo_cotizante';";
	}
	if($subtipo_cotizante<>''){
		$acumscript .= "document.getElementById('subtipo_cotizante').value='$subtipo_cotizante';";
	}
	if($tipo_entidad_salud<>''){
		$acumscript .= "document.getElementById('tipo_entidad_salud').value='$tipo_entidad_salud';";
	}
	if ($fecha_nacimiento<>'') {
		$acumscript .= "document.getElementById('fecha_nacimiento').value='$fecha_nacimiento';calcula_edad();";
	}
	if ($grupo_sanguineo <> '') {
		$acumscript .= "document.getElementById('grupo_sanguineo').value='$grupo_sanguineo';";
	}
	if ($factor_rh <> '') {
		$acumscript .= "document.getElementById('rh').value='$factor_rh';";
	}
	if ($extranjero_obligado_cotizar<> '') {
		$acumscript .= "document.getElementById('extranjero_obligado_cotizar').value='$extranjero_obligado_cotizar';";
	}
	if ($residente_en_exterior<> '') {
		$acumscript .= "document.getElementById('residente_en_exterior').value='$residente_en_exterior';";
	}

	if ($email_notificaciones<>'') {
		$acumscript .= "document.getElementById('email_notificaciones').value='$email_notificaciones';";
	}

?>

<style>

	.content-personal-info{
		padding-left : 15px;
		width        : calc(100% - 15px);
	}

</style>
<div class="content-personal-info">
	<div class="buttom-content">
		<button class="button" data-value="save" onclick="guardar_datos_personales()">Guardar</button>
	</div>

	<table class="table-form">
		<tr class="thead">
			<td colspan="2">INFORMACIÓN BASICA</td>
		</tr>
		<tr>
			<td>Nombres</td>
			<td><input type="text" readonly="readonly" value="<?php echo $nombres ?>" title="<?php echo $nombres ?>"></td>
		</tr>
		<tr>
			<td>Apellidos</td>
			<td><input type="text" readonly="readonly" value="<?php echo $apellidos ?>" title="<?php echo $apellidos ?>"></td>
		</tr>
		<tr>
			<td>Identificación</td>
			<td><input type="text" readonly="readonly" style="width:50px;border-right:none;" value="<?php echo $tipo_documento_nombre; ?>"><input type="text" readonly="readonly" style="width:150px;" value="<?php echo $documento ?>"></td>
		</tr>

		<tr>
			<td>Pais de Documento</td>
			<td><select onchange="busca_departamento(this.value,'documento')" id="pais_documento"><?php echo $optionPais_documento ?></select></td>
		</tr>
		<tr>
			<td>Departamento de Documento</td>
			<td id="loadDeptoDocumento"><select id="departamento_documento" ><option>Seleccione...</option></select></td>
		</tr>
		<tr>
			<td>Ciudad de Documento</td>
			<td id="loadCiudad_documento"><select id="ciudad_documento"><option>Seleccione...</option></select></td>
		</tr>

		<tr>
			<td>Fecha de Nacimiento</td>
			<td><input type="text" readonly="readonly" id="fecha_nacimiento" style="text-align:center;" ></td>
		</tr>
		<tr>
			<td>Edad</td>
			<td><input type="text" readonly="readonly" id="edad" ></td>
		</tr>
		<tr>
			<td>Pais de Nacimiento</td>
			<td><select onchange="busca_departamento(this.value)" id="pais_nacimiento"><?php echo $optionPaisNacimiento ?></select></td>
		</tr>
		<tr>
			<td>Departamento de Nacimiento</td>
			<td id="loadDepto"><select id="departamento_nacimiento" ><option>Seleccione...</option></select></td>
		</tr>
		<tr>
			<td>Ciudad de Nacimiento</td>
			<td id="loadCiudad"><select id="ciudad_nacimiento"><option>Seleccione...</option></select></td>
		</tr>
		<tr>
			<td>Sexo</td>
			<td>
				<select id="sexo">
					<option>Seleccione...</option>
					<option value="Femenino">Femenino</option>
					<option value="Masculino">Masculino</option>
				</select>
			</td>
		</tr>
	</table>

	<table class="table-form">
		<tr class="thead">
			<td colspan="2">INFORMACIÓN DE CONTACTO</td>
		</tr>
		<tr>
			<td>Pais Residencia</td>
			<td><select onchange="busca_departamento(this.value,'residencia')" id="pais_residencia"><?php echo $optionPaisResidencia ?></select></td>
		</tr>
		<tr>
			<td>Departamento Residencia</td>
			<td id="loadDeptoResidencia"><select id="departamento_residencia"><option>Seleccione...</option></select></td>
		</tr>
		<tr>
			<td>Ciudad Residencia</td>
			<td id="loadCiudadResidencia"><select id="ciudad_residencia"><option>Seleccione...</option></select></td>
		</tr>
		<tr>
			<td>Dirección Residencia</td>
			<td><input type="text" id="direccion" value="<?php echo $direccion; ?>"></td>
		</tr>
		<tr>
			<td>Barrio Residencia</td>
			<td><input type="text" id="barrio" value="<?php echo $barrio; ?>"></td>
		</tr>
		<tr>
			<td>Numero Telefonico</td>
			<td><input type="text" id="telefono" value="<?php echo $telefono1; ?>" onkeyup="validate_int(this)"></td>
		</tr>
		<tr>
			<td>Numero Telefonico Alterno</td>
			<td><input type="text" id="telefono_alterno" value="<?php echo $telefono2; ?>" onkeyup="validate_int(this)"> </td>
		</tr>
		<tr>
			<td>Numero Celular</td>
			<td><input type="text" id="numero_celular" value="<?php echo $celular1; ?>" onkeyup="validate_int(this)"></td>
		</tr>
		<tr>
			<td>Email Personal</td>
			<td><input type="text" id="email" value="<?php echo $email_personal; ?>"></td>
		</tr>
		<tr>
			<td>Email Empresa</td>
			<td><input type="text" id="email_empresa" value="<?php echo $email_empresa; ?>"></td>
		</tr>
		<tr>
			<td>Email Notificaciones</td>
			<td>
				<select id="email_notificaciones">
					<option value="email_empresa">Email empresarial</option>
					<option value="email_personal">Email personal</option>
				</select>
			</td>
		</tr>
	</table>
	<table class="table-form">
		<tr class="thead">
			<td colspan="2">INFORMACIÓN MÉDICA</td>
		</tr>
		<tr>
			<td>Grupo Sanguineo</td>
			<td>
				<select id="grupo_sanguineo">
					<option>Seleccione...</option>
					<option value="A">A</option>
					<option value="B">B</option>
					<option value="AB">AB</option>
					<option value="O">O</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>RH</td>
			<td>
				<select id="rh">
					<option>Seleccione...</option>
					<option value="+">+</option>
					<option value="-">-</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Alergico a medicamentos</td>
			<td><textarea id="alergico"><?php echo $cual_alergico_medicamento; ?></textarea></td>
		</tr>
		<tr>
			<td>Toma medicamentos</td>
			<td><textarea id="medicamentos"><?php echo $cual_toma_medicamento; ?></textarea></td>
		</tr>
	</table>
	<table class="table-form">
		<tr class="thead">
			<td colspan="2">INFORMACIÓN SEGURIDAD SOCIAL</td>
		</tr>
		<tr>
			<td>Extranjero Obligado a cotizar pension</td>
			<td>
				<select id="extranjero_obligado_cotizar">
					<option value="No">No</option>
					<option value="Si">Si</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Colombiano Residente en el Exterior</td>
			<td>
				<select id="residente_en_exterior">
					<option value="No">No</option>
					<option value="Si">Si</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Tipo Cotizante</td>
			<td>
				<select id="tipo_cotizante">
					<option value="" >Seleccione...</option>
					<option value="1">1. Dependiente</option>
					<option value="2">2. Servicio Domestico</option>
					<option value="3">3. Independiente</option>
					<option value="4">4. Madre comunitaria</option>
					<option value="12">12. Aprendices del SENA en etapa lectiva</option>
					<option value="15">15. Desempleado con subsidio de Caja de Compensación Familiar</option>
					<option value="16">16. Independiente agremiado ó asociado</option>
					<option value="18">18. Funcionarios públicos sin tope máximo en el IBC</option>
					<option value="19">19. Aprendices del SENA en etapa productiva</option>
					<option value="20">20. Estudiantes (Régimen especial-Ley 789/2002)</option>
					<option value="21">21. Estudiantes de postgrado en salud (Decreto 190 de 1996)</option>
					<option value="22">22. Profesor de establecimiento particular</option>
					<option value="30">30. Dependiente Entidades o Universidades Públicas con régimen especial en Salud</option>
					<option value="31">31.  Cooperados o Precooperativas de trabajo asociado</option>
					<option value="32">32. Cotizante miembro de la carrera diplomática o consular de un país extranjero o funcionario de organismo multilateral no sometido a la legislación colombiana</option>
					<option value="33">33. Beneficiario del Fondo de Solidaridad Pensiona</option>
					<option value="34">34. Concejal amparado por póliza de salud</option>
					<option value="40">40. Beneficiario UPC Adicional</option>
					<option value="43">43. Cotizante Voluntario a pensiones con pago por tercero</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Subtipo Cotizante</td>
			<td>
				<select id="subtipo_cotizante">
					<option value="" >Seleccione...</option>
					<option value="1">1. Dependiente pensionado por vejez activo</option>
					<option value="2">2. Independiente pensionado por vejez activo</option>
					<option value="3">3. Cotizante no obligado a cotización a pensiones por edad</option>
					<option value="4">4. Cotizante con requisitos cumplidos para pensión</option>
					<option value="5">5. Cotizante a quien se le ha reconocido indemnización sustitutiva o devolución de saldos</option>
					<option value="6">6. Cotizante perteneciente a un régimen exceptuado de pensiones o a entidades autorizadas para recibir aportes exclusivamente de un grupo de sus propios trabajadores</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Codigo del Departamento de la ubicacion laboral</td>
			<td><input type="text" id="codigo_departamento_laboral" value="<?php echo $codigo_departamento_laboral; ?>" maxlength="2"></select></td>
		</tr>
		<tr>
			<td>Codigo Municipio de la ubicacion laboral</td>
			<td><input type="text" id="codigo_municipio_laboral" value="<?php echo $codigo_municipio_laboral; ?>" maxlength="3"></td>
		</tr>
		<tr>
			<td>Codigo Administradora de Pensiones</td>
			<td><input type="text" id="codigo_administradora_pensiones" value="<?php echo $codigo_administradora_pensiones; ?>" maxlength="6"></td>
		</tr>
		<tr>
			<td>Código EPS o EOC a la cual pertenece el afiliado </td>
			<td>
				<select id="tipo_entidad_salud" style="float:left;width:100px;border-right: none;">
					<option>Seleccione...</option>
					<option value="EPS">EPS</option>
					<option value="EOC">EOC</option>
				</select>
				<input type="text" id="codigo_EPS_EOC" value="<?php echo $codigo_EPS_EOC; ?>" maxlength="6" style="float:left;width:100px;height: 25px;"></td>
		</tr>
		<tr>
			<td>Código CCF a la cual pertenece el afiliado </td>
			<td><input type="text" id="codigo_CCF" value="<?php echo $codigo_CCF; ?>" maxlength="6"></td>
		</tr>
	</table>
</div>

<script>

	// FUNCION PARA BUSCAR EL DEPARTAMENTO
	function busca_departamento(id,opcion){
		var divLoad = '';
		if (opcion=='residencia') { divLoad = 'loadDeptoResidencia'; }
		else if (opcion == 'documento') { divLoad = 'loadDeptoDocumento'; }
		else{ divLoad = 'loadDepto'; }
		Ext.get(divLoad).load({
			url     : 'perfil_empleado/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc         : 'busca_departamento',
				id_pais     : id,
				opcion      : opcion,
				id_empleado : '<?php echo $id_empleado ?>',
			}
		});
	}

	function busca_ciudad(id,opcion) {
		var divLoad = '';
		if (opcion=='residencia') { divLoad = 'loadCiudadResidencia'; }
		else if (opcion == 'documento') { divLoad = 'loadCiudad_documento'; }
		else{ divLoad = 'loadCiudad'; }

		Ext.get(divLoad).load({
			url     : 'perfil_empleado/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc         : 'busca_ciudad',
				id_depto    : id,
				opcion      : opcion,
				id_empleado : '<?php echo $id_empleado ?>',
			}
		});
	}

	new Ext.form.DateField({
	    format     : 'Y-m-d',               //FORMATO
	    width      : 217,                   //ANCHO
	    // allowBlank : false,
	    showToday  : false,
	    applyTo    : 'fecha_nacimiento',
	    editable   : false,                 //EDITABLE
	    listeners  : { select: function() {  calcula_edad(); } }
	});

	function calcula_edad() {
		var fecha_nacimiento = document.getElementById('fecha_nacimiento').value;
		var anio_nacimiento = fecha_nacimiento.split('-')[0];
		document.getElementById('edad').value=<?php echo date('Y') ?>-anio_nacimiento;
	}

	<?php echo $acumscript; ?>


	// GUARDAR LA INFORMACION DEL FORMULARIO
	function guardar_datos_personales() {

		var pais_documento                  = document.getElementById('pais_documento').value
		,	departamento_documento          = document.getElementById('departamento_documento').value
		,	ciudad_documento                = document.getElementById('ciudad_documento').value
		,	fecha_nacimiento                = document.getElementById('fecha_nacimiento').value
		,	pais_nacimiento                 = document.getElementById('pais_nacimiento').value
		,	departamento_nacimiento         = document.getElementById('departamento_nacimiento').value
		,	ciudad_nacimiento               = document.getElementById('ciudad_nacimiento').value
		,	sexo                            = document.getElementById('sexo').value
		,	extranjero_obligado_cotizar     = document.getElementById('extranjero_obligado_cotizar').value
		,	residente_en_exterior           = document.getElementById('residente_en_exterior').value
		,	tipo_cotizante                  = document.getElementById('tipo_cotizante').value
		,	subtipo_cotizante               = document.getElementById('subtipo_cotizante').value
		,	codigo_departamento_laboral     = document.getElementById('codigo_departamento_laboral').value
		,	codigo_municipio_laboral        = document.getElementById('codigo_municipio_laboral').value
		,	codigo_administradora_pensiones = document.getElementById('codigo_administradora_pensiones').value
		,	tipo_entidad_salud              = document.getElementById('tipo_entidad_salud').value
		,	codigo_EPS_EOC                  = document.getElementById('codigo_EPS_EOC').value
		,	codigo_CCF                      = document.getElementById('codigo_CCF').value
		,	pais_residencia                 = document.getElementById('pais_residencia').value
		,	departamento_residencia         = document.getElementById('departamento_residencia').value
		,	ciudad_residencia               = document.getElementById('ciudad_residencia').value
		,	direccion                       = document.getElementById('direccion').value
		,	barrio                          = document.getElementById('barrio').value
		,	telefono                        = document.getElementById('telefono').value
		,	telefono_alterno                = document.getElementById('telefono_alterno').value
		,	numero_celular                  = document.getElementById('numero_celular').value
		,	email                           = document.getElementById('email').value
		,	email_empresa                   = document.getElementById('email_empresa').value
		,	email_notificaciones            = document.getElementById('email_notificaciones').value
		,	grupo_sanguineo                 = document.getElementById('grupo_sanguineo').value
		,	rh                              = document.getElementById('rh').value
		,	alergico                        = document.getElementById('alergico').value
		,	medicamentos                    = document.getElementById('medicamentos').value

		// VALIDAR SI TIENE VALOR EL CAMPO CORREO
		// if (email!='') {if (validarEmail(email)==false){ alert("El campo email personal no es un email valido"); return; } }
		// if (email_empresa!='') {if (validarEmail(email_empresa)==false){ alert("El campo email empresa no es un email valido"); return; } }

		MyLoading2('on');

		Ext.get('loadForm').load({
			url     : 'perfil_empleado/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc                             : 'guardar_datos_personales',
				id_empleado                     : '<?php echo $id_empleado; ?>',
				pais_documento                  : pais_documento,
				departamento_documento          : departamento_documento,
				ciudad_documento                : ciudad_documento,
				fecha_nacimiento                : fecha_nacimiento,
				pais_nacimiento                 : pais_nacimiento,
				departamento_nacimiento         : departamento_nacimiento,
				ciudad_nacimiento               : ciudad_nacimiento,
				sexo                            : sexo,
				extranjero_obligado_cotizar     : extranjero_obligado_cotizar,
				residente_en_exterior           : residente_en_exterior,
				tipo_cotizante                  : tipo_cotizante,
				subtipo_cotizante               : subtipo_cotizante,
				codigo_departamento_laboral     : codigo_departamento_laboral,
				codigo_municipio_laboral        : codigo_municipio_laboral,
				codigo_administradora_pensiones : codigo_administradora_pensiones,
				tipo_entidad_salud              : tipo_entidad_salud,
				codigo_EPS_EOC                  : codigo_EPS_EOC,
				codigo_CCF                      : codigo_CCF,
				pais_residencia                 : pais_residencia,
				departamento_residencia         : departamento_residencia,
				ciudad_residencia               : ciudad_residencia,
				direccion                       : direccion,
				barrio                          : barrio,
				telefono                        : telefono,
				telefono_alterno                : telefono_alterno,
				numero_celular                  : numero_celular,
				email                           : email,
				email_empresa                   : email_empresa,
				email_notificaciones            : email_notificaciones,
				grupo_sanguineo                 : grupo_sanguineo,
				rh                              : rh,
				alergico                        : alergico,
				medicamentos                    : medicamentos,

			}
		});

	}

	function validarEmail( email ) {
    	expr = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3,4})+$/;
    	if ( !expr.test(email) )
    	    return false
	}
</script>
