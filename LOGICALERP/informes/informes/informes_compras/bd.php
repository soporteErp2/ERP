<?php
include('../../../../configuracion/conectar.php');
include('../../../../configuracion/define_variables.php');

$id_empresa = $_SESSION['EMPRESA'];

switch ($opc) {
	case 'busquedaTerceroPaginacion':
		busquedaTerceroPaginacion($opcGrillaContable,$pagina,$limite,$limit,$rows_registros,$paginas,$id_tercero,$tercero,$nit,$whereSum,$tabla,$estado,$imprimeVar,$filtro,$link,$id_empresa);
		break;
	case 'cuerpoVentanaConfiguracionOrdenesCompra':
		cuerpoVentanaConfiguracionOrdenesCompra();
		break;

	case 'cuerpoVentanaConfiguracionFacturasCompra':
		cuerpoVentanaConfiguracionFacturasCompra();
		break;

	case 'cuerpoVentanaConfiguracionDevolucionCompras':
		cuerpoVentanaConfiguracionDevolucionCompras();
		break;

	case 'cuerpoVentanaConfiguracionComprobanteEgreso':
		cuerpoVentanaConfiguracionComprobanteEgreso();
		break;

	case 'ventanaConfiguracion_FCIR':
		ventanaConfiguracion_FCIR();
		break;
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
									<input type="checkbox" id="checkbox_'.$rowCuentas[$id_tercero].'" onchange="checkGrilla(this,\''.$rowCuentas[$id_tercero].'\')" value="'.$rowCuentas[$id_tercero].'" >
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

//========================== CUERPO DE LA VENTANA DE CONFIGURACION ORDENES DE COMPRA =======================//
function cuerpoVentanaConfiguracionOrdenesCompra(){
	$date = strtotime(date("Y-m-d"));
    $anio = date("Y", $date);
    $mes  = date("m", $date);
    $dia  = date("d",$date);

    //CALCULAR EL FINAL DEL MES
    $fechaInicial = date("Y-m-d",(mktime(0,0,0,$mes,1,$anio)-1));

	echo'<div style="border-top:1px solid #8DB2E3; width:100%; overflow:hidden;" id="informeBalancePrueba">
			<!-- DIV MENU IZQUIERDO -->
			<div style="width: calc(100% - 215px - 3px); padding:0; float:left; height:270px; margin-left:5px;float:left;">

				<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRAR POR PROVEEDOR(S)</div>

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
								<div class="campo2" style="width: 150px;">Proveedor</div>
								<div class="campo4" style="width:25px;"><img src="img/buscar20.png" onclick="ventanaBusquedaTerceroFV();" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC"></div>
							</div>
							<div id="bodyTablaConfiguracion" style="height:140px;">

							</div>

						</div>
					</div>
				</div>

				<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRAR POR USUARIO(S)</div>

				<!-- VENTANA BUSCAR TERCERO -->
				<div style="display:none;background-color: #F3F3F3;border-right: 1px solid #D4D4D4;border-top: 1px solid #D4D4D4;float: left;height: 26px;width: 35px;border-top-right-radius: 5px;padding: 7 0 0 7;" >
					<img src="img/buscar20.png" onclick="ventanaBusquedaTercero();" style="cursor: pointer;width:21px;height:21px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC5">
				</div>

				<div style="width:100%;height:180px;background-color: #CDDBF0;overflow:hidden;">
					<div id="contenedor_formulario_configuracion" >
						<div id="contenedor_tabla_configuracion" style="height:178px;">
							<div class="headTablaBoletas">
								<div class="campo0"><img src="img/buscar20.png" onclick="ventanaBusquedaTerceroFV(\'vendedores\');" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC"></div>
								<div class="campo1">Identificacion</div>
								<div class="campo2" style="width: 150px;">Vendedor</div>
								<div class="campo4" style="width:25px;">&nbsp;</div>
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
				<!-- ESTADO DE LA ORDEN DE COMPRA -->
				<div style="margin-bottom:15px;margin-top:20px; font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none;" class="x-panel-header">Estado de la Orden</div>
				<div style="margin-left:10px; overflow:hidden;">

					<div style="margin-bottom:6px; overflow:hidden;">
						<input type="radio" name="estado_orden" value="todas"  style="float:left; width:30px">
						<div style="float:left;">Todas</div>
					</div>

					<div style="margin-bottom:6px; overflow:hidden;">
						<input type="radio" name="estado_orden" value="pendientes"  style="float:left; width:30px">
						<div style="float:left;">Pendientes por Facturar</div>
					</div>

					<div style="margin-bottom:15px; overflow:hidden;">
						<input type="radio" name="estado_orden" value="facturadas"  style="float:left; width:30px">
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


			if (typeof(localStorage.sucursal_ordenes_compra)!="undefined") {
				if (localStorage.sucursal_ordenes_compra!="") {
					setTimeout(function(){document.getElementById("filtro_sucursal_ordenes_compra").value=localStorage.sucursal_ordenes_compra;},100);
				}
			}

			if (typeof(localStorage.MyInformeFiltroFechaInicioOrdenesCompra)!="undefined") {
				if (localStorage.MyInformeFiltroFechaInicioOrdenesCompra!="") {
					document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicioOrdenesCompra;
				}
			}

			if (typeof(localStorage.MyInformeFiltroFechaFinalOrdenesCompra)!="undefined") {
				if (localStorage.MyInformeFiltroFechaFinalOrdenesCompra!="") {
					document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinalOrdenesCompra;
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

			var elementos = document.getElementsByName("estado_orden");

			if (typeof(localStorage.estado_orden)!="undefined") {
				if (localStorage.estado_orden!="") {
					for(var i=0; i<elementos.length; i++) {
						if (elementos[i].value==localStorage.estado_orden) {elementos[i].checked=true;}
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
			for ( i = 0; i < arraytercerosOC.length; i++) {
				if (typeof(arraytercerosOC[i])!="undefined" && arraytercerosOC[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_cartera_tercero_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracion").appendChild(div);
		    		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_cartera_tercero_"+i).innerHTML=tercerosConfiguradosOC[i];

				}
			}
			//CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS VENDEDORES GUARDADOS
			for ( i = 0; i < arrayvendedoresOC.length; i++) {
				if (typeof(arrayvendedoresOC[i])!="undefined" && arrayvendedoresOC[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_empleado_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracionVendedores").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_empleado_"+i).innerHTML=vendedoresConfiguradosOC[i];

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

//========================== CUERPO DE LA VENTANA DE CONFIGURACION ORDENES DE COMPRA =======================//
function cuerpoVentanaConfiguracionFacturasCompra(){
	$date = strtotime(date("Y-m-d"));
    $anio = date("Y", $date);
    $mes  = date("m", $date);
    $dia  = date("d",$date);

    //CALCULAR EL FINAL DEL MES
    $fechaInicial = date("Y-m-d",(mktime(0,0,0,$mes,1,$anio)-1));
    ?>
	<div style="border-top:1px solid #8DB2E3; width:100%; overflow:hidden;" id="informeBalancePrueba">
			<!-- DIV MENU IZQUIERDO -->
			<div style="width: calc(100% - 320px - 3px); padding:0; float:left; height:270px; margin-left:5px;float:left;">

				<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRAR POR PROVEEDOR(S)</div>

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

				<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRAR POR USUARIO(S)</div>

				<!-- VENTANA BUSCAR TERCERO -->
				<div style="display:none;background-color: #F3F3F3;border-right: 1px solid #D4D4D4;border-top: 1px solid #D4D4D4;float: left;height: 26px;width: 35px;border-top-right-radius: 5px;padding: 7 0 0 7;" >
					<img src="img/buscar20.png" onclick="ventanaBusquedaTercero();" style="cursor: pointer;width:21px;height:21px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC5">
				</div>

				<div style="width:100%;height:180px;background-color: #CDDBF0;overflow:hidden;">
					<div id="contenedor_formulario_configuracion" >
						<div id="contenedor_tabla_configuracion" style="height:178px;">
							<div class="headTablaBoletas">
								<div class="campo0"><img src="img/buscar20.png" onclick="ventanaBusquedaTerceroFV(\'vendedores\');" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC"></div>
								<div class="campo1">Identificacion</div>
								<div class="campo2" style="width: 150px;">Vendedor</div>
								<div class="campo4" style="width:25px;">&nbsp;</div>
							</div>
							<div id="bodyTablaConfiguracionVendedores" style="height:140px;">

							</div>

						</div>
					</div>
				</div>

			</div>

			<!-- DIV MENU DERECHO -->
			<div style="float:right; width:300px; background-color: #eff3fa; height: 100%; border-left:1px solid #8DB2E3;">
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
						<input type="checkbox" id="discriminar_items_facturas_compra"> Discriminar items
					</div>
				</div>

				<div style="margin:25px 0 25px 0; font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none;" class="x-panel-header">Centros de Costo</div>
				<div style="width: calc(100% - 12px);height:180px;background-color: #CDDBF0;overflow:hidden;margin-left: 5px;">
					<div id="contenedor_formulario_configuracion" >
						<div id="contenedor_tabla_configuracion" style="height:178px;">
							<div class="headTablaBoletas">
								<div class="campo0"><img src="img/buscar20.png" onclick="ventanaBusquedaCentroCostosFC();" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC"></div>
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


			if (typeof(localStorage.sucursal_facturas_compra)!="undefined") {
				if (localStorage.sucursal_facturas_compra!="") {
					setTimeout(function(){document.getElementById("filtro_sucursal_facturas_compra").value=localStorage.sucursal_facturas_compra;},100);
				}
			}

			if (typeof(localStorage.MyInformeFiltroFechaInicioFacturasCompra)!="undefined") {
				if (localStorage.MyInformeFiltroFechaInicioFacturasCompra!="") {
					document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicioFacturasCompra;
				}
			}

			if (typeof(localStorage.MyInformeFiltroFechaFinalFacturasCompra)!="undefined") {
				if (localStorage.MyInformeFiltroFechaFinalFacturasCompra!="") {
					document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinalFacturasCompra;
				}
			}

			//CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS TERCEROS GUARDADOS
			for ( i = 0; i < arraytercerosFC.length; i++) {
				if (typeof(arraytercerosFC[i])!="undefined" && arraytercerosFC[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_cartera_tercero_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracion").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_cartera_tercero_"+i).innerHTML=tercerosConfiguradosFC[i];

				}
			}
			//CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS VENDEDORES GUARDADOS
			for ( i = 0; i < arrayvendedoresFC.length; i++) {
				if (typeof(arrayvendedoresFC[i])!="undefined" && arrayvendedoresFC[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_empleado_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracionVendedores").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_empleado_"+i).innerHTML=vendedoresConfiguradosFC[i];

				}
			}

			//CREAMOS LOS DIV DE LOS CENTROS DE COSTO AÑADIDOS RECORRIENDO EL ARRAY DE LOS VENDEDORES GUARDADOS
			for ( i = 0; i < arrayCentroCostosFC.length; i++) {
				if (typeof(arrayCentroCostosFC[i])!="undefined" && arrayCentroCostosFC[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_centro_costo_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracionCentroCostos").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_centro_costo_"+i).innerHTML=CentroCostosConfiguradosFC[i];

				}
			}

			if (typeof(localStorage.discriminar_items_facturas_compra)!="undefined") {
				if (localStorage.discriminar_items_facturas_compra!="") {
					if (localStorage.discriminar_items_facturas_compra=="true") {
						document.getElementById("discriminar_items_facturas_compra").checked = true;
					}
					else{
						document.getElementById("discriminar_items_facturas_compra").checked = false;
					}
				}
			}


		</script>
	<?php
}

//========================== CUERPO DE LA VENTANA DE CONFIGURACION DEL BALANCE DE COMPROBACION =======================//
function cuerpoVentanaConfiguracionDevolucionCompras(){

	$date = strtotime(date("Y-m-d"));
    $anio = date("Y", $date);
    $mes  = date("m", $date);
    $dia  = date("d",$date);

    //CALCULAR EL FINAL DEL MES
    $fechaInicial = date("Y-m-d",(mktime(0,0,0,$mes,1,$anio)-1));

	echo'<div style="border-top:1px solid #8DB2E3; width:100%; overflow:hidden;" id="informeBalancePrueba">
			<!-- DIV MENU IZQUIERDO -->
			<div style="width: calc(100% - 215px - 3px); padding:0; float:left; height:270px; margin-left:5px;float:left;">

				<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRAR POR PROVEEDOR(S)</div>

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
								<div class="campo4" style="width:25px;"><img src="img/buscar20.png" onclick="ventanaBusquedaTerceroNDC();" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC"></div>
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
								<div class="campo4" style="width:25px;"><img src="img/buscar20.png" onclick="ventanaBusquedaTerceroNDC(\'vendedores\');" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC"></div>
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


			if (typeof(localStorage.sucursal_DevolucionCompras)!="undefined") {
				if (localStorage.sucursal_DevolucionCompras!="") {
					setTimeout(function(){document.getElementById("filtro_sucursal_DevolucionCompras").value=localStorage.sucursal_DevolucionCompras;},100);
				}
			}

			if (typeof(localStorage.MyInformeFiltroFechaInicioDevolucionCompras)!="undefined") {
				if (localStorage.MyInformeFiltroFechaInicioDevolucionCompras!="") {
					document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicioDevolucionCompras;
				}
			}

			if (typeof(localStorage.MyInformeFiltroFechaFinalDevolucionCompras)!="undefined") {
				if (localStorage.MyInformeFiltroFechaFinalDevolucionCompras!="") {
					document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinalDevolucionCompras;
				}
			}


			//CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS TERCEROS GUARDADOS
			for ( i = 0; i < arraytercerosNDC.length; i++) {
				if (typeof(arraytercerosNDC[i])!="undefined" && arraytercerosNDC[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_cartera_tercero_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracion").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_cartera_tercero_"+i).innerHTML=tercerosConfiguradosNDC[i];

				}
			}


		</script>';
}

//========================== CUERPO DE LA VENTANA DE CONFIGURACION DEL BALANCE DE COMPROBACION =======================//
function cuerpoVentanaConfiguracionComprobanteEgreso(){

	$date = strtotime(date("Y-m-d"));
    $anio = date("Y", $date);
    $mes  = date("m", $date);
    $dia  = date("d",$date);

    //CALCULAR EL FINAL DEL MES
    $fechaInicial = date("Y-m-d",(mktime(0,0,0,$mes,1,$anio)-1));

	echo'<div style="border-top:1px solid #8DB2E3; width:100%; overflow:hidden;" id="informeBalancePrueba">
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
								<div class="campo4" style="width:25px;"><img src="img/buscar20.png" onclick="ventanaBusquedaTerceroCE();" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC"></div>
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


			if (typeof(localStorage.sucursal_comprobante_egreso)!="undefined") {
				if (localStorage.sucursal_comprobante_egreso!="") {
					setTimeout(function(){document.getElementById("filtro_sucursal_comprobante_egreso").value=localStorage.sucursal_comprobante_egreso;},100);
				}
			}

			if (typeof(localStorage.MyInformeFiltroFechaInicioComprobanteEgreso)!="undefined") {
				if (localStorage.MyInformeFiltroFechaInicioComprobanteEgreso!="") {
					document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicioComprobanteEgreso;
				}
			}

			if (typeof(localStorage.MyInformeFiltroFechaFinalComprobanteEgreso)!="undefined") {
				if (localStorage.MyInformeFiltroFechaFinalComprobanteEgreso!="") {
					document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinalComprobanteEgreso;
				}
			}

			//CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS TERCEROS GUARDADOS
			for ( i = 0; i < arraytercerosCE.length; i++) {
				if (typeof(arraytercerosCE[i])!="undefined" && arraytercerosCE[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_cartera_tercero_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracion").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_cartera_tercero_"+i).innerHTML=tercerosConfiguradosCE[i];

				}
			}


		</script>';
}

function ventanaConfiguracion_FCIR(){

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
								<div class="campo4" style="width:25px;"><img src="img/buscar20.png" onclick="ventanaBusquedaTercero_FCIR();" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC"></div>
							</div>
							<div id="bodyTablaConfiguracion" style="height:140px;"></div>
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
					setTimeout(function(){document.getElementById("filtro_sucursal_facturas").value=localStorage.sucursal_facturas;},100);
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
			for ( i = 0; i < arrayTerceros_FCIR.length; i++) {
				if (typeof(arrayTerceros_FCIR[i])!="undefined" && arrayTerceros_FCIR[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_cartera_tercero_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracion").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_cartera_tercero_"+i).innerHTML=tercerosConfigurados_FCIR[i];

				}
			}


			// ARRAY CCOS
			for ( i = 0; i < arrayCentroCostos_FCIR.length; i++) {
				if (typeof(arrayCentroCostos_FCIR[i])!="undefined" && arrayCentroCostos_FCIR[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_centro_costo_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracionCentroCostos").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_centro_costo_"+i).innerHTML=conceptosConfigurados_FCIR[i];

				}
			}

		</script>';
}


?>