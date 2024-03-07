<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$id_empresa=$_SESSION['EMPRESA'];

	$sql="SELECT
				id_cuenta_colgaap_debito,
				cuenta_colgaap_debito,
				descripcion_cuenta_colgaap_debito,
				id_cuenta_niif_debito,
				cuenta_niif_debito,
				descripcion_cuenta_niif_debito,
				id_cuenta_colgaap_credito,
				cuenta_colgaap_credito,
				descripcion_cuenta_colgaap_credito,
				id_cuenta_niif_credito,
				cuenta_niif_credito,
				descripcion_cuenta_niif_credito
			FROM costo_cuentas_transito
			WHERE
			activo=1
			AND id_empresa=$id_empresa";
	$query=mysql_query($sql,$link);

	$id_cuenta_colgaap_debito           = mysql_result($query,0,'id_cuenta_colgaap_debito');
	$cuenta_colgaap_debito              = mysql_result($query,0,'cuenta_colgaap_debito');
	$descripcion_cuenta_colgaap_debito  = mysql_result($query,0,'descripcion_cuenta_colgaap_debito');
	$id_cuenta_niif_debito              = mysql_result($query,0,'id_cuenta_niif_debito');
	$cuenta_niif_debito                 = mysql_result($query,0,'cuenta_niif_debito');
	$descripcion_cuenta_niif_debito     = mysql_result($query,0,'descripcion_cuenta_niif_debito');
	$id_cuenta_colgaap_credito          = mysql_result($query,0,'id_cuenta_colgaap_credito');
	$cuenta_colgaap_credito             = mysql_result($query,0,'cuenta_colgaap_credito');
	$descripcion_cuenta_colgaap_credito = mysql_result($query,0,'descripcion_cuenta_colgaap_credito');
	$id_cuenta_niif_credito             = mysql_result($query,0,'id_cuenta_niif_credito');
	$cuenta_niif_credito                = mysql_result($query,0,'cuenta_niif_credito');
	$descripcion_cuenta_niif_credito    = mysql_result($query,0,'descripcion_cuenta_niif_credito');


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
	<div class="titulos_ventana">TERCERO DE ORIGEN</div>

	<div class="contenedor_tablas_cuentas">
		<div class="headDivs" style="width:90px;">DOCUMENTO</div>
		<div class="headDivs" style="width:calc(100% - 97px);border-right:none;">NOMBRE</div>

		<input type="hidden" id="id_tercero_origen">
		<div class="filaDivs" style="width:90px;" id="documento_tercero_origen">&nbsp;</div>
		<div class="filaDivs" style="width:calc(100% - 97px - 26px);" id="nombre_tercero_origen">&nbsp;</div>
		<div class="divIcono" onclick="ventanaBuscarTercero('origen')">
			<img src="img/buscar20.png" title="Buscar Tercero">
		</div>

	</div>

	<div class="titulos_ventana">TERCERO DE DESTINO</div>

	<div class="contenedor_tablas_cuentas">
		<div class="headDivs" style="width:90px;">DOCUMENTO</div>
		<div class="headDivs" style="width:calc(100% -  97px);border-right:none;">NOMBRE</div>

		<input type="hidden" id="id_tercero_destino">
		<div class="filaDivs" style="width:90px;" id="documento_tercero_destino">&nbsp;</div>
		<div class="filaDivs" style="width:calc(100% - 97px - 26px);" id="nombre_tercero_destino">&nbsp;</div>
		<div class="divIcono" onclick="ventanaBuscarTercero('destino')">
			<img src="img/buscar20.png" title="Buscar Tercero">
		</div>

	</div>

</div>


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
							text		: 'Trasladar',
							scale		: 'large',
							iconCls		: 'reload32',
							iconAlign	: 'top',
							handler 	: function(){BloqBtn(this); trasladar_saldo(); }
						},
						{
							xtype		: 'button',
							//id			: 'btn2',
							text		: 'Regresar',
							scale		: 'large',
							iconCls		: 'regresar',
							iconAlign	: 'top',
							handler 	: function(){Win_Ventana_traslado_saldo_tercero.close();}
						}
					]
				}
			]
		}
	);

	function ventanaBuscarTercero(opc) {
		var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

		Win_Ventana_buscar_cuenta = new Ext.Window({
		    width       : myancho-100,
		    height      : myalto-50,
		    id          : 'Win_Ventana_buscar_cuenta',
		    title       : 'Seleccionar el tercero',
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
					nombre_grilla : 'terceros',
					cargaFuncion    : "rederizaResultadoVentana(\\'"+opc+"\\',id);",
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
		                    handler     : function(){ Win_Ventana_buscar_cuenta.close(id) }
		                }
		            ]
		        }
		    ]
		}).show();
	}

	function rederizaResultadoVentana(opc,id) {
		var id_element        =''
		,	documento_element =''
		,	nombre_element    =''
		,	documento_value   = document.getElementById('div_terceros_numero_identificacion_'+id).innerHTML
		,	nombre_value      = document.getElementById('div_terceros_nombre_comercial_'+id).innerHTML;

		if (opc=='origen') {
			id_element        ='id_tercero_origen';
			documento_element ='documento_tercero_origen';
			nombre_element    ='nombre_tercero_origen';
		}
		else{
			id_element        ='id_tercero_destino';
			documento_element ='documento_tercero_destino';
			nombre_element    ='nombre_tercero_destino';
		}

		document.getElementById(id_element).value=id;
		document.getElementById(documento_element).innerHTML=documento_value;
		document.getElementById(nombre_element).innerHTML=nombre_value;

		Win_Ventana_buscar_cuenta.close();

	}

		function trasladar_saldo() {
			var id_tercero_origen = document.getElementById('id_tercero_origen').value
			,	id_tercero_destino = document.getElementById('id_tercero_destino').value;

			if (id_tercero_origen==0 || id_tercero_destino=='' || id_tercero_destino==0 || id_tercero_destino=='') {
				alert("Aviso\nDebe seleccionar el tercero de origen y el tercero de destino");
				return;
			}

			if (confirm('Todas las cuentas en los asientos del sistema cambiaran del tercero de origen al tercero de destino\nRealmente desea continuar?')) {
				console.log("traslate");
				Ext.get('').load({
					url     : '.php',
					scripts : true,
					nocache : true,
					params  :
					{
						var1 : 'var1',
						var2 : 'var2',
					}
				});
			}

		}


</script>