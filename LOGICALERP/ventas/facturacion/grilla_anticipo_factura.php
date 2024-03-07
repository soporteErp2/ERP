<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$whereTercero = "";
	$filaGrilla   = '';
	$contFila     = 0;
	$id_empresa   = $_SESSION['EMPRESA'];

	if(!isset($opcGrilla)) $opcGrilla = $opcGrillaContable;
	if($terceroAnticipo > 0){ $whereTercero = "AND C.id_tercero='$terceroAnticipo'"; }

	//=============================// ANTICIPOS //=============================//
    //*************************************************************************//
    $sqlAnticipos   = "SELECT SUM(valor) AS valorAnticipos FROM anticipos WHERE id_documento='$idFactura' AND activo=1 AND tipo_documento='FV' AND id_empresa='$id_empresa'";
    $queryAnticipos = mysql_query($sqlAnticipos,$link);
    $totalAnticipo  = mysql_result($queryAnticipos, 0, 'valorAnticipos');
    $totalAnticipo *= 1;

	//====================// CUENTAS DE ANTICIPO //====================//
	//*****************************************************************//
	$whereCuenta = "";
	$sqlCuenta   = "SELECT cuenta FROM puc WHERE tipo='Anticipo de cliente' AND activo=1 AND id_empresa='$id_empresa'";
	$queryCuenta = mysql_query($sqlCuenta,$link);
	while ($rowCuenta = mysql_fetch_assoc($queryCuenta)) { $whereCuenta .= "C.cuenta=$rowCuenta[cuenta] OR "; }

	$whereCuenta = substr($whereCuenta, 0, -4);
	if($whereCuenta == ""){ echo '<div style="margin:10px; font-size:15px; color:blue;">No existen cuentas anticipo configuradas en el sistema!</div>'; exit; }

	$sqlAnticipo = "SELECT E.consecutivo,
						E.fecha_recibo AS fecha,
						C.id AS id_cuenta_anticipo,
						C.cuenta,
						C.cuenta_niif,
						if(C.id_tercero>0,C.nit_tercero,E.nit_tercero) AS nit_tercero,
						if(C.id_tercero>0,C.tercero,E.tercero) AS tercero,
						C.saldo_pendiente AS saldo_cuenta,
						A.id AS id_anticipo,
						A.valor AS anticipo
					FROM recibo_caja AS E
						INNER JOIN recibo_caja_cuentas AS C ON(
							C.saldo_pendiente>0
							AND C.activo=1
						)
						LEFT JOIN anticipos AS A ON(
							A.activo=1
							AND A.id_cuenta_anticipo=C.id
							AND A.id_documento='$idFactura'
							AND A.tipo_documento='FV'
							AND A.tipo_documento_anticipo='RC'
							AND A.valor > 0
							AND A.id_empresa = '$id_empresa'
						)
					WHERE E.id_empresa='$id_empresa'
						AND E.activo=1
						AND E.id=C.id_recibo_caja
						AND (E.estado=1 || E.estado=2)
						$whereTercero
						AND ($whereCuenta)
					ORDER BY E.fecha_recibo DESC";
	$queryAnticipo = mysql_query($sqlAnticipo,$link);

	while ($rowCuentas = mysql_fetch_array($queryAnticipo)) {
		$contFila++;
		$saldo    = $rowCuentas['saldo_cuenta']*1;
		$anticipo = $rowCuentas['anticipo']*1;

		$filaGrilla .= '<div class="fila_'.$opcGrilla.'" id="fila_'.$opcGrilla.'_'.$contFila.'" ondblclick="ventanaValorAnticipo(\''.$contFila.'\',\''.$rowCuentas['id_anticipo'].'\',\''.$rowCuentas['id_cuenta_anticipo'].'\');">
							<div class="campo0">
								'.$contFila.'
								<div id="div_load_'.$opcGrilla.'_'.$contFila.'" style="position:fixed; overflow:hidden; width:18px; height:18px;"></div>
							</div>
							<div class="campo3" style="width:70px;">'.$rowCuentas['fecha'].'</div>
							<div class="campo3" style="width:70px; border:none;">'.$rowCuentas['consecutivo'].'</div>
							<div class="campo3" style="width:30px; text-align:left;">RC</div>
							<div class="campo1" id="cuenta_'.$opcGrilla.'_'.$contFila.'">'.$rowCuentas['cuenta'].'</div>
							<div class="campo3" title="'.$rowCuentas['nit_tercero'].'">'.$rowCuentas['nit_tercero'].'</div>
							<div class="campo2" title="'.$rowCuentas['tercero'].'">'.$rowCuentas['tercero'].'</div>
							<div class="campo3" id="saldo_'.$opcGrilla.'_'.$contFila.'">'.$saldo.'</div>
							<div class="campo3" id="valor_'.$opcGrilla.'_'.$contFila.'">'.$anticipo.'</div>
						</div>';
	}

?>

<style>
	#grilla_<?php echo $opcGrilla; ?>{
		overflow   : hidden;
		width      : calc(100% - 30px);
		margin     : 15px;
		margin-top : 20px;
		height     : calc(100% - 30px);
	}

	#grilla_<?php echo $opcGrilla; ?> #contenedor_tabla{
		overflow              : hidden;
		width                 : calc(100% - 2px);
		height                : 100%;
		border                : 1px solid #BBB;
		border-radius         : 4px;
		-webkit-border-radius : 4px;
		-webkit-box-shadow    : 1px 1px 1px #d4d4d4;
		-moz-box-shadow       : 1px 1px 1px #d4d4d4;
		box-shadow            : 1px 1px 1px #d4d4d4;
		background-image      : url(img/MyGrillaFondo.png);
	}

	#grilla_<?php echo $opcGrilla; ?> .headTabla{
		overflow         : hidden;
		font-weight      : bold;
		width            : 100%;
		border-bottom    : 1px solid #d4d4d4;
		background-image : url(img/MyGrillaFondo.png);
	}

	#grilla_<?php echo $opcGrilla; ?> #bodyTabla {
		overflow-x       : hidden;
		overflow-y       : auto;
		width            : 100%;
		height           : calc(100% - 27px);
		background-color : #FFF;
		border-bottom    : 1px solid #d4d4d4;
	}

	#grilla_<?php echo $opcGrilla; ?> #bodyTabla > div{
		overflow      : hidden;
		height        : 22px;
		border-bottom : 1px solid #d4d4d4;
	}

	#grilla_<?php echo $opcGrilla; ?> #bodyTabla > div > div { height: 18px; background-color : #FFF; padding-top: 4px; }

	.headTabla div{
		height      : 22px;
		padding-top : 4px;
		text-align  : left;
	}

	#grilla_<?php echo $opcGrilla; ?> .fila{ background-color: #FFF; cursor: hand; }

	#grilla_<?php echo $opcGrilla; ?> .campo0{
		float            : left;
		width            : 28px;
		text-indent      : 5px;
		border-right     : 1px solid #d4d4d4;
		background-image : url(img/MyGrillaFondo.png);
	}

	#grilla_<?php echo $opcGrilla; ?> .campo1{
		float        : left;
		width        : 60px;
		text-indent  : 5px;
		border-right : 1px solid #d4d4d4;
	}

	#grilla_<?php echo $opcGrilla; ?> .campo2{
		float         : left;
		text-indent   : 5px;
		overflow      : hidden;
		white-space   : nowrap;
		text-overflow : ellipsis;
		border-right  : 1px solid #d4d4d4;
		width         : 240px;
	}

	#grilla_<?php echo $opcGrilla; ?> .campo3{
		float         : left;
		width         : 85px;
		text-indent   : 5px;
		border-right  : 1px solid #d4d4d4;
		padding-right : 5px;
	}

	#grilla_<?php echo $opcGrilla; ?> #bodyTabla .campo3{ text-align : right; }
	#grilla_<?php echo $opcGrilla; ?> #bodyTabla{ cursor : pointer; }

</style>

<div id="grilla_<?php echo $opcGrilla; ?>">
	<div id="load_grilla_<?php echo $opcGrilla; ?>" style="overflow:hidden; position:fixed; width:18px; height:18px;"></div>
	<div id="contenedor_tabla" style="overflow-x:auto;">
		<div class="headTabla" style="min-width: 820px;">
			<div class="campo0"></div>
			<div class="campo3" style="width:70px;">Fecha</div>
			<div class="campo3" style="width:105px;">Documento</div>
			<div class="campo1">Cuenta</div>
			<div class="campo3">Nit</div>
			<div class="campo2">Tercero</div>
			<div class="campo3">Saldo</div>
			<div class="campo3">Anticipo</div>
		</div>
		<div id="bodyTabla" style="min-width: 820px;"><?php echo $filaGrilla; ?></div>
	</div>
</div>

<script>

	function ventanaValorAnticipo(contFila,id_anticipo,id_cuenta_anticipo){
		var	valor_Factura  = totalFacturaVenta
		,	saldo_anticipo = document.getElementById('saldo_<?php echo $opcGrilla; ?>_'+contFila).innerHTML
		,	valor_anticipo = document.getElementById('valor_<?php echo $opcGrilla; ?>_'+contFila).innerHTML;

		valor_anticipo = (valor_anticipo.replace(/[^\d]/g, ''))*1;

        if(isNaN(valor_Factura) || valor_Factura == 0){
        	alert('Aviso,\nSolo se puede agregar el anticipo cuando el total de la factura es superior a cero.');
        	Win_Ventana_cuenta_<?php echo $opcGrilla; ?>.close();
        	return;
        }

		Win_Ventana_valor_anticipo = new Ext.Window({
		    width       : 400,
		    height      : 300,
		    id          : 'Win_Ventana_valor_anticipo',
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
					opc                : 'ventanaValorAnticipo',
					valor_Factura      : valor_Factura,
					idFactura          : '<?php echo $idFactura; ?>',
					id_anticipo        : id_anticipo,
					id_cuenta_anticipo : id_cuenta_anticipo,
					opcGrillaContable  : '<?php echo $opcGrilla; ?>',
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
		                    text        : 'Guardar',
		                    scale       : 'large',
		                    iconCls     : 'guardar',
		                    iconAlign   : 'top',
		                    hidden      : false,
		                    handler     : function(){ BloqBtn(this); guardar_valor_<?php echo $opcGrilla; ?>(contFila,id_anticipo,id_cuenta_anticipo); }
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
		                    handler     : function(){ BloqBtn(this); Win_Ventana_valor_anticipo.close(id); }
		                }
		            ]
		        }
		    ]
		}).show();
	}

	function guardar_valor_<?php echo $opcGrilla; ?>(contFila,id_anticipo,id_cuenta_anticipo){
		var valor_Factura  = totalFacturaVenta
		,	saldo_anticipo = document.getElementById("saldo_anticipo").value
		,	valor_anticipo = document.getElementById("valor_anticipo").value;

		saldo_anticipo *= 1;
		valor_anticipo *= 1;

		if(isNaN(valor_anticipo)){
			valor_anticipo = 0;
			document.getElementById("valor_anticipo").value=0;
		}
		Ext.get('load_save_<?php echo $opcGrilla; ?>').load({
			url     : 'facturacion/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc                : 'guardarAnticipo',
				contFila           : contFila,
				valor_anticipo     : valor_anticipo,
				valor_Factura	   : valor_Factura,
				idFactura          : '<?php echo $idFactura; ?>',
				id_anticipo        : id_anticipo,
				id_cuenta_anticipo : id_cuenta_anticipo,
				opcGrillaContable  : '<?php echo $opcGrilla; ?>',
			}
		});
	}

	document.getElementById("total_<?php echo $opcGrilla; ?>").innerHTML = '$ <?php echo number_format($totalAnticipo); ?>';

	function cancelar_<?php echo $opcGrilla; ?>(){
		Ext.get('load_grilla_<?php echo $opcGrilla; ?>').load({
			url     : 'facturacion/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc       : 'cancelarAnticipoFactura',
				idFactura : '<?php echo $idFactura; ?>'
			}
		});
	}


</script>