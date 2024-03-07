<?php
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

	if(!isset($_SESSION)){
		session_start();
	}
	include('doctype.php'); //ESTE ARCHIVO DEFINE EL DOCTYPE DEPENDIENDO DE LA VERSION DEL NAVEGADOR

	//LECTURA DEL XML DE CONFIGURACION/////////////////////////////////////////////
	include('xml2array.php');
	$DIRECTORIO = explode ("/", $_SERVER['REQUEST_URI']);

   	if(file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$DIRECTORIO[1].'/ARCHIVOS_PROPIOS/conexion.xml')){
		$fichero  = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/'.$DIRECTORIO[1].'/ARCHIVOS_PROPIOS/conexion.xml'); //SI SE LLAMA DESDE LOCAL O EN CARPETA /SIIP
	}
	if(file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$DIRECTORIO[0].'/ARCHIVOS_PROPIOS/conexion.xml')){
		$fichero  = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/'.$DIRECTORIO[0].'/ARCHIVOS_PROPIOS/conexion.xml'); //SI SE LLAMA DESDE UN DOMINIO
	}
    if(file_exists('/SIIP/SIIP4/ARCHIVOS_PROPIOS/conexion.xml')){
        $fichero  = file_get_contents('/SIIP/SIIP4/ARCHIVOS_PROPIOS/conexion.xml'); //SI SE LLAMA DESDE LA CARPÉTA DE CONTROL EN UN HOST VIRTUALÑ
    }

	$array = xml2array($fichero);

	$servidor 	= $array['configuracion']['database']['servidor'];
	$usuario 	= $array['configuracion']['database']['usuario'];
	$password 	= $array['configuracion']['database']['password'];
	$bd 		= $array['configuracion']['database']['bd'];

	$PRODUCT 	= '4';
	$APP 		= 'LogicalSoft-ERP';

	///////////////////////////////////////////////////////////////////////////////

	$link = mysql_connect($servidor,$usuario,$password);
	if(!$link){echo 'Error Conectando a Mysql<br />';};
	mysql_select_db($bd,$link);
	//mysql_query("SET NAMES 'iso-8859-1'");
	//mysql_set_charset('iso-8859-1',$link);
	if(!@mysql_select_db($bd,$link)){echo 'Error Conectando a la la base de datos "'.$bd.'" <br />';};



	///////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////
	function user_permisos($permiso,$alReves='false'){
		if (in_array($permiso, $_SESSION["PERMISOS"])) {
			if($alReves=='true'){$el_permiso = 'false';}else{$el_permiso = 'true';}
		}else{
			if($alReves=='true'){$el_permiso = 'true';}else{$el_permiso = 'false';}
		};
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
		$meses = array("Ene.","Feb.","Mar.","Abr.","May.","Jun.","Jul.","Ago.","Sep.","Oct.","Nov.","Dic.");
		$resultado = $ddia." ".$meses[$mmes-1];
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
	
	include("class.numerosenletras.php");
?>
