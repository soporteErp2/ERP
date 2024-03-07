<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	header('Content-Type: text/html; charset=utf-8');

	$id_empresa  = $_SESSION['EMPRESA'];

	if ($id_diferido>0) {
		$sql="SELECT
				id_documento,
				tipo_documento,
				consecutivo_documento,
				id_tercero,
				documento_tercero,
				tercero,
				fecha_inicio,
				estado,
				id_cuenta_debito,
				cuenta_debito,
				descripcion_cuenta_debito,
				id_cuenta_credito,
				cuenta_credito,
				descripcion_cuenta_credito,
				id_centro_costos,
				cod_centro_costos,
				centro_costos,
				valor,
				meses,
				saldo,
				id_usuario,
				documento_usuario,
				nombre_usuario,
				id_sucursal,
				id_empresa
			FROM diferidos
			WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_diferido
				";
		$query=$mysql->query($sql,$mysql->link);

		$id_documento               = $mysql->result($query,0,'id_documento');
		$tipo_documento             = $mysql->result($query,0,'tipo_documento');
		$consecutivo_documento      = $mysql->result($query,0,'consecutivo_documento');
		$id_tercero                 = $mysql->result($query,0,'id_tercero');
		$documento_tercero          = $mysql->result($query,0,'documento_tercero');
		$tercero                    = $mysql->result($query,0,'tercero');
		$fecha_inicio               = $mysql->result($query,0,'fecha_inicio');
		$estado                     = $mysql->result($query,0,'estado');
		$id_cuenta_debito           = $mysql->result($query,0,'id_cuenta_debito');
		$cuenta_debito              = $mysql->result($query,0,'cuenta_debito');
		$descripcion_cuenta_debito  = $mysql->result($query,0,'descripcion_cuenta_debito');
		$cuenta_credito             = $mysql->result($query,0,'id_cuenta_credito');
		$id_cuenta_credito          = $mysql->result($query,0,'cuenta_credito');
		$descripcion_cuenta_credito = $mysql->result($query,0,'descripcion_cuenta_credito');
		$id_centro_costos           = $mysql->result($query,0,'id_centro_costos');
		$cod_centro_costos          = $mysql->result($query,0,'cod_centro_costos');
		$centro_costos              = $mysql->result($query,0,'centro_costos');
		$valor                      = $mysql->result($query,0,'valor');
		$meses                      = $mysql->result($query,0,'meses');
		$saldo                      = $mysql->result($query,0,'saldo');
		$id_sucursal                = $mysql->result($query,0,'id_sucursal');

		$acumscript ="
						document.getElementById('tipo_documento').value             = '$tipo_documento';
						document.getElementById('documento').setAttribute('data-value',$id_documento);
						document.getElementById('documento').value                  = '$consecutivo_documento';
						document.getElementById('documento_tercero').setAttribute('data-value',$id_tercero);
						document.getElementById('documento_tercero').value          = '$documento_tercero';
						document.getElementById('tercero').value                    = '".utf8_encode($tercero)."';
						document.getElementById('fecha_inicio').value               = '$fecha_inicio';
						document.getElementById('estado').value                     = '$estado';
						document.getElementById('cuenta_debito').setAttribute('data-value',$id_cuenta_debito);
						document.getElementById('cuenta_debito').value              = '$cuenta_debito';
						document.getElementById('descripcion_cuenta_debito').value  = '$descripcion_cuenta_debito';
						document.getElementById('cuenta_credito').setAttribute('data-value',$id_cuenta_credito);
						document.getElementById('cuenta_credito').value             = '$cuenta_credito';
						document.getElementById('descripcion_cuenta_credito').value = '$descripcion_cuenta_credito';
						document.getElementById('centro_costos').setAttribute('data-id','$id_centro_costos');
						document.getElementById('centro_costos').setAttribute('data-codigo','$cod_centro_costos');
						document.getElementById('centro_costos').setAttribute('data-nombre','$centro_costos');
						document.getElementById('centro_costos').value              = '$cod_centro_costos - $centro_costos';
						document.getElementById('valor').value                      = '$valor';
						document.getElementById('meses').value                      = '$meses';
						document.getElementById('saldo').value                      = '$saldo';
						document.getElementById('cuota').value                      = '".(round($valor/$meses,$_SESSION['DECIMALESMONEDA']))."';
						document.getElementById('id_sucursal').value                = '$id_sucursal';

						document.getElementById('tipo_documento').disabled = true;
						document.getElementById('id_sucursal').disabled    = true;
						document.getElementById('documento').style.width   = '190px';
						document.getElementById('valor').readOnly          = true;

					";
	}
	else{
		$btn_buscar_doc = '<img onclick="buscar_documento_cruce()" src="img/buscar.png" id="img_buscar_doc">';
		$acumscript .= '
						Ext.getCmp("btn_eliminar").hide()
						new Ext.form.DateField({
						    format     : "Y-m-d",               //FORMATO
						    width      : 190,                   //ANCHO
						    allowBlank : false,
						    showToday  : false,
						    applyTo    : "fecha_inicio",
						    editable   : false,                 //EDITABLE
						    value      : new Date(),             //VALOR POR DEFECTO
						    listeners  : { select: function() {   } }
						});
						';
	}

	$sql="SELECT id,nombre FROM empresas_sucursales WHERE activo=1 AND id_empresa=$id_empresa";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$sucursales.='<option value="'.$row['id'].'">'.$row['nombre'].'</option>';
	}

	$sql="SELECT id_parentesco,nombres,apellidos,ocupacion,direccion,telefono,celular FROM empleados_informacion_contacto WHERE activo=1 AND id=$id";
	$query=$mysql->query($sql,$mysql->link);

	$id_parentesco = $mysql->result($query,0,'id_parentesco');
	$nombres       = $mysql->result($query,0,'nombres');
	$apellidos     = $mysql->result($query,0,'apellidos');
	$ocupacion     = $mysql->result($query,0,'ocupacion');
	$direccion     = $mysql->result($query,0,'direccion');
	$telefono      = $mysql->result($query,0,'telefono');
	$celular       = $mysql->result($query,0,'celular');

?>

<style>
	img{
		cursor: pointer;
	}
</style>
<div class="content" >

	<table class="table-form" style="width:90%;" >
		<tr class="thead" style="background-color: #a2a2a2;">
			<td colspan="3">GENERAL</td>
		</tr>
		<tr>
			<td>Sucursal</td>
			<td colspan="2">
				<select style="width:190px;" data-requiere="true" id="id_sucursal" >
					<option value="">Seleccione...</option>
					<?php echo $sucursales; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Tipo Documento</td>
			<td colspan="2">
				<select style="width:190px;" data-requiere="true" id="tipo_documento" >
					<option value="">Seleccione...</option>
					<option value="FC">FC - Factura de Compra</option>
					<option value="CE">CE - Comprobante de Egreso</option>
					<option value="RC">RC - Recibo de caja</option>
					<option value="NCG">NCG - Nota Contable General</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Documento</td>
			<td><input type="text" readonly value="<?php echo $nombres; ?>" style="width:190px;" data-requiere="true" id="documento" data-value="" ></td>
			<td style="padding:0px;"><?php echo $btn_buscar_doc; ?></td>
		</tr>
		<tr>
			<td>Documento Tercero</td>
			<td colspan="2"><input type="text" readonly style="width:190px;" value="<?php echo $apellidos; ?>" id="documento_tercero" data-value=""></td>
		</tr>
		<tr>
			<td>Tercero</td>
			<td colspan="2"><input type="text" readonly value="<?php echo $ocupacion; ?>" style="width:190px;"  id="tercero"></td>
		</tr>
		<tr class="thead" style="background-color: #a2a2a2;">
			<td colspan="3">CONTABILIDAD COLGAAP</td>
		</tr>
		<tr>
			<td>Fecha Inicio</td>
			<td colspan="2"><input type="text" value="<?php echo $ocupacion; ?>" style="width:190px;" id="fecha_inicio" readonly ></td>
		</tr>
		<tr>
			<td>Estado</td>
			<td colspan="2">
				<select style="width:190px;" id="estado" >
					<option value="Activo">Activo</option>
					<option value="Inactivo">Inactivo</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Valor</td>
			<td colspan="2"><input type="text" value="<?php echo $ocupacion; ?>" style="width:190px;" data-requiere="true" id="valor" onkeyup="calculacuota()"></td>
		</tr>
		<tr>
			<td>Meses</td>
			<td colspan="2"><input type="text" value="<?php echo $ocupacion; ?>" style="width:190px;" data-requiere="true" id="meses" onkeyup="calculacuota()"></td>
		</tr>
		<tr>
			<td>Cuota</td>
			<td colspan="2"><input readonly type="text" value="<?php echo $ocupacion; ?>" style="width:190px;" id="cuota"></td>
		</tr>
		<tr>
			<td>Saldo</td>
			<td colspan="2"><input readonly type="text" value="<?php echo $ocupacion; ?>" style="width:190px;" id="saldo"></td>
		</tr>
		<tr>
			<td>Cuenta Debito</td>
			<td><input type="text" readonly value="<?php echo $direccion; ?>" style="width:190px;" data-requiere="true" id="cuenta_debito" data-value=""></td>
			<td style="padding:0px;" onclick="ventanaBuscarCuenta('cuenta_debito','descripcion_cuenta_debito')"><img src="img/buscar.png"></td>
		</tr>
		<tr>
			<td>Descripcion Cuenta</td>
			<td colspan="2"><input type="text" readonly value="<?php echo $telefono; ?>" style="width:190px;" id="descripcion_cuenta_debito" ></td>
		</tr>
		<tr>
			<td>Cuenta Credito</td>
			<td><input type="text" readonly value="<?php echo $direccion; ?>" style="width:190px;" data-requiere="true" id="cuenta_credito" data-value=""></td>
			<td style="padding:0px;" onclick="ventanaBuscarCuenta('cuenta_credito','descripcion_cuenta_credito')"><img src="img/buscar.png"></td>
		</tr>
		<tr>
			<td>Descripcion Cuenta</td>
			<td colspan="2"><input type="text" readonly value="<?php echo $telefono; ?>" style="width:190px;" id="descripcion_cuenta_credito" ></td>
		</tr>
		<tr>
			<td>Centro de Costos</td>
			<td><input type="text" readonly value="<?php echo $centro_costos; ?>" style="width:190px;" data-requiere="true" id="centro_costos" data-id="" data-codigo="" data-nombre="" ></td>
			<td style="padding:0px;" onclick="ventanaBuscarCentroCostos()"><img src="img/buscar.png"></td>
		</tr>
		<!--
		<tr class="thead" style="background-color: #a2a2a2;">
			<td colspan="3">CONTABILIDAD NIIF</td>
		</tr>
		<tr>
			<td>Valor</td>
			<td colspan="2"><input type="text" value="<?php echo $ocupacion; ?>" style="width:190px;" data-requiere="true" id="valor"></td>
		</tr>
		<tr>
			<td>Meses</td>
			<td colspan="2"><input type="text" value="<?php echo $ocupacion; ?>" style="width:190px;" data-requiere="true" id="meses"></td>
		</tr>
		<tr>
			<td>Cuota</td>
			<td colspan="2"><input readonly type="text" value="<?php echo $ocupacion; ?>" style="width:190px;" id="cuota"></td>
		</tr>
		<tr>
			<td>Cuenta Debito</td>
			<td><input type="text" readonly value="<?php echo $direccion; ?>" style="width:155px;" data-requiere="true" id="cuenta_debito"></td>
			<td style="padding:0px;"><img src="img/buscar.png"></td>
		</tr>
		<tr>
			<td>Descripcion Cuenta</td>
			<td colspan="2"><input type="text" readonly value="<?php echo $telefono; ?>" style="width:190px;" id="descripcion_cuenta_debito" ></td>
		</tr>
		<tr>
			<td>Cuenta Credito</td>
			<td><input type="text" readonly value="<?php echo $direccion; ?>" style="width:155px;" data-requiere="true" id="cuenta_credito"></td>
			<td style="padding:0px;"><img src="img/buscar.png"></td>
		</tr>
		<tr>
			<td>Descripcion Cuenta</td>
			<td colspan="2"><input type="text" readonly value="<?php echo $telefono; ?>" style="width:190px;" id="descripcion_cuenta_credito" ></td>
		</tr>-->

	</table>
	<div id="loadForm" style="display:none;"></div>
</div>
<script>

	<?php echo $acumscript; ?>

	function buscar_documento_cruce() {
		var myalto      = Ext.getBody().getHeight()
		,	myancho     = Ext.getBody().getWidth()
		,	tipo        = document.getElementById('tipo_documento').value
		,	id_sucursal = document.getElementById('id_sucursal').value

		if (id_sucursal==''){ alert("Aviso\nDebe seleccionar la sucrsal!"); return; }
		if (tipo==''){ alert("Aviso\nDebe seleccionar el tipo de documento!"); return; }

		Win_Ventana_documento_cruce = new Ext.Window({
		    width       : myancho-100,
		    height      : myalto-50,
		    id          : 'Win_Ventana_documento_cruce',
		    title       : 'Documento',
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : 'amortizaciones/diferidos/bd/buscarDocumentoCruce.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
					tipo         : tipo,
					id_sucursal  : id_sucursal,
					cargaFuncion :'rederizaVentanaDocumento(id);',
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
		                    handler     : function(){ BloqBtn(this); Win_Ventana_documento_cruce.close(id) }
		                }
		            ]
		        }
		    ]
		}).show();
	}

	function rederizaVentanaDocumento(id) {
		var	tipo              = document.getElementById('tipo_documento').value
		,	id_tercero        = document.getElementById('id_tercero_'+id).value
		,	documento         = ''
		,	documento_tercero = ''
		,	tercero           = ''

		if (tipo=='FC'){
			documento         = document.getElementById('div_compras_facturas_consecutivo_'+id).innerHTML;
			documento_tercero = document.getElementById('div_compras_facturas_nit_'+id).innerHTML;
			tercero           = document.getElementById('div_compras_facturas_proveedor_'+id).innerHTML;
		}
		if (tipo=='CE'){
			documento         = document.getElementById('div_comprobante_egreso_consecutivo_'+id).innerHTML;
			documento_tercero = document.getElementById('div_comprobante_egreso_nit_tercero_'+id).innerHTML;
			tercero           = document.getElementById('div_comprobante_egreso_tercero_'+id).innerHTML;
		}
		if (tipo=='RC'){
			documento         = document.getElementById('div_recibo_caja_consecutivo_'+id).innerHTML;
			documento_tercero = document.getElementById('div_recibo_caja_nit_tercero_'+id).innerHTML;
			tercero           = document.getElementById('div_recibo_caja_tercero_'+id).innerHTML;
		}
		if (tipo=='NCG'){
			documento         = document.getElementById('div_nota_contable_general_consecutivo_'+id).innerHTML;
			documento_tercero = document.getElementById('div_nota_contable_general_numero_identificacion_tercero_'+id).innerHTML;
			tercero           = document.getElementById('div_nota_contable_general_tercero_'+id).innerHTML;
		}

		document.getElementById('documento').setAttribute('data-value',id);
		document.getElementById('documento_tercero').setAttribute('data-value',id_tercero);
		document.getElementById('documento').value         = documento;
		document.getElementById('documento_tercero').value = documento_tercero;
		document.getElementById('tercero').value           = tercero;
		Win_Ventana_documento_cruce.close();
	}

	function calculacuota(){
		var valor = document.getElementById('valor').value
		,	meses = document.getElementById('meses').value;

		if (isNaN(valor)===false && isNaN(meses)===false && valor!=0 && meses!=0) {
			var cuota = valor/meses;
			document.getElementById('cuota').value= cuota.toFixed(<?php echo $_SESSION['DECIMALESMONEDA'] ?>);
		}
		else{
			document.getElementById('cuota').value='';
		}
	}

	function ventanaBuscarCuenta(campoId,campoText){

			Win_VentanaBuscarCuenta = new Ext.Window({
	            width       : 680,
	            height      : 500,
	            id          : 'Win_VentanaBuscarCuenta',
	            title       : 'Buscar Cuenta Colgaap',
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
								opc          : 'colgaap',
								nombreGrilla : 'buscar_cuenta',
								cargaFuncion : 'renderizaResultadoVentanaBuscarCuenta(id,"'+campoId+'","'+campoText+'")',
                			}
	            },
	            tbar        :
	            [
	                {
						xtype     : 'button',
						text      : 'Regresar',
						scale     : 'large',
						iconCls   : 'regresar',
						iconAlign : 'top',
						handler   : function(){ Win_VentanaBuscarCuenta.close(); }
	                }
	            ]
	        }).show();
	}

	function renderizaResultadoVentanaBuscarCuenta(id,campoId,campoText){
			var cuenta = document.getElementById('div_buscar_cuenta_cuenta_'+id).innerHTML
			,	descripcion = document.getElementById('div_buscar_cuenta_descripcion_'+id).innerHTML

			document.getElementById(campoId).setAttribute('data-value',id);
			document.getElementById(campoId).value   = cuenta;
			document.getElementById(campoText).value = descripcion;

			Win_VentanaBuscarCuenta.close();
	}

	function ventanaBuscarCentroCostos(){

			Win_VentanaBuscarCentroCostos = new Ext.Window({
	            width       : 550,
	            height      : 430,
	            id          : 'Win_VentanaBuscarCentroCostos',
	            title       : 'Buscar Centro Costos',
	            modal       : true,
	            autoScroll  : false,
	            closable    : false,
	            autoDestroy : true,
	            autoLoad    :
	            {
	                url     : '../funciones_globales/grillas/grillaBuscarCentroCostos.php',
	                scripts : true,
	                nocache : true,
	                params  :
	                		{
								cargaFunction : 'renderizaResultadoVentanaBuscarCentroCostos(id);return;',
                			}
	            },
	            tbar        :
	            [
	                {
						xtype     : 'button',
						text      : 'Regresar',
						scale     : 'large',
						iconCls   : 'regresar',
						iconAlign : 'top',
						handler   : function(){ Win_VentanaBuscarCentroCostos.close(); }
	                }
	            ]
	        }).show();
	}

	function renderizaResultadoVentanaBuscarCentroCostos(id){
			var codigo=document.getElementById('div_CentroCostos_codigo_'+id).innerHTML
			,	nombre=document.getElementById('div_CentroCostos_nombre_'+id).innerHTML;

			document.getElementById('centro_costos').setAttribute('data-id',id);
			document.getElementById('centro_costos').setAttribute('data-codigo',codigo);
			document.getElementById('centro_costos').setAttribute('data-nombre',nombre);

			document.getElementById('centro_costos').value   = codigo+' - '+nombre;

			Win_VentanaBuscarCentroCostos.close();
	}

	function guardarDiferido() {
		// CAPTURAR INPUTS
		var id_sucursal                = document.getElementById('id_sucursal').value
		,	id_documento               = document.getElementById('documento').getAttribute('data-value')
		,	tipo_documento             = document.getElementById('tipo_documento').value
		,	documento                  = document.getElementById('documento').value
		,	id_tercero                 = document.getElementById('documento_tercero').getAttribute('data-value')
		,	documento_tercero          = document.getElementById('documento_tercero').value
		,	tercero                    = document.getElementById('tercero').value
		,	fecha_inicio               = document.getElementById('fecha_inicio').value
		,	estado                     = document.getElementById('estado').value
		,	valor                      = document.getElementById('valor').value
		,	meses                      = document.getElementById('meses').value
		,	cuota                      = document.getElementById('cuota').value
		,	id_cuenta_debito           = document.getElementById('cuenta_debito').getAttribute('data-value')
		,	cuenta_debito              = document.getElementById('cuenta_debito').value
		,	descripcion_cuenta_debito  = document.getElementById('descripcion_cuenta_debito').value
		,	id_cuenta_credito          = document.getElementById('cuenta_credito').getAttribute('data-value')
		,	cuenta_credito             = document.getElementById('cuenta_credito').value
		,	descripcion_cuenta_credito = document.getElementById('descripcion_cuenta_credito').value
		,	id_centro_costos           = document.getElementById('centro_costos').getAttribute('data-id')
		,	cod_centro_costos          = document.getElementById('centro_costos').getAttribute('data-codigo')
		,	centro_costos              = document.getElementById('centro_costos').getAttribute('data-nombre')

		// VALIDAR INPUTS
		if (id_sucursal       =='') { alert('Aviso\nSeleccione la Sucursal'); return; }
		if (id_documento      =='') { alert('Aviso\nSeleccione el documento'); return; }
		if (fecha_inicio      =='') { alert('Aviso\nIngrese la fecha de inicio'); return; }
		if (valor             =='') { alert('Aviso\nIngrese el Valor'); return; }
		if (meses             =='') { alert('Aviso\nIngrese los meses'); return; }
		if (id_cuenta_debito  =='') { alert('Aviso\nSeleccione la cuenta Debito'); return; }
		if (id_cuenta_credito =='') { alert('Aviso\nSeleccione la cuenta Credito'); return; }

		MyLoading2('on');

		// REALIZAR LA PETICION
		Ext.get('loadForm').load({
			url     : 'amortizaciones/diferidos/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc                        : 'agregarActualizarDiferido',
				id_diferido                : '<?php echo $id_diferido ?>',
				id_sucursal                : id_sucursal,
				id_documento               : id_documento,
				tipo_documento             : tipo_documento,
				consecutivo_documento      : documento,
				id_tercero                 : id_tercero,
				documento_tercero          : documento_tercero,
				tercero                    : tercero,
				fecha_inicio               : fecha_inicio,
				estado                     : estado,
				valor_diferido             : valor,
				meses                      : meses,
				cuota                      : cuota,
				id_cuenta_debito           : id_cuenta_debito,
				cuenta_debito              : cuenta_debito,
				descripcion_cuenta_debito  : descripcion_cuenta_debito,
				id_cuenta_credito          : id_cuenta_credito,
				cuenta_credito             : cuenta_credito,
				descripcion_cuenta_credito : descripcion_cuenta_credito,
				id_centro_costos           : id_centro_costos,
				cod_centro_costos          : cod_centro_costos,
				centro_costos              : centro_costos,
			}
		});
	}

	function eliminarDiferido() {
		if (!confirm("Advertencia!\nDesea Eliminar el registro?")) { return; }
		MyLoading2('on');
		Ext.get('loadForm').load({
			url     : 'amortizaciones/diferidos/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc                        : 'eliminarDiferido',
				id_diferido                : '<?php echo $id_diferido ?>',

			}
		});
	}

</script>