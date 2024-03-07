<?php

	//ARRAY CONFIGURACIONES CONTABLES POR PAIS SIMILARES
	$arrayConfigPais1 = array(
								44,		// CHILE
								49,		// COLOMBIA
								63,		// REPUBLICA DOMINICANA
								65,		// SAN SALVADOR
								170,	// PANAMA
								55,		// COSTA RICA
								140,	// MEXICO
								100,  // HONDURAS
								233,  // ESTADOS UNIDOS
                173,  // PERU
                94,   // GUATEMALA
                22,   // BOLIVIA
                11,   // ARGENTINA
                64,   // ECUADOR
                158,  // NICARAGUA
							);

	foreach ($arrayConfigPais1 as $idConfigPais) {
		// COLGAAP
		$arrayNaturaleza[$idConfigPais]['items_compra_precio']               = array('naturaleza' => 'debito',  'prefijo' => 'D', 'btnSinc'=>'si');
		$arrayNaturaleza[$idConfigPais]['items_compra_impuesto']             = array('naturaleza' => 'debito',  'prefijo' => 'D', 'btnSinc'=>'si');
		$arrayNaturaleza[$idConfigPais]['items_compra_activo_fijo']          = array('naturaleza' => 'debito',  'prefijo' => 'D', 'btnSinc'=>'si');
		$arrayNaturaleza[$idConfigPais]['items_compra_gasto']                = array('naturaleza' => 'debito',  'prefijo' => 'D', 'btnSinc'=>'si');
		$arrayNaturaleza[$idConfigPais]['items_compra_costo']                = array('naturaleza' => 'debito',  'prefijo' => 'D', 'btnSinc'=>'si');
		$arrayNaturaleza[$idConfigPais]['items_venta_precio']                = array('naturaleza' => 'credito', 'prefijo' => 'C', 'btnSinc'=>'si');
		$arrayNaturaleza[$idConfigPais]['items_venta_devprecio']             = array('naturaleza' => 'debito',  'prefijo' => 'D', 'btnSinc'=>'no');
		$arrayNaturaleza[$idConfigPais]['items_venta_impuesto']              = array('naturaleza' => 'credito', 'prefijo' => 'C', 'btnSinc'=>'si');
		$arrayNaturaleza[$idConfigPais]['items_venta_costo']                 = array('naturaleza' => 'credito', 'prefijo' => 'C', 'btnSinc'=>'si');
		$arrayNaturaleza[$idConfigPais]['items_venta_contraPartida_costo']   = array('naturaleza' => 'debito',  'prefijo' => 'D', 'btnSinc'=>'si');
		$arrayNaturaleza[$idConfigPais]['items_venta_contraPartida_precio']  = array('naturaleza' => 'debito',  'prefijo' => 'D', 'btnSinc'=>'si');
		$arrayNaturaleza[$idConfigPais]['items_compra_contraPartida_precio'] = array('naturaleza' => 'credito', 'prefijo' => 'C', 'btnSinc'=>'si');

		// NIIF
		$arrayNaturaleza[$idConfigPais]['items_compra_precio_niif']               = array('naturaleza' => 'debito',  'prefijo' => 'D');
		$arrayNaturaleza[$idConfigPais]['items_compra_impuesto_niif']             = array('naturaleza' => 'debito',  'prefijo' => 'D');
		$arrayNaturaleza[$idConfigPais]['items_compra_activo_fijo_niif']          = array('naturaleza' => 'debito',  'prefijo' => 'D');
		$arrayNaturaleza[$idConfigPais]['items_compra_gasto_niif']                = array('naturaleza' => 'debito',  'prefijo' => 'D');
		$arrayNaturaleza[$idConfigPais]['items_compra_costo_niif']                = array('naturaleza' => 'debito',  'prefijo' => 'D');
		$arrayNaturaleza[$idConfigPais]['items_venta_precio_niif']                = array('naturaleza' => 'credito', 'prefijo' => 'C');
		$arrayNaturaleza[$idConfigPais]['items_venta_impuesto_niif']              = array('naturaleza' => 'credito', 'prefijo' => 'C');
		$arrayNaturaleza[$idConfigPais]['items_venta_costo_niif']                 = array('naturaleza' => 'credito', 'prefijo' => 'C');
		$arrayNaturaleza[$idConfigPais]['items_venta_contraPartida_costo_niif']   = array('naturaleza' => 'debito',  'prefijo' => 'D');
		$arrayNaturaleza[$idConfigPais]['items_venta_contraPartida_precio_niif']  = array('naturaleza' => 'debito',  'prefijo' => 'D');
		$arrayNaturaleza[$idConfigPais]['items_compra_contraPartida_precio_niif'] = array('naturaleza' => 'credito', 'prefijo' => 'C');
	}

?>