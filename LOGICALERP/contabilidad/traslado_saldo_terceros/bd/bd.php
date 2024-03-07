<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$id_empresa = $_SESSION['EMPRESA'];

	switch ($opc) {

		case 'homologarCuentaColgaap':
			homologarCuentaColgaap($cuenta_colgaap,$naturaleza,$id_empresa,$link);
			break;
		case 'guardaCuenta':
			guardaCuenta($id_cuenta,$nombre_tabla,$naturaleza,$id_empresa,$link);
			break;

	}

	function homologarCuentaColgaap($cuenta_colgaap,$naturaleza,$id_empresa,$link){
		$cuenta_colgaap=str_replace('&nbsp;', '', $cuenta_colgaap);
		// CONSULTAR LA CUENTA NIIF EQUIVALENTE
		$sql="SELECT cuenta_niif FROM puc WHERE activo=1 AND id_empresa=$id_empresa AND cuenta='$cuenta_colgaap'";
		$query=mysql_query($sql,$link);
		$cuenta_niif = mysql_result($query,0,'cuenta_niif');

		if ($cuenta_niif=='' || $cuenta_niif==0) {
			echo '<img src="img/refresh.png" title="Homologar Cuenta en Niif">
					<script>alert("Aviso!\nNo hay una cuenta niif relacionada!");</script>';
			exit;
		}

		$sql="SELECT id,descripcion FROM puc_niif WHERE activo=1 AND id_empresa=$id_empresa AND cuenta=$cuenta_niif";
		$query=mysql_query($sql,$link);
		$id_niif     = mysql_result($query,0,'id');
		$descripcion = mysql_result($query,0,'descripcion');

		$campo = ($naturaleza=='debito')? 'id_cuenta_niif_debito' : 'id_cuenta_niif_credito' ;

		$sql="UPDATE costo_cuentas_transito SET $campo=$id_niif WHERE activo=1 AND id_empresa=$id_empresa";
		$query=mysql_query($sql,$link);

		if ($query) {
			$script = ($naturaleza=='debito')? 'document.getElementById("cuenta_niif_debito").innerHTML="'.$cuenta_niif.'";
												document.getElementById("descripcion_cuenta_niif_debito").innerHTML="'.$descripcion.'";'
												:
												'document.getElementById("cuenta_niif_credito").innerHTML="'.$cuenta_niif.'";
												document.getElementById("descripcion_cuenta_niif_credito").innerHTML="'.$descripcion.'";' ;

			echo '<img src="img/refresh.png" title="Homologar Cuenta en Niif">
				<script>
					'.$script.'
				</script>';
		}
		else{
			echo '<img src="img/refresh.png" title="Homologar Cuenta en Niif">
					<script>alert("Error\nNo se ha sincronizado la cuenta niif intentelo de nuevo");</script>';
		}


	}

	function guardaCuenta($id_cuenta,$nombre_tabla,$naturaleza,$id_empresa,$link){

		// CONSULTAR SI YA SE INSERTO EL REGISTRO
		$sql         = "SELECT id FROM costo_cuentas_transito WHERE activo=1 AND id_empresa=$id_empresa";
		$query       = mysql_query($sql,$link);
		$id_registro = mysql_result($query,0,'id');

		// CONSULTAR LA CUENTA
		$sql="SELECT cuenta,descripcion FROM $nombre_tabla WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_cuenta";
		$query=mysql_query($sql,$link);
		$cuenta      = mysql_result($query,0,'cuenta');
		$descripcion = mysql_result($query,0,'descripcion');

		if ($naturaleza=='debito') {
			$campo = ($nombre_tabla=='puc')? 'id_cuenta_colgaap_debito' : 'id_cuenta_niif_debito';
			$script= ($nombre_tabla=='puc')? 'document.getElementById("cuenta_colgaap_debito").innerHTML="'.$cuenta.'";
												document.getElementById("descripcion_cuenta_colgaap_debito").innerHTML="'.$descripcion.'";'
												:
												'document.getElementById("cuenta_niif_debito").innerHTML="'.$cuenta.'";
												document.getElementById("descripcion_cuenta_niif_debito").innerHTML="'.$descripcion.'";';
		}
		else{
			$campo = ($nombre_tabla=='puc')? 'id_cuenta_colgaap_credito' : 'id_cuenta_niif_credito';
			$script= ($nombre_tabla=='puc')? 'document.getElementById("cuenta_colgaap_credito").innerHTML="'.$cuenta.'";
												document.getElementById("descripcion_cuenta_colgaap_credito").innerHTML="'.$descripcion.'";'
												:
												'document.getElementById("cuenta_niif_credito").innerHTML="'.$cuenta.'";
												document.getElementById("descripcion_cuenta_niif_credito").innerHTML="'.$descripcion.'";';
		}

		// SI EXISTEN REGISTROS SE ACTUALIZAN
		if ($id_registro>0) {

			$sql="UPDATE costo_cuentas_transito SET $campo=$id_cuenta WHERE activo=1 AND id_empresa=$id_empresa";
			$query=mysql_query($sql,$link);
			if ($query) {
				echo '<script>
							'.$script.'
							Win_Ventana_buscar_cuenta.close();
						</script>';
			}
			else{
				echo '<script>
						Actualiza_Div_'.$nombre_tabla.'('.$id_cuenta.');
						alert("Error!\nNo se actualizo la cuenta, intentelo de nuevo");
					</script>';
			}

		}
		// SI NO EXISTEN REGISTROS SE INSERTA
		else{

			$sql="INSERT INTO costo_cuentas_transito ($campo,id_empresa) VALUES ($id_cuenta,$id_empresa)";
			$query=mysql_query($sql,$link);
			if ($query) {
				echo '<script>
							'.$script.'
							Win_Ventana_buscar_cuenta.close();
						</script>';
			}
			else{
				echo '<script>
						Actualiza_Div_'.$nombre_tabla.'('.$id_cuenta.');
						alert("Error!\nNo se inserto la cuenta, intentelo de nuevo");
					</script>';
			}
		}

	}


 ?>