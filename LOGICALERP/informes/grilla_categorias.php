<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");

	$concat         = "";
	$id_empresa     = $_SESSION['EMPRESA'];
	$groupCategoria = "id";
	$campoNombre    = "nombre";

	$sqlSelect1     = "COUNT(id) as cont";
	$sqlSelect2     = "id,CONCAT(cod_familia,cod_grupo,codigo) AS codigo, nombre";

	if($nivel == "familia"){
		$sqlSelect1     = "COUNT(id_familia) as cont";
		$sqlSelect2     = "id_familia AS id,CONCAT(cod_familia) AS codigo, familia AS nombre";
		$groupCategoria = "id_familia";
	}
	else if($nivel == "grupo"){
		$sqlSelect1     = "COUNT(id_grupo) as cont";
		$sqlSelect2     = "id_grupo AS id,CONCAT(cod_familia,cod_grupo) AS codigo, grupo AS nombre";
		$groupCategoria = "id_grupo";
	}

	if ($opc == 'busquedaCategoriasPaginacion') {
		busquedaCategoriasPaginacion($sqlSelect1,$sqlSelect2,$opcGrilla,$pagina,$limite,$limit,$rows_registros,$paginas,$imprimeVar,$filtro,$link,$id_empresa);
		exit;
	}

	$contFilaCuenta = 0;
	$limit			= '100';

	$sql            = "SELECT $sqlSelect1 FROM items_familia_grupo_subgrupo WHERE activo=1 AND id_empresa='$id_empresa' GROUP BY $groupCategoria ORDER BY cod_familia,cod_grupo,codigo";
	$query          = mysql_query($sql,$link);
	$rows_registros = mysql_result($query,0,'cont');
	$paginas        = ceil( $rows_registros/$limit );

	//CREAR ARRAY CON LOS LIMITES DE LAS CONSULTAS
	$limit1     = 0;
	$limit2     = $limit;
	$acumScript = '';

	for ($i=1; $i <= $paginas; $i++) {
		$acumScript .= 'arrayLimitGrilla'.$opcGrilla.'['.$i.']="'.$limit1.','.$limit2.'";';
		$limit1 = $limit2+1;
		$limit2 = $limit2+$limit;
	}

 	$sqlCuentas   = "SELECT $sqlSelect2
					FROM items_familia_grupo_subgrupo
					WHERE activo=1
						AND id_empresa = '$id_empresa'
					GROUP BY $groupCategoria
					ORDER BY codigo ASC
					LIMIT $limit";

	$queryCuentas = mysql_query($sqlCuentas,$link);

	while ($rowCuentas = mysql_fetch_array($queryCuentas)) {
		$contFilaCuenta++;

		$filaInsertBoleta .= '<div class="filaBoleta" id="fila_boleta_'.$opcGrilla.'_'.$contFilaCuenta.'">

								<div class="campo0 campoInforme0">'.$contFilaCuenta.'</div>
								<div class="campo1 campoInforme1" id="codigo_'.$rowCuentas['id'].'">'.$rowCuentas['codigo'].'</div>
								<div class="campo2 campoInforme2" style="border-left:0px;width:170px;" id="nombre_'.$rowCuentas['id'].'" title="'.$rowCuentas['nombre'].'">'.$rowCuentas['nombre'].'</div>

								<div class="campo4 campoInforme4" id="valor_anticipo_'.$opcGrilla.'_'.$contFilaCuenta.'">
									<input type="checkbox" id="checkbox_'.$rowCuentas['id'].'" onchange="checkGrillaCategoriasItems(this,\''.$rowCuentas['id'].'\')" value="'.$rowCuentas['id'].'">
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
</style>


<div id="contenedor_formulario">
	<!-- <div class="loadSaveFormulario" id="loadSaveFormulario_<?php echo $opcGrilla; ?>"></div> -->
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
			<div style="float:left; margin:2px 5px 0 5px;font-weight:bold;" id="labelPaginacion_<?php echo $opcGrilla; ?>">Pagina 1 de <?php echo $paginas; ?></div>
			<div class="my_first" onclick="pag_grilla_<?php echo $opcGrilla; ?>('first')"></div>
			<div class="my_prev" onclick="pag_grilla_<?php echo $opcGrilla; ?>('prev')"></div>
			<div class="my_next" onclick="pag_grilla_<?php echo $opcGrilla; ?>('next')"></div>
			<div class="my_last" onclick="pag_grilla_<?php echo $opcGrilla; ?>('last')"></div>
		  </div>
	</div>
</div>

<script>
	//VARIABLES PARA LA PAGINACION
	arrayLimitGrilla<?php echo $opcGrilla; ?> = new Array();
	PaginaActual<?php echo $opcGrilla; ?>     = 1;
	MaxPage<?php echo $opcGrilla; ?>          = <?php echo $paginas; ?>;
	<?php echo $acumScript; ?>

	seleccionaCheck<?php echo $opcGrilla; ?>();

	//======================// FUNCION PARA HACER CHECK //======================//
	//**************************************************************************//
	function seleccionaCheck<?php echo $opcGrilla; ?>() {
		var arrayTemp=new Array();

		switch ("<?php echo $opcGrilla ?>") {

			case 'items': arrayTemp = array_categorias_items; break;
			case 'itemsRemisionados': arrayTemp = array_categorias_itemsRemisionados; break;
			default: break;

		}

		//RECORRER EL ARRAY DE LOS CLIENTES, PARA HACER CHECK A LOS QUE YA ESTAN EN LA GRILLA PRINCIPAL DE CONFIGURACION
		for ( i = 1; i < arrayTemp.length ; i++) {

			if (arrayTemp[i]!="" && typeof(arrayTemp[i])!="undefined") {
				if(document.getElementById('checkbox_'+i)){
					document.getElementById('checkbox_'+i).checked=true;
				}
			}
		}
	}

	//======================// FUNCION PARA LA PAGINACION //======================//
	//****************************************************************************//
	function pag_grilla_<?php echo $opcGrilla; ?>(accion){
		var MyParent = 'bodyTablaBoletas';
		var valor    = document.getElementById('inputBuscarGrillaManual').value;
		var filtro   = (valor!='')?'AND (codigo LIKE "%'+valor+'%" OR nombre LIKE "%'+valor+'%")' : '';

		if(accion=='first'){
			var pagina = 1;

			if(PaginaActual<?php echo $opcGrilla; ?>!=1){
				Ext.get(MyParent).load({
					url		: "<?php echo $_SERVER['SCRIPT_NAME']; ?>",
					scripts	: true,
					nocache	: true,
					params	:
					{
						opc            : 'busquedaCategoriasPaginacion',
						limite         : '<?php echo $limit; ?>',
						limit          : arrayLimitGrilla<?php echo $opcGrilla; ?>[pagina],
						rows_registros : '<?php echo $rows_registros; ?>',
						paginas        : '<?php echo $paginas; ?>',
						pagina         : pagina,
						imprimeVar     : '',
						opcGrilla      :'<?php echo $opcGrilla; ?>',
						filtro         : filtro
					}
				});
			}
		}

		if(accion=='prev'){
			var pagina = PaginaActual<?php echo $opcGrilla; ?>-1;

			if(PaginaActual<?php echo $opcGrilla; ?>!=1){
				Ext.get(MyParent).load({
					url		: "<?php echo $_SERVER['SCRIPT_NAME']; ?>",
					scripts	: true,
					nocache	: true,
					params	:
					{
						opc            : 'busquedaCategoriasPaginacion',
						limite         : '<?php echo $limit; ?>',
						limit          : arrayLimitGrilla<?php echo $opcGrilla; ?>[pagina],
						rows_registros : '<?php echo $rows_registros; ?>',
						paginas        : '<?php echo $paginas; ?>',
						pagina         : pagina,
						imprimeVar     : '',
						opcGrilla      :'<?php echo $opcGrilla; ?>',
						filtro         : filtro
					}
				});
			}
		}
		if(accion=='next'){
			var pagina = PaginaActual<?php echo $opcGrilla; ?>+1;

			if(PaginaActual<?php echo $opcGrilla; ?>!=MaxPage<?php echo $opcGrilla; ?>){
				Ext.get(MyParent).load({
					url		: "<?php echo $_SERVER['SCRIPT_NAME']; ?>",
					scripts	: true,
					nocache	: true,
					params	:
					{
						opc            : 'busquedaCategoriasPaginacion',
						limite         : '<?php echo $limit; ?>',
						limit          : arrayLimitGrilla<?php echo $opcGrilla; ?>[pagina],
						rows_registros : '<?php echo $rows_registros; ?>',
						paginas        : '<?php echo $paginas; ?>',
						pagina         : pagina,
						imprimeVar     : '',
						opcGrilla      :'<?php echo $opcGrilla; ?>',
						filtro         : filtro
					}
				});
			}
		}

		if(accion=='last'){
			var pagina = MaxPage<?php echo $opcGrilla; ?>;

			if(PaginaActual<?php echo $opcGrilla; ?>!=MaxPage<?php echo $opcGrilla; ?>){
				Ext.get(MyParent).load({
					url		: "<?php echo $_SERVER['SCRIPT_NAME']; ?>",
					scripts	: true,
					nocache	: true,
					params	:
					{
						opc            : 'busquedaCategoriasPaginacion',
						limite         : '<?php echo $limit; ?>',
						limit          : arrayLimitGrilla<?php echo $opcGrilla; ?>[pagina],
						rows_registros : '<?php echo $rows_registros; ?>',
						paginas        : '<?php echo $paginas; ?>',
						pagina         : pagina,
						imprimeVar     : '',
						opcGrilla      :'<?php echo $opcGrilla; ?>',
						filtro         : filtro
					}
				});
			}
		}
	}

	//======================// ACTUALIZAR GRILLA MANUAL //======================//
	//**************************************************************************//
	function actualizarDatosGrillaManual() {
		var valor  = document.getElementById('inputBuscarGrillaManual').value;
		var filtro = (valor!='')?'AND (codigo LIKE "%'+valor+'%" OR nombre LIKE "%'+valor+'%")' : '';

		Ext.get('bodyTablaBoletas').load({
			url		: "<?php echo $_SERVER['SCRIPT_NAME']; ?>",
			scripts	: true,
			nocache	: true,
			params	:
			{
				opc            : 'busquedaCategoriasPaginacion',
				limite         : '<?php echo $limit; ?>',
				limit          : arrayLimitGrilla<?php echo $opcGrilla; ?>[PaginaActual<?php echo $opcGrilla; ?>],
				rows_registros : '<?php echo $rows_registros; ?>',
				paginas        : '<?php echo $paginas; ?>',
				estado         : '<?php echo $estado; ?>',
				pagina         : PaginaActual<?php echo $opcGrilla; ?>,
				imprimeVar     : '',
				opcGrilla      : '<?php echo $opcGrilla; ?>',
				filtro         : filtro
			}
		});
	}


	function inputBuscarGrillaManual(event,input) {
		var tecla = input ? event.keyCode : event.which
		,   valor = input.value;

	    if (tecla==13) { buscarDatosGrillaManual(valor); }
	}

	//======================// FUNCION PARA BUSCAR REGISTROS POR UN VALOR //======================//
	//********************************************************************************************//
	function buscarDatosGrillaManual(valor) {

		var filtro       = (valor!='')?'AND (codigo LIKE "%'+valor+'%" OR nombre LIKE "%'+valor+'%")' : '';
		var MyParent     = 'bodyTablaBoletas';
		var limit        = (typeof(arrayLimitGrilla<?php echo $opcGrilla; ?>[PaginaActual<?php echo $opcGrilla; ?>])!="undefined")? arrayLimitGrilla<?php echo $opcGrilla; ?>[PaginaActual<?php echo $opcGrilla; ?>] : '0,<?php echo $limit ?>';
		var PaginaActual = (typeof(arrayLimitGrilla<?php echo $opcGrilla; ?>[PaginaActual<?php echo $opcGrilla; ?>])!="undefined")?PaginaActual<?php echo $opcGrilla; ?> : '1' ;

		Ext.get(MyParent).load({
			url		: "<?php echo $_SERVER['SCRIPT_NAME']; ?>",
			scripts	: true,
			nocache	: true,
			params	:
			{
				opc            : 'busquedaCategoriasPaginacion',
				limite         : '<?php echo $limit; ?>',
				limit          : limit,
				rows_registros : '<?php echo $rows_registros; ?>',
				paginas        : '<?php echo $paginas; ?>',
				estado         : '<?php echo $estado; ?>',
				pagina         : PaginaActual,
				imprimeVar     : '',
				opcGrilla      : '<?php echo $opcGrilla; ?>',
				filtro         : filtro
			}
		});
	}

</script>

<?php

// $campoNombre AS nombre
// GROUP BY $groupCategoria

	// FUNCION DE LA PAGINACION DE LA GRILLA
	function busquedaCategoriasPaginacion($sqlSelect1,$sqlSelect2,$opcGrilla,$pagina,$limite,$limit,$rows_registros,$paginas,$imprimeVar,$filtro,$link,$id_empresa){

		//SI LA VARIABLE FILTRO NO ESTA VACIA, RECONTAMOS EL LIMITE DE LOS REGISTROS
		if ($filtro!='') {
			$sql   = "SELECT $sqlSelect1  FROM items_familia_grupo_subgrupo WHERE activo=1 $filtro AND id_empresa='$id_empresa' GROUP BY $groupCategoria";
			$query = mysql_query($sql,$link);
			$rows_registros = mysql_result($query,0,'cont');
			$paginas        = ceil( $rows_registros/$limite );

			//CREAR ARRAY CON LOS LIMITES DE LAS CONSULTAS
			$limit1     = 0;
			$limit2     = $limite;
			$acumScript = '';
			for ($i=1; $i <= $paginas; $i++) {
				$acumScript .= 'arrayLimitGrilla'.$opcGrilla.'['.$i.']="'.$limit1.','.$limit2.'";';
				$limit1 = $limit2+1;
				$limit2 = $limit2+$limite;
			}
		}
		//SI NO SE HACE LA BUSQUEDA CON FILTRO SINO DE FORMA NORMAL
		else{
			$sql   = "SELECT $sqlSelect1  FROM items_familia_grupo_subgrupo WHERE activo=1 AND id_empresa='$id_empresa' GROUP BY $groupCategoria";
			$query = mysql_query($sql,$link);
			$rows_registros = mysql_result($query,0,'cont');
			$paginas        = ceil( $rows_registros/$limite );

			//CREAR ARRAY CON LOS LIMITES DE LAS CONSULTAS
			$limit1     = 0;
			$limit2     = $limite;
			$acumScript = '';
			for ($i=1; $i <= $paginas; $i++) {
				$acumScript .= 'arrayLimitGrilla'.$opcGrilla.'['.$i.']="'.$limit1.','.$limit2.'";';
				$limit1 = $limit2+1;
				$limit2 = $limit2+$limite;
			}
		}

		//SI SE BUSCA DESDE UNA PAGINA DIFERENTE A LA 1, VALIDAR SI EL RESULTADO DA LA MISMA CANTIDAD DE PAGINAS, SINO, PONER EN PAGINA 1 EJ(9 PAGINAS CONTRA EL RESULTADO DE 1 PAGINA)
		if ($pagina>$paginas) {
			$limit='0,'.$limite;
			$pagina=1;
		}

		$sqlCuentas   = "SELECT $sqlSelect2
						FROM items_familia_grupo_subgrupo
						WHERE activo=1
							$filtro
							AND id_empresa = '$id_empresa'
						LIMIT $limit";
		$queryCuentas = mysql_query($sqlCuentas,$link);

		while ($rowCuentas = mysql_fetch_array($queryCuentas)) {
			$contFilaCuenta++;

			$filaInsertBoleta .= '<div class="filaBoleta" id="fila_boleta_'.$opcGrilla.'_'.$contFilaCuenta.'">

									<div class="campo0 campoInforme0">'.$contFilaCuenta.'</div>
									<div class="campo1 campoInforme1" id="codigo_'.$rowCuentas['id'].'">'.$rowCuentas['codigo'].'</div>
									<div class="campo2 campoInforme2" style="border-left:0px; width:170px;" id="nombre_'.$rowCuentas['id'].'" title="'.$rowCuentas['nombre'].'">'.$rowCuentas['nombre'].'</div>

									<div class="campo4 campoInforme4" id="valor_anticipo_'.$opcGrilla.'_'.$contFilaCuenta.'">
										<input type="checkbox" id="checkbox_'.$rowCuentas['id'].'" onchange="checkGrillaCategoriasItems(this,\''.$rowCuentas['id'].'\')" value="'.$rowCuentas['id'].'">
									</div>
								</div>';
		}

		if ($filaInsertBoleta=='') {
			echo '<br><span style="font-style: italic;color: #999;font-weight: bold;margin-left: 20px;">No hay informacion que coincida con la busqueda...</span>';
		}

		$filaInsertBoleta .= '<script>
								document.getElementById("labelPaginacion_'.$opcGrilla.'").innerHTML="Pagina '.$pagina.' de '.$paginas.' ";
								PaginaActual'.$opcGrilla.'='.$pagina.';
								MaxPage'.$opcGrilla.'='.$paginas.';
								arrayLimitGrilla'.$opcGrilla.'.length=0;
								'.$acumScript.'
								'.$imprimeVar.'
								seleccionaCheck'.$opcGrilla.'();
							</script>';

			echo $filaInsertBoleta;
	}

 ?>