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
			$grilla->GrillaName	 		= 'nominaGruposConceptos';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'nomina_grupos_conceptos';		//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_empresa='$id_empresa'";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
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
			$grilla->CamposBusqueda		= 'descripcion';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Descripcion','descripcion',250);
			// $grilla->AddRow('Fecha Inicio Vigencia','fecha_inicio_vigencia',120);
			// $grilla->AddRow('Fecha Fin Vigencia','fecha_fin_vigencia',120);
			$grilla->AddRowImage('','<img src="img/config16.png" style="cursor:pointer" width="16" height="16" title="Conceptos de este Grupo" onclick="ventana_conceptos(\'[id]\',\'[descripcion]\')">',16);


		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 270;
			$grilla->FColumnaGeneralAncho	= 270;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 80;
			$grilla->FColumnaFieldAncho		= 150;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'true';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Ventana Grupos Conceptos'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'true';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		= 'Grupo Nuevo'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'cubos_add';			//IMAGEN CSS DEL BOTON
			$grilla->AddBotton('Regresar','regresar','Win_Panel_Global.close();');
			$grilla->VAutoResize		= 'true';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		= 330;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		= 180;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		= 'true';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

		//CONFIGURACION DE LOS FORMULARIOS DE CAPTURA Y EDICION
			// $grilla->AddSeparator('Datos Centro De Costos');
			// $grilla->AddValidation('codigo_centro_costos','numero');
			$grilla->AddSeparator('General');
			$grilla->AddTextField('Descripcion:','descripcion',150,'true','false');
            $grilla->AddValidation('descripcion','mayuscula');
            // $grilla->AddSeparator('Vigencia');
			// $grilla->AddTextField('Fecha Inicio:','fecha_inicio_vigencia',150,'true','false');
			// $grilla->AddTextField('Fecha Final:','fecha_fin_vigencia',150,'true','false');
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

		function ventana_conceptos(id,descripcion){

			Win_Ventana_definicion_tributaria = new Ext.Window({
			    width       : 650,
			    height      : 600,
			    id          : 'Win_Ventana_definicion_tributaria',
			    title       : 'Conceptos de Grupo '+descripcion,
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : 'nomina_conceptos/nomina_conceptos.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
						id_grupo : id,
						grupo    : descripcion,
			        }
			    }
			    // ,
			    // tbar        :
			    // [
			    //     {
			    //         xtype   : 'buttongroup',
			    //         columns : 3,
			    //         title   : 'Opciones',
			    //         items   :
			    //         [
			    //             {
			    //                 xtype       : 'button',
			    //                 width       : 60,
			    //                 height      : 56,
			    //                 text        : 'Regresar',
			    //                 scale       : 'large',
			    //                 iconCls     : 'regresar',
			    //                 iconAlign   : 'left',
			    //                 handler     : function(){ Win_Ventana_definicion_tributaria.close(id) }
			    //             }
			    //         ]
			    //     }
			    // ]
			}).show();
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