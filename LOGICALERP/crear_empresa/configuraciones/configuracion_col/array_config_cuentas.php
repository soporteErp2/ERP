<?php
	/*
		ARRAY configuracion cuentas niif contra colgap
		$arrayConfigColgaap[cuenta_colgaap] = array(cuenta_niif,detalle Niif,excepción = array(codigo colgaap),subcuentas = array subcuentas);

		*copiar -> copia la cuenta de un digito remplaza numero y nombre
		*copiarTodo -> crea la cuenta en niif reemplazando el numero y el nombre y copia todas las cuentas hijas
		*duplicar ->
	*/

	//=======================================// CONFIGURACION CONFIGURACION NIIF //=======================================//
	//********************************************************************************************************************//

	$arrayConfigColgaap[1] = array('cuenta' => 1, 'action' => 'copiar', 'detalle' => 'ACTIVOS');
	$arrayConfigColgaap[2] = array('cuenta' => 2, 'action' => 'copiar', 'detalle' => 'PASIVOS');
	$arrayConfigColgaap[3] = array('cuenta' => 3, 'action' => 'copiar', 'detalle' => 'PATRIMONIO');
	$arrayConfigColgaap[4] = array('cuenta' => 4, 'action' => 'copiar', 'detalle' => 'INGRESOS');
	$arrayConfigColgaap[5] = array('cuenta' => 5, 'action' => 'copiar', 'detalle' => 'GASTOS');
	$arrayConfigColgaap[6] = array('cuenta' => 6, 'action' => 'copiar', 'detalle' => 'COSTOS DE VENTAS');
	$arrayConfigColgaap[7] = array('cuenta' => 7, 'action' => 'copiar', 'detalle' => 'COSTOS DE PRODUCCION O DE OPERACION');

	$arrayConfigColgaap[12] = array('cuenta' => 12, 'action' => 'copiarTodo', 'detalle' => 'INVERSIONES');
	$arrayConfigColgaap[15] = array('cuenta' => 15, 'action' => 'copiarTodo', 'detalle' => 'PROPIEDADES PLANTA Y EQUIPO');
	$arrayConfigColgaap[28] = array('cuenta' => 28, 'action' => 'copiarTodo', 'detalle' => 'OTROS PASIVOS');
	$arrayConfigColgaap[31] = array('cuenta' => 31, 'action' => 'copiarTodo', 'detalle' => 'CAPITAL SOCIAL');
	$arrayConfigColgaap[33] = array('cuenta' => 33, 'action' => 'copiarTodo', 'detalle' => 'RESERVAS');
	$arrayConfigColgaap[36] = array('cuenta' => 36, 'action' => 'copiarTodo', 'detalle' => 'RESULTADOS DEL EJERCICIO');
	$arrayConfigColgaap[37] = array('cuenta' => 37, 'action' => 'copiarTodo', 'detalle' => 'RESULTADOS DE EJERCICIOS ANTERIORES');
	$arrayConfigColgaap[41] = array('cuenta' => 41, 'action' => 'copiarTodo', 'detalle' => 'OPERACIONALES');
	$arrayConfigColgaap[59] = array('cuenta' => 59, 'action' => 'copiarTodo', 'detalle' => 'GANANCIAS Y PERDIDAS');
	$arrayConfigColgaap[61] = array('cuenta' => 61, 'action' => 'copiarTodo', 'detalle' => 'COSTO DE VENTAS Y DE PRESTACION DE SERVICIOS');
	$arrayConfigColgaap[72] = array('cuenta' => 72, 'action' => 'copiarTodo', 'detalle' => 'MANO DE OBRA DIRECTA');
	$arrayConfigColgaap[73] = array('cuenta' => 73, 'action' => 'copiarTodo', 'detalle' => 'COSTOS INDIRECTOS');
	$arrayConfigColgaap[74] = array('cuenta' => 74, 'action' => 'copiarTodo', 'detalle' => 'CONTRATOS DE SERVICIOS');


	$arrayConfigColgaap[11] = array('cuenta' => 11, 'action' => 'copiarTodo', 'detalle' => 'EFECTIVO Y EQUIVALENTES AL EFECTIVO');
	$arrayConfigColgaap[13] = array('cuenta' => 13, 'action' => 'copiarTodo', 'detalle' => 'CUENTAS COMERCIALES POR COBRAR Y OTRAS CUENTAS POR COBRAR');
	$arrayConfigColgaap[14] = array('cuenta' => 14, 'action' => 'copiarTodo', 'detalle' => 'INVENTARIOS ');
	$arrayConfigColgaap[16] = array('cuenta' => 16, 'action' => 'copiarTodo', 'detalle' => 'INTANGIBLES Y PLUSVALIA');
	$arrayConfigColgaap[17] = array('cuenta' => 17, 'action' => 'copiarTodo', 'detalle' => 'ACTIVOS POR IMPUESTOS DIFERIDOS');
	$arrayConfigColgaap[18] = array('cuenta' => 18, 'action' => 'copiarTodo', 'detalle' => 'PROPIEDAD DE INVERSION');
	$arrayConfigColgaap[21] = array('cuenta' => 21, 'action' => 'copiarTodo', 'detalle' => 'PASIVOS FINANCIEROS');
	$arrayConfigColgaap[22] = array('cuenta' => 22, 'action' => 'copiarTodo', 'detalle' => 'PROVEEDORES COMERCIALES');
	$arrayConfigColgaap[23] = array('cuenta' => 23, 'action' => 'copiarTodo', 'detalle' => 'CUENTAS POR PAGAR COMERCIALES');
	$arrayConfigColgaap[24] = array('cuenta' => 24, 'action' => 'copiarTodo', 'detalle' => 'PASIVOS POR IMPUESTOS CORRIENTES');
	$arrayConfigColgaap[25] = array('cuenta' => 25, 'action' => 'copiarTodo', 'detalle' => 'BENEFICIOS A LOS EMPLEADOS POR PAGAR');
	$arrayConfigColgaap[26] = array('cuenta' => 26, 'action' => 'copiarTodo', 'detalle' => 'CLASES DE PROVISIONES');
	$arrayConfigColgaap[27] = array('cuenta' => 27, 'action' => 'copiarTodo', 'detalle' => 'INGRESOS DIFERIDOS');
	$arrayConfigColgaap[42] = array('cuenta' => 42, 'action' => 'copiarTodo', 'detalle' => 'GANANCIALES U OTROS INGRESOS');
	$arrayConfigColgaap[51] = array('cuenta' => 51, 'action' => 'copiarTodo', 'detalle' => 'GASTOS DE ADMINISTRACION');
	$arrayConfigColgaap[52] = array('cuenta' => 52, 'action' => 'copiarTodo', 'detalle' => 'GASTOS COMERCIALES Y DE VENTAS');
	$arrayConfigColgaap[53] = array('cuenta' => 53, 'action' => 'copiarTodo', 'detalle' => 'GASTOS FINANCIEROS');
	$arrayConfigColgaap[54] = array('cuenta' => 54, 'action' => 'copiarTodo', 'detalle' => 'IMPUESTOS A LAS GANANCIAS');
	$arrayConfigColgaap[62] = array('cuenta' => 62, 'action' => 'copiarTodo', 'detalle' => 'OTROS COSTOS DE VENTA NO ORDINARIOS');


	$arrayConfigColgaap[1105] = array('cuenta' => 1105, 'action' => 'copiarTodo', 'detalle' => 'EFECTIVO EN CAJA');
	$arrayConfigColgaap[1110] = array('cuenta' => 1110, 'action' => 'copiarTodo', 'detalle' => 'SALDO EN BANCOS CUENTAS CORRIENTE');
	$arrayConfigColgaap[1120] = array('cuenta' => 1120, 'action' => 'copiarTodo', 'detalle' => 'SALDO EN BANCOS CUENTAS DE AHORRO');
	$arrayConfigColgaap[1205] = array('cuenta' => 1205, 'action' => 'copiarTodo', 'detalle' => 'INVERSIONES EN ASOCIADAS');
	$arrayConfigColgaap[1260] = array('cuenta' => 1260, 'action' => 'copiarTodo', 'detalle' => 'INVERSIONES EN NEGOCIOS CONJUNTOS');
	$arrayConfigColgaap[1299] = array('cuenta' => 1299, 'action' => 'copiarTodo', 'detalle' => 'DETERIORO');
	$arrayConfigColgaap[1305] = array('cuenta' => 1305, 'action' => 'copiarTodo', 'detalle' => 'CUENTAS POR COBRAR A CARGO DE CLIENTES');
	$arrayConfigColgaap[1355] = array('cuenta' => 1355, 'action' => 'copiarTodo', 'detalle' => 'ACTIVOS POR IMPUESTOS CORRIENTES');
	$arrayConfigColgaap[1399] = array('cuenta' => 1399, 'action' => 'copiarTodo', 'detalle' => 'DETERIORO');
	$arrayConfigColgaap[1455] = array('cuenta' => 1455, 'action' => 'copiarTodo', 'detalle' => 'MATERIALES Y SUMINISTROS CORRIENTES A CONSUMIR EN PROCESO DE PRODUCCION O PRESTACION DE SERVICIOS');
	$arrayConfigColgaap[1499] = array('cuenta' => 1499, 'action' => 'copiarTodo', 'detalle' => 'DETERIORO');
	$arrayConfigColgaap[1508] = array('cuenta' => 1508, 'action' => 'copiarTodo', 'detalle' => 'CONSTRUCCIONES EN PROCESO');
	$arrayConfigColgaap[1540] = array('cuenta' => 1540, 'action' => 'copiarTodo', 'detalle' => 'VEHICULOS');
	$arrayConfigColgaap[1599] = array('cuenta' => 1599, 'action' => 'copiarTodo', 'detalle' => 'DETERIORO');
	$arrayConfigColgaap[1625] = array('cuenta' => 1625, 'action' => 'copiarTodo', 'detalle' => 'DERECHOS DE PROPIEDAD INTELECTUAL, PATENTES Y OTROS DERECHOS DE PROPIEDAD INDUSTRIAL, SERVICIO Y DERECHO DE EXPLOTACION');
	$arrayConfigColgaap[1710] = array('cuenta' => 1710, 'action' => 'copiarTodo', 'detalle' => 'ACTIVOS POR IMPUESTOS DIFERIDOS');
	$arrayConfigColgaap[2205] = array('cuenta' => 2205, 'action' => 'copiarTodo', 'detalle' => 'PROVEEDORES NACIONALES');
	$arrayConfigColgaap[2210] = array('cuenta' => 2210, 'action' => 'copiarTodo', 'detalle' => 'PROVEEDORES DEL EXTERIOR');
	$arrayConfigColgaap[2610] = array('cuenta' => 2610, 'action' => 'copiarTodo', 'detalle' => 'Provisiones corrientes por beneficios a los empleados');
	$arrayConfigColgaap[2725] = array('cuenta' => 2725, 'action' => 'copiarTodo', 'detalle' => 'PASIVO POR IMPUESTOS DIFERIDOS');
	$arrayConfigColgaap[4210] = array('cuenta' => 4210, 'action' => 'copiarTodo', 'detalle' => 'OTROS INGRESOS FINANCIEROS');
	$arrayConfigColgaap[4215] = array('cuenta' => 4215, 'action' => 'copiarTodo', 'detalle' => 'OTROS INGRESOS DIVIDENDOS Y PARTICIPACIONES');
	$arrayConfigColgaap[4225] = array('cuenta' => 4225, 'action' => 'copiarTodo', 'detalle' => 'OTROS INGRESOS COMISIONES');
	$arrayConfigColgaap[4235] = array('cuenta' => 4235, 'action' => 'copiarTodo', 'detalle' => 'OTROS INGRESOS SERVICIOS');
	$arrayConfigColgaap[4240] = array('cuenta' => 4240, 'action' => 'copiarTodo', 'detalle' => 'OTROS INGRESOS UTILIDAD EN VENTA DE INVERSIONES');
	$arrayConfigColgaap[4245] = array('cuenta' => 4245, 'action' => 'copiarTodo', 'detalle' => 'OTROS INGRESOS UTILIDAD EN VENTA DE PROPIEDADES PLANTA Y EQUIPO');
	$arrayConfigColgaap[4250] = array('cuenta' => 4250, 'action' => 'copiarTodo', 'detalle' => 'OTROS INGRESOS RECUPERACIONES');
	$arrayConfigColgaap[4255] = array('cuenta' => 4255, 'action' => 'copiarTodo', 'detalle' => 'OTROS INGRESOS INDEMNIZACIONES');
	$arrayConfigColgaap[4265] = array('cuenta' => 4265, 'action' => 'copiarTodo', 'detalle' => 'OTROS INGRESOS INGRESOS DE EJERCICIOS ANTERIORES');
	$arrayConfigColgaap[4295] = array('cuenta' => 4295, 'action' => 'copiarTodo', 'detalle' => 'OTROS INGRESOS DIVERSOS');
	$arrayConfigColgaap[5405] = array('cuenta' => 5405, 'action' => 'copiarTodo', 'detalle' => 'IMPUESTOS A LAS GANANCIAS');
	$arrayConfigColgaap[6135] = array('cuenta' => 6135, 'action' => 'copiarTodo', 'detalle' => 'COSTO DE VENTAS');
	$arrayConfigColgaap[6155] = array('cuenta' => 6155, 'action' => 'copiarTodo', 'detalle' => 'COSTO DE PRESTACION DE SERVICIOS');
	$arrayConfigColgaap[6170] = array('cuenta' => 6170, 'action' => 'copiarTodo', 'detalle' => 'COSTO DE PRODUCCION DE EVENTOS');

	//==========================================// ARRAY NUEVAS CUENTAS NIIF //==========================================//
	//*******************************************************************************************************************//
	$arrayConfigNiif[1130] = 'INVERSIONES A CORTO PLAZO, CLASIFICADOS COMO EQUIVALENTES AL EFECTIVO';
	$arrayConfigNiif[1199] = 'DETERIORO';
	$arrayConfigNiif[1450] = 'OTROS INVENTARIOS';
	$arrayConfigNiif[1806] = 'PROPIEDADES DE INVERSION';
	$arrayConfigNiif[145020] = 'TERRENOS URBANOS';
	$arrayConfigNiif[145030] = 'CONSTRUCCIONES Y EDIFICACIONES - OFICINAS';
	$arrayConfigNiif[180604] = 'TERRENOS URBANOS';
	$arrayConfigNiif[180605] = 'TERRENOS RURALES';
	$arrayConfigNiif[180608] = 'CONSTRUCCIONES EN CURSO';
	$arrayConfigNiif[180616] = 'CONSTRUCCIONES Y EDIFICACIONES';


?>