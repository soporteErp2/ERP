<?php

    include("../../../../configuracion/conectar.php");
    include("../../../../configuracion/define_variables.php");
    include("../../../../misc/MyGrilla/class.MyGrilla.php");

    /**//////////////////////////////////////////////**/
    /**///       INICIALIZACION DE LA CLASE       ///**/
    /**/                                            /**/
    /**/    $grilla = new MyGrilla();               /**/
    /**/                                            /**/
    /**//////////////////////////////////////////////**/

    $id_empresa = $_SESSION["EMPRESA"];
    //CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        //NOMBRE DE LA GRILLA
            $grilla->GrillaName         = 'ItemsDocumentos';     //NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
        //QUERY
            $grilla->TableName          = 'items_documentos';       //NOMBRE DE LA TABLA EN LA BASE DE DATOS
            $grilla->MyWhere            = 'activo=1 AND id_inventario='.$elid;     //WHERE DE LA CONSULTA A LA TABLA "$TableName"
            $grilla->MySqlLimit         = '0,100';          //LIMITE DE LA CONSULTA
        //TAMANO DE LA GRILLA
            $grilla->AutoResize         = 'true';               //SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
            $grilla->Ancho              = 560;              //ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
            // $grilla->Alto               = 390;              //ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
            // $grilla->QuitarAncho        = 105;               //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
            $grilla->QuitarAlto         = 290;              //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
        //TOOLBAR Y CAMPO DE BUSQUEDA
            $grilla->Gtoolbar           = 'true';           //SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
            $grilla->CamposBusqueda     = 'nombre_inventario_documento';     //VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
            $grilla->DivActualiBusqueda = '' ;                //VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA

        //CONFIGURACION DE CAMPOS EN LA GRILLA
            $grilla->AddRow('Tipo Documento','tipo_documento_nombre',200);
            $grilla->AddRow('Fecha','fecha_creacion',200);
            $grilla->AddRowImage('','<img src="../../../temas/clasico/images/BotonesTabs/buscar16.png?" title="Ver" style="cursor:pointer" width="16" height="16" onclick="ver_documentos_items([id],\'[randomico_documento]\',\'[nombre_documento]\',\'[ext]\');">',20);
            $grilla->AddRowImage('','<img src="../../../temas/clasico/images/BotonesTabs/guardar16.png?" title="Descargar" style="cursor:pointer" width="16" height="16" onclick="descargar_documentos_items([id],\'[randomico_documento]\',\'[nombre_documento]\',\'[ext]\');">',20);
            $grilla->AddRowImage('','<img src="items/items_documentos/images/eliminar.png" title="Eliminar" style="cursor:pointer" width="16" height="16" onclick="eliminar_documento([id],\'[randomico_documento]\',\'[ext]\');">',20);

        //CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
            $grilla->VentanaAuto        = 'true';           //SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
            $grilla->TituloVentana      = 'Inventarios Documentos'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
            $grilla->VBarraBotones      = 'true';           //SI HAY O NO BARRA DE BOTONES
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
            $grilla->AddBotton('Agregar Documento','documentadd','ventana_agregar_documentos_items('.$elid.')','false','true');
        //CONFIGURACION DEL MENU CONTEXTUAL
            $grilla->MenuContext        = 'false';
            $grilla->MenuContextEliminar= 'true';

        //CONFIGURACION DE LOS FORMULARIOS DE CAPTURA Y EDICION
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
        Agregar_Autosize_Ext("Win_Ventana_items_documentos",360,20,"true","true");
        ////////////////////////////.Procede a eliminar documento.////////////////////
        function eliminar_documento(id,randomico_documento,ext){
            if(confirm('Esta seguro que desea cotinuar!')){
                Elimina_Div_ItemsDocumentos(id);
                Ext.Ajax.request({
                    url     : "items/items_documentos/bd/bd.php",
                    params  :
                    {
                        op              : 'eliminar_archivo',
                        idArchivo       : id,
                        nombreRandomico : randomico_documento+'_'+id+'.'+ext
                    },
                    success :function (result, request){
                                var responseAjax = result.responseText;
                                if(responseAjax != 'true'){ alert("Aviso\nHa ocurrido un problema con la conexion de la base de datos!"); }
                            },
                    failure : function(){ alert("Aviso\nHa ocurrido un problema con la conexion al servidor!"); }
                });
            }
        }

        function descargar_documentos_items(id,randomico_documento,nombre_documento,ext){
            window.open("items/items_documentos/bd/bd.php?op=descargarArchivo&nombreDocumento="+nombre_documento+"."+ext+"&nombreRandomico="+randomico_documento+"_"+id+"."+ext);
        }

        function validaImagen(ext){
            var arrayTest = Array('bmp','jpg','png','gif','pdf','BMP','JPG','PNG','GIF','PDF');

            for(i=0; i<arrayTest.length; i++){ if(arrayTest[i]==ext){ return true; } }
            return false;
        }

        function ver_documentos_items(id,randomico_documento,nombre_documento,ext){
            if(!validaImagen(ext)){
                window.open("items/items_documentos/bd/bd.php?op=descargarArchivo&nombreDocumento="+nombre_documento+"."+ext+"&nombreRandomico="+randomico_documento+"_"+id+"."+ext);
                // window.location.href=ruta+randomico_documento+'_'+id+'.'+ext;
            }
            else{
                if(ext=='pdf'){ return; viewDocumentoItems(id,randomico_documento,ext,Ext.getBody().getWidth()-50,Ext.getBody().getHeight()-50); return; }
                else{

                    Ext.Ajax.request({
                        url     : "items/items_documentos/bd/bd.php",
                        success : function(response){
                                    response  = response.responseText;
                                    response  = JSON.parse(response);

                                    var alto  = response.alto
                                    ,   ancho = response.ancho;

                                    if(response.alto<96){ alto=96; }
                                    else if(response.alto>Ext.getBody().getHeight()-170){ alto = Ext.getBody().getHeight()-170; }
                                    else{ alto += 10; }

                                    if(response.ancho<96){ ancho=96; }
                                    else if(response.ancho>Ext.getBody().getWidth()-120){ ancho = Ext.getBody().getWidth()-120; }
                                    else{ ancho += 10; }

                                    alto  += 100;
                                    ancho += 70;

                                    viewDocumentoItems(id,randomico_documento,nombre_documento+'.'+ext,ext,ancho,alto);
                                },
                        params  :
                        {
                            op     : 'consultaSizeImageDocumentInventario',
                            nombre : randomico_documento+'_'+id+'.'+ext
                        }
                    });
                }
            }
        }

        function viewDocumentoItems(id,randomico_documento,nombre_documento,ext,width,height){

            var titulo = document.getElementById('div_ItemsDocumentos_tipo_documento_nombre_'+id).innerHTML;
            Win_Ventana_VerDocumento_items = new Ext.Window({
                width       : width,
                height      : height,
                id          : 'Win_Ventana_VerDocumento_items',
                title       : titulo,
                modal       : true,
                autoScroll  : true,
                closable    : true,
                autoDestroy : true,
                autoLoad    :
                {
                    url     : 'items/items_documentos/bd/bd.php',
                    scripts : true,
                    nocache : true,
                    params  :
                    {
                        op              : 'ventanaVerImagenDocumentoItems',
                        nombreImage     : randomico_documento+'_'+id+'.'+ext,
                        nombreDocumento : nombre_documento,
                        type            : ext
                    }
                },
                tbar        :
                [
                    {
                        xtype     : 'button',
                        text      : 'Regresar',
                        scale     : 'large',
                        iconCls   : 'regresar',
                        iconAlign : 'left',
                        handler   : function(){ Win_Ventana_VerDocumento_items.close(); }
                    }
                ]
            }).show();
        }

        function Editar_ItemsDocumentos(id){ }

        function ventana_agregar_documentos_items(id){
            Win_select_items_documentos = new Ext.Window({
                width       : 250,
                id          : 'Win_select_items_documentos',
                height      : 120,
                title       : 'Seleccione Documento',
                modal       : true,
                autoScroll  : false,
                closable    : true,
                autoDestroy : true,
                autoLoad    :
                {
                    url     : 'items/items_documentos/select_items_documentos.php',
                    scripts : true,
                    nocache : true,
                    params  : { id : id }
                },
            }).show();
        }

    </script>
<?php } ?>