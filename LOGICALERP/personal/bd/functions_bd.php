<?php
	function cargaOptionTrueFalse($valueBooleano){
		$selectedTrue  ='';
		$selectedFalse ='';
		$comboOption   ='';

		if($valueBooleano=="true"){ $selectedTrue  ='selected="selected"'; }
		else { $selectedFalse  ='selected="selected"'; }

		echo'<option value="false" '.$selectedFalse.'>No</option>
			<option value="true" '.$selectedTrue.'>Si</option>';
	}

	function cargaOption($tabla,$nombreId,$nombreText,$todos,$select){ /// Carga los options para un select html creandolos de una tabla en la base de datos, todos(true,false) sirve para traer todos los campos de la tabla

		if($todos){ $query = "SELECT * FROM $tabla WHERE activo=1 AND id_empresa='$_SESSION[EMPRESA]'"; }
		else{ $query = "SELECT $nombreId,$nombreText FROM $tabla WHERE activo=1 AND id_empresa='$_SESSION[EMPRESA]'"; }

		$result = mysql_query($query);
		$num    = mysql_numrows($result);
		$i      = 0;

		while ($i < $num) {
			$id          = mysql_result($result,$i,$nombreId);
			$nombre_tipo = mysql_result($result,$i,$nombreText);

			if($select==$id){ echo '<option value="'.$id.'" selected>'.$nombre_tipo.'</option>'; }
			else{ echo '<option value="'.$id.'">'.$nombre_tipo.'</option>'; }

			$i++;
		}
	}

	function cargaEmpleado($id){ /// Carga los options para un select html creandolos de una tabla en la base de datos, todos(true,false) sirve para traer todos los campos de la tabla

		$query  = "SELECT * FROM empleados where id='$id' AND id_empresa='$_SESSION[EMPRESA]'";
		$result = mysql_query($query);
		$i      = 0;

		global $activo,$tipo_id1,$username1,$id1,$nombre1,$nombre2,$apellido1,$apellido2,$rol,$cargo,$mail1,$mail2,$nacimiento1,$direccion1,$telefono1,$telefono2,$celular1,$celular2,
		       $contrato,$rolvalor,$eps,$arp,$salario,$vendedor,$tecnico_operativo,$conductor,$id_pais, $id_departamento, $id_ciudad,$acceso_sistema,$sexo,$estado_civil,$estrato_residencia,
		       $id_pais_nacimiento, $id_departamento_nacimiento, $id_ciudad_nacimiento,$id_pais_documento, $id_departamento_documento, $id_ciudad_documento, $barrio,$tipo_residencia,$observaciones_empleado,$token;

		$activo                     = mysql_result($result,$i,"activo");
		$tipo_id1                   = mysql_result($result,$i,"tipo_documento");
		$id1                        = mysql_result($result,$i,"documento");
		$nombre1                    = mysql_result($result,$i,"nombre1");
		$nombre2                    = mysql_result($result,$i,"nombre2");
		$apellido1                  = mysql_result($result,$i,"apellido1");
		$apellido2                  = mysql_result($result,$i,"apellido2");
		$rol                        = mysql_result($result,$i,"id_rol");
		$cargo                      = mysql_result($result,$i,"id_cargo");
		$username1                  = mysql_result($result,$i,"username");
		$mail1                      = mysql_result($result,$i,"email_empresa");
		$mail2                      = mysql_result($result,$i,"email_personal");
		$nacimiento1                = mysql_result($result,$i,"nacimiento");
		$direccion1                 = mysql_result($result,$i,"direccion");
		$barrio                     = mysql_result($result,$i,"barrio");
		$tipo_residencia            = mysql_result($result,$i,"tipo_residencia");
		$telefono1                  = mysql_result($result,$i,"telefono1");
		$telefono2                  = mysql_result($result,$i,"telefono2");
		$celular1                   = mysql_result($result,$i,"celular_empresa");
		$celular2                   = mysql_result($result,$i,"celular1");
		$arp                        = mysql_result($result,$i,"arp");
		$eps                        = mysql_result($result,$i,"eps");
		$id_pais                    = mysql_result($result,$i,"id_pais");
		$id_departamento            = mysql_result($result,$i,"id_departamento");
		$id_ciudad                  = mysql_result($result,$i,"id_ciudad");
		$id_pais_nacimiento         = mysql_result($result,$i,"id_pais_nacimiento");
		$id_departamento_nacimiento = mysql_result($result,$i,"id_departamento_nacimiento");
		$id_ciudad_nacimiento       = mysql_result($result,$i,"id_ciudad_nacimiento");
		$id_pais_documento          = mysql_result($result,$i,"id_pais_documento");
		$id_departamento_documento  = mysql_result($result,$i,"id_departamento_documento");
		$id_ciudad_documento        = mysql_result($result,$i,"id_ciudad_documento");
		$acceso_sistema             = mysql_result($result,$i,"acceso_sistema");
		$sexo                       = mysql_result($result,$i,"sexo");
		$estado_civil               = mysql_result($result,$i,"estado_civil");
		$estrato_residencia         = mysql_result($result,$i,"estrato_residencia");
		$observaciones_empleado     = mysql_result($result,$i,"observaciones_empleado");
		$token                      = mysql_result($result,$i,"token");
		$rolvalor                   = mysql_result(mysql_query("SELECT valor FROM empleados_roles WHERE id = $rol"),0,'valor');

		$queryAdd = "SELECT * FROM empleados_adicional where id_empleado=".$id;
		$resultAdd=mysql_query($queryAdd);

		global $ciudad_trabajo,$fondo_pension,$tipo_sangre,$alergico_medicamento,$cual_alergico_medicamento,$toma_medicamento,$cual_toma_medicamento,$grupo_sanguineo,$factor_rh;

		//global $activo,$tipo_id1,$username1,$id1,$nombre1,$nombre2,$apellido1,$apellido2,$rol,$cargo,$mail1,$mail2,$nacimiento1,$direccion1,$telefono1,$telefono2,$celular1,$celular2,$contrato,$rolvalor,$eps,$arp,$salario,$vendedor,$tecnico_operativo,$conductor,$extras,$validaingreso,$residente;

		$ciudad_trabajo            = mysql_result($resultAdd,$i,"ciudad_trabajo");
		$fondo_pension             = mysql_result($resultAdd,$i,"fondo_pension");
		$tipo_sangre               = mysql_result($resultAdd,$i,"tipo_sangre");
		$alergico_medicamento      = mysql_result($resultAdd,$i,"alergico_medicamento");
		$cual_alergico_medicamento = mysql_result($resultAdd,$i,"cual_alergico_medicamento");
		$toma_medicamento          = mysql_result($resultAdd,$i,"toma_medicamento");
		$cual_toma_medicamento     = mysql_result($resultAdd,$i,"cual_toma_medicamento");
		$grupo_sanguineo           = mysql_result($resultAdd,$i,"grupo_sanguineo");
		$factor_rh                 = mysql_result($resultAdd,$i,"factor_rh");
	}

	function cargaTextoContrato($id){
		global $texto;

		$query  = "SELECT contrato FROM empleados_contratos where id='$id' AND id_empresa=$_SESSION[EMPRESA]";
		$result = mysql_query($query);
		$texto  = mysql_result($result,$i,"contrato");
	}

	function ubicacion_pais($id_pais){

		$sqlPais   = "SELECT id,pais FROM ubicacion_pais WHERE activo=1 ORDER BY pais ASC";
		$queryPais = mysql_query($sqlPais);

		while ($row = mysql_fetch_assoc($queryPais)) {
			$selected = ($id_pais == $row['id'])? 'selected': '';
			echo '<option value="'.$row['id'].'" '.$selected.'>'.$row['pais'].'</option>';
		}
	}

?>