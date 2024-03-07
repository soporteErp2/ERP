<?php
	switch ($opcGrillaContable) {
		case 'DevolucionCompra':
			$carpeta               = "notas_inventario/notas_devolucion/devolucion_compra";
			$tablaPrincipal        = "devoluciones_compra";
			$idTablaPrincipal      = "id_devolucion_compra";
			$tablaInventario       = "devoluciones_compra_inventario";
			$tablaRetenciones      = "compras_facturas_retenciones";
			$campoTablaRetenciones = "id_factura_compra";
			break;

		case 'DevolucionVenta':
			$carpeta               = "notas_inventario/notas_devolucion/devolucion_venta";
			$tablaPrincipal        = "devoluciones_venta";
			$idTablaPrincipal      = "id_devolucion_venta";
			$tablaInventario       = "devoluciones_venta_inventario";
			$tablaRetenciones      = "ventas_facturas_retenciones";
			$campoTablaRetenciones = "id_factura_venta";
			break;
	}
?>