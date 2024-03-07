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
			$grilla->GrillaName	 		= 'informes_formatos_secciones_filas_terceros';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'informes_formatos_secciones_filas_terceros';		//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_empresa='$id_empresa' AND id_formato=$id_formato AND id_seccion=$id_seccion AND id_fila=$id_fila";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			// $grilla->OrderBy			= 'CAST(codigo AS CHAR) ASC';
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'false';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 		= 465;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 		= 370;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			//$grilla->QuitarAncho		= 150;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			//$grilla->QuitarAlto		= 265;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'codigo,nombre';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Documento','documento_tercero',100);
			$grilla->AddRow('Tercero','tercero',100);
			$grilla->AddRow('Columna','columna',100);
			$grilla->AddRow('Fila','fila',100);

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 300;
			$grilla->FColumnaGeneralAncho	= 300;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 80;
			$grilla->FColumnaFieldAncho		= 200;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'true';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Ventana Centro De Costos'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'true';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		= 'Agregar Tercero'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'addcontactos';			//IMAGEN CSS DEL BOTON
			$grilla->AddBotton('Regresar','regresar','Win_Ventana_terceros.close();');
			$grilla->VAutoResize		= 'true';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		= 340;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		= 190;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		= 'true';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

		//CONFIGURACION DE LOS FORMULARIOS DE CAPTURA Y EDICION

			$grilla->AddTextField('Doc. Tercero:','documento_tercero',150,'true','false');
			$grilla->AddTextField('Tercero:','tercero',150,'true','false');
			$grilla->AddComboBox('Columna','id_columna',150,'true','true','informes_formatos_secciones_columnas,id,nombre,true','activo=1 AND id_empresa='.$id_empresa.' AND id_formato='.$id_formato.' AND id_seccion='.$id_seccion.' ORDER BY orden ASC');


			$grilla->AddTextField('','id_tercero',200,'true','true');
			$grilla->AddTextField('','id_empresa',200,'true','true',$id_empresa);
			$grilla->AddTextField('','id_formato',200,'true','true',$id_formato);
			$grilla->AddTextField('','id_seccion',200,'true','true',$id_seccion);
			$grilla->AddTextField('','id_fila',200,'true','true',$id_fila);


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

		function ventanaBuscarTercero() {
			var myalto  = Ext.getBody().getHeight();
			var myancho = Ext.getBody().getWidth();

			Win_Ventana_buscar_tercero = new Ext.Window({
			    width       : myancho-100,
			    height      : myalto-50,
			    id          : 'Win_Ventana_buscar_tercero',
			    title       : 'Seleccionar tercero',
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : '../funciones_globales/grillas/BusquedaTerceros.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
						cargaFuncion  : 'rederizaTerceros(id);',
						nombre_grilla : 'grillaTercerosInformes',
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
			                    text        : 'Regresar',
			                    scale       : 'large',
			                    iconCls     : 'regresar',
			                    iconAlign   : 'top',
			                    hidden      : false,
			                    handler     : function(){ BloqBtn(this); Win_Ventana_buscar_tercero.close(id) }
			                }
			            ]
			        }
			    ]
			}).show();
		}

		function rederizaTerceros(id) {
			var nombre    = document.getElementById(`div_grillaTercerosInformes_nombre_comercial_${id}`).innerHTML
			,	documento = document.getElementById(`div_grillaTercerosInformes_numero_identificacion_${id}`).innerHTML

			document.getElementById('informes_formatos_secciones_filas_terceros_documento_tercero').value = documento;
			document.getElementById('informes_formatos_secciones_filas_terceros_tercero').value           = nombre ;
			document.getElementById('informes_formatos_secciones_filas_terceros_id_tercero').value        = id;
			Win_Ventana_buscar_tercero.close();
		}

	</script>
<?php
}
if ($opcion=='Vupdate' || $opcion=='Vagregar') {
?>
	<script>
		var inputCod  = document.getElementById('informes_formatos_secciones_filas_terceros_documento_tercero')
		,	inputCcos = document.getElementById('informes_formatos_secciones_filas_terceros_tercero')

		inputCod.readOnly  = true;
		inputCcos.readOnly = true;
		inputCod.setAttribute("style","float:left;");

		var divBtnPlantilla = document.createElement("div");
		divBtnPlantilla.setAttribute("class","divBtnBuscarPuc");
		divBtnPlantilla.setAttribute("onclick","ventanaBuscarTercero()");
		divBtnPlantilla.setAttribute('title','Buscar Tercero');
		divBtnPlantilla.innerHTML = '<img src="img/buscar20.png" />';
		document.getElementById("DIV_informes_formatos_secciones_filas_terceros_documento_tercero").appendChild(divBtnPlantilla);

	</script>
<?php
}
?>