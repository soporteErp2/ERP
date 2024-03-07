<?php

    function contabilizarSinPlantilla($arrayCuentaPago,$idCcos,$fechaFactura,$consecutivoFactura,$idSucursal,$idEmpresa,$idFactura,$idCliente,$exento_iva,$link){

		$sqlDoc = "SELECT VF.id,
						VF.id_consecutivo_referencia AS id_referencia,
						VF.nombre_consecutivo_referencia AS nombre_referencia,
						VF.id_inventario AS id_item,
						VF.codigo,
						VF.cantidad,
						VF.costo_unitario AS precio,
						VF.costo_inventario AS costo,
						VF.descuento,
						VF.tipo_descuento,
						VF.id_impuesto,
						VF.valor_impuesto,
						VF.inventariable,
						I.cuenta_venta AS cuenta_iva
					FROM ventas_facturas_inventario AS VF LEFT JOIN impuestos AS I ON(
							I.activo=1
							AND I.id=VF.id_impuesto
						)
					WHERE VF.id_factura_venta='$idFactura' AND VF.activo=1";
		$queryDoc = mysql_query($sqlDoc,$link);

		while($rowDoc = mysql_fetch_array($queryDoc)){
			$whereIdItemsCuentas .= ($whereIdItemsCuentas != '')? ' OR ': ' ';
			$whereIdItemsCuentas .='id_items = '.$rowDoc['id_item'];

		}

		$sqlItemsCuentas = "SELECT id, id_items,descripcion, puc, tipo
							FROM items_cuentas
							WHERE activo=1
								AND id_empresa='$idEmpresa'
								AND estado='venta'
								AND ($whereIdItemsCuentas)
							GROUP BY id_items,descripcion
							ORDER BY id_items ASC";
		$queryItemsCuentas = mysql_query($sqlItemsCuentas,$link);

		while ($rowCuentasItems = mysql_fetch_array($queryItemsCuentas)) {

			if($rowCuentasItems['descripcion'] == 'contraPartida_precio'){ $rowCuentasItems['puc'] = $cuentaPago; }

			$arrayCuentasItems[$rowCuentasItems['id_items']][$rowCuentasItems['descripcion']]= array('estado' => $rowCuentasItems['tipo'], 'cuenta'=> $rowCuentasItems['puc']);

			$valueInsertContabilizacion .= "('".$rowCuentasItems['id_items']."',
											'".$rowCuentasItems['puc']."',
											'".$rowCuentasItems['tipo']."',
											'".$rowCuentasItems['descripcion']."',
											'$idFactura',
											'FV',
											'$idEmpresa',
											'$idSucursal',
											'$idBodega'),";
		}


		$valueInsertContabilizacion = substr($valueInsertContabilizacion, 0, -1);
		$sqlContabilizar   = "INSERT INTO contabilizacion_compra_venta (
								id_item,
								codigo_puc,
								caracter,
								descripcion,
								id_documento,
								tipo_documento,
								id_empresa,
								id_sucursal,
								id_bodega)
							VALUES $valueInsertContabilizacion";
		$queryContabilizar = mysql_query($sqlContabilizar,$link);

    }

?>
