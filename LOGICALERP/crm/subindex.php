<?php
	//include("js/js.php");
?>

<script>
//////////////////////////////////   VARIABLES PARA LOS INFORMES  /////////////////////////////////////////
var my_fecha_desde = '<?php $fecha = date("Y-m-d"); echo date("Y-m-d", strtotime("$fecha -5 day")); ?>';
var my_fecha_hasta = '<?php $fecha = date("Y-m-d"); echo $fecha; ?>';
var Tam            = parent.TamVentana();
var myancho        = Tam[0];
var myalto         = Tam[1];
var apuntador_este_gridraro = 2;
var arrayContGlobals = new Array();

var VentanaActi1 = 0;
var VentanaACti2 = 0;


Ext.QuickTips.init();
/////////////////////////////////////////////////////////////////////////////////////////////////////////////

var id_activo
,	randomico_maestro
,	id_intercambio
,	id_intercambio_cotizacion
,	id_intercambio_pedido;

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
						bodyStyle 	: 'background-image:url(../../temas/clasico/images/fondo_cabecera.png);'
					},
					{
						region			: 'center',
						xtype			: 'tabpanel',
						id				: 'tabPanelComercial',
						activeTab		: 0,
						margins			: '0 0 0 0',
						deferredRender	: true,
						border			: false,
						bodyStyle		: 'background-color:<?php echo $_SESSION['COLOR_CONTRASTE'] ?>;',
						items			:
						[
							//DASHBOARD////////////////////////////////////////////////////////////////////////////////////////////////////
							{
								closable	: false,
								autoScroll	: false,
								border		: false,
								title		: 'Dashboard',
								//bodyStyle 	: 'background:url(../../temas/clasico/images/Fondo.png)',
								iconCls 	: 'dashboard',
								items		:
								[
									{
										xtype			: 'panel',
										id				: 'ContenedorDashboard',
										border			: false,
										//bodyStyle 		: 'background:url(../../temas/clasico/images/Fondo.png)',
										autoLoad		:
										{
											url		: 'dashboard.php',//'logistico.php',
											scripts	: true,
											nocache	: true,
											params	:
												{
													periodo			: 'dia'
												}
										}
									}
								]/*,
								tbar		:
								[
									{
										xtype	: 'buttongroup',
										height  : 80,
										style   : 'border:none;',
										columns	: 1,
										title	: 'Opciones',
										items	:
										[
											{
												xtype       : 'button',
												width		: 60,
												height		: 56,
												text        : 'Generar Formato de Visitas',
												tooltip		: 'Genera Formato de Visitas',
												scale       : 'large',
												iconCls     : 'guardar',
												iconAlign   : 'top',
												disabled	: false,
												handler     : function(){ BloqBtn(this);}
											},
										]
									}
								]*/
							},
	/*----------------------------------------------------------------CONFIGURACION DE PROSPECTOS------------------------------------------------------------*/
							{
								closable	: false,
								autoScroll	: false,
								title		: 'Prospectos',
								iconCls 	: 'cliente16',
								bodyStyle 	: 'background:url(../../temas/clasico/images/Fondo.png);',
								items		:
								[
									{
										xtype		: "panel",
										id			: 'contenedor_prospectos',
										border		: false,
										bodyStyle 	: 'background:url(../../temas/clasico/images/Fondo.png); box-shadow: inset 0 4px 6px rgba(0,0,0,.5);',
										autoLoad    : {
											url		:	'../terceros/terceros/prospectos.php',
											scripts	:	true,
											nocache	:	true,
											params	:	{}

										}

									}

								]
							},
	/*----------------------------------------------------------------CONFIGURACION DE CLIENTES------------------------------------------------------------*/
							{
								closable	: false,
								autoScroll	: false,
								title		: 'Terceros',
								iconCls 	: 'cliente16',
								bodyStyle 	: 'background:url(../../temas/clasico/images/Fondo.png);',
								items		:
								[
									{
										xtype		: "panel",
										id			: 'contenedor_terceros',
										border		: false,
										bodyStyle 	: 'background:url(../../temas/clasico/images/Fondo.png); box-shadow: inset 0 4px 6px rgba(0,0,0,.5);',
										autoLoad    : {
											url		:	'../terceros/terceros/terceros.php',
											scripts	:	true,
											nocache	:	true,
											params	:	{}

										}

									}

								]
							},
	/*---------------------------------------------------------------- INFORMES ------------------------------------------------------------*/
							{
								closable	: false,
								autoScroll	: false,
								title		: 'Informes',
								iconCls 	: 'doc16',
								//disabled	: true,
								bodyStyle 	: 'background:url(../../temas/clasico/images/Fondo.png)',
								items		:
								[
									{
										xtype		: "toolbar",
										style 		: 'box-shadow:0 4px 6px rgba(0,0,0,.5);',
										items		:
										[
											{
												xtype	: 'buttongroup',
												columns	:9,
												title	: 'Informes',
												items	:
												[
												{
													text		: 'Informe Prospectos',
													scale		: 'small',
													iconCls		: 'doc',
													iconAlign	: 'top',
													handler		: function(){informe('informe_de_prospectos.php');}

												},{
													text		: 'Informe Objetivos',
													scale		: 'small',
													iconCls		: 'doc',
													iconAlign	: 'top',
													handler		: function(){informe('informe_de_objetivos.php');}
												},{
													text		: 'Excel Objetivos',
													scale		: 'small',
													iconCls		: 'doc',
													iconAlign	: 'top',
													handler		: function(){informe('informe_de_objetivos_listado.php');}
												},{
													text		: 'Informe Actividades',
													scale		: 'small',
													iconCls		: 'doc',
													iconAlign	: 'top',
													handler		: function(){informe('informe_de_actividades.php');}
												},{
													text		: 'formato Visitas',
													scale		: 'small',
													iconCls		: 'doc',
													iconAlign	: 'top',
													handler		: function(){informe('formato_visitas.php');}
												},{
													text		: 'Informe Terceros',
													scale		: 'small',
													iconCls		: 'doc',
													iconAlign	: 'top',
													handler		: function(){informe('terceros.php');}
												},{
													text		: 'Informe Clientes',
													scale		: 'small',
													iconCls		: 'doc',
													iconAlign	: 'top',
													handler		: function(){informe('informe_de_clientes.php');}
												},{
													text		: 'Informe Contactos',
													scale		: 'small',
													iconCls		: 'doc',
													iconAlign	: 'top',
													handler		: function(){informe('informe_de_terceros_contactos.php');}
												},{
													text		: 'Informe Estadistico',
													scale		: 'small',
													iconCls		: 'doc',
													iconAlign	: 'top',
													handler		: function(){informe('informe_de_objetivos_estadistico.php');}
												}
											]
											}
										]
									},
									{
										xtype		: "panel",
										id			: 'contenedor_informes',
										bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
										border		: false,
										//autoheight  : true,
									}
								]

							},
							//AYUDA ///////////////////////////////////////////////////////////////////////////////////////////
							{
								closable	: false,
								autoScroll	: false,
								title		: 'Ayuda',
								iconCls 	: 'help16',
								//disabled	: true,
								bodyStyle 	: 'background:url(../../temas/clasico/images/Fondo.png)',
								items		:
								[
									{
										xtype		: "toolbar",
										style 		: 'box-shadow:0 4px 6px rgba(0,0,0,.5);',
										items		:
										[
											{
												xtype	: 'buttongroup',
												columns	: 5,
												title	: 'Manuales',
												items	:
												[
													{
														text		: 'Creacion de Prospectos <br> y Terceros',
														scale		: 'small',
														iconCls		: 'doc',
														iconAlign	: 'top',
														handler		: function(){abrirManual('../ayuda/crm/creacion_prospectos_terceros.pdf');}
													},
													{
														text		: 'Creacion de Proyectos <br> y Actividades',
														scale		: 'small',
														iconCls		: 'doc',
														iconAlign	: 'top',
														handler		: function(){abrirManual('../ayuda/crm/gestion_prospectos_terceros_crm.pdf');}
													},
													{
														text		: 'Manejo del Calendario',
														scale		: 'small',
														iconCls		: 'doc',
														iconAlign	: 'top',
														handler		: function(){abrirManual('../ayuda/crm/manual_calendario.pdf');}
													},
													{
														text		: 'Seccion de Informes',
														scale		: 'small',
														iconCls		: 'doc',
														iconAlign	: 'top',
														handler		: function(){abrirManual('../ayuda/crm/manual_informes_crm.pdf');}
													},	
													{
														text		: 'Parametrizaciones',
														scale		: 'small',
														iconCls		: 'doc',
														iconAlign	: 'top',
														handler		: function(){abrirManual('../ayuda/crm/parametrizaciones_crm.pdf');}
													},														
													/*{
														text		: 'Informe de Turnos <br />Asignados',
														scale		: 'small',
														iconCls		: 'doc',
														iconAlign	: 'top',
														handler		: function(){informe('indicadores/indicadores_informe_de_turnos_empleados.php');}
													},
													{
														text		: 'Informe de Registros <br />Empleados',
														scale		: 'small',
														iconCls		: 'doc',
														iconAlign	: 'top',
														hidden      : <?php echo $permiso_informe_registros; ?>,
														handler		: function(){informe('indicadores/indicadores_informe_de_registros_empleados.php');}
													}*/
												]
											}
										]
									},
									{
										xtype		: "panel",
										id			: 'contenedor_ayuda',
										bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
										border		: false,
									}
								]								
								
							}
						]
					}
				]
			}
		);
	}
);

function informe(cual){

	Ext.getCmp('contenedor_informes').load(
		{
			url 	:	'../informes/informes/crm/'+cual,
			scripts	:	true,
			nocache	:	true,
			params	:	{modulo:'comercial'}
		}
	);

}

/*---------------------------FUNCION EJECUTA VENTANA GLOBAL CLIENTES PARA EXTRAER DATOS------------------------*/

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
				url		:'BusquedaTerceros.php',
				scripts	:true,
				nocache	:true,
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
		//title		: 'CRM Gestion de la Relacion con el Cliente - Actividades' ,
		modal		: true,
		autoScroll	: false,
		border		: false,
		closable	: false,
		autoDestroy : true,
		//iconCls		: 'actividades16',
		autoLoad	:
		{
			url		:'../crm/actividades.php',
			scripts	:true,
			nocache	:true,
			params	:
					{
						id_intercambio 	: 	Elid_intercambio,
						opcion_objetivo	: 	opcion_objetivo,
						id              : 	id
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
		//title		: 'CRM Gestion de la Relacion con el Cliente - Objetivos o Proyectos' ,
		modal		: true,
		autoScroll	: false,
		border		: false,
		closable	: false,
		autoDestroy : true,
		//iconCls		: 'proyecto16',
		autoLoad	:
		{
			url		:'../crm/objetivos.php',
			scripts	:true,
			nocache	:true,
			params	:
					{
						id_cliente 	: 	cual
					}
		}
	}).show();
}


//////////////////////////// AUTOSIZES DEL MODULO /////////////////////////////////
//Agregar_Autosize_Ext("DivDelTabMaestroAlquileres",1,158,"true","true");
//Agregar_Autosize_Ext("PanelDeRequerimientos",1,1,"true","true");
Agregar_Autosize_Ext('Win_Ventana_CRM',30,30,'true','true');
Agregar_Autosize_Ext('Win_Ventana_CRMObjetivos',30,30,'true','true');


//Agregar_Autosize("ContenedorPrincipalReque",1,1,"true","true");
Agregar_Autosize("contenedorIzq",220,185,"true","true");
Agregar_Autosize("contenedorDer",210,190,"false","true");
///////////////////////////////////////////////////////////////////////////////////

/*-------------- FUNCION PARA COLOCAR LOS ICONOS EN PESTAÑA PEDIDO ---------------*/
/**********************************************************************************/
// Deshabilitado por mejoras en rendimiento
// function iconoEstadoEvento(div,estado_pedido,valor){
//     if(estado_pedido >= valor && valor==2){ div.setAttribute("src","images/add3.png"); }
//     else if(estado_pedido >= valor && valor==3){ div.setAttribute("src","images/add3.png"); }
//     else if(estado_pedido >= valor && valor==4){ div.setAttribute("src","images/add3.png"); }
//     else if(estado_pedido >= valor && valor==5){ div.setAttribute("src","images/add3.png"); }
//     else if(estado_pedido >= valor && valor==7){ div.setAttribute("src","images/add3.png"); }
//     else if(estado_pedido >= valor && valor==8){ div.setAttribute("src","images/add3.png"); }
// }

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

function quitaOptionSelectOrder(select,value){
	var tmsel = select.length;
	for(i = 0;i < tmsel;i++){
		t = select.options[i].value;
		if(t == value){
			select.remove(i);
			localStorage.filtroOrden_itemsRemisionados = '';
		}
		tmsel = select.length;
	}
}

array_funcionarios_Terceros  = new Array();
funcionarios_config_Terceros = new Array();

function windows_upload_file(){ document.getElementById('divPadreModalUploadFile').setAttribute('style','display:block;'); }
function close_ventana_upload_file(){ document.getElementById('divPadreModalUploadFile').setAttribute('style',''); }
//arrayFuncionariosTerceros   = 

//INFORME CONTACTOS POR TERCERO
arrayproveedoresTC            = new Array();
proveedoresConfiguradosTC     = new Array();
checkboxConContactos       	  = "";
checkboxSinContactos       	  = "";

//VER LOS MANUALES DEL MODULO

function abrirManual(cual){
	Ext.getCmp('contenedor_ayuda').load(
		{
			url     : '../ayuda/iframe_ayuda.php',
			scripts : true,
			nocache : true,
			params : { 
				url    : cual,
				width  :'100%',
				height :'92%' 
			}
		}
	);		
}

</script>

