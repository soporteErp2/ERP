<script>
// var PermisoAgregarEmpleados = <?php echo user_permisos(2,'true') ?>;
// var PermisoEditarEmpleados = <?php echo user_permisos(11,'true') ?>;
// var PermisoCargos 	 = <?php echo user_permisos(5,'true') ?>;
// var PermisoContratos = <?php echo user_permisos(6,'true') ?>;
// var PermisoTiposDocumentos = <?php echo user_permisos(10,'true') ?>;

// var PermisoConfigCorreo = <?php echo user_permisos(12,'true') ?>;
// var PermisoConfigContabilidad = <?php echo user_permisos(13,'true') ?>;

var PermisoAgregarEmpleados = false;
var PermisoEditarEmpleados = false;
var PermisoCargos 	 = false;
var PermisoContratos = false;
var PermisoTiposDocumentos = false;

var PermisoConfigCorreo = false;
var PermisoConfigContabilidad = false;

var id_empleado_adjuntar_equipo;

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
					// {
					// 	region		: 'north',
					// 	xtype		: 'panel',
					// 	height		: 33,
					// 	border		: false,
					// 	margins		: '0 0 0 0',
					// 	html		: '<div class="DivNorth" style="float:left;"><?php echo $_SESSION["NOMBREEMPRESA"] ." - ". $_SESSION["NOMBRESUCURSAL"]?></div><div class="DivNorth" style="float:right; text-align:right;"><?php echo $_SESSION["NOMBREFUNCIONARIO"] ?></div>',
					// 	bodyStyle 	: 'background-image:url(../../temas/clasico/images/fondo_cabecera.png);'
					// },
					{
						region			: 'center',
						xtype			: 'tabpanel',
						margins			: '0 0 0 0',
						deferredRender	: true,
						border			: false,
						activeTab		: 0,
						bodyStyle 		: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
						items			:
						[
							{
								closable	: false,
								autoScroll	: false,
								title		: 'Administracion de Empleados',
								iconCls 	: 'user16',
								bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
								items		:
								[
									{
										xtype		: "panel",
										id			: 'contenedor_Empleados',
										border		: false,
										bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
										// autoLoad    :
										// {
										// 	url		:	'empleados.php',
										// 	scripts	:	true,
										// 	nocache	:	true
										// }
									}
								],
								tbar		:
									[
										{
											xtype		: 	'button',
											id			: 	'btnAgregaEmpleado',
											text		:	'Agregar Empleado',
											scale		: 	'large',
											iconCls		: 	'adduser',
											iconAlign	: 	'top',
											disabled	:	PermisoAgregarEmpleados,
											handler 	:	function(){BloqBtn(this);Agregar_Empleado();}
										},
										{
											xtype		: 	'button',
											text		: 	'Administrar Cargos',
											scale		: 	'large',
											iconCls		: 	'cargo',
											iconAlign	: 	'top',
											disabled	:	PermisoCargos,
											handler 	: 	function(){BloqBtn(this);Administracion_Cargos();}
										},
										// {
										// 	xtype	: 'buttongroup',
										// 	columns	: 4,
										// 	title	: 'Opciones',
										// 	items	:
										// 	[
										// 		{
										// 			xtype		: 	'button',
										// 			id			: 	'btnAgregaEmpleado',
										// 			text		:	'Agregar Empleado',
										// 			scale		: 	'large',
										// 			iconCls		: 	'adduser',
										// 			iconAlign	: 	'top',
										// 			disabled	:	PermisoAgregarEmpleados,
										// 			handler 	:	function(){BloqBtn(this);Agregar_Empleado();}
										// 		},
										// 		{
										// 			xtype		: 	'button',
										// 			//id			: 'btn2',
										// 			text		: 	'Administrar Contratos',
										// 			scale		: 	'large',
										// 			iconCls		: 	'contrato',
										// 			iconAlign	: 	'top',
										// 			disabled	:	PermisoContratos,
										// 			handler 	: 	function(){BloqBtn(this);Administracion_Contratos();}
										// 		},
										// 		{
										// 			xtype		: 	'button',
										// 			text		: 	'Administrar Cargos',
										// 			scale		: 	'large',
										// 			iconCls		: 	'cargo',
										// 			iconAlign	: 	'top',
										// 			disabled	:	PermisoCargos,
										// 			handler 	: 	function(){BloqBtn(this);Administracion_Cargos();}
										// 		},
										// 		{
										// 			xtype		: 	'button',
										// 			text		: 	'Tipos de Documento',
										// 			scale		: 	'large',
										// 			iconCls		: 	'tipodocumentos',
										// 			iconAlign	: 	'top',
										// 			disabled	:	PermisoTiposDocumentos,
										// 			handler 	: 	function(){BloqBtn(this);Administracion_TiposDocumentos();}
										// 		}

										// 	]
										// },
										// {
										// 	xtype	: 'buttongroup',
										// 	columns	: 3,
										// 	title	: 'Busqueda',
										// 	items	:
										// 	[
										// 		{
										// 			xtype		: 'panel',
										// 			border		: false,
										// 			width		: 240,
										// 			height		: 56,
										// 			bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_CONTRASTE'] ?>;',
										// 			html		: '<div style="margin:18 0 0 15"><input class="myfieldBusqueda" name="busqueda_empleado" type="text" id="busqueda_empleado" style="width:210px" onKeyUp="ValEnterBusqEmpleados(event)" onFocus="this.value=\'\'" /></div>',
										// 		}
										// 	]
										// },
										// '-',
										{
											xtype     : 'buttongroup',
											columns   : 1,
											width : 220,
											title     : 'Filtro Sucursal',
											// bodyStyle : 'border-left:1px solid #8DB2E3;',
											id : 'panel_sucursal',
											items     :
											[
												{
													xtype		: 'panel',
													border		: false,
													width		: 215,
													height		: 56,
													bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_CONTRASTE'] ?>;',
													autoLoad    :
													{
														url		:	'filtros_empresa.php',
														scripts	:	true,
														nocache	:	true
													}
												}
											]
										}
									]
							},
							// {
							// 	closable	: false,
							// 	autoScroll	: false,
							// 	title		: 'Roles y Permisos',
							// 	iconCls 	: 'doc16',
							// 	//disabled	: true,
							// 	bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
							// 	autoLoad	: {
							// 				url 	: '../panel_de_control/roles.php',
							// 				scripts	: true,
							// 				nocache	: true,
							// 				params	: {modulo:'Usuarios'}
							// 	}
							// }
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
			url 	:	'../informes/informes/personal/'+cual,
			scripts	:	true,
			nocache	:	true,
			params	:	{modulo:'personal'}
		}
	);

}


///////////////////////////////////////////////////////////////////////////////
// ROLES
///////////////////////////////////////////////////////////////////////////////


///////////////////////////////////////////////////////////////////////////////
// EMPLEADOS
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
// TIPOS DOCUMENTOS
///////////////////////////////////////////////////////////////////////////////

function Administracion_TiposDocumentos(){
	var myalto2  = Ext.getBody().getHeight();
	var myancho2  = Ext.getBody().getWidth();
	Win_Administracion_TiposDocumentos = new Ext.Window
	(
		{
			width		: 500,
			//id			: 'contenedor_Cargo',
			height		: 300,
			title		: 'Administracion de Tipos de Documentos',
			modal		: true,
			autoScroll	: false,
			autoDestroy : true,
			items		:
			[
				{
					xtype	: 	'panel',
					id		:	'contenedor_Documentos',
					border		: false,
					bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
					autoLoad	:
					{
						url		:'documentos.php',
						scripts	:true,
						nocache	:true,
						params	:
								{

								}
					}
				}
			],
			tbar		:
			[
				{
						xtype		: 'button',
						text		: 'Agregar tipo<br />de Documento',
						scale		: 'large',
						iconCls		: 'tipodocumentosadd',
						iconAlign	: 'left',
						handler 	: function(){BloqBtn(this); Agregar_Documentos()}
				},'-'
			]
		}
	).show();
}

///////////////////////////////////////////////////////////////////////////////
// CARGOS
///////////////////////////////////////////////////////////////////////////////
function Administracion_Cargos(){
	var myalto2  = Ext.getBody().getHeight();
	var myancho2  = Ext.getBody().getWidth();
	Win_Administracion_Cargos = new Ext.Window
	(
		{
			width		: 500,
			//id			: 'contenedor_Cargo',
			height		: 300,
			title		: 'Administracion de Cargos',
			modal		: true,
			autoScroll	: false,
			autoDestroy : true,
			items		:
			[
				{
					xtype	: 	'panel',
					id		:	'contenedor_Cargo',
					border		: false,
					bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
					autoLoad	:
					{
						url		:'cargos.php',
						scripts	:true,
						nocache	:true,
						params	:
								{

								}
					}
				}
			],
			tbar		:
			[
				{
						xtype		: 'button',
						text		: 'Agregar Cargo',
						scale		: 'large',
						iconCls		: 'addcargo',
						iconAlign	: 'left',
						handler 	: function(){BloqBtn(this); Agregar_Cargo()}
				},'-'
			]
		}
	).show();
}

///////////////////////////////////////////////////////////////////////////////
// CONTRATOS
///////////////////////////////////////////////////////////////////////////////
function Administracion_Contratos(){
	var myalto  = Ext.getBody().getHeight();
	var myancho  = Ext.getBody().getWidth();
	var filtro_empresa = document.getElementById('filtro_empresa').value;
	var filtro_sucursal = document.getElementById('filtro_sucursal').value;

	Win_Administracion_Contratos = new Ext.Window
	(
		{
			width		: 650,
			id			: 'Win_Administracion_Contratos',
			height		: 400,
			title		: 'Administracion de Contratos',
			modal		: true,
			autoScroll	: false,
			autoDestroy : true,
			items		:
			[
				{
					xtype	: 	'panel',
					id		:	'contenedor_Documento',
					border		: false,
					bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
					autoLoad	:
					{
						url		:'contratos.php',
						scripts	:true,
						nocache	:true,
						params	:{
									filtro_empresa	:	filtro_empresa,
									filtro_sucursal	:	filtro_sucursal
								}
					}
				}
			]
		}
	).show();
}



</script>