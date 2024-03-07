<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$idEmpresa = $_SESSION['EMPRESA'];

	switch ($op) {
		case 'saveConfig':
			saveConfig($tope,$idEmpresa,$link);
		break;
		
	}

	function saveConfig($tope,$idEmpresa,$link){

		$sql = "REPLACE INTO ventas_pos_tope_facturacion VALUES(1,'$tope',$idEmpresa)";
		$query  = mysql_query($sql,$link);

		if(!$query){ echo '<script>alert("Aviso,\nSin conexion con el servidor, si el problema persiste comuniquese con el administrador del sistema.");</script>';  exit; }

		echo '<script>Win_Panel_Global.close();</script>';
	}


?>