<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$idEmpresa = $_SESSION['EMPRESA'];

	switch ($op) {
		case 'guardarConfiguracionMoneda':
			guardarConfiguracionMoneda($idMoneda,$decimalesMoneda,$idEmpresa,$link);
		break;
		
	}

	function guardarConfiguracionMoneda($idMoneda,$decimalesMoneda,$idEmpresa,$link){

		$updateMoneda = "UPDATE empresas SET id_moneda = '$idMoneda', decimales_moneda = '$decimalesMoneda' WHERE activo=1 AND id='$idEmpresa' AND activo=1";
		$queryMoneda  = mysql_query($updateMoneda,$link);

		if(!$queryMoneda){ echo '<script>alert("Aviso,\nSin conexion con el servidor, si el problema persiste comuniquese con el administrador del sistema.");</script>';  exit; }

		echo '<script>Win_Panel_Global.close();</script>';
	}


?>