<?php
	switch ($opcGrillaContable) {
		case 'FacturaCompraCuentas':
			$carpeta          = "facturacion_cuentas/";
			$tablaPrincipal   = "compras_facturas";
			$idTablaPrincipal = "id_factura_compra";
			$tablaCuentasNota  = "compras_facturas_cuentas";
			break;

		case 'FacturaCompra':
			$carpeta          = "facturacion_cuentas/";
			$tablaPrincipal   = "compras_facturas";
			$idTablaPrincipal = "id_factura_compra";
			$tablaCuentasNota  = "compras_facturas_cuentas";
			break;

	}
?>