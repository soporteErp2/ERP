<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");

	$whereTercerosFecha = "";
	if($fecha_inicial != '' && $fecha_final != ''){ $whereTercerosFecha = "AND fecha BETWEEN  '$fecha_inicial' AND '$fecha_final'"; }
	else if($fecha_inicial != ''){ $whereTercerosFecha = "AND fecha >=  '$fecha_inicial'"; }
	else if($fecha_final != ''){ $whereTercerosFecha = "AND fecha <=  '$fecha_final'"; }

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	$idEmpresa  = $_SESSION['EMPRESA'];
	$idSucursal = $_SESSION['SUCURSAL'];
	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'consultarTerceros';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'asientos_colgaap';		//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_empresa='$idEmpresa' AND id_sucursal='$idSucursal' AND id_tercero > 0 $whereTercerosFecha";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA
			$grilla->GroupBy 			= 'id_tercero';
			$grilla->OrderBy 			= 'id DESC';
		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'false';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 		= 605;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 		= 350;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->QuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			// $grilla->QuitarAlto			= 220;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'consecutivo_documento,tipo_documento,tipo_documento_extendido,fecha,nit_tercero,tercero';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRowImage('Nit','<div style="float:left" id="div_consultarTerceros_nit_tercero_[id]">[nit_tercero]</div><div style="display:none" id="id_tercero_consultarTerceros_[id]">[id_tercero]</div>',100);
			$grilla->AddRow('Tercero','tercero',200);

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

		function Editar_consultarTerceros(id){
			var myalto     = Ext.getBody().getHeight()
			,	myancho    = Ext.getBody().getWidth()
			,	id_tercero = document.getElementById('id_tercero_consultarTerceros_'+id).innerHTML
			,	nit        = document.getElementById("div_consultarTerceros_nit_tercero_"+id).innerHTML
			,	tercero    = document.getElementById("div_consultarTerceros_tercero_"+id).innerHTML
			,	title      = 'Tercero Nit. '+nit+" "+tercero;

			Win_Ventana_Consultar_terceros_cuentas_colgaap = new Ext.Window({
				width		: myancho - 100,
				id			: 'Win_Ventana_Consultar_terceros_cuentas_colgaap',
				height		: 510,
				title		: title,
				modal		: true,
				autoScroll	: true,
				closable	: false,
				autoDestroy : true,
				items		:
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
						title	: 'Filtro Contabilidad',
						items	:
						[
							{
								xtype		: 'panel',
								border		: false,
								width		: 150,
								height		: 56,
								bodyStyle 	: 'background-color:rgba(255,255,0,0);',
								autoLoad    :
								{
									url		: '../funciones_globales/filtros/filtro_niif.php',
									scripts	: true,
									nocache	: true,
									params	:
									{
										opc               : "consultarCuentasColgaap",
										tabla 			  : 'asientos_colgaap',
										imprimeVarPhp     : 'id_tercero : "'+id_tercero+'",nit: "'+nit+'",tercero: "'+tercero+'",fecha_inicial: "<?php echo $fecha_inicial ?>", fecha_final: "<?php echo $fecha_final ?>", ancho_grilla: "'+myancho+'"',
										renderizaBody     : 'true',
										newUrlRender        : 'consulta_terceros_cuentas_colgaap/consulta_terceros_cuentas_colgaap.php',
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
						handler 	: function(){ Win_Ventana_Consultar_terceros_cuentas_colgaap.close() }
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
			}).show();
		}

		function imprimirBusquedaPricipal(){
			window.open("consulta_terceros_cuentas_colgaap/imprimir.php?fecha_inicial=<?php echo $fecha_inicial; ?>&fecha_final=<?php echo $fecha_final; ?>&consulta=principal");
		}

    </script>
<?php } ?>