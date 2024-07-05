<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../../../../misc/MyGrilla/class.MyGrilla.php");

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	$tabla_puc = 'puc';
	if ($opc=='niif') { $tabla_puc='puc_niif'; }

	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= $opcGrilla;  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= $tabla_puc;			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_empresa='$id_empresa' AND (id_sucursal=0 OR id_sucursal='$id_sucursal') $sql";						//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->OrderBy			= 'CAST(cuenta AS CHAR) ASC';			//LIMITE DE LA CONSULTA
			$grilla->MySqlLimit			= '0,200';			//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize 		= 'true';
			// $grilla->Ancho		 	= $CualAncho;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->Alto		 	= $CualAlto;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->QuitarAncho		= 145;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 150;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'

		//TOOLBAR Y CAMPO DE BUSQUEDA Y FILTROS
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'cuenta,descripcion';

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Cuenta','cuenta',120);
			$grilla->AddRow('Descripcion','descripcion',300);

			$grilla->AddColStyle('cuenta','text-align:right; width:115px !important; margin-right:5px;');   //ALINEAR NUMEROS LA DERECHA

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 760;
			$grilla->FColumnaGeneralAncho	= 380;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 130;
			$grilla->FColumnaFieldAncho		= 150;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Ventana Reuniones Coope'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'false';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'false';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		= 'Nueva Reunion';  //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'addcontactos';	//IMAGEN CSS DEL BOTON
			$grilla->VAutoResize		= 'true';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		= 400;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		= 200;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		= 'true';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

 		//CONFIGURACION DEL MENU CONTEXTUAL
 			// $grilla->MenuContext		= 'true';		//MENU CONTEXTUAL
	 		// $grilla->MenuContextEliminar= 'true';

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
		function Editar_<?php echo $opcGrilla; ?>(id){

			var cuenta = document.getElementById('div_<?php echo $opcGrilla; ?>_cuenta_'+id).innerHTML;

			Ext.Ajax.request({
			    url     : 'nota_general/bd/bd.php',
			    params  :
			    {
					opc    : 'validarCuenta',
					cuenta : cuenta
			    },
			    success :function (result, request){
			    			var result = result.responseText;

			                if(result > 1){ alert("Aviso.\nNo se puede seleccionar una cuenta contable padre!"); return; }
			                else if(result == 0){ alert("Aviso.\nLa cuenta contable "+cuenta+" No existe!"); return; }
			                else if(result != 1){ alert("Aviso.\nNo se comprobo la cuenta contable!"); return; }

			                <?php echo $cargaFuncion; ?>
			            },
			    failure : function(){ alert("Aviso.\nNo se comprobo la cuenta contable!"); return; }
			});
		}
	</script>

<?php
} ?>




