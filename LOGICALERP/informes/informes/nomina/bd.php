<?php
include('../../../../configuracion/conectar.php');
include('../../../../configuracion/define_variables.php');

$id_empresa = $_SESSION['EMPRESA'];

switch ($opc) {

	case 'cuerpoVentanaConfiguracionNomina':
		cuerpoVentanaConfiguracionNomina();
		break;

	case 'cuerpoVentanaConfiguracionLiquidacion':
		cuerpoVentanaConfiguracionLiquidacion();
		break;
	case 'cuerpoVentanaConfiguracionPlanillaAjuste':
		cuerpoVentanaConfiguracionPlanillaAjuste();
		break;
	case 'busquedaTerceroPaginacion':
		busquedaTerceroPaginacion($opcGrillaContable,$pagina,$limite,$limit,$rows_registros,$paginas,$id_tercero,$tercero,$nit,$whereSum,$tabla,$estado,$imprimeVar,$filtro,$link,$id_empresa);
}

//========================== CUERPO DE LA VENTANA DE CONFIGURACION DEL BALANCE DE COMPROBACION =======================//
function cuerpoVentanaConfiguracionNomina(){
	$date = strtotime(date("Y-m-d"));
    $anio = date("Y", $date);
    $mes  = date("m", $date);
    $dia  = date("d",$date);

    //CALCULAR EL FINAL DEL MES
    $fechaInicial = date("Y-m-d",(mktime(0,0,0,$mes,1,$anio)-1));

	echo'<div style="border-top:1px solid #8DB2E3; width:100%; overflow:hidden;" id="informeBalancePrueba">
			<!-- DIV MENU IZQUIERDO -->
			<div style="width: calc(100% - 215px - 3px); padding:0; float:left; height:270px; margin-left:5px;float:left;">

				<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRAR POR EMPLEADO(S)</div>

				<!-- VENTANA BUSCAR TERCERO -->
				<div style="display:none;background-color: #F3F3F3;border-right: 1px solid #D4D4D4;border-top: 1px solid #D4D4D4;float: left;height: 26px;width: 35px;border-top-right-radius: 5px;padding: 7 0 0 7;" >
					<img src="img/buscar20.png" onclick="ventanaBusquedaTercero();" style="cursor: pointer;width:21px;height:21px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC5">
				</div>

				<div style="width:100%;height:180px;background-color: #CDDBF0;overflow:hidden;">
					<div id="contenedor_formulario_configuracion" >
						<div id="contenedor_tabla_configuracion" style="height:178px;">
							<div class="headTablaBoletas">
								<div class="campoInforme0">&nbsp;</div>
								<div class="campoInforme1">Documento</div>
								<div class="campoInforme2" style="width: 150px;">Empleado</div>
								<div class="campoInforme4" style="width:25px;"><img src="img/buscar20.png" onclick="ventanaBusquedaGrillas(\'empleados\');" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC"></div>
							</div>
							<div id="bodyTablaConfiguracion" style="height:140px;">

							</div>

						</div>
					</div>
				</div>

				<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRAR POR CONCEPTO(S)</div>

				<!-- VENTANA BUSCAR TERCERO -->
				<div style="display:none;background-color: #F3F3F3;border-right: 1px solid #D4D4D4;border-top: 1px solid #D4D4D4;float: left;height: 26px;width: 35px;border-top-right-radius: 5px;padding: 7 0 0 7;" >
					<img src="img/buscar20.png" onclick="ventanaBusquedaTercero();" style="cursor: pointer;width:21px;height:21px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC5">
				</div>

				<div style="width:100%;height:180px;background-color: #CDDBF0;overflow:hidden;">
					<div id="contenedor_formulario_configuracion" >
						<div id="contenedor_tabla_configuracion" style="height:178px;">
							<div class="headTablaBoletas">
								<div class="campoInforme0">&nbsp;</div>
								<div class="campoInforme1" style="width:220px;">Concepto</div>
								<div class="campoInforme2" style="width: 30px;" title="Naturaleza del Concepto">Nat.</div>
								<div class="campoInforme4" style="width:25px;"><img src="img/buscar20.png" onclick="ventanaBusquedaGrillas();" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC"></div>
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

				<div style="margin-bottom:25px; margin-top:25px; font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none;" class="x-panel-header">Agrupado por</div>
				<div style="float:left; width:100%; border-right:1px solid #8DB2E3;">
					<div style="margin-left:18px;">
						<input type="radio" name="agrupado" value="empleados" id="agrupado_empleados"  onchange="">
						<label>Empleados</label>
					</div>
					<div style="margin-top:10px;margin-left:18px;">
						<input type="radio" name="agrupado" value="conceptos" id="agrupado_conceptos" onchange="" >
						<label>Conceptos</label>
					</div>
					<div style="margin-top:20px;margin-left:18px;" id="div_discriminar_planillas">
						<input type="checkbox" id="discrimina_planillas" > Discriminar planillas
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


			if (typeof(localStorage.sucursal_nomina)!="undefined") {
				if (localStorage.sucursal_nomina!="") {
					setTimeout(function(){document.getElementById("filtro_sucursal_nomina").value=localStorage.sucursal_nomina;},100);
				}
			}

			if (typeof(localStorage.MyInformeFiltroFechaInicioNomina)!="undefined") {
				if (localStorage.MyInformeFiltroFechaInicioNomina!="") {
					document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicioNomina;
				}
			}

			if (typeof(localStorage.MyInformeFiltroFechaFinalNomina)!="undefined") {
				if (localStorage.MyInformeFiltroFechaFinalNomina!="") {
					document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinalNomina;
				}
			}

			if (typeof(localStorage.agrupacion_nomina)!="undefined") {
				if (localStorage.agrupacion_nomina!="") {
					if (localStorage.agrupacion_nomina=="empleados") {
						document.getElementById("agrupado_empleados").checked = true;
					}
					else if (localStorage.agrupacion_nomina=="conceptos") {
						document.getElementById("agrupado_conceptos").checked = true;
					}

				}
				else{
					document.getElementById("agrupado_empleados").checked = true;
				}
			}
			else{
				document.getElementById("agrupado_empleados").checked = true;
			}

			if (typeof(localStorage.discrimina_planillas)!="undefined") {
				if (localStorage.discrimina_planillas!="") {
					var check = (localStorage.discrimina_planillas=="true")? true : false ;
					document.getElementById("discrimina_planillas").checked=check;
				}
			}


			//CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS TERCEROS GUARDADOS
			for ( i = 0; i < arrayEmpleados.length; i++) {
				if (typeof(arrayEmpleados[i])!="undefined" && arrayEmpleados[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_empleado_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracion").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_empleado_"+i).innerHTML=arrayEmpleadosNomina[i];

				}
			}
			//CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS VENDEDORES GUARDADOS
			for ( i = 0; i < arrayConceptos.length; i++) {
				if (typeof(arrayConceptos[i])!="undefined" && arrayConceptos[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_concepto_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracionVendedores").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_concepto_"+i).innerHTML=arrayConceptosNomina[i];

				}
			}


		</script>';
}

//========================== CUERPO DE LA VENTANA DE CONFIGURACION DEL BALANCE DE COMPROBACION =======================//
function cuerpoVentanaConfiguracionLiquidacion(){
	$date = strtotime(date("Y-m-d"));
    $anio = date("Y", $date);
    $mes  = date("m", $date);
    $dia  = date("d",$date);

    //CALCULAR EL FINAL DEL MES
    $fechaInicial = date("Y-m-d",(mktime(0,0,0,$mes,1,$anio)-1));

	echo'<div style="border-top:1px solid #8DB2E3; width:100%; overflow:hidden;" id="informeBalancePrueba">
			<!-- DIV MENU IZQUIERDO -->
			<div style="width: calc(100% - 215px - 3px); padding:0; float:left; height:270px; margin-left:5px;float:left;">

				<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRAR POR EMPLEADO(S)</div>

				<!-- VENTANA BUSCAR TERCERO -->
				<div style="display:none;background-color: #F3F3F3;border-right: 1px solid #D4D4D4;border-top: 1px solid #D4D4D4;float: left;height: 26px;width: 35px;border-top-right-radius: 5px;padding: 7 0 0 7;" >
					<img src="img/buscar20.png" onclick="ventanaBusquedaTercero();" style="cursor: pointer;width:21px;height:21px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC5">
				</div>

				<div style="width:100%;height:180px;background-color: #CDDBF0;overflow:hidden;">
					<div id="contenedor_formulario_configuracion" >
						<div id="contenedor_tabla_configuracion" style="height:178px;">
							<div class="headTablaBoletas">
								<div class="campoInforme0">&nbsp;</div>
								<div class="campoInforme1">Documento</div>
								<div class="campoInforme2" style="width: 150px;">Empleado</div>
								<div class="campoInforme4" style="width:25px;"><img src="img/buscar20.png" onclick="ventanaBusquedaGrillas(\'empleados\');" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC"></div>
							</div>
							<div id="bodyTablaConfiguracion" style="height:140px;">

							</div>

						</div>
					</div>
				</div>

				<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRAR POR CONCEPTO(S)</div>

				<!-- VENTANA BUSCAR TERCERO -->
				<div style="display:none;background-color: #F3F3F3;border-right: 1px solid #D4D4D4;border-top: 1px solid #D4D4D4;float: left;height: 26px;width: 35px;border-top-right-radius: 5px;padding: 7 0 0 7;" >
					<img src="img/buscar20.png" onclick="ventanaBusquedaTercero();" style="cursor: pointer;width:21px;height:21px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC5">
				</div>

				<div style="width:100%;height:180px;background-color: #CDDBF0;overflow:hidden;">
					<div id="contenedor_formulario_configuracion" >
						<div id="contenedor_tabla_configuracion" style="height:178px;">
							<div class="headTablaBoletas">
								<div class="campoInforme0">&nbsp;</div>
								<div class="campoInforme1" style="width:220px;">Concepto</div>
								<div class="campoInforme2" style="width: 30px;" title="Naturaleza del Concepto">Nat.</div>
								<div class="campoInforme4" style="width:25px;"><img src="img/buscar20.png" onclick="ventanaBusquedaGrillas();" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC"></div>
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

				<div style="margin-bottom:25px; margin-top:25px; font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none;" class="x-panel-header">Agrupado por</div>
				<div style="float:left; width:100%; border-right:1px solid #8DB2E3;">
					<div style="margin-left:18px;">
						<input type="radio" name="agrupado" value="empleados" id="agrupado_empleados"  onchange="">
						<label>Empleados</label>
					</div>
					<div style="margin-top:10px;margin-left:18px;">
						<input type="radio" name="agrupado" value="conceptos" id="agrupado_conceptos" onchange="" >
						<label>Conceptos</label>
					</div>
					<div style="margin-top:20px;margin-left:18px;" id="div_discriminar_planillas">
						<input type="checkbox" id="discrimina_planillas_liquidacion" > Discriminar planillas
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


			if (typeof(localStorage.sucursal_liquidacion)!="undefined") {
				if (localStorage.sucursal_liquidacion!="") {
					setTimeout(function(){document.getElementById("filtro_sucursal_liquidacion").value=localStorage.sucursal_liquidacion;},100);
				}
			}

			if (typeof(localStorage.MyInformeFiltroFechaInicioLiquidacion)!="undefined") {
				if (localStorage.MyInformeFiltroFechaInicioLiquidacion!="") {
					document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicioLiquidacion;
				}
			}

			if (typeof(localStorage.MyInformeFiltroFechaFinalLiquidacion)!="undefined") {
				if (localStorage.MyInformeFiltroFechaFinalLiquidacion!="") {
					document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinalLiquidacion;
				}
			}

			if (typeof(localStorage.agrupacion_liquidacion)!="undefined") {
				if (localStorage.agrupacion_liquidacion!="") {
					if (localStorage.agrupacion_liquidacion=="empleados") {
						document.getElementById("agrupado_empleados").checked = true;
					}
					else if (localStorage.agrupacion_liquidacion=="conceptos") {
						document.getElementById("agrupado_conceptos").checked = true;
					}

				}
				else{
					document.getElementById("agrupado_empleados").checked = true;
				}
			}
			else{
				document.getElementById("agrupado_empleados").checked = true;
			}

			if (typeof(localStorage.discrimina_planillas_liquidacion)!="undefined") {
				if (localStorage.discrimina_planillas_liquidacion!="") {
					var check = (localStorage.discrimina_planillas_liquidacion=="true")? true : false ;
					document.getElementById("discrimina_planillas_liquidacion").checked=check;
				}
			}


			//CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS TERCEROS GUARDADOS
			for ( i = 0; i < arrayEmpleadosLiquidacion.length; i++) {
				if (typeof(arrayEmpleadosLiquidacion[i])!="undefined" && arrayEmpleadosLiquidacion[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_empleado_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracion").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_empleado_"+i).innerHTML=arrayEmpleadosConfiguradosLiquidacion[i];

				}
			}
			//CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS VENDEDORES GUARDADOS
			for ( i = 0; i < arrayConceptosLiquidacion.length; i++) {
				if (typeof(arrayConceptosLiquidacion[i])!="undefined" && arrayConceptosLiquidacion[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_concepto_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracionVendedores").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_concepto_"+i).innerHTML=arrayConceptosConfiguradosLiquidacion[i];

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

	$sqlCuentas   = "SELECT id,descripcion,naturaleza FROM nomina_conceptos WHERE activo=1 AND id_empresa = '$id_empresa' $filtro LIMIT $limit";
	$queryCuentas = mysql_query($sqlCuentas,$link);
	while ($row = mysql_fetch_array($queryCuentas)) {
		$contFilaCuenta++;

		$divSaldoPendiente=($tabla!='terceros')? '<div class="campo3" id="saldo_'.$contFilaCuenta.'">'.$row['saldo'].'</div>' : '' ;

		$filaInsertBoleta .= '<div class="filaBoleta" id="fila_concepto_'.$opcGrillaContable.'_'.$contFilaCuenta.'">

								<div class="campoInforme0">'.$contFilaCuenta.'</div>
								<div class="campoInforme1" style="width:200px;" id="descripcion_concepto_'.$contFilaCuenta.'">'.$row['descripcion'].'</div>
								<div class="campoInforme2" style="border-left:0px;width:100px;" id="naturaleza_concepto_'.$contFilaCuenta.'" title="'.$row['naturaleza'].'">'.$row['naturaleza'].'</div>
								<div class="campoInforme4" id="valor_anticipo_'.$opcGrillaContable.'_'.$contFilaCuenta.'">
									<input type="checkbox" id="checkbox_'.$row[$id_tercero].'" onchange="checkGrillaConceptos(this,\''.$contFilaCuenta.'\')" value="'.$row[$id_tercero].'">
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

//========================== CUERPO DE LA VENTANA DE CONFIGURACION DE LA PLANILLA DE AJUSTE =======================//
function cuerpoVentanaConfiguracionPlanillaAjuste(){
	$date = strtotime(date("Y-m-d"));
    $anio = date("Y", $date);
    $mes  = date("m", $date);
    $dia  = date("d",$date);

    //CALCULAR EL FINAL DEL MES
    $fechaInicial = date("Y-m-d",(mktime(0,0,0,$mes,1,$anio)-1));

	echo'<div style="border-top:1px solid #8DB2E3; width:100%; overflow:hidden;" id="informeBalancePrueba">
			<!-- DIV MENU IZQUIERDO -->
			<div style="width: calc(100% - 215px - 3px); padding:0; float:left; height:270px; margin-left:5px;float:left;">

				<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRAR POR EMPLEADO(S)</div>

				<!-- VENTANA BUSCAR TERCERO -->
				<div style="display:none;background-color: #F3F3F3;border-right: 1px solid #D4D4D4;border-top: 1px solid #D4D4D4;float: left;height: 26px;width: 35px;border-top-right-radius: 5px;padding: 7 0 0 7;" >
					<img src="img/buscar20.png" onclick="ventanaBusquedaTercero();" style="cursor: pointer;width:21px;height:21px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC5">
				</div>

				<div style="width:100%;height:180px;background-color: #CDDBF0;overflow:hidden;">
					<div id="contenedor_formulario_configuracion" >
						<div id="contenedor_tabla_configuracion" style="height:178px;">
							<div class="headTablaBoletas">
								<div class="campoInforme0">&nbsp;</div>
								<div class="campoInforme1">Documento</div>
								<div class="campoInforme2" style="width: 150px;">Empleado</div>
								<div class="campoInforme4" style="width:25px;"><img src="img/buscar20.png" onclick="ventanaBusquedaGrillas(\'empleados\');" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC"></div>
							</div>
							<div id="bodyTablaConfiguracion" style="height:140px;">

							</div>

						</div>
					</div>
				</div>

				<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRAR POR CONCEPTO(S)</div>

				<!-- VENTANA BUSCAR TERCERO -->
				<div style="display:none;background-color: #F3F3F3;border-right: 1px solid #D4D4D4;border-top: 1px solid #D4D4D4;float: left;height: 26px;width: 35px;border-top-right-radius: 5px;padding: 7 0 0 7;" >
					<img src="img/buscar20.png" onclick="ventanaBusquedaTercero();" style="cursor: pointer;width:21px;height:21px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC5">
				</div>

				<div style="width:100%;height:180px;background-color: #CDDBF0;overflow:hidden;">
					<div id="contenedor_formulario_configuracion" >
						<div id="contenedor_tabla_configuracion" style="height:178px;">
							<div class="headTablaBoletas">
								<div class="campoInforme0">&nbsp;</div>
								<div class="campoInforme1" style="width:220px;">Concepto</div>
								<div class="campoInforme2" style="width: 30px;" title="Naturaleza del Concepto">Nat.</div>
								<div class="campoInforme4" style="width:25px;"><img src="img/buscar20.png" onclick="ventanaBusquedaGrillas();" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC"></div>
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

				<div style="margin-bottom:25px; margin-top:25px; font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none;" class="x-panel-header">Agrupado por</div>
				<div style="float:left; width:100%; border-right:1px solid #8DB2E3;">
					<div style="margin-left:18px;">
						<input type="radio" name="agrupado" value="empleados" id="agrupado_empleados"  onchange="">
						<label>Empleados</label>
					</div>
					<div style="margin-top:10px;margin-left:18px;">
						<input type="radio" name="agrupado" value="conceptos" id="agrupado_conceptos" onchange="" >
						<label>Conceptos</label>
					</div>
					<div style="margin-top:20px;margin-left:18px;" id="div_discriminar_planillas">
						<input type="checkbox" id="discrimina_planillas_PlanillaAjuste" > Discriminar planillas
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


			if (typeof(localStorage.sucursal_PlanillaAjuste)!="undefined") {
				if (localStorage.sucursal_PlanillaAjuste!="") {
					if(document.getElementById("filtro_sucursal_PlanillaAjuste")) {document.getElementById("filtro_sucursal_PlanillaAjuste").value=localStorage.sucursal_PlanillaAjuste;}
				}
			}

			if (typeof(localStorage.MyInformeFiltroFechaInicioPlanillaAjuste)!="undefined") {
				if (localStorage.MyInformeFiltroFechaInicioPlanillaAjuste!="") {
					document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicioPlanillaAjuste;
				}
			}

			if (typeof(localStorage.MyInformeFiltroFechaFinalPlanillaAjuste)!="undefined") {
				if (localStorage.MyInformeFiltroFechaFinalPlanillaAjuste!="") {
					document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinalPlanillaAjuste;
				}
			}

			if (typeof(localStorage.agrupacion_PlanillaAjuste)!="undefined") {
				if (localStorage.agrupacion_PlanillaAjuste!="") {
					if (localStorage.agrupacion_PlanillaAjuste=="empleados") {
						document.getElementById("agrupado_empleados").checked = true;
					}
					else if (localStorage.agrupacion_PlanillaAjuste=="conceptos") {
						document.getElementById("agrupado_conceptos").checked = true;
					}

				}
				else{
					document.getElementById("agrupado_empleados").checked = true;
				}
			}
			else{
				document.getElementById("agrupado_empleados").checked = true;
			}

			if (typeof(localStorage.discrimina_planillas_PlanillaAjuste)!="undefined") {
				if (localStorage.discrimina_planillas_PlanillaAjuste!="") {
					var check = (localStorage.discrimina_planillas_PlanillaAjuste=="true")? true : false ;
					document.getElementById("discrimina_planillas_PlanillaAjuste").checked=check;
				}
			}


			//CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS TERCEROS GUARDADOS
			for ( i = 0; i < arrayEmpleadosPlanillaAjuste.length; i++) {
				if (typeof(arrayEmpleadosPlanillaAjuste[i])!="undefined" && arrayEmpleadosPlanillaAjuste[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_empleado_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracion").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_empleado_"+i).innerHTML=arrayEmpleadosConfiguradosPlanillaAjuste[i];

				}
			}
			//CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS VENDEDORES GUARDADOS
			for ( i = 0; i < arrayConceptosPlanillaAjuste.length; i++) {
				if (typeof(arrayConceptosPlanillaAjuste[i])!="undefined" && arrayConceptosPlanillaAjuste[i]!="") {

					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_concepto_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracionVendedores").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_concepto_"+i).innerHTML=arrayConceptosNomina[i];

				}
			}


		</script>';
}

?>