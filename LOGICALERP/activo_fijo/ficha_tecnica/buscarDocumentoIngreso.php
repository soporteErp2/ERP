<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");

	switch ($tipo_doc) {
		case 'FC':
			$tablaDocumento = "compras_facturas";
			$whereDocumento = "factura_por_cuentas='true'";
			break;

		case 'NCG':
			$tablaDocumento = "nota_contable_general";
			$whereDocumento = "";
			break;

	}

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'documentoIngreso';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= $tablaDocumento;			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_sucursal=$id_sucursal AND id_empresa=$id_empresa AND consecutivo>0 AND (estado =1 OR estado=2)";						//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->OrderBy			= 'id DESC';			//LIMITE DE LA CONSULTA
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize 		= 'true';
			// $grilla->Ancho		 	    = $CualAncho;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->Alto		 		= $CualAlto;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->QuitarAncho		= 145;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 195;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'

		if ($tipo_doc=='FC') {
			//TOOLBAR Y CAMPO DE BUSQUEDA Y FILTROS
				$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
				$grilla->CamposBusqueda		= 'nit,proveedor,prefijo_factura,numero_factura,consecutivo'; //CAMPOS DE BUSQUEDA DE LA GRILLA

			//CONFIGURACION DE CAMPOS EN LA GRILLA
				$grilla->AddRow('id','id',80);
				$grilla->AddRow('Prefijo','prefijo_factura',80);
				$grilla->AddRow('N. Factura proveedor','numero_factura',150);
				$grilla->AddRow('Consecutivo','consecutivo',100);
				$grilla->AddRow('Nit','nit',100);
				$grilla->AddRow('Proveedor','proveedor',200);
				$grilla->AddRow('Fecha','fecha_inicio',250,'fecha');
		}
		else if ($tipo_doc=='NCG') {
			//TOOLBAR Y CAMPO DE BUSQUEDA Y FILTROS
				$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
				$grilla->CamposBusqueda		= 'consecutivo,tipo_nota,numero_identificacion_tercero,tercero,usuario'; //CAMPOS DE BUSQUEDA DE LA GRILLA

			//CONFIGURACION DE CAMPOS EN LA GRILLA
				$grilla->AddRow('Consecutivo','consecutivo',70);
				$grilla->AddRow('Filtro','tipo_nota',120);
				$grilla->AddRow('Contabilidad','sinc_nota',80);
				$grilla->AddRow('','tipo_identificacion_tercero',50);
				$grilla->AddRow('N. Identificacion','numero_identificacion_tercero',120);
				$grilla->AddRow('Tercero','tercero',200);
				$grilla->AddRow('Fecha','fecha_nota',100);
		}


		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL

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
		function Editar_documentoIngreso(id){ <?php echo $cargaFunction ?> }

	</script>

<?php
} ?>




