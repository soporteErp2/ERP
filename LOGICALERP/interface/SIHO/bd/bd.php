<?php
	include("../../../../configuracion/conectar.php");
    include("../../../../configuracion/define_variables.php");

    $id_empresa = $_SESSION['EMPRESA'];
    switch ($opc) {
    	case 'guardarConfiguracion':
    		guardarConfiguracion($id_metodo,$id_cuenta_pago,$contraPartida_colgaap,$contraPartida_niif,$id_empresa,$link);
    		break;

		case '':
			# code...
			break;

    	case 'terminarGenerar':
    		terminarGenerar($id,$id_empresa,$id_sucursal,$idBodega,$observacion,$tablaPrincipal,$idTablaPrincipal,$tablaInventario,$tablaRetenciones,$opcGrillaContable,$id_empresa,$idPlantilla,$fechaFactura,$link)
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

    function terminarGenerar($id,$id_empresa,$link){

		$sqlFactBd     = "SELECT
								fecha_inicio,
								numero_factura,
								prefijo,
								estado,
								activo,
								id_cliente,
								id_configuracion_cuenta_pago,
								cuenta_pago,
								cuenta_pago_niif,
								cuenta_anticipo,
								valor_anticipo,
								id_centro_costo,
								exento_iva,
								id_sucursal
							FROM ventas_facturas
							WHERE id='$id'";
		$queryFactBd   = mysql_query($sqlFactBd, $link);

		$fechaFactura   = mysql_result($queryFactBd,0,'fecha_inicio');
		$newNumFactBd   = mysql_result($queryFactBd,0,'numero_factura');
		$newPrefijoFac  = mysql_result($queryFactBd,0,'prefijo');
		$estadoFactBd   = mysql_result($queryFactBd,0,'estado');
		$activoFactBd   = mysql_result($queryFactBd,0,'activo');
		$idCliente      = mysql_result($queryFactBd,0,'id_cliente');
		$idCcos         = mysql_result($queryFactBd,0,'id_centro_costo');
		$exento_iva     = mysql_result($queryFactBd,0,'exento_iva');
		$idCuentaPago   = mysql_result($queryFactBd,0,'id_configuracion_cuenta_pago');
		$cuentaPago     = mysql_result($queryFactBd,0,'cuenta_pago');
		$cuentaPagoNiif = mysql_result($queryFactBd,0,'cuenta_pago_niif');
		$id_sucursal    = mysql_result($queryFactBd,0,'id_sucursal');

		//CUENTA DE PAGO ESTADO (credito-contado)
		$sqlEstadoCuentaPago   = "SELECT estado FROM configuracion_cuentas_pago WHERE id='$idCuentaPago' AND id_empresa='$id_empresa' AND tipo='Venta'";
		$queryEstadoCuentaPago = mysql_query($sqlEstadoCuentaPago,$link);
		$estadoCuentaPago      = mysql_result($queryEstadoCuentaPago, 0, 'estado');

		$arrayCuentaPago = array('cuentaColgaap' => $cuentaPago, 'cuentaNiif' => $cuentaPagoNiif, 'estado' => $estadoCuentaPago);

		//PARA LLENAR EL CAMPO NUMERO FACTURA COMPLETO, VERIFICAMOS SI HAY UN PREFIJO PARA CONCATENARLO SI NO NO
		$newPrefijoFac      = str_replace(" ", "", $newPrefijoFac);
		$consecutivoFactura = (strlen($newPrefijoFac) > 0)? $newPrefijoFac.' '.$newNumFactBd: $newNumFactBd;

		// CONTABILIZACION FACTURA SIN PLANTILLA
		contabilizarSinPlantilla($arrayCuentaPago,$idCcos,$fechaFactura,$consecutivoFactura,$id_sucursal,$id_empresa,$id,$idCliente,$exento_iva,$link);
		contabilizarSinPlantillaNiif($arrayCuentaPago,$idCcos,$fechaFactura,$consecutivoFactura,$id_sucursal,$id_empresa,$id,$idCliente,$exento_iva,$link);

		$fecha_actual = date('Y-m-d');
		$hora_actual  = date('H:i:s');

		//INSERTAR EL LOG DE EVENTOS
		$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
				       VALUES($id,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Sincronizar','FV','Factura de Venta (SIHO)',$id_sucursal,'".$_SESSION['EMPRESA']."','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
		$queryLog = mysql_query($sqlLog,$link);
	}

?>
