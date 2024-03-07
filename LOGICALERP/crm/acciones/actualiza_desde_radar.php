<?php
include('../../../configuracion/conectar.php');
include('../../../configuracion/define_variables.php');

if($opcion == 'cancela_alerta'){
	if($mysql->query("UPDATE calendario_notificaciones SET descartar = 'true' WHERE id = $id",$link)){
		echo 'true';
	}
}

if($opcion == 'posponer_alerta'){
	//LE SUMO LOS SEGUNDOS QUE SE POSPUSO A LA HORA ACTUAL.

	$HORA_POSPUESTA = date('Y-m-d H:i:s', mktime(date('H'),date('i'),date('s')+$posponer,date('m'),date('d'),date('Y')));
	if($mysql->query("UPDATE calendario_notificaciones SET pospuesto = 'true', fecha_pospuesto = '$HORA_POSPUESTA' WHERE id = $id",$link)){
		echo 'true';
	}
}
?>