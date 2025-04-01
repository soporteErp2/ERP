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

        $this->mysqli->set_charset("utf8mb4");
        return $this->mysqli;
    }

    public function change_connection($database)
    {
        $this->mysqli->close();

        global $server;
        $this->mysqli = new mysqli($server->server_name, $server->user, $server->password, $database);
        if ($this->mysqli->connect_error) {
            return ["error conectando al servidor: " . $this->mysqli->connect_error];
        }

        $this->mysqli->set_charset("utf8mb4");
        return $this->mysqli;
    }

    public function get_company_id($licence_id,$company_doc){
        $this->change_connection("erp_".$licence_id);
        $sql = "SELECT id FROM empresas WHERE documento='$company_doc' ";
        $query = $this->mysqli->query($sql);
        $id = ($fila = $query->fetch_row()) ? $fila[0] : null;
        return $id;
    }

    public function get_branch($id_empresa,$licence_id){
        $this->change_connection("erp_".$licence_id);
        $sql = "SELECT id FROM empresas_sucursales WHERE id_empresa='$id_empresa' ";
        $query = $this->mysqli->query($sql);
        $id = ($fila = $query->fetch_row()) ? $fila[0] : null;
        return $id;
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

        $dbName = "erp_$data[licence]"; // Nombre de la base de datos
        $sql = "CREATE DATABASE  $dbName DEFAULT CHARACTER SET utf8mb4 DEFAULT COLLATE utf8mb4_general_ci";
        
        if ($this->mysqli->query($sql)) {
            $this->mysqli->select_db($dbName);

            // Ruta del archivo SQL (tres directorios atrás)
            $sqlFilePath = realpath('../../../../configuracion/structure.sql');

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

                $installation_path = realpath('../../../../configuracion/installation.sql');
                if ($installation_path && file_exists($installation_path)) {
                    $installation_sql = file_get_contents($installation_path);
                    
                    
                    // Ejecutar las consultas del archivo
                    if ($this->mysqli->multi_query($installation_sql)) {
                        do {
                            // Vaciar el buffer de resultados
                            if ($result = $this->mysqli->store_result()) {
                                $result->free();
                            }
                        } while ($this->mysqli->next_result());
    
                        // echo json_encode(["message" => "Base de datos creada y estructura ejecutada correctamente"]);
                    } else {
                        echo json_encode(["error" => "Error ejecutando el archivo SQL: " . $this->mysqli->error]);
                    }
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

    public function create_company($data)
    {
        $this->change_connection("erp_".$data["licence"]);

        $sql = "INSERT INTO empresas (nombre,tipo_documento_nombre,id_pais,pais,id_departamento,id_ciudad, razon_social,tipo_regimen,actividad_economica,direccion,documento,digito_verificacion,telefono,celular,zona_horaria,id_moneda,formato_hora,interface,grupo_empresarial)
			 	VALUES ('$data[company_name]','','','','','','$data[company_rs]','','','','$data[company]',$data[company_dv],'','','','','','','')";
        $query = $this->mysqli->query($sql);

        if (!$query) {
            echo json_encode("error al insertar la empresa");
            return;
        }

        $id_empresa = $this->get_company_id($data['licence'],$data['company_doc']);
        $sql = "INSERT INTO `empresas_sucursales` ( `id_empresa`, `nombre` ) VALUES ('$id_empresa', 'SUCURSAL PRINCIPAL')";
        $query = $this->mysqli->query($sql);
        

        $this->change_connection("erp_acceso");
        $sql = "INSERT INTO host (id,`nit`, `nombre`, `servidor`, `bd`, `id_plan`, `fecha_creacion`, `hora_creacion`, `fecha_vencimiento_plan`, `timezone`, `almacenamiento`, `activo`, `usuario_nombre1`, `usuario_nombre2`, `usuario_apellido1`, `usuario_apellido2`) 
                VALUES 
                ($data[licence],'$data[company]', '$data[company_name]', 'localhost', 'erp_$data[licence]', '1', '".date("Y-m-d")."', '".date("H:i:s")."', '2100-04-02', 'America/Bogota', '50', '1', NULL, NULL, NULL, NULL);";
        $query = $this->mysqli->query($sql);

        if (!$query) {
            echo json_encode("error al insertar la empresa en la tabla host");
            return;
        }
        echo json_encode("compañia creada!");
    }

    public function create_user($data){
        $id_empresa = $this->get_company_id($data['licence'],$data['company_doc']);
        $id_sucursal = $this->get_branch($id_empresa,$data['licence']);
        $password=md5("12345678");
        $sql ="INSERT INTO `empleados` 
                ( `tipo_documento`, `tipo_documento_nombre`, `documento`, `nombre1`, `nombre2`, `apellido1`, `apellido2`, `nombre`, `id_empresa`, `empresa`, `id_sucursal`, `sucursal`, `id_unidad_negocio`, `unidad_negocio`, `id_pais`, `pais`, `id_departamento`, `departamento`, `id_ciudad`, `ciudad`, `id_rol`, `rol`, `id_cargo`, `cargo`, `username`, `password`, `email_empresa`, `nacimiento`, `direccion`, `email_personal`, `telefono1`, `telefono2`, `celular1`, `id_celular_empresa`, `celular_empresa`, `foto`, `id_contrato`, `contrato`, `salario_base`, `salario`, `ad_contrato`, `ad_certificado_judicial`, `ad_cedula`, `ad_certificado_estudios`, `ad_hoja_vida`, `ad_afiliaciones`, `alerta_actualizacion`, `activo`, `ciudad_cedula`, `eps`, `arp`, `tecnico_operativo`, `conductor`, `vendedor`, `qrcode`, `color_menu`, `color_fondo`, `change_update`, `sinc_tercero`, `id_tercero`)
                VALUES ('1', 'C.C', '$data[user_doc]', '$data[user_firstname]', '$data[user_secondname]', '$data[user_firstlastname]',' $data[user_secondlastename]', NULL, '$id_empresa', '', '$id_sucursal', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 'Administrador', '0', NULL, 'data[user_email]', '$password', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '00', '0.00', NULL, NULL, NULL, NULL, NULL, NULL, 'false', '1', NULL, NULL, NULL, 'false', 'false', 'false', NULL, '0,0,0', '32,124,229', NULL, 'false', NULL);";
        $query = $this->mysqli->query($sql);
        if (!$query) {
            echo json_encode("error al insertar el usuario a la empresa");
            return;
        }
        echo json_encode("usuario insertado");
    }

}
