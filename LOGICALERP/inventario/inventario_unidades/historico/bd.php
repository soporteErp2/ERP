<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

    switch ($opc) {

        case "generarFiltro":
            generarFiltro();
            break;

        case "generarHistorico":
            generarHistorico($id,$filtroHistorial,$link);
            break;
    }

    //----------------------------------- GENERA FILTRO VENTANA HISTORICO ---------------------------------//
    function generarFiltro(){
        echo'<div style="float:left; margin: 5px 0 0 10px">
                <div style="float:left; width:60px; padding:3px 0 0 0">
                    Seleccione
                </div>
                <div style="float:left; width:140px">
                <select class="myfield" style="width:140px" onchange="renderizaHistorico(this.value)">
                    <option value="Todo">Todos los eventos</option>
                    <option value="Compra">Compra</option>
                    <option value="Ingreso">Ingreso al inventario</option>
                    <option value="Mantenimiento">Mantenimientos</option>
                    <option value="Reparaciones">Reparaciones</option>
                    <option value="Prestamos">Prestamos</option>
                    <option value="Traslados">Traslados</option>
                </select>
                </div>
            </div>';
    }

    //----------------------------------- GENERA CUERPO VENTANA HISTORICO ---------------------------------//
    function generarHistorico($id,$filtroHistorial,$link){

        $contArray=0;

        // SQL INVENTARIO COMPRA E INGRESO --->
        if($filtroHistorial=='Todo' || $filtroHistorial=='Compra' || $filtroHistorial=='Ingreso'){

            $sqlInv="   SELECT  id,
                                fecha_compra,
                                fecha_creacion_en_inventario
                        FROM    inventarios
                        WHERE   id='$id'";

            $queryInv= mysql_query($sqlInv,$link);
            while($row=mysql_fetch_array($queryInv)){

                if($filtroHistorial=='Compra' || $filtroHistorial=='Todo'){
                    $contArray++;
                    $arrayObj[$contArray]['id']      = $row['id'];
                    $arrayObj[$contArray]['estado']  = 'Compra';
                    $arrayObj[$contArray]['fecha']   = $row['fecha_compra'];
                    $arrayObj[$contArray]['usuario'] = '';
                }

                if($filtroHistorial=='Ingreso' || $filtroHistorial=='Todo'){
                    $contArray++;
                    $arrayObj[$contArray]['id']      = $row['id'];
                    $arrayObj[$contArray]['estado']  = 'Ingreso Inventario';
                    $arrayObj[$contArray]['fecha']   = $row['fecha_creacion_en_inventario'];
                    $arrayObj[$contArray]['usuario'] = '';
                }
            }
        }

        // SQL MANTENIMIENTO --->
        if($filtroHistorial=='Todo' || $filtroHistorial=='Mantenimiento'){

            $sqlMan="   SELECT  id,fecha_mantenimiento,nombre_usuario
                        FROM    mantenimiento
                        WHERE   id_inventario='$id'";

            $queryMan= mysql_query($sqlMan,$link);
            while($row=mysql_fetch_array($queryMan)){
                $contArray++;
                $arrayObj[$contArray]['id']      = $row['id'];
                $arrayObj[$contArray]['estado']  = 'Mantenimiento';
                $arrayObj[$contArray]['fecha']   = $row['fecha_mantenimiento'];
                $arrayObj[$contArray]['usuario'] = $row['nombre_usuario'];
            }
        }

        // SQL reparacion --->
        if($filtroHistorial=='Todo' || $filtroHistorial=='Reparaciones'){

            $sqlRep="   SELECT  id,fecha_reparacion,nombre_usuario
                        FROM    reparacion
                        WHERE   id_inventario='$id'";

            $queryRep= mysql_query($sqlRep,$link);
            while($row=mysql_fetch_array($queryRep)){
                $contArray++;
                $arrayObj[$contArray]['id']      = $row['id'];
                $arrayObj[$contArray]['estado']  = 'Reparacion';
                $arrayObj[$contArray]['fecha']   = $row['fecha_reparacion'];
                $arrayObj[$contArray]['usuario'] = $row['nombre_usuario'];
            }
        }

        // SQL prestamos --->
        if($filtroHistorial=='Todo' || $filtroHistorial=='Prestamos'){

            $sqlPres="  SELECT  id,fecha,nombre_usuario
                        FROM    inventario_prestamos
                        WHERE   id_equipo='$id'";

            $queryPres= mysql_query($sqlPres,$link);
            while($row=mysql_fetch_array($queryPres)){
                $contArray++;
                $fecha=explode(' ',$row['fecha']);

                $arrayObj[$contArray]['id']      = $row['id'];
                $arrayObj[$contArray]['estado']  = 'Prestamo';
                $arrayObj[$contArray]['fecha']   = $fecha[0];
                $arrayObj[$contArray]['usuario'] = $row['nombre_usuario'];
            }
        }

        // SQL traslados --->
        if($filtroHistorial=='Todo' || $filtroHistorial=='Traslados'){

            $sqlPres="  SELECT  id,fecha,nombre_usuario
                        FROM    inventario_traslados
                        WHERE   id_equipo='$id'";

            $queryPres= mysql_query($sqlPres,$link);
            while($row=mysql_fetch_array($queryPres)){
                $contArray++;
                $fecha=explode(' ',$row['fecha']);

                $arrayObj[$contArray]['id']      = $row['id'];
                $arrayObj[$contArray]['estado']  = 'Traslado';
                $arrayObj[$contArray]['fecha']   = $fecha[0];
                $arrayObj[$contArray]['usuario'] = $row['nombre_usuario'];
            }
        }

        usort($arrayObj, 'ordenar');
        mostrar_array($arrayObj);
    }

    function ordenar( $a, $b ) {
        return strtotime($a['fecha']) - strtotime($b['fecha']);
    }

    function mostrar_array($datos) {
        $acumHistorico='';
        foreach($datos as $dato){
            $acumHistorico.= '  <div class="FilaDatosDesgloseItems" ondblclick="detalleHistorial('.$dato['id'].',\''.$dato['estado'].'\')">';
            //$acumHistorico.= '  <div class="FilaDatosDesgloseItems">';
            $acumHistorico.= '      <div class="DatoDesgloseItems D0">'.fecha_larga($dato['fecha']).'</div>';
            $acumHistorico.= '      <div class="DatoDesgloseItems D1">'.$dato['estado'].'</div>';
            $acumHistorico.= '      <div class="DatoDesgloseItems D2">'.$dato['usuario'].'</div>';
            $acumHistorico.= '  </div>';
        }
        echo $acumHistorico;
    }
?>

