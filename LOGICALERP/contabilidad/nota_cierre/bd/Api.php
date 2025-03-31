<?php
	// error_reporting(E_ERROR | E_WARNING | E_PARSE);
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: *");
	header("Access-Control-Allow-Headers: *");
	
    include("../../../../configuracion/conectar.php");
	include('../../../../configuracion/define_variables.php');

	$method = $_SERVER['REQUEST_METHOD'];
	$json   = file_get_contents('php://input');
	$params   = json_decode($json);

    $object = new Nota_Cierre_Api($mysql);

    switch ($method) {
        case 'GET':
            $object->get_documents();
            break;
        
        default:
            echo json_encode(["warning"=>"method not found"]);
            break;
    }

    class Nota_Cierre_Api
    {
        private $mysql = null;
        public function __construct($mysql) {
            $this->mysql = $mysql;
        }

        // listar los items y si estan configurados para la seccion dada
        public function get_documents(){
            // Parámetros de solicitud
            $limit = isset($_GET["limit"]) ? intval($_GET["limit"]) : 100;
            $page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
            $search = isset($_GET["q"]) ? utf8_decode($_GET["q"]) : "";
            // Calcular el offset
            $offset = ($page - 1) * $limit;
            // $where = ($_GET['tipo_documento'])? " AND ac.tipo_documento='$_GET[tipo_documento]' " : "";
            // $where .= ($_GET['id_sucursal']!='global')? " AND ac.id_sucursal='$_GET[id_sucursal]' " : "";

            // Agregar cláusula WHERE si se proporciona un parámetro de búsqueda
            $where = "";
            if (!empty($search)) {
                $where .= " AND (
                                    cuenta_puc LIKE '%$search%' OR 
                                    nit_tercero LIKE '%$search%' OR 
                                    tercero LIKE '%$search%' OR 
                                    descripcion_puc LIKE '%$search%' 
                                )";
            }

            // $tabla_asientos =($_GET['contabilidad']=='niif')? "asientos_niif" : "asientos_colgaap";

            // Consulta SQL para obtener registros con paginación y límite
            $sql = "SELECT
                        id,
                        id_puc,
                        cuenta_puc,
                        descripcion_puc,
                        debe,
                        haber,
                        id_tercero,
                        tercero,
                        tipo_documento_cruce,
                        id_documento_cruce,
                        prefijo_documento_cruce,
                        numero_documento_cruce
                    FROM
                        nota_cierre_cuentas
                    WHERE
                        id_nota_general = '$_GET[id_nota]'
                        $where
                    ORDER BY
                        cuenta_puc ASC
                    LIMIT $limit OFFSET $offset";
            $query = $this->mysql->query($sql);
            $ret_val = [];
			while ($row=$this->mysql->fetch_assoc($query)) {
               $ret_val[] = [
                "cuenta_puc"      => utf8_encode($row["cuenta_puc"]),
                "descripcion_puc" => utf8_encode($row["descripcion_puc"]),
                "tercero"         => utf8_encode($row["tercero"]),
                "debe"            => number_format($row['debe'],$_SESSION['DECIMALESMONEDA']),
                "haber"            => number_format($row['haber'],$_SESSION['DECIMALESMONEDA']),
               ];
            }

            $ret_val = json_encode($ret_val);
            echo $ret_val;
        }

    }

?>