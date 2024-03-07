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
			$grilla->GrillaName	 		= 'Terceros_Contactos';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'terceros_contactos';		//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_tercero='$elid' AND id_empresa='$id_empresa' AND ContactoAuto=0";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize = 'true';
			// $grilla->Ancho		 		= 780;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			//$grilla->Alto		 		= 500;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->QuitarAncho		= 140;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 310;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'nombre,identificacion,cargo,direccion';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRowImage('','<center><img src="../../temas/clasico/images/BotonesTabs/[sexo].png" style="cursor:pointer" width="16" height="16"></center>',23);
			$grilla->AddRow('','tipo_identificacion',25);
			$grilla->AddRow('Identificacion','identificacion',100);
			$grilla->AddRow('','tratamiento',40);
			$grilla->AddRow('Nombre','nombre',250);
			$grilla->AddRow('Cargo','cargo',170);
			$grilla->AddRowImage('e-mail','<center><div style="float:left; margin: 0 0 0 1px"><img src="../../temas/clasico/images/BotonesTabs/email16.png" style="cursor:pointer" width="16" height="16" onclick="VentanaContactosEmail([id]);"></div><div style="float:left">&nbsp;([emails])</div></center>',42);
			//$grilla->AddRow('Direccion','direccion',100);
			//$grilla->AddRow('Celular','celular',80);

			$grilla->FContenedorAncho		= 350;
			$grilla->FColumnaGeneralAncho	= 340;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 110;
			$grilla->FColumnaFieldAncho		= 150;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'true';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Ventana Contacto'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'true';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		= 'Agregar Contacto'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'addcontactos';			//IMAGEN CSS DEL BOTON
			$grilla->VAutoResize		= 'false';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		= 320;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		= 500;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			//$grilla->VQuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			//$grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		= 'false';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

		//CONFIGURACION DE LO FORMULARIOS DE CAPTURA Y EDICION
			$grilla->AddTextField('','id_tercero',150,'true','true',$elid);
			$grilla->AddComboBox ('Tipo Identificacion','id_tipo_identificacion',150,'false','true','tipo_documento,id,nombre,true,true','activo = 1 AND id_empresa="'.$id_empresa.'"');
			$grilla->AddTextField('Identificacion','identificacion',150,'false','false');
			$grilla->AddTextField('','id_empresa',150,'true','true',$id_empresa);
			$grilla->AddComboBox ('Tratamiento','id_tratamiento',150,'true','true','terceros_tratamiento,id,nombre,true,true','activo = 1 AND id_empresa="'.$id_empresa.'"');
			$grilla->AddTextField('Nombre','nombre',150,'true','false');
			$grilla->AddValidation('nombre','mayuscula');
			$grilla->AddTextField('Cargo','cargo',150,'false','false');
			$grilla->AddTextField('Direccion','direccion',150,'false','false');
			$grilla->AddTextField('Telefono 1','telefono1',150,'false','false');
			$grilla->AddTextField('Telefono 2','telefono2',150,'false','false');
			$grilla->AddTextField('Celular 1','celular1',150,'false','false');
			$grilla->AddTextField('Celular 2','celular2',150,'false','false');
			$grilla->AddComboBox ('Genero','sexo',150,'true','false','Masculino:Masculino,Femenino:Femenino');
			$grilla->AddTextField('Fecha de nacimiento','nacimiento',150,'false','false');
			$grilla->AddValidation('nacimiento','fecha');
			$grilla->AddTextArea ('Observaciones','observaciones',150,60,'false');

		//CONFIGURACION DEL MENU CONTEXTUAL
 			$grilla->MenuContext		= 'true';
	 		$grilla->MenuContextEliminar= 'true';
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$grilla->Link = $link;  	//Conexion a la BD			/**/
	/**/	$grilla->inicializa($_POST);//variables POST			/**/
	/**/	$grilla->GeneraGrilla(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/

if(!isset($opcion)){?>
	<script>
		//document.getElementById("Terceros_Contactos_nacimiento").style.width="200px";

		function VentanaContactosEmail(id){
			//var myalto  = Ext.getBody().getHeight();
			//var myancho  = Ext.getBody().getWidth();
			Win_Ventana_ConfiguracionClientes_Contactos_email = new Ext.Window({
				width		: 430,
				id			: 'Win_Ventana_ConfiguracionClientes_Contactos_email',
				height		: 300,
				title		: 'Administracion de Cuentas de e-mail',
				modal		: true,
				autoScroll	: false,
				closable	: false,
				autoDestroy : true,
				autoLoad	:
				{
					url		: '../terceros/terceros/email.php',
					scripts	: true,
					nocache	: true,
					params	: { elid : 	id }
				}
			}).show();
		}
    </script>
<?php } ?>