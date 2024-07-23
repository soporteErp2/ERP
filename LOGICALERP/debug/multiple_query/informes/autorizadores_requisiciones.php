<?php
require_once('../model.php');
include('../../../../misc/excel/Classes/PHPExcel.php');

function cleanData($data) {
    // Eliminar caracteres no imprimibles y codificar en UTF-8
    return utf8_encode($data);
}

$synchronize = new Synchronize();
$dataBasesArray = $synchronize->getDataBases();

// Crear el objeto de Excel
$objPHPExcel = new PHPExcel();
header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment;filename="Informe_de_autorizadores.xls"');
header('Cache-Control: max-age=0');

// Obtener la información de cada empresa
$sheetIndex = 0;

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

    // Crear una nueva hoja en el libro de Excel
    $objPHPExcel->createSheet($sheetIndex);
    $objPHPExcel->setActiveSheetIndex($sheetIndex);
    $title = (strlen(mysql_result($datosQuery, 0, 'nombres_concatenados'))>=28)? 
            substr(mysql_result($datosQuery, 0, 'nombres_concatenados'),0,27)."..." :
            mysql_result($datosQuery, 0, 'nombres_concatenados');
    $objPHPExcel->getActiveSheet()->setTitle(cleanData($title));

    // Escribir encabezados
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'ID_USUARIO');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'ID_EMPRESA');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', 'DOCUMENTO');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', 'NOMBRE');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', 'AREA');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', 'NIVEL');

    $consultaPermisos = "SELECT
                            CAR.id_empleado,
                            CD.id_empresa,
                            CAR.documento_empleado,
                            CAR.nombre_empleado,
                            CD.nombre AS nombre_area,
                            CAR.orden 
                         FROM
                            costo_departamentos AS CD
                            INNER JOIN costo_autorizadores_requisicion AS CAR ON CD.id = CAR.id_area 
                         WHERE
                            CAR.activo = 1 
                            AND CD.activo = 1";

    $consul = mysql_query($consultaPermisos, $newLink);

    if (!$consul) {
        echo "Error en la consulta: " . mysql_error($newLink);
        continue;
    }

    $rowNumber = 2; // Empezar desde la fila 2
    while ($row = mysql_fetch_array($consul)) {
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $rowNumber, cleanData($row['id_empleado']));
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $rowNumber, cleanData($row['id_empresa']));
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $rowNumber, cleanData($row['documento_empleado']));
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $rowNumber, cleanData($row['nombre_empleado']));
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $rowNumber, cleanData($row['nombre_area']));
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $rowNumber, cleanData($row['orden']));
        $rowNumber++;
    }

    // Incrementar el índice de la hoja
    $sheetIndex++;
}

// Establecer la primera hoja como activa
$objPHPExcel->setActiveSheetIndex(0);

// Guardar el archivo de Excel
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
?>