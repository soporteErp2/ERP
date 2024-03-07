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
			$grilla->GrillaName	 		= 'centroCostos';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'centro_costos';		//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_empresa='$id_empresa'";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->OrderBy			= 'CAST(codigo AS CHAR) ASC';
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'false';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 		= 610;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 		= 465;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			//$grilla->QuitarAncho		= 150;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			//$grilla->QuitarAlto		= 265;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'codigo,nombre';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Codigo','codigo',100);
			$grilla->AddRow('Nombre','nombre',250);

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 300;
			$grilla->FColumnaGeneralAncho	= 300;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 80;
			$grilla->FColumnaFieldAncho		= 200;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Ventana Centro De Costos'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'true';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		= 'Nuevo Centro De Costos'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'addcontactos';			//IMAGEN CSS DEL BOTON
			$grilla->AddBotton('Regresar','regresar','Win_Panel_Global.close();');
			$grilla->VAutoResize		= 'true';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		= 340;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		= 190;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		= 'true';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

		//CONFIGURACION DE LOS FORMULARIOS DE CAPTURA Y EDICION
			$grilla->AddSeparator('Datos Centro De Costos');
			$grilla->AddTextField('Codigo:','codigo_centro_costos',200,'true','false');
			$grilla->AddValidation('codigo_centro_costos','numero');
			$grilla->AddTextField('Nombre:','nombre_centro_costos',200,'true','false');
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

		function Agregar_centroCostos(){ ventana_centro_costo(0); }
		function Editar_centroCostos(id){ ventana_centro_costo(id); }

		function ventana_centro_costo(id){
			var textBtn   = 'Guardar'
			,	textTitle = 'Nuevo Centro de Costo'
			,   style 	  = true;

			if(id > 0){
				textBtn   = 'actualizar';
				textTitle = 'Actualizar Centro de Costo';
				style     = false;
			}

			Win_Ventana_centro_costo = new Ext.Window({
			    width       : 250,
			    height      : 170,
			    id          : 'Win_Ventana_centro_costo',
			    title       : textTitle,
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : 'centro_costos/bd/bd.php',
			        scripts : true,
			        nocache : true,
			        params  : { opc : 'ventana_centro_costo', id : id }
			    },
			    tbar        :
			    [
			        {
	                    xtype       : 'button',
	                    width       : 60,
	                    height      : 56,
	                    text        : textBtn,
	                    scale       : 'large',
	                    iconCls     : 'guardar',
	                    iconAlign   : 'top',
	                    handler     : function(){ btnCentroCostos(id); }
	                },
	                {
	                    xtype       : 'button',
	                    width       : 60,
	                    height      : 56,
	                    text        : 'eliminar',
	                    scale       : 'large',
	                    iconCls     : 'eliminar',
	                    iconAlign   : 'top',
	                    hidden      : style,
	                    handler     : function(){ eliminarCentroCostos(id); }
	                },
	                {
	                    xtype       : 'button',
	                    width       : 60,
	                    height      : 56,
	                    text        : 'Regresar',
	                    scale       : 'large',
	                    iconCls     : 'regresar',
	                    iconAlign   : 'top',
	                    handler     : function(){ Win_Ventana_centro_costo.close(id) }
	                }
			    ]
			}).show();
		}

		function validateNumberInt(input){
			var patron = /[^\d]/g;
		    if(patron.test(input.value)){ input.value = (input.value).replace(patron, ''); }

		    return true;
		}

		function btnCentroCostos(id){
			var patronNumber = /[^\d]/g
			,	patronString = /[^a-zA-Z\d\s]/g
			,	codigo       = document.getElementById('codigo_cuenta_costo').value
			,	nombre       = document.getElementById('nombre_cuenta_costo').value;

			codigo = codigo.replace(patronNumber, '');
			nombre = nombre.replace(patronString, '');

			if(codigo.length == 0){ alert('Aviso.\nEl campo Codigo es obligatorio'); return; }
			else if(codigo.length%2 == 1){ alert('Aviso.\nEl campo Codigo debe tener cantidad de digitos pares'); return; }
			else if(nombre.length == 0){ alert('Aviso.\nEl campo Nombre es obligatorio'); return; }

			if(id > 0) action = 'update';

			Ext.get('render_centro_costo').load({
				url     : 'centro_costos/bd/bd.php',
				scripts : true,
				nocache : true,
				params  :
				{
					opc    : 'save_update_centro_costo',
					id     : id,
					codigo : codigo,
					nombre : nombre
				}
			});
		}

		function eliminarCentroCostos(id) {
			if (confirm("Advertencia\nSi elimina un Centro Costos se eliminara todos los demas asociados\nDesea continuar?")) {
				Ext.get('render_centro_costo').load({
					url     : 'centro_costos/bd/bd.php',
					scripts : true,
					nocache : true,
					params  :
					{
						opc    : 'eliminar_centro_costo',
						id     : id
					}
				});
			}

		}

	</script>
<?php
}?>