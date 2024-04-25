<?php
	require '../../../misc/ConnectDb/class.ConnectDb.php';

/**
 * mainApi clase principal para heredar metodos reusables
 */
class mainApi extends ConnectDb
{
	protected $data;
	protected $table;
	protected $dataTable;
	protected $mysql;
	protected $nit_empresa;
	protected $id_sucursal;
	protected $nombre_sucursal;
	protected $id_bodega;
	protected $nombre_bodega;
	protected $id_empresa;
	protected $nombre_empresa;
	protected $id_usuario;
	protected $tipo_doc_usuario;
	protected $documento_usuario;
	protected $nombre_usuario;
	protected $camposTable;

	protected $UsuarioDb  = 'root';
	protected $PasswordDb = 'serverchkdsk';

	// CONEXION DESARROLLO
	protected $ServidorDb = '192.168.8.202';
	protected $NameDb     = 'erp_bd';

	// CONEXION PRODUCCION
	// protected $ServidorDb = 'localhost';
	// protected $UsuarioDb  = 'root';
	// protected $PasswordDb = 'serverchkdsk';
	// protected $NameDb     = 'erp_acceso';


	function __construct(){
		$this->conexion();
	}

	public function conexion(){
		parent::__construct("MySql",$this->ServidorDb,$this->UsuarioDb,$this->PasswordDb,$this->NameDb);
		$this->mysql = $this->getApi();
		$link        = $this->mysql->conectar();
	}

	public function accessControlOrigin(){
		header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE');
		// @$origin = $_SERVER["HTTP_ORIGIN"];
		$allowed_domains = ["http://www.example.com"];
		// if(in_array($origin, $allowed_domains)) {
		// 	header("Access-Control-Allow-Origin: " . $origin);
		// }
	}

	/**
	 * [authentication metodo para autenticar la sesion en ERP]
	 * Este metodo es usada para validar que se envie el usuario, token, nit de la empresa, id de la sucursal, id de la bodega
	 * ademas de validar que los datos sean correctos y existan en el sistema ERP, este metodo no retorna valores pero si asigna
	 * valores a varibale necesarias para continuar el proceso del API
	 *
	 */
	public function authentication(){
		if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) || $_SERVER['PHP_AUTH_USER']=='' || $_SERVER['PHP_AUTH_PW']=='') {
			$this->response(array('status' => 401,'data'=> 'Datos de autenticacion incompletos'));
		}
		else{
			$arrayExplode      = explode(":", $_SERVER['PHP_AUTH_PW']);
			$token             = $arrayExplode[0];
			$this->nit_empresa = $arrayExplode[1];
			// $this->id_sucursal = $arrayExplode[2];
			// $this->id_bodega   = $arrayExplode[3];

		    // echo "<p>Hola {$_SERVER['PHP_AUTH_USER']} - {$_SERVER['PHP_AUTH_PW']} = $token - $this->nit_empresa </p>";
			$sql   = "SELECT id,servidor,bd FROM host WHERE activo=1 AND nit=$this->nit_empresa ";
			$query = $this->mysql->query($sql,$this->mysql->link);
			$rows  = $this->mysql->num_rows($query);
			if ($rows==0) {
				$this->response(array('status' => 401,'data'=> 'La empresa no existe en el sistema'));
			}
			$this->ServidorDb = $this->mysql->result($query,0,'servidor');
			$this->NameDb     = $this->mysql->result($query,0,'bd');

			$this->conexion();

			$sql="SELECT id,nombre FROM empresas WHERE activo=1 AND documento=$this->nit_empresa";
			$query=$this->mysql->query($sql,$this->mysql->link);
			$this->id_empresa     = $this->mysql->result($query,0,'id');
			$this->nombre_empresa = $this->mysql->result($query,0,'nombre');

			// $sql="SELECT id,nombre,id_sucursal,sucursal FROM empresas_sucursales_bodegas WHERE activo=1 AND id_empresa=$this->id_empresa";
			// $query=$mysql->query($sql,$mysql->link);
			// $nombre_bodega   = $this->mysql->result($query,0,'nombre');
			// $nombre_sucursal = $this->mysql->result($query,0,'sucursal');
			// if ($id_sucursal <> $this->mysql->result($query,0,'id_sucursal') ) {
			// 	$this->response(array('status' => 401,'data'=> 'La sucursal no existe en el sistema'));
			// }
			// if ($id_bodega <> $this->mysql->result($query,0,'id') ) {
			// 	$this->response(array('status' => 401,'data'=> 'La bodega no existe en el sistema'));
			// }

			$sql="SELECT id,tipo_documento_nombre,documento,nombre,token FROM empleados WHERE activo=1 AND id_empresa=$this->id_empresa AND username='$_SERVER[PHP_AUTH_USER]'";
			$query=$this->mysql->query($sql,$this->mysql->link);
			$rows  = $this->mysql->num_rows($query);
			if ($rows==0) {
				$this->response(array('status' => 401,'data'=> 'El usuario no existe en el sistema'));
			}

			$this->id_usuario        = $this->mysql->result($query,0,'id');
			$this->tipo_doc_usuario  = $this->mysql->result($query,0,'tipo_documento_nombre');
			$this->documento_usuario = $this->mysql->result($query,0,'documento');
			$this->nombre_usuario    = $this->mysql->result($query,0,'nombre');

			if ($token<>$this->mysql->result($query,0,'token')){
				$this->response(array('status' => 401,'data'=> 'Error, token invalido'));
			}

			echo "Auth OK !";

		}
	}

	public function response($response){

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

	    header('HTTP/1.1 ' . $response['status'] . ' ' . $http_response_code[$response['status']]);
	    header('Content-Type: application/json; charset=utf-8');
	    $json_response = json_encode($response['data']);
	    echo $json_response;
	    exit;
	}

	/**
	 * [index description]
	 * @return [type] [description]
	 */
	public function index(){
		// var_dump($this->mysql);
		$camposSelect = '';
		$whereSelect  = '';
		foreach ($this->camposTable as $key => $arrayResult) {
			$camposSelect .= ($arrayResult['alias']=='')? "$arrayResult[campo]," : "$arrayResult[campo] AS $arrayResult[alias],";
		}
		// print_r($this->camposTable);
		$sql="SELECT $camposSelect FROM $this->table WHERE activo=1 $whereSelect";
		$query=$this->mysql->query($sql,$this->mysql->link);
		echo "$camposSelect -- <br>";
		// while ( <= 10) {
		// 	# code...
		// }

		// $activeColumn = (isset($activeColumn))? $activeColumn.' = "'.$activeValue.'"' : '';
		// $w='';
		// foreach ($where as $key => $value) {
		// 	$w.=$key.'='.$value.' AND';
		// }
		// $w      = 'WHERE '.substr($w, 0, -3);
		// $query  = 'SELECT * FROM '.$this->tables[$this->app][$table].' '.$w;
		// $result = $this->mysql->query($query);
		// $i=0;
		// while ($row = $this->mysql->fetch_assoc($result)) {
		// 	foreach ($row as $key => $value) {
		// 		$data[$i][$key] = $value;
		// 	}
		// 	$i++;
		// }
		// return $data;
	}

	/**
	 * [show Retorna un resulado]
	 */
	public function show(){
		echo "show";
		// $query  = 'SELECT * FROM '.$this->tables.' WHERE id='.$id;
		// $result = $this->mysql->query($query);
		// while($data[] = $this->mysql->fetch_assoc($result));
		// return $data;
	}

	/**
	 * [store Inserta los datos pasados por parametros]
	 */
	public function store(){
		// $colums = '';
		// $values = '';
		// foreach ($data as $column => $value) {
		// 	$colums.=$column.',';
		// 	$values.='"'.$value.'",';
		// }
		// $columns= substr($colums, 0, -1);
		// $values = substr($values, 0, -1);
		// $query  = 'INSERT INTO '.$this->tables[$this->app][$table].' ('.$colums.') VALUES ('.$values.')';
		// $result = $this->mysql->query($query);
		// if($this->mysql->affected_rows($result)){
		// 	$data['response'] =true;
		// 	$data['status']   =202;
		// }else{
		// 	$data['response'] =false;
		// 	$data['status']   =505;
		// }
		// return $data;
	}

	/**
	 * [update Actualiza los datos dados por parametros al registro indicado]
	 */
	public function update(){
		// $colums = '';
		// foreach ($data as $column => $value) {
		// 	$colums.= $column.'="'.$value.'",';
		// }
		// $columns = substr($colums, 0, -1);
		// $query   = 'UPDATE '.$this->tables[$this->app][$table].' SET '.$columns.' WHERE id='.$id;
		// $result  = $this->mysql->query($query);
		// if($this->mysql->affected_rows($result)){
		// 	$data['response'] =true;
		// 	$data['status']   =202;
		// }else{
		// 	$data['response'] =false;
		// 	$data['status']   =505;
		// }
		// return $data;
	}

	/**
	 * [delete borra de la tabla el registro indicado por parametro]
	 */
	public function delete(){
		// $query  = 'DELETE FROM '.$this->$tables[$this->app][$table].' WHERE id='.$id;
		// $result = $this->mysql->query($query);
		// if($this->mysql->affected_rows($result)){
		// 	$data['response'] =true;
		// 	$data['status']   =202;
		// }else{
		// 	$data['response'] =false;
		// 	$data['status']   =505;
		// }
		// return $data;
	}

}

?>