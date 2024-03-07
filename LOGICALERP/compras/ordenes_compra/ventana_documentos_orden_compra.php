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

    $id_empresa  = $_SESSION["EMPRESA"];
    $id_empleado = $_SESSION['IDUSUARIO'];

    $sql_select_id   = "SELECT id FROM compras_ordenes WHERE consecutivo = '$consecutivo' AND id_empresa = '$id_empresa'";
    $query_id        = mysql_query($sql_select_id);
    $id_orden_compra = mysql_result($query_id,0,'id');

    //CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        //NOMBRE DE LA GRILLA
            $grilla->GrillaName         = 'ordenesCompraDocumentos';     //NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
        //QUERY
            $grilla->TableName          = 'compras_ordenes_documentos';       //NOMBRE DE LA TABLA EN LA BASE DE DATOS
            $grilla->MyWhere            = "activo=1 AND id_orden_compra='$id_orden_compra'";     //WHERE DE LA CONSULTA A LA TABLA "$TableName"
            $grilla->MySqlLimit         = '0,100';          //LIMITE DE LA CONSULTA
        //TAMANO DE LA GRILLA
            $grilla->AutoResize         = 'false';               //SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
            $grilla->Ancho              = 510;              //ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
            $grilla->Alto               = 370;              //ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
            // $grilla->QuitarAncho        = 105;               //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
            // $grilla->QuitarAlto         = 290;              //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
        //TOOLBAR Y CAMPO DE BUSQUEDA
            $grilla->Gtoolbar           = 'true';           //SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
            $grilla->CamposBusqueda     = 'nombre,ext';     //VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
           // $grilla->DivActualiBusqueda = 'nombre,ext';                //VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA

        //CONFIGURACION DE CAMPOS EN LA GRILLA
            $grilla->AddRow('Documento','nombre',150);
            $grilla->AddRow('Extension','ext',70);
            $grilla->AddRowImage('','<center><div id="renderDeleteDocumentoOrden_[id]" style="float:left; max-width:20px; overflow:hidden;"></div>
                                    <img src="img/delete.png" style="cursor:pointer" style="width:16px; height:16px;" onclick="deleteDocumentoOrden([id],\'[nombre]\',\'[ext]\');" /></center>','20');
            $grilla->AddRowImage('Ver','<center><img src="img/ver.png" style="cursor:pointer" style="width:16px; height:16px;" onclick="viewDocumentoOrden([id],\'[nombre]\',\'[ext]\');" /></center>','25');

        //CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
            $grilla->VentanaAuto        = 'false';           //SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
            $grilla->TituloVentana      = 'Subir Documento'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
            $grilla->VBarraBotones      = 'false';           //SI HAY O NO BARRA DE BOTONES
            $grilla->VBotonNuevo        = 'true';           //SI LLEVA EL BOTON DE AGREGAR REGISTRO
            $grilla->VBotonNText        = 'Nuevo Documento'; //TEXTO DEL BOTON DE NUEVO REGISTRO
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

        //CONFIGURACION DE LOS FORMULARIOS DE CAPTURA Y EDICION



    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    /**//////////////////////////////////////////////////////////////**/
    /**///              INICIALIZACION DE LA GRILLA               ///**/
    /**/                                                            /**/
    /**/    $grilla->Link = $link;      //Conexion a la BD          /**/
    /**/    $grilla->inicializa($_POST);//variables POST            /**/
    /**/    $grilla->GeneraGrilla();    // Inicializa la Grilla     /**/
    /**/                                                            /**/
    /**//////////////////////////////////////////////////////////////**/

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
    <script>

        function Editar_ordenesCompraDocumentos(){}

        function deleteDocumentoOrden(id_documento,nombre,ext){
            if(!confirm("Aviso,\nEsta seguro de eliminar un documento cargado?")) return;

            Ext.get('renderDeleteDocumentoOrden_'+id_documento).load({
                url     : 'ordenes_compra/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc         : 'deleteDocumentoOrden',
                    idDocumento : id_documento,
                    nombre      : nombre,
                    ext         : ext,
                }
            });
        }

    	//====================================// UPLOAD FILE NOTA CONTABLE //====================================//
		//*******************************************************************************************************//
		function createUploader(){

	        var uploader = new qq.FileUploader({
	            element : document.getElementById('div_upload_file'),
	            action  : 'ordenes_compra/upload_file/upload_file.php',
	            debug   : false,
	            params  : { consecutivo : '<?php echo $consecutivo; ?>',empresa  : '<?php echo $id_empresa; ?>' },
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


                                        if(responseJSON.idInsert > 0){ Inserta_Div_ordenesCompraDocumentos(responseJSON.idInsert); }
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

        function viewDocumentoOrden(id,nameFile,ext){

            if(!validaImagen(ext)){
                window.open("ordenes_compra/bd/bd.php?opc=downloadFile&nameFile="+nameFile+"&ext="+ext+"&id="+id);
                // window.location.href=ruta+randomico_documento+'_'+id+'.'+ext;
            }
            else{
                var ancho = Ext.getBody().getWidth()-50;
                var alto  = Ext.getBody().getHeight()-50;

                if(ext=='pdf'){  ventanaViewDocumento(id,nameFile,ext,ancho,alto); return; }
                else{

                    Ext.Ajax.request({
                        url     : "ordenes_compra/bd/bd.php",
                        success : function(response){
                                    response  = response.responseText;
                                    response  = JSON.parse(response);
                                    var alto  = response.alto
                                    ,   ancho = response.ancho
                                    ,   fileName = response.file;

                                    if(response.alto<96){ alto=96; }
                                    else if(response.alto > Ext.getBody().getHeight()-170){ alto = Ext.getBody().getHeight()-170; }
                                    else{ alto += 10; }

                                    if(response.ancho<96){ ancho=96; }
                                    else if(response.ancho > Ext.getBody().getWidth()-120){ ancho = Ext.getBody().getWidth()-120; }
                                    else{ ancho += 10; }

                                    alto  += 22;
                                    ancho += 10;

                                    ventanaViewDocumento(id,nameFile,ext,ancho,alto);
                                },
                        params  : //parametros que se le envia a la funcion consultaSizeImageDocumentEmpleado() ubicada en EJERCITO/personal/bd/bd.php
                        {
                            opc      : 'consultaSizeDocumento',
                            nameFile : nameFile,
                            ext      : ext,
                            id       : id,
                        }
                    });
                }
            }
        }

        function ventanaViewDocumento(id,nameFile,ext,width,height){
            // console.log('alto:'+height+' ancho:'+width)
            var titulo = (document.getElementById('div_ordenesCompraDocumentos_nombre_'+id).innerHTML)+'.'+(document.getElementById('div_ordenesCompraDocumentos_ext_'+id).innerHTML);

            Win_Ventana_View_documento_upload = new Ext.Window({
                width       : width,
                height      : height,
                id          : 'Win_Ventana_View_documento_upload',
                title       : titulo,
                modal       : true,
                autoScroll  : true,
                closable    : true,
                autoDestroy : true,
                autoLoad    :
                {
                    url     : 'ordenes_compra/bd/bd.php',
                    scripts : true,
                    nocache : true,
                    params  :
                    {
                        opc      : 'ventanaViewDocumento',
                        nameFile : nameFile,
                        ext      : ext,
                        id       : id,
                    }
                }
            }).show();
        }

        function validaImagen(ext){
            var arrayTest = Array('bmp','jpg','png','gif','pdf','BMP','JPG','PNG','GIF','PDF');

            for(i=0; i<arrayTest.length; i++){ if(arrayTest[i]==ext){ return true; } }
            return false;
        }
    </script>
<?php
} ?>
