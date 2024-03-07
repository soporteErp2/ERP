<?php
	switch ($opcGrillaContable) {
		case 'CotizacionVenta':
			$carpeta          = "cotizacion";
			$tablaPrincipal   = "ventas_cotizaciones";
			$idTablaPrincipal = "id_cotizacion_venta";
			$tablaInventario  = "ventas_cotizaciones_inventario";
			$tablaRetenciones = "";
			break;

		case 'PedidoVenta':
			$carpeta          = "pedido";
			$tablaPrincipal   = "ventas_pedidos";
			$idTablaPrincipal = "id_pedido_venta";
			$tablaInventario  = "ventas_pedidos_inventario";
			$tablaRetenciones = "";
			break;

		case 'RemisionesVenta':
			$carpeta          = "remisiones";
			$tablaPrincipal   = "ventas_remisiones";
			$idTablaPrincipal = "id_remision_venta";
			$tablaInventario  = "ventas_remisiones_inventario";
			$tablaRetenciones = "";
			break;

		case 'FacturaVenta':
			$carpeta          = "facturacion";
			$tablaPrincipal   = "ventas_facturas";
			$idTablaPrincipal = "id_factura_venta";
			$tablaInventario  = "ventas_facturas_inventario";
			$tablaRetenciones = "ventas_facturas_retenciones";
			break;
	}
?>