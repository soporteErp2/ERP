<?php 
	require_once('../../configuracion/conectar.php');
	require_once('../../misc/nuSoap/nusoap.php');
	//error_reporting(E_ALL);

	$consul 	= mysql_query("SELECT * FROM interface_siesa_configuracion WHERE id_empresa = $_SESSION[EMPRESA]",$link);
	
	if(mysql_result($consul,0,'directorio')==""){$directorio = "";}else{$directorio = "<directorio>".mysql_result($consul,0,'directorio')."</directorio>";}

	$consul2 	= mysql_query("SELECT * FROM vista_rental_pedidos WHERE id = '$_POST[id_intercambio]'",$link);

	
	/////////////////////////////////////////////////////////////////////////////////////////////
	$url 		= mysql_result($consul,0,'url');
	$function 	= 'importar';
	$log 		= 'false';
	$repite		= '0';
	$numero_dias= '0';

	$FECHA_ESCRITURA	= "";
	if(mysql_result($consul2,0,'FECHA_ESCRITURA')!=""){
		$FECHA_ESCRITURA	="<FECHA_ESCRITURA>".mysql_result($consul2,0,'FECHA_ESCRITURA')."T00:00:00-00:00</FECHA_ESCRITURA>";
	}

	$FECHA_MATRICULA	= "";
	if(mysql_result($consul2,0,'FECHA_MATRICULA')!=""){
		$FECHA_MATRICULA	="<FECHA_MATRICULA>".mysql_result($consul2,0,'FECHA_MATRICULA')."T00:00:00-00:00</FECHA_MATRICULA>";
	}

	$argumentos	=	"<?xml version=\"1.0\" encoding=\"UTF-8\"?>
						<ws>
							<seguridad>
									<usuario>".mysql_result($consul,0,'usuario')."</usuario>
									<clave>".mysql_result($consul,0,'clave')."</clave>
									".$directorio."
							</seguridad>
							<importacion m=\"1\" o=\"1\">
								<datos>
									<contrato>
										<ID_CIA>".mysql_result($consul,0,'id_compania')."</ID_CIA>
										<NRO_CONTRATO>".mysql_result($consul2,0,'no_contrato')."</NRO_CONTRATO>
										<SECUENCIA_CONTRATO>0</SECUENCIA_CONTRATO>
										<ROWID_TERCERO>".mysql_result($consul2,0,'id_otros')."</ROWID_TERCERO>
										<NIT_CLIENTE>".mysql_result($consul2,0,'numero_identificacion')."</NIT_CLIENTE>
										<ID_SUCURSAL_CLIENTE>".mysql_result($consul2,0,'id_sucursal_otros')."</ID_SUCURSAL_CLIENTE>
										<ID_ESTADO_CONTRATO>01</ID_ESTADO_CONTRATO>
										<FECHA_INICIAL>".mysql_result($consul2,0,'fechai')."T00:00:00-00:00</FECHA_INICIAL>
										<FECHA_FINAL>".mysql_result($consul2,0,'fechaf')."T00:00:00-00:00</FECHA_FINAL>
										<NRO_ESCRITURA>".mysql_result($consul2,0,'NRO_ESCRITURA')."</NRO_ESCRITURA>
										$FECHA_ESCRITURA
										<NOTARIA>".mysql_result($consul2,0,'NOTARIA')."</NOTARIA>
										<CIUDAD_NOTARIA>".mysql_result($consul2,0,'CIUDAD_NOTARIA')."</CIUDAD_NOTARIA>
										<NRO_MATRICULA>".mysql_result($consul2,0,'NRO_MATRICULA')."</NRO_MATRICULA>
										$FECHA_MATRICULA
										<CIUDAD_MATRICULA>".mysql_result($consul2,0,'CIUDAD_MATRICULA')."</CIUDAD_MATRICULA>
										<TIPO_FACTURACION>".mysql_result($consul2,0,'facturacion')."</TIPO_FACTURACION>";
					
	$consul3 	= mysql_query("SELECT * FROM rental_pedido_requerimientos WHERE id_intercambio = '$_POST[id_intercambio]' AND tipo_requerimiento = 1 GROUP BY id_requerimiento",$link);

	while($row = mysql_fetch_array($consul3)){
		
		$ROWID = mysql_result(mysql_query("SELECT id_otros FROM rental_espacios WHERE id = $row[id_requerimiento]",$link),0,'id_otros');
		if($repite!=$ROWID){
			$argumentos	.=	"
										<inmueble>
											<ROWID_INMUEBLE>".$ROWID."</ROWID_INMUEBLE>
											<VALOR_CANON_INMUEBLE>".$row['subtotal_con_descuento']."</VALOR_CANON_INMUEBLE>
											<NOMBRE_COMERCIAL_INMUEBLE>".$row['requerimiento']."</NOMBRE_COMERCIAL_INMUEBLE>";
			if(mysql_result($consul2,0,'facturacion') == '1'){
				$argumentos	.=					"
												<dias>";
				$consul4 	= mysql_query("SELECT * FROM rental_pedido_requerimientos WHERE id_intercambio = '$_POST[id_intercambio]' AND tipo_requerimiento = 1 AND id_requerimiento=$row[id_requerimiento]",$link);
				while($row4 = mysql_fetch_array($consul4)){
						$argumentos	.=				"
													<FECHA_INICIAL>".$row4['fecha_i']."T00:00:00-00:00</FECHA_INICIAL>
													<FECHA_FINAL>".$row4['fecha_f']."T00:00:00-00:00</FECHA_FINAL>
													<PRECIO>".$row4['subtotal_con_descuento']."</PRECIO>
													<OBSERVACION>Obs:".$row4['observaciones_comerciales']."</OBSERVACION> ";
						$numero_dias += cuantos_dias($row4['fecha_i'],$row4['fecha_f']);
				}
				
				$argumentos	.=					"
												</dias>";
			}
			$argumentos	.=					"
											<NRO_DIAS>".$numero_dias."</NRO_DIAS>
										</inmueble>";
		}
	}
										
	$argumentos	.=	"
									</contrato>
								</datos>
							</importacion>
						</ws>				
					";
					
	$param = array("arg0"=>$argumentos);

	$status 	= 'true';
	$mensaje 	= '';
	$error 		= '';
	$ROW_ID		= 'false';	


	/////////////////////////////////////////////////////////////////////////////////////////////

	$client = new nusoap_client($url,'wsdl');
	$err = $client->getError();

	if($err){
		$status 	= 'false';
		$mensaje 	= 'Error Sincronizando con UnoEE (X001)';
		$error 		= 'Constructor error<br /><br />'.$err;
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
		
	if($log == 'true'){
		echo $mensaje.'<br />'.$error;
		echo '<h2>Request</h2><pre>'.$client->request . '</pre>';
		echo '<h2>Response</h2><pre>'.$client->response. '</pre>';
		echo '<h2>Debug</h2><pre>'.$client->debug_str.'</pre>';	
	}else{
		echo $status.'{.}'.$ROW_ID.'{.}'.$mensaje.'{.}'.$error."{.}".$argumentos;
	}



?>