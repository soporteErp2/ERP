<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");

	$id_empresa = $_SESSION['EMPRESA'];

	if ($opc == 'busquedaCentroCostoPaginacion') {
		busquedaCentroCostoPaginacion($opcGrillaContable,$pagina,$limite,$limit,$rows_registros,$paginas,$imprimeVar,$filtro,$link,$id_empresa);
		exit;
	}

	$contFilaCuenta = 0;
	$limit			= '100';

	$sql            = "SELECT COUNT(id) as cont $whereSum FROM centro_costos WHERE activo=1 $estado AND id_empresa='$id_empresa'";
	$query          = mysql_query($sql,$link);
	$rows_registros = mysql_result($query,0,'cont');
	$paginas        = ceil( $rows_registros/$limit );

	//CREAR ARRAY CON LOS LIMITES DE LAS CONSULTAS
	$limit1     = 0;
	$limit2     = $limit;
	$acumScript = '';

	for ($i=1; $i <= $paginas; $i++) {
		$acumScript .='arrayLimitGrilla'.$opcGrillaContable.'['.$i.']="'.$limit1.','.$limit2.'";';
		$limit1     =$limit2+1;
		$limit2     =$limit2+$limit;
	}

 	$sqlCuentas   = "SELECT id,codigo,nombre
					FROM centro_costos
					WHERE activo=1
					AND id_empresa = '$id_empresa'
					LIMIT $limit";

	$queryCuentas = mysql_query($sqlCuentas,$link);

	while ($rowCuentas = mysql_fetch_array($queryCuentas)) {
		$contFilaCuenta++;

		$filaInsertBoleta .= '<div class="filaBoleta" id="fila_boleta_'.$opcGrillaContable.'_'.$contFilaCuenta.'">

								<div class="campo0 campoInforme0">'.$contFilaCuenta.'</div>
								<div class="campo1 campoInforme1" id="codigo_'.$rowCuentas['id'].'">'.$rowCuentas['codigo'].'</div>
								<div class="campo2 campoInforme2" style="border-left:0px;width:170px;" id="nombre_'.$rowCuentas['id'].'" title="'.$rowCuentas['nombre'].'">'.$rowCuentas['nombre'].'</div>

								<div class="campo4 campoInforme4" id="valor_anticipo_'.$opcGrillaContable.'_'.$contFilaCuenta.'">
									<input type="checkbox" id="checkbox_'.$rowCuentas['id'].'" onchange="checkGrillaCentroCostos(this,\''.$rowCuentas['id'].'\')" value="'.$rowCuentas['id'].'">
								</div>
							</div>';
	}


?>

<style>
	#contenedor_formulario{
		overflow   : hidden;
		width      : calc(100% - 30px);
		height     : calc(100% - 15px);
		margin     : 15px;
		margin-top : 0px;
	}
	/*#contenedor_tabla_boletas*/
</style>


<div id="contenedor_formulario">
	<!-- <div class="loadSaveFormulario" id="loadSaveFormulario_<?php echo $opcGrillaContable; ?>"></div> -->
	<div class="toolbar_grilla_manual">
		<div class="div_input_busqueda_grilla_manual">
			<input type="text" id="inputBuscarGrillaManual" onkeyup="inputBuscarGrillaManual(event,this);">
		</div>
		<div class="div_img_actualizar_datos_grilla_manual">
			<img src="img/reload_grilla.png" onclick="actualizarDatosGrillaManual();">
		</div>

	</div>
	<div id="contenedor_tabla_boletas" style="height:calc(100% - 41px);">
		<div class="headTablaBoletas">
			<div class="campo0 campoInforme0"></div>
			<div class="campo1 campoInforme1">Codigo</div>
			<div class="campo2 " style="border-left:0px;width:170px;">Nombre</div>
			<div class="campo4">Seleccione</div>
		</div>
		<div id="bodyTablaBoletas" style="height:calc(100% - 51px);"><?php echo $filaInsertBoleta; ?></div>
		<div style="float:right; margin:2 20px 0 0;">
			<div style="float:left; margin:2px 5px 0 5px;font-weight:bold;" id="labelPaginacion">Pagina 1 de <?php echo $paginas; ?></div>
			<div class="my_first" onclick="pag_centro_costos('first')"></div>
			<div class="my_prev" onclick="pag_centro_costos('prev')"></div>
			<div class="my_next" onclick="pag_centro_costos('next')"></div>
			<div class="my_last" onclick="pag_centro_costos('last')"></div>
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
	function seleccionaCheck() {
		var arrayTemp=new Array();

		switch ("<?php echo $opcGrillaContable ?>") {

			case 'facturas': arrayTemp = arrayCentroCostosFV; break;
			case 'facturas_compras': arrayTemp = arrayCentroCostosFC; break;
			case 'items': arrayTemp = array_ccos_items; break;
			case 'itemsRemisionados': arrayTemp = array_ccos_itemsRemisionados; break;
			default: break;

		}

		//RECORRER EL ARRAY DE LOS CLIENTES, PARA HACER CHECK A LOS QUE YA ESTAN EN LA GRILLA PRINCIPAL DE CONFIGURACION
		for ( i =1; i < arrayTemp.length ; i ++) {

			if (arrayTemp[i]!="" && typeof(arrayTemp[i])!="undefined") {
				if(document.getElementById('checkbox_'+i)){
					document.getElementById('checkbox_'+i).checked=true;
				}
			}
		}
	}

	//======================// FUNCION PAGINACION //======================//
	//********************************************************************//
	function pag_centro_costos(accion){
		var MyParent = 'bodyTablaBoletas';
		var valor    = document.getElementById('inputBuscarGrillaManual').value;
		var filtro   = (valor!='')?'AND (codigo LIKE "%'+valor+'%" OR nombre LIKE "%'+valor+'%")' : '';

		if(accion=='first'){
			var pagina = 1;

			if(PaginaActual<?php echo $opcGrillaContable; ?>!=1){
				Ext.get(MyParent).load({
					url		: "/LOGICALERP/informes/grillaBuscarCentroCostos.php",
					scripts	: true,
					nocache	: true,
					params	:
					{
						opc               : 'busquedaCentroCostoPaginacion',
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
					url		: "/LOGICALERP/informes/grillaBuscarCentroCostos.php",
					scripts	: true,
					nocache	: true,
					params	:
					{
						opc               : 'busquedaCentroCostoPaginacion',
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
					url		: "/LOGICALERP/informes/grillaBuscarCentroCostos.php",
					scripts	: true,
					nocache	: true,
					params	:
					{
						opc               : 'busquedaCentroCostoPaginacion',
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
					url		: "/LOGICALERP/informes/grillaBuscarCentroCostos.php",
					scripts	: true,
					nocache	: true,
					params	:
					{
						opc               : 'busquedaCentroCostoPaginacion',
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
	}

	//======================// UPDATE GRILLA MANUAL //======================//
	//**********************************************************************//
	function actualizarDatosGrillaManual() {
		var valor    = document.getElementById('inputBuscarGrillaManual').value;
		var filtro   = (valor!='')?'AND (codigo LIKE "%'+valor+'%" OR nombre LIKE "%'+valor+'%")' : '';

		Ext.get('bodyTablaBoletas').load({
			url		: "/LOGICALERP/informes/grillaBuscarCentroCostos.php",
			scripts	: true,
			nocache	: true,
			params	:
			{
				opc               : 'busquedaCentroCostoPaginacion',
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


	function inputBuscarGrillaManual(event,input) {
		var tecla = input ? event.keyCode : event.which
		,   valor = input.value;

	    if (tecla==13) { buscarDatosGrillaManual(valor); }
	}

	//======================// FILTRO GRILLA //======================//
	//***************************************************************//
	function buscarDatosGrillaManual(valor) {

		// var nit = ('<?php echo $tabla; ?>'!='terceros')? 'nit ' : 'numero_identificacion' ;
		var filtro       = (valor!='')?'AND (codigo LIKE "%'+valor+'%" OR nombre LIKE "%'+valor+'%")' : '';
		var MyParent     = 'bodyTablaBoletas';
		var limit        =(typeof(arrayLimitGrilla<?php echo $opcGrillaContable; ?>[PaginaActual<?php echo $opcGrillaContable; ?>])!="undefined")? arrayLimitGrilla<?php echo $opcGrillaContable; ?>[PaginaActual<?php echo $opcGrillaContable; ?>] : '0,<?php echo $limit ?>';
		var PaginaActual =(typeof(arrayLimitGrilla<?php echo $opcGrillaContable; ?>[PaginaActual<?php echo $opcGrillaContable; ?>])!="undefined")?PaginaActual<?php echo $opcGrillaContable; ?> : '1' ;

		Ext.get(MyParent).load({
			url		: "/LOGICALERP/informes/grillaBuscarCentroCostos.php",
			scripts	: true,
			nocache	: true,
			params	:
			{
				opc               : 'busquedaCentroCostoPaginacion',
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

<?php

	// FUNCION DE LA PAGINACION DE LA GRILLA
	function busquedaCentroCostoPaginacion($opcGrillaContable,$pagina,$limite,$limit,$rows_registros,$paginas,$imprimeVar,$filtro,$link,$id_empresa){

		//SI LA VARIABLE FILTRO NO ESTA VACIA, RECONTAMOS EL LIMITE DE LOS REGISTROS
		if ($filtro!='') {
			$sql="SELECT COUNT(id) as cont  FROM centro_costos WHERE activo=1 $filtro AND id_empresa='$id_empresa' ";
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
			$sql="SELECT COUNT(id) as cont  FROM centro_costos WHERE activo=1 AND id_empresa='$id_empresa'";
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

		$sqlCuentas   = "SELECT id,codigo,nombre FROM centro_costos WHERE activo=1 $filtro AND id_empresa = '$id_empresa'  LIMIT $limit";
		$queryCuentas = mysql_query($sqlCuentas,$link);

		while ($rowCuentas = mysql_fetch_array($queryCuentas)) {
			$contFilaCuenta++;

			$filaInsertBoleta .= '<div class="filaBoleta" id="fila_boleta_'.$opcGrillaContable.'_'.$contFilaCuenta.'">

								<div class="campo0 campoInforme0">'.$contFilaCuenta.'</div>
								<div class="campo1 campoInforme1" id="codigo_'.$rowCuentas['id'].'">'.$rowCuentas['codigo'].'</div>
								<div class="campo2 campoInforme2" style="border-left:0px;width:170px;" id="nombre_'.$rowCuentas['id'].'" title="'.$rowCuentas['nombre'].'">'.$rowCuentas['nombre'].'</div>

								<div class="campo4 campoInforme4" id="valor_anticipo_'.$opcGrillaContable.'_'.$contFilaCuenta.'">
									<input type="checkbox" id="checkbox_'.$rowCuentas['id'].'" onchange="checkGrillaCentroCostos(this,\''.$rowCuentas['id'].'\')" value="'.$rowCuentas['id'].'">
								</div>
							</div>';
		}

		if ($filaInsertBoleta=='') {
			echo '<br><span style="  font-style: italic;color: #999;font-weight: bold;margin-left: 20px;">No hay informacion que coincida con la busqueda...</span>';
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

 ?>