<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");

	// echo $tipo_documento;
	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	$id_empresa    = $_SESSION['EMPRESA'];
	$whereSucursal = ($id_sucursal!='global')? $whereSucursal=" AND id_sucursal='$id_sucursal' " : "";

	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'consultarCuentasColgaap';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= $tabla_asiento;		//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_empresa='$id_empresa' AND id_documento='$id_documento' AND tipo_documento='$tipo_documento' ";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA
			$grilla->GroupBy 			= 'id';
			$grilla->OrderBy   			= 'CAST(codigo_cuenta AS CHAR) ASC';
		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'false';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			// $grilla->Ancho		 		= 660;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->Alto		 		= 290;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->QuitarAncho		= 120;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 230;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'codigo_cuenta,cuenta,tercero';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Cuenta','codigo_cuenta',150);
			$grilla->AddRow('Detalle','cuenta',250);
			$grilla->AddRow('Tercero','tercero',150);
			$grilla->AddRow('Debito','debe',100,'MonedaAsientos');
			$grilla->AddRow('Credito','haber',100,'MonedaAsientos');
			$grilla->AddRow('Sucursal','sucursal',100);
			$grilla->AddRow('C. Cos','codigo_centro_costos',100);

		//CONFIGURACION CSS X COLUMNA
			$grilla->AddColStyle('codigo_cuenta','text-align:right; width:145px !important; padding-right:5px');
			$grilla->AddColStyle('cuenta','text-align:left; width:245px !important; padding-right:5px');
			$grilla->AddColStyle('debe','text-align:right; width:95px !important; padding-right:5px');
			$grilla->AddColStyle('haber','text-align:right; width:95px !important; padding-right:5px');

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 300;
			$grilla->FColumnaGeneralAncho	= 280;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 80;
			$grilla->FColumnaFieldAncho		= 200;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Ventana Familia Items '.$subtitulo; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
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


if(!isset($opcion)){ ?>
	<script>

		if('<?php echo $tipo_documento; ?>' == 'FV'
			|| '<?php echo $tipo_documento; ?>' == 'FC'
			|| '<?php echo $tipo_documento; ?>' == 'NDFV'
			|| '<?php echo $tipo_documento; ?>' == 'NDFC'
			|| '<?php echo $tipo_documento; ?>' == 'RV'
			){ Ext.getCmp("btnEditarContabilizacion").show(); }
		else{ Ext.getCmp("btnEditarContabilizacion").hide(); }

		function Editar_consultarCuentasColgaap(id){ }

		function imprimirBusqueda(){
			window.open("consulta_documentos_cuentas_colgaap/imprimir.php?sucursal=<?php echo $sucursal; ?>&tabla_asiento=<?php echo $tabla_asiento; ?>&id_documento=<?php echo $id_documento; ?> &type_document=<?php echo $tipo_documento; ?> &tipo_documento_extendido=<?php echo $tipo_documento_extendido; ?> &numero_documento=<?php echo $numero_documento; ?>");
		}
		 var titulo=('<?php echo $tabla_asiento; ?>'=='asientos_colgaap')? 'Colgaap' : 'Niif' ;
		function editarCuentasDocumento(){

			Win_Ventana_editar_cuentas_documento = new Ext.Window({
			    width       : 700,
			    height      : 370,
			    id          : 'Win_Ventana_editar_cuentas_documento',
			    title       : 'Editar Contabilizacion '+titulo,
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    resizable	: false,
			    autoLoad    :
			    {
			        url     : 'consulta_documentos_cuentas_colgaap/grilla_editar_cuentas.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
						opcGrilla                : 'EditarCuentasDocumento',
						fecha_documento          : '<?php echo $fecha_documento; ?>',
						id_documento             : '<?php echo $id_documento; ?>',
						consecutivo_documento    : '<?php echo $consecutivo_documento; ?>',
						tipo_documento           : '<?php echo $tipo_documento; ?>',
						tipo_documento_extendido : '<?php echo $tipo_documento_extendido; ?>',
						consecutivo_documento    : '<?php echo $consecutivo_documento; ?>',
						id_tercero               : '<?php echo $id_tercero; ?>',
						tabla_asiento            : '<?php echo $tabla_asiento; ?>',
			        }
			    },
			    tbar        :
			    [
			        {
			            xtype   : 'buttongroup',
			            columns : 3,
			            title   : 'Opciones',
			            items   :
			            [
			                {
			                    xtype       : 'button',
			                    width       : 60,
			                    height      : 56,
			                    text        : 'Guardar',
			                    scale       : 'large',
			                    iconCls     : 'guardar',
			                    iconAlign   : 'top',
			                    id 			: 'btnGuardarActualizarCuentas',
			                    handler     : function(){ save_update_contabilizacion(); }
			                },
			                {
			                    xtype       : 'button',
			                    width       : 60,
			                    height      : 56,
			                    text        : 'Regresar',
			                    scale       : 'large',
			                    iconCls     : 'regresar',
			                    iconAlign   : 'top',
			                    handler     : function(){ Win_Ventana_editar_cuentas_documento.close(id) }
			                }
			            ]
			        }
			    ]
			}).show();
		}

    </script>
<?php } ?>
<script>
	saldoCuentaDetalle('consultarCuentasColgaap');
</script>
