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

        $id_empresa = $_SESSION['EMPRESA'];
        //CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

            //NOMBRE DE LA GRILLA
                $grilla->GrillaName         = 'activos_fijos_upload';      //NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
            //QUERY
                $grilla->TableName          = 'activos_fijos_upload';      //NOMBRE DE LA TABLA EN LA BASE DE DATOS
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
        /**/    $grilla->Link = $link;      //Conexion a la BD          /**/
        /**/    $grilla->inicializa($_POST);//variables POST            /**/
        /**/    $grilla->GeneraGrilla();    // Inicializa la Grilla     /**/
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

            //====================================// UPLOAD FILE PLANTILLA APORTES //====================================//
            //***********************************************************************************************************//
            function createUploader(){
                var sucursal = document.getElementById('filtro_sucursal_inventario').value
                ,   bodega   = document.getElementById('filtro_ubicacion_inventario').value;

                var uploader = new qq.FileUploader({
                    element : document.getElementById('div_upload_file'),
                    action  : 'upload_file/upload_file.php',
                    debug   : false,
                    params  : { sucursal : sucursal, bodega : bodega },
                    button            : null,
                    multiple          : false,
                    maxConnections    : 3,
                    allowedExtensions : ['xls', 'ods'],
                    sizeLimit         : 10*1024*1024,
                    minSizeLimit      : 0,
                    onSubmit          : function(id, fileName){},
                    onProgress        : function(id, fileName, loaded, total){},
                    onComplete        : function(id, fileName, responseJSON){
                                            document.getElementById('div_upload_file').querySelector('.qq-upload-list').innerHTML='';

                                            console.log(responseJSON);
                                            var JsonText = JSON.stringify(responseJSON);

                                            // buscarEmpleadoCargado();
                                            console.log(responseJSON);
                                            console.log(responseJSON.success);
                                            if(JsonText == '{}'){ alert("Aviso\nLo sentimos a ocurrido un problema con la carga del archivo, por favor verifique si se logro subir el excel en caso contrario intentelo nuevamente!"); return; }
                                            else if (responseJSON.success==true){
                                                var idUpload     = responseJSON.idUpload;
                                                // document.getElementById('divPadreModalUploadFile').setAttribute('style','');
                                                close_ventana_upload_file();
                                                Inserta_Div_activos_fijos_upload(idUpload);
                                                MyBusquedaActivosFijos();
                                            }
                                            else{
                                                // document.getElementById('divPadreModalUploadFile').setAttribute('style','');
                                                close_ventana_upload_file();
                                                MyBusquedaactivos_fijos_upload();
                                            }
                                        },
                    onCancel : function(fileName){},
                    messages :
                    {
                        typeError    : "{file}\nArchivo no permitido.\n\n Solo se permiten los siguientes archivo:\n\n'xls', 'ods'",
                        sizeError    : "\"{file}\" Archivo muy grande, Tamano Maximo Permitido ( {sizeLimit} ).",
                        minSizeError : "{file} is too small, minimum file size is {minSizeLimit}.",
                        emptyError   : "{file} is empty, please select files again without it.",
                        onLeave      : "Cargando Archivo."
                    }
                });
            }
            createUploader();

            function ventana_upload_excel(){
                if(globalNameFileUpload != ''){ alert('Elimine el archivo anterior antes de subir uno nuevo!'); return; }
                document.getElementById('divPadreModalUploadFile').setAttribute('style','display:block;');
            }

            function close_ventana_upload_file(){ document.getElementById('divPadreModalUploadFile').setAttribute('style',''); }

            function cancelUploadFile(){
                var xhr     = new XMLHttpRequest()
                ,   bodyXhr = 'bd.php?nameFileUpload='+globalNameFileUpload+'&opc=cancelUploadFile';

                xhr.open('POST',bodyXhr, true);
                xhr.onreadystatechange=function(){
                    if(xhr.readyState==4){
                        var responseError = xhr.responseText;
                        if (responseError=='true') {
                            globalNameFileUpload = '';
                            document.getElementById('nombre_excel').value = '';
                            document.getElementById('btn_cancel_doc_upload').style.display = 'none';
                            return;
                        }
                        alert(responseError);
                    }
                    else return;
                }
                xhr.send(null);
            }

            function Agregar_terceros_upload(id){ }

            function Editar_activos_fijos_upload(id){

                var myalto  = Ext.getBody().getHeight()
                ,   myancho = Ext.getBody().getWidth()
                ,   consecutivo = document.getElementById('div_activos_fijos_upload_consecutivo_'+id).innerHTML;

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
                                    id          : 'contenedor_registro_activos',
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
                                url     : '../activo_fijo/filtro_subir_activos.php',
                                scripts : true,
                                nocache : true,
                                params  :
                                {
                                    opc           : 'registro_activos',
                                    urlRender     : '../activo_fijo/subir_activos_registro.php',
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