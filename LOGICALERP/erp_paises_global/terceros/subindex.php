<script>
	//////////////////////////////////   VARIABLES PARA LOS INFORMES  /////////////////////////////////////////
	var my_fecha_desde = '<?php $fecha = date("Y-m-d"); echo date("Y-m-d", strtotime("$fecha -5 day")); ?>';
	var my_fecha_hasta = '<?php $fecha = date("Y-m-d"); echo $fecha; ?>';
	var Tam                 = parent.TamVentana();
	var myancho             = Tam[0];
	var myalto              = Tam[1];
	apuntador_este_gridraro = 2;

	Ext.QuickTips.init();
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////

	Ext.onReady
	(function()
		{
			new Ext.Viewport //TAB PRINCIPAL
			(
				{
				layout		: 'border',
				style 		: 'font-family:Tahoma, Geneva, sans-serif; font-size:12px;',
				items:
					[
						{
							region		: 'north',
							xtype		: 'panel',
							height		: 33,
							border		: false,
							margins		: '0 0 0 0',
							html		: '<div class="DivNorth" style="float:left;"><?php echo $_SESSION["NOMBREEMPRESA"] ." - ". $_SESSION["NOMBRESUCURSAL"]?></div><div class="DivNorth" style="float:right; text-align:right;"><?php echo $_SESSION["NOMBREFUNCIONARIO"] ?></div>',
							bodyStyle 	: 'background-image:url(../../../temas/clasico/images/fondo_cabecera.png);'
						},
						{
							region			: 'center',
							xtype			: 'panel',
							id				: 'tabPanelComercial',
							margins			: '0 0 0 0',
							border			: false,
							bodyStyle		: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
							autoLoad    : {
								url		: 'terceros/terceros.php',
								scripts	: true,
								nocache	: true,
								params	: { }
							}
						}

						/*{  ESTE CODIGO VA SI LLEVA TABS,
							region			: 'center',
							xtype			: 'tabpanel',
							id				: 'tabPanelComercial',
							//activeTab		: 0,
							margins			: '0 0 0 0',
							deferredRender	: true,
							border			: false,
							bodyStyle		: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
							items			:
							[
							//-------- CONFIGURACION DE TERCEROS ------------------------------------------------------------
								{
									closable	: false,
									autoScroll	: false,
									title		: 'Terceros',
									iconCls 	: 'cliente16',
									bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
									items		:
									[
										{
											xtype		: "panel",
											id			: 'contenedor_terceros',
											border		: false,
											bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
											autoLoad    : {
												url		: 'terceros/terceros.php',
												scripts	: true,
												nocache	: true,
												params	: { }
											}
										}
									]
								}
							]
						}*/
					]
				}
			);
		}
	);

	function windows_upload_file(){ document.getElementById('divPadreModalUploadFile').setAttribute('style','display:block;'); }
    function close_ventana_upload_file(){ document.getElementById('divPadreModalUploadFile').setAttribute('style',''); }

	/*
	variables funcion VentanaGlobalClientes
	nombre_modulo 	--> evitar que esta grilla se llame con el mismo nombre en varios modulos
	titulo 			--> titulo de la ventana de terceros-clientes
	campos 			--> Array con los campos a los cuales devolver informacion cargada en otros input
						NOTA nombre_campo_grilla_extraer_dato_ DEBE SIEMPRE TERMINAR CON "_" PARA QUE SE LE CONCATENE EL ID	EXCEPTO EL PRIMERO QUE SOLO TRAE EL ID
						'id_campo:id, nombre_campo_mostrar_dato:nombre_campo_grilla_extraer_dato_,...todos los campos que se quieran devolver'
	condicional 	--> aumentar condicional al where de la grilla
	javascript 		--> javascript que se desea ejecutar se pueden hacer llamados de funcion tambien
	*/


	/*-------------------------------- FUNCIONES GLOBALES DOCUMENTO MAESTRO ------------------------------*/
	/******************************************************************************************************/
	function VentanaGlobalClientes(nombre_modulo,titulo,campos,condicional,javascript){
		var myalto  = Ext.getBody().getHeight();
		var myancho  = Ext.getBody().getWidth();

	    campos =  Base64.encode(campos);  //FUNCION DE ENCODE EN MyFunction.js codifica en 64 desde javascript y ya en la funcion se descodifica con PHP

		if (nombre_modulo=="Win_Ventana_Terceros_Global") {

			Win_Ventana_Terceros_Global = new Ext.Window({
				id			: 'Win_Ventana_Terceros_Global',
				width		: 900,// myancho-100,
				height		: myalto - 100,
				title		: titulo,
				modal		: true,
				autoScroll	: false,
				closable	: true,
				autoDestroy : true,
				autoLoad	:
				{
					url		: 'BusquedaTerceros.php',
					scripts	: true,
					nocache	: true,
					params	:
					{
						nombreVentana	: nombre_modulo,
						campos			: campos,
						condicional		: condicional,
						javascript 		: javascript
					}
				}
			}).show();
		};
	}

	/*------------------------------------------ MODULO CRM ----------------------------------------------*/
	/******************************************************************************************************/
	function CRM(cual,id){

		if(cual=='maestro'){var Elid_intercambio = randomico_maestro; var id='false'; var opcion_objetivo = 'documento'}
		if(cual=='cotizacion'){var Elid_intercambio = id_intercambio_cotizacion ; var id='false'; var opcion_objetivo = 'documento'}
		if(cual=='personalizado'){var Elid_intercambio = 'false'; var opcion_objetivo = 'personalizado'}

		var myalto  = Ext.getBody().getHeight();
		var myancho  = Ext.getBody().getWidth();

		Win_Ventana_CRM = new Ext.Window({
			id			: 'Win_Ventana_CRM',
			width		: myancho - 80,// myancho-100,
			height		: myalto - 80,
			title		: 'CRM Gestion de la Relacion con el Cliente - Actividades' ,
			modal		: true,
			autoScroll	: false,
			closable	: true,
			autoDestroy : true,
			//iconCls		: 'actividades16',
			autoLoad	:
			{
				url		: '../crm/actividades.php',
				scripts	: true,
				nocache	: true,
				params	:
				{
					id_intercambio  : Elid_intercambio,
					opcion_objetivo : opcion_objetivo,
					id              : id
				}
			}
		}).show();
	}

	function CRMobjetivos(cual){

		var myalto  = Ext.getBody().getHeight();
		var myancho  = Ext.getBody().getWidth();

		Win_Ventana_CRMObjetivos = new Ext.Window({
			id			: 'Win_Ventana_CRMObjetivos',
			width		: myancho - 30,// myancho-100,
			height		: myalto - 30,
			title		: 'CRM Gestion de la Relacion con el Cliente - Objetivos o Proyectos' ,
			modal		: true,
			autoScroll	: false,
			closable	: true,
			autoDestroy : true,
			iconCls		: 'proyecto16',
			autoLoad	:
			{
				url		: '../crm/objetivos.php',
				scripts	: true,
				nocache	: true,
				params	: { id_cliente : cual }
			}
		}).show();
	}

	//////////////////////////// AUTOSIZES DEL MODULO /////////////////////////////////
	Agregar_Autosize_Ext('Win_Ventana_CRM',30,30,'true','true');
	Agregar_Autosize_Ext('Win_Ventana_CRMObjetivos',30,30,'true','true');


	//Agregar_Autosize("ContenedorPrincipalReque",1,1,"true","true");
	Agregar_Autosize("contenedorIzq",220,185,"true","true");
	Agregar_Autosize("contenedorDer",210,190,"false","true");
	///////////////////////////////////////////////////////////////////////////////////

	//FILTRA LA GRILLA POR COMERCIAL ASIGNADO A LOS PROSPECTOS/TERCEROS
	function recargaGrillaAsignado(grilla){

		var id_asignado     = document.getElementById('idAsignado_'+grilla).value;
		var nombre_asignado = document.getElementById('nombreAsignado_'+grilla).value;

		if(nombre_asignado == ''){
			id_asignado = '';

			document.getElementById('idAsignado_'+grilla).value = '';
		}

		if(grilla == 'prospectos'){
			div          = 'contenedor_prospectos';
		}
		else if(grilla == 'terceros'){
			div          = 'contenedor_terceros';
			if(document.getElementById(div) == null){
				div = 'tabPanelComercial';
			}
		}

		//alert(grilla);

		Ext.get(div).load({
			url     : '../terceros/terceros/'+grilla+'.php',
			scripts : true,
			nocache : true,
			params  :
			{
				id_asignado     : id_asignado,
				nombre_asignado : nombre_asignado,
			}
		});

	}

</script>

