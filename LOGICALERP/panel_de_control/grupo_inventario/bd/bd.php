<?php

	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$id_empresa     = $_SESSION['EMPRESA'];

	switch ($opc) {
		case 'guardaCuenta':
			guardaCuenta($id_cuenta,$id_grupo,$id_campo,$nombre_tabla,$cuenta,$descripcion,$id_empresa,$mysql);
			break;
		case 'homologarCuentaColgaap':
			homologarCuentaColgaap($cuenta_colgaap,$id_campo,$id_grupo,$id_empresa,$mysql);
			break;
	}


	function guardaCuenta($id_cuenta,$id_grupo,$id_campo,$nombre_tabla,$cuenta,$descripcion,$id_empresa,$mysql){
		$sql="SELECT id FROM cuentas_default_activos_fijos WHERE activo=1 AND id_empresa=$id_empresa AND id_grupo=$id_grupo";
		$query=$mysql->query($sql,$mysql->link);
		$id = $mysql->result($query,0,'id');

		if ($id>0) {
			$sql="UPDATE cuentas_default_activos_fijos SET $id_campo = '$id_cuenta' WHERE activo=1 AND id_empresa=$id_empresa AND id=$id ";
			$query=$mysql->query($sql,$mysql->link);
		}
		else{
			$sql="INSERT INTO cuentas_default_activos_fijos ($id_campo,id_grupo,id_empresa) VALUES ($id_cuenta,$id_grupo,$id_empresa) ";
			$query=$mysql->query($sql,$mysql->link);
		}

		if ($id_campo == 'id_cuenta_depreciacion_colgaap_debito') {
			$script ='document.getElementById("cuenta_colgaap_debito").innerHTML             = "'.$cuenta.'";
					  document.getElementById("descripcion_cuenta_colgaap_debito").innerHTML = "'.$descripcion.'";';
		}
		elseif ($id_campo == 'id_cuenta_depreciacion_colgaap_credito') {
			$script ='document.getElementById("cuenta_colgaap_credito").innerHTML             = "'.$cuenta.'";
					  document.getElementById("descripcion_cuenta_colgaap_credito").innerHTML = "'.$descripcion.'";';
		}
		elseif ($id_campo == 'id_cuenta_depreciacion_niif_debito') {
			$script ='document.getElementById("cuenta_niif_debito").innerHTML             = "'.$cuenta.'";
					  document.getElementById("descripcion_cuenta_niif_debito").innerHTML = "'.$descripcion.'";';
		}
		elseif ($id_campo == 'id_cuenta_depreciacion_niif_credito') {
			$script ='document.getElementById("cuenta_niif_credito").innerHTML             = "'.$cuenta.'";
					  document.getElementById("descripcion_cuenta_niif_credito").innerHTML = "'.$descripcion.'";';
		}
		elseif ($id_campo == 'id_cuenta_deterioro_debito') {
			$script ='document.getElementById("cuenta_deterioro_debito").innerHTML             = "'.$cuenta.'";
					  document.getElementById("descripcion_cuenta_deterioro_debito").innerHTML = "'.$descripcion.'";';
		}
		elseif ($id_campo == 'id_cuenta_deterioro_credito') {
			$script ='document.getElementById("cuenta_deterioro_credito").innerHTML             = "'.$cuenta.'";
					  document.getElementById("descripcion_cuenta_deterioro_credito").innerHTML = "'.$descripcion.'";';
		}
		echo '<script>
				Actualiza_Div_'.$nombre_tabla.'('.$id_cuenta.');
				'.$script.'
				// console.log("'.$sql.'");
				 Win_Ventana_buscar_cuenta.close();
			</script>';
	}

	function homologarCuentaColgaap($cuenta_colgaap,$id_campo,$id_grupo,$id_empresa,$mysql){
		$sql   = "SELECT cuenta_niif FROM puc WHERE activo=1 AND id_empresa=$id_empresa AND cuenta='$cuenta_colgaap' ";
		$query = $mysql->query($sql,$mysql->link);
		$cuenta_niif = $mysql->result($query,0,'cuenta_niif');

		if ($cuenta_niif == '' || $cuenta_niif == '0') {
			echo '<script>
					alert("La cuenta colgaap no tiene una cuenta niif configurada");
				</script>
				<img src="img/refresh.png" title="Homologar Cuenta en Niif">';
			return;
		}

		$sql   = "SELECT id,descripcion FROM puc_niif WHERE activo=1 AND id_empresa=$id_empresa AND cuenta='$cuenta_niif' ";
		$query = $mysql->query($sql,$mysql->link);

		$id          = $mysql->result($query,0,'id');
		$descripcion = $mysql->result($query,0,'descripcion');

		if ($id_campo=='id_cuenta_depreciacion_colgaap_debito') {
			$campo_update = 'id_cuenta_depreciacion_niif_debito';
			$script ='document.getElementById("cuenta_niif_debito").innerHTML 			  = "'.$cuenta_niif.'";
					  document.getElementById("descripcion_cuenta_niif_debito").innerHTML = "'.$descripcion.'";';
		}
		else{
			$campo_update = 'id_cuenta_depreciacion_niif_credito';
			$script ='document.getElementById("cuenta_niif_credito").innerHTML 			   = "'.$cuenta_niif.'";
					  document.getElementById("descripcion_cuenta_niif_credito").innerHTML = "'.$descripcion.'";';
		}

		$sql="UPDATE cuentas_default_activos_fijos SET $campo_update = '$id' WHERE activo=1 AND id_empresa=$id_empresa AND id_grupo='$id_grupo' ";
		$query=$mysql->query($sql,$mysql->link);
		if (!$query) {
			echo '<script>
					alert("No se sincronizo la cuenta, intentelo de nuevo");
				</script>
				<img src="img/refresh.png" title="Homologar Cuenta en Niif">';
			return;
		}

		echo '<script>
				'.$script.'
			</script>
			<img src="img/refresh.png" title="Homologar Cuenta en Niif">';

	}

?>


