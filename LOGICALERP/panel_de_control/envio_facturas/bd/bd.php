<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$id_empresa = $_SESSION['EMPRESA'];

	switch ($opc) {
		case 'set_automatic_sending':
			set_automatic_sending($is_active,$id_empresa,$link);
		break;
		
	}

	function set_automatic_sending($is_active,$id_empresa,$link){

		$sql = "SELECT id FROM configuracion_general 
				WHERE activo=1 AND id_empresa=$id_empresa AND modulo='panel_de_control' AND descripcion='envio automatico de FV electronica' ";
		$query = mysql_query($sql,$link);
		$id = mysql_result($query,0,'id');

		if ($id>0) {
			$sql = "UPDATE configuracion_general SET data = '".json_encode([["is_active" => $is_active]])."' 
					WHERE activo=1 AND id_empresa=$id_empresa AND modulo='panel_de_control' AND descripcion='envio automatico de FV electronica'";
			$query = mysql_query($sql,$link);
		}
		else{
			$sql = "INSERT INTO configuracion_general (modulo,descripcion,data,id_empresa)
					VALUES ('panel_de_control','envio automatico de FV electronica','".json_encode([["is_active" => $is_active]])."','$id_empresa') ";
			$query = mysql_query($sql,$link);
		}
		// echo $sql;
		$retVal = ($query)? "true" : "false";
		echo json_encode(["success"=>$retVal]);

		// $updateMoneda = "UPDATE empresas SET id_moneda = '$idMoneda', decimales_moneda = '$decimalesMoneda' WHERE activo=1 AND id='$id_empresa' AND activo=1";
		// $queryMoneda  = mysql_query($updateMoneda,$link);

		// if(!$queryMoneda){ echo '<script>alert("Aviso,\nSin conexion con el servidor, si el problema persiste comuniquese con el administrador del sistema.");</script>';  exit; }

		// echo '<script>Win_Panel_Global.close();</script>';
	}


?>