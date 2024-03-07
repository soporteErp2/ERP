<?php

	require_once("../../../configuracion/conectar.php");
	require_once("../../../configuracion/define_variables.php");
	require_once("contabilizar_bd.php");
	require_once("contabilizar_niif_bd.php");

	exit; //BLOQUEO SE SEGURIDAD PARA QUE ESTE SCRIPT NO SE A EJECUTADO

	$sqlFv   = "SELECT id,
					numero_factura,
					prefijo,
					id_cliente,
					id_configuracion_cuenta_pago,
					cuenta_pago,
					cuenta_pago_niif,
					cuenta_anticipo,
					valor_anticipo,
					id_centro_costo,
					exento_iva,
					fecha_inicio,
					id_bodega,
					id_sucursal,
					id_empresa
				FROM ventas_facturas
				WHERE activo=1
					AND debug_descuento=1
					AND id_saldo_inicial=0
					AND estado=1
				GROUP BY id";

	// echo $sqlFv; exit;
	$queryFv = mysql_query($sqlFv,$link);

	if(!$queryFv){ echo '<script>alert("Error!\nNo se ha establecido la comunicacion con el servidor.")</script>'; return; }

	while ($rowFv = mysql_fetch_assoc($queryFv)) {

		$idFactura      = $rowFv['id'];
		$fechaFactura   = $rowFv['fecha_inicio'];
		$newNumFactBd   = $rowFv['numero_factura'];
		$newPrefijoFac  = $rowFv['prefijo'];
		$idCliente      = $rowFv['id_cliente'];
		$idCuentaPago   = $rowFv['id_configuracion_cuenta_pago'];
		$cuentaPago     = $rowFv['cuenta_pago'];
		$cuentaPagoNiif = $rowFv['cuenta_pago_niif'];
		$cuentaAnticipo = $rowFv['cuenta_anticipo'];
		$valorAnticipo  = $rowFv['valor_anticipo'];
		$idCcos         = $rowFv['id_centro_costo'];
		$exento_iva     = $rowFv['exento_iva'];
		$id_empresa     = $rowFv['id_empresa'];
		$id_sucursal    = $rowFv['id_sucursal'];
		$id_bodega      = $rowFv['id_bodega'];

		//CUENTA DE PAGO ESTADO (credito-contado)
		$sqlEstadoCuentaPago   = "SELECT estado FROM configuracion_cuentas_pago WHERE id='$idCuentaPago' AND id_empresa='$id_empresa' AND tipo='venta'";
		$queryEstadoCuentaPago = mysql_query($sqlEstadoCuentaPago,$link);
		$estadoCuentaPago      = mysql_result($queryEstadoCuentaPago, 0, 'estado');

		$arrayCuentaPago = array('cuentaColgaap' => $cuentaPago, 'cuentaNiif' => $cuentaPagoNiif, 'estado' => $estadoCuentaPago);

		$newPrefijoFac      = str_replace(" ", "", $newPrefijoFac);
		$consecutivoFactura = (strlen($newPrefijoFac) > 0)? $newPrefijoFac.' '.$newNumFactBd: $newNumFactBd;

		contabilizarSinPlantilla($arrayCuentaPago,$idCcos,$arrayAnticipo,$fechaFactura,$consecutivoFactura,$id_bodega,$id_sucursal,$id_empresa,$idFactura,$idCliente,$exento_iva,$link);
		contabilizarSinPlantillaNiif($arrayCuentaPago,$idCcos,$arrayAnticipo,$fechaFactura,$consecutivoFactura,$id_bodega,$id_sucursal,$id_empresa,$idFactura,$idCliente,$exento_iva,$link);

	}



?>