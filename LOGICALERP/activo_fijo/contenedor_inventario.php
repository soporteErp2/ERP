<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");
?>

<div id="ToolbarDivInventario"></div>
<div id="ContenedorDivInventario"></div>
<script>

	var opcion             = '<?php echo $opcion ?>';
	var cual               = '<?php echo $cual ?>';
	var PermisoDatosAsiste = <?php echo user_permisos(91,'false') ?>;


	if(opcion == 'Vagregar'){
		var contactos 		= true
		,	direcciones 	= true
		,	documentos 		= true
		,	ElBtnGuardar	= false
		,	ElBtnActualiza	= true
		,	ElBtnElimina	= true
	    ,	asiste          = true;
	}

	if(opcion == 'Vupdate'){

		var contactos		= false
		,	direcciones		= false
		,	documentos		= false
		,	ElBtnGuardar	= true
		,	ElBtnActualiza	= false
		,	ElBtnElimina	= true
		,	contador		= document.getElementById('MuestraToltip_ActivosFijos_'+cual).innerHTML;
	    if(PermisoDatosAsiste == true || PermisoDatosAsiste == 'true'){ var asiste = false; }else{ var asiste  = true; }
	}

    var myancho = Ext.getBody().getWidth();
	var myalto  = Ext.getBody().getHeight();
	//var myancho  = Ext.getBody().getWidth();
	var AnchoInventario= myancho -500;
	var AltoInventario = myalto - 185;

	var ToolbarInventario = new Ext.Toolbar({
			renderTo	: 'ToolbarDivInventario',
			items: [
				'-',
				{
					xtype		: 'button',
					text		: 'Guardar',
					scale		: 'large',
					iconCls		: 'guardar',
					hidden		: ElBtnGuardar,
					iconAlign	: 'left',
					handler 	: function(){guardaActivosFijos(); }
				},
				{
					xtype		: 'button',
					text		: 'Actualizar',
					id  		: 'BtnV_Inventario',
					scale		: 'large',
					iconCls		: 'guardar',
					hidden		: ElBtnActualiza,
					iconAlign	: 'left',
					handler 	: function(){guardaActivosFijos();}
				},'-',
				{
					xtype		: 'button',
					text		: 'Eliminar',
					id  		: 'BtnV0_Inventario',
					scale		: 'large',
					iconCls		: 'eliminar',
					hidden		: ElBtnElimina,
					iconAlign	: 'left',
					handler 	: function(){eliminaActivosFijos();}
				}
			]
	});

	var TabsInventario = new Ext.TabPanel({
			renderTo	: 'ContenedorDivInventario',
			border		: false,
			activeTab	: 0,
			items		:
			[
				{
					closable	: false,
					autoScroll	: true,
					/*autoHeight	: true, */
					height 		: '100',
					width 		: '100',
					title		: 'Activo Fijo',
					iconCls 	: 'cliente16',
					bodyStyle 	: 'width:'+AnchoInventario+';height: '+AltoInventario+'; overflow-x: hidden; background-color:<?php echo $_SESSION['COLOR_CONTRASTE'] ?>;',
					autoLoad    :
					{
						url		: 'inventario.php',
						scripts	: true,
						nocache	: true,
						params	:
						{
							opcion           : opcion,
							id               : cual,
							contador         : contador,
							filtro_empresa   : '<?php echo $filtro_empresa ?>',
							filtro_sucursal  : '<?php echo $filtro_sucursal ?>',
							filtro_ubicacion : '<?php echo $filtro_ubicacion ?>'
						}
					}
				} //,
				// {

				// 	closable	: false,
				// 	autoScroll	: false,
				// 	disabled	: documentos,
				// 	title		: 'Documentos',
				// 	iconCls 	: 'doc16',
				// 	bodyStyle 	: 'width:'+AnchoInventario+';height: '+AltoInventario+'; overflow-x: hidden; background-color:<?php echo $_SESSION['COLOR_CONTRASTE'] ?>;',
				// 	autoLoad    :
				// 	{
				// 		url		: 'inventario_documentos.php',
				// 		scripts	: true,
				// 		nocache	: true,
				// 		params	:
				// 		{
				// 			elid             : cual,
				// 			filtro_empresa   : '<?php echo $filtro_empresa ?>',
				// 			filtro_sucursal  : '<?php echo $filtro_sucursal ?>',
				// 			filtro_ubicacion : '<?php echo $filtro_ubicacion ?>'
				// 		}
				// 	}

				// }
			]
		});

</script>