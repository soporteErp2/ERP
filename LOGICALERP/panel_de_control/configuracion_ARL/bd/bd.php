<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $filtro_sucursal;

	switch ($opc) {
		case 'guardar_configuracion_arl':
			guardar_configuracion_arl($codigo_arl,$id_empresa,$mysql);
			break;
	}

	//FUNCION PARA DESHABILITAR UNA CAJA
	function guardar_configuracion_arl($codigo_arl,$id_empresa,$mysql){
		// echo '<script>MyLoading2("off")</script>';
		$sql="SELECT id FROM configuracion_arl WHERE activo=1 AND id_empresa=$id_empresa";
		$query=$mysql->query($sql,$mysql->link);
		$id_config = $mysql->result($query,0,'id');

		if ($id_config>0) {
			$sql="UPDATE configuracion_arl SET codigo_arl='$codigo_arl' WHERE activo=1 AND id_empresa=$id_empresa ";
			$query=$mysql->query($sql,$mysql->link);
		}
		else{
			$sql="INSERT INTO configuracion_arl (codigo_arl,id_empresa) VALUES ('$codigo_arl',$id_empresa) ";
			$query=$mysql->query($sql,$mysql->link);
		}

		if ($query) {
			echo '<script>MyLoading2("off");Win_Panel_Sucursal.close();</script>';
		}
		else{
			echo '<script>MyLoading2("off",{icono:"fail",texto:"Se produjo un error no se guardo el registro",duracion:2000})</script>'.$sql;
		}
	}


 ?>