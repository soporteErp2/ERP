<?php
include_once '../../../../configuracion/conexion.php';

class Api_Controller
{
    private $mysqli = null;
    
    public function __construct() 
    {
        $this->connect();
    }

    public function connect()
    {
        global $server;
        $this->mysqli = new mysqli($server->server_name, $server->user, $server->password, $server->database);
        if ($this->mysqli->connect_error) {
            return ["error conectando al servidor: " . $this->mysqli->connect_error];
        }

        return $this->mysqli;
    }

    public function verify_db($data)
    {
        $sql = "SELECT id FROM host WHERE nit = '$data[company]'";
        $result = $this->mysqli->query($sql);
        // $rows = $result->fetch_all(MYSQLI_ASSOC);
        if ($result->num_rows>0) 
        {
            $this->structure_db();
        }
        else
        {
            $this->create_db($data);
        }

        // echo $result->num_rows;
    }

    public function create_db($data)
    {
        
        // echo json_encode([1]);
        // return;

        $dbName = "erp_$data[licence]"; // Nombre de la base de datos
        $sql = "CREATE DATABASE  $dbName DEFAULT CHARACTER SET utf8mb4 DEFAULT COLLATE utf8mb4_general_ci";
        
        if ($this->mysqli->query($sql)) {
            $this->mysqli->select_db($dbName);

            // Ruta del archivo SQL (tres directorios atrás)
            $sqlFilePath = realpath('../../../../structure.sql');

            if ($sqlFilePath && file_exists($sqlFilePath)) {
                $sqlContent = file_get_contents($sqlFilePath);
                
                // Ejecutar las consultas del archivo
                if ($this->mysqli->multi_query($sqlContent)) {
                    do {
                        // Vaciar el buffer de resultados
                        if ($result = $this->mysqli->store_result()) {
                            $result->free();
                        }
                    } while ($this->mysqli->next_result());

                    echo json_encode(["message" => "Base de datos creada y estructura ejecutada correctamente"]);
                } else {
                    echo json_encode(["error" => "Error ejecutando el archivo SQL: " . $this->mysqli->error]);
                }
            } else {
                echo json_encode(["error" => "Archivo SQL no encontrado en: " . $sqlFilePath]);
            }
        } else {
            echo json_encode(["error" => "Error creando la base de datos o la base de datos ya existe: " . $this->mysqli->error]);
        }
    }

    function structure_db(){
        echo json_encode("db exist, structure");
    }
}
