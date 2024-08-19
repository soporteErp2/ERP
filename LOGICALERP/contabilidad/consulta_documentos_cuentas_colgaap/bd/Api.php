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

    switch ($params->option) {
        case 'get_documents':
            $object->get_documents($params);
            break;
        
        default:
            echo json_encode(["warning"=>"no se detecto la opcion"]);
            break;
    }

    class Api_colgapp_documents
    {
        private $mysql = null;
        public function __construct($mysql) {
            $this->mysql = $mysql;
        }

        // listar los items y si estan configurados para la seccion dada
        public function get_documents($params){
            // Parámetros de solicitud
            $limit = isset($params->limit) ? intval($params->limit) : 100;
            $page = isset($params->page) ? intval($params->page) : 1;
            $search = isset($params->q) ? $params->q : "";
            // Calcular el offset
            $offset = ($page - 1) * $limit;
            $where = "";
            // Agregar cláusula WHERE si se proporciona un parámetro de búsqueda
            if (!empty($search)) {
                $where .= " AND (
                                    items.codigo LIKE '%$search%' OR 
                                    items.nombre_equipo LIKE '%$search%' OR 
                                    items.familia LIKE '%$search%' OR 
                                    items.grupo LIKE '%$search%' OR
                                    items.subgrupo LIKE '%$search%'  
                                )";
            }
            // seccion_items
            // Consulta SQL para obtener registros con paginación y límite
           $sql = "SELECT
                        items.id,
                        items.codigo,
                        items.nombre_equipo AS item,
                        items.familia,
                        items.grupo,
                        items.subgrupo,
                        CASE
                            WHEN seccion_items.id_seccion = $params->id_seccion THEN seccion_items.id_seccion
                            ELSE NULL
                        END AS id_seccion
                    FROM items 
                    LEFT JOIN seccion_items ON seccion_items.id_item = items.id 
                    AND (seccion_items.id_seccion = $params->id_seccion OR seccion_items.id_seccion IS NULL)
                    WHERE activo=1 AND id_empresa=$params->id_empresa
                    $where
                    AND modulo_pos = 'true'
                    ORDER BY nombre_equipo ASC
                    LIMIT $limit OFFSET $offset";
            $query = $this->mysql->query($sql);
            $ret_val = [];
			while ($row=$this->mysql->fetch_array($query)) {
               $ret_val[] = [
                    "id"         => $row["id"],
                    "codigo"     => $row["codigo"],
                    "nombre"     => utf8_encode($row["item"]),
                    "familia"    => utf8_encode($row["familia"]),
                    "grupo"      => utf8_encode($row["grupo"]),
                    "subgrupo"   => utf8_encode($row["subgrupo"]),
                    "id_seccion" => $row["id_seccion"],
                ];
            }

            $ret_val = json_encode($ret_val);
            echo $ret_val;
        }

    }

?>