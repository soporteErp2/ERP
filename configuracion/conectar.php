<?php
	error_reporting(E_ALL);
	//header('Location: /mantenimiento.php');
	set_time_limit(0);
	//ini_set("memory_limit","100M");
	//////////////////////////////////////////////////////////////
	if(file_exists('../ARCHIVOS_PROPIOS/zona_horaria.php')){
		include('../ARCHIVOS_PROPIOS/zona_horaria.php');
	}else{
		$zona_horaria = "America/Bogota";
	}
	date_default_timezone_set($zona_horaria);
	///////////////////////////////////////////////////////////////
	// var_dump($_SESSION);
	if(!isset($_SESSION)){
		session_start();
	}

	////////////////////////////  VALIDACION DE LA SESSION /////////////////////////////////
	$ruta = "";
	$explode_ruta = explode("/",$_SERVER['SCRIPT_NAME']);
	for($i=0;$i<(count($explode_ruta)-1);$i++){
		$ruta .=  $explode_ruta[$i].'/';
	}

	include('doctype.php'); //ESTE ARCHIVO DEFINE EL DOCTYPE DEPENDIENDO DE LA VERSION DEL NAVEGADOR

    if (isset($_SESSION['TIMEZONE'])) {
		date_default_timezone_set($_SESSION['TIMEZONE']);
    }
    else{ date_default_timezone_set("America/Bogota"); }

	if(!isset($server))
	{
		include_once("conexion.php");		
	}
	$servidor = $_SESSION['SERVIDOR'];
	$bd       = $_SESSION['BD'];
	// $server->user;
	// $server->password;
	// $password = "";
	// echo " $servidor - $usuario - $password ";
	// var_dump($_SESSION);
	// var_dump($server);

 	$PRODUCT 	= '4';
	$APP 		= 'LogicalSoft-ERP';

	// if(file_exists('../misc/ConnectDb/class.ConnectDb.php')){
	// 	include_once('../misc/ConnectDb/class.ConnectDb.php');
	// }
	// if(file_exists('misc/ConnectDb/class.ConnectDb.php')){
	// 	include_once('misc/ConnectDb/class.ConnectDb.php');
	// }
	
	if (!class_exists('ConnectDb')) {
		$DIRECTORIO = explode ("/", $_SERVER['REQUEST_URI']);

		// include_once($_SERVER['DOCUMENT_ROOT'].'/'.$DIRECTORIO[1].'/misc/ConnectDb/class.ConnectDb.php');
		include_once(__DIR__.'/../misc/ConnectDb/class.ConnectDb.php');

		// echo __DIR__.'/../misc/ConnectDb/class.ConnectDb.php';
		// echo "<br> ". __DIR__;
		// exit;

		// if(file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$DIRECTORIO[1].'/ARCHIVOS_PROPIOS/conexion.xml')){
		// 	$fichero  = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/'.$DIRECTORIO[1].'/ARCHIVOS_PROPIOS/conexion.xml'); //SI SE LLAMA DESDE LOCAL O EN CARPETA /SIIP
		// }
		
		// if(file_exists('../misc/ConnectDb/class.ConnectDb.php')){
		// 	include_once('../misc/ConnectDb/class.ConnectDb.php');
		// }
		// if(file_exists('misc/ConnectDb/class.ConnectDb.php')){
		// 	include_once('misc/ConnectDb/class.ConnectDb.php');
		// }
	}
	global $mysql;
	
	$objConectDB = new ConnectDb(
						"MySql",			// API SQL A UTILIZAR  MySql, MySqli
						$server->server_name,			// SERVIDOR
						$server->user, 			// USUARIO DATA BASE
						$server->password, 			// PASSWORD DATA BASE
						$bd 				// NOMBRE DATA BASE
					);

	$mysql = $objConectDB->getApi();
	$link  = $mysql->conectar();

	$objConectDBLi = new ConnectDb(
						"MySqli",			// API SQL A UTILIZAR  MySql, MySqli
						$server->server_name,			// SERVIDOR
						$server->user, 			// USUARIO DATA BASE
						$server->password, 			// PASSWORD DATA BASE
						$bd 				// NOMBRE DATA BASE
					);
					
	// $mysql_i = $objConectDBLi->getApi();
	$linkLi = $objConectDBLi->getApi();

	// var_dump($mysql_i);
	// $linkLi  = $mysql_i->conectar();

	///////////////////////////////////////////////////////////////////////////////
	function user_permisos($permiso,$alReves='false'){
		if (in_array($permiso, $_SESSION["PERMISOS"])) {
			if($alReves=='true'){ $el_permiso = 'false'; }
			else{ $el_permiso = 'true'; }
		}
		else{
			if($alReves=='true'){ $el_permiso = 'true'; }
			else{ $el_permiso = 'false'; }
		}
		return $el_permiso;
	}

	function mylog($log,$modulo,$link){
		$log = str_replace("'","`",$log);
		mysql_query("INSERT INTO log (fecha,user,log,modulo)VALUES(now(),$_SESSION[IDUSUARIO],'$log',$modulo)",$link);
	}

	//FUNCION QUE ARROJA LA FECHA EN FORMATO LARGO DESDE UNA VARIABLE PARA LOS TAB DEL COMERCIAL
	function fecha_tab($date){
		list($aano,$mmes,$ddia) = explode("-",$date);
		$ww = date('w', mktime(0,0,0,date($mmes)  ,date($ddia) ,date($aano)));
		$dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado");
		$meses = array("Ene.","Feb.","Mar.","Abr.","May.","Jun.","Jul.","Ago.","Sep.","Oct.","Nov.","Dic.");
		$resultado = $dias[$ww]." <br> ".$ddia." ".$meses[$mmes-1]." ".$aano;
		return $resultado;
	}

	function fecha_corta($date){
		list($aano,$mmes,$ddia) = explode("-",$date);
		$ww = date('w', mktime(0,0,0,date($mmes)  ,date($ddia) ,date($aano)));
		$dias = array("Dom","Lun","Mar","Mier","Jue","Vie","Sab");
		$meses = array("Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");
		$resultado = $dias[$ww]." ".$ddia." ".$meses[$mmes-1]." ".$aano;
		return $resultado;
	}

	function fecha_larga($date){
		list($aano,$mmes,$ddia) = explode("-",$date);
		$ww = date('w', mktime(0,0,0,date($mmes)  ,date($ddia) ,date($aano)));
		$dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado");
		$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
		$resultado = $dias[$ww]." ".$ddia." ".$meses[$mmes-1]." de ".$aano;
		return $resultado;
	}

	function fecha_larga2($date){
		list($aano,$mmes,$ddia) = explode("-",$date);
		$ww = date('w', mktime(0,0,0,date($mmes)  ,date($ddia) ,date($aano)));
		$dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado");
		$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
		$resultado = $ddia." ".$meses[$mmes-1]." ".$aano;
		return $resultado;
	}

	function hora($date){
		list($h,$m,$s) = explode(":",$date);
		if($h > 12){
			$h = $h - 12;
			$s = pm;
		}else{
			$s = am;
		}
		return $h.':'.$m.' '.$s;
	}

	function fecha_larga_hora($date){

		list($date1,$date2) = explode(" ",$date);
		list($aano,$mmes,$ddia) = explode("-",$date1);
		$ww = date('w', mktime(0,0,0,date($mmes)  ,date($ddia) ,date($aano)));
		$dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado");
		$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
		$resultado = $dias[$ww]." ".$ddia." ".$meses[$mmes-1]." de ".$aano." ".$date2;
		return $resultado;
	}

	function fecha_larga_hora_m($date){

		list($date1,$date2) = explode(" ",$date);
		list($aano,$mmes,$ddia) = explode("-",$date1);
		$ww = date('w', mktime(0,0,0,date($mmes)  ,date($ddia) ,date($aano)));
		$dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado");
		$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
		list($h,$m,$s) = explode(":",$date2);
		if($h > 12){
			$h = $h - 12;
			$s = pm;
		}else{
			$s = am;
		}
		$resultado = $dias[$ww]." ".$ddia." ".$meses[$mmes-1]." de ".$aano." ".$h.':'.$m.' '.$s;
		return $resultado;
	}

	function fecha_larga_idiomas($date,$idioma){
		list($aano,$mmes,$ddia) = explode("-",$date);
		$ww = date('w', mktime(0,0,0,date($mmes)  ,date($ddia) ,date($aano)));

		$dias[0]  = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado");
		$dias[1]  = array("sunday ","monday","tuesday","Wednesday","thursday ","friday","Saturday");
		$meses[0] = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
		$meses[1] = array("January","February","March","April","May","June","July","August","September","October","November","December");

		$resultado[0] = $dias[0][$ww]." ".$ddia." ".$meses[0][$mmes-1]." de ".$aano;
		$resultado[1] = $dias[1][$ww]." ".$meses[1][$mmes-1]." ".$ddia.", ".$aano;

		return $resultado[$idioma];
	}
	//FUNCION QUE DA COMO RESULTADO EL NUMERO DE DIAS (SIN HORAS) QUE HAY ENTRE DOS FECHAS (el resultado es en segundos para volver a dias dividri entre 60 luego 60 y luego 24)
	function resta($fecha1,$fecha2){
		list($fecha,$hora)=explode(' ',$fecha1);
		list($ano,$mes,$dia)=explode('-', $fecha);
		list($hora,$minuto,$segundo)=explode(':', $hora);
		$y=mktime(0,0,0,$mes,$dia,$ano);

		list($fecha,$hora)=explode(' ',$fecha2);
		list($ano,$mes,$dia)=explode('-', $fecha);
		list($hora,$minuto,$segundo)=explode(':', $hora);
		$z=mktime(0,0,0,$mes,$dia,$ano);
		$resta = $y - $z;

		return $resta;
	}

	function cortar_texto($cadena,$longitud){
		$cadena = substr($cadena,0,$longitud);
		if(strlen($cadena)>= $longitud ){$cadena .='...';}
		return $cadena;
	}

	function invierte_filas_col_array($array) {
		$out = array();
		foreach ($array as  $rowkey => $row) {
			foreach($row as $colkey => $col){
				$out[$colkey][$rowkey]=$col;
			}
		}
		return $out;
	}

	function cuantos_dias($fecha_i,$fecha_f){
		$dias	= (strtotime($fecha_i)-strtotime($fecha_f))/86400;
		$dias 	= abs($dias); $dias = floor($dias);
		return $dias+1;
	}

	function validar_numero_formato($numero,$opc){
		$numero=$numero*1;
	    if($opc!='true'){

	        return(number_format($numero,$_SESSION['DECIMALESMONEDA']));
	    }
	    else{
	    	return(round($numero,$_SESSION['DECIMALESMONEDA']));
	    }
	    return $numero;
	}

	// FUNCION PARA EL TAMAÑO DE UN DIRECTORIO
	function getFolderSize($id_host,$ruta){

		// $path,
		$formated = false;
		$retstring = null;

		// ARRAY CON LAS DIRECCIONES A VALIDAR EL TAMAÑO
		$paths[0] = $ruta.'ARCHIVOS_PROPIOS/empresa_'.$id_host;
		// $paths[0] = $ruta.'ARCHIVOS_PROPIOS/adjuntos_compras/empresa_'.$id_host;
		// $paths[1] = $ruta.'ARCHIVOS_PROPIOS/imagenes_empresas/empresa_'.$id_host;

		foreach ($paths as $key => $path) {

			if(!is_dir($path) || !is_readable($path)){
				if(is_file($path) || file_exists($path)){
					$size = filesize($path);
				}
				else {
					return false;
				}
			}
			else {
				$path_stack[] = $path;
				// $size = 0;

				do {
					$path	= array_shift($path_stack);
					$handle	= opendir($path);
					while(false !== ($file = readdir($handle))) {
						if($file != '.' && $file != '..' && is_readable($path . DIRECTORY_SEPARATOR . $file)) {
							if(is_dir($path . DIRECTORY_SEPARATOR . $file)){ $path_stack[] = $path . DIRECTORY_SEPARATOR . $file; }
							$size += filesize($path . DIRECTORY_SEPARATOR . $file);
						}
					}
					closedir($handle);
				} while (count($path_stack) > 0);
			}

			if($formated){
				$sizes = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
				if($retstring == null) { $retstring = '%01.2f %s'; }
				$lastsizestring = end($sizes);
				foreach($sizes as $sizestring){
					if($size < 1024){ break; }
					if($sizestring != $lastsizestring){ $size /= 1024; }
				}
				if($sizestring == $sizes[0]){ $retstring = '%01d %s'; }
				// los Bytes normalmente no son fraccionales
				$size = sprintf($retstring, $size, $sizestring);
			}



		}

		// KB
		$size /= 1024;
		// MB
		$size /= 1024;

		return $size;
	}

	function logInventario($arrayDatos,$mysql){
		if ($arrayDatos['id_bodega'] <>'' && $arrayDatos['id_sucursal']<>'') {
			$id_bodega_sql   = "$arrayDatos[id_bodega]";
			$id_sucursal_sql = "$arrayDatos[id_sucursal]";
		}
		else{
			$id_bodega_sql   = "id_bodega";
			$id_sucursal_sql = "id_sucursal";
		}

		// COSULTAR LA FECHA DEL DOCUMENTO
		$sql="SELECT $arrayDatos[campo_fecha] AS fecha_documento,$id_bodega_sql,$id_sucursal_sql,id_empresa FROM $arrayDatos[tablaPrincipal] WHERE id=$arrayDatos[id_documento] ";
		$query=$mysql->query($sql,$mysql->link);
		$fecha_documento = $mysql->result($query,0,'fecha_documento');
		$id_bodega       = $mysql->result($query,0,'id_bodega');
		$id_sucursal     = $mysql->result($query,0,'id_sucursal');
		$id_empresa      = $mysql->result($query,0,'id_empresa');

		// CONSULTAR EL INVENTARIO DE ESE DOCUMENTO
		$sql="SELECT $arrayDatos[campos_tabla_inventario] FROM $arrayDatos[tablaInventario] WHERE $arrayDatos[idTablaPrincipal]=$arrayDatos[id_documento] ";
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
								'$arrayDatos[id_documento]',
								'$arrayDatos[documento]',
								'$arrayDatos[descripcion_documento]',
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

	include("class.numerosenletras.php");
?>
