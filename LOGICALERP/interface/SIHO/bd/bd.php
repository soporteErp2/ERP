<?php
	include("../../../../configuracion/conectar.php");
    include("../../../../configuracion/define_variables.php");

    $id_empresa = $_SESSION['EMPRESA'];
    switch ($opc) {
    	case 'guardarConfiguracion':
    		guardarConfiguracion($id_metodo,$id_cuenta_pago,$contraPartida_colgaap,$contraPartida_niif,$id_empresa,$link);
    		break;

    	default:
    		# code...
    		break;
    }

    function guardarConfiguracion($id_metodo,$id_cuenta_pago,$contraPartida_colgaap,$contraPartida_niif,$id_empresa,$link){
		$sqlCuentaPago   = "SELECT COUNT(id) AS cont, nombre, cuenta, cuenta_niif FROM configuracion_cuentas_pago WHERE id_empresa='$id_empresa' AND id='$id_cuenta_pago'";
		$queryCuentaPago = mysql_query($sqlCuentaPago,$link);

		$contCuentaPago               = mysql_result($queryCuentaPago, 0, 'cont');
		$array['nombre']              = mysql_result($queryCuentaPago, 0, 'nombre');
		$array['cuenta_pago_colgaap'] = mysql_result($queryCuentaPago, 0, 'cuenta');
		$array['cuenta_pago_niif']    = mysql_result($queryCuentaPago, 0, 'cuenta_niif');
		if(!$queryCuentaPago || $contCuentaPago == '' || $contCuentaPago==0){ echo '<script>alert("Aviso,\nNo se encontro la configuracion de la cuenta de pago")</script>'; exit; }

		$array['id_cuenta_pago']        = $id_cuenta_pago;
		$array['contraPartida_colgaap'] = $contraPartida_colgaap;
		$array['contraPartida_niif']    = $contraPartida_niif;

		$textJson    = json_encode($array);
		$sqlUpdate   = "UPDATE web_service_metodos SET configuracion='$textJson' WHERE id='$id_metodo'";
		$queryUpdate = mysql_query($sqlUpdate,$link);

		if(!$queryUpdate){ echo '<script>alert("Aviso,\nNo se actualizo la informacion!")</script>'; exit; }
		echo'<script>Win_Ventana_config_saldo_facturas.close();</script>';
    }
?>