<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$id_empresa=$_SESSION['EMPRESA'];
	$sql     = "SELECT nit,tercero FROM web_service_tercero_causacion WHERE activo=1 AND id_empresa=$id_empresa";
	$query   = $mysql->query($sql,$mysql->link);
	$nit     = $mysql->result($query,0,'nit' );
	$tercero = $mysql->result($query,0,'tercero' );


?>

<style>
	.titulos_ventana{
		color       : #15428B;
		font-weight : bold;
		font-size   : 13px;
		font-family : tahoma,arial,verdana,sans-serif;
		text-align  : center;
		margin-top  : 15px;
		float       : left;
		width       : 100%;
	}

	.contenedor_tablas_cuentas{
		float            : left;
		width            : 90%;
		background-color : #FFF;
		margin-top       : 10px;
		margin-left      : 20px;
		border           : 1px solid #D4D4D4;
	}

	.headDivs{
		float            : left;
		background-color : #F3F3F3;
		padding          : 5 0 5 3;
		font-size        : 11px;
		font-weight      : bold;
		border-right     : 1px solid #D4D4D4;
		border-bottom    : 1px solid #D4D4D4;
	}

	.filaDivs{
		float         : left;
		border-right  : 1px solid #D4D4D4;
		padding       :  5 0 5 3;
		overflow      : hidden;
		white-space   : nowrap;
		text-overflow : ellipsis;
	}

	.divIcono{
		float            : left;
		width            : 20px;
		height           : 16px;
		padding          : 3 0 4 5;
		background-color : #F3F3F3;
		overflow         : hidden;
	}

	.divIcono>img{
		cursor : pointer;
		width  : 16px;
		height : 16px;
	}

</style>

<div id="toolbar_ventana_cuentas_transito" style="height:85px"></div>


<div style="width:100%;">
	<div class="titulos_ventana">TERCERO INGRESO-REVERSION</div>

	<div class="contenedor_tablas_cuentas">
		<div class="headDivs" style="width:90px;">NIT</div>
		<div class="headDivs" style="width:calc(100% - 97px);border-right:none;">TERCERO</div>

		<div class="filaDivs" style="width:90px;" id="nit_tercero">&nbsp;<?php echo $nit; ?></div>
		<div class="filaDivs" style="width:calc(100% - 104px - 20px);" id="tercero">&nbsp;<?php echo $tercero; ?></div>
		<div class="divIcono" style="border-right  :1px solid #D4D4D4;"  onclick="ventanaBuscarTercero()">
			<img src="img/buscar20.png" title="Buscar Cuenta">
		</div>

	</div>

</div>
<div id="loadForm" style="display:none;"></div>

<script>


	new Ext.Panel
	(
		{
			renderTo	:'toolbar_ventana_cuentas_transito',
			frame		:false,
			border		:false,
			tbar		:
			[
				{
					xtype	: 'buttongroup',
					columns	: 3,
					title	: 'Opciones',
					items	:
					[
						{
							xtype		: 'button',
							//id			: 'btn2',
							text		: 'Regresar',
							scale		: 'large',
							iconCls		: 'regresar',
							iconAlign	: 'top',
							handler 	: function(){Win_Panel_Global.close();}
						}
					]
				}
			]
		}
	);

	function ventanaBuscarTercero() {
		var myalto  = Ext.getBody().getHeight();
		var myancho = Ext.getBody().getWidth();

		Win_Ventana_buscar_tercero = new Ext.Window({
		    width       : myancho-100,
	   		height      : myalto-50,
		    id          : 'Win_Ventana_buscar_tercero',
		    title       : 'Terceros',
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../funciones_globales/grillas/BusquedaTerceros.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
					cargaFuncion : 'guardaTercero(id)',
					nombre_grilla: 'terceros',
					QuitarAncho  : 450,
					QuitarAlto   : 380,
		        }
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
		                    text        : 'Regresar',
		                    scale       : 'large',
		                    iconCls     : 'regresar',
		                    iconAlign   : 'top',
		                    hidden      : false,
		                    handler     : function(){ Win_Ventana_buscar_tercero.close(id) }
		                }
		            ]
		        }
		    ]
		}).show();
	}



	function guardaTercero(id) {

		MyLoading2('on');
		var nit = document.getElementById('div_terceros_numero_identificacion_'+id).innerHTML
		,	tercero = document.getElementById('div_terceros_nombre_comercial_'+id).innerHTML

		Ext.get('loadForm').load({
			url     : 'configuracion_SIHO/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc     : 'guardaTercero',
				id      : id,
				nit     :nit,
				tercero :tercero,
			}
		});
	}




</script>