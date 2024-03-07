<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$id_empresa = $_SESSION['EMPRESA'];

	switch ($opc) {
		case 'guardarCuenta':
			guardarCuenta($id_cuenta,$cuenta,$estado,$naturaleza,$id_empresa,$mysql);
		break;

	}

	function guardarCuenta($id_cuenta,$cuenta,$estado,$naturaleza,$id_empresa,$mysql){
		// CONSULTAR SI EN ESE ESTADO Y NATURALEZA YA EXISTE UNA CUENTA
		$sql="SELECT id FROM deterioro_cartera_proveedores_cuentas WHERE activo=1 AND id_empresa=$id_empresa AND estado='$estado' AND naturaleza='$naturaleza' ";
		$query=$mysql->query($sql,$mysql->link);
		$id_row=$mysql->result($query,0,'id');

		if ($id_row>0) {
			$sql   = "UPDATE deterioro_cartera_proveedores_cuentas SET id_cuenta='$id_cuenta' WHERE activo=1 AND id_empresa=$id_empresa AND estado='$estado' AND naturaleza='$naturaleza' ";
			$query = $mysql->query($sql,$mysql->link);
		}
		else{
			$sql   = "INSERT INTO deterioro_cartera_proveedores_cuentas (id_cuenta,naturaleza,estado,id_empresa) VALUES ($id_cuenta,'$naturaleza','$estado',$id_empresa) ";
			$query = $mysql->query($sql,$mysql->link);
		}

		if ($query) {
			echo '<script>
					MyLoading2("off");
					document.getElementById("'.$estado.'_'.$naturaleza.'_CP").innerHTML="'.$cuenta.'";
					Win_Ventana_buscar_cuenta_puc.close()
				</script>';
		}
		else{
			echo '<script>
					MyLoading2("off",{icono:"fail",texto:"No se configuro la cuenta, intentelo de nuevo"});
					document.getElementById("'.$estado.'_'.$naturaleza.'_CP").innerHTML="";
				</script>';
		}


	}



?>