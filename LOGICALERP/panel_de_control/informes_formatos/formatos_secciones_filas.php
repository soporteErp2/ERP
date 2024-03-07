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
			$grilla->GrillaName	 		= 'informes_formatos_secciones_filas';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'informes_formatos_secciones_filas';		//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_empresa='$id_empresa' AND id_formato=$id_formato AND id_seccion=$id_seccion ";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
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
			// $grilla->AddRow('Concepto','concepto',80);
			$grilla->AddRow('Nombre','nombre',250);
			$grilla->AddRow('Tercero Unico','tercero_unico',80);
			$grilla->AddRowImage('Cuentas','<center><img src="../../temas/clasico/images/BotonesTabs/table_gear.png" style="cursor:pointer" width="16" height="16" title="Cuentas del Formato" onclick="ventana_cuentas(\'[id]\',\'[nombre]\')"></center>',56);
			$grilla->AddRowImage('C. Costos','<center title="Configurar centro de costos"><img src="img/config16.png" style="cursor:pointer" width="16" height="16" onclick="ventana_centro_costos(\'[id]\')"></center>',60);
			$grilla->AddRowImage('Documentos','<center title="Configurar los documentos a tomar"><img src="img/informe0.png" style="cursor:pointer" width="16" height="16" onclick="ventana_documentos_fila(\'[id]\')"></center>',70);
			$grilla->AddRowImage('Terceros','<center title="Configurar los terceros a tomar"><img src="img/user_suit.png" style="cursor:pointer" width="16" height="16" onclick="ventana_terceros_fila(\'[id]\')"></center>',70);

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 270;
			$grilla->FColumnaGeneralAncho	= 290;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 110;
			$grilla->FColumnaFieldAncho		= 180;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'true';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Ventana Conceptos formato'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'true';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		= 'Nueva Fila'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'add_new';			//IMAGEN CSS DEL BOTON
			$grilla->AddBotton('Regresar','regresar','Win_Ventana_filas.close();');
			$grilla->VAutoResize		= 'true';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		= 350;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		= 230;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		= 'true';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

		//CONFIGURACION DE LOS FORMULARIOS DE CAPTURA Y EDICION
			$grilla->AddSeparator('Informacion de la fila');
			$grilla->AddTextField('Nombre:','nombre',150,'true','false');
			$grilla->AddComboBox ('Tercero Unico:','tercero_unico',150,'true','false','No:No,Si:Si');
			$grilla->AddTextField('Tercero:','tercero',150,'false');
			$grilla->AddTextField('','id_tercero',180,'false','true');
			$grilla->AddTextField('','documento_tercero',180,'false','true');

			$grilla->AddTextField('','id_empresa',200,'true','true',$id_empresa);
			$grilla->AddTextField('','id_formato',200,'true','true',$id_formato);
			$grilla->AddTextField('','id_seccion',200,'true','true',$id_seccion);

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

		function ventana_centro_costos(is_fila_cuenta) {

			Win_Ventana_centro_costo = new Ext.Window({
			    width       : 500,
			    height      : 500,
			    id          : 'Win_Ventana_centro_costo',
			    title       : 'filtrar las cuentas por centro de costos',
			    modal       : true,
			    autoScroll  : false,
			    closable    : true,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : 'informes_formatos/formatos_secciones_filas_centro_costos.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
			            id_formato : '<?php echo $id_formato; ?>',
						id_seccion : '<?php echo $id_seccion; ?>',
						id_fila    : is_fila_cuenta,
			        }
			    },
			}).show();
		}

		function ventana_documentos_fila(is_fila_cuenta) {

			Win_Ventana_documentos = new Ext.Window({
			    width       : 500,
			    height      : 500,
			    id          : 'Win_Ventana_documentos',
			    title       : 'Filtrar las cuentas por documentos',
			    modal       : true,
			    autoScroll  : false,
			    closable    : true,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : 'informes_formatos/formatos_secciones_filas_documentos.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
			            id_formato : '<?php echo $id_formato; ?>',
						id_seccion : '<?php echo $id_seccion; ?>',
						id_fila    : is_fila_cuenta,
			        }
			    },
			}).show();
		}

		function ventana_terceros_fila(is_fila_cuenta) {
			Win_Ventana_terceros = new Ext.Window({
			    width       : 500,
			    height      : 500,
			    id          : 'Win_Ventana_terceros',
			    title       : 'Filtrar las cuentas por terceros',
			    modal       : true,
			    autoScroll  : false,
			    closable    : true,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : 'informes_formatos/formatos_secciones_filas_terceros.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
			            id_formato : '<?php echo $id_formato; ?>',
						id_seccion : '<?php echo $id_seccion; ?>',
						id_fila    : is_fila_cuenta,
			        }
			    },
			}).show();
		}

		function ventana_cuentas(id,fila){

			Win_Ventana_conceptos_cuentas = new Ext.Window({
			    width       : 650,
			    height      : 600,
			    id          : 'Win_Ventana_conceptos_cuentas',
			    title       : 'Cuentas de la fila '+fila,
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : 'informes_formatos/formatos_secciones_filas_cuentas.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
						id_formato : '<?php echo $id_formato; ?>',
						id_seccion : '<?php echo $id_seccion; ?>',
						id_fila    : id,
			        }
			    }

			}).show();
		}

	</script>
<?php
}
if ($opcion=='Vupdate' || $opcion=='Vagregar') {
?>
	<script>
		// EVENTO EN EL CAMPO SELECT
		var selectTercero = document.getElementById('informes_formatos_secciones_filas_tercero_unico');
		selectTercero.setAttribute("onchange","muestraTercero(this.value)");

		// BOTON DE BUSCAR TERCERO
		var inputTercero = document.getElementById('informes_formatos_secciones_filas_tercero')
		,	div_content  = document.getElementById('EmpConte_informes_formatos_secciones_filas_tercero')
		inputTercero.readOnly              = true;
		inputTercero.setAttribute("style","float:left;");

		var divBtnTercero = document.createElement("div");
		divBtnTercero.setAttribute("class","divBtnBuscarPuc");
		divBtnTercero.setAttribute("onclick","ventanaBuscarTercero()");
		divBtnTercero.setAttribute('title','Buscar Tercero');
		divBtnTercero.innerHTML = '<img src="img/buscar20.png" />';
		document.getElementById("DIV_informes_formatos_secciones_filas_tercero").appendChild(divBtnTercero);

		<?php
		if ($opcion=='Vagregar') {
		?>
			div_content.style.display = 'none';

		<?php
		}
		if ( $opcion=='Vupdate') {
		?>
			if (selectTercero.value!='Si') {div_content.style.display = 'none';}
		<?php
		}
	 	?>

		function muestraTercero(valor) {
			if (valor=='Si') {div_content.style.display = 'block';}
			else{div_content.style.display = 'none';}
		}

		function ventanaBuscarTercero(){
			var myalto  = Ext.getBody().getHeight();
			var myancho = Ext.getBody().getWidth();

			Win_Ventana_buscar_tercero = new Ext.Window({
			    width       : myancho-100,
			    height      : myalto-50,
			    id          : 'Win_Ventana_buscar_tercero',
			    title       : 'Seleccion el Tercero',
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
						nombre_grilla : 'terceros',
						cargaFuncion  : 'renderizaTercero(id)',
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

		function renderizaTercero(id){

			document.getElementById('informes_formatos_secciones_filas_id_tercero').value        = id ;
			document.getElementById('informes_formatos_secciones_filas_documento_tercero').value = document.getElementById('div_terceros_numero_identificacion_'+id).innerHTML ;
			document.getElementById('informes_formatos_secciones_filas_tercero').value           = document.getElementById('div_terceros_nombre_comercial_'+id).innerHTML ;

			Win_Ventana_buscar_tercero.close();
		}

	</script>
<?php
}
?>