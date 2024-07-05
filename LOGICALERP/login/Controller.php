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

    public function get_host($nit_code){
        $sql = "SELECT
                    id,
                    nit,
                    nombre,
                    servidor,
                    bd,
                    id_plan,
                    fecha_creacion,
                    hora_creacion,
                    fecha_vencimiento_plan,
                    timezone,
                    almacenamiento,
                    activo,
                    usuario_nombre1,
                    usuario_nombre2,
                    usuario_apellido1,
                    usuario_apellido2
                FROM host WHERE nit=$nit_code";
        $result = $this->mysqli->query($sql);
        $host = $result->fetch_assoc();
        return $host;
        
    }

    public function get_subscription($subscription_id){
        $sql = "SELECT id,plan,usuarios,sucursales FROM planes WHERE activo=1 AND id=$subscription_id";
        $result = $this->session_mysqli->query($sql);
        $subscription = $result->fetch_assoc();
        return $subscription;
    }

    public function get_company($nit_code){
        $sql = "SELECT 
                    nombre,
                    id,
                    id_pais,
                    pais,
                    id_moneda,
                    documento,
                    nit_completo,
                    simbolo_moneda,
                    decimales_moneda,
                    grupo_empresarial,
                    descripcion_moneda
                FROM empresas WHERE documento=$nit_code";
        $result = $this->session_mysqli->query($sql);
        $company = $result->fetch_assoc();
        return $company;
    }

    public function get_branch($branch_id){
        $sql = "SELECT id, nombre  FROM empresas_sucursales WHERE id=$branch_id";
        $result = $this->session_mysqli->query($sql);
        $branch = $result->fetch_assoc();
        return $branch;
    }

    public function get_support_licence($company_id){
        $sql = "SELECT id, id_unico,autorizado  FROM licencia_soporte WHERE id=$company_id";
        $result = $this->session_mysqli->query($sql);
        $licence = $result->fetch_assoc();
        return $licence;
        
    }

    public function get_permissions($rol_id){
        $sql = "SELECT id_permiso FROM empleados_roles_permisos WHERE id_rol =  $rol_id";
        $result = $this->session_mysqli->query($sql);
        while ($row = $result->fetch_assoc()) {
            $permissions[] = $row["id_permiso"];
        }
        return $permissions;
    }

    public function load_company($n_documento){
        $host = self::get_host($n_documento);
        if (count($host) > 0) {
            $this->session_connect($host['bd']);
            $company = self::get_company($n_documento);
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
            $sql = "SELECT id, documento, nombre, id_rol, id_sucursal, username,email_empresa,id_empresa FROM empleados WHERE activo = 1 AND username = ? AND password = ?";
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
                    return $this->start_session($employee_data,$n_documento,$sucursal);
                } else {
                    if ($employee_data["id_sucursal"] == $sucursal) {
                        return $this->start_session($employee_data,$n_documento,$sucursal);
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

    public function start_session($employee_data,$nit_code,$branch_id){
        session_start();
        $host = self::get_host($nit_code);
        $company = self::get_company($nit_code);
        $branch = self::get_branch($branch_id);
        $licence = self::get_support_licence($company["id"]);
        $subscription = self::get_subscription($host["id_plan"]);
        $permissions = self:: get_permissions( $employee_data["id_rol"]);
        
        $sql = "SELECT valor FROM empleados_roles WHERE id =  $employee_data[id_rol]";
        $result = $this->session_mysqli->query($sql);
        $rol = $result->fetch_assoc();

        $_SESSION["ROL"]                    = $employee_data['id_rol'];
        $_SESSION["ROLVALOR"]               = $rol["valor"];
        $_SESSION["PERMISOS"]               = $permissions;
        $_SESSION["COLOR_VENTANA"]          = '#157FCC';
        $_SESSION["COLOR_CONTRASTE"]        = '#DFE8F6';
        $_SESSION["COLOR_FONDO"]            = '#CDDBF0';
        $_SESSION["COLOR_LINEA"]            = '#8DB2E3';
        $_SESSION["COLOR_FUENTE"]           = '#033999';
        $_SESSION['BD']                     = $host["bd"];
        $_SESSION['SERVIDOR']               = $host["servidor"];
        $_SESSION['ID_HOST']                = $host["id"];
        $_SESSION['TIMEZONE']               = $host["timezone"];
        $_SESSION['ALMACENAMIENTO']         = $host["almacenamiento"];
        $_SESSION['PLAN_FECHA_VENCIMIENTO'] = $host["fecha_vencimiento_plan"];
        $_SESSION['PLAN_USUARIOS']          = $subscription["usuarios"];
        $_SESSION['PLAN_SUCURSALES']        = $subscription["sucursales"];
        $_SESSION["IDUSUARIO"]              = $employee_data["id"];
        $_SESSION["CEDULAFUNCIONARIO"]      = $employee_data["documento"];
        $_SESSION["NOMBREFUNCIONARIO"]      = $employee_data["nombre"];
        $_SESSION["NOMBREUSUARIO"]          = $employee_data["username"];
        $_SESSION["EMAIL"]                  = $employee_data["email_empresa"];
        $_SESSION["SUCURSAL"]               = $branch["id"];
        $_SESSION["NOMBRESUCURSAL"]         = $branch["nombre"];
        $_SESSION["EMPRESA"]                = $company["id"];
        $_SESSION["NOMBREEMPRESA"]          = $company["nombre"];
        $_SESSION["NITEMPRESA"]             = $company["nit_completo"];
        $_SESSION["GRUPOEMPRESARIAL"]       = $company["grupo_empresarial"];
        $_SESSION["PAIS"]                   = $company["id_pais"];
        $_SESSION["MONEDA"]                 = $company["id_moneda"];
        $_SESSION["DESCRIMONEDA"]           = $company["descripcion_moneda"];
        $_SESSION["SIMBOLOMONEDA"]          = $company["simbolo_moneda"];
        $_SESSION["DECIMALESMONEDA"]        = $company["decimales_moneda"];
        $_SESSION["SUCURSALORIGEN"]         = $employee_data["id_sucursal"];
        $_SESSION["EMPRESAORIGEN"]          = $employee_data["id_empresa"];
        $_SESSION["CONEXIONSIIP3"]          = "";
        $_SESSION["APIGOOGLE"]              = "";
        $_SESSION["LICENCIASOPORTE"]        = $licence["id_unico"];
        $_SESSION["PRODUCTO"]               = 4;
        $SESSION['APP']                     = 'LogicalSoft-ERP';

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
