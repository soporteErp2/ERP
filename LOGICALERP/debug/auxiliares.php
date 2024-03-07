<?php

	include_once("../../configuracion/conectar.php");

	// exit; // BLOQUEO SE SEGURIDAD PARA QUE ESTE SCRIPT NO SE A EJECUTADO

	//=================// CUENTAS DE 2 DIGITOS QUE HACEN FALTA //=================//
	/******************************************************************************/
	$falta = "";
	$sqlCuenta   = "SELECT LEFT(cuenta,2) AS cuenta_corta,nombre
					FROM puc_carga_auxiliares
					WHERE
						LEFT(cuenta,2) NOT IN (
							SELECT
								cuenta
							FROM
								puc
							WHERE
								id_empresa = 2
							AND activo=1
						)
					GROUP BY cuenta_corta";
	$queryCuenta = mysql_query($sqlCuenta,$link);
	while ($rowCuenta = mysql_fetch_assoc($queryCuenta)) { $falta .= "$rowCuenta[cuenta_corta],'$rowCuenta[nombre]'<br>"; }

	//=================// CUENTAS DE 4 DIGITOS QUE HACEN FALTA //=================//
	/******************************************************************************/
	$sqlCuenta   = "SELECT LEFT(cuenta,4) AS cuenta_corta,nombre
					FROM puc_carga_auxiliares
					WHERE
						LEFT(cuenta,4) NOT IN (
							SELECT
								cuenta
							FROM
								puc
							WHERE
								id_empresa = 2
							AND activo=1
						)
					GROUP BY cuenta_corta";
	$queryCuenta = mysql_query($sqlCuenta,$link);
	while ($rowCuenta = mysql_fetch_assoc($queryCuenta)) { $falta .= "$rowCuenta[cuenta_corta],'$rowCuenta[nombre]'<br>"; }

	//=================// CUENTAS DE 6 DIGITOS QUE HACEN FALTA //=================//
	/******************************************************************************/
	$sqlCuenta   = "SELECT LEFT(cuenta,6) AS cuenta_corta,nombre
					FROM puc_carga_auxiliares
					WHERE
						LEFT(cuenta,6) NOT IN (
							SELECT
								cuenta
							FROM
								puc
							WHERE
								id_empresa = 2
							AND activo=1
						)
					GROUP BY cuenta_corta";
	$queryCuenta = mysql_query($sqlCuenta,$link);
	while ($rowCuenta = mysql_fetch_assoc($queryCuenta)) { $falta .= "$rowCuenta[cuenta_corta],'$rowCuenta[nombre]'<br>"; }

	if($falta != ""){ echo "NO SE PUEDE SINCRONIZAR LAS CUENTAS AUXILIARES POR QUE FALTAN LAS SIGUIENTES CUENTAS PUC:<br><br>".$falta; exit; }

	//========================// SINC CUENTAS AUXILIARES //=======================//
	/******************************************************************************/
	$valueNiif    = "";
	$valueColgaap = "";
	$sqlAux   = "SELECT cuenta,nombre
					FROM puc_carga_auxiliares
					WHERE
						cuenta NOT IN (
							SELECT
								cuenta
							FROM
								puc
							WHERE
								id_empresa = 2
							AND activo=1
						)";
	$queryAux = mysql_query($sqlAux,$link);
	while ($rowAux = mysql_fetch_assoc($queryAux)) {
		$valueNiif    .= "('$rowAux[cuenta]','$rowAux[nombre]',2),";
		$valueColgaap .= "('$rowAux[cuenta]','$rowAux[nombre]','$rowAux[cuenta]',2),";
	}

	if($valueColgaap != ''){
		echo"INSERT INTO puc (cuenta,descripcion,cuenta_niif,id_empresa) VALUES $valueColgaap
			<br><br>
			INSERT INTO puc_niif (cuenta,descripcion,id_empresa) VALUES $valueNiif";
	}
	else{ echo"TODAS LAS CUENTAS AUXILIARES YA HAN SIDO SINCRONIZADAS"; }

?>