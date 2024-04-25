<?php 

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");
error_reporting(E_ERROR | E_WARNING | E_PARSE);

include '../../../misc/ConnectDb/class.ConnectDb.php';
/**
 * 
 */
class erpAdmin 
{
	private $UsuarioDb  = 'root';
	private $PasswordDb = 'serverchkdsk';

	private $ServidorDb = 'localhost';
	private $NameDb     = 'erp_bd';

	// private $ServidorDb = 'localhost';
	// private $NameDb     = 'erp_acceso';

	
	function __construct()
	{
		$this->objConectDB = new ConnectDb(
			                       'MySql',
			                       $this->ServidorDb,
			                       $this->UsuarioDb,
			                       $this->PasswordDb,
			                       $this->NameDb
			                   );
		$this->mysql = $this->objConectDB->getApi();
		$this->link  = $this->mysql->conectar();
	}

	public function index($params = NULL)
	{
		$sql="SELECT *,(SELECT plan FROM planes WHERE id=id_plan) AS plan FROM host WHERE activo=1";
		$query=$this->mysql->query($sql,$this->mysql->link);
		while($data[]=$this->mysql->fetch_assoc($query));
		array_pop($data);
		return $data;
	}

	public function update($params)
	{
		$sql="UPDATE host SET fecha_vencimiento_plan = '$params[fecha]'
					WHERE activo=1
					AND id=$params[id] ";
		$query=$this->mysql->query($sql,$this->mysql->link);
		if(!$query) { return array('status'=>false,'detalle'=>$this->mysql->error());}
		return array('status'=>true);
	}

	public function apiResponse($response)
	{
	    $http_response_code = array(
	        100 => 'Continue',
	        101 => 'Switching Protocols',
	        200 => 'OK',
	        201 => 'Created',
	        202 => 'Accepted',
	        203 => 'Non-Authoritative Information',
	        204 => 'No Content',
	        205 => 'Reset Content',
	        206 => 'Partial Content',
	        300 => 'Multiple Choices',
	        301 => 'Moved Permanently',
	        302 => 'Found',
	        303 => 'See Other',
	        304 => 'Not Modified',
	        305 => 'Use Proxy',
	        306 => '(Unused)',
	        307 => 'Temporary Redirect',
	        400 => 'Bad Request',
	        401 => 'Unauthorized',
	        402 => 'Payment Required',
	        403 => 'Forbidden',
	        404 => 'Not Found',
	        405 => 'Method Not Allowed',
	        406 => 'Not Acceptable',
	        407 => 'Proxy Authentication Required',
	        408 => 'Request Timeout',
	        409 => 'Conflict',
	        410 => 'Gone',
	        411 => 'Length Required',
	        412 => 'Precondition Failed',
	        413 => 'Request Entity Too Large',
	        414 => 'Request-URI Too Long',
	        415 => 'Unsupported Media Type',
	        416 => 'Requested Range Not Satisfiable',
	        417 => 'Expectation Failed',
	        500 => 'Internal Server Error',
	        501 => 'Not Implemented',
	        502 => 'Bad Gateway',
	        503 => 'Service Unavailable',
	        504 => 'Gateway Timeout',
	        505 => 'HTTP Version Not Supported',
	    );
	    // header('HTTP/1.1 ' . $response['status'] . ' ' . $http_response_code[$response['status']]);
    	header('Content-Type: application/json; charset=utf-8');
		if (is_array($response['data'])) {
			foreach ($response['data'] as $key => $arrayResult) {
				if (is_array($arrayResult)) {
					foreach ($arrayResult as $campo => $valor) {
						if (!is_array($valor)) {
							$response['data'][$key][$campo]=utf8_encode($valor);
						}

					}
				}
			}
		}
		// print_r($response);
	    $json_response = json_encode($response['data']);
	    echo $json_response;
	    exit;
	}

}

	// OBJETO DE LA CLASE
	$obj    = new erpAdmin();
	$method = $_SERVER['REQUEST_METHOD'];
	$json   = file_get_contents('php://input');
	$data   = json_decode($json,true);
	// $data   = (array) $data;
	switch($method){
		/*
		 * Nota: Por regla el JSON debe estar aramado con comilla doble no simple para los string.
		*/

		case 'GET':
			if(empty($_SERVER['QUERY_STRING'])){
				$result=$obj->index();
				if(count($result)>0){
        			$response['status'] = 202;
        			$response['data']=$result;
				}else{
					$response['status'] = 404;
        			$response['data']=array('failure'=>'No hay informacion para mostrar');
				}
			}else{
				$result=$obj->show($_GET);
				if(count($result)>0){
        			$response['status'] = 202;
        			$response['data']=$result;
				}else{
					$response['status'] = 404;
        			$response['data']=array('failure'=>'No hay informacion para mostrar');
				}
			}
		break;

		case 'POST':
				$result =$obj->store($data);
				if($result['status']){
        			$response['status'] = 202;
        			$response['data']=array('success'=>'Informacion registrada');
				}else{
					$response['status'] = 400;
        			$response['data']=array('failure'=>'Ha ocurrido un error','detalle'=>$result['detalle']);
				}
			break;

		case 'PUT':
				$result =$obj->update($data);
				if($result){
        			$response['status'] = 202;
        			$response['data']=array('success'=>'Informacion actualizada');
				}else{
					$response['status'] = 500;
        			$response['data']=array('failure'=>'Ha ocurrido un error. La informacion no ha podido ser actualizada.');
				}
			break;

		case 'DELETE':
				$result =$obj->delete($data);
				if($result){
        			$response['status'] = 202;
        			$response['data']=array('success'=>'Informacion eliminada');
				}else{
					$response['status'] = 500;
        			$response['data']=array('failure'=>'Ha ocurrido un error','detalle'=>$result['detalle']);
				}
			break;

		default:
			$response['status'] = 405;
        	$response['data']=array('failure'=>'Metodo HTTP no configurado para respuesta.');
			break;
	}

	// print_r($response);
	$obj->apiResponse($response);