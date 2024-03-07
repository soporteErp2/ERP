<?php
    // include "../configuracion/conectar.php";

    $client = curl_init();
    //$data = array("token" => $token, "id" => 1);
    $url  = 'http://192.168.8.145:8000/api/users/' . $_POST['id'] . '/?token=' . $_POST["token"] . '&id=' . $_POST["id"];
    $data = array("token" => $_POST["token"]);
    $data = json_encode($data);
    $nit       = $_POST["nit"];
    $username = $_POST["username"];

    $options = array(
        CURLOPT_URL            => $url,
        CURLOPT_CUSTOMREQUEST  => "GET",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => false,
        CURLOPT_HTTPHEADER     => array("Authorization:Bearer " . $_POST["token"]),
        CURLOPT_POSTFIELDS     => $data,
    );

    curl_setopt_array($client, $options);

    $response = curl_exec($client);

    $httpCode = curl_getinfo($client, CURLINFO_HTTP_CODE);
    curl_close($client);

    $respuesta = json_decode($response);

    if ($respuesta->response == true) {

        // CONSULTAR LA EMPRESA
        include_once('../misc/ConnectDb/class.ConnectDb.php');

        // $conexionDB = '127.0.0.1'; //PRODUCCION
        // $bd         = 'erp_acceso'; // PRODUCCION

        $conexionDB = '192.168.8.202';
        $user       = 'root';
        $pass       = 'serverchkdsk';
        $bd         = 'logicalsofterp';

        $objConectDB = new ConnectDb(
                        "MySql",            // API SQL A UTILIZAR  MySql, MySqli
                        "$conexionDB",  // SERVIDOR
                        "$user",                // USUARIO DATA BASE
                        "$pass",        // PASSWORD DATA BASE
                        "$bd"               // NOMBRE DATA BASE
                    );

        $mysql = $objConectDB->getApi();
        $link  = $mysql->conectar();

        $sql="SELECT nombre,servidor,bd,id_plan,fecha_vencimiento_plan FROM host WHERE activo=1 AND nit=$nit";
        $query=$mysql->query($sql,$mysql->link);
        // $nombre_empresa         = $mysql->result($query,0,'nombre');
        $servidor               = $mysql->result($query,0,'servidor');
        $bd                     = $mysql->result($query,0,'bd');
        $id_plan                = $mysql->result($query,0,'id_plan');
        $fecha_vencimiento_plan = $mysql->result($query,0,'fecha_vencimiento_plan');

        $mysql->close();

        // CONECTAR A LA BASE DE DATOS DE LA EMPRESA
        $objConectDB = new ConnectDb(
                        "MySql",            // API SQL A UTILIZAR  MySql, MySqli
                        "$servidor",  // SERVIDOR
                        "$user",                // USUARIO DATA BASE
                        "$pass",        // PASSWORD DATA BASE
                        "$bd"               // NOMBRE DATA BASE
                    );

        // CONSULTAR EL ID DE LA EMPRESA
        $sql="SELECT id,nombre FROM empresas WHERE activo=1 AND documento=$nit";
        $query=$mysql->query($sql,$mysql->link);
        $id_empresa     = $mysql->result($query,0,'id');
        $nombre_empresa = $mysql->result($query,0,'nombre');
        if ($id_usuario<=0 || $id_usuario=='') {
            // return array("result" => "error", "detalle" => "No existe la empresa con el nit $nit" );
            echo "<h1>Error</h1><br>No existe la empresa con el nit $nit ";
        }

        // VERIFICAR EL USUARIO QUE ESTA INICIANDO SESION
        $sql="SELECT id FROM empleados WHERE activo=1 AND usuario=$username AND id_empresa=$id_empresa";
        $query=$mysql->query($sql,$mysql->link);
        $id_usuario = $mysql->result($query,0,'id');
        if ($id_usuario<=0 || $id_usuario=='') {
            // return array("result" => "error", "detalle" => "El usuario no existe en la empresa con nit $nit" );
            echo "<h1>Error</h1><br>El usuario no existe en la empresa con nit $nit ";
        }

        echo "<h1>Login OK</h1>";
        //

    }
    else{
        var_dump("Error de token");
    }

?>
