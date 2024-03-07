<?php

	require_once("../../../configuracion/conectar.php");
	require_once("../../../configuracion/define_variables.php");
	require_once("contabilizar_bd.php");
	require_once("contabilizar_niif_bd.php");

	exit; //BLOQUEO SE SEGURIDAD PARA QUE ESTE SCRIPT NO SE A EJECUTADO

	$sqlFacturaCompra   = "SELECT id,
								fecha_inicio,
								estado,
								activo,
								id_configuracion_cuenta_pago,
								cuenta_pago,
								cuenta_pago_niif,
								contabilidad_manual,
								id_bodega,
								id_sucursal,
								id_proveedor,
								consecutivo,
								id_empresa
							FROM compras_facturas
							WHERE activo=1
								AND debug_ccos=1
								AND factura_por_cuentas='false'
								AND id_saldo_inicial=0
								AND estado=1
								AND (id_empresa=1 OR id_empresa=47)
							GROUP BY id";
	$queryfacturaCompra = mysql_query($sqlFacturaCompra,$link);

	if(!$queryfacturaCompra){ echo '<script>alert("Error!\nNo se ha establecido la comunicacion con el servidor.")</script>'; return; }

	while ($rowFc = mysql_fetch_assoc($queryfacturaCompra)) {
		$consecutivoFc       = $rowFc['consecutivo'];
		$idProveedor         = $rowFc['id_proveedor'];
		$idFactura           = $rowFc['id'];
		$id_empresa          = $rowFc['id_empresa'];
		$id_sucursal         = $rowFc['id_sucursal'];
		$id_bodega           = $rowFc['id_bodega'];
		$contFactura         = $rowFc['cont_factura'];
		$estadoFactura       = $rowFc['estado'];
		$activoFactura       = $rowFc['activo'];
		$fechaInicioFactura  = $rowFc['fecha_inicio'];
		$contabilidad_manual = $rowFc['contabilidad_manual'];

		$idCuentaPago   = $rowFc['id_configuracion_cuenta_pago'];
		$cuentaPago     = $rowFc['cuenta_pago'];
		$cuentaPagoNiif = $rowFc['cuenta_pago_niif'];

		//CUENTA DE PAGO ESTADO (credito-contado)
		$sqlEstadoCuentaPago   = "SELECT estado FROM configuracion_cuentas_pago WHERE id='$idCuentaPago' AND id_empresa='$id_empresa' AND tipo='Compra'";
		$queryEstadoCuentaPago = mysql_query($sqlEstadoCuentaPago,$link);
		$estadoCuentaPago      = mysql_result($queryEstadoCuentaPago, 0, 'estado');

		$arrayCuentaPago = array('cuentaColgaap' => $cuentaPago, 'cuentaNiif' => $cuentaPagoNiif, 'estado' => $estadoCuentaPago);

		contabilizarSinPlantilla($arrayCuentaPago,$fechaInicioFactura,$consecutivoFc,$id_bodega,$id_sucursal,$id_empresa,$idFactura,$idProveedor,$link);
		contabilizarSinPlantillaNiif($arrayCuentaPago,$fechaInicioFactura,$consecutivoDocReferencia,$id_bodega,$id_sucursal,$id_empresa,$idFactura,$idProveedor,$link);
	}



?>