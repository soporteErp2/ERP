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

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	// echo "$filtro_sucursal - $tipo_documento_cruce";
	$whereSucursal='';
	if($filtro_sucursal > 0 && $filtro_sucursal!='global') $whereSucursal="AND id_sucursal='$filtro_sucursal'";

	switch ($tipo_documento_cruce) {
		case 'FC':
			$tabla_documento      = 'compras_facturas';
			$CamposBusquedaGrilla = 'fecha_inicio,fecha_final,prefijo_factura,numero_factura,consecutivo,nit,proveedor';
			$whereConsecutivos    = 'numero_factura>0 OR consecutivo>0';
			$orderBy = 'consecutivo DESC';
			break;

		case 'CE':
			$tabla_documento      = 'comprobante_egreso';
			$CamposBusquedaGrilla = 'fecha_comprobante,consecutivo,nit_tercero,tercero';
			$whereConsecutivos    = 'consecutivo>0';
			$orderBy = 'consecutivo DESC';
			break;

		case 'RV':
			$tabla_documento      = 'ventas_remisiones';
			$CamposBusquedaGrilla = 'fecha_inicio,fecha_finalizacion,consecutivo,nit,cliente,bodega';
			$whereConsecutivos    = 'consecutivo>0';
			$orderBy = 'consecutivo DESC';
			break;

		case 'FV':
			$tabla_documento      = 'ventas_facturas';
			$CamposBusquedaGrilla = 'fecha_inicio,fecha_vencimiento,prefijo,numero_factura,nit,cliente,numero_factura_completo,bodega';
			$whereConsecutivos    = "numero_factura>0 ";
			$orderBy = 'fecha_inicio DESC';
			break;

		case 'RC':
			$tabla_documento      = 'recibo_caja';
			$CamposBusquedaGrilla = 'fecha_recibo,consecutivo,nit_tercero,tercero';
			$whereConsecutivos    = 'consecutivo>0';
			$orderBy = 'consecutivo DESC';
			break;

		case 'LN':
			$tabla_documento      = 'nomina_planillas';
			$CamposBusquedaGrilla = 'fecha_documento,consecutivo,usuario';
			$whereConsecutivos    = 'consecutivo>0';
			$orderBy = 'consecutivo DESC';
			break;

		case 'LE':
			$tabla_documento      = 'nomina_planillas_liquidacion';
			$CamposBusquedaGrilla = 'fecha_documento,consecutivo,usuario';
			$whereConsecutivos    = 'consecutivo>0';
			$orderBy = 'consecutivo DESC';
			break;

		case 'PA':
			$tabla_documento      = 'nomina_planillas_ajuste';
			$CamposBusquedaGrilla = 'fecha_documento,consecutivo,usuario';
			$whereConsecutivos    = 'consecutivo>0';
			$orderBy = 'consecutivo DESC';
			break;

		case 'NCG':
			$tabla_documento      = 'nota_contable_general';
			$CamposBusquedaGrilla = 'fecha_nota,consecutivo,consecutivo_niif,sucursal,tipo_nota,numero_identificacion_tercero,tercero,tipo_nota';
			$whereConsecutivos    = 'consecutivo>0';
			$orderBy = 'consecutivo DESC';
			break;


		default:
			$tipo_documento_cruce = 'FC';
			$tabla_documento      = 'compras_facturas';
			$CamposBusquedaGrilla = 'fecha_inicio,fecha_final,prefijo_factura,numero_factura,consecutivo,nit,proveedor';
			$whereConsecutivos    = 'numero_factura>0 OR consecutivo>0';
			$orderBy = 'consecutivo DESC';
			break;
	}


	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'consultarDocumentos';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= $tabla_documento;		//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND ($whereConsecutivos) AND id_empresa='$id_empresa' $whereSucursal";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA
			$grilla->GroupBy 			= '';
			$grilla->OrderBy 			= $orderBy;
		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'false';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			// $grilla->Ancho		 		= 700;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->Alto		 		= 350;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->QuitarAncho		= 85;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= $CamposBusquedaGrilla;		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			if ($tipo_documento_cruce=='FC') {
				$grilla->AddRow('Fecha','fecha_inicio',80);
				$grilla->AddRow('Vencimiento','fecha_final',80);
				$grilla->AddRowImage('Numero','<center title="[prefijo_factura] [numero_factura]">[prefijo_factura] [numero_factura]</center>',150);
				$grilla->AddRowImage('Consecutivo','<center id="div_consultarDocumentos_consecutivo_[id]" title="[consecutivo]">[consecutivo]</center>',150);
				$grilla->AddRow('Nit','nit',80);
				$grilla->AddRow('Proveedor','proveedor',300);
				$grilla->AddRow('Sucursal','sucursal',100);
				$grilla->AddRow('Bodega','bodega',100);

			}
			else if ($tipo_documento_cruce=='CE') {
				$grilla->AddRow('Fecha','fecha_comprobante',80);
				$grilla->AddRowImage('Consecutivo','<center id="div_consultarDocumentos_consecutivo_[id]" title="[consecutivo]">[consecutivo]</center>',150);
				$grilla->AddRow('Nit','nit_tercero',80);
				$grilla->AddRow('Tercero','tercero',300);
				$grilla->AddRow('Sucursal','sucursal',100);
			}
			else if ($tipo_documento_cruce=='RV') {
				$grilla->AddRow('Fecha','fecha_inicio',80);
				$grilla->AddRow('Vencimiento','fecha_finalizacion',80);
				$grilla->AddRowImage('Consecutivo','<center id="div_consultarDocumentos_consecutivo_[id]" title="[consecutivo]">[consecutivo]</center>',150);
				$grilla->AddRow('Nit','nit',80);
				$grilla->AddRow('Cliente','cliente',300);
				$grilla->AddRow('Sucursal','sucursal',100);
				$grilla->AddRow('Bodega','bodega',100);

			}
			else if ($tipo_documento_cruce=='FV') {
				$grilla->AddRow('Fecha','fecha_inicio',80);
				$grilla->AddRow('Vencimiento','fecha_vencimiento',80);
				$grilla->AddRowImage('Consecutivo','<center id="div_consultarDocumentos_numero_factura_completo_[id]" title="[numero_factura_completo]">[numero_factura_completo]</center>',150);
				$grilla->AddRow('Nit','nit',80);
				$grilla->AddRow('Cliente','cliente',300);
				$grilla->AddRow('Sucursal','sucursal',100);
				$grilla->AddRow('Bodega','bodega',100);
			}
			else if ($tipo_documento_cruce=='RC') {
				$grilla->AddRow('Fecha','fecha_recibo',80);
				$grilla->AddRowImage('Consecutivo','<center id="div_consultarDocumentos_consecutivo_[id]" title="[consecutivo]">[consecutivo]</center>',150);
				$grilla->AddRow('Nit','nit_tercero',80);
				$grilla->AddRow('Tercero','tercero',300);
				$grilla->AddRow('Sucursal','sucursal',100);
			}
			else if ($tipo_documento_cruce=='LN' || $tipo_documento_cruce=='LE' || $tipo_documento_cruce=='PA') {
				$grilla->AddRow('Fecha','fecha_documento',80);
				$grilla->AddRowImage('Consecutivo','<center id="div_consultarDocumentos_consecutivo_[id]" title="[consecutivo]">[consecutivo]</center>',150);
				$grilla->AddRow('Usuario','usuario',300);
				$grilla->AddRow('Sucursal','sucursal',100);
			}
			else if ($tipo_documento_cruce=='NCG') {
				$grilla->AddRow('Fecha','fecha_nota',80);
				$grilla->AddRowImage('Consecutivo Colgaap','<center id="div_consultarDocumentos_consecutivo_[id]" title="[consecutivo]">[consecutivo]</center>',150);
				$grilla->AddRowImage('Consecutivo Niif','<center id="div_consultarDocumentos_consecutivo_[id]" title="[consecutivo]">[consecutivo]</center>',150);
				$grilla->AddRow('Nit','numero_identificacion_tercero',80);
				$grilla->AddRow('Tercero','tercero',300);
				$grilla->AddRow('Tipo','tipo_nota',150);
				$grilla->AddRow('Sucursal','sucursal',100);
			}

		//CONFIGURACION CSS X COLUMNA
			$grilla->AddColStyle('consecutivo_documento','text-align:right; width:75px !important; padding-right:5px');

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
			$grilla->VBotonNText		= 'Nueva Familia'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'cubos_add';			//IMAGEN CSS DEL BOTON
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
		var filtroBusqueda = '';
		<?php if(isset($MyFiltroBusqueda))echo 'filtroBusqueda = "'.$MyFiltroBusqueda.'"'; ?>

		function Editar_consultarDocumentos(id){
			var documento=document.getElementById('filtro_tipo_documento').value;
			var titulo      = ''
			,	consecutivo = '';

			if (documento=='FC') {
				consecutivo=document.getElementById('div_consultarDocumentos_consecutivo_'+id).innerHTML;
				titulo='Factura de Compra<br>Nr. '+consecutivo;
			}
			else if (documento=='CE') {
				consecutivo=document.getElementById('div_consultarDocumentos_consecutivo_'+id).innerHTML;
				titulo='Comprobante de Egreso<br>Nr.'+consecutivo;
			}
			else if (documento=='RV') {
				consecutivo=document.getElementById('div_consultarDocumentos_consecutivo_'+id).innerHTML;
				titulo='Remision de Venta<br>Nr.'+consecutivo;
			}
			else if (documento=='FV') {
				consecutivo=document.getElementById('div_consultarDocumentos_numero_factura_completo_'+id).innerHTML;
				titulo='Factura de Venta<br>Nr.'+consecutivo;
			}
			else if (documento=='RC') {
				consecutivo=document.getElementById('div_consultarDocumentos_consecutivo_'+id).innerHTML;
				titulo='Recibo de Caja<br>Nr.'+consecutivo;
			}
			else if (documento=='LN') {
				consecutivo=document.getElementById('div_consultarDocumentos_consecutivo_'+id).innerHTML;
				titulo='Planilla de Nomina<br>Nr.'+consecutivo;
			}
			else if (documento=='LE') {
				consecutivo=document.getElementById('div_consultarDocumentos_consecutivo_'+id).innerHTML;
				titulo='Liquidacion de Empleado<br>Nr.'+consecutivo;
			}
			else if (documento=='NCG') {
				consecutivo=document.getElementById('div_consultarDocumentos_consecutivo_'+id).innerHTML;
				titulo='Nota Contable General<br>Nr.'+consecutivo;
			}


		 	var myalto2  = Ext.getBody().getHeight();
	        var myancho2 = Ext.getBody().getWidth();

	        WinAlto = myalto2-20;
	        WinAncho = myancho2-30;

	        Win_Panel_buscar_documento = new Ext.Window({
	            width       : WinAncho,
	            height      : WinAlto,
	            title       : 'Informacion detallada del documento',
	            modal       : true,
	            autoScroll  : true,
	            closable    : false,
	            autoDestroy : false,
	            bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>',
	            items       :
	            [
	                {
	                    closable    : false,
	                    border      : false,
	                    autoScroll  : true,
	                    iconCls     : '',
	                    bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
	                    items       :
	                    [
	                        {
	                            xtype       : "panel",
	                            id          : 'contenedor_Win_Panel_buscar_documento',
	                            border      : false,
	                            autoLoad    :
	                                    {
	                                        url     : 'consulta_documentos/grillaDocumento.php',
	                                        scripts : true,
	                                        nocache : true,
	                                        params  :
	                                                {
														documento    : documento,
														consecutivo  : consecutivo,
														id_documento : id,
	                                                }
	                                    }
	                        }
	                    ],
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
	                                    text        : 'Log de eventos',
	                                    scale       : 'large',
	                                    iconCls     : 'busca_doc',
	                                    iconAlign   : 'top',
	                                    handler     : function(){ ventana_log_documento() }
	                                },
	                                {
	                                    xtype       : 'button',
	                                    width       : 60,
	                                    height      : 56,
	                                    text        : 'Regresar',
	                                    scale       : 'large',
	                                    iconCls     : 'regresar',
	                                    iconAlign   : 'top',
	                                    handler     : function(){ Win_Panel_buscar_documento.close() }
	                                },
		                            {
		                                xtype       : 'button',
		                                width       : 60,
		                                height      : 56,
		                                text        : 'Imprimir',
		                                scale       : 'large',
		                                iconCls     : 'pdf32_new',
		                                iconAlign   : 'top',
		                                handler     : function(){ imprimir_bitacora() }
		                            }

	                            ]
	                        },'->',
                {
                    xtype       : "tbtext",
                    text        : '<div id="divContenedor_" style="font-weight:bold;font-size:18px;"><div>',
                    scale       : "large",
                }
	                    ]
	                }
	            ]
	        }).show();
		}



    </script>
<?php } ?>
