<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	if($idCliente == 0 || $idCliente == '' || !isset($idCliente)){ echo 'No existe un cliente seleccionado'; exit; }

	$contFilaCuenta = 0;
	$sqlCuentas   = "SELECT codigo_cuenta, cuenta, SUM(haber-debe) AS saldo_cuenta
					FROM asientos_colgaap
					WHERE activo=1
						AND LEFT(codigo_cuenta,4) = '2805'
						AND id_tercero = '$idCliente'
					GROUP BY codigo_cuenta ASC";

	$queryCuentas = mysql_query($sqlCuentas,$link);

	while ($rowCuentas = mysql_fetch_array($queryCuentas)) {
		$contFilaCuenta++;

		$filaInsertBoleta .= '<div class="filaBoleta" id="fila_boleta_'.$opcGrillaContable.'_'.$contFilaCuenta.'">
								<div class="campo0">'.$contFilaCuenta.'</div>
								<div class="campo1" id="cuenta_anticipo_'.$opcGrillaContable.'_'.$contFilaCuenta.'">'.$rowCuentas['codigo_cuenta'].'</div>
								<div class="campo2" title="'.$rowCuentas['cuenta'].'">'.$rowCuentas['cuenta'].'</div>
								<div class="campo3" id="valor_anticipo_'.$opcGrillaContable.'_'.$contFilaCuenta.'">'.$rowCuentas['saldo_cuenta'].'</div>
							</div>
							<script>
								Ext.get("fila_boleta_'.$opcGrillaContable.'_'.$contFilaCuenta.'").on("contextmenu", function(eventObj, menuAnticipo_'.$opcGrillaContable.'_'.$contFilaCuenta.'){
									eventObj.stopEvent();

									if (!this.ctxMenu) {
										this.ctxMenu = new Ext.menu.Menu({
											items :
											[
												{
													text 	: "Valor total anticipo",
													iconCls : "delete",
													handler : function(){ subMenuAnticipoTotal_'.$opcGrillaContable.'('.$contFilaCuenta.'); }
												},
												{
													text 	: "Valor parcial anticipo",
													iconCls : "delete",
													handler : function(){ subMenuAnticipoParcial_'.$opcGrillaContable.'('.$contFilaCuenta.'); }
												}
											]
										});
									}
									this.ctxMenu.show(menuAnticipo_'.$opcGrillaContable.'_'.$contFilaCuenta.');
								});
							</script>';
	}
?>

<style>
	#contenedor_formulario{
		overflow   : hidden;
		width      : calc(100% - 30px);
		margin     : 15px;
		margin-top : 0px;
	}

	#contenedor_tabla_boletas{
		overflow              : hidden;
		width                 : calc(100% - 2px);
		height                : 200px;
		/*border              : 1px solid #d4d4d4;*/
		border                : 1px solid #BBB;
		border-radius         : 4px;
		-webkit-border-radius : 4px;
		-webkit-box-shadow    : 1px 1px 1px #d4d4d4;
		-moz-box-shadow       : 1px 1px 1px #d4d4d4;
		box-shadow            : 1px 1px 1px #d4d4d4;
		background-image      : url(img/MyGrillaFondo.png);
	}

	.fila_formulario{
		min-height : 30px;
		margin-top : 5px;
		overflow   : hidden;
	}

	.divLabelFormulario{
		float : left;
		width : 100px;
	}

	.divInputFormulario{
		float       : left;
		width       : calc(100% - 100px - 20px - 10px);
		margin-left : 5px;
	}

	.divInputFormulario input, .divInputFormulario select, .divInputFormulario textarea{
		min-height            : 22px;
		width                 : 100%;
		border                : 0px solid #999;
		-webkit-border-radius : 2px;
		border-radius         : 2px;
		-webkit-box-shadow    : 0px 0px 3px #666;
		-moz-box-shadow       : 1px 1px 3px #999;
		box-shadow            : 1px 1px 3px #999;
	}

	.loadSaveFormulario{
		overflow : hidden;
		width    : 100%;
		height   : 20px;
	}

	.divLoadCedula, .divLoadBoleta{
		float : left;
		width : 20px;
	}

	.headTablaBoletas{
		overflow         : hidden;
		font-weight      : bold;
		width            : 100%;
		border-bottom    : 1px solid #d4d4d4;
		background-image : url(img/MyGrillaFondo.png);
	}

	#bodyTablaBoletas{
		overflow-x       : hidden;
		overflow-y       : auto;
		width            : 100%;
		height           : 156px;
		background-color : #FFF;
		border-bottom    : 1px solid #d4d4d4;
	}

	#bodyTablaBoletas > div{
		overflow         : hidden;
		height           : 22px;
		border-bottom    : 1px solid #d4d4d4;
	}

	#bodyTablaBoletas > div > div { height: 18px; background-color : #FFF; padding-top: 4px;}

	.headTablaBoletas div{
		height      : 22px;
		padding-top : 4px;
		text-align  : left;
	}

	.filaBoleta{ background-color: #FFF; cursor: hand; }

	.campo0{
		float            : left;
		width            : 28px;
		text-indent      : 5px;
		border-right     : 1px solid #d4d4d4;
		background-image : url(img/MyGrillaFondo.png);
	}

	.campo1{
		float       : left;
		width       : 60px;
		text-indent : 5px;
	}

	.campo2{
		float         : left;
		text-indent   : 5px;
		overflow      : hidden;
		white-space   : nowrap;
		text-overflow : ellipsis;
		border-left   : 1px solid #d4d4d4;
		border-right  : 1px solid #d4d4d4;
		width         : 240px;
	}

	.campo3{
		float       : left;
		width       : 85px;
		text-indent : 5px;
		text-align  : right;
	}

</style>
<div id="contenedor_formulario">
	<div class="loadSaveFormulario" id="loadSaveFormulario_<?php echo $opcGrillaContable; ?>"></div>
	<div id="contenedor_tabla_boletas">
		<div class="headTablaBoletas">
			<div class="campo0"></div>
			<div class="campo1">Codigo</div>
			<div class="campo2">Cuenta</div>
			<div class="campo3" >Valor Anticipo</div>
		</div>
		<div id="bodyTablaBoletas"><?php echo $filaInsertBoleta; ?></div>
	</div>
</div>

<script>

	if(document.getElementById("anticipo_cliente_<?php echo $opcGrillaContable; ?>").value > 0){
		Ext.getCmp("Btn_cancelar_anticipo_<?php echo $opcGrillaContable; ?>").enable();
	}

	function subMenuAnticipoTotal_<?php echo $opcGrillaContable; ?>(contFila){
		var cuenta_anticipo = document.getElementById('cuenta_anticipo_<?php echo $opcGrillaContable; ?>_'+contFila).innerHTML
		,	valor_anticipo  = document.getElementById('valor_anticipo_<?php echo $opcGrillaContable; ?>_'+contFila).innerHTML
		,	valor_Factura   = document.getElementById("totalAcumulado<?php echo $opcGrillaContable; ?>").innerHTML;

		valor_Factura  = (valor_Factura.replace(/[^\d]/g, ''))*1;
		valor_anticipo = (valor_anticipo.replace(/[^\d]/g, ''))*1;

        if(isNaN(valor_Factura) || valor_Factura == 0){
        	alert('Aviso,\nSolo se puede agregar el anticipo cuando el total de la factura es superior a cero.');
        	Win_Ventana_cuenta_anticipo_<?php echo $opcGrillaContable; ?>.close();
        	return;
        }
        else if(valor_Factura < valor_anticipo){ valor_anticipo =  valor_Factura; }

		Ext.get('loadSaveFormulario_<?php echo $opcGrillaContable; ?>').load({
			url     : 'facturacion/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc               : 'anticipoTotal',
				id_factura        : '<?php echo $idFacturaVenta; ?>',
				cuenta_anticipo   : cuenta_anticipo,
				valor_anticipo    : valor_anticipo,
				opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
			}
		});
	}

	function subMenuAnticipoParcial_<?php echo $opcGrillaContable; ?>(contFila){
		var myalto  = Ext.getBody().getHeight();
		var myancho = Ext.getBody().getWidth();

		Win_Ventana_anticipo_parcial_<?php echo $opcGrillaContable; ?> = new Ext.Window({
		    width       : 280,
		    height      : 180,
		    id          : 'Win_Ventana_anticipo_parcial',
		    title       : '',
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : 'facturacion/bd/bd.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
					opc               : 'ventanaAnticipoParcial',
					opcGrillaContable : '<?php echo $opcGrillaContable; ?>'
		        }
		    },
		    tbar        :
		    [
		        {
		            xtype   : 'buttongroup',
		            columns : 3,
		            title   : 'Opciones',
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
		                    handler     : function(){ guardar_anticipo_parcial_<?php echo $opcGrillaContable; ?>(contFila); }
		                },
		                {
		                    xtype       : 'button',
		                    width       : 60,
		                    height      : 56,
		                    text        : 'Regresar',
		                    scale       : 'large',
		                    iconCls     : 'regresar',
		                    iconAlign   : 'top',
		                    handler     : function(){ Win_Ventana_anticipo_parcial_<?php echo $opcGrillaContable; ?>.close(id); }
		                }
		            ]
		        }
		    ]
		}).show();
	}

	function cancelarAnticipoFactura_<?php echo $opcGrillaContable; ?>(){
		Ext.get('loadSaveFormulario_<?php echo $opcGrillaContable; ?>').load({
			url     : 'facturacion/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc               : 'cancelarAnticipoFactura',
				id_factura        : '<?php echo $idFacturaVenta; ?>',
				opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
			}
		});
	}

	function guardar_anticipo_parcial_<?php echo $opcGrillaContable; ?>(contFila){

		var valor_anticipo         = document.getElementById('input_valor_parcial_anticipo_<?php echo $opcGrillaContable; ?>').value
		,	cuenta_anticipo_grilla = document.getElementById('cuenta_anticipo_<?php echo $opcGrillaContable; ?>_'+contFila).innerHTML
		,	valor_anticipo_grilla  = document.getElementById('valor_anticipo_<?php echo $opcGrillaContable; ?>_'+contFila).innerHTML
		,	valor_Factura          = document.getElementById("totalAcumulado<?php echo $opcGrillaContable; ?>").innerHTML;

		valor_Factura         = (valor_Factura.replace(/[^\d]/g, ''))*1;
		valor_anticipo_grilla = valor_anticipo_grilla*1;

		if(isNaN(valor_anticipo) || valor_anticipo==0){ alert("Aviso,\nEl valor ingresado debe se numerico mayor a cero"); return; }
		if(valor_anticipo > valor_Factura){ alert("Aviso,\nEl valor del anticipo no puede superar al valor de la factura"); return; }
		else if(valor_anticipo > valor_anticipo_grilla){ alert("Aviso,\nEl valor ingresado no puede superar al saldo de anticipo registrado en el sistema"); return; }

		Ext.get('loadSaveAnticipoParcial_<?php echo $opcGrillaContable; ?>').load({
			url     : 'facturacion/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc               : 'anticipoParcial',
				cuenta_anticipo   : cuenta_anticipo_grilla,
				valor_anticipo    : valor_anticipo,
				id_factura        : '<?php echo $idFacturaVenta; ?>',
				opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
			}
		});
	}
</script>