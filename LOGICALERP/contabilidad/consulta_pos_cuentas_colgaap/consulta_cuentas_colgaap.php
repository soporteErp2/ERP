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

	$idEmpresa  = $_SESSION['EMPRESA'];
	$idSucursal = $_SESSION['SUCURSAL'];

	$where = $filtro_sucursal > 0 ? "AND id_sucursal='$filtro_sucursal'": "";
	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'consultarCuentasColgaap';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= $tabla_asiento;		//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_empresa='$idEmpresa' $where AND id_documento='$id_documento' AND tipo_documento='$type_document'";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,20';			//LIMITE DE LA CONSULTA
			$grilla->GroupBy 			= 'id';
		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'false';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 		= 675;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 		= 295;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->QuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			// $grilla->QuitarAlto		= 220;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'codigo_cuenta,cuenta';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Cuenta','codigo_cuenta',80);
			$grilla->AddRow('Detalle','cuenta',250);
			$grilla->AddRow('Debito','debe',100);
			$grilla->AddRow('Credito','haber',100);

		//CONFIGURACION CSS X COLUMNA
			$grilla->AddColStyle('codigo_cuenta','text-align:right; width:75px !important; padding-right:5px');
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

		function Editar_consultarCuentasColgaap(id){ }

		function imprimirBusqueda(){
			window.open("consulta_pos_cuentas_colgaap/imprimir.php?tabla_asiento=<?php echo $tabla_asiento; ?>&id_documento=<?php echo $id_documento; ?> &type_document=<?php echo $type_document; ?> &tipo_documento_extendido=<?php echo $tipo_documento_extendido; ?> &numero_documento=<?php echo $numero_documento; ?> &filtro_sucursal=<?php echo $filtro_sucursal; ?>");
		}

		function editarCuentasDocumento() {

			Win_Ventana_editar_cuentas_documento = new Ext.Window({
			    width       : 700,
			    height      : 430,
			    id          : 'Win_Ventana_editar_cuentas_documento',
			    title       : 'Edite los valores de las cuentas',
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : 'consulta_documentos_cuentas_colgaap/grilla_editar_cuentas.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
						id_documento      : '<?php echo $id_documento; ?>',
						type_document     : '<?php echo $type_document; ?>',
						opcGrillaContable : 'EditarCuentasDocumento',
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
			                    text        : 'Regresar',
			                    scale       : 'large',
			                    iconCls     : 'regresar',
			                    iconAlign   : 'left',
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