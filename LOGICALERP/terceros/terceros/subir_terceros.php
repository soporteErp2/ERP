<?php

        include("../../../configuracion/conectar.php");
        include("../../../configuracion/define_variables.php");
        include("../../../misc/MyGrilla/class.MyGrilla.php");

        /**//////////////////////////////////////////////**/
        /**///       INICIALIZACION DE LA CLASE       ///**/
        /**/                                            /**/
        /**/    $grilla = new MyGrilla();              /**/
        /**/                                            /**/
        /**//////////////////////////////////////////////**/

        $id_empresa = $_SESSION['EMPRESA'];
        //CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

            //NOMBRE DE LA GRILLA
                $grilla->GrillaName         = 'terceros_upload';      //NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
            //QUERY
                $grilla->TableName          = 'terceros_upload';      //NOMBRE DE LA TABLA EN LA BASE DE DATOS
                $grilla->MyWhere            = "activo=1 AND id_empresa='$id_empresa' AND tercero = 1";     //WHERE DE LA CONSULTA A LA TABLA ""
                $grilla->OrderBy            = 'consecutivo DESC';           //LIMITE DE LA CONSULTA
                $grilla->MySqlLimit         = '0,50';           //LIMITE DE LA CONSULTA
            //TAMANO DE LA GRILLA
                $grilla->AutoResize         = 'false';           //SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
                $grilla->Ancho              = 760;              //ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
                $grilla->Alto               = 400;              //ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
                // $grilla->QuitarAncho        = 70;               //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
                // $grilla->QuitarAlto         = 220;              //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
            //TOOLBAR Y CAMPO DE BUSQUEDA
                $grilla->Gtoolbar           = 'true';           //SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
                $grilla->CamposBusqueda     = '';       //VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
                $grilla->DivActualiBusqueda = '' ;              //VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
            //CONFIGURACION DE CAMPOS EN LA GRILLA
                // $grilla->AddRowImage('','<center><div style="float:left; margin: 0 0 0 7px" onclick="ventana_registro_terceros([id])"><img src="../../temas/clasico/images/BotonesTabs/doc.png?" style="cursor:pointer" width="16" height="16"></div></center>',30);

                $grilla->AddRow('Consecutivo','consecutivo',70);
                $grilla->AddRow('Archivo','nombre_archivo',150);
                $grilla->AddRow('Ok','ok',60);
                $grilla->AddRow('Fail','fail',60);
                $grilla->AddRow('Repetido','repetido',60);
                $grilla->AddRow('Fecha','fecha',80);
                $grilla->AddRow('Hora','hora',80);
                $grilla->AddRow('Usuario','usuario',200);

                $grilla->AddColStyle('consecutivo','text-align:right; width:65px !important; margin-right:5px;');   //ALINEAR NUMEROS LA DERECHA
                $grilla->AddColStyle('ok','text-align:right; width:55px !important; margin-right:5px;');   //ALINEAR NUMEROS LA DERECHA
                $grilla->AddColStyle('fail','text-align:right; width:55px !important; margin-right:5px;');   //ALINEAR NUMEROS LA DERECHA
                $grilla->AddColStyle('repetido','text-align:right; width:55px !important; margin-right:5px;');   //ALINEAR NUMEROS LA DERECHA

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

        <div id="divPadreModalUploadFile" class="fondo_modal_upload_file">
            <div>
                <div>
                    <div>
                        <div id="div_upload_file">
                            <div></div>
                        </div>
                        <div class="btn_div_upload_file2" onclick="close_ventana_upload_file()">X</div>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript">

            function close_ventana_upload_file(){ document.getElementById('divPadreModalUploadFile').setAttribute('style',''); }
            function createUploader(){

                var uploader = new qq.FileUploader({
                    element : document.getElementById('div_upload_file'),
                    action  : '../terceros/terceros/upload_file/upload_file.php',
                    debug   : false,
                    params  : { },
                    button            : null,
                    multiple          : false,
                    maxConnections    : 3,
                    allowedExtensions : ['xls', 'xlsx', 'csv','doc', 'docx', 'bmp', 'jpeg', 'jpg', 'png', 'pdf', 'txt'],
                    sizeLimit         : 10*1024*1024,
                    minSizeLimit      : 0,
                    onSubmit          : function(id, fileName){},
                    onProgress        : function(id, fileName, loaded, total){},
                    onComplete        : function(id, fileName, responseJSON){
                                            document.getElementById('div_upload_file').querySelector('.qq-upload-list').innerHTML='';
                                            document.getElementById('divPadreModalUploadFile').setAttribute('style','');

                                            if(responseJSON.idInsert > 0){ Inserta_Div_terceros_upload(responseJSON.idInsert); }
                                        },
                    onCancel : function(fileName){},
                    messages :
                    {
                        typeError    : "{file}\nArchivo no permitido.\n\n Solo se permiten los siguientes archivo:\n\n'jpg', 'bmp', 'pdf','xls','doc'",
                        sizeError    : "\"{file}\"  Archivo muy grande, Tamano Maximo Permitido ( {sizeLimit} ).",
                        minSizeError : "{file} is too small, minimum file size is {minSizeLimit}.",
                        emptyError   : "{file} is empty, please select files again without it.",
                        onLeave      : "Cargando Archivo."
                    }
                });
            }
            createUploader();

            function Agregar_terceros_upload(id){ }

            function Editar_terceros_upload(id){

                var myalto  = Ext.getBody().getHeight()
                ,   myancho = Ext.getBody().getWidth()
                ,   consecutivo = document.getElementById('div_terceros_upload_consecutivo_'+id).innerHTML;

                Win_Ventana_registro_terceros = new Ext.Window({
                    width       : myancho - 100,
                    height      : myalto - 70,
                    id          : 'Win_Ventana_registro_terceros',
                    title       : 'Consecutivo Upload #'+consecutivo,
                    modal       : true,
                    autoScroll  : false,
                    closable    : false,
                    autoDestroy : true,
                    bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
                    items       :
                    [
                        {
                            border      : false,
                            autoScroll  : true,
                            bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
                            items       :
                            [

                                {
                                    xtype       : "panel",
                                    id          : 'contenedor_registro_terceros',
                                    border      : false,
                                    bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
                                }
                            ]
                        }
                    ],
                    tbar        :
                    [

                        {
                            xtype       : 'panel',
                            border      : false,
                            width       : 130,
                            height      : 56,
                            bodyStyle   : 'background-color:rgba(255,255,255,0);',
                            autoLoad    :
                            {
                                url     : '../terceros/terceros/filtro_subir_terceros.php',
                                scripts : true,
                                nocache : true,
                                params  :
                                {
                                    opc           : 'registro_terceros',
                                    urlRender     : '../terceros/terceros/subir_terceros_registro.php',
                                    imprimeVarphp : 'id_upload : '+id
                                }
                            }
                        },'-',
                        {
                            xtype       : 'button',
                            width       : 60,
                            height      : 56,
                            text        : 'Regresar',
                            scale       : 'large',
                            iconCls     : 'regresar',
                            iconAlign   : 'top',
                            handler     : function(){ Win_Ventana_registro_terceros.close(id) }
                        }
                    ]
                }).show();
            }

        </script>

    <?php
    }


?>