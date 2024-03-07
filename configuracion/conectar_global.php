<?php
error_reporting(E_ALL);
	if(!isset($_SESSION)){
		session_start();
	}

	// objeto con los datos de la conexion al servidor
	include_once("conexion.php");
	$acceso = mysql_connect($server->server_name,$server->user,$server->password) ;
	$error = mysql_errno($acceso);
	// echo " $servidor - $usuario - $password ";
	// $acceso = mysql_connect($servidor,$usuario,"");
	if(!$acceso){ echo "Error Conectando a Mysql  (global connect) <br /> $error"; };
	mysql_select_db($server->database,$acceso);
	if(!@mysql_select_db($server->database,$acceso)){ echo 'Error Conectando a la la base de datos "'.$server->database.'" (global connect) <br />'; };

	//SI EXISTE NIT_EMPRESA
	if(isset($nit_empresa)){
		$nit_empresa = mysql_real_escape_string($nit_empresa);

		$sqlBd   = "SELECT * FROM host WHERE activo=1 AND nit='$nit_empresa' LIMIT 0,1";
		$queryBd = mysql_query($sqlBd,$acceso);

		//RETORNA VARIABLES DE CONEXION
		if(isset($return_conectar_global)){
			if(mysql_num_rows($queryBd)){		//SI LA EMPRESA SI EXISTE
				$bdEmpresa       = mysql_result($queryBd, 0, 'bd');
				$servidorEmpresa = mysql_result($queryBd, 0, 'servidor');
				
				mysql_close($acceso);

				return array('estado'=>true, 'bdEmpresa'=>$bdEmpresa, 'servidorEmpresa'=>$servidorEmpresa);
			}
			else{ return array('estado'=>false); }
		}
		else{
			if(mysql_num_rows($queryBd)){		//SI LA EMPRESA SI EXISTE
				$id_plan                    = mysql_result($queryBd, 0, 'id_plan');
				$_SESSION['BD']             = mysql_result($queryBd, 0, 'bd');
				$_SESSION['SERVIDOR']       = mysql_result($queryBd, 0, 'servidor');
				$_SESSION['ID_HOST']        = mysql_result($queryBd, 0, 'id');
				$_SESSION['TIMEZONE']       = mysql_result($queryBd, 0, 'timezone');
				$_SESSION['ALMACENAMIENTO'] = mysql_result($queryBd, 0, 'almacenamiento');

				$_SESSION['PLAN_FECHA_VENCIMIENTO'] = mysql_result($queryBd, 0,'fecha_vencimiento_plan');

				$sql   = "SELECT usuarios,sucursales FROM planes WHERE activo=1 AND id='$id_plan'";
				$query = mysql_query($sql,$acceso);

				$_SESSION['PLAN_USUARIOS']   = mysql_result($query, 0, 'usuarios');
				$_SESSION['PLAN_SUCURSALES'] = mysql_result($query, 0, 'sucursales');

				mysql_close($acceso);
				
				include_once('conectar.php');
			}
			else{ echo 'false{.}Identificacion de Empresa no Existe{.}false{.}false'; exit; }
		}
	}
?>