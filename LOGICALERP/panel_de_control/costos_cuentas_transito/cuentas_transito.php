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
	<div class="titulos_ventana">CUENTAS COLGAAP</div>

	<div class="contenedor_tablas_cuentas">
		<div class="headDivs" style="width:90px;">NATURALEZA</div>
		<div class="headDivs" style="width:100px;">CUENTA</div>
		<div class="headDivs" style="width:calc(100% - 107px - 94px);border-right:none;">DESCRIPCION</div>

		<div class="filaDivs" style="width:90px;">DEBITO</div>
		<div class="filaDivs" id="cuenta_colgaap_debito" style="width:100px;">&nbsp;<?php echo $cuenta_colgaap_debito ?></div>
		<div class="filaDivs" id="descripcion_cuenta_colgaap_debito" style="width:calc(100% - 110px - 94px - 49px);">&nbsp;<?php echo $descripcion_cuenta_colgaap_debito ?></div>
		<div class="divIcono" style="border-right  :1px solid #D4D4D4;"  onclick="ventanaBuscarCuenta('colgaap','debito')">
			<img src="img/buscar20.png" title="Buscar Cuenta">
		</div>
		<div class="divIcono" id="divLoadHomologarDebito" onclick="homologarCuentaColgaap('debito')">
			<img src="img/refresh.png" title="Homologar Cuenta en Niif">
		</div>

		<div class="filaDivs" style="width:90px;border-top:1px solid #D4D4D4;">CREDITO</div>
		<div class="filaDivs" id="cuenta_colgaap_credito" style="width:100px;border-top:1px solid #D4D4D4;">&nbsp;<?php echo $cuenta_colgaap_credito ?></div>
		<div class="filaDivs" id="descripcion_cuenta_colgaap_credito" style="width:calc(100% - 110px - 94px - 49px);border-top:1px solid #D4D4D4;">&nbsp;<?php echo $descripcion_cuenta_colgaap_credito ?></div>
		<div class="divIcono"  style="border-top:1px solid #D4D4D4;border-right  :1px solid #D4D4D4;" onclick="ventanaBuscarCuenta('colgaap','credito')">
			<img src="img/buscar20.png" title="Buscar Cuenta">
		</div>
		<div class="divIcono" id="divLoadHomologarCredito" style="border-top:1px solid #D4D4D4;" onclick="homologarCuentaColgaap('credito')">
			<img src="img/refresh.png" title="Homologar Cuenta en Niif">
		</div>

	</div>

	<div class="titulos_ventana">CUENTAS NIIF</div>

	<div class="contenedor_tablas_cuentas">
		<div class="headDivs" style="width:90px;">NATURALEZA</div>
		<div class="headDivs" style="width:100px;">CUENTA</div>
		<div class="headDivs" style="width:calc(100% - 107px - 94px);border-right:none;">DESCRIPCION</div>

		<div class="filaDivs" style="width:90px;">DEBITO</div>
		<div class="filaDivs" id="cuenta_niif_debito" style="width:100px;">&nbsp;<?php echo $cuenta_niif_debito ?></div>
		<div class="filaDivs" id="descripcion_cuenta_niif_debito" style="width:calc(100% - 110px - 94px - 23px);">&nbsp;<?php echo $descripcion_cuenta_niif_debito ?></div>
		<div class="divIcono"  onclick="ventanaBuscarCuenta('niif','debito')">
			<img src="img/buscar20.png" title="Buscar Cuenta">
		</div>

		<div class="filaDivs" style="width:90px;border-top:1px solid #D4D4D4;">CREDITO</div>
		<div class="filaDivs" id="cuenta_niif_credito" style="width:100px;border-top:1px solid #D4D4D4;">&nbsp;<?php echo $cuenta_niif_credito ?></div>
		<div class="filaDivs" id="descripcion_cuenta_niif_credito" style="width:calc(100% - 110px - 94px - 23px);border-top:1px solid #D4D4D4;">&nbsp;<?php echo $descripcion_cuenta_niif_credito ?></div>
		<div class="divIcono"  style="border-top:1px solid #D4D4D4;" onclick="ventanaBuscarCuenta('niif','credito')">
			<img src="img/buscar20.png" title="Buscar Cuenta">
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
						// {
						// 	xtype		: 'button',
						// 	//id			: 'btn2',
						// 	text		: 'Guadar',
						// 	scale		: 'large',
						// 	iconCls		: 'guardar',
						// 	iconAlign	: 'top',
						// 	handler 	: function(){BloqBtn(this); actualiza_info_empresa();}
						// },
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

	function ventanaBuscarCuenta(opc,naturaleza) {
		if (opc=='colgaap') {
			var title     ='Buscar cuenta Colgaap';
			var tabla_puc = 'puc';
		}
		else{
			var title     ='Buscar cuenta Niif';
			var tabla_puc = 'puc_niif';
		}

		Win_Ventana_buscar_cuenta = new Ext.Window({
		    width       : 600,
		    height      : 650,
		    id          : 'Win_Ventana_buscar_cuenta',
		    title       : title,
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../funciones_globales/grillas/BuscarCuentaPuc.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
					nombreGrilla : tabla_puc,
					tabla_puc    : tabla_puc,
					cargaFuncion : 'guardaCuenta(id,"'+tabla_puc+'","'+naturaleza+'")',
					QuitarAncho  : 450,
					QuitarAlto   : 380,
					opc          : opc,
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

	function guardaCuenta(id,nombre_tabla,naturaleza) {
		var divLoad = 'item_'+nombre_tabla+'_'+id;

		Ext.get(divLoad).load({
			url     : 'costos_cuentas_transito/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc          : 'guardaCuenta',
				id_cuenta    : id,
				nombre_tabla : nombre_tabla,
				naturaleza   : naturaleza,
			}
		});
	}

	function homologarCuentaColgaap(naturaleza) {
		if (naturaleza=='debito') {
			var divLoad = 'divLoadHomologarDebito' ;
			var cuenta_colgaap = document.getElementById('cuenta_colgaap_debito').innerHTML;
		}
		else{
			var divLoad = 'divLoadHomologarCredito' ;
			var cuenta_colgaap = document.getElementById('cuenta_colgaap_credito').innerHTML;
		}

		if (cuenta_colgaap=='' || cuenta_colgaap==0) {
			alert("Aviso\nDebe seleccionar primero la cuenta colgaap");
			return;
		}

		Ext.get(divLoad).load({
			url     : 'costos_cuentas_transito/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc               : 'homologarCuentaColgaap',
				cuenta_colgaap : cuenta_colgaap,
				naturaleza        : naturaleza,
			}
		});

	}


</script>