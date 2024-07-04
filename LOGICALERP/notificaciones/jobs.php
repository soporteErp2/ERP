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

	//Conexion DB -->
	$link = mysql_connect($conexionDB,$user,$pass);
	if(!$link){echo 'Error Conectando a Mysql<br />';};
	mysql_select_db('logicalsofterp',$link);
	if(!@mysql_select_db('logicalsofterp',$link)){ echo 'Error Conectando a la la base de datos "'.$bd.'" <br />'; };
	
	// include($path.'/LOGICALERP/LOGICALERP/notificaciones/depreciaciones.php');
	// include($path.'/LOGICALERP/LOGICALERP/notificaciones/depurar_documentos.php');
	include($path.'/LOGICALERP/LOGICALERP/notificaciones/ticket.php');

?>