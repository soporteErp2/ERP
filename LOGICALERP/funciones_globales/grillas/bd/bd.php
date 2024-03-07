<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$id_empresa = $_SESSION['EMPRESA'];

	switch ($opc) {
		case 'validarCuenta':
			validarCuenta($cuenta,$id_empresa,$link);
			break;
		case 'validaCentroCostos':
			validar_centro_costo($id_ccos,$codigo_centro_costos,$centro_costos,$id_empresa,$link);
			break;
	}

	function validarCuenta($cuenta,$id_empresa,$link){
		$sqlCuenta   = "SELECT COUNT(id) AS contCuenta FROM puc WHERE activo=1 AND id_empresa='$id_empresa' AND cuenta LIKE '$cuenta%'";
		$queryCuenta = mysql_query($sqlCuenta,$link);

		echo mysql_result($queryCuenta, 0, 'contCuenta');
	}

	function validar_centro_costo($id_ccos,$codigo_centro_costos,$centro_costos,$id_empresa,$link){
		$sql="SELECT COUNT(id) AS cont FROM centro_costos WHERE activo=1 AND id_empresa=$id_empresa AND codigo LIKE '$codigo_centro_costos%' AND codigo<>'$codigo_centro_costos' ";
		$query=mysql_query($sql,$link);
		$res = mysql_result($query,0,'cont');
		if ($res>0) {
			echo '<script>
					alert("Debe seleccionar un centro de costos hijo!");
				</script>';
		}
		else{
			echo "<script>
					renderSelectedCcos_FacturaCompraCuentas(".$id_ccos.",".$cont.");
				</script>";
		}
	}


?>