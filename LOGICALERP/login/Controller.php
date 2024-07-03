<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
$DIRECTORIO = explode("/", $_SERVER['REQUEST_URI']);

include_once($_SERVER['DOCUMENT_ROOT'].'/'.$DIRECTORIO[1].'/configuracion/conexion.php');

class Login 
{
    private $mysqli;
    private $session_mysqli;

    public function __construct() {
        $this->global_connect();
    }

    private function global_connect(){
        global $server;

        $this->mysqli = new mysqli($server->server_name, $server->user, $server->password, "erp_acceso");
        if ($this->mysqli->connect_errno) {
            echo "Fallo al conectar a MySQL: (" . $this->mysqli->connect_errno . ") " . $this->mysqli->connect_error;
        }

        if (!$this->mysqli->set_charset("utf8")) {
            echo "Error cargando el conjunto de caracteres utf8: " . $this->mysqli->error;
        }
    }

    public function session_connect($data_base)  {
        global $server;
        $this->session_mysqli = new mysqli($server->server_name, $server->user, $server->password, $data_base);
        if ($this->session_mysqli->connect_errno) {
            echo "Fallo al conectar a MySQL en sesion: (" . $this->session_mysqli->connect_errno . ") " . $this->session_mysqli->connect_error;
        }

        if (!$this->session_mysqli->set_charset("utf8")) {
            echo "Error cargando el conjunto de caracteres utf8: " . $this->session_mysqli->error;
        }
    }

    public function get_companies(){
        $sql = "SELECT id, nit, nombre, bd, fecha_vencimiento_plan FROM host WHERE activo=1";
        $result = $this->mysqli->query($sql);

        if ($result->num_rows > 0) {
            $companies = [];
            while ($row = $result->fetch_assoc()) {
                $companies[] = $row;
            }
            return $companies;
        } else {
            return [];
        }
    }

    public function load_company($n_documento){
        $sql = "SELECT id,bd FROM host WHERE nit=$n_documento";
        $result = $this->mysqli->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $this->session_connect($row['bd']);
            $sql = "SELECT id,nombre FROM empresas WHERE documento=$n_documento";
            $result = $this->session_mysqli->query($sql);
            $company = $result->fetch_assoc();
            $sql = "SELECT id,nombre FROM empresas_sucursales WHERE id_empresa=$company[id] AND activo=1";
            $result = $this->session_mysqli->query($sql);
            $branches = [];
            while ($row = $result->fetch_assoc()) {
                $branches[] = $row;
            }
            return $branches;
        } else {
            return [];
        }
    }
    /**
     * @method [login] metodo para iniciar sesion, con validacion contra inyeccion sql
     * @param [$n_documento]  numero de documento o nit de la empresa a iniciar sesion
     * @param [$sucursal] id de la sucursal a donde se iniciara sesion
     * @param [$usuario] user name de quien inicia sesion
     * @param [$password] contraseña del usuario
     * @return array resultado del inicio de sesion ["error" => "detalle"] o  ["success"=>true]
     */
    public function login($n_documento, $sucursal, $usuario, $password) {
        // Consultar la empresa
        $sql = "SELECT id, bd FROM host WHERE nit = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("s", $n_documento);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $this->session_connect($row['bd']);
            
            // Consultar el empleado
            $sql = "SELECT id, nombre, id_rol, id_sucursal FROM empleados WHERE activo = 1 AND username = ? AND password = ?";
            $stmt = $this->session_mysqli->prepare($sql);
            $hashed_password = md5($password);
            $stmt->bind_param("ss", $usuario, $hashed_password);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $employee_data = $result->fetch_assoc();
                
                // Verificar permiso multisucursal empleados_permisos
                $sql = "SELECT id FROM empleados_roles_permisos WHERE id_rol = ? AND id_permiso = 1";
                $stmt = $this->session_mysqli->prepare($sql);
                $stmt->bind_param("i", $employee_data['id_rol']);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    return $this->start_session();
                } else {
                    if ($employee_data["id_sucursal"] == $sucursal) {
                        return $this->start_session();
                    } else {
                        return ["error" => "No tiene permisos para ingresar a esta sucursal"];
                    }
                }
            } else {
                return ["error" => "Datos de usuario incorrectos"];
            }
        } else {
            return ["error" => "No existe la empresa"];
        }
    }

    public function start_session(){
        return ["success" => true];
    }

}
header('Content-Type: application/json; charset=utf-8');
$login = new Login();

switch ($_GET['method']) {
    case 'get_companies':
        $companies = $login->get_companies();
        echo json_encode($companies);
        break;
    case 'load_company':
        $is_valid = $login->load_company($_GET['n_documento']);
        echo json_encode($is_valid);
        break;
    case 'login':
        $is_valid = $login->login($_GET['n_documento'],$_GET['sucursal'],$_GET['usuario'],$_GET['password']);
        echo json_encode($is_valid);
        break;
    
    default:
        echo "Método no válido";
        break;
}
?>
