<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $filtro_sucursal;
	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'configuracionCajasPos';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'ventas_pos_cajas';			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_empresa = '$id_empresa' AND id_sucursal=$id_sucursal";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			// $grilla->OrderBy			= 'digitos ASC';
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA

		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 	= 'true';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 		= 895;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 		= 355;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->QuitarAncho = 530;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			// $grilla->QuitarAlto  = 570;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'

		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'nombre,consecutivo_caja';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			// $grilla->AddRowImage('Nombre','<left><div>[nombre]</div><div style="display:none;" id="id_resolucion_[id]">[id_resolucion]</div></left>','70');
			$grilla->AddRow('Nombre','nombre',100);
			$grilla->AddRow('Cons. Caja','consecutivo_caja',100);
			$grilla->AddRowImage('Estado','<center><div><img src="configuracion_pos/img/[estado].png" style="cursor:pointer;"></div><div style="display:none;" id="estado_[id]">[estado]</div></center>','50');

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 300;
			$grilla->FColumnaGeneralAncho	= 290;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 60;
			$grilla->FColumnaFieldAncho		= 200;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Configuracion'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'false';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'false';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		= ''; 				//TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= '';				//IMAGEN CSS DEL BOTON
			// $grilla->AddBotton('Consecutivos','opciones','ventanaConfigurarNumero()');
			// $grilla->AddBotton('Certificados','opciones','ventanaConfigurarNumero()');
			$grilla->VAutoResize		= 'true';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		= 310;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		= 140;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		= 'false';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

		//CONFIGURACION DE LO FORMULARIOS DE CAPTURA Y EDICION
			$grilla->AddTextField('Nombre','nombre',200,'true','false');

		// $grilla->AddMenuContext('Imprimir Codigo de Barras','barcode16','ventana_codigo_barras([id])');
		// $grilla->AddMenuContext('Configurar Cantidades Stock','config16','ventanaConfigurarInventario([id])');
		// $grilla->AddMenuContext('Agregar al Inventario','auto_back16','ventana_entrada_salida([id],"entrada",[id_item],"[nombre_equipo]",[cantidad])');
		// $grilla->AddMenuContext('Sacar del inventario','auto_go16','ventana_entrada_salida([id],"salida",[id_item],"[nombre_equipo]",[cantidad])');


	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$grilla->Link = $link;  	//Conexion a la BD			/**/
	/**/	$grilla->inicializa($_POST);//variables POST			/**/
	/**/	$grilla->GeneraGrilla(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/

if(!isset($opcion)){ ?>
	<script>
		<?php if(!isset($VBarraBotones)){ ?>

			// var toolbar = Ext.getCmp('ToolBar_configuracionCajasPos').getTopToolbar();
			// toolbar.add({
			// 	xtype   : 'buttongroup',
			// 	columns : 3,
			// 	title   : 'Descargar',
			// 	items   :
			// 	[
			// 		{
			// 			text      : 'Certificados',
			// 			scale     : 'large',
			// 			iconCls   : 'genera_informe',
			// 			iconAlign : 'top',
			// 			handler   : function(){ descargarCertificados(); }
			// 		}
			// 	]
			// });
			// toolbar.doLayout();

		<?php } ?>

		// function descargarCertificados() { window.open('../../../ARCHIVOS_PROPIOS/archivos_erp/qz-free certs.zip'); }

		function ventanaConfigurarNumero() {

			Win_Ventana_configurar_numero = new Ext.Window({
			    width       : 250,
			    height      : 200,
			    id          : 'Win_Ventana_configurar_numero',
			    title       : 'Configuracion Consecutivos',
			    modal       : true,
			    autoScroll  : false,
			    closable    : true,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : 'configuracion_pos/bd/bd.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
			            opc : 'cargarVentanaConfiguracionConsecutivos',
						filtro_sucursal : '<?php echo $filtro_sucursal ?>'
			        }
			    },
			    tbar        :
			    [
	            	{
	                    xtype       : 'button',
	                    width       : 60,
	                    height      : 56,
	                    text        : 'Guardar',
	                    scale       : 'large',
	                    iconCls     : 'guardar',
	                    iconAlign   : 'top',
	                    handler     : function(){ guardarNumeroPos() }
	                },'-'
			    ]
			}).show();
		}

		function guardarNumeroPos() {
			Ext.get('rederLoad').load({
				url     : 'configuracion_pos/bd/bd.php',
				scripts : true,
				nocache : true,
				params  :
				{
					opc    : 'guardarNumeroPos',
					numero : document.getElementById('numeroPos').value,
					filtro_sucursal : '<?php echo $filtro_sucursal ?>'
				}
			});
		}

		function validaNumero(input) {
			numero = input.value;
			patron = /[^\d]/g;
            if(patron.test(numero)){
                numero      = numero.replace(patron,'');
                input.value = numero;
            }
		}


		function Editar_configuracionCajasPos(id){
			title  = '';
			caja   = document.getElementById('div_configuracionCajasPos_consecutivo_caja_'+id).innerHTML;
			estado = document.getElementById('estado_'+id).innerHTML;

			if (estado=='true') {title=' - Habilitada';}
			else if (estado=='disabled') {title=' - Deshabilitada';}
			else if (estado=='block') {title=' - Bloqueada';}
			else if (estado=='changed'){ title= ' - Caja aun no asignada! ' }

			Win_Ventana_consecutivos = new Ext.Window({
			    width       : 700,
			    height      : 448,
			    id          : 'Win_Ventana_consecutivos',
			    title       : 'Configuracion Caja : '+caja+' '+title,
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : 'configuracion_pos/grilla_configuracion_consecutivos.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
						caja   : caja,
						id     : id,
						estado : estado,
						filtro_sucursal : '<?php echo $filtro_sucursal ?>'
			        }
			    }
			}).show();
		}


    </script>

<?php } ?>