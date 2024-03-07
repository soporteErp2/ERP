<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$idEmpresa = $_SESSION['EMPRESA'];

	switch ($op) {
		case 'saveConfig':
			saveConfig($tipo,$idEmpresa,$link);
		break;
		
	}

	function saveConfig($tipo,$idEmpresa,$link){

		$sql = "REPLACE INTO nomina_configuracion_hora_extra VALUES(1,'$tipo',$idEmpresa,1)";
		$query  = mysql_query($sql,$link);

		if(!$query){ echo '<script>alert("Aviso,\nSin conexion con el servidor, si el problema persiste comuniquese con el administrador del sistema.");</script>';  exit; }

		echo '<script>Win_Panel_Global.close();</script>';
	}


?>