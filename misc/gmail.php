<?php
include("../configuracion/conectar.php");
include("../configuracion/define_variables.php");
require_once 'Zend/Loader.php';

Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_Gapps');

//$client=logon($link);

switch ($op) {
		case "creaCorreo":
			crear($username,$givenName,$familyName,$link);
			break;
		case "checkCorreo":
			getUser($username,$link);
			break;
		case "eliminaCorreo":
			borrar($username,$link);
			break;
		case "resetPassword":
			resetPassword($username,$link);
			break;
}


function logon($link){	// LOGIN EN GMAIL // DEBE SER UN USUARIO CON DERECHOS DE ADMINISTRADOR

	$email		= "";
	$password	= "";
	$domain		= "";
	$client		= "";
	$sql="SELECT * FROM configuracion_global_api_google";
	$result = mysql_query($sql,$link);		
			if(mysql_num_rows($result)){
				$email		= mysql_result($result,$i,"email");
				$password	= mysql_result($result,$i,"password");
				$domain	= mysql_result($result,$i,"dominio");
			}
	//$email ="siip@plataforma.com.co";	// USUARIO ADMINISTRADOR
	//$password= "serverchkdsk";	
	//$domain= "plataforma.com.co";		// DOMINIO
	$client = Zend_Gdata_ClientLogin::getHttpClient($email, $password, Zend_Gdata_Gapps::AUTH_SERVICE_NAME);
	$client = new Zend_Gdata_Gapps($client, $domain);
	return $client; // RETORNA CONEXION
}

function getState($username,$link){	// VERIFICA SI EL ESTADO DE LA CUENTA DEL USUARIO SUSPENDIDA (false) // ACTIVA (true)
	$client=logon($link);
	try{
		$user=$client->retrieveUser($username);
		if($user->login->userName){
			if($user->login->suspended){
				echo "true{.}";
			}else{
				echo "false{.}";
			}		
		}else{
			echo "false{.}Cuenta no existente";		
		}		
		
	} catch (Zend_Gdata_Gapps_ServiceException $e) {
		foreach ($e->getErrors() as $error) {
			$fail=$error->getErrorCode();
		}					
		switch ($fail) {			
			default:
				echo "false{.}";
				echo "Error no conocido (Error ".$fail.")/n".$error->getReason();
				break;    
		}
	}
}

function getUser($username,$link){	// VERIFICA SI EL NOMBRE DE USUARIO EXISTE EN EL DOMINIO //EXISTE (TRUE) // NO EXISTE (FALSE)
	$client=logon($link);
	try{
		$user=$client->retrieveUser($username); 
		if($user->login->userName){
			echo "true{.}Existe";		
		}else{
			echo "false{.}No existe";		
		}		
		
	} catch (Zend_Gdata_Gapps_ServiceException $e) {
		foreach ($e->getErrors() as $error) {
			$fail=$error->getErrorCode();
		}					
		switch ($fail) {			
			default:
				echo "false{.}";
				echo "Error no conocido (Error ".$fail.")/n".$error->getReason();
				break;    
		}
	}
}

function resetPassword($username,$link){	// RESETEA LA CONTRASEÑA DEL USUARIO A LA PREDETERMINADA
	$client=logon($link);
	try{
		$user=$client->retrieveUser($username);
		$user->login->password = "12345678";
		$user = $user->save();		
		if($user->login->userName){
			echo "true{.}Contraseña restablecida, correo: ".$username."@plataforma.com.co";	
		}else{
			echo "false{.}Error, no se realizo.";		
		}		
		
	} catch (Zend_Gdata_Gapps_ServiceException $e) {
		foreach ($e->getErrors() as $error) {
			$fail=$error->getErrorCode();
		}					
		switch ($fail) {			
			default:
				echo "false{.}";
				echo "Error no conocido (Error ".$fail.")/n".$error->getReason();
				break;    
		}
	}
}

function borrar($username,$link){ // ELIMINA LA CUENTA DEL USUARIO.
	$client=logon($link);
	try{
		$user=$client->retrieveUser($username);
		$name=$user->login->userName;
		if($name!=""){
				$client->deleteUser($username); /// CUIDADO!!!! /// CUIDADO!!!! /// CUIDADO!!!! /// CUIDADO!!!!
				echo "true{.}Cuenta eliminada correctamente: ".$username."@plataforma.com.co";
			}else
				echo "false{.}Error, Cuenta no existe: ".$username."@plataforma.com.co";
		
	} catch (Zend_Gdata_Gapps_ServiceException $e) {
		foreach ($e->getErrors() as $error) {
			$fail=$error->getErrorCode();
		}					
		switch ($fail) {			
			default:
				echo "false{.}";
				echo "Error no conocido (Error ".$fail.")/n".$error->getReason();
				break;    
		}
	}
}

function suspender($username,$link){ // DESHABILITA LA CUENTA DEL USUARIO
	$client=logon($link);
	try{
		$client->suspendUser($username);
		echo "true{.}";	
	} catch (Zend_Gdata_Gapps_ServiceException $e) {
		foreach ($e->getErrors() as $error) {
			$fail=$error->getErrorCode();
		}					
		switch ($fail) {			
			default:
				echo "false{.}";
				echo "Error no conocido (Error ".$fail.")/n".$error->getReason();
				break;    
		}
	}
}

function restaurar($username,$link){ // HABILITA LA CUENTA DEL USUARIO
	$client=logon($link);
	try{
		$client->restoreUser($username);
		echo "true{.}";	
	} catch (Zend_Gdata_Gapps_ServiceException $e) {
		foreach ($e->getErrors() as $error) {
			$fail=$error->getErrorCode();
		}					
		switch ($fail) {			
			default:
				echo "false{.}";
				echo "Error no conocido (Error ".$fail.")/n".$error->getReason();
				break;    
		}
	}
}
function crear($username,$givenName,$familyName,$link){	/// CREA LA CUENTA DE CORREO DEL USUARIO
	$client=logon($link);
	$password = "12345678";
	try{
		$client->createUser($username, $givenName, $familyName, $password, $passwordHashFunction = null, $quota = null);
		echo "true{.}Cuenta de correo creada correctamente: ".$username."@plataforma.com.co";;
	} catch (Zend_Gdata_Gapps_ServiceException $e) {
		foreach ($e->getErrors() as $error) {
			$fail=$error->getErrorCode();
		}					
		switch ($fail) {
			case "1300":
				echo "false{.}";
				echo "Mail ya existe (Error 1300)";
				break;
			case "1303":
				echo "false{.}";
				echo "Usuario no valido (Error 1303)";
				break;
			case "1400":
				echo "false{.}";
				echo "Nombre de usuario no valido (Error 1400)";
				break;
			case "1401":
				echo "false{.}";
				echo "Apellido de usuario no valido (Error 1401)";
				break;
			case "1402":
				echo "false{.}";
				echo "Contraseña no valida (Error 1402)";
				break;
			default:
				echo "false{.}";
				echo "Error no conocido (Error ".$fail.")/n".$error->getReason();
				break;    
		}
	}
}
?>
