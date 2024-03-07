<?php

	include("../../../../configuracion/conectar.php");
    include("../../../../configuracion/define_variables.php");

	$id_empresa   = $_SESSION['EMPRESA'];
	$metodos      = '';
	$sqlMetodos   = "SELECT M.id,M.metodo,M.direccion,M.icono,M.titulo,M.configuracion
					FROM web_service_metodos AS M,
						web_service_software AS S
					WHERE M.id='$id_metodo'
						AND M.activo=1
						AND M.id_software=S.id
						AND S.id_empresa='$id_empresa'";
	$queryMetodos = mysql_query($sqlMetodos,$link);
	$textJson = mysql_result($queryMetodos, 0, 'configuracion');

	$tabla   = 'ventas_facturas';
	$visible = 'none';
	$estado1 = 'block';

	$tipo_factura   = '';
	$fecha_factura  = '';
	$id_cuenta_pago = '';
	$cuenta_pago    = 0;
	$cuenta_colgaap = '';
	$cuenta_niif    = '';
	$contraPartida_colgaap = '';
	$contraPartida_niif    = '';

	if($textJson != ''){
		$arrayJson = json_decode($textJson,true);

		$tipo_factura   = $arrayJson['tipo_factura'];
		$fecha_factura  = $arrayJson['fecha_factura'];
		$id_cuenta_pago = $arrayJson['id_cuenta_pago'];
		$cuenta_pago    = $arrayJson['nombre'];
		$cuenta_colgaap = $arrayJson['cuenta_pago_colgaap'];
		$cuenta_niif    = $arrayJson['cuenta_pago_niif'];
		$contraPartida_colgaap = $arrayJson['contraPartida_colgaap'];
		$contraPartida_niif    = $arrayJson['contraPartida_niif'];
	}

	// $tipo_factura=($tipo_factura=='FV')? 'document.getElementById("tipo_factura").value="FV";' : 'document.getElementById("tipo_factura").value="FC";' ;

	$MSucursales = user_permisos(1);

	if($MSucursales == 'false'){ $filtroS = "AND id = $id_sucursal"; }
	if($MSucursales == 'true'){ $filtroS = ""; }

	$sqlSucursal   = "SELECT id,nombre FROM empresas_sucursales WHERE id_empresa = '$id_empresa' $filtroS";
	$querySucursal = mysql_query($sqlSucursal,$link);
	$optSucursal   = '';

	while($rowS=mysql_fetch_array($querySucursal)){
		$selected     = ($rowS['id'] == $id_sucursal)? 'selected': '';
		$optSucursal .= '<option value="'.$rowS['id'].'" '.$selected.'>'.$rowS['nombre'].'</option>';
	}

?>
	<style>
		.EmpSeparador{
			float       : left;
			width       : 90%;
			color       : #333;
			padding     : 2px 0 3px 5px;
			margin      : 10px 0 8px 10px;
			font-weight : bold;
			-moz-border-radius    : 3px;
			-webkit-border-radius : 3px;
			-webkit-box-shadow    : 1px 1px 3px #666;
			-moz-box-shadow       : 1px 1px 2px #666;
			background : -webkit-linear-gradient(#DFE8F6, #CDDBF0);
			background : -moz-linear-gradient(#DFE8F6, #CDDBF0);
			background : -o-linear-gradient(#DFE8F6, #CDDBF0);
			background : linear-gradient(#DFE8F6, #CDDBF0);
		}
	</style>
	<div style="width:100%;height:100%;">
		<div id="divLoad_config_saldo_fv" style="position:absolute; width:20px; height:20px; overflow:hidden;"></div>
		<div style="margin-top:9px; width:330px; margin-left:20px; float:left;">
			<div style="width: 110px;;float: left;font-weight:bold;">Cuenta Pago:</div>
			<div style="width: calc(100% - 110px);float: left;">
				<input type="hidden" id="id_cuenta_pago" value="<?php echo $id_cuenta_pago; ?>">
				<input type="text" class="myfield" id="cuenta_pago" style="width:calc(100% - 20px);float:left;" value="<?php echo $cuenta_pago; ?>" readonly/>
				<div style="float:right;width:18px;height:18px;cursor:pointer; border:1px solid #d4d4d4; background-color: #F3F3F3; display:<?php echo $estado1; ?>;" onclick="ventanaBusquedaCuentaPago()">
					<img src="img/buscar20.png" style="width:16px;height:16px;padding-top: 1;padding-left: 1;">
				</div>
			</div>
		</div>

		<div style="margin-top:9px; width:330px; margin-left:20px; float:left;">
			<div style="width: 110px;float: left;font-weight:bold;">Cuenta Colgaap:</div>
			<div style="width: calc(100% - 110px);float: left;">
				<input type="text" class="myfield" id="cuenta_pago_colgaap" style="width:100%;float:left;" value="<?php echo $cuenta_colgaap; ?>" readonly/>
			</div>
		</div>

		<div style="margin-top:9px; width:330px; margin-left:20px; float:left;">
			<div style="width: 110px;float: left;font-weight:bold;">Cuenta Niif:</div>
			<div style="width: calc(100% - 110px);float: left;">
				<input type="text" class="myfield" id="cuenta_pago_niif" style="width:100%;float:left;" value="<?php echo $cuenta_niif; ?>" readonly/>
			</div>
		</div>

		<div style="margin-top:9px; width:330px; margin-left:20px; float:left;">
			<div style="width: 110px;float: left;font-weight:bold;">Contrapartida Cuenta Colgaap:</div>
			<div style="width: calc(100% - 110px);float: left;">
				<input type="text" class="myfield" id="contrapartida_cuenta_pago_colgaap" readonly style="width:calc(100% - 40px);float:left;" value="<?php echo $contraPartida_colgaap; ?>" />
				<div style="float:left;width:18px;height:18px;cursor:pointer;border: 1px solid #d4d4d4;background-color: #F3F3F3; display:<?php echo $estado1; ?>;" title="Sincronizar cuenta niif" onclick="sincronizarCuentaNiif()">
					<img src="img/refresh.png" style="width:16px;height:16px;padding-top: 1;padding-left: 1;">
				</div>

				<div style="float:left;width:18px;height:18px;cursor:pointer;border: 1px solid #d4d4d4;background-color: #F3F3F3; display:<?php echo $estado1; ?>;" title="Buscar" onclick="ventanaBuscarCuenta('puc')">
					<img src="img/buscar20.png" style="width:16px;height:16px;padding-top: 1;padding-left: 1;">
				</div>
			</div>
		</div>

		<div style="margin-top:9px; width:330px; margin-left:20px; float:left;">
			<div style="width: 110px;float: left;font-weight:bold;">Contrapartida Cuenta Niif:</div>
			<div style="width: calc(100% - 110px);float: left;">
				<input type="text" class="myfield" id="contrapartida_cuenta_pago_niif" readonly style="width:calc(100% - 20px);float:left;" value="<?php echo $contraPartida_niif; ?>" />
				<div style="float:right;width:18px;height:18px;cursor:pointer;border: 1px solid #d4d4d4;background-color: #F3F3F3; display:<?php echo $estado1; ?>" onclick="ventanaBuscarCuenta('niif')">
					<img src="img/buscar20.png" style="width:16px;height:16px;padding-top: 1;padding-left: 1;">
				</div>
			</div>
		</div>

	</div>

	<script>
		function ventanaBusquedaCuentaPago() {
			var myalto  = Ext.getBody().getHeight();
			var myancho = Ext.getBody().getWidth();

			Win_Ventana_buscar_cuenta_pago = new Ext.Window({
			    width       : myancho-100,
			    height      : myalto-50,
			    id          : 'Win_Ventana_buscar_cuenta_pago',
			    title       : 'Buscar Cuenta de Pago',
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : '../funciones_globales/grillas/BusquedaCuentaPago.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
						nombre_grilla : 'buscar_cuenta_pago',
						cargaFuncion  : 'responseVentanaBuscarCuentaPago(id);',
						sql           : 'AND tipo="Compra" AND estado="Credito"',
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
			                    text        : 'Regresar',
			                    scale       : 'large',
			                    iconCls     : 'regresar',
			                    iconAlign   : 'left',
			                    handler     : function(){ Win_Ventana_buscar_cuenta_pago.close(id) }
			                }
			            ]
			        }
			    ]
			}).show();
		}

		//RENDERIZA LA VENTA QUE BUSCA LA CUENTA DE PAGO
		function responseVentanaBuscarCuentaPago(id) {
			var cuenta_pago         = document.getElementById('div_buscar_cuenta_pago_nombre_'+id).innerHTML;
			var cuenta_pago_colgaap = document.getElementById('div_buscar_cuenta_pago_cuenta_'+id).innerHTML;
			var cuenta_pago_niif    = document.getElementById('div_buscar_cuenta_pago_cuenta_niif_'+id).innerHTML;

			document.getElementById('id_cuenta_pago').value      = id;
			document.getElementById('cuenta_pago').value         = cuenta_pago;
			document.getElementById('cuenta_pago_colgaap').value = cuenta_pago_colgaap;
			document.getElementById('cuenta_pago_niif').value    = cuenta_pago_niif;

			Win_Ventana_buscar_cuenta_pago.close(id);
		}

		//BUSCAR LA CUENTA DE CONTRAPARTIDA
		function ventanaBuscarCuenta(opc) {
			var myalto  = Ext.getBody().getHeight();
	        var myancho = Ext.getBody().getWidth();

	        var title = (opc=='puc')? 'PUC' : 'NIIF';

			Win_Ventana_buscar_cuenta = new Ext.Window({
			    width       : myancho-100,
			    height      : myalto-50,
			    id          : 'Win_Ventana_buscar_cuenta',
			    title       : 'Consultar la cuenta '+title,
			    modal       : true,
			    autoScroll  : false,
			    closable    : true,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : '../funciones_globales/grillas/BuscarCuentaPuc.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
						nombreGrilla : 'buscarCuentaSaldoInterface',
						cargaFuncion : 'renderizaResultadoVentanaPuc(id,"'+opc+'")',
						opc          : opc,

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
			                    text        : 'Regresar',
			                    scale       : 'large',
			                    iconCls     : 'regresar',
			                    iconAlign   : 'left',
			                    handler     : function(){ Win_Ventana_buscar_cuenta.close() }
			                }
			            ]
			        }
			    ]
			}).show();
		}

		//RENDERIZAR LOS RESULTADOS DE LA VENTANA
		function renderizaResultadoVentanaPuc(id,opc){
			var campo = (opc=='niif')? 'contrapartida_cuenta_pago_niif': 'contrapartida_cuenta_pago_colgaap';

			input=document.getElementById(campo);
			input.value=document.getElementById('div_buscarCuentaSaldoInterface_cuenta_'+id).innerHTML;
			input.setAttribute("title",document.getElementById('div_buscarCuentaSaldoInterface_descripcion_'+id).innerHTML);
			Win_Ventana_buscar_cuenta.close();
		}


		//SINCRONIZAR LA CONTRAPARTIDA NIIF DE COLGAAP
		function sincronizarCuentaNiif() {
			var cuenta = document.getElementById('contrapartida_cuenta_pago_colgaap');
			if (cuenta.value==0 || cuenta.value=='') { alert("Debe seleccionar la cuenta colgaap primero"); cuenta.focus(); return; }

			Ext.get('divLoad_config_saldo_fv').load({
				url     : 'facturas_saldos_iniciales/bd/bd.php',
				scripts : true,
				nocache : true,
				params  :
				{
					opc    : 'sincronizarCuentaNiif',
					cuenta : cuenta.value,
				}
			});
		}

		function guardar_config_saldo_facturas(id_metodo){

			var id_cuenta_pago        = document.getElementById('id_cuenta_pago').value
			,	contraPartida_colgaap = document.getElementById('contrapartida_cuenta_pago_colgaap').value
			,	contraPartida_niif    = document.getElementById('contrapartida_cuenta_pago_niif').value;

			if(isNaN(id_cuenta_pago) || id_cuenta_pago==0
				|| isNaN(contraPartida_colgaap) || contraPartida_colgaap==0
				|| isNaN(contraPartida_niif) || contraPartida_niif==0){ alert("Aviso,\nTodos los campos son obligatorios!"); return; }

			Ext.get('divLoad_config_saldo_fv').load({
				url     : '../interface/SIHO/bd/bd.php',
				scripts : true,
				nocache : true,
				params  :
				{
					opc            : 'guardarConfiguracion',
					id_metodo      : id_metodo,
					id_cuenta_pago : id_cuenta_pago,
					contraPartida_colgaap : contraPartida_colgaap,
					contraPartida_niif    : contraPartida_niif,
				}
			});
		}

	</script>';

<?php
?>