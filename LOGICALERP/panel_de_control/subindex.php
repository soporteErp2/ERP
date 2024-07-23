<?php
	include("../configuracion/conectar.php");

	$permiso_configuracion_global   = (user_permisos(63,'false') == 'true')? 'false' : 'true';
	$permiso_configuracion_sucursal = (user_permisos(73,'false') == 'true')? 'false' : 'true';
?>

<script>
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
									autoScroll	: true,
									title		: 'Configuracion Global',
									iconCls 	: 'global16',
									disabled    : <?php echo $permiso_configuracion_global; ?>,
									bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
									items		:
									[
										{
											xtype		: "panel",
											id			: 'contenedor_configuracion_global',
											border		: false,
											bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
											autoLoad	:
											{
												url		: 'panel_global.php',
												scripts	: true,
												nocache	: true
											}
										}
									]
								},
								{
									closable	: false,
									autoScroll	: true,
									title		: 'Configuracion Sucursal',
									iconCls 	: 'empresa16',
									disabled    : <?php echo $permiso_configuracion_sucursal; ?>,
									bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
									items		:
									[
										{
											xtype		: "panel",
											id			: 'contenedor_configuracion_sucursal',
											border		: false,
											bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
										}
									],
									tbar		:
									[
										{
											xtype   : 'buttongroup',
											columns : 3,
											title   : 'Filtro Sucursal',
											items   :
											[
												{
													xtype		: 'panel',
													border		: false,
													width		: 210,
													height		: 46,
													bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
													autoLoad    :
													{
														url		: '../funciones_globales/filtros/filtro_unico_sucursal.php',
														scripts	: true,
														nocache	: true,
														params  :
												        {
															opc        : 'panel_sucursal',
															contenedor : 'contenedor_configuracion_sucursal',
															url_render : 'panel_sucursal.php',
												        }
													}
												}
											]
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

    // FUNCION PARA ACTUALIZAR LA FILA DE LA VENTANA DEL DOCUMENTO CRUCE
    function actualiza_fila_ventana_busqueda(id) {
        var div    = '';
        var divImg = '';
        var divEvt = '';

        // MOSTRAR LA FILA DE LA VENTANA DEL DOCUMENTO CRUCE COMO ELIMINADO
        if (document.getElementById("div_nominaConceptosBuscar_codigo_"+id)) {
			div    = document.getElementById('item_nominaConceptosBuscar_'+id);
			divImg = document.getElementById('MuestraToltip_nominaConceptosBuscar_'+id);
			divEvt = document.getElementById('MuestraToltip_General_nominaConceptosBuscar_'+id);
        }
        else if (document.getElementById("div_grillaPedidoFactura_consecutivo_"+id)) {
			div    = document.getElementById('item_grillaPedidoFactura_'+id);
			divImg = document.getElementById('MuestraToltip_grillaPedidoFactura_'+id);
			divEvt = document.getElementById('MuestraToltip_General_grillaPedidoFactura_'+id);
        }

        if (div) {
        	div.setAttribute('style',div.getAttribute('style')+';color:#999 !important;font-style:italic;background-color:#e5ffe5 !important;');
        }
        if (divEvt) {
        	divEvt.setAttribute('ondblclick','');
        }
    	if (divImg) {
    		divImg.setAttribute('style',divImg.getAttribute('style')+';background-image:url(../../misc/MyGrilla/MyGrillaFondoOk.png);');
    	}
    }

</script>