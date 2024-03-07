<?php


	//=====================================// WEB SERVICE //=====================================//
	//*******************************************************************************************//
	$whereEmpresa = ($arrayWs['grupo_empresarial'] > 0)? "grupo_empresarial = '$arrayWs[grupo_empresarial]'": "documento = '$arrayWs[nit_empresa]'";

	$sqlEmpresa   = "SELECT id FROM empresas WHERE activo=1 AND $whereEmpresa";
	$queryEmpresa = mysql_query($sqlEmpresa,$conexion);

	if(!$queryEmpresa){ return array("estado" => 'false', "msj" => $sqlEmpresa); }					//ERROR EN LA CONSULTA MYSQL
	while ($rowEmpresa = mysql_fetch_assoc($queryEmpresa)) {
		$response = insertUpdateTerceroEmpresa($arrayWs,$rowEmpresa['id'],$conexion);
		if($response['estado'] == 'error') return $response;
	}

	return array('estado' => 'true');

	//FUNCION POR CADA UNA DE LAS EMPRESAS
	function insertUpdateTerceroEmpresa($arrayWs,$idEmpresaErp,$conexion){
		$infoTercero = $arrayWs['tercero'];

		//=============================// VALIDACION DATOS TERCERO //============================//
		//***************************************************************************************//
		$infoTercero['cliente']    = (strtolower($infoTercero['cliente']) != 'no')? 'Si':'No';
		$infoTercero['proveedor']  = (strtolower($infoTercero['proveedor']) != 'no')? 'Si':'No';
		$infoTercero['exento_iva'] = (strtolower($infoTercero['exento_iva']) != 'si')? 'No':'Si';


		//================================// UBICACION TERCERO //================================//
		//***************************************************************************************//

		//PAIS
		if($infoTercero['pais'] != ''){
			$queryUbicacion = validatePais($infoTercero['pais'],$conexion);
			if($queryUbicacion['estado'] != 'true'){ return $queryUbicacion; }
			else{ $infoTercero['id_pais'] = $queryUbicacion['id_pais']; }
		}
		else{ $infoTercero['id_pais'] = $arrayWs['id_pais']; }

		//DEPARTAMENTO
		if($infoTercero['departamento'] != ''){
			$queryUbicacion = validateDepartamento($infoTercero['id_pais'],$infoTercero['departamento'],$conexion);
			if($queryUbicacion['estado'] != 'true'){ return $queryUbicacion; }
			else{ $infoTercero['id_departamento'] = $queryUbicacion['id_departamento']; }
		}
		else{ $infoTercero['id_departamento'] = $arrayWs['id_departamento']; }

		//CIUDAD
		if($infoTercero['ciudad'] != ''){
			$queryUbicacion = validateCiudad($infoTercero['id_pais'],$infoTercero['id_departamento'],$infoTercero['ciudad'],$conexion);
			if($queryUbicacion['estado'] != 'true'){ return $queryUbicacion; }
			else{ $infoTercero['id_ciudad'] = $queryUbicacion['id_ciudad']; }
		}
		else{ $infoTercero['id_ciudad'] = $arrayWs['id_ciudad']; }



		//ARRAY TIPOS DE DOCUMENTOS
		$sqlTipoNitTercero   = "SELECT id,nombre FROM tipo_documento WHERE id_empresa='$idEmpresaErp'";
		$queryTipoNitTercero = mysql_query($sqlTipoNitTercero,$conexion);
		if(!$queryTipoNitTercero || mysql_num_rows($queryTipoNitTercero) == 0){ return array("estado" => "error", "msj" => 'ErrorConsultaTipoDocumentoTercero'); }				//ERROR EN LA CONSULTA MYSQL

		while ($rowTipoDocumento = mysql_fetch_assoc($queryTipoNitTercero)) {
			$documento = $rowTipoDocumento['nombre'];
			$tablaTipoDocumento[$documento] = $rowTipoDocumento['id'];
		}

		//ARRAY TRATAMIENTO TERCERO
		$sqlTratamiento   = "SELECT COUNT(id) AS cont, id, nombre FROM terceros_tratamiento WHERE id_empresa='$idEmpresaErp'";
		$queryTratamiento = mysql_query($sqlTratamiento,$conexion);
		if(!$queryTratamiento){ return array("estado" => "error", "msj" => 'ErrorConsultaTratamientoTercero'); }				//ERROR EN LA CONSULTA MYSQL

		while ($rowTratamiento = mysql_fetch_assoc($queryTratamiento)) {
			$nombre = $rowTratamiento['nombre'];
			$tablaTratamiento[$nombre] = $rowTratamiento['id'];
		}

		$idTipoNitTercero       = $tablaTipoDocumento[$infoTercero['tipo_identificacion']];
		$idNitRepresentante = $tablaTipoDocumento[$infoTercero['tipo_identificacion_representante']];

		//SECTOR EMPRESARIAL
		$idSectorEmpresa = '';
		if($infoTercero['sector_empresarial'] <> ''){
			$sqlSectorEmpresa   = "SELECT COUNT(id) AS cont, id FROM configuracion_sector_empresarial WHERE nombre='$infoTercero[sector_empresarial]' AND id_empresa='$idEmpresaErp' LIMIT 0,1";
			$querySectorEmpresa = mysql_query($sqlSectorEmpresa,$conexion);
			$contSectorEmpresa  = mysql_result($querySectorEmpresa, 0, 'cont');
			$idSectorEmpresa    = mysql_result($querySectorEmpresa, 0, 'id');
			if(!$querySectorEmpresa || $contSectorEmpresa == 0){ return array("estado" => "error", "msj" => 'ErrorConsultaSectorEmpresa'); }				//ERROR EN LA CONSULTA MYSQL
		}

		//TIPO REGIMEN
		$sqlRegimen   = "SELECT COUNT(id) AS cont,id FROM terceros_tributario WHERE nombre='$infoTercero[nombre_regimen]' AND id_pais='$arrayWs[id_pais]' LIMIT 0,1";
		$queryRegimen = mysql_query($sqlRegimen,$conexion);
		$contRegimen  = mysql_result($queryRegimen, 0, 'cont');
		$idRegimenErp = mysql_result($queryRegimen, 0, 'id');

		//TERCERO
		$sqlTercero   = "SELECT COUNT(id) AS contTercero, id FROM terceros WHERE numero_identificacion='$infoTercero[numero_identificacion]' AND activo=1 AND id_empresa='$idEmpresaErp' LIMIT 0,1";
		$queryTercero = mysql_query($sqlTercero,$conexion);
		$contTercero  = mysql_result($queryTercero, 0, 'contTercero');
		$idTerceroErp = mysql_result($queryTercero, 0, 'id');
		//SI NO EXISTE INSERT
		if($contTercero == 0){
			$sqlInsertTercero = "INSERT INTO terceros (
									id_empresa,
									id_tipo_identificacion,
									ciudad_identificacion,
									dv,
									numero_identificacion,
									nombre,
									nombre_comercial,
									direccion,
									telefono1,
									telefono2,
									celular1,
									celular2,
									id_pais,
									id_departamento,
									id_ciudad,
									pagina_web,
									representante_legal,
									ciudad_id_representante,
									ciudad_representante,
									id_tipo_identificacion_representante,
									identificacion_representante,
									fecha_creacion,
									id_sector_empresarial,
									tipo_cliente,
									tipo_proveedor,
									exento_iva,
									id_tercero_tributario,
									nombre1,
									nombre2,
									apellido1,
									apellido2
								)
								VALUES(
									'$idEmpresaErp',
									'$idTipoNitTercero',
									'$infoTercero[ciudad_identificacion]',
									'$infoTercero[dv]',
									'$infoTercero[numero_identificacion]',
									'$infoTercero[nombre]',
									'$infoTercero[nombre_comercial]',
									'$infoTercero[direccion]',
									'$infoTercero[telefono1]',
									'$infoTercero[telefono2]',
									'$infoTercero[celular1]',
									'$infoTercero[celular2]',
									'$infoTercero[id_pais]',
									'$infoTercero[id_departamento]',
									'$infoTercero[id_ciudad]',
									'$infoTercero[pagina_web]',
									'$infoTercero[representante_legal]',
									'$infoTercero[ciudad_representante]',
									'$infoTercero[ciudad_representante]',
									'$idNitRepresentante',
									'$infoTercero[identificacion_representante]',
									Now(),
									'$idSectorEmpresa',
									'$infoTercero[cliente]',
									'$infoTercero[proveedor]',
									'$infoTercero[exento_iva]',
									'$idRegimenErp',
									'$infoTercero[nombre1]',
									'$infoTercero[nombre2]',
									'$infoTercero[apellido1]',
									'$infoTercero[apellido2]'
								)";

			$queryInsertTercero = mysql_query($sqlInsertTercero,$conexion);
			$sqlIdTerceroErp    = "SELECT LAST_INSERT_ID()";
			$idTerceroErp       = mysql_result(mysql_query($sqlIdTerceroErp,$conexion),0,0);
		}
		//SI EXISTE UPDATE
		else{
			$sqlUpdateTercero = "UPDATE terceros
								SET id_tipo_identificacion = '$idTipoNitTercero',
									ciudad_identificacion = '$infoTercero[ciudad_identificacion]',
									dv = '$infoTercero[dv]',
									numero_identificacion = '$infoTercero[numero_identificacion]',
									nombre = '$infoTercero[nombre]',
									nombre_comercial = '$infoTercero[nombre_comercial]',
									direccion = '$infoTercero[direccion]',
									telefono1 = '$infoTercero[telefono1]',
									telefono2 = '$infoTercero[telefono2]',
									celular1 = '$infoTercero[celular1]',
									celular2 = '$infoTercero[celular2]',
									pagina_web = '$infoTercero[pagina_web]',
									representante_legal = '$infoTercero[representante_legal]',
									ciudad_id_representante = '$infoTercero[ciudad_representante]',
									ciudad_representante = '$infoTercero[ciudad_representante]',
									identificacion_representante = '$infoTercero[identificacion_representante]',
									fecha_creacion = Now(),
									tipo_cliente = '$infoTercero[cliente]',
									tipo_proveedor = '$infoTercero[proveedor]',
									exento_iva = '$infoTercero[exento_iva]',
									id_tercero_tributario = '$idRegimenErp',
									nombre1 = '$infoTercero[nombre1]',
									nombre2 = '$infoTercero[nombre2]',
									apellido1 = '$infoTercero[apellido1]',
									apellido2 = '$infoTercero[apellido2]'
								WHERE numero_identificacion='$infoTercero[numero_identificacion]' AND activo=1";
			$queryUpdateTercero = mysql_query($sqlUpdateTercero,$conexion);
		}
		//===================================================// SINC CONTACTOS //===================================================//
		$arrayEmail = array();
		if(is_array($arrayWs['arrayContactos'])){
			$whereIn = "";

			foreach ($arrayWs['arrayContactos'] as $keyArrayContacto => $subArray) {

				$nitContacto = $subArray['numero_identificacion'];

				//SIN CONTACTOS DEL TERCERO
				$whereIn   .= "OR identificacion = '$subArray[numero_identificacion]' ";
				$idTratamiento     = $tablaTratamiento[$subArray['tratamiento']];
				$idtipoNitContacto = $tablaTipoDocumento[$subArray['tipo_identificacion']];

				$arrayContactoInsert[$nitContacto] = array("idTercero" => "$idTerceroErp",
															"nombre" => "$subArray[nombre]",
															"telefono1" => "$subArray[telefono1]",
															"telefono2" => "$subArray[telefono2]",
															"celular1" => "$subArray[celular1]",
															"celular2" => "$subArray[celular2]",
															"nacimiento" => "$subArray[nacimiento]",
															"observaciones" => "$subArray[observaciones]",
															"sexo" => "$subArray[sexo]",
															"idTratamiento" => "$idTratamiento",
															"identificacion" => "$subArray[numero_identificacion]",
															"idtipoNitContacto" => "$idtipoNitContacto",
															"cargo" => "$subArray[cargo]",
															"direccion" => "$subArray[direccion]",
															"idEmpresaErp" => "$idEmpresaErp");

				$arrayEmail[$nitContacto] = $subArray['emails'];
			}

			$valueInsert = "";
			$whereIn     = substr($whereIn, 3);

			$sqlContactos   = "SELECT identificacion FROM terceros_contactos WHERE activo=1 AND id_tercero='$idTerceroErp' AND ($whereIn) AND id_empresa='$idEmpresaErp' GROUP BY id";
			$queryContactos = mysql_query($sqlContactos,$conexion);
			while ($rowContactos = mysql_fetch_assoc($queryContactos)) {

				$nitContacto = $rowContactos['identificacion'];
				$arrayContactoUpdate[$nitContacto] = $arrayContactoInsert[$nitContacto];

				unset($arrayContactoInsert[$nitContacto]);
			}

			if(COUNT($arrayContactoInsert) > 0){
				$estadoContacto = insertUpdateTerceroContacto($arrayContactoInsert,"INSERT",$conexion);
				if(@$estadoContacto['estado']=='error') return $estadoContacto;
			}

			if(COUNT($arrayContactoUpdate) > 0){
				$estadoContacto = insertUpdateTerceroContacto($arrayContactoUpdate,"UPDATE",$conexion);
				if(@$estadoContacto['estado']=='error') return $estadoContacto;
			}

			//CONSULTA ID CONTACTOS EN ERP
			$arrayIdErpContactos = array();
			$sqlIdErpContactos   = "SELECT id,identificacion FROM terceros_contactos WHERE id_tercero='$idTerceroErp' AND activo=1";
			$queryIdErpContactos = mysql_query($sqlIdErpContactos,$conexion);
			while ($rowContactosErp = mysql_fetch_assoc($queryIdErpContactos)){
				$arrayIdErpContactos[$rowContactosErp['identificacion']] = $rowContactosErp['id'];
			}

			//===============================// SINC EMAIL //===============================//
			//******************************************************************************//

			if(is_array($arrayEmail)){

				$whereIn = "";
				$arrayEmailInsert = array();
				foreach ($arrayEmail as $nitContacto => $emails) {

					foreach ($emails as $email) {
						$idContacto = $arrayIdErpContactos[$nitContacto];
						$whereIn   .= "OR (email = '$email' AND id_contacto= '$idContacto')";

						$arrayEmailInsert[$idContacto][] = $email;
					}
				}

				$valueInsert = "";
				$whereIn     = ($whereIn == '')? '': "AND (".substr($whereIn, 3).")";
				$acum = '';
				$sqlEmail   = "SELECT email,id_contacto FROM terceros_contactos_email WHERE activo=1 $whereIn GROUP BY id,email";
				$queryEmail = mysql_query($sqlEmail,$conexion);
				while ($rowEmail = mysql_fetch_assoc($queryEmail)) {
					$emailErp   = $rowEmail['email'];
					$idContacto = $rowEmail['id_contacto'];

					if(is_array($arrayEmailInsert[$idContacto])){
						$keyExist = array_search($emailErp, $arrayEmailInsert[$idContacto]);
						if($keyExist >= 0) unset($arrayEmailInsert[$idContacto][$keyExist]);

						if(COUNT($arrayEmailInsert[$idContacto]) == 0){ unset($arrayEmailInsert[$idContacto]); }
					}
				}

				if(COUNT($arrayEmailInsert) > 0){
					$estadoEmail = insertUpdateTerceroEmail($arrayEmailInsert,"INSERT",$conexion);
					if(@$estadoEmail['estado'] == 'error'){	return $estadoEmail; }
				}
				// if(COUNT($arrayEmailUpdate) > 0){ insertUpdateTerceroEmail($arrayEmailUpdate,"UPDATE",$conexion); }	//NO HAY FORMA DE VALIDAR CUANDO UN EMAIL SE HA UPDATE
			}
		}

		//================================// SINC SUCURSALES //===============================//
		//************************************************************************************//
		$arrayDireccionInsert = array();
		if(is_array($arrayWs['arraySucursales'])){
			$whereIn = "";

			foreach ($arrayWs['arraySucursales'] as $arrayDireccion) {
				$arrayDireccion['idTerceroErp'] = $idTerceroErp;

				$nombre   = $arrayDireccion['nombre'];
				$whereIn .= "OR nombre = '$nombre' ";

				$arrayDireccionInsert[$nombre] = $arrayDireccion;
			}

			$valueInsert = "";
			$whereIn     = substr($whereIn, 3);

			$sqlDireccion   = "SELECT nombre FROM terceros_direcciones WHERE activo=1 AND id_tercero='$idTerceroErp' AND ($whereIn) GROUP BY id";
			$queryDireccion = mysql_query($sqlDireccion,$conexion);
			while ($rowDireccion = mysql_fetch_assoc($queryDireccion)) {

				$nombre = $rowDireccion['nombre'];

				if(is_array($arrayDireccionInsert[$nombre])){
					$arrayDireccionUpdate[$nombre] = $arrayDireccionInsert[$nombre];
					unset($arrayDireccionInsert[$nombre]);
				}
			}

			if(COUNT($arrayDireccionInsert) > 0){
				$estadoSucursal = insertUpdateTerceroSucursales($infoTercero,$arrayDireccionInsert,"INSERT",$conexion);
				if(@$estadoSucursal['estado'] == 'error'){ return $estadoSucursal; }
			}

			if(COUNT($arrayDireccionUpdate) > 0){
				$estadoSucursal = insertUpdateTerceroSucursales($infoTercero,$arrayDireccionUpdate,"UPDATE",$conexion);
				if(@$estadoSucursal['estado'] == 'error'){ return $estadoSucursal; }
			}
		}
	}

	function insertUpdateTerceroContacto($arrayContacto,$action,$conexion){

		if($action == "INSERT"){

			$valueInsert = "";
			foreach ($arrayContacto as $nitContacto => $infoContacto) { $valueInsert  = "('".join("','",$infoContacto)."'),"; }

			//FILTRO CONTACTOS POR INSERTAR
			if($valueInsert != ""){
				$valueInsert        = substr($valueInsert, 0, -1);
				$sqlInsertContactos = "INSERT INTO terceros_contactos (
											id_tercero,
											nombre,
											telefono1,
											telefono2,
											celular1,
											celular2,
											nacimiento,
											observaciones,
											sexo,
											id_tratamiento,
											identificacion,
											id_tipo_identificacion,
											cargo,
											direccion,
											id_empresa)
										VALUES $valueInsert";
				$queryInsertContactos = mysql_query($sqlInsertContactos,$conexion);
				// return array('estado' => 'error', 'msjsa' => $sqlInsertContactos);
			}
		}
		else if($action == "UPDATE"){
			foreach ($arrayContacto as $nitContacto => $contacto) {

				$sqlUpdateContacto = "UPDATE terceros_contactos
									SET nombre = '$contacto[nombre]',
										telefono1 = '$contacto[telefono1]',
										telefono2 = '$contacto[telefono2]',
										celular1 = '$contacto[celular1]',
										celular2 = '$contacto[celular2]',
										nacimiento = '$contacto[nacimiento]',
										observaciones = '$contacto[observaciones]',
										sexo = '$contacto[sexo]',
										id_tratamiento = '$contacto[idTratamiento]',
										id_tipo_identificacion = '$contacto[idtipoNitContacto]',
										cargo = '$contacto[cargo]',
										direccion = '$contacto[direccion]'
									WHERE identificacion = '$nitContacto'
										AND activo=1";
				$queryUpdateContacto = mysql_query($sqlUpdateContacto,$conexion);
			}
		}
	}


	function insertUpdateTerceroEmail($arrayEmails,$action,$conexion){

		if($action == "INSERT"){
			$valueInsert = "";
			foreach ($arrayEmails as $idContacto => $emails) {
				foreach ($emails as $email) {
					$valueInsert .= "('$idContacto','$email'),";
				}
			}

			$valueInsert      = substr($valueInsert, 0, -1);
			$sqlInsertEmail   = "INSERT INTO terceros_contactos_email (id_contacto,email) VALUES $valueInsert";
			$queryInsertEmail = mysql_query($sqlInsertEmail,$conexion);
		}
		else if($action == "UPDATE"){								//NO APLICA PARA EL PRESENTE WS
			foreach ($arrayEmail as $idContacto => $infoEmail) {
				$sqlUpdateEmail   = "UPDATE terceros_contactos SET email = '$infoEmail[email]' WHERE activo=1";
				$queryUpdateEmail = mysql_query($sqlUpdateEmail,$conexion);
			}
		}
	}

	function insertUpdateTerceroSucursales($arrayTercero,$arraySucursal,$action,$conexion){

		if($action == "INSERT"){

			$valueInsert = "";
			foreach ($arraySucursal as $infoSucursal) {

				//PAIS
				if($infoSucursal['pais'] != ''){
					$queryUbicacion = validatePais($infoSucursal['pais'],$conexion);
					if($queryUbicacion['estado'] != 'true'){ return $queryUbicacion; }
					else{ $infoSucursal['id_pais'] = $queryUbicacion['id_pais']; }
				}
				else{ $infoSucursal['id_pais'] = $arrayTercero['id_pais']; }

				//DEPARTAMENTO
				if($infoSucursal['departamento'] != ''){
					$queryUbicacion = validateDepartamento($infoSucursal['id_pais'],$infoSucursal['departamento'],$conexion);
					if($queryUbicacion['estado'] != 'true'){ return $queryUbicacion; }
					else{ $infoSucursal['id_departamento'] = $queryUbicacion['id_departamento']; }
				}
				else{ $infoSucursal['id_departamento'] = $arrayTercero['id_departamento']; }

				//CIUDAD
				if($infoSucursal['ciudad'] != ''){
					$queryUbicacion = validateCiudad($infoSucursal['id_pais'],$infoSucursal['id_departamento'],$infoSucursal['ciudad'],$conexion);
					if($queryUbicacion['estado'] != 'true'){ return $queryUbicacion; }
					else{ $infoSucursal['id_ciudad'] = $queryUbicacion['id_ciudad']; }
				}
				else{ $infoSucursal['id_ciudad'] = $arrayTercero['id_ciudad']; }

				$valueInsert .= "('$infoSucursal[idTerceroErp]',
									'$infoSucursal[direccion]',
									'$infoSucursal[id_pais]',
									'$infoSucursal[id_departamento]',
									'$infoSucursal[id_ciudad]',
									'$infoSucursal[telefono1]',
									'$infoSucursal[telefono2]',
									'$infoSucursal[celular1]',
									'$infoSucursal[celular2]',
									'$infoSucursal[nombre]'
									),";
			}

			//FILTRO DIRECCIONES POR INSERTAR
			if($valueInsert != ""){
				$valueInsert    = substr($valueInsert, 0, -1);
				$sqlInsertSucursal = "INSERT INTO terceros_direcciones (
										id_tercero,
										direccion,
										id_pais,
										id_departamento,
										id_ciudad,
										telefono1,
										telefono2,
										celular1,
										celular2,
										nombre
									)
									VALUES $valueInsert";
				$queryInsertSucursal = mysql_query($sqlInsertSucursal,$conexion);
				if(!$queryInsertSucursal){ return array("estado" => "error", "msj" => 'Error al agregar las sucursales del tercero'); }
			}
		}
		else if($action == "UPDATE"){
			foreach ($arraySucursal as $infoSucursal) {

				//PAIS
				if($infoSucursal['pais'] != ''){
					$queryUbicacion = validatePais($infoSucursal['pais'],$conexion);
					if($queryUbicacion['estado'] != 'true'){ return $queryUbicacion; }
					else{ $infoSucursal['id_pais'] = $queryUbicacion['id_pais']; }
				}
				else{ $infoSucursal['id_pais'] = $arrayTercero['id_pais']; }

				//DEPARTAMENTO
				if($infoSucursal['departamento'] != ''){
					$queryUbicacion = validateDepartamento($infoSucursal['id_pais'],$infoSucursal['departamento'],$conexion);
					if($queryUbicacion['estado'] != 'true'){ return $queryUbicacion; }
					else{ $infoSucursal['id_departamento'] = $queryUbicacion['id_departamento']; }
				}
				else{ $infoSucursal['id_departamento'] = $arrayTercero['id_departamento']; }

				//CIUDAD
				if($infoSucursal['ciudad'] != ''){
					$queryUbicacion = validateCiudad($infoSucursal['id_pais'],$infoSucursal['id_departamento'],$infoSucursal['ciudad'],$conexion);
					if($queryUbicacion['estado'] != 'true'){ return $queryUbicacion; }
					else{ $infoSucursal['id_ciudad'] = $queryUbicacion['id_ciudad']; }
				}
				else{ $infoSucursal['id_ciudad'] = $arrayTercero['id_ciudad']; }


				$sqlUpdateSucursal   = "UPDATE terceros_direcciones
										SET id_tercero = '$infoSucursal[idTerceroErp]',
											direccion = '$infoSucursal[direccion]',
											id_pais = '$infoSucursal[id_pais]',
											id_departamento = '$infoSucursal[id_departamento]',
											id_ciudad = '$infoSucursal[id_ciudad]',
											telefono1 = '$infoSucursal[telefono1]',
											telefono2 = '$infoSucursal[telefono2]',
											celular1 = '$infoSucursal[celular1]',
											celular2 = '$infoSucursal[celular2]'
										WHERE nombre = '$infoSucursal[nombre]' AND activo=1";
				$queryUpdateSucursal = mysql_query($sqlUpdateSucursal,$conexion);
				if(!$queryUpdateSucursal){ return array("estado" => "error", "msj" => 'Error al actualizar las sucursales del tercero'); }
			}
		}
	}

?>

