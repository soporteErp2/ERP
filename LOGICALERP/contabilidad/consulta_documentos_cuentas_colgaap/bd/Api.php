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

    $object = new Api_colgapp_documents($mysql);

    switch ($method) {
        case 'GET':
            $object->get_documents();
            break;
        
        default:
            echo json_encode(["warning"=>"method not found"]);
            break;
    }

    class Api_colgapp_documents
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
            $search = isset($_GET["q"]) ? $_GET["q"] : "";
            // Calcular el offset
            $offset = ($page - 1) * $limit;
            $where = ($_GET['tipo_documento'])? " AND tipo_documento='$_GET[tipo_documento]' " : "";
            $where .= ($_GET['id_sucursal']!='global')? " AND id_sucursal='$_GET[id_sucursal]' " : "";

            // Agregar cláusula WHERE si se proporciona un parámetro de búsqueda
            if (!empty($search)) {
                $where .= " AND (
                                    fecha LIKE '%$search%' OR 
                                    tipo_documento LIKE '%$search%' OR 
                                    consecutivo_documento LIKE '%$search%' OR 
                                    tipo_documento_extendido LIKE '%$search%' OR 
                                    nit_tercero LIKE '%$search%' OR 
                                    tercero LIKE '%$search%' 
                                )";
            }

            $tabla_asientos =($_GET['contabilidad']=='niif')? "asientos_niif" : "asientos_colgaap";

            // Consulta SQL para obtener registros con paginación y límite
            $sql = "SELECT 
                            id,
                            id_documento,
                            fecha,
                            tipo_documento,
                            consecutivo_documento,
                            tipo_documento_extendido,
                            id_tercero,
                            nit_tercero,
                            tercero,
                            id_sucursal,
                            sucursal
                        FROM $tabla_asientos 
                        WHERE
                        activo = 1 AND id_empresa='$_GET[id_empresa]'
                        AND fecha BETWEEN '$_GET[fecha_inicial]' AND '$_GET[fecha_final]' 
                        $where
                        GROUP BY id_documento, tipo_documento
                        LIMIT $limit OFFSET $offset ";
            $query = $this->mysql->query($sql);
            $ret_val = [];
			while ($row=$this->mysql->fetch_assoc($query)) {
               $ret_val[] = $row;
            }

            $ret_val = json_encode($ret_val);
            echo $ret_val;
        }

    }

?>