<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$id_empresa = $_SESSION['EMPRESA'];
	$cuentaAnterior;
	$nombreAnterior;

	switch ($op) {
		case 'inputNuevoItemPuc':
			if ($action != 'editar'){ $cuenta= ''; $descripcion= ''; }
			inputNuevoItemPuc($action,$cuenta,$descripcion,$id_empresa,$link);
			break;

		case 'divCuerpoInsertUpdateItemPuc':
			divCuerpoInsertUpdateItemPuc($link,$newCodigo,$id_empresa,$action,$newNombre,$id,$idSucursalPuc,$title);
			break;

		case 'validarNuevoCodigoItemPuc':
			validarNuevoCodigoItemPuc($link,$newCodigo,$id_empresa,$action,$newNombre,$id,$idSucursalPuc,$title,$crearNiif,$validar);
			break;

		case 'eliminarItemPuc':
			eliminarItemPuc($link,$newCodigo,$id_empresa,$id);
			break;

		case 'editar':
			editar($link,$newCodigo,$id_empresa,$action,$newNombre,$id);
			break;

		case 'configurarCuentaPuc':
			configurarCuentaPuc($id,$id_empresa,$link);
			break;

		case 'actualizarNiif':
			actualizarNiif($id_niif,$id_colgaap,$cuenta_niif,$cuenta_colgaap,$id_empresa,$link);
			break;

		case 'ventanaUpdateNombreCuenta':
			ventanaUpdateNombreCuenta($idCuenta,$id_empresa,$link);
			break;

		case 'updateNombreCuenta':
			updateNombreCuenta($idCuenta,$newNombre,$id_empresa,$link);
			break;

		case 'eliminaCuentaNiif':
			eliminaCuentaNiif($id,$link);
			break;

		case 'ventanaSeleccionarUbicacion':
			ventanaSeleccionarUbicacion($id,$id_empresa,$link);
			break;

		case 'updateComboCiudadPuc':
			updateComboCiudadPuc($idDepartamento,$idCiudad,$id_empresa,$link);
			break;

		case 'guardarUbicacionPuc':
			guardarUbicacionPuc($id,$idDepartamento,$idCiudad,$id_empresa,$link);
			break;

		case 'guardarConfiguracionPuc':
			guardarConfiguracionPuc($id_cuenta_colgaap,$cuentaColgaap,$cuentaNiif,$tipo,$ccosPuc,$cuentaCruce,$sincDescripcion,$descripcionColgaap,$id_empresa,$link);
			break;
	}

	function inputNuevoItemPuc($action,$cuenta,$descripcion,$id_empresa,$link){
		$sql   = "SELECT id,nombre FROM empresas_sucursales WHERE id_empresa='$id_empresa' AND activo=1";
		$query = mysql_query($sql,$link);

		$idSelected     = 0;
		$optionSucursal = '<option value="0">MULTISUCURSAL</option>';
		while ($row = mysql_fetch_array($query)) {
			$optionSucursal .='<option value="'.$row['id'].'">'.$row['nombre'].'</option>';
		}

		if($cuenta > 0){
			$sql        = "SELECT id_sucursal FROM puc WHERE id_empresa='$id_empresa' AND activo=1 AND cuenta='$cuenta'";
			$idSelected = mysql_result(mysql_query($sql,$link),0,'id_sucursal');
		}

		$disable       = '';
		$disableCuenta = '';
		$loadScript    = 'document.getElementById("newCodigoItemPuc").onkeypress   = function(event){ return changeCodigoInputItemPuc(event); };
							document.getElementById("newNombreItemPuc").onkeypress = function(event){ return changeNombreInputItemPuc(event); };
							document.getElementById("newNombreItemPuc").onkeyup    = function(event){ this.value = (this.value).toUpperCase(); };
							document.getElementById("idSucursalPuc").onchange      = function(event){ cambiaSucursalCuentaPuc(this.value,"'.$idSelected.'"); };';

		//BLOQUEAR CUANDO ACTION = EDITAR Y NUMEROS DE DIGITOS
		if ($action=='editar' && strlen($cuenta)<=2) {
			$disable       = 'disabled="disabled"';
			$disableCuenta = 'disabled="disabled"';
			$loadScript    = 'Ext.getCmp("btnGuardarItemPuc").disable();
					    		Ext.getCmp("btnComprobarNewCodigoItemPuc").disable();
					    		Ext.getCmp("btnEliminarItemPuc").disable();
					    		setTimeout(function(){document.getElementById("cuerpoInsertUpdateItemPuc").innerHTML="<div style=\"font-size:14px; font-weight:bold; width:100%; text-align:center;\">Este item puc no se puede eliminar/editar por ser un item raiz</div>";},50);';
		}
		else if($action=='editar'){ $disableCuenta = 'disabled="disabled"'; }
		else{
			$campoCreaNiif .=  "<div style='float:left; margin: 5px 0 0 10px'>
												    <div style='float:left; width:68px; padding:3px 0 0 0'>Crear Niif</div>
												    <div id='recibidor_filtro_empresa' style='float:left; width:160px'>
												    	<select id='crearNiif' class='myfield' style='width:100%'>
															<option value='Si'>Si</option>
															<option value='No'>No</option>
												    	</select>
											    	</div>
											    </div>";

			$campoValidar .= "<div style='float:left; margin: 5px 0 0 10px'>
											    <div style='float:left; width:68px; padding:3px 0 0 0'>Validar</div>
											    <div id='recibidor_filtro_empresa' style='float:left; width:160px'>
											    	<select id='validar' class='myfield' style='width:100%'>
														<option value='si'>Si</option>
														<option value='no'>No</option>
											    	</select>
										    	</div>
										    </div>";
		}

		echo'<div style="float:left; margin: 5px 0 0 10px">
			    <div style="float:left; width:68px; padding:3px 0 0 0">Codigo</div>
			    <div style="float:left; width:160px">
				    <input type="text" class="myfield" id="newCodigoItemPuc" '.$disableCuenta.' style="width:100%" value="'.$cuenta.'"/>
				</div>
			</div>
			<div style="float:left; margin: 5px 0 0 10px">
			    <div style="float:left; width:68px; padding:3px 0 0 0">Nombre</div>
			    <div id="recibidor_filtro_empresa" style="float:left; width:160px">
			    	<input type="text" class="myfield" id="newNombreItemPuc" style="width:100%" '.$disable.' value="'.$descripcion.'"/>
		    	</div>
		    </div>
		    '.$campoCreaNiif.$campoValidar.'
		    <div style="float:left; margin: 5px 0 0 10px">
			    <div style="float:left; width:68px; padding:3px 0 0 0">Sucursal</div>
			    <div id="recibidor_filtro_sucursal" style="float:left; width:160px">
				    <select class="myfield" id="idSucursalPuc" style="width:100%" '.$disable.' value="'.$descripcion.'"/>
				    	'.$optionSucursal .'
				    </select>
		    	</div>
		    </div>
		    <script>
				'.$loadScript.'
		    	document.getElementById("idSucursalPuc").value="'.$idSelected.'";
		    	document.getElementById("newCodigoItemPuc").focus();
		    	Ext.getCmp("btnGuardarItemPuc").disable();
		    </script>';
	}

	function divCuerpoInsertUpdateItemPuc($link,$newCodigo,$id_empresa,$action,$newNombre,$id,$idSucursalPuc,$title){
		echo'<div id="cuerpoInsertUpdateItemPuc" style="margin:10px 10px 20px 10px;"></div>';
		if($action=='cargar'){ validarNuevoCodigoItemPuc($link,$newCodigo,$id_empresa,$action,$newNombre,$id,$idSucursalPuc,$title,'',''); }
		else{ return; }
	}

	function validarNuevoCodigoItemPuc($link,$newCodigo,$id_empresa,$action,$newNombre,$id,$idSucursalPuc,$title,$crearNiif,$validar){

		$sql 			= "SELECT cuenta, descripcion, id_sucursal, id FROM puc WHERE id = $id AND activo = 1 AND id_empresa = '$id_empresa' LIMIT 0,1";
		$query    = mysql_query($sql,$link);
		$camposDb = mysql_fetch_array($query);

		$sqlPUC  	 = "SELECT COUNT(id) AS cantidad,id,cuenta,descripcion FROM puc WHERE cuenta = '$newCodigo' AND activo = 1 AND id_empresa = '$id_empresa' GROUP BY id LIMIT 0,1";
		$queryPUC  = mysql_query($sqlPUC,$link);
		$contFilas = mysql_result($queryPUC,0,'cantidad');
		$idPuc     = mysql_result($queryPUC,0,'id');
		$codigo    = mysql_result($queryPUC,0,'cuenta');
		$itemPuc   = mysql_result($queryPUC,0,'descripcion');

		//===========================// CONDICIONAL SI EL CODIGO EXISTE Y NO HAY CAMBIOS RETURN //===========================//
		if($contFilas == 1 && $itemPuc == $newNombre && $idPuc == $id && $idSucursalPuc == $camposDb['id_sucursal']){ echo '<script>Ext.getCmp("btnGuardarItemPuc").disable();</script>'; return; }

		//========================// CONDICIONAL SI NEWCODIGO ESTA ASIGNADO A OTRO ITEM PUC RETURN //========================//
		else if($contFilas > 0 && $title!='cargar' && $idPuc!=$id){
			echo'<div style="font-size:14px; font-weight:bold; width:100%; text-align:center;">
					El codigo '.$codigo.' pertenece al item PUC '.$itemPuc.'
				</div>
				<script>
					Ext.getCmp("btnGuardarItemPuc").disable();
				</script>';
			return;
		}

		//=================================// CONDICIONAL SI NEWCODIGO ESTA DISPONIBLE //===================================//
		else{
			$cont         = 0;
			$compruebaCod = 0;

			$sqlConfigPUC   = "SELECT nombre,digitos FROM puc_configuracion WHERE activo=1 AND id_empresa='$id_empresa' GROUP BY id ASC";		//CONSULTA CONFIGURACION PUC
			$queryConfigPUC = mysql_query($sqlConfigPUC,$link);
			$varAceptaAncho = 'false';

			$cuentaAnteriorColgaap = 0;
			while($row = mysql_fetch_array($queryConfigPUC)){										// CICLO QUE CREA ARRAYS NOMBRE Y DIGITOS DE CONFIGURACION PUC CON INDICE CONT
				$arrayNameConfig[$cont]    = $row['nombre'];
				$arrayDigitosConfig[$cont] = $row['digitos'];

				$codigo = substr($newCodigo, 0, $row['digitos']);									// SUSTRAE DIGITOS DE NEWCODIGO SEGUN CAMPO DIGITOS EN TABLA CONFIGURACION PUC

				if($codigo != $newCodigo){ $cuentaAnteriorColgaap = $codigo; }
				if($row['digitos'] == strlen($newCodigo)){ $varAceptaAncho = $row['digitos']; }		// COMPARA LA CANTIDAD DE DIGITOS DE NEWCODIGO CON LOS PERMITIDOS EN CONFIGURACION PUC

				if($compruebaCod != $codigo){														// CONDICIONAL QUE ALMACENA VACIO CUANDO NEWCODIGO NO ES TAN LARGO COMO LA CONFIGURACION DE DIGITOS
					$arrayDigitos[$cont] = $codigo;
					$compruebaCod        = $codigo;

					if(isset($whereCuentas)){ $whereCuentas .= " OR cuenta='$codigo'"; }			// ARMA WHERE PARA OBTENER LOS NOMBRE DE LAS CUENTAS QUE ESTAN EN LOS DIGITOS DE NEWCODIGO
					else{ $whereCuentas = "cuenta='$codigo'"; }

					$arrayCuentas[] = $codigo;
				}
				else{ $arrayDigitos[$cont] = ''; }
				$cont++;
			}
			$cont--;																				// QUTAR ELEMENTO DE MAS EN EL CONTADOR

			if($varAceptaAncho == 'false'){									// CONDICIONAL MENSAJE ERROR CUANDO LA CANTIDAD DE DIGITOS DE NEWCODIGO NO ESTA EN CONFIGURACIONES PUC RETURN
				echo'<div style="margin:10px;">
						<div style="font-size:14px; font-weight:bold; margin-bottom:20px;">
							La cantidad de digitos del codigo '.$newCodigo.' no esta definido en el modulo Configuraciones PUC
						</div>
					</div>
					<script>
						Ext.getCmp("btnGuardarItemPuc").disable();
					</script>';
				return;
			}

			switch($title){																		// GENERA TITULO
				case 'cargar':
					$titulo='Estructura Item PUC';
					break;

				case 'nuevo':
					$titulo='Nueva Estructura Item PUC';
					break;

				case 'actualizar':
					$titulo='Actualizacion Estructura Item PUC';
					break;

				default:
					break;
			}

			$temp        = 0;																															// CONTADOR SI EL CODIGO ANTERIOR EXISTE
			$sqlCuentas  = "SELECT cuenta,descripcion,id_sucursal,sucursal FROM puc	WHERE activo=1 AND ($whereCuentas) AND id_empresa='$id_empresa' ORDER BY cuenta ASC";							// CONSULTA SQL NOMBRES Y CODIGOS DE EXTRUCTURA NEWCODIGO
			$queryCuenta = mysql_query($sqlCuentas,$link);

			while($row = mysql_fetch_array($queryCuenta)){ 								// CICLO ARRAY $NOMBRECUENTAS CON INDICE DEL CODIGO DE CUENTA
				$nombreCuentas[$row['cuenta']] = $row['descripcion'];
				if($row['cuenta'] != $newCodigo){
					$arraySucursalCuentaAnterior = array('id_sucursal' =>$row['id_sucursal'], 'sucursal' =>$row['sucursal']);
				}
			}

			$contCuentasVacias = 0;
			for($i = 0; $i <= $cont; $i++){
				if(!array_key_exists($arrayCuentas[$i],$nombreCuentas)){
					if($arrayCuentas[$i] != "" || $arrayCuentas[$i] != NULL){
						$nombreCuentas[$arrayCuentas[$i]] = "Cuenta No Usada";
						$contCuentasVacias++;
					}
				}
			}

			for ($i = 0; $i <= $cont; $i++){ if($nombreCuentas[$arrayDigitos[$i]] == '' && $arrayDigitos[$i]!=''){ $temp++; } }					// CICLO PARA BUSCAR CUENTAS DE DIGITOS VACIAS

			if($temp == 0 || ($action == 'cargar' && $temp == 1) || ($action == 'comprobarEditar' && $temp == 1)){									// CONDICIONAL SI CUMPLE CON LA ESTRUCTURA
				echo '<div style="margin:10px;">
								<div style="font-size:14px; font-weight:bold; margin-bottom:20px; text-align:center;">'.$titulo.'</div>
								<div id="divMensaje" class="contenedorInfoCuentaPuc">
									<div class="contenedorHeadInfoPuc">
										<div>&nbsp;</div>
										<div style="width:19%; margin-right:2px;">ESTRUCTURA</div>
										<div style="width:59%; padding-left:1%; border-left: 1px solid #d4d4d4;">DETALLE</div>
										<div style="width:14%; padding-left:1%; margin-right:2px; border-left: 1px solid #d4d4d4;">DIGITOS</div>
									</div>';

				for ($i = 0; $i <= $cont; $i++) {
					if($nombreCuentas[$arrayDigitos[$i]] != '' && $arrayDigitos[$i]!=$newCodigo){
						echo '<div class="divInfoPuc">
										<div>&nbsp;</div>
										<div style="width:20%; margin-right:2px;">'.$arrayNameConfig[$i].'</div>
										<div style="width:60%;">'.$nombreCuentas[$arrayDigitos[$i]].'</div>
										<div style="width:15%; margin-right:2px;">'.$arrayDigitos[$i].'</div>
									</div>';
					}
					else if($nombreCuentas[$arrayDigitos[$i]] != '' && $arrayDigitos[$i]==$newCodigo &&  $action=='cargar'){
						$newNombre  = $nombreCuentas[$arrayDigitos[$i]];
						$contNombre = $i;
						break;
					}
					else{
						$contNombre = $i;
						break;
					}
				}

				echo     '<div class="divInfoPuc">
										<div>&nbsp;</div>
										<div style="width:20%; margin-right:2px;">'.$arrayNameConfig[$contNombre].'</div>
										<div style="width:60%;">'.$newNombre.'</div>
										<div style="width:15%; margin-right:2px;">'.$newCodigo.'</div>
									</div>
								</div>
							</div>
							<script>
								Ext.getCmp("btnGuardarItemPuc").enable();
							</script>';
			}
			else if($temp == 1 && $action != 'guardar' && $action != 'editar'){																	// NO EXISTE CODIGO ANTERIOR
				echo '<div style="margin-top:7px; text-align:center;">
								<div style="font-size:14px; font-weight:bold; margin-bottom:20px;">Debe crear un codigo anterior</div>
							</div>
							<script>
								Ext.getCmp("btnGuardarItemPuc").disable();
							</script>';
				return;
			}
			else if($validar == "no"){
				$contNombre = strlen($newCodigo) - 1;

				echo '<div style="margin:10px;">
								<div style="font-size:14px; font-weight:bold; margin-bottom:20px; text-align:center;">'.$titulo.'</div>
								<div id="divMensaje" class="contenedorInfoCuentaPuc">
									<div class="contenedorHeadInfoPuc">
										<div>&nbsp;</div>
										<div style="width:19%; margin-right:2px;">ESTRUCTURA</div>
										<div style="width:59%; padding-left:1%; border-left: 1px solid #d4d4d4;">DETALLE</div>
										<div style="width:14%; padding-left:1%; margin-right:2px; border-left: 1px solid #d4d4d4;">DIGITOS</div>
									</div>
									<div class="divInfoPuc">
										<div>&nbsp;</div>
										<div style="width:20%; margin-right:2px;">'.$arrayNameConfig[$contNombre].'</div>
										<div id="nombrePuc" style="width:60%;">	</div>
										<div style="width:15%; margin-right:2px;">'.$newCodigo.'</div>
									</div>
								</div>
							</div>
							<script>
								nuevo_nombre_puc();
								function nuevo_nombre_puc(){
									try{
										document.getElementById("nombrePuc").innerHTML = document.getElementById("newNombreItemPuc").value;
									} catch{
										setTimeout("nuevo_nombre_puc()",500);
									}
								}
								Ext.getCmp("btnGuardarItemPuc").enable();
							</script>';
			}

			//	VALIDACION ID SUCURSAL
			if($arraySucursalCuentaAnterior['id_sucursal'] > 0 && $idSucursalPuc != $arraySucursalCuentaAnterior['id_sucursal'] && $action != 'cargar' && $title != 'cargar'){
				echo'<script>alert("Aviso,\nLa cuenta '.$newCodigo.' debe asignarse a la sucursal '.$arraySucursalCuentaAnterior['sucursal'].'.");</script>'; exit;
			}

			//================================== ACTION GUARDAR NUEVO ======================================//
			if($action == 'guardar'){
				if($validar == "si"){
					$noSaveNiif                 = false;
					$anchoCuentaColgaap         = strlen($newCodigo);
					$anchoCuentaAnteriorColgaap = strlen($cuentaAnteriorColgaap);

					//CONSULTA RECIPROCA CUENTA ANTERION NIIF
					$sqlNiif = "SELECT COUNT(id) AS cont_colgaap,cuenta_niif
											FROM puc
											WHERE activo=1
											AND id_empresa='$id_empresa'
											AND cuenta = '$cuentaAnteriorColgaap'
											LIMIT 0,1";
					$queryNiif 					= mysql_query($sqlNiif,$link);
					$contNiif           = mysql_result($queryNiif,0,'cont_colgaap');
					$cuentaAnteriorNiif = mysql_result($queryNiif,0,'cuenta_niif');
					$newCuentaNiif      = str_replace($cuentaAnteriorColgaap, $cuentaAnteriorNiif, $newCodigo);		//NUEVA CUENTA NIIF REEMPAZANDO DIGITOS CUENTA ANTERIOR COLGAAP CON NIIF

					if($newCodigo == $newCuentaNiif && $contCuentasVacias == 1){
						if($crearNiif == 'Si'){
							if($cuentaAnteriorNiif > 0 && $anchoCuentaAnteriorColgaap <= 2){ $noSaveNiif = true; }
							else if($contNiif == 0){ echo'<script>alert("Aviso,\nLa cuenta NIIF no tiene una cuenta padre relacionada.");</script>'; exit; }
						}
						else{
							$noSaveNiif    = true;
							$newCuentaNiif = '';
						}

						//VALIDACION NUEVA CUENTA NIIF NO EXISTE
						$sqlNiif = "SELECT COUNT(id) AS cont_niif
									FROM puc_niif
									WHERE activo=1
										AND id_empresa='$id_empresa'
										AND cuenta = '$newCuentaNiif'
									LIMIT 0,1";
						$queryNiif = mysql_query($sqlNiif,$link);
						$contNiif = mysql_result($queryNiif,0,'cont_niif');

						if($contNiif > 0 && $noSaveNiif == false){															//SI LA RECIPROCA COLGAAP YA EXISTE EN NIIF
							$sqlNiif = "SELECT cuenta, LENGTH(cuenta_colgaap) AS ancho_cuenta
										FROM puc_niif
										WHERE activo=1
											AND id_empresa='$id_empresa'
											AND cuenta_colgaap LIKE '$cuentaAnteriorColgaap%'
											AND LENGTH(cuenta_colgaap) <= $anchoCuentaColgaap
										ORDER BY cuenta DESC
										LIMIT 0,1";
							$queryNiif = mysql_query($sqlNiif,$link);

							$cuentaMenorNiif      = mysql_result($queryNiif,0,'cuenta');
							$anchoCuentaMenorNiif = mysql_result($queryNiif,0,'ancho_cuenta');

							if($anchoCuentaColgaap == $anchoCuentaMenorNiif){	//ANCHO DE LA ULTIMA CUENTA NIIF = ANCHO CUENTA COLGAAP
								$newCuentaNiif = $cuentaMenorNiif+1;
								if(strlen($newCodigo) != $anchoCuentaColgaap){	//ANCHO DE LA NUEVA CUENTA NIIF != ANCHO CUENTA COLGAAP
									echo'<script>alert("Error,\nLa cuenta NIIF excedio la capacidad de los consecutivos.");</script>'; exit;
								}
							}
							else if($anchoCuentaAnteriorColgaap == $anchoCuentaMenorNiif){
								$newCuentaNiif = $cuentaMenorNiif.(substr('0000000', 0, $anchoCuentaColgaap-$anchoCuentaMenorNiif-1)).'1';
							}
						}

						if($noSaveNiif == false){
							$sqlContNiif    = "SELECT COUNT(id) AS contCuentaNiif FROM puc_niif WHERE id_empresa='$id_empresa' AND cuenta='$newCuentaNiif' AND activo=1";
							$queryContNiif  = mysql_query($sqlContNiif,$link);
							$contCuentaNiif = mysql_result($queryContNiif, 0, 'contCuentaNiif');

							//INSER NUEVA CUENTA NIIF

							if($contCuentaNiif == 0){
								$sqlNiif   = "INSERT INTO puc_niif(id_empresa,id_sucursal,cuenta,descripcion,activo) VALUES ('$id_empresa','$idSucursalPuc','$newCuentaNiif','$newNombre',1)";
								$queryNiif = mysql_query($sqlNiif,$link);
							}
						}

						//INSERT NUEVA CUENTA COLGAAP
						$sql   = "INSERT INTO puc(id_empresa,id_sucursal,cuenta,cuenta_niif,descripcion,activo) VALUES ('$id_empresa','$idSucursalPuc','$newCodigo','$newCuentaNiif','$newNombre',1)";
						$query = mysql_query($sql,$link);
					}
					else{
						echo "<script>alert('Error, No se pueden crear las cuentas porque la estructura no esta completamente configurada.');</script>";
						exit;
					}
				}
				else{
					$sqlConsultaNiif = "SELECT id FROM puc_niif WHERE cuenta = '$newCodigo' AND id_empresa = '$id_empresa' AND activo = '1'";
					$queryConsultaNiif = mysql_query($sqlConsultaNiif,$link);
					$idConsultaNiif = mysql_result($queryConsultaNiif,0,'id');

					if(($idConsultaNiif != "" || $idConsultaNiif != NULL) && $crearNiif == "Si"){
						echo "<script>alert('Error,La cuenta NIIF $newCodigo ya existe.');</script>";
						return;
					}
					else{
						if($crearNiif == "Si"){
							$sqlNiif   = "INSERT INTO puc_niif(id_empresa,id_sucursal,cuenta,descripcion,activo) VALUES ('$id_empresa','$idSucursalPuc','$newCodigo','$newNombre',1)";
							$queryNiif = mysql_query($sqlNiif,$link);
						}

						$sql   = "INSERT INTO puc(id_empresa,id_sucursal,cuenta,cuenta_niif,descripcion,activo) VALUES ('$id_empresa','$idSucursalPuc','$newCodigo','$newCodigo','$newNombre',1)";
						$query = mysql_query($sql,$link);
					}
				}

				if($query){
					$sqlLastId = "SELECT LAST_INSERT_ID()";
    			$lastId    = mysql_result(mysql_query($sqlLastId,$link),0,0);

					echo '<script>
									Win_Ventana_AgregarItemPuc.close();
				  				Inserta_Div_grillaPuc("'.$lastId.'");
								</script>';
				}
				else{
					echo '<script>
									alert("Error, no se ha establecido una conexion con el servidor.\nSi el problema persiste comuniquese con el administrador del sistema.");
								</script>';
				}
				return;
			}

			//================================= ACTION GUARDAR EDITAR =====================================//
			if($action == 'editar'){
				$contDependencias = verificarDependencia($link,$camposDb["cuenta"],$id_empresa);
				if ($contDependencias >= 1 && $camposDb["descripcion"]==$newNombre && $idSucursalPuc==$sucursalDb) {
					echo'<script>
							document.getElementById("cuerpoInsertUpdateItemPuc").innerHTML="<div style=\"font-size:14px; font-weight:bold; width:100%; text-align:center;\">Error, El codigo no se puede editar, existen '.$contDependencias.' item(s) PUC con referencia esta cuenta.</div>";
							document.getElementById("newCodigoItemPuc").value="'.$camposDb["cuenta"].'";
							document.getElementById("newCodigoItemPuc").setAttribute("readonly","readonly");
							Ext.getCmp("btnGuardarItemPuc").disable();
						</script>';
						return;
				}
				else{
					if($camposDb["cuenta"]==$newCodigo && ($camposDb["descripcion"]!=$newNombre || $idSucursalPuc!=$sucursalDb)){
						//UPDATE CUENTAS INTERNAS DIFERENTES NUEVA SUCURSAL
						$whereSucursalNiif    = '';
						$whereSucursalColgaap = '';
						$sqlSucursalCuentas   = "SELECT id,cuenta
												FROM puc
												WHERE cuenta LIKE '$newCodigo%'
													AND cuenta<>'$newCodigo'
													AND id_sucursal<>'$idSucursalPuc'
													AND id_empresa='$id_empresa'
													AND activo=1";
						$querySucursalCuentas = mysql_query($sqlSucursalCuentas,$link);
						while ($row = mysql_fetch_array($querySucursalCuentas)) {
							$whereSucursalNiif    .= "OR cuenta_colgaap='".$row['cuenta']."' ";
							$whereSucursalColgaap .= "OR id='".$row['id']."' ";

							$arrayCuentasSucursal[]=$row['id'];
						}
						//SI HAY CUENTAS HIJAS CON DIFERENTE SUCURSAL
						if($whereSucursalNiif != ''){
							$whereSucursalNiif    = substr($whereSucursalNiif,3);
							$whereSucursalColgaap = substr($whereSucursalColgaap,3);

							$sqlUpdateSucursalNiif = "UPDATE puc_niif SET id_sucursal='$idSucursalPuc' WHERE activo=1 AND id_empresa='$id_empresa' AND ($whereSucursalNiif)";
							$querySucursalNiif     = mysql_query($sqlUpdateSucursalNiif,$link);

							$sqlUpdateSucursalColgaap = "UPDATE puc SET id_sucursal='$idSucursalPuc' WHERE activo=1 AND id_empresa='$id_empresa' AND ($whereSucursalColgaap)";
							$querySucursalColgaap     = mysql_query($sqlUpdateSucursalColgaap,$link);
						}

						$sqlUpdateColgaap   = "UPDATE puc SET descripcion='$newNombre', id_sucursal='$idSucursalPuc' WHERE id='$id'";
						$queryUpdateColgaap = mysql_query($sqlUpdateColgaap,$link);

						if($queryUpdateColgaap){
							echo'<script>
									wiv_ventana_EditarItemPuc.close();
									Actualiza_Div_grillaPuc("'.$id.'");
								</script>';

								if($whereSucursalNiif != ''){
									foreach ($arrayCuentasSucursal AS $idCuentaHija) {
										echo'<script>Actualiza_Div_grillaPuc("'.$idCuentaHija.'");</script>';
									}
								}
						}
						else{
							echo'<script>
									alert("Error, no se ha establecido una conexion con el servidor.\nSi el problema persiste comuniquese con el administrador del sistema.");
								</script>';
								exit;
						}

						$sqlUpdateNiif   = "UPDATE puc_niif SET descripcion='$newNombre', id_sucursal='$idSucursalPuc' WHERE id_empresa='$id_empresa' AND cuenta_colgaap='$newCodigo' AND activo=1";
						$queryUpdateNiif = mysql_query($sqlUpdateNiif,$link);
					}
					else{
						// $sqlUpdateColgaap   = "UPDATE puc SET cuenta='$newCodigo', descripcion='$newNombre', id_sucursal='$idSucursalPuc' WHERE id='$id'";
						// $queryUpdateColgaap = mysql_query($sqlUpdateColgaap,$link);
						echo'<script>alert("Aviso,\nNo se puede modificar la cuenta puc colgaap, Elimine la cuenta y vuelva a crearla");</script>';
					}
				}
			}
		}
	}

	function eliminarItemPuc($link,$newCodigo,$id_empresa,$id){
		$sql      = "SELECT cuenta, descripcion FROM puc WHERE id='$id'";
		$query    = mysql_query($sql,$link);
		$camposDb = mysql_fetch_array($query);

		//CONT DEPENDECIAS ASOCIADAS A LA CUENTA
		$contDependencias = verificarDependencia($link,$camposDb["cuenta"],$id_empresa);

		if ($contDependencias>=1) {
			echo'<div style="margin-top:7px; text-align:center;">
					<div style="font-size:14px; font-weight:bold; margin-bottom:20px;">
						<br>Error, Elimine primero las '.$contDependencias.' cuentas asociadas a la presente cuenta PUC antes de continuar.
					</div>
				</div>
				<script>
					Ext.getCmp("btnEliminarItemPuc").disable();
				</script>';
		}
		else{
			//UPDATE ESTADO CUENTA NIIF
			$sqlNiif   = "UPDATE puc_niif SET activo=0 WHERE cuenta_colgaap='$newCodigo' AND id_empresa='$id_empresa'";
			$queryNiif = mysql_query($sqlNiif,$link);

			//UPDATE ESTADO CUENTA COLGAAP
			$cadena_update = "UPDATE puc SET activo=0 WHERE cuenta='$newCodigo' AND id_empresa='$id_empresa' AND id='$id'";
			$sql_insert    = mysql_query($cadena_update,$link);
			echo'<script>
					wiv_ventana_EditarItemPuc.close();
					Elimina_Div_grillaPuc("'.$id.'");
				</script>';
		}
	}

	//creamos una funcion para verificar las dependencias del item es decir si otros items dependen de este, y le pasamos como parametro la cuenta del item a verificar
	function verificarDependencia($link,$newCodigo,$id_empresa){
		$consulta_sql   = "SELECT count(cuenta) AS valor FROM puc WHERE cuenta LIKE '$newCodigo%'AND cuenta<>'$newCodigo' AND id_empresa='$id_empresa' AND activo=1";
		$sql_consulta   = mysql_query($consulta_sql,$link);
		$array_consulta = mysql_fetch_array($sql_consulta);
		$valor          = $array_consulta["valor"];

		return $valor;
	}

	//==========================// FUNCION PARA CARGAR LA CUENTA NIIF //==========================//
 	function configurarCuentaPuc($id,$id_empresa,$link){
 		$sql = "SELECT
 					puc.descripcion AS descripcion_colgaap,
					puc.id,
					puc.cuenta,
					puc.centro_costo,
					puc.cuenta_cruce,
					puc.tipo,
					puc_niif.cuenta AS cuenta_niif,
					puc_niif.descripcion AS descripcion_niif
				FROM puc
				LEFT JOIN puc_niif ON(
						puc_niif.activo = 1
						AND puc_niif.cuenta = puc.cuenta_niif
						AND puc_niif.id_empresa = $id_empresa
					)
				WHERE puc.id = $id
					AND puc.activo=1
					AND puc.id_empresa = $id_empresa";

 		$query = mysql_query($sql,$link);

		$tipo               = mysql_result($query,0,'tipo');
		$cCos               = mysql_result($query,0,'centro_costo');
		$cuentaCruce        = mysql_result($query,0,'cuenta_cruce');
		$cuenta             = mysql_result($query,0,'cuenta');
		$cuenta_niif        = mysql_result($query,0,'cuenta_niif');
		$descripcionNiif    = mysql_result($query,0,'descripcion_niif');
		$descripcionColgaap = mysql_result($query,0,'descripcion_colgaap');

		$script = '';
		$style  = 'display:none;';

		$script .= 'document.getElementById("tipo_cuenta_puc").value="'.$tipo.'";';
		$script .= 'document.getElementById("ccos_puc").value="'.$cCos.'";';
		$script .= 'document.getElementById("cuenta_cruce_puc").value="'.$cuentaCruce.'";';

		// CONSULTAR SI YA HAY CUENTA DE CIERRE
		$sql="SELECT COUNT(id) AS cont,cuenta FROM puc WHERE activo=1 AND tipo='cuenta_cierre' AND id_empresa=$id_empresa;";
		$query=mysql_query($sql,$link);
		$cont = mysql_result($query,0,'cont');

		if ($cont>0) {
			$option_estado='disabled';
			$option_label = '(asignada a la cuenta '.mysql_result($query,0,'cuenta').' )';
		}

		$sincDescripcion = 'false';
 		if ($cuenta_niif > 0) {
 			$style = 'display:block;';
 			if($descripcionNiif == $descripcionColgaap){ $sincDescripcion = 'true'; }
 		}

 		echo'<div style="width:100%; margin-top:10px; margin-left:10px;">
 				<div id="divLoadPucNiif" style="width:16px; height:16px; float:left; overflow:hidden; position:fixed;"></div>
 				<div style="float:left; width:100%; overflow:hidden; height:25px;">
	 				<div style="float:left; width:100px; padding:5px 0 5px 3px; font-weight:bold; font-size:11px;">Descripcion</div>
	 				<div style="float:left; width:300px; border:1px solid #D4D4D4; background-color:#FFF;">
	 					<input style="width:100%; border:none; height:89%; padding-left:5px; font-size:11px;" type="text" value="'.$descripcionColgaap.'" id="descripcion_colgaap"/>
	 				</div>
	 				<div onclick="sincDescripcion(this)" data-sinc="'.$sincDescripcion.'" id="sincDescripcion" title="Sincronizar nombre en cuenta Niif" style="width:20px; height:20px; border:1px solid #D4D4D4; margin-left:10px; float:left;">
	 					<img src="puc/img/'.$sincDescripcion.'.png" id="imgSincDescripcion" style="width:100%; height:100%;"/>
	 				</div>
 				</div>
 				<div style="float:left; width:100%; overflow:hidden; margin-top:10px; height:25px;">
	 				<div style="float:left; width:100px; padding:5px 0 5px 3px; font-weight:bold; font-size:11px;">Cuenta cruce Niif</div>
	 				<div style="float:left; width:300px; border:1px solid #D4D4D4; background-color:#FFF; height:89%;">
	 					<div style="float:left; width:20%; padding: 5px 0 5px 3px;" id="cuenta_niif">'.$cuenta_niif.'</div>
	 					<div style="float:left; width:calc(80% - 50px); padding: 5px 0 5px 3px; overflow:hidden; white-space:nowrap; text-overflow:ellipsis;" id="descripcion_cuenta_niif">'.$descripcionNiif.'</div>
	 				</div>
	 				<div style="float:left; margin-top:3px; width:20px; padding:0 0 0 5px; border-left: 1px solid #D4D4D4;" onclick="ventanaBuscarCuentaNiif(\'niif\',\''.$id.'\')">
						<img src="img/buscar20.png" style="cursor:pointer;width:16px;height:16px;" title="Cambiar Cuenta">
					</div>

					<div style="'.$style.' float:left; margin-top:3px; width:20px; padding:0 0 0 5; border-left:1px solid #D4D4D4;" id="divEliminaCuenta" onclick="eliminaCuentaNiif(\''.$id.'\')">
						<img src="img/false.png" style="cursor:pointer;width:16px;height:16px;" title="Eliminar Cuenta">
					</div>
				</div>
				<div style="float:left; width:100%; overflow:hidden; margin-top:10px; height:25px;">
	 				<div style="float:left; width:100px; padding:5px 0 5px 3px; font-weight:bold; font-size:11px;">Centro de costo</div>
	 				<div style="float:left; width:300px; border:1px solid #D4D4D4; background-color:#FFF;">
	 					<select style="width:100%; border:none; height:89%; font-size:11px;" type="text" id="ccos_puc">
	 						<option value="Si">Si</option>
	 						<option value="No">No</option>
	 					</select>
	 				</div>
 				</div>

 				<div style="float:left; width:100%; overflow:hidden; margin-top:10px; height:25px;">
	 				<div style="float:left; width:100px; padding:5px 0 5px 3px; font-weight:bold; font-size:11px;">Cuenta Cruce</div>
	 				<div style="float:left; width:300px; border:1px solid #D4D4D4; background-color:#FFF;">
	 					<select style="width:100%; border:none; height:89%; font-size:11px;" type="text" id="cuenta_cruce_puc">
	 						<option value="Si">Si</option>
	 						<option value="No">No</option>
	 					</select>
	 				</div>
 				</div>

 				<div style="float:left; width:100%; overflow:hidden; margin-top:10px; height:25px;">
	 				<div style="float:left; width:100px; padding:5px 0 5px 3px; font-weight:bold; font-size:11px;">Tipo</div>
	 				<div style="float:left; width:300px; border:1px solid #D4D4D4; background-color:#FFF;">
	 					<select style="width:100%; border:none; height:89%; font-size:11px;" type="text" id="tipo_cuenta_puc">
	 						<option value="">Seleccione...</option>
	 						<option value="Banco">Banco</option>
	 						<option value="Anticipo a proveedor">Anticipo a Proveedores</option>
	 						<option value="Anticipo de cliente">Anticipo de Clientes</option>
	 						<option value="Devoluciones de Venta">Devoluciones en Venta</option>
	 						<option value="Descuentos de Venta">Descuentos en Venta</option>
	 						<option value="cuenta_cierre" '.$option_estado.'>Cuenta de Cierre '.$option_label.'</option>
	 					</select>
	 				</div>
 				</div>
 				<script>'.$script.'</script>
			</div>';
 	}

 	//=========================== FUNCION PARA ACTUALIZAR LA CUENTA NIIF DE UNA COLGAAP ====================================//
 	function actualizarNiif($id_niif,$id_colgaap,$cuenta_niif,$cuenta_colgaap,$id_empresa,$link){

		$sql   = "UPDATE puc SET cuenta_niif='$cuenta_niif' WHERE  cuenta LIKE '$cuenta_colgaap%' AND id_empresa='$id_empresa' AND activo=1";
		$query = mysql_query($sql,$link);
 		if ($query){
 			//CONSULTAR LAS CUENTAS PARA ACTUALIZAR LA GRILLA
			$sql   = "SELECT id FROM puc WHERE cuenta LIKE '$cuenta_colgaap%' AND id_empresa='$id_empresa' AND activo=1";
			$query = mysql_query($sql,$link);

 			echo "<script>
 					Win_Ventana_buscar_cuenta_nota.close();
                  	Win_Ventana_cambiar_cuenta_niif.close();";
 			while ($row=mysql_fetch_array($query)) { echo 'Actualiza_Div_grillaPuc("'.$row['id'].'");'; }
            echo "</script>";
 		}
 		else{ echo '<script>alert("Error\nNo se logro Actualizar la cuenta, intentelo de nuevo\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; }
 	}

 	function ventanaUpdateNombreCuenta($idCuenta,$id_empresa,$link){
		$sql   = "SELECT descripcion FROM puc WHERE id='$idCuenta' AND id_empresa='$id_empresa' LIMIT 0,1";
		$query = mysql_query($sql,$link);

 		$descripcion = mysql_result($query, 0, 'descripcion');

 		echo'<div style="width:92%; height:92%; margin:5% 4%; overflow:hidden; float:left;">
 				<div id="renderUpdateNombreCuenta" style="width:16px; height:16px; overflow:hidden; position:fixed;"></div>
 				<div style="float:left; width:70px; height:16px;">Nombre</div>
 				<div style="float:left; width:190px; height:16px;">
 					<input type="text" id="new_nombre_cuenta" value="'.$descripcion.'" style="width:100%;" onkeyup="puc_colgaap_mayuscula(this);"/>
 				</div>
 			</div>';
 	}

 	function updateNombreCuenta($idCuenta,$newNombre,$id_empresa,$link){
		$sqlUpdate   = "UPDATE puc SET descripcion='$newNombre' WHERE id=$idCuenta AND id_empresa=$id_empresa";
		$queryUpdate = mysql_query($sqlUpdate,$link);

		if(!$queryUpdate){ echo "<script>alert('Aviso\nNo se actualizo el nombre de la cuenta!')</script>"; exit; }

		echo"<script>
				Win_Ventana_new_nombre_cuenta.close();
				Actualiza_Div_grillaPuc($idCuenta);
			</script>";
 	}

 	//FUNCION PARA ELIMINAR LA CUENTA NIIF CRUZADA
 	function eliminaCuentaNiif($id,$link){
		$sql   = "UPDATE puc SET cuenta_niif='' WHERE id=$id";
		$query = mysql_query($sql,$link);
 		if ($query) {
 			echo '<script>
 					document.getElementById("divEliminaCuenta").style.display="none";
 					Win_Ventana_cambiar_cuenta_niif.close();
 					Actualiza_Div_grillaPuc("'.$id.'");
 				</script>';
 		}
 		else{ echo '<script>alert("Error!\nNo se elimino la cuenta, intentelo de nuevo\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; }
 	}

 	function ventanaSeleccionarUbicacion($id,$id_empresa,$link){
 		$id_pais = $_SESSION['PAIS'];

		$sqlPuc   = "SELECT id_ciudad,id_departamento FROM puc WHERE id='$id' AND activo=1 AND id_empresa='$id_empresa' LIMIT 0,1";
		$queryPuc = mysql_query($sqlPuc,$link);

		$id_ciudad       = mysql_result($queryPuc, 0, 'id_ciudad');
		$id_departamento = mysql_result($queryPuc, 0, 'id_departamento');

 		//==================// SELECT DEPARTAMENTO //==================//
		$sqlDepartamento   = "SELECT id,departamento FROM ubicacion_departamento WHERE id_pais='$id_pais' AND activo=1";
		$queryDepartamento = mysql_query($sqlDepartamento,$link);

		$optionDepartamento = '<option value="">Seleccione...</option>';
 		while ($row = mysql_fetch_assoc($queryDepartamento)) {
 			$selected = ($row['id'] == $id_departamento)? 'selected': '';
 			$optionDepartamento .= '<option value="'.$row['id'].'" '.$selected.'>'.$row['departamento'].'</option>';
 		}

 		//====================// SELECT CIUDAD //====================//
 		$sqlCiudad   = "SELECT id,ciudad FROM ubicacion_ciudad WHERE id_pais='$id_pais' AND id_departamento='$id_departamento' AND activo=1";
		$queryCiudad = mysql_query($sqlCiudad,$link);

		$optionCiudad = '<option value="">Seleccione...</option>';
 		while ($row = mysql_fetch_assoc($queryCiudad)) {
 			$selected = ($row['id'] == $id_ciudad)? 'selected': '';
 			$optionCiudad .= '<option value="'.$row['id'].'" '.$selected.'>'.$row['ciudad'].'</option>';
 		}

 		echo'<div style="margin:5%; height:90%; width:90%;">
 				<div id="renderSaveUbicacionPuc" style="width:20px; height:20px; overflow:hidden; position:fixed;"></div>
 				<div style="width:100%; height:25px; overflow:hidden;">
 					<div style="float:left; width:100px; height:100%;">Departamento</div>
 					<div style="float:left; width:150px; height:100%;">
 						<select style="width:100%;" id="departamento_puc" class="myfield" onchange="updateComboCiudadPuc(this.value,\''.$id_ciudad.'\')">'.$optionDepartamento.'</select>
 					</div>
 				</div>
 				<div style="width:100%; overflow:hidden; margin-top:5px;">
 					<div style="float:left; width:100px; height:100%;">Ciudad</div>
 					<div style="float:left; width:150px; height:100%;" id="combo_ciudad_puc">
 						<select style="width:100%;" id="ciudad_puc" class="myfield">'.$optionCiudad.'</select>
 					</div>
 				</div>
 			</div>';
 	}

 	function updateComboCiudadPuc($idDepartamento,$idCiudad,$id_empresa,$link){
 		$id_pais = $_SESSION['PAIS'];

 		//====================// SELECT CIUDAD //====================//
 		$sqlCiudad   = "SELECT id,ciudad FROM ubicacion_ciudad WHERE id_pais='$id_pais' AND id_departamento='$idDepartamento' AND activo=1";
		$queryCiudad = mysql_query($sqlCiudad,$link);

		$optionCiudad = '<option value="">Seleccione...</option>';
 		while ($row = mysql_fetch_assoc($queryCiudad)) {
 			$selected = ($row['id'] == $idCiudad)? 'selected': '';
 			$optionCiudad .= '<option value="'.$row['id'].'">'.$row['ciudad'].'</option>';
 		}

 		echo'<select style="width:100%;" id="ciudad_puc" class="myfield">'.$optionCiudad.'</select>';
 	}

 	function guardarUbicacionPuc($id,$idDepartamento,$idCiudad,$id_empresa,$link){
 		//UPDATE COLGAAP
		$sqlUbicacion   = "UPDATE puc SET id_departamento='$idDepartamento', id_ciudad='$idCiudad' WHERE id='$id' AND id_empresa='$id_empresa' AND activo=1";
		$queryUbicacion = mysql_query($sqlUbicacion,$link);

		if(!$queryUbicacion){ echo $sqlUbicacion.'<script>alert("Aviso\nNo se actualizo el departamento y la cuidad de la cuenta,\n\nintentelo nuevamente si el problema persiste comuniquese con el administrador del sistema!")</script>'; exit; }

		//UPDATE NIIF
		$sqlCuentaNiif   = "SELECT cuenta_niif FROM puc WHERE id='$id' AND id_empresa='$id_empresa' AND activo=1";
		$queryCuentaNiif = mysql_query($sqlCuentaNiif,$link);
		$cuentaNiif      = mysql_result($queryCuentaNiif, 0, 'cuenta_niif');

		if($cuentaNiif > 0){
			$sqlUpdateNiif   = "UPDATE puc_niif SET id_departamento='$idDepartamento', id_ciudad='$idCiudad' WHERE id_empresa='$id_empresa' AND activo=1 AND cuenta='$cuentaNiif'";
			$queryUpdateNiif = mysql_query($sqlUpdateNiif,$link);
		}

		echo"<script>
				Actualiza_Div_grillaPuc($id);
				Win_Ventana_selecciona_ubicacion.close();
			</script>";
 	}

 	function guardarConfiguracionPuc($id_cuenta_colgaap,$cuentaColgaap,$cuentaNiif,$tipo,$ccosPuc,$cuentaCruce,$sincDescripcion,$descripcionColgaap,$id_empresa,$link){

 		//=====================// UPDATE CUENTAS COLGAAP //=====================//
 		//**********************************************************************//
 		$sqlColgaap = "UPDATE puc
 						SET descripcion='$descripcionColgaap',
 							cuenta_niif='$cuentaNiif',
 							centro_costo='$ccosPuc',
 							cuenta_cruce='$cuentaCruce',
 							tipo='$tipo'
						WHERE id='$id_cuenta_colgaap'
							AND id_empresa='$id_empresa'
							AND activo=1";
 		$queryColgaap = mysql_query($sqlColgaap,$link);

 		//==================// UPDATE CONFIG CUENTAS COLGAAP //==================//
 		//***********************************************************************//
 		$sqlConfig = "UPDATE puc
 						SET centro_costo='$ccosPuc',
 							cuenta_cruce='$cuentaCruce',
 							tipo='$tipo'
						WHERE cuenta LIKE '$cuentaColgaap%'
							AND id_empresa='$id_empresa'
							AND activo=1";
		$queryConfig = mysql_query($sqlConfig,$link);

		//===================// UPDATE CONFIG CUENTAS NIIF //====================//
 		//***********************************************************************//
 		if($cuentaNiif > 0){
	 		$sqlConfigNiif = "UPDATE puc_niif
	 						SET centro_costo='$ccosPuc',
	 							cuenta_cruce='$cuentaCruce',
 								tipo='$tipo',
 								descripcion=if('true'='$sincDescripcion','$descripcionColgaap',descripcion)
							WHERE cuenta LIKE '$cuentaNiif%'
								AND id_empresa='$id_empresa'
								AND activo=1";
			$queryConfigNiif = mysql_query($sqlConfigNiif,$link);
		}

		if ($queryConfig){
            $sqlUpdate = "SELECT id
            			FROM puc
 						WHERE cuenta LIKE '$cuentaColgaap%'
							AND id_empresa='$id_empresa'
							AND activo=1";
			$queryUpdate = mysql_query($sqlUpdate,$link);

			echo"<script>
					Win_Ventana_configuracion_cuenta.close();";
			while ($row = mysql_fetch_assoc($queryUpdate)) {
				echo "if(document.getElementById('item_grillaPuc_".$row['id']."')){ Actualiza_Div_grillaPuc('".$row['id']."'); }";
			}
			echo"</script>";
 		}
 		else{ echo '<script>alert("Error\nNo se logro Actualizar la cuenta, intentelo de nuevo\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; }

 	}
?>
