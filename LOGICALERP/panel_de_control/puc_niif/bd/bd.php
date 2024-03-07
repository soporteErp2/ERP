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
			validarNuevoCodigoItemPuc($link,$newCodigo,$id_empresa,$action,$newNombre,$id,$idSucursalPuc,$title);
			break;

		case 'eliminarItemPuc':
			eliminarItemPuc($link,$newCodigo,$id_empresa,$id);
			break;

		case 'editar':
			editar($link,$newCodigo,$id_empresa,$action,$newNombre,$id);
			break;

		case 'ventanaUpdateNombreCuenta':
			ventanaUpdateNombreCuenta($idCuenta,$id_empresa,$link);
			break;

		case 'updateNombreCuenta':
			updateNombreCuenta($idCuenta,$newNombre,$id_empresa,$link);
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

		case 'configurarCuentaPuc':
			configurarCuentaPuc($id,$id_empresa,$mysql);
			break;
		case 'guardarConfiguracionPuc':
			guardarConfiguracionPuc($id_cuenta,$tipo,$ccosPuc,$descripcionNiif,$id_empresa,$mysql);
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
			$sql        = "SELECT id_sucursal FROM puc_niif WHERE id_empresa='$id_empresa' AND activo=1 AND cuenta='$cuenta'";
			$idSelected = mysql_result(mysql_query($sql,$link),0,'id_sucursal');
		}


		$disable       = '';
		$disableCuenta = '';
		$loadScript    = 'document.getElementById("newCodigoItemPuc").onkeypress   = function(event){ return changeCodigoInputItemPuc(event); };
							document.getElementById("newNombreItemPuc").onkeypress = function(event){ return changeNombreInputItemPuc(event); };
							document.getElementById("newNombreItemPuc").onkeyup    = function(event){ this.value = (this.value).toUpperCase(); };
							document.getElementById("idSucursalPuc").onchange      = function(event){ cambiaSucursalCuentaPuc(this.value,"'.$idSelected.'"); };';
		//BLOQUEAR CUANDO ACTION = EDITAR Y NUMEROS DE DIGITOS
		if ($action=='editar' && strlen($cuenta)<=1) {
			$disable       = 'disabled="disabled"';
			$disableCuenta = 'disabled="disabled"';
			$loadScript    = 'Ext.getCmp("btnGuardarItemPucNiif").disable();
					    		Ext.getCmp("btnComprobarNewCodigoItemPucNiif").disable();
					    		Ext.getCmp("btnEliminarItemPucNiif").disable();
					    		setTimeout(function(){document.getElementById("cuerpoInsertUpdateItemPuc").innerHTML="<div style=\"font-size:14px; font-weight:bold; width:100%; text-align:center;\">Este item puc no se puede eliminar/editar por ser un item raiz</div>";},50);';
		}
		else if($action=='editar'){
			//CONSULTAR SI LA CUENTA ESTA RELACIONADA EN UNA COLGAAP, SI ESTA RELACINADA MOSTRAR DISABLED
			$sql="SELECT count(id) AS cont FROM puc WHERE activo=1 AND id_empresa='$id_empresa' AND cuenta='$cuenta'";
			$query=mysql_query($sql,$link);
			$cont=mysql_result($query,0,'cont');
			if ($cont>0) { $disableCuenta = 'disabled="disabled"'; }

		}

		echo'<div style="float:left; margin: 5px 0 0 10px">
			    <div style="float:left; width:50px; padding:3px 0 0 0">Codigo</div>
			    <div style="float:left; width:160px">
				    <input type="text" class="myfield" id="newCodigoItemPuc" '.$disableCuenta.' style="width:100%" value="'.$cuenta.'"/>
				</div>
			</div>
			<div style="float:left; margin: 5px 0 0 10px">
			    <div style="float:left; width:50px; padding:3px 0 0 0">Nombre</div>
			    <div id="recibidor_filtro_empresa" style="float:left; width:160px">
			    	<input type="text" class="myfield" id="newNombreItemPuc" style="width:100%" '.$disable.' value="'.$descripcion.'"/>
		    	</div>
		    </div>
		    <div style="float:left; margin: 5px 0 0 10px">
			    <div style="float:left; width:50px; padding:3px 0 0 0">Sucursal</div>
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
		    	Ext.getCmp("btnGuardarItemPucNiif").disable();
		    	console.log("in");
		    </script>';

	}

	function divCuerpoInsertUpdateItemPuc($link,$newCodigo,$id_empresa,$action,$newNombre,$id,$idSucursalPuc,$title){
		echo'<div id="cuerpoInsertUpdateItemPuc" style="margin:10px 10px 20px 10px;"></div>';
		if($action=='cargar'){ validarNuevoCodigoItemPuc($link,$newCodigo,$id_empresa,$action,$newNombre,$id,$idSucursalPuc,$title); }
	}

	function validarNuevoCodigoItemPuc($link,$newCodigo,$id_empresa,$action,$newNombre,$id,$idSucursalPuc,$title){

	 	$sql     = "SELECT cuenta, descripcion, id_sucursal,id FROM puc_niif WHERE id=$id AND activo=1 AND id_empresa='$id_empresa' LIMIT 0,1";
		$query   = mysql_query($sql,$link);
		$camposDb = mysql_fetch_array($query);

		$sqlPUC   = "SELECT COUNT(id) AS cantidad,id,cuenta,descripcion FROM puc_niif WHERE cuenta='$newCodigo' AND activo=1 AND id_empresa='$id_empresa' GROUP BY id LIMIT 0,1";
		$queryPUC = mysql_query($sqlPUC,$link);

		$contFilas = mysql_result($queryPUC,0,'cantidad');
		$idPuc     = mysql_result($queryPUC,0,'id');
		$codigo    = mysql_result($queryPUC,0,'cuenta');
		$itemPuc   = mysql_result($queryPUC,0,'descripcion');

		//===========================// CONDICIONAL SI EL CODIGO EXISTE Y NO HAY CAMBIOS RETURN //===========================//
		if($contFilas == 1 && $itemPuc == $newNombre && $idPuc==$id && $idSucursalPuc==$camposDb['id_sucursal']){ echo '<script>Ext.getCmp("btnGuardarItemPucNiif").disable();</script>'; return; }

		//========================// CONDICIONAL SI NEWCODIGO ESTA ASIGNADO A OTRO ITEM PUC RETURN //========================//
		else if($contFilas > 0 && $title!='cargar' && $idPuc!=$id){
			echo'<div style="font-size:14px; font-weight:bold; width:100%; text-align:center;">
					El codigo '.$codigo.' pertenece al item NIIF  '.$itemPuc.'
				</div>
				<script>
					Ext.getCmp("btnGuardarItemPucNiif").disable();
				</script>';
			return;
		}

		//=================================// CONDICIONAL SI NEWCODIGO ESTA DISPONIBLE //===================================//
		else{
			$cont         = 0;
			$compruebaCod = 0;

			$sqlConfigPUC   = "SELECT nombre,digitos FROM puc_configuracion_niif WHERE id_empresa='$id_empresa' AND activo=1 GROUP BY id ASC";		//CONSULTA CONFIGURACION PUC
			$queryConfigPUC = mysql_query($sqlConfigPUC,$link);
			$varAceptaAncho = 'false';

			$newCodigo=$newCodigo*1;

			while($row = mysql_fetch_array($queryConfigPUC)){										// CICLO QUE CREA ARRAYS NOMBRE Y DIGITOS DE CONFIGURACION PUC CON INDICE CONT
				$arrayNameConfig[$cont]    = $row['nombre'];
				$arrayDigitosConfig[$cont] = $row['digitos'];

				$codigo = substr($newCodigo, 0, $row['digitos']);									// SUSTRAE DIGITOS DE NEWCODIGO SEGUN CAMPO DIGITOS EN TABLA CONFIGURACION PUC
				if($row['digitos'] == strlen($newCodigo)){ $varAceptaAncho = $row['digitos']; }		// COMPARA LA CANTIDAD DE DIGITOS DE NEWCODIGO CON LOS PERMITIDOS EN CONFIGURACION PUC

				if($compruebaCod != $codigo){														// CONDICIONAL QUE ALMACENA VACIO CUANDO NEWCODIGO NO ES TAN LARGO COMO LA CONFIGURACION DE DIGITOS
					$arrayDigitos[$cont] = $codigo;
					$compruebaCod        = $codigo;

					if(isset($whereCuentas)){ $whereCuentas .= " OR cuenta='$codigo'"; }			// ARMA WHERE PARA OBTENER LOS NOMBRE DE LAS CUENTAS QUE ESTAN EN LOS DIGITOS DE NEWCODIGO
					else{ $whereCuentas = " cuenta='$codigo'"; }
				}
				else{ $arrayDigitos[$cont] = ''; }
				$cont++;
			}
			$cont--;																				// QUITAR ELEMENTO DE MAS EN EL CONTADOR

			if($varAceptaAncho == 'false'){	// CONDICIONAL MENSAJE ERROR CUANDO LA CANTIDAD DE DIGITOS DE NEWCODIGO NO ESTA EN CONFIGURACIONES PUC RETURN
				echo'<div style="margin:10px;">
						<div style="font-size:14px; font-weight:bold; margin-bottom:20px;">
							La cantidad de digitos del codigo '.$newCodigo.' no esta definido en el modulo Configuraciones PUC
						</div>
					</div>
					<script>
						Ext.getCmp("btnGuardarItemPucNiif").disable();
					</script>';
				return;
			}

			switch ($title) {																		// GENERA TITULO
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

			$temp = 0;																															// CONTADOR SI EL CODIGO ANTERIOR EXISTE

			$sqlCuentas  = "SELECT cuenta,descripcion FROM puc_niif WHERE activo=1 AND ($whereCuentas) AND id_empresa='$id_empresa' ORDER BY cuenta ASC";							// CONSULTA SQL NOMBRES Y CODIGOS DE EXTRUCTURA NEWCODIGO
			$queryCuenta = mysql_query($sqlCuentas,$link);

			while($row = mysql_fetch_array($queryCuenta)){ $nombreCuentas[$row['cuenta']] = $row['descripcion']; }								// CICLO ARRAY $NOMBRECUENTAS CON INDICE DEL CODIGO DE CUENTA
			for ($i = 0; $i <= $cont; $i++){ if($nombreCuentas[$arrayDigitos[$i]] == '' && $arrayDigitos[$i]!=''){ $temp++; } }					// CICLO PARA BUSCAR CUENTAS DE DIGITOS VACIAS

			if($temp == 0 || ($action == 'cargar' && $temp==1) || ($action == 'comprobarEditar' && $temp==1)){									// CONDICIONAL SI CUMPLE CON LA ESTRUCTURA
				echo'	<div style="margin:10px;">
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
						echo '	<div class="divInfoPuc">
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
				echo'			<div class="divInfoPuc">
									<div>&nbsp;</div>
									<div style="width:20%; margin-right:2px;">'.$arrayNameConfig[$contNombre].'</div>
									<div style="width:60%;">'.$newNombre.'</div>
									<div style="width:15%; margin-right:2px;">'.$newCodigo.'</div>
								</div>
							</div>
						</div>
						<script>
							Ext.getCmp("btnGuardarItemPucNiif").enable();
						</script>';
			}
			elseif($temp==1 && $action != 'guardar' && $action != 'editar'){																	// NO EXISTE CODIGO ANTERIOR
				echo'<div style="margin-top:7px; text-align:center;">
						<div style="font-size:14px; font-weight:bold; margin-bottom:20px;">Debe crear un codigo anterior</div>
					</div>
					<script>
						Ext.getCmp("btnGuardarItemPucNiif").disable();
					</script>';
				return;
			}

			//================================== ACTION GUARDAR NUEVO ======================================//
			if ($action=='guardar') {
				$sql   = "INSERT INTO puc_niif(id_empresa,cuenta,descripcion,activo) VALUES ('$id_empresa','$newCodigo','$newNombre',1)";
				$query = mysql_query($sql,$link);
				if($query){
					$sqlLastId = "SELECT LAST_INSERT_ID()";
    				$lastId    = mysql_result(mysql_query($sqlLastId,$link),0,0);

					echo'<script>
							Win_Ventana_AgregarItemPuc.close();
			  				Inserta_Div_grillaPucNiif("'.$lastId.'");
						 </script>';
				}
				else{ echo'<script>alert("Error, no se ha establecido una conexion con el servidor.\nSi el problema persiste comuniquese con el administrador del sistema.");</script>'; }
				return;
			}

			//================================= ACTION GUARDAR EDITAR =====================================//
			if ($action=='editar') {
				$contDependencias = verificarDependencia($link,$camposDb["cuenta"],$id_empresa);
				if ($contDependencias >= 1 && $camposDb["descripcion"]==$newNombre && $idSucursalPuc==$sucursalDb) {
					echo'<script>
							document.getElementById("cuerpoInsertUpdateItemPuc").innerHTML="<div style=\"font-size:14px; font-weight:bold; width:100%; text-align:center;\">Error, El codigo no se puede editar, existen '.$contDependencias.' items PUC con referencia esta cuenta.</div>";
							document.getElementById("newCodigoItemPuc").value="'.$camposDb["cuenta"].'";
							document.getElementById("newCodigoItemPuc").setAttribute("readonly","readonly");
							Ext.getCmp("btnGuardarItemPucNiif").disable();
						</script>';
						return;
				}
				else{

					if($camposDb["cuenta"]!=$newCodigo && ($camposDb["descripcion"]!=$newNombre || $idSucursalPuc!=$sucursalDb)){
						//UPDATE CUENTAS INTERNAS DIFERENTES NUEVA SUCURSAL
						$whereSucursalNiif    = '';
						$whereSucursalColgaap = '';
						$numeroDigitos=strlen($newCodigo)+1;
						$sqlSucursalCuentas   = "SELECT id,cuenta
												FROM puc_niif
												WHERE cuenta LIKE '".$camposDb["cuenta"]."%'
													AND cuenta<>'".$camposDb["cuenta"]."'
													AND id_empresa='$id_empresa'
													AND activo=1";
						$querySucursalCuentas = mysql_query($sqlSucursalCuentas,$link);
						while ($row = mysql_fetch_array($querySucursalCuentas)) {
							$whereSucursalNiif .= "OR id='".$row['id']."' ";

							$arrayCuentasSucursal[]=$row['id'];
						}
						//SI HAY CUENTAS HIJAS CON DIFERENTE SUCURSAL
						if($whereSucursalNiif != ''){
							$whereSucursalNiif    = substr($whereSucursalNiif,3);
							// $whereSucursalColgaap = substr($whereSucursalColgaap,3);

							$sqlUpdateSucursalNiif = "UPDATE puc_niif SET id_sucursal='$idSucursalPuc',cuenta=CONCAT('$newCodigo',SUBSTRING(cuenta,$numeroDigitos,20)) WHERE id_empresa='$id_empresa' AND activo=1 AND ($whereSucursalNiif)";
							$querySucursalNiif     = mysql_query($sqlUpdateSucursalNiif,$link);
						}

						$sqlUpdateNiif   = "UPDATE puc_niif SET descripcion='$newNombre',cuenta='$newCodigo',id_sucursal='$idSucursalPuc' WHERE id='$id'";
						$queryUpdateNiif = mysql_query($sqlUpdateNiif,$link);

						if($queryUpdateNiif){
							echo'<script>
									wiv_ventana_EditarItemPucNiif.close();
									Actualiza_Div_grillaPucNiif("'.$id.'");
								</script>';

								if($whereSucursalNiif != ''){
									foreach ($arrayCuentasSucursal AS $idCuentaHija) {
										echo'<script>Actualiza_Div_grillaPucNiif("'.$idCuentaHija.'");</script>';
									}
								}
						}
						else{
							echo'<script>
									alert("Error, no se ha establecido una conexion con el servidor.\nSi el problema persiste comuniquese con el administrador del sistema.");
								</script>';
								exit;
						}

					}
					else{
						// $sqlUpdateColgaap   = "UPDATE puc SET cuenta='$newCodigo', descripcion='$newNombre', id_sucursal='$idSucursalPuc' WHERE id='$id'";
						// $queryUpdateColgaap = mysql_query($sqlUpdateColgaap,$link);
						echo'<script>alert("Aviso,\nNo se puede modificar la cuenta niif, Elimine la cuenta y vuelva a crearla '.$camposDb["cuenta"].'-'.$newCodigo.'");</script>';
					}
				}
			}
		}
	}

	function eliminarItemPuc($link,$newCodigo,$id_empresa,$id){
		$sql     = "SELECT cuenta, descripcion FROM puc_niif WHERE id='$id'";
		$query   = mysql_query($sql,$link);
		$camposDb = mysql_fetch_array($query);

		//CONT DEPENDECIAS ASOCIADAS A LA CUENTA
		$contDependencias = verificarDependencia($link,$camposDb["cuenta"],$id_empresa);

		if ($contDependencias>=1) {
			echo'<div style="margin-top:7px; text-align:center;">
					<div style="font-size:14px; font-weight:bold; margin-bottom:20px;">
						<br>Error, Elimine primero las '.$contDependencias.' cuentas asociadas al presente item PUC antes de continuar.
					</div>
				</div>';
		}
		else{
			$cadena_update = "UPDATE puc_niif SET activo=0 WHERE cuenta='$newCodigo' AND id_empresa='$id_empresa' AND id='$id'";
			$sql_insert    = mysql_query($cadena_update,$link);
			echo'<script>
					wiv_ventana_EditarItemPucNiif.close();
					Elimina_Div_grillaPucNiif("'.$id.'");
				</script>';
		}
	}

	//creamos una funcion para verificar las dependencias del item es decir si otros items dependen de este, y le pasamos como parametro la cuenta del item a verificar
	function verificarDependencia($link,$newCodigo,$id_empresa){
		$consulta_sql   = "SELECT count(cuenta) AS valor FROM puc_niif WHERE cuenta LIKE '$newCodigo%'AND cuenta<>'$newCodigo' AND id_empresa='$id_empresa' AND activo=1";
		$sql_consulta   = mysql_query($consulta_sql,$link);
		$array_consulta = mysql_fetch_array($sql_consulta);
		$valor          = $array_consulta["valor"];

		return $valor;
	}

	function ventanaUpdateNombreCuenta($idCuenta,$id_empresa,$link){
		$sql   = "SELECT descripcion FROM puc_niif WHERE id='$idCuenta' AND id_empresa='$id_empresa' LIMIT 0,1";
		$query = mysql_query($sql,$link);

 		$descripcion = mysql_result($query, 0, 'descripcion');

 		echo'<div style="width:92%; height:92%; margin:5% 4%; overflow:hidden; float:left;">
 				<div id="renderUpdateNombreCuenta" style="width:16px; height:16px; overflow:hidden; position:fixed;"></div>
 				<div style="float:left; width:70px; height:16px;">Nombre</div>
 				<div style="float:left; width:190px; height:16px;">
 					<input type="text" id="new_nombre_cuenta" value="'.$descripcion.'" style="width:100%;" onkeyup="puc_Niif_mayuscula(this);"/>
 				</div>
 			</div>';
 	}

 	function updateNombreCuenta($idCuenta,$newNombre,$id_empresa,$link){
		$sqlUpdate   = "UPDATE puc_niif SET descripcion='$newNombre' WHERE id=$idCuenta AND id_empresa=$id_empresa";
		$queryUpdate = mysql_query($sqlUpdate,$link);

		if(!$queryUpdate){ echo "<script>alert('Aviso\nNo se actualizo el nombre de la cuenta!')</script>"; exit; }

		echo"<script>
				Win_Ventana_new_nombre_cuenta.close();
				Actualiza_Div_grillaPucNiif($idCuenta);
			</script>";
 	}

 	function ventanaSeleccionarUbicacion($id,$id_empresa,$link){
 		$id_pais = $_SESSION['PAIS'];

		$sqlPuc   = "SELECT id_ciudad,id_departamento FROM puc_niif WHERE id='$id' AND activo=1 AND id_empresa='$id_empresa' LIMIT 0,1";
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
		$sqlUbicacion   = "UPDATE puc_niif SET id_departamento='$idDepartamento', id_ciudad='$idCiudad' WHERE id='$id' AND id_empresa='$id_empresa' AND activo=1";
		$queryUbicacion = mysql_query($sqlUbicacion,$link);

		if(!$queryUbicacion){ echo $sqlUbicacion.'<script>alert("Aviso\nNo se actualizo el departamento y la cuidad de la cuenta,\n\nintentelo nuevamente si el problema persiste comuniquese con el administrador del sistema!")</script>'; exit; }

		echo"<script>
				Actualiza_Div_grillaPucNiif($id);
				Win_Ventana_selecciona_ubicacion.close();
			</script>";
 	}

 	//==========================// FUNCION PARA CARGAR LA CUENTA NIIF //==========================//
 	function configurarCuentaPuc($id,$id_empresa,$mysql){
 		$sql="SELECT
 					descripcion,
					cuenta,
					centro_costo,
					cuenta_cruce,
					tipo
				FROM puc_niif
				WHERE activo=1
					AND id_empresa=$id_empresa
					AND id=$id";
 		$query=$mysql->query($sql,$mysql->link);

		$descripcion  = $mysql->result($query,0,'descripcion');
		$cuenta       = $mysql->result($query,0,'cuenta');
		$centro_costo = $mysql->result($query,0,'centro_costo');
		$cuenta_cruce = $mysql->result($query,0,'cuenta_cruce');
		$tipo         = $mysql->result($query,0,'tipo');

		$script  = "document.getElementById('tipo_cuenta_puc').value  = '$tipo';
					document.getElementById('centro_costo').value     = '$centro_costo';";

		// CONSULTAR SI YA HAY CUENTA DE CIERRE
		$sql="SELECT COUNT(id) AS cont,cuenta FROM puc_niif WHERE activo=1 AND tipo='cuenta_cierre' AND id_empresa=$id_empresa;";
		$query=$mysql->query($sql,$mysql->link);
		$cont = $mysql->result($query,0,'cont');

		if ($cont>0) {
			$option_estado='disabled';
			$option_label = '(asignada a la cuenta '.mysql_result($query,0,'cuenta').' )';
		}

		$sincDescripcion = 'false';
 		if ($cuenta_niif > 0) {
 			$style = 'display:block;';
 			if($descripcionNiif == $descripcionColgaap){ $sincDescripcion = 'true'; }
 		}

 		echo"<div class='content'>
 				<div id='divLoadPucNiif' style='width:16px; height:16px; float:left; overflow:hidden; position:fixed;'></div>
 				<table class='table-form' style='width:90%;' >
					<tr class='thead' style='background-color: #a2a2a2;'>
						<td colspan='2'>CONFIGURACION GENERAL</td>
					</tr>
					<tr>
						<td>Descripcion</td>
						<td colspan='2'><input type='text' value='$descripcion' style='width:190px;' id='descripcion'</td>
					</tr>
					<tr>
						<td>Centro de costo</td>
						<td colspan='2'>
							<select style='width:190px;' data-requiere='true' id='centro_costo' >
								<option value='No'>No</option>
								<option value='Si'>Si</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>Tipo</td>
						<td>
							<select style='width:190px;' id='tipo_cuenta_puc'>
		 						<option value=''>Seleccione...</option>
		 						<option value='Banco'>Banco</option>
		 						<option value='Anticipo a proveedor'>Anticipo a Proveedores</option>
		 						<option value='Anticipo de cliente'>Anticipo de Clientes</option>
		 						<option value='Devoluciones de Venta'>Devoluciones en Venta</option>
		 						<option value='Descuentos de Venta'>Descuentos en Venta</option>
		 						<option value='cuenta_cierre' $option_estado>Cuenta de Cierre $option_label</option>
		 					</select>
						</td>
					</tr>
				</table>
			</div>
			<script>$script</script>
			";
 	}


 	function guardarConfiguracionPuc($id_cuenta,$tipo,$ccosPuc,$descripcionNiif,$id_empresa,$mysql){
 		$sql="UPDATE puc_niif
				SET descripcion = '$descripcionNiif',
					centro_costo='$ccosPuc',
					tipo='$tipo'
				WHERE    id_empresa = '$id_empresa'
					AND activo     = 1
					AND id         = $id_cuenta";
 		$query=$mysql->query($sql,$mysql->link);
 		if ($query) {
 			echo"<script>
					Win_Ventana_configuracion_cuenta.close();
					Actualiza_Div_grillaPucNiif('$id_cuenta');
					MyLoading2('off');
				</script>";
 		}
 		else{
 			echo '<script>alert("Error\nNo se logro Actualizar la cuenta, intentelo de nuevo\nSi el problema persiste comuniquese con el administrador del sistema");MyLoading2("off");</script>';
 		}

 	}

?>
