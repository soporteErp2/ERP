<?php
	include("../../../../../configuracion/conectar.php");
	include("../../../../../configuracion/define_variables.php");
	include("../../../../../misc/MyGrilla/class.MyGrilla.php");

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	if      ($tipo=='FC') { $tabla='compras_facturas'; }
	else if ($tipo=='CE') { $tabla='comprobante_egreso'; }
	else if ($tipo=='RC') { $tabla='recibo_caja'; }
	else if ($tipo=='NCG'){ $tabla='nota_contable_general'; }

	$id_empresa  = $_SESSION['EMPRESA'];
	// $id_sucursal = $_SESSION['SUCURSAL'];
	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= $tabla;  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= $tabla;			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_empresa=$id_empresa AND id_sucursal=$id_sucursal AND (estado=1 OR estado=2)"; //WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->OrderBy			= 'id DESC';			//LIMITE DE LA CONSULTA
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize 		= 'true';
			// $grilla->Ancho		 	= $CualAncho;		//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->Alto		 	= $CualAlto;		//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->QuitarAncho		= 145;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 195;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'

		//TOOLBAR Y CAMPO DE BUSQUEDA Y FILTROS
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			if ($tipo == 'FC') {
	 			$grilla->CamposBusqueda		= 'fecha_inicio,fecha_final,numero_factura,consecutivo,nit,proveedor';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
				$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
				$grilla->AddRowImage('Prefijo','[prefijo_factura]<input type="hidden" value="[id_proveedor]" id="id_tercero_[id]">','90');
	 			// $grilla->AddRow('Prefijo','prefijo_factura',90);
	 			$grilla->AddRow('Numero','numero_factura',90);
	 			$grilla->AddRow('Consecutivo','consecutivo',70);
	 			$grilla->AddRow('Nit','nit',90);
	 			$grilla->AddRow('Proveedor','proveedor',200);
			}
			else if ($tipo == 'CE') {
				$grilla->CamposBusqueda		= 'fecha_comprobante,consecutivo,numero_cheque,nit_tercero,tercero';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
				$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
				$grilla->AddRowImage('Fecha','[fecha_comprobante]<input type="hidden" value="[id_tercero]" id="id_tercero_[id]">','90');
	 			// $grilla->AddRow('Fecha','fecha_comprobante',90);
	 			$grilla->AddRow('Consecutivo','consecutivo',70);
	 			$grilla->AddRow('Cheque','numero_cheque',90);
	 			$grilla->AddRow('Nit','nit_tercero',90);
	 			$grilla->AddRow('Tercero','tercero',200);
			}
			else if ($tipo == 'RC') {
				$grilla->CamposBusqueda		= 'fecha_recibo,consecutivo,nit_tercero,tercero';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
				$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
				$grilla->AddRowImage('Fecha','[fecha_recibo]<input type="hidden" value="[id_tercero]" id="id_tercero_[id]">','90');
	 			// $grilla->AddRow('Fecha','fecha_recibo',90);
	 			$grilla->AddRow('Consecutivo','consecutivo',70);
	 			$grilla->AddRow('Nit','nit_tercero',90);
	 			$grilla->AddRow('Tercero','tercero',200);

			}
			else if ($tipo == 'NCG'){
				$grilla->CamposBusqueda		= 'fecha_nota,tipo_nota,consecutivo,numero_identificacion_tercero,tercero';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
				$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
				$grilla->AddRowImage('Fecha','[fecha_nota]<input type="hidden" value="[id_tercero]" id="id_tercero_[id]">','90');
	 			// $grilla->AddRow('Fecha','fecha_nota',90);
	 			$grilla->AddRow('Tipo','tipo_nota',90);
	 			$grilla->AddRow('Consecutivo','consecutivo',70);
	 			$grilla->AddRow('Nit','numero_identificacion_tercero',90);
	 			$grilla->AddRow('Tercero','tercero',200);
			}

 			$grilla->AddRow('Sucursal','sucursal',100);

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
			// $grilla->MenuContext         = 'true';		//MENU CONTEXTUAL
			// $grilla->MenuContextEliminar = 'true';

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
	    //alert("<?php echo $_SERVER['SERVER_NAME']; ?>");
		function Editar_<?php echo $tabla; ?>(id){
			<?php echo $cargaFuncion; ?>
		}

	</script>

<?php
} ?>




