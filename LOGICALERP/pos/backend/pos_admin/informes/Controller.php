<?php
	include('../../../../../configuracion/conectar.php');
	include('../../../../../configuracion/define_variables.php');
	$id_empresa = $_SESSION['EMPRESA'];
	// error_reporting(E_ALL);
	switch ($method) {
		case 'ClassInfoChequeCuenta':
			include 'ClassInfoChequeCuenta.php';
			$obj = new ClassInfoChequeCuenta($id_empresa,$ambiente,$desde,$hasta,$mysql);
			$obj->generate();
			break;
		case 'ClassInfoFacturaVenta':
			include 'ClassInfoFacturaVenta.php';
			$obj = new ClassInfoFacturaVenta($id_empresa,$ambiente,$fechaInicio,$fechaFin,$tipo,$numFactura,$mysql);
			$obj->generate();
			break;
		case 'ClassInfoComandas':
			include 'ClassInfoComandas.php';
			$obj = new ClassInfoComandas($id_empresa,$ambiente,$desde,$hasta,$estado,$mysql);
			$obj->generate();
			break;
		case 'ClassInfoFacturasAnuladas':
			include 'ClassInfoFacturasAnuladas.php';
			$obj = new ClassInfoFacturasAnuladas($id_empresa,$ambiente,$fechaInicio,$fechaFin,$tipo,$numFactura,$mysql);
			$obj->generate();
			break;
		case 'ClassInfoCajas':
			include 'ClassInfoCajas.php';
			$obj = new ClassInfoCajas($id_empresa,$ambiente,$fecha,$cajero,$mysql);
			$obj->generate();
			break;
		case 'ClassInfoCajasDetallado':
			include 'ClassInfoCajasDetallado.php';
			$obj = new ClassInfoCajasDetallado($id_empresa,$ambiente,$fecha_inicio,$fecha_final,$cajero,$mysql);
			$obj->generate();
			break;
		case 'ClassInfoPropinas':
			include 'ClassInfoPropinas.php';
			$obj = new ClassInfoPropinas($id_empresa,$ambiente,$fechaInicio,$fechaFin,$mysql);
			$obj->generate();
			break;
		case 'ClassInfoDescuentos':
			include 'ClassInfoDescuentos.php';
			$obj = new ClassInfoDescuentos($id_empresa,$ambiente,$fechaInicio,$fechaFin,$mysql);
			$obj->generate();
			break;
		case 'ClassInfoComprobanteDiario':
			include 'ClassInfoComprobanteDiario.php';
			$obj = new ClassInfoComprobanteDiario($id_empresa,$fecha,$mysql);
			$obj->generate();
			break;
		case 'ClassInfoDiarioProductos':
			include 'ClassInfoDiarioProductos.php';
			$obj = new ClassInfoDiarioProductos($id_empresa,$ambiente,$fecha,$mysql);
			$obj->generate();
			break;
		case 'ClassInfoProductos':
			include 'ClassInfoProductos.php';
			$obj = new ClassInfoProductos($id_empresa,$mysql);
			$obj->generate();
			break;
		case 'ClassInfoPopularidadProductos':
			include 'ClassInfoPopularidadProductos.php';
			$obj = new ClassInfoPopularidadProductos($id_empresa,$ambiente,$fechaInicio,$fechaFin,$orden,$mysql);
			$obj->generate();
			break;
		case 'ClassInfoCubiertos':
			include 'ClassInfoCubiertos.php';
			$obj = new ClassInfoCubiertos($id_empresa,$ambiente,$fechaInicio,$fechaFin,$mysql);
			$obj->generate();
			break;
		case 'ClassInfoIngredientes':
			include 'ClassInfoIngredientes.php';
			$obj = new ClassInfoIngredientes($id_empresa,$ambiente,$cod_item,$desde,$hasta,$mysql);
			$obj->generate();
			break;

		default:
			echo "Api default, enviar metodo";
			break;
	}

?>