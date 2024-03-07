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
			$grilla->GrillaName	 		= 'certificado_ingreso_retenciones_empleados_secciones';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'certificado_ingreso_retenciones_empleados_secciones';		//NOMBRE DE LA TABLA EN LA BASE DE DATOS
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
			$grilla->AddRow('Nombre','nombre',200);
			$grilla->AddRow('Campo Total','nombre_total',190);
			$grilla->AddRowImage('Filas','<center><img src="img/config16.png" style="cursor:pointer" width="16" height="16" title="Columnas del Formato" onclick="ventana_conceptos(\'[id]\',\'[codigo]\')"></center>',40);

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
			$grilla->VBotonNText		= 'Nueva Seccion'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'add_new';			//IMAGEN CSS DEL BOTON
			$grilla->AddBotton('Regresar','regresar','Win_Panel_Global.close();');
			$grilla->VAutoResize		= 'true';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		= 330;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		= 210;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		= 'true';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

		//CONFIGURACION DE LOS FORMULARIOS DE CAPTURA Y EDICION
			// $grilla->AddSeparator('Datos Centro De Costos');
			// $grilla->AddValidation('codigo_centro_costos','numero');
			$grilla->AddSeparator('Informacion de la seccion');
			$grilla->AddTextField('Nombre:','nombre',150,'true','false');
			$grilla->AddTextField('Nombre Campo total:','nombre_total',150,'false','false');
			$grilla->AddTextField('Codigo Campo total:','codigo_total',150,'false','false');
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

		function ventana_conceptos(id){

			Win_Ventana_conceptos = new Ext.Window({
			    width       : 600,
			    height      : 500,
			    id          : 'Win_Ventana_conceptos',
			    title       : 'Filas de la Seccion ',
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : 'certificado_ingreso_retenciones_empleados/filas_secciones.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
						id_seccion : id,
			        }
			    }

			}).show();
		}

		function ventana_columnas(id,codigo){

			Win_Ventana_columnas = new Ext.Window({
			    width       : 650,
			    height      : 600,
			    id          : 'Win_Ventana_columnas',
			    title       : 'Columnas del formato '+codigo,
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : 'certificado_ingreso_retenciones_empleados/columnas_formatos.php',
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

		function cargar_formatos_basicos() {
			MyLoading2('on');

			Ext.Ajax.request({
			    url     : 'certificado_ingreso_retenciones_empleados_secciones/bd/bd.php',
			    params  :
			    {
					opc : 'cargar_formatos_basicos',
			    },
			    success :function (result, request){
		    				var resul = result.responseText;

			                if(resul.split('{.}')[1] == 'true'){
			                	MyLoading2('off',{texto:'Formatos cargados Correctamente' });
			                	MyBusquedacertificado_ingreso_retenciones_empleados_secciones();
			                }
			                else{
		                		MyLoading2('off',{icono:'fail',texto:'No se cargaron los formatos<br>intentelo de nuevo',duracion:3000 });
		                		console.log(resul.split('{.}')[1]);
		                		console.log(resul.split('{.}')[2]);
		                		// document.getElementById('ContenedorPrincipal_certificado_ingreso_retenciones_empleados_secciones').innerHTML=resul.split('{.}')[1];
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