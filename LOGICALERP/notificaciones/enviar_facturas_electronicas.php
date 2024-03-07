<?php
	ini_set('max_execution_time', 300); //300 segundos o 5 minutos
	/**
	 * @class enviarFacturaElectronica Envio de facturas a la Dian
	 * @param obj $mysql Objeto de conexion mysql
	 */
	class enviarFacturaElectronica
	{

		public $mysql;

		/**
	 	 * @param obj $mysql Objeto de conexion mysql
		 */
		function __construct($mysql)
		{
			$this->mysql = $mysql;
		}

		/**
		 * consultar las empresas a depurar el envio
		 * @return arr arrayEmpresas Retornar array con todas las empresas a consultar de colombia
		 */
		public function getEmpresas()
		{
			$sql = "SELECT id,nombre,documento,decimales_moneda FROM empresas WHERE activo = 1 AND id_pais = '49' ";
			$query = $this->mysql->query($sql,$this->mysql->link);

			while($row = $this->mysql->fetch_array($query)){
				$arrayEmpresas[$row['id']] = array('documento' => $row['documento'], 'nombre' => $row['nombre'],'decimales_moneda' => $row['decimales_moneda']);
			}

			return $arrayEmpresas;
		}

		/**
		 * Consultar las resoluciones de facturacion electronica
		 * @param int id_empresa id de la empresa a realizar la consulta
		 */
		public function getResoluciones($id_empresa)
		{
			$arrayResoluciones = array();
			$sql = "SELECT * FROM ventas_facturas_configuracion
							WHERE activo = 1 AND id_empresa = $id_empresa AND tipo = 'FE' AND consecutivo_factura < numero_final_resolucion";
			$query = $this->mysql->query($sql,$this->mysql->link);

			while($row = $this->mysql->fetch_array($query)){
				$arrayResoluciones[$row['id']] = array(
															'prefijo'                   => $row['prefijo'],
															'consecutivo_resolucion'    => $row['consecutivo_resolucion'],
															'fecha_resolucion'          => $row['fecha_resolucion'],
															'numero_inicial_resolucion' => $row['numero_inicial_resolucion'],
															'numero_final_resolucion'   => $row['numero_final_resolucion'],
															'tipo'                      => $row['tipo'],
															'consecutivo_factura'       => $row['consecutivo_factura'],
														);
			}

			return $arrayResoluciones;
		}

		/**
		 * consultar las facturas a enviar a la Dian
		 * @param arr arrayResoluciones array con las resoluciones de las facturas a consultar
		 * @param int id_empresa id de la empresa a realizar la consulta
		 */
		public function getFacturas($arrayResoluciones,$id_empresa)
		{

			$whereIdResolucion = "";
			$arrayFacturas = array();
			foreach($arrayResoluciones as $id_resolucion => $values){
				$whereIdResolucion .= ($whereIdResolucion == '')? "id_configuracion_resolucion = $id_resolucion" : " OR id_configuracion_resolucion = $id_resolucion " ;
			}

			$fecha = date('Y-m-d');
			$nuevafecha = strtotime ( '-2 day' , strtotime ( $fecha ) ) ;
			$nuevafecha = date ( 'Y-m-d' , $nuevafecha );

			$sql = "SELECT id FROM ventas_facturas
					WHERE activo = 1
					AND id_empresa = $id_empresa
					AND estado = 1
					AND ($whereIdResolucion)
					AND (ISNULL(response_FE) OR response_FE = '' OR response_FE != 'Ejemplar recibido exitosamente pasara a verificacion')
					AND fecha_inicio <= '$nuevafecha'";
			$query = $this->mysql->query($sql,$this->mysql->link);

			while($row = $this->mysql->fetch_array($query)){
				$arrayFacturas[$row['id']] = $id_empresa;
			}

			return $arrayFacturas;
		}

		/**
		 * enviar las facturas a la Dian
		 * @param arr arrayFacturas array con las facturas a enviar
		 */
		public function sendFacturas($arrayFacturas)
		{
			foreach($arrayFacturas as $id_factura => $id_empresa){
				$facturaJSON = new ClassFacturaJSON($this->mysql);
				$facturaJSON->obtenerDatos($id_factura,$id_empresa);
				$facturaJSON->construirJSON();
				$result      = $facturaJSON->enviarJSON();
				// echo $result["id_factura"] . " - " . $result["comentario"];
				// echo "<br><br>------ENVIADO------<br><br>";

				$agregar     = array("'",".");
				$result      = str_replace($agregar,"",$result);

				$sql = "UPDATE
							ventas_facturas
						SET
							fecha_FE = CURRENT_DATE(),
							hora_FE = CURRENT_TIME(),
							response_FE = '$result[comentario]',
							UUID = '$result[id_factura]',
							id_usuario_FE = '',
							nombre_usuario_FE = 'Enviado automaticamente por el sistema',
							cedula_usuario_FE = 'Enviado automaticamente por el sistema'
						WHERE
							id = $id_factura";
				$query = $this->mysql->query($sql,$this->mysql->link);
			}
		}

		/**
		 * Iniciar el proceso de envio
		 */
		public function enviar()
		{
			session_start();
			$arrayEmpresas = $this->getEmpresas();
			// RECORRER CADA EMPRESA PARA ENVIAR LAS FACTURAS PENDIENTES
			foreach($arrayEmpresas as $id_empresa => $arrayResult){
				$_SESSION['DECIMALESMONEDA'] = $arrayResult['decimales_moneda'];
				$arrayResoluciones = $this->getResoluciones($id_empresa);

				if(empty($arrayResoluciones)){
					$log = 'No hay resoluciones configuradas para facturacion electronica';
					LogNotificacionesErp($log);
					continue;
				}

				$arrayFacturas = $this->getFacturas($arrayResoluciones,$id_empresa);

				if(empty($arrayFacturas)){
					$log = 'No hay facturas pendientes por enviar';
					LogNotificacionesErp($log);
					continue;
				}

				$this->sendFacturas($arrayFacturas);
				$log = 'Se enviaron Facturas';
				LogNotificacionesErp($log);
			}
			session_destroy();
		}

	}

	$server_name = $_SERVER['SERVER_NAME'];

	if($server_name == "logicalerp.localhost"){ //DESARROLLO
		$ip_conexion_db    = '192.168.8.202';
		$nombre_bd         = 'logicalsofterp';
		$url_class_connect = '../../misc/ConnectDb/class.ConnectDb.php';
		$url_class_factura = '../ventas/facturacion/bd/ClassFacturaJSON.php';
		$url_class_nusoap  = '../web_service/nuSoap/nusoap.php';
	}
	else{ //PRODUCCION
		$ip_conexion_db    = '127.0.0.1';
		$nombre_bd         = 'erp';
		$url_class_connect = '/WWW/ERP/misc/ConnectDb/class.ConnectDb.php';
		$url_class_factura = '/WWW/ERP/LOGICALERP/ventas/facturacion/bd/ClassFacturaJSON.php';
		$url_class_nusoap  = '/WWW/ERP/LOGICALERP/web_service/nuSoap/nusoap.php';
	}

	$conexionDB = $ip_conexion_db;
	$user       = 'root';
	$pass       = 'serverchkdsk';
	$bd         = $nombre_bd;

	include_once($url_class_connect);
	include_once($url_class_factura);

	//SI YA ESTA INCLUIDO EL NUSOAP
	if(!function_exists("timestamp_to_iso8601")){
		include_once($url_class_nusoap);
	}

	$objConectDB = new ConnectDb(
									"MySql",		// API SQL A UTILIZAR  MySql, MySqli
									"$conexionDB",	// SERVIDOR
									"$user",		// USUARIO DATA BASE
									"$pass",		// PASSWORD DATA BASE
									"$bd"			// NOMBRE DATA BASE
								);

	$mysql = $objConectDB->getApi();
	$link  = $mysql->conectar();

	// if(!isset($link)){
	// 	$link = mysql_connect($conexionDB,$user,$pass);
	// }
	// if(!$link){echo 'Error Conectando a Mysql<br />';};
	// mysql_select_db($bd,$link);
	// if(!@mysql_select_db($bd,$link)){echo 'Error Conectando a la la base de datos "'.$bd.'" <br />';};

	date_default_timezone_set("America/Bogota");
	$hora_notificacion = date("H:i");
	$hora_activacion1  = '02:45';
	$hora_activacion2  = '03:15';

	if($hora_notificacion >= $hora_activacion1 && $hora_notificacion <= $hora_activacion2){
		$log = 'Se inicio el JOB Enviar Facturas Electronicas';
		LogNotificacionesErp($log);
		$objeto = new enviarFacturaElectronica($mysql);
		$objeto->enviar();
		$log = 'Se termino el JOB Enviar Facturas Electronicas';
		LogNotificacionesErp($log);
	}
?>
