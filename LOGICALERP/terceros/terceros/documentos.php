<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");

	// VERIFICAR EL TAMAÑO DISPONIBLE PARA LA SESION DE ESA EMPRESA
	$size = getFolderSize($_SESSION['ID_HOST'],'../../../../');
	$porcentaje = $size*100/$_SESSION['ALMACENAMIENTO'];
	$proporcion = 400*$porcentaje/100;

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'TercerosDocumentos';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'terceros_documentos';		//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_tercero =$elid";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize = 'true';
			// $grilla->Ancho		 		= 780;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			//$grilla->Alto		 		= 500;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->QuitarAncho		= 140;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 310;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'nombre,fecha_creacion';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('tipo','tipo_documento_nombre',100);
			$grilla->AddRow('nombre','documento',150);
			$grilla->AddRow('Fecha Creacion','fecha_creacion',230,'fecha');
			$grilla->AddRowImage('','<center><div style="float:left; margin: 0 0 0 7px"><img src="../../temas/clasico/images/BotonesTabs/buscar16.png?" style="cursor:pointer" width="16" height="16" onclick="ver_documento_terceros([id],\'[nombre]\',\'[ext]\');"></div></center>',30);
			$grilla->AddRowImage('','<center><div style="float:left; margin: 0 0 0 7px"><img src="../../temas/clasico/images/BotonesTabs/delete.png?" style="cursor:pointer" width="16" height="16" onclick="eliminar_documento([id],\'[nombre]\',\'[ext]\');"></div></center>',30);

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Ventana Documentos'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'true';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		= 'Agregar Documento'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'documentadd';			//IMAGEN CSS DEL BOTON
			//$grilla->VAutoResize		= 'false';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			//$grilla->VAncho		 	= 400;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			//$grilla->VAlto		 	= 300;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			//$grilla->VQuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			//$grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			//$grilla->VAutoScroll		= 'false';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			//$grilla->VBotonEliminar	= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			//$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

		//CONFIGURACION DE LO FORMULARIOS DE CAPTURA Y EDICION

		//CONFIGURACION DEL MENU CONTEXTUAL
 			// $grilla->MenuContext		= 'true';
	 		// $grilla->MenuContextEliminar= 'true';

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$grilla->Link = $link;  	//Conexion a la BD			/**/
	/**/	$grilla->inicializa($_POST);//variables POST			/**/
	/**/	$grilla->GeneraGrilla(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/

if(!isset($opcion)){?>
	<script>

		function ver_documento_terceros(id,nombre,ext){

			if(ext!='bmp' && ext!='BMP' && ext!='jpg' && ext!='JPG' && ext!='png' && ext!='PNG' && ext!='gif' && ext!='GIF' && ext!='pdf' && ext!='PDF'){
				// window.open('../../../ARCHIVOS_PROPIOS/documentos_tercero/'+nombre+'_'+id+'.'+ext);
				window.location.href='.../../../../../ARCHIVOS_PROPIOS/empresa_<?php echo $_SESSION[ID_HOST]; ?>/terceros/'+nombre+'_'+id+'.'+ext;
			}
			else{
				if(ext=='pdf'){ viewDocumentoTerceros(id,nombre,ext,Ext.getBody().getWidth()-50,Ext.getBody().getHeight()-50); return; }
				else{
					Ext.Ajax.request({
						url		: "../terceros/bd/bd.php",
						success	: function(response){
									response  = response.responseText;
									response  = JSON.parse(response);
									var alto  = response.alto
									,	ancho = response.ancho;

									if(response.alto<96){ alto=96; }
									else if(response.alto>Ext.getBody().getHeight()-170){ alto = Ext.getBody().getHeight()-170; }
									else{ alto += 10; }

									if(response.ancho<96){ ancho=96; }
									else if(response.ancho>Ext.getBody().getWidth()-120){ ancho = Ext.getBody().getWidth()-120; }
									else{ ancho += 10; }

									alto  += 100;
									ancho += 70;

									viewDocumentoTerceros(id,nombre,ext,ancho,alto);
								  },
						params	:
						{
							op     : 'consultaSizeImageDocumentTerceros',
							nombre : nombre+'_'+id+'.'+ext
						}
					});
				}
			}
		}

		function viewDocumentoTerceros(id,nombre,ext,ancho,alto){

			var titulo    = document.getElementById('div_TercerosDocumentos_tipo_documento_nombre_'+id).innerHTML;
			var documento = document.getElementById('div_TercerosDocumentos_documento_'+id).innerHTML;

			Win_Ventana_VerDocumento_Terceros = new Ext.Window({
				width		: ancho,
				height		: alto,
				id			: 'Win_Ventana_VerDocumento_Terceros',
				title		: titulo,
				modal		: true,
				autoScroll	: true,
				closable	: true,
				autoDestroy : true,
				autoLoad	:
				{
					url		: '../terceros/bd/bd.php',
					scripts	: true,
					nocache	: true,
					params	:
					{
						op              : 'ventanaVerImagenDocumentoTerceros',
						nombreImage     : nombre+'_'+id+'.'+ext,
						nombreDocumento : documento,
						type            : ext
					}
				},
				tbar		:
				[
					{
						xtype     : 'button',
						text      : 'Regresar',
						scale     : 'large',
						iconCls   : 'regresar',
						iconAlign : 'left',
						handler   : function(){ Win_Ventana_VerDocumento_Terceros.close(); }
					}
				]
			}).show();
		}

		function eliminar_documento(id,nombre,ext){
            if(confirm('Esta seguro que desea cotinuar!')){
                Elimina_Div_TercerosDocumentos(id);
                Ext.Ajax.request({
                    url     : "bd/bd.php",
                    params  :
                    {
						op        : 'eliminar_archivo',
						idArchivo : id,
						fileName  : nombre+'_'+id+'.'+ext
                    },
                    success :function (result, request){
                                var responseAjax = result.responseText;
                                if(responseAjax != 'true'){ alert("Aviso\nHa ocurrido un problema con la conexion de la base de datos!"); }
                            },
                    failure : function(){ alert("Aviso\nHa ocurrido un problema con la conexion al servidor!"); }
                });
            }
        }

		function Editar_TercerosDocumentos (id){}

		function Agregar_TercerosDocumentos (id){
			Win_Ventana_SelectTipoTercerosDocumentos = new Ext.Window({
				width		: 330,
				id			: 'Win_Ventana_SelectTipoTercerosDocumentos',
				height		: 150,
				title		: 'Seleccionar Tipo Documento',
				modal		: true,
				autoScroll	: false,
				closable	: true,
				autoDestroy : true,
				autoLoad	:
				{
					url		: '../terceros/bd/bd.php',
					scripts	: true,
					nocache	: true,
					params	: { op : 'OptionSelectTerceroDocumentos' }
				},
				tbar		:
				[
					{
						xtype     : 'button',
						text      : 'Siguiente',
						scale     : 'large',
						iconCls   : 'siguiente',
						iconAlign : 'left',
						handler   : function(){ CargarImagenDocumentoTercero(id); }
					}
				]
			}).show();
		}

		function CargarImagenDocumentoTercero(id){
			var id_select_tipo_documento = document.getElementById('Terceros_id_documento').value;
			if(id_select_tipo_documento == 0){
				alert('Seleccione el tipo de Documento!');
			}
			else{
				indice_tipo_documento = document.getElementById('Terceros_id_documento').selectedIndex;
				texto_tipo_documento  = document.getElementById('Terceros_id_documento').options[indice_tipo_documento].text;
				value_tipo_documento  = document.getElementById('Terceros_id_documento').selectedIndex;

				Win_Ventana_SelectTipoTercerosDocumentos.close(id);
				Win_Ventana_Agregar_TercerosDocumentos = new Ext.Window({
					width		: 330,
					height		: 220,
					id			: 'Win_Ventana_Agregar_TercerosDocumentos',
					title		: 'Agregar Documento &nbsp;&nbsp;-'+texto_tipo_documento,
					modal		: true,
					autoScroll	: false,
					closable	: true,
					autoDestroy : true,
					autoLoad	:
					{
						url		: '../terceros/terceros/upload_documento_terceros.php',
						scripts	: true,
						nocache	: true,
						params	:
						{
							id 	: '<?php echo $elid; ?>',
							td 	: value_tipo_documento
						}
					}
				}).show();
			}
		}

	</script>
<?php } ?>