<?php

require '../../Clases/ApiFunctions.php';

class Pedido_Controller extends ApiFunctions
{
    private $configuration_data = null;
    private $cash_register = null;
    private $table_detail = null;
    private $id_comanda = null;

    /**
     * @api {get} /contler/pedido?pedido=35604_34315 Consultar Pedido
     * @apiVersion 1.0.0
     * @apiDescription Consultar pedido del pos
     * @apiName get_pos_contler
     * @apiGroup Contler
     *
     * @apiParam {String} [pedido] Numero del pedido a consultar
     * 
     * @apiError Unauthorized datos incorrectos de autenticacion
     * @apiError failure No se recibieron parametros para la consulta
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "failure": "No se recibieron parametros para la consulta"
     *     }
     *
     * @apiSuccess {Number} estado Estado del pedido
     *
     * @apiSuccessExample Success-Response:
     * 
     * 
     * {
     * 		"estado": 3,
     * }
     */
    public function show($params){
        $id_cuenta = explode("_",$params['pedido'])[0];
        $id_comensal = explode("_",$params['pedido'])[1];
        $sql = "SELECT id,cantidad,cantidad_pendiente FROM ventas_pos_mesas_cuenta_items WHERE id_cuenta=$id_cuenta AND id_comensal=$id_comensal";
        $query = $this->mysql->query($sql);
        // $n_rows = $this->mysql->numrows($query);
        $db_id = false;
        $state = 3;
        while ($row=$this->mysql->fetch_array($query)) {
            $db_id = $row['id'];
            if ( ($row['cantidad']-$row['cantidad_pendiente']) > 0) {
                $state = 1;
            }
        }

        if (!$db_id) {
            return ["status"=>false,"detalle"=>"no existe un pedido con ese id"];
        }
        return ["status"=>true,"estado"=>$state];

    }

    
    public function get_config(){
        $sql = "SELECT data FROM configuracion_general WHERE modulo='panel_de_control/contler' AND activo=1 AND id_empresa=$this->id_empresa";
        $query = $this->mysql->query($sql);
        $response = $this->mysql->result($query,0,'data');
        $this->configuration_data = json_decode($response,true);
    }

    public function is_open_cash_register(){
        $sql="SELECT
						id,
						id_caja,
						nombre_caja,
						id_seccion,
						seccion
					FROM ventas_pos_cajas_secciones
					WHERE activo=1
					AND id_empresa=$this->id_empresa
					AND id_seccion=".$this->configuration_data['section'];
        $query=$this->mysql->query($sql);
        $estadoCaja = 'Cerrada';
        while ($row=$this->mysql->fetch_array($query)) {
            $arrayRest[] = array('id' => $row['id_caja'], 'nombre' => $row['nombre_caja'] , 'id_seccion' => $row['id_seccion'] );
        }
        if (count($arrayRest)>0) {
            if (count($arrayRest)==1) {
                $sql   = "SELECT estado
                            FROM ventas_pos_cajas_movimientos
                            WHERE activo=1
                            AND id_caja = ".$arrayRest[0]['id']."
                            AND id_empresa=$this->id_empresa
                            AND estado='Abierta' ";
                $query=$this->mysql->query($sql);
                $estadoCaja = $this->mysql->result($query,0,'estado');
            }
            // print_r($arrayRest);
            $arrayResult = array('status' => 'success', 'cajas'=> $arrayRest, 'estado_caja_unica'=> $estadoCaja, 'id_caja_unica'=>$arrayRest[0]['id'], 'nombre_caja_unica' =>$arrayRest[0]['nombre'] , 'id_seccion' =>$arrayRest[0]['id_seccion'] );
        }
        else{
            $arrayResult = array('status' => 'failed', 'message'=>'No hay cajas configuradas' );
        }
        $this->cash_register = $arrayResult;
        return $arrayResult['estado_caja_unica'];
    }

    public function is_open_table($id_mesa){
        $sql=" SELECT
                    vm.id,
                    vm.id_estado,
                    vm.descripcion,
                    vm.estado,
                    vm.color_estado,
                    SUM(vc.cantidad) AS cantidad
                FROM
                    ventas_pos_mesas_cuenta vm
                LEFT JOIN ventas_pos_mesas_cuenta_comensales vc ON vc.id_cuenta = vm.id
                WHERE
                    vm.activo = 1
                AND vm.id_empresa=$this->id_empresa
                AND vm.id_mesa = $id_mesa
                AND vm.estado<>'Cerrada'
                GROUP BY
                    vc.id_cuenta";
        $query=$this->mysql->query($sql);
        if($query) {
            $num_rows = $this->mysql->num_rows($query);
            $disponible = ($num_rows>0)? false : true ;
            $color = "";
            $totalComensales = $this->mysql->result($query,0,'cantidad');
            $id_cuenta = $this->mysql->result($query,0,'id');

            $sql="SELECT
                        vc.*
                    FROM
                        ventas_pos_mesas_cuenta_comensales vc
                    WHERE
                    vc.tipo='Huesped'
                    AND vc.activo=1
                    AND vc.id_cuenta=$id_cuenta";
            $query=$this->mysql->query($sql);
            while ($row=$this->mysql->fetch_array($query)) {
                $arrayHuespedes[] = $row;
            }

            $arrayHuespedes = isset($arrayHuespedes)?$arrayHuespedes:'';
            //
            // ventas_pos_mesas_cuenta_items_recetas
            // DATOS DE LA CUENTA DE LA MESA
            $sql="SELECT
                        VPI.id,
                        VPI.id_cuenta,
                        VPI.id_item,
                        VPI.codigo_item,
                        VPI.nombre_item,
                        VPI.cantidad,
                        VPI.cantidad_pendiente,
                        VPI.termino,
                        VPI.precio,
                        VPI.id_impuesto,
                        VPI.nombre_impuesto,
                        VPI.porcentaje_impuesto,
                        VPI.id_comanda,
                        VPC.estado AS estado_comanda,
                        VPI.observaciones,
                        VPI.id_usuario,
                        VPI.documento_usuario,
                        VPI.usuario
                    FROM ventas_pos_mesas_cuenta_items AS VPI LEFT JOIN ventas_pos_comanda AS VPC ON VPC.id=VPI.id_comanda
                    WHERE VPI.activo=1 AND VPI.id_empresa=$this->id_empresa AND VPI.id_cuenta=$id_cuenta ";
            $query=$this->mysql->query($sql);
            while ($row=$this->mysql->fetch_array($query)) {
                if ($row['estado_comanda']==3) { continue; }

                $cantidadTotal = (int)$row['cantidad'] - (int)$row['cantidad_pendiente'];
                $arrayDetail[] = array(
                                        "id"                  => $row['id'],
                                        "id_cuenta"           => $row['id_cuenta'],
                                        "id_item"             => $row['id_item'],
                                        "codigo_item"         => $row['codigo_item'],
                                        "nombre_item"         => $row['nombre_item'],
                                        "cantidad"            => $cantidadTotal,
                                        "termino"             => $row['termino'],
                                        "precio"              => $row['precio'],
                                        "id_impuesto"         => $row['id_impuesto'],
                                        "nombre_impuesto"     => $row['nombre_impuesto'],
                                        "porcentaje_impuesto" => $row['porcentaje_impuesto'],
                                        "observaciones"       => $row['observaciones'],
                                        "id_comanda" 		  => $row['id_comanda'],
                                        "comandado"           => (($row['id_comanda']>0)? true : false ),
                                        //"receta"              => $arrayReceta,
                                        "id_usuario"          => $row['id_usuario'],
                                        "documento_usuario"   => $row['documento_usuario'],
                                        "usuario"             => $row['usuario'],
                                        );
            }



            $arrayResult = array(
                                'status'          => 'success',
                                'id_cuenta'       => $id_cuenta,
                                'detalle_cuenta'  => $arrayDetail,
                                'huespedes'       => $arrayHuespedes,
                                'totalComensales' =>$totalComensales,
                                'disponible'      => $disponible,
                                'color'           => $color,
                                'debug'           =>  $sql,
                                // 'color'        => "#db5957"
                            );
        }
        else{
            $arrayResult = array('status' => 'failed', 'message'=>'Se produjo un error al consultar' );
        }

        $this->table_detail = $arrayResult;
        return  $arrayResult['id_cuenta'];
    }

    public function add_account_client($data){
        $randomico = $this->random();

        if(isset($data['id_cuenta'])){
            $sql = "SELECT id FROM ventas_pos_mesas_cuenta WHERE id=".$data['id_cuenta'];
            // $deleteQuery=$this->mysql->query("DELETE FROM ventas_pos_mesas_cuenta_comensales WHERE id_cuenta=".$data['id_cuenta']);
        }
        else{
            $sql = "INSERT INTO ventas_pos_mesas_cuenta
                (
                    randomico,
                    id_mesa,
                    nombre_mesa,
                    id_estado,
                    descripcion,
                    estado_mesa,
                    color_estado,
                    fecha_apertura,
                    hora_apertura,
                    id_usuario_apertura,
                    documento_usuario_apertura,
                    nombre_usuario_apertura,
                    estado,
                    id_empresa
                )
                VALUES
                (
                    '$randomico',
                    '".$this->configuration_data['table']."',
                    '$data[nombre_mesa]',
                    '3',
                    '".$this->cash_register['nombre_caja_unica']."',
                    'no_disponible',
                    '#db5957',
                    '".date("Y-m-d")."',
                    '".date("H:i:s")."',
                    '".$this->id_usuario."',
                    '".$this->documento_usuario."',
                    '".$this->nombre_usuario."',
                    'Abierta',
                    '".$this->id_empresa."'
                )";

            $query=$this->mysql->query($sql);
            if (!$query) {
                return ["status"=>false,"detalle"=>"Se produjo un error al abrir la cuenta de la mesa","debug"=>$sql];
                // $arrayResult = array('status' => 'failed', 'message'=>'Se produjo un error al abrir la cuenta de la mesa' );
                // echo json_encode($arrayResult);
                // return;
            }
            $sql = "SELECT id FROM ventas_pos_mesas_cuenta WHERE randomico='$randomico' ";
        }

        $query=$this->mysql->query($sql);
        $id_cuenta = $this->mysql->result($query,0,'id');

        // consultar la informacion del huesped
        $hotels_data['request_url']    = $this->apiHotels['url']."getPosHuesped/".$this->nit_empresa."/$data[num_hab]"; // ESTA VARIABLE ES HEREADA DE LA CLASE DE FUNCIONES GLOBALES
        $hotels_data['request_method'] = "GET";
        $huesped = json_decode($this->curlApi($hotels_data));
        
        if ($huesped->error) {
            return ["status"=>false,"detalle"=>"no se encontro el huesped"];
        }
        

        // consultar si ya se agrego el huesped entonces solo agregar el item a la cuenta y generar la comanda
        $sql = "SELECT id_comensal FROM 
                    ventas_pos_mesas_cuenta_comensales 
                WHERE id_cuenta=$id_cuenta AND numero_habitacion=".$huesped->numero_habitacion." AND numero_reserva=".$huesped->response[0]->numero_reserva;
        $query=$this->mysql->query($sql);
        $id_comensal = $this->mysql->result($query,0,'id_comensal');

        //insertar comanda
        if(!$this->add_comanda($id_cuenta)){
            return ["status"=>false,"detalle"=>"error al insertar comanda"];
        }

        if ($id_comensal) {
            $add_items = $this->add_client_items($huesped,$data['pedidos'],$id_cuenta);
            if (!$add_items["status"]) {
                return ["status"=>false,"detalle"=>"error agregando los items, ".$add_items["detalle"]];
            }
        }
        else{
            $id_reserva         = $huesped->response[0]->guest_reservations;
            $numero_reserva     = $huesped->response[0]->numero_reserva;
            $numero_habitacion  = $huesped->response[0]->numero_habitacion;
            $id_comensal        = $huesped->response[0]->guest_id;
            $documento_comensal = $huesped->response[0]->numero_documento;
            $comensal           = $huesped->response[0]->primer_nombre.' '.$huesped->response[0]->segundo_nombre.' '.$huesped->response[0]->primer_apellido.' '.$huesped->response[0]->segundo_apellido;

            
            $sql = "INSERT INTO ventas_pos_mesas_cuenta_comensales
            (
                id_cuenta,
                tipo,
                cantidad,
                id_reserva,
                numero_reserva,
                numero_habitacion,
                id_comensal,
                documento_comensal,
                comensal,
                id_empresa

            )
            VALUES
            (
                '$id_cuenta',
                'Huesped',
                '1',
                '".$id_reserva."',							
                '".$numero_reserva."',
                '".$numero_habitacion."',
                '".$id_comensal."',
                '".$documento_comensal."',
                '".$comensal."',
                '$this->id_empresa'
            )";

            $query=$this->mysql->query($sql);

            if (!$query) {
                return ["status"=>false,"detalle"=>"error agregando el huesped al pedido","debug"=>$sql];
            }

            $add_items = $this->add_client_items($huesped,$data['pedidos'],$id_cuenta);
            if (!$add_items["status"]) {
                return ["status"=>false,"detalle"=>"error agregando los items, ".$add_items["detalle"]];
            }
        }

        return ["status"=>true,"id_pedido"=>$id_cuenta."_".$id_comensal];

    }

    public function add_comanda($id_cuenta){
        $randomico = $this->random();
        $sql = "INSERT INTO ventas_pos_comanda 
                    (
                        id_cuenta,
                        fecha,
                        hora,
                        randomico,
                        id_usuario,
                        documento_usuario,
                        usuario,
                        id_caja,
                        nombre_caja,
                        id_empresa,
                        estado
                    )
                    VALUES
                    (
                        '$id_cuenta',
                        '".date("Y-m-d")."',
                        '".date("H:i:s")."',
                        '$randomico',
                        '".$this->id_usuario."',
                        '".$this->documento_usuario."',
                        '".$this->nombre_usuario."',
                        '".$this->cash_register['id_caja_unica']."',
                        '".$this->cash_register['nombre_caja_unica']."',
                        '".$this->id_empresa."',
                        '1'
                    )";
        $query=$this->mysql->query($sql);
        if (!$query) {
            return false;
        } 
        $this->id_comanda = $this->mysql->insert_id();
        return true;
    }

    public function add_client_items($huesped,$items,$id_cuenta){
        $id_items = array_column($items, 'id_item');
        $sql = "SELECT id,codigo, nombre_equipo as nombre,id_impuesto,impuesto FROM items WHERE id IN (".implode(",",$id_items).")";
        $query=$this->mysql->query($sql);

        $result_ids = [];
        while ($row = $this->mysql->fetch_assoc($query)) {
            $result_ids[] = $row['id'];
            $erp_items[$row['id']] = $row;
        }

        // Validar si todos los id_items están en los resultados
        $missing_items = array_diff($id_items, $result_ids);
        if (!empty($missing_items)) {
            return ["status"=>false,"detalle"=>"los items con id ". implode(",", $missing_items)."  no existe en el sistema"];
        } 

        // consultar las recetas de items
        $sql = "SELECT 
                    id_item,
                    codigo_item,
                    nombre_item,
                    id_item_materia_prima,
                    codigo_item_materia_prima,
                    nombre_item_materia_prima,
                    cantidad_item_materia_prima 
                    FROM items_recetas WHERE id_item IN (".implode(",",$result_ids).")";
        $query=$this->mysql->query($sql);
        while ($row = $this->mysql->fetch_assoc($query)) {
            $items_receta[$row['id_item']][] = $row;
        }
        // echo json_encode($items_receta);
        // guardar los items
        foreach ($items as $key => $item) {
           $sql = "INSERT INTO ventas_pos_mesas_cuenta_items
                        (
                            id_cuenta,
                            id_item,
                            codigo_item,
                            nombre_item,
                            cantidad,
                            cantidad_pendiente,
                            termino,
                            precio,
                            id_impuesto,
                            nombre_impuesto,
                            porcentaje_impuesto,
                            observaciones,
                            id_comanda,
                            id_usuario,
                            documento_usuario,
                            usuario,
                            id_bodega_produccion,
                            id_empresa,
                            activo,
                            id_comensal
                        )
                        VALUES
                        (
                            '$id_cuenta',
                            '".$erp_items[$item['id_item']]['id']."',
                            '".$erp_items[$item['id_item']]['codigo']."',
                            '".$erp_items[$item['id_item']]['nombre']."',
                            '$item[cantidad]',
                            '0',
                            '',
                            '$item[precio]',
                            '".$erp_items[$item['id_item']]['id_impuesto']."',
                            '".$erp_items[$item['id_item']]['impuesto']."',
                            '".$erp_items[$item['id_item']]['id']."porcentaje_impuesto',
                            '',
                            '".$this->id_comanda."',
                            '".$this->id_usuario."',
                            '".$this->documento_usuario."',
                            '".$this->nombre_usuario."',
                            '',
                            '".$this->id_empresa."',
                            '1',
                            '".$huesped->response[0]->guest_id."'
                        )";
            $query =$this->mysql->query($sql);
            $last_insert_id = $this->mysql->insert_id();
            $insert_str = "";
            // echo $item['id_item'];
            // var_dump($items_receta[$item['id_item']]);
            //insertar receta del item
            foreach ($items_receta[$item['id_item']] as $key => $receta) {
                $insert_str .= "(
                                    '$id_cuenta',
                                    '$last_insert_id',
                                    '$receta[id_item_materia_prima]',
                                    '$receta[codigo_item_materia_prima]',
                                    '$receta[nombre_item_materia_prima]',
                                    '$receta[cantidad_item_materia_prima]',
                                    '',
                                    '".$this->id_usuario."',
                                    '".$this->documento_usuario."',
                                    '".$this->nombre_usuario."',
                                    '".$this->id_empresa."',
                                    '1'
                                ),";
            }
            // echo $insert_str;
            $insert_str = substr($insert_str, 0, -1);
            // echo $insert_str;
            $sql = "INSERT INTO ventas_pos_mesas_cuenta_items_recetas
                                (
                                    id_cuenta,
                                    id_cuenta_item,
                                    id_item,
                                    codigo_item,
                                    nombre_item,
                                    cantidad,
                                    observaciones,
                                    id_usuario,
                                    documento_usuario,
                                    usuario,
                                    id_empresa,
                                    activo
                                )
                                VALUES $insert_str";
            $query =$this->mysql->query($sql);

        }


        return ["status"=>true];
        // return ["status"=>true,"detalle"=>"los items con id ". implode(",", $missing_items)." no existe en el sistema"];
    }

    /**
     * @api {post} /contler/pedido/ Crear pedido en el POS
     * @apiVersion 1.0.0
     * @apiDescription Registrar pedido en el pos
     * @apiName store_pos_contler
     * @apiPermission Pos
     * @apiGroup Contler
     *
     * @apiParam {String} nit Nit de la empresa
     * @apiParam {String} num_hab Numero de habitacion del huesped
     * @apiParam {String} doc_huesped Numero del documento del huesped
     * @apiParam {Object[]} pedidos Listado de los articulos a facturar
     * @apiParam {Number} pedidos.id_item Id del item en ERP
     * @apiParam {Number} pedidos.precio Precio de venta
     * @apiParam {Number} pedidos.cantidad Cantidad vendida
     *
     * @apiParamExample {json} Request-Example:
     * {
     *    "nit": "2002",
     *    "num_hab": "9023",
     *    "doc_huesped": "9085357",
     *    "pedidos": [
     *        {
     *            "id_item": 332,
     *            "precio": 1200,
     *            "cantidad": 1
     *        },
     *        {
     *            "id_item": 1751,
     *            "precio": 25000,
     *            "cantidad": 3
     *        }
     *    ]
     * }
     * 
     * @apiSuccess {200} success  informacion registrada
     * @apiSuccess {200} pedido  Consecutivo del Pedido
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *        "success": "informacion registrada",
     *        "pedido": "Prefijo de la factura Ej. 35605_34315",
     *     }
     * @apiErrorExample Error-Response:
     * HTTP/1.1 400 Bad Response
     * {
     *  "failure": "Ha ocurrido un error",
     *   "detalle": "detalle del o los errores"
     * }
     *
     * @apiError failure Ha ocurrido un error
     * @apiError detalle
     *     HTTP/1.1 400 Bad Response
     *     {
     *     	"failure":"Ha ocurrido un error",
     *     "detalle": "detalle del error"
     *     }
     */
    public function store($data){
        $this->get_config();
        if (!$this->configuration_data) {
            return ["status"=>false,"detalle"=>"no se ha configurado contler en ERP (en el panel de control, opcion contler)"];
        }
        if(!$this->is_open_cash_register()){
            return ["status"=>false,"detalle"=>"la caja del restaurante esta cerrada!"];
        }
        
        // si ya existe una cuenta creada en la mesa
        $this->is_open_table($this->configuration_data['table']);
        if ($this->table_detail['id_cuenta']) {
            $data['id_cuenta'] = $this->table_detail['id_cuenta'];
        }
        $order_add = $this->add_account_client($data);
        if (!$order_add['status']) {
            return ["status"=>false,"msg"=>"se produjo un error al agregar el pedido","detalle"=>$order_add['detalle']];
        }
        else{
            return ["status"=>true,"id_pedido"=>$order_add['id_pedido']];
        }

        // echo $this->configuration_data['section'];
        // echo json_encode($this->table_detail);
    }
}