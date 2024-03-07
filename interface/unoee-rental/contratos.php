<?php 
require_once('../../configuracion/conectar.php');
require_once('../../misc/nuSoap/nusoap.php');
error_reporting(E_ALL);

//$UltimaActualizacion = str_replace("-","", mysql_result( mysql_query("SELECT contratos FROM interface_siesa_syncode WHERE id = 1",$link) ,0,'contratos') );
$UltimaActualizacion = '20130101';
$Hoy = date("Ymd");
$consul = mysql_query("SELECT * FROM interface_siesa_configuracion WHERE id_empresa = $_SESSION[EMPRESA]",$link);
if(mysql_result($consul,0,'directorio')==""){$directorio = "";}else{$directorio = "<directorio>".mysql_result($consul,0,'directorio')."</directorio>";}

/////////////////////////////////////////////////////////////////////////////////////////////
$url = 'http://96.127.139.106:8081/siesa/ws?WSDL';
$function = 'consultar';
$log = 'true';
$bd_intercambio	= 'interface_siesa_contratos';

$argumentos = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
						<ws>
							<seguridad>
								<usuario>".mysql_result($consul,0,'usuario')."</usuario>
								<clave>".mysql_result($consul,0,'clave')."</clave>
								".$directorio."
							</seguridad>
							<consulta>
								<proveedor>".mysql_result($consul,0,'proveedor')."</proveedor>
								<nombre>CONTRATOS</nombre>
								<parametros>
									<p_id_cia>".mysql_result($consul,0,'id_compania')."</p_id_cia>
									<p_fec_ini_modif>".$UltimaActualizacion."</p_fec_ini_modif>
									<p_fec_fin_modif>".$Hoy."</p_fec_fin_modif>
								</parametros>
							</consulta>
						</ws>";

$param = array("arg0"=>$argumentos);
/////////////////////////////////////////////////////////////////////////////////////////////

$client = new nusoap_client($url,'wsdl');
$err = $client->getError();

if ($err) {
	echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
}
$result = $client->call($function, $param);

//$xml = simplexml_load_string($result,NULL, LIBXML_NOCDATA);
	$result = new SimpleXMLElement(utf8_encode($result),LIBXML_NOCDATA); //LEE EL STRIG CON LA ETIQUETA CDATA Y LO CONVIERTE EN ARRAY

		ob_start();
		if ($client->fault) {		
			$status 	= 'false';
			$mensaje 	= 'Error de Cliente NuSoap (X002)';
			$error 		= 'Error<br /><br />';
			ob_start();	
			print_r($result);
			$error .= ob_get_contents(); ob_end_clean();
			
		} else {
			// CHEQUEAR ERRORES
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
				$error = $result->error;
				$mensaje .= ob_get_contents(); ob_end_clean();
				$ROW_ID = $result->rs[0]->resultado[0]->contrato[0]->rowid;			
			}
		}

for ($i=0;$i<count($result->rs[0]->r);$i++){
	
	/*echo  $result->rs[0]->r[$i]->ROWID_INMUEBLE.'<br/>';
	echo  $result->rs[0]->r[$i]->ID_INMUEBLE.'<br/>';
	echo  $result->rs[0]->r[$i]->DESCRIPCION.'<br/>';
	echo  $result->rs[0]->r[$i]->ID_UBICACION.'<br/>';
	echo  $result->rs[0]->r[$i]->UBICACION.'<br/>';
	echo  $result->rs[0]->r[$i]->ID_TIPO_INMUEBLE.'<br/>';
	echo  $result->rs[0]->r[$i]->TIPO_INMUEBLE.'<br/>';
	echo  $result->rs[0]->r[$i]->ID_TIPO_PROPIEDAD.'<br/>';
	echo  $result->rs[0]->r[$i]->TIPO_PROPIEDAD.'<br/>';
	echo  $result->rs[0]->r[$i]->ID_ESTADO_INMUEBLE.'<br/>';
	echo  $result->rs[0]->r[$i]->ESTADO_INMUEBLE.'<br/>';
	echo  $result->rs[0]->r[$i]->VLR_CONTRAPRESTACION.'<br/>';
	echo  $result->rs[0]->r[$i]->AREA.'<br/>';
	echo  $result->rs[0]->r[$i]->NOTAS.'<br/>';
	echo  $result->rs[0]->r[$i]->ESTADO_REGISTRO.'<br/>';*/
}


if($log == 'true'){
		echo $mensaje.'<br />'.$error;
		echo '<h2>Request</h2><pre>'.$client->request . '</pre>';
		echo '<h2>Response</h2><pre>'.$client->response. '</pre>';
		echo '<h2>Debug</h2><pre>'.$client->debug_str.'</pre>';	
	}else{
		echo $status.'{.}'.$ROW_ID.'{.}'.$mensaje.'{.}'.$error."{.}".$argumentos;
	}



echo '<br /><br />Finalizado';

?>