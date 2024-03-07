<?php

    include("../../../../configuracion/conectar.php");
    include("../../../../configuracion/define_variables.php");
    include("../../../../misc/MyGrilla/class.MyGrilla.php");

    $id_empresa=$_SESSION['EMPRESA'];

    /**//////////////////////////////////////////////**/
    /**///       INICIALIZACION DE LA CLASE       ///**/
    /**/                                            /**/
    /**/    $grilla = new MyGrilla();              /**/
    /**/                                            /**/
    /**//////////////////////////////////////////////**/


    //CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        //NOMBRE DE LA GRILLA
            $grilla->GrillaName         = 'buscar_item';      //NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
        //QUERY
            $grilla->TableName          = 'items';      //NOMBRE DE LA TABLA EN LA BASE DE DATOS
            $grilla->MyWhere            = "activo=1 AND id_empresa='$id_empresa'";     //WHERE DE LA CONSULTA A LA TABLA ""
            $grilla->OrderBy            = 'codigo ASC';           //LIMITE DE LA CONSULTA
            $grilla->MySqlLimit         = '0,50';           //LIMITE DE LA CONSULTA
        //TAMANO DE LA GRILLA
            $grilla->AutoResize         = 'true';           //SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
            $grilla->Ancho              = 515;              //ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
            $grilla->Alto               = 470;              //ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
            $grilla->QuitarAncho        = 515;               //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
            $grilla->QuitarAlto         = 380;              //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
        //TOOLBAR Y CAMPO DE BUSQUEDA
            $grilla->Gtoolbar           = 'true';           //SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
            $grilla->CamposBusqueda     = 'codigo,nombre_equipo,familia,grupo,subgrupo';       //VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
            $grilla->DivActualiBusqueda = '' ;              //VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
        //CONFIGURACION DE CAMPOS EN LA GRILLA
            $grilla->AddRow('Codigo','codigo',80);
			$grilla->AddRow('Nombre','nombre_equipo',250);
			$grilla->AddRow('Familia','familia',200);
			$grilla->AddRow('Grupo','grupo',200);
			$grilla->AddRow('Subgrupo','subgrupo',200);
			$grilla->AddRowImage('Venta','<center><img src="img/[estado_venta].png"></center>','80');
			$grilla->AddRowImage('Compra','<center><img src="img/[estado_compra].png"></center>','80');

        //CONFIGURACION FORMULARIO
            $grilla->FContenedorAncho       = 350;
            $grilla->FColumnaGeneralAncho   = 330;
            $grilla->FColumnaGeneralAlto    = 25;
            $grilla->FColumnaLabelAncho     = 50;
            $grilla->FColumnaFieldAncho     = 200;

        //CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
            $grilla->VentanaAuto        = 'false';          //SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
            $grilla->TituloVentana      = 'Nuevo'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
            $grilla->VBarraBotones      = 'false';          //SI HAY O NO BARRA DE BOTONES
            $grilla->VBotonNuevo        = 'false';          //SI LLEVA EL BOTON DE AGREGAR REGISTRO
            $grilla->VBotonNText        = 'Nuevo'; //TEXTO DEL BOTON DE NUEVO REGISTRO
            $grilla->VBotonNImage       = 'documentadd';            //IMAGEN CSS DEL BOTON

            $grilla->VAutoResize        = 'true';           //SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
            $grilla->VAncho             = 290;              //ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
            $grilla->VAlto              = 130;              //ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
            $grilla->VQuitarAncho       = 200;               //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
            $grilla->VQuitarAlto        = 150;              //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
            $grilla->VAutoScroll        = 'false';          //SI LA VENTANA TIENE O NO AUTOSCROLL
            $grilla->VBotonEliminar     = 'true';           //SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
            $grilla->VComporEliminar    = 'true';           //COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)




    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    /**//////////////////////////////////////////////////////////////**/
    /**///              INICIALIZACION DE LA GRILLA               ///**/
    /**/                                                            /**/
    /**/    $grilla->Link = $link;      //Conexion a la BD        /**/
    /**/    $grilla->inicializa($_POST);//variables POST          /**/
    /**/    $grilla->GeneraGrilla();    // Inicializa la Grilla    /**/
    /**/                                                            /**/
    /**//////////////////////////////////////////////////////////////**/

 if($opcion == 'Vupdate' || $opcion == 'Vagregar'){ ?>

    <script></script>

<?php
}

if(!isset($opcion)){ ?>

    <script>
        function Editar_buscar_item(id){
        	var nombre = document.getElementById('div_buscar_item_nombre_equipo_'+id).innerHTML;
        	document.getElementById('itemsGeneral_id_item_transformacion').value=id;
        	document.getElementById('itemsGeneral_nombre_item_transformacion').value=nombre;
        	Win_Ventana_buscarItemTransformacion.close();
        }

        function Agregar_buscar_item(id){ }

    </script>

<?php
}

 ?>