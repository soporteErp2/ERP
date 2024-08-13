<?php
require_once('../model.php');
include('../../../../misc/excel/Classes/PHPExcel.php');


$synchronize = new Synchronize();
$dataBasesArray = $synchronize->getDataBases();

// Crear el objeto de Excel
$objPHPExcel = new PHPExcel();
header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment;filename="Informe_de_compra_activo_fijos.xls"');
header('Cache-Control: max-age=0');

// Obtener la información de cada empresa
$sheetIndex = 0;
    // Escribir encabezados
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'EMPRESA');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'SUCURSAL');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', 'NIT PROVEEDOR');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', 'PROVEEDOR');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', 'NOMBRE EQUIPO');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', 'CANTIDAD');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', 'COSTO');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', '% IMPUESTO');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', 'VALOR IMPUESTO');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', 'TOTAL');
    $objPHPExcel->getActiveSheet()->setCellValue('K1', 'CONSECUTIVO ERP');
    $objPHPExcel->getActiveSheet()->setCellValue('L1', 'NUMERO FACTURA');

foreach ($dataBasesArray as $dataBase) {
    $newLink = $synchronize->getConn()->conectarse($dataBase['bd']);
    $id_empresa = ($dataBase['bd'] == 'erp') ? 47 : 2;
    $query = "SELECT GROUP_CONCAT(nombre SEPARATOR '-') as nombres_concatenados FROM empresas WHERE activo = 1 AND documento <> 9999";
    $datosQuery = mysql_query($query, $newLink);
    
    if (!$datosQuery) {
        echo "Error en la consulta: " . mysqli_error($newLink);
        continue;
    }
    if (mysql_num_rows($datosQuery)==0) {
        echo "No se encontraron resultados para la consulta en la base de datos: " . $dataBase['bd'];
        continue;
    }

    //Nombre Empresa
    $nombreEmpresa = mysql_result($datosQuery, 0, 'nombres_concatenados');

    $consultaItems = "SELECT
                        	CF.sucursal,
                        	CF.nit,
                        	CF.proveedor,
                        	CFI.nombre,
                        	CFI.cantidad,
                        	CFI.costo_unitario,
                        	CFI.valor_impuesto,
                        	CF.consecutivo,
                        	CF.numero_factura 
                        FROM
                        	`compras_facturas_inventario` CFI
                        	INNER JOIN compras_facturas AS CF ON CF.id = CFI.id_factura_compra 
                        WHERE
                        	codigo LIKE '00%'
                        	AND CF.activo = 1 
                        	AND CFI.activo = 1 
                        	AND CF.fecha_inicio BETWEEN '2021-12-31' 
                        	AND '2024-12-31'";

    $consul = mysql_query($consultaItems, $newLink);

    if (!$consul) {
        echo "Error en la consulta: " . mysql_error($newLink);
        continue;
    }

    $rowNumber = 2; // Empezar desde la fila 2
    while ($row = mysql_fetch_array($consul)) {
        $valorImpuesto = $row['cantidad']*$row['costo_unitario']*$row['valor_impuesto']*0.01;
        $total = $valorImpuesto + $row['cantidad']*$row['costo_unitario'];
        
        $objPHPExcel->getActiveSheet()->setCellValue('a' . $rowNumber, utf8_encode($nombreEmpresa));
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $rowNumber, utf8_encode($row['sucursal']));
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $rowNumber, utf8_encode($row['nit']));
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $rowNumber, utf8_encode($row['proveedor']));
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $rowNumber, utf8_encode($row['nombre']));
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $rowNumber, utf8_encode($row['cantidad']));
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $rowNumber, utf8_encode($row['costo_unitario']));
        $objPHPExcel->getActiveSheet()->setCellValue('H' . $rowNumber, utf8_encode($row['valor_impuesto']));
        $objPHPExcel->getActiveSheet()->setCellValue('I' . $rowNumber, utf8_encode($valorImpuesto));
        $objPHPExcel->getActiveSheet()->setCellValue('J' . $rowNumber, utf8_encode($total));
        $objPHPExcel->getActiveSheet()->setCellValue('K' . $rowNumber, utf8_encode($row['consecutivo']));
        $objPHPExcel->getActiveSheet()->setCellValue('L' . $rowNumber, utf8_encode($row['numero_factura']));

        $rowNumber++;
    }

}

// Establecer la primera hoja como activa
$objPHPExcel->setActiveSheetIndex(0);

// Guardar el archivo de Excel
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
?>