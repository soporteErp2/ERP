<?php 
require_once('../../configuracion/conectar.php');
require_once('../../misc/nuSoap/nusoap.php');
error_reporting(E_ALL);

$consul = mysql_query("SELECT * FROM interface_siesa_configuracion WHERE id_empresa = $_SESSION[EMPRESA]",$link);
if(mysql_result($consul,0,'directorio')==""){$directorio = "";}else{$directorio = "<directorio>".mysql_result($consul,0,'directorio')."</directorio>";}

$UltimaActualizacion = str_replace("-","", mysql_result( mysql_query("SELECT inmuebles FROM interface_siesa_syncode WHERE id = $_SESSION[EMPRESA]",$link) ,0,'inmuebles') );
$UltimaActualizacion = '20120101';
$Hoy = date("Ymd");

/////////////////////////////////////////////////////////////////////////////////////////////
$url = mysql_result($consul,0,'url');
$function = 'consultar';
$log = 'false';
$bd_intercambio	= 'interface_siesa_inmuebles';
$argumentos	=	"<?xml version=\"1.0\" encoding=\"UTF-8\"?>
				<ws>
					<seguridad>
						<usuario>".mysql_result($consul,0,'usuario')."</usuario>
						<clave>".mysql_result($consul,0,'clave')."</clave>
						".$directorio."
					</seguridad>
					<consulta>
						<proveedor>".mysql_result($consul,0,'proveedor')."</proveedor>
						<nombre>INMUEBLES</nombre>
						<parametros>
							<p_id_cia>".mysql_result($consul,0,'id_compania')."</p_id_cia>
							<p_fec_ini_modif>".$UltimaActualizacion."</p_fec_ini_modif>
							<p_fec_fin_modif>".$Hoy."</p_fec_fin_modif>
						</parametros>
					</consulta>
				</ws>";
	

//echo $argumentos;
$param = array("arg0"=>$argumentos);

/////////////////////////////////////////////////////////////////////////////////////////////

$status 	= 'true';
$mensaje 	= '';
$error 		= '';	
$client = new nusoap_client($url,'wsdl');
$err = $client->getError();
if($err){
	$status 	= 'false';
	$mensaje 	= 'Error Sincronizando con UnoEE (X001)';
	$error 		= 'Constructor error<br /><br />'.$err;
}
$result = $client->call($function, $param);

//$xml = simplexml_load_string($result,NULL, LIBXML_NOCDATA); //LEE EL STRIG CON LA ETIQUETA CDATA Y LO CONVIERTE EN ARRAY
libxml_use_internal_errors(true);
try {
	$result = new SimpleXMLElement(utf8_encode($result),LIBXML_NOCDATA); //LEE EL STRIG CON LA ETIQUETA CDATA Y LO CONVIERTE EN ARRAY
} catch (Exception $e) {
	echo $e; 
}
mysql_query("TRUNCATE TABLE $bd_intercambio",$link);
mysql_query("UPDATE interface_siesa_syncode SET inmuebles = NOW() WHERE id = $_SESSION[EMPRESA]",$link);

for ($i=0;$i<count($result->rs[0]->r);$i++){
	
	$ROWID_INMUEBLE			=	$result->rs[0]->r[$i]->ROWID_INMUEBLE;
	$ID_INMUEBLE			=	$result->rs[0]->r[$i]->ID_INMUEBLE;
	$DESCRIPCION			=	utf8_decode($result->rs[0]->r[$i]->DESCRIPCION);
	$ID_UBICACION			=	$result->rs[0]->r[$i]->ID_UBICACION;
	$UBICACION				=	utf8_decode($result->rs[0]->r[$i]->UBICACION);
	$ID_TIPO_INMUEBLE		=	$result->rs[0]->r[$i]->ID_TIPO_INMUEBLE;
	$TIPO_INMUEBLE			=	utf8_decode($result->rs[0]->r[$i]->TIPO_INMUEBLE);
	$ID_TIPO_PROPIEDAD		=	$result->rs[0]->r[$i]->ID_TIPO_PROPIEDAD;
	$TIPO_PROPIEDAD			=	utf8_decode($result->rs[0]->r[$i]->TIPO_PROPIEDAD);
	$ID_ESTADO_INMUEBLE		=	$result->rs[0]->r[$i]->ID_ESTADO_INMUEBLE;
	$ESTADO_INMUEBLE		=	$result->rs[0]->r[$i]->ESTADO_INMUEBLE;
	$VLR_CONTRAPRESTACION	=	$result->rs[0]->r[$i]->VLR_CONTRAPRESTACION;
	$AREA					=	$result->rs[0]->r[$i]->AREA;
	$NOTAS					=	utf8_decode($result->rs[0]->r[$i]->NOTAS);
	$ESTADO_REGISTRO		=	$result->rs[0]->r[$i]->ESTADO_REGISTRO;
	
	$SQL =  ' INSERT INTO '.$bd_intercambio.'(
					id_empresa,
					id_sucursal,
					ROWID_INMUEBLE,
					ID_INMUEBLE,
					DESCRIPCION,
					ID_UBICACION,
					UBICACION,
					ID_TIPO_INMUEBLE,
					TIPO_INMUEBLE,
					ID_TIPO_PROPIEDAD,
					TIPO_PROPIEDAD,
					ID_ESTADO_INMUEBLE,
					ESTADO_INMUEBLE,
					VLR_CONTRAPRESTACION,
					AREA,
					NOTAS,
					ESTADO_REGISTRO
				)VALUES(
					'.$_SESSION['EMPRESA'].',
					'.$_SESSION['SUCURSAL'].',
					"'.$ROWID_INMUEBLE.'",
					"'.$ID_INMUEBLE.'",
					"'.$DESCRIPCION.'",
					"'.$ID_UBICACION.'",
					"'.$UBICACION.'",
					"'.$ID_TIPO_INMUEBLE.'",
					"'.$TIPO_INMUEBLE.'",
					"'.$ID_TIPO_PROPIEDAD.'",
					"'.$TIPO_PROPIEDAD.'",
					"'.$ID_ESTADO_INMUEBLE.'",
					"'.$ESTADO_INMUEBLE.'",
					"'.$VLR_CONTRAPRESTACION.'",
					"'.$AREA.'",
					"'.$NOTAS.'",
					"'.$ESTADO_REGISTRO.'"			
				)';
	//echo $SQL."<br><br>";
	mysql_query($SQL,$link);
}


if($log == 'true'){
	if ($client->fault) {
		echo '<h2>Fault</h2><pre>';
		//print_r($result);
		echo '</pre>';
	} else {
		// XHEQUEAR ERRORES
		$err = $client->getError();
		if ($err) {
			//MOSTAR ERRORES
			echo '<h2>Error</h2><pre>' . $err . '</pre>';
		} else {
			//MOSTRA RESULTADOS
			echo '<h2>Result</h2><pre>';
			print_r($result);
			echo '</pre>';
		}
	}
	//echo '<h2>Request</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
	//echo '<h2>Response</h2><pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
	echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->debug_str, ENT_QUOTES) . '</pre>';	
}else{
	echo $status.'{.}'.$mensaje.'{.}'.$error.'{.}'.$argumentos;
}

?>