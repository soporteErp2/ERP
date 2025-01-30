<?php

include("../../../../configuracion/conectar.php");
include("../../../../configuracion/define_variables.php");

switch ($opt) {
    case 'get_sections':
        Controller::get_sections($mysql);
        break;
    case 'get_cash_register':
        Controller::get_cash_register($section_id,$mysql);
        break;
    case 'get_table':
        Controller::get_table($section_id,$mysql);
        break;
          
    default:
        echo json_encode(["msg"=>"what are you looking for?"]);
        break;
}

class Controller {

    public static function get_sections($mysql){
        $sql = "SELECT id,nombre FROM `ventas_pos_secciones` WHERE activo=1 AND restaurante='Si' ";
        $query = $mysql->query($sql);
        $ret_val = [];
        while($row = $mysql->fetch_array($query)){
            $ret_val[] = ["id"=>$row['id'],"nombre"=>$row["nombre"]];
        }
        echo json_encode($ret_val);
    }
    
    public static function get_cash_register($section_id,$mysql){
        $sql = "SELECT id,nombre_caja as nombre  FROM `ventas_pos_cajas_secciones` WHERE activo=1 AND id_seccion='$section_id' ";
        $query = $mysql->query($sql);
        $ret_val = [];
        while($row = $mysql->fetch_array($query)){
            $ret_val[] = ["id"=>$row['id'],"nombre"=>$row["nombre"]];
        }
        echo json_encode($ret_val);
    }
    public static function get_table($section_id,$mysql){
        $sql = "SELECT id,nombre  FROM `ventas_pos_mesas` WHERE activo=1 AND id_seccion='$section_id' ";
        $query = $mysql->query($sql);
        $ret_val = [];
        while($row = $mysql->fetch_array($query)){
            $ret_val[] = ["id"=>$row['id'],"nombre"=>$row["nombre"]];
        }
        echo json_encode($ret_val);
    }

}