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
			$grilla->GrillaName	 		= 'informes_niif_formatos';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'informes_niif_formatos';		//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_empresa='$id_empresa'";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->OrderBy			= '';
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'false';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 		= 560;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 		= 365;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			//$grilla->QuitarAncho		= 150;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			//$grilla->QuitarAlto		= 265;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'descripcion';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Codigo','codigo',80);
			$grilla->AddRow('Nombre','nombre',250);
			$grilla->AddRowImage('Config.','<center><img src="img/informe0.png" style="cursor:pointer" width="16" height="16" title="Configuracion del Formato" onclick="ventana_secciones(\'[id]\',\'[codigo]\',\'[nombre]\')"></center>',56);


		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 270;
			$grilla->FColumnaGeneralAncho	= 270;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 110;
			$grilla->FColumnaFieldAncho		= 150;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'true';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Ventana Grupos Conceptos'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'true';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		= 'Nuevo Formato'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'add_new';			//IMAGEN CSS DEL BOTON
			$grilla->AddBotton('Cargar Formatos <br>Standart','Copier','cargar_formatos_basicos();');
			$grilla->AddBotton('Regresar','regresar','Win_Panel_Global.close();');
			$grilla->VAutoResize		= 'true';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		= 330;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		= 350;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		= 'true';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

		//CONFIGURACION DE LOS FORMULARIOS DE CAPTURA Y EDICION
			// $grilla->AddSeparator('Datos Centro De Costos');
			// $grilla->AddValidation('codigo_centro_costos','numero');
			$grilla->AddSeparator('Informacion del Formato');
			$grilla->AddTextField('Codigo:','codigo',150,'true','false');
			$grilla->AddTextField('Nombre:','nombre',150,'true','false');
            $grilla->AddValidation('codigo','numero');
            $grilla->AddValidation('nombre','mayuscula');

            $grilla->AddSeparator('Informacion de filtros');
			// $grilla->AddComboBox ('Por Terceros:','filtro_terceros',150,'true','false','Si:Si,No:No');
			// $grilla->AddComboBox ('Por Centro de costos:','filtro_ccos',150,'true','false','Si:Si,No:No');
			$grilla->AddComboBox ('Corte Anual:','filtro_corte_anual',150,'true','false','Si:Si,No:No');
			$grilla->AddComboBox ('Rango de fechas:','filtro_rango_fechas',150,'true','false','Si:Si,No:No');
			$grilla->AddComboBox ('Rango de Cuentas:','filtro_cuentas',150,'true','false','Si:Si,No:No');

			$grilla->AddTextField('','id_empresa',200,'true','true',$id_empresa);


	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$grilla->Link = $link;  	//Conexion a la BD			/**/
	/**/	$grilla->inicializa($_POST);//variables POST			/**/
	/**/	$grilla->GeneraGrilla(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/

if(!isset($opcion)){  ?>
	<script>

		function ventana_conceptos(id,codigo){

			Win_Ventana_conceptos = new Ext.Window({
			    width       : 650,
			    height      : 600,
			    id          : 'Win_Ventana_conceptos',
			    title       : 'Conceptos del formato '+codigo,
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : 'configuracion_informes_niif/conceptos_formatos.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
						id_formato : id,
						codigo     : codigo,
			        }
			    }

			}).show();
		}

		function ventana_secciones(id,codigo,nombre){
			var myalto  = Ext.getBody().getHeight();
			var myancho = Ext.getBody().getWidth();

			Win_Ventana_secciones = new Ext.Window({
			    width       : myancho-100,
			    height      : myalto-50,
			    id          : 'Win_Ventana_secciones',
			    title       : 'Secciones del formato '+codigo,
			    modal       : true,
			    autoScroll  : false,
			    closable    : true,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : 'configuracion_informes_niif/formatos_secciones.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
						id_formato : id,
						codigo     : codigo,
						nombre     : nombre,
			        }
			    },
			    tbar        :
			    [
			        {
			            xtype   : 'buttongroup',
			            columns : 3,
			            title   : 'Opciones',
			            style   : 'border-right:none;',
			            items   :
			            [
			                {
			                    xtype       : 'button',
			                    width       : 60,
			                    height      : 56,
			                    text        : 'Nueva Seccion',
			                    scale       : 'large',
			                    iconCls     : 'doc_secciones',
			                    iconAlign   : 'top',
			                    hidden      : false,
			                    handler     : function(){ BloqBtn(this); agregarModificarSeccion(); }
			                },
			                {
			                    xtype       : 'button',
			                    width       : 60,
			                    height      : 56,
			                    text        : 'Nueva Fila ',
			                    scale       : 'large',
			                    iconCls     : 'doc_filas',
			                    iconAlign   : 'top',
			                    hidden      : false,
			                    handler     : function(){ BloqBtn(this); agregarModificarFila() }
			                },
			                {
			                    xtype       : 'button',
			                    width       : 60,
			                    height      : 56,
			                    text        : 'Regresar',
			                    scale       : 'large',
			                    iconCls     : 'regresar',
			                    iconAlign   : 'top',
			                    hidden      : false,
			                    handler     : function(){ BloqBtn(this); Win_Ventana_secciones.close(id) }
			                }
			            ]
			        }
			    ]

			}).show();
		}

		function cargar_formatos_basicos() {
			MyLoading2('on');

			Ext.Ajax.request({
			    url     : 'configuracion_informes_niif/bd/bd.php',
			    params  :
			    {
					opc : 'cargar_formatos_basicos',
			    },
			    success :function (result, request){
		    				// console.log(result);
		    				var resul = result.responseText;

			                if(resul.split('{.}')[1] == 'true'){
			                	MyLoading2('off',{texto:'Formatos cargados Correctamente' });
			                	MyBusquedainformes_niif_formatos();
			                }
			                else{
		                		MyLoading2('off',{icono:'fail',texto:'No se cargaron los formatos<br>intentelo de nuevo',duracion:3000 });
		                		console.log(resul.split('{.}')[1]);
		                		console.log(resul.split('{.}')[2]);
		                		// document.getElementById('ContenedorPrincipal_informes_niif_formatos').innerHTML=resul.split('{.}')[1];
			                }
			            },
			    failure : function(){
			    	MyLoading2('off',{icono:'fail',texto:'No se cargaron los formatos<br>intentelo de nuevo',duracion:3000 });
			    }
			});
		}


	</script>
<?php
}
if ($opcion=='Vupdate' || $opcion=='Vagregar') {
?>
	<script>

		// new Ext.form.DateField({
		//     format     : 'Y-m-d',
		//     width      : 150,
		//     allowBlank : false,
		//     showToday  : false,
		//     applyTo    : 'nominaGruposConceptos_fecha_inicio_vigencia',
		//     editable   : false,
		//     listeners  : { select: function() {   } }
		// });

		// new Ext.form.DateField({
		//     format     : 'Y-m-d',
		//     width      : 150,
		//     allowBlank : false,
		//     showToday  : false,
		//     applyTo    : 'nominaGruposConceptos_fecha_fin_vigencia',
		//     editable   : false,
		//     listeners  : { select: function() {   } }
		// });

	</script>
<?php
}
?>