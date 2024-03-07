<?php
	/*
		ARRAY configuracion cuentas niif contra colgap
		$arrayConfigColgaap[cuenta_colgaap] = array(cuenta_niif,detalle Niif,excepción = array(codigo colgaap),subcuentas = array subcuentas);

		*copiar -> copia la cuenta de un digito remplaza numero y nombre
		*copiarTodo -> crea la cuenta en niif reemplazando el numero y el nombre y copia todas las cuentas hijas
		*duplicar ->
	*/

	/*======================= ARRAY CUENTAS COLGAAP =======================*/
	/***********************************************************************/

	//ACTIVOS
	$arrayConfigColgaap[1]    = array('cuenta' => 1, 'action' => 'copiar', 'detalle' => 'ACTIVOS');
	$arrayConfigColgaap[11]   = array('cuenta' => 11, 'action' => 'copiarTodo', 'detalle' => 'EFECTIVO Y EQUIVALENTES DE EFECTIVO');
	$arrayConfigColgaap[12]   = array('cuenta' => 12, 'action' => 'copiarTodo', 'detalle' => 'ACTIVOS FINANCIEROS');
	$arrayConfigColgaap[13]   = array('cuenta' => 13, 'action' => 'copiarTodo', 'detalle' => 'DEUDORES COMERCIALES Y OTRAS CUENTAS POR COBRAR');
	$arrayConfigColgaap[14]   = array('cuenta' => 14, 'action' => 'copiarTodo', 'detalle' => 'INVENTARIOS');
	$arrayConfigColgaap[15]   = array('cuenta' => 15, 'action' => 'copiarTodo', 'detalle' => 'PROPIEDAD PLANTA Y EQUIPO');
	$arrayConfigColgaap[16]   = array('cuenta' => 17, 'action' => 'copiarTodo', 'detalle' => 'ACTIVOS INTANGIBLES');
	$arrayConfigColgaap[18]   = array('cuenta' => 18, 'action' => 'copiarTodo', 'detalle' => 'OTROS ACTIVOS CORRIENTES');
	$arrayConfigColgaap[19]   = array('cuenta' => 19, 'action' => 'copiarTodo', 'detalle' => 'INVERSIONES EN ASOCIADAS');

	/*MANEJO DE CUENTAS DE EXEPCIONES*/
	// $arrayConfigColgaap[1355] = array('cuenta' => 1605, 'action' => 'copiarTodo', 'detalle' => 'ACTIVOS POR IMPUESTOS CORRIENTE');
	// $arrayConfigColgaap[1360] = array('cuenta' => 1610, 'action' => 'copiarTodo', 'detalle' => 'ACTIVOS POR IMPUESTOS DIFERIDOS');

	//PASIVOS
	$arrayConfigColgaap[2]    = array('cuenta' => 2, 'action' => 'copiar', 'detalle' => 'PASIVOS');
	$arrayConfigColgaap[22]   = array('cuenta' => 22, 'action' => 'copiarTodo', 'detalle' => 'ACREEDORES COMERCIALES Y OTRAS CUENTAS POR PAGAR');
	$arrayConfigColgaap[2365] = array('cuenta' => 2365, 'action' => 'copiarTodo', 'detalle' => '');
	$arrayConfigColgaap[2368] = array('cuenta' => 2368, 'action' => 'copiarTodo', 'detalle' => '');
	$arrayConfigColgaap[2369] = array('cuenta' => 2369, 'action' => 'copiarTodo', 'detalle' => '');
	$arrayConfigColgaap[2408] = array('cuenta' => 2408, 'action' => 'copiarTodo', 'detalle' => '');
	$arrayConfigColgaap[25]   = array('cuenta' => 25, 'action' => 'copiarTodo', 'detalle' => 'PARTE CORRIENTE DE OBLIGACIONES POR BENEFICIOS A LOS EMPLEADOS');
	$arrayConfigColgaap[26]   = array('cuenta' => 26, 'action' => 'copiarTodo', 'detalle' => 'PROVISIONES A CORTO PLAZO');

	// PATRIMONIO
	$arrayConfigColgaap[3]    = array('cuenta' => 3, 'action' => 'copiar', 'detalle' => 'PATRIMONIO');
	$arrayConfigColgaap[31]   = array('cuenta' => 31, 'action' => 'copiar', 'detalle' => 'CAPITAL EN ACCIONES');
	$arrayConfigColgaap[3105] = array('cuenta' => 3105, 'action' => 'duplicar');

	//NGRESOS
	$arrayConfigColgaap[4]    = array('cuenta' => 4, 'action' => 'copiar', 'detalle' => 'INGRESOS');
	$arrayConfigColgaap[41]   = array('cuenta' => 41, 'action' => 'copiarTodo', 'detalle' => 'INGRESOS DE ACTIVIDADES ORDINARIAS');
	$arrayConfigColgaap[42]   = array('cuenta' => 42, 'action' => 'copiarTodo', 'detalle' => 'OTROS INGRESOS');

	//GASTOS
	$arrayConfigColgaap[5]    = array('cuenta' => 5, 'action' => 'copiar', 'detalle' => 'GASTOS');
	$arrayConfigColgaap[51]   = array('cuenta' => 51, 'action' => 'copiarTodo', 'detalle' => 'GASTOS DE ADMINISTRACION');
	//$arrayConfigColgaap[5105] = array('cuenta' => 5110, 'action' => 'copiarTodo', 'detalle' => 'GASTOS POR BENEFICIOS A LOS EMPLEADOS');
	//$arrayConfigColgaap[5160] = array('cuenta' => 5160, 'action' => 'copiarTodo', 'detalle' => 'GASTOS DE DEPRECIACION Y AMORTIZACION');
	$arrayConfigColgaap[59]   = array('cuenta' => 59, 'action' => 'copiarTodo', 'detalle' => 'CIERRE DE INGRESOS, GASTOS Y COSTOS');

	// COSTOS
	$arrayConfigColgaap[6]    = array('cuenta' => 6, 'action' => 'copiar', 'detalle' => 'COSTOS');
	$arrayConfigColgaap[61]   = array('cuenta' => 61, 'action' => 'copiarTodo', 'detalle' => 'COSTOS DE VENTAS');

	// COSTOS DE PRODUCCION
	$arrayConfigColgaap[7]    = array('cuenta' => 7, 'action' => 'copiar', 'detalle' => 'COSTOS DE PRODUCCCION');


	/*======================== ARRAY CUENTAS NIIF ========================*/
	/**********************************************************************/

	// ACTIVO
	$arrayConfigNiif['16 ']   = 'ACTIVOS POR IMPUESTOS';

	//PASIVO
	$arrayConfigNiif['21 ']   = 'PASIVOS FINANCIEROS';
	$arrayConfigNiif['2105 '] = 'PRESTAMOS A CORTO PLAZO';
	$arrayConfigNiif['2110 '] = 'PARTE CORRIENTE DE PRESTAMOS BANCARIOS';
	$arrayConfigNiif['2115 '] = 'PARTE CORRIENTE DE OBLIGACIONES POR ARRENDAMIENTOS FINANCIEROS';
	$arrayConfigNiif['2120 '] = 'SOBREGIROS BANCARIOS';
	$arrayConfigNiif['2125 '] = 'PRESTAMOS BANCARIOS';
	$arrayConfigNiif['2130 '] = 'OBLIGACIONES POR ARRENDAMIENTOS FINANCIEROS';
	$arrayConfigNiif['23 ']   = 'PASIVOS POR IMPUESTOS CORRIENTES';
	$arrayConfigNiif['24 ']   = 'PASIVOS POR IMPUESTOS DIFERIDOS';

	// PATRIMONIO
	$arrayConfigNiif['32 ']   = 'GANANCIAS ACUMULADAS';
	$arrayConfigNiif['3205 '] = 'GANANCIAS SOBRE COBERTURAS DE RIESGOS DE TASA DE CAMBIO DE LA MONEDA EXTRANJERA DE COMPROMISOS EN FIRME';
	$arrayConfigNiif['3210 '] = 'PARTICIPACIONES NO CONTROLADORAS';
	$arrayConfigNiif['3215 '] = 'PATRIMONIO ATRIBUIBLE A LOS PROPIETARIOS DE LA CONTROLADORA';

	//GASTOS
	$arrayConfigNiif['5105 '] = 'GASTOS DE ADMINISTRACION';
	$arrayConfigNiif['52 ']   = 'OTROS GASTOS';

	// COSTOS DE PRODUCCION
	$arrayConfigNiif['71 ']   = 'COSTOS DE DISTRIBUCION';
	$arrayConfigNiif['75 ']   = 'COSTOS FINANCIEROS';

?>