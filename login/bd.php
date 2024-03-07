<?php
	include("../configuracion/conectar.php");
	include('../configuracion/define_variables.php');

	switch ($opc) {
		case 'updateUserName':
			updateUserName($userName,$mysql);
			break;
	}

	function updateUserName($userName,$mysql){
		// VALIDAR QUE ESE CORREO NO EXISTA
		$sql="SELECT COUNT(id) AS cont FROM empleados WHERE activo=1 AND username='$userName' AND id_empresa=".$_SESSION['EMPRESA'];
		$query=$mysql->query($sql,$mysql->link);
		$cont = $mysql->result($query,0, 'cont');

		if ($cont>0) {
			echo json_encode( array('response' => 'failure', 'detail'=>'El correo ingresado ya existe en el sistema' ,'sql' =>$sql) );
		}
		else{
			$sql   = "UPDATE empleados SET username='$userName' WHERE activo=1 AND id=".$_SESSION['IDUSUARIO'];
			$query = $mysql->query($sql,$mysql->link);
			if ($query) {
				echo json_encode( array('response' => 'success', 'detail'=>'Se actualizo el usuario' ,'sql' =>$sql) );
			}
			else{
				echo json_encode( array('response' => 'failure', 'detail'=>'No se logro actualizar el nombre de usuario ' ,'sql' =>$sql) );
			}
		}

	}

?>