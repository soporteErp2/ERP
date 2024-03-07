<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$id_empresa = $_SESSION['EMPRESA'];
	$arrayValores=json_decode($arrayDatos);

	//CONSULTAR LA TABLA PARA IDENTIFICAR SI SE AGREGA O ACTUALIZA
	$sql="SELECT COUNT(id) AS cont FROM nomina_conceptos_empleados WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado";
	$query=mysql_query($sql,$link);
	$cont=mysql_result($query,0,'cont');



	if ($cont>0) {
		$error=0;
		foreach ($arrayValores as $id => $resul) {
			//CONCULTAR SI EXISTE EL REGISTRO Y ACTUALIZARLO, SINO INSERTARLO
			$sql="SELECT COUNT(id) AS cont_concepto FROM nomina_conceptos_empleados WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id_concepto=$resul->id";
			$query=mysql_query($sql,$link);
			if (mysql_result($query,0,'cont_concepto')>0) {
				// ACTUALIZAR EL CONCEPTO
				$sql="UPDATE nomina_conceptos_empleados SET valor_concepto='$resul->valor'
						WHERE activo=1 AND id_empleado='$id_empleado' AND id_concepto='$resul->id' AND id_empresa='$id_empresa' ";
				$query=mysql_query($sql,$link);
				if (!$query) { $error++; }
			}
			else{
				//INSERTAR UN NUEVO CONCEPTO
				$sql="INSERT INTO nomina_conceptos_empleados (id_empleado,id_concepto,valor_concepto,id_empresa) VALUES ('$id_empleado','$resul->id','$resul->valor','$id_empresa')";
				$query=mysql_query($sql,$link);
				if (!$query) { $error++; }
			}
		}
		if ($error>0) {
			echo '<script>alert("Error!\nAlgunos valores no se actualizaron, intentelo de nuevo\nSi el problema persiste comuniquese con el administrador del sistema");</script>';
		}
	}
	else{
		$cadenaSql='';
		foreach ($arrayValores as $id => $resul) {
			$cadenaSql.="('$id_empleado','$resul->id','$resul->valor','$id_empresa'),";
		}

		$cadenaSql = substr($cadenaSql,0,-1);
		$sql="INSERT INTO nomina_conceptos_empleados (id_empleado,id_concepto,valor_concepto,id_empresa) VALUES $cadenaSql";
		$query=mysql_query($sql,$link);
		if (!$query) {
			echo '<script>alert("Error!\nNo se guardaron los valores de los conceptos para este cargo\nSi el problema persiste comuniquese con el administrador del sistema");</script>';
		}

	}


 ?>