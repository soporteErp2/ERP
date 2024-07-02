<?php
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

            // $branches = $result->fetch_array();
            return $branches;
            // var_dump($branches);
        } else {
            return [];
        }
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
    
    default:
        echo "Método no válido";
        break;
}
?>
