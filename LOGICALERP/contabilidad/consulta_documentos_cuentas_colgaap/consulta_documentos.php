<script type="module" src="../web_components/data-table/DataTable.js"></script>

<data-table></data-table>
<?php

exit;	

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

	$whereFecha    = "";
	$whereSucursal = "";
	if($fecha_inicial != '' && $fecha_final != ''){ $whereFecha = "AND fecha BETWEEN  '$fecha_inicial' AND '$fecha_final'"; }
	else if($fecha_inicial != ''){ $whereFecha = "AND fecha >=  '$fecha_inicial'"; }
	else if($fecha_final != ''){ $whereFecha = "AND fecha <=  '$fecha_final'"; }

	$whereAsientos  = "";
	$tabla_asientos = 'asientos_colgaap';

	if ($contabilidad=='niif'){ $tabla_asientos='asientos_niif'; }

	if($filtro_sucursal > 0) $whereSucursal = "AND id_sucursal='$filtro_sucursal'";
	if($tipo_documento != "") $whereAsientos  = "AND tipo_documento='$tipo_documento'";

	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'consultarDocumentos';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= $tabla_asientos;		//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_empresa='$id_empresa' $whereSucursal $whereAsientos AND tipo_documento<>'POS' $whereFecha";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA
			$grilla->GroupBy 			= 'id_documento, tipo_documento';
			$grilla->OrderBy 			= 'id DESC';
		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'false';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			// $grilla->Ancho		 		= 700;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->Alto		 		= 350;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->QuitarAncho		= 85;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'consecutivo_documento,tipo_documento,tipo_documento_extendido,fecha,nit_tercero,tercero';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Fecha','fecha',80);
			$grilla->AddRowImage('','<div style="float:left" id="tipo_documento_consultarDocumentos_[id]">[tipo_documento]</div><div style="display:none" id="id_documento_consultarDocumentos_[id]">[id_documento]</div><div style="display:none" id="consecutivo_consultarDocumentos_[id]">[consecutivo_documento]</div><div style="display:none" id="id_tercero_consultarDocumentos_[id]">[id_tercero]</div><div style="display:none" id="fecha_documento_consultarDocumentos_[id]">[fecha]</div>',40);
			$grilla->AddRowImage('Consecutivo','<div id="div_consultarDocumentos_consecutivo_documento_[id]" title="[consecutivo_documento]">[consecutivo_documento]</div>',150);
			$grilla->AddRowImage('Documento','<div id="div_consultarDocumentos_tipo_documento_extendido_[id]" title="[tipo_documento_extendido]">[tipo_documento_extendido]</div>',120);
			$grilla->AddRowImage('Nit','<div title="[nit_tercero]">[nit_tercero]</div>',120);
			$grilla->AddRowImage('Tercero','<div title="[tercero]">[tercero]</div>',210);
			$grilla->AddRowImage('Sucursal','<div id="div_consultarDocumentos_sucursal_[id]" title="[sucursal]">[sucursal]</div>',150);

		//CONFIGURACION CSS X COLUMNA
			$grilla->AddColStyle('consecutivo_documento','text-align:right; width:75px !important; padding-right:5px');
			$grilla->AddColStyle('nit_tercero','text-align:right; width:115px !important; padding-right:5px');

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
			var myalto                   = Ext.getBody().getHeight()
			,	myancho                  = Ext.getBody().getWidth()
			,	sucursal                 = document.getElementById("div_consultarDocumentos_sucursal_"+id).innerHTML
			,	id_sucursal              = document.getElementById("filtro_sucursal_panel_filtro_sucursal").value
			,	fecha_documento          = document.getElementById("fecha_documento_consultarDocumentos_"+id).innerHTML
			,	tipo_documento           = document.getElementById('tipo_documento_consultarDocumentos_'+id).innerHTML
			,	id_documento             = document.getElementById('id_documento_consultarDocumentos_'+id).innerHTML
			,	consecutivo              = document.getElementById('consecutivo_consultarDocumentos_'+id).innerHTML
			,	id_tercero               = document.getElementById('id_tercero_consultarDocumentos_'+id).innerHTML
			,	tipo_documento_extendido = document.getElementById("div_consultarDocumentos_tipo_documento_extendido_"+id).innerHTML
			,   numero_documento         = document.getElementById("div_consultarDocumentos_consecutivo_documento_"+id).innerHTML
			,	title                    = tipo_documento_extendido+" No. "+numero_documento;

			var imprimeVarPhp = 'sucursal 					: "'+sucursal+'"'
								+',id_sucursal              : "'+id_sucursal+'"'
								+',fecha_documento          : "'+fecha_documento+'"'
								+',id_documento             : "'+id_documento+'"'
								+',consecutivo_documento    : "'+consecutivo+'"'
								+',tipo_documento           : "'+tipo_documento+'"'
								+',tipo_documento_extendido : "'+tipo_documento_extendido+'"'
								+',numero_documento         : "'+numero_documento+'"'
								+',id_tercero               : "'+id_tercero+'"';

			var hidenBoton = (tipo_documento == 'FV' || tipo_documento == 'FC' || tipo_documento == 'NDFV' || tipo_documento == 'NDFC')? false: true;

			Win_Ventana_Consultar_cuentas_colgaap = new Ext.Window({
				height		: myalto - 80,
				width		: myancho - 70,
				id			: 'Win_Ventana_Consultar_cuentas_colgaap',
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
										height		: 56,
										bodyStyle 	: 'background-color:rgba(255,255,255,0);',
										autoLoad    :
										{
											url		: '../funciones_globales/filtros/filtro_niif.php',
											scripts	: true,
											nocache	: true,
											params	:
											{
												opc           : "consultarCuentasColgaap",
												tabla         : 'asientos_colgaap',
												imprimeVarPhp : imprimeVarPhp,
												renderizaBody : 'true',
												newUrlRender  : 'consulta_documentos_cuentas_colgaap/consulta_cuentas_colgaap.php',
											}
										}
									}
								]
							},
							{
		                        xtype       : 'button',
		                        width       : 60,
		                        height      : 56,
		                        id 			: 'btnEditarContabilizacion',
		                        text        : 'Editar',
		                        scale       : 'large',
		                        iconCls     : 'edit',
		                        iconAlign   : 'top',
		                        hiden    	: true,
		                        handler     : function(){ editarCuentasDocumento() }
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
								handler 	: function(){Win_Ventana_Consultar_cuentas_colgaap.close()}
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

		function imprimirBusquedaPricipal(){
			// var tipo_cuenta  = document.getElementById('filtro_tipo_cuenta').value;
			var tipo_documento  = document.getElementById('filtro_documento').value;
			var fecha_inicial   = document.getElementById('filtroFechaInicial').value;
			var fecha_final     = document.getElementById('filtroFechaFinal').value;
			var fecha_final     = document.getElementById('filtroFechaFinal').value;
			var filtro_sucursal = document.getElementById('filtro_sucursal_panel_filtro_sucursal').value;


			var varImprimir = "fecha_inicial="+fecha_inicial
								+"&fecha_final="+fecha_final
								+"&consulta=principal&filtro_sucursal="+filtro_sucursal
								+"&filtro_busqueda="+filtroBusqueda
								+"&tipo_documento="+tipo_documento;
								// +"&tipo_cuenta="+tipo_cuenta


			window.open("consulta_documentos_cuentas_colgaap/imprimir.php?"+varImprimir);
		}

    </script>
<?php } ?>
