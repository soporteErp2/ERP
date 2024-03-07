<?php
include("../configuracion/conectar.php");
//include('../configuracion/define_variables.php');

$pass = md5($_POST['password']);
//$pass = $_POST['password'];

$empresa 	= mysql_real_escape_string($_POST['empresa']);
$usuario	= mysql_real_escape_string($_POST['usuario']);
$IdEmpresa 	= $_POST['IdEmpresa'];
$sucursal	= $_POST['sucursal'];

$consul  = mysql_query("SELECT * FROM empleados WHERE username = '$usuario' AND password = '$pass' AND (id_empresa = $IdEmpresa OR id_empresa = 0) AND activo = 1 ORDER BY id_empresa LIMIT 0,1",$link);
$consul2 = mysql_query("SELECT * FROM configuracion_global",$link);
$consul3 = mysql_query("SELECT * FROM vista_sucursales_empresas WHERE id_sucursal = $sucursal AND id_empresa = $IdEmpresa",$link);
$consul4 = mysql_query("SELECT activo FROM configuracion_global_api_google",$link);
//echo 'false{.}';
// echo "SELECT * FROM vista_sucursales_empresas WHERE id_sucursal = $sucursal AND id_empresa = $IdEmpresa";exit;
if(mysql_num_rows($consul)){

	//PERMISOS DE USUARIO
	//--------------------------------------------------------------------------------------------------------------//
	$_SESSION["ROL"] = mysql_result($consul,0,"id_rol");
	$_SESSION["ROLVALOR"] = mysql_result(mysql_query("SELECT valor FROM empleados_roles WHERE id = $_SESSION[ROL]"),0,"valor");

	$permisos = array();
	$consul_permisos = mysql_query("SELECT id_permiso FROM empleados_roles_permisos WHERE id_rol = $_SESSION[ROL]",$link);
	while($row_permi = mysql_fetch_array($consul_permisos)){
		$permisos[]=$row_permi['id_permiso'];
	}
	$_SESSION["PERMISOS"] = $permisos;

	if($_POST['password'] == '12345678'){
		$_SESSION["ACTUALIZA_PASS"] = 'true';
	}else{
		$_SESSION["ACTUALIZA_PASS"] = 'false';
	}

	//DATOS DEL TEMA DE ESCRITORIO
	//--------------------------------------------------------------------------------------------------------------//
	$_SESSION["COLOR_VENTANA"]    = '#157FCC';  //OLIVE '#F3FFF3'
    $_SESSION["COLOR_CONTRASTE"]  = '#DFE8F6';  //OLIVE '#F3FFF3'
	$_SESSION["COLOR_FONDO"]      = '#CDDBF0';  //OLIVE '#CAE5B0'
	$_SESSION["COLOR_LINEA"]      = '#8DB2E3';  //OLIVE '#92C95D'
	$_SESSION["COLOR_FUENTE"]     = '#033999';
    $_SESSION["COLOR_ESCRITORIO"] = mysql_result($consul,0,"color_fondo");
    $_SESSION["COLOR_MENU"]       = mysql_result($consul,0,"color_menu");
    $_SESSION["COLOR_MD_CALENDARIO"] = mysql_result($consul,0,"color_menu"); //'#1485C9';

	//DATOS DEL USUARIO
	//--------------------------------------------------------------------------------------------------------------//
	$_SESSION["IDUSUARIO"] = mysql_result($consul,0,"id");//IDENTIFICA LA VARIABLE DE SESSION
	$_SESSION["CEDULAFUNCIONARIO"] =  mysql_result($consul,0,"documento");//IDENTIFICA LA CEDULA
	$_SESSION["NOMBREFUNCIONARIO"] =  mysql_result($consul,0,"nombre");
	$_SESSION["NOMBREUSUARIO"] =  mysql_result($consul,0,"username");
	$_SESSION["EMAIL"] = mysql_result($consul,0,"email_empresa");

	//DATOS DE LA SUCURSAL
	//--------------------------------------------------------------------------------------------------------------//
	$_SESSION["SUCURSAL"] = mysql_result($consul3,0,"id_sucursal");
	$_SESSION["NOMBRESUCURSAL"] = mysql_result($consul3,0,"sucursal");
	$_SESSION["EMPRESA"] = mysql_result($consul3,0,"id_empresa");
	$_SESSION["NOMBREEMPRESA"] = mysql_result($consul3,0,"empresa");
	$_SESSION["NITEMPRESA"] = mysql_result($consul3,0,"nit_completo");
	$_SESSION["GRUPOEMPRESARIAL"] = mysql_result($consul3,0,"grupo_empresarial");

	$_SESSION["PAIS"] = mysql_result($consul3,0,"id_pais");
	$_SESSION["MONEDA"] = mysql_result($consul3,0,"id_moneda");
	$_SESSION["DESCRIMONEDA"] = mysql_result($consul3,0,"descripcion_moneda");
	$_SESSION["SIMBOLOMONEDA"] = mysql_result($consul3,0,"simbolo_moneda");
	$_SESSION["DECIMALESMONEDA"] = mysql_result($consul3,0,"decimales_moneda");

	$_SESSION["SUCURSALORIGEN"] = mysql_result($consul,0,"id_sucursal");
	$_SESSION["EMPRESAORIGEN"] = mysql_result($consul,0,"id_empresa");
	$_SESSION["CONEXIONSIIP3"] = mysql_result($consul2,0,"conexion_siip3");
	$_SESSION["APIGOOGLE"] = mysql_result($consul4,0,"activo");

	/*$DIRECTORIO = explode ("/", $_SERVER['REQUEST_URI']);
	if(count($DIRECTORIO) == 4){
		$_SESSION["MYPATH"] = "/SIIP/";
	}else{
		$_SESSION["MYPATH"] = "/";
	}*/

	//LICENCIA DE SOPORTE
	//--------------------------------------------------------------------------------------------------------------//

	$_SESSION["LICENCIASOPORTE"]  = mysql_result(mysql_query("SELECT id_unico FROM licencia_soporte",$link),0,'id_unico');
	$_SESSION["PRODUCTO"] = $PRODUCT; //INDICA EL NUMERO DE PRODUCTO EN EL MODULO DE SOPORTE  (2 -> SIIP)
	$_SESSION["APP"] = $APP; //NOMBRE DEL APLICATIVO


	//PATH DE LA CARPETA DE ARCHIVOS PARA EL FILEUPLOADER DE CKFINDER
	//--------------------------------------------------------------------------------------------------------------//

	$PATH = $array['configuracion']['fileuploader']['path'];
	$_SESSION["PATHCKFINDER"] = $PATH; //NOMBRE DEL APLICATIVO

	//COOKIE
	//--------------------------------------------------------------------------------------------------------------//
	if($_POST['recordar'] == 'true'){
	 	$_SESSION['valor_cookie'] = $_POST['empresa'].'-'.$_POST['sucursal'].'-'.$_POST['usuario'].'-'.$_POST['IdEmpresa'];
	}else{
		$_SESSION['valor_cookie'] = 'false';
	}
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    echo 'true';
}else{
    echo 'false{.}Su Contrase&ntilde;a esta Incorrecta!';
}

?>