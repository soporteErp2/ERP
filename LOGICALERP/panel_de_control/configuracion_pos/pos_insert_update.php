<?php

	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");


	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $filtro_sucursal;
	$estilo      = ' class="myfield" ';

	$btn_ventana = "{
						text      : 'Guardar',
						width     : 60,
						height    : 56,
						scale     : 'large',
						iconCls   : 'guardar',
						iconAlign : 'top',
						handler   : function(){ guardarconfiguracionPos(); }
					}";

	if ($opc=='update') {
		//CONSULTAR LA INFORMACION DE LA EMPRESA SI ES REGIMEN COMUN O SIMPLIFICADO PARA EXIGIR O NO LA RESOLUCION DE LA DIAN
		$sql          = "SELECT tipo_regimen FROM empresas WHERE activo=1 AND id='$id_empresa'";
		$query        = mysql_query($sql,$link);
		$tipo_regimen = mysql_result($query,0,'tipo_regimen');

		if ($tipo_regimen == 'REGIMEN COMUN') { $estilo = ' class="myfieldObligatorio" '; }

		// REGIMEN SIMPLIFICADO

		//CONSULTAR CONFIGURACION ANTERIOR
		$sql   = "SELECT numero_resolucion_dian,fecha_resolucion_dian,prefijo,numero_inicial,numero_final,id_configuracion_cuenta_cobro,id_tercero,tercero,cantidad_consecutivos,estado
					FROM ventas_pos_configuracion
					WHERE activo=1 AND id_empresa='$id_empresa' AND id_sucursal='$id_sucursal' AND id=$id";
		$query = mysql_query($sql,$link);

		$numero_resolucion_dian = mysql_result($query,0,'numero_resolucion_dian');
		$fecha_resolucion_dian  = mysql_result($query,0,'fecha_resolucion_dian');
		$prefijo                = mysql_result($query,0,'prefijo');
		$numero_inicial         = mysql_result($query,0,'numero_inicial');
		$numero_final           = mysql_result($query,0,'numero_final');
		$idConfigCuentaPagoDB   = mysql_result($query,0,'id_configuracion_cuenta_cobro');
		$id_tercero             = mysql_result($query,0,'id_tercero');
		$tercero                = mysql_result($query,0,'tercero');
		$cantidad_consecutivos  = mysql_result($query,0,'cantidad_consecutivos');
		$estado                 = mysql_result($query,0,'estado');

		if ($estado=='block') {
			$labelBlock = '(BLOQUEADA)';
			$btn_ventana = "{
								text      : 'Regresar',
								width     : 60,
								height    : 56,
								scale     : 'large',
								iconCls   : 'regresar',
								iconAlign : 'top',
								handler   : function(){ Win_Ventana_insert_update.close(); }
							}";

			$acumScript.="
							document.getElementById('cuenta_por_pagar').disabled        = true;;
							document.getElementById('id_tercero').readOnly              = true;
							document.getElementById('tercero').readOnly                 = true;
							document.getElementById('imgBuscarProveedor').style.display = 'none';
							document.getElementById('numeroResolucion').readOnly        = true;
							document.getElementById('fechaResolucion').readOnly         = true;
							document.getElementById('prefijo').readOnly                 = true;
							document.getElementById('numeroInicialResolucion').readOnly = true;
							document.getElementById('numeroFinalResolucion').readOnly   = true;
							document.getElementById('cantidad_consecutivos').readOnly   = true;
						";

		}
		else{
			$btn_ventana.= ",{
								text      : 'Eliminar',
								width     : 60,
								height    : 56,
								scale     : 'large',
								iconCls   : 'eliminar',
								iconAlign : 'top',
								handler   : function(){ eliminarConfiguracionPos(); }
							}";

			$acumScript.="new Ext.form.DateField({
						    format     : 'Y-m-d',
						    width      : 130,
						    allowBlank : false,
						    showToday  : false,
						    applyTo    : 'fechaResolucion',
						    editable   : false,
						    // listeners  : { select: function() {   } }
						});";
		}


	}
	else{
			$acumScript.="new Ext.form.DateField({
						    format     : 'Y-m-d',
						    width      : 130,
						    allowBlank : false,
						    showToday  : false,
						    applyTo    : 'fechaResolucion',
						    editable   : false,
						    // listeners  : { select: function() {   } }
						});";
		}

	//CONSULTAR LAS CUENTAS DE PAGO
	$optionCuentasPago = '';
	$sqlCuentasPago    = "SELECT id,nombre,cuenta,id_cuenta FROM configuracion_cuentas_pago WHERE activo=1 AND id_empresa='$id_empresa' AND tipo='Venta'";
	$queryCuentasPago  = mysql_query($sqlCuentasPago,$link);

	while ($rowCuentasPago=mysql_fetch_array($queryCuentasPago)) {
		$optionCuentasPago .= '<option value="'.$rowCuentasPago['id'].'" >'.$rowCuentasPago['nombre'].'</option>';
	}

	if ($optionCuentasPago =='' ) {
	    echo'<script>
	            alert("Error!\nNo hay ninguna cuenta de pago configurada\nDirijase al panel de control->cuentas de pago\nCree una y vuelva a intentarlo");
	            Win_Ventana_insert_update.close();
	        </script>';
	    exit;
	}

	$sql   = "SELECT COUNT(id) AS cont FROM ventas_pos_configuracion WHERE activo=1 AND id_empresa=$id_empresa AND id_sucursal=$id_sucursal AND estado='true' ";
	$query = $mysql->query($sql,$mysql->link);
	$cant_config = $mysql->result($query,0,'cont');
	if ($opc=='insert' && $cant_config>1) {
		echo '<style>
				.contenedor{
					width : 100%;
					height : 100%;
				}
				.title{ text-align:center; float: left; width: 100%; margin: 20px 0px 20px 0px; font-weight:bold;}
				.mensaje{ text-align: center; float: left; width: 80%; margin-left: 30px; }
			</style>

				<div id="barraBotonesVentana"></div>
				<div class="contenedor">
					<div class="title">INFORMACION</div>
					<div class="mensaje">Existen dos resoluciones activas y configuradas, no se pueden tener mas de dos configuradas, termine de utilizar almenos una de las actuales e intentelo de nuevo</div>
				</div>

			<script>
				//barra de botones de la ventana
				var barBt = new Ext.Toolbar();
				barBt.render("barraBotonesVentana");
				barBt.add({
					xtype   : "buttongroup",
					columns : 2,
					items   :
					[
						{
							text      : "Regresar",
							width     : 60,
							height    : 56,
							scale     : "large",
							iconCls   : "regresar",
							iconAlign : "top",
							handler   : function(){ Win_Ventana_insert_update.close(); }
						}
					]
				});
				barBt.doLayout();
			</script>
			';
		exit;
	}

 ?>
<style>
	.contenedorPrincipal{
		width            : 100%;
		float            : left;
		height           : 370px;
		background-color : #DFE8F6;
	}

	.contenedorCuentaPorCobrar{

	}

	.contenedorConsecutivo{
		float       : left;
		width       : 100%;
		text-align  : center;
		margin-top  : 10px;
		height      : 100%;
	}

	.filasInfoTributaria{
		width       : 100%;
		text-align  : left;
		margin-top  : 5px;
		float       : left;
		margin-left : 20px;
	}

	.filasInfoTributaria > div{
		width: 40%;
		float: left;
		font-size: 11px;
	}

	.titulosVentanaConfiguracionPos{
		color         : #15428b;
		font-weight   : bold;
		font-size     : 11px;
		font-family   : tahoma,arial,verdana,sans-serif;
		padding-top   : 5px;
		margin-bottom : 10px;
		text-align    : center;
		font-weight   : bold;
		font-size     : 12px;
	}

	.iconBuscarTercero {
		height           : 18px;
		width            : 18px !important;
		float            : left;
		cursor           : pointer;
		border           : 1px solid #d4d4d4;
		background-color : #F3F3F3;
  	}

/*style="display: table-cell;vertical-align: middle;"*/
</style>
<div id="barraBotonesVentana"></div>
<div id="divLoad" style="background-color: #DFE8F6;display:none;" ></div>
<div class="contenedorPrincipal">
	<div class="contenedorCuentaPorCobrar">
		<div class="titulosVentanaConfiguracionPos" style="border-top:0px;">CONFIGURACION CONTABLE <?php echo $labelBlock; ?></div>
		<div class="filasInfoTributaria">
			<div >Cuenta por cobrar</div>
			<div ><select style="width:150px" readonly id="cuenta_por_pagar"><?php echo $optionCuentasPago; ?></select></div>
		</div>

		<div class="filasInfoTributaria">
			<div >
				Tercero Tickects
			</div>
			<div >
				<input type="hidden" id="id_tercero" value="<?php echo $id_tercero; ?>">
				<input type="text" value="<?php echo $tercero; ?>" readonly id="tercero" <?php echo $estilo; ?> >
			</div>
			<div class="iconBuscarTercero" onclick="ventanaBuscarTercero()" id="imgBuscarProveedor" title="Buscar Tercero">
           		<img src="img/buscar20.png">
        	</div>
		</div>


	</div>
	<div class="contenedorConsecutivo">
		<div class="titulosVentanaConfiguracionPos" style="float:left;width:100%;">INFORMACION TRIBUTARIA </div>

		<div class="filasInfoTributaria">
			<div >
				Resolucion DIAN N.
			</div>
			<div >
				<input type="text" value="<?php echo $numero_resolucion_dian; ?>" onkeyup="validaNumero(this);" id="numeroResolucion" <?php echo $estilo; ?> >
			</div>
		</div>
		<div class="filasInfoTributaria">
			<div >
				Fecha
			</div>
			<div >
				<input type="text" value="<?php echo $fecha_resolucion_dian; ?>" id="fechaResolucion" <?php echo $estilo; ?>>
			</div>
		</div>
		<div class="filasInfoTributaria">
			<div >
				Prefijo
			</div>
			<div >
				<input type="text" value="<?php echo $prefijo; ?>" id="prefijo" class="myfield">
			</div>
		</div>
		<div class="filasInfoTributaria">
			<div >
				Numero inicial
			</div>
			<div >
				<input type="text" value="<?php echo $numero_inicial; ?>" onkeyup="validaNumero(this);" id="numeroInicialResolucion" <?php echo $estilo; ?> >
			</div>
		</div>
		<div class="filasInfoTributaria">
			<div >
				Numero final
			</div>
			<div >
				<input type="text" value="<?php echo $numero_final; ?>" onkeyup="validaNumero(this);" id="numeroFinalResolucion" <?php echo $estilo; ?> >
			</div>
		</div>

		<div class="titulosVentanaConfiguracionPos" style="float:left;width:100%;margin-bottom :0px;margin-top:10px;">CONFIGURACION CAJA </div>

		<div class="filasInfoTributaria" style="margin-top:15px;">
			<div >
				Consecutivos a descargar por Caja
			</div>
			<div >
				<input type="text" value="<?php echo $cantidad_consecutivos; ?>" onkeyup="validaNumero(this);" id="cantidad_consecutivos" <?php echo $estilo; ?> >
			</div>
		</div>

	</div>
</div>

<script>
	<?php echo $acumScript; ?>
	var txtBtn = '';
	var displayBtn = '';
	if ('<?php echo $opc ?>'=='update') {}
	else if ('<?php echo $opc ?>'=='insert') {}

	//barra de botones de la ventana
	var barBt = new Ext.Toolbar();
	barBt.render('barraBotonesVentana');
	barBt.add({
		xtype   : 'buttongroup',
		columns : 2,
		items   :
		[
			<?php echo $btn_ventana; ?>
		]
	});
	barBt.doLayout();

	<?php if($idConfigCuentaPagoDB > 0){ echo 'document.getElementById("cuenta_por_pagar").value='.$idConfigCuentaPagoDB.';'; } ?>


	function guardarconfiguracionPos () {

		var prefijo                 = document.getElementById('prefijo').value
		,	numeroResolucion        = document.getElementById('numeroResolucion').value
		,	fechaResolucion         = document.getElementById('fechaResolucion').value
		,	numeroInicialResolucion = document.getElementById('numeroInicialResolucion').value
		,	numeroFinalResolucion   = document.getElementById('numeroFinalResolucion').value
		,	id_configuracion_cuenta = document.getElementById('cuenta_por_pagar').value
		,	id_tercero              = document.getElementById('id_tercero').value
		,	cantidad_consecutivos   = document.getElementById('cantidad_consecutivos').value;

		if (id_tercero=='' || id_tercero==0){ alert("Debe seleccionar el Tercero"); return; }
		if (numeroResolucion.replace(" ","")==""){ alert("Ingrese el numero de Resolucion"); return; }
		if (fechaResolucion.replace(" ","")==""){ alert("Ingrese la fecha de resolucion"); return; }
		if (numeroInicialResolucion.replace(" ","")==""){ alert("Ingrese el numero inicial"); return; }
		if (numeroFinalResolucion.replace(" ","")==""){ alert("Ingrese el numero final"); return; }
		if (cantidad_consecutivos=="" || cantidad_consecutivos==0){ alert("La cantidad de consecutivos"); return; }

		MyLoading2('on');

		Ext.get('divLoad').load({
			url     : 'configuracion_pos/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc                     : 'guardarConfiguracionPos',
				numeroResolucion        : numeroResolucion,
				fechaResolucion         : fechaResolucion,
				prefijo                 : prefijo,
				numeroInicialResolucion : numeroInicialResolucion,
				numeroFinalResolucion   : numeroFinalResolucion,
				id_configuracion_cuenta : id_configuracion_cuenta,
				id_tercero              : id_tercero,
				filtro_sucursal         : '<?php echo $filtro_sucursal ?>',
				cantidad_consecutivos   : cantidad_consecutivos
			}
		});
	}

	function eliminarConfiguracionPos() {

		Ext.MessageBox.show({
	        title: 'Confirmacion',
	        msg: 'Esta seguro que desea eliminar el registro?',
	        // buttons: Ext.MessageBox.OKCANCEL,
	        buttons: Ext.MessageBox.YESNO,
	        icon: Ext.MessageBox.WARNING,
	        fn: function(btn){
	            if(btn == 'yes'){
	            	deleteRow();
	            }
	        }
	    });

		this.deleteRow= function () {
			var numeroResolucion = document.getElementById('numeroResolucion').value;
			MyLoading2('on');

			Ext.get('divLoad').load({
				url     : 'configuracion_pos/bd/bd.php',
				scripts : true,
				nocache : true,
				params  :
				{
					opc              : 'eliminarConfiguracionPos',
					numeroResolucion : numeroResolucion,
					filtro_sucursal  : '<?php echo $filtro_sucursal ?>',
				}
			});
		}

	}

	function validaNumero(input) {
		numero = input.value;
		patron = /[^\d]/g;
        if(patron.test(numero)){ input.value = numero.replace(patron,''); }
	}

	function ventanaBuscarTercero(){
		var myalto  = Ext.getBody().getHeight();
		var myancho = Ext.getBody().getWidth();

		Win_Ventana_buscar_tercero = new Ext.Window({
		    width       : myancho-100,
		    height      : myalto-50,
		    id          : 'Win_Ventana_buscar_tercero',
		    title       : '',
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
		            sql : " AND tipo_cliente=\"Si\" ",
		            cargaFuncion : 'renderizaTercero(id)',
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
		                    handler     : function(){ BloqBtn(this); Win_Ventana_buscar_tercero.close(id) }
		                }
		            ]
		        }
		    ]
		}).show();
	}

	function renderizaTercero(id){

		document.getElementById('id_tercero').value=id;
		document.getElementById('tercero').value=document.getElementById('div_terceros_nombre_comercial_'+id).innerHTML;
		Win_Ventana_buscar_tercero.close()
	}

</script>