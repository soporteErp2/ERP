<?php

     include("../../configuracion/conectar.php");
     include("../../configuracion/define_variables.php");
     include("../../misc/MyGrilla/class.MyGrilla.php");

    /**//////////////////////////////////////////////**/
    /**///       INICIALIZACION DE LA CLASE       ///**/
    /**/                                            /**/
    /**/    $grilla = new MyGrilla();              /**/
    /**/                                            /**/
    /**//////////////////////////////////////////////**/


    //CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        //NOMBRE DE LA GRILLA
            $grilla->GrillaName         = 'grillaContactos';      //NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
        //QUERY
            $grilla->TableName          = 'empleados_informacion_contacto';      //NOMBRE DE LA TABLA EN LA BASE DE DATOS
            $grilla->MyWhere            = "activo=1 AND id_empleado= $ID";     //WHERE DE LA CONSULTA A LA TABLA ""
            $grilla->OrderBy            = '';           //LIMITE DE LA CONSULTA
            $grilla->MySqlLimit         = '0,50';           //LIMITE DE LA CONSULTA
        //TAMANO DE LA GRILLA
            $grilla->AutoResize         = 'false';           //SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
            $grilla->Ancho              = 515;              //ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
            $grilla->Alto               = 470;              //ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
            $grilla->QuitarAncho        = 70;               //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
            $grilla->QuitarAlto         = 220;              //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
        //TOOLBAR Y CAMPO DE BUSQUEDA
            $grilla->Gtoolbar           = 'true';           //SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
            $grilla->CamposBusqueda     = 'campobd1,campobd2';       //VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
            $grilla->DivActualiBusqueda = '' ;              //VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
        //CONFIGURACION DE CAMPOS EN LA GRILLA
            $grilla->AddRowImage('','<center><img src="images/[contacto_principal].png" style="cursor:pointer" width="16" height="16" id="imgPrincipal_[id]" /></center>','30');
            $grilla->AddRow('Nombre','nombre_completo',350);
            $grilla->AddRow('Direccion','direccion',200);
            $grilla->AddRow('Telefono','telefono',150);
            $grilla->AddRow('Celular','celular',150);
            $grilla->AddRow('Ocupacion','ocupacion',150);
            $grilla->AddRow('Parentesco','parentesco',200);
            //$grilla->AddRow('Principal','parentesco',200);

            //$grilla->AddRowImage('','<center><div style="float:left"></div></center>',18);

            $grilla->AddColStyle('campoBd','text-align:right; width:95px !important; margin-right:5px;');   //ALINEAR NUMEROS LA DERECHA

        //CONFIGURACION FORMULARIO
            $grilla->FContenedorAncho       = 280;
            $grilla->FColumnaGeneralAncho   = 270;
            $grilla->FColumnaGeneralAlto    = 25;
            $grilla->FColumnaLabelAncho     = 70;
            $grilla->FColumnaFieldAncho     = 170;

        //CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
            $grilla->VentanaAuto        = 'true';          //SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
            $grilla->TituloVentana      = 'Nuevo Contacto'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
            $grilla->VBarraBotones      = 'true';          //SI HAY O NO BARRA DE BOTONES
            $grilla->VBotonNuevo        = 'true';          //SI LLEVA EL BOTON DE AGREGAR REGISTRO
            $grilla->VBotonNText        = 'Agregar Informacion <br>de Contacto'; //TEXTO DEL BOTON DE NUEVO REGISTRO
            $grilla->VBotonNImage       = 'documentadd';            //IMAGEN CSS DEL BOTON
            //$grilla->AddBotton('Regresar','regresar',' Win_Panel_Global.close();');

            $grilla->VAutoResize        = 'false';           //SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
            $grilla->VAncho             = 280;              //ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
            $grilla->VAlto              = 305;              //ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
            $grilla->VQuitarAncho       = 200;               //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
            $grilla->VQuitarAlto        = 150;              //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
            $grilla->VAutoScroll        = 'false';          //SI LA VENTANA TIENE O NO AUTOSCROLL
            $grilla->VBotonEliminar     = 'true';           //SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
            $grilla->VComporEliminar    = 'true';           //COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

        //CONFIGURACION DEL MENU CONTEXTUAL
            $grilla->MenuContext        = 'true';       //MENU CONTEXTUAL
            $grilla->MenuContextEliminar= 'false';

        //OPCIONES ADICIONALES EN EL MENU CONTEXTUAL
            //$grilla->AddMenuContext('label','calendario16','javascript');

            $grilla->AddTextField('id_empleado','id_empleado',150,'true','true',$ID);
            $grilla->AddComboBox('Parentesco','id_parentesco',150,'true','true','configuracion_tipos_contacto,id,nombre,true');
            $grilla->AddTextField('Nombres','nombres',150,'true');
            $grilla->AddValidation('nombres','mayuscula');
            $grilla->AddTextField('Apellidos','apellidos',150,'true');
            $grilla->AddValidation('apellidos','mayuscula');
            $grilla->AddTextField('Ocupacion','ocupacion',150,'true');
            $grilla->AddTextField('Direccion','direccion',150,'true');
            $grilla->AddTextField('Telefono','telefono',150,'true');
            $grilla->AddTextField('Celular','celular',150,'true');
            $grilla->AddComboBox('Principal','contacto_principal',150,'true','false','Si:Si, No:No');




            //$grilla->AddComboBox('Apellidos','apellidos',160,'true','boleanoSiesBd(false)','Si:Si,No:No');//estatico
           // $grilla->AddTextArea('label','campoBd',160,50,'true');


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

    <script>
        var parentesco=document.getElementById('grillaContactos_id_parentesco').value;
        Ext.get('DIV_grillaContactos_id_parentesco').load({
            url     : 'bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                op          : 'select_informacion_parentesco',
                parentesco  : parentesco,
                id_empleado : '<?php echo $ID ?>'
                //id_contacto :
            }
        });

        //var principal=document.getElementById('grillaContactos_contacto_principal').value;
        Ext.get('DIV_grillaContactos_contacto_principal').load({
            url     : 'bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                op          : 'select_contacto_principal',
                //parentesco  : parentesco,
                id_empleado : '<?php echo $ID ?>',
                opcion      : '<?php echo $opcion ?>',
                id_contacto : '<?php echo $id ?>'
                //id_contacto :
            }
        });


        // document.getElementById('grillaContactos_id_parentesco').setAttribute("onchange","cambia()");

        // function cambia(){

        //     var parentesco=document.getElementById('grillaContactos_nativo').value;
        //     if(parentesco == 'Referencia Personal'){
        //         document.getElementById('EmpConte_grillaContactos_institucion').style.display = "none";
        //         document.getElementById('EmpConte_grillaContactos_ciudad').style.display      = "none";

        //         document.getElementById('grillaContactos_institucion').value = "nativo";
        //         document.getElementById('grillaContactos_ciudad').value      = "nativo";

        //     }
        //     else{
        //         document.getElementById('EmpConte_grillaContactos_institucion').style.display = "inline";
        //         document.getElementById('EmpConte_grillaContactos_ciudad').style.display      = "inline";

        //         document.getElementById('grillaContactos_institucion').value = "";
        //         document.getElementById('grillaContactos_ciudad').value      = "";
        //     }

        // }

        // cambia();




    </script>

<?php
}

if(!isset($opcion)){ ?>

    <script></script>

<?php
}

//$sql = "SELECT unico "

/*if($opcion == 'Vupdate'){ ?>

    <script>
        //alert("hola");
    </script>

<?php
}*/

?>