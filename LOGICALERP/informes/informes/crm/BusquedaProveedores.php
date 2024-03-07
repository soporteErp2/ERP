<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$id_empresa = $_SESSION['EMPRESA'];
	// if($idCliente == 0 || $idCliente == '' || !isset($idCliente)){ echo 'No existe un cliente seleccionado'; exit; }

	$contFilaCuenta = 0;
	$whereEmpresa= '';
	if ($tabla=='terceros') { $nit='numero_identificacion AS nit'; $whereEmpresa = 'AND tercero_empleado = \'false\' AND id_empresa='.$id_empresa; }
	else if ($tabla=='empleados') { $nit='documento AS nit'; $whereEmpresa = 'AND id_empresa='.$id_empresa;}
	else{ $nit='nit'; }

	$estado         = ($tabla!='terceros'  && $tabla!="empleados")? 'AND estado=1 ' : '' ;
	$limit			= '100';

	$sql            = "SELECT COUNT(id) as cont FROM $tabla WHERE activo=1 $whereEmpresa $where"; 
	$query          = $mysql->query($sql,$link);
	$rows_registros = $mysql->result($query,0,'cont');
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

 	$sqlCuentas   = "SELECT $id_tercero,$tercero,$nit $whereSum
					FROM $tabla
					WHERE activo=1 $estado
					$whereEmpresa
					AND tipo_proveedor = 'Si'
					$where
					GROUP BY $id_tercero ASC LIMIT $limit";

	$queryCuentas = $mysql->query($sqlCuentas,$link);

	while ($rowCuentas = $mysql->fetch_array($queryCuentas)) {
		$contFilaCuenta++;

		$divSaldoPendiente=($tabla!='terceros' && $tabla!="empleados")? '<div class="campo3" id="saldo_'.$contFilaCuenta.'">'.$rowCuentas['saldo'].'</div>' : '' ;

		$filaInsertBoleta .= '<div class="filaBoleta" id="fila_boleta_'.$opcGrillaContable.'_'.$contFilaCuenta.'">

								<div class="campo0 campoInforme0">'.$contFilaCuenta.'</div>
								<div class="campo1 campoInforme1" id="nit_'.$rowCuentas[$id_tercero].'">'.$rowCuentas['nit'].'</div>
								<div class="campo2 campoInforme2" style="border-left:0px" id="tercero_'.$rowCuentas[$id_tercero].'" title="'.$rowCuentas[$tercero].'">'.$rowCuentas[$tercero].'</div>
								'.$divSaldoPendiente.'
								<div class="campo4 campoInforme4" id="valor_anticipo_'.$opcGrillaContable.'_'.$contFilaCuenta.'">
									<input type="checkbox" id="checkbox_'.$rowCuentas[$id_tercero].'" onchange="checkGrilla(this,\''.$rowCuentas[$id_tercero].'\',\''.$tabla.'\')" value="'.$rowCuentas[$id_tercero].'">
								</div>
							</div>';
	}

	$titulo_identificacion='Nit';

	if ($tabla=='ventas_facturas') { $titulo_tercero='Cliente'; }
	else if ($tabla=='compras_facturas') { $titulo_tercero='Proveedor'; }
	else if ($tabla=='empleados') { $titulo_tercero='Empleado'; $titulo_identificacion='Documento';}
	else{ $titulo_tercero='Tercero'; }

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
			<div class="campo0 campoInforme0"></div>
			<div class="campo1 campoInforme1"><?php echo $titulo_identificacion; ?></div>
			<div class="campo2 campoInforme2" style="border-left:0px;"><?php echo $titulo_tercero; ?></div>
			<?php
				if ($tabla!='terceros' && $tabla!="empleados") { echo'<div class="campo3">Saldo Pendiente</div>'; }
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
	// myfieldBusqueda
	//VARIABLES PARA LA PAGINACION
	arrayLimitGrilla<?php echo $opcGrillaContable; ?>  = new Array();
	PaginaActual<?php echo $opcGrillaContable; ?> = 1;
	MaxPage<?php echo $opcGrillaContable; ?>      = <?php echo $paginas; ?>;
	<?php echo $acumScript; ?>

	seleccionaCheck();

	url_modulo = '';
	if('<?php echo $modulo; ?>' == 'compras' || '<?php echo $modulo; ?>' == 'comercial'){
		url_modulo = '../informes/';
	}

//================== FUNCION PARA HACER CHECK ================================//
function seleccionaCheck() {
	var arrayTemp = new Array();
	
	if('<?php echo $opcGrillaContable ?>'=='documentos_terceros'){
		if ('<?php echo $tabla; ?>'=='terceros') {arrayTemp=arrayproveedoresDT;}
		else{arrayTemp=arrayproveedoresDT;}
	}
	if('<?php echo $opcGrillaContable ?>'=='terceros_contactos'){
		if ('<?php echo $tabla; ?>'=='terceros') {arrayTemp=arrayproveedoresTC;}
		else{arrayTemp=arrayproveedoresTC;}
	}

	//RECORRER EL ARRAY DE LOS CLIENTES, PARA HACER CHECK A LOS QUE YA ESTAN EN LA GRILLA PRINCIPAL DE CONFIGURACION
	for ( i =1; i < arrayTemp.length ; i++) {

		if (arrayTemp[i]!="" && typeof(arrayTemp[i])!="undefined") {
			if(document.getElementById('checkbox_'+i)){
				document.getElementById('checkbox_'+i).checked=true;
			}
		}
	}

}

//================= 	FUNCION PARA LA PAGINACION =======================================//
function pag_Terceros(accion){

	var ciudad_tercero       = document.getElementById('select_ciudad').value	
	,   departamento_tercero = document.getElementById('select_departamento').value
	,   pais_tercero         = document.getElementById('select_pais').value;

	where = '';

	if(ciudad_tercero != 'todos'){
		where = ' AND id_ciudad = '+ciudad_tercero;
	}

	if(departamento_tercero != 'todos'){
		where += ' AND id_departamento = '+departamento_tercero;
	}

	if(pais_tercero != 'todos'){
		where += ' AND id_pais = '+pais_tercero;
	}
	
	var MyParent       = 'bodyTablaBoletas';
	var nit            = ('<?php echo $tabla; ?>'!='terceros')? 'documento ' : 'numero_identificacion' ;
	var valor          = document.getElementById('inputBuscarGrillaManual').value;
	var filtro         = (valor!='')?'AND ('+nit+' LIKE "%'+valor+'%" OR <?php echo $tercero; ?> LIKE "%'+valor+'%")' : '';

	if(accion=='first'){
		var pagina = 1;

		if(PaginaActual<?php echo $opcGrillaContable; ?>!=1){
			Ext.get(MyParent).load({
				url		: url_modulo+"informes/crm/bd.php",
				scripts	: true,
				nocache	: true,
				params	:
				{
					opc               : 'busquedaTerceroPaginacion',
					id_empresa		  : '',					
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
					modulo		   	  : '<?php echo $modulo; ?>',
					where             : where
				}
			});
		}
	}

	if(accion=='prev'){
		var pagina = PaginaActual<?php echo $opcGrillaContable; ?>-1;

		if(PaginaActual<?php echo $opcGrillaContable; ?>!=1){
			Ext.get(MyParent).load({
				url		: url_modulo+"informes/crm/bd.php",
				scripts	: true,
				nocache	: true,
				params	:
				{
					opc               : 'busquedaTerceroPaginacion',
					id_empresa		  : '',			
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
					modulo		   	  : '<?php echo $modulo; ?>',
					where             : where
				}
			});
		}
	}
	if(accion=='next'){
		var pagina = PaginaActual<?php echo $opcGrillaContable; ?>+1;

		if(PaginaActual<?php echo $opcGrillaContable; ?>!=MaxPage<?php echo $opcGrillaContable; ?>){
			Ext.get(MyParent).load({
				url		: url_modulo+"informes/crm/bd.php",
				scripts	: true,
				nocache	: true,
				params	:
				{
					opc               : 'busquedaTerceroPaginacion',
					id_empresa		  : '',				
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
					modulo		   	  : '<?php echo $modulo; ?>',
					where             : where
				}
			});
		}
	}

	if(accion=='last'){
		var pagina = MaxPage<?php echo $opcGrillaContable; ?>;

		if(PaginaActual<?php echo $opcGrillaContable; ?>!=MaxPage<?php echo $opcGrillaContable; ?>){
			Ext.get(MyParent).load({
				url		: url_modulo+"informes/crm/bd.php",
				scripts	: true,
				nocache	: true,
				params	:
				{
					opc               : 'busquedaTerceroPaginacion',
					id_empresa		  : '',				
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
					modulo		   	  : '<?php echo $modulo; ?>',
					where             : where
				}
			});
		}
	}
}

//=================== FUNCION PARA ACTUALIZAR LOS DATOS DE LA GRILLA MANUAL ==================//
function actualizarDatosGrillaManual() {

	var ciudad_tercero       = document.getElementById('select_ciudad').value	
	,   departamento_tercero = document.getElementById('select_departamento').value
	,   pais_tercero         = document.getElementById('select_pais').value;

	where = '';

	if(ciudad_tercero != 'todos'){
		where = ' AND id_ciudad = '+ciudad_tercero;
	}

	if(departamento_tercero != 'todos'){
		where += ' AND id_departamento = '+departamento_tercero;
	}

	if(pais_tercero != 'todos'){
		where += ' AND id_pais = '+pais_tercero;
	}
	
	var MyParent       = 'bodyTablaBoletas';
	var nit            = ('<?php echo $tabla; ?>'!='terceros')? 'documento ' : 'numero_identificacion' ;
	var valor          = document.getElementById('inputBuscarGrillaManual').value;
	var filtro         = (valor!='')?'AND ('+nit+' LIKE "%'+valor+'%" OR <?php echo $tercero; ?> LIKE "%'+valor+'%")' : '';

	Ext.get(MyParent).load({
		url		: url_modulo+"informes/crm/bd.php",
		scripts	: true,
		nocache	: true,
		params	:
		{
			opc               : 'busquedaTerceroPaginacion',
			id_empresa		  : '',				
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
			filtro            : filtro,
			modulo		   	  : '<?php echo $modulo; ?>',
			where             : where
		}
	});
}


function inputBuscarGrillaManual(event,input) {
	var tecla = input ? event.keyCode : event.which
	,   valor = input.value;

    if (tecla==13) { buscarDatosGrillaManual(valor); }
}

//=========================== FUNCION PARA BUSCAR REGISTROS POR UN VALOR =========================================//
function buscarDatosGrillaManual(valor) {

	var ciudad_tercero       = document.getElementById('select_ciudad').value	
	,   departamento_tercero = document.getElementById('select_departamento').value
	,   pais_tercero         = document.getElementById('select_pais').value;

	where = '';

	if(ciudad_tercero != 'todos'){
		where = ' AND id_ciudad = '+ciudad_tercero;
	}

	if(departamento_tercero != 'todos'){
		where += ' AND id_departamento = '+departamento_tercero;
	}

	if(pais_tercero != 'todos'){
		where += ' AND id_pais = '+pais_tercero;
	}	

	if ('<?php echo $tabla; ?>'=='terceros') { var nit='numero_identificacion'; }

	// var nit = ('<?php echo $tabla; ?>'!='terceros')? 'nit ' : 'numero_identificacion' ;
	var filtro       = (valor!='')?'AND ('+nit+' LIKE "%'+valor+'%" OR <?php echo $tercero; ?> LIKE "%'+valor+'%")' : '';
	var MyParent     = 'bodyTablaBoletas';
	var limit        =(typeof(arrayLimitGrilla<?php echo $opcGrillaContable; ?>[PaginaActual<?php echo $opcGrillaContable; ?>])!="undefined")? arrayLimitGrilla<?php echo $opcGrillaContable; ?>[PaginaActual<?php echo $opcGrillaContable; ?>] : '0,<?php echo $limit ?>';
	var PaginaActual =(typeof(arrayLimitGrilla<?php echo $opcGrillaContable; ?>[PaginaActual<?php echo $opcGrillaContable; ?>])!="undefined")?PaginaActual<?php echo $opcGrillaContable; ?> : '1' ;

	Ext.get(MyParent).load({
		url		: url_modulo+"informes/crm/bd.php",
		scripts	: true,
		nocache	: true,
		params	:
		{
			opc               : 'busquedaTerceroPaginacion',
			id_empresa		  : '',				
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
			filtro 			  : filtro,
			modulo		   	  : '<?php echo $modulo; ?>',
			where             : where
		}
	});
}

</script>