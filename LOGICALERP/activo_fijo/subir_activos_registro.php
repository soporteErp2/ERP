<?php

        include("../../configuracion/conectar.php");
        include("../../configuracion/define_variables.php");
        include("../../misc/MyGrilla/class.MyGrilla.php");

        /**//////////////////////////////////////////////**/
        /**///       INICIALIZACION DE LA CLASE       ///**/
        /**/                                            /**/
        /**/    $grilla = new MyGrilla();               /**/
        /**/                                            /**/
        /**//////////////////////////////////////////////**/

        $whereEstado = ($filtro != "")? "AND estado='$filtro'": "";

        //CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

            //NOMBRE DE LA GRILLA
                $grilla->GrillaName         = 'activos_fijos_upload_registro';      //NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
            //QUERY
                $grilla->TableName          = 'activos_fijos_upload_registro';      //NOMBRE DE LA TABLA EN LA BASE DE DATOS
                $grilla->MyWhere            = "activo=1 AND id_upload='$id_upload' $whereEstado";     //WHERE DE LA CONSULTA A LA TABLA ""
                // $grilla->OrderBy            = 'numero_identificacion ASC';           //LIMITE DE LA CONSULTA
                $grilla->MySqlLimit         = '0,100';           //LIMITE DE LA CONSULTA
            //TAMANO DE LA GRILLA
                $grilla->AutoResize         = 'true';           //SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
                // $grilla->Ancho              = 515;              //ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
                // $grilla->Alto               = 470;              //ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
                $grilla->QuitarAncho        = 140;               //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
                $grilla->QuitarAlto         = 190;              //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
           //TOOLBAR Y CAMPO DE BUSQUEDA Y FILTROS
                $grilla->Gtoolbar           = 'true';           //SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
                // $grilla->CamposBusqueda     = 'numero_identificacion,nombre,nombre_comercial,direccion,pais,departamento,ciudad';         //VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
                $grilla->Gfilters           = 'false';
                $grilla->GfiltersAutoOpen   = 'false';
                $grilla->AddFilter('Pais','id_pais','pais');
                $grilla->AddFilter('Tipo de Tercero','tipo','tipo');
                $grilla->AddFilter('Tipo de Documento','id_tipo_identificacion','tipo_identificacion');

            //CONFIGURACION DE CAMPOS EN LA GRILLA
                $grilla->AddRowImage('Estado','<center><div style="float:left; width:100%; text-align:center;"><img src="../terceros/images/tercero_upload/[estado].png" style="cursor:pointer" width="16" height="16" /></div></center>',40);
                $grilla->AddRowImage('Error','<center><div style="float:left; width:100%; text-align:center;" onclick="ventana_registro_terceros_error([id],\'[tiene_error]\')"><img src="../terceros/images/tercero_upload/[tiene_error].png" style="cursor:pointer" width="16" height="16" /></div></center>',40);

                $grilla->AddRow('Codigo grupo','codigo_grupo',100);
                $grilla->AddRow('Codigo subgrupo','codigo_subgrupo',100);
                $grilla->AddRow('Centro costos','centro_costos',100);
                $grilla->AddRow('Deteriorable','deteriorable',100);
                $grilla->AddRow('Codigo item','code_bar',100);
                $grilla->AddRow('Codigo activo fijo','codigo_activo',100);
                $grilla->AddRow('Tipo','tipo',100);
                $grilla->AddRow('Metodo depreciacion colgaap','metodo_depreciacion_colgaap',160);
                $grilla->AddRow('Consecutivo nota','consecutivo_nota',100);
                $grilla->AddRow('Vida util','vida_util',100);
                $grilla->AddRow('Valor salvamento','valor_salvamento',100);
                $grilla->AddRow('Cuenta colgaap depreciacion','cuenta_colgaap_depreciacion',160);
                $grilla->AddRow('Contrapartida cuenta colgaap depreciacion','contrapartida_cuenta_colgaap_depreciacion',240);
                $grilla->AddRow('Metodo depreciacion niif','metodo_depreciacion_niif',160);
                $grilla->AddRow('Vida util niif','vida_util_niif',100);
                $grilla->AddRow('Valor salvamento niif','valor_salvamento_niif',140);
                $grilla->AddRow('Cuenta niif depreciacion','cuenta_niif_depreciacion',160);
                $grilla->AddRow('Contrapartida niif depreciacion','contrapartida_niif_depreciacion',190);
                $grilla->AddRow('Cuenta niif deterioro','cuenta_niif_deterioro',110);
                $grilla->AddRow('Contrapartida cuenta niif deterioro','contrapartida_cuenta_niif_deterioro',200);
                $grilla->AddRow('Nit Tercero','nit_tercero',100);
                $grilla->AddRow('Fila excel','fila_excel',70);


                // $grilla->AddRowImage('','<center><div style="float:left; width:100%; text-align:center;"><img src="../terceros/images/[tipo].png" style="cursor:pointer" width="16" height="16" /></div></center>',40);
                // $grilla->AddRow('','tipo_identificacion',70);
                // $grilla->AddRow(utf8_decode('N° Identificacion'),'numero_identificacion',130);
                // $grilla->AddRow('Ciudad Identificacion','ciudad_identificacion',130);
                // $grilla->AddRow('Nombre Comercial','nombre_comercial',200);
                // $grilla->AddRow('Nombre o Razon Social','nombre',200);
                // $grilla->AddRow('Regimen','Tercero Tributario',200);
                // $grilla->AddRow('Telefono 1','telefono1',100);
                // $grilla->AddRow('Telefono 2','telefono2',100);
                // $grilla->AddRow('Celular 1','celular1',100);
                // $grilla->AddRow('Celular 2','celular2',100);
                // $grilla->AddRow('Direccion','direccion',180);

                // $grilla->AddRowImage('Pais','<img src="../../temas/clasico/images/Banderas/[iso2].png" width="16" height="16">&nbsp;&nbsp;[pais]',130);
                // $grilla->AddRow('Estado/Departamento','departamento',150);
                // $grilla->AddRow('Ciudad','ciudad',130);
                // $grilla->AddRow('Cliente','tipo_cliente',70);
                // $grilla->AddRow('Proveedor','tipo_proveedor',70);
                // $grilla->AddRow('Fecha Creacion','fecha_creacion',100);
                // $grilla->AddRow('Fila Excel','fila_excel',50);

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

            //CONFIGURACION DEL MENU CONTEXTUAL
                $grilla->MenuContext        = 'false';       //MENU CONTEXTUAL
                $grilla->MenuContextEliminar= 'false';

            //OPCIONES ADICIONALES EN EL MENU CONTEXTUAL
                // $grilla->AddMenuContext('label','calendario16(icono)','javascript');

                // $grilla->AddTextField('label','campoBd',170,'true');
                // $grilla->AddComboBox('label','campoBd',160,'true','boleanoSiesBd(false)','Si:Si,No:No');//estatico
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

        <script></script>

    <?php
    }

    if(!isset($opcion)){ ?>

        <script type="text/javascript">

            function Editar_activos_fijos_upload_registro(id){ }
            function Agregar_activos_fijos_upload_registro(id){ }

            function ventana_registro_terceros_error(id,error){
                if(error == 'false'){ return; }

                var myalto  = Ext.getBody().getHeight();
                var myancho = Ext.getBody().getWidth();

                Win_Ventana_error_tercero_upload = new Ext.Window({
                    width       : 450,
                    height      : 200,
                    id          : 'Win_Ventana_error_tercero_upload',
                    title       : 'No se identificaron los siguientes campos al subir el activo!',
                    modal       : true,
                    autoScroll  : false,
                    closable    : true,
                    autoDestroy : true,
                    autoLoad    :
                    {
                        url     : '../activo_fijo/bd/bd.php',
                        scripts : true,
                        nocache : true,
                        params  :
                        {
                            op      : 'msjErrorUpload',
                            idError : id
                        }
                    }
                }).show();
            }

        </script>

    <?php
    }


?>