<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	$sqlAux = "SELECT id_factura_compra 
				FROM compras_facturas_inventario 
				WHERE activo = 1  AND id_sucursal=$id_sucursal 
				AND id_bodega=$filtro_bodega 
				AND id_empresa='$id_empresa' GROUP BY id_factura_compra";
	$queryAux = mysql_query($sqlAux,$link);

	while($row = mysql_fetch_array($queryAux)){
		$id_facturas .= "'$row[id_factura_compra]',";
	}
	$id_facturas = substr($id_facturas, 0, strlen($id_facturas)-1);
	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'facturaCompra';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//CONSULTA CUSTOM
			//$grilla->ConsulCustom		= 'SELECT 
			//									estado, 
			//									prefijo_factura, 
			//									numero_factura, 
			//									consecutivo, 
			//									nit, 
			//									proveedor, 
			//									fecha_inicio 
			//								FROM';
		//QUERY
			$grilla->TableName			= 'compras_facturas';			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 
											AND proveedor <> '' 
											AND id_sucursal=$id_sucursal 
											AND id_bodega=$filtro_bodega 
											AND id_empresa='$id_empresa' 
											AND id_saldo_inicial=0 
											AND factura_por_cuentas='false' 
											AND (id in($id_facturas) OR numero_factura > 0)";
			$grilla->OrderBy			= 'id DESC';			//LIMITE DE LA CONSULTA
			$grilla->GroupBy			= 'id';			//LIMITE DE LA CONSULTA
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize 		= 'true';
			// $grilla->Ancho		 	    = $CualAncho;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->Alto		 		= $CualAlto;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->QuitarAncho		= 145;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 170;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA Y FILTROS
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'nit,proveedor,prefijo_factura,numero_factura,consecutivo';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
			$grilla->Gfilters			= 'false';
			$grilla->GfiltersAutoOpen	= 'false';
	 		$grilla->AddFilter('Estado de la Factura','estado','estado');

		//CONFIGURACION DE CAMPOS EN LA GRILLA
	 		$grilla->AddRowImage('Estado','<center><img src="img/estado_doc/[estado].png" style="cursor:pointer" width="16" height="16" id="imgEstadoFacturaCompra_[id]" /></center>','40');
			$grilla->AddRow('Prefijo','prefijo_factura',80);
			$grilla->AddRow('N. Factura proveedor','numero_factura',150);
			$grilla->AddRow('Consecutivo','consecutivo',100);
			$grilla->AddRow('Nit','nit',100);
			$grilla->AddRow('Proveedor','proveedor',200);
			$grilla->AddRow('Fecha','fecha_inicio',250,'fecha');

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
			$grilla->VBotonNText		= 'Nueva Reunion'; //TEXTO DEL BOTON DE NUEVO REGISTRO
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

		function Editar_facturaCompra(id){
			var direccionRender = 'facturacion/facturacion_compras_bloqueada.php'
			,	estado          = document.getElementById('imgEstadoFacturaCompra_'+id).getAttribute('src')
			,	consecutivo     = document.getElementById('div_facturaCompra_consecutivo_'+id).innerHTML;

			if(estado == 'img/estado_doc/0.png'){ direccionRender = 'facturacion/facturacion_compras.php'; }

			if(estado == 'img/estado_doc/3.png' ){
 				document.getElementById('titleDocuementoFacturaCompra').innerHTML='<span style="color:red;text-align: center;font-size: 18px;font-weight: bold;">Consecutivo<br>N. '+consecutivo+'</span>';
 			}
			else if (consecutivo!='') { document.getElementById('titleDocuementoFacturaCompra').innerHTML='Consecutivo<br>N. '+consecutivo; }
			else{ document.getElementById('titleDocuementoFacturaCompra').innerHTML=''; }

			Ext.get("contenedor_facturacion_compras").load({
				url     : direccionRender,
				scripts : true,
				nocache : true,
				params  :
				{
					id_factura_compra : id,
					filtro_bodega     : '<?php echo $filtro_bodega; ?>'
				}
			});

			Win_Ventana_buscar_factura_compra.close();
		}
	</script>

<?php
} ?>




