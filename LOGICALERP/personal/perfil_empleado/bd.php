<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$id_empresa = $_SESSION['EMPRESA'];

	switch ($opc) {
		case 'busca_departamento':
			busca_departamento($id_pais,$opcion,$id_empleado,$mysql);
			break;
		case 'busca_ciudad':
			busca_ciudad($id_depto,$opcion,$id_empleado,$mysql);
			break;
		case 'guardar_datos_personales':
			guardar_datos_personales($id_empleado,$pais_documento,$departamento_documento,$ciudad_documento,$fecha_nacimiento,$pais_nacimiento,$departamento_nacimiento,$ciudad_nacimiento,$sexo,$extranjero_obligado_cotizar,$residente_en_exterior,$tipo_cotizante,$subtipo_cotizante,$codigo_departamento_laboral,$codigo_municipio_laboral,$codigo_administradora_pensiones,$tipo_entidad_salud,$codigo_EPS_EOC,$codigo_CCF,$pais_residencia,$departamento_residencia,$ciudad_residencia,$direccion,$barrio,$telefono,$telefono_alterno,$numero_celular,$email,$email_empresa,$email_notificaciones,$grupo_sanguineo,$rh,$alergico,$medicamentos,$id_empresa,$mysql);
			break;
		case 'agregar_editar_informacion_familiar':
			agregar_editar_informacion_familiar($id_empleado,$id,$id_parentesco,$nombres,$apellidos,$ocupacion,$direccion,$telefono,$celular,$id_empresa,$mysql);
			break;
		case 'eliminar_informacion_familiar':
			eliminar_informacion_familiar($id,$mysql);
			break;

		case 'agregar_editar_informacion_academica':
			agregar_editar_informacion_academica($id,$id_empleado,$tipo_estudio,$titulo_obtenido,$institucion,$fecha_inicio,$fecha_fin,$ciclo,$modalidad,$ciudad,$mysql);
			break;
		case 'eliminar_informacion_academica':
			eliminar_informacion_academica($id,$mysql);
			break;
		case 'agregar_editar_informacion_idioma':
			agregar_editar_informacion_idioma($id,$id_empleado,$idioma,$nativo,$institucion,$ciudad,$lectura,$escritura,$habla,$mysql);
			break;
		case 'eliminar_informacion_idioma':
			eliminar_informacion_idioma($id,$mysql);
			break;
		case 'agregar_editar_experiencia_laboral':
			agregar_editar_experiencia_laboral($id,$id_empleado,$empresa,$nombre_empresa,$ciudad,$cargo,$actividad,$fecha_inicio,$fecha_fin,$jefe_inmediato,$telefono,$tipo_salario,$valor_mensual,$otros_ingresos,$mysql);
			break;
		case 'eliminar_experiencia_laboral':
			eliminar_experiencia_laboral($id,$mysql);
			break;
	}

	//////////////////////////////////////
	//	PESTAÑA INFORMACION PERSONAL 	//
	//////////////////////////////////////

	// LISTAR LOS DEPARTAMENTOS DE UN PAIS
	function busca_departamento($id_pais,$opcion,$id_empleado,$mysql){
		if ($opcion == 'residencia') { $campo = " id_departamento AS id_departamento"; }
		else if ($opcion=='documento'){ $campo = " id_departamento_documento AS id_departamento"; }
		else{ $campo = " id_departamento_nacimiento AS id_departamento"; }
		$sql="SELECT $campo FROM empleados WHERE activo=1 AND id=$id_empleado";
		$query=$mysql->query($sql,$mysql->link);
		$id_departamento = $mysql->result($query,0,'id_departamento');

		$sql="SELECT id, departamento FROM ubicacion_departamento WHERE activo=1 AND id_pais = $id_pais";
		$query=$mysql->query($sql,$mysql->link);
		while ($row=$mysql->fetch_array($query)) {
			$select =($id_departamento==$row['id'])? 'selected' : '' ;
			$options.='<option value="'.$row['id'].'" '.$select.'>'.$row['departamento'].'</option>';
		}

		if ($opcion == 'residencia') {
			echo '<select onchange="busca_ciudad(this.value,\'residencia\')" id="departamento_residencia"><option>Seleccione...</option>'.$options.'</select>
				<script>
					busca_ciudad(document.getElementById("departamento_residencia").value,"residencia");
				</script>';
		}
		else if ($opcion=='documento') {
			echo '<select onchange="busca_ciudad(this.value,\'documento\')" id="departamento_documento"><option>Seleccione...</option>'.$options.'</select>
				<script>
					busca_ciudad(document.getElementById("departamento_documento").value,"documento");
				</script>';
		}
		else{
			echo '<select onchange="busca_ciudad(this.value)" id="departamento_nacimiento"><option>Seleccione...</option>'.$options.'</select>
				<script>
					busca_ciudad(document.getElementById("departamento_nacimiento").value);
				</script>';
		}
	}

	// LISTAR LAS CIUDADES
	function busca_ciudad($id_depto,$opcion,$id_empleado,$mysql){
		if ($opcion == 'residencia') { $campo = "id_ciudad AS id_ciudad"; }
		else if ($opcion=='documento') { $campo = "id_ciudad_documento AS id_ciudad"; }
		else{ $campo = "id_ciudad_nacimiento AS id_ciudad"; }

		$sql="SELECT $campo FROM empleados WHERE activo=1 AND id=$id_empleado";
		$query=$mysql->query($sql,$mysql->link);
		$id_ciudad = $mysql->result($query,0,'id_ciudad');

		$sql="SELECT id, ciudad FROM ubicacion_ciudad WHERE activo=1 AND id_departamento = $id_depto";
		$query=$mysql->query($sql,$mysql->link);
		while ($row=$mysql->fetch_array($query)) {
			$select =($id_ciudad==$row['id'])? 'selected' : '' ;
			$options.='<option value="'.$row['id'].'" '.$select.'>'.$row['ciudad'].'</option>';
		}

		if ($opcion == 'residencia') {
			echo '<select id="ciudad_residencia"><option>Seleccione...</option>'.$options.'</select>';
		}

		else if ($opcion=='documento') {
			echo '<select id="ciudad_documento"><option>Seleccione...</option>'.$options.'</select>';
		}

		else{
			echo '<select id="ciudad_nacimiento"><option>Seleccione...</option>'.$options.'</select>';
		}
	}

	// GUARDAR O ACTUALIZAR DATOS PERSONALES DEL EMPLEADOS
	function guardar_datos_personales($id_empleado,$pais_documento,$departamento_documento,$ciudad_documento,$fecha_nacimiento,$pais_nacimiento,$departamento_nacimiento,$ciudad_nacimiento,$sexo,$extranjero_obligado_cotizar,$residente_en_exterior,$tipo_cotizante,$subtipo_cotizante,$codigo_departamento_laboral,$codigo_municipio_laboral,$codigo_administradora_pensiones,$tipo_entidad_salud,$codigo_EPS_EOC,$codigo_CCF,$pais_residencia,$departamento_residencia,$ciudad_residencia,$direccion,$barrio,$telefono,$telefono_alterno,$numero_celular,$email,$email_empresa,$email_notificaciones,$grupo_sanguineo,$rh,$alergico,$medicamentos,$id_empresa,$mysql){

		// INFORMACION DEL EMPLEADO
		$sql="UPDATE empleados
				SET
					id_pais_documento               = '$pais_documento',
					id_departamento_documento       = '$departamento_documento',
					id_ciudad_documento             = '$ciudad_documento',
					fecha_nacimiento                = '$fecha_nacimiento',
					id_pais_nacimiento              = '$pais_nacimiento',
					id_departamento_nacimiento      = '$departamento_nacimiento',
					id_ciudad_nacimiento            = '$ciudad_nacimiento',
					sexo                            = '$sexo',
					extranjero_obligado_cotizar     = '$extranjero_obligado_cotizar',
					residente_en_exterior           = '$residente_en_exterior',
					tipo_cotizante                  = '$tipo_cotizante',
					subtipo_cotizante               = '$subtipo_cotizante',
					codigo_departamento_laboral     = '$codigo_departamento_laboral',
					codigo_municipio_laboral        = '$codigo_municipio_laboral',
					codigo_administradora_pensiones = '$codigo_administradora_pensiones',
					tipo_entidad_salud              = '$tipo_entidad_salud',
					codigo_EPS_EOC                  = '$codigo_EPS_EOC',
					codigo_CCF                      = '$codigo_CCF',
					id_pais                         = '$pais_residencia',
					id_departamento                 = '$departamento_residencia',
					id_ciudad                       = '$ciudad_residencia',
					direccion                       = '$direccion',
					barrio                          = '$barrio',
					telefono1                       = '$telefono',
					telefono2                       = '$telefono_alterno',
					celular1                        = '$numero_celular',
					email_personal                  = '$email',
					email_empresa                   = '$email_empresa',
					email_notificaciones            = '$email_notificaciones'
				WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_empleado
					";
		$query=$mysql->query($sql,$mysql->link);

		// INFORMACION MEDICA
		$sql="SELECT id FROM empleados_adicional WHERE id_empleado=$id_empleado";
		$query=$mysql->query($sql,$mysql->link);
		$id_registro = $mysql->result($query,0,'id');

		if ($id_registro>0) {
			$sql="UPDATE empleados_adicional
					SET
						grupo_sanguineo           = '$grupo_sanguineo'
						factor_rh                 = '$rh'
						cual_alergico_medicamento = '$alergico'
						cual_toma_medicamento     = '$medicamentos'
					WHERE id_empleado=$id_empleado";
			$query=$mysql->query($sql,$mysql->link);
		}
		else{
			$sql="INSERT INTO empleados_adicional (grupo_sanguineo,factor_rh,cual_alergico_medicamento,cual_toma_medicamento,id_empleado)
					VALUES ('$grupo_sanguineo','$rh','$alergico','$medicamentos','$id_empleado')";
			$query=$mysql->query($sql,$mysql->link);
		}

		echo "<script>MyLoading2('off');</script>";
	}

	//////////////////////////////////////
	//	PESTAÑA INFORMACION FAMILIAR	//
	//////////////////////////////////////

	// GUARDAR O ACTUALIZAR LA INFORMACION FAMILIAR DEL EMPLEADO
	function agregar_editar_informacion_familiar($id_empleado,$id,$id_parentesco,$nombres,$apellidos,$ocupacion,$direccion,$telefono,$celular,$id_empresa,$mysql){
		if ($id>0) {
			$sql="UPDATE empleados_informacion_contacto
					SET
					id_parentesco = '$id_parentesco',
					nombres       = '$nombres',
					apellidos     = '$apellidos',
					ocupacion     = '$ocupacion',
					direccion     = '$direccion',
					telefono      = '$telefono',
					celular       = '$celular'
					WHERE activo=1 AND id=$id";
			$query=$mysql->query($sql,$mysql->link);

			if ($query) {
				$sql    = "SELECT nombre FROM configuracion_tipos_contacto WHERE activo=1 AND id=$id_parentesco";
				$query  = $mysql->query($sql,$mysql->link);
				$nombre = $mysql->result($query,0,'nombre');
				echo '<script>
						MyLoading2("off");
						Win_Ventana_agregar_editar.close();
						document.getElementById("row_informacion_familiar_'.$id.'").innerHTML="<td>'.$nombre.'</td><td>'.$nombres.' '.$apellidos.'</td><td>'.$direccion.'</td><td>'.$telefono.'</td><td>'.$celular.'</td><td>'.$ocupacion.'</td>";
					</script>';
			}
			else{
				echo '<script>
						MyLoading2("off",{icono:"fail",texto:"Error, no se actualizo Intentelo de nuevo"})
					</script>';
			}
		}
		else{
			$sql="INSERT INTO empleados_informacion_contacto (id_parentesco,nombres,apellidos,ocupacion,direccion,telefono,celular,id_empleado)
					VALUES ('$id_parentesco','$nombres','$apellidos','$ocupacion','$direccion','$telefono','$celular',$id_empleado)";
			$query=$mysql->query($sql,$mysql->link);

			if ($query) {
				$id = mysql_insert_id();

				$sql    = "SELECT nombre FROM configuracion_tipos_contacto WHERE activo=1 AND id=$id_parentesco";
				$query  = $mysql->query($sql,$mysql->link);
				$nombre = $mysql->result($query,0,'nombre');

				echo '<script>
						MyLoading2("off");
						Win_Ventana_agregar_editar.close();
						var body_table = document.getElementById("body_grilla_info_familiar").innerHTML;

						document.getElementById("body_grilla_info_familiar").innerHTML = body_table+"<tr ondblclick=\'ventana_agregar_editar('.$id.')\' id=\'row_informacion_familiar_'.$id.'\'>"+
																										"<td>'.$nombre.'</td>"+
																										"<td>'.$nombres.' '.$apellidos.'</td>"+
																										"<td>'.$direccion.'</td>"+
																										"<td>'.$telefono.'</td>"+
																										"<td>'.$celular.'</td>"+
																										"<td>'.$ocupacion.'</td>"+
																									"</tr>";
						// var tr = document.createElement("tr");
						// tr.setAttribute("id","row_informacion_familiar_'.$id.'");
						// tr.setAttribute("ondblclick","ventana_agregar_editar('.$id.')");
						// document.getElementById("body_grilla_info_familiar").appendChild(tr);
					</script>';
			}
			else{
				echo '<script>
						MyLoading2("off",{icono:"fail",texto:"Error, no se inserto Intentelo de nuevo"})
					</script>';
			}
		}
	}

	// ELIMINAR LA INFORMACION FAMILIAR DE UN EMPLEADO
	function eliminar_informacion_familiar($id,$mysql){
		$sql   = "DELETE FROM empleados_informacion_contacto WHERE id=$id";
		$query = $mysql->query($sql,$mysql->link);
		if ($query) {
			echo '<script>
					MyLoading2("off");
					Win_Ventana_agregar_editar.close();
					document.getElementById("row_informacion_familiar_'.$id.'").parentNode.removeChild(document.getElementById("row_informacion_familiar_'.$id.'"));
				</script>';
		}
		else{
			echo '<script>
					MyLoading2("off",{icono:"fail",texto:"Error, no se elimino Intentelo de nuevo"})
				</script>';
		}
	}

	//////////////////////////////////////
	//	PESTAÑA INFORMACION ACADEMICA	//
	//////////////////////////////////////

	// GUARDAR O ACTUALIZAR LA INFORMACION ACADEMICA DEL EMPLEADO
	function agregar_editar_informacion_academica($id,$id_empleado,$tipo_estudio,$titulo_obtenido,$institucion,$fecha_inicio,$fecha_fin,$ciclo,$modalidad,$ciudad,$mysql){
		if ($id>0) {
			$sql="UPDATE empleados_estudios
					SET
					tipo_estudio = '$tipo_estudio',
					institucion  = '$institucion',
					ciudad       = '$ciudad',
					grado        = '$titulo_obtenido',
					fecha_inicio = '$fecha_inicio',
					fecha_fin    = '$fecha_fin',
					ciclo        = '$ciclo',
					modalidad    = '$modalidad'
					WHERE activo=1 AND id=$id";
			$query=$mysql->query($sql,$mysql->link);

			if ($query) {
				echo '<script>
						MyLoading2("off");
						Win_Ventana_agregar_editar_academicos.close();
						document.getElementById("row_informacion_academica_'.$id.'").innerHTML="<td>'.$tipo_estudio.'</td><td>'.$institucion.'</td><td>'.$ciudad.'</td><td>'.$titulo_obtenido.'</td><td>'.$fecha_inicio.'</td><td>'.$fecha_fin.'</td><td>'.$ciclo.'</td><td>'.$modalidad.'</td>";
					</script>';
			}
			else{
				echo '<script>
						MyLoading2("off",{icono:"fail",texto:"Error, no se actualizo Intentelo de nuevo"})
					</script>';
			}
		}
		else{
			$sql="INSERT INTO empleados_estudios (tipo_estudio,institucion,ciudad,grado,fecha_inicio,fecha_fin,ciclo,modalidad,id_empleado)
					VALUES ('$tipo_estudio','$institucion','$ciudad','$titulo_obtenido','$fecha_inicio','$fecha_fin','$ciclo','$modalidad',$id_empleado)";
			$query=$mysql->query($sql,$mysql->link);

			if ($query) {
				$id = mysql_insert_id();

				echo '<script>
						MyLoading2("off");
						Win_Ventana_agregar_editar_academicos.close();
						var body_table = document.getElementById("body_grilla_info_academica").innerHTML;

						document.getElementById("body_grilla_info_academica").innerHTML = body_table+"<tr ondblclick=\'ventana_agregar_editar_academicos('.$id.')\' id=\'row_informacion_academica_'.$id.'\'>"+
																										"<td>'.$tipo_estudio.'</td>"+
																										"<td>'.$institucion.'</td>"+
																										"<td>'.$ciudad.'</td>"+
																										"<td>'.$titulo_obtenido.'</td>"+
																										"<td>'.$fecha_inicio.'</td>"+
																										"<td>'.$fecha_fin.'</td>"+
																										"<td>'.$ciclo.'</td>"+
																										"<td>'.$modalidad.'</td>"+
																									"</tr>";
					</script>';
			}
			else{
				echo '<script>
						MyLoading2("off",{icono:"fail",texto:"Error, no se inserto Intentelo de nuevo"})
					</script>';
			}
		}
	}

	// ELIMINAR REGISTRO DE LA INFORMACION ACADEMICA DEL EMPLEADO
	function eliminar_informacion_academica($id,$mysql){
		$sql   = "DELETE FROM empleados_estudios WHERE id=$id";
		$query = $mysql->query($sql,$mysql->link);
		if ($query) {
			echo '<script>
					MyLoading2("off");
					Win_Ventana_agregar_editar_academicos.close();
					document.getElementById("row_informacion_academica_'.$id.'").parentNode.removeChild(document.getElementById("row_informacion_academica_'.$id.'"));
				</script>';
		}
		else{
			echo '<script>
					MyLoading2("off",{icono:"fail",texto:"Error, no se elimino Intentelo de nuevo"})
				</script>';
		}
	}

	// GUARDAR O ACTUALIZAR LA INFORMACION DE IDIOMAS DE ESE EMPLEADO
	function agregar_editar_informacion_idioma($id,$id_empleado,$idioma,$nativo,$institucion,$ciudad,$lectura,$escritura,$habla,$mysql){
		if ($id>0) {
			$sql="UPDATE empleados_idiomas
					SET
						idioma      = '$idioma',
						nativo      = '$nativo',
						institucion = '$institucion',
						ciudad      = '$ciudad',
						lectura     = '$lectura',
						escritura   = '$escritura',
						habla       = '$habla'
					WHERE activo=1 AND id=$id";
			$query=$mysql->query($sql,$mysql->link);

			if ($query) {
				echo '<script>
						MyLoading2("off");
						Win_Ventana_agregar_editar_idioma.close();
						document.getElementById("row_informacion_idiomas_'.$id.'").innerHTML="<td>'.$idioma.'</td><td>'.$nativo.'</td><td>'.$institucion.'</td><td>'.$ciudad.'</td><td>'.$lectura.'</td><td>'.$escritura.'</td><td>'.$habla.'</td>";
					</script>';
			}
			else{
				echo '<script>
						MyLoading2("off",{icono:"fail",texto:"Error, no se actualizo Intentelo de nuevo"})
					</script>';
			}
		}
		else{
			echo$sql="INSERT INTO empleados_idiomas (idioma,nativo,institucion,ciudad,lectura,escritura,habla,id_empleado)
					VALUES ('$idioma','$nativo','$institucion','$ciudad','$lectura','$escritura','$habla',$id_empleado)";
			$query=$mysql->query($sql,$mysql->link);

			if ($query) {
				$id = mysql_insert_id();

				echo '<script>
						MyLoading2("off");
						Win_Ventana_agregar_editar_idioma.close();
						var body_table = document.getElementById("body_grilla_info_idiomas").innerHTML;

						document.getElementById("body_grilla_info_idiomas").innerHTML =body_table+"<tr ondblclick=\'ventana_agregar_editar_idiomas('.$id.')\' id=\'row_informacion_idiomas_'.$id.'\'>"+
																										"<td>'.$idioma.'</td>"+
																										"<td>'.$nativo.'</td>"+
																										"<td>'.$institucion.'</td>"+
																										"<td>'.$ciudad.'</td>"+
																										"<td>'.$lectura.'</td>"+
																										"<td>'.$escritura.'</td>"+
																										"<td>'.$habla.'</td>"+
																									"</tr>";
					</script>';
			}
			else{
				echo '<script>
						MyLoading2("off",{icono:"fail",texto:"Error, no se inserto Intentelo de nuevo"})
					</script>';
			}
		}
	}

	// ELIMINAR REGISTRO DE LA INFORMACION ACADEMICA
	function eliminar_informacion_idioma($id,$mysql){
		$sql   = "DELETE FROM empleados_idiomas WHERE id=$id";
		$query = $mysql->query($sql,$mysql->link);
		if ($query) {
			echo '<script>
					MyLoading2("off");
					Win_Ventana_agregar_editar_idioma.close();
					document.getElementById("row_informacion_idiomas_'.$id.'").parentNode.removeChild(document.getElementById("row_informacion_idiomas_'.$id.'"));
				</script>';
		}
		else{
			echo '<script>
					MyLoading2("off",{icono:"fail",texto:"Error, no se elimino Intentelo de nuevo"})
				</script>';
		}
	}

	//////////////////////////////////////
	//	PESTAÑA INFORMACION ACADEMICA	//
	//////////////////////////////////////
	function agregar_editar_experiencia_laboral($id,$id_empleado,$empresa,$nombre_empresa,$ciudad,$cargo,$actividad,$fecha_inicio,$fecha_fin,$jefe_inmediato,$telefono,$tipo_salario,$valor_mensual,$otros_ingresos,$mysql){
		if ($id>0) {
			$sql="UPDATE empleados_experiencia_laboral
					SET
					empresa         = '$empresa',
					nombre_empresa  = '$nombre_empresa',
					ciudad          = '$ciudad',
					actividad       = '$actividad',
					cargo           = '$cargo',
					jefe_inmediato  = '$jefe_inmediato',
					telefono        = '$telefono',
					fecha_inicio    = '$fecha_inicio',
					fecha_fin       = '$fecha_fin',
					salario         = '$tipo_salario',
					salario_mensual = '$valor_mensual',
					otros_ingresos  = '$otros_ingresos'
					WHERE activo=1 AND id=$id";
			$query=$mysql->query($sql,$mysql->link);

			if ($query) {
				echo '<script>
						MyLoading2("off");
						Win_ventana_agregar_editar_experiencia_laboral.close();
						document.getElementById("row_informacion_experiencia_laboral_'.$id.'").innerHTML="<td>'.$nombre_empresa.'</td><td>'.$empresa.'</td><td>'.$cargo.'</td><td>'.$fecha_inicio.'</td><td>'.$fecha_fin.'</td><td>'.$telefono.'</td>";
					</script>';
			}
			else{
				echo '<script>
						MyLoading2("off",{icono:"fail",texto:"Error, no se actualizo Intentelo de nuevo"})
					</script>';
			}
		}
		else{
			$sql="INSERT INTO empleados_experiencia_laboral (empresa,nombre_empresa,ciudad,actividad,cargo,jefe_inmediato,telefono,fecha_inicio,fecha_fin,salario,salario_mensual,otros_ingresos,id_empleado)
					VALUES ('$empresa','$nombre_empresa','$ciudad','$actividad','$cargo','$jefe_inmediato','$telefono','$fecha_inicio','$fecha_fin','$tipo_salario','$valor_mensual','$otros_ingresos',$id_empleado)";
			$query=$mysql->query($sql,$mysql->link);

			if ($query) {
				$id = mysql_insert_id();

				echo '<script>
						MyLoading2("off");
						Win_ventana_agregar_editar_experiencia_laboral.close();
						var body_table = document.getElementById("body_grilla_info_experiencia_laboral").innerHTML;

						document.getElementById("body_grilla_info_experiencia_laboral").innerHTML = body_table+"<tr ondblclick=\'Win_ventana_agregar_editar_experiencia_laboral('.$id.')\' id=\'row_informacion_experiencia_laboral_'.$id.'\'>"+
																													"<td>'.$nombre_empresa.'</td>"+
																													"<td>'.$empresa.'</td>"+
																													"<td>'.$cargo.'</td>"+
																													"<td>'.$fecha_inicio.'</td>"+
																													"<td>'.$fecha_fin.'</td>"+
																													"<td>'.$telefono.'</td>"+
																												"</tr>";
					</script>';
			}
			else{
				echo '<script>
						MyLoading2("off",{icono:"fail",texto:"Error, no se inserto Intentelo de nuevo"})
					</script>';
			}
		}
	}

	// ELIMINAR REGISTRO DE LA EXPERIENCIA LABORAL
	function eliminar_experiencia_laboral($id,$mysql){
		$sql   = "DELETE FROM empleados_experiencia_laboral WHERE id=$id";
		$query = $mysql->query($sql,$mysql->link);
		if ($query) {
			echo '<script>
					MyLoading2("off");
					Win_ventana_agregar_editar_experiencia_laboral.close();
					document.getElementById("row_informacion_experiencia_laboral_'.$id.'").parentNode.removeChild(document.getElementById("row_informacion_experiencia_laboral_'.$id.'"));
				</script>';
		}
		else{
			echo '<script>
					MyLoading2("off",{icono:"fail",texto:"Error, no se elimino Intentelo de nuevo"})
				</script>';
		}
	}

?>