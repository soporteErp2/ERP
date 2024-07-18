<?php
	include('../../configuracion/conectar.php');
	include("../../configuracion/define_variables_debug.php");

	//ESTILO DEL PERMISO
	$style_permiso='style="display:none;"';

?>
<meta charset="UTF-8">
<style>
	.PERMISOtrue{ }
	.PERMISOfalse{ }
</style>
<div style="float:left; padding:15px; width:95%">

	<!-- --------------------------------------------------------------------------------------- -->
	<div class="ContenedorGrupoPanelControl" >
        <div class="TituloPanelControl">Configuraciones por Sucursal</div>
        <div style="width:100%; float:left">

            <div class="IconoPanelControl" onClick="AbreVentanaPanelSucursal('formatos_documentos','documentos/configuracion_documentos','Configuracion de Documentos',550,350,'true');">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/cuentas_predefinidas44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Dise&ntilde;ador de Formatos</div>
            </div>

        </div>
    </div>

	<!-- --------------------------------------------------------------------------------------- -->
	<div class="ContenedorGrupoPanelControl" <?php if (user_permisos(74,'true') == 'true') { echo $style_permiso;}  ?> >
        <div class="TituloPanelControl">Parametrizaciones Modulo Compras</div>
        <div style="width:100%; float:left">

        	<div class="IconoPanelControl" onClick="AbreVentanaPanelSucursal('consecutivos_compras','compras/consecutivos','Consecutivos Documentos',285,290,'true');">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/numero44.png" width="35" height="35"></div>
                <div class="IconoPanelControltxt">Configuracion Consecutivos</div>
            </div>

        </div>
    </div>

    <!-- --------------------------------------------------------------------------------------- -->
    <div class="ContenedorGrupoPanelControl" <?php if (user_permisos(75,'true') == 'true') { echo $style_permiso;}  ?> >
        <div class="TituloPanelControl">Parametrizaciones Modulo Ventas</div>
        <div style="width:100%; float:left">

            <div class="IconoPanelControl" onClick="AbreVentanaPanelSucursal('consecutivos_ventas','ventas/consecutivos','Consecutivos Documentos',265,360,'true');">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/numero44.png" width="35" height="35"></div>
                <div class="IconoPanelControltxt">Configuracion Consecutivos</div>
            </div>

            <div class="IconoPanelControl" onClick="AbreVentanaPanelSucursal('pos','configuracion_pos/panel_pos','Configuracion POS',933,520,'true');">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/pos.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Ventas POS</div>
            </div>

            <div class="IconoPanelControl" onClick="AbreVentanaPanelSucursal('notificacion_cartera','compras/notificacion_correos','Notificaciones de Cartera',600,600,'true');">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/calendario44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Notificaciones de Cartera</div>
            </div>
            <!-- <div class="IconoPanelControl" onClick="AbreVentanaPanelSucursal('pos','configuracion_pos/panel_pos','Configuracion POS',933,520,'true');">
               <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/pos.png" width="44" height="44"></div>
               <div class="IconoPanelControltxt">Configurar Cajas</div>
           </div> -->

        </div>
    </div>

    <div class="ContenedorGrupoPanelControl" >
        <div class="TituloPanelControl">Parametrizaciones Modulo Nomina</div>
        <div style="width:100%; float:left">

        	<div class="IconoPanelControl" onClick="AbreVentanaPanelSucursal('consecutivos_nomina','nomina/consecutivos','Consecutivos Documentos',270,190,'true');">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/numero44.png" width="35" height="35"></div>
                <div class="IconoPanelControltxt">Configuracion Consecutivos</div>
            </div>
        </div>
    </div>

</div>

<script>
function AbreVentanaPanelSucursal(nombre,archivo,titulo,ancho,alto,permiso){

	if(permiso == 'true'){

		var indexSucursal   = document.getElementById('filtro_sucursal_panel_sucursal').selectedIndex
		,	nombreSucursal  = document.getElementById('filtro_sucursal_panel_sucursal').options[indexSucursal].text
		,	mytitulo        = nombreSucursal

		var myalto2  = Ext.getBody().getHeight();
		var myancho2 = Ext.getBody().getWidth();

		if(alto == 0){ WinAlto = myalto2-20; }
		else{ WinAlto = alto; }

		if(ancho == 0){ WinAncho = myancho2-30 }
		else{ WinAncho = ancho }

		Win_Panel_Sucursal = new Ext.Window({
			id			: 'Win_Ventana_'+nombre,
			width		: WinAncho,
			height		: WinAlto,
			title		: mytitulo,
			modal		: true,
			resizable   : true,
			autoScroll	: false,
			autoDestroy : true,
			bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
			items		:
			[
				{
					xtype     : 'panel',
					id        :	'contenedor_Win_Panel_Sucursal',
					border    :	false,
					bodyStyle :	'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
					autoLoad  :
					{
						url     : archivo+'.php',
						scripts : true,
						nocache : true,
						params  : { filtro_sucursal : '<?php echo $filtro_sucursal; ?>' }
					}
				}
			]
		}).show();
	}
}

</script>
