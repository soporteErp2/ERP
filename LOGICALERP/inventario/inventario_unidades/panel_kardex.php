<?php include('../../configuracion/conectar.php'); ?>

<div style="float:left; padding:15px; width:100%">

    <!-- --------------------------------------------------------------------------------------- -->
    <div class="ContenedorGrupoPanelControl">
        <div class="TituloPanelControl">
            Sitio en construccion
        </div>
        <!--
        <div style="width:100%; float:left">

            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('sucursales/sucursales','Sucursales',650,500);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/empresa44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Sucursales</div>
            </div>

        </div>
    </div>

    <!-- ---------------------------------------------------------------------------------------

    <div class="ContenedorGrupoPanelControl">
        <div class="TituloPanelControl">
            Parametrizaciones Contables
        </div>
        <div style="width:100%; float:left">

            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('puc/puc','Plan unico de cuentas',0,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/empresa44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">PUC</div>
            </div>

            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('configuracion_puc/configuracion_puc','Plan unico de cuentas',400,300);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/empresa44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Configuracion PUC</div>
            </div>

            <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('bancos/bancos','Bancos',0,0);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/empresa44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Bancos</div>
            </div>

             <div class="IconoPanelControl" onClick="AbreVentanaPanelGlobal('prueba/prueba','Prueba',800,600);">
                <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/empresa44.png" width="44" height="44"></div>
                <div class="IconoPanelControltxt">Prueba</div>
            </div>

        </div>
    </div>-->

</div>

<script>
    function AbreVentanaPanelGlobal(archivo,titulo,ancho,alto){

        var myalto2  = Ext.getBody().getHeight();
        var myancho2 = Ext.getBody().getWidth();

    	if(alto == 0){ WinAlto = myalto2-20; }
        else{ WinAlto = alto; }

    	if(ancho == 0){ WinAncho = myancho2-30 }
        else{ WinAncho = ancho }

    	Win_Panel_Global = new Ext.Window({
            width       : WinAncho,
            height      : WinAlto,
            title       : titulo,
            modal       : true,
            autoScroll  : false,
            autoDestroy : true,
            bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
            items       :
			[
				{
					xtype		: 'panel',
					id			: 'contenedor_Win_Panel_Global',
					border		: false,
					bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
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