<?php

include("../../../../configuracion/conectar.php");
include("../../../../configuracion/define_variables.php");

switch ($opt) {
    case 'get_sections':
        Panel_Control_Contler_Controller::get_sections($mysql);
        break;
    case 'get_cash_register':
        Panel_Control_Contler_Controller::get_cash_register($section_id,$mysql);
        break;
    case 'get_table':
        Panel_Control_Contler_Controller::get_table($section_id,$mysql);
        break;        
    case 'get_configuration':
        Panel_Control_Contler_Controller::get_configuration($mysql);
        break;
    case 'save':
        Panel_Control_Contler_Controller::save($mysql);
        break;
          
    default:
        echo json_encode(["msg"=>"what are you looking for?","opt"=>$opt]);
        break;
}

class Panel_Control_Contler_Controller {

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

    public static function get_configuration($mysql){
        $sql = "SELECT data FROM configuracion_general WHERE modulo='panel_de_control/contler' AND activo=1 AND id_empresa=$_SESSION[EMPRESA] ";
        $query = $mysql->query($sql);
        $data = $mysql->result($query,0,'data');
        echo ($data)? json_encode($data) : json_encode(["data"=>null]) ; 
    }

    public static function save($mysql){
        $json   = file_get_contents('php://input');
        $data   = json_decode($json,true);
        $form_data = $data["form_data"];

        $sql = "SELECT id FROM configuracion_general WHERE modulo='panel_de_control/contler' AND activo=1 AND id_empresa=$_SESSION[EMPRESA] ";
        $query = $mysql->query($sql);
        $id = $mysql->result($query,0,'id');

        if ($id>0) {
            $sql ="UPDATE configuracion_general SET data='".json_encode($form_data)."' WHERE id=$id";
            $query = $mysql->query($sql);
        }
        else{
            $sql ="INSERT INTO configuracion_general (modulo, descripcion, data, id_empresa) 
                    VALUES ('panel_de_control/contler','configuracion sincronizacion contler','".json_encode($form_data)."',$_SESSION[EMPRESA]) ";
            $query = $mysql->query($sql);
        }

        echo ($query)? json_encode(["status"=>'success']) : json_encode(["status"=>"failed"]) ; 
    }

}