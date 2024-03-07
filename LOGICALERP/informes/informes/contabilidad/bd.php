<?php
include('../../../../configuracion/conectar.php');
include('../../../../configuracion/define_variables.php');

$id_empresa = $_SESSION['EMPRESA'];

switch ($opc) {
	case 'ventana_configuracion_BC':
		ventana_configuracion_BC();
		break;

	case 'cuerpoVentanaConfiguracionCartera':
		cuerpoVentanaConfiguracionCartera($id_empresa,$link);
		break;

	case 'cuerpoVentanaConfiguracionBalancePrueba':
		cuerpoVentanaConfiguracionBalancePrueba();
		break;

	case 'buscarCuenta':
		buscarCuenta($cuenta_inicial,$cuenta_final,$link);
		break;

	case 'ventana_configuracion_PyG':
		ventana_configuracion_PyG($link);
		break;

	case 'busquedaTerceroPaginacion':
		busquedaTerceroPaginacion($opcGrillaContable,$pagina,$limite,$limit,$rows_registros,$paginas,$id_tercero,$tercero,$nit,$whereSum,$tabla,$estado,$imprimeVar,$filtro,$link,$id_empresa);
		break;

	case 'buscarCentroCostos':
		buscarCentroCostos($codigo,$id_empresa,$link);
		break;

	case 'ventanaBalanceComprobacion':
		ventanaBalanceComprobacion($id_empresa, $link);
		break;

	case 'ventanaConfiguracion_CIR':
		ventanaConfiguracion_CIR($id_empresa,$link);
		break;

	case 'ventanaLibroDiario':
		ventanaLibroDiario($id_empresa, $link);
		break;

	case 'cargarEmpleadosGuardados':
		cargarEmpleadosGuardados($arrayEmpleadosJSON,$id_empresa,$mysql);
		break;
}

function ventana_configuracion_BC(){

	$date = strtotime(date("Y-m-d"));
    $anio = date("Y", $date);
    $mes  = date("m", $date);
    $dia  = date("d",$date);

    //CALCULAR EL FINAL DEL MES
    $fechaInicial=date("Y-m-d",(mktime(0,0,0,$mes,1,$anio)-1));

	echo'<div style="width:100%; height:100%; border-top:1px solid #8DB2E3; overflow:hidden;">
			<div style="float:left; width:50%; height:100%; padding:10px; border-right:1px solid #8DB2E3; box-sizing:border-box;">
				<div style="font-weight: bolder;font-size:12px; margin-bottom:15px; text-align:center;">Tipo de Balance</div>
				<div>
					<input type="radio" name="tipo_balance" value="clasificado" id="tipo_balance" onchange="mostrarOcultarDivFechaInicio(this.value);">
					<label>Balance Clasificado</label>
				</div>
				<div style="margin-top:10px;">
					<input type="radio" name="tipo_balance" value="comparativo" id="tipo_balance" onchange="mostrarOcultarDivFechaInicio(this.value);">
					<label>Balance Comparativo</label>
				</div>
				<div style="font-weight: bolder;font-size:12px; margin-bottom:15px; margin:15px 0; text-align:center; display:none;">Rango</div>
				<div style="text-align:center; display:none;">
					<select id="rango_balance" style="width:100px;">
						<option value="anual">Anual</option>
						<option value="mensual">Mensual</option>
					</select>
				</div>
			</div>
			<div style="float:left; width:50%; height:100%; padding:10px; text-align:center; box-sizing:border-box;">
				<div style="font-weight: bolder;font-size:12px ;margin-bottom:10px;">Nivel de las cuentas</div>
				<div>
					<select id="nivel_cuenta" style="width:100px;">
						<option value="Grupos">Grupo</option>
						<option value="Cuentas">Cuenta</option>
						<option value="Subcuentas">Subcuenta</option>
						<option value="Auxiliares">Auxiliar</option>
					</select>
				</div>

				<div style="float:left;width:100%; margin-bottom:15px; margin-top:40px;text-align:center;font-weight: bolder;font-size:12px;">Fechas del Informe</div>
				<div style="display:table; text-align:center; margin:auto;">
					<div id="divFechaInicio">
						Periodo Inicial:<br>
						<input type="text" id="MyInformeFiltroFechaInicio"/>
					</div>

					<div style="margin-top:10px;">
						Periodo final:<br>
						<input type="text" id="MyInformeFiltroFechaFinal"/>
					</div>
				</div>
			</div>
		</div>



		<script>
		  	new Ext.form.DateField({
				format     : "Y-m-d",
				width      : 120,
				id         :"cmpFechaInicio",
				allowBlank : false,
				showToday  : false,
				applyTo    : "MyInformeFiltroFechaInicio",
				editable   : false,
				value      : "'.$fechaInicial.'"
		  	    // listeners  : { select: function() {   } }
		  	});

			new Ext.form.DateField({
				format     : "Y-m-d",
				width      : 120,
				allowBlank : false,
				showToday  : false,
				applyTo    : "MyInformeFiltroFechaFinal",
				editable   : false,
				value      : new Date(),
		  	    // listeners  : { select: function() {   } }
		  	});

			var elementos = document.getElementsByName("tipo_balance");

			//SI LAS VARIABLES LOCALSTORAGE TIENEN VALORES, ENTONCES MOSTRAR EN LA CONFIGURACION DE IMPRESION DEL INFORME ESAS VARIABLES
			//&& localStorage.MyInformeFiltroFechaFinal!="" && localStorage.generar!=""
			if ( typeof(localStorage.tipo_balance)!="undefined" && localStorage.tipo_balance!="") {

				for(var i=0; i<elementos.length; i++) {
					if (elementos[i].value==localStorage.tipo_balance) {tipo_balance=elementos[i].checked=true;}
				}

				document.getElementById("nivel_cuenta").value=localStorage.generar;
				document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinal;

				if (localStorage.tipo_balance=="comparativo") {
					document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicio;
				}
			}

			if (localStorage.tipo_balance=="clasificado") {
				document.getElementById("divFechaInicio").style.display="none";
				elementos[0].checked=true;
			}
			else if (localStorage.tipo_balance=="comparativo") {
				document.getElementById("divFechaInicio").style.display="block";
				elementos[1].checked=true;
			}else{
				document.getElementById("divFechaInicio").style.display="none";
				elementos[0].checked=true;
			}

			function mostrarOcultarDivFechaInicio(value){

				if (value=="clasificado") { document.getElementById("divFechaInicio").style.display="none"; }
				else if (value=="comparativo") { document.getElementById("divFechaInicio").style.display="block"; }
				else{ Win_Ventana_configurar_balance_general.close(); }
			}

		</script>';
}

function cuerpoVentanaConfiguracionCartera($id_empresa,$link){

	$optionCuentas = '';

	$sqlCuentasPago   = "SELECT cuenta,nombre FROM configuracion_cuentas_pago WHERE id_empresa=$id_empresa AND activo=1 AND tipo='Venta' AND estado='Credito' ORDER BY cuenta ASC";
    $queryCuentasPago = mysql_query($sqlCuentasPago,$link);
    while ($rowCuenta = mysql_fetch_assoc($queryCuentasPago)) {
		$optionCuentas .= '<div style="float:left; width:100%;" class="div_check_cuentas_pago_FV">
								<input type="checkbox" value="'.$rowCuenta['cuenta'].'" style="float:left; width:30px;" class="check_cuentas_pago_FV">
								<div style="float:left; text-overflow:ellipsis; overflow:hidden; width:160px; white-space: nowrap;">'.$rowCuenta['cuenta'].' '.$rowCuenta['nombre'].'</div>
							</div>';
    }


	$date = strtotime(date("Y-m-d"));
    $anio = date("Y", $date);
    $mes  = date("m", $date);
    $dia  = date("d",$date);

    //CALCULAR EL FINAL DEL MES
    $fechaInicial=date("Y-m-d",(mktime(0,0,0,$mes,1,$anio)-1));

	echo'<div style="border-top:1px solid #8DB2E3; width:100%; overflow:hidden;" id="informeFacturasCartera">

			<!-- DIV IZQUIERDO -->
			<div style="width: calc(100% - 250px - 3px); padding:0; float:left; height:100%; margin-left:18px;">

				<!-- DIV CON TERCEROS -->
				<div style="width:100%; padding:0; float:left; height:100%;">
					<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:15px; text-align:center;">FILTRAR POR CLIENTE</div>

					<div style="width:100%; height:calc(100% - 50px); background-color:#CDDBF0; overflow:hidden;">
						<div id="contenedor_formulario_configuracion">
							<div id="contenedor_tabla_configuracion">
								<div class="headTablaBoletas">
									<div class="campo0"></div>
									<div class="campo1">Nit</div>
									<div class="campo2">Cliente</div>
									<div class="campo4" style="width:25px;"><img src="img/buscar20.png" onclick="ventanaBusquedaTercero();" style="cursor: pointer;width:18px;height:18px;margin-top:0px;" title="Buscar cliente"></div>
								</div>
								<div id="bodyTablaConfiguracion"></div>
							</div>
						</div>
					</div>
				</div>

				<!-- DIV CON CCOS
				<div style="width:100%; padding:0; float:left; height:50%;">
					<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:15px; text-align:center;">FILTRAR POR CENTRO DE COSTO</div>

					<div style="width:100%; height:calc(100% - 50px); background-color:#CDDBF0; overflow:hidden;">
						<div id="contenedor_formulario_configuracion">
							<div id="contenedor_tabla_configuracion">
								<div class="headTablaBoletas">
									<div class="campo0"></div>
									<div class="campo1">Nit</div>
									<div class="campo2">Cliente</div>
									<div class="campo4" style="width:25px;"><img src="img/buscar20.png" onclick="ventanaBusquedaTercero();" style="cursor: pointer;width:18px;height:18px;margin-top:0px;" title="Buscar cliente"></div>
								</div>
								<div id="bodyTablaConfiguracion"></div>
							</div>
						</div>
					</div>
				</div> -->
			</div>

			<!-- DIV MENU DERECHO -->
			<div style="float:right; width:210px; background-color:#eff3fa; height:100%; border-left:1px solid #8DB2E3; overflow-y: auto;">
				<div style="margin-bottom:20px; font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none;" class="x-panel-header">Edades de Vencimiento</div>
				<div style="margin-left:10px; overflow:hidden;">
					<div style="margin-bottom:6px; overflow:hidden;">
						<input type="checkbox" id="plazo_por_vencer" value="por_vencer" style="float:left; width:30px">
						<div style="float:left;">Por vencer</div>
					</div>
					<div style="margin-bottom:6px; overflow:hidden;">
						<input type="checkbox" id="vencido_1_30" value="vencido_1_30" style="float:left; width:30px">
						<div style="float:left;">Vencido 1 - 30 dias</div>
					</div>
					<div style="margin-bottom:6px; overflow:hidden;">
						<input type="checkbox" id="vencido_31_60" value="vencido_31_60" style="float:left; width:30px">
						<div style="float:left;">Vencido 31 - 60 dias</div>
					</div>
					<div style="margin-bottom:6px; overflow:hidden;">
						<input type="checkbox" id="vencido_61_90" value="vencido_61_90" style="float:left; width:30px">
						<div style="float:left;">Vencido 61 - 90 dias</div>
					</div>
					<div style="margin-bottom:6px; overflow:hidden;">
						<input type="checkbox" id="vencido_mas_90" value="vencido_mas_90" style="float:left; width:30px">
						<div style="float:left;">Vencido mas de 90 dias</div>
					</div>
				</div>

				<!-- TIPO DE INFORME -->
				<div style="margin:20px 0; font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none;" class="x-panel-header">Tipo de Informe</div>
				<div style="margin-left:10px; overflow:hidden;">

					<div style="margin-bottom:6px; overflow:hidden;">
						<input type="radio" name="tipo_informe" value="detallado"  style="float:left; width:30px">
						<div style="float:left;">Detallado</div>
					</div>

					<div style="margin-bottom:6px; overflow:hidden;">
						<input type="radio" name="tipo_informe" value="totalizado_terceros"  style="float:left; width:30px">
						<div style="float:left;">Totalizado por Terceros</div>
					</div>

					<div style="margin-bottom:6px; overflow:hidden;">
						<input type="radio" name="tipo_informe" value="totalizado_edades"  style="float:left; width:30px">
						<div style="float:left;">Totalizado por Edades</div>
					</div>

				</div>

				<!-- FECHAS DE INFORME -->
				<div style="margin:10px 0; font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none;" class="x-panel-header">Fecha</div>
				<div style="margin-left:10px; overflow:hidden;">
					<div style="margin-bottom:6px; overflow:hidden;">
						<input type="radio" name="tipo_fecha_informe" value="corte" onchange="mostrarOcultarDivFechaInicioCarteraEdades(this.value);" style="float:left; width:30px">
						<div style="float:left;">Con corte a</div>
					</div>

					<div style="margin-bottom:10px; overflow:hidden;">
						<input type="radio" name="tipo_fecha_informe" value="rango_fechas" onchange="mostrarOcultarDivFechaInicioCarteraEdades(this.value);" style="float:left; width:30px">
						<div style="float:left;">Rango de Fechas</div>
					</div>
					<div style="display:table; text-align:center; margin:auto;">
						<div style="display:table-cell;" id="divFechaInicio">
							Fecha Inicial:<br>
							<input type="text" id="MyInformeFiltroFechaInicio">
						</div>

						<div style="display: table-cell;">
							Fecha Corte:<br>
							<input type="text" id="MyInformeFiltroFechaFinal">
						</div>
					</div>
				</div>

				<!-- CUENTA DE PAGO -->
				<div style="margin:10px 0; font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none;" class="x-panel-header">Cuentas</div>
				<div style="margin:10px 0 20px 0; overflow:hidden;">
					<div style="float:left; width:100%; margin-bottom: 15px;" class="check_cuentas_pago_FV">
						<input type="checkbox" value="" style="float:left; width:30px;" id="check_todas_cuentas_pago_FV" onclick="check_cuentas_pago_FV(this.checked)" checked />
						<div style="float:left; text-overflow:ellipsis; overflow:hidden; width:160px; white-space: nowrap; font-weight:bold;">TODAS LAS CUENTAS</div>
					</div>
					<div style="margin:auto; width:100%; height:80px; max-height:80px;" id="contenedor_check_cuentas_pago_FV">'.$optionCuentas.'</div>
				</div>
			</div>
		</div>
		<script>

			check_cuentas_pago_FV(document.getElementById("check_todas_cuentas_pago_FV").checked);

		  	new Ext.form.DateField({
				format     : "Y-m-d",
				width      : 100,
				id         :"cmpFechaInicio",
				allowBlank : false,
				showToday  : false,
				applyTo    : "MyInformeFiltroFechaInicio",
				editable   : false,
				value      : "'.$fechaInicial.'"
		  	    // listeners  : { select: function() {   } }
		  	});

			new Ext.form.DateField({
				format     : "Y-m-d",
				width      : 100,
				allowBlank : false,
				showToday  : false,
				applyTo    : "MyInformeFiltroFechaFinal",
				editable   : false,
				value      : new Date(),
		  	    // listeners  : { select: function() {   } }
		  	});

			var elementos = document.getElementsByName("tipo_fecha_informe");
			var elementos_informe =document.getElementsByName("tipo_informe");

			// //SI LAS VARIABLES LOCALSTORAGE TIENEN VALORES, ENTONCES MOSTRAR EN LA CONFIGURACION DE IMPRESION DEL INFORME ESAS VARIABLES

			if ( typeof(localStorage.tipo_informe_cartera_edades)!="undefined" && localStorage.tipo_informe_cartera_edades!="") {
				for(var i=0; i<elementos_informe.length; i++) {
					if (elementos_informe[i].value==localStorage.tipo_informe_cartera_edades) {tipo_informe=elementos_informe[i].checked=true;}
				}
			}
			else{ elementos_informe[0].checked=true; }

			if ( typeof(localStorage.sucursal_cartera_edades )!="undefined" && localStorage.sucursal_cartera_edades !="") {
				setTimeout(function(){document.getElementById("filtro_sucursal_cartera_edades").value=localStorage.sucursal_cartera_edades;},100);
			}

			if ( typeof(localStorage.tipo_fecha_informe)!="undefined" && localStorage.tipo_fecha_informe!="") {

				for(var i=0; i<elementos.length; i++) {
					if (elementos[i].value==localStorage.tipo_fecha_informe) { tipo_fecha_informe=elementos[i].checked=true; }
				}

				document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinalCartera;

				if (localStorage.tipo_fecha_informe=="rango_fechas") {
					document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicioCartera;
				}
			}

			if (localStorage.tipo_fecha_informe=="corte") {
				document.getElementById("divFechaInicio").style.display="none";
				elementos[0].checked=true;
			}
			else if (localStorage.tipo_fecha_informe=="rango_fechas") {
				document.getElementById("divFechaInicio").style.display="block";
				elementos[1].checked=true;
			}else{
				document.getElementById("divFechaInicio").style.display="none";
				elementos[0].checked=true;
			}

			function mostrarOcultarDivFechaInicioCarteraEdades(id){

				if (id=="corte") { document.getElementById("divFechaInicio").style.display="none"; }
				else if (id=="rango_fechas") { document.getElementById("divFechaInicio").style.display="block"; }
				else{ Win_Ventana_configurar_cartera_edades .close(); }
			}

			//CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS TERCEROS GUARDADOS
			for ( i = 0; i < arrayClientes.length; i++) {
				if (typeof(arrayClientes[i])!="undefined" && arrayClientes[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_cartera_cliente_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracion").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_cartera_cliente_"+i).innerHTML=clientesConfigurados[i];
				}
			}

			//SELECCIONAMOS LOS CHECKBOX DE LA CONSULTA ANTERIOR

			if (typeof(localStorage.plazo_por_vencer)!="undefined" ) {
				if (localStorage.plazo_por_vencer!="") {
					//COMO LOCALSTORAGE ES STRING, SE PARSEA PARA BOLEANO
					document.getElementById("plazo_por_vencer").checked = JSON.parse(localStorage.plazo_por_vencer) ;
				}
				else{ document.getElementById("plazo_por_vencer").checked = true; }
			}
			else{ document.getElementById("plazo_por_vencer").checked = true; }

			if (typeof(localStorage.vencido_1_30)!="undefined" ) {
				if (localStorage.vencido_1_30!="") {
					//COMO LOCALSTORAGE ES STRING, SE PARSEA PARA BOLEANO
					document.getElementById("vencido_1_30").checked = JSON.parse(localStorage.vencido_1_30) ;
				}
				else{ document.getElementById("vencido_1_30").checked = true; }
			}
			else{ document.getElementById("vencido_1_30").checked = true; }

			if (typeof(localStorage.vencido_31_60)!="undefined" ) {
				if (localStorage.vencido_31_60!="") {
					//COMO LOCALSTORAGE ES STRING, SE PARSEA PARA BOLEANO
					document.getElementById("vencido_31_60").checked = JSON.parse(localStorage.vencido_31_60) ;
				}
				else{ document.getElementById("vencido_31_60").checked = true; }
			}
			else{ document.getElementById("vencido_31_60").checked = true; }

			if (typeof(localStorage.vencido_61_90)!="undefined" ) {
				if (localStorage.vencido_61_90!="") {
					//COMO LOCALSTORAGE ES STRING, SE PARSEA PARA BOLEANO
					document.getElementById("vencido_61_90").checked = JSON.parse(localStorage.vencido_61_90) ;
				}
				else{ document.getElementById("vencido_61_90").checked = true; }
			}
			else{ document.getElementById("vencido_61_90").checked = true; }

			if (typeof(localStorage.vencido_mas_90)!="undefined" ) {
				if (localStorage.vencido_mas_90!="") {
					//COMO LOCALSTORAGE ES STRING, SE PARSEA PARA BOLEANO
					document.getElementById("vencido_mas_90").checked = JSON.parse(localStorage.vencido_mas_90) ;
				}
				else{ document.getElementById("vencido_mas_90").checked = true; }
			}
			else{ document.getElementById("vencido_mas_90").checked = true; }


		</script>

		<style>
			.div_check_cuentas_pago_FV:hover{ color:red; }
		</style>';
}

//========================== CUERPO DE LA VENTANA DE CONFIGURACION DEL BALANCE DE PRUEBA =======================//

function cuerpoVentanaConfiguracionBalancePrueba(){

	$date = strtotime(date("Y-m-d"));
    $anio = date("Y", $date);
    $mes  = date("m", $date);
    $dia  = date("d",$date);

    //CALCULAR EL FINAL DEL MES
    $fechaInicial = date("Y-m-d",(mktime(0,0,0,$mes,1,$anio)-1));

	echo'<div style="border-top:1px solid #8DB2E3; width:100%; overflow:hidden;" id="informeBalancePrueba">
			<!-- DIV CON LOS TERCEROS A AGREGAR -->
			<div style="width: calc(100% - 250px - 3px); padding:0; float:left; height:240px; margin-left:18px;">
				<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:20px; margin-top:15px; text-align:center;">FILTRAR POR TERCERO</div>
				<div style="float:left; margin-left:-33px; margin-top:15px;" class="x-panel-header">
				</div>
				<div style="width:100%;height:90%;background-color: #CDDBF0;overflow:hidden;">
					<div id="contenedor_formulario_configuracion">
						<div id="contenedor_tabla_configuracion">
							<div class="headTablaBoletas">
								<div class="campo0"></div>
								<div class="campo1">Nit</div>
								<div class="campo2">Tercero</div>
								<div class="campo4" style="width:25px;">
									<img src="img/buscar20.png" onclick="ventanaBusquedaTercero();" style="cursor: pointer;width:18px;height:18px;margin-top:0px;" title="Buscar tercero">
								</div>
							</div>
							<div id="bodyTablaConfiguracion"></div>
						</div>
					</div>
				</div>
			</div>

			<!-- DIV MENU DERECHO -->
			<div style="float:right; width:210px; background-color: #eff3fa; height: 100%; border-left:1px solid #8DB2E3;">
				<div style="margin-bottom:25px; font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none;" class="x-panel-header">Fechas del Informe</div>
				<div style="display:table; margin:auto;">
					<div style="overflow:hidden;" id="divFechaInicio">
						<div style="float:left; width:70px; margin-right:5px">Fecha Inicial:</div>
						<div style="float:left; width:120px;"><input type="text" id="MyInformeFiltroFechaInicio"></div>
					</div>
					<div style="overflow:hidden; margin-top:20px;">
						<div style="float:left; width:70px; margin-right:5px">Fecha Final:</div>
						<div style="float:left; width:120px;"><input type="text" id="MyInformeFiltroFechaFinal"></div>
					</div>
				</div>

				<!-- RANGO DE LAS CUENTAS -->
				<div style="margin:25px 0; font-weight: bolder; font-size:12px; border-right:0px; border-left:none; text-align:center;" class="x-panel-header">Rango de Cuentas</div>
				<div style="margin:0 7px; float:left;">
					<div style="float:left;">
						<div style="float:left; width:70px; margin-right:5px">Cuenta Inicial:</div>
						<div style="float:left; width:120px;"><input type="text" class="myField" id="cuenta_inicial" style="width:120px" onkeyup="validaCuentaPuc(event,this);"></div>
					</div>
					<div style="float:left; margin-top: 2px; margin-left:-20px; background-image: url(\'img/MyGrillaFondo.png\');" onclick="ventanaBuscarCuentaPuc(\'cuenta_inicial\')">
						<img src="img/buscar20.png" style="width:18px;height:18px;cursor:pointer;" title="Buscar cuenta en el Puc">
					</div>

					<div style="float:left; margin-top:20px;">
						<div style="float:left; width:70px; margin-right:5px">Cuenta Final:</div>
						<div style="float:left; width:120px;"><input type="text" class="myField" id="cuenta_final" style="width:120px" onkeyup="validaCuentaPuc(event,this);"></div>
					</div>
					<div style="float:left; margin-top: 22px; margin-left: -20px; background-image: url(\'img/MyGrillaFondo.png\');" onclick="ventanaBuscarCuentaPuc(\'cuenta_final\')">
						<img src="img/buscar20.png" style="width:18px;height:18px;cursor:pointer;" title="Buscar cuenta en el Puc">
					</div>
				</div>
			</div>
		</div>

		<script>

		  	new Ext.form.DateField({
				format     : "Y-m-d",
				width      : 120,
				id         :"cmpFechaInicio",
				allowBlank : false,
				showToday  : false,
				applyTo    : "MyInformeFiltroFechaInicio",
				editable   : false,
				value      : "'.$fechaInicial.'"
		  	    // listeners  : { select: function() {   } }
		  	});

			new Ext.form.DateField({
				format     : "Y-m-d",
				width      : 120,
				allowBlank : false,
				showToday  : false,
				applyTo    : "MyInformeFiltroFechaFinal",
				editable   : false,
				value      : new Date(),
		  	    // listeners  : { select: function() {   } }
		  	});


			document.getElementById("cuenta_inicial").value=(typeof(localStorage.cuenta_inicial )!="undefined")? localStorage.cuenta_inicial  : "" ;
			document.getElementById("cuenta_final").value=(typeof(localStorage.cuenta_final )!="undefined")? localStorage.cuenta_final  : "" ;

			if (typeof(localStorage.MyInformeFiltroFechaInicioBalancePrueba)!="undefined") {
				if (localStorage.MyInformeFiltroFechaInicioBalancePrueba!="") {
					document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicioBalancePrueba;
				}
			}

			if (typeof(localStorage.MyInformeFiltroFechaFinalBalancePrueba)!="undefined") {
				if (localStorage.MyInformeFiltroFechaFinalBalancePrueba!="") {
					document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinalBalancePrueba;
				}
			}

			// if (localStorage.tipo_fecha_informe=="corte") {
			// 	document.getElementById("divFechaInicio").style.display="none";
			// 	elementos[0].checked=true;
			// }
			// else if (localStorage.tipo_fecha_informe=="rango_fechas") {
			// 	document.getElementById("divFechaInicio").style.display="block";
			// 	elementos[1].checked=true;
			// }else{
			// 	document.getElementById("divFechaInicio").style.display="none";
			// 	elementos[0].checked=true;
			// }

			//CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS TERCEROS GUARDADOS
			for ( i = 0; i < arrayterceros.length; i++) {
				if (typeof(arrayterceros[i])!="undefined" && arrayterceros[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_cartera_cliente_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracion").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_cartera_cliente_"+i).innerHTML=tercerosConfigurados[i];

				}
			}

		</script>';
}

//========================= FUNCION PARA VALIDAR LAS CUENTAS DEL BALANCE DE PRUEBA ============================//
function buscarCuenta($cuenta_inicial,$cuenta_final,$link){
	$cont  = 0;
	$sql   = "SELECT cuenta FROM puc WHERE cuenta ='$cuenta_inicial' OR cuenta='$cuenta_final' AND id_empresa= ".$_SESSION['EMPRESA'];
	$query = mysql_query($sql,$link);

	while ($row=mysql_fetch_array($query)) {
		if ($row['cuenta']==$cuenta_inicial) { $cont++; }
		else if($row['cuenta']==$cuenta_final){ $cont++; }
	}
	echo $cont;
}

//======================== CUERPO DE LA VENTANA DE CONFIGURACION DEL ESTADO DE RESULTADOS ======================//
function ventana_configuracion_PyG($link){

	$date = strtotime(date("Y-m-d"));
    $anio = date("Y", $date);
    $mes  = date("m", $date);
    $dia  = date("d",$date);

    //CALCULAR EL FINAL DEL MES
    $fechaInicial=date("Y-m-d",(mktime(0,0,0,$mes,1,$anio)-1));

	echo'<div style="float:left;width:100%; border-top:1px solid #8DB2E3;  overflow:hidden;">

			<!-- DIV IZQUIERDO -->
			<div style="float:left;width:calc(60% - 2px); background-color:#eff3fa; height:100%;">

				<div style="margin-bottom:10px; font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none; border-top:0px;" class="x-panel-header">Centros de Costos</div>

				<!-- MOSTRAR TODOS LOS CENTROS DE COSTOS -->
				<div style="width:100%; height:30px; overflow:hidden;">
					<div style="cursor:pointer; padding-left: 10px; font-weight:bolder; float:left;" onclick="cambiaChekc()">
						<img src="img/checkox_false.png" id="imgCheckCC">
					</div>
					<div style="cursor:pointer; padding-top:5px; font-weight:bolder; float:left;" onclick="cambiaChekc()">
						Todos los centros de costos
					</div>
				</div>
				<!-- CAMPO DE BUSQUEDA -->
				<div style="height:25px; padding:0; background-color: #FFF; border: 1px solid #BBB; float:left; width:180px; margin-left:8px; border-bottom: none;" id="contenedor_campo_busqueda_ccos_ER">
					<input type="text" style="font-size: 12px; border:none; text-indent: 5px; width:calc(100% - 22px); height:100%; float:left; background-color:transparent;" id="inputBuscarCentroCostos" onkeyup="buscarCentroCostos(event,this)" placeholder="Codigo">

					<div style="float:left; width:22px; height:34px;">
						<img src="img/buscar20.png" onclick="ventanaBuscarCentroCostos()" id="imgBuscarCC" style="padding-top: 4px; cursor: pointer;" title="Buscar Centro de Costos">
					</div>
				</div>

				<!--CUERPO DE LA GRILLA -->
				<div style="width:100%;float:left;height:100%;">
					<div style="width:95%; height:280px; background-color:#CDDBF0; overflow:hidden;margin-left: 8px;">
						<div id="contenedor_formulario_configuracion">
							<div id="contenedor_tabla_configuracion">
								<div class="headTablaBoletas">
									<div class="campo0"></div>
									<div class="campo1">Codigo</div>
									<div class="campo3" style="width:140px;">Descripcion</div>
									<div class="campo4" style="width:25px;">&nbsp;</div>
								</div>

								<div id="bodyTablaConfiguracion" style="height:250px; position:relative;">
									<div style="width:302px; height:calc(100% - 1px); background-color:rgb(243, 243, 243); position:absolute; display:none;" id="divDisable"></div>
								</div>
							</div>
						</div>
					</div>

				</div>
			</div>

			<!-- DIV DERECHO -->
			<div style="float:left; width:40%; background-color:#eff3fa; height:100%; border-left:1px solid #99BBE8;">

				<!-- NIVEL DE LAS CUENTAS -->
				<div style="width:100%; float:left; height:70px;">
					<div style=" font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none;border-top:0px;" class="x-panel-header">Nivel de las cuentas</div>
					<div style="text-align: center;padding-top: 10px;">
						<select id="nivel_cuenta" style="width:100px;">
							<option value="Cuentas">Cuentas</option>
							<option value="Subcuentas">Subcuentas</option>
							<option value="Auxiliares">Auxiliares</option>
						</select>
					</div>
				</div>

				<!-- FECHAS -->
				<div style="width:100%; float:left; height:90px;">
					<div style="font-weight: bolder; font-size:12px; text-align:center; border-right:0px;border-left:none;margin-bottom:10px; " class="x-panel-header">Fechas del Informe</div>
					<div style="	display:table; text-align:center; margin:auto;">
						<div style="display:table-cell;" id="divFechaInicio">
							Fecha Inicial:<br>
							<input type="text" id="MyInformeFiltroFechaInicio">
						</div>

						<div style="display: table-cell;">
							Fecha Corte:<br>
							<input type="text" id="MyInformeFiltroFechaFinal">
						</div>
					</div>
				</div>

				<!-- TIPO DE INFORME -->
				<div style="width:100%; float:left; height:100px;">
					<div style="margin-bottom:20px; font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none;" class="x-panel-header">Tipo de Informe</div>

					<div style="margin-top:10px;margin-left:45px;">
						<input type="radio" name="tipo_balance" value="mensual" id="tipo_balance"  onchange="mostrarOcultarDivFechaInicio(this.value);">
						<label>Mensual</label>
					</div>
					<div style="margin-top:10px;margin-left:45px;">
						<input type="radio" name="tipo_balance" value="mensual_acumulado" id="tipo_balance" onchange="mostrarOcultarDivFechaInicio(this.value);">
						<label>Mensual Acumulado</label>
					</div>
					<div style="margin-top:10px;margin-left:45px;">
						<input type="radio" name="tipo_balance" value="comparativo_mensual" id="tipo_balance" onchange="mostrarOcultarDivFechaInicio(this.value);">
						<label>Comparativo Mensual</label>
					</div>
					<div style="margin-top:10px;margin-left:45px;display:none;">
						<input type="radio" name="tipo_balance" value="rango_fechas" id="tipo_balance" onchange="mostrarOcultarDivFechaInicio(this.value);">
						<label>Rango de Fechas</label>
					</div>
					<div style="margin-top:10px;margin-left:45px;">
						<input type="radio" name="tipo_balance" value="comparativo_anual" id="tipo_balance" onchange="mostrarOcultarDivFechaInicio(this.value);">
						<label>Comparativo Anual</label>
					</div>
				</div>

			</div>
		</div>

		<script>

		  	new Ext.form.DateField({
				format     : "Y-m-d",
				width      : 120,
				id         :"cmpFechaInicio",
				allowBlank : false,
				showToday  : false,
				applyTo    : "MyInformeFiltroFechaInicio",
				editable   : false,
				value      : "'.$fechaInicial.'"
		  	    // listeners  : { select: function() {   } }
		  	});

			new Ext.form.DateField({
				format     : "Y-m-d",
				width      : 120,
				allowBlank : false,
				showToday  : false,
				applyTo    : "MyInformeFiltroFechaFinal",
				editable   : false,
				value      : new Date(),
		  	    // listeners  : { select: function() {   } }
		  	});

			if ( typeof(localStorage.nivel_cuentas_EstadoResultado)!="undefined" && localStorage.nivel_cuentas_EstadoResultado!="") {
				document.getElementById("nivel_cuenta").value=localStorage.nivel_cuentas_EstadoResultado;
			}

			if ( typeof(localStorage.sucursales_estado_resultado)!="undefined" && localStorage.sucursales_estado_resultado!="") {
				setTimeout(function(){document.getElementById("filtro_sucursal_sucursales_estado_resultado").value=localStorage.sucursales_estado_resultado;},100);
			}

			var elementos = document.getElementsByName("tipo_balance");

			//SI LAS VARIABLES LOCALSTORAGE TIENEN VALORES, ENTONCES MOSTRAR EN LA CONFIGURACION DE IMPRESION DEL INFORME ESAS VARIABLES
			if ( typeof(localStorage.tipo_balance_EstadoResultado)!="undefined" && localStorage.tipo_balance_EstadoResultado!="") {

				for(var i=0; i<elementos.length; i++) {
					if (elementos[i].value==localStorage.tipo_balance_EstadoResultado) {elementos[i].checked=true;}
				}

				// document.getElementById("nivel_cuenta").value=localStorage.generar;
				document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinalEstadoResultado;

				if (localStorage.tipo_balance_EstadoResultado=="rango_fechas") {
					document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicioEstadoResultado;
				}

			}
			else{ elementos[0].checked=true; }

			if (localStorage.tipo_balance_EstadoResultado!="rango_fechas") { document.getElementById("divFechaInicio").style.display="none"; }
			else if (localStorage.tipo_balance_EstadoResultado=="rango_fechas") { document.getElementById("divFechaInicio").style.display="block"; }
			else{ document.getElementById("divFechaInicio").style.display="none"; }

			function mostrarOcultarDivFechaInicio(id){

				if (id!="rango_fechas") { document.getElementById("divFechaInicio").style.display="none"; }
				else if (id=="rango_fechas") { document.getElementById("divFechaInicio").style.display="block"; }
			}

			//CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS TERCEROS GUARDADOS
			for ( i = 0; i < arrayCentroCostos.length; i++) {
				if (typeof(arrayCentroCostos[i])!="undefined" && arrayCentroCostos[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_centro_costos_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracion").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_centro_costos_"+i).innerHTML=centroCostosConfigurados[i];
				}
			}

			if (checkBoxSelectAll=="true") {
				document.getElementById("imgCheckCC").setAttribute("src","img/checkox_true.png");

				document.getElementById("inputBuscarCentroCostos").readOnly = true;
				document.getElementById("divDisable").style.display         = "block";
				document.getElementById("imgBuscarCC").style.display        = "none";

				document.getElementById("contenedor_campo_busqueda_ccos_ER").style.backgroundColor = "rgb(243, 243, 243)";
			}

			//FUNCION DEL CHECKBOX
			function cambiaChekc(){
				var elemento = document.getElementById("imgCheckCC").getAttribute("src");

				if (elemento=="img/checkox_false.png") {
					document.getElementById("imgCheckCC").setAttribute("src","img/checkox_true.png");

					document.getElementById("inputBuscarCentroCostos").readOnly = true;
					document.getElementById("divDisable").style.display         = "block";
					document.getElementById("imgBuscarCC").style.display        = "none";
					checkBoxSelectAll = "true";

					document.getElementById("contenedor_campo_busqueda_ccos_ER").style.backgroundColor = "rgb(243, 243, 243)";
				}
				else{
					document.getElementById("imgCheckCC").setAttribute("src","img/checkox_false.png");

					document.getElementById("inputBuscarCentroCostos").readOnly = false;
					document.getElementById("divDisable").style.display         = "none";
					document.getElementById("imgBuscarCC").style.display        = "block";
					checkBoxSelectAll = "false";

					document.getElementById("contenedor_campo_busqueda_ccos_ER").style.backgroundColor = "#FFF";
				}
			}

		</script>';
}

//======================= FUNCION PARA PAGINAR LA BUSQUEDA DE LA VENTANA ========================================//
function busquedaTerceroPaginacion($opcGrillaContable,$pagina,$limite,$limit,$rows_registros,$paginas,$id_tercero,$tercero,$nit,$whereSum,$tabla,$estado,$imprimeVar,$filtro,$link,$id_empresa){


	//SI LA VARIABLE FILTRO NO ESTA VACIA, RECONTAMOS EL LIMITE DE LOS REGISTROS
	if ($filtro!='') {
		$sql="SELECT COUNT(id) as cont $whereSum  FROM $tabla WHERE activo=1 $estado $filtro AND id_empresa='$id_empresa'";
		$query=mysql_query($sql,$link);
		$rows_registros=mysql_result($query,0,'cont');
		$paginas=ceil( $rows_registros/$limite );

		//CREAR ARRAY CON LOS LIMITES DE LAS CONSULTAS
		$limit1     = 0;
		$limit2     = $limite;
		$acumScript = '';
		for ($i=1; $i <= $paginas; $i++) {
			$acumScript .='arrayLimitGrilla'.$opcGrillaContable.'['.$i.']="'.$limit1.','.$limit2.'";';
			$limit1     =$limit2+1;
			$limit2     =$limit2+$limite;
		}
	}
	//SI NO SE HACE LA BUSQUEDA CON FILTRO SINO DE FORMA NORMAL
	else{
		$sql="SELECT COUNT(id) as cont $whereSum  FROM $tabla WHERE activo=1 $estado AND id_empresa='$id_empresa'";
		$query=mysql_query($sql,$link);
		$rows_registros=mysql_result($query,0,'cont');
		$paginas=ceil( $rows_registros/$limite );

		//CREAR ARRAY CON LOS LIMITES DE LAS CONSULTAS
		$limit1     = 0;
		$limit2     = $limite;
		$acumScript = '';
		for ($i=1; $i <= $paginas; $i++) {
			$acumScript .='arrayLimitGrilla'.$opcGrillaContable.'['.$i.']="'.$limit1.','.$limit2.'";';
			$limit1     =$limit2+1;
			$limit2     =$limit2+$limite;
		}
	}

	//SI SE BUSCA DESDE UNA PAGINA DIFERENTE A LA 1, VALIDAR SI EL RESULTADO DA LA MISMA CANTIDAD DE PAGINAS, SINO, PONER EN PAGINA 1 EJ(9 PAGINAS CONTRA EL RESULTADO DE 1 PAGINA)
	if ($pagina>$paginas) {
		$limit='0,'.$limite;
		$pagina=1;
	}

	$sqlCuentas   = "SELECT $id_tercero,$tercero,$nit $whereSum FROM $tabla WHERE activo=1 $estado $filtro AND id_empresa='$id_empresa' GROUP BY $id_tercero ASC LIMIT $limit";
	$queryCuentas = mysql_query($sqlCuentas,$link);
	while ($rowCuentas = mysql_fetch_array($queryCuentas)) {
		$contFilaCuenta++;

		$divSaldoPendiente=($tabla!='terceros')? '<div class="campo3" id="saldo_'.$contFilaCuenta.'">'.$rowCuentas['saldo'].'</div>' : '' ;

		$filaInsertBoleta .= '<div class="filaBoleta" id="fila_boleta_'.$opcGrillaContable.'_'.$contFilaCuenta.'">
								<div class="campo0">'.$contFilaCuenta.'</div>
								<div class="campo1" id="nit_'.$rowCuentas[$id_tercero].'">'.$rowCuentas['nit'].'</div>
								<div class="campo2" style="border-left:0px;" id="tercero_'.$rowCuentas[$id_tercero].'" title="'.$rowCuentas[$tercero].'">'.$rowCuentas[$tercero].'</div>
								'.$divSaldoPendiente.'
								<div class="campo4" id="valor_anticipo_'.$opcGrillaContable.'_'.$contFilaCuenta.'">
									<input type="checkbox" id="checkbox_'.$rowCuentas[$id_tercero].'" onchange="checkGrilla(this,\''.$rowCuentas[$id_tercero].'\',\''.$tabla.'\')" value="'.$rowCuentas[$id_tercero].'">
								</div>
							</div>';
	}

	$filaInsertBoleta .= '<script>
							// console.log("'.$sqlCuentas.'");
							// console.log(arrayLimitGrilla'.$opcGrillaContable.');
							document.getElementById("labelPaginacion").innerHTML="Pagina '.$pagina.' de '.$paginas.' ";
							PaginaActual'.$opcGrillaContable.'='.$pagina.';
							MaxPage'.$opcGrillaContable.'='.$paginas.';
							arrayLimitGrilla'.$opcGrillaContable.'.length=0;
							'.$acumScript.'
							// console.log(arrayLimitGrilla'.$opcGrillaContable.');
							// console.log("'.$limit.'");
							'.$imprimeVar.'

						</script>';

		echo $filaInsertBoleta;
}

//======================= FUNCION PARA BUSCAR EL CENTRO DE COSTOS POR CODIGO =================================//
function buscarCentroCostos($codigo,$id_empresa,$link){
	$sql="SELECT id,nombre FROM centro_costos WHERE activo=1 AND id_empresa=$id_empresa AND codigo='$codigo' ";
	$query  = mysql_query($sql,$link);
	$id     = mysql_result($query,0,'id');
	$nombre = mysql_result($query,0,'nombre');

	if ($id!="" && $nombre!=""){
		echo json_encode(array('id' => $id, 'nombre' => $nombre));
	}
	else{
		echo "false";
	}
}

//========================== CUERPO DE LA VENTANA DE CONFIGURACION DEL BALANCE DE COMPROBACION =======================//
function ventanaBalanceComprobacion($id_empresa, $link){

	$optionNivel = '';
	$ultimoNivel = 0;

	$sqlNivel   = "SELECT nombre,digitos FROM puc_configuracion WHERE activo=1 AND id_empresa='$id_empresa' ORDER BY digitos ASC";
	$queryNivel = mysql_query($sqlNivel,$link);
	while ($rowNivel = mysql_fetch_assoc($queryNivel)) {
		$ultimoNivel  = $rowNivel['digitos'];
		$optionNivel .= '<option value="'.$rowNivel['digitos'].'">'.$rowNivel['nombre'].'</option>';
	}

	if($ultimoNivel > 0){ $optionNivel .= '<script>document.getElementById("nivel_cuentas_BC").value="'.$ultimoNivel.'";</script>'; }

	$date = strtotime(date("Y-m-d"));
    $anio = date("Y", $date);
    $mes  = date("m", $date);
    $dia  = date("d",$date);

    //CALCULAR EL FINAL DEL MES
    $fechaInicial = date("Y-m-d",(mktime(0,0,0,$mes,1,$anio)-1));

	echo'<div style="border-top:1px solid #8DB2E3; width:100%; overflow:hidden;" id="informeBalancePrueba">
			<!-- DIV CON LOS TERCEROS A AGREGAR -->
			<div style="width: calc(100% - 250px - 3px); padding:0; float:left; height:240px; margin-left:10px;">
				<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRAR POR TERCERO</div>

				<!-- MOSTRAR TODOS LOS TERCEROS-->
				<div style="width:100%;height:30px;">
					<spam style="cursor:pointer;padding-left: 10px;font-weight:bolder;float:left;margin-bottom: 7px;" onclick="cambiaChekc()"><img src="img/checkox_false.png" id="imgCheckTercerosBC">Todos los Terceros</spam><br>
					<!--<spam style="float:right;font-weight: bolder;font-size:13px;">FILTRO POR TERCERO</spam>-->
				</div>

				<!-- CAMPO BUSCAR TERCERO -->
				<div style="display:none;height: 28px;padding-top: 5px;padding: 5 0 0 6;background-color: #F3F3F3;border-top-left-radius: 5;border: 1px solid #D4D4D4;float: left;width: 140px;border-bottom: 0px;">
					<!--<input type="text" style="border-radius: 3px;font-size: 12px;min-height: 22px;border: 1px solid #D4D4D4;text-indent: 5px;" id="inputBuscarCentroCostos" onkeyup=" buscarterceroBC(event,this)" placeholder="nit tercero...">-->
				</div>

				<!-- VENTANA BUSCAR TERCERO -->
				<div style="display:none;background-color: #F3F3F3;border-right: 1px solid #D4D4D4;border-top: 1px solid #D4D4D4;float: left;height: 26px;width: 35px;border-top-right-radius: 5px;padding: 7 0 0 7;">
					<img src="img/buscar20.png" onclick="ventanaBusquedaTercero();" style="cursor: pointer;width:21px;height:21px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC5">
				</div>

				<div style="width:100%;height:90%;background-color: #CDDBF0;overflow:hidden;">
					<div id="contenedor_formulario_configuracion">
						<div id="contenedor_tabla_configuracion">
							<div class="headTablaBoletas">
								<div class="campo0">&nbsp;</div>
								<div class="campo1">Nit</div>
								<div class="campo2">Tercero</div>
								<div class="campo4" style="width:25px;"><img src="img/buscar20.png" onclick="ventanaBusquedaTercero();" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC"></div>
							</div>
							<div id="bodyTablaConfiguracion">
								<div style="width: 481px; height: 164px; position: absolute; display: none; background-color: rgba(214, 211, 211, 0.639216);" id="divDisableBC"></div>
							</div>

						</div>
					</div>
				</div>
			</div>

			<!-- DIV MENU DERECHO -->
			<div style="float:right; width:230px; background-color: #eff3fa; height: 100%; border-left:1px solid #8DB2E3; overflow-y:auto;">
				<div style="margin-bottom:25px; font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none;border-top:none;" class="x-panel-header">Fechas del Informe</div>
				<div style="display:table; margin:auto;">
					<div style="overflow:hidden;" id="divFechaInicio">
						<div style="float:left; width:70px; margin-right:5px">Fecha Inicial:</div>
						<div style="float:left; width:120px;"><input type="text" id="MyInformeFiltroFechaInicio"></div>
					</div>
					<div style="overflow:hidden; margin-top:20px;">
						<div style="float:left; width:70px; margin-right:5px">Fecha Final:</div>
						<div style="float:left; width:120px;"><input type="text" id="MyInformeFiltroFechaFinal"></div>
					</div>
				</div>

				<!-- RANGO DE LAS CUENTAS -->
				<div style="margin:25px 0; font-weight: bolder; font-size:12px; border-right:0px; border-left:none; text-align:center;" class="x-panel-header">Rango de Cuentas</div>
				<div style="margin:0 7px; overflow:hidden;">
					<div style="float:left;">
						<div style="float:left; width:70px; margin-right:5px">Cuenta Inicial:</div>
						<div style="float:left; width:120px;"><input type="text" class="myField" id="cuenta_inicial" style="width:120px" onkeyup="validaCuentaPuc(event,this);"></div>
					</div>
					<div style="float:left; margin-top: 2px; margin-left:-20px; background-image: url(\'img/MyGrillaFondo.png\');" onclick="ventanaBuscarCuentaPuc(\'cuenta_inicial\')">
						<img src="img/buscar20.png" style="width:18px;height:18px;cursor:pointer;" title="Buscar cuenta en el Puc">
					</div>

					<div style="float:left; margin-top:20px;">
						<div style="float:left; width:70px; margin-right:5px">Cuenta Final:</div>
						<div style="float:left; width:120px;"><input type="text" class="myField" id="cuenta_final" style="width:120px" onkeyup="validaCuentaPuc(event,this);"></div>
					</div>
					<div style="float:left; margin-top: 22px; margin-left: -20px; background-image: url(\'img/MyGrillaFondo.png\');" onclick="ventanaBuscarCuentaPuc(\'cuenta_final\')">
						<img src="img/buscar20.png" style="width:18px;height:18px;cursor:pointer;" title="Buscar cuenta en el Puc">
					</div>
				</div>

				<!-- NIVEL DE CUENTAS -->
				<div style="margin:25px 0; font-weight: bolder; font-size:12px; border-right:0px; border-left:none; text-align:center;" class="x-panel-header">Nivel de Cuentas</div>
				<div style="margin-bottom:15px; text-align:center;">
					<select type="text" id="nivel_cuentas_BC" style="width:120px">'.$optionNivel.'</select>
				</div>

				<!-- CUENTAS DE CIERRE -->
				<div style="margin:15px 0; font-weight: bolder; font-size:12px; border-right:0px; border-left:none; text-align:center;" class="x-panel-header">CUENTAS DE CIERRE</div>
				<div style="margin-bottom:15px; text-align:center;">
					Incluir cuentas de Cierre<br><br>
					<select type="text" id="incluir_cuentas_cierre_BC" style="width:120px">
						<option value="false">No</option>
						<option value="true">Si</option>
					</select>
				</div>
			</div>
		</div>

		<script>

		  	new Ext.form.DateField({
				format     : "Y-m-d",
				width      : 120,
				id         :"cmpFechaInicio",
				allowBlank : false,
				showToday  : false,
				applyTo    : "MyInformeFiltroFechaInicio",
				editable   : false,
				value      : "'.$fechaInicial.'"
		  	    // listeners  : { select: function() {   } }
		  	});

			new Ext.form.DateField({
				format     : "Y-m-d",
				width      : 120,
				allowBlank : false,
				showToday  : false,
				applyTo    : "MyInformeFiltroFechaFinal",
				editable   : false,
				value      : new Date(),
		  	    // listeners  : { select: function() {   } }
		  	});


			document.getElementById("cuenta_inicial").value=(typeof(localStorage.cuenta_inicialBC )!="undefined")? localStorage.cuenta_inicialBC  : "" ;
			document.getElementById("cuenta_final").value=(typeof(localStorage.cuenta_finalBC )!="undefined")? localStorage.cuenta_finalBC  : "" ;

			if (typeof(localStorage.sucursal_balance_comprobacion)!="undefined") {
				if (localStorage.sucursal_balance_comprobacion!="") {
					setTimeout(function(){document.getElementById("filtro_sucursal_sucursales_balance_comprobacion").value=localStorage.sucursal_balance_comprobacion;},100);
				}
			}

			if (typeof(localStorage.MyInformeFiltroFechaInicioBalanceComprobacion)!="undefined") {
				if (localStorage.MyInformeFiltroFechaInicioBalanceComprobacion!="") {
					document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicioBalanceComprobacion;
				}
			}

			if (typeof(localStorage.MyInformeFiltroFechaFinalBalanceComprobacion)!="undefined") {
				if (localStorage.MyInformeFiltroFechaFinalBalanceComprobacion!="") {
					document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinalBalanceComprobacion;
				}
			}

			// if (localStorage.tipo_fecha_informe=="corte") {
			// 	document.getElementById("divFechaInicio").style.display="none";
			// 	elementos[0].checked=true;
			// }
			// else if (localStorage.tipo_fecha_informe=="rango_fechas") {
			// 	document.getElementById("divFechaInicio").style.display="block";
			// 	elementos[1].checked=true;
			// }else{
			// 	document.getElementById("divFechaInicio").style.display="none";
			// 	elementos[0].checked=true;
			// }

			//CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS TERCEROS GUARDADOS
			for ( i = 0; i < arraytercerosBC.length; i++) {
				if (typeof(arraytercerosBC[i])!="undefined" && arraytercerosBC[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_cartera_tercero_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracion").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_cartera_tercero_"+i).innerHTML=tercerosConfiguradosBC[i];

				}
			}

			if (checkBoxSelectAllTercerosBC=="true") {
				document.getElementById("imgCheckTercerosBC").setAttribute("src","img/checkox_true.png");
				// document.getElementById("inputBuscarCentroCostos").disabled=true;
				document.getElementById("divDisableBC").style.display="block";
				document.getElementById("imgBuscarTerceroBC").style.display="none";
			}


			//FUNCION DEL CHECKBOX
			function cambiaChekc(){
				var elemento = document.getElementById("imgCheckTercerosBC").getAttribute("src");

				if (elemento=="img/checkox_false.png") {
					document.getElementById("imgCheckTercerosBC").setAttribute("src","img/checkox_true.png");
					// document.getElementById("inputBuscarCentroCostos").disabled=true;
					document.getElementById("divDisableBC").style.display="block";
					document.getElementById("imgBuscarTerceroBC").style.display="none";
					checkBoxSelectAllTercerosBC="true";
				}
				else{
					document.getElementById("imgCheckTercerosBC").setAttribute("src","img/checkox_false.png");
					// document.getElementById("inputBuscarCentroCostos").disabled=false;
					document.getElementById("divDisableBC").style.display="none";
					document.getElementById("imgBuscarTerceroBC").style.display="block";
					checkBoxSelectAllTercerosBC="false";
				}

			}

		</script>';
}

function ventanaConfiguracion_CIR($id_empresa,$link){

	$date = strtotime(date("Y-m-d"));
    $anio = date("Y", $date);
    $mes  = date("m", $date);
    $dia  = date("d",$date);

    $optionCIR = '';

    //CONSULTA IMPUESTOS
	$sqlCIR   = "SELECT id,impuesto FROM impuestos AS nombre WHERE id_empresa='$id_empresa' AND activo=1";
	$queryCIR = mysql_query($sqlCIR, $link);

    while ($rowCIR = mysql_fetch_assoc($queryCIR)) {
		$optionCIR .= '<div style="float:left; width:100%;" class="div_check_tipo_CIR">
							<input type="checkbox" value="I_'.$rowCIR['id'].'" style="float:left; width:30px;" class="check_impuestos_CIR">
							<div style="float:left; text-overflow:ellipsis; overflow:hidden; width:160px; white-space: nowrap;" title="'.$rowCIR['impuesto'].'">'.$rowCIR['impuesto'].'</div>
						</div>';
    }

    //CONSULTA RETENCIONES
    $sqlCIR   = "SELECT id,retencion,valor FROM retenciones AS nombre WHERE id_empresa='$id_empresa' AND activo=1";
	$queryCIR = mysql_query($sqlCIR, $link);

    while ($rowCIR = mysql_fetch_assoc($queryCIR)) {
    	$valor = $rowCIR['valor'] * 1;
		$optionCIR .= '<div style="float:left; width:100%;" class="div_check_tipo_CIR">
							<input type="checkbox" value="R_'.$rowCIR['id'].'" style="float:left; width:30px;" class="check_impuestos_CIR">
							<div style="float:left; text-overflow:ellipsis; overflow:hidden; width:160px; white-space: nowrap;" title="'.$rowCIR['retencion'].' '.$valor.'">'.$rowCIR['retencion'].' '.$valor.'</div>
						</div>';
    }

    //CONSULTA TIPOS DE DOCUMENTOS
    $arrayDocumento = array(
    					'VENTAS' => array(
    									"FV"=>"&nbsp;&nbsp;&nbsp;&nbsp;Facturas de Venta",
    									"RC"=>"&nbsp;&nbsp;&nbsp;&nbsp;Recibo de Caja",
    								),
    					'COMPRAS' => array(
    									"FC"=>"&nbsp;&nbsp;&nbsp;&nbsp;Facturas de Compra",
    									"CE"=>"&nbsp;&nbsp;&nbsp;&nbsp;Comprobantes de Egreso",
    								),
    					'CONTABILIDAD' => array(
    									"NCG"=>"&nbsp;&nbsp;Notas Contables",
    									"NDFC"=>"Devoluciones en Compra",
    									"NDFV"=>"Devoluciones en Venta",
    								),
    					'NOMINA' => array(
    									"LN"=>"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Liquidacion Nomina",
    									"LE"=>"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Liquidacion Empleado",
    								)
    				);

    $documentoCIR = "";
    foreach ($arrayDocumento as $tipo => $arrayTipo) {
    	foreach ($arrayTipo as $documento => $nombre) {

    		$documentoCIR .= '<div style="float:left; width:100%;" class="div_check_documentos_CIR">
								<input type="checkbox" value="'.$documento.'" style="float:left; width:30px;" class="check_documentos_CIR">
								<div style="float:left; text-overflow:ellipsis; overflow:hidden; width:160px; white-space: nowrap;">'.$documento.' '.$nombre.'</div>
							</div>';
    	}
    }

    //CALCULAR EL FINAL DEL MES
    $fechaInicial = date("Y-m-d",(mktime(0,0,0,$mes,1,$anio)-1));

	echo'<div style="border-top:1px solid #8DB2E3; width:100%; overflow:hidden;" id="informe_CIR">
			<!-- DIV MENU IZQUIERDO -->
			<div style="width:calc(100% - 250px - 3px); padding:0; float:left; height:400px; margin-left:9px;">

				<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRAR POR TERCERO(S)</div>

				<div style="float:left; width:100%; margin-bottom: 10px; font-weight: bold;">
					<input type="checkbox" style="float:left; width:30px;" id="check_agrupar_terceros_CIR">
					<div style="float:left; text-overflow:ellipsis; overflow:hidden; width:160px; white-space: nowrap;">Agrupar por terceros</div>
				</div>

				<!-- VENTANA BUSCAR TERCERO -->
				<div style="width:100%; height:calc(100% - 50px); background-color:#CDDBF0; overflow:hidden;">
					<div id="contenedor_formulario_configuracion" >
						<div id="contenedor_tabla_configuracion">
							<div class="headTablaBoletas">
								<div class="campo0">&nbsp;</div>
								<div class="campo1">Nit</div>
								<div class="campo1">Tercero</div>
								<div class="campo4" style="width:25px;"><img src="img/buscar20.png" onclick="ventanaBusquedaTercero_CIR();" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC"></div>
							</div>
							<div id="bodyTablaConfiguracion"></div>
						</div>
					</div>
				</div>
			</div>

			<!-- DIV MENU DERECHO -->
			<div style="float:right; width:230px; background-color: #eff3fa; height: 100%; border-left:1px solid #8DB2E3; overflow-y:auto;">

				<!-- FECHA DE INFORME -->
				<div style="margin-bottom:25px; font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none;border-top:none;" class="x-panel-header">Fechas del Informe</div>
				<div style="display:table; margin:auto;">
					<div style="overflow:hidden;" id="divFechaInicio">
						<div style="float:left; width:70px; margin-right:5px">Fecha Inicial:</div>
						<div style="float:left; width:120px;"><input type="text" id="fechaInicio_CIR"></div>
					</div>
					<div style="overflow:hidden; margin-top:20px;">
						<div style="float:left; width:70px; margin-right:5px">Fecha Final:</div>
						<div style="float:left; width:120px;"><input type="text" id="fechaFinal_CIR"></div>
					</div>
				</div>

				<!-- IMPUESTOS Y RETENCIONES -->
				<div style="margin:10px 0; font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none;" class="x-panel-header">Impuestos y retenciones</div>
				<div style="margin:10px 0 20px 0; overflow:hidden;">
					<div style="float:left; width:100%; margin-bottom: 15px;">
						<input type="checkbox" value="" style="float:left; width:30px;" id="check_todos_impuestos_CIR" onclick="check_impuestos_CIR(this.checked)" checked />
						<div style="float:left; text-overflow:ellipsis; overflow:hidden; width:160px; white-space: nowrap; font-weight:bold;">TODOS LOS IMPUESTOS Y RETENCIONES</div>
					</div>
					<div style="margin:auto; width:100%; height:80px; max-height:80px;" id="contenedor_check_impuestos_CIR">'.$optionCIR.'</div>
				</div>

				<!-- TIPOS DOCUMENTO -->
				<div style="margin:10px 0; font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none;" class="x-panel-header">TIPOS DOCUMENTO</div>
				<div style="margin:10px 0 20px 0; overflow:hidden;">
					<div style="float:left; width:100%; display:none;">
						<input type="checkbox" style="float:left; width:30px;" id="check_agrupar_documentos_CIR">
						<div style="float:left; text-overflow:ellipsis; overflow:hidden; width:160px; white-space: nowrap;">Agrupar por tipos de documentos</div>
					</div>

					<div style="float:left; width:100%; margin-bottom: 15px;">
						<input type="checkbox" value="" style="float:left; width:30px;" id="check_todos_documentos_CIR" onclick="check_documentos_CIR(this.checked)" checked />
						<div style="float:left; text-overflow:ellipsis; overflow:hidden; width:160px; white-space: nowrap; font-weight:bold;">TODOS LOS DOCUMENTOS</div>
					</div>
					<div style="margin:auto; width:100%; height:80px; max-height:80px;" id="contenedor_check_documentos_CIR">'.$documentoCIR.'</div>
				</div>
			</div>
		</div>

		<script>

			check_impuestos_CIR(document.getElementById("check_todos_impuestos_CIR").checked);
			check_documentos_CIR(document.getElementById("check_todos_documentos_CIR").checked);

		  	new Ext.form.DateField({
				format     : "Y-m-d",
				width      : 120,
				id         :"cmpFechaInicio",
				allowBlank : false,
				showToday  : false,
				applyTo    : "fechaInicio_CIR",
				editable   : false,
				value      : "'.$fechaInicial.'"
		  	    // listeners  : { select: function() {   } }
		  	});

			new Ext.form.DateField({
				format     : "Y-m-d",
				width      : 120,
				allowBlank : false,
				showToday  : false,
				applyTo    : "fechaFinal_CIR",
				editable   : false,
				value      : new Date(),
		  	    // listeners  : { select: function() {   } }
		  	});


			if (typeof(localStorage.sucursal_facturas)!="undefined") {
				if (localStorage.sucursal_facturas!="") {
					setTimeout(function(){ document.getElementById("filtro_sucursal_CIR").value=localStorage.sucursal_CIR; }, 100);
				}
			}

			if (typeof(localStorage.fechaInicio_CIR)!="undefined") {
				if (localStorage.fechaInicio_CIR!="") {
					document.getElementById("fechaInicio_CIR").value=localStorage.fechaInicio_CIR;
				}
			}

			if (typeof(localStorage.fechaFinal_CIR)!="undefined") {
				if (localStorage.fechaFinal_CIR!="") {
					document.getElementById("fechaFinal_CIR").value=localStorage.fechaFinal_CIR;
				}
			}

			//CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS TERCEROS GUARDADOS
			for ( i = 0; i < arrayTerceros_CIR.length; i++) {
				if (typeof(arrayTerceros_CIR[i])!="undefined" && arrayTerceros_CIR[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_cartera_tercero_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracion").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_cartera_tercero_"+i).innerHTML=tercerosConfigurados_CIR[i];
				}
			}

		</script>

		<style>
			.div_check_tipo_CIR:hover{ color:red; }
			.div_check_documentos_CIR:hover{ color:red; }
		</style>';
}

//========================== CUERPO DE LA VENTANA DE CONFIGURACION DEL BALANCE DE COMPROBACION =======================//
function ventanaLibroDiario($id_empresa,$link){

	$optionNivel = '';
	$ultimoNivel = 0;

	$sqlNivel   = "SELECT digitos,nombre FROM puc_configuracion WHERE activo = 1 AND id_empresa = '$id_empresa' ORDER BY digitos ASC";
	$queryNivel = mysql_query($sqlNivel,$link);

	while($rowNivel = mysql_fetch_assoc($queryNivel)){
		$ultimoNivel  = $rowNivel['digitos'];
		$optionNivel .= '<option value="'.$rowNivel['digitos'].'">'.$rowNivel['nombre'].'('.$rowNivel['digitos'].')</option>';
	}


	$date = strtotime(date("Y-m-d"));
  $anio = date("Y", $date);
  $mes  = date("m", $date);
  $dia  = date("d",$date);

  //CALCULAR EL FINAL DEL MES
  $fechaInicial = date("Y-m-d",(mktime(0,0,0,$mes,1,$anio)-1));

	echo '<div style="border-top:1px solid #8DB2E3; width:100%; overflow:hidden;" id="informeBalancePrueba">
					<div style="float:left; width:250px; background-color: #eff3fa; height: 100%; border-left:1px solid #8DB2E3; overflow-y:auto;">
						<div style="margin-bottom:25px; font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none;border-top:none;" class="x-panel-header">Fechas del Informe</div>
						<div style="display:table;margin:auto;">
							<div style="overflow:hidden;" id="divFechaInicio">
								<div style="float:left; width:70px; margin-right:5px">Fecha Inicial:</div>
								<div style="float:left; width:120px;"><input type="text" id="MyInformeFiltroFechaInicio"></div>
							</div>
							<div style="overflow:hidden; margin-top:20px;">
								<div style="float:left; width:70px; margin-right:5px">Fecha Final:</div>
								<div style="float:left; width:120px;"><input type="text" id="MyInformeFiltroFechaFinal"></div>
							</div>
						</div>
						<!-- RANGO DE LAS CUENTAS -->
						<div style="margin:25px 0; font-weight: bolder; font-size:12px; border-right:0px; border-left:none; text-align:center;" class="x-panel-header">Rango de Cuentas</div>
						<div style="margin:0 7px; float:left;">
							<div style="float:left;">
								<div style="float:left; width:70px; margin-right:5px">Cuenta Inicial:</div>
								<div style="float:left; width:120px;"><input type="text" class="myField" id="cuenta_inicial" style="width:120px" onkeyup="validaCuentaPucLibroDiario(event,this);"></div>
							</div>
							<div style="float:left; margin-top: 2px; margin-left:-20px; background-image: url(\'img/MyGrillaFondo.png\');" onclick="ventanaBuscarCuentaPucLibroDiario(\'cuenta_inicial\')">
								<img src="img/buscar20.png" style="width:18px;height:18px;cursor:pointer;" title="Buscar cuenta en el Puc">
							</div>
							<div style="float:left; margin-top:20px;">
								<div style="float:left; width:70px; margin-right:5px">Cuenta Final:</div>
								<div style="float:left; width:120px;"><input type="text" class="myField" id="cuenta_final" style="width:120px" onkeyup="validaCuentaPucLibroDiario(event,this);"></div>
							</div>
							<div style="float:left; margin-top: 22px; margin-left: -20px; background-image: url(\'img/MyGrillaFondo.png\');" onclick="ventanaBuscarCuentaPucLibroDiario(\'cuenta_final\')">
								<img src="img/buscar20.png" style="width:18px;height:18px;cursor:pointer;" title="Buscar cuenta en el Puc">
							</div>
						</div>
					</div>
					<div style="float:left; width:234px; background-color: #eff3fa; height: 100%; border-left:1px solid #8DB2E3; overflow-y:auto;">
						<div style="margin-bottom:25px; font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none;border-top:none;" class="x-panel-header">Clase de la Cuenta</div>
						<div style="display:table; margin:auto;">
							<div style="overflow:hidden;" id="divFechaInicio">
								<div style="float:left; width:100%;text-align:center;">
									<select type="text" id="clase_cuenta" style="width:80%;">
										<option value="global" >TODAS</option>
										<option value="1" >1 - ACTIVO</option>
										<option value="2" >2 - PASIVO</option>
										<option value="3" >3 - PATRIMONIO</option>
										<option value="4" >4 - INGRESOS</option>
										<option value="5" >5 - GASTOS</option>
										<option value="6" >6 - COSTO DE VENTA</option>
										<option value="7" >7 - COSTO DE PRODUCCION O DE OPERACION</option>
										<option value="8" >8 - CUENTAS DE ORDEN DEUDORAS</option>
										<option value="9" >9 - CUENTAS DE ORDEN ACREEDORAS</option>
									</select>
								</div>
							</div>
						</div>
						<div style="font-weight: bolder;font-size:12px ;margin-bottom:10px;margin-top:20px;text-align:center;border-left:none;border-right:none;" class="x-panel-header">Nivel de las cuentas</div>
						<div style="display:table; margin:auto;">
							<select type="text" id="nivel_cuentas_libro_diario" style="width:120px">
								'.$optionNivel.'
							</select>
						</div>
					</div>
				</div>
				<script>
			  	new Ext.form.DateField({
						format     : "Y-m-d",
						width      : 120,
						id         :"cmpFechaInicio",
						allowBlank : false,
						showToday  : false,
						applyTo    : "MyInformeFiltroFechaInicio",
						editable   : false,
						value      : "'.$fechaInicial.'"
			  	});

					new Ext.form.DateField({
						format     : "Y-m-d",
						width      : 120,
						allowBlank : false,
						showToday  : false,
						applyTo    : "MyInformeFiltroFechaFinal",
						editable   : false,
						value      : new Date()
				  });

					if(typeof(localStorage.sucursal_libro_diario) != "undefined"){
						if(localStorage.sucursal_libro_diario != "" && document.getElementById("filtro_sucursal_libro_diario")){
							setTimeout(function(){document.getElementById("filtro_sucursal_libro_diario").value = localStorage.sucursal_libro_diario;},100);
						}
					}

					if(typeof(localStorage.MyInformeFiltroFechaInicioLibroDiario) != "undefined"){
						if(localStorage.MyInformeFiltroFechaInicioLibroDiario != ""){
							document.getElementById("MyInformeFiltroFechaInicio").value = localStorage.MyInformeFiltroFechaInicioLibroDiario;
						}
					}

					if(typeof(localStorage.MyInformeFiltroFechaFinalLibroDiario) != "undefined"){
						if (localStorage.MyInformeFiltroFechaFinalLibroDiario != ""){
							document.getElementById("MyInformeFiltroFechaFinal").value = localStorage.MyInformeFiltroFechaFinalLibroDiario;
						}
					}

					if(typeof(localStorage.clase_cuenta_libro_diario) != "undefined"){
						if(localStorage.clase_cuenta_libro_diario != ""){
							document.getElementById("clase_cuenta").value = localStorage.clase_cuenta_libro_diario;
						}
					}

					if(typeof(localStorage.nivel_cuentas_libro_diario) != "undefined"){
						if(localStorage.nivel_cuentas_libro_diario != ""){
							document.getElementById("nivel_cuentas_libro_diario").value = localStorage.nivel_cuentas_libro_diario;
						}
					}

					if(typeof(localStorage.cuenta_inicial_libro_diario) != "undefined"){
						if(localStorage.cuenta_inicial_libro_diario != ""){
							document.getElementById("cuenta_inicial").value = localStorage.cuenta_inicial_libro_diario;
						}
					}

					if(typeof(localStorage.cuenta_final_libro_diario) != "undefined"){
						if(localStorage.cuenta_final_libro_diario != ""){
							document.getElementById("cuenta_final").value = localStorage.cuenta_final_libro_diario;
						}
					}
				</script>';
}

function cargarEmpleadosGuardados($arrayEmpleadosJSON,$id_empresa,$mysql){
	$arrayEmpleadosJSON = json_decode($arrayEmpleadosJSON);

	if(!empty($arrayEmpleadosJSON)){
		foreach($arrayEmpleadosJSON as $indice => $id_empleado){
			$empleados .= ($empleados == "")? "id = '$id_empleado'" : " OR id = '$id_empleado'";
		}
		$whereEmpleados .= " AND ($empleados)";

		$sql = "SELECT id,documento,nombre FROM empleados WHERE activo = 1 AND id_empresa = $id_empresa $whereEmpleados";
		$query = $mysql->query($sql,$mysql->link);

		$cont = 1;
		while($row = $mysql->fetch_array($query)){
			$grillaEmpleados .=  "<div class='row' id='row_empleado_$row[id]'>
															<div class='cell' data-col='1'>$cont</div>
															<div class='cell' data-col='2'>$row[documento]</div>
															<div class='cell' data-col='3' title='$row[nombre]'>$row[nombre]</div>
															<div class='cell' data-col='1' data-icon='delete' onclick='eliminaEmpleado($row[id])' title='Eliminar Empleado'></div>
														</div>
														<script>
															arrayEmpleadosDA[$row[id]] = $row[id];
														</script>";
		  $cont++;
		}

		$grillaEmpleados .= "<script>contEmpleados = $cont;</script>";

		echo $grillaEmpleados;
	}
}
?>
