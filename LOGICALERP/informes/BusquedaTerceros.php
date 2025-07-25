<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");


	$id_empresa = $_SESSION['EMPRESA'];
	// if($idCliente == 0 || $idCliente == '' || !isset($idCliente)){ echo 'No existe un cliente seleccionado'; exit; }
	$server_name = $_SERVER['SERVER_NAME'];


	$contFilaCuenta = 0;
	$whereSum       = ($tabla!='terceros' && $tabla!="empleados")? ',SUM(total_factura_sin_abono) AS saldo ' : '' ;

	if ($tabla=='terceros'){ $nit='numero_identificacion AS nit'; }
	else if ($tabla=='empleados'){ $nit='documento AS nit'; }
	else{ $nit='nit'; }

	$estado         = ($tabla!='terceros'  && $tabla!="empleados")? 'AND estado=1 ' : '' ;
	$limit			= '100';

	$sql            = "SELECT COUNT(id) as cont $whereSum FROM $tabla WHERE activo=1 $estado AND id_empresa='$id_empresa'";
	$query          = mysql_query($sql,$link);
	$rows_registros = mysql_result($query,0,'cont');
	$paginas        = ceil( $rows_registros/$limit );

	//CREAR ARRAY CON LOS LIMITES DE LAS CONSULTAS
	$limit1     = 0;
	$limit2     = $limit;
	$acumScript = '';

	for ($i=1; $i <= $paginas; $i++){
		$acumScript .='arrayLimitGrilla'.$opcGrillaContable.'['.$i.']="'.$limit1.','.$limit2.'";';
		$limit1     =$limit2+1;
		$limit2     =$limit2+$limit;
	}
	$whereCuentas    = "";
    $sqlCuentasPago  = "SELECT cuenta FROM configuracion_cuentas_pago
                        WHERE id_empresa = $id_empresa
                        AND activo = 1
                        AND tipo = 'Compra'
                        AND estado = 'Credito'";
    $queryCuentasPago = mysql_query($sqlCuentasPago,$link);
    while($rowCuenta = mysql_fetch_assoc($queryCuentasPago)){
      $whereCuentas .= " OR A.codigo_cuenta=$rowCuenta[cuenta]";
    }

    $whereCuentas = substr($whereCuentas, 3);
	
	putenv("TZ=America/Bogota");
	$fechaActual = date("Y-m-d");
	$sqlCuentas = "SELECT
						A.id_tercero as $id_tercero,
						A.tercero as $tercero,
						A.nit_tercero as nit,
						SUM( A.haber - A.debe ) AS saldo
                  	FROM asientos_colgaap AS A
                  	INNER JOIN compras_facturas AS CF ON (
                  	  A.id_documento_cruce = CF.id
                  	  AND A.codigo_cuenta = CF.cuenta_pago
                  	)
                  	WHERE A.activo = 1
					AND A.fecha <= '$fechaActual'
                  	AND ($whereCuentas)
                  	AND A.tipo_documento_cruce = 'FC'
                  	AND A.id_empresa = $id_empresa
                  	GROUP BY A.id_tercero
                  	HAVING saldo > 0";

	$queryCuentas = mysql_query($sqlCuentas,$link);

	while ($rowCuentas = mysql_fetch_array($queryCuentas)){
		$contFilaCuenta++;

		$divSaldoPendiente=($tabla!='terceros' && $tabla!="empleados")? '<div class="campo3" id="saldo_'.$contFilaCuenta.'">'.$rowCuentas['saldo'].'</div>' : '' ;

		$filaInsertBoleta .= '<div class="filaBoleta" id="fila_boleta_'.$opcGrillaContable.'_'.$contFilaCuenta.'">

								<div class="campo0 campoInforme0">'.$contFilaCuenta.'</div>
								<div class="campo1 campoInforme1" id="nit_'.$rowCuentas[$id_tercero].'">'.$rowCuentas['nit'].'</div>
								<div class="campo2 campoInforme2" style="border-left:0px;" id="tercero_'.$rowCuentas[$id_tercero].'" title="'.$rowCuentas[$tercero].'">'.$rowCuentas[$tercero].'</div>
								'.$divSaldoPendiente.'
								<div class="campo4 campoInforme4" id="valor_anticipo_'.$opcGrillaContable.'_'.$contFilaCuenta.'">
									<input type="checkbox" id="checkbox_'.$rowCuentas[$id_tercero].'" onchange="checkGrilla(this,\''.$rowCuentas[$id_tercero].'\',\''.$tabla.'\')" value="'.$rowCuentas[$id_tercero].'">
								</div>
							</div>';
	}

	$titulo_identificacion='Nit';

	if ($tabla=='ventas_facturas'){ $titulo_tercero='Cliente'; }
	else if ($tabla=='compras_facturas'){ $titulo_tercero='Proveedor'; }
	else if ($tabla=='empleados'){ $titulo_tercero='Empleado'; $titulo_identificacion='Documento'; }
	else{ $titulo_tercero='Tercero'; }

?>

<style>
	#contenedor_formulario{
		overflow   : hidden;
		width      : calc(100% - 30px);
		height     : calc(100% - 10px);
		margin     : 15px;
		margin-top : 0px;
	}
</style>


<div id="contenedor_formulario">
	<!-- <div class="loadSaveFormulario" id="loadSaveFormulario_<?php echo $opcGrillaContable; ?>"></div> -->
	<div class="toolbar_grilla_manual">
		<div class="div_input_busqueda_grilla_manual">
			<input type="text" id="inputBuscarGrillaManual" onkeyup="inputBuscarGrillaManual(event,this);">
		</div>
		<div class="div_img_actualizar_datos_grilla_manual">
			<img src="../informes/img/reload_grilla.png" onclick="actualizarDatosGrillaManual();">
		</div>

	</div>
	<div id="contenedor_tabla_boletas">
		<div class="headTablaBoletas">
			<div class="campo0 campoInforme0"></div>
			<div class="campo1 campoInforme1"><?php echo $titulo_identificacion; ?></div>
			<div class="campo2 campoInforme2" style="border-left:0px;"><?php echo $titulo_tercero; ?></div>
			<?php
				if ($tabla!='terceros' && $tabla!="empleados"){ echo'<div class="campo3">Saldo Pendiente</div>'; }
			 ?>
			<div class="campo4">Seleccione</div>
		</div>
		<div id="bodyTablaBoletas"><?php echo $filaInsertBoleta; ?></div>
		<div style="float:right; margin:2 20px 0 0;">
			<div style="float:left; margin:2px 5px 0 5px;font-weight:bold;" id="labelPaginacion">Pagina 1 de <?php echo $paginas; ?></div>
			<div class="my_first" onclick="pag_Terceros('first')"></div>
			<div class="my_prev" onclick="pag_Terceros('prev')"></div>
			<div class="my_next" onclick="pag_Terceros('next')"></div>
			<div class="my_last" onclick="pag_Terceros('last')"></div>
		</div>
	</div>
</div>

<script>
	//VARIABLES PARA LA PAGINACION
	arrayLimitGrilla<?php echo $opcGrillaContable; ?> = new Array();
	PaginaActual<?php echo $opcGrillaContable; ?>     = 1;
	MaxPage<?php echo $opcGrillaContable; ?>          = <?php echo $paginas; ?>;
	<?php echo $acumScript; ?>

	//======================// FUNCION CHECKED //======================//
	//*****************************************************************//
	seleccionaCheck();
	function seleccionaCheck(){
		var arrayTemp = new Array();


		switch ("<?php echo $opcGrillaContable ?>") {
			// CONTABILIDAD COLGAAP
			case 'libro_auxiliar': arrayTemp = arraytercerosLA; break;
			case 'balance_comprobacion': arrayTemp = arraytercerosBC; break;
			case 'impuestos_retenciones': arrayTemp = arrayTerceros_CIR; break;
			case 'estado_resultados_ccos': arrayTemp = arraytercerosERC; break;
			case 'report': arrayTemp = arraytercerosERPR; break;
			case 'documentos_auditados': arrayTemp = arrayEmpleadosDA; break;

			// CONTABILIDAD NIIF
			case 'balance_comprobacion_niif': arrayTemp = arraytercerosBCNiif; break;
			case 'libro_auxiliar_niif': arrayTemp = arraytercerosLANIIF; break;

			// VENTAS
			case 'cotizaciones_venta': arrayTemp = ('<?php echo $tabla; ?>'=='empleados')? arrayvendedoresCV: arraytercerosCV; break;
			case 'pedidos_venta': arrayTemp = ('<?php echo $tabla; ?>'=='empleados')? arrayvendedoresPV: arraytercerosPV; break;
			case 'remisiones_venta': arrayTemp = ('<?php echo $tabla; ?>'=='empleados')? arrayvendedoresRV: arraytercerosRV; break;
			case 'facturas': arrayTemp = ('<?php echo $tabla; ?>'=='empleados')? arrayvendedoresFV: arraytercerosFV; break;
			case 'facturas_archivos_adjuntos': arrayTemp = arraytercerosFAA; break;
			case 'cartera_edades': arrayTemp = arrayClientes; break;
			case 'DevolucionVentas': arrayTemp = arraytercerosNDV; break;
			case 'items': arrayTemp = ('<?php echo $tabla; ?>'=='empleados')? array_vendedores_items: array_terceros_items; break;
			case 'itemsRemisionados': arrayTemp = ('<?php echo $tabla; ?>'=='empleados')? array_vendedores_itemsRemisionados: array_terceros_itemsRemisionados; break;
			case 'pendiente_facturar': arrayTemp = arraytercerosPF; break;
			case 'recibo_caja': arrayTemp = arraytercerosRC; break;
			case 'Terceros': arrayTemp = array_funcionarios_Terceros; break;

			// COMPRAS
			case 'ordenes_compra': arrayTemp = ('<?php echo $tabla; ?>'=='empleados')? arrayempleadosOC: arraytercerosOC; break;
			case 'requisiciones_compra': arrayTemp = arraySolicitante; break;
			case 'facturas_compra': arrayTemp = ('<?php echo $tabla; ?>'=='empleados')? arrayvendedoresFC: arraytercerosFC; break;
			case 'DevolucionCompras': arrayTemp = arraytercerosNDC; break;
			case 'facturas_por_pagar': arrayTemp = arrayProveedores; break;
			case 'comprobante_egreso': arrayTemp = arraytercerosCE; break;
			case 'comprobante_egreso_archivos_adjuntos': arrayTemp = arrayTercerosCEAA; break;

			// NOMINA
			case 'nomina': arrayTemp = arrayEmpleados; break;
			case 'vacaciones': arrayTemp = arrayEmpleadosVacaciones; break;
			case 'liquidacion': arrayTemp = arrayEmpleadosLiquidacion; break;
			case 'planilla_ajuste': arrayTemp = arrayEmpleadosPlanillaAjuste; break;

			// GRAFICOS
			case 'facturadGraficos': arrayTemp = arraytercerosFVG; break;

			default: arrayTemp = arrayterceros; break;

		}

		//RECORRER EL ARRAY DE LOS CLIENTES, PARA HACER CHECK A LOS QUE YA ESTAN EN LA GRILLA PRINCIPAL DE CONFIGURACION
		for(i in arrayTemp){
			if(arrayTemp[i] != "" && typeof(arrayTemp[i]) != "undefined"){
				if(document.getElementById('checkbox_' + arrayTemp[i])){
					document.getElementById('checkbox_' + arrayTemp[i]).checked = true;
				}
			}
		}

		// //RECORRER EL ARRAY DE LOS CLIENTES, PARA HACER CHECK A LOS QUE YA ESTAN EN LA GRILLA PRINCIPAL DE CONFIGURACION
		// for ( i =1; i < arrayTemp.length ; i ++){
		//
		// 	if (arrayTemp[i]!="" && typeof(arrayTemp[i])!="undefined"){
		// 		if(document.getElementById('checkbox_'+i)){
		// 			document.getElementById('checkbox_'+i).checked=true;
		// 		}
		// 	}
		// }

	}

	//======================// FUNCION PAGINACION //======================//
	//********************************************************************//
	function pag_Terceros(accion){
		if ('<?php echo $tabla; ?>'=='terceros'){ var nit='numero_identificacion'; }
		else if ('<?php echo $tabla; ?>'=='empleados'){ var nit='documento'; }
		else{ var nit='nit'; }

		var MyParent = 'bodyTablaBoletas';
		var valor    = document.getElementById('inputBuscarGrillaManual').value;
		var filtro   = (valor!='')?'AND ('+nit+' LIKE "%'+valor+'%" OR <?php echo $tercero; ?> LIKE "%'+valor+'%")' : '';
		if('<?php echo $server_name;?>'=='localhost'){var url  = '/ERP/LOGICALERP/informes/informes/contabilidad/bd.php'; }
		else{var url  = '/LOGICALERP/informes/informes/contabilidad/bd.php';}
		if(accion=='first'){
			var pagina = 1;

			if(PaginaActual<?php echo $opcGrillaContable; ?>!=1){
				Ext.get(MyParent).load({
					url		: '',
					scripts	: true,
					nocache	: true,
					params	:
					{
						opc               : 'busquedaTerceroPaginacion',
						limite            : '<?php echo $limit; ?>',
						limit             : arrayLimitGrilla<?php echo $opcGrillaContable; ?>[pagina],
						rows_registros    : '<?php echo $rows_registros; ?>',
						paginas           : '<?php echo $paginas;    ?>',
						id_tercero        : '<?php echo $id_tercero; ?>',
						tercero           : '<?php echo $tercero;    ?>',
						nit               : '<?php echo $nit;        ?>',
						whereSum          : '<?php echo $whereSum;   ?>',
						tabla             : '<?php echo $tabla;      ?>',
						estado            : '<?php echo $estado;     ?>',
						pagina            : pagina,
						imprimeVar        : '',
						opcGrillaContable :'<?php echo $opcGrillaContable; ?>',
						filtro            : filtro
					}
				});
			}
		}

		if(accion=='prev'){
			var pagina = PaginaActual<?php echo $opcGrillaContable; ?>-1;

			if(PaginaActual<?php echo $opcGrillaContable; ?>!=1){
				Ext.get(MyParent).load({
					url		: url,
					scripts	: true,
					nocache	: true,
					params	:
					{
						opc               : 'busquedaTerceroPaginacion',
						limite            : '<?php echo $limit; ?>',
						limit             : arrayLimitGrilla<?php echo $opcGrillaContable; ?>[pagina],
						rows_registros    : '<?php echo $rows_registros; ?>',
						paginas           : '<?php echo $paginas;    ?>',
						id_tercero        : '<?php echo $id_tercero; ?>',
						tercero           : '<?php echo $tercero;    ?>',
						nit               : '<?php echo $nit;        ?>',
						whereSum          : '<?php echo $whereSum;   ?>',
						tabla             : '<?php echo $tabla;      ?>',
						estado            : '<?php echo $estado;     ?>',
						pagina            : pagina,
						imprimeVar        : '',
						opcGrillaContable :'<?php echo $opcGrillaContable; ?>',
						filtro            : filtro
					}
				});
			}
		}
		if(accion=='next'){
			var pagina = PaginaActual<?php echo $opcGrillaContable; ?>+1;

			if(PaginaActual<?php echo $opcGrillaContable; ?>!=MaxPage<?php echo $opcGrillaContable; ?>){
				Ext.get(MyParent).load({
					url		: url,
					scripts	: true,
					nocache	: true,
					params	:
					{
						opc               : 'busquedaTerceroPaginacion',
						limite            : '<?php echo $limit; ?>',
						limit             : arrayLimitGrilla<?php echo $opcGrillaContable; ?>[pagina],
						rows_registros    : '<?php echo $rows_registros; ?>',
						paginas           : '<?php echo $paginas;    ?>',
						id_tercero        : '<?php echo $id_tercero; ?>',
						tercero           : '<?php echo $tercero;    ?>',
						nit               : '<?php echo $nit;        ?>',
						whereSum          : '<?php echo $whereSum;   ?>',
						tabla             : '<?php echo $tabla;      ?>',
						estado            : '<?php echo $estado;     ?>',
						pagina            : pagina,
						imprimeVar        : '',
						opcGrillaContable :'<?php echo $opcGrillaContable; ?>',
						filtro            : filtro
					}
				});
			}
		}

		if(accion=='last'){
			var pagina = MaxPage<?php echo $opcGrillaContable; ?>;

			if(PaginaActual<?php echo $opcGrillaContable; ?>!=MaxPage<?php echo $opcGrillaContable; ?>){
				Ext.get(MyParent).load({
					url		: url,
					scripts	: true,
					nocache	: true,
					params	:
					{
						opc               : 'busquedaTerceroPaginacion',
						limite            : '<?php echo $limit; ?>',
						limit             : arrayLimitGrilla<?php echo $opcGrillaContable; ?>[pagina],
						rows_registros    : '<?php echo $rows_registros; ?>',
						paginas           : '<?php echo $paginas;    ?>',
						id_tercero        : '<?php echo $id_tercero; ?>',
						tercero           : '<?php echo $tercero;    ?>',
						nit               : '<?php echo $nit;        ?>',
						whereSum          : '<?php echo $whereSum;   ?>',
						tabla             : '<?php echo $tabla;      ?>',
						estado            : '<?php echo $estado;     ?>',
						pagina            : pagina,
						imprimeVar        : '',
						opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
						filtro            : filtro
					}
				});
			}
		}
	}

	//======================// UPDATE GRILLA MANUAL //======================//
	//**********************************************************************//
	function actualizarDatosGrillaManual(){

		if ('<?php echo $tabla; ?>'=='terceros'){ var nit='numero_identificacion'; }
		else if ('<?php echo $tabla; ?>'=='empleados'){ var nit='documento'; }
		else{ var nit='nit'; }

		var MyParent = 'bodyTablaBoletas';
		var valor    = document.getElementById('inputBuscarGrillaManual').value;
		var filtro   = (valor!='')?'AND ('+nit+' LIKE "%'+valor+'%" OR <?php echo $tercero; ?> LIKE "%'+valor+'%")' : '';
		if('<?php echo $server_name;?>'=='localhost'){var url  = '/ERP/LOGICALERP/informes/informes/contabilidad/bd.php'; }
		else{var url  = '/LOGICALERP/informes/informes/contabilidad/bd.php';}
		if ('<?php echo $opcGrillaContable; ?>'=='nomina') {
			url = '/LOGICALERP/informes/informes/informes_ventas/bd.php';
		}

		Ext.get(MyParent).load({
			url		: url,
			scripts	: true,
			nocache	: true,
			params	:
			{
				opc               : 'busquedaTerceroPaginacion',
				limite            : '<?php echo $limit; ?>',
				limit             : arrayLimitGrilla<?php echo $opcGrillaContable; ?>[PaginaActual<?php echo $opcGrillaContable; ?>],
				rows_registros    : '<?php echo $rows_registros; ?>',
				paginas           : '<?php echo $paginas;    ?>',
				id_tercero        : '<?php echo $id_tercero; ?>',
				tercero           : '<?php echo $tercero;    ?>',
				nit               : '<?php echo $nit;        ?>',
				whereSum          : '<?php echo $whereSum;   ?>',
				tabla             : '<?php echo $tabla;      ?>',
				estado            : '<?php echo $estado;     ?>',
				pagina            : PaginaActual<?php echo $opcGrillaContable; ?>,
				imprimeVar        : '',
				opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
				filtro            : filtro
			}
		});
	}


	function inputBuscarGrillaManual(event,input){
		var tecla = input ? event.keyCode : event.which
		,   valor = input.value;

	    if (tecla==13){ buscarDatosGrillaManual(valor); }
	}

	//======================// FILTRO GRILLA //======================//
	//***************************************************************//
	function buscarDatosGrillaManual(valor){

		if ('<?php echo $tabla; ?>'=='terceros'){ var nit='numero_identificacion'; }
		else if ('<?php echo $tabla; ?>'=='empleados'){ var nit='documento'; }
		else{ var nit='nit'; }

		// var nit = ('<?php echo $tabla; ?>'!='terceros')? 'nit ' : 'numero_identificacion' ;
		var filtro       = (valor!='')?'AND ('+nit+' LIKE "%'+valor+'%" OR <?php echo $tercero; ?> LIKE "%'+valor+'%")' : '';
		var MyParent     = 'bodyTablaBoletas';
		var limit        = (typeof(arrayLimitGrilla<?php echo $opcGrillaContable; ?>[PaginaActual<?php echo $opcGrillaContable; ?>])!="undefined")? arrayLimitGrilla<?php echo $opcGrillaContable; ?>[PaginaActual<?php echo $opcGrillaContable; ?>] : '0,<?php echo $limit ?>';
		var PaginaActual = (typeof(arrayLimitGrilla<?php echo $opcGrillaContable; ?>[PaginaActual<?php echo $opcGrillaContable; ?>])!="undefined")?PaginaActual<?php echo $opcGrillaContable; ?> : '1' ;
		if('<?php echo $server_name;?>'=='localhost'){var url  = '/ERP/LOGICALERP/informes/informes/contabilidad/bd.php'; }
		else{var url  = '/LOGICALERP/informes/informes/contabilidad/bd.php';}
		Ext.get(MyParent).load({
			url		: url,
			scripts	: true,
			nocache	: true,
			params	:
			{
				opc               : 'busquedaTerceroPaginacion',
				limite            : '<?php echo $limit; ?>',
				limit             : limit,
				rows_registros    : '<?php echo $rows_registros; ?>',
				paginas           : '<?php echo $paginas;    ?>',
				id_tercero        : '<?php echo $id_tercero; ?>',
				tercero           : '<?php echo $tercero;    ?>',
				nit               : '<?php echo $nit;        ?>',
				whereSum          : '<?php echo $whereSum;   ?>',
				tabla             : '<?php echo $tabla;      ?>',
				estado            : '<?php echo $estado;     ?>',
				pagina            : PaginaActual,
				imprimeVar        : '',
				opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
				filtro 			  : filtro
			}
		});
	}

</script>
