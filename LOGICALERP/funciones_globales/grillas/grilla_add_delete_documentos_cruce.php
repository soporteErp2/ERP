<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$contFilaGrilla = 0;
	$nameTabla      = "";
	$id_empresa     = $_SESSION['EMPRESA'];
	$titulo_tercero = 'Tercero';
	// if($idCliente == 0 || $idCliente == '' || !isset($idCliente)){ echo 'No existe un cliente seleccionado'; exit; }


	if($typeDocCruce == "FV"){
		$nombreTablaGrilla  = "ventas_facturas";
		$nombreTablaCuentas = "recibo_caja_cuentas";
		$campoTablaCuentas  = "id_recibo_caja";
		$titulo_tercero     = "Cliente";
	}
	else if($typeDocCruce == "FC"){
		$nombreTablaGrilla  = "compras_facturas";
		$nombreTablaCuentas = "comprobante_egreso_cuentas";
		$campoTablaCuentas  = "id_comprobante_egreso";
		$titulo_tercero     = "Proveedor";
	}

	//CONSULTAR LOS DOCUMENTOS CRUSADOS
	$whereDocsCruce = "";
	$sql   = "SELECT id_documento_cruce,tipo_documento_cruce
				FROM $nombreTablaCuentas
				WHERE activo=1 AND id_documento_cruce>0 AND $campoTablaCuentas='$id_documento' AND tipo_documento_cruce='$typeDocCruce'";
	$query = mysql_query($sql,$link);
	while ($row=mysql_fetch_array($query)) { $whereDocsCruce .= "AND id <> '".$row['id_documento_cruce']."' "; }

	if ($opc == 'busquedaPaginacion') {
		busquedaPaginacion($id_documento,$modulo,$opcGrilla,$pagina,$limite,$limit,$rows_registros,$paginas,$imprimeVar,$filtroGrilla,$link,$id_empresa);
		exit;
	}

	$limit          = '100';
	$sqlCont        = "SELECT COUNT(id) as cont FROM $nombreTablaGrilla WHERE activo=1 AND id_empresa='$id_empresa' AND total_factura_sin_abono>0 $whereDocsCruce";
	$queryCont      = mysql_query($sqlCont,$link);
	$rows_registros = mysql_result($queryCont,0,'cont');
	$paginas        = ceil( $rows_registros/$limit );

	//CREAR ARRAY CON LOS LIMITES DE LAS CONSULTAS
	$limit1     = 0;
	$limit2     = $limit;
	$acumScript = '';

	for ($i=1; $i <= $paginas; $i++) {
		$acumScript.='arrayLimitGrilla'.$opcGrilla.'['.$i.']="'.$limit1.','.$limit2.'";';
		$limit1     =$limit2+1;
		$limit2     =$limit2+$limit;
	}

 	$sql   = "SELECT id,prefijo,numero_factura,id_cliente,cliente AS tercero,total_factura_sin_abono,fecha_inicio,fecha_vencimiento,cuenta_pago, if(fecha_vencimiento < NOW(), '#b70c00', '#000') AS font_color
			FROM $nombreTablaGrilla
			WHERE (activo=1 OR activo=2 OR activo=4) AND id_empresa='$id_empresa' AND total_factura_sin_abono>0 $whereDocsCruce ORDER BY fecha_vencimiento ASC LIMIT $limit";
	$query = mysql_query($sql,$link);

	while ($row = mysql_fetch_array($query)) {
		$contFilaGrilla++;
		$checked = ($arrayCheckGrilla[$row['id']]=='checked')? 'checked' : '';
		$filaInsertGrilla .= '<div class="filaBoleta" id="filaBoleta'.$opcGrilla.'_'.$contFilaGrilla.'" style="color:'.$row['font_color'].'">
									<div class="campo0" id="id_cont'.$opcGrilla.'_'.$row['id'].'">'.$contFilaGrilla.'</div>
									<div class="campo1" style="width:50px;" id="prefijoFactura'.$opcGrilla.'_'.$row['id'].'">'.$row['prefijo'].'</div>
									<div class="campo1" style="width:70px;" id="numeroFactura'.$opcGrilla.'_'.$row['id'].'">'.$row['numero_factura'].'</div>
									<div class="campo1" style="width:170px;" title="'.$row['tercero'].'">'.$row['tercero'].'</div>
									<div class="campo1" style="width:70px; text-align:right; padding-right:5px;" id="abono'.$opcGrilla.'_'.$row['id'].'" title="'.$row['total_factura_sin_abono'].'">'.$row['total_factura_sin_abono'].'</div>
									<div class="campo1" id="fechaInicio'.$opcGrilla.'_'.$row['id'].'">'.$row['fecha_inicio'].'</div>
									<div class="campo1" id="fechaFin'.$opcGrilla.'_'.$row['id'].'">'.$row['fecha_vencimiento'].'</div>
								</div>';

	}

	$titulo_identificacion='Nit';

	//========================================================== PAGINACION Y BUSQUEDA ===============================================================//
	//************************************************************************************************************************************************//
	function busquedaPaginacion($id_documento,$modulo,$opcGrilla,$pagina,$limite,$limit,$rows_registros,$paginas,$imprimeVar,$filtroGrilla,$link,$id_empresa){

		//SI LA VARIABLE FILTRO NO ESTA VACIA, RECONTAMOS EL LIMITE DE LOS REGISTROS
		$whereFiltro = "";
		if ($filtroGrilla!='') {
			$whereFiltro    = "AND (retencion LIKE '%$filtroGrilla%' OR tipo_retencion LIKE '%$filtroGrilla%' OR valor LIKE '%$filtroGrilla%' OR base LIKE '%$filtroGrilla%' OR departamento LIKE '%$filtroGrilla%' OR ciudad LIKE '%$filtroGrilla%')" : "";
		}

		$sql            = "SELECT COUNT(id) as cont FROM $nombreTablaGrilla WHERE activo=1 $whereFiltro AND id_empresa='$id_empresa' AND total_factura_sin_abono>0 $whereDocsCruce";
		$query          = mysql_query($sql,$link);
		$rows_registros = mysql_result($query,0,'cont');
		$paginas        = ceil( $rows_registros/$limite );

		//CREAR ARRAY CON LOS LIMITES DE LAS CONSULTAS
		$limit1     = 0;
		$limit2     = $limite;
		$acumScript = '';
		for ($i=1; $i <= $paginas; $i++) {
			$acumScript .='arrayLimitGrilla'.$opcGrilla.'['.$i.']="'.$limit1.','.$limit2.'";';
			$limit1     =$limit2+1;
			$limit2     =$limit2+$limite;
		}

		//SI SE BUSCA DESDE UNA PAGINA DIFERENTE A LA 1, VALIDAR SI EL RESULTADO DA LA MISMA CANTIDAD DE PAGINAS, SINO, PONER EN PAGINA 1 EJ(9 PAGINAS CONTRA EL RESULTADO DE 1 PAGINA)
		if ($pagina>$paginas) {
			$limit  = '0,'.$limite;
			$pagina = 1;
		}

		$sqlCuentas   = "SELECT id,retencion,tipo_retencion,valor,base,cuenta,departamento,ciudad FROM retenciones WHERE activo=1 $filtroGrilla AND id_empresa = '$id_empresa' AND modulo='$modulo'  LIMIT $limit";
		$queryCuentas = mysql_query($sqlCuentas,$link);

		while ($rowCuentas = mysql_fetch_array($queryCuentas)) {
			$contFilaGrilla++;

			$filaInsertGrilla .= '<div class="filaBoleta" id="fila_boleta_'.$opcGrilla.'_'.$contFilaGrilla.'">

									<div class="campo0">'.$contFilaGrilla.'</div>
									<div class="campo1"  id="retencion_'.$rowCuentas['retencion'].'" title="'.$rowCuentas['retencion'].'">'.$rowCuentas['retencion'].'</div>
									<div class="campo1" style="width:50px" id="valor_'.$rowCuentas['valor'].'">'.$rowCuentas['valor'].'</div>
									<div class="campo1" style="width:70px" id="base_'.$rowCuentas['base'].'">'.$rowCuentas['base'].'</div>
									<div class="campo1" id="departamento_'.$rowCuentas['departamento'].'" title="'.$rowCuentas['departamento'].'">'.$rowCuentas['departamento'].'</div>
									<div class="campo1" id="ciudad_'.$rowCuentas['ciudad'].'" title="'.$rowCuentas['ciudad'].'">'.$rowCuentas['ciudad'].'</div>
									<div class="campo4" id="valor_anticipo_'.$opcGrilla.'_'.$contFilaGrilla.'">
										<input type="checkbox" id="checkbox_'.$rowCuentas['id'].'" onchange="checkGrilla(this,\''.$rowCuentas['id'].'\',\''.$rowCuentas['retencion'].'\',\''.$rowCuentas['valor'].'\',\''.$rowCuentas['tipo_retencion'].'\',\''.$rowCuentas['base'].'\',\''.$rowCuentas['cuenta'].'\')" value="'.$rowCuentas['id'].'">
									</div>
								</div>';
		}

		$filaInsertGrilla .= '<script>
								// console.log(arrayLimitGrilla'.$opcGrilla.');
								document.getElementById("labelPaginacion").innerHTML="Pagina '.$pagina.' de '.$paginas.' ";
								PaginaActual'.$opcGrilla.'='.$pagina.';
								MaxPage'.$opcGrilla.'='.$paginas.';
								arrayLimitGrilla'.$opcGrilla.'.length=0;
								'.$acumScript.'
								// console.log(arrayLimitGrilla'.$opcGrilla.');
								// console.log("'.$limit.'");
								'.$imprimeVar.'
							</script>';

		echo $filaInsertGrilla;
	}



?>

<style>
	#contenedor_formulario{
		overflow   : hidden;
		width      : calc(100% - 30px);
		height     : calc(100% - 10px);
		margin     : 15px;
		margin-top : 0px;
	}

	/*TOOLBAR DE LA BUSQUEDA DE LA GRILLA MANUAL*/
	.toolbar_grilla_manual{
		overflow   : hidden;
		width      : 100%;
		height     : 34px;
		margin-top : 5px;
	}
	.div_input_busqueda_grilla_manual{
		width                  : 170px;
		background-color       : #FFF;
		height                 : 28px;
		padding-top            : 5px;
		padding                : 5 0 0 6;
		background-color       : #F3F3F3;
		border-top-left-radius : 5;
		border                 : 1px solid #D4D4D4;
		float                  : left;
	}

	.div_input_busqueda_grilla_manual>input{
		background-image  : url(../../temas/clasico/images/BotonesTabs/buscar16.png);
		background-repeat : no-repeat;
		padding           : 0 0 0 27px;
		border-radius     : 3px;
		font-size         : 12px;
		min-height        : 22px;
		border: 1px solid #D4D4D4;
	}

	.div_img_actualizar_datos_grilla_manual{
		float                   : left;
		width                   : 35px;
		height                  : 40px;
		background-color        : #F3F3F3;
		border-top-right-radius : 5;
		border-top              : 1px solid #D4D4D4;
		border-right            : 1px solid #d4d4d4;
	}

	.div_img_actualizar_datos_grilla_manual>img{
		padding: 7 0 0 7;
		cursor: pointer;
	}

	#contenedor_tabla_boletas{
		overflow              : hidden;
		width                 : calc(100% - 2px);
		height                : calc(100% - 41px);
		/*border              : 1px solid #d4d4d4;*/
		border                : 1px solid #D4D4D4 	;
		border-radius         : 4px;
		-webkit-border-radius : 4px;
		-webkit-box-shadow    : 1px 1px 1px #d4d4d4;
		-moz-box-shadow       : 1px 1px 1px #d4d4d4;
		box-shadow            : 1px 1px 1px #d4d4d4;
		background-color      :#F3F3F3;
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
		overflow      : hidden;
		font-weight   : bold;
		width         : 100%;
		border-bottom : 1px solid #d4d4d4;
		height        : 22px;
	}

	.headTablaBoletas div{
		background-color :#F3F3F3;
		height           : 22px;
		padding-top      : 3;
	}

	.bodyGrilla{
		overflow-x       : hidden;
		overflow-y       : auto;
		width            : 100%;
		height           : calc(100% - 46px);
		background-color : #FFF;
		border-bottom    : 1px solid #d4d4d4;
	}

	.bodyGrilla > div{
		overflow      : hidden;
		height        : 22px;
		border-bottom : 1px solid #d4d4d4;
	}

	.bodyGrilla > div > div { height: 18px; /*background-color : #FFF;*/ padding-top: 4px; }
	.bodyGrilla >  div:hover {background-color: #E3EBFC;}

	.filaBoleta{ /*background-color:#F3F3F3;*/ cursor: hand; }

	.filaBoleta input[type=text]{
		border:0px;
		width: 90%;
		height: 100%;
	}

	.filaBoleta input[type=text]:focus { background: #FFF; }

	.campo0{
		float            : left;
		width            : 28px;
		text-indent      : 5px;
		border-right     : 1px solid #d4d4d4;
		background-color:#F3F3F3;
	}

	.campo1{
		float            : left;
		width            : 100px;
		text-indent      : 5px;
		/*background-color : #FFF;*/
		border-right: 1px solid #d4d4d4;
		white-space:nowrap;
		text-overflow: ellipsis;
		overflow:hidden;
	}

	.campo2{
		float            : left;
		width            : 280px;
		text-indent      : 5px;
		overflow         : hidden;
		white-space      : nowrap;
		text-overflow    : ellipsis;
		/*border-left      : 1px solid #d4d4d4;*/
		border-right     : 1px solid #d4d4d4;
		/*background-color : #FFF;*/
	}

	.campo3{
		float            : left;
		width            : 100px;
		text-indent      : 5px;
		text-align       : right;
		padding-right    : 3px;
		/*background-color : #FFF;*/
		border-right     : 1px solid #d4d4d4;
	}

	.campo4{
		float            : left;
		width            : 70px;
		text-indent      : 5px;
		text-align       : center;
		/*background-color : #FFF;*/
		/*border-left      : 1px solid #d4d4d4;*/
		border-right     : 1px solid #d4d4d4;
	}

</style>


<div id="contenedor_formulario">
	<!-- <div class="loadSaveFormulario" id="loadSaveFormulario_<?php echo $opcGrilla; ?>"></div> -->
	<div class="toolbar_grilla_manual">
		<div class="div_input_busqueda_grilla_manual">
			<input type="text" id="inputBuscarGrillaManual_<?php echo $opcGrilla; ?>" onchange="buscarDatosGrillaManual_<?php echo $opcGrilla; ?>(this);">
		</div>
		<div class="div_img_actualizar_datos_grilla_manual">
			<img src="img/reload_grilla.png" onclick="actualizarDatosGrillaManual();">
		</div>

	</div>
	<div id="contenedor_tabla_boletas">
		<div class="headTablaBoletas">
			<div class="campo0">&nbsp;</div>
			<div class="campo1" style="width:50px">Prefijo</div>
			<div class="campo1" style="width:70px">Numero</div>
			<div class="campo1" style="width:170px">Tercero</div>
			<div class="campo1" style="	width:70px;padding-right:5px;">Saldo</div>
			<div class="campo1">Fecha Inicial</div>
			<div class="campo1">Fecha Final</div>
		</div>
		<div class="bodyGrilla" id="bodyGrilla_<?php echo $opcGrilla; ?>"><?php echo $filaInsertGrilla; ?></div>
		<div style="float:right; margin:2 20px 0 0;">
			<div style="float:left; margin:2px 5px 0 5px;font-weight:bold;" id="labelPaginacion">Pagina 1 de <?php echo $paginas; ?></div>
			<div class="my_first" onclick="pag_<?php echo$opcGrilla; ?>('first')"></div>
			<div class="my_prev" onclick="pag_<?php echo$opcGrilla; ?>('prev')"></div>
			<div class="my_next" onclick="pag_<?php echo$opcGrilla; ?>('next')"></div>
			<div class="my_last" onclick="pag_<?php echo$opcGrilla; ?>('last')"></div>
		  </div>
	</div>
</div>

<script>
	//VARIABLES PARA LA PAGINACION
	arrayLimitGrilla<?php echo $opcGrilla; ?> = new Array();
	PaginaActual<?php echo $opcGrilla; ?>     = 1;
	MaxPage<?php echo $opcGrilla; ?>          = <?php echo $paginas; ?>;
	<?php echo $acumScript; ?>


	//========================================= FUNCION PARA LA PAGINACION ==========================================//
	//***************************************************************************************************************//
	function pag_<?php echo$opcGrilla; ?>(accion){
		var pagina = 0;

		if(accion=='first'){				//PRIMERA PAGINA
			pagina = 1;
			if(PaginaActual<?php echo $opcGrilla; ?>!=1){ ajaxLoadPaginacion_<?php echo $opcGrilla; ?>(pagina); }
		}

		else if(accion=='prev'){			//ANTERIOR PAGINA
			pagina = PaginaActual<?php echo $opcGrilla; ?>-1;
			if(PaginaActual<?php echo $opcGrilla; ?>!=1){ ajaxLoadPaginacion_<?php echo $opcGrilla; ?>(pagina); }
		}

		else if(accion=='next'){			//SIGUIENTE PAGINA
			pagina = PaginaActual<?php echo $opcGrilla; ?>+1;
			if(PaginaActual<?php echo $opcGrilla; ?>!=MaxPage<?php echo $opcGrilla; ?>){ ajaxLoadPaginacion_<?php echo $opcGrilla; ?>(pagina); }
		}

		else if(accion=='last'){			//ULTIMA PAGINA
			pagina = MaxPage<?php echo $opcGrilla; ?>;
			if(PaginaActual<?php echo $opcGrilla; ?>!=MaxPage<?php echo $opcGrilla; ?>){ ajaxLoadPaginacion_<?php echo $opcGrilla; ?>(pagina); }
		}
	}

	function ajaxLoadPaginacion_<?php echo $opcGrilla; ?>(pagina){
		var filtroGrilla  = document.getElementById('inputBuscarGrillaManual_<?php echo $opcGrilla; ?>').value;

		Ext.get('bodyGrilla_<?php echo $opcGrilla; ?>').load({
			url		: "/LOGICALERP/funciones_globales/grillas/configuracion_retenciones.php",
			scripts	: true,
			nocache	: true,
			params	:
			{
				opc               : 'busquedaPaginacion',
				limite            : '<?php echo $limit; ?>',
				limit             : arrayLimitGrilla<?php echo $opcGrilla; ?>[pagina],
				rows_registros    : '<?php echo $rows_registros; ?>',
				paginas           : '<?php echo $paginas;    ?>',
				pagina            : pagina,
				imprimeVar        : '',
				modulo            : '<?php echo $modulo; ?>',
				id_documento      : '<?php echo $id_documento; ?>',
				ejecutaFuncion    : '<?php echo $ejecutaFuncion; ?>',
				opcGrillaContable :'<?php echo $opcGrilla; ?>',
				filtroGrilla      : filtroGrilla,
			}
		});
	}

	//=================== FUNCION PARA ACTUALIZAR LOS DATOS DE LA GRILLA MANUAL ==================//
	function actualizarDatosGrillaManual() {
		var filtroGrilla = document.getElementById('inputBuscarGrillaManual_<?php echo $opcGrilla; ?>').value;

		Ext.get('bodyGrilla_<?php echo $opcGrilla; ?>').load({
			url		: "/LOGICALERP/funciones_globales/grillas/configuracion_retenciones.php",
			scripts	: true,
			nocache	: true,
			params	:
			{
				opc               : 'busquedaPaginacion',
				limite            : '<?php echo $limit; ?>',
				limit             : arrayLimitGrilla<?php echo $opcGrilla; ?>[PaginaActual<?php echo $opcGrilla; ?>],
				rows_registros    : '<?php echo $rows_registros; ?>',
				paginas           : '<?php echo $paginas;    ?>',
				pagina            : PaginaActual<?php echo $opcGrilla; ?>,
				imprimeVar        : '',
				modulo            : '<?php echo $modulo; ?>',
				id_documento      : '<?php echo $id_documento; ?>',
				ejecutaFuncion    : '<?php echo $ejecutaFuncion; ?>',
				opcGrillaContable :'<?php echo $opcGrilla; ?>',
				filtroGrilla      : filtroGrilla,

			}
		});
	}

	//=========================== FUNCION PARA BUSCAR REGISTROS POR UN VALOR =========================================//
	function buscarDatosGrillaManual_<?php echo $opcGrilla; ?>(filtroGrilla) {

		var limit        = (typeof(arrayLimitGrilla<?php echo $opcGrilla; ?>[PaginaActual<?php echo $opcGrilla; ?>])!="undefined")? arrayLimitGrilla<?php echo $opcGrilla; ?>[PaginaActual<?php echo $opcGrilla; ?>] : '0,<?php echo $limit ?>';
		var PaginaActual = (typeof(arrayLimitGrilla<?php echo $opcGrilla; ?>[PaginaActual<?php echo $opcGrilla; ?>])!="undefined")?PaginaActual<?php echo $opcGrilla; ?> : '1' ;

		Ext.get('bodyGrilla_<?php echo $opcGrilla; ?>').load({
			url		: "/LOGICALERP/funciones_globales/grillas/configuracion_retenciones.php",
			scripts	: true,
			nocache	: true,
			params	:
			{
				opc               : 'busquedaPaginacion',
				limite            : '<?php echo $limit; ?>',
				limit             : limit,
				rows_registros    : '<?php echo $rows_registros; ?>',
				paginas           : '<?php echo $paginas; ?>',
				pagina            : PaginaActual,
				imprimeVar        : '',
				modulo            : '<?php echo $modulo; ?>',
				id_documento      : '<?php echo $id_documento; ?>',
				ejecutaFuncion    : '<?php echo $ejecutaFuncion; ?>',
				opcGrillaContable :'<?php echo $opcGrilla; ?>',
				filtroGrilla      : filtroGrilla,
			}
		});
	}

</script>
