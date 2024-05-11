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

    if ($params->option=="get_items") {
        $object->get_items($params);
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
            $search = isset($params->search) ? $params->search : "";
            // Calcular el offset
            $offset = ($page - 1) * $limit;
            $where = "";
            // Agregar cláusula WHERE si se proporciona un parámetro de búsqueda
            if (!empty($search)) {
                // $where .= " WHERE nombre LIKE '%$search%' OR apellido LIKE '%$search%' OR correo LIKE '%$search%'";
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
                    $where 
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