<?php

	/*============================ CONFIG CUENTAS NIIF DEFAULT ===========================*/
	/**************************************************************************************/
	$arrayCuentasDefault = array('compra' => array(143501 => array('niif' => true, 'estado' => 'debito', 'detalle' => 'items_compra_precio'),
													240802 => array('niif' => true, 'estado' => 'debito', 'detalle' => 'items_compra_impuesto'),
													220501 => array('niif' => true, 'estado' => 'credito', 'detalle' => 'items_compra_contraPartida_precio'),
													519530 => array('niif' => true, 'estado' => 'debito', 'detalle' => 'items_compra_gasto'),
													151610 => array('niif' => true, 'estado' => 'debito', 'detalle' => 'items_compra_activo_fijo'),
													613520 => array('niif' => true, 'estado' => 'debito', 'detalle' => 'items_compra_costo')
												),
									'venta' => array(143501 => array('niif' => true, 'estado' => 'credito', 'detalle' => 'items_venta_costo'),
													613516 => array('niif' => true, 'estado' => 'debito', 'detalle' => 'items_venta_contraPartida_costo'),
													413520 => array('niif' => true, 'estado' => 'credito', 'detalle' => 'items_venta_precio'),
													130505 => array('niif' => true, 'estado' => 'debito', 'detalle' => 'items_venta_contraPartida_precio'),
													240801 => array('niif' => true, 'estado' => 'credito', 'detalle' => 'items_venta_impuesto'),
													417501 => array('niif' => false, 'estado' => 'debito', 'detalle' => 'items_venta_devprecio')
												)
									);

	/*=========================== CONFIG CUENTAS DE PAGO  DEFAULT ==========================*/
	/****************************************************************************************/
	$arrayCuentaPagoDefault = array(22050101 => array('type' => 'Compra', 'detalle' => 'PROVEEDORES', 'estado' => 'Credito'),
									13050501 => array('type' => 'Venta', 'detalle' => 'CLIENTES', 'estado' => 'Credito'),
									11050501 => array('type' => 'Venta', 'detalle' => 'VENTA CAJA', 'estado' => 'Contado'),
									11100501 => array('type' => 'Venta', 'detalle' => 'VENTA BANCOS', 'estado' => 'Contado'));

?>