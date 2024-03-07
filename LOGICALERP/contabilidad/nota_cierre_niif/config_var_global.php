<?php
	switch ($opcGrillaContable) {
		case 'DevolucionCompra':
			$carpeta          = "notas_inventario/notas_devolucion/devolucion_compra/";
			$tablaPrincipal   = "devoluciones_compra";
			$idTablaPrincipal = "id_devolucion_compra";
			$tablaInventario  = "devoluciones_compra_inventario";
			$tablaRetenciones = "";
			break;

		case 'DevolucionVenta':
			$carpeta          = "notas_inventario/notas_devolucion/devolucion_venta/";
			$tablaPrincipal   = "devoluciones_venta";
			$idTablaPrincipal = "id_devolucion_venta";
			$tablaInventario  = "devoluciones_venta_inventario";
			$tablaRetenciones = "";
			break;

		case 'NotaCierre':
            $carpeta          = "nota_cierre/";
            $tablaPrincipal   = "nota_cierre";
            $idTablaPrincipal = "id_nota_general";
            $tablaCuentasNota  = "nota_cierre_cuentas";
            $tablaRetenciones = "";
            break;

	}
?>