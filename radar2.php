<?php
	include('configuracion/conectar.php');
	$consul = mysql_query("SELECT * FROM usuarios WHERE id = $_SESSION[IDUSUARIO] AND alerta_actualizacion = 'true'",$link);
	if(mysql_num_rows($consul)>0){
		echo 'true';
	}
?>