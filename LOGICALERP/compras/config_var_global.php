<?php
	switch ($opcGrillaContable) {
		case 'RequisicionCompra':
			$carpeta          = "requisicion";
			$tablaPrincipal   = "compras_requisicion";
			$idTablaPrincipal = "id_requisicion_compra";
			$tablaInventario  = "compras_requisicion_inventario";
			$tablaRetenciones = "";
			break;

		case 'EntradaAlmacen':
			$carpeta          = "entrada_almacen";
			$tablaPrincipal   = "compras_entrada_almacen";
			$idTablaPrincipal = "id_entrada_almacen";
			$tablaInventario  = "compras_entrada_almacen_inventario";
			$tablaRetenciones = "";
			break;

		case 'OrdenCompra':
			$carpeta          = "ordenes_compra";
			$tablaPrincipal   = "compras_ordenes";
			$idTablaPrincipal = "id_orden_compra";
			$tablaInventario  = "compras_ordenes_inventario";
			$tablaRetenciones = "";
			break;

	}
?>