<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
?>

<div id="ToolbarDivitemsGeneral"></div>
<div id="ContenedorDivitemsGeneral"></div>
<script>

	var opcion = '<?php echo $opcion ?>'
	,	cual   = '<?php echo $cual ?>';

	function copiaitemsGeneral() {
		Ext.Ajax.request({
            url     : 'items/bd/bd.php',
            params  :
            {
				op     : 'copiaBD',
				idItem : cual
            },
            success :function (result, request){

                        if(result.responseText != 'true'){
                        	alert("Error \n"+result.responseText+".\nHa ocurrido un problema en la conexion con la base de datos, si el problema persiste comuniquese con el administrador del sistema.");
                        	Win_Editar_itemsGeneral.close();
                        }
                    },
            failure : function(){ console.log('Error de conexion con el servidor'); }
        });
	}

	if(opcion == 'Vagregar'){
		var contactos 		= true
		,	direcciones 	= true
		,	documentos 		= true
		,	receta 			= true
		,	ElBtnGuardar	= false
		,	ElBtnActualiza	= true
		,	ElBtnElimina	= true;
	}

	if(opcion == 'Vupdate'){

		//COPIA ITEM A INVENTARIO TOTALES POR CADA BODEGA
		Ext.Ajax.request({
            url     : 'items/bd/bd.php',
            params  :
            {
				op     : 'configuracionItemsCuentasInventario',
				idItem : cual
            },
            success :function (result, request){

                        if(result.responseText != 'true'){
                        	// console.log(result.responseText);
                        	// numberError = (result.responseText).replace("false_","");
                        	alert("Error \n"+result.responseText+".\nHa ocurrido un problema en la conexion con la base de datos, si el problema persiste comuniquese con el administrador del sistema.");
                        	Win_Editar_itemsGeneral.close();
                        }
                    },
            failure : function(){ console.log('Error de conexion con el servidor'); }
        });

		var contactos		= false
		,	direcciones		= false
		,	documentos		= false
		,	receta			= false
		,	ElBtnGuardar	= true
		,	ElBtnActualiza	= false
		,	ElBtnElimina	= false
		,	contador		= document.getElementById('MuestraToltip_itemsGeneral_'+cual).innerHTML;

	}

    var myancho = Ext.getBody().getWidth();
	var myalto  = Ext.getBody().getHeight();

	var AnchoitemsGeneral= myancho -500;
	var AltoitemsGeneral = myalto - 185;

	var ToolbaritemsGeneral = new Ext.Toolbar({
		renderTo	: 'ToolbarDivitemsGeneral',
		items:
		[
			'-',
			{
				xtype		: 'button',
				text		: 'Guardar',
				scale		: 'large',
				iconCls		: 'guardar',
				hidden		: ElBtnGuardar,
				iconAlign	: 'left',
				handler 	: function(){ BloqBtn(this); guardaitemsGeneral(); }
			},
			{
				xtype		: 'button',
				text		: 'Actualizar',
				id  		: 'BtnV_itemsGeneral',
				scale		: 'large',
				iconCls		: 'guardar',
				hidden		: ElBtnActualiza,
				iconAlign	: 'left',
				handler 	: function(){ BloqBtn(this); guardaitemsGeneral(); }
			},'-',
			{
				xtype		: 'button',
				text		: 'Eliminar',
				id  		: 'BtnV0_itemsGeneral',
				scale		: 'large',
				iconCls		: 'eliminar',
				hidden		: ElBtnElimina,
				iconAlign	: 'left',
				handler 	: function(){ BloqBtn(this); eliminaitemsGeneral(); }
			}
			,'-',
			{
				xtype		: 'button',
				text		: 'Copiar',
				id  		: 'BtnV2_itemsGeneral',
				scale		: 'large',
				iconCls		: 'doc_sinc',
				hidden		: ElBtnActualiza,
				iconAlign	: 'left',
				handler 	: function(){ BloqBtn(this); copiaitemsGeneral(); }
			}
		]
	});

	var TabsitemsGeneral = new Ext.TabPanel({
		renderTo	: 'ContenedorDivitemsGeneral',
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
				title		: 'itemsGeneral',
				iconCls 	: 'cliente16',
				bodyStyle 	: 'width:'+AnchoitemsGeneral+';height: '+AltoitemsGeneral+'; overflow-x: hidden; background-color:<?php echo $_SESSION['COLOR_CONTRASTE'] ?>;',
				autoLoad    :
				{
					url		: 'items/items.php',
					scripts	: true,
					nocache	: true,
					params	:
					{
						opcion   : opcion,
						id       : cual,
						contador : contador,
					}
				}
			},
			{
				closable	: false,
				autoScroll	: true,
				disabled	: documentos,
				title		: 'Cuentas Colgaap',
				iconCls 	: 'doc16',
				bodyStyle 	: 'width:'+AnchoitemsGeneral+';height: '+AltoitemsGeneral+'; overflow-x: hidden; background-color:<?php echo $_SESSION['COLOR_CONTRASTE'] ?>;',
				autoLoad    :
				{
					url		: 'items/items_cuentas/items_cuentas.php',
					scripts	: true,
					nocache	: true,
					params	: { idItems : cual }
				}

			},
			{
				closable	: false,
				autoScroll	: true,
				disabled	: documentos,
				title		: 'Cuentas Niif',
				iconCls 	: 'doc16',
				bodyStyle 	: 'width:'+AnchoitemsGeneral+';height: '+AltoitemsGeneral+'; overflow-x: hidden; background-color:<?php echo $_SESSION['COLOR_CONTRASTE'] ?>;',
				autoLoad    :
				{
					url		: 'items/items_cuentas/items_cuentas_niif.php',
					scripts	: true,
					nocache	: true,
					params	: { idItems : cual }
				}

			},
			{
				closable   : false,
				autoScroll : false,
				disabled   : receta,
				id         : 'pestana_receta',
				title      : 'Receta',
				iconCls    : 'book_open',
				bodyStyle  : 'width:'+AnchoitemsGeneral+';height: '+AltoitemsGeneral+'; overflow-x: hidden; background-color:<?php echo $_SESSION['COLOR_CONTRASTE'] ?>;',
				autoLoad   :
				{
					url		: 'items/items_recetas/items_recetas.php',
					scripts	: true,
					nocache	: true,
					params	: { elid : cual }
				}

			},
			{
				closable	: false,
				autoScroll	: false,
				disabled	: documentos,
				title		: 'Documentos',
				iconCls 	: 'doc16',
				bodyStyle 	: 'width:'+AnchoitemsGeneral+';height: '+AltoitemsGeneral+'; overflow-x: hidden; background-color:<?php echo $_SESSION['COLOR_CONTRASTE'] ?>;',
				autoLoad    :
				{
					url		: 'items/items_documentos/items_documentos.php',
					scripts	: true,
					nocache	: true,
					params	: { elid : cual }
				}

			}
		]
	});

</script>