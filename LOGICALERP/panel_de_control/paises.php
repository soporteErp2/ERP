<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");
	include("../../misc/MyGrilla/class.MyGrilla.php");

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'Paises';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'ubicacion_pais';			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= 'activo = 1';		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,50';			//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'true';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			//$grilla->Ancho		 	= 400;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			//$grilla->Alto		 		= 300;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->QuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 75;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'pais,continente,subcontinente';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Codigo','id',40,'codigo');
			$grilla->AddRowImage('','<center><img src="../../temas/clasico/images/Banderas/[iso2].png" width="16" height="16"></center>',25);
			$grilla->AddRow('Pais','pais',150);
			$grilla->AddRow('Continente','continente',100);
			$grilla->AddRow('Subcontinente','subcontinente',100);
			$grilla->AddRow('ISO2','iso2',40);
			$grilla->AddRow('ISO3','iso3',40);
			$grilla->AddRowImage('Moneda','[moneda]&nbsp;&nbsp;&nbsp;&nbsp;[nombre-moneda]',150);
			$grilla->AddRow('Zona Horaria','time_zone',150);
			//$grilla->AddRow('Impuesto','impuesto',80);
			//$grilla->AddRowImage('Configuracion Tributaria','<center><div style="float:left; margin: 0 0 0 7px;"><img src="../../../temas/clasico/images/BotonesTabs/config16.png?" style="cursor:pointer" width="16" height="16" onclick="terceros_tributario([id]);"></div></center>',160);
			$grilla->AddRowImage('Configuracion Tributaria','<center><div style="float:left; text-align:center; width:100%"><img src="../../../temas/clasico/images/BotonesTabs/config16.png?" style="cursor:pointer" width="16" height="16" onclick="terceros_tributario([id]);"></div></center>',150);


		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			// $grilla->TituloVentana		= 'Ventana Pais'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			// $grilla->VBarraBotones		= 'false';			//SI HAY O NO BARRA DE BOTONES
			// $grilla->VBotonNuevo		= 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			// $grilla->VBotonNText		= 'Nuevo Registro'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			// $grilla->VBotonNImage		= 'add';			//IMAGEN CSS DEL BOTON
			// $grilla->VAutoResize		= 'false';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			// $grilla->VAncho		 		= 350;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			// $grilla->VAlto		 		= 150;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			// $grilla->VQuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			// $grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			// $grilla->VAutoScroll		= 'false';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			// $grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			// $grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)
		//CONFIGURACION DE LO FORMULARIOS DE CAPTURA Y EDICION
			//$grilla->AddTextField('TextField','nombre',150,'true','false');
			//$grilla->AddComboBox('ComboBox BD','dato1',150,'true','true','configuracion_zonas,id,nombre,true,true,true');
			//$grilla->AddComboBox('Combobox Manual','dato2',150,'true','false','1:primera opcion,2:segunda opcion,3:tercera opcion');
			$grilla->AddTextField('Zona Horaria','time_zone',150,'true','false');
			$grilla->AddTextField('Impuesto','impuesto',150,'true','false');

			//$grilla->AddTextArea('TextArea','dato3',150,80,'true');

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$grilla->Link = $link;  	//Conexion a la BD			/**/
	/**/	$grilla->inicializa($_POST);//variables POST			/**/
	/**/	$grilla->GeneraGrilla(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/
if (!isset($opcion)) { ?>

	<script>
	function terceros_tributario(id){
		var nombrePais = document.getElementById('div_Paises_pais_'+id).innerHTML;
		Win_Ventana_Terceros_Tributario = new Ext.Window({
			width		: 500,
			id			: 'Win_Ventana_Terceros_Tributario',
			height		: 400,
			title		: 'Configuracion Tributaria &nbsp;&nbsp;Pais&nbsp;&nbsp;-'+nombrePais,
			modal		: true,
			autoScroll	: false,
			closable	: false,
			autoDestroy : true,
			autoLoad	:
			{
				url		: 'paises/tercero_tributario.php',
				scripts	: true,
				nocache	: true,
				params	: { id_pais	:id }
			}
		}).show();
	}
	//al poner la opcion de edicion automatica en false, entonces la grilla busca el evento sigt, para enviar la accion, sino lo encuentra genera error
	//por eso se crea para q acceda y no muestre error asi no se haga ninguna accion
	function Editar_Paises(){ }
	</script>




<?php } ?>



