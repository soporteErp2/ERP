<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$id_empresa = $_SESSION['EMPRESA'];

	if ($opc == 'busquedaTerceroPaginacion') {
		busquedaTerceroPaginacion($id_documento,$opcGrillaContable,$pagina,$limite,$limit,$rows_registros,$paginas,$imprimeVar,$filtro,$link,$id_empresa);
		exit;
	}



	$limit			= '500';

	$sql            = "SELECT COUNT(id) as cont FROM empleados_contratos WHERE activo=1 AND id_empresa='$id_empresa' AND estado=0";
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

	// CONSULTAR LOS EMPLEADOS QUE TENGAN PRESTAMOS PENDIENTES
	$sql="SELECT id_empleado,nombre_empleado FROM nomina_prestamos_empleados WHERE activo=1 AND id_empresa=$id_empresa AND valor_prestamo_restante>0 GROUP BY id_empleado";
	$query=mysql_query($sql,$link);
	while ($row=mysql_fetch_array($query)) {
		$id_empleado=$row['id_empleado'];
		$arrayPrestamos[$id_empleado]=$row['nombre_empleado'];
	}

 	$sqlCuentas   = "SELECT id,id_empleado,nombre_empleado FROM empleados_contratos WHERE activo=1 AND id_empresa = '$id_empresa' AND estado=0 AND nombre_empleado IS NOT NULL LIMIT $limit";

	$queryCuentas = mysql_query($sqlCuentas,$link);
	// $contFilaCuenta=1;
	while ($rowCuentas = mysql_fetch_array($queryCuentas)) {
		unset($arrayPrestamos[$rowCuentas['id_empleado']]);
		$contFilaCuenta++;

		$filaInsertBoleta .= '<div class="filaBoleta_empleado" id="fila_boleta_'.$opcGrillaContable.'_'.$contFilaCuenta.'">

								<!--<div class="campo0">'.$contFilaCuenta.'</div>-->
								<div class="campo2_empleado"  id="contrato_'.$rowCuentas['id'].'" title="'.$rowCuentas['nombre_empleado'].'" onclick="cargaConceptosEmpleados(\''.$rowCuentas['id_empleado'].'\',\''.$rowCuentas['nombre_empleado'].'\')">
									'.$rowCuentas['nombre_empleado'].'
								</div>
							</div>';

	}

	// RECORRER EL ARRAY DE LOS PRESTAMOS POR SI HAY EMPLEADOS SIN CONTRATO Y CON PRESTAMOS PENDIENTES
	$empleadosPendientes='';
	foreach ($arrayPrestamos as $id_empleado => $nombre_empleado) {
		$contFilaCuenta++;
		$empleadosPendientes.='<div class="filaBoleta_empleado"  id="fila_boleta_'.$opcGrillaContable.'_'.$contFilaCuenta.'">
								<div class="campo2_empleado" style="color:#f98484;font-style:italic;" id="contrato_" title="Sin contrato, con prestamo pendiente" onclick="cargaConceptosEmpleados(\''.$id_empleado.'\',\''.$nombre_empleado.'\',1)">
									'.$nombre_empleado.'
								</div>
							</div>';
	}

	$filaInsertBoleta=($empleadosPendientes=='')? $filaInsertBoleta : $empleadosPendientes.$filaInsertBoleta ;

	$titulo_identificacion='Nit';

	if ($tabla=='ventas_facturas') { $titulo_tercero='Cliente'; }
	else if ($tabla=='compras_facturas') { $titulo_tercero='Proveedor'; }
	else if ($tabla=='empleados') { $titulo_tercero='Empleado'; $titulo_identificacion='Documento';}
	else{ $titulo_tercero='Tercero'; }


?>

<style>
	#contenedor_formulario_empleados{
		overflow      : hidden;
		width         : 90%;
		height        : 90%;
		margin        : 10% auto;
		border-radius : 5px;
		border        : 1px solid #999;
		font-weight   : bold;
		font-family   : sans-serif;
	}

	/*TOOLBAR DE LA BUSQUEDA DE LA GRILLA MANUAL*/
	.toolbar_grilla_manual_empleados{
		overflow   : hidden;
		width      : 100%;
		height     : 34px;
		background-color: #999;
		color: #FFF;
		/*margin-top : 5px;*/
	}
	.div_input_busqueda_grilla_manual_empleados{
		width            : calc(100% - 43px);
		/*background-color : #FFF;*/
		height           : 28px;
		padding-top      : 5px;
		padding          : 5 0 0 6;
		/*border           : 1px solid #D4D4D4;*/
		float            : left;
		/*border-right     : none;*/
	}

	.div_input_busqueda_grilla_manual_empleados input{
		background-repeat : no-repeat;
		padding           : 0 0 0 3px;
		font-size         : 12px;
		min-height        : 22px;
		border            : none;
		margin-left       : 10px;
		width             : 135px;
	}

	.div_input_busqueda_grilla_manual_empleados > div{
		float: left;
	}

	.div_img_actualizar_datos_grilla_manual_empleados{
		float            : left;
		width            : 35px;
		height           : 40px;
		/*background-color : #FFF;*/
		/*border-top       : 1px solid #D4D4D4;*/
		/*border-right     : 1px solid #d4d4d4;*/
	}

	.div_img_actualizar_datos_grilla_manual_empleados>img{
		padding : 7 0 0 7;
		cursor  : pointer;
	}

	#contenedor_tabla_boletas_empleado{
		overflow              : hidden;
		width                 : calc(100% - 2px);
		height                : calc(100% - 36px);
		border                : 1px solid #D4D4D4 	;
		border-radius         : 4px;
		-webkit-border-radius : 4px;
		-webkit-box-shadow    : 1px 1px 1px #d4d4d4;
		-moz-box-shadow       : 1px 1px 1px #d4d4d4;
		box-shadow            : 1px 1px 1px #d4d4d4;
		background-color      :#F3F3F3;
	}


	#bodyTablaBoletasEmpleados{
		overflow-x       : hidden;
		overflow-y       : auto;
		width            : 100%;
		height           : 100%;
		background-color : #FFF;
		border-bottom    : 1px solid #d4d4d4;
	}

	/*#bodyTablaBoletasEmpleados > div{*/
/**/
	/*}*/

	/*#bodyTablaBoletasEmpleados > div > div { height: 18px; /*background-color : #FFF; padding-top: 4px; }*/
	/*#bodyTablaBoletasEmpleados >  div:hover {background-color: #EEE;}*/

	.filaBoleta_empleado{
		overflow      : hidden;
		height        : 25px;
		border-bottom : 1px solid #EBEBEB;
		cursor: pointer;
		width         : calc(100% - 20px);
	}

	.filaBoleta_empleado:hover{
		background-color: #EEE;
	}

	.campo2_empleado{
		float         : left;
		width         : calc(100% - 10px);
		text-indent   : 5px;
		overflow      : hidden;
		white-space   : nowrap;
		text-overflow : ellipsis;
		font-weight   : bold;
		color         : #8B8787;
		padding       : 5px 10px;
	}


	.divSeparador{
		width: 100%;
		border: 2px solid #F3F3F3;

	}

	.mCSB_container{
		margin-right: 0px !important;
	}

	.mCSB_dragger_bar{
		background-color: #d3d3d3 !important;
	}

	.mCSB_draggerRail{
		background-color: #FFF !important;
	}


</style>

<link rel="stylesheet" href="lib/jquery.mCustomScrollbar.css">
<meta name="viewport" content="width=device-width, initial-scale=1" />

<div id="contenedor_formulario_empleados">
	<!-- <div class="loadSaveFormulario" id="loadSaveFormulario_<?php echo $opcGrillaContable; ?>"></div> -->

	 <div class="toolbar_grilla_manual_empleados">

		<div class="div_input_busqueda_grilla_manual_empleados">
			<div>
				EMPLEADOS
			</div>
			<div>
				<input type="text" id="inputBuscarGrillaManualEmpleados" onkeyup="inputBuscarGrillaManualEmpleados(event,this);" placeholder="Buscar..." >
			</div>
		</div>
		<div class="div_img_actualizar_datos_grilla_manual_empleados">
			<img src="img/reload_grilla_new.png" onclick="actualizarDatosGrillaManualEmpleados();" >
		</div>

	</div>

	<div id="contenedor_tabla_boletas_empleado">
		<!-- <div class="headTablaBoletas">
			<div class="campo0">&nbsp;</div>
			<div class="campo2_empleado" >Cargo</div>
		</div> -->
		<div class="divSeparador"></div>
		<div id="bodyTablaBoletasEmpleados"><?php echo $filaInsertBoleta; ?></div>
		<div style="float:right; margin:2 20px 0 0;display:none;">
			<div style="float:left; margin:2px 5px 0 5px;font-weight:bold;" id="labelPaginacion">Pagina 1 de <?php echo $paginas; ?></div>
			<div class="my_first" onclick="pag_conceptos_empleados('first')"></div>
			<div class="my_prev" onclick="pag_conceptos_empleados('prev')"></div>
			<div class="my_next" onclick="pag_conceptos_empleados('next')"></div>
			<div class="my_last" onclick="pag_conceptos_empleados('last')"></div>
		  </div>
	</div>
</div>

<script>
	$("#bodyTablaBoletasEmpleados").mCustomScrollbar({
		theme:'3d-dark',
	});
	//VARIABLES PARA LA PAGINACION
	arrayLimitGrilla<?php echo $opcGrillaContable; ?>  = new Array();
	PaginaActual<?php echo $opcGrillaContable; ?> = 1;
	MaxPage<?php echo $opcGrillaContable; ?>      = <?php echo $paginas; ?>;
	<?php echo $acumScript; ?>


//================= 	FUNCION PARA LA PAGINACION =======================================//
function pag_conceptos_empleados(accion){
	var MyParent = 'bodyTablaBoletasEmpleados';

	var valor = document.getElementById('inputBuscarGrillaManualEmpleados').value;
	var filtro = (valor!='')?'AND (nombre_empleado LIKE "%'+valor+'%"  OR documento_empleado LIKE "%'+valor+'%") ' : '';

	if(accion=='first'){
		var pagina = 1;
		if(PaginaActual<?php echo $opcGrillaContable; ?>!=1){
				Ext.get(MyParent).load(
					{
						url		: "conceptos_empleados/menu_grilla_cargos.php",
						scripts	: true,
						nocache	: true,
						params	:
							{
								opc               : 'busquedaTerceroPaginacion',
								limite            : '<?php echo $limit; ?>',
								limit             : arrayLimitGrilla<?php echo $opcGrillaContable; ?>[pagina],
								rows_registros    : '<?php echo $rows_registros; ?>',
								paginas           : '<?php echo $paginas;    ?>',
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
						url		: "conceptos_empleados/menu_grilla_cargos.php",
						scripts	: true,
						nocache	: true,
						params	:
							{
								opc               : 'busquedaTerceroPaginacion',
								limite            : '<?php echo $limit; ?>',
								limit             : arrayLimitGrilla<?php echo $opcGrillaContable; ?>[pagina],
								rows_registros    : '<?php echo $rows_registros; ?>',
								paginas           : '<?php echo $paginas;    ?>',
								pagina            : pagina,
								imprimeVar        : '',
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
					url		: "conceptos_empleados/menu_grilla_cargos.php",
					scripts	: true,
					nocache	: true,
					params	:
						{
								opc               : 'busquedaTerceroPaginacion',
								limite            : '<?php echo $limit; ?>',
								limit             : arrayLimitGrilla<?php echo $opcGrillaContable; ?>[pagina],
								rows_registros    : '<?php echo $rows_registros; ?>',
								paginas           : '<?php echo $paginas;    ?>',
								pagina            : pagina,
								imprimeVar        : '',
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
					url		: "conceptos_empleados/menu_grilla_cargos.php",
					scripts	: true,
					nocache	: true,
					params	:
						{
								opc               : 'busquedaTerceroPaginacion',
								limite            : '<?php echo $limit; ?>',
								limit             : arrayLimitGrilla<?php echo $opcGrillaContable; ?>[pagina],
								rows_registros    : '<?php echo $rows_registros; ?>',
								paginas           : '<?php echo $paginas;    ?>',
								pagina            : pagina,
								imprimeVar        : '',
								opcGrillaContable :'<?php echo $opcGrillaContable; ?>',
								filtro            : filtro,

						}
				}
			);
		}
	}
}

//=================== FUNCION PARA ACTUALIZAR LOS DATOS DE LA GRILLA MANUAL ==================//
function actualizarDatosGrillaManualEmpleados() {
	var MyParent = 'bodyTablaBoletasEmpleados';

	var valor = document.getElementById('inputBuscarGrillaManualEmpleados').value;
	var filtro = (valor!='')?'AND (nombre_empleado LIKE "%'+valor+'%" OR documento_empleado LIKE "%'+valor+'%") ' : '';

	Ext.get(MyParent).load(
						{
							url		: "conceptos_empleados/menu_grilla_cargos.php",
							scripts	: true,
							nocache	: true,
							params	:
								{
									opc               : 'busquedaTerceroPaginacion',
									limite            : '<?php echo $limit; ?>',
									limit             : arrayLimitGrilla<?php echo $opcGrillaContable; ?>[PaginaActual<?php echo $opcGrillaContable; ?>],
									rows_registros    : '<?php echo $rows_registros; ?>',
									paginas           : '<?php echo $paginas;    ?>',
									pagina            : PaginaActual<?php echo $opcGrillaContable; ?>,
									imprimeVar        : '',
									opcGrillaContable :'<?php echo $opcGrillaContable; ?>',
									filtro            : filtro,

								}
						}
					);
}


function inputBuscarGrillaManualEmpleados(event,input) {
	var tecla   = input ? event.keyCode : event.which
    ,   valor  = input.value;
    if (tecla==13) {
    	buscarDatosGrillaManualEmpleados(valor);
    }
}

//=========================== FUNCION PARA BUSCAR REGISTROS POR UN VALOR =========================================//
function buscarDatosGrillaManualEmpleados(valor) {

	// var nit = ('<?php echo $tabla; ?>'!='terceros')? 'nit ' : 'numero_identificacion' ;
	var filtro = (valor!='')?'AND (nombre_empleado LIKE "%'+valor+'%"  OR documento_empleado LIKE "%'+valor+'%") ' : '';
	var MyParent = 'bodyTablaBoletasEmpleados';
	var limit =(typeof(arrayLimitGrilla<?php echo $opcGrillaContable; ?>[PaginaActual<?php echo $opcGrillaContable; ?>])!="undefined")? arrayLimitGrilla<?php echo $opcGrillaContable; ?>[PaginaActual<?php echo $opcGrillaContable; ?>] : '0,<?php echo $limit ?>';
	var PaginaActual=(typeof(arrayLimitGrilla<?php echo $opcGrillaContable; ?>[PaginaActual<?php echo $opcGrillaContable; ?>])!="undefined")?PaginaActual<?php echo $opcGrillaContable; ?> : '1' ;
	Ext.get(MyParent).load(
						{
							url		: "conceptos_empleados/menu_grilla_cargos.php",
							scripts	: true,
							nocache	: true,
							params	:
								{
									opc               : 'busquedaTerceroPaginacion',
									limite            : '<?php echo $limit; ?>',
									limit             : limit,
									rows_registros    : '<?php echo $rows_registros; ?>',
									paginas           : '<?php echo $paginas;    ?>',
									pagina            : PaginaActual,
									imprimeVar        : '',
									opcGrillaContable :'<?php echo $opcGrillaContable; ?>',
									filtro            : filtro,

								}
						}
					);
}

//========================== FUNCION PARA CARGAR LAS DEFINICIONES TRIBUTARIAS ====================================//
function cargaConceptosEmpleados(id,nombre,estado){

	Ext.get('divContenedorDerechoConceptosEmpleados').load({
		url     : 'conceptos_empleados/panel_contenedor_conceptos_cargos.php',
		scripts : true,
		nocache : true,
		text    : 'Cargando conceptos...',
		params  :
		{
			id_empleado     : id,
			nombre_empleado : nombre,
			estado          : estado,
		}
	});
}

</script>
<?php

	//BUSQUEDA DE LA GRILLA MANUAL
	function busquedaTerceroPaginacion($id_documento,$opcGrillaContable,$pagina,$limite,$limit,$rows_registros,$paginas,$imprimeVar,$filtro,$link,$id_empresa){

		//SI LA VARIABLE FILTRO NO ESTA VACIA, RECONTAMOS EL LIMITE DE LOS REGISTROS
		if ($filtro!='') {
			$sql="SELECT COUNT(id) as cont  FROM empleados_contratos WHERE activo=1 $filtro AND estado=0 AND id_empresa='$id_empresa' ";
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
			$sql="SELECT COUNT(id) as cont  FROM empleados_contratos WHERE activo=1 AND estado=0 AND id_empresa='$id_empresa'";
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

		// CONSULTAR LOS EMPLEADOS QUE TENGAN PRESTAMOS PENDIENTES
		$sql="SELECT id_empleado,nombre_empleado FROM nomina_prestamos_empleados WHERE activo=1 AND id_empresa=$id_empresa AND valor_prestamo_restante>0 GROUP BY id_empleado";
		$query=mysql_query($sql,$link);
		while ($row=mysql_fetch_array($query)) {
			$id_empleado=$row['id_empleado'];
			$arrayPrestamos[$id_empleado]=$row['nombre_empleado'];
		}

		$sqlCuentas   = "SELECT id,id_empleado,nombre_empleado FROM empleados_contratos WHERE activo=1 $filtro AND estado=0 AND id_empresa = '$id_empresa' AND nombre_empleado IS NOT NULL LIMIT $limit";
		$queryCuentas = mysql_query($sqlCuentas,$link);

		while ($rowCuentas = mysql_fetch_array($queryCuentas)) {
			unset($arrayPrestamos[$rowCuentas['id_empleado']]);
			$contFilaCuenta++;

			$filaInsertBoleta .= '<div class="filaBoleta_empleado" id="fila_boleta_'.$opcGrillaContable.'_'.$contFilaCuenta.'">
									<div class="campo2_empleado"  id="contrato_'.$rowCuentas['id'].'" title="'.$rowCuentas['nombre_empleado'].'" onclick="cargaConceptosEmpleados(\''.$rowCuentas['id_empleado'].'\',\''.$rowCuentas['nombre_empleado'].'\')">
										'.$rowCuentas['nombre_empleado'].'
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
								// seleccionaCheck();
							</script>';
		// RECORRER EL ARRAY DE LOS PRESTAMOS POR SI HAY EMPLEADOS SIN CONTRATO Y CON PRESTAMOS PENDIENTES
		$empleadosPendientes='';
		foreach ($arrayPrestamos as $id_empleado => $nombre_empleado) {
			$contFilaCuenta++;
			$empleadosPendientes.='<div class="filaBoleta_empleado"  id="fila_boleta_'.$opcGrillaContable.'_'.$contFilaCuenta.'">
									<div class="campo2_empleado" style="color:#f98484;font-style:italic;" id="contrato_" title="Sin contrato, con prestamo pendiente" onclick="cargaConceptosEmpleados(\''.$id_empleado.'\',\''.$nombre_empleado.'\',1)">
										'.$nombre_empleado.'
									</div>
								</div>';
		}

		$filaInsertBoleta=($empleadosPendientes=='')? $filaInsertBoleta : $empleadosPendientes.$filaInsertBoleta ;

			echo $filaInsertBoleta;

	}

 ?>