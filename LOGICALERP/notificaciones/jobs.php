<?php
	////////// DESARROLLO ////////////
	$path       ='C:/PROYECTOS';
	$conexionDB ='192.168.8.202';

	$user       ='root';
	$pass       ='serverchkdsk';

	////////// PRODUCCION ////////////
	/*
	$path     ='/SIIP';
	$conexionDB ='localhost';
	$user       ='root';
	$pass       ='simipyme';
	*/

	include($path.'/LOGICALERP/misc/phpmailer/PHPMailerAutoload.php');
	include($path.'/LOGICALERP/misc/phpmailer/language/phpmailer.lang-es.php');

	//Conexion DB -->
	$link = mysql_connect($conexionDB,$user,$pass);
	if(!$link){echo 'Error Conectando a Mysql<br />';};
	mysql_select_db('logicalsofterp',$link);
	if(!@mysql_select_db('logicalsofterp',$link)){ echo 'Error Conectando a la la base de datos "'.$bd.'" <br />'; };

	//FUNCION PARA CONSULTAR LA INFORACION DE LA CUENTA DE CORREO
	function getConfiguracionSmtp($id_empresa,$link){
		$sql    ="SELECT * FROM empresas_config_correo WHERE id_empresa='".$id_empresa."' LIMIT 1";
		$result =mysql_query($sql,$link);
		if(mysql_num_rows($result)){
			while($data[] = mysql_fetch_array($result, MYSQL_ASSOC));
			return $data;
		}else{
			echo  'No existen datos, debes ingresar la configuracion.';
		}
	}

	function fecha_corta($date){
		list($aano,$mmes,$ddia) = explode("-",$date);
		$ww = date('w', mktime(0,0,0,date($mmes)  ,date($ddia) ,date($aano)));
		$dias = array("Dom","Lun","Mar","Mier","Jue","Vie","Sab");
		$meses = array("Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");
		$resultado = $dias[$ww]." ".$ddia." ".$meses[$mmes-1]." ".$aano;
		return $resultado;
	}

	// include($path.'/LOGICALERP/LOGICALERP/notificaciones/depreciaciones.php');
	// include($path.'/LOGICALERP/LOGICALERP/notificaciones/depurar_documentos.php');
	// include($path.'/LOGICALERP/LOGICALERP/notificaciones/ticket.php');
	include($path.'/LOGICALERP/LOGICALERP/notificaciones/notificacion_correo_compras_cartera.php');

?>