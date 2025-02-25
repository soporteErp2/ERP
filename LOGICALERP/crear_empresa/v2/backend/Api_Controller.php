<?php
include_once '../../../../configuracion/conexion.php';

class Api_Controller
{
    private $link = null;
    public function connect(){
        global $server;
        $this->link = mysql_connect($server->server_name,$server->user,$server->password);
        if(!$this->link){ return ["error conectando al servidor"]; }
        mysql_select_db($server->database,$this->link);
        if(!@mysql_select_db($server->database,$this->link)){ return ["error conectando a la bd ".$server->database]; }
        return $this->link;
    }

    public function verify_db(){
        echo json_encode([1,2,3]);
    }
}
