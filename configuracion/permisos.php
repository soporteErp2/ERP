<?php

//ARRAY QUE DEFINE LOS PERMISOS DE CADA ICONO (EL ORDEN DE ESTOS PERMISOS DEBE SER IGUAL A LAS CONSULTAS QUE ESTAN ORGANIZADAS POR "ID")

	$fecha_actual=date("Y-m-d");
	$fecha_vencimiento=$_SESSION['PLAN_FECHA_VENCIMIENTO'];
	// $primera = "31/01/2000";
	// $segunda = "31/01/2000";
	// VERIFICAR SI AUN TIENE EL PLAN CON DIAS DISPONIBLES O SI YA SE VENCIO
	$dias_plan=compararFechas($fecha_vencimiento,$fecha_actual);
	// echo '<script>console.log("'.$fecha_actual.' - '.$fecha_vencimiento.' : '.$dias_plan.' ");</script>';
	// CREAR LA VARIABLE JAVASCRIPT CON EL VALOR DE LOS DIAS RESTANTES DEL PLAN
	echo '<script>dias_plan_erp='.$dias_plan.';</script>';

	if ($dias_plan<-10) {
		/* **
		DEFINE LOS PERMISOS DE CADA ICONO
		Array PERMISO
		Indice = id tabla modulos
		Valor = id permiso tabla empleados permisos
		*/
		$PERMISO = array(
			0  => 'false',//0 ->  CENTROS COMERCIALES
			1  => 'false',//1 ->  VISOR DE INMUEBLES
			2  => 'false',//2 ->
			3  => 'false',//3 ->
			4  => 'false',//4 ->
			5  => 'false',//5 ->
			6  => 'false',//6 ->
			7  => 'false',//7 ->
			8  => 'false',//8 ->  MODULO POS
			9  => 'false',//9 ->  MODULO VENTAS
			10 => 'false',//10 ->  MODULO COMPRAS
			11 => 'false',//11 ->  MODULO TERCEROS
			12 => 'false',//12 ->  CONTABILIDAD
			13 => 'false',//13 ->  PANEL DE CONTROL
			14 => 'false',//14 ->  MODULO INVENTARIO
			15 => 'false',//15 ->  MODULO ACTIVOS FIJOS
			16 => 'false',//16 ->
			17 => 'false',//17 ->  MODULO RECURSO HUMANO
			18 => 'true',//18 ->
			19 => 'false',//19 -> MODULO DE INFORMES
			20 => 'false',//20 -> MODULO DE NOMINA
			21 => 'true',//21 -> MI-ERP
			22 => 'true', //22 -> SOPORTE
			23 => 'true', //23 -> MODULO PRODUCCION
			24  => 'false',//24 ->
			25 => 'false',  //25 -> MODULO CRM
			26 => 'false',  //26 -> CALENDARIO
			27 => 'false'  //27 -> VIDEOCONFERENCIA
		);
	}
	else{
		/* **
		DEFINE LOS PERMISOS DE CADA ICONO
		Array PERMISO
		Indice = id tabla modulos
		Valor = id permiso tabla empleados permisos
		*/
		$PERMISO = array(
			0  => user_permisos(2),//0 ->  CENTROS COMERCIALES
			1  => user_permisos(3),//1 ->  VISOR DE INMUEBLES
			2  => 'true',//2 ->
			3  => 'true',//3 ->
			4  => 'true',//4 ->
			5  => 'true',//5 ->
			6  => 'true',//6 ->
			7  => 'true',//7 ->
			8  => user_permisos(58),//8 ->  MODULO POS
			9  => user_permisos(4),//9 ->  MODULO VENTAS
			10 => user_permisos(31),//10 ->  MODULO COMPRAS
			11 => user_permisos(47),//11 ->  MODULO TERCEROS
			12 => user_permisos(48),//12 ->  CONTABILIDAD
			13 => user_permisos(49),//13 ->  PANEL DE CONTROL
			14 => user_permisos(50),//14 ->  MODULO INVENTARIO
			15 => user_permisos(59),//15 ->  MODULO ACTIVOS FIJOS
			16 => 'true',//16 ->
			17 => user_permisos(55),  //17 ->  MODULO RECURSO HUMANO
			18 => 'true',//18 ->
			19 => user_permisos(62),//19 -> MODULO DE INFORMES
			20 => user_permisos(92),//20 -> MODULO DE NOMINA
			21 => 'true',//21 -> MI-ERP
			22 => 'true',//22 -> SOPORTE
			23 => 'true',//23 -> MODULO PRODUCCION
			24 => 'false',//24 ->
			25 => user_permisos(188), //25 -> MODULO CRM
			26 => user_permisos(189), //25 -> MODULO CALENDARIO
			27 => 'true'//27 -> MODULO VIDEOCONFERENCIA
		);
	}


	function compararFechas($primera, $segunda){
		$valoresPrimera = explode ("-", $primera);
		$valoresSegunda = explode ("-", $segunda);

		$diaPrimera  = $valoresPrimera[2];
		$mesPrimera  = $valoresPrimera[1];
		$anyoPrimera = $valoresPrimera[0];

		$diaSegunda  = $valoresSegunda[2];
		$mesSegunda  = $valoresSegunda[1];
		$anyoSegunda = $valoresSegunda[0];

		$diasPrimeraJuliano = gregoriantojd($mesPrimera, $diaPrimera, $anyoPrimera);
		$diasSegundaJuliano = gregoriantojd($mesSegunda, $diaSegunda, $anyoSegunda);

		if(!checkdate($mesPrimera, $diaPrimera, $anyoPrimera)){ return 0; }		// "La fecha ".$primera." no es válida";
		elseif(!checkdate($mesSegunda, $diaSegunda, $anyoSegunda)){ return 0; } 		// "La fecha ".$segunda." no es válida";
		else{ return  $diasPrimeraJuliano - $diasSegundaJuliano; }

	}
?>
