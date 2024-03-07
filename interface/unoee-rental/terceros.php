<?php 
require_once('../../configuracion/conectar.php');
require_once('../../misc/nuSoap/nusoap.php');
error_reporting(E_ALL);

$consul = mysql_query("SELECT * FROM interface_siesa_configuracion WHERE id_empresa = $_SESSION[EMPRESA]",$link);
if(mysql_result($consul,0,'directorio')==""){$directorio = "";}else{$directorio = "<directorio>".mysql_result($consul,0,'directorio')."</directorio>";}

$UltimaActualizacion = str_replace("-","", mysql_result( mysql_query("SELECT terceros FROM interface_siesa_syncode WHERE id = $_SESSION[EMPRESA]",$link) ,0,'terceros') );
$UltimaActualizacion = '20120101';
$Hoy = date("Ymd");

/////////////////////////////////////////////////////////////////////////////////////////////
$url = mysql_result($consul,0,'url');
$function = 'consultar';
$log = 'false';
$bd_intercambio	= 'interface_siesa_terceros';
$argumentos	=	"<?xml version=\"1.0\" encoding=\"UTF-8\"?>
					<ws>
						<seguridad>
							<usuario>".mysql_result($consul,0,'usuario')."</usuario>
							<clave>".mysql_result($consul,0,'clave')."</clave>
							".$directorio."
						</seguridad>
						<consulta>
							<proveedor>".mysql_result($consul,0,'proveedor')."</proveedor>
							<nombre>CLIENTES</nombre>
							<parametros>
								<p_id_cia>".mysql_result($consul,0,'id_compania')."</p_id_cia>
								<p_fec_ini_modif>".$UltimaActualizacion."</p_fec_ini_modif>
								<p_fec_fin_modif>".$Hoy."</p_fec_fin_modif>
							</parametros>
						</consulta>
					</ws>";

$param = array("arg0"=>$argumentos);

$status 	= 'true';
$mensaje 	= '';
$error 		= '';	

/////////////////////////////////////////////////////////////////////////////////////////////

$client = new nusoap_client($url,'wsdl');
$err = $client->getError();

if($err){
	$status 	= 'false';
	$mensaje 	= 'Error Sincronizando con UnoEE (X001)';
	$error 		= 'Constructor error<br /><br />'.$err;
}
$result = $client->call($function, $param);
libxml_use_internal_errors(true);
//$xml = simplexml_load_string($result,NULL, LIBXML_NOCDATA);
try {
	$result = new SimpleXMLElement(utf8_encode($result),LIBXML_NOCDATA); //LEE EL STRIG CON LA ETIQUETA CDATA Y LO CONVIERTE EN ARRAY
} catch (Exception $e) {
	echo $e; 
}

mysql_query("TRUNCATE TABLE $bd_intercambio",$link);
mysql_query("UPDATE interface_siesa_syncode SET terceros = NOW() WHERE id = $_SESSION[EMPRESA]",$link);

for ($i=0;$i<count($result->rs[0]->r);$i++){
	
	$ROWID_TERCERO				=	$result->rs[0]->r[$i]->ROWID_TERCERO;
	$ID_TERCERO					=	$result->rs[0]->r[$i]->ID_TERCERO;
	$ID_SUCURSAL				=	$result->rs[0]->r[$i]->ID_SUCURSAL;
	$RAZON_SOCIAL				=	utf8_decode($result->rs[0]->r[$i]->RAZON_SOCIAL);
	$DESCRIPCION_SUCURSAL		=	utf8_decode($result->rs[0]->r[$i]->DESCRIPCION_SUCURSAL);
	$ID_TIPO_IDENTIFICACION		=	$result->rs[0]->r[$i]->ID_TIPO_IDENTIFICACION;
	$DESCRIPCION_TIPO_IDENTIF	=	utf8_decode($result->rs[0]->r[$i]->DESCRIPCION_TIPO_IDENTIF);
	$NIT						=	$result->rs[0]->r[$i]->NIT;
	$DIG_VERIFIC_NIT			=	$result->rs[0]->r[$i]->DIG_VERIFIC_NIT;
	$DIRECCION_1				=	utf8_decode($result->rs[0]->r[$i]->DIRECCION_1);
	$DIRECCION_2				=	utf8_decode($result->rs[0]->r[$i]->DIRECCION_2);
	$TELEFONO					=	$result->rs[0]->r[$i]->TELEFONO;
	$EMAIL						=	$result->rs[0]->r[$i]->EMAIL;
	$ID_PAIS					=	$result->rs[0]->r[$i]->ID_PAIS;
	$ID_DEPTO					=	$result->rs[0]->r[$i]->ID_DEPTO;
	$DESCRIP_DEPTO				=	utf8_decode($result->rs[0]->r[$i]->DESCRIP_DEPTO);
	$ID_CIUDAD					=	$result->rs[0]->r[$i]->ID_CIUDAD;
	$DESCRIP_CIUDAD				=	utf8_decode($result->rs[0]->r[$i]->DESCRIP_CIUDAD);
	$NOM_CONTACTO				=	utf8_decode($result->rs[0]->r[$i]->NOM_CONTACTO);
	$NOTAS						=	utf8_decode($result->rs[0]->r[$i]->NOTAS);
	$ESTADO_REG					=	$result->rs[0]->r[$i]->ESTADO_REG;
	
	$SQL =  " INSERT INTO $bd_intercambio(
					id_empresa,
					ROWID_TERCERO,
					ID_TERCERO,
					ID_SUCURSAL,
					RAZON_SOCIAL,
					DESCRIPCION_SUCURSAL,
					ID_TIPO_IDENTIFICACION,
					DESCRIPCION_TIPO_IDENTIF,
					NIT,
					DIG_VERIFIC_NIT,
					DIRECCION_1,
					DIRECCION_2,
					TELEFONO,
					EMAIL,
					ID_PAIS,
					ID_DEPTO,
					DESCRIP_DEPTO,
					ID_CIUDAD,
					DESCRIP_CIUDAD,
					NOM_CONTACTO,
					NOTAS,
					ESTADO_REG
				)VALUES(
					$_SESSION[EMPRESA],
					'$ROWID_TERCERO',
					'$ID_TERCERO',
					'$ID_SUCURSAL',
					'$RAZON_SOCIAL',
					'$DESCRIPCION_SUCURSAL',
					'$ID_TIPO_IDENTIFICACION',
					'$DESCRIPCION_TIPO_IDENTIF',
					'$NIT',
					'$DIG_VERIFIC_NIT',
					'$DIRECCION_1',
					'$DIRECCION_2',
					'$TELEFONO',
					'$EMAIL',
					'$ID_PAIS',
					'$ID_DEPTO',
					'$DESCRIP_DEPTO',
					'$ID_CIUDAD',
					'$DESCRIP_CIUDAD',
					'$NOM_CONTACTO',
					'$NOTAS',
					'$ESTADO_REG'		
				)";

	mysql_query($SQL,$link);
}
	ob_start();
	if ($client->fault) {		
		$status 	= 'false';
		$mensaje 	= 'Error de Cliente NuSoap (X002)';
		$error 		= 'Error<br /><br />';
		ob_start();	
		print_r($result);
		$error .= ob_get_contents(); ob_end_clean();
		
	} else {
		// XHEQUEAR ERRORES
		$err = $client->getError();
		if ($err) {
			$status 	= 'false';
			$mensaje 	= 'Error de Cliente NuSoap (X003)';
			$error 		= 'Error<br /><br />'.$err;	
			$error 		.= ob_get_contents(); ob_end_clean();
		} else {
			//MOSTRA RESULTADOS
			ob_start();	
			print_r($result);
			$mensaje .= ob_get_contents(); ob_end_clean();			
		}
	} 
	
if($log == 'true'){
	echo $mensaje.'<br />'.$error;
	//echo '<h2>Request</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
	//echo '<h2>Response</h2><pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
	echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->debug_str, ENT_QUOTES) . '</pre>';	
}else{
	echo $status.'{.}'.$mensaje.'{.}'.$error.'{.}'.$argumentos;
}



?>