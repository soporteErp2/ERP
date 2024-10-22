<?php
$baseDir = __DIR__;

// Construye la ruta absoluta al archivo de configuración
$configPath = realpath($baseDir . '/../../../../configuracion/conexion.php');

if ($configPath) {
    include_once($configPath);
} else {
    die('Error: No se pudo encontrar el archivo de configuración.');
}

// Establece la zona horaria a Bogotá
date_default_timezone_set('America/Bogota');

// validar que sea el ultimo dia del mes
// if (date('t') != date('d')) {
if (date('j') != 2) {
    exit("no es el ultimo dia del mes!\n");
}
    // guardar log para verificacion de ejecucion
    $logFile = $baseDir . '/task_log.txt'; 

    // Obtén la fecha y hora actual
    $date = new DateTime();
    $formattedDate = $date->format('Y-m-d H:i:s');

    // Construye el mensaje de log
    $logMessage = "Ejecutado el día " . $formattedDate . "\n";

    // Abre el archivo de log en modo de escritura (append)
    $fileHandle = fopen($logFile, 'a');

    if ($fileHandle) {
        // Escribe el mensaje de log en el archivo
        fwrite($fileHandle, $logMessage);
        // Cierra el archivo
        fclose($fileHandle);
        echo "Log guardado correctamente.\n";
    } else {
        echo "Error al abrir el archivo de log.\n";
    }

   

// id empresas a las que se les realiza la copia
$companies = [
    "20",
    "1090"
];

try {
    // Crear conexión
    $conn = new mysqli($server->server_name, $server->user, $server->password, $server->database);

    // Verificar la conexión
    if ($conn->connect_error) {
        throw new Exception("Conexión fallida: " . $conn->connect_error);
    }

    array_map(function($company_id ) use ($conn,$server){
        $sql = "SELECT servidor,bd FROM host WHERE id=$company_id";
        $query = $conn->query($sql);
        $result = $query->fetch_assoc();
        $servidor = $result['servidor'];
        $base_datos = $result['bd'];
        $company_link = new mysqli($servidor, $server->user, $server->password, $base_datos);

        $log_object = new Inventory_Log($company_id,$company_link);
        $log_object->set_log();

    },$companies);

    
} catch (Exception $e) {
    echo 'Error: ',  $e->getMessage(), "\n";
} 

class Inventory_Log
{
    public function __construct($id_empresa,$mysql) {
        $this->id_empresa = $id_empresa;
        $this->mysql = $mysql;
    }

    public function set_log(){
        $sql = "SELECT
                    id_item,
                    codigo,
                    nombre_equipo,
                    unidad_medida,
                    cantidad_unidades,
                    costos,
                    precio_venta,
                    id_empresa,
                    empresa,
                    id_sucursal,
                    sucursal,
                    id_ubicacion,
                    ubicacion,
                    id_familia,
                    familia,
                    id_grupo,
                    grupo,
                    id_subgrupo,
                    subgrupo,
                    id_impuesto,
                    cantidad,
                    inventariable,
                    estado_compra,
                    estado_venta
                FROM inventario_totales WHERE activo=1";
        $query = $this->mysql->query($sql);
        $sql_items = "";
        while($row = $query->fetch_assoc()) {
            $sql_items .= "(
                            '$row[id_item]',
                            '$row[codigo]',
                            '$row[nombre_equipo]',
                            '$row[unidad_medida]',
                            '$row[cantidad_unidades]',
                            '$row[costos]',
                            '$row[precio_venta]',
                            '$row[id_impuesto]',
                            '$row[id_empresa]',
                            '$row[empresa]',
                            '$row[id_sucursal]',
                            '$row[sucursal]',
                            '$row[id_ubicacion]',
                            '$row[ubicacion]',
                            '$row[id_familia]',
                            '$row[familia]',
                            '$row[id_grupo]',
                            '$row[grupo]',
                            '$row[id_subgrupo]',
                            '$row[subgrupo]',
                            '$row[cantidad]',
                            '$row[inventariable]',
                            '$row[estado_compra]',
                            '$row[estado_venta]',
                            '".date("Y-m-d")."',
                            '".date("H:i:s")."'
                            ),";
            // echo "codigo: " . $row["codigo"]. " - nombre_equipo: " . $row["nombre_equipo"]." - ubicacion $row[ubicacion] <br>";
        }   
        $sql_items = substr($sql_items, 0, -1);

        $this->mysql->query("SET SESSION wait_timeout = 28800");
        $this->mysql->query("SET SESSION interactive_timeout = 28800");
        $this->mysql->query("SET SESSION max_allowed_packet = 67108864"); // 64MB

        $sql = "INSERT INTO inventario_totales_log_mensual
                (
                    id_item,
                    codigo,
                    nombre,
                    unidad_medida,
                    cantidad_unidades,
                    costo,
                    precio_venta,
                    id_impuesto,
                    id_empresa,
                    empresa,
                    id_sucursal,
                    sucursal,
                    id_bodega,
                    bodega,
                    id_familia,
                    familia,
                    id_grupo,
                    grupo,
                    id_subgrupo,
                    subgrupo,
                    cantidad,
                    inventariable,
                    estado_compra,
                    estado_venta,
                    fecha,
                    hora
                ) VALUES $sql_items";
        $query = $this->mysql->query($sql);
        if (!$query) {
            throw new Exception("Error en la inserción SQL: " . $this->mysql->error);
        }
        // echo "log";
        // echo "<pre>$sql</pre>";
        // echo "<br><b>Inventory_Log set_log $this->id_empresa</b><br><br>";
    }
}