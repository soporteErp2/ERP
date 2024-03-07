<?php

/**
 * ClassExternalApis Clase para el manejo de apis externas a ERP
 */
class ClassExternalApis
{
	public $id_sucursal;
	public $id_empresa;
	public $mysql;
	public $arrayInfoEmpresa;

	function __construct($id_sucursal,$id_empresa,$mysql){
		$this->id_sucursal = $id_sucursal;
		$this->id_empresa  = $id_empresa;
		$this->mysql       = $mysql;
		$this->getEmpresaInfo();
	}

	/**
	 * getEmpresaInfo Consultar la informacion de la empresa
	 */
	public function getEmpresaInfo(){
		$sql   = "SELECT documento,nit_completo,nombre FROM empresas WHERE activo=1 AND id=$this->id_empresa";
		$query = $this->mysql->query($sql);
		$this->arrayInfoEmpresa['documento']    = $this->mysql->result($query,0,'documento');
		$this->arrayInfoEmpresa['nit_completo'] = $this->mysql->result($query,0,'nit_completo');
		$this->arrayInfoEmpresa['nombre']       = $this->mysql->result($query,0,'nombre');
	}

	public function getUserInfo(){
		$sql   = "SELECT token FROM empleados WHERE activo=1 AND id=$_SESSION[IDUSUARIO]";
		$query = $this->mysql->query($sql);
		return $this->mysql->result($query,0,'token');
	}

	/**
	 * getApiInfo Consultar la informacion del api a consumir
	 * @param  Int   $id_api Id del api a consultar
	 * @return Array         Lista con toda la informacion del api a consumir
	 */
	public function getApiInfo($id_api){
		$sql="SELECT
				request_url,
				request_method,
				authorization,
				request_url_callback,
				request_method_callback,
				authorization_callback
				FROM api_conections WHERE activo=1 AND id=$id_api ";
		$query=$this->mysql->query($sql);
		$arrayReturn = array(
								'request_url'             => $this->mysql->result($query,0,'request_url'),
								'request_method'          => $this->mysql->result($query,0,'request_method'),
								'authorization'           => $this->mysql->result($query,0,'authorization'),
								'request_url_callback'    => $this->mysql->result($query,0,'request_url_callback'),
								'request_method_callback' => $this->mysql->result($query,0,'request_method_callback'),
								'authorization_callback'  => $this->mysql->result($query,0,'authorization_callback'),
							);
		return $arrayReturn;
	}

	/**
	 * curl Funcion para consumo de api con curl
	 * @param  Array $params Array con los parametros necesarios para el consumo del api
	 * @param  String 		$params.Authorization Si la peticion lleva un header de autorizacion entonces se envia la cabcera completa
	 * @param  String 		$params.request_url Url del api a consumir
	 * @param  String 		$params.request_method Metodo a usar en el consumo del API (GET,POST,PUT,DELETE)
	 * @param  String 		$params.data Datos a enviar al Api
	 * @return Array 		Lista con la respuesta del consumo del api
	 */
	public function curlApi($params){
		$client = curl_init();
		$options = array(
							CURLOPT_HTTPHEADER     => array(
														'Content-Type: application/json',
														"$params[Authorization]"),
						    CURLOPT_URL            => "$params[request_url]",
						    CURLOPT_CUSTOMREQUEST  => "$params[request_method]",
						    CURLOPT_RETURNTRANSFER => true,
						    CURLOPT_POSTFIELDS     => $params['data'],
						    CURLOPT_SSL_VERIFYPEER => false,
						);
		curl_setopt_array($client, $options);
		$response = curl_exec($client);
		$curl_errors=curl_error($client);
		if(!empty($curl_errors)){
			$response['status']               = 'failed';
			$response['errors'][0]['titulo']  = curl_getinfo($client) ;
			$response['errors'][0]['detalle'] = curl_error($client);
			// return;
		}
		$httpCode = curl_getinfo($client, CURLINFO_HTTP_CODE);
		curl_close($client);
		return $response;
	}

	/**
	 * setCausacionIngreso Generar el proceso de causacion diaria de ingresos en el ERP
	 * @param Int  $id_api    Id del api a consumir
	 * @param Date $fecha_api Fecha del api (parametro del api, varia deacuerdo al api a consumir)
	 */
	public function setCausacion($id_api,$fecha_api,$tipo){
		$arrayApiInfo = $this->getApiInfo($id_api);
		$token = $this->getUserInfo();
		if ($token=='') {
			$params['status']               = 'failed';
			$params['errors'][0]['titulo']  = "Token Vacio" ;
			$params['errors'][0]['detalle'] = "El token de seguridad esta vacio, dirigase al modulo de empleados, seleccione su empleado y luego en el campo token genere uno";
			$this->bodyResponde($params);
			return;
		}

		// CONSUMIR LA PRIMER API
		// http://192.168.8.112:8000/api/cargosCausacionDiara/
		// 9005429756/2019-01-01/INGRESO
		// $this->arrayInfoEmpresa['documento1'] = 9005429756;

		$params['request_url']    = "$arrayApiInfo[request_url]".$this->arrayInfoEmpresa['documento']."/$fecha_api/$tipo";
		$params['request_method'] = $arrayApiInfo['request_method'];
		$params['Authorization']  = "";
		$params['data']           = "";
		$response = $this->curlApi($params);
		$arrayResponse = (is_array($response))? $response : json_decode($response,true) ;
		// echo $params['request_url'];
		// print_r($response);
		// var_dump($response);
		// print_r($arrayResponse);
		if ( count($arrayResponse['errors'])>0){
			foreach ($arrayResponse['errors'] as $key => $arrayResult){
				$arrayError[]=$arrayResult['message'];
			}
			$params['status']               = 'failed';
			$params['errors'][0]['titulo']  = "Api Error HOTELS" ;
			$params['errors'][0]['detalle'] = $arrayError;
			$this->bodyResponde($params);
			return;
		}
		// echo count($arrayResponse['cargos']);
		// RECORRER LAS CUENTAS RECIBIDAS POR HOTELS PARA CREAR EL CUERPO DE LA NOTA CONTABLE
		if (count($arrayResponse['cargos'])<=0) {
			$params['status']               = 'failed';
			$params['errors'][0]['titulo']  = "Api HOTELS" ;
			$params['errors'][0]['detalle'] = "Api HOTELS no hay cuentas para insertar en esa fecha";
			$params['errors'][0]['debug']   = $arrayResponse;
			$this->bodyResponde($params);
			return;
		}

		// print_r($arrayResponse);
		// ARMAR EL CUERPO DE CUENTAS
		foreach ($arrayResponse['cargos'] as $cuenta => $arrayTercerosAccounts) {
			foreach ($arrayTercerosAccounts as $docTercero => $arrayResult) {
				$arrayData[] = array(
									"cuenta"            => $cuenta,
									"debito"            => "$arrayResult[debito]",
									"credito"           => "$arrayResult[credito]",
									"documento_tercero" => $docTercero,
									);
			}
		}

		// print_r($arrayData);

		// CONSULTAR LA SEGUNDA API PARA INSERTAR LAS NOTAS
		$params = '';
		$params['request_url']    = $arrayApiInfo['request_url_callback'];
		$params['request_method'] = $arrayApiInfo['request_method_callback'];
		$params['Authorization']  = "Authorization: $arrayApiInfo[authorization_callback] ".base64_encode("$_SESSION[NOMBREUSUARIO]:$token:".$this->arrayInfoEmpresa['documento']);
		$data = array(
						"fecha_documento"   => $fecha_api,
						"documento_tercero" => $this->arrayInfoEmpresa['documento'],
						"consecutivo"       => str_replace("-", "", $fecha_api),
						"tipo"              => "$tipo",
						"id_sucursal"       => $this->id_sucursal,
						"observacion"       => "",
						"cuentas"           => $arrayData
					);
		$params['data'] = json_encode($data);
		$response = $this->curlApi($params);

		// print_r($response);

		$arrayResponse = (is_array($response))? $response : json_decode($response,true) ;
		if ($arrayResponse['failure']<>'' || $arrayResponse['status']=='failed'){
			$params['status']               = 'failed';
			$params['errors'][0]['titulo']  = "Api Error ERP" ;
			$params['errors'][0]['detalle'] = $arrayResponse['detalle'];
			$this->bodyResponde($params);
			return;
		}

		// print_r($arrayResponse);
		// return $response;

		$params['status']               = 'success';
		$params['detail'][0]['titulo']  = $arrayResponse['consecutivo'] ;
		$params['detail'][0]['detalle'] = $arrayResponse['success'];
		$this->bodyResponde($params);


		// print_r($arrayApiInfo);
		// echo $_SESSION['EMPRESA'];
		// NOMBREUSUARIO
		//
	}

	/**
	 * setCausacionReversion Generar el proceso de causacion diaria de ingresos en el ERP
	 * @param Int  $id_api    Id del api a consumir
	 * @param Date $fecha_api Fecha del api (parametro del api, varia deacuerdo al api a consumir)
	 */
	public function setCausacionReversion($id_api,$fecha_api){
		$arrayApiInfo = $this->getApiInfo($id_api);
		$token = $this->getUserInfo();
		if ($token=='') {
			$params['status']               = 'failed';
			$params['errors'][0]['titulo']  = "Token Vacio" ;
			$params['errors'][0]['detalle'] = "El token de seguridad esta vacio, dirigase al modulo de empleados, seleccione su empleado y luego en el campo token genere uno";
			$this->bodyResponde($params);
			return;
		}

		// CONSUMIR LA PRIMER API
		$params['request_url']    = $arrayApiInfo['request_url'];
		$params['request_method'] = $arrayApiInfo['request_method'];
		$params['Authorization']  = "Authorization: $arrayApiInfo[authorization] ".base64_encode("$_SESSION[NOMBREUSUARIO]:$token:".$this->arrayInfoEmpresa['documento']);
		$data = array(
						"fecha_documento"   => $fecha_api,
						"documento_tercero" => $this->arrayInfoEmpresa['documento'],
						"consecutivo"       => str_replace("-", "", $fecha_api),
						"tipo"              => "REVERSION",
						"id_sucursal"       => $this->id_sucursal,
						"observacion"       => "",
						"cuentas"           => array(
														'0' => array(
																		"cuenta"      => "11050501",
																		"debito"      => "1",
																		"credito"     => "0",
																		"observacion" => "obs de prueba cuenta"
																	),
														'1' => array(
																		"cuenta" => "11050502",
										 								"debito" => "0",
										 								"credito"=> "1"
																	),
													),
					);
		$params['data'] = json_encode($data);
		$response = $this->curlApi($params);

		// // print_r($options);
		$arrayResponse = json_decode($response,true);
		if ($arrayResponse['failure']<>'' || $arrayResponse['status']=='failed') {
			$params['status']               = 'failed';
			$params['errors'][0]['titulo']  = "Api Error" ;
			$params['errors'][0]['detalle'] = $arrayResponse['detalle'];
			// print_r($arrayResponse);
			$this->bodyResponde($params);
			return;
		}

		// print_r($response);
		// print_r($arrayResponse);
		// return $response;

		$params['status']               = 'success';
		$params['detail'][0]['titulo']  = $arrayResponse['consecutivo'] ;
		$params['detail'][0]['detalle'] = $arrayResponse['success'];
		$this->bodyResponde($params);


		// print_r($arrayApiInfo);
		// echo $_SESSION['EMPRESA'];
		// NOMBREUSUARIO
		//
	}

	public function bodyResponde($params){

		if ($params['status']=='success') {
			foreach ($params['detail'] as $key => $message) {

				$bodyError .= "<div class='row'>
								<!-- <div class='cell' data-col='1'></div> -->
								<div class='cell' data-col='2'><b>$message[titulo]</b></div>
								<div class='cell' data-col='3'>$message[detalle]</div>
							</div>";
			}
			?>
			<style>
				.content-grilla-filtro .body {height: 100% !important; }
				.sub-content[data-position="right"]{width: 100%; height: 100%; }
				.content-grilla-filtro .row {height: auto !important;}
			    .content-grilla-filtro .cell[data-col="1"]{width: 2px; height: auto;}
			    .content-grilla-filtro .cell[data-col="2"]{width: 90px; height: auto; border-right: none;}
			    .content-grilla-filtro .cell[data-col="3"]{width: 250px; height: auto; white-space: initial; border-left: 1px solid rgb(221, 221, 221);}
			    .content-grilla-filtro .cell[data-col="4"]{width: 211px; height: auto;}
			    .sub-content [data-width="input"]{width: 120px;}
			</style>

			<div class="main-content" style="height: 100%;overflow-y: hidden;overflow-x: hidden;">
				<div class="sub-content" data-position="right">
					<div class="title">SINCRONIZACION EXITOSA</div>
					<div class="content-grilla-filtro">
			            <div class="head">
			                <!-- <div class="cell" data-col="1"></div> -->
			                <div class="cell" data-col="2">Documento</div>
			                <div class="cell" data-col="3">Detalle</div>
			            </div>
			            <div class="body" id="body_grilla_filtro">
			            	<?php echo $bodyError ?>
						</div>
					</div>

				</div>
			</div>
		<?php
		}
		if ($params['status']=='failed') {
			foreach ($params['errors'] as $key => $message) {

				if(is_array($message['detalle'])){
					$bodyError .= "<div class='row'>
									<div class='cell' data-col='2'><b>$message[titulo]</b></div>";
					foreach ($message['detalle'] as $key => $arrayResult) {
						$bodyError .= "$space
										<div class='cell' data-col='3'>$arrayResult</div> ";
						$space = "<div class='cell' data-col='2'></div>";
					}
					$bodyError .= "</div>";
				}
				else{
					$bodyError .= "<div class='row'>
										<div class='cell' data-col='2'><b>$message[titulo]</b></div>
										<div class='cell' data-col='3'>$message[detalle]</div>
									</div>";
				}

			}
			?>
			<style>
				/*.content-grilla-filtro .body {height: 100% !important; }*/
				.sub-content[data-position="right"]{width: 100%; height: 100%; }
				.content-grilla-filtro .row {height: auto !important;}
			    .content-grilla-filtro .cell[data-col="1"]{width: 2px; height: auto;}
			    .content-grilla-filtro .cell[data-col="2"]{width: 90px; height: auto; border-right: none;}
			    .content-grilla-filtro .cell[data-col="3"]{width: 239px; height: auto; white-space: initial; border-left: 1px solid rgb(221, 221, 221);}
			    .content-grilla-filtro .cell[data-col="4"]{width: 211px; height: auto;}
			    .sub-content [data-width="input"]{width: 120px;}
			</style>

			<div class="main-content" style="height: 100%;overflow-y: hidden;overflow-x: hidden;">
				<div class="sub-content" data-position="right">
					<div class="title">ERRORES EN LA SINCRONIZACION</div>
					<div class="content-grilla-filtro">
			            <div class="head">
			                <!-- <div class="cell" data-col="1"></div> -->
			                <div class="cell" data-col="2">Error generado</div>
			                <div class="cell" data-col="3">Detalle del error</div>
			            </div>
			            <div class="body" id="body_grilla_filtro">
			            	<?php echo $bodyError ?>
						</div>
					</div>

				</div>
			</div>
		<?php
		}
	}


}

 ?>