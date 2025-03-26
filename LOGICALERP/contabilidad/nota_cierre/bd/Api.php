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
            $search = isset($_GET["q"]) ? $_GET["q"] : "";
            // Calcular el offset
            $offset = ($page - 1) * $limit;
            $where = ($_GET['tipo_documento'])? " AND ac.tipo_documento='$_GET[tipo_documento]' " : "";
            $where .= ($_GET['id_sucursal']!='global')? " AND ac.id_sucursal='$_GET[id_sucursal]' " : "";

            // Agregar cláusula WHERE si se proporciona un parámetro de búsqueda
            if (!empty($search)) {
                $where .= " AND (
                                    ac.fecha LIKE '%$search%' OR 
                                    ac.tipo_documento LIKE '%$search%' OR 
                                    ac.consecutivo_documento LIKE '%$search%' OR 
                                    ac.tipo_documento_extendido LIKE '%$search%' OR 
                                    ac.nit_tercero LIKE '%$search%' OR 
                                    ac.tercero LIKE '%$search%' 
                                )";
            }

            $tabla_asientos =($_GET['contabilidad']=='niif')? "asientos_niif" : "asientos_colgaap";

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
                        id_nota_general = '$_POST[id_nota]'
                    ORDER BY
                        cuenta_puc ASC
            SELECT 
                        ac.id,
                        ac.id_documento,
                        ac.fecha,
                        ac.tipo_documento,
                        ac.consecutivo_documento,
                        ac.tipo_documento_extendido,
                        ac.id_tercero,
                        ac.nit_tercero,
                        ac.tercero,
                        ac.id_sucursal,
                        ac.sucursal
                    FROM 
                        $tabla_asientos ac
                    WHERE 
                        ac.activo = 1
                        AND ac.id_empresa = '$_GET[id_empresa]'
                        AND ac.fecha BETWEEN '$_GET[fecha_inicial]' AND '$_GET[fecha_final]' 
                        AND ac.id = (
                            SELECT MIN(ac2.id)
                            FROM asientos_colgaap ac2
                            WHERE ac2.id_documento = ac.id_documento
                            AND ac2.tipo_documento = ac.tipo_documento
                        )
                        $where
                    LIMIT $limit OFFSET $offset";
            $query = $this->mysql->query($sql);
            $ret_val = [];
			while ($row=$this->mysql->fetch_assoc($query)) {
               $ret_val[] = [
                "id"                       => utf8_encode($row['id']),
                "id_documento"             => utf8_encode($row['id_documento']),
                "fecha"                    => utf8_encode($row['fecha']),
                "tipo_documento"           => utf8_encode($row['tipo_documento']),
                "consecutivo_documento"    => utf8_encode($row['consecutivo_documento']),
                "tipo_documento_extendido" => utf8_encode($row['tipo_documento_extendido']),
                "id_tercero"               => utf8_encode($row['id_tercero']),
                "nit_tercero"              => utf8_encode($row['nit_tercero']),
                "tercero"                  => utf8_encode($row['tercero']),
                "id_sucursal"              => utf8_encode($row['id_sucursal']),
                "sucursal"                 => utf8_encode($row['sucursal']),
               ];
            }

            $ret_val = json_encode($ret_val);
            echo $ret_val;
        }

    }

?>