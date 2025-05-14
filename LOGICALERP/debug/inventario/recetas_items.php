<?php
//Informe de items con recetas ordenado por familias

include("../../../configuracion/conectar.php");
include("../../../configuracion/define_variables.php");
include('../../../misc/excel/Classes/PHPExcel.php');
// Crear el objeto de Excel
$objPHPExcel = new PHPExcel();
header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment;filename="items_recetas.xls"');
header('Cache-Control: max-age=0');

$sheetIndex = 0;

// Obtener la información de las familias 
$sqlGrupos = "SELECT id, nombre FROM items_familia_grupo WHERE activo = 1";
$resultSqlGrupos = mysql_query($sqlGrupos, $link);

$gruposItems = array();

if ($resultSqlGrupos) {
    while ($row = mysql_fetch_assoc($resultSqlGrupos)) {
        $gruposItems[] = $row;
    }
} else {
    // Manejo de error         
    echo "Error en la consulta de grupos: " . mysql_error($link);

}


foreach ($gruposItems as $grupo) {
    // Crear una nueva hoja en el libro de Excel
    $objPHPExcel->createSheet($sheetIndex);
    $objPHPExcel->setActiveSheetIndex($sheetIndex);
    $title = ($grupo['nombre']);
    $objPHPExcel->getActiveSheet()->setTitle(utf8_encode($title));

    // Escribir encabezados
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'CODIGO');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'NOMBRE');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', 'CODIGO ITEM RECETA');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', 'NOMBRE ITEM RECETA');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', 'CANTIDAD ITEM RECETA');

    $consultaRecetas = "SELECT
                            IR.codigo_item,
                            IR.nombre_item,
                            IR.codigo_item_materia_prima,
                            IR.nombre_item_materia_prima,
                            IR.cantida_item_materia_prima
                         FROM
                            items_recetas AS IR INNER JOIN items AS I ON I.id = IR.id_item AND I.id_grupo = ".$grupo['id']."
                         WHERE
                            I.activo = 1 
                            AND IR.activo = 1";

    $resultRecetas = mysql_query($consultaRecetas, $link);

    if (!$resultRecetas) {
        echo "Error en la consulta de recetas: " . mysql_error($link);
        continue;
    }

    $rowNumber = 2; // Empezar desde la fila 2
    while ($row = mysql_fetch_array($resultRecetas)) {
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $rowNumber, utf8_encode($row['codigo_item']));
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $rowNumber, utf8_encode($row['nombre_item']));
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $rowNumber, utf8_encode($row['codigo_item_materia_prima']));
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $rowNumber, utf8_encode($row['nombre_item_materia_prima']));
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $rowNumber, utf8_encode($row['cantida_item_materia_prima']));
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