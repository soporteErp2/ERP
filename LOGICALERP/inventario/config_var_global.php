<?php
	switch ($opcGrillaContable) {

		case 'EntradaAlmacen':
			$carpeta          = "entrada_almacen/";
			$tablaPrincipal   = "compras_entradas_almacen";
			$idTablaPrincipal = "id_entrada_almacen";
			$tablaInventario  = "compras_entradas_almacen_inventario";
			// $tablaRetenciones = "ventas_facturas_retenciones";
			break;
		case 'SalidaAlmacen':
			$carpeta          = "salida_almacen/";
			$tablaPrincipal   = "compras_salidas_almacen";
			$idTablaPrincipal = "id_salida_almacen";
			$tablaInventario  = "compras_salidas_almacen_inventario";
			// $tablaRetenciones = "ventas_facturas_retenciones";
			break;

		case 'Traslados':
			$carpeta          = "traslados/";
			$tablaPrincipal   = "inventario_traslados";
			$idTablaPrincipal = "id_traslado";
			$tablaInventario  = "inventario_traslados_unidades";
			break;
	}
?>