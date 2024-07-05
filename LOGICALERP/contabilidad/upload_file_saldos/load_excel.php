<?php
	$id_usuario  = $_SESSION['IDUSUARIO'];
	$id_empresa  = $_SESSION['EMPRESA'];

	// $idSaldoInicial = 3;
	// $id_empresa     = 8;
	// $id_usuario     = 1;
	// $nameFileUpload = '14081426344AW0WH.xls';

	$objPHPExcel = PHPExcel_IOFactory::load($uploadDirectory . $filename . '.' . $ext);
	$arrayExcel  = $objPHPExcel->getActiveSheet()->toArray(null,true,false,false);

	$contArray = COUNT($arrayExcel);
	$contCol   = COUNT($arrayExcel[0]);

	// if($contCol > 8){ $errorLoadFile = "El archivo excel solo puede tener contenido en las columnas A, B, C, D, E, F, G"; return array('error'=> $errorLoadFile, 'debug'=> "$debugError"); }
	if(is_nan($arrayExcel[1][1]) && is_nan($arrayExcel[1][1])){ $errorLoadFile = "No se puede reconocer la columna numero de factura en el excel"; return array('error'=> $errorLoadFile, 'debug'=> "$debugError"); }
	if(is_nan($arrayExcel[1][3]) || is_nan($arrayExcel[1][4]) || is_nan($arrayExcel[1][5])){ $errorLoadFile = "No se puede reconocer las columnas de fecha en el excel"; return array('error'=> $errorLoadFile, 'debug'=> "$debugError"); }

	//==================================// CONSULTA INFORMACION SALDOS INICIAL //==================================//
	//*************************************************************************************************************//
	$sqlSaldoInicial   = "SELECT * FROM facturas_saldos_iniciales WHERE id='$idSaldoInicial' LIMIT 0,1";
	$querySaldoInicial = mysql_query($sqlSaldoInicial);

	$tipo_factura       = mysql_result($querySaldoInicial, 0, 'tipo_factura');
	$id_sucursal        = mysql_result($querySaldoInicial, 0, 'id_sucursal');
	$id_cuenta_pago     = mysql_result($querySaldoInicial, 0, 'id_cuenta_pago');
	$cuenta_pago        = mysql_result($querySaldoInicial, 0, 'cuenta_pago');
	$estado_cuenta_pago = mysql_result($querySaldoInicial, 0, 'estado_cuenta_pago');
	$fecha_saldo        = mysql_result($querySaldoInicial, 0, 'fecha_factura');

	$valueInsert = "";
	$contError   = 0;

	//CICLO SQL ID DE LOS TERCEROS
	$whereNitTercero = "";
	for ($contFila=0; $contFila < $contArray; $contFila++) {
		$nitTercero = $arrayExcel[$contFila][7];			//nit tercero

		if($nitTercero == '' || strlen($nitTercero) <= 3)continue;
		$whereNitTercero .= "numero_identificacion='$nitTercero' OR ";
	}
	$whereNitTercero = substr($whereNitTercero, 0,-4);

	$arrayTercero = array();
	$sqlTercero   = "SELECT id,numero_identificacion AS nit FROM terceros WHERE id_empresa='$id_empresa' AND activo=1 AND tercero = 1 AND ($whereNitTercero)";
	$queryTercero = mysql_query($sqlTercero);
	while ($row = mysql_fetch_assoc($queryTercero)) {
		$arrayTercero[$row['nit']*1] = $row['id'];
	}

	//CICLO PARA INSERTAR FACTURAS
	for ($contFila=1; $contFila < $contArray; $contFila++) {

		$fecha_contabilidad = $fecha_saldo;

		$prefijo     = $arrayExcel[$contFila][0];			//prefijo
		$numero      = $arrayExcel[$contFila][1];			//numero
		$observacion = $arrayExcel[$contFila][2];			//observacion
		$fecha_year  = $arrayExcel[$contFila][3];			//fecha_year
		$fecha_mes   = $arrayExcel[$contFila][4];			//fecha_mes
		$fecha_dia   = $arrayExcel[$contFila][5];			//fecha_dia
		$valor       = ABS($arrayExcel[$contFila][6]);		//valor
		$nitTercero  = $arrayExcel[$contFila][7]*1;			//nit tercero

		$fecha_year_c = $arrayExcel[$contFila][8];			//fecha_year_contabilidad
		$fecha_mes_c  = $arrayExcel[$contFila][9];			//fecha_mes_contabilidad
		$fecha_dia_c  = $arrayExcel[$contFila][10];		//fecha_dia_contabilidad

		if($fecha_year_c > 0 && $fecha_mes_c > 0 && $fecha_dia_c > 0){ $fecha_contabilidad =  $fecha_year_c.'-'.$fecha_mes_c.'-'.$fecha_dia_c; }

		$id_tercero = $arrayTercero[$nitTercero];

		$valor2      = ($estado_cuenta_pago == 'Credito')? $valor: 0;
		$fecha       = $fecha_year.'-'.$fecha_mes.'-'.$fecha_dia;
		$con_factura = $prefijo.' '.$numero;

		if(is_nan($numero) || $numero == 0 || $fecha=='' || $valor==0 || is_nan($valor)) continue;

		$valueInsert = 1;
		$valueInsertFV .= "(NOW(),
						'$fecha_contabilidad',
						'$fecha',
						'$prefijo',
						'$numero',
						'$con_factura',
						'$id_tercero',
						'$id_usuario',
						'$observacion',
						'$valor',
						'$valor2',
						'$id_cuenta_pago',
						'$cuenta_pago',
						'$id_empresa',
						'$id_sucursal',
						'$idSaldoInicial'),";

		$valueInsertFC .= "(NOW(),
						'$fecha_contabilidad',
						'$fecha',
						'$prefijo',
						'$numero',
						'$id_tercero',
						'$id_usuario',
						'$observacion',
						'$valor',
						'$valor2',
						'$id_cuenta_pago',
						'$cuenta_pago',
						'$id_empresa',
						'$id_sucursal',
						'$idSaldoInicial'),";
	}

	if($valueInsert == ""){ $errorLoadFile = "No se encontraron filas con informacion en el archivo excel!"; return array('error'=> $errorLoadFile, 'debug'=> "$debugError"); }
	//====================================// INSERT CUERPO SALDOS DE FACTURAS //===================================//
	//*************************************************************************************************************//

	$valueInsertFV  = substr($valueInsertFV, 0, -1);
	$valueInsertFC  = substr($valueInsertFC, 0, -1);
	if($tipo_factura == 'FV'){
		$sqlFacturas   = "INSERT INTO ventas_facturas (
							fecha_creacion,
							fecha_inicio,
							fecha_vencimiento,
							prefijo,
							numero_factura,
							numero_factura_completo,
							id_cliente,
							id_usuario,
							observacion,
							total_factura,
							total_factura_sin_abono,
							id_configuracion_cuenta_pago,
							configuracion_cuenta_pago,
							id_empresa,
							id_sucursal,
							id_saldo_inicial)
						VALUES $valueInsertFV";
	}
	else if($tipo_factura == 'FC'){
		$sqlFacturas   = "INSERT INTO compras_facturas (
							fecha_registro,
							fecha_inicio,
							fecha_final,
							prefijo_factura,
							numero_factura,
							id_proveedor,
							id_usuario,
							observacion,
							total_factura,
							total_factura_sin_abono,
							id_configuracion_cuenta_pago,
							configuracion_cuenta_pago,
							id_empresa,
							id_sucursal,
							id_saldo_inicial)
						VALUES $valueInsertFC";
	}

	$queryFacturas = mysql_query($sqlFacturas);
	if(!$queryFacturas){ $debugError =  $sqlFacturas; $errorLoadFile = "Ha ocurrido un problema al insertar las facturas del excel!"; return array('error'=> $errorLoadFile, 'debug'=> "$debugError"); }

?>
