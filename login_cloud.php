<?php
	error_reporting (E_ERROR | E_PARSE);
	// $origin = $_SERVER['HTTP_ORIGIN'];
 //    $allowed_domains = [
 //        'http://soporte.logicalsoft.co',
 //        'http://cloud.logicalsoft.co',
 //    ];
 //    if (in_array($origin, $allowed_domains)) {
 //        header('Access-Control-Allow-Origin: ' . $origin);
 //        // header('Access-Control-Allow-Origin: *');
 //    }
	$token   = $_POST['token'];
	$support = $_POST['support'];
	$id      = ($support=='true')? $_POST['id_usuario'] : $_POST['id'];

	$urlCloud = 'http://cloud.logicalsoft.co';//Url de LogicalSoft Cloud
	$url      = $urlCloud.'/api/users/' . $id . '/?token=' . $token . '&id=' . $id;
	$data     = array("token" => $token, "id" => $id);
	$header   = array("Authorization:Bearer " . $token);

    //Seguridad: Reenvia el token para validadr si coincide
    $data    = json_encode($data);
    $client  = curl_init();
    $options = array(
        CURLOPT_URL            => $url,
        CURLOPT_CUSTOMREQUEST  => "GET",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => false,
        CURLOPT_HTTPHEADER     => $header,
        CURLOPT_POSTFIELDS     => $data,
    );

    curl_setopt_array($client, $options);
    $response = curl_exec($client);
    $httpCode = curl_getinfo($client, CURLINFO_HTTP_CODE);
    curl_close($client);
    $respuesta = json_decode($response);

    // var_dump($respuesta);
    // echo '<br>'.$token;
	if ($respuesta->response == true) {

		include_once('misc/ConnectDb/class.ConnectDb.php');

		$nit_empresa = $_POST['nit'];
		$username    = $_POST['username'];

		// $nit_empresa = $_GET['nit_empresa'];
		// $username    = $_GET['username'];

		// DESARROLLO
		// $conexionDB = '192.168.8.202';
		// $user       = 'root';
		// $pass       = 'serverchkdsk';
		// $bd         = 'erp_bd';
		// PRODUCCION
		$conexionDB = 'localhost';
		$user       = 'root';
		$pass       = 'serverchkdsk';
		$bd         = 'erp_acceso';

		$objConectDB = new ConnectDb(
						"MySql",		// API SQL A UTILIZAR  MySql, MySqli
						"$conexionDB",	// SERVIDOR
						"$user",		// USUARIO DATA BASE
						"$pass",		// PASSWORD DATA BASE
						"$bd"			// NOMBRE DATA BASE
					);

		$mysql = $objConectDB->getApi();
		$link  = $mysql->conectar();
		// echo "string";

		// CONSULTAR EL NOMBRE LA BASE DE DATOS DE LA EMPRESA
		$sql="SELECT id,bd,servidor,id_plan,almacenamiento,fecha_vencimiento_plan FROM host WHERE activo=1 AND nit=$nit_empresa";
		$query=$mysql->query($sql,$mysql->link);
		$id_host                = $mysql->result($query,0,'id');
		$bd                     = $mysql->result($query,0,'bd');
		$servidor               = $mysql->result($query,0,'servidor');
		$id_plan                = $mysql->result($query,0,'id_plan');
		$almacenamiento         = $mysql->result($query,0,'almacenamiento');
		$fecha_vencimiento_plan = $mysql->result($query,0,'fecha_vencimiento_plan');
		// var_dump($mysql);

		$sql   = "SELECT usuarios,sucursales FROM planes WHERE activo=1 AND id='$id_plan'";
		$query = $mysql->query($sql,$mysql->link);
		$usuarios_plan   = $mysql->result($query,0,'usuarios');
		$sucursales_plan = $mysql->result($query,0,'sucursales');

		if ($bd=='') {
			echo "<script>alert('No existe la empresa en la base de datos');</script>";
			// header ("Location: login.php");
			// exit;
		}

		// VALIDAR LA EXISTENCIA DEL USUARIO EN LA BASE DE DATOS
		// if (isset($mysql->link) && is_resource($mysql->link)) {
		//     // mysql_close($dbh);
		// 	$mysql->close($mysql->link);
		// } else {
		//     // mysql_close();
		// 	$mysql->close();
		// }

		// $mysql->close($mysql->link);

		$objConectDB = new ConnectDb(
						"MySql",		// API SQL A UTILIZAR  MySql, MySqli
						"$servidor",	// SERVIDOR
						"$user",		// USUARIO DATA BASE
						"$pass",		// PASSWORD DATA BASE
						"$bd"			// NOMBRE DATA BASE
					);

		$mysql = $objConectDB->getApi();
		$link  = $mysql->conectar();
		// var_dump($mysql);
		// CONSULTAR EL ID DE LA EMPRESA
		$sql   ="SELECT id,nombre,razon_social,id_pais,id_moneda,descripcion_moneda,simbolo_moneda,decimales_moneda FROM empresas WHERE activo=1 AND documento=$nit_empresa ";
		$query =$mysql->query($sql,$mysql->link);
		$id_empresa         = $mysql->result($query,0,'id');
		$nombre_empresa     = $mysql->result($query,0,'nombre');
		$razon_social       = $mysql->result($query,0,'razon_social');
		$id_pais            = $mysql->result($query,0,'id_pais');
		$id_moneda          = $mysql->result($query,0,'id_moneda');
		$descripcion_moneda = $mysql->result($query,0,'descripcion_moneda');
		$simbolo_moneda     = $mysql->result($query,0,'simbolo_moneda');
		$decimales_moneda   = $mysql->result($query,0,'decimales_moneda');

		// CONSULTAR LAS SUCURSALES
		$sql="SELECT id,nombre FROM empresas_sucursales WHERE activo=1 AND id_empresa=$id_empresa";
		$query=$mysql->query($sql,$mysql->link);
		$contSucursales = $mysql->numrows($query);
		$arraySucursales = array();
		if ($contSucursales>1) {
			while ($row=$mysql->fetch_array($query)) {
				$optionSucursal .= "<option value='$row[id]' data-name='$row[nombre]' >$row[nombre]</option>";
				$arraySucursales[] = array('id' => $row['id'], 'nombre' => $row['nombre']);
			}
		}
		else{
			$id_sucursal     = $mysql->result($query,0,'id');
			$nombre_sucursal = $mysql->result($query,0,'nombre');
		}
		// print_r($arraySucursales);
		$sql   = "SELECT id,documento,nombre,color_fondo,color_menu,email_empresa,id_rol FROM empleados WHERE activo=1 AND id_empresa=$id_empresa AND username='$username' ";
		$query = $mysql->query($sql,$mysql->link);
		$id_empleado        = $mysql->result($query,0, 'id');
		$documento_empleado = $mysql->result($query,0, 'documento');
		$nombre_empleado    = $mysql->result($query,0, 'nombre');
		$color_fondo        = $mysql->result($query,0, 'color_fondo');
		$color_menu         = $mysql->result($query,0, 'color_menu');
		$email_empresa      = $mysql->result($query,0, 'email_empresa');
		$id_rol             = $mysql->result($query,0, 'id_rol');

		$sql="SELECT valor FROM empleados_roles WHERE id=$id_rol";
		$query=$mysql->query($sql,$mysql->link);
		$rol_valor = $mysql->result($query,0, 'valor');

		$permisos = array();
		if($support=='true'){
			$permiso = 1;
			while ($permiso<500) {
				$permisos[]=$permiso;
				$permiso++;
			}
		}
		else{
			$sql="SELECT id_permiso FROM empleados_roles_permisos WHERE id_rol =$id_rol";
			$query=$mysql->query($sql,$mysql->link);
			while($row = $mysql->fetch_array($query)){
				$permisos[]=$row['id_permiso'];
			}
		}


		$sql="SELECT id_unico FROM licencia_soporte WHERE id_empresa=$id_empresa ";
		$query=$mysql->query($sql,$mysql->link);
		$id_licencia = $mysql->result($query,0, 'id_unico');

		if (($id_empleado=='' || $id_empleado==0) && $support<>'true') {
			echo "<script>alert('No existe el empleado en la empresa $nit_empresa');</script>";
			// header ("Location: login.php");
			// exit;
		}

		// CREAR LAS VARIABLES DE SESION
		session_start();

		$_SESSION['BD']                     = $bd;
		$_SESSION['SERVIDOR']               = $servidor;
		$_SESSION['ID_HOST']                = $id_host;
		$_SESSION['ALMACENAMIENTO']         = $almacenamiento;
		$_SESSION['PLAN_FECHA_VENCIMIENTO'] = $fecha_vencimiento_plan;
		$_SESSION['PLAN_USUARIOS']          = $usuarios_plan;
		$_SESSION['PLAN_SUCURSALES']        = $sucursales_plan;
		$_SESSION['ROL']                    = ($support=='true')? 0 : $id_rol;
		$_SESSION['ROLVALOR']               = $rol_valor;
		$_SESSION['PERMISOS']               = $permisos;
		$_SESSION['ACTUALIZA_PASS']         = 'false';
		$_SESSION["COLOR_VENTANA"]          = '#157FCC';  //OLIVE '#F3FFF3'
		$_SESSION["COLOR_CONTRASTE"]        = '#DFE8F6';  //OLIVE '#F3FFF3'
		$_SESSION["COLOR_FONDO"]            = '#CDDBF0';  //OLIVE '#CAE5B0'
		$_SESSION["COLOR_LINEA"]            = '#8DB2E3';  //OLIVE '#92C95D'
		$_SESSION["COLOR_FUENTE"]           = '#033999';
		$_SESSION['COLOR_ESCRITORIO']       = ($support=='true')? '32,124,229' : $color_fondo;
		$_SESSION['COLOR_MENU']             = ($support=='true')? '0,0,0' : $color_menu;
		$_SESSION['COLOR_MD_CALENDARIO']    = ($support=='true')? '0,0,0' : $color_menu;

		$_SESSION['IDUSUARIO']              = ($support=='true')? 0  : $id_empleado;
		$_SESSION['CEDULAFUNCIONARIO']      = ($support=='true')? $_POST['cedula_usuario']  : $documento_empleado;
		$_SESSION['NOMBREFUNCIONARIO']      = ($support=='true')? $_POST['nombre_funcionario']  : $nombre_empleado;
		$_SESSION['NOMBREUSUARIO']          = ($support=='true')? $_POST['username']  : $username;
		$_SESSION['EMAIL']                  = ($support=='true')? $_POST['email']  : $email_empresa;
		$_SESSION['SUPPORT']                = ($support=='true')? 'true' : 'false';

		$_SESSION['EMPRESA']                = $razon_social;
		$_SESSION['NOMBREEMPRESA']          = $nombre_empresa;
		$_SESSION['NITEMPRESA']             = $nit_empresa;
		// $_SESSION['GRUPOEMPRESARIAL']       = $;
		$_SESSION['PAIS']                   = $id_pais;
		$_SESSION['MONEDA']                 = $id_moneda;
		$_SESSION['DESCRIMONEDA']           = $descripcion_moneda;
		$_SESSION['SIMBOLOMONEDA']          = $simbolo_moneda;
		$_SESSION['DECIMALESMONEDA']        = $decimales_moneda;
		// $_SESSION['SUCURSALORIGEN']         = $;
		// $_SESSION['EMPRESAORIGEN']          = $;
		// $_SESSION['CONEXIONSIIP3']          = $;
		// $_SESSION['APIGOOGLE']              = $;
		$_SESSION['LICENCIASOPORTE']        = $id_licencia;
		$_SESSION['PRODUCTO']               = 4;
		$_SESSION['APP']                    = 'LogicalSoft-ERP';
		$_SESSION['PATHCKFINDER']           = '/ARCHIVOS_PROPIOS/';
		// $_SESSION['valor_cookie']           = $;
		$_SESSION['VERSION']                = '1.0.0.19-06-2013';

		// echo "SESION INICIADA";
		// $cloud = true;

		if ($id_sucursal>0) {
			$_SESSION['SUCURSAL']               = $id_sucursal;
			$_SESSION['NOMBRESUCURSAL']         = $nombre_sucursal;
			include 'escritorio.php';
		}
		else{
			include 'seleccion_sucursal.php';
		}

		// echo $_SESSION['IDUSUARIO']."<br>";
		// echo $_SESSION['CEDULAFUNCIONARIO']."<br>";
		// echo $_SESSION['NOMBREFUNCIONARIO']."<br>";
		// echo $_SESSION['NOMBREUSUARIO']."<br>";
		// echo $_SESSION['EMAIL']."<br>";
		// echo $_SESSION['SUPPORT']."<br>";

		// include 'escritorio.php';
		// session_destroy();
	}
	else{
		echo "<script>alert('Error de token de seguridad');</script>";
		// header ("Location: login.php");
	}

 ?>