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

	$empresa          = $_SESSION['EMPRESA'];
	$filtro_sucursal  = $_SESSION['SUCURSAL'];
	$filtro_ubicacion = $filtro_bodega;

	//capturo el id del item
	$tmp         = mysql_fetch_array(mysql_query("SELECT id_item AS id FROM inventario_totales WHERE id=$elid ",$link));
	$codigo_item = $tmp['id'];

	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'Inventario_totales_traslados';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'inventario_totales_traslados_manual';		//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= 'activo = 1 AND id_equipo = '.$codigo_item.' AND id_empresa='.$_SESSION['EMPRESA'];		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA
			$grilla->OrderBy			= 'fecha DESC';
		//TAMANO DE LA GRILLA
			//$grilla->AutoResize	 		= 'true';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			//$grilla->Ancho		 		= 470;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			//$grilla->Alto		 		= 220;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->QuitarAncho		= 150;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 200;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'fecha';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('N° Documento','consecutivo',100,'codigo');
			$grilla->AddRow('Fecha y Hora','fecha',150);
			$grilla->AddRow('Usuario Que Realizo El Traslado','nombre_usuario',350);
			$grilla->AddRow('Cantidad','cantidad',80);

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 760;
			$grilla->FColumnaGeneralAncho	= 380;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 130;
			$grilla->FColumnaFieldAncho		= 150;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Ventana Inventario'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'true';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		= 'Trasladar Inventario'; 	//TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'addcontactos';	//IMAGEN CSS DEL BOTON
			$grilla->AddBotton('Regresar','regresar','Win_Ventana_Traslado.close()');
			$grilla->VAutoResize		= 'true';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			//$grilla->VAncho		 	= 840;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			//$grilla->VAlto		 	= 570;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho		= 540;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto		= 5;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		= 'true';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)
 		//CONFIGURACION DEL MENU CONTEXTUAL
 			$grilla->MenuContext		= 'true';			//MENU CONTEXTUAL
	 		$grilla->MenuContextEliminar= 'false';  		// BOTON ELIMINAR EN MENU CONTEXTUAL

		//OPCIONES ADICIONALES EN EL MENU CONTEXTUAL
 			$grilla->AddMenuContext('Imprimir Formato de Traslado','doc','window.open("informe_movimiento_equipo.php?id=[id]&funcion=Traslado")');

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


if(!isset($opcion)){ ?>
	<script>


		/*--------------------------------------------------------------Funcion agregar nuevo traslado----------------------------------------------------*/

		function Agregar_Inventario_totales_traslados(){
			Win_Nuevo_Traslado = new Ext.Window({
				width		: 515,
				id			: 'Win_Nuevo_Traslado',
				height		: 390,
				title		: 'Nuevo Informe Traslado Items',
				modal		: true,
				autoScroll	: false,
				closable	: true,
				autoDestroy : true,
				autoLoad	:
				{
					url		:'inventario_unidades/nuevo_movimiento_inventario.php',
					scripts	:true,
					nocache	:true,
					params	:
					{
						opc                     :"empresa",
						elid                    :"<?php echo $elid;?>",
						cantidad_actual         :"<?php echo $cantidad_actual;?>",
						filtro_sucursal_origen  :"<?php echo $filtro_sucursal_origen;?>",
						filtro_ubicacion_origen :"<?php echo $filtro_ubicacion_origen;?>"
					}
				},
				tbar		:
				[
					{
						xtype		: 'button',
						text		: 'Trasladar',
						scale		: 'large',
						iconCls		: 'guardaruser',
						iconAlign	: 'left',
						handler 	: function(){comparar_destino()}
					},
					{
						xtype		: 'button',
						text		: 'Regresar',
						scale		: 'large',
						iconCls		: 'regresar',
						iconAlign	: 'left',
						handler 	: function(){Win_Nuevo_Traslado.close(id)}
					}

				]
			}).show();
		}

		function comparar_destino(){
		/*-------------------------------------------Informacion Destino-------------------------------------------------------*/
		var sucursal_destino = document.getElementById('Inventario_sucursal').value;
		var bodega_destino   = document.getElementById('Inventario_bodega').value;
		/*--------------------------------------------Informacion Origen-------------------------------------------------------*/

		var sucursal_origen   = document.getElementById('id_sucursal_origen').value;
		var bodega_origen     = document.getElementById('id_ubicacion_origen').value;
		var cantidad_anterior = (<?php echo $cantidad_actual;?>*1);
		var cantidad_nueva    = ((document.getElementById('cantidad_trasladar').value)*1);
		var observaciones     = document.getElementById('observaciones').value;

			if(bodega_destino === bodega_origen && sucursal_destino === sucursal_origen){ alert("Error, El destino y el origen son iguales"); }
			else{

				if(bodega_destino == ""){ alert("Error, Seleccione una bodega Destino"); }
				else{

					if(isNaN(cantidad_nueva) || cantidad_nueva==0){ alert('Error, Campo Cantidad es Obligatorio'); return; }
					else{

						if (cantidad_nueva>cantidad_anterior){ alert('Error, Cantidad a trasladar es mayor a la existente'); }
						else{

							if (cantidad_nueva<0) { alert('Error, Cantidad a trasladar menor a cero'); }
							else{

								if (observaciones=="") { alert('Error, Campo Observaciones es Obligatorio'); }
								else{ guardar_traslado(sucursal_destino,bodega_destino,sucursal_origen,bodega_origen,cantidad_nueva,observaciones); }
							}
						}
					}
				}
			}
		}

		function guardar_traslado(sucursal_destino,bodega_destino,sucursal_origen,bodega_origen,cantidad,observaciones){
			Ext.get("guardar").load({
				url		: "inventario_unidades/nuevo_movimiento_inventario.php",
				scripts	: true,
				nocache	: true,
				params	:
				{
					opc              : "guardar_traslado",
					elid             : "<?php echo $elid;?>",
					sucursal_destino : sucursal_destino,
					bodega_destino   : bodega_destino,
					sucursal_origen  : sucursal_origen,
					bodega_origen    : bodega_origen,
					cantidad         : cantidad,
					observaciones    : observaciones
				}
			});
		}

		/*----------------------------------------MOSTRAR INFORME TRASLADO-------------------------------------*/
		function Editar_Inventario_totales_traslados(id){
		var myalto  = Ext.getBody().getHeight();
			Win_informe_Traslado = new Ext.Window
			(
				{
					width		: 550,
					id			: 'Win_informe_Traslado',
					height		: myalto - 250,
					title		: 'Informe Traslado Item',
					modal		: true,
					autoScroll	: true,
					closable	: true,
					autoDestroy : true,
					autoLoad	:
					{
						url		:'inventario_unidades/nuevo_movimiento_inventario.php',
						scripts	:true,
						nocache	:true,
						params	:
						{
							opc : "ver_informe",
							id  : id
						}
					},
					tbar		:
					[	{
							xtype		: 'button',
							width 		: 60,
							height 		: 56,
							text		: 'Imprimir PDF',
							scale		: 'large',
							iconCls		: 'genera',
							iconAlign	: 'top',
							handler 	: function(){window.open("inventario_unidades/informe_movimiento_equipo.php?id="+id+"&funcion=Traslado")}
						},
						{
							xtype		: 'button',
							width 		: 60,
							height 		: 56,
							text		: 'Regresar',
							scale		: 'large',
							iconCls		: 'regresar',
							iconAlign	: 'top',
							handler 	: function(){Win_informe_Traslado.close(id)}
						},'-'
					]
				}
			).show();
		}
	</script>
<?php }
?>

