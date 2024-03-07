<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");

  /**//////////////////////////////////////////////**/
  /**///       INICIALIZACION DE LA CLASE       ///**/
  /**/                                            /**/
  /**/    			$grilla = new MyGrilla();         /**/
  /**/                                            /**/
  /**//////////////////////////////////////////////**/

  $id_empresa  = $_SESSION["EMPRESA"];
  $id_empleado = $_SESSION['IDUSUARIO'];

  $id_recibo_caja = $id_documento;

  //CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //NOMBRE DE LA GRILLA
      $grilla->GrillaName         	= 'reciboCajaDocumentos';     			//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
    //QUERY
      $grilla->TableName          	= 'ventas_recibo_caja_documentos';  //NOMBRE DE LA TABLA EN LA BASE DE DATOS
      $grilla->MyWhere            	= "activo = 1 AND id_recibo_caja = '$id_recibo_caja'";      //WHERE DE LA CONSULTA A LA TABLA "$TableName"
      $grilla->MySqlLimit         	= '0,100';          								//LIMITE DE LA CONSULTA
    //TAMANO DE LA GRILLA
      $grilla->AutoResize         	= 'false';          								//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
      $grilla->Ancho              	= 510;              								//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
      $grilla->Alto               	= 370;              								//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
    //TOOLBAR Y CAMPO DE BUSQUEDA
      $grilla->Gtoolbar           	= 'true';           								//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
      $grilla->CamposBusqueda     	= 'nombre,ext';     								//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
    //CONFIGURACION DE CAMPOS EN LA GRILLA
      $grilla->AddRow('Documento','nombre',150);
      $grilla->AddRow('Extension','ext',70);
      $grilla->AddRowImage('', '<center><div id="renderDeleteDocumentoReciboCaja_[id]" style="float:left; max-width:20px; overflow:hidden;"></div><img src="img/delete.png" style="cursor:pointer" style="width:16px; height:16px;" onclick="deleteDocumentoReciboCaja([id],\'[nombre]\',\'[ext]\');" /></center>','20');
      $grilla->AddRowImage('Ver','<center><img src="img/ver.png" style="cursor:pointer" style="width:16px; height:16px;" onclick="viewDocumentoReciboCaja([id],\'[nombre]\',\'[ext]\');" /></center>','25');
    //CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
      $grilla->VentanaAuto        	= 'false';           								//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
      $grilla->TituloVentana      	= 'Subir Documento'; 								//NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
      $grilla->VBarraBotones      	= 'false';           								//SI HAY O NO BARRA DE BOTONES
      $grilla->VBotonNuevo        	= 'true';           								//SI LLEVA EL BOTON DE AGREGAR REGISTRO
      $grilla->VBotonNText        	= 'Nuevo Documento'; 								//TEXTO DEL BOTON DE NUEVO REGISTRO
      $grilla->VBotonNImage       	= 'add';            								//IMAGEN CSS DEL BOTON
      $grilla->VAutoResize        	= 'true';           								//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
      $grilla->VQuitarAncho       	= 120;               								//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
      $grilla->VQuitarAlto        	= 160;              								//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
      $grilla->VAutoScroll        	= 'false';          								//SI LA VENTANA TIENE O NO AUTOSCROLL
      $grilla->VBotonEliminar     	= 'false';           								//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
      $grilla->VComporEliminar    	= 'false';           								//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)
    //CONFIGURACION DEL MENU CONTEXTUAL
      $grilla->MenuContext        	= 'false';
      $grilla->MenuContextEliminar	= 'true';
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

  /**//////////////////////////////////////////////////////////////**/
  /**///              INICIALIZACION DE LA GRILLA               ///**/
  /**/                                                            /**/
  /**/    $grilla->Link = $link;      //Conexion a la BD          /**/
  /**/    $grilla->inicializa($_POST);//Variables POST            /**/
  /**/    $grilla->GeneraGrilla();    //Inicializa la Grilla     /**/
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
	    function deleteDocumentoReciboCaja(id_documento,nombre,ext){
	        if(!confirm("Aviso,\nEsta seguro de eliminar un documento cargado?")) return;

	        Ext.get('renderDeleteDocumentoReciboCaja_'+id_documento).load({
	            url     : 'recibo_caja/bd/bd.php',
	            scripts : true,
	            nocache : true,
	            params  :
	            {
	                opc         : 'deleteDocumentoReciboCaja',
	                idDocumento : id_documento,
	                nombre      : nombre,
	                ext         : ext,
	            }
	        });
	    }

			function createUploader(){
	      var uploader = new qq.FileUploader({
          element 					: document.getElementById('div_upload_file'),
          action  					: 'recibo_caja/upload_file/upload_file.php',
          debug   					: false,
          params  					: {
																id_documento 	: '<?php echo $id_recibo_caja; ?>',
																empresa  			: '<?php echo $id_empresa; ?>'
															},
          button            : null,
          multiple          : false,
          maxConnections    : 3,
          allowedExtensions : [
																'xls',
																'xlsx',
																'csv',
																'doc',
																'docx',
																'bmp',
																'jpeg',
																'jpg',
																'png',
																'pdf',
																'txt'
															],
          sizeLimit         : 10 * 1024 * 1024,
          minSizeLimit      : 0,
          onSubmit          : function(id, fileName){},
          onProgress        : function(id, fileName, loaded, total){},
          onComplete        : function(id, fileName, responseJSON){
                                    document.getElementById('div_upload_file').querySelector('.qq-upload-list').innerHTML='';
                                    document.getElementById('divPadreModalUploadFile').setAttribute('style','');
                                    if(responseJSON.idInsert > 0){
																			Inserta_Div_reciboCajaDocumentos(responseJSON.idInsert);
																		}
                              },
          onCancel 					: function(fileName){},
          messages 					: {
									              typeError    : "{file}\nArchivo no permitido.\n\n Solo se permiten los siguientes archivo:\n\n'jpg', 'bmp', 'pdf','xls','doc'",
									              sizeError    : "\"{file}\"  Archivo muy grande, Tamano Maximo Permitido ( {sizeLimit} ).",
									              minSizeError : "{file} is too small, minimum file size is {minSizeLimit}.",
									              emptyError   : "{file} is empty, please select files again without it.",
									              onLeave      : "Cargando Archivo."
									            }
	      });
		  }

			createUploader();

	    function viewDocumentoReciboCaja(id,nameFile,ext){

	          if(!validaImagen(ext)){
	            window.open("recibo_caja/bd/bd.php?opc=downloadFile&nameFile="+nameFile+"&ext="+ext+"&id="+id);
	          } else{
	            var ancho = Ext.getBody().getWidth()-50;
	            var alto  = Ext.getBody().getHeight()-50;

	            if(ext=='pdf'){
								ventanaViewDocumento(id,nameFile,ext,ancho,alto); return;
							} else{
	              Ext.Ajax.request({
	                url     : "recibo_caja/bd/bd.php",
	                success : function(response){
	                            response    = response.responseText;
	                            response    = JSON.parse(response);
	                            var   alto  = response.alto
	                            ,     ancho = response.ancho
	                            ,  fileName = response.file;

	                            if(response.alto<96){
																alto=96;
															} else if(response.alto > Ext.getBody().getHeight()-170){
																alto = Ext.getBody().getHeight()-170;
															} else{
																alto += 10;
															}

	                            if(response.ancho<96){
																ancho=96;
															} else if(response.ancho > Ext.getBody().getWidth()-120){
																ancho = Ext.getBody().getWidth()-120;
															} else{
																ancho += 10;
															}

	                            alto  += 22;
	                            ancho += 10;

	                            ventanaViewDocumento(id,nameFile,ext,ancho,alto);
	                        	},
	                params  : {
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
	          var titulo = (document.getElementById('div_reciboCajaDocumentos_nombre_'+id).innerHTML)+'.'+(document.getElementById('div_reciboCajaDocumentos_ext_'+id).innerHTML);

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
	                  url     : 'recibo_caja/bd/bd.php',
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
