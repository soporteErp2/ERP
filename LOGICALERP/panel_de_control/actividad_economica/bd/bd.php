<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$empresa=$_SESSION['EMPRESA'];

	switch ($opc) {

		case 'buscarActividadEconomica':
			buscarActividadEconomica($cuentaPuc,$empresa,$link);
			break;

		case 'guardarActividadEconomica':
			guardarActividadEconomica($idCuentaPuc,$cuentaPuc,$empresa,$link);
			break;

	}

	//================================== INSERT NUEVA CUENTA DE BANCO =======================================//

	function buscarActividadEconomica($cuentaPuc,$empresa,$link){
		$sql   = "SELECT id,descripcion FROM puc WHERE cuenta='$cuentaPuc' AND activo=1 AND cuenta LIKE '4135%' AND cuenta>99999 LIMIT 0,1";
		$query = mysql_query($sql,$link);

		if(!$query){ echo '<script>alert("Error,\nNo hay conexion con el servidor si el problema persiste comuniquese con el administrador del sistema")</script>'; return; }
		$id          = mysql_result($query,0,'id');
		$descripcion = mysql_result($query,0,'descripcion');

		if($id==''){ echo '<script>alert("Error,\nNo hay Cuenta puc relacionada al codigo PUC '.$cuentaPuc.'")</script>'; return; }
		echo'<script>
				id_actividad_economica = '.$id.';
				document.getElementById("detalleCuentaActividadEconomica").value = "'.$descripcion.'";
			</script>';
	}

	function guardarActividadEconomica($idCuentaPuc,$cuentaPuc,$empresa,$link){
		$sql   = "SELECT descripcion FROM puc WHERE cuenta='$cuentaPuc' AND id='$idCuentaPuc' AND activo=1 AND cuenta LIKE '4135%' AND cuenta>99999 LIMIT 0,1";
		$query = mysql_query($sql,$link);

		if(!$query){ echo '<script>alert("Error,\nNo hay conexion con el servidor si el problema persiste comuniquese con el administrador del sistema");</script>'; return; }
		$descripcion = mysql_result($query,0,'descripcion');

		if($descripcion==''){ echo '<script>alert("Error,\nNo hay Cuenta puc relacionada al codigo PUC '.$cuentaPuc.'");</script>'; return; }
		$sqlUpdate = "UPDATE empresas 
						SET id_puc_actividad_economica 	   = '$idCuentaPuc', 
							codigo_puc_actividad_economica = '$cuentaPuc',
							cuenta_puc_actividad_economica = '$descripcion'
						WHERE id   = '$empresa'
						AND activo = 1";
		$queryUpdate = mysql_query($sqlUpdate,$link);

		if(!$queryUpdate){ echo '<script>alert("Error,\nNo hay conexion con el servidor si el problema persiste comuniquese con el administrador del sistema");</script>'; return; }
		echo '<script>Win_Panel_Global.close();</script>';
	}
?>