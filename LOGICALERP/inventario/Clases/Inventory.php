<?php
// include("../../../configuracion/conectar.php");
date_default_timezone_set($_SESSION['TIMEZONE']);

/**
 * clase para el movimiento de inventario por promedio ponderado (PP)
 */
class Inventario_pp
{

    /**
     * @param Array $params All params to execute the process, example
     * 
         $params = [ 
            "documento_id" => 1,
            "documento_tipo" => "FC",
            "documento_consecutivo" => "123",
            "fecha" => "2023-12-12",
            "accion_inventario" => "ingresar" o "reversar" o "sacar",
            "accion_documento" => "Generar", // accion del documento, generar, editar, etc
            "items" => [
                            [
                                "id" => 1,
                                "codigo" => "01010101",
                                "nombre" => "ACEITE PIMPINA",
                                "unidad_medida" => "Mililitros",
                                "cantidad_unidades" => "1",
                                "costo" => 100,
                                "cantidad" => 100,
                                "fijar_costo" => true or false // si se envia true entonces no se hace promedio si no que se actualiza el item con el costo que se envia
                                "empresa_id" => 2,
                                "empresa" => "empresa",
                                "sucursal_id" => 2,
                                "sucursal" => "principal",
                                "bodega_id" => 3,
                                "bodega" => "principal"
                            ]
                        ]
        ];
     *          
     */
    public function UpdateInventory($params)
    {   
        $mysql = $params["mysql"];
        // var_dump($params);
        switch ($params["accion_inventario"]) {
            case "ingreso":
            case "reversar salida":
            case "traslado ingreso":
            case "reversar traslado salida":
            case "ingreso ajuste":
            case "reversar salida ajuste":
                $operacion = "+";
                break;
            case "salida":
            case "reversar ingreso":
            case "traslado salida":
            case "reversar traslado ingreso":
            case "reversar ingreso ajuste":
            case "salida ajuste":
                $operacion = "-";
                break;
        }
        
        // $operacion = $params["accion_inventario"]=="ingreso" ? " + " : " - " ;
        
        // actualizar inventario y almacenar el log
        foreach ($params["items"] as $key => $item) {
            // consultar el inventario antes de la actualizacion
            $sql =  "SELECT cantidad, costos 
                    FROM inventario_totales 
                    WHERE id_item = $item[id]
                        AND id_empresa= $item[empresa_id]
                        AND id_sucursal= $item[sucursal_id]
                        AND id_ubicacion= $item[bodega_id]; ";
            $query = $mysql->query($sql);
            $item_antes['cantidad'] = $mysql->result($query,0,"cantidad");
            $item_antes['costos'] = $mysql->result($query,0,"costos");

            $sql_costo = ",costos = $item_antes[costos]";

            //crear condicion para actualizacion de inventario y costo
            if(($item_antes['cantidad']<=0 || $item_antes['costos'] <=0) &&  $params["accion_inventario"] != 'salida'){
                $sql_costo = ",costos = $item[costo]";
            }
            else  if($params['accion_inventario'] == 'ingreso' || 
                    $params['accion_inventario'] == 'traslado ingreso' ||
                    $params['accion_inventario'] == 'reversar traslado salida'){
                $nuevoCostoPromedio = (($item_antes['cantidad']*$item_antes['costos']) + ($item['cantidad']*$item['costo'])) / ($item_antes['cantidad'] + $item['cantidad']);
                $sql_costo=",costos = $nuevoCostoPromedio";
            }
            else if ($params['accion_inventario'] == 'reversar ingreso' ||
                     $params['accion_inventario'] == 'reversar traslado ingreso' ) {
                $nuevoCostoTotal = ($item_antes['cantidad'] * $item_antes['costos']) - ($item['cantidad'] * $item['costo']);
                $nuevaCantidadTotal = $item_antes['cantidad'] - $item['cantidad'];
                $nuevoCostoPromedio = ($nuevaCantidadTotal > 0) ? $nuevoCostoTotal / $nuevaCantidadTotal : 0;
                $sql_costo=",costos = $nuevoCostoPromedio";
            }
            else if (
                    // $params['accion_inventario'] == 'reversar salida'||
                    $params['accion_inventario'] =='traslado salida'||
                    $params['accion_inventario'] =='ingreso ajuste'  ||                  
                    $params['accion_inventario'] =='salida ajuste'                  
                    ) {
                $sql_costo = ",costos = $item[costo]";
            }
            else if (
                $params['accion_inventario'] =='reversar ingreso ajuste' ||                 
                $params['accion_inventario'] =='reversar salida ajuste'                 
                ) 
            {
                $sql_costo = ",costos = costos";
            }
            
            $sql_update = "UPDATE inventario_totales 
                    SET cantidad = (cantidad $operacion $item[cantidad] )
                    $sql_costo
                    WHERE id_item = $item[id]
                    AND id_empresa= $item[empresa_id]
                    AND id_sucursal= $item[sucursal_id]
                    AND id_ubicacion= $item[bodega_id];";
            $query_update = $mysql->query($sql_update);
            if (!$query_update) {
                $error_update = $mysql->error();
            }
            $query = $mysql->query($sql);
            $item_despues['cantidad'] = $mysql->result($query,0,"cantidad");
            $item_despues['costos'] = $mysql->result($query,0,"costos");
            $fijar_costo = !isset($item['fijar_costo']) ? 'false' : $item["fijar_costo"] ;
            $sql_log = "INSERT INTO logs_inventario
                            (
                                id_documento,
                                tipo_documento,
                                consecutivo_documento,
                                fecha_documento,
                                accion_documento,
                                fecha_movimiento,
                                hora_movimiento,
                                accion_inventario,
                                id_item,
                                codigo,
                                item,
                                unidad_medida,
                                cantidad_unidades,
                                costo,
                                cantidad,
                                fijar_costo,
                                costo_anterior,
                                costo_nuevo,
                                cantidad_anterior,
                                cantidad_nueva,
                                id_usuario,
                                documento_usuario,
                                usuario,
                                id_bodega,
                                bodega,
                                id_sucursal,
                                sucursal,
                                id_empresa,
                                empresa,
                                `sql`,
                                sql_estado,
                                sql_respuesta
                            ) VALUES 
                            (
                                $params[documento_id],
                                '$params[documento_tipo]',
                                '$params[documento_consecutivo]',
                                '$params[fecha]',
                                '$params[accion_documento]',
                                '".date("Y-m-d")."',
                                '".date("H:i:s")."',
                                '$params[accion_inventario]',
                                $item[id],
                                '$item[codigo]',
                                '$item[nombre]',
                                '$item[unidad_medida]',
                                '$item[cantidad_unidades]',
                                $item[costo],
                                $item[cantidad],
                                '$fijar_costo',
                                $item_antes[costos],
                                $item_despues[costos],
                                $item_antes[cantidad],
                                $item_despues[cantidad],
                               '$_SESSION[IDUSUARIO]',
                                '$_SESSION[CEDULAFUNCIONARIO]',
                                '$_SESSION[NOMBREFUNCIONARIO]',
                                $item[bodega_id],
                                '$item[bodega]',
                                $item[sucursal_id],
                                '$item[sucursal]',
                                $item[empresa_id],
                                '$item[empresa]',
                                '_sql_',
                                'sql_estado_',
                                'sql_response_'
                            )";
            // almacenar el log detallado
            $sql_log = str_replace("_sql_",str_replace("'","\'",$sql_update),$sql_log);
            
            if (!$query_update) {
                $sql_log = str_replace("sql_estado_","error",$sql_log);
                $sql_log = str_replace("sql_response_",str_replace("'","\'",$error_update),$sql_log);
            }
            else{
                $sql_log = str_replace("sql_estado_","success",$sql_log);
                $sql_log = str_replace("sql_response_","",$sql_log);
            }

            $query_log = $mysql->query($sql_log);
            if (!$query_log) {
                $ret_val[] = ["error" => true, "type"=>"log","msg"=>"error on insert log","detail"=>$mysql->error(),"sql_log"=>$sql_log];
            }
            else{ $ret_val[] = ["error" => false]; }

        }
        return $ret_val;


    }
}

$params = [ 
    "documento_id" => 1,
    "documento_tipo" => "FC",
    "documento_consecutivo" => "123",
    "fecha" => "2023-12-12",
    "accion_inventario" => "sumar",
    "accion_documento" => "Generar", // accion del documento, generar, editar, etc
    "items" => [
                    [
                        "id" => 1,
                        "codigo" => "01010101",
                        "nombre" => "ACEITE PIMPINA",
                        "unidad_medida" => "Mililitros",
                        "cantidad_unidades" => "1",
                        "costo" => 10,
                        "cantidad" => 100,
                        "empresa_id" => 2,
                        "empresa" => "empresa",
                        "sucursal_id" => 2,
                        "sucursal" => "principal",
                        "bodega_id" => 7,
                        "bodega" => "principal"
                    ]
                ]
];
// $obj = new Inventario_pp();
// $obj->UpdateInventory($params);