<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");

	
	$idEmpresa  = $_SESSION['EMPRESA'];
	$idSucursal = $_SESSION['SUCURSAL'];

	// CONSULTAR EL COSTO DEL INVENTARIO1

	$sqlRemision = "SELECT id_entrada_almacen FROM ventas_pos WHERE id='$id_documento' AND activo =1 AND id_empresa='$idEmpresa'";
	$queryRemision=$mysql->query($sqlRemision,$mysql->link);
	$idRemision = $mysql->result($queryRemision,0,'id_entrada_almacen');
	
	if($idRemision){
		$sqlRemisionCosto = "SELECT SUM(cantidad * costo_unitario) AS costo_total FROM ventas_remisiones_inventario WHERE id_remision_venta='$idRemision' AND activo =1";
		$queryRemisionCosto=$mysql->query($sqlRemisionCosto,$mysql->link);
		$costo_ticket = $mysql->result($queryRemisionCosto,0,'costo_total');
	}
	else{
		$sqlReceta="SELECT SUM(cantidad * costo) AS costo_total
		FROM ventas_pos_inventario_receta
		WHERE activo = 1 AND id_empresa='$idEmpresa' AND id_pos='$id_documento'";
	
		$queryReceta=$mysql->query($sqlReceta,$mysql->link);
		$costo_ticket = $mysql->result($queryReceta,0,'costo_total');
	}


	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	// $where = $filtro_sucursal > 0 ? "AND id_sucursal='$filtro_sucursal'": "";
	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'consultarItems';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
		if($tabla == 'ventas_pos_inventario_receta'){
			$grilla->ConsulCustom		= "SELECT
											codigo,
											nombre,
											cantidad,
											costo,
											(costo*cantidad) as costo_total
											FROM";
		}
			$grilla->TableName			= $tabla;		//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_empresa='$idEmpresa' AND id_pos='$id_documento' ";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,20';			//LIMITE DE LA CONSULTA
			// $grilla->GroupBy 			= 'id';
		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'false';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 		= 675;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 		= 295;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->QuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			// $grilla->QuitarAlto		= 220;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'codigo,nombre';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Codigo','codigo',60);
			$grilla->AddRow('Nombre','nombre',200);
			$grilla->AddRow('Cantidad','cantidad',60);
			if ($tabla=='ventas_pos_inventario') {
				$grilla->AddRow('Valor Unit.','precio_venta',100);
				$grilla->AddRow('Imp. %','valor_impuesto',50);
			}
			else{
				$grilla->AddRow('Costo Unit.','costo',60);
				$grilla->AddRow('Costo total','costo_total',80);
			}

		//CONFIGURACION CSS X COLUMNA
			// $grilla->AddColStyle('codigo_cuenta','text-align:right; width:75px !important; padding-right:5px');
			// $grilla->AddColStyle('debe','text-align:right; width:95px !important; padding-right:5px');
			// $grilla->AddColStyle('haber','text-align:right; width:95px !important; padding-right:5px');

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 300;
			$grilla->FColumnaGeneralAncho	= 280;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 80;
			$grilla->FColumnaFieldAncho		= 200;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Items ticket'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'false';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'false';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		= 'Nueva Familia'; 	//TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'cubos_add';		//IMAGEN CSS DEL BOTON
			$grilla->VAutoResize		= 'true';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		= 340;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		= 130;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		= 'false';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)


	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$grilla->Link = $link;  	//Conexion a la BD			/**/
	/**/	$grilla->inicializa($_POST);//variables POST			/**/
	/**/	$grilla->GeneraGrilla(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/

?>
<script>
		document.getElementById('Contenedor_costo_items').innerHTML='<b>Costo Total Ticket</b><br>$ <?= $costo_ticket ?>';
		document.getElementById('Contenedor_costo_items').style.fontSize='17px';
		document.getElementById('Contenedor_costo_items').style.marginRight='40px';
			function Editar_consultarItems(id){ 
			Win_Ventana_Consultar_costo_item_receta = new Ext.Window({
				width		: 715,
				id			: 'Win_Ventana_Consultar_costo_item_receta',
				height		: 430,
				title		: ' Ingredientes',
				modal		: true,
				autoScroll	: true,
				closable	: true,
				autoDestroy : true,
				bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
				autoLoad    :
							{
								url		: 'consulta_pos_cuentas_colgaap/consulta_costo_item_receta.php',
								scripts	: true,
								nocache	: true,
								params	:
								{
									id_documento     : <?php echo $id_documento ?>,
									id_producto      : id,
									id_remision      : <?php echo ($idRemision)? $idRemision : 0 ?>,
								}
							},
				tbar		:
						[
							{
								xtype		: 'button',
								width 		: 60,
								height 		: 56,
								text		: 'Regresar',
								scale		: 'large',
								iconCls		: 'regresar',
								iconAlign	: 'top',
								handler 	: function(){Win_Ventana_Consultar_costo_item_receta.close()}
							},'->',
                    		{
                    		    xtype       : "tbtext",
                    		    text        : '<div id="costo_item_receta"></div>',
                    		    scale       : "large",
                    		}
						]
			}).show();
		}
</script>