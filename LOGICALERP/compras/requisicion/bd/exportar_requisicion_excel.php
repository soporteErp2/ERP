<?php
	error_reporting(E_ALL);
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include('../../../../misc/excel/Classes/PHPExcel.php');

	// CREAR EL OBJETO DE EXCEL
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getProperties()
		->setCreator("LogicalSoft.com")
		->setLastModifiedBy("LogicalSoft.com")
		->setTitle("Requisicion de Compra")
		->setSubject("Formato Excel")
		->setDescription("Requisicion de Compra")
		->setKeywords("Requisicion Compra")
		->setCategory("Compra");

	// ESTABLECER LA SEGURIDAD DEL EXCEL
	$objPHPExcel->getActiveSheet()->setTitle('Requisicion de Compra')->getProtection()->setSheet(true);
	// $objPHPExcel->getActiveSheet()->getProtection()->setSort(true);
	// $objPHPExcel->getActiveSheet()->getProtection()->setInsertRows(true);
	// $objPHPExcel->getActiveSheet()->getProtection()->setFormatCells(true);
	$objPHPExcel->getActiveSheet()->getProtection()->setPassword(md5(date('Y-m-d')));

	$id_empresa  = $_SESSION['EMPRESA'];

	// CONSULTAR LA INFORMACION DE CABECERA DE LA REQUISICION
	$sql = "SELECT
                sucursal,
                bodega,
                consecutivo,
                fecha_registro,
                fecha_inicio,
                documento_solicitante,
                nombre_solicitante,
                observacion,
                estado,
                area_solicitante
            FROM compras_requisicion
            WHERE id=$id
                AND activo=1
                AND id_empresa=$id_empresa";
    $query = $mysql->query($sql,$mysql->link);

	$sucursal              = $mysql->result($query,0,'sucursal');
	$bodega                = $mysql->result($query,0,'bodega');
	$consecutivo           = $mysql->result($query,0,'consecutivo');
	$fecha_registro        = $mysql->result($query,0,'fecha_registro');
	$fecha_inicio          = $mysql->result($query,0,'fecha_inicio');
	$documento_solicitante = $mysql->result($query,0,'documento_solicitante');
	$nombre_solicitante    = $mysql->result($query,0,'nombre_solicitante');
	$observacion           = $mysql->result($query,0,'observacion');
	$estado                = $mysql->result($query,0,'estado');
	$area_solicitante      = $mysql->result($query,0,'area_solicitante');

	header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
	header('Content-Disposition: attachment;filename="Requisicion_de_compra_'.$consecutivo.'.xls"');
	header('Cache-Control: max-age=0');

	// ARMAR LA CABECERA DEL EXCEL CON LA INFORMACION DE CABECERA DEL DOCUMENTO
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $_SESSION['NOMBREEMPRESA']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', $_SESSION['NITEMPRESA']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A3', "Sucursal: ");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B3', $sucursal);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A4', "Bodega: ");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B4', $bodega);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A5', "Solicitante:");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B5', $nombre_solicitante);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A6', "Documento:");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B6', $documento_solicitante);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A7', "Area: ");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B7', $area_solicitante);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C4', "REQUISICION DE COMPRA No $consecutivo");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C5', "Fecha $fecha_inicio");


	// EJEMPLO DE FORMULA DE EXCEL
	// $objPHPExcel->getActiveSheet()->setCellValue('A7','=IF(A1=2,"si","no")');

	// COMBINAR Y CENTRAR
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:C1');
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:C2');
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('C4:E4');
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('C5:E5');

	// AJUSTAR TAMAÑO DE CELDA AL TEXTO
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth('30');
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth('50');
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth('30');

	// ESTILO DE TEXTO
	$styleTitle = array(
	    'font' => array(
	        'bold' => true,
	    ),
	    'alignment' => array(
	        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
	    ),
	);

	$styleSubTitle = array(
	    'alignment' => array(
	        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
	    ),
	);

	$styleNumberT = array(
	    'alignment' => array(
	        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
	    ),
	);

	$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleTitle);
	$objPHPExcel->getActiveSheet()->getStyle('C4')->applyFromArray($styleTitle);
	$objPHPExcel->getActiveSheet()->getStyle('A2')->applyFromArray($styleSubTitle);
	$objPHPExcel->getActiveSheet()->getStyle('C5')->applyFromArray($styleSubTitle);
	$objPHPExcel->getActiveSheet()->getStyle('B6')->applyFromArray($styleNumberT);

	// ESTILO DE LOS TITULOS PARA LOS ITEMS
	$styleTitleItems = array(
								'fill' 	=> array(
												'type'		=> PHPExcel_Style_Fill::FILL_SOLID,
												'startcolor' => array(
										             					'rgb' => '2a80b9'
										        					)
												),
								'font' => array(
										        'bold' => true,
												'color'		=> array('argb' => PHPExcel_Style_Color::COLOR_WHITE)
										    ),
							);

	// ARMAR CUERPO DEL DOCUMENTO CON TITULOS E ITEMS
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A8','CODIGO');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B8','ITEM');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C8','UNIDAD');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D8','CANTIDAD');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E8','OBSERVACION');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F8','CENTRO COSTOS');

	// APLICAR ESTILOS TITULOS ITEMS
	$objPHPExcel->getActiveSheet()->getStyle('A8')->applyFromArray($styleTitleItems);
	$objPHPExcel->getActiveSheet()->getStyle('B8')->applyFromArray($styleTitleItems);
	$objPHPExcel->getActiveSheet()->getStyle('C8')->applyFromArray($styleTitleItems);
	$objPHPExcel->getActiveSheet()->getStyle('D8')->applyFromArray($styleTitleItems);
	$objPHPExcel->getActiveSheet()->getStyle('E8')->applyFromArray($styleTitleItems);
	// SECCION DE CABECERA DEL COTIZADOR
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G8', "DIGITE # DE COTIZACION");


	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H7', "COTIZACION 1");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H8', "PROVEEDOR");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I8', "VALOR UNITARIO");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J7', "COTIZACION 2");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J8', "PROVEEDOR");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K8', "VALOR UNITARIO");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L7', "COTIZACION 3");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L8', "PROVEEDOR");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('M8', "VALOR UNITARIO");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N8', "VALOR SELECCIONADO");

	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('H7:I7');
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('J7:K7');
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('L7:M7');


	// ESTILOS COTIZADOR
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getStyle('f8')->applyFromArray($styleTitleItems);
	$objPHPExcel->getActiveSheet()->getStyle('G8')->applyFromArray($styleTitleItems);
	$objPHPExcel->getActiveSheet()->getStyle('H8')->applyFromArray($styleTitleItems);
	$objPHPExcel->getActiveSheet()->getStyle('I8')->applyFromArray($styleTitleItems);
	$objPHPExcel->getActiveSheet()->getStyle('J8')->applyFromArray($styleTitleItems);
	$objPHPExcel->getActiveSheet()->getStyle('K8')->applyFromArray($styleTitleItems);
	$objPHPExcel->getActiveSheet()->getStyle('L8')->applyFromArray($styleTitleItems);
	$objPHPExcel->getActiveSheet()->getStyle('M8')->applyFromArray($styleTitleItems);
	$objPHPExcel->getActiveSheet()->getStyle('N8')->applyFromArray($styleTitleItems);

	$objPHPExcel->getActiveSheet()->getStyle('H7')->applyFromArray($styleTitle);
	$objPHPExcel->getActiveSheet()->getStyle('J7')->applyFromArray($styleTitle);
	$objPHPExcel->getActiveSheet()->getStyle('L7')->applyFromArray($styleTitle);




	$sql="SELECT
				codigo,
				nombre_unidad_medida,
				cantidad_unidad_medida,
				nombre,
				cantidad,
				observaciones,
				codigo_centro_costo,
				centro_costo
			FROM compras_requisicion_inventario WHERE activo=1 AND id_requisicion_compra=$id";
	$query=$mysql->query($sql,$mysql->link);
	// FILA DONDE INICIA EL LISTADO DE LOS ITEMS
	$fila = 9;
	while ($row=$mysql->fetch_array($query)) {
		// ARMAR CUERPO DEL DOCUMENTO CON TITULOS E ITEMS
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit("A$fila","$row[codigo]",PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B$fila",utf8_encode($row['nombre']) );
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue("C$fila",$row['nombre_unidad_medida'].' x '.$row['cantidad_unidad_medida']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue("D$fila",$row['cantidad']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue("E$fila",utf8_encode($row['observaciones']) );
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue("F$fila",$row['codigo_centro_costo'].' - '.utf8_encode($row['centro_costo']) );
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue("N$fila","=IF(G$fila=1,I$fila*D$fila,(IF(G$fila=2,K$fila*D$fila,(IF(G$fila=3,M$fila*D$fila, \"SIN SELECCION\" ) )) ) )");
		// DESPROTEGER LA CELDA F PARA QUE PUEDAN SELECCIONAR UNA OPCION
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue("G$fila",' ');
		$objPHPExcel->getActiveSheet()->getStyle("G$fila")->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
		$objPHPExcel->getActiveSheet()->getStyle("H$fila")->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
		$objPHPExcel->getActiveSheet()->getStyle("I$fila")->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
		$objPHPExcel->getActiveSheet()->getStyle("J$fila")->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
		$objPHPExcel->getActiveSheet()->getStyle("K$fila")->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
		$objPHPExcel->getActiveSheet()->getStyle("L$fila")->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
		$objPHPExcel->getActiveSheet()->getStyle("M$fila")->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);

		$objPHPExcel->getActiveSheet()->getStyle("N$fila")->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle("I$fila")->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle("K$fila")->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle("M$fila")->getNumberFormat()->setFormatCode('#,##0.00');

		// INCREMETAR LA FILA PARA CADA SIGUIENTE ITEM
		$fila++;
	}

	// MOSTRAR LAS OBSERVACIONES
	$fila+=3;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A$fila", 'OBSERVACION DEL DOCUMENTO');
	$objPHPExcel->getActiveSheet()->getStyle("A$fila")->applyFromArray($styleTitle);
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells("A$fila:E$fila");

	$fila++;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A$fila", $observacion);
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells("A$fila:E$fila");

	$objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
	$objWriter->save('php://output');
	exit;

?>