<?php

    include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");
    include("../../../misc/MyGrilla/class.MyGrilla.php");
    $id_empresa = $_SESSION['EMPRESA'];
    $id_sucursal = $_SESSION['SUCURSAL'];
    $agregar_cierre = (user_permisos(199,'false') == 'false')? 'false' : 'true';
    $editar_cierre = (user_permisos(200,'false') == 'false')? 'false' : 'true';

    /**//////////////////////////////////////////////**/
    /**///       INICIALIZACION DE LA CLASE       ///**/
    /**/                                            /**/
    /**/    $grilla = new MyGrilla();              /**/
    /**/                                            /**/
    /**//////////////////////////////////////////////**/


    //CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        //NOMBRE DE LA GRILLA
            $grilla->GrillaName         = 'cierre_por_periodo';      //NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
        //QUERY
            $grilla->TableName          = 'cierre_por_periodo';      //NOMBRE DE LA TABLA EN LA BASE DE DATOS
            $grilla->MyWhere            = "activo=1 AND id_empresa=$id_empresa AND id_sucursal=$id_sucursal";     //WHERE DE LA CONSULTA A LA TABLA ""
            $grilla->OrderBy            = 'consecutivo DESC';           //LIMITE DE LA CONSULTA
            $grilla->MySqlLimit         = '0,100';           //LIMITE DE LA CONSULTA
        //TAMANO DE LA GRILLA
            $grilla->AutoResize         = 'false';           //SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
            $grilla->Ancho              = 500;              //ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
            $grilla->Alto               = 310;              //ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
            // $grilla->QuitarAncho        = 550;               //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
            // $grilla->QuitarAlto         = 560;              //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
        //TOOLBAR Y CAMPO DE BUSQUEDA
            $grilla->Gtoolbar           = 'true';           //SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
            $grilla->CamposBusqueda     = 'consecutivo,fecha_inicio,fecha_final,usuario';       //VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
            $grilla->DivActualiBusqueda = '' ;              //VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
        //CONFIGURACION DE CAMPOS EN LA GRILLA
            $grilla->AddRowImage('Estado','<center><img src="img/estado_doc/[estado].png" style="cursor:pointer" width="16" height="16"/></center>','50');
            $grilla->AddRow('Consecutivo','consecutivo',70);
            $grilla->AddRow('Fecha Inicio','fecha_inicio',80);
            $grilla->AddRow('Fecha Final','fecha_final',80);
            $grilla->AddRow('Usuario','usuario',220);

            // $grilla->AddColStyle('campoBd','text-align:right; width:95px !important; margin-right:5px;');   //ALINEAR NUMEROS LA DERECHA

        //CONFIGURACION FORMULARIO
            $grilla->FContenedorAncho       = 350;
            $grilla->FColumnaGeneralAncho   = 330;
            $grilla->FColumnaGeneralAlto    = 25;
            $grilla->FColumnaLabelAncho     = 50;
            $grilla->FColumnaFieldAncho     = 200;

        //CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
            $grilla->VentanaAuto        = 'false';          //SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
            $grilla->TituloVentana      = 'Nuevo'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
            $grilla->VBarraBotones      = 'true';          //SI HAY O NO BARRA DE BOTONES
            $grilla->VBotonNuevo        = 'false';          //SI LLEVA EL BOTON DE AGREGAR REGISTRO
            $grilla->VBotonNText        = 'Nuevo'; //TEXTO DEL BOTON DE NUEVO REGISTRO
            $grilla->VBotonNImage       = 'documentadd';            //IMAGEN CSS DEL BOTON
            if ($agregar_cierre=='true') {
                $grilla->AddBotton('Nuevo','add_new','Agregar_cierre_por_periodo();');
            }

            $grilla->AddBotton('Regresar','regresar',' Win_Ventana_cierre_por_periodo.close();');

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

        function Agregar_cierre_por_periodo() {
            ventanaAgregarEditar(0);
        }

        function Editar_cierre_por_periodo(id) {
            if ('<?php echo $editar_cierre; ?>'=='true') {
                ventanaAgregarEditar(id);
            }
            else{
                alert('Aviso\nNo posee los permisos para editar el cierre');
            }
        }

        function ventanaAgregarEditar(id) {
            var opc           = ''
            ,   title_ventana = ''
            ,   title_boton   = '';

            if (id==0) {
                opc           = 'agregar';
                title_ventana = 'Agregar Cierre';
                title_boton   = 'Guardar';
            }
            else{
                opc           = 'editar';
                title_ventana = 'Modificar Cierre';
                title_boton   = 'Actualizar';
            }

            Win_Ventana_AgregarEditar = new Ext.Window({
                width       : 350,
                height      : 380,
                id          : 'Win_Ventana_AgregarEditar',
                title       : title_ventana,
                modal       : true,
                autoScroll  : false,
                closable    : false,
                autoDestroy : true,
                autoLoad    :
                {
                    url     : 'cierre_por_periodo/bd.php',
                    scripts : true,
                    nocache : true,
                    params  :
                    {
                        opc : 'ventanaAgregarEditar',
                        id : id,
                    }
                },
                tbar        :
                [
                    {

                        xtype   : 'buttongroup',
                        columns : 3,
                        title   : '',
                        style   : 'border-right:none;',
                        id      : 'content_btn',
                        items   :
                        [
                            {
                                xtype     : 'button',
                                id        : 'btn_genera_cierre',
                                width     : 60,
                                height    : 56,
                                text      : 'Generar',
                                scale     : 'large',
                                iconCls   : 'guardar',
                                iconAlign : 'top',
                                hidden    : false,
                                handler   : function(){ BloqBtn(this); generarCierre(); }
                            },
                            {
                                xtype     : 'button',
                                id        : 'btn_edita_cierre',
                                width     : 60,
                                height    : 56,
                                text      : 'Editar',
                                scale     : 'large',
                                iconCls   : 'edit',
                                iconAlign : 'top',
                                hidden    : true,
                                handler   : function(){ BloqBtn(this); editarCierre(); }
                            },

                        ]
                    },
                    {
                        xtype   : 'buttongroup',
                        columns : 1,
                        id      : 'btn_elimina_cierre',
                        title   : '',
                        style   : 'border-right:none;',
                        hidden    : true,
                        items   :
                        [
                            {
                                xtype     : 'button',
                                id        : '',
                                width     : 60,
                                height    : 56,
                                text      : 'Eliminar',
                                scale     : 'large',
                                iconCls   : 'cancel',
                                iconAlign : 'top',
                                // hidden    : true,
                                handler   : function(){ BloqBtn(this); eliminaCierre(); }
                            },
                        ]
                    },
                    {
                        xtype   : 'buttongroup',
                        columns : 1,
                        id      : 'btn_restaura_cierre',
                        title   : '',
                        style   : 'border-right:none;',
                        hidden    : true,
                        items   :
                        [
                            {
                                xtype     : 'button',
                                id        : '',
                                width     : 60,
                                height    : 56,
                                text      : 'Restaurar',
                                scale     : 'large',
                                iconCls   : 'restaurar32',
                                iconAlign : 'top',
                                // hidden    : true,
                                handler   : function(){ BloqBtn(this); restauraCierre(); }
                            },
                        ]
                    },
                    {
                        xtype   : 'buttongroup',
                        columns : 1,
                        title   : '',
                        style   : 'border-right:none;',
                        items   :
                        [
                            {
                                xtype       : 'button',
                                width       : 60,
                                height      : 56,
                                text        : 'Regresar',
                                scale       : 'large',
                                iconCls     : 'regresar',
                                iconAlign   : 'top',
                                hidden      : false,
                                handler     : function(){ BloqBtn(this); Win_Ventana_AgregarEditar.close(id) }
                            }
                        ]
                    }
                ]
            }).show();
        }

    </script>

<?php
}

?>