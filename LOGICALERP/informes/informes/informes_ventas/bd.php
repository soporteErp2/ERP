<?php
include('../../../../configuracion/conectar.php');
include('../../../../configuracion/define_variables.php');

$id_empresa = $_SESSION['EMPRESA'];

switch ($opc) {
	case 'busquedaTerceroPaginacion':
		busquedaTerceroPaginacion($opcGrillaContable,$pagina,$limite,$limit,$rows_registros,$paginas,$id_tercero,$tercero,$nit,$whereSum,$tabla,$estado,$imprimeVar,$filtro,$link,$id_empresa);
		break;

	case 'buscarCentroCostos':
		buscarCentroCostos($codigo,$id_empresa,$link);
		break;

	case 'cuerpoVentanaConfiguracionCotizacionesVenta':
		cuerpoVentanaConfiguracionCotizacionesVenta();
		break;

	case 'cuerpoVentanaConfiguracionResmisionesVenta':
		cuerpoVentanaConfiguracionResmisionesVenta();
		break;

	case 'ventana_configuracion_items':
		ventana_configuracion_items();
		break;

	case 'ventana_configuracion_itemsRemisionados':
		ventana_configuracion_itemsRemisionados();
		break;

	case 'cuerpoVentanaConfiguracionFacturas':
		cuerpoVentanaConfiguracionFacturas();
		break;

	case 'ventanaConfiguracion_FVIR':
		ventanaConfiguracion_FVIR();
		break;

	case 'cuerpoVentanaConfiguracionFacturasCuentas':
		cuerpoVentanaConfiguracionFacturasCuentas();
		break;

	case 'cuerpoVentanaConfiguracionDevolucionVentas':
		cuerpoVentanaConfiguracionDevolucionVentas();
		break;

	case 'cuerpoVentanaConfiguracionPedidosVenta':
		cuerpoVentanaConfiguracionPedidosVenta();
		break;

	case 'cuerpoVentanaConfiguracionReciboCaja':
		cuerpoVentanaConfiguracionReciboCaja();
		break;
}

//======================= FUNCION PARA PAGINAR LA BUSQUEDA DE LA VENTANA ========================================//
function busquedaTerceroPaginacion($opcGrillaContable,$pagina,$limite,$limit,$rows_registros,$paginas,$id_tercero,$tercero,$nit,$whereSum,$tabla,$estado,$imprimeVar,$filtro,$link,$id_empresa){


	//SI LA VARIABLE FILTRO NO ESTA VACIA, RECONTAMOS EL LIMITE DE LOS REGISTROS
	if ($filtro!='') {
		$sql   = "SELECT COUNT(id) as cont $whereSum FROM $tabla WHERE activo=1 $estado $filtro AND id_empresa='$id_empresa'";
		$query = mysql_query($sql,$link);

		$rows_registros = mysql_result($query,0,'cont');
		$paginas        = ceil( $rows_registros/$limite );

		//CREAR ARRAY CON LOS LIMITES DE LAS CONSULTAS
		$limit1     = 0;
		$limit2     = $limite;
		$acumScript = '';
		for ($i=1; $i <= $paginas; $i++) {
			$acumScript .= 'arrayLimitGrilla'.$opcGrillaContable.'['.$i.']="'.$limit1.','.$limit2.'";';
			$limit1      = $limit2+1;
			$limit2      = $limit2+$limite;
		}
	}
	//SI NO SE HACE LA BUSQUEDA CON FILTRO SINO DE FORMA NORMAL
	else{
		$sql   = "SELECT COUNT(id) as cont $whereSum  FROM $tabla WHERE activo=1 $estado AND id_empresa='$id_empresa'";
		$query = mysql_query($sql,$link);

		$rows_registros = mysql_result($query,0,'cont');
		$paginas        = ceil( $rows_registros/$limite );

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

		// $divSaldoPendiente=($tabla!='terceros' )? '<div class="campo3" id="saldo_'.$contFilaCuenta.'">'.$rowCuentas['saldo'].'</div>' : '' ;

		$filaInsertBoleta .= '<div class="filaBoleta" id="fila_boleta_'.$opcGrillaContable.'_'.$contFilaCuenta.'">
								<div class="campo0 campoInforme0">'.$contFilaCuenta.'</div>
								<div class="campo1 campoInforme1" id="nit_'.$rowCuentas[$id_tercero].'">'.$rowCuentas['nit'].'</div>
								<div class="campo2 campoInforme2" style="border-left:0px;" id="tercero_'.$rowCuentas[$id_tercero].'" title="'.$rowCuentas[$tercero].'">'.$rowCuentas[$tercero].'</div>
								'.$divSaldoPendiente.'
								<div class="campo4" id="valor_anticipo_'.$opcGrillaContable.'_'.$contFilaCuenta.'">
									<input type="checkbox" id="checkbox_'.$rowCuentas[$id_tercero].'" onchange="checkGrilla(this,\''.$rowCuentas[$id_tercero].'\',\''.$tabla.'\')" value="'.$rowCuentas[$id_tercero].'" >
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
							seleccionaCheck();
						</script>';

		echo $filaInsertBoleta;

}

//========================== CUERPO DE LA VENTANA DE CONFIGURACION DEL BALANCE DE COMPROBACION =======================//
function cuerpoVentanaConfiguracionCotizacionesVenta(){
	$date = strtotime(date("Y-m-d"));
    $anio = date("Y", $date);
    $mes  = date("m", $date);
    $dia  = date("d",$date);

    //CALCULAR EL FINAL DEL MES
    $fechaInicial = date("Y-m-d",(mktime(0,0,0,$mes,1,$anio)-1));

	echo'<div style="border-top:1px solid #8DB2E3; width:100%; overflow:hidden;" id="informeBalancePrueba">
			<!-- DIV MENU IZQUIERDO -->
			<div style="width: calc(100% - 215px - 3px); padding:0; float:left; height:270px; margin-left:5px;float:left;">

				<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRAR POR CLIENTE(S)</div>

				<!-- VENTANA BUSCAR TERCERO -->
				<div style="display:none;background-color: #F3F3F3;border-right: 1px solid #D4D4D4;border-top: 1px solid #D4D4D4;float: left;height: 26px;width: 35px;border-top-right-radius: 5px;padding: 7 0 0 7;" >
					<img src="img/buscar20.png" onclick="ventanaBusquedaTercero();" style="cursor: pointer;width:21px;height:21px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC5">
				</div>

				<div style="width:100%;height:180px;background-color: #CDDBF0;overflow:hidden;">
					<div id="contenedor_formulario_configuracion" >
						<div id="contenedor_tabla_configuracion" style="height:178px;">
							<div class="headTablaBoletas">
								<div class="campo0">&nbsp;</div>
								<div class="campo1">Nit</div>
								<div class="campo2" style="width: 150px;">Cliente</div>
								<div class="campo4" style="width:25px;"><img src="img/buscar20.png" onclick="ventanaBusquedaTerceroFV();" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC"></div>
							</div>
							<div id="bodyTablaConfiguracion" style="height:140px;">

							</div>

						</div>
					</div>
				</div>

				<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRAR POR VENDEDORES(S)</div>

				<!-- VENTANA BUSCAR TERCERO -->
				<div style="display:none;background-color: #F3F3F3;border-right: 1px solid #D4D4D4;border-top: 1px solid #D4D4D4;float: left;height: 26px;width: 35px;border-top-right-radius: 5px;padding: 7 0 0 7;" >
					<img src="img/buscar20.png" onclick="ventanaBusquedaTercero();" style="cursor: pointer;width:21px;height:21px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC5">
				</div>

				<div style="width:100%;height:180px;background-color: #CDDBF0;overflow:hidden;">
					<div id="contenedor_formulario_configuracion" >
						<div id="contenedor_tabla_configuracion" style="height:178px;">
							<div class="headTablaBoletas">
								<div class="campo0">&nbsp;</div>
								<div class="campo1">Identificacion</div>
								<div class="campo2" style="width: 150px;">Vendedor</div>
								<div class="campo4" style="width:25px;"><img src="img/buscar20.png" onclick="ventanaBusquedaTerceroFV(\'vendedores\');" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC"></div>
							</div>
							<div id="bodyTablaConfiguracionVendedores" style="height:140px;">

							</div>

						</div>
					</div>
				</div>

			</div>

			<!-- DIV MENU DERECHO -->
			<div style="float:right; width:210px; background-color: #eff3fa; height: 100%; border-left:1px solid #8DB2E3;">
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


			if (typeof(localStorage.sucursal_cotizaciones_venta)!="undefined") {
				if (localStorage.sucursal_cotizaciones_venta!="") {
					setTimeout(function(){ document.getElementById("filtro_sucursal_cotizaciones_venta").value=localStorage.sucursal_cotizaciones_venta; },100);
				}
			}

			if (typeof(localStorage.MyInformeFiltroFechaInicioCotizacionesVenta)!="undefined") {
				if (localStorage.MyInformeFiltroFechaInicioCotizacionesVenta!="") {
					document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicioCotizacionesVenta;
				}
			}

			if (typeof(localStorage.MyInformeFiltroFechaFinalCotizacionesVenta)!="undefined") {
				if (localStorage.MyInformeFiltroFechaFinalCotizacionesVenta!="") {
					document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinalCotizacionesVenta;
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
			for ( i = 0; i < arraytercerosCV.length; i++) {
				if (typeof(arraytercerosCV[i])!="undefined" && arraytercerosCV[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_cartera_tercero_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracion").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_cartera_tercero_"+i).innerHTML=tercerosConfiguradosCV[i];

				}
			}
			//CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS VENDEDORES GUARDADOS
			for ( i = 0; i < arrayvendedoresCV.length; i++) {
				if (typeof(arrayvendedoresCV[i])!="undefined" && arrayvendedoresCV[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_empleado_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracionVendedores").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_empleado_"+i).innerHTML=vendedoresConfiguradosCV[i];

				}
			}


		</script>';
}

//========================== CUERPO DE LA VENTANA DE CONFIGURACION DEL BALANCE DE COMPROBACION =======================//
function cuerpoVentanaConfiguracionPedidosVenta(){
	$date = strtotime(date("Y-m-d"));
    $anio = date("Y", $date);
    $mes  = date("m", $date);
    $dia  = date("d",$date);

    //CALCULAR EL FINAL DEL MES
    $fechaInicial = date("Y-m-d",(mktime(0,0,0,$mes,1,$anio)-1));

	echo'<div style="border-top:1px solid #8DB2E3; width:100%; overflow:hidden;" id="informeBalancePrueba">
			<!-- DIV MENU IZQUIERDO -->
			<div style="width: calc(100% - 215px - 3px); padding:0; float:left; height:270px; margin-left:5px;float:left;">

				<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRAR POR CLIENTE(S)</div>

				<!-- VENTANA BUSCAR TERCERO -->
				<div style="display:none;background-color: #F3F3F3;border-right: 1px solid #D4D4D4;border-top: 1px solid #D4D4D4;float: left;height: 26px;width: 35px;border-top-right-radius: 5px;padding: 7 0 0 7;" >
					<img src="img/buscar20.png" onclick="ventanaBusquedaTercero();" style="cursor: pointer;width:21px;height:21px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC5">
				</div>

				<div style="width:100%;height:180px;background-color: #CDDBF0;overflow:hidden;">
					<div id="contenedor_formulario_configuracion" >
						<div id="contenedor_tabla_configuracion" style="height:178px;">
							<div class="headTablaBoletas">
								<div class="campo0">&nbsp;</div>
								<div class="campo1">Nit</div>
								<div class="campo2" style="width: 150px;">Cliente</div>
								<div class="campo4" style="width:25px;"><img src="img/buscar20.png" onclick="ventanaBusquedaTerceroFV();" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC"></div>
							</div>
							<div id="bodyTablaConfiguracion" style="height:140px;">

							</div>

						</div>
					</div>
				</div>

				<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRAR POR VENDEDORES(S)</div>

				<!-- VENTANA BUSCAR TERCERO -->
				<div style="display:none;background-color: #F3F3F3;border-right: 1px solid #D4D4D4;border-top: 1px solid #D4D4D4;float: left;height: 26px;width: 35px;border-top-right-radius: 5px;padding: 7 0 0 7;" >
					<img src="img/buscar20.png" onclick="ventanaBusquedaTercero();" style="cursor: pointer;width:21px;height:21px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC5">
				</div>

				<div style="width:100%;height:180px;background-color: #CDDBF0;overflow:hidden;">
					<div id="contenedor_formulario_configuracion" >
						<div id="contenedor_tabla_configuracion" style="height:178px;">
							<div class="headTablaBoletas">
								<div class="campo0">&nbsp;</div>
								<div class="campo1">Identificacion</div>
								<div class="campo2" style="width: 150px;">Vendedor</div>
								<div class="campo4" style="width:25px;"><img src="img/buscar20.png" onclick="ventanaBusquedaTerceroFV(\'vendedores\');" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC"></div>
							</div>
							<div id="bodyTablaConfiguracionVendedores" style="height:140px;">

							</div>

						</div>
					</div>
				</div>

			</div>

			<!-- DIV MENU DERECHO -->
			<div style="float:right; width:210px; background-color: #eff3fa; height: 100%; border-left:1px solid #8DB2E3;">
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


			if (typeof(localStorage.sucursal_pedidos_venta)!="undefined") {
				if (localStorage.sucursal_pedidos_venta!="") {
					setTimeout(function(){ document.getElementById("filtro_sucursal_pedidos_venta").value=localStorage.sucursal_pedidos_venta; },100);
				}
			}

			if (typeof(localStorage.MyInformeFiltroFechaInicioPedidosVenta)!="undefined") {
				if (localStorage.MyInformeFiltroFechaInicioPedidosVenta!="") {
					document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicioPedidosVenta;
				}
			}

			if (typeof(localStorage.MyInformeFiltroFechaFinalPedidosVenta)!="undefined") {
				if (localStorage.MyInformeFiltroFechaFinalPedidosVenta!="") {
					document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinalPedidosVenta;
				}
			}


			//CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS TERCEROS GUARDADOS
			for ( i = 0; i < arraytercerosPV.length; i++) {
				if (typeof(arraytercerosPV[i])!="undefined" && arraytercerosPV[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_cartera_tercero_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracion").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_cartera_tercero_"+i).innerHTML=tercerosConfiguradosPV[i];

				}
			}
			//CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS VENDEDORES GUARDADOS
			for ( i = 0; i < arrayvendedoresPV.length; i++) {
				if (typeof(arrayvendedoresPV[i])!="undefined" && arrayvendedoresPV[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_empleado_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracionVendedores").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_empleado_"+i).innerHTML=vendedoresConfiguradosPV[i];

				}
			}


		</script>';
}

//==========================// VENTANA CONFIGURACION REMISION //=======================//
//*************************************************************************************//
function cuerpoVentanaConfiguracionResmisionesVenta(){

	$date = strtotime(date("Y-m-d"));
    $anio = date("Y", $date);
    $mes  = date("m", $date);
    $dia  = date("d",$date);

    //CALCULAR EL FINAL DEL MES
    $fechaInicial = date("Y-m-d",(mktime(0,0,0,$mes,1,$anio)-1));

	echo'<div style="border-top:1px solid #8DB2E3; width:100%; overflow:hidden;" id="informeBalancePrueba">
			<!-- DIV CON LOS TERCEROS A AGREGAR -->
			<div style="width: calc(100% - 215px - 3px); padding:0; float:left; height:270px; margin-left:5px;">
				<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRAR POR CLIENTE(S)</div>

				<!-- VENTANA BUSCAR TERCERO -->

				<div style="width:100%;height:180px;background-color: #CDDBF0;overflow:hidden;">
					<div id="contenedor_formulario_configuracion" >
						<div id="contenedor_tabla_configuracion" style="height:178px;">
							<div class="headTablaBoletas">
								<div class="campo0">&nbsp;</div>
								<div class="campo1">Nit</div>
								<div class="campo2" style="width: 150px;">Tercero</div>
								<div class="campo4" style="width:25px;"><img src="img/buscar20.png" onclick="ventanaBusquedaTerceroRV();" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC"></div>
							</div>
							<div id="bodyTablaConfiguracion" style="height:140px;">

							</div>

						</div>
					</div>
				</div>

				<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRAR POR VENDEDORES(S)</div>

				<!-- VENTANA BUSCAR VENDEDORES -->

				<div style="width:100%;height:180px;background-color: #CDDBF0;overflow:hidden;">
					<div id="contenedor_formulario_configuracion" >
						<div id="contenedor_tabla_configuracion" style="height:178px;">
							<div class="headTablaBoletas">
								<div class="campo0">&nbsp;</div>
								<div class="campo1">Identificacion</div>
								<div class="campo2" style="width: 150px;">Vendedor</div>
								<div class="campo4" style="width:25px;"><img src="img/buscar20.png" onclick="ventanaBusquedaTerceroRV(\'vendedores\');" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC"></div>
							</div>
							<div id="bodyTablaConfiguracionVendedores" style="height:140px;">

							</div>

						</div>
					</div>
				</div>
			</div>

			<!-- DIV MENU DERECHO -->
			<div style="float:right; width:210px; background-color: #eff3fa; height: 100%; border-left:1px solid #8DB2E3;">
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

				<!-- ESTADO DE LA REMISION -->
				<div style="margin-bottom:15px;margin-top:20px; font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none;" class="x-panel-header">Estado de la Remision</div>
				<div style="margin-left:10px; overflow:hidden;">

					<div style="margin-bottom:6px; overflow:hidden;">
						<input type="radio" name="estado_remision" value="todas" style="float:left; width:30px">
						<div style="float:left;">Todas</div>
					</div>

					<div style="margin-bottom:6px; overflow:hidden;">
						<input type="radio" name="estado_remision" value="pendientes" style="float:left; width:30px">
						<div style="float:left;">Pendientes por Facturar</div>
					</div>

					<div style="margin-bottom:15px; overflow:hidden;">
						<input type="radio" name="estado_remision" value="facturadas" style="float:left; width:30px">
						<div style="float:left;">Facturadas</div>
					</div>
					<div style="margin-left:8px; overflow:hidden;">
						<input type="checkbox" id="mostrar_aritulos" onclick="verificaMostrarArticulos(this);"> &nbsp;&nbsp;Discriminar Items
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


			if (typeof(localStorage.sucursal_remisiones_venta)!="undefined") {
				if (localStorage.sucursal_remisiones_venta!="") {
					setTimeout(function(){ document.getElementById("filtro_sucursal_remisiones_venta").value=localStorage.sucursal_remisiones_venta; },100);
				}
			}

			if (typeof(localStorage.MyInformeFiltroFechaInicioRemisionesVenta)!="undefined") {
				if (localStorage.MyInformeFiltroFechaInicioRemisionesVenta!="") {
					document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicioRemisionesVenta;
				}
			}

			if (typeof(localStorage.MyInformeFiltroFechaFinalRemisionesVenta)!="undefined") {
				if (localStorage.MyInformeFiltroFechaFinalRemisionesVenta!="") {
					document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinalRemisionesVenta;
				}
			}

			var elementos = document.getElementsByName("estado_remision");

			if (typeof(localStorage.estado_remision)!="undefined") {
				if (localStorage.estado_remision!="") {
					for(var i=0; i<elementos.length; i++) {
						if (elementos[i].value==localStorage.estado_remision) {elementos[i].checked=true;}
					}
				}else{
					elementos[0].checked=true;
				}
			}
			else{
				elementos[0].checked=true;
			}

			if (checkBoxMostrarArticulos=="true") {
				document.getElementById("mostrar_aritulos").checked=true;
			}


			//CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS TERCEROS GUARDADOS
			for ( i = 0; i < arraytercerosRV.length; i++) {
				if (typeof(arraytercerosRV[i])!="undefined" && arraytercerosRV[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_cartera_tercero_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracion").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_cartera_tercero_"+i).innerHTML=tercerosConfiguradosRV[i];
				}
			}

			//CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS VENDEDORES GUARDADOS
			for ( i = 0; i < arrayvendedoresRV.length; i++) {
				if (typeof(arrayvendedoresRV[i])!="undefined" && arrayvendedoresRV[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_empleado_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracionVendedores").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_empleado_"+i).innerHTML=vendedoresConfiguradosRV[i];

				}
			}


			function verificaMostrarArticulos(check){
				if(check.checked==true){
					checkBoxMostrarArticulos="true";
				}else{
					checkBoxMostrarArticulos="";
				}
			}

		</script>';
}

//==========================// VENTANA CONFIGURACION ITEMS //==========================//
//*************************************************************************************//
function ventana_configuracion_items(){

	$date = strtotime(date("Y-m-d"));
    $anio = date("Y", $date);
    $mes  = date("m", $date);
    $dia  = date("d",$date);

    //CALCULAR EL FINAL DEL MES
    $fechaInicial = date("Y-m-d",(mktime(0,0,0,$mes,1,$anio)-1));

	echo'<div style="border-top:1px solid #8DB2E3; width:100%; overflow:hidden;" id="informeBalancePrueba">
			<!-- DIV IZQUIERDO -->
			<div style="width: calc(100% - 211px); padding:0; float:left; height:270px;">

				<div style="overflow:visible; border-bottom:1px solid #99BBE8; float:left; width:100%; height:24px;">
					<div style="font-weight:bolder; font-size:10px; text-align:center; margin:2px 1px 0 0; padding:3px 5px; display:block; float:left; border-bottom: none;" class="x-panel-header" onClick="display_filter_items(\'filter_terceros_items\');" id="tab_filter_terceros_items">CLIENTE</div>
					<div style="font-weight:bolder; font-size:10px; text-align:center; margin:2px 1px 0 1px; padding:3px 5px; display:block; float:left; border-bottom: none;" class="x-panel-header" onClick="display_filter_items(\'filter_vendedores_items\');" id="tab_filter_vendedores_items">VENDEDOR</div>
					<div style="font-weight:bolder; font-size:10px; text-align:center; margin:2px 1px 0 1px; padding:3px 5px; display:block; float:left; border-bottom: none;" class="x-panel-header" onClick="display_filter_items(\'filter_ccos_items\');" id="tab_filter_ccos_items">CENTRO DE COSTO</div>
					<div style="font-weight:bolder; font-size:10px; text-align:center; margin:2px 1px 0 1px; padding:3px 5px; display:block; float:left; border-bottom: none;" class="x-panel-header" onClick="display_filter_items(\'filter_categorias_items\');" id="tab_filter_categorias_items">CATEGORIAS</div>
				</div>

				<!-- VENTANA FILTRO TERCERO -->

				<div style="width:100%; height:432px; background-color:#CDDBF0; overflow:hidden; display:none;" id="filter_terceros_items">
					<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRO POR CLIENTES</div>

					<!-- OPCION TODOS LOS CLIENTES -->
					<div style="width:100%; height:30px; margin-left:5px;">
						<span style="cursor:pointer;padding-left: 10px;font-weight:bolder;float:left;margin-bottom: 7px;" onclick="cambiaChekClientesItems()"><img src="img/checkbox_false.png" id="imgCheckClientesItems">Todos los Clientes</span><br>
					</div>

					<div id="contenedor_formulario_configuracion" style="width:94%; margin:3%; position:relative;">
						<div id="contenedor_tabla_configuracion" style="height:178px; position:absolute;">
							<div class="headTablaBoletas">
								<div class="campo0">&nbsp;</div>
								<div class="campo1">Nit</div>
								<div class="campo2" style="width:150px;">Tercero</div>
								<div class="campo4" style="width:25px;"><img src="img/buscar20.png" onclick="ventanaBusquedaTercero_items();" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar Clientes" id="imgBuscarTerceroBC"></div>
							</div>
							<div id="bodyTablaConfiguracion" style="height:140px;"></div>
						</div>
						<div style="width:481px; height:180px; position:absolute; display:none; background-color:rgba(214, 211, 211, 0.4);" id="divDisableClientesItems"></div>
					</div>
				</div>

				<!-- VENTANA FILTRO VENDEDORES -->

				<div style="width:100%; height:432px; background-color: #CDDBF0; overflow:hidden; display:none;" id="filter_vendedores_items">
					<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRO POR VENDEDORES</div>

					<!-- OPCION TODOS LOS VENDEDORES -->
					<div style="width:100%; height:30px; margin-left:5px;">
						<span style="cursor:pointer;padding-left: 10px;font-weight:bolder;float:left;margin-bottom: 7px;" onclick="cambiaChekVendedoresItems()"><img src="img/checkbox_false.png" id="imgCheckVendedoresItems">Todas los Vendedores</span><br>
					</div>

					<div id="contenedor_formulario_configuracion" style="width:94%; margin:3%; position:relative;">
						<div id="contenedor_tabla_configuracion" style="height:178px; position:absolute;">
							<div class="headTablaBoletas">
								<div class="campo0">&nbsp;</div>
								<div class="campo1">Identificacion</div>
								<div class="campo2" style="width: 150px;">Vendedor</div>
								<div class="campo4" style="width:25px;"><img src="img/buscar20.png" onclick="ventanaBusquedaTercero_items(\'vendedores\');" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar Vendedores" id="imgBuscarTerceroBC"></div>
							</div>
							<div id="bodyTablaConfiguracionVendedores" style="height:140px;"></div>
						</div>
						<div style="width:481px; height:180px; position:absolute; display:none; background-color:rgba(214, 211, 211, 0.4);" id="divDisableVendedoresItems"></div>
					</div>
				</div>

				<!-- VENTANA FILTRO CENTRO DE COSTO -->

				<div style="width:100%; height:432px; background-color:#CDDBF0; overflow:hidden; display:none;" id="filter_ccos_items">
					<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRO POR CENTROS DE COSTO</div>

					<!-- OPCION TODOS LOS CENTROS DE COSTO -->
					<div style="width:100%; height:30px; margin-left:5px;">
						<span style="cursor:pointer;padding-left: 10px;font-weight:bolder;float:left;margin-bottom: 7px;" onclick="cambiaChekCcosItems()"><img src="img/checkbox_false.png" id="imgCheckCcosItems">Todos los Centros de Costo</span><br>
					</div>

					<div id="contenedor_formulario_configuracion" style="width:94%; margin:3%; position:relative;">
						<div id="contenedor_tabla_configuracion" style="height:178px; position:absolute;">
							<div class="headTablaBoletas">
								<div class="campo0"><img src="img/buscar20.png" onclick="ventanaBusquedaCentroCostosItems();" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar Centros de Costo" id="imgBuscarTerceroBC"></div>
								<div class="campo1" style="width:120px;">Codigo</div>
								<div class="campo2" style="width:180px;">Nombre</div>
							</div>
							<div id="bodyTablaConfiguracionCentroCostos" style="height:140px;"></div>
						</div>
						<div style="width:481px; height:180px; position:absolute; display:none; background-color:rgba(214, 211, 211, 0.4);" id="divDisableCcosItems"></div>
					</div>
				</div>

				<!-- VENTANA FILTRO CATEGORIAS -->

				<div style="width:100%; height:432px; background-color:#CDDBF0; overflow:hidden; display:none;" id="filter_categorias_items">
					<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRO POR CATEGORIAS</div>
					<div style="float:left; width:94%; margin-bottom:20px; margin-left:3%; text-align:center;">
						<select style="width:150px; font-weight:bolder; font-size:12px; border: 1px solid #BBB;" onchange="cambiarCategoriaItems(this)" id="nivelCategoriaItems">
							<option value="familia">Familia</option>
							<option value="grupo">Grupo</option>
							<option value="subGrupo">SubGrupo</option>
						</select>
					</div>

					<!-- OPCION TODAS LAS CATEGORIAS -->
					<div style="width:100%; height:30px; margin-left:5px;">
						<span style="cursor:pointer;padding-left: 10px;font-weight:bolder;float:left;margin-bottom: 7px;" onclick="cambiaChekCategoriasItems()"><img src="img/checkbox_false.png" id="imgCheckCategoriasItems">Todas las Categorias</span><br>
					</div>

					<div id="contenedor_formulario_configuracion" style="width:94%; margin:3%; position:relative;">
						<div id="contenedor_tabla_configuracion" style="height:178px; position:absolute;">
							<div class="headTablaBoletas">
								<div class="campo0"><img src="img/buscar20.png" onclick="ventanaBusquedaCategoriasItems();" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar Categorias" id="imgBuscarTerceroBC"></div>
								<div class="campo1" style="width:120px;">Codigo</div>
								<div class="campo2" style="width:180px;">Nombre</div>
							</div>
							<div id="bodyTablaConfiguracionCategorias" style="height:140px;"></div>
						</div>
						<div style="width:481px; height:180px; position:absolute; display:none; background-color:rgba(214, 211, 211, 0.4);" id="divDisableCategoriasItems"></div>
					</div>
				</div>
			</div>

			<!-- DIV MENU DERECHO -->
			<div style="float:right; width:210px; background-color: #eff3fa; height: 100%; border-left:1px solid #8DB2E3;">
				<div style="padding-left:20px;font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none;border-top:none;cursor:pointer" class="x-panel-header" onclick="manageDivsConfig(\'divFechas\')">

						Fechas del Informe

					<div style="float:right;width:20px">
						<img id="img_divFechas" src ="img/arrow.gif"/>
					</div>
				</div>
				<div style="display:table; margin:auto;margin-top:25px;margin-bottom:20px;" id="divFechas">
					<div style="overflow:hidden;" id="divFechaInicio">
						<div style="float:left; width:70px; margin-right:5px">Fecha Inicial:</div>
						<div style="float:left; width:120px;"><input type="text" id="MyInformeFiltroFechaInicio"></div>
					</div>
					<div style="overflow:hidden; margin-top:20px;">
						<div style="float:left; width:70px; margin-right:5px">Fecha Final:</div>
						<div style="float:left; width:120px;"><input type="text" id="MyInformeFiltroFechaFinal"></div>
					</div>
				</div>

				<!-- TOTALIZADOS FILTROS -->
				<div id="divMenuTotalizado" style="font-weight: bolder;padding-left: 20px; font-size:12px; text-align:center; border-right:0px; border-left:none;cursor:pointer" class="x-panel-header" onclick="manageDivsConfig(\'divTotalizados\')">
					Totalizado
					<div style="float:right;width:20px">
						<img id="img_divTotalizados" src ="img/arrow.gif"/>
					</div>
				</div>
				<div style="margin-left:10px;margin-top:15px;margin-bottom:20px; overflow:hidden;" id="divTotalizados">

					<div style="margin-bottom:6px; overflow:hidden;">
						<input type="radio" name="totalizado_items" value="ninguno" style="float:left; width:30px" onchange="checkTotalItems=this.value;" checked>
						<div style="float:left;">Ninguno</div>
					</div>

					<div style="margin-bottom:6px; overflow:hidden;">
						<input type="radio" id="totalizado_items_clientes" name="totalizado_items" value="clientes" style="float:left; width:30px" onchange="checkTotalItems=this.value;">
						<div style="float:left;">Clientes</div>
					</div>

					<div style="margin-bottom:6px; overflow:hidden;">
						<input type="radio" id="totalizado_items_vendedoes" name="totalizado_items" value="vendedoes" style="float:left; width:30px" onchange="checkTotalItems=this.value;">
						<div style="float:left;">Vendedores</div>
					</div>

					<div style="margin-bottom:6px; overflow:hidden;">
						<input type="radio" id="totalizado_items_ccos" name="totalizado_items" value="ccos" style="float:left; width:30px" onchange="checkTotalItems=this.value;">
						<div style="float:left;">Centro de Costo</div>
					</div>

					<div style="margin-bottom:6px; overflow:hidden;">
						<input type="radio" id="totalizado_items_categorias" name="totalizado_items" value="categorias" style="float:left; width:30px" onchange="checkTotalItems=this.value;">
						<div style="float:left;">Categorias</div>
					</div>

				</div>
				<!--FILTRO DE ORDENAMIENTO-->
				<div style="padding-left:20px;font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none;cursor:pointer" class="x-panel-header" onclick="manageDivsConfig(\'divOpciones\')">
					Opciones
					<div style="float:right;width:20px">
						<img id="img_divOpciones" src ="img/arrow.gif"/>
					</div>
				</div>
				<div style="margin-left:10px; margin-top:15px;overflow:hidden;" id="divOpciones">
					<div style="margin-bottom:6px; overflow:hidden;width:100%">
						<div style="margin-bottom:6px; overflow:hidden;width:35%;height:26px;float:left">
							Ordenar por:
						</div>
						<div style="margin-bottom:6px; overflow:hidden;width:65%;height:26px;float:left">
							<select class="myfield" id="select_order_items" name="select_order_items" style="width:120px" onchange="">
								<option value="codigo_item">Codigo</option>
								<option value="nombre_item">Nombre</option>
								<option value="cantidad">Cantidad</option>
								<option value="precio">Precio</option>
							</select>
						</div>
					</div>
					<div style="margin-bottom:6px; overflow:hidden;width:100%">
						<input type="radio" id="order_items_ascendente" name="order_items" value="ASC" style="float:left; width:30px" onchange="checkOrderItems=this.value;" checked>
						<div style="float:left;">Ascendente</div>
					</div>
					<div style="margin-bottom:6px; overflow:hidden;width:100%">
						<input type="radio" id="order_items_descendente" name="order_items" value="DESC" style="float:left; width:30px" onchange="checkOrderItems=this.value;">
						<div style="float:left;">Descendente</div>
					</div>
					<div style="margin-bottom:6px;margin-top:14px;overflow:hidden;width:100%;height:30px">
						<div style="float:left;width:115px;">Numero de Registros:</div>
						<input class ="myfield" id="fieldLimiteRows_items" value="" placeholder="todos" style="float:left; width:75px;padding-left:5px;" onKeyup="validarCampoNumerico(event,this);">
					</div>
				</div>
			</div>
		</div>

		<script>

			var selectOrder = document.getElementById("select_order_items");

			document.getElementById("nivelCategoriaItems").value = nivelCategoria;

			allClientesItems   = (allClientesItems=="true")? "false": "true";
			allVendedoresItems = (allVendedoresItems=="true")? "false": "true";
			allCcosItems       = (allCcosItems=="true")? "false": "true";
			allCategoriasItems = (allCategoriasItems=="true")? "false": "true";

			cambiaChekCategoriasItems();
			cambiaChekCcosItems();
			cambiaChekVendedoresItems();
			cambiaChekClientesItems();

		  	new Ext.form.DateField({
				format     : "Y-m-d",
				width      : 120,
				id         : "cmpFechaInicio",
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

			if (typeof(localStorage.MyInformeFiltroFechaInicioItems)!="undefined") {
				if (localStorage.MyInformeFiltroFechaInicioItems!="") {
					document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicioItems;
				}
			}

			if (typeof(localStorage.MyInformeFiltroFechaFinalItems)!="undefined") {
				if (localStorage.MyInformeFiltroFechaFinalItems!="") {
					document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinalItems;
				}
			}

			//DIV DE ARRAY CON TERCEROS
			for ( i = 0; i < array_terceros_items.length; i++) {
				if (typeof(array_terceros_items[i])!="undefined" && array_terceros_items[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_tercero_items_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracion").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_tercero_items_"+i).innerHTML=terceros_config_items[i];
				}
			}

			//DIV DE ARRAY CON VENDEDORES
			for ( i = 0; i < array_vendedores_items.length; i++) {
				if (typeof(array_vendedores_items[i])!="undefined" && array_vendedores_items[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_vendedor_items_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracionVendedores").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_vendedor_items_"+i).innerHTML=vendedores_config_items[i];
				}
			}

			//DIV DE ARRAY CON CENTRO DE COSTO
			for ( i = 0; i < array_ccos_items.length; i++) {
				if (typeof(array_ccos_items[i])!="undefined" && array_ccos_items[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_ccos_items_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracionCentroCostos").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_ccos_items_"+i).innerHTML=ccos_config_Items[i];
				}
			}

			//DIV DE ARRAY CON CATEGORIAS
			for ( i = 0; i < array_categorias_items.length; i++) {
				if (typeof(array_categorias_items[i])!="undefined" && array_categorias_items[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_categorias_items_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracionCategorias").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_categorias_items_"+i).innerHTML=categorias_config_Items[i];
				}
			}

			if (typeof(localStorage.filtroOrden_items)!="undefined") {
				if (localStorage.filtroOrden_items!="") {
					document.getElementById("select_order_items").value=localStorage.filtroOrden_items;
				}
			}

			if (typeof(localStorage.limiteRows_items)!="undefined") {
				if (localStorage.limiteRows_items!="") {
					document.getElementById("fieldLimiteRows_items").value=localStorage.limiteRows_items;
				}
			}

			function display_filter_items(filter){
				document.getElementById("filter_ccos_items").style.display       = "none";
				document.getElementById("filter_terceros_items").style.display   = "none";
				document.getElementById("filter_vendedores_items").style.display = "none";
				document.getElementById("filter_categorias_items").style.display = "none";

				document.getElementById("filter_ccos_items").style.backgroundColor       = "none";
				document.getElementById("filter_terceros_items").style.backgroundColor   = "none";
				document.getElementById("filter_vendedores_items").style.backgroundColor = "none";
				document.getElementById("filter_categorias_items").style.backgroundColor = "none";

				document.getElementById("tab_filter_ccos_items").style.margin       = "2px 1px 0 1px";
				document.getElementById("tab_filter_terceros_items").style.margin   = "2px 1px 0 1px";
				document.getElementById("tab_filter_vendedores_items").style.margin = "2px 1px 0 1px";
				document.getElementById("tab_filter_categorias_items").style.margin = "2px 1px 0 1px";

				document.getElementById(filter).style.display       = "block";
				document.getElementById("tab_"+filter).style.margin = "3px 1px 0 1px";
			}

			//CHECKBOX CATEGORIAS
			function cambiaChekCategoriasItems(){

				if (allCategoriasItems=="false") {
					document.getElementById("imgCheckCategoriasItems").setAttribute("src","img/checkbox_true.png");
					document.getElementById("divDisableCategoriasItems").style.display="block";
					allCategoriasItems="true";
					agregaOptionSelectOrder(selectOrder,"Categoria","categoria");
				}
				else{
					document.getElementById("imgCheckCategoriasItems").setAttribute("src","img/checkbox_false.png");
					document.getElementById("divDisableCategoriasItems").style.display="none";
					allCategoriasItems="false";
					quitaOptionSelectOrder(selectOrder,"categoria");
				}

			}

			//CHECKBOX CCOS
			function cambiaChekCcosItems(){

				if (allCcosItems=="false") {
					document.getElementById("imgCheckCcosItems").setAttribute("src","img/checkbox_true.png");
					document.getElementById("divDisableCcosItems").style.display="block";
					allCcosItems="true";
					agregaOptionSelectOrder(selectOrder,"Centro de Costos","centro_costos");
				}
				else{
					document.getElementById("imgCheckCcosItems").setAttribute("src","img/checkbox_false.png");
					document.getElementById("divDisableCcosItems").style.display="none";
					allCcosItems="false";
					quitaOptionSelectOrder(selectOrder,"centro_costos");
				}

			}

			//CHECKBOX VENDEDORES
			function cambiaChekVendedoresItems(){

				if (allVendedoresItems=="false") {
					document.getElementById("imgCheckVendedoresItems").setAttribute("src","img/checkbox_true.png");
					document.getElementById("divDisableVendedoresItems").style.display="block";
					allVendedoresItems="true";
					agregaOptionSelectOrder(selectOrder,"Vendedor","vendedor");
				}
				else{
					document.getElementById("imgCheckVendedoresItems").setAttribute("src","img/checkbox_false.png");
					document.getElementById("divDisableVendedoresItems").style.display="none";
					allVendedoresItems="false";
					quitaOptionSelectOrder(selectOrder,"vendedor");
				}
			}

			//CHECKBOX CLIENTES
			function cambiaChekClientesItems(){

				if (allClientesItems=="false") {
					document.getElementById("imgCheckClientesItems").setAttribute("src","img/checkbox_true.png");
					document.getElementById("divDisableClientesItems").style.display="block";
					allClientesItems="true";
					agregaOptionSelectOrder(selectOrder,"Cliente","cliente");
				}
				else{
					document.getElementById("imgCheckClientesItems").setAttribute("src","img/checkbox_false.png");
					document.getElementById("divDisableClientesItems").style.display="none";
					allClientesItems="false";
					quitaOptionSelectOrder(selectOrder,"cliente");
				}
			}

		</script>';
}

//==========================// VENTANA CONFIGURACION ITEMS //==========================//
//*************************************************************************************//
function ventana_configuracion_itemsRemisionados(){

	$date = strtotime(date("Y-m-d"));
    $anio = date("Y", $date);
    $mes  = date("m", $date);
    $dia  = date("d",$date);

    //CALCULAR EL FINAL DEL MES
    $fechaInicial = date("Y-m-d",(mktime(0,0,0,$mes,1,$anio)-1));

	echo'<div style="border-top:1px solid #8DB2E3; width:100%; overflow:hidden;" id="informeBalancePrueba">
			<!-- DIV IZQUIERDO -->
			<div style="width: calc(100% - 211px); padding:0; float:left; height:270px;">

				<div style="overflow:visible; border-bottom:1px solid #99BBE8; float:left; width:100%; height:24px;">
					<div style="font-weight:bolder; font-size:10px; text-align:center; margin:2px 1px 0 0; padding:3px 5px; display:block; float:left; border-bottom: none;" class="x-panel-header" onClick="display_filter_itemsRemisionados(\'filter_terceros_itemsRemisionados\');" id="tab_filter_terceros_itemsRemisionados">CLIENTE</div>
					<div style="font-weight:bolder; font-size:10px; text-align:center; margin:2px 1px 0 1px; padding:3px 5px; display:block; float:left; border-bottom: none;" class="x-panel-header" onClick="display_filter_itemsRemisionados(\'filter_vendedores_itemsRemisionados\');" id="tab_filter_vendedores_itemsRemisionados">VENDEDOR</div>
					<div style="font-weight:bolder; font-size:10px; text-align:center; margin:2px 1px 0 1px; padding:3px 5px; display:block; float:left; border-bottom: none;" class="x-panel-header" onClick="display_filter_itemsRemisionados(\'filter_ccos_itemsRemisionados\');" id="tab_filter_ccos_itemsRemisionados">CENTRO DE COSTO</div>
					<div style="font-weight:bolder; font-size:10px; text-align:center; margin:2px 1px 0 1px; padding:3px 5px; display:block; float:left; border-bottom: none;" class="x-panel-header" onClick="display_filter_itemsRemisionados(\'filter_categorias_itemsRemisionados\');" id="tab_filter_categorias_itemsRemisionados">CATEGORIAS</div>
				</div>

				<!-- VENTANA FILTRO TERCERO -->

				<div style="width:100%; height:432px; background-color:#CDDBF0; overflow:hidden; display:none;" id="filter_terceros_itemsRemisionados">
					<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRO POR CLIENTES</div>

					<!-- OPCION TODOS LOS CLIENTES -->
					<div style="width:100%; height:30px; margin-left:5px;">
						<span style="cursor:pointer;padding-left: 10px;font-weight:bolder;float:left;margin-bottom: 7px;" onclick="cambiaChekClientesItemsRemisionados()"><img src="img/checkbox_false.png" id="imgCheckClientesItemsRemisionados">Todos los Clientes</span><br>
					</div>

					<div id="contenedor_formulario_configuracion" style="width:94%; margin:3%; position:relative;">
						<div id="contenedor_tabla_configuracion" style="height:178px; position:absolute;">
							<div class="headTablaBoletas">
								<div class="campo0">&nbsp;</div>
								<div class="campo1">Nit</div>
								<div class="campo2" style="width:150px;">Tercero</div>
								<div class="campo4" style="width:25px;"><img src="img/buscar20.png" onclick="ventanaBusquedaTercero_itemsRemisionados();" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar Clientes" id="imgBuscarTerceroBC"></div>
							</div>
							<div id="bodyTablaConfiguracion" style="height:140px;"></div>
						</div>
						<div style="width:481px; height:180px; position:absolute; display:none; background-color:rgba(214, 211, 211, 0.4);" id="divDisableClientesItemsRemisionados"></div>
					</div>
				</div>

				<!-- VENTANA FILTRO VENDEDORES -->

				<div style="width:100%; height:432px; background-color: #CDDBF0; overflow:hidden; display:none;" id="filter_vendedores_itemsRemisionados">
					<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRO POR VENDEDORES</div>

					<!-- OPCION TODOS LOS VENDEDORES -->
					<div style="width:100%; height:30px; margin-left:5px;">
						<span style="cursor:pointer;padding-left: 10px;font-weight:bolder;float:left;margin-bottom: 7px;" onclick="cambiaChekVendedoresItemsRemisionados()"><img src="img/checkbox_false.png" id="imgCheckVendedoresItemsRemisionados">Todas los Vendedores</span><br>
					</div>

					<div id="contenedor_formulario_configuracion" style="width:94%; margin:3%; position:relative;">
						<div id="contenedor_tabla_configuracion" style="height:178px; position:absolute;">
							<div class="headTablaBoletas">
								<div class="campo0">&nbsp;</div>
								<div class="campo1">Identificacion</div>
								<div class="campo2" style="width: 150px;">Vendedor</div>
								<div class="campo4" style="width:25px;"><img src="img/buscar20.png" onclick="ventanaBusquedaTercero_itemsRemisionados(\'vendedores\');" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar Vendedores" id="imgBuscarTerceroBC"></div>
							</div>
							<div id="bodyTablaConfiguracionVendedores" style="height:140px;"></div>
						</div>
						<div style="width:481px; height:180px; position:absolute; display:none; background-color:rgba(214, 211, 211, 0.4);" id="divDisableVendedoresItemsRemisionados"></div>
					</div>
				</div>

				<!-- VENTANA FILTRO CENTRO DE COSTO -->

				<div style="width:100%; height:432px; background-color:#CDDBF0; overflow:hidden; display:none;" id="filter_ccos_itemsRemisionados">
					<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRO POR CENTROS DE COSTO</div>

					<!-- OPCION TODOS LOS CENTROS DE COSTO -->
					<div style="width:100%; height:30px; margin-left:5px;">
						<span style="cursor:pointer;padding-left: 10px;font-weight:bolder;float:left;margin-bottom: 7px;" onclick="cambiaChekCcosItemsRemisionados()"><img src="img/checkbox_false.png" id="imgCheckCcosItemsRemisionados">Todos los Centros de Costo</span><br>
					</div>

					<div id="contenedor_formulario_configuracion" style="width:94%; margin:3%; position:relative;">
						<div id="contenedor_tabla_configuracion" style="height:178px; position:absolute;">
							<div class="headTablaBoletas">
								<div class="campo0"><img src="img/buscar20.png" onclick="ventanaBusquedaCentroCostosItemsRemisionados();" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar Centros de Costo" id="imgBuscarTerceroBC"></div>
								<div class="campo1" style="width:120px;">Codigo</div>
								<div class="campo2" style="width:180px;">Nombre</div>
							</div>
							<div id="bodyTablaConfiguracionCentroCostos" style="height:140px;"></div>
						</div>
						<div style="width:481px; height:180px; position:absolute; display:none; background-color:rgba(214, 211, 211, 0.4);" id="divDisableCcosItemsRemisionados"></div>
					</div>
				</div>

				<!-- VENTANA FILTRO CATEGORIAS -->

				<div style="width:100%; height:432px; background-color:#CDDBF0; overflow:hidden; display:none;" id="filter_categorias_itemsRemisionados">
					<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRO POR CATEGORIAS</div>
					<div style="float:left; width:94%; margin-bottom:20px; margin-left:3%; text-align:center;">
						<select style="width:150px; font-weight:bolder; font-size:12px; border: 1px solid #BBB;" onchange="cambiarCategoriaItemsRemisionados(this)" id="nivelCategoriaItemsRemisionados">
							<option value="familia">Familia</option>
							<option value="grupo">Grupo</option>
							<option value="subGrupo">SubGrupo</option>
						</select>
					</div>

					<!-- OPCION TODAS LAS CATEGORIAS -->
					<div style="width:100%; height:30px; margin-left:5px;">
						<span style="cursor:pointer;padding-left: 10px;font-weight:bolder;float:left;margin-bottom: 7px;" onclick="cambiaChekCategoriasItemsRemisionados()"><img src="img/checkbox_false.png" id="imgCheckCategoriasItemsRemisionados">Todas las Categorias</span><br>
					</div>

					<div id="contenedor_formulario_configuracion" style="width:94%; margin:3%; position:relative;">
						<div id="contenedor_tabla_configuracion" style="height:178px; position:absolute;">
							<div class="headTablaBoletas">
								<div class="campo0"><img src="img/buscar20.png" onclick="ventanaBusquedaCategoriasItemsRemisionados();" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar Categorias" id="imgBuscarTerceroBC"></div>
								<div class="campo1" style="width:120px;">Codigo</div>
								<div class="campo2" style="width:180px;">Nombre</div>
							</div>
							<div id="bodyTablaConfiguracionCategorias" style="height:140px;"></div>
						</div>
						<div style="width:481px; height:180px; position:absolute; display:none; background-color:rgba(214, 211, 211, 0.4);" id="divDisableCategoriasItemsRemisionados"></div>
					</div>
				</div>
			</div>

			<!-- DIV MENU DERECHO -->
			<div style="float:right; width:210px; background-color: #eff3fa; height: 100%; border-left:1px solid #8DB2E3;">
				<div style="padding-left:20px;font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none;border-top:none;cursor:pointer" class="x-panel-header" onclick="manageDivsConfig(\'divFechas\')">
					Fechas del Informe
					<div style="float:right;width:20px">
						<img id="img_divFechas" src ="img/arrow.gif"/>
					</div>
				</div>
				<div style="display:table; margin:auto;margin-top:25px;margin-bottom:20px;" id="divFechas">
					<div style="overflow:hidden;" id="divFechaInicio">
						<div style="float:left; width:70px; margin-right:5px">Fecha Inicial:</div>
						<div style="float:left; width:120px;"><input type="text" id="MyInformeFiltroFechaInicio"></div>
					</div>
					<div style="overflow:hidden; margin-top:20px;">
						<div style="float:left; width:70px; margin-right:5px">Fecha Final:</div>
						<div style="float:left; width:120px;"><input type="text" id="MyInformeFiltroFechaFinal"></div>
					</div>
				</div>

				<!-- TOTALIZADOS FILTROS -->
				<div id="divMenuTotalizado" style="font-weight: bolder;padding-left: 20px; font-size:12px; text-align:center; border-right:0px; border-left:none;cursor:pointer" class="x-panel-header" onclick="manageDivsConfig(\'divTotalizados\')">
					Totalizado
					<div style="float:right;width:20px">
						<img id="img_divTotalizados" src ="img/arrow.gif"/>
					</div>
				</div>
				<div style="margin-left:10px;margin-top:15px;margin-bottom:20px; overflow:hidden;" id="divTotalizados">

					<div style="margin-bottom:6px; overflow:hidden;">
						<input type="radio" name="totalizado_itemsRemisionados" value="ninguno" style="float:left; width:30px" onchange="checkTotalItemsRemisionados=this.value;" checked>
						<div style="float:left;">Ninguno</div>
					</div>

					<div style="margin-bottom:6px; overflow:hidden;">
						<input type="radio" id="totalizado_itemsRemisionados_clientes" name="totalizado_itemsRemisionados" value="clientes" style="float:left; width:30px" onchange="checkTotalItemsRemisionados=this.value;">
						<div style="float:left;">Clientes</div>
					</div>

					<div style="margin-bottom:6px; overflow:hidden;">
						<input type="radio" id="totalizado_itemsRemisionados_vendedoes" name="totalizado_itemsRemisionados" value="vendedoes" style="float:left; width:30px" onchange="checkTotalItemsRemisionados=this.value;">
						<div style="float:left;">Vendedores</div>
					</div>

					<div style="margin-bottom:6px; overflow:hidden;">
						<input type="radio" id="totalizado_itemsRemisionados_ccos" name="totalizado_itemsRemisionados" value="ccos" style="float:left; width:30px" onchange="checkTotalItemsRemisionados=this.value;">
						<div style="float:left;">Centro de Costo</div>
					</div>

					<div style="margin-bottom:6px; overflow:hidden;">
						<input type="radio" id="totalizado_itemsRemisionados_categorias" name="totalizado_itemsRemisionados" value="categorias" style="float:left; width:30px" onchange="checkTotalItemsRemisionados=this.value;">
						<div style="float:left;">Categorias</div>
					</div>

				</div>
				<!--FILTRO DE ORDENAMIENTO-->
				<div style="padding-left:20px;font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none;cursor:pointer" class="x-panel-header" onclick="manageDivsConfig(\'divOpciones\')">
					Opciones
					<div style="float:right;width:20px">
						<img id="img_divOpciones" src ="img/arrow.gif"/>
					</div>
				</div>
				<div style="margin-left:10px; margin-top:15px;overflow:hidden;" id="divOpciones">
					<div style="margin-bottom:6px; overflow:hidden;width:100%">
						<div style="margin-bottom:6px; overflow:hidden;width:35%;height:26px;float:left">
							Ordenar por:
						</div>
						<div style="margin-bottom:6px; overflow:hidden;width:65%;height:26px;float:left">
							<select class="myfield" id="select_order_itemsRemisionados" name="select_order_itemsRemisionados" style="width:120px" onchange="">
								<option value="codigo_item">Codigo</option>
								<option value="nombre_item">Nombre</option>
								<option value="cantidad">Cantidad</option>
								<option value="precio">Precio</option>
							</select>
						</div>
					</div>
					<div style="margin-bottom:6px; overflow:hidden;width:100%">
						<input type="radio" id="order_itemsRemisionados_ascendente" name="order_itemsRemisionados" value="ASC" style="float:left; width:30px" onchange="checkOrderItemsRemisionados=this.value;" checked>
						<div style="float:left;">Ascendente</div>
					</div>
					<div style="margin-bottom:6px; overflow:hidden;width:100%">
						<input type="radio" id="order_itemsRemisionados_descendente" name="order_itemsRemisionados" value="DESC" style="float:left; width:30px" onchange="checkOrderItemsRemisionados=this.value;">
						<div style="float:left;">Descendente</div>
					</div>
					<div style="margin-bottom:6px;margin-top:14px;overflow:hidden;width:100%;height:30px">
						<div style="float:left;width:115px;">Numero de Registros:</div>
						<input class ="myfield" id="fieldLimiteRows_itemsRemisionados" value="" placeholder="todos" style="float:left; width:75px;padding-left:5px;" onKeyup="validarCampoNumerico(event,this);">
					</div>
				</div>
			</div>
		</div>

		<script>

			var selectOrder = document.getElementById("select_order_itemsRemisionados");

			document.getElementById("nivelCategoriaItemsRemisionados").value = nivelCategoria;

			allClientesItemsRemisionados   = (allClientesItemsRemisionados=="true")? "false": "true";
			allVendedoresItemsRemisionados = (allVendedoresItemsRemisionados=="true")? "false": "true";
			allCcosItemsRemisionados       = (allCcosItemsRemisionados=="true")? "false": "true";
			allCategoriasItemsRemisionados = (allCategoriasItemsRemisionados=="true")? "false": "true";

			cambiaChekCategoriasItemsRemisionados();
			cambiaChekCcosItemsRemisionados();
			cambiaChekVendedoresItemsRemisionados();
			cambiaChekClientesItemsRemisionados();

		  	new Ext.form.DateField({
				format     : "Y-m-d",
				width      : 120,
				id         : "cmpFechaInicio",
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

			if (typeof(localStorage.MyInformeFiltroFechaInicioItemsRemisionados)!="undefined") {
				if (localStorage.MyInformeFiltroFechaInicioItemsRemisionados!="") {
					document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicioItemsRemisionados;
				}
			}

			if (typeof(localStorage.MyInformeFiltroFechaFinalItemsRemisionados)!="undefined") {
				if (localStorage.MyInformeFiltroFechaFinalItemsRemisionados!="") {
					document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinalItemsRemisionados;
				}
			}

			//DIV DE ARRAY CON TERCEROS
			for ( i = 0; i < array_terceros_itemsRemisionados.length; i++) {
				if (typeof(array_terceros_itemsRemisionados[i])!="undefined" && array_terceros_itemsRemisionados[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_tercero_itemsRemisionados_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracion").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_tercero_itemsRemisionados_"+i).innerHTML=terceros_config_itemsRemisionados[i];
				}
			}

			//DIV DE ARRAY CON VENDEDORES
			for ( i = 0; i < array_vendedores_itemsRemisionados.length; i++) {
				if (typeof(array_vendedores_itemsRemisionados[i])!="undefined" && array_vendedores_itemsRemisionados[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_vendedor_itemsRemisionados_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracionVendedores").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_vendedor_itemsRemisionados_"+i).innerHTML=vendedores_config_itemsRemisionados[i];
				}
			}

			//DIV DE ARRAY CON CENTRO DE COSTO
			for ( i = 0; i < array_ccos_itemsRemisionados.length; i++) {
				if (typeof(array_ccos_itemsRemisionados[i])!="undefined" && array_ccos_itemsRemisionados[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_ccos_itemsRemisionados_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracionCentroCostos").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_ccos_itemsRemisionados_"+i).innerHTML=ccos_config_ItemsRemisionados[i];
				}
			}

			//DIV DE ARRAY CON CATEGORIAS
			for ( i = 0; i < array_categorias_itemsRemisionados.length; i++) {
				if (typeof(array_categorias_itemsRemisionados[i])!="undefined" && array_categorias_itemsRemisionados[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_categorias_itemsRemisionados_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracionCategorias").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_categorias_itemsRemisionados_"+i).innerHTML=categorias_config_ItemsRemisionados[i];
				}
			}

			if (typeof(localStorage.filtroOrden_itemsRemisionados)!="undefined") {
				if (localStorage.filtroOrden_itemsRemisionados!="") {
					document.getElementById("select_order_itemsRemisionados").value=localStorage.filtroOrden_itemsRemisionados;
				}
			}

			if (typeof(localStorage.limiteRows_itemsRemisionados)!="undefined") {
				if (localStorage.limiteRows_itemsRemisionados!="") {
					document.getElementById("fieldLimiteRows_itemsRemisionados").value=localStorage.limiteRows_itemsRemisionados;
				}
			}

			function display_filter_itemsRemisionados(filter){
				document.getElementById("filter_ccos_itemsRemisionados").style.display       = "none";
				document.getElementById("filter_terceros_itemsRemisionados").style.display   = "none";
				document.getElementById("filter_vendedores_itemsRemisionados").style.display = "none";
				document.getElementById("filter_categorias_itemsRemisionados").style.display = "none";

				document.getElementById("filter_ccos_itemsRemisionados").style.backgroundColor       = "none";
				document.getElementById("filter_terceros_itemsRemisionados").style.backgroundColor   = "none";
				document.getElementById("filter_vendedores_itemsRemisionados").style.backgroundColor = "none";
				document.getElementById("filter_categorias_itemsRemisionados").style.backgroundColor = "none";

				document.getElementById("tab_filter_ccos_itemsRemisionados").style.margin       = "2px 1px 0 1px";
				document.getElementById("tab_filter_terceros_itemsRemisionados").style.margin   = "2px 1px 0 1px";
				document.getElementById("tab_filter_vendedores_itemsRemisionados").style.margin = "2px 1px 0 1px";
				document.getElementById("tab_filter_categorias_itemsRemisionados").style.margin = "2px 1px 0 1px";

				document.getElementById(filter).style.display       = "block";
				document.getElementById("tab_"+filter).style.margin = "3px 1px 0 1px";
			}

			//CHECKBOX CATEGORIAS
			function cambiaChekCategoriasItemsRemisionados(){

				if (allCategoriasItemsRemisionados=="false") {
					document.getElementById("imgCheckCategoriasItemsRemisionados").setAttribute("src","img/checkbox_true.png");
					document.getElementById("divDisableCategoriasItemsRemisionados").style.display="block";
					allCategoriasItemsRemisionados="true";
					agregaOptionSelectOrder(selectOrder,"Categoria","categoria");
				}
				else{
					document.getElementById("imgCheckCategoriasItemsRemisionados").setAttribute("src","img/checkbox_false.png");
					document.getElementById("divDisableCategoriasItemsRemisionados").style.display="none";
					allCategoriasItemsRemisionados="false";
					quitaOptionSelectOrder(selectOrder,"categoria");
				}
			}

			//CHECKBOX CCOS
			function cambiaChekCcosItemsRemisionados(){

				if (allCcosItemsRemisionados=="false") {
					document.getElementById("imgCheckCcosItemsRemisionados").setAttribute("src","img/checkbox_true.png");
					document.getElementById("divDisableCcosItemsRemisionados").style.display="block";
					allCcosItemsRemisionados="true";
					agregaOptionSelectOrder(selectOrder,"Centro de Costos","centro_costos");
				}
				else{
					document.getElementById("imgCheckCcosItemsRemisionados").setAttribute("src","img/checkbox_false.png");
					document.getElementById("divDisableCcosItemsRemisionados").style.display="none";
					allCcosItemsRemisionados="false";
					quitaOptionSelectOrder(selectOrder,"centro_costos");
				}
			}

			//CHECKBOX VENDEDORES
			function cambiaChekVendedoresItemsRemisionados(){

				if (allVendedoresItemsRemisionados=="false") {
					document.getElementById("imgCheckVendedoresItemsRemisionados").setAttribute("src","img/checkbox_true.png");
					document.getElementById("divDisableVendedoresItemsRemisionados").style.display="block";
					allVendedoresItemsRemisionados="true";
					agregaOptionSelectOrder(selectOrder,"Vendedor","vendedor");
				}
				else{
					document.getElementById("imgCheckVendedoresItemsRemisionados").setAttribute("src","img/checkbox_false.png");
					document.getElementById("divDisableVendedoresItemsRemisionados").style.display="none";
					allVendedoresItemsRemisionados="false";
					quitaOptionSelectOrder(selectOrder,"vendedor");
				}
			}

			//CHECKBOX CLIENTES
			function cambiaChekClientesItemsRemisionados(){

				if (allClientesItemsRemisionados=="false") {
					document.getElementById("imgCheckClientesItemsRemisionados").setAttribute("src","img/checkbox_true.png");
					document.getElementById("divDisableClientesItemsRemisionados").style.display="block";
					allClientesItemsRemisionados="true";
					agregaOptionSelectOrder(selectOrder,"Cliente","cliente");
				}
				else{
					document.getElementById("imgCheckClientesItemsRemisionados").setAttribute("src","img/checkbox_false.png");
					document.getElementById("divDisableClientesItemsRemisionados").style.display="none";
					allClientesItemsRemisionados="false";
					quitaOptionSelectOrder(selectOrder,"cliente");
				}
			}

		</script>';
}

//==========================// VENTANA CONFIGURACION FACTURA //==========================//
//***************************************************************************************//
function cuerpoVentanaConfiguracionFacturas(){

	$date = strtotime(date("Y-m-d"));
    $anio = date("Y", $date);
    $mes  = date("m", $date);
    $dia  = date("d",$date);

    //CALCULAR EL FINAL DEL MES
    $fechaInicial = date("Y-m-d",(mktime(0,0,0,$mes,1,$anio)-1));

	echo'<div style="border-top:1px solid #8DB2E3; width:100%; overflow:hidden;" id="informeBalancePrueba">
			<!-- DIV MENU IZQUIERDO -->
			<div style="width: calc(100% - 320px - 3px); padding:0; float:left; height:270px; margin-left:5px;float:left;">

				<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRAR POR CLIENTE(S)</div>

				<!-- VENTANA BUSCAR TERCERO -->
				<div style="width:100%;height:180px;background-color: #CDDBF0;overflow:hidden;">
					<div id="contenedor_formulario_configuracion" >
						<div id="contenedor_tabla_configuracion" style="height:178px;">
							<div class="headTablaBoletas">
								<div class="campo0">&nbsp;</div>
								<div class="campo1">Nit</div>
								<div class="campo2" style="width: 150px;">Cliente</div>
								<div class="campo4" style="width:25px;"><img src="img/buscar20.png" onclick="ventanaBusquedaTerceroFV();" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC"></div>
							</div>
							<div id="bodyTablaConfiguracion" style="height:140px;"></div>
						</div>
					</div>
				</div>

				<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRAR POR VENDEDORES(S)</div>

				<!-- VENTANA BUSCAR TERCERO -->
				<div style="width:100%;height:180px;background-color: #CDDBF0;overflow:hidden;">
					<div id="contenedor_formulario_configuracion" >
						<div id="contenedor_tabla_configuracion" style="height:178px;">
							<div class="headTablaBoletas">
								<div class="campo0">&nbsp;</div>
								<div class="campo1">Identificacion</div>
								<div class="campo2" style="width: 150px;">Vendedor</div>
								<div class="campo4" style="width:25px;"><img src="img/buscar20.png" onclick="ventanaBusquedaTerceroFV(\'vendedores\');" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC"></div>
							</div>
							<div id="bodyTablaConfiguracionVendedores" style="height:140px;"></div>
						</div>
					</div>
				</div>

			</div>

			<!-- DIV MENU DERECHO -->
			<div style="float:right; width:310px; background-color: #eff3fa; height: 100%; border-left:1px solid #8DB2E3;">
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

					<div style="margin-top:20px;text-align:center;font-weight:bold;/*display:none;*/">
						<input type="checkbox" id="discriminar_items_facturas_venta"> Discriminar items
					</div>
				</div>

				<div style="margin:25px 0 25px 0; font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none;" class="x-panel-header">Centros de Costo</div>
				<div style="width: calc(100% - 12px);height:180px;background-color: #CDDBF0;overflow:hidden;margin-left: 5px;">
					<div id="contenedor_formulario_configuracion" >
						<div id="contenedor_tabla_configuracion" style="height:178px;">
							<div class="headTablaBoletas">
								<div class="campo0"><img src="img/buscar20.png" onclick="ventanaBusquedaCentroCostosFV();" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC"></div>
								<div class="campo1" style="width: 70px;">Codigo</div>
								<div class="campo2" style="width: 150px;">Nombre</div>
							</div>
							<div id="bodyTablaConfiguracionCentroCostos" style="height:140px;"></div>
						</div>
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


			if (typeof(localStorage.sucursal_facturas)!="undefined") {
				if (localStorage.sucursal_facturas!="") {
					setTimeout(function(){ document.getElementById("filtro_sucursal_facturas").value=localStorage.sucursal_facturas; },100);
				}
			}

			if (typeof(localStorage.MyInformeFiltroFechaInicioFacturas)!="undefined") {
				if (localStorage.MyInformeFiltroFechaInicioFacturas!="") {
					document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicioFacturas;
				}
			}

			if (typeof(localStorage.MyInformeFiltroFechaFinalFacturas)!="undefined") {
				if (localStorage.MyInformeFiltroFechaFinalFacturas!="") {
					document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinalFacturas;
				}
			}

			if (typeof(localStorage.discriminar_items_facturas_venta)!="undefined") {
				if (localStorage.discriminar_items_facturas_venta!="") {
					if (localStorage.discriminar_items_facturas_venta=="true") {
						document.getElementById("discriminar_items_facturas_venta").checked = true;
					}
					else{
						document.getElementById("discriminar_items_facturas_venta").checked = false;
					}
				}
			}

			//CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS TERCEROS GUARDADOS
			for ( i = 0; i < arraytercerosFV.length; i++) {
				if (typeof(arraytercerosFV[i])!="undefined" && arraytercerosFV[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_cartera_tercero_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracion").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_cartera_tercero_"+i).innerHTML=tercerosConfiguradosFV[i];

				}
			}
			//CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS VENDEDORES GUARDADOS
			for ( i = 0; i < arrayvendedoresFV.length; i++) {
				if (typeof(arrayvendedoresFV[i])!="undefined" && arrayvendedoresFV[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_empleado_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracionVendedores").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_empleado_"+i).innerHTML=vendedoresConfiguradosFV[i];

				}
			}

			//CREAMOS LOS DIV DE LOS CENTROS DE COSTO AÑADIDOS RECORRIENDO EL ARRAY DE LOS VENDEDORES GUARDADOS
			for ( i = 0; i < arrayCentroCostosFV.length; i++) {
				if (typeof(arrayCentroCostosFV[i])!="undefined" && arrayCentroCostosFV[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_centro_costo_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracionCentroCostos").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_centro_costo_"+i).innerHTML=CentroCostosConfiguradosFV[i];

				}
			}

		</script>';
}

//==========================// VENTANA IMPUESTOS RETENCIONES //==========================//
//***************************************************************************************//
function ventanaConfiguracion_FVIR(){

	$date = strtotime(date("Y-m-d"));
    $anio = date("Y", $date);
    $mes  = date("m", $date);
    $dia  = date("d",$date);

    //CALCULAR EL FINAL DEL MES
    $fechaInicial = date("Y-m-d",(mktime(0,0,0,$mes,1,$anio)-1));

	echo'<div style="border-top:1px solid #8DB2E3; width:100%; overflow:hidden;" id="informeBalancePrueba">
			<!-- DIV MENU IZQUIERDO -->
			<div style="width: calc(100% - 220px - 3px); padding:0; float:left; height:270px; margin-left:5px;float:left;">

				<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRAR POR CLIENTE(S)</div>

				<!-- VENTANA BUSCAR TERCERO -->
				<div style="width:100%;height:180px;background-color: #CDDBF0;overflow:hidden;">
					<div id="contenedor_formulario_configuracion" >
						<div id="contenedor_tabla_configuracion" style="height:178px;">
							<div class="headTablaBoletas">
								<div class="campo0">&nbsp;</div>
								<div class="campo1">Nit</div>
								<div class="campo2" style="width: 150px;">Cliente</div>
								<div class="campo4" style="width:25px;"><img src="img/buscar20.png" onclick="ventanaBusquedaTercero_FVIR();" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC"></div>
							</div>
							<div id="bodyTablaConfiguracion" style="height:140px;"></div>
						</div>
					</div>
				</div>
			</div>

			<!-- DIV MENU DERECHO -->
			<div style="float:right; width:200px; background-color: #eff3fa; height: 100%; border-left:1px solid #8DB2E3;">
				<div style="margin-bottom:25px; font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none;border-top:none;" class="x-panel-header">Fechas del Informe</div>
				<div style="display:table; margin:auto;">
					<div style="overflow:hidden;" id="divFechaInicio">
						<div style="float:left; width:70px; margin-right:5px">Fecha Inicial:</div>
						<div style="float:left; width:100px;"><input type="text" id="MyInformeFiltroFechaInicio"></div>
					</div>
					<div style="overflow:hidden; margin-top:20px;">
						<div style="float:left; width:70px; margin-right:5px">Fecha Final:</div>
						<div style="float:left; width:100px;"><input type="text" id="MyInformeFiltroFechaFinal"></div>
					</div>
				</div>
			</div>
		</div>

		<script>

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


			if (typeof(localStorage.sucursal_facturas)!="undefined") {
				if (localStorage.sucursal_facturas!="") {
					setTimeout(function(){ document.getElementById("filtro_sucursal_facturas").value=localStorage.sucursal_facturas; },100);
				}
			}

			if (typeof(localStorage.MyInformeFiltroFechaInicioFacturas)!="undefined") {
				if (localStorage.MyInformeFiltroFechaInicioFacturas!="") {
					document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicioFacturas;
				}
			}

			if (typeof(localStorage.MyInformeFiltroFechaFinalFacturas)!="undefined") {
				if (localStorage.MyInformeFiltroFechaFinalFacturas!="") {
					document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinalFacturas;
				}
			}

			//CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS TERCEROS GUARDADOS
			for ( i = 0; i < arrayTerceros_FVIR.length; i++) {
				if (typeof(arrayTerceros_FVIR[i])!="undefined" && arrayTerceros_FVIR[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_cartera_tercero_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracion").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_cartera_tercero_"+i).innerHTML=tercerosConfigurados_FVIR[i];

				}
			}

			// ARRAY CCOS
			for ( i = 0; i < arrayCentroCostos_FVIR.length; i++) {
				if (typeof(arrayCentroCostos_FVIR[i])!="undefined" && arrayCentroCostos_FVIR[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_centro_costo_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracionCentroCostos").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_centro_costo_"+i).innerHTML=conceptosConfigurados_FVIR[i];

				}
			}

		</script>';
}

function cuerpoVentanaConfiguracionFacturasCuentas(){

	$date = strtotime(date("Y-m-d"));
    $anio = date("Y", $date);
    $mes  = date("m", $date);
    $dia  = date("d",$date);

    //CALCULAR EL FINAL DEL MES
    $fechaInicial = date("Y-m-d",(mktime(0,0,0,$mes,1,$anio)-1));

	echo'<div style="border-top:1px solid #8DB2E3; width:100%; overflow:hidden;" id="informeBalancePrueba">
			<!-- DIV MENU IZQUIERDO -->
			<div style="width: calc(100% - 320px - 3px); padding:0; float:left; height:270px; margin-left:5px;float:left;">

				<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRAR POR CLIENTE(S)</div>

				<!-- VENTANA BUSCAR TERCERO -->
				<div style="display:none;background-color: #F3F3F3;border-right: 1px solid #D4D4D4;border-top: 1px solid #D4D4D4;float: left;height: 26px;width: 35px;border-top-right-radius: 5px;padding: 7 0 0 7;" >
					<img src="img/buscar20.png" onclick="ventanaBusquedaTercero();" style="cursor: pointer;width:21px;height:21px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC5">
				</div>

				<div style="width:100%;height:180px;background-color: #CDDBF0;overflow:hidden;">
					<div id="contenedor_formulario_configuracion" >
						<div id="contenedor_tabla_configuracion" style="height:178px;">
							<div class="headTablaBoletas">
								<div class="campo0">&nbsp;</div>
								<div class="campo1">Nit</div>
								<div class="campo2" style="width: 150px;">Cliente</div>
								<div class="campo4" style="width:25px;"><img src="img/buscar20.png" onclick="ventanaBusquedaTerceroFV();" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC"></div>
							</div>
							<div id="bodyTablaConfiguracion" style="height:140px;">

							</div>

						</div>
					</div>
				</div>

				<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRAR POR VENDEDORES(S)</div>

				<!-- VENTANA BUSCAR TERCERO -->
				<div style="display:none;background-color: #F3F3F3;border-right: 1px solid #D4D4D4;border-top: 1px solid #D4D4D4;float: left;height: 26px;width: 35px;border-top-right-radius: 5px;padding: 7 0 0 7;" >
					<img src="img/buscar20.png" onclick="ventanaBusquedaTercero();" style="cursor: pointer;width:21px;height:21px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC5">
				</div>

				<div style="width:100%;height:180px;background-color: #CDDBF0;overflow:hidden;">
					<div id="contenedor_formulario_configuracion" >
						<div id="contenedor_tabla_configuracion" style="height:178px;">
							<div class="headTablaBoletas">
								<div class="campo0">&nbsp;</div>
								<div class="campo1">Identificacion</div>
								<div class="campo2" style="width: 150px;">Vendedor</div>
								<div class="campo4" style="width:25px;"><img src="img/buscar20.png" onclick="ventanaBusquedaTerceroFV(\'vendedores\');" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC"></div>
							</div>
							<div id="bodyTablaConfiguracionVendedores" style="height:140px;">

							</div>

						</div>
					</div>
				</div>

			</div>

			<!-- DIV MENU DERECHO -->
			<div style="float:right; width:310px; background-color: #eff3fa; height: 100%; border-left:1px solid #8DB2E3;">
				<div style="margin-bottom:22px; font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none;border-top:none;" class="x-panel-header">Otras Configuraciones</div>
				<div style="display:table; margin:auto;">
					<div style="overflow:hidden;" id="divFechaInicio">
						<div style="float:left; width:70px; margin-right:5px">Fecha Inicial:</div>
						<div style="float:left; width:120px;"><input type="text" id="MyInformeFiltroFechaInicio"></div>
					</div>
					<div style="overflow:hidden; margin-top:20px;">
						<div style="float:left; width:70px; margin-right:5px">Fecha Final:</div>
						<div style="float:left; width:120px;"><input type="text" id="MyInformeFiltroFechaFinal"></div>
					</div>
					<div style="overflow:hidden; margin-top:20px;">
						<div style="float:left; width:175px; margin-right:5px">Ocultar Informaci&oacute;n de Factura:</div>
						<div style="float:left; width:40px;"><input type="checkbox" id="MyInformeCheckCabecera"></div>
					</div>
					<div style="overflow:hidden; margin-top:20px;">
						<div style="float:left; width:175px; margin-right:5px">Total por Conceptos:</div>
						<div style="float:left; width:40px;"><input type="checkbox" id="MyInformeCheckConceptos"></div>
					</div>
				</div>

				<div style="margin:20px 0 25px 0; font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none;" class="x-panel-header">Conceptos</div>
				<div style="width: calc(100% - 12px);height:180px;background-color: #CDDBF0;overflow:hidden;margin-left: 5px;">
					<div id="contenedor_formulario_configuracion" >
						<div id="contenedor_tabla_configuracion" style="height:178px;">
							<div class="headTablaBoletas">
								<div class="campo0"><img src="img/buscar20.png" onclick="ventanaBusquedaCentroCostosFV();" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC"></div>
								<div class="campo1" style="width: 70px;">Codigo</div>
								<div class="campo2" style="width: 150px;">Nombre</div>
							</div>
							<div id="bodyTablaConfiguracionCentroCostos" style="height:140px;">

							</div>

						</div>
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


			if (typeof(localStorage.sucursal_facturas)!="undefined") {
				if (localStorage.sucursal_facturas!="") {
					setTimeout(function(){ document.getElementById("filtro_sucursal_facturas").value=localStorage.sucursal_facturas; },100);
				}
			}

			if (typeof(localStorage.MyInformeFiltroFechaInicioFacturas)!="undefined") {
				if (localStorage.MyInformeFiltroFechaInicioFacturas!="") {
					document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicioFacturas;
				}
			}

			if (typeof(localStorage.MyInformeFiltroFechaFinalFacturas)!="undefined") {
				if (localStorage.MyInformeFiltroFechaFinalFacturas!="") {
					document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinalFacturas;
				}
			}

			//CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS TERCEROS GUARDADOS
			for ( i = 0; i < arraytercerosFV.length; i++) {
				if (typeof(arraytercerosFV[i])!="undefined" && arraytercerosFV[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_cartera_tercero_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracion").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_cartera_tercero_"+i).innerHTML=tercerosConfiguradosFV[i];

				}
			}
			//CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS VENDEDORES GUARDADOS
			for ( i = 0; i < arrayvendedoresFV.length; i++) {
				if (typeof(arrayvendedoresFV[i])!="undefined" && arrayvendedoresFV[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_empleado_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracionVendedores").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_empleado_"+i).innerHTML=vendedoresConfiguradosFV[i];

				}
			}

			//CREAMOS LOS DIV DE LOS CENTROS DE COSTO AÑADIDOS RECORRIENDO EL ARRAY DE LOS VENDEDORES GUARDADOS
			for ( i = 0; i < arrayCentroCostosFV.length; i++) {
				if (typeof(arrayCentroCostosFV[i])!="undefined" && arrayCentroCostosFV[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_centro_costo_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracionCentroCostos").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_centro_costo_"+i).innerHTML=CentroCostosConfiguradosFV[i];

				}
			}


		</script>';
}


//========================== CUERPO DE LA VENTANA DE CONFIGURACION DEL BALANCE DE COMPROBACION =======================//
function cuerpoVentanaConfiguracionDevolucionVentas(){

	$date = strtotime(date("Y-m-d"));
    $anio = date("Y", $date);
    $mes  = date("m", $date);
    $dia  = date("d",$date);

    //CALCULAR EL FINAL DEL MES
    $fechaInicial = date("Y-m-d",(mktime(0,0,0,$mes,1,$anio)-1));

	echo'<div style="border-top:1px solid #8DB2E3; width:100%; overflow:hidden;" id="informeBalancePrueba">
			<!-- DIV MENU IZQUIERDO -->
			<div style="width: calc(100% - 215px - 3px); padding:0; float:left; height:270px; margin-left:5px;float:left;">

				<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRAR POR CLIENTE(S)</div>

				<!-- VENTANA BUSCAR TERCERO -->
				<div style="display:none;background-color: #F3F3F3;border-right: 1px solid #D4D4D4;border-top: 1px solid #D4D4D4;float: left;height: 26px;width: 35px;border-top-right-radius: 5px;padding: 7 0 0 7;" >
					<img src="img/buscar20.png" onclick="ventanaBusquedaTercero();" style="cursor: pointer;width:21px;height:21px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC5">
				</div>

				<div style="width:100%;height:180px;background-color: #CDDBF0;overflow:hidden;">
					<div id="contenedor_formulario_configuracion" >
						<div id="contenedor_tabla_configuracion" style="height:178px;">
							<div class="headTablaBoletas">
								<div class="campo0">&nbsp;</div>
								<div class="campo1">Nit</div>
								<div class="campo2" style="width: 150px;">Cliente</div>
								<div class="campo4" style="width:25px;"><img src="img/buscar20.png" onclick="ventanaBusquedaTerceroNDV();" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC"></div>
							</div>
							<div id="bodyTablaConfiguracion" style="height:140px;">

							</div>

						</div>
					</div>
				</div>

				<!-- <div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRAR POR VENDEDORES(S)</div>
				 <div style="display:none;background-color: #F3F3F3;border-right: 1px solid #D4D4D4;border-top: 1px solid #D4D4D4;float: left;height: 26px;width: 35px;border-top-right-radius: 5px;padding: 7 0 0 7;" >
					<img src="img/buscar20.png" onclick="ventanaBusquedaTercero();" style="cursor: pointer;width:21px;height:21px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC5">
				</div>

				<div style="width:100%;height:180px;background-color: #CDDBF0;overflow:hidden;">
					<div id="contenedor_formulario_configuracion" >
						<div id="contenedor_tabla_configuracion" style="height:178px;">
							<div class="headTablaBoletas">
								<div class="campo0">&nbsp;</div>
								<div class="campo1">Identificacion</div>
								<div class="campo2" style="width: 150px;">Vendedor</div>
								<div class="campo4" style="width:25px;"><img src="img/buscar20.png" onclick="ventanaBusquedaTerceroNDV(\'vendedores\');" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC"></div>
							</div>
							<div id="bodyTablaConfiguracionVendedores" style="height:140px;">

							</div>

						</div>
					</div>
				</div>-->

			</div>

			<!-- DIV MENU DERECHO -->
			<div style="float:right; width:210px; background-color: #eff3fa; height: 100%; border-left:1px solid #8DB2E3;">
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

				<div style="margin:20px 0px 20px 0px; font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none;" class="x-panel-header">Documento de devolucion</div>
				<div style="overflow:hidden;" id="">
					<div style="display:table; margin:auto;">
						<div style="float:left; width:70px; margin-right:5px">Tipo de Documento</div>
						<div style="float:left; width:120px;">
							<select id="documento_venta">
								<option value="Todos" >Todos</option>
								<option value="Remision" >Remisiones</option>
								<option value="Factura" >Facturas</option>
							</select>
						</div>
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


			if (typeof(localStorage.sucursal_DevolucionVentas)!="undefined") {
				if (localStorage.sucursal_DevolucionVentas!="") {
					setTimeout(function(){ document.getElementById("filtro_sucursal_DevolucionVentas").value=localStorage.sucursal_DevolucionVentas; },100);
				}
			}

			if (typeof(localStorage.MyInformeFiltroFechaInicioDevolucionVentas)!="undefined") {
				if (localStorage.MyInformeFiltroFechaInicioDevolucionVentas!="") {
					document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicioDevolucionVentas;
				}
			}

			if (typeof(localStorage.MyInformeFiltroFechaFinalDevolucionVentas)!="undefined") {
				if (localStorage.MyInformeFiltroFechaFinalDevolucionVentas!="") {
					document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinalDevolucionVentas;
				}
			}

			if (typeof(localStorage.documento_venta)!="undefined") {
				if (localStorage.documento_venta!="") {
					document.getElementById("documento_venta").value=localStorage.documento_venta;
				}
			}



			//CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS TERCEROS GUARDADOS
			for ( i = 0; i < arraytercerosNDV.length; i++) {
				if (typeof(arraytercerosNDV[i])!="undefined" && arraytercerosNDV[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_cartera_tercero_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracion").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_cartera_tercero_"+i).innerHTML=tercerosConfiguradosNDV[i];

				}
			}


		</script>';
}

//========================== CUERPO DE LA VENTANA DE CONFIGURACION DEL BALANCE DE COMPROBACION =======================//
function cuerpoVentanaConfiguracionReciboCaja(){

	$date = strtotime(date("Y-m-d"));
    $anio = date("Y", $date);
    $mes  = date("m", $date);
    $dia  = date("d",$date);

    //CALCULAR EL FINAL DEL MES
    $fechaInicial = date("Y-m-d",(mktime(0,0,0,$mes,1,$anio)-1));

	echo'<div style="border-top:1px solid #8DB2E3; width:100%; overflow:hidden;">
			<!-- DIV CON LOS TERCEROS A AGREGAR -->
			<div style="width: calc(100% - 215px - 3px); padding:0; float:left; height:270px; margin-left:5px;">
				<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRAR POR TERCERO</div>

				<!-- VENTANA BUSCAR TERCERO -->
				<div style="display:none;background-color: #F3F3F3;border-right: 1px solid #D4D4D4;border-top: 1px solid #D4D4D4;float: left;height: 26px;width: 35px;border-top-right-radius: 5px;padding: 7 0 0 7;" >
					<img src="img/buscar20.png" onclick="ventanaBusquedaTercero();" style="cursor: pointer;width:21px;height:21px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC5">
				</div>

				<div style="width:100%;height:90%;background-color: #CDDBF0;overflow:hidden;">
					<div id="contenedor_formulario_configuracion" >
						<div id="contenedor_tabla_configuracion" style="height:240px;">
							<div class="headTablaBoletas">
								<div class="campo0">&nbsp;</div>
								<div class="campo1">Nit</div>
								<div class="campo2" style="width: 150px;">Tercero</div>
								<div class="campo4" style="width:25px;"><img src="img/buscar20.png" onclick="ventanaBusquedaTercero();" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC"></div>
							</div>
							<div id="bodyTablaConfiguracion" style="height:200px;">

							</div>

						</div>
					</div>
				</div>
			</div>

			<!-- DIV MENU DERECHO -->
			<div style="float:right; width:210px; background-color: #eff3fa; height: 100%; border-left:1px solid #8DB2E3;">
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


			if (typeof(localStorage.sucursal_recibo_caja)!="undefined") {
				if (localStorage.sucursal_recibo_caja!="") {
					setTimeout(function(){ document.getElementById("filtro_sucursal_recibo_caja").value=localStorage.sucursal_recibo_caja; },100);
				}
			}

			if (typeof(localStorage.MyInformeFiltroFechaInicioReciboCaja)!="undefined") {
				if (localStorage.MyInformeFiltroFechaInicioReciboCaja!="") {
					document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicioReciboCaja;
				}
			}

			if (typeof(localStorage.MyInformeFiltroFechaFinalReciboCaja)!="undefined") {
				if (localStorage.MyInformeFiltroFechaFinalReciboCaja!="") {
					document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinalReciboCaja;
				}
			}

			//CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS TERCEROS GUARDADOS
			for ( i = 0; i < arraytercerosRC.length; i++) {
				if (typeof(arraytercerosRC[i])!="undefined" && arraytercerosRC[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_cartera_tercero_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracion").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_cartera_tercero_"+i).innerHTML=tercerosConfiguradosRC[i];

				}
			}


		</script>';
}

?>