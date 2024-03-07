<?php

	include_once("../../configuracion/conectar.php");
	include_once("../../configuracion/define_variables.php");

	exit; //BLOQUEO SE SEGURIDAD PARA QUE ESTE SCRIPT NO SE A EJECUTADO

	$sql = "SELECT
				AC.id AS id_asiento,
				CF.id AS id_factura,
				CF.id_empresa AS id_empresa,
				CF.sucursal AS sucursal,
				CF.bodega AS bodega,
				CF.fecha_registro AS fecha_registro,
				CF.cuenta_pago AS cuenta_pago,
				CF.prefijo_factura AS prefijo_factura,
				CF.numero_factura AS numero_factura,
				CF.consecutivo AS consecutivo,
				CF.proveedor AS proveedor,
				CF.total_factura AS total_factura,
				CF.total_factura_sin_abono AS total_factura_sin_abono,
				AC.debe AS debe,
				AC.numero_documento_cruce AS numero_factura_asiento,
				AC.consecutivo_documento AS consecutivo_CE
			FROM compras_facturas CF
				INNER JOIN asientos_colgaap AC
			WHERE CF.id = AC.id_documento_cruce
				AND AC.tipo_documento_cruce = 'FC'
				AND AC.tipo_documento = 'CE'
				AND CF.total_factura_sin_abono > 0
				AND CF.id_cuenta_pago = AC.id_cuenta
				AND CF.id_empresa = AC.id_empresa
				AND AC.debe = CF.total_factura
			ORDER BY CF.consecutivo";
	$query = mysql_query($sql,$link);

	$where = "";
	while ($row = mysql_fetch_assoc($query)) {
		$where .= "OR id='$row[id_factura]' ";
	}

	echo "SELECT id AS id_factura,
				id_empresa,
				sucursal,
				bodega,
				fecha_registro,
				cuenta_pago,
				prefijo_factura,
				numero_factura,
				consecutivo,
				proveedor,
				total_factura,
				total_factura_sin_abono,
				debug
			FROM compras_facturas
			WHERE activo=1
				AND ($where)";


?>