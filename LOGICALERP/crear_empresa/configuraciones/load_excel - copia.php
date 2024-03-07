<?php
		include_once ('configuraciones/configuracion_col/array_config_cuentas.php');

		set_include_path(get_include_path() . PATH_SEPARATOR . '../../misc/PHPExcel/Classes/');
		include_once('PHPExcel/IOFactory.php');		/** PHPExcel_IOFactory */

		$rutaServer = $_SERVER['DOCUMENT_ROOT'];
    	$findme     = 'LOGICALERP';
    	$pos        = strpos($rutaServer, $findme);
    	if (!$pos && $_SERVER['HTTP_HOST'] != 'erp.plataforma.co') {
    	    $rutaServer=$_SERVER['DOCUMENT_ROOT'].'/LOGICALERP';
    	}
    	$serv = $rutaServer."/";
    	$ruta1= $serv.'ARCHIVOS_PROPIOS/documentos_crear_empresa/';

		$nameFileUpload = $ruta1.$nameFileUpload;

		$objPHPExcel    = PHPExcel_IOFactory::load($nameFileUpload);
		$arrayColgaap   = $objPHPExcel->getActiveSheet()->toArray(null,true,false,false);

		$contArray = COUNT($arrayColgaap);
		$contCol   = COUNT($arrayColgaap[0]);

		// if($contCol != 2){ $error .= "El archivo excel solo puede tener contenido en las columas A y B<br/>"; return; }
		if(is_nan($arrayColgaap[1][0]) && is_nan($arrayColgaap[1][1])){ $error .= "No se puede reconocer la columna codigo puc en el excel<br/>"; return; }
		else if(!is_nan($arrayColgaap[1][0])){ $colCodigo = 0; $colDetalle=1; }
		else if(!is_nan($arrayColgaap[1][1])){ $colCodigo = 1; $colDetalle=0; }
		else{ $error .= "No se puede leer el archivo Excel<br/>"; return; }

		$valueInsertNiif    = "";
		$valueInsertColgaap = "";
		/*======================================= TABLA COLGAAP =======================================*/
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

			$valueInsertColgaap .= "('$id_empresa', '$cuenta', '$detalle', '$codNiif','$idGrupoEmpresarial'),";
		}

		/*======================================== TABLA NIIF ========================================*/
		ksort($arrayConfigNiif);
		foreach ($arrayConfigNiif as $cuenta => $detalle) {
			$valueInsertNiif .= "('$id_empresa', '$cuenta', '$detalle','$idGrupoEmpresarial'),";
		}

		$valueInsertNiif    = substr($valueInsertNiif, 0, -1);
		$valueInsertColgaap = substr($valueInsertColgaap, 0, -1);

		$sqlPucColgaap = "INSERT INTO puc (id_empresa,cuenta,descripcion,cuenta_niif,grupo_empresarial) VALUES $valueInsertColgaap";
		$sqlPucNiif    = "INSERT INTO puc_niif (id_empresa,cuenta,descripcion,grupo_empresarial) VALUES $valueInsertNiif";

?>
