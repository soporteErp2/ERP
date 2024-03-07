<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");


	$id_empresa = $_SESSION['EMPRESA'];

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'nominaConceptosBaseLiquidacion';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'nomina_conceptos_base_liquidacion';		//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_empresa='$id_empresa' AND id_concepto=$id_concepto ";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->OrderBy			= '';
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'false';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 		= 610;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 		= 465;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			//$grilla->QuitarAncho		= 150;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			//$grilla->QuitarAlto		= 265;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'codigo_concepto_base,concepto_base';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Codigo','codigo_concepto_base',80);
			$grilla->AddRow('Concepto','concepto_base',250);
			$grilla->AddRow('Naturaleza','naturaleza_base',80);
			$grilla->AddRowImage('','<center title="Eliminar"><img src="img/eliminar.png" style="cursor:pointer;" onclick="eliminar_concepto_liquidacion(\'[id]\')"></center>',30);

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 310;
			$grilla->FColumnaGeneralAncho	= 300;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 120;
			$grilla->FColumnaFieldAncho		= 180;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Ventana Concepto Nomina'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'true';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'false';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		= 'Agregar Concepto'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'reunionadd';			//IMAGEN CSS DEL BOTON
			$grilla->AddBotton('Agregar Concepto','reunionadd','ventana_agregar_concepto('.$id_concepto.');');
			$grilla->AddBotton('Regresar','regresar','Win_Ventana_configuracion_conceptos_liquidacion.close();Actualiza_Div_nominaConceptos('.$id_concepto.');');
			$grilla->VAutoResize		= 'true';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		= 380;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		= 500;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		= 'true';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)


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

		//function Agregar_nominaConceptosBaseLiquidacion(){ ventana_agregar_concepto(0); }
		function Editar_nominaConceptosBaseLiquidacion(id){ }

		function ventana_agregar_concepto(id){
			var textBtn   = 'Guardar'
			,	textTitle = 'Agregar Concepto'
			,   style 	  = true;

			// if(id > 0){
			// 	textBtn   = 'actualizar';
			// 	textTitle = 'Actualizar Centro de Costo';
			// 	style     = false;
			// }

			Win_Ventana_bucar_concepto = new Ext.Window({
			    width       : 650,
			    height      : 600,
			    id          : 'Win_Ventana_bucar_concepto',
			    title       : textTitle,
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : 'nomina_conceptos/buscar_concepto.php',
			        scripts : true,
			        nocache : true,
			        params  : { id_concepto : id }
			    }
			}).show();
		}

		function eliminar_concepto_liquidacion(id) {
			if (!confirm('Realmente desea Eliminar el concepto?')) { return; }
			Ext.get('MuestraToltip_nominaConceptosBaseLiquidacion_'+id).load({
				url     : 'nomina_conceptos/bd/bd.php',
				scripts : true,
				nocache : true,
				params  :
				{
					opc : 'eliminar_concepto_liquidacion',
					id : id,
				}
			});
		}

	</script>
<?php
}
if ($opcion=='Vupdate' || $opcion=='Vagregar') {
?>
	<script>


	</script>

<?php
}


?>