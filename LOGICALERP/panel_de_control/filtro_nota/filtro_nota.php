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

	$id_empresa = $_SESSION['EMPRESA'];
	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'panelControlFiltroNota';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'tipo_nota_contable';			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_empresa = '$id_empresa'";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->OrderBy			= "id ASC";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA

		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'false';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 		= 560;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 		= 365;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->QuitarAncho		= 80;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			// $grilla->QuitarAlto		= 170;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'

		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'descripcion';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Codigo','codigo',50);
			$grilla->AddRow('Descripcion','descripcion',220);
			$grilla->AddRow('Consecutivo colgaap','consecutivo',120);
			$grilla->AddRow('Consecutivo Niif','consecutivo_niif',100);
			$grilla->AddRow('Documento cruce','documento_cruce',120);

			$grilla->AddColStyle('codigo','text-align:right; width:45px !important; margin-right:5px;');   //ALINEAR NUMEROS LA DERECHA

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho     = 300;
			$grilla->FColumnaGeneralAncho = 290;
			$grilla->FColumnaGeneralAlto  = 25;
			$grilla->FColumnaLabelAncho   = 60;
			$grilla->FColumnaFieldAncho   = 200;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto     = 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana   = 'Filtro Nota'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones   = 'true';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo     = 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText     = 'Nuevo Filtro Nota'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage    = 'add';			//IMAGEN CSS DEL BOTON
			$grilla->AddBottonVentana('Subgrupo','admincontactos','VentanaSubGrupo(elid);','true','true');
			$grilla->VAutoResize     = 'false';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho          = 310;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto           = 160;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho    = 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto     = 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll     = 'false';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar  = 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar = 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

		//CONFIGURACION DE LO FORMULARIOS DE CAPTURA Y EDICION
			$grilla->AddTextField('','id_empresa',200,'true','true',$id_empresa);
			$grilla->AddTextField('Descripcion','descripcion',200,'true','false');
			$grilla->AddValidation('descripcion','mayuscula');


	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$grilla->Link = $link;  	//Conexion a la BD			/**/
	/**/	$grilla->inicializa($_POST);//variables POST			/**/
	/**/	$grilla->GeneraGrilla(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/

if(!isset($opcion)){
?>

	<script>
		function Editar_panelControlFiltroNota(id){

			var titulo  = document.getElementById('div_panelControlFiltroNota_descripcion_'+id).innerHTML;

			Win_Ventana_Editar_panelControlFiltroNota = new Ext.Window({
				width		: 350,
				height		: 260,
				id			: 'Win_Ventana_Editar_panelControlFiltroNota',
				title		: titulo,
				modal		: true,
				autoScroll	: true,
				closable	: false,
				autoDestroy : true,
				autoLoad	:
				{
					url		: 'filtro_nota/bd/bd.php',
					scripts	: true,
					nocache	: true,
					params	:
					{
						op : 'ventanaUpdateFiltroNota',
						id : id
					}
				},
				tbar		:
				[
					{
						xtype   : 'buttongroup',
						title   : 'Opciones',
						columns : 6,
						items   :
						[
							{
								xtype		: 'button',
								id 			: 'btnGuardarItemPuc',
								width		: 80,
								text		: 'Actualizar',
								scale		: 'large',
								iconCls		: 'guardar',
								iconAlign	: 'top',
								// disabled	: true,
								handler 	: function(){ saveInsertUpdateFiltroNotaPanelControl(id); }
							},
							{
								xtype		: 'button',
								width		: 80,
								text		: 'Elimina',
								scale		: 'large',
								iconCls		: 'eliminar',
								iconAlign	: 'top',
								handler 	: function(){ saveEliminaFiltroNotaPanelControl(id); }
							},
							{
								xtype		: 'button',
								width		: 80,
								text		: 'Regresar',
								scale		: 'large',
								iconCls		: 'regresar',
								iconAlign	: 'top',
								handler 	: function(){ Win_Ventana_Editar_panelControlFiltroNota.close(); Actualiza_Div_panelControlFiltroNota(id); }
							}
						]
					}
				]
			}).show();
		}



		function Agregar_panelControlFiltroNota(){
			Win_Ventana_Agregar_panelControlFiltroNota = new Ext.Window({
				width		: 350,
				height		: 260,
				id			: 'Win_Ventana_Agregar_panelControlFiltroNota',
				title		: 'Nuevo Filtro Nota',
				modal		: true,
				autoScroll	: true,
				closable	: false,
				autoDestroy : true,
				autoLoad	:
				{
					url		: 'filtro_nota/bd/bd.php',
					scripts	: true,
					nocache	: true,
					params	: { op: 'ventanaInsertFiltroNota' }
				},
				tbar		:
				[
					{
						xtype   : 'buttongroup',
						title   : 'Opciones',
						columns : 6,
						items   :
						[
							{
								xtype		: 'button',
								id 			: 'btnGuardarItemPuc',
								width		: 80,
								text		: 'Guardar',
								scale		: 'large',
								iconCls		: 'guardar',
								iconAlign	: 'top',
								// disabled	: true,
								handler 	: function(){ saveInsertUpdateFiltroNotaPanelControl(0); }
							},
							{
								xtype		: 'button',
								width		: 80,
								text		: 'Regresar',
								scale		: 'large',
								iconCls		: 'regresar',
								iconAlign	: 'top',
								handler 	: function(){ Win_Ventana_Agregar_panelControlFiltroNota.close(); }
							}
						]
					}
				]
			}).show();
		}

		function saveInsertUpdateFiltroNotaPanelControl(id){
			var nombreNota      = document.getElementById('nombreFiltroNota').value
			,	consecutivoNota = document.getElementById('consecutivoFiltroNota').value
			,	consecutivoNotaNiif = document.getElementById('consecutivoFiltroNotaNiif').value
			,	documentoCruce  = document.getElementById('documentoCruceFiltroNota').value;

			nombreNota = nombreNota.replace(/[\#\<\>\'\"]/g, '');

			if(nombreNota == ''){ alert('El campo descripcion es obligatorio'); return; }
			else if(documentoCruce == ''){ alert('El campo Documento cruce es obligatorio'); return; }
			else if(isNaN(consecutivoNota) || consecutivoNota==0){ alert('El consecutivo de la nota debe ser numerico mayor a cero'); return; }

			op = (id > 0) ? 'saveUpdateFiltroNota': 'saveInsertFiltroNota';

			Ext.get('renderInserUpdateFiltroNota').load({
				url		: 'filtro_nota/bd/bd.php',
				timeout : 180000,
				scripts	: true,
				nocache	: true,
				params	:
				{
					op                  : op,
					id                  : id,
					nombreNota          : nombreNota,
					consecutivoNota     : consecutivoNota,
					consecutivoNotaNiif : consecutivoNotaNiif,
					documentoCruce      : documentoCruce
				}
			});
		}

		function saveEliminaFiltroNotaPanelControl(id){
			Ext.get('renderInserUpdateFiltroNota').load({
				url		: 'filtro_nota/bd/bd.php',
				timeout : 180000,
				scripts	: true,
				nocache	: true,
				params	:
				{
					op : 'eliminaFiltroNota',
					id : id
				}
			});
		}


    </script>

<?php }
else if ($opcion=='Vagregar' || $opcion=='Vupdate') {
?>


<?php
} ?>
