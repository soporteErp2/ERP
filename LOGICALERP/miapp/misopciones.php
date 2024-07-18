<?php
    include('../../configuracion/conectar.php');
    include('../../configuracion/define_variables.php');
?>

<div class="myfuente" style="float:left; padding:15px; width:100%">

        <!-- --------------------------------------------------------------------------------------- -->
        <div class="ContenedorGrupoPanelControl">
            <div class="TituloPanelControl">
                Opciones de Configuracion Personal
            </div>
            <div style="width:100%; float:left">

                <div class="IconoPanelControl" onClick="AbreVentanaMisOpciones('password','Contrase&ntilde;a',300,180);">
                    <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/password44.png" width="44" height="44"></div>
                    <div class="IconoPanelControltxt">Contrase&ntilde;a</div>
                </div>

                <div class="IconoPanelControl" onClick="AbreVentanaMisOpciones('configurar_correo','Configurar correo personal',250,135);">
                    <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/miemail44.png" width="44" height="44"></div>
                    <div class="IconoPanelControltxt beta">Configuracion de mi Correo</div>
                </div>

                <div class="IconoPanelControl" onClick="AbreVentanaMiStyleColor('personalizar_escritorio','Personalizar Escritorio',210,150);">
                    <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/colors44.png" width="44" height="44"></div>
                    <div class="IconoPanelControltxt">Personalizar Escritorio</div>
                </div>

            </div>
        </div>

        <!-- --------------------------------------------------------------------------------------- -->
        <div class="ContenedorGrupoPanelControl" style="margin-top:10px;">
            <div class="TituloPanelControl">
                Herramientas
            </div>
            <div style="width:100%; float:left">



                <!-- <div class="IconoPanelControl" onClick="AbreVentanaMisOpciones('formatos_descargables','Descarga de Formatos',0,0);">
                    <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/formatos44.png" width="44" height="44"></div>
                    <div class="IconoPanelControltxt">Descarga de Formatos</div>
                </div>

                <div class="IconoPanelControl" onClick="AbreVentanaMisOpciones('../personal/horas_extras','Informe de mis Horas Extras',750,500);">
                    <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/horasextras44.png" width="44" height="44"></div>
                    <div class="IconoPanelControltxt">Informe de mis Horas Extras</div>
                </div> -->

                <div class="IconoPanelControl" onClick="AbreVentanaMisOpciones('../miapp/directorio','Directorio de Funcionarios',0,0);">
                    <div class="IconoPanelControlimg"><img src="../../temas/clasico/images/PanelControl/contactos44.png" width="44" height="44"></div>
                    <div class="IconoPanelControltxt">Directorio de Funcionarios</div>
                </div>

            </div>
        </div>

</div>

<script>
	function AbreVentanaMisOpciones(archivo,titulo,ancho,alto){

		var myalto2  = Ext.getBody().getHeight();
		var myancho2  = Ext.getBody().getWidth();
		if(alto == 0){
			WinAlto = myalto2-20;
		}else{
			WinAlto = alto;
		}
		if(ancho == 0){
			WinAncho = myancho2-30
		}else{
			WinAncho = ancho
		}
		//var filtro_empresa = document.getElementById('filtro_empresa').value;
		//var filtro_sucursal = document.getElementById('filtro_sucursal').value;
		Win_Panel_Global = new Ext.Window
		(
			{
				width		:	WinAncho,
				height		:	WinAlto,
				title		:	titulo,
				modal		: 	true,
				autoScroll	: 	false,
				autoDestroy : 	true,
				//bodyStyle 	: 	'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
				items		:
				[
					{
						xtype		: 	'panel',
						id			:	'contenedor_Win_Panel_Global',
						// layout		: 	'border',
						border		: 	false,
						// bodyStyle 	: 	'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
						bodyStyle 	: 	'background-color:#DFE8F6',
						autoLoad	:
						{
							url		: 	archivo+'.php',
							scripts	:	true,
							nocache	:	true,
							params	:	{
											id	:	"<?php echo $_SESSION['CEDULAFUNCIONARIO']; ?>"
										}
						}
					}
				]
			}
		).show();
	}

	function AbreVentanaMiStyleColor(archivo,titulo,ancho,alto){

		var myalto2  = Ext.getBody().getHeight();
		var myancho2  = Ext.getBody().getWidth();

		Win_Panel_Global = new Ext.Window
		(
			{
				width       : ancho,
				height      : alto,
				title       : titulo,
				modal       : true,
				autoScroll  : false,
				autoDestroy : true,
				closable	: false,
				autoLoad    :
				{
					url		: archivo+'.php',
					scripts	: true,
					nocache	: true,
					params	: { id	:	"<?php echo $_SESSION['CEDULAFUNCIONARIO']; ?>" }
				},
				tbar		:
				[

					{
						xtype		: 'button',
						text		: 'Guardar',
						width		: 60,
						height		: 50,
						scale		: 'large',
						iconCls		: 'guardar',
						iconAlign	: 'top',
						handler 	: function(){ GuardarStyleColor(); }
					},
					{
						xtype		: 'button',
						text		: 'Vista previa',
						width		: 60,
						height		: 50,
						scale		: 'large',
						iconCls		: 'guardar',
						iconAlign	: 'top',
						handler 	: function(){ vistaPreviaStyleColor(); }
					},
					{
						xtype		: 'button',
						text		: 'Regresar',
						width		: 60,
						height		: 50,
						scale		: 'large',
						iconCls		: 'regresar',
						iconAlign	: 'top',
						handler 	: function(){ cerrarWinStyleColor(); }
					}
				]
			}
		).show();
	}
</script>