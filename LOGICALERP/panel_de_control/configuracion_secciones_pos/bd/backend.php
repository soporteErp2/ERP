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

    $object = new Sections_Settings($mysql);

    switch ($params->option) {
        case 'get_items':
            $object->get_items($params);
            break;
        case 'set_item':
            $object->set_item($params);
            break;
        
        default:
            echo json_encode(["warning"=>"no se detecto la opcion"]);
            break;
    }

    class Sections_Settings
    {
        private $mysql = null;
        public function __construct($mysql) {
            $this->mysql = $mysql;
        }

        // listar los items y si estan configurados para la seccion dada
        public function get_items($params){
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
                        seccion_items.id_seccion
                    FROM items 
                    LEFT JOIN seccion_items ON seccion_items.id_item = items.id
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

        public function set_item($params){
            // id_empresa
            // id_item
            // id_seccion
            if ($params->set) {
                $sql = "INSERT INTO seccion_items (id_item,id_seccion) VALUES ($params->id_item,$params->id_seccion) ";
            }
            else{
                $sql = "DELETE FROM seccion_items WHERE id_item=$params->id_item AND id_seccion=$params->id_seccion ";
            }
            $query = $this->mysql->query($sql);
            if ($query) {
                echo json_encode(["success"=>true]);
            }
            else{
                echo json_encode(["success"=>false]);
            }

        }

    }

?>