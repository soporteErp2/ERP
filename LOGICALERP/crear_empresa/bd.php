
<?php

	include_once("../../configuracion/define_variables.php");
	header('Content-Type: text/html; charset=UTF-8');

	// CANCELAR ARCHIVO DE SUBIDA DE PUC
	if($opc=='cancelUploadFile') {
		unlink($_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/documentos_crear_empresa/'.$nameFileUpload);
		echo 'true';
		exit;
	}

	// CONSULTAR PAIS
	else if ($opc=='consultarPais') {

		if($_SERVER['SERVER_NAME'] == 'logicalerp.localhost'){
	        $host     = '192.168.8.202';
	        $usuario  = 'root';
	        $password = 'serverchkdsk';
	        $nameDb   = 'logicalsofterp';
	    }

	    else if($_SERVER['SERVER_NAME'] == 'www.logicalsoft-erp.com' || $_SERVER['SERVER_NAME'] == 'logicalsoft-erp.com'){
	        $host     = 'localhost';
	        $usuario  = 'root';
	        $password = 'serverchkdsk';
	        $nameDb   = 'logicalsofterp';
	    }
	    else if($_SERVER['SERVER_NAME'] == 'erp.plataforma.co'){
	        $host     = 'localhost';
	        $usuario  = 'root';
	        $password = 'serverchkdsk';
	        $nameDb   = 'erp';
	    }
		$link = mysql_connect($host,$usuario,$password);
	    mysql_select_db($nameDb,$link);

		$sql_dep   = "SELECT id,departamento FROM $nameDb.ubicacion_departamento WHERE id_pais=$id_pais";
		$query_dep = mysql_query($sql_dep,$link);

		while ($row2=mysql_fetch_array($query_dep)) { echo '<option value="'.$row2['id'].'">'.$row2['departamento'].'</option>'; }
	}

	// CONSULTAR CIUDAD
	else if ($opc=='consultarCiudad') {

		if($_SERVER['SERVER_NAME'] == 'logicalerp.localhost'){
	        $host     = '192.168.8.202';
	        $usuario  = 'root';
	        $password = 'serverchkdsk';
	        $nameDb   = 'logicalsofterp';
	    }

	    else if($_SERVER['SERVER_NAME'] == 'www.logicalsoft-erp.com' || $_SERVER['SERVER_NAME'] == 'logicalsoft-erp.com'){
	        $host     = 'localhost';
	        $usuario  = 'root';
	        $password = 'serverchkdsk';
	        $nameDb   = 'logicalsofterp';
	    }
	    else if($_SERVER['SERVER_NAME'] == 'erp.plataforma.co'){
	        $host     = 'localhost';
	        $usuario  = 'root';
	        $password = 'serverchkdsk';
	        $nameDb   = 'erp';
	    }
		$link = mysql_connect($host,$usuario,$password);
	    mysql_select_db($nameDb,$link);

		$sql   = "SELECT id,ciudad FROM $nameDb.ubicacion_ciudad WHERE id_departamento=$id_departamento";
		$query = mysql_query($sql,$link);

		while ($row=mysql_fetch_array($query)) { echo '<option value="'.$row['id'].'">'.$row['ciudad'].'</option>'; }
	}

	// CREAR EMPRESA
	else if($opc=='crearEmpresa'){

		include_once('configuraciones/configuracion_col/array_cuentas_default.php');
		error_reporting ( E_ALL & ~E_NOTICE & ~E_DEPRECATED);

		$nombre             = htmlentities($nombre, ENT_QUOTES);
		$numero_documento   = htmlentities($numero_documento, ENT_QUOTES);
		$tipo_documento     = htmlentities($tipo_documento, ENT_QUOTES);
		$razon_social       = htmlentities($razon_social, ENT_QUOTES);
		$direccion          = htmlentities($direccion, ENT_QUOTES);
		$telefono           = htmlentities($telefono, ENT_QUOTES);
		$celular            = htmlentities($celular, ENT_QUOTES);
		$sucursal           = htmlentities($sucursal, ENT_QUOTES);
		$bodega             = htmlentities($bodega, ENT_QUOTES);
		$nit                = htmlentities($numero_identificacion, ENT_QUOTES);
		$nombre1            = htmlentities($nombre1, ENT_QUOTES);
		$nombre2            = htmlentities($nombre2, ENT_QUOTES);
		$apellido1          = htmlentities($apellido1, ENT_QUOTES);
		$apellido2          = htmlentities($apellido2, ENT_QUOTES);
		$grupoEmpresarial   = htmlentities($grupoEmpresarial, ENT_QUOTES);
		$idGrupoEmpresarial = 0;
		$digito_verificacion = ($digito_verificacion>0)? $digito_verificacion : 'NULL';

		// SI NO ES PLATAFORMA CREAR LA BASE DE DATOS
		if($_SERVER['SERVER_NAME'] != 'erp.plataforma.co' && $_SERVER['SERVER_NAME'] != 'logicalerp.localhost'){

		    // if($_SERVER['SERVER_NAME'] == 'logicalerp.localhost'){
		    //     $host     = '192.168.8.202';
		    //     $usuario  = 'root';
		    //     $password = 'serverchkdsk';
		    //     $nameDb   = 'erp_bd';
		    // }

		    // }else if($_SERVER['SERVER_NAME'] == 'logicalsoft-erp.com' || $_SERVER['SERVER_NAME'] == 'www.logicalsoft-erp.com'){
		    //     $host     = 'localhost';
		    //     $usuario  = 'root';
		    //     $password = 'serverchkdsk';
		    //     $nameDb   = 'erp_acceso';
		    // }

		    $host     = 'localhost';
	        $usuario  = 'root';
	        $password = 'serverchkdsk';
	        $nameDb   = 'erp_acceso';

		    // CONEXION AL SERVIDOR
			$acceso_server = mysql_connect($host,$usuario,$password);

			// CONEXION A LA BASE DE DATOS NUEVA
			$link = mysql_connect($host,$usuario,$password);

			// CONEXION A LA BASE DE DATOS PRINCIPAL, QUE GUARDA LAS EMPRESAS Y EL NOMBRE DE LAS BASES DE DATOS
			$acceso = mysql_connect($host,$usuario,$password);

		    if(!$acceso){ echo "No se establecio la conexion mysql acceso!"; exit; }
		    mysql_select_db($nameDb,$acceso);
		    if(!mysql_select_db($nameDb,$acceso)){ echo "No se puede conectar a la base de datos acceso!"; exit; }

		    // VALIDAR SI YA ESTA CREADA LA EMPRESA
			$sql   = "SELECT COUNT(id) AS id FROM host WHERE activo=1 AND nit='$numero_documento' LIMIT 0,1";
			$query = mysql_query($sql,$acceso);
			if (@mysql_result($query,0,'id')>0) { echo 'LA EMPRESA YA EXISTE!'; exit; }


			// SI NO SE HA CREADO LA EMPRESA, INSERTAR EL REGISTRO Y CREAR LA BD

			// CONSULTAR EL ULTIMO ID MAYOR PARA EL NUEVO ID DE LA EMPRESA DE LA TABLA PRINCIPAL
			$sql     = "SELECT id FROM host WHERE activo=1 ORDER BY id DESC LIMIT 0,1";
			$query   = mysql_query($sql,$acceso);
			$id_host = mysql_result($query,0,'id');

			$id_host++;

			// echo "DEBE SELECCIONAR EL CONSECUTIVO DE SOPORTE PARA CREAR LA EMPRESA"; exit;

			$id_host=1329;

			// exit; 
			$bd = 'erp_'.$id_host;
			// exit;
			//====================================// CREAR LA EMPRESA EN LA BASE DE DATOS PRINCIPAL //====================================//
		    if(!isset($id_plan)){ $id_plan = 1; }
			$sql   = "INSERT INTO host (id,nit,nombre,servidor,bd,id_plan,fecha_creacion,hora_creacion,fecha_vencimiento_plan,activo)
						VALUES ($id_host,'$numero_documento','$nombre','$host','$bd','$id_plan',NOW(),NOW(),DATE_ADD(NOW(), INTERVAL 1 MONTH),1)";
			$query = mysql_query($sql,$acceso);
			if (!$query) { echo 'NO SE INSERTO LA NUEVA EMPRESA '.$sql; exit; }

			//==============================================// CREAR LA BASE DE DATOS //==============================================//
			$sql   = "CREATE DATABASE $bd DEFAULT CHARACTER SET latin1 DEFAULT COLLATE latin1_general_ci;";
			$query = mysql_query($sql,$acceso_server);
			if (!$query) {
				echo 'NO SE CREO LA BASE DE DATOS DE LA EMPRESA';
				$sql   = "DELETE FROM host WHERE activo=1 AND id=$id_host";
				$query = mysql_query($sql,$acceso);
				exit;
			}

			// CONEXION PARA LA BASE DE DATOS CREADA
			mysql_select_db($bd,$link);
			if(!mysql_select_db($bd,$link)){ echo "NO SE PUEDE CONECTAR A LA BASE DE DATOS NUEVA!"; }
			@mysql_query("SET NAMES 'utf8'",$link);

			$bd_copiar='logicalsofterp';

			// CONSULTAR LAS TABLAS DE LA BASE DE DATOS A COPIAR
			$sql   = "SHOW TABLES FROM $bd_copiar";
			$query = mysql_query($sql,$acceso_server) or die("NO SE CONSULTARON LAS TABLAS DE LA BASE DE DATOS A COPIAR: ".mysql_error());
		    while ($filas = mysql_fetch_array($query, MYSQL_NUM)) {  $tablas[] = $filas[0]; }

		    $create_table_query = '';
		    // RECORRER LAS TABLAS E INSERTARLAS EN LA BASE DE DATOS CREADA
		    foreach ($tablas as $tabla) {
		    	//RECORRER LAS TABLAS Y MOSTRAR EL SQL DE CREACION
				$sql   = "SHOW CREATE TABLE $bd_copiar.$tabla;";
				$query = mysql_query($sql,$acceso_server);
		    	while (@$fila = mysql_fetch_array($query, MYSQL_NUM)) {
		        	// CON EL SQL DE CREACION, COPIAR LAS TABLAS A LA NUEVA BASE DE DATOS
					$create_table_query = "".$fila[1].";";
					$create_table_query = str_replace($bd_copiar,$bd, $create_table_query);
					$query_create_table = mysql_query($create_table_query,$link);
				    if (!$query_create_table) {
				    	deshacer_registro($id_host,$bd,'NO SE CREARON LAS TABLAS DE LA NUEVA BASE DE DATOS<br/>',$acceso_server);
				    }
				}

				// TRUNCAR LAS TABLAS DE LA NUEVA BASE DE DATOS
				$sql   = "TRUNCATE TABLE $tabla;";
				$query = mysql_query($sql,$link);
		    }

		    // CONSULTAR LOS TRIGGERS DE LA BASE DE DATOS   COPIAR
			$sql   = "SHOW TRIGGERS FROM $bd_copiar";
			$query = mysql_query($sql,$acceso_server);
			// RECORRER LOS TRIGGERS
		    while (@$row = mysql_fetch_array($query, MYSQL_NUM)) {
		    	// CONSULTAR EL SQL DE CREACION DEL TRIGGER
				$sql_trigger   = "SHOW CREATE TRIGGER $bd_copiar.".$row[0];
				$query_trigger = mysql_query($sql_trigger,$acceso);

		    	// INSERTAR EL TRIGGER EN LA NUEVA BD
				$sql_create_trigger   = mysql_result($query_trigger,0,2);
				$query_create_trigger = mysql_query($sql_create_trigger,$link);

		    	if (!$query_create_trigger) {
		    		deshacer_registro($id_host,$bd,'NO SE CREO EL TRIGGER DE LA BD<br/>',$acceso_server);
		    	}
		    }

		    // INSERTAR LOS REGISTROS DE INSTALACION DE LA APLICACION
		    include_once('installacionERP.php');
		}
		// SI ES PLATAFORMA CREAR LA CONEXION A LA BASE DE DATOS
		else if($_SERVER['SERVER_NAME'] == 'erp.plataforma.co'){

	        $host     = 'localhost';
	        $usuario  = 'root';
	        $password = 'serverchkdsk';
	        $nameDb   = $origen_empresa=='empresa_nacional'? 'erp': 'erp_exterior';
	        $link     = mysql_connect($host,$usuario,$password);
			mysql_select_db($nameDb,$link);
	    }
	    else if ($_SERVER['SERVER_NAME'] == 'logicalerp.localhost'){

	        $host     = '192.168.8.202';
	        $usuario  = 'root';
	        $password = 'serverchkdsk';
	        $nameDb   = $origen_empresa=='empresa_nacional'? 'logicalsofterp': 'erp_empresas_exterior';
	        $link     = mysql_connect($host,$usuario,$password);
			mysql_select_db($nameDb,$link);
	    }

		//VALIDAR QUE NO EXISTA OTRA EMPRESA CON ESE NUMERO DE DOCUMENTO
		$sqlVerificaEmpresa   = "SELECT COUNT(id) AS cont,id_pais FROM empresas WHERE documento='$numero_documento' AND tipo_documento_nombre='$tipo_documento' LIMIT 0,1";
		$queryVerificaEmpresa = mysql_query($sqlVerificaEmpresa,$link);

		$contEmpresa = mysql_result($queryVerificaEmpresa,0,'cont');

		if ($contEmpresa > 0 && $opc == 'cancelNewEmpresa') { exit; }
		else if ($contEmpresa > 0) { echo "YA EXISTE UNA EMPRESA REGISTRADA ".$tipo_documento." ".$numero_documento."<br/>"; exit; }
		else if(!$queryVerificaEmpresa){ echo "NO SE COMPROBO LA EXISTENCIA DE LA EMPRESA<br/>"; exit; }

		$opc = 'cancelNewEmpresa';

		//VALIDACION GRUPO EMPRESARIAL
		if($grupoEmpresarial != ''){
			$sqlGrupoEmpresa    = "SELECT COUNT(id) AS contGrupoEmpresa, id FROM grupos_empresariales WHERE nombre='$grupoEmpresarial' AND activo=1 LIMIT 0,1";
			$queryGrupoEmpresa  = mysql_query($sqlGrupoEmpresa,$link);
			$idGrupoEmpresarial = mysql_result($queryGrupoEmpresa,0,'id');
			$contGrupoEmpresa   = mysql_result($queryGrupoEmpresa,0,'contGrupoEmpresa');

			if ($contGrupoEmpresa == 0) { echo "GRUPO EMPRESARIAL NO DEFINIDO ".$grupoEmpresarial."<br/>"; exit; }
			else if(!$queryGrupoEmpresa){ echo "NO SE VALIDO EL GRUPO EMPRESARIAL<br/>"; exit; }
		}

		if ($pais=='49') {
			$pais=49;
			$pais_name='Colombia';
		}
		else{
			$sql_pais = "SELECT id,pais FROM ubicacion_pais WHERE id='$pais'";
			$pais = mysql_result(mysql_query($sql_pais,$link),0,'id');
			$pais_name = mysql_result(mysql_query($sql_pais,$link),0,'pais');
		}

		// $sql ="insert into debug (prueba) values ('INSERT INTO empresas (nombre,tipo_documento_nombre,id_pais,pais,id_departamento,id_ciudad, razon_social,tipo_regimen,actividad_economica,direccion,documento,digito_verificacion,telefono,celular,zona_horaria,id_moneda,formato_hora,interface,grupo_empresarial)
		// 	 	VALUES ('$nombre','$tipo_documento',".$pais.",'$pais_name',$departamento,$ciudad,'$razon_social','$tipo_regimen','$actividad_economica','$direccion','$numero_documento',$digito_verificacion,'$telefono','$celular','America/Bogota','1','AM/PM','false','$idGrupoEmpresarial')')";

		 $sql = "INSERT INTO empresas (nombre,tipo_documento_nombre,id_pais,pais,id_departamento,id_ciudad, razon_social,tipo_regimen,actividad_economica,direccion,documento,digito_verificacion,telefono,celular,zona_horaria,id_moneda,formato_hora,interface,grupo_empresarial)
			 	VALUES ('$nombre','$tipo_documento',".$pais.",'$pais_name',$departamento,$ciudad,'$razon_social','$tipo_regimen','$actividad_economica','$direccion','$numero_documento',$digito_verificacion,'$telefono','$celular','America/Bogota','1','AM/PM','false','$idGrupoEmpresarial')";
		 $query = mysql_query($sql,$link);

		if (!$query) { if (isset($id_host)) { deshacer_registro($id_host,$bd,'NO SE CREO LA EMPRESA <br/>',$acceso_server); }else{deleteInfoEmpresa($link,$id_empresa,"NO SE CREO LA EMPRESA <br/>",1);} }
		else{
			$sqlLastId  = "SELECT LAST_INSERT_ID()";
			$id_empresa = mysql_result(mysql_query($sqlLastId,$link),0,0);

			//INSERTAR LA SUCURSAL
			$sqlSucursal   = "INSERT INTO empresas_sucursales (id_empresa,nombre,id_departamento,id_ciudad) VALUES ($id_empresa,'$sucursal','$departamento','$ciudad')";
			$querySucursal = mysql_query($sqlSucursal,$link);
			if (!$querySucursal) { if (isset($id_host)) { deshacer_registro($id_host,$bd,'NO SE INSERTO LA SUCURSAL <br/>',$acceso_server); }else{deleteInfoEmpresa($link,$id_empresa,"NO SE INSERTO LA SUCURSAL <br/>",1);} }

			$sql_sucursal = "SELECT LAST_INSERT_ID()";
			$id_sucursal  = mysql_result(mysql_query($sql_sucursal,$link),0,0);


			//===================================== INSERT CUENTAS ====================================//
			if($nameFileUpload == ''){														//PUC POR DEFECTO
				// if($_SERVER['SERVER_NAME'] == 'logicalerp.localhost' || $_SERVER['SERVER_NAME'] == 'erp.plataforma.co'){
					include_once ('configuraciones/configuracion_col/puc_colgaap_plataforma.php');
					include_once ('configuraciones/configuracion_col/puc_niif_plataforma.php');
				// }
				// else{
				// 	include_once ('configuraciones/configuracion_col/puc_colgaap.php');
				// 	include_once ('configuraciones/configuracion_col/puc_niif.php');
				// }

				$queryPucColgaap = mysql_query($sqlPucColgaap,$link);							//CUENTAS PUC COLGAAP
				if (!$queryPucColgaap) { if (isset($id_host)) { deshacer_registro($id_host,$bd,'NO SE INSERTO EL PUC COLGAAP <br/>',$acceso_server); }else{deleteInfoEmpresa($link,$id_empresa,"NO SE INSERTO EL PUC COLGAAP <br/>",1);} }

				$queryPucNiif = mysql_query($sqlPucNiif,$link); 								//CUENTAS PUC NIIF
				if (!$queryPucNiif) { if (isset($id_host)) { deshacer_registro($id_host,$bd,'NO SE INSERTO EL PUC NIIF <br/>',$acceso_server); }else{deleteInfoEmpresa($link,$id_empresa,"NO SE INSERTO EL PUC NIIF <br/>",1);} }
			}
			else{ include_once ('configuraciones/load_excel.php'); } 						//SI SE CARGA UN ARCHIVO EXCEL

			//================================== INSERT ASIENTOS DEFAULT ==================================//
			$whereCuentas         = '';
			$valueAsientosDefault = '';

			//FOREACH OPCION COMPRA-VENTA
			foreach ($arrayCuentasDefault as $tipoCuenta => $arrayCuentaDefault) {

				//FOREACH CONFIGURACION CUENTAS POR DEFAULT
				foreach ($arrayCuentaDefault as $cuentaColgaap => $arrayCuenta){
					$whereCuentas         .= ' OR cuenta = '.$cuentaColgaap;
					$valueAsientosDefault .= "('".$arrayCuenta['detalle']."','".$arrayCuenta['estado']."','".$cuentaColgaap."',$id_empresa),";
				}
			}

			$whereCuentas = substr($whereCuentas, 4);
			$whereCuentas = 'AND ('.$whereCuentas.')';

			//==================== ASIENTOS COLGAAP DEFAULT ======================//
			$valueAsientosDefault = substr($valueAsientosDefault, 0, -1);
			$sqlAsientosDefault   = "INSERT INTO asientos_colgaap_default (descripcion,estado,cuenta,id_empresa) VALUES $valueAsientosDefault";
			$queryAsientosDefault = mysql_query($sqlAsientosDefault,$link);

			if (!$queryAsientosDefault) { if (isset($id_host)) { deshacer_registro($id_host,$bd,'NO SE INSERTO LA CONFIGURACION DE LOS ASIENTOS POR DEFECTO <br/>',$acceso_server); }else{deleteInfoEmpresa($link,$id_empresa,"NO SE INSERTO LA CONFIGURACION DE LOS ASIENTOS POR DEFECTO <br/>",1);} }

			//===================== ASIENTOS NIIF DEFAULT ========================//
			$valueInsert = '';
			$sqlCuentasDefaultNiif   = "SELECT cuenta_niif, cuenta AS cuenta_colgaap
										FROM puc
										WHERE activo=1 AND id_empresa='$id_empresa' $whereCuentas";
			$queryCuentasDefaultNiif = mysql_query($sqlCuentasDefaultNiif,$link);

			while ($rowCuentasDefault = mysql_fetch_array($queryCuentasDefaultNiif)) {
				$cuentaColgaap = $rowCuentasDefault['cuenta_colgaap'];
				$arrayCuentaNiif[$cuentaColgaap] = $rowCuentasDefault['cuenta_niif'];
			}

			//FOREACH OPCION COMPRA-VENTA
			foreach ($arrayCuentasDefault as $tipoCuenta => $arrayCuentaDefault) {

				//FOREACH CONFIGURACION CUENTAS POR DEFAULT
				foreach ($arrayCuentaDefault as $cuentaColgaap => $arrayCuenta) {
					if($arrayCuenta['niif'] != true) continue;
					$valueInsert .= "('".$arrayCuenta['detalle']."', '".$arrayCuenta['estado']."', '".$arrayCuentaNiif[$cuentaColgaap]."', '$id_empresa'),";
				}
			}

			$valueInsert = substr($valueInsert, 0, -1);
			$sqlInsertDefaulNiif   = "INSERT INTO asientos_niif_default(descripcion,estado,cuenta,id_empresa) VALUES $valueInsert";
			$queryInsertDefaulNiif = mysql_query ($sqlInsertDefaulNiif,$link);

			//INSERTAR TIPOS DE NOTAS POR DEFAULT
			$sqlTipoNota="INSERT INTO tipo_nota_contable (descripcion,consecutivo,consecutivo_niif,documento_cruce,id_empresa)
							VALUES
							('NOTA GENERAL',1,1,'Si',$id_empresa),
							('NOTA BANCARIA',1,1,'No',$id_empresa),
							('SALDO INICIAL CONTABLE',1,1,'No',$id_empresa),
							('DEPRECIACION',1,1,'No',$id_empresa),
							('AMORTIZACION',1,1,'No',$id_empresa)";

			$queryTipoNota=mysql_query($sqlTipoNota,$link);
			if (!$queryTipoNota) { if (isset($id_host)) { deshacer_registro($id_host,$bd,'NO SE INSERTO LOS TIPOS DE NOTAS CONTABLES POR DEFECTO <br/>',$acceso_server); }else{deleteInfoEmpresa($link,$id_empresa,"NO SE INSERTO LOS TIPOS DE NOTAS CONTABLES POR DEFECTO <br/>",1);} }

			//============================ CONFIGURACION IMPUESTOS ============================//
			$sqlImpuestos = "INSERT INTO impuestos (impuesto, valor, compra, cuenta_compra, cuenta_compra_niif, cuenta_compra_devolucion, cuenta_compra_devolucion_niif, venta, cuenta_venta, cuenta_venta_niif, cuenta_venta_devolucion, cuenta_venta_devolucion_niif, id_empresa)
							VALUES
								('IVA SERVICIOS 19%', '19.00', 'No', '', '', '', '', 'Si', '24080107', '24080107', '24080201', '24080201', '$id_empresa'),
								('IVA SERVICIOS 5%', '5.00', 'No', '', '', '', '', 'Si', '24080107', '24080107', '24080201', '24080201', '$id_empresa'),
								('IVA COMPRAS 5%', '5.00', 'Si', '24080220', '24080220', '24080220', '24080220', 'No', '', '', '', '', '$id_empresa'),
								('IVA COMPRAS 19%', '19.00', 'Si', '24080219', '24080219', '24080219', '24080219', 'No', '', '', '', '', '$id_empresa')";

			$queryImpuestos=mysql_query($sqlImpuestos,$link);
			if (!$queryImpuestos) { if (isset($id_host)) { deshacer_registro($id_host,$bd,'NO SE INSERTARON LOS IMPUESTOS POR DEFECTO <br/>',$acceso_server); }else{deleteInfoEmpresa($link,$id_empresa,"NO SE INSERTARON LOS IMPUESTOS POR DEFECTO <br/>",1);} }


			//============================ CONFIGURACION RETENCIONES ============================//
				// $arrayRetencionesColgaap = array(0 => array('cuentaVenta' => 135515,'cuentaCompra' => 236540, 'valor' => 2.5, 'tipo' => 'ReteFuente', 'nombre' => 'RETENCION EN LA FUENTE'),
				// 								1 => array('cuentaVenta' => 135517,'cuentaCompra' => 236701, 'valor' => 15, 'tipo' => 'ReteIva', 'nombre' => 'RETENCION IVA'),
				// 								2 => array('cuentaVenta' => 135518,'cuentaCompra' => 236801, 'valor' => 0.33, 'tipo' => 'ReteIca', 'nombre' => 'RETENCION ICA'),
				// 								3 => array('cuentaVenta' => 135519,'cuentaCompra' => '', 'valor' => 0.8, 'tipo' => 'ReteCree', 'nombre' => 'RETENCION CREE', 'cuentaAuto' => 236575));


				// $whereCuentas = '';
				// for($i=0; $i<=3; $i++){
				// 	if($arrayRetencionesColgaap[$i]['cuentaVenta'] > 0){ $whereCuentas .= ' OR cuenta = '.$arrayRetencionesColgaap[$i]['cuentaVenta']; }
				// 	if($arrayRetencionesColgaap[$i]['cuentaCompra'] > 0){ $whereCuentas .= ' OR cuenta = '.$arrayRetencionesColgaap[$i]['cuentaCompra']; }
				// 	if($arrayRetencionesColgaap[$i]['cuentaAuto'] > 0){ $whereCuentas .= ' OR cuenta = '.$arrayRetencionesColgaap[$i]['cuentaAuto']; }
				// }

				// //CONSULTA CUENTAS DE RETENCION NIIF
				// $sqlCuentasNiif   = "SELECT cuenta_niif, cuenta AS cuenta_colgaap FROM puc WHERE id_empresa='$id_empresa' AND activo=1 AND (1>2 $whereCuentas)";
				// $queryCuentasNiif = mysql_query($sqlCuentasNiif,$link);

				// while ($rowNiif = mysql_fetch_array($queryCuentasNiif)) {
				// 	$cuentaNiif    = $rowNiif['cuenta_niif'];
				// 	$cuentaColgaap = $rowNiif['cuenta_colgaap'];

				// 	$arrayCuentaNiif[$cuentaColgaap] = $cuentaNiif;
				// }

				// $valueRetenciones = '';
				// for($i=0; $i<=3; $i++){
				// 	$cuentaAuto     = "null";
				// 	$cuentaAutoNiif = "null";

				// 	if($arrayRetencionesColgaap[$i]['cuentaAuto'] > 0){
				// 		$cuentaAuto     = $arrayRetencionesColgaap[$i]['cuentaAuto'];
				// 		$cuentaAutoNiif = $arrayCuentaNiif[$arrayRetencionesColgaap[$i]['cuentaAuto']];
				// 	}

				// 	$valor               = $arrayRetencionesColgaap[$i]['valor'];
				// 	$tipoRetencion       = $arrayRetencionesColgaap[$i]['tipo'];
				// 	$nombreRetencion     = $arrayRetencionesColgaap[$i]['nombre'];
				// 	$cuentaColgaapCompra = $arrayRetencionesColgaap[$i]['cuentaCompra'];
				// 	$cuentaColgaapVenta  = $arrayRetencionesColgaap[$i]['cuentaVenta'];

				// 	$cuentaNiifCompra = $arrayCuentaNiif[$cuentaColgaapCompra];
				// 	$cuentaNiifVenta  = $arrayCuentaNiif[$cuentaColgaapVenta];

				// 	if ($cuentaColgaapCompra>0) {
				// 		$valueRetenciones .= "('$nombreRetencion','$tipoRetencion','$valor','$cuentaColgaapCompra','$cuentaNiifCompra',$id_empresa,'','','Compra'),";
				// 	}
				// 	if ($cuentaColgaapVenta>0) {
				// 		$valueRetenciones .= "('$nombreRetencion','$tipoRetencion','$valor','$cuentaColgaapVenta','$cuentaNiifVenta',$id_empresa,$cuentaAuto,$cuentaAutoNiif,'Venta'),";
				// 	}
				// }

				// $valueRetenciones = substr($valueRetenciones, 0, -1);
				// $sqlRetenciones   = "INSERT INTO retenciones (retencion,tipo_retencion,valor,cuenta,cuenta_niif,id_empresa,cuenta_autoretencion,cuenta_autoretencion_niif,modulo)
										// VALUES $valueRetenciones";

				// $sqlRetenciones   = "INSERT INTO retenciones (retencion,tipo_retencion,valor,base,cuenta,cuenta_niif,cuenta_autoretencion,cuenta_autoretencion_niif,modulo,id_empresa,id_departamento,departamento,id_ciudad,ciudad)
				// 						VALUES
				// 						('RETEFUENTE HONORARIOS 10% (NO DECLARANTES)', 'ReteFuente', 10.00, 0.00, 23651501, 23651501, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RETEFUENTE HONORARIOS 11% (DECLARANTES Y JURIDICAS', 'ReteFuente', 11.00, 0.00, 23651502, 23651502, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RETEFUENTE HONORARIOS 33% (NO DOMICILIADOS EN EL P', 'ReteFuente', 33.00, 0.00, 23651503, 23651503, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RETEFUENTE COMISIONES 10% (NO DECLARANTES)', 'ReteFuente', 10.00, 0.00, 23652001, 23652001, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RETEFUENTE COMISIONES 11% (DECLARANTES Y JURIDICAS', 'ReteFuente', 11.00, 0.00, 23652011, 23652011, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RETEFUENTE SERVICIOS 1%', 'ReteFuente', 1.00, 110000.00, 23652501, 23652501, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RETEFUENTE SERVICIOS 2%', 'ReteFuente', 2.00, 110000.00, 23652502, 23652502, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RETEFUENTE SERVICIOS 3%', 'ReteFuente', 3.00, 110000.00, 23652503, 23652503, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RETEFUENTE SERVICIO GENERAL 4% (DECLARANTES)', 'ReteFuente', 4.00, 110000.00, 23652505, 23652505, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RETEFUENTE SERVICIO GENERAL 6% (NO DECLARANTES)', 'ReteFuente', 6.00, 110000.00, 23652506, 23652506, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('SERVICIO TRANS PASAJEROS TERRESTRE 2.5% (DECLARANT', 'ReteFuente', 2.50, 742000.00, 23652512, 23652512, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('SERVICIO TRANS PASAJEROS TERRESTRE 3.5% (NO DECLAR', 'ReteFuente', 3.50, 742000.00, 23652513, 23652513, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RETEFUENTE HOTEL Y REST. 3.5% (NO DECLARANTES)', 'ReteFuente', 3.50, 110000.00, 23652535, 23652535, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RETEFUENTE HOTEL Y REST. 2.5% (DECLARANTES)', 'ReteFuente', 2.50, 110000.00, 23652536, 23652536, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RETEFUENTE ARREND. MUEBLE 4%', 'ReteFuente', 4.00, 0.00, 23653001, 23653001, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RETEFUENTE ARREND. INMUEBLE 3.5% (NO DECLARANTES)', 'ReteFuente', 3.50, 742000.00, 23653002, 23653002, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RETEFUENTE ARREND. INMUEBLE 2.5% (DECLARANTES)', 'ReteFuente', 2.50, 742000.00, 23653003, 23653003, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RETEFUENTE SOBRE INTERESES Y DEMAS RENDIMIENTOS 7%', 'ReteFuente', 7.00, 0.00, 23653501, 23653501, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RETEFUENTE SOBRE RENDIMIENTOS TITULOS RENTA FIJA 4', 'ReteFuente', 4.00, 0.00, 23653502, 23653502, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RETEFUENTE POR COMPRAS 3.5% (NO DECLARANTES)', 'ReteFuente', 3.50, 742000.00, 23654001, 23654001, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RETEFUENTE POR COMPRAS 1%', 'ReteFuente', 1.00, 742000.00, 23654002, 23654002, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RETEFUENTE POR COMPRAS 2.5% (DECLARANTES)', 'ReteFuente', 2.50, 742000.00, 23654005, 23654005, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RETEFUENTE POR COMPRA COMBUSTIBLE 0.1%', 'ReteFuente', 0.10, 0.00, 23654010, 23654010, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RETEFUENTE OTROS CONCEPTOS 3.5% (NO DECLARANTES)', 'ReteFuente', 3.50, 742000.00, 23657001, 23657001, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RETEFUENTE OTROS CONCEPTOS 2.5% (DECLARANTES)', 'ReteFuente', 2.50, 742000.00, 23657002, 23657002, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RTF CTA PROPIA COMPRAS 1%', 'ReteFuente', 1.00, 742000.00, 23658001, 23658001, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RTF CTA PROPIA COMPRAS 2.5% (DECLARANTES)', 'ReteFuente', 2.50, 742000.00, 23658003, 23658003, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RTF CTA PROPIA COMPRAS 3.5% (NO DECLARANTES)', 'ReteFuente', 3.50, 742000.00, 23658004, 23658004, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RTF CTA PROPIA TRANSPORTE 1%', 'ReteFuente', 1.00, 110000.00, 23658011, 23658011, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RTF CTA PROPIA CONSTRUCCION 2%', 'ReteFuente', 2.00, 0.00, 23658012, 23658012, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RTF CTA PROPIA SERVICIOS 2.5% (DECLARANTES)', 'ReteFuente', 2.50, 110000.00, 23658013, 23658013, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RTF CTA PROPIA SERVICIOS 3.5% (NO DECLARANTES)', 'ReteFuente', 3.50, 110000.00, 23658014, 23658014, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RTF CTA PROPIA SERVICIOS 4% (DECLARANTES)', 'ReteFuente', 4.00, 110000.00, 23658015, 23658015, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RTF CTA PROPIA SERVICIOS 6% (NO DECLARANTES)', 'ReteFuente', 6.00, 110000.00, 23658016, 23658016, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RTF CTA PROPIA TRANSPORTE PASAJEROS 2.5% (DECLARAN', 'ReteFuente', 2.50, 742000.00, 23658017, 23658017, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RTF CTA PROPIA TRANSPORTE PASAJEROS 3.5% (NO DECLA', 'ReteFuente', 3.50, 742000.00, 23658018, 23658018, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('CALI - RETEICA SERVICIOS 10%', 'ReteIca', 1.00, 82000.00, 23680110, 23680110, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('CALI - RETEICA SERVICIOS 2.2%', 'ReteIca', 0.22, 82000.00, 23680122, 23680122, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('CALI - RETEICA SERVICIOS 3.3%', 'ReteIca', 0.33, 82000.00, 23680133, 23680133, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('CALI - RETEICA INDUSTRIAL 6.6%', 'ReteIca', 0.66, 412000.00, 23680166, 23680166, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('CALI - RETEICA COMERCIAL 7.7%', 'ReteIca', 0.77, 412000.00, 23680177, 23680177, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('CALI - RETEICA SERVICIOS 8.8%', 'ReteIca', 0.88, 82000.00, 23680188, 23680188, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('PEREIRA - SERVICIOS 10%', 'ReteIca', 1.00, 110000.00, 23680438, 23680438, 0, 0, 'Compra', $id_empresa, 1027, 'Risaralda', 2266, 'Pereira' ),
				// 						('CARTAGENA - SERVICIOS 3.0%', 'ReteIca', 0.30, 154000.00, 23680501, 23680501, 0, 0, 'Compra', $id_empresa, 188, 'Bolívar', 2262, 'Cartagena' ),
				// 						('CARTAGENA - SERVICIOS 3.5%', 'ReteIca', 0.35, 154000.00, 23680502, 23680502, 0, 0, 'Compra', $id_empresa, 188, 'Bolívar', 2262, 'Cartagena' ),
				// 						('CARTAGENA - INDUSTRIAL 4%', 'ReteIca', 0.40, 154000.00, 23680503, 23680503, 0, 0, 'Compra', $id_empresa, 188, 'Bolívar', 2262, 'Cartagena' ),
				// 						('CARTAGENA - COMERCIAL 4.5%', 'ReteIca', 0.45, 154000.00, 23680504, 23680504, 0, 0, 'Compra', $id_empresa, 188, 'Bolívar', 2262, 'Cartagena' ),
				// 						('CARTAGENA - SERVICIOS 5%', 'ReteIca', 0.50, 154000.00, 23680505, 23680505, 0, 0, 'Compra', $id_empresa, 188, 'Bolívar', 2262, 'Cartagena' ),
				// 						('CARTAGENA - SERVICIOS 6%', 'ReteIca', 0.60, 154000.00, 23680506, 23680506, 0, 0, 'Compra', $id_empresa, 188, 'Bolívar', 2262, 'Cartagena' ),
				// 						('CARTAGENA - SERVICIOS 7%', 'ReteIca', 0.70, 154000.00, 23680507, 23680507, 0, 0, 'Compra', $id_empresa, 188, 'Bolívar', 2262, 'Cartagena' ),
				// 						('CARTAGENA - SERVICIOS 8%', 'ReteIca', 0.80, 154000.00, 23680508, 23680508, 0, 0, 'Compra', $id_empresa, 188, 'Bolívar', 2262, 'Cartagena' ),
				// 						('RETEF. R.S 15% PARA IVA 5% SERVICIOS', 'ReteFuente', 15.00, 5500.00, 23670103, 23670103, 24080237, 24080237, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RETEF. R.S 15% PARA IVA 5% COMPRAS', 'ReteFuente', 15.00, 37100.00, 23670104, 23670104, 24080238, 24080238, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RETEF. R.S 15% PARA IVA 16% SERVICIOS', 'ReteFuente', 15.00, 17600.00, 23670105, 23670105, 24080240, 24080240, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RETEF. R.S 15% PARA IVA 16% COMPRAS', 'ReteFuente', 15.00, 118720.00, 23670106, 23670106, 24080241, 24080241, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('AUTORRETENCION DE CREE 0.80%', 'AutoRetencion', 0.80, 0.00, 13551920, 13551920, 23692015, 23692015, 'Venta', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RETEFUENTE A FAVOR ALQUILER', 'ReteFuente', 4.00, 0.00, 13551501, 13551501, 0, 0, 'Venta', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RETEFUENTE A FAVOR SERVICIOS', 'ReteFuente', 4.00, 110000.00, 13551502, 13551502, 0, 0, 'Venta', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RETEFUENTE A FAVOR INTERESES', 'ReteFuente', 7.00, 0.00, 13551503, 13551503, 0, 0, 'Venta', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RETEFUENTE A FAVOR HONORARIOS', 'ReteFuente', 11.00, 0.00, 13551504, 13551504, 0, 0, 'Venta', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RETEFUENTE A FAVOR COMPRAS', 'ReteFuente', 2.50, 742000.00, 13551506, 13551506, 0, 0, 'Venta', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('IMPOVENTAS RETENIDO A FAVOR (ALQ-HON-FIN)', 'ReteIva', 15.00, 0.00, 13551701, 13551701, 0, 0, 'Venta', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('IMPOVENTAS RETENIDO A FAVOR (SERVICIOS)', 'ReteIva', 15.00, 110000.00, 13551701, 13551701, 0, 0, 'Venta', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('IMPOVENTAS RETENIDO A FAVOR (COMPRAS)', 'ReteIva', 15.00, 742000.00, 13551701, 13551701, 0, 0, 'Venta', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RETENCION ICA - CALI', 'ReteIca', 1.00, 82000.00, 13551801, 13551801, 0, 0, 'Venta', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
				// 						('RETENCION ICA - PEREIRA', 'ReteIca', 1.00, 110000.00, 13551804, 13551804, 0, 0, 'Venta', $id_empresa, 1027, 'Risaralda', 2266, 'Pereira' ),
				// 						('RETENCION ICA - CARTAGENA', 'ReteIca', 0.80, 154000.00, 13551805, 13551805, 0, 0, 'Venta', $id_empresa, 188, 'Bolívar', 2262, 'Cartagena' ),
				// 						('RETENCION ICA - ARMENIA', 'ReteIca', 1.00, 1232000.00, 13551806, 13551806, 0, 0, 'Venta', $id_empresa, 1006, 'Quindío', 2273, 'Armenia' )";
			//end

			$sqlRetenciones="INSERT INTO retenciones (retencion,tipo_retencion,valor,base,cuenta,cuenta_niif,cuenta_autoretencion,cuenta_autoretencion_niif,modulo,id_empresa,id_departamento,departamento,id_ciudad,ciudad)
							VALUES
							('RETEFUENTE SERVICIOS 1%', 'ReteFuente', 1.00, 110000.00, 23652501, 23652501, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('RETEFUENTE SERVICIOS 2%', 'ReteFuente', 2.00, 110000.00, 23652502, 23652502, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('RETEFUENTE SERVICIOS 3%', 'ReteFuente', 3.00, 110000.00, 23652503, 23652503, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('RETEFUENTE POR COMPRAS 3.5% (NO DECLARANTES)', 'ReteFuente', 3.50, 742000.00, 23654001, 23654001, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('RETEFUENTE POR COMPRAS 1%', 'ReteFuente', 1.00, 742000.00, 23654002, 23654002, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('RETEFUENTE POR COMPRAS 2.5% (DECLARANTES)', 'ReteFuente', 2.50, 742000.00, 23654005, 23654005, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('CALI - RETEICA SERVICIOS 10%', 'ReteIca', 1.00, 82000.00, 23680110, 23680110, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('CALI - RETEICA SERVICIOS 2.2%', 'ReteIca', 0.22, 82000.00, 23680122, 23680122, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('CALI - RETEICA SERVICIOS 3.3%', 'ReteIca', 0.33, 82000.00, 23680133, 23680133, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('CALI - RETEICA INDUSTRIAL 6.6%', 'ReteIca', 0.66, 412000.00, 23680166, 23680166, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('CALI - RETEICA COMERCIAL 7.7%', 'ReteIca', 0.77, 412000.00, 23680177, 23680177, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('CALI - RETEICA SERVICIOS 8.8%', 'ReteIca', 0.88, 82000.00, 23680188, 23680188, 0, 0, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('RETEF. R.S 15% PARA IVA 5% SERVICIOS', 'ReteFuente', 15.00, 5500.00, 23670103, 23670103, 24080237, 24080237, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('RETEF. R.S 15% PARA IVA 5% COMPRAS', 'ReteFuente', 15.00, 37100.00, 23670104, 23670104, 24080238, 24080238, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('RETEF. R.S 15% PARA IVA 19% SERVICIOS', 'ReteFuente', 15.00, 17600.00, 23670105, 23670105, 24080240, 24080240, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('RETEF. R.S 15% PARA IVA 19% COMPRAS', 'ReteFuente', 15.00, 118720.00, 23670106, 23670106, 24080241, 24080241, 'Compra', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('AUTORRETENCION DE CREE 0.80%', 'AutoRetencion', 0.80, 0.00, 13551920, 13551920, 23692015, 23692015, 'Venta', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('RETEFUENTE A FAVOR ALQUILER', 'ReteFuente', 4.00, 0.00, 13551501, 13551501, 0, 0, 'Venta', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('RETEFUENTE A FAVOR SERVICIOS', 'ReteFuente', 4.00, 110000.00, 13551502, 13551502, 0, 0, 'Venta', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('RETEFUENTE A FAVOR INTERESES', 'ReteFuente', 7.00, 0.00, 13551503, 13551503, 0, 0, 'Venta', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('RETEFUENTE A FAVOR HONORARIOS', 'ReteFuente', 11.00, 0.00, 13551504, 13551504, 0, 0, 'Venta', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('RETEFUENTE A FAVOR COMPRAS', 'ReteFuente', 2.50, 742000.00, 13551506, 13551506, 0, 0, 'Venta', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('IMPOVENTAS RETENIDO A FAVOR (ALQ-HON-FIN)', 'ReteIva', 15.00, 0.00, 13551701, 13551701, 0, 0, 'Venta', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('IMPOVENTAS RETENIDO A FAVOR (SERVICIOS)', 'ReteIva', 15.00, 110000.00, 13551701, 13551701, 0, 0, 'Venta', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('IMPOVENTAS RETENIDO A FAVOR (COMPRAS)', 'ReteIva', 15.00, 742000.00, 13551701, 13551701, 0, 0, 'Venta', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('RETENCION ICA - CALI', 'ReteIca', 1.00, 82000.00, 13551801, 13551801, 0, 0, 'Venta', $id_empresa, 1277, 'Valle', 2259, 'Cali' ),
							('RETENCION ICA - PEREIRA', 'ReteIca', 1.00, 110000.00, 13551804, 13551804, 0, 0, 'Venta', $id_empresa, 1027, 'Risaralda', 2266, 'Pereira' ),
							('RETENCION ICA - CARTAGENA', 'ReteIca', 0.80, 154000.00, 13551805, 13551805, 0, 0, 'Venta', $id_empresa, 188, 'Bolívar', 2262, 'Cartagena' ),
							('RETENCION ICA - ARMENIA', 'ReteIca', 1.00, 1232000.00, 13551806, 13551806, 0, 0, 'Venta', $id_empresa, 1006, 'Quindío', 2273, 'Armenia' )";
			$queryRetenciones = mysql_query($sqlRetenciones,$link);

			if (!$queryRetenciones) { if (isset($id_host)) { deshacer_registro($id_host,$bd,'NO SE INSERTARON LAS RETENCIONES POR DEFECTO <br/>',$acceso_server); }else{deleteInfoEmpresa($link,$id_empresa,"NO SE INSERTARON LAS RETENCIONES POR DEFECTO <br/>",1);} }

			//===================== USUARIO ADMINISTRADOR ========================//
			/**********************************************************************/
			//ROLES
			$sqlRoles="INSERT INTO empleados_roles (nombre,valor,id_empresa)
						VALUES
						('Administrador',1,$id_empresa),
						('Director de Zona',2,$id_empresa),
						('Auditoria Interna',2,$id_empresa),
						('Direccion de Calidad',2,$id_empresa),
						('Auxiliar de Recursos Humanos',2,$id_empresa),
						('Direccion General',2,$id_empresa),
						('Subdireccion General',2,$id_empresa),
						('Direccion Financiera',2,$id_empresa),
						('Direccion de Compras y Contrataciones',2,$id_empresa),
						('Direccion Comercial',2,$id_empresa),
						('Direccion Juridica',2,$id_empresa),
						('Direccion de Proyectos',2,$id_empresa),
						('Direccion de Tecnologia e Informatica',2,$id_empresa),
						('Auxiliar de Sistemas',2,$id_empresa)";

			$queryRoles=mysql_query($sqlRoles,$link);
			if (!$queryRoles) { if (isset($id_host)) { deshacer_registro($id_host,$bd,'NO SE INSERTARON LOS ROLES POR DEFECTO PARA EL EMPLEADO  <br/>',$acceso_server); }else{deleteInfoEmpresa($link,$id_empresa,"NO SE INSERTARON LOS ROLES POR DEFECTO PARA EL EMPLEADO  <br/>",1);} }

			//CARGOS
			$sqlCargos = "INSERT INTO empleados_cargos (nombre,id_empresa)
				 			VALUES
				 			('Administrador',$id_empresa),
				 			('Director General',$id_empresa),
				 			('Subdirectora General',$id_empresa),
				 			('Director Juridico',$id_empresa),
				 			('Director Financiero',$id_empresa),
				 			('Director de Tecnologia e Informática',$id_empresa),
				 			('Directora de Calidad',$id_empresa),
				 			('Directora Comercial',$id_empresa),
				 			('Director de Proyectos y Mantenimiento',$id_empresa),
				 			('Director de Video y Comunicaciones Dig',$id_empresa),
				 			('Director de Zona',$id_empresa),
				 			('Asistente Administrativa',$id_empresa),
				 			('Asistente Administrativo',$id_empresa),
				 			('Asistente Financiera',$id_empresa),
				 			('Directora de Compras y Contrataciones',$id_empresa),
				 			('Directora de Recursos Humanos',$id_empresa),
				 			('Tesorero',$id_empresa),
				 			('Mensajero',$id_empresa),
				 			('Asistente de Mantenimiento',$id_empresa),
				 			('Coordinador de Eventos',$id_empresa),
				 			('Director Operativo',$id_empresa),
				 			('Asistente de Video y Comunicaciones Di',$id_empresa),
				 			('Asistente Comercial',$id_empresa),
				 			('Asistente de Sistemas',$id_empresa),
				 			('Auditora Interna',$id_empresa),
				 			('Servicios Generales',$id_empresa),
				 			('Estudiante SENA',$id_empresa),
				 			('Ejecutivo Comercial',$id_empresa),
				 			('Director Unidad Independiente',$id_empresa),
				 			('Gerente General',$id_empresa),
				 			('Desarrollador y Soportista',$id_empresa)";

			$queryCargos=mysql_query($sqlCargos,$link);
			if (!$queryCargos) { if (isset($id_host)) { deshacer_registro($id_host,$bd,'NO SE INSERTARON LOS CARGOS POR DEFECTO PARA LOS EMPLEADOS <br/>',$acceso_server); }else{deleteInfoEmpresa($link,$id_empresa,"NO SE INSERTARON LOS CARGOS POR DEFECTO PARA LOS EMPLEADOS <br/>",1);} }

			//EMPLEADOS ROLES PERMISOS, PARA CREAR EL USUARIO SUPER USUARIO
			//CONSULTAR EL ID DEL ROL DEL SUPER USUARIO PARA INSERTARLE LOS PERMISOS
			$sqlSelectRol   = "SELECT id FROM empleados_roles WHERE id_empresa=$id_empresa AND valor=1";
			$querySelectRol = mysql_query($sqlSelectRol,$link);
			$idRolAdmin     = mysql_result($querySelectRol, 0, 'id');


			/*=================== VAR NUMERO DE PERMISOS =================*/
			$contPermisos  = 300;
			$valuePermisos = "";
			for ($i=1; $i <= $contPermisos ; $i++) { $valuePermisos .= "($idRolAdmin,$i),"; }

			$valuePermisos      = substr($valuePermisos, 0, -1);
			$sqlRolesPermisos   = "INSERT INTO empleados_roles_permisos (id_rol,id_permiso) VALUES $valuePermisos";
			$queryRolesPermisos = mysql_query($sqlRolesPermisos,$link);

			if (!$queryRolesPermisos) { if (isset($id_host)) { deshacer_registro($id_host,$bd,'NO SE INSERTARON LOS PERMISOS PARA EL USUARIO <br/>',$acceso_server); }else{deleteInfoEmpresa($link,$id_empresa,"NO SE INSERTARON LOS PERMISOS PARA EL USUARIO <br/>",1);} }

			//INSERTAR LOS TIPOS DE DOCUMENTOS POR DEFAULT
			$sqlTipoDocumento = "INSERT INTO tipo_documento (codigo, codigo_tipo_documento_dian, nombre, detalle, tipo, id_empresa)
								VALUES
									('1', '11', 'R.C', 'Registro civil', 'Persona', '$id_empresa'),
									('2', '12', 'T.I', 'Tarjeta de identidad', 'Persona', '$id_empresa'),
									('3', '13', 'C.C', 'Cedula de Ciudadania', 'Persona', '$id_empresa'),
									('4', '21', 'T.E', 'Tarjeta de extranjeria', 'Persona', '$id_empresa'),
									('5', '22', 'C.E', 'Cedula de extranjeria', 'Persona', '$id_empresa'),
									('6', '31', 'NIT', 'Numero de Identificacion', 'Empresa', '$id_empresa'),
									('7', '41', 'Pasaporte', 'Pasaporte', 'Persona', '$id_empresa'),
									('8', '42', 'DIE', '	Documento de identificacion extranjero', 'Persona', '$id_empresa'),
									('9', '91', 'NUIP', 'NUIP *', 'Persona', '$id_empresa')";
			$queryTipoDocumento=mysql_query($sqlTipoDocumento,$link);
			if (!$queryTipoDocumento) { if (isset($id_host)) { deshacer_registro($id_host,$bd,'NO SE INSERTARON LOS TIPOS DE DOCUMENTOS DEL EMPLEADO <br/>',$acceso_server); }else{deleteInfoEmpresa($link,$id_empresa,"NO SE INSERTARON LOS TIPOS DE DOCUMENTOS DEL EMPLEADO <br/>",1);} }

			// INSERTAR LOS METODOS DE PAGO DE LAS FACTURAS DE VENTA (DIAN)
			$sql = "INSERT INTO configuracion_metodos_pago (id, nombre, activo, codigo_metodo_pago_dian, id_empresa)
					VALUES
						('1', 'Efectivo', '1', '25', '1'),
						('2', 'Cheque', '1', '26', '1'),
						('3', 'Transferencia Bancaria', '1', '27', '1'),
						('4', 'Consigancion Bancaria', '1', '28', '1');";
			$query = mysql_query($sql,$link);

			$sqlTypeDoc   = "SELECT id,nombre FROM tipo_documento WHERE id_empresa='$id_empresa' AND activo=1 AND (nombre='$tipo_documento' OR nombre='C.C')";
			$queryTypeDoc = mysql_query($sqlTypeDoc,$link);

			while ($rowTypeDoc = mysql_fetch_assoc($queryTypeDoc)) {
				if($rowTypeDoc['nombre'] == 'C.C'){ $idTypeDocEmpleado = $rowTypeDoc['id']; }
				if($rowTypeDoc['nombre'] == $tipo_documento){ $idTypeDoc = $rowTypeDoc['id']; }
			}

			$sqlUpdateTypeDoc   = "UPDATE empresas SET tipo_documento='$idTypeDoc' WHERE id='$id_empresa' AND activo=1";
			$queryUpdateTypeDoc = mysql_query($sqlUpdateTypeDoc,$link);

			//INSERTAR UN USUARIO POR DEFAULT CON PERMISOS DE SUPER-USUARIO
			//CONSULTAR EL ID DE LA SUCURSAL CREADA
			$sqlSelectSucursal   = "SELECT id FROM empresas_sucursales WHERE id_empresa=$id_empresa AND nombre='$sucursal'";
			$querySelectSucursal = mysql_query($sqlSelectSucursal,$link);
			$id_sucursal         = mysql_result($querySelectSucursal,0,'id');
			//CONSULTAR CARGO
			$sqlSelectCargo   = "SELECT id FROM empleados_cargos WHERE nombre='Gerente General' AND id_empresa=$id_empresa";
			$querySelectCargo = mysql_query($sqlSelectCargo,$link);
			$id_cargo         = mysql_result($querySelectCargo,0,'id');

			$username = strtolower ( $nombre1.'.'.$apellido1 );
			$password = md5('12345678');

			$sqlUsuario   = "INSERT INTO empleados (id_empresa,id_sucursal,id_rol,id_cargo,tipo_documento,documento,nombre1,nombre2,apellido1,apellido2,id_pais,username,password)
							VALUES($id_empresa,$id_sucursal,$idRolAdmin,$id_cargo,$idTypeDocEmpleado,$nit,'$nombre1','$nombre2','$apellido1','$apellido2','$id_pais','$username','$password')";
			$queryUsuario = mysql_query($sqlUsuario,$link);

			if (!$queryUsuario) { if (isset($id_host)) { deshacer_registro($id_host,$bd,'NO SE INSERTO EL EMPLEADO <br/>',$acceso_server); }else{deleteInfoEmpresa($link,$id_empresa,"NO SE INSERTO EL EMPLEADO <br/>",1);} }

			//INSERTAR LOS TIPOS DE DOCUMENTO DEL EMPLEADO
			$sqlEmpleadoTipoDocumento = "INSERT INTO empleados_tipo_documento (nombre,id_empresa)
		 								VALUES ('Documento de Identidad',$id_empresa),
		 										('Libreta Militar',$id_empresa),
		 										('Certificado Judicial',$id_empresa),
		 										('Contrato',$id_empresa),
		 										('Hoja de Vida',$id_empresa),
		 										('Certificado de Estudios',$id_empresa),
		 										('Afiliaciones',$id_empresa),
		 										('Llamados de Atencion',$id_empresa),
		 										('Felicitaciones',$id_empresa),
		 										('Evaluaciones de Desempeño',$id_empresa),
		 										('Perfil de Cargos y Funciones',$id_empresa)";
			$queryEmpleadoTipoDocumento=mysql_query($sqlEmpleadoTipoDocumento,$link);
			if (!$queryEmpleadoTipoDocumento) { if (isset($id_host)) { deshacer_registro($id_host,$bd,'NO SE INSERTO EL TIPO DE DOCUMENTO POR DEFAULT PARA EL EMPLEADO <br/>',$acceso_server); }else{deleteInfoEmpresa($link,$id_empresa,"NO SE INSERTO EL TIPO DE DOCUMENTO POR DEFAULT PARA EL EMPLEADO <br/>",1);} }

			//INSERTAR UNA FORMA DE PAGO POR DEFECTO
			$sqlFormaPago="INSERT INTO configuracion_formas_pago (nombre,plazo,id_empresa)
		 					VALUES ('Contado','1',$id_empresa),
		 							('Semanal','7',$id_empresa),
		 							('Quincena','15',$id_empresa),
		 							('Mes','30',$id_empresa)";
			$queryFormaPago=mysql_query($sqlFormaPago,$link);
			if (!$queryFormaPago) { if (isset($id_host)) { deshacer_registro($id_host,$bd,'NO SE INSERTO LA FORMA DE PAGO POR DEFECTO <br/>',$acceso_server); }else{deleteInfoEmpresa($link,$id_empresa,"NO SE INSERTO LA FORMA DE PAGO POR DEFECTO <br/>",1);} }


			//===================== CUENTAS DE PAGO POR DEFECTO POR GRUPO EMPRESARIAL ========================//
			/**************************************************************************************************/
			$whereCuentas         = '';
			$valueAsientosDefault = '';
			foreach ($arrayCuentaPagoDefault as $cuentaColgaap => $arrayCuenta) { $whereCuentas .= ' OR cuenta = '.$cuentaColgaap; }
			$whereCuentas = substr($whereCuentas, 4);
			$whereCuentas = 'AND ('.$whereCuentas.')';

			$sqlCuentasDefaultNiif   = "SELECT cuenta_niif, cuenta AS cuenta_colgaap
										FROM puc
										WHERE activo=1
											AND id_empresa='$id_empresa' $whereCuentas";
			$queryCuentasDefaultNiif = mysql_query($sqlCuentasDefaultNiif,$link);
			while ($rowCuentasDefault = mysql_fetch_array($queryCuentasDefaultNiif)) {
				$cuentaColgaap = $rowCuentasDefault['cuenta_colgaap'];

				$arrayCuentaPagoDefault[$cuentaColgaap]['cuenta_niif'] = $rowCuentasDefault['cuenta_niif'];
			}

			$valueInsert = '';
			foreach ($arrayCuentaPagoDefault as $cuentaColgaap => $arrayCuenta) {
				$valueInsert .= "('".$arrayCuenta['detalle']."', '".$arrayCuenta['type']."', '".$cuentaColgaap."','".$arrayCuenta['cuenta_niif']."', '$id_empresa', '".$arrayCuenta['estado']."'),";
			}
			$valueInsert     = substr($valueInsert, 0, -1);
			$sqlCuentaPago   = "INSERT INTO configuracion_cuentas_pago (nombre,tipo,cuenta,cuenta_niif,id_empresa,estado) VALUES $valueInsert";
			$queryCuentaPago = mysql_query($sqlCuentaPago,$link);
			if (!$queryCuentaPago) { if (isset($id_host)) { deshacer_registro($id_host,$bd,'NO SE INSERTO LA CUENTA DE PAGO POR DEFECTO <br/>',$acceso_server); }else{deleteInfoEmpresa($link,$id_empresa,"NO SE INSERTO LA CUENTA DE PAGO POR DEFECTO <br/>",1);} }

			//================ CONFIGURACION POR DEFECTO DEL PUC =======================//
			$sqlConfiPuc = "INSERT INTO puc_configuracion (nombre,digitos,id_empresa)
							VALUES ('CLASE',1,$id_empresa),
									('GRUPO',2,$id_empresa),
									('CUENTA',4,$id_empresa),
									('SUBCUENTA',6,$id_empresa),
									('AUXILIARES',8,$id_empresa)";

			$queryConfigPuc=mysql_query($sqlConfiPuc,$link);
			if (!$queryConfigPuc) { if (isset($id_host)) { deshacer_registro($id_host,$bd,'NO SE INSERTO LA CONFIGURACION DEL PUC POR DEFECTO <br/>',$acceso_server); }else{deleteInfoEmpresa($link,$id_empresa,"NO SE INSERTO LA CONFIGURACION DEL PUC POR DEFECTO <br/>",1);} }

			//INSERTAR LA BODEGA
			$sqlBodega   = "INSERT INTO empresas_sucursales_bodegas (id_empresa,id_sucursal,nombre) VALUES ($id_empresa,$id_sucursal,'$bodega')";
			$queryBodega = mysql_query($sqlBodega,$link);
			if (!$queryBodega) { if (isset($id_host)) { deshacer_registro($id_host,$bd,'NO SE INSERTO LA BODEGA <br/>',$acceso_server); }else{deleteInfoEmpresa($link,$id_empresa,"NO SE INSERTO LA BODEGA <br/>",1);} }

			//INSERTAR UNIDADES POR DEFECTO PARA LOS ITEMS
			$sqlUnidades="INSERT INTO inventario_unidades (nombre, unidades,id_empresa)
							VALUES ('Unidad','1',$id_empresa),
									('Docena','12',$id_empresa),
									('Servicio','1',$id_empresa)";
			$queryUnidades=mysql_query($sqlUnidades,$link);
			if (!$queryUnidades) { if (isset($id_host)) { deshacer_registro($id_host,$bd,'NO SE INSERTARON LAS UNIDADES DEL INVENTARIO POR DEFAULT <br/>',$acceso_server); }else{deleteInfoEmpresa($link,$id_empresa,"NO SE INSERTARON LAS UNIDADES DEL INVENTARIO POR DEFAULT <br/>",1);} }

			//INSERTAR LA CONFIGURACION DEL POS POR DEFECTO (CANTIDAD DE CONSECUTIVOS POR CAJA)
			$sqlPos   = "INSERT INTO ventas_pos_configuracion_consecutivos (cantidad, id_empresa) VALUES ('100',$id_empresa)";
			$queryPos = mysql_query($sqlPos,$link);
			if (!$queryPos) { if (isset($id_host)) { deshacer_registro($id_host,$bd,'NO SE INSERTO LA CANTIDAD DE CONSECUTIVOS PARA CADA POS <br/>',$acceso_server); }else{deleteInfoEmpresa($link,$id_empresa,"NO SE INSERTO LA CANTIDAD DE CONSECUTIVOS PARA CADA POS <br/>",1);} }

			//INSERTAR LOS SECTORES EMPRESARIALES POR DEEFECTO
			$sqlSectoresEmpresariales = "INSERT INTO configuracion_sector_empresarial (nombre,id_empresa)
										VALUES('Educativo', $id_empresa),
												('Hotelero', $id_empresa),
												('Comercial', $id_empresa),
												('Industrial', $id_empresa),
												('Financiero', $id_empresa),
												('Salud', $id_empresa),
												('Produccion de Eventos', $id_empresa),
												('Centros Comerciales', $id_empresa),
												('Clubes', $id_empresa),
												('Asociaciones', $id_empresa),
												('Servicios', $id_empresa),
												('Persona Natural', $id_empresa),
												('Iglesias', $id_empresa),
												('Software y Tecnologia', $id_empresa)";
			$querySectoresEmpresariales=mysql_query($sqlSectoresEmpresariales,$link);
			if (!$querySectoresEmpresariales) { if (isset($id_host)) { deshacer_registro($id_host,$bd,'NO SE INSERTARON LOS SECTORES EMPRESARIALES POR DEFAULT <br/>',$acceso_server); }else{deleteInfoEmpresa($link,$id_empresa,"NO SE INSERTARON LOS SECTORES EMPRESARIALES POR DEFAULT <br/>",1);} }

			//INSERTAR EL TRATAMIENTO A TERCEROS
			$sqlTratamientoTerceros="INSERT INTO terceros_tratamiento (nombre,id_empresa)
										VALUES ('Sr.',$id_empresa),
												('Sra.',$id_empresa),
												('Srta.',$id_empresa),
												('Dr.',$id_empresa),
												('Dra.',$id_empresa),
												('Lic.',$id_empresa),
												('Ing.',$id_empresa)";
			$queryTratamientoTerceros=mysql_query($sqlTratamientoTerceros,$link);
			if (!$queryTratamientoTerceros) { if (isset($id_host)) { deshacer_registro($id_host,$bd,'NO SE INSERTARON LOS TRATAMIENTO A TERCEROS POR DEFAULT <br/>',$acceso_server); }else{deleteInfoEmpresa($link,$id_empresa,"NO SE INSERTARON LOS TRATAMIENTO A TERCEROS POR DEFAULT <br/>",1);} }

			//INSERTAR TIPOS DOCUMENTO TERCEROS
			$sqlTratamientoTerceros = "INSERT INTO terceros_tipo_documento (nombre,id_empresa)
										VALUES ('Foto',$id_empresa),
												('Cedula',$id_empresa),
												('Tarjeta',$id_empresa),
												('Certificado',$id_empresa),
												('Contrato',$id_empresa),
												('Cedula de Extranjeria',$id_empresa),
												('RUT',$id_empresa)";
			$queryTratamientoTerceros=mysql_query($sqlTratamientoTerceros,$link);
			if (!$queryTratamientoTerceros) { if (isset($id_host)) { deshacer_registro($id_host,$bd,'NO SE INSERTARON LOS TIPOS DE DOCUMENTOS A TERCEROS POR DEFAULT <br/>',$acceso_server); }else{deleteInfoEmpresa($link,$id_empresa,"NO SE INSERTARON LOS TIPOS DE DOCUMENTOS A TERCEROS POR DEFAULT <br/>",1);} }

			//INSERTAR TIPOS DOCUMENTO TERCEROS
			$sqlTipoOrdenes = "INSERT INTO compras_ordenes_tipos (nombre, id_empresa)
								VALUES ('NUEVO PROYECTO', '$id_empresa'),
									('REPOSICION DE EQUIPOS', '$id_empresa'),
									('REFUERZO DE OPERACION', '$id_empresa'),
									('EQUIPOS PARA LA VENTA', '$id_empresa'),
									('SUMINISTROS', '$id_empresa')";
			$queryTipoOrdenes=mysql_query($sqlTipoOrdenes,$link);
			if (!$queryTipoOrdenes) { if (isset($id_host)) { deshacer_registro($id_host,$bd,'NO SE INSERTARON LOS TIPOS DE ORDENES DE COMPRA <br/>',$acceso_server); }else{deleteInfoEmpresa($link,$id_empresa,"NO SE INSERTARON LOS TIPOS DE DOCUMENTOS A TERCEROS POR DEFAULT <br/>",1);} }

			//SE INSERTAN POR TRIGER
				//INSERTAR CONFIGURACION CONSECUTIVOS DOCUMENTOS
				// $sqlTratamientoTerceros="INSERT INTO configuracion_consecutivos_documentos (documento,id_empresa,id_sucursal,consecutivo,modulo)
				// 							VALUES ('cotizacion',$id_empresa,$id_sucursal,1,'venta'),
				// 									('pedido',$id_empresa,$id_sucursal,1,'venta'),
				// 									('remision',$id_empresa,$id_sucursal,1,'venta'),
				// 									('factura',$id_empresa,$id_sucursal,1,'venta'),
				// 									('recibo_de_caja',$id_empresa,$id_sucursal,1,'venta'),
				// 									('orden_de_compra',$id_empresa,$id_sucursal,1,'compra'),
				// 									('factura',$id_empresa,$id_sucursal,1,'compra'),
				// 									('planilla_de_nomina',$id_empresa,$id_sucursal,1,'nomina'),
				// 									('pos_venta',$id_empresa,$id_sucursal,1,'venta'),
				// 									('comprobante_de_egreso',$id_empresa,$id_sucursal,1,'compra')";

				// $queryTratamientoTerceros=mysql_query($sqlTratamientoTerceros,$link);
				// if (!$queryTratamientoTerceros) { if (isset($id_host)) { deshacer_registro($id_host,$bd,'NO SE CREO LA EMPRESA <br/>',$acceso_server); }else{deleteInfoEmpresa($link,$id_empresa,"NO SE CREO LA EMPRESA <br/>",1);} deshacer_registro($id_host,$bd,'',$acceso_server); /*deleteInfoEmpresa($link,$id_empresa,"NO SE INSERTO lA CONFIGURACION DE LOS CONSECUTIVOS DE LOS DOCUMENTOS<br/>",27); */}

				//INSERTAR CENTROS DE COSTOS
			// if($_SERVER['SERVER_NAME'] == 'logicalerp.localhost' || $_SERVER['SERVER_NAME'] == 'erp.plataforma.co'){
				include_once ('configuraciones/ccos_familia_siip.php');
			// }
			// else{ include_once ('configuraciones/ccos_familia.php'); }

			//INSERTAR LAS VARIABLES DEL SISTEMA
			if($_SERVER['SERVER_NAME'] != 'logicalerp.localhost' || $_SERVER['SERVER_NAME'] != 'erp.plataforma.co' || $_SERVER['SERVER_NAME'] != 'www.erp.plataforma.co'){

				$sqlVariablesGrupos   = "INSERT INTO variables_grupos (nombre,id_empresa) VALUES
										('General','$id_empresa'),
										('Cotizacion de Venta','$id_empresa'),
										('Pedido de Venta','$id_empresa'),
										('Remision de Venta','$id_empresa'),
										('Factura de Venta','$id_empresa'),
										('Orden de Compra','$id_empresa'),
										('Factura de Compra','$id_empresa')";
				$queryVariablesGrupos = mysql_query($sqlVariablesGrupos,$link);
				if (!$queryVariablesGrupos) { if (isset($id_host)) { deshacer_registro($id_host,$bd,'NO SE INSERTO EL GRUPO DE VARIABLES <br/>',$acceso_server); }
				else{ deleteInfoEmpresa($link,$id_empresa,"NO SE INSERTO EL GRUPO DE VARIABLES <br/>",1); } }
			}

			//INSERTAR LAS VARIABLES DEL GRUPO DE VARIABLES
			$sqlVariablesGrupos   = "SELECT id,nombre FROM variables_grupos WHERE id_empresa='$id_empresa'";
			$queryVariablesGrupos = mysql_query($sqlVariablesGrupos,$link);

			$idGrupoFV = 0;
			$idGrupoOC = 0;
			while ($rowVariablesGrupo = mysql_fetch_assoc($queryVariablesGrupos)) {

				switch ($rowVariablesGrupo['nombre']) {
					case 'General':
						$idGrupoGeneral = $rowVariablesGrupo['id'];
						break;

					case 'Cotizacion de Venta':
						$idGrupoCV = $rowVariablesGrupo['id'];
						break;

					case 'Pedido de Venta':
						$idGrupoPV = $rowVariablesGrupo['id'];
						break;

					case 'Remision de Venta':
						$idGrupoRV = $rowVariablesGrupo['id'];
						break;

					case 'Factura de Venta':
						$idGrupoFV = $rowVariablesGrupo['id'];
						break;

					case 'Orden de Compra':
						$idGrupoOC = $rowVariablesGrupo['id'];
						break;

					case 'Factura de Compra':
						$idGrupoFC = $rowVariablesGrupo['id'];
						break;

					default:
						# code...
						break;
				}
			}

			include_once ('configuraciones/variables_documentos.php');
			$sqlVariables   = "INSERT INTO variables (nombre,detalle,id_grupo,grupo,campo,tabla,funcion,automatica,id_empresa)
								VALUES $valuesGeneral $valuesCV $valuesPV $valuesRV $valuesFV $valuesOC $valuesFC";
			$queryVariables = mysql_query($sqlVariables,$link);
			if (!$queryVariables) { if (isset($id_host)) { deshacer_registro($id_host,$bd,'NO SE INSERTARON LAS VARIABLES DEL SISTEMA <br/>'.$sqlVariables,$acceso_server); }else{deleteInfoEmpresa($link,$id_empresa,"NO SE INSERTARON LAS VARIABLES DEL SISTEMA <br/>".mysql_error().mysql_errno(),1);} }

			//INSERTAR EL DOCUMENTO POR DEFECTO
			$sqlConfiguracionDocumento = "INSERT INTO configuracion_documentos_erp (nombre,tipo,texto,id_empresa,empresa,id_sucursal) VALUES
										('Cotizacion de Venta','CV','<style type=\"text/css\">\n.StyleTableHeader{\n	font-size		:10px;\n		font-family		:\"Segoe UI Light\",\"Helvetica Neue Light\",\"Segoe UI\",\"Helvetica Neue\",\"Trebuchet MS\",Helvetica,\"Droid Sans\",Tahoma,Geneva,sans-serif;\n		border			:1px solid #000;\nborder-collapse:collapse;\n	}\n	.StyleTableFooter{\n		font-size		:9px;\n		font-family		:\"Segoe UI Light\",\"Helvetica Neue Light\",\"Segoe UI\",\"Helvetica Neue\",\"Trebuchet MS\",Helvetica,\"Droid Sans\",Tahoma,Geneva,sans-serif;\n		border			:1px solid #000;\n	}\n.StyleTablaEncuesta td{ border-right:1px solid #000; border-bottom:1px solid #000;}\n.StyleTablaEncuesta{ border-collapse:collapse; border:none; }</style>\n<htmlpageheader class=\"SoloPDF\" name=\"MyHeader1\">\n<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" class=\"StyleTableHeader\" width=\"740\">\n	<tbody>\n		<tr>\n			<td align=\"center\">\n				<img alt=\"\" src=\"../../../../ARCHIVOS_PROPIOS/imagenes_empresas/empresa_1/formato_documentos/plataforma_LOGO.png\" style=\"width: 200px; height: 51px;\" /></td>\n			<td align=\"center\">\n				<span style=\"font-size:14px; font-weight:bold\"><span style=\"font-size:18px;\">COTIZACION DE VENTA</span></span></td>\n			<td>\n				<div style=\"text-align: left;\">\n					<strong><span style=\"font-size:12px;\">Codigo:&nbsp;<br />\n					Version: 1<br />\n					Vigencia:&nbsp;</span></strong></div>\n			</td>\n		</tr>\n		<tr>\n			<td align=\"center\" colspan=\"3\">\n				<span style=\"font-size:16px;\"><span style=\"font-weight: bold;\"><span style=\"background-color: rgb(255, 0, 0);\">[RAZON_SOCIAL]</span></span></span><br />\n				<span style=\"font-size:12px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\"><span style=\"font-size:12px;\"><span style=\"background-color: rgb(255, 0, 0);\">[TIPO_REGIMEN]</span>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[TIPO_IDENTIFICACION]</span>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[NUMERO_IDENTIFICACION]</span></span></span><br />\n				<strong>SUCURSAL:</strong>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[CV_SUCURSAL]</span>&nbsp;<br />\n				<strong>BODEGA:</strong>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[CV_BODEGA]</span>&nbsp;</span><br />\n				<span style=\"font-size:12px;\">&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[DIRECCION]</span>&nbsp;</span>&nbsp;<span style=\"font-size:14px;\"><span style=\"font-size:12px;\">CALI-COLOMBIA</span></span><br />\n				<span style=\"font-size:12px;\"><strong>TELEFONO:</strong>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[TELEFONO_EMPRESA]</span></span><br />\n				&nbsp;</td>\n		</tr>\n		<tr align=\"left\">\n			<td>\n				<span style=\"font-size:12px;\"><span style=\"font-weight: bold;\">FECHA SOLICITUD:<strong>&nbsp;</strong></span><strong><span style=\"background-color: rgb(255, 0, 0);\">[CV_FECHA_INICIAL]</span>&nbsp;</strong></span></td>\n			<td style=\"text-align: right;\">\n				<span style=\"font-size:12px;\"><span style=\"font-weight: bold;\">COTIZACION N.&nbsp;</span><strong><span style=\"background-color: rgb(255, 0, 0);\">[CV_CONSECUTIVO]</span>&nbsp;<span style=\"font-weight: bold;\">&nbsp;&nbsp;</span></strong></span></td>\n			<td style=\"text-align: right;\">\n				<span style=\"font-size:12px;\"><span style=\"font-weight: bold;\">Paginas({PAGENO} de {nb})</span></span></td>\n		</tr>\n	</tbody>\n</table>\n</htmlpageheader> <sethtmlpageheader name=\"MyHeader1\" show-this-page=\"1\" value=\"on\"></sethtmlpageheader> <span style=\"background-color: rgb(255, 0, 0);\">[CONTENIDO_DOCUMENTO]</span>\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"StyleTableCabecera\" style=\"font-size:10px\" width=\"740\">\n	<tbody>\n		<tr>\n			<td colspan=\"3\" height=\"60\">\n				&nbsp;</td>\n		</tr>\n		<tr>\n			<td align=\"center\" style=\"border-top:1px solid #000; font-size:14px\" width=\"350\">\n				Elaboro<br />\n				<span style=\"background-color: rgb(255, 0, 0);\">[CV_USUARIO]</span>&nbsp;<br />\n				<span style=\"background-color: rgb(255, 0, 0);\">[CV_CC_USUARIO]</span></td>\n			<td align=\"center\" width=\"40\">\n				&nbsp;</td>\n			<td align=\"center\" style=\"border-top:1px solid #000; font-size:14px\" width=\"350\">\n				Autorizacion Requisicion</td>\n		</tr>\n	</tbody>\n</table>\n<htmlpagefooter class=\"SoloPDF\" name=\"MyFooter1\">\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"StyleTableFooter\" style=\"font-size:10px\" width=\"740\">\n	<tbody>\n		<tr>\n			<td align=\"center\" width=\"400\">\n				<table cellpadding=\"1\" cellspacing=\"1\" class=\"StyleTablaEncuesta\" style=\"width: 400px;\">\n					<tbody>\n						<tr>\n							<td style=\"width: 250px;\">\n								<span style=\"font-size:12px;\"><strong>EVALUACION DE COMPRA</strong></span></td>\n							<td style=\"width: 50px;\">\n								<span style=\"font-size:12px;\"><strong>MARQUE (SI O NO)</strong></span></td>\n						</tr>\n						<tr>\n							<td>\n								<span style=\"font-size:12px;\">CUMPLE CON LAS ESPECIFICACIONES</span></td>\n							<td style=\"width: 100px;\">\n								&nbsp;</td>\n						</tr>\n						<tr>\n							<td>\n								<span style=\"font-size:12px;\">ESTADO FISICO DE LOS EQUIPOS</span></td>\n							<td>\n								&nbsp;</td>\n						</tr>\n						<tr>\n							<td>\n								<span style=\"font-size:12px;\">CALIDAD DEL EMPAQUE</span></td>\n							<td>\n								&nbsp;</td>\n						</tr>\n						<tr>\n							<td style=\"border-bottom: none;\">\n								<span style=\"font-size:12px;\">APROBADO PARA PAGO</span></td>\n							<td style=\"width: 100px; border-bottom: none;\">\n								&nbsp;</td>\n						</tr>\n					</tbody>\n				</table>\n			</td>\n			<td align=\"center\" width=\"250\">\n				<br />\n				<br />\n				<br />\n				<font size=\"3\"><b>_________________________________<br />\n				<span style=\"font-size:12px;\">Firma aceptacion</span></b></font></td>\n			<td style=\"border-left:1px solid;\" width=\"250\">\n				<span style=\"font-size:11px;\"><span style=\"font-weight: bold;\">&nbsp;Observacion Final</span></span><br />\n				<br />\n				<br />\n				<br />\n				&nbsp;</td>\n		</tr>\n	</tbody>\n</table>\n</htmlpagefooter> <sethtmlpagefooter name=\"MyFooter1\" value=\"on\"></sethtmlpagefooter> ',$id_empresa,'$razon_social','$id_sucursal'),
										('Pedido de Venta','PV','<style type=\"text/css\">\n.StyleTableHeader{\n		font-size		:10px;\n		font-family		:\"Segoe UI Light\",\"Helvetica Neue Light\",\"Segoe UI\",\"Helvetica Neue\",\"Trebuchet MS\",Helvetica,\"Droid Sans\",Tahoma,Geneva,sans-serif;\n		border			:1px solid #000;\nborder-collapse:collapse;\n	}\n	.StyleTableFooter{\n		font-size		:9px;\n		font-family		:\"Segoe UI Light\",\"Helvetica Neue Light\",\"Segoe UI\",\"Helvetica Neue\",\"Trebuchet MS\",Helvetica,\"Droid Sans\",Tahoma,Geneva,sans-serif;\n		border			:1px solid #000;\n	}\n.StyleTablaEncuesta td{ border-right:1px solid #000; border-bottom:1px solid #000;}\n.StyleTablaEncuesta{ border-collapse:collapse; border:none; }</style>\n<htmlpageheader class=\"SoloPDF\" name=\"MyHeader1\">\n<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" class=\"StyleTableHeader\" width=\"740\">\n	<tbody>\n		<tr>\n			<td align=\"center\">\n				<img alt=\"\" src=\"../../../../ARCHIVOS_PROPIOS/imagenes_empresas/empresa_1/formato_documentos/plataforma_LOGO.png\" style=\"width: 200px; height: 51px;\" /></td>\n			<td align=\"center\">\n				<span style=\"font-size:14px; font-weight:bold\"><span style=\"font-size:18px;\">PEDIDO DE VENTA</span></span></td>\n			<td>\n				<div style=\"text-align: left;\">\n					<strong><span style=\"font-size:12px;\">Codigo: COM-PR-01-F03<br />\n					Version: 1<br />\n					Vigencia:&nbsp;<span style=\"color: rgb(38, 38, 38); font-family: arial, sans-serif; line-height: 16px;\">2015-03-16</span></span></strong></div>\n			</td>\n		</tr>\n		<tr>\n			<td align=\"center\" colspan=\"3\">\n				<span style=\"font-size:16px;\"><span style=\"font-weight: bold;\"><span style=\"background-color: rgb(255, 0, 0);\">[RAZON_SOCIAL]</span></span></span><br />\n				<span style=\"font-size:12px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\"><span style=\"font-size:12px;\"><span style=\"background-color: rgb(255, 0, 0);\">[TIPO_REGIMEN]</span>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[TIPO_IDENTIFICACION]</span>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[NUMERO_IDENTIFICACION]</span></span></span><br />\n				<strong>SUCURSAL:</strong>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[PV_SUCURSAL]</span>&nbsp;<br />\n				<strong>BODEGA:</strong>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[PV_BODEGA]</span>&nbsp;</span><br />\n				<span style=\"font-size:12px;\">&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[DIRECCION]</span>&nbsp;</span>&nbsp;<span style=\"font-size:14px;\"><span style=\"font-size:12px;\">CALI-COLOMBIA</span></span><br />\n				<span style=\"font-size:12px;\"><strong>TELEFONO:</strong>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[TELEFONO_EMPRESA]</span></span><br />\n				&nbsp;</td>\n		</tr>\n		<tr align=\"left\">\n			<td>\n				<span style=\"font-size:12px;\"><span style=\"font-weight: bold;\">FECHA SOLICITUD:<strong>&nbsp;</strong></span><strong><span style=\"background-color: rgb(255, 0, 0);\">[PV_FECHA_INICIAL]</span>&nbsp;</strong></span></td>\n			<td style=\"text-align: right;\">\n				<span style=\"font-size:12px;\"><span style=\"font-weight: bold;\">PEDIDO N.&nbsp;</span><strong><span style=\"background-color: rgb(255, 0, 0);\">[PV_CONSECUTIVO]</span>&nbsp;<span style=\"font-weight: bold;\">&nbsp;&nbsp;</span></strong></span></td>\n			<td style=\"text-align: right;\">\n				<span style=\"font-size:12px;\"><span style=\"font-weight: bold;\">Paginas({PAGENO} de {nb})</span></span></td>\n		</tr>\n	</tbody>\n</table>\n</htmlpageheader> <sethtmlpageheader name=\"MyHeader1\" show-this-page=\"1\" value=\"on\"></sethtmlpageheader> <span style=\"background-color: rgb(255, 0, 0);\">[CONTENIDO_DOCUMENTO]</span>\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"StyleTableCabecera\" style=\"font-size:10px\" width=\"740\">\n	<tbody>\n		<tr>\n			<td colspan=\"3\" height=\"60\">\n				&nbsp;</td>\n		</tr>\n		<tr>\n			<td align=\"center\" style=\"border-top:1px solid #000; font-size:14px\" width=\"350\">\n				Elaboro<br />\n				<span style=\"background-color: rgb(255, 0, 0);\">[PV_USUARIO]</span>&nbsp;<br />\n				<span style=\"background-color: rgb(255, 0, 0);\">[PV_CC_USUARIO]</span></td>\n			<td align=\"center\" width=\"40\">\n				&nbsp;</td>\n			<td align=\"center\" style=\"border-top:1px solid #000; font-size:14px\" width=\"350\">\n				Autorizacion Requisicion</td>\n		</tr>\n	</tbody>\n</table>\n<htmlpagefooter class=\"SoloPDF\" name=\"MyFooter1\">\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"StyleTableFooter\" style=\"font-size:10px\" width=\"740\">\n	<tbody>\n		<tr>\n			<td align=\"center\" width=\"400\">\n				<table cellpadding=\"1\" cellspacing=\"1\" class=\"StyleTablaEncuesta\" style=\"width: 400px;\">\n					<tbody>\n						<tr>\n							<td style=\"width: 250px;\">\n								<span style=\"font-size:12px;\"><strong>EVALUACION DE COMPRA</strong></span></td>\n							<td style=\"width: 50px;\">\n								<span style=\"font-size:12px;\"><strong>MARQUE (SI O NO)</strong></span></td>\n						</tr>\n						<tr>\n							<td>\n								<span style=\"font-size:12px;\">CUMPLE CON LAS ESPECIFICACIONES</span></td>\n							<td style=\"width: 100px;\">\n								&nbsp;</td>\n						</tr>\n						<tr>\n							<td>\n								<span style=\"font-size:12px;\">ESTADO FISICO DE LOS EQUIPOS</span></td>\n							<td>\n								&nbsp;</td>\n						</tr>\n						<tr>\n							<td>\n								<span style=\"font-size:12px;\">CALIDAD DEL EMPAQUE</span></td>\n							<td>\n								&nbsp;</td>\n						</tr>\n						<tr>\n							<td style=\"border-bottom: none;\">\n								<span style=\"font-size:12px;\">APROBADO PARA PAGO</span></td>\n							<td style=\"width: 100px; border-bottom: none;\">\n								&nbsp;</td>\n						</tr>\n					</tbody>\n				</table>\n			</td>\n			<td align=\"center\" width=\"250\">\n				<br />\n				<br />\n				<br />\n				<font size=\"3\"><b>_________________________________<br />\n				<span style=\"font-size:12px;\">Firma aceptacion</span></b></font></td>\n			<td style=\"border-left:1px solid;\" width=\"250\">\n				<span style=\"font-size:11px;\"><span style=\"font-weight: bold;\">&nbsp;Observacion Final</span></span><br />\n				<br />\n				<br />\n				<br />\n				&nbsp;</td>\n		</tr>\n	</tbody>\n</table>\n</htmlpagefooter> <sethtmlpagefooter name=\"MyFooter1\" value=\"on\"></sethtmlpagefooter> ',$id_empresa,'$razon_social','$id_sucursal'),
										('Remision de Venta','RV','<style type=\"text/css\">\n.StyleTableHeader{\n		font-size		:10px;\n		font-family		:\"Segoe UI Light\",\"Helvetica Neue Light\",\"Segoe UI\",\"Helvetica Neue\",\"Trebuchet MS\",Helvetica,\"Droid Sans\",Tahoma,Geneva,sans-serif;\n		border			:1px solid #000;\nborder-collapse:collapse;\n	}\n	.StyleTableFooter{\n		font-size		:9px;\n		font-family		:\"Segoe UI Light\",\"Helvetica Neue Light\",\"Segoe UI\",\"Helvetica Neue\",\"Trebuchet MS\",Helvetica,\"Droid Sans\",Tahoma,Geneva,sans-serif;\n		border			:1px solid #000;\n	}\n.StyleTablaEncuesta td{ border-right:1px solid #000; border-bottom:1px solid #000;}\n.StyleTablaEncuesta{ border-collapse:collapse; border:none; }</style>\n<htmlpageheader class=\"SoloPDF\" name=\"MyHeader1\">\n<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" class=\"StyleTableHeader\" width=\"740\">\n	<tbody>\n		<tr>\n			<td align=\"center\">\n				<img alt=\"\" src=\"../../../../ARCHIVOS_PROPIOS/imagenes_empresas/empresa_1/formato_documentos/plataforma_LOGO.png\" style=\"width: 200px; height: 51px;\" /></td>\n			<td align=\"center\">\n				<span style=\"font-size:14px; font-weight:bold\"><span style=\"font-size:18px;\">REMISION DE VENTA</span></span></td>\n			<td>\n				<div style=\"text-align: left;\">\n					<strong><span style=\"font-size:12px;\">Codigo:<br />\n					Version:<br />\n					Vigencia:&nbsp;<span style=\"color: rgb(38, 38, 38); font-family: arial, sans-serif; line-height: 16px;\">2015-03-16</span></span></strong></div>\n			</td>\n		</tr>\n		<tr>\n			<td align=\"center\" colspan=\"3\">\n				<span style=\"font-size:16px;\"><span style=\"font-weight: bold;\"><span style=\"background-color: rgb(255, 0, 0);\">[RAZON_SOCIAL]</span></span></span><br />\n				<span style=\"font-size:12px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\"><span style=\"font-size:12px;\"><span style=\"background-color: rgb(255, 0, 0);\">[TIPO_REGIMEN]</span>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[TIPO_IDENTIFICACION]</span>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[NUMERO_IDENTIFICACION]</span></span></span><br />\n				<strong>SUCURSAL:</strong>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[RV_SUCURSAL]</span>&nbsp;<br />\n				<strong>BODEGA:</strong>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[RV_BODEGA]</span>&nbsp;</span><br />\n				<span style=\"font-size:12px;\">&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[DIRECCION]</span>&nbsp;</span>&nbsp;<span style=\"font-size:14px;\"><span style=\"font-size:12px;\">CALI-COLOMBIA</span></span><br />\n				<span style=\"font-size:12px;\"><strong>TELEFONO:</strong>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[TELEFONO_EMPRESA]</span></span><br />\n				&nbsp;</td>\n		</tr>\n		<tr align=\"left\">\n			<td>\n				<span style=\"font-size:12px;\"><span style=\"font-weight: bold;\">FECHA SOLICITUD:<strong>&nbsp;</strong></span><strong><span style=\"background-color: rgb(255, 0, 0);\">[RV_FECHA_INICIAL]</span>&nbsp;</strong></span></td>\n			<td style=\"text-align: right;\">\n				<span style=\"font-size:12px;\"><span style=\"font-weight: bold;\">REMISION N.&nbsp;</span><strong><span style=\"background-color: rgb(255, 0, 0);\">[RV_CONSECUTIVO]</span>&nbsp;<span style=\"font-weight: bold;\">&nbsp;&nbsp;</span></strong></span></td>\n			<td style=\"text-align: right;\">\n				<span style=\"font-size:12px;\"><span style=\"font-weight: bold;\">Paginas({PAGENO} de {nb})</span></span></td>\n		</tr>\n	</tbody>\n</table>\n</htmlpageheader> <sethtmlpageheader name=\"MyHeader1\" show-this-page=\"1\" value=\"on\"></sethtmlpageheader> <span style=\"background-color: rgb(255, 0, 0);\">[CONTENIDO_DOCUMENTO]</span>\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"StyleTableCabecera\" style=\"font-size:10px\" width=\"740\">\n	<tbody>\n		<tr>\n			<td colspan=\"3\" height=\"60\">\n				&nbsp;</td>\n		</tr>\n		<tr>\n			<td align=\"center\" style=\"border-top:1px solid #000; font-size:14px\" width=\"350\">\n				Elaboro<br />\n				<span style=\"background-color: rgb(255, 0, 0);\">[RV_USUARIO]</span>&nbsp;<br />\n				<span style=\"background-color: rgb(255, 0, 0);\">[RV_CC_USUARIO]</span></td>\n			<td align=\"center\" width=\"40\">\n				&nbsp;</td>\n			<td align=\"center\" style=\"border-top:1px solid #000; font-size:14px\" width=\"350\">\n				Autorizacion Requisicion</td>\n		</tr>\n	</tbody>\n</table>\n<htmlpagefooter class=\"SoloPDF\" name=\"MyFooter1\">\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"StyleTableFooter\" style=\"font-size:10px\" width=\"740\">\n	<tbody>\n		<tr>\n			<td align=\"center\" width=\"400\">\n				<table cellpadding=\"1\" cellspacing=\"1\" class=\"StyleTablaEncuesta\" style=\"width: 400px;\">\n					<tbody>\n						<tr>\n							<td style=\"width: 250px;\">\n								<span style=\"font-size:12px;\"><strong>EVALUACION DE COMPRA</strong></span></td>\n							<td style=\"width: 50px;\">\n								<span style=\"font-size:12px;\"><strong>MARQUE (SI O NO)</strong></span></td>\n						</tr>\n						<tr>\n							<td>\n								<span style=\"font-size:12px;\">CUMPLE CON LAS ESPECIFICACIONES</span></td>\n							<td style=\"width: 100px;\">\n								&nbsp;</td>\n						</tr>\n						<tr>\n							<td>\n								<span style=\"font-size:12px;\">ESTADO FISICO DE LOS EQUIPOS</span></td>\n							<td>\n								&nbsp;</td>\n						</tr>\n						<tr>\n							<td>\n								<span style=\"font-size:12px;\">CALIDAD DEL EMPAQUE</span></td>\n							<td>\n								&nbsp;</td>\n						</tr>\n						<tr>\n							<td style=\"border-bottom: none;\">\n								<span style=\"font-size:12px;\">APROBADO PARA PAGO</span></td>\n							<td style=\"width: 100px; border-bottom: none;\">\n								&nbsp;</td>\n						</tr>\n					</tbody>\n				</table>\n			</td>\n			<td align=\"center\" width=\"250\">\n				<br />\n				<br />\n				<br />\n				<font size=\"3\"><b>_________________________________<br />\n				<span style=\"font-size:12px;\">Firma aceptacion</span></b></font></td>\n			<td style=\"border-left:1px solid;\" width=\"250\">\n				<span style=\"font-size:11px;\"><span style=\"font-weight: bold;\">&nbsp;Observacion Final</span></span><br />\n				<br />\n				<br />\n				<br />\n				&nbsp;</td>\n		</tr>\n	</tbody>\n</table>\n</htmlpagefooter> <sethtmlpagefooter name=\"MyFooter1\" value=\"on\"></sethtmlpagefooter> ',$id_empresa,'$razon_social','$id_sucursal'),
										('Factura de Venta','FV','<style type=\"text/css\">\n.StyleTableHeader{\n		font-size		:10px;\n		font-family		:\"Segoe UI Light\",\"Helvetica Neue Light\",\"Segoe UI\",\"Helvetica Neue\",\"Trebuchet MS\",Helvetica,\"Droid Sans\",Tahoma,Geneva,sans-serif;\n		border			:1px solid #000;\nborder-collapse:collapse;\n	}\n	.StyleTableFooter{\n		font-size		:9px;\n		font-family		:\"Segoe UI Light\",\"Helvetica Neue Light\",\"Segoe UI\",\"Helvetica Neue\",\"Trebuchet MS\",Helvetica,\"Droid Sans\",Tahoma,Geneva,sans-serif;\n		border			:1px solid #000;\n	}</style>\n<htmlpageheader class=\"SoloPDF\" name=\"MyHeader1\">\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"StyleTableHeader\" width=\"740\">\n	<tbody>\n		<tr>\n			<td align=\"center\">\n				<img alt=\"\" src=\"../../../../ARCHIVOS_PROPIOS/imagenes_empresas/empresa_1/formato_documentos/plataforma_LOGO.png\" style=\"width: 300px; height: 51px;\" /></td>\n			<td align=\"center\">\n				<span style=\"font-size:18px; font-weight:bold\">FACTURA DE VENTA</span><br />\n				<span style=\"font-size:22px; font-weight:bold\">No.<span style=\"background-color: rgb(255, 0, 0);\">[FV_NUMERO_FACTURA]</span></span></td>\n		</tr>\n		<tr>\n			<td align=\"center\">\n				<span style=\"font-size:18px; font-weight:bold\"><span style=\"background-color: rgb(255, 0, 0);\">[RAZON_SOCIAL]</span></span><br />\n				<span style=\"font-size:18px;\"><span style=\"background-color: rgb(255, 0, 0);\">[TIPO_REGIMEN]</span>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[TIPO_IDENTIFICACION]</span>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[NUMERO_IDENTIFICACION]</span></span><br />\n				<span style=\"font-size:14px; font-weight:bold\"><b>CALI OFICINA PRINCIPAL</b></span><br />\n				<span style=\"font-size:14px; font-weight:bold\">CALLE 3 No. 60-46 BARRIO PAMPALINDA</span><br />\n				<span style=\"font-size:14px; font-weight:bold\">CALI - VALLE</span></td>\n			<td align=\"center\">\n				<span style=\"font-size:14px;\">Paginas({PAGENO} de {nb})</span><br />\n				<br />\n				ACTIVIDAD ECONOMICA <span style=\"background-color: rgb(255, 0, 0);\">[ACTIVIDAD_ECONOMICA]</span><br />\n				RETENEDOR IVA A REGIMEN SIMPLIFICADO<br />\n				REGIMEN COMUN</td>\n		</tr>\n		<tr>\n			<td align=\"center\" colspan=\"2\">\n				AUTORIZACION DE FACTURACION No&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[FV_NUMERO_RESOLUCION_DIAN]</span>&nbsp;DE&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[FV_FECHA_RESOLUCION_DIAN]</span>&nbsp; &nbsp;DEL&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[FV_PREFIJO_RESOLUCION_DIAN]</span>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[FV_NUMERO_INICIAL_RESOLUCION]</span>&nbsp;HASTA&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[FV_PREFIJO_RESOLUCION_DIAN]</span>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[FV_NUMERO_FINAL_RESOLUCION]</span>&nbsp; - &nbsp;FACTURA IMPRESA POR COMPUTADOR</td>\n		</tr>\n	</tbody>\n</table>\n</htmlpageheader> <sethtmlpageheader name=\"MyHeader1\" show-this-page=\"1\" value=\"on\"></sethtmlpageheader> <span style=\"background-color: rgb(255, 0, 0);\">[CONTENIDO_DOCUMENTO]</span>\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"StyleTableCabecera\" style=\"font-size:10px\" width=\"740\">\n	<tbody>\n		<tr>\n			<td colspan=\"3\" height=\"60\">\n				&nbsp;</td>\n		</tr>\n		<tr>\n			<td align=\"center\" style=\"border-top:1px solid #000; font-size:14px\" width=\"350\">\n				Vendedor <span style=\"background-color: rgb(255, 0, 0);\">[FV_VENDEDOR]</span></td>\n			<td align=\"center\" width=\"40\">\n				&nbsp;</td>\n			<td align=\"center\" style=\"border-top:1px solid #000; font-size:14px\" width=\"350\">\n				Aceptacion de la factura<br />\n				Nombre legible de quien recibe y sello</td>\n		</tr>\n	</tbody>\n</table>\n<htmlpagefooter class=\"SoloPDF\" name=\"MyFooter1\">\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"StyleTableFooter\" style=\"font-size:10px\" width=\"740\">\n	<tbody>\n		<tr>\n			<td align=\"center\" width=\"488\">\n				ESTA FACTURA DE VENTA SE PAGARA A LA ORDEN DE&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[RAZON_SOCIAL]</span>&nbsp;<br />\n				A PARTIR DEL VENCIMIENTO SE CAUSARAN INTERESES DE MORA A LA TASA MAXIMA LEGAL VIGENTE<br />\n				FAVOR GIRAR CHEQUE CRUZADO Y ENVIAR EL COMPROBANTE DE PAGO AL DEPARTAMENTO DE CARTERA<br />\n				&nbsp;</td>\n			<td align=\"center\" width=\"250\">\n				<span style=\"font-size:16px; font-weight:bold\">PAGOS POR CONSIGNACION</span><br />\n				<span style=\"font-size:12px;\">BBVA CTA.CTE.397009739<br />\n				BANCOLOMBIA CTA.CTE.381-239127-37<br />\n				BANCOLOMBIA CTA.AH.3001-5259857<br />\n				BCO. BOGOTA CTA.CTE.249243387 </span></td>\n		</tr>\n		<tr>\n			<td colspan=\"2\">\n				&nbsp;</td>\n		</tr>\n	</tbody>\n</table>\n</htmlpagefooter> <sethtmlpagefooter name=\"MyFooter1\" value=\"on\"></sethtmlpagefooter> ',$id_empresa,'$razon_social','$id_sucursal'),
										('Orden de Compra', 'OC', '<style type=\"text/css\">\n.StyleTableHeader{\n		font-size		:10px;\n		font-family		:\"Segoe UI Light\",\"Helvetica Neue Light\",\"Segoe UI\",\"Helvetica Neue\",\"Trebuchet MS\",Helvetica,\"Droid Sans\",Tahoma,Geneva,sans-serif;\n		border			:1px solid #000;\nborder-collapse:collapse;\n	}\n	.StyleTableFooter{\n		font-size		:9px;\n		font-family		:\"Segoe UI Light\",\"Helvetica Neue Light\",\"Segoe UI\",\"Helvetica Neue\",\"Trebuchet MS\",Helvetica,\"Droid Sans\",Tahoma,Geneva,sans-serif;\n		border			:1px solid #000;\n	}\n.StyleTablaEncuesta td{ border-right:1px solid #000; border-bottom:1px solid #000;}\n.StyleTablaEncuesta{ border-collapse:collapse; border:none; }</style>\n<htmlpageheader class=\"SoloPDF\" name=\"MyHeader1\">\n<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" class=\"StyleTableHeader\" width=\"740\">\n	<tbody>\n		<tr>\n			<td align=\"center\">\n				<img alt=\"\" src=\"../../../../ARCHIVOS_PROPIOS/imagenes_empresas/empresa_1/formato_documentos/plataforma_LOGO.png\" style=\"width: 200px; height: 51px;\" /></td>\n			<td align=\"center\">\n				<span style=\"font-size:14px; font-weight:bold\"><span style=\"font-size:18px;\">ORDEN DE COMPRA</span> </span></td>\n			<td>\n				<div style=\"text-align: left;\">\n					<strong><span style=\"font-size:12px;\">Codigo: COM-PR-01-F03<br />\n					Version: 1<br />\n					Vigencia:&nbsp;<span style=\"color: rgb(38, 38, 38); font-family: arial, sans-serif; line-height: 16px;\">2015-03-16</span></span></strong></div>\n			</td>\n		</tr>\n		<tr>\n			<td align=\"center\" colspan=\"3\">\n				<span style=\"font-size:16px;\"><span style=\"font-weight: bold;\"><span style=\"background-color: rgb(255, 0, 0);\">[RAZON_SOCIAL]</span></span></span><br />\n				<span style=\"font-size:12px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\"><span style=\"font-size:12px;\"><span style=\"background-color: rgb(255, 0, 0);\">[TIPO_REGIMEN]</span>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[TIPO_IDENTIFICACION]</span>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[NUMERO_IDENTIFICACION]</span></span></span><br />\n				<strong>SUCURSAL:</strong>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[OC_SUCURSAL]</span>&nbsp;<br />\n				<strong>BODEGA:</strong>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[OC_BODEGA]</span>&nbsp;</span><br />\n				<span style=\"font-size:12px;\">&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[DIRECCION]</span>&nbsp;</span>&nbsp;<span style=\"font-size:14px;\"><span style=\"font-size:12px;\">CALI-COLOMBIA</span></span><br />\n				<span style=\"font-size:12px;\"><strong>TELEFONO:</strong>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[TELEFONO_EMPRESA]</span></span><br />\n				&nbsp;</td>\n		</tr>\n		<tr align=\"left\">\n			<td>\n				<span style=\"font-size:12px;\"><span style=\"font-weight: bold;\">FECHA SOLICITUD:<strong>&nbsp;</strong></span><strong><span style=\"background-color: rgb(255, 0, 0);\">[OC_FECHA_INICIAL]</span>&nbsp;</strong></span></td>\n			<td style=\"text-align: right;\">\n				<span style=\"font-size:12px;\"><span style=\"font-weight: bold;\">ORDEN N.&nbsp;</span><strong><span style=\"background-color: rgb(255, 0, 0);\">[OC_CONSECUTIVO]</span>&nbsp;<span style=\"font-weight: bold;\">&nbsp;&nbsp;</span></strong></span></td>\n			<td style=\"text-align: right;\">\n				<span style=\"font-size:12px;\"><span style=\"font-weight: bold;\">Paginas({PAGENO} de {nb})</span></span></td>\n		</tr>\n	</tbody>\n</table>\n</htmlpageheader> <sethtmlpageheader name=\"MyHeader1\" show-this-page=\"1\" value=\"on\"></sethtmlpageheader> <span style=\"background-color: rgb(255, 0, 0);\">[CONTENIDO_DOCUMENTO]</span>\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"StyleTableCabecera\" style=\"font-size:10px\" width=\"740\">\n	<tbody>\n		<tr>\n			<td colspan=\"3\" height=\"60\">\n				&nbsp;</td>\n		</tr>\n		<tr>\n			<td align=\"center\" style=\"border-top:1px solid #000; font-size:14px\" width=\"350\">\n				Elaboro<br />\n				<span style=\"background-color: rgb(255, 0, 0);\">[OC_USUARIO]</span>&nbsp;<br />\n				<span style=\"background-color: rgb(255, 0, 0);\">[OC_CC_USUARIO]</span></td>\n			<td align=\"center\" width=\"40\">\n				&nbsp;</td>\n			<td align=\"center\" style=\"border-top:1px solid #000; font-size:14px\" width=\"350\">\n				Autorizacion Requisicion</td>\n		</tr>\n	</tbody>\n</table>\n<htmlpagefooter class=\"SoloPDF\" name=\"MyFooter1\">\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"StyleTableFooter\" style=\"font-size:10px\" width=\"740\">\n	<tbody>\n		<tr>\n			<td align=\"center\" width=\"400\">\n				<table cellpadding=\"1\" cellspacing=\"1\" class=\"StyleTablaEncuesta\" style=\"width: 400px;\">\n					<tbody>\n						<tr>\n							<td style=\"width: 250px;\">\n								<span style=\"font-size:12px;\"><strong>EVALUACION DE COMPRA</strong></span></td>\n							<td style=\"width: 50px;\">\n								<span style=\"font-size:12px;\"><strong>MARQUE (SI O NO)</strong></span></td>\n						</tr>\n						<tr>\n							<td>\n								<span style=\"font-size:12px;\">CUMPLE CON LAS ESPECIFICACIONES</span></td>\n							<td style=\"width: 100px;\">\n								&nbsp;</td>\n						</tr>\n						<tr>\n							<td>\n								<span style=\"font-size:12px;\">ESTADO FISICO DE LOS EQUIPOS</span></td>\n							<td>\n								&nbsp;</td>\n						</tr>\n						<tr>\n							<td>\n								<span style=\"font-size:12px;\">CALIDAD DEL EMPAQUE</span></td>\n							<td>\n								&nbsp;</td>\n						</tr>\n						<tr>\n							<td style=\"border-bottom: none;\">\n								<span style=\"font-size:12px;\">APROBADO PARA PAGO</span></td>\n							<td style=\"width: 100px; border-bottom: none;\">\n								&nbsp;</td>\n						</tr>\n					</tbody>\n				</table>\n			</td>\n			<td align=\"center\" width=\"250\">\n				<br />\n				<br />\n				<br />\n				<font size=\"3\"><b>_________________________________<br />\n				<span style=\"font-size:12px;\">Firma aceptacion</span></b></font></td>\n			<td style=\"border-left:1px solid;\" width=\"250\">\n				<span style=\"font-size:11px;\"><span style=\"font-weight: bold;\">&nbsp;Observacion Final</span></span><br />\n				<br />\n				<br />\n				<br />\n				&nbsp;</td>\n		</tr>\n	</tbody>\n</table>\n</htmlpagefooter> <sethtmlpagefooter name=\"MyFooter1\" value=\"on\"></sethtmlpagefooter> ', '$id_empresa', '$razon_social', '$id_sucursal'),
										('Factura de Compra', 'FC', '<style type=\"text/css\">\n.StyleTableHeader{\n	font-size		:10px;\n		font-family		:\"Segoe UI Light\",\"Helvetica Neue Light\",\"Segoe UI\",\"Helvetica Neue\",\"Trebuchet MS\",Helvetica,\"Droid Sans\",Tahoma,Geneva,sans-serif;\n		border			:1px solid #000;\nborder-collapse:collapse;\n	}\n	.StyleTableFooter{\n		font-size		:9px;\n		font-family		:\"Segoe UI Light\",\"Helvetica Neue Light\",\"Segoe UI\",\"Helvetica Neue\",\"Trebuchet MS\",Helvetica,\"Droid Sans\",Tahoma,Geneva,sans-serif;\n		border			:1px solid #000;\n	}\n.StyleTableFooter td{ border-right:1px solid #000; border-bottom:1px solid #000; padding-left: 5px; }</style>\n<htmlpageheader class=\"SoloPDF\" name=\"MyHeader1\">\n<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" class=\"StyleTableHeader\" width=\"740\">\n	<tbody>\n		<tr>\n			<td align=\"center\">\n				<img alt=\"\" src=\"../../../../../ARCHIVOS_PROPIOS/imagenes_empresas/empresa_1/formato_documentos/plataforma_LOGO.png\" style=\"width: 200px; height: 51px;\" /></td>\n			<td align=\"center\">\n				<span style=\"font-size:14px; font-weight:bold\"><span style=\"font-size:18px;\">FACTURA DE COMPRA</span> </span></td>\n			<td>\n				<div style=\"text-align: left;\">\n					<strong><span style=\"font-size:12px;\">&nbsp;Codigo: COM-PR-01-F03<br />\n					&nbsp;Version: 1<br />\n					&nbsp;Vigencia:&nbsp;<span style=\"color: rgb(38, 38, 38); font-family: arial, sans-serif; line-height: 16px;\">2015-03-16</span></span></strong></div>\n			</td>\n		</tr>\n		<tr>\n			<td align=\"center\" colspan=\"3\">\n				<span style=\"font-size:16px;\"><span style=\"font-weight: bold;\"><span style=\"background-color: rgb(255, 0, 0);\">[RAZON_SOCIAL]</span></span></span><br />\n				<span style=\"font-size:12px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\"><span style=\"font-size:12px;\"><span style=\"background-color: rgb(255, 0, 0);\">[TIPO_REGIMEN]</span>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[TIPO_IDENTIFICACION]</span>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[NUMERO_IDENTIFICACION]</span></span></span><br />\n				<strong>SUCURSAL:</strong>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[FC_SUCURSAL]</span>&nbsp;<br />\n				<span style=\"font-size:12px;\">&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[DIRECCION]</span>&nbsp;</span>&nbsp;<span style=\"font-size:14px;\"><span style=\"font-size:12px;\">BOGOTA-COLOMBIA</span></span><br />\n				<span style=\"font-size:12px;\"><strong>TELEFONO:</strong>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[TELEFONO_EMPRESA]</span></span><br />\n				&nbsp;</span></td>\n		</tr>\n		<tr align=\"left\">\n			<td>\n				<span style=\"font-size:11px;\"><strong><span style=\"font-weight: bold;\">&nbsp;FECHA SOLICITUD:&nbsp;</span></strong><span style=\"background-color: rgb(255, 0, 0);\">[FC_FECHA_EMISION]</span>&nbsp;<br />\n				<strong>&nbsp;FECHA DE VENCIMIENTO:&nbsp;</strong><span style=\"background-color: rgb(255, 0, 0);\">[FC_FECHA_VENCIMIENTO]</span>&nbsp;</span></td>\n			<td>\n				<span style=\"font-size:11px;\"><strong><span style=\"font-weight: bold;\">&nbsp;FC N.&nbsp;</span></strong><span style=\"background-color: rgb(255, 0, 0);\">[FC_PREFIJO]</span>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[FC_NUMERO]</span>&nbsp;<strong style=\"font-size: 12px;\">&nbsp;</strong><br />\n				<strong><span style=\"font-weight: bold;\">&nbsp;CONSECUTIVO N.</span></strong>&nbsp;<span style=\"background-color: rgb(255, 0, 0);\">[FC_CONSECUTIVO]</span>&nbsp;&nbsp;&nbsp;</span></td>\n			<td style=\"text-align: right;\">\n				<span style=\"font-size:12px;\"><span style=\"font-weight: bold;\">Paginas({PAGENO} de {nb})</span></span></td>\n		</tr>\n	</tbody>\n</table>\n</htmlpageheader> <sethtmlpageheader name=\"MyHeader1\" show-this-page=\"1\" value=\"on\"></sethtmlpageheader> <span style=\"background-color: rgb(255, 0, 0);\">[CONTENIDO_DOCUMENTO]</span>\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"StyleTableCabecera\" style=\"font-size:10px\" width=\"740\">\n	<tbody>\n		<tr>\n			<td colspan=\"3\" height=\"60\">\n				&nbsp;</td>\n		</tr>\n		<tr>\n			<td align=\"center\" style=\"border-top:1px solid #000; font-size:14px\" width=\"350\">\n				Elaboro<br />\n				<span style=\"background-color: rgb(255, 0, 0);\">[OC_USUARIO]</span>&nbsp;<br />\n				<span style=\"background-color: rgb(255, 0, 0);\">[OC_CC_USUARIO]</span></td>\n			<td align=\"center\" width=\"40\">\n				&nbsp;</td>\n			<td align=\"center\" style=\"font-size:14px\" width=\"350\">\n				&nbsp;</td>\n		</tr>\n	</tbody>\n</table>\n<htmlpagefooter class=\"SoloPDF\" name=\"MyFooter1\"></htmlpagefooter><br />\n<sethtmlpagefooter name=\"MyFooter1\" value=\"on\"></sethtmlpagefooter>', '$id_empresa', 'PLATAFORMA COLOMBIA S.A.S.   ', '$id_sucursal');";
			$queryConfiguracionDocumento=mysql_query($sqlConfiguracionDocumento,$link);
			if (!$queryConfiguracionDocumento) { if (isset($id_host)) { deshacer_registro($id_host,$bd,'NO SE INSERTO EL DOCUMENTO POR DEFECTO FV PARA LA CONFIGURACION <br/>',$acceso_server); }else{deleteInfoEmpresa($link,$id_empresa,"NO SE INSERTO EL DOCUMENTO POR DEFECTO FV PARA LA CONFIGURACION <br/>",1);} }

			// INSERTAR LA CUENTA DEL COMPROBANTE DE EGRESO POR DEFECTO
			$sqlConfigConprobante   = "INSERT INTO configuracion_comprobante_egreso (cuenta,descripcion,id_empresa) VALUES ('2','PASIVOS',$id_empresa)";
			$queryConfigConprobante = mysql_query($sqlConfigConprobante,$link);
			if (!$queryConfigConprobante) { if (isset($id_host)) { deshacer_registro($id_host,$bd,'NO SE INSERTO LA CUENTA DE CONFIGURACION DEL COMPROBANTE DE EGRESO POR DEFECTO <br/>',$acceso_server); }else{deleteInfoEmpresa($link,$id_empresa,"NO SE INSERTO LA CUENTA DE CONFIGURACION DEL COMPROBANTE DE EGRESO POR DEFECTO <br/>",1);} }

			// INSERTAR EL TIPO DE PAGO DE LA NOMINA
			$sqlTipoPagoNomina = "INSERT INTO nomina_tipos_liquidacion (nombre,dias,id_empresa) VALUES ('NOMINA QUINCENAL','15',$id_empresa) ";
			$queryTipoNomina   = mysql_query($sqlTipoPagoNomina,$link);
			if (!$queryTipoNomina) { if (isset($id_host)) { deshacer_registro($id_host,$bd,'NO SE INSERTO EL TIPO DE PAGO DE NOMINA <br/>',$acceso_server); }else{deleteInfoEmpresa($link,$id_empresa,"NO SE INSERTO EL TIPO DE PAGO DE NOMINA <br/>",1);} }


			// INSERTAR LOS GRUPOS DE CONCEPTOS DE NOMINA CON LOS CONCEPTOS Y SUS FORMULAS Y CUENTAS
			include_once ('configuraciones/conceptos_nomina.php');

			// INSERTAR EL TIPO DE CONTRATO DE LA NOMINA
			$sqlTipoPagoNomina = "INSERT INTO nomina_tipo_contrato (descripcion,dias,id_empresa)
									VALUES
									('TERMINO INDEFINIDO','0',$id_empresa),
								 	('TERMINO FIJO','365',$id_empresa),
								 	('OBRA O LABOR','365',$id_empresa),
								 	('TEMPORAL','365',$id_empresa),
								 	('APRENDIZ SENA ETAPA LECTIVA','365',$id_empresa),
								 	('APRENDIZ SENA ETAPA PRODUCTIVA','180',$id_empresa),
								 	('PRACTICANTE UNIVERSITARIO','180',$id_empresa)";
			$queryTipoNomina   = mysql_query($sqlTipoPagoNomina,$link);
			if (!$queryTipoNomina) { if (isset($id_host)) { deshacer_registro($id_host,$bd,'NO SE INSERTO EL TIPO DE CONTRATO DE NOMINA <br/>',$acceso_server); }else{deleteInfoEmpresa($link,$id_empresa,"NO SE INSERTO EL TIPO DE CONTRATO DE NOMINA <br/>",1);} }

			// INSERTAR LOS GRUPOS DE TRABAJO DE LA NOMINA
			$sqlTipoPagoNomina = "INSERT INTO nomina_grupos_trabajo (nombre,id_empresa)
									VALUES
									('ADMINISTRACION',$id_empresa),
								 	('VENTAS',$id_empresa),
								 	('PRODUCCION',$id_empresa) ";
			$queryTipoNomina   = mysql_query($sqlTipoPagoNomina,$link);
			if (!$queryTipoNomina) { if (isset($id_host)) { deshacer_registro($id_host,$bd,'NO SE INSERTARON LOS GRUPOS DE TRABAJO DE NOMINA <br/>',$acceso_server); }else{deleteInfoEmpresa($link,$id_empresa,"NO SE INSERTARON LOS GRUPOS DE TRABAJO DE NOMINA <br/>",1);} }


			// INSERTAR LOS NIVELES DE RIESGO LABORAL DE LA NOMINA
			$sqlTipoPagoNomina = "INSERT INTO nomina_niveles_riesgos_laborales (nombre,porcentaje,id_empresa)
									VALUES
									('RIESGO 1  (0.522%)',0.522,$id_empresa),
									('RIESGO 2 (1.044%)',1.044,$id_empresa),
									('RIESGO 3 (2.436%)',2.436,$id_empresa),
									('RIESGO 4 (4.35%)',4.350,$id_empresa),
									('RIESGO 5 (6.96%)',6.960,$id_empresa)";
			$queryTipoNomina   = mysql_query($sqlTipoPagoNomina,$link);
			if (!$queryTipoNomina) { if (isset($id_host)) { deshacer_registro($id_host,$bd,'NO SE INSERTARON LOS NIVELES DE RIESGO LABORAL DE NOMINA <br/>',$acceso_server); }else{deleteInfoEmpresa($link,$id_empresa,"NO SE INSERTARON LOS NIVELES DE RIESGO LABORAL DE NOMINA <br/>",1);} }
		}

		if($nameFileUpload != ''){ unlink($_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/documentos_crear_empresa/'.$nameFileUpload); }

		// CONFIGURACION DEL CERTIFICADO DE INGRESOS Y RETENCIONES DE LOS EMPLEADOS
		$sql   = "INSERT INTO certificado_ingreso_retenciones_empleados_secciones (id, nombre, nombre_total, codigo_total, id_empresa)
					VALUES
					(1,'Concepto de los Ingresos', 'Total de ingresos brutos (Sume casillas 37 a 41)', '42', $id_empresa),
					(2,'Concepto de los aportes', 'Valor de la retención en la fuente por salarios y demás pagos laborales', '46', $id_empresa)";
		$query = mysql_query($sql,$link);
		if (!$query) { if (isset($id_host)) { deshacer_registro($id_host,$bd,'NO SE INSERTO LA CONFIGURACION DEL CERTIFICADO DE INGRESOS Y RETENCIONES <br/>',$acceso_server); }else{deleteInfoEmpresa($link,$id_empresa,"NO SE INSERTO LA CONFIGURACION DEL CERTIFICADO DE INGRESOS Y RETENCIONES <br/>",1);} }

		$sql   = "INSERT INTO certificado_ingreso_retenciones_empleados_secciones_filas (id_seccion, nombre, codigo, id_empresa)
					VALUES
						('1', 'Pagos al empleado  (No incluya valores de las casillas 38 a 41)', '37', $id_empresa),
						('1', 'Cesantias e intereses de cesantias efectivamente pagadas en el periodo', '38', $id_empresa),
						('1', 'Gastos de representacion', '39', $id_empresa),
						('1', 'Pensiones de jubilacion, vejez o invalidez', '40', $id_empresa),
						('1', 'Otros ingresos como empleado', '41', $id_empresa),
						('2', 'Aportes obligatorios por salud', '43', $id_empresa),
						('2', 'Aportes obligatorios a fondos de pensiones y solidaridad pensional', '44', $id_empresa),
						('2', 'Aportes voluntarios, a fondos de pensiones y cuentas AFC', '45', $id_empresa)";
		$query = mysql_query($sql,$link);
		if (!$query) { if (isset($id_host)) { deshacer_registro($id_host,$bd,'NO SE INSERTO LA CONFIGURACION DE FILAS DEL CERTIFICADO DE INGRESOS Y RETENCIONES <br/>',$acceso_server); }else{deleteInfoEmpresa($link,$id_empresa,"NO SE INSERTO LA CONFIGURACION DE FILAS DEL CERTIFICADO DE INGRESOS Y RETENCIONES <br/>",1);} }



		echo 'true'; exit;
	}

	function deleteInfoEmpresa($link,$id_empresa,$msjError,$contError){
		$sqlDelete   = "DELETE FROM empresas WHERE id='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 1){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM empresas_sucursales WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 2){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM asientos_colgaap_default WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 3){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM asientos_niif_default WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 4){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM puc WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 5){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM puc_niif WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 6){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM nota_contable_general WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 7){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM nota_contable_general_cuentas WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 8){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM tipo_nota_contable WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 9){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM impuestos WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 10){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM retenciones WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 11){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM empleados_roles WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 12){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM empleados_cargos WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 13){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM empleados_roles_permisos WHERE id_rol='$idRolAdmin'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 14){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM tipo_documento WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 15){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM empleados WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 16){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM empleados_tipo_documento WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 17){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM configuracion_formas_pago WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 18){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM configuracion_cuentas_pago WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 19){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM puc_configuracion WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 20){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM empresas_sucursales_bodegas WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 21){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM empleados_contratos WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 22){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM inventario_unidades WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 23){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM ventas_pos_configuracion_consecutivos WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 24){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM configuracion_sector_empresarial WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 25){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM terceros_tratamiento WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 26){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM terceros_tipo_documento WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 27){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM configuracion_consecutivos_documentos WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 28){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM items_familia WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 29){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM items_familia_grupo WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 30){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM items_familia_grupo_subgrupo WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 31){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM centro_costos WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 32){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM centro_costos WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 33){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM variables_grupos WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 34){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM variables WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 35){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM configuracion_documentos_erp WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 36){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM configuracion_comprobante_egreso WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 37){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM nomina_tipos_liquidacion WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 38){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM nomina_grupos_conceptos WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 39){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM nomina_grupos_conceptos WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 40){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM nomina_tipo_contrato WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 41){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM nomina_grupos_trabajo WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 42){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM nomina_niveles_riesgos_laborales WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 43){ echo $msjError; exit; }

		$sqlDelete   = "DELETE FROM nomina_conceptos_base_liquidacion WHERE id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ $msjError .= $sqlDelete; }
		if($contError == 44){ echo $msjError; exit; }
	}

	function deshacer_registro($id_host,$bd,$mensaje,$acceso_server){
		$sql   = "DELETE FROM erp_bd.host WHERE activo=1 AND id=$id_host";
		$query = mysql_query($sql,$acceso_server);

		$sql   = "DELETE FROM erp_acceso.host WHERE activo=1 AND id=$id_host";
		$query = mysql_query($sql,$acceso_server);

		$sql   = "DROP DATABASE $bd";
		$query = mysql_query($sql,$acceso_server);

		echo $mensaje;
		exit;
	}

?>
