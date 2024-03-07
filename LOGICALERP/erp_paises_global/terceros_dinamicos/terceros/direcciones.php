<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../../../../misc/MyGrilla/class.MyGrilla.php");
	include '../../config_paises.php';
	$id_pais = $_SESSION['PAIS'];
	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'TercerosDirecciones';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'terceros_direcciones';		//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= 'activo = 1 AND id_tercero = ' . $elid;		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			//$grilla->AutoResize = 'true';
			$grilla->Ancho		 		= 780;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			//$grilla->Alto		 		= 500;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			//$grilla->QuitarAncho		= 300;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 310;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'nombre,departamento,ciudad,direccion';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Nombre','nombre',150);
			$grilla->AddRow($labelDepto,'departamento',100);
			$grilla->AddRow($labelCiudad,'ciudad',100);
			$grilla->AddRow($labelMunicipio,'comuna',100);
			$grilla->AddRow('Direccion','direccion',150);
			$grilla->AddRowImage('e-mail F.E','<center><div style="float:left; margin: 0 0 0 1px"><img src="../../../temas/clasico/images/BotonesTabs/email16.png" style="cursor:pointer" width="16" height="16" onclick="VentanaDireccionesEmail([id]);"></div><div style="float:left">&nbsp;([emails])</div></center>',62);

			$grilla->FContenedorAncho		= 350;
			$grilla->FColumnaGeneralAncho	= 340;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 110;
			$grilla->FColumnaFieldAncho		= 150;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'true';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Ventana Direccion'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'true';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		= 'Agregar Direccion'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'addcontactos';			//IMAGEN CSS DEL BOTON
			$grilla->VAutoResize		= 'false';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		= 320;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		= 350;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			//$grilla->VQuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			//$grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		= 'false';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

		//CONFIGURACION DE LO FORMULARIOS DE CAPTURA Y EDICION
			$grilla->AddTextField('Nombre de Sucursal','nombre',150,'true','false');
			$grilla->AddTextField('','id_tercero',150,'true','true',$elid);
			$grilla->AddComboBox ($labelDepto,'id_departamento',150,'true','true','ubicacion_departamento,id,departamento,true','activo = 1 AND id_pais='.$id_pais.' ORDER BY pais ASC');
			$grilla->AddComboBox ($labelCiudad,'id_ciudad',150,'true','true','ubicacion_ciudad,id,ciudad,true','activo = 2');
			$grilla->AddComboBox ($labelMunicipio,'id_comuna',150,'false','true','ubicacion_comuna,id,comuna,true','activo = 1');
			$grilla->AddTextField('Direccion','direccion',150,'true','false');
			$grilla->AddTextField('Telefono 1','telefono1',150,'true','false');
			$grilla->AddTextField('Telefono 2','telefono2',150,'false','false');
			$grilla->AddTextField('Celular 1','celular1',150,'false','false');
			$grilla->AddTextField('Celular 2','celular2',150,'false','false');
			$grilla->AddTextField('Direccion Principal','direccion_principal',150,'false','true');

		//CONFIGURACION DEL MENU CONTEXTUAL
			$grilla->MenuContext         = 'false';
			$grilla->MenuContextEliminar = 'false';

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$grilla->Link = $link;  	//Conexion a la BD			/**/
	/**/	$grilla->inicializa($_POST);//variables POST			/**/
	/**/	$grilla->GeneraGrilla(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/

if($opcion == 'Vupdate' || $opcion == 'Vagregar'){?>
	<script>
		//Si la direccion es la principal del tercero, no se puede eliminar
		var DireccionPrincipal = document.getElementById('TercerosDirecciones_direccion_principal').value;
		if(DireccionPrincipal == 1){
			// document.getElementById('BtnV_eliminar_TercerosDirecciones').style.display = "none";
			// document.getElementById('BtnV_TercerosDirecciones').style.display = "none";
		}

		/*------------------------- Departamento, Ciudad -------------------------*/
		var ComboDepartamento = Ext.get('TercerosDirecciones_id_departamento');

		ComboDepartamento.addListener(
			'change',
			function(event,element,options){
				id_departamento = document.getElementById('TercerosDirecciones_id_departamento').value;
				ActualizaCiudadDirecciones(id_departamento);
			},
			this
		);

		function ActualizaCiudadDirecciones(id_departamento){
			var MyParentCiudadDirecciones = document.getElementById('TercerosDirecciones_id_ciudad').parentNode;
			Ext.get(MyParentCiudadDirecciones).load({
				url		: '../terceros/bd/bd.php',
				timeout : 180000,
				scripts	: true,
				nocache	: true,
				params	:
				{
					op              : 'OptionSelectDepartamentoDireccion',
					id_cliente      : '<?php echo $id?>',
					id_departamento : id_departamento
				}
			});
		};

		var id_ciudad = document.getElementById('TercerosDirecciones_id_ciudad').value;
		var ComboCiudad = Ext.get('TercerosDirecciones_id_ciudad');
		ComboCiudad.addListener(
			'change',
			function(event,element,options){
				id_ciudad = document.getElementById('TercerosDirecciones_id_ciudad').value;
				ActualizaComunaDirecciones(id_ciudad);
			},
			this
		);

		function ActualizaComunaDirecciones(id_ciudad){
			var MyParentComunaDirecciones = document.getElementById('TercerosDirecciones_id_comuna').parentNode;
			Ext.get(MyParentComunaDirecciones).load({
				url		: '../terceros/bd/bd.php',
				timeout : 180000,
				scripts	: true,
				nocache	: true,
				params	:
				{
					op         : 'OptionSelectComunaDireccion',
					id_cliente : '<?php echo $id?>',
					clase      : 'TercerosDirecciones',
					id_ciudad  : id_ciudad
				}
			});
		};

		var id_departamento = document.getElementById('TercerosDirecciones_id_departamento').value;
		ActualizaCiudadDirecciones(id_departamento);
		// console.log(id_ciudad);
		// var id_ciudad = document.getElementById('TercerosDirecciones_id_ciudad').value;
		// ActualizaComunaDirecciones(id_ciudad);

	</script>
<?php }
if(!isset($opcion)){?>
	<script>
		/*-------------------------------- Email ---------------------------------*/
		function VentanaDireccionesEmail(id){
			Win_Ventana_ConfiguracionClientes_Direcciones_email = new Ext.Window({
				width		    : 520,
				id					: 'Win_Ventana_ConfiguracionClientes_Direcciones_email',
				height			: 300,
				title				: 'Administracion de Cuentas de e-mail',
				modal				: true,
				autoScroll	: false,
				closable		: false,
				autoDestroy : true,
				autoLoad		: {
												url			: '../terceros/terceros/direcciones_email.php',
												scripts	: true,
												nocache	: true,
												params	: {
																		elid : id
																	}
											}
			}).show();
		}
  </script>
<?php } ?>
