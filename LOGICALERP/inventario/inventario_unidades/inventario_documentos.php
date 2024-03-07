<?php

    include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");
    include("../../../misc/MyGrilla/class.MyGrilla.php");

    /**//////////////////////////////////////////////**/
    /**///       INICIALIZACION DE LA CLASE       ///**/
    /**/                                            /**/
    /**/    $grilla = new MyGrilla();               /**/
    /**/                                            /**/
    /**//////////////////////////////////////////////**/

    //CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        //NOMBRE DE LA GRILLA
            $grilla->GrillaName         = 'InventariosDocumentos';     //NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
        //QUERY
            $grilla->TableName          = 'inventario_documentos';       //NOMBRE DE LA TABLA EN LA BASE DE DATOS
            $grilla->MyWhere            = 'id_inventario='.$elid;     //WHERE DE LA CONSULTA A LA TABLA "$TableName"
            $grilla->MySqlLimit         = '0,100';          //LIMITE DE LA CONSULTA
        //TAMANO DE LA GRILLA
            $grilla->AutoResize         = 'true';               //SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
            //$grilla->Ancho              = 1100;              //ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
            //$grilla->Alto               = 550;              //ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
            $grilla->QuitarAncho        = 387;               //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
            $grilla->QuitarAlto         = 170;              //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
        //TOOLBAR Y CAMPO DE BUSQUEDA
            $grilla->Gtoolbar           = 'true';           //SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
            $grilla->CamposBusqueda     = 'nombre_inventario_documento';     //VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
            $grilla->DivActualiBusqueda = '' ;                //VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
        //CONFIGURACION DE CAMPOS EN LA GRILLA
            $grilla->AddRow('Tipo Documento','nombre_inventario_documento',250);
            $grilla->AddRow('Fecha','fecha_creacion',250);
            $grilla->AddRowImage('Ver','<center><div style="float:left; margin: 0 0 0 7px"><img src="images/ver.png" style="cursor:pointer" width="16" height="16" onClick="ver_documento([id]);"></div></center>',30);
            $grilla->AddRowImage('Eliminar','<center><div style="float:left; margin: 0 0 0 7px"><img src="images/eliminar.png" style="cursor:pointer" width="16" height="16" onclick="eliminar_documento([id]);"></div></center>',30);

        //CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
            $grilla->VentanaAuto        = 'true';           //SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
            $grilla->TituloVentana      = 'Inventarios Documentos'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
            $grilla->VBarraBotones      = 'false';           //SI HAY O NO BARRA DE BOTONES
            $grilla->VBotonNuevo        = 'false';           //SI LLEVA EL BOTON DE AGREGAR REGISTRO
            $grilla->VBotonNText        = 'Nuevo Registro'; //TEXTO DEL BOTON DE NUEVO REGISTRO
            $grilla->VBotonNImage       = 'add';            //IMAGEN CSS DEL BOTON
            $grilla->VAutoResize        = 'true';           //SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
            //$grilla->VAncho             = 400;              //ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
            //$grilla->VAlto              = 200;              //ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
            $grilla->VQuitarAncho       = 120;               //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
            $grilla->VQuitarAlto        = 160;              //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
            $grilla->VAutoScroll        = 'false';          //SI LA VENTANA TIENE O NO AUTOSCROLL
            $grilla->VBotonEliminar     = 'false';           //SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
            $grilla->VComporEliminar    = 'false';           //COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

        //CONFIGURACION DEL MENU CONTEXTUAL
            $grilla->MenuContext        = 'false';
            $grilla->MenuContextEliminar= 'true';

        //OPCIONES ADICIONALES EN EL MENU CONTEXTUAL


        //CONFIGURACION DE LOS FORMULARIOS DE CAPTURA Y EDICION
            //$grilla->AddTextField('Codigo','codigo',200,'true','false');
            //$grilla->AddValidation('codigo','numero');
            $grilla->AddTextField('Nombre','nombre',200,'true','false');



    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    /**//////////////////////////////////////////////////////////////**/
    /**///              INICIALIZACION DE LA GRILLA               ///**/
    /**/                                                            /**/
    /**/    $grilla->Link = $link;      //Conexion a la BD          /**/
    /**/    $grilla->inicializa($_POST);//variables POST            /**/
    /**/    $grilla->GeneraGrilla();    // Inicializa la Grilla     /**/
    /**/                                                            /**/
    /**//////////////////////////////////////////////////////////////**/

if(!isset($opcion)){?>
    <script>
        Agregar_Autosize_Ext("Win_Ventana_inventario_documentos",360,20,"true","true");
        ////////////////////////////.Procede a eliminar documento.////////////////////
        function eliminar_documento(id){
            if(confirm('Esta seguro que desea cotinuar')){
                Elimina_Div_InventariosDocumentos(id);
                Ext.Ajax.request({
                    url     : "bd/bd.php",
                    params  :
                    {
                        op   : 'eliminar_documento',
                        id   : id,
                    }
                });

            }
        }
        ////////////////////////////.Procede a visualizar el documento.////////////////////
        function ver_documento(id){ window.open("ver_documento_inventario.php?id="+id); }

        function Editar_InventariosDocumentos(){ }


    </script>
<?php } ?>