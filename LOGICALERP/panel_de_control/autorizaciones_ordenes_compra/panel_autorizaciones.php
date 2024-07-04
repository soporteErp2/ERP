
<div class="ContenedorGrupoPanelControl" style="margin-top:20px;padding-left:50px;">

	<div class="IconoPanelControl" onClick="AbreVentanaPanelAutorizacionOC('autorizaciones_ordenes_compra/rango_precios','Autorizacion Por Precio',500,450,0,0)">
		<div class="IconoPanelControlimg"><img src="../../../temas/clasico/images/PanelControl/pagos.png" width="44" height="44"></div>
		<div class="IconoPanelControltxt">Autorizacion por Costos</div>
	</div>
	<div class="IconoPanelControl" onClick="AbreVentanaPanelAutorizacionOC('autorizaciones_ordenes_compra/autorizaciones_ordenes_area','Autorizacion por Area',500,450,0,0);">
		<div class="IconoPanelControlimg"><img src="img/ubicacion.png" width="44" height="44"></div>
		<div class="IconoPanelControltxt">Autorizacion por Areas</div>
	</div>
</div>
<script>

	function AbreVentanaPanelAutorizacionOC(archivo,titulo,ancho,alto,color,resize){

        var myalto2  = Ext.getBody().getHeight();
        var myancho2 = Ext.getBody().getWidth();

    	if(alto == 0){ WinAlto = myalto2-20; }
        else{ WinAlto = alto; }

    	if(ancho == 0){ WinAncho = myancho2-30 }
        else{ WinAncho = ancho }

    	Win_Panel_AOC = new Ext.Window({
            width       : WinAncho,
            height      : WinAlto,
            title       : titulo,
            modal       : true,
            autoScroll  : false,
            autoDestroy : true,
            items       :
			[
				{
					xtype		: 'panel',
					id			: 'contenedor_Win_Panel_AOC',
					border		: false,
					bodyStyle 	: color,
					autoLoad	:
					{
						url		: archivo+'.php',
						scripts	: true,
						nocache	: true,
						params	: {	}
					}
				}
			]
		}).show();
    }
</script>