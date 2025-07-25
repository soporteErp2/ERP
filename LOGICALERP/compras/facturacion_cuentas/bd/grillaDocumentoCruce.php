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

	$filtro_empresa  = $_SESSION['EMPRESA'];
	// $filtro_sucursal = $_SESSION['SUCURSAL'];

	// echo $filtro_sucursal;
	$where ='';
	 // if ($tipo_documento_cruce == 'FC') { $tablaBuscar = 'compras_facturas'; }
	 // if ($tipo_documento_cruce == 'FV') { $tablaBuscar = 'ventas_facturas'; }
	 if ($tipo_documento_cruce == 'CE') {
	 	$tablaBuscar = 'comprobante_egreso';
	 	$sql="SELECT
					CE.id,
					CE.consecutivo,
					CEC.cuenta,
					CEC.tercero,
					CEC.id_comprobante_egreso
				FROM
					comprobante_egreso AS CE,
					comprobante_egreso_cuentas AS CEC
				WHERE
					CE.activo = 1
				AND CE.estado = 1
				AND CE.id_empresa = $filtro_empresa
				AND CE.id_sucursal = $filtro_sucursal
				AND CEC.id_comprobante_egreso = CE.id
				AND CEC.saldo_pendiente>0
				AND CEC.id_documento_cruce=0
				AND CEC.tipo_documento_cruce=''
				AND CEC.cuenta LIKE '13%'";
	 	$query=mysql_query($sql,$link);
	 	while ($row=mysql_fetch_array($query)) {
	 		$var.=($var=='')?' id='.$row['id'] : ' OR id='.$row['id'] ;
	 	}

	 	if ($var!='') {
	 		$where=' AND ('.$var.') ';
	 	}
	 	else{
	 		$where='AND id=0';
	 	}
	 }else{
		$tablaBuscar = 'compras_facturas';
	 }


	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= $opcGrillaContable;  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= $tablaBuscar;			//NOMBRE DE LA TABLA EN LA BASE DE DATOS

			$grilla->MyWhere			= "activo = 1 AND estado=1 AND id_empresa='$filtro_empresa' AND id_sucursal= '$filtro_sucursal' $where ";

			$grilla->OrderBy			= 'id DESC';			//LIMITE DE LA CONSULTA
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize 		= 'true';
			// $grilla->Ancho		 	    = $CualAncho;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->Alto		 		= $CualAlto;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->QuitarAncho		= 150;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 190;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA Y FILTROS
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
			$grilla->Gfilters			= 'false';
			$grilla->GfiltersAutoOpen	= 'false';
	 		$grilla->AddFilter('Estado de la Factura','estado','estado');

		//CONFIGURACION DE CAMPOS EN LA GRILLA

	 	if ($tipo_documento_cruce == 'CE') {
	 		$grilla->CamposBusqueda		= 'nit_tercero,tercero,consecutivo,fecha_comprobante';

	 		$grilla->AddRowImage('Estado','<center><img src="img/estado_doc/[estado].png" style="cursor:pointer" width="16" height="16" id="imgEstadoFacturaCompra_[id]" /></center><div id="id_tercero_[id]" style="display:none;">[id_tercero]</div><div id="cuenta_pago_[id]" style="display:none;" >[cuenta_pago]</div><div id="total_factura_sin_abono_[id]" style="display:none;" >[total_factura_sin_abono]</div>','50');
			$grilla->AddRow('Consecutivo','consecutivo',80);
			$grilla->AddRow('Nit','nit_tercero',100);
			$grilla->AddRow('Tercero','tercero',200);
			// $grilla->AddRow('Saldo','total_factura_sin_abono',120);
			$grilla->AddRow('Fecha','fecha_comprobante',120);

		}else{
			$grilla->CamposBusqueda		= 'nit,proveedor,consecutivo';

	 		$grilla->AddRowImage('Estado','<center><img src="img/estado_doc/[estado].png" style="cursor:pointer" width="16" height="16" id="imgEstadoFacturaCompra_[id]" /></center><div id="id_tercero_[id]" style="display:none;">[id_tercero]</div><div id="cuenta_pago_[id]" style="display:none;" >[cuenta_pago]</div><div id="total_factura_sin_abono_[id]" style="display:none;" >[total_factura_sin_abono]</div>','50');
			$grilla->AddRow('Consecutivo','consecutivo',80);
			$grilla->AddRow('prefijo','prefijo_factura',80);
			$grilla->AddRow('numero','numero_factura',80);
			$grilla->AddRow('Nit','nit',100);
			$grilla->AddRow('Tercero','proveedor',200);
			// $grilla->AddRow('Saldo','total_factura_sin_abono',120);
			$grilla->AddRow('Fecha','fecha_inicio',120);
		}

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

		function Editar_<?php echo $opcGrillaContable; ?>(id){
			let tipo_documento_cruce = '<?php echo $tipo_documento_cruce; ?>'
			let terceroElement = (tipo_documento_cruce == 'FC')? "_proveedor_" : "_tercero_"
			var consecutivo = document.getElementById('div_<?php echo $opcGrillaContable; ?>_consecutivo_'+id).innerHTML;
			var tercero     = document.getElementById('div_<?php echo $opcGrillaContable; ?>'+terceroElement+id).innerHTML;
			var id_tercero  = document.getElementById('id_tercero_'+id).innerHTML;

			var myalto  = Ext.getBody().getHeight();
			var myancho = Ext.getBody().getWidth();

			Win_Ventana_cuentas_documento_cruce = new Ext.Window({
			    width       : myancho-100,
			    height      : myalto-50,
			    id          : 'Win_Ventana_cuentas_documento_cruce',
			    title       : 'Cuentas del documento N.'+consecutivo,
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : 'facturacion_cuentas/bd/grillaCuentasComprobanteEgreso.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
						id_documento         : id,
						consecutivo          : consecutivo,
						opcGrillaContable    : '<?php echo $opcGrillaContable; ?>',
						cont                 : '<?php echo $cont; ?>',
						filtro_sucursal      : '<?php echo $filtro_sucursal; ?>',
						tipo_documento_cruce : '<?php echo $tipo_documento_cruce; ?>',
						tercero              : tercero,
						id_tercero           : id_tercero,
			        }
			    },
			    tbar        :
			    [
			        {
			            xtype   : 'buttongroup',
			            columns : 3,
			            title   : 'Opciones',
			            style   : 'border-right:none;',
			            items   :
			            [
			                {
			                    xtype       : 'button',
			                    width       : 60,
			                    height      : 56,
			                    text        : 'Regresar',
			                    scale       : 'large',
			                    iconCls     : 'regresar',
			                    iconAlign   : 'top',
			                    hidden      : false,
			                    handler     : function(){ BloqBtn(this); Win_Ventana_cuentas_documento_cruce.close(id) }
			                }
			            ]
			        }
			    ]
			}).show();
		}

	</script>

<?php
} ?>

