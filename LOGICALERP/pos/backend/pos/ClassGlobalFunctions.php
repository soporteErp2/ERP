<?php

	// error_reporting(E_ERROR | E_PARSE);

	/**
	 * ClassGlobalFunctions Clase con funciones globales a ser reutilizadas
	 */
	class ClassGlobalFunctions
	{

		public $id_sucursal;
		public $id_empresa;
		public $mysql;
		// public $apiHotels = array( 'url' => "http://192.168.8.181:8000/api/" );
		public $apiHotels = array( 'url' => "https://logicalhotels.com/api/" );


		function __construct($id_sucursal,$id_empresa,$mysql){
			$this->id_sucursal = $id_sucursal;
			$this->id_empresa  = $id_empresa;
			$this->mysql       = $mysql;
		}


		/**
		 * getInfoEmpresa Consultar el nit de la empresa
		 * @return String nit de la empresa
		 */
		public function getInfoEmpresa(){
			$sql="SELECT documento,nombre,id_pais,pais,id_departamento,departamento,id_ciudad,ciudad,direccion,telefono FROM empresas
					WHERE activo=1 AND id=$this->id_empresa ";
			$query=$this->mysql->query($sql);
			$array['documento']    = $this->mysql->result($query,0,'documento');
			$array['nombre']       = $this->mysql->result($query,0,'nombre');
			$array['id_pais']         = $this->mysql->result($query,0,'id_pais');
			$array['pais']         = $this->mysql->result($query,0,'pais');
			$array['id_departamento'] = $this->mysql->result($query,0,'id_departamento');
			$array['departamento'] = $this->mysql->result($query,0,'departamento');
			$array['ciudad']       = $this->mysql->result($query,0,'ciudad');
			$array['id_ciudad']       = $this->mysql->result($query,0,'id_ciudad');
			$array['direccion']    = $this->mysql->result($query,0,'direccion');
			$array['telefono']     = $this->mysql->result($query,0,'telefono');

			return $array;
		}

		/**
		 * randomico Generar ramdon unico
		 * @return String Cadena de texto con un valor aleatorio
		 */
		public function randomico(){
			$random1 = mktime();             //GENERA PRIMERA PARTE DEL ID UNICO
	        $chars = array(
	                'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H',
	                'I', 'J', 'K', 'L', 'M', 'N', 'O',
	                'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W',
	                'X', 'Y', 'Z', '1', '2', '3', '4', '5',
	                '6', '7', '8', '9', '0'
	                );
	        $max_chars = count($chars) - 1;
	        srand((double) microtime()*1000000);
	        $random2 = '';
	        for($i=0; $i < 6; $i++){ $random2 = $random2 . $chars[rand(0, $max_chars)]; }

	    	$randomico = $random1.''.$random2; // ID UNICO
	    	return $randomico;
		}

		/**
		 * fecha_larga Mostrar la fecha con el nombre del dia, mes año
		 * @param  String $date Fecha a convertir en formato Y-m-d
		 * @return String       Fecha convertida
		 */
		public function fecha_larga($date){
			list($aano,$mmes,$ddia) = explode("-",$date);
			$ww = date('w', mktime(0,0,0,date($mmes)  ,date($ddia) ,date($aano)));
			$dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado");
			$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
			$resultado = $dias[$ww]." ".$ddia." ".$meses[$mmes-1]." de ".$aano;
			return $resultado;
		}

		/**
		 * logInventario  Insertar el Log de inventarios para ver el detalle de los movimientos
		 * @param  Array  $params   						Parametros necesarios para la ejecucion de la funcion
		 * @param  Int    $params.id_bodega 				Id de la bodega a realizar el log
		 * @param  Int    $params.id_sucursal 				Id de la sucursala realizar el log
		 * @param  Date   $params.campo_fecha 				Nombre de los campos fechas en el documento
		 * @param  String $params.tablaPrincipal 			Nombre de la tabla principal a consultar
		 * @param  Int    $params.id_documento 				Id del documento a consultar el inventario
		 * @param  String $params.campos_tabla_inventario 	Campos de la tabla inventario o detalle de la tabla principal
		 * @param  String $params.tablaInventario 			Nombre de la tabla de inventario o detalle de la tabla principal
		 * @param  Int    $params.idTablaPrincipal 			Id que relaciona la tabla inventario con la principal
		 * @param  String $params.documento 			 	Nombre del documento que se insertara en el Log
		 * @param  String $params.descripcion_documento 	Descripcion o detalle del nombre del documento insertado en el log
		 */
		public function logInventario($params){
			if ($params['id_bodega'] <>'' && $params['id_sucursal']<>'') {
				$id_bodega_sql   = "$params[id_bodega]";
				$id_sucursal_sql = "$params[id_sucursal]";
			}
			else{
				$id_bodega_sql   = "id_bodega";
				$id_sucursal_sql = "id_sucursal";
			}

			// COSULTAR LA FECHA DEL DOCUMENTO
			$sql="SELECT $params[campo_fecha] AS fecha_documento,$id_bodega_sql,$id_sucursal_sql,id_empresa FROM $params[tablaPrincipal] WHERE id=$params[id_documento] ";
			$query=$mysql->query($sql,$mysql->link);
			$fecha_documento = $mysql->result($query,0,'fecha_documento');
			$id_bodega       = $mysql->result($query,0,'id_bodega');
			$id_sucursal     = $mysql->result($query,0,'id_sucursal');
			$id_empresa      = $mysql->result($query,0,'id_empresa');

			// CONSULTAR EL INVENTARIO DE ESE DOCUMENTO
			$sql="SELECT $params[campos_tabla_inventario] FROM $params[tablaInventario] WHERE $params[idTablaPrincipal]=$params[id_documento] ";
			$query=$mysql->query($sql,$mysql->link);
			$whereId = '';
			while ($row=$mysql->fetch_array($query)){
				$whereId .= ($whereId=='')? " id_item=$row[id_item] " : " OR id_item=$row[id_item] " ;
			}

			// CONSULTAR EL INVENTARIO
			$sql="SELECT
					id_item,
					codigo,
					code_bar,
					nombre_equipo,
					costos,
					precio_venta,
					cantidad,
					unidad_medida,
					cantidad_unidades,
					id_empresa,
					empresa,
					id_sucursal,
					sucursal,
					id_ubicacion AS id_bodega,
					ubicacion AS bodega
			 	FROM inventario_totales
			 	WHERE activo=1
				AND id_ubicacion = $id_bodega
				AND id_sucursal  = $id_sucursal
				AND id_empresa   = $id_empresa
				AND ($whereId)
				 ";
			$query=$mysql->query($sql,$mysql->link);
			// 2019-01-17 14:29:44
			while ($row=$mysql->fetch_array($query)){
				$valueInsert .= "(
									'$row[id_item]',
									'$row[codigo]',
									'$row[code_bar]',
									'$row[nombre_equipo]',
									'$row[costos]',
									'$row[precio_venta]',
									'$row[cantidad]',
									'$params[id_documento]',
									'$params[documento]',
									'$params[descripcion_documento]',
									'$fecha_documento ".date("H:i:s")."',
									'".date("Y-m-d H:i:s")."',
									'$row[id_empresa]',
									'$row[id_sucursal]',
									'$row[sucursal]',
									'$row[id_bodega]',
									'$row[bodega]'
								),";
			}
			// echo "$valueInsert";
			$valueInsert = substr($valueInsert, 0, -1);

			// INSERTAR EL LOG DE INVENTARIO
			$sql="INSERT INTO inventario_totales_historico
						(
							id_item,
							codigo,
							code_bar,
							nombre_equipo,
							costos,
							precio_venta,
							cantidad,
							id_documento,
							documento,
							descripcion_documento,
							fecha_inventario,
							fecha_registro,
							id_empresa,
							id_sucursal,
							sucursal,
							id_bodega,
							bodega
						)
					VALUES $valueInsert";
			$query=$mysql->query($sql,$mysql->link);
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

	}





 ?>