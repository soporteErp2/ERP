<?php

	include_once('../../misc/excel/Classes/PHPExcel.php');
	include_once ('configuraciones/configuracion_col/array_config_cuentas.php');


	$ruta = $_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/documentos_crear_empresa/';

	$objPHPExcel    = PHPExcel_IOFactory::load($ruta.$nameFileUpload);
	$arrayColgaap   = $objPHPExcel->getActiveSheet()->toArray(null,true,false,false);

	$contArray = COUNT($arrayColgaap);
	$contCol   = COUNT($arrayColgaap[0]);

	if($contCol > 4){ deleteInfoEmpresa("El archivo excel solo puede tener contenido en las columas A, B, C, y D<br/>",2); }
	if(is_nan($arrayColgaap[1][0]) && is_nan($arrayColgaap[1][1])){ deleteInfoEmpresa("No se puede reconocer la columna codigo puc en el excel<br/>",2); }
	else if(!is_nan($arrayColgaap[1][0])){ $colCodigo = 0; $colDetalle=1; }
	else if(!is_nan($arrayColgaap[1][1])){ $colCodigo = 1; $colDetalle=0; }
	else{ deleteInfoEmpresa("No se puede leer el archivo Excel<br/>",2); }

	/*======================================= TABLA COLGAAP =======================================
		@$contFila -> contador de las filas devueltas del excel
		@$colCodigo -> numero de la columa donde esta el codigo de la cuenta(0 o 1)
		@$colDetalle -> numero de la columa donde esta el detalle de la cuenta (0 o 1)

		@cuenta -> numero de cuenta
		@detalle -> detalle de la cuenta
		@debito -> saldo debito de la cuenta
		@credito -> saldo credito de la cuenta

		@$acumDebito -> acumulador saldo debito de la cuenta
		@$acumCredito -> acumulador saldo credito de la cuenta
	*/

	$acumDebito       = 0;
	$acumCredito      = 0;
	$valueNiif        = "";
	$valueColgaap     = "";
	$valueNotaNiif    = "";
	$valueNotaColgaap = "";

	$whereIdPuc = "";

	for ($contFila=0; $contFila < $contArray; $contFila++) {
		$codNiif = '';
		$cuenta  = $arrayColgaap[$contFila][$colCodigo];
		$detalle = $arrayColgaap[$contFila][$colDetalle];

		if(is_nan($cuenta) || $cuenta == 0)continue;

		$cuentaX2 = substr($cuenta, 0, 2);
		$cuentaX4 = substr($cuenta, 0, 4);

		if(@$arrayConfigColgaap[$cuenta]['action']=='copiar'){											//SI LA CUENTA DEBE SER COPIADA
			$codNiif = $arrayConfigColgaap[$cuenta]['cuenta'];
			$arrayConfigNiif[$codNiif] = $arrayConfigColgaap[$cuenta]['detalle'];
		}
		else if(@$arrayConfigColgaap[$cuentaX4]['action'] == 'copiarTodo' && $cuentaX4 == $cuenta){  	//CONFIGURACION CUENTAS DE 4 DIGITOS
			$codNiif = $arrayConfigColgaap[$cuenta]['cuenta'];
			$detalleNiif = ($arrayConfigColgaap[$cuenta]['detalle'] == '')? $detalle: $arrayConfigColgaap[$cuenta]['detalle'];

			$arrayConfigNiif[$codNiif] = $detalleNiif;
		}
		else if(@$arrayConfigColgaap[$cuentaX4]['action'] == 'copiarTodo'){							//HIJOS DE CONFIGURACION DE CUENTAS DE 4 DIGITOS
			$bodyCuentaX4 = substr($cuenta, 4, 20);

			$codNiif = $arrayConfigColgaap[$cuentaX4]['cuenta'].$bodyCuentaX4;
			$arrayConfigNiif[$codNiif] = $detalle;
		}
		else if(@$arrayConfigColgaap[$cuentaX2]['action'] == 'copiarTodo' && $cuentaX2 == $cuenta){	//CONFIGURACION DE CUENTAS DE 2 DIGITOS
			$codNiif     = $arrayConfigColgaap[$cuenta]['cuenta'];
			$detalleNiif = ($arrayConfigColgaap[$cuenta]['detalle'] == '')? $detalle: $arrayConfigColgaap[$cuenta]['detalle'];

			$arrayConfigNiif[$codNiif] = $detalleNiif;
		}
		else if(@$arrayConfigColgaap[$cuentaX2]['action'] == 'copiarTodo'){							//HIJOS DE CONFIGURACION DE CUENTAS DE 2 DIGITOS
			$bodyCuentaX2 = substr($cuenta, 2, 20);

			$codNiif = $arrayConfigColgaap[$cuentaX2]['cuenta'].$bodyCuentaX2;
			$arrayConfigNiif[$codNiif] = $detalle;
		}
		else if(@$arrayConfigColgaap[$cuentaX2]['action'] == 'duplicar'){							//DUPLICAR CUENTAS EN CONFIGURACION DE 2 DIGITOS
			$bodyCuentaX2 = substr($cuenta, 2, 20);

			$codNiif = $arrayConfigColgaap[$cuentaX2]['cuenta'].$bodyCuentaX2;
			$arrayConfigNiif[$codNiif] = $detalle;
		}
		else if(@$arrayConfigColgaap[$cuentaX4]['action'] == 'duplicar'){							//DUPLICAR CUENTAS EN CONFIGURACION DE 4 DIGITOS
			$bodyCuentaX4 = substr($cuenta, 4, 20);

			$codNiif = $arrayConfigColgaap[$cuentaX4]['cuenta'].$bodyCuentaX4;
			$arrayConfigNiif[$codNiif] = $detalle;
		}

		$valueColgaap .= "('$id_empresa', '$cuenta', '$detalle', '$codNiif','$idGrupoEmpresarial'),";

		//MANEJO DE SALDOS
		/*
			@debito  = $arrayColgaap[$contFila][2];
			@credito = $arrayColgaap[$contFila][3];
		*/

		$debito  = $arrayColgaap[$contFila][2];
		$credito = $arrayColgaap[$contFila][3];

		if($debito > 0 || $credito > 0){
			if($debito > 0){ $acumDebito  += $debito; }
			if($credito > 0){ $acumCredito += $credito; }

			$whereIdPuc .= "OR cuenta=$cuenta ";

			$arrayConfigSaldos[$cuenta]['debito']  = $debito;
			$arrayConfigSaldos[$cuenta]['credito'] = $credito;
		}
	}

	//QUE LOS SALDOS DEBITOS Y CREDITOS SI SON MAYORES A CERO SEHAN IGUALES
	$acumCredito = ROUND($acumCredito,2) * 1;
	$acumDebito  = ROUND($acumDebito,2) * 1;
	if($acumCredito != $acumDebito && ($acumCredito > 0 || $acumDebito > 0)){
		deleteInfoEmpresa("<br>LOS SALDOS DEBITO/CREDITO NO CUMPLEN DOBLE PARTIDA CONTABLE!",1);
	}

	/*======================================= TABLA NIIF =======================================
		@$contFila -> contador de las filas devueltas del excel
		@$colCodigo -> numero de la columa donde esta el codigo de la cuenta(0 o 1)
		@$colDetalle -> numero de la columa donde esta el detalle de la cuenta (0 o 1)

		@cuenta -> numero de cuenta
		@detalle -> detalle de la cuenta
		@debito -> saldo debito de la cuenta
		@credito -> saldo credito de la cuenta

		@$acumDebito -> acumulador saldo debito de la cuenta
		@$acumCredito -> acumulador saldo credito de la cuenta
	*/
	ksort($arrayConfigNiif);
	foreach ($arrayConfigNiif as $cuenta => $detalle) {
		$valueNiif .= "('$id_empresa', '$cuenta', '$detalle','$idGrupoEmpresarial'),";
	}

	$valueNiif    = substr($valueNiif, 0, -1);
	$valueColgaap = substr($valueColgaap, 0, -1);

	//INSERT PUC COLGAAP
	$sqlPucColgaap   = "INSERT INTO puc (id_empresa,cuenta,descripcion,cuenta_niif,grupo_empresarial) VALUES $valueColgaap";
	$queryPucColgaap = mysql_query($sqlPucColgaap,$link);							//CUENTAS PUC COLGAAP
	if (!$queryPucColgaap) { $msjError .= $sqlPucColgaap."NO SE INSERTO EL PUC COLGAAP<br/>"; }

	//INSERT PUC NIIF
	$sqlPucNiif   = "INSERT INTO puc_niif (id_empresa,cuenta,descripcion,grupo_empresarial) VALUES $valueNiif";
	$queryPucNiif = mysql_query($sqlPucNiif,$link); 								//CUENTAS PUC NIIF
	if (!$queryPucNiif) { deleteInfoEmpresa("NO SE INSERTO EL PUC NIF",1); }


	/*==================================== NOTA COLGAAP AUTO ====================================
		@$contFila -> contador de las filas devueltas del excel

	*/
	if($acumCredito > 0 && $acumCredito == $acumDebito){

		//INSERT HEAD NOTA
		$sqlInsertNota   = "INSERT INTO nota_contable_general(sinc_nota, id_empresa, id_sucursal, fecha_registro, fecha_nota, fecha_finalizacion, estado)
							VALUES ('colgaap_niif', '$id_empresa', '$id_sucursal', NOW(), NOW(), NOW(), 0)";
		$queryInsertNota = mysql_query($sqlInsertNota,$link);

		$sqlIdNota = "SELECT LAST_INSERT_ID()";
		$idNota    = mysql_result(mysql_query($sqlIdNota,$link),0,0);

		//SELECT ID CUENTA
		$whereIdPuc = substr($whereIdPuc, 3);
		$sqlIdPuc   = "SELECT id,cuenta FROM puc WHERE id_empresa='$id_empresa' AND activo=1 AND ($whereIdPuc)";
		$queryIdPuc = mysql_query($sqlIdPuc,$link);
		while ($row = mysql_fetch_array($queryIdPuc)) {
			$idPuc   = $row['id'];
			$cuenta  = $row['cuenta'];
			$debito  = $arrayConfigSaldos[$cuenta]['debito'];
			$credito = $arrayConfigSaldos[$cuenta]['credito'];
			$valueNotaPuc .= "('$idNota','$idPuc', '$debito', '$credito', '$id_empresa'),";
		}

		$valueNotaPuc = substr($valueNotaPuc, 0, -1);

		$sqlInsertCuentasNota   = "INSERT INTO nota_contable_general_cuentas(id_nota_general,id_puc,debe,haber,id_empresa) VALUES $valueNotaPuc";
		$queryInsertCuentasNota = mysql_query($sqlInsertCuentasNota,$link);
	}
?>