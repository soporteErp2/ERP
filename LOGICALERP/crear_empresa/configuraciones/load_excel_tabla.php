<?php

error_reporting(E_ALL);
set_time_limit(0);

date_default_timezone_set('Europe/London');

?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>

<?php

	set_include_path(get_include_path() . PATH_SEPARATOR . '../../../misc/PHPExcel/Classes/');

	/** PHPExcel_IOFactory */
	include 'PHPExcel/IOFactory.php';
	include 'configuracion_col/arrays.php';

	$inputFileName = $_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/documentos_crear_empresa/1400536235FD8K20.xlsx';
	$objPHPExcel   = PHPExcel_IOFactory::load($inputFileName);
	// echo 'Loading file ',pathinfo($inputFileName,PATHINFO_BASENAME),' using IOFactory to identify the format<br />';

	$arrayColgaap = $objPHPExcel->getActiveSheet()->toArray(null,true,false,false);

	// print_r($arrayColgaap);
	$contArray = COUNT($arrayColgaap);
	$contCol   = COUNT($arrayColgaap[0]);

	// if($contCol != 2){ echo '<script>alert("Aviso,\nEl archivo excel solo puede tener contenido en las columas A y B")</script>'; exit; }
	if(is_nan($arrayColgaap[1][0]) && is_nan($arrayColgaap[1][1])){ echo '<script>alert("Aviso,\nNo se puede reconocer la columna codigo puc en el excel")</script>'; exit; }
	else if(!is_nan($arrayColgaap[1][0])){ $colCodigo = 0; $colDetalle=1; }
	else if(!is_nan($arrayColgaap[1][1])){ $colCodigo = 1; $colDetalle=0; }
	else{ echo '<script>alert("Aviso,\nNo se puede leer el archivo Excel")</script>'; exit; }

	/*======================================= TABLA COLGAAP =======================================*/
	$html = '<div style="overflow:hidden; font-size:12px;">
				<div style="width:48%; float:left; border-bottom:1px solid #000;">
					<div style="float:left; width:100%; backgroun-color:green; text-align:center;">CUENTAS COLGAAP</div>
					<div style="overflow:hidden; border-bottom:1px solid #000; width:100%;">
						<div style="float:left; width:30px;">&nbsp;</div>
						<div style="float:left; width:80px;">Cuenta</div>
						<div style="float:left; width:calc(100% - 190px);">Descripcion</div>
						<div style="float:left; width:80px;">Cuenta Niif</div>
					</div>';
	for ($contFila=0; $contFila < $contArray; $contFila++) {
		$codNiif = '';
		$cuenta  = $arrayColgaap[$contFila][$colCodigo];
		$detalle = $arrayColgaap[$contFila][$colDetalle];

		$cuentaX2 = substr($cuenta, 0, 2);
		$cuentaX4 = substr($cuenta, 0, 4);

		if(@$arrayConfigColgaap[$cuenta]['action']=='copiar'){											//SI LA CUENTA DEBE SER COPIADA
			$codNiif = $arrayConfigColgaap[$cuenta]['cuenta'];
			$arrayConfigNiif[$codNiif.' '] = $arrayConfigColgaap[$cuenta]['detalle'];
		}
		else if(@$arrayConfigColgaap[$cuentaX4]['action'] == 'copiarTodo' && $cuentaX4 == $cuenta){  	//CONFIGURACION CUENTAS DE 4 DIGITOS
			$codNiif = $arrayConfigColgaap[$cuenta]['cuenta'];
			$detalleNiif = ($arrayConfigColgaap[$cuenta]['detalle'] == '')? $detalle: $arrayConfigColgaap[$cuenta]['detalle'];

			$arrayConfigNiif[$codNiif.' '] = $detalleNiif;
		}
		else if(@$arrayConfigColgaap[$cuentaX4]['action'] == 'copiarTodo'){							//HIJOS DE CONFIGURACION DE CUENTAS DE 4 DIGITOS
			$bodyCuentaX4 = substr($cuenta, 4, 20);

			$codNiif = $arrayConfigColgaap[$cuentaX4]['cuenta'].$bodyCuentaX4;
			$arrayConfigNiif[$codNiif.' '] = $detalle;
		}
		else if(@$arrayConfigColgaap[$cuentaX2]['action'] == 'copiarTodo' && $cuentaX2 == $cuenta){	//CONFIGURACION DE CUENTAS DE 2 DIGITOS
			$codNiif     = $arrayConfigColgaap[$cuenta]['cuenta'];
			$detalleNiif = ($arrayConfigColgaap[$cuenta]['detalle'] == '')? $detalle: $arrayConfigColgaap[$cuenta]['detalle'];

			$arrayConfigNiif[$codNiif.' '] = $detalleNiif;
		}
		else if(@$arrayConfigColgaap[$cuentaX2]['action'] == 'copiarTodo'){							//HIJOS DE CONFIGURACION DE CUENTAS DE 2 DIGITOS
			$bodyCuentaX2 = substr($cuenta, 2, 20);

			$codNiif = $arrayConfigColgaap[$cuentaX2]['cuenta'].$bodyCuentaX2;
			$arrayConfigNiif[$codNiif.' '] = $detalle;
		}


		$contGrilla = $contFila+1;
		$html .= '<div style="overflow:hidden; width:100%;">
					<div style="float:left; width:30px;">'.$contGrilla.'</div>
					<div style="float:left; width:80px;">'.$cuenta.'</div>
					<div style="float:left; width:calc(100% - 190px);">'.$detalle.'</div>
					<div style="float:left; width:80px;">'.$codNiif.'</div>
				</div>';
	}

	$html .= '</div>';

	/*======================================== TABLA NIIF ========================================*/
	$contFila = 0;
	ksort($arrayConfigNiif);
	$html .= '<div style="width:48%; margin-left:2%; float:left; border-bottom:1px solid #000;">
				<div style="float:left; width:100%; backgroun-color:green; text-align:center;">CUENTAS NIIF</div>
				<div style="overflow:hidden; border-bottom:1px solid #000; width:100%;">
					<div style="float:left; width:30px;">&nbsp;</div>
					<div style="float:left; width:80px;">Cuenta</div>
					<div style="float:left; width:calc(100% - 115px);">Descripcion</div>
				</div>';
	foreach ($arrayConfigNiif as $cuenta => $detalle) {

		$contFila++;
		$html .= '<div style="overflow:hidden; width:100%;">
					<div style="float:left; width:30px;">'.$contFila.'</div>
					<div style="float:left; width:80px;">'.$cuenta.'</div>
					<div style="float:left; width:calc(100% - 115px);">'.$detalle.'</div>
				</div>';


	}
	$html .= 	'</div>
			</div>';
	echo $html;
?>
<body>
</html>