<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$id_empresa = $_SESSION['EMPRESA'];

	$sql="SELECT cuenta,naturaleza,estado FROM deterioro_cartera_clientes_cuentas WHERE activo=1 AND id_empresa=$id_empresa";
	$query=$mysql->query($sql,$mysql->link);
	while ($row = $mysql->fetch_array($query)) {
		$cuenta     = $row['cuenta'];
		$naturaleza = $row['naturaleza'];
		$estado     = $row['estado'];

		$arrayCuentas[$estado][$naturaleza] = $cuenta;

	}

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
		width            : 92%;
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

<div style="width:100%;">
	<div class="titulos_ventana">CUENTAS DETERIORO CARTERA CLIENTES</div>

	<div class="contenedor_tablas_cuentas">
		<div class="headDivs" style="width:90px;">ESTADO</div>
		<div class="headDivs" style="width:calc(100% - 107px - 94px - 26px);">DESCRIPCION</div>
		<div class="headDivs" style="width:126px;border-right:none;">CUENTA</div>

		<div class="filaDivs" style="width:90px;height:39px;line-height:19px;">COMPROMISO <br>DE PAGO</div>
		<div class="filaDivs" id="descripcion_cuenta_colgaap_debito" style="width:calc(100% - 110px - 94px - 23px);">Perdida por Deterioro clientes a VP (Debito) </div>
		<div class="filaDivs" id="compromiso_pago_debito" style="width:100px;">&nbsp;<?php echo $arrayCuentas['compromiso_pago']['debito'] ?></div>
		<div class="divIcono" style=""  onclick="ventanaBuscarCuenta('compromiso_pago','debito')">
			<img src="img/buscar20.png" title="Buscar Cuenta">
		</div>
		<div class="filaDivs" id="descripcion_cuenta_colgaap_credito" style="width:calc(100% - 110px - 94px - 23px);border-top:1px solid #D4D4D4;"> Utilidad por Des reconocimiento de deterioro clientes a VP (Credito) </div>
		<div class="filaDivs" id="compromiso_pago_credito" style="width:100px;border-top:1px solid #D4D4D4;">&nbsp;<?php echo $arrayCuentas['compromiso_pago']['credito'] ?></div>
		<div class="divIcono"  style="border-top:1px solid #D4D4D4;" onclick="ventanaBuscarCuenta('compromiso_pago','credito')">
			<img src="img/buscar20.png" title="Buscar Cuenta">
		</div>

		<div class="filaDivs" style="width:90px;height:39px;line-height:30px;border-top: 1px solid #D4D4D4;">INCOBRABLE</div>
		<div class="filaDivs" id="descripcion_cuenta_colgaap_debito" style="width:calc(100% - 110px - 94px - 23px);border-top: 1px solid #D4D4D4;">Perdida  por  clientes incobrables (Debito)</div>
		<div class="filaDivs" id="incobrable_debito" style="width:100px;border-top: 1px solid #D4D4D4;">&nbsp;<?php echo $arrayCuentas['incobrable']['debito'] ?></div>
		<div class="divIcono" style="border-top: 1px solid #D4D4D4;"  onclick="ventanaBuscarCuenta('incobrable','debito')">
			<img src="img/buscar20.png" title="Buscar Cuenta">
		</div>
		<div class="filaDivs" id="descripcion_cuenta_colgaap_credito" style="width:calc(100% - 110px - 94px - 23px);border-top:1px solid #D4D4D4;">Utilidad por Des reconocimiento de clientes incobrables (Credito)</div>
		<div class="filaDivs" id="incobrable_credito" style="width:100px;border-top:1px solid #D4D4D4;">&nbsp;<?php echo $arrayCuentas['incobrable']['credito'] ?></div>
		<div class="divIcono"  style="border-top:1px solid #D4D4D4;" onclick="ventanaBuscarCuenta('incobrable','credito')">
			<img src="img/buscar20.png" title="Buscar Cuenta">
		</div>

		<div class="filaDivs" style="width:90px;height:39px;line-height:19px;border-top: 1px solid #D4D4D4;">COBRO<br>JURIDICO</div>
		<div class="filaDivs" id="descripcion_cuenta_colgaap_debito" style="width:calc(100% - 110px - 94px - 23px);    border-top: 1px solid #D4D4D4;">Perdida  por  clientes en cobro juridico (Debito)</div>
		<div class="filaDivs" id="cobro_juridico_debito" style="width:100px;border-top: 1px solid #D4D4D4;">&nbsp;<?php echo $arrayCuentas['cobro_juridico']['debito'] ?></div>
		<div class="divIcono" style="border-top: 1px solid #D4D4D4;"  onclick="ventanaBuscarCuenta('cobro_juridico','debito')">
			<img src="img/buscar20.png" title="Buscar Cuenta">
		</div>
		<div class="filaDivs" id="descripcion_cuenta_colgaap_credito" style="width:calc(100% - 110px - 94px - 23px);border-top:1px solid #D4D4D4;">Utilidad por Des reconocimiento de clientes en cobro juridico (Credito)</div>
		<div class="filaDivs" id="cobro_juridico_credito" style="width:100px;border-top:1px solid #D4D4D4;">&nbsp;<?php echo $arrayCuentas['cobro_juridico']['credito'] ?></div>
		<div class="divIcono"  style="border-top:1px solid #D4D4D4;" onclick="ventanaBuscarCuenta('cobro_juridico','credito')">
			<img src="img/buscar20.png" title="Buscar Cuenta">
		</div>

	</div>
	<div id="loadForm" style="display:none;"></div>
</div>
<script>

	function ventanaBuscarCuenta(estado,naturaleza) {

		Win_Ventana_buscar_cuenta_puc = new Ext.Window({
		    width       : 680,
		    height      : 530,
		    id          : 'Win_Ventana_buscar_cuenta_puc',
		    title       : 'Cuentas Niif',
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
					opc          : 'niif',
					nombreGrilla : 'puc_niif',
					tabla_puc    : 'puc_niif',
					cargaFuncion : 'guardarCuenta(id,"'+estado+'","'+naturaleza+'")',
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
		                    handler     : function(){ BloqBtn(this); Win_Ventana_buscar_cuenta_puc.close(id) }
		                }
		            ]
		        }
		    ]
		}).show();
	}

	function guardarCuenta(id,estado,naturaleza) {
		var cuenta = document.getElementById('div_puc_niif_cuenta_'+id).innerHTML;
		MyLoading2('on');
		Ext.get('loadForm').load({
			url     : 'cuentas_deterioro_cartera_niif/bd/bd_cartera_clientes.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc        : 'guardarCuenta',
				id_cuenta  : id,
				cuenta     : cuenta,
				estado     : estado,
				naturaleza : naturaleza,
			}
		});
	}

</script>