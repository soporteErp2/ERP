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
			$grilla->GrillaName	 		= 'BusquedaTerceros';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'terceros';		//NOMBRE DE LA TABLA DE CONSULTA EN LA BASE DE DATOS DE
			$grilla->MyWhere			= "activo = 1 AND id_empresa=$id_empresa"; //.$condicional;		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA

		//TAMANO DE LA GRILLA
			//$grilla->AutoResize	 		= 'true';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 		= 865;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			//$grilla->Alto		 		= 220;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			//$grilla->QuitarAncho		= 30;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 155;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'

		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'numero_identificacion,nombre,direccion, pais, departamento, ciudad';

            $grilla->Gfilters			= 'true';
			$grilla->GfiltersAutoOpen	= 'true';
			$grilla->AddFilter('Pais','id_pais','pais');
			$grilla->AddFilter('Tipo de Tercero','tipo','tipo');
			$grilla->AddFilter('Tipo de Documento','id_tipo_identificacion','tipo_identificacion');

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRowImage('','<center><img src="../../temas/clasico/images/BotonesTabs/[tipo].png" style="cursor:pointer" width="16" height="16"></center>',23);
			$grilla->AddRow('','tipo_identificacion',25);
			$grilla->AddRow(utf8_decode('N° Identificacion'),'numero_identificacion',100);
			$grilla->AddRow('Nombre Completo','nombre',200);
			$grilla->AddRow('Direccion','direccion',150);
			$grilla->AddRow('Telefono','telefono1',150);
			$grilla->AddRowImage('Pais','<img src="../../temas/clasico/images/Banderas/[iso2].png" width="16" height="12">&nbsp;&nbsp;[pais]',130);
			$grilla->AddRow('Departamento','departamento',100);
			$grilla->AddRow('Ciudad','ciudad',130);


		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 760;
			$grilla->FColumnaGeneralAncho	= 380;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 150;
			$grilla->FColumnaFieldAncho		= 150;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto			= 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana			= 'Clientes'; 		//NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->CerrarDespuesDeAgregar	= 'false';
			$grilla->VBarraBotones			= 'false';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo			= 'false';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			//$grilla->VBotonNText		= 'Agregar Cliente'; 	//TEXTO DEL BOTON DE NUEVO REGISTRO
			//$grilla->VBotonNImage		= 'addcliente';		//IMAGEN CSS DEL BOTON
			//$grilla->VAutoResize		= 'true';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			//$grilla->VAncho		 		= 800;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			//$grilla->VAlto		 		= 570;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			//$grilla->VQuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			//$grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			//$grilla->VAutoScroll		= 'true';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			//$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			//$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

		//CONFIGURACION DEL MENU CONTEXTUAL
 			//$grilla->MenuContext		= 'true';
	 		//$grilla->MenuContextEliminar= 'true';

		//OPCIONES ADICIONALES EN EL MENU CONTEXTUAL
 			//$grilla->AddMenuContext('Eliminar','delete','Eliminar_inventario_empleado([id])');

 		//CONFIGURACION DE LOS FORMULARIOS DE CAPTURA Y EDICION


	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$grilla->Link = $link;  	//Conexion a la BD			/**/
	/**/	$grilla->inicializa($_POST);//variables POST			/**/
	/**/	$grilla->GeneraGrilla(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/

if(!isset($opcion)) {
	echo'<script>
			function Editar_BusquedaTerceros(id){';

                $NEWcampos = base64_decode($campos);
				$arrayCuantosCampos=explode(",",$NEWcampos);
				foreach ($arrayCuantosCampos as $bucleCuantosCampos) {

					$arrayCampos=explode(":",$bucleCuantosCampos);
					$arrayCampos[0]=str_replace(" ","",$arrayCampos[0]);
					$arrayCampos[1]=str_replace(" ","",$arrayCampos[1]);

					if($arrayCampos[1]=='id'){echo 'document.getElementById("'.$arrayCampos[0].'").value= id;'; }
					else{ echo 'document.getElementById("'.$arrayCampos[0].'").value= document.getElementById("'.$arrayCampos[1].'"+id).innerHTML;'; }
				}
				echo $javascript.$nombreVentana.".close(id)";
	echo'	}
		</script>';
} ?>
