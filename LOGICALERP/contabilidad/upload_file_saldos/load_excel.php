<?php
    include_once('../../../misc/excel/Classes/PHPExcel.php');
	$id_usuario  = $_SESSION['IDUSUARIO'];
	$id_empresa  = $_SESSION['EMPRESA'];

	$objPHPExcel = PHPExcel_IOFactory::load($uploadDirectory . $filename . '.' . $ext);
	$arrayExcel  = $objPHPExcel->getActiveSheet()->toArray(null,true,false,false);

	$contArray = COUNT($arrayExcel);
	$contCol   = COUNT($arrayExcel[0]);
	$debug     = "documentos";

	$arrayLabelCol = array(
                            0  => 'Columna <b>A</b>',
                            1  => 'Columna <b>B</b>',
                            2  => 'Columna <b>C</b>',
                            3  => 'Columna <b>D</b>',
                            4  => 'Columna <b>E</b>',
                            5  => 'Columna <b>F</b>',
                            6  => 'Columna <b>G</b>',
                            7  => 'Columna <b>H</b>',
                            8  => 'Columna <b>I</b>',
                            9  => 'Columna <b>J</b>',
                            10 => 'Columna <b>K</b>',
                            );

	$sql1   = "SELECT tipo_factura,id_sucursal,id_cuenta_pago,cuenta_pago,fecha_factura
				FROM facturas_saldos_iniciales WHERE id='$idSaldoInicial' LIMIT 0,1";
	$query = $mysql->query($sql1,$mysql->link);

	$tipo_factura       = $mysql->result($query, 0, 'tipo_factura');
	$id_sucursal        = $mysql->result($query, 0, 'id_sucursal');
	$id_cuenta_pago     = $mysql->result($query, 0, 'id_cuenta_pago');
	$cuenta_pago        = $mysql->result($query, 0, 'cuenta_pago');
	$estado_cuenta_pago = $mysql->result($query, 0, 'estado_cuenta_pago');
	$fecha_saldo        = $mysql->result($query, 0, 'fecha_factura');

	// CONSULTAR LOS TERCEROS
    $sql="SELECT id,numero_identificacion,nombre FROM terceros WHERE activo=1 AND id_empresa=$id_empresa ";
    $query=$mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)) {
        $arrayTerceros[$row['numero_identificacion']] = array('id' => $row['id'],'nombre'=>$row['nombre'] );
    }

    // CONSULTAR LAS CUENTAS DE PAGO
    $sql="SELECT id,id_cuenta,cuenta,cuenta_niif FROM configuracion_cuentas_pago WHERE activo=1 AND id_empresa=$id_empresa ";
    $query=$mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)) {
        $arrayCuentaPago[$row['id']] = array('cuenta' => $row['cuenta'],'cuenta_niif'=>$row['cuenta_niif'],'id_cuenta'=>$row['id_cuenta'] );
    }

    $contFilas = 0;
    foreach ($arrayExcel as $filas => $arrayExcelCol) {
    	// SALTAR LA FILA DE TITULOS
		if ($contFilas<=0) { $contFilas++; continue; }

		// VALIDAR QUE LAS FECHAS SEAN TIPO INT
		if(!is_numeric($arrayExcelCol[3])) {
            $arrayError[$arrayExcelCol[1]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[3]</div><div class='cell' data-col='3'>El campo año debe ser un dato numerico</div></div>";
        }
        if(!is_numeric($arrayExcelCol[4])) {
            $arrayError[$arrayExcelCol[1]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[4]</div><div class='cell' data-col='3'>El campo mes debe ser un dato numerico</div></div>";
        }
        if(!is_numeric($arrayExcelCol[5])) {
            $arrayError[$arrayExcelCol[1]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[5]</div><div class='cell' data-col='3'>El campo dia debe ser un dato numerico</div></div>";
        }

        // VALIDAR QUE LA FACTURA TENGA SALDO
        if ($arrayExcelCol[6]<=0 || $arrayExcelCol[6]=='') {
            $arrayError[$arrayExcelCol[1]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[6]</div><div class='cell' data-col='3'>El campo saldo debe tener un valor numerico</div></div>";
        }

        // VALIDAR EL TERCERO
        if ($arrayExcelCol[7]<>'' && !array_key_exists("$arrayExcelCol[7]",$arrayTerceros)) {
            $arrayError[$arrayExcelCol[1]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[7]</div><div class='cell' data-col='3'> No existe el tercero </div></div>";
        }

		$valor      = ABS($arrayExcelCol[6]);
		// $valor2     = ($estado_cuenta_pago == 'Credito')? $valor: 0;
		$id_tercero = $arrayTerceros[$arrayExcelCol[7]]['id'];

		// if(is_nan($numero) || $numero == 0 || $fecha=='' || $valor==0 || is_nan($valor)) continue;

		$valueInsert = 1;
		$valueInsertFV .= "(NOW(),
						'$fecha_saldo',
						'$arrayExcelCol[3]-$arrayExcelCol[4]-$arrayExcelCol[5]',
						'$arrayExcelCol[0]',
						'$arrayExcelCol[1]',
						'$arrayExcelCol[0] $arrayExcelCol[1]',
						'$id_tercero',
						'$id_usuario',
						'$arrayExcelCol[2]',
						'$valor',
						'$valor',
						'$id_cuenta_pago',
						'$cuenta_pago',
						'".$arrayCuentaPago[$id_cuenta_pago]['id_cuenta']."',
						'".$arrayCuentaPago[$id_cuenta_pago]['cuenta']."',
						'".$arrayCuentaPago[$id_cuenta_pago]['cuenta_niif']."',
						'$id_empresa',
						'$id_sucursal',
						'$idSaldoInicial'),";

		$valueInsertFC .= "(NOW(),
						'$fecha_saldo',
						'$arrayExcelCol[3]-$arrayExcelCol[4]-$arrayExcelCol[5]',
						'$arrayExcelCol[0]',
						'$arrayExcelCol[1]',
						'$id_tercero',
						'$id_usuario',
						'$arrayExcelCol[2]',
						'$valor',
						'$valor',
						'$id_cuenta_pago',
						'$cuenta_pago',
						'".$arrayCuentaPago[$id_cuenta_pago]['id_cuenta']."',
						'".$arrayCuentaPago[$id_cuenta_pago]['cuenta']."',
						'".$arrayCuentaPago[$id_cuenta_pago]['cuenta_niif']."',
						'$id_empresa',
						'$id_sucursal',
						'$idSaldoInicial'),";

    }

    if (empty($arrayError)) {
    	$valueInsertFV  = substr($valueInsertFV, 0, -1);
		$valueInsertFC  = substr($valueInsertFC, 0, -1);
    	if($tipo_factura == 'FV'){
			$sql   = "INSERT INTO ventas_facturas (
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
								id_cuenta_pago,
								cuenta_pago,
								cuenta_pago_niif,
								id_empresa,
								id_sucursal,
								id_saldo_inicial)
							VALUES $valueInsertFV";
		}
		else if($tipo_factura == 'FC'){
			$sql   = "INSERT INTO compras_facturas (
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
								id_cuenta_pago,
								cuenta_pago,
								cuenta_pago_niif,
								id_empresa,
								id_sucursal,
								id_saldo_inicial)
							VALUES $valueInsertFC";
		}
		$query=$mysql->query($sql,$mysql->link);
		if (!$query) {
            $debug      = "bd";
            $arrayError[1] = "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'> Error al insertar los registros </div><div class='cell' data-col='3' title='".$mysql->error()."'><b>".$mysql->errno()." </b>:".$mysql->error()."</div></div>";
		}
    }

?>
