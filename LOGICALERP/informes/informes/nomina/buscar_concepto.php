<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$id_empresa = $_SESSION['EMPRESA'];
	// if($idCliente == 0 || $idCliente == '' || !isset($idCliente)){ echo 'No existe un cliente seleccionado'; exit; }

	$contFilaCuenta = 0;

	$estado         = ($tabla!='terceros'  && $tabla!="empleados")? 'AND estado=1 ' : '' ;
	$limit			= '100';

	$sql            = "SELECT COUNT(id) as cont FROM nomina_conceptos WHERE activo=1 AND id_empresa='$id_empresa'";
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

 	$sql   = "SELECT id,descripcion,naturaleza
					FROM nomina_conceptos
					WHERE activo=1
					AND id_empresa = '$id_empresa'";

	$query = mysql_query($sql,$link);

	while ($row = mysql_fetch_array($query)) {
		$contFilaCuenta++;

		$filaInsertBoleta .= '<div class="filaBoleta" id="fila_concepto_'.$opcGrillaContable.'_'.$contFilaCuenta.'">

								<div class="campoInforme0">'.$contFilaCuenta.'</div>
								<div class="campoInforme1" style="width:200px;" id="descripcion_concepto_'.$contFilaCuenta.'">'.$row['descripcion'].'</div>
								<div class="campoInforme2" style="border-left:0px;width:100px;" id="naturaleza_concepto_'.$contFilaCuenta.'" title="'.$row['naturaleza'].'">'.$row['naturaleza'].'</div>
								<div class="campoInforme4" id="valor_anticipo_'.$opcGrillaContable.'_'.$contFilaCuenta.'">
									<input type="checkbox" id="checkbox_'.$row[$id_tercero].'" onchange="checkGrillaConceptos(this,\''.$contFilaCuenta.'\')" value="'.$row[$id_tercero].'">
								</div>
							</div>';
	}

	$titulo_identificacion='Nit';

	$tabla=='nomina_conceptos';


?>

<style>
	#contenedor_formulario{
		overflow   : hidden;
		width      : calc(100% - 30px);
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
			<img src="img/reload_grilla.png" onclick="actualizarDatosGrillaManual();">
		</div>

	</div>
	<div id="contenedor_tabla_boletas">
		<div class="headTablaBoletas">
			<div class="campoInforme0"></div>
			<div class="campoInforme1" style="width:200px">Concepto</div>
			<div class="campoInforme2" style="border-left:0px;width:100px;">Naturaleza</div>
			<div class="campoInforme4">Seleccione</div>
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

	seleccionaCheck();

//================== FUNCION PARA HACER CHECK ================================//
function seleccionaCheck() {
	var arrayTemp=new Array();

	if('<?php echo $opcGrillaContable ?>'=='nomina'){arrayTemp=arrayConceptos;}
	else if('<?php echo $opcGrillaContable ?>'=='liquidacion'){arrayTemp=arrayConceptosLiquidacion;}
	else if('<?php echo $opcGrillaContable ?>'=='planilla_ajuste'){arrayTemp=arrayConceptosPlanillaAjuste;}
	// arrayTemp=arrayConceptos;

	//RECORRER EL ARRAY DE LOS CLIENTES, PARA HACER CHECK A LOS QUE YA ESTAN EN LA GRILLA PRINCIPAL DE CONFIGURACION
	for ( i =1; i < arrayTemp.length ; i ++) {
		if (arrayTemp[i]!="" && typeof(arrayTemp[i])!="undefined") {
			if(document.getElementById('checkbox_'+arrayTemp[i])){
				document.getElementById('checkbox_'+arrayTemp[i]).checked=true;
			}
		}
	}

}

//================= 	FUNCION PARA LA PAGINACION =======================================//
function pag_Terceros(accion){
	var MyParent = 'bodyTablaBoletas';
	var nit = ('<?php echo $tabla; ?>'!='terceros')? 'nit ' : 'numero_identificacion' ;
	var valor = document.getElementById('inputBuscarGrillaManual').value;
	var filtro = (valor!='')?'AND (descripcion LIKE "%'+valor+'%" OR codigo LIKE "%'+valor+'%")' : '';

	if(accion=='first'){
		var pagina = 1;
		if(PaginaActual<?php echo $opcGrillaContable; ?>!=1){
				Ext.get(MyParent).load(
					{
						url		: "/LOGICALERP/informes/informes/nomina/bd.php",
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
								filtro            : filtro,

							}
					}
				);
		}
	}

	if(accion=='prev'){
		var pagina = PaginaActual<?php echo $opcGrillaContable; ?>-1;
		if(PaginaActual<?php echo $opcGrillaContable; ?>!=1){
				Ext.get(MyParent).load(
					{
						url		: "/LOGICALERP/informes/informes/nomina/bd.php",
						scripts	: true,
						nocache	: true,
						params	:
							{
								opc            : 'busquedaTerceroPaginacion',
								limite         : '<?php echo $limit; ?>',
								limit          : arrayLimitGrilla<?php echo $opcGrillaContable; ?>[pagina],
								rows_registros : '<?php echo $rows_registros; ?>',
								paginas        : '<?php echo $paginas;    ?>',
								id_tercero     : '<?php echo $id_tercero; ?>',
								tercero        : '<?php echo $tercero;    ?>',
								nit            : '<?php echo $nit;        ?>',
								whereSum       : '<?php echo $whereSum;   ?>',
								tabla          : '<?php echo $tabla;      ?>',
								estado         : '<?php echo $estado;     ?>',
								pagina         : pagina,
								imprimeVar     : '',
								opcGrillaContable :'<?php echo $opcGrillaContable; ?>',
								filtro            : filtro,

							}
					}
				);
		}
	}
	if(accion=='next'){
		var pagina = PaginaActual<?php echo $opcGrillaContable; ?>+1;
		if(PaginaActual<?php echo $opcGrillaContable; ?>!=MaxPage<?php echo $opcGrillaContable; ?>){
			Ext.get(MyParent).load(
				{
					url		: "/LOGICALERP/informes/informes/nomina/bd.php",
					scripts	: true,
					nocache	: true,
					params	:
						{
								opc            : 'busquedaTerceroPaginacion',
								limite            : '<?php echo $limit; ?>',
								limit          : arrayLimitGrilla<?php echo $opcGrillaContable; ?>[pagina],
								rows_registros : '<?php echo $rows_registros; ?>',
								paginas        : '<?php echo $paginas;    ?>',
								id_tercero     : '<?php echo $id_tercero; ?>',
								tercero        : '<?php echo $tercero;    ?>',
								nit            : '<?php echo $nit;        ?>',
								whereSum       : '<?php echo $whereSum;   ?>',
								tabla          : '<?php echo $tabla;      ?>',
								estado         : '<?php echo $estado;     ?>',
								pagina         : pagina,
								imprimeVar     : '',
								opcGrillaContable :'<?php echo $opcGrillaContable; ?>',
								filtro            : filtro,

						}
				}
			);
		}
	}

	if(accion=='last'){
		var pagina = MaxPage<?php echo $opcGrillaContable; ?>;
		if(PaginaActual<?php echo $opcGrillaContable; ?>!=MaxPage<?php echo $opcGrillaContable; ?>){
			Ext.get(MyParent).load(
				{
					url		: "/LOGICALERP/informes/informes/nomina/bd.php",
					scripts	: true,
					nocache	: true,
					params	:
						{
								opc            : 'busquedaTerceroPaginacion',
								limite            : '<?php echo $limit; ?>',
								limit          : arrayLimitGrilla<?php echo $opcGrillaContable; ?>[pagina],
								rows_registros : '<?php echo $rows_registros; ?>',
								paginas        : '<?php echo $paginas;    ?>',
								id_tercero     : '<?php echo $id_tercero; ?>',
								tercero        : '<?php echo $tercero;    ?>',
								nit            : '<?php echo $nit;        ?>',
								whereSum       : '<?php echo $whereSum;   ?>',
								tabla          : '<?php echo $tabla;      ?>',
								estado         : '<?php echo $estado;     ?>',
								pagina         : pagina,
								imprimeVar     : '',
								opcGrillaContable :'<?php echo $opcGrillaContable; ?>',
								filtro            : filtro,

						}
				}
			);
		}
	}
}

//=================== FUNCION PARA ACTUALIZAR LOS DATOS DE LA GRILLA MANUAL ==================//
function actualizarDatosGrillaManual() {
	var MyParent = 'bodyTablaBoletas';
	var nit = ('<?php echo $tabla; ?>'!='terceros')? 'nit ' : 'numero_identificacion' ;
	var valor = document.getElementById('inputBuscarGrillaManual').value;
	var filtro = (valor!='')?'AND (descripcion LIKE "%'+valor+'%" OR codigo LIKE "%'+valor+'%")' : '';
	Ext.get(MyParent).load(
						{
							url		: "/LOGICALERP/informes/informes/nomina/bd.php",
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
									opcGrillaContable :'<?php echo $opcGrillaContable; ?>',
									filtro            : filtro,

								}
						}
					);
}


function inputBuscarGrillaManual(event,input) {
	 var tecla   = input ? event.keyCode : event.which
    ,   valor  = input.value;
    if (tecla==13) {
    	buscarDatosGrillaManual(valor);
    }
}

//=========================== FUNCION PARA BUSCAR REGISTROS POR UN VALOR =========================================//
function buscarDatosGrillaManual(valor) {

	// var nit = ('<?php echo $tabla; ?>'!='terceros')? 'nit ' : 'numero_identificacion' ;
	var filtro = (valor!='')?'AND (descripcion LIKE "%'+valor+'%" OR codigo LIKE "%'+valor+'%")' : '';
	var MyParent = 'bodyTablaBoletas';
	var limit =(typeof(arrayLimitGrilla<?php echo $opcGrillaContable; ?>[PaginaActual<?php echo $opcGrillaContable; ?>])!="undefined")? arrayLimitGrilla<?php echo $opcGrillaContable; ?>[PaginaActual<?php echo $opcGrillaContable; ?>] : '0,<?php echo $limit ?>';
	var PaginaActual=(typeof(arrayLimitGrilla<?php echo $opcGrillaContable; ?>[PaginaActual<?php echo $opcGrillaContable; ?>])!="undefined")?PaginaActual<?php echo $opcGrillaContable; ?> : '1' ;
	Ext.get(MyParent).load(
						{
							url		: "/LOGICALERP/informes/informes/nomina/bd.php",
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
									opcGrillaContable :'<?php echo $opcGrillaContable; ?>',
									filtro 			  : filtro,

								}
						}
					);
}

</script>