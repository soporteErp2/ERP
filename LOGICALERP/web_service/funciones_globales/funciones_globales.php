<?php

	//FUNCIONES API V1 TERCEROS
	function validatePais($pais,$conexion){
		$sqlPais    = "SELECT id
						FROM ubicacion_pais
						WHERE pais COLLATE latin1_spanish_ci = '$pais'
						GROUP BY id LIMIT 0,1";
		$queryPais  = mysql_query($sqlPais, $conexion);
		$contResult = mysql_num_rows($queryPais);

		if($contResult == 1){ return array('estado'=>'true', 'id_pais'=>mysql_result($queryPais, 0, 'id')); }
		else{ return array('estado'=>'error', 'msj'=>'el pais no se encuentra en la base de datos'); }
	}

	function validateDepartamento($id_pais,$departamento,$conexion){
		$sqlDepartamento   = "SELECT id
						FROM ubicacion_departamento
						WHERE id_pais='$id_pais'
							AND departamento COLLATE latin1_spanish_ci LIKE '$departamento%'
						GROUP BY id";
		$queryDepartamento = mysql_query($sqlDepartamento);

		$contResult = mysql_num_rows($queryDepartamento);

		if($contResult == 1){ return array('estado'=>'true', 'id_departamento'=>mysql_result($queryDepartamento, 0, 'id')); }
		else if($contResult > 1){ return array('estado'=>'warning', 'msj'=>'No se puede diferenciar el departamento'); }
		else{ return array('estado'=>'error', 'msj'=>'el departamento no se encuentra en la base de datos'); }
	}

	function validateCiudad($id_pais,$id_departamento, $ciudad, $conexion){
		$sqlCiudad   = "SELECT id
						FROM ubicacion_ciudad
						WHERE id_pais='$id_pais'
							AND id_departamento='$id_departamento'
							AND ciudad COLLATE latin1_spanish_ci ='$ciudad'
						GROUP BY id";
		$queryCiudad = mysql_query($sqlCiudad);

		$contResult = mysql_num_rows($queryCiudad);

		if($contResult == 1){ return array('estado'=>'true', 'id_ciudad'=>mysql_result($queryCiudad, 0, 'id')); }
		else if($contResult > 1){ return array('estado'=>'warning', 'msj'=>'No se puede diferenciar la ciudad'); }
		else{ return array('estado'=>'error', 'msj'=>'La ciudad no se encuentra en la base de datos'); }
	}

	//FUNCIONES API V2 TERCEROS
	function ubicacion($codigo){
	    $sqlCiudad   = "SELECT COUNT(id) AS contUbicacion, id AS id_ciudad,ciudad,id_pais,id_departamento FROM ubicacion_ciudad WHERE id = '$codigo' LIMIT 0,1";
	    $queryCiudad = mysql_query($sqlCiudad);

		$contUbicacion   = mysql_result($queryCiudad,0,'contUbicacion');
		$id_pais         = mysql_result($queryCiudad,0,'id_pais');
		$id_departamento = mysql_result($queryCiudad,0,'id_departamento');
		$id_ciudad       = mysql_result($queryCiudad,0,'id_ciudad');
		$ciudad          = mysql_result($queryCiudad,0,'ciudad');

		$estado = ($contUbicacion > 0)? "true": "false";
	    return array("estado"=>$estado, "ciudad"=>$ciudad, "id_pais"=>$id_pais, "id_departamento"=>$id_departamento, "id_ciudad"=>$id_ciudad);
	}

	function tipo_documento($codigo, $id_empresa){
	    $sqlTipo   = "SELECT COUNT(id) AS contTipo,id,codigo,tipo FROM tipo_documento WHERE id_empresa='$id_empresa' AND codigo='$codigo' LIMIT 0,1";
		$queryTipo = mysql_query($sqlTipo);

		$contTipo = mysql_result($queryTipo,0,'contTipo');
		$id       = mysql_result($queryTipo,0,'id');
		$codigo   = mysql_result($queryTipo,0,'codigo');
		$tipo     = mysql_result($queryTipo,0,'tipo');

		$estado = ($contTipo > 0)? "true": "false";
	    return array("estado"=>$estado, "id"=>$id, "codigo"=>$codigo, "tipo"=>$tipo);
	}

	function tipo_regimen($id_pais,$codigo){
		$sqlRegimen   = "SELECT COUNT(id) AS contRegimen,id FROM terceros_tributario WHERE id_pais='$id_pais' AND codigo='$codigo' LIMIT 0,1";
	    $queryRegimen = mysql_query($sqlRegimen);

		$cont = mysql_result($queryRegimen,0,'contRegimen');
		$id   = mysql_result($queryRegimen,0,'id');

		$estado = ($cont > 0)? "true": "false";
	    return array("estado"=>$estado, "id"=>$id);
	}

?>