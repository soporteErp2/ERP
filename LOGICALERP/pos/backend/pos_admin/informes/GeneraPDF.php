<?php
include('../../../../../configuracion/define_variables.php');

header('Content-Type: application/json'); // Asegura que no haya salida previa si se usa JSON

if (isset($op) && $op == "guardaFile") {
    $file = "temp/" . $nombre;
    
    if (empty($html)) {
        echo json_encode(["status" => "error", "message" => "El contenido del archivo está vacío."]);
        exit;
    }
    
    if (file_put_contents($file, $html) === false) {
        echo json_encode(["status" => "error", "message" => "No se pudo escribir en el archivo."]);
        exit;
    }
    
    echo json_encode(["status" => "success", "message" => "Archivo guardado correctamente.", "file" => $file]);
    exit;
}

// GENERAR EL PDF
set_time_limit(240);
ini_set("memory_limit", "500M");

$file = "temp/" . $nombre;
if (!file_exists($file)) {
    echo json_encode(["status" => "error", "message" => "El archivo no existe."]);
    exit;
}

$contents = file_get_contents($file);
if ($contents === false) {
    echo json_encode(["status" => "error", "message" => "No se pudo leer el archivo."]);
    exit;
}

$params = base64_decode($params);
$options = json_decode($params, true);
$texto = base64_decode($contents);

$orientacion = ($options["orientacion"] == "V") ? "P" : "L";
$TAMANO_ENCA = isset($TAMANO_ENCA) ? $TAMANO_ENCA : 12;

if ($options["debug"] == "false") {
    include("../../misc/MPDF54/mpdf.php");
    
    ob_start(); // Previene salida inesperada
    
    try {
        $mpdf = new mPDF(
            "utf-8", 
            'A4', 
            12, 
            "", 
            $options["margins"]["left"], 
            $options["margins"]["right"], 
            $options["margins"]["top"], 
            $options["margins"]["bottom"], 
            3, 
            10, 
            $orientacion
        );
        
        $mpdf->SetAutoPageBreak(TRUE, 15);
        $mpdf->SetTitle("GENERADOR DE INFORMES LOGICALSOFT");
        $mpdf->SetAuthor("LOGICALSOFT");
        $mpdf->SetDisplayMode("fullpage");
        $mpdf->SetHeader("");
        
        $mpdf->WriteHTML(utf8_encode($texto));
        
        ob_end_clean(); // Limpia el buffer antes de salida
        
        if ($options["op"] == "view") {
            $mpdf->Output($nombre . ".pdf", "I");
        } elseif ($options["op"] == "download") {
            $mpdf->Output($nombre . ".pdf", "D");
        }
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Error al generar el PDF: " . $e->getMessage()]);
    }
    exit;
} else {
    echo $texto;
}
