<?php

	include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");

	$metodos      = '';
	$sqlMetodos   = "SELECT id,
						metodo,
						direccion,
						software,
						icono,
						titulo,
						archivo,
						btn_configuracion
					FROM web_service_metodos
					WHERE id_software='$id_software'
						AND activo=1";
	$queryMetodos = mysql_query($sqlMetodos,$link);

    while ($rowMetodos = mysql_fetch_assoc($queryMetodos)) {
    	$metodos .= '<div class="IconoPanelControl" onClick="ventanaMetodosWs('.$rowMetodos['id'].',\''.$rowMetodos['titulo'].'\',\''.$rowMetodos['btn_configuracion'].'\',\''.$rowMetodos['software'].'\',\''.$rowMetodos['archivo'].'\');">
                        <div class="IconoPanelControlimg"><img src="'.$rowMetodos['icono'].'" width="44" height="44"></div>
                        <div class="IconoPanelControltxt">'.$rowMetodos['titulo'].'</div>
                    </div>';
    }

    if($metodos == ''){ echo '<div style="margin:10px; font_weight:bold;">NO HAY METODOS DECLARADOS PARA EL PRESENTE WEB SERVICE</div>'; exit; }

    echo '<div style="margin:10px; font_weight:bold;">'.$metodos.'</div>';
?>

<script type="text/javascript">

	function ventanaMetodosWs(id_metodo,metodo,btn_configuracion,software,archivo){

		var width  = (metodo=='Descargar Facturas')? 600: 500
		,	hidden = (btn_configuracion == 'si')? false: true;

		Win_Ventana_metodo_ws = new Ext.Window({
		    width       : 600,
		    height      : 280,
		    id          : 'Win_Ventana_metodo_ws',
		    title       : 'Metodo '+metodo,
		    modal       : true,
		    autoScroll  : true,
		    closable    : true,
		    autoDestroy : true,
		    bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
		    items       :
		    [
		        {
		            closable    : false,
		            border      : false,
		            autoScroll  : true,
		            iconCls     : '',
		            bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
		            items       :
		            [

		                {
		                    xtype       : "panel",
		                    id          : 'contenedor_Ventana_metodo_ws',
		                    border      : false,
		                    bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
		                }
		            ]
		        }
		    ],
		    tbar        :
		    [
		        {
		            xtype   : 'buttongroup',
		            columns : 3,
		            title   : 'Fecha',
		            style 	: 'border:none;',
		            items   :
		            [
		                {
		                    xtype       : 'panel',
		                    border      : false,
		                    width       : 140,
		                    height      : 40,
		                    bodyStyle   : 'background-color:rgba(255,255,255,0);',
		                    autoLoad    :
		                    {
		                        url     : 'conectividad/bd/bd.php',
		                        scripts : true,
		                        nocache : true,
		                        params  : { opc : 'filtro_fecha_interface' }
		                    }
		                }
		            ]
		        },'-',
		        {
		            xtype       : 'button',
		            width       : 60,
		            height      : 56,
		            text        : 'Ejecutar',
		            scale       : 'large',
		            iconCls     : 'guardar',
		            iconAlign   : 'top',
		            handler     : function(){ ejecutarInterface(id_metodo) }
		        },'-',
		        {
		            xtype       : 'button',
		            width       : 60,
		            height      : 56,
		            hidden 		: hidden,
		            text        : 'Configurar',
		            scale       : 'large',
		            iconCls     : 'opciones',
		            iconAlign   : 'top',
		            handler     : function(){ configurarInterface(id_metodo,software,archivo) }
		        }
		    ]
		}).show();
	}

	function ejecutarInterface(id_metodo){
		var fecha_metodo = document.getElementById('input_fecha_interface').value;

		Ext.get('contenedor_Ventana_metodo_ws').load({
			url     : '../interface/register.php',
			scripts : true,
			nocache : true,
			timeout : 600000,
			params  :
			{
				id_metodo    : id_metodo,
				fecha_metodo : fecha_metodo
			}
		});
	}

	function configurarInterface(id_metodo,software,archivo){
		var fecha_metodo = document.getElementById('input_fecha_interface').value;

		Win_Ventana_config_saldo_facturas = new Ext.Window({
		    width       : 400,
		    height      : 300,
		    id          : 'Win_Ventana_config_saldo_facturas',
		    title       : 'CONFIGURACION CONTABLE '+software,
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../interface/'+software+'/config/config_'+archivo,
		        scripts : true,
		        nocache : true,
		        params  : { id_metodo : id_metodo, }
		    },
		    tbar        :
		    [
		        {
		            xtype   : 'buttongroup',
		            columns : 3,
		            title   : 'Opciones',
		            style   : 'border-right:none;',
		            items   :
		            [
		                {
		                    xtype       : 'button',
		                    width       : 60,
		                    height      : 56,
		                    text        : 'Guardar',
		                    scale       : 'large',
		                    iconCls     : 'guardar',
		                    iconAlign   : 'top',
		                    hidden      : false,
		                    handler     : function(){ BloqBtn(this); guardar_config_saldo_facturas(id_metodo) }
		                },
		                {
		                    xtype       : 'button',
		                    width       : 60,
		                    height      : 56,
		                    text        : 'Regresar',
		                    scale       : 'large',
		                    iconCls     : 'regresar',
		                    iconAlign   : 'top',
		                    hidden      : false,
		                    handler     : function(){ BloqBtn(this); Win_Ventana_config_saldo_facturas.close(id) }
		                }
		            ]
		        }
		    ]
		}).show();
	}

</script>