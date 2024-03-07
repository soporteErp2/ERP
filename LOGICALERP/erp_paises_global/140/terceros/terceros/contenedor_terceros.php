<?php
	include("../../../../../configuracion/conectar.php");
	include("../../../../../configuracion/define_variables.php");

	$SQL      = "SELECT * FROM terceros WHERE id=$cual";
	$consulta = mysql_query($SQL,$link);
	while($rowb = mysql_fetch_array($consulta)){ $id_pais=$rowb['id_pais']; }
	if(!isset($id_pais)){$id_pais=0;}

	if(isset($Convierte_Prospecto)){
		$ConvPros = ",Convierte_Prospecto : 'true'";
	}else{
		$ConvPros = "";
	}

?>
<div id="ToolbarDivTerceros"></div>
<div id="ContenedorDivTerceros"></div>
<script>
	var opcion             = '<?php echo $opcion ?>';
	var cual               = '<?php echo $cual ?>';
	var PermisoDatosAsiste = <?php echo user_permisos(91,'false') ?>;


	if(opcion == 'Vagregar'){
		var contactos         = true;
		var emails            = true;
		var direcciones       = true;
		var documentos        = true;
		var ElBtnGuardar      = false;
		var ElBtnActualiza    = true;
		var ElBtnElimina      = true;
		var asiste            = true;
		var ElBtnRetenciones  = true;
		var ElBtnFichaTecnica = true;
	}

	if(opcion == 'Vupdate'){
		var contactos         = false;
		var emails            = false;
		var direcciones       = false;
		var documentos        = false;
		var ElBtnGuardar      = true;
		var ElBtnActualiza    = false;
		var ElBtnElimina      = false;
		var ElBtnRetenciones  = false;
		var ElBtnFichaTecnica = false;
	    if(PermisoDatosAsiste == true || PermisoDatosAsiste == 'true'){ var asiste = false; }else{ var asiste  = true; }

	    if(document.getElementById('MuestraToltip_Terceros_'+cual)){
			var contador		= document.getElementById('MuestraToltip_Terceros_'+cual).innerHTML;
		}else{
			var contador		= 0;
		}
	}

	var myalto  = Ext.getBody().getHeight();
	//var myancho  = Ext.getBody().getWidth();
	var AltoTerceros = myalto - 205;

	var ToolbarTerceros = new Ext.Toolbar({
			renderTo	: 'ToolbarDivTerceros',
			items: [
				'-',
				{
					xtype		: 'button',
					//id			: 'BtnV'.$i.'_'.$this->GrillaName.'',
					text		: 'Guardar',
					scale		: 'large',
					iconCls		: 'guardar',
					hidden		: ElBtnGuardar,
					iconAlign	: 'left',
					handler 	: function(){guardaTerceros();}
				},
				{
					xtype		: 'button',
					//id			: 'BtnV'.$i.'_'.$this->GrillaName.'',
					text		: 'Actualizar',
					scale		: 'large',
					iconCls		: 'guardar',
					hidden		: ElBtnActualiza,
					iconAlign	: 'left',
					handler 	: function(){guardaTerceros();}
				},
				{
					xtype		: 'button',
					//id			: 'BtnV'.$i.'_'.$this->GrillaName.'',
					text		: 'Eliminar',
					scale		: 'large',
					iconCls		: 'eliminar',
					hidden		: ElBtnElimina,
					iconAlign	: 'left',
					handler 	: function(){eliminaTerceros();}
				},
				'-',
				{
					xtype		: 'button',
					id			: 'ElBtnRetenciones',
					text		: 'Retenciones',
					scale		: 'large',
					iconCls		: 'impuestos_articulo',
					hidden		: ElBtnRetenciones,
					iconAlign	: 'left',
					handler 	: function(){retenciones_tercero(cual);}
				},
				'-',
				{
					xtype		: 'button',
					id			: 'ElBtnFichaTecnica',
					text		: 'Ficha Tecnica',
					scale		: 'large',
					iconCls		: 'documentadd',
					hidden		: ElBtnFichaTecnica,
					iconAlign	: 'left',
					handler 	: function(){ventanaFichaTecnica(cual);}
				},
				'-','->','-',
				{
					xtype		: 'button',
					text		: 'Asignar a un<br />Funcionario',
					scale		: 'large',
					iconCls		: 'carpeta_personal',
					//hidden		: ElBtnElimina,
					disabled	: ElBtnElimina,
					iconAlign	: 'left',
					handler 	: function(){VentanaCambiaFuncionario2(cual);}
				},
				'-',
				{
					xtype		: 'button',
					text		: 'CRM - Gestion de la<br />Relacion con el Cliente',
					scale		: 'large',
					iconCls		: 'crm',
					hidden		: ElBtnElimina,
					iconAlign	: 'left',
					handler 	: function(){CRMobjetivos(cual);}
				},
				'-'
			]
	});

	var TabsTerceros = new Ext.TabPanel({
			renderTo	: 'ContenedorDivTerceros',
			border		: false,
			activeTab	: 0,
			items		:
			[
				{
					closable	: false,
					autoScroll	: true,
					height 		: '100',
					title		: 'Datos del Tercero',
					iconCls 	: 'cliente16',
					bodyStyle 	: 'height:'+AltoTerceros+'; overflow-x: hidden; background-color:<?php echo $_SESSION['COLOR_CONTRASTE'] ?>;',
					autoLoad    :
					{
						url		: '../erp_paises_global/<?= $_SESSION['PAIS'] ?>/terceros/terceros/terceros.php',
						scripts	: true,
						nocache	: true,
						params	:
						{
							opcion				: opcion,
							id					: cual,
							contador			: contador
							<?php echo $ConvPros ?>
						}
					}
				},
				{

					closable	: false,
					autoScroll	: false,
					disabled	: direcciones,
					title		: 'Sucursales &oacute; Direcciones',
					iconCls 	: 'sucursales16',
					bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_CONTRASTE'] ?>;',
					autoLoad    :
					{
						url		: '../erp_paises_global/<?= $_SESSION['PAIS'] ?>/terceros/terceros/direcciones.php',
						scripts	: true,
						nocache	: true,
						params	:
						{
							elid    : cual,
							id_pais : <?php echo $id_pais; ?>
						}
					}
				},
				{
					closable	: false,
					autoScroll	: false,
					disabled	: contactos,
					title		: 'Contactos Comerciales',
					iconCls 	: 'user16',
					bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_CONTRASTE'] ?>;',
					autoLoad    :
					{
						url		: '../erp_paises_global/<?= $_SESSION['PAIS'] ?>/terceros/terceros/contactos.php',
						scripts	: true,
						nocache	: true,
						params	: { elid : cual }
					}
				},
				{

			    	closable	: false,
			    	autoScroll	: false,
			    	disabled	: emails,
			    	title		: 'E-mails',
			    	iconCls 	: 'email16',
			    	bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_CONTRASTE'] ?>;',
			    	autoLoad    :
			    				{
			    					url		:	'../erp_paises_global/<?= $_SESSION['PAIS'] ?>/terceros/terceros/emails.php',
			    					scripts	:	true,
			    					nocache	:	true,
			    					params	:	{
			    									elid		:	cual
			    								}

			    				}
			    },
				{
					closable	: false,
					autoScroll	: false,
					disabled	: documentos,
					title		: 'Documentos',
					iconCls 	: 'doc16',
					bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_CONTRASTE'] ?>;',
					autoLoad    :
					{
						url		: '../erp_paises_global/<?= $_SESSION['PAIS'] ?>/terceros/terceros/documentos.php',
						scripts	: true,
						nocache	: true,
						params	: { elid : cual }
					}
				}
			]
	});
</script>