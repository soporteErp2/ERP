<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");

	$whereFecha = "";
	if($fecha_inicial != '' && $fecha_final != ''){ $whereFecha = "AND fecha_documento BETWEEN  '$fecha_inicial' AND '$fecha_final'"; }
	else if($fecha_inicial != ''){ $whereFecha = "AND fecha_documento >=  '$fecha_inicial'"; }
	else if($fecha_final != ''){ $whereFecha = "AND fecha_documento <=  '$fecha_final'"; }

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	$where = $filtro_sucursal > 0 ? "AND id_sucursal='$filtro_sucursal'": "";
	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'consultarDocumentosPos';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'ventas_pos';		//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_empresa='$id_empresa' $where $whereFecha";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA
			// $grilla->GroupBy 			= 'id_documento';
			$grilla->OrderBy 			= 'id DESC';
		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'false';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 		= 960;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 		= 480;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->QuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			// $grilla->QuitarAlto			= 220;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'prefijo,consecutivo,fecha_documento,caja,seccion,documento_cliente,cliente';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA id_documento numero_documento

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Prefijo','prefijo',40);
			$grilla->AddRow('Consecutivo','consecutivo',80);
			$grilla->AddRow('N. Factura','numero_factura_completo',80);
			$grilla->AddRow('N. Remision','consecutivo_entrada_almacen',70);
			$grilla->AddRow('Fecha','fecha_documento',70);
			$grilla->AddRow('Ambiente','seccion',120);
			$grilla->AddRow('Doc. cliente ','documento_cliente',70);
			$grilla->AddRow('Cliente ','cliente',120);
			$grilla->AddRowImage('Cuentas','<div style="float:left; margin:0 0 0 10px; cursor:pointer" onClick="winAccounts([id],\'[prefijo] [consecutivo]\')"><div style="float:left"><img src="../../temas/clasico/images/BotonesTabs/book_open.png" ></div></div>',50);
			$grilla->AddRowImage('Productos','<div style="float:left; margin:0 0 0 10px; cursor:pointer" onClick="winInventori({id:[id],consecutivo:\'[prefijo] [consecutivo]\',tabla:\'ventas_pos_inventario\'})"><div style="float:left"><img src="../../temas/clasico/images/BotonesTabs/inventario16.png" ></div></div>',65);
			$grilla->AddRowImage('Estado','<center><img src="img/estado_doc/[estado].png" style="cursor:pointer" width="16" height="16" id="imgEstado'.$opcGrillaContable.'_[id]" /></center>','40');
			// $grilla->AddRowImage('','<div style="float:left" id="tipo_documento_consultarDocumentosPos_[id]">[tipo_documento]</div><div style="display:none" id="id_documento_consultarDocumentosPos_[id]">[id_documento]</div>',30);
			// $grilla->AddRow('Sucursal','sucursal',100);

		//CONFIGURACION CSS X COLUMNA
			$grilla->AddColStyle('consecutivo_documento','text-align:right; width:50px !important; padding-right:10px');

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 300;
			$grilla->FColumnaGeneralAncho	= 280;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 80;
			$grilla->FColumnaFieldAncho		= 200;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Tickets POS'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
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

		var winAccounts = (id_documento,numero_documento) =>{

			var myalto           = Ext.getBody().getHeight()
			,	myancho          = Ext.getBody().getWidth()
			// ,	type_documento   = document.getElementById('tipo_documento_consultarDocumentosPos_'+id).innerHTML
			// ,	id_documento     = document.getElementById('id_documento_consultarDocumentosPos_'+id).innerHTML
			// ,   numero_documento = document.getElementById("div_consultarDocumentosPos_consecutivo_documento_"+id).innerHTML
			,	title            = "Punto de Venta No. "+numero_documento;

			Win_Ventana_Consultar_cuentas_colgaap_pos = new Ext.Window({
				width		: 715,
				id			: 'Win_Ventana_Consultar_cuentas_colgaap_pos',
				height		: 430,
				title		: title,
				modal		: true,
				autoScroll	: true,
				closable	: false,
				autoDestroy : true,
				bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
				items		:
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
								xtype		: "panel",
								id			: 'contenedor_consultarCuentasColgaap',
								border		: false,
								bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
							}
						],
						tbar		:
						[
							{
								xtype	: 'buttongroup',
								columns	: 3,
								height 	: 70,
								title	: 'Filtro Contabilidad',
								items	:
								[
									{
										xtype		: 'panel',
										border		: false,
										width		: 150,
										bodyStyle 	: 'background-color:rgba(255,255,255,0);',
										autoLoad    :
										{
											url		: '../funciones_globales/filtros/filtro_niif.php',
											scripts	: true,
											nocache	: true,
											params	:
											{
												opc               : "consultarCuentasColgaap",
												tabla 			  : 'asientos_colgaap',
												imprimeVarPhp     : 'id_documento : "'+id_documento+'",type_document: "POS", tipo_documento_extendido: "Punto de Venta", numero_documento:"'+numero_documento+'", filtro_sucursal:"<?php echo $filtro_sucursal; ?>"',
												renderizaBody     : 'true',
												newUrlRender      : 'consulta_pos_cuentas_colgaap/consulta_cuentas_colgaap.php',
											}
										}
									}
								]
							},
							{
		                        xtype       : 'button',
		                        width       : 60,
		                        height      : 56,
		                        text        : 'Imprimir',
		                        scale       : 'large',
		                        iconCls     : 'genera_pdf',
		                        iconAlign   : 'top',
		                        handler     : function(){ imprimirBusqueda() }
		                    },
							{
								xtype		: 'button',
								width 		: 60,
								height 		: 56,
								text		: 'Regresar',
								scale		: 'large',
								iconCls		: 'regresar',
								iconAlign	: 'top',
								handler 	: function(){Win_Ventana_Consultar_cuentas_colgaap_pos.close()}
							},'->',
		                    {
		                        xtype       : "tbtext",
		                        text        : 	'<div class="contenedorSaldos">'
			                        				+'<div id="saldoConsultaCuenta_debito"></div>'
			                        				+'<div id="saldoConsultaCuenta_credito"></div>'
			                        				+'<div id="saldoConsultaCuenta"></div>'
		                        				+'<div>',
		                        scale       : "large",
		                    }
						]
					}
				]
			}).show();
		}

		var winInventori = (objData) => {
			var myalto           = Ext.getBody().getHeight()
			,	myancho          = Ext.getBody().getWidth()
			,	title            = "Items de POS No. "+objData.consecutivo;

			Win_Ventana_Consultar_items = new Ext.Window({
				width		: 715,
				id			: 'Win_Ventana_Consultar_items',
				height		: 430,
				title		: title,
				modal		: true,
				autoScroll	: true,
				closable	: true,
				autoDestroy : true,
				bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
				autoLoad    :
							{
								url		: 'consulta_pos_cuentas_colgaap/consulta_items.php',
								scripts	: true,
								nocache	: true,
								params	:
								{
									id_documento     : objData.id,
									numero_documento : objData.consecutivo,
									tabla            : objData.tabla,
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
								handler 	: function(){Win_Ventana_Consultar_items.close()}
							},'->',
                    		{
                    		    xtype       : "tbtext",
                    		    text        : '<div id="Contenedor_costo_items"></div>',
                    		    scale       : "large",
                    		}
						]
			}).show();
		}

		function Editar_consultarDocumentosPos(id){
		}

		function imprimirBusquedaPricipal(){
			window.open("consulta_pos_cuentas_colgaap/imprimir.php?fecha_inicial=<?php echo $fecha_inicial; ?>&fecha_final=<?php echo $fecha_final; ?>&consulta=principal&filtro_sucursal=<?php echo $filtro_sucursal; ?>");
		}

    </script>
<?php } ?>