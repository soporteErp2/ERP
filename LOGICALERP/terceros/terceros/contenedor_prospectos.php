<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$SQL      = "SELECT * FROM terceros WHERE id=$cual";
	$consulta = mysql_query($SQL,$link);
	while($rowb = mysql_fetch_array($consulta)){ $id_pais=$rowb['id_pais']; }
	if(!isset($id_pais)){$id_pais=0;}

?>
<div id="ToolbarDivProspectos"></div>
<div id="ContenedorDivProspectos"></div>
<script>
var opcion = '<?php echo $opcion ?>';
var cual = '<?php echo $cual ?>';

//var PermisoDatosDocumentosConfidenciales = <?php echo user_permisos(91,'false') ?>;

if(opcion == 'Vagregar'){
	var contactos 		= true;
	var emails          = true;
	var direcciones 	= true;
	var documentos 		= true;
	var ConvertirCliente= true;
	var ElBtnGuardar	= false;			//FALSE VISIBLE
	var ElBtnActualiza	= true;				//TRUE OCULTO
	var ElBtnElimina	= true;
    var asiste          = true;
}

if(opcion == 'Vupdate'){
	var contactos		= false;
	var emails          = false;
	var direcciones		= false;
	var documentos		= true;
	var ConvertirCliente= false;
	var ElBtnGuardar	= true;				//TRUE OCULTO
	var ElBtnActualiza	= false;			//FALSE VISIBLE
	var ElBtnElimina	= false;
	var contador		= document.getElementById('MuestraToltip_Prospectos_'+cual).innerHTML;
	var asiste          = true;

    //if(PermisoDatosDocumentosConfidenciales == true || PermisoDatosDocumentosConfidenciales == 'true'){ var asiste = false; }else{ var asiste  = true; }
}

var myalto  = Ext.getBody().getHeight();
//var myancho  = Ext.getBody().getWidth();
var AltoProspectos = myalto - 205;

var ToolbarProspectos = new Ext.Toolbar({
	renderTo : 'ToolbarDivProspectos',
	items    :
	[
		'-',
		{
			xtype		: 'button',
			//id			: 'BtnV'.$i.'_'.$this->GrillaName.'',
			text		: 'Guardar',
			scale		: 'large',
			iconCls		: 'guardar',
			hidden		: ElBtnGuardar,
			iconAlign	: 'left',
			handler 	: function(){ guardaProspectos();}
		},
		{
			xtype		: 'button',
			//id			: 'BtnV'.$i.'_'.$this->GrillaName.'',
			text		: 'Actualizar',
			scale		: 'large',
			iconCls		: 'guardar',
			hidden		: ElBtnActualiza,
			iconAlign	: 'left',
			handler 	: function(){ guardaProspectos(); }
		},
		{
			xtype		: 'button',
			//id			: 'BtnV'.$i.'_'.$this->GrillaName.'',
			text		: 'Eliminar',
			scale		: 'large',
			iconCls		: 'eliminar',
			//hidden		: ElBtnElimina,
			disabled	: ElBtnElimina,
			iconAlign	: 'left',
			handler 	: function(){WinEliminaProspectos();}
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
			handler 	: function(){VentanaCambiaFuncionario(cual);}
		},
		'-',
		{
			xtype		: 'button',
			text		: 'Realizar Gestion con<br />el Cliente  <b>CRM</b>',
			scale		: 'large',
			iconCls		: 'crm',
			//hidden		: ElBtnElimina,
			disabled	: ElBtnElimina,
			iconAlign	: 'left',
			handler 	: function(){CRMobjetivos(cual);}
		},
		'-',
		{
			xtype		: 'button',
			text		: 'Convertir Prospecto<br />en Cliente',
			scale		: 'large',
			iconCls		: 'addcliente',
			//hidden		: ElBtnElimina,
			disabled	: ConvertirCliente,
			iconAlign	: 'left',
			handler 	: function(){VentanaAgregarCliente3(cual,'true');}
		},
		'-'
	]
});

var TabsProspectos = new Ext.TabPanel({
		renderTo	: 'ContenedorDivProspectos',
		border		: false,
		activeTab	: 0,
		items		:	[
			{

				closable	: false,
				autoScroll	: true,
				/*autoHeight	: true, */
				height 		: '100',
				title		: 'Datos del Tercero',
				iconCls 	: 'cliente16',
				bodyStyle 	: 'height:'+AltoProspectos+'; overflow-x: hidden; background-color:<?php echo $_SESSION['COLOR_CONTRASTE'] ?>;',
				autoLoad    :
							{
								url		:	'../terceros/terceros/prospectos.php',
								scripts	:	true,
								nocache	:	true,
								params	:	{
									opcion		: opcion,
									id			: cual,
									contador	: contador
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
								url		:	'../terceros/terceros/direcciones.php',
								scripts	:	true,
								nocache	:	true,
								params	:	{
												elid		: cual,
												id_pais		: <?php echo $id_pais; ?>
											}

							}
			},
			{

				closable	: false,
				autoScroll	: false,
				disabled	: contactos,
				title		: 'Contactos',
				iconCls 	: 'user16',
				bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_CONTRASTE'] ?>;',
				autoLoad    :
							{
								url		:	'../terceros/terceros/contactos.php',
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
				disabled	: emails,
				title		: 'E-mails',
				iconCls 	: 'email16',
				bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_CONTRASTE'] ?>;',
				autoLoad    :
							{
								url		:	'../terceros/terceros/emails.php',
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
								url		:	'../terceros/terceros/documentos.php',
								scripts	:	true,
								nocache	:	true,
								params	:	{
												elid		:	cual
											}

							}
			},
		]
});

function WinEliminaProspectos(){
	Win_Ventana_EliminarProspecto = new Ext.Window({
			width		: 400,
			id			: 'Win_Ventana_EliminarProspecto',
			height		: 260,
			title		: 'Eliminar Prospecto',
			modal		: true,
			autoScroll	: false,
			closable	: false,
			autoDestroy : true,
			autoLoad	:
			{
				url		: '../terceros/bd/bd.php',
				scripts	: true,
				nocache	: true,
				params	:
				{
					op : "ventana_eliminar_prospecto",
					id : cual
				}
			},
			tbar		:
			[
				{
					xtype		: 'button',
					text		: 'Eliminar',
					scale		: 'large',
					iconCls		: 'eliminar',
					iconAlign	: 'left',
					handler 	: function(){eliminarProspecto(cual)}
				},
				{
					xtype		: 'button',
					text		: 'Regresar',
					scale		: 'large',
					iconCls		: 'regresar',
					iconAlign	: 'left',
					handler 	: function(){Win_Ventana_EliminarProspecto.close(id)}
				}
			]
		}).show();
}

function eliminarProspecto(cual){
	observaciones_eliminar_prospecto = document.getElementById('observaciones_eliminar_prospecto').value;
	if(observaciones_eliminar_prospecto==""){	alert("ERROR; Campo Observaciones Obligatorio");}
	else {
		observaciones_eliminar_prospecto = observaciones_eliminar_prospecto.replace(/[\#\<\>\'\"]/g, '');
		Ext.get("div_eliminacion_prospecto").load({
			url		: "../terceros/bd/bd.php",
			scripts	: true,
			nocache	: true,
			params	:
			{
				id            : cual,
				op            : "eliminar_campo_prospecto",
				observaciones : observaciones_eliminar_prospecto
			}
		});
	}
}
</script>