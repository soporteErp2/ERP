<?php
set_time_limit(0);


 //LECTURA DEL XML DE CONFIGURACION DE CONEXION LDAP/////////////////////////////////////////////
include('xml2array.php');
$DIRECTORIO = explode ("/", $_SERVER['REQUEST_URI']);
if(file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$DIRECTORIO[1].'/ARCHIVOS_PROPIOS/conexion_ldap.xml')){
	$fichero  = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/'.$DIRECTORIO[1].'/ARCHIVOS_PROPIOS/conexion_ldap.xml'); //SI SE LLAMA DESDE LOCAL O EN CARPETA /SIIP
}else{
	$fichero  = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/'.$DIRECTORIO[0].'/ARCHIVOS_PROPIOS/conexion_ldap.xml'); //SI SE LLAMA DESDE UN DOMINIO
}
$array = xml2array($fichero); 

$domain = $array['configuracion']['activedirectory']['dominio'];
$ext = $array['configuracion']['activedirectory']['ext'];
$ou = $array['configuracion']['activedirectory']['ou'];
$uid = $array['configuracion']['activedirectory']['user']."";
$psw = $array['configuracion']['activedirectory']['pass']."";
///////////////////////////////////////////////////////////////////////////////

function pingDomain(){		// FUNCION PARA COMPROBAR EL ESTADO DEL SERVICIO DE DIRECTORIO ACTIVO (OFFLINE/ONLINE)
	$domain="192.168.0.150";	// MAS EFICIENTE CON DIRECCION IP DEL DIRECTORIO ACTIVO
    $starttime = microtime(true);
    $file      = fsockopen ($domain, 389, $errno, $errstr, 0.9);// SE COMPRUEBA EL SERVIDOR VERIFICANDO EL PUERTO 389 (LDAP AD)// EL ULTIMO VALOR ES EL TIMEOUT
    $stoptime  = microtime(true);
    $status    = 0;

    if (!$file)
		$status = -1;  // SITE IS DOWN
    else {
        fclose($file);
        $status = ($stoptime - $starttime) * 1000;
        $status = floor($status);
    }
	if($status==-1)
		return false;
	else
		return true;
}


 function datosUsuarioAD($name) { // FUNCION PARA BUSCAR INFORMACION DE UN USUARIO EN EL DIRECTORIO ACTIVO

		global $domain,$ext,$ou,$uid,$psw;
		$url=$domain.".".$ext;
 
        $sroot		= "OU=".$ou.",DC=".$domain.",DC=".$ext;//CONTRUYENDO RAIZ(DC) DEL DIRECTORIO ACTIVO Y RAMA(OU) EN QUE SE REALIZARA LA BUSQUEDA
        $host		= "ldap://".$url;
        $ds			= @ldap_connect($host);
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
		$user=$uid;
		$uid=$domain.trim('\ ').$uid;
        if (!$ds) {
				ldap_close($ds);
            return false;
        } else {		
            $validacion = @ldap_bind( $ds, $uid, $psw );    
            if (!$validacion) {
				ldap_close($ds);
                return false;
            } else {
                $ActiveDirectory = array();
				$search	= array("givenname","sn","samaccounttype"); // CAMPOS A BUSCAR EN EL DIRECTORIO ACTIVO
				$filtro	= "(&(objectCategory=user)(sAMAccountName=".$name."))"; // FILTRO DE LO QUE SE ESTA BUSCANDO, USER=PERSONAS, SAMACCOUNTNAME=LOGIN DE USUARIO
                $sr	= @ldap_search($ds, $sroot,$filtro, $search);  // BUSQUEDA
                $ad	= @ldap_get_entries( $ds, $sr );	//RETORNO DE LA BUSQUEDA ARRAY CON LOS NOMBRES DE LOS CAMPOS BUSCADOS
				for ($i=0; $i<$ad["count"]; $i++){
					$ActiveDirectory[$i]['givenname']		= $ad[$i]["givenname"][0];
					$ActiveDirectory[$i]['sn']				= $ad[$i]["sn"][0];
					$ActiveDirectory[$i]['samaccountname']	= $ad[$i]["samaccountname"][0];
					}
				ldap_close($ds);
                return $ActiveDirectory; //RETORNA
            }
        }
		
    }
	
function validaUsuarioAD($name,$pass) { // FUNCION PARA VALIDAR USUARIO Y CONTRASEÑA EN EL DIRECTORIO ACTIVO

		global $domain,$ext,$ou;
		$uid=$name;
		$psw=$pass;
		$url=$domain.".".$ext;
 
        $sroot		= "OU=".$ou.",DC=".$domain.",DC=".$ext;//CONTRUYENDO RAIZ(DC) DEL DIRECTORIO ACTIVO Y RAMA(OU) EN QUE SE REALIZARA LA BUSQUEDA
        $host		= "ldap://".$url;
        $ds			= @ldap_connect($host);
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
		$user=$uid;
		$uid=$domain.trim('\ ').$uid;
        if (!$ds) {
				ldap_close($ds);
            return false;
        } else {		
            $validacion = @ldap_bind( $ds, $uid, $psw );    
            if (!$validacion) {
				ldap_close($ds);
                return false;
            } else {
                $ActiveDirectory = array();
				$search	= array("givenname","sn","samaccounttype"); // CAMPOS A BUSCAR EN EL DIRECTORIO ACTIVO
				$filtro	= "(&(objectCategory=user)(sAMAccountName=".$name."))"; // FILTRO DE LO QUE SE ESTA BUSCANDO, USER=PERSONAS, SAMACCOUNTNAME=LOGIN DE USUARIO
                $sr	= @ldap_search($ds, $sroot,$filtro, $search);  // BUSQUEDA
                $ad	= @ldap_get_entries( $ds, $sr );	//RETORNO DE LA BUSQUEDA ARRAY CON LOS NOMBRES DE LOS CAMPOS BUSCADOS
				for ($i=0; $i<$ad["count"]; $i++){
					$ActiveDirectory[$i]['givenname']		= $ad[$i]["givenname"][0];
					$ActiveDirectory[$i]['sn']				= $ad[$i]["sn"][0];
					$ActiveDirectory[$i]['samaccountname']	= $ad[$i]["samaccountname"][0];
					}
				ldap_close($ds);
                return $ActiveDirectory; //RETORNA
            }
        }		
    }
	


?>